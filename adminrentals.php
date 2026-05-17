<?php
session_start();
include(__DIR__ . "/db.php");

$departments = ['CABA','CEIT','COED','CPAG','NB','CAS'];

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Rentals - Locker Management System</title>
</head>
<body style="margin:0; font-family:Arial, sans-serif; background:grey; color:black; display:flex; flex-direction:column; min-height:100vh;">
  <!-- Orange header bar -->
  <header style="background:#FF7F32; color:white; padding:15px; text-align:center;">
    <h1>Locker Management System - Admin</h1>
  </header>
  <!-- Orange nav bar -->
  <nav style="background:#E56717; padding:15px;">
    <ul style="list-style:none; display:flex; justify-content:center; margin:0; padding:0;">
      <li style="margin:0 10px;"><a href="adminpage.php" style="color:white; font-weight:bold; text-decoration:none;">Home</a></li>
      <li style="margin:0 10px;"><a href="adminrentals.php" style="color:white; font-weight:bold; text-decoration:none;">Rentals</a></li>
      <li style="margin:0 10px;">
        <button onclick="window.location.href='logout.php'" 
                style="padding:8px 15px; background:#cc0000; color:white; border:none; border-radius:4px; cursor:pointer;">
          Logout
        </button>
      </li>
    </ul>
  </nav>

  <!-- Main content expands to push footer down -->
  <main style="flex:1; padding:20px; text-align:center;">
    <h2 style="color:#FF7F32;">Admin Rentals</h2>
    <p>Select a department to manage lockers:</p>
    <div class="dept-list" style="display:flex; flex-wrap:wrap; justify-content:center; gap:15px; margin-top:20px;">
      <?php foreach ($departments as $dept): ?>
        <a href="adminfloors.php?dept=<?php echo urlencode($dept); ?>" 
           style="background:#FF7F32; color:white; padding:15px 25px; text-decoration:none; border-radius:5px; font-weight:bold;">
          <?php echo htmlspecialchars($dept); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Orange footer bar -->
  <footer style="background:#FF7F32; color:white; text-align:center; padding:15px;">
    <p>&copy; 2026 Locker Management System</p>
  </footer>
</body>
</html>
