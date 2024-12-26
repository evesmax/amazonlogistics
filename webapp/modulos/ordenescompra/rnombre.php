<?php
include("bd.php");


//Recupera Variables
$idfabricante=$_GET["idfabricante"];
$idmarca=$_GET["idmarca"];
$idproducto=$_GET["idproducto"];
$idlote=$_GET["idlote"];
$idestadoproducto=$_GET["idestadoproducto"];
$idbodega=$_GET["idbodega"];
$volumenorden=str_replace(',','',$_GET["volumenorden"]);
$existencia=0;

include("../inventarios/clases/clinventarios.php");
$inventarios = new clinventarios();
$existencia = $inventarios->regresaexistenciaventas($idfabricante,$idmarca,$idproducto,$idlote,$idestadoproducto,$idbodega,$conexion);
echo "$existencia|$volumenorden| Producto= $idproducto | Lote= $idlote | Estado Producto= $idestadoproducto | bodega= $idbodega |";



?>
