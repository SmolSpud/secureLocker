<?php
session_start();
include(__DIR__ . "/db.php");

$name     = $_SESSION['name'] ?? null;
$userRole = $_SESSION['role'] ?? null;
$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['studentId']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - SecureLocker Inc.</title>
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
      padding:12px;
      text-align:center;
    }
    nav {
      background:rgba(0,85,165,0.9);
      padding:12px;
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
    .content-box {
      max-width:800px;
      margin:30px auto;
      background:rgba(255,255,255,0.85);
      backdrop-filter:blur(5px);
      border-radius:10px;
      box-shadow:0 4px 8px rgba(0,0,0,0.2);
      padding:25px;
    }
    .content-box h2 {
      font-size:26px;
      color:#003366;
      text-align:center;
      margin-bottom:15px;
    }
    .content-box h3 {
      font-size:20px;
      color:#0055a5;
      margin:15px 0 8px;
    }
    .content-box p {
      font-size:15px;
      color:#003366;
      line-height:1.4;
      margin-bottom:10px;
      text-align:justify;
    }
    .grid-two {
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:15px;
      margin-top:10px;
    }
    .contact-box {
      display:flex;
      justify-content:space-between;
      flex-wrap:wrap;
      margin-top:10px;
      background:rgba(255,255,255,0.7);
      border-radius:6px;
      padding:10px;
    }
    .contact-item {
      flex:1 1 160px;
      margin:6px;
      font-size:14px;
      color:#003366;
    }
    .contact-item strong {
      display:block;
      margin-bottom:3px;
    }
    footer {
      background:rgba(0,85,165,0.9);
      color:white;
      text-align:center;
      padding:12px;
      margin-top:15px;
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

  <!-- About Content -->
  <main>
    <div class="content-box">
      <h2>About Us</h2>
      <p>
        SecureLocker Inc. is a technology-driven company focused on providing innovative locker reservation and management solutions for educational institutions. Our goal is to improve convenience, accessibility, and security by offering a digital platform that streamlines locker allocation and monitoring processes.
      </p>
      <p>
        We believe that students deserve a secure and efficient storage system that supports their daily academic activities. Through our user-friendly platform, students can easily reserve lockers based on building and floor availability, while administrators can efficiently manage locker records, reservations, and occupancy status.
      </p>
      <p>
        SecureLocker Inc. is committed to delivering reliable and modern solutions that enhance campus organization and promote a better student experience through technology and innovation.
      </p>

      <div class="grid-two">
        <div>
          <h3>Mission</h3>
          <p>
            Our mission is to provide students and educational institutions with a secure, reliable, and user-friendly locker reservation system that improves campus organization, accessibility, and convenience through modern technology.
          </p>
        </div>
        <div>
          <h3>Vision</h3>
          <p>
            Our vision is to become a leading provider of smart campus storage solutions by creating innovative systems that enhance efficiency, security, and student experience in educational institutions.
          </p>
        </div>
      </div>

      <h3 style="text-align:center; margin-top:15px;">Contact Us</h3>
      <div class="contact-box">
        <div class="contact-item"><strong>Email:</strong> securelockerinc@gmail.com</div>
        <div class="contact-item"><strong>Phone:</strong> 8352-7000</div>
        <div class="contact-item"><strong>Location:</strong> Maysan, Tongco, Valenzuela City</div>
        <div class="contact-item"><strong>Hours:</strong> Mon–Fri | 8:00 AM – 5:00 PM</div>
      </div>
      <p style="text-align:center; margin-top:8px; font-size:13px; color:#003366;">
        We are committed to providing fast and reliable assistance to ensure a smooth and convenient locker reservation experience for all users.
      </p>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <p>&copy; 2026 SecureLocker Inc.</p>
  </footer>

</body>
</html>
