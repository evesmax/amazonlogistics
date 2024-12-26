<?php 

include("bd.php");

	$sQuery = "Select * From calidad_resultadospruebas Order By idresultadoprueba";
	$todos = $conexion->consultar($sQuery);
	while($rstodos = $conexion->siguiente($todos)){    
	//Procesa Todos los Resultados        
	$datosexcel= "";
	$catalog_id_utilizado=$rstodos{"idresultadoprueba"};
			 
		  include("registroresultados.php"); 
					
	}               
	$conexion->cerrar_consulta($todos);   
               
                

?>