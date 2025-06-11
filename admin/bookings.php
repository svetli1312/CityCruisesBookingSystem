<?php
session_start();
require_once "../includes/db.php";

// Only allow access if admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all bookings ordered by latest date first
$result = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bookings - City Cruises</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Admin panel layout and table styling */
        .admin-panel {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #072f57;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises Admin</h1>
        <p>Booking Overview</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Admin Panel</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="admin-panel">
        <h2>All Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Yacht ID</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <!-- Display each booking record -->
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= $row['yacht_id'] ?></td>
                            <td><?= $row['booking_date'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- Message if no bookings exist -->
                    <tr><td colspan="6">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <footer>
        <p>&copy; <?= date("Y"); ?> City Cruises. All rights reserved.</p>
    </footer>
</div>
</body>
</html>