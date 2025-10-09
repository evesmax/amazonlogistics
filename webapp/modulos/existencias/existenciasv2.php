<?php
ini_set('display_errors', '0');

include("../../netwarelog/webconfig.php");
include("../../netwarelog/catalog/conexionbd.php");

set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];

$fechainicial = null;
$fechafinal = null;
$idfabricante="NULL";
$idmarca="NULL";
$idproducto="NULL";
$idloteproducto="NULL";
$idestado="NULL";
$idbodega="NULL";

echo $_SESSION['applied_filters'];
exit();

if (isset($_SESSION['applied_filters']) && is_array($_SESSION['applied_filters'])) {

    // 3. Recorre el arreglo de filtros
    foreach ($_SESSION['applied_filters'] as $filtro) {

        if (isset($filtro['label']) && isset($filtro['value'])) {
            $etiqueta = htmlspecialchars($filtro['label']); // Escapa para seguridad en HTML
            $valor = htmlspecialchars((string)$filtro['value']); // Convierte a string y escapa

            // Muestra la información
            //echo "<li>" . $etiqueta . ": " . $valor . "</li>";
            if ($etiqueta == "Del") {
                $fechainicial = $valor." 00:00:00"; // 2025-02-01 00:00:00
            }
            if ($etiqueta == "Al") {
                $fechafinal = $valor." 23:59:59"; // 2025-02-01 00:00:00
            }

            if ($etiqueta == "Propietario") {
                $sqlAux="Select idfabricante id from operaciones_fabricantes where nombrefabricante like '%".$valor."%' limit 1";
                //echo $sqlAux."<br>";
                $resultado = $conexion->consultar($sqlAux);
                while($rs = $conexion->siguiente($resultado)){
                    $idfabricante=$rs{"id"};
                }
                $conexion->cerrar_consulta($resultado);
            }
            
            if ($etiqueta == "Marca") {
                $sqlAux="Select idmarca id from vista_marcas where nombremarca like '%".$valor."%' limit 1";
                $resultado = $conexion->consultar($sqlAux);
                while($rs = $conexion->siguiente($resultado)){
                    $idmarca=$rs{"id"};
                }
                $conexion->cerrar_consulta($resultado);            
            }  

            if ($etiqueta == "Producto") {

                    $sqlAux="select idproducto id from inventarios_productos where nombreproducto like '%".$valor."%' limit 1";
                    $resultado = $conexion->consultar($sqlAux);
                    while($rs = $conexion->siguiente($resultado)){
                        $idproducto=$rs{"id"};
                    }
                    $conexion->cerrar_consulta($resultado);

            }
            if ($etiqueta=="Zafra") {

                    $sqlAux="select idloteproducto id from inventarios_lotes where descripcionlote like '%".$valor."%' limit 1";
                    $resultado = $conexion->consultar($sqlAux);
                    while($rs = $conexion->siguiente($resultado)){
                        $idloteproducto=$rs{"id"};
                    }
                    $conexion->cerrar_consulta($resultado);

            }
            if ($etiqueta=="Estado Producto") {

                    $sqlAux="select idestadoproducto id from inventarios_estados where descripcionestado like '%".$valor."%' limit 1";
                    $resultado = $conexion->consultar($sqlAux);
                    while($rs = $conexion->siguiente($resultado)){
                        $idestado=$rs{"id"};
                    }
                    $conexion->cerrar_consulta($resultado);

            }    
            if ($etiqueta=="Nombre Bodega") {
                    $sqlAux="select idbodega id from operaciones_bodegas where nombrebodega like '%".$valor."%' limit 1";
                    $resultado = $conexion->consultar($sqlAux);
                    while($rs = $conexion->siguiente($resultado)){
                        $idbodega=$rs{"id"};
                    }
                    $conexion->cerrar_consulta($resultado);

            }    

        } else {
            // Opcional: Manejar el caso de que un filtro no tenga 'label' o 'value'
            echo "<li>Filtro con formato incorrecto encontrado.</li>";
            // Puedes imprimir el filtro para depurar: print_r($filtro);
        }
    }

} else {
    echo "<p>No se encontraron filtros aplicados en la sesión o no es un arreglo.</p>";
    // Puedes añadir un var_dump($_SESSION['applied_filters']); aquí para depurar si es necesario
}



    //echo $sqlAux."<br>";  
        //Elimina Existencias 
        $sqldelete="delete from inventarios_existencias where idempleado=".$usuario;
        $resultado=$conexion->consultar($sqldelete); 
        //echo "<br>".$fechainicial." ".$fechafinal." ".$idfabricante." ".$idmarca." ".$idproducto." ".$idloteproducto." ".$idestado." ".$idbodega."<br>";
        //LLamar SP
        $sqlsp="call generaExistenciasInventario('$fechafinal',$idfabricante,$idmarca,$idbodega,$idproducto,$idloteproducto,$idestado,$usuario);";
        $resultado=$conexion->consultar($sqlsp);
        //echo $sqlsp;


?>
