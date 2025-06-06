<?php
session_start();
require '../config/db.php';
$user_id = $_SERVER['user_id'];


$data = json_decode(file_get_contents("php://input"));

$sql = "INSERT INTO flashcards (user_id, word_id, front_text, back_text, created_at, updated_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $data->user_id, $data->word_id, $data->front_text, $data->back_text);

if ($stmt->execute()) {
    echo json_encode(["message" => "Flashcard created successfully"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
