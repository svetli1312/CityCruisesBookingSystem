<?php
require_once "includes/db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameOrEmail = trim($_POST["username"]);
    $password = $_POST["password"];

    // First check in admin_users
    $stmt = $conn->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $usernameOrEmail);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($adminId, $adminHashed);
        $stmt->fetch();
        if (password_verify($password, $adminHashed)) {
            $_SESSION['user'] = $usernameOrEmail;
            $_SESSION['role'] = 'admin';
            header("Location: admin/dashboard.php");
            exit;
        } else {
            $error = "Invalid password for admin.";
        }
    } else {
        $stmt->close();

        // Then check in users
        $stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $username, $userHashed, $email);
            $stmt->fetch();
            if (password_verify($password, $userHashed)) {
                $_SESSION['user'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password for user.";
            }
        } else {
            $error = "User not found.";
        }
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - City Cruises</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="assets/favicon.webp">
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises</h1>
        <p>Login to access your bookings or the admin panel</p>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="booking.php">Booking</a></li>
                <li><a href="chatbot.php">Chatbot</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <section class="contact-form">
        <h2>Login</h2>

        <?php if ($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Username or Email:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn">Login</button>
        </form>

        <p style="margin-top: 15px; text-align: center;">
            Don't have an account? <a href="user/register.php">Register here</a>
        </p>
    </section>

    <footer>
        <p>&copy; <?= date("Y") ?> City Cruises. All rights reserved.</p>
    </footer>
</div>
</body>
</html>