<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$deckname = $data['deckname'];

$sql = "INSERT INTO flashcard_decks (user_id, deck_name)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $deckname);

if ($stmt->execute()) {
    echo json_encode(["message" => "Deck created", "deck_id" => $stmt->insert_id]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
