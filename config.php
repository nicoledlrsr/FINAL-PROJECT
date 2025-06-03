<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'mrrbdb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    error_log("Database connection successful");
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
}

// Function to check if user exists
function userExists($email) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        error_log("Error checking user existence: " . $e->getMessage());
        return false;
    }
}

// Function to register new user
function registerUser($firstName, $lastName, $age, $email, $password, $role, $gradeLevel = null, $school = null) {
    global $pdo;
    try {
        // First check if user already exists
        if (userExists($email)) {
            error_log("User already exists: " . $email);
            return false;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare the SQL statement with role and additional fields
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, age, email, password, role, grade_level, school) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Execute with parameters
        $result = $stmt->execute([$firstName, $lastName, $age, $email, $hashed_password, $role, $gradeLevel, $school]);
        
        if ($result) {
            error_log("User registered successfully: " . $email);
            return true;
        } else {
            error_log("Failed to register user: " . $email);
            return false;
        }
    } catch(PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

// Function to verify login
function verifyLogin($email, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                // Update last login timestamp
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                error_log("Login successful for: " . $email);
                return $user;
            }
        }
        error_log("Login failed for: " . $email);
        return false;
    } catch(PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

// Function to save quiz score
function saveScore($userId, $quizName, $score, $maxScore) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO scores (user_id, quiz_name, score, max_score) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $quizName, $score, $maxScore]);
    } catch(PDOException $e) {
        error_log("Score save error: " . $e->getMessage());
        return false;
    }
}

// Function to get user scores
function getUserScores($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM scores WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Score retrieval error: " . $e->getMessage());
        return [];
    }
}

// Function to update progress
function updateProgress($userId, $activityType, $activityId, $completed = true) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO progress (user_id, activity_type, activity_id, completed) 
                              VALUES (?, ?, ?, ?) 
                              ON DUPLICATE KEY UPDATE completed = ?");
        return $stmt->execute([$userId, $activityType, $activityId, $completed, $completed]);
    } catch(PDOException $e) {
        error_log("Progress update error: " . $e->getMessage());
        return false;
    }
}

// Function to get user progress
function getUserProgress($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT 
                (SELECT COUNT(*) FROM scores WHERE user_id = ?) as quizzes_completed,
                (SELECT COUNT(*) FROM user_activity WHERE user_id = ? AND activity = 'lesson_completed') as lessons_completed,
                (SELECT COUNT(*) FROM user_activity WHERE user_id = ? AND activity = 'story_read') as stories_read
        ");
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Error getting user progress: " . $e->getMessage());
        return ['quizzes_completed' => 0, 'lessons_completed' => 0, 'stories_read' => 0];
    }
}

// Function to check if user is admin
function isAdmin($email) {
    return $email === 'admin@mrrb.com';
}

// Function to get lessons
function getLessons($category = null) {
    global $pdo;
    try {
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM lessons WHERE category = ? ORDER BY created_at DESC");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM lessons ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting lessons: " . $e->getMessage());
        return [];
    }
}

// Function to get quizzes
function getQuizzes($type = null) {
    global $pdo;
    try {
        if ($type) {
            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_type = ? ORDER BY created_at DESC");
            $stmt->execute([$type]);
        } else {
            $stmt = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting quizzes: " . $e->getMessage());
        return [];
    }
}

// Function to get quiz questions
function getQuizQuestions($quizId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting quiz questions: " . $e->getMessage());
        return [];
    }
}

// Function to get stories
function getStories($category = null) {
    global $pdo;
    try {
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM lessons WHERE category = ? ORDER BY created_at DESC");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM lessons ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting lessons: " . $e->getMessage());
        return [];
    }
}

// Function to save quiz score
function saveQuizScore($userId, $quizId, $score) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO scores (user_id, quiz_id, score) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $quizId, $score]);
    } catch(PDOException $e) {
        error_log("Error saving quiz score: " . $e->getMessage());
        return false;
    }
}

// Function to get user's quiz scores
function getUserQuizScores($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, q.title as quiz_title 
            FROM scores s 
            JOIN quizzes q ON s.quiz_id = q.id 
            WHERE s.user_id = ? 
            ORDER BY s.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error getting user quiz scores: " . $e->getMessage());
        return [];
    }
}

// Function to check if user has completed a quiz
function hasCompletedQuiz($userId, $quizId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM scores WHERE user_id = ? AND quiz_id = ?");
        $stmt->execute([$userId, $quizId]);
        return $stmt->fetchColumn() > 0;
    } catch(PDOException $e) {
        error_log("Error checking quiz completion: " . $e->getMessage());
        return false;
    }
}

// Function to record user activity
function recordUserActivity($userId, $activity, $activityId = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO user_activity (user_id, activity, activity_id) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $activity, $activityId]);
    } catch(PDOException $e) {
        error_log("Error recording user activity: " . $e->getMessage());
        return false;
    }
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Function to require admin
function requireAdmin() {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }
}
?> 