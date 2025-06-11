-- Create the database
CREATE DATABASE city_cruises;

-- Use the database
USE city_cruises;

-- Create 'yachts' table
CREATE TABLE yachts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL
);

-- Insert data into 'yachts' table
INSERT INTO yachts (name, description, price, image) VALUES
('Luxury Yacht', 'A luxury yacht for premium events.', 5000.00, 'luxury_yacht.jpg'),
('Family Yacht', 'Perfect for family trips and outings.', 2000.00, 'family_yacht.jpg'),
('Party Yacht', 'Great for parties and celebrations.', 3000.00, 'party_yacht.jpg'),
('Fishing Yacht', 'Ideal for fishing enthusiasts.', 1500.00, 'fishing_yacht.jpg'),
('Romantic Yacht', 'Perfect for a romantic getaway.', 2500.00, 'romantic_yacht.jpg'),
('Explorer Yacht', 'A yacht for long-distance exploration and adventure.', 6000.00, 'explorer_yacht.jpg'),
('Super Yacht', 'A mega yacht with luxurious amenities and high-end features.', 15000.00, 'super_yacht.jpg'),
('Vintage Yacht', 'A classic yacht with timeless design and elegance.', 4000.00, 'vintage_yacht.jpg'),
('Eco Yacht', 'An eco-friendly yacht with sustainable technology.', 3500.00, 'eco_yacht.jpg'),
('Spa Yacht', 'A yacht with spa services for relaxation and rejuvenation.', 4500.00, 'spa_yacht.jpg'),
('Adventurer Yacht', 'Perfect for thrill-seekers with extreme water sports features.', 5500.00, 'adventurer_yacht.jpg'),
('Sailing Yacht', 'A traditional sailing yacht for the pure sailing experience.', 3000.00, 'sailing_yacht.jpg'),
('Charter Yacht', 'A charter yacht for luxury crewed experiences.', 7000.00, 'charter_yacht.jpg'),
('Business Yacht', 'A yacht designed for business meetings and corporate events.', 8000.00, 'business_yacht.jpg'),
('Private Yacht', 'A private yacht for exclusive personal trips and getaways.', 10000.00, 'private_yacht.jpg');

-- Create 'bookings' table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    yacht_id INT NOT NULL,
    booking_date DATE NOT NULL,
    FOREIGN KEY (yacht_id) REFERENCES yachts(id)
);

-- Create 'chatbot' table
CREATE TABLE chatbot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_query VARCHAR(255) NOT NULL,
    bot_response TEXT NOT NULL
);

-- Insert data into 'chatbot' table
-- Insert additional data into 'chatbot' table
INSERT INTO chatbot (user_query, bot_response) VALUES
('Hello', 'Hello! Welcome to City Cruises. How can I assist you today?'),
('Hi', 'Hi there! How can I help you with your yacht booking today?'),
('How to Book A Yacht?', 'Sure! You can visit our booking page to reserve a yacht of your choice. Let me know if you need help choosing one!'),
('What are the Prices?', 'The price varies depending on the yacht. Please visit our booking page for details. I can also help you with specific yacht options if you like!'),
('What Services do You Offer?', 'We offer yacht rentals. Let me know if you want more details about any of these services!'),
('Do You Have Luxury Yachts?', 'Yes! We have luxury yachts perfect for premium events. You can book one through our booking page. Would you like more details?'),
('Tell me about the Family Yacht', 'Our Family Yacht is perfect for family trips and outings. It’s affordable and very spacious. The price starts at $2000. Would you like to book it?'),
('How Much is the Super Yacht?', 'Our Super Yacht is priced at $15,000. It offers luxurious amenities and high-end features. Would you like to know more or proceed with booking?'),
('Do You Have a Yacht for Parties?', 'Yes! Our Party Yacht is ideal for celebrations, parties, and events. It’s priced at $3000. Would you like to reserve it?'),
('Do You Offer Fishing Yachts?', 'Yes, we have a Fishing Yacht available, designed for fishing enthusiasts. It’s priced at $1500. Interested in booking?'),
('Can You Tell Me About the Eco Yacht?', 'Our Eco Yacht is a sustainable option that uses eco-friendly technology. It’s priced at $3500. Would you like to know more or book it?'),
('Is There a Spa Yacht?', 'Yes, we offer a Spa Yacht that includes spa services for relaxation and rejuvenation. It’s priced at $4500. Let me know if you want to book it!'),
('Do You Have a Yacht for Business Events?', 'Yes, our Business Yacht is designed for corporate meetings and events, priced at $8000. Let me know if you’d like more details or would like to book it.'),
('I Want to Book a Yacht', 'Great! You can head over to our booking page to select a yacht and reserve your preferred date. Would you like me to take you there?'),
('Can I Get a Discount?', 'Currently, we don’t offer discounts, but keep an eye out for special promotions on our website!'),
('What’s the Booking Process?', 'The booking process is simple. Just visit the booking page, choose your yacht, fill in the form with your details, and confirm your booking! Let me know if you need assistance.'),
('How Can I Contact You?', 'You can reach us through the contact page on our website. We’re happy to assist you with any further questions!'),
('Do You Offer Private Yachts?', 'Yes, we offer Private Yachts for exclusive personal trips. They are available for $10,000. Let me know if you’d like more information or wish to book!'),
('Thank You', 'You’re welcome! Let me know if there’s anything else I can assist with. Have a great day!'),
('Goodbye', 'Goodbye! We hope to see you soon for an amazing yacht experience!'),
('Can You Help Me with Booking?', 'Of course! I can guide you through the booking process or answer any questions about our yachts. Let me know how I can assist you!'),
('What’s Special About the Explorer Yacht?', 'The Explorer Yacht is perfect for long-distance exploration and adventure. It’s priced at $6000. Want to book it or know more?');