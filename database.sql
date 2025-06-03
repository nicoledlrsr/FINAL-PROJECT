-- Create the database
CREATE DATABASE IF NOT EXISTS mrrbdb;
USE mrrbdb;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    grade_level VARCHAR(20),
    school VARCHAR(100),
    status VARCHAR(20) DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create lessons table
CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content TEXT NOT NULL,
    category VARCHAR(50),
    difficulty_level VARCHAR(20),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create quizzes table
CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    quiz_type VARCHAR(50) NOT NULL,
    difficulty_level VARCHAR(20),
    time_limit INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create quiz_questions table
CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    correct_answer TEXT NOT NULL,
    options TEXT,
    points INT DEFAULT 1,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Create stories table
CREATE TABLE IF NOT EXISTS stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50),
    difficulty_level VARCHAR(20),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create scores table
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

-- Create user_activity table
CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity VARCHAR(255) NOT NULL,
    activity_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert some sample lessons
INSERT INTO lessons (title, description, content, category, difficulty_level) VALUES
('Learning the Alphabet', 'Introduction to the English alphabet', 'The English alphabet has 26 letters...', 'alphabet', 'beginner'),
('Numbers 1-10', 'Learn to count from 1 to 10', 'Let''s learn to count...', 'numbers', 'beginner'),
('Basic Colors', 'Learn the primary colors', 'The primary colors are red, blue, and yellow...', 'colors', 'beginner');

-- Insert some sample quizzes
INSERT INTO quizzes (title, description, quiz_type, difficulty_level) VALUES
('Alphabet Quiz', 'Test your knowledge of the alphabet', 'alphabet', 'beginner'),
('Numbers Quiz', 'Test your counting skills', 'numbers', 'beginner'),
('Colors Quiz', 'Test your knowledge of colors', 'colors', 'beginner');

-- Insert quiz questions
INSERT INTO quiz_questions (quiz_id, question, correct_answer, options, points) VALUES
(1, 'What is the first letter of the alphabet?', 'A', '["A", "B", "C", "D"]', 1),
(1, 'What letter comes after B?', 'C', '["A", "B", "C", "D"]', 1),
(2, 'What number comes after 5?', '6', '["4", "5", "6", "7"]', 1),
(2, 'How many fingers do you have on one hand?', '5', '["4", "5", "6", "10"]', 1),
(3, 'What color is the sky on a sunny day?', 'Blue', '["Red", "Blue", "Green", "Yellow"]', 1),
(3, 'What color is a banana?', 'Yellow', '["Red", "Blue", "Green", "Yellow"]', 1);

-- Insert some sample stories
INSERT INTO stories (title, content, category, difficulty_level) VALUES
('The Little Red Hen', 'Once upon a time, there was a little red hen...', 'animals', 'beginner'),
('The Three Little Pigs', 'Once upon a time, there were three little pigs...', 'animals', 'beginner'),
('Goldilocks and the Three Bears', 'Once upon a time, there was a little girl named Goldilocks...', 'fairy_tales', 'beginner'); 