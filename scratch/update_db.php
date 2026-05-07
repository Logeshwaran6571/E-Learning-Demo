<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    
    $sql = "ALTER TABLE test_packs 
            ADD COLUMN IF NOT EXISTS instructions TEXT NULL,
            ADD COLUMN IF NOT EXISTS pass_mark INT DEFAULT 50,
            ADD COLUMN IF NOT EXISTS max_attempts INT DEFAULT 1,
            ADD COLUMN IF NOT EXISTS shuffle_questions TINYINT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS shuffle_options TINYINT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS proctored_exam TINYINT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS browser_lockdown TINYINT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS show_results TINYINT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS allow_backtracking TINYINT DEFAULT 0";
            
    $pdo->exec($sql);
    echo "Table test_packs updated successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
