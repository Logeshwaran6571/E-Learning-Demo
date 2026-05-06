<?php
// Script to check columns of questions table

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'assessment_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SHOW COLUMNS FROM questions");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

$conn->close();
?>
