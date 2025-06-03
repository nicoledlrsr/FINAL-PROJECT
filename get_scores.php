<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please log in to view scores']);
    exit;
}

$user_id = (int)$_SESSION['id']; // Assuming user ID is stored in session as 'id'

$scores = [];

// Fetch quiz scores
$quizScoresQuery = $conn->prepare("
    SELECT s.score, q.title as item_title, s.created_at, 'quiz' as type
    FROM scores s
    JOIN quizzes q ON s.quiz_id = q.id
    WHERE s.user_id = ? AND s.type = 'quiz'
    ORDER BY s.created_at DESC
");
$quizScoresQuery->bind_param("i", $user_id);
$quizScoresQuery->execute();
$quizScoresResult = $quizScoresQuery->get_result();
while ($row = $quizScoresResult->fetch_assoc()) {
    $scores[] = $row;
}
$quizScoresQuery->close();

// Fetch riddle scores
$riddleScoresQuery = $conn->prepare("
    SELECT s.score, l.title as item_title, s.created_at, 'riddle' as type
    FROM scores s
    JOIN lessons l ON s.lesson_id = l.id
    WHERE s.user_id = ? AND s.type = 'riddle'
    ORDER BY s.created_at DESC
");
$riddleScoresQuery->bind_param("i", $user_id);
$riddleScoresQuery->execute();
$riddleScoresResult = $riddleScoresQuery->get_result();
while ($row = $riddleScoresResult->fetch_assoc()) {
    $scores[] = $row;
}
$riddleScoresQuery->close();

// Sort all scores by created_at in descending order
usort($scores, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo json_encode([
    'success' => true,
    'scores' => $scores
]);

$conn->close();
?> 