<?php
//ini_set('display_erros', 1);
require('common.php');
require('models/orden_servicio.php');

class Orden_Servicio extends Common {

	public $OrdenServicio;

	function __construct(){
		$this->OrdenServicio = new OrdenServicio();
		$this->OrdenServicio->connect();
	}

	function __destruct() {
		$this->OrdenServicio->close();
	}

	 function index() {
	 	$destino = $this->OrdenServicio->destinos();
	 	$solicitudes = $this->OrdenServicio->solicitudes();
	 	$clientes = $this->OrdenServicio->clientes();
	 	$aeronaves = $this->OrdenServicio->lista_aeronaves();
		require 'views/orden_servicio/orden.php';
	}
	function indexAdmin(){
		$solicitudes = $this->OrdenServicio->solicitudes();
		$formasDePago = $this->OrdenServicio->formasDePago();
		$listaFormasPago = $this->OrdenServicio->listaFormasPago();
		$sucursales = $this->OrdenServicio->getSucursales();
		$segmentos = $this->OrdenServicio->getSegmentos();
		$categorias = $this->OrdenServicio->getCategorias();
		$cuentas = $this->OrdenServicio->cuentasBancarias();
		$productos = $this->OrdenServicio->productos();
		require 'views/orden_servicio/ordenadmin.php';
	}
	function obtenerTipoDeCambioPorMoneda(){
		$idTipoMoneda = $_POST["tipoMoneda"];

		$res = $this->OrdenServicio->obtenerTipoDeCambioPorMoneda($idTipoMoneda);

		echo json_encode($res["tipoDeCambio"]);
	}


	function calcularCostoViaje(){
		$idAeroNave = $_POST["idAeroNave"];
		$minutos    = $_POST["minutos"];

		$res = $this->OrdenServicio->calcularCostoViaje($idAeroNave,$minutos);

		echo json_encode($res[0]);
	}

