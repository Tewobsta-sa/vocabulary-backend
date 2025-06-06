<?php
$host = 'localhost';
$db = 'vocabulary_practice';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['error' => 'DB connection failed']));
}
session_start();
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    return $_SESSION['user_id'];
}
?>
