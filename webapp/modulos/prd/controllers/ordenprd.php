<?php

require ("controllers/listadoprd.php");
//Carga el modelo para este controlador
require ("models/ordenprd.php");

class OrdenPrd extends ListadoPrd {
	public $OrdenPrdModel;
	public $ListadoPrdModel;

	function __construct() {
		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> OrdenPrdModel = new OrdenPrdModel();
		$this -> ListadoPrdModel = $this -> OrdenPrdModel;
		$this -> OrdenPrdModel -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> OrdenPrdModel -> close();
	}

	function viewOrdenPrd() {
		$resultReq = $this -> OrdenPrdModel -> getProductos5();
		if ($resultReq -> num_rows > 0) {
			while ($r = $resultReq -> fetch_assoc()) {
				$productos[] = $r;
			}
		} else {
			$productos = 0;
		}
		//
		$resultReq = $this -> OrdenPrdModel -> getUsuario();
		if ($resultReq -> num_rows > 0) {
			$set = $resultReq -> fetch_assoc();
			$username = $set['username'];
			$iduser = $set['idempleado'];
		} else {
			$username = 'Favor de salir y loguearse nuevamente';
			$iduser = '0';
		}
		//
		$resultReq = $this -> OrdenPrdModel -> getSucursales();
		if ($resultReq -> num_rows > 0) {
			while ($r = $resultReq -> fetch_assoc()) {
				$sucursales[] = $r;
			}
		} else {
			$sucursales = 0;
		}
		//
		$resultReq = $this -> OrdenPrdModel -> getEmpleados();
		if ($resultReq -> num_rows > 0) {
			while ($r = $resultReq -> fetch_assoc()) {
				$empleados[] = $r;
			}
		} else {
			$empleados = 0;
		}
		require ('views/produccion/ordenprd.php');
	}

	function a_addProductoProduccion() {
		$resultReq = $this -> OrdenPrdModel -> getEmpleados();
		if ($resultReq -> num_rows > 0) {
			while ($r = $resultReq -> fetch_assoc()) {
				$empleados[] = $r;
			}
		} else {
			$empleados = 0;
		}
		$idProducto = $_POST['idProducto'];
		$resultReq = $this -> OrdenPrdModel -> addProductoProduccion($idProducto);
		$cccar = 0;
		$html = '';
		if ($resultReq -> num_rows > 0) {
			$row = $resultReq -> fetch_array();
			$producto[] = $row;

			$adds = '<select id="prelis" onchange="refreshCants(' . $producto[0]['id'] . ',0,0)">
          	<option value="' . $producto[0]['costo'] . '>0">$' . $producto[0]['costo'] . ' Precio lista</option>';
			$adds .= '<option value="OTRO>x">Otro precio</option>';

			$JSON = array('success' => 1, 'datos' => $producto, 'adds' => $adds, 'car' => $html, 'cccar' => $cccar);
		} else {
			$JSON = array('success' => 0);
		}
		echo json_encode($JSON);
	}

	function a_nuevaorden() {
		$bandera = $this -> OrdenPrdModel -> bandera();
		$resultReq = $this -> OrdenPrdModel -> getLastOrden();
		if ($resultReq -> num_rows > 0) {
			$row = $resultReq -> fetch_array();
			$JSON = array('success' => 1, 'op' => $row['id'], 'regordenp' => $bandera['regordenp']);
		} else {
			$JSON = array('success' => 0);
		}

		echo json_encode($JSON);

	}

	function getUsuario() {
		session_start();
		$idusr = $_SESSION['accelog_idempleado'];
		$myQuery = "SELECT concat(nombre,' ',apellido1) as username, idempleado from empleados where idempleado='$idusr';";
		$nreq = $this -> query($myQuery);
		session_destroy();
		return $nreq;
	}

