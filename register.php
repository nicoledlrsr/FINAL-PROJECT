<?php
session_start();

function registerUser($firstName, $lastName, $age, $email, $password, $role, $gradeLevel, $school) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $dbpassword = "";
    $dbname = "mrrbdb"; // Updated to correct database name

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        echo "<div style='color:red;text-align:center;margin:10px;'>This email is already registered. Please use a different email or try logging in.</div>";
        $checkEmail->close();
        $conn->close();
        return;
    }
    $checkEmail->close();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, age, email, password, role, grade_level, school, created_at, last_login, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL, 'active')");
    $stmt->bind_param("ssisssss", $firstName, $lastName, $age, $email, $hashedPassword, $role, $gradeLevel, $school);

    if ($stmt->execute()) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $email;
        header("Location: index.php");
        exit;
    } else {
        echo "<div style='color:red;text-align:center;margin:10px;'>Registration failed: " . $stmt->error . "</div>";
    }
    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $gradeLevel = $_POST['grade_level'];
    $school = $_POST['school'];
    registerUser($firstName, $lastName, $age, $email, $password, $role, $gradeLevel, $school);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mini Ready Read Books</title>
    <link rel="stylesheet" href="styles.css">
    <style>
body.register {
    --register-bg-image: url('pic/register.jpg');
    --register-bg-color: #d7b9d5;
    background: var(--register-bg-image) no-repeat center center fixed;
    background-size: cover;
    background-position: center;
    min-height: 100vh;
    position: center;
    overflow: hidden;
    display: flex;
    background-color: var(--register-bg-color); /* Fallback color */
}
body.register::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #d7b9d5, #e6b0aa);
    opacity: 0.6;
    z-index: 0;
}
.floating-block {
    position: absolute;
    width: 25%;
    height: 25%;
    z-index: 1;
    opacity: 0.85;
    animation: floatBlock 6s ease-in-out infinite;
}
.floating-block.left.block1 { top: 10%; left: 1%; animation-delay: 0s; rotate: 10deg; }
.floating-block.left.block2 { top: 45%; left: -12%; animation-delay: 1s; }
.floating-block.left.block3 { top: 80%; width: 35%; height: 30%; left: 3%; animation-delay: 2s; }
.floating-block.right.block4 { top: 20%; right: 2%; width: 30%; height: 30%; animation-delay: 0.5s; }
.floating-block.right.block5 { top: 80%; right: 3%; animation-delay: 1.5s; }
@keyframes floatBlock {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-18px); }
}
    </style>
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
            <div class="menu-icon">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="nav-links">
                <a href="login.php">Log In</a>
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="mission.php">Our Mission</a>
                <a href="teachers.php">Personal Teacher</a>
            </div>
        </nav>

        <!-- Register Form Section -->
        <div class="register-container">
            <!-- Floating Block Images Left -->
            <img class="floating-block left block1" src="pic/c1.png" alt="Block 1">
            <img class="floating-block left block2" src="pic/c2.png" alt="Block 2">
            <img class="floating-block left block3" src="pic/c3.png" alt="Block 3">
            <!-- Floating Block Images Right -->
            <img class="floating-block right block4" src="pic/c4.png" alt="Block 4">
            <img class="floating-block right block5" src="pic/c5.png" alt="Block 5">

            <div class="register-form">
                <button class="close-button" onclick="window.location.href='index.php'">×</button>
                <h2>Create New Account</h2>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" id="age" name="age" placeholder="Age" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="" disabled selected>Select a Role</option>
                            <option value="student">Student</option>    
                            <option value="parent">Parent</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="grade_level">Grade Level</label>
                        <input type="text" id="grade_level" name="grade_level" placeholder="Grade Level" required>
                    </div>
                    <div class="form-group">
                        <label for="school">School</label>
                        <input type="text" id="school" name="school" placeholder="School" required>
                    </div>
                    <button type="submit" name="register" class="register-button">Create Account</button>
                    <div class="login-link">
                        <p>Already have an account? <a href="login.php">Log In</a></p>
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