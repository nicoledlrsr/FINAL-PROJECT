<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please log in to take quizzes']);
    exit;
}

// Check if quiz_id is provided
if (!isset($_GET['quiz_id']) || !is_numeric($_GET['quiz_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid quiz ID']);
    exit;
}

$quiz_id = (int)$_GET['quiz_id'];

try {
    // First verify that the quiz exists
    $stmt = $conn->prepare("SELECT id FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Quiz not found']);
        exit;
    }

    // Fetch quiz questions
    $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No questions found for this quiz']);
        exit;
    }

    $questions = $result->fetch_all(MYSQLI_ASSOC);

    // Verify that each question has valid options
    foreach ($questions as &$question) {
        if (!empty($question['options'])) {
            $options = json_decode($question['options'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid options format for question ID: ' . $question['id']);
            }
            $question['options'] = json_encode($options); // Re-encode to ensure valid JSON
        }
    }

    // Return questions
    echo json_encode([
        'success' => true,
        'questions' => $questions
    ]);
} catch (Exception $e) {
    error_log("Error in get_quiz_questions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading quiz questions: ' . $e->getMessage()
    ]);
} 