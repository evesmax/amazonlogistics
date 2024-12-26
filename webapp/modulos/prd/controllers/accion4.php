<?php
require ("controllers/ordenprd.php");
//Carga el modelo para este controlador
require ("models/accion4.php");

class Accion4 extends OrdenPrd {
	public $Accion4Model;
	public $OrdenPrdModel;

	function __construct() {

		//Se crea el objeto que instancia al modelo que se va a utilizar
		$this -> Accion4Model = new Accion4Model();
		$this -> OrdenPrdModel = $this -> Accion4Model;
		$this -> Accion4Model -> connect();
	}

	function __destruct() {
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this -> Accion4Model -> close();
	}

	function viewAccion4() {
		$resultReq = $this -> OrdenPrdModel -> getEmpleados();
		if ($resultReq -> num_rows > 0) {
			while ($r = $resultReq -> fetch_assoc()) {
				$empleados[] = $r;
			}
		} else {
			$empleados = 0;
		}
		require ("views/acciones/accion4.php");
	}

	function a_clipasoAccion4() {
		session_start();
		unset($_SESSION['v_rePr']);
		$idop = $_POST['idop'];
		$paso = $_POST['paso'];
		$accion = $_POST['accion'];
		$idap = $_POST['idap'];

		if ($accion == 4) {
			$tr = '';
			$tr5 = '';
			$rsqlpaso4 = $this -> OrdenPrdModel -> sqlPaso4($idop);
			if ($rsqlpaso4 -> num_rows > 0) {
				while ($rowSqlpaso4 = $rsqlpaso4 -> fetch_assoc()) {
					$tr .= '
			<tr id="tr_empp_' . $rowSqlpaso4['idEmpleado'] . '">
			<td>' . $rowSqlpaso4['nombre'] . '</td><td>
			<button id="eliemp4" style=" padding: 0px;  height:33px;" onclick="eliemp4(' . $rowSqlpaso4['idEmpleado'] . ');" class="btn btn-danger btn-sm btn-block">
			Elimina
			</button></td>
			</tr>';
				}
				$JSON = array('success' => 1, 'data' => $tr);
				echo json_encode($JSON);
				exit();
			} else {
				$JSON = array('success' => 1, 'data' => '');
				echo json_encode($JSON);
				exit();
			}
		}

	}
	function a_guardarPaso4(){
		
    	$idsProductos=trim($_POST['idsProductos']);
      	$paso=trim($_POST['paso']);
      	$accion=trim($_POST['accion']);
      	$idop=trim($_POST['idop']);
      	$idap=trim($_POST['idap']);
		$idp=trim($_POST['idp']);
	  	echo $this -> Accion4Model -> savePaso4($idsProductos,$accion,$idop,$paso,$idap); 
	}
}
?>