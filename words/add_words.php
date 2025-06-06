<?php
session_start();
require '../config/db.php';

// Check if user is authenticated
$user_id = requireAuth();

$data = json_decode(file_get_contents("php://input"), true);
$word = $data['word'];
$definition = $data['definition'];

$stmt = $conn->prepare("INSERT INTO words (word, definition, user_id) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $word, $definition, $user_id);
$stmt->execute();

echo json_encode(['message' => 'Word added']);