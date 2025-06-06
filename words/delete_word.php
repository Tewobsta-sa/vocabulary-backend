<?php
session_start();
require '../config/db.php';

// Check for user login
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get word id from URL
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing word ID']);
    exit;
}

$word_id = intval($_GET['id']);

// Prepare and run delete query
$stmt = $conn->prepare("DELETE FROM words WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $word_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['message' => 'Word deleted successfully']);
    } else {
        echo json_encode(['error' => 'Invalid word ID or unauthorized']);
    }
} else {
    echo json_encode(['error' => $stmt->error]);
}
