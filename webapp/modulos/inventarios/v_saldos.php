<?php 


include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);

        //Datos Actualizar        
            $entradas=0;
            $salidas=0;
            $saldo=0;
            $entradassecundario=0;
            $salidassecundario=0;
            $saldosecundario=0;
					$fecha=date("Y-m-d g:i:s");
					$doctoorigen=99;
					$foliodoctoorigen=99;        
        //Proceso para Recalcular invetarios_saldos
        include("bd.php");
		//ELimina Datos de Saldos
		$sql="Delete From inventarios_saldos ";
		$conexion->consultar($sql);

echo "Inicia el Proceso <br><br>";
		
		//Consulta Detalle
		$sQuery = "select idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto
				from inventarios_movimientos
				group By idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto
				Order By idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto";
		
		$z=0;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                    $sqlwhere=" and (im.idfabricante=".$rs{"idfabricante"}." and im.idmarca=".$rs{"idmarca"}." and 
                                      im.idbodega=".$rs{"idbodega"}." and im.idproducto=".$rs{"idproducto"}." and 
                                      im.idloteproducto=".$rs{"idloteproducto"}." and im.idestadoproducto=".$rs{"idestadoproducto"}.") ";
                    //Entradas Acumuladas
				
					$sql="Select ifnull(sum(im.cantidad),0) cantidad,ifnull(sum(im.cantidadsecundaria),0) cantidadsecundaria from inventarios_movimientos im 
                                inner join inventarios_tiposmovimiento tim 
                                    on tim.idtipomovimiento=im.idtipomovimiento
                                where tim.efectoinventario=1 $sqlwhere";
                        $result2 = $conexion->consultar($sql);
                        while($rs2 = $conexion->siguiente($result2)){
                            $entradas=$rs2{"cantidad"};
                            $entradassecundario=$rs2{"cantidadsecundaria"};
                        }
                        $conexion->cerrar_consulta($result2);
			//echo $sql."<br>";	 //Imprime Entradas		
                    //Salidas Acumuladas
                    $sql="Select ifnull(sum(im.cantidad),0) cantidad,ifnull(sum(im.cantidadsecundaria),0) cantidadsecundaria from inventarios_movimientos im 
                            inner join inventarios_tiposmovimiento tim 
                                on tim.idtipomovimiento=im.idtipomovimiento
                            where tim.efectoinventario=-1 $sqlwhere";
                        $result2 = $conexion->consultar($sql);
                        while($rs2 = $conexion->siguiente($result2)){
                            $salidas=$rs2{"cantidad"};
                            $salidassecundario=$rs2{"cantidadsecundaria"};
                        }
                        $conexion->cerrar_consulta($result2); 
			//echo $sql."<br>";		//Imprime alidas				
                    //Calcula Saldos
                    $saldo=$entradas-$salidas;
                    $saldosecundario=$entradassecundario-$salidassecundario;
                        
                    //Actualiza inventarios_saldos
                    //$sql="update inventarios_saldos 
                    //        set entradas=$entradas, salidas=$salidas, saldo=$saldo, entradassecundario=$entradassecundario, salidassecundario=$salidassecundario, saldosecundario=$saldosecundario 
                    //     where idsaldo=".$rs{'idsaldo'};
					
                    //Inserto Los Registros 

					
					$sql="Insert Into inventarios_saldos 
                        (idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto, entradas, salidas, saldo, entradassecundario, salidassecundario, saldosecundario, fechaactualizacion, doctoorigen, foliodoctoorigen) Values                              
                        (".$rs{"idfabricante"}.",".$rs{"idmarca"}.",".$rs{"idbodega"}.",".$rs{"idproducto"}.",".$rs{"idloteproducto"}.",".$rs{"idestadoproducto"}.",".$entradas.",".$salidas.",".$saldo.",".$entradassecundario.",
						".$salidassecundario.",".$saldosecundario.",'".$fecha."',".$doctoorigen.",".$foliodoctoorigen.")";
					
					$conexion->consultar($sql);
			//echo $sql."<br><br><br><br>";	//Imprime Insert
					
			$z++;
                    
		}
		$conexion->cerrar_consulta($result);
                
		echo "Proceso Concluido, Se Procesaron $z Registros";
?>