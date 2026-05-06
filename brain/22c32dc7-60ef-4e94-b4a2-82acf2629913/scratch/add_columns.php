<?php
// Simple script to add missing columns to questions table

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'assessment_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$queries = [
    "ALTER TABLE questions ADD COLUMN template_id INT DEFAULT NULL AFTER test_pack_id",
    "ALTER TABLE questions ADD COLUMN section_idx INT DEFAULT 0 AFTER template_id"
];

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Successfully ran: $sql\n";
    } else {
        echo "Error running $sql: " . $conn->error . "\n";
    }
}

$conn->close();
?>
