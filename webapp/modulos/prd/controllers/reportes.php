<?php
require ("controllers/ordenprd.php");
require ("models/reportes.php");

class Reportes extends OrdenPrd {
	public $ReportesModel;
	public $OrdenPrdModel;

	function __construct() {

		$this -> ReportesModel = new ReportesModel();
		$this -> OrdenPrdModel = $this -> ReportesModel;
		$this -> ReportesModel -> connect();
	}

	function __destruct() {
		$this ->  ReportesModel -> close();
	}
	function viewReabasto(){
		$lista = $this->ReportesModel->listareabastoGnr();
		require("views/reportes/reabasto.php");
	}
	function vertInsumos(){
		$existencias = Array();
		$insumos = $this->ReportesModel->insumosReabasto($_REQUEST['idread']);
		$exist= $this->ReportesModel->cantidadAlmacenOrden($_REQUEST['idop']);
		while($x = $exist->fetch_object()){
			$existencias[$x->id_producto] = $x->cantidad;
		}
		require('views/reportes/vistainsumos.php');
	}
	function autorizaReabasto(){
		if($_REQUEST['opc'] == 1){
			echo $this->ReportesModel->autorizaTodo($_REQUEST['idop']);
		}else{
			echo $this->ReportesModel->autorizaInsumo($_REQUEST['idop'],$_REQUEST['insumo']);
		}
	}
	function cancelarReabasto(){
		echo $this->ReportesModel->cancelarReabasto($_REQUEST['idop']);
	}
}
?>