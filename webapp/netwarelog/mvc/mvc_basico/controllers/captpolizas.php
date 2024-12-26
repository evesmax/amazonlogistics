<?php
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/captpolizas.php");

class CaptPolizas extends Common
{
	public $CaptPolizasModel;

	function __construct()
	{
		//Se crea el objeto que instancia al modelo que se va a utilizar

		$this->CaptPolizasModel = new CaptPolizasModel();
		$this->CaptPolizasModel->connect();
	}

	function __destruct()
	{
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this->CaptPolizasModel->close();
	}

	
	function Capturar()
	{
		$numPoliza 			=	 $this->CaptPolizasModel->getLastNumPoliza();
		$Exercise 			= 	 $this->CaptPolizasModel->getExerciseInfo();
		$Ex 				=	 $Exercise->fetch_assoc();
		
		
		require('views/captpolizas/capturapolizas.php');
	}	
}


?>
