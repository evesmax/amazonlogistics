<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idtraslado=$_GET["idtraslado"];
        
         //LLamar SP
         $sqlsp="Update logistica_traslados set idestadodocumento=4 where idtraslado=$idtraslado;";
         $resultado=$conexion->consultar($sqlsp);
        
         echo '<script>history.back();</script>';
         exit; // Buena práctica para detener el script

?>