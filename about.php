<?php 
// Start the session if needed for user authentication 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Fun World</title>
    <link rel="stylesheet" href="nav.css">
    <style>
        body.about {
            background: url('pic/bgabout.jpg') no-repeat center center fixed;
            font-family: 'Arial', sans-serif;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            width: 100%;
            position: relative;
            overflow: hidden;
            background-color: #cdc1ff;
            margin: 0;
            padding: 0;
        }

        body.about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #cdc1ff, #905782);
            opacity: 0.8;
            z-index: 0;
        }

        .main-content {
            flex-grow: 1;
            position: relative;
            z-index: 1;
            padding: 0;
            width: 100%;
            min-height: 100vh;
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

        .logo span:nth-child(1) {
            color: #4CAF50;  /* M */
        }

        .logo span:nth-child(2) {
            color: #55555A;  /* R */
        }

        .logo span:nth-child(3) {
            color: #7A5C58;  /* R */
        }

        .logo span:nth-child(4) {
            color: #8B4513;  /* B */
        }

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

        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            transform: scaleX(1);
            transition: transform 0.3s ease;
        }

        .nav-links a:hover::after {
            transform: scaleX(1);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem;
            min-height: 100vh;
            width: 100%;
            margin-top: 0;
        }

        .card-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 4rem;
            padding: 2.5rem;
            margin-top: 2rem;
            position: relative;
            z-index: 2;
            max-width: 1200px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            flex-wrap: nowrap;
        }

        .card {
            background: linear-gradient(45deg,rgb(207, 190, 231),rgb(182, 166, 181));;
            border-radius: 20px;;
            width: 500px;
            height: 480px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border: 3px solid transparent;
            background-clip: padding-box;
            z-index: 3;
            margin: 1.2rem;
        }

        .card-image {
            width: 100%;
            height: 50%;
            overflow: hidden;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .card-image img {
            transform: scale(1.05);
        }

        .card-content {
            width: 100%;
            height: 50%;
            padding: 2.2rem;
            display: flex;
            flex-direction: column;
            justify-content: last baseline;
            align-items: last baseline;
            text-align: left;
        }

        .card h3 {
            font-size: 2rem;
            color: #6b48a8;
            margin-bottom: 1.5rem;
            font-weight: bold;
            text-align: left;
            width: 100%;
        }

        .card p {
            font-size: 12;
            color: #333;
            margin-bottom: 1.7rem;
            line-height: 1.7;
            text-align: justify;
            width: 80%;
        }

        .card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 30px rgba(107, 72, 168, 0.3);
        }

        /* Floating 3D Blocks */
        .floating-block {
            position: absolute;
            width: 80px;
            height: 80px;
            z-index: 1;
            transform-style: preserve-3d;
            perspective: 1000px;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        .floating-block img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }

        .block1 {
            top: -10%;
            left: -20%;
            width: 50%;
            height: 50%;
            animation: float1 6s ease-in-out infinite;
            transform: rotate(45deg) rotateX(20deg);
        }

        .block2 {
            top: 25%;
            right: 15%;
            width: 400px;
            max-width: 90vw;
            height: 200px;
            animation: float2 7s ease-in-out infinite;
            transform: rotate(-30deg) rotateY(20deg);
            display: block;
        }

        .block3 {
            top: 45%;
            left: 20%;
            animation: float3 8s ease-in-out infinite;
            transform: rotate(15deg) rotateX(-20deg);
        }

        .block4 {
            top: 55%;
            right: 25%;
            animation: float4 9s ease-in-out infinite;
            transform: rotate(-45deg) rotateY(-20deg);
        }

        .block5 {
            top: 60%;
            left: -20%;
            width: 50%;
            height: 50%;
            animation: float5 7s ease-in-out infinite;
            transform: rotate(30deg) rotateX(15deg);
        }

        .block6 {
            top: 35%;
            left: 35%;
            animation: float6 8s ease-in-out infinite;
            transform: rotate(-15deg) rotateY(15deg);
        }

        .block7 {
            top: 50%;
            right: -25%;
            width: 50%;
            height: 50%;
            animation: float7 6s ease-in-out infinite;
            transform: rotate(60deg) rotateX(-15deg);
        }

        @keyframes float1 {
            0%, 100% { transform: rotate(45deg) rotateX(20deg) translateY(0); }
            50% { transform: rotate(45deg) rotateX(20deg) translateY(-20px); }
        }

        @keyframes float2 {
            0%, 100% { transform: rotate(-30deg) rotateY(20deg) translateY(0); }
            50% { transform: rotate(-30deg) rotateY(20deg) translateY(-25px); }
        }

        @keyframes float3 {
            0%, 100% { transform: rotate(15deg) rotateX(-20deg) translateY(0); }
            50% { transform: rotate(15deg) rotateX(-20deg) translateY(-15px); }
        }

        @keyframes float4 {
            0%, 100% { transform: rotate(-45deg) rotateY(-20deg) translateY(0); }
            50% { transform: rotate(-45deg) rotateY(-20deg) translateY(-30px); }
        }

        @keyframes float5 {
            0%, 100% { transform: rotate(30deg) rotateX(15deg) translateY(0); }
            50% { transform: rotate(30deg) rotateX(15deg) translateY(-20px); }
        }

        @keyframes float6 {
            0%, 100% { transform: rotate(-15deg) rotateY(15deg) translateY(0); }
            50% { transform: rotate(-15deg) rotateY(15deg) translateY(-25px); }
        }

        @keyframes float7 {
            0%, 100% { transform: rotate(60deg) rotateX(-15deg) translateY(0); }
            50% { transform: rotate(60deg) rotateX(-15deg) translateY(-15px); }
        }

        @media (max-width: 992px) {
            .card-container { gap: 2rem; }
            .card { width: 250px; min-height: 350px; }
            .about-us-image { width: 280px; }
            .floating-block {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding: 1rem;
            }
            
            .card-container {
                flex-direction: column;
                gap: 2.5rem;
                padding: 1.8rem;
            }

            .card {
                width: 98vw;
                max-width: 420px;
                height: 340px;
            }

            .card-image {
                height: 45%;
            }

            .card-content {
                padding: 1.2rem;
            }

            .card h3 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            .card p {
                font-size: 1.1rem;
                line-height: 1.5;
                width: 98%;
            }

            .about-us-image { width: 200px; }
            .menu-icon { display: block; }
            .navbar {
                flex-direction: row;
                align-items: center;
            }
            .nav-links {
                display: none;
                flex-direction: column;
                width: 100%;
                text-align: center;
                padding: 1rem 0;
                order: 2;
            }
            .nav-links.active {
                display: flex;
            }
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
            .floating-block {
                width: 40px;
                height: 40px;
            }
        }

        /* Floating About Text Image */
        .floating-about-text {
            position: fixed;
            bottom: -10%;
            left: 50%;
            transform: translateX(-50%);
            width: 40%;
            height: auto;
            z-index: 10;
            animation: floatText 3s ease-in-out infinite;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        @keyframes floatText {
            0% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-15px); }
            100% { transform: translateX(-50%) translateY(0); }
        }

        @media (max-width: 768px) {
            .floating-about-text {
                width: 80%;
                bottom: 1%;
            }
        }
    </style>
