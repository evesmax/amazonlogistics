
<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL
require("models/connection_sqli_manual.php"); // funciones mySQLi

class ReportesModel extends Connection
{
	public function get_clientes()
	{
		$myQuery = "SELECT id, codigo, nombre FROM comun_cliente WHERE borrado = 0;";
		$res = $this->query($myQuery);
		return $res;
	}

	public function get_sucursales()
	{
		$myQuery = "SELECT idSuc, nombre FROM mrp_sucursal WHERE activo = -1;";
		$res = $this->query($myQuery);
		return $res;
	}

	public function get_matriculas()
	{
		$myQuery = "SELECT id, aeronave, tipo FROM app_catalogo_aeronaves WHERE activo = 1;";
		$res = $this->query($myQuery);
		return $res;
	}
	public function get_formas_pago()
	{
		$myQuery = "SELECT idFormaPago AS id, nombre, claveSat AS clave FROM forma_pago WHERE activo = 1;";
		$res = $this->query($myQuery);
		return $res;
	}

	public function cortesTerminados($idSuc){
		$sql = "SELECT cc.idCortecaja id, cc.fechainicio inicio, cc.fechafin fin  FROM app_pos_corte_caja cc
		LEFT JOIN administracion_usuarios u on u.`idempleado` = cc.`idempleado`
		WHERE u.idSuc = '$idSuc';";
		$res = $this->queryArray($sql);
		return $res['rows'];
	}

	public function gastoDatos($vars)
	{
		$rango = explode(' / ',$vars['rango']);
		$where = '';
		if(intval($vars['matricula']))
			$where .= " AND v.idaeronave = " . $vars['matricula'] . " ";

		if($vars['no_vuelo'] != '')
			$where .= " AND v.num_viaje LIKE '%" . $vars['no_vuelo'] . "%' ";

		$myQuery = "SELECT v.id AS idViaje, v.fechaIda,
		(SELECT nombre FROM comun_cliente WHERE id = v.idCliente) AS cliente,
		(SELECT clave FROM app_destinos WHERE id = v.origen) AS origen,
		(SELECT clave FROM app_destinos WHERE id = v.destino) AS destino,
		(SELECT CONCAT('(',clave,') ',nombre) AS concepto FROM app_categoria_aeronaves WHERE  id = vg.idcategoria) AS concepto,
		vg.importe, vg.formaPago, v.tiempoTotal, vg.idMoneda, vg.tipoCambio,
		IFNULL((SELECT SUM(monto) FROM app_pos_venta_pagos WHERE idVenta = v.idVenta AND idFormapago = 1),0) AS ingresoOpE, 
		IFNULL((SELECT SUM(monto) FROM app_pos_venta_pagos WHERE idVenta = v.idVenta AND idFormapago = 6),0) AS ingresoOpC, 
		IFNULL((SELECT CONCAT(moneda,'/',tipo_cambio) FROM app_pos_venta WHERE idVenta = v.idVenta),'1/1') AS ingresoOpMTC 

					FROM
						app_solicitud_viaje_gastos vg
					INNER JOIN
						app_solicitud_viaje v ON v.id = vg.idSolicitud
					WHERE
						v.idCliente = ".$vars['cliente']."
						AND v.fecha BETWEEN '".$rango[0]." 00:00:00' AND '".$rango[1]." 23:59:59'
						AND activo = 1 $where 
						ORDER BY v.id_semana, v.fecha, v.fechaIda, v.id";
						//AND vg.formaPago = " . $vars['forma'] ."
		$res = $this->query($myQuery);
		return $res;
	}

	public function cortes(){
		$sql = "SELECT idCortecaja id, fechainicio inicio, fechafin fin FROM app_pos_corte_caja;";
		$res = $this->queryArray($sql);
		return $res['rows'];
	}
	
