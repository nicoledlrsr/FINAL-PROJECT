<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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

// Get all users with error handling
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
if (!$users) {
    $users = new stdClass();
    $users->num_rows = 0;
    error_log("Error in users query: " . $conn->error);
}

// Get recent logins with error handling
$recentLogins = $conn->query("SELECT * FROM users WHERE last_login IS NOT NULL ORDER BY last_login DESC LIMIT 5");
if (!$recentLogins) {
    $recentLogins = new stdClass();
    $recentLogins->num_rows = 0;
    error_log("Error in recent logins query: " . $conn->error);
}

// Get top scores with error handling
$topScores = $conn->query("SELECT u.first_name, u.last_name, s.score, s.created_at 
                          FROM scores s 
                          JOIN users u ON s.user_id = u.id 
                          ORDER BY s.score DESC 
                          LIMIT 10");

if (!$topScores) {
    // If there's an error with the query, initialize an empty result
    $topScores = new stdClass();
    $topScores->num_rows = 0;
    error_log("Error in scores query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MRRB Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
        }
        .admin-container {
            max-width: 200%;
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
        .stats-grid {
        display: flex;
        flex-direction: row;
        gap: 1.5rem;
        margin-bottom: 2rem;
        justify-content: center;
        flex-wrap: nowrap; /* Ensure cards stay in a single row */
    }

    .stat-card {
        background: white;
        padding: 1.2rem;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(77, 12, 12, 0.1);
        width: 45%; /* Keep the original width */
        max-width: 70%; /* Keep the original max-width */
        align-items: center;
    }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .user-table th, .user-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .user-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
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
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        .btn-success {
            background-color: #059669;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .nav-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
            justify-content: center;
        }
        .nav-tab {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            background: #f3f4f6;
            color: #4b5563;
            font-weight: 500;
            align-items: center;
        }
        .nav-tab.active {
            background: #2563eb;
            color: white;
        }
        h1, h2, h3, h4 {
            color: #111827;
            font-weight: 600;
        }
        .text-gray-600 {
            color: #4b5563;
        }
        .text-gray-500 {
            color: #6b7280;
        }
        .user-table td .btn {
            margin-right: 0.5rem;
        }
        .user-table td .btn:last-child {
            margin-right: 0;
        }
        #lessons .card,
     #scores .card,
    #history .card {
        width: 1000px;
        max-width: 1050px;
    }

    /* Target the inner cards for Lessons and Quizzes to take the full width of the parent */
    #lessons .card .card {
        width: 100%; /* Take the full width of the parent container */
        max-width: none; /* Remove any max-width restrictions */
    }

    /* Ensure the content inside the inner cards also stretches to the full width */
    #lessons .card .card .space-y-4 {
        width: 100%;
    }

    #lessons .card .card .space-y-4 > div {
        width: 100%;
    }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="card">
            <h1 class="text-3xl font-bold mb-4" style="text-align:center;">Admin Dashboard</h1>
            <p class="text-gray-600" style="text-align:center;">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="text-gray-500">Total Users</div>
                <div class="text-2xl font-bold"><?php echo $users->num_rows; ?></div>
            </div>
            <div class="stat-card">
                <div class="text-gray-500">Active Users</div>
                <div class="text-2xl font-bold">
                    <?php
                    $activeUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
                    echo $activeUsers->fetch_assoc()['count'];
                    ?>
                </div>
            </div>
            <div class="stat-card">
                <div class="text-gray-500">New Users Today</div>
                <div class="text-2xl font-bold">
                    <?php
                    $newUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()");
                    echo $newUsers->fetch_assoc()['count'];
                    ?>
                </div>
            </div>
        </div>

        <div class="nav-tabs">
            <div class="nav-tab active" onclick="showTab('users')">User Management</div>
            <div class="nav-tab" onclick="showTab('lessons')">Lessons & Quizzes</div>
            <div class="nav-tab" onclick="showTab('scores')">Scoreboard</div>
            <div class="nav-tab" onclick="showTab('history')">User History</div>
        </div>

        <!-- User Management Tab -->
        <div id="users" class="tab-content active">
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">User Management</h2>
                    <button class="btn btn-primary" onclick="location.href='add_user.php'">
                        <i class="fas fa-plus"></i> Add New User
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                                <td>
                                    <span class="px-2 py-1 rounded-full text-sm <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?></td>
                                <td>
                                    <button class="btn btn-primary" onclick="location.href='edit_user.php?id=<?php echo $user['id']; ?>'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="if(confirm('Are you sure you want to delete this user?')) location.href='delete_user.php?id=<?php echo $user['id']; ?>'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       <!-- Lessons & Quizzes Tab -->
