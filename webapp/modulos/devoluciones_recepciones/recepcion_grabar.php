<?php
	
	
            include("../../netwarelog/catalog/conexionbd.php");
	
//RECUPERANDO VARIABLES REFERENCIA PARA GRABAR
            $idtraslado=$_REQUEST["txtidtraslado"];
            $idenvio=$_REQUEST["txtidenvio"];
            $idrecepcion=$_REQUEST["txtidrecepcion"];
            $iddevolucion=$_REQUEST["txtiddevolucion"];
            
            
//Recibida de devolucion 
            $cantidadrecibida1=$_REQUEST["txtcantrec1"];
            $cantidadrecibida2=$_REQUEST["txtcantrec2"];
            $diferencia1=$_REQUEST["txtcantdif1"];
            $diferencia2=$_REQUEST["txtcantdif2"];            
            
            $idbodega=$_REQUEST["cmbbodega"];
            $estiba=$_REQUEST["txtestiba"];
            $banco=$_REQUEST["txtbanco"];
            $folios=$_REQUEST["txtfolios"];
            $idestadoproducto=$_REQUEST["cmbestado"];
            $fecha=$_REQUEST["txtfecharec"];

//Grabando Documento
         $sql="Update 
                    logistica_devoluciones 
                Set 
                    cantidadrecibida1=$cantidadrecibida1, cantidadrecibida2=$cantidadrecibida2,
                    diferencia1=$diferencia1, diferencia2=$diferencia2, idbodega='$idbodega', 
                    estiba='$estiba',banco='$banco',foliosd='$folios',idestadoproducto='$idestadoproducto',
                    fecharecepcion='$fecha', idestadodocumento=2
                Where iddevolucion=$iddevolucion";
         
        //echo $sql;

        
        $conexion->consultar($sql);
        //$idrecepcion=$conexion->insert_id();
        
        
//Afectando Inventario con Documento.
            //Consulta Politicas el Tipo de movimiento que afecta
            //# POLITICA Consulta Politicas el Tipo de movimiento que afecta
                $tipomovimiento="20";
                $sqlbodega="select * from logistica_politicas where idpolitica=11";
                $result = $conexion->consultar($sqlbodega);
                while($rs = $conexion->siguiente($result)){
                    $tipomovimiento=$rs{"valor1"};
                }
                $conexion->cerrar_consulta($result);
            
            include("../inventarios/clases/clinventarios.php");
            $movimientos = new clinventarios();
            
            //Consulta Detalle
		$sQuery = "select b.idfabricante 'fabricante',b.idmarca marca,b.idproducto 'producto',
                                b.idloteproducto 'lote'
                            from logistica_traslados b 
                                inner join logistica_envios a on a.idtraslado=b.idtraslado
                            Where b.idtraslado=".$idtraslado." And a.idenvio=".$idenvio;

                $result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                        $fabricante=$rs{"fabricante"};
                        $marca=$rs{"marca"};
                        //$bodega=$rs{"bodega"};
                        $producto=$rs{"producto"};
                        $lote=$rs{"lote"};
                        $estadoproducto=$idestadoproducto;  
                        //Datos para afectar
                        $scantidadrecibida1=$cantidadrecibida1;
                        $scantidadrecibida2=$cantidadrecibida2;
                        $doctoorigen=6;
                        
                        //Si no tiene cantidad no hace movimiento de almacen
                        if($cantidadrecibida1*1>0){
                            //Agrega Movimiento Almacen
                            $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$producto,$lote,$estadoproducto,$cantidadrecibida1,$cantidadrecibida2,$fecha,$doctoorigen,$iddevolucion,$conexion);
                        }
                }
		$conexion->cerrar_consulta($result);
                
                if ($diferencia1*1>0){
                            //Obtiene datos faltantes                

                            $sql = "Insert Into logistica_faltantestraslados (idrecepcion,cantfalt1,cantfalt2,idestadodocumento
                                                ) VALUES 
                                    ('" . $idrecepcion . "','" . $diferencia1 . "','" . $diferencia2 . "','1')";

                            $conexion->consultar($sql);
                            $idfaltante = $conexion->insert_id();
                }  
                

                
                
                header("Location: recepcion_imprimir.php?iddevolucion=".$iddevolucion)
         
?>