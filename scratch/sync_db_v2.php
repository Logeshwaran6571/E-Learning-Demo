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

    // Template Sections table
    addColumnIfMissing($pdo, 'template_sections', 'section_name', "VARCHAR(255) DEFAULT NULL AFTER `template_id` ");

    echo "Database sync complete!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
