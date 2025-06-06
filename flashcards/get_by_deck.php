<?php
session_start();
require '../config/db.php';

$user_id = $_SESSION['user_id'];
$deck_id = $_GET['deck_id'] ?? null;

if (!$deck_id) {
    echo json_encode(["error" => "Deck ID required"]);
    exit;
}

// Verify deck belongs to user
$check = $conn->prepare("SELECT * FROM flashcard_decks WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $deck_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// Fetch flashcards
$query = "
    SELECT f.*
    FROM flashcards f
    JOIN flashcard_deck_contents dc ON f.id = dc.flashcard_id
    WHERE dc.deck_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $deck_id);
$stmt->execute();
$res = $stmt->get_result();

$cards = [];
while ($row = $res->fetch_assoc()) {
    $cards[] = $row;
}
echo json_encode($cards);
?>