</head>
<body class="about">

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
                <a href="about.php" class="active">About Us</a>
                <a href="mission.php">Our Mission</a>
                <a href="personalteacher.php">Personal Teacher</a>
                <a href="login.php">Log In</a>
            </div>
        </nav>

        <!-- About Section -->
        <section class="hero">
            <!-- Floating 3D Blocks -->
            <div class="floating-block block1">
                <img src="pic/block7.png" alt="Floating Block 1">
            </div>
            <div class="floating-block block2">
                <img src="pic/block1.png" alt="Floating Block 2">
            </div>
            <div class="floating-block block3">
                <img src="pic/block5.png" alt="Floating Block 3">
            </div>
            <div class="floating-block block4">
                <img src="pic/block7.png" alt="Floating Block 4">
            </div>
            <div class="floating-block block5">
                <img src="pic/block5.png" alt="Floating Block 5">
            </div>
            <div class="floating-block block6">
                <img src="pic/block7.png" alt="Floating Block 6">
            </div>
            <div class="floating-block block7">
                <img src="pic/block1.png" alt="Floating Block 7">
            </div>

            <div class="card-container">
                <div class="card">
                    <div class="card-content">
                        <h3>Who We Are</h3>
                        <p>We are a group dedicated to helping every child grow and succeed. We provide a safe and supportive space where children can learn, explore, and develop their skills. Our goal is to teach not just knowledge, but also values like kindness, respect, and responsibility, preparing them for a bright future.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>Our Vision</h3>
                        <p>We're on a mission to make learning so fun, kids will forget they're even doing it. Developing creativity and critical thinking skills and to inspire a love for learning through interactive experiences.  </p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <h3>Our Approach</h3>
                        <p>We blend education with entertainment, Making learning an exciting adventure for us. Who says education can't be a thrilling adventure? Hold tight!.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Floating About Text Image -->
    <img src="pic/about us.png" alt="About Text" class="floating-about-text">

    <!-- JavaScript for Sidebar and Menu Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const menuIcon = document.querySelector('.menu-icon');
            const navLinks = document.querySelector('.nav-links');

            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });

            menuIcon.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        });
    </script>
</body>
</html>