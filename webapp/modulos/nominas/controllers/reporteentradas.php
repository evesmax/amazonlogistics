<?php
require('controllers/nominalibre.php');
require("models/reporteentradas.php");

class reporteEntradas extends Nominalibre{

public $reporteEntradasModel;
public $NominalibreModel;

function __construct(){

$this->reporteEntradasModel = new reporteEntradasModel();
$this->NominalibreModel = $this->reporteEntradasModel;
$this->reporteEntradasModel->connect();
}

function __destruct(){

$this->reporteEntradasModel->close();
}

//  R E P O R T E   D E   E N T R A D A S   D E   E M P L E A D O S 

function reporteEntradas(){

$empleados = $this->reporteEntradasModel->empleados();
$sucursal  = $this->reporteEntradasModel->sucursales();

require ("views/reportes/reporteEntradas.php");

}

function llenarReporteEntradas(){

$reporteEntradas = $this->reporteEntradasModel->entradaSalidasEmple(
$_REQUEST['fechainicio'],$_REQUEST['fechafin'],$_REQUEST['empleado'],$_REQUEST['sucursal']);

require ("views/reportes/llenarreporteEntradas.php");  
}
}
?>