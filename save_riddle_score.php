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

if (!isset($data['lesson_id']) || !isset($data['score'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$lesson_id = (int)$data['lesson_id'];
$score = (int)$data['score'];
$user_id = (int)$_SESSION['id']; // Assuming user ID is stored in session as 'id'

// Start transaction
$conn->begin_transaction();

try {
    // Save score to scores table
    // Using lesson_id, setting quiz_id to NULL, type to 'riddle'
    $stmt = $conn->prepare("INSERT INTO scores (user_id, quiz_id, lesson_id, type, score, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $nullQuizId = NULL;
    $type = 'riddle';
    $stmt->bind_param("iiisi", $user_id, $nullQuizId, $lesson_id, $type, $score);
    
    if (!$stmt->execute()) {
        throw new Exception('Error saving riddle score: ' . $stmt->error);
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Riddle score saved successfully',
        'score' => $score
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error saving riddle score: ' . $e->getMessage() . ' SQLSTATE: ' . ($stmt->sqlstate ?? 'N/A') . ' Error: ' . ($stmt->error ?? $conn->error)
    ]);
}

$conn->close();
?> 