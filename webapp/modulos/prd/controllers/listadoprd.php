<?php
//Carga la funciones comunes top y footer
require('common.php');
//Carga el modelo para este controlador
require("models/listadoprd.php");

class ListadoPrd extends Common{
	public $ListadoPrdModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->ListadoPrdModel = new ListadoPrdModel();
        $this->ListadoPrdModel->connect();
    }
    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->ListadoPrdModel->close();
    }
	function a_listaOrdenes(){
    	$resultReq =  $this->ListadoPrdModel->listaOrdenes();
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
	function listadoOprd(){
		$row =  $this->ListadoPrdModel->bandera();
      	$insumosvariables=$row['insumosvariables'];
		$tipoexplosion = $row['explosionmat'];
		$ordenmasiva = $row['regordenp'];
		$mostrarprv = $row['mostrar_prov_op'];
		$ord_x_lote = $row['ord_x_lotes'];
		
		require('views/produccion/listadoprd.php');
	}
	function a_listaOrdenesP() {

    	$resultReq 	=  $this->ListadoPrdModel->listaOrdenesP();
      	$row 		= $this->ListadoPrdModel->bandera();
     	$bandera	= $row['aut_ord_prod'];
      	$orden		= $row['genoc_sinreq'];//sin requisision 
		$tipoexplosion = $row['explosionmat'];
		$cant_x_lote = $row['cant_x_lote'];

	    $listas=array();
	    $listas['data']='';
		  
		$listas["tipoexplosion"]=$tipoexplosion;
	    if($resultReq->num_rows>0){
	    	$acciones='Sin botones';
	       	while ($r = $resultReq->fetch_array()) {
				$check = "";
				$incremen = 0;
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
	
				$r[9+$incremen]=$acciones;
			  	$listas['data'][]=$r;
	        }
	    }else{
	    	$listas['data']=array();
	    }
		echo json_encode($listas);
 	}
    
    
}
?>