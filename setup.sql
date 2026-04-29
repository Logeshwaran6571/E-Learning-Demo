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
