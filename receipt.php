<?php
session_start();
include(__DIR__ . "/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
  <title>Reservation Receipt</title>
  <link rel="stylesheet" href="styleniya.css?v=103">
  <style>
    body { margin:0; font-family:Arial, sans-serif; background:url('images/plv.jpg') no-repeat center center; background-size:cover; }
    header { background:#003366; color:white; padding:15px; text-align:center; }
    .receipt-box {
      max-width:700px;
      margin:40px auto;
      background:rgba(255,255,255,0.85);
      backdrop-filter:blur(6px);
      border-radius:12px;
      box-shadow:0 6px 12px rgba(0,0,0,0.25);
      padding:30px;
      text-align:left;
    }
    h2 { text-align:center; color:#003366; }
    h3 { margin-top:20px; color:#0055a5; }
    p { margin:5px 0; }
    .btn-back, .btn-print {
      display:inline-block;
      margin:20px 10px 0;
      background:#0055a5;
      color:white;
      padding:10px 20px;
      border:none;
      border-radius:6px;
      cursor:pointer;
      font-weight:bold;
    }
    .btn-back:hover, .btn-print:hover { background:#003366; }
    footer { background:#003366; color:white; text-align:center; padding:15px; margin-top:20px; }
  </style>
</head>
<body>
  <header>
    <h1>SecureLocker Inc.</h1>
  </header>

  <main>
    <div class="receipt-box">
      <h2>Reservation Receipt</h2>

      <h3>Student Information</h3>
      <p><strong>Full Name:</strong> <?= htmlspecialchars($fullName) ?></p>
      <p><strong>Student ID:</strong> <?= htmlspecialchars($row['studentId']) ?></p>
      <p><strong>Course:</strong> <?= htmlspecialchars($row['course']) ?></p>
      <p><strong>Year Level:</strong> <?= htmlspecialchars($row['yearLevel']) ?></p>
      <p><strong>Contact:</strong> <?= htmlspecialchars($row['contact']) ?></p>

      <h3>Locker Information</h3>
      <p><strong>Department:</strong> <?= htmlspecialchars($row['department']) ?></p>
      <p><strong>Floor:</strong> <?= htmlspecialchars($row['floor']) ?></p>
      <p><strong>Locker Number:</strong> <?= htmlspecialchars($row['locker_id']) ?></p>
      <p><strong>Duration:</strong> <?= htmlspecialchars($row['duration']) ?></p>
      <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>

      <h3>Transaction Information</h3>
      <p><strong>Reservation ID:</strong> <?= htmlspecialchars($row['reservation_id']) ?></p>
      <p><strong>Reserved At:</strong> <?= $row['reserved_at']->format('Y-m-d H:i:s') ?></p>

      <div style="text-align:center;">
        <button onclick="window.location.href='myrentals.php'" class="btn-back">&larr; Back to My Rentals</button>
        <button onclick="window.print()" class="btn-print">🖨 Print Receipt</button>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy;SecureLocker Inc.</p>
  </footer>
</body>
</html>
