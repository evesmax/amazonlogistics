<?php
require ("controllers/ordenprd.php");
//Carga el modelo para este controlador
require ("models/accion17.php");

class Accion17 extends OrdenPrd {
	public $Accion17Model;
	public $OrdenPrdModel;

	function __construct() {

		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> Accion17Model = new Accion17Model();
		$this -> OrdenPrdModel = $this -> Accion17Model;
		$this -> Accion17Model -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> Accion17Model -> close();
	}

	function viewAccion17() {
		require ("views/acciones/accion17.php");
	}

	function a_guardarPaso17() {

		$paso = trim($_POST['paso']);
		$accion = trim($_POST['accion']);
		$idop = trim($_POST['idop']);
		$idap = trim($_POST['idap']);

		$idp = trim($_POST['idp']);
		$cant = trim($_POST['lacant']);

		$ac = $this -> OrdenPrdModel -> costoOpInv($idop);
		if ($ac['total'] > 0) {
			$costo = $ac['rows'][0]['costo'];
		} else {
			$costo = 0;
		}

		$al = $this -> OrdenPrdModel -> getAlmacen($idop);
		if ($al['total'] > 0) {
			$almacen = $al['rows'][0]['idalmacen'];
		} else {
			$almacen = 0;
		}

		echo $this -> Accion17Model -> savePaso17($accion, $idop, $paso, $idp, $costo, $cant, $almacen, $idap);
	}

}
?>