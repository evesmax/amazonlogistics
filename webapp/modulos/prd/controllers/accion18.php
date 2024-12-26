<?php
require ("controllers/ordenprd.php");
require ("models/accion18.php");

class Accion18 extends OrdenPrd {
	public $Accion18Model;
	public $OrdenPrdModel;

	function __construct() {

		$this -> Accion18Model = new Accion18Model();
		$this -> OrdenPrdModel = $this -> Accion18Model;
		$this -> Accion18Model -> connect();
	}

	function __destruct() {
		$this -> Accion18Model -> close();
	}

	function viewAccion18() {
		$existepaso = $this -> Accion18Model -> accion18Existe($_REQUEST['idop'], $_REQUEST['idap']);
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
		$hist11 = $this -> Accion18Model -> historial18($_REQUEST['idop'], $_REQUEST['idap'], 1);
		if ($hist11['total'] > 0) {
			$wed = $hist11['rows'];
		} else {
			$wed = 0;
		}
		$rsqlpaso4 = $this -> OrdenPrdModel -> sqlPaso4($_REQUEST['idop']);
		$solicitante = $this -> OrdenPrdModel -> sqlPaso4($_REQUEST['idop']);
		/*verificar reabasto*/
		$config = $this->OrdenPrdModel->config();
		// reabasto autorizado
		$autoRebasto = $this->Accion18Model->reabastoAutorizado($_REQUEST['idop']);
		require ("views/acciones/accion18.php");
	}

	function a_guardarPaso18() {

		$idsProductos = trim($_POST['idsProductos']);
		$paso = trim($_POST['paso']);
		$accion = trim($_POST['accion']);
		$idop = trim($_POST['idop']);
		$idap = trim($_POST['idap']);
		$idemp = trim($_POST['idemp']);
		$idp = trim($_POST['idp']);

		echo $this -> Accion18Model -> savePaso18($idsProductos, $accion, $idop, $paso, $idap, $idemp, $_REQUEST['opc'], $_REQUEST['ppf']);

	}
	function reabasto(){
		$productos = trim($_POST['productos']);
		$matp = 0;
		if( isset($_REQUEST['idmatp']) ){
			$matp = $_REQUEST['idmatp'];
		}
		echo $this->Accion18Model->rebastoInsumos($productos, $_REQUEST['solicitante'], $_REQUEST['obs'], $_REQUEST['idop'], $_REQUEST['idap'],$matp);
	}

}
?>