<?php
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/produccion.php");


class Produccion extends Common
{
    public $ProduccionModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->ProduccionModel = new ProduccionModel();
        $this->ProduccionModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->ProduccionModel->close();
    }

    function a_nuevaorden()
    {
    		$bandera = $this->ProduccionModel->bandera();
      $resultReq = $this->ProduccionModel->getLastOrden();
      if($resultReq->num_rows>0){
        $row = $resultReq->fetch_array();
        $JSON = array('success' =>1, 'op'=>$row['id'],'regordenp'=>$bandera['regordenp']);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);

    }

    function a_addProductoProduccion(){

      $resultReq = $this->ProduccionModel->getEmpleados();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $empleados[]=$r;
        }
      }else{
        $empleados=0;
      }


      $idProducto=$_POST['idProducto'];

      $resultReq = $this->ProduccionModel->addProductoProduccion($idProducto);
      $cccar=0;
      $html='';
  

      if($resultReq->num_rows>0){
        $row = $resultReq->fetch_array();
        $producto[]=$row;

        $adds='<select id="prelis" onchange="refreshCants('.$producto[0]['id'].',0,0)">
          <option value="'.$producto[0]['costo'].'>0">$'.$producto[0]['costo'].' Precio lista</option>';
        $adds.='<option value="OTRO>x">Otro precio</option>';

        $JSON = array('success' =>1, 'datos'=>$producto, 'adds'=>$adds, 'car'=>$html, 'cccar'=>$cccar);
      }else{
        $JSON = array('success' =>0);
      }

      
      
      echo json_encode($JSON);


    }

    function agrupas(){
      $resultReq = $this->ProduccionModel->getProductos5();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
      }else{
        $productos=0;
      }

      $resultReq = $this->ProduccionModel->getUsuario();
      if($resultReq->num_rows>0){
        $set = $resultReq->fetch_assoc();
        $username=$set['username'];
        $iduser=$set['idempleado'];
      }else{
        $username='Favor de salir y loguearse nuevamente';
        $iduser='0';
      }

      $resultReq = $this->ProduccionModel->getSucursales();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $sucursales[]=$r;
        }
      }else{
        $sucursales=0;
      }

      $resultReq = $this->ProduccionModel->getEmpleados();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $empleados[]=$r;
        }
      }else{
        $empleados=0;
      }

      require('views/produccion/agrupas.php');
    }

    function getiquetas(){
      $resultReq = $this->ProduccionModel->getProductos5();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
      }else{
        $productos=0;
      }

      $resultReq = $this->ProduccionModel->getUsuario();
      if($resultReq->num_rows>0){
        $set = $resultReq->fetch_assoc();
        $username=$set['username'];
        $iduser=$set['idempleado'];
      }else{
        $username='Favor de salir y loguearse nuevamente';
        $iduser='0';
      }

      $resultReq = $this->ProduccionModel->getSucursales();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $sucursales[]=$r;
        }
      }else{
        $sucursales=0;
      }

      $resultReq = $this->ProduccionModel->getEmpleados();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $empleados[]=$r;
        }
      }else{
        $empleados=0;
      }

      require('views/produccion/getiquetas.php');
    }

    function agrupacioninsumos(){
      session_start();
      unset($_SESSION['insumos_producto']);

      $resultReq = $this->ProduccionModel->getProductos5();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
      }else{
        $productos=0;
      }

      require('views/produccion/agrupaciones.php');
    }


    function oproduccion(){
      $resultReq = $this->ProduccionModel->getProductos5();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
      }else{
        $productos=0;
      }
		
	 $row =  $this->ProduccionModel->bandera();
      $insumosvariables=$row['insumosvariables'];
		$tipoexplosion = $row['explosionmat'];
		$ordenmasiva = $row['regordenp'];
		$mostrarprv = $row['mostrar_prov_op'];
		$material_almacen = $row['material_almacen'];
      $resultReq = $this->ProduccionModel->getUsuario();
      if($resultReq->num_rows>0){
        $set = $resultReq->fetch_assoc();
        $username=$set['username'];
        $iduser=$set['idempleado'];
      }else{
        $username='Favor de salir y loguearse nuevamente';
        $iduser='0';
      }

      $resultReq = $this->ProduccionModel->getSucursales();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $sucursales[]=$r;
        }
      }else{
        $sucursales=0;
      }

      $resultReq = $this->ProduccionModel->getEmpleados();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $empleados[]=$r;
        }
      }else{
        $empleados=0;
      }

      require('views/produccion/oproduccion.php');
    }

    function prerequisito(){
      $resultReq = $this->ProduccionModel->getProductos5();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
      }else{
        $productos=0;
      }

      $resultReq = $this->ProduccionModel->getUsuario();
      if($resultReq->num_rows>0){
        $set = $resultReq->fetch_assoc();
        $username=$set['username'];
        $iduser=$set['idempleado'];
      }else{
        $username='Favor de salir y loguearse nuevamente';
        $iduser='0';
      }

      $resultReq = $this->ProduccionModel->getSucursales();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $sucursales[]=$r;
        }
      }else{
        $sucursales=0;
      }

      require('views/produccion/prerequisito.php');
    }


    function ordenp(){
      $resultReq = $this->ProduccionModel->getProductos5();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
      }else{
        $productos=0;
      }

      $resultReq = $this->ProduccionModel->getUsuario();
      if($resultReq->num_rows>0){
        $set = $resultReq->fetch_assoc();
        $username=$set['username'];
        $iduser=$set['idempleado'];
      }else{
        $username='Favor de salir y loguearse nuevamente';
        $iduser='0';
      }

      $resultReq = $this->ProduccionModel->getSucursales();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $sucursales[]=$r;
        }
      }else{
        $sucursales=0;
      }
         $resultReq = $this->ProduccionModel->getEmpleados();
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $empleados[]=$r;
        }
      }else{
        $empleados=0;
      }

      require('views/produccion/ordenp.php');
    }
    function a_guardarPaso(){
       
      $lote6_nolote=trim($_POST['lote6_nolote']);
      $lote6_fechafab=trim($_POST['lote6_fechafab']);
      $lote6_fechacad=trim($_POST['lote6_fechacad']);

      $costo15_adicional=trim($_POST['costo15_adicional']);
      $costo15_terminado=trim($_POST['costo15_terminado']);

      $idsProductos=trim($_POST['idsProductos']);
      $paso=trim($_POST['paso']);
      $accion=trim($_POST['accion']);
      $idop=trim($_POST['idop']);
      $idap=trim($_POST['idap']);

      $idemp=trim($_POST['idemp']);

      $ideti=trim($_POST['ideti']);
      $code=trim($_POST['code']);

      $idp=trim($_POST['idp']);
      $cant=trim($_POST['lacant']);
	  


      $caja10_operador=trim($_POST['caja10_operador']);
      $caja10_peso=trim($_POST['caja10_peso']);

      $clotes=trim($_POST['clotes']);


      if($accion==16){
        $idsProductos=trim($idsProductos,'_#_');
        $result = $this->ProduccionModel->savePaso16($idsProductos,$accion,$idop,$paso,$ideti,$idap); 
      }

      if($accion==1){
        $result = $this->ProduccionModel->savePaso1($idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==11){
        $result = $this->ProduccionModel->savePaso11($idsProductos,$accion,$idop,$paso,$idap,$idemp,$_REQUEST['opc'],$_REQUEST['ppf']); 
      }
	  if($accion==18){
        $result = $this->ProduccionModel->savePaso11($idsProductos,$accion,$idop,$paso,$idap,$idemp,$_REQUEST['opc'],$_REQUEST['ppf']); 
      }

      if($accion==2){
        $al = $this->ProduccionModel->getAlmacen($idop); 
        if($al['total']>0){
          $almacen=$al['rows'][0]['idalmacen'];
        }else{
          $almacen=0;
        }

        $result = $this->ProduccionModel->savePaso2($idsProductos,$accion,$idop,$paso,$clotes,$idp,$almacen,$idap); 
      }

      if($accion==3){
        $result = $this->ProduccionModel->savePaso3($idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==14){
        $al = $this->ProduccionModel->getAlmacen($idop); 
        if($al['total']>0){
          $almacen=$al['rows'][0]['idalmacen'];
        }else{
          $almacen=0;
        }
        $result = $this->ProduccionModel->savePaso14($idsProductos,$accion,$idop,$paso,$almacen,$idap); 
      }

      if($accion==7){
        $result = $this->ProduccionModel->savePaso7($idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==4){
        $result = $this->ProduccionModel->savePaso4($idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==5){
        $result = $this->ProduccionModel->savePaso5($idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==6){

        $result = $this->ProduccionModel->savePaso6($lote6_nolote,$lote6_fechafab,$lote6_fechacad,$idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==9){

        $result = $this->ProduccionModel->savePaso9($accion,$idop,$paso,$idap); 
      }

      if($accion==10){

        $result = $this->ProduccionModel->savePaso10($caja10_operador,$caja10_peso,$idsProductos,$accion,$idop,$paso,$idap); 
      }

      if($accion==15){

        $result = $this->ProduccionModel->savePaso15($costo15_adicional,$costo15_terminado,$idsProductos,$accion,$idop,$paso,$idap); 
      }
      
      if($accion==17){

        $ac = $this->ProduccionModel->costoOpInv($idop); 
        if($ac['total']>0){
          $costo=$ac['rows'][0]['costo'];
        }else{
          $costo=0;
        }

        $al = $this->ProduccionModel->getAlmacen($idop); 
        if($al['total']>0){
          $almacen=$al['rows'][0]['idalmacen'];
        }else{
          $almacen=0;
        }

        $result = $this->ProduccionModel->savePaso17($accion,$idop,$paso,$idp,$costo,$cant,$almacen,$idap); 
      }
	  echo $result;

    }

    function a_activar(){
       
   
      $id=trim($_POST['id']);
       $result = $this->ProduccionModel->activar($id);
        echo $result;

    }

    function a_guardarUsar(){
    		$id_op=($_POST['id_op']);
      	$iduserlog=trim($_POST['iduserlog']);
		/*se actualiza la formula por los insumos variables cambiados*/
		//$update
		if($_REQUEST['insumosvariables']>0){
			$obj = json_decode($_POST['insumo'] , true); 
			 foreach ($obj['datos'] as  $k=>$v) {
			 	
			 	$prdiniciada = $this->ProduccionModel->ordenPrdIniciada($v['idProduct']);
				if($prdiniciada>0){
					
					if($_REQUEST['continua']>0){
						$result = $this->ProduccionModel->saveUsar($id_op,$iduserlog);
						echo $result;
					}else{
						echo "si";
					}
					
					exit();
				}
				
				$this->ProduccionModel->updateInsumosVariables($v['idProduct'], $v['idinsumo'], $v['cantidad']);
			 }
		
		}
				
		
		 
		/*fin variables*/
      	$result = $this->ProduccionModel->saveUsar($id_op,$iduserlog);
      	echo $result;
    }

    function a_guardarAgrupa(){
      $iduserlog=trim($_POST['iduserlog']);
      $option=trim($_POST['option']);
      $nombre=trim($_POST['nombre']);
      $id_eti=trim($_POST['id_eti']);
     

      if($option==1){
      $result = $this->ProduccionModel->saveAgrupa($iduserlog,$nombre);
        echo $result;
      }

      if($option==2){
      $result = $this->ProduccionModel->modiAgrupa($iduserlog,$nombre,$id_eti);
        echo $result;
      }
    }


    function a_guardarEtiqueta(){
      $idsProductos=trim($_POST['idsProductos']);
      $iduserlog=trim($_POST['iduserlog']);
      $obs=trim($_POST['obs']);
      $option=trim($_POST['option']);
      $nombre=trim($_POST['nombre']);
      $id_eti=trim($_POST['id_eti']);
     

      if($option==1){
      $result = $this->ProduccionModel->saveEtiqueta($idsProductos,$iduserlog,$obs,$nombre);
        echo $result;
      }

      if($option==2){
      $result = $this->ProduccionModel->modiEtiqueta($idsProductos,$iduserlog,$obs,$nombre,$id_eti);
        echo $result;
      }


    }

    function a_guardarOrdenP(){
   
      $idsProductos=trim($_POST['idsProductos']);
      $fecha_registro=trim($_POST['fecha_registro']);
      $fecha_entrega=trim($_POST['fecha_entrega']);
      $prioridad=trim($_POST['prioridad']);
      $sucursal=trim($_POST['sucursal']);
      $option=trim($_POST['option']);
      $obs=trim($_POST['obs']);
      $iduserlog=trim($_POST['iduserlog']);
      $id_op=trim($_POST['id_op']);
      $ttt=trim($_POST['ttt']);
      $orden=trim($_POST['orden']);
      $sol=trim($_POST['sol']);
	  
	$lote = json_decode($_POST['lote'],true); 
	$lotes = array();
	 foreach ($lote as $k => $v) {
	 	foreach ($v as  $k=>$l){
	 		$lotes[$k]=$l;
	 	}
	 	
	 }
	 //print_r ($idsProductos);
	 //exit();
	
      if($option==1){//guarda orden primero
      	$result = $this->ProduccionModel->saveOP($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op,$ttt,$sol,$lotes);
        
      }

      if($option==2){
      $result = $this->ProduccionModel->modifyOP($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op,$ttt,$sol,$lotes);
      }

      if($option==3){
       
      $result = $this->ProduccionModel->savePre($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op,$ttt,$orden,$sol);
      }
	  echo $result;

    }

    function a_listaEtiquetas(){
      $resultReq =  $this->ProduccionModel->listaEtiquetas();
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }

    function a_listaAgrupas(){
      $resultReq =  $this->ProduccionModel->listaAgrupas();
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }


    function a_listaOrdenes(){
      $resultReq =  $this->ProduccionModel->listaOrdenes();

      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }

    function a_listaOrdenesPre(){
      $resultReq =  $this->ProduccionModel->listaOrdenesPre();
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }

        function a_listaOrdenesf(){
             $ffin=$_GET['ffin'];
                $fini=$_GET['fini'];
                    $prod=$_GET['prod'];
                        $suc=$_GET['suc'];
                            $sol=$_GET['sol'];
                             $est=$_GET['est'];
      $resultReq =  $this->ProduccionModel->listaOrdenesf($ffin,$fini,$prod,$suc,$sol,$est);
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }



        function a_seg(){
            $id=$_GET['id'];
      $resultReq =  $this->ProduccionModel->seg($id);
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }

       function a_seg2(){
            $id=$_GET['id'];
      $resultReq =  $this->ProduccionModel->seg2($id);
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }

          function a_segl(){
            $id=$_GET['id'];
      $resultReq =  $this->ProduccionModel->segl($id);
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        $acciones='Sin botones';
        while ($r = $resultReq->fetch_array()) {

          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
      
    }




    function a_listaOrdenesP() {

    		$resultReq =  $this->ProduccionModel->listaOrdenesP();

      	$row 		= $this->ProduccionModel->bandera();
     	$bandera		= $row['aut_ord_prod'];
      	$orden		= $row['genoc_sinreq'];//sin requisision 
		$tipoexplosion = $row['explosionmat'];

	      $listas=array();
	      $listas['data']='';
		  
		$listas["tipoexplosion"]=$tipoexplosion;
	    if($resultReq->num_rows>0){
	      $acciones='Sin botones';
	       	 while ($r = $resultReq->fetch_array()) {
				
				$check = "";
				$incremen = 0;
				//&& $r['insumovariable']==0
				/*si es multiple*/
				if($tipoexplosion == 2){
					if($r['autorizado']=='0' && $bandera=='1' && $r['estatus']!=0){
					}else if($r['estatus']==1)	{
						$check = "<input type='checkbox' class='multiexplosion' id='".$r['id']."' />";
					}
					$r[0]=$check;

					$incremen = 1;
				}
				$r[0+$incremen ]=$r['id'];
				$r[1+$incremen ]=$r['nombre'];
				$r[2+$incremen ]=$r['cantidad'];
				$r[3+$incremen ]=$r['fr'];
				$r[4+$incremen ]=$r['fi'];
				$r[5+$incremen ]=$r['fe'];
				$r[6+$incremen ]=$r['sucursal'];
				$r[7+$incremen ]=$r['usuario'];
          /*
          $link='<a  class="btn btn-default btn-xs btn-block">'.$r['id'].'</span></a>';
          $elimin='<a style="margin-top:4px;" onclick="editReq('.$r['id'].',0);" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-search"></span> Ver</a>
            <a style="margin-top:4px;" onclick="" class="btn btn-default btn-xs disabled"><span class="glyphicon glyphicon-remove"></span> Borrar</a>';
          if($r['urgente']==0){
            $r[5]='<span class="label label-default" style="cursor:pointer;">Normal</span>';
          }
          if($r['urgente']==1){
            $r[5]='<span class="label label-danger" style="cursor:pointer;">Urgente</span>';
          }
          if($r['activo']==0){
            $elimin='<a style="margin-top:4px;" onclick="editReq('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Editar</a>
            <a style="margin-top:4px;" onclick="eliminaReq('.$r['id'].')" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Inactivar </a>';
            $r[6]='<span class="label label-warning" style="cursor:pointer;">Nueva</span>';
          }
          if($r['activo']==1){

            $r[6]='<span class="label label-success" style="cursor:pointer;">OV Autorizada</span>';
          }
          if($r['activo']==2){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['id'].'</a>';
            $r[6]='<span class="label label-default" style="cursor:pointer;">Inactiva</span>';
          }
          if($r['activo']==3){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['id'].'</a>';
            $r[6]='<span class="label label-success" style="cursor:pointer;">OV activa</span>';
            $elimin='<a style="margin-top:4px;" onclick="editReq('.$r['id'].',0);" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-search"></span> Ver</a>
            <a style="margin-top:4px;" onclick="" class="btn btn-default btn-xs disabled"><span class="glyphicon glyphicon-remove"></span> Borrar.</a>';
          }

          if($r['activo']==4){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['id'].'</a>';
            $r[6]='<span class="label label-success" style="cursor:pointer;">OK recibida ok</span>';
            $elimin='<a style="margin-top:4px;" onclick="editReq('.$r['id'].',0);" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-search"></span> Ver</a>
            <a style="margin-top:4px;" onclick="" class="btn btn-default btn-xs disabled"><span class="glyphicon glyphicon-remove"></span> Borrar.</a>';
          }

          if($r['aceptada']==1){
            $r[6].=' <span class="label label-success" style="cursor:pointer;">Aceptada por cliente</span>';
          }

          
          

          $r[7]=$elimin;
          if($r['cadenaCoti']!=null){
            $r[7].=' <button style="margin-top:4px;"  onclick="vercomcli(\''.$r['cadenaCoti'].'\');" class="btn btn-default btn-xs">Comentarios </button>';
          }
          $r[7].=' <button style="margin-top:4px;" id="btn_imprimir_'.$r['id'].'_" onclick="imprimir2('.$r['id'].',2);" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-print"></span> </button>';
*/



	           if($r['autorizado']=='0' && $bandera=='1' && $r['estatus']!=0){
				$boton='<button  style="margin-top:4px;" onclick="autorizar('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Autorizar</button>';
				        
				
				            }
				elseif($r['estatus']==1){
				  $boton='<button  style="margin-top:4px;" onclick="explosionMat('.$r['id'].' , '.$orden.');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Explosion de materiales</button>';
				
				
				}
				else{
					$boton='<button disabled style="margin-top:4px;" onclick="explosionMat('.$r['id'].','.$orden.');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Explosion de materiales</button>';
	
	
	            }
			
			

	          if($r['estatus']==0){
	            $r[8+$incremen]='<span class="label label-danger" style="cursor:pointer;">Orden eliminada</span>';
	            $acciones='<button disabled style="margin-top:4px;" onclick="editReq('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Editar orden</button> '.$boton;
	          }
	
	
	          if($r['estatus']==1){
	            $r[8+$incremen]='<span class="label label-default" style="cursor:pointer;">Registro inicial</span>';
	            $acciones='<button style="margin-top:4px;" onclick="editReq('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Editar orden</button> '.$boton.' <button style="margin-top:4px;" onclick="eliminarOP('.$r['id'].');"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Eliminar</button>';
	          }
	
	          if($r['estatus']==2){
	            $r[8+$incremen]='<span class="label label-warning" style="cursor:pointer;">En espera de insumos</span>';
	            $acciones='<button disabled style="margin-top:4px;" onclick="editReq('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Editar orden</button> '.$boton.'<button onclick="abrirNueva(1);" style="margin-top:4px;" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Pre-Requisiciones</button> <!--<button style="margin-top:4px;" onclick="cancelarTodo('.$r['id'].');"  class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>-->';
	          }
	
	          if($r['estatus']==10){
	            $r[8+$incremen]='<span class="label label-success" style="cursor:pointer;">Produccion finalizada</span>';
	            $acciones='';
	          }
	
	          if($r['estatus']==3){
	            $r[8+$incremen]='<span class="label label-success" style="cursor:pointer;">Lista para producir</span>';
	            $acciones='<button onclick="abrirNueva(1);" style="margin-top:4px;" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Pre-Requisiciones</button> <button style="margin-top:4px;" onclick="ciclo('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Ejecutar ciclo</button>';
	          }
	
	          if($r['estatus']==4){
	            $r[8+$incremen]='<span class="label label-success" style="cursor:pointer;">Lista para producir</span>';
	            $acciones='<button style="margin-top:4px;" onclick="ciclo('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Ejecutar ciclo</button>';
	          }
	
	          if($r['estatus']==9){
	            $r[8+$incremen]='<span class="label label-info" style="cursor:pointer;">Produccion iniciada</span>';
	            $acciones=' <button style="margin-top:4px;" onclick="ciclo('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Ejecutar ciclo</button>';
	          }
	
	          //$acciones.='<button style="margin-top:4px;" onclick="ciclo('.$r['id'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Ejecutar ciclo</button>';
	
	          
	
	          $r[9+$incremen]=$acciones;
			  
	          $listas['data'][]=$r;
	        }
	      }else{
	        $listas['data']=array();
	      }
	
	      echo json_encode($listas);
	  

    }

    function a_autorizar(){
   $id=$_POST['id'];
    $this->ProduccionModel->autorizar($id);

    }


     function a_editarAgrupas(){
      $idEti=$_POST['idReq'];
      $m=$_POST['m'];
      $mod=$_POST['mod'];
      $pr=$_POST['pr']; //proviene

      $resultReq = $this->ProduccionModel->editarAgrupas($idEti);

      if($resultReq->num_rows>0){
        $row = $resultReq->fetch_assoc();
        $JSON = array('success' =>1, 'requisicion'=>$row,  'ss'=>0);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);
      

    }

    function a_editarEtiqueta(){
      $idEti=$_POST['idReq'];
      $m=$_POST['m'];
      $mod=$_POST['mod'];
      $pr=$_POST['pr']; //proviene

      $resultReq = $this->ProduccionModel->editarEtiqueta($idEti);

      if($resultReq->num_rows>0){
        $row = $resultReq->fetch_assoc();

        $row2['adds']='';

        $resultReq2 = $this->ProduccionModel->tiposEtiqueta($idEti,$m);
        while ($row2 = $resultReq2->fetch_assoc()) {

          $productos[]=$row2;
        }

        $JSON = array('success' =>1, 'requisicion'=>$row, 'productos'=>$productos, 'adds'=>$adds, 'ss'=>0);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);
      

    }

   

    function a_editarordenp(){
      $idReq=$_POST['idReq'];
      $m=$_POST['m'];
      $mod=$_POST['mod'];
      $pr=$_POST['pr']; //proviene
      $resultReq = $this->ProduccionModel->editarordenp($idReq);

      if($resultReq->num_rows>0){
      
        $row = $resultReq->fetch_assoc();
        $row['fi']=substr($row['fi'],0,10);
        $row['fe']=substr($row['fe'],0,10);

        $row2['adds']='';
 
        $resultReq2 = $this->ProduccionModel->productosOp($idReq,$m);
        while ($row2 = $resultReq2->fetch_assoc()) {

          $productos[]=$row2;
        }
       // $row2 = $resultReq2->fetch_assoc();
        
        $JSON = array('success' =>1, 'requisicion'=>$row, 'productos'=>$productos, 'adds'=>$adds, 'ss'=>0);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);

    }

    function a_listaPaso5(){
      $idop=$_POST['idop'];
      $tr5='';
        $rsqlpaso4 = $this->ProduccionModel->sqlPaso4($idop);
        if($rsqlpaso4->num_rows>0){

          while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {
            $tr5.='<tr id="tr_empp_'.$rowSqlpaso4['idEmpleado'].'"><td>'.$rowSqlpaso4['nombre'].'</td>
            <td><input id="maq_'.$rowSqlpaso4['idmaq'].'" type="text" class="form-control" value="'.$rowSqlpaso4['maquinaria'].'"></td>
            </tr>';
          }
        }else{
          $JSON = array('tr5'=>0);
        }

        $JSON = array('tr5'=>$tr5);

      
      echo json_encode($JSON);

    }

    function a_finalizar(){
      $id=$_POST['id'];
      $this->ProduccionModel->finalizar($id);
    }

    function printer()
    {
        $etiquetas = $this->ProduccionModel->getEtiquetaPrint($_REQUEST['idOp']);
        if($etiquetas['total']>0){
          require("views/produccion/print_etiqueta.php");
        }else{
          echo 'No hay etiquetas';
        }
    }

    function printerexcel()
    {
        $etiquetas = $this->ProduccionModel->getEtiquetaPrint($_REQUEST['idOp']);
        if($etiquetas['total']>0){
          echo json_encode($etiquetas['rows']);
          //require("views/produccion/export_etiqueta.php");
        }else{
          echo 0;
        }
    }

    function printerp()
    {
        require("views/produccion/prueba_etiqueta.php");
    }

    function a_clipaso(){
      session_start();
      unset($_SESSION['v_rePr']);
      $idop=$_POST['idop'];
      $paso=$_POST['paso'];
      $accion=$_POST['accion'];
      $idap=$_POST['idap'];

      if($accion==16){

        $resInsumos = $this->ProduccionModel->getEtiqueta($idop,$paso,$accion);
        if($resInsumos['total']>0){
          $JSON = array('success' =>1, 'data'=>$resInsumos['rows']);
          echo json_encode($JSON);
          exit();
        }else{
          $JSON = array('success' =>0);
          echo json_encode($JSON);
          exit();
        }

      }

      if($accion==1){
        $insumos=array();
        $resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
        if($resInsumos['total']>0){
          foreach ($resInsumos['rows'] as $k => $v) {
            $existencias = $this->ProduccionModel->getExistencias($v['idProducto'],'0');
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

      if($accion==11){

        $tr=array();

        $rsqlpaso4 = $this->ProduccionModel->sqlPaso4($idop);
        if($rsqlpaso4->num_rows>0){
          $cad='<div id="lose" class="col-sm-12">
          <select onchange="agre('.$idap.');" id="mmm_'.$idap.'" class="form-control"  >';
		  $cad.='<option value="0">-Seleccione-</option>';
          while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {
            $cad.='<option  value="'.$rowSqlpaso4['idEmpleado'].'">'.$rowSqlpaso4['nombre'].'</option>';
            //$tr[]='<div id="tr_11empp_'.$rowSqlpaso4['idEmpleado'].'"><b>'.$rowSqlpaso4['nombre'].'</b></div>';
          }
          $cad.='</select></div>';
        }else{
          $tr=0;
          $cad=0;
        }

        $proce = $this->ProduccionModel->matProceso($idop);


        $wed='';
        $hist11 = $this->ProduccionModel->historial11($idop, $idap,0);
        if($hist11['total']>0){
          $wed=$hist11['rows'];
        }else{
          $wed=0;
        }



        $insumos=array();
        //$resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
        $resInsumos = $this->ProduccionModel->productosOpExplosionProceso($idop,$idap);
        if($resInsumos['total']>0){
          foreach ($resInsumos['rows'] as $k => $v) {
            $existencias = $this->ProduccionModel->getExistencias($v['idProducto'],'0');
            if($existencias[0]['cantidad']==null){
              $g=0;
            }else{
              $g=$existencias[0]['cantidad'];
            }
            $resInsumos['rows'][$k]['existen']=$g;

            $usados = $this->ProduccionModel->getUsados($idop, $idap, $v['idProducto'],$accion);
            if($usados['total']>0){
              $resInsumos['rows'][$k]['usados']=$usados['rows'][0]['tot_real'];
            }else{
              $resInsumos['rows'][$k]['usados']=0;
            }
            
          }
        }else{
          $JSON = array('success' =>0);
          echo json_encode($JSON);
          exit();
        }

        $JSON = array('success' =>1, 'data'=>$resInsumos['rows'], 'tr'=>$cad, 'proce'=>$proce, 'hist11'=>$wed);
        echo json_encode($JSON);
        exit();
      }
/*insumos variables con envio de material*/
if($accion==18){

        $tr=array();
		$existepaso = $this->ProduccionModel->accion18Existe($idop, $idap);
        $rsqlpaso4 = $this->ProduccionModel->sqlPaso4($idop);
        if($rsqlpaso4->num_rows>0){
          $cad='<div id="lose" class="col-sm-12">
          <select  id="mmm_'.$idap.'" class="form-control"  >';
		  $cad.='<option value="0">-Seleccione-</option>';
          while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {
            $cad.='<option  value="'.$rowSqlpaso4['idEmpleado'].'">'.$rowSqlpaso4['nombre'].'</option>';
            //$tr[]='<div id="tr_11empp_'.$rowSqlpaso4['idEmpleado'].'"><b>'.$rowSqlpaso4['nombre'].'</b></div>';
          }
          $cad.='</select></div>';
        }else{
          $tr=0;
          $cad=0;
        }

        $proce = $this->ProduccionModel->matProceso($idop);


        $wed='';
        $hist11 = $this->ProduccionModel->historial11($idop, $idap,1);
        if($hist11['total']>0){
          $wed=$hist11['rows'];
        }else{
          $wed=0;
        }



        $insumos=array();
        //$resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
        $resInsumos = $this->ProduccionModel->productosOpExplosionProceso($idop,$idap);
        if($resInsumos['total']>0){
          foreach ($resInsumos['rows'] as $k => $v) {
            $existencias = $this->ProduccionModel->getExistencias($v['idProducto'],'0');
            if($existencias[0]['cantidad']==null){
              $g=0;
            }else{
              $g=$existencias[0]['cantidad'];
            }
            $resInsumos['rows'][$k]['existen']=$g;

            $usados = $this->ProduccionModel->getUsados($idop, $idap, $v['idProducto'],$accion);
            if($usados['total']>0){
              $resInsumos['rows'][$k]['usados']=$usados['rows'][0]['tot_real'];
            }else{
              $resInsumos['rows'][$k]['usados']=0;
            }
            
          }
        }else{
          $JSON = array('success' =>0);
          echo json_encode($JSON);
          exit();
        }

        $JSON = array('success' =>1, 'data'=>$resInsumos['rows'], 'tr'=>$cad, 'proce'=>$proce, 'hist11'=>$wed,'existepaso'=>$existepaso);
        echo json_encode($JSON);
        exit();
      }

/*fin envio material insumos variables*/
      if($accion==2){
          $insumos=array();
          $resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
          if($resInsumos['total']>0){
            foreach ($resInsumos['rows'] as $k => $v) {
              $rsqlpaso2 = $this->ProduccionModel->sqlPaso2($idop,$v['idProducto']);
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
          exit();
      }

      if($accion==3){
          $insumos=array();
          $resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
          if($resInsumos['total']>0){
            foreach ($resInsumos['rows'] as $k => $v) {
              $rsqlpaso3 = $this->ProduccionModel->sqlPaso3($idop,$v['idProducto']);
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

      if($accion==14){     
          // $insumos=array();
          // $resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
// 
          // if($resInsumos['total']>0){
            // foreach ($resInsumos['rows'] as $k => $v) {
              // $rsqlpaso14 = $this->ProduccionModel->sqlPaso14($idop,$v['idProducto']);
// 
              // if($rsqlpaso14->num_rows>0){
// 
                // $rowSqlpaso14 = $rsqlpaso14->fetch_assoc();
                // $resInsumos['rows'][$k]['merma']=$rowSqlpaso14['merma'];
              // }else{
                // $resInsumos['rows'][$k]['merma']=0.00;
              // }
            // }
          // }else{
            // $JSON = array('success' =>0);
            // echo json_encode($JSON);
            // exit();
          // }
// 
          // $JSON = array('success' =>1, 'data'=>$resInsumos['rows']);
          // echo json_encode($JSON);
          // exit();
          /*merma nivel ppf*/
            $tipoMerma = $this->ProduccionModel->tipoMerma();
			$merma = "";
              if($tipoMerma){
					while($t =$tipoMerma->fetch_object() ){
						$merma.="<option value=".$t->id.">".$t->tipo_merma."</option>";
					}
              }
            
          $cad14 = "";
          $producto = $this->ProduccionModel->productosOp($idop,1);
          $p = $producto->fetch_assoc();
          
		  
          $JSON = array('success' =>1, 'data'=>$p,'merma'=>$merma);
          echo json_encode($JSON);
          exit();
      }

      if($accion==4){
          $tr='';
          $tr5='';
          $rsqlpaso4 = $this->ProduccionModel->sqlPaso4($idop);
          if($rsqlpaso4->num_rows>0){
            while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {
              $tr.='<tr id="tr_empp_'.$rowSqlpaso4['idEmpleado'].'"><td>'.$rowSqlpaso4['nombre'].'</td><td><button id="eliemp4" style=" padding: 0px;  height:33px;" onclick="eliemp4('.$rowSqlpaso4['idEmpleado'].');" class="btn btn-danger btn-sm btn-block">Elimina</button></td></tr>';
            }
            $JSON = array('success' =>1, 'data'=>$tr);
            echo json_encode($JSON);
            exit();
          }else{
            $JSON = array('success' =>1, 'data'=>'');
            echo json_encode($JSON);
            exit();
          }
      }

      if($accion==5){
          $tr5='';
          $rsqlpaso4 = $this->ProduccionModel->sqlPaso4($idop);
          if($rsqlpaso4->num_rows>0){
            while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {
              $tr5.='<tr id="tr_empp_'.$rowSqlpaso4['idEmpleado'].'"><td>'.$rowSqlpaso4['nombre'].'</td>
              <td><input id="maq_'.$rowSqlpaso4['idmaq'].'" type="text" class="form-control" value="'.$rowSqlpaso4['maquinaria'].'"></td>
              </tr>';
            }
            $JSON = array('success' =>1, 'data'=>$tr5);
            echo json_encode($JSON);
            exit();
          }else{
            $JSON = array('success' =>1, 'data'=>'');
            echo json_encode($JSON);
            exit();
          }
      }

      if($accion==6){
        $lotes=array();
        $rsqlpaso6 = $this->ProduccionModel->sqlPaso6($idop);
        if($rsqlpaso6->num_rows>0){
          $rowSqlpaso6 = $rsqlpaso6->fetch_assoc();
          $lotes[0]=substr($rowSqlpaso6['no_lote'],0,10);
          $lotes[1]=substr($rowSqlpaso6['fecha_fabricacion'],0,10);
          $lotes[2]=substr($rowSqlpaso6['fecha_caducidad'],0,10);
          $JSON = array('success' =>1, 'data'=>$lotes);
            echo json_encode($JSON);
            exit();
        }else{
          $JSON = array('success' =>1, 'data'=>0);
            echo json_encode($JSON);
            exit();
        }
      }

      if($accion==10){
        $caja=array();
        $rsqlpaso10 = $this->ProduccionModel->sqlPaso10($idop);
        if($rsqlpaso10->num_rows>0){
          $rowSqlpaso10 = $rsqlpaso10->fetch_assoc();
          $caja[0]=substr($rowSqlpaso10['operador'],0,10);
          $caja[1]=substr($rowSqlpaso10['peso'],0,10);
          $JSON = array('success' =>1, 'data'=>$caja);
            echo json_encode($JSON);
            exit();
        }else{
          $JSON = array('success' =>1, 'data'=>0);
            echo json_encode($JSON);
            exit();
        }
      }

      if($accion==15){
        $costos=array();
        $rsqlpaso15 = $this->ProduccionModel->sqlPaso15($idop);
        if($rsqlpaso15->num_rows>0){
          $rowSqlpaso15 = $rsqlpaso15->fetch_assoc();
          $costos[0]=$rowSqlpaso15['costo_adicional'];
          $costos[1]=$rowSqlpaso15['costo_total'];
          $JSON = array('success' =>1, 'data'=>$costos);
            echo json_encode($JSON);
            exit();
        }else{
          $JSON = array('success' =>1, 'data'=>0);
            echo json_encode($JSON);
            exit();
        }
      }

      if($accion==7){
          $insumos=array();
          $resInsumos = $this->ProduccionModel->productosOpExplosion($idop);
          if($resInsumos['total']>0){
            foreach ($resInsumos['rows'] as $k => $v) {
              $rsqlpaso7 = $this->ProduccionModel->sqlPaso7($idop,$v['idProducto']);
              if($rsqlpaso7->num_rows>0){
                $rowSqlpaso7 = $rsqlpaso7->fetch_assoc();
                $resInsumos['rows'][$k]['cbatch']=$rowSqlpaso7['cbatch'];
              }else{
                $resInsumos['rows'][$k]['cbatch']=0.00;
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

      if($accion==9){
            $JSON = array('success' =>1);
            echo json_encode($JSON);
            exit();
      }

      if($accion==17){

            $JSON = array('success' =>1);
            echo json_encode($JSON);
            exit();

      }

    }



    function a_explosionMatCiclo(){
        $idop=$_POST['idop'];
        $resultReq = $this->ProduccionModel->editarordenp($idop);
        $lospasos = $this->ProduccionModel->listar_pasos_op($idop);

        //SUB ORDENES PROD AGRUPADAS
        $rsqlpaso2 = $this->ProduccionModel->buscaAgrupadas($idop);
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

        exit();
      if($resultReq->num_rows>0){
      
        $row = $resultReq->fetch_assoc();
        $row['fi']=substr($row['fi'],0,10);
        $row['fe']=substr($row['fe'],0,10);

        $row2['adds']='';

        $tr='';
        $tr5='';
        $rsqlpaso4 = $this->ProduccionModel->sqlPaso4($idop);
        if($rsqlpaso4->num_rows>0){

          while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {
            $tr.='<tr id="tr_empp_'.$rowSqlpaso4['idEmpleado'].'"><td>'.$rowSqlpaso4['nombre'].'</td><td><button id="eliemp4" style=" padding: 0px;  height:33px;" onclick="eliemp4('.$rowSqlpaso4['idEmpleado'].');" class="btn btn-danger btn-sm btn-block">Elimina</button></td></tr>';
            $tr5.='<tr id="tr_empp_'.$rowSqlpaso4['idEmpleado'].'"><td>'.$rowSqlpaso4['nombre'].'</td>
            <td><input id="maq_'.$rowSqlpaso4['idmaq'].'" type="text" class="form-control" value="'.$rowSqlpaso4['maquinaria'].'"></td>
            </tr>';
          }
        }else{
        
        }

        $lotes=array();
        $rsqlpaso6 = $this->ProduccionModel->sqlPaso6($idop);
        if($rsqlpaso6->num_rows>0){
          $rowSqlpaso6 = $rsqlpaso6->fetch_assoc();
          $lotes[0]=substr($rowSqlpaso6['no_lote'],0,10);
          $lotes[1]=substr($rowSqlpaso6['fecha_fabricacion'],0,10);
          $lotes[2]=substr($rowSqlpaso6['fecha_caducidad'],0,10);
        }else{
          $lotes=0;
        }

        $costos=array();
        $rsqlpaso15 = $this->ProduccionModel->sqlPaso15($idop);
        if($rsqlpaso15->num_rows>0){
          $rowSqlpaso15 = $rsqlpaso15->fetch_assoc();
          $costos[0]=$rowSqlpaso15['costo_adicional'];
          $costos[1]=$rowSqlpaso15['costo_total'];
        }else{
          $costos=0;
        }




 
        $insumos=array();
        $resultReq2 = $this->ProduccionModel->productosOp($idop,1);
        while ($row2 = $resultReq2->fetch_assoc()) {

          $resultReq3 = $this->ProduccionModel->productosOpExplosion($idop,$row2['id_producto']);
          if($resultReq3->num_rows>0){
            while ($row3 = $resultReq3->fetch_assoc()) {
              $rsqlpaso2 = $this->ProduccionModel->sqlPaso2($idop,$row3['idProducto']);
              if($rsqlpaso2->num_rows>0){
                $rowSqlpaso2 = $rsqlpaso2->fetch_assoc();
                $row3['cantidad2']=$rowSqlpaso2['cantUti'];
              }else{
                $row3['cantidad2']='';
              }

              $rsqlpaso3 = $this->ProduccionModel->sqlPaso3($idop,$row3['idProducto']);
              if($rsqlpaso3->num_rows>0){
                $rowSqlpaso3 = $rsqlpaso3->fetch_assoc();
                $row3['peso']=$rowSqlpaso3['pesoUti'];
              }else{
                $row3['peso']=0;
              }

              $rsqlpaso14 = $this->ProduccionModel->sqlPaso14($idop,$row14['idProducto']);
              if($rsqlpaso14->num_rows>0){
                $rowSqlpaso14 = $rsqlpaso14->fetch_assoc();
                $row14['merma']=$rowSqlpaso14['merma'];
              }else{
                $row14['merma']=0;
              }

              $rsqlpaso8 = $this->ProduccionModel->sqlPaso8($idop,$row3['idProducto']);
              if($rsqlpaso8->num_rows>0){
                $rowSqlpaso8 = $rsqlpaso8->fetch_assoc();
                $row3['cbatch']=$rowSqlpaso8['cbatch'];
              }else{
                $row3['cbatch']=0;
              }


        
        $existencias = $this->ProduccionModel->getExistencias($row3['idProducto'],'0');
        if($existencias[0]['cantidad']==null){
          $g=0;
        }else{
          $g=$existencias[0]['cantidad'];
        }
        $row3['existen']=$g;


    


              //consulta pa los proveedores y costos
              if($row3['idcostoprovs']!=''){
                $resultReq4 = $this->ProduccionModel->proveedoresCostoOP($row3['idcostoprovs']);
                if($resultReq4->num_rows>0){
                  $cadprovs="<select id='cmbProv_".$row2['id_producto']."_".$row3['idProducto']."' onchange='refreshCants(".$row3['idProducto'].",".$row2['id_producto'].");' id='insprv'><option value='0-0'>Seleccione</option>";
                  while ($row4 = $resultReq4->fetch_assoc()) {
                    $cadprovs.="<option value='".$row4['id_proveedor']."-".$row4['costo']."'>".$row4['razon_social']."</option>";
                  }
                  $cadprovs.='</select>';
                }else{
                  $cadprovs="<select id='insprv'><option value='0-0'>No hay proveedores para este producto</option></select>";
                }
              }else{
                $cadprovs="<select id='insprv'><option value='0-0'>No hay proveedores</option></select>";

              }

              $row3['listprovs']=$cadprovs;
              $row2['insumos'][]=$row3;

            }

          }else{
            $row2['insumos']=0;
          }

          $productos[]=$row2;
        }


        



       // $row2 = $resultReq2->fetch_assoc();
        
        $JSON = array('success' =>1, 'requisicion'=>$row, 'productos'=>$productos, 'adds'=>$adds, 'ss'=>0, 'tr'=>$tr, 'tr5'=>$tr5, 'lotes'=>$lotes, 'costos'=>$costos);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);

    }



    function a_modalRecepcion(){
      $idProd=$_POST['idProd'];
      $modo=$_POST['modo'];
      $cadcar=$_POST['cadcar'];
      $cantrecibid=$_POST['modalcantrecibida'];



      session_start();
      if($modo==1){
        $lotes=$_POST['lotes'];
        $cantslotes=$_POST['cantslotes'];
        $lotes_imp=implode(',', $lotes);
        $_SESSION['v_rePr'][$modo][$idProd][$cadcar]=array('cantrecibid' => $cantrecibid, 'lotes' => $lotes, 'cantslotes'=>$cantslotes);
        echo $modo.'->-'.$idProd.'->-'.$cantrecibid.'->-'.$lotes_imp.'->-'.$cantslotes;
      }

    }

    function a_getLotes(){

      $idProd=$_POST['idProducto'];
      $cadcar='0';
      $resultReq =  $this->ProduccionModel->getLotes($idProd,$cadcar);

      echo json_encode($resultReq);

    }

    function eliAgrupados(){
      $id=$_POST['id'];
      $this->ProduccionModel->eliAgrupados($id);
    }

    function reloadInsumos() {
      session_start();
      unset($_SESSION['insumos_producto']);
      require('views/produccion/listar_insumos_producto.php');

    }

    function agregar_insumos_producto($objeto) {
    // Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
    // Si no conserva su valor normal
        $objeto=(empty($objeto))?$_REQUEST:$objeto;

        session_start();

        if (!empty($_SESSION['insumos_producto'][$objeto['id']]))
                unset($_SESSION['insumos_producto'][$objeto['id']]);
        else
            $_SESSION['insumos_producto'][$objeto['id']]=$objeto;


        //echo json_encode($_SESSION['insumos_producto']);

    // carga la vista para listar las reservaciones
        //require('views/recetas/listar_parametros_agregados.php');
        require('views/produccion/listar_insumos_producto.php');
    }

    function asignar_cant_req($objeto) {
    // Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
    // Si no conserva su valor normal
        $objeto = (empty($objeto)) ? $_REQUEST : $objeto;
        session_start();

        $_SESSION['insumos_producto'][$objeto['id']]['cantidad'] = $objeto['cantidad'];

        echo json_encode($_SESSION['insumos_producto']);
    }

    function a_guardarGrupo(){
      $idp=$_POST['idp'];
      $nombre=$_POST['nombre'];
      $guardaredicion=$_POST['guardaredicion'];
      $this->ProduccionModel->guardaGrupo($idp,$nombre,$guardaredicion);

    }


    function buscaAgrupados(){
      session_start();
      unset($_SESSION['insumos_producto']);

      $idp=$_POST['idp'];
      $agrupados=0;
      $explosion=0;

      $resultReq1 = $this->ProduccionModel->getAgrupados($idp);
      if($resultReq1['total']>0){
        $agrupados=$resultReq1['rows'];
      }
      $resultReq3 = $this->ProduccionModel->productosExplosion($idp,$ver);
      if($resultReq3['total']>0){
        $explosion=$resultReq3['rows'];
      }

      if($agrupados==0 && $explosion==0){
        $JSON = array('success' =>0);
      }else{

        $JSON = array('success' =>1, 'explosion'=>$explosion, 'agrupados'=>$agrupados);
      }

      echo json_encode($JSON);
      
    }


    function buscaAgrupadosEdicion(){
      session_start();
      unset($_SESSION['insumos_producto']);

      $idp=$_POST['idp'];
      $idedicion=$_POST['idedicion'];
      
      $agrupados=0;
      $explosion=0;

      $resultReq1 = $this->ProduccionModel->getAgrupados($idp,$idedicion);
      if($resultReq1['total']>0){
        $agrupados=$resultReq1['rows'];
      }
      $resultReq3 = $this->ProduccionModel->productosExplosion($idp,$idedicion);
      if($resultReq3['total']>0){
        $explosion=$resultReq3['rows'];
      }

      if($agrupados==0 && $explosion==0){
        $JSON = array('success' =>0);
      }else{

        $JSON = array('success' =>1, 'explosion'=>$explosion, 'agrupados'=>$agrupados);
      }

      echo json_encode($JSON);
      
    }


    function a_explosionMat(){
      $idop=$_POST['idop'];
          $resultReq = $this->ProduccionModel->editarordenp($idop);

      if($resultReq->num_rows>0){
      
        $row = $resultReq->fetch_assoc();
        $row['fi']=substr($row['fi'],0,10);
        $row['fe']=substr($row['fe'],0,10);

        $row2['adds']='';
 
        $insumos=array();
        $resultReq2 = $this->ProduccionModel->productosOp($idop,1);
        while ($row2 = $resultReq2->fetch_assoc()) {


          $resultReq3 = $this->ProduccionModel->productosOpExplosion($idop,$row2['id_producto']);
		$cantitotalinsumos = 0;
          if($resultReq3['total']>0){
            foreach ($resultReq3['rows'] as $key => $row3) {
			$cantitotalinsumos += $row3['canti'];
              //consulta pa los proveedores y costos
              $existencias = $this->ProduccionModel->getExistenciasNueva($row3['idProducto'],'0');
              
              if($row3['idcostoprovs']!=''){
                $existencias = $this->ProduccionModel->getExistenciasNueva($row3['idProducto'],'0');
                $resultReq4 = $this->ProduccionModel->proveedoresCostoOP($row3['idcostoprovs']);
                if($resultReq4->num_rows>0){
                  $cadprovs="<select id='cmbProv_".$row2['id_producto']."_".$row3['idProducto']."' onchange='refreshCants(".$row3['idProducto'].",".$row2['id_producto'].");' id='insprv'><option value='0-0'>Seleccione</option>";
                  while ($row4 = $resultReq4->fetch_assoc()) {
                    $cadprovs.="<option value='".$row4['id_proveedor']."-".$row4['costo']."'>".$row4['razon_social']."</option>";
                  }
                  $cadprovs.='</select>';
                }else{
                  $cadprovs="<select id='insprv'><option value='0-0'>No hay proveedores para este producto</option></select>";
                }
              }else{
                $cadprovs="<select id='insprv'><option value='0-0'>No hay proveedores</option></select>";

              }

              $row3['listprovs']=$cadprovs;
              $row3['existencias']=$existencias;
              $row2['insumos'][]=$row3;




            }

          }else{
            $row2['insumos']=0;
          }

          $productos[]=$row2;
        }



       // $row2 = $resultReq2->fetch_assoc();
        
        $JSON = array('success' =>1, 'requisicion'=>$row, 'productos'=>$productos, 'adds'=>$adds, 'ss'=>0, 'cantidadinsumos'=>$cantitotalinsumos);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);

    }
    //explosion masiva
    function a_explosionMatMasiva(){
    	
		$row =  $this->ProduccionModel->bandera();
		$mostrarprd = $row['mostrar_prov_op'];
			
		$idop = implode(",", $_REQUEST['idop']);
       
          // $resultReq = $this->ProduccionModel->editarordenp($idop);
// 
      		// if($resultReq->num_rows>0){
//       
		        // $row = $resultReq->fetch_assoc();
		        // $row['fi']=substr($row['fi'],0,10);
		        // $row['fe']=substr($row['fe'],0,10);
// 		
		        $row2['adds']='';
		 
		        $insumos=array();
        $insumos=array();
         $row2 = $this->ProduccionModel->productosOpMasiva($idop);
       


          $resultReq3 = $this->ProduccionModel->productosOpExplosionMasiva($idop);
		$cantitotalinsumos = 0;
          if($resultReq3['total']>0){
            foreach ($resultReq3['rows'] as $key => $row3) {
			$cantitotalinsumos += $row3['canti'];
              //consulta pa los proveedores y costos
              if($mostrarprd == 1){
              	
              }
	           
	           $existencias = $this->ProduccionModel->getExistenciasNueva($row3['idProducto'],'0');
               $resultReq4 = $this->ProduccionModel->proveedoresCostoOParaMasivo($row3['idProducto']);
				
                if($resultReq4->num_rows>0){
                  $cadprovs="<select id='cmbProv_".$row2['id_producto']."_".$row3['idProducto']."' onchange='refreshCants(".$row3['idProducto'].",".$row2['id_producto'].");' id='insprv'><option value='0-0'>Seleccione</option>";
                  while ($row4 = $resultReq4->fetch_assoc()) {
                    $cadprovs.="<option value='".$row4['id_proveedor']."-".$row4['costo']."'>".$row4['razon_social']."</option>";
                  }
                  $cadprovs.='</select>';
                }else{
                  $cadprovs="<select id='insprv'><option value='0-0'>No hay proveedores para este producto</option></select>";
                }
              

              $row3['listprovs']=$cadprovs;
              $row3['existencias']=$existencias;
              $row2['insumos'][]=$row3;




            }

          }else{
            $row2['insumos']=0;
          }

          $productos[]=$row2;
        

		        
		        
		    	$JSON = array('success' =>1, 'requisicion'=>$row, 'productos'=>$productos, 'adds'=>$adds, 'ss'=>0, 'cantidadinsumos'=>$cantitotalinsumos);
      // }else{
        // $JSON = array('success' =>0);
      // }

      echo json_encode($JSON);

    }
    //fin explosion masiva

    function a_eliminaOP()
    {
      $idop=$_POST['idop'];
      $resultReq = $this->ProduccionModel->delOP($idop);
      echo $resultReq;

    }

    function a_eliminaEtiqueta()
    {
      $id=$_POST['id'];
      $resultReq = $this->ProduccionModel->delEtiqueta($id);
      echo $resultReq;

    }

    function a_eliminaAgrupas()
    {
      $id=$_POST['id'];
      $resultReq = $this->ProduccionModel->delAgrupas($id);
      echo $resultReq;

    }

     function editaragrupacion()
    {
      $id=$_POST['idp'];
      $resultReq = $this->ProduccionModel->editaragrupacion($id);
      echo $resultReq;

    }


}


?>
