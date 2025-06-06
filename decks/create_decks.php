<?php
session_start();
require '../config/db.php';

$data = json_decode(file_get_contents("php://input"));

$sql = "INSERT INTO flashcard_decks (user_id, deck_name, created_at, updated_at)
        VALUES (?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $data->user_id, $data->deck_name);

if ($stmt->execute()) {
    echo json_encode(["message" => "Deck created"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
