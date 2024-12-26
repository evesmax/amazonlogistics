<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);


    //Elimina Datos Anteriores si Existen
        $elimina="delete from cierre_movimientos where idcierre=$catalog_id_utilizado";
        $conexion->consultar($elimina);
        $elimina="delete from cierre_saldos where idcierre=$catalog_id_utilizado";
        $conexion->consultar($elimina);
        
        
    //Obtiene datos de tiempo del cierre
            $fecha1="";
            $fecha2="";
            $sdel="";
            $sal="";
            $sqld="SELECT fechainicial, fechafinal FROM cierre WHERE idcierre=$catalog_id_utilizado";
            $resultado = $conexion->consultar($sqld);
                while($rs = $conexion->siguiente($resultado)){
                    $sdel=$rs{"fechainicial"};
                    $sal=$rs{"fechafinal"};
                }
            $conexion->cerrar_consulta($resultado);
            
            $fecha1 = new DateTime($sdel);
            $del = $fecha1->format('Y-m-d');
            
            $fecha2 = new DateTime($sal);
            $al = $fecha2->format('Y-m-d');
        
    //Declaracion de Arreglos
        $em=1;
        $es=1;
        //cierre_movimientos
        $cmov=array("idcierre", "idfabricante", "idmarca", "idbodega", "idloteproducto", 
                    "idproducto", "idestadoproducto", "idtipomovimiento", "cantidad");
        //cierre_saldos
        $csaldos=array("idcierre", "idfabricante", "idmarca", "idbodega", "idloteproducto", 
                        "idproducto", "idestadoproducto", "fisicoinicial", "fisicofinal", 
                        "certificados", "comprometida", "disponible","transito");

    //Llena Movimientos
     //Llena Movimientos
            $sqld="SELECT im.idfabricante, idmarca,  idbodega, idloteproducto, idproducto, idestadoproducto,
                        idtipomovimiento, sum(im.cantidadsecundaria) cantidad FROM inventarios_movimientos im 
                    WHERE im.fecha BETWEEN '$del 00:00:00' AND '$al 23:59:59'
                    GROUP BY im.idfabricante, idmarca,  idbodega, idloteproducto, idproducto, idestadoproducto,
                        idtipomovimiento
                    ORDER BY im.idfabricante, idmarca,  idbodega, idloteproducto, idproducto, idestadoproducto,
                        idtipomovimiento";
            //echo $sqld;
            
            $resultado = $conexion->consultar($sqld);
                while($rs = $conexion->siguiente($resultado)){
                    $cmov["idcierre"][$em]=$catalog_id_utilizado;
                    $cmov["idfabricante"][$em]=$rs{"idfabricante"};
                    $cmov["idmarca"][$em]=$rs{"idmarca"};
                    $cmov["idbodega"][$em]=$rs{"idbodega"};
                    $cmov["idloteproducto"][$em]=$rs{"idloteproducto"};
                    $cmov["idproducto"][$em]=$rs{"idproducto"};
                    $cmov["idestadoproducto"][$em]=$rs{"idestadoproducto"};
                    $cmov["idtipomovimiento"][$em]=$rs{"idtipomovimiento"};
                    $cmov["cantidad"][$em]=$rs{"cantidad"};
                    $em++;
                }
            $conexion->cerrar_consulta($resultado);

    //Llena Saldos
            $sqld="Select im.idfabricante, im.idmarca, im.idbodega, im.idloteproducto, im.idproducto, im.idestadoproducto, 
                        sum(im.cantidadsecundaria*tm.efectoinventario) 'fisicofinal',

                        ifnull((SELECT sum(cantidad2) cedestm FROM logistica_certificados ce
                        WHERE (ce.idfabricante=im.idfabricante AND ce.idmarca=im.idmarca AND ce.idbodega=im.idbodega
                        AND ce.idloteproducto=im.idloteproducto AND ce.idproducto=im.idproducto 
                        AND ce.idestadoproducto=im.idestadoproducto) 
                        AND ce.fechaoperacion<='$al 23:59:59' 
                        AND (ce.fechacancelacion>='$al 23:59:59' OR ce.fechacancelacion IS NULL)),0) 'certificados', 

                        0 'comprometida',
						
						ifnull((SELECT sum(lo.cantidad2) 
                        FROM logistica_ordenesentrega lo 
                        WHERE (lo.idfabricante=im.idfabricante AND lo.idmarca=im.idmarca AND lo.idbodega=im.idbodega
                        AND lo.idloteproducto=im.idloteproducto AND lo.idproducto=im.idproducto 
                        AND lo.idestadoproducto=im.idestadoproducto) 
                        AND (lo.fecha>='$del 00:00:00' and lo.fecha<='$al 23:59:59')
                        ),0) 'ventas',
            
						ifnull((select sum(coe.cantidad2) from logistica_cancelacionordenesentrega coe
						left join logistica_ordenesentrega oe on coe.idordenentrega=oe.idordenentrega
						where coe.idestadodocumento=1 and coe.fechacancelacion between '$del 00:00:00' and '$al 23:59:59'
						AND oe.idfabricante=im.idfabricante AND oe.idmarca=im.idmarca AND oe.idbodega=im.idbodega 
                        AND oe.idloteproducto=im.idloteproducto AND oe.idproducto=im.idproducto AND oe.idestadoproducto=im.idestadoproducto
						),0) ventascanceladas,
					

                        ifnull((SELECT sum(imi.cantidadsecundaria*tmi.efectoinventario) 
                        FROM inventarios_movimientos imi 
							left join inventarios_tiposmovimiento tmi on tmi.idtipomovimiento=imi.idtipomovimiento
                        WHERE imi.fecha <='$del 00:00:00'
                        AND im.idfabricante=imi.idfabricante AND im.idmarca=imi.idmarca AND im.idbodega=imi.idbodega 
                        AND im.idloteproducto=imi.idloteproducto AND im.idproducto=imi.idproducto AND im.idestadoproducto=imi.idestadoproducto),0) 
                        'fisicoinicial'

                    FROM inventarios_movimientos im
                        left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento
                    WHERE im.fecha <='$al 23:59:59'
                    Group by im.idfabricante, im.idmarca, im.idbodega, im.idloteproducto, im.idproducto, im.idestadoproducto";
            
			//echo $sqld;
			
            $resultado = $conexion->consultar($sqld);
                while($rs = $conexion->siguiente($resultado)){
                    $csaldos["idcierre"][$es]=$catalog_id_utilizado;
                    $csaldos["idfabricante"][$es]=$rs{"idfabricante"};
                    $csaldos["idmarca"][$es]=$rs{"idmarca"};
                    $csaldos["idbodega"][$es]=$rs{"idbodega"};
                    $csaldos["idloteproducto"][$es]=$rs{"idloteproducto"};
                    $csaldos["idproducto"][$es]=$rs{"idproducto"};
                    $csaldos["idestadoproducto"][$es]=$rs{"idestadoproducto"};
                    $csaldos["fisicoinicial"][$es]=$rs{"fisicoinicial"};
                    $csaldos["fisicofinal"][$es]=$rs{"fisicofinal"};
                    $csaldos["certificados"][$es]=$rs{"certificados"};
                    $csaldos["comprometida"][$es]=$rs{"comprometida"};
                    $csaldos["disponible"][$es]=0; //$csaldos["fisicofinal"][$es]-($csaldos["certificados"][$es]+$csaldos["comprometida"][$es]);
                    $csaldos["ventas"][$es]=($rs{"ventas"})-$rs{"ventascanceladas"};
					$csaldos["transito"][$es]=0;
					$es++;
                }
            $conexion->cerrar_consulta($resultado);
 
  
    //Agregando Transitos        
            
        $csaldos=agregatransitos($al,$csaldos,$es,$conexion);
        $csaldos=agregacomprometidas($al,$csaldos,$es,$conexion);    
            
    //Inserta Datos
        $coma="";
        $values="";
        //Movimientos
            $sqlinsert="Insert Into cierre_movimientos 
                            (idcierre, idfabricante, idmarca, idbodega, idloteproducto, 
                            idproducto, idestadoproducto, idtipomovimiento, cantidad) Values ";
            for ($i = 1; $i <= $em-1; $i++) {
                $values.="$coma (
                            '".$cmov["idcierre"][$i]."',
                            '".$cmov["idfabricante"][$i]."',
                            '".$cmov["idmarca"][$i]."',
                            '".$cmov["idbodega"][$i]."',
                            '".$cmov["idloteproducto"][$i]."',    
                            '".$cmov["idproducto"][$i]."',
                            '".$cmov["idestadoproducto"][$i]."',
                            '".$cmov["idtipomovimiento"][$i]."',
                            '".$cmov["cantidad"][$i]."'
                           ) "; 
                $coma=",";
            }
            
        //echo "<br> $sqlinsert $values";    
        $conexion->consultar("$sqlinsert $values");   
            
        //Saldos
		
			//Recalcula Disponible
			for ($i = 1; $i <= $es-1; $i++) {
					$csaldos["disponible"][$i]=($csaldos["fisicofinal"][$i])-($csaldos["certificados"][$i]+$csaldos["comprometida"][$i]);
			}
		
            $coma="";
            $values="";
            $sqlinsert="Insert Into cierre_saldos 
                            (idcierre, idfabricante, idmarca, idbodega, idloteproducto, 
                            idproducto, idestadoproducto, fisicoinicial, fisicofinal, certificados, 
                            comprometida, disponible, ventas, transito) Values ";
            for ($i = 1; $i <= $es-1; $i++) {
                $values.="$coma (
                            '".$csaldos["idcierre"][$i]."',
                            '".$csaldos["idfabricante"][$i]."',
                            '".$csaldos["idmarca"][$i]."',
                            '".$csaldos["idbodega"][$i]."',
                            '".$csaldos["idloteproducto"][$i]."',    
                            '".$csaldos["idproducto"][$i]."',
                            '".$csaldos["idestadoproducto"][$i]."',
                            '".$csaldos["fisicoinicial"][$i]."',
                            '".$csaldos["fisicofinal"][$i]."',
                            '".$csaldos["certificados"][$i]."',
                            '".$csaldos["comprometida"][$i]."',
                            '".$csaldos["disponible"][$i]."',
							'".$csaldos["ventas"][$i]."',
								'".$csaldos["transito"][$i]."'
                           ) "; 
                $coma=",";
            }
            
        //echo "<br> $sqlinsert $values";    
        $conexion->consultar("$sqlinsert $values");

        echo "<br><font color=blue><b>Cierre Concluido.</b></font>";
        

		
		
        //Funciones
        
        function agregatransitos($fechacorte,$arreglo,$elementos,$conexion){
                //Obtener En Transito
                
                $sqlfaltantes="";
                $sqlenvios="";
                $sqldevoluciones="";
                $sqlrecepcion="";
                
                $sqlfaltantes="ifnull((SELECT ifnull(sum(cantfalt2),0) cantidad FROM logistica_faltantestraslados a 
                                WHERE idrecepcion IN (SELECT idrecepcion FROM logistica_recepciones WHERE idtraslado=re.idtraslado)
                                AND fechaaclaracion <='$fechacorte 23:59:59' AND idestadodocumento=2),0)";
								
                $sqlenvios="ifnull((select ifnull(sum(loe.cantidad2),0) from logistica_envios loe 
                                    where re.idtraslado=loe.idtraslado and loe.fechaenvio<='$fechacorte 23:59:59' 
                                        and loe.idestadodocumento<>4 limit 1),0)";
                
                $sqldevoluciones="ifnull((select ifnull(sum(dr.cantidadrecibida2),0) from logistica_devoluciones dr 
                                    where dr.idtraslado=re.idtraslado and dr.idestadodocumento<>4 
                                        and dr.fecharecepcion<='$fechacorte 23:59:59'),0)";
                
                $sqlrecepcion="ifnull((select ifnull(sum(lor.cantidadrecibida2),0) from logistica_recepciones lor 
								where lor.idtraslado=re.idtraslado and lor.fecharecepcion<='$fechacorte 23:59:59' 
								and lor.idestadodocumento<>4 limit 1),0) ";

                $sqlre="Select re.idtraslado,re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodegadestino idbodega,
                            ($sqlenvios-($sqlrecepcion+$sqldevoluciones+$sqlfaltantes)) 'transito'							
						From logistica_traslados re 
							left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
							left join vista_marcas vm on vm.idmarca=re.idfabricante						
							left join inventarios_productos ip on ip.idproducto=re.idproducto 
							left join  inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto 
							left join inventarios_lotes il on il.idloteproducto=re.idloteproducto 
							left join operaciones_transportistas ot on ot.idtransportista=re.idtransportista 
							left join operaciones_bodegas ob on ob.idbodega=re.idbodegadestino
							left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia
							left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
							left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 						
                        Where re.fecha<='$fechacorte 23:59:59' 
                        Group By re.idtraslado,re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodegadestino";
                //echo $sqlre;
	
                $transito=0;
                $resultado = $conexion->consultar($sqlre);
                while($rs = $conexion->siguiente($resultado)){
                    $idfabricante=$rs{"idfabricante"};
                    $idmarca=$rs{"idmarca"};
                    $idproducto=$rs{"idproducto"};
                    $idestadoproducto=$rs{"idestadoproducto"};
                    $idloteproducto=$rs{"idloteproducto"};
                    $idbodega=$rs{"idbodega"};
                    $transito=$rs{"transito"};
                    //Agregando Transito
                    for ($ibm = 1; $ibm <= $elementos-1; $ibm++) {
			if(
				$arreglo["idfabricante"][$ibm]==$idfabricante 
                                && $arreglo["idmarca"][$ibm]==$idmarca 
                                && $arreglo["idproducto"][$ibm]==$idproducto 
                                && $arreglo["idestadoproducto"][$ibm]==$idestadoproducto 
                                && $arreglo["idloteproducto"][$ibm]==$idloteproducto 
                                && $arreglo["idbodega"][$ibm]==$idbodega){
                            $arreglo["transito"][$ibm]=$arreglo["transito"][$ibm]+$transito;
                            break;
                        } 
                    }    
                }        
                $conexion->cerrar_consulta($resultado);
                
                return $arreglo;
            
        }
        		
        function agregacomprometidas($fechacorte,$arreglo,$elementos,$conexion){
                //Obtener Comprometida
                
            $sqlcan="(select ifnull(sum(c.cantidad2),0) from logistica_cancelacionordenesentrega c 
                        where c.fechacancelacion<='$fechacorte 23:59:59' and c.idordenentrega=re.idordenentrega and c.idestadodocumento=1)";
            
            $sqloe="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega,
                        sum(re.cantidad2)-
                        (select ifnull(sum(lor.cantidad2),0) 
                            from logistica_retiros lor 
                                where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1  
                                    and lor.fechasalida<='$fechacorte 23:59:59' limit 1)-$sqlcan 'saldo2'
                    from logistica_ordenesentrega re 
                                left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                                left join vista_marcas vm on vm.idmarca=re.idmarca
                                left join operaciones_bodegas ob on ob.idbodega=re.idbodega
                                left join inventarios_productos ip on ip.idproducto=re.idproducto
                                left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                                left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                                left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia                    
					Where re.fecha<='$fechacorte 23:59:59'
                        And (re.fechacancelacion>='$fechacorte 23:59:59' or re.fechacancelacion is null)
                        group by re.idordenentrega, re.fechacancelacion, re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto,re.idbodega";
                //echo $sqloe;
				
	
                $comprmetida=0;
                $resultado = $conexion->consultar($sqloe);
                while($rs = $conexion->siguiente($resultado)){
                    $idfabricante=$rs{"idfabricante"};
                    $idmarca=$rs{"idmarca"};
                    $idproducto=$rs{"idproducto"};
                    $idestadoproducto=$rs{"idestadoproducto"};
                    $idloteproducto=$rs{"idloteproducto"};
                    $idbodega=$rs{"idbodega"};
                    $comprometida=$rs{"saldo2"};
                    //Agregando Comprometida
                    for ($ibm = 1; $ibm <= $elementos-1; $ibm++) {
			if(
				$arreglo["idfabricante"][$ibm]==$idfabricante 
                                && $arreglo["idmarca"][$ibm]==$idmarca 
                                && $arreglo["idproducto"][$ibm]==$idproducto 
                                && $arreglo["idestadoproducto"][$ibm]==$idestadoproducto 
                                && $arreglo["idloteproducto"][$ibm]==$idloteproducto 
                                && $arreglo["idbodega"][$ibm]==$idbodega){
                            $arreglo["comprometida"][$ibm]=$arreglo["comprometida"][$ibm]+$comprometida;
                            break;
                        } 
                    }    
                }        
                $conexion->cerrar_consulta($resultado);
                
                return $arreglo;
            
        }		
		
?>
