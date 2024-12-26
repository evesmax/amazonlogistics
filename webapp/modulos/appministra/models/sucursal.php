<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class SucursalModel extends Connection
{
	function obtenerEstados(){
		$myQuery = "SELECT idestado, estado FROM estados WHERE idestado BETWEEN 1 AND 32";
		$Result = $this->query($myQuery);
		return $Result;
	}    

	function obtenerMunicipios($id){
		$myQuery = "SELECT idmunicipio, municipio FROM municipios WHERE idestado = $id ORDER BY municipio";
		$Result = $this->query($myQuery);
		return $Result;
	}

	function obtenerAlmacenes(){
		$myQuery = "SELECT  id, nombre FROM app_almacenes";
		$Result = $this->query($myQuery);
		return $Result;
	}

	function obtenerOrganizaciones(){
		$myQuery = "SELECT idorganizacion as id, nombreorganizacion as nombre FROM organizaciones";
		$Result = $this->query($myQuery);
		return $Result;
	}

	function validarFormulario($form){
		$clave 		= $form['clave'];
		$nombre 	= $form['nombre'];
		$estado 	= $form['estado'];
		$direccion 	= $form['direccion'];
		$municipio 	= $form['municipio'];

		$myQuery = "".
		"SELECT idSuc FROM mrp_sucursal 
		WHERE nombre 	= '$nombre'
		AND direccion 	= '$direccion'
		AND idEstado 	= $estado
		AND idMunicipio = $municipio
		AND clave 		= '$clave'";

		$Result = $this->query($myQuery);

		//Validamos que no se encuentre una sucursal con las mismas caracteristicas
		if ($Result->num_rows != 0) {
			//Encontro registros (no prosigue)
			return 1;
		} else {
			//No encontro registros (prosigue)
			return 0;
		}
	}

	function agregarSucursal($form){
		//Igualamos variables
		$activo 		= $form['activo'];
		$estado 		= $form['estado'];
		$almacen 		= $form['almacen'];
		$telefono 		= $form['telefono'];
		$municipio 		= $form['municipio'];
		$clave 			= trim($form['clave']);
		$nombre 		= trim($form['nombre']);
		$codigoPostal 	= $form['codigoPostal'];
		$organizacion 	= $form['organizacion'];
		$contacto 		= trim($form['contacto']);
		$direccion 		= trim($form['direccion']);

    if ($codigoPostal == '') {
    	$codigoPostal = 0;
    }

		$myQuery = "INSERT INTO mrp_sucursal (nombre, direccion, idEstado, idMunicipio, cp, tel_contacto, contacto, idOrganizacion, clave, activo, idAlmacen) 
			VALUES('$nombre', '$direccion', $estado, $municipio, '$codigoPostal', '$telefono', '$contacto', 
			$organizacion, '$clave', $activo, $almacen);";

		$Result = $this->insert_id($myQuery) or die("Hubo un error.");
		
		if($Result > 0 && $Result != '"Hubo un error."'){
			//ch@ Para ingresar la configuracion de foodware de la nueva Sucursal
			$sql = "INSERT INTO `com_configuracion` (`propina`, `consumo`, `reservaciones`, `seguridad`, `tipo_operacion`, 
						`pedir_pass`, `mostrar_dolares`, `mostrar_info_comanda`, `calculo_automatico`, `mostrar_sd`, `switch_propina`, 
						`facturar_propina`, `idioma`, `mostrar_nombre`, `mostrar_domicilio`, `mostrar_tel`, `switch_info_ticket`, 
						`mostrar_info_empresa`, `imprimir_pedido_general`, `mostrar_iva`, `mostrar_info_correo`, `mostrar_logo_correo`, 
						`imagen_promo`, `imagen_felicitaciones`, `informacion_adicional`, `enviar_promociones`, `enviar_menu`, `enviar_felicitaciones`, 
						`menu_digital`, `mostrar_logo_qr`, `mostrar_info_qr`, `tipo_vista_qr`, `mostrar_opciones_menu`, `imagen_fondo`, 
						`una_linea`, `id_consumo_clave`, `id_sucursal`) 
					SELECT c.propina, c.consumo, c.reservaciones, c.seguridad, c.tipo_operacion, c.pedir_pass, c.mostrar_dolares, c.mostrar_info_comanda, c.calculo_automatico,
						c.mostrar_sd, c.switch_propina, c.facturar_propina, c.idioma, c.mostrar_nombre, c.mostrar_domicilio, c.mostrar_tel, c.switch_info_ticket, c.mostrar_info_empresa,
						c.imprimir_pedido_general, c.mostrar_iva, c.mostrar_info_correo, c.mostrar_logo_correo, c.imagen_promo, c.imagen_felicitaciones,
						c.informacion_adicional, c.enviar_promociones, c.enviar_menu, c.enviar_felicitaciones, c.menu_digital, c.mostrar_logo_qr,
						c.mostrar_info_qr, c.tipo_vista_qr, c.mostrar_opciones_menu, c.imagen_fondo, c.una_linea, c.id_consumo_clave, " .$Result. " 
					FROM com_configuracion c limit 1 ";
			$res = $this->query($sql);	
		}	
		
		return $Result;
	}
 
	function obtenerSucursales(){
		$myQuery = "SELECT 
		suc.idSuc AS id_suc,
		suc.nombre AS nombre_suc, 
		suc.direccion AS direccion_suc,
		suc.contacto AS contacto_suc, 
		suc.tel_contacto AS telefono_suc, 
		alm.nombre AS nombre_alm,
		suc.activo AS activo_suc
		
		FROM `mrp_sucursal` AS suc

		LEFT JOIN `app_almacenes` AS alm
		ON alm.id = suc.idAlmacen

		LEFT JOIN `estados` AS est
		ON suc.idEstado = est.idestado

		LEFT JOIN `municipios` AS mun
		ON suc.idMunicipio = mun.idmunicipio

		ORDER BY id_suc;";

		$Result = $this->query($myQuery) or die("Hubo un error");
		return $Result;
	}

	function obtenerSucursal($id){
		$myQuery = "SELECT nombre, direccion, idEstado, idMunicipio, cp AS codigoPostal, tel_contacto AS telefono, contacto, idOrganizacion, clave, activo, idalmacen AS almacen FROM mrp_sucursal WHERE idSuc = $id;";
		$Result = $this->query($myQuery);
		return $Result;
	}

	function modificarSucursal($form){
		//Igualamos variables
		$id				= $form['id'];
		$activo 		= $form['activo'];
		$estado 		= $form['estado'];
		$almacen 		= $form['almacen'];
		$telefono 		= $form['telefono'];
		$municipio 		= $form['municipio'];
		$clave 			= trim($form['clave']);
		$nombre 		= trim($form['nombre']);
		$codigoPostal 	= $form['codigoPostal'];
		$organizacion 	= $form['organizacion'];
		$contacto 		= trim($form['contacto']);
		$direccion 		= trim($form['direccion']);

    	$myQuery = "UPDATE mrp_sucursal
		SET 
		nombre = '$nombre', 
		direccion = '$direccion',
		idEstado = $estado,
		idMunicipio = $municipio,
		cp = '$codigoPostal',
		tel_contacto = '$telefono',
		contacto = '$contacto',
		idOrganizacion = $organizacion,
		clave = '$clave',
		activo = $activo,
		idAlmacen = $almacen
		WHERE idSuc = $id;";

		$Result = $this->query($myQuery) or die("Hubo un error.");
		return $Result;
	}

}
?>
