<?php
/**
 * @author Fer De La Cruz
 */




require('common.php');
require("models/recetas.php");

class recetas extends Common{
	public $recetasModel;

	function __construct(){
		$this->recetasModel = new recetasModel();
	}


///////////////// ******** ---- 		vista_recetas				------ ************ //////////////////
	//////// Carga la vista en la que se consultan las recetas

	function vista_recetas($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	 // Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

		$vista = $objeto['v'];
		require('views/recetas/vista_recetas.php');
		// echo "Hola";
		// exit (0);
	}

function vista_recetas2($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	 // Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

		$vista = $objeto['v'];
		require('views/recetas/vista_recetas2.php');
		// echo "Hola";
		// exit (0);
	}


///////////////// ******** ---- 		FIN	vista_recetas			------ ************ //////////////////

///////////////// ******** ---- 		vista_nueva			------ ************ //////////////////
	//////// Consulta los productos, las recetas y las agrega a un div
		// Como parametros recibe:
			// div -> div donde se cargara el contenido html
			// btn -> boton del loader

	function vista_nueva($objeto){
		
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	 // Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
		$panel = $objeto['panel'];

	// AM esta variable es para indicar que es edicion $editarproducto 1 es editar 0 es nuevo
		$editarproducto ='';
		$objeto['modi']!='' ? $editarproducto='1' : $editarproducto='0';

	// Consulta los insumos
		$objeto['tipo_producto'] = 3;
		$datos['insumos'] = $this -> recetasModel -> listar_insumos($objeto);
		$datos['insumos'] = $datos['insumos']['rows'];

	// Obtiene las unidades de medida
		$datos['unidades'] = $this -> recetasModel -> listar_unidades($objeto);
		$datos['unidades'] = $datos['unidades']['rows'];

	// Consulta los insumos preparados
		$objeto['tipo'] = 4;
		$datos['insumos_preparados'] = $this -> recetasModel -> listarselect($objeto);
		$datos['insumos_preparados'] = $datos['insumos_preparados']['rows'];
		foreach ($datos['insumos_preparados'] as $key => $value) {
			$datos['insumos'][]=$value;
		}
		//echo json_encode($datos['insumos']);

	// Consulta los productos con fórmula
		$objeto['tipo'] = 5;
		$datos['productos_formula'] = $this -> recetasModel -> listarselect($objeto);
		$datos['productos_formula'] = $datos['productos_formula']['rows'];

  // Consulta los productos terminados
  		$objeto['tipo'] = 1;
			$datos['productos_terminados'] = $this -> recetasModel -> listarselect($objeto);
  		$datos['productos_terminados'] = $datos['productos_terminados']['rows'];

	// Consulta los productos Insumo preparado (4) y Productos con Fórmula (5)
			$objeto['tipo'] = "";
			$objeto['filtro'] = 'insumos_preparados_formula';
			$datos['insumos_preparados_formula'] = $this -> recetasModel -> listarselect($objeto,$editarproducto);
			$datos['insumos_preparados_formula'] = $datos['insumos_preparados_formula']['rows'];

  // Consulta los productos terminados
      $datos['procesos_produccion'] = $this -> recetasModel -> listar_procesos($objeto);
      $datos['procesos_produccion'] = $datos['procesos_produccion']['rows'];

	// Consulta los Pasos de Procesos de Producción
			/*$datos['pasos_procesos_produccion'] = $this -> recetasModel -> listar_acciones_procesos($objeto);
			$datos['pasos_procesos_produccion'] = $datos['pasos_procesos_produccion']['rows'];*/

			$datos['pasos_procesos_produccion'][1]['id'] = 1;
			$datos['pasos_procesos_produccion'][1]['nombre'] = "Paso 1";
			$datos['pasos_procesos_produccion'][2]['id'] = 2;
			$datos['pasos_procesos_produccion'][2]['nombre'] = "Paso 2";
			$datos['pasos_procesos_produccion'][3]['id'] = 3;
			$datos['pasos_procesos_produccion'][3]['nombre'] = "Paso 3";

			//var_dump($datos['pasos_procesos_produccion']);

	// Consulta las Acciones de Procesos de Producción
		  $datos['acciones_procesos_produccion'] = $this -> recetasModel -> listar_acciones_procesos($objeto);
		  $datos['acciones_procesos_produccion'] = $datos['acciones_procesos_produccion']['rows'];

	// Consulta las familias de productos
      $datos['familias_productos'] = $this -> recetasModel -> listar_familias($objeto);
      $datos['familias_productos'] = $datos['familias_productos']['rows'];

	// Consulta las unidades de prueba de laboratorio
			// $datos['lab_unidades'] = $this -> recetasModel -> listar_lab_unidades();
			// $datos['lab_unidades'] = $datos['lab_unidades']['rows'];

			$consulta['campos'] = "id, descripcion";
			$consulta['from'] = "prd_lab_unidades";

			$datos['lab_unidades'] = $this -> recetasModel -> listar_lab_general($consulta['campos'], $consulta['from']);
			$datos['lab_unidades'] = $datos['lab_unidades']['rows'];


			$consulta['from'] = "prd_lab_tipos";
			$datos['lab_tipos'] = $this -> recetasModel -> listar_lab_general($consulta['campos'], $consulta['from']);
			$datos['lab_tipos'] = $datos['lab_tipos']['rows'];

			$datos['lab_conceptos'] = $this -> recetasModel -> listar_lab_conceptos("libre");
			$datos['lab_conceptos'] = $datos['lab_conceptos']['rows'];


	// Inicializa el array de los insumos agregados
		session_start();
		$_SESSION['insumos_agregados'] = '';
		$_SESSION['insumos_producto'] = '';
		$_SESSION['procesos_produccion'] = '';
		$_SESSION['parametros_lab'] = '';
		$_SESSION['produccion_acciones'] = '';
		$datos['productos_proceso'] = '';
		unset($_SESSION['pasos']);

		// Carga la vista de las recetas

		switch($objeto['vista']){
			case "prc_prd":
				//$require = "views/recetas/nueva.php"; -- New form taking over
				$require = "views/recetas/nueva_procesos_produccion.php";
				break;
			case "lab_cpts":
				$require = "views/recetas/nueva_lab_conceptos.php";
				break;
			case "lab_cs_prd":
				$require = "views/recetas/nueva_lab_conceptos_productos.php";
				break;
			case "lab_rgtr":
				$require = "views/recetas/nueva_lab_registro.php";
				break;
			case "frm_prd":
				$require = "views/recetas/nueva_form_producto.php";
				break;
		}

		require($require);
	}

///////////////// ******** ---- 	FIN	vista_nueva			------ ************ //////////////////

///////////////// ******** ---- 		guardar				------ ************ //////////////////
//////// Manda llamar a la funcion que Guarda la receta o insumo preparado en la BD
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	function guardar($objeto) {
	
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();
		$objeto['ids'] = '';


		foreach ($_SESSION['insumos_agregados']['insumos'] as $key => $value) {
			$objeto['ids'] .= $key . ',';
		}

    /*
    echo json_encode($objeto['ids']);
    return 0;
*/

	// Valida que existan insumos o insumos preparados
		if (!empty($objeto['ids'])) {
			$objeto['ids'] = substr($objeto['ids'], 0, -1);

		// Consulta si existe un producto con el mismo nombre o codigo
			//$coincidencia = $this -> recetasModel -> listar_insumos($objeto);
      $coincidencia = 0;
		// Valida que no exista el producto
			if ($coincidencia['total'] < 1) {
				$objeto['margen_ganancia'] = (empty($objeto['margen_ganancia'])) ? 0 : $objeto['margen_ganancia'];


				//FUNCIONABA
				foreach ($_SESSION['insumos_agregados']['insumos'] as $key => $value) {
					$param['id_producto'] = $objeto['prd'];
          $param['id_proceso'] = $value['id'];
          if ($objeto['btn'] == "btn_guardar_receta_prd")
            $resp['insumos'][$key] = $this -> recetasModel -> guardar_producto_proceso($param);
          else {
            $productos_fam = $this -> recetasModel -> listar_productos_por_familia($param['id_producto']);
						$productos_fam = $productos_fam['rows'];
            foreach ($productos_fam as $key => $value) {
              $param['id_producto'] = $value['id'];
              $resp['insumos'][$key] = $this -> recetasModel -> guardar_producto_proceso($param);
            }
          }
        }

      	// echo json_encode($resp['insumos']);
        // return 0;
			// 1 -> Todo bien :)
			// 2 -> Fallo la consulta :(
        //$resp['status'] = (!empty($resp['result'])) ? 1 : 0;
        $resp['status'] = 1;
			} else {
			// El prodcuto ya existe
				$resp['status'] = 3;
			}
		} else {
		// Sin insumos
			$resp['status'] = 2;
		}

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	guardar				------ ************ //////////////////

///////////////// ******** ---- 		guardar_procesos_produccion				------ ************ //////////////////
//////// Manda llamar a la funcion que Guarda la receta o insumo preparado en la BD
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	function guardar_procesos_produccion3() {
		$idFamilia=$_POST['idFamilia'];
		$sel_ciclo=$_POST['sel_ciclo'];
		session_start();

		if (empty($_SESSION['pasos'])) {
  		} else {
 			$this->recetasModel->guardar_producto_proceso3($_SESSION['pasos'],$idFamilia,$sel_ciclo);
  		}

  		if($sel_ciclo==1 || $sel_ciclo==2){
  			$this->recetasModel->guardar_producto_proceso3($_SESSION['pasos'],$idFamilia,$sel_ciclo);
  		}

	}

	function guardar_procesos_produccion2() {


		$idProducto=$_POST['idProducto'];
		$modi=$_POST['modi'];
		$sel_ciclo=$_POST['sel_ciclo'];
		
		session_start();
		$objeto['ids'] = '';
		
		if (empty($_SESSION['pasos'])) {
  		} else {
  			
 			$this->recetasModel->guardar_producto_proceso2($_SESSION['pasos'],$idProducto,$modi,$sel_ciclo,$_POST['accionesDelPaso'],$_POST['data']);
  		}

  		if($sel_ciclo==1 || $sel_ciclo==2){
  			$this->recetasModel->guardar_producto_proceso2($_SESSION['pasos'],$idProducto,$modi,$sel_ciclo,$_POST['accionesDelPaso'],$_POST['data']);
  		}

	}

	function guardar_procesos_produccion($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();
		$objeto['ids'] = '';


		foreach ($_SESSION['procesos_produccion'] as $key => $value) {
			$objeto['ids'] .= $key . ',';
		}

    /*
    echo json_encode($objeto['ids']);
    return 0;
*/

	// Valida que existan insumos o insumos preparados
		if (!empty($objeto['ids'])) {
			$objeto['ids'] = substr($objeto['ids'], 0, -1);

		// Consulta si existe un producto con el mismo nombre o codigo
			//$coincidencia = $this -> recetasModel -> listar_insumos($objeto);
      $coincidencia = 0;
		// Valida que no exista el producto
			if ($coincidencia['total'] < 1) {
				$objeto['margen_ganancia'] = (empty($objeto['margen_ganancia'])) ? 0 : $objeto['margen_ganancia'];


				//FUNCIONABA
				foreach ($_SESSION['procesos_produccion'] as $key => $value) {
					$param['id_producto'] = $objeto['prd'];
          $param['id_proceso'] = $value['id'];
          if ($objeto['btn'] == "btn_guardar_receta_prd")
            $resp['insumos'][$key] = $this -> recetasModel -> guardar_producto_proceso($param);
          else {
						$productos_fam = $this -> recetasModel -> listar_productos_por_familia($param['id_producto']);
						$productos_fam = $productos_fam['rows'];
						foreach ($productos_fam as $key => $value) {
              $param['id_producto'] = $value['id'];
              $resp['insumos'][$key] = $this -> recetasModel -> guardar_producto_proceso($param);
            }
          }
        }

      	// echo json_encode($resp['insumos']);
        // return 0;
			// 1 -> Todo bien :)
			// 2 -> Fallo la consulta :(
        //$resp['status'] = (!empty($resp['result'])) ? 1 : 0;
        $resp['status'] = 1;
			} else {
			// El prodcuto ya existe
				$resp['status'] = 3;
			}
		} else {
		// Sin insumos
			$resp['status'] = 2;
		}

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	guardar				------ ************ //////////////////

///////////////// ******** ---- 		guardar_lab_varias				------ ************ //////////////////

	function guardar_lab_varias($objeto){
		$objeto=(empty($objeto))?$_REQUEST:$objeto;
		session_start();

		$resp['id_lab_concepto'] = $this -> recetasModel -> guardar_lab_varias($objeto);
		$resp['status'] = 1;
		echo json_encode($resp);

	}

///////////////// ******** ---- 		FIN	guardar_lab_varias				------ ************ //////////////////

///////////////// ******** ---- 		guardar_lab_conceptos_productos				------ ************ //////////////////

	function guardar_lab_conceptos_productos($objeto) {
		$objeto=(empty($objeto))?$_REQUEST:$objeto;
		session_start();

		foreach ($_SESSION['parametros_lab'] as $key => $value) {
				$resp = $this -> recetasModel -> guardar_lab_conceptos_productos($_SESSION['parametros_lab'][$key], $objeto['prd']);

		}

		$resp['status'] = 1;
		echo json_encode($resp);
		}

///////////////// ******** ---- 		FIN	guardar_lab_conceptos_productos				------ ************ //////////////////

///////////////// ******** ---- 		guardar_insumos_producto				------ ************ //////////////////

	function guardar_insumos_producto($objeto) {
		$objeto=(empty($objeto))?$_REQUEST:$objeto;
		session_start();

		// com_recetas
		if (!empty($_SESSION['insumos_producto'])){

			//
			$coincidencia = $this -> recetasModel -> listar_insumos($objeto);
			// Valida que no exista el producto
			if ($coincidencia['total'] < 1) {

				$a_ids = array();
				foreach ($_SESSION['insumos_producto'] as $key => $value){
					array_push($a_ids, $value['id']);
				}
				//echo json_encode ($a_ids);
				$ids_insumos = implode(",", $a_ids);

				// app_productos 1/3
				$resp['id_app_prd'] = $this -> recetasModel -> guardar_app_productos($objeto['nombre'], $objeto['codigo'], $objeto['cant_min'], $objeto['unidad'],$objeto['factor']);

				// com_recetas 2/3
				$resp['id_com_recetas'] = $this -> recetasModel -> guardar_com_recetas($resp['id_app_prd'], $objeto['nombre'], $ids_insumos);

				// app_producto_material 3/3
				foreach ($_SESSION['insumos_producto'] as $key => $value) {
						//"Hola: " . json_encode($_SESSION['insumos_producto']);
						$resp['id_app_prd_mat'] = $this -> recetasModel -> guardar_app_producto_material($resp['id_app_prd'], $value['cantidad'], $value['idunidad'], $value['id'], 1);
				}
				// Producto grabado con éxito
				$resp['status'] = 1;
			}
			else {
				// El producto ya existe
				$resp['status'] = 3;
			}
		} else {
			// Sin insumos
			$resp['status'] = 2;
		}

		// echo json_encode($resp['status']);
		echo json_encode($resp);
	}

///////////////// ******** ---- 		FIN	guardar_insumos_producto				------ ************ //////////////////

///////////////// ******** ---- 		guardar_lab_registro				------ ************ //////////////////

	function guardar_lab_registro($objeto) {
		$objeto=(empty($objeto))?$_REQUEST:$objeto;
		session_start();

		$resp['id'] = $this -> recetasModel -> guardar_lab_registro($objeto);

		foreach ($_SESSION['formulario_lab_campo'] as $key => $value) {
			$valor_num = -1;
			$valor_alfa = "";
			if ($value['is_numeric'] == 1)
				$valor_num = $value['valor'];
			else
				$valor_alfa = $value['valor'];
			$detalle = $this -> recetasModel -> guardar_lab_detalle($resp['id'], $value['id'], $valor_num, $valor_alfa);
		}

		$resp['status'] = 1;
		echo json_encode($resp);
		}

///////////////// ******** ---- 		FIN	guardar_lab_registro				------ ************ //////////////////


///////////////// ******** ---- 		agregar_insumo			------ ************ //////////////////
//////// Agrega un insumo al array de los insumos agregados y carga la vista donde aparecen
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// div -> ID de la div donde se cargara la vista
		// idunidad -> ID de la unidad
		// idunidadCompra -> ID de la unidad de compra
		// nombre -> nombre del insumo
		// unidad -> nombre de la unidad

	function agregar_insumo($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto=(empty($objeto))?$_REQUEST:$objeto;

		session_start();

	// Valida si es prearado o no
		if ($objeto['preparado']==1) {
    // Si no existe el producto lo agrega al array,  si existe lo elimina
			if (!empty($_SESSION['insumos_agregados']['insumos_preparados'][$objeto['id']])) {
				unset($_SESSION['insumos_agregados']['insumos_preparados'][$objeto['id']]);
			}else{
				$_SESSION['insumos_agregados']['insumos_preparados'][$objeto['id']]=$objeto;
			}
		} else {
    // Si no existe el producto lo agrega al array,  si existe lo elimina
			if (!empty($_SESSION['insumos_agregados']['insumos'][$objeto['id']])) {
        unset($_SESSION['insumos_agregados']['insumos'][$objeto['id']]);
			}else{
        $_SESSION['insumos_agregados']['insumos'][$objeto['id']]=$objeto;
			}
    }

	// carga la vista para listar las reservaciones
		require('views/recetas/listar_insumos_agregados.php');
	}

 ///////////////// ******** ---- 	FIN agregar_insumo			------ ************ /////////////////

 ///////////////// ******** ---- 		agregar_proceso			------ ************ //////////////////
 //////// Agrega un insumo al array de los insumos agregados y carga la vista donde aparecen
 	// Como parametros recibe:
 		// idProducto -> ID del insumo
 		// div -> ID de la div donde se cargara la vista
 		// idunidad -> ID de la unidad
 		// idunidadCompra -> ID de la unidad de compra
 		// nombre -> nombre del insumo
 		// unidad -> nombre de la unidad

 	function agregar_proceso($objeto) {
 	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
 	// Si no conserva su valor normal
 		$objeto=(empty($objeto))?$_REQUEST:$objeto;

 		session_start();

 		// Si no existe el proceso lo agrega al array,  si existe lo elimina
 		if (!empty($_SESSION['procesos_produccion'][$objeto['id']])) {
    	unset($_SESSION['procesos_produccion'][$objeto['id']]);
 		} else {
			$_SESSION['procesos_produccion'][$objeto['id']]=$objeto;
 		}


 	// carga la vista para listar las reservaciones
 		require('views/recetas/listar_procesos.php');
 	}

  ///////////////// ******** ---- 	FIN agregar_proceso			------ ************ /////////////////

	///////////////// ******** ---- 		agregar_accion			------ ************ //////////////////
  //////// Agrega un insumo al array de los insumos agregados y carga la vista donde aparecen
  	// Como parametros recibe:
  		// idProducto -> ID del insumo
  		// div -> ID de la div donde se cargara la vista
  		// idunidad -> ID de la unidad
  		// idunidadCompra -> ID de la unidad de compra
  		// nombre -> nombre del insumo
  		// unidad -> nombre de la unidad

  	function agregar_accion() {
  		
  	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
  	// Si no conserva su valor normal
  		$objeto=$_POST['objeto'];
  		$alias=$_POST['alias'];
  		$idp=$_POST['idp'];
  		//$objeto=(empty($objeto))?$_REQUEST:$objeto;

  		session_start();


  		//if($objeto['id']==16){
	  		$etiquetas = $this->recetasModel->getEtiquetas();
	  		$agrupados = $this->recetasModel->getAgrupados($idp);
	  //	}else{
	  	//	$etiquetas=0;
	  	//}

	  		$pon=1;
	  		$pon2=1;



  		// Si no existe el proceso lo agrega al array,  si existe lo elimina
  		if (!empty($_SESSION['produccion_acciones'][$objeto['id']])) {
	     	//unset($_SESSION['produccion_acciones'][$objeto['id']]);
	     	foreach ($_SESSION['produccion_acciones'] as $k => $v) {
	     		$xpl=explode('_', $k);
	     		if($xpl[0]==$objeto['id']){
	     			unset($_SESSION['produccion_acciones'][$k]);
	     			
	     		}
	     	}
	     	unset($_SESSION['produccion_acciones'][$objeto['id']]);
	     	if($objeto['id']==16){
	  			$pon=0;
	  		}
	  		$ola=explode('_', $objeto['id']);
	  		if($ola[0]=='11' or $objeto['id']==11){
	  			$pon2=0;

	  		}
  		}else{
 			$_SESSION['produccion_acciones'][$objeto['id']]=$objeto;
  		}


  		


  	// carga la vista para listar las reservaciones
  		require('views/recetas/listar_acciones.php');
  	}

  	function agregar_accion_m() {
  	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
  	// Si no conserva su valor normal
  		//$objeto=(empty($objeto))?$_REQUEST:$objeto;

  		$objeto=$_POST['objeto'];
  		$alias=$_POST['alias'];
  		$idp=$_POST['idp'];

  		$pon=1;
  		$pon2=1;
  		$etiquetas = $this->recetasModel->getEtiquetas();
  		$agrupados = $this->recetasModel->getAgrupados($idp);



  		session_start();
  		$r=rand(111,999);
  		$t=$r.''.time();
  		$id = $_POST['id'];

  		$objeto=$_SESSION['produccion_acciones'][$id];
  		$objeto['id']=$objeto['id'].'_'.$t;


  		// Si no existe el proceso lo agrega al array,  si existe lo elimina
  		if (!isset($_SESSION['produccion_acciones'][$id])) {
     		unset($_SESSION['produccion_acciones'][$id]);
	     	if($objeto['id']==16){
	  			$pon=0;
	  		}
	  		$ola=explode('_', $objeto['id']);
	  		if($ola[0]=='11' or $objeto['id']==11){
	  			$pon2=0;

	  		}
  		} else {
 			$_SESSION['produccion_acciones'][$objeto['id']]=$objeto;
  		}

  		//var_dump($_SESSION['produccion_acciones']);


  	// carga la vista para listar las reservaciones
  		require('views/recetas/listar_acciones.php');
  	}


  	 function remover_paso() { 
  	 	
  	 // Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
  	 // Si no conserva su valor normal
  	    $arrayRegreso = json_decode( $_POST['pasos']);
  	 	session_start(); 
  	 	// Si no existe el proceso lo agrega al array,  si existe lo elimina
  	 	$_SESSION['pasos'] = $arrayRegreso; 
  	 // carga la vista para listar las reservaciones
  	 	require('views/recetas/listar_pasos2.php');
  	 }


  // 	function remover_paso_accion() {
  // 		$paso=trim($_POST['paso']);
  // 		$accion=trim($_POST['accion']);  

  // 		session_start();
  // 		foreach ($_SESSION['pasos'][$paso] as $key => $value) { 
  //   		if ($value["idAccion"] == $accion) { unset($_SESSION['pasos'][$paso][$key]); }
		// }

		// $tiene = count($_SESSION['pasos'][$paso]);
		// if($tiene==0){
		// 	echo 0; 
		// }else{
		// 	echo 1;
		// } 
  // 	}

   ///////////////// ******** ---- 	FIN agregar_accion			------ ************ /////////////////


  	function ver_pasos() {
  		
  		$paso=trim($_POST['paso']);
  		$num=trim($_POST['num']);
  		$idp=trim($_POST['idp']);
    
        // AM se agrego el accionesDelPaso desde el js
  		$acciones = json_decode($_POST['accionesDelPaso']); 

     
     // echo json_encode($_POST['accionesDelPaso']);
  	//  Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
   //   Si no conserva su valor normal

  		$etiquetas = $this->recetasModel->getEtiquetas();
  		$agrupados = $this->recetasModel->getAgrupados($idp);
  		
  		session_start();
 
  		// Si no existe el proceso lo agrega al array,  si existe lo elimina
  		if (empty($acciones)) {

  		} else {
  			
 			require('views/recetas/listar_pasos2.php');
  		}

  	     // carga la vista para listar las reservaciones
  		//require('views/recetas/listar_pasos2.php');
  	}

	 ///////////////// ******** ---- 		agregar_paso			------ ************ //////////////////
   //////// Agrega un insumo al array de los insumos agregados y carga la vista donde aparecen
   	// Como parametros recibe:
   		// div -> ID de la div donde se cargara la vista

  	function morden_paso(){
  		$pasoslist= $_POST['pasoslist'];
  		$ciclo=explode('#..#', $pasoslist);
  		session_start();
  		$nuevoOrden=array();
  		foreach ($ciclo as $k => $v) {
  			$nuevoOrden[$v]=$_SESSION['pasos'][$v];
  		}
  		$_SESSION['pasos']=$nuevoOrden;

  	}

  	function morden_insumos(){
  		$pasoslist=$_POST['pasoslist'];
  		$ciclo=explode('#..#', $pasoslist);
  		session_start();
  		$nuevoOrden=array();
  		foreach ($ciclo as $k => $v) {
  			$nuevoOrden[$v]=$_SESSION['produccion_acciones'][$v];
  		}
  		$_SESSION['produccion_acciones']=$nuevoOrden;

  	}


  	 function morden_accion(){
  	// 	$pasosAcciones=$_POST['pasosAcciones'];
  	// 	$paso=$_POST['paso_ori'];
  	// 	$ciclo=explode('#..#', $pasosAcciones);
  	// 	session_start();
  	// 	$nuevoOrden=array();
  	// 	foreach ($ciclo as $k => $v) {
			// $nuevoOrden[$v]=$_SESSION['pasos'][$paso][$v];
  	// 	}
  	// 	$_SESSION['pasos']=$nuevoOrden;
  		$pasosAcciones=  json_decode( $_POST['pasosAcciones']);
  		var_dump($pasosAcciones);
  		$_SESSION['pasos'] = $pasosAcciones;
  	}

  	function quitatodo(){
  		session_start();
  		unset($_SESSION['produccion_acciones']);
  		unset($_SESSION['insumos_producto']);
  		unset($_SESSION['pasos']);
  		unset($_SESSION['insumos_agregados']);
  		unset($_SESSION['procesos_produccion']);
  	}

   	function agregar_paso($objeto) {
   	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
   	// Si no conserva su valor normal
   		$objeto=(empty($objeto))?$_REQUEST:$objeto;
   		//var_dump($objeto);
   		$pasot=trim($_POST['paso']);
   		$alias=trim($_POST['alias']);
   		

   		$exp_paso=explode('_##_', $alias);

   		$paso=str_replace(' ', '_', $pasot);
   		$paso=str_replace('"', '', $paso);
   		session_start();
   		if($_SESSION['produccion_acciones']==''){
   			$JSON = array('success'=>0, 'error'=>'NOACCIONES' );
   			echo json_encode($JSON);
			exit();
   		} 

   		if (empty($_SESSION['pasos'])) {
		    $_SESSION['pasos']=array(); 
		}  
    
	    $x=0; 
	    foreach ($_SESSION['produccion_acciones'] as $k => $v) {
	     	$valors=explode('_#_', $exp_paso[$x]); 
	 			$alias=$valors[1];
	 			$alias_hr=$valors[2];
	 			$actividad=$valors[3];
	 			$tipo=$valors[4];
	 			$estatus=$valors[5];
	 			$eti=$valors[6];
	 			$agru=$valors[7]; 

			if($actividad==1){
    			$alias_hr=str_replace('h', '0', $alias_hr);
    			$alias_hr=str_replace('m', '0', $alias_hr);
    		}

			$element = new stdClass();
			$element->nombrePaso=  $paso;
			$element->idAccion  =  $v['id'];
			$element->nombreAccion =$v['nombre'] ;
			$element->tiempo    =  $v['tiempo_hrs'];
			$element->pasoR     =  $pasot;
			$element->alias     =  $alias=='' ? $v['nombre'] : $alias ;
			$element->alias_hr  =  $alias_hr;
			$element->actividad =  $actividad;
			$element->tipo 	    =  $tipo;
			$element->estatus   =  $estatus;
			$element->eti       =  $eti;
			$element->agru      =  $agru;

			array_push($_SESSION['pasos'], $element );
			$x++;
		}
		// }else{
		// 	if(array_key_exists($paso, $_SESSION['pasos'])) {
		// 	    $JSON = array('success'=>0, 'error'=>'PASOREP' );
  //  				echo json_encode($JSON);

  //  				exit();
		// 	}else{
		// 		$x=0;
		// 		foreach ($_SESSION['produccion_acciones'] as $k => $v) {
		// 			$valors=explode('_#_', $exp_paso[$x]);
	 //   				$iaccion=$valors[0];
	 //   				$alias=$valors[1];
	 //   				$alias_hr=$valors[2];
	 //   				$actividad=$valors[3];
	 //   				$tipo=$valors[4];
	 //   				$estatus=$valors[5];
	 //   				$eti=$valors[6];
	 //   				$agru=$valors[7];

	 //   				if($alias==''){
	 //   					$alias=$v['nombre'];
	 //   				}

	 //   				if($actividad==1){
		//    				$alias_hr=str_replace('h', '0', $alias_hr);
		//    				$alias_hr=str_replace('m', '0', $alias_hr);
		//    			}

		// 			$_SESSION['pasos'][$paso][$v['id']]=array('idAccion' => $v['id'], 'nombreAccion'=> $v['nombre'], 'tiempo'=>$v['tiempo_hrs'], 'pasoR'=>$pasot, 'alias'=>$alias, 'alias_hr'=>$alias_hr, 'actividad'=>$actividad, 'tipo'=>$tipo, 'estatus'=>$estatus, 'eti'=>$eti, 'agru'=>$agru);
  
		// 			$x++;
		// 		}
		// 	}
		//} 

		$_SESSION['produccion_acciones']='';
		$JSON = array('success'=>1, 'data'=>$_SESSION['pasos']);
		echo json_encode($JSON); 
		exit();
 
   	}

    ///////////////// ******** ---- 	FIN agregar_paso			------ ************ /////////////////


///////////////// ******** ---- 		agregar_parametro			------ ************ //////////////////

function agregar_parametro($objeto) {
// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
// Si no conserva su valor normal
	$objeto=(empty($objeto))?$_REQUEST:$objeto;

	session_start();

	if (!empty($_SESSION['parametros_lab'][$objeto['id']]))
			unset($_SESSION['parametros_lab'][$objeto['id']]);
	else
		$_SESSION['parametros_lab'][$objeto['id']]=$objeto;

// carga la vista para listar las reservaciones
	require('views/recetas/listar_parametros_agregados.php');
}

///////////////// ******** ---- 	FIN agregar_parametro			------ ************ ////////////////

///////////////// ******** ---- 		agregar_insumos_producto			------ ************ //////////////////

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
	require('views/recetas/listar_insumos_producto.php');
}

///////////////// ******** ---- 	FIN agregar_insumos_producto			------ ************ ////////////////


///////////////// ******** ---- 	cargar_formulario_lab			------ ************ ////////////////

function cargar_formulario_lab($objeto){
	$objeto=(empty($objeto))?$_REQUEST:$objeto;

	session_start();

	$_SESSION['formulario_lab_campo'] = "";

	$campos = $this -> recetasModel -> cargar_formulario_lab($objeto['producto']);

	foreach ($campos['rows'] as $key => $value) {
		$_SESSION['formulario_lab_campo'][$value['id']] = $value;
		if ($value['is_numeric'] == 1)
			$_SESSION['formulario_lab_campo'][$value['id']]['valor'] = 0;
		else {
			$_SESSION['formulario_lab_campo'][$value['id']]['valor'] = "";
		}
	}

	//echo "Formulario lab campo: " . json_encode($_SESSION['formulario_lab_campo']);

	require('views/recetas/listar_formulario.php');
}

///////////////// ******** ---- 	FIN cargar_formulario_lab			------ ************ ////////////////


 ////////////////// ******** ---- 		calcular_precio			------ ************ //////////////////
//////// Calcula el sub total del insumo, el total de la receta,
	// Como parametros recibe:
		// id -> ID del insumo
		// cantidad -> cantidad del insumo

	function calcular_precio($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

		session_start();
		$_SESSION['insumos_agregados']['total'] = 0;

	// Valida la cantidad
		if (empty($objeto['cantidad'])) {
			$objeto['cantidad'] = 0;
		}

		$tipo = ($objeto['preparado'] == 1) ? 'insumos_preparados' : 'insumos';

		$_SESSION['insumos_agregados'][$tipo][$objeto['id']]['cantidad'] = $objeto['cantidad'];

		$id_unidad = $_SESSION['insumos_agregados'][$tipo][$objeto['id']]['id_unidad'];
		$unidad_compra = $_SESSION['insumos_agregados'][$tipo][$objeto['id']]['unidad_compra'];

		if ($id_unidad == $unidad_compra) {
			// Calculamos el sub total del insumo
			$sub_total = $_SESSION['insumos_agregados'][$tipo][$objeto['id']]['costo'] * $objeto['cantidad'];
			$_SESSION['insumos_agregados'][$tipo][$objeto['id']]['sub_total'] = $sub_total;
		} else {
			// - -- - -- - - --	-	-		**		 NOTA		**			- - - - - -- - - -- - 	//

			//** Dividimos el valor de compra entre el de venta para sacar la conversion
			// Ejem.
			// Kilo -> 1'000,000   // El valor de un kilo son 1'000,000 miligramos
			// Gramo -> 1,000   // El valor de un kilo son 1,000 miligramos

			// Para calcular la diferencia en miligramos dividimos  el valor de compra entre el de venta

			// 1000000/1000=1000	(kilo/gramo es igual a 1000 miligramos)

			// - -- - -- - - --	-	-		**		FIN NOTA		**			- - - - - -- - - -- - 	//

		// Obtiene la conversion de la unidad de venta
			$objeto['unidad'] = $id_unidad;
			$conversion = $this -> recetasModel -> listar_conversion($objeto);
			$valor_venta = $conversion['rows'][0]['conversion'];

		// Obtiene la conversion de la unidad de compra
			$objeto['unidad'] = $unidad_compra;
			$conversion = $this -> recetasModel -> listar_conversion($objeto);
			$valor_compra = $conversion['rows'][0]['conversion'];

		// Calculamos el equivalente
			if ($unidad_compra == 21) {
				$sub_total = ($_SESSION['insumos_agregados'][$tipo][$objeto['id']]['costo'] / $valor_compra) * $objeto['cantidad'];
				$_SESSION['insumos_agregados'][$tipo][$objeto['id']]['sub_total'] = $sub_total;

			// Actualiza el total de la receta
				$_SESSION['insumos_agregados']['total'] += $sub_total;
			} else {
			// Calculamos el equivalente de la conversion
				$conversion = $valor_compra / $valor_venta;
				$sub_total = ($_SESSION['insumos_agregados'][$tipo][$objeto['id']]['costo'] / $conversion) * $objeto['cantidad'];
				$_SESSION['insumos_agregados'][$tipo][$objeto['id']]['sub_total'] = $sub_total;
			}
		}

		echo json_encode($_SESSION['insumos_agregados']);
	}

///////////////// ******** ---- 		FIN	calcular_precio				------ ************ //////////////////

///////////////// ******** ---- 		asignar_parametros				------ ************ //////////////////

function asignar_referencias($objeto) {
// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
// Si no conserva su valor normal
	$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
	session_start();

	//$_SESSION['insumos_agregados']['total'] = 0;
	switch ($objeto['tipo_parametro']) {
		case 'lim_inf':
			$_SESSION['parametros_lab'][$objeto['id']]['lim_inf'] = $objeto['cantidad'];
			break;
		case 'lim_sup':
			$_SESSION['parametros_lab'][$objeto['id']]['lim_sup'] = $objeto['cantidad'];
			break;
		case 'referencia':
			$_SESSION['parametros_lab'][$objeto['id']]['referencia'] = $objeto['cantidad'];
			break;
		default:
			# code...
			break;
	}
	echo json_encode($_SESSION['parametros_lab']);
}

///////////////// ******** ---- 		FIN	asignar_parametros				------ ************ //////////////////

///////////////// ******** ---- 		asignar_cant_req				------ ************ //////////////////

function asignar_cant_req($objeto) {
// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
// Si no conserva su valor normal
	$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
	session_start();

	$_SESSION['insumos_producto'][$objeto['id']]['cantidad'] = $objeto['cantidad'];

	echo json_encode($_SESSION['insumos_producto']);
}

///////////////// ******** ---- 		FIN	asignar_cant_req				------ ************ //////////////////

///////////////// ******** ---- 		asignar_valor_lab				------ ************ //////////////////

function asignar_valor_lab($objeto) {
// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
// Si no conserva su valor normal
	$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
	session_start();

	$_SESSION['formulario_lab_campo'][$objeto['id']]['valor'] = $objeto['valor'];

	echo json_encode($_SESSION['formulario_lab_campo']);
}

///////////////// ******** ---- 		FIN	asignar_valor_lab				------ ************ //////////////////


///////////////// ******** ---- 		guardar_opcionales				------ ************ //////////////////
//////// Guarda los opcionales del insumo
	// Como parametros recibe:
		// id -> ID del insumo
		// opcionales -> cadena con los IDS de los opcionales

	function guardar_opcionales($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto=(empty($objeto))?$_REQUEST:$objeto;

		session_start();

	// Se utiliza para cargar el select con los opcionales
		if ($objeto['preparado']==1) {
			$_SESSION['insumos_agregados']['insumos_preparados'][$objeto['id']]['select']=$objeto['opcionales'];
		}else{
			$_SESSION['insumos_agregados']['insumos'][$objeto['id']]['select']=$objeto['opcionales'];
		}

	// Junta los ids de los opcionales en una cadena
		foreach ($objeto['opcionales'] as $key => $value) {
			$opcionales.=$value.',';
		}

	// Agrega los opcionales al insumo
		$opcionales=substr($opcionales, 0,-1);
		if ($objeto['preparado']==1) {
			$_SESSION['insumos_agregados']['insumos_preparados'][$objeto['id']]['opcionales']=$opcionales;
		}else{
			$_SESSION['insumos_agregados']['insumos'][$objeto['id']]['opcionales']=$opcionales;
		}

		$resp['status'] = (!$opcionales) ? 1 : 0 ;
		$resp['insumos'] =$_SESSION['insumos_agregados'];

		echo json_encode($resp);
	}

///////////////// ******** ---- 	FIN guardar_opcionales		------ ************ //////////////////

///////////////// ******** ---- 		vista_copiar	------ ************ //////////////////
	//////// Consulta las recetas y los insumos preparados y los carga en la div
		// Como parametros recibe:
			// div -> div donde se cargara el contenido html
			// btn -> boton del loader

	function vista_copiar($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
 		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
		$panel = $objeto['panel'];

	// Consulta las recetas
		$objeto['orden'] = 'p.tipo_producto ASC';
		$objeto['tipo'] = 5;
		$recetas = $this -> recetasModel -> listar($objeto);
		$recetas = $recetas['rows'];

		foreach ($recetas as $key => $value) {
		// Optiene los insumos normales y los agrega al array
			if (!empty($value['insumos'])) {
				$objeto['ids'] = $value['insumos'];
				$objeto['id_receta'] = $value['idProducto'];
				$value['insumos'] = $this -> recetasModel -> listar_materiales($objeto);
				$value['insumos'] = $value['insumos']['rows'];
			}

		// Optiene los insumos preparados y los agrega al array
			if (!empty($value['insumos_preparados'])) {
				$objeto['ids'] = $value['insumos_preparados'];
				$objeto['id_receta'] = $value['idProducto'];
				$value['insumos_preparados'] = $this -> recetasModel -> listar_materiales($objeto);
				$value['insumos_preparados'] = $value['insumos_preparados']['rows'];
			}

		// Agrega el elemento al array
			$datos[$value['idProducto']] = $value;
		}

	// Inicializa el array de los insumos agregados
		session_start();
		$_SESSION['insumos_agregados'] = '';

		// Carga la vista de las recetas
		//require('views/recetas/copiar.php');

		switch($objeto['vista']){
			case "prc_prd":
				$require = "views/recetas/editar_procesos_produccion.php"; // Cambiar para copiar
				break;
			case "lab_cpts":
				$require = "views/recetas/editar_lab_conceptos.php"; // Cambiar para copiar
				break;
			case "lab_cs_prd":
				$require = "views/recetas/nueva_lab_conceptos_productos.php"; // Cambiar para copiar
				break;
			case "lab_rgtr":
				$require = "views/recetas/nueva_lab_registro.php"; // Cambiar para copiar
				break;
			case "frm_prd":
				$require = "views/recetas/copiar_form_producto.php";
				break;
		}

		require($require);
	}

///////////////// ******** ---- 	FIN	vista_copiar		------ ************ //////////////////

///////////////// ******** ---- 		vista_editar		------ ************ //////////////////
	//////// Consulta las recetas y los insumos preparados y los carga en la div
		// Como parametros recibe:
			// div -> div donde se cargara el contenido html
			// btn -> boton del loader

	function cargaEdicion(){
		
		$idProd = $_POST['idProd'];
		$lospasos = $this->recetasModel->listar_pasos($idProd);
		
		session_start();
		unset($_SESSION['pasos']);
		$_SESSION['pasos']=array();
	/*
   		if($_SESSION['produccion_acciones']==''){
   			$JSON = array('success'=>0, 'error'=>'NOACCIONES' );
   			echo json_encode($JSON);
			exit();
   		}*/

		if($lospasos['total']==0){
			$JSON = array('success'=>0, 'error'=>'NOHAYPASOS' );
   			echo json_encode($JSON);
			exit();
		}else{
			$rt=1;
			foreach ($lospasos['rows'] as $k => $v1) {
				

				$pasot=$v1['nombre_paso'];
				$paso=str_replace(' ', '_', $pasot);
   				$paso=str_replace('"', '', $paso);
   				$actividad=$v1['actividad'];

   				if($v1['alias']==''){
   					$v1['alias']=$v1['nombre_accion'];
   				}

   				if($actividad==1){
   					
	   			}
	   			if($actividad==2){
	   				$v1['tiempo']=$v1['pieza'];
	   			}

	   			if($v1['id_accion']==11){
	   			   $v1['id_accion']=$v1['id_accion'].'_'.$rt;
	   			}

	   			$element = new stdClass();
				$element->nombrePaso  	=  $v1['nombre_paso'];
				$element->idAccion     	=  $v1['id_accion'];
				$element->nombreAccion 	=  $v1['nombre_accion'];
				$element->tiempo       	=  $v1['tiempo_hrs'];
				$element->pasoR        	=  $pasot;
				$element->alias        	=  $v1['alias'];
				$element->alias_hr     	=  $v1['tiempo'];
				$element->actividad    	=  $actividad;
				$element->tipo 	       	=  $v1['tipo'];
				$element->estatus      	=  $v1['estatus'];
				$element->eti          	=  $v1['id_etiqueta'];
				$element->agru         	=  $v1['id_agrupacion'];
				$element->nombre        =  $v1['nombre'];
				array_push($_SESSION['pasos'], $element);

			
					$rt++;
			}
		}





		$_SESSION['produccion_acciones']='';
		$JSON = array('success'=>1, 'data'=>$_SESSION['pasos']);
		echo json_encode($JSON);
  
	}

	function vista_editar2(){
		$listar2 = $this->recetasModel->listar2();
		$require = "views/recetas/editProc.php";
		require($require);
	}

	function vista_editar($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
 		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
		$panel = $objeto['panel'];

	// Consulta las recetas
		$objeto['orden'] = 'p.tipo_producto ASC';
		$recetas = $this -> recetasModel -> listar($objeto);
		$recetas = $recetas['rows'];

		foreach ($recetas as $key => $value) {
		// Optiene los insumos normales y los agrega al array
			if (!empty($value['insumos'])) {
				$objeto['ids'] = $value['insumos'];
				$objeto['id_receta'] = $value['idProducto'];
				$value['insumos'] = $this -> recetasModel -> listar_materiales($objeto);
				$value['insumos'] = $value['insumos']['rows'];
			}

		// Optiene los insumos preparados y los agrega al array
			if (!empty($value['insumos_preparados'])) {
				$objeto['ids'] = $value['insumos_preparados'];
				$objeto['id_receta'] = $value['idProducto'];
				$value['insumos_preparados'] = $this -> recetasModel -> listar_materiales($objeto);
				$value['insumos_preparados'] = $value['insumos_preparados']['rows'];
			}

		// Agrega el elemento al array
			$datos[$value['idProducto']] = $value;
		}

		// Arreglo para editar Procesos por producto
		$productos_formulados = $this -> recetasModel -> listar_productos_proceso();
		$productos_formulados = $productos_formulados['rows'];

		foreach ($productos_formulados as $key => $value) {
			// Procesos de Producción por Producto
			$value['procesos'] = $this -> recetasModel ->listar_procesos_por_producto($value['id']);
			$value['procesos'] = $value['procesos']['rows'];
			$procesos_productos[$value['id']] = $value;
		}

		// Arreglo para editar Conceptos-lab por producto
		$productos_test_lab = $this -> recetasModel -> listar_productos_conceptos_lab();
		$productos_test_lab = $productos_test_lab['rows'];

		foreach ($productos_test_lab as $key => $value) {
			// Conceptos-lab por Producto
			$value['conceptos_lab'] = $this -> recetasModel ->listar_conceptos_lab_por_producto($value['id']);
			$value['conceptos_lab'] = $value['conceptos_lab']['rows'];
			$conceptos_lab_productos[$value['id']] = $value;
		}

		$datos['lab_conceptos'] = $this -> recetasModel -> listar_lab_conceptos("utilizado");
		$datos['lab_conceptos'] = $datos['lab_conceptos']['rows'];

		// Inicializa el array de los insumos agregados
		session_start();
		$_SESSION['insumos_producto'] = '';
		$_SESSION['insumos_agregados'] = '';



		// Carga la vista de las recetas

		switch($objeto['vista']){
			case "prc_prd":
				$require = "views/recetas/editar_procesos_produccion.php";
				break;
			case "lab_cpts":
				$require = "views/recetas/editar_lab_conceptos.php";
				break;
			case "lab_cs_prd":
				$require = "views/recetas/editar_lab_conceptos_productos.php";
				break;
			case "lab_rgtr":
				$require = "views/recetas/nueva_lab_registro.php"; // Cambiar para editar
				break;
			case "frm_prd":
				$require = "views/recetas/editar_form_producto.php";
				break;
		}

		require($require);


	}

///////////////// ******** ---- 	FIN	vista_editar		------ ************ //////////////////

///////////////// ******** ---- 		actualizar			------ ************ //////////////////
//////// Manda llamar a la funcion que actualiza la receta o insumo preparado en la BD
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	function actualizar($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();
		$objeto['ids'] = '';
		$objeto['ids_preparados'] = '';

	// Forma una cadena con los IDs de los insumos preparados
		foreach ($_SESSION['insumos_agregados']['insumos_preparados'] as $key => $value) {
			if ($value['cantidad'] > 0) {
				$objeto['ids_preparados'] .= $key . ',';
			}
		}

	// Forma una cadena con los IDs de los insumos normales
		foreach ($_SESSION['insumos_agregados']['insumos'] as $key => $value) {
			if ($value['cantidad'] > 0) {
				$objeto['ids'] .= $key . ',';
			}
		}

	// Valida que existan insumos o insumos preparados
		if (!empty($objeto['ids']) || !empty($objeto['ids_preparados'])) {
		// Formatea las cadenas de ID´s
			$objeto['ids'] = substr($objeto['ids'], 0, -1);
			$objeto['ids_preparados'] = substr($objeto['ids_preparados'], 0, -1);

		// Valida el margen de ganancia
			$objeto['margen_ganancia'] = (empty($objeto['margen_ganancia'])) ? 0 : $objeto['margen_ganancia'];

		// Valida si existe la receta o no
			$valida = $this -> recetasModel -> validar($objeto);

		// Si no existe la receta agrega una nueva, si existe la actualiza
			if ($valida['total']<1) {
			// llama a la funcion que inserta la receta en la BD y obtiene el ID de la insercion
				$resp['result'] = $this -> recetasModel -> guardar_receta($objeto);
			} else {
			// llama a la funcion que actualiza la receta en la BD
				$resp['result'] = $this -> recetasModel -> actualizar_receta($objeto);
			}

		// llama a la funcion que actualiza el producto en la tabla de productos
			$resp['actualiza_producto'] = $this -> recetasModel -> actualizar_producto($objeto);

		//  Elimina los insumos de las receta de la BD
			$resp['eliminar_insumos'] = $this -> recetasModel -> eliminar_insumos($objeto);

		// Guarda los insumos preparados en la BD
			foreach ($_SESSION['insumos_agregados']['insumos_preparados'] as $key => $value) {
				$value['id_receta'] = $objeto['id_receta'];
				$value['tipo']=$objeto['tipo'];
				$resp['insumos_preparados'][$key] = $this -> recetasModel -> guardar_insumo($value);
			}

		// Guarda los insumos en la BD
			foreach ($_SESSION['insumos_agregados']['insumos'] as $key => $value) {
			$value['id_receta'] = $objeto['id_receta'];
				$value['tipo']=$objeto['tipo'];
				$resp['insumos'][$key] = $this -> recetasModel -> guardar_insumo($value);
			}

		// 1 -> Todo bien :)
		// 2 -> Fallo la consulta :(
			$resp['status'] = (!empty($resp['result'])) ? 1 : 0;
		} else {
		// Sin insumos
			$resp['status'] = 2;
		}

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	actualizar				------ ************ //////////////////

///////////////// ******** ---- 		actualizar_form_producto			------ ************ //////////////////
//////// Manda llamar a la funcion que actualiza la receta o insumo preparado en la BD
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	function actualizar_form_producto($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();
		$objeto['ids'] = '';
		$objeto['ids_preparados'] = '';


	// Forma una cadena con los IDs de los insumos normales
		/*foreach ($_SESSION['insumos_agregados']['insumos'] as $key => $value) {
			if ($value['cantidad'] > 0) {
				$objeto['ids'] .= $key . ',';
			}
		}*/

		foreach ($_SESSION['insumos_producto'] as $key => $value) {
			if ($value['cantidad'] > 0){
				$objeto['ids'] .= $key .',';
			}
		}

	// Valida que existan insumos o insumos preparados
		if (!empty($objeto['ids'])) {
		// Formatea las cadenas de ID´s
			$objeto['ids'] = substr($objeto['ids'], 0, -1);
			$objeto['ids_preparados'] = substr($objeto['ids_preparados'], 0, -1);

		// Valida el margen de ganancia
			$objeto['margen_ganancia'] = (empty($objeto['margen_ganancia'])) ? 0 : $objeto['margen_ganancia'];

		// Valida si existe la receta o no
			$valida = $this -> recetasModel -> validar($objeto);

		// Si no existe la receta agrega una nueva, si existe la actualiza
			if ($valida['total']<1) {
			// llama a la funcion que inserta la receta en la BD y obtiene el ID de la insercion
				$resp['result'] = $this -> recetasModel -> guarda_receta_sin_insumos($objeto);
			} else {
			// llama a la funcion que actualiza la receta en la BD
				$resp['result'] = $this -> recetasModel -> actualizar_receta($objeto);
			}

		// llama a la funcion que actualiza el producto en la tabla de productos
			$resp['actualiza_producto'] = $this -> recetasModel -> actualizar_producto($objeto);

		//  Elimina los insumos de las receta de la BD. Se crea otro objeto para no altera la firma del método
			$objeto_eliminar_insumos['id'] = $objeto['id_receta'];
			$resp['eliminar_insumos'] = $this -> recetasModel -> eliminar_insumos($objeto_eliminar_insumos);

			// app_producto_material 3/3
			foreach ($_SESSION['insumos_producto'] as $key => $value) {
					//"Hola: " . json_encode($_SESSION['insumos_producto']);
					$resp['id_app_prd_mat'] = $this -> recetasModel -> guardar_app_producto_material($objeto['id_receta'], $value['cantidad'], $value['idunidad'], $value['id'], 1);
			}

		// 1 -> Todo bien :)
		// 2 -> Fallo la consulta :(
			$resp['status'] = (!empty($resp['result'])) ? 1 : 0;
		} else {
		// Sin insumos
			$resp['status'] = 2;
		}

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	actualizar_form_producto				------ ************ //////////////////

///////////////// ******** ---- 		actualizar_conceptos_lab_productos			------ ************ //////////////////
//////// Manda llamar a la funcion que actualiza los conceptos de Laboratorio por Producto
	// Como parametros recibe:
		// productId -> ID del producto para el que se van a actualizar los Conceptos Laboratorio

	function actualizar_conceptos_lab_productos($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();

	// Valida que existan insumos o insumos preparados
		if (!empty($_SESSION['parametros_lab'])) {

		//  Elimina los Conceptos Lab asociados con el producto
			$resp['eliminar_conceptos_lab'] = $this -> recetasModel -> eliminar_lab_conceptos_producto($objeto['prd']);

			// Agrega los nuevos Conceptos Lab asociados con el producto
			// Se modificar un parámetro del objeto para no alterar la firma del método
			foreach ($_SESSION['parametros_lab'] as $key => $value) {
					$resp['id_lab_concepto_prd'] = $this -> recetasModel -> guardar_lab_conceptos_productos($value, $objeto['prd']);
			}

		// 1 -> Todo bien :)
		// 2 -> Fallo la consulta :(
			$resp['status'] = (!empty($resp['id_lab_concepto_prd'])) ? 1 : 0;
		} else {
		// Sin Conceptos lab - Producto
			$resp['status'] = 2;
		}

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	actualizar_conceptos_lab_productos				------ ************ //////////////////


///////////////// ******** ---- 		actualizar_conceptos_lab			------ ************ //////////////////
//////// Manda llamar a la funcion que actualiza la receta o insumo preparado en la BD
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	function actualizar_conceptos_lab($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();

		$resp['result'] = $this -> recetasModel -> actualizar_conceptos_lab($objeto);
		$resp['status'] = (!empty($resp['result'])) ? 1 : 0;

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	actualizar_conceptos_lab				------ ************ //////////////////


///////////////// ******** ---- 		actualizar_procesos_produccion_producto			------ ************ //////////////////
//////// Manda llamar a la funcion que actualiza la receta o insumo preparado en la BD
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	function actualizar_procesos_produccion_producto($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Guarda los ids de los insumos
		session_start();
		//$objeto['ids'] = '';


	// Forma una cadena con los IDs de los insumos normales
		/*foreach ($_SESSION['procesos_produccion'] as $key => $value) {
				$objeto['ids'] .= $key . ',';
		}*/

	// Valida que existan insumos o insumos preparados
		if (!empty($_SESSION['procesos_produccion'])) {

		//  Elimina los procesos de producción del producto seleccionado
			$resp['eliminar_procesos'] = $this -> recetasModel -> eliminar_procesos_produccion($objeto['prd']);
			// Agrega los procesos de producción del producto seleccionado
			foreach ($_SESSION['procesos_produccion'] as $key => $value) {
					$objeto_param['id_producto'] = $objeto['prd'];
					$objeto_param['id_proceso'] = $value['id'];
					$resp['id_prc_prd'] = $this -> recetasModel -> guardar_producto_proceso($objeto_param);
			}

		// 1 -> Todo bien :)
		// 2 -> Fallo la consulta :(
			$resp['status'] = (!empty($resp['id_prc_prd'])) ? 1 : 0;
		} else {
		// Sin insumos
			$resp['status'] = 2;
		}

		echo json_encode($resp);
	}
///////////////// ******** ---- 		FIN	actualizar_procesos_produccion_producto				------ ************ //////////////////

///////////////// ******** ---- 		vista_eliminar		------ ************ //////////////////
	//////// Consulta las recetas y los insumos preparados y los carga en la div
		// Como parametros recibe:
			// div -> div donde se cargara el contenido html
			// btn -> boton del loader

	function vista_eliminar($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto=(empty($objeto))?$_REQUEST:$objeto;
		$panel=$objeto['panel'];

	// Consulta las recetas
		$objeto['orden']='	p.tipo_producto ASC';
		$recetas=$this->recetasModel->listar($objeto);
		$recetas=$recetas['rows'];

		foreach ($recetas as $key => $value) {
		// Optiene los insumos normales y ls agrega al array
			if (!empty($value['insumos'])) {
				$objeto['ids']=$value['insumos'];
				$objeto['id_receta']=$value['idProducto'];
				$value['insumos']=$this->recetasModel->listar_materiales($objeto);
				$value['insumos']=$value['insumos']['rows'];
			}

		// Optiene los insumos preparados y los agrega al array
			if (!empty($value['insumos_preparados'])) {
				$objeto['ids']=$value['insumos_preparados'];
				$value['insumos_preparados']=$this->recetasModel->listar_materiales($objeto);
				$value['insumos_preparados']=$value['insumos_preparados']['rows'];
			}

		// Agrega el elemento al array
			$datos[$value['idProducto']]=$value;
		}

	// Inicializa el array de los insumos agregados
		session_start();
		$_SESSION['insumos_agregados']='';

	// Carga la vista de las recetas
		require('views/recetas/eliminar.php');
	}

///////////////// ******** ---- 		FIN	vista_eliminar			------ ************ //////////////////

///////////////// ******** ---- 				eliminar			------ ************ //////////////////
//////// Elimina una receta o insumo preparado, el producto y sus materiales
	// Como parametros recibe:
		// id -> ID de la receta o insumo preparado

	function eliminar($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto=(empty($objeto))?$_REQUEST:$objeto;

	// Elimina la receta, el producto y sus materiales
	    $eliminarP=$this->recetasModel->eliminar($objeto);
		//$resp['result']=$this->recetasModel->eliminar($objeto);
		//echo "string".$resp['result'];

	// 1 -> Todo bien :)
	// 0 -> Fallo la consulta :(
		//$resp['status'] = (!empty($resp['result'])) ? 1 : 0;


		//echo json_encode($resp);
	}

///////////////// ******** ---- 			FIN	eliminar			------ ************ //////////////////

///////////////// ******** ---- 		restaurar_precio			------ ************ //////////////////
//////// Busca el precio actual del producto y lo agrega al campo precio_venta
	// Como parametros recibe:
		// id -> ID de la receta o insumo preparado
		// btn -> boton del loader

	function restaurar_precio($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto=(empty($objeto))?$_REQUEST:$objeto;

	// Elimina la receta, el producto y sus materiales
		$resp['result'] = $this->recetasModel->restaurar_precio($objeto);
		$resp['result'] = $resp['result']['rows'][0]['precio'];

	// 1 -> Todo bien :)
	// 0 -> Fallo la consulta :(
		$resp['status'] = (!empty($resp['result'])) ? 1 : 0;

		echo json_encode($resp);
	}

///////////////// ******** ---- 			FIN	restaurar_precio		------ ************ //////////////////

///////////////// ******** ---- 			vista_preparacion			------ ************ //////////////////
//////// Carga la vista de los insumos preparados para producirlos
	// Como parametro puede recibir:

	function vista_preparacion($objeto) {
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

	// Consulta los insumos preparados
		$objeto['tipo'] = 4;
		$insumos_preparados = $this -> recetasModel -> listar($objeto);
		$insumos_preparados = $insumos_preparados['rows'];

		foreach ($insumos_preparados as $key => $value) {
		// Optiene los insumos y los agrega al array
			if (!empty($value['insumos'])) {
				$objeto['ids'] = $value['insumos'];
				$objeto['id_receta'] = $value['idProducto'];
				$value['insumos'] = $this -> recetasModel -> listar_materiales($objeto);
				$value['insumos'] = $value['insumos']['rows'];
			}

			$insumos_preparados[$key] = $value;
		}

	// Carga la vista de los insumos preparados
		require ('views/recetas/vista_preparacion.php');
	}

///////////////// ******** ---- 			FIN vista_preparacion		------ ************ //////////////////

///////////////// ******** ---- 			preparar_insumo				------ ************ //////////////////
//////// Descuenta del inventario los insumos y prepara un insumo preparado
	// Como parametros recibe:
		// id -> ID del preparado
		// cantidad -> Cantidad que se debe preparar del insumo

	function preparar_insumo($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	// Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
		date_default_timezone_set('America/Mexico_City');
		$objeto['f_ini'] = date('Y-m-d H:i:s');

	// Guarda la preparacion
		$resp['id_preparacion'] = $this->recetasModel->preparar_insumo($objeto);

	// Descuenta los insumos del inventario
		foreach ($objeto['insumos'] as $key => $value) {
			$value['id_preparacion'] = $resp['id_preparacion'];
			$value['id_producto'] = $objeto['id_producto'];
			$value['importe'] = $value['costo'] * $objeto['cantidad'];
			$value['cantidad'] = $value['cantidad'] * $objeto['cantidad'];
			$value['fecha'] = $objeto['f_ini'];

			$resp['insumos'] = $this->recetasModel->descontar_insumo($value);
		}

	// 1 -> Todo bien :)
	// 0 -> Fallo la consulta :(
		$resp['status'] = (!empty($resp['insumos'])) ? 1 : 0;

		echo json_encode($resp);
	}

///////////////// ******** ---- 			FIN preparar_insumo			------ ************ //////////////////

///////////////// ******** ---- 				terminar_insumo			------ ************ //////////////////
//////// Actualiza el inventario y el insumo preparado
	// Como parametros recibe:
		// id -> ID del insumo preparado
		// id_preparacion -> ID de la preparacion
		// cantidad -> Cantidad que se debe preparar del insumo

	function terminar_insumo($objeto){
	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
	 // Si no conserva su valor normal
		$objeto = (empty($objeto)) ? $_REQUEST : $objeto;
		date_default_timezone_set('America/Mexico_City');
		$objeto['f_fin'] = date('Y-m-d H:i:s');


		$id_unidad = $objeto['unidad_venta'];
		$unidad_compra = $objeto['unidad_compra'];

		if ($id_unidad == $unidad_compra) {
		// Calculamos el importe
			$objeto['importe'] = $objeto['precio'] * $objeto['cantidad'];
		} else {
			// - -- - -- - - --	-	-		**		 NOTA		**			- - - - - -- - - -- - 	//

			//** Dividimos el valor de compra entre el de venta para sacar la conversion
			// Ejem.
			// Kilo -> 1'000,000   // El valor de un kilo son 1'000,000 miligramos
			// Gramo -> 1,000   // El valor de un kilo son 1,000 miligramos

			// Para calcular la diferencia en miligramos dividimos  el valor de compra entre el de venta

			// 1000000/1000=1000	(kilo/gramo es igual a 1000 miligramos)

			// - -- - -- - - --	-	-		**		FIN NOTA		**			- - - - - -- - - -- - 	//

		// Obtiene la conversion de la unidad de venta
			$objeto['unidad'] = $id_unidad;
			$conversion = $this -> recetasModel -> listar_conversion($objeto);
			$valor_venta = $conversion['rows'][0]['conversion'];

		// Obtiene la conversion de la unidad de compra
			$objeto['unidad'] = $unidad_compra;
			$conversion = $this -> recetasModel -> listar_conversion($objeto);
			$valor_compra = $conversion['rows'][0]['conversion'];

		// Calculamos el equivalente
			if ($unidad_compra == 21) {
				$objeto['precio'] = $objeto['precio'] / $valor_compra;
				$objeto['importe'] = $objeto['precio'] * $objeto['cantidad'];
				$objeto['cantidad'] = $objeto['cantidad'] * $valor_compra;
			} else {
			// Calculamos el equivalente de la conversion
				$conversion = $valor_compra / $valor_venta;
				$objeto['precio'] = $objeto['precio'] / $conversion;
				$objeto['importe'] = $objeto['precio'] * $objeto['cantidad'];
				$objeto['cantidad'] = $objeto['cantidad'] * $conversion;
			}
		}

	// Actualiza el inventario y el insumo preparado
		$resp['result'] = $this->recetasModel->terminar_insumo($objeto);

	// 1 -> Todo bien :)
	// 0 -> Fallo la consulta :(
		$resp['status'] = (!empty($resp['result'])) ? 1 : 0;


		echo json_encode($resp);
	}

///////////////// ******** ---- 			FIN	terminar_insumo			------ ************ //////////////////
 


///funcion para guardar la copia AM
	function guardarcopia(){

		$guardarcopia = $this ->recetasModel-> guardarcopia($_REQUEST['inicial'],$_REQUEST['final']);
	}

	//producir producto de mantis --> 0001253: Administración de ciclo de producción (Ligero) AM
	 function producirproducto(){
		
		$ciclo          = $this ->recetasModel-> ciclo();
		$cicloseleccion = $this ->recetasModel-> ciclo();
		$departamento   = $this ->recetasModel-> departamento();
	 	$familia        = $this ->recetasModel-> familia();
	 	$linea          = $this ->recetasModel-> linea();

	 	require ('views/producirproducto.php');

	 }

	 function verproducirproducto(){

   	 	echo $cargarproducirproductos  = $this->recetasModel->verproducirproducto($_REQUEST['ciclo'],$_REQUEST['departamento'],$_REQUEST['familia'],$_REQUEST['linea']);
	 }
	function editarproductounico(){
		
		echo $editarproductounico  = $this->recetasModel->editarproductounico($_REQUEST['id']);
	}

	function asignaciongeneral(){

	 $ciclo    = 	$_REQUEST['ciclo'];
	 $masivo   = 	array($_REQUEST['arr']);
	 $boton    = 	$_REQUEST['boton'];
	 $id       = 	$_REQUEST['id'];

	
		echo $asignaciongeneral  = $this->recetasModel->asignaciongeneral($ciclo,$masivo,$boton,$id);
	 
	}
	//FIN DE producir producto de mantis --> 0001253: Administración de ciclo de producción (Ligero) AM
	//
	

	function admincatalogociclos(){
		// Consulta las Acciones de Procesos de Producción
		  	$datos['acciones_procesos_produccion'] = $this ->recetasModel-> listar_acciones_procesos($objeto);
		  	$datos['acciones_procesos_produccion'] = $datos['acciones_procesos_produccion']['rows'];

		  	$ciclostipos = $this ->recetasModel->ciclo();

			
		  // unset($_SESSION['produccion_acciones']);
  		//   unset($_SESSION['pasos']);

  		//   session_start();
  		// unset($_SESSION['produccion_acciones']);
  		// unset($_SESSION['insumos_producto']);
  		// unset($_SESSION['pasos']);
  		// unset($_SESSION['insumos_agregados']);
  		// unset($_SESSION['procesos_produccion']);

  		   	//unset($_SESSION['produccion_acciones']);
  		// unset($_SESSION['insumos_producto']);
  		// unset($_SESSION['pasos']);
  		// unset($_SESSION['insumos_agregados']);
  		// unset($_SESSION['procesos_produccion']);
			
		require ('views/admincatalogociclos.php');
	}


function agregar_accioncatalogo() {
  		
  	// Si el objeto viene vacio(llamado desde el index) se le asigna el $_REQUEST que manda el Index
  	// Si no conserva su valor normal
  		$objeto=$_POST['objeto'];
  		$alias=$_POST['alias'];
  		$idp=$_POST['idp'];
  		//$objeto=(empty($objeto))?$_REQUEST:$objeto;

  		session_start();

	  		// $pon=1;
	  		// $pon2=1;



  		// Si no existe el proceso lo agrega al array,  si existe lo elimina
  		if (!empty($_SESSION['produccion_acciones'][$objeto['id']])) {
	     	//unset($_SESSION['produccion_acciones'][$objeto['id']]);
	     	foreach ($_SESSION['produccion_acciones'] as $k => $v) {
	     		$xpl=explode('_', $k);
	     		if($xpl[0]==$objeto['id']){
	     			unset($_SESSION['produccion_acciones'][$k]);
	     			
	     		}
	     	}
	     	unset($_SESSION['produccion_acciones'][$objeto['id']]);
	    //  	if($objeto['id']==16){
	  		// 	$pon=0;
	  		// }
	  		// $ola=explode('_', $objeto['id']);
	  		// if($ola[0]=='11' or $objeto['id']==11){
	  		// 	$pon2=0;

	  		// }
  		}else{
 			$_SESSION['produccion_acciones'][$objeto['id']]=$objeto;
  		}
  	// carga la vista para listar las reservaciones
  		
  	    // carga la vista para listar las reservaciones
  		require('views/recetas/agreecatalogociclos.php');
  	}


  	function guardar_catalogo_ciclos() {
  		
		 session_start();
			
		if (empty($_SESSION['pasos'])) {

		} else {

		$this->recetasModel->guardar_catalogo_ciclos($_SESSION['pasos'],$_POST['cicloSeleccionado'],$_POST['accionesDelPaso'],$_POST['data'],$_POST['edicion'],$_POST['tipociclo']);
		}
	}

	function editarCatalogociclos(){

		$editarCatalogociclos = $this->recetasModel->editarCatalogociclos();

	}



	function cargaEdicioncatalogo(){
		
		$ciclo = $_POST['ciclo'];
		$lospasos = $this->recetasModel->listar_pasoscatalogo($ciclo);
		
		session_start();
		unset($_SESSION['pasos']);
		$_SESSION['pasos']=array();
	

		if($lospasos['total']==0){
			$JSON = array('success'=>0, 'error'=>'NOHAYPASOS' );
   			echo json_encode($JSON);
			exit();
		}else{
			$rt=1;
			foreach ($lospasos['rows'] as $k => $v1) {
				

				$pasot=$v1['nombre_paso'];
				$paso=str_replace(' ', '_', $pasot);
   				$paso=str_replace('"', '', $paso);

   				if($v1['alias']==''){
   					$v1['alias']=$v1['nombre_accion'];
   				}
   		
	   			
	   			$element = new stdClass();
	   			$element->descripcionciclo  =  $v1['descripcion'];
				$element->nombrePaso  	    =  $v1['nombre_paso'];
				$element->idAccion       	=  $v1['id_accion'];
				$element->nombreAccion  	=  $v1['nombre_accion'];
				$element->pasoR         	=  $pasot;
				$element->alias        	    =  $v1['alias'];
				$element->id_tipociclo    	=  $v1['id_tipociclo'];
				array_push($_SESSION['pasos'], $element);

					$rt++;
			}
		}





		$_SESSION['produccion_acciones']='';
		$JSON = array('success'=>1, 'data'=>$_SESSION['pasos']);
		echo json_encode($JSON);
		exit();
  
	}

	function agregar_pasociclo($objeto) {
  
   		$objeto=(empty($objeto))?$_REQUEST:$objeto;
  
   		$pasot=trim($_POST['paso']);
   		$alias=trim($_POST['alias']);
   		

   		$exp_paso=explode('_##_', $alias);

   		$paso=str_replace(' ', '_', $pasot);
   		$paso=str_replace('"', '', $paso);
   		session_start();
   		if($_SESSION['produccion_acciones']==''){
   			$JSON = array('success'=>0, 'error'=>'NOACCIONES' );
   			echo json_encode($JSON);
			exit();
   		} 

   		if (empty($_SESSION['pasos'])) {
		    $_SESSION['pasos']=array(); 
		}  
    
	    $x=0; 
	    foreach ($_SESSION['produccion_acciones'] as $k => $v) {
	     	$valors=explode('_#_', $exp_paso[$x]); 
	 			$alias=$valors[1];
	 		

			$element = new stdClass();
			$element->nombrePaso   =  $paso;
			$element->idAccion     =  $v['id'];
			$element->nombreAccion =  $v['nombre'] ;
			$element->tiempo       =  $v['tiempo_hrs'];
			$element->pasoR        =  $pasot;
			$element->alias        =  $alias=='' ? $v['nombre'] : $alias ;
	

			array_push($_SESSION['pasos'], $element );
			$x++;
		}
	

		$_SESSION['produccion_acciones']='';
		$JSON = array('success'=>1, 'data'=>$_SESSION['pasos']);
		echo json_encode($JSON); 
		exit();
 
   	}

   	function nuevo(){

   		session_start();
		$_SESSION['produccion_acciones'] = '';
		unset($_SESSION['pasos']);

   	}

   	function eliminarciclo($cicloEliminar){
	
	    $eliminarciclo=$this->recetasModel->cicloEliminar($_POST['cicloEliminar']);
		
	}

   	
} 
?>
