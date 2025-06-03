<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "mrrbdb";

// Create connection
$conn = new mysqli($servername, $username, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?> 