<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));
$deck_id = $data->deck_id;
$flashcard_id = $data->flashcard_id;

// Check if deck belongs to user
$stmt = $conn->prepare("SELECT * FROM flashcard_decks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $deck_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Deck not found or unauthorized"]);
    exit;
}

// Insert flashcard into deck
$insert = $conn->prepare("INSERT INTO flashcard_deck_contents (deck_id, flashcard_id) VALUES (?, ?)");
$insert->bind_param("ii", $deck_id, $flashcard_id);

if ($insert->execute()) {
    echo json_encode(["message" => "Flashcard added to deck"]);
} else {
    echo json_encode(["error" => $insert->error]);
}
?>
