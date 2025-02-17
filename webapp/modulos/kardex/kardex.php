<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];


//Recupera Filtros
 
$sql = $_SESSION["sequel"];

// Expresión regular para extraer las condiciones del WHERE
$regex = '/WHERE\s+(.*?)\s+ORDER BY/si'; // Modificado para soportar 'OR NOT EXISTS'

// Buscar las coincidencias
if (preg_match($regex, $sql, $matches)) {
    $condiciones = $matches[1];

    // Dividir las condiciones por "AND" (manejar paréntesis y comillas)
    $condiciones_array = preg_split('/(?<!\'[^\\\]*)\bAND\b(?![^\\\]*\')/i', $condiciones); // Mejora para AND

    // Inicializar el array para los filtros
    $filtros = array();

    // Iterar sobre las condiciones y extraer los valores
    foreach ($condiciones_array as $condicion) {
        $condicion = trim($condicion); // Limpiar espacios en blanco

        // Expresión regular para extraer el campo, operador y valor
        $regex_filtro = '/([\w\.]+)\s*([<>=!]{0,2}LIKE|BETWEEN|=|<|>|<=|>=|IS NULL|IS NOT NULL)\s*(.*?)(?:\s*OR NOT EXISTS)?$/si'; // Maneja BETWEEN, LIKE, =, <, >, etc.

        if (preg_match($regex_filtro, $condicion, $match_filtro)) {

            $campo = trim($match_filtro[1]);
            $operador = trim($match_filtro[2]);
            $valor = trim($match_filtro[3]);


            // Quitar comillas y limpiar valores
            $valor = str_replace(array('"', "'"), '', $valor);

            // Agregar el filtro al array
            $filtros[$campo] = array(
                "operador" => $operador,
                "valor" => $valor
            );
        } else if (preg_match('/([\w\.]+)\s*IN\s*\((.*?)\)/si', $condicion, $match_in)) {

            $campo = trim($match_in[1]);
            $operador = "IN";
            $valor = trim($match_in[2]);
            $valor = str_replace(array('"', "'"), '', $valor); // Quitar comillas
             $filtros[$campo] = array(
                "operador" => $operador,
                "valor" => explode(',', $valor)
            );

        }

        else if (preg_match('/NOT EXISTS\s*\((.*?)\)/si', $condicion, $match_not_exists)) {

             $campo = "NOT EXISTS";
            $operador = "NOT EXISTS";
            $valor =  trim($match_not_exists[1]);
             $filtros[$campo] = array(
                "operador" => $operador,
                "valor" => $valor
            );
        }


    }

    // Ahora tienes los filtros en el array $filtros
    echo "<pre>";
    print_r($filtros);
    echo "</pre>";

      // Ejemplo de cómo acceder a los valores:
    if (isset($filtros["ik.fecha"])) {
        $fecha_inicio = $filtros["ik.fecha"]["valor"][0];
        $fecha_fin = $filtros["ik.fecha"]["valor"][1];
        echo "Fecha inicio: " . $fecha_inicio . "<br>";
        echo "Fecha fin: " . $fecha_fin . "<br>";
    }
    if (isset($filtros["of.nombrefabricante"])) {
        $nombrefabricante = $filtros["of.nombrefabricante"]["valor"];
        echo "Nombre fabricante: " . $nombrefabricante . "<br>";

    }
    if (isset($filtros["ip.nombreproducto"])) {
        $nombreproducto = $filtros["ip.nombreproducto"]["valor"];
        echo "Nombre producto: " . $nombreproducto . "<br>";
    }
    if (isset($filtros["il.descripcionlote"])) {
        $descripcionlote = $filtros["il.descripcionlote"]["valor"];
        echo "descripcion lote: " . $descripcionlote . "<br>";
    }
      if (isset($filtros["ie.descripcionestado"])) {
        $descripcionestado = $filtros["ie.descripcionestado"]["valor"];
        echo "descripcion estado: " . $descripcionestado . "<br>";
    }
      if (isset($filtros["ob.nombrebodega"])) {
        $nombrebodega = $filtros["ob.nombrebodega"]["valor"];
        echo "Nombre Bodega: " . $nombrebodega . "<br>";
    }
     if (isset($filtros["ik.idbodega"])) {
        $idbodega = $filtros["ik.idbodega"]["valor"];
        echo "ID Bodega: " . implode(",", $idbodega) . "<br>";
    }
      if (isset($filtros["NOT EXISTS"])) {
        $not_exists = $filtros["NOT EXISTS"]["valor"];
        echo "NOT EXISTS: " . $not_exists . "<br>";
    }

} else {
    echo "No se encontraron condiciones WHERE.";
}

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
