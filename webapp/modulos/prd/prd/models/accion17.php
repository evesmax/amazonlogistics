<?php

class Accion17Model extends OrdenPrdModel {
	
	function savePaso17($accion, $idop, $paso, $idp, $costo, $cant, $almacen, $idap) {
		session_start();
		$idusr = $_SESSION['accelog_idempleado'];

		$myQuery = "SELECT id_lote FROM prd_lote_detalles WHERE id_oproduccion='$idop';";
		$rr = $this -> queryArray($myQuery);
		if ($rr['total'] > 0) {
			$idlote = $rr['rows'][0]['id_lote'];
		} else {
			$idlote = 0;
		}

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		/* BLOQUE DESAPARTADO APARTADOS */
		$myQuery = "SELECT * FROM app_inventario_movimientos WHERE referencia='Orden de produccion / Apartado usarInsumo -" . $idop . "';";
		$rr = $this -> queryArray($myQuery);
		if ($rr['total'] > 0) {
			$cad = '';
			foreach ($rr['rows'] as $k => $v) {
				$cad .= "('" . $v['id_producto'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $v['id_almacen_origen'] . "', '" . $v['id_almacen_destino'] . "', '" . $creacion . "', '" . $idusr . "', '0', '" . $v['costo'] . "', 'Orden de produccion / usarInsumo -" . $idop . "', '1', '" . $v['id_lote'] . "'),";
			}

			$cad = trim($cad, ',');
			$qq = "INSERT INTO  app_inventario_movimientos (id_producto,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,id_lote) VALUES " . $cad . ";";
			$query = $this -> query($qq);

			$qqq = "UPDATE app_inventario_movimientos SET estatus=0 WHERE referencia='Orden de produccion / Apartado usarInsumo -" . $idop . "';";
			$this -> query($qqq);

		}
		/* FIN BLOQUE DESAPARTADO APARTADOS */
		/* BLOQUE DESAPARTADO APARTADOS ReabastoInsumo */
		$myQuery = "SELECT * FROM app_inventario_movimientos WHERE referencia='Orden de produccion / Apartado ReabastoInsumo -" . $idop . "';";
		$rr = $this -> queryArray($myQuery);
		if ($rr['total'] > 0) {
			$cad = '';
			foreach ($rr['rows'] as $k => $v) {
				$cad .= "('" . $v['id_producto'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $v['id_almacen_origen'] . "', '" . $v['id_almacen_destino'] . "', '" . $creacion . "', '" . $idusr . "', '0', '" . $v['costo'] . "', 'Orden de produccion / ReabastoInsumo -" . $idop . "', '1', '" . $v['id_lote'] . "'),";
			}

			$cad = trim($cad, ',');
			$qq = "INSERT INTO  app_inventario_movimientos (id_producto,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,id_lote) VALUES " . $cad . ";";
			$query = $this -> query($qq);

			$qqq = "UPDATE app_inventario_movimientos SET estatus=0 WHERE referencia='Orden de produccion / Apartado ReabastoInsumo -" . $idop . "';";
			$this -> query($qqq);

		}
		/* FIN BLOQUE DESAPARTADO APARTADOS  ReabastoInsumo*/

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		//verificamos si existe merma en esta orden de produccion
		$sql = $this -> query("select sum(cantidad) as merma from prd_merma_detalle m inner join prd_merma pm on pm.id_oproduccion=$idop and pm.id=id_merma where id_insumo=$idp");
		if ($sql -> num_rows > 0) {
			$s = $sql -> fetch_object();
			if ($s -> merma > 0) {
				$cant -= $s -> merma;
			}
		}

		$referencia = 'Orden de produccion-' . $idop;
		$importe = $cant * $costo;
		//entrada
		$tipo_traspaso = 1;
/*Verificar el origen de la orden
 * si es 2 que es pedidos sera registro el producto como apartado*/
 		$origen = $this->origenorden($idop);
 		if($origen == 2){
 			//apartado
 			$tipo_traspaso = 3;
 		}

		$myQuery = "INSERT INTO  app_inventario_movimientos (id_producto,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,id_lote) VALUES ( '" . $idp . "','" . $cant . "','" . $importe . "','0','" . $almacen . "','" . $creacion . "','" . $idusr . "',$tipo_traspaso,'" . $costo . "','" . $referencia . "','1','" . $idlote . "') ;";
		$myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";

		if ($this -> dataTransact($myQuery) === true) {
			return 1;
		} else {
			return 0;
		}

	}
	function origenorden($idop){
		$sql = $this->query("SELECT origen from prd_orden_produccion where id=$idop;");
		$origen = $sql->fetch_object();
		return $origen->origen;
	}

}
?>