<div id="lessons" class="tab-content">
    <div class="card">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Lessons & Quizzes</h2>
            <div class="space-x-2">
                <button class="btn btn-primary" onclick="location.href='add_lesson.php'">
                    <i class="fas fa-plus"></i> Add Lesson
                </button>
                <button class="btn btn-success" onclick="location.href='add_quiz.php'">
                    <i class="fas fa-plus"></i> Add Quiz
                </button>
            </div>
        </div>

        <!-- Lessons Section -->
        <div class="card">
            <h3 class="text-lg font-bold mb-4">Lessons</h3>
            <div class="space-y-4">
                <?php
                $lessons = $conn->query("SELECT * FROM lessons ORDER BY created_at DESC");
                while($lesson = $lessons->fetch_assoc()):
                ?>
                <div class="flex justify-between items-center p-4 bg-white rounded-lg shadow">
                    <div>
                        <h4 class="font-bold"><?php echo htmlspecialchars($lesson['title']); ?></h4>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($lesson['description']); ?></p>
                    </div>
                    <div class="space-x-2">
                        <button class="btn btn-primary" onclick="location.href='edit_lesson.php?id=<?php echo $lesson['id']; ?>'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="if(confirm('Delete this lesson?')) location.href='delete_lesson.php?id=<?php echo $lesson['id']; ?>'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Quizzes Section -->
        <div class="card">
            <h3 class="text-lg font-bold mb-4">Quizzes</h3>
            <div class="space-y-4">
                <?php
                $quizzes = $conn->query("SELECT * FROM quizzes ORDER BY created_at DESC");
                while($quiz = $quizzes->fetch_assoc()):
                ?>
                <div class="flex justify-between items-center p-4 bg-white rounded-lg shadow">
                    <div>
                        <h4 class="font-bold"><?php echo htmlspecialchars($quiz['title']); ?></h4>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($quiz['description']); ?></p>
                    </div>
                    <div class="space-x-2">
                        <button class="btn btn-primary" onclick="location.href='edit_quiz.php?id=<?php echo $quiz['id']; ?>'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="if(confirm('Delete this quiz?')) location.href='delete_quiz.php?id=<?php echo $quiz['id']; ?>'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

        <!-- Scoreboard Tab -->
        <div id="scores" class="tab-content">
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Scoreboard</h2>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Name</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Score</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="scoreboardTableBody">
                        <!-- Scores will be loaded here by JavaScript -->
                        <tr>
                            <td colspan="6" class="text-center text-gray-600">Loading scores...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User History Tab -->
        <div id="history" class="tab-content">
            <div class="card">
                <h2 class="text-xl font-bold mb-4">Recent User Activity</h2>
                <div class="overflow-x-auto">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Last Login</th>
                                <th>Status</th>
                                <th>Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($login = $recentLogins->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($login['first_name'] . ' ' . $login['last_name']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($login['last_login'])); ?></td>
                                <td>
                                    <span class="px-2 py-1 rounded-full text-sm <?php echo $login['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ucfirst(htmlspecialchars($login['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $activity = $conn->query("SELECT * FROM user_activity WHERE user_id = {$login['id']} ORDER BY created_at DESC LIMIT 1");
                                    if($activity->num_rows > 0) {
                                        $act = $activity->fetch_assoc();
                                        echo htmlspecialchars($act['activity']);
                                    } else {
                                        echo "No recent activity";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to show the selected tab
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tabContent => {
                tabContent.classList.remove('active');
            });

            // Deactivate all nav tabs
            document.querySelectorAll('.nav-tab').forEach(navTab => {
                navTab.classList.remove('active');
            });

            // Show the selected tab content
            document.getElementById(tabId).classList.add('active');

            // Activate the corresponding nav tab
            document.querySelector(`.nav-tab[onclick="showTab('${tabId}')"]`).classList.add('active');

            // If the scoreboard tab is selected, fetch and display scores
            if (tabId === 'scores') {
                fetchAndDisplayRankedScores();
            }
        }

        // Function to fetch and display ranked scores
        function fetchAndDisplayRankedScores() {
            const scoreboardTableBody = document.getElementById('scoreboardTableBody');
            scoreboardTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-600">Loading scores...</td></tr>'; // Show loading message

            fetch('get_ranked_scores.php')
                .then(response => response.json())
                .then(data => {
                    scoreboardTableBody.innerHTML = ''; // Clear loading message

                    if (data.success && data.scores.length > 0) {
                        data.scores.forEach((score, index) => {
                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${score.first_name}</td>
                                    <td>${score.item_title}</td>
                                    <td>${score.type}</td>
                                    <td>${score.score}</td>
                                    <td>${new Date(score.created_at).toLocaleString()}</td>
                                </tr>
                            `;
                            scoreboardTableBody.innerHTML += row;
                        });
                    } else {
                        scoreboardTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-600">No scores found.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching ranked scores:', error);
                    scoreboardTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-red-600">Error loading scores.</td></tr>';
                });
        }

        // Show the default tab on page load
        // Check if a tab is specified in the URL hash
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'users'; // Default to 'users' tab
        showTab(activeTab);

        // Add event listener for tab clicks (already handled by onclick in HTML)
        // You can remove these if the onclick attributes are sufficient
        /*
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                showTab(this.dataset.tab);
            });
        });
        */
    </script>
</body>
</html> 