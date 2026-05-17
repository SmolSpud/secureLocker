<?php
session_start();
include(__DIR__ . "/db.php");

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: homepage.php");
    exit();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Handle cancel request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cancel_id'])) {
    $reservationId = $_POST['cancel_id'];

    // Free the locker
    $sqlLocker = "UPDATE Lockers 
                  SET status = 'available', owner_id = NULL
                  WHERE id = (SELECT locker_id FROM Reservations WHERE id = ?)";
    sqlsrv_query($conn, $sqlLocker, [$reservationId]);

    // Delete the reservation
    $sqlDelete = "DELETE FROM Reservations WHERE id = ?";
    sqlsrv_query($conn, $sqlDelete, [$reservationId]);
}

// Query reservations joined with lockers
$sql = "SELECT r.id AS reservation_id, l.id AS locker_id, l.department, l.floor, 
               r.reserved_at, r.status
        FROM Reservations r
        INNER JOIN Lockers l ON r.locker_id = l.id
        WHERE r.user_id = ?";
$params = [$userId];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("❌ Failed to load rentals.<br>" . print_r(sqlsrv_errors(), true));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Locker</title>
  <link rel="stylesheet" href="styleniya.css?v=103">
  <style>
    body {
      margin:0;
      font-family:Arial, sans-serif;
      background:url('images/plv.jpg') no-repeat center center;
      background-size:cover;
    }
    header {
      background:rgba(0,51,102,0.9);
      color:white;
      padding:15px;
      text-align:center;
    }
    nav {
      background:rgba(0,85,165,0.9);
      padding:15px;
    }
    nav ul {
      list-style:none;
      margin:0;
      padding:0;
      display:flex;
      justify-content:center;
    }
    nav ul li { margin:0 10px; }
    nav ul li a { color:white; text-decoration:none; font-weight:bold; }
    .logout-link {
      background:#cc0000;
      color:white;
      font-weight:bold;
      text-decoration:none;
      padding:6px 12px;
      border-radius:4px;
    }
    .logout-link:hover { background:#990000; }
    .content-box {
      width:80%;
      margin:40px auto;
      background:rgba(255,255,255,0.8);
      backdrop-filter:blur(6px);
      border-radius:12px;
      box-shadow:0 6px 12px rgba(0,0,0,0.25);
      padding:30px;
      text-align:center;
    }
    table {
      border-collapse: collapse;
      margin: 20px auto;
      width: 90%;
      text-align: center;
      background:rgba(255,255,255,0.9);
      border-radius:8px;
    }
    th, td { border: 1px solid #ccc; padding: 10px; }
    th { background:#003366; color:white; }
    .status-pending { background: orange; color: black; font-weight: bold; }
    .status-approved { background: green; color: white; font-weight: bold; }
    .status-rejected { background: red; color: white; font-weight: bold; }
    button {
      background:#003366;
      color:white;
      padding:6px 12px;
      border:none;
      border-radius:4px;
      cursor:pointer;
      margin:2px;
    }
    button:hover { background:#0055a5; }
    footer {
      background:rgba(0,85,165,0.9);
      color:white;
      text-align:center;
      padding:15px;
    }
  </style>
</head>
<body>
  <div class="page-wrapper">
    <header>
      <h1>SecureLocker Inc.</h1>
    </header>
    <nav>
      <ul>
        <li><a href="homepage.php">Home</a></li>
        <li><a href="rentals.php">Rentals</a></li>
        <li><a href="myrentals.php">My Locker</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="?action=logout" class="logout-link">Logout</a></li>
      </ul>
    </nav>
    <main>
      <div class="content-box">
        <h2 style="color:#003366;">My Rentals</h2>
        <?php
        if (sqlsrv_has_rows($stmt)) {
            echo "<table>";
            echo "<tr><th>Locker ID</th><th>Department</th><th>Floor</th><th>Reserved At</th><th>Status</th><th>Action</th></tr>";
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $statusClass = "status-" . strtolower($row['status']);
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['locker_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                echo "<td>" . htmlspecialchars($row['floor']) . "</td>";
                echo "<td>" . $row['reserved_at']->format('Y-m-d H:i:s') . "</td>";
                echo "<td class='" . $statusClass . "'>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>
                        <form method='POST' action='myrentals.php' style='display:inline;' 
                              onsubmit=\"return confirm('Are you sure you want to cancel this reservation?');\">
                          <input type='hidden' name='cancel_id' value='" . $row['reservation_id'] . "'>
                          <button type='submit'>Cancel</button>
                        </form>
                        <form method='GET' action='receipt.php' style='display:inline;'>
                          <input type='hidden' name='id' value='" . $row['reservation_id'] . "'>
                          <button type='submit'>View Receipt</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no current rentals.</p>";
        }
        ?>
      </div>
    </main>
    <footer>
      <p>&copy; 2026 SecureLocker Inc.</p>
    </footer>
  </div>
</body>
</html>
