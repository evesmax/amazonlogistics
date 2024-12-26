<?php

require ("controllers/ordenprd.php");
require ("models/accion10.php");

class Accion10 extends OrdenPrd {
	public $Accion10Model;
	public $OrdenPrdModel;

	function __construct() {
		$this -> Accion10Model = new Accion10Model();
		$this -> OrdenPrdModel = $this -> Accion10Model;
		$this -> Accion10Model -> connect();
	}
	function __destruct() {
		$this -> Accion10Model -> close();
	}
	function viewAccion10() {
		$almacenes = $this->Accion10Model->almacenes();
		$empacadosOrden = $this->Accion10Model->empacadostotal($_REQUEST['idop']);
		$encajas = $this->Accion10Model->encajas($_REQUEST['idop']);
		$orden = $this -> OrdenPrdModel -> productosOp($_REQUEST['idop'],1);
		$campos = $orden -> fetch_array();
		$cantOrd = $campos['cantidad'];
		$merma = $this -> Accion10Model -> mermaTotal($_REQUEST['idop'], $_REQUEST['idp']);
		$cantTotalOrden = $cantOrd - $merma;
		
		$sobrante = 0;
		if($campos['cantidadxempaque']>=1){//PARA SI SE HACEN CAJAS A PARTIR DE EMPAQUES
			if($campos['empaquexcaja']>=1){
				if(( $empacadosOrden % $campos['empaquexcaja']) == 0){
					$cajas = $empacadosOrden / $campos['empaquexcaja'];
				}else{
					$paqprevio = ($empacadosOrden / $campos['empaquexcaja']);
					$sep = explode(".", $paqprevio);
					$cajas = $sep[0];
					$sobrante = $empacadosOrden - ( $sep[0] * $campos['empaquexcaja']);
					$sobrante = $sobrante * $campos['cantidadxempaque'];
				}
			}else{
				$cajas = 0;
			}
		}else{//SI NO TIENE EMPAQUES
			if($campos['empaquexcaja']>=1){// PERO SI LLENADO PARA CAJA ENTONCES SERAN PIEZAS DIRECTAS A CAJA SIN EMPAQUE
				if(( $cantTotalOrden % $campos['empaquexcaja']) == 0){
					$cajas = $cantTotalOrden / $campos['empaquexcaja'];
				}else{
					$paqprevio = ($cantTotalOrden / $campos['empaquexcaja']);
					$sep = explode(".", $paqprevio);
					$cajas = $sep[0];
					$sobrante = $cantTotalOrden - ( $sep[0] * $campos['empaquexcaja']);
				}
			}else{//SI NO HAY NADA EN EMPAQUES DE CAJA ENTONCES NO HACE CAJAS
				$cajas = 0;
			}
			
		}
		require ("views/acciones/accion10.php");
	}
	function guardarPesoCaja(){
		echo $this->Accion10Model->guardarPesoCaja($_REQUEST['idop'], $_REQUEST['empxcaja'], $_REQUEST['peso'],$_REQUEST['numcaja']);
	}
	function a_guardarPaso10(){
		echo $this->Accion10Model->savePaso10($_REQUEST['accion'], $_REQUEST['idop'], $_REQUEST['paso'], $_REQUEST['idap'],$_REQUEST['idp'],$_REQUEST['sobrante'],$_REQUEST['almacen']);
	}
	
}