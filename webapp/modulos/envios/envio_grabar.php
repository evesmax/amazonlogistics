<?php


	include("../../netwarelog/catalog/conexionbd.php");

//RECUPERANDO VARIABLES
         $idtraslado=$_REQUEST["txtidtraslado"];
         //echo "idtraslado ".$idtraslado."<br>";
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
		 $capturista=$_REQUEST["txtcapturista"];
		 $licenciaoperador=$_REQUEST["txtlicencia"];

//VALIDA INFORMACION DE VALORES
			$politica=0;
			$msg="";

			//Valida Existencia Fisica
			$sqlfisica="SELECT
							ifnull((SELECT sum(isa.saldosecundario) FROM inventarios_saldos isa WHERE isa.idfabricante=lt.idfabricante AND isa.idmarca=lt.idmarca AND isa.idbodega=lt.idbodegaorigen AND isa.idproducto=lt.idproducto AND isa.idestadoproducto=lt.idestadoproducto AND isa.idloteproducto=lt.idloteproducto),0) saldo,
							ifnull((SELECT sum(lc.cantidad2) cedes FROM logistica_certificados lc WHERE lc.idfabricante=lt.idfabricante AND lc.idmarca=lt.idmarca AND lc.idbodega=lt.idbodegaorigen AND lc.idproducto=lt.idproducto AND lc.idestadoproducto=lt.idestadoproducto AND lc.idloteproducto=lt.idloteproducto and lc.idestadodocumento=1),0) cedes
						FROM logistica_traslados lt WHERE idtraslado=$idtraslado";
			$result = $conexion->consultar($sqlfisica);
			while($rs = $conexion->siguiente($result)){
                                $saldo=$rs{"saldo"};
                                $cedes=$rs{"cedes"};
                                $fisica=$saldo-$cedes;
						if($cantidad2>$fisica){
								$politica=1;
								$msg.="No hay inventario fisico suficiente";
						}
			}
			$conexion->cerrar_consulta($result);

			//Valida si se regresaron que aun quede saldo
			$sql="select
					ifnull(cantidad1,0)-ifnull(cantidadretirada1,0) saldo1,
					ifnull(cantidad2,0)-ifnull(cantidadretirada2,0) saldo2
				from logistica_traslados where idtraslado=$idtraslado";
			$result = $conexion->consultar($sql);
			while($rs = $conexion->siguiente($result)){
                                $saldo1=$rs{"saldo1"};
                                $saldo2=$rs{"saldo2"};
				if($cantidad1>$saldo1){
                                    $politica=2;
                                    $msg.="La instruccion ya no tiene Saldo, Toneladas";
                                }
                                $saldo2=$rs{"saldo2"};
				if($cantidad2>$saldo2){
                                    $politica=2;
                                    $msg.="La instruccion ya no tiene Saldo, Bultos";
                                }

			}
			$conexion->cerrar_consulta($result);

			if((trim($cartaporte)=="")){
				$politica=1;
				$msg=" Falta la Carta Porte";
			}
			if(trim($placastractor)==""){
				$politica=1;
				$msg=" Faltaron las Placas del Tractor";
			}
			if(trim($nombreoperador)==""){
				$politica=1;
				$msg=" Falto el Nombre del Operador";
			}
			if(trim($licenciaoperador=="")){
				$politica=1;
				$msg=" Falto el numero de Licencia del Operador";
			}
			if($cantidad1==0){
				$politica=1;
				$msg=" Falto escribir una cantidad valida";
			}
			if(trim($folios=="")){
				$politica=1;
				$msg=" Faltaron los folios";
			}
       if($politica==1){
            echo "
            <script  language='javascript'>
                alert('Verifique que la informacion este correcta, ".$msg."');
                history.back();
            </script>";
            exit();
        }
        if($politica==2){
            echo "
            <script  language='javascript'>
                alert('Verifique que la informacion este correcta, ".$msg."');
            </script>";

            header ("Location: ../../netwarelog/repolog/reporte.php");
            exit();
        }
//FIN POLITICAS


//Grabando Documento


         $sql="Insert Into logistica_envios (idtraslado,fechaenvio,idtransportista,cartaporte,nombreoperador,
                                            placastractor,placasremolque,horallegada,ticketbascula,banco,
                                            estiba,cantidad1,cantidad2,consecutivobodega,folios,observaciones,licenciaoperador,idempleado
                                            ) VALUES
                                            ('".$idtraslado."','".$fechaenvio."','".$idtransportista."','".$cartaporte."','".$nombreoperador."','".
                                            $placastractor."','".$placasremolque."','".$horallegada."','".$ticketbascula."','".$banco."','".
                                            $estiba."','".$cantidad1."','".$cantidad2."','0','".$folios."','".$observaciones."','".$licenciaoperador."','".$capturista."')";



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
		$sQuery = "select b.idfabricante 'fabricante',b.idmarca,b.idbodegaorigen 'bodega',b.idproducto 'producto',
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
                        $marca=$rs{"idmarca"};
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
                                    $sqlafecta="UPDATE logistica_envios set idestadodocumento=2 where idenvio=".$idenvio;
                                    echo $sqlafecta;
                                    $conexion->consultar($sqlafecta);
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


				$sqlafecta="UPDATE logistica_envios set consecutivobodega=$consecutivobodega where idenvio=".$idenvio;
				$conexion->consultar($sqlafecta);

				$url_dominio=$_SESSION["url_dominio"];
                header("Location: $url_dominio envio_imprimir.php?idenvio=".$idenvio)

?>
