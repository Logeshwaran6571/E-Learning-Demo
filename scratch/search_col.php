<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    $stmt = $pdo->query("SHOW TABLES");
    while ($table = $stmt->fetchColumn()) {
        $cols = $pdo->query("DESCRIBE $table");
        while ($col = $cols->fetch(PDO::FETCH_ASSOC)) {
            if (stripos($col['Field'], 'paper_title') !== false) {
                echo "Found column '{$col['Field']}' in table '$table'\n";
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
