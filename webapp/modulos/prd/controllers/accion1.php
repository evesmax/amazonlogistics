<?php

require("controllers/ordenprd.php");
//Carga el modelo para este controlador
require("models/accion1.php");


class Accion1 extends OrdenPrd
{
    public $Accion1Model;
	public $OrdenPrdModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->Accion1Model = new Accion1Model();
		$this->OrdenPrdModel = $this->Accion1Model;
        $this->Accion1Model->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->Accion1Model->close();
    }
	function viewAccion1(){
		require("views/acciones/accion1.php");
	}
	function a_clipasoAccion1(){
      session_start();
      unset($_SESSION['v_rePr']);
      $idop=$_POST['idop'];
      $paso=$_POST['paso'];
      $accion=$_POST['accion'];
      $idap=$_POST['idap'];

     
        $insumos=array();
        $resInsumos = $this->OrdenPrdModel->productosOpExplosion($idop);
        if($resInsumos['total']>0){
          foreach ($resInsumos['rows'] as $k => $v) {
            $existencias = $this->OrdenPrdModel->getExistencias($v['idProducto'],'0');
            if($existencias[0]['cantidad']==null){
              $g=0;
            }else{
              $g=$existencias[0]['cantidad'];
            }
            $resInsumos['rows'][$k]['existen']=$g;
          }
        }else{
          $JSON = array('success' =>0);
          echo json_encode($JSON);
          exit();
        }

        $JSON = array('success' =>1, 'data'=>$resInsumos['rows']);
        echo json_encode($JSON);
        exit();
      
	}
	function a_guardarPaso1(){
       
      
      $idsProductos=trim($_POST['idsProductos']);
      $paso=trim($_POST['paso']);
      $accion=trim($_POST['accion']);
      $idop=trim($_POST['idop']);
      $idap=trim($_POST['idap']);
      
      echo $this->Accion1Model->savePaso1($idsProductos,$accion,$idop,$paso,$idap); 
      
    }

}

?>