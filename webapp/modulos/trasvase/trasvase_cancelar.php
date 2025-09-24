<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idtrasvase=$_GET["idtrasvase"];
        
         //LLamar SP
         $sqlsp="call cancelarTrasvase($idtrasvase);";
         $resultado=$conexion->consultar($sqlsp);
        
        echo "$sqlsp";
        
?>
