<?php
	// isaias
	//Carga la clase de coneccion con sus metodos para consultas o transacciones
	//require("models/connection.php"); // funciones mySQL
	require("models/connection_sqli_manual.php"); // funciones mySQLi

class InventariosModel extends Connection
{
	//INICIAN FUNCIONES ENTRADAS
	public $connection_transversal;

	public function config_ini(){
        $selct = "SELECT * from app_configuracion";
        $res = $this->queryArray($selct);
        return $res['total'];
    }
  public function buscarProductoPorId($idProducto){
		return $this->query("CALL buscarProductoPorId('".$idProducto."')");
	}
	public function listaMovimientos($t) {

		$myQuery = "SELECT id, (SELECT CONCAT('(',codigo,') ',nombre,'*/*',series) FROM app_productos WHERE id = i.id_producto) AS producto,
		i.id_producto_caracteristica,
		i.cantidad,
		i.importe,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = i.id_almacen_origen) AS almacen_origen,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = i.id_almacen_destino) AS almacen_destino,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = SUBSTRING_INDEX(i.referencia,'Destino:',-1)) AS destino_final,
		i.fecha,
		(SELECT usuario FROM accelog_usuarios WHERE idempleado = i.id_empleado) AS empleado,
		i.tipo_traspaso,
		i.costo,
		i.referencia,
		i.estatus,
		i.origen
		FROM app_inventario_movimientos i ORDER BY i.id DESC";
		//FROM app_inventario_movimientos i ORDER BY i.id DESC LIMIT 100";
		return $this->query($myQuery);
	}

	public function listaMovimientosNueva($params) {
		$myQuery = "SELECT id, (SELECT CONCAT('(',codigo,') ',nombre,'*/*',series) FROM app_productos WHERE id = i.id_producto) AS producto,
		i.id_producto_caracteristica,
		i.cantidad,
		i.importe,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = i.id_almacen_origen) AS almacen_origen,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = i.id_almacen_destino) AS almacen_destino,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = SUBSTRING_INDEX(i.referencia,'Destino:',-1)) AS destino_final,
		i.fecha,
		(SELECT usuario FROM accelog_usuarios WHERE idempleado = i.id_empleado) AS empleado,
		i.tipo_traspaso,
		i.costo,
		i.referencia,
		i.estatus,
		i.origen
		FROM app_inventario_movimientos i
		WHERE fecha BETWEEN '{$params['desde']} 00:00:00' AND '{$params['hasta']} 23:59:59'
		ORDER BY i.id DESC";//echo $myQuery;die;
		//FROM app_inventario_movimientos i ORDER BY i.id DESC LIMIT 100";
		return $this->query($myQuery);
	}

	public function info_traslado_movimientos($id_tras) {
		$myQuery = "SELECT t.recibidos,
					m.id, (SELECT CONCAT('(',codigo,') ',nombre) FROM app_productos WHERE id = m.id_producto) AS Producto,
					m.id_producto_caracteristica,
					m.cantidad,
					m.importe
					FROM app_inventario_traslados_movimientos t
					INNER JOIN app_inventario_movimientos m ON m.id = t.id_movimiento
					WHERE t.id_traslado = $id_tras AND estatus = 1";
		$info = $this->query($myQuery);
		return $info;
	}

	public function cerrar_traslado($vars)
	{
		$myQuery = "UPDATE app_inventario_traslados SET fecha_recepcion = '".$vars['fecha']."', comentario_rec = '".$vars['comentario']."', cerrado = 1 WHERE id = ".$vars['idrec'];

		if($this->query($myQuery))
			return 1;
		else
			return 0;
	}

	public function listaProductos()
	{
		/*return $this->query("SELECT p.id, codigo, nombre, c.costo precio, (SELECT clave FROM app_unidades_medida WHERE id=id_unidad_venta) AS unidad, (SELECT codigo FROM cont_coin WHERE coin_id=p.id_moneda) AS moneda, id_tipo_costeo
							FROM app_productos p
							INNER JOIN (
									SELECT * FROM (SELECT * FROM app_costos_proveedor ORDER BY id_producto, id_proveedor) TMP GROUP BY id_producto
							) c ON p.id = c.id_producto
							WHERE status = 1;"); */
		return $this->query(
				'SELECT
					p.id,
					codigo,
					nombre,
					c.costo  as precio1 ,
					(SELECT
						clave
					FROM app_unidades_medida
					WHERE id=id_unidad_venta) AS unidad,
					(SELECT
						codigo
					FROM cont_coin
					WHERE coin_id = p.id_moneda) AS moneda,
					id_tipo_costeo ,
					(SELECT
						clave
					FROM app_unidades_medida
					WHERE id=id_unidad_compra) AS unidadcompra,
					(SELECT
						factor
					FROM app_unidades_medida
					WHERE id=id_unidad_compra) AS factor,
					(c.costo/(SELECT factor FROM app_unidades_medida WHERE id=id_unidad_compra)) as precio

							FROM app_productos p
							INNER JOIN (
									SELECT * FROM
									(SELECT
										*
									FROM app_costos_proveedor
									ORDER BY
										id_producto,
										id_proveedor) TMP
									GROUP BY id_producto
							) c ON p.id = c.id_producto
							WHERE status = 1');


	}

	public function buscarProductos($opcion, $patron)
	{
		$filtro = " AND nombre LIKE '%$patron%' ";
		if ($opcion == 1)
			$filtro .= " AND series = '1'";
		else if ($opcion == 4)
			$filtro .= " AND lotes = '1'";
		else
			$filtro .= "";
		return $this->queryArray("SELECT id,  nombre AS text, (SELECT clave FROM app_unidades_medida WHERE id=id_unidad_venta) AS unidad, (SELECT codigo FROM cont_coin WHERE coin_id=id_moneda) AS moneda, id_tipo_costeo FROM app_productos WHERE status = 1 $filtro ; ");
	}

	public function listaAlmacenes($tipo,$idprod,$caracteristicas,$pedimentos,$lotes,$series)
	{

		if(!intval($tipo))
			return $this->query("SELECT a.id, a.codigo_manual, a.nombre, a.codigo_sistema FROM app_almacenes a WHERE a.activo = 1 AND a.codigo_sistema != '999' ORDER BY a.codigo_sistema");
		else
		{
			$carac = '';
			if($caracteristicas != '')
			{
				$caracteristicas = explode('|',$caracteristicas);
				for($i=0;$i<count($caracteristicas)-1;$i++)
				{
					$carac .= " AND id_producto_caracteristica LIKE \"%".$caracteristicas[$i]."%\"";
				}
			}
			$series_query = "";
			if(!empty($series))
			{
				$series_query .= " AND id_producto IN (SELECT id_producto FROM app_producto_serie)";
			}

			$myQuery = "SELECT a.id, a.codigo_manual, a.codigo_sistema, a.nombre,
@e := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE estatus = 1 AND id_almacen_destino = a.id AND id_producto = $idprod $carac AND id_pedimento = $pedimentos AND id_lote = $lotes $series_query AND tipo_traspaso != 3 ) AS entradas,
@s := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE estatus = 1 and id_almacen_origen = a.id AND id_producto = $idprod $carac AND id_pedimento = $pedimentos AND id_lote = $lotes $series_query AND tipo_traspaso != 3) AS salidas,
(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
FROM app_almacenes a WHERE a.activo = 1 AND a.codigo_sistema != '999'
ORDER BY a.codigo_sistema";
			//return $myQuery;
			return $this->query($myQuery);
		}
	}

	public function guardar_movimiento($idprod,$cantidad,$importe,$almacen_origen,$almacen_destino,$tipo,$costo,$fecha,$referencia,$caracteristicas,$pedimentos,$lotes,$series,$tras)
	{
		$myQuery = "";
		$id_pedimento = 0;
		if($pedimentos != '')
		{
			if(intval($tipo) == 1)
			{
				$pedimentos = explode("@|@",$pedimentos);
				$id_pedimento = $this->insert_id("INSERT INTO app_producto_pedimentos(id,no_pedimento,aduana,no_aduana,tipo_cambio,fecha_pedimento) VALUES(0,'".$pedimentos[0]."','".$pedimentos[1]."','".$pedimentos[2]."',".$pedimentos[3].",'".$pedimentos[4]."')");
			}
			else
			{
				$id_pedimento = $pedimentos;
			}
		}

		$id_lote = 0;
		if($lotes != '')
		{
			if(intval($tipo) == 1)
			{
				$lotes = explode("@|@",$lotes);
				$id_lote = $this->insert_id("INSERT INTO app_producto_lotes(id,no_lote,fecha_fabricacion,fecha_caducidad) VALUES(0,'".$lotes[0]."','".$lotes[1]."','".$lotes[2]."')");
			}
			else
			{
				$id_lote = $lotes;
			}
		}

		if($caracteristicas == '')
			$caracteristicas = "'0'";

		$estatus = 1;
		if(intval($tras))
			$estatus = 0;
		 $myQuery = "INSERT INTO app_inventario_movimientos(id, id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus,origen) VALUES(0, $idprod, \"$caracteristicas\", $id_pedimento, $id_lote, $cantidad, $importe, $almacen_origen, $almacen_destino, '$fecha', ".$_SESSION["accelog_idempleado"].", $tipo, $costo, '$referencia',$estatus,0);";

		if($id_mov = $this->insert_id($myQuery))
		{
			//Si se trata de un traspaso
			if(intval($tras))
			{
				//SI ES TRASPASO INSERTA UN REGISTRO EN LA RELACION CON EL TRASLADO
				$query_traspaso = "INSERT INTO app_inventario_traslados_movimientos(id,id_traslado,id_movimiento,recibidos) VALUES(0,$tras,$id_mov,0)";

				$this->query($query_traspaso);
			}
			else
			{
				///////////////////////ACONTIA///////////////////////////////
				////////////////////////////////////////////////////////////
				 $genera_poliza = 0; //Por default no genera poliza
				//Si tiene acontia y esta conectado
				$conexion_acontia = $this->query("SELECT conectar_acontia, pol_autorizacion FROM app_configuracion WHERE id = 1");
				$conexion_acontia = $conexion_acontia->fetch_assoc();

				if(intval($conexion_acontia['conectar_acontia']))
				{
					if(intval($tipo))
					{
						$idpol = 5;//Busca la poliza de ingresp
						$concepto = "Ingreso de Mercancia";
					}
					else
					{
						$idpol = 6;//Busca la poliza de salida
						$concepto = "Salida de Mercancia";
					}
					//Busca si es poliza automatica
					$automatica = $this->query("SELECT* FROM app_tpl_polizas WHERE id = $idpol");
					$automatica = $automatica->fetch_assoc();

					//Si es automatica y se genera por documento
					if(intval($automatica['automatica']) && intval($automatica['poliza_por_mov']) == 1)
					{
						$fecha = explode('-',$fecha);

						//Busca el id del ejercicio, si no existe, busca el ultimo y le suma al id para sacar el ejercicio
						$ejercicio = $this->query("SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = ".$fecha[0]);
						$ejercicio = $ejercicio->fetch_assoc();
						$ejercicio = $ejercicio['Id'];
						//Si no existe calcula el Id
						if(!intval($ejercicio))
						{
							$ejercicioAntes = $this->query("SELECT * FROM cont_ejercicios ORDER BY Id DESC LIMIT 1");
							$ejercicioAntes = $ejercicioAntes->fetch_assoc();
							$nuevoEj = intval($fecha[0]) - intval($ejercicioAntes['NombreEjercicio']);
							$ejercicio = intval($ejercicioAntes['Id']) + $nuevoEj;
						}
						$numpol = $this->query("SELECT pp.numpol+1 FROM cont_polizas pp WHERE pp.idtipopoliza = ".$automatica['id_tipo_poliza']." AND pp.activo = 1 AND pp.idejercicio = $ejercicio AND pp.idperiodo = ".intval($fecha[1])." ORDER BY pp.numpol DESC LIMIT 1");
						$numpol = $numpol->fetch_assoc();
						$numpol = $numpol['numpol'];
						if(!intval($numpol))
							$numpol = 1;
						$activo = 1;
						if(intval($conexion_acontia['pol_autorizacion']))
							$activo = 0;

						$id_poliza_acontia = $this->insert_id("INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, concepto, fecha, fecha_creacion, activo, eliminado, pdv_aut, usuario_creacion, usuario_modificacion)
						 VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'".$automatica['nombre_poliza']." $referencia','$fecha[0]-$fecha[1]-$fecha[2]',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, ".$_SESSION["accelog_idempleado"].", 0)");
						$cont = 0;//Contador de movimientos
						$cuentas_poliza = $this->query("SELECT id_cuenta, tipo_movto, id_dato, nombre_impuesto FROM app_tpl_polizas_mov WHERE activo = 1 AND id_tpl_poliza = $idpol");
						while($cp = $cuentas_poliza->fetch_assoc())
						{
							$cont++;
							//Cargo o abono
							if(intval($cp['tipo_movto']) == 1)
								$tipo_movto = "Abono";
							if(intval($cp['tipo_movto']) == 2)
								$tipo_movto = "Cargo";

							//dependiendo el tipo de dato sera el valor que tomara, en este caso solo existe el total del pago.
							if(intval($cp['id_dato']) == 3)
							{
								$importe = 0;
							}

							$id_mov_acontia = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, FormaPago, tipocambio) VALUES($id_poliza_acontia, $cont, 1, 1, ".$cp['id_cuenta'].", '$tipo_movto', $importe, '','$referencia Doc: $id_mov', $activo, DATE_SUB(NOW(), INTERVAL 6 HOUR), 1, 1)");
							$ids_movs_acontia .= $id_mov_acontia.",";
						}
						$this->query("UPDATE app_inventario_movimientos SET id_poliza_mov = '$ids_movs_acontia' WHERE id = $id_mov");
						$ids_movs_acontia = '';
					}
				}

				//Termina conexion con acontia
				////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////
			}

			if($series != '')
			{
				$myQuery = '';
				$series = explode("@|@",$series);
				for($i=0;$i<=count($series)-2;$i++)
				{
					if(intval($tipo) == 1)
					{
						$myQuery .= "INSERT INTO app_producto_serie(id, id_producto, id_ocompra, id_recepcion, id_venta, estatus, serie, id_almacen, id_pedimento) VALUES(0,$idprod,0,0,0,0,'".$series[$i]."',$almacen_destino,$id_pedimento);";

						$myQuery .= "INSERT INTO app_producto_serie_rastro(id,id_serie,id_almacen,fecha_reg,id_mov) VALUES(0,(SELECT id FROM app_producto_serie WHERE serie = '".$series[$i]."'),$almacen_destino,'".date("Y-m-d H:i:s")."',$id_mov);";
					}
					elseif(!intval($tipo))
					{
					   $myQuery .= "UPDATE app_producto_serie SET estatus = 1 WHERE id = ".$series[$i].";";
					   $myQuery .= "INSERT INTO app_producto_serie_rastro(id,id_serie,id_almacen,fecha_reg,id_mov) VALUES(0,".$series[$i].",$almacen_origen,'".date("Y-m-d H:i:s")."',$id_mov);";
					}
					else
					{
						$myQuery .= "UPDATE app_producto_serie SET id_almacen = $almacen_destino WHERE id = ".$series[$i].";";
						 $myQuery .= "INSERT INTO app_producto_serie_rastro(id,id_serie,id_almacen,fecha_reg,id_mov) VALUES(0,".$series[$i].",$almacen_destino,'".date("Y-m-d H:i:s")."',$id_mov);";
					}
				}
				if($this->dataTransact($myQuery))
				{
					return intval($id_mov);
				}
				else
				{
					//Deshacer todo
					return false;
				}
			}
			return intval($id_mov);
		}
		else
		{
			//Deshacer todo
			return false;
		}





		//Si se  hace entrada de producto hace una recepcion en ceros.
		/*if(intval($tipo) == 1)
		{
			$myQuery .= "INSERT INTO app_recepcion_datos(id,id_oc,id_producto,id_recepcion,cantidad,id_lote,id_pedimento,estatus,id_almacen) VALUES(0,0,$idprod,0,$cantidad,$id_lote,$id_pedimento,0,$almacen_destino);";
		}*/

		//return $this->dataTransact($myQuery);
		//return $myQuery;
		//return $carArray;
	}

	public function salidasSinExistencia()
	{
		$myQuery = "SELECT salidas_sin_existencia FROM app_configuracion WHERE id = 1";
		$result = $this->query($myQuery);
		$result = $result->fetch_assoc();
		return $result['salidas_sin_existencia'];
	}

	public function caracteristicasProd($idprod,$caracteristicas)
	{
		/*$myQuery = "SELECT cp.id, cp.nombre
					FROM app_producto_caracteristicas pc
					INNER JOIN app_caracteristicas_padre cp ON cp.id = pc.id_caracteristica_padre
					WHERE pc.id_producto = $idprod AND cp.activo = 1";*/

		$myQuery = "SELECT cp.id AS IdPadre, cp.nombre AS NombrePadre, ch.id AS IdHija, ch.nombre AS NombreHija
					FROM app_caracteristicas_hija ch
					INNER JOIN app_caracteristicas_padre cp ON cp.id = ch.id_caracteristica_padre
					INNER JOIN app_producto_caracteristicas pc ON pc.id_caracteristica_padre = cp.id AND pc.id_producto = $idprod
					WHERE ch.activo = 1 AND cp.activo = 1
					ORDER BY cp.id,ch.id";

		return $this->query($myQuery);
	}

	public function otrasCarac($idprod)
	{
		$myQuery = "SELECT series, lotes, pedimentos FROM app_productos WHERE id = $idprod";
		$carac = $this->query($myQuery);
		$carac = $carac->fetch_array();
		return $carac;
	}

	public function pls($idprod,$pls,$idped=null,$idalmacen=null)
	{
		if(intval($pls) == 2)//Pedimentos
		{
			$myQuery = "SELECT DISTINCT p.id, p.no_pedimento AS nombre FROM app_producto_pedimentos p
					INNER JOIN app_inventario_movimientos m ON m.id_pedimento = p.id
					WHERE m.id_producto = $idprod";
		}
		if(intval($pls) == 1)//Lotes
		{
			$myQuery = "SELECT DISTINCT l.id, l.no_lote AS nombre,
						IFNULL((SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_producto = m.id_producto AND tipo_traspaso = 1 AND id_lote = m.id_lote AND estatus = 1),0)-IFNULL((SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_producto = m.id_producto AND tipo_traspaso = 0 AND id_lote = m.id_lote AND estatus = 1),0) AS existencia
						FROM app_producto_lotes l
						INNER JOIN app_inventario_movimientos m ON m.id_lote = l.id
						WHERE m.id_producto = $idprod
						HAVING existencia != 0";
		}


		if(intval($pls) == 0)//Series
		{
			if(is_null($idped))
				$idped = 0;
			$myQuery = "SELECT id, serie AS nombre FROM app_producto_serie WHERE id_producto = $idprod AND estatus = 0 AND id_pedimento = $idped AND id_almacen = $idalmacen";
		}

		return $this->query($myQuery);
	}

	//TERMINAN FUNCIONES ENTRADAS
			   ////CH
	public function indexGrid2($idProducto,$idalmacen,$desde,$hasta,$R1,$iddep,$idfa,$idli)
	{
		$filtro = '1 = 1 and p.status = 1';
		$filtroaux = "(p.codigo = 'Q1W2' or  p.codigo = 'Z1X2') and m.fecha BETWEEN '2016-05-23' and '2016-06-29'";

		if($idProducto!=""){
			if($idProducto=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.codigo="'.$idProducto.'")';
			}

		}
		if($iddep!=""){
			if($iddep=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.departamento="'.$iddep.'")';
			}

		}
		if($idfa!=""){
			if($idfa=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.familia="'.$idfa.'")';
			}

		}
		if($idli!=""){
			if($idli=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.linea="'.$idli.'")';
			}

		}
		if($idalmacen!=""){
			if($idalmacen =='0'){
				$filtro .='';
			}else{
				$filtro .=' and if (dd.codigo_sistema is null, oo.codigo_sistema, dd.codigo_sistema) LIKE "'.$idalmacen.'%"';
			}

		}

		if($desde!='' && $hasta!=''){
			$filtro .=' and m.fecha BETWEEN "'.$desde.'" and "'.$hasta.'" ';
		}
		// obtine los traspasos
		$query00 = "SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, if(dd.id is null, oo.id, dd.id) as almacen, if(dd.nombre is null, oo.nombre, dd.nombre) as almacenNombre
					from app_inventario_movimientos m
					left join app_almacenes oo on oo.id = m.id_almacen_origen
					left join app_almacenes dd on dd.id = m.id_almacen_destino
					left join app_productos p on p.id = m.id_producto
					left join accelog_usuarios u on u.idempleado = m.id_empleado
					where m.tipo_traspaso = 2
					order by almacen asc, p.codigo, m.fecha";

		$result00 = $this->queryArray($query00);

		// obtine todos los movieminetos
		$query = "SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, if(dd.id is null, oo.id, dd.id) as almacen, if(dd.nombre is null, oo.nombre, dd.nombre) as almacenNombre
					from app_inventario_movimientos m
					left join app_almacenes oo on oo.id = m.id_almacen_origen
					left join app_almacenes dd on dd.id = m.id_almacen_destino
					left join app_productos p on p.id = m.id_producto
					left join accelog_usuarios u on u.idempleado = m.id_empleado
					order by almacen asc, p.codigo, m.fecha";

		$result = $this->queryArray($query);

		// obtine los movimientos segun el filtro(condicion)
		$query1 = "SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, if(dd.id is null, oo.id, dd.id) as almacen, if(dd.nombre is null, oo.nombre, dd.nombre) as almacenNombre, if (dd.codigo_sistema is null, oo.codigo_sistema, dd.codigo_sistema) as almacenCodigo
					from app_inventario_movimientos m
					left join app_almacenes oo on oo.id = m.id_almacen_origen
					left join app_almacenes dd on dd.id = m.id_almacen_destino
					left join app_productos p on p.id = m.id_producto
					left join accelog_usuarios u on u.idempleado = m.id_empleado
					where ".$filtro."
					order by almacen asc, p.codigo, m.fecha";

		$result1 = $this->queryArray($query1);


		$query2 = "SELECT * from app_productos where status = 1";
		$result2 = $this->queryArray($query2);

		$query3 = "SELECT * from app_almacenes where activo = 1 order by codigo_sistema asc";
		$result3 = $this->queryArray($query3);

		$query4 = "SELECT * from app_departamento";
		$result4 = $this->queryArray($query4);


		return array('grid00' => $result00['rows'] ,'grid0' => $result['rows'] ,'grid' => $result1['rows'] , 'productos' => $result2['rows'], 'almacenes' => $result3['rows'], 'departamentos' => $result4['rows']);
	}
	public function listarFamilia($iddepartamento){
		$query = "SELECT * from app_familia where id_departamento = $iddepartamento";
		$result = $this->queryArray($query);
		return $result['rows'];
	}
	public function listarLinea($idfamilia){
		$query = "SELECT * from app_linea where id_familia = $idfamilia and activo = 1";
		$result = $this->queryArray($query);
		return $result['rows'];
	}
	public function existenciasGrid($idProducto,$idalmacen,$desde,$hasta,$R1,$iddep,$idfa,$idli){

		$filtro = '1 = 1 and p.status = 1';

		if($idProducto!=""){
			if($idProducto=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.codigo="'.$idProducto.'")';
			}

		}
		if($iddep!=""){
			if($iddep=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.departamento="'.$iddep.'")';
			}

		}
		if($idfa!=""){
			if($idfa=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.familia="'.$idfa.'")';
			}

		}
		if($idli!=""){
			if($idli=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (p.linea="'.$idli.'")';
			}

		}
		if($idalmacen!=""){
			if($idalmacen =='0'){
				$filtro .='';
			}else{
				$filtro .=' and if (dd.codigo_sistema is null, oo.codigo_sistema, dd.codigo_sistema) LIKE "'.$idalmacen.'%"';
			}

		}
		if($hasta!=''){
			$filtro .=' and m.fecha <= "'.$hasta.'" ';
		}

		// obtine los movimientos segun el filtro(condicion)
		$query1 = "SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, if(dd.id is null, oo.id, dd.id) as almacen, if(dd.nombre is null, oo.nombre, dd.nombre) as almacenNombre, cc.codigo moneda, un.nombre unidad
					from app_inventario_movimientos m
					left join app_almacenes oo on oo.id = m.id_almacen_origen
					left join app_almacenes dd on dd.id = m.id_almacen_destino
					left join app_productos p on p.id = m.id_producto
					left join accelog_usuarios u on u.idempleado = m.id_empleado
					left join app_unidades_medida un on un.id = p.id_unidad_venta
					left join cont_coin cc on cc.coin_id = p.id_moneda
					where ".$filtro."
					order by almacen asc, p.codigo, m.fecha;";

		$result1 = $this->queryArray($query1);


		$query2 = "SELECT * from app_productos where status = 1";
		$result2 = $this->queryArray($query2);

		$query3 = "SELECT * from app_almacenes where activo = 1 order by codigo_sistema asc";
		$result3 = $this->queryArray($query3);

		$query4 = "SELECT * from app_departamento";
		$result4 = $this->queryArray($query4);

		return array('grid' => $result1['rows'] , 'productos' => $result2['rows'], 'almacenes' => $result3['rows'], 'departamentos' => $result4['rows']);
	}
	public function listarProductos($idProducto,$idUnidad,$idMoneda,$lote,$series,$pedi,$carac){

		$filtro = 'a.status = 1';

		if($idProducto!=""){
			if($idProducto=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (a.id="'.$idProducto.'")';
			}

		}
		if($idUnidad!=""){
			if($idUnidad=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (a.id_unidad_venta="'.$idUnidad.'")';
			}

		}
		if($idMoneda!=""){
			if($idMoneda=='0'){
				$filtro .='';
			}else{
				$filtro .=' and (a.id_moneda="'.$idMoneda.'")';
			}

		}

		if($lote==3){
			$filtro .='';
		}
		if($lote==1 or $lote==0){
			$filtro .=' and (a.lotes="'.$lote.'")';
		}

		if($series==3){
			$filtro .='';
		}
		if($series==1 or $series==0){
			$filtro .=' and (a.series="'.$series.'")';
		}

		if($pedi==3){
			$filtro .='';
		}
		if($pedi==1 or $pedi==0){
			$filtro .=' and (a.pedimentos="'.$pedi.'")';
		}

		if($carac==3){
			$filtro .='';
		}
		if($carac==1){
			$filtro .=" and ( if(d.id_producto is not null, 'SI', 'NO') = 'SI')";
		}
		if($carac==0){
			$filtro .=" and ( if(d.id_producto is not null, 'SI', 'NO') = 'NO')";
		}



		$myquery ="SELECT DISTINCT a.id, a.codigo, a.nombre producto, a.id_unidad_venta, b.nombre unidad,
					if(a.lotes = 1, 'SI','NO') as lotes,
					if(a.series = 1, 'SI','NO') as series,
					if(a.pedimentos = 1, 'SI','NO') as pedimentos,
					a.tipo_producto, if(a.tipo_producto = 1, 'Producto',if(tipo_producto = 2,'Servicio','NA')) as tipo,
					a.id_moneda, c.codigo moneda, if(d.id_producto is not null, 'SI', 'NO') as caracteristicas
					from app_productos a
					left join app_unidades_medida b on b.id = a.id_unidad_venta
					left join cont_coin c on c.coin_id = a.id_moneda
					left join app_producto_caracteristicas d on d.id_producto = a.id
					where ".$filtro."";
		$productos = $this->queryArray($myquery);
		return $productos["rows"];
	}
	public function selectProductosM(){
		$myquery = "SELECT id, nombre from app_productos where status = 1";
		$productos = $this->queryArray($myquery);
		return $productos["rows"];
	}
	public function selectUnidadesM(){
		$myquery = "SELECT id, clave, nombre FROM app_unidades_medida";
		$productos = $this->queryArray($myquery);
		return $productos["rows"];
	}
	public function selectMonedasM(){
		$myquery = "SELECT coin_id, codigo, description FROM cont_coin";
		$productos = $this->queryArray($myquery);
		return $productos["rows"];
	}

	 public function configPeriodos()
	{
		$myQuery = "SELECT (SELECT nombre FROM app_ejercicios WHERE id = id_ejercicio_actual) AS ejercicio_actual, permitir_cerrados, id_periodo_actual, periodos_abiertos FROM app_configuracion WHERE id = 1";
		$info = $this->query($myQuery);
		$info = $info->fetch_assoc();
		return $info;
	}

	public function ejerciciosDisponibles($tipo,$cerrado)
	{
		$per_cer = '';
		if(!intval($cerrado))
			$per_cer = "WHERE cerrado != 1";

		$myQuery = "SELECT nombre FROM app_ejercicios $per_cer ORDER BY nombre $tipo LIMIT 1";
		$info = $this->query($myQuery);
		$info = $info->fetch_assoc();
		return $info['nombre'];
	}

	public function listaSucursales()
	{
		return $this->query("SELECT idSuc, clave, nombre FROM mrp_sucursal WHERE activo = -1");
	}

	public function listaAlmacenesSuc($idSuc)
	{
		return $this->query("SELECT id, codigo_manual, nombre FROM app_almacenes WHERE id_sucursal = $idSuc and activo = 1 ORDER BY codigo_sistema");
	}

	public function series_slp($vars)
	{
		$prods = '';
		if(intval($vars['idprod']))
			$prods = "AND im.id_producto = ".$vars['idprod'];

		$almacenes = '';
		if(intval($vars['id_alm']))
			$almacenes = "AND (im.id_almacen_origen = ".$vars['id_alm']." OR im.id_almacen_destino = ".$vars['id_alm'].")";


		$tipoM = $vars['tipoM'];
		if($tipoM == '3') // todos ch@
		{
			$tipoM = '';
		}else{
			$tipoM = "AND im.tipo_traspaso = ".$tipoM;
		}



		$myQuery = "SELECT ps.id AS id_serie, ps.serie,
							ps.estatus,
							im.fecha,
							im.id AS Folio,
							im.tipo_traspaso AS Concepto,
							im.id_almacen_origen,
							im.id_almacen_destino,
							(SELECT CONCAT('(',codigo_manual,') ', nombre) FROM app_almacenes WHERE id = im.id_almacen_origen) AS Almacen_Origen,
							(SELECT CONCAT('(',codigo_manual,') ', nombre) FROM app_almacenes WHERE id = im.id_almacen_destino) AS Almacen_Destino,
							im.origen,
							im.id_producto,
							(SELECT nombre FROM app_productos WHERE id = im.id_producto) AS Producto,
							if(im.tipo_traspaso = 0,(SELECT id_oventa from app_envios where id = if(im.origen = 1, SUBSTRING_INDEX(im.referencia,'-',-1), 0)), if(im.tipo_traspaso = 1, (SELECT id_oc from app_recepcion where id = if(im.origen = 1, SUBSTRING_INDEX(im.referencia,'-',-1), 0)), 0)) folioO1,
							if(im.tipo_traspaso = 0, (SELECT nombre from comun_cliente where id = (SELECT idCliente from app_pos_venta where idVenta = SUBSTRING_INDEX(im.referencia,' ',-1))),null) cliente
							FROM app_producto_serie_rastro psr
							LEFT JOIN app_producto_serie ps ON ps.id = psr.id_serie
							LEFT JOIN app_inventario_movimientos im ON im.id = psr.id_mov
							WHERE im.fecha BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."'
							$prods
							$almacenes
							$tipoM
							ORDER BY im.id_producto,im.fecha desc,psr.id_serie, psr.id;";//echo $myQuery;die;
		return $this->query($myQuery);
	}

	public function series_slp2($vars)
	{
		$prods = '';
		if(intval($vars['idprod']))
			$prods = "AND im.id_producto = ".$vars['idprod'];

		$almacenes = '';
		if(intval($vars['id_alm']))
			$almacenes = "AND (im.id_almacen_origen = ".$vars['id_alm']." OR im.id_almacen_destino = ".$vars['id_alm'].")";


		$tipoM = $vars['tipoM'];
		if($tipoM == '3') // todos ch@
		{
			$tipoM = '';
		}else{
			$tipoM = "AND im.tipo_traspaso = ".$tipoM;
		}



		$myQuery = "SELECT
					im.id_producto,
					p.nombre AS Producto,
					ps.serie AS serie,
					ps.estatus AS estatus,
					IF(im.id_almacen_origen = 0 AND im.id_almacen_destino != 0 , IF( im.origen = 0 , 'Entrada', 'Compra' ) ,
						IF(im.id_almacen_origen != 0 AND im.id_almacen_destino = 0 , IF( im.origen = 0 , 'Salida', 'Venta' ) ,
							IF(im.id_almacen_origen != 0 AND im.id_almacen_destino != 0 , 'Traspaso' ,
							'' )
					 	)
					) concepto,

					IF(im.id_almacen_destino != 0 , im.fecha ,
						''
					) fechaE,
					IF( r.id IS NOT NULL  , r.id ,
						''
					) folioE,
					IF(im.id_almacen_origen != 0 , ( CONCAT('(',ao.codigo_manual,') ', ao.nombre) ) ,
						''
					) AlmacenE,

					IF(im.id_almacen_origen != 0 , im.fecha ,
						''
					) fechaS,
					IF( r.id IS NULL  , e.id ,
						''
					) folioS,
					IF(im.id_almacen_destino != 0 , ( CONCAT('(',ad.codigo_manual,') ', ad.nombre) ) ,
						''
					) AlmacenS,
					IF( r.id IS NULL  , c.nombre ,
						''
					) AS cliente,
					'B' AS aux


						FROM	app_inventario_movimientos im
						LEFT JOIN app_productos p ON im.id_producto = p.id
						LEFT JOIN app_almacenes ao ON im.id_almacen_origen = ao.id
						LEFT JOIN app_almacenes ad ON im.id_almacen_destino = ad.id

						LEFT JOIN app_producto_serie_rastro psr ON im.id = psr.id_mov
						LEFT JOIN app_producto_serie ps ON ps.id = psr.id_serie

						LEFT JOIN app_recepcion r ON SUBSTRING_INDEX(im.referencia,'-',-1) = r.id
						LEFT JOIN app_envios e ON SUBSTRING_INDEX(im.referencia,'-',-1) = e.id
						LEFT JOIN app_oventa ov ON  e.id_oventa = ov.id
						LEFT JOIN comun_cliente c ON ov.id_cliente = c.id

					WHERE im.fecha BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."'
							$prods
							$almacenes
							$tipoM
							ORDER BY im.id_producto,im.fecha desc,psr.id_serie, psr.id;";//echo $myQuery;die;
		return $this->queryArray($myQuery);
	}

	public function pedimentos_lotes_slp($vars)
	{
		$prods = '';
		if(intval($vars['idprod']))
			$prods = "AND im.id_producto = ".$vars['idprod'];

		$almacenes = '';
		if(intval($vars['id_alm']))
			$almacenes = "AND (im.id_almacen_origen = ".$vars['id_alm']." OR im.id_almacen_destino = ".$vars['id_alm'].")";

		//Lotes
		if($vars['opc'] == 2)
		{
			$no_lote_pedimento = "(SELECT no_lote FROM app_producto_lotes WHERE id = im.id_lote) AS Folio ";
			$lp = "im.id_lote";
		}

		//Pedimentos
		if($vars['opc'] == 3)
		{
			$no_lote_pedimento = "(SELECT no_pedimento FROM app_producto_pedimentos WHERE id = im.id_pedimento) AS Folio ";
			$lp = "im.id_pedimento";
		}

		$tipoM = $vars['tipoM'];
		if($tipoM == '3') // todos ch@
		{
			$tipoM = '';
		}else{
			$tipoM = "AND im.tipo_traspaso = ".$tipoM;
		}

		$myQuery = "SELECT  im.fecha,
							im.tipo_traspaso AS Concepto,
							im.id_almacen_origen,
							im.id_almacen_destino,
							(SELECT CONCAT('(',codigo_manual,') ', nombre) FROM app_almacenes WHERE id = im.id_almacen_origen) AS Almacen_Origen,
							(SELECT CONCAT('(',codigo_manual,') ', nombre) FROM app_almacenes WHERE id = im.id_almacen_destino) AS Almacen_Destino,
							im.cantidad,
							im.origen,
							im.id_producto,
							(SELECT nombre FROM app_productos WHERE id = im.id_producto) AS Producto,
							$no_lote_pedimento
							FROM app_inventario_movimientos im
							WHERE im.fecha BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."'
							$prods
							$almacenes
							$tipoM
							AND $lp != 0
							AND im.estatus = 1
							ORDER BY im.id_producto,$lp , im.id;";//echo $myQuery;die;

		return $this->query($myQuery);
	}

	public function caducos_slp($vars)
	{

		$prods = '';
		if(intval($vars['idprod']))
			$prods = "AND im.id_producto = ".$vars['idprod'];

		$almacenes = 'AND im.id_almacen_destino != 0';
		if(intval($vars['id_alm']))
			$almacenes = "AND im.id_almacen_destino = ".$vars['id_alm'].")";

		$myQuery = "SELECT  (SELECT clave FROM app_unidades_medida WHERE id = pr.id_unidad_venta) unidad_venta, (SELECT clave FROM app_unidades_medida WHERE id = pr.id_unidad_compra) unidad_compra, im.id_almacen_destino AS id_almacen,
				CONCAT('(',al.codigo_manual,') ',al.nombre) AS Almacen,
				LEFT(al.codigo_sistema,1) AS Almacen_Padre,
				(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE codigo_sistema = LEFT(al.codigo_sistema,1)) Nombre_Padre,
				codigo_sistema AS codigo_almacen,
				al.id_padre,
				pr.codigo, pr.nombre, lo.no_lote, lo.fecha_caducidad, lo.fecha_fabricacion,
				((SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = im.id_almacen_destino AND id_producto = pr.id AND fecha_caducidad <=  '".$vars['f_fin']."' AND id_lote = im.id_lote)-IFNULL((SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = im.id_almacen_destino AND id_producto = pr.id AND lo.fecha_caducidad <=  '".$vars['f_fin']."' AND id_lote = im.id_lote),0)) AS disponibles
				FROM app_producto_lotes lo
				LEFT JOIN app_inventario_movimientos im ON im.id_lote = lo.id
				LEFT JOIN app_productos pr ON pr.id = im.id_producto
				LEFT JOIN app_almacenes al ON al.id = im.id_almacen_destino
				WHERE lo.fecha_caducidad BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."'
				$prods
				$almacenes
				GROUP BY lo.id, codigo_almacen
				HAVING disponibles > 0
				ORDER BY codigo_almacen, pr.id, lo.id";//echo $myQuery;die;

		return $this->query($myQuery);
	}

	public function printer($idMov)
	{
		$res = $this->query("SELECT im.*,
			(SELECT CONCAT(codigo,' / ',nombre,'*|*',series) FROM app_productos WHERE id = im.id_producto) AS Producto,
			(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = im.id_almacen_origen) AS Almacen_Origen,
			(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = im.id_almacen_destino) AS Almacen_Destino
			FROM app_inventario_movimientos im WHERE im.id = (SELECT MAX(id) FROM app_inventario_movimientos )");
		return $res->fetch_assoc();
	}

	public function info_lote($id_lote)
	{
		$res = $this->query("SELECT* FROM app_producto_lotes WHERE id = $id_lote");
		return $res->fetch_assoc();
	}

	public function info_pedimento($id_pedimento)
	{
		$res = $this->query("SELECT* FROM app_producto_pedimentos WHERE id = $id_pedimento");
		return $res->fetch_assoc();
	}

	public function info_srs($idMov)
	{
		$res = $this->query("SELECT serie FROM app_producto_serie_rastro psr INNER JOIN app_producto_serie ps ON psr.id_serie = ps.id WHERE psr.id_mov = $idMov");
		return $res;
	}

	public function info_carac($idCaracs)
	{
		$idCaracs = explode("','",$idCaracs);
		$where = '';
		for($i=0;$i<=count($idCaracs)-1;$i++)
		{
			$idc = explode("=>'",$idCaracs[$i]);
			if($i>0)
				$where .= " OR ";
			$idc[1] = str_replace("'", "", $idc[1]);
			$where .= " ch.id = ".$idc[1];
		}


		$myQuery = "SELECT cp.nombre AS NombrePadre, ch.nombre AS NombreHija FROM app_caracteristicas_hija ch
					INNER JOIN app_caracteristicas_padre cp ON cp.id = ch.id_caracteristica_padre
					WHERE $where
					ORDER BY cp.id,ch.id";
		return $this->query($myQuery);
	}

	public function carac_hija()
	{
		$arr = Array();
		$myQuery = "SELECT id,nombre FROM app_caracteristicas_hija";
		$res = $this->query($myQuery);
		while($r = $res->fetch_assoc())
			$arr[$r['id']] = $r['nombre'];

		return json_encode($arr);
	}

	public function guardarLayPed($dato)
	{
		$this->query("INSERT INTO app_producto_pedimentos VALUES(0,'".$dato[1]."','".$dato[2]."','".$dato[3]."',".$dato[4].",'".$dato[5]."');");
	}

	public function guardarLayLot($dato)
	{
		$this->query("INSERT INTO app_producto_lotes VALUES(0,'".$dato[1]."','".$dato[2]."','".$dato[3]."');");
	}

	public function guardarLayMovs($dato) {
		//Caracteristicas
		if($dato[2] != '0')
		{
			$dato[2] = str_replace("=", "'=>'", $dato[2]);
			$dato[2] = "'".$dato[2]."'";

			$pos = strpos($dato[2],',');

			if($pos !== false)
			{
				$dato[2] = str_replace(",", "','", $dato[2]);
			}

		}
		else
			$dato[2] = "'0'";

		$caracs = $dato[2];

		//Pedimento
		if($dato[3] != '0')
		{
			$d = $this->query("SELECT id FROM app_producto_pedimentos WHERE no_pedimento = '".$dato[3]."'");
			$a = $d->fetch_assoc();
			$dato[3] = $a['id'];
		}


		//Lote
		if($dato[4] != '0')
		{
			$d = $this->query("SELECT id FROM app_producto_lotes WHERE no_lote = '".$dato[4]."'");
			$a = $d->fetch_assoc();
			$dato[4] = $a['id'];
		}

		//Guarda Movimiento
		$idMov = $this->insert_id("INSERT INTO app_inventario_movimientos VALUES (0,".$dato[1].",\"$caracs\",".$dato[3].",".$dato[4].",".$dato[5].",".$dato[6].",0,".$dato[7].",'".date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s') . ' -7 hour'))."',".$_SESSION["accelog_idempleado"].",1,".$dato[8].",'".$dato[9]."',1,0,'0');");

		//Guardar Series
		if($dato[10] != '' && $dato[10] != '0')
		{
			$pos = strpos($dato[10],',');

			if($pos === false)
			{
				$idSerie = $this->insert_id("INSERT INTO app_producto_serie VALUES(0,".$dato[1].",0,0,0,0,'".$dato[10]."',".$dato[7].",".$dato[3].",1);");
				$this->query("INSERT INTO app_producto_serie_rastro VALUES(0,$idSerie,".$dato[7].",'".date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s') . ' -7 hour'))."',$idMov);");
			}
			else
			{
				$d = explode(',',$dato[10]);
				for($i=0;$i<=count($d)-1;$i++)
				{
					$idSerie = $this->insert_id("INSERT INTO app_producto_serie VALUES(0,".$dato[1].",0,0,0,0,'".$d[$i]."',".$dato[7].",".$dato[3].",1);");
					$this->query("INSERT INTO app_producto_serie_rastro VALUES(0,$idSerie,".$dato[7].",'".date("Y-m-d H:i:s",strtotime(date('Y-m-d H:i:s') . ' -7 hour'))."',$idMov);");
				}

			}
		}
	}

	public function borrar()
	{
		$this->multi_query("TRUNCATE TABLE app_producto_pedimentos; TRUNCATE TABLE app_producto_lotes; TRUNCATE TABLE app_inventario_movimientos;TRUNCATE TABLE app_producto_serie;TRUNCATE TABLE app_producto_serie_rastro");
	}

	public function tipoCosteoProd($idProd){
            //Query para obtener el numero de requisicion nuevo (ultimo id + 1)
            $myQuery = "SELECT id_tipo_costeo from app_productos where id='$idProd';";
            //echo $myQuery.'<br>';
            $nreq = $this->queryArray($myQuery);
            $tc = $nreq['rows'][0]['id_tipo_costeo'];
            return $tc;
        }
        public function costeoProd($idprod){
            /*$myQuery = "SELECT id, SUM(costo*cantidad) AS t, SUM(cantidad) AS c
                        FROM  app_inventario_movimientos
                        WHERE id_producto = $idprod AND tipo_traspaso = 1 AND estatus = 1 AND costo != 0";
                        //echo $myQuery.'<br>';
            $res = $this->query($myQuery);
            $res = $res->fetch_assoc();
            return floatval($res['t']) / floatval($res['c']);*/

            /*$sql = "SELECT id, SUM(costo*cantidad * IF(tipo_traspaso=1,1,-1) ) AS t, SUM(cantidad * IF(tipo_traspaso=1,1,-1)) AS c , SUM(costo*cantidad * IF(tipo_traspaso=1,1,-1) ) / SUM(cantidad * IF(tipo_traspaso=1,1,-1)) costo_promedio
                        FROM  app_inventario_movimientos
                        WHERE id_producto = '1' AND estatus = 1 AND costo != 0 ;";*/

            /*$sql ="SELECT id, sum(costo*cantidad * IF(id_almacen_destino=0 OR referencia NOT LIKE '%Recepcion%',-1,1) ) AS t, sum(cantidad * IF(id_almacen_destino=0 OR referencia NOT LIKE '%Recepcion%',-1,1)) AS c , sum(costo*cantidad * IF(id_almacen_destino=0 OR referencia NOT LIKE '%Recepcion%',-1,1) ) / sum(cantidad * IF(id_almacen_destino=0 OR referencia NOT LIKE '%Recepcion%',-1,1)) costo_promedio
FROM  app_inventario_movimientos
WHERE id_producto = '$idprod' AND estatus = 1 AND costo != 0;";*/

			/*$sql ="SELECT id, IF(tipo_traspaso=1 OR referencia like '%Recepcion Movto:%',1,-1), sum(costo*cantidad * IF(tipo_traspaso=1 OR referencia like '%Recepcion Movto:%',1,-1) ) AS t, sum(cantidad * IF(tipo_traspaso=1 OR referencia like '%Recepcion Movto:%',1,-1) ) AS c , sum(costo*cantidad * IF(tipo_traspaso=1 OR referencia like '%Recepcion Movto:%',1,-1) ) / sum(cantidad * IF(tipo_traspaso=1 OR referencia like '%Recepcion Movto:%',1,-1) ) costo_promedio
					FROM  app_inventario_movimientos
					WHERE id_producto = '$idprod' AND estatus = 1 AND costo != 0;";*/
			$sql = "SELECT	(sum(valor) / sum(cantidad) ) costo_promedio
					FROM	app_inventario
					WHERE	id_producto='$idprod';";
            $res = $this->queryArray($sql);
            return $res['rows'][0][costo_promedio];
        }
        public function costeoUltimoCosto($idProducto){
        	/*Ultimo Costo*/
        	$sql = "SELECT costo
					FROM	app_inventario_movimientos
					WHERE	id_producto = '$idProducto' AND referencia LIKE '%Orden de compra / recepcion%'
					ORDER BY id DESC
					LIMIT	1;";
			$res = $this->queryArray($sql);
            return $res['rows'][0][costo];
        }
        public function costeoPEPS($idProducto){
        	/*PEPS*/
			/*$sql = "SELECT	SUM(cantidad) cantidad
					FROM	app_inventario_movimientos
					WHERE	id_producto = '1' AND (referencia LIKE '%Venta%')
					ORDER BY id ASC;";*/
			$sql = "SELECT	SUM(cantidad) cantidad
					FROM	app_inventario_movimientos
					WHERE	id_producto = '1' AND tipo_traspaso=0
					ORDER BY id ASC;";
			$resSalidas = $this->queryArray($sql);

			/*$sql = "SELECT	cantidad, costo
			FROM	app_inventario_movimientos
			WHERE	id_producto = '1' AND (referencia LIKE '%Orden de compra%' OR referencia LIKE 'Recepcion Movto' OR referencia LIKE '%Cancelacion%' OR referencia LIKE '%Devolución%')
			ORDER BY id ASC;";*/
			$sql = "SELECT	cantidad, costo
					FROM	app_inventario_movimientos
					WHERE	id_producto = '1' AND (tipo_traspaso=1 OR referencia like '%Recepcion Movto:%')
					ORDER BY id ASC;";
			$resEntradas = $this->queryArray($sql);

			$sumatoriaCantidadEntradas = 0;
			foreach ($resEntradas['rows'] as $key => $value) {
				$sumatoriaCantidadEntradas += $value['cantidad'];
				if ($sumatoriaCantidadEntradas > $resSalidas['rows'][0]['cantidad']){
					$costo = $value['costo'];
					break;
				}
			}
			return $costo;
        }
        public function costeoUEPS($idProducto){
        	/*UEPS*/
			/*$sql = "SELECT	SUM(cantidad) cantidad
					FROM	app_inventario_movimientos
					WHERE	id_producto = '1' AND (referencia LIKE '%Venta%')
					ORDER BY id DESC;";*/
			$sql = "SELECT	SUM(cantidad) cantidad
					FROM	app_inventario_movimientos
					WHERE	id_producto = '1' AND tipo_traspaso=0
					ORDER BY id DESC;";
			$resSalidas = $this->queryArray($sql);

			/*$sql = "SELECT	cantidad, costo
			FROM	app_inventario_movimientos
			WHERE	id_producto = '1' AND (referencia LIKE '%Orden de compra%' OR referencia LIKE 'Recepcion Movto' OR referencia LIKE '%Cancelacion%' OR referencia LIKE '%Devolución%')
			ORDER BY id DESC;";*/
			$sql = "SELECT	cantidad, costo
					FROM	app_inventario_movimientos
					WHERE	id_producto = '1' AND (tipo_traspaso=1 OR referencia like '%Recepcion Movto:%')
					ORDER BY id DESC;";
			$resEntradas = $this->queryArray($sql);

			$sumatoriaCantidadEntradas = 0;
			foreach ($resEntradas['rows'] as $key => $value) {
				$sumatoriaCantidadEntradas += $value['cantidad'];
				if ($sumatoriaCantidadEntradas > $resSalidas['rows'][0]['cantidad']){
					$costo = $value['costo'];
					break;
				}
			}
			return $costo;
        }

	public function costosSeries($series)
	{
		$series = explode("@|@",$series);
		$where = '';
				for($i=0;$i<=count($series)-2;$i++)
				{
					if($i)
						$where .= " || ";
					$where .= "s.id_serie = ".$series[$i];
				}

		$myQuery = "SELECT ps.id, ps.serie, m.costo FROM app_inventario_movimientos m
					RIGHT JOIN app_producto_serie_rastro s ON s.id_mov = m.id
					INNER JOIN app_producto_serie ps ON ps.id = s.id_serie
					WHERE ($where) AND m.tipo_traspaso = 1";
		$res = $this->query($myQuery);
		return $res;
	}

	public function costoL($id)
	{
		$myQuery = "SELECT costo
					FROM app_inventario_movimientos
					WHERE id_lote = $id AND tipo_traspaso = 1 AND estatus = 1 AND costo != 0 ORDER BY id ASC LIMIT 1";
		$res = $this->query($myQuery);
		$res = $res->fetch_assoc();
		return $res['costo'];
	}

	public function costoP($id)
	{
		$myQuery = "SELECT costo
					FROM app_inventario_movimientos
					WHERE id_pedimento = $id AND tipo_traspaso = 1 AND estatus = 1 AND costo != 0 ORDER BY id ASC LIMIT 1";
		$res = $this->query($myQuery);
		$res = $res->fetch_assoc();
		return $res['costo'];
	}
	public function generarAlmacenTransito($id_sis)
	{
		$id = $this->idAlmacenTransito($id_sis);
		if(!intval($id['id']))
		{
			$myQuery = "INSERT IGNORE INTO app_almacenes (id, codigo_sistema, codigo_manual, nombre, id_padre, id_sucursal, id_estado, id_municipio, direccion, id_almacen_tipo, id_empleado_encargado, telefono, ext, es_consignacion, id_clasificador, activo) VALUES(0, '$id_sis', 'tra-1', 'Transito', 0, 0, 14, 539, '', 1, 0, '', '12', 0, 0, 1);";
			$this->query($myQuery);
		}

	}

	public function idAlmacenTransito($id_sis)
	{
		$myQuery = "SELECT id FROM app_almacenes WHERE codigo_sistema = '$id_sis'";
		$id = $this->query($myQuery);
		$id = $id->fetch_assoc();
		return $id['id'];
	}

	public function genera_traslado()
	{
		//Cerrado = 0 ACTIVO
		//Cerrado = 1 RECIBIDO Y CERRADO
		//Cerrado = 2 CANCELADO
		$myQuery = "INSERT INTO app_inventario_traslados(clave,cerrado) VALUES(IFNULL((SELECT t.clave+1 FROM app_inventario_traslados t WHERE id_solicitante != 0 ORDER BY t.clave DESC LIMIT 1),1),2);";
		$id = $this->insert_id($myQuery);
		$myQuery = "SELECT id,clave FROM app_inventario_traslados WHERE id = $id;";
		$info = $this->query($myQuery);
		$info = $info->fetch_assoc();
		return $info['clave']."**/**".$info['id'];
	}

	public function info_traspaso_mod($idtras)
	{
		$myQuery = "SELECT* FROM app_inventario_traslados WHERE id = $idtras";
		$info = $this->query($myQuery);
		$info = $info->fetch_assoc();
		return $info['clave']."**/**".$info['id_almacen_origen']."**/**".$info['id_almacen_destino']."**/**".$info['fecha']."**/**".$info['referencia'];
	}

	public function info_traslado($id_tras)
	{
		$myQuery = "SELECT t.*,
					(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = t.id_almacen_origen) AS almacen_origen,
					(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = t.id_almacen_destino) AS almacen_destino,
					(SELECT usuario FROM accelog_usuarios WHERE idempleado = t.id_solicitante) AS solicitante
					FROM app_inventario_traslados t WHERE t.id = $id_tras;";
		$info = $this->query($myQuery);
		$info = $info->fetch_assoc();
		return $info['id']."**/**".$info['clave']."**/**".$info['id_almacen_destino']."**/**".$info['almacen_origen']."**/**".$info['almacen_destino']."**/**".$info['solicitante']."**/**".$info['fecha']."**/**".$info['referencia'];
	}

	public function tras_prods($tras)
	{
		$myQuery = "SELECT t.*, p.nombre, p.codigo, m.id_producto, m.cantidad, m.importe, m.id_producto_caracteristica
					FROM app_inventario_traslados_movimientos t
					INNER JOIN app_inventario_movimientos m ON m.id = t.id_movimiento
					INNER JOIN app_productos p ON p.id = m.id_producto
					WHERE t.id_traslado = $tras";

		$info = $this->query($myQuery);
		return $info;
	}

	public function guardar_traslado($vars)
	{
		$cont = 0;
		$myQuery = "UPDATE app_inventario_traslados SET id_almacen_origen = ".$vars['origen'].", id_almacen_destino = ".$vars['destino'].", id_solicitante = ".$_SESSION["accelog_idempleado"].", fecha = '".$vars['fecha']."', referencia = '".$vars['referencia']."', cerrado = 0 WHERE id = ".$vars['tras'];

		if($this->query($myQuery))
			$cont++;

		$myQuery = "UPDATE app_inventario_movimientos SET estatus = 1 WHERE id IN (SELECT id_movimiento FROM app_inventario_traslados_movimientos WHERE id_traslado = ".$vars['tras'].")";

		if($this->query($myQuery))
			$cont++;

		if($cont == 2)
			return 1;
		else
			return 0;

	}

	public function guardar_recepcion($id_cantidad,$vars)
	{
		$id_cantidad = explode(":",$id_cantidad);
		/*$myQuery = "SELECT* FROM app_inventario_movimientos WHERE id = ".$id_cantidad[0];
		$res = $this->query($myQuery);
		$res = $res->fetch_assoc();*/
		if(intval($id_cantidad[1]))
		{
			$myQuery = "INSERT INTO app_inventario_movimientos (id, id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus, origen)
					SELECT 0 AS id, id_producto, id_producto_caracteristica, id_pedimento, id_lote, ".$id_cantidad[1]." AS cantidad, (costo*".$id_cantidad[1].") AS importe, ".$vars['origen']." AS id_almacen_origen, ".$vars['destino']." AS id_almacen_destino, '".$vars['fecha']."' AS fecha, ".$_SESSION["accelog_idempleado"]." AS id_empleado, 2 AS tipo_traspaso, costo, 'Recepcion Movto: ".$id_cantidad[0]."' AS referencia, 1 AS estatus, 0 AS origen
					FROM app_inventario_movimientos WHERE id = ".$id_cantidad[0];
			if($insid = $this->insert_id($myQuery))
			{
				$myQuery = "UPDATE app_inventario_traslados_movimientos SET id_movimiento_rec = $insid, recibidos = ".$id_cantidad[1]." WHERE id_movimiento = ".$id_cantidad[0];
				if($this->query($myQuery))
				{
					$myQuery = "INSERT INTO app_producto_serie_rastro(id,id_serie,id_almacen,fecha_reg,id_mov)
								SELECT 0 AS id, id_serie, ".$vars['destino']." AS id_almacen, '".date("Y-m-d H:i:s")."' AS fecha_reg, $insid  AS id_mov FROM app_producto_serie_rastro WHERE id_mov = ".$id_cantidad[0];
					if($this->query($myQuery))
					{
						$myQuery = "UPDATE app_producto_serie SET id_almacen = ".$vars['destino']." WHERE id IN (SELECT id_serie FROM app_producto_serie_rastro WHERE id_mov = $insid)";
						$this->query($myQuery);
					}

					$num = 1;
				}
				else
					$num = 0;
			}
		}

		return $num;
	}

	public function listaTraspasos($t)
	{
		$where = "";
		if(intval($t))
			$where = "t.cerrado = 0 AND";

		$myQuery = "SELECT t.*, (SELECT SUM(recibidos) FROM app_inventario_traslados_movimientos WHERE id_traslado = t.id) AS recibidos, (SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = id_almacen_origen) AS almacen_origen,
		(SELECT CONCAT('(',codigo_manual,') ',nombre) FROM app_almacenes WHERE id = id_almacen_destino) AS almacen_destino, (SELECT usuario FROM accelog_usuarios WHERE idempleado = id_solicitante) AS empleado FROM app_inventario_traslados t WHERE $where t.id_solicitante != 0";
		$info = $this->query($myQuery);
		return $info;
	}


	//FUNCIONES QUE CANCELAN

	public function cancelar_accion($idmov)
	{
		$this->query("UPDATE app_inventario_movimientos SET estatus = 0 WHERE id = $idmov");
	}

	public function cancelar_movto($id)
	{
		$idMov = $this->query("SELECT id_movimiento,id_traslado FROM app_inventario_traslados_movimientos WHERE id = $id");
		if($idMov = $idMov->fetch_assoc())
		{
			$id_tras = $idMov['id_traslado'];
			$idMov = $idMov['id_movimiento'];
			$this->query("UPDATE app_inventario_traslados_movimientos SET id_traslado = 0 WHERE id = $id");
			if($this->query("UPDATE app_inventario_movimientos SET estatus = 0 WHERE id = $idMov;"))
			{
					$this->cancela_series($idMov);
			}
			return $id_tras;
		}
		else
			return 0;

	}

	public function cancela_series($idMov)
	{
		//selecciona el id de la serie del movimiento
					$myQuery = "SELECT id_serie FROM app_producto_serie_rastro WHERE id_mov = ".$idMov;
					$id_serie = $this->query($myQuery);
					if($id_serie = $id_serie->fetch_assoc())
					{
						$id_serie = $id_serie['id_serie'];
						$myQuery = "DELETE FROM app_producto_serie_rastro WHERE id_mov = ".$idMov;

						//borra el ultimo rastro
						if($this->query($myQuery))
						{
							$myQuery = "SELECT id_almacen FROM app_producto_serie_rastro WHERE id_serie = ".$id_serie." ORDER BY id DESC LIMIT 1";

							//Busca el almacen del penultimo movimiento de la serie
							if($id_almacen = $this->query($myQuery))
							{
								$id_almacen = $id_almacen->fetch_assoc();
								$id_almacen = $id_almacen['id_almacen'];
								$myQuery = "UPDATE app_producto_serie SET id_almacen = ".$id_almacen." WHERE id = ".$id_serie;
								$this->query($myQuery);
							}
						}
					}
	}

	//Cancelar traspaso

	public function cancelar_traspaso($idtras)
	{
		//cierra el traslado
		$myQuery = "UPDATE app_inventario_traslados SET cerrado = 2 WHERE id = $idtras";
		if($this->query($myQuery))
		{
			//selecciona los id de los movimientos implicados en el traslado
			$myQuery = "SELECT id_movimiento FROM app_inventario_traslados_movimientos WHERE id_traslado = $idtras";
			$movs = $this->query($myQuery);
			while($m = $movs->fetch_assoc())
			{
				//cancela el movimiento en el inventario
				$myQuery = "UPDATE app_inventario_movimientos SET estatus = 0 WHERE id = ".$m['id_movimiento'];
				if($this->query($myQuery))
				{
					 $this->cancela_series($m['id_movimiento']);
				}
			}
		}
		return 1;
	}

	public function tipoinstancia()
	{
		$res = $this->query("SELECT tipoinstancia, sincronizar FROM organizaciones WHERE idorganizacion = 1");
		$res = $res->fetch_assoc();
		if($res['tipoinstancia'] == 2 && $res['sincronizar'] == 1)
			return 1;
		else
			return 0;

	}

	function conectar_transversal()
	{
		if(!$this->connection_transversal = mysqli_connect("34.66.63.218","nmdevel","nmdevel","netwarstore"))
			{
				echo "<br><b style='color:red;'>Error al tratar de conectar a la instancia transversal</b><br>";
			}

		//echo "Crear clase de conexion";

	}

	function destruir_conexion()
	{
		$this->connection_transversal->close();
		//echo "Destruir conexion";
	}

	public function listainstancias($instanciaPadre)
	{
		$this->conectar_transversal();

		$myQuery = "SELECT id FROM customer WHERE instancia = '$instanciaPadre'";
		$idinstancia = $this->connection_transversal->query($myQuery);

	 	$idinstancia = $idinstancia->fetch_assoc();

	 	$myQuery = "SELECT
						c.instancia, c.nombre_db
					FROM
						relacion_profesores_alumnos rpa
					INNER JOIN
						customer c ON c.id = rpa.idalumno
					WHERE
						rpa.idprofesor = " . $idinstancia['id'];
	 	$instancias = $this->connection_transversal->query($myQuery);
	 	$this->destruir_conexion();
	 	return $instancias;
	}

	public function guardar_en_instancia($vars)
	{
		$this->conectar_transversal();

		//GENERAR TRASLADO EN LA INSTANCIA HIJA
		$instancia = $vars['instancia'];
		$idprod = $vars['idprod'];
		$caracteristicas = $vars['caracteristicas'];
		$id_pedimento = 0;
		$id_lote = 0;
		if($vars['lotes'] != '')
			$id_lote = $vars['lotes'];
		$cantidad = $vars['cantidad'];
		$importe = $vars['importe'];
		$fecha = $vars['fecha'];
		$costo = $vars['costo'];
		$referencia = $vars['referencia'] . '*tras*';
		$estatus = 1;

		if($caracteristicas == '')
			$caracteristicas = "'0'";

		//INSERTAR REGISTRO DE TRASPASO
		$myQuery = "INSERT INTO $instancia app_inventario_traslados(clave,id_almacen_origen,id_almacen_destino,id_solicitante,fecha,referencia,cerrado) VALUES(IFNULL((SELECT t.clave+1 FROM $instancia app_inventario_traslados t WHERE id_solicitante != 0 ORDER BY t.clave DESC LIMIT 1),1),0,1,1,'$fecha','traspaso de instancia padre',0);";
		$this->connection_transversal->query($myQuery);
		$tras = $this->connection_transversal->insert_id;

		//INSRTAR MOVIMIENTO
		 $myQuery = "INSERT INTO $instancia app_inventario_movimientos(id, id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus,origen) VALUES(0, $idprod, \"$caracteristicas\", $id_pedimento, $id_lote, $cantidad, $importe, 0, (SELECT id FROM $instancia app_almacenes WHERE codigo_sistema = '999'), '$fecha ".date("H:m:s",strtotime(date('H:i:s') . ' -7 hour'))."', 1, 1, $costo, '$referencia',$estatus,0);";
		if($this->connection_transversal->query($myQuery)){
			//ID del ultimo movimiento
			$id_mov = $this->connection_transversal->insert_id;

			//INSERTAR MOVIMIENTOS DEL TRASPASO
			$query_traspaso = "INSERT INTO $instancia app_inventario_traslados_movimientos(id,id_traslado,id_movimiento,recibidos) VALUES(0,$tras,".$id_mov.",0)";

			$this->connection_transversal->query($query_traspaso);

			$series = $_POST['series'];

			//SI TIENE SERIES LAS INSERTA EN LAS TABLAS CORRESPONDIENTES
			if($series != ''){
				$myQuery = '';
				$series = explode("@|@",$series);
				for($i=0;$i<=count($series)-2;$i++){
					$numserie = $this->query("SELECT serie FROM app_producto_serie WHERE id = $series[$i]");
					$numserie = $numserie->fetch_object();

					$myQuery .= "INSERT INTO $instancia app_producto_serie(id, id_producto, id_ocompra, id_recepcion, id_venta, estatus, serie, id_almacen, id_pedimento) VALUES(0,$idprod,0,0,0,0,'$numserie->serie',(SELECT id FROM $instancia app_almacenes WHERE codigo_sistema = '999'),0);";

					$myQuery .= "INSERT INTO $instancia app_producto_serie_rastro(id,id_serie,id_almacen,fecha_reg,id_mov) VALUES(0,(SELECT id FROM $instancia app_producto_serie WHERE serie = '$numserie->serie'),2,'".date("Y-m-d H:i:s")."',$id_mov);";
				}
				$this->connection_transversal->multi_query($myQuery);
			}
		}

		$this->destruir_conexion();
	}

}
?>
