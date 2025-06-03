<?php
session_start();
require_once 'config.php';
requireLogin();

// Get story ID from URL
$storyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get story from database
$stories = getStories();
$story = null;
foreach ($stories as $s) {
    if ($s['id'] == $storyId) {
        $story = $s;
        break;
    }
}

// If story not found, redirect to storybook
if (!$story) {
    header('Location: storybook.php');
    exit;
}

// Record user activity
// recordUserActivity($_SESSION['user_id'], 'view_story', $storyId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($story['title']); ?> - MRRB</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100">

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-center mb-6"><?php echo htmlspecialchars($story['title']); ?></h1>
            
            <?php if ($story['image_url'] ?? 'https://storage.googleapis.com/a1aa/image/4ab534f6-787d-42aa-91a6-932228674e9d.jpg'): ?>
            <div class="mb-8">
                <img src="<?php echo htmlspecialchars($story['image_url'] ?? 'https://storage.googleapis.com/a1aa/image/4ab534f6-787d-42aa-91a6-932228674e9d.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($story['title']); ?>"
                     class="w-full h-auto rounded-lg shadow-md">
            </div>
            <?php endif; ?>

            <div class="prose max-w-none">
                <?php echo nl2br(htmlspecialchars($story['content'])); ?>
            </div>

            <div class="mt-8 flex justify-between items-center">
                <a href="storybook.php" class="text-purple-600 hover:text-purple-800">
                    ← Back to Stories
                </a>
                <?php if ($story['category']): ?>
                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                    <?php echo htmlspecialchars($story['category']); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html> 