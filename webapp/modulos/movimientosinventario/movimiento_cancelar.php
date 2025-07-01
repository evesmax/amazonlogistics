<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idmovimiento=$_GET["idmovimientotitulo"];
        
         //LLamar SP
         $sqlsp="call cancelarMovimientoInventario($idmovimiento);";
         $resultado=$conexion->consultar($sqlsp);
        
        echo "$sqlsp";

?>
