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
        $usuario= $_SESSION["accelog_idempleado"];   

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
			
				$sqlafecta="update logistica_recepciones set consecutivobodega=$consecutivobodega where identradasproduccion=$catalog_id_utilizado";
				$conexion->consultar($sqlafecta);
                //echo "Proceso Finalizado con Exito";


			 
            //Marca como procesado el documento
                $sQuery="Update produccion_entradas set idestadodocumento=2,idempleado='".$usuario."' where identradasproduccion=".$catalog_id_utilizado;
                $conexion->consultar($sQuery);
                
                echo "<br>Proceso Concluido con Exito<br>";
								
				$linkcot="modulos/produccion/produccion_imprimir.php?folio=".$catalog_id_utilizado;
				echo "<A href='".$url_dominio.$linkcot."'><img src='../../netwarelog/repolog/img/impresora.png' border='0'>Produccion</A>";
						
        }



?>