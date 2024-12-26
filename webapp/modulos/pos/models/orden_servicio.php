<?php
//ini_set("display_errors", 1); error_reporting(E_ALL);

require("models/connection_sqli_manual.php"); // funciones mySQLi

class OrdenServicio extends Connection {



	public function actualiza() {

	}
	public function destinos(){
		$sel = 'SELECT * from app_destinos where activo=1';
		$res = $this->queryArray($sel);

		return  array('destinos' => $res['rows']);
	}
	public function solicitudes(){
		/*$select = '
							SELECT
										s.*,
										IFNULL((SELECT SUM(1) FROM app_solicitud_viaje_gastos WHERE idSolicitud = s.id),0) AS tiene_gastos,
										c.nombre,
										(select clave from app_destinos where id=s.origen) as origenN,
										(select clave from app_destinos where id=s.destino) as destinoN
							FROM app_solicitud_viaje s , comun_cliente c
							WHERE c.id=s.idCliente';
		*/
		$sql = '
					SELECT
						s.id,
						s.aprobado,
						s.num_viaje,
						s.estatus,
						s.idventa,
						c.nombre,
						ord.clave AS origenN,
						des.clave AS destinoN,
						s.fechaIda,
						s.fechaRegreso,
						s.fecha,
						CASE WHEN COUNT(tg.id) > 0 THEN 1 ELSE 0 END AS tiene_gastos
					FROM app_solicitud_viaje AS s
					INNER JOIN comun_cliente c 	ON(s.idCliente 	= c.id)
					LEFT JOIN app_solicitud_viaje_gastos tg ON(tg.idSolicitud = s.id)
					LEFT JOIN app_destinos ord 	ON(ord.id 				= s.origen)
					LEFT JOIN app_destinos des	ON(des.id 			= s.destino)
					GROUP BY
										s.id,
										s.estatus,
										s.idventa,
										c.nombre,
										ord.clave,
										des.clave,
										s.fechaIda,
										s.fechaRegreso,
										s.fecha

							';
		$rs = $this->queryArray($sql);
		foreach ($rs['rows'] as $key => $value) {
			if($value['idVenta']!=''){
				$sel = "SELECT folio from app_respuestaFacturacion where idSale=".$value['idVenta'];
				$r = $this->queryArray($sel);
				$rs['rows'][$key]['uuid']= $r['rows'][0]['folio'];
			}else{
				$rs['rows'][$key]['uuid']='';
			}
		}

		return  array('solici' => $rs["rows"]);
	}

	public function obtenerTipoDeCambioPorMoneda($idTipoMoneda){
		$sql = "
					SELECT
								id,
								tipo_cambio
					FROM cont_tipo_cambio
					WHERE  moneda = $idTipoMoneda
					ORDER BY id DESC
					LIMIT 1
		";
		$resultado = $this->queryArray($sql);
		return array("tipoDeCambio"=> $resultado["rows"]);
	}

