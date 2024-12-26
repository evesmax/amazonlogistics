<?php
require ("controllers/ordenprd.php");
//Carga el modelo para este controlador
require ("models/accion9.php");

class Accion9 extends OrdenPrd {
	public $Accion9Model;
	public $OrdenPrdModel;

	function __construct() {

		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> Accion9Model = new Accion9Model();
		$this -> OrdenPrdModel = $this -> Accion9Model;
		$this -> Accion9Model -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> Accion9Model -> close();
	}

	function viewAccion9() {
		require ("views/acciones/accion9.php");
	}

	function a_guardarPaso9() {

		$paso = trim($_POST['paso']);
		$accion = trim($_POST['accion']);
		$idop = trim($_POST['idop']);
		$idap = trim($_POST['idap']);
		$idp = trim($_POST['idp']);

		echo $this -> Accion9Model -> savePaso9($accion, $idop, $paso, $idap);
	}

}
?>