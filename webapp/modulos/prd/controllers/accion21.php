<?php

require ("controllers/ordenprd.php");
require ("models/accion21.php");

class Accion21 extends OrdenPrd {
	public $Accion21Model;
	public $OrdenPrdModel;

	function __construct() {
		$this -> Accion21Model = new Accion21Model();
		$this -> OrdenPrdModel = $this -> Accion21Model;
		$this -> Accion21Model -> connect();
	}

	function __destruct() {
		$this -> Accion21Model -> close();
	}

	function viewAccion21() {
		$numcajas = $this->Accion21Model->totalencajas($_REQUEST['idop']);
		require ("views/acciones/accion21.php");
	}
	function a_guardarPaso21(){
		echo $this->Accion21Model->savePaso21($_REQUEST['accion'], $_REQUEST['idop'], $_REQUEST['paso'], $_REQUEST['idap']);
	}
	function etiqueta(){
		$caja = $this->Accion21Model->infocaja($_REQUEST['idop']);
		$prod = $this->Accion21Model->infoPrd($_REQUEST['idop']);
		$arraycaja = array();
		$meses = array (1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
		while($ca = $caja->fetch_object()){
			$nu = str_pad($ca->numcaja, 3, "0", STR_PAD_LEFT);
			$fecha = strtotime($prod['fecha_caducidad']);
			$mes = date("m", $fecha);
			$anio = date("Y", $fecha);
			
			array_push($arraycaja,[$_REQUEST['idop'].$nu,$meses[intval($mes)]." ".$anio,$prod['no_lote'] ,$prod['nombre'],$ca->peso]);
		}
		$array = $arraycaja;
		require('../appministra/views/produccion/generacionetiqueta.php');
		//print_r ($arraycaja);
	}
	
}