<?php
$host = 'localhost';
$db = 'flashcards_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['error' => 'DB connection failed']));
}
?>
