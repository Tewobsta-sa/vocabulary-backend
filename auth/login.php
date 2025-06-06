<?php
session_start();
require '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['message' => 'Login successful']);
    } else {
        echo json_encode(['error' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
