<?php
require_once "includes/db.php";

$userName = $_GET['name'] ?? '';
$userEmail = $_GET['email'] ?? '';
$userPhone = $_GET['phone'] ?? '';
$userDate = $_GET['date'] ?? '';

if (empty($userDate)) {
    die("Booking date is required.");
}

// Fetch seats
$seats = [];
$seat_result = $conn->query("SELECT * FROM yacht_seats WHERE yacht_id = 1");
while ($row = $seat_result->fetch_assoc()) {
    $seats[$row['seat_label']] = $row;
}

// Fetch booked seats for this date
$booked_seat_ids = [];
$booked = $conn->prepare("SELECT seat_id FROM seat_bookings WHERE booking_date = ?");
$booked->bind_param("s", $userDate);
$booked->execute();
$res = $booked->get_result();
while ($r = $res->fetch_assoc()) {
    $booked_seat_ids[] = $r['seat_id'];
}
$booked->close();

// Seat box generator
function seatBox($label, $seats, $bookedIds) {
    if (!isset($seats[$label])) return "<div class='seat'>$label</div>";
    $seatId = $seats[$label]['id'];
    $class = 'seat';
    if (in_array($seatId, $bookedIds)) $class .= ' booked';

    if (in_array($label, ['21','22','23','24','25','26','27','28','29','30','31','32','33','34'])) {
        $class .= ' vip';
    } elseif (in_array($label, ['1','2','3','4','5','6','15','16','17','18','19','20'])) {
        $class .= ' window';
    }

    return "<div class='$class' data-id='$seatId' data-label='$label'>$label</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Choose Your Seat - City Cruises</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="assets/favicon.webp">
  <style>
    .seatmap-content { margin-top: 30px; text-align: center; }
    .row { display: flex; justify-content: center; margin-bottom: 10px; gap: 10px; }
    .column { display: flex; flex-direction: column; gap: 10px; }
    .seat {
      width: 50px; height: 50px; background: #0073e6; color: white;
      display: flex; justify-content: center; align-items: center;
      border-radius: 8px; cursor: pointer; position: relative; font-weight: bold;
    }
    .seat.booked { background: #ccc; cursor: not-allowed; }
    .seat.selected { background: #2e8b57; }
    .seat.vip { border: 2px solid gold; }
    .seat.vip::after {
      content: "VIP"; font-size: 10px; color: gold;
      position: absolute; bottom: 2px; right: 4px;
    }
    .seat.window { border: 2px dashed #7db7ff; }
    .seat.window::after {
      content: "Window"; font-size: 10px; color: white;
      position: absolute; bottom: 2px; right: 4px;
    }
    form button {
      margin-top: 25px;
      padding: 10px 20px;
      background-color: #072f57;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    form button:hover { background-color: #0050a0; }
  </style>
</head>
<body>
<div class="container">
  <header>
    <h1>Welcome to City Cruises</h1>
    <p>Choose your seat aboard the Dinner Cruise Yacht</p>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="booking.php">Booking</a></li>
        <li><a href="chatbot.php">Chatbot</a></li>
        <li><a href="contact.php">Contact</a></li>

      </ul>
    </nav>
  </header>

  <section class="seatmap-content">
    <h2>Seat Map - Dinner Cruise Yacht</h2>

    <?php if (isset($_GET['success'])): ?>
      <p style="color: green; font-weight: bold;">✅ Seat <?= htmlspecialchars($_GET['label']) ?> booked successfully for <?= $userDate ?>!</p>
    <?php elseif (isset($_GET['error'])): ?>
      <p style="color: red; font-weight: bold;">❌ Seat <?= htmlspecialchars($_GET['label']) ?> is already booked on <?= $userDate ?>.</p>
    <?php endif; ?>

    <div id="seatmap">
      <!-- Top Arc -->
      <div class="row">
        <?php foreach (['21','22','23','24','25','26','27'] as $label) echo seatBox($label, $seats, $booked_seat_ids); ?>
      </div>
      <div class="row">
        <?php foreach (['28','29','30','31','32','33','34'] as $label) echo seatBox($label, $seats, $booked_seat_ids); ?>
      </div>

      <!-- Middle -->
      <div class="row">
        <div class="column">
          <?php foreach (['1','2','3','4','5','6'] as $label) echo seatBox($label, $seats, $booked_seat_ids); ?>
        </div>
        <div class="column" style="margin: 0 40px;">
          <?php foreach (['7L','8L','9L','10L','11L','12L','13L','14L'] as $label) echo seatBox($label, $seats, $booked_seat_ids); ?>
        </div>
        <div class="column" style="margin-right: 40px;">
          <?php foreach (['7R','8R','9R','10R','11R','12R','13R','14R'] as $label) echo seatBox($label, $seats, $booked_seat_ids); ?>
        </div>
        <div class="column">
          <?php foreach (['15','16','17','18','19','20'] as $label) echo seatBox($label, $seats, $booked_seat_ids); ?>
        </div>
      </div>
    </div>

    <form method="POST" action="book-seat.php" id="booking-form">
      <input type="hidden" name="seat_id" id="seat_id">
      <input type="hidden" name="seat_label" id="seat_label">
      <input type="hidden" name="name" value="<?= htmlspecialchars($userName) ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($userEmail) ?>">
      <input type="hidden" name="phone" value="<?= htmlspecialchars($userPhone) ?>">
      <input type="hidden" name="booking_date" value="<?= htmlspecialchars($userDate) ?>">
      <button type="submit">Confirm Booking</button>
    </form>
  </section>

  <footer>
    <p>&copy; <?= date("Y"); ?> City Cruises. All rights reserved.</p>
  </footer>
</div>

<script>
  const seats = document.querySelectorAll('.seat:not(.booked)');
  const seatIdInput = document.getElementById('seat_id');
  const seatLabelInput = document.getElementById('seat_label');

  seats.forEach(seat => {
    seat.addEventListener('click', () => {
      seats.forEach(s => s.classList.remove('selected'));
      seat.classList.add('selected');
      seatIdInput.value = seat.dataset.id;
      seatLabelInput.value = seat.dataset.label;
    });
  });

  document.getElementById('booking-form').addEventListener('submit', function(e) {
    if (!seatIdInput.value) {
      e.preventDefault();
      alert("Please select a seat before confirming.");
    }
  });
</script>
</body>
</html>