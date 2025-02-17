<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];


//Recupera Filtros
 $sql = $_SESSION["sequel"];
echo "Consulta SQL Original:<pre>" . $sql . "</pre>"; // <--- IMPRIME LA CONSULTA ORIGINAL

$regex = '/WHERE\s+(.*?)(?:\s+GROUP BY|\s+ORDER BY|\s+LIMIT|$)/si';

if (preg_match($regex, $sql, $matches)) {
    echo "Coincidencias de la expresión regular principal:<br>";
    var_dump($matches);

    $condiciones = $matches[1];
    $condiciones_array = preg_split('/(?<!\'[^\\\]*)\bAND\b(?![^\\\]*\')/i', $condiciones);

    $filtros = array();

    foreach ($condiciones_array as $condicion) {
        $condicion = trim($condicion);
        echo "Condición actual: <pre>" . $condicion . "</pre><br>"; // <--- IMPRIME CADA CONDICIÓN INDIVIDUALMENTE

        // Expresión regular para filtros (BETWEEN, LIKE, =, <, >, etc.)
        $regex_filtro = '/([\w\.]+)\s*([<>=!]{0,2}LIKE|BETWEEN|=|<|>|<=|>=|IS NULL|IS NOT NULL)\s*(.*?)(?:\s*OR NOT EXISTS)?$/si';

        if (preg_match($regex_filtro, $condicion, $match_filtro)) {
            echo "Coincidencias de la expresión regular del filtro (normal):<br>";
            var_dump($match_filtro); // Imprime coincidencias
            // ... (código para procesar $match_filtro)
        } else {
            echo "Error en preg_match (normal).<br>"; // Indica error en la expresión normal.
             if (preg_last_error() !== PREG_NO_ERROR) {
                echo "Error de PCRE: " . preg_last_error_msg() . "<br>";
            }

        }

        // Expresión regular para IN
        $regex_in = '/([\w\.]+)\s*IN\s*\((.*?)\)/si';

        if (preg_match($regex_in, $condicion, $match_in)) {
            echo "Coincidencias de la expresión regular del filtro (IN):<br>";
            var_dump($match_in);
            // ... (código para procesar $match_in)
        } else {
            echo "Error en preg_match (IN).<br>"; // Indica error en la expresión IN.
             if (preg_last_error() !== PREG_NO_ERROR) {
                echo "Error de PCRE: " . preg_last_error_msg() . "<br>";
            }
        }
           // Expresión regular para NOT EXISTS
        $regex_not_exists = '/NOT EXISTS\s*\((.*?)\)/si';

        if (preg_match($regex_not_exists, $condicion, $match_not_exists)) {
            echo "Coincidencias de la expresión regular del filtro (NOT EXISTS):<br>";
            var_dump($match_not_exists);
            // ... (código para procesar $match_not_exists)
        } else {
             echo "Error en preg_match (NOT EXISTS).<br>"; // Indica error en la expresión NOT EXISTS.
             if (preg_last_error() !== PREG_NO_ERROR) {
                echo "Error de PCRE: " . preg_last_error_msg() . "<br>";
            }
        }

    } // Fin del bucle foreach

    echo "Array de filtros:<br>";
    print_r($filtros);

} else {
    echo "Error: No se encontró la cláusula WHERE. Consulta original:<pre>" . $sql . "</pre>";
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
