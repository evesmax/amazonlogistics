<?php
require ("controllers/ordenprd.php");
//Carga el modelo para este controlador
require ("models/accion14.php");

class Accion14 extends OrdenPrd {
	public $Accion14Model;
	public $OrdenPrdModel;

	function __construct() {

		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> Accion14Model = new Accion14Model();
		$this -> OrdenPrdModel = $this -> Accion14Model;
		$this -> Accion14Model -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> Accion14Model -> close();
	}

	function viewAccion14() {
		$tipoMerma = $this -> Accion14Model -> tipoMerma();
		$producto = $this-> OrdenPrdModel -> productosOp($_REQUEST['idop'], 1);
		$p = $producto -> fetch_object();
		require ("views/acciones/accion14.php");
	}
	function a_guardarPaso14(){
       
      
      $idsProductos=trim($_POST['idsProductos']);
      $paso=trim($_POST['paso']);
      $accion=trim($_POST['accion']);
      $idop=trim($_POST['idop']);
      $idap=trim($_POST['idap']);


      
      $idp=trim($_POST['idp']);
	  
	  $al = $this->OrdenPrdModel->getAlmacen($idop); 
        if($al['total']>0){
          $almacen=$al['rows'][0]['idalmacen'];
        }else{
          $almacen=0;
        }
        echo $this->Accion14Model->savePaso14($idsProductos,$accion,$idop,$paso,$almacen,$idap); 
     
	}

}
?>