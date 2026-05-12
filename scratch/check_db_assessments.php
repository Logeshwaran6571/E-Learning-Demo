<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    echo "Table: assessments\n";
    $stmt = $pdo->query("DESCRIBE assessments");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} ({$row['Type']})\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
