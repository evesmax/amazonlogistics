<?php


$oeagregar=0;
$ieagregar=0;
$idordencompraagregar=0;

$idoe=0;
$idie=0;
$oe=0;
$ie="";
$idordencompra=0;

$idfabricante=0;

//**** AGREGA OE Busca OE, Guarda Valores y trae Ingenio en consulta
                $sqlc="SELECT coe.idoe, coe.oe, oc.idfabricante, coe.idordencompra FROM consecutivos_oe coe 
                    LEFT JOIN ventas_ordenesdecompra oc ON oc.idordencompra=coe.idordencompra 
                    ORDER BY idoe DESC LIMIT 1";
                $resultado = $conexion->consultar($sqlc);
                while($rs = $conexion->siguiente($resultado)){
                    $idoe=$rs{"idoe"};
                    $oe=$rs{"oe"};
                    $idfabricante=$rs{"idfabricante"};
                    $idordencompra=$rs{"idordencompra"};
                } 
                $conexion->cerrar_consulta($resultado);

                //Recupera Valores de nueva tabla
                $sqlc="SELECT coe.ieinsertar, coe.oeinsertar, coe.idordencompra, coe.idfabricante
                        FROM procesos_insertarreferencias coe 
                        where idref=$catalog_id_utilizado";
                $resultado = $conexion->consultar($sqlc);
                while($rs = $conexion->siguiente($resultado)){
                    $oeagregar=$rs{"oeinsertar"};
                    $ieagregar=$rs{"ieinsertar"};
                    $idfabricanteagregar=$rs{"idfabricante"};
                    $idordencompraagregar=$rs{"idordencompra"};
                } 
                $conexion->cerrar_consulta($resultado);
				
        //Elimina Referencias Anteriores Iguales
                $sqlelimina="DELETE FROM consecutivos_oe WHERE oe='$oeagregar'";
                                $conexion->consultar($sqlelimina);

                $sqlelimina="DELETE FROM consecutivos_ie 
                                    WHERE ie='$ieagregar' and idordencompra='$idordencompraagregar' and idfabricante='$idfabricanteagregar'";
                                $conexion->consultar($sqlelimina);
        //Edita OE 
                $sqlagregar="Update consecutivos_oe set oe='$oeagregar', idordencompra='$idordencompraagregar' where idoe=$idoe";
                $conexion->consultar($sqlagregar);

        //Agrega OE
                $sqlagregar="Insert Into consecutivos_oe (oe,idordencompra) values ('$oe','$idordencompra')";
                $conexion->consultar($sqlagregar);

//FIN AGREGA OE   
     

//****** AGREGA IE Busca IE, Guarda Valores
                $sqlc="SELECT cie.idie, cie.ie FROM consecutivos_ie cie WHERE cie.idfabricante=$idfabricante ORDER BY idie DESC LIMIT 1";
                $resultado = $conexion->consultar($sqlc);
                while($rs = $conexion->siguiente($resultado)){
                    $idie=$rs{"idie"};
                    $ie=$rs{"ie"};
                } 
                $conexion->cerrar_consulta($resultado);

        //Edita IE 
                $sqlagregar="Update consecutivos_ie set ie='$ieagregar',idfabricante=$idfabricanteagregar,idordencompra='$idordencompraagregar' where idie=$idie";
                $conexion->consultar($sqlagregar);

        //Agrega IE
                $sqlagregar="Insert Into consecutivos_ie (idfabricante,ie,idordencompra) values ('$idfabricante','$ie',$idordencompra)";
                $conexion->consultar($sqlagregar);


?>
