<?php
session_start();
require_once 'db_connect.php';

// Fetch English quizzes (alphabet, numbers, riddles, general)
$englishQuizzes = $conn->query("SELECT * FROM quizzes WHERE quiz_type IN ('alphabet', 'numbers', 'riddles', 'general') ORDER BY created_at DESC");

// Fetch Tagalog quizzes (tagalog_alphabet, tagalog_numbers, tagalog_riddles)
$tagalogQuizzes = $conn->query("SELECT * FROM quizzes WHERE quiz_type IN ('tagalog_alphabet', 'tagalog_numbers', 'tagalog_riddles') ORDER BY created_at DESC");

// Fetch questions for each quiz
function getQuizQuestions($conn, $quizId) {
    $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes Page</title>
    <!-- Google Fonts & Font Awesome for icons -->
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&family=Luckiest+Guy&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'MedievalSharp', cursive;
            background: linear-gradient(135deg, #a084ca, #d7b9d5);
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        /* Custom scrollbar for sidebar */
        aside::-webkit-scrollbar {
            width: 6px;
        }
        aside::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        .flex {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
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
        .sidebar:hover ~ main {
            margin-left: 250px;
        }
        /* Logo styles */
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
            font-size: 2rem;
        }
        .sidebar:hover .logo {
            opacity: 1;
            pointer-events: auto;
        }
        .logo span {
            font-family: Arial, sans-serif !important;
            font-size: 2rem;
            font-weight: bold;
        }
        .logo .m { color: #22c55e; } /* green-500 */
        .logo .r1 { color: #60a5fa; } /* blue-400 */
        .logo .r2 { color: #374151; } /* gray-700 */
        .logo .b { color: #4b5563; } /* gray-600 */
        .logo .library {
            margin-left: 12px;
            letter-spacing: 0.2em;
            font-size: 1rem;
            font-weight: 800;
            color: #1a1a1a;
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
        .sidebar-footer {
            padding: 0.8rem;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer-image {
            width: 70%;
            height: auto;
            margin-bottom: 8px;
        }
        /* Main Content */
        main {
            margin-left: 80px;
            width: calc(100vw - 80px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 15px;
            transition: margin-left 0.3s ease;
            overflow-y: auto;
        }
        .tab-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
            gap: 15px;
            flex-wrap: wrap;
        }
        .tab-buttons button {
            padding: 8px 20px;
            border-radius: 25px;
            border: none;
            background: #b3b6e0;
            color: #2d2d7b;
            font-size: 1rem;
            font-family: Arial, sans-serif;
            font-weight: bold;
            cursor: pointer;
            touch-action: manipulation;
            transition: all 0.2s ease;
        }
        .tab-buttons button:hover {
            background: #9b8fc2;
            color: #fff;
        }
        .quizzes-group {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
            padding: 10px 0;
        }
        .quiz-card {
            background: rgba(245, 223, 255, 0.75);
            border-radius: 30px;
            box-shadow: 0 3px 10px rgba(80, 60, 120, 0.15);
            width: 90%;
            max-width: 500px;
            padding: 20px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: Arial, sans-serif;
            backdrop-filter: blur(8px);
        }
        .quiz-card h2 {
            margin: 0 0 15px 0;
            font-family: Arial, sans-serif;
            color: #2d2d7b;
            font-size: 1.5rem;
            text-align: center;
            width: 100%;
        }
        .quiz {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        .quiz p {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            font-family: Arial, sans-serif;
            text-align: center;
            color: #2d2d7b;
            line-height: 1.4;
        }
        .quiz button {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: #b3b6e0;
            color: #2d2d7b;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: Arial, sans-serif;
            font-weight: 600;
            touch-action: manipulation;
        }
        .quiz button:hover {
            background: #9b8fc2;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(155, 143, 194, 0.3);
        }
        /* Mobile-first adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 50px;
                height: 100%;
            }
            .sidebar:hover {
                width: 180px;
            }
            .sidebar:hover ~ main {
                margin-left: 180px;
            }
            main {
                margin-left: 50px;
                width: calc(100vw - 50px);
                padding: 10px;
            }
            .quiz-card {
                width: 95%;
                padding: 15px 20px;
            }
            .quiz-card h2 {
                font-size: 1.3rem;
            }
            .quiz p {
                font-size: 1rem;
            }
            .quiz button {
                font-size: 0.9rem;
                padding: 8px 18px;
            }
            .tab-buttons button {
                font-size: 0.9rem;
                padding: 6px 15px;
            }
        }
        @media (max-width: 480px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar:hover {
                width: 160px;
            }
            .sidebar:hover ~ main {
                margin-left: 0;
            }
            main {
                margin-left: 0;
                width: 100vw;
                padding: 5px;
            }
            .quiz-card {
                width: 98%;
                padding: 10px 15px;
            }
            .quiz-card h2 {
                font-size: 1.2rem;
            }
            .quiz p {
                font-size: 0.9rem;
            }
            .quiz button {
                font-size: 0.85rem;
                padding: 7px 15px;
            }
            .tab-buttons {
                gap: 10px;
            }
            .tab-buttons button {
                font-size: 0.85rem;
                padding: 5px 12px;
            }
        }
        /* Add new styles for quiz interface */
        .quiz-interface {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .quiz-modal {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.15);
            border-radius: 20px;
            padding: 2.5rem 2rem 2rem 2rem;
            position: relative;
            animation: popIn 0.3s cubic-bezier(.68,-0.55,.27,1.55);
        }
        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .answer-input {
            border: 2px solid #b3b6e0;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1.1rem;
            margin-top: 1rem;
            outline: none;
            transition: border 0.2s;
        }
        .answer-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px #c7d2fe;
        }
        .btn-danger {
            background: #dc2626;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: bold;
            font-size: 1rem;
            transition: background 0.2s;
        }
        .btn-danger:hover {
            background: #b91c1c;
        }
        .question-container {
            margin-bottom: 1.5rem;
        }
        
        .options-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .option-btn {
            padding: 0.75rem;
            border: 2px solid #b3b6e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .option-btn:hover {
            background: #b3b6e0;
            color: white;
        }
        
        .option-btn.selected {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }
        
        .timer {
            font-size: 1.2rem;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 1rem;
        }
        
        .quiz-progress {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        /* Add styles for the check answer button */
        .check-answer-btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            background: #4f46e5; /* Using a primary color */
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: Arial, sans-serif;
            font-weight: 600;
            touch-action: manipulation;
            margin-top: 1rem; /* Add some space above the button */
        }
        .check-answer-btn:hover {
            background: #4338ca; /* Darker shade on hover */
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.3);
        }
        .check-answer-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .feedback {
            margin-top: 1rem;
            font-weight: bold;
        }
        .score {
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
<div class="flex">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <span class="m">M</span>
            <span class="r1">R</span>
            <span class="r2">R</span>
            <span class="b">B</span>
            <span class="library">LIBRARY</span>
        </div>
        <div class="nav-links">
            <a href="storybook.php"><i class="fas fa-book"></i><span>Storybook</span></a>
            <a href="alphabet.php"><i class="fas fa-font"></i><span>Alphabet</span></a>
            <a href="numbers.php"><i class="fas fa-calculator"></i><span>Numbers</span></a>
            <a href="riddles.php"><i class="fas fa-puzzle-piece"></i><span>Scoreboard</span></a>
            <a href="quizzes.php"><i class="fas fa-question-circle"></i><span>Quizzes</span></a>
            <a href="music.php"><i class="fas fa-music"></i><span>Music</span></a>
            <a href="logout.php" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>
    <!-- Main Content -->
    <main>
        <div class="tab-buttons">
            <button id="btnEng">English Quizzes</button>
            <button id="btnTag">Tagalog Quizzes</button>
        </div>
        <!-- English Quizzes -->
        <div id="EnglishQuizzes" class="quizzes-group">
            <?php if ($englishQuizzes && $englishQuizzes->num_rows > 0): ?>
                <?php while($quiz = $englishQuizzes->fetch_assoc()): ?>
                    <div class="quiz-card">
                        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                        <div class="quiz">
                            <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                            <div class="quiz-info">
                                <span>Type: <?php echo ucfirst(htmlspecialchars($quiz['quiz_type'])); ?></span>
                                <span>Difficulty: <?php echo ucfirst(htmlspecialchars($quiz['difficulty_level'])); ?></span>
                                <span>Time: <?php echo htmlspecialchars($quiz['time_limit']); ?> seconds</span>
                            </div>
                            <button onclick="startQuiz(<?php echo $quiz['id']; ?>, '<?php echo htmlspecialchars($quiz['title']); ?>', <?php echo $quiz['time_limit']; ?>)">
                                Start Quiz
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="quiz-card">
                    <p>No English quizzes available yet.</p>
                </div>
            <?php endif; ?>
        </div>
        <!-- Tagalog Quizzes -->
        <div id="TagalogQuizzes" class="quizzes-group" style="display:none;">
            <?php if ($tagalogQuizzes && $tagalogQuizzes->num_rows > 0): ?>
                <?php while($quiz = $tagalogQuizzes->fetch_assoc()): ?>
                    <div class="quiz-card">
                        <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                        <div class="quiz">
                            <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                            <div class="quiz-info">
                                <span>Type: <?php echo ucfirst(htmlspecialchars($quiz['quiz_type'])); ?></span>
                                <span>Difficulty: <?php echo ucfirst(htmlspecialchars($quiz['difficulty_level'])); ?></span>
                                <span>Time: <?php echo htmlspecialchars($quiz['time_limit']); ?> seconds</span>
                            </div>
                            <button onclick="startQuiz(<?php echo $quiz['id']; ?>, '<?php echo htmlspecialchars($quiz['title']); ?>', <?php echo $quiz['time_limit']; ?>)">
                                Start Quiz
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="quiz-card">
                    <p>No Tagalog quizzes available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Quiz Interface Modal -->
<div id="quizInterface" class="quiz-interface">
    <div class="quiz-modal">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 id="quizTitle" class="text-2xl font-bold"></h2>
            <button id="exitQuizBtn" class="btn btn-danger" style="background: #dc2626; color: #fff; border: none; border-radius: 8px; padding: 8px 16px; font-weight: bold; font-size: 1rem; transition: background 0.2s;">Exit</button>
        </div>
        <div class="timer" id="quizTimer"></div>
        <div class="quiz-progress" id="quizProgress"></div>
        <div id="questionContainer"></div>
        <div class="quiz-controls">
        </div>
    </div>
</div>

<script>
let currentQuiz = null;
let currentQuestionIndex = 0;
let userAnswers = [];
let timeLeft = 0;
let timerInterval = null;
let questionScores = [];
let questionFeedback = [];

// Tab switching logic
const engBtn = document.getElementById('btnEng');
const tagBtn = document.getElementById('btnTag');
const engGroup = document.getElementById('EnglishQuizzes');
const tagGroup = document.getElementById('TagalogQuizzes');

engBtn.onclick = () => {
    engGroup.style.display = 'flex';
    tagGroup.style.display = 'none';
    engBtn.classList.add('active');
    tagBtn.classList.remove('active');
};

tagBtn.onclick = () => {
    engGroup.style.display = 'none';
    tagGroup.style.display = 'flex';
    tagBtn.classList.add('active');
    engBtn.classList.remove('active');
};

function startQuiz(quizId, title, timeLimit) {
    // Fetch quiz questions
    fetch(`get_quiz_questions.php?quiz_id=${quizId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentQuiz = {
                    id: quizId,
                    title: title,
                    questions: data.questions
                };
                currentQuestionIndex = 0;
                userAnswers = new Array(data.questions.length).fill(null);
                questionScores = new Array(data.questions.length).fill(0);
                questionFeedback = new Array(data.questions.length).fill('');
                timeLeft = timeLimit; // Convert to seconds
                
                // Show quiz interface
                document.getElementById('quizTitle').textContent = title;
                document.getElementById('quizInterface').style.display = 'flex';
                
                // Start timer
                startTimer();
                
                // Show first question
                showQuestion();
            } else {
                alert('Error loading quiz: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading quiz. Please try again.');
        });
}

function showQuestion() {
    const question = currentQuiz.questions[currentQuestionIndex];
    const container = document.getElementById('questionContainer');
    const progress = document.getElementById('quizProgress');
    
    // Update progress
    progress.textContent = `Question ${currentQuestionIndex + 1} of ${currentQuiz.questions.length}`;

    let optionsHtml = '';
    let answerInputHtml = '';
    let optionsArr = [];
    try {
        optionsArr = question.options ? JSON.parse(question.options) : [];
    } catch (e) {
        optionsArr = [];
    }
    if (Array.isArray(optionsArr) && optionsArr.length > 0) {
        optionsHtml = `
            <div class="options-list">
                ${optionsArr.map((option, index) => `
                    <button class="option-btn ${userAnswers[currentQuestionIndex] === option ? 'selected' : ''}"
                            onclick="selectAnswer('${option}')">
                        ${option}
                    </button>
                `).join('')}
            </div>
        `;
    } else {
        // Show a text input for open-ended questions
        answerInputHtml = `
            <input type="text" id="answerInput" class="answer-input" placeholder="Type your answer..." value="${userAnswers[currentQuestionIndex] ? userAnswers[currentQuestionIndex] : ''}" oninput="userAnswers[currentQuestionIndex] = this.value" style="width: 100%; padding: 0.75rem; border: 2px solid #b3b6e0; border-radius: 8px; font-size: 1rem; margin-top: 1rem;" />
        `;
    }

    // Feedback area
    let feedbackHtml = '';
    if (questionFeedback[currentQuestionIndex]) {
        feedbackHtml = `<div class="feedback" style="margin-top:1rem;font-weight:bold;color:${questionScores[currentQuestionIndex] ? '#16a34a' : '#dc2626'};">${questionFeedback[currentQuestionIndex]}</div>`;
    }
    // Per-question score
    let scoreHtml = `<div style="margin-top:0.5rem;font-size:0.95rem;">Score for this question: <b>${questionScores[currentQuestionIndex]}</b></div>`;

    container.innerHTML = `
        <div class="question">
            <h3>${question.question}</h3>
            ${optionsHtml}
            ${answerInputHtml}
            <div id="feedback-${currentQuestionIndex}" class="feedback"></div>
            <div id="score-${currentQuestionIndex}" class="score">Score for this question: ${questionScores[currentQuestionIndex]}</div>
            <button class="check-answer-btn" data-question-index="${currentQuestionIndex}">Check Answer</button>
        </div>
    `;

    // Add event listeners for options if they exist
    if (question.options) {
        container.querySelectorAll('.option-btn').forEach(button => {
            button.addEventListener('click', () => {
                handleOptionClick(currentQuestionIndex, button.dataset.value);
            });
        });
    }

    // Add event listener for the check answer button
    container.querySelector('.check-answer-btn').addEventListener('click', handleCheckAnswer);

    // Restore saved answer if it exists
    if (userAnswers[currentQuestionIndex] !== null) {
        if (question.options) {
            const selectedOption = container.querySelector(`.option-btn[data-value="${userAnswers[currentQuestionIndex]}"]`);
            if (selectedOption) {
                selectedOption.classList.add('selected');
            }
        } else {
            const answerInput = container.querySelector('.answer-input');
            if (answerInput) {
                answerInput.value = userAnswers[currentQuestionIndex];
            }
        }
    }
}

function selectAnswer(answer) {
    userAnswers[currentQuestionIndex] = answer;
    checkCurrentAnswer();
    showQuestion(); // Refresh to show selection and feedback
}

function checkCurrentAnswer() {
    const question = currentQuiz.questions[currentQuestionIndex];
    let userAnswer = userAnswers[currentQuestionIndex];
    let correctAnswer = question.correct_answer;
    let optionsArr = [];
    try {
        optionsArr = question.options ? JSON.parse(question.options) : [];
    } catch (e) {
        optionsArr = [];
    }
    let isCorrect = false;
    if (Array.isArray(optionsArr) && optionsArr.length > 0) {
        // Multiple choice: check for exact match
        isCorrect = userAnswer === correctAnswer;
    } else {
        // Open-ended: allow multiple correct answers, ignore leading 'the', trim, and lowercase
        if (typeof userAnswer === 'string' && typeof correctAnswer === 'string') {
            let userAns = userAnswer.trim().toLowerCase();
            let correctAnsList = correctAnswer.split(',').map(ans => ans.trim().toLowerCase());
            if (userAns.startsWith('the ')) userAns = userAns.slice(4);
            isCorrect = correctAnsList.some(ans => {
                let a = ans;
                if (a.startsWith('the ')) a = a.slice(4);
                return userAns === a;
            });
        }
    }
    if (isCorrect) {
        questionScores[currentQuestionIndex] = parseInt(question.points);
        questionFeedback[currentQuestionIndex] = 'Correct!';
    } else {
        questionScores[currentQuestionIndex] = 0;
        questionFeedback[currentQuestionIndex] = 'Incorrect!';
    }
}

function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
    
    updateTimerDisplay();
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            submitQuiz();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('quizTimer').textContent = 
        `Time remaining: ${minutes}:${seconds.toString().padStart(2, '0')}`;
}

function submitQuiz() {
    clearInterval(timerInterval);
    // Calculate total score
    let score = questionScores.reduce((a, b) => a + b, 0);
    // Save score to database
    fetch('save_quiz_score.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            quiz_id: currentQuiz.id,
            score: score,
            answers: userAnswers
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('quizInterface').style.display = 'none';
            alert(`Quiz completed! Your score: ${score}`);
            // Trigger confetti
            const rect = document.querySelector('.quiz-modal').getBoundingClientRect();
            confettiBurst(rect.left + rect.width/2, rect.top + rect.height/2);
        } else {
            alert('Error saving score: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving score. Please try again.');
    });
}

// Keep existing confetti function
function confettiBurst(x, y) {
    for (let i = 0; i < 30; i++) {
        const conf = document.createElement('div');
        conf.style.position = 'fixed';
        conf.style.left = (x + Math.random() * 60 - 30) + 'px';
        conf.style.top = (y + Math.random() * 30 - 15) + 'px';
        conf.style.width = '6px';
        conf.style.height = '6px';
        conf.style.background = `hsl(${Math.random()*360},80%,70%)`;
        conf.style.borderRadius = '50%';
        conf.style.opacity = 1;
        conf.style.pointerEvents = 'none';
        conf.style.zIndex = 9999;
        document.body.appendChild(conf);
        const angle = Math.random() * 2 * Math.PI;
        const distance = 60 + Math.random() * 40;
        const dx = Math.cos(angle) * distance;
        const dy = Math.sin(angle) * distance;
        setTimeout(() => {
            conf.style.transition = 'all 0.8s cubic-bezier(.25,1,.5,1)';
            conf.style.transform = `translate(${dx}px,${dy}px)`;
            conf.style.opacity = 0;
        }, 10);
        setTimeout(() => conf.remove(), 1000);
    }
}

// Exit button logic
const exitQuizBtn = document.getElementById('exitQuizBtn');
if (exitQuizBtn) {
    exitQuizBtn.onclick = function() {
        if (confirm('Are you sure you want to exit the quiz? Your progress will be lost.')) {
            document.getElementById('quizInterface').style.display = 'none';
            clearInterval(timerInterval);
        }
    };
}

// Add event listener for the check answer button
function handleCheckAnswer(event) {
    const button = event.target;
    const questionIndex = parseInt(button.dataset.questionIndex);
    const question = currentQuiz.questions[questionIndex];
    
    let userAnswer = null;
    const answerInput = document.querySelector(`#questionContainer textarea[name="answers[]"], #questionContainer input[name="answers[]"]`);
    if (answerInput) {
        userAnswer = answerInput.value.trim();
    } else if (question.options) {
        // For multiple choice, the answer is already stored in userAnswers by handleOptionClick
        userAnswer = userAnswers[questionIndex];
    }

    if (userAnswer === null || userAnswer === '') {
        alert('Please enter an answer before checking.');
        return;
    }

    const correctAnswer = question.correct_answer.trim();
    let isCorrect = false;
    let feedbackText = '';

    if (question.options) {
        // Handle multiple choice
        isCorrect = (userAnswer === correctAnswer);
        if (isCorrect) {
            feedbackText = 'Correct!';
        } else {
            // Find the selected option text for feedback
            let selectedOptionText = '';
            try {
                const options = JSON.parse(question.options);
                const correctOption = options.find(option => option.value === correctAnswer);
                 // Assuming options are { label: 'Option Text', value: 'Option Value' }
                 const selectedOption = options.find(option => option.value === userAnswer);
                 if(selectedOption) selectedOptionText = selectedOption.label;

                if(correctOption) feedbackText = `Incorrect. The correct answer is: ${correctOption.label}`;
                else feedbackText = `Incorrect. The correct answer is: ${correctAnswer}`;
            } catch (e) {
                feedbackText = `Incorrect. The correct answer is: ${correctAnswer}`;
            }
        }
    } else {
        // Handle text input (case-insensitive and trimmed)
        isCorrect = (userAnswer.toLowerCase() === correctAnswer.toLowerCase());
        if (isCorrect) {
            feedbackText = 'Correct!';
        } else {
            feedbackText = `Incorrect. The correct answer is: ${correctAnswer}`;
        }
    }

    // Calculate score
    const points = parseInt(question.points) || 1;
    questionScores[questionIndex] = isCorrect ? points : 0;

    // Display feedback and score
    document.getElementById(`feedback-${questionIndex}`).textContent = feedbackText;
    document.getElementById(`score-${questionIndex}`).textContent = `Score for this question: ${questionScores[questionIndex]}`;

    // Disable the check answer button
    button.disabled = true;
}

// Keep the existing handleOptionClick function as it is
function handleOptionClick(questionIndex, selectedValue) {
    userAnswers[questionIndex] = selectedValue;
    // Optional: Visually indicate selected option
    const container = document.querySelector(`.question-block[data-question='${questionIndex}']`);
    if (container) {
        container.querySelectorAll('.option-btn').forEach(btn => {
            if (btn.dataset.value === selectedValue) {
                btn.classList.add('selected');
            } else {
                btn.classList.remove('selected');
            }
        });
    }
}

// Function to fetch and display scores
function displayScores() {
    fetch('get_scores.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const scoresContainer = document.getElementById('scoresContainer');
                scoresContainer.innerHTML = ''; // Clear previous scores
                
                if (data.scores.length > 0) {
                    data.scores.forEach(score => {
                        const scoreElement = document.createElement('div');
                        scoreElement.classList.add('score-item');
                        scoreElement.innerHTML = `
                            <strong>${score.item_title} (${score.type}):</strong> ${score.score} points
                            <span style="font-size: 0.8em; color: #666;">${new Date(score.created_at).toLocaleString()}</span>
                        `;
                        scoresContainer.appendChild(scoreElement);
                    });
                } else {
                    scoresContainer.innerHTML = '<p>No scores recorded yet.</p>';
                }
            } else {
                console.error('Error fetching scores:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Call displayScores when the page loads
displayScores();
</script>
</body>
</html>