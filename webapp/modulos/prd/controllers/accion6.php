<?php
require ("controllers/ordenprd.php");
//Carga el modelo para este controlador
require ("models/accion6.php");

class Accion6 extends OrdenPrd {
	public $Accion6Model;
	public $OrdenPrdModel;

	function __construct() {

		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> Accion6Model = new Accion6Model();
		$this -> OrdenPrdModel = $this -> Accion6Model;
		$this -> Accion6Model -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> Accion6Model -> close();
	}
	function viewAccion6(){
		$datos = $this->OrdenPrdModel->editarordenp($_REQUEST['idop']);
		$datosOrden = $datos->fetch_array();
		require("views/acciones/accion6.php");
	}
	function a_guardarPaso6(){
		echo $this->Accion6Model->saveLote($_REQUEST['lote'], 0, $_REQUEST['idop'],$_REQUEST['paso'],$_REQUEST['accion'],$_REQUEST['idap'],$_REQUEST['fechacad'],$_REQUEST['fechafab']);
	}
}

?>