	public function guarda_solicitud($num_viaje,$origen,$destino,$pasajeros,$pasajeros_nom,$aeronave,$escalas,$escalas_array,$ida,$regreso,$redondo,$tipoViaje,$totalTiempo,$nombreCliente,$costo_viaje,$idmoneda,$tipo_cambio,$tarifaDeViaje){

		$idCliente = $nombreCliente;
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
		$ddate = date("Y-m-d");
		$date = new DateTime($ddate);
		$week = $date->format("W");



		$insert = "INSERT into app_solicitud_viaje(
																								num_viaje,
																								idCliente,
																								idaeronave,
																								tipoVuelo,
																								tipoViaje,
																								origen,
																								destino,
																								fechaIda,
																								fechaRegreso,
																								fecha,
																								id_semana,
																								pasajeros,
																								numPasajeros,
																								escalas,
																								tiempoTotal,
																								costo_viaje,
																								idmoneda,
																								tipo_cambio,
																								tarifaViaje
																							)
																							values
																							(
																								$num_viaje,
																								'".$idCliente."',
																								$aeronave,
																								'".$redondo."',
																								'".$tipoViaje."',
																								'".$origen."',
																								'".$destino."',
																								'".$ida."',
																								'".$regreso."',
																								'".$fechaactual."',
																								'".$week."',
																								'$pasajeros_nom',
																								'".$pasajeros."',
																								'".$escalas."',
																								'".$totalTiempo."',
																								$costo_viaje,
																								'$idmoneda',
																								'$tipo_cambio',
																								$tarifaDeViaje
																							)";
		if($resin = $this->queryArray($insert)){
			$escalas_array = json_decode($escalas_array);
			if(is_array($escalas_array)){
				for($i=0;$i<=count($escalas_array)-1;$i++){
					$this->query("INSERT INTO app_solicitud_viaje_escalas(idsolicitud,numero,origen,destino,tiempo,fecha) VALUES(".$resin['insertId'].",".$escalas_array[$i][0].",".$escalas_array[$i][1].",".$escalas_array[$i][2].",'".$escalas_array[$i][3]."','".$escalas_array[$i][4]."');");

				}
			}
		}


	}
	public function gastosInfoSum($idSolicitud){
		/*********************************************************************
		* CREAMOS UNA TABLA TEMPORAL PARA NO HACER SUBQUERIES
		**********************************************************************/
		$sql = "
					CREATE TEMPORARY TABLE IF NOT EXISTS tipoCambiaAgrupado
					(
						idTipoCambio INT NULL
					)
		";
		$this->query($sql);

		/*********************************************************************
		* INSERTAMOS LAS DIVISAS EN LA TABLA TEMPORAL
		**********************************************************************/
		$sql = "
						INSERT INTO tipoCambiaAgrupado
						SELECT
								MAX(id)
						FROM cont_tipo_cambio
							group by moneda
		";

		$this->query($sql);
		/*********************************************************************
		* REALIZAMOS EL QUERIE PARA SACAR LA SUMA EN PESOS DE TODOS LOS GASTOS
		**********************************************************************/
		$sql = "
				SELECT
					SUM(vg.importe*tc.tipo_cambio) AS importe
				FROM app_solicitud_viaje_gastos vg
				INNER JOIN cont_tipo_cambio tc ON(vg.idMoneda = tc.moneda)
				INNER JOIN tipoCambiaAgrupado ca ON(ca.idTipoCambio = tc.id)
				WHERE vg.idSolicitud = $idSolicitud
		";
		$res = $this->queryArray($sql);

		/*********************************************************************
		* ELIMINAMOS LA TABLA TEMPORAL
		**********************************************************************/
		$sql = "DROP TABLE tipoCambiaAgrupado";
		$this->query($sql);
		/*********************************************************************
		* REGRESAMOS EL RESULTADO
		**********************************************************************/
		return $res["rows"];
	}
	public function gastosInfo($id){
		//$se = "SELECT g.* from app_solicitud_viaje_gastos where g.idSolicitud=".$id;
		$sel = "SELECT
										vg.id,
										sv.num_viaje,
										vg.fecha,
										(SELECT CONCAT('(',clave,') ',nombre) FROM app_categoria_aeronaves WHERE id = vg.idcategoria) AS categoria,
										vg.idcategoria,
										vg.importe,
										(SELECT codigo FROM cont_coin WHERE coin_id = vg.idMoneda) AS Moneda,
										vg.idMoneda,
										vg.tipoCambio,
										vg.cuenta,
										vg.formaPago,
										vg.referencia
				FROM app_solicitud_viaje_gastos vg
				INNER JOIN app_solicitud_viaje sv ON sv.id = vg.idSolicitud
				WHERE vg.activo = 1 AND vg.idSolicitud = $id";

		return $this->query($sel);
	}

	public function gastosInfo_anterior($id){
		//$se = "SELECT g.* from app_solicitud_viaje_gastos where g.idSolicitud=".$id;
		$sel = 'SELECT g.*, fp.nombre as fp ,c.idbancaria,c.cuenta, b.Clave, b.nombre as banco, cg.concepto
			from app_solicitud_viaje_gastos g, forma_pago fp, bco_cuentas_bancarias c, cont_bancos b, app_conceptos_gastos cg
			where g.activo=1 and g.formaPago=fp.idFormapago and c.idbanco=b.idbanco and g.cuenta=c.idbancaria and cg.id=g.categoria and g.idSolicitud='.$id;

		$res = $this->queryArray($sel);
		return array('gastos' => $res['rows'] );
	}
	public function formasDePago(){
		$query = "select * from view_forma_pago WHERE activo = 1 ORDER BY claveSat ASC ";
		$res = $this->queryArray($query);

		return array('formas' => $res['rows'] );
	}
	function calcularCostoViaje($idAeroNave,$minutos){
		$sql =
		"
			SELECT
					FORMAT((ca.tarifaPorHora/60)*$minutos,2) AS costoDeVuelo
			FROM app_catalogo_aeronaves ca
			WHERE id = $idAeroNave
		";
		$res = $this->queryArray($sql);

		return $res["rows"];
	}
	public function listaFormasPago()
    {
        $query = "SELECT* FROM view_forma_pago WHERE claveSat < '99' ORDER BY claveSat";

        $res = $this->queryArray($query);

		return array('formas' => $res['rows'] );
    }
    public function getSucursales(){
		$selcSuc = "SELECT * from mrp_sucursal where activo = -1";
		$resSel = $this->queryArray($selcSuc);
		return $resSel['rows'];
	}
	public function getSegmentos(){
		$sel = "SELECT * from cont_segmentos";
		$resSel = $this->queryArray($sel);
		return $resSel['rows'];
	}
	public function getCategorias(){
		$sel = "SELECT * from app_conceptos_gastos";
		$resSel = $this->queryArray($sel);
		return $resSel['rows'];
	}
	public function cuentasBancarias(){
		$sel = 'SELECT c.idbancaria,c.cuenta, b.Clave, b.nombre from bco_cuentas_bancarias c, cont_bancos b where c.idbanco=b.idbanco';
		$resSel = $this->queryArray($sel);
		return $resSel['rows'];
	}
	public function saveGasto($idSolicitud,$fecha,$importe,$formaPago,$cuentaGasto,$segmentoGasto,$sucursalGasto,$categoriaGasto,$referenciaGasto){
		$sel = "INSERT into app_solicitud_viaje_gastos(idSolicitud,fecha,importe,formaPago,cuenta,segmento,sucursal,categoria,referencia) values('".$idSolicitud."','".$fecha."','".$importe."','".$formaPago."','".$cuentaGasto."','".$segmentoGasto."','".$sucursalGasto."','".$categoriaGasto."','".$referenciaGasto."')";
		$res = $this->queryArray($sel);

		$sel2 = "SELECT g.*, fp.nombre as fp ,c.idbancaria,c.cuenta, b.Clave, b.nombre as banco, cg.concepto
			from app_solicitud_viaje_gastos g, forma_pago fp, bco_cuentas_bancarias c, cont_bancos b, app_conceptos_gastos cg
			where g.activo=1 and g.formaPago=fp.idFormapago and c.idbanco=b.idbanco and g.cuenta=c.idbancaria and cg.id=g.categoria and g.id=".$res['insertId'];
		$res2 = $this->queryArray($sel2);

		return   array('estatus' => true, 'newRow' => $res2['rows']);

	}
	public function eliminaGasto($id){
		$delete = "UPDATE app_solicitud_viaje_gastos set activo=0 where id=".$id;
		$resDel = $this->queryArray($delete);
		return  array('estatus' => true );
	}
	public function agregaPCoti($id){
		$select = "Select * from app_productos where id=".$id;
		$res = $this->queryArray($select);

		return $res['rows'];

	}
	public function productos(){
		$e = "SELECT * from app_productos";
		$res = $this->queryArray($e);

		return  array('productos' => $res['rows']);
	}
