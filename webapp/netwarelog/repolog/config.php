<?php
/**
 * Configuration file for database connection
 * 
 * Using PDO for MySQL database connection
 */

// Database credentials for MySQL connection
$host = '34.66.63.218';
$dbname = '_dbmlog0000019551';
$user = 'nmdevel';
$password = 'nmdevel';

// PDO connection string for MySQL
define('DB_DSN', "mysql:host=$host;dbname=$dbname;charset=utf8");
define('DB_USER', $user);
define('DB_PASS', $password);

// Error reporting (set to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
