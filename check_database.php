<?php
// Database connection
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "mrrbdb";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check if a column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Function to check if a table exists
function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result->num_rows > 0;
}

echo "<h2>Database Structure Check</h2>";

// Check and create tables if they don't exist
$tables = [
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'user',
        status VARCHAR(20) DEFAULT 'active',
        last_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'lessons' => "CREATE TABLE IF NOT EXISTS lessons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        content TEXT NOT NULL,
        category VARCHAR(50),
        difficulty_level VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    'quizzes' => "CREATE TABLE IF NOT EXISTS quizzes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        quiz_type VARCHAR(50) NOT NULL,
        difficulty_level VARCHAR(20),
        time_limit INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    
    'quiz_questions' => "CREATE TABLE IF NOT EXISTS quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quiz_id INT NOT NULL,
        question TEXT NOT NULL,
        correct_answer TEXT NOT NULL,
        options TEXT,
        points INT DEFAULT 1,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
    )",
    
    'scores' => "CREATE TABLE IF NOT EXISTS scores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        quiz_id INT NOT NULL,
        score INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
    )",
    
    'user_activity' => "CREATE TABLE IF NOT EXISTS user_activity (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        activity VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

// Create tables
foreach ($tables as $table => $sql) {
    if (!tableExists($conn, $table)) {
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>Created table: $table</p>";
        } else {
            echo "<p style='color: red;'>Error creating table $table: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>Table exists: $table</p>";
    }
}

// Check and add missing columns to users table
$userColumns = [
    'status' => "ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active'",
    'last_login' => "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL",
    'role' => "ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user'"
];

foreach ($userColumns as $column => $sql) {
    if (!columnExists($conn, 'users', $column)) {
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>Added column $column to users table</p>";
        } else {
            echo "<p style='color: red;'>Error adding column $column: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>Column exists in users table: $column</p>";
    }
}

// Verify scores table structure
if (tableExists($conn, 'scores')) {
    $requiredColumns = ['user_id', 'quiz_id', 'score'];
    foreach ($requiredColumns as $column) {
        if (!columnExists($conn, 'scores', $column)) {
            echo "<p style='color: red;'>Missing required column in scores table: $column</p>";
            // Drop and recreate scores table
            $conn->query("DROP TABLE IF EXISTS scores");
            if ($conn->query($tables['scores'])) {
                echo "<p style='color: green;'>Recreated scores table with correct structure</p>";
            } else {
                echo "<p style='color: red;'>Error recreating scores table: " . $conn->error . "</p>";
            }
            break;
        }
    }
}

$conn->close();
echo "<p>Database check completed!</p>";
?> 