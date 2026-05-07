CREATE DATABASE IF NOT EXISTS assessment_db;
USE assessment_db;

CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS template_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT NOT NULL,
    marks_type VARCHAR(50) NOT NULL,
    num_questions INT NOT NULL,
    knowledge_type VARCHAR(255),
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    status ENUM('Draft', 'Active') DEFAULT 'Draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS test_packs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    pack_name VARCHAR(255) NOT NULL,
    user_role VARCHAR(100) NOT NULL,
    template_id INT NOT NULL,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (template_id) REFERENCES templates(id)
);

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role VARCHAR(100),
    college VARCHAR(255),
    type ENUM('internal', 'recruitment') DEFAULT 'internal'
);

CREATE TABLE IF NOT EXISTS question_bank_repositories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS question_bank (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repository_id INT NOT NULL,
    question TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    category VARCHAR(100),
    difficulty ENUM('Easy', 'Medium', 'Hard') DEFAULT 'Medium',
    marks INT DEFAULT 1,
    option_a TEXT,
    option_b TEXT,
    option_c TEXT,
    option_d TEXT,
    correct_answer TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (repository_id) REFERENCES question_bank_repositories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_pack_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    option_a TEXT,
    option_b TEXT,
    option_c TEXT,
    option_d TEXT,
    correct_answer VARCHAR(10),
    marks INT NOT NULL,
    knowledge_type VARCHAR(255),
    FOREIGN KEY (test_pack_id) REFERENCES test_packs(id) ON DELETE CASCADE
);

-- OPTIONAL: Sample data
INSERT INTO templates (name, description) VALUES ('Frontend Developer — Standard', 'Standard template for frontend roles');
INSERT INTO template_sections (template_id, marks_type, num_questions, knowledge_type) VALUES (1, 'MCQ', 15, 'HTML / CSS Basics');
INSERT INTO template_sections (template_id, marks_type, num_questions, knowledge_type) VALUES (1, '2 Marks', 8, 'JS Fundamentals');

INSERT INTO employees (name, email, type) VALUES ('John Doe', 'john.doe@company.com', 'internal');
INSERT INTO employees (name, email, type) VALUES ('Jane Smith', 'jane.smith@company.com', 'internal');
INSERT INTO employees (name, email, type) VALUES ('Aditya Kumar', 'aditya.k@gmail.com', 'recruitment');

INSERT INTO question_bank_repositories (name) VALUES ('General');

INSERT INTO question_bank (repository_id, question, type, category, marks, option_a, option_b, option_c, option_d, correct_answer) 
VALUES (1, 'What is the output of 2 + "2"?', 'MCQ', 'JavaScript', 1, '4', '22', 'Error', 'None', 'B');

INSERT INTO question_bank (repository_id, question, type, category, marks) 
VALUES (1, 'Explain closures in JavaScript.', '2-Mark', 'JavaScript', 2);
