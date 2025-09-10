CREATE DATABASE IF NOT EXISTS adit_suggestion_box;
USE adit_suggestion_box;

-- Drop existing tables if they exist to start fresh
DROP TABLE IF EXISTS votes;
DROP TABLE IF EXISTS suggestions;

-- Create the main suggestions table
CREATE TABLE suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) DEFAULT 'Anonymous',
    name VARCHAR(100) DEFAULT 'Anonymous',
    category VARCHAR(100) NOT NULL,
    suggestion TEXT NOT NULL,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    status ENUM('Pending','Approved','Implemented') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create a table to track user votes and prevent duplicates
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suggestion_id INT NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    UNIQUE KEY unique_vote (suggestion_id, student_id),
    FOREIGN KEY (suggestion_id) REFERENCES suggestions(id) ON DELETE CASCADE
);