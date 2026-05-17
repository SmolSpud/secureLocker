<?php
session_start();
include(__DIR__ . "/db.php"); // db.php should hold your mysqli connection ($conn)

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $studentId = $_POST['studentId'] ?? '';
    $password  = $_POST['password'] ?? '';

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT id, studentId, role FROM Users WHERE studentId = ? AND password = ?");
    $stmt->bind_param("ss", $studentId, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['user_id']   = $row['id'];
        $_SESSION['studentId'] = $row['studentId'];
        $_SESSION['role']      = $row['role'];

        // Redirect based on role
        if (strtolower($row['role']) === 'admin') {
            header("Location: adminpage.php");
        } else {
            header("Location: homepage.php");
        }
        exit();
    } else {
        echo "<script>alert('❌ Invalid ID or Password');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - SecureLocker Inc.</title>
  <link rel="stylesheet" href="styleniya.css">
  <style>
    body, html { margin:0; padding:0; height:100%; font-family:Arial, sans-serif; }
    .split { display:flex; height:calc(100vh - 120px); }
    .left { flex:1; display:flex; flex-direction:column; justify-content:center; align-items:center;
      background:#f9f9f9; padding:40px; }
    .left h2 { color:#003366; margin-bottom:20px; }
    .left p { color:#333; margin-bottom:30px; text-align:center; max-width:300px; }
    .left form { width:100%; max-width:300px; }
    .left label { font-weight:bold; color:#003366; }
    .left input { width:100%; padding:10px; margin:8px 0 20px 0; border:1px solid #ccc; border-radius:4px; }
    .left button { width:100%; padding:12px; background:#0055a5; color:white; border:none; border-radius:5px; cursor:pointer;
      font-size:16px; font-weight:bold; }
    .left button:hover { background:#003366; }
    .right { flex:1; background:url('images/plv.jpg') no-repeat center center; background-size:cover; }
    header { background:#003366; color:white; padding:15px; text-align:center; }
    nav ul { list-style:none; margin:0; padding:0; display:flex; justify-content:center; background:#0055a5; }
    nav ul li { margin:0 10px; }
    nav ul li a { color:white; text-decoration:none; font-weight:bold; padding:15px; display:block; }
    footer { background:#003366; color:white; text-align:center; padding:15px; }
  </style>
</head>
<body>
  <header>
    <h1>SecureLocker Inc. </h1>
  </header>
  <nav>
    <ul>
      <li><a href="homepage.php">Home</a></li>
    </ul>
  </nav>

  <div class="split">
    <div class="left">
      <h2>Login</h2>
      <p>To gain full access, login with your ID and password.</p>
      <form method="POST" action="login.php">
        <label for="studentId">ID:</label><br>
        <input type="text" id="studentId" name="studentId" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
      </form>
    </div>
    <div class="right"></div>
  </div>

  <footer>
    <p>&copy; 2026 Locker Management System</p>
  </footer>
</body>
</html>
