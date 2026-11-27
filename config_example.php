<?php
/**
 * StudyCircle Configuration Example
 *
 * Copy this file to config.php(or rename this file name to config.php) and update the database
 * credentials for your local development environment.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'StudyCircle');
define('BASE_URL', '/StudyCircle');

define('MAX_UPLOAD_BYTES', 10 * 1024 * 1024);

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'studycircle');
define('DB_USER', 'root');
define('DB_PASS', '');

define('DB_CHARSET', 'utf8mb4');

// PDO configuration
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
