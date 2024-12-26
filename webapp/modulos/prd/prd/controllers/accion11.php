<?php

require ("controllers/ordenprd.php");
//Carga el modelo para este controlador
require ("models/accion11.php");

class Accion11 extends OrdenPrd {
	public $Accion11Model;
	public $OrdenPrdModel;

	function __construct() {
		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> Accion11Model = new Accion11Model();
		$this -> OrdenPrdModel = $this -> Accion11Model;
		$this -> Accion11Model -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> Accion11Model -> close();
	}

	function viewAccion11() {
		$resInsumos = $this -> OrdenPrdModel -> productosOpExplosionProceso($_REQUEST['idop'], $_REQUEST['idap']);
		if ($resInsumos['total'] > 0) {
			foreach ($resInsumos['rows'] as $k => $v) {
				$existencias = $this -> OrdenPrdModel -> getExistencias($v['idProducto'], '0');
				if ($existencias[0]['cantidad'] == null) {
					$g = 0;
				} else {
					$g = $existencias[0]['cantidad'];
				}
				$resInsumos['rows'][$k]['existen'] = $g;

				$usados = $this -> OrdenPrdModel -> getUsados($_REQUEST['idop'], $_REQUEST['idap'], $v['idProducto'], $_REQUEST['accion']);
				if ($usados['total'] > 0) {
					$resInsumos['rows'][$k]['usados'] = $usados['rows'][0]['tot_real'];
				} else {
					$resInsumos['rows'][$k]['usados'] = 0;
				}

			}
		}
		$hist11 = $this -> Accion11Model -> historial11($_REQUEST['idop'], $_REQUEST['idap'], 0);
		if ($hist11['total'] > 0) {
			$wed = $hist11['rows'];
		} else {
			$wed = 0;
		}
		$rsqlpaso4 = $this -> OrdenPrdModel -> sqlPaso4($_REQUEST['idop']);
		/*verificar reabasto*/
		$config = $this->OrdenPrdModel->config();
		$autotodas = $this->Accion11Model->reabastoAutorizado11($_REQUEST['idop'], 0, $_REQUEST['idap']);
		require ("views/acciones/accion11.php");
	}

	function a_guardarPaso11() {
		$idsProductos = trim($_POST['idsProductos']);
		$paso = trim($_POST['paso']);
		$accion = trim($_POST['accion']);
		$idop = trim($_POST['idop']);
		$idap = trim($_POST['idap']);
		$idemp = trim($_POST['idemp']);
		$idp = trim($_POST['idp']);

		echo $this -> Accion11Model -> savePaso11($idsProductos, $accion, $idop, $paso, $idap, $idemp, $_REQUEST['opc'], $_REQUEST['ppf']);

	}
	function a_finalizar(){
      $id=$_POST['id'];
      $this->Accion11Model->finalizar($id);
    }
	

}
?>