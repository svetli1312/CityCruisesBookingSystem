<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../includes/db.php";

$error = "";

// Redirect if already logged in
if (isset($_SESSION['user']) && $_SESSION['role'] === 'admin') {
    header("Location: ../admin/dashboard.php");
    exit;
}
if (isset($_SESSION['user']) && $_SESSION['role'] === 'user') {
    header("Location: ../index.php");
    exit;
}

// Handle login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameOrEmail = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // First, try to log in as admin
    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $usernameOrEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($adminId, $adminPassword);
        $stmt->fetch();

        if (password_verify($password, $adminPassword)) {
            $_SESSION['user'] = $usernameOrEmail;
            $_SESSION['role'] = 'admin';
            header("Location: ../admin/dashboard.php");
            exit;
        } else {
            $error = "❌ Invalid admin password.";
        }
        $stmt->close();
    } else {
        $stmt->close();

        // If not admin, try user login
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $userName, $userPassword);
            $stmt->fetch();

            if (password_verify($password, $userPassword)) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['user'] = $userName;
                $_SESSION['email'] = $usernameOrEmail; // ✅ Add this
                $_SESSION['role'] = 'user';
                header("Location: ../index.php");
                exit;
            } else {
                $error = "❌ Invalid user password.";
            }
        } else {
            $error = "❌ User not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - City Cruises</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" href="../assets/favicon.webp" type="image/webp">
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises</h1>
        <p>Log in to your account</p>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../booking.php">Booking</a></li>
                <li><a href="../chatbot.php">Chatbot</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <section class="contact-form">
        <h2>Login</h2>

        <?php if ($error): ?>
            <p style="color:red; font-weight:bold;"><?= $error ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Username or Email:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn">Login</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?= date("Y") ?> City Cruises. All rights reserved.</p>
    </footer>
</div>
</body>
</html>