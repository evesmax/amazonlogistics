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
    include("../../netwarelog/webconfig.php");
    include("parametros.php");
    include "../catalog/clases/clcsrf.php";

    $usuario=$_SESSION["accelog_idempleado"];
    $idorg = $_SESSION["accelog_idorganizacion"];
    $sql = $_SESSION["sequel"];
    $idestiloomision = $_SESSION["iestilo"];
    $descripcion = $_SESSION["desc"];

    $idorg = $_SESSION["accelog_idorganizacion"];
    
    $idreporte = mysql_real_escape_string($_GET["i"]);
    $_SESSION["repolog_idreporte"]=$idreporte;
    
    $sql = "select * from repolog_reportes where idreporte=".$idreporte;
    $result = $conexion->consultar($sql);
    $url_include = "";
	$nombrereporte="";

    $sql = "";
    while($rs=$conexion->siguiente($result)){
        $descripcion = $rs{'descripcion'};
        $sql.=" select ".$rs{'sql_select'};
        $sql.=" from ".$rs{'sql_from'};
        if(($rs{'sql_where'}!=null)&&($rs{'sql_where'}!=""))            $sql.=" where ".$rs{'sql_where'};
        if(($rs{'sql_groupby'}!=null)&&($rs{'sql_groupby'}!=""))    $sql.=" group by ".$rs{'sql_groupby'};
        if(($rs{'sql_having'}!=null)&&($rs{'sql_having'}!=""))          $sql.=" having ".$rs{'sql_having'};
        if(($rs{'sql_orderby'}!=null)&&($rs{'sql_orderby'}!=""))       $sql.=" order by ".$rs{'sql_orderby'};
        $idestiloomision = $rs{"idestiloomision"};
        $url_include = $rs{"url_include"};
        $url_include_despues = $rs{"url_include_despues"};
        $nombrereporte=$rs{"nombrereporte"};
        $subtotales_agrupaciones = $rs{"subtotales_agrupaciones"};
        $subtotales_funciones = $rs{"subtotales_funciones"};
        $subtotales_subtotal = $rs{"subtotales_subtotal"};
    }    
    $conexion->cerrar_consulta($result);

    $_SESSION["url_include"] =$url_include;
    $_SESSION["url_include_despues"] =$url_include_despues;
    $_SESSION["desc"] = $descripcion;
    $_SESSION["sequel"]=$sql;
    $_SESSION["iestilo"] = $idestiloomision;
    $_SESSION["nombrereporte"] = $nombrereporte;
    $_SESSION["subtotales_agrupaciones"] = $subtotales_agrupaciones;
    $_SESSION["subtotales_funciones"] = $subtotales_funciones;   
    $_SESSION["subtotales_subtotal"] = $subtotales_subtotal;   
    $caracter_pregunta = "[";
    if(strrpos($sql,$caracter_pregunta)){
        $url_siguiente = "filtros.php";
    } else {
        $url_siguiente = "reporte.php";
    }


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
