<?php

class Accion14Model extends OrdenPrdModel {

	function tipoMerma() {
		$sql = $this -> query("select * from app_merma_tipo;");
		if ($sql -> num_rows > 0) {
			return $sql;
		} else {
			return 0;
		}
	}

	function savePaso14($idsProductos, $accion, $idop, $paso, $almacen, $idap) {

		$idusr = $_SESSION['accelog_idempleado'];
		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		$myQuery3 = "INSERT INTO prd_merma (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion','$idusr');";
		$last_id = $this -> insert_id($myQuery3);

		$cpro = explode('___', $idsProductos);
		$cpro = count($cpro);
		$myQuery2 = "INSERT INTO app_merma (fecha,usuario,productos,importe) VALUES ('$creacion','$idusr','$cpro',0);";
		$last_id_merma = $this -> insert_id($myQuery2);

		if ($last_id > 0) {
			$cad = '';
			$cardexmulti = '';
			$cardmerma = '';
			$productos = explode('___', $idsProductos);
			foreach ($productos as $k => $v) {
				$exp = explode('>#', $v);
				$idPadre = $exp[0];
				$idHijo = $exp[1];
				$cant = $exp[2];
				$tipomerma = $exp[3];
				$observa = $exp[4];

				$cad .= "('" . $last_id . "','" . $idHijo . "','" . $cant . "'),";

				$elcost = 0;
				$elunit = 0;
				$cardexmulti .= "('" . $idHijo . "','0','0','" . $cant . "','" . $elcost . "','" . $almacen . "','0','" . $creacion . "','" . $idusr . "','0','" . $elunit . "','Registro de mermas / mermas -" . $idop . "','0'),";

				$cardmerma .= "('" . $last_id_merma . "','" . $idHijo . "','" . $cant . "',0,'" . $idusr . "','" . $almacen . "','" . $observa . "','0',$tipomerma,'0','0'),";

			}
			$cadtrim = trim($cad, ',');
			$myQuery .= "INSERT INTO prd_merma_detalle (id_merma,id_insumo,cantidad) VALUES " . $cadtrim . ";";

			$cardmerma = trim($cardmerma, ',');
			$myQuery .= "INSERT INTO app_merma_datos (id_merma,id_producto,cantidad,precio,usuario,almacen,observaciones,caracteristicas,tipo,idlote,idproveedor) VALUES " . $cardmerma . ";";
		}

		$myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
		if ($this -> dataTransact($myQuery) === true) {
			return $last_id;
		} else {
			return 0;
		}

	}

}
?>
