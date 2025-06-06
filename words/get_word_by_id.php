<?php
session_start();
require '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get word ID from query parameter
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing word ID']);
    exit;
}

$word_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM words WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $word_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
if ($word = $result->fetch_assoc()) {
    echo json_encode($word);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Word not found']);
}
?>
