<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);
$word = $data['word'];
$meaning = $data['meaning'];

$stmt = $conn->prepare("INSERT INTO words (user_id, word, meaning) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $word, $meaning);
$stmt->execute();

echo json_encode(['message' => 'Word added']);
?>
