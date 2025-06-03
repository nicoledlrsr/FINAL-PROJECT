<?php
session_start();

function loginUser($email, $password) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $dbpassword = "";
    $dbname = "mrrbdb";

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, email, password, first_name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a new session
            session_start();
            
            // Store data in session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login time
            $updateLogin = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $updateLogin->bind_param("i", $user['id']);
            $updateLogin->execute();
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: storybook.php");
            }
            exit;
        } else {
            return "Incorrect password. Please try again.";
        }
    } else {
        $stmt->close();
        $conn->close();
        return "Email not found. Please check your email or register.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['username']; // Using username field for email
    $password = $_POST['password'];
    $error = loginUser($email, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mini Ready Read Books</title>
    <link rel="stylesheet" href="styles.css">
    <style>
body {
    --login-bg-image: url('pic/backdrop.jpg');
    --login-bg-color: #d7b9d5;
    background: var(--login-bg-image) no-repeat center center fixed;
    background-size: cover;
    background-position: center;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
    display: flex;
    background-color: var(--login-bg-color); /* Fallback color */
}
body.login::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #d7b9d5, #e6b0aa);
    opacity: 0.6;
    z-index: 0;
}
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 80px);
    position: relative;
    margin-top: 80px;
}
.background-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}
.kids-reading {
    position: absolute;
    bottom: -30%;
    left: -30%;
    width: 75%;
    z-index: 1;
    animation: float 6s ease-in-out infinite;
}
.floating-star {
    position: absolute;
    top: -30%;
    left: 5%;
    transform: translateX(-50%);
    width: 55%;
    animation: float 6s ease-in-out infinite;
}
.login-form {
    background: rgba(173, 216, 230, 0.35);
    backdrop-filter: transparent (2px);
    padding: 2rem;
    border-radius: 20px;
    width: 400px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    z-index: 2;
    text-align: center;
    position: relative;
}
.close-button {
    position: absolute;
    top: 10px;
    right: 10px;
    background: transparent;
    border: none;
    font-size: 24px;
    color: #333;
    cursor: pointer;
    transition: color 0.3s;
}
.close-button:hover {
    color: #2196F3;
}
.login-form h2 {
    color: #2196F3;
    margin-bottom: 2rem;
    font-size: 2.5rem;
    font-weight: 600;
}
.form-group {
    margin-bottom: 1.5rem;
    text-align: left;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}
.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    background: rgba(255, 255, 255, 0.8);
}
.form-group input:focus {
    outline: none;
    border-color: #2196F3;
}
.forgot-password {
    text-align: right;
    margin-bottom: 1.5rem;
}
.forgot-password a {
    color: #2196F3;
    text-decoration: none;
}
.login-button {
    display: block;
    width: 100%;
    padding: 0.8rem;
    background-color: #2196F3;
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}
.login-button:hover {
    background-color: #0b7dda;
}
.register-link {
    text-align: center;
    margin-top: 1.5rem;
}
.register-link a {
    color: #2196F3;
    text-decoration: none;
}
    </style>
    <!-- To change the login background, override the CSS variable below: -->
    <!--
    <style>
    body.login {
        --login-bg-image: url('pic/loginbg.jpg');
        --login-bg-color: #e6b0aa;
    }
    </style>
    -->
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Mini Ready Read Books Library</h3>
            <button class="sidebar-toggle">☰</button>
        </div>
        <div class="sidebar-menu">
            <a href="storybook.php" class="sidebar-link active">Storybook</a>
            <a href="alphabet.php" class="sidebar-link">Alphabet</a>
            <a href="numbers.php" class="sidebar-link">Numbers</a>
            <a href="riddles.php" class="sidebar-link">Riddle</a>
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
            <div class="menu-icon">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="nav-links">
                <a href="login.php" class="active">Login</a>
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="mission.php">Our Mission</a>
                <a href="personalteacher.php">Personal Teacher</a>
            </div>
        </nav>

        <!-- Login Form Section -->
        <div class="login-container">
            <div class="background-elements">
                <img src="pic/booksfly.png" alt="Kids reading" class="kids-reading">
                <img src="pic/star.png" alt="Star" class="floating-star">
            </div>
            <div class="login-form">
                <button class="close-button" onclick="window.location.href='index.php'">×</button>
                <h2>Log In</h2>
                <?php if (isset($error)): ?>
                    <div style="color: red; margin-bottom: 15px; text-align: center;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Email</label>
                        <input type="email" id="username" name="username" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" name="login" class="login-button">Log In</button>
                    <div class="register-link">
                        <p>Don't have an account? <a href="register.php">Create Account</a></p>
                        <p>Are you an admin? <a href="admin.php">Admin Login</a></p>
                    </div>
                </form>
            </div>
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