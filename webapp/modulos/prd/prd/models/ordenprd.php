<?php

class OrdenPrdModel extends ListadoPrdModel {

	function getLastOrden() {
		$myQuery = "SELECT if(MAX(id) is NULL,1,MAX(id)+1) as id from prd_orden_produccion;";
		$nreq = $this -> query($myQuery);
		return $nreq;
	}

	function getEmpleados() {
		$myQuery = "SELECT a.idEmpleado as idempleado, concat(a.nombreEmpleado,' ',a.apellidoPaterno,' ',a.apellidoMaterno) as nombre, b.nombre as nomarea FROM nomi_empleados a
	    left join app_area_empleado b on b.id=a.id_area_empleado ORDER BY a.nombreEmpleado;";
		$empleados = $this -> query($myQuery);
		return $empleados;
	}

	function addProductoProduccion($idProducto) {
		$myQuery = "SELECT a.id, a.codigo, if(a.descripcion_corta='',a.nombre,a.descripcion_corta) as descripcion_corta, a.precio as costo, x.clave, a.tipo_producto,if(a.minimoprod is null,0,a.minimoprod) as minimo, a.factor FROM app_productos a
            INNER join app_unidades_medida x on x.id=a.id_unidad_venta
            WHERE a.id='$idProducto'  group by a.id;";

		$producto = $this -> query($myQuery);
		return $producto;
	}

	function getProductos5() {
		$myQuery = "SELECT id, nombre FROM app_productos WHERE (tipo_producto='8' or tipo_producto='9') ORDER BY nombre;";
		$productos = $this -> query($myQuery);
		return $productos;
	}

	function getUsuario() {
		session_start();
		$idusr = $_SESSION['accelog_idempleado'];
		$myQuery = "SELECT concat(nombre,' ',apellido1) as username, idempleado from empleados where idempleado='$idusr';";
		$nreq = $this -> query($myQuery);
		return $nreq;
	}

	function getSucursales() {
		$myQuery = "SELECT a.id_sucursal as idSuc, b.nombre as nombre from app_almacenes a
	     LEFT JOIN mrp_sucursal b on b.idSuc=a.id_sucursal group by a.id_sucursal order by b.nombre;";
		$nreq = $this -> query($myQuery);
		return $nreq;
	}

	function editarordenp($idop) {

		$myQuery = "SELECT a.id, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe, d.idSuc as idsuc, a.solicitante as idsol,d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as username, a.estatus, a.prioridad, a.observaciones, b.idempleado,a.solicitante as idsol, concat('(',f.codigo,') ',f.nombre) as nombre, e.cantidad, f.peso_dimension,a.lote 
        FROM prd_orden_produccion a 
        INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
        left JOIN mrp_sucursal d on d.idSuc=a.id_sucursal
        left JOIN prd_orden_produccion_detalle e on e.id_orden_produccion=a.id
        left JOIN app_productos f on f.id=e.id_producto
        WHERE a.id='$idop';";
		$datosReq = $this -> query($myQuery);
		return $datosReq;

	}

	function listar_pasos_op($idop) {
		$sql = "SELECT a.id as id_paso, a.descripcion as nombre_paso, a.id_producto, b.id as id_accion_producto, c.id as id_accion, if(b.alias='',c.nombre,b.alias) as nombre_accion, c.tiempo_hrs, d.nombre, if(e.id is null,0,1) as pasorealizado, b.tipo 
        from prd_pasos_producto a
        inner join prd_pasos_acciones_producto b on b .id_paso=a.id
        inner join prd_acciones c on c.id=b.id_accion
        inner join app_productos d on d.id=a.id_producto
        left join prd_ini_proceso e on e.id_oproduccion='$idop' and e.id_paso=b.id_paso and e.id_accion=c.id  and e.id_accion_producto = b.id
        where a.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop') order by b.id asc, c.id asc;";
		$result = $this -> queryArray($sql);
		return $result;
	}

