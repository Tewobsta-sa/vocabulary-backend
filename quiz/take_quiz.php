<?php

require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['quiz_set_id'])) {
    $quizSetId = intval($_POST['quiz_set_id']);

   
    $stmt = $conn->prepare("SELECT id, word FROM quiz_questions WHERE quiz_set_id = ?");
    $stmt->bind_param("i", $quizSetId);
    $stmt->execute();
    $result = $stmt->get_result();

    $questions = [];
    while ($row = $result->fetch_assoc()) {
        
        $answerStmt = $conn->prepare("SELECT id, answer_text FROM quiz_answers WHERE quiz_question_id = ?");
        $answerStmt->bind_param("i", $row['id']);
        $answerStmt->execute();
        $answerResult = $answerStmt->get_result();

        $answers = [];
        while ($answer = $answerResult->fetch_assoc()) {
            $answers[] = $answer;
        }

        $questions[] = [
            'id' => $row['id'],
            'word' => $row['word'],
            'answers' => $answers
        ];
    }

    $_SESSION['quiz_questions'] = $questions;
    $_SESSION['quiz_current'] = 0;
    $_SESSION['quiz_set_id'] = $quizSetId;
    $_SESSION['quiz_answers'] = [];

    header("Location: quiz_question.php");
    exit;
} else {
    echo "<p>No quiz set selected.</p>";
}
?>