<?php session_start(); // Start session to manage login state ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Cruises - Home</title>
    <link rel="icon" type="image/png" href="assets/favicon.webp">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>            
            <h1>Welcome to City Cruises</h1>
            <p>Discover endless possibilities and start your adventure with us today!</p>
            <nav>
                <ul>
                    <!-- Always visible pages -->
                    <li><a href="index.php">Home</a></li>
                    <li><a href="booking.php">Booking</a></li>
                    <li><a href="chatbot.php">Chatbot</a></li>
                    <li><a href="contact.php">Contact</a></li>

                    <!-- Conditional links based on user role -->
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

        <!-- Hero banner section -->
        <section class="hero">
            <img src="assets/hero.webp" alt="Luxury Yacht" class="hero-image">
            <h2>Your Gateway to Luxurious Yacht Adventures</h2>
            <p>Whether it's a romantic getaway, family outing, or a grand celebration, City Cruises has the perfect yacht for you.</p>
        </section>

        <!-- Features section -->
        <section class="features">
            <h2>Why Choose Us?</h2>
            <div class="feature-grid">
                <div class="feature">
                    <img src="assets/luxury.webp" alt="Luxury">
                    <h3>Luxurious Yachts</h3>
                    <p>We offer a wide range of luxurious yachts to make your journey unforgettable.</p>
                </div>
                <div class="feature">
                    <img src="assets/safe.jpeg" alt="Safe">
                    <h3>Safety First</h3>
                    <p>Your safety is our priority. All our yachts are fully equipped and maintained.</p>
                </div>
                <div class="feature">
                    <img src="assets/experience.jpeg" alt="Experience">
                    <h3>Unparalleled Experience</h3>
                    <p>Our crew ensures you have a seamless and enjoyable cruise experience.</p>
                </div>
            </div>
        </section>

        <!-- Call-to-action section -->
        <section class="cta">
            <h2>Ready to Set Sail?</h2>
            <p>Book your dream yacht today and experience the luxury of City Cruises.</p>
            <a href="booking.php" class="btn">Book Now</a>
        </section>

        <footer>
            <p>&copy; 2024 City Cruises. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>