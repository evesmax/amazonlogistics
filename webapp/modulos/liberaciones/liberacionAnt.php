<?php
	include("../../netwarelog/catalog/conexionbd.php");
        
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
                    (oc.volumenorden/ifnull(up.factor,1)) cantidad1, oc.volumenorden cantidad2
                From ventas_ordenesdecompra oc 
                    left join inventarios_unidadesproductos up on up.idproducto=oc.idproducto
                Where idordencompra=$idordencompra and idestadodocumento=1";
        
        $result = $conexion->consultar($sqloc);
        while($rs = $conexion->siguiente($result)){
		
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
    	
        
	header("Location: liberacion_imprimir.php?idordencompra=" . $idordencompra)

	

?>