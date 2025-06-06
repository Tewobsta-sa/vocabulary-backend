<?php
session_start();
require '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

$sql = "INSERT INTO flashcard_deck_contents (deck_id, flashcard_id)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $data->deck_id, $data->flashcard_id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Flashcard added to deck"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
