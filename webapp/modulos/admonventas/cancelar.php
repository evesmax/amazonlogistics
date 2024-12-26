<?php
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


include("bd.php"); 

//RECUPERANDO VARIABLES
$idref=$_REQUEST["idref"];
$oe = $_REQUEST["txtoe"];
$cantidadtm = $_REQUEST["txtcantidad"];
$obs = $_REQUEST["txtobs"];
$fecha=date("Y-m-d G:i:s");
$oecancelacion=0;
$estatus=$_REQUEST["txtestatus"];



//AGREGA CONSECUTIVOS
    $oecancelacion=regresaref(1, 1, -1, $conexion);
                $factor="0.05";
                //Obtener Conversion
                $sQuery = "SELECT u.descripcionunidad,ifnull(i.factor,0) factor ,i.idtipounidadmedida edita FROM inventarios_unidadesproductos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto in (select idproducto from logistica_ordenesentrega where idordenentrega=$idref) limit 1";
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $factor= $rs["factor"];
                }
                $conexion->cerrar_consulta($result);
//Agrega Historico
$cantidadbultos=$cantidadtm/$factor;
$sqlafecta="Insert Into logistica_cancelacionordenesentrega 
                (idordenentrega,oecancelacion,fechacancelacion,cantidad1,cantidad2,observaciones,idestadodocumento) Values 
                ('$idref','$oecancelacion','$fecha','$cantidadbultos','$cantidadtm','$obs','1')";
$conexion->consultar($sqlafecta);
$idcancelacion=$conexion->insert_id();

//Genera Registro de Cancelacion
$sqlafecta="Insert Into logistica_historialoe (fecha,oe,oerelacion,observaciones) Values ('$fecha','$oe','$oecancelacion','$obs')";
$conexion->consultar($sqlafecta);

//Si la cancelacion es total entonces edita la orden de entrega
if($estatus=="Completo"){
    $sqlafecta="Update logistica_ordenesentrega set idestadodocumento=4, fechacancelacion='$fecha' where idordenentrega=$idref";
    $conexion->consultar($sqlafecta);
}
//Agrega un registro a saldo clientes detalle en negativo la cantidad qen efectivo que represente la cantidad cancelada
        //Obtiene Importe $$
                $precioventa=0;
                $sQuery = "SELECT precioventa FROM ventas_ordenesdecompra 
                            WHERE idordencompra IN (SELECT idpedido FROM logistica_ordenesentrega WHERE idordenentrega=$idref)";
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $precioventa= $rs["precioventa"];
                }
                $conexion->cerrar_consulta($result);
                $totalcancelacion=($precioventa*$cantidadtm);
                
                echo "$sQuery";

        //Aplica Cancelaciones a Depositos        
                $saldo=$totalcancelacion;
                $aplicar=0;
                $sQuery = "SELECT refdeposito, importe, fechadeposito, idorigen, d.idordencompra 
                            FROM ventas_ordenescompra_depositos d  
                            LEFT JOIN logistica_ordenesentrega oe ON oe.idpedido=d.idordencompra  WHERE oe.idordenentrega=$idref";
                
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $idordencompra=$rs["idordencompra"];
                        $refdeposito=$rs["refdeposito"];
                        $importe= $rs["importe"];
                        $idorigen=$rs["idorigen"];
                        //Determinando cantidad aplicar pr tupla de deposito
                        if($saldo>0){
                            if($saldo>$importe){
                                $aplicar=$importe;
                            }else{
                                $aplicar=$saldo;
                            }
                        //Aplica Cancelacion
                            $aplicar=$aplicar*-1;
                            $sqlsaldocliente="Insert Into ventas_saldosclientes_detalle 
                                    (idsaldocliente, fechamovimiento, importe, doctoorigen, foliodoctoorigen) values 
                                    ('$idorigen','$fecha','$aplicar','9','$idordencompra')";
                            echo "$sqlsaldocliente <br>";
                            $conexion->consultar($sqlsaldocliente);
                            $aplicar=0;
                        }
                    //Actualiza Saldos    
                    $sqlsaldocliente="UPDATE ventas_saldosclientes SET 
                            importeaplicado=ifnull((SELECT sum(importe) FROM ventas_saldosclientes_detalle WHERE idsaldocliente=$idorigen),0),
                            saldo=importeinicial-ifnull((SELECT sum(importe) FROM ventas_saldosclientes_detalle WHERE idsaldocliente=$idorigen),0)
                            WHERE idsaldocliente=$idorigen";
                    echo "$sqlsaldocliente <br>";
                    $conexion->consultar($sqlsaldocliente);

                }
                $conexion->cerrar_consulta($result);
                


header("Location: cancelacion_imprimir.php?idcancelacion=$idcancelacion")
?>