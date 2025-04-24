<?php

ini_set('display_errors', '0');



include("../../netwarelog/webconfig.php");
include("../../netwarelog/catalog/conexionbd.php");

set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];

$sqlAux = "inventarios_kardex where idempleado=".$usuario;
$resultado = $conexion->consultar($sqlAux);
//echo $sqlAux;

//Recupera Filtros
$_SESSION["sequel"]=$_SESSION['sql_consulta_original_repologfilters'];
$sqlAux = $_SESSION["sequel"];
$uw=strpos($sqlAux,'where');
$ct=strlen($sqlAux);
$sqlwhere=substr($sqlAux,$uw);

echo $sqlwhere."<br>".$sqlAux."<br>";

// Expresión regular para extraer fechainicial y fechafinal
if (preg_match('/ik\.fecha between "([^"]+)" And "([^"]+)"/', $sqlwhere, $matches)) {
    $fechainicial = $matches[1]; // 2025-02-01 00:00:00
    $fechafinal = $matches[2];   // 2025-02-17 23:59:59
} else {
    $fechainicial = null;
    $fechafinal = null;
}

// Expresión regular para extraer otros filtros
$filtros = [];
if (preg_match_all('/and (\w+\.\w+) like "([^"]*)"/', $sqlwhere, $matches)) {
    foreach ($matches[1] as $index => $nombreFiltro) {
        $filtros[$nombreFiltro] = $matches[2][$index];
    }
}

$idfabricante="NULL";
$idmarca="NULL";
$idproducto="NULL";
$idloteproducto="NULL";
$idestado="NULL";
$idbodega="NULL";

$sqlfiltroslike="";
foreach ($filtros as $nombre => $valor) {
    //echo $nombre."=".$valor. "<br>";
    $sqlAux="";
    if ($nombre=="of.nombrefabricante") {
        if ($valor<>"%%"){
            $sqlAux="Select idfabricante id from operaciones_fabricantes where nombrefabricante like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sqlAux);
            while($rs = $conexion->siguiente($resultado)){
                $idfabricante=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre=="vm.nombremarca") {
        if ($valor<>"%%"){
            $sqlAux="Select idmarca id from vista_marcas where nombremarca like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sqlAux);
            while($rs = $conexion->siguiente($resultado)){
                $idmarca=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre=="ip.nombreproducto") {
        if ($valor<>"%%"){
            $sqlAux="select idproducto id from inventarios_productos where nombreproducto like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sqlAux);
            while($rs = $conexion->siguiente($resultado)){
                $idproducto=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre=="il.descripcionlote") {
        if ($valor<>"%%"){
            $sqlAux="select idloteproducto id from inventarios_lotes where descripcionlote like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sqlAux);
            while($rs = $conexion->siguiente($resultado)){
                $idloteproducto=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre=="ie.descripcionestado") {
        if ($valor<>"%%"){
            $sqlAux="select idestadoproducto id from inventarios_estados where descripcionestado like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sqlAux);
            while($rs = $conexion->siguiente($resultado)){
                $idestado=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }    
    if ($nombre=="ob.nombrebodega") {
        if ($valor<>"%%"){
            $sqlAux="select idbodega id from operaciones_bodegas where nombrebodega like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sqlAux);
            while($rs = $conexion->siguiente($resultado)){
                $idbodega=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }    
    //echo $sqlAux."<br>";  
}
        echo "<br>".$fechainicial." ".$fechafinal." ".$idfabricante." ".$idmarca." ".$idproducto." ".$idloteproducto." ".$idestado." ".$idbodega."<br>";
        //LLamar SP
        $sqlsp="call generaKardex('$fechainicial','$fechafinal',$idfabricante,$idmarca,$idbodega,$idproducto,$idloteproducto,$idestado,$usuario);";
        $resultado=$conexion->consultar($sqlsp);
        //echo $sqlsp;


?>
