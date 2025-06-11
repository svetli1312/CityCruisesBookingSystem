<?php
// Enable error reporting for development/debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../includes/db.php";

$error = "";
$success = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect form inputs
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Secure password hashing

    // Check if username or email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // User already exists
        $error = "❌ Username or email already taken.";
    } else {
        // Register new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            $success = "✅ Account created! <a href='login.php'>Click here to log in</a>.";
        } else {
            $error = "❌ Registration failed. Please try again.";
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - City Cruises</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" type="image/png" href="../assets/favicon.webp">
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises</h1>
        <p>Create your account</p>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../booking.php">Booking</a></li>
                <li><a href="../chatbot.php">Chatbot</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <section class="contact-form">
        <h2>Register</h2>

        <!-- Show error message if any -->
        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;"><?= $error ?></p>
        <?php endif; ?>

        <!-- Show success message if account created -->
        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;"><?= $success ?></p>
        <?php endif; ?>

        <!-- Registration form -->
        <form method="post">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" class="btn">Register</button>
        </form>
    </section>

    <footer>
        <p>&copy; <?= date("Y") ?> City Cruises. All rights reserved.</p>
    </footer>
</div>
</body>
</html>