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

$stmt = $conn->prepare("SELECT * FROM flashcards WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($flashcard = $result->fetch_assoc()) {
    echo json_encode($flashcard);
} else {
    echo json_encode(['error' => 'Flashcard not found or unauthorized']);
}
?>
