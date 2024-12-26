<?php
if(array_key_exists("api", $_REQUEST)){		
	require ("../webapp/modulos/herramientas/models/connection_sqli.php");
} else {
	require ("models/connection_sqli.php");
}
// funciones mySQLi

class herramientasModel extends Connection {
///////////////// ******** ---- 				listar_instancias						------ ************ //////////////////
//////// Consulta las instancias y las devuelve en un array
	// Como parametros recibe:

	function listar_instancias($objeto) {
		$sql = "SELECT
					id, instancia, nombre_db AS db
				FROM
					customer
				WHERE
					status_instancia != 4";
		// return $sql;
		$result = $this -> queryArray($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN listar_instancias					------ ************ //////////////////

///////////////// ******** ---- 				listar_proveedores						------ ************ //////////////////
//////// Consulta los proveedores y los devuelve en un array
	// Como parametros recibe:

	function listar_proveedores($objeto) {
		$sql = "SELECT
					idPrv, razon_social
				FROM
					mrp_proveedor
				WHERE
					status = 0";
		// return $sql;
		$result = $this -> queryArray($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN listar_proveedores					------ ************ //////////////////

///////////////// ******** ---- 				listar_productos						------ ************ //////////////////
//////// Consulta los productos y los devuelve en un array
	// Como parametros recibe:

	function listar_productos($objeto) {
		$sql = "SELECT
					id, nombre
				FROM
					app_productos";
		// return $sql;
		$result = $this -> queryArray($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN listar_productos					------ ************ //////////////////

///////////////// ******** ---- 				listar_unidades_medida					------ ************ //////////////////
//////// Consulta las unidades de medida y las devuelve en un array
	// Como parametros recibe:

	function listar_unidades_medida($objeto) {
		$sql = "SELECT
					id, nombre
				FROM
					app_unidades_medida";
		// return $sql;
		$result = $this -> queryArray($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN listar_unidades_medida				------ ************ //////////////////

///////////////// ******** ---- 				mudar_proveedores						------ ************ //////////////////
//////// Muda los proveedores de la version vieja a la nueva
	// Como parametros recibe:
		// instancia_vieja -> Instancia de donde obtenemos la informacion
		// instancia_nueva -> Instancia donde guardamos la informacion
		
	function mudar_proveedores($objeto) {
		$sql = "INSERT IGNORE INTO 
					".$objeto['instancia_nueva'].".mrp_proveedor(idPrv, razon_social, rfc, domicilio, telefono, email, 
					web, diascredito, idestado, idmunicipio, legal, precioycalidad, disponibilidad, idtipotercero, 
					idtipoperacion, curp, cuenta, numidfiscal, nombrextranjero, PaisdeResidencia, nacionalidad, 
					ivaretenido, isretenido, idTasaPrvasumir, idtipoiva, idIETU, ImOtSis, idtipo)
				(Select  idPrv, razon_social, rfc, domicilio, telefono, email, web, diascredito, idestado, idmunicipio, 
					legal, precioycalidad, disponibilidad, idtipotercero, idtipoperacion, curp, cuenta, numidfiscal, 
					nombrextranjero, PaisdeResidencia, nacionalidad, ivaretenido, isretenido, idTasaPrvasumir, idtipoiva, 
					idIETU, ImOtSis, idtipo 
				FROM ".$objeto['instancia_vieja'].".mrp_proveedor);
				
				INSERT IGNORE INTO 
					".$objeto['instancia_nueva'].".app_producto_proveedor(id, id_producto, id_proveedor, id_unidad)
					(SELECT id, idProducto, idPrv, idUni FROM ".$objeto['instancia_vieja'].".mrp_producto_proveedor);
				
				INSERT IGNORE INTO 
					".$objeto['instancia_nueva'].".app_costos_proveedor(id_proveedor, id_producto, id_moneda, costo, fecha)
					(SELECT idPrv, idProducto, 1, costo, curdate() FROM ".$objeto['instancia_vieja'].".mrp_producto_proveedor);";
		// return $sql;
		$result = $this -> dataTransact($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN mudar_proveedores					------ ************ //////////////////

///////////////// ******** ---- 				mudar_productos							------ ************ //////////////////
//////// Muda los productos de la version vieja a la nueva
	// Como parametros recibe:
		// instancia_vieja -> Instancia de donde obtenemos la informacion
		// instancia_nueva -> Instancia donde guardamos la informacion
		
	function mudar_productos($objeto) {
		$sql = "INSERT IGNORE INTO ".$objeto['instancia_nueva'].".app_departamento
				(SELECT * FROM ".$objeto['instancia_vieja'].".mrp_departamento);
		
				INSERT IGNORE INTO ".$objeto['instancia_nueva'].".app_familia
				(SELECT * FROM ".$objeto['instancia_vieja'].".mrp_familia);
				
				INSERT IGNORE INTO ".$objeto['instancia_nueva'].".app_linea
				(id, nombre, id_familia, activo)
				(SELECT idLin, nombre, idFam, 1 AS activo FROM ".$objeto['instancia_vieja'].".mrp_linea);
				
				INSERT IGNORE INTO ".$objeto['instancia_nueva'].".app_producto_impuesto
				(SELECT * FROM ".$objeto['instancia_vieja'].".producto_impuesto);
				
				INSERT IGNORE INTO 
					".$objeto['instancia_nueva'].".app_productos
					(id, codigo, nombre, precio, descripcion_corta, descripcion_larga, ruta_imagen, tipo_producto, maximos, 
					minimos, departamento, familia, linea, inventariable, id_unidad_venta, status, id_unidad_compra)
					(SELECT
						p.idProducto, p.codigo, p.nombre, p.precioventa, p.descorta, p.deslarga, p.imagen, p.tipo_producto, 
						p.maximo, p.minimo, d.idDep, f.idFam, l.idLin, p.vendible, p.idunidad, p.estatus, p.idunidadCompra
					FROM
						".$objeto['instancia_vieja'].".mrp_producto p
					LEFT JOIN
							".$objeto['instancia_vieja'].".mrp_linea l
						ON
							l.idLin = p.idLinea
					LEFT JOIN
							".$objeto['instancia_vieja'].".mrp_familia f
						ON
							f.idFam = l.idFam
					LEFT JOIN
							".$objeto['instancia_vieja'].".mrp_departamento d
						ON
							d.idDep = f.idDep);
				
				INSERT IGNORE INTO ".$objeto['instancia_nueva'].".app_campos_foodware(id_producto)
				(SELECT idProducto FROM ".$objeto['instancia_vieja'].".mrp_producto);";
		// return $sql;
		$result = $this -> dataTransact($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN mudar_productos						------ ************ //////////////////

///////////////// ******** ---- 				mudar_unidades							------ ************ //////////////////
//////// Muda los proveedores de la version vieja a la nueva
	// Como parametros recibe:
		// instancia_vieja -> Instancia de donde obtenemos la informacion
		// instancia_nueva -> Instancia donde guardamos la informacion
		
	function mudar_unidades($objeto) {
		$sql = "INSERT INTO 
					".$objeto['instancia_nueva'].".app_unidades_medida(id, nombre, factor, unidad_base, activo)
					(SELECT idUni, compuesto, conversion, unidad, 1 FROM ".$objeto['instancia_vieja'].".mrp_unidades);";
		// return $sql;
		$result = $this -> query($sql);
		
		return $result;
	}

///////////////// ******** ---- 				FIN mudar_unidades						------ ************ //////////////////

} // Fin Clase herramientasModel

?>