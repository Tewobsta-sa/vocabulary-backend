<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing flashcard ID']);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM flashcards WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['message' => 'Flashcard deleted successfully']);
    } else {
        echo json_encode(['error' => 'Flashcard not found or unauthorized']);
    }
} else {
    echo json_encode(['error' => $stmt->error]);
}
?>
