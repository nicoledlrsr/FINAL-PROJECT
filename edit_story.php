<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: admin.php');
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

$story = null;
$error = '';
$success = '';

// Get story data if ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM stories WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $story = $result->fetch_assoc();
    } else {
        $error = "Story not found";
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $difficulty_level = trim($_POST['difficulty_level'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if (empty($title) || empty($content) || empty($category) || empty($difficulty_level)) {
        $error = "Please fill in all required fields";
    } else {
        $stmt = $conn->prepare("UPDATE stories SET title = ?, content = ?, category = ?, difficulty_level = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("sssssi", $title, $content, $category, $difficulty_level, $image_url, $_POST['id']);
        
        if ($stmt->execute()) {
            $success = "Story updated successfully";
            // Refresh story data
            $story = [
                'id' => $_POST['id'],
                'title' => $title,
                'content' => $content,
                'category' => $category,
                'difficulty_level' => $difficulty_level,
                'image_url' => $image_url
            ];
        } else {
            $error = "Error updating story: " . $stmt->error;
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
    <title>Edit Story - MRRB Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Story</h1>
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

                <?php if ($story): ?>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($story['id']); ?>">
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="title" name="title" required
                                value="<?php echo htmlspecialchars($story['title']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea id="content" name="content" rows="10" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($story['content']); ?></textarea>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="alphabet" <?php echo $story['category'] === 'alphabet' ? 'selected' : ''; ?>>Alphabet</option>
                                <option value="numbers" <?php echo $story['category'] === 'numbers' ? 'selected' : ''; ?>>Numbers</option>
                                <option value="riddles" <?php echo $story['category'] === 'riddles' ? 'selected' : ''; ?>>Riddles</option>
                            </select>
                        </div>

                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
                            <select id="difficulty_level" name="difficulty_level" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="beginner" <?php echo $story['difficulty_level'] === 'beginner' ? 'selected' : ''; ?>>Beginner</option>
                                <option value="intermediate" <?php echo $story['difficulty_level'] === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                                <option value="advanced" <?php echo $story['difficulty_level'] === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                            </select>
                        </div>

                        <div>
                            <label for="image_url" class="block text-sm font-medium text-gray-700">Image URL (optional)</label>
                            <input type="url" id="image_url" name="image_url"
                                value="<?php echo htmlspecialchars($story['image_url']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                                Update Story
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center text-gray-600">
                        Story not found or you don't have permission to edit it.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 