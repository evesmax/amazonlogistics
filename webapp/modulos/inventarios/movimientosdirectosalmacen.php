<?php // include("bd.php");

        $tipomovimiento = "";   
        $fabricante = "";	
        $marca= ""; 
        $bodega = "";
        $producto = "";
        $lote = "";
        $estadoproducto = "";
        $cantidad = "";
        $fecha = "";
        $doctoorigen = "";
        $foliodoctoorigen = "";
                

        if( $a == 1 ){       
            //Si el movimiento es Nuevo
		include("clases/clinventarios.php");
		$movimientos = new clinventarios();
		
            //Consulta Detalle
		$sQuery = "SELECT  imd.idtipomovimiento, imt.idfabricante,imt.idmarca,imt.idbodega,imd.idproducto,
                                    imd.idloteproducto,imd.idestadoproducto,imd.cantidad,imt.fecha 
                            FROM inventarios_movimientostitulo imt 
                                inner join inventarios_movimientosdetalle imd on imd.idmovimientotitulo=imt.idmovimientotitulo 
                            where imt.idmovimientotitulo=".$catalog_id_utilizado;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                                $tipomovimiento =$rs["idtipomovimiento"];   
                                $fabricante = $rs["idfabricante"];	
                                $marca= $rs["idmarca"];
                                $bodega = $rs["idbodega"];
                                $producto = $rs["idproducto"];
                                $lote = $rs["idloteproducto"];
                                $estadoproducto = $rs["idestadoproducto"];
                                $cantidad = $rs["cantidad"];
                                
                                $cantidadsecundaria=cantidadsecundaria($producto,$cantidad,$conexion);
                                
                                $fecha = $rs["fecha"];
                                $doctoorigen=1;//Se deja fijo el Numero de Documento
                                $foliodoctoorigen=$catalog_id_utilizado;
                                
                                
            //Agrega Movimiento Almacen
                        $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$bodega,$producto,$lote,$estadoproducto,$cantidad,$cantidadsecundaria,$fecha,$doctoorigen,$foliodoctoorigen,$conexion);
		}
		$conexion->cerrar_consulta($result);
                
            //Marca como procesado el documento
                $sQuery="Update inventarios_movimientostitulo set idestadodocumento=2 where idmovimientotitulo=".$catalog_id_utilizado;
                $conexion->consultar($sQuery);
                
                echo "<br>Proceso Concluido con Exito ";
        }

        //FUNCION PARA REGRESAR CANTIDAD SECUNDARIA
        function cantidadsecundaria($producto,$cantidadp,$conexionf){
                $edita=0;
                $desc2="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,ifnull(i.factor,0) factor,i.idtipounidadmedida edita FROM inventarios_unidadesproductos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$producto." Limit 1";
                    $result = $conexionf->consultar($sQuery);
                while($rs = $conexionf->siguiente($result)){
                        $desc2 = $rs["descripcionunidad"];
                        $factor= $rs["factor"];
                        $edita=$rs{"edita"};
                }
                $conexionf->cerrar_consulta($result);
                $cantidadconversion=0;
                $cantidadconversion=str_replace(',','',$cantidadp)*str_replace(',','',$factor);
                return $cantidadconversion;
        }

?>