<?php
// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
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
        <a href="storybook.php" <?php echo basename($_SERVER['PHP_SELF']) == 'storybook.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-book"></i><span>Storybook</span>
        </a>
        <a href="alphabet.php" <?php echo basename($_SERVER['PHP_SELF']) == 'alphabet.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-font"></i><span>Alphabet</span>
        </a>
        <a href="numbers.php" <?php echo basename($_SERVER['PHP_SELF']) == 'numbers.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-calculator"></i><span>Numbers</span>
        </a>
        <a href="riddles.php" <?php echo basename($_SERVER['PHP_SELF']) == 'riddles.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-puzzle-piece"></i><span>Scoreboard</span>
        </a>
        <a href="quizzes.php" <?php echo basename($_SERVER['PHP_SELF']) == 'quizzes.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-question-circle"></i><span>Quizzes</span>
        </a>
        <a href="music.php" <?php echo basename($_SERVER['PHP_SELF']) == 'music.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-music"></i><span>Music</span>
        </a>
        <a href="logout.php" class="logout">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </div>
</aside>

<style>
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
.nav-links a.active {
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
/* Main content transition */
.main-content {
    margin-left: 80px;
    transition: margin-left 0.3s ease;
}
.sidebar:hover ~ .main-content {
    margin-left: 250px;
}
</style> 