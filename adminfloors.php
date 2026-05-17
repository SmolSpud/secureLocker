<?php
session_start();
include(__DIR__ . "/db.php");

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

$dept = $_GET['dept'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Floor Selection - Locker Management System</title>
  <style>
    .nav-link {
      color:white;
      font-weight:bold;
      text-decoration:none;
      padding:6px 12px;
      border-radius:4px;
    }
    .logout-link {
      background:#cc0000;
      color:white;
      font-weight:bold;
      text-decoration:none;
      padding:6px 12px;
      border-radius:4px;
    }
    .logout-link:hover {
      background:#990000;
    }
  </style>
</head>
<body style="margin:0; font-family:Arial, sans-serif; background:grey; color:black; display:flex; flex-direction:column; min-height:100vh;">
  <!-- Orange header bar -->
  <header style="background:#FF7F32; color:white; padding:15px; text-align:center;">
    <h1>Locker Management System - Admin</h1>
  </header>
  <!-- Orange nav bar -->
  <nav style="background:#E56717; padding:15px;">
    <ul style="list-style:none; display:flex; justify-content:center; margin:0; padding:0;">
      <li style="margin:0 10px;">
        <a href="adminpage.php" class="nav-link">Home</a>
      </li>
      <li style="margin:0 10px;">
        <a href="adminrentals.php" class="nav-link">Rentals</a>
      </li>
      <li style="margin:0 10px;">
        <a href="logout.php" class="logout-link">Logout</a>
      </li>
    </ul>
  </nav>

  <!-- Main content -->
  <main style="flex:1; padding:20px; text-align:center;">
    <h2 style="color:#FF7F32;">Floor Selection</h2>
    <?php if (!$dept): ?>
      <p>No department selected.</p>
    <?php else: ?>
      <p>Select a floor for <strong><?= htmlspecialchars($dept) ?></strong>:</p>
      <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:15px; margin-top:20px;">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <a href="adminlockers.php?dept=<?= urlencode($dept) ?>&floor=<?= $i ?>" 
             style="background:#FF7F32; color:white; padding:15px 25px; text-decoration:none; border-radius:5px; font-weight:bold;">
            Floor <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
      <!-- Back button -->
      <div style="text-align:center; margin-top:20px;">
        <button onclick="window.location.href='adminrentals.php'" 
                style="background:#E56717; color:white; padding:10px 15px; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">
          &larr; Back to Rentals
        </button>
      </div>
    <?php endif; ?>
  </main>

  <!-- Orange footer bar -->
  <footer style="background:#FF7F32; color:white; text-align:center; padding:15px;">
    <p>&copy; 2026 Locker Management System</p>
  </footer>
</body>
</html>
