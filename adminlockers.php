<?php
session_start();
include(__DIR__ . "/db.php");

// Restrict access to admins only
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$dept  = $_GET['dept'] ?? 'CABA';
$floor = $_GET['floor'] ?? 1;

// Handle admin actions
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['locker_id'])) {
    $lockerId = $_POST['locker_id'];
    $action   = $_POST['action'] ?? '';

    if ($action === 'approve') {
        $sqlUpdate = "UPDATE Lockers SET status='occupied' WHERE id=?";
        sqlsrv_query($conn, $sqlUpdate, [$lockerId]);

        $sqlRes = "UPDATE Reservations SET status='approved' WHERE locker_id=? AND status='pending'";
        sqlsrv_query($conn, $sqlRes, [$lockerId]);

    } elseif ($action === 'release') {
        $sqlUpdate = "UPDATE Lockers SET status='available', owner_id=NULL WHERE id=?";
        sqlsrv_query($conn, $sqlUpdate, [$lockerId]);

        $sqlRes = "DELETE FROM Reservations WHERE locker_id=?";
        sqlsrv_query($conn, $sqlRes, [$lockerId]);
    }

    header("Location: adminlockers.php?dept=$dept&floor=$floor");
    exit();
}

$sql  = "SELECT l.*, u.studentId, r.id AS reservation_id, r.status AS res_status
         FROM Lockers l 
         LEFT JOIN Users u ON l.owner_id = u.id 
         LEFT JOIN Reservations r ON l.id = r.locker_id AND r.status IN ('pending','approved')
         WHERE l.department=? AND l.floor=?";
$stmt = sqlsrv_query($conn, $sql, [$dept, $floor]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Lockers - Locker Management System</title>
  <style>
    body { margin:0; height:100vh; display:flex; flex-direction:column; font-family:Arial, sans-serif; background:grey; color:black; }
    header { background:#FF7F32; color:white; padding:15px; text-align:center; }
    nav { background:#E56717; padding:15px; }
    nav ul { list-style:none; display:flex; justify-content:center; margin:0; padding:0; }
    nav li { margin:0 10px; }
    nav a { color:white; font-weight:bold; text-decoration:none; }
    .logout-link {
      background:#cc0000;
      color:white !important;
      padding:6px 12px;
      border-radius:4px;
    }
    .logout-link:hover { background:#990000; }
    table { width:600px; border-spacing:15px; border-collapse:separate; }
    button { border:none; border-radius:5px; font-weight:bold; cursor:pointer; }
    .btn-approve { background:orange; color:black; font-size:16px; width:120px; height:60px; }
    .btn-occupied { background:red; color:white; width:120px; height:60px; }
    .btn-available { background:green; color:white; width:120px; height:60px; }
    .details-link { display:block; margin-top:5px; font-size:12px; color:#003366; font-weight:bold; text-decoration:none; }
    footer { background:#FF7F32; color:white; text-align:center; padding:15px; }
  </style>
</head>
<body>
  <header>
    <h1>Locker Management System - Admin</h1>
  </header>

  <nav>
    <ul>
      <li><a href="adminpage.php">Home</a></li>
      <li><a href="adminrentals.php">Rentals</a></li>
      <li><a href="logout.php" class="logout-link">Logout</a></li>
    </ul>
  </nav>

  <main style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center;">
    <h2 style="color:#FF7F32;">Lockers - <?= htmlspecialchars($dept) ?> (Floor <?= htmlspecialchars($floor) ?>)</h2>
    <div style="margin:10px 0; font-weight:bold;">
      <span style="color:green;">■ Available</span> &nbsp;
      <span style="color:orange;">■ Pending</span> &nbsp;
      <span style="color:red;">■ Occupied</span>
    </div>

    <div style="display:flex; justify-content:center; align-items:center;">
      <table>
        <?php
        $count = 1;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if ($count % 5 == 1) echo "<tr>";

            $statusClass = strtolower(trim($row['status'] ?? 'available'));
            $studentId   = htmlspecialchars($row['studentId'] ?? 'N/A');
            $reservationId = $row['reservation_id'] ?? null;

            echo "<td style='text-align:center;'>";

            if ($statusClass === 'pending') {
                echo '<form method="POST" action="adminlockers.php?dept=' . urlencode($dept) . '&floor=' . urlencode($floor) . '" 
                          onsubmit="return confirm(\'Approve this locker? Owned by Student ID: ' . $studentId . '\');">';
                echo '<input type="hidden" name="locker_id" value="' . $row['id'] . '">';
                echo '<input type="hidden" name="action" value="approve">';
                echo '<button class="btn-approve" type="submit">Locker ' . $count . ' (Pending - ' . $studentId . ')</button>';
                echo '</form>';
                if ($reservationId) {
                    echo '<a class="details-link" href="adminlocker_view.php?id=' . $reservationId . '">View Receipt</a>';
                }
            } elseif ($statusClass === 'occupied') {
                echo '<form method="POST" action="adminlockers.php?dept=' . urlencode($dept) . '&floor=' . urlencode($floor) . '" 
                          onsubmit="return confirm(\'Release this locker? Owned by Student ID: ' . $studentId . '\');">';
                echo '<input type="hidden" name="locker_id" value="' . $row['id'] . '">';
                echo '<input type="hidden" name="action" value="release">';
                echo '<button class="btn-occupied" type="submit">Locker ' . $count . ' (Occupied - ' . $studentId . ')</button>';
                echo '</form>';
                if ($reservationId) {
                    echo '<a class="details-link" href="adminlocker_view.php?id=' . $reservationId . '">View Receipt</a>';
                }
            } else {
                echo '<button class="btn-available" disabled>Locker ' . $count . ' (Available)</button>';
            }

            echo "</td>";

            if ($count % 5 == 0) echo "</tr>";
            $count++;
        }
        ?>
      </table>
    </div>

    <div style="margin-top:20px;">
      <button onclick="window.location.href='adminfloors.php?dept=<?= urlencode($dept) ?>'" 
              style="padding:10px 20px; background:#E56717; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">
        &larr; Back to Floor Selection
      </button>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 Locker Management System</p>
  </footer>
</body>
</html>
