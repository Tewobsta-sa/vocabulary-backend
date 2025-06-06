<?php
session_start();
require '../config/db.php';

$user_id = $_GET['user_id'];

$sql = "SELECT * FROM flashcards WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$flashcards = [];
while ($row = $result->fetch_assoc()) {
    $flashcards[] = $row;
}

echo json_encode($flashcards);
?>
