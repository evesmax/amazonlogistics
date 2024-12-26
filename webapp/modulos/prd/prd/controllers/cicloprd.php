<?php

require("controllers/ordenprd.php");
//Carga el modelo para este controlador
require("models/cicloprd.php");


class CicloPrd extends OrdenPrd
{
	public $OrdenPrdModel;
	public $CicloPrdModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->CicloPrdModel = new CicloPrdModel();
		$this->OrdenPrdModel = $this->CicloPrdModel;
        $this->CicloPrdModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->CicloPrdModel->close();
    }
	function a_explosionMatCiclo(){
        $idop=$_POST['idop'];
        $resultReq = $this->OrdenPrdModel->editarordenp($idop);
        $lospasos = $this->OrdenPrdModel->listar_pasos_op($idop);

        //SUB ORDENES PROD AGRUPADAS
        $rsqlpaso2 = $this->OrdenPrdModel->buscaAgrupadas($idop);
        if($rsqlpaso2['total']>0){
          $agrupes=$rsqlpaso2['rows'];
        }else{
          $agrupes=0;
        }

        if($lospasos['total']>0){
          $row = $resultReq->fetch_assoc();

          $JSON = array('success' =>1, 'data'=>$lospasos['rows'], 'ddd'=>$row, 'agrupes'=>$agrupes);
        }else{
          $JSON = array('success' =>0);
        }

        echo json_encode($JSON);

    }
}
?>