	function buscaAgrupadas($idop) {

		$myQuery = "SELECT id,dependencia from prd_orden_produccion where id ='$idop';";
		$r1 = $this -> queryArray($myQuery);
		$depende = $r1['rows'][0]['dependencia'];

		if ($depende == 0) {
			$idPadre = $idop;
		} else {
			$exp = explode('-', $depende);
			$idPadre = $exp[0];
		}
		$myQuery = "SELECT id from prd_orden_produccion where id='" . $idPadre . "' and estatus=4 UNION ALL SELECT id from prd_orden_produccion where estatus=4 and dependencia like '" . $idPadre . "%';";
		$prodsReq = $this -> queryArray($myQuery);
		return $prodsReq;

	}

	function sqlPaso4($idop) {

		$myQuery = "SELECT c.idEmpleado, concat(c.nombreEmpleado,' ',c.apellidoPaterno) as nombre, maquinaria, a.id as idmaq
        FROM prd_personal_detalle a 
        INNER JOIN prd_personal b ON b.id=a.id_personal
        INNER JOIN nomi_empleados c ON c.idEmpleado=a.id_empleado
        WHERE b.id_oproduccion='$idop';";
		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function sqlPaso6($idop) {

		$myQuery = "SELECT b.no_lote, b.fecha_fabricacion, b.fecha_caducidad
        FROM prd_lote_detalles a 
        INNER JOIN app_producto_lotes b ON b.id=a.id_lote
        WHERE a.id_oproduccion='$idop';";
		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function sqlPaso15($idop) {

		$myQuery = "SELECT * FROM prd_costo_produccion WHERE id_oproduccion='$idop';";
		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function productosOp($idop, $m) {

		if ($m == 1) {
			$myQuery = "SELECT a.*, c.id, c.codigo, c.nombre as nomprod, c.series, c.lotes, c.pedimentos, c.precio as precioorig, x.clave,c.minimoprod as minimos,c.insumovariable
	        from prd_orden_produccion_detalle a
	        INNER JOIN app_productos c on c.id = a.id_producto
	        INNER join app_unidades_medida x on x.id=c.id_unidad_venta
	        WHERE a.id_orden_produccion='$idop' group by a.id;";

		} else {
			$myQuery = "SELECT c.id, c.codigo, c.nombre as nomprod, a.cantidad, c.series, c.lotes, c.pedimentos, if(a.precio is null,0,a.precio) as costo,  if(sum(ee.cantidad) is null,0,sum(ee.cantidad)) as cantidadr, a.id_lista, c.precio as precioorig, x.clave, a.caracteristica, c.tipo_producto,c.minimoprod as minimos from app_requisiciones_datos_venta a
            INNER JOIN app_productos c on c.id = a.id_producto
            left join app_envios_datos ee on ee.id_envio='$idEnv'
             INNER join app_unidades_medida x on x.id=c.id_unidad_venta
            WHERE a.id_requisicion='$idReq' group by a.id;";
		}

		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function productosOpExplosion($idop) {

		$myQuery = "SELECT
        p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
        p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
        (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
        (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti, p.insumovariable,x.cantidad as cantproduct,
		
		 (select sum(e.cantidad*y.cantidad) FROM app_productos r INNER JOIN app_producto_material e ON r.id=e.id_material LEFT JOIN app_unidades_medida g ON g.id=r.id_unidad_compra                
		INNER JOIN prd_orden_produccion_detalle y on y.id_orden_produccion= $idop AND e.id_producto=y.id_producto
        WHERE
        r.status=1
        AND
        e.id_producto in  (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion=$idop) and g.clave =u.clave) as cantidadunidad
       
        FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
        pro.id_producto=p.id
        INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$idop' AND m.id_producto=x.id_producto
        WHERE
        p.status=1
        AND
        m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop') group by p.id;";

		$prodsReq = $this -> queryArray($myQuery);
		return $prodsReq;

	}

	function sqlPaso2($idop, $idproducto) {

		$myQuery = "SELECT if(b.cantidad is null,0,b.cantidad) as cantUti
        FROM prd_utilizados a 
        INNER JOIN prd_utilizados_detalle b ON b.id_utilizado=a.id
        WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function sqlPaso3($idop, $idproducto) {

		$myQuery = "SELECT if(b.peso is null,0,b.peso) as pesoUti
        FROM prd_peso a 
        INNER JOIN prd_peso_detalle b ON b.id_peso=a.id
        WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function sqlPaso14($idop, $idproducto) {

		$myQuery = "SELECT if(b.cantidad is null,0,b.cantidad) as merma
        FROM prd_merma a 
        INNER JOIN prd_merma_detalle b ON b.id_merma=a.id
        WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
		$prodsReq = $this -> query($myQuery);
		return $prodsReq;

	}

	function getExistencias($idProducto, $caracteristicas) {
		$caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
		if ($caracteristicas != '0') {
			$carac = " AND id_producto_caracteristica =\"" . $caracteristicas . "\" ";
		} else {
			$carac = '';
		}
		$myQuery2 = "SELECT a.id,a.codigo_manual, a.codigo_sistema, a.nombre, 
		@e := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = a.id AND id_producto
		 = " . $idProducto . " " . $carac . "  AND id_pedimento = 0 AND id_lote = 0  ) AS entradas,
		@s := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = a.id AND id_producto
		 = " . $idProducto . " " . $carac . "   AND id_pedimento = 0 AND id_lote = 0  ) AS salidas,
		(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
		FROM app_almacenes a WHERE a.activo = 1 and a.id=1
		ORDER BY a.codigo_sistema;";

		$totpedis = $this -> queryArray($myQuery2);
		$cant = 0;
		foreach ($totpedis['rows'] as $k2 => $v2) {
			if ($v2['cantidad'] > 0) {
				$arrPedis[] = array('idAlmacen' => $v2['id'] . '-' . $v2['cantidad'] . '-#*-' . $v2['nombre'], 'cantidad' => $v2['cantidad'], 'almacen' => $v2['nombre']);
			}
		}
		return $arrPedis;

	}

	function proveedoresCostoOP($proveedores) {
		$myQuery = "SELECT a.costo, a.id_proveedor, b.razon_social FROM app_costos_proveedor a inner join mrp_proveedor b on b.idPrv=a.id_proveedor where a.id in($proveedores);";
		$datosReq = $this -> query($myQuery);
		return $datosReq;

	}

	function getAlmacen($idop) {
		$myQuery = "SELECT a.id as idalmacen from app_almacenes a 
		inner join prd_orden_produccion c on c.id_sucursal=a.id_sucursal
		WHERE c.id='$idop' limit 1;";

		$p = $this -> queryArray($myQuery);
		return $p;
	}

	function costoOpInv($idop) {

		$myQuery = "SELECT
                sum(pro.costo) as costo
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                WHERE
                p.status=1
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop');";

		$p = $this -> queryArray($myQuery);
		return $p;

	}

	function productosOpExplosionProceso($idop, $idap) {//krmn

		$myQuery = "SELECT
            p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
            p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
            (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
            (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti, IFNULL(cc.cantidad,0) as cantproceso,x.cantidad as totaldeproduct
            FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
            pro.id_producto=p.id
            INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$idop' AND m.id_producto=x.id_producto
            inner join prd_pasos_acciones_producto aa on aa.id='$idap'
            left join prd_agrupacion bb on bb.id=aa.id_agrupacion
            left join prd_agrupacion_detalle cc on cc.id_agrupacion=bb.id and cc.id_insumo=p.id
            WHERE
            p.status=1
            AND
            m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop') group by p.id;";

		$prodsReq = $this -> queryArray($myQuery);
		return $prodsReq;

	}

	function getUsados($idop, $idap, $idProd, $accion) {
		if ($accion == 11) { $valor = "AND b.id_pa='$idap'";
		} else {$valor = "";
		}
		$myQuery = "SELECT a.id_insumo, round(sum(a.cantidad),3) as tot_real 
            FROM prd_matpro a 
            INNER JOIN prd_matp b ON b.id=a.id_mp 
            WHERE b.id_oproduccion='$idop' $valor and a.id_insumo='$idProd' GROUP BY a.id_insumo;";

		$q = $this -> queryArray($myQuery);
		return $q;

	}

	function sucursalUsuario($iduser) {
		$sql = $this -> query("select idSuc from administracion_usuarios u where u.idempleado=$iduser");
		if ($sql -> num_rows > 0) {
			$s = $sql -> fetch_object();
			return $s -> idSuc;
		} else {
			return 0;
		}
	}

	function nl2brCH($string) {
		return preg_replace('/\R/u', '<br/><br/>', $string);
	}

	//guardar orden
	function saveOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lotesprd) {
		if (!$sucursal) {
			$sucursal = $this -> sucursalUsuario($iduserlog);
		}
		//
		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		$productos = explode('--c--', $idsProductos);

		foreach ($productos as $k => $v) {
			$exp = explode('>', $v);
			$idprod = $exp[0];
			$cant1 = $exp[1];

			$myQuery = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad,solicitante,lote) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','" . $this -> nl2brCH($obs) . "','$prioridad','$sol','" . $lotesprd[$idprod] . "');";

			$last_id = $this -> insert_id($myQuery);

			if ($last_id > 0) {
				$cad = '';

				$cad .= "('" . $last_id . "','" . $idprod . "','" . $cant1 . "'),";

			}
			$cadtrim = trim($cad, ',');
			$myQuery = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES " . $cadtrim . ";";
			$query = $this -> query($myQuery);

			$sq = "SELECT gen_aut_op FROM prd_configuracion WHERE id=1;";
			$config = $this -> queryArray($sq);
			if ($config['total'] > 0) {
				$genop = $config['rows'][0]['gen_aut_op'];
			} else {
				$genop = 0;
			}

			if ($genop == 0) {
				return $last_id;
				exit();
			}
			//
			//Empieza a generar ordenes de produccion si se tienen productos tipo 8 dentro de la formulacion
			$myQuery = "SELECT
                p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$last_id' AND m.id_producto=x.id_producto
                WHERE
                p.status=1
                AND
                p.tipo_producto=8
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$last_id') group by p.id;";

			$prodsReq = $this -> queryArray($myQuery);

			if ($prodsReq['total'] > 0) {

				foreach ($prodsReq['rows'] as $k => $v) {
					$sql = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad,solicitante,dependencia) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','" . $this -> nl2brCH($obs) . "','$prioridad','$sol','" . $last_id . "');";
					$last_id_sp = $this -> insert_id($sql);

					$ncan = $v['cantidad'] * $cant1;
					$q = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ('" . $last_id_sp . "','" . $v['idProducto'] . "','" . $ncan . "');";
					$query = $this -> query($q);
					# code...
				}
				//
				//=== SEGUNDO NIVEL
				$myQuery2 = "SELECT
                    p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                    p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                    (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                    (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti
                    FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                    pro.id_producto=p.id
                    INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$last_id_sp' AND m.id_producto=x.id_producto
                    WHERE
                    p.status=1
                    AND
                    p.tipo_producto=8
                    AND
                    m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$last_id_sp') group by p.id;";

				$prodsReq2 = $this -> queryArray($myQuery2);

				if ($prodsReq2['total'] > 0) {

					foreach ($prodsReq2['rows'] as $k2 => $v2) {
						$sql2 = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad,solicitante,dependencia) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','" . $this -> nl2brCH($obs) . "','$prioridad','$sol','" . $last_id . "-" . $last_id_sp . "');";
						$last_id_sp2 = $this -> insert_id($sql2);

						$ncan2 = $v2['cantidad'] * $ncan;
						$q2 = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ('" . $last_id_sp2 . "','" . $v2['idProducto'] . "','" . $ncan2 . "');";
						$query = $this -> query($q2);
					}

				}

			}
		}

		return $last_id;

	}

	function modifyOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lote) {
		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		//se cambiara el update para q funcione el multiple orden en edicion ya q dentro de una edicion puedes agregar mas prd
		// $this->saveOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lote);
		//return 1;
		$productos = explode('--c--', $idsProductos);
		$multiple = count($productos);
		//si hay mas de un producto esq es multiple
		if ($multiple > 1) {
			$myQuery = "DELETE FROM prd_orden_produccion WHERE id=$id_op;";
			$myQuery .= "DELETE FROM prd_orden_produccion_detalle WHERE id_orden_produccion=$id_op;";
			//
			if ($this -> multi_query($myQuery)) {
				while ($this -> connection -> next_result()) {;
				}
				return $this -> saveOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lote);
			} else {
				return 0;
			}
		} else {
			foreach ($productos as $k => $v) {
				$exp = explode('>', $v);
				$idprod = $exp[0];
				$cant = $exp[1];

			}

			$myQuery = "UPDATE prd_orden_produccion SET id_usuario='$iduserlog', id_sucursal='$sucursal', fecha_registro='$creacion', fecha_inicio='$fecha_registro', fecha_entrega='$fecha_entrega', solicitante='$sol',observaciones='" . $this -> nl2brCH($obs) . "', prioridad='$prioridad', lote='$lote[$idprod]' WHERE id='$id_op'  ";
			$this -> query($myQuery);

			$myQuery = "DELETE FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$id_op';";
			$this -> query($myQuery);
			$last_id = $id_op;
			if ($last_id > 0) {
				$cad = '';

				$cad .= "('" . $last_id . "','" . $idprod . "','" . $cant . "'),";

				$cadtrim = trim($cad, ',');
				$myQuery = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES " . $cadtrim . ";";
				$query = $this -> query($myQuery);
			}
			return $id_op;
		}

	}

	function savePre($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $orden, $sol) {
		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		$o = explode('--c--', $idsProductos);
		$arraypro = array();

		foreach ($o as $key => $value) {

			$q = explode('>', $value);
			$idpro = $q[0];
			if ($idpro != 0) {

				if (array_key_exists($idpro, $arraypro)) {
					$arraypro[$q[0]][] = array('idpadre' => $q[1], 'idproducto' => $q[2], 'cantidad' => $q[3], 'precio' => $q[4]);
				} else {

					$arraypro[$q[0]][] = array('idpadre' => $q[1], 'idproducto' => $q[2], 'cantidad' => $q[3], 'precio' => $q[4]);

				}
			}
		}
		$myQuery3 = "SELECT fecha_inicio,fecha_entrega from prd_orden_produccion where id='$id_op'";
		$result = $this -> query($myQuery3);
		$row = $result -> fetch_array();
		$fecha = $row['fecha_inicio'];
		$fecha_entrega = $row['fecha_entrega'];
		foreach ($arraypro as $k => $v) {
			$myQuery = "INSERT INTO prd_prerequisicion (id_op,id_usuario,id_proveedor,observaciones_pre,fecha_creacion,activo,subtotal,total) VALUES ('$id_op','$iduserlog','$k','" . $this -> nl2brCH($obs) . "','$creacion','1','0','0');";
			$last_id = $this -> insert_id($myQuery);
			$myQuery2 = "SELECT id_moneda from app_productos a 
                 JOIN prd_orden_produccion_detalle b on b.id_orden_produccion='$id_op'
                where a.id=b.id_producto limit 1";

			$result = $this -> query($myQuery2);
			$row = $result -> fetch_array();
			$moneda = $row['id_moneda'];
			if ($moneda == null || $moneda == '' || $moneda == 0) {
				$moneda = 1;
			}

			if ($orden == 1) {
				$au = 1;
			} else {
				$au = 0;
			}
			$myQuery = "INSERT INTO app_requisiciones (id_solicito,id_tipogasto,id_almacen,id_moneda,id_proveedor,urgente,inventariable,observaciones,fecha,fecha_entrega,activo,tipo_cambio,pr,subtotal,total,id_usuario,fecha_creacion,idoproduccion,idprereq) VALUES ('$sol','7','1','$moneda','$k','0','1','" . $this -> nl2brCH($obs) . "','$fecha','$fecha_entrega','$au','0','2','$ttt','$ttt','$iduserlog','$creacion','$id_op','$last_id');";
			$last_id2 = $this -> insert_id($myQuery);

			if ($orden == 1) {
				$myQuery = "INSERT INTO app_ocompra (id_proveedor,id_usrcompra,observaciones,fecha,fecha_entrega,activo,id_requisicion,subtotal,total,id_almacen,id_usuario,fecha_creacion,tipo) VALUES ('$k','1','" . $this -> nl2brCH($obs) . "','$fecha','$fecha_entrega','1','$last_id2','$ttt','$ttt','1','$iduserlog','$creacion','1');";
				$last_id3 = $this -> insert_id($myQuery);
			}
			if ($last_id > 0) {
				$cad = '';
				$cad2 = '';
				$cad3 = '';
				$ptotal = 0;
				foreach ($arraypro[$k] as $k2 => $v2) {
					$ptotal += ($v2['precio'] * $v2['cantidad']);
					$costo = $v2['precio'];
					$cad .= "('" . $last_id . "','" . $v2['idproducto'] . "','1','1','" . $v2['cantidad'] . "','" . $v2['idpadre'] . "'),";
					$cad2 .= "('" . $last_id2 . "','" . $v2['idproducto'] . "','sestemp','1','1','" . $v2['cantidad'] . "','0'),";
					$cad3 .= "('" . $last_id3 . "','" . $v2['idproducto'] . "','sestemp','1','1','" . $v2['cantidad'] . "','" . $costo . "','0','0'),";
				}
				$cadtrim = trim($cad, ',');
				$cadtrim2 = trim($cad2, ',');
				$cadtrim3 = trim($cad3, ',');
				$myQuery = "INSERT INTO prd_prerequisicion_datos (id_prerequisicion,id_producto,estatus,activo,cantidad,id_producto_padre) VALUES " . $cadtrim . ";";
				$query = $this -> query($myQuery);
				$myQuery = "INSERT INTO app_requisiciones_datos (id_requisicion,id_producto,ses_tmp,estatus,activo,cantidad,caracteristica) VALUES " . $cadtrim2 . ";";
				$query = $this -> query($myQuery);

				if ($orden == 1) {
					$myQuery = "INSERT INTO app_ocompra_datos (id_ocompra,id_producto,ses_tmp,estatus,activo,cantidad,costo,impuestos,caracteristica) VALUES " . $cadtrim3 . ";";
					$query = $this -> query($myQuery);

				}
				$myQuery = "UPDATE app_requisiciones SET subtotal='$ptotal',total='$ptotal' WHERE id='" . $last_id2 . "';";
				$query = $this -> query($myQuery);

				if ($orden == 1) {
					$myQuery = "UPDATE app_ocompra SET subtotal='$ptotal',total='$ptotal' WHERE id='" . $last_id3 . "';";
					$query = $this -> query($myQuery);
				}

			}

		}
		$myQuery = "UPDATE prd_orden_produccion SET estatus='2' WHERE id='" . $id_op . "';";
		$query = $this -> query($myQuery);

		echo 'p';

	}

	function delOP($idop) {
		$myQuery = "UPDATE prd_orden_produccion SET estatus=0 WHERE id='$idop';";
		$update = $this -> query($myQuery);
		return $update;
	}

	function getExistenciasNueva($idProducto, $caracteristicas, $lote) {
		$caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
		$myQuery2 = "SELECT sum(if(cantidad is null,0,cantidad)) as cantidad, sum(if(apartados is null,0,apartados)) as apartados FROM app_inventario WHERE id_producto='$idProducto' AND caracteristicas =\"$caracteristicas\"; ";
		$totpedis = $this -> queryArray($myQuery2);
		$cantidad = $totpedis['rows'][0]['cantidad'] - $totpedis['rows'][0]['apartados'];
		if ($cantidad == '' || $cantidad == NULL) {
			$cantidad = 0;
		}
		return $cantidad;
	}

	function ordenPrdIniciada($idproduct) {
		$sql = $this -> query("select * from prd_orden_produccion_detalle pd
			inner join prd_orden_produccion p on p.id=pd.id_orden_produccion and p.estatus=4
			where pd.id_producto=$idproduct;");
		//si tiene iniciada una orden del producto
		if ($sql -> num_rows > 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function saveUsar($id_op, $iduserlog) {
		/*explosion masiva*/
		if (is_array($id_op)) {
			$id_op = implode(",", $id_op);
		}

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		$myQuery = "UPDATE prd_orden_produccion SET estatus=4 WHERE id in (" . $id_op . ");";
		$query = $this -> query($myQuery);

		return 1;

	}

	/*insumos variables cambio de fortmula cuando cambias los variables*/
	function updateInsumosVariables($idproduc, $idinsumo, $cantidad) {
		$sql = $this -> query("update app_producto_material set  cantidad =$cantidad  where id_producto=$idproduc and id_material=$idinsumo");
	}

	/*fin variables*/
	function autorizar($id) {
		$myQuery = "UPDATE prd_orden_produccion set autorizado=1 where id='$id'";
		$resultb = $this -> query($myQuery);
		return $resultb;
	}

	function productosOpMasiva($idop) {
		$myQuery = "SELECT a.*, c.id, c.codigo, c.nombre as nomprod, c.series, c.lotes, c.pedimentos, c.precio as precioorig, x.clave,c.minimoprod as minimos,c.insumovariable
                    from prd_orden_produccion_detalle a
                    INNER JOIN app_productos c on c.id = a.id_producto
                    INNER join app_unidades_medida x on x.id=c.id_unidad_venta
                    WHERE a.id_orden_produccion in ($idop) group by a.id;";

		$prodsReq = $this -> query($myQuery);
		return $prodsReq -> fetch_assoc();

	}

	//explosion masiva
	function productosOpExplosionMasiva($idops) {
		$myQuery = "SELECT
                p.id AS idProducto, p.nombre, 
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales, p.lotes, 							sum(m.cantidad*x.cantidad) as canti, p.insumovariable,x.cantidad as cantproduct,
				
					 (select sum(e.cantidad*y.cantidad)
				FROM app_productos r 
				INNER JOIN app_producto_material e ON r.id=e.id_material 
				LEFT JOIN app_unidades_medida g ON g.id=r.id_unidad_compra                
	 			INNER JOIN prd_orden_produccion_detalle y on y.id_orden_produccion in($idops) AND e.id_producto=y.id_producto
	                WHERE
	                r.status=1
	                AND
	                e.id_producto in  (SELECT id_producto FROM prd_orden_produccion_detalle 
	                WHERE id_orden_produccion in($idops)) and g.clave =u.clave) as cantidadunidad,
	                x.id_orden_produccion
	                
	             FROM app_productos p 
	             left JOIN app_producto_material m ON p.id=m.id_material 
	             LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra 
	             left JOIN prd_orden_produccion_detalle x on x.id_orden_produccion in($idops) AND m.id_producto=x.id_producto
	            WHERE
	                p.status=1
	                AND
	                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion in($idops)) group by p.id;";
		$prodsReq = $this -> queryArray($myQuery);
		return $prodsReq;

	}

	//fin explosion masiva

	function proveedoresCostoOParaMasivo($proveedores) {
		$myQuery = "SELECT a.costo, a.id_proveedor, b.razon_social FROM app_costos_proveedor a inner join mrp_proveedor b on b.idPrv=a.id_proveedor where a.id_producto=$proveedores;";
		$datosReq = $this -> query($myQuery);
		return $datosReq;

	}

	function config() {
		$sql = $this -> query("SELECT * FROM prd_configuracion WHERE id=1;");
		return $sql -> fetch_array();
	}

	public function calculaImpuestos($stringTaxes) {
		$impuestos = array();
		$productos = explode('/', $stringTaxes);

		$ppii = array();

		foreach ($productos as $key => $value) {
			$prod = explode('-', $value);
			if ($prod[0] != '') {
				$idProducto = $prod[0];
				$precio = $prod[1];
				$cantidad = $prod[2];
				$ch = $prod[3];
				$formula = 1;
				//desc o asc 1 = ieps de los vinos , 2 = ieps de la gasolina
				$subtotal = $precio * $cantidad;
				$subtotalVenta += $subtotal;
				if ($formula == 2) {
					$ordenform = 'ASC';
				} else {
					$ordenform = 'DESC';
				}

				$queryImpuestos = "select p.id,p.precio, i.valor, i.clave,pi.formula,i.nombre";
				$queryImpuestos .= " from app_impuesto i, app_productos p ";
				$queryImpuestos .= " left join app_producto_impuesto pi on p.id=pi.id_producto ";
				$queryImpuestos .= " where p.id=" . $idProducto . " and i.id=pi.id_impuesto ";
				$queryImpuestos .= " Order by pi.id_impuesto " . $ordenform;
				$resImpues = $this -> queryArray($queryImpuestos);

				foreach ($resImpues['rows'] as $key => $valueImpuestos) {
					if ($valueImpuestos["clave"] == 'IEPS') {
						//echo 'Y'.$producto_impuesto;
						$producto_impuesto = $ieps = (($subtotal) * $valueImpuestos["valor"] / 100);
						$producto_impuesto2 += (($subtotal) * $valueImpuestos["valor"] / 100);
					} elseif ($valueImpuestos["clave"] == 'IVAR' || $valueImpuestos["clave"] == 'ISR' || $valueImpuestos["clave"] == 'RTP') {

						$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
						$producto_impuestoR = (($subtotal) * $valueImpuestos["valor"] / 100);
						$producto_impuestoR += (($subtotal) * $valueImpuestos["valor"] / 100);
						$producto_impuesto2 += (($subtotal) * $valueImpuestos["valor"] / 100);

					} else {

						if ($ieps != 0) {
							$producto_impuesto = ((($subtotal + $ieps)) * $valueImpuestos["valor"] / 100);

						} else {

							$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
							$producto_impuesto2 += (($subtotal) * $valueImpuestos["valor"] / 100);
							//}
						}
					}

					$ppii[$idProducto . '-' . $ch][] = $valueImpuestos["nombre"] . '-' . $valueImpuestos["valor"] . '-' . $producto_impuesto;
					$totalImpestos += $producto_impuesto;
					$impuestos['cargos']['impuestos'][$valueImpuestos["clave"]] = $impuestos['cargos']['impuestos'][$valueImpuestos["clave"]] + $producto_impuesto;
					$impuestos['cargos']['impuestosPorcentajes'][$valueImpuestos["nombre"]] = $impuestos['cargos']['impuestosPorcentajes'][$valueImpuestos["nombre"]] + $producto_impuesto;

				}
				$ieps = '0';
			}

		}
		$impuestos['cargos']['ppii'] = $ppii;
		$impuestos['cargos']['total'] = ($totalImpestos + $subtotalVenta) - $producto_impuestoR;
		$impuestos['cargos']['subtotal'] = $subtotalVenta;

		return $impuestos;

	}

}
?>