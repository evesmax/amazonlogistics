<?php
require('common.php');
require("models/rep_produccion.php");

class Rep_produccion extends Common
{
    public $Rep_ProduccionModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->Rep_ProduccionModel = new Rep_ProduccionModel();
        $this->Rep_ProduccionModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->Rep_ProduccionModel->close();
    }

    function abasto(){

      /* ====== Reporte de abasto
      ========================= */

      //$datos_reporte = $this->Rep_ProduccionModel->reporte_abasto();
      $logo = $this->Rep_ProduccionModel->logo();
      $row =  $this->Rep_ProduccionModel->bandera();
      $bandera=$row['aut_ord_prod'];
      $orden=$row['genoc_sinreq'];
      $datos_reporte = $this->Rep_ProduccionModel->reporteAbasto();
      require('views/produccion/rep_abasto.php');

    }

    function index(){
      echo 'Func:Index';
    }
	function vertInsumos(){
		      $logo = $this->Rep_ProduccionModel->logo();
		$orde = $this->Rep_ProduccionModel->editarordenp($_REQUEST['idop']);
		$datosOrd = $orde->fetch_array();
		$org = $this->Rep_ProduccionModel->organizacion();
		$insumos = $this->Rep_ProduccionModel->insumosOrden($_REQUEST['idop']);
		require('views/produccion/vistainsumos.php');
	}
}