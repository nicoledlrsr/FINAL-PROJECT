<?php
session_start();
require_once 'config.php';
requireLogin();
require_once 'db_connect.php';

// Remove riddle fetching as this page will now be a scoreboard
// $riddles = getLessons('riddles');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard - MRRB Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: Arial, sans-serif !important;
            background: linear-gradient(to right, #d4a9e6, #8a4af3);
            min-height: 100vh;
        }
        /* Remove riddle-specific styles */
        /*
        .riddle-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .riddle-card:hover {
            transform: translateY(-5px);
        }
        */
        .main-content {
            margin-left: 80px;
            transition: margin-left 0.3s ease;
        }
        .sidebar:hover ~ .main-content {
            margin-left: 250px;
        }
        /* Remove riddle-specific styles */
        /*
        .answer {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .answer.show {
            display: block;
            opacity: 1;
        }
        */
        .logo {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            white-space: nowrap;
            font-family: Arial, sans-serif;
            gap: 2px;
            width: 100%;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .sidebar:hover .logo {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main class="main-content p-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-bold text-center mb-8 text-white">Scoreboard</h1>
            
            <!-- Scoreboard Section -->
            <section class="scoreboard-section">
                <div class="max-w-6xl mx-auto">
                    <div id="scoresContainer" class="bg-white rounded-lg shadow-md p-6">
                        <!-- Scores will be loaded here by JavaScript -->
                        <p class="text-center text-gray-600">Loading scores...</p>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <script>
        // Remove riddle-specific JavaScript functions
        /*
        function toggleAnswer(button) {
            const answer = button.previousElementSibling;
            const isShowing = answer.classList.contains('show');
            
            if (isShowing) {
                answer.classList.remove('show');
                button.textContent = 'Show Answer';
            } else {
                answer.classList.add('show');
                button.textContent = 'Hide Answer';
            }
        }

        function checkRiddleAnswer(btn, correctAnswer) { ... }
        */

        // Function to fetch and display scores (using the get_scores.php script)
        function displayScores() {
            const scoresContainer = document.getElementById('scoresContainer');
            scoresContainer.innerHTML = '<p class="text-center text-gray-600">Loading scores...</p>'; // Show loading message

            fetch('get_scores.php?time_in_seconds=1') // Fetching all user's scores (quizzes and riddles) with time in seconds
                .then(response => response.json())
                .then(data => {
                    scoresContainer.innerHTML = ''; // Clear loading message
                    
                    if (data.success && data.scores.length > 0) {
                        // Create a simple list for individual scores on this page
                        const scoreList = document.createElement('ul');
                        scoreList.classList.add('score-list');

                        data.scores.forEach(score => {
                            const scoreItem = document.createElement('li');
                            scoreItem.classList.add('score-item');
                            scoreItem.innerHTML = `
                                <strong>${score.item_title} (${score.type}):</strong> ${score.score} points
                                <span style="font-size: 0.8em; color: #666; margin-left: 10px;">${new Date(score.created_at).toLocaleString()}</span>
                            `;
                            scoreList.appendChild(scoreItem);
                        });
                        scoresContainer.appendChild(scoreList);

                    } else {
                        scoresContainer.innerHTML = '<p class="text-center text-gray-600">No scores recorded yet.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching scores:', error);
                    scoresContainer.innerHTML = '<p class="text-center text-red-600">Error loading scores.</p>';
                });
        }

        // Call displayScores when the page loads
        displayScores();
    </script>
</body>
</html>