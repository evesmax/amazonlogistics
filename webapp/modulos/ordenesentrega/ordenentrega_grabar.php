<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
//RECUPERANDO VARIABLES
         $idtraslado=$_REQUEST["txtidtraslado"];
         echo "idtraslado ".$idtraslado."<br>";
         $fechaenvio=$_REQUEST["txtfechaenvio"];
         $idtransportista=$_REQUEST["cmbtransportista"];
         $cartaporte=$_REQUEST["txtcartaporte"];
         $nombreoperador=$_REQUEST["txtoperador"];
         $placastractor=$_REQUEST["txtplacastractor"];
         $placasremolque=$_REQUEST["txtplacasremolque"];
         $horallegada=$_REQUEST["txtllegada"];
         $ticketbascula=$_REQUEST["txtticketbascula"];
         $banco=$_REQUEST["txtbanco"];
         $estiba=$_REQUEST["txtestiba"];
         $cantidad1=$_REQUEST["txtcantidad1"];
         $cantidad2=$_REQUEST["txtcantidad2"];
         $doctoorigen=3;
         $folios=$_REQUEST["txtfolios"];
         $observaciones=$_REQUEST["txtobservaciones"];
//Grabando Documento
         
         
         $sql="Insert Into logistica_envios (idtraslado,fechaenvio,idtransportista,cartaporte,nombreoperador,
                                            placastractor,placasremolque,horallegada,ticketbascula,banco,
                                            estiba,cantidad1,cantidad2,consecutivobodega,folios,observaciones
                                            ) VALUES 
                                            ('".$idtraslado."','".$fechaenvio."','".$idtransportista."','".$cartaporte."','".$nombreoperador."','".
                                            $placastractor."','".$placasremolque."','".$horallegada."','".$ticketbascula."','".$banco."','".
                                            $estiba."','".$cantidad1."','".$cantidad2."','0','".$folios."','".$observaciones."')";

        
         $conexion->consultar($sql);
        $idenvio=$conexion->insert_id();
        
        
//Afectando Inventario con Documento.
            //Consulta Politicas el Tipo de movimiento que afecta
            //# POLITICA Consulta Politicas el Tipo de movimiento que afecta
                $tipomovimiento="16";
                $sqlbodega="select * from logistica_politicas where idpolitica=3";
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
                                ifnull(b.cantidadrecibida1,0) 'cantidadrecibida2'
                            from logistica_traslados b 
                                inner join logistica_envios a on a.idtraslado=b.idtraslado
                            Where b.idtraslado=".$idtraslado." And a.idenvio=".$idenvio;
                echo $sQuery."<br>";
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                        $fabricante=$rs{"fabricante"};
                        $marca=$rs{"fabricante"};
                        $bodega=$rs{"bodega"};
                        $producto=$rs{"producto"};
                        $lote=$rs{"lote"};
                        $estadoproducto=$rs{"estadoproducto"};  
                        //Datos para afectar
                        $cantidadretirada1=$rs{"cantidadretirada1"}+$cantidad1;
                        $cantidadretirada2=$rs{"cantidadretirada2"}+$cantidad2;

                        //Agrega Movimiento Almacen
                                    $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$bodega,$producto,$lote,$estadoproducto,$cantidad1,$cantidad2,$fechaenvio,$doctoorigen,$idenvio,$conexion);
                        //Afecta Cantidades en Traslados
                                    $sqlafecta="UPDATE logistica_traslados set cantidadretirada1=".$cantidadretirada1.",cantidadretirada2=".$cantidadretirada2." Where idtraslado=".$idtraslado;
                                    echo $sqlafecta;
                                    $conexion->consultar($sqlafecta);
                        //Afecta Estatus en Envio
                                    $sqlafecta="UPDATE logistica_envios set idestadodocumento=2, consecutivobodega=consecutivobodega+1 where idenvio=".$idenvio;
                                    echo $sqlafecta;
                                    $conexion->consultar($sqlafecta);
                }
		$conexion->cerrar_consulta($result);

                
                header("Location: envio_imprimir.php?idenvio=".$idenvio) 
         
?>