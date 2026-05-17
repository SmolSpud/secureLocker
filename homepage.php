<?php
session_start();
include(__DIR__ . "/db.php");

// Grab name and role from session
$name     = $_SESSION['name'] ?? null;
$userRole = $_SESSION['role'] ?? null;

// Use whichever session variable your login script sets.
$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['studentId']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SecureLocker Inc. - Home</title>
  <link rel="stylesheet" href="styleniya.css?v=118">
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
      font-weight:bold;
      text-decoration:none;
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
    .welcome-banner {
      background:rgba(238,243,247,0.9);
      padding:15px;
      text-align:center;
      font-size:18px;
      color:#003366;
    }
    .hero-section {
      display:flex;
      justify-content:center;
      align-items:center;
      min-height:70vh;
    }
    .hero-box {
      max-width:900px;
      background:rgba(255,255,255,0.85);
      backdrop-filter:blur(6px);
      border-radius:12px;
      box-shadow:0 6px 12px rgba(0,0,0,0.25);
      padding:40px;
      text-align:center;
    }
    .hero-box h1 {
      font-size:32px;
      color:#003366;
      margin-bottom:20px;
    }
    .hero-box p {
      font-size:18px;
      color:#003366;
      line-height:1.6;
      text-align:justify;
      margin-bottom:20px;
    }
    .hero-buttons {
      margin-top:30px;
    }
    .hero-buttons button {
      padding:12px 25px;
      margin:10px;
      font-size:16px;
      border:none;
      border-radius:6px;
      cursor:pointer;
    }
    .btn-start {
      background:#0055a5;
      color:white;
    }
    .btn-start:hover { background:#003366; }
    .btn-learn {
      background:white;
      color:#0055a5;
      border:2px solid #0055a5;
    }
    .btn-learn:hover { background:#e6e6e6; }
    footer {
      background:rgba(0,85,165,0.9);
      color:white;
      text-align:center;
      padding:15px;
      font-size:14px;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <h1>SecureLocker Inc.</h1>
  </header>

  <!-- Navigation -->
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

  <!-- Welcome Banner -->
  <?php if ($loggedIn && $name): ?>
    <div class="welcome-banner">
      Welcome, <?= htmlspecialchars($name) ?> (<?= htmlspecialchars($userRole) ?>)!
    </div>
  <?php endif; ?>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-box">
      <h1>Work Better, Smarter, Together</h1>
      <p>
        Welcome to SecureLocker Inc. in Pamantasan ng Lungsod ng Valenzuela — your trusted digital solution for safe, convenient, and organized locker reservations. Our platform is designed to simplify the way PLV students manage their personal storage by providing an easy-to-use system for viewing available lockers, making reservations, and tracking locker usage in real time.
      </p>
      <p>
        At SecureLocker Inc., we aim to create a smarter and more efficient campus environment within PLV by replacing traditional manual locker assignment processes with a secure and modern digital platform. Whether students need a locker for a semester or for the entire school year, the system ensures a fast, reliable, and hassle-free experience that supports convenience, accessibility, and campus organization anytime and anywhere.
      </p>
      <div class="hero-buttons">
        <button onclick="window.location.href='lockers.php'" class="btn-start">Get Started</button>
        <button onclick="window.location.href='about.php'" class="btn-learn">Learn More</button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; 2026 SecureLocker Inc.</p>
  </footer>

</body>
</html>
