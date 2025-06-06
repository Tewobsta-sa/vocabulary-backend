<?php
session_start();
require '../config/db.php';

$user_id = $_GET['user_id'];

$sql = "SELECT * FROM flashcard_decks WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$decks = [];
while ($row = $result->fetch_assoc()) {
    $decks[] = $row;
}

echo json_encode($decks);
?>
