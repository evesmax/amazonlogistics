<?php



include("bd.php"); 

//RECUPERANDO VARIABLES
$idref=$_REQUEST["idref"];
$obs = $_REQUEST["txtobs"];
$fecha=$_REQUEST["f1_1"]."-".$_REQUEST["f1_2"]."-".$_REQUEST["f1_3"];

//Si la cancelacion es total entonces edita la orden de entrega
    $sqlafecta="Update logistica_certificados set idestadodocumento=4, fechacancelacion='$fecha', observaciones=concat(observaciones,' $obs') where idcede=$idref";

    $conexion->consultar($sqlafecta);
    $conexion->transaccion("CANCELACION CERTIFICADOS",$sqlafecta);

            
header("Location: ../../netwarelog/repolog/reporte.php")
?>