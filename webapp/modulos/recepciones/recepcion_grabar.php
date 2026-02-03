<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
       
//RECUPERANDO VARIABLES PARA GRABAR
            $idtraslado=$_REQUEST["txtidtraslado"];
            $idenvio=$_REQUEST["txtidenvio"];
            $idrecepcion=0;
            $fecharecepcion=$_REQUEST["txtfecharec"];
            $banco=$_REQUEST["txtbanco"];
            $estiba=$_REQUEST["txtestiba"];
            $ticketbascula=$_REQUEST["txtticketbascula"];
            $referencia=$_REQUEST["txtreferencia"];
            $observaciones=$_REQUEST["txtobservaciones"];
            $almacenista=$_REQUEST["txtalmacenista"];
            $supervisor="";
            $cabocuadrilla=$_REQUEST["txtcabocuadrilla"];
            
			$cantidadenviada1=str_replace(",","",$_REQUEST["txtcantenv1"]);
            $cantidadenviada2=$_REQUEST["txtcantenv2"];
            $cantidadrecibida1=str_replace(",","",$_REQUEST["txtcantrec1"]);
			
            $cantidadrecibida2=$_REQUEST["txtcantrec2"];
            $idbodega=$_REQUEST["cmbbodega"];   //Bodega Real
            $diferencia1=$_REQUEST["txtcantdif1"];
            $diferencia2=$_REQUEST["txtcantdif2"];
            $folios=$_REQUEST["txtfolios"];
            $doctoorigen=4;
            
            $pbruto=$_REQUEST["txtbruto"];
            $ptara=$_REQUEST["txttara"];
            $pneto=$_REQUEST["txtneto"];

            $cantdev1=0;
            $cantdev2=0;
            $cantfalt1=0;
            $cantfalt2=0;
            $estatus1=0;
            $estatus2=0;
            

            
            //Esto es si existen diferencias
            if($diferencia1>0){
                    $cantdev1=$_REQUEST["txtcantdev1"];
                    $cantdev2=$_REQUEST["txtcantdev2"];
                    $cantfalt1=$_REQUEST["txtcantfalt1"];
                    $cantfalt2=$_REQUEST["txtcantfalt2"];
                    $estatus1=$_REQUEST["txtestatus1"]*1;
                    $estatus2=$_REQUEST["txtestatus2"]*1;                  
            }
            

            $capturista=$_REQUEST["txtcapturista"];
 
//VALIDA INFORMACION DE VALORES

			$politica=0;
			$msg="";

			//Valida que no exista recepcion previamente			
			$sql="select idrecepcion from logistica_recepciones where idenvio=$idenvio";
			$result = $conexion->consultar($sql);
			while($rs = $conexion->siguiente($result)){
				$idrecepcion=$rs{"idrecepcion"};
				$politica=1;
				$msg="Ya esta registrada la recepci�n relacionada a este envio con el folio: $idrecepcion"; 
			}
			$conexion->cerrar_consulta($result);
            

			if(($diferencia1*1>0 && $estatus1*1<>0)){
                            $politica=1;
                            $msg="Falto aclarar la diferencia de envio con recepcion";
                        }
			if($cantidadrecibida1==0){
				$politica=1;
				$msg=" Falto escribir una cantidad valida";
			}
			if(trim($folios=="")){
				$politica=1;
				$msg=" Faltaron los folios";
			}		
       if($politica==1){
            echo "
            <script  language='javascript'>
                alert('Verifique que la informacion este correcta, ".$msg."');
                history.back();
            </script>";    
            exit();
        }
//FIN POLITICAS
                                       

