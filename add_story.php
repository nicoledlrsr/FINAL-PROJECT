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
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $difficulty_level = trim($_POST['difficulty_level'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($title) || empty($content) || empty($category) || empty($difficulty_level)) {
        $error = "Please fill in all required fields";
    } else {
        $stmt = $conn->prepare("INSERT INTO stories (title, content, category, difficulty_level, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $content, $category, $difficulty_level, $image_url);
        
        if ($stmt->execute()) {
            $success = "Story added successfully";
            // Clear form fields after successful submission
            $title = $content = $category = $difficulty_level = $image_url = '';
            error_log("Story added successfully: " . $title);
        } else {
            $error = "Error adding story: " . $stmt->error;
            error_log("Error adding story: " . $stmt->error);
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
    <title>Add Story - MRRB Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif !important;
            background: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Add New Story</h1>
                    <a href="admin.php?tab=stories" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Stories</a>
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

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="title" name="title" required
                            value="<?php echo htmlspecialchars($title ?? ''); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                        <textarea id="content" name="content" rows="10" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category" name="category" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select a category</option>
                            <option value="alphabet" <?php echo (isset($category) && $category === 'alphabet') ? 'selected' : ''; ?>>Alphabet</option>
                            <option value="numbers" <?php echo (isset($category) && $category === 'numbers') ? 'selected' : ''; ?>>Numbers</option>
                            <option value="riddles" <?php echo (isset($category) && $category === 'riddles') ? 'selected' : ''; ?>>Riddles</option>
                            <option value="fairy_tales" <?php echo (isset($category) && $category === 'fairy_tales') ? 'selected' : ''; ?>>Fairy Tales</option>
                            <option value="animals" <?php echo (isset($category) && $category === 'animals') ? 'selected' : ''; ?>>Animals</option>
                        </select>
                    </div>

                    <div>
                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
                        <select id="difficulty_level" name="difficulty_level" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select a difficulty level</option>
                            <option value="beginner" <?php echo (isset($difficulty_level) && $difficulty_level === 'beginner') ? 'selected' : ''; ?>>Beginner</option>
                            <option value="intermediate" <?php echo (isset($difficulty_level) && $difficulty_level === 'intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                            <option value="advanced" <?php echo (isset($difficulty_level) && $difficulty_level === 'advanced') ? 'selected' : ''; ?>>Advanced</option>
                        </select>
                    </div>

                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL (optional)</label>
                        <input type="url" id="image_url" name="image_url"
                            value="<?php echo htmlspecialchars($image_url ?? ''); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Leave empty to use a default image</p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                            Add Story
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 