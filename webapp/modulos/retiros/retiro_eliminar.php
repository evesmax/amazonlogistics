<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idretiro=$_GET["idretiro"];
        
         //LLamar SP
         $sqlsp="call cancelacion_retiros($idretiro);";
         $resultado=$conexion->consultar($sqlsp);
        
        echo "$sqlsp";
               
         $conexion->transaccion("CANCELACION RETIRO: $idretiro",$sqlsp);

        //echo "$sqlsp";

    // mostrar un mensaje antes de redirigir
    echo "Retiro cancelado correctamente. Redirigiendo...";
    
    // INSTRUCCIÓN PARA REGRESAR A LA PÁGINA ANTERIOR
    echo '<script>window.history.back();</script>';
    
    exit();
 
?>
