<?php
include("bd.php"); 
//RECUPERANDO VARIABLES
$idref=$_REQUEST["idref"];
$oe = $_REQUEST["txtoe"];
$oerelacion = $_REQUEST["combooe"];
$obs = $_REQUEST["txtobs"];
$fecha=date("Y-m-d G:i:s");

$sqlafecta="Insert Into logistica_historialoe (fecha,oe,oerelacion,observaciones) Values ('$fecha','$oe','$oerelacion','$obs')";
echo $sqlafecta;
$conexion->consultar($sqlafecta);



header("Location: proceso.php?idref=$idref&tipo=1")
?>