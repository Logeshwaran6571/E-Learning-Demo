<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=assessment_db", "root", "");
    $stmt = $pdo->query("DESCRIBE test_packs");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} ({$row['Type']})\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
