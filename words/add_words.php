<?php
session_start();
require '../config/db.php';

// Check if user is authenticated
$user_id = requireAuth();

$data = json_decode(file_get_contents("php://input"), true);
$word = $data['word'];
$definition = $data['definition'];
$difficulty = $data['difficulty'];

$stmt = $conn->prepare("INSERT INTO words (word, definition, user_id, difficulty) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $word, $definition, $user_id, $difficulty);
$stmt->execute();

echo json_encode(['message' => 'Word added']);