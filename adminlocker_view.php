<?php
session_start();
include(__DIR__ . "/db.php");

// Restrict access to admins only
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

// 🔹 Handle admin actions inline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = $_POST['reservation_id'] ?? null;
    $action        = $_POST['action'] ?? null;

    if ($reservationId && $action) {
        if ($action === 'approve') {
            sqlsrv_query($conn, "UPDATE Reservations SET status='approved' WHERE id=?", [$reservationId]);
            sqlsrv_query($conn, "UPDATE Lockers SET status='occupied' WHERE id=(SELECT locker_id FROM Reservations WHERE id=?)", [$reservationId]);
            echo "<script>alert('Reserved successfully'); window.location.href='adminlockers.php';</script>";
            exit();
        } elseif ($action === 'reject') {
            sqlsrv_query($conn, "UPDATE Reservations SET status='rejected' WHERE id=?", [$reservationId]);
            sqlsrv_query($conn, "UPDATE Lockers SET status='available', owner_id=NULL WHERE id=(SELECT locker_id FROM Reservations WHERE id=?)", [$reservationId]);
            echo "<script>alert('Reservation rejected'); window.location.href='adminlockers.php';</script>";
            exit();
        } elseif ($action === 'release') {
            sqlsrv_query($conn, "UPDATE Reservations SET status='released' WHERE id=?", [$reservationId]);
            sqlsrv_query($conn, "UPDATE Lockers SET status='available', owner_id=NULL WHERE id=(SELECT locker_id FROM Reservations WHERE id=?)", [$reservationId]);
            echo "<script>alert('Locker released'); window.location.href='adminlockers.php';</script>";
            exit();
        }
    }
}

// 🔹 Show reservation details if GET
$reservationId = $_GET['id'] ?? null;
if (!$reservationId) {
    die("No reservation selected.");
}

$sql = "SELECT r.id AS reservation_id, r.status, r.duration, r.reserved_at,
               r.course, r.yearLevel, r.contact,
               u.firstName, u.middleName, u.lastName, u.studentId,
               l.id AS locker_id, l.department, l.floor
        FROM Reservations r
        INNER JOIN Users u ON r.user_id = u.id
        INNER JOIN Lockers l ON r.locker_id = l.id
        WHERE r.id = ?";
$stmt = sqlsrv_query($conn, $sql, [$reservationId]);

if ($stmt === false) {
    die('❌ Query failed: ' . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$row) {
    die("Reservation not found.");
}

$fullName = trim(($row['firstName'] ?? '') . ' ' . ($row['middleName'] ?? '') . ' ' . ($row['lastName'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Locker Receipt</title>
  <link rel="stylesheet" href="styleniya.css?v=103">
  <style>
    body { margin:0; background:grey; color:black; font-family:Arial, sans-serif; }
    header { background:#FF7F32; color:white; padding:15px; text-align:center; }
    .receipt-container {
      max-width: 700px;
      margin: 30px auto;
      background: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      text-align: left;
    }
    h2 { color: #FF7F32; text-align: center; }
    h3 { margin-top: 20px; color: #003366; }
    p { margin: 5px 0; }
    .admin-controls { margin-top: 20px; text-align: center; }
    .admin-controls button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin: 0 10px;
    }
    .btn-approve { background: green; color: white; }
    .btn-reject { background: red; color: white; }
    .btn-release { background: #E56717; color: white; }
    .btn-back { background:#003366; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
    footer { background:#FF7F32; color:white; text-align:center; padding:15px; margin-top:20px; }
  </style>
</head>
<body>

  <header>
    <h1>Locker Management System - Admin</h1>
  </header>

  <main>
    <div class="receipt-container">
      <h2>Reservation Receipt (Admin View)</h2>

      <h3>Student Information</h3>
      <p><strong>Full Name:</strong> <?= htmlspecialchars($fullName) ?></p>
      <p><strong>Student ID:</strong> <?= htmlspecialchars($row['studentId']) ?></p>
      <p><strong>Course / Program:</strong> <?= htmlspecialchars($row['course']) ?></p>
      <p><strong>Year Level:</strong> <?= htmlspecialchars($row['yearLevel']) ?></p>
      <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact']) ?></p>

      <h3>Locker Information</h3>
      <p><strong>Department:</strong> <?= htmlspecialchars($row['department']) ?></p>
      <p><strong>Floor:</strong> <?= htmlspecialchars($row['floor']) ?></p>
      <p><strong>Locker Number:</strong> <?= htmlspecialchars($row['locker_id']) ?></p>

      <h3>Reservation Information</h3>
      <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
      <p><strong>Duration:</strong> <?= htmlspecialchars($row['duration']) ?></p>
      <p><strong>Reserved At:</strong> <?= $row['reserved_at']->format('Y-m-d H:i:s') ?></p>
      <p><strong>Reservation ID:</strong> <?= htmlspecialchars($row['reservation_id']) ?></p>

      <div class="admin-controls">
        <?php if ($row['status'] === 'pending'): ?>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
            <input type="hidden" name="action" value="approve">
            <button type="submit" class="btn-approve">Approve</button>
          </form>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="btn-reject">Reject</button>
          </form>
        <?php elseif ($row['status'] === 'approved'): ?>
          <form method="POST" style="display:inline;">
            <input type="hidden" name="reservation_id" value="<?= $row['reservation_id'] ?>">
            <input type="hidden" name="action" value="release">
            <button type="submit" class="btn-release">Release Locker</button>
          </form>
        <?php endif; ?>
      </div>

      <div style="text-align:center; margin-top:20px;">
        <button onclick="window.history.back()" class="btn-back">&larr; Back</button>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 Locker Management System</p>
  </footer>
</body>
</html>
