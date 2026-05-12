<?php

// --- TEMPORARY AUTO-SETUP START ---
$host = 'localhost';
$user = 'root';
$pass = '';
$mysqli = new mysqli($host, $user, $pass);
if (!$mysqli->connect_error) {
    $mysqli->query("CREATE DATABASE IF NOT EXISTS assessment_db");
    $mysqli->select_db('assessment_db');

    // Ensure template_id exists in questions
    $res = $mysqli->query("SHOW TABLES LIKE 'questions'");
    if ($res && $res->num_rows > 0) {
        $colCheck = $mysqli->query("SHOW COLUMNS FROM `questions` LIKE 'template_id'");
        if ($colCheck && $colCheck->num_rows == 0) {
            $mysqli->query("ALTER TABLE `questions` ADD COLUMN `template_id` INT DEFAULT NULL");
        }
    }

    // Also check if any tables are missing entirely
    $res = $mysqli->query("SHOW TABLES");
    if ($res && $res->num_rows < 5 && file_exists(__DIR__ . '/../setup.sql')) {
        $sql = file_get_contents(__DIR__ . '/../setup.sql');
        $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS assessment_db;/i', '', $sql);
        $sql = preg_replace('/USE assessment_db;/i', '', $sql);
        $mysqli->multi_query($sql);
        while ($mysqli->next_result()) {
            if ($result = $mysqli->store_result())
                $result->free();
        }
    }
    $mysqli->close();
}
// --- TEMPORARY AUTO-SETUP END ---

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '../app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));
