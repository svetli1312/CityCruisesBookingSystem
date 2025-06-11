<?php
session_start(); // Start session to track login status
require_once "includes/db.php"; // Include database connection

// Redirect user if already logged in
if (isset($_SESSION['user_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); // Sanitize email input
    $password = $_POST['password']; // Get password as-is

    // Prepare and execute query to check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    // If a matching user is found
    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // Verify password using hashed value
        if (password_verify($password, $user['password'])) {
            // Store user details in session and redirect to homepage
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login - CityCruises</title>
  <style>
    /* Basic styling for the login page */
    body {
      font-family: Arial;
      background: #f2f6fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-box {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 400px;
      text-align: center;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #072f57;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .error {
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
<div class="login-box">
  <h2>User Login</h2>

  <!-- Show error message if login fails -->
  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Login form -->
  <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
</div>
</body>
</html>