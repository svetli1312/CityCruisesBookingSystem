<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../includes/db.php";

// Redirect logged-in users immediately
if (isset($_SESSION['user']) && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
    exit;
}
if (isset($_SESSION['user']) && $_SESSION['role'] === 'user') {
    header("Location: ../index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

   // Check admin first
$stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $usernameOrEmail);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($adminId, $adminUsername, $adminHashed);
    $stmt->fetch();
    
    // ✅ Admin login success
    if (password_verify($password, $adminHashed)) {
        $_SESSION['user'] = $adminUsername;
        $_SESSION['email'] = $adminUsername . '@admin.local'; // Fake email so chatbot still works
        $_SESSION['role'] = 'admin';
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid password.";
    }
    } else {
        $stmt->close();

        // Check regular user
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $usernameOrEmail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $userHashed);
            $stmt->fetch();
            if (password_verify($password, $userHashed)) {
                $_SESSION['user'] = $usernameOrEmail;
                $_SESSION['role'] = 'user';
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Invalid password.";
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
    <title>City Cruises - Login</title>
    <link rel="icon" href="../assets/favicon.webp" type="image/png">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome to City Cruises</h1>
            <p>Login to access your bookings or the admin panel</p>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../booking.php">Booking</a></li>
                    <li><a href="../chatbot.php">Chatbot</a></li>
                    <li><a href="../contact.php">Contact</a></li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="dashboard.php">Admin Panel</a></li>
                        <li><a href="../logout.php">Logout</a></li>
                    <?php elseif (isset($_SESSION['user'])): ?>
                        <li><a href="../logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <section class="login-section">
            <h2>Login</h2>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="post" class="login-form">
                <label>Username or Email:
                    <input type="text" name="username" required>
                </label><br><br>
                <label>Password:
                    <input type="password" name="password" required>
                </label><br><br>
                <button type="submit" class="btn">Login</button>
            </form>
        </section>

        <footer>
            <p>&copy; <?= date("Y"); ?> City Cruises. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>