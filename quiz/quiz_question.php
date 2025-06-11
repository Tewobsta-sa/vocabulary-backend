<?php

require "../config/db.php";

if (!isset($_SESSION['quiz_questions'])) {
    echo "Quiz not found.";
    exit;
}

$questions = $_SESSION['quiz_questions'];
$currentIndex = $_SESSION['quiz_current'] ?? 0;
$currentQuestion = $questions[$currentIndex];
$feedback = "";
$answered = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['submit_answer'])) {
        if (isset($_POST['answer'])) {
            $questionId = $currentQuestion['id'];
            $selectedAnswerId = $_POST['answer'];
            $_SESSION['quiz_answers'][$questionId] = $selectedAnswerId;

          
            $stmt = $conn->prepare("SELECT id FROM quiz_answers WHERE quiz_question_id = ? AND is_correct = 1");
            $stmt->bind_param("i", $questionId);
            $stmt->execute();
            $stmt->bind_result($correctId);
            $stmt->fetch();
            $stmt->close();

            if ((int)$selectedAnswerId === (int)$correctId) {
                $feedback = "<p style='color:green;'>✅ Correct!</p>";
            } else {
               
                $stmt = $conn->prepare("SELECT answer_text FROM quiz_answers WHERE id = ?");
                $stmt->bind_param("i", $correctId);
                $stmt->execute();
                $stmt->bind_result($correctText);
                $stmt->fetch();
                $stmt->close();

                $feedback = "<p style='color:red;'>❌ Wrong! Correct answer: <strong>" . htmlspecialchars($correctText) . "</strong></p>";
            }

            $answered = true;
        }
    }

    if (isset($_POST['next_question'])) {
        $_SESSION['quiz_current']++;
        $currentIndex = $_SESSION['quiz_current'];

        
        if ($currentIndex >= count($questions)) {
            header("Location: submit_quiz.php");
            exit;
        }

        $currentQuestion = $questions[$currentIndex];
    }
    

}

?>