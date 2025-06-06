<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['error' => 'Missing flashcard ID']);
    exit;
}

$id = intval($data->id);

// Optional: allow updating front_text and back_text manually
$front_text = $data->front_text ?? null;
$back_text = $data->back_text ?? null;

if (!$front_text && !$back_text) {
    echo json_encode(['error' => 'Nothing to update']);
    exit;
}

// Build dynamic query parts depending on provided fields
$fields = [];
$params = [];
$types = "";

if ($front_text !== null) {
    $fields[] = "front_text = ?";
    $params[] = $front_text;
    $types .= "s";
}

if ($back_text !== null) {
    $fields[] = "back_text = ?";
    $params[] = $back_text;
    $types .= "s";
}

$fields[] = "updated_at = NOW()";

$sql = "UPDATE flashcards SET " . implode(", ", $fields) . " WHERE id = ? AND user_id = ?";
$params[] = $id;
$params[] = $user_id;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Flashcard updated successfully']);
} else {
    echo json_encode(['error' => $stmt->error]);
}
?>
