<?php

        date_default_timezone_set("America/Mexico_City");


        include("../../netwarelog/catalog/conexionbd.php");
        include("../../netwarelog/webconfig.php");
        //Funcion Agrega Consecutivos
        function regresaref($tipo,$idfabricante,$idref, $conexionf){
            //Tipo 1=OE, 2=IE
            $ref=0;
            if ($tipo==1){
                //OE
                $sqlestatus="Select oe from consecutivos_oe order by idoe desc limit 1";
                $result = $conexionf->consultar($sqlestatus);
                    while($rs = $conexionf->siguiente($result)){
                        $ref= $rs{"oe"};
                    }
                $conexionf->cerrar_consulta($result);    
                    $ref+=1;
                $sqlact="Insert Into consecutivos_oe (oe,idordencompra) Values ('$ref','$idref')";    
                $conexionf->consultar($sqlact);
            }elseif($tipo==2){
                //IE
                $sqlestatus="Select ie from consecutivos_ie where idfabricante=$idfabricante order by idie desc limit 1";
                $result = $conexionf->consultar($sqlestatus);
                    while($rs = $conexionf->siguiente($result)){
                        $ref= $rs{"ie"};
                    }
                $conexionf->cerrar_consulta($result);    
                    $ref+=1;                
                $sqlact="Insert Into consecutivos_ie (idfabricante,ie,idordencompra) Values ('$idfabricante','$ref','$idref')";    
                                                            $conexionf->consultar($sqlact);
            }   
                return $ref;
        } 
        
 	
//RECUPERANDO VARIABLES
                                        
        $idordencompra=0;
        $idordencompra=$_GET["idordencompra"];
        $idfabricante=$_GET["idfabricante"];
        $saldo=0;
        $idcliente=0;
        
        
