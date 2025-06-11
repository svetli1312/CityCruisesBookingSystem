<?php
$password = "user123"; // your password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed password for '{$password}':<br><br>";
echo "<code>$hash</code>";