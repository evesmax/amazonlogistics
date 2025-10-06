<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
        $idrecepcion=$_GET["idrecepcion"];
        
        //LLamar SP
        $sqlsp="call cancelacion_recepciones($idrecepcion);";
        $resultado=$conexion->consultar($sqlsp);
    
        $conexion->transaccion("CANCELACION RECEPCION: $idrecepcion",$sqlsp);

-------
//include("netwarelog/catalog/conexionbd.php");
//$sqlsp="call prueba_float();";
//$idfolio=1;   
         //LLamar SP
         //$sqlsp="call cancelarTrasvase($idfolio);";
         //$resultado=$conexion->consultar($sqlsp);
         //$conexion->transaccion("CANCELACION Folio: $idfolio",$sqlsp);
        
    // mostrar un mensaje antes de redirigir
    echo "<br><br> Recepcion cancelado correctamente. Redirigiendo.. $idrecepcion + $sqlsp ";
    
    // INSTRUCCIÓN PARA REGRESAR A LA PÁGINA ANTERIOR
    echo '<script>window.history.back();</script>';


?>
