<?php

class Accion10Model extends OrdenPrdModel{
	function empacadostotal($idop){
		$sql = $this->query("select count(id) num from prd_empaque where idop=$idop;");
		$e = $sql ->fetch_object();
		return $e->num;
	}
	function encajas($idop){
		$sql = $this->query("select count(id) num from prd_caja_master where idop=$idop;");
		$e = $sql ->fetch_object();
		return $e->num;
	}
	function guardarPesoCaja($idop,$empxcaja,$peso,$numcaja){
		date_default_timezone_set("Mexico/General");
		$fecharegistro = date('Y-m-d H:i:s');
		session_start();
		$idusuario = $_SESSION['accelog_idempleado'];
		$sql = "update  prd_empaque set encaja =1 where idop=$idop and encaja=0  limit $empxcaja;";
		
		$sql .= "INSERT INTO prd_caja_master (idop, fecharegistro, idusuario, empaquexcaja, peso, numcaja)
				VALUES
				($idop, '$fecharegistro', $idusuario, $empxcaja, $peso,$numcaja);";
		if($this->dataTransact($sql) === true){
			return 1;
		}else{
			return 0;
		}
	}
	function savePaso10($accion, $idop, $paso, $idap,$idp,$sobrante, $almacen) {

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		session_start();
		$sql = "";
		$idusr = $_SESSION['accelog_idempleado'];
		if($sobrante>0){
			$referencia = "Orden de produccion / Sobrante producto -$idop";
			$sql.="INSERT INTO  app_inventario_movimientos 
			(id_producto,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,id_lote) 
			VALUES ( '" . $idp . "','" . $sobrante . "',0,0,'" . $almacen . "','" . $creacion . "','" . $idusr . "',1,'0','" . $referencia . "','1',0) ;";
			
		}



		$sql.= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
		if ($this->dataTransact($sql) === true) {
			return 1;
		} else {
			return 0;
		}

	}
	function mermaTotal($idop, $idp) {
		$merma = 0;
		$sql = $this -> query("select sum(cantidad) as merma from prd_merma_detalle m inner join prd_merma pm on pm.id_oproduccion=$idop and pm.id=id_merma where id_insumo=$idp");
		if ($sql -> num_rows > 0) {
			$s = $sql -> fetch_object();
			if ($s -> merma > 0) {
				$merma += $s -> merma;
			}
		}
		return $merma;
	}
	function almacenes() {
		$sql = $this -> query("select id,nombre from app_almacenes where activo=1;");
		if ($sql -> num_rows > 0) {
			return $sql;
		} else {
			return 0;
		}

	}
	
}