//Verifica Si stan completos los depositos en la orden, encaso de que no enviara el formulario de registro de pago sobre saldos.    
        $sqlsaldo="";
        $sqlsaldo="SELECT ifnull(oc.importe,0)-ifnull(sum(ocd.importe),0) saldo, oc.idcliente 
                        FROM ventas_ordenesdecompra oc 
                        LEFT JOIN ventas_ordenescompra_depositos ocd ON ocd.idordencompra=oc.idordencompra
                    WHERE oc.idordencompra=$idordencompra";
        
        
        $result = $conexion->consultar($sqlsaldo);
        while($rs = $conexion->siguiente($result)){
            $saldo=$rs{"saldo"};

            $idcliente=$rs{"idcliente"};
            if($saldo>0){
                //Redirige a nuevo php para registrar pagos desde saldo del cliente
                //header ("Location: completadepositos.php?idcliente=$idcliente&idordencompra=".$idordencompra);
                ?>
                    <script type="text/javascript">
                    window.location="<?php echo $url_dominio; ?>modulos/liberaciones/completadepositos.php?diferencia=<?php echo $saldo; ?>&idcliente=<?php echo $idcliente; ?>&idordencompra=<?php echo $idordencompra; ?>";
                    </script>
                <?php
                exit();
            }
        }
        $conexion->cerrar_consulta($result);      

    //Genera de los depositos registrados un deposito en ventas_saldosclientes_detalle y afecta ventas_saldosclientes  
        
        
        
        //OBTIENE VALORES
        $idcontrato=0;              
        $idpedido=$idordencompra;    //Orden de Compra
        $fecha=date("Y-m-d H:i:s");  //Fecha del Dia
        $idcliente=0;
        $referencia1="";
        $referencia2="";
        $idfabricante=$idfabricante;
        $idmarca=0;
        $idbodega=0;
        $idloteproducto=0;
        $idproducto=0;
        $idestadoproducto=0;
        $cantidad1=0;
        $cantidad2=0;
        $observaciones=0;
        $cantidadretirada1=0;
        $cantidadretirada2=0;
        $saldo1=0;
        $saldo2=0;
        $idestadodocumento=1;
        $fechacancelacion="";
        $inicialv11=0;
        $inicialv12=0;

        $sqloc="Select  oc.clavecontrato,oc.fecha,oc.idcliente,oc.idmarca,oc.idbodega, oc.idloteproducto,oc.idproducto,oc.idestadoproducto,
                    (oc.volumenorden/ifnull(up.factor,1)) cantidad1, oc.volumenorden cantidad2, oc.idfabricante
                From ventas_ordenesdecompra oc 
                    left join inventarios_unidadesproductos up on up.idproducto=oc.idproducto
                Where idordencompra=$idordencompra and idestadodocumento=1";
        
        $result = $conexion->consultar($sqloc);
        while($rs = $conexion->siguiente($result)){
            $idfabricante=$rs{"idfabricante"};
            //AGREGA CONSECUTIVOS
                    $oe=regresaref(1, 1, $idordencompra, $conexion);
                    $ie=regresaref(2, $idfabricante, $idordencompra, $conexion);

            $idcontrato=$rs{"clavecontrato"};              
            $idcliente=$rs{"idcliente"};
            $idmarca=$rs{"idmarca"};
            $idbodega=$rs{"idbodega"};
            $idloteproducto=$rs{"idloteproducto"};
            $idproducto=$rs{"idproducto"};
            $idestadoproducto=$rs{"idestadoproducto"};
            $cantidad1=$rs{"cantidad1"};
            $cantidad2=$rs{"cantidad2"};
            $saldo1=$rs{"cantidad1"};
            $saldo2=$rs{"cantidad2"};
	
            //INSERTANDO DATOS PARA LA OE
				$sqlinsert="Insert Into logistica_ordenesentrega 
								(
									clavecontrato,idpedido,fecha,idcliente,
									referencia1,referencia2,idfabricante,idmarca,idbodega,
									idloteproducto,idproducto,idestadoproducto,cantidad1,cantidad2,
									observaciones,cantidadretirada1,cantidadretirada2,saldo1,saldo2,
									idestadodocumento,inicialv11,inicialv12
								)
							Values 
								(
									'$idcontrato','$idpedido','$fecha','$idcliente',
									'$oe','$ie','$idfabricante','$idmarca','$idbodega',
									'$idloteproducto','$idproducto','$idestadoproducto','$cantidad1','$cantidad2',
									'$observaciones','$cantidadretirada1','$cantidadretirada2','$saldo1','$saldo2',
									'$idestadodocumento','$inicialv11','$inicialv12'
								)";
				$conexion->consultar($sqlinsert); 
				
                                //INSERTANDO HISTORICO OE
				$obs="Creación del OE: $oe";
							$sqlinsert="Insert Into logistica_historialoe 
								(fecha,oe,oerelacion,observaciones)
							Values 
								('$fecha','$oe','$oe','$obs')";
				$conexion->consultar($sqlinsert);
                                //echo "SqlHistorialOE: $sqlinsert";

			//PROCESA LA ORDEN DE COMPRA
				$sqlafecta = "UPDATE ventas_ordenesdecompra set idestadodocumento=2 where idordencompra=$idordencompra";
				$conexion->consultar($sqlafecta);
        }
        $conexion->cerrar_consulta($result);
    	