	function guarda_solicitud(){
		$num_viaje =  $_POST['num_viaje'];
		$origen = $_POST['origen'];
		$destino = $_POST['destino'];
		$pasajeros = $_POST['pasajeros'];
		$pasajeros_nom = $_POST['pasajeros_nom'];
		$aeronave =  $_POST['aeronave'];
		$escalas = $_POST['escalas'];
		$escalas_array = $_POST['escalas_array'];
		$ida = $_POST['ida'];
		$regreso = $_POST['regreso'];
		$redondo = $_POST['redondo'];
		$tipoViaje = $_POST['tipoViaje'];
		$totalTiempo = $_POST['totalTiempo'];
		$nombreCliente = $_POST['nombreCliente'];
		$costo_viaje =  $_POST['costo_viaje'];
		$idmoneda =  $_POST['idmoneda'];
		$tipo_cambio =  $_POST['tipo_cambio'];
		$tarifaDeViaje = $_POST["tarifaDeViaje"];

		$res = $this->OrdenServicio->guarda_solicitud($num_viaje,$origen,$destino,$pasajeros,$pasajeros_nom,$aeronave,$escalas,$escalas_array,$ida,$regreso,$redondo,$tipoViaje,$totalTiempo,$nombreCliente,$costo_viaje,$idmoneda,$tipo_cambio,$tarifaDeViaje);

		echo json_encode($res);
	}
	function gastosInfoSum(){
		$idSolicitud = $_POST["idSolicitud"];

		$res = $this->OrdenServicio->gastosInfoSum($idSolicitud);

		echo json_encode($res[0]);
	}
	function gastosInfo(){
		$id = $_POST['id'];
		$datos = array();
		$arreglo = $_POST['arreglo'];
		$limite = count($arreglo)-1;


			$res = $this->OrdenServicio->gastosInfo($id);
			while($l = $res->fetch_object()){
				$idGasto = $l->id;
				array_push($datos,array(
					'num_viaje' => "<input type='hidden' class='num_viaje' gasto='$idGasto' id='num_viaje_$idGasto' value='$l->num_viaje'>".$l->num_viaje,
					'fecha' => "<input size='15' type='text' class='form-control fecha' id='fecha_$idGasto' value='".$l->fecha."'>",
					'categoria' => "<input type='hidden' class='idcategoria' id='idcategoria_$idGasto' value='$l->idcategoria'>".utf8_encode($l->categoria),
					'importe' => "<input size='15' type='text' class='form-control importe' id='importe_$idGasto' value='".number_format($l->importe,2)."'>",
					'Moneda' => $this->monedas($l->idMoneda,$idGasto),
					'tipoCambio' => "<input size='15' type='text' class='form-control tipoCambio' id='tipoCambio_$idGasto' value='".$l->tipoCambio."'>",
					'cuenta' => $this->cuentas($l->cuenta,$idGasto),
					'formaPago' => $this->formaPago($l->formaPago,$idGasto),
					'referencia' => "<input size='15' type='text' class='form-control referencia' id='referencia_$idGasto' value='".$l->referencia."'>"
					));
			}

			if(is_array($arreglo)){// SI ES UN ARREGLO
				$cont = 1;
				for($i=0;$i<=$limite;$i++){
					$info_cat = $this->OrdenServicio->infocat($arreglo[$i]);

					$idGasto = '0_temp_'.$cont;
					$num_viaje = $this->OrdenServicio->num_viaje($id);
					$fecha = date('Y-m-d H:i:s');
					$categoria = $info_cat['categoria'];
					$idcategoria = $info_cat['idcategoria'];
					$importe = $info_cat['importe'];
					$Moneda = $info_cat['cod_moneda'];
					$idMoneda = $info_cat['idMoneda'];
					$tipoCambio = '1.00';
					$cuenta = 0;
					$formaPago = 1;
					$referencia = '';

					array_push($datos,array(
						'num_viaje' => "<input type='hidden' class='num_viaje_temp' num='$cont' id='num_viaje_$idGasto' value='$num_viaje'>*<i style='color:gray;' title='Registro aún no guardado'>".$num_viaje."</i>",
						'fecha' => "<input size='15' type='text' class='form-control fecha' id='fecha_$idGasto' value='".$fecha."'>",
						'categoria' => "<input type='hidden' class='idcategoria_temp' id='idcategoria_$idGasto' value='$idcategoria'>".utf8_encode($categoria),
						'importe' => "<input size='15' type='text' class='form-control importe_temp' id='importe_$idGasto' value='".number_format($importe,2)."'>",
						'Moneda' => $this->monedas($idMoneda,$idGasto),
						'tipoCambio' => "<input size='15' type='text' class='form-control tipoCambio_temp' id='tipoCambio_$idGasto' value='".$tipoCambio."'>",
						'cuenta' => $this->cuentas($cuenta,$idGasto),
						'formaPago' => $this->formaPago($formaPago,$idGasto),
						'referencia' => "<input size='15' type='text' class='form-control referencia_temp' id='referencia_$idGasto' value='".$referencia."'>"
						));
					$cont++;
				}
			}

		echo json_encode($datos);
	}
	function saveGasto(){
		$idSolicitud = $_POST['idSolicitud'];
		$fecha = $_POST['fecha'];
		$importe = $_POST['importe'];
		$formaPago = $_POST['formaPago'];
		$cuentaGasto = $_POST['cuentaGasto'];
		$segmentoGasto = $_POST['segmentoGasto'];
		$sucursalGasto = $_POST['sucursalGasto'];
		$categoriaGasto = $_POST['categoriaGasto'];
		$referenciaGasto = $_POST['referenciaGasto'];

		$res = $this->OrdenServicio->saveGasto($idSolicitud,$fecha,$importe,$formaPago,$cuentaGasto,$segmentoGasto,$sucursalGasto,$categoriaGasto,$referenciaGasto);

		echo json_encode($res);

	}
	function eliminaGasto(){
		$id = $_POST['id'];

		$res = $this->OrdenServicio->eliminaGasto($id);
		echo json_encode($res);
	}
	function agregaPCoti(){
		$id = $_POST['idProd'];
		$res = $this->OrdenServicio->agregaPCoti($id);
		echo json_encode($res);
	}
	function calculaPrecios(){
		$productos = $_POST['productos'];

		$precios = $this->OrdenServicio->calculaImpuestos($productos);
		echo json_encode($precios);
	}
	function gurdarCoti(){
		$productos= $_POST['productos'];
        $idSoliCoti = $_POST['idSoliCoti'];
        $subTotal = $_POST['subTotal'];
        $total = $_POST['total'];
        $obs = $_POST['obs'];
		$precios = $this->OrdenServicio->gurdarCoti($productos,$idSoliCoti,$subTotal,$total,$obs);
		echo json_encode($precios);
	}
	function pdfCotizacion(){
		 $idSoliCoti = $_POST['idSoliCoti'];
		$pdf = $this->OrdenServicio->pdfCotizacion($idSoliCoti);
		echo json_encode($pdf);
	}
  	function sendCajaOrden(){
        $idOrden = $_POST['id'];
        $products = $this->OrdenServicio->sendCajaOrden($idOrden);
        echo json_encode($products);
    }

