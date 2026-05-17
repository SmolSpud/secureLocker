<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Select Department</title>
  <link rel="stylesheet" href="styleniya.css">
</head>
<body>
  <div class="page-wrapper">
    <header>
      <h1>Locker Management System</h1>
    </header>
    <nav>
      <ul>
        <li><a href="homepage.html">Home</a></li>
        <li><a href="rentals.php">Rentals</a></li>
        <li><a href="myrentals.html">My Rentals</a></li>
        <li><a href="lockers.html">Lockers</a></li>
        <li><a href="about.html">About Us / Contact</a></li>
        <li><a href="login.html">Login</a></li>
      </ul>
    </nav>
    <main>
      <h2>Select your department</h2>
      <p>Choose a department to view available lockers:</p>
      <div class="grid">
        <?php
        $departments = ['CABA','CEIT','COED','CPAG','NB','CAS','ENG','MED'];
        foreach ($departments as $dept) {
            echo "<a href='floors.php?dept=$dept'><button>$dept</button></a>";
        }
        ?>
      </div>
    </main>
    <footer>
      <p>&copy; 2026 Locker Management System</p>
    </footer>
  </div>
</body>
</html>
