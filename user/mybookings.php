<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userEmail = $_SESSION['email'] ?? '';

// Cancel yacht booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_yacht_id'])) {
    $bookingId = (int)$_POST['cancel_yacht_id'];
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $bookingId, $userEmail);
    $stmt->execute();
    $stmt->close();
    header("Location: mybookings.php?cancel=yacht");
    exit;
}

// Cancel seat booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_seat_id'])) {
    $seatId = (int)$_POST['cancel_seat_id'];
    $stmt = $conn->prepare("DELETE FROM seat_bookings WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $seatId, $userEmail);
    $stmt->execute();
    $stmt->close();
    header("Location: mybookings.php?cancel=seat");
    exit;
}

// Get yacht bookings
$stmt = $conn->prepare("SELECT id, yacht_name, booking_date, phone FROM bookings WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$yacht_result = $stmt->get_result();
$stmt->close();

// Get seat bookings with seat label
$seat_stmt = $conn->prepare("
    SELECT sb.id, ys.seat_label, sb.booking_date
    FROM seat_bookings sb
    JOIN yacht_seats ys ON sb.seat_id = ys.id
    WHERE sb.email = ?
");
$seat_stmt->bind_param("s", $userEmail);
$seat_stmt->execute();
$seat_result = $seat_stmt->get_result();
$seat_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - City Cruises</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="icon" href="../assets/favicon.webp" type="image/png">
    <style>
        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .booking-table th,
        .booking-table td {
            padding: 10px 15px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .booking-table th {
            background-color: #072f57;
            color: white;
        }

        .cancel-btn {
            background: crimson;
            border: none;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .success-msg {
            color: green;
            font-weight: bold;
            text-align: center;
            margin: 20px auto;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises</h1>
        <p>Your current bookings</p>
        <nav>
            <ul>
                <li><a href="../index.php">Home</a></li>
                <li><a href="../booking.php">Booking</a></li>
                <li><a href="../chatbot.php">Chatbot</a></li>
                <li><a href="../contact.php">Contact</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h2>My Yacht Bookings</h2>

        <?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'yacht'): ?>
            <p class="success-msg">✅ Yacht booking cancelled successfully.</p>
        <?php endif; ?>

        <?php if ($yacht_result->num_rows > 0): ?>
            <table class="booking-table">
                <tr>
                    <th>Yacht</th>
                    <th>Date</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $yacht_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['yacht_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['booking_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Cancel this yacht booking?')">
                                <input type="hidden" name="cancel_yacht_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="cancel-btn">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;">You don’t have any yacht bookings yet.</p>
        <?php endif; ?>
    </section>

    <section>
        <h2>My Seat Bookings</h2>

        <?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'seat'): ?>
            <p class="success-msg">✅ Seat booking cancelled successfully.</p>
        <?php endif; ?>

        <?php if ($seat_result->num_rows > 0): ?>
            <table class="booking-table">
                <tr>
                    <th>Seat</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $seat_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['seat_label'] ?? '') ?></td>
                        <td><?= htmlspecialchars($row['booking_date'] ?? '') ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Cancel this seat booking?')">
                                <input type="hidden" name="cancel_seat_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="cancel-btn">Cancel</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;">You don’t have any seat bookings yet.</p>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; <?= date("Y") ?> City Cruises. All rights reserved.</p>
    </footer>
</div>
</body>
</html>