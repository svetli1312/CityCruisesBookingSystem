<?php
session_start();
session_unset();
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit;