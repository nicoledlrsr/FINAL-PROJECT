<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    header("Location: personalteacher.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Teacher - Mini Ready Read Books</title>
    <style>
        body {
            background: #A3BFFA; /* Light blue background from the image */
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            position: relative;
        }
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
        .main-content {
            padding: 2rem;
            position: relative;
        }
        .teacher-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2.5rem;
            position: relative;
            z-index: 1;
            background: linear-gradient(135deg, rgba(255,255,255,0.85) 60%, rgba(163,191,250,0.7));
            border-radius: 32px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255,255,255,0.25);
        }
        .block-elements {
            display: none; /* Remove the original floating blocks */
        }
        .floating-image {
            position: absolute;
            width: 70%;
            height: auto;
            animation: floatBlocks 4s ease-in-out infinite;
            z-index: 0; /* Behind the teacher cards */
        }
        .floating-image.left-1 {
            top: 100%;
            left: -50%; /* Adjusted gap */
        }
        .floating-image.left-2 {
            top: -5%;
            left: -50%;
        }
        .floating-image.right-1 {
            top: 20%;
            right: -60%;
        }
        .floating-image.right-2 {
            top: 120%;
            right: -50%;
        }
        @keyframes floatBlocks {
            0% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0); }
        }
        .card-container {
            display: flex;
            top: 50%;
            justify-content: center;
            align-items: stretch;
            gap: 2rem;
            flex-wrap: wrap;
            padding: 1rem 0;
            width: 100%;
            position: relative;
            z-index: 2;
        }
        .teacher-card {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            padding: 2rem 1.5rem 1.5rem 1.5rem;
            flex: 1 1 260px;
            min-width: 260px;
            max-width: 320px;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 24px rgba(80, 80, 180, 0.10);
            transition: transform 0.25s, box-shadow 0.25s;
            border: 1.5px solid #e0e7ff;
        }
        .teacher-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 12px 32px rgba(80, 80, 180, 0.18);
            border-color: #a3bffa;
        }
        .teacher-card .image-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 1.2rem;
        }
        .teacher-card .pink-square {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            opacity: 0.7;
            transform: translate(-50%, -50%) rotate(35deg);
            z-index: 1;
            border-radius: 18px;
        }
        .teacher-card img {
            position: relative;
            width: 100%;
            max-width: 120px;
            height: 120px;
            border-radius: 16px;
            object-fit: cover;
            z-index: 2;
            border: 2.5px solid #a3bffa;
            background: #f3f4f6;
        }
        .teacher-card h3 {
            color: #3b3b5c;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .teacher-card p {
            color: #555;
            font-size: 1rem;
            margin: 0.5rem 0;
            line-height: 1.5;
        }
        .teacher-card a {
            color: #3B82F6;
            text-decoration: none;
            transition: color 0.3s;
            font-weight: 500;
        }
        .teacher-card a:hover {
            color: #F472B6;
            text-decoration: underline;
        }
        @media (max-width: 900px) {
            .card-container {
                flex-wrap: wrap;
                justify-content: center;
            }
            .teacher-card {
                flex: 1 1 100%;
                max-width: 100%;
            }
            .floating-image {
                display: none; /* Hide floating images on small screens */
            }
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
<body>
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
            <a href="mission.php">Our Mission</a>
            <a href="personalteacher.php" class="active">Personal Teacher</a>
            <a href="login.php">Log In</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="teacher-container">
            <!-- Floating Images -->
            <img src="pic/block1.png" alt="Floating Block 1" class="floating-image left-1" />
            <img src="pic/c4.png" alt="Floating Block 2" class="floating-image left-2" />
            <img src="pic/block7.png" alt="Floating Block 3" class="floating-image right-1" />
            <img src="pic/c5.png" alt="Floating Block 4" class="floating-image right-2" />

            <div class="block-elements">
                <!-- Removed the original floating blocks -->
            </div>
            <div class="card-container">
                <div class="teacher-card">
                    <div class="image-wrapper">
                        <div class="pink-square"></div>
                        <img src="pic/e.jpg" onerror="this.onerror=null;this.src='pic/default_teacher.png';" alt="Teacher Image" />
                    </div>
                    <h3>Ejay Nicole O. del Rosario</h3>
                    <p>09954014799</p>
                    <p><a href="mailto:delrosarioejaynicole@gmail.com">delrosarioejaynicole@gmail.com</a></p>
                </div>
                <div class="teacher-card">
                    <div class="image-wrapper">
                        <div class="pink-square"></div>
                        <img src="pic/c.jpg" onerror="this.onerror=null;this.src='pic/default_teacher.png';" alt="Teacher Image" />
                    </div>
                    <h3>Caryll Carlos</h3>
                    <p>09309797833</p>
                    <p><a href="mailto:0323-3679@lspu.edu.ph">0323-3679@lspu.edu.ph</a></p>
                </div>
                <div class="teacher-card">
                    <div class="image-wrapper">
                        <div class="pink-square"></div>
                        <img src="pic/r.jpg" onerror="this.onerror=null;this.src='pic/default_teacher.png';" alt="Teacher Image" />
                    </div>
                    <h3>Rachel Abusman</h3>
                    <p>09076691578</p>
                    <p><a href="mailto:0323-3458@lspu.edu.ph">0323-3458@lspu.edu.ph</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelector('.menu-icon').addEventListener('click', function() {
        document.querySelector('.nav-links').classList.toggle('active');
    });
    </script>
</body>
</html>