<?php

class Accion4Model extends OrdenPrdModel {
	
	function savePaso4($idsProductos,$accion,$idop,$paso,$idap){

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		$myQuery = "SELECT * from prd_personal WHERE id_oproduccion='$idop' LIMIT 1;";
		$rr = $this -> queryArray($myQuery);
		if ($rr['total'] > 0) {
			$last_id = $rr['rows'][0]['id'];
		} else {
			$myQuery = "INSERT INTO prd_personal (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
			$last_id = $this -> insert_id($myQuery);
		}

		$myQuery = "DELETE FROM prd_personal_detalle WHERE id_personal='$last_id';";
		//$query = $this->query($myQuery);

		if ($last_id > 0) {
			$cad = '';
			$productos = explode('___', $idsProductos);
			foreach ($productos as $k => $v) {
				$exp = explode('>#', $v);
				$idEmpleado = $exp[0];
				$maq = $exp[1];

				$cad .= "('" . $last_id . "','" . $idEmpleado . "','" . $maq . "'),";

			}
			$cadtrim = trim($cad, ',');
			$myQuery .= "INSERT INTO prd_personal_detalle (id_personal,id_empleado,maquinaria) VALUES " . $cadtrim . ";";
			//$query = $this->query($myQuery);
		}

		$myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
		// $query = $this->query($myQuery);

		// echo $last_id;
		if ($this -> dataTransact($myQuery) === true) {
			return $last_id;
		} else {
			return 0;
		}

	}

}
?>