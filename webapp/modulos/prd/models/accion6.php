<?php

class Accion6Model extends OrdenPrdModel {
	
	function saveLote($no_lote,$fecha_caducidad,$idop,$paso,$accion,$idap,$fechacad,$fechafab){
		if(!$fechacad){
		date_default_timezone_set("Mexico/General");
		$fechacad = date('Y-m-d H:i:s');
		}
		$sql = "INSERT INTO app_producto_lotes (no_lote, fecha_fabricacion, fecha_caducidad)
			VALUES
				( '$no_lote', '$fechafab', '$fechacad');";	
		$idlote= $this->insert_id($sql);
		if($idlote>0){
			$sql = "INSERT INTO prd_lotes_op (idop, idlote)
					VALUES
					( $idop, $idlote);";
			$sql .="INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$fechacad',$idap);";			
			if($this->dataTransact($sql)){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
}
?>