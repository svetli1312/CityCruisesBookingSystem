<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

// Handle POST request to cancel a seat booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $bookingId = (int)$_POST['booking_id'];

    // Delete the seat booking from the database
    $stmt = $conn->prepare("DELETE FROM seat_bookings WHERE id = ?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to the admin seat bookings page
header("Location: seatbookings.php");
exit;