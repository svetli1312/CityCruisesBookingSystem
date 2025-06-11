<?php
session_start();

// Only allow access if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$adminEmail = $_SESSION['user'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>City Cruises - Admin Panel</title>
  <link rel="stylesheet" href="../styles.css">
  <style>
    .admin-panel {
      margin-top: 30px;
    }

    .admin-panel h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .feature-grid {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    .feature {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 220px;
      text-align: center;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      user-select: none;
    }

    .feature:hover {
      transform: scale(1.05);
      box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    }

    .feature h3 {
      margin-bottom: 10px;
    }

    .feature * {
      pointer-events: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1>Welcome to City Cruises</h1>
      <p>Admin Panel Access for Managing Bookings and Messages</p>
      <nav>
        <ul>
          <li><a href="../index.php">Home</a></li>
          <li><a href="../booking.php">Booking</a></li>
          <li><a href="../chatbot.php">Chatbot</a></li>
          <li><a href="../contact.php">Contact</a></li>
          <li><a href="dashboard.php">Admin Panel</a></li>
          <li><a href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </header>

    <section class="admin-panel">
      <h2>Welcome back, <?= htmlspecialchars($adminEmail) ?></h2>

      <div class="feature-grid">
        <div class="feature" onclick="location.href='bookings.php'">
          <h3>View Bookings</h3>
          <p>Check and manage all user bookings.</p>
        </div>

        <div class="feature" onclick="location.href='seatbookings.php'">
          <h3>Seat Bookings</h3>
          <p>Manage individual seat selections and bookings.</p>
        </div>

        <div class="feature" onclick="location.href='messages.php'">
          <h3>Contact Messages</h3>
          <p>Read and respond to customer inquiries.</p>
        </div>

        <div class="feature" onclick="location.href='../logout.php'">
          <h3>Logout</h3>
          <p>Exit the admin panel securely.</p>
        </div>
      </div>
    </section>

    <footer>
      <p>&copy; <?= date("Y"); ?> City Cruises. All rights reserved.</p>
    </footer>
  </div>
</body>
</html>