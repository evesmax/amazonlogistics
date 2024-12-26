<?php
include("bd.php"); 
//RECUPERANDO VARIABLES
$idordenentrega = $_REQUEST["idref"];
$idh = $_REQUEST["idh"];


$sqlafecta="Delete From logistica_historialoe where idhistorico=$idh";
//echo $sqlafecta;
$conexion->consultar($sqlafecta);



header("Location: proceso.php?idref=$idordenentrega&tipo=1")
?>