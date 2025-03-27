<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idretiro=$_GET["idretiro"];
        
         //LLamar SP
         $sqlsp="call cancelacion_retiros($idretiro);";
         $resultado=$conexion->consultar($sqlsp);
        
        echo "$sqlsp";
        
?>
