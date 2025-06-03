<?php
session_start();
require_once 'config.php';
requireLogin();

// Get stories from database
$stories = getStories();
?>
<!DOCTYPE html>
<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>
   Storybook - MRRB Library
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&amp;display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <style>
   body {
      font-family: 'MedievalSharp', cursive;
      background: linear-gradient(to right, #d4a9e6, #8a4af3);
      min-height: 100vh;
    }
    /* Custom scrollbar for sidebar */
    aside::-webkit-scrollbar {
      width: 6px;
    }
    aside::-webkit-scrollbar-thumb {
      background-color: rgba(0,0,0,0.2);
      border-radius: 3px;
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
    /* Logo styles */
    .logo {
      position: absolute;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      white-space: nowrap;
      font-family: Arial, sans-serif;
    }
    .logo span:last-child {
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .sidebar:hover .logo span:last-child {
      opacity: 1;
    }
    /* Card text font */
    .card-text {
      font-family: Arial, sans-serif;
      font-size: 14px;
    }
    .card-button {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }
    /* Underwater decorations animations */
    .float-slow {
      animation: float-slow 6s ease-in-out infinite;
    }
    .float-medium {
      animation: float-medium 4s ease-in-out infinite;
    }
    .float-fast {
      animation: float-fast 3s ease-in-out infinite;
    }
    .sway-left {
      animation: sway-left 5s ease-in-out infinite;
    }
    .sway-right {
      animation: sway-right 5s ease-in-out infinite;
    }
    @keyframes float-slow {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }
    @keyframes float-medium {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    @keyframes float-fast {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-8px); }
    }
    @keyframes sway-left {
      0%, 100% { transform: rotate(-5deg); }
      50% { transform: rotate(5deg); }
    }
    @keyframes sway-right {
      0%, 100% { transform: rotate(5deg); }
      50% { transform: rotate(-5deg); }
    }
    /* Card styles */
    .story-card {
      background: rgba(240, 217, 255, 0.35);
      backdrop-filter: blur(8px);
      transition: all 0.3s ease;
      width: 100%;
      margin-left: 0;
    }
    .story-card:hover {
      background: rgba(240, 217, 255, 0.5);
      transform: translateY(-5px);
    }
    /* Main content transition */
    .main-content {
      margin-left: 80px;
      transition: margin-left 0.3s ease;
    }
    .sidebar:hover ~ .main-content {
      margin-left: 250px;
    }
  </style>
 </head>
 <body>
  <div class="flex min-h-screen">
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
      <a href="storybook.php" class="active"><i class="fas fa-book"></i><span>Storybook</span></a>
      <a href="alphabet.php"><i class="fas fa-font"></i><span>Alphabet</span></a>
      <a href="numbers.php"><i class="fas fa-calculator"></i><span>Numbers</span></a>
      <a href="riddles.php"><i class="fas fa-puzzle-piece"></i><span>Scoreboard</span></a>
      <a href="quizzes.php"><i class="fas fa-question-circle"></i><span>Quizzes</span></a>
      <a href="music.php"><i class="fas fa-music"></i><span>Music</span></a>
      <a href="logout.php" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
      </div>
   </aside>

   <!-- Main content -->
   <main class="main-content flex-1 relative overflow-hidden min-h-screen">
    <!-- Underwater decorations -->
   
    <!-- Cards grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 p-8 max-w-8xl mx-auto mt-8">
        <?php foreach ($stories as $story): ?>
        <article class="story-card rounded-3xl p-6 flex flex-col items-center relative h-[40vh]">
            <img alt="<?php echo htmlspecialchars($story['title']); ?>" 
                 class="rounded-2xl w-[70%] h-auto object-cover" 
                 src="<?php echo htmlspecialchars($story['image_url'] ?? 'https://storage.googleapis.com/a1aa/image/4ab534f6-787d-42aa-91a6-932228674e9d.jpg'); ?>"/>
            <p class="card-text mt-4 text-center text-lg">
                <?php echo htmlspecialchars($story['title']); ?>
            </p>
            <a href="view_story.php?id=<?php echo $story['id']; ?>" 
               class="card-button absolute bottom-6 right-6 bg-gradient-to-b from-[#7a5f8a] to-[#5a3f6a] text-white rounded-full px-4 py-2 hover:opacity-90 transition-opacity">
                View
            </a>
        </article>
        <?php endforeach; ?>
    </section>
   </main>
  </div>
 </body>
</html>