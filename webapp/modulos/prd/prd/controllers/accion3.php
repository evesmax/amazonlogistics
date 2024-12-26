<?php
require("controllers/ordenprd.php");
//Carga el modelo para este controlador
require("models/accion3.php");

class Accion3 extends OrdenPrd
{
  public $Accion3Model;
  public $OrdenPrdModel;

  function __construct()
  {

//Se crea el objeto que instancia al modelo que se va a utilizar
    $this->Accion3Model  = new Accion3Model();
    $this->OrdenPrdModel = $this->Accion3Model;
    $this->Accion3Model->connect();
  }

  function __destruct()
  {
//Se destruye el objeto que instancia al modelo que se va a utilizar
    $this->Accion3Model->close();
  }

  function viewAccion3(){

    require("views/acciones/accion3.php");
  }
  function a_clipasoAccion3(){

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
        $rsqlpaso3 = $this->OrdenPrdModel->sqlPaso3($idop,$v['idProducto']);
        if($rsqlpaso3->num_rows>0){
          $rowSqlpaso3 = $rsqlpaso3->fetch_assoc();
          $resInsumos['rows'][$k]['peso']=$rowSqlpaso3['pesoUti'];
        }else{
          $resInsumos['rows'][$k]['peso']=0.00;
        }
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

  function a_guardarPaso3(){

    $idsProductos=trim($_POST['idsProductos']);
    $paso=trim($_POST['paso']);
    $accion=trim($_POST['accion']);
    $idop=trim($_POST['idop']);
    $idap=trim($_POST['idap']);

    echo $result = $this->Accion3Model->savePaso3($idsProductos,$accion,$idop,$paso,$idap); 

  }
  function url_bascula(){
   echo $url_bascula = $this->Accion3Model->url_bascula();

  }

}

?>