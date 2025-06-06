<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));
$deck_id = $data->deck_id;

// Check ownership
$stmt = $conn->prepare("SELECT id FROM flashcard_decks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $deck_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Delete flashcards from deck
$conn->query("DELETE FROM flashcard_deck_contents WHERE deck_id = $deck_id");

// Delete the deck
$delete = $conn->prepare("DELETE FROM flashcard_decks WHERE id = ?");
$delete->bind_param("i", $deck_id);

if ($delete->execute()) {
    echo json_encode(["message" => "Deck deleted"]);
} else {
    echo json_encode(["error" => $delete->error]);
}
?>
