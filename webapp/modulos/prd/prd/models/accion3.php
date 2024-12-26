<?php

class Accion3Model extends OrdenPrdModel{

	function savePaso3($idsProductos,$accion,$idop,$paso,$idap){

		date_default_timezone_set("Mexico/General");
		$creacion=date('Y-m-d H:i:s');


		$myQuery = "SELECT * from prd_peso WHERE id_oproduccion='$idop' LIMIT 1;";
		$rr = $this->queryArray($myQuery);
		if($rr['total']>0){
			$last_id=$rr['rows'][0]['id'];
		}else{
			$myQuery = "INSERT INTO prd_peso (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
			$last_id = $this->insert_id($myQuery);
		}

		$myQuery = "DELETE FROM prd_peso_detalle WHERE id_peso='$last_id';";
// $query = $this->query($myQuery);


		if($last_id>0){
			$cad='';
			$productos = explode('___', $idsProductos);
			foreach ($productos as $k => $v) {
				$exp=explode('>#', $v);
				$idPadre=$exp[0];
				$idHijo=$exp[1];
				$cant=$exp[2];

				$cad.="('".$last_id."','".$idHijo."','".$cant."'),";

			}
			$cadtrim = trim($cad, ',');
			$myQuery .= "INSERT INTO prd_peso_detalle (id_peso,id_insumo,peso) VALUES ".$cadtrim.";";
//$query = $this->query($myQuery);
		}

		$myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
		if($this->dataTransact($myQuery) === true){
			return $last_id;
		}else{
			return 0;
		}
//echo $last_id;

	}
	function url_bascula(){
		
		$myQuery = "SELECT url_bascula from app_config_ventas";
		$url = $this->queryArray($myQuery);
		if($url['total']>0){
			$urlreturn=$url['rows'][0]['url_bascula'];
	}
	return $urlreturn;
}

}

?>