	public function getCut($init,$end,$onlyShow,$iduser,$idcorte ){

		$iduser = $_SESSION['accelog_idempleado'];

		// $selIniCaj = "SELECT max(fecha) as fechaInicio from app_pos_inicio_caja where idUsuario=".$iduser;
		// $resFechaIni = $this->queryArray($selIniCaj);

		$SelemontoInicial ="SELECT monto from app_pos_inicio_caja where idUsuario='".$iduser."' and fecha='".$init."'";
		$resMon = $this->queryArray($SelemontoInicial);

		$montoInical = $resMon['rows'][0]['monto'];

		//ch@
		$sql = 'SELECT "Ventas" AS Flag, v.idVenta, v.fecha, c.nombre, ROUND(v.descuentoGeneral,2) as descuentoGeneral, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp LEFT JOIN view_forma_pago vfp on vfp.idFormapago = vp.idFormapago WHERE vfp.claveSat = 01 AND v.idVenta = vp.idVenta ) AS Efectivo , 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 4 AND v.idVenta = vp.idVenta ) AS TCredito, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 5 AND v.idVenta = vp.idVenta ) AS TDebito, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 6 AND v.idVenta = vp.idVenta ) AS CxC, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 2 AND v.idVenta = vp.idVenta ) AS Cheque, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp LEFT JOIN view_forma_pago vfp on vfp.idFormapago = vp.idFormapago WHERE vfp.claveSat = 03 AND v.idVenta = vp.idVenta ) AS Trans, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 8 AND v.idVenta = vp.idVenta ) AS SPEI, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 3 AND v.idVenta = vp.idVenta ) AS TRegalo, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 12 AND v.idVenta = vp.idVenta ) AS TVales, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 9 AND v.idVenta = vp.idVenta ) AS Ni, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 21 AND v.idVenta = vp.idVenta ) AS Otros, 
				( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto)) FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 26 AND v.idVenta = vp.idVenta ) AS Cortesia, 
				REPLACE(FORMAT(v.cambio, 2),",","") as cambio, 
				REPLACE(FORMAT(v.montoimpuestos, 2), ",", "") AS Impuestos, 
				REPLACE(FORMAT((v.monto - v.montoimpuestos), 2 ), ",", "") AS Monto, 
				REPLACE(FORMAT(v.monto, 2), ",", "") AS Importe, 
				v.estatus estatus, (d.estatus) condevolucion 
				FROM app_pos_venta v 
				LEFT JOIN app_pos_venta_pagos p ON p.idVenta = v.idVenta 
		        LEFT JOIN comun_cliente c ON v.idCliente = c.id 
                LEFT JOIN app_devolucioncli d ON v.idVenta = d.id_ov
                WHERE v.idEmpleado = ' . $iduser . ' AND v.fecha BETWEEN "' . $init . '" AND "' . $end . '" 
                GROUP BY v.idVenta ;';
        $resVentas = $this->queryArray($sql);

		//echo $sql.'<br>';
		///Obtiene los productos vendidos
		$sql2 = 'SELECT ';
		$sql2 .= '   "Productos" AS Flag, ';
		$sql2 .= '   p.codigo, ';
		$sql2 .= '   p.nombre, ';
		$sql2 .= '   sum(vp.cantidad) AS Cantidad, ';
		$sql2 .= '   REPLACE(FORMAT(vp.preciounitario,2), ",", "") AS preciounitario, ';
		$sql2 .= '   REPLACE(FORMAT(sum(vp.montodescuento), 2), ",", "") AS Descuento, ';
		$sql2 .= '   REPLACE(FORMAT(sum(vp.impuestosproductoventa), 2), ",", "") AS Impuestos, ';
		//$sql .= ' REPLACE(FORMAT(sum( (vp.subtotal + vp.impuestosproductoventa) - vp.descuento ), 2), ",", "") AS Subtotal, ';
		$sql2 .= '   REPLACE(FORMAT(sum(vp.total), 2), ",", "") AS Subtot, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00, ';
		$sql2 .= '   0.00 ';
		$sql2 .= 'FROM ';
		$sql2 .= '   app_pos_venta_producto vp ';
		$sql2 .= '   INNER JOIN app_productos p ON vp.idProducto = p.id ';
		$sql2 .= 'WHERE ';
		$sql2 .= '   vp.idVenta IN(SELECT idVenta from app_pos_venta v WHERE v.idEmpleado = ' . $iduser . ' AND v.estatus = 1 AND v.fecha BETWEEN "' . $init . '" AND "' . $end . '") ';
		$sql2 .= 'GROUP BY ';
		$sql2 .= '   p.id, vp.preciounitario ';
		$resProductos = $this->queryArray($sql2);
		/*var_dump($resProductos);
		echo $sql2;
		print_r($resVentas['rows']);
		echo '<br/><br/><br/>';
		print_r($resProductos['rows']); */
		//echo $sql2;
		//retiros de caja
		$sql3 = "SELECT r.id,r.cantidad,r.concepto, u.usuario, r.fecha from app_pos_retiro_caja r, accelog_usuarios u where r.idempleado=u.idempleado and fecha between  '".$init."' and '".$end."' and r.idempleado=".$iduser;
		//echo $sql3;
		$resRetiros = $this->queryArray($sql3);
		////Abonos de Caja
		$sql56 = "SELECT r.id,r.cantidad,r.concepto, u.usuario, r.fecha, r.id_forma_pago from app_pos_abono_caja r, accelog_usuarios u where r.idempleado=u.idempleado and fecha between  '".$init."' and '".$end."' and r.idempleado=".$iduser;
		//echo $sql56;
		$resAbonos = $this->queryArray($sql56);


		/////Tipo de Tarjetas
		//$sql4 = "SELECT sum(p.monto) as total, t.tarjeta from app_pos_venta v, app_pos_venta_pagos p, app_tarjetas t where p.tarjeta=t.id group by t.tarjeta;";
		//$sql4 = "SELECT sum(p.monto) as total, t.tarjeta from app_pos_venta v, app_pos_venta_pagos p, app_tarjetas t where v.idVenta=p.idVenta  and v.fecha between '".$init."' and '".$end."' and p.tarjeta=t.id and v.idEmpleado='".$iduser."' group by t.tarjeta;";
		$sql4 = "SELECT sum(p.monto) as total,  case when p.tarjeta = 0 then 'No identificada' else t.tarjeta end as tarjeta
				from app_pos_venta v
				inner join app_pos_venta_pagos p on p.idVenta= v.idVenta
				left join  app_tarjetas t on p.tarjeta=t.id
				where  v.fecha between '".$init."' and '".$end."' and v.idEmpleado='".$iduser."' and p.idFormapago in (4,5)
				group by t.tarjeta;";
		$resTarjetas = $this->queryArray($sql4);


		foreach ($resVentas['rows'] as $key => $value) {
		   $x = $value['Efectivo'] - $value['cambio'];
		   $totalX += $x;
		}

		foreach ($resVentas['rows'] as $key => $value) {
		   $x2 = $value['Importe'];
		   $totalX2 += $x2;
		}


		foreach ($resRetiros['rows'] as $key1 => $value1) {
		   $totalRetiros += $value1['cantidad'];
		}
		foreach ($resAbonos['rows'] as $key1 => $value1) {
		   $totalAbonos += $value1['cantidad'];
		}

		$saldoDisponible = ($montoInical+$totalX+$totalAbonos) - $totalRetiros;

		$totalof = $totalX - $saldoDisponible;



		$sql  = 'SELECT ';
		$sql .= '   "Ventas" AS Flag, ';
		$sql .= '   v.idVenta, ';
		$sql .= '   v.fecha, ';
		$sql .= '   c.nombre, ';
		$sql .= '   ROUND(v.descuentoGeneral,2) as descuentoGeneral, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 1 AND v.idVenta = vp.idVenta )  AS Efectivo , ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 4 AND v.idVenta = vp.idVenta ) AS TCredito, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 5 AND v.idVenta = vp.idVenta ) AS TDebito, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 6 AND v.idVenta = vp.idVenta ) AS CxC, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 2 AND v.idVenta = vp.idVenta ) AS Cheque, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 7 AND v.idVenta = vp.idVenta ) AS Trans, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 8 AND v.idVenta = vp.idVenta ) AS SPEI, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 3 AND v.idVenta = vp.idVenta ) AS TRegalo, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 9 AND v.idVenta = vp.idVenta ) AS Ni, ';
		$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 25 AND v.idVenta = vp.idVenta ) AS TVales, ';
		//$sql .= '     ';
		$sql .= '   REPLACE(FORMAT(v.cambio, 2),",","") as cambio, ';
		$sql .= '   REPLACE(FORMAT(v.montoimpuestos, 2), ",", "") AS Impuestos, ';
		$sql .= '   REPLACE(FORMAT((v.monto - v.montoimpuestos), 2 ), ",", "") AS Monto, ';
		$sql .= '   REPLACE(FORMAT(v.monto, 2), ",", "") AS Importe ';
		$sql .= 'FROM ';
		$sql .= '   app_pos_venta v ';
		$sql .= '   LEFT JOIN app_pos_venta_pagos p ON p.idVenta = v.idVenta ';
		$sql .= '   LEFT JOIN comun_cliente c ON v.idCliente = c.id ';
		$sql .= 'WHERE ';
		$sql .= '   v.idEmpleado = ' . $iduser . ' ';
		$sql .= '   AND ';
		$sql .= '   v.fecha BETWEEN ';
		$sql .= '   "' . $init . '" ';
		$sql .= '   AND ';
		$sql .= '   "' . $end . '" ';
		$sql .= 'GROUP BY ';
		$sql .= '   v.idVenta ';
		$resVentasTmp = $this->queryArray($sql);

		$inIdVentas = "( ";
		foreach ($resVentasTmp['rows'] as $key => $value) {
			$idVenTmp = $value['idVenta'];
			if($key != 0) $inIdVentas .= ",";
			$inIdVentas .= " '$idVenTmp' ";
		}
		if( $inIdVentas === "( ")
			$inIdVentas .= " '-1'  )";
		else
			$inIdVentas .= " )";

		$efectivo = "SELECT	p.idVenta, v.monto
					FROM	app_pos_venta_pagos p
					LEFT JOIN	app_pos_venta v ON p.idVenta = v.idVenta
					WHERE	p.idVenta IN $inIdVentas AND p.idFormapago = '1'";
		$efectivo = $this->queryArray($efectivo);

		$cancelaciones = "SELECT	v.idVenta, v.monto
						FROM	app_pos_venta v
						WHERE	v.idVenta IN $inIdVentas AND v.estatus = '0'";
		$cancelaciones = $this->queryArray($cancelaciones);

		$propinas = "SELECT	p.id_venta, e.nombre, v.fecha, SUM(p.monto) as monto, p.metodo_pago, p.tipo_tarjeta
					FROM	com_propinas p
					LEFT JOIN app_pos_venta v ON v.idVenta = p.id_venta
					LEFT JOIN empleados e ON e.idempleado = v.idEmpleado
					WHERE	p.id_venta IN $inIdVentas group by p.id_venta, p.metodo_pago";
		//print_r($propinas);
		$propinas = $this->queryArray($propinas);

		$garantias = "SELECT	g.id_venta, vp.total
					FROM	app_pos_garantia_movimientos g
					LEFT JOIN	app_pos_venta_producto vp ON g.id_venta_producto = vp.idventa_producto
					WHERE	g.id_venta IN $inIdVentas AND g.atendida = '1'";
		$garantias = $this->queryArray($garantias);

		$devoluciones = "SELECT	d.id_ov, d.total
					FROM	app_devolucioncli d
					WHERE	d.id_ov IN $inIdVentas";
		$devoluciones = $this->queryArray($devoluciones);

		$descuentos = "SELECT	v.idVenta, v.descuentoGeneral
					FROM	app_pos_venta v
					WHERE	v.idVenta IN $inIdVentas";
		$descuentos = $this->queryArray($descuentos);

		$facturas = "SELECT	v.idVenta, v.monto
					FROM	app_pos_venta v
					RIGHT JOIN	app_respuestaFacturacion rf ON v.idVenta = rf.idSale
					WHERE	v.idVenta IN $inIdVentas ";
		$facturas = $this->queryArray($facturas);

		$impuestos = "SELECT	v.idVenta, v.montoimpuestos
					FROM	app_pos_venta v
					WHERE	v.idVenta IN $inIdVentas";
		$impuestos = $this->queryArray($impuestos);
		$propinas_2 = [];
		foreach ($propinas['rows'] as $key => $value) {
			$propinas_2[$value['id_venta']]['id_venta'] = $value['id_venta'];
			$propinas_2[$value['id_venta']]['nombre'] = $value['nombre'];
			$propinas_2[$value['id_venta']]['fecha'] = $value['fecha'];
			if($value['metodo_pago'] == 1){
				$propinas_2[$value['id_venta']]['efectivo'] = $propinas_2[$value['id_venta']]['efectivo'] + $value['monto'];

			} else {
				if($value['tipo_tarjeta'] == 1){
					$propinas_2[$value['id_venta']]['visa'] = $propinas_2[$value['id_venta']]['visa'] + $value['monto'];
				} else if($value['tipo_tarjeta'] == 2){
					$propinas_2[$value['id_venta']]['mc'] = $propinas_2[$value['id_venta']]['mc'] + $value['monto'];
				} else if($value['tipo_tarjeta'] == 3){
					$propinas_2[$value['id_venta']]['amex'] = $propinas_2[$value['id_venta']]['amex'] + $value['monto'];
				}
			}
			$propinas_2[$value['id_venta']]['total'] = $propinas_2[$value['id_venta']]['total'] + $value['monto'];
		}
		foreach ($propinas_2 as $key => $value) {
			if(empty($value['efectivo'])){
				$propinas_2[$key]['efectivo'] = 0;
			}
			if(empty($value['visa'])){
				$propinas_2[$key]['visa'] = 0;
			}
			if(empty($value['mc'])){
				$propinas_2[$key]['mc'] = 0;
			}
			if(empty($value['amex'])){
				$propinas_2[$key]['amex'] = 0;
			}
			if(empty($value['total'])){
				$propinas_2[$key]['total'] = 0;
			}
		}

	$sql8 = "SELECT @i:=@i+1 numeroCorte,
			if(@i = 1,
			(select fechainicio from app_pos_corte_caja where idCortecaja = cp.`id_corte`),
			(2)
			) fechaInicio,
			cp.id_corte_parcial id,
			cp.fecha, u.nombre,
			if(turno = 1, 'Matutino','Vespertino') turno, 
			total_ventas, disponible, total_reportado reportado, (disponible - total_reportado) diferencia 
			FROM app_pos_corte_parcial cp
			LEFT JOIN administracion_usuarios u on u.idempleado = cp.id_empleado
			, (SELECT @i:=0) r
                WHERE id_corte = '$idcorte';";
    $cortesP = $this->queryArray($sql8);

    foreach ($cortesP['rows'] as $k => $v) {
    	$fecha = $v['fecha'];
    		if($v['numeroCorte'] == 1){
    			$newArra[] = array(
                    id           => $v['numeroCorte'],
                    fechainicio  => $v['fechaInicio'],
                   	fechafin     => $v['fecha'],
                   	nombre     	 	=> $v['nombre'],
                    turno     	  	=> $v['turno'],
                    total_ventas    => $v['total_ventas'],
                    disponible     	=> $v['disponible'],
                    reportado     	=> $v['reportado'],
                    diferencia     	=> $v['diferencia'],
                );
    		}else{
    			$newArra[] = array(
                    id           	=> $v['numeroCorte'],
                    fechainicio  	=> $fechaAnt,
                    fechafin     	=> $v['fecha'],
                    nombre     	 	=> $v['nombre'],
                    turno     	  	=> $v['turno'],
                    total_ventas    => $v['total_ventas'],
                    disponible     	=> $v['disponible'],
                    reportado     	=> $v['reportado'],
                    diferencia     	=> $v['diferencia'],
                );
    		}
    	$fechaAnt = $fecha;	    	
    }
    foreach ($newArra as $ke => $va) {
    	foreach ($resVentas['rows'] as $ke2 => $va2) {
    		if($va['fechainicio'] < $va2['fecha'] && $va['fechafin'] > $va2['fecha']){
    			$disponible = $va2['Efectivo'];
    			$diferencia = $disponible - $va['reportado'];
    			$newArra2[] = array(
                    id           	=> $va['id'],
                    fechainicio  	=> $va['fechainicio'],
                    fecha     		=> $va['fechafin'],
                    nombre     		=> $va['nombre'],
                    turno     		=> $va['turno'],
                    total_ventas    => $va['total_ventas'],
                    disponible     	=> $disponible,
                    reportado     	=> $va['reportado'],
                    diferencia     	=> $diferencia,
                    efectivo     	=> $va2['Efectivo'],
                );
    		}
    	}
    }  


	return  array( 'ventas' => $resVentas['rows'] ,'productos' => $resProductos['rows'], 'retiros' => $resRetiros['rows'], 'desde' => $init, 'hasta' => $end, 'montoInical' => $montoInical, 'monto_ventas' => $totalX, 'saldoDisponible' => $saldoDisponible, 'ventas_total' => $totalX2, 'totalof' => $totalof, 'tarjetas' => $resTarjetas['rows'], 'abonos' => $resAbonos['rows'],
		'cancelaciones' => $cancelaciones['rows'], 'efectivo' => $efectivo['rows'], 'propinas' => $propinas_2, 'garantias' => $garantias['rows'], 'devoluciones' => $devoluciones['rows'], 'descuentos' => $descuentos['rows'], 'facturas' => $facturas['rows'], 'impuestos' => $impuestos['rows'], 'cortesP' => $newArra2 ) ;
	}
}
