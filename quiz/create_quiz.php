<?php
require "../config/db.php"; 


function generateQuizSet($conn, $userId) {
    
    $stmt = $conn->prepare("SELECT word, definition FROM words WHERE user_id = ? ORDER BY RAND() LIMIT 10");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $quizName = "Quiz " . time();
        $quizsetStmt = $conn->prepare("INSERT INTO quiz_sets (user_id, name) VALUES (?, ?)");
        $quizsetStmt->bind_param("is", $userId, $quizName);

        if (!$quizsetStmt->execute()) {
            echo "Failed to insert quiz set: " . $quizsetStmt->error;
            return;
        }
        $quizSetId = $conn->insert_id;
        echo "Connected Quiz Set Created with ID: $quizSetId<br>";

        
        $questionStmt = $conn->prepare("INSERT INTO quiz_questions (quiz_set_id, word, correct_definition) VALUES (?, ?, ?)");
        $answerStmt = $conn->prepare("INSERT INTO quiz_answers (quiz_question_id, answer_text, is_correct) VALUES (?, ?, ?)");

        while ($row = $result->fetch_assoc()) {
            $word = $row['word'];
            $correctDefinition = $row['definition'];

            
            $questionStmt->bind_param("iss", $quizSetId, $word, $correctDefinition);
            if (!$questionStmt->execute()) {
                echo "Failed to insert question for word $word: " . $questionStmt->error . "<br>";
                continue;
            }
            $quizQuestionId = $conn->insert_id;

            
            $stmtWrong = $conn->prepare("SELECT definition FROM words WHERE word != ? AND user_id = ? ORDER BY RAND() LIMIT 3");
            $stmtWrong->bind_param("si", $word, $userId);
            $stmtWrong->execute();
            $wrongResult = $stmtWrong->get_result();

            $choices = [$correctDefinition];
            while ($wrong = $wrongResult->fetch_assoc()) {
                $choices[] = $wrong['definition'];
            }
            shuffle($choices);

           
            foreach ($choices as $choice) {
                $is_correct = ($choice === $correctDefinition) ? 1 : 0;
                $answerStmt->bind_param("isi", $quizQuestionId, $choice, $is_correct);
                if (!$answerStmt->execute()) {
                    echo "Failed to insert answer '$choice' for question $quizQuestionId: " . $answerStmt->error . "<br>";
                }
            }
        }

        echo "<p>Quiz set created successfully!</p>";
    } else {
        echo "<p>No words found. Please add words first.</p>";
    }
}
?>