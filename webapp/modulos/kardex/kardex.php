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
$idproducto=-1;
$idloteproducto=-1;
$idestado=-1;
$idbodega=-1;

$sqlfiltroslike="";
foreach ($filtros as $nombre => $valor) {
    echo "$nombre: $valor\n";
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
}

// Formar filtro para obtener claves
echo "Fecha Inicial: " . $fechainicial . "\n";
echo "Fecha Final: " . $fechafinal . "\n";
echo "idfabricante: ".$idfabricante . "\n";


// ... tu código posterior ...



            exit();
//SQL'S ___

        /*
        $sqlfechacorte=" And (re.fecha<='".$fechacorte." 23:59:59') "; //El movimiento del ultimo segundo
        $sqlclaves="";
        $resultado = $conexion->consultar($sqlclaves);
        while($rs = $conexion->siguiente($resultado)){
            $ingenio=$rs{"idfabricante"};
        
        }
        $conexion->cerrar_consulta($resultado);
        */

        //LLamar SP
        $sqlsp="call generaKardex('2025-01-01 23:59:59','2025-02-16 23:59:59',3,16,NULL,NULL,NULL,NULL,1);";
        $resultado = $conexion->consultar($sqlsp);

        

?>
