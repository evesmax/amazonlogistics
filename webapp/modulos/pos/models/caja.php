<?php 
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL
global $api_lite;
if(!isset($api_lite)){
	if(!isset($_REQUEST["netwarstore"])) require("models/connection_sqli_manual.php"); // funciones mySQLi
	else require("../webapp/modulos/pos/models/connection_sqli_manual.php"); // funciones mySQLi
}
else require $api_lite . "/modulos/pos/models/connection_sqli_manual.php";

class CajaModel extends Connection
{

	public function indexGridProductos(){
		$query = "SELECT * from app_productos where status=1 order by id asc";
		$rest = $this->queryArray($query);

		return $rest['rows'];
	}
	public function buscaClientes($term) {
		/*obtiene los clientes*/
		/*$queryClientes = "SELECT  cl.id,cl.nombre,t.numero ";
		$queryClientes .= " FROM comun_cliente cl, tarjeta_regalo t";
		$queryClientes .= " WHERE (cl.nombre like '%" . $term . "%' or t.numero like '%".$term."%') order by nombre desc "; */


		$queryClientes = "SELECT  id,nombre ";
		$queryClientes .= " FROM comun_cliente ";
		//$queryClientes .= " WHERE nombre like '%" . $term . "%' and borrado=0 order by nombre desc ";
		$queryClientes .= " WHERE (nombre like '%".$term."%' or codigo like '%".$term."%') and borrado=0 order by nombre desc ";
		$queryClientes;

		$result = $this->queryArray($queryClientes);
		//print_r($result["rows"]);
		return $result["rows"];

	}
	public function buscaVendedores($term) {
		/*obtiene los vendedores*/
		$sql = "SELECT idadmin idempleado, CONCAT( CONCAT ( CONCAT( CONCAT(nombre, ' ') , apellidos), ' | ') , nombreusuario) nombre
				FROM administracion_usuarios
				WHERE nombre LIKE '%$term%' OR  apellidos LIKE '%$term%' OR nombreusuario LIKE '%$term%'
				ORDER BY nombre, apellidos, nombreusuario ";

		$result = $this->queryArray($sql);
		//print_r($result["rows"]);
		return $result["rows"];

	}
	public function formasDePago(){
		$query = "select * from view_forma_pago WHERE activo = 1 ORDER BY claveSat ASC ";
		$res = $this->queryArray($query);

		return array('formas' => $res['rows'] );
	}
	public function listaFormasPago()
    {
        $query = "SELECT* FROM view_forma_pago WHERE claveSat < '99' ORDER BY claveSat";

        $res = $this->queryArray($query);

		return array('formas' => $res['rows'] );
    }
	public function esComanda($idProducto){
		$sel = 'SELECT id from app_productos where codigo="'.$idProducto.'"';
		$res = $this->queryArray($sel);

		return $res['total'];

	}
	public function pintaRegistros() {
		//unset($_SESSION['caja']);
		//unset($_SESSION['pagos-caja']);
		//Consultamos si hay ventas suspendidas
		$suspendidas = '';
		$dataInicio = false;
		$selectVentasSuspendidas = "SELECT id,identi from app_pos_venta_suspendida ";
		$selectVentasSuspendidas .= " where borrado = 0";

		$resultVentasSuspendidas = $this->queryArray($selectVentasSuspendidas);
		if ($resultVentasSuspendidas["total"] > 0) {
			$suspendidas = $resultVentasSuspendidas["rows"];
		}

		$verificaInicio = $this->verificainicioCaja();
		if ($verificaInicio == 'false') {
			$dataInicio = $this->iniciocaja();
		}

		//// corte parcial
			$verificaInicioP = $this->verificainicioCajaP(); 
		//// corte parcial fin 

		if (isset($_SESSION['caja'])) {
			return array('estatus' => true, "productos" => $_SESSION['caja'], "cargos" => $_SESSION["caja"]['cargos'], "simple" => $_SESSION["simple"], "suspendidas" => $suspendidas, "inicio" => $dataInicio, "sucursal" => $_SESSION["sucursalNombre"], "empleado" => $_SESSION["nombreEmpleado"],'descGeneral' => $_SESSION['caja']['descGeneral'],'inicioP' => $verificaInicioP);
		} else {
			return array('status' => false, "suspendidas" => $suspendidas, "inicio" => $dataInicio,'inicioP' => $verificaInicioP);
		}
	}
	public function verificainicioCaja() {

		$empleado = "SELECT nombre from empleados where idEmpleado = " . $_SESSION['accelog_idempleado'];
		$empleadoResult = $this->queryArray($empleado);

		$_SESSION["nombreEmpleado"] = $empleadoResult["rows"][0]["nombre"];

		if (!isset($_SESSION["sucursal"])) {
			$qry = "SELECT ";
			$qry .= "   au.idSuc ";
			$qry .= " ,mp.nombre ";
			$qry .= "FROM ";
			$qry .= "   administracion_usuarios au ";
			$qry .= "   INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc ";
			$qry .= "WHERE ";
			$qry .= "   au.idempleado = " . $_SESSION['accelog_idempleado'] . " ";
			$qry .= "LIMIT 1;";

			$q = $this->queryArray($qry);

			if ($q["total"] > 0) {

				foreach ($q["rows"] as $key => $value) {
					$_SESSION["sucursal"] = $value["idSuc"];
					$_SESSION["sucursalNombre"] = $value["nombre"];
				}
			} else {
				$_SESSION["sucursal"] = 1;
				$_SESSION["sucursalNombre"] = "Sucursal";
			}
		}

		$qry2 = "SELECT ";
		$qry2 .= "id ";
		$qry2 .= "FROM ";
		$qry2 .= "app_pos_inicio_caja ";
		$qry2 .= "WHERE ";
		$qry2 .= "idSucursal = " . $_SESSION['sucursal'] . " ";
		$qry2 .= "AND idUsuario = " . $_SESSION['accelog_idempleado'] . " and idcorteCaja is null ";
		$qry2 .= "ORDER BY ";
		$qry2 .= "id desc ";
		$qry2 .= "LIMIT 1;";

		$q = $this->queryArray($qry2);

		if ($q["total"] > 0) {
			foreach ($q["rows"] as $key => $value) {
				if (is_numeric($value["idCortecaja"])) {//Selecciona el corte y si ese dato no es nulo ese inicio esta cerrado
					return "false";
				} else {
					return "true"; // si el registro traido es nulo envia 0
				}
			}
		} else {// Si ni siquiera tiene registro de inicio de caja
			return "false";
		}
	}
	public function iniciocaja() {

		$queryUsuarios = "SELECT au.idSuc,mp.nombre from administracion_usuarios au,mrp_sucursal mp where mp.idSuc=au.idSuc and au.idempleado=" . $_SESSION['accelog_idempleado'];

		$queryUsuarios = $this->queryArray($queryUsuarios);

		if ($queryUsuarios["total"] > 0) {

			$sucursal_operando = $queryUsuarios["rows"][0]["nombre"];
			$sucursal_id = $queryUsuarios["rows"][0]["idSuc"];

			//var_dump("select  cc.saldofinalcaja from inicio_caja i inner join corte_caja cc on i.idCortecaja=cc.idCortecaja where i.idSucursal=".$sucursal_id." order by i.fecha desc limit 1");

			$queryInicioCaja = "SELECT  cc.saldofinalcaja from app_pos_inicio_caja i inner join app_pos_corte_caja cc on i.idCortecaja=cc.idCortecaja where i.idSucursal=" . $sucursal_id . " order by i.fecha desc limit 1";

			$queryInicioCaja = $this->queryArray($queryInicioCaja);

			if ($queryInicioCaja["total"] > 0) {
				$saldoencaja = "$" . number_format($queryInicioCaja["rows"][0]["saldofinalcaja"], 2, ".", ",");
			} else {
				$saldoencaja = "$0.00";
			}

			return array("status" => 1, "sucursalNombre" => $sucursal_operando, "sucursalId" => $sucursal_id, "saldo" => $saldoencaja);
		} else {

			$cbo = '<select id="sucursal" name="sucursal" onchange="cargasaldocaja(this.value);" >';
			$query = "SELECT idSuc id,nombre  from mrp_sucursal";

			$query = $this->queryArray($query);

			return array("status" => 2, "rows" => $query["rows"]);
		}
	}
	public function buscaProductos($term){
		 $return = array();

		//$sellista = "SELECT * from app_producto_sucursal";
		//$res = $this->queryArray($sellista);
		/*if($res['total'] > 0){
			$selctPro = "SELECT * from app_productos p left join app_producto_sucursal ps on ps.id_producto=p.id where p.status='1' and p.nombre like '%".$term."%' and ps.id_sucursal='".$_SESSION['sucursal']."'";
		}else{ */
			$selctPro = "SELECT codigo,nombre,id from app_productos where status='1' and vendible=1 and tipo_producto!=8 and (nombre like '%".$term."%' or codigo like '%".$term."%') limit 25";
			//echo $selctPro;
		//}

		 $resutlSelec = $this->queryArray($selctPro);

		 foreach ($resutlSelec['rows'] as $key => $value) {
			  $x = $this->existenciaProducto($value["id"]);
			  $x = $x - $_SESSION['caja'][$value["id"]]->cantidad;
			  array_push($return, array('id' => $value["codigo"], 'label' => $value['codigo'] . " / " . $value['nombre'].': '.$x));
		 }
		 return $return;

	}
	public function buscaProductosRapido($term){
		$idAlmacen = $this->obtenAlm();
		$sql = "SELECT
				p.codigo AS id,
				CONCAT(p.codigo,' / ',p.nombre,' : ',i.cantidad ) AS label
				#p.nombre nombre, p.codigo codigo,
				#id_producto,
				#cantidad
					FROM app_inventario i
					INNER JOIN app_productos p ON i.id_producto = p.id
					WHERE	p.status='1'  AND p.tipo_producto != '6' AND p.tipo_producto != '8' AND (p.nombre LIKE '%$term%' OR p.codigo LIKE '%$term%') AND i.id_almacen = '$idAlmacen'
				UNION
				SELECT
				p.codigo AS id,
				CONCAT(p.codigo,' / ',p.nombre,' : ',MIN( i.cantidad / k.cantidad ) ) AS label
				#p.nombre nombre, p.codigo codigo,
				#k.id_kit id_producto,
				#MAX( i.cantidad / k.cantidad ) cantidad
					FROM com_kitsXproductos k
					INNER JOIN app_productos p ON k.id_kit = p.id
					INNER JOIN app_inventario i ON k.id_producto = i.id_producto
					WHERE	p.status='1' AND p.tipo_producto != '8' AND (p.nombre LIKE '%$term%' OR p.codigo LIKE '%$term%')  AND i.id_almacen = '$idAlmacen'
					GROUP BY k.id_kit";
					//echo $sql;die;
		$res = $this->queryArray($sql);
		return $res['rows'];
	}

	public function datosFacturacion($id) {
		if ($id != '') {
			$datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
			e.estado estado,ciudad,municipio,regimen_fiscal
			from comun_facturacion cf left join estados e on  e.idestado=cf.estado
			where  id=" . $id;

			$result = $this->queryArray($datosFacturacion);

			if ($result["total"] > 0) {
				return $result["rows"][0];
			}
		} else {
			return false;
		}
	}
	public function datosFacturacion2($id,$cliprov) {
		//echo '$'.$id;
		if ($id != '') {
			$datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
			e.estado estado,ciudad,municipio,regimen_fiscal
			from comun_facturacion cf left join estados e on  e.idestado=cf.estado
			where  cliPro='".$cliprov."' and  nombre=" . $id;
			//echo $datosFacturacion;
			$result = $this->queryArray($datosFacturacion);

			if ($result["total"] > 0) {
				return $result["rows"][0];
			}
		} else {
			return false;
		}
	}
	public function datosFacturacionCom($id,$cliprov){
			if ($id != '') {
			$datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
			e.estado estado,ciudad,municipio,regimen_fiscal
			from comun_facturacion cf left join estados e on  e.idestado=cf.estado
			where  cliPro='".$cliprov."' and  nombre=" . $id;
			//echo $datosFacturacion;
			$result = $this->queryArray($datosFacturacion);

			if ($result["total"] > 0) {
				$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
				$logo = $this->queryArray($logo);
				$r3 = $logo["rows"][0]['logoempresa'];

				return array('correo' => $result["rows"][0]['correo'], 'logo' => $r3);
			}
		} else {
			return false;
		}
	}
		public function datosFacturacion3($rfc,$cliprov) {
		//echo '$'.$id;
			$id = 1;
		if ($id != '') {
			$datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
			e.estado estado,ciudad,municipio,regimen_fiscal
			from comun_facturacion cf left join estados e on  e.idestado=cf.estado
			where  cliPro='".$cliprov."' and  rfc='".$rfc."' ";
			//echo $datosFacturacion;
			$result = $this->queryArray($datosFacturacion);

			if ($result["total"] > 0) {
				return $result["rows"][0];
			}
		} else {
			return false;
		}
	}
	public function agregPodCar($idProducto,$cantidadInicial,$caracteristicas){

	}
	public function existenciaProducto($idProducto){
		$idAlmacen = $this->obtenAlm();

		//$idAlmacen = 'q33';
	   /* $query = "(SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, p.id_tipo_costeo costeo, m.id_almacen_destino as aux, 0 as traspasoaux, rr.codigo_sistema, left(rr.codigo_sistema,1) almacenRR, un.clave unidad, x.nombre nombreAlmacen, m.id_producto_caracteristica caract, alr.nombre almacenUbicacion
						from app_inventario_movimientos m
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_almacenes rr on rr.id = oo.id
											left join app_productos p on p.id = m.id_producto
											left join accelog_usuarios u on u.idempleado = m.id_empleado
											left join app_unidades_medida un on un.id = p.id_unidad_venta
											left join cont_coin cc on cc.coin_id = p.id_moneda
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema

						where m.tipo_traspaso = 0 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN ('".$idAlmacen."')) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN ('".$idAlmacen."'))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN ('".$idAlmacen."')) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN ('".$idAlmacen."'))) and m.id_producto=".$idProducto.")
						union all
						(SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, p.id_tipo_costeo costeo, m.id_almacen_origen as aux, 1 as traspasoaux, rr.codigo_sistema, left(rr.codigo_sistema,1) almacenRR, un.clave unidad, x.nombre nombreAlmacen, m.id_producto_caracteristica caract, alr.nombre almacenUbicacion
						from app_inventario_movimientos m
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_almacenes rr on rr.id = dd.id
											left join app_productos p on p.id = m.id_producto
											left join accelog_usuarios u on u.idempleado = m.id_empleado
											left join app_unidades_medida un on un.id = p.id_unidad_venta
											left join cont_coin cc on cc.coin_id = p.id_moneda
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema

						where m.tipo_traspaso = 1 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN (".$idAlmacen.")) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN (".$idAlmacen."))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN (".$idAlmacen.")) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN (".$idAlmacen."))) and m.id_producto=".$idProducto.")
						union all
						(SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, p.id_tipo_costeo costeo, m.id_almacen_origen as aux, 0 as traspasoaux, rr.codigo_sistema, left(rr.codigo_sistema,1) almacenRR, un.clave unidad, x.nombre nombreAlmacen, m.id_producto_caracteristica caract, alr.nombre almacenUbicacion
						from app_inventario_movimientos m
											left join app_almacenes rr on rr.id = m.id_almacen_origen
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_productos p on p.id = m.id_producto
											left join accelog_usuarios u on u.idempleado = m.id_empleado
											left join app_unidades_medida un on un.id = p.id_unidad_venta
											left join cont_coin cc on cc.coin_id = p.id_moneda
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema

						where m.tipo_traspaso = 2 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN (".$idAlmacen.")) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN (".$idAlmacen."))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN (".$idAlmacen.")) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN (".$idAlmacen.")))
							 and m.id_producto=".$idProducto.")
						union all
						(SELECT m.id, p.nombre, p.codigo, m.cantidad, m.importe , m.fecha, u.usuario, m.tipo_traspaso, m.costo, m.referencia, m.id_producto, oo.id idorigen, oo.nombre origen, dd.id iddestino, dd.nombre destino, p.id_tipo_costeo costeo, m.id_almacen_destino as aux, 1 as traspasoaux, rr.codigo_sistema, left(rr.codigo_sistema,1) almacenRR, un.clave unidad, x.nombre nombreAlmacen, m.id_producto_caracteristica caract, alr.nombre almacenUbicacion
						from app_inventario_movimientos m
											left join app_almacenes rr on rr.id = m.id_almacen_destino
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_productos p on p.id = m.id_producto
											left join accelog_usuarios u on u.idempleado = m.id_empleado
											left join app_unidades_medida un on un.id = p.id_unidad_venta
											left join cont_coin cc on cc.coin_id = p.id_moneda
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema

						where m.tipo_traspaso = 2 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN (".$idAlmacen.")) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN (".$idAlmacen."))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN (".$idAlmacen.")) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN (".$idAlmacen."))) and m.id_producto=".$idProducto.")
						ORDER BY almacenRR, codigo, fecha ASC;"; */
						//echo $query;

	$query = ' (SELECT m.id, m.cantidad, m.id_producto, 0 as traspasoaux
						from app_inventario_movimientos m use index (id_producto_idx)
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_almacenes rr on rr.id = oo.id
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema

					 where m.tipo_traspaso = 0 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))) and m.id_producto='.$idProducto.')

						union all
(SELECT m.id, m.cantidad, m.id_producto, 1 as traspasoaux
						from app_inventario_movimientos m use index (id_producto_idx)
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_almacenes rr on rr.id = dd.id
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema


						where m.tipo_traspaso = 1 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))) and m.id_producto='.$idProducto.')
						union all
(SELECT m.id, m.cantidad, m.id_producto, 0 as traspasoaux
						from app_inventario_movimientos m use index (id_producto_idx)
											left join app_almacenes rr on rr.id = m.id_almacen_origen
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema


						where m.tipo_traspaso = 2 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.')))
							 and m.id_producto='.$idProducto.')
						union all
(SELECT m.id, m.cantidad, m.id_producto, 1 as traspasoaux
 from app_inventario_movimientos m use index (id_producto_idx)
											left join app_almacenes rr on rr.id = m.id_almacen_destino
											left join app_almacenes oo on oo.id = m.id_almacen_origen
											left join app_almacenes dd on dd.id = m.id_almacen_destino
											left join app_almacenes x on x.id = left(rr.codigo_sistema,1)
											left join app_almacenes alr on alr.codigo_sistema = rr.codigo_sistema


						where m.tipo_traspaso = 2 and ((m.tipo_traspaso = 1 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 0 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))or
							 (m.tipo_traspaso = 2 and left(dd.codigo_sistema,1) IN ('.$idAlmacen.')) or
							 (m.tipo_traspaso = 2 and left(oo.codigo_sistema,1) IN ('.$idAlmacen.'))) and m.id_producto='.$idProducto.');';
			$cantiF = 0;
			$query = "SELECT * from app_inventario where id_producto='".$idProducto."' and id_almacen='".$idAlmacen."';";

			$sql = "SELECT * FROM app_productos WHERE id = '$idProducto'";
			$res = $this->queryArray($sql);
			if($res['rows'][0]['tipo_producto'] == 6) {
				$query = "SELECT MAX( ip.cantidad / k.cantidad ) cantidad \n
						FROM com_kitsXproductos k \n
						INNER JOIN app_inventario ip ON k.id_producto = ip.id_producto \n
						WHERE id_kit = '$idProducto' AND id_almacen = '1';";
			}

			$resQuery = $this->queryArray($query);
			foreach ($resQuery['rows'] as $key => $value) {
				$cantiF +=  $value['cantidad'];
			}
			//$cantidad = $resQuery['rows'][0]['cantidad']*1;
			//$apartados = $resQuery['rows'][0]['apartados']*1;
			//print_r($resQuery['rows']);
			/*foreach ($resQuery['rows'] as $key => $value) {
				if($value['traspasoaux']==1){
					$entradas += floatval($value['cantidad']);
				}else{
					$salidas += floatval($value['cantidad']);
				}
			}

			$total = $entradas - $salidas; */
			$total = ($cantiF * 1);
			//$total = $cantidad - $apartados;
			//echo 'XXX'.$total;
			return $total;
	}
	public function get_promocion($id_promocion) {
		$sql = "SELECT
					nombre, tipo, cantidad, cantidad_descuento, descuento, precio_fijo
				FROM
					com_promociones
				WHERE
					id = " . $id_promocion . "
				AND
					status = 1";
		// return $sql;
		$promocion = $this -> queryArray($sql);

		return $promocion['rows'][0];
	}

	function get_promociones($id_dependencia, $id_promocion) {
		$sql = "SELECT a.id, a.dependencia_promocion, b.codigo, (CASE a.dependencia_promocion
						WHEN 0 THEN
							'0'
						ELSE c.comprar END) as comprar, c.recibir, a.idproducto, SUM(a.cantidad) AS cantidad, b.nombre,
			ROUND(b.precio, 2) AS precio, opcionales, adicionales, sin, a.status,
			a.complementos, a.id_promocion, a.costo, (0) as sumaExtras
			FROM com_pedidos a
			LEFT JOIN app_productos b ON b.id = a.idproducto
			LEFT JOIN com_promocionesXproductos c on a.idproducto = c.id_producto AND c.id_promocion = ".$id_promocion."
			WHERE a.dependencia_promocion = ".$id_dependencia."
			AND cantidad > 0
			GROUP BY status, a.id, a.opcionales, a.adicionales, a.complementos
			ORDER BY comprar desc, b.precio desc, b.id asc;";
			//print_r($sql); exit();
		// return $sql;
		$productsComanda = $this -> queryArray($sql);
		$array = Array("rows");

		$contador = 0;
		$sumaExtras = 0;

		// Recorre los registros para formar una cadena de lo opcionales, extra y sin
		foreach ($productsComanda['rows'] as $value) {
			/* Impuestos del producto
			 ============================================================================= */

			$precio = $value['precio'];
			$objeto['id'] = $value['idproducto'];
			$costo = $value['costo'];

			$impuestos = $this -> listar_impuestos($objeto);
			if ($impuestos['total'] > 0) {
				foreach ($impuestos['rows'] as $k => $v) {
					if ($v["clave"] == 'IEPS') {
						$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
					} else {
						if ($ieps != 0) {
							$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
						} else {
							$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
						}
					}

					// Precio actualizado
					$precio += $producto_impuesto;
					$precio = round($precio, 2);
				}
			}

			/* FIN Impuestos del producto
			 ============================================================================= */

			$items = "";

		// Opcionales
			if ($value['opcionales'] != "") {
				$sql = "SELECT
							CONCAT('Con: ',GROUP_CONCAT(nombre)) nombre
						FROM
							app_productos
						WHERE
							id IN(" . $value['opcionales'] . ")";
				$itemsProduct = $this -> query($sql);

				if ($row = $itemsProduct -> fetch_array())
					$items .= "(" . $row['nombre'] . ")";
			}

		// Adicionales
			if ($value['adicionales'] != "") {
				$items .="Extras:";

				$sql2 = "SELECT  nombre, id, precio FROM app_productos WHERE id in(" . $value['adicionales'] . ")";
				$itemsProduct2 = $this -> queryArray($sql2);

				foreach ($itemsProduct2['rows'] as $k => $v) {
				/* Impuestos del producto
				============================================================================= */
					$objeto['id'] = $v['id'];
					$producto_impuesto = 0;
					$impuestos = $this -> listar_impuestos($objeto);
					if ($impuestos['total'] > 0) {
						foreach ($impuestos['rows'] as $kk => $vv) {
							if ($vv["clave"] == 'IEPS') {
								$producto_impuesto = $ieps = (($vv["precio"]) * $vv["valor"] / 100);

							} else {
								if ($ieps != 0) {
									$producto_impuesto = ((($vv["precio"] + $ieps)) * $vv["valor"] / 100);
								} else {
									$producto_impuesto = (($vv["precio"]) * $vv["valor"] / 100);
								}
							}

						// Precio actualizado
							//$precio += $producto_impuesto + $vv["precio"];
							//$precio += $producto_impuesto;
							$precio = round($precio, 2);
							$sumaExtras += $producto_impuesto + $v["precio"]; // extras
						}
					}else{ /// extra sin impustos
						//$precio +=  $v["precio"];
						$precio = round($precio, 2);
						$sumaExtras  += $v["precio"];
					}

				/* FIN Impuestos del producto
				============================================================================= */

					$items .= "(" . $v['nombre'] . ")";
				}


			}

		// Sin
			if ($value['sin'] != "") {
				$sql = "SELECT
							CONCAT('Sin: ',GROUP_CONCAT(nombre)) nombre
						FROM
							app_productos
						WHERE
							id in(" . $value['sin'] . ")";
				$itemsProduct = $this -> query($sql);

				if ($row = $itemsProduct -> fetch_array())
					$items .= "(" . $row['nombre'] . ")";
			}

			$array['rows'][$contador] = Array('id' => $value['id'],
				'idproducto' => $value['idproducto'],
				'status' => $value['status'],
				'cantidad' => $value['cantidad'],
				'nombre' => $value['nombre'] . " $items",
				'precio' => $precio,
				'costo' => $costo,
				'complementos' => $value['complementos'],
				'id_promocion' => $value['id_promocion'],
				'recibir' => $value['recibir'],
				'comprar' => $value['comprar'],
				'codigo' => $value['codigo'],
				'dependencia_promocion' => $value['dependencia_promocion'],
				'sumaExtras' => $sumaExtras);
			$contador++;
		}

		return $array;
	}

///////////////// ******** ---- 		listar_impuestos		------ ************ //////////////////
//////// Consulta los impuestos de un producto y los devuelve en un array
	// Como parametros recibe:
	// id -> ID de la mesa

	function listar_impuestos($objeto) {
		$orden = ($objeto['formula'] == 2) ? ' ASC' : ' DESC';

		$sql = "SELECT
					p.id, p.precio, i.valor, i.clave, pi.formula, i.nombre
				FROM
					app_impuesto i, app_productos p
				LEFT JOIN
						app_producto_impuesto pi
					ON
						p.id = pi.id_producto
				WHERE
					p.id = " . $objeto['id'] . "
				AND
					i.id = pi.id_impuesto
				ORDER BY
					pi.id_impuesto " . $orden;
		// return $sql;
		$result = $this -> queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN listar_impuestos		------ ************ //////////////////

	//public function agregaProducto($idProducto,$cantidadInicial,$caracteristicas,$cliente,$series,$lotes,$tipo_desc,$monto_desc){
	public function agregaProducto($idProducto,$cantidadInicial,$caracteristicas,$cliente,$series,$lotes,$medico,$receta,$recetaRetenida,$kits,$tipo_desc,$monto_desc,$precioaux = 0,$idpedidoComanda = 0,$comandaR = 0,$gcNum = ''){

		//print_r("lala");
		if(!empty($idProducto)){
		$caras = '';
		$productosTotal = 0;
		//unset($_SESSION['caja']);

		//print_r($_SESSION['caja']);
		//exit();
		//print_r($_SESSION['pagos-caja']);
		//exit();
		//unset($_SESSION['caja']);
		$stringTaxes = '';
		//session_start();

		/*$sellista = "SELECT * from app_producto_sucursal";
		$res = $this->queryArray($sellista);
		if($res['total'] > 0){
				$select1 = "	SELECT
							IF(f.descripcion!='', CONCAT(p.nombre, f.descripcion), p.nombre) AS nombre,
							p.id, p.codigo, p.descripcion_larga, p.id_unidad_venta, p.precio,
							p.ruta_imagen, p.formulaIeps, f.descripcion, p.tipo_producto
						FROM
							app_productos p
						LEFT JOIN
								app_campos_foodware f
							ON
								p.id=f.id_producto
						left join app_producto_sucursal ps on ps.id_producto=p.id
						WHERE
							p.codigo = '".$idProducto."' and p.status=1 and and ps.id_sucursal='".$_SESSION['sucursal']."'";
		}else{*/
			$select1 = "	SELECT
							IF(f.descripcion!='', CONCAT(p.nombre, f.descripcion), p.nombre) AS nombre,
							p.id, p.codigo, p.descripcion_larga, p.id_unidad_venta, p.precio,
							p.ruta_imagen, p.formulaIeps, f.descripcion, p.tipo_producto, p.clave_sat, p.division_sat,p.grupo_sat,p.clase_sat
						FROM
							app_productos p
						LEFT JOIN
								app_campos_foodware f
							ON
								p.id=f.id_producto
						WHERE
							p.codigo = '".$idProducto."' and p.status=1";
		//}


		$resut1 = $this->queryArray($select1);

		$claveSAT = $resut1['rows'][0]['clave_sat'];
		/*if($resut1['rows'][0]['clave_sat']!=''){
			$selCla = "SELECT c_claveprodserv from c_claveprodserv where id=".$resut1['rows'][0]['clave_sat'];
			//echo $selCla;
			$resCLA = $this->queryArray($selCla);
			$claveSAT = $resCLA['rows'][0]['c_claveprodserv'];
		}else{


		} */
		////////Politica de puntos y calculo de puntos
		$selPolitic = "SELECT pt.*
					from app_producto_politica pp , app_politicas_tarjeta pt
					where pp.id_producto='".$resut1['rows'][0]['id']."' and pp.id_politica = pt.id;";
		$resp = $this->queryArray($selPolitic);
		if($resp['total']>0){
			$idPol = $resp['rows'][0]['id'];
			$tipoPol = $resp['rows'][0]['tipo'];
			$porcenPol = $resp['rows'][0]['porcentaje'];
			$dineroPol = $resp['rows'][0]['dinero'];
			$puntosPol = $resp['rows'][0]['puntos'];
			$nombrePol = $resp['rows'][0]['nombre'];
		}else{
			$idPol = 0;
			$tipoPol = 0;
			$porcenPol = 0;
			$dineroPol = 0;
			$puntosPol = 0;
			$nombrePol = 0;
		}




//para precio por sucursal
		$resListPreTmp = $this->listaPreciosDe($resut1['rows'][0]['id']);
		$resut1['rows'][0]['precio'] = ( count($resListPreTmp) == 0 ?$resut1['rows'][0]['precio'] : $resListPreTmp[0]['precio'] );
		$resut1['rows'][0]['precio'] = str_replace(',','',$resut1['rows'][0]['precio']);


		if($caracteristicas!=''){

			if($tipo_desc == '%' and $monto_desc > 0){
				$res = 9999;
			}else{
				$caracteristicas3 = str_replace('*',',', $caracteristicas);

				$res = $this->getExisCara($idProducto,$caracteristicas3);

				$res = $res['cantidadExis'];
			}


		}else{
			$res = $this->existenciaProducto($resut1['rows'][0]['id']);
		}


			////Valida si se pueden hacer salidas sin existecnia
			$permiteVq = "SELECT salidas_sin_existencia from app_configuracion limit 1";
			$permiteVresult = $this->queryArray($permiteVq);

		if($resut1['rows'][0]['tipo_producto']!=2 && $resut1['rows'][0]['tipo_producto']!=10){

			$exiSiNo = 0;
			if($cantidadInicial > ($res - $_SESSION['caja'][$resut1['rows'][0]['id']]->cantidad)){
				$exiSiNo = 1;
			}

			if($permiteVresult['rows'][0]['salidas_sin_existencia']==0 && $exiSiNo==1){
				if($resut1['rows'][0]['tipo_producto']==4 || $resut1['rows'][0]['tipo_producto']==5 || $resut1['rows'][0]['tipo_producto']==7){
					// salta el return ya que de la existencia ya que se da de alta al pagar en caso de comanda rapida en otros caso no afecta - 7(combos)
				}else{
					return array('estatus' =>1000,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal);
				}

			}

		}

			/*if($resut1['rows'][0]['tipo_producto']==6){

					$selComp = "SELECT * from com_kitsXproductos where id_kit=".$resut1['rows'][0]['id'];
					$resComp = $this->queryArray($selComp);

					foreach ($resComp['rows'] as $k => $v) {
						$resInsumo = $this->existenciaProducto($resut1['rows'][0]['id']);
						$exiSiNo = 0;
						if(($cantidadInicial * $resComp['rows'][0]['cantidad']) > $res){
							$exiSiNo = 1;
						}

						if($permiteVresult['rows'][0]['salidas_sin_existencia']==0 && $exiSiNo==1){
							return array('estatus' =>1000,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal);
						}

					}
			}  */

		if($resut1["total"] < 1){
			return array('estatus' =>false,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal);

		}

		$select2 = "SELECT nombre, codigo_sat from app_unidades_medida where id=".$resut1['rows'][0]['id_unidad_venta'];
		$result2 = $this->queryArray($select2);
		$unidadSAT = $result2['rows'][0]['codigo_sat'];


		if($caracteristicas!=''){
			$idProdCar = $resut1['rows'][0]['id'].'_'.$caracteristicas;
		}else{
			$idProdCar = $resut1['rows'][0]['id'];
		}






		if(isset($_SESSION['caja'][$idProdCar])){
			$_SESSION['caja'][$idProdCar]->gifCardNumber = $gcNum;
			$seriesIds = '';
			$seriesDisplay = '';
			if($series!=''){

				$cantSerie = 0;
				$series = explode(',',$series);
				foreach ($series as $key => $value) {
					$sre = explode('-',$value);
					//echo 'idSerie='.$sre[0].'<br>';
					//echo 'Almacen='.$sre[1];
					if($sre[0]!=''){
						$seriesIds .=$sre[0].',';
						$seriesDisplay .= $sre[2].',';
					}
					$cantSerie++;
				}
				if($seriesDisplay!=''){

					$_SESSION['caja'][$idProdCar]->nombre = $resut1['rows'][0]['nombre'] .'['.$seriesDisplay.']';
					$_SESSION['caja'][$idProdCar]->cantidad = $cantidadInicial;
					$_SESSION['caja'][$idProdCar]->series_ids = $seriesIds;
					$_SESSION['caja'][$idProdCar]->series_display = $seriesDisplay;
				}
			}
			else if($lotes!=''){
				$lotesEx = explode(',',$lotes);
				foreach ($lotesEx as $key => $value) {
					$valueEx = explode('-',$value);
					$idsLotes .= $valueEx[0].'-'.$valueEx[2].',';
				}
				$_SESSION['caja'][$idProdCar]->cantidad = $cantidadInicial;
				$_SESSION['caja'][$idProdCar]->importe = $_SESSION['caja'][$idProdCar]->cantidad * $_SESSION['caja'][$idProdCar]->precio;
			}
			else{
				$_SESSION['caja'][$idProdCar]->cantidad = $_SESSION['caja'][$idProdCar]->cantidad + $cantidadInicial;
				$_SESSION['caja'][$idProdCar]->importe = $_SESSION['caja'][$idProdCar]->cantidad * $_SESSION['caja'][$idProdCar]->precio;
			}

			///es la segunda ves que entra
			$_SESSION['caja'][$idProdCar]->promoooo = 1;

			/*if($caracteristicas!=''){
				$caracteristicas2 =  explode("/", $caracteristicas);;
				foreach ($caracteristicas2 as $key => $value) {
					$expv=explode('=>', $value);
					$ip=$expv[0];
					$ih=$expv[1];
					$my = "SELECT concat('( ',a.nombre,': ',b.nombre,' )') as dcar FROM app_caracteristicas_padre a
					LEFT JOIN app_caracteristicas_hija b on b.id=".$ih."
					WHERE a.id=".$ip.";";
					$producto = $this->queryArray($my);
					$caras.= $producto['rows'][0]['dcar'];
				}
				$_SESSION['caja'][$resut1['rows'][0]['id'].'_'.$caracteristicas]->nombre = $_SESSION['caja'][$resut1['rows'][0]['id']]->nombre.' '.$caras;
			} */

		}else{

			$cliente = (empty($cliente)) ? 9999999 : $cliente ;
			$selecLis = "SELECT id_lista_precios from comun_cliente where id=".$cliente;
			$resLis = $this->queryArray($selecLis);

			if($resLis['total'] > 0){

				$selPrLis = "SELECT l.id, l.nombre, l.porcentaje, l.descuento, lp.id_producto";
				$selPrLis.=" from app_lista_precio l";
				$selPrLis.=" left join app_lista_precio_prods lp on lp.id_lista=l.id";
				$selPrLis.=" where l.id =".$resLis['rows'][0]['id_lista_precios']." and lp.id_producto=".$resut1['rows'][0]['id'];
				$resPrLis = $this->queryArray($selPrLis);
				//print_r($resPrLis);
				$idListaPrecios = $resLis['rows'][0]['id_lista_precios'];
				$descuento = 0;
				$precioFinal = 0;
				$descuento = $resut1['rows'][0]['precio'] * $resPrLis['rows'][0]['porcentaje']/ 100;
				if($resPrLis['rows'][0]['descuento'] == 1){
					$precioFinal = (float) $resut1['rows'][0]['precio'] - (float) $descuento;
				}else{
					$precioFinal = (float) $resut1['rows'][0]['precio'] + (float) $descuento;
				}


			}else{
				$desc = '';
				if($tipo_desc == '%' and $monto_desc > 0){
					$precioFinal = $resut1['rows'][0]['precio'];
					//$precioFinal = $precioFinal - ($precioFinal*($monto_desc/100));
					$desc = 'Desc de '.$monto_desc.'%';
				}else{
					$precioFinal = $resut1['rows'][0]['precio'];  /// aqui se aplicaria el desc
					$idListaPrecios = 0;
				}


			}

			////Promociones
		   /* date_default_timezone_set("Mexico/General");
			$hora = date("H:i");
			$hoy = getdate();
			$promQuery = 'SELECT pr.* from com_promociones pr, com_promocionesXproductos prp where pr.status=1 and prp.status=1 and pr.id=prp.id_promocion and prp.id_producto='.$resut1['rows'][0]['id'];
			$reProm = $this->queryArray($promQuery);

			$inicio = $reProm['rows'][0]['inicio'];
			$fin = $reProm['rows'][0]['fin'];

			if(preg_match('/['.$hoy['wday'].']+/', $reProm['rows'][0]['dias'])){
				if(($hora > $inicio)&&($hora < $fin)){
					if($reProm['rows'][0]['tipo']==1){
						///Descuento


					}else{
						///2x1
					}
				}else{
					echo 'esta fuera de promo';
				}
			}  */
			////Cortesias
			date_default_timezone_set("Mexico/General");
			$fechaactual = date("Y-m-d H:i:s");
			$selCor = "SELECT cp.id_producto,c.nombre,c.fecha_inicio,c.fecha_fin
						from app_cortesia c, app_cortesia_producto cp
						where c.estatus= 1 and cp.id_producto ='".$resut1['rows'][0]['id']."' and c.id=cp.id_cortesia and '".$fechaactual."' between c.fecha_inicio and c.fecha_fin;";
			$resCor = $this->queryArray($selCor);

			if($resCor['total'] > 0){
				$precioFinal = 0.00;
				$resut1['rows'][0]['nombre'] = $resut1['rows'][0]['nombre'].'['.$resCor['rows'][0]['nombre'].']';
				$resut1['rows'][0]['descripcion_larga'] = $resut1['rows'][0]['descripcion_larga'].'['.$resCor['rows'][0]['nombre'].']';
			}


			session_start();

			$arraySession = new stdClass();


			if($caracteristicas!=''){
				$idProducto = $resut1['rows'][0]['id'].'_'.$caracteristicas;
			}else{
				$idProducto = $resut1['rows'][0]['id'];
			}

			///////Series
			$seriesIds = '';
			$seriesDisplay = '';
			if($series!=''){

				$series = explode(',',$series);
				foreach ($series as $key => $value) {
					$sre = explode('-',$value);
					//echo 'idSerie='.$sre[0].'<br>';
					//echo 'Almacen='.$sre[1];
					if($sre[0]!=''){
						$seriesIds .=$sre[0].',';
						$seriesDisplay .= $sre[2].',';
					}
				}
				if($seriesDisplay!=''){
					$resut1['rows'][0]['nombre'] = $resut1['rows'][0]['nombre'].'['.$seriesDisplay.']';
				}
			}
			///////////fin series
			////////Lotes
			$idsLotes = '';
			if($lotes!=''){
				$lotesEx = explode(',',$lotes);
				foreach ($lotesEx as $key => $value) {
					$valueEx = explode('-',$value);
					$idsLotes .= $valueEx[0].'-'.$valueEx[2].',';
				}

			}

			/////////////////PRECIO MODIFICADO EN PEDIDO DE COMANDERA///////////////////////////////////////////
			if($precioaux > 0){
				$precioFinal = $precioaux;
			}else{
				$precioFinal = $precioFinal;
			}
			/////////////////PRECIO MODIFICADO EN PEDIDO DE COMANDERA FIN //////////////////////////////////////


			/////////////////OBTENER ID DE PEDIDO CUAANDO PERTENECE A COMANDA///////////////////////////////////
			// agrega producot a la comanda de foodware
	        $idpedidoComanda2 = 0;
	        if($comandaR != '' and $f == 0 and $comandaR != 0){
	            $idpedidoComanda2 = $this->agregaProductoComanda($resut1['rows'][0]['id'],$comandaR);
	        }
			/////////////////OBTENER ID DE PEDIDO CUAANDO PERTENECE A COMANDA///////////////////////////////////



			$arraySession->idProducto = $resut1['rows'][0]['id'];
			$arraySession->codigo = $resut1['rows'][0]['codigo'];
			$arraySession->nombre = $resut1['rows'][0]['nombre'];
			$arraySession->descripcion = $resut1['rows'][0]['descripcion_larga'];
			$arraySession->unidad = $result2['rows'][0]['nombre'];
			$arraySession->idunidad = $resut1['rows'][0]['id_unidad_venta'];
			$arraySession->unidadSAT = $unidadSAT;
			//$arraySession->precio = $resut1['rows'][0]['precio'];
			$arraySession->precio = $precioFinal;
			$arraySession->cantidad = $cantidadInicial;
			$arraySession->ruta_imagen = $resut1['rows'][0]['ruta_imagen'];
			$arraySession->importe = $precioFinal * $cantidadInicial;
			$arraySession->impuesto = '';
			$arraySession->suma_impuestos = '';
			$arraySession->cargos = '';
			$arraySession->cargos33 = '';
			$arraySession->cargosLocales = '';
			$arraySession->formula = $resut1['rows'][0]['formulaIeps'];
			$arraySession->caracteristicas = $caracteristicas;
			$arraySession->tipoProducto = $resut1['rows'][0]['tipo_producto'];
			$arraySession->perVsinInventario = $permiteVresult['rows'][0]['salidas_sin_existencia'];
			$arraySession->inventario = $res;
			$arraySession->series_display = $seriesDisplay;
			$arraySession->series_ids = $seriesIds;
			$arraySession->lotes = $idsLotes;
			$arraySession->medico = $medico;
			$arraySession->receta = $receta;
			$arraySession->recetaRetenida = $recetaRetenida;
			$arraySession->kits = $kits;
			$arraySession->id_promocion = 0;
			$arraySession->comentario = '';
			$arraySession->idPolitica = $idPol;
			$arraySession->nombrePolitica = $nombrePol;
			$arraySession->tipoPolitica = $tipoPol;
			$arraySession->puntosPolitica = $puntosPol;
			$arraySession->dineroPolitica = $dineroPol;
			$arraySession->porcentajePolitica = $porcenPol;
			$arraySession->puntosVenta = 0;
			$arraySession->claveSat = $claveSAT;

			$arraySession->idpedidoComanda = $idpedidoComanda;
			$arraySession->idpedidoComanda2 = $idpedidoComanda2;
			$arraySession->gifCardNumber = $gcNum;


			$_SESSION['caja'][$idProducto] = $arraySession;
			$_SESSION['caja']['descGeneral'] = 0;
			///Caracteristicas
			if($caracteristicas!=''){
				$caracteristicas2 =  explode("*", $caracteristicas);
				foreach ($caracteristicas2 as $key => $value) {
					$expv=explode('=>', $value);
					$ip=$expv[0];
					$ih=$expv[1];
					$my = "SELECT concat('( ',a.nombre,': ',b.nombre,' )') as dcar FROM app_caracteristicas_padre a
					LEFT JOIN app_caracteristicas_hija b on b.id=".$ih."
					WHERE a.id=".$ip.";";
					$producto = $this->queryArray($my);
					$caras.= $producto['rows'][0]['dcar'];
				}
				$_SESSION['caja'][$resut1['rows'][0]['id'].'_'.$caracteristicas]->nombre = $_SESSION['caja'][$resut1['rows'][0]['id'].'_'.$caracteristicas]->nombre.' '.$caras;
			}

		}



		//$_SESSION['caja']['cargos']['subtotal'] = $subtotal;
		//$_SESSION['caja']['cargos']['total'] = $subtotal;

		$sessionArray = $this->object_to_array($_SESSION['caja']);


		foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				$stringTaxes .=$value['idProducto'].'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'-'.$value['caracteristicas'].'/';
				$productosTotal += $value['cantidad'];
			}
		}

		$this->calculaImpuestos($stringTaxes);

			////Promociones, Verifica si tiene foodware, si tiene fodware no entra.

			$selCom = "SELECT idmenu from accelog_perfiles_me where idmenu=2156";
			$resCom = $this->queryArray($selCom);

			if($resCom['total'] == 0){

				date_default_timezone_set("Mexico/General");
				$hora = date("H:i");
				$hoy = getdate();
				$promQuery = 'SELECT pr.* from com_promociones pr, com_promocionesXproductos prp where pr.status=1 and prp.status=1 and pr.id=prp.id_promocion and prp.id_producto='.$resut1['rows'][0]['id'];
				$reProm = $this->queryArray($promQuery);

				$inicio = $reProm['rows'][0]['inicio'];
				$fin = $reProm['rows'][0]['fin'];

				if(preg_match('/['.$hoy['wday'].']+/', $reProm['rows'][0]['dias'])){
					if(($hora > $inicio)&&($hora < $fin)){
						if($reProm['rows'][0]['tipo']==1){
							///Descuento
							if(isset($_SESSION['caja'][$idProdCar]->promoooo)){
								//echo '333';
								$xer = $this->cambiaCantidad($resut1['rows'][0]['id'],$reProm['rows'][0]['descuento'], '%','',1);
							}else{
								//echo '44';
								$_SESSION['caja'][$idProdCar]->precio_sindes = $_SESSION['caja'][$idProdCar]->precio;
								$_SESSION['caja'][$idProdCar]->importe_sindes = $cantidad * $_SESSION['caja'][$idProdCar]->precio;
								$xer = $this->cambiaCantidad($resut1['rows'][0]['id'],$reProm['rows'][0]['descuento'], '%');
							}


						}else{
							///2x1
							//echo 'Promociones='.floor($_SESSION['caja'][$idProdCar]->cantidad / $reProm['rows'][0]['cantidad']);
							//echo '<br>Producto sobrante='.($_SESSION['caja'][$idProdCar]->cantidad % $reProm['rows'][0]['cantidad']);
							$promociones = floor($_SESSION['caja'][$idProdCar]->cantidad / $reProm['rows'][0]['cantidad']);
							$sobrantes = ($_SESSION['caja'][$idProdCar]->cantidad % $reProm['rows'][0]['cantidad']);
							$xer2 = $this->promocionNxN($promociones,$sobrantes,$idProdCar);
						}
					}else{
						//echo 'esta fuera de promo';
					}
				}
			}


		////regresa los productos en orden de incersion
        $ar = $_SESSION['caja'];
        $nar=array();
        foreach ($ar as $key => $value) {
            $nar[$key.'+']=$ar[$key];
        }
        $nar = array_reverse($nar);
    	}
		return array('estatus' =>true,'productos' =>$nar, 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal, 'listaDePrecios' => $idListaPrecios);



	}

	public function agregaProductoPromo($objeto){
		//print_r($objeto);
		$arraySession->idProducto = 0;
		$arraySession->codigo = '';
		$arraySession->nombre = $objeto['nombre'];
		$arraySession->descripcion = '';
		$arraySession->unidad = '';
		$arraySession->idunidad = '';
		$arraySession->products = $objeto['products'];
		//$arraySession->precio = $resut1['rows'][0]['precio'];
		$arraySession->precio = $objeto['precio'];
		$arraySession->cantidad = 1;
		$arraySession->ruta_imagen = '';
		$arraySession->importe = $objeto['precio'];
		$arraySession->impuesto = '';
		$arraySession->suma_impuestos = '';
		$arraySession->cargos = '';
		$arraySession->formula = '';
		$arraySession->caracteristicas = '';
		$arraySession->tipoProducto = '';
		$arraySession->perVsinInventario = '';
		$arraySession->inventario = '';
		$arraySession->series_display = '';
		$arraySession->series_ids = '';
		$arraySession->lotes = '';
		$arraySession->tipin = $objeto['tipin'];
		$arraySession->id_promocion = $objeto['id_promocion'];

		$_SESSION['caja']['cargos']['subtotal'] += $objeto['precio'];
		$_SESSION['caja']['cargos']['total'] += $objeto['precio'];
		$_SESSION['caja']['prom'.$objeto['tipin']] = $arraySession;
		$ar = $_SESSION['caja'];
        $nar=array();
        foreach ($ar as $key => $value) {
            $nar[$key.'+']=$ar[$key];
        }
        $nar = array_reverse($nar);

		return array('estatus' =>true,'productos' =>$nar, 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal, 'listaDePrecios' => $idListaPrecios);
	}


	public function promocionNxN($promociones,$sobrantes,$producto){
		$selectPrec = 'SELECT * from app_productos where id='.$producto;
		$resPre = $this->queryArray($selectPrec);

		$descuento = $resPre['rows'][0]['precio'] * $promociones;

		//if($descuento > 0){
			$_SESSION['caja'][$producto]->precio = $resPre['rows'][0]['precio'];
			$_SESSION['caja'][$producto]->subtotal = $_SESSION['caja'][$producto]->cantidad  * $resPre['rows'][0]['precio'];
			$_SESSION['caja'][$producto]->descuento = 0.0;
			$_SESSION['caja'][$producto]->tipodescuento = '$';
			$_SESSION['caja'][$producto]->descuento_cantidad = 0.0;
			$_SESSION['caja'][$producto]->nombre = $resPre['rows'][0]['nombre'];
			$_SESSION['caja'][$producto]->importe = $_SESSION['caja'][$producto]->cantidad  * $resPre['rows'][0]['precio'];

			$xer = $this->cambiaCantidad($producto,$descuento, '$');
		//}


	}


	public function object_to_array($data) {
		if (is_array($data) || is_object($data)) {
			$result = array();
			foreach ($data as $key => $value) {
				$result[$key] = $this->object_to_array($value);
			}
			return $result;
		}
	return $data;
	}
	public function cargaRfcs($idCliente,$numTarjeta=0) {
		$queryRfc = "select id , rfc from comun_facturacion where nombre=" . $idCliente." and cliPro=1";
		$result = $this->queryArray($queryRfc);

		$sel = "SELECT id_moneda from comun_cliente where id=".$idCliente;
		$res = $this->queryArray($sel);

		if($numTarjeta==0){
			$sel2 ="SELECT * from tarjeta_regalo where cliente='".$idCliente."'". " and usada <> 1;";
			$res2 = $this->queryArray($sel2);
		}else{// tarjeta espesifica
			$sel2 ="SELECT * from tarjeta_regalo where cliente='".$idCliente."'". " and usada <> 1 and numero = '".$numTarjeta."';";
			$res2 = $this->queryArray($sel2);
		}
		


		if ($result["total"] > 0) {
			return array("status" => true, "rfc" => $result["rows"], "moneda" => $res['rows'][0]['id_moneda'], "tarjeta" =>$res2['rows'] );
		} else {
			return array("status" => false,  "tarjeta" =>$res2['rows'] );
		}
	}
	public function calculaImpuestos($stringTaxes){
		//echo '['.$stringTaxes.']';
		unset($_SESSION['caja']['cargos']);
		unset($_SESSION['caja']['cargosLocales']);
		//echo $precio;
		//echo $stringTaxes.'Z';
		//exit();
		//unset($_SESSION['prueba']);
		$productos = explode('/', $stringTaxes);

		foreach ($productos as $key => $value) {
			$producto_impuesto = 0;
			$prod = explode('-', $value);
			if($prod[0]!=''){
				$idProducto = $prod[0];
				$precio = $prod[1];
				$precio = bcdiv($prod[1],'1',10);
				//$precio = str_replace(',', '', number_format($prod[1],2));
				//$precio = round($prod[1],2);
				$cantidad = $prod[2];
				$formula = $prod[3];//desc o asc 1 = ieps de los vinos , 2 = ieps de la gasolina
				$carac = $prod[4];
				$subtotal = $precio * $cantidad;
				$producto_impuesto2 = 0;
				$producto_impuestoR = 0;
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
				$tmpId = ( explode('_', $idProducto) );
				$tmpId = $tmpId[0];
				$queryImpuestos = "select p.id,p.precio, i.valor, i.clave,pi.formula,i.nombre";
				$queryImpuestos .= " from app_impuesto i, app_productos p ";
				$queryImpuestos .= " left join app_producto_impuesto pi on p.id=pi.id_producto ";
				$queryImpuestos .= " where p.id=" . $tmpId . " and i.id=pi.id_impuesto ";
				$queryImpuestos .= " Order by pi.id_impuesto ".$ordenform;
			    	//echo $queryImpuestos.'<br>';
				$resImpues = $this->queryArray($queryImpuestos);
				///echo '>>>|'.count($resImpues['rows']).'|';exit();
				//si tiene caracteristicas
				if($carac!=''){
					$idProducto = $idProducto.'_'.$carac;
				}else{
					$idProducto = $idProducto;
				}
				/*if(count($resImpues['rows']) == 0){
					$cla = 'IVA';
					$nom = 'IVA 0%';
					$valori = 0.00;
					$_SESSION['caja'][$idProducto]->impuesto =  bcdiv(0,'1',10);
					$_SESSION['caja'][$idProducto]->suma_impuestos += bcdiv(0,'1',10);
					$_SESSION['caja'][$idProducto]->cargos->$nom = bcdiv(0,'1',10);
					$_SESSION['caja'][$idProducto]->cargos33->$cla=array('tasa' => 0.00 ,'importe' => bcdiv(0,'1',10), 'nombre' => $nom, 'formula' => 1);
					//echo $valueImpuestos["nombre"].' '.$valueImpuestos["valor"].'='.$producto_impuesto.'<br>';
					//$total += $producto_impuesto;
					$_SESSION['caja']['cargos']['impuestos'][$cla] = $_SESSION['caja']['cargos']['impuestos'][$cla] + bcdiv(0,'1',10);
					$_SESSION['caja']['cargos']['impuestosPorcentajes'][$nom] = $_SESSION['caja']['cargos']['impuestosPorcentajes'][$nom] + bcdiv(0,'1',10);

					$_SESSION['caja']['cargos']['impuestosFactura'][$cla][$valueImpuestos["valor"]] = $_SESSION['caja']['cargos']['impuestosFactura'][$cla][$valori] + bcdiv(0,'1',10);
					$_SESSION['caja']['cargos']['impuestosPdf'][$cla][$valori]['Valor'] = $_SESSION['caja']['cargos']['impuestosPdf'][$cla][$valori]['Valor'] + bcdiv(0,'1',10);
				}*/
				//echo $idProducto.'<br>';
				foreach ($resImpues['rows'] as $key => $valueImpuestos) {
						//echo 'Clave='.$valueImpuestos["clave"].'<br>';
						if ($valueImpuestos["clave"] == 'IEPS') {
							//echo 'Y'.$producto_impuesto;
							$ieps = (($subtotal) * $valueImpuestos["valor"] / 100);
							$producto_impuesto  = (($subtotal) * $valueImpuestos["valor"] / 100);
							$producto_impuesto2 += (($subtotal) * $valueImpuestos["valor"] / 100);
						} elseif($valueImpuestos["clave"]=='IVAR' || $valueImpuestos["clave"]=='ISR'){
							if($valueImpuestos["clave"]=='IVAR'){
								/*$perPaso = (($subtotal) * 16 / 100);
								$sdoPaso = (($perPaso * 2) / 3);
								//// ********Nueva formula IVAR ******
								$producto_impuesto = $sdoPaso;
								//$producto_impuestoR = (($subtotal) * $valueImpuestos["valor"] / 100);
								$producto_impuestoR += $sdoPaso; */
								//$producto_impuesto = (($subtotal) * 10.6667 / 100);
								$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
								//$producto_impuestoR += (($subtotal) * 10.6667 / 100);
								$producto_impuestoR += (($subtotal) * $valueImpuestos["valor"] / 100);

							}else{
							 	$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
								//$producto_impuestoR = (($subtotal) * $valueImpuestos["valor"] / 100);
								$producto_impuestoR += (($subtotal) * $valueImpuestos["valor"] / 100);
								//$producto_impuesto2 += (($subtotal) * $valueImpuestos["valor"] / 100);
							}
						}else {

							if ($ieps != 0) {
								//echo 'tiene iepswowkowkdokwdkowdkwkdowkdowdowdowkokwdodokwokdokwooo';
								$producto_impuesto = ((($subtotal + $ieps)) * $valueImpuestos["valor"] / 100);
								$producto_impuesto2 += ((($subtotal + $ieps)) * $valueImpuestos["valor"] / 100);
								 /*if($valueImpuestos["retenido"]==1){
									$nombreret=$valueImpuestos["nombre"];
									$producto_impuesto_ret =  (($subtotal) * $valueImpuestos["retenido"] / 100);//sacco el retenido

								}   */
							} else {

								//echo 'nohayieps';
								//$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
							   // echo 'Y'.$producto_impuesto;
								//exit();
							  /*  if($valueImpuestos["retenido"]==1){
									$nombreret=$valueImpuestos["nombre"];

									$producto_impuesto_ret =  (($subtotal) * $valueImpuestos["valor"] / 100);//sacco el retenido
								}else{ */
									$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
									$producto_impuesto2 += (($subtotal) * $valueImpuestos["valor"] / 100);
								//}
							}
						}

						$_SESSION['caja'][$idProducto]->impuesto =  bcdiv($producto_impuesto,'1',10);
						$_SESSION['caja'][$idProducto]->suma_impuestos += bcdiv($suma_impuestos,'1',10);
						$_SESSION['caja'][$idProducto]->cargos->$valueImpuestos["nombre"] = bcdiv($producto_impuesto,'1',10);
						$_SESSION['caja'][$idProducto]->cargos33->$valueImpuestos["clave"]=array('tasa' =>$valueImpuestos["valor"] ,'importe' => bcdiv($producto_impuesto,'1',10), 'nombre' => $valueImpuestos["nombre"], 'formula' => $formula);
						//echo $valueImpuestos["nombre"].' '.$valueImpuestos["valor"].'='.$producto_impuesto.'<br>';
						//$total += $producto_impuesto;
						$_SESSION['caja']['cargos']['impuestos'][$valueImpuestos["clave"]] = $_SESSION['caja']['cargos']['impuestos'][$valueImpuestos["clave"]] + bcdiv($producto_impuesto,'1',10);
						$_SESSION['caja']['cargos']['impuestosPorcentajes'][$valueImpuestos["nombre"]] = $_SESSION['caja']['cargos']['impuestosPorcentajes'][$valueImpuestos["nombre"]] + bcdiv($producto_impuesto,'1',10);

						$_SESSION['caja']['cargos']['impuestosFactura'][$valueImpuestos["clave"]][$valueImpuestos["valor"]] = $_SESSION['caja']['cargos']['impuestosFactura'][$valueImpuestos["clave"]][$valueImpuestos["valor"]] + bcdiv($producto_impuesto,'1',10);
						$_SESSION['caja']['cargos']['impuestosPdf'][$valueImpuestos["clave"]][$valueImpuestos["valor"]]['Valor'] = $_SESSION['caja']['cargos']['impuestosPdf'][$valueImpuestos["clave"]][$valueImpuestos["valor"]]['Valor'] + bcdiv($producto_impuesto,'1',10);

				}

				$ieps=0;
				//echo $producto_impuestoR.'<br>'.($subtotal+$producto_impuesto2);

				//echo 'total='.($subtotal+$producto_impuesto).'<br>';
				//$_SESSION['caja']['cargos']['subtotal'] += bcdiv($subtotal,'1',10);
				//$_SESSION['caja']['cargos']['total'] += (bcdiv($subtotal,'1',10)+bcdiv($producto_impuesto2,'1',10)) - bcdiv($producto_impuestoR,'1',10);
				$_SESSION['caja']['cargos']['subtotal'] += round($subtotal,2);
				$_SESSION['caja']['cargos']['total'] += (round($subtotal,2)+round($producto_impuesto2,2)) - round($producto_impuestoR,2);


				$_SESSION['caja']['cargos']['total'] = round($_SESSION['caja']['cargos']['total'],2);
				if($_SESSION['caja'][$idProducto]->idPolitica !=0 ){
					if($_SESSION['caja'][$idProducto]->tipoPolitica == 1){
						$t = $_SESSION['caja'][$idProducto]->impuesto + $_SESSION['caja'][$idProducto]->importe;
						$puntos = ($t / 100) * $_SESSION['caja'][$idProducto]->porcentajePolitica;
						$_SESSION['caja'][$idProducto]->puntosVenta = $puntos;
					}else{
						//echo 'idejijeijed';
						$t = $_SESSION['caja'][$idProducto]->impuesto + $_SESSION['caja'][$idProducto]->importe;
						//echo $t.'?'.$_SESSION['caja'][$idProducto]->dineroPolitica;
						$veces = ($t / $_SESSION['caja'][$idProducto]->dineroPolitica);
						//echo '%%%%'.$veces;
						$puntos = floor($veces) * $_SESSION['caja'][$idProducto]->puntosPolitica;
						//echo '*'.$puntos.'*';
						$_SESSION['caja'][$idProducto]->puntosVenta = $puntos;
					}
				}

			}
		}
		//print_r($_SESSION['caja']);
		//echo '////////////////////////////////////////////////////////////////////';
		$this->calculaPuntos($stringTaxes);
		$this->calculaImpuestoLocal();
		//print_r($_SESSION['caja']);
		//echo json_encode($_SESSION['prueba']);
		//unset($_SESSION['prueba');
	}

	public function calculaPuntos($stringTaxes){
		$productos = explode('/', $stringTaxes);
		//print_r($productos)
		foreach ($productos as $key => $value) {
			$producto_impuesto = 0;
			$prod = explode('-', $value);
			if($prod[0]!=''){
				$idProducto = $prod[0];
				$precio = $prod[1];
				$cantidad = $prod[2];
				$formula = $prod[3];//desc o asc 1 = ieps de los vinos , 2 = ieps de la gasolina
				$carac = $prod[4];
				$subtotal = $precio * $cantidad;
				$producto_impuesto2 = 0;
				$producto_impuestoR = 0;
				//echo 'Subtotal='.$subtotal;

				//si tiene caracteristicas
				if($carac!=''){
					$idProducto = $idProducto.'_'.$carac;
				}else{
					$idProducto = $idProducto;
				}
				//echo '('.$_SESSION['caja'][$idProducto]->idPolitica.')';
				//echo $idProducto.'<br>';
				//echo $producto_impuestoR.'<br>'.($subtotal+$producto_impuesto2);

				//echo 'total='.($subtotal+$producto_impuesto).'<br>';
				/*$_SESSION['caja']['cargos']['subtotal'] += $subtotal;
				$_SESSION['caja']['cargos']['total'] += ($subtotal+$producto_impuesto2) - $producto_impuestoR;*/
				if($_SESSION['caja'][$idProducto]->idPolitica !=0 ){
					if($_SESSION['caja'][$idProducto]->idPolitica == 1){
						if($_SESSION['caja'][$idProducto]->impuesto == ''){
							$imp = 0;
						}else{
							$imp = $_SESSION['caja'][$idProducto]->impuesto;
						}
						$t = $_SESSION['caja'][$idProducto]->importe + $imp;
						$puntos = ($t / 100) * $_SESSION['caja'][$idProducto]->porcentajePolitica;
						$_SESSION['caja'][$idProducto]->puntosVenta = $puntos;
					}
				}

			}
		}
	}


	public function checarPagos() {

		$verificaInicio = $this->verificainicioCaja();
		if ($verificaInicio == 'false') {
			return array("statusInicio" => false, "inicio" => $this->iniciocaja());
		}

		if (isset($_SESSION['pagos-caja']["pagos"])) {

			return array("status" => true, "pagos" => $_SESSION['pagos-caja']["pagos"], "abonado" => $_SESSION['pagos-caja']["Abonado"], "porPagar" => $_SESSION['pagos-caja']["porPagar"], "cambio" => $_SESSION['pagos-caja']["cambio"]);
		} else {
			return array("status" => false);
		}
	}
	public function agregaPago($tipo, $tipostr, $cantidad, $referencia, $tarjeta) {
			//echo $tipo.'-'.$tipostr;
			$_SESSION['pagos-caja']["pagos"][$tipo]['tipostr'] = $tipostr;
			$str_cantidad = str_replace(",", "", $_SESSION['pagos-caja']["pagos"][$tipo]['cantidad']);
			$_SESSION['pagos-caja']["pagos"][$tipo]['cantidad'] = $str_cantidad + $cantidad;
			$_SESSION['pagos-caja']["pagos"][$tipo]['referencia'] = $referencia;
			$_SESSION['pagos-caja']["pagos"][$tipo]['tipo'] = $tipo;
			$_SESSION['pagos-caja']["pagos"][$tipo]['tarjeta'] = $tarjeta;
			//print_r($_SESSION['pagos-caja']);

			$abonado = 0;
			foreach ($_SESSION['pagos-caja']["pagos"] as $key => $value) {
				$str_cantidad2 = str_replace(",", "", $value["cantidad"]);
				$abonado += $str_cantidad2;
			}

				//Abonado es el dinero que entrega el cliente no importa como pague
			$abonado = str_replace(",", "", $abonado);
			$_SESSION['pagos-caja']["Abonado"] = $abonado;


				//Aun por pagar en los casos que pagan con diferentes metodos.
			$str_total = str_replace(",", "", $_SESSION["caja"]["cargos"]["total"]);
			$porPagar = $str_total - $_SESSION['pagos-caja']["Abonado"];
			if ($_SESSION['pagos-caja']["Abonado"] >= $str_total) {
				$_SESSION['pagos-caja']["porPagar"] = number_format(0, 2);
			} else {
				$_SESSION['pagos-caja']["porPagar"] = number_format($porPagar,2);
			}

				//Cambio en caso de que sea necesario
			$cambio = $_SESSION['pagos-caja']["Abonado"] - $str_total;
			if ($cambio > 0) {
				$_SESSION['pagos-caja']["cambio"] = number_format($cambio,2);
			} else {
				$_SESSION['pagos-caja']["cambio"] = number_format(0, 2);
			}

			$_SESSION['pagos-caja']["Abonado"] = number_format($abonado,2);
			//print_r($_SESSION['pagos-caja']);
			//exit();
			return array("status" => true, "tipo" => $tipo, "tipostr" => $tipostr, "cantidad" => $_SESSION['pagos-caja']["pagos"][$tipo]['cantidad'], "abonado" => $_SESSION['pagos-caja']["Abonado"], "porPagar" => $_SESSION['pagos-caja']["porPagar"], "cambio" => $_SESSION['pagos-caja']["cambio"]);
	}

	public function guardaVentaPronti($idProducto,$monto,$referencia){
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");

		$insertVenta = "INSERT INTO app_pos_venta (idCliente,monto,estatus,idEmpleado,rfc,documento,fecha,cambio,montoimpuestos,idSucursal,envio,impuestos,subtotal,moneda,tipo_cambio,descuentoGeneral) "
			. "VALUES ('0'," . $monto . ",1," . $_SESSION['accelog_idempleado'] . ",'','1','" . $fechaactual . "','0','0'," . $_SESSION["sucursal"] . ",'0','','".$monto."','1','1','0');";
			//echo $insertVenta;
			$result = $this->queryArray($insertVenta);
			$idVenta = $result['insertId'];

		$insert2 = "INSERT into app_pos_venta_producto(idProducto,cantidad,preciounitario,subtotal,idVenta,total,moneda) values('".$idProducto."', '1','".$monto."',,'".$monto."','".$idVenta."','".$monto."','1')";
		$result2 = $this->queryArray($insert2);




	}




	public function guardarVenta($cliente, $idFact, $documento, $suspendida, $propinas, $comentario,$moneda, $vendedor,$tipoCambio=1,$id_comanda=0,$usarPuntos,$totalPuntosInput=0,$tr){
		//echo $cliente . "::" . $idFact . "::" . $documento . "::" . $suspendida . "::" . $propina . "::" . $comentario . "::". $moneda;
		//print_r($_SESSION['caja']);
		//print_r($_SESSION['pagos-caja']["pagos"]);
		//echo $_SESSION['caja']['cargos']['descGeneral'].'SSSSS';
	   // exit();
	   //print_r($_SESSION);exit();

		try {
			//			$documento=1;
			$folioRec = 0;
		   if ($suspendida != 0) {
				$this->eliminarSuspendida($suspendida);
			}

			date_default_timezone_set("Mexico/General");
			$fechaactual = date("Y-m-d H:i:s");

			$_SESSION["caja"] = $this->object_to_array($_SESSION["caja"]);
			$_SESSION['pagos-caja'] = $this->object_to_array($_SESSION['pagos-caja']);

			$monto = str_replace(",", "", $_SESSION["caja"]["cargos"]["total"]);
			$cambio = str_replace(",", "", $_SESSION['pagos-caja']["cambio"]);

			//SE calcula el total de los impuestos
			foreach ($_SESSION["caja"]["cargos"]["impuestos"] as $key => $value) {
				$impuestos+= (float) str_replace(",", "", $value);
			}

			if (!is_numeric($cliente)) {
				$cliente = 'NULL';
			}
			//echo $impuestos.'SSSS';

			if ($_SESSION['pagos-caja']["porPagar"] != 0) {
				throw new Exception("No has cubierto el total de la compra.");
			}

			//seleccionas el ultimo id para sacar el nuevo de la venta
			$selectid = "SELECT max(idVenta) as idVenta from app_pos_venta for Update;";
			$result = $this->queryArray($selectid);

			if ($result["rows"] < 1) {
				throw new Exception($result["msg"]);
			}

			$idVenta = $result["rows"][0]["idVenta"] + 1;
			//echo '('.$idVenta.')';
			$envioFac = 0;
			if($documento == 2 || $documento == 5){
				$envioFac = 1;
			}
			$idALmacenUs = $this->obtenAlm();
			//Se inserta la venta
			////obtiene tipo de cambio
			$query34 = 'SELECT * FROM cont_tipo_cambio where moneda='.$moneda.' order by fecha desc limit 1;';
			$res34 = $this->queryArray($query34);

			if ($documento == 4) {
				////obtiene folio Recibo de pago
				$queryrp = 'SELECT (folio_recibo + 1) recibo FROM app_pos_venta order by folio_recibo desc limit 1;';
				$resrp = $this->queryArray($queryrp);

				$folioRec = $resrp['rows']['0']['recibo'];
			}
			$sesCajaSubtotal = $_SESSION['caja']['cargos']['subtotal'];
			if(($tipoCambio*1)>1){
				$tipoCambio = $tipoCambio * 1;
				$monto = $tipoCambio * $monto;
				$impuestos = $tipoCambio * $impuestos;
				$sesCajaSubtotal = $_SESSION['caja']['cargos']['subtotal'] * $tipoCambio;

			}

			$insertVenta = "INSERT INTO app_pos_venta (idCliente,monto,estatus,idEmpleado,rfc,documento,fecha,cambio,montoimpuestos,idSucursal,envio,impuestos,subtotal,moneda,tipo_cambio,descuentoGeneral, folio_recibo, puntosTarjataRegalo) "
			. "VALUES (" . $cliente . "," . $monto . ",1," . $_SESSION['accelog_idempleado'] . ",''," . $documento . ",'" . $fechaactual . "'," . $cambio . ",'" . $impuestos . "'," . $_SESSION["sucursal"] . ",'".$envioFac."','".json_encode($_SESSION['caja']['cargos']['impuestosPorcentajes'])."','".$sesCajaSubtotal."','".$moneda."','".$tipoCambio."','".$_SESSION['caja']['descGeneral']."',".$folioRec.",'".$totalPuntosInput."');";
			$result = $this->queryArray($insertVenta);
			//print_r($result);
			$idVenta = $result['insertId'];
			if ($result["total"] < 0) {
				throw new Exception("Error al registrar la venta 1. " . $result["msg"]);
			}
			//print_r($_SESSION["caja"]);
			//inserta los prodcutos de la venta

			foreach ($_SESSION["caja"] as $key => $producto) {

			if($key!='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden' && $producto['id_promocion'] == 0){
				$impuestos = 0;
				$producto = (object) $producto;

				$sql = "SELECT	cp.*, c.costo, c.id_moneda
						FROM	app_comision_productos cp
						LEFT JOIN app_costos_proveedor c ON cp.id_costo_proveedor_comision = c.id_proveedor  AND cp.id_producto = c.id_producto
						WHERE	cp.id_producto = '{$producto->idProducto}';";
				$resComision =  $this->queryArray($sql);

				if( $resComision['rows'][0]['config_comision'] == "1" ) {
					$comision = ( ($producto->importe) / 100 * ($resComision['rows'][0]['porcentaje_comision']) );
				}
				else if ( $resComision['rows'][0]['config_comision'] == "2" ) {
					$comision =  ( ($producto->precio * $producto->cantidad) - ($resComision['rows'][0]['costo'] * $producto->cantidad) ) / 100 * ($resComision['rows'][0]['porcentaje_comision']);
				}
				else {
					$comision = 0;
				}

				$sql = "INSERT INTO app_pos_comision_producto
						(id_venta, tipo_comision, sucursal, empleado, producto, cantidad, total_neto, porcentaje_comision, total_comision, fecha)
						VALUES ('$idVenta', '{$resComision['rows'][0]['config_comision']}', '{$_SESSION["sucursal"]}', '$vendedor', '{$producto->idProducto}', '{$producto->cantidad}', '{$producto->importe}', '{$resComision['rows'][0]['porcentaje_comision']}', '{$comision}', '$fechaactual')";
				$this->queryArray($sql);


				///obtiene el total de impuestos
				foreach ($producto->cargos as $key2 => $value2) {
					$impuestos += (float) str_replace(",", "", $value2);
				}
				//echo 'impuestos='.$impuestos.'<br>';
				$selectid = "SELECT max(idventa_producto) as idVentap from app_pos_venta_producto for Update;";
				$result = $this->queryArray($selectid);

				if ($result["rows"] < 1) {
					throw new Exception($result["msg"]);
				}

				$idVentap = $result["rows"][0]["idVentap"] + 1;

				// Si es un extra cambia el comentario por el nombre del extra y limpia el campo de foodware
				if (!empty($producto->descripcion_foodware)) {
					$comentario=$producto->descripcion_foodware;

					$sql = "	UPDATE
									app_campos_foodware
								SET
									descripcion=''
								WHERE
									id_producto=".$producto->idProducto;
					// return $sql;
					$result = $this->query($sql);
				}
				$impuestos = $impuestos * $tipoCambio;
				$producto->precio = $producto->precio * $tipoCambio;
				$producto->importe = $producto->importe * $tipoCambio;
				$producto->descuento_cantidad = $producto->descuento_cantidad * $tipoCambio;
			   // $subtotalSTR = str_replace(",", "", $producto->subtotal);
				if($comnentario == ''){
					$comentario = ' '.$producto->comentario;

				}

				/// EN EL CASO DE DESCUENTOS DESDE COMNADA RAPIDA SE UTILIZO LA FUNCION DE CARACT QUE EN ESTA PARTE SE BORRA PARA NO AFECTAR REPORTES
				$mystring = $producto->caracteristicas;
				$findme   = 'desc';  /// desc%50
				$pos = strpos($mystring, $findme);
				if ($pos === true) {
					$producto->caracteristicas = '';
				}
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				$insertVenta_Pro = "INSERT INTO app_pos_venta_producto (idProducto,cantidad,preciounitario,tipodescuento,descuento,subtotal,idVenta,impuestosproductoventa,montodescuento,total,arr_kit,comentario,caracteristicas,series,lotes,medico,receta,json_kits) "
				. "VALUES (" . $producto->idProducto . "," . $producto->cantidad . ",'" . $producto->precio . "','" . $producto->tipodescuento . "','" . $producto->descuento_cantidad . "'," . $producto->importe . "," . $idVenta . ",'" . $impuestos . "','" . str_replace(",", "", $producto->descuento) . "','" . ($impuestos + $producto->importe)  . "','NULL','" . $comentario . "','".$producto->caracteristicas."','".$producto->series_ids."','".$producto->lotes."','".$producto->medico."','".$producto->receta .",".$producto->recetaRetenida. "','".(  str_replace( "'", "\\'", json_encode($producto->kits) )  )."')";

//echo $insertVenta_Pro.'<br>';die;
				//var_dump(  str_replace( "'", "\\'", json_encode($producto->kits) )  );die;

				///Proceso de series
				$idsSeries = $producto->series_ids;
				//echo $idsSeries;
				$idSeries = explode(',',$idsSeries);
				foreach ($idSeries as $key => $value) {
					if($value!=''){
						$updSeries = "UPDATE app_producto_serie set id_venta='".$idVenta."', estatus='1' where id=".$value;
						//echo $updSeries.'<br>';
						$resupserie = $this->queryArray($updSeries);
					}

				}

				$resultVenta_Pro = $this->queryArray($insertVenta_Pro);
				$idVentaProdcutoI = $resultVenta_Pro['insertId'];

				if ($resultVenta_Pro["total"] < 0) {
					throw new Exception("Error al registrar la venta del producto 2. " . $resultVenta_Pro["msg"]);
				}

				if ($producto->idProducto == '' || $producto->idProducto == null) {
					throw new Exception("Error al registrar la venta del producto 3. ");
				}
				$updateRate = 'UPDATE app_campos_foodware set rate=rate+'.$producto->cantidad.' where id_producto='.$producto->idProducto;
				$resRate = $this->queryArray($updateRate);

				//Inicia la insercion de los impuestos por producto
				$selectProductoImpuesto = "SELECT i.id idImpuesto,i.nombre as impuesto, i.valor from app_producto_impuesto pi inner join app_impuesto i on i.id=pi.id_impuesto where id_producto=" . $producto->idProducto;
				$resultProductoImpuesto = $this->queryArray($selectProductoImpuesto);

				if ($resultProductoImpuesto["total"] < 0) {
					throw new Exception("Error al consultar los impuestos " . $resultProductoImpuesto["msg"]);
				}
				///Insercion de los impuestos de los productos en la venta.
				foreach ($resultProductoImpuesto["rows"] as $keyImpuesto => $valueImpuesto) {
					$valueImpuesto["valor"] = $valueImpuesto["valor"] * $tipoCambio;
					$insertventaproductoimpuesto = "INSERT into app_pos_venta_producto_impuesto (idVentaproducto,idImpuesto,porcentaje) values (" . $idVentaProdcutoI. "," . $valueImpuesto["idImpuesto"] . "," . $valueImpuesto["valor"] . ");";
					$resultventaproductoimpuesto = $this->queryArray($insertventaproductoimpuesto);
					//echo $insertventaproductoimpuesto.'<br>';
					/*if ($resultventaproductoimpuesto["status"] < 0) {
						throw new Exception("Error al guardar los impuestos... " . $resultventaproductoimpuesto["msg"]);
					} */
				} ///fin del ciclo de app_pos_venta_producto_impuesto
				$importe = 0;
				//ciclo de salida de almacen

				///caracteristicass
				if($producto->caracteristicas!=''){
					  if(stristr($producto->caracteristicas, 'desc') === FALSE || stristr($producto->caracteristicas, 'M') === FALSE) { /// si no es descuento desde comanda rapida o si no es precio modificado
					  	$caracteristica = preg_replace('/\*/', ',', $producto->caracteristicas);
						$caracteristicareplace = preg_replace('/([0-9])+/', '\'\0\'', $caracteristica);
						$caracteristicareplace=addslashes($caracteristicareplace);
						$caracteristicareplace = trim($caracteristicareplace, ',');
					  }else{
						  $caracteristicareplace = "'0'";
					  }
				}else{
					$caracteristicareplace = "'0'";
				}


				//$insertInventario = "INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia) values('".$producto->idProducto."','".$caracteristicareplace."','".$producto->cantidad."','".$producto->precio."','".$idALmacenUs."','".$fechaactual."','".$_SESSION['accelog_idempleado']."','0','".$producto->importe."','Venta ".$idVenta."')";
				if($producto->tipoProducto!=2 && $producto->tipoProducto!=6 && $producto->tipoProducto!=8 && $producto->tipoProducto!=10){
					//echo 'no es kit';

					$idtipocosto = $this->tipoCosteoProd($producto->idProducto);
						if($idtipocosto==1){
                            $elunit = $this->costeoProd($producto->idProducto);
                        }else if($idtipocosto==3){
                            $elunit = $this->costeoUltimoCosto($producto->idProducto);
                        }else{
                            $elunit = $this->costeoProd($producto->idProducto);
                        }
                        //$elcost = ($elunit*1)*($producto->cantidad*1);
                        $elcost = ($elunit*1);
                        $elcost = $elcost * $tipoCambio;
                        ///elunit  es el que debe de ir en costo
                        ////elcost es el elunit por cantidad y va en importe


						if($id_comanda != 0){
	                    	$sql = 'SELECT sum(costo) costo FROM com_pedidos where idcomanda = '.$id_comanda.' and status != 3 and idproducto = '.$producto->idProducto.';';
	                    	$res = $this->queryArray($sql);
	                    	$elcost = $res['rows'][0]['costo'];
	                    	$elcost = $elcost * $tipoCambio;
	                    	$elcost = $elcost / $producto->cantidad;
						}


                    if($producto->lotes!=''){
                    	$lotesx = explode(',',$producto->lotes);
                    	foreach ($lotesx as $keyLo => $valueLo) {
                    		if($valueLo!='-' && $valueLo!=''){
                    			$y = explode('-',$valueLo);
                    			$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,id_lote,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$producto->idProducto.'","'.$caracteristicareplace.'","'.$y[0].'","'.$y[1].'","'.$producto->importe.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$elcost.'","Venta '.$idVenta.'","2")';
                    			$resultInven = $this->queryArray($insertInventario);
								$id_mov = $resultInven['insertId'];
                    		}
                    	}
                    }else{
                    	
                    	//echo 'pero<br>';
                    	// insumo elaborado
                    	$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) 
                    	values("'.$producto->idProducto.'","'.$caracteristicareplace.'","'.$producto->cantidad.'","'.$producto->importe.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$elcost .'","Venta '.$idVenta.'","2")';
                    	$resultInven = $this->queryArray($insertInventario);
						$id_mov = $resultInven['insertId'];
                    }


				//echo $insertInventario.'<br>';
				//$resultInven = $this->queryArray($insertInventario);
				//$id_mov = $resultInven['insertId'];

				///Proceso de series
				$idsSeries = $producto->series_ids;
				//echo $idsSeries;
				$idSeries = explode(',',$idsSeries);
				foreach ($idSeries as $key => $value) {
					if($value!=''){
						$updSeries = "UPDATE app_producto_serie set id_venta='".$idVenta."', estatus='1', origen='2' where id=".$value;
						//echo $updSeries.'<br>';
						$resupserie = $this->queryArray($updSeries);
						$insertSerieRastro = 'INSERT into app_producto_serie_rastro(id_serie,id_almacen,fecha_reg,id_mov) values("'.$value.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$id_mov.'")';
						$resRastro = $this->queryArray($insertSerieRastro);
					}

				}

				}
				////Crea tarjetas de regalo
				if($producto->tipoProducto==10){
					//voto latino
					$arrTar = trim($producto->gifCardNumber,'+');
					$arrTar = explode('+',$arrTar);
					$totalTar = $producto->importe + $producto->impuesto;
					$totalXTart = $totalTar / count($arrTar);
					foreach ($arrTar as $keyTar => $valueTar) {
					$insrTR = "INSERT into tarjeta_regalo(numero,valor,usada,montousado,puntos,cliente,idVenta) values('".$valueTar."','".$totalXTart."','0','0','0','0','".$idVenta."')";
					$rex = $this->queryArray($insrTR);
					//echo $insrTR.'<br>';
					}
				}
				//// Fin de cracion de tarjetas de regalo
				if($producto->tipoProducto==6){


					$selComp = "SELECT * from com_kitsXproductos where id_kit=".$producto->idProducto;
					//echo $selComp;
					$resComp = $this->queryArray($selComp);
					//print_r($resComp);



					$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$producto->idProducto.'","'.$caracteristicareplace.'","'.$producto->cantidad.'","'.$producto->importe.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","0","Venta '.$idVenta.' - kit","2")';
                    	$resultInven = $this->queryArray($insertInventario);
						$id_mov = $resultInven['insertId'];


					  $normales = [];
				      $caracteristicas = [];
				      $series = [];
				      $lotes = [];
				      $strError = "";

				      // foreach ($nar as $idProducto => $producto) {

				        foreach ($producto as $campoProducto => $infoProducto) {

				          if( $campoProducto == 'kits' ) {

				            foreach ($infoProducto as $nKit => $kit) {


				              foreach ($kit['items'] as $nItem => $item) {

												if( $item['car'] == '0' && $item['ser'] == '0' && $item['lot'] == '0' ) {
													$pos = array_search( $item['idProduct'], array_column($normales, 'idProduct') );
													if( ( $pos === false ) )
														array_push($normales , $item);
													else
														$normales[$pos]['quantity'] += $item['quantity'];
												}

				                foreach ($item['characteristics'] as $nCharacteristic => $characteristic) {
													$characteristic['idProduct'] = $item['idProduct'];
				                  if( $characteristic['quantity'] != "0" ) {
				                    $pos = array_search( $characteristic['id'], array_column($caracteristicas, 'id') );
				                    if( ( $pos === false ) )
				                      array_push($caracteristicas , $characteristic);
				                    else
				                      $caracteristicas[$pos]['quantity'] += $characteristic['quantity'];
				                  }

				                }

				                foreach ($item['series'] as $nSerie => $serie) {
													$pos = array_search( $item['idProduct'], array_column($series, 'idProduct') );
													if( ( $pos === false ) ) {
														array_push($series , [ 'idProduct' => $item['idProduct'], 'series' => [ $serie['id'] ] ] );
													}
													else {
														if( !( in_array($serie['id'], $series[$pos]['series']) ) )
					                    array_push( $series[$pos]['series'], $serie['id'] );
					                  else {
					                    $pos = array_search( $serie['id'], array_column($item['optionsSeries'], 'id') );
					                    $strError .= "La serie {$item['optionsSeries'][$pos]['text']} esta repetida\n";
					                  }
													}
													// $serieAux['idProduct'] = $item['idProduct'];
													// $serieAux['id'] = $serie;



				                  // if( !( in_array($serie, $series) ) )
				                  //   array_push($series , $serieAux);
				                  // else {
				                  //   $pos = array_search( $serie['id'], array_column($item['optionsSeries'], 'id') );
				                  //   $strError .= "La serie {$item['optionsSeries'][$pos]['text']} esta repetida\n";
				                  // }

				                }

				                foreach ($item['batches'] as $nLote => $lote) {
													$lote['idProduct'] = $item['idProduct'];
				                  if( $lote['quantity'] != "0" ) {
				                    $pos = array_search( $lote['id'], array_column($lotes, 'id') );
				                    if( ( $pos === false ) )
				                      array_push($lotes , $lote);
				                    else
				                      $lotes[$pos]['quantity'] += $lote['quantity'];
				                  }

				                }

				              }


				            }
				          }
				        }
					// }

					foreach ($normales as $key => $value) {
						$selPrice = "SELECT precio from app_productos where id=".$value['idProduct'];
						$resPrice = $this->queryArray($selPrice);
						$im = $producto->cantidad * $value['quantity'];

						$idtipocosto = $this->tipoCosteoProd($value['idProduct']);
						if($idtipocosto==1){
			                $elunit = $this->costeoProd($value['idProduct']);
			            }else if($idtipocosto==3){
			                $elunit = $this->costeoUltimoCosto($value['idProduct']);
			            }else{
			                $elunit = $this->costeoProd($value['idProduct']);
			            }
			            $elcost = ($elunit*1);
						$elcost = $elcost * $tipoCambio;
						$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
										VALUES
											( {$value['idProduct']}, \"\'0\'\", 0, 0, {$value['quantity']}, 0, $idALmacenUs, 0, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 0, $elcost, 'Venta $idVenta - kit{$producto->idProducto}');";
						$this->queryArray($sql);
					}

					foreach ($caracteristicas as $key => $value) {
						$selPrice = "SELECT precio from app_productos where id=".$value['idProduct'];
						$resPrice = $this->queryArray($selPrice);
						$im = $producto->cantidad * $value['quantity'];

						$idtipocosto = $this->tipoCosteoProd($value['idProduct']);
						if($idtipocosto==1){
			                $elunit = $this->costeoProd($value['idProduct']);
			            }else if($idtipocosto==3){
			                $elunit = $this->costeoUltimoCosto($value['idProduct']);
			            }else{
			                $elunit = $this->costeoProd($value['idProduct']);
			            }
			            $elcost = ($elunit*1);
			            $elcost = $elcost * $tipoCambio;

						$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
										VALUES
											( {$value['idProduct']}, \"{$value['id']}\", 0, 0, {$value['quantity']}, 0, $idALmacenUs, 0, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 0, $elcost, 'Venta $idVenta - kit{$producto->idProducto}');";
						$this->queryArray($sql);
					}
					foreach ($series as $key => $value) {
						$selPrice = "SELECT precio from app_productos where id=".$value['idProduct'];
						$resPrice = $this->queryArray($selPrice);
						$im = $producto->cantidad * $value['quantity'];

						$idtipocosto = $this->tipoCosteoProd($value['idProduct']);
						if($idtipocosto==1){
								$elunit = $this->costeoProd($value['idProduct']);
						}else if($idtipocosto==3){
								$elunit = $this->costeoUltimoCosto($value['idProduct']);
						}else{
								$elunit = $this->costeoProd($value['idProduct']);
						}
						$elcost = ($elunit*1);
						$elcost = $elcost * $tipoCambio;
						$len = count($value['series']);
						$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
										VALUES
											( {$value['idProduct']}, \"\'0\'\", 0, '0', $len, 0, $idALmacenUs, 0, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 0, $elcost, 'Venta $idVenta - kit{$producto->idProducto}');";
						$idMov = $this->queryArray($sql);

						foreach ($value['series'] as $k => $v) {
							$sql = "INSERT INTO app_producto_serie_rastro (id_serie, id_almacen, fecha_reg, id_mov)
											VALUES
												($v, $idALmacenUs, '$fechaactual', {$idMov['insertId']});";
							$this->queryArray($sql);
							$sql = "UPDATE app_producto_serie
											SET id_venta = '$idVenta', estatus = '1'
											WHERE	id = '$v'";
							$this->queryArray($sql);
						}
					}
					foreach ($lotes as $key => $value) {
						$selPrice = "SELECT precio from app_productos where id=".$value['idProduct'];
						$resPrice = $this->queryArray($selPrice);
						$im = $producto->cantidad * $value['quantity'];

						$idtipocosto = $this->tipoCosteoProd($value['idProduct']);
						if($idtipocosto==1){
			                $elunit = $this->costeoProd($value['idProduct']);
			            }else if($idtipocosto==3){
			                $elunit = $this->costeoUltimoCosto($value['idProduct']);
			            }else{
			                $elunit = $this->costeoProd($value['idProduct']);
			            }
			            $elcost = ($elunit*1);
			            $elcost = $elcost * $tipoCambio;

						$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
										VALUES
											( {$value['idProduct']}, \"\'0\'\", 0, '{$value['id']}', {$value['quantity']}, 0, $idALmacenUs, 0, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 0, $elcost, 'Venta $idVenta - kit{$producto->idProducto}');";
						$this->queryArray($sql);
					}
					// var_dump($caracteristicas);
					// var_dump($series);
					// var_dump($lotes);

					//die;
					/*---------------------------------------------------------------------
					// -----------------------------------------------------------------------*/
					// foreach ($resComp['rows'] as $k => $v) {
					//
					// 	$selPrice = "SELECT precio from app_productos where id=".$v['id_producto'];
					// 	$resPrice = $this->queryArray($selPrice);
					// 	$im = $producto->cantidad * $v['cantidad'];
					//
					// 	$idtipocosto = $this->tipoCosteoProd($v['id_producto']);
					// 	if($idtipocosto==1){
			          //                   $elunit = $this->costeoProd($v['id_producto']);
			          //               }else if($idtipocosto==3){
			          //                   $elunit = $this->costeoUltimoCosto($v['id_producto']);
			          //               }else{
			          //                   $elunit = $this->costeoProd($v['id_producto']);
			          //               }
			          //               //$elcost = ($elunit*1)*($im*1);
			          //               $elcost = ($elunit*1);
			          //               $elcost = $elcost * $tipoCambio;
			          //               ///elunit  es el que debe de ir en costo
			          //               ////elcost es el elunit por cantidad y va en importe
					//
					//
					//
					// 	//$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$v['id_producto'].'","'.$caracteristicareplace.'","'.$im.'","'.($resPrice['rows'][0]['precio'] * $im).'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$resPrice['rows'][0]['precio'].'","Venta '.$idVenta.' - kit","2")';
					//
					// 	$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$v['id_producto'].'","'.$caracteristicareplace.'","'.$im.'","0","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$elcost.'","Venta '.$idVenta.' - kit'.$producto->idProducto.'","2")';
					// 	//echo $insertInventario.'<br>';
					// 	//echo $insertInventario.'<br>';
					//
					// 	$resultInven = $this->queryArray($insertInventario);
					//
					//
					// }
				}


					///else if del pimer if del ciclo
				} else if($key!='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden' && $producto['id_promocion'] != 0){

					$auximpR = 1; // en el caso de la promocion ingresa el importe varias veces -> auxiliar para solo ingresar una vez
					foreach ($producto['products'] as $key5 => $value5) {
						$impuestos = 0;
						$producto = (object) $producto;

						$sql = "SELECT	cp.*, c.costo, c.id_moneda
								FROM	app_comision_productos cp
								LEFT JOIN app_costos_proveedor c ON cp.id_costo_proveedor_comision = c.id_proveedor  AND cp.id_producto = c.id_producto
								WHERE	cp.id_producto = '".$value5['id']."';";
						$resComision =  $this->queryArray($sql);

						if( $resComision['rows'][0]['config_comision'] == "1" ) {
							$comision = ( ($producto->importe) / 100 * ($resComision['rows'][0]['porcentaje_comision']) );
						}
						else if ( $resComision['rows'][0]['config_comision'] == "2" ) {
							$comision =  ( ($value5['precio'] * 1) - ($resComision['rows'][0]['costo'] * 1) ) / 100 * ($resComision['rows'][0]['porcentaje_comision']);
						}
						else {
							$comision = 0;
						}

						$sql = "INSERT INTO app_pos_comision_producto
								(id_venta, tipo_comision, sucursal, empleado, producto, cantidad, total_neto, porcentaje_comision, total_comision, fecha)
								VALUES ('$idVenta', '{$resComision['rows'][0]['id_tipo_comision']}', '{$_SESSION["sucursal"]}', '$vendedor', '".$value5['id']."', '1', '{$producto->importe}', '{$resComision['rows'][0]['porcentaje_comision']}', '{$comision}', NOW())";
						$this->queryArray($sql);


						///obtiene el total de impuestos
						foreach ($producto->cargos as $key2 => $value2) {
							$impuestos += (float) str_replace(",", "", $value2);
						}
						//echo 'impuestos='.$impuestos.'<br>';
						$selectid = "SELECT max(idventa_producto) as idVentap from app_pos_venta_producto for Update;";
						$result = $this->queryArray($selectid);

						if ($result["rows"] < 1) {
							throw new Exception($result["msg"]);
						}

						$idVentap = $result["rows"][0]["idVentap"] + 1;

						// Si es un extra cambia el comentario por el nombre del extra y limpia el campo de foodware
						if (!empty($producto->descripcion_foodware)) {
							$comentario=$producto->descripcion_foodware;

							$sql = "	UPDATE
											app_campos_foodware
										SET
											descripcion=''
										WHERE
											id_producto=".$value5['id'];
							// return $sql;
							$result = $this->query($sql);
						}

						///Proceso de series
						$idsSeries = $producto->series_ids;
						//echo $idsSeries;
						$idSeries = explode(',',$idsSeries);
						foreach ($idSeries as $key => $value) {
							if($value!=''){
								$updSeries = "UPDATE app_producto_serie set id_venta='".$idVenta."', estatus='1' where id=".$value;
								//echo $updSeries.'<br>';
								$resupserie = $this->queryArray($updSeries);
							}

						}


						$updateRate = 'UPDATE app_campos_foodware set rate=rate+'.'1'.' where id_producto='.$value5['id'];
						$resRate = $this->queryArray($updateRate);

						$importe = 0;
						//ciclo de salida de almacen

						///caracteristicass
						if($producto->caracteristicas!=''){
							$caracteristica = preg_replace('/\*/', ',', $producto->caracteristicas);
							$caracteristicareplace = preg_replace('/([0-9])+/', '\'\0\'', $caracteristica);
							$caracteristicareplace=addslashes($caracteristicareplace);
							$caracteristicareplace = trim($caracteristicareplace, ',');
						}else{
							$caracteristicareplace = "'0'";
						}


						//$insertInventario = "INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia) values('".$producto->idProducto."','".$caracteristicareplace."','".$producto->cantidad."','".$producto->precio."','".$idALmacenUs."','".$fechaactual."','".$_SESSION['accelog_idempleado']."','0','".$producto->importe."','Venta ".$idVenta."')";
						if($producto->tipoProducto!=2 && $producto->tipoProducto!=6 && $producto->tipoProducto!=8 && $producto->tipoProducto!=10){
							//echo 'no es kit';

							$idtipocosto = $this->tipoCosteoProd($value5['id']);
								if($idtipocosto==1){
		                            $elunit = $this->costeoProd($value5['id']);
		                        }else if($idtipocosto==3){
		                            $elunit = $this->costeoUltimoCosto($value5['id']);
		                        }else{
		                            $elunit = $this->costeoProd($value5['id']);
		                        }
		                        //$elcost = ($elunit*1)*($producto->cantidad*1);
		                        $elcost = ($elunit*1);
		                        $elcost = $elcost * $tipoCambio;
		                        ///elunit  es el que debe de ir en costo
		                        ////elcost es el elunit por cantidad y va en importe

		                        //$importeR = $producto->importe
		                        $importeR = $producto->importe;


		                    	if($id_comanda != 0){
		                        	$sql = 'SELECT sum(costo) costo FROM com_pedidos where idcomanda = '.$id_comanda.' and status != 3 and idproducto = '.$producto->idProducto.';';
		                        	$res = $this->queryArray($sql);
		                        	$elcost = $res['rows'][0]['costo'];
		                        	$elcost = $elcost * $tipoCambio;

		                        	// costo en promociones
		                        	if($value5['costo'] != ''){
		                        		$elcost = $value5['costo'];
		                        	}
		                        	$importe = $value5['costo'];
		                        	// costo en promociones


		                        	if($auximpR == 1){
		                        		$importeR = $producto->importe;
		                        		$auximpR = 0;
		                        	}else{
		                        		$importeR = 0;
		                        	}



		                        }

		                    if($producto->lotes!=''){
		                    	$lotesx = explode(',',$producto->lotes);
		                    	foreach ($lotesx as $keyLo => $valueLo) {
		                    		if($valueLo!='-' && $valueLo!=''){
		                    			$y = explode('-',$valueLo);
		                    			$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,id_lote,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$value5['id'].'","'.$caracteristicareplace.'","'.$y[0].'","'.$y[1].'","'.$producto->importe.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$value5['precio'].'","Venta '.$idVenta.'","2")';
		                    			$resultInven = $this->queryArray($insertInventario);
										$id_mov = $resultInven['insertId'];
		                    		}
		                    	}
		                    }else{
		                    	//echo 'pero<br>';

		                    	// insumo elaborado
		                    	$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) 
		                    	values("'.$value5['id'].'","'.$caracteristicareplace.'","1","'.$importeR.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$elcost .'","Venta '.$idVenta.'","2")';
		                    	$resultInven = $this->queryArray($insertInventario);
								$id_mov = $resultInven['insertId'];
		                    }


						//echo $insertInventario.'<br>';
						//$resultInven = $this->queryArray($insertInventario);
						//$id_mov = $resultInven['insertId'];

						///Proceso de series
						$idsSeries = $producto->series_ids;
						//echo $idsSeries;
						$idSeries = explode(',',$idsSeries);
						foreach ($idSeries as $key => $value) {
							if($value!=''){
								$updSeries = "UPDATE app_producto_serie set id_venta='".$idVenta."', estatus='1', origen='2' where id=".$value;
								//echo $updSeries.'<br>';
								$resupserie = $this->queryArray($updSeries);
								$insertSerieRastro = 'INSERT into app_producto_serie_rastro(id_serie,id_almacen,fecha_reg,id_mov) values("'.$value.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$id_mov.'")';
								$resRastro = $this->queryArray($insertSerieRastro);
							}

						}

						}
						if($producto->tipoProducto==6){



							$selComp = "SELECT * from com_kitsXproductos where id_kit=".$value5['id'];
							//echo $selComp;
							$resComp = $this->queryArray($selComp);
							//print_r($resComp);
							foreach ($resComp['rows'] as $k => $v) {

								$selPrice = "SELECT precio from app_productos where id=".$v['id_producto'];
								$resPrice = $this->queryArray($selPrice);
								$im = $producto->cantidad * $v['cantidad'];

								$idtipocosto = $this->tipoCosteoProd($v['id_producto']);
								if($idtipocosto==1){
		                            $elunit = $this->costeoProd($v['id_producto']);
		                        }else if($idtipocosto==3){
		                            $elunit = $this->costeoUltimoCosto($v['id_producto']);
		                        }else{
		                            $elunit = $this->costeoProd($v['id_producto']);
		                        }
		                        //$elcost = ($elunit*1)*($im*1);
		                        $elcost = ($elunit*1);
		                        $elcost = $elcost * $tipoCambio;
		                        ///elunit  es el que debe de ir en costo
		                        ////elcost es el elunit por cantidad y va en importe



								//$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$v['id_producto'].'","'.$caracteristicareplace.'","'.$im.'","'.($resPrice['rows'][0]['precio'] * $im).'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$resPrice['rows'][0]['precio'].'","Venta '.$idVenta.' - kit","2")';

								$insertInventario = 'INSERT into app_inventario_movimientos(id_producto,id_producto_caracteristica,cantidad,importe,id_almacen_origen,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values("'.$v['id_producto'].'","'.$caracteristicareplace.'","'.$im.'","'.$elcost.'","'.$idALmacenUs.'","'.$fechaactual.'","'.$_SESSION['accelog_idempleado'].'","0","'.$elunit.'","Venta '.$idVenta.' - kit","2")';
								//echo $insertInventario.'<br>';
								//echo $insertInventario.'<br>';

								$resultInven = $this->queryArray($insertInventario);


							}
						}
					}
					//print_r($producto); exit();
					$insertVenta_Pro = "INSERT INTO app_pos_venta_producto (idProducto,cantidad,preciounitario,tipodescuento,descuento,subtotal,idVenta,impuestosproductoventa,montodescuento,total,arr_kit,comentario,caracteristicas,series,lotes) VALUES (0, 1,'" . $producto->precio . "','','0','" . $producto->precio . "'," . $idVenta . ",'0','0','" . $producto->precio  . "','NULL','".$producto->nombre."','','','')";

					$resultVenta_Pro = $this->queryArray($insertVenta_Pro);
				}//fin del if del cliclo
			} //fin del ciclo de prodcutos


			////////////////////////////////////////////////////////////////////////////////////////////////
			//ch@ para insumos elabordos en como opcionales en recetas en
				if($id_comanda != 0){
					$sqlo = "SELECT group_concat(opcionales) opcionales FROM com_pedidos WHERE idcomanda = ".$id_comanda." AND idproducto <> 0;";
					$respo =  $this->queryArray($sqlo);
					$opcionales = $respo['rows'][0]['opcionales'];
					$arrOpc = explode(',', $opcionales);

					foreach ($arrOpc as $key => $value) {
						$insertOpc = "INSERT INTO app_pos_venta_producto (idProducto,cantidad,preciounitario,tipodescuento,descuento,subtotal,idVenta,impuestosproductoventa,montodescuento,total,arr_kit,comentario) VALUES ('".$value."',1,0,0,0,0,'".$idVenta."',0,0,0,'NULL','omitir')";
						$resultOpc = $this->queryArray($insertOpc);
					}
				}
			//ch@
			////////////////////////////////////////////////////////////////////////////////////////////////

				///Insercion de los pagos de la venta
				foreach ($_SESSION['pagos-caja']["pagos"] as $idFormapago => $value) {
					if ($value["cantidad"] > 0) {
						$value["cantidad"] = $value["cantidad"] * $tipoCambio;
						$cantidad = $value["cantidad"];
						$referencia = $value["referencia"];
						$tarjeta = $value["tarjeta"];

						$selectid = "SELECT max(id) as idVentaPagos from app_pos_venta_pagos for Update;";
						$result = $this->queryArray($selectid);

						if ($result["rows"] < 1) {
							throw new Exception($result["msg"]);
						}

						$idVentaPagos = $result["rows"][0]["idVentaPagos"] + 1;
						//venta_pagos
						$insertVenta_Pagos = "INSERT INTO app_pos_venta_pagos(id,idVenta,idFormapago,monto,referencia,tarjeta) "
						. "VALUES(" . $idVentaPagos . "," . $idVenta . "," . $idFormapago . "," . str_replace(",", "", $cantidad) . ",'" . $referencia . "','".$tarjeta."')";
						//echo $insertVenta_Pagos.'<br>';
						$resultinsertVenta_Pagos = $this->queryArray($insertVenta_Pagos);

						if ($resultinsertVenta_Pagos["total"] < 0) {
							throw new Exception("No se pudo guardar el cargo del pago. " . $resultinsertVenta_Pagos["msg"]);
						}

						if($idFormapago == 6 && $documento!=2){
							$cambioReturn = (str_replace(",", "", $value["cantidad"])*1) / $tipoCambio;
							$query = "INSERT INTO app_pagos(cobrar_pagar,id_prov_cli,cargo,fecha_pago,concepto,id_forma_pago,id_moneda,tipo_cambio) values('0','".$cliente."','".str_replace(",", "", $cambioReturn)."','".$fechaactual."','Ticket Caja ".$idVenta.":".$referencia."','6','".$moneda."','".$tipoCambio."')";
							//echo $query;
							$resCargo = $this->queryArray($query);
						}
						if ($idFormapago == 3) {

							//unset($diasCredito);
							//unset($diasC);
							//unset($nuevafecha);

							$cc = "select * from tarjeta_regalo where numero='" . $referencia . "'";

							$resultCC = $this->queryArray($cc);

							foreach ($resultCC["rows"] as $key => $valueCC) {
								$cantidad = str_replace(',', '', $cantidad);
								$extensionconsulta = "";
								if (((float) $cantidad + (float) $valueCC["montousado"]) >= (float) $valueCC["valor"]) {
									if($tr ==''){
										$extensionconsulta = ",usada=1";
									}
								}

								$updateTarjeta = "Update tarjeta_regalo "
								. "set montousado=" . str_replace(',', '', ((float) $cantidad + (float) $valueCC["montousado"])) . $extensionconsulta . " "
								. "where numero='" . $referencia . "'";

								$resultupdateTarjeta = $this->queryArray($updateTarjeta);
								if($usarPuntos == 1){
									//echo 94945985;
									$updatePuntos = "UPDATE tarjeta_regalo set puntos=(puntos-".$cantidad.") where numero='".$referencia."';";
									///echo $updatePuntos;
									$this->queryArray($updatePuntos);

								}
								/*if($totalPuntosInput > 0 && $totalPuntosInput!=''){
									$updatePuntos = "UPDATE tarjeta_regalo set puntos=(puntos + ".$totalPuntosInput.")  where numero='".$referencia."';";
									$this->queryArray($updatePuntos);
								} */

								if ($resultupdateTarjeta["total"] < 0) {
									throw new Exception("Error al actualizar la tarjeta de regalo " . $resultupdateTarjeta["msg"]);
								}
							}
						}
						if ($idFormapago == 10) {
							$cc = "select * from tarjeta_regalo where numero='" . $referencia . "'";
							$resultCC = $this->queryArray($cc);

							foreach ($resultCC["rows"] as $key => $valueCC) {
								$cantidad = str_replace(',', '', $cantidad);
								$extensionconsulta = "";
								if (((float) $cantidad + (float) $valueCC["montousado"]) >= (float) $valueCC["valor"]) {
									if($tr ==''){
										$extensionconsulta = ",usada=1";
									}
								}

								/*$updateTarjeta = "Update tarjeta_regalo "
								. "set montousado=" . str_replace(',', '', ((float) $cantidad + (float) $valueCC["montousado"])) . $extensionconsulta . " "
								. "where numero='" . $referencia . "'";

								$resultupdateTarjeta = $this->queryArray($updateTarjeta); */
								if($usarPuntos == 1){
									$updatePuntos = "UPDATE tarjeta_regalo set puntos=(puntos-".$cantidad.") where numero='".$referencia."';";
									$this->queryArray($updatePuntos);
								}
								if ($resultupdateTarjeta["total"] < 0) {
									throw new Exception("Error al actualizar la tarjeta de regalo " . $resultupdateTarjeta["msg"]);
								}
							}
						}

					}
				} //fin del ciclo de los pago


				/////Abona los puntos a la tarjeta de regalo
				if($tr !=''){
					$_SESSION['puntos_venta'] = $totalPuntosInput;
					$updatePuntos = "UPDATE tarjeta_regalo set puntos=(puntos + ".$totalPuntosInput.")  where numero='".$tr."';";
					$this->queryArray($updatePuntos);
				}


			////////////////////////////////////////////////////////////////////////////////////
			//AÑADIDO POR IVAN CUENCA
			//INICIA CONEXION CON ACONTIA
			//Si existe una venta comprueba si hay conexion con acontia
			if(intval($idVenta))
			{
				//Si se guardo la venta genera la poliza
				//Esta conectado a acontia?
				$conexion_acontia = $this->conexion_acontia();
				$conexion_acontia = $conexion_acontia->fetch_assoc();
				if(intval($conexion_acontia['conectar_acontia']))
				{
					$this->generar_poliza(0,$fechaactual,$conexion_acontia,$idVenta,$documento,2);
					$this->generar_poliza_pagos(0,$fechaactual,$conexion_acontia,$idVenta,$documento,2);
				}
			}

			//TERMINA CONEXION CON ACONTIA
			////////////////////////////////////////////////////////////////////////////////////


			/* Propinas
			 ========================================================================================================= */
				if (!empty($_POST['propinas'])) {
					foreach ($_POST['propinas'] as $k => $v) {
						if($v['remove'] != 1){
							$sql = "INSERT INTO
										com_propinas(id_venta, metodo_pago, monto, num_tarjeta, tipo_tarjeta)
									VALUES
										(".$idVenta.", '".$v['metodo_pago']."', '".$v['monto']."', '".$v['num_tarjeta']."', '".$v['tipo_tarjeta']."')";

							$result_propina = $this->query($sql);
						}
					}
				}

			/* FIN Propinas
			 ========================================================================================================= */

			 $sql = "";

		   // exit();
			if (isset($_SESSION['caja']['pedido'])){
				$xyruryr = $this->estatusPedido($_SESSION['caja']['pedido'],$idVenta);
			}
			if (isset($_SESSION['caja']['idorden'])){
				$xyruryr = $this->estatusOrden($_SESSION['caja']['idorden'],$idVenta);
			}

			return array("status" => true, "idVenta" => $idVenta);
		} catch (Exception $e) {
			return array("status" => false, "msg" => $e->getMessage());
		}
	}//fin funcion guarda venta
	public function facturar($idFact, $idVenta, $bloqueo, $mensaje,$consumo,$doc,$moneda,$tipoCambio,$seriex) {
	   /* print_r($_SESSION['caja']);
		exit();*/
		$_SESSION["caja"] = $this->object_to_array($_SESSION["caja"]);

		$monto = str_replace(",", "", $_SESSION["caja"]["cargos"]["total"]);
		$impuestos = 0;

		$arraytmp = (object) $_SESSION['caja'];
		foreach ($arraytmp as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden' ) {
				$impuestos = 0;
				foreach ($producto->cargos as $key2 => $value2) {
					$impuestos += $value2;
				}
			}
		}

		if ($memsaje != false || $mensaje != '') {
			$updateVenta = $this->queryArray("UPDATE app_pos_venta set observacion = '" . $mensaje . "' where idVenta =" . $idVenta);
		}

		/* --- Configuracion de las series  ---*/
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);


		if($doc == 2){ // factura
			if($resSer['rows'][0]['seriesFactura']==1){
				$folios = "SELECT serie,folio FROM pvt_serie_folio where id=".$seriex;
			}else{
				$folios = "SELECT serie,folio FROM pvt_serie_folio LIMIT 1";
			}

		}else{ // honorarios
			$folios = "SELECT serie_h,folio_h FROM pvt_serie_folio LIMIT 1";
		}

		$data = $this->queryArray($folios);
		if ($data["total"] > 0) {
			$data = $data["rows"][0];
		}
		if($doc == 2){
			$serie = $data['serie'];
			$folio = $data['folio'];
		}else{
			$serie = $data['serie_h'];
			$folio = $data['folio_h'];
		}
		///Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];
		// Receptor
		//===============================================================

		$parametros['Receptor'] = array();
		if ($idFact == 0) {

			$parametros['Receptor']['RFC'] = "XAXX010101000";
		} else {
			$df = (object) $this->datosFacturacion($idFact);
			$parametros['Receptor']['RFC'] = $df->rfc;
			$parametros['Receptor']['RazonSocial'] = utf8_decode($df->razon_social);
			$parametros['Receptor']['Pais'] = utf8_decode($df->pais);
			$parametros['Receptor']['Calle'] = utf8_decode($df->domicilio);
			$parametros['Receptor']['NumExt'] = $df->num_ext;
			$parametros['Receptor']['Colonia'] = utf8_decode($df->colonia);
			$parametros['Receptor']['Municipio'] = utf8_decode($df->municipio);
			$parametros['Receptor']['Ciudad'] = utf8_decode($df->ciudad);
			$parametros['Receptor']['CP'] = $df->cp;
			$parametros['Receptor']['Estado'] = utf8_decode($df->estado);
			$parametros['Receptor']['Email1'] = $df->correo;
		}
		//Obteniendo la descripcion de la forma de pago
		$formapago = "";
		$queryFormaPago = " SELECT nombre,referencia,claveSat from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;
		$resultqueryFormaPago = $this->queryArray($queryFormaPago);
		foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
			if (strlen($pagosValue["referencia"]) > 0) {
				//$formapago .= $pagosValue['claveSat'] . " Ref:" . $pagosValue['referencia'] . ",";
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = $pagosValue['referencia'];
			} else {
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = '';

			}
		}
		$formapago = substr($formapago, 0, strlen($formapago) - 1);
		if ($formapago == "") {
			$formapago = ".";
		}

		$select = "SELECT * from cont_coin where coin_id=".$moneda;
		$resCon = $this->queryArray($select);

		$Email = $df->correo;

		$parametros['DatosCFD']['FormadePago'] = "Pago en una sola exhibicion";
		$parametros['DatosCFD']['MetododePago'] = utf8_decode($formapago);
		$parametros['DatosCFD']['NumCtaPago'] = $refFormaPago;
		$parametros['DatosCFD']['TipoCambio'] = $tipoCambio;
		$parametros['DatosCFD']['Moneda'] = $resCon['rows'][0]['codigo'];
		$parametros['DatosCFD']['Subtotal'] = str_replace(",", "", number_format($_SESSION["caja"]["cargos"]["subtotal"],2));
		$parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] + $_SESSION['caja']['descGeneral'];
	   // $parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] - 0.01;
		$parametros['DatosCFD']['Total'] = str_replace(",", "", number_format($_SESSION["caja"]["cargos"]["total"],2));

	   // $parametros['DatosCFD']['Total'] = $parametros['DatosCFD']['Total'] - 0.01;
		$parametros['DatosCFD']['Serie'] = $serie;
		$parametros['DatosCFD']['Folio'] = $folio;
		$parametros['DatosCFD']['TipodeComprobante'] = "F"; //F o C
		$parametros['DatosCFD']['MensajePDF'] = "";
		$parametros['DatosCFD']['LugarDeExpedicion'] = "Mexico";
		$parametros['DatosCFD']['Descuento'] = $_SESSION['caja']['descGeneral'];

		$x = 0;
		$textodescuento = "";
		//Empieza a llenar los conceptos
		foreach ($_SESSION['caja'] as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden') {
				$producto = (object) $producto;
				$descuentogeneral = 0;
				///desceuntos
				//echo "( descuento -> ".$producto->descuento_cantidad.")";
			   /* if ($producto->tipodescuento == "%") {
					$descuentogeneral = (($producto->precioventa * str_replace(",", "", $producto->descuento)) / 100) * $producto->cantidad;
					if ($producto->descuento > 0) {
						$textodescuento.=" - " . cajaModel::cortadec(str_replace(",", "", $producto->descuento_cantidad)) . " %";
					}
				}
				if ($producto->tipodescuento == "$") {
					$descuentogeneral = $producto->descuento;
					if ($producto->descuento > 0) {
						$textodescuento.=" - $" . cajaModel::cortadec(str_replace(",", "", $producto->descuento_cantidad)) . "";
					}
				} */
				$conceptosDatos[$x]["Cantidad"] = $producto->cantidad;
				$conceptosDatos[$x]["Unidad"] = $producto->unidad;
				$conceptosDatos[$x]["Precio"] = $producto->precio;
				if ($producto->descripcion != '') {
					$conceptosDatos[$x]["Descripcion"] = trim($producto->descripcion . " " . $textodescuento);
				} else {
					$conceptosDatos[$x]["Descripcion"] = trim($producto->nombre . " " . $textodescuento);
				}
				$textodescuento = '';
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio);
				$consumoTotal +=  $conceptosDatos[$x]['Importe']*1;
				$x++;


			}//fin del if del ciclo
		}//fin del cilo de llenar conceptos

		$nn2 = $_SESSION['caja']['cargos']['impuestosFactura'];
		$nnf = $_SESSION['caja']['cargos']['impuestosPdf'];
		/* FACTURACION AZURIAN
		============================================================== */
		global $api_lite;
		if(!isset($api_lite)){
			if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/config.php');
			else require_once('../webapp/modulos/SAT/config.php');
		}
		else require $api_lite . "modulos/SAT/config.php";

		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d') . 'T' . date('H:i:s', strtotime("-10 minute"));


		$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$logo = $this->queryArray($logo);
		$r3 = $logo["rows"][0];

		$azurian = array();
		//echo $bloqueo.'??';
		if ($bloqueo == 0) {
			//echo 'entro a bloqueo';
			$queryConfiguracion = "SELECT a.*, b.regimen as regimenf FROM pvt_configura_facturacion a INNER JOIN pvt_catalogo_regimen b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RFC'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp;
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				global $api_lite;
				if(!isset($api_lite)) echo json_encode($JSON);
				else return $JSON;
				exit();
			}
		}
		 /* Observaciones pdf */
		$azurian['Observacion']['Observacion'] = $mensaje;

		$sucursal = $this -> sucursal();

		  /*----- Factura de Consumo --------*/
		$queryConsumo = "SELECT consumo from com_configuracion where id_sucursal = ".$sucursal;

		$resConsumo = $this->queryArray($queryConsumo);
		$consumo = $resConsumo['rows'][0]['consumo'];

		if($consumo == 1 || $consumo=='1'){
			unset($nn2);
			unset($nnf);
			$precioSiniva = $parametros['DatosCFD']['Total'] / 1.16;
			//echo $ivaCon;
			$elIva = $precioSiniva * 0.16;
		   // $subTotalCon = $parametros['DatosCFD']['Total'] - $ivaCon;
			$parametros['DatosCFD']['Subtotal'] = $precioSiniva;
			$nn2["IVA"]["16.00"] = $elIva;
			$nnf["IVA"]["16.00"]['Valor'] = $elIva;

			//echo $nn2["IVA"]["16.00"];
			//echo 'sub'.$parametros['DatosCFD']['Subtotal'];
		}
		//exit();
		/* IMPUESTOS
		============================================================== */
		if ($nn2 == '') {
			$nn2["IVA"]["0.0"]["Valor"] = 0.00;
		}
		if ($nnf == '') {
			$nnf["IVA"]["0.0"]["Valor"] = 0.00;
		}
		$nn = $nn2;
		$azurian['nn']['nn'] = $nn;
		$azurian['nnf']['nnf'] = $nnf;
		$azurian['org']['logo'] = $r3["logoempresa"];

		/* CORREO RECEPTOR
		============================================================== */
		$azurian['Correo']['Correo'] = $Email;

		/* Datos Basicos
		============================================================== */
		$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		$azurian['Basicos']['Moneda'] = $parametros['DatosCFD']['Moneda'];
		$azurian['Basicos']['metodoDePago'] = $parametros['DatosCFD']['MetododePago'];
		$azurian['Basicos']['NumCtaPago'] = $parametros['DatosCFD']['NumCtaPago'];
		$azurian['Basicos']['LugarExpedicion'] = $parametros['DatosCFD']['LugarDeExpedicion'];
		$azurian['Basicos']['version'] = '3.2';
		$azurian['Basicos']['serie'] = $parametros['DatosCFD']['Serie']; //No obligatorio
		$azurian['Basicos']['folio'] = $parametros['DatosCFD']['Folio']; //No obligatorio
		$azurian['Basicos']['fecha'] = $fecha;
		$azurian['Basicos']['sello'] = '';
		$azurian['Basicos']['formaDePago'] = $parametros['DatosCFD']['FormadePago'];
		$azurian['Basicos']['tipoDeComprobante'] = 'ingreso';
		$azurian['tipoFactura'] = 'factura';
		$azurian['Basicos']['noCertificado'] = '';
		$azurian['Basicos']['certificado'] = '';
		$str_subtotal = number_format($parametros['DatosCFD']['Subtotal'], 2);
		$azurian['Basicos']['subTotal'] = str_replace(",", "", $str_subtotal);
		$azurian['Basicos']['descuento'] = number_format($parametros['DatosCFD']['Descuento'],2);
		$azurian['Basicos']['descuento'] = str_replace(',', '', $azurian['Basicos']['descuento']);
		$str_total = number_format($parametros['DatosCFD']['Total'], 2);
		$str_total = str_replace(',', '',$str_total);
		//$str_total = $str_total - 0.01;
		//$str_total = number_format($str_total,0).'.00';  //Comente para que Salgan Decimales Normalmente
		$str_total = number_format($str_total,2);
		$azurian['Basicos']['total'] = str_replace(",", "", $str_total);

		/* Datos Emisor
		============================================================== */

		$azurian['Emisor']['rfc'] = strtoupper($parametros['EmisorTimbre']['RFC']);
		$azurian['Emisor']['nombre'] = strtoupper($parametros['EmisorTimbre']['RazonSocial']);

		/* Datos Fiscales Emisor
		============================================================== */

		$azurian['FiscalesEmisor']['calle'] = $parametros['EmisorTimbre']['Calle'];
		$azurian['FiscalesEmisor']['noExterior'] = $parametros['EmisorTimbre']['NumExt'];
		$azurian['FiscalesEmisor']['colonia'] = $parametros['EmisorTimbre']['Colonia'];
		$azurian['FiscalesEmisor']['localidad'] = $parametros['EmisorTimbre']['Ciudad'];
		$azurian['FiscalesEmisor']['municipio'] = $parametros['EmisorTimbre']['Municipio'];
		$azurian['FiscalesEmisor']['estado'] = $parametros['EmisorTimbre']['Estado'];
		$azurian['FiscalesEmisor']['pais'] = $parametros['EmisorTimbre']['Pais'];
		$azurian['FiscalesEmisor']['codigoPostal'] = $parametros['EmisorTimbre']['CP'];
		/* Datos Regimen
		============================================================== */

		$azurian['Regimen']['Regimen'] = $parametros['EmisorTimbre']['RegimenFiscal'];

		/* Datos Receptor
		============================================================== */

		$azurian['Receptor']['rfc'] = strtoupper($parametros['Receptor']['RFC']);
		$azurian['Receptor']['nombre'] = strtoupper($parametros['Receptor']['RazonSocial']);

		/* Datos Domicilio Receptor
		============================================================== */

		$azurian['DomicilioReceptor']['calle'] = $parametros['Receptor']['Calle'];
		$azurian['DomicilioReceptor']['noExterior'] = $parametros['Receptor']['NumExt'];
		$azurian['DomicilioReceptor']['colonia'] = $parametros['Receptor']['Colonia'];
		$azurian['DomicilioReceptor']['localidad'] = $parametros['Receptor']['Ciudad'];
		$azurian['DomicilioReceptor']['municipio'] = $parametros['Receptor']['Municipio'];
		$azurian['DomicilioReceptor']['estado'] = $parametros['Receptor']['Estado'];
		$azurian['DomicilioReceptor']['pais'] = $parametros['Receptor']['Pais'];
		$azurian['DomicilioReceptor']['codigoPostal'] = $parametros['Receptor']['CP'];

		$conceptosOri = '';
		$conceptos = '';
		/*-----Factura de Consumo ---------*/
		if($consumo == 1 || $consumo=='1'){
			unset($conceptosDatos);
			$conceptosDatos[0]["Cantidad"] = 1;
			$conceptosDatos[0]["Unidad"] = "No Aplica";
			$conceptosDatos[0]["Precio"] = $precioSiniva;
			$conceptosDatos[0]["Descripcion"] = "Consumo de Alimentos y bebidas";
			$conceptosDatos[0]["Importe"] = $precioSiniva;

		}
		//se emepiza a llenar los conceptos en el arreglo de azurian
		foreach ($conceptosDatos as $key => $value) {
			$value['Descripcion'] = preg_replace("/'/", "&apos;", $value['Descripcion']);
			$value['Descripcion'] = preg_replace('/"/', "&quot;", $value['Descripcion']);
		   // $value['Descripcion'] = preg_replace('("|\')', "&apos;", $value['Descripcion']);
			$value['Descripcion'] = eregi_replace("[\n|\r|\n\r]", " ", $value['Descripcion']);
			$value['Descripcion'] = trim($value['Descripcion']);
			if($value['Unidad']==''){
				$value['Unidad']= "No Aplica";
			}
			$conceptosOri.='|' . $value['Cantidad'] . '|';
			$conceptosOri.=$value['Unidad'] . '|';
			$conceptosOri.=$value['Descripcion'] . '|';
			$conceptosOri.=str_replace(",", "", number_format($value['Precio'],2)) . '|';
			$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2));
			$conceptos.="<cfdi:Concepto cantidad='" . $value['Cantidad'] . "' unidad='" . $value['Unidad'] . "' descripcion='" . $value['Descripcion'] . "' valorUnitario='" . str_replace(",", "", number_format($value['Precio'],2)) . "' importe='" . str_replace(",", "", number_format($value['Importe'],2)) . "'/>";

			$subTotImportes += (float) str_replace(",", "", number_format($value['Importe'],2));
		}
		//////////impuestos azurian
		$ivas = '';
		$tisr = 0.00;
		$tiva = 0.00;
		$tieps = 0.00;

		$oriisr = '';
		$oriiva = '';

		$isr = '';
		$iva = '';
		$azurian['Conceptos']['conceptos'] = $conceptos;
		$azurian['Conceptos']['conceptosOri'] = $conceptosOri;

		$traslads = '';
		$retenids = '';
		$haytras = 0;
		$hayret = 0;
		$trasladsimp = 0.00;
		$retenciones = 0.00;
		$trasxml = '';
		$retexml = '';


		foreach ($nn as $clave => $imm) {
			if ($clave == 'IEPS' || $clave == 'IVA') {

				$haytras = 1;
				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
					if ($clave == 'IEPS') {
						$tieps+=number_format($val, 2, '.', '');
					}
					if ($clave == 'IVA') {
						$tiva+=number_format($val, 2, '.', '');
					}
					$traslads.='|' . $clave . '|';
				   // $traslads.='' . $clavetasa . '|';
					$traslads.='' . number_format($clavetasa,2) . '|';
					$traslads.=number_format($val, 2, '.', '');
					$trasladsimp+=number_format($val, 2, '.', '');
					$trasxml.="<cfdi:Traslado impuesto='" . $clave . "' tasa='" . number_format($clavetasa,2) . "' importe='" . number_format($val, 2, '.', '') . "' />";
				}
			} elseif ($clave == 'ISR' || $clave == 'IVAR') {
				$hayret = 1;

				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
				if($clave == 'IVAR'){
					$clave = substr($clave, 0, -1);
					$king = 1;
				}
					$tisr+=number_format($val, 2, '.', '');
					$retenids.='|' . $clave . '|';
					$retenidsT.='' . number_format($val, 2, '.', '') . '|';
					$retenids.=number_format($val, 2, '.', '');
					$retenciones+=number_format($val, 2, '.', '');
					$retexml.="<cfdi:Retencion impuesto='" . $clave . "' importe='" . number_format($val, 2, '.', '') . "' />";
					/*if($king ==1){
						$clave = 'IVAR';
						$king = 0;
					} */
				}
			}
		}////fin del foreach nn

		$azurian['Impuestos']['totalImpuestosIeps'] = $tieps;

		if ($haytras == 1) {
			$iva.='<cfdi:Traslados>' . $trasxml . '</cfdi:Traslados>';
		} else {
			$traslads.='|IVA|';
			$traslads.='0.00|';
			$traslads.='0.00';
			$trasladsimp = '0.00';
			$iva.="<cfdi:Traslados><cfdi:Traslado impuesto='IVA' tasa='0.00' importe='0.00' /></cfdi:Traslados>";
		}
		if ($hayret == 1) {
			$isr.='<cfdi:Retenciones>' . $retexml . '</cfdi:Retenciones>';
		}
		  if($hayret == 1){
			$cadRet = '|'.str_replace(',', '', number_format($tisr,2));
		  }else{
			$cadRet = '';
		  }

		  ///////Ajuste centavo
		/*echo 'SubImportes='.$subTotImportes.'<br>';
		echo 'SubAzurian='.$azurian['Basicos']['subTotal'].'<br>';
		echo 'totimpuestos='.$trasladsimp.'<br>';
		echo 'TotalAzurian='.$azurian['Basicos']['total'].'<br>';
		echo '('.$subTotImportes.'-'.$azurian['Basicos']['subTotal'].')<br>'; */

		$xsubT =  number_format($subTotImportes, 2, '.', '');
		$xsubA =  number_format($azurian['Basicos']['subTotal'], 2, '.', '');

		if($xsubT < $xsubA){
			$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] - 0.01;
			$trasladsimp = $trasladsimp + 0.01;
		}elseif($xsubT > $xsubA){
			if($trasladsimp > 0){
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
				$trasladsimp = $trasladsimp - 0.01;
			}else{
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
				$azurian['Basicos']['total'] = $azurian['Basicos']['total'] + 0.01;
			}

		}



		  $azurian['Impuestos']['isr'] = $retenids.$cadRet;
		  $azurian['Impuestos']['iva'] = $traslads . '|' . number_format($trasladsimp, 2, '.', '');

		  $azurian['Impuestos']['totalImpuestosRetenidos'] = number_format($retenciones, 2, '.', '');
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = number_format($trasladsimp, 2, '.', '');


	   /* echo 'SubImportes='.$subTotImportes.'<br>';
		echo 'SubAzurian='.$azurian['Basicos']['subTotal'].'<br>';
		echo 'totimpuestos='.$azurian['Impuestos']['totalImpuestosTrasladados'].'<br>';
		echo 'TotalAzurian='.$azurian['Basicos']['total'];
		exit();  */


		$ivas.=$isr . $iva;

		$azurian['Impuestos']['ivas'] = $ivas;
		/*print_r($azurian);
		//echo json_encode($azurian);
		exit(); */
		unset($_SESSION['pagos-caja']);
		unset($_SESSION['caja']);

		//ini_set('display_errors', 1);
        //error_reporting(E_ALL);
		//require_once('../../modulos/lib/nusoap.php');
		//require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/funcionesSAT2.php');
				else require_once('../webapp/modulos/SAT/funcionesSAT2_api.php');
			}
			else require $api_lite . "modulos/SAT/funcionesSAT2_api.php";
		}else if($pac==1){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])){
					require_once('../../modulos/lib/nusoap.php');
					require_once('../../modulos/SAT/funcionesSAT.php');
				}
				else{
					require_once('../webapp/modulos/lib/nusoap.php');
					require_once('../webapp/modulos/SAT/funcionesSAT_api.php');
				}
			}
			else{
				require_once($api_lite . 'modulos/lib/nusoap.php');
				require_once($api_lite . 'modulos/SAT/funcionesSAT_api.php');
			}
		}
		if(isset($_REQUEST["netwarstore"]) || isset($api_lite)) return $JSON;

	}//fin funcion facturar();
	public function pendienteFacturacion($idFacturacion, $monto, $cliente, $idventa, $trackId, $azurian, $documento) {

		$azurian = base64_encode($azurian);
		date_default_timezone_set("Mexico/General");
		$fechaactual = date('Y-m-d H:i:s');
		$tipo = ($documento = 2 ? 'F' : 'R');

		if (is_numeric($cliente)) {
			$query = "INSERT into app_pendienteFactura values(''," . $idventa . ",'" . $fechaactual . "'," . $cliente . ",'" . $monto . "',0,'" . $trackId . "','" . $azurian . "','" . $tipo . "','','2');";
			$resultquery = $this->queryArray($query);

			$referencia = 'Facturacion en espera';
			$query2 = "INSERT INTO app_pagos(cobrar_pagar,id_prov_cli,cargo,fecha_pago,concepto,id_forma_pago,id_moneda,tipo_cambio) values('0','".$cliente."','".str_replace(",", "", $monto)."','".$fechaactual."','Ticket Caja ".$idventa.":".$referencia."','6','1','1')";
							//echo $query;
			//$resCargo = $this->queryArray($query);

				//echo '1'.$query;
			return array("status" => true, "type" => 1);
		} else {
			$query = "INSERT into app_pendienteFactura values(''," . $idventa . ",'" . $fechaactual . "',NULL,'" . $monto . "',0,'" . $trackId . "','" . $azurian . "','" . $tipo . "','','2');";
				//echo '2'.$query;
			$resultquery = $this->queryArray($query);
			return array("status" => true, "type" => 2);
		}
	}
	public function factPendiente($id, $cliente, $obser,$seriex,$mpCat,$usoCfdi,$tipoRelacionCfdi,$cfdiUuidRelacion){
		//echo $id.'-'.$cliente.'-'.$obser;

		require_once('../../modulos/SAT/config.php');
		date_default_timezone_set("Mexico/General");
		$fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-10 minute"));
		$idVenta=$id;
		$rs =$this->queryArray("SELECT cadenaOriginal,factNum FROM app_pendienteFactura WHERE id_sale='$id';");

		/////Si es a credito y existe
		$sql = "SELECT	p.idFormapago, p.monto, v.monto montoV
				FROM	app_pos_venta v
				LEFT JOIN app_pos_venta_pagos p ON v.idVenta = p.idVenta
				WHERE v.idVenta = '$idVenta'; ";

		$formasPago = $this->queryArray($sql);

		$pagoTotal = 0;
		$pagoConCredito = 0;
		$otrasFormasPago = 0;
		foreach ($formasPago['rows'] as $key => $value) {
			$pagoTotal += (float) $value['monto'];
			if($value['idFormapago'] == 6)
				$pagoConCredito += (float) $value['monto'];
			else
				$otrasFormasPago += (float) $value['monto'];
		}
		if( $pagoConCredito != 0 ){

			$sql = "SELECT	CASE WHEN (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) IS NULL THEN cargo
		ELSE (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) END restante
								FROM	app_pagos  p
								RIGHT JOIN (
									select id
									FROM	app_pagos
									WHERE substring_index( substring_index(concepto,':',1) , ' ' , -1) = '$idVenta'
								) c ON p.id = c.id";

			$creditoPendiente = $this->queryArray($sql);

			$this->saldarCuenta( $idVenta, ($creditoPendiente['rows'][0]['restante'] ? $creditoPendiente['rows'][0]['restante'] : 0) );

		}

		////////////////////////////////////////////////////////////////////////////////////
			//AÑADIDO POR IVAN CUENCA
			//INICIA CONEXION CON ACONTIA
			//Si existe una venta comprueba si hay conexion con acontia
			if(intval($idVenta))
			{
				//Si se guardo la venta genera la poliza
				//Esta conectado a acontia?
				$conexion_acontia = $this->conexion_acontia();
				$conexion_acontia = $conexion_acontia->fetch_assoc();
				if(intval($conexion_acontia['conectar_acontia']))
				{
					$this->query("UPDATE cont_polizas SET activo = 0 WHERE origen = 'Venta' AND idorigen = $idVenta");
					$this->query("UPDATE cont_polizas SET activo = 0 WHERE origen LIKE 'Venta Pago%' AND idorigen = $idVenta");
					$this->generar_poliza(0,$fecha,$conexion_acontia,$idVenta,2,2);
					$this->generar_poliza_pagos(0,$fecha,$conexion_acontia,$idVenta,2,2);
				}
			}

			//TERMINA CONEXION CON ACONTIA
			////////////////////////////////////////////////////////////////////////////////////

		///////termina saldar cuenta
		///recuperar si tiene trackid
		$rastreo = $rs['rows'][0]['factNum'];
		if($rastreo!=0){
		   $consultaCFDI = 1;
		}
			$azurian=base64_decode($rs['rows'][0]['cadenaOriginal']);
			$azurian = str_replace("\\", "", $azurian);
			if($azurian!=''){ $azurian=json_decode($azurian); }
			$azurian = $this->object_to_array($azurian);

		if (isset($azurian['Basicos']['version'])){
    		$version = '3.2';
		}else{

			$this->pendienteFactura33($azurian, $cliente, $obser,$seriex,$mpCat,$usoCfdi,$tipoRelacionCfdi,$cfdiUuidRelacion);
			exit();
		}


		$result2 = "SELECT a.*, b.regimen as regimenf FROM pvt_configura_facturacion a INNER JOIN pvt_catalogo_regimen b WHERE a.id=1 AND b.id=a.regimen;";
		$rs2 = $this->queryArray($result2);

		////Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];

		/* DATOS OBLIGATORIOS DEL EMISOR
		================================================================== */
		$rfc_cliente=strtoupper($rs2['rows'][0]['rfc']);

		$parametros['EmisorTimbre'] = array();
		$parametros['EmisorTimbre']['RFC'] = strtoupper($rs2['rows'][0]['rfc']);
		$parametros['EmisorTimbre']['RegimenFiscal'] = strtoupper($rs2['rows'][0]['regimenf']);
		$parametros['EmisorTimbre']['Pais'] = $rs2['rows'][0]['pais'];
		$parametros['EmisorTimbre']['RazonSocial'] = $rs2['rows'][0]['razon_social'];
		$parametros['EmisorTimbre']['Calle'] = $rs2['rows'][0]['calle'];
		$parametros['EmisorTimbre']['NumExt'] = $rs2['rows'][0]['num_ext'];
		$parametros['EmisorTimbre']['Colonia'] = $rs2['rows'][0]['colonia'];
		$parametros['EmisorTimbre']['Ciudad'] = $rs2['rows'][0]['ciudad']; //Ciudad o Localidad
		$parametros['EmisorTimbre']['Municipio'] = $rs2['rows'][0]['municipio'];
		$parametros['EmisorTimbre']['Estado'] = $rs2['rows'][0]['estado'];
		$parametros['EmisorTimbre']['CP'] = $rs2['rows'][0]['cp'];
		$cer_cliente=$pathdc.'/'.$rs2['rows'][0]['cer'];
		$key_cliente=$pathdc.'/'.$rs2['rows'][0]['llave'];
		$pwd_cliente=$rs2['rows'][0]['clave'];

		if($rs2['rows'][0]['rfc']==''){

		  $JSON = array('success' =>0,
			'error'=>1001,
			'mensaje'=>'No existen datos de emisor.');
		  echo json_encode($JSON);
		  exit();

		}

		/* Datos Receptor
		============================================================== */
		if($cliente>0){
		  //$result = $this->conexion->consultar("SELECT * FROM comun_facturacion WHERE id='$rrfc';");
		  $result = "SELECT c.nombre,c.id, c.rfc, c.razon_social, c.correo, c.pais, c.regimen_fiscal, c.domicilio, c.num_ext, c.cp, c.colonia, e.estado, c.ciudad, c.municipio from comun_facturacion c , estados e WHERE e.idestado=c.estado and id='".$cliente."'";
		  $rs = $this->queryArray($result);
		  //print_r($rs);
		  $idCliente=$rs['rows'][0]['nombre'];
		  $azurian['Receptor']['rfc']=strtoupper($rs['rows'][0]['rfc']);
		  $azurian['Receptor']['nombre']=strtoupper($rs['rows'][0]['razon_social']);
		  $azurian['DomicilioReceptor']['calle']=$rs['rows'][0]['domicilio'];
		  $azurian['DomicilioReceptor']['noExterior']=$rs['rows'][0]['num_ext'];
		  $azurian['DomicilioReceptor']['colonia']=$rs['rows'][0]['colonia'];
		  $azurian['DomicilioReceptor']['localidad']=$rs['rows'][0]['ciudad'];
		  $azurian['DomicilioReceptor']['municipio']=$rs['rows'][0]['municipio'];
		  $azurian['DomicilioReceptor']['estado']=$rs['rows'][0]['estado'];
		  $azurian['DomicilioReceptor']['pais']=$rs['rows'][0]['pais'];
		  $azurian['DomicilioReceptor']['codigoPostal']=$rs['rows'][0]['cp'];
		  $azurian['Correo']['Correo'] = $rs['rows'][0]['correo'];

		}else{
		  $idCliente='';
		  $azurian['Receptor']['rfc']='XAXX010101000';
		  $azurian['Receptor']['nombre']='Factura generica';
		  $azurian['DomicilioReceptor']['calle']='';
		  $azurian['DomicilioReceptor']['noExterior']='';
		  $azurian['DomicilioReceptor']['colonia']='';
		  $azurian['DomicilioReceptor']['localidad']='';
		  $azurian['DomicilioReceptor']['municipio']='';
		  $azurian['DomicilioReceptor']['estado']='';
		  $azurian['DomicilioReceptor']['pais']='';
		  $azurian['DomicilioReceptor']['codigoPostal']='';
		  $azurian['Correo']['Correo'] = '';
		}

		/* --- Configuracion de las series  ---*/
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);

		if($resSer['rows'][0]['seriesFactura']==1){
			$result3 ="SELECT * FROM pvt_serie_folio WHERE id=".$seriex;
		}else{
			$result3 ="SELECT * FROM pvt_serie_folio WHERE id=1";
		}
		$rs3 = $this->queryArray($result3);

		$result4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
		$rs4 = $this->queryArray($result4);

		$azurian['org']['logo']        = $rs4['rows'][0]['logoempresa'];

		/* Moneda y Tipo de cambio */
        //$azurian['Basicos']['TipoCambio'] = 1.00;
        //$azurian['Basicos']['Moneda'] = 'MXN';

		/* Datos serie y folio
		============================================================== */
		$azurian['Basicos']['serie']=$rs3['rows'][0]['serie']; //No obligatorio
		$azurian['Basicos']['folio']=$rs3['rows'][0]['folio'];

		/* Datos Emisor
		============================================================== */
		$azurian['Emisor']['rfc']=strtoupper($parametros['EmisorTimbre']['RFC']);
		$azurian['Emisor']['nombre']=strtoupper($parametros['EmisorTimbre']['RazonSocial']);

		/* Datos Fiscales Emisor
		============================================================== */
		$azurian['FiscalesEmisor']['calle']=$parametros['EmisorTimbre']['Calle'];
		$azurian['FiscalesEmisor']['noExterior']=$parametros['EmisorTimbre']['NumExt'];
		$azurian['FiscalesEmisor']['colonia']=$parametros['EmisorTimbre']['Colonia'];
		$azurian['FiscalesEmisor']['localidad']=$parametros['EmisorTimbre']['Ciudad'];
		$azurian['FiscalesEmisor']['municipio']=$parametros['EmisorTimbre']['Municipio'];
		$azurian['FiscalesEmisor']['estado']=$parametros['EmisorTimbre']['Estado'];
		$azurian['FiscalesEmisor']['pais']=$parametros['EmisorTimbre']['Pais'];
		$azurian['FiscalesEmisor']['codigoPostal']=$parametros['EmisorTimbre']['CP'];

		/* Datos Regimen
		============================================================== */
		$azurian['Regimen']['Regimen']=$parametros['EmisorTimbre']['RegimenFiscal'];

		/* Fecha Factura
		============================================================== */
		$azurian['Basicos']['fecha']=$fecha;

		/* Impuestos
		============================================================== */
		$tisr=$azurian['Impuestos']['totalImpuestosRetenidos'];
		$tiva=$azurian['Impuestos']['totalImpuestosTrasladados'];
		$tieps=$azurian['Impuestos']['totalImpuestosIeps'];

		$azurian['Observacion']['Observacion']=$obser;


	/* Valida los sub totales
	============================================================== */

		$subTotImportes = 0;
		/*$sql_validacion ="	SELECT
								subtotal
							FROM
								app_pos_venta_producto
							WHERE
								idVenta = ".$idVenta;
		$result_validacion = $this->queryArray($sql_validacion);
		$result_validacion = $result_validacion['rows'];

	// Calcula el sub total
		foreach ($result_validacion as $key => $value) {
			$subTotImportes += $value['subtotal'];
		} */
			$xCon = $azurian['Conceptos']['conceptosOri'];
			$xCon = explode('|',$azurian['Conceptos']['conceptosOri']);
			$subTotImportes = 0;
			for($i = 5; $i < count($xCon); $i+=5) {
    			$subTotImportes+= $xCon[$i];

			}

		$traslads = '';
		$retenids = '';
		$haytras = 0;
		$hayret = 0;
		$trasladsimp = 0.00;
		$retenciones = 0.00;
		$trasxml = '';
		$retexml = '';

		foreach ($azurian['nn']['nn'] as $clave => $imm) {
			if ($clave == 'IEPS' || $clave == 'IVA') {
				$haytras = 1;

				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
					if ($clave == 'IEPS') {
						$tieps+=number_format($val, 2, '.', '');
					}
					if ($clave == 'IVA') {
						$tiva+=number_format($val, 2, '.', '');
					}
					$traslads.='|' . $clave . '|';
				   // $traslads.='' . $clavetasa . '|';
					$traslads.='' . number_format($clavetasa,2) . '|';
					$traslads.=number_format($val, 2, '.', '');
					$trasladsimp+=number_format($val, 2, '.', '');
					$trasxml.="<cfdi:Traslado impuesto='" . $clave . "' tasa='" . number_format($clavetasa,2) . "' importe='" . number_format($val, 2, '.', '') . "' />";
				}
			} elseif ($clave == 'ISR' || $clave == 'IVAR') {
				$hayret = 1;

				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
				if($clave == 'IVAR'){
					$clave = substr($clave, 0, -1);
					$king = 1;
				}
					$tisr+=number_format($val, 2, '.', '');
					$retenids.='|' . $clave . '|';
					$retenidsT.='' . number_format($val, 2, '.', '') . '|';
					$retenids.=number_format($val, 2, '.', '');
					$retenciones+=number_format($val, 2, '.', '');
					$retexml.="<cfdi:Retencion impuesto='" . $clave . "' importe='" . number_format($val, 2, '.', '') . "' />";
					// if($king ==1){
						// $clave = 'IVAR';
						// $king = 0;
					// }
				}
			}
		}////fin del foreach nn

		$xsubT =  number_format($subTotImportes, 2, '.', '');
		$xsubA =  number_format($azurian['Basicos']['subTotal'], 2, '.', '');
		if($xsubT < $xsubA){
			$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] - 0.01;
			$trasladsimp = $trasladsimp + 0.01;
		}elseif($xsubT > $xsubA){
			if($trasladsimp > 0){
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
				$trasladsimp = $trasladsimp - 0.01;
			}else{
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
				$azurian['Basicos']['total'] = $azurian['Basicos']['total'] + 0.01;
			}

		}


	/* FIN Valida los sub totales
	============================================================== */

		//require_once('../../modulos/lib/nusoap.php');
		//require_once('../../modulos/SAT/funcionesSAT.php');

		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT2.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT.php');
		}
	}

	public function rFac($uuid,$email,$cuerpoMsg){

		$selRes = "SELECT serieCsdEmisor from app_respuestaFacturacion where folio='".$uuid."'";
		$res = $this->queryArray($selRes);
		//echo '$'.$uuid;
		 require_once('../../modulos/phpmailer/sendMail.php');

			$mail->Subject = "Factura Generada";
			$mail->AltBody = "NetwarMonitor";
			$mail->MsgHTML($cuerpoMsg);

			if($res['rows'][0]['serieCsdEmisor']=='3'){
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $uuid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uuid .'.pdf');
			}else{
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $uuid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uuid .'.pdf');
			}

			$email = explode(';', $email);
			foreach ($email as $key => $value) {
				$mail->AddAddress($value, $value);
			}
			//$mail->AddAddress($email, $email);


			@$mail->Send();

			return  array('estatus' => true );

	}
	public function enviaCorteCaja($idCorte,$contenido){

		$selRes = "SELECT not_cortes from app_configuracion limit 1";
		$res = $this->queryArray($selRes);
		$email = $res['rows'][0]['not_cortes'];

		if($email!=''){
				require_once('../../modulos/phpmailer/sendMail.php');

				$mail->Subject = "Corte de Caja ".$idCorte;
				$mail->AltBody = "NetwarMonitor";
				$mail->MsgHTML($contenido);

				$mail->AddAttachment('../../modulos/pos/cortes/corte_'. $idCorte.'.pdf');
				$mail->AddAddress($email, $email);

				@$mail->Send();

			return  array('estatus' => true );
		}else{
			return  array('estatus' => false );
		}
		//echo '$'.$uuid;
	}

	public function envioFactura($uid, $Email, $azurian, $doc) {

		//$azurian=json_decode($azurian);
		if($Email=="muchasFac@gmail.com"){
			$azurian=json_decode($azurian);
			$azurian = $this->object_to_array($azurian);
			$azurian['nnf']['nnf'] = $azurian['nn']['nn'];
		}
		if($doc==10){
			$azurian=json_decode($azurian);
			$azurian = $this->object_to_array($azurian);
		}
		//$azurian=json_decode($azurian);
		//$azurian = $this->object_to_array($azurian);

		$datosTimbrado = $azurian['datosTimbrado'];
		//echo 'rrrrrrrrrr';
		//print_r($azurian['datosTimbrado']);
		if ($azurian['FiscalesEmisor']['noExterior'] == '') {
			$nemi = '';
		} else {
			$nemi = ' #' . $azurian['FiscalesEmisor']['noExterior'];
		}

		if ($azurian['DomicilioReceptor']['noExterior'] == '') {
			$nrec = '';
		} else {
			$nrec = ' #' . $azurian['DomicilioReceptor']['noExterior'];
		}
				//Obteniendo la descripcion de la forma de pago

		$idVenta = $azurian['datosTimbrado']['idVenta'];
		$formapago = "";

		$queryFormaPago = " SELECT nombre,referencia,claveSat from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;

		$resultqueryFormaPago = $this->queryArray($queryFormaPago);

		foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
			if (strlen($pagosValue["referencia"]) > 0) {
				//$formapago .= $pagosValue['nombre'] .'('.$pagosValue['claveSat'].')'." Ref:" . $pagosValue['referencia'] . ",";
				$formapago .= $pagosValue['nombre'] .'('.$pagosValue['claveSat'].')'.",";
				$refFormaPago = $pagosValue['referencia'];
				//$formapago .= $pagosValue['nombre'] . ",";
			} else {
				$formapago .= $pagosValue['nombre'] .'('.$pagosValue['claveSat'].')'.",";
				$refFormaPago = '';
			}
		}

		$formapago = substr($formapago, 0, strlen($formapago) - 1);

		if ($formapago == "") {
			$formapago = ".";
		}
		$versionFac = $this->versionFacturacion();



		if($versionFac == '3.3'){
			//echo $datosTimbrado['UUID'].'---';
			//$res = $this->pdf33($datosTimbrado['UUID']);
			//header("Location: http://localhost/mlog/webapp/modulos/cont/controllers/visorpdf.php?name=".$datosTimbrado['UUID'].".xml&logo=f_de_foodware.png&id=temporales&caja=1&nominas=1");
			//die();


		}else{



		include "../../modulos/SAT/PDF/CFDIPDF.php";

		$obj = new CFDIPDF( );

		if ($doc == 3) {
			$doc = "recibo";
		}
		if ($doc == 5) {
			$doc = "Honorarios";
			$azurian['Basicos']['tipoDeComprobante'] = "Honorarios";
		} else {
			$doc = "";
		}

	  	/*-- Adecuaciones Canacintra --*/
	  	/* --- Configuracion de las series  ---*/
	  	$codigo = '';
		$telefono = '';
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);
		if($resSer['rows'][0]['seriesFactura']==1){
			///Se obtiene codigo y telefono del cliente
		  	$selr = 'SELECT nombre from comun_facturacion where rfc="'.$azurian['Receptor']['rfc'].'"';


		  	$res = $this->queryArray($selr);

		  	$select2 = 'SELECT codigo,telefono1 from comun_cliente where id='.$res['rows'][0]['nombre'];
		  	$cliente = $this->queryArray($select2);
		  	$codigo = $cliente['rows'][0]['codigo'];
		  	$telefono = $cliente['rows'][0]['telefono1'];

		  	///Se obtiene el codigo de los productos
		  	$selProd = 'SELECT p.codigo from app_productos p, app_pos_venta_producto pr where p.id=pr.idProducto and pr.idVenta='.$idVenta;
		  	$cods = $this->queryArray($selProd);

		  	$cadena=substr($azurian['Conceptos']['conceptosOri'],1);
			$conceptos=explode("|",$cadena);
			$cadNew = '';
			$x = 2;
			$conta = 0;
			foreach ($conceptos as $key => $value) {
				if($key == $x){
					$cadNew .=$cods['rows'][$conta]['codigo'].'|'.$value.'|';
					$x = $x+5;
					$conta++;
				}else{
					$cadNew .=$value.'|';
				}

			}
			$azurian['Conceptos']['conceptosOri'] = $cadNew;
		}


		$azurian['Conceptos']['conceptosOri'] = preg_replace('/&apos;/', "'", $azurian['Conceptos']['conceptosOri']);
		$azurian['Conceptos']['conceptosOri'] = preg_replace('/&quot;/', '"', $azurian['Conceptos']['conceptosOri']);
			//$obj->ponerColor('#333333');
		$obj->datosCFD($datosTimbrado['UUID'], $azurian['Basicos']['serie'] . ' ' . $azurian['Basicos']['folio'], $datosTimbrado['noCertificado'], $datosTimbrado['FechaTimbrado'], $datosTimbrado['FechaTimbrado'], $datosTimbrado['noCertificadoSAT'], $azurian['Basicos']['formaDePago'], $azurian['Basicos']['tipoDeComprobante'], $doc,$azurian['Basicos']['Moneda'],$azurian['Basicos']['TipoCambio'],$resSer['rows'][0]['seriesFactura']);
		$obj->lugarE($azurian['Basicos']['LugarExpedicion']);
		$obj->datosEmisor($azurian['Emisor']['nombre'], $azurian['Emisor']['rfc'], $azurian['FiscalesEmisor']['calle'] . $nemi, $azurian['FiscalesEmisor']['localidad'], $azurian['FiscalesEmisor']['colonia'], $azurian['FiscalesEmisor']['municipio'], $azurian['FiscalesEmisor']['estado'], $azurian['FiscalesEmisor']['codigoPostal'], $azurian['FiscalesEmisor']['pais'], $azurian['Regimen']['Regimen']);
		$obj->datosReceptor($azurian['Receptor']['nombre'], $azurian['Receptor']['rfc'], $azurian['DomicilioReceptor']['calle'] . $nrec, $azurian['DomicilioReceptor']['localidad'], $azurian['DomicilioReceptor']['colonia'], $azurian['DomicilioReceptor']['municipio'], $azurian['DomicilioReceptor']['estado'], $azurian['DomicilioReceptor']['codigoPostal'], $azurian['DomicilioReceptor']['pais'],$codigo,$telefono);
		$obj->agregarConceptos($azurian['Conceptos']['conceptosOri'],$resSer['rows'][0]['seriesFactura']);
		$obj->agregarTotal($azurian['Basicos']['subTotal'], $azurian['Basicos']['total'], $azurian['nnf']['nnf'],$azurian['Basicos']['descuento']);
		$obj->agregarMetodo($formapago, $refFormaPago, $azurian['Basicos']['Moneda']);
		$obj->agregarSellos($datosTimbrado['csdComplemento'], $datosTimbrado['selloCFD'], $datosTimbrado['selloSAT']);
		$obj->agregarObservaciones($azurian['Observacion']['Observacion']);
		$obj->generar("../../netwarelog/archivos/1/organizaciones/" . $azurian['org']['logo'] . "", 0);
		$obj->borrarConcepto();
		$queryIdReceptor = "SELECT nombre from comun_facturacion where rfc='".$azurian['Receptor']['rfc']."' order by nombre desc";
		$resultOne = $this->queryArray($queryIdReceptor);

		/*$queryCupon = "SELECT cupon from comun_cliente_inadem where idCliente=".$resultOne['rows'][0]['nombre'];
		if($this->queryArray($queryCupon)){
			$resultTwo = $this->queryArray($queryCupon);
			$cuponInadem = $resultTwo['rows'][0]['cupon'];
		}else{
		   $resultTwo = '';
		   $cuponInadem = '';
		}  */
		$selRes = "SELECT serieCsdEmisor from app_respuestaFacturacion where folio='".$uid."'";
		$res = $this->queryArray($selRes);
	  }

		$cuponInadem = '';
		if ($Email != '') {

			require_once('../../modulos/phpmailer/sendMail.php');

			$mail->Subject = "Factura Generada";
			$mail->AltBody = "NetwarMonitor";
			$mail->MsgHTML('Factura Generada');
			if($res['rows'][0]['serieCsdEmisor']=='3'){
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $uid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uid .'.pdf');
			}else{
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $uid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uid .'.pdf');
			}

			$Email = explode(';', $Email);
			foreach ($Email as $key => $value) {
				$mail->AddAddress($value, $value);
			}
			//$mail->AddAddress($Email, $Email);


			@$mail->Send();
		}
		//echo 'jerjdjdfjfjfjf';
		//$cuponInadem='';
	   if($cuponInadem ==null || $cuponInadem==''){

		return array("status" => true, "receptor" => str_replace(' ','_',$azurian['Receptor']['nombre']), "cupon" => false);
	   }else{
		return array("status" => true, "receptor" => str_replace(' ','_',$azurian['Receptor']['nombre']), "cupon" => $cuponInadem);
	   }
	}

	public function origenPac($id){
		$selRes = "SELECT serieCsdEmisor, tipoComp from app_respuestaFacturacion where folio='".$id."'";
		$res = $this->queryArray($selRes);
		if($res['rows'][0]['serieCsdEmisor']=='3' || $res['rows'][0]['tipoComp']=='P'){
			return array('pac' => 'formas' );
		}else{
			return array('pac' => 'azurian');
		}
	}

	public function guardarFacturacion($UUID, $noCertificadoSAT, $selloCFD, $selloSAT, $FechaTimbrado, $idComprobante, $idFact, $idVenta, $noCertificado, $tipoComp, $monto, $cliente, $trackId, $idRefact, $azurian, $estatus,$seriex) {
		$azu = $azurian;
		$azu = str_replace("\\", "", $azu);
		if($azu!=''){
			$azu=json_decode($azu);
		}
		$azu = $this->object_to_array($azu);

		$azurian = base64_encode($azurian);
		$fechaactual = preg_replace('/T/', ' ', $FechaTimbrado);
		if ($idRefact == 'c') {
			$tipoComp = 'C';
			$queryRespuesta = "UPDATE app_respuestaFacturacion SET borrado=2 WHERE idSale='$idVenta'";
			$this->queryArray($queryRespuesta);
		}

		if (isset($azu['Basicos']['version'])){
    		$version = '3.2';
    		//print_r($azu['Basicos']);
    		//echo '3.2';
    		$folioDeFactura = $azu['Basicos']['folio'];

		}else{
			//echo '3.3';
			$version = "4.0";
			$folioDeFactura = $azu['Basicos']['Folio'];
		}

		if($idVenta> 0){
			if($azu['Basicos']['Moneda']=='MXN'){
				$queryEnvioz = "UPDATE app_pos_venta set monto='".$azu['Basicos']['Total']."' where idVenta=".$idVenta;
				$this->queryArray($queryEnvioz);
				$queryEnvioz2 = "UPDATE app_pos_venta_pagos set monto='".$azu['Basicos']['Total']."' where idVenta=".$idVenta;
				$this->queryArray($queryEnvioz2);
			}
			
		}
		

		$insertRespuestaFacturacion = "INSERT INTO app_respuestaFacturacion "
		. "(idSale,idFact,folio,factNum,serieCsdSat,serieCsdEmisor,selloDigitalSat,selloDigitalEmisor,fecha,borrado,tipoComp,idComprobante,cadenaOriginal,xmlfile,origen,version) VALUES "
		. "('" . $idVenta . "','" . $idFact . "','" . $UUID . "','" . $trackId . "','" . $noCertificadoSAT . "','" . $noCertificado . "','" . $selloSAT . "','" . $selloCFD . "','" . $fechaactual . "',0,'" . $tipoComp . "','" . $idComprobante . "','" . $azurian . "','" . $UUID . ".xml','2','".$version."');";


		$resultInsert = $this->queryArray($insertRespuestaFacturacion);
		$insertedId = $resultInsert["insertId"];

		if (is_numeric($insertedId)) {
			$queryUpdateContador = "UPDATE pvt_contadorFacturas set total=total+1 where id=1";
			$this->queryArray($queryUpdateContador);

			$ContadorLicencias = "UPDATE comun_parametros_licencias set valor=valor-1 where parametro='Facturas'";
			$this->queryArray($ContadorLicencias);

			/* --- Configuracion de las series  ---*/
			$selSer = "SELECT seriesFactura from app_config_ventas";
			$resSer = $this->queryArray($selSer);

			if ($tipoComp == "R") {
				$queryUpdateFolo = "UPDATE pvt_serie_folio SET folio_r=folio_r+1 where id=1";
			}else if($tipoComp == "H"){
				$queryUpdateFolo = "UPDATE pvt_serie_folio SET folio_h=folio_h+1 where id=1";
			}else {
				if($resSer['rows'][0]['seriesFactura']==1){
					$queryUpdateFolo = "UPDATE pvt_serie_folio SET folio=folio+1 where id=".$seriex;
				}else{
					$queryUpdateFolo = "UPDATE pvt_serie_folio SET folio=folio+1 where id=1";
				}

			}
			$this->queryArray($queryUpdateFolo);

		}

		$porventa = 1;
		if (preg_match('/all/', $idRefact)) {
			$idRefact = preg_replace('/all/', '', $idRefact);
			$idRefact = trim($idRefact,',');
			$updatePendienteFactura = "UPDATE app_pendienteFactura SET facturado=1, id_respFact='".$insertedId."' WHERE id_sale in (" . $idRefact . ")";
			//echo $updatePendienteFactura;
			$this->queryArray($updatePendienteFactura);
			$porventa = 0;
			/////Pone el Folio de la Factura en la venta
			$upF = 'UPDATE app_pos_venta set factura="'.$folioDeFactura.'" where idVenta in ('.$idRefact.')';
			//echo $upF;
			$resUf = $this->queryArray($upF);
		}

		if ($idRefact > 0 && $idRefact != 'c') {
			$updatePendienteFactura = "UPDATE app_pendienteFactura SET facturado=1 WHERE id_sale='$idRefact'";
			$this->queryArray($updatePendienteFactura);
			/////Pone el Folio de la Factura en la venta
			$upF = 'UPDATE app_pos_venta set factura="'.$folioDeFactura.'" where idVenta="'.$idRefact.'"';
			$resUf = $this->queryArray($upF);
			//echo $upF;
		}
		$queryEnvio = "UPDATE app_pos_venta set envio=2 where idVenta=".$idVenta;
		$this->queryArray($queryEnvio);

		//////Insert en cont_facturas
		//$UUID = '1A16D7FD-883E-460C-B32B-53902A1096F5';
		/*$xmlx = $UUID .'.xml';
		include("../../libraries/xml2json/xml2json.php");
		$cont_xml = simplexml_load_file('../facturas/temporales/'. $UUID .'.xml');
      	$cont_array = xmlToArray($cont_xml);
       	$json = utf8_encode(json_encode($cont_array,JSON_UNESCAPED_UNICODE));
       	$json = str_replace("\\", "", $json);*/
       	$json = '';

		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
		$fechaFac = str_replace('T', ' ', $azu['Basicos']['fecha']);

		if (isset($azu['Basicos']['version'])){
    		$inseCon = "INSERT into cont_facturas(folio,uuid,er,tipo,serie,emisor,receptor,importe,moneda,rfc,fecha,fecha_subida,xml,version,cancelada,json,temporal,origen) values('".$azu['Basicos']['folio']."','".$UUID."','E','Ingreso','".$azu['Basicos']['serie']."','".$azu['Emisor']['nombre']."','".$azu['Receptor']['nombre']."','".$azu['Basicos']['total']."','".$azu['Basicos']['Moneda']."','".$azu['Receptor']['rfc']."','".$fechaFac."','".$fechaactual."','".$xmlx."','".$version."','0','".$json."','1',1)";
		}else{

			$inseCon = "INSERT into cont_facturas(folio,uuid,er,tipo,serie,emisor,receptor,importe,moneda,rfc,fecha,fecha_subida,xml,version,cancelada,json,temporal,origen) values('".$azu['Basicos']['Folio']."','".$UUID."','E','Ingreso','".$azu['Basicos']['Serie']."','".$azu['Emisor']['Nombre']."','".$azu['Receptor']['Nombre']."','".$azu['Basicos']['Total']."','".$azu['Basicos']['Moneda']."','".$azu['Receptor']['Rfc']."','".str_replace('T', ' ', $azu['Basicos']['Fecha'])."','".$fechaactual."','".$xmlx."','".$version."','0','".$json."','1',1)";
		}
		$this->queryArray($inseCon);

		////////////////////////////////////////////////////////////////////////////////////
			//AÑADIDO POR IVAN CUENCA
			//INICIA CONEXION CON ACONTIA
				//Esta conectado a acontia?
				/*$IdPoliza = 0;
				$conexion_acontia = $this->conexion_acontia();
				$conexion_acontia = $conexion_acontia->fetch_assoc();
				if(intval($conexion_acontia['conectar_acontia']))
				{
					if($porventa)
					{
						$myQuery = "SELECT m.Id, m.IdPoliza , p.idtipopoliza 
											  FROM cont_movimientos m 
											  INNER JOIN cont_polizas p ON p.id = m.IdPoliza 
											  WHERE p.origen LIKE 'Venta%' AND p.idorigen = $idVenta  AND p.activo = 1 
											  AND m.Activo = 1;";
					}
					else
					{
						$myQuery = "SELECT m.Id, m.IdPoliza, p.idtipopoliza 
											  	FROM cont_movimientos m 
											  	INNER JOIN cont_polizas p ON p.id = m.IdPoliza 
											  	WHERE p.origen = 'Varias Ventas' AND p.idorigen = 0  AND p.activo = 1 
												AND m.Activo = 1 AND m.IdVenta IN ($idRefact);";
					}
					$InfoMov = $this->query($myQuery);
					$ids_movs = '';
					$arrPol = array();
					$tipopol = array();
					$polAnt = '';//Poliza anterior
					while($IM = $InfoMov->fetch_assoc())
					{
						if($polAnt != $IM['IdPoliza']){
							array_push($arrPol,$IM['IdPoliza']);
							array_push($tipopol,$IM['idtipopoliza']);
						}
						$ids_movs .= $IM['Id'].",";
						$polAnt = $IM['IdPoliza'];
					}

					for($i=0;$i<=count($arrPol)-1;$i++)
					{
						$IdPoliza = $arrPol[$i];
						if($tipopol[$i] == 3)
							$tipo = 0;
						else
							$tipo = 1;
						$ruta  = "../cont/xmls/facturas/";//Ruta donde se copiara
						if($this->anexarFactura($IdPoliza,"$UUID.xml",$ruta,$ids_movs,$insertedId))
						{
							$this->query("UPDATE cont_polizas SET referencia = 'Folio Factura: $folio_fac' WHERE id = $IdPoliza");
							//$this->query("UPDATE cont_polizas SET referencia = 'Id Factura: $insertedId' WHERE id = $IdPoliza");
							$this->query("UPDATE cont_movimientos SET Referencia = '$UUID.xml', Factura = '$UUID.xml' WHERE IdPoliza = $IdPoliza");

							//INSERTA RELACION DE FACTURA Y POLIZA----------

								$myQuery = "INSERT INTO cont_facturas_poliza(uuid,tipo_poliza,id_poliza,activo) 
									SELECT * FROM (SELECT '$UUID' as xml, $tipo as tipo, $IdPoliza as idpoliza,1 as activo) AS tmp 
									WHERE NOT EXISTS (SELECT uuid FROM cont_facturas_poliza WHERE uuid = '$UUID' AND id_poliza = $IdPoliza) LIMIT 1;";
								$this->query($myQuery);		
							//FIN-------------------------------------------
						}
					}
				}*/
			//TERMINA CONEXION CON ACONTIA
			////////////////////////////////////////////////////////////////////////////////////

		return $insertedId;
	}
	public function datosorganizacion(){
		$selectOrg = "SELECT * from organizaciones c left join estados e on e.idestado=c.idestado left join municipios m on m.idmunicipio=c.idmunicipio where idorganizacion=1";
		$resultSelect = $this->queryArray($selectOrg);
		return $resultSelect['rows'];
	}
	public function datosSucursal($idVenta){

		$sel1 = "SELECT idSucursal from app_pos_venta where idVenta=".$idVenta;
		$res1 = $this->queryArray($sel1);
		$idSuc = $res1['rows'][0]['idSucursal'];

		$select = "SELECT * from mrp_sucursal s left join estados e on e.idestado=s.idEstado left join municipios m on m.idmunicipio=s.idMunicipio  where idSuc=".$idSuc;
		$res2 = $this->queryArray($select);


		return $res2['rows'];
	}
	public function datosventa($idventa){
		$selectVenta = "SELECT
		v.idVenta as folio,
		v.fecha as fecha,
		v.cambio as cambio,
		v.impuestos as jsonImpuestos,
		v.descuentoGeneral as descuento,
		CASE WHEN c.nombre IS NOT NULL
			   THEN c.nombre
			   ELSE 'Publico general'
		END AS cliente,
		v.idCliente as idCliente,
		c.email emailCliente,
		e.nombre as empleado,
		s.nombre as sucursal,
		CASE WHEN v.estatus =1
			   THEN 'Activa'
			   ELSE 'Cancelada'
		END AS estatus,
		v.montoimpuestos as impuestos,
		(v.monto) as monto,
		m.description,
		m.codigo,
		v.folio_recibo as recibo,
		v.tipo_cambio, tr.puntos ,
		c.usoCFDI,
		c.email,
		c.rfc
		 from app_pos_venta v left join comun_cliente c on c.id=v.idCliente left join cont_coin m on m.coin_id=v.moneda inner join  empleados e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal
		 left join tarjeta_regalo tr on tr.cliente = c.id
		 where v.idVenta=".$idventa;
		$resutl = $this->queryArray($selectVenta);
		return $resutl['rows'];
	}

	public function datoscliente($idCliente){
		$selectCte = "SELECT
				f.rfc as rfc,
				f.nombre as clave,
				f.pais as pais,
				f.ciudad as ciudad,
				c.direccion as direccion,
				c.colonia as colonia,
				c.cp as cp,
                e.estado as estado
			from comun_facturacion f inner join comun_cliente c On c.id = f.nombre Inner Join estados e On c.idEstado = e.idestado
			where f.nombre=".$idCliente;
		$resutl = $this->queryArray($selectCte);
		return $resutl['rows'];
	}
	function obtenerLeyenda() {

        $sql = "SELECT  leyenda_ticket
                FROM    app_config_ventas
                WHERE   id=1";
        $res = $this->queryArray($sql);
        return $res['rows'][0]['leyenda_ticket'];
    }


	public function productosventa($idVenta){
		//$idVenta = 158;
		$sucursal = $this->sucursal();
		$sql = "SELECT consumoTicket from com_configuracion where id_sucursal =".$sucursal;
		$result = $this->queryArray($sql);
		$consumo = $result['rows'][0]['consumoTicket'];

		// consulta para englobar consumo
		if($consumo == 1){
			$sql = "SELECT  'Consumo' nombre,
							'Consumo' descripcion_corta, 
							sum(p.precio) precio,
							1 id,
							1 codigo,
							sum(vp.preciounitario)  preciounitario,
							1 cantidad,
							0 montodescuento,
							sum(vp.total) total,							
							'' impuestosproductoventa, 
							'' comentario , 
							'' caracteristicas, 
							'' tipodescuento,
							'' descuento,
						    '' idventa_producto, 
							'' series, 
							'' devoluciones,
							1 consumo							 
					FROM app_pos_venta_producto vp
					LEFT JOIN app_productos p ON vp.idProducto=p.id
					LEFT JOIN app_campos_foodware f ON p.id=f.id_producto
					LEFT JOIN app_devolucioncli_datos d ON	vp.idventa_producto = d.id_producto
					WHERE vp.idVenta=".$idVenta." AND vp.comentario != 'omitir'
					GROUP BY vp.idVenta;";

			$resSel = $this->queryArray($sql);
			return $resSel['rows'];	

		}else{

			$selProd = "	SELECT
									IF(vp.comentario!='', CONCAT(p.nombre, vp.comentario),
										IF(f.descripcion!='', CONCAT(p.nombre, f.descripcion), p.nombre))
									AS nombre, p.descripcion_corta,p.precio,
									vp.idProducto as id, p.codigo, vp.preciounitario, vp.cantidad, vp.montodescuento, vp.total,
									vp.impuestosproductoventa, vp.comentario , vp.caracteristicas, vp.tipodescuento,vp.descuento, vp.idventa_producto, vp.series, COUNT(d.id) devoluciones, 0 consumo
								FROM
									app_pos_venta_producto vp
								LEFT JOIN app_productos p ON vp.idProducto=p.id
								LEFT JOIN app_campos_foodware f ON p.id=f.id_producto
								LEFT JOIN app_devolucioncli_datos d ON	vp.idventa_producto = d.id_producto
								WHERE
									vp.idVenta=".$idVenta." AND vp.comentario != 'omitir'
								GROUP BY vp.idventa_producto";
				//print_r($selProd);
				$resSel = $this->queryArray($selProd);
				$caras = '';
				$seriesNombre = '';
				//print_r($resSel['rows']);
				//exit();
				foreach ($resSel['rows'] as $k => $v) {
				   // echo '['.$k.']<br>';
					if($v['caracteristicas']!="'0'"){
						$caracteristicas2 =  explode("*", $v['caracteristicas']);
						foreach ($caracteristicas2 as $key => $value) {
							$expv=explode('=>', $value);
							$ip=$expv[0];
							$ih=$expv[1];
							$my = "SELECT concat('( ',a.nombre,': ',b.nombre,' )') as dcar FROM app_caracteristicas_padre a
							LEFT JOIN app_caracteristicas_hija b on b.id=".$ih."
							WHERE a.id=".$ip.";";
							$producto = $this->queryArray($my);
							//echo $producto['rows'][0]['dcar'].'<br>';
							$caras.= $producto['rows'][0]['dcar'];
						}
						$resSel['rows'][$k]['nombre'] = $resSel['rows'][$k]['nombre'].$caras;
						if($resSel['rows'][$k]['descripcion_corta']!=''){
							$resSel['rows'][$k]['descripcion_corta'] = $resSel['rows'][$k]['descripcion_corta'].$caras;
						}else{
							$resSel['rows'][$k]['descripcion_corta'] = '';
						}

						$caras = '';
					}
					//echo $v['series'];
					if($v['series']!=''){

						$v['series'] = explode(',',$v['series']);
						foreach ($v['series'] as $keySeries => $valueSeries) {
							$selSerie = "SELECT serie from app_producto_serie where id=".$valueSeries;
							$resSelSerie = $this->queryArray($selSerie);
							//echo $resSelSerie['rows'][0]['serie'].'<br>';
							if($resSelSerie['rows'][0]['serie']!=''){
								$seriesNombre.=$resSelSerie['rows'][0]['serie'].',';
							}
						}
						$seriesNombre = '['.$seriesNombre.']';
						$resSel['rows'][$k]['nombre'] = $resSel['rows'][$k]['nombre'].$seriesNombre;
						if($resSel['rows'][$k]['descripcion_corta']!=''){
							$resSel['rows'][$k]['descripcion_corta'] = $resSel['rows'][$k]['descripcion_corta'].$seriesNombre;
						}else{
							$resSel['rows'][$k]['descripcion_corta'] = '';
						}


					}
				}
				//echo $seriesNombre;
				//exit();


				 //print_r($resSel['rows']);
				//echo $caras;
				return $resSel['rows'];


		}


		
	}
	public function pagos($idVenta){
		$selectPagos = "SELECT vp.monto, fp.nombre from app_pos_venta_pagos vp inner join app_pos_venta v on v.idVenta=vp.idVenta inner join forma_pago fp on vp.idFormapago=fp.idFormapago where v.idVenta=".$idVenta;

		$resPagos = $this->queryArray($selectPagos);
		return $resPagos['rows'];
	}
	public function getSucursales(){
		$selcSuc = "SELECT * from mrp_sucursal where activo = -1";
		$resSel = $this->queryArray($selcSuc);
		return $resSel['rows'];
	}
	public function eliminaProducto($idProducto,$idpedidoComanda){
		unset($_SESSION['caja'][$idProducto]);
		//print_r($_SESSION['caja']);
		//exit();
		$sessionArray = $this->object_to_array($_SESSION['caja']);

		$productosTotal = 0;
		foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				$stringTaxes .=$value['idProducto'].'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'-'.$value['caracteristicas'].'/';
				$productosTotal += $value['cantidad'];
			}
		}

		$this->calculaImpuestos($stringTaxes);
	   // print_r($_SESSION['caja']);

		if($idpedidoComanda > 0){
			//$sql = "UPDATE com_pedidos set status = 3 where id = ".$idpedidoComanda.";";
			$sql = "DELETE FROM com_pedidos where id = ".$idpedidoComanda.";";
			$res = $this->queryArray($sql);
		}

		return array('estatus' =>true,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'],"count" => count($_SESSION['caja']), 'totalProductos' => $productosTotal);

	}
	public function cancelarCaja() {
		  unset($_SESSION['caja']);
		  unset($_SESSION['pagos-caja']);

		  return true;
	}
	public function eliminaDescuento(){
		//print_r($_SESSION['caja']);

		//echo '('.$_SESSION['caja']['descGeneral'].')';
		$stringTaxes = '';
		$y1 = 0;
		$_SESSION['caja']['cargos']['total'];

		$_SESSION['caja']['cargos']['descGeneral'] = 100;
		$sessCaja = $this->object_to_array($_SESSION['caja']);
		foreach ($sessCaja as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				/*if($x == 0){
					$value['precio'] = 0;
				} */
				$desc = $value['precio'];

				$value['precio'] =  $value['precio'];

				$stringTaxes .=$key.'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'/';
				$productosTotal += $value['cantidad'];
			}
		}
		session_start();
		$_SESSION['caja']['descGeneral']= $y1;

		//print_r($_SESSION['caja']['cargos']);
		//echo 'dddd'.$_SESSION['caja']['cargos']['descGeneral'];
		$this->calculaImpuestos($stringTaxes);
				////regresa los productos en orden de incersion
        $ar = $_SESSION['caja'];
        $nar=array();
        foreach ($ar as $key => $value) {
            $nar[$key.'+']=$ar[$key];
        }
        $nar = array_reverse($nar);


		return array('estatus' =>true,'productos' =>$nar, 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal, 'descGeneral' => $y1);


	}
	public function guardaTIDPe($trackId,$id){
		$upd = "UPDATE app_pendienteFactura set factNum='".$trackId."' where id_sale=".$id;
		$res = $this->queryArray($upd);

		return 1;
	}
	public function suspenderVenta($idFact, $doc, $cliente, $nombre, $suspendida) {

		try {
			  date_default_timezone_set("Mexico/General");
			  $fechaactual = date("Y-m-d H:i:s");

			  $monto = str_replace(",", "", $_SESSION["caja"]["cargos"]["total"]);
			  $cambio = str_replace(",", "", $_SESSION['pagos-caja']["cambio"]);
			  foreach ($_SESSION["caja"]["cargos"]["impuestos"] as $key => $value) {
				$impuestos+=$value;
			}
			$almacen = $this->obtenAlm();
			$sucursal = $_SESSION["sucursal"];
			$empleado = $_SESSION['accelog_idempleado'];


			$arr = json_encode((object) $_SESSION['caja']);
			if (isset($_SESSION['pagos-caja'])) {
				$arr2 = json_encode((object) $_SESSION['pagos-caja']);
			} else {
				$arr2 = '{"pagos":{},"Abonado":"0.00","porPagar":"0.00","cambio":"0.00"}';
			}

			if ($idFact == '') {
				$idFact = 0;
			}
			if($cliente==''){
				$cliente = 0;
			}
				//Guardamos la venta suspendida
			$insertVentaSuspendida = "INSERT INTO app_pos_venta_suspendida (s_almacen,s_cambio,s_cliente,s_documento,s_empleado,s_funcion,s_idFact,s_impuestos,s_monto,s_pagoautomatico,s_sucursal,s_impuestost,arreglo1,arreglo2,identi,fecha) VALUES "
			. "('" . $almacen . "','" . $cambio . "','" . $cliente . "','" . $doc . "'," . $empleado . ",'suspenderVenta','" . $idFact . "','" . $impuestos . "','" . $monto . "',0,'" . $sucursal . "','" . $impuestos . "','" . $arr . "','" . $arr2 . "','" . $nombre . " - " . $fechaactual . " - $" . $monto . "','" . $fechaactual . "');";

			$resultinsertVentaSuspendida = $this->queryArray($insertVentaSuspendida);


			if (!$resultinsertVentaSuspendida["status"]) {
				throw new Exception("Error al suspender la venta.");
			}

			/*foreach ($_SESSION['caja'] as $key => $value) {
				if ($key != 'cargos') {
					$value = (object) $value;
					$updateStock = "UPDATE mrp_stock "
					. "SET cantidad=cantidad-" . $value->cantidad . " "
					. "WHERE idProducto='$key' AND idAlmacen='$almacen'";

					$resultUpdateStock = $this->queryTrans($updateStock);

					if (!$resultUpdateStock["status"]) {
						throw new Exception("Error al actualizar el stock.");
					}
				}
			} */

			unset($_SESSION['caja']);
			unset($_SESSION['pagos-caja']);
		   // $this->commit();
			//$this->eliminarSuspendida($suspendida);
			return array("status" => true ,"idSuspendido" => $resultinsertVentaSuspendida['insertId'] );
		} catch (Exception $e) {
			//$this->rollback();
			return array("status" => false, "msg" => $e->getMessage());
		}
	}
/*    public function eliminarSuspendida($id) {

		try {

			$this->iniTrans();
			$datosSuspendida = "SELECT arreglo1, s_almacen from app_pos_venta_suspendida where id='$id' ";

			$resultDatos = $this->queryTrans($datosSuspendida);

			if ($resultDatos["rows"][0] != '') {
				$json = str_replace("\"\"", null, $resultDatos["rows"]["arreglo1"][0]);
				$json = str_replace("\\", null, $json);
				$json = json_decode($json, true);

				$almacen = $resultDatos["rows"]["s_almacen"][0];

				foreach ($json as $key => $value) {

					if ($key != 'cargos') {

						$updateStock = "UPDATE mrp_stock "
						. "SET cantidad=cantidad+" . $value['cantidad'] . " "
						. "WHERE idProducto='$key' AND idAlmacen='$almacen'";


						$resultUpdateStock = $this->queryTrans($updateStock);

						if (!$resultUpdateStock["status"]) {
							throw new Exception("Error al actualizar el stock.");
						}
					}
				}
			} else {
				throw new Exception("Ocurrio un error al consultar los datos de la venta.");
			}

			$eliminarSuspendida = "Delete from app_pos_venta_suspendida where id =" . $id;

			$resutEliminaSuspendida = $this->queryTrans($eliminarSuspendida);

			if (!$resutEliminaSuspendida["status"]) {
				throw new Exception("No se pudo eliminar la venta suspendida.");
			}

			$this->commit();
			return array("status" => true);
		} catch (Exception $e) {
			$this->rollback();
			return array("status" => false, "msg" => $e->getMessage());
		}
	} */

	public function cargarSuspendida($id_susp) {
		//echo 'sdedededeedde';
		try {
				//Consultamos la informacion de la venta suspendida

			$datosSuspendida = "SELECT id, s_almacen, s_cambio, s_cliente, s_documento, s_empleado, s_funcion, s_idFact, s_impuestos, s_monto, s_pagoautomatico, s_sucursal, s_impuestost, arreglo1, arreglo2, identi, fecha, borrado";
			$datosSuspendida .= " from app_pos_venta_suspendida ";
			$datosSuspendida .= " where id = " . $id_susp . " ";

			$resultSuspendida = $this->queryArray($datosSuspendida);


			if ($resultSuspendida["total"] > 0) {
				$pos = strpos($resultSuspendida["rows"][0]["arreglo1"], "\"\"");

				$json = json_decode($resultSuspendida["rows"][0]["arreglo1"], true);

				$error = json_last_error();

				if ($error === JSON_ERROR_NONE) {
					$_SESSION['caja'] = $json;
				} else {
					if ($pos === FALSE) {
						$json = $resultSuspendida["rows"][0]["arreglo1"];
					} else {
						$json = str_replace("\"\"", null, $resultSuspendida["rows"][0]["arreglo1"]);
					}


					$pos2 = strpos($json, "\\");
					if (!$pos2 === FALSE) {
						$json = str_replace("\\", null, $json);
					}

					$_SESSION['caja'] = json_decode($json, true);
				}

				if ($resultSuspendida["rows"][0]["arreglo2"] != null) {
					$_SESSION['pagos-caja'] = json_decode($resultSuspendida["rows"][0]["arreglo2"], true);
				}
			} else {
				throw new Exception("Ocurrio un error al consultar los datos de la caja.");
			}

				//Facturar(0,$monto,$impuestos,$idVenta,$bloqueo,1);
			//echo 'e333333333';
			return array('estatus' => true, "productos" => $_SESSION['caja'], "cargos" => $_SESSION["caja"]['cargos'], "cliente" => $resultSuspendida["rows"][0]["s_cliente"]);
		} catch (Exception $e) {
			//echo 'eeeeeee';
			return array("status" => false, "msg" => $e);
		}
	}
	public function recalcula($idProducto,$cantidad,$precio, $field, $idpedidoComanda=0){


		//echo $idProducto.'XXX';
		$promoIdProducto = $idProducto;
		$xidPoducto = explode('_', $idProducto);
		$idProductoSinca = $xidPoducto[0];
		$carac = $xidPoducto[1];

		//echo 'prid='.$idProducto.' carac='.$carac;
		$totalProductos = 0;
		foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				$stringTaxes .=$value['idProducto'].'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'-'.$value['caracteristicas'].'/';
				$totalProductos += $value['cantidad'];
			}
		}

		if($carac!=''){
			$caracteristicas3 = str_replace('*',',', $carac);
			//echo 'id='.$idProductoSinca.'-Cara='.$caracteristicas3;
			$res = $this->getExisCara($_SESSION['caja'][$idProducto]->codigo,$caracteristicas3);
			$res = $res['cantidadExis'];
			//echo '$'.$res;
		}else{
			$res = $this->existenciaProducto($idProducto);
			//echo '?'.$res;
		}

		/*if($carac!=''){
			$res = $this->getExisCara($idProducto,);
		}else{
			$res = $this->existenciaProducto($idProducto);
		}	*/



		$serl = "select tipo_producto from app_productos where id=".$idProducto;
		$resrtf = $this->queryArray($serl);


		if($resrtf['rows'][0]['tipo_producto']!=2 && $field != "precio"){

			////Valida si se pueden hacer salidas sin existecnia
			$permiteVq = "SELECT salidas_sin_existencia from app_configuracion limit 1";
			$permiteVresult = $this->queryArray($permiteVq);

			$exiSiNo = 0;
			if($cantidad > $res ){
				$exiSiNo = 1;
			}
			if($permiteVresult['rows'][0]['salidas_sin_existencia']==0 && $exiSiNo==1){
				return array('estatus' =>false,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'],"count" => count($_SESSION['caja']), 'totalProductos' => $totalProductos );
				//exit();
			}

		}

		/////////////////////ACTUALIZA EL PRECIO EN COMANDA DE PEDIDO/////////////////////////////////////
		if($idpedidoComanda > 0){
			$sql = "UPDATE com_pedidos SET precioaux = '$precio' WHERE id = '$idpedidoComanda';";
			$result = $this->queryArray($sql);
		}
		/////////////////////ACTUALIZA EL PRECIO EN COMANDA DE PEDIDO FIN ////////////////////////////////


		$_SESSION['caja'][$idProducto]->cantidad = $cantidad;
		$_SESSION['caja'][$idProducto]->precio = $precio;
		$_SESSION['caja'][$idProducto]->importe = ($precio * $cantidad);

		$sessionArray = $this->object_to_array($_SESSION['caja']);
		$totalProductos = 0;
		foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				$stringTaxes .=$value['idProducto'].'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'-'.$value['caracteristicas'].'/';
				$totalProductos += $value['cantidad'];
			}
		}

		if($field == "precio"){
			//$_SESSION['caja'][$idProducto]->nombre =  $_SESSION['caja'][$idProducto]->descripcion;
			while( strrpos($_SESSION['caja'][$idProducto]->nombre, '[', 0) )
				$_SESSION['caja'][$idProducto]->nombre = substr( $_SESSION['caja'][$idProducto]->nombre , 0, (strrpos($_SESSION['caja'][$idProducto]->nombre, '[', 0))  ) ;
		}
		$this->calculaImpuestos($stringTaxes);
		$this->modificaPromo($promoIdProducto);
		return array('estatus' =>true,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'],"count" => count($_SESSION['caja']), 'totalProductos' => $totalProductos );

	}
	public function eliminarSuspendida($id) {

		try {


			$eliminarSuspendida = "DELETE from app_pos_venta_suspendida where id =" . $id;
			$resutEliminaSuspendida = $this->queryArray($eliminarSuspendida);

			if (!$resutEliminaSuspendida["status"]) {
				throw new Exception("No se pudo eliminar la venta suspendida.");
			}

			//$this->commit();
			return array("status" => true);
		} catch (Exception $e) {
			//$this->rollback();
			return array("status" => false, "msg" => $e);
		}
	}
	public function productosProntipagos(){
		$sel = "SELECT * from app_productos where tipo_producto=8";
		$res = $this->queryArray($sel);

		return $res['rows'];
	}
	public function enviaParaPronti($sku,$monto,$referencia){
		$sel = "SELECT usuarioProntipago, contrasenaProntipago from app_config_ventas";
		$resl = $this->queryArray($sel);

		/*$xml_usuario = 'pruebasPronti@pagos.com';
		$xml_contrasena = 'ProntiP30%'; */

		$xml_usuario = $resl['rows'][0]['usuarioProntipago'];
		$xml_contrasena = $resl['rows'][0]['contrasenaProntipago'];
		$xml_metodo = 'sellService';
		//echo $xml_usuario;
		//echo '<br>'.$xml_contrasena;
		//$tel = '5512179058';
		//$mystring = 'TELCEL100';
		$findme   = 'TELCEL';
		$pos = strpos($sku, $findme);
		if ($pos === false) {
			$sku = $sku;
		} else {
			$sku = '';
		}




		$xml_cuerpo = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:siv="http://prontipagos.ws.com">
	        <soapenv:Header/>
	        <soapenv:Body>
	            <siv:sellService>
	            <amount>'.$monto.'</amount>
	            <reference>'.$referencia.'</reference>
	            <sku>'.$sku.'</sku>
	            <clientReference></clientReference>
	            </siv:sellService>
	        </soapenv:Body>
	    </soapenv:Envelope>';


		$peticion = $this->peticion($xml_usuario, $xml_contrasena, $xml_metodo, $xml_cuerpo);

		$lector = new DOMDocument();
		$lector->loadXML($peticion);

		$lector_nodos = $lector->getElementsByTagName('codeTransaction');
    	if(intval($lector_nodos->length) > 0) return  array('error' => "Credito no disponible, por favor haz una recarga a tu cuenta");

		$lector_nodos = $lector->getElementsByTagName('transactionId');

	    $id_transaccion = $lector_nodos['transactionId']->nodeValue;


	   	//-------Comprobar respuesta-------//
		sleep(1);

		$xml_metodo1 = 'checkStatusService';
		$xml_cuerpo1 = '
		    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:siv="http://prontipagos.ws.com">
		        <soapenv:Header/>
		        <soapenv:Body>
		            <siv:checkStatusService>
		            	<transactionId>'.$id_transaccion.'</transactionId>
		            	<clientReference></clientReference>
		            </siv:checkStatusService>
		        </soapenv:Body>
		    </soapenv:Envelope>';

        $intXX=0;
        do{
                $intXX++;
                $status = $this->peticion($xml_usuario, $xml_contrasena, $xml_metodo1, $xml_cuerpo1);
		        $lector2 = new DOMDocument();
		    	$lector2->loadXML($status);
				$lector_nodos2 = $lector2->getElementsByTagName('codeDescription');
				$lector_nodos3 = $lector2->getElementsByTagName('codeTransaction');

				$descResp = $lector_nodos2['codeDescription']->nodeValue;
				$codeResp = $lector_nodos3['codeTransaction']->nodeValue;
                sleep(2);
        }while ($codeResp=='N/A' && $intXX < 61);

       // echo $intXX;



		/*$status = $this->peticion($xml_usuario, $xml_contrasena, $xml_metodo1, $xml_cuerpo1);

		$lector2 = new DOMDocument();
    	$lector2->loadXML($status);
		$lector_nodos2 = $lector2->getElementsByTagName('codeDescription');
		$lector_nodos3 = $lector2->getElementsByTagName('codeTransaction');

		$descResp = $lector_nodos2['codeDescription']->nodeValue;
		$codeResp = $lector_nodos3['codeTransaction']->nodeValue; */
		//echo '('.$descResp.'-'.$codeResp.')';
	    return  array('respCode' => $codeResp, 'respMsj'=>$descResp );

	}
	public function peticion($usuario, $contrasena, $metodo, $cuerpo){
	    $autentificacion = $usuario . ":" . $contrasena;
	    $url = "https://ws.prontipagos.mx/siveta-endpoint-ws-1.0-SNAPSHOT/ProntipagosTopUpServiceEndPoint?wsdl";
	    $encabezados = array(
	        'Content-Type: text/xml; charset="utf-8"',
	        'Content-Length: ' . strlen($cuerpo),
	        'Accept: text/xml',
	        'Cache-Control: no-cache',
	        'Pragma: no-cache',
	        'SOAPAction: "' . $metodo . '"'
	    );
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 180);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $encabezados);
	    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $cuerpo);
	    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($curl, CURLOPT_USERPWD, $autentificacion);
	    $resultado = curl_exec($curl);
	    //var_dump($resultado);
	    curl_close($curl);
	    unset($curl);

	    return $resultado;
	}
	public function touchProducts($departamento, $familia, $linea, $limit){
		$res = $this->verificainicioCaja();
		$sellista = "SELECT * from app_producto_sucursal";
		$res = $this->queryArray($sellista);
		$d = '';
		if($departamento > 0){
			$d .=' and (departamento ='.$departamento.' or departamento IS NULL)';
		}
		if($familia > 0){
			$d.=' and (familia='.$familia.' or familia IS NULL)';
		}
		if($linea > 0){
			$d.=' and (linea='.$linea. ' or linea IS NULL)';
		}

		if($res['total'] > 0){
			/*$sql = "SELECT	p.*, cf.rate as cantidad
				FROM	app_productos p
				LEFT JOIN app_campos_foodware cf ON p.id=cf.id_producto
				left join app_producto_sucursal ps on ps.id_producto=p.id
				WHERE p.tipo_producto!=3 and p.status=1 AND ( departamento like '%$departamento%' OR departamento IS NULL) AND ( familia like '%$familia%' OR familia IS NULL) AND ( linea like '%$linea%' OR linea IS NULL) and ps.id_sucursal='".$_SESSION['sucursal'] ."'
				GROUP BY p.id
				ORDER BY cantidad DESC
				$limit"; */
			$sql = "SELECT	p.*, cf.rate as cantidad
				FROM	app_productos p
				LEFT JOIN app_campos_foodware cf ON p.id=cf.id_producto
				left join app_producto_sucursal ps on ps.id_producto=p.id
				WHERE p.tipo_producto!=3 and p.status=1 and p.vendible=1 AND ps.id_sucursal='".$_SESSION['sucursal'] ."'".$d."
				GROUP BY p.id
				ORDER BY cantidad DESC
				$limit";
				//echo $sql;
		}else{
			/*$sql = "SELECT	p.*, cf.rate as cantidad
				FROM	app_productos p
				LEFT JOIN app_campos_foodware cf ON p.id=cf.id_producto
				WHERE p.tipo_producto!=3 and p.status=1 AND ( departamento like '%$departamento%' OR departamento IS NULL) AND ( familia like '%$familia%' OR familia IS NULL) AND ( linea like '%$linea%' OR linea IS NULL)
				GROUP BY p.id
				ORDER BY cantidad DESC
				$limit";  */
			$sql = "SELECT	p.*, cf.rate as cantidad
				FROM	app_productos p
				LEFT JOIN app_campos_foodware cf ON p.id=cf.id_producto
				WHERE p.tipo_producto!=3 and p.vendible=1 and p.status=1  ".$d."
				GROUP BY p.id
				ORDER BY cantidad DESC
				$limit";
				//echo $sql;
		}
	//echo $sql;	die;

		$res =$this->queryArray($sql);

		foreach ($res['rows'] as $key => $value) {
			$imp = $this->calImpu($value['id'],$value['precio'],$value['formulaIeps']);
			$res['rows'][$key]['precio']= $imp;
		}

		return  $res['rows'];
	}
	public function productosMoneda($moneda){
		$selectProd.="SELECT p.*, if(sum(vp.cantidad)!='', sum(vp.cantidad), 0) as cantidad";
		$selectProd.=" from app_productos p";
		$selectProd.=" left join app_pos_venta_producto vp on p.id=vp.idProducto";
		$selectProd.=" where p.status=1 and id_moneda=".$moneda;
		$selectProd.=" group by p.id";
		$selectProd.=" order by cantidad desc";

		$restSelec = $this->queryArray($selectProd);


		foreach ($restSelec['rows'] as $key => $value) {
			$imp = $this->calImpu($value['id'],$value['precio'],$value['formulaIeps']);
			$restSelec['rows'][$key]['precio']= $imp;
		}

		return array('productos' => $restSelec['rows'], 'respuesta' => $restSelec['total']);
	}
	public function calImpu($idProducto,$precio,$formula){
		$ieps = '';
		$producto_impuesto = '';

				if($formula==2){
					$ordenform = 'ASC';
				}else{
					$ordenform = 'DESC';
				}
				$subtotal = $precio;

				$queryImpuestos = "select p.id,p.precio, i.valor, i.clave,pi.formula,i.nombre";
				$queryImpuestos .= " from app_impuesto i, app_productos p ";
				$queryImpuestos .= " left join app_producto_impuesto pi on p.id=pi.id_producto ";
				$queryImpuestos .= " where p.id=" . $idProducto . " and i.id=pi.id_impuesto ";
				$queryImpuestos .= " Order by pi.id_impuesto ".$ordenform;
				//echo $queryImpuestos.'<br>';
				$resImpues = $this->queryArray($queryImpuestos);
//print_r($resImpues['rows']);
				foreach ($resImpues['rows'] as $key => $valueImpuestos) {
					if($valueImpuestos["clave"] == 'IEPS'){
						$producto_impuesto = $ieps = (($subtotal) * $valueImpuestos["valor"] / 100);
					}else{
						if($ieps!=0){
							$producto_impuesto = ((($subtotal + $ieps)) * $valueImpuestos["valor"] / 100);
						}else{
							//echo '/'.$subtotal.'-X'.$valueImpuestos["valor"].'X/';
							$producto_impuesto = (($subtotal) * $valueImpuestos["valor"] / 100);
							//echo '('.$producto_impuesto.')<br>';
						}
					}
				}
				//echo $producto_impuesto.'<br>';
				$precioNeto = $subtotal + $producto_impuesto;

				return $precioNeto;

	}
	public function estados(){
		$query = 'Select * from estados';
		$result = $this->queryArray($query);
		return $result['rows'];
	}
	public function municipios($idEstado){
		$queryM = "SELECT * from municipios where idestado=".$idEstado;
		$result = $this->queryArray($queryM);
		return $result['rows'];
	}
	public function munici(){
		$queryM = "SELECT * from municipios";
		$result = $this->queryArray($queryM);
		return $result['rows'];
	}
	public function listaPrecios(){
		$query = 'SELECT * from app_lista_precio where activo=1';
		$result = $this->queryArray($query);

		return $result['rows'];
	}
	public function listaPreciosDe($id){

		$sucursal = $this -> sucursal();

		$sql = "SELECT *
				FROM app_precio_sucursal
				WHERE producto='".$id."' and sucursal='".$sucursal."';";
        $result = $this->queryArray($sql);

		return $result['rows'];
	}
	public function moneda(){
		$query = "SELECT * from cont_coin";
		$result = $this->queryArray($query);

		return $result['rows'];
	}
	public function ventasCaja(){
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d");

		$selectVentas ="SELECT
							v.idVenta AS folio, v.fecha AS fecha, v.envio AS envio,
							CASE
								WHEN
									c.nombre IS NOT NULL
								THEN c.nombre
								ELSE
									'Publico general'
							END AS cliente, e.usuario AS empleado, s.nombre AS sucursal,
							CASE
								WHEN
									v.estatus =1
								THEN
									'Activa'
								ELSE
									'Cancelada'
							END
								AS estatus, COUNT(d.id) devoluciones, v.montoimpuestos AS iva, ROUND((v.monto),2) AS monto,
								v.documento,
								f.cadenaOriginal
						FROM
							app_pos_venta v
						LEFT JOIN
								comun_cliente c
							ON
								c.id=v.idCliente
						LEFT JOIN app_devolucioncli d
							ON
							 	v.idVenta=d.id_ov

						INNER JOIN
								accelog_usuarios e
							ON
								e.idempleado=v.idEmpleado
						INNER JOIN
								mrp_sucursal s
							ON
								s.idSuc=v.idSucursal
						LEFT JOIN
								com_comandas com
							ON
								com.id_venta = v.idVenta
						LEFT JOIN
								com_mesas m
							ON
								m.id_mesa = com.idmesa
						left join app_respuestaFacturacion f on f.idSale=v.idVenta

						where v.fecha like '%".$fechaactual."%' and v.idEmpleado='".$_SESSION['accelog_idempleado']."'
						GROUP BY v.idVenta
						ORDER BY
							folio DESC; " ;

		$resultVentas = $this->queryArray($selectVentas);

		return $resultVentas['rows'];
	}
	public function detalleVenta($idVenta){
	   $secltVent = "SELECT * from app_pos_venta where idVenta=".$idVenta;
	   $resSelc = $this->queryArray($secltVent);

		/*$secltDev = "SELECT COUNT(*) devoluciones from app_devolucioncli where id_ov=".$idVenta;
	   $resSelcDev = $this->queryArray($secltDev);*/

	   $productos = $this->productosventa($idVenta);
	   $impuestos_venta = json_decode($resSelc['rows'][0]['impuestos']);
	   $impuestos_venta = $this->object_to_array($impuestos_venta);
	   $pagos = $this->pagos($idVenta);

	   return array('products' => $productos,'taxes' => $impuestos_venta,'pay' => $pagos, 'total' => $resSelc['rows'][0]['monto'], 'descuentoGeneral' => $resSelc['rows'][0]['descuentoGeneral'], 'estatusVenta' => $resSelc['rows'][0]['estatus'] /*, 'devolucionesVenta' => $resSelcDev['rows'][0]['devoluciones']*/);
	}
	public function cancelarVenta($idVenta){

		$sql = "SELECT *
				FROM	app_respuestaFacturacion
				WHERE	idSale = '$idVenta' AND borrado != '1';";
				$facturado = $this->queryArray($sql);
		if($facturado['total'] > 0)
				return  array('estatus' => 'false' );

		/* Regresar monto por  cancelación */

		$sql = "SELECT	p.idFormapago, p.monto, v.monto montoV
				FROM	app_pos_venta v
				LEFT JOIN app_pos_venta_pagos p ON v.idVenta = p.idVenta
				WHERE v.idVenta = '$idVenta'; ";
		$formasPago = $this->queryArray($sql);

		$pagoTotal = 0;
		$pagoConCredito = 0;
		$otrasFormasPago = 0;
		foreach ($formasPago['rows'] as $key => $value) {
			$pagoTotal += (float) $value['monto'];
			if($value['idFormapago'] == 6)
				$pagoConCredito += (float) $value['monto'];
			else
				$otrasFormasPago += (float) $value['monto'];
		}

		$sql = "SELECT	SUM(total) montoDevolucion
				FROM	app_devolucioncli
				WHERE	id_ov = '$idVenta'; ";
		$totalMontoDevoluciones = $this->queryArray($sql);


		$otrasFormasPago = $formasPago['rows'][0]['montoV'] - $pagoConCredito;
		$otrasFormasPago = round($otrasFormasPago, 2, PHP_ROUND_HALF_UP);

		if( $otrasFormasPago != 0 ){
			$sql = "SELECT	activar_retiro_dev_can
					FROM	app_config_ventas;";
			$res = $this->queryArray($sql);
			if ($res['rows'][0]['activar_retiro_dev_can']){
				$concepto = "Retiro por cancelación de venta $idVenta ";
				$this->agregaretiro( ($otrasFormasPago - $totalMontoDevoluciones['rows'][0]['montoDevolucion']) ,$concepto);
			}


		}

		if( $pagoConCredito != 0 ){

			$sql = "SELECT	CASE WHEN (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) IS NULL THEN cargo
		ELSE (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) END restante
								FROM	app_pagos  p
								RIGHT JOIN (
									select id
									FROM	app_pagos
									WHERE substring_index( substring_index(concepto,':',1) , ' ' , -1) = '$idVenta'
								) c ON p.id = c.id";

			$creditoPendiente = $this->queryArray($sql);

			$this->saldarCuenta( $idVenta, ($creditoPendiente['rows'][0]['restante'] ? $creditoPendiente['rows'][0]['restante'] : 0) );

		}


		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
		///Cambia el estatus de la venta
		$updateVent = "UPDATE app_pos_venta set estatus=0 where idVenta=".$idVenta;
		$resUpdate = $this->queryArray($updateVent);

		$updateVentPen = "UPDATE app_pendienteFactura set facturado=2 where id_sale=".$idVenta;
		$resUpdatePen = $this->queryArray($updateVentPen);

		$selAl = "SELECT idSucursal from app_pos_venta where idVenta=".$idVenta;
		$resAl = $this->queryArray($selAl);
		$idsucursal = $resAl['rows'][0]['idSucursal'];

		$sel = "SELECT * from app_almacenes where id_sucursal=".$idsucursal;
		$res =$this->queryArray($sel);

		$idAlmacen = $res['rows'][0]['id'];


		$selPro = "SELECT caracteristicas, idProducto,cantidad,(subtotal -
						(SELECT IFNULL(SUM(d.subtotal),0)
						FROM app_devolucioncli d
						WHERE id_ov = '$idVenta')
						) total, lotes, lotesDevueltos, idventa_producto 
					FROM app_pos_venta_producto 
					WHERE idVenta=".$idVenta;
		$resSel = $this->queryArray($selPro);
		$sql = "SELECT idProducto, SUM(d.cantidad) cantidad
				FROM app_devolucioncli_datos d, app_pos_venta_producto v
				WHERE v.idventa_producto=d.id_producto AND id_ov='$idVenta'
				GROUP BY idProducto
				ORDER BY idProducto;";
		$devoluciones = $this->queryArray($sql);

		$cancelaSeries = "UPDATE app_producto_serie
						SET id_venta='0', estatus='0', id_almacen='$idAlmacen'
						WHERE id_venta = '$idVenta'";
		$cancelaSeries = $this->queryArray($cancelaSeries);

		foreach ($resSel['rows'] as $key => $value) {
			$caracteristicas = $value['caracteristicas'];
			if($caracteristicas!=''){

				$caracteristica = preg_replace('/\*/', ',', $caracteristicas);

				$caracteristicareplace = preg_replace('/([0-9])+/', '\'\0\'', $caracteristica);
				$caracteristicareplace=addslashes($caracteristicareplace);
				$caracteristicareplace = trim($caracteristicareplace, ',');
			}
			else{
				$caracteristicareplace = '\'0\'';
				$caracteristicareplace=addslashes($caracteristicareplace);
			}

			if( $value['lotes'] ) {
				$lotesUpdate = substr($value['lotes'] , 0, -3 );
				$sql = "UPDATE app_pos_venta_producto
								SET lotesDevueltos = '$lotesUpdate'
								WHERE idventa_producto = '{$value['idventa_producto']}';";
						$this->queryArray( $sql );


				$lotes = explode(',', substr($value['lotes'] , 0, -3 ) );

				foreach ($lotes as $ke => $val) {
					$loteCantidad = explode('-', $val);
					if( $value['lotesDevueltos'] ) {
						$lotesDevueltos = explode(',', $value['lotesDevueltos'] );
						foreach ($lotesDevueltos as $k => $val) {
							$loteDevueltoCantidad = explode('-', $val);

							if( $loteDevueltoCantidad[0] == $loteCantidad[0] ){
								$loteCantidad[1] = (float) $loteCantidad[1] - (float) $loteDevueltoCantidad[1];
							}
						}
					}
					$lotes[$ke] = $loteCantidad;
				}
				foreach ($lotes as $k => $v) {
					if($v[1] != 0) {
						/*$idtipocosto = $this->tipoCosteoProd($idProducto);
								if($idtipocosto==1){
		                            $elunit = $this->costeoProd($idProducto);
		                        }else if($idtipocosto==6){
		                            $elunit = $this->costeoProd($idProducto);
		                        }else{
		                            $elunit = $this->costeoProd($idProducto);
		                        }*/
		                        $sql = "SELECT costo 
										FROM app_inventario_movimientos im 
										WHERE im.id_lote = '{$v[0]}' AND im.id_producto = \"{$value['idProducto']}\" AND im.id_producto_caracteristica = \"{$caracteristicareplace}\" AND
											SUBSTRING_INDEX(im.referencia,' ',1) = 'Venta' AND SUBSTRING_INDEX( SUBSTRING_INDEX(im.referencia,' ',4) , ' ', -1) = '$idVenta';";
		                        $elunit = $this->queryArray($sql);
		                        //$elcost = ($elunit*1)*($cantidad*1);
		                        $elcost = ($elunit['rows'][0]['costo'] *1);

						$inser = "INSERT into app_inventario_movimientos(id_lote, id_producto_caracteristica, id_producto,cantidad,importe,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values('".$v[0]."','".$caracteristicareplace."','".$value['idProducto']."','".$v[1]."','".$value['total']."','".$idAlmacen."','".$fechaactual."','".$_SESSION['accelog_idempleado']."','1','".$elcost."','Cancelacion Venta ".$idVenta."','2')";

						$this->queryArray($inser);
					}
				}

			}
			else {
				$sql = "SELECT costo 
						FROM app_inventario_movimientos im 
						WHERE im.id_producto = \"{$value['idProducto']}\" AND im.id_producto_caracteristica = \"{$caracteristicareplace}\" AND
							SUBSTRING_INDEX(im.referencia,' ',1) = 'Venta' AND SUBSTRING_INDEX( SUBSTRING_INDEX(im.referencia,' ',4) , ' ', -1) = '$idVenta';";
				$elunit = $this->queryArray($sql);
				
				/*$idtipocosto = $this->tipoCosteoProd($idProducto);
								if($idtipocosto==1){
		                            $elunit = $this->costeoProd($idProducto);
		                        }else if($idtipocosto==6){
		                            $elunit = $this->costeoProd($idProducto);
		                        }else{
		                            $elunit = $this->costeoProd($idProducto);
		                        }*/


                
                //$elcost = ($elunit*1)*($cantidad*1);
                $elcost = ($elunit['rows'][0]['costo'] *1);

				$inser = "INSERT into app_inventario_movimientos(id_producto_caracteristica, id_producto,cantidad,importe,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values('".$caracteristicareplace."','".$value['idProducto']."','".($value['cantidad'] - $devoluciones['rows'][$key]['cantidad'])."','".$value['total']."','".$idAlmacen."','".$fechaactual."','".$_SESSION['accelog_idempleado']."','1','".$elcost."','Cancelacion Venta ".$idVenta."','2')";

				$x = $this->queryArray($inser);

/**********  PARCHE TEMPORAL Kits en inventario ****************/
				$idtipoproducto = $this->tipoProducto($value['idProducto']);

		        if( $idtipoproducto == 6 ) {

		        	$sqlkit = "SELECT	*
							FROM	app_inventario_movimientos
							WHERE	referencia LIKE 'Venta $idVenta - kit{$value['idProducto']}'";
					$productosDeKit = $this->queryArray($sqlkit);
					foreach ($productosDeKit['rows'] as $k => $v) {
						$caracteristicaskit = $v['caracteristicas'];
						$v['id_producto_caracteristica'] = addslashes($v['id_producto_caracteristica']);


						$sqlkitinfo = "SELECT	*
										FROM	com_kitsXproductos k
										WHERE	k.id_kit = '{$value['idProducto']}' AND k.id_producto = '{$v['id_producto']}';";
						$productosDeKitInfo = $this->queryArray($sqlkitinfo);
						$sqlkitInsert = "INSERT into app_inventario_movimientos(id_producto_caracteristica, id_producto,cantidad,importe,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values('{$v['id_producto_caracteristica']}','{$v['id_producto']}','".($value['cantidad'] - $devoluciones['rows'][$key]['cantidad'])*($productosDeKitInfo['rows'][0]['cantidad'])."','{$v['importe']}','$idAlmacen','$fechaactual','{$_SESSION['accelog_idempleado']}','1','{$v['costo']}','Cancelacion Venta $idVenta - kit{$value['idProducto']}','2')";
						$this->queryArray($sqlkitInsert);

					}

		        }
/**********  PARCHE TEMPORAL Kits en inventario ****************/
			}

		}

		//Inactiva las polizas relacionadas a ese ticket
		$this->query("UPDATE cont_polizas SET activo = 0 WHERE origen = 'Venta' AND idorigen = $idVenta; ");

		return  array('estatus' => true );


	}
	public function tipoProducto($idProducto) {
		$sql = "SELECT 	tipo_producto
				FROM	app_productos
				WHERE	id = '$idProducto'";
		$res = $this->queryArray($sql);
		return $res['rows'][0]['tipo_producto'];
	}

	public function saldarCuenta( $idVenta, $creditoPendiente ){

		$sql = "SELECT	p.*
				FROM	app_pagos  p
				RIGHT JOIN (
					select id
					FROM	app_pagos
					WHERE substring_index( substring_index(concepto,':',1) , ' ' , -1) = '$idVenta'
				) c ON p.id = c.id";
		$datosCxC = $this->queryArray($sql);

		$tmpConcepto = $datosCxC['rows'][0]['concepto'];
		$tmpConcepto = explode( ":" , $tmpConcepto );
		$datosCxC['rows'][0]['concepto'] = $tmpConcepto[0] .":". $tmpConcepto[1];

		$sql = "UPDATE app_pagos
					SET concepto = '{$datosCxC['rows'][0]['concepto']}'
					WHERE id = '{$datosCxC['rows'][0]['id']}' ;";
		$this->queryArray($sql);
		$sql = "INSERT INTO app_pagos (cobrar_pagar, id_prov_cli, cargo, abono, fecha_pago, concepto, id_forma_pago, id_moneda, tipo_cambio, origen)
				VALUES ({$datosCxC['rows'][0]['cobrar_pagar']} , {$datosCxC['rows'][0]['id_prov_cli']} , '0' , {$creditoPendiente} , NOW() ,'{$datosCxC['rows'][0]['concepto']}' ,{$datosCxC['rows'][0]['id_forma_pago']} , {$datosCxC['rows'][0]['id_moneda']} , {$datosCxC['rows'][0]['tipo_cambio']} , {$datosCxC['rows'][0]['origen']} );";
		$pagoCxC = $this->queryArray($sql);

		//require("../../appministra/models/cuentas.php");
		//$cuentasModel = new CuentasModel();
		$this->guardar_relacion( $pagoCxC['insertId'] , ($datosCxC['rows'][0]['id'].'@|@') ,'0',($creditoPendiente.'@|@'),'MXN@|@' , 'MXN');

	}

	function guardar_relacion($idpago,$idrelaciones,$tipo,$valores,$monedas,$monedaPago)
    {
        $fecha = $this->query("SELECT fecha_pago FROM app_pagos WHERE id = $idpago");
        $fecha = $fecha->fetch_object();

        $myQuery = '';
        $idrelaciones = explode("@|@",$idrelaciones);
        $valores = explode("@|@",$valores);
        $monedas = explode("@|@",$monedas);

        ///////////////////////ACONTIA///////////////////////////////
        ////////////////////////////////////////////////////////////
        $genera_poliza = 0; //Por default no genera poliza
        //Si tiene acontia y esta conectado
        $conexion_acontia = $this->query("SELECT conectar_acontia, pol_autorizacion FROM app_configuracion WHERE id = 1");
        $conexion_acontia = $conexion_acontia->fetch_assoc();

        if(intval($conexion_acontia['conectar_acontia']))
        {
            //Buscar si es una cuenta por pagar o por cobrar
            $cobrar_pagar = $this->query("SELECT * FROM app_pagos WHERE id = $idpago");
            $cobrar_pagar = $cobrar_pagar->fetch_assoc();

            if(intval($cobrar_pagar['cobrar_pagar']))
            {
                $idpol = 3;//Busca la poliza de cxp
                $concepto = "Pago CXP";
            }
            else
            {
                $idpol = 4;//Busca la poliza de cxc
                $concepto = "Cobro CXC";
            }


            //Busca si es poliza automatica
            $automatica = $this->query("SELECT* FROM app_tpl_polizas WHERE id = $idpol");
            $automatica = $automatica->fetch_assoc();

            //Si es automatica y se genera por documento
            if(intval($automatica['automatica']) && intval($automatica['poliza_por_mov']) == 1)
            {
                $fecha = explode('-',$cobrar_pagar['fecha_pago']);

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
                 VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'".$automatica['nombre_poliza']." ".$cobrar_pagar['concepto']."','".$cobrar_pagar['fecha_pago']."',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, ".$_SESSION["accelog_idempleado"].", 0)");
                $cont = 0;//Contador de movimientos
                $genera_poliza = 1;
            }
        }
        //Termina conexion con acontia
        ////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////

        $limite = count($idrelaciones)-2;
        for($i=0;$i<=$limite;$i++)
        {
            if($monedaPago != 'MXN')
                $valores[$i] = floatval($valores[$i]) / floatval($this->tipo_cambio_pago($idpago));

            $myQuery = "INSERT INTO app_pagos_relacion(id,id_pago,id_tipo,id_documento,cargo,abono) VALUES(0,$idpago,$tipo,".$idrelaciones[$i].",0,".$valores[$i]."); ";
            $id_pago_rel = $this->insert_id($myQuery);

            ///////////////////////ACONTIA///////////////////////////////
            ////////////////////////////////////////////////////////////
            if(intval($id_pago_rel) && $genera_poliza)
            {
                $cuentas_poliza = $this->query("SELECT id_cuenta, tipo_movto, id_dato FROM app_tpl_polizas_mov WHERE activo = 1 AND id_tpl_poliza = $idpol");
                while($cp = $cuentas_poliza->fetch_assoc())
                {
                    $cont++;
                    //Cargo o abono
                    if(intval($cp['tipo_movto']) == 1)
                        $tipo_movto = "Abono";
                    if(intval($cp['tipo_movto']) == 2)
                        $tipo_movto = "Cargo";

                    //dependiendo el tipo de dato sera el valor que tomara, en este caso solo existe el total del pago.
                    $importe = 0;
                    switch(intval($cp['id_dato']))
                    {
                        case 1 : $importe = $valores[$i]; break;
                        case 2 : $importe = $valores[$i]; break;
                        case 3 : $importe = 0; break;
                        case 4 : $importe = $valores[$i]; break;
                        case 5 : $importe = $valores[$i]; break;
                    }

                    //Si tiene cuenta de clientes busca si el id del cliente esta vinculado a una cuenta, si no es asi lo asignara a la cuenta configurada.
                    if(intval($cp['id_dato']) == 4 && intval($cobrar_pagar['cobrar_pagar']) == 0 && intval($cobrar_pagar['id_prov_cli']))
                    {
                        $cuentaCliProv = $this->query("SELECT cuenta FROM comun_cliente WHERE id = ".$cobrar_pagar['id_prov_cli']);
                        $cuentaCliProv = $cuentaCliProv->fetch_assoc();
                        if(intval($cuentaCliProv['cuenta']))
                            $cp['id_cuenta'] = $cuentaCliProv['cuenta'];
                    }

                    //Si tiene cuenta de proveedor busca si el id del proveedor esta vinculado a una cuenta, si no es asi lo asignara a la cuenta configurada.
                    if(intval($cp['id_dato']) == 5 && intval($cobrar_pagar['cobrar_pagar']) == 1)
                    {
                        $cuentaCliProv = $this->query("SELECT cuenta FROM mrp_proveedor WHERE idPrv = ".$cobrar_pagar['id_prov_cli']);
                        $cuentaCliProv = $cuentaCliProv->fetch_assoc();
                        if(intval($cuentaCliProv['cuenta']))
                            $cp['id_cuenta'] = $cuentaCliProv['cuenta'];
                    }


                    $activo2 = $activo;
                    if($importe == 0)
                    	$activo2 = 0;

                    $id_mov = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, FormaPago, tipocambio)
                                VALUES($id_poliza_acontia, $cont, 1, 1, ".$cp['id_cuenta'].", '$tipo_movto', $importe, '','$concepto Doc: $idrelaciones[$i]', $activo2, DATE_SUB(NOW(), INTERVAL 6 HOUR), ".$cobrar_pagar['id_forma_pago'].", ".$cobrar_pagar['tipo_cambio'].")");
                    $ids_movs .= $id_mov.",";

                }
                $this->query("UPDATE app_pagos_relacion SET id_poliza_mov = '$ids_movs' WHERE id = $id_pago_rel");
                $ids_movs = '';
            }
            //Termina conexion con acontia
            ////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////
        }
        if(($limite+1) == $cont)
            return 1;
        else
            return 0;
        //return $this->dataTransact($myQuery);
    }

	public function descuentoGeneral($descuento){
		//echo 'XXXX'.$descuento;
		//print_r($_SESSION['caja']);
		//exit();
		$stringTaxes = '';
		$y1 = 0;
		$_SESSION['caja']['cargos']['total'];

		$x = ($descuento*1) / 100;

		//$y = (float) $_SESSION['caja']['cargos']['subtotal'] * (float) $x;
		//$_SESSION['caja']['cargos']['descGeneral'] = $y;
		//$_SESSION['caja']['cargos']['subtotal'] =  $_SESSION['caja']['cargos']['subtotal'] - $y;
		//$_SESSION['caja']['cargos']['total'] =  $_SESSION['caja']['cargos']['total'] - $y;
		//echo '['.(float) $_SESSION['caja']['cargos']['subtotal'] * (float) $x;
		$_SESSION['caja']['cargos']['descGeneral'] = 100;
		$sessCaja = $this->object_to_array($_SESSION['caja']);
		foreach ($sessCaja as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				if($x == 0){
					$value['precio'] = 0;
				}
				$desc = $value['precio']*$x;
				$y1 += floatval($desc) * floatval($value['cantidad']);
				$value['precio'] =  $value['precio'] - $desc;

				$stringTaxes .=$key.'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'/';
				$productosTotal += $value['cantidad'];
			}
		}
		session_start();
		if($descuento > 99){
			$y1 = $_SESSION['caja']['cargos']['subtotal'];
		}
		$_SESSION['caja']['descGeneral']= $y1;


		$this->calculaImpuestos($stringTaxes);


		return array('estatus' =>true,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'], 'totalProductos' => $productosTotal, 'descGeneral' => $y1);

	}
	public function verificaRfcmodal($rfc){
		$select = "SELECT f.id,f.nombre,f.rfc,f.razon_social,f.correo,f.pais,f.regimen_fiscal,f.domicilio,f.num_ext,f.cp,f.colonia,e.estado,f.ciudad,f.municipio from comun_facturacion f,estados e where f.rfc='".$rfc."' and f.estado=e.idestado";
		$resSelct = $this->queryArray($select);

		if($resSelct['total']>0){
			return array('estatus' => true ,'datosFac' => $resSelct['rows']);
		}else{
			return array('estatus' => false );
		}

	}
	public function datosFacturacionCliente($idFact){
	  /*$datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
			e.estado estado,ciudad,municipio,regimen_fiscal,cf.estado as idEstado
			from comun_facturacion cf left join estados e on  e.idestado=cf.estado
			where  id=" . $idFact; */
		$datosFacturacion ="SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
			e.estado estado,ciudad,cf.municipio,regimen_fiscal,cf.estado as idEstado, m.idmunicipio as idMunicipio
			from comun_facturacion cf left join estados e on  e.idestado=cf.estado
			left join municipios m on cf.municipio=m.municipio
			where  id=".$idFact;

			$result = $this->queryArray($datosFacturacion);

		return array("Datafact" => $result['rows']);
	}
	public function updateDatosFac($idFac,$rfc,$razSoc,$email,$pais,$regimen,$domicilio,$numero,$cp,$col,$estado,$municipio,$ciudad){
		$selcMuni = "SELECT * from municipios where idmunicipio=".$municipio;
		$resmunici = $this->queryArray($selcMuni);
		$municipioNombre = $resmunici['rows'][0]['municipio'];

		$update = "UPDATE comun_facturacion set rfc='".$rfc."', razon_social='".$razSoc."', correo='".$email."', pais='".$pais."', regimen_fiscal='".$regimen."', domicilio='".$domicilio."', num_ext='".$numero."', cp='".$cp."', colonia='".$col."', estado='".$estado."', ciudad='".$ciudad."', municipio='".$municipioNombre."' where id=".$idFac;

		$res = $this->queryArray($update);

		return  array('estatus' => true , 'Datos' => $res['rows'] );

	}
	public function oneFact($idComunFactu,$idVenta){

		require_once('../../modulos/SAT/config.php');
		date_default_timezone_set("Mexico/General");
		$fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-5 minute"));


		/////Si es a credito y existe
		/*$sql = "SELECT	p.idFormapago, p.monto, v.monto montoV
				FROM	app_pos_venta v
				LEFT JOIN app_pos_venta_pagos p ON v.idVenta = p.idVenta
				WHERE v.idVenta = '$idVenta'; ";
				echo $sql;
		$formasPago = $this->queryArray($sql);

		$pagoTotal = 0;
		$pagoConCredito = 0;
		$otrasFormasPago = 0;
		foreach ($formasPago['rows'] as $key => $value) {
			$pagoTotal += (float) $value['monto'];
			if($value['idFormapago'] == 6)
				$pagoConCredito += (float) $value['monto'];
			else
				$otrasFormasPago += (float) $value['monto'];
		}
		if( $pagoConCredito != 0 ){

			$sql = "SELECT	CASE WHEN (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) IS NULL THEN cargo
		ELSE (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) END restante
								FROM	app_pagos  p
								RIGHT JOIN (
									select id
									FROM	app_pagos
									WHERE substring_index( substring_index(concepto,':',1) , ' ' , -1) = '$idVenta'
								) c ON p.id = c.id";

			$creditoPendiente = $this->queryArray($sql);

			$this->saldarCuenta( $idVenta, ($creditoPendiente['rows'][0]['restante'] ? $creditoPendiente['rows'][0]['restante'] : 0) );

		} */






		$SeleCad = "SELECT cadenaOriginal FROM app_pendienteFactura WHERE id_sale=".$idVenta;
		$cadenaOri = $this->queryArray($SeleCad);
		//echo $cadenaOri['rows'][0]['cadenaOriginal'];
		$azurian=base64_decode($cadenaOri['rows'][0]['cadenaOriginal']);

		$azurian = str_replace("\\", "", $azurian);
		if($azurian!=''){
			$azurian=json_decode($azurian);
		}
		$azurian = $this->object_to_array($azurian);

		////Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];

			$queryConfiguracion = "SELECT a.*, b.regimen as regimenf FROM pvt_configura_facturacion a INNER JOIN pvt_catalogo_regimen b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RFC'] = $r->rfc;
				$parametros['EmisorTimbre']['RegimenFiscal'] = $r->regimenf;
				$parametros['EmisorTimbre']['Pais'] = $r->pais;
				$parametros['EmisorTimbre']['RazonSocial'] = $r->razon_social;
				$parametros['EmisorTimbre']['Calle'] = $r->calle;
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = $r->colonia;
				$parametros['EmisorTimbre']['Ciudad'] = $r->ciudad; //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = $r->municipio;
				$parametros['EmisorTimbre']['Estado'] = $r->estado;
				$parametros['EmisorTimbre']['CP'] = $r->cp;
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				echo json_encode($JSON);
				exit();
			}

		/* Datos Receptor
		============================================================== */
		if($idComunFactu!=''){

			$selComFac = "SELECT * FROM comun_facturacion WHERE id=".$idComunFactu;
			$result = $this->queryArray($selComFac);
			//Estado
			$selEstado ="SELECT estado from estados where idestado=".$result['rows'][0]['estado'];
			$resultEstado = $this->queryArray($selEstado);


			$idCliente=$rs{'nombre'};
			$azurian['Receptor']['rfc']=strtoupper($result['rows'][0]['rfc']);
			$azurian['Receptor']['nombre']=strtoupper($result['rows'][0]['razon_social']);
			$azurian['DomicilioReceptor']['calle']=$result['rows'][0]['domicilio'];
			$azurian['DomicilioReceptor']['noExterior']=$result['rows'][0]['num_ext'];
			$azurian['DomicilioReceptor']['colonia']=$result['rows'][0]['colonia'];
			$azurian['DomicilioReceptor']['localidad']=$result['rows'][0]['ciudad'];
			$azurian['DomicilioReceptor']['municipio']=$result['rows'][0]['municipio'];
			$azurian['DomicilioReceptor']['estado']=$resultEstado['rows'][0]['estado'];
			$azurian['DomicilioReceptor']['pais']=$result['rows'][0]['pais'];
			$azurian['DomicilioReceptor']['codigoPostal']=$result['rows'][0]['cp'];
			$azurian['Correo']['Correo'] = $result['rows'][0]['correo'];

		}else{
			$idCliente='';
			$azurian['Receptor']['rfc']='XAXX010101000';
			$azurian['Receptor']['nombre']='Factura generica';
			$azurian['DomicilioReceptor']['calle']='';
			$azurian['DomicilioReceptor']['noExterior']='';
			$azurian['DomicilioReceptor']['colonia']='';
			$azurian['DomicilioReceptor']['localidad']='';
			$azurian['DomicilioReceptor']['municipio']='';
			$azurian['DomicilioReceptor']['estado']='';
			$azurian['DomicilioReceptor']['pais']='';
			$azurian['DomicilioReceptor']['codigoPostal']='';
			$azurian['Correo']['Correo'] = '';
		}
				/* --- Configuracion de las series  ---*/
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);

		if($resSer['rows'][0]['seriesFactura']==1){
			$result3 ="SELECT * FROM pvt_serie_folio WHERE id=".$seriex;
		}else{
			$result3 ="SELECT * FROM pvt_serie_folio WHERE id=1";
		}
		$rs3 = $this->queryArray($result3);

		/*$serFol = "SELECT * FROM pvt_serie_folio WHERE id=1";
		$rs3 = $this->queryArray($serFol); */

		$selecLogo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
		$rs4 = $this->queryArray($selecLogo);

		$azurian['org']['logo'] = $rs4['rows'][0]['logoempresa'];

		/* Datos serie y folio
		============================================================== */
		$azurian['Basicos']['serie']=$rs3['rows'][0]['serie']; //No obligatorio
		$azurian['Basicos']['folio']=$rs3['rows'][0]['folio'];

		/* Datos Emisor
		============================================================== */
		$azurian['Emisor']['rfc']=strtoupper($parametros['EmisorTimbre']['RFC']);
		$azurian['Emisor']['nombre']=strtoupper($parametros['EmisorTimbre']['RazonSocial']);
		/* Datos Fiscales Emisor
		============================================================== */
		$azurian['FiscalesEmisor']['calle']=$parametros['EmisorTimbre']['Calle'];
		$azurian['FiscalesEmisor']['noExterior']=$parametros['EmisorTimbre']['NumExt'];
		$azurian['FiscalesEmisor']['colonia']=$parametros['EmisorTimbre']['Colonia'];
		$azurian['FiscalesEmisor']['localidad']=$parametros['EmisorTimbre']['Ciudad'];
		$azurian['FiscalesEmisor']['municipio']=$parametros['EmisorTimbre']['Municipio'];
		$azurian['FiscalesEmisor']['estado']=$parametros['EmisorTimbre']['Estado'];
		$azurian['FiscalesEmisor']['pais']=$parametros['EmisorTimbre']['Pais'];
		$azurian['FiscalesEmisor']['codigoPostal']=$parametros['EmisorTimbre']['CP'];

		/* Datos Regimen
		============================================================== */
		$azurian['Regimen']['Regimen']=$parametros['EmisorTimbre']['RegimenFiscal'];

		/* Fecha Factura
		============================================================== */
		$azurian['Basicos']['fecha']=$fecha;

		/* Impuestos
		============================================================== */
		$tisr=$azurian['Impuestos']['totalImpuestosRetenidos'];
		$tiva=$azurian['Impuestos']['totalImpuestosTrasladados'];
		$tieps=$azurian['Impuestos']['totalImpuestosIeps'];

	//    $positionPath='../../webapp/modulos';


		//require_once('../../modulos/lib/nusoap.php');
		//require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT2.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT.php');
		}

	}
	public function Iniciarcaja($sucursal,$monto){
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
		$_SESSION['sucursal'] = $sucursal;

		$insertInicioCaja = "INSERT INTO app_pos_inicio_caja(id,fecha,monto,idUsuario,idCortecaja,idSucursal) values('','" . $fechaactual . "','" . $monto . "'," . $_SESSION['accelog_idempleado'] . ",NULL," . $sucursal . ")";

		$resultInsert = $this->queryArray($insertInicioCaja);

		$query = "select  s.idSuc, s.nombre sucursal,a.idAlmacen ,a.nombre almacen from mrp_sucursal s, almacen a where s.idAlmacen=a.idAlmacen and s.idSuc=" . $sucursal;

		$resultQuery = $this->queryArray($query);

		$id = $resultQuery["rows"][0]["idSuc"];
		$sucursal = $resultQuery["rows"][0]["sucursal"];
		$idAlmacen = $resultQuery["rows"][0]["idAlmacen"];

		return '<input type="hidden" id="caja-sucursal" value="' . $idSuc . '"><input type="hidden" id="caja-almacen" value="' . $idAlmacen . '">Sucursal:' . $sucursal;
	}
	public function getCut($init, $end, $onlyShow = 0 , $iduser, $cortePF = 0){

				if($onlyShow==0){
					//echo 'Entro al False';
					date_default_timezone_set("Mexico/General");
					$fechaactual = date("Y-m-d H:i:s");

					$iduser = $_SESSION['accelog_idempleado'];

					$selIniCaj = "SELECT max(fecha) as fechaInicio from app_pos_inicio_caja where idUsuario=".$iduser;
					$resFechaIni = $this->queryArray($selIniCaj);

					$SelemontoInicial ="SELECT monto from app_pos_inicio_caja where idUsuario='".$iduser."' and fecha='".$resFechaIni['rows'][0]['fechaInicio']."'";
					$resMon = $this->queryArray($SelemontoInicial);

					$montoInical = $resMon['rows'][0]['monto'];


					$init = $resFechaIni['rows'][0]['fechaInicio'];
					$end = $fechaactual;

				}else{
					//echo 'entro al true';
				}

				//echo $iduser.']]';

// 				$sql  = 'SELECT ';
// 				$sql .= '   "Ventas" AS Flag, ';
// 				$sql .= '   v.idVenta, ';
// 				$sql .= '   v.fecha, ';
// 				$sql .= '   c.nombre, ';
// 				$sql .= '   ROUND(v.descuentoGeneral,2) as descuentoGeneral, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 1 AND v.idVenta = vp.idVenta )  AS Efectivo , ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 4 AND v.idVenta = vp.idVenta ) AS TCredito, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 5 AND v.idVenta = vp.idVenta ) AS TDebito, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 6 AND v.idVenta = vp.idVenta ) AS CxC, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 2 AND v.idVenta = vp.idVenta ) AS Cheque, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 7 AND v.idVenta = vp.idVenta ) AS Trans, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 8 AND v.idVenta = vp.idVenta ) AS SPEI, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 3 AND v.idVenta = vp.idVenta ) AS TRegalo, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 12 AND v.idVenta = vp.idVenta ) AS TVales, ';	
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 9 AND v.idVenta = vp.idVenta ) AS Ni, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 21 AND v.idVenta = vp.idVenta ) AS Otros, ';
// 				$sql .= '   ( SELECT if(ISNULL(SUM(vp.monto)),0.00,SUM(vp.monto))  FROM app_pos_venta_pagos vp WHERE vp.idFormapago = 26 AND v.idVenta = vp.idVenta ) AS Cortesia, ';
// 				//$sql .= '     ';
// 				$sql .= '   REPLACE(FORMAT(v.cambio, 2),",","") as cambio, ';
// 				$sql .= '   REPLACE(FORMAT(v.montoimpuestos, 2), ",", "") AS Impuestos, ';
// 				$sql .= '   REPLACE(FORMAT((v.monto - v.montoimpuestos), 2 ), ",", "") AS Monto, ';
// 				$sql .= '   REPLACE(FORMAT(v.monto, 2), ",", "") AS Importe, ';
// 				$sql .= '	v.estatus estatus, (d.estatus) condevolucion ';
// 				$sql .= '	FROM ';
// 				$sql .= '   app_pos_venta v ';
// 				$sql .= '   LEFT JOIN app_pos_venta_pagos p ON p.idVenta = v.idVenta ';
// 				$sql .= '   LEFT JOIN comun_cliente c ON v.idCliente = c.id ';
// 				$sql .= '   LEFT JOIN app_devolucioncli d ON v.idVenta = d.id_ov ';
// 				$sql .= '   WHERE ';
// /*				$sql .= '   v.estatus = 1 ';*/
// /*				$sql .= '   AND ';*/
// 				$sql .= '   v.idEmpleado = ' . $iduser . ' ';
// 				$sql .= '   AND ';
// 				$sql .= '   v.fecha BETWEEN ';
// 				$sql .= '   "' . $init . '" ';
// 				$sql .= '   AND ';
// 				$sql .= '   "' . $end . '" ';
// 				$sql .= 'GROUP BY ';
// 				$sql .= '   v.idVenta ';
// 				$resVentas = $this->queryArray($sql);

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

				///// SUM DE CORTES PARCIALES PARA CORTE FINAL CONFIG PARCIALES ////
				$reportado = 0;
				if($cortePF == 1){
					$sql = "SELECT sum(total_reportado) reportado FROM app_pos_corte_parcial WHERE id_empleado = ".$iduser." and id_corte is null;";
					$result = $this->queryArray($sql);
					$reportado = $result['rows'][0]['reportado'];
				}

				///// SUM DE CORTES PARCIALES PARA CORTE FINAL CONFIG PARCIALES FIN ////
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
			return  array( 'ventas' => $resVentas['rows'] ,'productos' => $resProductos['rows'], 'retiros' => $resRetiros['rows'], 'desde' => $init, 'hasta' => $end, 'montoInical' => $montoInical, 'monto_ventas' => $totalX, 'saldoDisponible' => $saldoDisponible, 'ventas_total' => $totalX2, 'totalof' => $totalof, 'tarjetas' => $resTarjetas['rows'], 'abonos' => $resAbonos['rows'],
				'cancelaciones' => $cancelaciones['rows'], 'efectivo' => $efectivo['rows'], 'propinas' => $propinas_2, 'garantias' => $garantias['rows'], 'devoluciones' => $devoluciones['rows'], 'descuentos' => $descuentos['rows'], 'facturas' => $facturas['rows'], 'impuestos' => $impuestos['rows'], 'reportado' => $reportado) ;
	}
	public function eliminarPago($pago) {

		$pagado = $_SESSION['pagos-caja']["pagos"][$pago]['cantidad'];
		unset($_SESSION['pagos-caja']["pagos"][$pago]);

		if (count($_SESSION['pagos-caja']["pagos"]) < 1) {
			unset($_SESSION['pagos-caja']["pagos"]);

			$_SESSION['pagos-caja']["Abonado"] = number_format(0, 2);
			$_SESSION['pagos-caja']["porPagar"] = number_format($_SESSION['caja']['cargos']['total'], 2);
			$_SESSION['pagos-caja']["cambio"] = number_format(0, 2);

			return array("status" => false, "abonado" => $_SESSION['pagos-caja']["Abonado"], "porPagar" => $_SESSION['pagos-caja']["porPagar"], "cambio" => $_SESSION['pagos-caja']["cambio"]);
		} else {

			$_SESSION['pagos-caja']["Abonado"] -= number_format($pagado, 2);
				//Aun por pagar en los casos que pagan con diferentes metodos.
			$porPagar = $_SESSION["caja"]["cargos"]["total"] - $_SESSION['pagos-caja']["Abonado"];
			if ($_SESSION['pagos-caja']["Abonado"] >= $_SESSION["caja"]["cargos"]["total"]) {
				$_SESSION['pagos-caja']["porPagar"] = number_format(0, 2);
			} else {
				$_SESSION['pagos-caja']["porPagar"] = number_format($porPagar, 2);
			}
			$_SESSION['pagos-caja']["cambio"] -= number_format($pagado, 2);
		}

		return array("status" => true, "abonado" => $_SESSION['pagos-caja']["Abonado"], "porPagar" => $_SESSION['pagos-caja']["porPagar"], "cambio" => $_SESSION['pagos-caja']["cambio"]);
	}
	public function crearCorteNormal($fecha_inicio,$fecha_fin,$saldo_inicial,$monto_venta,$saldo_disponible,$retiro_caja,$deposito_caja,$retiros, $jsonArqueo){

			$sql = 'SELECT printAuto FROM app_config_ventas;';
			$result=$this->queryArray($sql);
			$configPrint = $result['rows'][0]['printAuto'];

			  if($retiro_caja==''){
					$retiro_caja = 0;
				}
				if($deposito_caja==''){
					$deposito_caja = 0;
				}

		$saldo_final = ( $saldo_disponible - $retiro_caja ) + $deposito_caja;


				$qry  = "INSERT INTO app_pos_corte_caja ";
				$qry .= "(fechainicio, ";
				$qry .= "fechafin, ";
				$qry .= "retirocaja, ";
				$qry .= "abonocaja, ";
				$qry .= "saldoinicialcaja, ";
				$qry .= "saldofinalcaja, ";
				$qry .= "montoventa, ";
				$qry .= "idEmpleado, ";
				$qry .= "tipoCorte) ";
				$qry .= "VALUES ";
				$qry .= "('" . $fecha_inicio . "', ";
				$qry .= "'" . $fecha_fin . "', ";
				$qry .= "" . $retiro_caja . ", ";
				$qry .= "" . $deposito_caja . ", ";
				$qry .= "" . $saldo_inicial . ", ";
				$qry .= "" . $saldo_final . ", ";
				$qry .= "" . $monto_venta . ", ";
				$qry .= "" . $_SESSION['accelog_idempleado'] . ", ";
				$qry .= "" . '1' . ");";
				//echo $qry;
				$res1 = $this->queryArray($qry);
				$id = $res1['insertId'];

				//Arqueo de caja
					$sql = "INSERT INTO	app_pos_arqueo_caja (id_corte, info_json)
							VALUES	('$id', '$jsonArqueo')";
					$this->queryArray( $sql );

				$token =explode("-", $retiros);
				foreach ($token as $key => $value) {
					if(is_numeric($value)){
						$updt = "UPDATE venta_retiro_caja set idcorte=".$id." where id=".$value;
						$resUPdt = $this->queryArray($updt);
					}

				}

				$qry  = "SELECT ";
				$qry .= "au.idSuc ";
				// $qry .= "mp.nombre ";
				$qry .= "FROM ";
				$qry .= "administracion_usuarios au ";
				// $qry .= "mrp_sucursal mp ";
				$qry .= "WHERE ";
				//$qry .= "mp.idSuc=au.idSuc ";
				$qry .= "au.idempleado = " . $_SESSION['accelog_idempleado'] . ";";

				$res2 = $this->queryArray($qry);
				$sucursal_id = $res2['rows'][0]['idSuc'];

				$qry  = "UPDATE ";
				$qry .= "app_pos_inicio_caja ";
				$qry .= "SET ";
				$qry .= "idCortecaja = " . $id . " ";
				$qry .= "WHERE ";
				$qry .= "idCortecaja IS NULL ";
				$qry .= "AND idSucursal = " . $sucursal_id . " ";
				$qry .= "AND idUsuario = " . $_SESSION['accelog_idempleado'] . ";";

				$res3 = $this->queryArray($qry);



				// termina el corte parcial agregado 0 por default
				$sqlch = "UPDATE app_pos_corte_parcial SET new_inicio = 0 where id_empleado = ".$_SESSION['accelog_idempleado']." and new_inicio is null ORDER BY id_corte_parcial DESC LIMIT 1;";		
				$this->queryArray($sqlch);
				$sqlch2 = "UPDATE app_pos_corte_parcial SET id_corte = '$id' where id_empleado = ".$_SESSION['accelog_idempleado']." and id_corte IS NULL ;";		
				$this->queryArray($sqlch2);
				// termina el corte parcial agregado 0 por default fin




				if(intval($id))
				{
					//Si se guardo el corte de caja genera la poliza
					//Esta conectado a acontia?
					$conexion_acontia = $this->conexion_acontia();
					$conexion_acontia = $conexion_acontia->fetch_assoc();
					if(intval($conexion_acontia['conectar_acontia']))
						$this->generar_poliza($fecha_inicio,$fecha_fin,$conexion_acontia,$id,0,1);
				}

				return  array('idCorte' => $id, 'configPrint' => $configPrint );
	}
	public function crearCorteParcial($fecha_inicio,$fecha_fin,$saldo_inicial,$monto_venta,$saldo_disponible,$retiro_caja,$deposito_caja,$retiros, $jsonArqueo){

			  if($retiro_caja==''){
					$retiro_caja = 0;
				}
				if($deposito_caja==''){
					$deposito_caja = 0;
				}

		$saldo_final = ( $saldo_disponible - $retiro_caja ) + $deposito_caja;


				$qry  = "INSERT INTO app_pos_corte_caja ";
				$qry .= "(fechainicio, ";
				$qry .= "fechafin, ";
				$qry .= "retirocaja, ";
				$qry .= "abonocaja, ";
				$qry .= "saldoinicialcaja, ";
				$qry .= "saldofinalcaja, ";
				$qry .= "montoventa, ";
				$qry .= "idEmpleado, ";
				$qry .= "tipoCorte, ";
				$qry .= "corteZ) ";
				$qry .= "VALUES ";
				$qry .= "('" . $fecha_inicio . "', ";
				$qry .= "'" . $fecha_fin . "', ";
				$qry .= "" . $retiro_caja . ", ";
				$qry .= "" . $deposito_caja . ", ";
				$qry .= "" . $saldo_inicial . ", ";
				$qry .= "" . $saldo_final . ", ";
				$qry .= "" . $monto_venta . ", ";
				$qry .= "" . $_SESSION['accelog_idempleado'] . ", ";
				$qry .= "" . '2' . ", ";
				$qry .= "" . '-1' . ");";
				//echo $qry;
				$res1 = $this->queryArray($qry);
				$id = $res1['insertId'];

				//Arqueo de caja
					$sql = "INSERT INTO	app_pos_arqueo_caja (id_corte, info_json)
							VALUES	('$id', '$jsonArqueo')";
					$this->queryArray( $sql );

				$token =explode("-", $retiros);
				foreach ($token as $key => $value) {
					if(is_numeric($value)){
						$updt = "UPDATE venta_retiro_caja set idcorte=".$id." where id=".$value;
						$resUPdt = $this->queryArray($updt);
					}

				}

				$qry  = "SELECT ";
				$qry .= "au.idSuc ";
				// $qry .= "mp.nombre ";
				$qry .= "FROM ";
				$qry .= "administracion_usuarios au ";
				// $qry .= "mrp_sucursal mp ";
				$qry .= "WHERE ";
				//$qry .= "mp.idSuc=au.idSuc ";
				$qry .= "au.idempleado = " . $_SESSION['accelog_idempleado'] . ";";

				$res2 = $this->queryArray($qry);
				$sucursal_id = $res2['rows'][0]['idSuc'];

				$qry  = "UPDATE ";
				$qry .= "app_pos_inicio_caja ";
				$qry .= "SET ";
				$qry .= "idCortecaja = " . $id . " ";
				$qry .= "WHERE ";
				$qry .= "idCortecaja IS NULL ";
				$qry .= "AND idSucursal = " . $sucursal_id . " ";
				$qry .= "AND idUsuario = " . $_SESSION['accelog_idempleado'] . ";";

				$res3 = $this->queryArray($qry);
				if(intval($id))
				{
					//Si se guardo el corte de caja genera la poliza
					//Esta conectado a acontia?
					$conexion_acontia = $this->conexion_acontia();
					$conexion_acontia = $conexion_acontia->fetch_assoc();
					if(intval($conexion_acontia['conectar_acontia']))
						$this->generar_poliza($fecha_inicio,$fecha_fin,$conexion_acontia,$id,0,1);
				}

				return  array('idCorte' => $id );
	}
	public function crearCorteZ($fecha_inicio,$fecha_fin,$saldo_inicial,$monto_venta,$saldo_disponible,$retiro_caja,$deposito_caja,$retiros, $jsonArqueo, $tipoCorte){

			  if($retiro_caja==''){
					$retiro_caja = 0;
				}
				if($deposito_caja==''){
					$deposito_caja = 0;
				}

		$saldo_final = ( $saldo_disponible - $retiro_caja ) + $deposito_caja;


				$qry  = "INSERT INTO app_pos_corte_caja ";
				$qry .= "(fechainicio, ";
				$qry .= "fechafin, ";
				$qry .= "retirocaja, ";
				$qry .= "abonocaja, ";
				$qry .= "saldoinicialcaja, ";
				$qry .= "saldofinalcaja, ";
				$qry .= "montoventa, ";
				$qry .= "idEmpleado, ";
				$qry .= "tipoCorte) ";
				$qry .= "VALUES ";
				$qry .= "('" . $fecha_inicio . "', ";
				$qry .= "'" . $fecha_fin . "', ";
				$qry .= "" . $retiro_caja . ", ";
				$qry .= "" . $deposito_caja . ", ";
				$qry .= "" . $saldo_inicial . ", ";
				$qry .= "" . $saldo_final . ", ";
				$qry .= "" . $monto_venta . ", ";
				$qry .= "" . $_SESSION['accelog_idempleado'] . ", ";
				$qry .= "" . '3' . ");";
				//echo $qry;
				$res1 = $this->queryArray($qry);
				$id = $res1['insertId'];

				//Arqueo de caja
					$sql = "INSERT INTO	app_pos_arqueo_caja (id_corte, info_json)
							VALUES	('$id', '$jsonArqueo')";
					$this->queryArray( $sql );

				$token =explode("-", $retiros);
				foreach ($token as $key => $value) {
					if(is_numeric($value)){
						$updt = "UPDATE venta_retiro_caja set idcorte=".$id." where id=".$value;
						$resUPdt = $this->queryArray($updt);
					}

				}

				$qry  = "SELECT ";
				$qry .= "au.idSuc ";
				// $qry .= "mp.nombre ";
				$qry .= "FROM ";
				$qry .= "administracion_usuarios au ";
				// $qry .= "mrp_sucursal mp ";
				$qry .= "WHERE ";
				//$qry .= "mp.idSuc=au.idSuc ";
				$qry .= "au.idempleado = " . $_SESSION['accelog_idempleado'] . ";";

				$res2 = $this->queryArray($qry);
				$sucursal_id = $res2['rows'][0]['idSuc'];

				$qry  = "UPDATE ";
				$qry .= "app_pos_inicio_caja ";
				$qry .= "SET ";
				$qry .= "idCortecaja = " . $id . " ";
				$qry .= "WHERE ";
				$qry .= "idCortecaja IS NULL ";
				$qry .= "AND idSucursal = " . $sucursal_id . " ";
				$qry .= "AND idUsuario = " . $_SESSION['accelog_idempleado'] . ";";

				$res3 = $this->queryArray($qry);
				if(intval($id))
				{
					//Si se guardo el corte de caja genera la poliza
					//Esta conectado a acontia?
					$conexion_acontia = $this->conexion_acontia();
					$conexion_acontia = $conexion_acontia->fetch_assoc();
					if(intval($conexion_acontia['conectar_acontia']))
						$this->generar_poliza($fecha_inicio,$fecha_fin,$conexion_acontia,$id,0,1);
				}

				return  array('idCorte' => $id );
	}
	public function actulizaCampoZ($idParcial, $idTotal) {
		$sql = "UPDATE  app_pos_corte_caja
                SET     corteZ = '$idTotal'
                WHERE   idCortecaja = '$idParcial' ";
                //die($sql);
        $this->queryArray($sql);
	}
	public function conexion_acontia()
	{
		return $this->query("SELECT conectar_acontia, pol_autorizacion FROM app_configuracion WHERE id = 1");
	}

	public function generar_poliza($fecha_inicio,$fecha_fin,$conexion_acontia,$idAccion, $doc, $cv, $pago = 0, $monto = 0, $referencia = '')
	{
		//FUNCIONES DE CREACION DE POLIZAS ACONTIA
		if($pago)
		{
			$myQuery = "SELECT* FROM app_tpl_polizas_pagos WHERE id = $pago";
		}
		else
		{
			$where = 10;
			if(intval($doc) == 2)
				$where = 1;
			$myQuery = "SELECT* FROM app_tpl_polizas WHERE id = $where";
		}

		$automatica = $this->query($myQuery);
		$automatica = $automatica->fetch_assoc();
		if(intval($automatica['automatica']))
		{
			$fecha = explode('-',$fecha_fin);
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

			if($ejercicio>0)
			{
				$numpol = $this->query("SELECT IFNULL((SELECT pp.numpol+1  FROM cont_polizas pp WHERE pp.idtipopoliza = ".$automatica['id_tipo_poliza']." AND pp.activo = 1 AND pp.idejercicio = $ejercicio AND pp.idperiodo = ".intval($fecha[1])." ORDER BY pp.numpol DESC LIMIT 1),0) AS n");
				$numpol = $numpol->fetch_assoc();
				$numpol = $numpol['n'];
				if(!intval($numpol))
					$numpol = 1;
				$activo = 1;

				if(intval($conexion_acontia['pol_autorizacion']))
					$activo = 0;

				$mensaje_corte = "Corte";
				if(intval($automatica['automatica']) == 2)
					$mensaje_corte = "Venta";

				switch($pago)
				{
					case 1 : $mensaje_corte .= " Pago Efectivo Factura";break;
					case 2 : $mensaje_corte .= " Pago Tarjeta Factura";break;
					case 3 : $mensaje_corte .= " Pago Transferencia Factura";break;
					case 4 : $mensaje_corte .= " Pago Efectivo Ticket";break;
					case 5 : $mensaje_corte .= " Pago Tarjeta Ticket";break;
					case 6 : $mensaje_corte .= " Pago Transferencia Ticket";break;
				}

				if(intval($automatica['automatica']) == intval($cv))
				{
					$idorigen = 0;
					$mensaje_corte = "Varias Ventas";
					if(strpos($idAccion,',') === false)
					{
						$idorigen = $idAccion;
						$mensaje_corte = "Corte";
						if(intval($automatica['automatica']) == 2)
							$mensaje_corte = "Venta";
					}
					if($referencia != '')
						$referencia = '# Referencia '.$referencia;
					$id_poliza_acontia = $this->insert_id("INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, concepto, origen, idorigen, fecha, fecha_creacion, activo, eliminado, pdv_aut, usuario_creacion, usuario_modificacion)
						 VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'".$automatica['nombre_poliza']." ($referencia) $mensaje_corte : $idAccion', '$mensaje_corte', $idAccion,'$fecha[0]-$fecha[1]-$fecha[2]',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, ".$_SESSION["accelog_idempleado"].", 0)");

					if(intval($id_poliza_acontia))
					{
						$this->generar_movimientos_poliza($fecha_inicio,$fecha_fin,$conexion_acontia,$idAccion,$id_poliza_acontia,$activo,$automatica['automatica'],$doc,$pago, $monto);
					}
				}
			}
		}
	}

	public function generar_poliza_pagos($fechainicio,$fechaactual,$conexion_acontia,$idVenta,$documento,$cv)
	{
		//Ver si tiene configurado la poliza de pagos en efectivo
		$sumEfectivo = $this->query("SELECT SUM(monto) AS montof FROM app_pos_venta_pagos WHERE idVenta = $idVenta AND idFormapago = 1");
		$sumEfectivo = $sumEfectivo->fetch_assoc();
		$sumEfectivo = $sumEfectivo['montof'];
		if(floatval($sumEfectivo))
		{
			$pago = 1;//Efectivo Factura por default
			if(intval($documento) == 1)//Si el documento es ticket
				$pago = 4;//Cambia a efectivo ticket

			$this->generar_poliza($fechainicio,$fechaactual,$conexion_acontia,$idVenta,$documento,$cv,$pago,$sumEfectivo);
		}

		//Ver si tiene configurado la poliza de pagos en tarjeta
		$sumTarjeta = $this->query("SELECT SUM(monto) AS montof FROM app_pos_venta_pagos WHERE idVenta = $idVenta AND (idFormapago = 4 || idFormapago = 5)");
		$sumTarjeta = $sumTarjeta->fetch_assoc();
		$sumTarjeta = $sumTarjeta['montof'];
		if(floatval($sumTarjeta))
		{
			$pago = 2;//Tarjeta Factura por default
			if(intval($documento) == 1)//Si el documento es ticket
				$pago = 5;//Cambia a tarjeta ticket

			$this->generar_poliza($fechainicio,$fechaactual,$conexion_acontia,$idVenta,$documento,$cv,$pago,$sumTarjeta);
		}

		//Ver si tiene configurado la poliza de pagos en transferencia
		$sumTrans = $this->query("SELECT SUM(monto) AS montof, referencia FROM app_pos_venta_pagos WHERE idVenta = $idVenta AND idFormapago = 7");
		$sumTrans = $sumTrans->fetch_assoc();
		$referencia = $sumTrans['referencia'];
		$sumTrans = $sumTrans['montof'];
		if(floatval($sumTrans))
		{
			$pago = 3;//Transferencia Factura por default
			if(intval($documento) == 1)//Si el documento es ticket
				$pago = 6;//Cambia a transferencia ticket

			$this->generar_poliza($fechainicio,$fechaactual,$conexion_acontia,$idVenta,$documento,$cv,$pago,$sumTrans,$referencia);
		}

	}

	public function generar_movimientos_poliza($fecha_inicio,$fecha_fin,$conexion_acontia,$idAccion,$id_poliza_acontia,$activo,$tipo,$doc,$pago, $monto)
	{
		$ruta  = "../cont/xmls/facturas/";//Ruta donde se copiara
		$cont = 0;

		//Si viene del corte de caja
		if(intval($tipo) == 1)
		{
			$myQuery = "SELECT 'corte' AS tipo, v.documento, rf.id AS idFac, rf.xmlfile, v.idVenta AS idSale, v.idSucursal,
						v.tipo_cambio, v.idCliente, v.subtotal, v.subtotal + v.montoimpuestos AS total, impuestos,
						(SELECT idFormapago FROM app_pos_venta_pagos WHERE idVenta = v.idVenta LIMIT 1) AS forma_pago
						FROM app_pos_venta v
						LEFT JOIN app_respuestaFacturacion rf ON rf.idSale = v.idVenta AND rf.origen = 2 AND rf.id_poliza_mov = '0' AND xmlfile != ''
						WHERE v.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' AND v.estatus = 1 AND v.idVenta NOT IN (SELECT idorigen FROM cont_polizas WHERE origen = 'Venta' AND activo = 1 AND fecha >= '$fecha_inicio');";
		}
		//Si viene de la venta
		if(intval($tipo) == 2)
		{
			$myQuery = "SELECT 'venta' AS tipo, v.documento, '' AS idFac, '' AS xmlfile, v.idVenta AS idSale, v.idSucursal,
						v.tipo_cambio, v.idCliente, v.subtotal, v.subtotal + v.montoimpuestos AS total, impuestos,
						(SELECT idFormapago FROM app_pos_venta_pagos WHERE idVenta = v.idVenta LIMIT 1) AS forma_pago
						FROM app_pos_venta v
						WHERE v.idVenta IN ($idAccion);";
		}

		$ventas = $this->query($myQuery);

		while($v = $ventas->fetch_assoc())
		{
			if($pago)
			{
				if($pago == 1 || $pago == 4)//Efectivo
					$metodo_pago = 1;
				if($pago == 2 || $pago == 5)//Tarjeta
					$metodo_pago = 4;
				if($pago == 3 || $pago == 6)//Transferecia
					$metodo_pago = 7;

				$myQuery = "SELECT id_cuenta, tipo_movto, id_dato, nombre_impuesto FROM app_tpl_polizas_pagos_mov WHERE activo = 1 AND id_tpl_poliza = $pago;";
			}
			else
			{
				$metodo_pago = $v['forma_pago'];
				$where = 10;
				if($v['documento'] == 2)
					$where = 1;
				$myQuery = "SELECT id_cuenta, tipo_movto, id_dato, nombre_impuesto FROM app_tpl_polizas_mov WHERE activo = 1 AND id_tpl_poliza = $where;";
			}


			$cuentas_poliza = $this->query($myQuery);
			while($cp = $cuentas_poliza->fetch_assoc())
			{
				$cont++;
				$importe = 0;
				//Cargo o abono
				if(intval($cp['tipo_movto']) == 1)
					$tipo_movto = "Abono";
				if(intval($cp['tipo_movto']) == 2)
					$tipo_movto = "Cargo";

					//Asigna importes segun el concepto
					if(intval($cp['id_dato']) == 2)
					{
						$importe = $v['subtotal'];
					}
					elseif(intval($cp['id_dato']) == 3)//Si se trata de un impuesto
					{

						switch($cp['nombre_impuesto'])
						{
							case 'IVA 0%': 		$campo = "vp.total";
												$idimpuesto = 2;
												break;
							case 'IVA EXENTO': 	$campo = "vp.total";
												$idimpuesto = 3;
												break;
							case 'IVA IMPS': 	$campo = "(vp.total-vp.impuestosproductoventa)";
												$idimpuesto = 1;
												break;
							default:			$campo = "vp.impuestosproductoventa";
												$idimpuesto = 1;
						}
						//Hace sumatoria de impuestos correspondientes a la venta
						$impuestos = $this->query("SELECT
												IFNULL(SUM($campo*IFNULL(IF(v.tipo_cambio = 0,1,v.tipo_cambio),1)),0) AS totalImp
												FROM app_pos_venta_producto vp
												INNER JOIN app_pos_venta_producto_impuesto pi ON pi.idVentaproducto = vp.idventa_producto
												INNER JOIN app_pos_venta v ON v.idVenta = vp.idVenta
												WHERE vp.idVenta = ".$v['idSale']." AND pi.idImpuesto = $idimpuesto");
						$impuestos = $impuestos->fetch_assoc();
						$importe = $impuestos['totalImp'];


					}
					else
					{
						//Si es total, cliente o proveedor agrega el total en el importe
						if($pago)
							$importe = $monto;
						else
							$importe = $v['total'];
					}


				if(intval($v['idSale']))
					{
						if(!intval($v['idSucursal']))
							$v['idSucursal'] = 1;
						if(!$v['tipo_cambio'])
							$v['tipo_cambio'] = 1;

						//Si el cliente tiene una cuenta asignada entonces no toma en cuenta la cuenta configurada
						if(intval($cp['id_dato']) == 4 && intval($v['idCliente']))
						{
							$cuentaCliProv = $this->query("SELECT cuenta FROM comun_cliente WHERE id = ".$v['idCliente']);
							$cuentaCliProv = $cuentaCliProv->fetch_assoc();
							if(intval($cuentaCliProv['cuenta']))
								$cp['id_cuenta'] = $cuentaCliProv['cuenta'];
						}
					}
					else
					{
						$info_venta['id_sucursal'] = 1;
						$info_venta['tipo_cambio'] = 1;
					}

					$activo2 = $activo;
					if($cp['nombre_impuesto'] == 'IVA 0%' && $importe == 0)
						$activo2 = 0;

					$activo2 = ($importe == 0) ? 0 : 1;

					$id_mov = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura, FormaPago, tipocambio, IdVenta)
								VALUES($id_poliza_acontia, $cont, 1, ".$v['idSucursal'].", ".$cp['id_cuenta'].", '$tipo_movto', $importe, '".$v['xmlfile']."','Id Venta: ".$v['idSale']."', $activo2, DATE_SUB(NOW(), INTERVAL 6 HOUR), '".$v['xmlfile']."', $metodo_pago, ".$v['tipo_cambio'].", ".$v['idSale'].")");

					$ids_movs .= $id_mov.",";
			}

			if($v['xmlfile'] != '')
				$this->anexarFactura($id_poliza_acontia,$v['xmlfile'],$ruta,$ids_movs,$v['idFac']);

			$ids_movs = '';


		}
	}

	public function anexarFactura($id_poliza_acontia,$xmlfile,$ruta,$ids_movs,$idFact)
	{
		//Crear carpeta y copiar xml de la factura, ya se que esta no es el controlador pero no quedaba de otra, asi que hare una excepcion.
		if(!file_exists($ruta.$id_poliza_acontia))//Si no existe la carpeta de ese poliza la crea
		{
			mkdir ($ruta.$id_poliza_acontia, 0777);
		}
		copy($ruta.'temporales/'.$xmlfile, $ruta.$id_poliza_acontia."/".$xmlfile);
		///unlink($ruta.'temporales/'.$xmlfile);

		return $this->query("UPDATE app_respuestaFacturacion SET id_poliza_mov = '$ids_movs' WHERE id = ".$idFact);
	}

	public function obtenAlm(){

		$sucursal = $this -> sucursal();


		$sel = "SELECT * from app_almacenes where id_sucursal=".$sucursal.' and activo=1 limit 1';
		//echo $sel;
		$res =$this->queryArray($sel);
		return $res['rows'][0]['id'];
	}
	public function updatevista(){
		$up = 'SELECT vista from app_config_ventas';
		$res = $this->queryArray($up);

		if($res['rows'][0]['vista']==1){
			$upda = 'UPDATE app_config_ventas set vista="2"';
			$res1 = $this->queryArray($upda);
			$x = 2;
		}else{
			$upda = 'UPDATE app_config_ventas set vista="1"';
			$res1 = $this->queryArray($upda);
			$x = 1;
		}
		return array('vista' => $x );
	}

	public function newClientDatfact($idFac,$rfc,$razSoc,$email,$pais,$regimen,$domicilio,$numero,$cp,$col,$estado,$municipio,$ciudad){

		$queryCliente = "INSERT INTO comun_cliente (codigo,nombre,nombretienda,direccion,colonia,email,celular,cp,idEstado,idMunicipio,rfc,curp,telefono1,telefono2,limite_credito,dias_credito,num_ext,num_int,id_clasificacion,permitir_vtas_credito,id_tipo_credito,permitir_exceder_limite,dcto_pronto_pago,intereses_moratorios,id_lista_precios,envios,comision_vta,comision_cobranza) values ";
		$queryCliente .="('".$codigo."','".$razSoc."','".$tienda."','".$domicilio."','".$col."','".$email."','".$celular."','".$cp."','".$estado."','".$municipio."','".$rfc."','".$curp."','".$tel1."','".$tel2."','".$limiteCredito."','".$diasCredito."','".$numext."','".$mumint."','".$tipoClas."','".$perVenCre."','".$tipoDeCredito."','".$perExLim."','".$descuentoPP."','".$interesesMoratorios."','".$listaPrecio."','".$enviosDom."','".$comisionVenta."','".$comisionCobranza."')";
		$insertClienteRes = $this->queryArray($queryCliente);
		$idClienteInsert = $insertClienteRes['insertId'];



		$selcMuni = "SELECT * from municipios where idmunicipio=".$municipio;
		$resmunici = $this->queryArray($selcMuni);
		$municipioNombre = $resmunici['rows'][0]['municipio'];

		$insertCo = "INSERT into comun_facturacion(nombre,rfc,razon_social,correo,pais,regimen_fiscal,domicilio,num_ext,cp,colonia,estado,ciudad,municipio) values('".$idClienteInsert."','".$rfc."','".$razSoc."','".$email."','".$pais."','".$regimen."','".$domicilio."','".$numero."','".$cp."','".$col."','".$estado."','".$ciudad."','".$municipioNombre."')";
		$resInsert = $this->queryArray($insertCo);

		if(is_numeric($resInsert['insertId'])){
			return  array('estatus' => true );
		}else{
			return  array('estatus' => false );
		}



	}
	public function ventasGrid(){

		$selectVentas ="SELECT
		v.idVenta as folio,
		v.fecha as fecha,
		v.envio as envio,
		CASE WHEN c.nombre IS NOT NULL
		THEN c.nombre
		ELSE 'Publico general'
		END AS cliente,
		e.usuario as empleado,
		s.nombre as sucursal,
		CASE WHEN v.estatus =1
		THEN 'Activa'
		ELSE 'Cancelada'
		END AS estatus,
		COUNT(d.id) devoluciones,
		v.montoimpuestos as iva,
		(v.monto) as monto,
		v.documento,
		f.cadenaOriginal
		from app_pos_venta v left join comun_cliente c on c.id=v.idCliente left join app_devolucioncli d on v.idVenta=d.id_ov inner join  accelog_usuarios e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal left join app_respuestaFacturacion f on f.idSale=v.idVenta
WHERE v.fecha BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 1 day) AND NOW()
		GROUP BY v.idVenta" ;
		//echo $selectVentas;
	   // exit();
		$resultVentas = $this->queryArray($selectVentas);
		//print_r($resultVentas['rows']);

		foreach ($resultVentas['rows'] as $key => $value) {
			$x = $this->formasPaVentas($value['folio']);
			$resultVentas['rows'][$key]['formas_pago'] = $x;
		}


		return  array('ventas' => $resultVentas['rows'], 'numVentas' => $resultVentas['total']);
	}
	public function formasPaVentas($idVenta){
		$select = 'SELECT f.nombre from forma_pago f , app_pos_venta_pagos v where f.idFormapago=v.idFormapago and v.idVenta='.$idVenta;
		//echo $select.'<br>';
		$re = $this->queryArray($select);
		$cad = '';
		foreach ($re['rows'] as $key => $value) {
			$cad .= $value['nombre'].',';
		}
		return $cad;
	}
	public function numeroFactura($idVenta){
		$select = 'SELECT folio, cadenaOriginal from app_respuestaFacturacion where idSale='.$idVenta;
		$res = $this->queryArray($select);
		//echo $res['rows'][0]['cadenaOriginal'];
		$cad = base64_decode($res['rows'][0]['cadenaOriginal']);
		$cad = json_decode($cad);
		$cad = $this->object_to_array($cad);
		return $cad['Basicos']['folio'];
	}
	public function comisionesGrid(){

		$sql ="SELECT	id_venta, tipo_comision, sucursal, empleado, producto, SUM(cantidad) cantidad, SUM(total_neto) total_neto, porcentaje_comision, SUM(total_comision) total_comision
FROM	(SELECT * FROM app_pos_comision_producto ORDER BY empleado, producto, id_comision DESC) app_pos_comision_producto
GROUP BY empleado, producto;" ;


		$resultComisiones = $this->queryArray($sql);

		return  array('comisiones' => $resultComisiones['rows']);
	}
	public function ventasIndex()
	{
		//$result2 = $this->touchProducts();

		$result2  = '';
		$query3 = "SELECT * from accelog_usuarios";
		$result3 = $this->queryArray($query3);

		$query45 = "SELECT * from comun_cliente";
		$result5 = $this->queryArray($query45);

		//return $result['rows'];
		return array('productos' => $result2 ,  'usuarios' => $result3['rows'], 'clientes' => $result5['rows']);


	}
	public function empleadosComision()
	{
		//$result2 = $this->touchProducts();

		$result2  = '';
		$query3 = "SELECT idadmin idempleado, CONCAT( CONCAT ( CONCAT( CONCAT(nombre, ' ') , apellidos), ' | ') , nombreusuario) nombre
				FROM administracion_usuarios";
		$result3 = $this->queryArray($query3);


		//return $result['rows'];
		return array( 'usuarios' => $result3['rows']);


	}
	public function buscarVentas($cliente,$empleado,$desde,$hasta,$idSucursal, $via_contacto){

	$inicio = $desde;
	$fin = $hasta;
	$filtro = 1;

	if($fin!="")
	{
		list($a,$m,$d)=explode("-",$fin);
		$fin=$a."-".$m."-".((int)$d+1);
	}


	if($inicio!="" && $fin=="")
	{
		$filtro.=" and  v.fecha >= '".$inicio."' ";
	}
	if($fin!="" && $inicio=="")
	{
		$filtro.=" and  v.fecha <= '".$fin."' ";
	}
	if($inicio!="" && $fin!="")
	{
		$filtro.=" and  v.fecha <= '".$fin."' and   v.fecha >= '".$inicio."' ";
	}

	if(is_numeric($estatus))
	{
		$filtro.=" and estatus=".$estatus;
	}

	if(is_numeric($sucursal))
	{
		$filtro.=" and idSucursal=".$sucursal;
	}
	if($empleado > 0)
	{
		$filtro.=" and v.idEmpleado=".$empleado;
	}
	if(is_numeric($cliente))
	{
		if($cliente==0)
			{$filtro.="";

		}else{  $filtro.=" and idCliente=".$cliente;}
	}
	if($idSucursal!=0){
		$filtro.=' and v.idSucursal="'.$idSucursal.'"';

	}

// Filtra por la via de contacto si existe
	if(!empty($via_contacto)){
		$filtro .= ' AND m.id_via_contacto = "'.$via_contacto.'"';

	}
		$selectVentas ="SELECT  p.id_respFact id_respFact2, f.cadenaOriginal cadenaOriginal2,
							v.idVenta AS folio, v.fecha AS fecha, v.envio AS envio,
							CASE
								WHEN
									c.nombre IS NOT NULL
								THEN c.nombre
								ELSE
									'Publico general'
							END AS cliente, e.usuario AS empleado, s.nombre AS sucursal,
							CASE
								WHEN
									v.estatus =1
								THEN
									'Activa'
								ELSE
									'Cancelada'
							END
								AS estatus, COUNT(d.id) devoluciones, v.montoimpuestos AS iva, ROUND((v.monto),2) AS monto,
								v.documento,
								f.cadenaOriginal
						FROM
							app_pos_venta v
						LEFT JOIN
								comun_cliente c
							ON
								c.id=v.idCliente
						LEFT JOIN app_devolucioncli d
							ON
							 	v.idVenta=d.id_ov

						INNER JOIN
								accelog_usuarios e
							ON
								e.idempleado=v.idEmpleado
						INNER JOIN
								mrp_sucursal s
							ON
								s.idSuc=v.idSucursal
						LEFT JOIN
								com_comandas com
							ON
								com.id_venta = v.idVenta
						LEFT JOIN
								com_mesas m
							ON
								m.id_mesa = com.idmesa
						left join app_respuestaFacturacion f on f.idSale=v.idVenta
						LEFT JOIN app_pendienteFactura p ON p.id_respFact=f.id
						WHERE  ".
							$filtro."
						GROUP BY v.idVenta
						ORDER BY
							folio DESC" ;
		//echo $selectVentas;
		$resultVentas = $this->queryArray($selectVentas);

		foreach ($resultVentas['rows'] as $key => $value) {
			$x = $this->formasPaVentas($value['folio']);
			$resultVentas['rows'][$key]['formas_pago'] = $x;
		}

		return  array('ventas' => $resultVentas['rows'], 'numTrans' =>$resultVentas['total']);

	}
	public function buscarComisiones($empleado,$desde,$hasta,$idSucursal){

	$inicio = $desde;
	$fin = $hasta;
	$filtro = 1;

	if($fin!="")
	{
		list($a,$m,$d)=explode("-",$fin);
		$fin=$a."-".$m."-".((int)$d+1);
	}


	if($inicio!="" && $fin=="")
	{
		$filtro.=" and  fecha >= '".$inicio."' ";
	}
	if($fin!="" && $inicio=="")
	{
		$filtro.=" and  fecha <= '".$fin."' ";
	}
	if($inicio!="" && $fin!="")
	{
		$filtro.=" and  fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
	}


	/*if(is_numeric($sucursal))
	{
		$filtro.=" and sucursal=".$sucursal;
	}*/
	if($empleado > 0)
	{
		$filtro.=" and empleado=".$empleado;
	}

	if($idSucursal!=0){
		$filtro.=' and sucursal="'.$idSucursal.'"';

	}

		$selectVentas ="SELECT	id_venta, cp.tipo_comision , s.nombre sucursal, CONCAT( CONCAT ( CONCAT( CONCAT(e.nombre, ' ') , e.apellidos), ' | ') , e.nombreusuario) empleado, p.nombre producto, SUM(cp.cantidad) cantidad, SUM(cp.total_neto) total_neto, cp.porcentaje_comision, SUM(cp.total_comision) total_comision
						FROM	(SELECT * FROM app_pos_comision_producto ORDER BY empleado, producto, id_comision DESC) cp
						LEFT JOIN mrp_sucursal s ON cp.sucursal = s.idSuc
						LEFT JOIN administracion_usuarios e ON cp.empleado = e.idadmin
						LEFT JOIN app_productos p ON cp.producto = p.id
						WHERE	{$filtro}
						GROUP BY empleado, producto;" ;
		//echo $selectVentas;
		$resultVentas = $this->queryArray($selectVentas);

		return  array('ventas' => $resultVentas['rows']);

	}
	public function buscaVentaCaja($idVenta){
		 $selectVentas ="SELECT
							v.idVenta AS folio, v.fecha AS fecha, v.envio AS envio,
							CASE
								WHEN
									c.nombre IS NOT NULL
								THEN c.nombre
								ELSE
									'Publico general'
							END AS cliente, e.usuario AS empleado, s.nombre AS sucursal,
							CASE
								WHEN
									v.estatus =1
								THEN
									'Activa'
								ELSE
									'Cancelada'
							END
								AS estatus, COUNT(d.id) devoluciones, v.montoimpuestos AS iva, ROUND((v.monto),2) AS monto,
								v.documento,
								f.cadenaOriginal
						FROM
							app_pos_venta v
						LEFT JOIN
								comun_cliente c
							ON
								c.id=v.idCliente
						LEFT JOIN app_devolucioncli d
							ON
							 	v.idVenta=d.id_ov

						INNER JOIN
								accelog_usuarios e
							ON
								e.idempleado=v.idEmpleado
						INNER JOIN
								mrp_sucursal s
							ON
								s.idSuc=v.idSucursal
						LEFT JOIN
								com_comandas com
							ON
								com.id_venta = v.idVenta
						LEFT JOIN
								com_mesas m
							ON
								m.id_mesa = com.idmesa
						left join app_respuestaFacturacion f on f.idSale=v.idVenta
						where v.idVenta =".$idVenta."  and v.idEmpleado='".$_SESSION['accelog_idempleado']."'
						GROUP BY v.idVenta
						ORDER BY
							folio DESC;";
/*		 $selectVentas ="SELECT
		v.idVenta as folio,
		v.fecha as fecha,
		v.envio as envio,
		CASE WHEN c.nombre IS NOT NULL
		THEN c.nombre
		ELSE 'Publico general'
		END AS cliente,
		e.nombre as empleado,
		s.nombre as sucursal,
		CASE WHEN v.estatus =1
		THEN 'Activa'
		ELSE 'Cancelada'
		END AS estatus,
		v.montoimpuestos as iva,
		(v.monto) as monto
		from app_pos_venta v left join comun_cliente c on c.id=v.idCliente inner join  empleados e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal where v.idVenta =".$idVenta."  and v.idEmpleado='".$_SESSION['accelog_idempleado']."'";
	*///die($selectVentas);
		$resultVentas = $this->queryArray($selectVentas);

		return  array('venta' => $resultVentas['rows']);
	}
	public function datosretiro($idRetiro){
		$datosRetiro = "SELECT r.id,r.cantidad,r.concepto, u.usuario, r.fecha, s.nombre as sucursal from app_pos_retiro_caja r, accelog_usuarios u, mrp_sucursal s where r.idSucursal=s.idSuc and r.idempleado=u.idempleado and id=".$idRetiro;
	   ;
		$result = $this->queryArray($datosRetiro);
		return $result['rows'];

	}
	public function datosAbono($idAbono){
		/*$datosAbono = "SELECT a.id,a.cantidad,a.concepto, u.usuario, a.fecha, s.nombre as sucursal, c.nombre cliente
		from app_pos_abono_caja a, accelog_usuarios u, mrp_sucursal s, comun_cliente c
		where a.idSucursal=s.idSuc
		and a.idempleado=u.idempleado
		and a.idcliente = c.id
		and a.id=".$idAbono; */
	$datosAbono = "SELECT a.id,a.cantidad,a.concepto, u.usuario, a.fecha, s.nombre as sucursal, c.nombre as cliente
		from app_pos_abono_caja a
		LEFT JOIN  accelog_usuarios u on a.idempleado=u.idempleado
		left join mrp_sucursal s on a.idSucursal=s.idSuc
		left join comun_cliente c on a.idcliente = c.id
		where a.id=".$idAbono;

		$result = $this->queryArray($datosAbono);
		return $result['rows'];

	}
	public function checalimitecredito($cliente, $monto) {

		$querySaldo = "select sum(saldoactual) as debe from cxc where idCliente=" . $cliente;

		$result = $this->queryArray($querySaldo);

		if ($result["total"] > 0) {
			$queryCredito = "SELECT limite_credito credito, permitir_vtas_credito , permitir_exceder_limite from comun_cliente where id=" . $cliente;

			$resultCredito = $this->queryArray($queryCredito);

			if ($resultCredito["total"] < 1) {
				return array("status" => false, "msg" => "Ocurrio un error al obtener los datos del cliente..");
			}
			if($resultCredito["rows"][0]['permitir_vtas_credito']==1){
					$debe = $result["rows"][0]["debe"];
					$credito = $resultCredito["rows"][0]["credito"];

					$cargo = (float) ($debe + (float) ($monto));

					if ($cargo > $credito) {
						if($resultCredito["rows"][0]['permitir_exceder_limite']==0){
							return array("status" => false, "msg" => "El limite de credito del cliente se ha excedido, su limite de credito es de $" . number_format($credito, 2) . " y actualmente tiene un monto por liquidar de $" . number_format($debe, 2));
						}else{
							return array("status" => true);
						}

					} else {
						return array("status" => true);
					}
			}else{
				return array("status" => false, "msg" => "El cliente no tiene permitido ventas a credito.");
			}

		} else {
			return array("status" => false, "msg" => "Ocurrio un error al obtener los datos del cliente.");
		}
	}
	public function graficar($desde,$hasta,$orderby,$idSucursal,$idEmpleado,$cliente){

	$inicio = $desde;
	$fin = $hasta;
	//$filtro=1;
	//echo 'inicioi='.$inicio.' hasta='.$fin;
	if($fin!="")
	{
		list($a,$m,$d)=explode("-",$fin);
		$fin=$a."-".$m."-".((int)$d+1);
	}


	if($inicio!="" && $fin=="")
	{
		$filtro.=" and fecha >= '".$inicio."' ";
	}
	if($fin!="" && $inicio=="")
	{
		$filtro.=" and fecha <= '".$fin."' ";
	}
	if($inicio!="" && $fin!="")
	{
		$filtro.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
	}


	if($inicio!="" && $fin=="")
	{
		$filtro2.=" and fecha >= '".$inicio."' ";
	}
	if($fin!="" && $inicio=="")
	{
		$filtro2.=" and fecha <= '".$fin."' ";
	}
	if($inicio!="" && $fin!="")
	{
		$filtro2.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
	}
	if($idSucursal!=0){
		$filtro.=' and v.idSucursal="'.$idSucursal.'"';
		$filtro2.=' and v.idSucursal="'.$idSucursal.'"';
	}
	if($idEmpleado!=0){
		$filtro.=' and v.idEmpleado='.$idEmpleado;
		$filtro2.=' and v.idEmpleado='.$idEmpleado;
	}
	if($cliente!=0){
		$filtro.=' and v.idCliente='.$cliente;
		$filtro2.=' and v.idCliente='.$cliente;
	}



		$sel = 'SELECT p.nombre as label , sum(cantidad) as value';
		$sel.= ' from app_pos_venta_producto vp';
		$sel.= ' INNER JOIN app_productos p ON p.id = vp.idProducto';
		$sel.= ' INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta';
		$sel.= ' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
		$sel.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
		$sel.= ' where v.estatus=1 '.$filtro;
		$sel.= ' group by idProducto';
		$sel.= ' order by value desc';
		$sel.= ' limit 10';
		//echo $sel.'///';
		$resGra = $this->queryArray($sel);

		$sel4 = 'SELECT p.nombre as label , sum(cantidad) as value';
		$sel4.= ' from app_pos_venta_producto vp';
		$sel4.= ' INNER JOIN app_productos p ON p.id = vp.idProducto';
		$sel4.= ' INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta';
		$sel4.= ' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
		$sel.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
		$sel4.= ' where v.estatus=1 '.$filtro;
		$sel4.= ' group by idProducto';
		$sel4.= ' order by value asc';
		$sel4.= ' limit 10';
		//echo $sel.'///';
		$resGra3 = $this->queryArray($sel4);



		$sel2 = 'SELECT v.fecha as y, ROUND(sum(ROUND(v.monto,2)),2) as a';
		$sel2.= ' from app_pos_venta v';
		//$sel2.= ' INNER JOIN app_pos_venta_producto vp on v.idVenta=vp.idVenta';
		$sel2.= ' where v.estatus=1 '.$filtro2.' ';
		$sel2.= ' group by '.$orderby.'(v.fecha)';
		//echo $sel2;
			//exit();
		$resGra2 = $this->queryArray($sel2);


		return array('dona' => $resGra['rows'], 'linea' => $resGra2['rows'], 'donaMenos'=> $resGra3['rows'], );

	}
	public function graficarComision($desde,$hasta,$idSucursal){

	$inicio = $desde;
	$fin = $hasta;
	//$filtro=1;
	//echo 'inicioi='.$inicio.' hasta='.$fin;
	if($fin!="")
	{
		list($a,$m,$d)=explode("-",$fin);
		$fin=$a."-".$m."-".((int)$d+1);
	}


	if($inicio!="" && $fin=="")
	{
		$filtro.=" where fecha >= '".$inicio."' ";
	}
	if($fin!="" && $inicio=="")
	{
		$filtro.=" where fecha <= '".$fin."' ";
	}
	if($inicio!="" && $fin!="")
	{
		$filtro.=" where fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
	}



	if($idSucursal!=0){
		$filtro.=' and cp.sucursal="'.$idSucursal.'"';
	}



		$sel = 'SELECT e.nombre as label , sum(total_comision) as value';
		$sel.= ' from app_pos_comision_producto cp';
		$sel.= ' INNER JOIN administracion_usuarios e ON cp.empleado = e.idadmin';
		$sel.= ' '.$filtro;
		$sel.= ' group by empleado';
		$sel.= ' order by total_comision desc';
		$sel.= ' limit 10';
		//echo $sel.'///';
		$resGra = $this->queryArray($sel);



		$sel2 = 'SELECT cp.fecha as y, SUM(total_comision) as a';
		$sel2.= ' from app_pos_comision_producto cp';
		$sel2.= $filtro;
		$sel2.= ' group by cp.fecha';
		//echo $sel2;
			//exit();
		$resGra2 = $this->queryArray($sel2);


		return array('dona' => $resGra['rows'], 'linea' => $resGra2['rows'] );

	}
	public function getCortes(){
		$query = 'SELECT c.idCortecaja, c.fechainicio, c.fechafin, c.saldoinicialcaja, c.montoventa, c.retirocaja, c.abonocaja, c.saldofinalcaja, u.usuario, c.tipoCorte, c.corteZ from app_pos_corte_caja c , accelog_usuarios u where c.idEmpleado=u.idempleado';
		$res1 = $this->queryArray($query);

		return array('cortes' =>  $res1['rows']);
	}
	public function saldosCorte($idCorte){
		$selc = "SELECT c.*, u.usuario, s.nombre AS sucursal, o.nombreorganizacion AS organizacion
				from app_pos_corte_caja c, accelog_usuarios u, administracion_usuarios au, mrp_sucursal s, organizaciones o
				where c.idEmpleado = u.idempleado and u.idempleado = au.idempleado and au.idSuc = s.idSuc  and s.idOrganizacion = o.idorganizacion and idCortecaja = '$idCorte'";
		$result = $this->queryArray($selc);

		return $result['rows'];
	}
	public function cortesfiltrados($empleado,$desde,$hasta){

		$filtro = '';

		if($empleado!=0){
			$filtro .= ' and c.idEmpleado='.$empleado;
		}
		if($desde!='' && $hasta!=''){
			$filtro .=' and fechainicio BETWEEN "'.$desde.'" AND "'.$hasta.'" ';
		}


		$query = 'SELECT c.idCortecaja, c.fechainicio, c.fechafin, c.saldoinicialcaja, c.montoventa, c.retirocaja, c.abonocaja, c.saldofinalcaja, u.usuario, c.tipoCorte, c.corteZ from app_pos_corte_caja c , accelog_usuarios u where c.idEmpleado=u.idempleado'.$filtro;
		//echo $query;
		$res1 = $this->queryArray($query);

		return array('cortes' =>  $res1['rows']);
	}

	public function creaQR($texto,$idventa) {
        include "../SAT/PDF/phpqrcode/qrlib.php";

		$ruta = 'images/qrventas/qrticket.png';
		QRcode::png($texto, $ruta);
		return $ruta;
	}

	public function enviarRecibo($idVenta,$correo){
		$htmlC = '';
		$htmlN = '';
		$htmlX = '';

		$organizacion = $this->datosorganizacion();
		$venta = $this->datosventa($idVenta);
		$cliente = $this->datoscliente($venta[0]['idCliente']);
		$datosSucursal = $this->datosSucursal($idventa);
		$productos = $this->productosventa($idVenta);
		$impuestos_venta = json_decode($venta[0]['jsonImpuestos']);
		$impuestos_venta = $this->object_to_array($impuestos_venta);
		$pagos = $this->pagos($idventa);
		$configTikcet = $this->configTikcet();
		$color = "#D8D8D8";
		$content = '';

		// Generando el código QR para el Recibo
		unlink('images/qrventas/qrticket.png');
		$texto="netwarmonitor.mx/clientes/".$_SESSION['accelog_nombre_instancia']."/kiosko";
//    	$err = $this->creaQR($texto,$idventa);
		$codigoQrVenta = '<img style="-webkit-user-select:none" src="'.$err.'" alt="">';

		// Cargando el logo tipop o imagen de la empresa
		$imagen='../../netwarelog/archivos/1/organizaciones/'.$organizacion[0]['logoempresa'];
		$imagesize=getimagesize($imagen);
		$porcentaje=0;
		if($imagesize[0]>200 && $imagesize[1]>90){
			if($imagesize[0]>$imagesize[1]){
				$porcentaje=intval(($imagesize[1]*100)/$imagesize[0]);
				$imagesize[0]=200;
				$imagesize[1]=(($porcentaje*200)/100);
			}else{
				$porcentaje=intval(($imagesize[0]*100)/$imagesize[1]);
				$imagesize[0]=200;
				$imagesize[1]=(($porcentaje*200)/100);
			}
		}

		$src="";
		if($imagen!="" && file_exists($imagen)){
			$src='<img src="'.$imagen.'" style="width:'.$imagesize[0].'px;height:'.$imagesize[1].'px;display:block;margin:0 auto 0 auto;"/>';
		}
		include "../SAT/PDF/EnLetras.php";
		$obj=new EnLetras();
		$total_letra=strtoupper($obj->ValorEnLetras($venta[0]['monto'],'pesos','M.N.'));

		$htmlN = '<html>
			<head>
				<title>Recibo de pago</title>
			</head>

			<body>
				<table>
					<tr>
						<td><div style="width:120px;height:30px">'.$src.'</div></td>
						<td>
							<div style="margin-left:76px"> RECIBO DE PAGO
								<table>
									<tr>
										<td>
											<div style="margin-left:12px">
												<table text-align="center">
													<tr><td style="width:550px;font-size:10px"><h3>'.$organizacion[0]['nombreorganizacion'].'</h3></td></tr>
													<tr><td style="width:90px;font-size:11px">'.$organizacion[0]['RFC'].'</td></tr>
													<tr><td style="width:218px;font-size:10px">'.$organizacion[0]['domicilio'].'</td></tr>
													<tr><td style="width:218px;font-size:10px">'.$organizacion[0]['colonia'].'</td></tr>
													<tr><td style="width:138px;font-size:10px">'.$organizacion[0]['municipio'].', '.$organizacion[0]['estado'].'</td></tr>
													<tr><td style="width:153px;font-size:10px">'.$organizacion[0]['estado'].', C.P.'.$organizacion[0]['cp'].'</td></tr>
												</table>
											</div>
										</td>

										<td>
											<div style="margin-left:12px">
												<table>
												<tr><td style="font-weight:bold;font-size:14px">Folio</td></tr>
													<tr><td style="font-size:12px;color:#ff0000">'.$venta[0]["folio"].'</td></tr>
												</table>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>

				<div style="font-size:10px;margin-bottom:15px">Fecha de Expedicion: '.$venta[0]['fecha'].'</div>

				<div style="font-weight:bold;font-size:11px;background:'.$color.'">Datos Cliente Receptor:</div></td></tr>
					<table style="border:1px '.$color.'">
						<tr><td style="font-size:10px">Nombre: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$venta[0]['cliente']. '</td></tr>
						<tr><td style="font-size:10px">Direcci&oacute;n: &nbsp;&nbsp;&nbsp;&nbsp;'.$cliente[0]['direccion']. '</td></tr>
						<tr><td style="font-size:10px">Colonia: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$cliente[0]['colonia']. '</td></tr>
						<tr><td style="font-size:10px">Ciudad: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$cliente[0]['ciudad']. '</td></tr>
						<tr><td style="font-size:10px">Estado: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$venta[0]['estado']. '</td></tr>
						<tr><td style="font-size:10px">C.P.: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$cliente[0]['cp']. '</td></tr>
						<tr><td style="font-size:10px">R.F.C.: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$cliente[0]['rfc']. '</td></tr>
					</table>
				</div>
				<br>
				<div>
					<table style="border:1px '.$color.'">
						<tr>
							<td style="font-size:10px"> </td>
							<td style="width:670px;font-size:10px">  </td>
						</tr>
					</table>
				</div>

				<div>
					<table>
						<tr>
							<td style="font-weight:bold;font-size:11px;background:'.$color.';width:553px">Descripcion</td>
							<td style="font-weight:bold;font-size:11px;background:'.$color.';width:173px">Importe</td>
						</tr>
					</table>
				</div>';

		$htmlC = ' <div>
					<table style="border-collapse:collapse"> ';
						$ptotal = 0;
						foreach ($productos as $k => $v) {
							$ptotal += ($v['precio']*$v['cantidad']);
							$htmlC.='
							<tr>

								<td style="font-size:11px;border-bottom:1px dashed '.$color.';border-right:1px dashed '.$color.';width:545px">'.$v['nombre'].'</td>
								<td style="font-size:11px;border-bottom:1px dashed '.$color.';width:173px;text-align:right">'.number_format((float)($v['precio']*$v['cantidad']),2,'.',',').'</td>
							</tr> ';
						}
					$htmlC.='
					</table>
				</div>';

		$htmlX = '<div>
					<table>
						<tr>
							<td style="font-weight:bold;font-size:11px;background:'.$color.';width:553px; text-align:right">TOTAL</td>
							<td style="font-weight:bold;font-size:11px;background:'.$color.';width:173px; text-align:right"> $ '.number_format((float)$ptotal,2,'.',',').'</td>
						</tr>
					</table>
				</div>

				<div style="margin-left:10px">
					<table>
						<tr>
							<td style="font-size:11px;border-bottom:0px '.$color.';padding-left:30px"></td>
							<td style="font-size:11px;border-bottom:0px '.$color.';padding-left:5px"></td>
						</tr>

						<tr>
							<td style="font-size:11px;border-bottom:0px '.$color.';padding-left:30px"></td>
						<td style="font-size:11px;border-bottom:0px '.$color.';padding-left:5px"></td>
						</tr>

						<tr>
							<td style="font-size:11px;border-bottom:1px dashed '.$color.';border-right:1px dashed '.$color.';padding-left:30px;padding-right:10px">TOTAL EN LETRA:</td>
							<td style="font-size:10px;border-bottom:1px dashed '.$color.';padding-left:5px">'.$total_letra.'</td>
						</tr>
					</table>
				</div>

				<div>
					<table>
						<tr>

							<td style="font-size:8px;width:392px;border-left:1px dashed '.$color.';padding-left:7px">
								<div style="font-weight:bold;font-size:11px;background:'.$color.';width:387px;margin-top:10px">RECIB&Iacute:</div>
								<div>
									<div> </div>
								</div>
								<div style="font-weight:bold;font-size:11px;background:'.$color.';width:387px;margin-top:10px">FIRMA:</div>
								<div style="width:387px">
									<div> </div>
								</div>
								<div style="font-weight:bold;font-size:11px;background:'.$color.';width:387px;margin-top:10px">FECHA:</div>
								<div>
									<div> </div>
								</div>
								<div style="font-weight:bold;font-size:11px;background:'.$color.';width:387px;margin-top:10px">PERIODO A DECLARAR:</div>
								<div style="width:387px">
									<div> </div>
								</div>

							</td>
						</tr>
					</table>
				</div>

				<div style="font-size:8px;padding-left:100px">
					<table>
						<tr>
							<td></td>
							<td style="padding-left:50px;border-collapse:collapse"></td>
						</tr>
					</table>
				</div>
			</body>
		</html>';

		$content = $htmlN.$htmlC.$htmlX;

		require_once('../../modulos/phpmailer/sendMail.php');

		$mail->Subject = "Recibo de pago";
		$mail->AltBody = "NetwarMonitor";
		$mail->MsgHTML($content);
		/*$mail->AddAttachment('../../modulos/facturas/'. $uid .'.xml');
		$mail->AddAttachment('../../modulos/facturas/'. $uid .'.pdf'); */
		$correo = explode(';', $correo);
		foreach ($correo as $key => $value) {
			$mail->AddAddress($value, $value);
		}

		@$mail->Send();

		return  array('estatus' => true );
	}

	public function enviarTicket($idVenta,$correo,$asunto,$mensaje){
		//echo $asunto;
		//echo 'oekdoekoekokfokeofkoekfe';
			$organizacion = $this->datosorganizacion();

			$datosSucursal = $this->datosSucursal($idVenta);
			$venta = $this->datosventa($idVenta);
			$productos = $this->productosventa($idVenta);
			$impuestos_venta = json_decode($venta[0]['jsonImpuestos']);
			$impuestos_venta = $this->object_to_array($impuestos_venta);
			$pagos = $this->pagos($idVenta);
			$content='';
            //print_r($datosSucursal);
			/*$content .='<div id="contenedor" style="font-family:Euphemia UCAS; font-color:black;">';
			$content .='   <div align="center" style="height:80px;background:#a1b62c;">';
			$content .='    <img src="https://www.netwarmonitor.com/assets/img/netwarmonitor.png?1435793573" alt="" align="center" style="padding-top:2%">';
			$content .='</div>'; */

			$imagen='../../netwarelog/archivos/1/organizaciones/'.$organizacion[0]['logoempresa'];
			$imagesize=getimagesize($imagen);
			$porcentaje=0;
			if($imagesize[0]>200 && $imagesize[1]>90){
				if($imagesize[0]>$imagesize[1]){
					$porcentaje=intval(($imagesize[1]*100)/$imagesize[0]);
					$imagesize[0]=200;
					$imagesize[1]=(($porcentaje*200)/100);
				}else{
					$porcentaje=intval(($imagesize[0]*100)/$imagesize[1]);
					$imagesize[0]=200;
					$imagesize[1]=(($porcentaje*200)/100);
				}
			}
			$src="";
			if($imagen!="" && file_exists($imagen)){
				$src='<img src="'.$imagen.'" style="width:'.$imagesize[0].'px;height:'.$imagesize[1].'px;display:block;margin:0 auto 0 auto;"/>';
			}

            $content.='<div id="logo">'.$src.'</div>';
            $content.='<table align="center">
                            <tbody>
                                <tr>
                                    <td>
                                        <div id="receipt_header" style="text-align:center;">
                                            <div id="company_name">'.$organizacion[0]['nombreorganizacion'].'</div>
                                        <!--    <div id="company_address">Av. 18 de Mzo. 287 Guadalajara,Jalisco</div> -->
                                        <div id="company_address"></div>

                                        <!--<div id="sale_receipt">1490637193___ervfd.png</div> -->
                                        <div id="sucursal">Sucursal:'.$datosSucursal[0]['nombre'].'</div>
                                        <div id="sale_receipt">Ticket de compra</div>
                                        <div id="sale_time">'.$venta[0]['fecha'].'</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="receipt_general_info" style="text-align:center;">
                                            <div id="customer">Cliente:'.$venta[0]['cliente'].'</div>
                                            <div id="sale_id">Id Venta:'.$venta[0]['folio'].'</div>
                                            <div id="employee">Cajero:'.$venta[0]['empleado'].'</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>';
            $content.='<table id="receipt_items" border="0" align="center">
                            <tbody><tr>
                                        <th style="width:16%;text-align:center;">Cantidad</th>
                                        <th style="width:25%;">Producto</th>
                                        <th style="width:17%;text-align:left;">Total</th>
                                    </tr>';
                        $sub = 0;
                        $descDesc = '';
                        foreach ($productos as $key => $value) {
                         $content.= "<tr>";
                         $content.= "<td style='text-align:center;'>".$value['cantidad']."</td>";
                         if($value['montodescuento'] > 0){
                            $descDesc  = '[Precio: $'.number_format($value['precio'],2).', Descuento: $'.number_format($value['montodescuento'],2).'/'.$value['tipodescuento'].$value['descuento'].']';
                         }
                         $content.= "<td style='text-align:center;' class='textWrap'><span class='short_name'>".$value['nombre'].' '.$descDesc."</td>";
                         $content.= "<td style='text-align:left;'>$".number_format(($value['cantidad'] * $value['preciounitario']),2)."</td>";
                         $content.= "</tr>";
                         $sub +=($value['cantidad'] * $value['preciounitario']);
                         $descDesc = '';
                        }

			$content .='<tr>
						<td colspan="2" style="text-align:right;border-top:2px solid #000000;"><b>Subtotal:</b></td>
						<td colspan="1" style="text-align:right;border-top:2px solid #000000;">$'.number_format($sub,2,".",",").' '.$venta[0]['codigo'].'</td>
						</tr>';
						///descuento
						if($venta[0]['descuento'] > 0){
							$content .='<tr><td colspan="2" style="text-align:right;"><b>Descuento:</b></td>';
							$content .='<td colspan="1" style="text-align:right;">$'.number_format($venta[0]['descuento'],2).' '.$venta[0]['codigo'].'</td></tr>';
						}
					foreach ($impuestos_venta as $key2 => $value2) {
						//ech$content.=o 'CCCC'.$key;
						$content.= '<tr>';
						$content.= '<td colspan="2" style="text-align:right;">'.$key2.'</td>';
						$content.= '<td colspan="1" style="text-align:right;">$'.number_format($value2,2).' '.$venta[0]['codigo'].'</td>';
						$content.= '</tr>';
						$totalimpuestos+=$value2;
					}
			$content.='<tr>
							<td colspan="2" style="text-align:right;"><b>Total:</b></td>
							<td colspan="1" style="text-align:right">$'.number_format((($sub+$totalimpuestos)-$venta[0]['descuento']),2,".",",").' '.$venta[0]['codigo'].'</td>
						</tr>';
			$content.='<tr><td colspan="6">&nbsp;</td></tr>';

				foreach ($pagos as $key => $value) {
					$content.= '<tr><td colspan="2" style="text-align:right;"><b>'.$value['nombre'].'</b></td>';
					$content.= '<td colspan="1" style="text-align:right">$'.number_format($value['monto'],2).' '.$venta[0]['codigo'].'</td></tr>';
				}

			$content.='<tr>
						<td colspan="2" style="text-align:right;"><b>Cambio</b></td>
						<td colspan="1" style="text-align:right">$'.number_format($venta[0]['cambio'],2,".",",").' '.$venta[0]['codigo'].'</td>
					 </tr>';
			$content .='</table>';

			$configTikcet = "SELECT ticket_config FROM pvt_configura_facturacion WHERE id=1";
			$res = $this->queryArray($configTikcet);
			if($res['rows'][0]['ticket_config']>0){

					$url="netwarmonitor.mx/clientes/".$_SESSION['accelog_nombre_instancia']."/kiosko";
					if(strlen($url) >50)
					{
						//echo $url;
						/*$url1 = substr($url, 0,50);
						$url2 = substr($url, 51);

						echo $url1."</br>";
						echo $url2; */
					}else
					{
						//echo $url;
					}
				$longuitud=strlen($_SESSION['accelog_nombre_instancia']);
				$codinstancia=$_SESSION['accelog_nombre_instancia'][0].$_SESSION['accelog_nombre_instancia'][$longuitud-1];

				$fecha=str_replace('-', '', $venta[0]['fecha'] );
				$fecha=str_replace(':', '', $fecha);
				$fecha=str_replace(' ', '', $fecha);
		//echo "Codigo sin convertir:".$codinstancia.$fecha.$venta->folio.";";
				//$codigoHex=base64_encode($codinstancia.$fecha.$venta->folio);
				$codigoHex = $codinstancia.dechex($fecha.$venta[0]['folio']);
				$codigoFactura=$codigoHex;
				//echo $codigoFactura;
                $rutaQr = $this->creaQR($url,$idVenta);
                $srcQr='<img src="'.$rutaQr.'" style="width:100px;height:100px;display:block;margin:0 auto 0 auto;"/>';
				$content.='<div id="codigoFac" align="center" style="background:#CFCFC4;">
								<div>Para obtener tu factura ingresa a la direccion</div>
								<div id="urlFac">'.$url.'</div>
								<div> Ingresando el Siguiente codigo:</div>
								<div id="codigoFac" style="font-size:20px"> <strong>'.$codigoFactura.'</strong></div>
                                <div id="codigoQr">'.$srcQr.'</div>
							</div>';

			}
		   /* $content .='<div id="promocion" align="center" style="background:#a1b62c;font-size:12px;">
							<div><img src="https://www.netwarmonitor.com/assets/img/netwarmonitor.png?1435793573" alt="" align="center" style="padding-top:2%"></div>
							<div>¿Deseas tener un punto de venta igual?</div>
							<div>Av. 18 de Marzo #287, La Nogalera</div>
							<div>CP 44470, Guadalajara ,Jalisco</div>
							<div>Tel: 4849384948758</div>
						</div>'; */
			$content .='</div>';



			require_once('../../modulos/phpmailer/sendMail.php');

			if(is_numeric($idVenta)){
				$mail->Subject = "Ticket de Venta";
				$mail->AltBody = "NetwarMonitor";
				$mail->MsgHTML($content);
			}else{
				$mail->Subject = $asunto;
				$mail->AltBody = "NetwarMonitor";
				$mail->MsgHTML($mensaje);
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $idVenta .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $idVenta .'.pdf');
			}




			$correo = explode(';', $correo);
			foreach ($correo as $key => $value) {
				$mail->AddAddress($value, $value);
			}

			@$mail->Send();

			return  array('estatus' => true );

	}

	public function formatofecha($fecha){
	
			list($anio,$mes,$rest)=explode("-",$fecha);
			list($dia,$hora)=explode(" ",$rest);

			return $dia."/".$mes."/".$anio." ".$hora;
	}
	public function gridFacturas($limit){
		//$query = "SELECT * from app_respuestaFacturacion ORDER BY id DESC ".$limit;
		$query = "SELECT rf.*, IFNULL((SELECT SUM(pr.abono*p.tipo_cambio) FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_pago WHERE pr.id_tipo = 1 AND pr.id_documento = rf.id AND p.cobrar_pagar = 0),0) AS saldocxc,  suc.nombre as
 sucursal , usu.usuario as empleado  from app_respuestaFacturacion rf
		left join app_pos_venta ven on rf.idSale=ven.idVenta
		left join mrp_sucursal suc on suc.idSuc=ven.idSucursal
	left join accelog_usuarios usu on usu.idempleado=ven.idEmpleado
		ORDER BY id DESC ".$limit;
		//echo $query;
		$res = $this->queryArray($query);
		//print_r($res);
		return $res['rows'];
	}
	public function buscarFacturas($desde,$hasta,$tipo,$empleado,$sucursal){
		$filtro = 'where 1 = 1';
		$inicio = $desde;
		$fin = $hasta;

		if($fin!="")
		{
			list($a,$m,$d)=explode("-",$fin);
			$fin=$a."-".$m."-".((int)$d+1);
		}


		if($inicio!="" && $fin=="")
		{
			$filtro.=" and  rf.fecha >= '".$inicio."' ";
		}
		if($fin!="" && $inicio=="")
		{
			$filtro.=" and  rf.fecha <= '".$fin."' ";
		}
		if($inicio!="" && $fin!="")
		{
			$filtro.=" and  rf.fecha <= '".$fin."' and   rf.fecha >= '".$inicio."' ";
		}

		if($tipo == 1){
			//todas
			$filtro.="";
		}
		if($tipo == 2){
			//factura
			$filtro.=" and  rf.tipoComp = 'F' ";
		}
		if($tipo == 3){
			//notas
			$filtro.=" and  rf.tipoComp = 'C' ";
		}
		if($tipo == 4){
			//honorarios
			$filtro.=" and  rf.tipoComp = 'H' ";
		}
		if($tipo == 5){
			//notas
			$filtro.=" and  rf.tipoComp = 'P' ";
		}
		if($tipo == 6){
			//honorarios
			$filtro.=" and  rf.tipoComp = 'T' ";
		}
		if($empleado!=0){
			$filtro .=' and ven.idEmpleado='.$empleado;
		}
		if($sucursal!=0){
			$filtro.=' and ven.idSucursal='.$sucursal;
		}

		$select = "SELECT rf.*, IFNULL((SELECT SUM(pr.abono*p.tipo_cambio) FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_pago WHERE pr.id_tipo = 1 AND pr.id_documento = rf.id AND p.cobrar_pagar = 0),0) AS saldocxc,suc.nombre as
 	sucursal , usu.usuario as empleado   from app_respuestaFacturacion rf
		left join app_pos_venta ven on rf.idSale=ven.idVenta
		left join mrp_sucursal suc on suc.idSuc=ven.idSucursal
		left join accelog_usuarios usu on usu.idempleado=ven.idEmpleado ".$filtro." ORDER BY id DESC ";
		$resSel = $this->queryArray($select);
		//echo $select;
/*		foreach ($resSel['rows'] as $key => $value) {
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}*/

		return $resSel['rows'];
	}
	function muestraMasFact($limit){

		$query = "SELECT rf.*, IFNULL((SELECT SUM(pr.abono*p.tipo_cambio) FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_pago WHERE pr.id_tipo = 1 AND pr.id_documento = rf.id AND p.cobrar_pagar = 0),0) AS saldocxc,suc.nombre as
 	sucursal , usu.usuario as empleado  from app_respuestaFacturacion rf left join app_pos_venta ven on rf.idSale=ven.idVenta
		left join mrp_sucursal suc on suc.idSuc=ven.idSucursal
		left join accelog_usuarios usu on usu.idempleado=ven.idEmpleado  ORDER BY id DESC ".$limit;
		//echo $query;
		$resSel = $this->queryArray($query);
		//print_r($res);


/*		foreach ($resSel['rows'] as $key => $value) {
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}*/

		return $resSel['rows'];
	}

	public function sumaImportesFacturas($uuid)
	{
		$myQuery = "SELECT IFNULL(SUM(m.Importe),0) AS SumImportes
					FROM cont_movimientos m
					INNER JOIN cont_polizas p ON p.id = m.IdPoliza
					WHERE p.activo = 1 AND m.Activo = 1 AND m.Factura LIKE '%$uuid%' AND m.TipoMovto = 'Abono' AND p.idtipopoliza = 1";
		$importes = $this->query($myQuery);
		$importes = $importes->fetch_assoc();
		return $importes['SumImportes'];
	}

	public function agregaPedido($idProducto){
		$idPedido = substr($idProducto, 3);

		$query = "SELECT cp.origen,cp.status,p.id as idProducto ,p.codigo, pp.cantidad, pp.caracteristicas,pp.tipoDes,pp.descuentoCantidad, pp.precio from cotpe_pedido_producto pp, app_productos p, cotpe_pedido cp where pp.idProducto=p.id and cp.id=pp.idPedido and idPedido=".$idPedido;
		$res1 = $this->queryArray($query);
		if($res1['rows'][0]['origen']=='1' &&  $res1['rows'][0]['status']=='5'){
			$resp['idPedido'] = 'Vendido';
			return $resp;
			exit();
		}
		//print_r($res1['rows']);
		//exit();
		$price = 0;
		foreach ($res1['rows'] as $key => $value) {
			$resp0 = $this->agregaProducto($value['codigo'],$value['cantidad'],$value['caracteristicas'],'','','');
			if($value['tipoDes']=='%'){
				//echo 'A';
				$xsd = ($value['precio'] * 100);
				//$xsd = $value['precio'];
				//echo $xsd;
				$price = $xsd / (100 - $value['descuentoCantidad']) ;
				//echo $price;
				//echo $price;
			}else{
				//echo 'b';
				$price = $value['precio'] + $value['descuentoCantidad'];
			}
			if($value['caracteristicas']!=''){
				$recalcula = $this->recalcula($value['idProducto'].'_'.$value['caracteristicas'],$value['cantidad'],$price);
			}else{
				$recalcula = $this->recalcula($value['idProducto'],$value['cantidad'],$price);
			}

			if($value['descuentoCantidad']!='' && $value['descuentoCantidad'] > 0){
					 $resultado = $this->cambiaCantidad($value['idProducto'], $value['descuentoCantidad'], $value['tipoDes']);
				}
			$price = 0;
		}
		$query44 = "SELECT descCant  from cotpe_pedido where id=".$idPedido;
		$res44 = $this->queryArray($query44);

		//echo $res44['rows'][0]['descCant'].'<jfjfjfjfjfjf';
		if($res44['rows'][0]['descCant']>0 && $res44['rows'][0]['descCant']!=''){
			$erx = $this->descuentoGeneral($res44['rows'][0]['descCant']);
		}
		//sleep(60);
		$resp['idPedido'] = $idPedido;
		$_SESSION['caja']['pedido'] = $idPedido;

		//print_r($_SESSION['caja']);
		return $resp;
	}
	////////Aviones 
	public function cargarOrdenServicio($idOrden){
		$sel = 'SELECT p.codigo, p.id,s.cantidad, s.precio from app_productos p, app_cotizacion_viaje_prods s
				where s.idProducto=p.id and s.idSolicitud='.$idOrden;
				//echo $sel;
		$res = $this->queryArray($sel);
		//print_r($res);

		foreach ($res['rows'] as $key => $value) {
			$resp0 = $this->agregaProducto($value['codigo'],$value['cantidad'],'','','','');
			$recalcula = $this->recalcula($value['id'],$value['cantidad'],$value['precio']);
		}
		$resp['idOrden'] = $idOrden;
		$_SESSION['caja']['idorden'] = $idOrden;

		//print_r($_SESSION['caja']);
		return $resp;
	}

		////Cambia Estatus de un pedido a vendido
	public function estatusPedido($idPedido,$idVenta){
		$query1 = 'UPDATE cotpe_pedido set status=5, idVenta="'.$idVenta.'" where id='.$idPedido;
		$res1 = $this->queryArray($query1);
		$sql = "UPDATE	app_inventario_movimientos
						SET		estatus = '0'
						WHERE	referencia = 'Apartado Pedido $idPedido';";
		$res2 = $this->queryArray($sql);
		return array('resp' => true );

	}
	public function estatusOrden($idPedido,$idVenta){
		$query1 = 'UPDATE app_solicitud_viaje set estatus=5, idVenta="'.$idVenta.'" where id='.$idPedido;
		$res1 = $this->queryArray($query1);
		return array('resp' => true );

	}
	public function configTikcet(){
			$configTikcet = "SELECT ticket_config FROM pvt_configura_facturacion WHERE id=1";
			$res = $this->queryArray($configTikcet);
			return $res['rows'][0]['ticket_config'];
	}
	public function cancelaFactura($id){
			$sele = "SELECT id_sale from app_respuestaFacturacion where id=".$id;
			$res1 = $this->queryArray($sele);


			$sel = 'SELECT * from app_pagos_relacion where id_documento='.$id.' and id_tipo=1 and activo=1';

			$res= $this->queryArray($sel);

			if($res['total']>0){
				$JSON = array('success' =>0,
				'error'=>50,
				'mensaje'=>'La Factura cuenta con abonos a una cuenta, no se puede cancelar.');
				echo json_encode($JSON);
				exit();
			}
			$selNC = 'SELECT * from app_notas_credito where idFactura='.$id;
			$resNC = $this->queryArray($selNC);
			if($resNC['total']>0){
				$JSON = array('success' =>0,
				'error'=>50,
				'mensaje'=>'La Factura cuenta con notas de credito, no se puede cancelar.');
				echo json_encode($JSON);
				exit();
			}

		  $cancelFac=1;
		  $query = "select a.folio, a.version, a.idComprobante, b.rfc, b.cer, b.llave, b.clave, a.serieCsdEmisor from app_respuestaFacturacion a, pvt_configura_facturacion b where a.id='".$id."' LIMIT 1";

		  $res = $this->queryArray($query);
		  if($res['total']>0){
			$rfccancel=  $res['rows'][0]['rfc'];
			$cancelFolio=  $res['rows'][0]['folio'];
			$cancelID= $res['rows'][0]['idComprobante'];
			$elkey= $res['rows'][0]['llave'];
			$elcer= $res['rows'][0]['cer'];
			$clave= $res['rows'][0]['clave'];

			$pac = $res['rows'][0]['serieCsdEmisor'];
			$strUUID = $res['rows'][0]['folio'];
			$strVersion = $res['rows'][0]['version'];
			//$cancelXMLopen = fopen('../../modulos/facturas/'.$cancelFolio.'.xml', "r");
		   // $cancelXML = fread($cancelXMLopen, filesize('../../modulos/facturas/'.$cancelFolio.'.xml'));
		   // fclose($cancelXMLopen);
		   // $val=explode(' ', $cancelXML);
		   /* if($val[0]!='<?xml'){
			  $JSON = array('success' =>0,
			  'error'=>201,
			  'mensaje'=>'El XML de la factura a cancelar esta mal formado.');
			  echo json_encode($JSON);
			  exit();
			}  */
		  }else{
			$JSON = array('success' =>0,
			'error'=>200,
			'mensaje'=>'No se encontro el XML de la factura solicitada.');
			echo json_encode($JSON);
			exit();
		  }

		  require_once('../../modulos/SAT/config.php');
		  //require_once('../../modulos/lib/nusoap.php');
		  //require_once('../../modulos/SAT/funcionesSAT.php');
		//if($pac==3){
			require_once('../../modulos/wsinvoice/cancelInvoice.php');
		/*}else{
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT.php');
		} */

	}
	public function cancelaFacturaEstatus($id){
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");

		$query = "UPDATE app_respuestaFacturacion set borrado=1, fecha_cancelacion='".$fechaactual."'  where id=".$id;
		$res = $this->queryArray($query);

		$deletNota = 'DELETE from app_notas_credito where idNota='.$id;
		$delreN = $this->queryArray($deletNota);
		//ch
		// obtener el id de venta con id de folio de factura
		$query1 = "SELECT r.idSale, r.fecha, p.idCliente, p.monto, r.folio, cadenaOriginal, tipoComp, origen  from app_respuestaFacturacion r left join app_pos_venta p on p.idVenta = r.idSale where r.id = '".$id."';";
		$resultId = $this->queryArray($query1);
		$idSale 		= $resultId['rows'][0]['idSale'];
		$fecha 			= $resultId['rows'][0]['fecha'];
		$cliente 		= $resultId['rows'][0]['idCliente'];
		$monto 			= $resultId['rows'][0]['monto'];
		$cadenaOriginal = $resultId['rows'][0]['cadenaOriginal'];
		$tipoComp 		= $resultId['rows'][0]['tipoComp'];
		$origen 		= $resultId['rows'][0]['origen'];
		$uid 			= $resultId['rows'][0]['folio'];

		if($idSale==0){
			$updatePendi ='UPDATE app_pendienteFactura set facturado=0, id_respFact="0" where id_respFact='.$id;
			$reswe = $this->queryArray($updatePendi);
		}else{
			// verifica si existe en pendientes
			$query2 = "SELECT * FROM app_pendienteFactura where id_sale = '".$idSale."';";
			$resultP = $this->queryArray($query2);
			$total 	 = $resultP['total'];

			if($total>0){
				// si ya existe se actualiza status
				$query3 = "UPDATE app_pendienteFactura set facturado=0 where id_sale=".$idSale;
				$res = $this->queryArray($query3);
			}else{
				// si no existe se crea nuevo registro
				$query4 = "INSERT into app_pendienteFactura (id_sale,fecha,id_cliente,monto,facturado,cadenaOriginal,tipoComp,id_respFact,origen) values ('".$idSale."','".$fecha."','".$cliente."','".$monto."','0','".$cadenaOriginal."','".$tipoComp."','0','".$origen."')";
				$res = $this->queryArray($query4);
			}
		}

		if($tipoComp=='P'){
			$del = "DELETE from cont_facturas_relacion where uuid_pago='".$uid."';";
			$this->queryArray($del);
		}

		//ch fin
		//// Crea cuenta por cobrar si es a credito
		$selFp = "SELECT * from app_pos_venta_pagos where idVenta=".$idSale;
		$resFp = $this->queryArray($selFp);
		$pagoCredito = 0;
		foreach ($resFp['rows'] as $key => $value) {
			if($value['idFormapago'] == 6){
				$pagoCredito += (float) $value['monto'];
			}

		}

		if($pagoCredito > 0){
			$selcli = "SELECT idCliente,moneda,tipo_cambio from app_pos_venta where idVenta=".$idSale;
			$resCli = $this->queryArray($selcli);
			$cliente = $resCli['rows'][0]['idCliente'];
			$moneda = $resCli['rows'][0]['moneda'];
			$tipoCambio = $resCli['rows'][0]['tipo_cambio'];
			if($moneda == 0){
				$moneda =1;
			}
			if($tipoCambio == 0){
				$tipoCambio = 1;
			}
			$pagoCredito = $pagoCredito / $tipoCambio;

			$referencia = "Factura Cancelada";
			$query = "INSERT INTO app_pagos(cobrar_pagar,id_prov_cli,cargo,fecha_pago,concepto,id_forma_pago,id_moneda,tipo_cambio) values('0','".$cliente."','".str_replace(",", "", $pagoCredito)."','".$fechaactual."','Ticket Caja ".$idSale.":".$referencia."','6','".$moneda."','".$tipoCambio."')";
							//echo $query;
			$resCargo = $this->queryArray($query);

		}

		//Actualiza el estatus de la factura en la tabla cont_facturas
		$this->query("UPDATE cont_facturas SET cancelada = 1 WHERE uuid LIKE '%".$uid."%'; ");
		//Inactiva las polizas relacionadas a esa factura
		$this->query("UPDATE cont_polizas SET activo = 0 WHERE origen LIKE 'Venta%' AND idorigen = $idSale; ");
		//Inactiva los movimientos relacionados a esa factura
		$this->query("UPDATE cont_movimientos SET Activo = 0 WHERE Factura LIKE '%".$uid."%'; ");



		//// TRANS
			//uuid = factura -> tran_ordenservicio			
			$sql = 'SELECT idordenservicio FROM tran_ordenservicio WHERE factura = "'.$uid.'";';
			$result = $this->queryArray($sql);
			
			if($result['total'] > 0){
				//$idos = $result['rows'][0]['idordenservicio'];
				foreach ($result['rows'] as $k => $v) {
					$idos = $v['idordenservicio'];
					//Reinicio la order de servicio
					$this->query("UPDATE tran_ordenservicio SET estatus = 5, viaje_estatus = 'NULL', factura = 0, venta = 0 WHERE idordenservicio = ".$idos."; ");
					// STATUS EN ORDEN DE SERVICIO
				}				
				
			}			
		//// TRANS FIN

		return   array('respuesta' => 1);
	}

	public function buscarVentasPendientes($desde,$hasta,$empleado,$sucursal){
		$filtro = '';
		$inicio = $desde;
		$fin = $hasta;


		if($fin!="")
		{
			list($a,$m,$d)=explode("-",$fin);
			$fin=$a."-".$m."-".((int)$d+1);
		}


		if($inicio!="" && $fin=="")
		{
			$filtro.=" and  p.fecha >= '".$inicio."' ";
		}
		if($fin!="" && $inicio=="")
		{
			$filtro.=" and  p.fecha <= '".$fin."' ";
		}
		if($inicio!="" && $fin!="")
		{
			$filtro.=" and  p.fecha <= '".$fin."' and   p.fecha >= '".$inicio."' ";
		}
		if($empleado!=0){
			$filtro .=' and ven.idEmpleado='.$empleado;
		}
		if($sucursal!=0){
			$filtro.=' and ven.idSucursal='.$sucursal;
		}


		$select = "SELECT p.*, if(p.id_cliente !='NULL',c.nombre, 'Publico en General') as cliente, suc.nombre as sucursal , usu.usuario as empleado , ven.monto
			from app_pendienteFactura p
			left join comun_cliente c on p.id_cliente=c.id
			left join app_pos_venta ven on ven.idVenta = p.id_sale
			left join mrp_sucursal suc on suc.idSuc=ven.idSucursal
			left join accelog_usuarios usu on usu.idempleado=ven.idEmpleado where 0=0 ".$filtro;
		//echo 'select='.$select;
		//exit();
		$resSel = $this->queryArray($select);


		foreach ($resSel['rows'] as $key => $value) {
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}

		return $resSel['rows'];
	}
	public function gridPendienteFact(){
		$select  = "SELECT p.*, if(p.id_cliente !='NULL',c.nombre, 'Publico en General') as cliente, suc.nombre as sucursal , usu.usuario as empleado , ven.monto
			from app_pendienteFactura p
			left join comun_cliente c on p.id_cliente=c.id
			left join app_pos_venta ven on ven.idVenta = p.id_sale
			left join mrp_sucursal suc on suc.idSuc=ven.idSucursal
			left join accelog_usuarios usu on usu.idempleado=ven.idEmpleado;";
		$res = $this->queryArray($select);

		return $res['rows'];
	}
	public function comunFactRfcs(){

			//$selComFac = "SELECT * FROM comun_facturacion";
			$selComFac = 'SELECT cf.* FROM comun_facturacion cf , comun_cliente cl where cf.nombre=cl.id and borrado=0';
			$result = $this->queryArray($selComFac);

			return $result['rows'];
	}
	public function clientePenFac($id){
		$selec = "SELECT * from app_pendienteFactura  where id=".$id;
		$res1 = $this->queryArray($selec);

		if($res1['rows'][0]['id_cliente']!=''){
			$select2 = 'SELECT id from comun_facturacion where nombre='.$res1['rows'][0]['id_cliente'].' limit 1';
			$res2 = $this->queryArray($select2);

			return $res2['rows'][0]['id'];
		}else{
			return 0;
		}


	}
	function getCaracteristicas()
  {
      $sql = "SELECT p.id ID_P, h.id ID_H, p.nombre CRARACTERISTICA_PADRE, h.nombre CARACTERISTICA_HIJA
              FROM    app_caracteristicas_padre p
              INNER JOIN  app_caracteristicas_hija h ON p.id = h.id_caracteristica_padre
              ORDER BY ID_P, ID_H;";
      $res = $this->queryArray($sql);
      return $res['rows'];
  }
	private function caract2nombre($caracteristicas ,$caract)
	{
		$caract = $this->caract2id($caract);
		$caracteristicasProductoTmp = explode( '_' , $caract );
		$caracteristicasEtiqueta = '';
		foreach ($caracteristicasProductoTmp as $key => $val) {
			$caracteristica = explode( 'H' , $val);
			$caracteristica = $this->buscarCaracteristica($caracteristicas , $caracteristica);
			$caracteristicasEtiqueta .= "{$caracteristica['CRARACTERISTICA_PADRE']}:{$caracteristica['CARACTERISTICA_HIJA']} ," ;
		}
		return $caracteristicasEtiqueta;
	}
	private function caract2id($caract){
		return preg_replace(["/=>/", "/,/", "/'/" ], ["H", "_", ""], $caract);
	}
	private function id2caract($id)
	{
		return preg_replace(['/H/','/_/','/(\d+)/' ], [ '=>',',', "'\${1}'"], $id);
	}
	private function buscarCaracteristica($caracteristicas , $caracteristica)
	{
		foreach ($caracteristicas as $key => $value) {
			if($value['ID_P'] == $caracteristica[0] && $value['ID_H'] == $caracteristica[1] )
				return $value;
		}
	}
	public function getKitTemplate($idProducto)
	{
		$que = "SELECT id,ruta_imagen, nombre, antibiotico, IF(tipo_producto = '6' , true, false) esKit  from app_productos where codigo='".$idProducto."'";
		$res = $this->queryArray($que);
		$idP = $res['rows'][0]['id'];
		$idAlmacen = $this->obtenAlm();
		$sql = "SELECT	k.id_kit idKit, pk.codigo idCode, p.id idProduct, IF(c.id IS NOT NULL, 1, 0 ) car, p.series ser, p.lotes lot, p.codigo code, p.nombre name, k.cantidad quantity, IF( s.id IS NULL , SUM(i.cantidad) , i.cantidad ) stock,

								CONCAT( '[ ',
								( IF( i.caracteristicas != '\'0\'' ,
								GROUP_CONCAT( CONCAT( '{ \"id\":\"' ,i.caracteristicas, '\", \"name\":' , '\"\"' , ', \"stock\":' , i.cantidad , ', \"quantity\":0 }' ) ) ,
								''
							) ) , ' ]' ) characteristics,

								CONCAT( '[ ',
								( IF( s.id IS NOT NULL ,
								GROUP_CONCAT( CONCAT( '{ \"id\":\"' ,s.id, '\", \"text\":\"' , s.serie , '\" }' ) ) ,
								''
								) ) , ' ]' ) optionsSeries,

								'[  ]' series,

								CONCAT( '[ ',
								( IF( i.lote != 0 ,
								GROUP_CONCAT( CONCAT( '{ \"id\":\"' ,i.lote, '\", \"name\":\"' , l.no_lote , '\", \"stock\":' , i.cantidad , ', \"quantity\":0 }' ) ) ,
								''
								) ) , ' ]' ) batches,
								false done

						FROM	com_kitsXproductos k
						LEFT JOIN	app_productos pk ON k.id_kit = pk.id
						LEFT JOIN	app_productos p ON k.id_producto = p.id
						LEFT JOIN app_producto_caracteristicas c ON k.id_producto = c.id_producto
						LEFT JOIN	app_inventario i ON p.id = i.id_producto
						LEFT JOIN	app_producto_serie s ON p.id = s.id_producto  AND estatus = '0'
						LEFT JOIN	app_producto_lotes l ON i.lote = l.id
						WHERE	k.id_kit = '$idP' AND i.id_almacen = '$idAlmacen'
						GROUP BY	p.id
						;";

		$resKit = $this->queryArray($sql);
		foreach ($resKit['rows'] as $key => $value) {
			$resKit['rows'][$key]['series'] = json_decode( $value['series'] , true);
			$resKit['rows'][$key]['optionsSeries'] = json_decode( $value['optionsSeries'] , true);
			$resKit['rows'][$key]['batches'] = json_decode( $value['batches'] , true);

			$resKit['rows'][$key]['characteristics'] = json_decode( $value['characteristics'] , true );
			$caracteristicas = $this->getCaracteristicas();
			foreach ($resKit['rows'][$key]['characteristics'] as $k => $v){
				// var_dump($resKit['rows'][$key]['characteristics'][$k]);die;
				$resKit['rows'][$key]['characteristics'][$k]['name'] = $this->caract2nombre($caracteristicas ,$v['id']);
			}
		}
		return $resKit['rows'];
	}
	public function obtenCaracteristicas($idProducto){
			$idAlmacen = $this->obtenAlm();
			$tieneAlgo = 0;
			$que = "SELECT id,ruta_imagen, nombre, antibiotico, IF(tipo_producto = '6' , true, false) esKit, tipo_producto  from app_productos where codigo='".$idProducto."'";
			$res = $this->queryArray($que);
			$imagen = $res['rows'][0]['ruta_imagen'];
			$nombreP = $res['rows'][0]['nombre'];
			$idP = $res['rows'][0]['id'];
			$antibiotico = $res['rows'][0]['antibiotico'];
			$esKit = $res['rows'][0]['esKit'];
			$tpr = $res['rows'][0]['tipo_producto'];

			$arrTipo =  array('tpr'=> $tpr, 'nombre' => $nombreP,'id' => $idProducto,'imagen' => $imagen );
			$resKit = false;
			if($esKit) {
				$sql = "SELECT	k.id_kit idKit, pk.codigo idCode, p.id idProduct, IF(c.id IS NOT NULL, 1, 0 ) car, p.series ser, p.lotes lot, p.codigo code, p.nombre name, k.cantidad quantity, IF( s.id IS NULL , SUM(i.cantidad) , i.cantidad ) stock,

										CONCAT( '[ ',
										( IF( i.caracteristicas != '\'0\'' ,
										GROUP_CONCAT( CONCAT( '{ \"id\":\"' ,i.caracteristicas, '\", \"name\":' , '\"\"' , ', \"stock\":' , i.cantidad , ', \"quantity\":0 }' ) ) ,
										''
									) ) , ' ]' ) characteristics,

										CONCAT( '[ ',
										( IF( s.id IS NOT NULL ,
										GROUP_CONCAT( CONCAT( '{ \"id\":\"' ,s.id, '\", \"text\":\"' , s.serie , '\" }' ) ) ,
										''
										) ) , ' ]' ) optionsSeries,

										'[  ]' series,

										CONCAT( '[ ',
										( IF( i.lote != 0 ,
										GROUP_CONCAT( CONCAT( '{ \"id\":\"' ,i.lote, '\", \"name\":\"' , l.no_lote , '\", \"stock\":' , i.cantidad , ', \"quantity\":0 }' ) ) ,
										''
										) ) , ' ]' ) batches,
										false done

								FROM	com_kitsXproductos k
								LEFT JOIN	app_productos pk ON k.id_kit = pk.id
								LEFT JOIN	app_productos p ON k.id_producto = p.id
								LEFT JOIN app_producto_caracteristicas c ON k.id_producto = c.id_producto
								LEFT JOIN	app_inventario i ON p.id = i.id_producto
								LEFT JOIN	app_producto_serie s ON p.id = s.id_producto AND estatus = '0'
								LEFT JOIN	app_producto_lotes l ON i.lote = l.id
								WHERE	k.id_kit = '$idP' AND i.id_almacen = '$idAlmacen'
								GROUP BY	p.id
								;";

				$resKit = $this->queryArray($sql);
				foreach ($resKit['rows'] as $key => $value) {
					$resKit['rows'][$key]['series'] = json_decode( $value['series'] , true);
					$resKit['rows'][$key]['optionsSeries'] = json_decode( $value['optionsSeries'] , true);
					$resKit['rows'][$key]['batches'] = json_decode( $value['batches'] , true);

					$resKit['rows'][$key]['characteristics'] = ( json_decode( $value['characteristics'] , true ) );
					//delete duplicated characteristics
					if( count($resKit['rows'][$key]['characteristics']) ) {
						$temp_array = [];
						foreach ($resKit['rows'][$key]['characteristics'] as &$v) {
							if (!isset($temp_array[$v['id']]))
							$temp_array[$v['id']] =& $v;
						}
						$resKit['rows'][$key]['characteristics'] = array_values($temp_array);
						$tmpStock = 0;
						foreach ($resKit['rows'][$key]['characteristics'] as &$v) {
							$tmpStock +=$v['stock'];
						}
						$resKit['rows'][$key]['stock'] = $tmpStock;
					}
					$caracteristicas = $this->getCaracteristicas();
		      foreach ($resKit['rows'][$key]['characteristics'] as $k => $v){
						// var_dump($resKit['rows'][$key]['characteristics'][$k]);die;
		        $resKit['rows'][$key]['characteristics'][$k]['name'] = $this->caract2nombre($caracteristicas ,$v['id']);
					}
				}
				$tieneAlgo++;
			}



			if($imagen==''){
				$imagen='noimage.jpeg';
			}
			///Caracteristicas
			$myQuery = "SELECT e.id as idcp, e.nombre as nombrecp
			FROM  app_producto_caracteristicas d
			LEFT JOIN app_caracteristicas_padre e on e.id=d.id_caracteristica_padre
			WHERE d.id_producto='".$res['rows'][0]['id']."' order by idcp;";
			$producto = $this->queryArray($myQuery);

			if($producto['total'] > 0){
				foreach ($producto['rows'] as $key => $value) {
					$selec = "SELECT id_caracteristica_padre,id,nombre from app_caracteristicas_hija where activo=1 and id_caracteristica_padre=".$value['idcp'];
					$result = $this->queryArray($selec);

					$carac[$value['nombrecp']] = $result['rows'];
				}
				$tieneAlgo++;
			}



			//lotes
			$lotesSi = 0;
			$arrPedis=array();
			 $myQuery = "SELECT a.id,a.no_lote from app_producto_lotes a
				inner join app_inventario_movimientos b on b.id_lote=a.id
				WHERE b.id_producto='".$res['rows'][0]['id']."' group by a.id;";
				//echo $myQuery;
			$pedimentos = $this->queryArray($myQuery);
			if($pedimentos['total']>0){
				foreach ($pedimentos['rows'] as $k => $v) {


					$myQuery2="SELECT a.id, a.codigo_manual, a.codigo_sistema, a.nombre,
	@e := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = a.id AND id_producto
	 = ".$res['rows'][0]['id']." ".$carac." AND id_pedimento = 0 AND id_lote = ".$v['id']."  ) AS entradas,
	@s := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = a.id AND id_producto
	 = ".$res['rows'][0]['id']." ".$carac." AND id_pedimento = 0 AND id_lote = ".$v['id']."  ) AS salidas,
	(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
	FROM app_almacenes a WHERE a.activo = 1 and a.id='".$idAlmacen."'
	ORDER BY a.codigo_sistema;";
	//echo $myQuery2;
					$totpedis = $this->queryArray($myQuery2);
					$cant=0;
					foreach ($totpedis['rows'] as $k2 => $v2) {
						//$cant+=$v2['cantidad'];

						if($v2['cantidad']>0){
							$arrPedis[]=array('idLote'=>$v['id'].'-'.$v2['id'].'-'.$v2['cantidad'].'-#*-'.$v['no_lote'].' ('.$v2['nombre'].')', 'cantidad'=>$v2['cantidad'], 'numero'=>'Lote: '.$v['no_lote'].' - '.$v2['nombre']);
							$lotesSi = 1;
						}
					}


				}
				$tieneAlgo++;
			}

			$myQuery = "SELECT a.*, b.nombre, b.id as ida from app_producto_serie a
            inner join app_almacenes b on b.id= a.id_almacen
            where a.id_producto='$idP' AND a.estatus=0 and a.id_almacen='".$idAlmacen."'";
            //echo $myQuery;
                $series = $this->queryArray($myQuery);
                $seriesSi = 0;
                if($series['total']>0){
                    foreach ($series['rows'] as $k2 => $v2) {
                        $arrSeries[]=array('idSerie'=>$v2['id'].'-'.$v2['ida'], 'serie'=>'Serie: '.$v2['serie'].' ('.$v2['nombre'].')', 'serie2' => $v2['serie']);
                        $tieneAlgo++;
                        $seriesSi = 1;
                    }
                }else{

                }


			return array('tieneCar' => $tieneAlgo, 'cararc' => $carac, 'lotes'=> $arrPedis, 'series'=> $arrSeries, 'imagen'=> $imagen, 'nombreProd'=> $nombreP,'seriesSi'=> $seriesSi, 'lotesSi'=> $lotesSi, 'antibiotico' => $antibiotico, 'kit' => $resKit['rows'], 'tipoProd' => $arrTipo);
	}
	public function getExisCara($idProducto,$caracteristicas){

			//echo '('.$idProducto.'ñ'.$caracteristicas.')';
			$selIdPr = "SELECT id from app_productos where codigo='".$idProducto."'";
			$resIdPr = $this->queryArray($selIdPr);

			$idProducto =  $resIdPr['rows'][0]['id'];
			$caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
			$caracteristicas = trim($caracteristicas, ',');
			if($caracteristicas != '0'){
					$carac = " AND id_producto_caracteristica =\"".$caracteristicas."\" ";
			}else{
				$carac='';
			}

			$idAlmacen = $this->obtenAlm();
				 $myQuery2="SELECT a.id, a.codigo_manual, a.codigo_sistema, a.nombre,
@e := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = a.id AND id_producto
 = ".$idProducto." ".$carac." ) AS entradas,
@s := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = a.id AND id_producto
 = ".$idProducto." ".$carac." ) AS salidas,
(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
FROM app_almacenes a WHERE a.activo = 1 and a.id='".$idAlmacen."'
ORDER BY a.codigo_sistema;";



	$cantidad = 0;
				$totpedis = $this->queryArray($myQuery2);
				//print_r($totpedis);
				$cant=0;
				foreach ($totpedis['rows'] as $k2 => $v2) {
					//echo $idAlmacen.'?';
					if (preg_match("/^['".$idAlmacen."']/", $v2['codigo_sistema'])) {
						//echo 'enttit';
						$cantidad += floatval($v2['cantidad']);
					} else {
						//echo "No se encontró ninguna coincidencia.<br>";
					}
				}
			$sel = "SELECT * from app_inventario where id_producto='".$idProducto."' and id_almacen='".$idAlmacen."' and caracteristicas=\"".$caracteristicas."\"";
			//echo $sel;
			$res = $this->queryArray($sel);

					//echo '['.$cantidad.']';
				return  array('cantidadExis' => $res['rows'][0]['cantidad'] );

	}
	public function getInfoProducto($id){
		$idR = explode("_", $id);
		$query = "SELECT * FROM app_lista_precio a, app_lista_precio_prods b where b.id_lista=a.id  and a.activo=1 and b.id_producto=".$idR[0]." and  b.id_suc='".$_SESSION["sucursal"]."';";
        $result = $this->queryArray($query);

        $sql = "SELECT precio,edicion
				FROM	app_productos
				WHERE id = '$id';";
		$res = $this->queryArray($sql);

$resListPreTmp = $this->listaPreciosDe($id);
$precioBaseLista = ( count($resListPreTmp) == 0 ? $res['rows'][0]['precio'] : $resListPreTmp[0]['precio'] );
$precioBaseLista = str_replace(',','',$precioBaseLista);

		return  array('nombre' => $_SESSION['caja'][$id]->nombre , 'precio' =>$_SESSION['caja'][$id]->precio, 'importe' => $_SESSION['caja'][$id]->importe, 'listaPrecio' => $result['rows'], 'precioBaseLista' => $precioBaseLista, 'edicion'=> $res['rows'][0]['edicion']);
	}
	public function cambiaCantidad($idProducto, $descuento, $tipo,$nombre='',$promo=0) {
		//echo '('.$idProducto.')';
		 //print_r($_SESSION['caja']);
		 //exit();

		$cantidad = $_SESSION['caja'][$idProducto]->cantidad;

		$cantidad = str_replace(",", "", $cantidad);
		$_SESSION['caja'][$idProducto]->subtotal = $cantidad * $_SESSION['caja'][$idProducto]->precio;
		//$_SESSION['caja'][$idProducto]->precio_sindes = $_SESSION['caja'][$idProducto]->precio;
		//$_SESSION['caja'][$idProducto]->importe_sindes = $cantidad * $_SESSION['caja'][$idProducto]->precio;
		$_SESSION['caja'][$idProducto]->descuento = 0.0;
		$_SESSION['caja'][$idProducto]->tipodescuento = $tipo;
		$_SESSION['caja'][$idProducto]->descuento_cantidad = $descuento;
		if($nombre!=''){
			$_SESSION['caja'][$idProducto]->nombre = $nombre;
			$_SESSION['caja'][$idProducto]->comentario = $nombre;
			$_SESSION['caja'][$idProducto]->descripcion = $nombre;
		}

		if ($tipo != '' && $descuento != 0.0) {
			if ($tipo == "%") {
				if($promo==1){
					//echo 'kk';
					$xxxx = explode('[', $_SESSION['caja'][$idProducto]->nombre);
					$_SESSION['caja'][$idProducto]->nombre = $xxxx;
					$_SESSION['caja'][$idProducto]->descuento = ($_SESSION['caja'][$idProducto]->precio_sindes * $cantidad) * $descuento / 100;
					$_SESSION['caja'][$idProducto]->descuento_neto = $_SESSION['caja'][$idProducto]->descuento;
					$_SESSION['caja'][$idProducto]->nombre = $xxxx[0].' [Descuento:$'.number_format($_SESSION['caja'][$idProducto]->descuento_neto,2).']';
				}else{
					$_SESSION['caja'][$idProducto]->descuento = ($_SESSION['caja'][$idProducto]->precio * $cantidad) * $descuento / 100;
					$_SESSION['caja'][$idProducto]->descuento_neto = $_SESSION['caja'][$idProducto]->descuento;
					$_SESSION['caja'][$idProducto]->nombre = $_SESSION['caja'][$idProducto]->nombre.' [Descuento:$'.number_format($_SESSION['caja'][$idProducto]->descuento_neto,2).']';
				}

			} else if ($tipo == "$") {
				$_SESSION['caja'][$idProducto]->descuento = number_format($descuento, 2, '.', '');
				$_SESSION['caja'][$idProducto]->descuento_neto = number_format($_SESSION['caja'][$idProducto]->descuento, 2, '.', '');
				$_SESSION['caja'][$idProducto]->nombre = $_SESSION['caja'][$idProducto]->nombre.' [Descuento:$'.number_format($_SESSION['caja'][$idProducto]->descuento_neto,2).']';
			} else if($tipo == "C"){
				$_SESSION['caja'][$idProducto]->precio = 0.00;
				$_SESSION['caja'][$idProducto]->nombre = $_SESSION['caja'][$idProducto]->nombre.' [Cortesía]';
			}
		} else {
			$_SESSION['caja'][$idProducto]->descuento_neto = 0.0;
			$_SESSION['caja'][$idProducto]->nombre = preg_replace('/\[.*/', '', $_SESSION['caja'][$idProducto]->nombre);
		}

		if($promo==1){
			//echo 'ooo';
			//$_SESSION['caja'][$idProducto]->importe = ($_SESSION['caja'][$idProducto]->precio * $cantidad) - str_replace(",", "", $_SESSION['caja'][$idProducto]->descuento);
			//$_SESSION['caja'][$idProducto]->precio = ($_SESSION['caja'][$idProducto]->importe / $cantidad);
		}else{
			$_SESSION['caja'][$idProducto]->importe = ($_SESSION['caja'][$idProducto]->precio * $cantidad) - str_replace(",", "", $_SESSION['caja'][$idProducto]->descuento);
			$_SESSION['caja'][$idProducto]->precio = ($_SESSION['caja'][$idProducto]->importe / $cantidad);
		}


		$sessionArray = $this->object_to_array($_SESSION['caja']);
		$totalProductos = 0;
		foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				$stringTaxes .=$value['idProducto'].'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'-'.$value['caracteristicas'].'/';
				$totalProductos += $value['cantidad'];
			}
		}
		$this->calculaImpuestos($stringTaxes);

		return array('estatus' =>true,'productos' =>$_SESSION['caja'], 'cargos' => $_SESSION['caja']['cargos'],"count" => count($_SESSION['caja']), 'totalProductos' => $totalProductos );
	}
	////////////////////////////////////////

///////////////// ******** ---- 	listar_pedidos			------ ************ //////////////////
//////// Obtiene los pedidos de la comanda y los regresa en un Array
	// Como parametros puede recibir:
		// codigo-> codigo de la comanda
		// persona -> numero de la persona
	public function allfs22($id){
		$cadena=trim($id,',');

		require_once('../../modulos/SAT/config.php');
		date_default_timezone_set("Mexico/General");
		$fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-5 minute"));

		$query2 = "SELECT a.*, b.regimen as regimenf FROM pvt_configura_facturacion a INNER JOIN pvt_catalogo_regimen b WHERE a.id=1 AND b.id=a.regimen;";
		$rs2 = $this->queryArray($query2);
		//$rs2 = $this->conexion->siguiente($result2);

		$query3 ="SELECT * FROM pvt_serie_folio WHERE id=1;";
		$rs3 = $this->queryArray($query3);

		$query4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$rs4 = $this->queryArray($query4);
		/////Selecciona el pack
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];

		$azurian['org']['logo'] = $rs4['rows'][0]['logoempresa'];

		/* DATOS OBLIGATORIOS DEL EMISOR
		================================================================== */
		$rfc_cliente=$rs2['rows'][0]['rfc'];

		$parametros['EmisorTimbre'] = array();
		$parametros['EmisorTimbre']['RFC'] = $rs2['rows'][0]['rfc'];
		$parametros['EmisorTimbre']['RegimenFiscal'] = $rs2['rows'][0]['regimenf'];
		$parametros['EmisorTimbre']['Pais'] = $rs2['rows'][0]['pais'];
		$parametros['EmisorTimbre']['RazonSocial'] = $rs2['rows'][0]['razon_social'];
		$parametros['EmisorTimbre']['Calle'] = $rs2['rows'][0]['calle'];
		$parametros['EmisorTimbre']['NumExt'] = $rs2['rows'][0]['num_ext'];
		$parametros['EmisorTimbre']['Colonia'] = $rs2['rows'][0]['colonia'];
		$parametros['EmisorTimbre']['Ciudad'] = $rs2['rows'][0]['ciudad']; //Ciudad o Localidad
		$parametros['EmisorTimbre']['Municipio'] = $rs2['rows'][0]['municipio'];
		$parametros['EmisorTimbre']['Estado'] = $rs2['rows'][0]['estado'];
		$parametros['EmisorTimbre']['CP'] = $rs2['rows'][0]['cp'];
		$cer_cliente=$pathdc.'/'.$rs2['rows'][0]['cer'];
		$key_cliente=$pathdc.'/'.$rs2['rows'][0]['llave'];
		$pwd_cliente=$rs2['rows'][0]['clave'];

		if($rs2['rows'][0]['rfc']==''){

		$JSON = array('success' =>0,
			'error'=>1001,
			'mensaje'=>'No existen datos de emisor.');
		echo json_encode($JSON);
		exit();

		}

		  /* Datos Basicos
		  ============================================================== */
		  $azurian['Basicos']['Moneda']='MXN';
		  $azurian['Basicos']['metodoDePago']='99';
		  $azurian['Basicos']['LugarExpedicion']='Mexico';
		  $azurian['Basicos']['version']='3.2';
		  $azurian['Basicos']['serie']=''; //No obligatorio
		  $azurian['Basicos']['folio']=''; //No obligatorio
		  $azurian['Basicos']['fecha']=$fecha;
		  $azurian['Basicos']['sello']='';
		  $azurian['Basicos']['formaDePago']='Pago en una sola exhibicion';
		  $azurian['Basicos']['tipoDeComprobante']='ingreso';
		  $azurian['Basicos']['noCertificado']='';
		  $azurian['Basicos']['certificado']='';
		  $azurian['Basicos']['subTotal']=0.00;
		  $azurian['Basicos']['total']=0.00;
		  /* Datos serie y folio
		============================================================== */
		  $azurian['Basicos']['serie']=$rs3['rows'][0]['serie']; //No obligatorio
		  $azurian['Basicos']['folio']=$rs3['rows'][0]['folio'];
		  /* Datos Emisor
		  ============================================================== */
		  $azurian['Emisor']['rfc']=$parametros['EmisorTimbre']['RFC'];
		  $azurian['Emisor']['nombre']=$parametros['EmisorTimbre']['RazonSocial'];

		  /* Datos Fiscales Emisor
		  ============================================================== */
		  $azurian['FiscalesEmisor']['calle']=$parametros['EmisorTimbre']['Calle'];
		  $azurian['FiscalesEmisor']['noExterior']=$parametros['EmisorTimbre']['NumExt'];
		  $azurian['FiscalesEmisor']['colonia']=$parametros['EmisorTimbre']['Colonia'];
		  $azurian['FiscalesEmisor']['localidad']=$parametros['EmisorTimbre']['Ciudad'];
		  $azurian['FiscalesEmisor']['municipio']=$parametros['EmisorTimbre']['Municipio'];
		  $azurian['FiscalesEmisor']['estado']=$parametros['EmisorTimbre']['Estado'];
		  $azurian['FiscalesEmisor']['pais']=$parametros['EmisorTimbre']['Pais'];
		  $azurian['FiscalesEmisor']['codigoPostal']=$parametros['EmisorTimbre']['CP'];

		  /* Datos Regimen
		  ============================================================== */
		  $azurian['Regimen']['Regimen']=$parametros['EmisorTimbre']['RegimenFiscal'];

		  /* Datos Receptor
		  ============================================================== */
		  $azurian['Receptor']['rfc']='XAXX010101000';
		  $azurian['Receptor']['nombre']='Factura generica';
		  $azurian['DomicilioReceptor']['calle']='';
		  $azurian['DomicilioReceptor']['noExterior']='';
		  $azurian['DomicilioReceptor']['colonia']='';
		  $azurian['DomicilioReceptor']['localidad']='';
		  $azurian['DomicilioReceptor']['municipio']='';
		  $azurian['DomicilioReceptor']['estado']='';
		  $azurian['DomicilioReceptor']['pais']='';
		  $azurian['DomicilioReceptor']['codigoPostal']='';
		  $azurian['Correo']['Correo'] = '';

		$azurian['Impuestos']['totalImpuestosRetenidos']=0.00;
		$azurian['Impuestos']['totalImpuestosTrasladados']=0.00;

		$queryCad = "SELECT cadenaOriginal FROM app_pendienteFactura WHERE id_sale in (".$cadena.");";
		$resCad = $this->queryArray($queryCad);
		$azuriant = '';
		foreach ($resCad['rows'] as $key => $value) {
			$azuriant=base64_decode($value['cadenaOriginal']);
			$azuriant = str_replace("\\", "", $azuriant);
			if($azuriant!=''){ $azuriant=json_decode($azuriant); }
			$azuriant = $this->object_to_array($azuriant);

			$azurian['Conceptos']['conceptos'].=$azuriant['Conceptos']['conceptos'];
			$azurian['Conceptos']['conceptosOri'].=$azuriant['Conceptos']['conceptosOri'];

			$azurian['Impuestos']['totalImpuestosRetenidos']+=$azuriant['Impuestos']['totalImpuestosRetenidos'];
			$azurian['Impuestos']['totalImpuestosTrasladados']+=$azuriant['Impuestos']['totalImpuestosTrasladados'];


			if (isset($azuriant['nn']['nn']['IEPS'])) {
				//echo "Esta variable está definida, así que se imprimirá";
				//print_r($azuriant['nn']['nn']);
				foreach ($azuriant['nn']['nn'] as $key => $value) {

					if($key == 'IEPS'){
						foreach ($value as $key2 => $value2) {
							$totalIeps += str_replace(',','',number_format($value2,2));
							$porIeps = $key2;
						}
					}


				}

			}

				foreach ($azuriant['nn']['nn'] as $key => $value) {

					if($key == 'IVA'){
						foreach ($value as $key3 => $value3) {
							$totalIVAm += str_replace(',','',number_format($value3,2));

							$porIVAm = $key3;
						}
					}


				}

			$azurian['Basicos']['subTotal']+=$azuriant['Basicos']['subTotal'];
			$azurian['Basicos']['total']+=$azuriant['Basicos']['total'];
		}

			$xCon = $azurian['Conceptos']['conceptosOri'];
			$xCon = explode('|',$azurian['Conceptos']['conceptosOri']);
			$subTotImportes = 0;
			for($i = 5; $i < count($xCon); $i+=5) {
    			$subTotImportes+= $xCon[$i];

			}
				/*$conceptosOri = '';
				$conceptos = '';

					$conceptosDatos[0]["Cantidad"] = 1;
					$conceptosDatos[0]["Unidad"] = "No Aplica";
					$conceptosDatos[0]["Precio"] = $subTotImportes;
					$conceptosDatos[0]["Descripcion"] = "Factura de Global de ventas (".$cadena.")";
					$conceptosDatos[0]["Importe"] = $subTotImportes;


				//se emepiza a llenar los conceptos en el arreglo de azurian
				foreach ($conceptosDatos as $key => $value) {
					$value['Descripcion'] = preg_replace("/'/", "&apos;", $value['Descripcion']);
					$value['Descripcion'] = preg_replace('/"/', "&quot;", $value['Descripcion']);
				   // $value['Descripcion'] = preg_replace('("|\')', "&apos;", $value['Descripcion']);
					$value['Descripcion'] = eregi_replace("[\n|\r|\n\r]", " ", $value['Descripcion']);
					$value['Descripcion'] = trim($value['Descripcion']);
					if($value['Unidad']==''){
						$value['Unidad']= "No Aplica";
					}
					$conceptosOri.='|' . $value['Cantidad'] . '|';
					$conceptosOri.=$value['Unidad'] . '|';
					$conceptosOri.=$value['Descripcion'] . '|';
					$conceptosOri.=str_replace(",", "", number_format($value['Precio'],2)) . '|';
					$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2));
					$conceptos.="<cfdi:Concepto cantidad='" . $value['Cantidad'] . "' unidad='" . $value['Unidad'] . "' descripcion='" . $value['Descripcion'] . "' valorUnitario='" . str_replace(",", "", number_format($value['Precio'],2)) . "' importe='" . str_replace(",", "", number_format($value['Importe'],2)) . "'/>";

				} */

				//$azurian['Conceptos']['conceptos']="<cfdi:Concepto cantidad='1' unidad='1' descripcion='Facturacion Global(".$cadena.")' valorUnitario='".str_replace(",","",number_format($subTotImportes,2))."' importe='".str_replace(",","",number_format($subTotImportes,2))."'/>";
            	//$azurian['Conceptos']['conceptosOri']="|1|1|Facturacion Global(".$cadena.")|".str_replace(",","",number_format($subTotImportes,2))."|".str_replace(",","",number_format($subTotImportes,2))."";


				/*$azurian['Conceptos']['conceptos'] = $conceptos;
				$azurian['Conceptos']['conceptosOri'] = $conceptosOri; */

			/*----------------------------------------------------------------------------------------------------------*/


			/*echo '······'.$subTotImportes;
			exit();*/
		//echo 'total='.$totalIeps;
		  $ivas='';
		  $tisr=0.00;
		  $tiva=0.00;

		  $isr='';
		  $iva='';
		  $nodIeps = '';
		  $cadOrIeps = '';
			if($totalIeps>0){
			   $cadOrIeps = 'IEPS|'.$porIeps.'|'.number_format($totalIeps,2,'.','').'';
			   $nodIeps = "<cfdi:Traslado impuesto='IEPS' tasa='".$porIeps."' importe='".number_format($totalIeps,2,'.','')."' />";
			   $nn2["IEPS"][$porIeps]["Valor"] = str_replace(",", "", $totalIeps);

			}


		  $azurian['Impuestos']['totalImpuestosRetenidos']=str_replace(',','',number_format($azurian['Impuestos']['totalImpuestosRetenidos'],2));
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = str_replace(',','',number_format($azurian['Impuestos']['totalImpuestosTrasladados'],2));


		  //echo 'kii'.$azurian['Impuestos']['totalImpuestosTrasladados'];

		  $tisr=number_format($azurian['Impuestos']['totalImpuestosRetenidos'],2,'.','');
		  //$tiva=number_format($azurian['Impuestos']['totalImpuestosTrasladados'],2,'.','');
		  $tiva = number_format($totalIVAm,2,'.','');
		 $azurian['Impuestos']['totalImpuestosRetenidos']=str_replace(',','',number_format($azurian['Impuestos']['totalImpuestosRetenidos'],2));
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = str_replace(',','',number_format($azurian['Impuestos']['totalImpuestosTrasladados'],2));

		  if($tisr>0){
			$azurian['Impuestos']['isr']='|ISR|'.$tisr.'|'.$tisr.'|';
			$isr="<cfdi:Retenciones><cfdi:Retencion impuesto='ISR' importe='".number_format($tisr,2,'.','')."' /></cfdi:Retenciones>";
		  }
		  if($tiva>0){
			/*$xsubTiva =  $tiva;
			$xsubA =  number_format($azurian['Impuestos']['totalImpuestosTrasladados'], 2, '.', '');

			if($xsubTiva < $xsubAiva){
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] - 0.01;
				$trasladsimp = $trasladsimp + 0.01;
			}elseif($xsubTiva > $xsubAiva){
				if($trasladsimp > 0){
					$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
					$trasladsimp = $trasladsimp - 0.01;
				}else{
					$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
					$azurian['Basicos']['total'] = $azurian['Basicos']['total'] + 0.01;
				}

			} */
			$azurian['Impuestos']['iva']='|IVA|16|'.number_format($tiva,2,'.','').'|'.$cadOrIeps.'|'.number_format(($tiva + $totalIeps),2,'.','').'';
			$iva="<cfdi:Traslados><cfdi:Traslado impuesto='IVA' tasa='16' importe='".number_format($tiva,2,'.','')."' />".$nodIeps."</cfdi:Traslados>";
			 $azurian['Impuestos']['totalImpuestosTrasladados'] = number_format($tiva,2,'.','');
		  }
		  if($tiva <= 0){
		  	$azurian['Impuestos']['iva']='|IVA|16|'.number_format($tiva,2,'.','').'|'.$cadOrIeps.'|'.number_format(($tiva + $totalIeps),2,'.','').'';
			$iva="<cfdi:Traslados><cfdi:Traslado impuesto='IVA' tasa='16' importe='".number_format($tiva,2,'.','')."' />".$nodIeps."</cfdi:Traslados>";
		  }
		  //echo $azurian['Impuestos']['iva'];
		  //echo $iva;
//exit();
		  //$azurian['Observacion']['Observacion']="Esta nota de credito esta vinculada a la factura con folio ".$folio;
		  $nn2["IVA"]["16.0"]["Valor"] = str_replace(",", "", $tiva);
		  //echo $nn2["IVA"]["16.0"]["Valor"];
		  //exit();

		  $azurian['nn']['nn']=$nn2;
		  $ivas.=$isr.$iva;
		  $azurian['Impuestos']['ivas']=$ivas;
		  $azurian['Basicos']['total'] = number_format($azurian['Basicos']['total'],2,'.','');

		$xsubT =  number_format($subTotImportes, 2, '.', '');
		$xsubA =  number_format($azurian['Basicos']['subTotal'], 2, '.', '');

		if($xsubT < $xsubA){
			$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] - 0.01;
			$trasladsimp = $trasladsimp + 0.01;
		}elseif($xsubT > $xsubA){
			if($trasladsimp > 0){
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
				$trasladsimp = $trasladsimp - 0.01;
			}else{
				$azurian['Basicos']['subTotal'] = $azurian['Basicos']['subTotal'] + 0.01;
				$azurian['Basicos']['total'] = $azurian['Basicos']['total'] + 0.01;
			}

		}

		////////////////////////////////////////////////////////////////////////////////////
			//AÑADIDO POR IVAN CUENCA
			//INICIA CONEXION CON ACONTIA
			//Si existe una venta comprueba si hay conexion con acontia
			/*if($cadena != '')
			{
				//Si se guardo la venta genera la poliza
				//Esta conectado a acontia?
				$conexion_acontia = $this->conexion_acontia();
				$conexion_acontia = $conexion_acontia->fetch_assoc();
				if(intval($conexion_acontia['conectar_acontia']))
				{
					$this->query("UPDATE cont_polizas SET activo = 0 WHERE origen = 'Venta' AND idorigen IN ($cadena)");
					$this->query("UPDATE cont_polizas SET activo = 0 WHERE origen = 'Venta Pago%' AND idorigen IN ($cadena)");
					$this->generar_poliza(0,$fecha,$conexion_acontia,$cadena,2,2);
					$this->generar_poliza_pagos(0,$fecha,$conexion_acontia,$cadena,2,2);
				}
			}  */

			//TERMINA CONEXION CON ACONTIA
			////////////////////////////////////////////////////////////////////////////////////
			//print_r($azurian);
			//exit();

		  //require_once('../../modulos/lib/nusoap.php');
		  //require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT2.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT.php');
		}


	}
	//allfs33
	public function allfs($id){
		$cadena=trim($id,',');

		require_once('../../modulos/SAT/config.php');
		date_default_timezone_set("Mexico/General");
		$fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-5 minute"));

		$query2 = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
		$rs2 = $this->queryArray($query2);
		//$rs2 = $this->conexion->siguiente($result2);

		$query3 ="SELECT * FROM pvt_serie_folio WHERE id=1;";
		$rs3 = $this->queryArray($query3);

		$query4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$rs4 = $this->queryArray($query4);
		/////Selecciona el pack
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];

		$azurian['org']['logo'] = $rs4['rows'][0]['logoempresa'];

		/* DATOS OBLIGATORIOS DEL EMISOR
		================================================================== */
		$rfc_cliente=$rs2['rows'][0]['rfc'];
		/*$parametros['EmisorTimbre'] = array();
		$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
		$parametros['EmisorTimbre']['RFC'] = utf8_decode($r->rfc);
		$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social); */
		$parametros['EmisorTimbre'] = array();

		$parametros['EmisorTimbre']['RFC'] = $rs2['rows'][0]['rfc'];
		$parametros['EmisorTimbre']['RazonSocial'] = $rs2['rows'][0]['razon_social'];
		$parametros['EmisorTimbre']['RegimenFiscal'] = $rs2['rows'][0]['regimenf'];
		/*$parametros['EmisorTimbre']['Pais'] = $rs2['rows'][0]['pais'];

		$parametros['EmisorTimbre']['Calle'] = $rs2['rows'][0]['calle'];
		$parametros['EmisorTimbre']['NumExt'] = $rs2['rows'][0]['num_ext'];
		$parametros['EmisorTimbre']['Colonia'] = $rs2['rows'][0]['colonia'];
		$parametros['EmisorTimbre']['Ciudad'] = $rs2['rows'][0]['ciudad']; //Ciudad o Localidad
		$parametros['EmisorTimbre']['Municipio'] = $rs2['rows'][0]['municipio'];
		$parametros['EmisorTimbre']['Estado'] = $rs2['rows'][0]['estado'];
		$parametros['EmisorTimbre']['CP'] = $rs2['rows'][0]['cp']; */
		$cer_cliente=$pathdc.'/'.$rs2['rows'][0]['cer'];
		$key_cliente=$pathdc.'/'.$rs2['rows'][0]['llave'];
		$pwd_cliente=$rs2['rows'][0]['clave'];

		if($rs2['rows'][0]['rfc']==''){

		$JSON = array('success' =>0,
			'error'=>1001,
			'mensaje'=>'No existen datos de emisor.');
		echo json_encode($JSON);
		exit();

		}
					///Lugar de expedicion
		//$lugExpe = 'SELECT cp from pvt_configura_facturacion';
		$lugExp = 'SELECT cp from organizaciones';

		$lugExpeRes = $this->queryArray($lugExp);

		  /* Datos Basicos
		  ============================================================== */
		  $azurian['Basicos']['Moneda']='MXN';
		  $azurian['Basicos']['MetodoPago']='PUE';
		  $azurian['Basicos']['LugarExpedicion']=$lugExpeRes['rows'][0]['cp'];
		  $azurian['Basicos']['Version']='3.3';
		  $azurian['Basicos']['Serie']=''; //No obligatorio
		  $azurian['Basicos']['Folio']=''; //No obligatorio
		  $azurian['Basicos']['Fecha']=$fecha;
		  $azurian['Basicos']['Sello']='';
		  $azurian['Basicos']['FormaPago']='99';
		  $azurian['Basicos']['TipoDeComprobante']='I';
		  $azurian['Basicos']['NoCertificado']='';
		  $azurian['Basicos']['Certificado']='';
		  $azurian['Basicos']['SubTotal']=0.00;
		  $azurian['Basicos']['Total']=0.00;
		  /* Datos serie y folio
		============================================================== */
		  $azurian['Basicos']['Serie']=$rs3['rows'][0]['serie']; //No obligatorio
		  $azurian['Basicos']['Folio']=$rs3['rows'][0]['folio'];
		  /* Datos Emisor
		  ============================================================== */
		  $azurian['Emisor']['RegimenFiscal'] = strtoupper($parametros['EmisorTimbre']['RegimenFiscal']);
		  $azurian['Emisor']['Rfc']=$parametros['EmisorTimbre']['RFC'];
		  $azurian['Emisor']['Nombre']=$parametros['EmisorTimbre']['RazonSocial'];

		  /* Datos Fiscales Emisor
		  ============================================================== */
		  /*$azurian['FiscalesEmisor']['calle']=$parametros['EmisorTimbre']['Calle'];
		  $azurian['FiscalesEmisor']['noExterior']=$parametros['EmisorTimbre']['NumExt'];
		  $azurian['FiscalesEmisor']['colonia']=$parametros['EmisorTimbre']['Colonia'];
		  $azurian['FiscalesEmisor']['localidad']=$parametros['EmisorTimbre']['Ciudad'];
		  $azurian['FiscalesEmisor']['municipio']=$parametros['EmisorTimbre']['Municipio'];
		  $azurian['FiscalesEmisor']['estado']=$parametros['EmisorTimbre']['Estado'];
		  $azurian['FiscalesEmisor']['pais']=$parametros['EmisorTimbre']['Pais'];
		  $azurian['FiscalesEmisor']['codigoPostal']=$parametros['EmisorTimbre']['CP']; */

		  /* Datos Regimen
		  ============================================================== */
		  //$azurian['Regimen']['Regimen']=$parametros['EmisorTimbre']['RegimenFiscal'];

		  /* Datos Receptor
		  ============================================================== */
			$azurian['Receptor']['Rfc'] = 'XAXX010101000';
			$azurian['Receptor']['Nombre'] = 'Factura generica';
			$azurian['Receptor']['UsoCFDI'] = 'P01';

		  /*$azurian['Receptor']['rfc']='XAXX010101000';
		  $azurian['Receptor']['nombre']='Factura generica';
		  $azurian['DomicilioReceptor']['calle']='';
		  $azurian['DomicilioReceptor']['noExterior']='';
		  $azurian['DomicilioReceptor']['colonia']='';
		  $azurian['DomicilioReceptor']['localidad']='';
		  $azurian['DomicilioReceptor']['municipio']='';
		  $azurian['DomicilioReceptor']['estado']='';
		  $azurian['DomicilioReceptor']['pais']='';
		  $azurian['DomicilioReceptor']['codigoPostal']=''; */
		  $azurian['Correo']['Correo'] = '';

		$azurian['Impuestos']['totalImpuestosRetenidos']=0.00;
		$azurian['Impuestos']['totalImpuestosTrasladados']=0.00;

		//$queryCad = "SELECT cadenaOriginal,id_sale FROM app_pendienteFactura WHERE id_sale in (".$cadena.");";
		$queryCad = "SELECT p.cadenaOriginal,p.id_sale,v.impuestos,v.subtotal  FROM app_pendienteFactura p, app_pos_venta v WHERE p.id_sale=v.idVenta and id_sale in (".$cadena.")";
		//echo $queryCad;
		$resCad = $this->queryArray($queryCad);
		$azuriant = '';
		$totalImpuestosFor = 0;
		$subim = 0;
		$fpf = array();
		$sumDesc = 0;
		$tasa0 = 0;
		$tIVA = 0;
		$tIEPS = 0;
		$ips = 0;
		foreach ($resCad['rows'] as $key => $value) {
			$azuriant=base64_decode($value['cadenaOriginal']);
			$azuriant = str_replace("\\", "", $azuriant);
			if($azuriant!=''){ $azuriant=json_decode($azuriant); }
			$azuriant = $this->object_to_array($azuriant);

			if($azuriant['Basicos']['FormaPago']!='99'){

			if(array_key_exists($azuriant['Basicos']['FormaPago'], $fpf)){
				if (isset($azurian['Basicos']['version'])){
					$fpf[$azuriant['Basicos']['FormaPago']]+=$azuriant['Basicos']['subTotal'];

				}else{

					$fpf[$azuriant['Basicos']['FormaPago']]+=$azuriant['Basicos']['SubTotal'];

				}

			}else{
				if (isset($azurian['Basicos']['version'])){
					$fpf[$azuriant['Basicos']['FormaPago']]=$azuriant['Basicos']['subTotal'];

				}else{

					$fpf[$azuriant['Basicos']['FormaPago']]=$azuriant['Basicos']['SubTotal'];

				}
			}

			$conceptosOri.='|01010101|';
			$conceptosOri.='|'.$value['id_sale'].'|';
			$conceptosOri.='1|';
			$conceptosOri.='ACT|';
			//$conceptosOri.='Actividad|';
			$conceptosOri.='Venta '.$value['id_sale'].'|';
			if (isset($azurian['Basicos']['version'])){
	    		$stal = $azuriant['Basicos']['subTotal'];
			}else{
				$stal = $azuriant['Basicos']['SubTotal'];

			}

			if (isset($azurian['Basicos']['version'])){
				if (isset($azuriant['Basicos']['Descuento'])){
					$desc = $azuriant['Basicos']['Descuento'];
				}else{
					$desc = 0;
				}

			}else{
				if (isset($azuriant['Basicos']['Descuento'])){
					$desc = $azuriant['Basicos']['Descuento'];
				}else{
					$desc = 0;
				}

			}
				//echo '(Venta ='.$value['id_sale'].' Des='.$desc.')<br>';
			if($desc > 0 && $desc!=''){
				$stal = bcdiv(round(($value['subtotal'] + $desc) ,2),'1',2) ;
				$conceptosOri.=$stal.'|';
				$conceptosOri.=bcdiv($stal,'1',2).'|';
				$conceptosOri.=bcdiv($desc,'1',2).'|';
				$desXml = " Descuento='".bcdiv($desc,'1',2)."'";
				$sumDesc += bcdiv($desc,'1',2);
			}else{
				$stal = bcdiv(round($value['subtotal'],2),'1',2);
				$conceptosOri.=$stal.'|';
				$conceptosOri.=bcdiv($stal,'1',2).'|';
				$desXml = '';
			}


			$conceptos.="<cfdi:Concepto ClaveProdServ='01010101' NoIdentificacion='".$value['id_sale']."' Cantidad='1' ClaveUnidad='ACT' Descripcion='Venta ".$value['id_sale']."' ValorUnitario='" .$stal. "' Importe='".bcdiv($stal,'1',2)."'".$desXml.">";
			$subim +=bcdiv($stal,'1',2);
			//echo 'Venta ='.$value['id_sale'].'--Zum='.$subim.'<br>';
			//$conceptos.='<cfdi:Impuestos>';
			//$conceptos.='<cfdi:Traslados>';
			//echo 'kdddfjk';
			//echo $value['id_sale'].'--'.$value['impuestos'].'<br>';
			if($value['impuestos']!='null'){
				$conceptos.='<cfdi:Impuestos>';
				$conceptos.='<cfdi:Traslados>';
				//echo 'owoeoe';
				//echo '(Entro la '.$value['id_sale'].')';
				//echo $value['impuestos'];
				$jsonImp = json_decode($value['impuestos']);
				$arryImp = $this->object_to_array($jsonImp);
				//print_r($arryImp);
				//exit();
				foreach ($arryImp as $keysx => $valuesx) {
					//echo 'key='.$keysx.' value='.$valuesx;
					$impuesClave = explode(' ', $keysx);
					if($impuesClave[0]=='IVA'){
						$immpuesSAT = '002';
					}else{
						$immpuesSAT = '003';
					}
					$tasaCuota = explode('%', $impuesClave[1]);
					$rsw = strlen($tasaCuota[0]);
						if($rsw == 1){
							$tasaCuota2 = '0.0'.$tasaCuota[0].'0000';
						}else{
							$tasaCuota2 = '0.'.$tasaCuota[0].'0000';
						}
					/*if($desXml!=''){
						$stal = $stal - $desc;
						$conceptosOri.=$stal.'|';
					}else{
						$conceptosOri.=$stal.'|';
					}

					$conceptosOri.=$immpuesSAT.'|';
					$conceptosOri.='Tasa|'; */
					//$conceptosOri.='0.160000|';
					if($tasaCuota2==0.000000){
						if($desXml!=''){
							$stal = $stal - $desc;
							$conceptosOri.=$stal.'|';
						}else{
							$conceptosOri.=$stal.'|';
						}

						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.='Tasa|';
						$tasaCuota2 = 0.000000;
						$conceptosOri.=$tasaCuota2.'|';
						$conceptosOri.=bcdiv(round(($stal * 0),2),'1',2).'|';
						$conceptos.="<cfdi:Traslado Base='".$stal."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$tasaCuota2."' Importe='".bcdiv(round(($stal * 0),2),'1',2)."' />";
						$totalImpuestosFor +=bcdiv(round(($stal * 0),2),'1',2);
						$tasa0 = 1;
					}else{

						if($immpuesSAT=='003'){
							if($desXml!=''){
								$stal = $stal - $desc;
								$conceptosOri.=$stal.'|';
							}else{
								$conceptosOri.=$stal.'|';
							}

							$conceptosOri.=$immpuesSAT.'|';
							$conceptosOri.='Tasa|';
							$tasaCuota2 = $tasaCuota2;
							$conceptosOri.=$tasaCuota2.'|';
							$ips = bcdiv(round(($stal * $tasaCuota2),2),'1',2) ;
							$conceptosOri.=bcdiv(round(($stal * $tasaCuota2),2),'1',2).'|';
							$conceptos.="<cfdi:Traslado Base='".$stal."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$tasaCuota2."' Importe='".bcdiv(round(($stal * $tasaCuota2),2),'1',2)."' />";
							$totalImpuestosFor +=bcdiv(round(($stal * $tasaCuota2),2),'1',2);
							if($immpuesSAT=='002'){
								$tIVA += bcdiv(round(($stal * $tasaCuota2),2),'1',2);
							}elseif($immpuesSAT=='003'){
								$tIEPS += bcdiv(round(($stal * $tasaCuota2),2),'1',2);
							}
						}else{
							if($desXml!=''){
								$stal = $stal - $desc;
								$conceptosOri.=($stal +$ips) .'|';
							}else{
								$conceptosOri.=($stal +$ips) .'|';
							}

							$conceptosOri.=$immpuesSAT.'|';
							$conceptosOri.='Tasa|';
							$tasaCuota2 = $tasaCuota2;
							$conceptosOri.=$tasaCuota2.'|';
							//echo '('.$ips.')';
							$conceptosOri.=bcdiv(round((($stal+$ips) * $tasaCuota2),2),'1',2).'|';
							$conceptos.="<cfdi:Traslado Base='".($stal+$ips)."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$tasaCuota2."' Importe='".bcdiv(round((($stal +$ips) * $tasaCuota2),2),'1',2)."' />";
							$totalImpuestosFor +=bcdiv(round((($stal + $ips) * $tasaCuota2),2),'1',2);
							if($immpuesSAT=='002'){
								$tIVA += bcdiv(round((($stal+$ips) * $tasaCuota2),2),'1',2);
							}elseif($immpuesSAT=='003'){
								$tIEPS += bcdiv(round(($stal * $tasaCuota2),2),'1',2);
							}
						}



					}




				}
				$conceptos.='</cfdi:Traslados>';
				$conceptos.="</cfdi:Impuestos>";
				$conceptos.='</cfdi:Concepto>';
				$ips = 0;

			}else{
				$conceptos.='<cfdi:Impuestos>';
				$conceptos.='<cfdi:Traslados>';

				$conceptosOri.=$stal.'|';
				$conceptosOri.='002|';
				$conceptosOri.='Tasa|';
				$conceptosOri.='0.000000|';
				$conceptosOri.=bcdiv(round(($stal * 0),2),'1',2).'|';
				$conceptos.="<cfdi:Traslado Base='".$stal."' Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='0.00' />";

				$tasa0 = 1;
				$conceptos.='</cfdi:Traslados>';
				$conceptos.="</cfdi:Impuestos>";
				$conceptos.='</cfdi:Concepto>';
				$ips = 0;
			}


			//exit();




			//$azurian['Conceptos']['conceptos'].=$azuriant['Conceptos']['conceptos'];
			//$azurian['Conceptos']['conceptosOri'].=$azuriant['Conceptos']['conceptosOri'];

			$azurian['Impuestos']['totalImpuestosRetenidos']+=$azuriant['Impuestos']['totalImpuestosRetenidos'];
			$azurian['Impuestos']['totalImpuestosTrasladados']+=$azuriant['Impuestos']['totalImpuestosTrasladados'];


			if (isset($azuriant['nn']['nn']['IEPS'])) {
				//echo "Esta variable está definida, así que se imprimirá";
				//print_r($azuriant['nn']['nn']);
				foreach ($azuriant['nn']['nn'] as $key => $value) {

					if($key == 'IEPS'){
						foreach ($value as $key2 => $value2) {
							$totalIeps += str_replace(',','',number_format($value2,2));
							$porIeps = $key2;
						}
					}


				}

			}

				foreach ($azuriant['nn']['nn'] as $key => $value) {

					if($key == 'IVA'){
						foreach ($value as $key3 => $value3) {
							$totalIVAm += str_replace(',','',number_format($value3,2));

							$porIVAm = $key3;
						}
					}


				}
			if (isset($azurian['Basicos']['version'])){
	    		$stalSum = $azuriant['Basicos']['subTotal'];
	    		$tsum = $azuriant['Basicos']['total'];
			}else{
				$stalSum = $azuriant['Basicos']['SubTotal'];
				$tsum = $azuriant['Basicos']['Total'];

			}

			$azurian['Basicos']['SubTotal']+=$stalSum;
			$azurian['Basicos']['Total']+=$tsum;
		}
		} ///fin del forech

		$azurian['Conceptos']['conceptos']=$conceptos;
		$azurian['Conceptos']['conceptosOri']=$conceptosOri;

		$banderas = 0;
		foreach ($fpf as $key => $value) {
			if($value>$banderas){
				$cl = $key;
				$banderas = $value;
			}

		}
		//echo '?'.$cl;
		$azurian['Basicos']['FormaPago'] = $cl;
		//exit();

		  $azurian['Impuestos']['isr'] = $retenids.$cadRet;
		  //echo $$azurian['Impuestos']['isr'];
		  //$azurian['Impuestos']['iva'] = $traslads . '|' . number_format($trasladsimp, 2, '.', '');
		  if(bcdiv(round($totalImpuestosFor,2),'1',2)<1){
		  	$azurian['Impuestos']['iva'] = '|002|Tasa|0.000000|'.bcdiv(round($totalImpuestosFor,2),'1',2).'|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  	$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /></cfdi:Traslados>";
		  }else{
		  	if($tasa0==1){
				$azurian['Impuestos']['iva'] = '|002|Tasa|0.160000|'.bcdiv(round($totalImpuestosFor,2),'1',2).'|002|Tasa|0.000000|0.00|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  		$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='0.00' /></cfdi:Traslados>";
		  	}else{
		  		$azurian['Impuestos']['iva'] = '|002|Tasa|0.160000|'.bcdiv(round($totalImpuestosFor,2),'1',2).'|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  		$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /></cfdi:Traslados>";
		  	}
		  	if($tIEPS > 0){
		  		$azurian['Impuestos']['iva'] = '003|Tasa|0.080000|'.bcdiv(round($tIEPS,2),'1',2).'|002|Tasa|0.160000|'.bcdiv(round($tIVA,2),'1',2).'|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  		$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='003' TipoFactor='Tasa' TasaOCuota='0.080000' Importe='".bcdiv(round($tIEPS,2),'1',2)."' /><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($tIVA,2),'1',2)."' /></cfdi:Traslados>";
		  	}

		  }


		  //$azurian['Impuestos']['iva'] = $traslads . '|' . round($trasladsimp,2);

		  //$azurian['Impuestos']['totalImpuestosRetenidos'] = number_format($retenciones, 2, '.', '');
		  $azurian['Impuestos']['totalImpuestosRetenidos'] = ''	;
		  //echo 'total trasladdos='.$azurian['Impuestos']['totalImpuestosRetenidos'];
		  //$azurian['Impuestos']['totalImpuestosTrasladados'] = number_format($trasladsimp, 2, '.', '');
		  //echo $totalImpuestosFor.'-'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  	$azurian['Impuestos']['totalImpuestosTrasladados'] = bcdiv(round($totalImpuestosFor,2),'1',2);



		 /* if(bcdiv(round($totalImpuestosFor,2),'1',2)<1){
		  	$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /></cfdi:Traslados>";
		  }else{
		  	$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /></cfdi:Traslados>";
		  }*/



		  $azurian['Impuestos']['ivas'] = $ivax;

		  	$azurian['Basicos']['SubTotal']=bcdiv(round($subim,2),'1',2);
		  	if($sumDesc > 0){
		  		$ttyy = round($subim,2) + round($totalImpuestosFor,2);
		  		$ttyy = $ttyy - $sumDesc;
		  		$azurian['Basicos']['Descuento'] =$sumDesc;
		  	}else{
		  		$ttyy = round($subim,2) + round($totalImpuestosFor,2);
		  	}

			$azurian['Basicos']['Total']=bcdiv(round($ttyy ,2),'1',2);



		  //echo 'rjnfnfnfnfnfnfnfnfnffnfnfnfnf';
			//print_r( $azurian['Basicos']);
			//exit();

		  //require_once('../../modulos/lib/nusoap.php');
		  //require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}


	}
	function allfs2018($id){
		$cadena=trim($id,',');

		require_once('../../modulos/SAT/config.php');
		date_default_timezone_set("Mexico/General");
		$fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-5 minute"));

		$query2 = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
		$rs2 = $this->queryArray($query2);
		//$rs2 = $this->conexion->siguiente($result2);

		$query3 ="SELECT * FROM pvt_serie_folio WHERE id=1;";
		$rs3 = $this->queryArray($query3);

		$query4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$rs4 = $this->queryArray($query4);
		/////Selecciona el pack
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];

		$azurian['org']['logo'] = $rs4['rows'][0]['logoempresa'];

		/* DATOS OBLIGATORIOS DEL EMISOR
		================================================================== */
		$rfc_cliente=$rs2['rows'][0]['rfc'];

		$parametros['EmisorTimbre'] = array();

		$parametros['EmisorTimbre']['RFC'] = $rs2['rows'][0]['rfc'];
		$parametros['EmisorTimbre']['RazonSocial'] = $rs2['rows'][0]['razon_social'];
		$parametros['EmisorTimbre']['RegimenFiscal'] = $rs2['rows'][0]['regimenf'];

		$cer_cliente=$pathdc.'/'.$rs2['rows'][0]['cer'];
		$key_cliente=$pathdc.'/'.$rs2['rows'][0]['llave'];
		$pwd_cliente=$rs2['rows'][0]['clave'];

		if($rs2['rows'][0]['rfc']==''){

		$JSON = array('success' =>0,
			'error'=>1001,
			'mensaje'=>'No existen datos de emisor.');
		echo json_encode($JSON);
		exit();

		}
					///Lugar de expedicion
		//$lugExpe = 'SELECT cp from pvt_configura_facturacion';
		$lugExp = 'SELECT cp from organizaciones';

		$lugExpeRes = $this->queryArray($lugExp);

		  /* Datos Basicos
		  ============================================================== */
		  $azurian['Basicos']['Moneda']='MXN';
		  $azurian['Basicos']['MetodoPago']='PUE';
		  $azurian['Basicos']['LugarExpedicion']=$lugExpeRes['rows'][0]['cp'];
		  $azurian['Basicos']['Version']='3.3';
		  $azurian['Basicos']['Serie']=''; //No obligatorio
		  $azurian['Basicos']['Folio']=''; //No obligatorio
		  $azurian['Basicos']['Fecha']=$fecha;
		  $azurian['Basicos']['Sello']='';
		  $azurian['Basicos']['FormaPago']='99';
		  $azurian['Basicos']['TipoDeComprobante']='I';
		  $azurian['Basicos']['NoCertificado']='';
		  $azurian['Basicos']['Certificado']='';
		  $azurian['Basicos']['SubTotal']=0.00;
		  $azurian['Basicos']['Total']=0.00;
		  /* Datos serie y folio
		============================================================== */
		  $azurian['Basicos']['Serie']=$rs3['rows'][0]['serie']; //No obligatorio
		  $azurian['Basicos']['Folio']=$rs3['rows'][0]['folio'];
		  /* Datos Emisor
		  ============================================================== */
		  $azurian['Emisor']['RegimenFiscal'] = strtoupper($parametros['EmisorTimbre']['RegimenFiscal']);
		  $azurian['Emisor']['Rfc']=$parametros['EmisorTimbre']['RFC'];
		  $azurian['Emisor']['Nombre']=$parametros['EmisorTimbre']['RazonSocial'];


		  /* Datos Receptor
		  ============================================================== */
			$azurian['Receptor']['Rfc'] = 'XAXX010101000';
			$azurian['Receptor']['Nombre'] = 'Factura generica';
			$azurian['Receptor']['UsoCFDI'] = 'P01';
		 	$azurian['Correo']['Correo'] = '';

		$azurian['Impuestos']['totalImpuestosRetenidos']=0.00;
		$azurian['Impuestos']['totalImpuestosTrasladados']=0.00;

		$queryCad = "SELECT idVenta,subtotal,montoimpuestos,monto, impuestos,descuentoGeneral FROM app_pos_venta v WHERE idVenta in (".$cadena.")";
		//echo $queryCad;
		//exit();
		$resCad = $this->queryArray($queryCad);
		$azuriant = '';
		$totalImpuestosFor = 0;
		$subim = 0;
		$fpf = array();
		$sumDesc = 0;
		$tasa0 = 0;
		$tIVA = 0;
		$tIEPS = 0;
		$ips = 0;
		$xyz = 0;
		////// Se empiezan a crear los nodos de conceptos
		foreach ($resCad['rows'] as $key => $value) {
			$selDes = 'SELECT sum(montodescuento) as suma from app_pos_venta_producto where idVenta='.$value['idVenta'];
			$rs = $this->queryArray($selDes);
			$desc = $rs['rows'][0]['suma'];

			$conceptosOri.='|01010101|';
			$conceptosOri.='|'.$value['idVenta'].'|';
			$conceptosOri.='1|';
			$conceptosOri.='ACT|';
			//$conceptosOri.='Actividad|';
			$conceptosOri.='Venta '.$value['idVenta'].'|';
			//echo '(Venta ='.$value['id_sale'].' Des='.$desc.')<br>';
			if($desc > 0 && $desc!=''){
				$stal = bcdiv(round(($value['subtotal'] + $desc) ,2),'1',2) ;
				$conceptosOri.=$stal.'|';
				$conceptosOri.=bcdiv($stal,'1',2).'|';
				$conceptosOri.=bcdiv($desc,'1',2).'|';
				$desXml = " Descuento='".bcdiv($desc,'1',2)."'";
				$sumDesc += bcdiv($desc,'1',2);
			}else{
				$stal = bcdiv(round($value['subtotal'],2),'1',2);
				$conceptosOri.=$stal.'|';
				$conceptosOri.=bcdiv($stal,'1',2).'|';
				$desXml = '';
			}


			$conceptos.="<cfdi:Concepto ClaveProdServ='01010101' NoIdentificacion='".$value['idVenta']."' Cantidad='1' ClaveUnidad='ACT' Descripcion='Venta ".$value['idVenta']."' ValorUnitario='" .$stal. "' Importe='".bcdiv($stal,'1',2)."'".$desXml.">";
			$subim +=bcdiv($stal,'1',2);
			///////Se emepizan a crear los nodos de impuestos del concepto
			if($value['impuestos']!='null'){
				$sel = 'SELECT v.idventa_producto,sum(v.subtotal) as subt,sum(v.impuestosproductoventa) as ti,i.idImpuesto,i.porcentaje,ip.clave from app_pos_venta_producto v, app_pos_venta_producto_impuesto i left join app_impuesto ip on  i.idImpuesto=ip.id where v.idventa_producto=i.idVentaproducto and v.idVenta='.$value['idVenta'].' group by i.porcentaje';
				$resTo = $this->queryArray($sel);
				$imPv = array();
				$conceptos.='<cfdi:Impuestos>';
				$conceptos.='<cfdi:Traslados>';
				foreach ($resTo['rows'] as $keyro => $valuero) {
					if($valuero['porcentaje']==0){
						$valuero['porcentaje'] = '0.000000';
						$tasa0 = 1;
					}else{
						$valuero['porcentaje'] = '0.160000';
					}

					$xyz =  bcdiv(round($valuero['subt'],2),'1',2) * ($valuero['porcentaje'] * 1);

					$stal = $valuero['subt'];
					$conceptosOri.=bcdiv(round($valuero['subt'],2),'1',2).'|';
					$conceptosOri.='002|';
					$conceptosOri.='Tasa|';

					$conceptosOri.=$valuero['porcentaje'].'|';
					$conceptosOri.=bcdiv(round($xyz,2),'1',2).'|';
					$conceptos.="<cfdi:Traslado Base='".bcdiv(round($valuero['subt'],2),'1',2)."' Impuesto='002' TipoFactor='Tasa' TasaOCuota='".$valuero['porcentaje']."' Importe='".bcdiv(round($xyz ,2),'1',2)."' />";
					$totalImpuestosFor +=bcdiv(round($xyz,2),'1',2);
				}
				$conceptos.='</cfdi:Traslados>';
				$conceptos.="</cfdi:Impuestos>";
				$conceptos.='</cfdi:Concepto>';
			}else{
				$conceptos.='<cfdi:Impuestos>';
				$conceptos.='<cfdi:Traslados>';

				$conceptosOri.=$stal.'|';
				$conceptosOri.='002|';
				$conceptosOri.='Tasa|';
				$conceptosOri.='0.000000|';
				$conceptosOri.=bcdiv(round(($stal * 0),2),'1',2).'|';
				$conceptos.="<cfdi:Traslado Base='".$stal."' Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='0.00' />";

				$tasa0 = 1;
				$conceptos.='</cfdi:Traslados>';
				$conceptos.="</cfdi:Impuestos>";
				$conceptos.='</cfdi:Concepto>';
				$ips = 0;
			}




		}
		//echo $conceptos;
		//exit();
		$azurian['Conceptos']['conceptos']=$conceptos;
		$azurian['Conceptos']['conceptosOri']=$conceptosOri;
		/*$banderas = 0;
		foreach ($fpf as $key => $value) {
			if($value>$banderas){
				$cl = $key;
				$banderas = $value;
			}

		} */
		//echo '?'.$cl;
		$azurian['Basicos']['FormaPago'] = '99';
		//$azurian['Basicos']['FormaPago'] = $cl;
		//exit();

		  $azurian['Impuestos']['isr'] = $retenids.$cadRet;
		  //echo $$azurian['Impuestos']['isr'];
		  //$azurian['Impuestos']['iva'] = $traslads . '|' . number_format($trasladsimp, 2, '.', '');
		  if(bcdiv(round($totalImpuestosFor,2),'1',2)<1){
		  	$azurian['Impuestos']['iva'] = '|002|Tasa|0.000000|'.bcdiv(round($totalImpuestosFor,2),'1',2).'|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  	$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /></cfdi:Traslados>";
		  }else{
		  	if($tasa0==1){
				$azurian['Impuestos']['iva'] = '|002|Tasa|0.160000|'.bcdiv(round($totalImpuestosFor,2),'1',2).'|002|Tasa|0.000000|0.00|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  		$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.000000' Importe='0.00' /></cfdi:Traslados>";
		  	}else{
		  		$azurian['Impuestos']['iva'] = '|002|Tasa|0.160000|'.bcdiv(round($totalImpuestosFor,2),'1',2).'|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  		$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($totalImpuestosFor,2),'1',2)."' /></cfdi:Traslados>";
		  	}
		  	if($tIEPS > 0){
		  		$azurian['Impuestos']['iva'] = '003|Tasa|0.080000|'.bcdiv(round($tIEPS,2),'1',2).'|002|Tasa|0.160000|'.bcdiv(round($tIVA,2),'1',2).'|'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  		$ivax="<cfdi:Traslados><cfdi:Traslado Impuesto='003' TipoFactor='Tasa' TasaOCuota='0.080000' Importe='".bcdiv(round($tIEPS,2),'1',2)."' /><cfdi:Traslado Impuesto='002' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".bcdiv(round($tIVA,2),'1',2)."' /></cfdi:Traslados>";
		  	}

		  }


		  //$azurian['Impuestos']['iva'] = $traslads . '|' . round($trasladsimp,2);

		  //$azurian['Impuestos']['totalImpuestosRetenidos'] = number_format($retenciones, 2, '.', '');
		  $azurian['Impuestos']['totalImpuestosRetenidos'] = ''	;
		  //echo 'total trasladdos='.$azurian['Impuestos']['totalImpuestosRetenidos'];
		  //$azurian['Impuestos']['totalImpuestosTrasladados'] = number_format($trasladsimp, 2, '.', '');
		  //echo $totalImpuestosFor.'-'.bcdiv(round($totalImpuestosFor,2),'1',2);
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = bcdiv(round($totalImpuestosFor,2),'1',2);

		  $azurian['Impuestos']['ivas'] = $ivax;

		  	$azurian['Basicos']['SubTotal']=bcdiv(round($subim,2),'1',2);
		  	if($sumDesc > 0){
		  		$ttyy = round($subim,2) + round($totalImpuestosFor,2);
		  		$ttyy = $ttyy - $sumDesc;
		  		$azurian['Basicos']['Descuento'] =$sumDesc;
		  	}else{
		  		$ttyy = round($subim,2) + round($totalImpuestosFor,2);
		  	}

			$azurian['Basicos']['Total']=bcdiv(round($ttyy ,2),'1',2);

			//$queryCad = "SELECT cadenaOriginal,id_sale FROM app_pendienteFactura WHERE id_sale in (".$cadena.");";
			$queryCad = "SELECT p.cadenaOriginal,p.id_sale,v.impuestos,v.subtotal  FROM app_pendienteFactura p, app_pos_venta v WHERE p.id_sale=v.idVenta and id_sale in (".$cadena.")";
			//echo $queryCad;
			$resCad = $this->queryArray($queryCad);
			$fpf = array();
		foreach ($resCad['rows'] as $key => $value) {
			$azuriant=base64_decode($value['cadenaOriginal']);
			$azuriant = str_replace("\\", "", $azuriant);
			if($azuriant!=''){ $azuriant=json_decode($azuriant); }
			$azuriant = $this->object_to_array($azuriant);
			if(array_key_exists($azuriant['Basicos']['FormaPago'], $fpf)){
				if (isset($azurian['Basicos']['version'])){
					$fpf[$azuriant['Basicos']['FormaPago']]+=$azuriant['Basicos']['subTotal'];

				}else{

					$fpf[$azuriant['Basicos']['FormaPago']]+=$azuriant['Basicos']['SubTotal'];

				}

			}else{
				if (isset($azurian['Basicos']['version'])){
					$fpf[$azuriant['Basicos']['FormaPago']]=$azuriant['Basicos']['subTotal'];

				}else{

					$fpf[$azuriant['Basicos']['FormaPago']]=$azuriant['Basicos']['SubTotal'];

				}
			}
		}

		$banderas = 0;
		foreach ($fpf as $key => $value) {
			if($value>$banderas){
				$cl = $key;
				$banderas = $value;
			}

		}
		//echo '?'.$cl;
		$azurian['Basicos']['FormaPago'] = $cl;


		$soloExento = 1;

		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}

	}


	function verAcuse($id){
		$select = 'SELECT r.fecha_cancelacion, r.folio, c.rfc from app_respuestaFacturacion r left join pvt_configura_facturacion c on c.id=1 where r.id='.$id;
		$resp = $this->queryArray($select);
		return array('fecha' => $resp['rows'][0]['fecha_cancelacion'],'folio'=>$resp['rows'][0]['folio'],'rfc'=>$resp['rows'][0]['rfc'] );
		//return $resp['rows'];
	}
	function enviarAcuse($idFact,$correo,$imprime){

			$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
			$logo = $this->queryArray($logo);
			$r3 = $logo["rows"][0]['logoempresa'];

			$imagen='../../netwarelog/archivos/1/organizaciones/'.$r3;
			$imagesize=getimagesize($imagen);
			$porcentaje=0;
			if($imagesize[0]>200 && $imagesize[1]>90){
				if($imagesize[0]>$imagesize[1]){
					$porcentaje=intval(($imagesize[1]*100)/$imagesize[0]);
					$imagesize[0]=200;
					$imagesize[1]=(($porcentaje*200)/100);
				}else{
					$porcentaje=intval(($imagesize[0]*100)/$imagesize[1]);
					$imagesize[0]=200;
					$imagesize[1]=(($porcentaje*200)/100);
				}
			}
			//"../../netwarelog/archivos/1/organizaciones/'.$cliente[0]->logoempresa.'"
			$src="";
			if($imagen!="" && file_exists($imagen))
				$src='<img src="'.$imagen.'" style="width:'.$imagesize[0].'px;height:'.$imagesize[1].'px;display:block;margin:0 auto 0 auto;"/>';



			$resp = $this->verAcuse($idFact);
			$content = '<table>
				<tr>
					<td colspan="2">'.$src.'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="250px;"><b>Fecha y hora de solicitud:</b></td>
					<td class="acusefecha">'.$resp['fecha'].'</td>
				</tr>
				<tr>
					<td><b>Fecha y hora de cancelacion:</b></td>
					<td class="acusefecha">'.$resp['fecha'].'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><b>RFC emisor:</b></td>
					<td id="acuserfc">'.$resp['rfc'].'</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><b>Folio fiscal</b></td>
					<td id="acusefolio">'.$resp['folio'].'</td>
				</tr>
				<tr>
					<td><b>Estado CFDI</b></td>
					<td id="estadofolio">Cancelado</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				</table>';
			if($imprime==1){
				return array('contenido' => $content);
			}else{
				require_once('../../modulos/phpmailer/sendMail.php');

				$mail->Subject = "Acuse de Cancelacion";
				$mail->AltBody = "NetwarMonitor";
				$mail->MsgHTML($content);
				/*$mail->AddAttachment('../../modulos/facturas/'. $uid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uid .'.pdf'); */
				$correo = explode(';', $correo);
				foreach ($correo as $key => $value) {
					$mail->AddAddress($value, $value);
				}

				@$mail->Send();
				return  array('enviado' => 1 );
			}

	}

	function listar_pedidos($objeto){
	// Filtra por persona si existe
		$condicion = (!empty($objeto['persona'])) ? ' AND a.npersona='.$objeto['persona'] : '' ;
		// b.precio -> if(a.tipo_desc = '%',b.precio -(b.precio*(a.monto_desc/100)),b.precio) precio,
		// (b.precio*SUM(a.cantidad)) AS importe,  -> (if(a.tipo_desc = '%',b.precio -(b.precio*(a.monto_desc/100)),b.precio) *SUM(a.cantidad)) AS importe,
		$sql="	SELECT
					a.id, a.costo, a.idproducto AS idProducto, b.codigo, a.complementos, b.nombre, b.descripcion_larga AS descripcion,
					u.nombre AS unidad, b.id_unidad_venta AS idunidad,
					if(a.tipo_desc = '%',b.precio -(b.precio*(a.monto_desc/100)),b.precio) precio,
					SUM(a.cantidad) AS cantidad,
					b.ruta_imagen,
					(if(a.tipo_desc = '%',b.precio -(b.precio*(a.monto_desc/100)),b.precio) *SUM(a.cantidad)) AS importe,

					'' AS inpuestos, '' AS suma_impuestos, '' AS cargos,b.formulaIeps AS formula,
					a.npersona, a.opcionales, a.adicionales, a.sin, c.tipo, c.nombre AS nombreu,a.id_promocion,
					c.domicilio, (CASE a.id_promocion
						WHEN 0 THEN
							'producto'
						ELSE a.id END) as tipin, a.tipo_desc, a.monto_desc, a.precioaux
				FROM
					com_pedidos a
				LEFT JOIN
						app_productos b
					ON
						b.id=a.idproducto
				LEFT JOIN
						com_comandas d
					ON
						d.codigo='".$objeto['codigo']."'
				LEFT JOIN
						com_mesas c
					ON
						c.id_mesa=d.idmesa
				LEFT JOIN
						app_unidades_medida u
					ON
						b.id_unidad_venta=u.id
				WHERE
					idcomanda=(
						SELECT
							id
						FROM
							com_comandas
						WHERE
							codigo='".$objeto['codigo']."'
					)
				AND
					a.status !=3
				AND
					a.cantidad > 0
				AND
					a.dependencia_promocion = 0
				AND
					a.origen = 1".
				$condicion."
				GROUP BY
					tipin, a.npersona, a.idProducto, a.opcionales, a.adicionales, a.complementos, a.tipo_desc, a.monto_desc, a.precioaux
				ORDER BY
					a.npersona ASC, a.id ASC";
					//print_r($sql); exit();
		//echo $sql;
		$result = $this->queryArray($sql);

		return $result;
	}
	function checatarjetaregalo($numero, $monto) {
		$tarjetaQuery = "select valor,usada,montousado from tarjeta_regalo where numero='" . $numero . "'";

		$resultTarjeta = $this->queryArray($tarjetaQuery);

		if ($resultTarjeta["total"] > 0) {
			$disponible = (float) $resultTarjeta["rows"][0]["valor"] - (float) $resultTarjeta["rows"][0]["montousado"];

			if ($disponible < $monto) {
				return array("status" => false, "msg" => "Saldo disponible en tarjeta de regalo:$" . number_format($disponible, 2));
			}

			if ($resultTarjeta["rows"][0]["usada"] == 1) {
				return array("status" => false, "msg" => "Se ha agotado el saldo de la tarjeta de regalo.");
			} else {
				return array("status" => true, "data" => $monto);
			}
		} else {
			return array("status" => false, "msg" => "No esta registrado este numero de tarjeta.");
		}
	}
	function gridTarjetasRegalo(){
		$select = "SELECT tr.*, c.nombre cliente from tarjeta_regalo tr LEFT JOIN comun_cliente c ON tr.cliente = c.id";
		$res = $this->queryArray($select);

		$select2 = "SELECT count(*) as total FROM tarjeta_regalo";
		$res2 = $this->queryArray($select2);

		return  array('tarjetas' => $res['rows'] , 'total' => $res2['rows'][0]['total']);


	}
	function reloadtable($tipo){

		if($tipo == 1){
			$where = 'WHERE idVenta > 0;';
		}else{
			$where = 'WHERE cliente > 0;';
		}
		$sql = "SELECT tr.*, (tr.valor - tr.montousado) disponible, c.nombre cliente 
		from tarjeta_regalo tr 
		LEFT JOIN comun_cliente c ON tr.cliente = c.id ".$where;
		$res = $this->queryArray($sql);
		return $res['rows'];
	}
	function modificaTarjeta($idCard, $numero, $monto){
		//$upd = "UPDATE tarjeta_regalo set monto='".$monto."', "
	}
	function guardarTarjeta($numero, $monto,  $puntos, $cliente){
		$select = "SELECT id from tarjeta_regalo where numero='".$numero."'";
		$resp = $this->queryArray($resp);

		if($resp['total'] > 0){
			return  array('idTarjeta' => 0 );
		}else{
			$insert = "INSERT into tarjeta_regalo (numero,valor,usada,puntos,cliente) values ('".$numero."','".$monto."', '0','".$puntos."','".$cliente."')";
			$resInsert = $this->queryArray($insert);

			return array('idTarjeta' =>  $resInsert['insertId'] );
		}


	}
	function desactivaGift($id){
		$update = 'UPDATE tarjeta_regalo set usada=1 where id='.$id;
		$res = $this->queryArray($update);

		return array('estatus' => true );
	}
	function activaGift($id){
		$update = 'UPDATE tarjeta_regalo set usada=0 where id='.$id;
		$res = $this->queryArray($update);

		return array('estatus' => true );
	}
	function verTarjeta($id){
		$se = 'SELECT * from tarjeta_regalo where id='.$id;
		$res = $this->queryArray($se);

		return  array('numero' => $res['rows'][0]['numero'], 'id' => $res['rows'][0]['id'],'monto' => $res['rows'][0]['valor'], 'montousado' => $res['rows'][0]['montousado'], 'disponible' =>  ($res['rows'][0]['valor'] - $res['rows'][0]['montousado']) );
	}

	function dataMonedero($POST){
		$sql = "SELECT tj.id, tj.puntos, cc.id idcliente, cc.nombre cliente 
		        FROM tarjeta_regalo tj
				LEFT JOIN comun_cliente cc on cc.id = tj.cliente
				where tj.numero = ".$POST['id']." and tj.cliente > 0 and tj.puntos > 0;";
		$result = $this->queryArray($sql);
		return $result;
	}

	function saveNewMon($POST){
		//guarda nuevo monedero
		$sql = "INSERT INTO tarjeta_regalo (`numero`, `valor`, `usada`, `montousado`, `puntos`, `cliente`, `idVenta`)
			    VALUES (".$POST['monedero'].", 0, 0, NULL, ".$POST['puntos'].", ".$POST['idcliente'].",0);";
		$res = $this->queryArray($sql);
		$insertedId = $res['insertId'];

		return $res;
	}

	function saveNewRep($POST){
		//guarda nuevo monedero
		$sql = "INSERT INTO tarjeta_regalo (`numero`, `valor`, `usada`, `montousado`, `puntos`, `cliente`, `idVenta`)
			    VALUES (".$POST['numMonederoNew'].", 0, 0, NULL, ".$POST['puntos'].", ".$POST['idcliente'].",0);";
		$res = $this->queryArray($sql);
		$insertedId = $res['insertId'];

		// actualiza viejo monedero y lo deja en 0
		$sql = 'UPDATE tarjeta_regalo set usada = 1, puntos = 0 where numero='.$POST['numMonedero'].';';
		$res = $this->queryArray($sql);

		return $res;
	}


	function verificaMonedero($POST){
		$sql = "SELECT id FROM tarjeta_regalo where numero = ".$POST['monedero'].";";
		$result = $this->queryArray($sql);
		return $result;
	}
	function verificaVenta($POST){
		$sql = "SELECT vp.cantidad, (vp.total/vp.cantidad) valorTarjeta from app_pos_venta v
				left join app_pos_venta_producto vp on vp.idVenta = v.idVenta
				left join app_productos p on p.id = vp.idProducto
				where v.idVenta = ".$POST['idVenta']." and p.tipo_producto = 10;";
		$result = $this->queryArray($sql);
		return $result['rows'];
	}
	function saveTarjeta($newArray){

		foreach ($newArray as $k => $v) {
			$sql2 .= "('".$v['tarjeta']."', '".$v['monto']."', 0, 0.00, 0.00, 0, '".$v['idVenta']."'),";
		}
		$sql2 = substr($sql2, 0, -1).';';
		
		
		echo $sql = "INSERT INTO `tarjeta_regalo` (`numero`, `valor`, `usada`, `montousado`, `puntos`, `cliente`, `idVenta`)
		VALUES ".$sql2;
		//$res = $this->queryArray($sql);
		return $res;

		 
	}

    function configDatos(){
        $sel = 'SELECT * from app_config_ventas';
        $res = $this->queryArray($sel);

        return $res['rows'];
    }

    function infoFact($id){
        $sel = 'SELECT * from app_respuestaFacturacion where id='.$id;
        $res = $this->queryArray($sel);

            $azurian=base64_decode($res['rows'][0]['cadenaOriginal']);
            $azurian = str_replace("\\", "", $azurian);
            if($azurian!=''){ $azurian=json_decode($azurian); }
            $azurian = $this->object_to_array($azurian);

	    	if (isset($azurian['Basicos']['total'])){
	    		$totalAzu = $azurian['Basicos']['total'];
			}else{
				$totalAzu = $azurian['Basicos']['Total'];
			}


        $selnot = 'SELECT n.monto,f.folio,n.idVenta
                    from app_notas_credito n, app_respuestaFacturacion f
                where n.idFactura ='.$id.' and n.idNota=f.id';
        $resno = $this->queryArray($selnot);

        foreach ($resno['rows'] as $key => $value) {
            $disponible +=$value['monto'];
        }
        $disponible = str_replace(',', '', number_format($disponible,2));
        $disponible = (float) $totalAzu - (float) $disponible;

        return array('monto' => $totalAzu, 'notas' => $resno['rows'], 'disponible' =>$disponible);

    }

    function creaNota($monto,$montosiniva,$iva,$total,$idFac){


          $montosiniva=str_replace(',', '', number_format($montosiniva,2));
          $iva=str_replace(',','',number_format($iva,2));
          $total=str_replace(',','',number_format($total,2));

        require_once('../../modulos/SAT/config.php');
        date_default_timezone_set("Mexico/General");
        $fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-5 minute"));

        $SeleCad = "SELECT cadenaOriginal, idFact, idSale FROM app_respuestaFacturacion WHERE id=".$idFac;
        $cadenaOri = $this->queryArray($SeleCad);

        $rrfc=$cadenaOri['rows'][0]['idFact'];
        $idFact=$cadenaOri['rows'][0]['idFact'];
        $idVenta=$cadenaOri['rows'][0]['idSale'];
        //echo $cadenaOri['rows'][0]['cadenaOriginal'];
        $azurian=base64_decode($cadenaOri['rows'][0]['cadenaOriginal']);

        $azurian = str_replace("\\", "", $azurian);
        if($azurian!=''){
            $azurian=json_decode($azurian);
        }
        $azurian = $this->object_to_array($azurian);

       $facConcepto = $azurian['Basicos']['folio'];


          if (isset($azurian['nn']['nn']['IVA']['0.0']['Valor'])) {
              $iva = 0.00;
              $ivaPorcet = '0.00';
              $montosiniva = $total;
          }else{
              $ivaPorcet = '16.00';
          }

        ////Busca el pack para facturar
        $qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
        $respac = $this->queryArray($qrpac);
        $pac = $respac["rows"][0]["pac"];

            $queryConfiguracion = "SELECT a.*, b.regimen as regimenf FROM pvt_configura_facturacion a INNER JOIN pvt_catalogo_regimen b WHERE a.id=1 AND b.id=a.regimen;";
            $returnConfiguracion = $this->queryArray($queryConfiguracion);
            if ($returnConfiguracion["total"] > 0) {
                $r = (object) $returnConfiguracion["rows"][0];

                /* DATOS OBLIGATORIOS DEL EMISOR
                ================================================================== */
                $rfc_cliente = $r->rfc;

                $parametros['EmisorTimbre'] = array();
                $parametros['EmisorTimbre']['RFC'] = $r->rfc;
                $parametros['EmisorTimbre']['RegimenFiscal'] = $r->regimenf;
                $parametros['EmisorTimbre']['Pais'] = $r->pais;
                $parametros['EmisorTimbre']['RazonSocial'] = $r->razon_social;
                $parametros['EmisorTimbre']['Calle'] = $r->calle;
                $parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
                $parametros['EmisorTimbre']['Colonia'] = $r->colonia;
                $parametros['EmisorTimbre']['Ciudad'] = $r->ciudad; //Ciudad o Localidad
                $parametros['EmisorTimbre']['Municipio'] = $r->municipio;
                $parametros['EmisorTimbre']['Estado'] = $r->estado;
                $parametros['EmisorTimbre']['CP'] = $r->cp;
                $cer_cliente = $pathdc . '/' . $r->cer;
                $key_cliente = $pathdc . '/' . $r->llave;
                $pwd_cliente = $r->clave;
            } else {

                $JSON = array('success' => 0,
                    'error' => 1001,
                    'mensaje' => 'No existen datos de emisor.');
                echo json_encode($JSON);
                exit();
            }

        /* Nota de credito
          ============================================================== */
          /*echo 'primero';
          print_r($azurian['Receptor']);
          echo '<br><br>'; */
         $azurian['Basicos']['tipoDeComprobante']='egreso';
          if($rrfc>0){
            //$result = $this->conexion->consultar("SELECT * FROM comun_facturacion WHERE id='$rrfc';");
            $rs = $this->queryArray("SELECT c.id, c.rfc, c.razon_social, c.correo, c.pais, c.regimen_fiscal, c.domicilio, c.num_ext, c.cp, c.colonia, e.estado, c.ciudad, c.municipio from comun_facturacion c , estados e WHERE e.idestado=c.estado and id='$rrfc';");

            $idCliente=$rs['rows'][0]['nombre'];
           /* $azurian['Receptor']['rfc']=strtoupper($rs['rows'][0]['rfc']);
            $azurian['Receptor']['nombre']=strtoupper($rs['rows'][0]['razon_social']);
            $azurian['DomicilioReceptor']['calle']=$rs['rows'][0]['domicilio'];
            $azurian['DomicilioReceptor']['noExterior']=$rs['rows'][0]['num_ext'];
            $azurian['DomicilioReceptor']['colonia']=$rs['rows'][0]['colonia'];
            $azurian['DomicilioReceptor']['localidad']=$rs['rows'][0]['ciudad'];
            $azurian['DomicilioReceptor']['municipio']=$rs['rows'][0]['municipio'];
            $azurian['DomicilioReceptor']['estado']=$rs['rows'][0]['estado'];
            $azurian['DomicilioReceptor']['pais']=$rs['rows'][0]['pais'];
            $azurian['DomicilioReceptor']['codigoPostal']=$rs['rows'][0]['cp'];
            $azurian['Correo']['Correo'] = $rs['rows'][0]['correo']; */
          }else{
            $idCliente='';
           /* $azurian['Receptor']['rfc']='XAXX010101000';
            $azurian['Receptor']['nombre']='Factura generica';
            $azurian['DomicilioReceptor']['calle']='';
            $azurian['DomicilioReceptor']['noExterior']='';
            $azurian['DomicilioReceptor']['colonia']='';
            $azurian['DomicilioReceptor']['localidad']='';
            $azurian['DomicilioReceptor']['municipio']='';
            $azurian['DomicilioReceptor']['estado']='';
            $azurian['DomicilioReceptor']['pais']='';
            $azurian['DomicilioReceptor']['codigoPostal']='';
            $azurian['Correo']['Correo'] = ''; */
          }

              $result3 = "SELECT * FROM pvt_serie_folio WHERE id=1;";
              $rs3 = $this->queryArray($result3);

              $result4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
              $rs4 = $this->queryArray($result4);

              $azurian['org']['logo'] = $rs4['rows'][0]['logoempresa'];

              /* Datos serie y folio
              ============================================================== */
              $azurian['Basicos']['serie']=$rs3['rows'][0]['serie_nc']; //No obligatorio
              $azurian['Basicos']['folio']=$rs3['rows'][0]['folio_nc'];

              /* Datos Emisor
              ============================================================== */
              $azurian['Emisor']['rfc']=strtoupper($parametros['EmisorTimbre']['RFC']);
              $azurian['Emisor']['nombre']=strtoupper($parametros['EmisorTimbre']['RazonSocial']);

              /* Datos Fiscales Emisor
              ============================================================== */
              $azurian['FiscalesEmisor']['calle']=$parametros['EmisorTimbre']['Calle'];
              $azurian['FiscalesEmisor']['noExterior']=$parametros['EmisorTimbre']['NumExt'];
              $azurian['FiscalesEmisor']['colonia']=$parametros['EmisorTimbre']['Colonia'];
              $azurian['FiscalesEmisor']['localidad']=$parametros['EmisorTimbre']['Ciudad'];
              $azurian['FiscalesEmisor']['municipio']=$parametros['EmisorTimbre']['Municipio'];
              $azurian['FiscalesEmisor']['estado']=$parametros['EmisorTimbre']['Estado'];
              $azurian['FiscalesEmisor']['pais']=$parametros['EmisorTimbre']['Pais'];
              $azurian['FiscalesEmisor']['codigoPostal']=$parametros['EmisorTimbre']['CP'];

              /* Datos Regimen
              ============================================================== */
              $azurian['Regimen']['Regimen']=$parametros['EmisorTimbre']['RegimenFiscal'];

              /* Fecha Factura
              ============================================================== */
              $azurian['Basicos']['fecha']=$fecha;
              $azurian['Basicos']['sello']='';

            /* Impuestos
              ============================================================== */
              $tisr=$azurian['Impuestos']['totalImpuestosRetenidos'];
              $tiva=$azurian['Impuestos']['totalImpuestosTrasladados'];
              $tieps=$azurian['Impuestos']['totalImpuestosIeps'];
               //$azurian['Observacion']['Observacion']="Esta nota de credito esta vinculada a la factura con folio ".$folio;
              $nn2["IVA"][$ivaPorcet] = str_replace(",", "", $iva);
              //echo $nn2["IVA"]["16.0"]["Valor"];
              //exit();

              $azurian['nn']['nn']=$nn2;

              $nnf2["IVA"][$ivaPorcet]["Valor"] = str_replace(",", "", $iva);
              $azurian['nnf']['nnf']=$nnf2;


              $conceptosOri='';
              $conceptos='';

                  $conceptosOri.='|1|';
                    $conceptosOri.='concepto|';
                    $conceptosOri.='Nota de credito vinculada a la factura con folio '.$facConcepto.'|';
                    $conceptosOri.=str_replace(",", "", $montosiniva) . '|';
                    $conceptosOri.=str_replace(",", "", $montosiniva);
                    $conceptos.="<cfdi:Concepto cantidad='1' unidad='concepto' descripcion='Nota de credito vinculada a la factura con folio ".$facConcepto."' valorUnitario='" . str_replace(",", "", $montosiniva) . "' importe='" . str_replace(",", "", $montosiniva) . "'/>";

              $azurian['Conceptos']['conceptos'] = $conceptos;
              $azurian['Conceptos']['conceptosOri'] = $conceptosOri;


              $azurian['Basicos']['subTotal'] = str_replace(",", "", $montosiniva);
              $azurian['Basicos']['total'] = str_replace(",", "", $total);
              $ivax="<cfdi:Traslados><cfdi:Traslado impuesto='IVA' tasa='".$ivaPorcet."' importe='".str_replace(",", "", $iva)."' /></cfdi:Traslados>";
              $azurian['Impuestos']['ivas'] = $ivax;
              $azurian['Impuestos']['totalImpuestosTrasladados']=str_replace(",", "", $iva);
              $azurian['Impuestos']['iva']='|IVA|'.$ivaPorcet.'|'.str_replace(",", "", $iva).'|'.str_replace(",", "", $iva).'';

              //print_r($azurian);
              // exit();

        if($pac==2){
            require_once('../../modulos/SAT/funcionesSAT2.php');
        }else if($pac==1){
            require_once('../../modulos/lib/nusoap.php');
            require_once('../../modulos/SAT/funcionesSAT.php');
        }


    }


    function guardaNota($UUID,$noCertificadoSAT,$selloCFD,$selloSAT,$FechaTimbrado,$idComprobante,$idFact,$idVenta,$noCertificado,$tipoComp,$monto,$cliente,$trackId,$idRefact,$azurian,$total=0.00,$idFacturaRelacion){

        $azurian=base64_encode($azurian);
        $fechaactual=preg_replace('/T/', ' ', $FechaTimbrado);
        if($idRefact=='c'){
            $tipoComp='C';
            $q="UPDATE app_respuestaFacturacion SET borrado=2 WHERE idSale='$idVenta' AND tipoComp='F'";
            $res = $this->queryArray($q);
        }
       /* $inser = "INSERT INTO app_respuestaFacturacion
            (idSale,idFact,folio,factNum,serieCsdSat,serieCsdEmisor,selloDigitalSat,selloDigitalEmisor,fecha,borrado,tipoComp,idComprobante,cadenaOriginal,origen) VALUES ('".$idVenta."','".$idFact."','".$UUID."','".$trackId."','".$noCertificadoSAT."','".$noCertificado."','".$selloSAT."','".$selloCFD."','".$fechaactual."',0,'".$tipoComp."','".$idComprobante."','".$azurian."','2');";
            //exit();
            //echo $inser;


        $res = $this->queryArray($inser); */

        $tipoComp = 'C';
        $insertRespuestaFacturacion = "INSERT INTO app_respuestaFacturacion "
		. "(idSale,idFact,folio,factNum,serieCsdSat,serieCsdEmisor,selloDigitalSat,selloDigitalEmisor,fecha,borrado,tipoComp,idComprobante,cadenaOriginal,xmlfile,origen,version) VALUES "
		. "('" . $idVenta . "','" . $idFact . "','" . $UUID . "','" . $trackId . "','" . $noCertificadoSAT . "','" . $noCertificado . "','" . $selloSAT . "','" . $selloCFD . "','" . $fechaactual . "',0,'" . $tipoComp . "','" . $idComprobante . "','" . $azurian . "','" . $UUID . ".xml','2','".$version."');";
		$res = $this->queryArray($insertRespuestaFacturacion);


		$selecNC ="SELECT id from app_respuestaFacturacion where folio='".$idFacturaRelacion."'";
        $resNx = $this->queryArray($selecNC);
        $idFacturaRelacion = $resNx['rows'][0]['id'];

        $insertedId = $res['insertId'];
        $ins = "INSERT into app_notas_credito(idFactura,idNota,monto,idVenta) values('".$idFacturaRelacion."','".$insertedId."','".$total."','".$idVenta."')";
        $resNot = $this->queryArray($ins);


        if(is_numeric($insertedId))
        {
            $this->queryArray('UPDATE pvt_serie_folio SET folio_nc=folio_nc+1 where id=1');
        }

        /*if(preg_match('/all/', $idRefact)){
            $idRefact=preg_replace('/all/', '', $idRefact);
            $idRefact = trim($idRefact,',');
            $this->queryArray("UPDATE app_pendienteFactura SET facturado=1 WHERE id_sale in (".$idRefact.")");
        } */

        if($idRefact>0 && $idRefact!='c'){
            $this->queryArray("UPDATE app_pendienteFactura SET facturado=1 WHERE id_sale='$idRefact'");
        }

        /*if($idRefact=='c'){
            $tipoComp='C';
            $q="INSERT into pvt_notadeCredito (total,idfac) values ('".$total."','".$insertedId."')";
            $res = $this->queryArray();
        } */

        return $insertedId;

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
    public function aplicaCortesiaGeneral(){
    	$_SESSION['caja']['cargos']['total'] = 0.00;

    	return  array('corte' => true );
    }
///////////////// ******** ---- 	FIN listar_pedidos		------ ************ //////////////////

///////////////// ******** ---- 	listar_productos_comp		------ ************ //////////////////
//////// Obtiene el nombre de los productos y su costo
	// Como parametros puede recibir:
		// adicionales-> string con los id de los adicionales

	function listar_productos_comp($objeto){
	// Si viene el id del estado Filtra por el id del estado
		$condicion.=(!empty($objeto['complementos']))?' AND b.id IN('.$objeto['complementos'].')':'';

		$sql="	SELECT
					b.codigo, b.id
				FROM
					app_productos b
				WHERE
					1=1 ".
				$condicion;
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN listar_productos_comp	------ ************ //////////////////

///////////////// ******** ---- 	listar_productos		------ ************ //////////////////
//////// Obtiene el nombre de los productos y su costo
	// Como parametros puede recibir:
		// adicionales-> string con los id de los adicionales

	function listar_productos($objeto){
	// Si viene el id del estado Filtra por el id del estado
		$condicion.=(!empty($objeto['adicionales']))?' AND b.id IN('.$objeto['adicionales'].')':'';

		$sql="	SELECT
					b.codigo, b.id
				FROM
					app_productos b
				WHERE
					1=1 ".
				$condicion;
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN listar_productos	------ ************ //////////////////

///////////////// ******** ---- 			id_venta		------ ************ //////////////////
//////// Obtiene el ultimo ID de las ventas
	// Como parametros puede recibir:

	function id_venta($objeto){
		$sql = "	SELECT
						MAX(idVenta) AS id_venta
					FROM
						app_pos_venta
					FOR UPDATE";
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 			FIN id_venta	------ ************ //////////////////

///////////////// ******** ---- 			pagar_comanda	------ ************ //////////////////
//////// Actualiza el status de la comanda a pagado y guarda el ID de la venta
	// Como parametros puede recibir:
		// id_venta -> ID de la venta
		// codigo -> Codigo de la comanda

	function pagar_comanda($objeto){
		$sql = "	UPDATE
						com_comandas
					SET
						status=1,
						id_venta = IF(id_venta!='', CONCAT(id_venta, ', ".$objeto['id_venta']."'), ".$objeto['id_venta'].")
					WHERE
						codigo='".$objeto['codigo']."'";
		// return $sql;
		$result = $this->query($sql);

		return $result;
	}

///////////////// ******** ---- 		FIN pagar_comanda	------ ************ //////////////////

///////////////// ******** ---- 		listar_comandas		------ ************ //////////////////
//////// Consulta las comandas pagadas y las regresa en un array
	// Como parametros puede recibir:
		// codigo -> codigo de la comanda
		// persona -> No de persona
		// status -> Estatus de la comanda

	function listar_comandas($objeto){
	// Filtra por la persona si existe
		$condicion .= (!empty($objeto['persona'])) ? ' AND tickets LIKE(\'%'.$objeto['persona'].'%\')' : '' ;
	// Filtra por el status si existe
		$condicion .= (!empty($objeto['status'])) ? ' AND status='.$objeto['status'] : ' AND status = 1' ;

		$sql = "SELECT
					id, id_venta
				FROM
					com_comandas
				WHERE
					codigo = '".$objeto['codigo']."'".
				$condicion;
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN listar_comandas		------ ************ //////////////////

///////////////// ******** ---- 	cambiar_descipcion		------ ************ //////////////////
//////// Actualiza el la descripcion del producto en la tabla app_campos_foodware
	// Como parametros puede recibir:
		// id -> ID del producto
		// descripcion -> texto que se cambiara

	function cambiar_descipcion($objeto){
		$sql = "	UPDATE
						app_campos_foodware
					SET
						descripcion='".$objeto['descripcion']."'
					WHERE
						id_producto=".$objeto['id'];
		// return $sql;
		$result = $this->query($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN cambiar_descipcion		------ ************ //////////////////

///////////////// ******** ---- 		cambiar_tickets			------ ************ //////////////////
//////// Actualiza el los tickets con el numero de persona
	// Como parametros puede recibir:
		// persona -> Numero de persona
		// codigo -> Codigo de la comanda

	function cambiar_tickets($objeto){
		$sql = "UPDATE
					com_comandas
				SET
					tickets = IF(tickets!='', CONCAT(tickets, ', ".$objeto['persona']."'), '".$objeto['persona']."')
				WHERE
					codigo='".$objeto['codigo']."'";
		// return $sql;
		$result = $this->query($sql);

		return $result;
	}

///////////////// ******** ---- 		FIN cambiar_tickets		------ ************ //////////////////

///////////////// ******** ---- 	listar_pedidos_sub_comanda	------ ************ //////////////////
////////Optiene los pedidos de la ssub comanda y los regresa en un array
	// Como parametros puede recibir:
		// codigo -> Codigo de la sub comanda

	function listar_pedidos_sub_comanda($objeto){
	// Optiene los IDs de los pedidos
		$pedidos = "SELECT
						pedidos
					FROM
						com_sub_comandas s
					WHERE
						s.codigo = '".$objeto['codigo']."'";
		$pedidos = $this->queryArray($pedidos);
		$pedidos = $pedidos['rows'][0]['pedidos'];

	// Consulta los productos
		$sql = "SELECT if(sc.tipo = 2,sc.cantidad,pe.cantidad) cantidad, p.codigo, p.nombre, pe.adicionales
				FROM com_pedidos pe
				left join com_sub_comandas sc on sc.codigo = '".$objeto['codigo']."'
				INNER JOIN app_productos p ON pe.idproducto=p.id
				WHERE pe.id IN (".$pedidos.") AND pe.origen = 1;";
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- FIN listar_pedidos_sub_comanda	------ ************ //////////////////

///////////////// ******** ---- 		pagar_sub_comanda		------ ************ //////////////////
//////// Actualiza el status de la  sub comanda a pagado y guarda el ID de la venta
	// Como parametros puede recibir:
		// id_venta -> ID de la venta
		// codigo -> Codigo de la comanda

	function pagar_sub_comanda($objeto){
		$sql = "SET @comanda = (	SELECT
										idpadre
									FROM
										com_sub_comandas
									WHERE
										codigo = '".$objeto['codigo']."');

				UPDATE
					com_comandas
				SET
					status = 1,
					id_venta = IF(id_venta!='', CONCAT(id_venta, ', ".$objeto['id_venta']."'), ".$objeto['id_venta'].")
				WHERE
					id=@comanda;

				UPDATE
					com_sub_comandas
				SET
					estatus = 1,
					id_venta = ".$objeto['id_venta']."
				WHERE
					codigo = '".$objeto['codigo']."';";
		// return $sql;
		$result = $this->dataTransact($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN pagar_sub_comanda		------ ************ //////////////////

///////////////// ******** ---- 	listar_sub_comandas			------ ************ //////////////////
//////// Consulta las sub comandas pagadas y las regresa en un array
	// Como parametros puede recibir:
		// codigo -> codigo de la sub comanda

	function listar_sub_comandas($objeto){
		$sql = "SELECT
					CONCAT(c.id, '-', s.id) AS id, s.id_venta
				FROM
					com_sub_comandas s
				INNER JOIN
						com_comandas c
					ON
						s.idpadre = c.id
				WHERE
					s.codigo = '".$objeto['codigo']."'
				AND
					s.estatus = 1";
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 	FIN listar_sub_comandas		------ ************ //////////////////

///////////////// ******** ---- 		detalles_mesa			------ ************ //////////////////
//////// Consulta los datos de la mesa y los regresa en un array
	// Como parametros puede recibir:
		// codigo -> codigo de la sub comanda

	function detalles_mesa($objeto){
		$condicion = (!empty($objeto['codigo_sub'])) ? "s.codigo = '".$objeto['codigo_sub']."'" : "c.codigo = '".$objeto['codigo']."'" ;

		$sql = "SELECT
					m.nombre AS nombre_mesa, m.domicilio, cli.celular AS tel, u.usuario AS nombre_mesero,
					c.comensales AS persona, c.codigo, cli.colonia , cli.celular, cli.telefono1, cli.referencia, cli.id id_cliente
				FROM
					com_comandas c
				LEFT JOIN
						com_sub_comandas s
					ON
						s.idpadre = c.id
				LEFT JOIN
						com_mesas m
					ON
						m.id_mesa = c.idmesa
				LEFT JOIN
						comun_cliente cli
					ON
						cli.nombre = m.nombre
				LEFT JOIN
						 accelog_usuarios u
					ON
						c.idempleado = u.idempleado
				WHERE ".
					$condicion;
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 			FIN detalles_mesa				------ ************ //////////////////

///////////////// ******** ---- 		listar_comandas_pendientes			------ ************ //////////////////
//////// Consulta las comandas y las regresa en un array
	// Como parametros recibe:
		// id -> id de la comanda
		// f_ini -> fecha y hora de inicio
		// F_fin -> fecha y hora final
		// status -> status de la comanda(abierta, cerrada, eliminada)

	function listar_comandas_pendientes($objeto) {
	// Si viene el id del la comanda Filtra por el id de la comanda
		$condicion .= (!empty($objeto['id'])) ? ' AND c.id = \'' . $objeto['id'] . '\'' : '';
	// Si viene el individual Filtra por la manera en que se cerro la comanda(1->todo junto, 2->individual, 3->mandar a caja)
		$condicion .= ($objeto['individual'] != '*' && $objeto['individual'] != '') ?
			' AND c.individual = \'' . $objeto['individual'] . '\'' : '';
	// Filtra por el status(default 2 -> comanda cerrada)
		$condicion .= (!empty($objeto['status'])) ? ' AND c.status = ' . $objeto['status'] : ' AND c.status = 2';

	// Agrupa la consulta por los parametros indicados si existe, si no la agrupa por id
		$condicion .= (!empty($objeto['agrupar'])) ? ' GROUP BY ' . $objeto['agrupar'] : ' GROUP BY c.id';

	// Ordena la consulta por los parametros indicados si existe, si no la ordena por id Descendente
		$orden .= (!empty($objeto['orden'])) ? ' ' . $objeto['orden'] : ' c.codigo DESC';

		session_start();
		$sucursal = "	SELECT
							mp.idSuc AS id
						FROM
							administracion_usuarios au
						INNER JOIN
								mrp_sucursal mp
							ON
								mp.idSuc = au.idSuc
						WHERE
							au.idempleado = " . $_SESSION['accelog_idempleado'] . "
						LIMIT 1";
		$sucursal = $this -> queryArray($sucursal);
		$sucursal = $sucursal['rows'][0]['id'];

		$sql = "SELECT
					COUNT(c.id) AS comandas, c.id, c.idmesa, c.personas, c.total,
					c.id_venta AS venta, m.nombre AS nombre_mesa,
					(CASE m.tipo
						WHEN 0 THEN
							'Mesa'
						WHEN 1 THEN
							'Para llevar'
						WHEN 2 THEN
							'A domicilio'
						ELSE '---' END) as tipo,
					(CASE c.status
						WHEN 0 THEN
							'Abierta'
						WHEN 1 THEN
							'Cerrada / Pagada'
						WHEN 2 THEN
							'Cerrada / Sin pago'
						WHEN 3 THEN
							'Eliminada'
						ELSE '---' END) as status,
					u.usuario,c.codigo,c.timestamp,c.total, c.fin,
					SUM(promedioComensal) AS promedioComensal, GROUP_CONCAT(s.id) AS sub_comandas, suc.nombre AS sucursal,
					cli.celular AS tel
				FROM
					 accelog_usuarios u, com_comandas c
				LEFT JOIN
						com_mesas m
					ON
						c.idmesa = m.id_mesa
				LEFT JOIN
						comun_cliente cli
					ON
						cli.nombre = m.nombre
				LEFT JOIN
						com_sub_comandas s
					ON
						s.idpadre = c.id
				LEFT JOIN
						mrp_sucursal suc
					ON
						suc.idSuc = m.idSuc
				WHERE
					c.idempleado = u.idempleado
				AND
					m.idSuc =  " . $sucursal . " " .
				$condicion . "
				ORDER BY " . $orden;
		// return $sql;
		$result = $this -> queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 			FIN listar_comandas_pendientes			------ ************ //////////////////

///////////////// ******** ---- 				listar_tipo_cambio					------ ************ //////////////////
//////// Consulta el tipo de cambio y lo devuelve
	// Como parametros puede recibir:
		// moneda -> ID de la moneda

	function listar_tipo_cambio($objeto){
		$sql = "SELECT
					*
				FROM
					cont_tipo_cambio
				WHERE
					moneda = ".$objeto['moneda']."
				ORDER BY
					fecha DESC
				LIMIT
					1";
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 				FIN listar_tipo_cambio				------ ************ //////////////////

///////////////// ******** ---- 				listar_vias_contacto				------ ************ //////////////////
//////// Consulta los datos de las vias de contacto en la BD
	// Como parametros puede recibir:

	function listar_vias_contacto($objet) {
		$sql = "SELECT
					*
				FROM
					com_vias_contacto";
		$result = $this -> queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 			FIN listar_vias_contacto				------ ************ //////////////////

		function sucursal() {
			// Obtiene la sucursal
				session_start();
				$sucursal = " SELECT DISTINCT mp.idSuc AS id FROM administracion_usuarios au
								INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc
								WHERE au.idempleado = " . $_SESSION['accelog_idempleado'] . " LIMIT 1";
				$sucursal = $this -> queryArray($sucursal);
				$sucursal = $sucursal['rows'][0]['id'];
				return $sucursal;
		}

///////////////// ******** ---- 			listar_ajustes_foodware					------ ************ //////////////////
//////// Consulta los ajustes de Foodware y los devuelve en un array
	// Como parametros puede recibir:
	function listar_ajustes_foodware($objet) {
		$sucursal = $this->sucursal();
		$sql = "SELECT
					*
				FROM
					com_configuracion WHERE id_sucursal = ".$sucursal.";";
		$result = $this -> queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 			FIN listar_ajustes_foodware				------ ************ //////////////////

///////////////// ******** ---- 			listar_detalles_comanda					------ ************ //////////////////
//////// Consulta los datos de la comanda y los devuelve en un array
	// Como parametros puede recibir:
		// id_venta -> ID de la venta

	function listar_detalles_comanda($objeto){
		$sql = "SELECT
					m.nombre AS nombre_mesa, m.domicilio, cli.celular AS tel, u.usuario AS nombre_mesero
				FROM
					com_comandas c
				LEFT JOIN
						com_mesas m
					ON
						m.id_mesa = c.idmesa
				LEFT JOIN
						comun_cliente cli
					ON
						cli.nombre = m.nombre
				LEFT JOIN
						 accelog_usuarios u
					ON
						c.idempleado = u.idempleado
				WHERE
					c.id_venta = ".$objeto['id_venta'];
		// return $sql;
		$result = $this->queryArray($sql);

		return $result;
	}

///////////////// ******** ---- 			FIN listar_detalles_comanda				------ ************ //////////////////

	//ch@
	function updateRepa($id_comanda){
		date_default_timezone_set('America/Mexico_City');
		$f_pagada = date('Y-m-d H:i:s');
		$sql = "UPDATE com_bit_repartidores
				SET fecha_pedido_pagado = '$f_pagada', estatus = 3
				WHERE id_comanda = '$id_comanda';";
		return $this->query($sql);
	}
	function updateMesa($id_comanda){
		$sql = "SELECT c.idmesa from com_comandas c
				left join com_mesas m on m.id_mesa = c.idmesa
				where c.id = '$id_comanda' and m.tipo > 0;";
		$result = $this->queryArray($sql);
		$idmesa = $result['rows'][0]['idmesa'];
		if($idmesa != ''){
			$sql = "UPDATE com_mesas
				SET status = 2
				WHERE id_mesa = '$idmesa';";
			return $this->query($sql);
		}
		return $idmesa;
	}
	//ch@ fin

    function obtenerConfigVenta() {

        $sql = "SELECT  *
                FROM    app_config_ventas
                WHERE   id=1";
        $res = $this->queryArray($sql);
        return $res['rows'][0];
    }

    function obtenerGarantiaVenta( $idVenta ) {
    	$sql = "SELECT
				v.idVenta AS id_venta,
				CASE
					WHEN v.idCliente IS  NULL THEN 'Publico general'
					ELSE c.nombre
				END AS cliente,
				v.fecha AS fecha
				FROM	app_pos_venta AS v, comun_cliente AS c
				WHERE	(v.idCliente = c.id OR v.idCliente IS NULL) AND v.idVenta = '$idVenta'
				LIMIT	1";
		$res = $this->queryArray($sql);

		$sql = "SELECT	vp.idProducto
				FROM	app_pos_venta_producto AS vp
				WHERE	vp.idVenta = '$idVenta' ";

		$resTmp = $this->queryArray($sql);

		$res2 = [];
		foreach ($resTmp['rows'] as $key => $value) {

			$idProducto = $value['idProducto'];
			$sql = "SELECT	vp.idventa_producto AS id_venta_producto, p.codigo AS codigo, p.nombre AS nombre, vp.cantidad AS cantidad, p.precio AS precio_producto, vp.impuestosproductoventa AS impuesto, vp.total AS subtotal, g.derecho AS derecho_garantia, DATE_ADD( DATE( v.fecha ) , INTERVAL g.duracion DAY ) >= DATE( CURRENT_DATE() )  AS vigencia_garantia
				FROM	app_pos_venta AS v,  app_pos_venta_producto AS vp, app_productos AS p, app_pos_garantia_producto AS gp, app_pos_garantia AS g
				WHERE	v.idVenta = vp.idVenta AND vp.idProducto = p.id AND p.id = gp.id_producto AND gp.id_garantia = g.id AND vp.idVenta = '$idVenta'  AND vp.idProducto = '$idProducto'";
			$rs = $this->queryArray($sql);

			if( $rs['total'] == "0") { //linea
				$sql = "SELECT	vp.idventa_producto AS id_venta_producto, p.codigo AS codigo, p.nombre AS nombre, vp.cantidad AS cantidad, p.precio AS precio_producto, vp.impuestosproductoventa AS impuesto, vp.total AS subtotal, g.derecho AS derecho_garantia, DATE_ADD( DATE( v.fecha ) , INTERVAL g.duracion DAY ) >= DATE( CURRENT_DATE() )  AS vigencia_garantia
					FROM	app_pos_venta AS v,  app_pos_venta_producto AS vp, app_productos AS p, app_pos_garantia_clasificador AS gc, app_pos_garantia AS g
					WHERE	v.idVenta = vp.idVenta AND vp.idProducto = p.id AND p.linea = gc.id_clasificador AND gc.id_tipo_clasificador = 3 AND gc.id_garantia = g.id AND vp.idVenta = '$idVenta' AND vp.idProducto = '$idProducto'";
				$rs = $this->queryArray($sql);

				if( $rs['total'] == "0") { //familia
					$sql = "SELECT	vp.idventa_producto AS id_venta_producto, p.codigo AS codigo, p.nombre AS nombre, vp.cantidad AS cantidad, p.precio AS precio_producto, vp.impuestosproductoventa AS impuesto, vp.total AS subtotal, g.derecho AS derecho_garantia, DATE_ADD( DATE( v.fecha ) , INTERVAL g.duracion DAY ) >= DATE( CURRENT_DATE() )  AS vigencia_garantia
							FROM	app_pos_venta AS v,  app_pos_venta_producto AS vp, app_productos AS p, app_pos_garantia_clasificador AS gc, app_pos_garantia AS g
							WHERE	v.idVenta = vp.idVenta AND vp.idProducto = p.id AND p.familia = gc.id_clasificador AND gc.id_tipo_clasificador = 2 AND gc.id_garantia = g.id AND vp.idVenta = '$idVenta' AND vp.idProducto = '$idProducto'";
					$rs = $this->queryArray($sql);

					if( $rs['total'] == "0") { //departamento
						$sql = "SELECT	vp.idventa_producto AS id_venta_producto, p.codigo AS codigo, p.nombre AS nombre, vp.cantidad AS cantidad, p.precio AS precio_producto, vp.impuestosproductoventa AS impuesto, vp.total AS subtotal, g.derecho AS derecho_garantia, DATE_ADD( DATE( v.fecha ) , INTERVAL g.duracion DAY ) >= DATE( CURRENT_DATE() )  AS vigencia_garantia
								FROM	app_pos_venta AS v,  app_pos_venta_producto AS vp, app_productos AS p, app_pos_garantia_clasificador AS gc, app_pos_garantia AS g
								WHERE	v.idVenta = vp.idVenta AND vp.idProducto = p.id AND p.departamento = gc.id_clasificador AND gc.id_tipo_clasificador = 1 AND gc.id_garantia = g.id AND vp.idVenta = '$idVenta' AND vp.idProducto = '$idProducto'";
						$rs = $this->queryArray($sql);

					}
				}

			}


			if( $rs['total'] != "0") {

				$clon =  $rs['rows'][0];
				array_push( $res2, $clon );
			}

		}

		/*$response = $res2;
		$response['venta']= $res['rows'][0];
		return $response;*/
		$response = $res;
		$response['venta'] = $res['rows'][0];
		$response['rows']= $res2;
		return $response;
    }

    function buscarAlmacenes() {
    	$sql = "SELECT	id, nombre
				FROM	app_almacenes";
		$res = $this->queryArray($sql);

		return $res['rows'];
    }

    function reclamarGarantia($idVenta, $idAlmacen, $comentario, $tablaVentaProducto ) {
    	foreach ($tablaVentaProducto as $key => $value) {
    		$idVentaProducto = $value['idVentaProducto'];
    		$codigo = $value['codigo'];
    		$nombre = $value['nombre'];
    		$cantidad = $value['cantidad'];
    		$tipoMovimiento = $value['tipoMovimiento'];

    		if( $tipoMovimiento == "1" ) { //cambio
    			$sql = "INSERT INTO	app_pos_garantia_movimientos (id_venta, id_venta_producto, codigo, nombre, cantidad, id_almacen, tipo_movimiento, atendida, comentario, fecha)
						VALUES	('$idVenta', '$idVentaProducto', '$codigo', '$nombre', '$cantidad', '$idAlmacen', '$tipoMovimiento', '1', '$comentario', CURRENT_DATE())";

				$res = $this->queryArray( $sql );
    		}
    		else if ($tipoMovimiento == "2" ) { //reparación
    			$sql = "INSERT INTO	app_pos_garantia_movimientos (id_venta, id_venta_producto, codigo, nombre, cantidad, id_almacen, tipo_movimiento, atendida, comentario, fecha)
						VALUES	('$idVenta', '$idVentaProducto', '$codigo', '$nombre', '$cantidad', '$idAlmacen', '$tipoMovimiento', '0', '$comentario', CURRENT_DATE())";
				$res = $this->queryArray( $sql );
    		}
    		else if ( $tipoMovimiento == "3" ) { //reparación & cambio
    			$sql = "INSERT INTO	app_pos_garantia_movimientos (id_venta, id_venta_producto, codigo, nombre, cantidad, id_almacen, tipo_movimiento, atendida, comentario, fecha)
						VALUES	('$idVenta', '$idVentaProducto', '$codigo', '$nombre', '$cantidad', '$idAlmacen', '$tipoMovimiento', '0', '$comentario', CURRENT_DATE())";
				$res = $this->queryArray( $sql );
    		}


    	}

		return $res;
    }

    function productosEnGarantia( $id ) {
    	$sql = "SELECT	 SUM( gm.cantidad ) AS en_garantia
				FROM	app_pos_garantia_movimientos AS gm
				WHERE	gm.atendida = 0 AND gm.id_venta_producto = '$id'";
		return $this->queryArray( $sql );
    }

    function detalleMovimientoGarantia( $idVentaProducto ) {
    	$sql = "SELECT	*
				FROM	app_pos_garantia_movimientos
				WHERE	id_venta_producto = '$idVentaProducto'
				ORDER BY fecha";
		return $this->queryArray( $sql );
    }

    function atenderMovimientoGarantia( $idVentaProducto ) {
    	$sql = "UPDATE	app_pos_garantia_movimientos
				SET		atendida = '1'
				WHERE	id_venta_producto = '$idVentaProducto'";
		return $this->queryArray( $sql );
    }

    function arqueoCaja( $json ) {
    	$sql = "INSERT INTO	app_pos_arqueo_caja (info_json)
				VALUES	('$json')";
		return $this->queryArray( $sql );
    }

    function obtenerArqueoCaja( $id ){
    	$sql = "SELECT	info_json
				FROM	app_pos_arqueo_caja
				WHERE	id_corte ='$id'";
		$res = $this->queryArray( $sql );
		return $res['rows'][0]['info_json'];
    }

    function productosDevueltos( $id ) {
    	$sql = "SELECT	 SUM( d.cantidad ) AS devueltos
				FROM	app_devolucioncli_datos AS d
				WHERE	d.id_producto = '$id'";
		return $this->queryArray( $sql );
    }

    function detalleMovimientoDevueltos( $id ) {
    	$sql = "select (CASE vp.idProducto
						WHEN 0 THEN
							''
						ELSE p.codigo END) as codigo,
						 (CASE vp.idProducto
						WHEN 0 THEN
							vp.comentario
						ELSE p.nombre END) as nombre, d.cantidad, a.nombre almacen, dc.observaciones comentario, dc.fecha_devolucion fecha
FROM app_pos_venta_producto vp
LEFT JOIN app_productos p ON vp.idProducto = p.id
LEFT JOIN app_devolucioncli_datos d ON d.id_producto = vp.idventa_producto
LEFT JOIN app_almacenes a ON d.id_almacen = a.id
LEFT JOIN app_devolucioncli dc ON dc.id = d.id_devolucion
where vp.idventa_producto = '$id'";
		return $this->queryArray( $sql );
    }

    function devolucion($venta, $almacen, $comentario, $subTotal, $total, $tabla) {


$tipoCambio=1;
			/* Regresar monto por devolución o cancelación */

			$sql = "SELECT	p.idFormapago, p.monto
					FROM	app_pos_venta v
					LEFT JOIN app_pos_venta_pagos p ON v.idVenta = p.idVenta
					WHERE v.idVenta = '$venta'; ";
			$formasPago = $this->queryArray($sql);

			$pagoConCredito = 0;
			$otrasFormasPago = 0;
			foreach ($formasPago['rows'] as $key => $value) {
				if($value['idFormapago'] == 6)
					$pagoConCredito += (float) $value['monto'];
				else
					$otrasFormasPago += (float) $value['monto'];
			}

			$sql = "SELECT	SUM(total) montoDevolucion
					FROM	app_devolucioncli
					WHERE	id_ov = '$venta'; ";
			$totalMontoDevoluciones = $this->queryArray($sql);

			$montoDisponibleDevolucion = $otrasFormasPago - ($totalMontoDevoluciones['rows'][0]['montoDevolucion']);



			if ( $montoDisponibleDevolucion < $total && ($totalMontoDevoluciones['rows'][0]['montoDevolucion'] != 0) && false) {
				//echo 'No es posible devolver sobre el monto de venta a crédito';
				$res2 = ['estatus' => false];
			}
			else {
				$sql = "INSERT INTO	app_devolucioncli (id_ov, id_envio, id_encargado, observaciones, estatus, fecha_devolucion, activo, xmlfile, desc_concepto, subtotal, total, id_consignacion, origen)
						VALUES	('$venta', '$venta', '1', '$comentario', '1', CURRENT_DATE(), '1', '','', '$subTotal', '$total', '0', '2')";
				$res = $this->queryArray($sql);
				$idDevolucion = $res['insertId'];
				if($res['status'] == true ) {
						foreach ($tabla as $key => $value) {
							$idVentaProducto = $value['idVentaProducto'];
							$cantidad = $value['cantidad'];
							$sql = "INSERT INTO	app_devolucioncli_datos (id_ov, id_envio, id_producto, id_devolucion, cantidad, id_lote, id_pedimento, `estatus`, id_almacen, caracteristica)
									VALUES	('$venta', '$venta', '$idVentaProducto', '$idDevolucion', '$cantidad', '0', '0', '1','$almacen', '0')";
							$res2 = $this->queryArray($sql);
						}
					}
					foreach ($tabla as $key => $value) {
						$idVentaProducto = $value['idVentaProducto'];
						$cantidad = $value['cantidad'];
						$idEmpleado = $_SESSION['accelog_idempleado'];
						$sql = "SELECT pr.idProducto, pr.preciounitario, pr.caracteristicas, pa.idFormapago
								FROM	app_pos_venta_producto pr
								LEFT JOIN app_pos_venta_pagos pa ON pr.idVenta = pa.idVenta
								WHERE	idventa_producto=$idVentaProducto";
						$rstmp = $this->queryArray($sql);
						$idProducto = $rstmp['rows'][0]['idProducto'];
						$caracteristicas = $rstmp['rows'][0]['caracteristicas'];
						if($caracteristicas!=''){

							$caracteristica = preg_replace('/\*/', ',', $caracteristicas);
							$caracteristicareplace = preg_replace('/([0-9])+/', '\'\0\'', $caracteristica);
							$caracteristicareplace=addslashes($caracteristicareplace);
							$caracteristicareplace = trim($caracteristicareplace, ',');
						}else{
							$caracteristicareplace = '\'0\'';
							$caracteristicareplace=addslashes($caracteristicareplace);
						}
						$costo = $rstmp['rows'][0]['preciounitario'];
						$importe = $costo * $cantidad;

						$value['seriesLotes'] = json_decode( $value['seriesLotes'] );
						if( count($value['seriesLotes']) == 0 ){

	                        /*$idtipocosto = $this->tipoCosteoProd($idProducto);
								if($idtipocosto==1){
		                            $elunit = $this->costeoProd($idProducto);
		                        }else if($idtipocosto==6){
		                            $elunit = $this->costeoProd($idProducto);
		                        }else{
		                            $elunit = $this->costeoProd($idProducto);
		                        }*/
		                        $sql = "SELECT costo 
										FROM app_inventario_movimientos im 
										WHERE im.id_lote = '0' AND im.id_producto = \"{$idProducto}\" AND im.id_producto_caracteristica = \"{$caracteristicareplace}\" AND
										SUBSTRING_INDEX(im.referencia,' ',1) = 'Venta' AND SUBSTRING_INDEX( SUBSTRING_INDEX(im.referencia,' ',4) , ' ', -1) = '$venta';";
		                        $elunit = $this->queryArray($sql);
		                        //$elcost = ($elunit*1)*($cantidad*1);
		                        $elcost = ($elunit['rows'][0]['costo'] *1);

							$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus, origen, id_poliza_mov)
									VALUES ('$idProducto', '$caracteristicareplace', '0', '0', '$cantidad', '$importe', 0, '$almacen', NOW(), $idEmpleado, '1', '$elcost', 'Devolución de venta $venta ($comentario)', '1', '2', '0');";

							$res2 = $this->queryArray($sql);

/**********  PARCHE TEMPORAL Kits en inventario ****************
date_default_timezone_set("Mexico/General");
$fechaactual = date("Y-m-d H:i:s");

				$sqlkitProducto = "SELECT	idProducto
						FROM	app_pos_venta_producto
						WHERE	idventa_producto= '{$value['idVentaProducto']}';";
				$idProductokit = $this->queryArray($sqlkitProducto);

				$idtipoproducto = $this->tipoProducto($idProductokit['rows'][0]['idProducto']);

		        if( $idtipoproducto == 6 ) {
		        	$sqlkit = "SELECT	*
							FROM	app_inventario_movimientos
							WHERE	referencia LIKE 'Venta $venta - kit{$idProductokit['rows'][0]['idProducto']}'";
					$productosDeKit = $this->queryArray($sqlkit);
					foreach ($productosDeKit['rows'] as $k => $v) {
						$sqlkitinfo = "SELECT	*
										FROM	com_kitsXproductos k
										WHERE	k.id_kit = '{$idProductokit['rows'][0]['idProducto']}' AND k.id_producto = '{$v['id_producto']}';";
						$productosDeKitInfo = $this->queryArray($sqlkitinfo);

						$v['id_producto_caracteristica'] = addslashes($v['id_producto_caracteristica']);
						$sqlkitInsert = "INSERT into app_inventario_movimientos(id_producto_caracteristica, id_producto,cantidad,importe,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values('{$v['id_producto_caracteristica']}','{$v['id_producto']}','".($value['cantidad'] - $devoluciones['rows'][$key]['cantidad'])*($productosDeKitInfo['rows'][0]['cantidad'])."','{$v['importe']}','$almacen','$fechaactual','{$_SESSION['accelog_idempleado']}','1','{$v['costo']}','Devolución Venta $venta - kit{$idProductokit['rows'][0]['idProducto']} ($comentario)','2')";

						$this->queryArray($sqlkitInsert);

					}

		        }
/**********  PARCHE TEMPORAL Kits en inventario ****************/


						}
						else {
										date_default_timezone_set("Mexico/General");
										$fechaactual = date("Y-m-d H:i:s");

										$sqlkitProducto = "SELECT	idProducto
												FROM	app_pos_venta_producto
												WHERE	idventa_producto= '{$value['idVentaProducto']}';";
										$idProductokit = $this->queryArray($sqlkitProducto);

										$idtipoproducto = $this->tipoProducto($idProductokit['rows'][0]['idProducto']);

								        if( $idtipoproducto == 6 ) {
								        	/*$sqlkit = "SELECT	*
													FROM	app_inventario_movimientos
													WHERE	referencia LIKE 'Venta $venta - kit{$idProductokit['rows'][0]['idProducto']}'";
														$productosDeKit = $this->queryArray($sqlkit);
														foreach ($productosDeKit['rows'] as $k => $v) {
															$sqlkitinfo = "SELECT	*
																			FROM	com_kitsXproductos k
																			WHERE	k.id_kit = '{$idProductokit['rows'][0]['idProducto']}' AND k.id_producto = '{$v['id_producto']}';";
															$productosDeKitInfo = $this->queryArray($sqlkitinfo);

															$v['id_producto_caracteristica'] = addslashes($v['id_producto_caracteristica']);
															$sqlkitInsert = "INSERT into app_inventario_movimientos(id_producto_caracteristica, id_producto,cantidad,importe,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) values('{$v['id_producto_caracteristica']}','{$v['id_producto']}','".($value['cantidad'] - $devoluciones['rows'][$key]['cantidad'])*($productosDeKitInfo['rows'][0]['cantidad'])."','{$v['importe']}','$almacen','$fechaactual','{$_SESSION['accelog_idempleado']}','1','{$v['costo']}','Devolución Venta $venta - kit{$idProductokit['rows'][0]['idProducto']} ($comentario)','2')";

															$this->queryArray($sqlkitInsert);

														}*/














														$jsonKitsTmp = (  str_replace( "'", "\\'", json_encode($value['seriesLotes']) )  );
														$sql = "UPDATE	app_pos_venta_producto
																		SET	json_kits = '$jsonKitsTmp'
																		WHERE	idProducto = '$idProducto' AND idVenta = '$venta'";
														$this->queryArray($sql);

														$normales = [];
														$caracteristicas = [];
											      $series = [];
											      $lotes = [];
											      // $strError = "";

											      // foreach ($nar as $idProducto => $producto) {

											         // foreach ($value['seriesLotes'] as $campoProducto => $infoProducto) {

											           // if( $campoProducto == 'kits' ) {


																 $value['seriesLotes'] = $this->object_to_array($value['seriesLotes']);

											            foreach ($value['seriesLotes'] as $nKit => $kit) {

																	if( $kit['done'] == 'false' ) {
											              foreach ($kit['items'] as $nItem => $item) {

																			if( $item['car'] == '0' && $item['ser'] == '0' && $item['lot'] == '0' ) {
																				$pos = array_search( $item['idProduct'], array_column($normales, 'idProduct') );
																				if( ( $pos === false ) )
																					array_push($normales , $item);
																				else
																					$normales[$pos]['quantity'] += $item['quantity'];
																			}

											                foreach ($item['characteristics'] as $nCharacteristic => $characteristic) {
																				$characteristic['idProduct'] = $item['idProduct'];
											                  if( $characteristic['quantity'] != "0" ) {
											                    $pos = array_search( $characteristic['id'], array_column($caracteristicas, 'id') );
											                    if( ( $pos === false ) )
											                      array_push($caracteristicas , $characteristic);
											                    else
											                      $caracteristicas[$pos]['quantity'] += $characteristic['quantity'];
											                  }

											                }

											                foreach ($item['series'] as $nSerie => $serie) {
																				$pos = array_search( $item['idProduct'], array_column($series, 'idProduct') );
																				if( ( $pos === false ) ) {
																					array_push($series , [ 'idProduct' => $item['idProduct'], 'series' => [ $serie['id'] ] ] );
																				}
																				else {
																					if( !( in_array($serie['id'], $series[$pos]['series']) ) )
												                    array_push( $series[$pos]['series'], $serie['id'] );
												                  else {
												                    $pos = array_search( $serie['id'], array_column($item['optionsSeries'], 'id') );
												                    $strError .= "La serie {$item['optionsSeries'][$pos]['text']} esta repetida\n";
												                  }
																				}
																				// $serieAux['idProduct'] = $item['idProduct'];
																				// $serieAux['id'] = $serie;



											                  // if( !( in_array($serie, $series) ) )
											                  //   array_push($series , $serieAux);
											                  // else {
											                  //   $pos = array_search( $serie['id'], array_column($item['optionsSeries'], 'id') );
											                  //   $strError .= "La serie {$item['optionsSeries'][$pos]['text']} esta repetida\n";
											                  // }

											                }

											                foreach ($item['batches'] as $nLote => $lote) {
																				$lote['idProduct'] = $item['idProduct'];
											                  if( $lote['quantity'] != "0" ) {
											                    $pos = array_search( $lote['id'], array_column($lotes, 'id') );
											                    if( ( $pos === false ) )
											                      array_push($lotes , $lote);
											                    else
											                      $lotes[$pos]['quantity'] += $lote['quantity'];
											                  }

											                }

											              }

																	}

											            }
											            // }
											         // }
														// }


														/*$sql = "SELECT costo FROM app_inventario_movimientos im WHERE SUBSTRING_INDEX(im.referencia,' ',1) = 'Venta' AND SUBSTRING_INDEX( SUBSTRING_INDEX(im.referencia,' ',4) , ' ', -1) = '$venta';";
		                        $elunit = $this->queryArray($sql);
		                        //$elcost = ($elunit*1)*($cantidad*1);
		                        $elcost = ($elunit['rows'][0]['costo'] *1);*/

														$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus, origen, id_poliza_mov)
																VALUES ('$idProducto', '$caracteristicareplace', '0', '0', '$cantidad', '$importe', 0, '$almacen', NOW(), $idEmpleado, '1', 0, 'Devolución de venta $venta - kit ($comentario)', '1', '2', '0');";

														$res2 = $this->queryArray($sql);

														foreach ($normales as $keyN => $valueN) {
															$selPrice = "SELECT precio from app_productos where id=".$valueN['idProduct'];
															$resPrice = $this->queryArray($selPrice);
															$im = $value['cantidad'] * $v['cantidad'];
//var_dump($value);var_dump($valueN);
															$idtipocosto = $this->tipoCosteoProd($valueN['idProduct']);
															if($idtipocosto==1){
									                $elunit = $this->costeoProd($valueN['idProduct']);
									            }else if($idtipocosto==3){
									                $elunit = $this->costeoUltimoCosto($valueN['idProduct']);
									            }else{
									                $elunit = $this->costeoProd($valueN['idProduct']);
									            }//var_dump($valueN['idProduct']);var_dump($elcost);
									            $elcost = ($elunit*1);
									            $elcost = $elcost * $tipoCambio;

															$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
																			VALUES
																				( {$valueN['idProduct']}, \"\'0\'\", 0, 0, {$valueN['quantity']}, 0, 0, $almacen, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 1, $elcost, 'Devolución Venta $venta - kit$idProducto ($comentario)');";
															$this->queryArray($sql);
														}

														foreach ($caracteristicas as $keyC => $valueC) {
															$selPrice = "SELECT precio from app_productos where id=".$valueC['idProduct'];
															$resPrice = $this->queryArray($selPrice);
															$im = $producto->cantidad * $v['cantidad'];

															$idtipocosto = $this->tipoCosteoProd($valueC['idProduct']);
															if($idtipocosto==1){
									                $elunit = $this->costeoProd($valueC['idProduct']);
									            }else if($idtipocosto==3){
									                $elunit = $this->costeoUltimoCosto($valueC['idProduct']);
									            }else{
									                $elunit = $this->costeoProd($valueC['idProduct']);
									            }
									            $elcost = ($elunit*1);
									            $elcost = $elcost * $tipoCambio;

															$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
																			VALUES
																				( {$valueC['idProduct']}, \"{$valueC['id']}\", 0, 0, {$valueC['quantity']}, 0, 0, $almacen, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 1, $elcost, 'Devolución Venta $venta - kit$idProducto ($comentario)');";
															$this->queryArray($sql);
														}
														foreach ($series as $keyS => $valueS) {
															$selPrice = "SELECT precio from app_productos where id=".$valueS['idProduct'];
															$resPrice = $this->queryArray($selPrice);
															$im = $producto->cantidad * $v['cantidad'];

															$idtipocosto = $this->tipoCosteoProd($valueS['idProduct']);
															if($idtipocosto==1){
																	$elunit = $this->costeoProd($valueS['idProduct']);
															}else if($idtipocosto==3){
																	$elunit = $this->costeoUltimoCosto($valueS['idProduct']);
															}else{
																	$elunit = $this->costeoProd($valueS['idProduct']);
															}
															$elcost = ($elunit*1);
															$elcost = $elcost * $tipoCambio;
															$len = count($valueS['series']);
															$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
																			VALUES
																				( {$valueS['idProduct']}, \"\'0\'\", 0, '0', $len, 0, 0, $almacen, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 1, $elcost, 'Devolución Venta $venta - kit$idProducto ($comentario)');";
															$idMov = $this->queryArray($sql);

															foreach ($valueS['series'] as $k => $v) {
																$sql = "INSERT INTO app_producto_serie_rastro (id_serie, id_almacen, fecha_reg, id_mov)
																				VALUES
																					($v, $almacen, '$fechaactual', {$idMov['insertId']});";
																$this->queryArray($sql);
																$sql = "UPDATE app_producto_serie
																				SET id_venta = '$venta', estatus = '1'
																				WHERE	id = '$v'";
																$this->queryArray($sql);
															}
														}
														foreach ($lotes as $keyL => $valueL) {
															$selPrice = "SELECT precio from app_productos where id=".$valueL['idProduct'];
															$resPrice = $this->queryArray($selPrice);
															$im = $producto->cantidad * $v['cantidad'];

															$idtipocosto = $this->tipoCosteoProd($valueL['idProduct']);
															if($idtipocosto==1){
									                $elunit = $this->costeoProd($valueL['idProduct']);
									            }else if($idtipocosto==3){
									                $elunit = $this->costeoUltimoCosto($valueL['idProduct']);
									            }else{
									                $elunit = $this->costeoProd($valueL['idProduct']);
									            }
									            $elcost = ($elunit*1);
									            $elcost = $elcost * $tipoCambio;

															$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia)
																			VALUES
																				( {$valueL['idProduct']}, \"\'0\'\", 0, '{$valueL['id']}', {$valueL['quantity']}, 0, 0, $almacen, '$fechaactual', '{$_SESSION['accelog_idempleado']}', 1, $elcost, 'Devolución Venta $venta - kit$idProducto ($comentario)');";
															$this->queryArray($sql);
														}
														// var_dump($caracteristicas);
														// var_dump($series);
														// var_dump($lotes);














								        }
												else {
							foreach ($value['seriesLotes'] as $ke => $val) {

								if( is_string($val) ) { //serie
									$val = explode( "_", $val );
								 	$sql = "UPDATE app_producto_serie
											SET id_venta = '0', estatus = '0', id_almacen = '{$almacen}'
											WHERE id = '{$val[1]}';";
									$this->queryArray($sql);

									

									/*$idtipocosto = $this->tipoCosteoProd($idProducto);
									if($idtipocosto==1){
			                            $elunit = $this->costeoProd($idProducto);
			                        }else if($idtipocosto==6){
			                            $elunit = $this->costeoProd($idProducto);
			                        }else{
			                            $elunit = $this->costeoProd($idProducto);
			                        }*/
			                        $sql = "SELECT costo 
											FROM app_inventario_movimientos im 
											WHERE im.id_lote = '0' AND im.id_producto = \"{$idProducto}\" AND im.id_producto_caracteristica = \"{$caracteristicareplace}\" AND
											SUBSTRING_INDEX(im.referencia,' ',1) = 'Venta' AND SUBSTRING_INDEX( SUBSTRING_INDEX(im.referencia,' ',4) , ' ', -1) = '$venta';";
			                        $elunit = $this->queryArray($sql);
			                        //$elcost = ($elunit*1)*($cantidad*1);
			                        $elcost = ($elunit['rows'][0]['costo'] *1);

										$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus, origen, id_poliza_mov)
												VALUES ('$idProducto', '$caracteristicareplace', '0', '0', '$cantidad', '$importe', 0, '$almacen', NOW(), $idEmpleado, '1', '$elcost', 'Devolución de venta $venta ($comentario)', '1', '2', '0');";
										$res2 = $this->queryArray($sql);

										$sql = "INSERT INTO app_producto_serie_rastro (id_serie, id_almacen, fecha_reg, id_mov)
											VALUES ('{$val[1]}', '{$almacen}', NOW(), '{$res2['insertId']}');";
									$this->queryArray($sql);
								}
								else { //lote
									$strLotmp = '';
									foreach ($val as $k => $v) {
										$lotes = explode( "_", $k );
										$strLotmp .= ','.$lotes[1].'-'. $v;

										/*$idtipocosto = $this->tipoCosteoProd($idProducto);
										if($idtipocosto==1){
				                            $elunit = $this->costeoProd($idProducto);
				                        }else if($idtipocosto==6){
				                            $elunit = $this->costeoProd($idProducto);
				                        }else{
				                            $elunit = $this->costeoProd($idProducto);
				                        }*/
				                        $sql = "SELECT costo 
												FROM app_inventario_movimientos im 
												WHERE im.id_lote = '{$lotes[1]}' AND im.id_producto = \"{$idProducto}\" AND im.id_producto_caracteristica = \"{$caracteristicareplace}\" AND
													SUBSTRING_INDEX(im.referencia,' ',1) = 'Venta' AND SUBSTRING_INDEX( SUBSTRING_INDEX(im.referencia,' ',4) , ' ', -1) = '$venta';";
				                        $elunit = $this->queryArray($sql);
				                        //$elcost = ($elunit*1)*($cantidad*1);
				                        $elcost = ($elunit['rows'][0]['costo'] *1);
										$sql = "INSERT INTO app_inventario_movimientos (id_producto, id_producto_caracteristica, id_pedimento, id_lote, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha, id_empleado, tipo_traspaso, costo, referencia, estatus, origen, id_poliza_mov)
										VALUES ('$idProducto', '$caracteristicareplace', '0', '{$lotes[1]}', '$v', '$importe', 0, '$almacen', NOW(), $idEmpleado, '1', '$elcost', 'Devolución de venta $venta ($comentario)', '1', '2', '0');";
										$res2 = $this->queryArray($sql);
									}
									$sql = "SELECT	lotes, lotesDevueltos
											FROM	app_pos_venta_producto vp
											WHERE	vp.idventa_producto='$idVentaProducto'";
									$resultLote = $this->queryArray( $sql );

									$loteCantidad = explode('-', $value);
									$strLotes = "";
									if( $resultLote['rows'][0]['lotesDevueltos'] ) {
										$resultLote['rows'][0]['lotesDevueltos'] .= $strLotmp;
										$lotesDevueltos = explode(',', $resultLote['rows'][0]['lotesDevueltos'] );

										foreach ($lotesDevueltos as $kk => $vv) {
											$loteDevueltoCantidad = explode('-', $vv);

											$cantidadLotes = 0;
											$idLotmp = $loteDevueltoCantidad[0];
											foreach ($lotesDevueltos as $kkk => $vvv) {
												$loteDevueltoCantidad2 = explode('-', $vvv);
												if( $loteDevueltoCantidad[0] == $loteDevueltoCantidad2[0] ){
													$cantidadLotes += (float) $loteDevueltoCantidad2[1];
													unset($lotesDevueltos[$kkk]);
												}
											}
											if($cantidadLotes != 0)
												$strLotes .= $idLotmp.'-'.$cantidadLotes.',';
										}


									}
									else {
										$strLotes = substr( $strLotmp , 1).",";

									}
									$strLotes = (count( $strLotes) != 0) ? substr( $strLotes, 0, -1 ) : "";
									$sql = "UPDATE app_pos_venta_producto
												SET lotesDevueltos = '$strLotes'
												WHERE idventa_producto = '$idVentaProducto';";
										$this->queryArray( $sql );
								}
							}
												}
					}
				//Retiro de caja en efectivo

				$sql = "SELECT	activar_retiro_dev_can
						FROM	app_config_ventas;";
				$res = $this->queryArray($sql);
				if ($res['rows'][0]['activar_retiro_dev_can']){
					$concepto = "Retiro por devolución de venta $venta ($comentario)";
					$this->agregaretiro($total,$concepto);
				}

				}

			}
		return $res2;
    }
    public function agregaretiro($cantidad,$concepto){
            session_start();
            $selSuc = 'SELECT idSuc from administracion_usuarios where idempleado='.$_SESSION['accelog_idempleado'];
            $res = $this->queryArray($selSuc);

            date_default_timezone_set("Mexico/General");
            $fechaactual = date("Y-m-d H:i:s");
            $_SESSION['sucursal'] = $res['rows'][0]['idSuc'];
            $insertRetiro = "INSERT into app_pos_retiro_caja(cantidad,concepto,idempleado,fecha,idSucursal) values('" . $cantidad . "','" . $concepto . "'," . $_SESSION['accelog_idempleado'] . ",'".$fechaactual."','".$_SESSION["sucursal"]."')";

            $resultInsert = $this->queryArray($insertRetiro);
            $idInsert = $resultInsert['insertId'];
            return array("status" => true, "type" => 2, 'id' => $idInsert);
        }


    public function buscarClasificadores( $clasificador, $patron, $parentClasific) {

		switch ($clasificador) {
			case '1':
				$tabla = 'app_departamento';
				$filtro = "";
				break;
			case '2':
				$tabla = 'app_familia';
				$filtro = "AND id_departamento='$parentClasific'";
				break;
			case '3':
				$tabla = 'app_linea';
				$filtro = "AND id_familia='$parentClasific'";
				break;
			default:
				# code...
				break;
		}
		if($parentClasific == "")
			$filtro = "";

		$sql = "SELECT	id, nombre as text
				FROM	$tabla
				WHERE	nombre LIKE '%$patron%' $filtro";

		$res = $this->queryArray($sql);

		return json_encode( $res );
	}

	/*
	public function save_gs1($codigo,$result,$nombre,$precio,$desc,$instancia){

		$tipo_producto = 1;
		$comision = 0;
		$id_unidad_venta = 1;
		$status = 1;
		$id_unidad_compra = 1;

		$servername = "nmdb.cyv2immv1rf9.us-west-2.rds.amazonaws.com";
		$username 	= "nmdevel";
		$password 	= "nmdevel";
		$dbname 	= "netwarstore";

		date_default_timezone_set("Mexico/General");
       	$fecha=date('Y-m-d H:i:s');

		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}

		$sql = "INSERT INTO gs_productos (codigo, resultado, instancia, fecha)
		VALUES ('$codigo', '$result', '$instancia', '$fecha')";

		if ($conn->query($sql) === TRUE) {
		    //echo "New record created successfully";
		    $gs = 1;
		} else {
		    //echo "Error: " . $sql . "<br>" . $conn->error;
		    $gs = 0;
		}

		$conn->close();

		if($result == 1){
			$query = "INSERT INTO app_productos (codigo,nombre,precio,descripcion_corta,tipo_producto,comision,id_unidad_venta,status,id_unidad_compra)
			 VALUES ('$codigo','$nombre','$precio','$desc','$tipo_producto','$comision','$id_unidad_venta','status','id_unidad_compra');";
			$last_id = $this->insert_id($query);
		}
        return array('gs' => $gs , 'producto' => $last_id);

	}
	*/

	function obtenerSucursal($idSuc){
		 $sql = "SELECT  nombre
                FROM    mrp_sucursal
                WHERE   idSuc='$idSuc' ";

        return $this->queryArray($sql);
	}
	function obtenerEmpleado($idVend){

        $sql = "SELECT idEmpleado idempleado, CONCAT( CONCAT ( CONCAT( CONCAT(nombre, ' ') , apellidos), ' | ') , nombreusuario) nombre
				FROM administracion_usuarios
				WHERE idadmin ='$idVend';";

        return $this->queryArray($sql);
	}
	function obtenerProducto($idProd){
		 $sql = "SELECT  nombre
                FROM    app_productos
                WHERE   id='$idProd' ";

        return $this->queryArray($sql);
	}



    function guardaMerma($venta, $almacen, $comentario, $subTotal, $total, $tabla) {
$almacen = $this->obtenAlm();
    	$this->devolucion($venta, $almacen, "Merma: ".$comentario, $subTotal, $total, $tabla);
    	$tipoTraspaso = 0;
        $contadorProductos = 0;
        $total = 0;

        date_default_timezone_set("Mexico/General");
        $fechaactual = date('Y-m-d H:i:s');

        $insert1 = "INSERT into app_merma(fecha,usuario,productos,importe) values ('".$fechaactual."','".$_SESSION['accelog_idempleado']."','0','0')";
        $result1 = $this->queryArray($insert1);
        $idMerma = $result1['insertId'];

        if($result1['status'] == true){
			foreach ($tabla as $key => $value) {

				$sql = "SELECT	idProducto, caracteristicas
						FROM	app_pos_venta_producto
						WHERE	idventa_producto = '{$value['idVentaProducto']}';";
				$productosTmp = $this->queryArray($sql);

				/*$sql = "SELECT	s.idAlmacen
						FROM	administracion_usuarios u
						INNER JOIN mrp_sucursal s ON u.idSuc = s.idSuc
						WHERE	u.idempleado = '{$_SESSION['accelog_idempleado']}';";
				$almacenTmp = $this->queryArray($sql);*/

				$productosTmp['rows'][0]['caracteristicas'] = ($productosTmp['rows'][0]['caracteristicas'] == '' || $productosTmp['rows'][0]['caracteristicas'] == 0) ? "\'0\'" : preg_replace("/\d+/", "'\${0}'", $productosTmp['rows'][0]['caracteristicas'] );

				$queryInserProd = "INSERT INTO app_merma_datos (id_merma,id_producto,cantidad,precio,usuario,almacen,observaciones,caracteristicas)
								VALUES ('{$idMerma}','{$productosTmp['rows'][0]['idProducto']}','{$value['cantidad']}','{$value['precio']}','{$_SESSION['accelog_idempleado']}','{$almacen}','Merma venta {$venta} ({$comentario})','{$productosTmp['rows'][0]['caracteristicas']}')";

                $insertaproducto = $this->queryArray($queryInserProd);


date_default_timezone_set("Mexico/General");
$fechaactual = date("Y-m-d H:i:s");
$idtipocosto = $this->tipoCosteoProd($productosTmp['rows'][0]['idProducto']);
if($idtipocosto==1){
    $elunit = $this->costeoProd($productosTmp['rows'][0]['idProducto']);
}else if($idtipocosto==3){
    $elunit = $this->costeoUltimoCosto($productosTmp['rows'][0]['idProducto']);
}else{
    $elunit = $this->costeoProd($productosTmp['rows'][0]['idProducto']);
}
//$elcost = ($elunit*1)*($producto->cantidad*1);
$tipoCambio=1;
$elcost = ($elunit*1);
$elcost = $elcost * $tipoCambio;
$insertMovi = "INSERT into app_inventario_movimientos
					(id_producto, id_producto_caracteristica, cantidad, importe, id_almacen_origen, id_almacen_destino, fecha,id_empleado,
						tipo_traspaso,costo,referencia)
				VALUES ('".$productosTmp['rows'][0]['idProducto']."', '".$productosTmp['rows'][0]['caracteristicas']."', '".$value['cantidad']."', '".$value['precio']."','".$almacen."', '0',
						'".$fechaactual."', '".$_SESSION['accelog_idempleado']."', '0', '".$elcost."',
						'Merma ".$idMerma."')";

$resInsert = $this->queryArray($insertMovi);

                $contadorProductos++;
                $total += ($value['precio'] * $value['cantidad']) ;
			}
			$concepto = "Retiro por devolución a merma de venta $venta ($comentario)";
			$this->agregaretiro($total,$concepto);
		}

        $updateCon = "UPDATE app_merma set productos=".$contadorProductos.", importe=".$total." where id=".$idMerma;
        $resUpdate = $this->queryArray($updateCon);

        return array('estatus' => true , 'idMerma' => $idMerma);

        /**/



    }

    function change_status($objeto){
    	if ($objeto['status'] == 'true') {
    		$sta = 1;
    	} else {
    		$sta = 0;
    	}
		$sql = "UPDATE
					forma_pago
				SET
					activo='".$sta."'
				WHERE
					idFormapago=".$objeto['id'];
		// return $sql;
		$result = $this->query($sql);
		return $result;
    }

    function guardar_tipo_pago($objeto){
		$sql = "INSERT into forma_pago(nombre,claveSat,tipo) values ('".$objeto['nombre']."','".$objeto['clave']."','2')";
		// return $sql;
		$result = $this->insert_id($sql);
		return $result;
    }

    function editar_tipo_pago($objeto){
    	$sql = "UPDATE
					forma_pago
				SET
					nombre='".$objeto['nombre']."',
					claveSat='".$objeto['clave']."'
				WHERE
					idFormapago=".$objeto['id'];
		// return $sql;
		$result = $this->query($sql);
		return $result;
    }

    function delete_tipo_pago($objeto){
    	$sql = "DELETE from forma_pago where idFormapago =".$objeto['id'];
		// return $sql;
		$result = $this->query($sql);
		return $result;
    }

    function obtenerSeriesYLotes($idVentaProducto){
    	$sql = "SELECT	lotes, lotesDevueltos
				FROM	app_pos_venta_producto vp
				WHERE	vp.idventa_producto='$idVentaProducto'";
		$resultLote = $this->queryArray( $sql );

		if( $resultLote['rows'][0]['lotes'] ) {
			$lotes = explode(',', substr($resultLote['rows'][0]['lotes'] , 0, -3 ) );


				foreach ($lotes as $key => $value) {
					$loteCantidad = explode('-', $value);
					if( $resultLote['rows'][0]['lotesDevueltos'] ) {
						$lotesDevueltos = explode(',', $resultLote['rows'][0]['lotesDevueltos'] );
						foreach ($lotesDevueltos as $k => $val) {
							$loteDevueltoCantidad = explode('-', $val);
							if( $loteDevueltoCantidad[0] == $loteCantidad[0] ){
								$loteCantidad[1] = (float) $loteCantidad[1] - (float) $loteDevueltoCantidad[1];
							}
						}
					}
					$sql = "SELECT	no_lote
							FROM	app_producto_lotes
							WHERE	id = '{$loteCantidad[0]}';";
					$no_lote = $this->queryArray( $sql );
					array_push($loteCantidad,  $no_lote['rows'][0]['no_lote']);
					$lotes[$key] = $loteCantidad;
				}

		}

		$sql = "SELECT	s.id, s.serie
				FROM	app_pos_venta_producto vp
				INNER JOIN app_producto_serie s ON vp.idProducto = s.id_producto AND vp.idVenta = s.id_venta
				WHERE	vp.idventa_producto='$idVentaProducto';";
		$resultSerie = $this->queryArray( $sql );

		$sql = "SELECT 	json_kits
						FROM	app_pos_venta_producto
						WHERE	idventa_producto = '$idVentaProducto'";
		$resultKit = $this->queryArray( $sql );

		return array('series' => $resultSerie['rows'] , 'lotes' => $lotes , 'kits' => json_decode( $resultKit['rows'][0]['json_kits'] )  );
    }
    function verMiUsuario() {
    	$sql = "SELECT  idadmin
                FROM    administracion_usuarios
                WHERE   idempleado = '{$_SESSION['accelog_idempleado']}';";
//echo $sql;
        $result = $this->queryArray( $sql );
//var_dump($result['rows'][0]['idadmin']);
//die();
        return  $result['rows'][0]['idadmin'];
    }

    /* ==== MOD CHRIS - tipo de documento === */
    function saveTipoDocumento($td){
    	$sql = "UPDATE
					app_config_ventas
				SET
					tipo_documento='$td'
				WHERE
					id=1";
		// return $sql;
		$result = $this->query($sql);
		echo $result;
    }
    /* ==== FIN MOD === */
    public function buscarFacturasCliente___($cliente,$desde,$hasta,$tipo){
		$filtro = 'where 1 = 1';
		$inicio = $desde;
		$fin = $hasta;


		if($fin!="")
		{
			list($a,$m,$d)=explode("-",$fin);
			$fin=$a."-".$m."-".((int)$d+1);
		}


		if($inicio!="" && $fin=="")
		{
			$filtro.=" and  fecha >= '".$inicio."' ";
		}
		if($fin!="" && $inicio=="")
		{
			$filtro.=" and  fecha <= '".$fin."' ";
		}
		if($inicio!="" && $fin!="")
		{
			$filtro.=" and  fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
		}

		if($tipo == 1){
			//todas
			$filtro.="";
		}
		if($tipo == 2){
			//factura
			$filtro.=" and  tipoComp = 'F' ";
		}
		if($tipo == 3){
			//notas
			$filtro.=" and  tipoComp = 'C' ";
		}
		if($tipo == 4){
			//honorarios
			$filtro.=" and  tipoComp = 'H' ";
		}

		$select = "SELECT a.*, d.nombre as nori1, dd.nombre as nori2  from app_respuestaFacturacion a
left join app_envios b on b.id=a.idSale
left join app_oventa c on c.id=b.id_oventa
left join comun_cliente d on d.id=c.id_cliente and d.id='$cliente'

left join app_pos_venta bb on bb.idVenta=a.idSale
left join comun_cliente dd on dd.id=bb.idCliente and dd.id='$cliente'
 where 1=1 AND idSale!=0  group by a.id;";
 //echo $select;
		$resSel = $this->queryArray($select);


		foreach ($resSel['rows'] as $key => $value) {
			if($value['origen']==1 && $value['nori1']==null){
				unset($resSel['rows'][$key]);
				continue;
			}
			if($value['origen']==2 && $value['nori2']==null){
				unset($resSel['rows'][$key]);
				continue;
			}
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}

		return $resSel['rows'];
	}
	 public function buscarFacturasCliente($cliente,$desde,$hasta,$tipo){
	 	$redr = 'SELECT nombre,rfc from comun_cliente where id='.$cliente;
	 	$resss = $this->queryArray($redr);


	 	$resSel = 'SELECT r.* from app_respuestaFacturacion r
				left join cont_facturas cf on cf.uuid COLLATE utf8_unicode_ci =r.folio
                where cf.rfc="'.$resss['rows'][0]['rfc'].'";';
                //echo $resSel;
	 	$resSel = $this->queryArray($resSel);
	 	//var_dump($resSel);
	 	//print_r($resSel['rows']);
	 		foreach ($resSel['rows'] as $key => $value) {
			/*if($value['origen']==1 && $value['nori1']==null){
				unset($resSel['rows'][$key]);
				continue;
			}
			if($value['origen']==2 && $value['nori2']==null){
				unset($resSel['rows'][$key]);
				continue;
			} */
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}

		return $resSel['rows'];


	 }
	public function relacion_facturas_pagos($uuid)
	{
		$myQuery = "
			SELECT
			p.numpol,
			p.fecha,
			IFNULL(m.Importe,0) AS SumImportes,
			p.concepto

			FROM cont_movimientos m

			INNER JOIN cont_polizas p ON p.id = m.IdPoliza

			WHERE p.activo = 1
			AND m.Activo = 1
			AND m.Factura LIKE '%$uuid%'
			AND m.TipoMovto = 'Abono'
			AND p.idtipopoliza = 1;
		";
		$Result = $this->query($myQuery);
		return $Result;
		//return $myQuery;
	}
	public function creaCxC($idVenta){
		$idSale=$idVenta;
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
				//// Crea cuenta por cobrar si es a credito
		$selFp = "SELECT * from app_pos_venta_pagos where idVenta=".$idSale;
		$resFp = $this->queryArray($selFp);
		$pagoCredito = 0;
		$referencia = '';
		foreach ($resFp['rows'] as $key => $value) {
			if($value['idFormapago'] == 6){
				$pagoCredito += (float) $value['monto'];
				$referencia .= $value['referencia'].' ';
			}

		}

		if($pagoCredito > 0){
			$selcli = "SELECT idCliente,moneda,tipo_cambio from app_pos_venta where idVenta=".$idSale;
			$resCli = $this->queryArray($selcli);
			$cliente = $resCli['rows'][0]['idCliente'];
			$moneda = $resCli['rows'][0]['moneda'];
			$tipoCambio = $resCli['rows'][0]['tipo_cambio'];
			if($moneda == 0){
				$moneda =1;
			}
			if($tipoCambio == 0){
				$tipoCambio = 1;
			}


			$query = "INSERT INTO app_pagos(cobrar_pagar,id_prov_cli,cargo,fecha_pago,concepto,id_forma_pago,id_moneda,tipo_cambio) values('0','".$cliente."','".str_replace(",", "", $pagoCredito)."','".$fechaactual."','Ticket Caja ".$idSale.":".$referencia."','6','".$moneda."','".$tipoCambio."')";
							//echo $query;
			$resCargo = $this->queryArray($query);

		}

	}
	public function tipodecambio($moneda){
		$selv = "SELECT * from cont_tipo_cambio order by id DESC limit 1";
		$re = $this->queryArray($selv);

		return array('cambio' => $re['rows'][0]['tipo_cambio']);
	}
	public function seriesCfdi(){
		$select = "SELECT * from pvt_serie_folio";
		$res = $this->queryArray($select);
		 return  array('series' => $res['rows'] );
	}
	/*public function usoCFDI(){
		$sel = "SELECT * from c_usocfdi ";
		$fres = $this->queryArray($sel);

		return  array('usos' => $fres['rows']);
	} */
	public function validaClienteFact($cliente,$venta){
		$ee = "SELECT nombre from comun_facturacion where id=".$cliente;
		$eer = $this->queryArray($ee);
		$idClienteAfac = $eer['rows'][0]['nombre'];

		$ter = "SELECT idCliente from app_pos_venta where idVenta=".$venta;
		$tres = $this->queryArray($ter);
		$idClienteVenta = $tres['rows'][0]['idCliente'];

		if($idClienteVenta==null && $cliente ==0){
			return  array('resx' => 1 );
		}

		if($idClienteAfac != $idClienteVenta){
			return  array('resx' => 0 );
		}else{
			return  array('resx' => 1 );
		}


	}

	/// ch@

    public function mesa($comandaR){
    	$sql = "SELECT idmesa from com_comandas where id = '$comandaR';";
        $result = $this->queryArray($sql);
        return $result['rows'][0]['idmesa'];

    }
    public function datosComanda($comandaR){
    	$sql = "SELECT c.idmesa, e.nombre empleado, (SELECT logoempresa as logo FROM organizaciones) logo from com_comandas c
			left join empleados e on e.idempleado = c.idempleado
    		where id = '$comandaR';";
        $result = $this->queryArray($sql);
        return $result;

    }

    public function get_que_mostrar_ticket() {
    	$sucursal = $this -> sucursal();
		$sql = "SELECT
					mostrar_nombre, mostrar_tel, mostrar_domicilio, switch_info_ticket, mostrar_info_empresa, mostrar_iva, una_linea, calculo_automatico
				FROM
					com_configuracion where id_sucursal = ".$sucursal;
        $result = $this->queryArray($sql);
        return $result["rows"][0];
	}
	public function datos_organizacion(){
        $sql = "SELECT * from organizaciones c left join estados e on e.idestado=c.idestado left join municipios m on m.idmunicipio=c.idmunicipio where idorganizacion=1";
        $result = $this->queryArray($sql);
        return $result['rows'];
    }

    public function datos_sucursal($id_mesa){
		if (empty($id_mesa)) {
			$sucursal = "SELECT mp.idSuc AS id
							FROM administracion_usuarios au
							INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc
							WHERE au.idempleado = " . $_SESSION['accelog_idempleado'] . " LIMIT 1";
			$sucursal = $this -> queryArray($sucursal);
			$idSuc = $sucursal['rows'][0]['id'];
		} else {
	        $sql = "SELECT idSuc from com_mesas where id_mesa=".$id_mesa;
	        $result = $this->queryArray($sql);
	        $idSuc = $result['rows'][0]['idSuc'];
		}

        $sql = "SELECT
        			*
        		FROM
        			mrp_sucursal s
        		LEFT JOIN estados e ON e.idestado = s.idEstado
        		LEFT JOIN municipios m on m.idmunicipio = s.idMunicipio
        		WHERE
        			idSuc=".$idSuc;
        $result = $this->queryArray($sql);

        return $result['rows'];
    }



    public function closeComanda2($idComanda,$bandera,$idmesa,$tipo){


		/*
		$idComanda = $objeto['idComanda'];
		$bandera = $objeto['bandera'];
		$idmesa = $objeto['idmesa'];
		$tipo = $objeto['tipo'];
		$tel = $objeto['tel'];
		*/
		$rbandera = $bandera;


		$size = 5 - strlen($idComanda);
		$string = "";

		for ($i = 0; $i < $size; $i++)
			$string .= "0";

		// Filtra por persona
		$condicion .= (!empty($objeto['persona'])) ? ' AND a.npersona = '.$objeto['persona'] : '' ;

		if (!empty($objeto['sucursal'])) {
			$sucursal = $objeto['sucursal'];
		} else {
			$sucursal = $this -> sucursal();
		}

		// Obtiene todos los productos de la comanda
		$sql = "SELECT
					a.npersona, SUM(a.cantidad) cantidad, b.nombre, (CASE WHEN (Select precio from app_precio_sucursal where sucursal = ".$sucursal." and producto = a.idproducto limit 1) IS NULL THEN
				ROUND(b.precio, 2)ELSE ROUND((Select precio from app_precio_sucursal where sucursal = ".$sucursal." and producto = a.idproducto limit 1), 2) END) as precioventa, b.id,
					a.opcionales, a.nota_opcional, a.nota_extra, a.nota_sin, a.adicionales, a.sin, c.tipo, c.nombre nombreu, c.domicilio, d.codigo, c.nombre AS nombre_mesa,
					d.timestamp AS fecha_comanda, a.complementos, a.id_promocion, (CASE a.id_promocion WHEN 0 THEN 'producto' ELSE a.id END) as tipin, a.monto_desc, a.precioaux
				FROM
					com_pedidos a
				LEFT JOIN
						app_productos b
					ON
						b.id=a.idproducto
				LEFT JOIN
						com_comandas d
					ON
						d.id=" . $idComanda . "
				LEFT JOIN
						com_mesas c
					ON
						c.id_mesa = d.idmesa
				WHERE
					idcomanda = " . $idComanda . "
				AND
					a.origen = 1
				AND
					a.status != 3
				AND
					a.dependencia_promocion = 0
				AND
					cantidad > 0 ".
				$condicion. "
				GROUP BY
					tipin, a.npersona, a.idProducto, a.opcionales, a.adicionales, a.sin, a.tipo_desc, a.precioaux, a.complementos
				ORDER BY
					a.npersona ASC, precioventa desc, a.id, a.tipo_desc ASC";
					//print_r($sql);
		$productsComanda = $this -> queryArray($sql);

		$array = Array('rows', 'tipo');

		$contador = 0;

		// La comanda se cierra pagando todo junto
		if (!$bandera) {
			$array['tipo'][0] = 0;

			foreach ($productsComanda['rows'] as $key => $value) {
				if ($value['id_promocion'] == 0) {
				/* Impuestos del producto
				============================================================================= */

					$impuestos_comanda = 0;
					$precio = $value['precioventa'];
					$objeto['id'] = $value['id'];

					$impuestos = $this -> listar_impuestos($objeto);
					if ($impuestos['total'] > 0) {
						foreach ($impuestos['rows'] as $k => $v) {
							if ($v["clave"] == 'IEPS') {
								$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
							} else {
								if ($ieps != 0) {
									$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
								} else {
									$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
								}
							}

						// Precio e impuestos de comanda actualizados
							$precio += $producto_impuesto;
							$precio = round($precio, 2);
							$value['precioventa'] = $precio;

							$impuestos_comanda += $producto_impuesto;
						}
					}

				/* FIN Impuestos del producto
				============================================================================= */

					$items = "";
					$costo_extra = '';

				// Obtiene los opcionales si existen
					if ($value['opcionales'] != "") {
						$sql = "SELECT
									CONCAT('Con: ',GROUP_CONCAT(nombre)) nombre
								FROM
									app_productos
								WHERE
									id IN(" . $value['opcionales'] . ")";
						$itemsProduct = $this -> query($sql);

						if ($row = $itemsProduct -> fetch_array()){
							if($value['nota_opcional'] != ''){
								$items .= "(" . $row['nombre'] . ",".$value['nota_opcional'].")";
							} else {
								$items .= "(" . $row['nombre'] . ")";
							}
						} else if($value['nota_opcional'] != '') {
							$items .= "(" . $value['nota_opcional'] . ")";
						}

					} else if($value['nota_opcional'] != '') {
						$items .= "(" . $value['nota_opcional'] . ")";
					}

				// Adicionales
					$costo_extra = [];
					if (!empty($value['adicionales'])) {
						$sql = "SELECT
									CONCAT('Extras: ',GROUP_CONCAT(nombre)) nombre
								FROM
									app_productos
								WHERE
									id IN(" . $value['adicionales'] . ")";
						$itemsProduct = $this -> query($sql);

						if ($row = $itemsProduct -> fetch_array())
							$items .= "(" . $row['nombre'] . ")";

					// Obtiene el costo y nombre de los productos
						$sql = "SELECT
									nombre, ROUND(precio, 2) AS costo, id
								FROM
									app_productos
								WHERE
									id IN(" . $value['adicionales'] . ")";
						$costo_extra = $this -> queryArray($sql);
						$costo_extra = $costo_extra['rows'];

					/* Impuestos del producto
					============================================================================= */

						foreach ($costo_extra as $kk => $vv) {
							$precio = $vv['costo'];
							$objeto['id'] = $vv['id'];

							$impuestos = $this -> listar_impuestos($objeto);
							if ($impuestos['total'] > 0) {
								foreach ($impuestos['rows'] as $k => $v) {
									if ($v["clave"] == 'IEPS') {
										$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
									} else {
										if ($ieps != 0) {
											$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
										} else {
											$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
										}
									}

								// Precio e impuestos de comanda actualizados
									$precio += $producto_impuesto;
									$precio = round($precio, 2);
									$costo_extra[$kk]['costo'] = $precio;

									$impuestos_comanda += $producto_impuesto;
								}
							}
						}

					/* FIN Impuestos del producto
					============================================================================= */
					}




				// Sin
					if ($value['sin'] != "") {
						$sql = "SELECT
									CONCAT('Sin: ',GROUP_CONCAT(nombre)) nombre
								FROM
									app_productos
								WHERE
									id in(" . $value['sin'] . ")";
						$itemsProduct = $this -> query($sql);

						if ($row = $itemsProduct -> fetch_array()){
							if($value['nota_sin'] != ''){
								$items .= "(" . $row['nombre'] . ",".$value['nota_sin'].")";
							} else {
								$items .= "(" . $row['nombre'] . ")";
							}
						} else if($value['nota_opcional'] != '') {
							$items .= "(" . $value['nota_sin'] . ")";
						}
					} else if($value['nota_opcional'] != '') {
						$items .= "(" . $value['nota_sin'] . ")";
					}
					$costo_complementos = [];
				// Si tiene adicionales los agrega al total
					if (!empty($value['complementos'])) {
						$sql = "SELECT
									CONCAT('Complementos: ',GROUP_CONCAT(nombre)) nombre
								FROM
									app_productos
								WHERE
									id IN(" . $value['complementos'] . ")";
						$itemsProduct = $this -> query($sql);

						if ($row = $itemsProduct -> fetch_array())
							$items .= "(" . $row['nombre'] . ")";

					// Obtiene el costo y nombre de los productos
						$sql = "SELECT
									nombre, ROUND(precio, 2) AS costo, id
								FROM
									app_productos
								WHERE
									id IN(" . $value['complementos'] . ")";
						$costo_complementos = $this -> queryArray($sql);
						$costo_complementos = $costo_complementos['rows'];

					/* Impuestos del producto
					============================================================================= */

						foreach ($costo_complementos as $kk => $vv) {
							$precio = $vv['costo'];
							$objeto['id'] = $vv['id'];

							$impuestos = $this -> listar_impuestos($objeto);
							if ($impuestos['total'] > 0) {
								foreach ($impuestos['rows'] as $k => $v) {
									if ($v["clave"] == 'IEPS') {
										$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
									} else {
										if ($ieps != 0) {
											$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
										} else {
											$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
										}
									}

								// Precio e impuestos de comanda actualizados
									$precio += $producto_impuesto;
									$precio = round($precio, 2);
									$costo_complementos[$kk]['costo'] = $precio;

									$impuestos_comanda += $producto_impuesto;
								}
							}
						}

					/* FIN Impuestos del producto
					============================================================================= */
					}


					$flag = 0;
					///DESC
					 if ($value['monto_desc'] != NULL) {
					 	if($value['monto_desc'] != 0){
					 		$precioventa = 0;
					 		$items .= '(Desc. '.$value['monto_desc'].'%)';
						 	$precioventa = $value['precioventa'];
						 	$precioventa = $precioventa - ($precioventa*($value['monto_desc']/100));
						 	$flag = 1;
					 	}
					 }else{
					 	$precioventa = $value['precioventa'];
					 }
					///DESC FIN

					 //////////// PRECIO MODIFICADO DESDE CAJA SE SOBRE PONE A TODO LO ANTERIOR /////////
					if($value['precioaux'] > 0){
						$precioventa = $value['precioaux'];
					}else{
						if($flag != 1){
							$precioventa = $value['precioventa'];
						}

					}
					//////////// PRECIO MODIFICADO DESDE CAJA SE SOBRE PONE A TODO LO ANTERIOR FIN /////


					// Pedido
					$array['rows'][$contador] = Array(
						'impuestos' => $impuestos_comanda,
						'costo_extra' => $costo_extra,
						'costo_complementos' => $costo_complementos,
						'npersona' => $value['npersona'],
						'cantidad' => $value['cantidad'],
						'nombre' => $value['nombre'] . " $items",
						'precioventa' => $precioventa,
						'tipo' => $value['tipo'],
						'nombreu' => $value['nombreu'],
						'domicilio' => $value['domicilio'],
						'codigo' => $value['codigo'],
						'nombre_mesa' => $value['nombre_mesa'],
						'monto_desc' => $value['monto_desc'],
						'precioUnitarito' => $value['precioventa']
					);

				// Siguiente pedido
					$contador++;

				} else {
					$promocion = [];
					$promociones = [];
					$promocion = $this -> get_promocion($value['id_promocion']);
					$productsComanda['rows'][$key]['nombre'] = $promocion['nombre'];
					$productsComanda['rows'][$key]['tipo_promocion'] = $promocion['tipo'];
					$productsComanda['rows'][$key]['cantidad_to'] = $promocion['cantidad'];
					$productsComanda['rows'][$key]['cantidad_descuento'] = $promocion['cantidad_descuento'];
					$productsComanda['rows'][$key]['descuento'] = $promocion['descuento'];
					$productsComanda['rows'][$key]['precio_fijo'] = $promocion['precio_fijo'];
					$promociones = $this -> get_promociones($value['tipin'], $value['id_promocion']);
					$promociones = $promociones['rows'];
					$precio = 0;
					$items = '';
					$extras = 0;
					if($promocion['tipo'] == 1){
						foreach ($promociones as $k => $v) {
							$extras += $v['sumaExtras']*1;
							$precio += $v['precio'];
							$promociones[$k]['precio'] = 0;
						}
						$desc = (100 - $promocion['descuento']) / 100;
						$precio = $precio * $desc;

						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} else if($promocion['tipo'] == 2){
						foreach ($promociones as $k => $v) {
							if($k%2==0){
								$extras += $v['sumaExtras']*1;
								$precio += $v['precio'];
							}
							$promociones[$k]['precio'] = 0;
						}
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} else if($promocion['tipo'] == 4){
						$i = $extras = 0;
						foreach ($promociones as $k => $v) {
							$i++;
							$extras += $promociones[$k]['sumaExtras']; // suma extras
							$precio = $promocion['precio_fijo'];
							$promociones[$k]['precio'] = 0;
						}
						$precio = $precio * $i;
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;

					} else if($promocion['tipo'] == 3){
						for ($x=0; $x < $promocion['cantidad_descuento']; $x++) {
							$promociones[(count($promociones)-1) - $x]['precio'] = 0;
						}
						foreach ($promociones as $k => $v) {
							$extras += $v['sumaExtras']*1;
							$precio += $v['precio'];
							$promociones[$k]['precio'] = 0;
						}
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} else if($promocion['tipo'] == 5){
						foreach ($promociones as $k => $v) {
							if($v['comprar'] == 1){
								$extras += $v['sumaExtras']*1;
								$precio += $v['precio'];
							}else{
								$extras += $v['sumaExtras']*1;
							}
							$promociones[$k]['precio'] = 0;
						}
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					}

					//echo '<pre>'; print_r($promociones); exit();

					//$productsComanda['rows'][$key]['promociones'] = $promociones;
					//echo '<pre>'; print_r($productsComanda['rows'][$key]); exit();
					// Pedido
					$array['rows'][$contador] = Array(
						'impuestos' => '', 'costo_extra' => '',
						'costo_complementos' => '',
						'npersona' => $productsComanda['rows'][$key]['npersona'],
						'cantidad' => $productsComanda['rows'][$key]['cantidad'],
						'nombre' => $productsComanda['rows'][$key]['nombre'] . " $items",
						'precioventa' => $productsComanda['rows'][$key]['precioventa'],
						'tipo' => $productsComanda['rows'][$key]['tipo'],
						'nombreu' => $productsComanda['rows'][$key]['nombreu'],
						'domicilio' => $productsComanda['rows'][$key]['domicilio'],
						'codigo' => $productsComanda['rows'][$key]['codigo'],
						'nombre_mesa' => $productsComanda['rows'][$key]['nombre_mesa'],
						'promociones' => $promociones,
						'precioUnitarito' => $productsComanda['rows'][$key]['precioventa']
					);

				// Siguiente pedido
					$contador++;
					$precio = 0;
					$items = '';
				}

			}

		}

		$sucursal = $this -> sucursal();

		// Consulta si se debe de mostrar la propina o no
		$sql = "SELECT
					propina, tipo_operacion, mostrar_dolares
				FROM
					com_configuracion where id_sucursal = ".$sucursal;
		$result = $this -> queryArray($sql);

		$array['mostrar'] = $result['rows'][0]['propina'];
		$array['mostrar_dolares'] = $result['rows'][0]['mostrar_dolares'];
		$array['tipo_operacion'] = $result['rows'][0]['tipo_operacion'];
		$array['tel'] = $tel;

		/*
			// ** Guarda la actividad
			$fecha = date('Y-m-d H:i:s');
			// Valida que exista el empleado si no agrega un cero como id
			$usuario = (!empty($_SESSION['mesero']['id'])) ? $_SESSION['mesero']['id'] : 0;
			$sql = "INSERT INTO
						com_actividades
							(id, empleado, accion, fecha)
					VALUES
						(''," . $usuario . ",'Cierra comanda', '" . $fecha . "')";
			$actividad = $this -> query($sql);

			// Actualiza la reservacion
			$sql = "UPDATE
						com_reservaciones
					SET
						activo = 0
					WHERE
						mesa = " . $idmesa;
			$fin = $this -> query($sql);
		*/

		return $array;

    }


    public function agregaProductoComanda($idProducto,$comandaR){

    	//datos producto
    	$sql3 = "SELECT cp.costo, p.id, p.precio from app_costos_proveedor cp
					right join app_productos p on p.id =  cp.`id_producto`
					where p.id = '$idProducto'
					order by fecha desc limit 1;";
		$result3 = $this -> queryArray($sql3);
		$costo = $result3['rows'][0]['costo'];
		$id = $result3['rows'][0]['id'];
		$precio = $result3['rows'][0]['precio'];


		// inser
    	$sql = 'INSERT into com_pedidos(idcomanda,idproducto,cantidad,npersona,tipo,status,origen,costo)
    	values("'.$comandaR.'","'.$id.'",1,1,0,0,1,"'.$costo.'")';
		$res2 = $this->queryArray($sql);

		$sql2 = "UPDATE com_comandas set total = total + ".$precio." where id = ".$comandaR.";";
		$this->queryArray($sql2);

		$res = $this->queryArray($sql2);

		return  $res2['insertId'];
    }

	/// ch@ fin

	public function guardarPolitica($tipo,$dinero,$porcentaje,$puntos,$nombre){
		$insr = 'INSERT into app_politicas_tarjeta(tipo,porcentaje,dinero,puntos,nombre) values("'.$tipo.'","'.$porcentaje.'","'.$dinero.'","'.$puntos.'","'.$nombre.'")';
		$res = $this->queryArray($insr);

		return  array('id' => $res['insertId'] );

	}
	public function getPointsCard($numTarjeta){
		$selC = "SELECT t.* , c.nombre from tarjeta_regalo t, comun_cliente c where t.cliente=c.id and t.numero='".$numTarjeta."' and usada <> 1;";
		$resC = $this->queryArray($selC);
		if($resC['total'] > 0){
			return  array('estatus' => true,'puntos' => $resC['rows'][0]['puntos'], 'valor' => $resC['rows'][0]['valor'], 'idCliente' => $resC['rows'][0]['cliente'], 'nombreCliente' => $resC['rows'][0]['nombre'], 'usada' => $resC['rows'][0]['usada']);
		}else{
			return  array('estatus' => false);
		}

	}
	public function usoCFDI(){
		$sel = "SELECT * from c_usocfdi ";
		$fres = $this->queryArray($sel);

		$sel2 = "SELECT * from c_metododepago";
		$mp  = $this->queryArray($sel2);

		$sel3 = 'SELECT * from c_tiporelacion';
		$res3 = $this->queryArray($sel3);

		return  array('usos' => $fres['rows'], 'metodosdepago' => $mp['rows'], 'relaciones' => $res3['rows']);

	}
	public function versionFacturacion(){
		$sel = "SELECT version from pvt_configura_facturacion limit 1";
		$res = $this->queryArray($sel);

		return $res['rows'][0]['version'];
	}
	public function pdf33($uuid,$Email){
	//echo $uuid.'-'.$Email;

	require ('../wsinvoice/config_api.php');
	//echo 'A';
	require ('../wsinvoice/lib/fpdf.php');
	//echo 'B';
	require ('../wsinvoice/lib/QRcode.php');
	//echo 'C';
	require ('../wsinvoice/class.invoice.pdf.php');
	//echo 'dddddd';
	// Recordatorio: Mudar archivo a controlador.
	$caja = 1;
	$path = "../cont/";
	$data = $path.'xmls/facturas/temporales/'.$uuid.'.xml';
	//echo $data;
	$intRed = 3;
	$intGreen = 139;
	$intBlue = 204;
	$strPDFFile = "muestra.pdf";
	/*if($_REQUEST['nominas']==1){
		$namexml = $_REQUEST['name'];
	}else{
		$namexml = "";
	} */
	$namexml = $uuid.'.xml';
	$logo = '';
	//echo($logo. "<br>");
//echo 'iritit';
	$objXmlToPDf = new invoiceXmlToPdf($data, $logo, $intRed, $intGreen, $intBlue, $strPDFFile,$namexml,$caja,'');
	//var_dump($objXmlToPDf);
//echo '33';
	$objXmlToPDf->genPDF();
	//echo 'ii';
	//var_dump($objXmlToPDf);
			$cuponInadem = '';

	if ($Email != '') {

			require_once('../../modulos/phpmailer/sendMail.php');

			$mail->Subject = "Factura Generada";
			$mail->AltBody = "NetwarMonitor";
			$mail->MsgHTML('Factura Generada');
			if($res['rows'][0]['serieCsdEmisor']=='3'){
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $uid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uid .'.pdf');
			}else{
				$mail->AddAttachment('../../modulos/cont/xmls/facturas/temporales/'. $uid .'.xml');
				$mail->AddAttachment('../../modulos/facturas/'. $uid .'.pdf');
			}

			$Email = explode(';', $Email);
			foreach ($Email as $key => $value) {
				$mail->AddAddress($value, $value);
			}
			//$mail->AddAddress($Email, $Email);


			@$mail->Send();
		}
		return array("status" => true, "receptor" => '', "cupon" => false);
	//header("Location: http://localhost/mlog/webapp/modulos/cont/controllers/visorpdf.php?name=".$uuid.".xml&logo=f_de_foodware.png&id=temporales&caja=1&nominas=1");

	}
	public function relacionCFDI(){
		$sel = 'SELECT * from c_tiporelacion';
		$res = $this->queryArray($sel);

		return $res['rows'];
	}
	public function facturar33_($idFact, $idVenta, $bloqueo, $mensaje,$consumo,$doc,$moneda,$tipoCambio,$seriex,$usoCfdi,$mpCat,$relacion,$uuidRelacion,$camposComple){
		//echo '('.$mpCat.')';
		//print_r($_SESSION["caja"]);
		//exit();
		$_SESSION["caja"] = $this->object_to_array($_SESSION["caja"]);

		$monto = str_replace(",", "", $_SESSION["caja"]["cargos"]["total"]);
		$impuestos = 0;

		$arraytmp = (object) $_SESSION['caja'];
		foreach ($arraytmp as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden' ) {
				$impuestos = 0;
				foreach ($producto->cargos as $key2 => $value2) {
					$impuestos += $value2;
				}
			}
		}

		if ($memsaje != false || $mensaje != '') {
			$updateVenta = $this->queryArray("UPDATE app_pos_venta set observacion = '" . $mensaje . "' where idVenta =" . $idVenta);
		}

		/* --- Configuracion de las series  ---*/
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);


		if($doc == 2){ // factura
			if($resSer['rows'][0]['seriesFactura']==1){
				$folios = "SELECT serie,folio FROM pvt_serie_folio where id=".$seriex;
			}else{
				$folios = "SELECT serie,folio FROM pvt_serie_folio LIMIT 1";
			}

		}else{ // honorarios
			$folios = "SELECT serie_h,folio_h FROM pvt_serie_folio LIMIT 1";
		}

		$data = $this->queryArray($folios);
		if ($data["total"] > 0) {
			$data = $data["rows"][0];
		}
		if($doc == 2){
			$serie = $data['serie'];
			$folio = $data['folio'];
		}elseif($doc == 15){
			$folios = "SELECT serie_nc,folio_nc FROM pvt_serie_folio LIMIT 1";
			$data = $this->queryArray($folios);
			if ($data["total"] > 0) {
				$data = $data["rows"][0];
			}
			$serie = $data['serie_nc'];
			$folio = $data['folio_nc'];

		}else{
			$serie = $data['serie_h'];
			$folio = $data['folio_h'];
		}
		///Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];

		///Clavel del uso de CFDI
		$seluso = 'SELECT c_usocfdi from c_usocfdi where id='.$usoCfdi;
		//echo $seluso;
		$resuso = $this->queryArray($seluso);
		$claveUsoCfdi = $resuso['rows'][0]['c_usocfdi'];


		//echo 'eee'.$claveUsoCfdi;
		// Receptor
		//===============================================================

		$parametros['Receptor'] = array();
		if ($idFact == 0) {

			$parametros['Receptor']['RFC'] = "XAXX010101000";
			$parametros['Receptor']['RazonSocial'] = 'Factura Generica';
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;
			$lugExp = 'SELECT cp from mrp_sucursal where idSuc="'.$_SESSION['sucursal'].'"';
			$lugExpeRes = $this->queryArray($lugExp);

			$parametros['Receptor']['DomicilioFiscalReceptor'] = $lugExpeRes['rows'][0]['cp'];
			$parametros['Receptor']['RegimenFiscalReceptor'] = '616';
		} else {
			$df = (object) $this->datosFacturacion($idFact);
			$parametros['Receptor']['RFC'] = $df->rfc;
			$parametros['Receptor']['RazonSocial'] = utf8_decode($df->razon_social);
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;
			$parametros['Receptor']['DomicilioFiscalReceptor'] = $df->cp;
			$parametros['Receptor']['RegimenFiscalReceptor'] = $df->regimen_fiscal;
			/*$parametros['Receptor']['Pais'] = utf8_decode($df->pais);
			$parametros['Receptor']['Calle'] = utf8_decode($df->domicilio);
			$parametros['Receptor']['NumExt'] = $df->num_ext;
			$parametros['Receptor']['Colonia'] = utf8_decode($df->colonia);
			$parametros['Receptor']['Municipio'] = utf8_decode($df->municipio);
			$parametros['Receptor']['Ciudad'] = utf8_decode($df->ciudad);
			$parametros['Receptor']['CP'] = $df->cp;
			$parametros['Receptor']['Estado'] = utf8_decode($df->estado); */
			$parametros['Receptor']['Email1'] = $df->correo;
		}
		//Obteniendo la descripcion de la forma de pago
		$formapago = "";
		$queryFormaPago = " SELECT nombre,referencia,claveSat,monto from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;
		$resultqueryFormaPago = $this->queryArray($queryFormaPago);
		$fpf =  array();
		foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
			$fpf[$pagosValue['claveSat']]+=$pagosValue['monto'];
			if (strlen($pagosValue["referencia"]) > 0) {
				//$formapago .= $pagosValue['claveSat'] . " Ref:" . $pagosValue['referencia'] . ",";
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = $pagosValue['referencia'];
			} else {
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = '';

			}
		}
		$banderas = 0;
		foreach ($fpf as $key => $value) {
			if($value>$banderas){
				$cl = $key;
				$banderas = $value;
			}

		}

		$formapago = substr($formapago, 0, strlen($formapago) - 1);
		if ($formapago == "") {
			$formapago = ".";
		}


		$formapago = $cl;


		if($doc == 15){
			$formapago = $this->buscaFormaPago($uuidRelacion);
		}





		///Moneda
		$select = "SELECT * from cont_coin where coin_id=".$moneda;
		$resCon = $this->queryArray($select);
		////Metodo de Pago
		$selMeto = "SELECT clave from c_metododepago where id=".$mpCat;
		//echo $selMeto;
		$resMeto = $this->queryArray($selMeto);
		$payMet = $resMeto['rows'][0]['clave'];


		///Lugar de expedicion
		//$lugExpe = 'SELECT cp from pvt_configura_facturacion';
		$lugExp = 'SELECT cp from mrp_sucursal where idSuc="'.$_SESSION['sucursal'].'"';

		$lugExpeRes = $this->queryArray($lugExp);
		$Email = $df->correo;

		$parametros['DatosCFD']['FormaPago'] = utf8_decode($formapago);
		$parametros['DatosCFD']['MetodoPago'] = $payMet;
		$parametros['DatosCFD']['NumCtaPago'] = $refFormaPago;
		$parametros['DatosCFD']['TipoCambio'] = $tipoCambio;
		$parametros['DatosCFD']['Moneda'] = $resCon['rows'][0]['codigo'];
		//$parametros['DatosCFD']['Subtotal'] = str_replace(",", "", number_format($_SESSION["caja"]["cargos"]["subtotal"],2));
		$parametros['DatosCFD']['Subtotal'] = str_replace(",", "", bcdiv($_SESSION["caja"]["cargos"]["subtotal"], '1', 2));
		$parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] + $_SESSION['caja']['descGeneral'];
	   // $parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] - 0.01;
		//$parametros['DatosCFD']['Total'] = str_replace(",", "", number_format($_SESSION["caja"]["cargos"]["total"],2));
		$parametros['DatosCFD']['Total'] = str_replace(",", "", bcdiv($_SESSION["caja"]["cargos"]["total"],'1',2));

	   // $parametros['DatosCFD']['Total'] = $parametros['DatosCFD']['Total'] - 0.01;
		if($doc == 15){
			$NoF = "E";
		}else{
			$NoF = "I";
		}
		$parametros['DatosCFD']['Serie'] = $serie;
		$parametros['DatosCFD']['Folio'] = $folio;
		$parametros['DatosCFD']['TipoDeComprobante'] = $NoF; //F o C
		$parametros['DatosCFD']['MensajePDF'] = "";
		$parametros['DatosCFD']['LugarExpedicion'] = $lugExpeRes['rows'][0]['cp'];
		$parametros['DatosCFD']['Descuento'] = $_SESSION['caja']['descGeneral'];

		$x = 0;
		$descGen = 0;
		$textodescuento = "";
		//Empieza a llenar los conceptos
		foreach ($_SESSION['caja'] as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden') { 
				$producto = (object) $producto;
				$descuentogeneral = 0;
				///desceuntos
				//echo "( descuento -> ".$producto->descuento_cantidad.")";
			   /* if ($producto->tipodescuento == "%") {
					$descuentogeneral = (($producto->precioventa * str_replace(",", "", $producto->descuento)) / 100) * $producto->cantidad;
					if ($producto->descuento > 0) {
						$textodescuento.=" - " . cajaModel::cortadec(str_replace(",", "", $producto->descuento_cantidad)) . " %";
					}
				}
				if ($producto->tipodescuento == "$") {
					$descuentogeneral = $producto->descuento;
					if ($producto->descuento > 0) {
						$textodescuento.=" - $" . cajaModel::cortadec(str_replace(",", "", $producto->descuento_cantidad)) . "";
					}
				} */
					if($doc == 15){
						$producto->claveSat = '84111506';
						$producto->unidadSAT = 'ACT';
						$producto->unidad = 'Actividad';
					}
				$conceptosDatos[$x]["ClaveProdServ"] = $producto->claveSat;
				$conceptosDatos[$x]["ClaveUnidad"] = $producto->unidadSAT;
				$conceptosDatos[$x]["Cantidad"] = $producto->cantidad;
				$conceptosDatos[$x]["Unidad"] = $producto->unidad;
				$conceptosDatos[$x]["NoIdentificacion"] = $producto->codigo;
				$conceptosDatos[$x]["ValorUnitario"] = $producto->precio;
				if ($producto->descripcion != '') {
					$conceptosDatos[$x]["Descripcion"] = trim($producto->descripcion . " " . $textodescuento);
				} else {
					$conceptosDatos[$x]["Descripcion"] = trim($producto->nombre . " " . $textodescuento);
				}
				$textodescuento = '';
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio);
				//$conceptosDatos[$x]['Descuento'] = number_format($producto->descuento, 2, '.', '') * 1;
				$conceptosDatos[$x]['Descuento'] = bcdiv($producto->descuento, '1', 6) * 1;

				//echo '/'.$conceptosDatos[$x]['Descuento'] .'/';
				$conceptosDatos[$x]['cargos33'] = $producto->cargos33;
				$consumoTotal +=  $conceptosDatos[$x]['Importe']*1;
				$x++;
				//$descGen += number_format($producto->descuento, 2, '.', '') * 1;
				$descGen += bcdiv($producto->descuento, '1', 6) * 1;

			}//fin del if del ciclo
		}//fin del cilo de llenar conceptos
		//echo '('.$descGen.')';
		//exit();
		if (isset($_SESSION['caja']['cargos']['impuestosFactura'])) {
    		$nn2 = $_SESSION['caja']['cargos']['impuestosFactura'];
			$nnf = $_SESSION['caja']['cargos']['impuestosPdf'];
		}else{
			$nn2 = '';
			$nnf = '';
		}

		/* FACTURACION AZURIAN
		============================================================== */
		global $api_lite;
		if(!isset($api_lite)){
			if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/config.php');
			else require_once('../webapp/modulos/SAT/config.php');
		}
		else require $api_lite . "modulos/SAT/config.php";

		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d') . 'T' . date('H:i:s', strtotime("-60 minute"));
		//$fecha = date('Y-m-d') . 'T' . date('H:i:s');
		//echo $fecha;

		$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$logo = $this->queryArray($logo);
		$r3 = $logo["rows"][0];

		$azurian = array();
		//echo $bloqueo.'??';
		if ($bloqueo == 0) {
			//echo 'entro a bloqueo';
			$queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Rfc'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				/*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp; */
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				global $api_lite;
				if(!isset($api_lite)) echo json_encode($JSON);
				else return $JSON;
				exit();
			}
		}
		/* Observaciones pdf */
		$azurian['Observacion']['Observacion'] = $mensaje;



		  /*----- Factura de Consumo --------*/

		$sucursal = $this -> sucursal();

		$queryConsumo = "SELECT consumo from com_configuracion where id_sucursal =".$sucursal;
		$resConsumo = $this->queryArray($queryConsumo);
		$consumo = $resConsumo['rows'][0]['consumo'];

		if($consumo == 1 || $consumo=='1'){
			unset($nn2);
			unset($nnf);
			$precioSiniva = bcdiv($parametros['DatosCFD']['Total'],'1',2) / 1.16;
			//echo $ivaCon;
			$elIva = bcdiv($precioSiniva,'1',6) * 0.16;
		   // $subTotalCon = $parametros['DatosCFD']['Total'] - $ivaCon;
			$parametros['DatosCFD']['Subtotal'] = bcdiv($precioSiniva,'1',2);
			$nn2["IVA"]["16"] = bcdiv($elIva,'1',2);
			$nnf["IVA"]["16"]['Valor'] = bcdiv($elIva,'1',2);
			$parametros['DatosCFD']['Total'] = bcdiv($precioSiniva,'1',2) + bcdiv($elIva,'1',2);
			//echo $nn2["IVA"]["16.00"];
			//echo 'sub'.$parametros['DatosCFD']['Subtotal'];
		}
		//exit();
		/* IMPUESTOS
		============================================================== */
		if ($nn2 == '') {
			$nn2["IVA"]["0.0"]["Valor"] = 0.00;
		}
		if ($nnf == '') {
			$nnf["IVA"]["0.0"]["Valor"] = 0.00;
		}
		$nn = $nn2;
		$azurian['nn']['nn'] = $nn;
		$azurian['nnf']['nnf'] = $nnf;
		$azurian['org']['logo'] = $r3["logoempresa"];
	   /* CORREO RECEPTOR
		============================================================== */
		$azurian['Correo']['Correo'] = $Email;

		/* Datos Basicos
		============================================================== */
		//$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		$azurian['Basicos']['Moneda'] = $parametros['DatosCFD']['Moneda'];
		if($parametros['DatosCFD']['Moneda']!='MXN'){
			$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		}

		$azurian['Basicos']['MetodoPago'] = $parametros['DatosCFD']['MetodoPago'];
		$azurian['Basicos']['NumCtaPago'] = $parametros['DatosCFD']['NumCtaPago'];
		$azurian['Basicos']['LugarExpedicion'] = $parametros['DatosCFD']['LugarExpedicion'];
		$azurian['Basicos']['Version'] = '4.0';
		$azurian['Basicos']['Serie'] = $parametros['DatosCFD']['Serie']; //No obligatorio
		$azurian['Basicos']['Folio'] = $parametros['DatosCFD']['Folio']; //No obligatorio
		$azurian['Basicos']['Fecha'] = $fecha;
		$azurian['Basicos']['Sello'] = '';
		$azurian['Basicos']['FormaPago'] = $parametros['DatosCFD']['FormaPago'];
		$azurian['Basicos']['TipoDeComprobante'] = $parametros['DatosCFD']['TipoDeComprobante'];
		$azurian['Basicos']['Exportacion'] = '01';
		$azurian['tipoFactura'] = 'factura';
		$azurian['Basicos']['NoCertificado'] = '';
		$azurian['Basicos']['Certificado'] = '';
		$str_subtotal = number_format($parametros['DatosCFD']['Subtotal'], 6);
		$str_subtotal = bcdiv($parametros['DatosCFD']['Subtotal'],'1',6);
		$azurian['Basicos']['SubTotal'] = str_replace(",", "", $str_subtotal);
		if($descGen > 0){
			//$azurian['Basicos']['Descuento'] =bcdiv($descGen,'1',2);
			$azurian['Basicos']['Descuento'] =bcdiv($descGen,'1',2);
		}
		//$azurian['Basicos']['Descuento'] = number_format($parametros['DatosCFD']['Descuento'],2);
		//$azurian['Basicos']['Descuento'] = str_replace(',', '', $azurian['Basicos']['Descuento']);
		$str_total = bcdiv($parametros['DatosCFD']['Total'],'1',6);
		$str_total = str_replace(',', '',$str_total);
		//$str_total = $str_total - 0.01;
		//$str_total = number_format($str_total,0).'.00';  //Comente para que Salgan Decimales Normalmente
		$str_total = bcdiv($str_total,'1',6);
		$azurian['Basicos']['Total'] = str_replace(",", "", $str_total);

		/* UUID relacionados
		============================================================== */
		//$relacion,$uuidRelacion;
		$relacionXML = '';
		$relacionCAD = '';
		if($relacion!='0'){
			$relacionCAD .= $relacion.'|';
			//$relacionXML .='<cfdi:CfdiRelacionados TipoRelacion="'.$relacion.'">';
			$relacionXML .="<cfdi:CfdiRelacionados TipoRelacion='".$relacion."'>";
			$rel = explode(';', $uuidRelacion);
			foreach ($rel as $key => $value) {
				if($value!=''){
					$relacionCAD .= $value.'|';
			 		//$relacionXML.='<cfdi:CfdiRelacionado UUID="'.$value.'"/>';
			 		$relacionXML.="<cfdi:CfdiRelacionado UUID='".$value."'/>";
				}
			 }
			 $relacionXML.='</cfdi:CfdiRelacionados>';
		}
		$azurian['Realacionados']['xml'] = $relacionXML;
		$azurian['Realacionados']['cadena'] = $relacionCAD;
		/*============================================================== */
		/* Datos Emisor
		============================================================== */
		$azurian['Emisor']['RegimenFiscal'] = strtoupper($parametros['EmisorTimbre']['RegimenFiscal']);
		$azurian['Emisor']['Rfc'] = strtoupper($parametros['EmisorTimbre']['Rfc']);
		$azurian['Emisor']['Nombre'] = strtoupper($parametros['EmisorTimbre']['RazonSocial']);

		/* Datos Fiscales Emisor
		============================================================== */

		/*$azurian['FiscalesEmisor']['calle'] = $parametros['EmisorTimbre']['Calle'];
		$azurian['FiscalesEmisor']['noExterior'] = $parametros['EmisorTimbre']['NumExt'];
		$azurian['FiscalesEmisor']['colonia'] = $parametros['EmisorTimbre']['Colonia'];
		$azurian['FiscalesEmisor']['localidad'] = $parametros['EmisorTimbre']['Ciudad'];
		$azurian['FiscalesEmisor']['municipio'] = $parametros['EmisorTimbre']['Municipio'];
		$azurian['FiscalesEmisor']['estado'] = $parametros['EmisorTimbre']['Estado'];
		$azurian['FiscalesEmisor']['pais'] = $parametros['EmisorTimbre']['Pais'];
		$azurian['FiscalesEmisor']['codigoPostal'] = $parametros['EmisorTimbre']['CP']; */
		/* Datos Receptor
		============================================================== */

		$azurian['Receptor']['Rfc'] = strtoupper($parametros['Receptor']['RFC']);
		$azurian['Receptor']['Nombre'] = strtoupper($parametros['Receptor']['RazonSocial']);
		$azurian['Receptor']['UsoCFDI'] = $parametros['Receptor']['UsoCFDI'];
		$azurian['Receptor']['DomicilioFiscalReceptor'] = $parametros['Receptor']['DomicilioFiscalReceptor'];
		$azurian['Receptor']['RegimenFiscalReceptor'] = $parametros['Receptor']['RegimenFiscalReceptor'];

		$conceptosOri = '';
		$conceptos = '';
		$conceptosx = '';
		$conceptosxR = '';
		$conceptosR = '';
		$ivasConceptos = 0;
		$iepsConceptos = 0;
		$conceptosOriR = '';
		$baseIVAGeneral = 0;
		$base0 = 0;
		$base16 = 0;
		$sucursal = $this -> sucursal();

		/*-----Factura de Consumo ---------*/
		/*============================================================== */
		if($consumo == 1 || $consumo=='1'){
			unset($conceptosDatos);
			$selec = "SELECT cat.clave from c_consumo cat , com_configuracion f where f.id_consumo_clave = cat.id and f.id_sucursal = ".$sucursal;
			$reCla  = $this->queryArray($selec);
				$conceptosDatos[0]["ClaveProdServ"] = $reCla['rows'][0]['clave'];
				$conceptosDatos[0]["ClaveUnidad"] = 'E48';
				$conceptosDatos[0]["Cantidad"] = 1;
				$conceptosDatos[0]["Unidad"] = 'zz';
				$conceptosDatos[0]["NoIdentificacion"] = '';
				$conceptosDatos[0]["ValorUnitario"] = $precioSiniva;
				$conceptosDatos[0]["Descripcion"] = "Consumo de Alimentos y bebidas";
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[0]['Importe'] = $precioSiniva;
				$conceptosDatos[0]['cargos33']->IVA = array('tasa' =>16 ,'importe' => $elIva);
				$consumoTotal =  $precioSiniva*1;

		}
		$descGen2  = 0;
		/*============================================================== */
		//se emepiza a llenar los conceptos en el arreglo de azurian
		foreach ($conceptosDatos as $key => $value) {
			$value['Descripcion'] = preg_replace("/'/", "&apos;", $value['Descripcion']);
			$value['Descripcion'] = preg_replace('/"/', "&quot;", $value['Descripcion']);
		   // $value['Descripcion'] = preg_replace('("|\')', "&apos;", $value['Descripcion']);
			$value['Descripcion'] = eregi_replace("[\n|\r|\n\r]", " ", $value['Descripcion']);
			$value['Descripcion'] = trim($value['Descripcion']);
			if($value['Unidad']==''){
				$value['Unidad']= "No Aplica";
			}
			if($value['ClaveProdServ'] == ''){
				$value['ClaveProdServ'] = '01010101';
			}
			$conceptosOri.='|'.$value['ClaveProdServ'].'|';
			//$conceptosOri.=$value['NoIdentificacion'] . '|';
			$conceptosOri.=$value['Cantidad'] . '|';
			$conceptosOri.=$value['ClaveUnidad'] . '|';
			$conceptosOri.=$value['Unidad'] . '|';
			$conceptosOri.=$value['Descripcion'] . '|';
			if($value['Descuento'] > 0){
				//$ValorUni = (str_replace(",", "", number_format($value['Importe'],2))+ str_replace(",", "", number_format($value['Descuento'],2))) / $value['Cantidad'];
				$ValorUni = (str_replace(",", "", bcdiv($value['Importe'],'1',2))+ str_replace(",", "", bcdiv($value['Descuento'],'1',2))) / $value['Cantidad'];
				//$Impor =  str_replace(",", "", number_format($value['Importe'],2))+ str_replace(",", "", number_format($value['Descuento'],2));
				$Impor =  str_replace(",", "", bcdiv($value['Importe'],'1',6))+ str_replace(",", "", bcdiv($value['Descuento'],'1',6));


				//$conceptosOri.=str_replace(",", "", number_format($ValorUni,2)) . '|';
				$conceptosOri.=str_replace(",", "", bcdiv($ValorUni,'1',6)) . '|';
				//$conceptosOri.=str_replace(",", "", number_format($Impor,2)). '|';
				$conceptosOri.=str_replace(",", "", bcdiv($Impor,'1',6)). '|';
				//$conceptosOri.=str_replace(",", "", number_format($value['Descuento'],2)). '|';
				$conceptosOri.=str_replace(",", "", bcdiv($value['Descuento'],'1',2)). '|';
				//$des  = ' Descuento="'.str_replace(",", "", number_format($value['Descuento'],2)).'"';
				$des  = " Descuento='".str_replace(",", "", bcdiv($value['Descuento'],'1',2))."'";
				$descGen2 += bcdiv($value['Descuento'],'1',2);

			}else{
				//$ValorUni = str_replace(",", "", number_format($value['ValorUnitario'],2));
				$ValorUni = str_replace(",", "", bcdiv($value['ValorUnitario'],'1',6));
				//$Impor =  str_replace(",", "", number_format($value['Importe'],2));
				$Impor =  str_replace(",", "", bcdiv($value['Importe'],'1',6));
				//$conceptosOri.=str_replace(",", "", number_format($value['ValorUnitario'],2)) . '|';
				$conceptosOri.=str_replace(",", "", bcdiv($value['ValorUnitario'],'1',6)) . '|';
				//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)). '|';
				$conceptosOri.=str_replace(",", "", bcdiv($value['Importe'],'1',6)). '|';
				$conceptosOri.='02|';
				$des = '';
			}



			//$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' ClaveUnidad='".$value['ClaveUnidad']."' Cantidad='" . $value['Cantidad'] . "' Unidad='" . $value['Unidad'] . "' NoIdentificacion='".$value['NoIdentificacion']."' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", number_format($value['ValorUnitario'],2)) . "' Importe='" . str_replace(",", "", number_format($value['Importe'],2)) . "'>";
			//$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' Cantidad='" . $value['Cantidad'] . "' ClaveUnidad='".$value['ClaveUnidad']."' Unidad='" . $value['Unidad'] . "' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", number_format($ValorUni,2)) . "' Importe='" . str_replace(",", "", number_format($Impor,2)) . "'".$des.">";
			$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' Cantidad='" . $value['Cantidad'] . "' ClaveUnidad='".$value['ClaveUnidad']."' Unidad='" . $value['Unidad'] . "' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", bcdiv($ValorUni,'1',6)) . "' Importe='" . str_replace(",", "", bcdiv($Impor,'1',6)) . "'".$des." ObjetoImp='02'>";
			$sumadelosimportes +=bcdiv($Impor,'1',6);
			//$conceptos.='<cfdi:Concepto ClaveProdServ="'.$value['ClaveProdServ'].'" ClaveUnidad="'.$value['ClaveUnidad'].'" Cantidad="'.$value['Cantidad'] .'" Unidad="'.$value['Unidad'] .'" Descripcion="'.$value['Descripcion'].'" ValorUnitario="'.str_replace(",", "", number_format($ValorUni,2)).'" Importe="'.str_replace(",", "", number_format($Impor,2)) .'"'.$des.'>';
			$conceptos.='<cfdi:Impuestos>';
			//echo '---------------------------------------------------------------------';
			//print_r($value['cargos33']);
			//echo '---------------------------------------------------------------------';
			if($value['cargos33']==''){
				$value['cargos33']->IVA = array('tasa' =>'00' ,'importe' => '0.00');
			}
			//print_r($value['cargos33']);
			$iepsCont = 0;
			foreach ($value['cargos33'] as $kim => $valim) {
				///echo '?'.print_r($valim).'?';
				if($kim == 'IEPS' || $kim == 'IVA' || $kim == 'IVAE'){
					if($kim == 'IVA' || $kim == 'IVAE'){
						$immpuesSAT = '002';
						if($valim['tasa']=='0'){
							$valim['tasa']='00';
						}
						//echo $valim['tasa'].')';
						$rsw = strlen($valim['tasa']);
						//echo $rsw;
						if($rsw == 1){
							$valim['tasa'] = '0.'.$valim['tasa'].'0000';
						}else{
							$valim['tasa'] = '0.'.$valim['tasa'].'0000';
						}


						if($des!=''){
							//$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							$base = str_replace(",", "", bcdiv($Impor,'1',6)) - str_replace(",", "", bcdiv($value['Descuento'],'1',6));
							//$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
							$conceptosOri.=str_replace(",", "", bcdiv($base,'1',6)).'|';
						}else{
							//$base = str_replace(",", "", number_format($value['Importe'],2));
							$base = (str_replace(",", "", bcdiv($value['Importe'],'1',6)) + $iepsCont);
							$conceptosOri.=bcdiv($base,'1',6).'|';
						}

						if($valim['nombre']=='IVA EXENTO'){
							$tFacto = 'Exento';
														//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
							$conceptosOri.=$immpuesSAT.'|';
							$conceptosOri.=$tFacto.'';
							//$conceptosOri.='0.160000|';
							//$conceptosOri.=$valim['tasa'].'|';
							//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
							//$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';
							//$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';
							$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", bcdiv($base,'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='".$tFacto."' />";
							$ivasConceptos+=bcdiv($valim['importe'],'1',6);
						}else{
							$tFacto = 'Tasa';

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.=$tFacto.'|';
						//$conceptosOri.='0.160000|';
						$conceptosOri.=$valim['tasa'].'|';
						//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
						//$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';
						$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';

						//echo 'X'.$iepsCont.'X';

						//echo $conceptosOri.'>>';exit();
						//$conceptos.="<cfdi:Traslados>";
						//$conceptos.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						//$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", number_format($base,2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", bcdiv($base,'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='".$tFacto."' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", bcdiv($valim['importe'],'1',6))."' />";
						$ivasConceptos+=bcdiv($valim['importe'],'1',6);
						if($valim['tasa'] > 0){
							$base16 += str_replace(",", "", bcdiv($base,'1',6));
						}else{
							$base0 += str_replace(",", "", bcdiv($base,'1',6));
						}
						//$conceptos.='<cfdi:Traslado Base="'.str_replace(",", "", number_format($base,2)).'" Impuesto="'.$immpuesSAT.'" TipoFactor="Tasa" TasaOCuota="'.$valim['tasa'].'" Importe="'.str_replace(",", "", number_format($valim['importe'],2)).'" />';
						//$conceptos.="</cfdi:Traslados>";
						}///fin sin exento flujo normal
					}else{
						$immpuesSAT = '003';
						if($valim['tasa']=='0'){
							$valim['tasa']='00';
						}
						//echo $valim['tasa'].')';
						$rsw = strlen($valim['tasa']);
						//echo $rsw;
						if($rsw == 1){
							$valim['tasa'] = '0.0'.$valim['tasa'].'0000';
						}else{
							$valim['tasa'] = '0.'.$valim['tasa'].'0000';
						}

						if($des!=''){
							//$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							//$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
							$base = str_replace(",", "", bcdiv($Impor,'1',6)) - str_replace(",", "", bcdiv($value['Descuento'],'1',2));
							$conceptosOri.=str_replace(",", "", bcdiv($base,'1',2)).'|';
						}else{
							//$base = str_replace(",", "", number_format($value['Importe'],2));
							//$conceptosOri.=$base.'|';
							$base = str_replace(",", "", bcdiv($value['Importe'],'1',6));
							$conceptosOri.=$base.'|';
						}

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.='Tasa|';
						//$conceptosOri.='0.160000|';
						$conceptosOri.=$valim['tasa'].'|';
						//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
						$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';

						//$valim['tasa'] = '0.'.$valim['tasa'];
						//$conceptos.="<cfdi:Traslados>";
						//$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", bcdiv($value['Importe'],'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", bcdiv($valim['importe'],'1',6))."' />";
						$iepsConceptos+=bcdiv($valim['importe'],'1',6);
						//$conceptos.="</cfdi:Traslados>";
						if($valim['formula']==1){
							$iepsCont = str_replace(",", "", bcdiv($valim['importe'],'1',6));
						}

						//echo '('.$iepsCont.')';
					}

				}else{
					if($kim == 'IVAR' || $kim == 'ISR'){
						if($kim == 'IVAR'){
							$immpuesSAT = '002';
							//$valim['tasa'] = 10.6667;
						}else{
							$immpuesSAT = '001';
						}

						if($valim['tasa']=='0'){
							$valim['tasa']='00';
						}
						//echo $valim['tasa'].')';
						if($valim['tasa']==10.6667){
							$valim['tasa'] = '0.106667';
						}else{
							$rsw = strlen($valim['tasa']);
							if($rsw == 1){
								$valim['tasa'] = '0.0'.$valim['tasa'].'0000';
							}else{
								$valim['tasa'] = '0.'.$valim['tasa'].'0000';
							}
						}



						if($des!=''){
							//$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							//$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
							$base = str_replace(",", "", bcdiv($Impor,'1',6)) - str_replace(",", "", bcdiv($value['Descuento'],'1',6));
							$conceptosOriR.=str_replace(",", "", bcdiv($base,'1',2)).'|';
						}else{
							//$base = str_replace(",", "", number_format($value['Importe'],2));
							//$conceptosOri.=$base.'|';
							$base = str_replace(",", "", bcdiv($value['Importe'],'1',6));
							$conceptosOriR.=$base.'|';
						}

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOriR.=$immpuesSAT.'|';
						$conceptosOriR.='Tasa|';
						//$conceptosOri.='0.160000|';
						$conceptosOriR.=$valim['tasa'].'|';
						//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
						$conceptosOriR.=str_replace(",", "", number_format(round($valim['importe'],2),2)).'|';

						//$conceptosxR.="<cfdi:Retencion Base='".str_replace(",", "", number_format($base,2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptosxR.="<cfdi:Retencion Base='".str_replace(",", "", bcdiv($base,'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format(round($valim['importe'],2),2))."' />";
						$ivarConceptos+=round($valim['importe'],2);
					}
				}
			}
			$iepsCont = 0;
			//echo $conceptosOri;
			$conceptosOri.=$conceptosOriR;
			$conceptosOriR = '';
			if($conceptosx!=''){
				$conceptos.="<cfdi:Traslados>".$conceptosx."</cfdi:Traslados>";
				$conceptosx = '';
			}
			if($conceptosxR!=''){
				$conceptos.="<cfdi:Retenciones>".$conceptosxR."</cfdi:Retenciones>";
				$conceptosxR = '';
			}
			//exit();
			$conceptos.="</cfdi:Impuestos>";
			$conceptos.='</cfdi:Concepto>';
			//$subTotImportes += (float) str_replace(",", "", number_format($value['Importe'],2));
			$subTotImportes += (float) str_replace(",", "", bcdiv($value['Importe'],'1',2));
		}
		//exit();
		//print_r($azurian);
		/*echo $conceptos;
		print_r($conceptosDatos);
		exit(); */
				//////////impuestos azurian
		$ivas = '';
		$tisr = 0.00;
		$tiva = 0.00;
		$tieps = 0.00;

		$oriisr = '';
		$oriiva = '';

		$isr = '';
		$iva = '';
		$azurian['Conceptos']['conceptos'] = $conceptos;
		$azurian['Conceptos']['conceptosOri'] = $conceptosOri;

		$traslads = '';
		$retenids = '';
		$haytras = 0;
		$hayret = 0;
		$trasladsimp = 0.00;
		$retenciones = 0.00;
		$trasxml = '';
		$retexml = '';
		$sumImTras = 0;
		$sumRetHijos = 0;

		foreach ($nn as $clave => $imm) {
			if ($clave == 'IEPS' || $clave == 'IVA'   ) {

				$haytras = 1;
				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
						$tasaOcu = '0.000000';
					}else{

						$rsw = strlen($clavetasa);

						if($rsw==1){
							$tasaOcu = '0.0'.$clavetasa.'0000';
						}else{
							$tasaOcu = '0.'.$clavetasa.'0000';
						}

					}
					if ($clave == 'IEPS') {
						//$tieps+=number_format($val, 2, '.', '');
						$tieps+=bcdiv($val,'1',2);
						$clIm = '003';
						$traslads.='|' . $clIm . '|';
						$traslads.='Tasa|';
					   // $traslads.='' . $clavetasa . '|';
						//$traslads.='0.160000|';
						$traslads.=$tasaOcu.'|';
						//$traslads.=number_format($val, 2, '.', '');
						if($tasaOcu == '0.000000'){
							$traslads.=bcdiv(0.000000,'1',2);
						}else{
							//$traslads.=bcdiv($iepsConceptos,'1',6);
							//$traslads.=round($iepsConceptos,2);
							$traslads.=str_replace(',', '', number_format(round($iepsConceptos,2),2));
						}

						//$trasladsimp+=number_format($val, 2, '.', '');
						//echo 'val='.$val.'<br>sumat='.$trasladsimp;
						if($tasaOcu == '0.000000'){

						}else{
							$trasladsimp+=bcdiv($iepsConceptos,'1',6);
						}

						//$trasxml.="<cfdi:Traslado Impuesto='" . $clIm . "' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='" . number_format($val, 2, '.', '') . "' />";
						//$trasxml.='<cfdi:Traslado Impuesto="'.$clIm .'" TipoFactor="Tasa" TasaOCuota="'.$tasaOcu.'" Importe="'.number_format($val, 2, '.', '') .'" />';
						//$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".number_format($val, 2, '.', '')."' />";

						if($tasaOcu == '0.000000'){
								$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv(0.000000,'1',2)."' />";

						}else{
							//$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv($iepsConceptos,'1',6)."' />";
							$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".str_replace(',', '', number_format(round($iepsConceptos,2),2))."' />";
							$sumImTras += round($iepsConceptos,2);
						}
					}
					if ($clave == 'IVA' || $clave == 'IVA' || $clave == 'IVAE') {
							//$tiva+=number_format($val, 2, '.', '');
							$tiva+=bcdiv($val,'1',2);
							$clIm = '002';
						if($tasaOcu == '0.000000'){
							$traslads.='|' . $base0 . '|';
						}else{
							$traslads.='|' . $base16 . '|';
						}

						$traslads.='|' . $clIm . '|';
						$traslads.='Tasa|';
					   // $traslads.='' . $clavetasa . '|';
						//$traslads.='0.160000|';
						$traslads.=$tasaOcu.'|';
						//$traslads.=number_format($val, 2, '.', '');
						if($tasaOcu == '0.000000'){
							$traslads.=bcdiv(0.000000,'1',2);
						}else{
							//$traslads.=bcdiv($ivasConceptos,'1',6);
							$traslads.=round($ivasConceptos,2);
						}

						//$trasladsimp+=number_format($val, 2, '.', '');
						//echo 'val='.$val.'<br>sumat='.$trasladsimp;
						if($tasaOcu == '0.000000'){

						}else{
							$trasladsimp+=bcdiv($ivasConceptos,'1',6);
						}

						//$trasxml.="<cfdi:Traslado Impuesto='" . $clIm . "' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='" . number_format($val, 2, '.', '') . "' />";
						//$trasxml.='<cfdi:Traslado Impuesto="'.$clIm .'" TipoFactor="Tasa" TasaOCuota="'.$tasaOcu.'" Importe="'.number_format($val, 2, '.', '') .'" />';
						//$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".number_format($val, 2, '.', '')."' />";

						if($tasaOcu == '0.000000'){
								$trasxml.="<cfdi:Traslado Base='".$base0."' Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv(0.000000,'1',2)."' />";

						}else{
							//$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv($ivasConceptos,'1',6)."' />";
							$trasxml.="<cfdi:Traslado Base='".$base16."' Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".round($ivasConceptos,2)."' />";
							$sumImTras += round($ivasConceptos,2);
						}

					}


				}
			} elseif ($clave == 'ISR' || $clave == 'IVAR') {
				$hayret = 1;

				/*foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
				if($clave == 'IVAR'){
					$clave = substr($clave, 0, -1);
					$king = 1;
				}
					$tisr+=number_format($val, 2, '.', '');
					$retenids.='|' . $clave . '|';
					$retenidsT.='' . number_format($val, 2, '.', '') . '|';
					$retenids.=number_format($val, 2, '.', '');
					$retenciones+=number_format($val, 2, '.', '');
					//$retexml.="<cfdi:Retencion Impuesto='" . $clave . "' Importe='" . number_format($val, 2, '.', '') . "' />";
					$retexml.='<cfdi:Retencion Impuesto="' . $clave . '" Importe="' . number_format($val, 2, '.', '') . '" />';

					/*if($king ==1){
						$clave = 'IVAR';
						$king = 0;
					} */
				//} */
				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
						$tasaOcu = '0.000000';
					}else{

						$rsw = strlen($clavetasa);

						if($rsw==1){
							$tasaOcu = '0.0'.$clavetasa.'0000';
						}else{
							$tasaOcu = '0.'.$clavetasa.'0000';
						}

					}
					if ($clave == 'ISR') {
						//$tieps+=number_format($val, 2, '.', '');
						$tieps+=bcdiv($val,'1',6);
						$clIm = '001';
					}
					if ($clave == 'IVAR') {
						//$tiva+=number_format($val, 2, '.', '');
						$tiva+=bcdiv($val,'1',6);
						$clIm = '002';
					}

					//$tisr+=number_format($val, 2, '.', '');
					$tisr+=bcdiv($val,'1',6);
					$retenids.='|' . $clIm . '|';
					//$retenids.='Tasa|';
				   // $traslads.='' . $clavetasa . '|';
					//$traslads.='0.160000|';
					//$retenids.=$tasaOcu.'|';
					//$retenids.=number_format($val, 2, '.', '');
					//$retenciones+=number_format($val, 2, '.', '');
					//$retenids.=bcdiv($val,'1',2);
					//$retenids.=round($val,2);
					$retenids.=str_replace(',', '', round($val,2));
					$retenciones+=bcdiv($val,'1',2);
					//$trasxml.="<cfdi:Traslado Impuesto='" . $clIm . "' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='" . number_format($val, 2, '.', '') . "' />";
					//$trasxml.='<cfdi:Traslado Impuesto="'.$clIm .'" TipoFactor="Tasa" TasaOCuota="'.$tasaOcu.'" Importe="'.number_format($val, 2, '.', '') .'" />';
					//$retexml.="<cfdi:Retencion Impuesto='".$clIm ."' Importe='".number_format($val, 2, '.', '')."' />";
					//$retexml.="<cfdi:Retencion Impuesto='".$clIm ."' Importe='".bcdiv($val,'1',2)."' />";
					//$retexml.="<cfdi:Retencion Impuesto='".$clIm ."' Importe='".round($val,2)."' />";
					$retexml.="<cfdi:Retencion Impuesto='".$clIm ."' Importe='".str_replace(',', '', round($val,2))."' />";
					$sumRetHijos+=round($val,2);

				}
			}
		}
		////fin del foreach nn
		//echo '///////'.$traslads;

		$azurian['Impuestos']['totalImpuestosIeps'] = $tieps;

		if ($haytras == 1) {
			$iva.='<cfdi:Traslados>' . $trasxml . '</cfdi:Traslados>';
		} else {
			/*$traslads.='|IVA|';
			$traslads.='0.00|';
			$traslads.='0.00';
			$trasladsimp = '0.00';
			$iva.="<cfdi:Traslados><cfdi:Traslado Impuesto='IVA' tasa='0.00' importe='0.00' /></cfdi:Traslados>"; */
			//$iva.='<cfdi:Traslados><cfdi:Traslado Impuesto="IVA" tasa="0.00" importe="0.00" /></cfdi:Traslados>';
		}
		if ($hayret == 1) {
			$isr.='<cfdi:Retenciones>' . $retexml . '</cfdi:Retenciones>';
		}
		  if($hayret == 1){
			//$cadRet = '|'.str_replace(',', '', bcdiv($tisr,'1',2));
			// ****** nuevo rst
			$cadRet = '|'.str_replace(',', '', round($tisr,2));
			//echo $ivarConceptos;
			//echo '('.$sumRetHijos.'>'.bcdiv($ivarConceptos,'1',2).')';
			//exit();
			if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
				$cadRet = '|'.str_replace(',', '', round($ivarConceptos,2));
			}else{
				$cadRet = '|'.str_replace(',', '', bcdiv($ivarConceptos,'1',2));
			}


			//$cadRet = '|'.str_replace(',', '', number_format($tisr,2));
		  }else{
			$cadRet = '';
		  }
		  //echo 'Cad ret ='.$cadRet.'---'.$tisr;
		   ///////Ajuste centavo
		/*echo 'SubImportes='.$subTotImportes.'<br>';
		echo 'SubAzurian='.$azurian['Basicos']['subTotal'].'<br>';
		echo 'totimpuestos='.$trasladsimp.'<br>';
		echo 'TotalAzurian='.$azurian['Basicos']['total'].'<br>';
		echo '('.$subTotImportes.'-'.$azurian['Basicos']['subTotal'].')<br>'; */

		//$xsubT =  number_format($subTotImportes, 2, '.', '');
		//$xsubA =  number_format($azurian['Basicos']['SubTotal'], 2, '.', '');
		/*$xsubT =  bcdiv($subTotImportes,'1',2);
		$xsubA =  bcdiv($azurian['Basicos']['SubTotal'],'1',2);
		/*echo 'sub delfor='.$subTotImportes.'<br>azurian subt='.$azurian['Basicos']['SubTotal'].'<br>';
		echo 'rdondeado='.round($azurian['Basicos']['SubTotal'],2).'<br>total='.$azurian['Basicos']['Total']; */
		/*$x =  round($azurian['Basicos']['SubTotal'],2) - $subTotImportes;
		if($x!=0){
			$azurian['Basicos']['SubTotal'] = round($azurian['Basicos']['SubTotal'],2) - 0.01;
			$azurian['Basicos']['SubTotal'] = round($azurian['Basicos']['SubTotal'],2);
			$azurian['Basicos']['Total'] = round($azurian['Basicos']['Total'],2) - 0.01;
			$azurian['Basicos']['Total'] = round($azurian['Basicos']['Total'],2);
		} */
		//exit();

		/*sub delfor=422.405172
		azurian subt=422.405172
		rdondeado=422.41
		total=489.990000 */
		/*if($descGen < 0){
			if($xsubT < $xsubA){
					$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] - 0.01;
					$trasladsimp = $trasladsimp + 0.01;
				}elseif($xsubT > $xsubA){
					if($trasladsimp > 0){
						$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + 0.01;
						$trasladsimp = $trasladsimp - 0.01;
					}else{
						$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + 0.01;
						$azurian['Basicos']['Total'] = $azurian['Basicos']['Total'] + 0.01;
					}

				}
	    }else{
	    	//$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + number_format($descGen, 2, '.', '');
	    	$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + bcdiv($descGen,'1',2);
	    }  */

	    //echo $retenids.'   --- '.$cadRet;exit();
		  $azurian['Impuestos']['isr'] = $retenids.$cadRet;
		  //echo $$azurian['Impuestos']['isr'];
		  //$azurian['Impuestos']['iva'] = $traslads . '|' . number_format($trasladsimp, 2, '.', '');
		  ///Nuevo para uso ieps **********
		  $azurian['Impuestos']['iva'] = $traslads . '|' . bcdiv(round($trasladsimp,2),'1',2);
		  $azurian['Impuestos']['iva'] = $traslads . '|' .bcdiv(round($sumImTras,2),'1',2);
		  //$azurian['Impuestos']['iva'] = $traslads . '|' . round($trasladsimp,2);

		  //$azurian['Impuestos']['totalImpuestosRetenidos'] = number_format($retenciones, 2, '.', '');
		  /// ****** Nuevo isr y iva retenido ivarConceptos
		  //$azurian['Impuestos']['totalImpuestosRetenidos'] = bcdiv($retenciones,'1',2);
		  if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
		   	$azurian['Impuestos']['totalImpuestosRetenidos'] = round($ivarConceptos,2);
			}else{
				$azurian['Impuestos']['totalImpuestosRetenidos'] = bcdiv($ivarConceptos,'1',2);
			}
		  //echo 'total trasladdos='.$azurian['Impuestos']['totalImpuestosRetenidos'];
		  //$azurian['Impuestos']['totalImpuestosTrasladados'] = number_format($trasladsimp, 2, '.', '');
		  ///Nuevo para uso ieps ******
		  //$azurian['Impuestos']['totalImpuestosTrasladados'] = bcdiv(round($trasladsimp,2),'1',2);
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = bcdiv(round($sumImTras,2),'1',2);
		  //$azurian['Impuestos']['totalImpuestosTrasladados'] = round($trasladsimp,2);
		  //$azurian['Basicos']['SubTotal']  = number_format($azurian['Basicos']['SubTotal'], 2, '.', '');
		  ////truncado
		  //$azurian['Basicos']['SubTotal']  = bcdiv($azurian['Basicos']['SubTotal'],'1',2);
		  //$azurian['Basicos']['Total']  = bcdiv($azurian['Basicos']['Total'],'1',2);
		 $totalXML =  round($sumadelosimportes,2) + round($ivasConceptos,2) + round($iepsConceptos,2) ;
		 $subtotalXML = round($sumadelosimportes,2);
		  ////redondeo
		  //$azurian['Basicos']['SubTotal']  = round($azurian['Basicos']['SubTotal'],2);
		  //$azurian['Basicos']['Total']  = round($azurian['Basicos']['Total'],2);
		/*-------------------------------------------------------------------*/
		  $azurian['Basicos']['SubTotal']  = bcdiv($subtotalXML,'1',2);
		  if($descGen > 0){
		  	$azurian['Basicos']['Descuento'] = $descGen2;
		  	if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
		  		$azurian['Basicos']['Total']  = ($totalXML - bcdiv(round($ivarConceptos,2),'1',2)) - bcdiv($descGen2,'1',2);
		  	}else{
		  		$azurian['Basicos']['Total']  = ($totalXML - bcdiv($ivarConceptos,'1',2)) - bcdiv($descGen2,'1',2);
		  	}
		  }else{
		  	if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
		  		$azurian['Basicos']['Total']  = $totalXML - bcdiv(round($ivarConceptos,2),'1',2);
		  	}else{
		  		$azurian['Basicos']['Total']  = $totalXML - bcdiv($ivarConceptos,'1',2);
		  	}
		  }

		  $azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2);



	   /* echo 'SubImportes='.$subTotImportes.'<br>';
		echo 'SubAzurian='.$azurian['Basicos']['subTotal'].'<br>';
		echo 'totimpuestos='.$azurian['Impuestos']['totalImpuestosTrasladados'].'<br>';
		echo 'TotalAzurian='.$azurian['Basicos']['total'];
		exit();  */


		$ivas.=$isr . $iva;

		$azurian['Impuestos']['ivas'] = $ivas;
			$soloExento = 0;
		  	foreach ($nn as $clave => $imm) {
				if ($clave != 'IVAE') {
					$soloExento = 1;

				}
			}
			if($soloExento == 0){
				$azurian['Impuestos']['iva'] = '';
			}
			$xmlC = '';
			$cadC = '';
			if($camposComple!=''){
				$th = "SELECT * from app_complementos where id=1";
    			$rles = $this->queryArray($th);
    			$xmlcomple = $rles['rows'][0]['xml'];
    			$cadenacomple = $rles['rows'][0]['cadena'];
				//echo $dataString;
				//echo '<br>';
				$string = explode('&',$camposComple);
				$cont = 0;
				foreach ($string as $key => $value) {
					//echo $value.'<br>';
					$straux = explode('=',$value);
					$campo = '@'.$straux[0];
					$valor = str_replace('+', ' ', $straux[1]);
					//echo 'campo='.$campo.' Valor='.$valor.'<br>';
					//str_replace(search, replace, subject)
					if($cont > 0){
						$xmlC = str_replace($campo.' ',$valor,$xmlC);
						$cadC = str_replace($campo.' ',$valor,$cadC);
					}else{
						$xmlC = str_replace($campo.' ',$valor,$xmlcomple);
						$cadC = str_replace($campo.' ',$valor,$cadenacomple);
					}
					$cont++;
				}
			}

			$azurian['CompleImpues']['xmlComple']=$xmlC;
			$azurian['CompleImpues']['cadenaComple']=$cadC;

				$traslados = 0;
		    	$retenidos = 0;
		    	$chain = '';
		    	$xmlLoc = '';
		    	$sessionArray = $this->object_to_array($_SESSION['caja']);
		    	foreach ($sessionArray as $key => $value) {
					if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
						foreach ($value['cargosLocales'] as $key2 => $value2) {
							if($key2 != 'MR'){
								$_SESSION['caja']['cargos']['impuestos'][$key2]=bcdiv($value2['importe'],'1',10) * 1;
								$_SESSION['caja']['cargos']['impuestosPorcentajes'][$value2['nombre']] = bcdiv($value2['importe'],'1',10) * 1;
								$_SESSION['caja']['cargos']['impuestosFactura'][$key2][$value2['tasa']] = bcdiv($value2['importe'],'1',10) * 1;
								$_SESSION['caja']['cargos']['impuestosPdf'][$key2][$value2['tasa']]['Valor'] = bcdiv($value2['importe'],'1',10) * 1;
		 						$traslados = $value2['importe'];
		 						$xmlLoc.="<implocal:TrasladosLocales ImpLocTrasladado='".$value2['nombre']."' TasadeTraslado='".$value2['tasa']."' Importe='".bcdiv($value2['importe'],'1',2) ."' />";
		 						$chain.= '|'.$value2['nombre'].'|'.$value2['tasa'].'|'.bcdiv($value2['importe'],'1',2);
							}else{
								$_SESSION['caja']['cargos']['impuestos'][$key2]=bcdiv($value2['importe'],'1',2) * 1;
								$_SESSION['caja']['cargos']['impuestosPorcentajes'][$value2['nombre']] = bcdiv($value2['importe'],'1',2) * 1;
								$_SESSION['caja']['cargos']['impuestosFactura'][$key2][$value2['tasa']] = bcdiv($value2['importe'],'1',2) * 1;
								$_SESSION['caja']['cargos']['impuestosPdf'][$key2][$value2['tasa']]['Valor'] = bcdiv($value2['importe'],'1',2) * 1;
								$retenidos = $value2['importe'];
								$xmlLoc.="<implocal:RetencionesLocales ImpLocRetenido='".$value2['nombre']."' TasadeRetencion='".$value2['tasa']."' Importe='".bcdiv($value2['importe'],'1',2) ."' />";
								$chain.= '|'.$value2['nombre'].'|'.$value2['tasa'].'|'.bcdiv($value2['importe'],'1',2);
							}
						}
					}
				}
				$tieneLocales = bcdiv($retenidos,'1',2) + bcdiv($traslados,'1',2);
				if($tieneLocales > 0){
					$azurian['CompleImpues']['xmlComple']="<implocal:ImpuestosLocales xmlns:implocal='http://www.sat.gob.mx/implocal' xsi:schemaLocation='http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd' version='1.0' TotaldeRetenciones='".bcdiv($retenidos,'1',2)."' TotaldeTraslados='".bcdiv($traslados,'1',2)."'>".$xmlLoc."</implocal:ImpuestosLocales>";
					$azurian['CompleImpues']['cadenaComple'] = '|1.0|'.bcdiv($retenidos,'1',2).'|'.bcdiv($traslados,'1',2).$chain;
					$azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2) - bcdiv($retenidos,'1',2);
					$azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2) + bcdiv($traslados,'1',2);
					$azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2);
				}

		//print_r($azurian);exit();//
		//echo json_encode($azurian);

		unset($_SESSION['pagos-caja']);
		unset($_SESSION['caja']);

		
		//require_once('../../modulos/lib/nusoap.php');
		//require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/funcionesSAT40.php');
				else require_once('../webapp/modulos/SAT/funcionesSAT2_api.php');
			}
			else require $api_lite . "modulos/SAT/funcionesSAT2_api.php";
		}else if($pac==1){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])){
					require_once('../../modulos/lib/nusoap.php');
					require_once('../../modulos/SAT/funcionesSAT40.php');
				}
				else{
					require_once('../webapp/modulos/lib/nusoap.php');
					require_once('../webapp/modulos/SAT/funcionesSAT_api.php');
				}
			}
			else{
				require_once($api_lite . 'modulos/lib/nusoap.php');
				require_once($api_lite . 'modulos/SAT/funcionesSAT_api.php');
			}
		}
		if(isset($_REQUEST["netwarstore"]) || isset($api_lite)) return $JSON;


	}
	public function pendienteFactura33($azurian, $cliente, $obser,$seriex,$mpCat,$usoCfdi,$tipoRelacionCfdi,$cfdiUuidRelacion){
		date_default_timezone_set("Mexico/General");
		$fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-10 minute"));

			////Busca el pack para facturar
			$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
			$respac = $this->queryArray($qrpac);
			$pac = $respac["rows"][0]["pac"];


			$queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['RFC'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				/*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp; */
				//$pathdc = '../../..';
				$pathdc = '../../modulos/SAT/cliente';
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;

			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				echo json_encode($JSON);
				exit();
			}

					/* Datos Receptor
		============================================================== */
		if($cliente>0){
		  //$result = $this->conexion->consultar("SELECT * FROM comun_facturacion WHERE id='$rrfc';");
		  $result = "SELECT c.nombre,c.id, c.rfc, c.razon_social, c.correo, c.pais, c.regimen_fiscal, c.domicilio, c.num_ext, c.cp, c.colonia, e.estado, c.ciudad, c.municipio from comun_facturacion c , estados e WHERE e.idestado=c.estado and id='".$cliente."'";
		  $rs = $this->queryArray($result);
		  //print_r($rs);
		  $idCliente=$rs['rows'][0]['nombre'];
		  $azurian['Receptor']['Rfc']=strtoupper($rs['rows'][0]['rfc']);
		  $azurian['Receptor']['Nombre']=strtoupper($rs['rows'][0]['razon_social']);
		  $azurian['Receptor']['UsoCFDI'] = 'G01';
		  $azurian['Correo']['Correo']  = $rs['rows'][0]['correo'];

		}else{
		  $idCliente='';
		  $azurian['Receptor']['Rfc']='XAXX010101000';
		  $azurian['Receptor']['Nombre']='Factura generica';
		  $azurian['Receptor']['UsoCFDI'] = 'G01';
		  $azurian['Correo']['Correo'] = '';
		}

		/* --- Configuracion de las series  ---*/
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);

		if($resSer['rows'][0]['seriesFactura']==1){
			$result3 ="SELECT * FROM pvt_serie_folio WHERE id=".$seriex;
		}else{
			$result3 ="SELECT * FROM pvt_serie_folio WHERE id=1";
		}
		$rs3 = $this->queryArray($result3);

		$result4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
		$rs4 = $this->queryArray($result4);

		$azurian['org']['logo']        = $rs4['rows'][0]['logoempresa'];


		///Clavel del uso de CFDI
		$seluso = 'SELECT c_usocfdi from c_usocfdi where id='.$usoCfdi;
		//echo $seluso;
		$resuso = $this->queryArray($seluso);
		$claveUsoCfdi = $resuso['rows'][0]['c_usocfdi'];
		//echo 'uso='.$claveUsoCfdi;
		$azurian['Receptor']['UsoCFDI'] = $claveUsoCfdi;

		////Metodo de Pago
		$selMeto = "SELECT clave from c_metododepago where id=".$mpCat;
		//echo $selMeto;
		$resMeto = $this->queryArray($selMeto);
		$payMet = $resMeto['rows'][0]['clave'];
		//echo '<br>Metodo='.$payMet;
		$azurian['Basicos']['MetodoPago']  = $payMet;
		/* UUID relacionados
		============================================================== */
		//$relacion,$uuidRelacion;
		$relacionXML = '';
		$relacionCAD = '';
		if($tipoRelacionCfdi!='0'){
			$relacionCAD .= $relacion.'|';
			//$relacionXML .='<cfdi:CfdiRelacionados TipoRelacion="'.$relacion.'">';
			$relacionXML .="<cfdi:CfdiRelacionados TipoRelacion='".$relacion."'>";
			$rel = explode(';', $uuidRelacion);
			foreach ($rel as $key => $value) {
				if($value!=''){
					$relacionCAD .= $value.'|';
			 		//$relacionXML.='<cfdi:CfdiRelacionado UUID="'.$value.'"/>';
			 		$relacionXML.="<cfdi:CfdiRelacionado UUID='".$value."'/>";
				}
			 }
			 $relacionXML.='</cfdi:CfdiRelacionados>';
		}
		$azurian['Realacionados']['xml'] = $relacionXML;
		$azurian['Realacionados']['cadena'] = $relacionCAD;


		/* Datos serie y folio
		============================================================== */
		$azurian['Basicos']['Serie']=$rs3['rows'][0]['serie']; //No obligatorio
		$azurian['Basicos']['Folio']=$rs3['rows'][0]['folio'];

		/* Datos Emisor
		============================================================== */
		$azurian['Emisor']['RegimenFiscal'] = $parametros['EmisorTimbre']['RegimenFiscal'];
		$azurian['Emisor']['Nombre']=strtoupper($parametros['EmisorTimbre']['RazonSocial']);
		$azurian['Emisor']['Rfc']=strtoupper($parametros['EmisorTimbre']['RFC']);

		/* Fecha Factura
		============================================================== */
		$azurian['Basicos']['Fecha']=$fecha;

		/* Impuestos
		============================================================== */
		$tisr=$azurian['Impuestos']['totalImpuestosRetenidos'];
		$tiva=$azurian['Impuestos']['totalImpuestosTrasladados'];
		$tieps=$azurian['Impuestos']['totalImpuestosIeps'];



		//print_r($azurian);
		//exit();

		$soloExento = 0;
		  	foreach ($azurian['nn']['nn'] as $clave => $imm) {
				if ($clave != 'IVAE') {
					$soloExento = 1;

				}
			}
		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}


	}
	public function modificaPromo($idProducto){
			////Promociones, Verifica si tiene foodware, si tiene fodware no entroa.
		$promoIdProducto = $idProducto;
		$xidPoducto = explode('_', $idProducto);
		$idProductoSinca = $xidPoducto[0];
		$carac = $xidPoducto[1];

			$selCom = "SELECT idmenu from accelog_perfiles_me where idmenu=2156";
			$resCom = $this->queryArray($selCom);

			if($resCom['total'] == 0){

				date_default_timezone_set("Mexico/General");
				$hora = date("H:i");
				$hoy = getdate();
				$promQuery = 'SELECT pr.* from com_promociones pr, com_promocionesXproductos prp where pr.status=1 and prp.status=1 and pr.id=prp.id_promocion and prp.id_producto='.$idProductoSinca;
				$reProm = $this->queryArray($promQuery);

				$inicio = $reProm['rows'][0]['inicio'];
				$fin = $reProm['rows'][0]['fin'];

				if(preg_match('/['.$hoy['wday'].']+/', $reProm['rows'][0]['dias'])){
					if(($hora > $inicio)&&($hora < $fin)){
						if($reProm['rows'][0]['tipo']==1){
							///Descuento
							$xer = $this->cambiaCantidad($idProducto,$reProm['rows'][0]['descuento'], '%','',1);

						}else{
							///2x1
							//echo 'Promociones='.floor($_SESSION['caja'][$idProdCar]->cantidad / $reProm['rows'][0]['cantidad']);
							//echo '<br>Producto sobrante='.($_SESSION['caja'][$idProdCar]->cantidad % $reProm['rows'][0]['cantidad']);
							$promociones = floor($_SESSION['caja'][$idProducto]->cantidad / $reProm['rows'][0]['cantidad']);
							$sobrantes = ($_SESSION['caja'][$idProducto]->cantidad % $reProm['rows'][0]['cantidad']);
							//if($promociones > 1){
								$xer2 = $this->promocionNxN($promociones,$sobrantes,$idProducto);
							//}

						}
					}else{
						//echo 'esta fuera de promo';
					}
				}
			}

	}

	public function sucUsus(){
		$sel = "select * from mrp_sucursal";
		$res = $this->queryArray($sel);

		$sel2 = "SELECT * from accelog_usuarios";
		$res2 = $this->queryArray($sel2);

		return  array('suc' =>$res['rows'] ,'usu' => $res2['rows']);
	}

	public function puedeDevolverCancelar() {
		$sql = "SELECT	activar_dev_can
				FROM	app_config_ventas;";
		$res = $this->queryArray($sql);
		return !($res['rows'][0]['activar_dev_can']);
	}

	public function autorizacionDevolverCancelar($pass) {
		$sql = "SELECT	password
				FROM	app_config_ventas;";
		$res = $this->queryArray($sql);
		return ($res['rows'][0]['password'] == $pass);
	}
	public function facVentas($id_respFact){
		$sel = 'SELECT id_sale from app_pendienteFactura where id_respFact ='.$id_respFact.';';
		$res = $this->queryArray($sel);
		//print_r($res);
		return  array('ventas' => $res['rows']);
	}
	public function ventasFact($id_sale){
		/*$sel = 'SELECT p.id_respFact, r.cadenaOriginal from app_pendienteFactura p, app_respuestaFacturacion r where p.id_respFact=r.id and id_sale='.$id_sale.';'; */
		$sel = 'SELECT factura from app_pos_venta where idVenta='.$id_sale.';';
		$res = $this->queryArray($sel);
		//print_r($res);
		return $res['rows']['0']['cadenaOriginal'];
	}
	public function ventasFact2($id_sale){
		$sel = 'SELECT p.id_respFact, r.cadenaOriginal from app_pendienteFactura p, app_respuestaFacturacion r where p.id_respFact=r.id and id_sale='.$id_sale.';';
		$res = $this->queryArray($sel);
		//print_r($res);
		if($res['total']>0){
			return  array('estatus'=> true,'cade' => $res['rows']['0']['cadenaOriginal'] );
		}else{
			return  array('estatus'=> false);
		}

	}

	public function comprobantesPago($idFact,$xmlDocRelaciondos,$cadoriDocRelaciondos,$cliprov){


		// Receptor
		//===============================================================

		$parametros['Receptor'] = array();
		if ($idFact == 0) {

			$parametros['Receptor']['RFC'] = "XAXX010101000";
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;
		} else {
			$df = (object) $this->datosFacturacion2($idFact,$cliprov);
			$parametros['Receptor']['RFC'] = $df->rfc;
			$parametros['Receptor']['RazonSocial'] = utf8_decode($df->razon_social);
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;
			/*$parametros['Receptor']['Pais'] = utf8_decode($df->pais);
			$parametros['Receptor']['Calle'] = utf8_decode($df->domicilio);
			$parametros['Receptor']['NumExt'] = $df->num_ext;
			$parametros['Receptor']['Colonia'] = utf8_decode($df->colonia);
			$parametros['Receptor']['Municipio'] = utf8_decode($df->municipio);
			$parametros['Receptor']['Ciudad'] = utf8_decode($df->ciudad);
			$parametros['Receptor']['CP'] = $df->cp;
			$parametros['Receptor']['Estado'] = utf8_decode($df->estado); */
			$parametros['Receptor']['Email'] = $df->correo;
		}
		/* FACTURACION AZURIAN
		============================================================== */
		global $api_lite;
		if(!isset($api_lite)){
			if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/config.php');
			else require_once('../webapp/modulos/SAT/config.php');
		}
		else require $api_lite . "modulos/SAT/config.php";

		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d') . 'T' . date('H:i:s', strtotime("-10 minute"));


		$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$logo = $this->queryArray($logo);
		$r3 = $logo["rows"][0];

		$azurian = array();
		//echo $bloqueo.'??';

			//echo 'entro a bloqueo';
			$queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Rfc'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				/*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp; */
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				global $api_lite;
				if(!isset($api_lite)) echo json_encode($JSON);
				else return $JSON;
				exit();
			}

		/* Folio y Serie */
		$sel = "SELECT serie_cp, folio_cp from pvt_serie_folio where id=1";
		$resSF = $this->queryArray($sel);



		/* Observaciones pdf */
		$azurian['Observacion']['Observacion'] = $mensaje;

		  /* CORREO RECEPTOR
		============================================================== */
		$azurian['Correo']['Correo'] = $Email;
		/* Lugar de Expedicion
		============================================================== */
		$sels = "SELECT s.cp from mrp_sucursal s, administracion_usuarios au where au.idSuc=s.idSuc and au.idempleado=". $_SESSION['accelog_idempleado'];
		$lugE = $this->queryArray($sels);
		/* Datos Basicos
		============================================================== */
		//$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		$azurian['Basicos']['Moneda'] = 'XXX';
		if($parametros['DatosCFD']['Moneda']!='MXN'){
			$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		}
		$azurian['Basicos']['MetodoPago'] = '';
		$azurian['Basicos']['NumCtaPago'] = '';
		$azurian['Basicos']['LugarExpedicion'] = $lugE['rows'][0]['cp'];
		$azurian['Basicos']['Version'] = '3.3';
		$azurian['Basicos']['Serie'] = $resSF['rows'][0]['serie_cp']; //No obligatorio
		$azurian['Basicos']['Folio'] = $resSF['rows'][0]['folio_cp']; //No obligatorio
		$azurian['Basicos']['Fecha'] = $fecha;
		$azurian['Basicos']['Sello'] = '';
		$azurian['Basicos']['FormaPago'] ='';
		$azurian['Basicos']['TipoDeComprobante'] = 'P';
		$azurian['tipoFactura'] = 'comprobante';
		$azurian['Basicos']['NoCertificado'] = '';
		$azurian['Basicos']['Certificado'] = '';
		$azurian['Basicos']['SubTotal'] ='0';
		$azurian['Basicos']['Total'] = '0';

		/*============================================================== */
		/* Datos Emisor
		============================================================== */
		$azurian['Emisor']['RegimenFiscal'] = strtoupper($parametros['EmisorTimbre']['RegimenFiscal']);
		$azurian['Emisor']['Rfc'] = strtoupper($parametros['EmisorTimbre']['Rfc']);
		$azurian['Emisor']['Nombre'] = strtoupper($parametros['EmisorTimbre']['RazonSocial']);

		/* Datos Fiscales Emisor
		============================================================== */

		/*$azurian['FiscalesEmisor']['calle'] = $parametros['EmisorTimbre']['Calle'];
		$azurian['FiscalesEmisor']['noExterior'] = $parametros['EmisorTimbre']['NumExt'];
		$azurian['FiscalesEmisor']['colonia'] = $parametros['EmisorTimbre']['Colonia'];
		$azurian['FiscalesEmisor']['localidad'] = $parametros['EmisorTimbre']['Ciudad'];
		$azurian['FiscalesEmisor']['municipio'] = $parametros['EmisorTimbre']['Municipio'];
		$azurian['FiscalesEmisor']['estado'] = $parametros['EmisorTimbre']['Estado'];
		$azurian['FiscalesEmisor']['pais'] = $parametros['EmisorTimbre']['Pais'];
		$azurian['FiscalesEmisor']['codigoPostal'] = $parametros['EmisorTimbre']['CP']; */
		/* Datos Receptor
		============================================================== */

		$azurian['Receptor']['Rfc'] = strtoupper($parametros['Receptor']['RFC']);
		$azurian['Receptor']['Nombre'] = strtoupper($parametros['Receptor']['RazonSocial']);
		$azurian['Receptor']['UsoCFDI'] ='P01';

		$conceptosOri = '';
		$conceptos = '';
		$conceptosx = '';
		$conceptosxR = '';
		$conceptosR = '';
		$ivasConceptos = 0;
		$iepsConceptos = 0;
		$conceptosOriR = '';

		$sucursal = $this -> sucursal();

		///Se llena el concepto de pago y eligue alguna clave configurada
		$selec = "SELECT cat.clave from c_consumo cat , com_configuracion f where f.id_consumo_clave = cat.id and f.id_sucursal = ".$sucursal;
		$reCla  = $this->queryArray($selec);
				$conceptosDatos[0]["ClaveProdServ"] = '84111506';
				$conceptosDatos[0]["ClaveUnidad"] = 'ACT';
				$conceptosDatos[0]["Cantidad"] = 1;
				//$conceptosDatos[0]["Unidad"] = 'zz';
				//$conceptosDatos[0]["NoIdentificacion"] = '';
				$conceptosDatos[0]["ValorUnitario"] = '0';
				$conceptosDatos[0]["Descripcion"] = "Pago";
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[0]['Importe'] = '0';
				//$conceptosDatos[0]['cargos33']->IVA = array('tasa' =>16 ,'importe' => $elIva);
				//$consumoTotal =  $precioSiniva*1;

		//print_r($azurian);
		foreach ($conceptosDatos as $key => $value) {
			/*$value['Descripcion'] = preg_replace("/'/", "&apos;", $value['Descripcion']);
			$value['Descripcion'] = preg_replace('/"/', "&quot;", $value['Descripcion']);
		   // $value['Descripcion'] = preg_replace('("|\')', "&apos;", $value['Descripcion']);
			$value['Descripcion'] = eregi_replace("[\n|\r|\n\r]", " ", $value['Descripcion']);
			$value['Descripcion'] = trim($value['Descripcion']); */

			$conceptosOri.='|'.$value['ClaveProdServ'].'|';
			//$conceptosOri.=$value['NoIdentificacion'] . '|';
			$conceptosOri.=$value['Cantidad'] . '|';
			$conceptosOri.=$value['ClaveUnidad'] . '|';
			//$conceptosOri.=$value['Unidad'] . '|';
			$conceptosOri.=$value['Descripcion'] . '|';
			$conceptosOri.=$value['ValorUnitario']. '|';
			$conceptosOri.=$value['Importe']. '|';
			$des = '';

			$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' Cantidad='" . $value['Cantidad'] . "' ClaveUnidad='".$value['ClaveUnidad']."' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" .$value['ValorUnitario']. "' Importe='" . $value['Importe'] . "'".$des." />";

		}

		$azurian['Conceptos']['conceptos'] = $conceptos;
		$azurian['Conceptos']['conceptosOri'] = $conceptosOri;


		  $azurian['Impuestos']['isr'] = '';
		  $azurian['Impuestos']['iva'] = '';
		  $azurian['Impuestos']['totalImpuestosRetenidos'] = '';
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = '';




		////Azurian complemntos de pago
		$pagoXML ="<pago10:Pagos xmlns:pago10='http://www.sat.gob.mx/Pagos' Version='1.0' xsi:schemaLocation='http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd'>".$xmlDocRelaciondos."</pago10:Pagos>";


		$azurian['ComprobantePago']['xml'] = $pagoXML;
		$azurian['ComprobantePago']['cadena'] = $cadoriDocRelaciondos;


		///Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];
		///Bandera para saber que es un comprobate de pago
		$comPago = 1;

		if($pac==2){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/funcionesSAT33.php');
				else require_once('../webapp/modulos/SAT/funcionesSAT2_api.php');
			}
			else require $api_lite . "modulos/SAT/funcionesSAT2_api.php";
		}else if($pac==1){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])){
					require_once('../../modulos/lib/nusoap.php');
					require_once('../../modulos/SAT/funcionesSAT33.php');
				}
				else{
					require_once('../webapp/modulos/lib/nusoap.php');
					require_once('../webapp/modulos/SAT/funcionesSAT_api.php');
				}
			}
			else{
				require_once($api_lite . 'modulos/lib/nusoap.php');
				require_once($api_lite . 'modulos/SAT/funcionesSAT_api.php');
			}
		}
		if(isset($_REQUEST["netwarstore"]) || isset($api_lite)) return $JSON;






	}

	public function gridFacturasComplementosPago($limit){
		//$query = "SELECT * from app_respuestaFacturacion ORDER BY id DESC ".$limit;
		$query = "SELECT	'P' tipoDeComprobante, CONCAT(f.serie, '-', f.folio) serieFolio, f.fecha fechaTimbrado, f.rfc rfcReceptor, f.receptor cliente, f.moneda moneda, f.uuid uuidComplemento, fr.uuid_relacionado uuidRelacionado, fr.imp_saldo_ant saldoAnterior, fr.imp_pagado importePagado, fr.imp_saldo_insoluto saldoInsoluto, fr.parcialidades parcialidad, u.usuario empleado, s.nombre sucursal, cancelada estatus, rf.cadenaOriginal
				FROM	cont_facturas f
				LEFT JOIN cont_facturas_relacion fr ON f.uuid = fr.uuid_pago
				LEFT JOIN app_respuestaFacturacion rf ON fr.uuid_relacionado = rf.folio COLLATE utf8_unicode_ci
				LEFT JOIN app_pos_venta v ON rf.idSale = v.idVenta
				LEFT JOIN mrp_sucursal s ON v.idSucursal = s.idSuc
				LEFT JOIN accelog_usuarios u ON  v.idEmpleado = u.idempleado
				WHERE	json LIKE '%TipoDeComprobante\":\"P\"%' ".$limit;
		//echo $query;
		$res = $this->queryArray($query);
		//print_r($res);
		return $res['rows'];
	}


	function muestraMasFactComplementosPago($limit){

		$query = "SELECT	'P' tipoDeComprobante, CONCAT(f.serie, '-', f.folio) serieFolio, f.fecha fechaTimbrado, f.rfc rfcReceptor, f.receptor cliente, f.moneda moneda, f.uuid uuidComplemento, fr.uuid_relacionado uuidRelacionado, fr.imp_saldo_ant saldoAnterior, fr.imp_pagado importePagado, fr.imp_saldo_insoluto saldoInsoluto, fr.parcialidades parcialidad, u.usuario empleado, s.nombre sucursal, cancelada estatus, rf.cadenaOriginal
				FROM	cont_facturas f
				INNER JOIN cont_facturas_relacion fr ON f.uuid = fr.uuid_pago
				INNER JOIN app_respuestaFacturacion rf ON fr.uuid_relacionado = rf.folio COLLATE utf8_unicode_ci
				INNER JOIN app_pos_venta v ON rf.idSale = v.idVenta
				LEFT JOIN mrp_sucursal s ON v.idSucursal = s.idSuc
				LEFT JOIN accelog_usuarios u ON  v.idEmpleado = u.idempleado
				WHERE	json LIKE '%TipoDeComprobante\":\"P\"%' AND f.origen = '3'".$limit;
		//echo $query;
		$resSel = $this->queryArray($query);
		//print_r($res);


/*		foreach ($resSel['rows'] as $key => $value) {
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}*/

		return $resSel['rows'];
	}

	public function buscarFacturasComPag($desde,$hasta,$empleado,$sucursal){
		$filtro = "WHERE	json LIKE '%TipoDeComprobante\":\"P\"%' AND f.origen = '3'";
		$inicio = $desde;
		$fin = $hasta;

		if($fin!="")
		{
			list($a,$m,$d)=explode("-",$fin);
			$fin=$a."-".$m."-".((int)$d+1);
		}


		if($inicio!="" && $fin=="")
		{
			$filtro.=" and  f.fecha >= '".$inicio."' ";
		}
		if($fin!="" && $inicio=="")
		{
			$filtro.=" and  f.fecha <= '".$fin."' ";
		}
		if($inicio!="" && $fin!="")
		{
			$filtro.=" and  f.fecha <= '".$fin."' and   f.fecha >= '".$inicio."' ";
		}
/*
		if($tipo == 1){
			//todas
			$filtro.="";
		}
		if($tipo == 2){
			//factura
			$filtro.=" and  rf.tipoComp = 'F' ";
		}
		if($tipo == 3){
			//notas
			$filtro.=" and  rf.tipoComp = 'C' ";
		}
		if($tipo == 4){
			//honorarios
			$filtro.=" and  rf.tipoComp = 'H' ";
		}*/
		if($empleado!=0){
			$filtro .=' and v.idEmpleado='.$empleado;
		}
		if($sucursal!=0){
			$filtro.=' and v.idSucursal='.$sucursal;
		}

		$select = "SELECT	'P' tipoDeComprobante, CONCAT(f.serie, '-', f.folio) serieFolio, f.fecha fechaTimbrado, f.rfc rfcReceptor, f.receptor cliente, f.moneda moneda, f.uuid uuidComplemento, fr.uuid_relacionado uuidRelacionado, fr.imp_saldo_ant saldoAnterior, fr.imp_pagado importePagado, fr.imp_saldo_insoluto saldoInsoluto, fr.parcialidades parcialidad, u.usuario empleado, s.nombre sucursal, cancelada estatus, rf.cadenaOriginal
				FROM	cont_facturas f
				INNER JOIN cont_facturas_relacion fr ON f.uuid = fr.uuid_pago
				INNER JOIN app_respuestaFacturacion rf ON fr.uuid_relacionado = rf.folio COLLATE utf8_unicode_ci
				INNER JOIN app_pos_venta v ON rf.idSale = v.idVenta
				LEFT JOIN mrp_sucursal s ON v.idSucursal = s.idSuc
				LEFT JOIN accelog_usuarios u ON  v.idEmpleado = u.idempleado ".$filtro." ORDER BY uuidComplemento DESC ";
		//echo $select;
		$resSel = $this->queryArray($select);
		//echo $select;
/*		foreach ($resSel['rows'] as $key => $value) {
			//echo $value['cadenaOriginal'].'<br>';
			$x = base64_decode($value['cadenaOriginal']);
			$x = str_replace("\\", "", $x);
			$resSel['rows'][$key]['cadenaOriginal'] = $x;
		}*/

		return $resSel['rows'];
	}
	function usosyFormas(){

	}
	function origenPacComPag($id){
		$selRes = "SELECT	serieCsdEmisor
					FROM	cont_facturas f
					INNER JOIN	cont_facturas_relacion fr ON f.uuid = fr.uuid_pago
					INNER JOIN	app_respuestaFacturacion rf ON fr.uuid_relacionado COLLATE utf8_unicode_ci = rf.folio
					WHERE f.uuid = '".$id."'";
		$res = $this->queryArray($selRes);

		if($res['rows'][0]['serieCsdEmisor']=='3'){
			return array('pac' => 'formas' );
		}else{
			return array('pac' => 'azurian');
		}
	}

	function logoOrganizacion($id){
		$selRes = "SELECT logoempresa as logo FROM organizaciones;";
		$res = $this->queryArray($selRes);

		return array('logo' => $res['rows'][0]['logo']);
	}




	/////////////////////////////////////////////////////////
	function consumo(){

		$sucursal = $this -> sucursal();

		$sql = "SELECT consumo from com_configuracion where id_sucursal = ".$sucursal;
		$result = $this->queryArray($sql);
		$consumo = $result['rows'][0]['consumo'];
		if($consumo == 1 || $consumo=='1'){
			return 1;
		}else{
			return 0;
		}
	}
	/////////////////////////////////////////////////////////

	function fecha_fin($id_comanda) {
		$sql = "SELECT
					fin
				FROM
					com_comandas
				WHERE
					id = " . $id_comanda;
		$fecha = $this -> queryArray($sql);

		return $fecha["rows"][0]["fin"];
	}

	function fecha_inicio($id_comanda) {
		$sql = "SELECT
					timestamp
				FROM
					com_comandas
				WHERE
					id = " . $id_comanda;
		$fecha = $this -> queryArray($sql);

		return $fecha["rows"][0]["timestamp"];
	}

	public function obtenerFormaPagoBase($idFormapago){
		$sql = "SELECT	idFormapago
				FROM	view_forma_pago
				WHERE	claveSat = (SELECT claveSat FROM view_forma_pago WHERE idFormapago='$idFormapago')
				ORDER BY idFormapago
				LIMIT	1";

		$res =$this->queryArray($sql);

		return  $res['rows'][0];
	}
		public function crearNota33($monto,$montosiniva,$iva,$total,$idFact,$idFm,$concepto,$refe,$usoCfdi,$mpCat,$relacion,$uuidRelacion,$clientecaja,$retenido,$rfcxx){

			//echo $relacion.'--'.$uuidRelacion;
			//tipoRelacionCfdiexit();
          $montosiniva=str_replace(',', '', number_format($montosiniva,2));
          $iva=str_replace(',','',number_format($iva,2));
          $total=str_replace(',','',number_format($total,2));
          $ivaRetenido = $montosiniva * 10.6667 /100;

          $montosiniva = $total / 1.12 ;
          $iva = $montosiniva * 0.16;
          $ivaRetenido = $montosiniva * 0.04;

          //echo 'SubTotal='.$montosiniva.'/IVA='.$iva.'/IVAR='.$ivaRetenido.'/Total='.$total;
          //exit();
		$monto = str_replace(",", "", $total);
		$impuestos = 0;

		$arraytmp = (object) $_SESSION['caja'];
		foreach ($arraytmp as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden') {
				$impuestos = 0;
				foreach ($producto->cargos as $key2 => $value2) {
					$impuestos += $value2;
				}
			}
		}


		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
		$folios = "SELECT serie_nc,folio_nc FROM pvt_serie_folio LIMIT 1";
		$data = $this->queryArray($folios);
		if ($data["total"] > 0) {
			$data = $data["rows"][0];
		}
			$serie = $data['serie_nc'];
			$folio = $data['folio_nc'];
			///Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];
		// Receptor
		//===============================================================
		$seluso = 'SELECT c_usocfdi from c_usocfdi where id='.$usoCfdi;
		//echo $seluso;
		$resuso = $this->queryArray($seluso);
		$claveUsoCfdi = $resuso['rows'][0]['c_usocfdi'];

		$parametros['Receptor'] = array();
		//echo '&&'.$idFact;
		//if ($idFact == 0) {

			//$parametros['Receptor']['RFC'] = "XAXX010101000";
		//} else {
			$df = (object) $this->datosFacturacion3($rfcxx,1);
			if($df->rfc == ''){
				$parametros['Receptor']['RFC'] = 'XAXX010101000';
			}else{
				//$parametros['Receptor']['RFC'] = $df->rfc;
				$parametros['Receptor']['RFC'] = $df->rfc;

			}
			if($df->razon_social==''){
				$df->razon_social = 'Factura Generica';
			}
			$parametros['Receptor']['RazonSocial'] = utf8_decode($df->razon_social);
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;

			$parametros['Receptor']['Email1'] = $df->correo;
		//}
		//Obteniendo la descripcion de la forma de pago
		$formapago = "";
		$queryFormaPago = " SELECT nombre,referencia,claveSat from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;
		//echo $queryFormaPago;
		$resultqueryFormaPago = $this->queryArray($queryFormaPago);
		foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
			if (strlen($pagosValue["referencia"]) > 0) {
				//$formapago .= $pagosValue['claveSat'] . " Ref:" . $pagosValue['referencia'] . ",";
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = $pagosValue['referencia'];
			} else {
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = '';

			}
		}
		$formapago = substr($formapago, 0, strlen($formapago) - 1);
		if ($formapago == "") {
			$formapago = ".";
		}
		//echo 'Forma de pago='.$formapago;
		//exit();
		$Email = $df->correo;
		///Moneda
		$select = "SELECT * from cont_coin where coin_id=".$moneda;
		$resCon = $this->queryArray($select);
		////Metodo de Pago
		$selMeto = "SELECT clave from c_metododepago where id=".$mpCat;
		//echo $selMeto;
		$resMeto = $this->queryArray($selMeto);
		$payMet = $resMeto['rows'][0]['clave'];


		///Lugar de expedicion
		//$lugExpe = 'SELECT cp from pvt_configura_facturacion';
		/*$lugExp = 'SELECT cp from organizaciones';
		$lugExpeRes = $this->queryArray($lugExp); */
		/* Lugar de Expedicion
		============================================================== */
		$lugExp = "SELECT s.cp from mrp_sucursal s, administracion_usuarios au where au.idSuc=s.idSuc and au.idempleado=". $_SESSION['accelog_idempleado'];
		$lugExpeRes = $this->queryArray($lugExp);


		$parametros['DatosCFD']['FormaPago'] = utf8_decode($formapago);
		$parametros['DatosCFD']['MetodoPago'] = $payMet;
		$parametros['DatosCFD']['NumCtaPago'] = $refFormaPago;
		//$parametros['DatosCFD']['TipoCambio'] = $tipoCambio;
		$parametros['DatosCFD']['Moneda'] = 'MXN';
		$parametros['DatosCFD']['Subtotal'] = str_replace(",", "", number_format($montosiniva,2));
		$parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] + $_SESSION['caja']['descGeneral'];
	   // $parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] - 0.01;
		$parametros['DatosCFD']['Total'] = str_replace(",", "", number_format($total,2));

	   // $parametros['DatosCFD']['Total'] = $parametros['DatosCFD']['Total'] - 0.01;
		$parametros['DatosCFD']['Serie'] = $serie;
		$parametros['DatosCFD']['Folio'] = $folio;
		$parametros['DatosCFD']['TipoDeComprobante'] = "I"; //F o C
		$parametros['DatosCFD']['MensajePDF'] = "";
		$parametros['DatosCFD']['LugarExpedicion'] = $lugExpeRes['rows'][0]['cp'];
		$parametros['DatosCFD']['Descuento'] = $_SESSION['caja']['descGeneral'];

		$nn2 = '';
		$nnf = '';
		/* FACTURACION AZURIAN
		============================================================== */
		if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/config.php');
		else require_once('../webapp/modulos/SAT/config.php');

		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d') . 'T' . date('H:i:s', strtotime("-10 minute"));


		$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$logo = $this->queryArray($logo);
		$r3 = $logo["rows"][0];

		$queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Rfc'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);

				/*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp; */
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				echo json_encode($JSON);
				exit();
			}
					/* Observaciones pdf */
			$azurian['Observacion']['Observacion'] ='';

			unset($nn2);
			unset($nnf);
			$precioSiniva = $parametros['DatosCFD']['Total'] / 1.16;
			//echo $ivaCon;
			$elIva = $precioSiniva * 0.16;
		   // $subTotalCon = $parametros['DatosCFD']['Total'] - $ivaCon;
			$parametros['DatosCFD']['Subtotal'] = $precioSiniva;
			$nn2["IVA"]["16.00"] = $elIva;
			$nnf["IVA"]["16.00"]['Valor'] = $elIva;
			if($retenido == 1){
				$montosiniva = $total / 1.12 ;
          		$iva = $montosiniva * 0.16;
          		$ivaRetenido = $montosiniva * 0.04;



				$precioSiniva = $montosiniva;
				//echo $ivaCon;
				$elIva = $iva;
			   // $subTotalCon = $parametros['DatosCFD']['Total'] - $ivaCon;
				$parametros['DatosCFD']['Subtotal'] = $precioSiniva;
				$nn2["IVAR"]["0.040000"] = $ivaRetenido;
				$nnf["IVAR"]["0.040000"]['Valor'] = $ivaRetenido;
				$nn2["IVA"]["16.00"] = $elIva;
				$nnf["IVA"]["16.00"]['Valor'] = $elIva;
			}
			//print_r($nn2);
			//exit();
			/* IMPUESTOS
			============================================================== */
			if ($nn2 == '') {
				$nn2["IVA"]["0.0"]["Valor"] = 0.00;
			}
			if ($nnf == '') {
				$nnf["IVA"]["0.0"]["Valor"] = 0.00;
			}
			$nn = $nn2;
			$azurian['nn']['nn'] = $nn;
			$azurian['nnf']['nnf'] = $nnf;
			$azurian['org']['logo'] = $r3["logoempresa"];

			/* CORREO RECEPTOR

			============================================================== */
			$azurian['Correo']['Correo'] = $Email;
			/* FORMa de PAgo */
			$forpabusca = $this->buscaFormaPago($uuidRelacion);

			/* Datos Basicos
			============================================================== */
			//$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
			$azurian['Basicos']['Moneda'] = $parametros['DatosCFD']['Moneda'];
			$azurian['Basicos']['MetodoPago'] = $parametros['DatosCFD']['MetodoPago'];
			$azurian['Basicos']['NumCtaPago'] = $parametros['DatosCFD']['NumCtaPago'];
			$azurian['Basicos']['LugarExpedicion'] = $parametros['DatosCFD']['LugarExpedicion'];
			$azurian['Basicos']['Version'] = '3.3';
			$azurian['Basicos']['Serie'] = $parametros['DatosCFD']['Serie']; //No obligatorio
			$azurian['Basicos']['Folio'] = $parametros['DatosCFD']['Folio']; //No obligatorio
			$azurian['Basicos']['Fecha'] = $fecha;
			$azurian['Basicos']['Sello'] = '';
			$azurian['Basicos']['FormaPago'] = $forpabusca;
			$azurian['Basicos']['TipoDeComprobante'] = 'E';
			$azurian['tipoFactura'] = 'factura';
			$azurian['Basicos']['NoCertificado'] = '';
			$azurian['Basicos']['Certificado'] = '';
			$str_subtotal = number_format($parametros['DatosCFD']['Subtotal'], 2);
			$azurian['Basicos']['SubTotal'] = str_replace(",", "", $str_subtotal);
			if($descGen > 0){
				$azurian['Basicos']['Descuento'] = str_replace(",", '', number_format($descGen, 2));
			}
			//$azurian['Basicos']['Descuento'] = number_format($parametros['DatosCFD']['Descuento'],2);
			//$azurian['Basicos']['Descuento'] = str_replace(',', '', $azurian['Basicos']['Descuento']);
			$str_total = number_format($parametros['DatosCFD']['Total'], 2);
			$str_total = str_replace(',', '',$str_total);
			//$str_total = $str_total - 0.01;
			//$str_total = number_format($str_total,0).'.00';  //Comente para que Salgan Decimales Normalmente
			$str_total = number_format($str_total,2);
			$azurian['Basicos']['Total'] = str_replace(",", "", $str_total);


			/* UUID relacionados
			============================================================== */
			//$relacion,$uuidRelacion;
			$relacionXML = '';
			$relacionCAD = '';
			if($relacion!='0'){
				$relacionCAD .= $relacion.'|';
				$relacionXML .='<cfdi:CfdiRelacionados TipoRelacion="'.$relacion.'">';
				$rel = explode(';', $uuidRelacion);
				foreach ($rel as $key => $value) {
					if($value!=''){
						$relacionCAD .= $value.'|';
				 		$relacionXML.='<cfdi:CfdiRelacionado UUID="'.$value.'"/>';
					}
				 }
				 $relacionXML.='</cfdi:CfdiRelacionados>';
			}
			$azurian['Realacionados']['xml'] = $relacionXML;
			$azurian['Realacionados']['cadena'] = $relacionCAD;
			/*============================================================== */
			/* Datos Emisor
			============================================================== */
			$azurian['Emisor']['RegimenFiscal'] = strtoupper($parametros['EmisorTimbre']['RegimenFiscal']);
			$azurian['Emisor']['Rfc'] = strtoupper($parametros['EmisorTimbre']['Rfc']);
			$azurian['Emisor']['Nombre'] = strtoupper($parametros['EmisorTimbre']['RazonSocial']);

			/* Datos Fiscales Emisor
			============================================================== */

			/*$azurian['FiscalesEmisor']['calle'] = $parametros['EmisorTimbre']['Calle'];
			$azurian['FiscalesEmisor']['noExterior'] = $parametros['EmisorTimbre']['NumExt'];
			$azurian['FiscalesEmisor']['colonia'] = $parametros['EmisorTimbre']['Colonia'];
			$azurian['FiscalesEmisor']['localidad'] = $parametros['EmisorTimbre']['Ciudad'];
			$azurian['FiscalesEmisor']['municipio'] = $parametros['EmisorTimbre']['Municipio'];
			$azurian['FiscalesEmisor']['estado'] = $parametros['EmisorTimbre']['Estado'];
			$azurian['FiscalesEmisor']['pais'] = $parametros['EmisorTimbre']['Pais'];
			$azurian['FiscalesEmisor']['codigoPostal'] = $parametros['EmisorTimbre']['CP']; */
			/* Datos Receptor
			============================================================== */

			$azurian['Receptor']['Rfc'] = strtoupper($parametros['Receptor']['RFC']);
			$azurian['Receptor']['Nombre'] = strtoupper($parametros['Receptor']['RazonSocial']);
			$azurian['Receptor']['UsoCFDI'] = $parametros['Receptor']['UsoCFDI'];

			$conceptosOri = '';
			$conceptos = '';
			/*-- conceptos --*/
			$sclCla = 'SELECT clave_sat from app_productos where id=1622';
			$resClavS = $this->queryArray($sclCla);

				unset($conceptosDatos);
				//$conceptosDatos[0]["ClaveProdServ"] = '01010101';
				if($resClavS['rows'][0]['clave_sat']==''){
					$resClavS['rows'][0]['clave_sat'] = '90121502';
				}
				$conceptosDatos[0]["ClaveProdServ"] = '84111506';
				$conceptosDatos[0]["ClaveUnidad"] = 'ACT';
				$conceptosDatos[0]["Cantidad"] = 1;
				$conceptosDatos[0]["Unidad"] = 'Actividad';
				$conceptosDatos[0]["NoIdentificacion"] = '';
				$conceptosDatos[0]["ValorUnitario"] = $precioSiniva;
				$conceptosDatos[0]["Descripcion"] = $concepto;
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[0]['Importe'] = $precioSiniva;
				$conceptosDatos[0]['cargos33']->IVA = array('tasa' =>16 ,'importe' => $elIva);
				if($retenido==1){
					$conceptosDatos[0]['cargos33']->IVAR = array('tasa' =>0.040000 ,'importe' => $ivaRetenido);
				}

				$consumoTotal =  $precioSiniva*1;

/*============================================================== */
		//se emepiza a llenar los conceptos en el arreglo de azurian
		foreach ($conceptosDatos as $key => $value) {
			$value['Descripcion'] = preg_replace("/'/", "&apos;", $value['Descripcion']);
			$value['Descripcion'] = preg_replace('/"/', "&quot;", $value['Descripcion']);
		   // $value['Descripcion'] = preg_replace('("|\')', "&apos;", $value['Descripcion']);
			$value['Descripcion'] = eregi_replace("[\n|\r|\n\r]", " ", $value['Descripcion']);
			$value['Descripcion'] = trim($value['Descripcion']);
			if($value['Unidad']==''){
				$value['Unidad']= "No Aplica";
			}

			$conceptosOri.='|'.$value['ClaveProdServ'].'|';
			//$conceptosOri.=$value['NoIdentificacion'] . '|';
			$conceptosOri.=$value['Cantidad'] . '|';
			$conceptosOri.=$value['ClaveUnidad'] . '|';
			$conceptosOri.=$value['Unidad'] . '|';
			$conceptosOri.=$value['Descripcion'] . '|';
			if($value['Descuento'] > 0){
				$ValorUni = (str_replace(",", "", number_format($value['Importe'],2))+ str_replace(",", "", number_format($value['Descuento'],2))) / $value['Cantidad'];
				$Impor =  str_replace(",", "", number_format($value['Importe'],2))+ str_replace(",", "", number_format($value['Descuento'],2));

				$conceptosOri.=str_replace(",", "", number_format($ValorUni,2)) . '|';
				$conceptosOri.=str_replace(",", "", number_format($Impor,2)). '|';
				$conceptosOri.=str_replace(",", "", number_format($value['Descuento'],2)). '|';
				$des  = " Descuento='".str_replace(",", "", number_format($value['Descuento'],2))."'";

			}else{
				$ValorUni = str_replace(",", "", number_format($value['ValorUnitario'],2));
				$Impor =  str_replace(",", "", number_format($value['Importe'],2));
				$conceptosOri.=str_replace(",", "", number_format($value['ValorUnitario'],2)) . '|';
				$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)). '|';
				$des = '';
			}



			//$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' ClaveUnidad='".$value['ClaveUnidad']."' Cantidad='" . $value['Cantidad'] . "' Unidad='" . $value['Unidad'] . "' NoIdentificacion='".$value['NoIdentificacion']."' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", number_format($value['ValorUnitario'],2)) . "' Importe='" . str_replace(",", "", number_format($value['Importe'],2)) . "'>";
			$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' ClaveUnidad='".$value['ClaveUnidad']."' Cantidad='" . $value['Cantidad'] . "' Unidad='" . $value['Unidad'] . "' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", number_format($ValorUni,2)) . "' Importe='" . str_replace(",", "", number_format($Impor,2)) . "'".$des.">";
			$conceptos.="<cfdi:Impuestos>";
			//echo '---------------------------------------------------------------------';
			//print_r($value['cargos33']);
			//echo '---------------------------------------------------------------------';
			if($value['cargos33']==''){
				$value['cargos33']->IVA = array('tasa' =>'00' ,'importe' => '0.00');
			}
			foreach ($value['cargos33'] as $kim => $valim) {
				///echo '?'.print_r($valim).'?';
				if($kim == 'IEPS' || $kim == 'IVA'){
					if($kim == 'IVA'){
						$immpuesSAT = '002';
						$valim['tasa'] = '0.'.$valim['tasa'].'0000';

						if($des!=''){
							$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
						}else{
							$base = str_replace(",", "", number_format($value['Importe'],2));
							$conceptosOri.=$base.'|';
						}

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.='Tasa|';
						//$conceptosOri.='0.160000|';
						$conceptosOri.=$valim['tasa'].'|';
						$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';




						$conceptos.="<cfdi:Traslados>";
						//$conceptos.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptos.="<cfdi:Traslado Base='".str_replace(",", "", number_format($base,2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptos.="</cfdi:Traslados>";
					}else{
						$immpuesSAT = '003';
						$valim['tasa'] = '0.'.$valim['tasa'];
						$conceptos.="<cfdi:Traslados>";
						$conceptos.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptos.="</cfdi:Traslados>";
					}
				}else{
					if($kim=='IVAR'){
						$immpuesSAT = '002';
						$valim['tasa'] = '0.040000';

						if($des!=''){
							$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
						}else{
							$base = str_replace(",", "", number_format($value['Importe'],2));
							$conceptosOri.=$base.'|';
						}

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.='Tasa|';
						//$conceptosOri.='0.160000|';
						$conceptosOri.=$valim['tasa'].'|';
						$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';




						$conceptos.="<cfdi:Retenciones>";
						//$conceptos.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptos.="<cfdi:Retencion Base='".str_replace(",", "", number_format($base,2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptos.="</cfdi:Retenciones>";
					}
				}
			}
			//echo $conceptosOri;
			//exit();
			$conceptos.="</cfdi:Impuestos>";
			$conceptos.='</cfdi:Concepto>';
			$subTotImportes += (float) str_replace(",", "", number_format($value['Importe'],2));
		}

		//print_r($azurian);
		/*echo $conceptos;
		print_r($conceptosDatos);
		exit(); */
				//////////impuestos azurian
		$ivas = '';
		$tisr = 0.00;
		$tiva = 0.00;
		$tieps = 0.00;

		$oriisr = '';
		$oriiva = '';

		$isr = '';
		$iva = '';
		$azurian['Conceptos']['conceptos'] = $conceptos;
		$azurian['Conceptos']['conceptosOri'] = $conceptosOri;

		$traslads = '';
		$retenids = '';
		$haytras = 0;
		$hayret = 0;
		$trasladsimp = 0.00;
		$retenciones = 0.00;
		$trasxml = '';
		$retexml = '';

  print_r($nn);
  exit();
		foreach ($nn as $clave => $imm) {
			if ($clave == 'IEPS' || $clave == 'IVA') {

				$haytras = 1;
				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
					if ($clave == 'IEPS') {
						$tieps+=number_format($val, 2, '.', '');
						$clIm = '003';
					}
					if ($clave == 'IVA') {
						$tiva+=number_format($val, 2, '.', '');
						$clIm = '002';
					}

					$traslads.='|' . $clIm . '|';
					$traslads.='Tasa|';
				   // $traslads.='' . $clavetasa . '|';
					$traslads.='0.160000|';
					$traslads.=number_format($val, 2, '.', '');
					$trasladsimp+=number_format($val, 2, '.', '');
					$trasxml.="<cfdi:Traslado Impuesto='" . $clIm . "' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='" . number_format($val, 2, '.', '') . "' />";
				}
			} elseif ($clave == 'ISR' || $clave == 'IVAR') {
				$hayret = 1;

				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
					}
				if($clave == 'IVAR'){
					$clave = '002';
					$king = 1;
				}
					$tisr+=number_format($val, 2, '.', '');
					$retenids.='|' . $clave . '|';
					$retenidsT.='' . number_format($val, 2, '.', '') . '|';
					$retenids.=number_format($val, 2, '.', '');
					$retenciones+=number_format($val, 2, '.', '');
					$retexml.="<cfdi:Retencion Impuesto='" . $clave . "' Importe='" . number_format($val, 2, '.', '') . "' />";
					/*if($king ==1){
						$clave = 'IVAR';
						$king = 0;
					} */
				}
			}
		}
		////fin del foreach nn
		//echo '///////'.$traslads;

		$azurian['Impuestos']['totalImpuestosIeps'] = $tieps;

		if ($haytras == 1) {
			$iva.='<cfdi:Traslados>' . $trasxml . '</cfdi:Traslados>';
		} else {
			$traslads.='|IVA|';
			$traslads.='0.00|';
			$traslads.='0.00';
			$trasladsimp = '0.00';
			$iva.="<cfdi:Traslados><cfdi:Traslado Impuesto='IVA' tasa='0.00' importe='0.00' /></cfdi:Traslados>";
		}
		if ($hayret == 1) {
			$isr.='<cfdi:Retenciones>' . $retexml . '</cfdi:Retenciones>';
		}
		  if($hayret == 1){
			$cadRet = '|'.str_replace(',', '', number_format($tisr,2));
		  }else{
			$cadRet = '';
		  }
		   ///////Ajuste centavo
		/*echo 'SubImportes='.$subTotImportes.'<br>';
		echo 'SubAzurian='.$azurian['Basicos']['subTotal'].'<br>';
		echo 'totimpuestos='.$trasladsimp.'<br>';
		echo 'TotalAzurian='.$azurian['Basicos']['total'].'<br>';
		echo '('.$subTotImportes.'-'.$azurian['Basicos']['subTotal'].')<br>'; */

		$xsubT =  number_format($subTotImportes, 2, '.', '');
		$xsubA =  number_format($azurian['Basicos']['SubTotal'], 2, '.', '');

		if($descGen < 0){
			if($xsubT < $xsubA){
					$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] - 0.01;
					$trasladsimp = $trasladsimp + 0.01;
				}elseif($xsubT > $xsubA){
					if($trasladsimp > 0){
						$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + 0.01;
						$trasladsimp = $trasladsimp - 0.01;
					}else{
						$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + 0.01;
						$azurian['Basicos']['Total'] = $azurian['Basicos']['Total'] + 0.01;
					}

				}
	    }else{
	    	$azurian['Basicos']['SubTotal'] = $azurian['Basicos']['SubTotal'] + number_format($descGen, 2, '.', '');
	    }


		  $azurian['Impuestos']['isr'] = $retenids.$cadRet;
		  $azurian['Impuestos']['iva'] = $traslads . '|' . number_format($trasladsimp, 2, '.', '');

		  $azurian['Impuestos']['totalImpuestosRetenidos'] = number_format($retenciones, 2, '.', '');
		  $azurian['Impuestos']['totalImpuestosTrasladados'] = number_format($trasladsimp, 2, '.', '');
		  if($azurian['Impuestos']['totalImpuestosRetenidos'] > 0){
		  	 $azurian['Basicos']['Total'] = $azurian['Basicos']['Total'];
		  }

	   /* echo 'SubImportes='.$subTotImportes.'<br>';
		echo 'SubAzurian='.$azurian['Basicos']['subTotal'].'<br>';
		echo 'totimpuestos='.$azurian['Impuestos']['totalImpuestosTrasladados'].'<br>';
		echo 'TotalAzurian='.$azurian['Basicos']['total'];
		exit();  */


		$ivas.=$isr . $iva;

		$azurian['Impuestos']['ivas'] = $ivas;
		//print_r($azurian);
		//echo json_encode($azurian);
		//exit();
		//print_r($azurian);
		//exit();

		$soloExento = 1;
		//require_once('../../modulos/lib/nusoap.php');
		//require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}



	}
	function crearNota33Total($uuidRelacion,$usoCfdi,$relacion){



		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");
		$folios = "SELECT serie_nc,folio_nc FROM pvt_serie_folio LIMIT 1";
		$data = $this->queryArray($folios);
		if ($data["total"] > 0) {
			$data = $data["rows"][0];
		}
			$serie = $data['serie_nc'];
			$folio = $data['folio_nc'];
			///Busca el pack para facturar
		$qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];
		// Receptor
		//===============================================================
		$seluso = 'SELECT c_usocfdi from c_usocfdi where id='.$usoCfdi;
		//echo $seluso;
		$resuso = $this->queryArray($seluso);
		$claveUsoCfdi = $resuso['rows'][0]['c_usocfdi'];

		$queryCad = "SELECT p.cadenaOriginal FROM app_respuestaFacturacion p where folio='".$uuidRelacion."'";
		//echo $queryCad;
		$resCad = $this->queryArray($queryCad);
		//echo $resCad['rows'][0]['cadenaOriginal'];
		$azurian=base64_decode($resCad['rows'][0]['cadenaOriginal']);
			$azurian = str_replace("\\", "", $azurian);
			if($azurian!=''){ $azurian=json_decode($azurian); }
			$azurian = $this->object_to_array($azurian);


		unset($azurian['datosTimbrado']);
		$azurian['Basicos']['Serie'] = $serie; //No obligatorio
		$azurian['Basicos']['Folio'] = $folio; //No obligatorio
		$azurian['Basicos']['TipoDeComprobante'] = 'E';
		$azurian['Receptor']['UsoCFDI'] = $claveUsoCfdi;
		/* UUID relacionados
		============================================================== */
		//$relacion,$uuidRelacion;
		$relacionXML = '';
		$relacionCAD = '';
		if($relacion!='0'){
			$relacionCAD .= $relacion.'|';
			$relacionXML .='<cfdi:CfdiRelacionados TipoRelacion="'.$relacion.'">';
			$rel = explode(';', $uuidRelacion);
			foreach ($rel as $key => $value) {
				if($value!=''){
					$relacionCAD .= $value.'|';
			 		$relacionXML.='<cfdi:CfdiRelacionado UUID="'.$value.'"/>';
				}
			 }
			 $relacionXML.='</cfdi:CfdiRelacionados>';
		}
		$azurian['Realacionados']['xml'] = $relacionXML;
		$azurian['Realacionados']['cadena'] = $relacionCAD;
			$queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Rfc'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);

				/*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp; */
				$pathdc = '../../modulos/SAT/cliente';
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				echo json_encode($JSON);
				exit();
			}
			$conceptos = '';
			$conceptosR = '';
			$conceptosT = '';
			$conceptosOriR = '';
			$conceptosOriT = '';
			//$conceptos = '<cfdi:Conceptos>';
			$conceptosOri = '|84111506|1|ACT|Actividad|Nota de credito relacionada a factura '.$uuidRelacion.'|'.$azurian['Basicos']['SubTotal'].'|'.$azurian['Basicos']['SubTotal'].'|';

			$conceptos.="<cfdi:Concepto ClaveProdServ='84111506' ClaveUnidad='ACT' Cantidad='1' Unidad='Actividad' Descripcion='Nota de credito relacionada a factura ".$uuidRelacion."' ValorUnitario='".$azurian['Basicos']['SubTotal']."' Importe='" . $azurian['Basicos']['SubTotal'] . "'>";

			$conceptos.="<cfdi:Impuestos>";
			//print_r($azurian['nn']['nn']);

			foreach ($azurian['nn']['nn'] as $clave => $imm) {
				//echo $clave.'---'.$imm;
				if ($clave == 'IEPS' || $clave == 'IVA' || $clave == 'IVAE'  ) {
					$conceptosT.="<cfdi:Traslados>";
					foreach ($imm as $key1 => $value1) {
						//echo '-'.$key1.'-'.$value1.'<br>';
						if($clave=='IVA' || $clave=='IVAE'){
							$imCl = '002';
						}else{
							$imCl = '003';
						}
						//echo '('.$key1.')';
						if($key1==0){
							$tasaCuo = '0.000000';
						}else{
							$tasaCuo = '0.'.$key1.'0000';
						}
						$conceptosOriT.=$azurian['Basicos']['SubTotal'].'|';
						$conceptosOriT.=$imCl.'|';
						$conceptosOriT.='Tasa|';
						$conceptosOriT.=$tasaCuo.'|';
						$conceptosOriT.=bcdiv(round($value1,2),'1',2).'|';

						//echo $tasaCuo.'ww';
						$conceptosT.="<cfdi:Traslado Base='".$azurian['Basicos']['SubTotal']."' Impuesto='".$imCl."' TipoFactor='Tasa' TasaOCuota='".$tasaCuo."' Importe='".bcdiv(round($value1,2),'1',2)."'/>";
					}
					$conceptosT.="</cfdi:Traslados>";
				}else{
					$conceptosR.="<cfdi:Retenciones>";
					foreach ($imm as $key2 => $value2) {
						//echo '-'.$key2.'-'.$value2.'<br>';
						if($clave=='IVAR'){
							$imCl = '002';
						}else{
							$imCl = '001';
						}

						if($key2==0){
							$tasaCuo = '0.000000';
						}else{
							$tasaCuo = '0.0'.$key2.'0000';
						}
						//echo $tasaCuo.'???';
						$conceptosOriR.=$azurian['Basicos']['SubTotal'].'|';
						$conceptosOriR.=$imCl.'|';
						$conceptosOriR.='Tasa|';
						$conceptosOriR.=$tasaCuo.'|';
						$conceptosOriR.=bcdiv(round($value2,2),'1',2).'|';


						$conceptosR.="<cfdi:Retencion Base='".$azurian['Basicos']['SubTotal']."' Impuesto='002' TipoFactor='Tasa' TasaOCuota='".$tasaCuo."' Importe='".bcdiv(round($value2,2),'1',2)."'/>";
					}
					$conceptosR.="</cfdi:Retenciones>";
				}
			}
			$conceptosR.="</cfdi:Impuestos>";
			$conceptosR.="</cfdi:Concepto>";
			//echo $conceptos.$conceptosT.$conceptosR;
			$azurian['Conceptos']['conceptos'] = $conceptos.$conceptosT.$conceptosR;
			$azurian['Conceptos']['conceptosOri'] = $conceptosOri.$conceptosOriT.$conceptosOriR;
			//echo $conceptos;
			//echo $azurian['Conceptos']['conceptos'];
			//echo 'kkkk'.$key_cliente;
		//print_r($azurian);
		//exit();
		if($pac==2){
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}else if($pac==1){
			require_once('../../modulos/lib/nusoap.php');
			require_once('../../modulos/SAT/funcionesSAT33.php');
		}


	}

	public function buscaMedicos($term) {
        /*obtiene los médicos*/
        $sql = "SELECT id, CONCAT( CONCAT ( CONCAT( CONCAT(codigo, ' : ') , nombre), ' | ') , cedula) text
                FROM medicos
                WHERE codigo LIKE '%$term%' OR  nombre LIKE '%$term%' OR cedula LIKE '%$term%'
                ORDER BY codigo, nombre, cedula ";

        $result = $this->queryArray($sql);
        //print_r($result["rows"]);
        return $result;

    }
    public function verificaImpuestos($uuidRelacion){
    	$sel = "SELECT cadenaOriginal from app_respuestaFacturacion where folio='".$uuidRelacion."'";
    	$rs = $this->queryArray($sel);

    		$azurian=base64_decode($rs['rows'][0]['cadenaOriginal']);
			$azurian = str_replace("\\", "", $azurian);
			if($azurian!=''){ $azurian=json_decode($azurian); }
			$azurian = $this->object_to_array($azurian);

			//echo $azurian['Impuestos']['totalImpuestosRetenidos'].'..';
			if(($azurian['Impuestos']['totalImpuestosRetenidos'] * 1) > 0){
				return 1;

			}else{
				return 0;
			}

    }

    public function tipocambio(){
		$sql = "SELECT tipo_cambio FROM cont_tipo_cambio ORDER BY id DESC limit 1;";
		$result = $this->queryArray($sql);
		return $result['rows'][0]['tipo_cambio'];
	}

	public function getLimiteMontoCaja(){
		$sql = "SELECT  limite_monto_caja
				FROM	app_config_ventas
				WHERE	id = '1';";
		$result = $this->queryArray($sql);
		return $result['rows'][0]['limite_monto_caja'];
	}
	public function datosDom33($uuid){

			$sel = 'SELECT idFact from app_respuestaFacturacion where folio="'.$uuid.'"';
			//echo $sel;
			$res1 = $this->queryArray($sel);
		
			if($res1['rows'][0]['idFact']=='0'){

				//print_r($df);
				$parametros['Receptor'] = array();

				$parametros['Receptor']['Pais'] = '';
				$parametros['Receptor']['Calle'] = '';
				$parametros['Receptor']['NumExt'] = '';
				$parametros['Receptor']['Colonia'] = '';
				$parametros['Receptor']['Municipio'] = '';
				$parametros['Receptor']['Ciudad'] = '';
				$parametros['Receptor']['CP'] = '';
				$parametros['Receptor']['Estado'] = '';
			}else{
				$df = (object) $this->datosFacturacion($res1['rows'][0]['idFact']);
				//print_r($df);
				$parametros['Receptor'] = array();

				$parametros['Receptor']['Pais'] = utf8_decode($df->pais);
				$parametros['Receptor']['Calle'] = utf8_decode($df->domicilio);
				$parametros['Receptor']['NumExt'] = $df->num_ext;
				$parametros['Receptor']['Colonia'] = utf8_decode($df->colonia);
				$parametros['Receptor']['Municipio'] = utf8_decode($df->municipio);
				$parametros['Receptor']['Ciudad'] = utf8_decode($df->ciudad);
				$parametros['Receptor']['CP'] = $df->cp;
				$parametros['Receptor']['Estado'] = utf8_decode($df->estado);
			}

	
			return $parametros;
	}
	public function buscaFormaPago($uuidRelacion){
    	$sel = "SELECT cadenaOriginal from app_respuestaFacturacion where folio='".$uuidRelacion."'";
    	$rs = $this->queryArray($sel);

    		$azurian=base64_decode($rs['rows'][0]['cadenaOriginal']);
			$azurian = str_replace("\\", "", $azurian);
			if($azurian!=''){ $azurian=json_decode($azurian); }
			$azurian = $this->object_to_array($azurian);

			return $azurian['Basicos']['FormaPago'];

    }
    public function infoNotaCredito($uid){
        $sel = 'SELECT * from app_respuestaFacturacion where folio="'.$uid.'"';
        $res = $this->queryArray($sel);

            $azurian=base64_decode($res['rows'][0]['cadenaOriginal']);
            $azurian = str_replace("\\", "", $azurian);
            if($azurian!=''){ $azurian=json_decode($azurian); }
            $azurian = $this->object_to_array($azurian);

	    	if (isset($azurian['Basicos']['total'])){
	    		$totalAzu = $azurian['Basicos']['total'];
			}else{
				$totalAzu = $azurian['Basicos']['Total'];
			}


        $selnot = 'SELECT n.monto,f.folio,n.idVenta
                    from app_notas_credito n, app_respuestaFacturacion f
                where n.idFactura ='.$res['rows'][0]['id'].' and n.idNota=f.id';
        $resno = $this->queryArray($selnot);

        foreach ($resno['rows'] as $key => $value) {
            $disponible +=$value['monto'];
        }
        $disponible = str_replace(',', '', number_format($disponible,2));
        $disponible = (float) $totalAzu - (float) $disponible;

        //$seNo = "SELECT nombre from comun_facturacion where id=".$res['rows'][0]['idFact'];
        $seNo = "SELECT cf.nombre, cc.nombre as nom from comun_facturacion cf, comun_cliente cc where cf.nombre=cc.id and cf.id=".$res['rows'][0]['idFact'];
        //echo $seNo;
        $resNo = $this->queryArray($seNo);

        return array('monto' => $totalAzu, 'notas' => $resno['rows'], 'disponible' =>$disponible, 'cliente'=>$resNo['rows'][0]['nombre'], 'clienteNombre'=>$resNo['rows'][0]['nom']);
    }
    public function complementos(){
    	$th = "SELECT * from app_complementos";
    	$res = $this->queryArray($th);
    	 return array('complementos' => $res['rows']);
    }
    public function formComplementos($id){
    	$th = "SELECT * from app_complementos where id=".$id;
    	$res = $this->queryArray($th);
    	if($res['total'] > 0){
			   	$string = $res['rows'][0]['xml'];
				$string = explode("@",$string);
				$campos = array();
				for($i=1;$i<=count($string)-1;$i++){
					$substring = explode(' ',$string[$i]);
					//$substring[0].'<br>';
					array_push($campos, $substring[0]);

				}
				return array('estatus' => true, 'campos' => $campos);
    	}else{
    		return array('estatus' => false);
    	}
    }
    public function calculaComplemento($idCom,$campos){
    	$th = "SELECT * from app_complementos where id=".$id;
    	$res = $this->queryArray($th);
    	$formula = $res['rows'][0]['formula'];


    }
    public function impuestosLocales(){
    	$sel = "SELECT * from app_impuesto where local=1";
    	$res = $this->queryArray($sel);
    	return array('impuestos' => $res['rows'] );
    }
    public function agregaImpuestoLocal($importe,$impuesto,$id){
    	$sel = 'SELECT * from app_impuesto where id='.$impuesto;
    	$res = $this->queryArray($sel);

    	$_SESSION['caja'][$id]->cargosLocales->$res['rows'][0]["clave"]=array('tasa' =>$res['rows'][0]["valor"] ,'importe' => bcdiv($importe,'1',10), 'nombre' => $res['rows'][0]["nombre"], 'formula' => 1);

    	//print_r($_SESSION['caja']);
    	$sessionArray = $this->object_to_array($_SESSION['caja']);

		$productosTotal = 0;
		foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				$stringTaxes .=$value['idProducto'].'-'.$value['precio'].'-'.$value['cantidad'].'-'.$value['formula'].'-'.$value['caracteristicas'].'/';
				$productosTotal += $value['cantidad'];
			}
		}

		$this->calculaImpuestos($stringTaxes);

    }
    public function calculaImpuestoLocal(){
    	//print_r($_SESSION['caja']);
    	//exit();
    	$traslados = 0;
    	$retenidos = 0;
    	$sessionArray = $this->object_to_array($_SESSION['caja']);
    	foreach ($sessionArray as $key => $value) {
			if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
				foreach ($value['cargosLocales'] as $key2 => $value2) {
					if($key2 != 'MR'){
						$_SESSION['caja']['cargos']['impuestos'][$key2]=bcdiv($value2['importe'],'1',10) * 1;
						$_SESSION['caja']['cargos']['impuestosPorcentajes'][$value2['nombre']] = bcdiv($value2['importe'],'1',10) * 1;
						$_SESSION['caja']['cargos']['impuestosFactura'][$key2][$value2['tasa']] = bcdiv($value2['importe'],'1',10) * 1;
						$_SESSION['caja']['cargos']['impuestosPdf'][$key2][$value2['tasa']]['Valor'] = bcdiv($value2['importe'],'1',10) * 1;
 						$traslados = $value2['importe'];
					}else{
						$_SESSION['caja']['cargos']['impuestos'][$key2]=bcdiv($value2['importe'],'1',10) * 1;
						$_SESSION['caja']['cargos']['impuestosPorcentajes'][$value2['nombre']] = bcdiv($value2['importe'],'1',10) * 1;
						$_SESSION['caja']['cargos']['impuestosFactura'][$key2][$value2['tasa']] = bcdiv($value2['importe'],'1',10) * 1;
						$_SESSION['caja']['cargos']['impuestosPdf'][$key2][$value2['tasa']]['Valor'] = bcdiv($value2['importe'],'1',10) * 1;
						$retenidos = $value2['importe'];
					}
				}
			}
		}
		$tiene = bcdiv($retenidos,'1',10) + bcdiv($traslados,'1',10);
		if($tiene > 0){
			//echo 'TRas='.$traslados.' Rete='.$retenidos;
			//exit();
			//$_SESSION['caja']['cargos']['subtotal'] += bcdiv($subtotal,'1',10);
			$_SESSION['caja']['cargos']['subtotal'] = $_SESSION['caja']['cargos']['subtotal'];
			$_SESSION['caja']['cargos']['total'] =  bcdiv($_SESSION['caja']['cargos']['total'],'1',10) - bcdiv($retenidos,'1',10);
			$_SESSION['caja']['cargos']['total'] =  bcdiv($_SESSION['caja']['cargos']['total'],'1',10) + bcdiv($traslados,'1',10);
			//print_r($_SESSION['caja']);
		}

    }
    function infoSuspendida($id){
        $select  = "SELECT * from app_pos_venta_suspendida where id=".$id;
        //echo $select;
        $res = $this->queryArray($select);

        return $res['rows'];
    }

    public function verificainicioCajaP() {

		$sql = "SELECT cp.new_inicio, ic.monto
				FROM  app_pos_corte_parcial cp
				LEFT JOIN app_pos_inicio_caja ic on ic.id = cp.id_inicio_caja
				WHERE cp.id_empleado = ".$_SESSION['accelog_idempleado']." ORDER BY id_corte_parcial DESC LIMIT 1;";

		$result = $this->queryArray($sql);		
		$result['rows'][0]['sucursal'] = $_SESSION['sucursalNombre'];
		$result['rows'][0]['idSuc'] = $_SESSION['sucursal'];
		
		return $result['rows'];

	}

    public function IniciarcajaP($monto){

		$sql = "UPDATE app_pos_corte_parcial SET new_inicio = '$monto' where id_empleado = ".$_SESSION['accelog_idempleado']." and new_inicio is null ORDER BY id_corte_parcial DESC LIMIT 1;";		
		$result = $this->queryArray($sql);
		return $result;
	}

	function totalVentasCP(){
		
		date_default_timezone_set('America/Mexico_City');
		$fechaAl = date('Y-m-d H:i:s');
		$idEmpleado = $_SESSION['accelog_idempleado'];
		// id de incio de caja
		$sql = "SELECT id FROM app_pos_inicio_caja WHERE idUsuario = '$idEmpleado' AND idCortecaja IS NULL;";
    	$reuslt = $this->queryArray($sql);
    	$idinicio = $reuslt['rows'][0]['id'];
    	// fecha de corte parcial
    	$sql = "SELECT fecha FROM  app_pos_corte_parcial where id_inicio_caja = '$idinicio' order by id_corte_parcial desc limit 1;";
    	$reuslt = $this->queryArray($sql);
    	$fecha = $reuslt['rows'][0]['fecha'];

    	if(isset($fecha)){ // para ventas despues de cortes parciales
    		$fechaDel = $fecha;

    	}else{ // para ventas antes del primer corte parcial

    		$sql = "SELECT fechafin FROM app_pos_corte_caja WHERE idEmpleado = ".$idEmpleado." ORDER BY fechafin DESC LIMIT 1;";
	    	$reuslt = $this->queryArray($sql);
	    	$fechaDel = $reuslt['rows'][0]['fechafin'];
    	}

    	$sql = "SELECT  sum(v.monto) totalVentas
				FROM app_pos_venta v 
				LEFT JOIN app_pos_venta_pagos vp ON vp.idVenta = v.idVenta 
				LEFT JOIN app_pos_inicio_caja ic ON ic.idUsuario = v.idEmpleado AND idCortecaja is null 
				WHERE v.fecha <= '".$fechaAl."' and v.fecha >= '".$fechaDel."' AND v.idEmpleado = ".$idEmpleado.";";
    	$reuslt = $this->queryArray($sql);
    	$totalVentas = $reuslt['rows'][0]['totalVentas'];

    	if(isset($totalVentas)){
    		$totalVentas = $totalVentas;
    	}else{
    		$totalVentas = 0;
    	}



    	return $totalVentas;
	}

    function infoCorteParcial($idEmpleado,$fechaAl){
    	$sql = "SELECT fechafin FROM app_pos_corte_caja WHERE idEmpleado = ".$idEmpleado." ORDER BY fechafin DESC LIMIT 1;";
    	$reuslt = $this->queryArray($sql);
    	$fechaDel = $reuslt['rows'][0]['fechafin'];

    	$sql = "SELECT  sum(v.monto) totalVentas
				FROM app_pos_venta v 
				LEFT JOIN app_pos_venta_pagos vp ON vp.idVenta = v.idVenta 
				LEFT JOIN app_pos_inicio_caja ic ON ic.idUsuario = v.idEmpleado AND idCortecaja is null 
				WHERE v.fecha <= '".$fechaAl."' and v.fecha >= '".$fechaDel."' AND v.idEmpleado = ".$idEmpleado.";";
    	$reuslt = $this->queryArray($sql);
    	$totalVentas = $reuslt['rows'][0]['totalVentas'];

    	$sql = "SELECT ROUND(sum(vp.monto),2) monto, ic.monto inicio, ic.id idinicio FROM app_pos_venta v
		LEFT JOIN app_pos_venta_pagos vp ON vp.idVenta = v.idVenta
		LEFT JOIN app_pos_inicio_caja ic ON ic.idUsuario = v.idEmpleado AND idCortecaja is null
		WHERE v.fecha <= '".$fechaAl."' AND v.fecha >= '".$fechaDel."' AND v.idEmpleado = ".$idEmpleado." AND vp.idFormapago = 1
		ORDER BY v.idVenta DESC;";

		$res = $this->queryArray($sql);
		
		$res['rows'][0]['fechaDel'] = $fechaDel;
		$res['rows'][0]['totalVentas'] = $totalVentas;

        return $res['rows'];
    }

    function infoCorteParcial2($idEmpleado,$fechaDel,$fechaAl,$new_inicio){
    	// $sql = "SELECT fechafin FROM app_pos_corte_caja WHERE idEmpleado = ".$idEmpleado." ORDER BY fechafin DESC LIMIT 1;";
    	// $reuslt = $this->queryArray($sql);
    	// $fechaDel = $reuslt['rows'][0]['fechafin'];

    	$sql = "SELECT  sum(v.monto) totalVentas
				FROM app_pos_venta v 
				LEFT JOIN app_pos_venta_pagos vp ON vp.idVenta = v.idVenta 
				LEFT JOIN app_pos_inicio_caja ic ON ic.idUsuario = v.idEmpleado AND idCortecaja is null 
				WHERE v.fecha <= '".$fechaAl."' and v.fecha >= '".$fechaDel."' AND v.idEmpleado = ".$idEmpleado.";";
    	$reuslt = $this->queryArray($sql);
    	$totalVentas = $reuslt['rows'][0]['totalVentas'];


    	$sql = "SELECT ROUND(sum(vp.monto),2) monto, ic.id idinicio FROM app_pos_venta v
		LEFT JOIN app_pos_venta_pagos vp ON vp.idVenta = v.idVenta
		LEFT JOIN app_pos_inicio_caja ic ON ic.idUsuario = v.idEmpleado AND idCortecaja is null
		WHERE v.fecha <= '".$fechaAl."' AND v.fecha >= '".$fechaDel."' AND v.idEmpleado = ".$idEmpleado." AND vp.idFormapago = 1
		ORDER BY v.idVenta DESC;";

		$res = $this->queryArray($sql);
		
		$res['rows'][0]['fechaDel'] = $fechaDel;
		$res['rows'][0]['totalVentas'] = $totalVentas;
		$res['rows'][0]['inicio'] = $new_inicio;

        return $res['rows'];
    }

    function cortePI(){
    	$sql = "SELECT monto FROM app_pos_inicio_caja WHERE idUsuario = ".$_SESSION["accelog_idempleado"]." AND idCortecaja IS NULL ORDER BY fecha DESC;";
    	$res = $this->queryArray($sql);
    	return $res['rows'][0]['monto'];
    }

    function primerCP($idEmpleado){
    	$sql = "SELECT id FROM app_pos_inicio_caja WHERE idUsuario = '$idEmpleado' AND idCortecaja IS NULL;";
    	$reuslt = $this->queryArray($sql);
    	$idinicio = $reuslt['rows'][0]['id'];

    	$sql = "SELECT id_corte_parcial, fecha, new_inicio FROM  app_pos_corte_parcial where id_inicio_caja = '$idinicio' order by id_corte_parcial desc limit 1;";
    	$reuslt = $this->queryArray($sql);
    	return $reuslt;

    }


    function hideprod($idEmpleado){

    	$sucursal = $this->sucursal();

    	$sql = "SELECT hideprod FROM com_configuracion WHERE id_sucursal = ".$sucursal.";";
    	$res = $this->queryArray($sql);
    	return $res['rows'][0]['hideprod'];
    }
    function datosEmpleado($idEmpleado){
    	$sql = "SELECT concat(nombre, ' ', apellidos) empleado FROM `administracion_usuarios`  WHERE `idempleado` = ".$idEmpleado.";";
    	$res = $this->queryArray($sql);
    	return $res['rows'][0]['empleado'];
    }

    function guardarCorteP($turno,$fecha,$idEmpleado,$total_ventas,$disponible,$total_reportado,$idsuc,$id_inicio_caja){
    	$sql = "INSERT INTO app_pos_corte_parcial (turno, fecha, id_empleado, total_ventas, disponible, total_reportado, idsuc, id_inicio_caja)
		VALUES ($turno, '".$fecha."', $idEmpleado, $total_ventas, $disponible, $total_reportado, $idsuc, $id_inicio_caja);";
		$result = $this->queryArray($sql);
		return $result['insertId'];
		
    }
    function pretickets(){
 
    	$sel = 'SELECT vs.id,vs.s_monto,vs.fecha,c.id as idCliente,c.nombre as cliente, suc.idSuc, suc.nombre as sucursal, u.idempleado, u.usuario
				from app_pos_venta_suspendida vs
				left join comun_cliente c on c.id=vs.s_cliente
				left join accelog_usuarios u on u.idempleado=vs.s_empleado 
				left join mrp_sucursal suc on suc.idSuc=vs.s_sucursal';

    	$res = $this->queryArray($sel);

    	return   array('pretickets' => $res['rows'] );
    }
    function buscaCar(){
    	$cont = 0;

    	$sel1 = "select * from app_producto_caracteristicas limit 1";
    	$res1 = $this->queryArray($sel1);
    	if($res1['total']>0){
    		$cont ++;

    	}

    	$sel2 = "select * from app_producto_lotes limit 1";
    	$res2 = $this->queryArray($sel2);
    	if($res3['total']>0){
    		$cont ++;

    	}

    	$sel3 = 'select * from app_producto_serie limit 1';
    	$res3 = $this->queryArray($sel3);
    	if($res3['total']>0){
    		$cont ++;

    	}

    	return $cont;
    }
    function crearpdfComplementos(){
    	$sele1 = "SELECT uuid_pago from cont_facturas_relacion GROUP BY uuid_pago limit 4";
    	$res1 = $this->queryArray($sele1);
    	$sele2 = "SELECT logoempresa from organizaciones";
    	$res2 = $this->queryArray($sele2);

    	return   array('comple' => $res1['rows'], 'logo' => $res2['rows'][0]['logoempresa'] );
    }
    function validaTarjetaRegalo($str){
		    $str = trim($str,'+');
		    $arrTar = explode('+',$str);
		    $usadas = '';
		    foreach ($arrTar as $keyTar => $valueTar) {
				$sel = "SELECT id from tarjeta_regalo where numero='".$valueTar."' and usada=0";
				$res = $this->queryArray($sel);
					if($res['total']>0){
						$usadas .= $valueTar.',';
					}
				}
		return  array('tarj' => trim($usadas,',') );
    }
    	public function facturar33($idFact, $idVenta, $bloqueo, $mensaje,$consumo,$doc,$moneda,$tipoCambio,$seriex,$usoCfdi,$mpCat,$relacion,$uuidRelacion,$camposComple){
		//echo '('.$mpCat.')';
		//print_r($_SESSION["caja"]);
		//exit();
		$_SESSION["caja"] = $this->object_to_array($_SESSION["caja"]);

		$monto = str_replace(",", "", $_SESSION["caja"]["cargos"]["total"]);
		$impuestos = 0;

		$arraytmp = (object) $_SESSION['caja'];
		foreach ($arraytmp as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden' ) {
				$impuestos = 0;
				foreach ($producto->cargos as $key2 => $value2) {
					$impuestos += $value2;
				}
			}
		}

		if ($memsaje != false || $mensaje != '') {
			$updateVenta = $this->queryArray("UPDATE app_pos_venta set observacion = '" . $mensaje . "' where idVenta =" . $idVenta);
		}

		/* --- Configuracion de las series  ---*/
		$selSer = "SELECT seriesFactura from app_config_ventas";
		$resSer = $this->queryArray($selSer);


		if($doc == 2){ // factura
			if($resSer['rows'][0]['seriesFactura']==1){
				$folios = "SELECT serie,folio FROM pvt_serie_folio where id=".$seriex;
			}else{
				$folios = "SELECT serie,folio FROM pvt_serie_folio LIMIT 1";
			}

		}else{ // honorarios
			$folios = "SELECT serie_h,folio_h FROM pvt_serie_folio LIMIT 1";
		}

		$data = $this->queryArray($folios);
		if ($data["total"] > 0) {
			$data = $data["rows"][0];
		}
		if($doc == 2){
			$serie = $data['serie'];
			$folio = $data['folio'];
		}elseif($doc == 15){
			$folios = "SELECT serie_nc,folio_nc FROM pvt_serie_folio LIMIT 1";
			$data = $this->queryArray($folios);
			if ($data["total"] > 0) {
				$data = $data["rows"][0];
			}
			$serie = $data['serie_nc'];
			$folio = $data['folio_nc'];

		}else{
			$serie = $data['serie_h'];
			$folio = $data['folio_h'];
		}
		///Busca el pack para facturar
		$qrpac = "SELECT * FROM pvt_configura_facturacion WHERE id=1;";
		$respac = $this->queryArray($qrpac);
		$pac = $respac["rows"][0]["pac"];
		$userPAC = $respac["rows"][0]["fc_user"];
		$passPAC = $respac["rows"][0]["fc_password"];

		///Clavel del uso de CFDI
		$seluso = 'SELECT c_usocfdi from c_usocfdi where id='.$usoCfdi;
		//echo $seluso;
		$resuso = $this->queryArray($seluso);
		$claveUsoCfdi = $resuso['rows'][0]['c_usocfdi'];


		//echo 'eee'.$claveUsoCfdi;
		// Receptor
		//===============================================================

		$parametros['Receptor'] = array();
		if ($idFact == 0) {

			$parametros['Receptor']['RFC'] = "XAXX010101000";
			$parametros['Receptor']['RazonSocial'] = 'Factura Generica';
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;
			$lugExp = 'SELECT cp from mrp_sucursal where idSuc="'.$_SESSION['sucursal'].'"';
			$lugExpeRes = $this->queryArray($lugExp);

			$parametros['Receptor']['DomicilioFiscalReceptor'] = $lugExpeRes['rows'][0]['cp'];
			$parametros['Receptor']['RegimenFiscalReceptor'] = '616';
		} else {
			$df = (object) $this->datosFacturacion($idFact);
			$parametros['Receptor']['RFC'] = $df->rfc;
			$parametros['Receptor']['RazonSocial'] = utf8_decode($df->razon_social);
			$parametros['Receptor']['UsoCFDI']=$claveUsoCfdi;
			$parametros['Receptor']['DomicilioFiscalReceptor'] = $df->cp;
			$parametros['Receptor']['RegimenFiscalReceptor'] = $df->regimen_fiscal;
			/*$parametros['Receptor']['Pais'] = utf8_decode($df->pais);
			$parametros['Receptor']['Calle'] = utf8_decode($df->domicilio);
			$parametros['Receptor']['NumExt'] = $df->num_ext;
			$parametros['Receptor']['Colonia'] = utf8_decode($df->colonia);
			$parametros['Receptor']['Municipio'] = utf8_decode($df->municipio);
			$parametros['Receptor']['Ciudad'] = utf8_decode($df->ciudad);
			$parametros['Receptor']['CP'] = $df->cp;
			$parametros['Receptor']['Estado'] = utf8_decode($df->estado); */
			$parametros['Receptor']['Email1'] = $df->correo;
		}
		//Obteniendo la descripcion de la forma de pago
		$formapago = "";
		$queryFormaPago = " SELECT nombre,referencia,claveSat,monto from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;
		$resultqueryFormaPago = $this->queryArray($queryFormaPago);
		$fpf =  array();
		foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
			$fpf[$pagosValue['claveSat']]+=$pagosValue['monto'];
			if (strlen($pagosValue["referencia"]) > 0) {
				//$formapago .= $pagosValue['claveSat'] . " Ref:" . $pagosValue['referencia'] . ",";
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = $pagosValue['referencia'];
			} else {
				$formapago .= $pagosValue['claveSat'] . ",";
				$refFormaPago = '';

			}
		}
		$banderas = 0;
		foreach ($fpf as $key => $value) {
			if($value>$banderas){
				$cl = $key;
				$banderas = $value;
			}

		}

		$formapago = substr($formapago, 0, strlen($formapago) - 1);
		if ($formapago == "") {
			$formapago = ".";
		}


		$formapago = $cl;


		if($doc == 15){
			$formapago = $this->buscaFormaPago($uuidRelacion);
		}





		///Moneda
		$select = "SELECT * from cont_coin where coin_id=".$moneda;
		$resCon = $this->queryArray($select);
		////Metodo de Pago
		$selMeto = "SELECT clave from c_metododepago where id=".$mpCat;
		//echo $selMeto;
		$resMeto = $this->queryArray($selMeto);
		$payMet = $resMeto['rows'][0]['clave'];


		///Lugar de expedicion
		//$lugExpe = 'SELECT cp from pvt_configura_facturacion';
		$lugExp = 'SELECT cp from mrp_sucursal where idSuc="'.$_SESSION['sucursal'].'"';

		$lugExpeRes = $this->queryArray($lugExp);
		$Email = $df->correo;

		$parametros['DatosCFD']['FormaPago'] = utf8_decode($formapago);
		$parametros['DatosCFD']['MetodoPago'] = $payMet;
		$parametros['DatosCFD']['NumCtaPago'] = $refFormaPago;
		$parametros['DatosCFD']['TipoCambio'] = $tipoCambio;
		$parametros['DatosCFD']['Moneda'] = $resCon['rows'][0]['codigo'];
		//$parametros['DatosCFD']['Subtotal'] = str_replace(",", "", number_format($_SESSION["caja"]["cargos"]["subtotal"],2));
		$parametros['DatosCFD']['Subtotal'] = str_replace(",", "", bcdiv($_SESSION["caja"]["cargos"]["subtotal"], '1', 2));
		$parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] + $_SESSION['caja']['descGeneral'];
	   // $parametros['DatosCFD']['Subtotal'] = $parametros['DatosCFD']['Subtotal'] - 0.01;
		//$parametros['DatosCFD']['Total'] = str_replace(",", "", number_format($_SESSION["caja"]["cargos"]["total"],2));
		$parametros['DatosCFD']['Total'] = str_replace(",", "", bcdiv($_SESSION["caja"]["cargos"]["total"],'1',2));

	   // $parametros['DatosCFD']['Total'] = $parametros['DatosCFD']['Total'] - 0.01;
		if($doc == 15){
			$NoF = "E";
		}else{
			$NoF = "I";
		}
		$parametros['DatosCFD']['Serie'] = $serie;
		$parametros['DatosCFD']['Folio'] = $folio;
		$parametros['DatosCFD']['TipoDeComprobante'] = $NoF; //F o C
		$parametros['DatosCFD']['MensajePDF'] = "";
		$parametros['DatosCFD']['LugarExpedicion'] = $lugExpeRes['rows'][0]['cp'];
		$parametros['DatosCFD']['Descuento'] = $_SESSION['caja']['descGeneral'];

		$x = 0;
		$descGen = 0;
		$textodescuento = "";
		//Empieza a llenar los conceptos
		foreach ($_SESSION['caja'] as $key => $producto) {
			if ($key != 'cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden') { 
				$producto = (object) $producto;
				$descuentogeneral = 0;
				///desceuntos
				//echo "( descuento -> ".$producto->descuento_cantidad.")";
			   /* if ($producto->tipodescuento == "%") {
					$descuentogeneral = (($producto->precioventa * str_replace(",", "", $producto->descuento)) / 100) * $producto->cantidad;
					if ($producto->descuento > 0) {
						$textodescuento.=" - " . cajaModel::cortadec(str_replace(",", "", $producto->descuento_cantidad)) . " %";
					}
				}
				if ($producto->tipodescuento == "$") {
					$descuentogeneral = $producto->descuento;
					if ($producto->descuento > 0) {
						$textodescuento.=" - $" . cajaModel::cortadec(str_replace(",", "", $producto->descuento_cantidad)) . "";
					}
				} */
					if($doc == 15){
						$producto->claveSat = '84111506';
						$producto->unidadSAT = 'ACT';
						$producto->unidad = 'Actividad';
					}
				$conceptosDatos[$x]["ClaveProdServ"] = $producto->claveSat;
				$conceptosDatos[$x]["ClaveUnidad"] = $producto->unidadSAT;
				$conceptosDatos[$x]["Cantidad"] = $producto->cantidad;
				$conceptosDatos[$x]["Unidad"] = $producto->unidad;
				$conceptosDatos[$x]["NoIdentificacion"] = $producto->codigo;
				$conceptosDatos[$x]["ValorUnitario"] = $producto->precio;
				if ($producto->descripcion != '') {
					$conceptosDatos[$x]["Descripcion"] = trim($producto->descripcion . " " . $textodescuento);
				} else {
					$conceptosDatos[$x]["Descripcion"] = trim($producto->nombre . " " . $textodescuento);
				}
				$textodescuento = '';
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio);
				//$conceptosDatos[$x]['Descuento'] = number_format($producto->descuento, 2, '.', '') * 1;
				$conceptosDatos[$x]['Descuento'] = bcdiv($producto->descuento, '1', 6) * 1;

				//echo '/'.$conceptosDatos[$x]['Descuento'] .'/';
				$conceptosDatos[$x]['cargos33'] = $producto->cargos33;
				$consumoTotal +=  $conceptosDatos[$x]['Importe']*1;
				$x++;
				//$descGen += number_format($producto->descuento, 2, '.', '') * 1;
				$descGen += bcdiv($producto->descuento, '1', 6) * 1;

			}//fin del if del ciclo
		}//fin del cilo de llenar conceptos
		//echo '('.$descGen.')';
		//exit();
		if (isset($_SESSION['caja']['cargos']['impuestosFactura'])) {
    		$nn2 = $_SESSION['caja']['cargos']['impuestosFactura'];
			$nnf = $_SESSION['caja']['cargos']['impuestosPdf'];
		}else{
			$nn2 = '';
			$nnf = '';
		}
		//print_r($_SESSION['caja']['cargos']);exit();

		/* FACTURACION AZURIAN
		============================================================== */
		global $api_lite;
		if(!isset($api_lite)){
			if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/config.php');
			else require_once('../webapp/modulos/SAT/config.php');
		}
		else require $api_lite . "modulos/SAT/config.php";

		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d') . 'T' . date('H:i:s', strtotime("-60 minute"));
		//$fecha = date('Y-m-d') . 'T' . date('H:i:s');
		//echo $fecha;

		$logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
		$logo = $this->queryArray($logo);
		$r3 = $logo["rows"][0];

		$azurian = array();
		//echo $bloqueo.'??';
		if ($bloqueo == 0) {
			//echo 'entro a bloqueo';
			$queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
			$returnConfiguracion = $this->queryArray($queryConfiguracion);
			if ($returnConfiguracion["total"] > 0) {
				$r = (object) $returnConfiguracion["rows"][0];

				/* DATOS OBLIGATORIOS DEL EMISOR
				================================================================== */
				$rfc_cliente = $r->rfc;

				$parametros['EmisorTimbre'] = array();
				$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Rfc'] = utf8_decode($r->rfc);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				/*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
				$parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
				$parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
				$parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
				$parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
				$parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
				$parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
				$parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
				$parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
				$parametros['EmisorTimbre']['CP'] = $r->cp; */
				$cer_cliente = $pathdc . '/' . $r->cer;
				$key_cliente = $pathdc . '/' . $r->llave;
				$pwd_cliente = $r->clave;
			} else {

				$JSON = array('success' => 0,
					'error' => 1001,
					'mensaje' => 'No existen datos de emisor.');
				global $api_lite;
				if(!isset($api_lite)) echo json_encode($JSON);
				else return $JSON;
				exit();
			}
		}
		/* Observaciones pdf */
		$azurian['Observacion']['Observacion'] = $mensaje;



		  /*----- Factura de Consumo --------*/

		$sucursal = $this -> sucursal();

		$queryConsumo = "SELECT consumo from com_configuracion where id_sucursal =".$sucursal;
		$resConsumo = $this->queryArray($queryConsumo);
		$consumo = $resConsumo['rows'][0]['consumo'];

		if($consumo == 1 || $consumo=='1'){
			unset($nn2);
			unset($nnf);
			$precioSiniva = bcdiv($parametros['DatosCFD']['Total'],'1',2) / 1.16;
			//echo $ivaCon;
			$elIva = bcdiv($precioSiniva,'1',6) * 0.16;
		   // $subTotalCon = $parametros['DatosCFD']['Total'] - $ivaCon;
			$parametros['DatosCFD']['Subtotal'] = bcdiv($precioSiniva,'1',2);
			$nn2["IVA"]["16"] = bcdiv($elIva,'1',2);
			$nnf["IVA"]["16"]['Valor'] = bcdiv($elIva,'1',2);
			$parametros['DatosCFD']['Total'] = bcdiv($precioSiniva,'1',2) + bcdiv($elIva,'1',2);
			//echo $nn2["IVA"]["16.00"];
			//echo 'sub'.$parametros['DatosCFD']['Subtotal'];
		}
		//exit();
		/* IMPUESTOS
		============================================================== */
		if ($nn2 == '') {
			$nn2["IVA"]["0.0"]["Valor"] = 0.00;
		}
		if ($nnf == '') {
			$nnf["IVA"]["0.0"]["Valor"] = 0.00;
		}
		$nn = $nn2;
		$azurian['nn']['nn'] = $nn;
		$azurian['nnf']['nnf'] = $nnf;
		$azurian['org']['logo'] = $r3["logoempresa"];
	   /* CORREO RECEPTOR
		============================================================== */
		$azurian['Correo']['Correo'] = $Email;

		/* Datos Basicos
		============================================================== */
		//$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		$azurian['Basicos']['Moneda'] = $parametros['DatosCFD']['Moneda'];
		if($parametros['DatosCFD']['Moneda']!='MXN'){
			$azurian['Basicos']['TipoCambio'] = $parametros['DatosCFD']['TipoCambio'];
		}

		$azurian['Basicos']['MetodoPago'] = $parametros['DatosCFD']['MetodoPago'];
		$azurian['Basicos']['NumCtaPago'] = $parametros['DatosCFD']['NumCtaPago'];
		$azurian['Basicos']['LugarExpedicion'] = $parametros['DatosCFD']['LugarExpedicion'];
		$azurian['Basicos']['Version'] = '4.0';
		$azurian['Basicos']['Serie'] = $parametros['DatosCFD']['Serie']; //No obligatorio
		$azurian['Basicos']['Folio'] = $parametros['DatosCFD']['Folio']; //No obligatorio
		$azurian['Basicos']['Fecha'] = $fecha;
		$azurian['Basicos']['Sello'] = '';
		$azurian['Basicos']['FormaPago'] = $parametros['DatosCFD']['FormaPago'];
		$azurian['Basicos']['TipoDeComprobante'] = $parametros['DatosCFD']['TipoDeComprobante'];
		$azurian['Basicos']['Exportacion'] = '01';
		$azurian['tipoFactura'] = 'factura';
		$azurian['Basicos']['NoCertificado'] = '';
		$azurian['Basicos']['Certificado'] = '';
		$str_subtotal = number_format($parametros['DatosCFD']['Subtotal'], 6);
		$str_subtotal = bcdiv($parametros['DatosCFD']['Subtotal'],'1',6);
		$azurian['Basicos']['SubTotal'] = str_replace(",", "", $str_subtotal);
		if($descGen > 0){
			//$azurian['Basicos']['Descuento'] =bcdiv($descGen,'1',2);
			$azurian['Basicos']['Descuento'] =bcdiv($descGen,'1',2);
		}
		//$azurian['Basicos']['Descuento'] = number_format($parametros['DatosCFD']['Descuento'],2);
		//$azurian['Basicos']['Descuento'] = str_replace(',', '', $azurian['Basicos']['Descuento']);
		$str_total = bcdiv($parametros['DatosCFD']['Total'],'1',6);
		$str_total = str_replace(',', '',$str_total);
		//$str_total = $str_total - 0.01;
		//$str_total = number_format($str_total,0).'.00';  //Comente para que Salgan Decimales Normalmente
		$str_total = bcdiv($str_total,'1',6);
		$azurian['Basicos']['Total'] = str_replace(",", "", $str_total);

		/* UUID relacionados
		============================================================== */
		//$relacion,$uuidRelacion;
		$relacionXML = '';
		$relacionCAD = '';
		if($relacion!='0'){
			$relacionCAD .= $relacion.'|';
			//$relacionXML .='<cfdi:CfdiRelacionados TipoRelacion="'.$relacion.'">';
			$relacionXML .="<cfdi:CfdiRelacionados TipoRelacion='".$relacion."'>";
			$rel = explode(';', $uuidRelacion);
			foreach ($rel as $key => $value) {
				if($value!=''){
					$relacionCAD .= $value.'|';
			 		//$relacionXML.='<cfdi:CfdiRelacionado UUID="'.$value.'"/>';
			 		$relacionXML.="<cfdi:CfdiRelacionado UUID='".$value."'/>";
				}
			 }
			 $relacionXML.='</cfdi:CfdiRelacionados>';
		}
		$azurian['Realacionados']['xml'] = $relacionXML;
		$azurian['Realacionados']['cadena'] = $relacionCAD;
		/*============================================================== */
		/* Datos Emisor
		============================================================== */
		$azurian['Emisor']['RegimenFiscal'] = strtoupper($parametros['EmisorTimbre']['RegimenFiscal']);
		$azurian['Emisor']['Rfc'] = strtoupper($parametros['EmisorTimbre']['Rfc']);
		$azurian['Emisor']['Nombre'] = strtoupper($parametros['EmisorTimbre']['RazonSocial']);

		/* Datos Fiscales Emisor
		============================================================== */

		/*$azurian['FiscalesEmisor']['calle'] = $parametros['EmisorTimbre']['Calle'];
		$azurian['FiscalesEmisor']['noExterior'] = $parametros['EmisorTimbre']['NumExt'];
		$azurian['FiscalesEmisor']['colonia'] = $parametros['EmisorTimbre']['Colonia'];
		$azurian['FiscalesEmisor']['localidad'] = $parametros['EmisorTimbre']['Ciudad'];
		$azurian['FiscalesEmisor']['municipio'] = $parametros['EmisorTimbre']['Municipio'];
		$azurian['FiscalesEmisor']['estado'] = $parametros['EmisorTimbre']['Estado'];
		$azurian['FiscalesEmisor']['pais'] = $parametros['EmisorTimbre']['Pais'];
		$azurian['FiscalesEmisor']['codigoPostal'] = $parametros['EmisorTimbre']['CP']; */
		/* Datos Receptor
		============================================================== */

		$azurian['Receptor']['Rfc'] = strtoupper($parametros['Receptor']['RFC']);
		$azurian['Receptor']['Nombre'] = strtoupper($parametros['Receptor']['RazonSocial']);
		$azurian['Receptor']['UsoCFDI'] = $parametros['Receptor']['UsoCFDI'];
		$azurian['Receptor']['DomicilioFiscalReceptor'] = $parametros['Receptor']['DomicilioFiscalReceptor'];
		$azurian['Receptor']['RegimenFiscalReceptor'] = $parametros['Receptor']['RegimenFiscalReceptor'];

		$conceptosOri = '';
		$conceptos = '';
		$conceptosx = '';
		$conceptosxR = '';
		$conceptosR = '';
		$ivasConceptos = 0;
		$iepsConceptos = 0;
		$conceptosOriR = '';
		$baseIVAGeneral = 0;
		$base0 = 0;
		$base16 = 0;
		$sucursal = $this -> sucursal();

		/*-----Factura de Consumo ---------*/
		/*============================================================== */
		if($consumo == 1 || $consumo=='1'){
			unset($conceptosDatos);
			$selec = "SELECT cat.clave from c_consumo cat , com_configuracion f where f.id_consumo_clave = cat.id and f.id_sucursal = ".$sucursal;
			$reCla  = $this->queryArray($selec);
				$conceptosDatos[0]["ClaveProdServ"] = $reCla['rows'][0]['clave'];
				$conceptosDatos[0]["ClaveUnidad"] = 'E48';
				$conceptosDatos[0]["Cantidad"] = 1;
				$conceptosDatos[0]["Unidad"] = 'zz';
				$conceptosDatos[0]["NoIdentificacion"] = '';
				$conceptosDatos[0]["ValorUnitario"] = $precioSiniva;
				$conceptosDatos[0]["Descripcion"] = "Consumo de Alimentos y bebidas";
				//$conceptosDatos[$x]['Importe'] = ($producto->cantidad * $producto->precio - str_replace(",", "", $producto->descuento) );
				$conceptosDatos[0]['Importe'] = $precioSiniva;
				$conceptosDatos[0]['cargos33']->IVA = array('tasa' =>16 ,'importe' => $elIva);
				$consumoTotal =  $precioSiniva*1;

		}
		$descGen2  = 0;
		/*============================================================== */
		//se emepiza a llenar los conceptos en el arreglo de azurian
		foreach ($conceptosDatos as $key => $value) {
			$value['Descripcion'] = preg_replace("/'/", "&apos;", $value['Descripcion']);
			$value['Descripcion'] = preg_replace('/"/', "&quot;", $value['Descripcion']);
		   // $value['Descripcion'] = preg_replace('("|\')', "&apos;", $value['Descripcion']);
			$value['Descripcion'] = eregi_replace("[\n|\r|\n\r]", " ", $value['Descripcion']);
			$value['Descripcion'] = trim($value['Descripcion']);
			if($value['Unidad']==''){
				$value['Unidad']= "No Aplica";
			}
			if($value['ClaveProdServ'] == ''){
				$value['ClaveProdServ'] = '01010101';
			}
			$conceptosOri.='|'.$value['ClaveProdServ'].'|';
			//$conceptosOri.=$value['NoIdentificacion'] . '|';
			$conceptosOri.=$value['Cantidad'] . '|';
			$conceptosOri.=$value['ClaveUnidad'] . '|';
			$conceptosOri.=$value['Unidad'] . '|';
			$conceptosOri.=$value['Descripcion'] . '|';
			if($value['Descuento'] > 0){
				//$ValorUni = (str_replace(",", "", number_format($value['Importe'],2))+ str_replace(",", "", number_format($value['Descuento'],2))) / $value['Cantidad'];
				$ValorUni = (str_replace(",", "", bcdiv($value['Importe'],'1',2))+ str_replace(",", "", bcdiv($value['Descuento'],'1',2))) / $value['Cantidad'];
				//$Impor =  str_replace(",", "", number_format($value['Importe'],2))+ str_replace(",", "", number_format($value['Descuento'],2));
				$Impor =  str_replace(",", "", bcdiv($value['Importe'],'1',6))+ str_replace(",", "", bcdiv($value['Descuento'],'1',6));


				//$conceptosOri.=str_replace(",", "", number_format($ValorUni,2)) . '|';
				$conceptosOri.=str_replace(",", "", bcdiv($ValorUni,'1',6)) . '|';
				//$conceptosOri.=str_replace(",", "", number_format($Impor,2)). '|';
				$conceptosOri.=str_replace(",", "", bcdiv($Impor,'1',6)). '|';
				//$conceptosOri.=str_replace(",", "", number_format($value['Descuento'],2)). '|';
				$conceptosOri.=str_replace(",", "", bcdiv($value['Descuento'],'1',2)). '|';
				//$des  = ' Descuento="'.str_replace(",", "", number_format($value['Descuento'],2)).'"';
				$des  = " Descuento='".str_replace(",", "", bcdiv($value['Descuento'],'1',2))."'";
				$descGen2 += bcdiv($value['Descuento'],'1',2);

			}else{
				//$ValorUni = str_replace(",", "", number_format($value['ValorUnitario'],2));
				$ValorUni = str_replace(",", "", bcdiv($value['ValorUnitario'],'1',6));
				//$Impor =  str_replace(",", "", number_format($value['Importe'],2));
				$Impor =  str_replace(",", "", bcdiv($value['Importe'],'1',6));
				//$conceptosOri.=str_replace(",", "", number_format($value['ValorUnitario'],2)) . '|';
				$conceptosOri.=str_replace(",", "", bcdiv($value['ValorUnitario'],'1',6)) . '|';
				//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)). '|';
				$conceptosOri.=str_replace(",", "", bcdiv($value['Importe'],'1',6)). '|';
				$conceptosOri.='02|';
				$des = '';
			}



			//$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' ClaveUnidad='".$value['ClaveUnidad']."' Cantidad='" . $value['Cantidad'] . "' Unidad='" . $value['Unidad'] . "' NoIdentificacion='".$value['NoIdentificacion']."' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", number_format($value['ValorUnitario'],2)) . "' Importe='" . str_replace(",", "", number_format($value['Importe'],2)) . "'>";
			//$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' Cantidad='" . $value['Cantidad'] . "' ClaveUnidad='".$value['ClaveUnidad']."' Unidad='" . $value['Unidad'] . "' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", number_format($ValorUni,2)) . "' Importe='" . str_replace(",", "", number_format($Impor,2)) . "'".$des.">";
			$conceptos.="<cfdi:Concepto ClaveProdServ='".$value['ClaveProdServ']."' Cantidad='" . $value['Cantidad'] . "' ClaveUnidad='".$value['ClaveUnidad']."' Unidad='" . $value['Unidad'] . "' Descripcion='" . $value['Descripcion'] . "' ValorUnitario='" . str_replace(",", "", bcdiv($ValorUni,'1',6)) . "' Importe='" . str_replace(",", "", bcdiv($Impor,'1',6)) . "'".$des." ObjetoImp='02'>";
			$sumadelosimportes +=bcdiv($Impor,'1',6);
			//$conceptos.='<cfdi:Concepto ClaveProdServ="'.$value['ClaveProdServ'].'" ClaveUnidad="'.$value['ClaveUnidad'].'" Cantidad="'.$value['Cantidad'] .'" Unidad="'.$value['Unidad'] .'" Descripcion="'.$value['Descripcion'].'" ValorUnitario="'.str_replace(",", "", number_format($ValorUni,2)).'" Importe="'.str_replace(",", "", number_format($Impor,2)) .'"'.$des.'>';
			$conceptos.='<cfdi:Impuestos>';
			//echo '---------------------------------------------------------------------';
			//print_r($value['cargos33']);
			//echo '---------------------------------------------------------------------';
			if($value['cargos33']==''){
				$value['cargos33']->IVA = array('tasa' =>'00' ,'importe' => '0.00');
			}
			//print_r($value['cargos33']);
			$iepsCont = 0;
			foreach ($value['cargos33'] as $kim => $valim) {
				///echo '?'.print_r($valim).'?';
				if($kim == 'IEPS' || $kim == 'IVA' || $kim == 'IVAE'){
					if($kim == 'IVA' || $kim == 'IVAE'){
						$immpuesSAT = '002';
						if($valim['tasa']=='0'){
							$valim['tasa']='00';
						}
						//echo $valim['tasa'].')';
						$rsw = strlen($valim['tasa']);
						//echo $rsw;
						if($rsw == 1){
							$valim['tasa'] = '0.'.$valim['tasa'].'0000';
						}else{
							$valim['tasa'] = '0.'.$valim['tasa'].'0000';
						}


						if($des!=''){
							//$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							$base = str_replace(",", "", bcdiv($Impor,'1',6)) - str_replace(",", "", bcdiv($value['Descuento'],'1',6));
							//$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
							$conceptosOri.=str_replace(",", "", bcdiv($base,'1',6)).'|';
						}else{
							//$base = str_replace(",", "", number_format($value['Importe'],2));
							$base = (str_replace(",", "", bcdiv($value['Importe'],'1',6)) + $iepsCont);
							$conceptosOri.=bcdiv($base,'1',6).'|';
						}

						if($valim['nombre']=='IVA EXENTO'){
							$tFacto = 'Exento';
														//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
							$conceptosOri.=$immpuesSAT.'|';
							$conceptosOri.=$tFacto.'';
							//$conceptosOri.='0.160000|';
							//$conceptosOri.=$valim['tasa'].'|';
							//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
							//$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';
							//$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';
							$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", bcdiv($base,'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='".$tFacto."' />";
							$ivasConceptos+=bcdiv($valim['importe'],'1',6);
						}else{
							$tFacto = 'Tasa';

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.=$tFacto.'|';
						//$conceptosOri.='0.160000|';
						$conceptosOri.=$valim['tasa'].'|';
						//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
						//$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';
						$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';

						//echo 'X'.$iepsCont.'X';

						//echo $conceptosOri.'>>';exit();
						//$conceptos.="<cfdi:Traslados>";
						//$conceptos.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='0.160000' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						//$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", number_format($base,2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", bcdiv($base,'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='".$tFacto."' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", bcdiv($valim['importe'],'1',6))."' />";
						$ivasConceptos+=bcdiv($valim['importe'],'1',6);
						if($valim['tasa'] > 0){
							$base16 += str_replace(",", "", bcdiv($base,'1',6));
						}else{
							$base0 += str_replace(",", "", bcdiv($base,'1',6));
						}
						//$conceptos.='<cfdi:Traslado Base="'.str_replace(",", "", number_format($base,2)).'" Impuesto="'.$immpuesSAT.'" TipoFactor="Tasa" TasaOCuota="'.$valim['tasa'].'" Importe="'.str_replace(",", "", number_format($valim['importe'],2)).'" />';
						//$conceptos.="</cfdi:Traslados>";
						}///fin sin exento flujo normal
					}else{
						$immpuesSAT = '003';
						if($valim['tasa']=='0'){
							$valim['tasa']='00';
						}
						//echo $valim['tasa'].')';
						$rsw = strlen($valim['tasa']);
						//echo $rsw;
						if($rsw == 1){
							$valim['tasa'] = '0.0'.$valim['tasa'].'0000';
						}else{
							$valim['tasa'] = '0.'.$valim['tasa'].'0000';
						}

						if($des!=''){
							//$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							//$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
							$base = str_replace(",", "", bcdiv($Impor,'1',6)) - str_replace(",", "", bcdiv($value['Descuento'],'1',2));
							$conceptosOri.=str_replace(",", "", bcdiv($base,'1',2)).'|';
						}else{
							//$base = str_replace(",", "", number_format($value['Importe'],2));
							//$conceptosOri.=$base.'|';
							$base = str_replace(",", "", bcdiv($value['Importe'],'1',6));
							$conceptosOri.=$base.'|';
						}

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOri.=$immpuesSAT.'|';
						$conceptosOri.='Tasa|';
						//$conceptosOri.='0.160000|';
						$conceptosOri.=$valim['tasa'].'|';
						//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
						$conceptosOri.=str_replace(",", "", bcdiv($valim['importe'],'1',6)).'|';

						//$valim['tasa'] = '0.'.$valim['tasa'];
						//$conceptos.="<cfdi:Traslados>";
						//$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", number_format($value['Importe'],2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptosx.="<cfdi:Traslado Base='".str_replace(",", "", bcdiv($value['Importe'],'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", bcdiv($valim['importe'],'1',6))."' />";
						$iepsConceptos+=bcdiv($valim['importe'],'1',6);
						//$conceptos.="</cfdi:Traslados>";
						if($valim['formula']==1){
							$iepsCont = str_replace(",", "", bcdiv($valim['importe'],'1',6));
						}

						//echo '('.$iepsCont.')';
					}

				}else{
					if($kim == 'IVAR' || $kim == 'ISR'){
						if($kim == 'IVAR'){
							$immpuesSAT = '002';
							//$valim['tasa'] = 10.6667;
						}else{
							$immpuesSAT = '001';
						}

						if($valim['tasa']=='0'){
							$valim['tasa']='00';
						}
						//echo $valim['tasa'].')';
						if($valim['tasa']==10.6667){
							$valim['tasa'] = '0.106667';
						}else{
							$rsw = strlen($valim['tasa']);
							if($rsw == 1){
								$valim['tasa'] = '0.0'.$valim['tasa'].'0000';
							}else{
								$valim['tasa'] = '0.'.$valim['tasa'].'0000';
							}
						}



						if($des!=''){
							//$base = str_replace(",", "", number_format($Impor,2)) - str_replace(",", "", number_format($value['Descuento'],2));
							//$conceptosOri.=str_replace(",", "", number_format($base,2)).'|';
							$base = str_replace(",", "", bcdiv($Impor,'1',6)) - str_replace(",", "", bcdiv($value['Descuento'],'1',6));
							$conceptosOriR.=str_replace(",", "", bcdiv($base,'1',2)).'|';
						}else{
							//$base = str_replace(",", "", number_format($value['Importe'],2));
							//$conceptosOri.=$base.'|';
							$base = str_replace(",", "", bcdiv($value['Importe'],'1',6));
							$conceptosOriR.=$base.'|';
						}

						//$conceptosOri.=str_replace(",", "", number_format($value['Importe'],2)).'|';
						$conceptosOriR.=$immpuesSAT.'|';
						$conceptosOriR.='Tasa|';
						//$conceptosOri.='0.160000|';
						$conceptosOriR.=$valim['tasa'].'|';
						//$conceptosOri.=str_replace(",", "", number_format($valim['importe'],2)).'|';
						$conceptosOriR.=str_replace(",", "", number_format(round($valim['importe'],2),2)).'|';

						//$conceptosxR.="<cfdi:Retencion Base='".str_replace(",", "", number_format($base,2))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format($valim['importe'],2))."' />";
						$conceptosxR.="<cfdi:Retencion Base='".str_replace(",", "", bcdiv($base,'1',6))."' Impuesto='".$immpuesSAT."' TipoFactor='Tasa' TasaOCuota='".$valim['tasa']."' Importe='".str_replace(",", "", number_format(round($valim['importe'],2),2))."' />";
						$ivarConceptos+=round($valim['importe'],2);
					}
				}
			}
			$iepsCont = 0;
			//echo $conceptosOri;
			$conceptosOri.=$conceptosOriR;
			$conceptosOriR = '';
			if($conceptosx!=''){
				$conceptos.="<cfdi:Traslados>".$conceptosx."</cfdi:Traslados>";
				$conceptosx = '';
			}
			if($conceptosxR!=''){
				$conceptos.="<cfdi:Retenciones>".$conceptosxR."</cfdi:Retenciones>";
				$conceptosxR = '';
			}
			//exit();
			$conceptos.="</cfdi:Impuestos>";
			$conceptos.='</cfdi:Concepto>';
			//$subTotImportes += (float) str_replace(",", "", number_format($value['Importe'],2));
			$subTotImportes += (float) str_replace(",", "", bcdiv($value['Importe'],'1',2));
		}
		//exit();
		//print_r($azurian);
		/*echo $conceptos;
		print_r($conceptosDatos);
		exit(); */
				//////////impuestos azurian
		$ivas = '';
		$tisr = 0.00;
		$tiva = 0.00;
		$tieps = 0.00;

		$oriisr = '';
		$oriiva = '';

		$isr = '';
		$iva = '';
		$azurian['Conceptos']['conceptos'] = $conceptos;
		$azurian['Conceptos']['conceptosOri'] = $conceptosOri;

		$traslads = '';
		$retenids = '';
		$haytras = 0;
		$hayret = 0;
		$trasladsimp = 0.00;
		$retenciones = 0.00;
		$trasxml = '';
		$retexml = '';
		$sumImTras = 0;
		$sumRetHijos = 0;
		//print_r($nn);
		//exit();
		foreach ($nn as $clave => $imm) {
			krsort($imm);
			if ($clave == 'IEPS' || $clave == 'IVA'   ) {

				$haytras = 1;
				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
						$tasaOcu = '0.000000';
					}else{

						$rsw = strlen($clavetasa);

						if($rsw==1){
							$tasaOcu = '0.0'.$clavetasa.'0000';
						}else{
							$tasaOcu = '0.'.$clavetasa.'0000';
						}

					}
					if ($clave == 'IEPS') {
						//$tieps+=number_format($val, 2, '.', '');
						$tieps+=bcdiv($val,'1',2);
						$clIm = '003';
						$traslads.='|' . $clIm . '|';
						$traslads.='Tasa|';
					   // $traslads.='' . $clavetasa . '|';
						//$traslads.='0.160000|';
						$traslads.=$tasaOcu.'|';
						//$traslads.=number_format($val, 2, '.', '');
						if($tasaOcu == '0.000000'){
							$traslads.=bcdiv(0.000000,'1',2);
						}else{
							//$traslads.=bcdiv($iepsConceptos,'1',6);
							//$traslads.=round($iepsConceptos,2);
							$traslads.=str_replace(',', '', number_format(round($iepsConceptos,2),2));
						}

						//$trasladsimp+=number_format($val, 2, '.', '');
						//echo 'val='.$val.'<br>sumat='.$trasladsimp;
						if($tasaOcu == '0.000000'){

						}else{
							$trasladsimp+=bcdiv($iepsConceptos,'1',6);
						}
						if($tasaOcu == '0.000000'){
								$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv(0.000000,'1',2)."' />";

						}else{
							//$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv($iepsConceptos,'1',6)."' />";
							$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".str_replace(',', '', number_format(round($iepsConceptos,2),2))."' />";
							$sumImTras += round($iepsConceptos,2);
						}
					}
					if ($clave == 'IVA' || $clave == 'IVA' || $clave == 'IVAE') {
							//$tiva+=number_format($val, 2, '.', '');
							$tiva+=bcdiv($val,'1',2);
							$clIm = '002';
						if($tasaOcu == '0.000000'){
							$traslads.='|' . number_format($base0,2,'.', ''). '|';
						}else{
							$traslads.='|' . number_format($base16,2,'.', ''). '|';
						}

						$traslads.='|' . $clIm . '|';
						$traslads.='Tasa|';
						$traslads.=$tasaOcu.'|';
						if($tasaOcu == '0.000000'){
							$traslads.=bcdiv(0.000000,'1',2);
						}else{
							//$traslads.=bcdiv($ivasConceptos,'1',6);
							$traslads.=round($ivasConceptos,2);
						}

						//$trasladsimp+=number_format($val, 2, '.', '');
						//echo 'val='.$val.'<br>sumat='.$trasladsimp;
						if($tasaOcu == '0.000000'){

						}else{
							$trasladsimp+=bcdiv($ivasConceptos,'1',6);
						}

						if($tasaOcu == '0.000000'){
								$trasxml.="<cfdi:Traslado Base='".number_format($base0,2,'.', '')."' Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv(0.000000,'1',2)."' />";

						}else{
							//$trasxml.="<cfdi:Traslado Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".bcdiv($ivasConceptos,'1',6)."' />";
							$trasxml.="<cfdi:Traslado Base='".number_format($base16,2,'.', '')."' Impuesto='".$clIm ."' TipoFactor='Tasa' TasaOCuota='".$tasaOcu."' Importe='".round($ivasConceptos,2)."' />";
							$sumImTras += round($ivasConceptos,2);
						}

					}


				}
			} elseif ($clave == 'ISR' || $clave == 'IVAR') {
				$hayret = 1;

				foreach ($nn[$clave] as $clavetasa => $val) {
					if($clavetasa=='0.0'){
						$val = 0;
						$tasaOcu = '0.000000';
					}else{

						$rsw = strlen($clavetasa);

						if($rsw==1){
							$tasaOcu = '0.0'.$clavetasa.'0000';
						}else{
							$tasaOcu = '0.'.$clavetasa.'0000';
						}

					}
					if ($clave == 'ISR') {
						//$tieps+=number_format($val, 2, '.', '');
						$tieps+=bcdiv($val,'1',6);
						$clIm = '001';
					}
					if ($clave == 'IVAR') {
						//$tiva+=number_format($val, 2, '.', '');
						$tiva+=bcdiv($val,'1',6);
						$clIm = '002';
					}

					//$tisr+=number_format($val, 2, '.', '');
					$tisr+=bcdiv($val,'1',6);
					$retenids.='|' . $clIm . '|';

					$retenids.=str_replace(',', '', round($val,2));
					$retenciones+=bcdiv($val,'1',2);

					$retexml.="<cfdi:Retencion Impuesto='".$clIm ."' Importe='".str_replace(',', '', round($val,2))."' />";
					$sumRetHijos+=round($val,2);

				}
			}
		}
		////fin del foreach nn
		//echo '///////'.$traslads;

		$azurian['Impuestos']['totalImpuestosIeps'] = $tieps;

		if ($haytras == 1) {
			$iva.='<cfdi:Traslados>' . $trasxml . '</cfdi:Traslados>';
		} else {
			/*$traslads.='|IVA|';
			$traslads.='0.00|';
			$traslads.='0.00';
			$trasladsimp = '0.00';
			$iva.="<cfdi:Traslados><cfdi:Traslado Impuesto='IVA' tasa='0.00' importe='0.00' /></cfdi:Traslados>"; */
			//$iva.='<cfdi:Traslados><cfdi:Traslado Impuesto="IVA" tasa="0.00" importe="0.00" /></cfdi:Traslados>';
		}
		if ($hayret == 1) {
			$isr.='<cfdi:Retenciones>' . $retexml . '</cfdi:Retenciones>';
		}
		  if($hayret == 1){
			// ****** nuevo rst
			$cadRet = '|'.str_replace(',', '', round($tisr,2));

			if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
				$cadRet = '|'.str_replace(',', '', round($ivarConceptos,2));
			}else{
				$cadRet = '|'.str_replace(',', '', bcdiv($ivarConceptos,'1',2));
			}


			//$cadRet = '|'.str_replace(',', '', number_format($tisr,2));
		  }else{
			$cadRet = '';
		  }


	    //echo $retenids.'   --- '.$cadRet;exit();
		  $azurian['Impuestos']['isr'] = $retenids.$cadRet;

		  $azurian['Impuestos']['iva'] = $traslads . '|' . bcdiv(round($trasladsimp,2),'1',2);
		  $azurian['Impuestos']['iva'] = $traslads . '|' .bcdiv(round($sumImTras,2),'1',2);

		  if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
		   	$azurian['Impuestos']['totalImpuestosRetenidos'] = round($ivarConceptos,2);
			}else{
				$azurian['Impuestos']['totalImpuestosRetenidos'] = bcdiv($ivarConceptos,'1',2);
			}

		  $azurian['Impuestos']['totalImpuestosTrasladados'] = bcdiv(round($sumImTras,2),'1',2);

		 $totalXML =  round($sumadelosimportes,2) + round($ivasConceptos,2) + round($iepsConceptos,2) ;
		 $subtotalXML = round($sumadelosimportes,2);

		/*-------------------------------------------------------------------*/
		  $azurian['Basicos']['SubTotal']  = bcdiv($subtotalXML,'1',2);
		  if($descGen > 0){
		  	$azurian['Basicos']['Descuento'] = $descGen2;
		  	if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
		  		$azurian['Basicos']['Total']  = ($totalXML - bcdiv(round($ivarConceptos,2),'1',2)) - bcdiv($descGen2,'1',2);
		  	}else{
		  		$azurian['Basicos']['Total']  = ($totalXML - bcdiv($ivarConceptos,'1',2)) - bcdiv($descGen2,'1',2);
		  	}
		  }else{
		  	if($sumRetHijos > bcdiv($ivarConceptos,'1',2)){
		  		$azurian['Basicos']['Total']  = $totalXML - bcdiv(round($ivarConceptos,2),'1',2);
		  	}else{
		  		$azurian['Basicos']['Total']  = $totalXML - bcdiv($ivarConceptos,'1',2);
		  	}
		  }

		  $azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2);

		$ivas.=$isr . $iva;

		$azurian['Impuestos']['ivas'] = $ivas;
			$soloExento = 0;
		  	foreach ($nn as $clave => $imm) {
				if ($clave != 'IVAE') {
					$soloExento = 1;

				}
			}
			if($soloExento == 0){
				$azurian['Impuestos']['iva'] = '';
			}
			$xmlC = '';
			$cadC = '';
			if($camposComple!=''){
				$th = "SELECT * from app_complementos where id=1";
    			$rles = $this->queryArray($th);
    			$xmlcomple = $rles['rows'][0]['xml'];
    			$cadenacomple = $rles['rows'][0]['cadena'];

				$string = explode('&',$camposComple);
				$cont = 0;
				foreach ($string as $key => $value) {

					$straux = explode('=',$value);
					$campo = '@'.$straux[0];
					$valor = str_replace('+', ' ', $straux[1]);
					if($cont > 0){
						$xmlC = str_replace($campo.' ',$valor,$xmlC);
						$cadC = str_replace($campo.' ',$valor,$cadC);
					}else{
						$xmlC = str_replace($campo.' ',$valor,$xmlcomple);
						$cadC = str_replace($campo.' ',$valor,$cadenacomple);
					}
					$cont++;
				}
			}

			$azurian['CompleImpues']['xmlComple']=$xmlC;
			$azurian['CompleImpues']['cadenaComple']=$cadC;

				$traslados = 0;
		    	$retenidos = 0;
		    	$chain = '';
		    	$xmlLoc = '';
		    	$sessionArray = $this->object_to_array($_SESSION['caja']);
		    	foreach ($sessionArray as $key => $value) {
					if($key !='cargos' && $key!='descGeneral' && $key!='pedido' && $key!='idorden'){
						foreach ($value['cargosLocales'] as $key2 => $value2) {
							if($key2 != 'MR'){
								$_SESSION['caja']['cargos']['impuestos'][$key2]=bcdiv($value2['importe'],'1',10) * 1;
								$_SESSION['caja']['cargos']['impuestosPorcentajes'][$value2['nombre']] = bcdiv($value2['importe'],'1',10) * 1;
								$_SESSION['caja']['cargos']['impuestosFactura'][$key2][$value2['tasa']] = bcdiv($value2['importe'],'1',10) * 1;
								$_SESSION['caja']['cargos']['impuestosPdf'][$key2][$value2['tasa']]['Valor'] = bcdiv($value2['importe'],'1',10) * 1;
		 						$traslados = $value2['importe'];
		 						$xmlLoc.="<implocal:TrasladosLocales ImpLocTrasladado='".$value2['nombre']."' TasadeTraslado='".$value2['tasa']."' Importe='".bcdiv($value2['importe'],'1',2) ."' />";
		 						$chain.= '|'.$value2['nombre'].'|'.$value2['tasa'].'|'.bcdiv($value2['importe'],'1',2);
							}else{
								$_SESSION['caja']['cargos']['impuestos'][$key2]=bcdiv($value2['importe'],'1',2) * 1;
								$_SESSION['caja']['cargos']['impuestosPorcentajes'][$value2['nombre']] = bcdiv($value2['importe'],'1',2) * 1;
								$_SESSION['caja']['cargos']['impuestosFactura'][$key2][$value2['tasa']] = bcdiv($value2['importe'],'1',2) * 1;
								$_SESSION['caja']['cargos']['impuestosPdf'][$key2][$value2['tasa']]['Valor'] = bcdiv($value2['importe'],'1',2) * 1;
								$retenidos = $value2['importe'];
								$xmlLoc.="<implocal:RetencionesLocales ImpLocRetenido='".$value2['nombre']."' TasadeRetencion='".$value2['tasa']."' Importe='".bcdiv($value2['importe'],'1',2) ."' />";
								$chain.= '|'.$value2['nombre'].'|'.$value2['tasa'].'|'.bcdiv($value2['importe'],'1',2);
							}
						}
					}
				}
				$tieneLocales = bcdiv($retenidos,'1',2) + bcdiv($traslados,'1',2);
				if($tieneLocales > 0){
					$azurian['CompleImpues']['xmlComple']="<implocal:ImpuestosLocales xmlns:implocal='http://www.sat.gob.mx/implocal' xsi:schemaLocation='http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd' version='1.0' TotaldeRetenciones='".bcdiv($retenidos,'1',2)."' TotaldeTraslados='".bcdiv($traslados,'1',2)."'>".$xmlLoc."</implocal:ImpuestosLocales>";
					$azurian['CompleImpues']['cadenaComple'] = '|1.0|'.bcdiv($retenidos,'1',2).'|'.bcdiv($traslados,'1',2).$chain;
					$azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2) - bcdiv($retenidos,'1',2);
					$azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2) + bcdiv($traslados,'1',2);
					$azurian['Basicos']['Total']  = bcdiv( $azurian['Basicos']['Total'],'1',2);
				}

		//print_r($azurian);exit();//
		//echo json_encode($azurian);

		unset($_SESSION['pagos-caja']);
		unset($_SESSION['caja']);

		
		//require_once('../../modulos/lib/nusoap.php');
		//require_once('../../modulos/SAT/funcionesSAT.php');
		if($pac==2){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])) require_once('../../modulos/SAT/funcionesSAT40.php');
				else require_once('../webapp/modulos/SAT/funcionesSAT2_api.php');
			}
			else require $api_lite . "modulos/SAT/funcionesSAT2_api.php";
		}else if($pac==1){
			global $api_lite;
			if(!isset($api_lite)){
				if(!isset($_REQUEST["netwarstore"])){
					require_once('../../modulos/lib/nusoap.php');
					require_once('../../modulos/SAT/funcionesSAT40.php');
				}
				else{
					require_once('../webapp/modulos/lib/nusoap.php');
					require_once('../webapp/modulos/SAT/funcionesSAT_api.php');
				}
			}
			else{
				require_once($api_lite . 'modulos/lib/nusoap.php');
				require_once($api_lite . 'modulos/SAT/funcionesSAT_api.php');
			}
		}
		if(isset($_REQUEST["netwarstore"]) || isset($api_lite)) return $JSON;


	}

} ///fin de la clase
?>
