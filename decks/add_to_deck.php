<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->deck_id, $data->word_id)) {
    echo json_encode(['error' => 'Missing deck_id or word_id']);
    exit;
}

$deck_id = $data->deck_id;
$word_id = $data->word_id;

// Step 1: Check if deck belongs to user
$stmt = $conn->prepare("SELECT id FROM flashcard_decks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $deck_id, $user_id);
$stmt->execute();
$deck_result = $stmt->get_result();

if ($deck_result->num_rows === 0) {
    echo json_encode(["error" => "Deck not found or unauthorized"]);
    exit;
}

// Step 2: Fetch word data
$stmt = $conn->prepare("SELECT word, definition, example_sentence FROM words WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $word_id, $user_id);
$stmt->execute();
$word_result = $stmt->get_result();

if ($word = $word_result->fetch_assoc()) {
    $front_text = $word['word'];
    $back_text = $word['definition'];
    if (!empty($word['example_sentence'])) {
        $back_text .= "\nExample: " . $word['example_sentence'];
    }
} else {
    echo json_encode(['error' => 'Word not found or unauthorized']);
    exit;
}

// Step 3: Insert flashcard
$insert_flashcard = $conn->prepare("INSERT INTO flashcards (user_id, word_id, front_text, back_text, created_at, updated_at) 
                                    VALUES (?, ?, ?, ?, NOW(), NOW())");
$insert_flashcard->bind_param("iiss", $user_id, $word_id, $front_text, $back_text);

if (!$insert_flashcard->execute()) {
    echo json_encode(["error" => "Failed to create flashcard: " . $insert_flashcard->error]);
    exit;
}

$flashcard_id = $insert_flashcard->insert_id;

// Step 4: Add flashcard to deck
$add_to_deck = $conn->prepare("INSERT INTO flashcard_deck_contents (deck_id, flashcard_id) VALUES (?, ?)");
$add_to_deck->bind_param("ii", $deck_id, $flashcard_id);

if ($add_to_deck->execute()) {
    echo json_encode([
        "message" => "Flashcard created and added to deck",
        "flashcard_id" => $flashcard_id
    ]);
} else {
    echo json_encode(["error" => "Flashcard created, but failed to add to deck: " . $add_to_deck->error]);
}
?>
