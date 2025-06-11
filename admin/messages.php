<?php
session_start();
require_once "../includes/db.php";

// Only allow access if admin
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all messages
$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Messages - City Cruises</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .admin-panel {
            margin-top: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #072f57;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .message-cell {
            max-width: 500px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises Admin</h1>
        <p>Contact Messages</p>
        <nav>
            <ul>
                <li><a href="dashboard.php">Admin Panel</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="admin-panel">
        <h2>Received Messages</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sender</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Received</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td class="message-cell"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                            <td><?= $row['created_at'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No messages found.</td></tr>
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