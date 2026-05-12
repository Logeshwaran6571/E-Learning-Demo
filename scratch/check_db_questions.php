<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    echo "Table: questions\n";
    $stmt = $pdo->query("DESCRIBE questions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} ({$row['Type']}) Null: {$row['Null']}\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
