<?php

require ("controllers/ordenprd.php");
require ("models/accion20.php");

class Accion20 extends OrdenPrd {
	public $Accion20Model;
	public $OrdenPrdModel;

	function __construct() {
		$this -> Accion20Model = new Accion20Model();
		$this -> OrdenPrdModel = $this -> Accion20Model;
		$this -> Accion20Model -> connect();
	}

	function __destruct() {
		$this -> Accion20Model -> close();
	}

	function viewAccion20() {
		$almacenes = $this->Accion20Model->almacenes();
		$orden = $this -> OrdenPrdModel -> productosOp($_REQUEST['idop'],1);
		$campos = $orden -> fetch_array();
		$cantOrd = $campos['cantidad'];
		$merma = $this -> Accion20Model -> mermaTotal($_REQUEST['idop'], $_REQUEST['idp']);
		$cantTotalOrden = $cantOrd - $merma;
		$sobrante = 0;
		if($campos['cantidadxempaque']>=1){
			//multiplo
			if(( $cantTotalOrden % $campos['cantidadxempaque']) == 0){
				$paquetes = $cantTotalOrden / $campos['cantidadxempaque'];
			}else{
				$paqprevio = ($cantTotalOrden / $campos['cantidadxempaque']);
				$sep = explode(".", $paqprevio);
				$paquetes = $sep[0];
				$sobrante = $cantTotalOrden - ( $sep[0] * $campos['cantidadxempaque']);
				
			}
			
		}else{
			$paquetes = 0;
		}
		$empacadosOrden = $this->Accion20Model->empacados($_REQUEST['idop']);
		require ("views/acciones/accion20.php");
	}
	function guardarPesoEmp(){
		echo $this->Accion20Model->guardarPesoEmp($_REQUEST['idop'], $_REQUEST['cantxempa'], $_REQUEST['peso'],$_REQUEST['paquete']);
	}
	function a_guardarPaso20(){
		echo $this->Accion20Model->savePaso20($_REQUEST['accion'], $_REQUEST['idop'], $_REQUEST['paso'], $_REQUEST['idap'],$_REQUEST['idp'],$_REQUEST['sobrante'],$_REQUEST['almacen']);
	}

}
