<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's scores
$scores = getUserScores($_SESSION['user_id']);

// Get user's progress
$progress = getUserProgress($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MRRB Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'MedievalSharp', cursive;
            background: linear-gradient(to right, #d4a9e6, #8a4af3);
            min-height: 100vh;
        }
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: width 0.3s ease;
            z-index: 50;
            overflow-x: hidden;
        }
        .sidebar:hover {
            width: 250px;
        }
        .nav-links {
            display: flex;
            flex-direction: column;
            padding-top: 100px;
        }
        .nav-links a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #4a3a6b;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-family: Arial, sans-serif;
        }
        .nav-links a span {
            opacity: 0;
            transition: opacity 0.3s ease;
            margin-left: 15px;
        }
        .sidebar:hover .nav-links a span {
            opacity: 1;
        }
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #2d1f4a;
        }
        .nav-links a i {
            min-width: 30px;
            font-size: 20px;
            text-align: center;
        }
        .nav-links a.logout {
            margin-top: auto;
            color: #dc2626;
        }
        .nav-links a.logout:hover {
            background: rgba(220, 38, 38, 0.1);
        }
        /* Rest of your existing styles */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            margin-left: 80px;
            transition: margin-left 0.3s ease;
        }
        .sidebar:hover ~ .dashboard-container {
            margin-left: 250px;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .welcome-text {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stat-title {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .activity-list {
            list-style: none;
            padding: 0;
        }
        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <span class="text-green-500 font-bold text-lg">M</span>
            <span class="text-blue-400 font-bold text-lg">R</span>
            <span class="text-gray-700 font-bold text-lg">R</span>
            <span class="text-gray-600 font-bold text-lg">B</span>
            <span class="text-3xl font-extrabold text-[#1a1a1a] ml-2 tracking-widest">LIBRARY</span>
        </div>
        <div class="nav-links">
            <a href="storybook.php"><i class="fas fa-book"></i><span>Storybook</span></a>
            <a href="alphabet.php"><i class="fas fa-font"></i><span>Alphabet</span></a>
            <a href="numbers.php"><i class="fas fa-calculator"></i><span>Numbers</span></a>
            <a href="riddles.php"><i class="fas fa-puzzle-piece"></i><span>Riddle</span></a>
            <a href="quizzes.php"><i class="fas fa-question-circle"></i><span>Quizzes</span></a>
            <a href="music.php"><i class="fas fa-music"></i><span>Music</span></a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <div class="dashboard-container">
        <div class="card">
            <div class="welcome-text">
                Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>!
            </div>
            <div class="text-gray-600">
                Role: <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                <?php if ($user['grade_level']): ?>
                    | Grade: <?php echo htmlspecialchars($user['grade_level']); ?>
                <?php endif; ?>
                <?php if ($user['school']): ?>
                    | School: <?php echo htmlspecialchars($user['school']); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Quizzes Completed</div>
                <div class="stat-value"><?php echo count($scores); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Average Score</div>
                <div class="stat-value">
                    <?php
                    if (count($scores) > 0) {
                        $total = 0;
                        foreach ($scores as $score) {
                            $total += ($score['score'] / $score['max_score']) * 100;
                        }
                        echo round($total / count($scores), 1) . '%';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Activities Completed</div>
                <div class="stat-value"><?php echo count($progress); ?></div>
            </div>
        </div>

        <?php if ($user['role'] === 'student'): ?>
            <!-- Student Dashboard -->
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Recent Quiz Scores</h2>
                <div class="activity-list">
                    <?php foreach (array_slice($scores, 0, 5) as $score): ?>
                        <div class="activity-item">
                            <div>
                                <div class="font-medium"><?php echo htmlspecialchars($score['quiz_name']); ?></div>
                                <div class="text-sm text-gray-600">
                                    Score: <?php echo $score['score']; ?>/<?php echo $score['max_score']; ?>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($score['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif ($user['role'] === 'teacher'): ?>
            <!-- Teacher Dashboard -->
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Class Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h3 class="font-bold mb-2">Grade Level: <?php echo htmlspecialchars($user['grade_level']); ?></h3>
                        <p class="text-gray-600">School: <?php echo htmlspecialchars($user['school']); ?></p>
                    </div>
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h3 class="font-bold mb-2">Quick Actions</h3>
                        <div class="space-y-2">
                            <button class="btn btn-primary w-full">Create New Quiz</button>
                            <button class="btn btn-primary w-full">View Student Progress</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($user['role'] === 'parent'): ?>
            <!-- Parent Dashboard -->
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Child's Progress</h2>
                <div class="p-4 bg-white rounded-lg shadow">
                    <p class="text-gray-600">Connect with your child's account to view their progress.</p>
                    <button class="btn btn-primary mt-4">Connect Child's Account</button>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 class="text-xl font-bold mb-4">Quick Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="music.php" class="btn btn-primary text-center">Music Player</a>
                <a href="quiz.php" class="btn btn-primary text-center">Take a Quiz</a>
                <a href="logout.php" class="btn btn-primary text-center">Logout</a>
            </div>
        </div>
    </div>
</body>
</html> 