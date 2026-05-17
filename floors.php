<?php
session_start();
include(__DIR__ . "/db.php");

$dept = $_GET['dept'] ?? null;

// 🔹 Map departments to background images
$deptBackgrounds = [
     'CAS'   => 'images/cas.jpg',         
    'CABA'  => 'images/caba.jpg',
    'CEIT'  => 'images/ceit.jpg',
    'COED'  => 'images/coed.jpg',
    'CPAG'  => 'images/cpag.jpg',
    'NB'    => 'images/nb.jpg',
];

// Default background if dept not found
$backgroundImage = $deptBackgrounds[$dept] ?? 'images/plv.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Floor Selection</title>
  <link rel="stylesheet" href="styleniya.css">
  <style>
    body {
      margin:0;
      font-family:Arial, sans-serif;
      background:url('<?php echo $backgroundImage; ?>') no-repeat center center;
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
    main {
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      min-height:70vh;
    }
    .content-box {
      width:700px;
      background:rgba(255,255,255,0.7);
      backdrop-filter:blur(6px);
      border-radius:12px;
      box-shadow:0 6px 12px rgba(0,0,0,0.25);
      padding:40px;
      text-align:center;
    }
    .content-box h2 {
      font-size:36px;
      color:#003366;
      margin-bottom:20px;
    }
    .content-box p {
      font-size:22px;
      color:#003366;
      margin-bottom:25px;
    }
    .floor-list {
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:20px;
      margin-top:20px;
    }
    .floor-list a {
      display:flex;
      justify-content:center;
      align-items:center;
      background:#0055a5;
      color:white;
      height:120px;
      font-size:20px;
      text-decoration:none;
      border-radius:8px;
      font-weight:bold;
      transition:transform 0.2s, background 0.3s;
    }
    .floor-list a:hover { 
      background:#003366; 
      transform:scale(1.05);
    }
    .nav-buttons {
      text-align:center;
      margin-top:30px;
    }
    .nav-buttons button {
      background:#0055a5;
      color:white;
      padding:12px 20px;
      border:none;
      border-radius:6px;
      cursor:pointer;
      font-size:16px;
      transition:background 0.3s;
    }
    .nav-buttons button:hover { background:#003366; }
    footer {
      background:rgba(0,85,165,0.9);
      color:white;
      text-align:center;
      padding:15px;
    }
    @media (max-width:600px) {
      .floor-list { grid-template-columns: repeat(2, 1fr); }
      .floor-list a { height:100px; font-size:18px; }
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
      </ul>
    </nav>
    <main>
      <div class="content-box">
        <h2>Floor Selection</h2>
        <?php if (!$dept): ?>
          <p>No department selected.</p>
        <?php else: ?>
          <p>Select a floor for <strong><?php echo htmlspecialchars($dept); ?></strong>:</p>
          <div class="floor-list">
            <?php for ($i = 1; $i <= 6; $i++): ?>
              <a href="lockers.php?dept=<?php echo urlencode($dept); ?>&floor=<?php echo $i; ?>">
                Floor <?php echo $i; ?>
              </a>
            <?php endfor; ?>
          </div>
          <div class="nav-buttons">
            <button onclick="window.location.href='rentals.php'">&larr; Back to Rentals</button>
          </div>
        <?php endif; ?>
      </div>
    </main>
    <footer>
      <p>&copy; 2026 SecureLocker Inc.</p>
    </footer>
  </div>
</body>
</html>
