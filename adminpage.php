<?php
session_start();
include(__DIR__ . "/db.php");

// Restrict access to admins only
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Homepage - Locker Management System</title>
</head>
<body style="margin:0; font-family:Arial, sans-serif; background:grey; color:white; display:flex; flex-direction:column; min-height:100vh;">
  <!-- Orange header bar -->
  <header style="background:#FF7F32; color:white; padding:15px; text-align:center;">
    <h1>Locker Management System - Admin</h1>
  </header>
  <!-- Orange nav bar -->
  <nav style="background:#E56717; padding:15px;">
    <ul style="list-style:none; margin:0; padding:0; display:flex; justify-content:center;">
      <li style="margin:0 10px;"><a href="adminpage.php" style="color:white; font-weight:bold; text-decoration:none;">Home</a></li>
      <li style="margin:0 10px;"><a href="adminrentals.php" style="color:white; font-weight:bold; text-decoration:none;">Rentals</a></li>
      <li style="margin:0 10px;"><a href="register.php" style="color:white; font-weight:bold; text-decoration:none;">Register</a></li>
      <li style="margin:0 10px;">
        <button onclick="window.location.href='logout.php'" 
                style="padding:8px 15px; background:#cc0000; color:white; border:none; border-radius:4px; cursor:pointer;">
          Logout
        </button>
      </li>
    </ul>
  </nav>
  <!-- Main content expands to push footer down -->
  <main style="flex:1; text-align:center; margin-top:40px;">
    <h2 style="color:#FFD580;">Welcome, Admin!</h2>
    <p>
      This is your admin homepage. From here you can navigate to the 
      <strong>Rentals Dashboard</strong> to manage lockers, approve pending reservations, 
      and release occupied lockers. Use the menu above to get started.
    </p>
    <?php if (isset($_SESSION['studentId'])): ?>
      <p><em>Logged in as: <?php echo htmlspecialchars($_SESSION['studentId']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</em></p>
    <?php endif; ?>
  </main>
  <!-- Orange footer bar stays at bottom -->
  <footer style="background:#FF7F32; color:white; text-align:center; padding:15px;">
    <p>&copy; 2026 Locker Management System</p>
  </footer>
</body>
</html>