//Proceso registro de depositos en Saldos Clientes
        $refdeposito="";
        $fechadeposito="";
        $existe=0;
        $importe=0;
        $sqldeposito="";
        $sqlsaldocliente="";
        $idsaldocliente=0;
        //Analiza los depositos de acuerdo a ordenes de compra depositos
        $sqldeposito="SELECT vd.refdeposito, vd.fechadeposito,vd.importe, vd.importe,vd.iddepositos,
                        IFNULL((SELECT idsaldocliente FROM ventas_saldosclientes 
                            WHERE idcliente=$idcliente AND referenciadeposito=vd.refdeposito),-1) existe
                      FROM ventas_ordenescompra_depositos vd
                      WHERE idordencompra=$idordencompra AND idorigen IS null";
        $result = $conexion->consultar($sqldeposito);
        while($rs = $conexion->siguiente($result)){
            $refdeposito=$rs{"refdeposito"};
            $fechadeposito=$rs{"fechadeposito"};
            $importe=$rs{"importe"};
            $existe=$rs{"existe"};
            $iddepositos=$rs{"iddepositos"};
            //Si existe Actualiza sumando el deposito actual
            if($existe==-1){
                //Actualiza Consecutivo y regresa Nuevo
                $siguiente="";
                $clave="";
                $sqlc="SELECT consecutivo+1 siguiente, clave FROM ventas_consecutivodepositos WHERE idfabricante=$idfabricante";
                $result2 = $conexion->consultar($sqlc);
                while($rs = $conexion->siguiente($result2)){
                    $siguiente=$rs{"siguiente"};
                    $clave=$rs{"clave"}."-$siguiente";
                }
                $conexion->cerrar_consulta($result2);
                $sqlc="Update ventas_consecutivodepositos set consecutivo=consecutivo+1 where idfabricante=$idfabricante";
                echo "$sqlc <br>";
                $conexion->consultar($sqlc);
                
                //Inserta Registro en Saldos Titulo
                $sqlsaldocliente="Insert Into ventas_saldosclientes 
                                    (idcliente, referenciadeposito, importeinicial, importeaplicado, saldo, fecha, consecutivo) values 
                                    ('$idcliente','$refdeposito','$importe','$importe','0','$fechadeposito','$clave')";
                echo "$sqlsaldocliente <br>";
                $conexion->consultar($sqlsaldocliente);
                $idsaldocliente=$conexion->insert_id();
                 
                //Inserta Registro en Saldos Detalle
                $sqlsaldocliente="Insert Into ventas_saldosclientes_detalle 
                                    (idsaldocliente, fechamovimiento, importe, doctoorigen, foliodoctoorigen) values 
                                    ('$idsaldocliente','$fechadeposito','$importe','8','$idordencompra')";
                echo "$sqlsaldocliente <br>";
                $conexion->consultar($sqlsaldocliente);
                
                //Actualiza en el deposito relacionandolo con saldos por ID
                $actualizafolio="update ventas_ordenescompra_depositos SET idorigen=$idsaldocliente WHERE iddepositos=$iddepositos";
                echo "$actualizafolio <br>";
                $conexion->consultar($actualizafolio);   
                
            }else{
                //Actualiza registro en detalle
                $sqlsaldocliente="Insert Into ventas_saldosclientes_detalle 
                                    (idsaldocliente, fechamovimiento, importe, doctoorigen, foliodoctoorigen) values 
                                    ('$existe','$fechadeposito','$importe','8','$idordencompra')";
                echo "$sqlsaldocliente <br>";
                $conexion->consultar($sqlsaldocliente);
                
                $sqlsaldocliente="UPDATE ventas_saldosclientes SET 
                                    importeaplicado=ifnull((SELECT sum(importe) FROM ventas_saldosclientes_detalle WHERE idsaldocliente=$existe),0),
                                    saldo=importeinicial-ifnull((SELECT sum(importe) FROM ventas_saldosclientes_detalle WHERE idsaldocliente=$existe),0)
                                  WHERE idsaldocliente=$existe";
                echo "$sqlsaldocliente <br>";
                $conexion->consultar($sqlsaldocliente);
                
                
                
            }
        }
        $conexion->cerrar_consulta($result);
//Finaliza Proceso        
        
        //header("Location: liberacion_imprimir.php?idordencompra=$idordencompra");
        //exit();
        ?>
            <script type="text/javascript">
            window.location="<?php echo $url_dominio; ?>modulos/liberaciones/liberacion_imprimir.php?idordencompra=<?php echo $idordencompra; ?>";
            </script>
        <?php
	
?>