	function a_guardarOrdenP() {

		$idsProductos = trim($_POST['idsProductos']);
		$fecha_registro = trim($_POST['fecha_registro']);
		$fecha_entrega = trim($_POST['fecha_entrega']);
		$prioridad = trim($_POST['prioridad']);
		$sucursal = trim($_POST['sucursal']);
		$option = trim($_POST['option']);
		$obs = trim($_POST['obs']);
		$iduserlog = trim($_POST['iduserlog']);
		$id_op = trim($_POST['id_op']);
		$ttt = trim($_POST['ttt']);
		$orden = trim($_POST['orden']);
		$sol = trim($_POST['sol']);

		$lote = json_decode($_POST['lote'], true);
		$lotes = array();
		foreach ($lote as $k => $v) {
			foreach ($v as $k => $l) {
				$lotes[$k] = $l;
			}

		}

		if ($option == 1) {//guarda orden primero
			$result = $this -> OrdenPrdModel -> saveOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lotes);

		}

		if ($option == 2) {
			$result = $this -> OrdenPrdModel -> modifyOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lotes);
		}

		if ($option == 3) {

			$result = $this -> OrdenPrdModel -> savePre($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $orden, $sol);
		}
		echo $result;

	}

	function a_editarordenp() {
		$idReq = $_POST['idReq'];
		$m = $_POST['m'];
		$mod = $_POST['mod'];
		$pr = $_POST['pr'];
		$resultReq = $this -> OrdenPrdModel -> editarordenp($idReq);

		if ($resultReq -> num_rows > 0) {

			$row = $resultReq -> fetch_assoc();
			$row['fi'] = substr($row['fi'], 0, 10);
			$row['fe'] = substr($row['fe'], 0, 10);

			$row2['adds'] = '';

			$resultReq2 = $this -> OrdenPrdModel -> productosOp($idReq, $m);
			while ($row2 = $resultReq2 -> fetch_assoc()) {

				$productos[] = $row2;
			}

			$JSON = array('success' => 1, 'requisicion' => $row, 'productos' => $productos, 'adds' => $adds, 'ss' => 0);
		} else {
			$JSON = array('success' => 0);
		}

		echo json_encode($JSON);

	}

	function a_eliminaOP() {
		$idop = $_POST['idop'];
		$resultReq = $this -> OrdenPrdModel -> delOP($idop);
		echo $resultReq;
	}

	function a_explosionMat() {
		$idop = $_POST['idop'];
		$resultReq = $this -> OrdenPrdModel -> editarordenp($idop);
		if ($resultReq -> num_rows > 0) {

			$row = $resultReq -> fetch_assoc();
			$row['fi'] = substr($row['fi'], 0, 10);
			$row['fe'] = substr($row['fe'], 0, 10);
			$row2['adds'] = '';
			$insumos = array();
			$resultReq2 = $this -> OrdenPrdModel -> productosOp($idop, 1);
			while ($row2 = $resultReq2 -> fetch_assoc()) {
				$resultReq3 = $this -> OrdenPrdModel -> productosOpExplosion($idop, $row2['id_producto']);
				$cantitotalinsumos = 0;
				if ($resultReq3['total'] > 0) {
					foreach ($resultReq3['rows'] as $key => $row3) {
						$cantitotalinsumos += $row3['canti'];
						//consulta pa los proveedores y costos
						$existencias = $this -> OrdenPrdModel -> getExistenciasNueva($row3['idProducto'], '0');

						if ($row3['idcostoprovs'] != '') {
							$existencias = $this -> OrdenPrdModel -> getExistenciasNueva($row3['idProducto'], '0');
							$resultReq4 = $this -> OrdenPrdModel -> proveedoresCostoOP($row3['idcostoprovs']);
							if ($resultReq4 -> num_rows > 0) {
								$cadprovs = "<select id='cmbProv_" . $row2['id_producto'] . "_" . $row3['idProducto'] . "' onchange='refreshCants(" . $row3['idProducto'] . "," . $row2['id_producto'] . ");' id='insprv'><option value='0-0'>Seleccione</option>";
								while ($row4 = $resultReq4 -> fetch_assoc()) {
									$cadprovs .= "<option value='" . $row4['id_proveedor'] . "-" . $row4['costo'] . "'>" . $row4['razon_social'] . "</option>";
								}
								$cadprovs .= '</select>';
							} else {
								$cadprovs = "<select id='insprv'><option value='0-0'>No hay proveedores para este producto</option></select>";
							}
						} else {
							$cadprovs = "<select id='insprv'><option value='0-0'>No hay proveedores</option></select>";

						}

						$row3['listprovs'] = $cadprovs;
						$row3['existencias'] = $existencias;
						$row2['insumos'][] = $row3;
					}

				} else {
					$row2['insumos'] = 0;
				}
				$productos[] = $row2;
			}

			$JSON = array('success' => 1, 'requisicion' => $row, 'productos' => $productos, 'adds' => $adds, 'ss' => 0, 'cantidadinsumos' => $cantitotalinsumos);
		} else {
			$JSON = array('success' => 0);
		}

		echo json_encode($JSON);
	}

	function a_guardarUsar() {
		$id_op = ($_POST['id_op']);
		$iduserlog = trim($_POST['iduserlog']);
		/*se actualiza la formula por los insumos variables cambiados*/
		//$update
		if ($_REQUEST['insumosvariables'] > 0) {
			$obj = json_decode($_POST['insumo'], true);
			foreach ($obj['datos'] as $k => $v) {

				$prdiniciada = $this -> OrdenPrdModel -> ordenPrdIniciada($v['idProduct']);
				if ($prdiniciada > 0) {

					if ($_REQUEST['continua'] > 0) {
						$result = $this -> OrdenPrdModel -> saveUsar($id_op, $iduserlog);
						echo $result;
					} else {
						echo "si";
					}

					exit();
				}

				$this -> OrdenPrdModel -> updateInsumosVariables($v['idProduct'], $v['idinsumo'], $v['cantidad']);
			}
		}
		/*fin variables*/
		$result = $this -> OrdenPrdModel -> saveUsar($id_op, $iduserlog);
		echo $result;
	}

	function a_autorizar() {
		$id = $_POST['id'];
		$this -> OrdenPrdModel -> autorizar($id);
	}

	function a_explosionMatMasiva() {

		$row = $this -> ListadoPrdModel -> bandera();
		$mostrarprd = $row['mostrar_prov_op'];

		$idop = implode(",", $_REQUEST['idop']);
		$row2['adds'] = '';
		$insumos = array();
		$insumos = array();
		$row2 = $this -> OrdenPrdModel -> productosOpMasiva($idop);
		$resultReq3 = $this -> OrdenPrdModel -> productosOpExplosionMasiva($idop);
		$cantitotalinsumos = 0;
		if ($resultReq3['total'] > 0) {
			foreach ($resultReq3['rows'] as $key => $row3) {
				$cantitotalinsumos += $row3['canti'];
				//consulta pa los proveedores y costos
				if ($mostrarprd == 1) {

				}

				$existencias = $this -> OrdenPrdModel -> getExistenciasNueva($row3['idProducto'], '0');
				$resultReq4 = $this -> OrdenPrdModel -> proveedoresCostoOParaMasivo($row3['idProducto']);

				if ($resultReq4 -> num_rows > 0) {
					$cadprovs = "<select id='cmbProv_" . $row2['id_producto'] . "_" . $row3['idProducto'] . "' onchange='refreshCants(" . $row3['idProducto'] . "," . $row2['id_producto'] . ");' id='insprv'><option value='0-0'>Seleccione</option>";
					while ($row4 = $resultReq4 -> fetch_assoc()) {
						$cadprovs .= "<option value='" . $row4['id_proveedor'] . "-" . $row4['costo'] . "'>" . $row4['razon_social'] . "</option>";
					}
					$cadprovs .= '</select>';
				} else {
					$cadprovs = "<select id='insprv'><option value='0-0'>No hay proveedores para este producto</option></select>";
				}
				$row3['listprovs'] = $cadprovs;
				$row3['existencias'] = $existencias;
				$row2['insumos'][] = $row3;
			}

		} else {
			$row2['insumos'] = 0;
		}

		$productos[] = $row2;
		$JSON = array('success' => 1, 'requisicion' => $row, 'productos' => $productos, 'adds' => $adds, 'ss' => 0, 'cantidadinsumos' => $cantitotalinsumos);

		echo json_encode($JSON);

	}

function calculaPrecios(){
      $productos = $_POST['productos'];

      $precios = $this->OrdenPrdModel->calculaImpuestos($productos);
      echo json_encode($precios);
    }
	//fin explosion masiva
}
?>