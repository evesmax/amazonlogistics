<?php

require("controllers/ordenprd.php");
//Carga el modelo para este controlador
require("models/accion2.php");


class Accion2 extends OrdenPrd
{
    public $Accion2Model;
	public $OrdenPrdModel;

    function __construct()
    {
        $this->Accion2Model = new Accion2Model();
		$this->OrdenPrdModel = $this->Accion2Model;
        $this->Accion2Model->connect();
    }

    function __destruct()
    {
        $this->Accion2Model->close();
    }
	
	function viewAccion2(){
		require("views/acciones/accion2.php" );
	}
	function a_guardarPaso2(){

		$idsProductos=trim($_POST['idsProductos']);
		$paso=trim($_POST['paso']);
		$accion=trim($_POST['accion']);
		$idop=trim($_POST['idop']);
		$idap=trim($_POST['idap']);
	
		$idp=trim($_POST['idp']);
	
		$clotes=trim($_POST['clotes']);
		$al = $this->OrdenPrdModel->getAlmacen($idop);
		if($al['total']>0){
			$almacen=$al['rows'][0]['idalmacen'];
		}else{
			$almacen=0;
		}

		$result = $this->Accion2Model->savePaso2($idsProductos,$accion,$idop,$paso,$clotes,$idp,$almacen,$idap);
	
	}
	function a_clipasoAccion2(){
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
          $rsqlpaso2 = $this->OrdenPrdModel->sqlPaso2($idop,$v['idProducto']);
          if($rsqlpaso2->num_rows>0){
            $rowSqlpaso2 = $rsqlpaso2->fetch_assoc();
            $resInsumos['rows'][$k]['cantidad2']=$rowSqlpaso2['cantUti'];
          }else{
            $resInsumos['rows'][$k]['cantidad2']=0.00;
          }
        }
      }else{
        $JSON = array('success' =>0);
        echo json_encode($JSON);
        exit();
      }

      $JSON = array('success' =>1, 'data'=>$resInsumos['rows']);
      echo json_encode($JSON);
          
      
	}
	

}
	?>