public function calculaImpuestos($stringTaxes){
        //echo $stringTaxes.'Z';
        //unset($_SESSION['prueba']);
        //idProdcuto-precio-cantidad-formula/idProducto2-precio2-cantidad2-formula2/
        //$productos = '41-100-1-0/42-50-1-2/44-100-1-1';
        $impuestos = array();
        $productos = explode('/', $stringTaxes);

        foreach ($productos as $key => $value) {
            $prod = explode('-', $value);
            if($prod[0]!=''){
                $idProducto = $prod[0];
                $precio = $prod[1];
                $cantidad = $prod[2];
                $formula = 1;//desc o asc 1 = ieps de los vinos , 2 = ieps de la gasolina
                $subtotal = $precio * $cantidad;
                $subtotalVenta +=$subtotal;
                //echo 'Subtotal='.$subtotal;
                if($formula==2){
                    $ordenform = 'ASC';
                }else{
                    $ordenform = 'DESC';
                }
               /* echo 'id='.$idProducto.'<br>';
                echo 'precio='.$precio.'<br>';
                echo 'cantidad='.$cantidad.'<br>';
                echo 'formula='.$formula; */
                $queryImpuestos = "select p.id,p.precio, i.valor, i.clave,pi.formula,i.nombre";
                $queryImpuestos .= " from app_impuesto i, app_productos p ";
                $queryImpuestos .= " left join app_producto_impuesto pi on p.id=pi.id_producto ";
                $queryImpuestos .= " where p.id=" . $idProducto . " and i.id=pi.id_impuesto ";
                $queryImpuestos .= " Order by pi.id_impuesto ".$ordenform;
                //echo $queryImpuestos.'<br>';
                $resImpues = $this->queryArray($queryImpuestos);
                //print_r($resImpues['rows']);
                //exit();
                foreach ($resImpues['rows'] as $key => $valueImpuestos) {
                        //echo 'Clave='.$valueImpuestos["clave"].'<br>';
                        if ($valueImpuestos["clave"] == 'IEPS') {
                            //echo 'Y'.$producto_impuesto;
                            $producto_impuesto = $ieps = (($subtotal) * $valueImpuestos["valor"] / 100);
                        } else {
                            if ($ieps != 0) {
                                $producto_impuesto = ((($subtotal + $ieps)) * $valueImpuestos["valor"] / 100);
                            } else {
                                $producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
                            }
                        }
                        //echo $valueImpuestos["nombre"].' '.$valueImpuestos["valor"].'='.$producto_impuesto.'<br>';
                        $totalImpestos += $producto_impuesto;
                        $impuestos['cargos']['impuestos'][$valueImpuestos["clave"]] = $impuestos['cargos']['impuestos'][$valueImpuestos["clave"]] + $producto_impuesto;
                        $impuestos['cargos']['impuestosPorcentajes'][$valueImpuestos["nombre"]] = $impuestos['cargos']['impuestosPorcentajes'][$valueImpuestos["nombre"]] + $producto_impuesto;
                }
                //echo 'total='.($subtotal+$producto_impuesto).'<br>';
                $ieps = 0;
            }


        }

        $impuestos['cargos']['total']= $totalImpestos + $subtotalVenta;
        $impuestos['cargos']['subtotal'] = $subtotalVenta;

        //print_r($impuestos);
        return $impuestos;
        //print_r($_SESSION['prueba']);
        //echo json_encode($_SESSION['prueba']);
        //unset($_SESSION['prueba');
    }

    public function gurdarCoti($productos,$idSoliCoti,$subTotal,$total,$obs){

        /*$fechaactual = date('Y-m-d H:i:s');
        $selctEmpleado = "SELECT usuario from accelog_usuarios where idempleado=".$_SESSION['accelog_idempleado'];
        $empleadoRes = $this->queryArray($selctEmpleado);
        $empleado = $empleadoRes['rows'][0]['usuario'];

        $idSuc = 1;
        $status ='Registrada';
        $user = $_SESSION['accelog_idempleado'];  */
       /*----- Total y subtotal -------------*/
       /* $token =explode("/", $productos);
        foreach ($token as $key => $value) {
            $token2 = explode('-',$value);
            if($token2[1]!='undefined' && $token2[2]!=''){
                $subtotal = $token2[1] * $token2[2];
                $total += $subtotal;
                $subtotal=0;
            }
        } */
       /* $queryInsert = "INSERT INTO mrp_orden_compra (fecha_pedido,elaborado_por,idSuc,idProveedor,estatus,idAlmacen)";
        $queryInsert.= " VALUES ('".$fechaactual."','".$empleado."','".$idSuc."','".$idProvedor."','".$status."','".$idAlmacen."');"; */
        /*$queryInsert = "INSERT INTO app_ocompra (id_proveedor,id_usrcompra,fecha,fecha_entrega,activo,subtotal, total,id_almacen) VALUES ";
        $queryInsert .="('".$idProvedor."','".$user."','".$fechaactual."','".$fecha_entrega."','1','".$subTotal."','".$total."','".$idAlmacen."')";
        //echo $queryInsert;
        $idOrCom = $this->queryArray($queryInsert);
        $idOrCom = $idOrCom['insertId']; */
        $upd = "UPDATE app_solicitud_viaje set observaciones='".$obs."', totalCoti='".$total."', estatus='2' where id=".$idSoliCoti;
        $res = $this->queryArray($upd);

        $token =explode("/", $productos);
        foreach ($token as $key => $value) {
            $token2 = explode('-',$value);
            if($token2[1]!='undefined' && $token2[2]!=''){
                /*$queryInserProd ="INSERT into mrp_producto_orden_compra (idOrden,cantidad,idUnidad,idProducto,ultCosto)";
                $queryInserProd.=" values('".$idOrCom."','".$token2[1]."','1','".$token2[0]."','".$token2[2]."')"; */
                $queryInserProd = "INSERT into app_cotizacion_viaje_prods (idSolicitud,idProducto,cantidad,precio,importe) ";
                $queryInserProd .="values ('".$idSoliCoti."','".$token2[0]."','".$token2[1]."','".$token2[2]."','".($token2[1]*$token2[2])."')";
                //echo $queryInserProd;
                $insertaproducto = $this->queryArray($queryInserProd);
            }
        }

         return array('status' => true);
    }
    public function pdfCotizacion($idSolicitud){
    	date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
    	$sele = "SELECT * from app_solicitud_viaje where id=".$idSolicitud;
    	$res = $this->queryArray($sele);
    	$cliente  = $res['rows'][0]['idCliente'];
    	$obs = $res['rows'][0]['observaciones'];
    	$totalCoti = $res['rows'][0]['totalCoti'];

    	$productos = $this->arrayProductos($idSolicitud);



    	$queryClient="SELECT c.nombre,c.direccion,c.colonia,c.email,c.cp, e.estado,m.municipio,c.rfc,c.num_ext,c.num_int , ppp.pais";
		$queryClient.=" from comun_cliente c left join estados e on c.idEstado=e.idestado left join municipios m on c.idMunicipio=m.idmunicipio left join paises ppp on c.idPais=ppp.idpais";
		$queryClient.=" where c.id=".$cliente;

		$result = $this->queryArray($queryClient);
		$Email = $result["rows"][0]["email"];
	  ////////DATOS EMISOR
		$queryOganizacion="SELECT o.nombreorganizacion,o.RFC,r.descripcion as regimen,o.domicilio,e.estado,m.municipio,o.cp,o.colonia,o.paginaweb,o.logoempresa ";
		$queryOganizacion.=" from organizaciones o, estados e,municipios m, nomi_regimenfiscal r ";
		$queryOganizacion.=" where o.idestado=e.idestado and o.idmunicipio=m.idmunicipio and o.idregfiscal = r.idregfiscal";
		$result2 = $this->queryArray($queryOganizacion);
		   // print_r($_SESSION["pedido"]["charges"]);
		//echo '('.$Email.')';
	   // var_dump($result2);

	   	unlink('../../modulos/cotizaciones/cotizacionesPdf/pedido_'.$idPedido.'.php');
		////////////////PDF
	   	$codMoneda = 'MX';

		include "../../modulos/SAT/PDF/COTIZACIONESPDF.php";
		$obj = new CFDIPDF( );

			$nrec = $result["rows"][0]["num_ext"].' Int.'.$result["rows"][0]["num_int"];
			$obj->datosCFD($idSolicitud, $fechaactual, 'Cotizacion', $codMoneda);
			$obj->lugarE('MEXICO');


			$obj->datosEmisor($result2["rows"][0]["nombreorganizacion"], $result2["rows"][0]["RFC"], $result2["rows"][0]["domicilio"], $result2["rows"][0]["estado"], $result2["rows"][0]["colonia"], $result2["rows"][0]["municipio"], $result2["rows"][0]["estado"], $result2["rows"][0]["cp"], "Mexico", '');

			$obj->datosReceptor($result["rows"][0]["nombre"], $result["rows"][0]["rfc"], $result["rows"][0]["direccion"] . $nrec, $result["rows"][0]["municipio"], $result["rows"][0]["colonia"], $result["rows"][0]["municipio"], $result["rows"][0]["estado"], $result["rows"][0]["cp"],$result["rows"][0]["pais"] );

			$obj->agregarConceptos($productos['productos']);
			//if (!isset($var)) {
				//$_SESSION['caja']['cargos']['impuestosPdf'] =  array();
			//}

			$obj->agregarTotal($productos["subtotal"], $totalCoti, $productos["impuestos"]);

			$obj->agregarMetodo('eeeeeeeeee', '', $codMoneda);
			//$obj->agregarSellos($datosTimbrado['csdComplemento'], $datosTimbrado['selloCFD'], $datosTimbrado['selloSAT']);
			$sssel = "SELECT leyenda_pedido from app_config_ventas";
			$rles = $this->queryArray($sssel);
			//echo $dataString;
			//echo '<br>';
			$string = explode('&',$dataString);
			$cont = 0;
			foreach ($string as $key => $value) {
				//echo $value.'<br>';
				$straux = explode('=',$value);
				$campo = '@'.$straux[0];
				$valor = str_replace('+', ' ', $straux[1]);
				//echo 'campo='.$campo.' Valor='.$valor.'<br>';
				//str_replace(search, replace, subject)
				if($cont > 0){
					$leyenda = str_replace($campo,$valor,$leyenda);
				}else{
					$leyenda = str_replace($campo,$valor,$rles['rows'][0]['leyenda_pedido']);
				}
				$cont++;
			}

			//echo $leyenda;
			//$leyenda = $rles['rows'][0]['leyenda_pedido'];
			$leyenda = '';
			$obj->agregarObservaciones($obs,$leyenda);

			$obj->generar("../../netwarelog/archivos/1/organizaciones/" . $result2["rows"][0]["logoempresa"] . "", 0);

			$obj->borrarConcepto();
			if ($Email != '') {

					require_once('../../modulos/phpmailer/sendMail.php');

					$mail->Subject = "Cotizacion";
					$mail->AltBody = "NetwarMonitor";
					$mail->MsgHTML('Envio de Cotizacion');

					$mail->AddAttachment('../../modulos/cotizaciones/cotizacionesPdf/cotizacion_'.$idPedido.".pdf");
					$mail->AddAddress($Email, $Email);


				 @$mail->Send();

						//unset($_SESSION['pedido']);
						//return array('status' => true);
				}else{
						//unset($_SESSION['pedido']);
						//return array('status' => false);
				}

			//exit();
				return  array('estatus' => true );


    }
    public function arrayProductos($idSolicitud){
    	$select = "SELECT p.id,p.nombre, p.codigo,c.cantidad,c.precio,c.importe,u.nombre as unidad
    			from app_cotizacion_viaje_prods c , app_productos p , app_unidades_medida u
				where p.id_unidad_venta=u.id and c.idProducto=p.id and c.idSolicitud=".$idSolicitud;
    	$res = $this->queryArray($select);
    	$ar = [];
    	$ar2 = [];
    	$subt = 0;
    	$iva  = 0;
    	foreach ($res['rows'] as $key => $value) {
    		$ar2['idProducto'] = $value['id'];
    		$ar2['codigo'] = $value['codigo'];
    		$ar2['nombre'] = $value['nombre'];
    		$ar2['unidad'] = $value['unidad'];
    		$ar2['precio'] = $value['precio'];
    		$ar2['cantidad'] = $value['cantidad'];
    		$ar2['importe'] = $value['importe'];
    		$subt += $value['importe'];
    		$ar[$value['id']] = $ar2;

    		$iva +=($value['importe']*0.16);
    	}
    	$impuestos['IVA']['16']['Valor'] = $iva;
    	//return $ar;
    	return  array('productos' =>$ar, 'subtotal' =>$subt, 'impuestos'=>$impuestos);
    }


    public function sendCajaOrden($idOrden){

    	$selVe = "SELECT idVenta from app_solicitud_viaje where id=".$idOrden;
        $res = $this->queryArray($selVe);

        if($res['rows'][0]['idVenta']!=''){

            return array('venta' => true);
        }

        $selectProductos = "SELECT idProducto, cantidad, precio from app_cotizacion_viaje_prods where idSolicitud=".$idOrden;
        $resultProductos= $this->queryArray($selectProductos);
        //var_dump($resultProductos);
        //$codigos = array();
        //$cantidad =
        foreach ($resultProductos['rows'] as $key => $value) {
            //echo 'X'.$value['idProducto'].'-'.$value['cantidad'].'X';
            $selectCodigo = "SELECT codigo from app_productos where id=".$value['idProducto'];
            $resultCodigo= $this->queryArray($selectCodigo);
            $codigos[$key]=array('codigo' => $resultCodigo['rows'][0]['codigo'], 'cantidad' => $value['cantidad'], 'precio' =>$value['precio']);

        }
        //print_r($codigos);
        /*$updatePedido = "UPDATE cotpe_pedido set status=4 where id=".$idPedido;
        $this->query($updatePedido); */
        return array('codigo' => $codigos);

    }


    function clientes(){
    	$query45 = "SELECT * from comun_cliente";
		$result5 = $this->queryArray($query45);

		return  array('clientes' => $result5['rows']);
    }

    function lista_aeronaves()
    {
    	return $this->query("SELECT id, aeronave, tipo FROM app_catalogo_aeronaves WHERE activo = 1");
    }

    function listaCategorias()
    {
    	return $this->query("SELECT c.*, (SELECT codigo FROM cont_coin WHERE coin_id = c.moneda) AS monedaLt FROM app_categoria_aeronaves c WHERE c.activo = 1");
    }

    function num_viaje($id)
    {
    	$res = $this->query("SELECT num_viaje FROM app_solicitud_viaje WHERE id = $id");
    	$res = $res->fetch_assoc();
    	return $res['num_viaje'];
    }

    function infocat($idcat)
    {
    	$res = $this->query("SELECT c.importe,(SELECT codigo FROM cont_coin WHERE coin_id = c.moneda) AS cod_moneda, c.moneda AS idMoneda, CONCAT('(',c.clave,') ',c.nombre) AS categoria, id AS idcategoria FROM app_categoria_aeronaves c WHERE c.id = $idcat");
    	$res = $res->fetch_assoc();
    	return $res;
    }

    function cuentas()
    {
    	//Cuentas contables
    	//return $this->query("SELECT account_id, manual_code, description FROM cont_accounts WHERE removed = 0 AND main_account = 3");

    	//Cuentas bancarias
    	return $this->query("SELECT c.idbancaria, c.cuenta, (SELECT nombre FROM cont_bancos WHERE  idbanco = c.idbanco) AS nom_banco FROM bco_cuentas_bancarias c WHERE c.activo = -1 AND c.cancelada = 0;");
    }

    function formaPago()
    {
    	return $this->query("SELECT idFormapago,nombre FROM forma_pago WHERE activo = 1");
    }

    function guardar_gastos($vars)
    {
    	$myQuery = "";
    	$vars['arreglo_temps'] = json_decode($vars['arreglo_temps']);
    	if(!empty($vars['arreglo_temps'])){
    		$vars['arreglo_temps'] = json_encode($vars['arreglo_temps']);
    		$vars['arreglo_temps'] = json_decode($vars['arreglo_temps']);
    		foreach($vars['arreglo_temps'] as $arr){
    			$myQuery .= "INSERT INTO app_solicitud_viaje_gastos(id, idSolicitud, fecha, importe, formaPago, cuenta, segmento, sucursal, referencia, cobroCliente, idMoneda, tipoCambio, idcategoria, activo)
    						VALUES(0,".$vars['idSolicitud'].",'".$arr[2]."',".$arr[4].",".$arr[8].",".$arr[7].",1,1,'".$arr[9]."',0,".$arr[5].",'".$arr[6]."',".$arr[3].",1);";
    		}
    		//GUARDA LOS REGISTROS NUEVOS
    	}
    	$vars['arreglo'] = json_decode($vars['arreglo']);
    	if(!empty($vars['arreglo'])){
    		$vars['arreglo'] = json_encode($vars['arreglo']);
    		$vars['arreglo'] = json_decode($vars['arreglo']);
    		foreach($vars['arreglo'] as $arr){
    			$myQuery .= "UPDATE app_solicitud_viaje_gastos
    							SET fecha = '".$arr[2]."',
    							importe = ".$arr[4].",
    							formaPago = ".$arr[8].",
    							cuenta = ".$arr[7].",
    							referencia = '".$arr[9]."',
    							tipoCambio = '".$arr[6]."',
    							idMoneda = '".$arr[5]."'
    							WHERE id = ".$arr[0].";";
    		}
    		//ACTUALIZA LOS REGISTROS YA EXISTENTES
    	}
    	$this->multi_query($myQuery);
    }

    function autorizar($idsolicitud,$aprobado)
    {
    	if(!intval($aprobado))
    		$aprobado = 2;
    	return $this->query("UPDATE app_solicitud_viaje SET aprobado = $aprobado WHERE id = $idsolicitud");
    }
}
