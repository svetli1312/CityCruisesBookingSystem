# CityCruises Smart Booking System

A web-based seat booking system for yacht cruises with admin and user login, seat selection, and a GPT-powered chatbot assistant.

## Features

- Visual seatmap with per-date seat booking
- User login and registration with booking dashboard
- Admin panel for managing bookings and messages
- Chatbot powered by OpenAI GPT API for assistance
- Clean interface with session and form handling

## Technologies Used

- PHP, JavaScript, MySQL
- Bootstrap (for layout and styling)
- GPT API (OpenAI)
- AJAX (for chatbot interaction)
- Session management, database integration

## Project Structure

- `index.php` – Homepage  
- `booking.php` – Main booking logic  
- `book-seat.php` – Handles booking actions  
- `seatmap.php` – Visual seat selector  
- `chatbot.php` – ChatGPT-powered interaction  
- `login.php`, `logout.php`, `users-login.php` – Authentication  
- `contact.php` – Contact form  
- `admin/` – Admin dashboard, login, bookings, messages  
- `user/` – Registration, login, view bookings  
- `includes/db.php` – Database connection  
- `database.sql` – MySQL schema file

## Setup Instructions

1. Clone the repository: git clone https://github.com/svetli1312/CityCruisesBookingSystem.git

2. Import `database.sql` into your local MySQL server.

3. Place the project in your local server directory (e.g., `htdocs` for XAMPP).

4. Update database credentials in `includes/db.php`.

5. Insert your OpenAI API key into `chatbot.php` or load it using environment variables.

## Security Notice

The OpenAI API key is removed from this repository. For safe usage, store your key securely and do not hardcode it in public code. Use environment variables or a separate configuration file excluded via `.gitignore`.

## License

This project is shared for educational and demonstration purposes. You may reuse or modify it with proper attribution.
