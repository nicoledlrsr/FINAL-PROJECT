<?php
require_once 'config.php';

try {
    // Test database connection
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful!<br>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Number of users in database: " . $count . "<br>";
    
    // Display all users
    $stmt = $pdo->query("SELECT * FROM users");
    echo "<br>All users in database:<br>";
    while ($row = $stmt->fetch()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['first_name'] . " " . $row['last_name'] . ", Email: " . $row['email'] . "<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 