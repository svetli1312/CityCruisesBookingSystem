<?php
session_start();
require_once "includes/db.php";

$message_sent = false;
$error = "";

// Prefill logic for logged-in users
$namePrefill = $_SESSION['user'] ?? '';
$emailPrefill = $_SESSION['email'] ?? '';

// If the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Reassign to keep input values on failure
    $namePrefill = $name;
    $emailPrefill = $email;

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $message_sent = true;
        } else {
            $error = "There was a problem sending your message. Please try again.";
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Cruises - Contact</title>
    <link rel="icon" type="image/png" href="assets/favicon.webp">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises</h1>
        <p>Reach out to us—your connection to assistance, answers, and opportunities starts here!</p>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="booking.php">Booking</a></li>
                <li><a href="chatbot.php">Chatbot</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
                    <li><a href="user/mybookings.php">My Bookings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="user/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="contact-form">
        <h2>Get in Touch</h2>

        <?php if ($message_sent): ?>
            <p class="success">✅ Thank you for reaching out! We'll get back to you shortly.</p>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required
                       value="<?= htmlspecialchars($namePrefill) ?>"
                       <?= isset($_SESSION['user']) ? 'readonly' : '' ?>>

                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($emailPrefill) ?>"
                       <?= isset($_SESSION['email']) ? 'readonly' : '' ?>>

                <label for="message">Your Message:</label>
                <textarea id="message" name="message" rows="5" required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>

                <button type="submit" class="btn">Send Message</button>
            </form>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; <?= date("Y"); ?> City Cruises. All rights reserved.</p>
    </footer>
</div>
</body>
</html>