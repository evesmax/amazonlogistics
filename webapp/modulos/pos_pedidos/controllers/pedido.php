<?php 
//ini_set('display_errors', 1);
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/pedido.php");

class Pedido extends Common {

    public $pedidoModel;

    function __construct() {
        //Se crea el objeto que instancia al modelo que se va a utilizar
        $this->pedidoModel = new pedidoModel();
        $this->pedidoModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->pedidoModel->close();
    }
    function imprimeGridP() {
       //echo 'eeee';
       
       require('views/pedido/pedido.php');
        
    }
	
    function loadProductsP(){
        require('views/pedido/newPedido.php');
    }
    function buscaP(){
        $idEmpleado = $_POST['empleado'];
        $idCliente = $_POST['cliente'];
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];

        $grid = $this->pedidoModel->buscaP($idEmpleado,$idCliente,$desde,$hasta);
        echo json_encode($grid);
    }
    function buscaP2(){
        $idCliente = $_POST['cliente'];
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];

        $grid = $this->pedidoModel->buscaP2($idCliente,$desde,$hasta);
        echo json_encode($grid);
    }
    function indexGridPedidosCliente(){
        $empleado=$_SESSION["accelog_idempleado"];
        $cliente = $this->pedidoModel->buscaClienteP($empleado);
        $pedidos = $this->pedidoModel->pedidosGridCliente($cliente['idCliente']);
            
        require('views/pedido/pedidosGridCliente.php');
        //echo $cliente;
    }


    function printGridP(){
        $pedidos = $this->pedidoModel->getGridP();
        echo json_encode($pedidos);
    }
    function printGridP2(){
        $pedidos = $this->pedidoModel->getGridP2();
        $res2['data'] = $pedidos['data'];
        foreach ($pedidos['data'] as $key => $value) {
            if($value['idCotizacion']==null){
                $cotizacion='';
            }else{
                $cotizacion=$value['idCotizacion'];
            }
                    switch($value['status']) {
                        case '0':
                            $estado = '<span class="label label-danger">Cancelado</span>';
                            $link = '#';
                            break;
                        case '1':
                            $estado = '<a onclick="aProceso('.$value['id'].');" class="btn btn-default">Activo</a><a onclick="cancelar('.$value['id'].');" class="btn btn-danger">Cancelar</a>';
                            $link = 'index.php?c=caja&f=indexPedido2&pe='.$value['id'].'';
                            break;
                        case '2':
                            $estado = '<a onclick="aTerminado('.$value['id'].');" class="btn btn-warning">Proceso</a>';
                            $link = 'index.php?c=caja&f=indexPedido2&pe='.$value['id'].'';
                            break;
                        case '3':
                            $estado = '<span class="label label-primary">Terminado</span><a onclick="pedido('.$value['id'].');" class="btn btn-default"><i class="fa fa-shopping-basket" ></i></a>';
                            $link = '#';;
                            break;
                        case '4':
                            $estado = '<span class="label label-info">En Venta</span><a onclick="pedido('.$value['id'].');" class="btn btn-default"><i class="fa fa-shopping-basket" aria-hidden="true"></i></a>';
                            $link = '#';
                            break;  
                        case '5':
                            $estado = '<span class="label label-success">Vendido</span>';
                            $link = '#';
                            break;

                    }
            $res2['data'][$key]['id'] = '<a href="'.$link.'">'.$value['id'].'</a>';
            $res2['data'][$key]['idCotizacion'] = '<a href="'.$link.'">'.$cotizacion.'</a>';
            $res2['data'][$key]['fecha'] = '<a href="'.$link.'">'.$value['fecha'].'</a>';
            $res2['data'][$key]['nombre'] = '<a href="'.$link.'">'.$value['nombre'].'</a>';
            $res2['data'][$key]['usuario'] = '<a href="'.$link.'">'.$value['usuario'].'</a>';
            $res2['data'][$key]['total'] = '<a href="'.$link.'">$'.number_format($value['total'],2).'</a>';
            $res2['data'][$key]['pdf'] = '<a onclick="FunPdf('.$value['id'].');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
            $res2['data'][$key]['status'] = $estado;
        }
        echo json_encode($res2);
    }
    function printFiltrosP(){
        $filtros = $this->pedidoModel->printFiltrosP();
        echo json_encode($filtros);
    }
    function pedidoView(){
        $idPedido = $_GET['pe'];
        
        $filtros = $this->pedidoModel->pedidoView($idPedido);
        //print_r($filtros);
        require('views/pedido/loadPedido.php');
    }
    function pedidoView2(){
        $idPedido = $_GET['pe'];
        
        $filtros = $this->pedidoModel->pedidoView($idPedido);
        //print_r($filtros);
        require('views/pedido/loadPedido2.php');
    }
    function pedidoView1(){
        require('views/pedido/newPedido.php');
    }
    function addProductP(){
        $idProducto = $_POST['idProducto'];
        $cantidad = $_POST['cantidad'];
        $addProduct = $this->pedidoModel->addProductP($idProducto,$cantidad);
  
        echo json_encode($addProduct);
    }
    function sendP(){
        $idPedido = $_POST['idPedido'];
        $idCliente = $_POST['idCliente'];
        $observacion = $_POST['observacion'];
        $cotizacion = $this->pedidoModel->saveP($idCliente,$observacion,$idPedido);

        echo json_encode($cotizacion);
    }
    function deleteProP(){
          $idPedido = $_POST['idPedido']; 
          $idProducto = $_POST['id'];
          $products = $this->pedidoModel->deleteProP($idProducto,$idPedido);
          echo json_encode($products);
    }
    function sendCajaPedido(){
        $idPedido = $_POST['idPedido'];
        $products = $this->pedidoModel->sendCajaPedido($idPedido);
        echo json_encode($products);
    }   
    function aProceso(){
        $idPedido = $_POST['id'];

        $res = $this->pedidoModel->aProceso($idPedido);

        echo json_encode($res);
    }
    function aTerminado(){
        $idPedido = $_POST['id'];

        $res = $this->pedidoModel->aTerminado($idPedido);
        
        echo json_encode($res);
    }
    function cancelar(){
        $idPedido = $_POST['id'];

        $res = $this->pedidoModel->cancelar($idPedido);
        
        echo json_encode($res);
    }
	//krmn
	function verPrdOrden(){
		$almacen = $this->pedidoModel->almacenes();
		$arrayAlmacen = array();
		if($almacen!=0){
			while($a = $almacen->fetch_object()){
				$arrayAlmacen[$a->id] = $a->nombre;
			}
			
		}
		require('views/pedido/pedordprod.php');
	}
	function validaPrd(){
		/*krmn pedido*/
   		$validaprd = $this->pedidoModel->validaproduccion();
	  	$bandera = 0;
		if($validaprd == 1){
		 	foreach ($_SESSION["caja"] as $key => $producto) {
				if($key!='cargos' && $key!='descGeneral' && $key!='descGeneralCant'){
					$producto = (object) $producto;
					
					$sq = $this->pedidoModel->configPrd();
					$ped = $sq->fetch_object();
					//si es 1 es que si creara la orden de prd
					// pero solo sera de productos q sean 8 y 9 transformado y fabricado
					$tipoprd = $this->pedidoModel->tipoPrd($producto->idProducto);
					
					if( $ped->produccion_pedidos == 1 && ($tipoprd == 8 || $tipoprd == 9) ){
						//si se ara pedido
						$bandera+=1;
					}
					
				}
			
			}
		}
		echo $bandera;
	}
	function existenciaPrd(){
		$exi = $this->pedidoModel->existenciaPrd($_REQUEST['almacen'], $_REQUEST['idprd']);
		if($exi!=0){
			$e = $exi->fetch_object();
			$exi = $e->cantidad;
		}
		echo $exi;
	}
}






















?>