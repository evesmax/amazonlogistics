<?php

session_start();
$usuario= $_SESSION["accelog_idempleado"];

include("bd.php"); 
//RECUPERANDO VARIABLES
$idref=$_REQUEST["idref"];
$idordenentrega=$_REQUEST["combooe"];
$obs=$_REQUEST["txtobs"];
$fecha="";
//Obtiene Datos del OE
    $sqlt="select fecha from logistica_ordenesentrega where idordenentrega=$idordenentrega";
    $result = $conexion->consultar($sqlt);
    while($rs = $conexion->siguiente($result)){
            $fecha=$rs{"fecha"};
    }
    $conexion->cerrar_consulta($result);


//Actualiza Faltantes a) Fecha, b)idestadodocumento, c) idorenentrega, d) Observaciones
$sqlafecta="Update logistica_faltantestraslados 
                set idestadodocumento=2, fechaaclaracion='$fecha', idordenentrega='$idordenentrega',observaciones='$obs' where idfaltante=$idref";
//Afecta Database
$conexion->consultar($sqlafecta);
//Graba Transaccion
$conexion->transaccion("ACLARACION FALTANTES: $idref",$sqlafecta);

//Regresa Reporte
header("Location: ../../netwarelog/repolog/reporte.php")

?>