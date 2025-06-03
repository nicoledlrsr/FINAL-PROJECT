<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if lesson ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid lesson ID']);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "mrrbdb";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$lessonId = (int)$_GET['id'];

// First, check if the lesson exists
$checkStmt = $conn->prepare("SELECT id FROM lessons WHERE id = ?");
$checkStmt->bind_param("i", $lessonId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lesson not found']);
    exit;
}
$checkStmt->close();

// Delete the lesson
$deleteStmt = $conn->prepare("DELETE FROM lessons WHERE id = ?");
$deleteStmt->bind_param("i", $lessonId);

if ($deleteStmt->execute()) {
    // Redirect back to admin dashboard with success message
    header('Location: admin.php?tab=lessons&message=Lesson deleted successfully');
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error deleting lesson: ' . $conn->error]);
    exit;
}

$deleteStmt->close();
$conn->close();
?> 