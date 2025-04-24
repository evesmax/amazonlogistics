<?php

ini_set('display_errors', '0');

if (isset($_SESSION['applied_filters']) && is_array($_SESSION['applied_filters'])) {

    echo "<h2>Filtros Aplicados:</h2>";
    echo "<ul>";

    // 3. Recorre el arreglo de filtros
    foreach ($_SESSION['applied_filters'] as $filtro) {
        // Cada $filtro es un arreglo como ['label' => 'Algún Label', 'value' => 'Algún Valor']

        // Verifica si las claves 'label' y 'value' existen en el sub-arreglo
        if (isset($filtro['label']) && isset($filtro['value'])) {
            $etiqueta = htmlspecialchars($filtro['label']); // Escapa para seguridad en HTML
            $valor = htmlspecialchars((string)$filtro['value']); // Convierte a string y escapa

            // Muestra la información
            echo "<li>" . $etiqueta . ": " . $valor . "</li>";

            // Aquí puedes hacer lo que necesites con $etiqueta y $valor
            // Por ejemplo, usarlos en consultas SQL, mostrarlos en diferentes formatos, etc.

        } else {
            // Opcional: Manejar el caso de que un filtro no tenga 'label' o 'value'
            echo "<li>Filtro con formato incorrecto encontrado.</li>";
            // Puedes imprimir el filtro para depurar: print_r($filtro);
        }
    }

    echo "</ul>";

} else {
    echo "<p>No se encontraron filtros aplicados en la sesión o no es un arreglo.</p>";
    // Puedes añadir un var_dump($_SESSION['applied_filters']); aquí para depurar si es necesario
}





include("../../netwarelog/webconfig.php");
include("../../netwarelog/catalog/conexionbd.php");

set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];


//Recupero sql real
$_SESSION["sequel"]=$_SESSION['sql_consulta_original_repologfilters'];
//Recupera Filtros
$sqlAux = $_SESSION["sequel"];
$uw=strpos($sqlAux,'WHERE');
$ct=strlen($sqlAux);
$sqlwhere=substr($sqlAux,$uw);


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
