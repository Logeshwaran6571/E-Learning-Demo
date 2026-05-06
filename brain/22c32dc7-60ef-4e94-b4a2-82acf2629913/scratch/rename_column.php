<?php
// Script to rename 'content' column to 'question' in questions table

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'assessment_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "ALTER TABLE questions CHANGE content question TEXT";

if ($conn->query($sql) === TRUE) {
    echo "Successfully renamed 'content' to 'question'\n";
} else {
    echo "Error: " . $conn->error . "\n";
}

$conn->close();
?>
