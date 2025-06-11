<?php
session_start();
require_once "includes/db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize chat history
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Clear chat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_chat'])) {
    $_SESSION['chat_history'] = [];
    unset($_SESSION['pending_cancel']);
    header("Location: chatbot.php");
    exit;
}

// Handle query
$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    $user_query = trim($_POST['query']);
    $is_logged_in = isset($_SESSION['user']) && isset($_SESSION['email']);
    $username = $_SESSION['user'] ?? 'Guest';
    $email = $_SESSION['email'] ?? '';
    $bookings_info = "You are not logged in, so I cannot access your bookings.";
    $personal_greeting = "Hello! How can I assist you today?";

    if ($is_logged_in) {
        $personal_greeting = "Hello $username! What would you like help with today?";

        $stmt = $conn->prepare("SELECT id, yacht_name, booking_date FROM bookings WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $has_booking = false;
        $booking_id = null;
        $yacht_name = null;
        $booking_date = null;

        if ($result->num_rows > 0) {
            $has_booking = true;
            $row = $result->fetch_assoc();
            $booking_id = $row['id'];
            $yacht_name = $row['yacht_name'];
            $booking_date = $row['booking_date'];

            $bookings_info = "You have a booking for the $yacht_name on $booking_date.";
        } else {
            $bookings_info = "You're logged in as $username, but you don’t have any bookings.";
        }
        $stmt->close();

        // Cancellation flow
        if ($has_booking && stripos($user_query, 'cancel') !== false) {
            $_SESSION['pending_cancel'] = [
                'id' => $booking_id,
                'yacht' => $yacht_name,
                'date' => $booking_date
            ];
            $bookings_info = "I'm sorry to hear that you want to cancel your booking for the $yacht_name on $booking_date.\n\nCan you please confirm by typing 'yes'?";
        } elseif (isset($_SESSION['pending_cancel']) && in_array(strtolower($user_query), ['yes', 'yes please', 'confirm'])) {
            $pending = $_SESSION['pending_cancel'];
            $cancel_stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND email = ?");
            $cancel_stmt->bind_param("is", $pending['id'], $email);
            if ($cancel_stmt->execute()) {
                $bookings_info = "✅ Your booking for the {$pending['yacht']} on {$pending['date']} has been cancelled successfully.";
            } else {
                $bookings_info = "❌ Something went wrong while cancelling. Please try again.";
            }
            unset($_SESSION['pending_cancel']);
            $cancel_stmt->close();
        } elseif (isset($_SESSION['pending_cancel'])) {
            $pending = $_SESSION['pending_cancel'];
            $bookings_info = "Please confirm: Cancel booking for the {$pending['yacht']} on {$pending['date']}? Type 'yes' to confirm.";
        }
    }

    // Call ChatGPT API
    $apiKey = "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";
    $apiUrl = "https://api.openai.com/v1/chat/completions";

    $postData = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" =>
                "You are a chatbot assistant for a student project called CityCruises.\n\n" .
                $personal_greeting . "\n\n" . $bookings_info
            ],
            ["role" => "user", "content" => $user_query]
        ],
        "temperature" => 0.7,
        "max_tokens" => 300
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    ]);
    $result = curl_exec($ch);
    $data = json_decode($result, true);
    $response = $data['choices'][0]['message']['content'] ?? "Sorry, I didn't understand.";
    curl_close($ch);

    $_SESSION['chat_history'][] = ['user' => $user_query, 'bot' => $response];

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        foreach ($_SESSION['chat_history'] as $chat) {
            echo '<div class="chat-message user"><strong>You:</strong> ' . htmlspecialchars($chat['user']) . '</div>';
            echo '<div class="chat-message bot"><strong>Bot:</strong> ' . nl2br(htmlspecialchars($chat['bot'])) . '</div>';
        }
        exit;
    }
}
?>

<!-- HTML Interface -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>City Cruises - Chatbot</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.webp">
</head>
<body>
<div class="container">
    <header>
        <h1>City Cruises Chatbot</h1>
        <p>Ask anything about cruises or bookings!</p>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="booking.php">Booking</a></li>
                <li><a href="chatbot.php">Chatbot</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Admin Panel</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif (isset($_SESSION['user'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="chatbot">
        <div id="chat-container">
            <div id="chat-log">
                <?php foreach ($_SESSION['chat_history'] as $chat): ?>
                    <div class="chat-message user"><strong>You:</strong> <?= htmlspecialchars($chat['user']) ?></div>
                    <div class="chat-message bot"><strong>Bot:</strong> <?= nl2br(htmlspecialchars($chat['bot'])) ?></div>
                <?php endforeach; ?>
            </div>

            <form id="chat-form">
                <input type="text" name="query" id="query" placeholder="Type your message here..." required>
                <button type="submit" class="btn">Send</button>
            </form>

            <form method="POST" id="clear-chat-form">
                <button type="submit" name="clear_chat" class="btn-clear">Clear Conversation</button>
            </form>
        </div>
    </section>

    <footer>
        <p>&copy; <?= date("Y") ?> City Cruises. All rights reserved.</p>
    </footer>
</div>

<script>
document.getElementById("chat-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const input = document.getElementById("query");
    const chatLog = document.getElementById("chat-log");
    const query = input.value.trim();
    if (!query) return;

    const formData = new FormData();
    formData.append("query", query);

    fetch("chatbot.php", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(res => res.text())
    .then(data => {
        chatLog.innerHTML = data;
        input.value = "";
        chatLog.scrollTop = chatLog.scrollHeight;
    });
});
</script>
</body>
</html>