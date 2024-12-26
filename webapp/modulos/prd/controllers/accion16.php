<?php

require("controllers/ordenprd.php");
require("models/accion16.php");


class Accion16 extends OrdenPrd
{
    public $Accion16Model;
	public $OrdenPrdModel;

    function __construct()
    {
        $this->Accion16Model = new Accion1Model();
		$this->OrdenPrdModel = $this->Accion16Model;
        $this->Accion16Model->connect();
    }

    function __destruct()
    {
        $this->Accion16Model->close();
    }
	function viewAccion16(){
		require("views/acciones/accion16.php");
	}
}