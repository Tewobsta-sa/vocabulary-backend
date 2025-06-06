<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['name'])) {
    echo json_encode(['error' => 'Missing word name']);
    exit;
}

$wordName = $_GET['name'];

// Optional: ensure the user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM words WHERE word = ? AND user_id = ?");
$stmt->bind_param("si", $wordName, $user_id);
$stmt->execute();

$result = $stmt->get_result();
if ($word = $result->fetch_assoc()) {
    echo json_encode($word);
} else {
    echo json_encode(['error' => 'Word not found']);
}
?>
