<?php
session_start();
require '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

$sql = "DELETE FROM flashcards WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $data->id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Flashcard deleted"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
