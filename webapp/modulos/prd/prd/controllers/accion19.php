<?php
require ("controllers/ordenprd.php");
require ("models/accion19.php");

class Accion19 extends OrdenPrd {
	public $Accion19Model;
	public $OrdenPrdModel;

	function __construct() {

		$this -> Accion19Model = new Accion19Model();
		$this -> OrdenPrdModel = $this -> Accion19Model;
		$this -> Accion19Model -> connect();
	}

	function __destruct() {
		$this -> Accion19Model -> close();
	}

	function viewAccion19() {
		$personal = $this -> OrdenPrdModel -> sqlPaso4($_REQUEST['idop']);
		$actividad = $this -> Accion19Model -> infoActividad($_REQUEST['idop'], $_REQUEST['idap']);
		require ("views/acciones/accion19.php");
	}

	function actividad() {
		if ($_REQUEST['opc'] == 1) {//opc 1 sera para iniciar
			echo $this -> Accion19Model -> actividad($_REQUEST['idop'], $_REQUEST['operador'], $_REQUEST['idap'], $_REQUEST['opc']);
		} else {
			echo $this -> Accion19Model -> finalizaActividad($_REQUEST['idop'], $_REQUEST['paso'], $_REQUEST['accion'], $_REQUEST['idap']);
		}
	}

	function tiempo() {
		date_default_timezone_set("Mexico/General");
		$fecha1 = new DateTime($_REQUEST['fecha']);
		$fecha2 = new DateTime(date('Y-m-d H:i:s'));
		$intervalo = $fecha1 -> diff($fecha2);
		echo $intervalo -> format('%H:%i:%s');
	}

}
?>