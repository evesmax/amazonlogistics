<?php 
		//Este proceso actualiza los saldos y la cantidad retirada de cada OEFC segun movimientos
		include("bd.php");
        //Datos Actualizar        
		$fecha=0;
		$oe=0;
		$obs="";
                $sqlinsert="Insert Into logistica_historialoe (fecha,oe,oerelacion,observaciones) ";
                $sqlvalores="";
                $coma="";
		$i=0;
		//Consulta Detalle
		$sQuery = "Select fecha,referencia1 oe,referencia1 oerelacion, concat('Creación del OE: ',referencia1) observaciones  
                            from logistica_ordenesentrega 
                            Where not referencia1 in (select oe from logistica_historialoe)";
				
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
					$fecha=$rs{"fecha"};
					$oe=$rs{"oe"};
					$obs=$rs{"observaciones"};

                        $sqlvalores.="$coma ('$fecha','$oe','$oe','$obs')";	
                        $i++;
                        $coma=",";
		}
		$conexion->cerrar_consulta($result);
	        
                
                $sqlinsert.=" values $sqlvalores";
                //echo $sqlinsert;
                $conexion->consultar($sqlinsert);
                
		echo "Proceso Concluido, $i: Registros Afectados";
?>