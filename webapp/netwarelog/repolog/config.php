<?php
/**
 * Configuration file for database connection
 * 
 * Using PDO for MySQL database connection
 */

include("../../netwarelog/webconfig.php");

// Database credentials for MySQL connection
$host = $servidor;  // IP actualizada del servidor
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

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- INICIO DE VALIDACIÓN DE SESIÓN ---
// Si no existe la variable de sesión accelog_idempleado, la sesión ha caducado
if (!isset($_SESSION['accelog_idempleado']) || empty($_SESSION['accelog_idempleado'])) {
    echo "<script type='text/javascript'>
            alert('Se caduco la sesion');
            window.top.location.href = '".$url_dominio."';
          </script>";
    exit();
}
?>
