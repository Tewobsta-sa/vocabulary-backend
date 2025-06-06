<?php
session_start();
require '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing word ID']);
    exit;
}

$word_id = intval($data['id']);
$word = isset($data['word']) ? $data['word'] : null;
$definition = isset($data['definition']) ? $data['definition'] : null;
$example = isset($data['example_sentence']) ? $data['example_sentence'] : null;

// Check if the word exists and belongs to the user
$check = $conn->prepare("SELECT id FROM words WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $word_id, $user_id);
$check->execute();
$result = $check->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Word not found or unauthorized']);
    exit;
}

// Update query (only update fields that are provided)
$stmt = $conn->prepare("
    UPDATE words 
    SET word = COALESCE(?, word), 
        definition = COALESCE(?, definition), 
        example_sentence = COALESCE(?, example_sentence), 
        updated_at = NOW()
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("sssii", $word, $definition, $example, $word_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Word updated successfully']);
} else {
    echo json_encode(['error' => $stmt->error]);
}
