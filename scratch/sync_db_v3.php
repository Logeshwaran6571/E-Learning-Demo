<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Modifying column 'test_pack_id' in table 'questions' to be NULLable...\n";
    $pdo->exec("ALTER TABLE `questions` MODIFY `test_pack_id` INT(11) NULL");

    echo "Database sync complete!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
