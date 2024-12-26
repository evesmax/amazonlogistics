<?php

class Accion2Model extends OrdenPrdModel {
	
	function savePaso2($idsProductos, $accion, $idop, $paso, $clotes, $idprod, $almacen, $idap) {
		$idusr = $_SESSION['accelog_idempleado'];
		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		if ($clotes > 0) {
			//Valida Session de lotes
			$cslote = count($_SESSION['v_rePr'][1]);

			if ($clotes != $cslote) {
				echo 'nolote';
				exit();
			}
		}
		$myQuery = "SELECT invprod from prd_configuracion WHERE id=1;";
		$config = $this -> queryArray($myQuery);
		if ($config['total'] > 0) {
			$invprod = $config['rows'][0]['invprod'];
		} else {
			$invprod = 0;
		}

		if ($invprod == 1) {
			$tt = 3;
			$txtt = ' Apartado ';
		} else {
			$tt = 0;
			$txtt = ' ';
		}

		$myQuery = "SELECT * from prd_utilizados WHERE id_oproduccion='$idop' LIMIT 1;";
		$rr = $this -> queryArray($myQuery);
		if ($rr['total'] > 0) {
			$last_id = $rr['rows'][0]['id'];
		} else {
			$myQuery = "INSERT INTO prd_utilizados (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
			$last_id = $this -> insert_id($myQuery);
		}

		$myQuery = "DELETE FROM prd_utilizados_detalle WHERE id_utilizado='$last_id';";
		$query = $this -> query($myQuery);

		if ($last_id > 0) {
			$cad = '';

			$productos = explode('___', $idsProductos);

			foreach ($productos as $k => $v) {
				$exp = explode('>#', $v);
				$idPadre = $exp[0];
				$idHijo = $exp[1];
				$cant = $exp[2];
				$elcost = 0;
				$elunit = 0;
				$cardexmulti = '';

				if ($clotes > 0 && array_key_exists($idHijo, $_SESSION['v_rePr'][1])) {
					$caracteristica = 0;
					$ciclo = explode(',', $_SESSION['v_rePr'][1][$idHijo][$caracteristica]['cantslotes']);
					$cadlotillo = '';

					foreach ($ciclo as $kk => $vv) {
						$desgl_cl = explode('-', $vv);
						if ($desgl_cl[2] != 0) {
							$elcost = 0;
							$elunit = 0;
							$cardexmulti .= "('" . $idHijo . "','0','" . $desgl_cl[0] . "','" . $desgl_cl[2] . "','" . $elcost . "','" . $desgl_cl[1] . "','0','" . $creacion . "','" . $idusr . "','0','" . $elunit . "','Orden de produccion / usarInsumo -" . $idop . "','0'),";

							$cadlotillo .= $desgl_cl[0] . '=>' . $desgl_cl[2] . ',';
						}
					}

					$cadlotillotrim = trim($cadlotillo, ',');

				} else {

					$cadlotillotrim = null;
					$cardexmulti .= "('" . $idHijo . "','0','0','" . $cant . "','" . $elcost . "','" . $almacen . "','0','" . $creacion . "','" . $idusr . "','" . $tt . "','" . $elunit . "','Orden de produccion /" . $txtt . "usarInsumo -" . $idop . "','0')";

				}

				$cadrdcardextrim = trim($cardexmulti, ',');
				$myQuery = "INSERT INTO app_inventario_movimientos (id_producto,id_pedimento,id_lote,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) VALUES " . $cadrdcardextrim . ";";
				$this -> query($myQuery);

				$cad .= "('" . $last_id . "','" . $idHijo . "','" . $cant . "','" . $cadlotillotrim . "'),";

			}
			$cadtrim = trim($cad, ',');
			$myQuery = "INSERT INTO prd_utilizados_detalle (id_utilizado,id_insumo,cantidad,lotes) VALUES " . $cadtrim . ";";
			$query = $this -> query($myQuery);
		}

		$myQuery = "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
		$query = $this -> query($myQuery);

		echo $last_id;

	}

}
?>