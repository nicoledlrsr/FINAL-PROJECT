<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please log in to save scores']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['quiz_id']) || !isset($data['score']) || !isset($data['answers'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$quiz_id = (int)$data['quiz_id'];
$score = (int)$data['score'];
$user_id = (int)$_SESSION['id']; // Using 'id' from session
$answers = json_encode($data['answers']);

// Start transaction
$conn->begin_transaction();

try {
    // Save score to scores table
    $stmt = $conn->prepare("INSERT INTO scores (user_id, quiz_id, lesson_id, type, score, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $nullLessonId = NULL;
    $type = 'quiz';
    $stmt->bind_param("iiisi", $user_id, $quiz_id, $nullLessonId, $type, $score);
    $stmt->execute();

    // Update user's total score if the column exists
    $stmt = $conn->prepare("UPDATE users SET total_score = COALESCE(total_score, 0) + ? WHERE id = ?");
    $stmt->bind_param("ii", $score, $user_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Score saved successfully',
        'score' => $score
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error saving quiz score: ' . $e->getMessage() . ' SQLSTATE: ' . ($stmt->sqlstate ?? 'N/A') . ' Error: ' . ($stmt->error ?? $conn->error)
    ]);
}

$conn->close();
?> 