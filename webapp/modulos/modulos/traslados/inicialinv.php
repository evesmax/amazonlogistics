<?php // include("bd.php");
    

        if( $a == 1 ){       
            //Si el movimiento es Nuevo
		include("../../modulos/inventarios/clases/clinventarios.php");
		$fecha=date("Y-m-d g:i:s");
            //Consulta Detalle
		$afecta=1;
                $sQuery = "SELECT * FROM logistica_traslados lt  
                            where idtraslado=$catalog_id_utilizado
                            and idfabricante in (select idfabricante from inventarios_movimientos 
                                where idbodega=lt.idbodegadestino 
                                        and idproducto=lt.idproducto 
                                        and idloteproducto=lt.idloteproducto
                                        and idestadoproducto=lt.idestadoproducto
                                        and idmarca=lt.idmarca
                                        )";
                echo $sQuery."<br><br>";
                $result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                    $afecta=0;
		}
		$conexion->cerrar_consulta($result);
                //Si no existe registro agregar uno.
                if($afecta==1){
                        $sQuery = "SELECT lt.idfabricante,lt.idbodegadestino idbodega, lt.idproducto,
                                            lt.idloteproducto, lt.idestadoproducto, lt.idmarca
                                    FROM logistica_traslados lt  
                                    where idtraslado=$catalog_id_utilizado  ";
                        //echo $sQuery."<br><br>";
                        $result = $conexion->consultar($sQuery);
                        while($rs = $conexion->siguiente($result)){
                            $idtipomovimiento=1;
                            $idfabricante=$rs{"idfabricante"};
                            $idbodega=$rs{"idbodega"};
                            $idproducto=$rs{"idproducto"};
                            $idloteproducto=$rs{"idloteproducto"};
                            $idestadoproducto=$rs{"idestadoproducto"};
                            $cantidad=0.00000000000000000001;
                            $cantidadsecundaria=0.00000000000000000001;
                            $fecha=$fecha;
                            $doctoorigen=1;
                            $foliodoctoorigen=-1;
                            $idmarca=$rs{"idmarca"};
                        }
                        $conexion->cerrar_consulta($result);
                        $sqlinsert="Insert Into inventarios_movimientos 
                                        (  idtipomovimiento,    idfabricante,  idbodega,   idproducto,   idloteproducto,   idestadoproducto,   cantidad,   cantidadsecundaria,   fecha,   doctoorigen,   foliodoctoorigen,   idmarca) Values 
                                        ('$idtipomovimiento','$idfabricante','$idbodega','$idproducto','$idloteproducto','$idestadoproducto','$cantidad','$cantidadsecundaria','$fecha','$doctoorigen','$foliodoctoorigen','$idmarca')";
                        //echo $sqlinsert;
                        $conexion->consultar($sqlinsert);
                        echo "<br>Se agrego registro en inventario";
                }
                
        }



?>