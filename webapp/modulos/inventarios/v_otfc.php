<?php 
        //Este proceso actualiza los saldos y la cantidad retirada de cada OEFC segun movimientos
		include("../../netwarelog/webconfig.php");
		set_time_limit($tiempo_timeout);
		include("bd.php");
                $fecha=date("Y-m-d");
        //Datos Actualizar        
		$retirada1=0;
		$retirada2=0;
                $recibida1=0;
                $recibida2=0;
                
		$sqlupdate="";
		$i=0;
        //Consulta Detalle
        $sQuery="select re.idtraslado, 
                (select ifnull(sum(le.cantidad1),0) from logistica_envios le where re.idtraslado=le.idtraslado and le.idestadodocumento<>4 and 	le.fechaenvio<='$fecha 23:59:59' limit 1) 'cantidadretirada1', 
                (select ifnull(sum(le.cantidad2),0) from logistica_envios le where re.idtraslado=le.idtraslado and le.idestadodocumento<>4 and 	le.fechaenvio<='$fecha 23:59:59' limit 1) 'cantidadretirada2', 
                (select ifnull(sum(lr.cantidadrecibida1),0) from logistica_recepciones lr where re.idtraslado=lr.idtraslado and lr.idestadodocumento<>4 and lr.fecharecepcion<='$fecha 23:59:59' limit 1) 'cantidadrecibida1',
                (select ifnull(sum(lr.cantidadrecibida2),0) from logistica_recepciones lr where re.idtraslado=lr.idtraslado and lr.idestadodocumento<>4 and lr.fecharecepcion<='$fecha 23:59:59' limit 1) 'cantidadrecibida2'
        from logistica_traslados re";
		echo $sQuery;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                                $retirada1=$rs{"cantidadretirada1"};
                                $retirada2=$rs{"cantidadretirada2"};
                                $recibida1=$rs{"cantidadrecibida1"};
                                $recibida2=$rs{"cantidadrecibida2"};

                                $sqlupdate="Update logistica_traslados Set 
                                                    cantidadretirada1=$retirada1, 
                                                    cantidadretirada2=$retirada2,
                                                    cantidadrecibida1=$recibida1,
                                                    cantidadrecibida2=$recibida2
                                        where idtraslado='".$rs{"idtraslado"}."'";
                            echo $sqlupdate."<br>";
                            $conexion->consultar($sqlupdate);
                            $i++;
                            
		}
		$conexion->cerrar_consulta($result);
                
	        
                echo "Proceso Concluido, $i: Registros Afectados";
?>