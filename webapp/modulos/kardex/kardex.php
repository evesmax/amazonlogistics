<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];


//Recupera Filtros
$sql = $_SESSION["sequel"];
$uw=strpos($sql,'where');
$ct=strlen($sql);
$sqlwhere=substr($sql,$uw);

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



$idfabricante=-1;
$idmarca=-1;
$idproducto=-1;
$idloteproducto=-1;
$idestado=-1;
$idbodega=-1;

$sqlfiltroslike="";
foreach ($filtros as $nombre => $valor) {
    if ($nombre="of.nombrefabricante") {
        if ($valor<>"%%"){
            $sql="Select idfabricante id from operaciones_fabricantes where nombrefabricante like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sql);
            while($rs = $conexion->siguiente($resultado)){
                $idfabricante=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre="vm.nombremarca") {
        if ($valor<>"%%"){
            $sql="Select idmarca id from vista_marcas where nombremarca like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sql);
            while($rs = $conexion->siguiente($resultado)){
                $idmarca=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre="ip.nombreproducto") {
        if ($valor<>"%%"){
            $sql="select idproducto id from inventarios_productos where nombreproducto like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sql);
            while($rs = $conexion->siguiente($resultado)){
                $idproducto=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre="il.descripcionlote") {
        if ($valor<>"%%"){
            $sql="select idloteproducto id from inventarios_lotes where descripcionlote like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sql);
            while($rs = $conexion->siguiente($resultado)){
                $idloteproducto=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }
    if ($nombre="ie.descripcionestado") {
        if ($valor<>"%%"){
            $sql="select idestadoproducto id from inventarios_estados where descripcionestado like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sql);
            while($rs = $conexion->siguiente($resultado)){
                $idestado=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }    
    if ($nombre="ob.nombrebodega") {
        if ($valor<>"%%"){
            $sql="select idbodega id from operaciones_bodegas where nombrebodega like '".$valor."' limit 1";
            $resultado = $conexion->consultar($sql);
            while($rs = $conexion->siguiente($resultado)){
                $idbodega=$rs{"id"};
            }
            $conexion->cerrar_consulta($resultado);
        }
    }      
}

// Formar filtro para obtener claves
echo "Fecha Inicial: " . $fechainicial . "\n";
echo "Fecha Final: " . $fechafinal . "\n";
echo "idfabricante: ". $idfabricante . "\n";
echo "idloteproducto: ". $idloteproducto . "\n";
echo "idproducto: ". $idproducto . "\n";
echo "idestado: ". $idestado . "\n";
echo "idbodega: ". $idbodega . "\n";

// ... tu código posterior ...



            exit();
//SQL'S ___
        $sql="DELETE FROM inventarios_kardex WHERE idempleado = $usuario;"; 
        $resultado = $conexion->consultar($sql);

        //LLamar SP
        $sqlsp="call generaKardex($fechainicial,$fechafinal,$idfabricante,$idmarca,$idbodega,$idproducto,$idloteproducto,$idestadoproducto,$usuario);";
        $resultado = $conexion->consultar($sqlsp);

        

?>
