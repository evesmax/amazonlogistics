<?php
/**
 * Archivo de redirección para mantener compatibilidad
 * 
 * Este archivo redirige las solicitudes de index.php a reporte.php
 * para mantener la compatibilidad con implementaciones existentes.
 */

// Inicializar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay una solicitud pendiente (reporte activo)
if (isset($_SESSION['sql_consulta']) && !empty($_SESSION['sql_consulta'])) {
    // Redirigir a la página de resultados
    header('Location: reporte.php');
} else {
    // Si no hay reporte activo, redirigir a la página principal
    header('Location: repolog.php?i=1');
}
exit;