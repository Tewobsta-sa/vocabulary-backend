<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"));
$deck_id = $data->deck_id;
$flashcard_id = $data->flashcard_id;

// Verify deck belongs to user
$stmt = $conn->prepare("SELECT * FROM flashcard_decks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $deck_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$delete = $conn->prepare("DELETE FROM flashcard_deck_contents WHERE deck_id = ? AND flashcard_id = ?");
$delete->bind_param("ii", $deck_id, $flashcard_id);

if ($delete->execute()) {
    echo json_encode(["message" => "Flashcard removed from deck"]);
} else {
    echo json_encode(["error" => $delete->error]);
}
?>
