<?php
require_once "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat_id'])) {
    $seatId = intval($_POST['seat_id']);
    $seatLabel = $_POST['seat_label'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $booking_date = $conn->real_escape_string($_POST['booking_date']);

    // Check if already booked on this date
    $check = $conn->prepare("SELECT id FROM seat_bookings WHERE seat_id = ? AND booking_date = ?");
    $check->bind_param("is", $seatId, $booking_date);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO seat_bookings (seat_id, name, email, phone, booking_date) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("issss", $seatId, $name, $email, $phone, $booking_date);
        $insert->execute();
        header("Location: seatmap.php?success=1&label=" . urlencode($seatLabel) . "&date=" . urlencode($booking_date));
    } else {
        header("Location: seatmap.php?error=1&label=" . urlencode($seatLabel) . "&date=" . urlencode($booking_date));
    }

    $check->close();
    exit;
}
header("Location: seatmap.php");
exit;