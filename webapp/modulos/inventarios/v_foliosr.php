<?php 
		//Este proceso actualiza los saldos y la cantidad retirada de cada OEFC segun movimientos
		include("../../netwarelog/webconfig.php");
		set_time_limit($tiempo_timeout);
		include("bd.php");
        //Datos Actualizar        
		$retirada1=0;
		$retirada2=0;
		$sqlupdate="";
		$i=0;
		//Consulta Detalle
		$sQuery = "select idbodega,doctoorigen, foliodoctoorigen from inventarios_movimientos order by idbodega, foliodoctoorigen";
				

		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
			$bodega=$rs{"idbodega"};
			$foliodoctoorigen=$rs{"idretiro"};
			$doctoorigen=$rs{"doctoorigen"};
				//Consecutivo Interno Bodega 
					$consecutivobodega=-1;
					//Afecta Folio Interno
					$sqlcon="select ifnull(consecutivobodega,0) consecutivobodega from logistica_consecutivosbodega where idbodega=$bodega";
					$result2 = $conexion->consultar($sqlcon);
					while ($rs2 = $conexion->siguiente($result2)) {
						$consecutivobodega = $rs2{"consecutivobodega"};
					}
					$conexion->cerrar_consulta($result2);

					if($consecutivobodega>0){
						$sqlafecta="Update logistica_consecutivosbodega set consecutivobodega=($consecutivobodega+1) where idbodega=$bodega";
						$consecutivobodega++;
					}else{
						$consecutivobodega=1;
						$sqlafecta="Insert Into logistica_consecutivosbodega (idbodega,doctoorigen,consecutivobodega) Values ('$bodega','0',1)";
					}
					$conexion->consultar($sqlafecta);
					echo $sqlafecta."<br>";
				//Actualiza en Documento
				//Produccion
				if($doctoorigen==2){
					$sqlafecta="update produccion_entradas set consecutivobodega=$consecutivobodega where identradasproduccion=$foliodoctoorigen";
				}
				//Envios
				if($doctoorigen==3){
					$sqlafecta="update logistica_envios set consecutivobodega=$consecutivobodega where idenvio=$foliodoctoorigen";
				}
				//Recepciones
				if($doctoorigen==4){
					$sqlafecta="update logistica_recepciones set consecutivobodega=$consecutivobodega where idrecepcion=$foliodoctoorigen";
				}
				//Salidas
				if($doctoorigen==5){
					$sqlafecta="update logistica_retiros set consecutivobodega=$consecutivobodega where idretiro=$foliodoctoorigen";
				}
					$conexion->consultar($sqlafecta);
					echo $sqlafecta."<br>";
		}
		$conexion->cerrar_consulta($result);  
		echo "Proceso Concluido, $i: Registros Afectados";
?>