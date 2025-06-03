<?php
session_start();

function loginUser($username, $password) {
    $validUsername = "testuser";
    $validPassword = "password123";
    
    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        return "Invalid credentials!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $error = loginUser($username, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Ready Read Books</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Mini Ready Read Books Library</h3>
            <button class="sidebar-toggle">☰</button>
        </div>
        <div class="sidebar-menu">
            <a href="#" class="sidebar-link active">Storybook</a>
            <a href="#" class="sidebar-link">Alphabet</a>
            <a href="#" class="sidebar-link">Numbers</a>
            <a href="#" class="sidebar-link">Riddle</a>
            <a href="#" class="sidebar-link">Quizzes</a>
            <a href="#" class="sidebar-link">Music</a>
        </div>
        <div class="sidebar-footer">
            <img src="images/girl_reading.png" alt="Girl Reading" class="footer-image">
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <div class="logo">
                <span>M</span><span>R</span><span>R</span><span>B</span>
            </div>
            <div class="menu-icon">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="nav-links">
                <a href="index.php" class="active">Home</a>
                <a href="about.php">About Us</a>
                <a href="mission.php">Our Mission</a>
                <a href="personalteacher.php">Personal Teacher</a>
                <a href="login.php">Log In</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>2025 Learnings in the Education</h2>
                <h1>Mini Ready Read Books</h1>
                <p>Enjoy our Free Children's Books</p>
                <p>Embark on Adventure, Inspire Young Minds and Cultivate a Love for Reading</p>
                <a href="login.php" class="cta-button">START READING</a>
            </div>
            <div class="hero-image">
                <img src="pic/bgplane.png" alt="Children on a paper airplane" class="paper-airplane">
            </div>
            <!-- Floating Blue Birds -->
            <img src="pic/bird.png" alt="Blue Bird" class="bird bird1">
            <img src="pic/bird.png" alt="Blue Bird" class="bird bird2">
        </section>
    </div>

    <style>
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999;
        border: none;
        box-shadow: none;
    }
    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #4CAF50;
    }
    .logo span:nth-child(1) { color: #4CAF50; }
    .logo span:nth-child(2) { color: #55555A; }
    .logo span:nth-child(3) { color: #7A5C58; }
    .logo span:nth-child(4) { color: #8B4513; }
    .menu-icon {
        display: none;
        cursor: pointer;
    }
    .menu-icon div {
        width: 25px;
        height: 4px;
        background-color: #333;
        margin: 5px 0;
    }
    .nav-links {
        display: flex;
        gap: 1rem;
        order: 2;
        margin-left: auto;
    }
    .nav-links a {
        text-decoration: none;
        color: rgba(51, 51, 51, 0.7);
        font-weight: 500;
        font-size: 18px;
        transition: color 0.3s, opacity 0.3s;
        border: none;
        padding: 0.5rem 1rem;
    }
    .nav-links a:hover {
        color: #00008B;
        opacity: 1;
    }
    .nav-links a.active {
        color: #2196F3;
        font-weight: bold;
        opacity: 1;
    }
    @media (max-width: 768px) {
        .navbar {
            flex-direction: row;
            align-items: center;
        }
        .menu-icon { display: block; }
        .nav-links {
            display: none;
            flex-direction: column;
            width: 100%;
            text-align: center;
            padding: 1rem 0;
            order: 2;
        }
        .nav-links.active { display: flex; }
        .nav-links a {
            color: #ffffff;
            font-size: 12px;
            padding: 0.8rem 1rem;
        }
        .nav-links a:hover {
            transform: none;
            background: rgba(255, 255, 255, 0.2);
        }
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.1);
        }
    }
    </style>
    <script>
    document.querySelector('.menu-icon').addEventListener('click', function() {
        document.querySelector('.nav-links').classList.toggle('active');
    });

    // Toggle sidebar
    document.querySelector('.sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });
    </script>
</body>
</html>