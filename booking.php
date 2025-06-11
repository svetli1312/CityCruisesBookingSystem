<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'city_cruises';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Prefill from session
$namePrefill = $_SESSION['user'] ?? '';
$emailPrefill = $_SESSION['email'] ?? '';

// Fetch yachts
$sql = "SELECT * FROM yachts";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>City Cruises - Booking</title>
    <link rel="icon" type="image/png" href="assets/favicon.webp">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises Booking</h1>
        <p>Select your yacht and book your adventure</p>
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

    <section class="yachts">
        <h2>Available Yachts</h2>
        <div class="yacht-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="yacht-card">';
                    echo '<img src="assets/' . $row['image'] . '" alt="' . $row['name'] . '">';
                    echo '<h3>' . $row['name'] . '</h3>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<p><strong>Price:</strong> $' . number_format($row['price'], 2) . '</p>';
                    echo '<a href="booking.php?yacht_id=' . $row['id'] . '#booking-form" class="btn">Book Now</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No yachts available at the moment.</p>';
            }
            ?>
        </div>
    </section>

    <section id="booking-form">
        <h2>Booking Form</h2>
        <form id="bookingForm" method="POST">
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($namePrefill) ?>" <?= isset($_SESSION['user']) ? 'readonly' : '' ?>>

            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($emailPrefill) ?>" <?= isset($_SESSION['email']) ? 'readonly' : '' ?>>

            <label for="phone">Your Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="yacht">Select Yacht:</label>
            <select id="yacht" name="yacht_id" required>
                <?php
                $result->data_seek(0); // Reset pointer
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
                ?>
            </select>

            <label for="date">Booking Date:</label>
            <input type="date" id="date" name="booking_date" required>

            <button type="submit" class="btn">Submit Booking</button>
        </form>
    </section>

    <?php
    // Handle standard bookings (not seat-based)
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yacht_id']) && $_POST['yacht_id'] != "1") {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $yacht_id = (int)$_POST['yacht_id'];
        $booking_date = $conn->real_escape_string($_POST['booking_date']);

        // Get yacht name
        $yachtStmt = $conn->prepare("SELECT name FROM yachts WHERE id = ?");
        $yachtStmt->bind_param("i", $yacht_id);
        $yachtStmt->execute();
        $yachtStmt->bind_result($yacht_name);
        $yachtStmt->fetch();
        $yachtStmt->close();

        // Save booking
        $stmt = $conn->prepare("INSERT INTO bookings (name, email, phone, yacht_id, yacht_name, booking_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $name, $email, $phone, $yacht_id, $yacht_name, $booking_date);

        if ($stmt->execute()) {
            echo '<p class="success">✅ Your booking has been successfully placed!</p>';
        } else {
            echo '<p class="error">❌ An error occurred. Please try again later.</p>';
        }
        $stmt->close();
    }

    $conn->close();
    ?>

    <footer>
        <p>&copy; <?= date("Y") ?> City Cruises. All rights reserved.</p>
    </footer>
</div>

<script>
document.getElementById('bookingForm').addEventListener('submit', function (e) {
    const yachtId = document.getElementById('yacht').value;
    if (yachtId === "1") {
        e.preventDefault();
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const date = document.getElementById('date').value;

        window.location.href = "seatmap.php?name=" + encodeURIComponent(name)
            + "&email=" + encodeURIComponent(email)
            + "&phone=" + encodeURIComponent(phone)
            + "&date=" + encodeURIComponent(date);
    }
});
</script>
</body>
</html>