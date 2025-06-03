<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
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

$lesson = null;
$error = '';
$success = '';
$type = '';

// Get lesson/quiz data if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // First try to get from lessons table
    $stmt = $conn->prepare("SELECT *, 'lesson' as type FROM lessons WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $lesson = $result->fetch_assoc();
        $type = 'lesson';
    } else {
        // If not found in lessons, try quizzes table
        $stmt = $conn->prepare("SELECT *, 'quiz' as type FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $lesson = $result->fetch_assoc();
            $type = 'quiz';
        } else {
            $error = "Content not found";
        }
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $difficulty_level = trim($_POST['difficulty_level'] ?? '');
    $type = $_POST['type'] ?? 'lesson';

    if (empty($title) || empty($description)) {
        $error = "Please fill in all required fields";
    } else {
        if ($type === 'lesson') {
            $stmt = $conn->prepare("UPDATE lessons SET title = ?, description = ?, content = ?, category = ?, difficulty_level = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("sssssi", $title, $description, $content, $category, $difficulty_level, $_POST['id']);
        } else {
            $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, content = ?, category = ?, difficulty_level = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->bind_param("sssssi", $title, $description, $content, $category, $difficulty_level, $_POST['id']);
        }
        
        if ($stmt->execute()) {
            $success = ucfirst($type) . " updated successfully";
            // Refresh lesson data
            $lesson = [
                'id' => $_POST['id'],
                'title' => $title,
                'description' => $description,
                'content' => $content,
                'category' => $category,
                'difficulty_level' => $difficulty_level,
                'type' => $type
            ];
        } else {
            $error = "Error updating " . $type . ": " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo ucfirst($type); ?> - MRRB Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit <?php echo ucfirst($type); ?></h1>
                    <a href="admin.php?tab=lessons" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Dashboard</a>
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

                <?php if ($lesson): ?>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($lesson['id']); ?>">
                        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="title" name="title" required
                                value="<?php echo htmlspecialchars($lesson['title']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($lesson['description']); ?></textarea>
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea id="content" name="content" rows="10" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($lesson['content']); ?></textarea>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="alphabet" <?php echo $lesson['category'] === 'alphabet' ? 'selected' : ''; ?>>Alphabet</option>
                                <option value="numbers" <?php echo $lesson['category'] === 'numbers' ? 'selected' : ''; ?>>Numbers</option>
                                <option value="riddles" <?php echo $lesson['category'] === 'riddles' ? 'selected' : ''; ?>>Riddles</option>
                                <?php if ($type === 'quiz'): ?>
                                <option value="quiz" <?php echo $lesson['category'] === 'quiz' ? 'selected' : ''; ?>>Quiz</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
                            <select id="difficulty_level" name="difficulty_level" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="beginner" <?php echo $lesson['difficulty_level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                <option value="intermediate" <?php echo $lesson['difficulty_level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="advanced" <?php echo $lesson['difficulty_level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                            </select>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Update <?php echo ucfirst($type); ?>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center text-gray-600">
                        Content not found or you don't have permission to edit it.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 