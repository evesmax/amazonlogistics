<?php

class Accion19Model extends OrdenPrdModel {

	function actividad($idoprd, $idope, $idap, $opc) {
		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d H:i:s');
		if ($opc == 1) {
			$sql = "INSERT INTO prd_regactividad (id_oproduccion, id_operador, id_pa, f_ini)VALUES( $idoprd, $idope, $idap, '$fecha');";
			if ($this -> query($sql)) {
				return $fecha;
			} else {
				return 0;
			}
		} else {
			$sql = "UPDATE prd_regactividad SET f_fin='$fecha' WHERE id='$id'; ";
			if ($this -> query($sql)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	function finalizaActividad($idop, $paso, $accion, $idap) {
		date_default_timezone_set("Mexico/General");
		$fecha = date('Y-m-d H:i:s');
		$sql = "UPDATE prd_regactividad SET f_fin='$fecha' WHERE id_pa='$idap'; ";
		$sql .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$fecha',$idap);";
		if ($this -> dataTransact($sql) === true) {
			return 1;
		} else {
			return 0;
		}
	}

	function infoActividad($idop, $idap) {

		$sql = $this -> query("select * from prd_regactividad where id_oproduccion=$idop and id_pa=$idap;");
		if ($sql -> num_rows > 0) {
			return $sql -> fetch_array();
		} else {
			return 0;
		}
	}

}
?>