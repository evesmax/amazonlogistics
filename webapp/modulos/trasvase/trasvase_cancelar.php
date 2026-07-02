<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idtrasvase=$_GET["idtrasvase"];
        
    try {
        $conexion->consultar("START TRANSACTION");

        //LLamar SP
        $sqlsp="call cancelarTrasvase($idtrasvase);";
        $resultado=$conexion->consultar($sqlsp);
        if ($resultado === false) {
            throw new Exception("Error al ejecutar la cancelación de trasvase: " . mysql_error());
        }
        
        $conexion->transaccion("CANCELACION TRASVASE: $idtrasvase",$sqlsp);

        $conexion->consultar("COMMIT");

        // mostrar un mensaje antes de redirigir
        echo "Trasvase cancelado correctamente. Redirigiendo...";
        
        // INSTRUCCIÓN PARA REGRESAR A LA PÁGINA ANTERIOR
        echo '<script>window.history.back();</script>';
        exit();

    } catch (Exception $e) {
        $conexion->consultar("ROLLBACK");
        echo "<div style='color:red; font-family:helvetica; font-size:14px; font-weight:bold; margin:20px; text-align:center;'>";
        echo "Error al cancelar el trasvase: " . htmlspecialchars($e->getMessage());
        echo "</div>";
        exit();
    }

        
?>
