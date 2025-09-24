<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idrecepcion=$_GET["idrecepcion"];
        
         //LLamar SP
         $sqlsp="call cancelacion_recepciones($idrecepcion);";
         $resultado=$conexion->consultar($sqlsp);
        
        echo "$sqlsp <br><br>";

        $conexion->transaccion("CANCELACION RECEPCION: $idrecepcion",$sqlsp);

        //echo "$sqlsp";

    // mostrar un mensaje antes de redirigir
    echo "<br><br> Recepcion cancelado correctamente. Redirigiendo.. $idrecepcion + $sqlsp ";
    
    // INSTRUCCIÓN PARA REGRESAR A LA PÁGINA ANTERIOR
    //echo '<script>window.history.back();</script>';

    
?>
