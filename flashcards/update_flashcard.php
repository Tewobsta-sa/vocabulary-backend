<?php
session_start();
require '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

$sql = "UPDATE flashcards SET front_text = ?, back_text = ?, updated_at = NOW() WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $data->front_text, $data->back_text, $data->id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Flashcard updated"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
