<?php // include("bd.php");

exit();

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
		include("../../modulos/inventarios/clases/clinventarios.php");
		$movimientos = new clinventarios();
		
            //Consulta Detalle
		$sQuery = "SELECT * FROM produccion_entradas p where identradasproduccion=".$catalog_id_utilizado;
		
                $result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                                $tipomovimiento =1;   
                                $fabricante = $rs["idfabricante"];	
                                $marca= $rs["idfabricante"];
                                $bodega = $rs["idbodega"];
                                $producto = $rs["idproducto"];
                                $lote = $rs["idloteproducto"];
                                $estadoproducto = $rs["idestadoproducto"];
                                $cantidad = $rs["cantidad1"];
                                $cantidadsecundaria = $rs["cantidad2"];
                                $fecha = $rs["fechamovimiento"];
                                $doctoorigen=2;//Se deja fijo el Numero de Documento ene ste caso Produccion
                                $foliodoctoorigen=$catalog_id_utilizado;
                                //echo "Encontre Datos";
            //Agrega Movimiento Almacen
                        $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$bodega,$producto,$lote,$estadoproducto,$cantidad,$cantidadsecundaria,$fecha,$doctoorigen,$foliodoctoorigen,$conexion);
		}
		$conexion->cerrar_consulta($result);
                
            //Marca como procesado el documento
                $sQuery="Update produccion_entradas set idestadodocumento=2 where identradasproduccion=".$catalog_id_utilizado;
                $conexion->consultar($sQuery);
                
                echo "<br>Proceso Concluido con Exito";
        }



?>