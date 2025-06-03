<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "mrrbdb";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user's information
$userId = $_SESSION['id'];
$userQuery = $conn->prepare("SELECT * FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();

// Get available lessons
$lessons = $conn->query("SELECT * FROM lessons ORDER BY created_at DESC");

// Get available quizzes
$quizzes = $conn->query("SELECT * FROM quizzes ORDER BY created_at DESC");

// Get user's scores
$scores = $conn->query("SELECT s.*, q.title as quiz_title 
                       FROM scores s 
                       JOIN quizzes q ON s.quiz_id = q.id 
                       WHERE s.user_id = $userId 
                       ORDER BY s.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - MRRB Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .lesson-card, .quiz-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .lesson-card:hover, .quiz-card:hover {
            transform: translateY(-2px);
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        .btn-success {
            background-color: #059669;
            color: white;
        }
        .nav-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .nav-tab {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            background: #f3f4f6;
            color: #4b5563;
            font-weight: 500;
        }
        .nav-tab.active {
            background: #2563eb;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="card">
            <h1 class="text-3xl font-bold mb-4" style="text-align:center;">Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
            <p class="text-gray-600" style="text-align:center;">Explore lessons and quizzes to enhance your learning.</p>
        </div>

        <div class="nav-tabs">
            <div class="nav-tab active" onclick="showTab('lessons')">Lessons</div>
            <div class="nav-tab" onclick="showTab('quizzes')">Quizzes</div>
            <div class="nav-tab" onclick="showTab('progress')">My Progress</div>
        </div>

        <!-- Lessons Tab -->
        <div id="lessons" class="tab-content active">
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Available Lessons</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php while($lesson = $lessons->fetch_assoc()): ?>
                    <div class="lesson-card">
                        <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($lesson['description']); ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Difficulty: <?php echo ucfirst(htmlspecialchars($lesson['difficulty_level'])); ?>
                            </span>
                            <button class="btn btn-primary" onclick="location.href='view_lesson.php?id=<?php echo $lesson['id']; ?>'">
                                Start Lesson
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Quizzes Tab -->
        <div id="quizzes" class="tab-content">
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Available Quizzes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php while($quiz = $quizzes->fetch_assoc()): ?>
                    <div class="quiz-card">
                        <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($quiz['description']); ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Type: <?php echo ucfirst(htmlspecialchars($quiz['quiz_type'])); ?>
                            </span>
                            <button class="btn btn-success" onclick="location.href='take_quiz.php?id=<?php echo $quiz['id']; ?>'">
                                Take Quiz
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Progress Tab -->
        <div id="progress" class="tab-content">
            <div class="card">
                <h2 class="text-xl font-bold mb-4">My Progress</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while($score = $scores->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($score['quiz_title']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($score['score']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($score['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Add active class to selected tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html> 