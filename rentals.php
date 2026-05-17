<?php
session_start();
include(__DIR__ . "/db.php");

$departments = ['CABA','CEIT','COED','CPAG','NB','CAS'];

$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['studentId']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rentals</title>
  <link rel="stylesheet" href="styleniya.css?v=104">
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
    nav ul li a { 
      color:white; 
      text-decoration:none; 
      font-weight:bold; 
    }
    .logout-link {
      background:#cc0000;
      color:white;
      font-weight:bold;
      text-decoration:none;
      padding:6px 12px;
      border-radius:4px;
    }
    .logout-link:hover { background:#990000; }
    main {
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      min-height:70vh;
    }
    .content-box {
      width:700px;
      background:rgba(255,255,255,0.8);
      backdrop-filter:blur(6px);
      border-radius:12px;
      box-shadow:0 6px 12px rgba(0,0,0,0.25);
      padding:30px;
      text-align:center;
    }
    .content-box h2 {
      font-size:28px;
      color:#003366;
      margin-bottom:15px;
    }
    .content-box p {
      font-size:16px;
      color:#003366;
      margin-bottom:20px;
    }
    .dept-list {
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:15px;
      margin-top:15px;
    }
    .dept-list a {
      display:flex;
      justify-content:center;
      align-items:center;
      background:#0055a5;
      color:white;
      height:100px;
      font-size:16px;
      text-decoration:none;
      border-radius:6px;
      font-weight:bold;
      transition:transform 0.2s, background 0.3s;
    }
    .dept-list a:hover { 
      background:#003366; 
      transform:scale(1.05);
    }
    .identifier {
      text-align:center;
      margin-top:10px;
      font-weight:bold;
      color:#003366;
      font-size:15px;
    }
    footer {
      background:rgba(0,85,165,0.9);
      color:white;
      text-align:center;
      padding:12px;
      font-size:14px;
    }
    @media (max-width:600px) {
      .dept-list { grid-template-columns: repeat(2, 1fr); }
      .dept-list a { height:80px; font-size:14px; }
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
        <?php if ($loggedIn): ?>
          <li><a href="logout.php" class="logout-link">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>
    <main>
      <div class="content-box">
        <h2>Rentals</h2>
        <p>Select your department to view available lockers:</p>
        <?php if (isset($_SESSION['studentId'])): ?>
          <div class="identifier">
            Logged in as: <?= htmlspecialchars($_SESSION['studentId']); ?> (<?= htmlspecialchars($_SESSION['role']); ?>)
          </div>
        <?php endif; ?>
        <div class="dept-list">
          <?php foreach ($departments as $dept): ?>
            <a href="floors.php?dept=<?= urlencode($dept); ?>">
              <?= htmlspecialchars($dept); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </main>
    <footer>
      <p>&copy; 2026 SecureLocker Inc.</p>
    </footer>
  </div>
</body>
</html>
