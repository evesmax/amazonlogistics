<?php
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/config.php");

class Config extends Common{

    //public $ConfigModel;

    function __construct()
    {
      

        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->ConfigModel = new ConfigModel();

    }

    function __destruct()
    {

        //Se destruye el objeto que instancia al modelo que se va a utilizar
        //$this->ConfigModel->close();
    }



    function saveConfig(){
      $opcion=$_POST['opcion'];
      $gap=$_POST['gap'];
      $gaop=$_POST['gaop'];
      $apop=$_POST['apop'];
      $notc=$_POST['notc'];
      $hereda=$_POST['hereda'];
      $insdir=$_POST['insdir'];
      $ocop=$_POST['ocop'];
      $ocsinr=$_POST['ocsinr'];
      $deaalm=$_POST['deaalm'];
      $salins=$_POST['salins'];
      $capaso=$_POST['capaso'];
      $invprod=$_POST['invprod'];
      $agins=$_POST['agins'];
      $insumvar=$_POST['insumvar'];
      $explomate=$_POST['explomate'];
      $ordenprod=$_POST['ordenprod'];
      $mostprovee=$_POST['mostprovee'];
      $productPedidos=$_POST['productPedidos'];
      $reabasto_insumos=$_POST['reabasto_insumos'];
	  $ord_x_lotes=$_POST['ord_x_lotes'];
	  
      $resultModel= $this->ConfigModel->saveConfig($opcion,$gap,$apop,$notc,$hereda,$insdir,$ocop,$ocsinr,$deaalm,$salins,$capaso,$gaop,$invprod,$agins,$insumvar,$explomate,$ordenprod,$mostprovee,$productPedidos,$reabasto_insumos,$ord_x_lotes);
    
    }


    function configurar(){

      $resultModel = $this->ConfigModel->loadConfig();

      if($resultModel->num_rows>0){
        while ($row = $resultModel->fetch_assoc()) {
          $gap=$row['gen_aut_ped'];
          $apop=$row['aut_ord_prod'];
          $notc=$row['not_correo'];
          $hereda=$row['heredar_op'];
          $insdir=$row['req_insumos'];
          $ocop=$row['oc_seareq'];
          $ocsinr=$row['genoc_sinreq'];
          $deaalm=$row['designar_almacen'];
          $salins=$row['salida_autinsumos'];
          $capaso=$row['capaso'];
          $gaop=$row['gen_aut_op'];
          $invprod=$row['invprod'];
          $agins=$row['agins'];
          $insumvar=$row['insumosvariables'];
          $explomate =$row['explosionmat'];
          $ordenprod =$row['regordenp'];
          $mostprovee=$row['mostrar_prov_op'];
          $productPedidos=$row['produccion_pedidos'];
		  $reabasto_insumos =$row['reabasto_insumos'];
        	$ord_x_lotes=$row['ord_x_lotes'];;

        }
      }else{
        $gap=0;
        $apop=0;
        $notc=0;
        $hereda=0;
        $insdir=0;
        $ocop=0;
        $ocsinr=0;
        $deaalm=0;
        $salins=0;
        $capaso=1;
        $agins=1;
        $insumvar=0;
        $explomate = 0;
        $ordenprod = 0;
        $mostprovee = 0;
        $productPedidos=0;
		$reabasto_insumos=0;


      }



      require('views/config.php');
    }

    function a_nuevaorden(){

      //Se cambia a funcion otro controlador


    }

    function configproductos(){

    $Array = $this->ConfigModel->configproductos();
    require('views/configuracion_productos.php');

    }

    // AM
     function UpdateActtr(){ 
       
        $types       = json_decode( $_POST['types']) ; 
        $attributes  = json_decode( $_POST['attributes'] );
        $resultModel = $this->ConfigModel->UpdateActtr($types,$attributes,$_REQUEST['confpeso'],$_REQUEST['clasificador']); 
    }



   // REPORTE SEGUIMIENTO DE FACTURACION
   //-------------------------------------------------------------------------------
    
    function reporteSeguimientoFabric(){
      $ordenproduccion    = $this->ConfigModel->pasos_producto();
      $producto           = $this->ConfigModel->app_productos();
      

    require('views/reporte_seguimiento_fabricacion.php');

    }


    function llenarReporteSeguimiento(){

      $ordenprod = $_REQUEST['ordenprod'];
      $producto  = $_REQUEST['producto'];
      $fecha     = $_REQUEST['fecha'];
     
      $reporteSeguimiento = $this->ConfigModel->llenarReporteSeguimiento($ordenprod,$producto,$fecha);
      $fecha   ;
      require ("views/llenarReporteSeguimiento.php");

}
  // -------------------------------------------------------------------------------



}


?>
