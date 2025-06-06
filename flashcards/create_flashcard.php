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

if (!isset($data->word_id)) {
    echo json_encode(['error' => 'Missing word_id']);
    exit;
}

// Fetch the word details to get front and back text
$stmt = $conn->prepare("SELECT word, definition, example_sentence FROM words WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $data->word_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($word = $result->fetch_assoc()) {
    $front_text = $word['word'];
    $back_text = $word['definition'];
    if (!empty($word['example_sentence'])) {
        $back_text .= "\nExample: " . $word['example_sentence'];
    }
} else {
    echo json_encode(['error' => 'Word not found or unauthorized']);
    exit;
}

// Insert the flashcard
$sql = "INSERT INTO flashcards (user_id, word_id, front_text, back_text, created_at, updated_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $data->word_id, $front_text, $back_text);

if ($stmt->execute()) {
    echo json_encode(["message" => "Flashcard created successfully"]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
