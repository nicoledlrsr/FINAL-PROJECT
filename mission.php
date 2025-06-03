<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Mission - Mini Ready Read Books</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="nav.css">
    <style>
        body.mission {
            background: url('pic/mission.jpg') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        .mission-main-content {
            margin-top: 80px;
            position: relative;
            width: 100%;
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mission-card {
            background: linear-gradient(45deg,rgb(207, 190, 231),rgb(182, 166, 181));
            border-radius: 20px;
            width: 30%;
            min-height: 60vh;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            padding: 2rem 1.5rem;
            position: absolute;
            top: 25%;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            font-size: 1.5rem;
            color: #6b48a8;
            border-radius: 32px;
            transition: box-shadow 0.3s;
            overflow: hidden;
            animation: float-card 4s ease-in-out infinite;
        }
        .mission-card.left {
            left: 5%;
        }
        .mission-card.right {
            right: 5%;
        }
        .floating-pencil-center {
            position: absolute;
            left: 50%;
            top: 55%;
            transform: translate(-50%, -50%);
            width: 50%;
            z-index: 3;
            animation: float-pencil 4s ease-in-out infinite;
        }
        .card-pencil-img {
            position: absolute;
            left: -5%;
            bottom: 5%;
            width:30%;
            z-index: 3;
            animation: float-pencil-bl 5s ease-in-out infinite;
        }
        @keyframes float-card {
            0% { transform: translateY(0); }
            50% { transform: translateY(-24px); }
            100% { transform: translateY(0); }
        }
        @keyframes float-pencil {
            0% { transform: translate(-50%, -50%) translateY(0); }
            50% { transform: translate(-50%, -50%) translateY(-30px); }
            100% { transform: translate(-50%, -50%) translateY(0); }
        }
        @keyframes float-pencil-bl {
            0% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0); }
        }
        .mission-title-img {
            position: absolute;
            top: -5%;
            right: 7%;
            width:75%;
            max-width: 40vw;
            z-index: 4;
            animation: float-title 4s ease-in-out infinite;
        }
        @keyframes float-title {
            0% { transform: translateY(0);}
            50% { transform: translateY(-25px);}
            100% { transform: translateY(0);}
        }
        .mission-desc {
            text-align: center;
            color: #6b48a8;
            font-size: 1.3rem;
            margin-top: 2rem;
            text-shadow: 1px 1px 4px #fff;
            z-index: 3;
            position: relative;
        }
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            background: white;
            padding: 2rem;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1001;
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .sidebar-header h3 {
            color: #6b48a8;
            font-size: 1.2rem;
            margin: 0;
        }
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b48a8;
        }
        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .sidebar-link {
            text-decoration: none;
            color: #333;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background-color: #f0f0f0;
            color: #6b48a8;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 2rem;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .footer-image {
            max-width: 80%;
            height: auto;
        }
        /* Navigation Bar Styles */
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
</head>
<body class="mission">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Mini Ready Read Books Library</h3>
            <button class="sidebar-toggle">☰</button>
        </div>
        <div class="sidebar-menu">
            <a href="storybook.php" class="sidebar-link">Storybook</a>
            <a href="alphabet.php" class="sidebar-link">Alphabet</a>
            <a href="numbers.php" class="sidebar-link">Numbers</a>
            <a href="riddles.php" class="sidebar-link">Scoreboard</a>
            <a href="quizzes.php" class="sidebar-link">Quizzes</a>
            <a href="music.php" class="sidebar-link">Music</a>
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
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="mission.php" class="active">Our Mission</a>
                <a href="personalteacher.php">Personal Teacher</a>
                <a href="login.php">Log In</a>
            </div>
        </nav>

        <div class="mission-main-content">
            <!-- Left Card -->
            <div class="mission-card left">
                <div class="card-content">
                    <h3>Inspire Young Minds</h3>
                    <p>We believe every child can achieve greatness through creativity and curiosity.</p>
                </div>
            </div>
            <!-- Right Card -->
            <div class="mission-card right">
                <div class="card-content">
                    <h3>Fun & Learning</h3>
                    <p>We blend fun and education to make every learning moment memorable and exciting.</p>
                </div>
            </div>
            <!-- Floating Kids on Pencil (center) -->
            <img src="pic/pen.png" alt="Kids riding a pencil" class="floating-pencil-center">
            <img src="pic/mtext.png" alt="Our Mission" class="mission-title-img">
            <img src="pic/PEN2.png" alt="Kids riding a pencil" class="card-pencil-img">
        </div>
    </div>

    <script>
        // Toggle mobile menu (top navbar)
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