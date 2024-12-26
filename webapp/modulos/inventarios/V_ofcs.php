<?php 

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);

echo "Inicia Proceso...<br><br>";

		//Este proceso actualiza los saldos y la cantidad retirada de cada OEFC segun movimientos
		include("bd.php");
        //Datos Actualizar        
		$retirada1=0;
		$retirada2=0;
		$sqlupdate="";
		$i=0;
		//Consulta Detalle
		$sQuery = "select re.idordenentrega,re.referencia1,ifnull(re.cantidad1,0) cantidad1,ifnull(re.cantidad2,0) cantidad2,
						(select ifnull(sum(lor.cantidad1),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<=now() limit 1) 'retirada1',
						(select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<=now() limit 1) 'retirada2'
					from logistica_ordenesentrega re
					Group By re.idordenentrega,re.referencia1";
				
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
					$retirada1=$rs{"retirada1"};
					$retirada2=$rs{"retirada2"};
					$cantidad1=$rs{"cantidad1"};
					$cantidad2=$rs{"cantidad2"};
					$saldo1=$cantidad1-$retirada1;
					$saldo2=$cantidad2-$retirada2;
                    $sqlupdate="Update logistica_ordenesentrega 
						set cantidadretirada1=$retirada1, cantidadretirada2=$retirada2,
							saldo1=$saldo1,
							saldo2=$saldo2
						where idordenentrega='".$rs{"idordenentrega"}."'";
				    //echo $sqlupdate."<br>";
					$i++;
					$conexion->consultar($sqlupdate);
		}
		$conexion->cerrar_consulta($result);
	        
		echo "Proceso Concluido, $i: Registros Afectados";
?>