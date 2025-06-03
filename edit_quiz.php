<?php
session_start();
require_once 'config.php';
requireAdmin(); // This ensures only admins can access this page

// Database connection
require_once 'db_connect.php';

$quiz = null;
$questions = [];
$error = '';
$success = '';

// Get quiz data if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Get quiz details
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc();
        
        // Get quiz questions
        $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $error = "Quiz not found";
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    
    try {
        // Update quiz details
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $quiz_type = trim($_POST['quiz_type'] ?? '');
        $difficulty_level = trim($_POST['difficulty_level'] ?? '');
        $time_limit = (int)($_POST['time_limit'] ?? 0);
        $time_limit_seconds = (int)($_POST['time_limit_seconds'] ?? 0);
        $total_time_limit = ($time_limit * 60) + $time_limit_seconds;

        if (empty($title) || empty($quiz_type) || empty($difficulty_level)) {
            throw new Exception("Please fill in all required fields");
        }

        $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, quiz_type = ?, difficulty_level = ?, time_limit = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("ssssii", $title, $description, $quiz_type, $difficulty_level, $total_time_limit, $_POST['id']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating quiz: " . $stmt->error);
        }
        $stmt->close();

        // Handle questions
        $questions = $_POST['questions'] ?? [];
        $correct_answers = $_POST['correct_answers'] ?? [];
        $options = $_POST['options'] ?? [];
        $points = $_POST['points'] ?? [];
        $question_ids = $_POST['question_ids'] ?? [];

        // Delete removed questions
        if (!empty($question_ids)) {
            $placeholders = str_repeat('?,', count($question_ids) - 1) . '?';
            $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id = ? AND id NOT IN ($placeholders)");
            $params = array_merge([$_POST['id']], $question_ids);
            $types = "i" . str_repeat("i", count($question_ids));
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();
        } else {
            // If no questions are kept, delete all questions for this quiz
            $stmt = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $stmt->close();
        }

        // Update or insert questions
        $stmt = $conn->prepare("INSERT INTO quiz_questions (id, quiz_id, question, correct_answer, options, points) 
                               VALUES (?, ?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE 
                               question = VALUES(question), 
                               correct_answer = VALUES(correct_answer), 
                               options = VALUES(options), 
                               points = VALUES(points)");

        foreach ($questions as $index => $question) {
            if (empty($question) || empty($correct_answers[$index])) {
                continue;
            }

            $current_options = isset($options[$index]) ? json_encode($options[$index]) : null;
            $current_points = isset($points[$index]) ? (int)$points[$index] : 1;
            $question_id = isset($question_ids[$index]) ? $question_ids[$index] : null;
            
            $stmt->bind_param("iisssi", 
                $question_id,
                $_POST['id'],
                $question,
                $correct_answers[$index],
                $current_options,
                $current_points
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating question: " . $stmt->error);
            }
        }
        $stmt->close();

        $conn->commit();
        $success = "Quiz updated successfully";
        
        // Refresh quiz data
        $stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $quiz = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Refresh questions
        $stmt = $conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

    } catch (Exception $e) {
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
    <title>Edit Quiz - MRRB Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Quiz</h1>
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

                <?php if ($quiz): ?>
                    <form method="POST" id="quizForm" class="space-y-6">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($quiz['id']); ?>">
                        
                        <!-- Quiz Details -->
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Quiz Title</label>
                                <input type="text" id="title" name="title" required
                                    value="<?php echo htmlspecialchars($quiz['title']); ?>"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="quiz_type" class="block text-sm font-medium text-gray-700">Quiz Type</label>
                                    <select id="quiz_type" name="quiz_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select type</option>
                                        <option value="alphabet" <?php echo $quiz['quiz_type'] === 'alphabet' ? 'selected' : ''; ?>>Alphabet</option>
                                        <option value="numbers" <?php echo $quiz['quiz_type'] === 'numbers' ? 'selected' : ''; ?>>Numbers</option>
                                        <option value="riddles" <?php echo $quiz['quiz_type'] === 'riddles' ? 'selected' : ''; ?>>Riddles</option>
                                        <option value="general" <?php echo $quiz['quiz_type'] === 'general' ? 'selected' : ''; ?>>General</option>
                                        <option value="tagalog_alphabet" <?php echo $quiz['quiz_type'] === 'tagalog_alphabet' ? 'selected' : ''; ?>>Tagalog Alphabet</option>
                                        <option value="tagalog_numbers" <?php echo $quiz['quiz_type'] === 'tagalog_numbers' ? 'selected' : ''; ?>>Tagalog Numbers</option>
                                        <option value="tagalog_riddles" <?php echo $quiz['quiz_type'] === 'tagalog_riddles' ? 'selected' : ''; ?>>Tagalog Riddles</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
                                    <select id="difficulty_level" name="difficulty_level" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select level</option>
                                        <option value="beginner" <?php echo $quiz['difficulty_level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                        <option value="intermediate" <?php echo $quiz['difficulty_level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                        <option value="advanced" <?php echo $quiz['difficulty_level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                                    <input type="number" id="time_limit" name="time_limit" min="0" step="1"
                                        value="<?php echo floor($quiz['time_limit'] / 60); ?>"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label for="time_limit_seconds" class="block text-sm font-medium text-gray-700 mt-2">Time Limit (seconds)</label>
                                    <input type="number" id="time_limit_seconds" name="time_limit_seconds" min="0" max="59" step="1"
                                        value="<?php echo $quiz['time_limit'] % 60; ?>"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div class="mt-8">
                            <h2 class="text-xl font-semibold mb-4">Questions</h2>
                            <div id="questions-container">
                                <?php foreach ($questions as $index => $question): ?>
                                    <div class="question-block bg-gray-50 p-4 rounded-lg mb-4" data-question="<?php echo $index; ?>">
                                        <input type="hidden" name="question_ids[]" value="<?php echo htmlspecialchars($question['id']); ?>">
                                        <div class="flex justify-between items-center mb-2">
                                            <h3 class="text-lg font-medium">Question <?php echo $index + 1; ?></h3>
                                            <button type="button" class="remove-question text-red-500 hover:text-red-700">Remove</button>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Question Text</label>
                                                <textarea name="questions[]" required rows="2"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($question['question']); ?></textarea>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Correct Answer</label>
                                                <input type="text" name="correct_answers[]" required
                                                    value="<?php echo htmlspecialchars($question['correct_answer']); ?>"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            </div>

                                            <div class="options-container">
                                                <label class="block text-sm font-medium text-gray-700">Options (one per line)</label>
                                                <textarea name="options[]" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3&#10;Option 4"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php 
                                                    $options = json_decode($question['options'], true);
                                                    echo htmlspecialchars(is_array($options) ? implode("\n", $options) : ''); 
                                                ?></textarea>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Points</label>
                                                <input type="number" name="points[]" value="<?php echo htmlspecialchars($question['points']); ?>" min="1" max="10"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="add-question" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                Add Question
                            </button>
                        </div>

                        <div class="flex justify-end space-x-4 mt-8">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Update Quiz
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center text-gray-600">
                        Quiz not found or you don't have permission to edit it.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let questionCount = <?php echo count($questions); ?>;

            function addQuestion() {
                const questionHtml = `
                    <div class="question-block bg-gray-50 p-4 rounded-lg mb-4" data-question="${questionCount}">
                        <input type="hidden" name="question_ids[]" value="">
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