<?php
//ini_set("display_errors", 1); error_reporting(E_ALL);
class reporteEntradasModel extends NominalibreModel
{

	function empleados(){

		$sql = $this->query("SELECT * from nomi_empleados where salario > 0;");
		return $sql;
	}

	function sucursales(){

		$sql = $this->query("SELECT * from mrp_sucursal;");
		return $sql;
	}


// R E P O R T E   D E   E N T R A D A S   D E   E M P L E A D O S 

	function entradaSalidasEmple($fechaini,$fechafin,$nomEmple,$sucursales){

		$filtroEmpleado    = "";
		$filtroSucursal    = "";

		//echo "fechaini: $fechaini, fechafin: $fechafin, Empleado: $empleados";

		if($nomEmple != '*' &&  $nomEmple != ''){
			$filtroEmpleado = "AND em.idEmpleado = $nomEmple";
		}

		if($sucursales != '*' && $sucursales != ''){
			$filtroSucursal = "AND re.idSuc = $sucursales";
		}


		$sql = $this->query("SELECT em.apellidoPaterno, em.apellidoMaterno,em.nombreEmpleado,em.idtipop,em.idEmpleado,em.codigo,em.nss,em.rfc,em.curp, re.horaentrada,re.iniciocomida,re.fincomida,re.horasalida,re.idEmpleado,re.fecha,re.dia,re.idnomp,re.idregistro,re.idSuc,suc.nombre from nomi_registro_entradas as re inner join nomi_empleados as em on em.idEmpleado=re.idEmpleado 
			left join mrp_sucursal suc on suc.idSuc=re.idSuc where re.fecha between '$fechaini' and '$fechafin' $filtroEmpleado 
			$filtroSucursal order by nombreEmpleado asc,re.fecha asc;");

		return $sql;

	}




}
?>