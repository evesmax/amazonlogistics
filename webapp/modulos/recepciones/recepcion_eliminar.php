<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idrecepcion=$_GET["idrecepcion"];
        
         /LLamar SP
         $sqlsp="call cancelacion_recepciones($idrecepcion);";
         $resultado=$conexion->consultar($sqlsp);
        
        echo "$sqlsp";
        
?>
