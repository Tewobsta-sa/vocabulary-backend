<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM flashcards WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$flashcards = [];
while ($row = $result->fetch_assoc()) {
    $flashcards[] = $row;
}

echo json_encode($flashcards);
?>
