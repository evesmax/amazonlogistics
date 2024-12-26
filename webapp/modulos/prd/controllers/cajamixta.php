<?php

require ("controllers/accion10.php");
require ("models/cajamixta.php");

class Cajamixta extends Accion10 {
	public $CajamixtaModel;
	public $Accion10Model;

	function __construct() {
		$this -> CajamixtaModel = new CajamixtaModel();
		$this -> Accion10Model = $this -> CajamixtaModel;
		$this -> CajamixtaModel -> connect();
	}
	function __destruct() {
		$this -> CajamixtaModel -> close();
	}
	function viewcaja(){
		require ("views/produccion/cajamixta.php");
	
	}
}