<?php

class Accion21Model extends OrdenPrdModel {
	function totalencajas($idop){
		$sql = $this->query("select count(id) num from prd_caja_master where idop=$idop;");
		$e = $sql ->fetch_object();
		return $e->num;
	}
	function savePaso21($accion, $idop, $paso, $idap) {

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		$myQuery = "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
		if ($this -> query($myQuery)) {
			return 1;
		} else {
			return 0;
		}

	}
	function infoPrd($idop){
		$sql = $this->query("select p.*,pr.nombre,pl.no_lote,pl.fecha_caducidad from prd_orden_produccion_detalle p
				inner join app_productos pr on pr.id=p.id_producto
				inner join prd_lotes_op l on l.idop=p.id_orden_produccion
				inner join app_producto_lotes pl on pl.id=l.idlote
				where p.id_orden_produccion=$idop;");
	 return $sql->fetch_array();
	}
	function infocaja($idop){
		$sql = $this->query("select * from prd_caja_master where idop=$idop;");
		return $sql;
	}
}