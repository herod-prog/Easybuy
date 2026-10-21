<?php
// Start session
session_start();

// Define absolute root path
define('ROOT_PATH', 'C:/xampp/htdocs/EasyBuy/');

// Define Base URL (capital E and B to match your folder)
define('BASE_URL', 'http://localhost/EasyBuy/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'easybuy_db');

// Create Connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check Connection
if (!$conn) {
    die("
        <div style='font-family: Arial; text-align: center; margin-top: 100px;'>
            <h2 style='color: red;'>Database Connection Failed!</h2>
            <p>" . mysqli_connect_error() . "</p>
            <p>Make sure MySQL is running in XAMPP Control Panel</p>
        </div>
    ");
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

// Function to sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}
?>