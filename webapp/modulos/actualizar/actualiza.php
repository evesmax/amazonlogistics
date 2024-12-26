<?php 

		//include("bd.php");

        $datosexcel= "";
        $idref=$catalog_id_utilizado;
        
		
		//Elimina los registros anteriores
		$sqldelete="Delete From reporte_estadisticaventas where idref=".$idref;
		echo $sqldelete;
$conexion->consultar($sqldelete);
		
		
		$sQuery = "Select * From actualizar_datos where idactualizar=".$idref;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
				$datosexcel=$rs{"ssql"};
				$datosexcel=str_replace("|","'",$datosexcel);
                //Agrega los nuevos resultados
			echo $datosexcel;
$conexion->consultar($datosexcel);
		}
		$conexion->cerrar_consulta($result);
				
		$sqlupdate="Update reporte_estadisticaventas set idref=$idref where idref=-1";
		echo $sqlupdate;
$conexion->consultar($sqlupdate);

                
                
                
                

?>