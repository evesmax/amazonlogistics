<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idtrasvase=$_GET["idtrasvase"];
        
         //LLamar SP
         $sqlsp="call cancelarTrasvase($idtrasvase);";
         $resultado=$conexion->consultar($sqlsp);
        
         $conexion->transaccion("CANCELACION TRASVASE: idtrasvase",$sqlsp);

        //echo "$sqlsp";

    // mostrar un mensaje antes de redirigir
    echo "Trasvase cancelado correctamente. Redirigiendo...";
    
    // INSTRUCCIÓN PARA REGRESAR A LA PÁGINA ANTERIOR
    echo '<script>window.history.back();</script>';
    
    exit();

        
?>
