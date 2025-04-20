<?php
/**
 * Configuration file for database connection
 * 
 * Using PDO for MySQL database connection
 */
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include("../../netwarelog/webconfig.php");

// Database credentials for MySQL connection
$host = $servidor;
$dbname = $bd;
$user = $usuariobd;
$password = $clavebd;

// PDO connection string for MySQL
define('DB_DSN', "mysql:host=$host;dbname=$dbname;charset=utf8");
define('DB_USER', $user);
define('DB_PASS', $password);

// Error reporting (set to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>