    function listaCategorias()
    {
    	$array = array();
    	$cats = $this->OrdenServicio->listaCategorias();
    	$tabla = '';
    	while($c = $cats->fetch_object()){
    		$tabla .= "<tr><td><input type='checkbox' class='checks' check='$c->id'></td><td idcategoria='$c->id'>($c->clave) $c->nombre</td><td idmoneda='$c->moneda'>$c->monedaLt</td><td><input type='text' class='importes form-control' value='$c->importe' readonly></td></tr>";
    	}
    	$tabla .= "<tr><td colspan='2'></td><td>Total de de gastos</td><td id='total_importes'>$ 0.00</td></tr>";
    	echo $tabla;
    }

    function cuentas($cuenta,$idGasto)
    {
    	$res = $this->OrdenServicio->cuentas();
    	$select = "<select class='form-control' width='200' style='width: 200px' id='cuentas_$idGasto'>";
    	while($r = $res->fetch_assoc()){
    		$selected = '';
    		if($cuenta == $r['account_id'])
    			$selected = 'selected';

    		$select .= "<option value='".$r['idbancaria']."' $selected>(".$r['cuenta'].") ".$r['nom_banco']."</option>";
    	}
    	$select .= "</select>";
    	return $select;
    }

    function monedas($idmoneda,$idGasto)
    {
    	//$res = $this->OrdenServicio->cuentas();

    	$select = "<select class='form-control' width='100' style='width: 100px' id='idmoneda_$idGasto'>";

    	$selected = '';
    	if($idmoneda == 1)
    		$selected = 'selected';
    	$select .= "<option value='1' $selected >MXN</option>";
    	$selected = '';
    	if($idmoneda == 2)
    		$selected = 'selected';
    	$select .= "<option value='2' $selected >USD</option>";
    	$selected = '';
    	if($idmoneda == 3)
    		$selected = 'selected';
    	$select .= "<option value='3' $selected >EUR</option>";

    	$select .= "</select>";

    	return $select;
    }

    function formaPago($formaPago,$idGasto)
    {
    	$res = $this->OrdenServicio->formaPago();
    	$select = "<select class='form-control' id='formaPago_$idGasto'>";
    	while($r = $res->fetch_assoc()){
    		$selected = '';
    		if($formaPago == $r['idFormapago'])
    			$selected = 'selected';

    		$select .= "<option value='".$r['idFormapago']."' $selected>".$r['nombre']."</option>";
    	}
    	$select .= "</select>";
    	return $select;
    }

    function guardar_gastos()
    {
    	echo $this->OrdenServicio->guardar_gastos($_POST);
    }

    function autorizar()
    {
    	echo $this->OrdenServicio->autorizar($_POST['idsolicitud'],$_POST['aprobado']);
    }
}
