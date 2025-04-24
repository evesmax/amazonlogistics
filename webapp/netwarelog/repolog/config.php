<?php
/**
 * Configuration file for database connection
 * 
 * Using PDO for MySQL database connection
 */

// Database credentials for MySQL connection
$host = '34.55.165.151';  // IP actualizada del servidor
$dbname = '_dbmlog0000018677';
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

// Si no existe la variable de sesión accelog_idempleado, inicializarla para propósitos de prueba
// En producción, esta variable debería ser configurada por el sistema de login
if (!isset($_SESSION['accelog_idempleado'])) {
    $_SESSION['accelog_idempleado'] = '2'; // Valor por defecto para pruebas
}
?>
