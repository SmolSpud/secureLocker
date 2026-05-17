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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId     = $_SESSION['user_id'];
$userRole   = $_SESSION['role'] ?? 'student';
$studentId  = $_SESSION['studentId'] ?? 'Unknown';

$dept  = $_GET['dept']  ?? 'CABA';
$floor = $_GET['floor'] ?? 1;

$deptBackgrounds = [
    'CAS'   => 'images/cas.jpg',
    'CABA'  => 'images/caba.jpg',
    'CEIT'  => 'images/ceit.jpg',
    'COED'  => 'images/coed.jpg',
    'CPAG'  => 'images/cpag.jpg',
    'NB'    => 'images/nb.jpg',
];

$backgroundImage = $deptBackgrounds[$dept] ?? 'images/plv.jpg';

$hasReservation = false;
$reservationStatus = null;

if (strtolower($userRole) === 'student') {
    $sqlCheck = "SELECT TOP 1 status FROM Reservations WHERE user_id=? AND status IN ('pending','approved')";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$userId]);
    if ($stmtCheck && $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
        $hasReservation = true;
        $reservationStatus = strtolower($rowCheck['status']);
    }
}

$sqlLockers = "SELECT id, status FROM Lockers WHERE department=? AND floor=? ORDER BY id";
$stmtLockers = sqlsrv_query($conn, $sqlLockers, [$dept, $floor]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Locker Grid</title>
  <link rel="stylesheet" href="styleniya.css?v=104">
  <style>
    body {
      margin:0;
      font-family:Arial, sans-serif;
      background:url('<?php echo $backgroundImage; ?>') no-repeat center center;
      background-size:cover;
      min-height:100vh;
      display:flex;
      flex-direction:column;
    }
    header {
      background:rgba(0,51,102,0.9);
      color:white;
      padding:10px;
      text-align:center;
      font-size:18px;
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
    nav ul li { margin:0 8px; }
    nav ul li a {
      color:white;
      font-weight:bold;
      text-decoration:none;
      font-size:13px;
    }
    nav ul li a.logout {
      background:#cc0000;
      color:white;
      padding:5px 10px;
      border-radius:4px;
    }
    main {
      flex:1;
      display:flex;
      justify-content:center;
      align-items:center;
      text-align:center;
      padding:15px 0;
    }
    .content-box {
      width:600px;
      background:rgba(255,255,255,0.75);
      backdrop-filter:blur(5px);
      border-radius:10px;
      box-shadow:0 4px 8px rgba(0,0,0,0.2);
      padding:18px;
    }
    .content-box h2 {
      font-size:22px;
      color:#003366;
      margin-bottom:8px;
    }
    .content-box p {
      font-size:14px;
      color:#003366;
      margin-bottom:10px;
    }
    .legend {
      margin:6px 0;
      font-size:13px;
      font-weight:bold;
    }
    table {
      width:100%;
      border-spacing:8px;
      border-collapse:separate;
    }
    button {
      width:85px;
      height:38px;
      border-radius:4px;
      font-weight:bold;
      font-size:12px;
      cursor:pointer;
    }
    .nav-btn {
      background:#0055a5;
      color:white;
      padding:8px 14px;
      border:none;
      border-radius:6px;
      cursor:pointer;
      font-size:13px;
      margin-top:12px;
    }
    .nav-btn:hover { background:#003366; }
    footer {
      background:rgba(0,85,165,0.9);
      color:white;
      text-align:center;
      padding:8px;
      font-size:13px;
    }
  </style>
</head>
<body>

  <header>
    <h1>SecureLocker Inc.</h1>
  </header>

  <nav>
    <ul>
      <li><a href="homepage.php">Home</a></li>
      <li><a href="rentals.php">Rentals</a></li>
      <li><a href="myrentals.php">My Locker</a></li>
      <li><a href="about.php">About Us</a></li>
      <li><a href="?action=logout" class="logout">Logout</a></li>
    </ul>
  </nav>

  <main>
    <div class="content-box">
      <h2>Locker Grid</h2>
      <div style="margin-bottom:6px;">Logged in as: <?= htmlspecialchars($studentId) ?> (<?= htmlspecialchars($userRole) ?>)</div>
      <p>Click an available locker to reserve.</p>

      <div class="legend">
        <span style="color:green;">■ Available</span>
        <span style="color:orange;">■ Pending</span>
        <span style="color:red;">■ Occupied</span>
      </div>

      <?php if ($hasReservation && strtolower($userRole) === 'student'): ?>
        <?php if ($reservationStatus === 'pending'): ?>
          <p style="color:orange; font-weight:bold;">You already have a pending reservation.</p>
        <?php elseif ($reservationStatus === 'approved'): ?>
          <p style="color:red; font-weight:bold;">You already have an approved locker.</p>
        <?php endif; ?>
      <?php endif; ?>

      <table>
        <?php
        $count = 1;
        while ($row = sqlsrv_fetch_array($stmtLockers, SQLSRV_FETCH_ASSOC)) {
            if ($count % 5 == 1) echo "<tr>";

            $statusClass = strtolower(trim($row['status'] ?? 'available'));
            $style = "";

            if ($statusClass === 'pending') {
                $style = "background:orange; color:black;";
            } elseif ($statusClass === 'occupied') {
                $style = "background:red; color:white;";
            } else {
                $style = "background:green; color:white;";
            }

            echo "<td style='text-align:center;'>";

            if ($statusClass === 'available' && strtolower($userRole) === 'student' && !$hasReservation) {
                echo '<form method="GET" action="reserve.php">';
                echo '<input type="hidden" name="dept" value="' . htmlspecialchars($dept) . '">';
                echo '<input type="hidden" name="floor" value="' . htmlspecialchars($floor) . '">';
                echo '<input type="hidden" name="locker" value="' . $row['id'] . '">';
                echo '<button style="' . $style . '">L' . $count . '</button>';
                echo '</form>';
            } else {
                echo '<button style="' . $style . '" disabled>L' . $count . '</button>';
            }

            echo "</td>";

            if ($count % 5 == 0) echo "</tr>";
            $count++;
        }
        ?>
      </table>

      <button onclick="window.location.href='floors.php?dept=<?= urlencode($dept) ?>'" class="nav-btn">&larr; Back</button>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 SecureLocker Inc.</p>
  </footer>
</body>
</html>
