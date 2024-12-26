
<?php
//ini_set('display_errors', 1);
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/reportes.php");

class Reportes extends Common
{
	public $ReportesModel;

	function __construct()
	{
		//Se crea el objeto que instancia al modelo que se va a utilizar

		$this->ReportesModel = new ReportesModel();
		$this->ReportesModel->connect();
	}

	function __destruct()
	{
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this->ReportesModel->close();
	}
 
	function gasto()
	{
		//EXTRAER los datos
		$clientes = $this->ReportesModel->get_clientes();
		$matriculas = $this->ReportesModel->get_matriculas();
		//$formasPago = $this->ReportesModel->get_formas_pago();
		//

		require('views/reportes/gasto.php');
	}

	function gastoDatos()
	{
		$datos = $this->ReportesModel->gastoDatos($_POST);
		require('views/reportes/gastoDatos.php');
	}
	function cortes(){
		$sucursales = $this->ReportesModel->get_sucursales();
		$cortes = $this->ReportesModel->cortes();
		require('views/reportes/cortes.php');
	}
	function obtenCortes($json = 0){
		$init = $_POST['desde'];
        $end = $_POST['hasta'];
        $idcorte = $_POST['idcorte'];
        $onlyShow = $_POST['show'];
        $iduser=$_POST['user'];

        $resultado = $this->ReportesModel->getCut($init, $end, $onlyShow, $iduser, $idcorte);
        echo json_encode($resultado);

	}
	function cortesTerminados(){
		$idSuc = $_POST['idSuc'];
		$data = $this->ReportesModel->cortesTerminados($idSuc);
		echo json_encode($data);
	}
}
