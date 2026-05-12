<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function addColumnIfMissing($pdo, $table, $column, $definition) {
        $stmt = $pdo->query("DESCRIBE `$table` `$column` ");
        if (!$stmt->fetch()) {
            echo "Adding column '$column' to table '$table'...\n";
            $pdo->exec("ALTER TABLE `$table` ADD `$column` $definition");
        } else {
            echo "Column '$column' already exists in table '$table'.\n";
        }
    }

    // Templates table
    addColumnIfMissing($pdo, 'templates', 'paper_title', "VARCHAR(255) DEFAULT NULL AFTER `name` ");
    addColumnIfMissing($pdo, 'templates', 'duration', "INT DEFAULT 60 AFTER `paper_title` ");
    addColumnIfMissing($pdo, 'templates', 'total_marks', "INT DEFAULT 0 AFTER `duration` ");

    // Template Sections table
    addColumnIfMissing($pdo, 'template_sections', 'marks_per_question', "INT DEFAULT 1 AFTER `num_questions` ");

    // Test Packs table
    addColumnIfMissing($pdo, 'test_packs', 'duration', "INT DEFAULT 60 AFTER `template_id` ");
    addColumnIfMissing($pdo, 'test_packs', 'scheduled_date', "DATE DEFAULT NULL AFTER `duration` ");
    addColumnIfMissing($pdo, 'test_packs', 'start_time', "TIME DEFAULT NULL AFTER `scheduled_date` ");
    addColumnIfMissing($pdo, 'test_packs', 'end_time', "TIME DEFAULT NULL AFTER `start_time` ");
    addColumnIfMissing($pdo, 'test_packs', 'candidates', "TEXT DEFAULT NULL AFTER `end_time` ");
    addColumnIfMissing($pdo, 'test_packs', 'candidates_type', "VARCHAR(50) DEFAULT 'all' AFTER `candidates` ");
    addColumnIfMissing($pdo, 'test_packs', 'status', "VARCHAR(50) DEFAULT 'draft' AFTER `candidates_type` ");
    addColumnIfMissing($pdo, 'test_packs', 'instructions', "TEXT DEFAULT NULL AFTER `status` ");
    addColumnIfMissing($pdo, 'test_packs', 'pass_mark', "INT DEFAULT 50 AFTER `instructions` ");
    addColumnIfMissing($pdo, 'test_packs', 'max_attempts', "INT DEFAULT 1 AFTER `pass_mark` ");
    addColumnIfMissing($pdo, 'test_packs', 'shuffle_questions', "TINYINT(1) DEFAULT 0 AFTER `max_attempts` ");
    addColumnIfMissing($pdo, 'test_packs', 'shuffle_options', "TINYINT(1) DEFAULT 0 AFTER `shuffle_questions` ");
    addColumnIfMissing($pdo, 'test_packs', 'proctored_exam', "TINYINT(1) DEFAULT 0 AFTER `shuffle_options` ");
    addColumnIfMissing($pdo, 'test_packs', 'browser_lockdown', "TINYINT(1) DEFAULT 0 AFTER `proctored_exam` ");
    addColumnIfMissing($pdo, 'test_packs', 'show_results', "TINYINT(1) DEFAULT 0 AFTER `browser_lockdown` ");
    addColumnIfMissing($pdo, 'test_packs', 'allow_backtracking', "TINYINT(1) DEFAULT 0 AFTER `show_results` ");
    addColumnIfMissing($pdo, 'test_packs', 'results_published', "TINYINT(1) DEFAULT 0 AFTER `allow_backtracking` ");

    echo "Database sync complete!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
