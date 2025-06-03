<?php
session_start();
require_once 'db_connect.php';

// Database connection
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "mrrbdb";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$rankedScores = [];

// Fetch all scores, joining with users and either quizzes or lessons
$sql = "
    SELECT 
        s.score,
        u.first_name, -- Assuming user name is in first_name column
        COALESCE(q.title, l.title) as item_title,
        s.type,
        s.created_at
    FROM scores s
    JOIN users u ON s.user_id = u.id
    LEFT JOIN quizzes q ON s.quiz_id = q.id
    LEFT JOIN lessons l ON s.lesson_id = l.id
    ORDER BY s.score DESC, s.created_at ASC
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rankedScores[] = $row;
    }
    echo json_encode([
        'success' => true,
        'scores' => $rankedScores
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching ranked scores: ' . $conn->error
    ]);
}

$conn->close();
?> 