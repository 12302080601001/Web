<?php
// --- BASE URL ---
// We don't need this for Render as paths are relative to the root.

// --- DATABASE CONNECTION ---
// Reads credentials securely from Render's Environment Variables
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- START SESSION ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- SET A UNIQUE USER ID FOR VOTE TRACKING ---
if (!isset($_SESSION['student_id'])) {
    $_SESSION['student_id'] = 'guest_' . uniqid();
}
?>