//Grabando Documento
         $sql="Insert Into logistica_recepciones 
                    (idtraslado,idenvio,fecharecepcion,banco,estiba,
                    ticketbascula,referencia,observaciones,almacenista,supervisor,
                    cabocuadrilla,cantidadenviada1,cantidadenviada2,cantidadrecibida1,cantidadrecibida2,
                    idbodega, diferencia1,diferencia2,folios,idempleado,pbruto,ptara,pneto
                    ) VALUES 
                    ('".$idtraslado."','".$idenvio."','".$fecharecepcion."','".$banco."','".$estiba."','".
                    $ticketbascula."','".$referencia."','".$observaciones."','".$almacenista."','".$supervisor."','".
                    $cabocuadrilla."','".$cantidadenviada1."','".$cantidadenviada2."','".$cantidadrecibida1."','".$cantidadrecibida2."','".
                    $idbodega."','".$diferencia1."','".$diferencia2."','".$folios."','".$capturista."','".$pbruto."','".$ptara."','".$pneto."')";
        echo $sql;

        $conexion->consultar($sql);
        $idrecepcion=$conexion->insert_id();
        
        
//Afectando Inventario con Documento.
            //Consulta Politicas el Tipo de movimiento que afecta
            //# POLITICA Consulta Politicas el Tipo de movimiento que afecta
                $tipomovimiento="17";
                $sqlbodega="select * from logistica_politicas where idpolitica=7";
                $result = $conexion->consultar($sqlbodega);
                while($rs = $conexion->siguiente($result)){
                    $tipomovimiento=$rs{"valor1"};
                }
                $conexion->cerrar_consulta($result);
            
            include("../inventarios/clases/clinventarios.php");
            $movimientos = new clinventarios();
            
            //Consulta Detalle
		$sQuery = "select b.idfabricante 'fabricante',b.idbodegaorigen 'bodega',b.idproducto 'producto',
                                b.idloteproducto 'lote', b.idestadoproducto 'estadoproducto', 
                                ifnull(b.cantidadretirada1,0) 'cantidadretirada1',
                                ifnull(b.cantidadretirada2,0) 'cantidadretirada2',
                                ifnull(b.cantidadrecibida1,0) 'cantidadrecibida1',
                                ifnull(b.cantidadrecibida2,0) 'cantidadrecibida2'
                            from logistica_traslados b 
                                inner join logistica_envios a on a.idtraslado=b.idtraslado
                            Where b.idtraslado=".$idtraslado." And a.idenvio=".$idenvio;
                echo $sQuery."<br>";
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                        $fabricante=$rs{"fabricante"};
                        $marca=$rs{"fabricante"};
                        //$bodega=$rs{"bodega"};
                        $producto=$rs{"producto"};
                        $lote=$rs{"lote"};
                        $estadoproducto=$rs{"estadoproducto"};  
                        //Datos para afectar
                        $scantidadrecibida1=$rs{"cantidadrecibida1"}+$cantidadrecibida1;
                        $scantidadrecibida2=$rs{"cantidadrecibida2"}+$cantidadrecibida2;

                        //Agrega Movimiento Almacen
                                    $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$producto,$lote,$estadoproducto,$cantidadrecibida1,$cantidadrecibida2,$fecharecepcion,$doctoorigen,$idrecepcion,$conexion);
                        //Afecta Cantidades en Traslados
                                    $sqlafecta="UPDATE logistica_traslados set cantidadrecibida1=".$scantidadrecibida1.",cantidadrecibida2=".$scantidadrecibida2." Where idtraslado=".$idtraslado;
                                    echo $sqlafecta;
                                    $conexion->consultar($sqlafecta);
                        //Afecta estado en envios
                                    $sqlafecta="UPDATE logistica_envios set idestadodocumento=3 Where idenvio=".$idenvio;
                                    echo $sqlafecta;
                                    $conexion->consultar($sqlafecta);                                    
                        //Afecta Estatus en Envio
                                    $sqlafecta="UPDATE logistica_recepciones set idestadodocumento=1 where idrecepcion=".$idrecepcion;
                                    echo $sqlafecta;
                                    $conexion->consultar($sqlafecta);
                }
		$conexion->cerrar_consulta($result);

                            //Agregando Devoluciones y Faltantes
        
            if ($cantdev1*1>0){
                        //Obtiene datos devolucion
                                $idtransportista="";
                                $cartaporte="";
                                $nombreoperador="";
                                $placastractor="";
                                $placasremolque="";
                                
                                $sqlenv="select * from logistica_envios where idenvio=$idenvio";
                                $result = $conexion->consultar($sqlenv);
                                while($rs = $conexion->siguiente($result)){
                                    $idtransportista=$rs{"idtransportista"};
                                    $cartaporte=$rs{"cartaporte"};
                                    $nombreoperador=$rs{"nombreoperador"};
                                    $placastractor=$rs{"placastractor"};
                                    $placasremolque=$rs{"placasremolque"};
                                }
                                $conexion->cerrar_consulta($result);                
                        
                        $sql = "Insert Into logistica_devoluciones (idtraslado,idenvio,idrecepcion,fechadevolucion,idtransportista,
                                                cartaporte,operador,placastractor,placasremolque,cantidad1,
                                                cantidad2,folios,observaciones,cantidadrecibida1,cantidadrecibida2,
                                                diferencia1,diferencia2,idestadodocumento
                                            ) VALUES 
                                ('" . $idtraslado . "','" . $idenvio . "','" . $idrecepcion . "','" . $fecharecepcion . "','" . $idtransportista . "','" .
                                $cartaporte . "','" . $nombreoperador . "','" . $placastractor . "','" . $placasremolque . "','" . $cantdev1 . "','" .
                                $cantdev2 . "','" . $folios . "','" . $observaciones . "','0','0','".
                                $cantdev1."','".$cantdev2."','1')";

                        $conexion->consultar($sql);
                        $iddevolucion = $conexion->insert_id();
            } 

            if ($cantfalt1*1>0){
                        //Obtiene datos faltantes                
                        
                        $sql = "Insert Into logistica_faltantestraslados (idrecepcion,cantfalt1,cantfalt2,idestadodocumento
                                            ) VALUES 
                                ('" . $idrecepcion . "','" . $cantfalt1 . "','" . $cantfalt2 . "','1')";

                        $conexion->consultar($sql);
                        $idfaltante = $conexion->insert_id();
            }  

						//Consecutivo Interno Bodega 
				$consecutivobodega=-1;
				//Afecta Folio Interno
				$sqlcon="select ifnull(consecutivobodega,0) consecutivobodega from logistica_consecutivosbodega where idbodega=$bodega";
				$result = $conexion->consultar($sqlcon);
				while ($rs = $conexion->siguiente($result)) {
					$consecutivobodega = $rs{"consecutivobodega"};
				}
				$conexion->cerrar_consulta($result);

				if($consecutivobodega>0){
					$sqlafecta="Update logistica_consecutivosbodega set consecutivobodega=($consecutivobodega+1) where idbodega=$bodega";
					$consecutivobodega++;
				}else{
					$consecutivobodega=1;
					$sqlafecta="Insert Into logistica_consecutivosbodega (idbodega,doctoorigen,consecutivobodega) Values ('$bodega','0',1)";
				}
				$conexion->consultar($sqlafecta);
			
				$sqlafecta="update logistica_recepciones set consecutivobodega=$consecutivobodega where idrecepcion=$idrecepcion";
				$conexion->consultar($sqlafecta);
                //echo "Proceso Finalizado con Exito";
                

                exit();
                
                header("Location: recepcion_imprimir.php?idrecepcion=".$idrecepcion) 
         
?>