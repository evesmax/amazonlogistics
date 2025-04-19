<?php
/**
 * RepoLog - Punto de entrada principal para reportes
 * 
 * Este script recibe un parámetro 'i' para el ID del reporte y siempre muestra
 * la página de filtros. Sirve como punto de entrada principal.
 * 
 * Compatible con PHP 5.5.9 y MySQL 5.5.62
 */


 //Depedemcias Legacy
// Inicializar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    include("parametros.php");
    $idorg = $_SESSION["accelog_idorganizacion"];
    $sql = $_SESSION["sequel"];
    $idestiloomision = $_SESSION["iestilo"];
    $descripcion = $_SESSION["desc"];

// Incluir archivos de configuración necesarios
require_once 'config.php';
require_once 'sqlcleaner.php';



// Comprobar si hay un parámetro 'i'
if (isset($_GET['i']) && !empty($_GET['i'])) {
    $reportId = intval($_GET['i']);
    
    // Almacenar el ID del reporte actual en la sesión
    $_SESSION['repolog_report_id'] = $reportId;
    
    // Mostrar siempre la página de filtros
    // En lugar de hacer un redirect, incluimos directamente la página de filtros
    $_GET['id'] = $reportId; // Asegurar que el filtro reciba el ID
    include 'repologfilters.php';
} else {
    // No hay ID de reporte, mostrar error
    echo "Error: Se requiere un ID de reporte válido. Por favor use: repolog.php?i=X donde X es el número de reporte.";
}
?>

<script>
    $('#nmloader_div',window.parent.document).hide();
</script>
