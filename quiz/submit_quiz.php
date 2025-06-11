<?php

require "../config/db.php";

$answers = $_SESSION['quiz_answers'] ?? [];
$questions = $_SESSION['quiz_questions'] ?? [];

$score = 0;

echo "<h2>Your Results:</h2>";

foreach ($questions as $question) {
    $questionId = $question['id'];
    $selectedAnswerId = $answers[$questionId] ?? null;

    
    $stmt = $conn->prepare("SELECT id FROM quiz_answers WHERE quiz_question_id = ? AND is_correct = 1");
    $stmt->bind_param("i", $questionId);
    $stmt->execute();
    $stmt->bind_result($correctId);
    $stmt->fetch();
    $stmt->close();

    $isCorrect = ((int)$selectedAnswerId === (int)$correctId);

    if ($isCorrect) $score++;

    echo "<p><strong>" . htmlspecialchars($question['word']) . "</strong><br>";
    echo "Your answer: " . htmlspecialchars($selectedAnswerId) . " | " . ($isCorrect ? "Correct ✅" : "Wrong ❌") . "</p>";
}

echo "<h3>Final Score: $score / " . count($questions) . "</h3>";


session_destroy();
?>