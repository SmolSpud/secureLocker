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
  <title>Register - SecureLocker Inc.</title>
  <script>
    // Toggle field label depending on role
    function toggleFields() {
      const role = document.getElementById("role").value;
      const idLabel = document.getElementById("idLabel");
      const idInput = document.getElementById("idInput");

      if (role === "Admin") {
        idLabel.textContent = "Admin Email:";
        idInput.type = "email";
        idInput.placeholder = "admin@example.com";
        idInput.pattern = ""; // no student ID pattern
      } else {
        idLabel.textContent = "Student ID (XX-XXXX):";
        idInput.type = "text";
        idInput.placeholder = "12-3456";
        idInput.pattern = "^[0-9]{2}-[0-9]{4}$";
      }
    }
  </script>
</head>
<body style="margin:0; font-family:Arial, sans-serif; background:white; color:black; display:flex; flex-direction:column; min-height:100vh;">
  <!-- Orange header bar -->
  <header style="background:#FF7F32; color:white; padding:15px; text-align:center;">
    <h1>SecureLocker Inc. - Admin</h1>
  </header>
  <!-- Orange nav bar -->
  <nav style="background:#E56717; padding:15px;">
    <ul style="list-style:none; margin:0; padding:0; display:flex; justify-content:center;">
      <li style="margin:0 10px;"><a href="adminpage.php" style="color:white; font-weight:bold; text-decoration:none;">Home</a></li>
      <li style="margin:0 10px;"><a href="adminrentals.php" style="color:white; font-weight:bold; text-decoration:none;">Rentals</a></li>
      <li style="margin:0 10px;"><a href="logout.php" style="color:white; font-weight:bold; text-decoration:none;">Logout</a></li>
    </ul>
  </nav>

  <div style="flex:1; display:flex; height:calc(100vh - 120px);">
    <!-- Left side: register form -->
    <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#f9f9f9; padding:40px;">
      <h2 style="color:#FF7F32; margin-bottom:20px;">Register User</h2>

      <?php
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
          $identifier = $_POST['identifier'] ?? '';
          $lastName   = $_POST['lastName'] ?? '';
          $middleName = $_POST['middleName'] ?? '';
          $firstName  = $_POST['firstName'] ?? '';
          $password   = $_POST['password'] ?? '';
          $role       = $_POST['role'] ?? 'Student';

          if ($identifier && $lastName && $firstName && $password) {
              // Save identifier into studentId column (works for both Student ID and Admin Email)
              $sql = "INSERT INTO Users (studentId, lastName, middleName, firstName, password, role)
                      VALUES (?, ?, ?, ?, ?, ?)";
              $params = [$identifier, $lastName, $middleName, $firstName, $password, $role];
              $stmt = sqlsrv_query($conn, $sql, $params);

              if ($stmt) {
                  echo "<p style='color:green;'>✅ $role registration successful! You may now <a href=\"login.php\">login</a>.</p>";
              } else {
                  echo "<p style='color:red;'>❌ Failed to register. Check backend connection.</p>";
                  die(print_r(sqlsrv_errors(), true));
              }
          } else {
              echo "<p style='color:red;'>❌ Please fill in all required fields.</p>";
          }
      }
      ?>

      <form method="POST" action="register.php" style="width:100%; max-width:400px;">
        <label id="idLabel" for="idInput" style="font-weight:bold; color:#E56717;">Student ID (XX-XXXX):</label><br>
        <input type="text" id="idInput" name="identifier" pattern="^[0-9]{2}-[0-9]{4}$" required><br>

        <label for="lastName" style="font-weight:bold; color:#E56717;">Last Name:</label><br>
        <input type="text" id="lastName" name="lastName" required><br>

        <label for="middleName" style="font-weight:bold; color:#E56717;">Middle Name (N/A if none):</label><br>
        <input type="text" id="middleName" name="middleName"><br>

        <label for="firstName" style="font-weight:bold; color:#E56717;">First Name:</label><br>
        <input type="text" id="firstName" name="firstName" required><br>

        <label for="password" style="font-weight:bold; color:#E56717;">Password:</label><br>
        <input type="password" id="password" name="password" required><br>

        <label for="role" style="font-weight:bold; color:#E56717;">Role:</label><br>
        <select id="role" name="role" onchange="toggleFields()">
          <option value="Student">Student</option>
          <option value="Admin">Admin</option>
        </select><br>

        <button type="submit" style="width:100%; padding:12px; background:#FF7F32; color:white; border:none; border-radius:5px; cursor:pointer; font-size:16px; font-weight:bold;">
          Register
        </button>
      </form>
    </div>

    <!-- Right side: background image -->
    <div style="flex:1; background:url('images/plv.jpg') no-repeat center center; background-size:cover;"></div>
  </div>

  <!-- Orange footer bar -->
  <footer style="background:#FF7F32; color:white; text-align:center; padding:15px;">
    <p>&copy; 2026 SecureLocker Inc.</p>
  </footer>
</body>
</html>
