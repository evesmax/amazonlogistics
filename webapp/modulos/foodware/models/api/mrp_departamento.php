<?php
    //Cargar la clase de conexión padre para el modelo
    require_once("models/model_father.php");
    //Cargar los archivos necesarios

    class MrpDepartamentoModel extends Model
    {
        //Definir los atributos de la clase
        public $idDep = null;
        public $nombre = null;

        function __construct($id = null)
        {
            parent::__construct($id);
        }

        function __destruct()
        {

        }

        public static function areas($filtros)
        {
            $consulta = "SELECT DISTINCT m.idDep id, d.nombre AS area 
                        FROM com_mesas m 
                        INNER JOIN mrp_departamento d ON m.idDep = d.idDep 
                        WHERE m.status = 1 AND m.tipo_mesa IS NOT NULL 
                        ORDER BY area;";
            $consulta = DB::queryArray($consulta, array());
            return $consulta;
        }

        public static function moduloTipoPrint() {
            $sql = "SELECT moduloPrint, moduloTipoPrint FROM app_config_ventas;";
            //$result = $this -> queryArray($sql);
            $result = DB::queryArray($sql, array());
		    return $result;//['rows'][0];
        }

        public static function ver_pedidos($request) {
			session_start();
			
			$sql = " SELECT DISTINCT mp.idSuc AS id FROM administracion_usuarios au
							INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc
							WHERE au.idempleado = " . $request['id_empleado'] . " LIMIT 1"; //
			$sucursal = DB::queryArray($sql, array());
			$sucursal = $sucursal['registros'][0]['id'];

			$sql2 = "SELECT tipo_operacion FROM com_configuracion where id_sucursal = ".$sucursal;
			$tipo_operacion = DB::queryArray($sql2, array());
			$tipo_operacion = $tipo_operacion['registros'][0]['tipo_operacion'];

			$pedidos_nuevos = '';
			$_POST['agrupar_producto'] = 1;

			$tipo = $_POST["tipo"];

			$sql3 = "SELECT tipo_operacion FROM com_configuracion where id_sucursal = ".$sucursal;
			$tipo_operacion2 = DB::queryArray($sql3, array());
			$tipo_operacion2 = $tipo_operacion2['registros'][0]['tipo_operacion'];

			$condicion = ($tipo_operacion2 == 1) ? ' AND com.status = 0' : '';
			$agrupar = ($post['agrupar_producto'] == 1) ? '' : ' cp.id, ';
			$where = ($post['agrupar_producto'] == 1) ? ' AND cp.status = 0' : '';

			$myQuery = "SELECT COUNT(DISTINCT cp.id) AS cantidad, idcomanda AS comanda, com.idmesa, cp.id AS producto, cp.id, cp.nota_opcional, cp.nota_extra,
        					cp.nota_sin, npersona AS persona, cp.tipo AS departamento, mp.nombre AS descripcion, timestamp,
        					cp.status, cp.opcionales, cp.adicionales, cp.sin, m.nombre AS nombre_mesa,
        					cp.inicio, cp.fin, m.tipo, m.domicilio, cli.celular AS tel, mp.tipo_producto,
        					mp.id AS id_producto, cp.tiempo AS tiempo_platillo, cp.origen, cp.complementos,
							ROUND(IF(mp.costo_servicio > 0, mp.costo_servicio, pro.costo), 2) AS costo, cp.cantidad AS cantidad_pedidos, notap, d.nombre area, e.nombre mesero
        				FROM com_pedidos cp
        				LEFT JOIN app_productos mp ON mp.id = cp.idproducto
						LEFT JOIN app_costos_proveedor pro ON pro.id_producto = mp.id
						LEFT JOIN com_comandas com ON cp.idcomanda = com.id
        				LEFT JOIN com_mesas m ON com.idmesa = m.id_mesa
						LEFT JOIN comun_cliente cli ON cli.nombre = m.nombre
						LEFT JOIN mrp_departamento d ON d.idDep = m.idDep
	 					LEFT JOIN empleados e on e.idempleado = com.idempleado
        				WHERE 1 = 1 ".$where." AND cp.tipo = ".$tipo." AND mp.id = cp.idproducto ".$condicion." AND cp.id != 0
						AND m.idSuc = ".$sucursal." AND (cp.status = 0 OR cp.status = 1)
        				GROUP BY ".$agrupar." idcomanda, npersona, cp.idproducto, opcionales, adicionales, sin
        				ORDER BY cp.tiempo DESC, timestamp, idcomanda, npersona, cp.id FOR UPDATE";
			$pedidos = DB::queryArray($myQuery, array());

			if ($pedidos["total"] > 0) {
				$ids = array();
				foreach ($pedidos['registros'][0] as $key => $value) {
					if ($value['origen'] == 2) {
						if ($value['cantidad_pedidos'] > 1) {
							$pedidos["rows"][$key]["descripcion"] = 'Combo-- '.$value['cantidad_pedidos'].'x '.$pedidos["rows"][$key]["descripcion"];
						} else {
							$pedidos["rows"][$key]["descripcion"] = 'Combo--'.$pedidos["rows"][$key]["descripcion"];
						}
					}
					
					if (empty($value['inicio'])) {
						date_default_timezone_set('America/Mexico_City');
						$fecha = date('Y-m-d H:i:s');

						$sql4 = "UPDATE com_pedidos
	            			SET inicio = '" . $fecha . "'
	            			WHERE id = " . $value["producto"];
						$inicio = DB::queryArray($sql4, array());

						$pedidos["rows"][$key]["inicio"] = $fecha;
					}

					$ids[] = $value["comanda"];
					$pedidos["rows"][$key]["opcionalesDesc"] = '';
					$pedidos["rows"][$key]["adicionalesDesc"] = '';

					$opcionalesCadena = '';
					$adicionalesCadena = '';
					$sin_cadena = '';
					$string_complementos = '';

					if ($value["opcionales"] != '') {
						$opcionalesCadena = 'Con: ';

						$selectOpcional = "		SELECT nombre,descripcion_larga AS deslarga ";
						$selectOpcional .= "	FROM app_productos ";
						$selectOpcional .= "	WHERE id in (" . $value["opcionales"] . ")  ";
						//$opcionales = $this -> queryArray($selectOpcional);
						$opcionales = DB::queryArray($selectOpcional, array());

						foreach ($opcionales["rows"] as $key2 => $producto) {
							$opcionalesCadena .= ' ' . $producto["nombre"].',';
						}
					}
					

					$opcionalesCadena = trim($opcionalesCadena,',');

					$opcionalesCadena .= ($value["nota_opcional"]) ? ' [' . $value["nota_opcional"] . ']' : '';
					$pedidos["rows"][$key]["opcionalesDesc"] = $opcionalesCadena;

					if ($value["adicionales"] != '') {
						$adicionalesCadena = 'Extras: ';

						$selectAdicional = "	SELECT nombre, descripcion_larga AS deslarga ";
						$selectAdicional .= " 	FROM app_productos ";
						$selectAdicional .= " 	WHERE id IN (" . $value["adicionales"] . ")";
						//$adicionales = $this -> queryArray($selectAdicional);
						$adicionales = DB::queryArray($selectAdicional, array());

						foreach ($adicionales["rows"] as $key2 => $producto) {
							$adicionalesCadena .= ' ' . $producto["nombre"].',';
						}
					}

					$adicionalesCadena = trim($adicionalesCadena, ',');

					$adicionalesCadena .= ($value["nota_extra"]) ? ' [' . $value["nota_extra"] . ']' : '';
					$pedidos["rows"][$key]["adicionalesDesc"] = $adicionalesCadena;

					if ($value["complementos"] != '') {
						$string_complementos = 'Complementos: ';
	
						$sql = "SELECT nombre, descripcion_larga AS deslarga
								FROM app_productos
								WHERE id IN (" . $value["complementos"] . ")";
						//$complementos = $this -> queryArray($sql);
						$complementos = DB::queryArray($sql, array());
						
						foreach ($complementos["rows"] as $key2 => $producto) {
							$string_complementos .= ' ' . $producto["nombre"];
						}
					}
					
	
					$pedidos["rows"][$key]["adicionalesDesc"] = $adicionalesCadena .= $string_complementos ;

					if ($value["sin"] != '') {
						$sin_cadena = 'Sin: ';
	
						$sin = "	SELECT nombre, descripcion_larga AS deslarga ";
						$sin .= " 	FROM app_productos ";
						$sin .= " 	WHERE id in (" . $value["sin"] . ")";
						//$sin = $this -> queryArray($sin);
						$sin = DB::queryArray($sin, array());
	
						foreach ($sin["rows"] as $key2 => $producto) {
							$sin_cadena .= ' ' . $producto["nombre"].',';
						}
					}
					
	
					$sin_cadena = trim($sin_cadena,',');
	
					$sin_cadena .= ($value["nota_sin"]) ? ' [' . $value["nota_sin"] . ']' : '';
					$pedidos["rows"][$key]["sin_desc"] = $sin_cadena;

					if ($post['perro'] == 1) {
						$productoOcupado = "UPDATE com_pedidos
											SET status = 1
											WHERE id = " . $value["producto"] . "
											AND status = 0;";
						//$result = $this -> query($productoOcupado);
						$result = DB::queryArray($productoOcupado, array());
	
					}
					//return $value['origen'];

					if ($value['tipo_producto'] == 6) {
						$pedidos["rows"][$key]["desc_kit"] = $this -> desc_kit($value);
					}
				}

				$ids = array_unique($ids);
				$ids = implode(",", $ids);

				$update = "UPDATE com_comandas ";
				$update .= "SET abierta = 1 ";
				$update .= "WHERE id in(" . $ids . ") ";
				//$update = $this -> query($update);
				$update = DB::queryArray($update, array());

				//return $pedidos;

				if ($update) {
					return $pedidos;
				}
			} else {
				return $pedidos;
			}

			foreach ($pedidos as $key => $value) {
				if ($value['status'] == 0) {
					$pedidos_nuevos[$value["comanda"]]["area"] = $value["area"];
					$pedidos_nuevos[$value["comanda"]]["mesero"] = $value["mesero"];
					$pedidos_nuevos[$value["comanda"]]["nombre_mesa"] = $value["nombre_mesa"];
					$pedidos_nuevos[$value["comanda"]]["inicioPedido"] = $value["timestamp"];
					$pedidos_nuevos[$value["comanda"]]["comanda"] = $value["comanda"];
					$pedidos_nuevos[$value["comanda"]]["mesa"] = $value["idmesa"];
					$pedidos_nuevos[$value["comanda"]]["domicilio"] = $value["domicilio"];
					$pedidos_nuevos[$value["comanda"]]["tel"] = $value["tel"];
					$pedidos_nuevos[$value["comanda"]]["tipo"] = $value["tipo"];
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["opcionalesDesc"] = $value["opcionalesDesc"];
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["adicionalesDesc"] = $value["adicionalesDesc"];
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["sin_desc"] = $value["sin_desc"];
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["nota_sin"] = $value["nota_sin"];

					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["producto"] = utf8_decode($value["producto"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["idproducto"] = utf8_decode($value["idproducto"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["descripcion"] = $value["descripcion"];
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["departamento"] = utf8_decode($value["departamento"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["opcionales"] = utf8_decode($value["opcionales"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["adicionales"] = utf8_decode($value["adicionales"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["sin"] = utf8_decode($value["sin"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["desc_kit"] = utf8_decode($value["desc_kit"]);
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["tiempo_platillo"] = $value["tiempo_platillo"];
					
					if ($value["notap"] != '') {
						$notap = '['.$value["notap"].']';
					} else {
						$notap = '';
					}

					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["notap"] = $notap;
					$pedidos_nuevos[$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["cantidad"] = $value["cantidad"];
				}
			}

			$_POST["perro"] = 1;
			$_POST["agrupar_producto"] = 0;

			$tipo = $_POST["tipo"];

			$sql5 = "SELECT tipo_operacion FROM com_configuracion where id_sucursal = ".$sucursal;
			$tipo_operacion3 = DB::queryArray($sql5, array());
			$tipo_operacion3 = $tipo_operacion3['registros'][0]['tipo_operacion'];

			$condicion = ($tipo_operacion == 1) ? ' AND com.status = 0' : '';
			$agrupar = ($post['agrupar_producto'] == 1) ? '' : ' cp.id, ';
			$where = ($post['agrupar_producto'] == 1) ? ' AND cp.status = 0' : '';

			$myQuery2 = "SELECT COUNT(DISTINCT cp.id) AS cantidad, idcomanda AS comanda, com.idmesa, cp.id AS producto, cp.id, cp.nota_opcional, cp.nota_extra, 
				cp.nota_sin, npersona AS persona, cp.tipo AS departamento, mp.nombre AS descripcion, timestamp,
				cp.status, cp.opcionales, cp.adicionales, cp.sin, m.nombre AS nombre_mesa,
				cp.inicio, cp.fin, m.tipo, m.domicilio, cli.celular AS tel, mp.tipo_producto,
				mp.id AS id_producto, cp.tiempo AS tiempo_platillo, cp.origen, cp.complementos,
				ROUND(IF(mp.costo_servicio > 0, mp.costo_servicio, pro.costo), 2) AS costo, cp.cantidad AS cantidad_pedidos, notap, d.nombre area, e.nombre mesero
				FROM com_pedidos cp
				LEFT JOIN app_productos mp ON mp.id = cp.idproducto
				LEFT JOIN app_costos_proveedor pro ON pro.id_producto = mp.id
				LEFT JOIN com_comandas com ON cp.idcomanda = com.id
				LEFT JOIN com_mesas m ON com.idmesa = m.id_mesa
				LEFT JOIN comun_cliente cli ON cli.nombre = m.nombre
				LEFT JOIN mrp_departamento d ON d.idDep = m.idDep
		 		LEFT JOIN empleados e on e.idempleado = com.idempleado
				WHERE 1 = 1 ".$where." AND cp.tipo = ".$tipo." AND mp.id = cp.idproducto ".$condicion.
				" AND cp.id != 0 AND m.idSuc = ".$sucursal.
				" AND (cp.status = 0 OR cp.status = 1)
				GROUP BY ".$agrupar." idcomanda, npersona, cp.idproducto, opcionales, adicionales, sin
				ORDER BY cp.tiempo DESC, timestamp, idcomanda, npersona, cp.id FOR UPDATE";

			$pedidos2 = DB::queryArray($myQuery2, array());

			if ($pedidos2["total"] > 0) {
				$ids = array();
	
				foreach ($pedidos2["rows"] as $key => $value) {
	
					if ($value['origen'] == 2) {
						if ($value['cantidad_pedidos'] > 1) {
							$pedidos2["rows"][$key]["descripcion"] = 'Combo-- '.$value['cantidad_pedidos'].'x '.$pedidos["rows"][$key]["descripcion"];
						}else{
							$pedidos2["rows"][$key]["descripcion"] = 'Combo--'.$pedidos2["rows"][$key]["descripcion"];
						}
					}
	
				// Guarda el inicio del pedido en los pedidod nuevos
					if (empty($value['inicio'])) {
						date_default_timezone_set('America/Mexico_City');
						$fecha = date('Y-m-d H:i:s');
	
						$sql = "UPDATE com_pedidos
								SET inicio = '" . $fecha . "'
								WHERE id = " . $value["producto"];
						//$inicio = $this -> query($sql);
						$inicio = DB::queryArray($sql, array()); 
	
						$pedidos2["rows"][$key]["inicio"] = $fecha;
					}
	
					$ids[] = $value["comanda"];
					$pedidos2["rows"][$key]["opcionalesDesc"] = '';
					$pedidos2["rows"][$key]["adicionalesDesc"] = '';
	
					$opcionalesCadena = '';
					$adicionalesCadena = '';
					$sin_cadena = '';
					$string_complementos = '';
	
				// ** Opcionales
				//Si el producto tiene opcionales marcados, consultamos la descripcion del opcional.
					if ($value["opcionales"] != '') {
						$opcionalesCadena = 'Con: ';
	
						$selectOpcional = "		SELECT nombre,descripcion_larga AS deslarga ";
						$selectOpcional .= "	FROM app_productos ";
						$selectOpcional .= "	WHERE id in (" . $value["opcionales"] . ")  ";
						//$opcionales = $this -> queryArray($selectOpcional);
						$opcionales = DB::queryArray($selectOpcional, array());
	
						foreach ($opcionales["rows"] as $key2 => $producto) {
							$opcionalesCadena .= ' ' . $producto["nombre"].',';
						}
					}
	
					$opcionalesCadena = trim($opcionalesCadena,',');
	
				// Si tiene nota extra la agrega
					$opcionalesCadena .= ($value["nota_opcional"]) ? ' [' . $value["nota_opcional"] . ']' : '';
					$pedidos2["rows"][$key]["opcionalesDesc"] = $opcionalesCadena;
	
				// ** Extras
				//Si el producto tiene extra marcados, consultamos la descripcion del producto.
					if ($value["adicionales"] != '') {
						$adicionalesCadena = 'Extras: ';
	
						$selectAdicional = "	SELECT nombre, descripcion_larga AS deslarga ";
						$selectAdicional .= " 	FROM app_productos ";
						$selectAdicional .= " 	WHERE id IN (" . $value["adicionales"] . ")";
						//$adicionales = $this -> queryArray($selectAdicional);
						$adicionales = DB::queryArray($selectAdicional, array());
	
						foreach ($adicionales["rows"] as $key2 => $producto) {
							$adicionalesCadena .= ' ' . $producto["nombre"].',';
						}
					}
	
					// elimina la ultima coma
					$adicionalesCadena = trim($adicionalesCadena, ',');
	
				// Si tiene nota extra la agrega
					$adicionalesCadena .= ($value["nota_extra"]) ? ' [' . $value["nota_extra"] . ']' : '';
					$pedidos2["rows"][$key]["adicionalesDesc"] = $adicionalesCadena;
	
	
				// ** Complementos
				//Si el producto tiene extra marcados, consultamos la descripcion del producto.
					if ($value["complementos"] != '') {
						$string_complementos = 'Complementos: ';
	
						$sql = "SELECT nombre, descripcion_larga AS deslarga
								FROM app_productos
								WHERE id IN (" . $value["complementos"] . ")";
						//$complementos = $this -> queryArray($sql);
						$complementos = DB::queryArray($sql, array());
						foreach ($complementos["rows"] as $key2 => $producto) {
							$string_complementos .= ' ' . $producto["nombre"];
						}
					}
	
					$pedidos2["rows"][$key]["adicionalesDesc"] = $adicionalesCadena .= $string_complementos ;
	
				// ** Sin
				//Si el producto tiene extra marcados, consultamos la descripcion del producto.
					if ($value["sin"] != '') {
						$sin_cadena = 'Sin: ';
	
						$sin = "	SELECT nombre, descripcion_larga AS deslarga ";
						$sin .= " 	FROM app_productos ";
						$sin .= " 	WHERE id in (" . $value["sin"] . ")";
						//$sin = $this -> queryArray($sin);
						$sin = DB::queryArray($sin, array());
	
						foreach ($sin["rows"] as $key2 => $producto) {
							$sin_cadena .= ' ' . $producto["nombre"].',';
						}
					}
	
					$sin_cadena = trim($sin_cadena,',');
	
					$sin_cadena .= ($value["nota_sin"]) ? ' [' . $value["nota_sin"] . ']' : '';
					$pedidos2["rows"][$key]["sin_desc"] = $sin_cadena;
	
	
	
				// Se bloquea el producto para no imprimirlo cada que se manda llamar la funcion
					if ($post['perro'] == 1) {
						$productoOcupado = "UPDATE com_pedidos
											SET status = 1
											WHERE id = " . $value["producto"] . "
											AND status = 0;";
						//$result = $this -> query($productoOcupado);
						$result = DB::queryArray($productoOcupado, array());
	
					}
	
				// Kit
					if ($value['tipo_producto'] == 6) {
						$pedidos["rows"][$key]["desc_kit"] = $this -> desc_kit($value);
					}
				}
	
				$ids = array_unique($ids);
				$ids = implode(",", $ids);
	
				/* Marcamos las comandas como abiertas = 1, para que no esten disponibles en otras pantallas, sin afectar el estatus de terminado.
				 */
	
				$update = "		UPDATE
									com_comandas ";
				$update .= "	SET
									abierta = 1 ";
				$update .= "	WHERE
									id in(" . $ids . ") ";
				//$update = $this -> query($update);
				$update = DB::queryArray($update, array());
	
				if ($update) {
					return $pedidos2;
				}
			} else {
				//return $pedidos2;
			}

			$ticket = $_POST['ticket'];
			$vista_listado = $_POST['vista_listado'];

			$producto = 0;
			$persona = 0;
			$id = $_POST['tipo'];

			foreach ($pedidos2 as $key => $value) {
				if ($persona != $value["persona"]) {
					$persona = $value["persona"];
					$producto = 0;
					$cantidad = 0;
				} else {
					$producto++;
				}

				date_default_timezone_set('America/Mexico_City');
				if (!empty($value['inicio'])) {
					$segundos = strtotime(date('Y-m-d H:i:s')) - strtotime($value['inicio']);
					$horas = floor($segundos / 3600);
					$minutos = floor(($segundos - ($horas * 3600)) / 60);
					$tiempo = $horas.":".$minutos;
				}else{
					$tiempo = "0:0";
				}
			
				$value['tiempo'] = $tiempo;

				$pedidos_permanentes["pedidos"][$id]["status"] = TRUE;
				$pedidos_permanentes["pedidos"][$id]["organizacion"] = $_SESSION["accelog_nombre_organizacion"];

				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["area"] = $value["area"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["mesero"] = $value["mesero"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["nombre_mesa"] = $value["nombre_mesa"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["inicioPedido"] = $value["timestamp"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["comanda"] = $value["comanda"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["mesa"] = $value["idmesa"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["domicilio"] = $value["domicilio"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["tel"] = $value["tel"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["tipo"] = $value["tipo"];

				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["tiempo"] = $tiempo;
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["opcionales"] = utf8_decode($value["opcionales"]);
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["adicionales"] = utf8_decode($value["adicionales"]);
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["sin"] = utf8_decode($value["sin"]);

				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["opcionalesDesc"] = $value["opcionalesDesc"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["adicionalesDesc"] = $value["adicionalesDesc"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["sin_desc"] = $value["sin_desc"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["nota_sin"] = $value["nota_sin"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["desc_kit"] = $value["desc_kit"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["costo"] = $value["costo"];

				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["producto"] = utf8_decode($value["producto"]);
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["idproducto"] = utf8_decode($value["idproducto"]);
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["cantidad"] = utf8_decode($value["cantidad"]);
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["descripcion"] = $value["descripcion"];
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["departamento"] = utf8_decode($value["departamento"]);
				if ($value["notap"] != '') { 
					$notap = '['.$value["notap"].']'; 
				} else { 
					$notap = '';
				}
				$pedidos_permanentes["pedidos"][$id]["comanda"][$value["comanda"]]["persona"][$value["persona"]]["productos"][$value["producto"]]["notap"] = $notap; 

				$listado_pedidos[$key] = $value;
			}

			$sql6 = "SELECT * FROM com_configuracion where id_sucursal = ".$sucursal;
			$ajustes = DB::queryArray($sql6, array());

			$datos['vista_listado'] = $vista_listado;
		  	$datos['ticket'] = $ticket;
		  	$datos['listado_pedidos'] = $listado_pedidos;

		  	$datos['pedidos_nuevos'] = $pedidos_nuevos;
		  	$datos['pedidos_permanentes'] = $pedidos_permanentes;
			$datos['id'] = $id;
			  
			if($_POST['moduloPrint'] == 1) {
				//echo json_encode($datos);
				//return json_encode($datos);
				//return $datos;
			} else {
				if ($vista_listado == 1) {

				}
			}

			if ($datos['ticket'] == 1 && ($datos['pedidos_nuevos'] != null && $datos['pedidos_nuevos'] != "" )) {
				return $datos['pedidos_nuevos'];
			}

			$sql7 = "SELECT tipo_operacion FROM com_configuracion where id_sucursal = ".$sucursal;
			$tipo_operacion4 = DB::queryArray($sql7, array());
			$tipo_operacion4 = $tipo_operacion4['registros'][0]['tipo_operacion'];

			$listado_pedidos = $datos['listado_pedidos'];
			$vista_listado = $datos['vista_listado'];

			return $datos;
		}

		public function terminar_pedido($objeto) {

// parametros necesarios
/*
adicionales: ""
adicionalesDesc: ""
area: "Principal"
cantidad: "1"
cantidad_pedidos: "1"
celular: null
comanda: "1315"
complementos: null
costo: "1.00"
departamento: "1"
descripcion: "Camaron al ajillo"
domicilio: ""
fin: null
id: "5041"
id_producto: "27"
idmesa: "1247"
inicio: "2018-10-22 13:40:27"
mesero: "Enrique"
nombre_mesa: "3"
nota_extra: ""
nota_opcional: ""
nota_sin: ""
notap: null
opcionales: ""
opcionalesDesc: ""
origen: "1"
persona: "1"
producto: "5041"
referencia: null
sin: ""
sin_desc: ""
status: "1"
tel: null
tiempo: "0:0"
tiempo_platillo: "0"
timestamp: "2018-10-22 13:39:56"
tipo: "0"
tipo_producto: "5"
*/

			$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

			date_default_timezone_set('America/Mexico_City');
			$fecha = date('Y-m-d H:i:s');

			$sql = "UPDATE com_pedidos
					SET status = 2, fin = '" . $fecha . "'
					WHERE id = " . $objeto['id_pedido'];
			//$result = $this -> query($sql);
			$result = DB::queryArray($sql, array());

			if (!empty($result['result'])) {
				session_start();
				// Elimina el pedido del array de pendientes
				$_SESSION["pedidos"][$id]["comanda"][$objeto["comanda"]]["persona"][$objeto["persona"]]["productos"][$objeto["producto"]] = '';
				unset($_SESSION["pedidos"][$id]["comanda"][$objeto["comanda"]]["persona"][$objeto["persona"]]["productos"][$objeto["producto"]]);
		
				// Agrega el pedido al array de terminados
				$_SESSION['terminados'][$objeto['comanda']]['persona'][$objeto['persona']]['productos'][$objeto['producto']] = $objeto;
				$_SESSION['listado_terminados'][$objeto['producto']] = $objeto;
			
				// Todo bien :D, regresa el resultado
				$result['terminados'] = $_SESSION['terminados'];
				$result['status'] = 1;
			} else {
				$result['status'] = 0;
			}

			return $result;
		}

		public function cancelar_pedido($objeto) {
			$objeto = (empty($objeto)) ? $_REQUEST : $objeto;

			date_default_timezone_set('America/Mexico_City');
			$fecha = date('Y-m-d H:i:s');

			$sql = "UPDATE com_pedidos
					SET status = 3, fin = '" . $fecha . "'
					WHERE id = " . $objeto['id_pedido'];
			//$result = $this -> query($sql);
			$result = DB::queryArray($sql, array());

			if (!empty($result['result'])) {
				session_start();
				// Elimina el pedido del array de pendientes
				$_SESSION["pedidos"][$id]["comanda"][$objeto["comanda"]]["persona"][$objeto["persona"]]["productos"][$objeto["producto"]] = '';
				unset($_SESSION["pedidos"][$id]["comanda"][$objeto["comanda"]]["persona"][$objeto["persona"]]["productos"][$objeto["producto"]]);
		
				// Agrega el pedido al array de terminados
				$_SESSION['terminados'][$objeto['comanda']]['persona'][$objeto['persona']]['productos'][$objeto['producto']] = $objeto;
				$_SESSION['listado_terminados'][$objeto['producto']] = $objeto;
			
				// Todo bien :D, regresa el resultado
				$result['terminados'] = $_SESSION['terminados'];
				$result['status'] = 1;
			} else {
				$result['status'] = 0;
			}

			return $result;
		}
		
		public function obtener_pedidos($objeto) {
			$sql = "SELECT ped.id, ped.idcomanda, ped.cantidad, ped.inicio AS fecha_inicio, mp.nombre AS pedido, 
			ms.nombre AS nombre_mesa, accus.usuario, d.nombre AS nombre_area, com.personas, mp2.nombre AS opcionales 
			FROM com_pedidos ped
			LEFT JOIN app_productos mp ON mp.id = ped.idproducto
			LEFT JOIN app_productos mp2 ON mp2.id = ped.opcionales
			LEFT JOIN com_comandas com ON ped.idcomanda = com.id
			LEFT JOIN accelog_usuarios accus ON accus.idempleado = com.idempleado
			LEFT JOIN com_mesas ms ON ms.id_mesa = com.idmesa
			INNER JOIN mrp_departamento d ON ms.idDep = d.idDep
			WHERE ped.status = ".$objeto['status']." AND ped.tipo = ".$objeto['tipo']."
			ORDER BY ped.id DESC";

			$resultado = DB::queryArray($sql, array());

			return $resultado;
		}

		public function info_ventas($objeto) {
			$sql = "SELECT v.idVenta, CASE WHEN v.documento = 1 THEN 'Ticket' ELSE 'Factura' END AS Documento,
			v.monto, v.subtotal, v.montoimpuestos, v.impuestos AS jsonImpuestos, v.fecha,
			IF(v.idCliente !='NULL',c.nombre, 'Publico en General') AS cliente, p.codigo AS codigoProducto,
			p.nombre AS producto,vp.cantidad,vp.preciounitario,vp.subtotal AS subtotalProducto, 
			vp.impuestosproductoventa AS impuestosProducto, vp.total AS totalProducto,fp.nombre
			FROM app_pos_venta v
			LEFT JOIN app_pos_venta_producto vp ON vp.idVenta=v.idVenta
			LEFT JOIN app_productos p ON vp.idProducto=p.id
			LEFT JOIN app_pos_venta_pagos vfp ON vfp.idVenta=v.idVenta
			LEFT JOIN forma_pago fp ON fp.idformapago=vfp.idFormapago
			LEFT JOIN comun_cliente c ON c.id=v.idCliente
			WHERE (v.fecha BETWEEN '".$objeto['fecha_inicial']."' AND '".$objeto['fecha_final']."')";

			// '2018-12-01 14:15:55'
			// '2018-12-29 10:15:55'

			$resultado = DB::queryArray($sql, array());

			return $resultado;
		}
    }

?>
