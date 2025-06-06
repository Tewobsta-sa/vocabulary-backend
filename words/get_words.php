<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT * FROM words WHERE user_id = $user_id");
$words = [];

while ($row = $result->fetch_assoc()) {
    $words[] = $row;
}
echo json_encode($words);
?>
