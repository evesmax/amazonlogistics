<?php

require('controllers/sobrerecibo.php');
require("models/dispersion.php");

class Dispersion extends Sobrerecibo
{
	
	public $SobrereciboModel;
	public $Dispersion;
	
	function __construct()
	{
		
		$this->DispersionModel = new DispersionModel();
		$this->SobrereciboModel = $this->DispersionModel;
		$this->DispersionModel->connect();	
	}
	
	function __destruct()
	{
		
		$this->DispersionModel->close();
	}

	function dispersion(){
		$cargarDatosDispersos=$this->DispersionModel->cargarDatosDispersos();
		
		require("views/prenomina/dispersion.php");
	}

	function cargaDeDatos(){
		
		$tipoPago          = $this->DispersionModel->tipoPago();
		$nominaAutorizada  = $this->DispersionModel->nominaAutorizada();
       
		
		

		require("views/prenomina/nuevadispersion.php");
	}

function cargardatosperiodo(){


	$cargardatosperiodo   = $this->DispersionModel->cargaDeDatos($_REQUEST['tipoperiodo']);
	if($cargardatosperiodo>0){
		while($e = $cargardatosperiodo->fetch_object()){
		echo"<tr>
		<td style='width:50px'>";
		if ($e->tipocuenta==0){ 
	 	echo"<input type='checkbox' style='width:50px;height:20px;'
	 	 	 class=check' disabled='true'>";
	 	}else{
	 	echo"<input  type='checkbox' style='width:50px;height:20px;'  
	 		 class='check'>";
	 		} 
		echo "</td>
					<td style='display: none;' class='tabledata idEmpleado'>".$e->idEmpleado."</td>
					<td class='tabledata'>".$e->codigo."</td>
					<td class='tabledata'>".$e->nombreEmpleado." ".$e->apellidoPaterno." ".$e->apellidoMaterno."</td>
					<td style='text-align:right' class='importe tabledata'>".number_format($e->total,2,'.',',')."</td> 
					<td class='bancorecep tabledata'>".$e->Clave."</td>";
					echo"<td class='tabledata'>";
						if ($e->tipocuenta==1 || $e->tipocuenta==3) {
								echo '0'.$e->tipocuenta;
							}else{
								echo $e->tipocuenta;
							} 
							echo"</td>
					<td class='numeroCuenta tabledata'>".$e->numeroCuenta."
					</td>	
					</tr>";
		}
	} 
//echo "<script language=\"JavaScript\" src=\"js/dispersion.js\"></script>";
}


	function actualizaStatus(){

		 echo $actualizaStatus=$this->DispersionModel->actualizaStatus($_REQUEST['empleId'],$_REQUEST['nominadesc'],$_REQUEST['consecutivo'],$_REQUEST['fechainicio'],$_REQUEST['txtfecha'],$_REQUEST['tipopago'], $_REQUEST['tableData']);

	}


	function accionEliminarDispersion(){ 
		
		echo $accionEliminarDispersion = $this->DispersionModel->accionEliminarDispersion($_REQUEST['idEmpleado'],$_REQUEST['idnomp']);
	}
	}

	?>


