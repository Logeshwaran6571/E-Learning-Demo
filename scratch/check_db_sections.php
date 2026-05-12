<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    echo "Table: template_sections\n";
    $stmt = $pdo->query("DESCRIBE template_sections");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} ({$row['Type']})\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
