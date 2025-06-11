<?php
$host = 'localhost';
$db = 'city_cruises'; // use your actual database name
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>