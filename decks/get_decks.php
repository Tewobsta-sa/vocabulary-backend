<?php
session_start();
require '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$decks = [];

$deck_result = $conn->prepare("SELECT id, deck_name, created_at, updated_at FROM flashcard_decks WHERE user_id = ?");
$deck_result->bind_param("i", $user_id);
$deck_result->execute();
$deck_result = $deck_result->get_result();

while ($deck = $deck_result->fetch_assoc()) {
    $deck_id = $deck['id'];

    // Get flashcards in this deck
    $flashcard_stmt = $conn->prepare("
        SELECT f.id, f.word_id, f.front_text, f.back_text, f.created_at, f.updated_at
        FROM flashcards f
        JOIN flashcard_deck_contents dc ON f.id = dc.flashcard_id
        WHERE dc.deck_id = ?
    ");
    $flashcard_stmt->bind_param("i", $deck_id);
    $flashcard_stmt->execute();
    $flashcards_result = $flashcard_stmt->get_result();

    $flashcards = [];
    while ($card = $flashcards_result->fetch_assoc()) {
        $flashcards[] = $card;
    }

    $deck['flashcards'] = $flashcards;
    $decks[] = $deck;
}

echo json_encode($decks);
