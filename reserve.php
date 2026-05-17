<?php
session_start();
include(__DIR__ . "/db.php");

// Restrict access to students only
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'student') {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dept      = $_POST['dept']   ?? null;
    $floor     = $_POST['floor']  ?? null;
    $locker    = $_POST['locker'] ?? null;
    $course    = $_POST['course'] ?? null;
    $yearLevel = $_POST['yearLevel'] ?? null;
    $contact   = $_POST['contact'] ?? null;
    $duration  = $_POST['duration'] ?? null;

    if (!$dept || !$floor || !$locker) {
        echo "<script>alert('Invalid reservation request.'); window.location.href='rentals.php';</script>";
        exit();
    }

    // Prevent multiple reservations
    $sqlCheck = "SELECT TOP 1 id FROM Reservations WHERE user_id=? AND status IN ('pending','approved')";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$userId]);
    if ($stmtCheck && sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
        echo "<script>alert('You already have a reservation.'); window.location.href='lockers.php?dept=" . urlencode($dept) . "&floor=" . urlencode($floor) . "';</script>";
        exit();
    }

    // Insert reservation
    $sqlInsert = "INSERT INTO Reservations (user_id, locker_id, status, duration, reserved_at, course, yearLevel, contact)
                  VALUES (?, ?, 'pending', ?, GETDATE(), ?, ?, ?)";
    $params = [$userId, $locker, $duration, $course, $yearLevel, $contact];
    $stmtInsert = sqlsrv_query($conn, $sqlInsert, $params);

    if ($stmtInsert === false) {
        echo "<script>alert('Reservation failed.'); window.location.href='lockers.php?dept=" . urlencode($dept) . "&floor=" . urlencode($floor) . "';</script>";
        exit();
    }

    // Update locker status
    $sqlUpdate = "UPDATE Lockers SET status='pending', owner_id=? WHERE id=?";
    sqlsrv_query($conn, $sqlUpdate, [$userId, $locker]);

    // Success alert + redirect
    echo "<script>
            alert('Locker $locker reserved successfully! You can view your receipt in My Rentals.');
            window.location.href='myrentals.php';
          </script>";
    exit();
}

// If GET request, show reservation form
$dept   = $_GET['dept'] ?? null;
$floor  = $_GET['floor'] ?? null;
$locker = $_GET['locker'] ?? null;

// Fetch student info
$sqlUser = "SELECT firstName, middleName, lastName, studentId FROM Users WHERE id=?";
$stmtUser = sqlsrv_query($conn, $sqlUser, [$userId]);
$rowUser = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC);

$fullName  = trim(($rowUser['firstName'] ?? '') . ' ' . ($rowUser['middleName'] ?? '') . ' ' . ($rowUser['lastName'] ?? ''));
$studentId = $rowUser['studentId'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reserve Locker</title>
  <link rel="stylesheet" href="styleniya.css?v=103">
  <style>
    body { margin:0; background:url('images/plv.jpg') no-repeat center center; background-size:cover; font-family:Arial, sans-serif; }
    header { background:#003366; color:white; padding:15px; text-align:center; }
    .form-container {
      max-width: 700px;
      margin: 30px auto;
      background: rgba(255,255,255,0.85);
      backdrop-filter: blur(6px);
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.25);
      text-align: left;
    }
    h2 { color: #003366; text-align: center; }
    h3 { margin-top: 20px; color: #0055a5; }
    label { display:block; margin-top:10px; font-weight:bold; }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top:5px;
      border:1px solid #ccc;
      border-radius:4px;
    }
    .form-controls { margin-top:20px; text-align:center; }
    .form-controls button {
      padding:10px 20px;
      border:none;
      border-radius:5px;
      cursor:pointer;
      font-weight:bold;
      margin:0 10px;
    }
    .btn-submit { background:green; color:white; }
    .btn-back { background:#003366; color:white; }
    footer { background:#003366; color:white; text-align:center; padding:15px; margin-top:20px; }
  </style>
</head>
<body>

  <header>
    <h1>SecureLocker Inc./h1>
  </header>

  <main>
    <div class="form-container">
      <h2>Locker Reservation Form</h2>

      <h3>Student Information</h3>
      <p><strong>Full Name:</strong> <?= htmlspecialchars($fullName) ?></p>
      <p><strong>Student ID:</strong> <?= htmlspecialchars($studentId) ?></p>

      <form method="POST" action="reserve.php">
        <input type="hidden" name="dept" value="<?= htmlspecialchars($dept) ?>">
        <input type="hidden" name="floor" value="<?= htmlspecialchars($floor) ?>">
        <input type="hidden" name="locker" value="<?= htmlspecialchars($locker) ?>">

        <label>Course / Program:</label>
        <input type="text" name="course" required>

        <label>Year Level:</label>
        <select name="yearLevel" required>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
        </select>

        <label>Contact Number or Email:</label>
        <input type="text" name="contact" required>

        <label>Duration of Use:</label>
        <select name="duration" required>
          <option value="Half Semester">Half Semester</option>
          <option value="Full Semester">Full Semester</option>
        </select>

        <div class="form-controls">
          <button type="submit" class="btn-submit">Submit Reservation</button>
          <button type="button" onclick="window.history.back()" class="btn-back">&larr; Back</button>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 SecureLocker Inc.</p>
  </footer>
</body>
</html>
