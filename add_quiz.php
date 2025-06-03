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

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert quiz
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $quiz_type = trim($_POST['quiz_type'] ?? '');
        $difficulty_level = trim($_POST['difficulty_level'] ?? '');
        $time_limit = (int)($_POST['time_limit'] ?? 0);
        $time_limit_seconds = (int)($_POST['time_limit_seconds'] ?? 0);
        $total_time_limit = $time_limit + $time_limit_seconds;

        if (empty($title) || empty($quiz_type) || empty($difficulty_level)) {
            throw new Exception("Please fill in all required fields");
        }

        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, quiz_type, difficulty_level, time_limit) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $quiz_type, $difficulty_level, $total_time_limit);
        
        if (!$stmt->execute()) {
            throw new Exception("Error adding quiz: " . $stmt->error);
        }
        
        $quiz_id = $conn->insert_id;
        $stmt->close();

        // Insert questions
        $questions = $_POST['questions'] ?? [];
        $correct_answers = $_POST['correct_answers'] ?? [];
        $options = $_POST['options'] ?? [];
        $points = $_POST['points'] ?? [];

        if (empty($questions)) {
            throw new Exception("Please add at least one question");
        }

        $stmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question, correct_answer, options, points) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($questions as $index => $question) {
            if (empty($question) || empty($correct_answers[$index])) {
                continue; // Skip empty questions
            }

            $current_options = isset($options[$index]) ? json_encode($options[$index]) : null;
            $current_points = isset($points[$index]) ? (int)$points[$index] : 1;
            
            $stmt->bind_param("isssi", 
                $quiz_id, 
                $question, 
                $correct_answers[$index], 
                $current_options, 
                $current_points
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error adding question: " . $stmt->error);
            }
        }
        
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        $success = "Quiz added successfully";
        
        // Clear form fields
        $_POST = array();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error = $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Quiz - MRRB Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: Arial, sans-serif !important;
            background: linear-gradient(to right, #d4a9e6, #8a4af3);
            min-height: 100vh;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            margin-left: 80px;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 0.375rem;
            font-size: 1rem;
        }
        .question-block {
            border: 1px solid #ddd;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
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
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Add New Quiz</h1>
                    <a href="admin.php?tab=quizzes" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Quizzes</a>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="quizForm" class="space-y-6">
                    <!-- Quiz Details -->
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Quiz Title</label>
                            <input type="text" id="title" name="title" required
                                value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="quiz_type" class="block text-sm font-medium text-gray-700">Quiz Type</label>
                                <select id="quiz_type" name="quiz_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select type</option>
                                    <option value="alphabet" <?php echo (isset($_POST['quiz_type']) && $_POST['quiz_type'] === 'alphabet') ? 'selected' : ''; ?>>Alphabet</option>
                                    <option value="numbers" <?php echo (isset($_POST['quiz_type']) && $_POST['quiz_type'] === 'numbers') ? 'selected' : ''; ?>>Numbers</option>
                                    <option value="riddles" <?php echo (isset($_POST['quiz_type']) && $_POST['quiz_type'] === 'riddles') ? 'selected' : ''; ?>>Riddles</option>
                                </select>
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
                                <select id="difficulty_level" name="difficulty_level" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select level</option>
                                    <option value="beginner" <?php echo (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] === 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="intermediate" <?php echo (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] === 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="advanced" <?php echo (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] === 'advanced') ? 'selected' : ''; ?>>Advanced</option>
                                </select>
                            </div>

                            <div>
                                <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                                <input type="number" id="time_limit" name="time_limit" min="0" step="1"
                                    value="<?php echo htmlspecialchars($_POST['time_limit'] ?? '10'); ?>"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <label for="time_limit_seconds" class="block text-sm font-medium text-gray-700 mt-2">Time Limit (seconds)</label>
                                <input type="number" id="time_limit_seconds" name="time_limit_seconds" min="0" max="59" step="1"
                                    value="<?php echo htmlspecialchars($_POST['time_limit_seconds'] ?? '0'); ?>"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Questions Section -->
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Questions</h2>
                        <div id="questions-container">
                            <!-- Questions will be added here dynamically -->
                        </div>
                        <button type="button" id="add-question" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Add Question
                        </button>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                            Create Quiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let questionCount = 0;

            function addQuestion() {
                const questionHtml = `
                    <div class="question-block bg-gray-50 p-4 rounded-lg mb-4" data-question="${questionCount}">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-medium">Question ${questionCount + 1}</h3>
                            <button type="button" class="remove-question text-red-500 hover:text-red-700">Remove</button>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Question Text</label>
                                <textarea name="questions[]" required rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                                <input type="text" name="correct_answers[]" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="options-container">
                                <label class="block text-sm font-medium text-gray-700">Options (one per line)</label>
                                <textarea name="options[]" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3&#10;Option 4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Points</label>
                                <input type="number" name="points[]" value="1" min="1" max="10"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                `;
                
                $('#questions-container').append(questionHtml);
                questionCount++;
            }

            // Add first question by default
            addQuestion();

            // Add question button click handler
            $('#add-question').click(addQuestion);

            // Remove question button click handler
            $(document).on('click', '.remove-question', function() {
                $(this).closest('.question-block').remove();
                // Renumber remaining questions
                $('.question-block').each(function(index) {
                    $(this).find('h3').text('Question ' + (index + 1));
                    $(this).attr('data-question', index);
                });
                questionCount--;
            });

            // Form submission validation
            $('#quizForm').submit(function(e) {
                if (questionCount === 0) {
                    e.preventDefault();
                    alert('Please add at least one question');
                }
            });
        });
    </script>
</body>
</html> 