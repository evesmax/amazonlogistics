<?php

class CierreanualModel extends PrenominaModel {

	function ultimaNominaPeriodo($idtipop) {
		$sql = $this -> query("select fechafin,ejercicio from nomi_nominasperiodo where idtipop=$idtipop order by idnomp desc limit 1");
		$s = $sql -> fetch_object();
		return $s -> fechafin."/".$s->ejercicio;
	}

	function periodoCierre($idtipo) {

		$tipoperiodo = $this -> query("select * from nomi_tiposdeperiodos where idtipop=" . $idtipo);
		return $tipoperiodo -> fetch_object();

	}
	/*ultima nomina del ano autorizada
	 * podra solo crear el nuevo periodo*/
	function ultimoAutNominaAno($idtipop){
		$sql = $this->query("select n.idnomp from nomi_nominasperiodo n where
			n.idtipop =$idtipop and finejercicio=1 and year(fechafin) = year(CURDATE()) and mes =12 and month(fechafin) = month(CURDATE()) and autorizado=1;");
		if($sql->num_rows>0){
			return 1;
		}else{
			return 0;
		}
	}
	function ejercicioGenerado($idtipop){
		 $sql = $this->query("select idnomp from nomi_nominasperiodo where ejercicio = (year(CURDATE())+1) and idtipop= $idtipop;");
		 if($sql->num_rows>0){
		 	return 1;
		 }else{
		 	return 0;
		 }
	}

}
?>
