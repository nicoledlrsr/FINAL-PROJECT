<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect back to admin.php with error message if ID is missing
    header('Location: admin.php?tab=quizzes&error=Invalid quiz ID');
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "mrrbdb";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    // Redirect back to admin.php with error message on database connection failure
    header('Location: admin.php?tab=quizzes&error=Database connection failed');
    exit;
}

$quizId = (int)$_GET['id'];

// Start transaction
$conn->begin_transaction();

try {
    // First, check if the quiz exists
    $checkStmt = $conn->prepare("SELECT id FROM quizzes WHERE id = ?");
    $checkStmt->bind_param("i", $quizId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Quiz not found');
    }
    $checkStmt->close();

    // Delete associated quiz questions
    $deleteQuestionsStmt = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
    $deleteQuestionsStmt->bind_param("i", $quizId);
    if (!$deleteQuestionsStmt->execute()) {
        throw new Exception('Error deleting quiz questions: ' . $conn->error);
    }
    $deleteQuestionsStmt->close();

    // Delete the quiz
    $deleteQuizStmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $deleteQuizStmt->bind_param("i", $quizId);

    if (!$deleteQuizStmt->execute()) {
        throw new Exception('Error deleting quiz: ' . $conn->error);
    }
    $deleteQuizStmt->close();

    // Commit transaction
    $conn->commit();

    // Redirect back to admin dashboard with success message
    header('Location: admin.php?tab=quizzes&message=Quiz deleted successfully');
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    // Redirect back to admin.php with error message
    header('Location: admin.php?tab=quizzes&error=Error deleting quiz: ' . urlencode($e->getMessage()));
    exit;
}

$conn->close();
?> 