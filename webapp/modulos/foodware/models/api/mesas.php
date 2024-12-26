<?php

//Cargar la clase de conexión padre para el modelo
require_once("models/model_father.php");
//Cargar los archivos necesarios

class MesasModel extends Model 
{
    function __construct($id = null)
    {
        parent::__construct($id);
    }
    
    function __destruct()
    {

    }
    
    public static function index()
    {
        return array("status" => true, "registros" => array());
    }
    
    public static function obtenerMesas($request)
    {
        /*$sql = "SELECT u.idempleado AS id, usuario, permisos, p.idperfil AS perfil FROM accelog_usuarios u INNER JOIN administracion_usuarios a ON u.idempleado = a.idempleado LEFT JOIN com_meseros m ON m.id_mesero = u.idempleado LEFT JOINm accelog_usuarios_per p ON p.idempleado = u.idempleado WHERE  u.idempleado = " . $request["id_mesero"]; $permisos = DB::queryArray($sql, array()); */
        $sucursal = "   SELECT mp.idSuc AS id FROM administracion_usuarios au
                INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc
                WHERE au.idempleado = " . $request['id_mesero'] . " LIMIT 1";
        
        $queryMesero =  "SELECT * FROM com_meseros WHERE id_mesero = '".$request["id_mesero"]."'";
        
        $sucursal = DB::queryArray($sucursal, array());
        $sucursal = $sucursal["registros"][0]["id"];
        
        $mesero = DB::queryArray($queryMesero, array());
        $mesero = $mesero["registros"][0];

        $objeto["noJuntas"] = 1;

        $objeto['f_ini'] = $_SESSION['f_ini'];
        $objeto['f_fin'] = $_SESSION['f_fin'];

        //Se valida que el mesero tenga Permisos y Asignaciones
        if($mesero["permisos"] != "" && $mesero['asignacion'] != "")
        {
            //Filtro de departamento
            $condicion = ' AND a.idDep="' . $request['id'] . '"';
            // Filtro de los permisos de Mesero
            $condicion .= ' AND (a.id_mesa IN(' . $mesero["permisos"] . ') OR a.tipo != 0)';
            // Filtra por las asignaciones del mesero
            $condicion .= ' AND a.id_mesa IN(' . $mesero['asignacion'] . ')';
            // Filtra para que no se muestren las mesas de servicio a domicilio y para llevar
            //$condicion .= ($objeto['asignar'] == 1) ? ' AND a.tipo=0' : '';
        }
        else{
            return array("status" => true, "registros" => array(array()));
        }

        //modificacion para obtener el total de las mesas con comandas

        if(!$objeto["noJuntas"]){
            $sql = "SELECT a.id_mesa AS mesa, IF(e.total is NULL,0,e.total) AS total, res.id as id_res, a.tipo,  a.x, a.y, a.width
            as width_barra, s.nombre as sucursal, a.id_area, a.height as height_barra, b.nombre, b.idDep, e.personas, a.status
            as mesa_status, a.tipo, a.domicilio, a.idempleado, ad.nombreusuario AS mesero, a.notificacion, tm.id
            as id_tipo_mesa, tm.tipo_mesa, tm.width, tm.height, tm.imagen, IF(GROUP_CONCAT(c.idmesa) is NULL, a.nombre, (SELECT GROUP_CONCAT(d.nombre)
            FROM com_mesas d
                INNER JOIN com_union c ON c.idmesa=d.id_mesa
                WHERE c.idprincipal = a.id_mesa)) nombre_mesa,
                        if(GROUP_CONCAT(c.idmesa) is NULL,'',GROUP_CONCAT(c.idmesa))
                            idmesas,
                        if(GROUP_CONCAT(d.personas) is NULL,'',GROUP_CONCAT(d.personas))
                            mpersonas,
                        if(e.id is NULL,0,e.id)
                            idcomanda FROM com_mesas a
                LEFT JOIN administracion_usuarios ad ON ad.idempleado = a.idempleado
                LEFT JOIN mrp_departamento b ON b.idDep = a.idDep
                LEFT JOIN mrp_sucursal s ON s.idSuc = a.idSuc
                LEFT JOIN com_union c ON c.idprincipal = a.id_mesa
                LEFT JOIN com_mesas d ON d.id_mesa = c.idmesa
                LEFT JOIN com_comandas e ON e.idmesa = a.id_mesa
                AND e.status = 0
                LEFT JOIN com_reservaciones res ON res.mesa = a.id_mesa
                AND res.activo = 1
                AND (res.inicio >= '".$objeto['f_ini']."'
                    AND res.inicio <= '".$objeto['f_fin']."')
                    JOIN com_tipo_mesas tm ON a.tipo_mesa = tm.id
                    WHERE a.status = 1
                    AND (a.id_mesa NOT IN(select idmesa from com_union)
                    OR a.id_mesa IN(select idprincipal from com_union))" . $condicion . "
                    AND a.id_dependencia = 0
                    AND a.idSuc = " . $sucursal ."
                    OR a.status = 4
                    AND (a.id_mesa NOT IN(select idmesa from com_union)
                    OR a.id_mesa IN(select idprincipal from com_union))" . $condicion . "
                    AND a.id_dependencia = 0
                    AND a.idSuc = " . $sucursal ."
                    GROUP BY a.id_mesa
                    ORDER BY a.id_mesa asc";



        } else {
            $sql = "SELECT a.id_mesa AS mesa, IF(e.total is NULL,0,e.total) AS total, a.tipo , a.x, a.y, a.width as width_barra, s.nombre
            as sucursal, a.id_area, a.height as height_barra, b.nombre, e.personas, a.status as mesa_status, a.domicilio, a.idempleado, ad.nombreusuario AS mesero, a.notificacion, a.nombre as nombre_mesa, if(e.id is NULL,0,e.id) idcomanda, tm.id as id_tipo_mesa, tm.tipo_mesa, tm.width, tm.height, tm.imagen
                FROM com_mesas a
                LEFT JOIN administracion_usuarios ad ON ad.idempleado = a.idempleado
                LEFT JOIN mrp_departamento b ON b.idDep = a.idDep
                LEFT JOIN mrp_sucursal s ON s.idSuc = a.idSuc
                LEFT JOIN com_mesas d ON d.id_mesa = a.id_mesa
                LEFT JOIN com_comandas e ON e.idmesa = a.id_mesa
                AND e.status = 0 JOIN com_tipo_mesas tm ON a.tipo_mesa = tm.id
                WHERE a.status = 1
                AND a.id_dependencia = 0
                " . $condicion . "
                AND a.idSuc = " . $sucursal ."
                OR a.status = 4
                and a.tipo = 0
                AND a.id_dependencia = 0
                " . $condicion . "
                AND a.idSuc = " . $sucursal ."
                GROUP BY a.id_mesa
                ORDER BY a.id_mesa asc";
        }
        $resultado = DB::queryArray($sql, array());

        //cambiando los nulos para iOS
        $index = 0;
        foreach ($resultado["registros"] as $value) {
            if($value["personas"] == null){
                $resultado["registros"][$index]["personas"] = "0";
            }
            if($value["nombre_mesa"] == null){
                $resultado["registros"][$index]["nombre_mesa"] = "";
            }
            $index = $index + 1;
        }

        return $resultado;
    }

    public static function insertarComanda($request)
        {
            //print_r($request); exit();
            $idmesa = $request["id_mesa"];
            $iddeparment = $request["id_departamento"];
            $usuario = $request["id_mesero"];

            // Inserta la comanda en la BD
            date_default_timezone_set('America/Mexico_City');

            $fecha = date('Y-m-d H:i:s');
            $sql = "INSERT INTO com_comandas(id, idmesa, personas, status, tipo, codigo, timestamp, abierta, idempleado)
                    VALUES ('','$idmesa','0','0','$iddeparment','','" . $fecha . "','3','" . $usuario . "');";
            $comanda = DB::queryArray($sql, array());
            //*************insert_id???????????

            // ** Consulta si es la comanda de la reservacion
            $sql = "SELECT * FROM com_reservaciones WHERE 1 = 1 AND '" . $fecha . "' BETWEEN inicio AND fin AND activo = 1;";
            $reservaciones = DB::queryArray($sql, array());

            // Si es la comanda actualiza la reservacion
            if (!empty($reservaciones['rows'])) {
                $sql = "UPDATE com_reservaciones SET activo = 1 WHERE id=" . $reservaciones['rows'][0]['id'];
                $update = DB::queryArray($sql, array());
            }
            // ** FIN Consulta si es la comanda de la reservacion
            // Agrega el codigo al a comanda
            if ($comanda["status"] && $comanda["total"] >= 1) {
                $size = 5 - strlen($comanda["id_insertado"]);
                $string = "";

                for ($i = 0; $i < $size; $i++)
                    $string .= "0";
                $string .= $comanda["id_insertado"];
                $sql = "UPDATE com_comandas SET codigo='COM" . $string . "' WHERE id = " . $comanda["id_insertado"];
                DB::queryArray($sql, array());
            }

            //** Guarda la actividad
            $sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
                    VALUES (''," . $usuario . ",'Abre comanda', '" . $fecha . "')";
            $actividad = DB::queryArray($sql, array());


            if($comanda["status"] && $comanda["total"] >= 1){
                $comanda["status"] = true;
                $comanda["registros"] =  json_decode('[{"id_comanda":"'. $comanda["id_insertado"] .'"}]');
            }else{
                $comanda["status"] = false;
                $comanda["mensaje"] = "No tiene opcionales";
            }

            return $comanda;
        }

    public static function insertarComensal($request)
    {
            $idcomanda = $request["id_comanda"];
            $usuario = $request["id_mesero"];
            // ** inserta el primer comensal por default
            $sql = "UPDATE com_comandas SET personas = personas + 1, comensales = personas WHERE id=" . $idcomanda;
                    DB::queryArray($sql, array());

            //Obtiene el numero de personas
            $sql = "SELECT npersona FROM com_pedidos WHERE idcomanda = " . $idcomanda . " ORDER BY npersona DESC LIMIT 1";
            $persons = DB::queryArray($sql, array());

            $idperson = 0;
            if ($persons["total"] > 0) {
                $row = $persons["registros"];
                $sql = "INSERT INTO com_pedidos (id, idcomanda, idproducto, cantidad, npersona, tipo, status, opcionales, adicionales)
                        VALUES (null, '$idcomanda', '0', '0', '" . ($row[0]['npersona'] + 1) . "', '0', '0', '', '')";
                $resultado  = DB::queryArray($sql, array());
              //print_r($row[0]['npersona']);exit();
                $idperson = ($row[0]['npersona'] + 1);
            } else {
                $sql = "INSERT INTO com_pedidos (id, idcomanda, idproducto, cantidad, npersona, tipo, status, opcionales, adicionales)
                        VALUES (null,'$idcomanda','0','0','1','0','0','','')";
                $resultado = DB::queryArray($sql, array());
                $idperson = 1;
            }

            //** Guarda la actividad
            $fecha = date('Y-m-d H:i:s');

            $sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
                    VALUES (''," . $usuario . ",'Agrega persona', '" . $fecha . "')";
            $actividad = DB::queryArray($sql, array());


            if($resultado["status"] && $resultado["total"] >= 1){
                $resultado["status"] = true;
                $resultado["registros"] =  json_decode('[{"id_comensal":"'. $idperson .'"}]');
            }else{
                $resultado["status"] = false;
                $resultado["mensaje"] = "No inserto el comensal";
            }

        return $resultado;
    }

    public static function borrarComensal($request)
    {
        $idcomanda = $request["id_comanda"];
        $usuario = $request["id_mesero"];
        $persons = $request["id_comensal"];
        // Actualiza la cantidad de personas en la mesa
        $sql = "UPDATE com_comandas SET personas = (personas-" . count(explode(',', $persons)) . ") WHERE id=" . $idcomanda;
        $person = DB::queryArray($sql, array());
        // Elimina los pedidos de la persona
        $sql = "DELETE FROM com_pedidos WHERE idcomanda = " . $idcomanda . " AND npersona in(" . $persons . ")";
        $person = DB::queryArray($sql, array());
        //** Guarda la actividad
        $fecha = date('Y-m-d H:i:s');
        // Valida que exista el empleado si no agrega un cero como id
        $sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
                    VALUES (''," . $usuario . ",'Elimina persona', '" . $fecha . "')";
        $actividad = DB::queryArray($sql, array());

        return $person;
    }

    public static function cerrarComanda($request)
    {
        $idComanda = $request["id_comanda"];
        $rbandera = $request["id_bandera"];
        $usuario = $request["id_mesero"];

        $tipo_mesa = $request["tipo_mesa"];
        $id_mesa = $request["id_mesa"];

        // Elimina la mesa si es servicio a domicilio o para llevar
        if ($tipo_mesa == 3 ) {
            $sql = "UPDATE com_mesas SET status = 2 WHERE id_mesa = " .$id_mesa;
            $result = DB::queryArray($sql, array());

            $sql = "SELECT permisos, asignacion FROM com_meseros WHERE id_mesero = " .$usuario;
            $result = DB::queryArray($sql, array());
            $permisos = $result['registros'][0]['permisos'];
            $asignacion = $result['registros'][0]['asignacion'];
            $id_mesa = ','.$id_mesa;
            $permisos = str_replace($id_mesa,"",$permisos);
            $asignacion = str_replace($id_mesa,"",$asignacion);
            $sql = "UPDATE com_meseros SET permisos = '".$permisos."', asignacion = '".$asignacion."' WHERE id_mesero = " .$usuario;
            $result = DB::queryArray($sql, array());

        }

        // Inserta la comanda en la BD
        date_default_timezone_set('America/Mexico_City');

        $fecha = date('Y-m-d H:i:s');

        // Actualiza el estatus de la comanda para marcar como cerrada
        $sql = "UPDATE com_comandas SET status = 2, fin = '$fecha', individual = '" . $rbandera . "' WHERE id = " . $idComanda;
        $status = DB::queryArray($sql, array());

        // actividades
        $sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
                    VALUES (''," . $usuario . ",'Cierra comanda', '" . $fecha . "')";
        $actividad = DB::queryArray($sql, array());

        return $status;
    }

    public static function obtenerComensales($request){
        $idComanda = $request["id_comanda"];

        $sql = "SELECT npersona, COUNT(npersona) AS num_personas FROM com_pedidos
                    WHERE idcomanda = " . $idComanda . " AND origen = 1 GROUP BY npersona ORDER BY npersona ASC";
        // return $sql;
        $comensales = DB::queryArray($sql, array());

        return $comensales;
    }

    public static function agregarMesa($request)
    {
        //$nombreMesa,$idsuc,$mesero,$idarea

        $sucursal = "   SELECT mp.idSuc AS id FROM administracion_usuarios au
                            INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc
                            WHERE au.idempleado = " . $request['id_mesero'] . "
                            LIMIT 1";

        $sucursal = DB::queryArray($sucursal, array());
        $idsuc = $sucursal["registros"][0]["id"];

        $nombreMesa = $request["nombre_mesa"];
        $idarea = $request["id_area"];
        $mesero = $request["id_mesero"];

        $sql = "INSERT INTO com_mesas (idDep, personas, tipo, nombre, domicilio, idempleado, x, y, status, idSuc, notificacion, tipo_mesa, width, height, id_area, id_dependencia, password)
                VALUES('$idarea', 12, 3, '$nombreMesa', '', '$mesero', 18, 0, 1, '$idsuc', 0, 1, 2, 2, 1, 0, '');";

        $idmesa = DB::queryArray($sql, array());

        //print_r($idmesa["id_insertado"]);

        $sql = "UPDATE com_meseros
                    SET asignacion = concat(asignacion,',".$idmesa["id_insertado"]."'), permisos = concat(permisos,',".$idmesa["id_insertado"]."')
                    WHERE id_mesero = " . $mesero;

        $result = DB::queryArray($sql, array());

        return $idmesa;
    }
    
    public static function listar_reservaciones($objeto) {
        /*foreach ($objeto as $key => $value) {
            $datos[$key] = $this -> escapalog($value);
        }*/

    // Filtra por la sucursal si existe
    $condicion = ($objeto['sucursal'] != '*' && !empty($objeto['sucursal'])) ?
      			' AND m.idSuc = \'' . $objeto['sucursal'] . '\'' : '';
      	// Filtra por el ID de la comanda si existe
      		$condicion = (!empty($datos['comanda'])) ? ' AND idprincipal=\'' . $datos['comanda'] . '\'' : '';
      	// Si viene el id del empleado Filtra por empleado
      		$condicion = ($objeto['cliente'] != '*' && !empty($objeto['cliente'])) ?
      			' AND r.idCliente = \'' . $objeto['cliente'] . '\'' : '';
      	// Si viene el id de la mesa Filtra por la mesa
      		$condicion = ($objeto['mesa'] != '*' && !empty($objeto['mesa'])) ?
      			' AND m.id_mesa=\'' . $objeto['mesa'] . '\'' : '';
      	// Filtra por el status si existe
      		$condicion = (!empty($datos['status'])) ? ' AND r.activo=' . $datos['status'] : '';
      	// Filtra por fecha de inicio y fin si existen
      		$condicion = (!empty($datos['f_ini']) && !empty($datos['f_fin'])) ?
      			' AND ((r.inicio BETWEEN \'' . $datos['f_ini'] . '\' AND \'' . $datos['f_fin'] . '\')
      				OR (r.fin BETWEEN \'' . $datos['f_ini'] . '\' AND \'' . $datos['f_fin'] . '\'))' : '';
      	// Filtra por fecha de inicio
      		$condicion = (!empty($datos['f_ini']) && empty($datos['f_fin'])) ?
      			' AND (r.inicio >= \'' . $datos['f_ini'] . ' 00:01\' AND r.inicio <=\'' . $datos['f_ini'] . ' 23:59\')' : '';

      	// Ordena la consulta por los parametros indicados si existe, si no la ordena por id Descendente
      		$agrupar = (!empty($objeto['agrupar'])) ? ' GROUP BY ' . $objeto['agrupar'] : ' GROUP BY r.id';

      	// Ordena la consulta por los parametros indicados si existe, si no la ordena por id Descendente
      		$orden = (!empty($datos['orden'])) ? ' ' . $datos['orden'] : ' r.id DESC';

      		$sql = "SELECT
      					COUNT(r.id) AS reservaciones, r.* ,c.id AS id_cliente, c.nombre AS cliente, m.nombre AS nombre_mesa, m.tipo_mesa as tipo_mesa,
      					suc.nombre AS sucursal, com.id_venta, com.total
      				FROM
      					com_reservaciones r
      				LEFT JOIN
      						comun_cliente c
      					ON
      						c.id = r.idCliente
      				LEFT JOIN
      						com_mesas m
      					ON
      						m.id_mesa = r.mesa
      				LEFT JOIN
      						mrp_sucursal suc
      					ON
      						suc.idSuc = m.idSuc
      				LEFT JOIN
      						accelog_usuarios u
      					ON
      						m.idempleado = u.idempleado
      				LEFT JOIN
      						com_comandas com
      					ON
      						com.idmesa = m.id_mesa
      					AND
      						com.status = 1
      				WHERE
      					1=1 " .
      					$condicion . " " .
      				$agrupar . "
      				ORDER BY " .
      					$orden;
      		// return $sql;
      		//$result = $this -> queryArray($sql);
          $result = DB::queryArray($sql, array());

      		return $result;
          //return $objeto;
    }

    public static function listar_clientes_reservacion($objet) {
        $orden = (!empty($objeto['orden'])) ? 'ORDER BY ' . $objeto['orden'] : 'ORDER BY id ASC';

		$sql = "SELECT
					id, nombre, celular, email
				FROM
					comun_cliente
				WHERE
					1 = 1 " .
				$orden;
				//print_r($sql);
        //$result = $this -> queryArray($sql);
        $result = DB::queryArray($sql, array());

        return $result;
    }
    
    public static function guardar_reservacion($objeto){
    	// Anti hack
		/*foreach ($objeto as $key => $value) {
			$datos[$key] = $this -> escapalog($value);
		}*/
		//print_r($datos);
		// Valida si existe la mesa o no
		$mesa = (!empty($objeto['mesa'])) ? $objeto['mesa'] : '';
		// Guarda la reservacion
		$sql = "INSERT INTO com_reservaciones(inicio, descripcion, idCliente, activo, mesa, num_personas)
						VALUES('".$objeto['fecha']."','" . $objeto['des'] . "','" . $objeto['cliente_id'] . "','".$objeto['status']."','" . $mesa . "', '".$objeto['num_per']."')";
		//print_r($datos['cliente']);
		//$result = $this -> insert_id($sql);
    	$result = DB::queryArray($sql, array());

		// Guarda la actividad
		$fecha = date('Y-m-d H:i:s');
		// Valida que exista el empleado si no agrega un cero como id
		$usuario = (!empty($_SESSION['accelog_idempleado'])) ? $_SESSION['accelog_idempleado'] : 0;
    	//$usuario = (!empty($objeto['sesion'])) ? $objeto['sesion'] : 0;

		$sql = "INSERT INTO com_actividades(empleado, accion, fecha)
						VALUES(" . $usuario . ",'Agrega reservacion', '" . $fecha . "')";

		//$actividad = $this -> query($sql);
    	$actividad = DB::queryArray($sql, array());
    	//return $objeto['correo'];
		if($result && !empty($objeto['correo'])){

			//return $objeto;

			$content = '<div style="width:100%; text-align: center;">';
			if (!empty($objeto['logo'])) {
				$content = $content.'<div id="logo" style="text-align: center">
					<input type="image" src="'.$objeto['logo'].'" style="width:90%; max-width: 350px;"/>
				</div>';
			}

			$content = $content.'<br><div class="info_correo" style="margin:auto; width: 70%; text-align: center; font-size:15px;font-family: Tahoma,'."'".'Trebuchet MS'."'".',Arial;">Confirmación de Reservación en '.$objeto['organizacion'][0]['nombreorganizacion'].'.</div>';

			$content = $content.'<br><div class="info_correo" style="margin:auto; width: 70%; text-align: center; font-size:15px;font-family: Tahoma,'."'".'Trebuchet MS'."'".',Arial;">'.utf8_decode($objeto['datos_sucursal'][0]['direccion']." ".$objeto['datos_sucursal'][0]['municipio'].", ".$objeto['datos_sucursal'][0]['estado']).'</div>';

			$content = $content.'<div class="info_correo" style="margin:auto; width: 70%; text-align: center; font-size:15px;font-family: Tahoma,'."'".'Trebuchet MS'."'".',Arial;">'.$objeto['fecha'].'</div>';

			$content = $content.'<br><div class="info_correo" style="margin:auto; width: 70%; text-align: center; font-size:15px;font-family: Tahoma,'."'".'Trebuchet MS'."'".',Arial;">'.$objeto['nombre'].'</div>';

			if($objeto['organizacion'][0]['paginaweb']!='-' || !empty($objeto['datos_sucursal'][0]['tel_contacto'])){
				$content = $content.'<br><br><div class="info_correo" style="margin:auto; width: 70%; text-align: center; font-size:15px;font-family: Tahoma,'."'".'Trebuchet MS'."'".',Arial;">Dudas o aclaraciones: ';
				if(!empty($objeto['datos_sucursal'][0]['tel_contacto'])){
					$content = $content.$objeto['datos_sucursal'][0]['tel_contacto'];
					if($objeto['organizacion'][0]['paginaweb']!='-'){
						$content = $content.' y ';
					}
				}
				if($objeto['organizacion'][0]['paginaweb']!='-'){
					$content = $content.$objeto['organizacion'][0]['paginaweb'];
				}
				$content = $content.'</div>';
			}

			$content = $content.'</div>';
			require_once('../../modulos/phpmailer/sendMail.php');

			$mail->Subject = "Confirmación de reservación";
			$mail->AltBody = $objeto['organizacion'][0]['nombreorganizacion'];
			$mail->MsgHTML($content);
			$mail->AddAddress($objeto['correo'], $objeto['correo']);

			@$mail->Send();
		}
		return $result;
		//return array("status" => true, "registros" => array($result));
    }

    public static function mudar_comanda($objeto) {
        // Guarda la actividad
		$fecha = date('Y-m-d H:i:s');
		// Valida que exista el empleado si no agrega un cero como id
		$usuario = (!empty($_SESSION['mesero']['id'])) ? $_SESSION['mesero']['id'] : 0;
		$sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
					VALUES (''," . $usuario . ",'Muda la comanda', '" . $fecha . "')";
        //$actividad = $this -> query($sql);
        $actividad = DB::queryArray($sql, array());

		$sql = "SELECT id 
				FROM com_comandas 
				WHERE status = 0 and idmesa = ".$objeto['mesa'];
        //$idcomanda = $this -> queryArray($sql);
        $idcomanda = DB::queryArray($sql, array());
		// En caso de que exista comanda guarda el id de dicha comanda
		if(count($idcomanda["rows"]) > 0){
			$idcomanda = $idcomanda["rows"][0];
			$idcomanda = $idcomanda["id"];
			
			// Da de baja la comanda secundaria
						$sql = "UPDATE com_comandas 
								SET status = 3
								WHERE id = " . $objeto["comanda"];
                        //$this -> query($sql);
                        DB::queryArray($sql, array());

						// Busca las persoanas que tienen pedidos
						$sql = "SELECT id, npersona, idcomanda
								FROM com_pedidos 
								WHERE status != 3 AND idcomanda = $idcomanda AND idproducto != 0
								OR status != 3 AND idcomanda = " . $objeto["comanda"] ." AND idproducto != 0
								group by npersona, idcomanda";
                        //$npersonas = $this -> queryArray($sql);
                        $npersonas = DB::queryArray($sql, array());
						$npersonas = $npersonas["rows"];

						// Foreach para las personas que tienen pedidos
						foreach ($npersonas as $key2 => $value) {
							// Variable para saber que numero de persona colocarle
							$nper = $key2+1;
							// ACtualiza el numero de persona en cada pedido y cambia el idcomanda a 0 para no confundirlo dependiendo el numero de personas y el id de su comanda
							$sql = "UPDATE com_pedidos
									SET idcomanda = 0, npersona = $nper
									WHERE idcomanda = " . $value['idcomanda'] . " AND npersona = ".$value['npersona'];
                            //$this -> query($sql);
                            DB::queryArray($sql, array());
						}
						// Actualiza el id de la comanda al de la comanda principal
						$sql = "UPDATE com_pedidos
								SET idcomanda = $idcomanda
								WHERE idcomanda = 0";
                        //$this -> query($sql);
                        DB::queryArray($sql, array());

		}
		else{
		$idcomanda = $objeto['comanda'];
		// Cambia la mesa de la comanda
		$sql = "UPDATE com_comandas
				SET idmesa=".$objeto['mesa']." WHERE id=" . $objeto['comanda'];
        //$result = $this -> query($sql);
        $result = DB::queryArray($sql, array());

		// Separa las mesas borrando los registros de la tabla com_union
		

		}
		$sql = "DELETE FROM com_union WHERE idprincipal=" . $objeto['mesa_origen'];
        //$union = $this -> query($sql);
		$union = DB::queryArray($sql, array());
		
		//return $idcomanda;
		return $result;
    }
    
    public static function close_comanda($objeto) {

        // Cierra la comanda, separa las mesas(si existen), elimina la mesa(si es temporal)
        // Actualiza el inventario.
	    // Como parametros puede recibir:
		// $idComanda -> ID de la comanda
		// $bandera -> Como se debe cerrar la comanda (0 -> todo junto, 1 -> individual, 2 -> se paga en caja, 3 -> se manda a caja) 
		// $idmesa -> ID de la mesa
		// $tipo -> si es mesa temporal(para llevar, servicio a domicilio) o normal
		// $id_reservacion -> ID de la reservacion(si existe)
        // reimprime -> bandera que indica que es reimpresion de comanda
        
        $idComanda = $objeto['idComanda'];
		$bandera = $objeto['bandera'];
		$idmesa = $objeto['idmesa'];
		$tipo = $objeto['tipo'];
		$tel = $objeto['tel'];
        $rbandera = $bandera;

		if ($bandera == 2) {
            $rbandera = 0;
        }

	    // Valida que no venga de la reimpresion
		if ($objeto['reimprime'] != 1) {
		    // Borra la union de las tablas si existe
			$sql1 = "DELETE FROM com_union 
					WHERE idprincipal='$idmesa'";
            //$table = $this -> query($sql);
            $table = DB::queryArray($sql1, array());

		    // Actualiza el estatus de la comanda para marcar como cerrada
			$sql2 = "UPDATE com_comandas 
					SET status = 2, individual = '" . $rbandera . "' 
					WHERE id = " . $idComanda;
            //$status = $this -> query($sql);
            $status = DB::queryArray($sql2, array());
		}

	    // Elimina la mesa si es servicio a domicilio o para llevar
		if ($tipo == 2 || $tipo == 1) {
			// Valida que no venga de la reimpresion
			if ($objeto['reimprime'] != 1) {
				$sql = "UPDATE com_mesas
						SET status = 2
						WHERE id_mesa = " . $idmesa;
                //$elimina = $this -> query($sql);
                $elimina = DB::queryArray($sql, array());
			}
		}

		$size = 5 - strlen($idComanda);
		$string = "";

		for ($i = 0; $i < $size; $i++) {
            $string .= "0";
        }
		
	    // Filtra por persona
		$condicion .= (!empty($objeto['persona'])) ? ' AND a.npersona = '.$objeto['persona'] : '' ;
        
		if (!empty($objeto['sucursal'])) {
			$sucursal = $objeto['sucursal'];
		} else {
            //$sucursal = $this -> sucursal();
            session_start();

            if (isset($objeto['id_mesero'])) {
                $idempleado = $objeto['id_mesero'];
            } else {
                $sql = "SELECT idempleado FROM administracion_usuarios limit 1";
                $res = DB::queryArray($sql, array());
                $idempleado = $res['registros'][0]['idempleado'];
            }

            $sucursal = " SELECT DISTINCT mp.idSuc AS id FROM administracion_usuarios au 
							INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc 
                            WHERE au.idempleado = " . $idempleado . " LIMIT 1";
            $sucursal = DB::queryArray($sucursal, array());
            $sucursal = $sucursal['registros'][0]['id'];
        }

	    // Obtiene todos los productos de la comanda
		$sql = "SELECT a.npersona, SUM(a.cantidad) cantidad, b.nombre, (CASE WHEN (SELECT precio FROM app_precio_sucursal WHERE sucursal = ".$sucursal." AND producto = a.idproducto LIMIT 1) IS NULL THEN
				ROUND(b.precio, 2)ELSE ROUND((SELECT precio FROM app_precio_sucursal WHERE sucursal = ".$sucursal." AND producto = a.idproducto LIMIT 1), 2) END) AS precioventa, b.id, 
					a.opcionales, a.nota_opcional, a.nota_extra, a.nota_sin, a.adicionales, a.sin, c.tipo, c.nombre nombreu, c.domicilio, d.codigo, c.nombre AS nombre_mesa,
					d.timestamp AS fecha_comanda, a.complementos, a.id_promocion, (CASE a.id_promocion WHEN 0 THEN 'producto' ELSE a.id END) AS tipin, a.monto_desc, a.precioaux,
					(SELECT concat(if(celular,CONCAT('Cel:',celular),''),if(telefono1,CONCAT('<br>Tel:',telefono1),'')) FROM comun_cliente WHERE nombre = c.nombre LIMIT 1) teldomicilio
				FROM com_pedidos a 
				LEFT JOIN app_productos b ON b.id=a.idproducto 
				LEFT JOIN com_comandas d ON d.id=" . $idComanda . " 
				LEFT JOIN com_mesas c ON c.id_mesa = d.idmesa 
				WHERE idcomanda = " . $idComanda . " AND a.origen = 1 AND a.status != 3 AND a.dependencia_promocion = 0 AND cantidad > 0 ".$condicion. "
				GROUP BY tipin, a.npersona, a.idProducto, a.opcionales, a.adicionales, a.sin, a.tipo_desc, a.precioaux, a.complementos
				ORDER BY a.npersona ASC, a.id ASC, precioventa desc, a.id, a.tipo_desc ASC";
					//echo ($sql);
        //$productsComanda = $this -> queryArray($sql);
        $productsComanda = DB::queryArray($sql, array());
        
        $array = Array('rows', 'tipo');

		$contador = 0;
        // La comanda se cierra pagando todo junto
        
		if ($bandera == 0) {
            
			$array['tipo'][0] = 0;		
			foreach ($productsComanda['registros'] as $key => $value) {
                
				if ($value['id_promocion'] == 0) {
					/* Impuestos del producto
					============================================================================= */

					$impuestos_comanda = 0;
					$precio = $value['precioventa'];
					$objeto['id'] = $value['id'];
                    
                    //$impuestos = $this -> listar_impuestos($objeto);

                    $orden = ($objeto['formula'] == 2) ? ' ASC' : ' DESC';

                    $sql = "SELECT p.id, p.precio, i.valor, i.clave, pi.formula, i.nombre
				        FROM app_impuesto i, app_productos p 
				        LEFT JOIN app_producto_impuesto pi ON p.id = pi.id_producto 
				        WHERE p.id = " . $objeto['id'] . " AND i.id = pi.id_impuesto 
                        ORDER BY pi.id_impuesto " . $orden;
                    $impuestos = DB::queryArray($sql, array());

					if ($impuestos['total'] > 0) {
						foreach ($impuestos['rows'] as $k => $v) {
							if ($v["clave"] == 'IEPS') {
								$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
							} else {
								if ($ieps != 0) {
									$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
								} else {
									$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
								}
							}

						    // Precio e impuestos de comanda actualizados
							$precio += $producto_impuesto;
							$precio = round($precio, 2);
							$value['precioventa'] = $precio;

							$impuestos_comanda += $producto_impuesto;
						}
					}

					/* FIN Impuestos del producto
					============================================================================= */

					$items = "";
                    $costo_extra = '';
                    
                    

					// Obtiene los opcionales si existen
					if ($value['opcionales'] != "") {
						$sql = "SELECT CONCAT('Con: ',GROUP_CONCAT(nombre)) nombre 
								FROM app_productos 
								WHERE id IN(" . $value['opcionales'] . ")";
                        //$itemsProduct = $this -> query($sql);
                        $itemsProduct = DB::queryArray($sql, array());

						if ($row = $itemsProduct -> fetch_array()){
							if($value['nota_opcional'] != ''){
								$items .= "(" . $row['nombre'] . ",".$value['nota_opcional'].")";
							} else {
								$items .= "(" . $row['nombre'] . ")";
							}
						} else if($value['nota_opcional'] != '') {
							$items .= "(" . $value['nota_opcional'] . ")";
						}

					} else if($value['nota_opcional'] != '') {
						$items .= "(" . $value['nota_opcional'] . ")";
					}
                    
					// Adicionales
					$costo_extra = [];
					if (!empty($value['adicionales'])) {
						$sql = "SELECT CONCAT('Extras: ',GROUP_CONCAT(nombre)) nombre 
								FROM app_productos 
								WHERE id IN(" . $value['adicionales'] . ")";
                        //$itemsProduct = $this -> query($sql);
                        $itemsProduct = DB::queryArray($sql, array());

						if ($row = $itemsProduct -> fetch_array()) {
                            $items .= "(" . $row['nombre'] . ")";
                        }

					    // Obtiene el costo y nombre de los productos
						$sql = "SELECT nombre, ROUND(precio, 2) AS costo, id
								FROM app_productos
								WHERE id IN(" . $value['adicionales'] . ")";
                        //$costo_extra = $this -> queryArray($sql);
                        $costo_extra = DB::queryArray($sql, array());
						$costo_extra = $costo_extra['rows'];

					    /* Impuestos del producto
					    ============================================================================= */
					
						foreach ($costo_extra as $kk => $vv) {
							$precio = $vv['costo'];
							$objeto['id'] = $vv['id'];

                            //$impuestos = $this -> listar_impuestos($objeto);
                            
                            $orden = ($objeto['formula'] == 2) ? ' ASC' : ' DESC';

		                    $sql = "SELECT p.id, p.precio, i.valor, i.clave, pi.formula, i.nombre
				                FROM app_impuesto i, app_productos p 
				                LEFT JOIN app_producto_impuesto pi ON p.id = pi.id_producto 
				                WHERE p.id = " . $objeto['id'] . " AND i.id = pi.id_impuesto 
                                ORDER BY pi.id_impuesto " . $orden;
                            $impuestos = DB::queryArray($sql, array());

							if ($impuestos['total'] > 0) {
								foreach ($impuestos['rows'] as $k => $v) {
									if ($v["clave"] == 'IEPS') {
										$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
									} else {
										if ($ieps != 0) {
											$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
										} else {
											$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
										}
									}

								    // Precio e impuestos de comanda actualizados
									$precio += $producto_impuesto;
									$precio = round($precio, 2);
									$costo_extra[$kk]['costo'] = $precio;

									$impuestos_comanda += $producto_impuesto;
								}
							}
						}

					    /* FIN Impuestos del producto
					    ============================================================================= */
					}

					// Sin
					if ($value['sin'] != "") {
						$sql = "SELECT CONCAT('Sin: ',GROUP_CONCAT(nombre)) nombre 
								FROM app_productos 
								WHERE id in(" . $value['sin'] . ")";
                        //$itemsProduct = $this -> query($sql);
                        $itemsProduct = DB::queryArray($sql, array());

						if ($row = $itemsProduct -> fetch_array()){
							if($value['nota_sin'] != ''){
								$items .= "(" . $row['nombre'] . ",".$value['nota_sin'].")";
							} else {
								$items .= "(" . $row['nombre'] . ")";
							}
						} else if($value['nota_opcional'] != '') {
							$items .= "(" . $value['nota_sin'] . ")";
						}
					} else if($value['nota_opcional'] != '') {
						$items .= "(" . $value['nota_sin'] . ")";
                    }

					$costo_complementos = [];
					// Si tiene adicionales los agrega al total
					if (!empty($value['complementos'])) {
						$sql = "SELECT CONCAT('Complementos: ',GROUP_CONCAT(nombre)) nombre 
								FROM app_productos 
								WHERE id IN(" . $value['complementos'] . ")";
                        //$itemsProduct = $this -> query($sql);
                        $itemsProduct = DB::queryArray($sql, array());

						if ($row = $itemsProduct -> fetch_array()) {
                            $items .= "(" . $row['nombre'] . ")";
                        }

					    // Obtiene el costo y nombre de los productos
						$sql = "SELECT nombre, ROUND(precio, 2) AS costo, id
								FROM app_productos
								WHERE id IN(" . $value['complementos'] . ")";
                        //$costo_complementos = $this -> queryArray($sql);
                        $costo_complementos = DB::queryArray($sql, array());
						$costo_complementos = $costo_complementos['rows'];

					    /* Impuestos del producto
					    ============================================================================= */
					
						foreach ($costo_complementos as $kk => $vv) {
							$precio = $vv['costo'];
							$objeto['id'] = $vv['id'];

                            //$impuestos = $this -> listar_impuestos($objeto);
                            
                            $orden = ($objeto['formula'] == 2) ? ' ASC' : ' DESC';

		                    $sql = "SELECT p.id, p.precio, i.valor, i.clave, pi.formula, i.nombre
				                FROM app_impuesto i, app_productos p 
				                LEFT JOIN app_producto_impuesto pi ON p.id = pi.id_producto 
				                WHERE p.id = " . $objeto['id'] . " AND i.id = pi.id_impuesto 
				                ORDER BY pi.id_impuesto " . $orden;
                            
                            $impuestos = DB::queryArray($sql, array());

							if ($impuestos['total'] > 0) {
								foreach ($impuestos['rows'] as $k => $v) {
									if ($v["clave"] == 'IEPS') {
										$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
									} else {
										if ($ieps != 0) {
											$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
										} else {
											$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
										}
									}

								// Precio e impuestos de comanda actualizados
									$precio += $producto_impuesto;
									$precio = round($precio, 2);
									$costo_complementos[$kk]['costo'] = $precio;

									$impuestos_comanda += $producto_impuesto;
								}
							}
						}

					    /* FIN Impuestos del producto
					    ============================================================================= */
                    }

					$flag = 0;
					///DESC
					 if ($value['monto_desc'] != NULL) {
					 	if($value['monto_desc'] != 0){
					 		$precioventa = 0;
					 		$items .= '(Desc. '.$value['monto_desc'].'%)';
						 	$precioventa = $value['precioventa'];
						 	$precioventa = $precioventa - ($precioventa*($value['monto_desc']/100));
						 	$flag = 1;
					 	}
					 }else{
					 	$precioventa = $value['precioventa'];
					 }					
                    ///DESC FIN
				
					//////////// PRECIO MODIFICADO DESDE CAJA SE SOBRE PONE A TODO LO ANTERIOR /////////
					if($value['precioaux'] > 0){
						$precioventa = $value['precioaux'];
					}else{
						if($flag != 1){
							$precioventa = $value['precioventa'];
						}
						
					}
					//////////// PRECIO MODIFICADO DESDE CAJA SE SOBRE PONE A TODO LO ANTERIOR FIN /////
															
					// Pedido
					$array['rows'][$contador] = Array(
						'impuestos' => $impuestos_comanda, 
						'costo_extra' => $costo_extra, 
						'costo_complementos' => $costo_complementos, 
						'npersona' => $value['npersona'], 
						'cantidad' => $value['cantidad'], 
						'nombre' => $value['nombre'] . " $items", 
						'precioventa' => $precioventa, 
						'tipo' => $value['tipo'], 
						'nombreu' => $value['nombreu'], 
						'domicilio' => $value['domicilio'], 
						'codigo' => $value['codigo'], 
						'nombre_mesa' => $value['nombre_mesa'],
						'monto_desc' => $value['monto_desc'], 
						'precioUnitarito' => $value['precioventa'],
						'teldomicilio' => $value['teldomicilio']
					);

					// Siguiente pedido
                    $contador++;

				} else {
					$promocion = [];
					$promociones = [];
					$promocion = $this -> get_promocion($value['id_promocion']);
					$productsComanda['rows'][$key]['nombre'] = $promocion['nombre'];
					$productsComanda['rows'][$key]['tipo_promocion'] = $promocion['tipo'];
					$productsComanda['rows'][$key]['cantidad_to'] = $promocion['cantidad'];
					$productsComanda['rows'][$key]['cantidad_descuento'] = $promocion['cantidad_descuento'];
					$productsComanda['rows'][$key]['descuento'] = $promocion['descuento'];
					$productsComanda['rows'][$key]['precio_fijo'] = $promocion['precio_fijo'];
					$promociones = $this -> get_promociones($value['tipin'], $value['id_promocion']);
					$promociones = $promociones['rows'];					
					$precio = 0;
					$items = '';
					$extras = 0;
					if($promocion['tipo'] == 1){
						foreach ($promociones as $k => $v) {
							$extras += $v['sumaExtras']*1;
							$precio += $v['precio'];
							$promociones[$k]['precio'] = 0;
						}
						$desc = (100 - $promocion['descuento']) / 100;
						$precio = $precio * $desc;
						
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} else if($promocion['tipo'] == 2){
						foreach ($promociones as $k => $v) {
							if($k%2==0){
								$extras += $v['sumaExtras']*1;
								$precio += $v['precio'];
							}
							$promociones[$k]['precio'] = 0;
						}
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} else if($promocion['tipo'] == 4){
						$i = $extras = 0;
						foreach ($promociones as $k => $v) {
							$i++;
							$extras += $promociones[$k]['sumaExtras']; // suma extras.  
							$precio = $promocion['precio_fijo'];
							$promociones[$k]['precio'] = 0;
						}
						$precio = $precio * $i;
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
						
					} else if($promocion['tipo'] == 3){
						for ($x=0; $x < $promocion['cantidad_descuento']; $x++) { 
							$promociones[(count($promociones)-1) - $x]['precio'] = 0;
						}
						foreach ($promociones as $k => $v) {
							$extras += $v['sumaExtras']*1;
							$precio += $v['precio'];
							$promociones[$k]['precio'] = 0;
						}
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} else if($promocion['tipo'] == 5){
						foreach ($promociones as $k => $v) {
							if($v['comprar'] == 1){
								$extras += $v['sumaExtras']*1;
								$precio += $v['precio'];
							}else{
								$extras += $v['sumaExtras']*1;
							}
							$promociones[$k]['precio'] = 0;
						}
						$productsComanda['rows'][$key]['precioventa'] = $precio + $extras;
					} 

					//echo '<pre>'; print_r($promociones); exit();
					
					//$productsComanda['rows'][$key]['promociones'] = $promociones;
					//echo '<pre>'; print_r($productsComanda['rows'][$key]); exit();
					// Pedido

					$array['rows'][$contador] = Array(
						'impuestos' => '', 'costo_extra' => '', 
						'costo_complementos' => '', 
						'npersona' => $productsComanda['rows'][$key]['npersona'], 
						'cantidad' => $productsComanda['rows'][$key]['cantidad'],
						'nombre' => $productsComanda['rows'][$key]['nombre'] . " $items", 
						'precioventa' => $productsComanda['rows'][$key]['precioventa'], 
						'tipo' => $productsComanda['rows'][$key]['tipo'], 
						'nombreu' => $productsComanda['rows'][$key]['nombreu'], 
						'domicilio' => $productsComanda['rows'][$key]['domicilio'], 
						'codigo' => $productsComanda['rows'][$key]['codigo'], 
						'nombre_mesa' => $productsComanda['rows'][$key]['nombre_mesa'],
						'promociones' => $promociones,
						'precioUnitarito' => $productsComanda['rows'][$key]['precioventa'],
						'teldomicilio' => $productsComanda['rows'][$key]['teldomicilio']
					);

				// Siguiente pedido
					$contador++;
					$precio = 0;
					$items = '';
				}
				
			}
			
			

			
        }

	    // La comanda se cierra pagando individual
		if ($bandera == 1) {
			$array['tipo'][0] = 1;
			$impuestos_comanda = 0;
			$person = 0;
			$codigo = "";
            
			foreach ($productsComanda['registros'] as $value) {
                //print_r($value); exit();
				if ($value['id_promocion'] == 0) {
				    /* Impuestos del producto
				    ============================================================================= */

					$impuestos_comanda = 0;
					$precio = $value['precioventa'];
					$objeto['id'] = $value['id'];

                    //$impuestos = $this -> listar_impuestos($objeto);

                    $orden = ($objeto['formula'] == 2) ? ' ASC' : ' DESC';

                    $sql = "SELECT p.id, p.precio, i.valor, i.clave, pi.formula, i.nombre
				        FROM app_impuesto i, app_productos p 
				        LEFT JOIN app_producto_impuesto pi ON p.id = pi.id_producto 
				        WHERE p.id = " . $objeto['id'] . " AND i.id = pi.id_impuesto 
                        ORDER BY pi.id_impuesto " . $orden;
                        
                    $impuestos = DB::queryArray($sql, array());
                    
					if ($impuestos['total'] > 0) {
                        $impuestos_comanda = 0;
                        
						foreach ($impuestos['registros'] as $k => $v) {
							if ($v["clave"] == 'IEPS') {
								$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
							} else {
								if ($ieps != 0) {
									$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
								} else {
									$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
								}
							}

						    // Precio e impuestos de comanda actualizados
							$precio += $producto_impuesto;
							$precio = round($precio, 2);
							$value['precioventa'] = $precio;

							$impuestos_comanda += $producto_impuesto;
						}
					}

				    /* FIN Impuestos del producto
				    ============================================================================= */

					$items = "";
                    $costo_extra = '';

					if ($value['opcionales'] != "") {
						$sql = "SELECT CONCAT('Con: ',GROUP_CONCAT(nombre)) nombre 
								FROM app_productos 
								WHERE id IN(" . $value['opcionales'] . ")";
                        //$itemsProduct = $this -> query($sql);
                        $itemsProduct = DB::queryArray($sql, array());

						if ($row = $itemsProduct -> fetch_array())
							$items .= "(" . $row['nombre'] . ")";
                    }

				    // Si tiene adicionales los agrega al total
					if ($value['adicionales'] != "") {
						$sql = "SELECT CONCAT('Extras: ',GROUP_CONCAT(nombre)) nombre 
								FROM app_productos 
								WHERE id IN(" . $value['adicionales'] . ")";
                        //$itemsProduct = $this -> query($sql);
                        $itemsProduct = DB::queryArray($sql, array());

						if ($row = $itemsProduct -> fetch_array())
							$items .= "(" . $row['nombre'] . ")";

					    // Obtiene el costo y nombre de los productos
						$sql = "SELECT nombre, ROUND(precio, 2) AS costo, id
								FROM app_productos 
								WHERE id IN(" . $value['adicionales'] . ")";
                        //$costo_extra = $this -> queryArray($sql);
                        $costo_extra = DB::queryArray($sql, array());

						$costo_extra = $costo_extra['rows'];

					    /* Impuestos del producto
					    ============================================================================= */
					
						foreach ($costo_extra as $kk => $vv) {
							$precio = $vv['costo'];
							$objeto['id'] = $vv['id'];

							$impuestos = $this -> listar_impuestos($objeto);
							if ($impuestos['total'] > 0) {
								foreach ($impuestos['rows'] as $k => $v) {
									if ($v["clave"] == 'IEPS') {
										$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
									} else {
										if ($ieps != 0) {
											$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
										} else {
											$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
										}
									}

								// Precio e impuestos de comanda actualizados
									$precio += $producto_impuesto;
									$precio = round($precio, 2);
									$costo_extra[$kk]['costo'] = $precio;

									$impuestos_comanda += $producto_impuesto;
								}
							}
						}

					/* FIN Impuestos del producto
					============================================================================= */
                    }

					$costo_complementos = [];
				    // Si tiene adicionales los agrega al total
					if (!empty($value['complementos'])) {
						$sql = "SELECT 
									CONCAT('Complementos: ',GROUP_CONCAT(nombre)) nombre 
								FROM 	
									app_productos 
								WHERE 
									id IN(" . $value['complementos'] . ")";
						$itemsProduct = $this -> query($sql);

						if ($row = $itemsProduct -> fetch_array())
							$items .= "(" . $row['nombre'] . ")";

					// Obtiene el costo y nombre de los productos
						$sql = "SELECT 
									nombre, ROUND(precio, 2) AS costo, id
								FROM 
									app_productos
								WHERE 
									id IN(" . $value['complementos'] . ")";
						$costo_complementos = $this -> queryArray($sql);
						$costo_complementos = $costo_complementos['rows'];

					/* Impuestos del producto
					============================================================================= */
					
						foreach ($costo_complementos as $kk => $vv) {
							$precio = $vv['costo'];
							$objeto['id'] = $vv['id'];

							$impuestos = $this -> listar_impuestos($objeto);
							if ($impuestos['total'] > 0) {
								foreach ($impuestos['rows'] as $k => $v) {
									if ($v["clave"] == 'IEPS') {
										$producto_impuesto = $ieps = (($v["precio"]) * $v["valor"] / 100);
									} else {
										if ($ieps != 0) {
											$producto_impuesto = ((($v["precio"] + $ieps)) * $v["valor"] / 100);
										} else {
											$producto_impuesto = (($v["precio"]) * $v["valor"] / 100);
										}
									}

								// Precio e impuestos de comanda actualizados
									$precio += $producto_impuesto;
									$precio = round($precio, 2);
									$costo_complementos[$kk]['costo'] = $precio;

									$impuestos_comanda += $producto_impuesto;
								}
							}
						}

					/* FIN Impuestos del producto
					============================================================================= */
                    }
				
					if ($value['sin'] != "") {
						$sql = "SELECT 
									CONCAT('Sin: ',GROUP_CONCAT(nombre)) nombre 
								FROM 
									app_productos 
								WHERE 
									id IN(" . $value['sin'] . ")";
						$itemsProduct = $this -> query($sql);

						if ($row = $itemsProduct -> fetch_array())
							$items .= "(" . $row['nombre'] . ")";
					}

					if ($person != $value['npersona']) {
						$ceros = "";
                        
						if ($value['npersona'] < 10) {
                            $ceros = "0" . $value['npersona'];
                        }

						$codigo = "COM" . $string . $idComanda . "P" . $ceros;
						$person = $value['npersona'];
                    }

				    // Armamos el array que se devuelve
					$array['rows'][$person]['tipo'] = $value['tipo'];
					$array['rows'][$person]['nombre_usuario'] = $value['nombreu'];
					$array['rows'][$person]['domicilio'] = $value['domicilio'];
                    $array['rows'][$person]['codigo'] = $codigo;
					
					$flag = 0;
					///DESC
					if ($value['monto_desc'] != NULL) {
					 	if($value['monto_desc'] != 0){
					 		$precioventa = 0;
					 		$items .= '(Desc. '.$value['monto_desc'].'%)';
						 	$precioventa = $value['precioventa'];
						 	$precioventa = $precioventa - ($precioventa*($value['monto_desc']/100));
						 	$flag = 1;
					 	}
					}else{
					 	$precioventa = $value['precioventa'];
					}					
					///DESC FIN
                    
					//////////// PRECIO MODIFICADO DESDE CAJA SE SOBRE PONE A TODO LO ANTERIOR /////////
					if($value['precioaux'] > 0){
						$precioventa = $value['precioaux'];
					}else{
						if($flag != 1){
							$precioventa = $value['precioventa'];
						}
						
					}
					//////////// PRECIO MODIFICADO DESDE CAJA SE SOBRE PONE A TODO LO ANTERIOR FIN /////
				
					// Pedido
					$array['rows'][$person]['pedidos'][$contador] = Array(
							'impuestos' => $impuestos_comanda, 
							'costo_extra' => $costo_extra, 
							'costo_complementos' => $costo_complementos,
							'npersona' => $value['npersona'], 
							'cantidad' => $value['cantidad'], 
							'nombre' => $value['nombre'] . " $items", 
							'precioventa' => $precioventa, 
							'tipo' => $value['tipo'], 
							'nombreu' => $value['nombreu'], 
							'domicilio' => $value['domicilio'], 
							'codigo' => $codigo,
							'monto_desc' => $value['monto_desc'], 
							'precioUnitarito' => $value['precioventa']
					);

					// Siguiente pedido	
					$contador++;
					
				} else { 
					$promocion = [];
					$promociones = [];
					$promocion = $this -> get_promocion($value['id_promocion']);
					$value['nombre'] = $promocion['nombre'];
					$value['tipo_promocion'] = $promocion['tipo'];
					$value['cantidad_to'] = $promocion['cantidad'];
					$value['cantidad_descuento'] = $promocion['cantidad_descuento'];
					$value['descuento'] = $promocion['descuento'];
					$value['precio_fijo'] = $promocion['precio_fijo'];
					//echo "<pre>"; print_r($value); exit();
					$promociones = $this -> get_promociones($value['tipin'], $value['id_promocion']);
					$promociones = $promociones['rows'];

					$precio = 0;					

					if($promocion['tipo'] == 1){
						foreach ($promociones as $k => $v) {
							$precio += $v['precio'];
							$promociones[$k]['precio'] = 0;
						}
						$desc = (100 - $promocion['descuento']) / 100;
						$precio = $precio * $desc;
						
						$value['precioventa'] = $precio;
					} else if($promocion['tipo'] == 2){
						foreach ($promociones as $k => $v) {
							if($k%2==0){
								$precio += $v['precio'];
							}
							$promociones[$k]['precio'] = 0;
						}
						$value['precioventa'] = $precio;
					} else if($promocion['tipo'] == 4){
						foreach ($promociones as $k => $v) {
							$precio += $promocion['precio_fijo'];
							$promociones[$k]['precio'] = 0;
						}
						$value['precioventa'] = $precio;
						
					} else if($promocion['tipo'] == 3){
						for ($x=0; $x < $promocion['cantidad_descuento']; $x++) { 
							$promociones[(count($promociones)-1) - $x]['precio'] = 0;
						}
						foreach ($promociones as $k => $v) {
							$precio += $v['precio'];
							$promociones[$k]['precio'] = 0;
						}
						$value['precioventa'] = $precio;
					} else if($promocion['tipo'] == 5){
						//print_r($promociones);
						foreach ($promociones as $k => $v) {
							if($v['comprar'] == 1){
								$precio += $v['precio'];
							}
							$promociones[$k]['precio'] = 0;
						}
						$value['precioventa'] = $precio;
					} 
					if ($person != $value['npersona']) {
						$ceros = "";

						if ($value['npersona'] < 10)
							$ceros = "0" . $value['npersona'];

						$codigo = "COM" . $string . $idComanda . "P" . $ceros;
						$person = $value['npersona'];
					}

					// Armamos el array que se devuelve
					$array['rows'][$person]['tipo'] = $value['tipo'];
					$array['rows'][$person]['nombre_usuario'] = $value['nombreu'];
					$array['rows'][$person]['domicilio'] = $value['domicilio'];
					$array['rows'][$person]['codigo'] = $codigo;
					//echo '<pre>'; print_r($promociones); exit();
					
					//$value['promociones'] = $promociones;
					//echo '<pre>'; print_r($value); exit();
					// Pedido
					$array['rows'][$person]['pedidos'][$contador] = Array(
							'impuestos' => $impuestos_comanda, 'costo_extra' => $costo_extra, 
							'costo_complementos' => $costo_complementos,'npersona' => $value['npersona'], 
							'cantidad' => $value['cantidad'], 'nombre' => $value['nombre'] . " $items", 
							'precioventa' => $value['precioventa'], 'tipo' => $value['tipo'], 'nombreu' => $value['nombreu'], 
							'domicilio' => $value['domicilio'], 'codigo' => $codigo, 'promociones' => $promociones
					);

					// Siguiente pedido	
					$contador++;
					$precio = 0;					
                }
                
			}				
			
		}
        
	    // La comanda se cierra pagando en caja
		if ($bandera == 2) {
			$array['tipo'][0] = 2;
			$array['rows'][0] = Array('respuesta' => 'ok', 'comanda' => 'COM' . $string . $idComanda);
		}

	    // La comanda se manda a caja
		if ($bandera == 3) {
			$array['tipo'][0] = 3;
			$array['rows'][0] = Array('respuesta' => 'ok', 'comanda' => 'COM' . $string . $idComanda);
		}

        // Consulta si se debe de mostrar la propina o no
        
        //$sucursal = $this->sucursal();

        session_start();

        if (isset($objeto['id_mesero'])) {
            $idempleado = $objeto['id_mesero'];
        } else {
            $sql = "SELECT idempleado FROM administracion_usuarios limit 1";
            //$res = $this -> queryArray($sql);
            $res = DB::queryArray($sql, array());
			$idempleado = $res['registros'][0]['idempleado'];
        }

        $sucursal = " SELECT DISTINCT mp.idSuc AS id FROM administracion_usuarios au 
							INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc 
							WHERE au.idempleado = " . $idempleado . " LIMIT 1";
        //$sucursal = $this -> queryArray($sucursal);
        $sucursal = DB::queryArray($sucursal, array());
		$sucursal = $sucursal['registros'][0]['id'];

		$sql = "SELECT propina, tipo_operacion, mostrar_dolares
				FROM com_configuracion where id_sucursal = ".$sucursal.";";
        //$result = $this -> queryArray($sql);
        $result = DB::queryArray($sql, array());

		$array['mostrar'] = $result['registros'][0]['propina'];
		$array['mostrar_dolares'] = $result['registros'][0]['mostrar_dolares'];
		$array['tipo_operacion'] = $result['registros'][0]['tipo_operacion'];
        $array['tel'] = $tel;

	    // ** Guarda la actividad
		$fecha = date('Y-m-d H:i:s');
		// Valida que exista el empleado si no agrega un cero como id
		$usuario = (!empty($_SESSION['mesero']['id'])) ? $_SESSION['mesero']['id'] : 0;
		$sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
				VALUES (''," . $usuario . ",'Cierra comanda', '" . $fecha . "')";
        //$actividad = $this -> query($sql);
        $actividad = DB::queryArray($sql, array());

	    // Valida que no venga de la reimpresion
		if ($objeto['reimprime'] != 1) {
			// Actualiza la fecha de cierre de la comanda
			$sql = "UPDATE com_comandas
					SET fin = '" . $fecha . "'
					WHERE id = " . $idComanda;
            //$fin = $this -> query($sql);
            $fin = DB::queryArray($sql, array());
		}

		// Actualiza la reservacion
		$sql = "UPDATE com_reservaciones
				SET activo = 0
				WHERE mesa = " . $idmesa;
		//$fin = $this -> query($sql);
		$fin = DB::queryArray($sql, array());
 		
		return $array;
    }

    function listar_impuestos($objeto) {
        //return $objeto;
        $orden = ($objeto['formula'] == 2) ? ' ASC' : ' DESC';

		$sql = "SELECT p.id, p.precio, i.valor, i.clave, pi.formula, i.nombre
			FROM  app_impuesto i, app_productos p 
			LEFT JOIN app_producto_impuesto pi ON p.id = pi.id_producto 
			WHERE p.id = ".$objeto['id']." AND i.id = pi.id_impuesto 
			ORDER BY pi.id_impuesto ".$orden;
		// return $sql;
        //$result = $this -> queryArray($sql);
        $result = DB::queryArray($sql, array());

		return $result;
    }

    public static function pedir($objeto) {
        $objeto = (empty($objeto)) ? $_REQUEST : $objeto;
	
	    // Procesa los pedidos
        //$result = $this -> comandasModel -> process($objeto['id_comanda']);
        
        //$usuario = (!empty($_SESSION['mesero']['id'])) ? $_SESSION['mesero']['id'] : $_SESSION['accelog_idempleado'];
        $usuario = (!empty($objeto['id_mesero'])) ? $objeto['id_mesero'] : $_SESSION['accelog_idempleado'];
        
        $sql = "SELECT pe.*, p.tipo_producto
				FROM com_pedidos pe
				LEFT JOIN app_productos p ON p.id = pe.idproducto
				WHERE pe.status = '-1' AND pe.origen = 1 AND idcomanda =" . $objeto['id_comanda'];//$idcomanda;
        //$pedidos = $this -> queryArray($sql);
        $pedidos = DB::queryArray($sql, array());
        
        if (!empty($objeto['id_sucursal'])) { //$sucursal
			// Obtiene el almacen
			$almacen = "SELECT a.id
						FROM administracion_usuarios au
						LEFT JOIN app_almacenes a ON a.id_sucursal = au.idSuc
						WHERE au.idSuc = " . $objeto['id_sucursal'] . " AND a.activo = 1 LIMIT 1"; //$sucursal
            //$almacen = $this -> queryArray($almacen);
            $almacen = DB::queryArray($almacen, array());
            $almacen = $almacen['registros'][0]['id'];
            
		} else {
			session_start();
		// Obtiene el almacen
			$almacen = "SELECT a.id
						FROM administracion_usuarios au
						LEFT JOIN app_almacenes a ON a.id_sucursal = au.idSuc
						WHERE au.idempleado = " .$objeto['id_mesero']. " AND 
							a.activo = 1 LIMIT 1";  //$_SESSION['accelog_idempleado']
            //$almacen = $this -> queryArray($almacen);
            $almacen = DB::queryArray($almacen, array());
            $almacen = $almacen['registros'][0]['id'];
        }
        
        // Valida que exista el almacen
		$almacen = (empty($almacen)) ? 1 : $almacen;

        $fecha = date('Y-m-d H:i:s');
        
        /* Actualiza el inventario
	    =========================================================================== */

		foreach ($pedidos['rows'] as $key => $value) {
            
			// Obtiene los insumos normales
			$sql = "SELECT p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
					FROM app_productos p
					INNER JOIN app_producto_material m ON m.id_material = p.id
					WHERE m.id_producto = " . $value['idproducto'] ." AND m.opcionales LIKE '%0%'";
			// return $sql;
            //$normales = $this -> queryArray($sql);
            $normales = DB::queryArray($sql, array());
			
			// Actualiza el inventario por cada insumo
			foreach ($normales['rows'] as $k => $v) {
				$sql = "INSERT INTO
							app_inventario_movimientos
							(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
							tipo_traspaso, costo, referencia)
						VALUES
							('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
							'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id'] . "')";
                //$result_opcional = $this -> query($sql);
                $result_opcional = DB::queryArray($sql, array());
			}
			
			// Opcionales
			if (!empty($value['opcionales'])) {
			    // Filtra solo por los opcionales seleccionados
				$condicion = (!empty($value['opcionales'])) ? " AND p.id IN(" . $value['opcionales'] . ")" : "";
				
			    // Obtiene los productos
				$sql = "SELECT p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						FROM app_productos p
						INNER JOIN app_producto_material m ON m.id_material = p.id
						WHERE m.id_producto = " . $value['idproducto'] . $condicion;
				// return $sql;
                //$opcionales = $this -> queryArray($sql);
                $opcionales = DB::queryArray($sql, array());
				
				// Actualiza el inventario por cada producto
				foreach ($opcionales['rows'] as $k => $v) {
					$sql = "INSERT INTO
								app_inventario_movimientos
								(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
								tipo_traspaso, costo, referencia)
							VALUES
								('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
								'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id'] . "')";
                    //$result_opcional = $this -> query($sql);
                    $result_opcional = DB::queryArray($sql, array());
				}
			}

			// Sin
			if (!empty($value['sin'])) {
			    // Excluye los insumos sin del inventario
				$condicion = (!empty($value['sin'])) ? " AND p.id NOT IN(" . $value['sin'] . ")" : "";
				
			    // Obtiene los productos
				$sql = "SELECT p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						FROM app_productos p
						INNER JOIN app_producto_material m ON m.id_material = p.id
						WHERE m.id_producto = " . $value['idproducto'] . $condicion;
				// return $sql;
                //$sin = $this -> queryArray($sql);
                $sin = DB::queryArray($sql, array());
				
			    // Actualiza el inventario por cada producto
				foreach ($sin['rows'] as $k => $v) {
					$sql = "INSERT INTO
								app_inventario_movimientos
								(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
								tipo_traspaso, costo, referencia)
							VALUES
								('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
								'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id'] . "')";
                    //$result_opcional = $this -> query($sql);
                    $result_opcional = DB::queryArray($sql, array());
				}
			}
		
			// Extras
			if (!empty($value['adicionales'])) {
				$sql = "SELECT p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						FROM app_productos p
						INNER JOIN app_producto_material m ON m.id_material = p.id
						WHERE p.id IN(" . $value['adicionales'] . ") AND m.id_producto = " . $value['idproducto'];
                //$adicionales = $this -> queryArray($sql);
                $adicionales = DB::queryArray($sql, array());

			    // Actualiza el inventario por cada producto
				foreach ($adicionales['rows'] as $k => $v) {
					$sql = "INSERT INTO
									app_inventario_movimientos
									(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
									tipo_traspaso, costo, referencia)
								VALUES
									('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
									'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id'] . "')";
                    //$result_adicionales = $this -> query($sql);
                    $result_adicionales = DB::queryArray($sql, array());
				}
			}
		
			// Complementos
			if (!empty($value['complementos'])) {
				$sql = "SELECT p.id, c.cantidad, ROUND(p.precio, 2) AS precio, c.cantidad AS importe, p.formulaieps AS formula
						FROM com_complementos c
						LEFT JOIN app_productos p ON p.id = c.id_producto
						LEFT JOIN app_costos_proveedor pro ON pro.id_producto = p.id
						WHERE c.id_producto IN(" . $value['complementos'] . ")";
                //$complementos = $this -> queryArray($sql);
                $complementos = DB::queryArray($sql, array());

			    // Actualiza el inventario por cada producto
				foreach ($complementos['rows'] as $k => $v) {
					$sql = "INSERT INTO
									app_inventario_movimientos
									(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
									tipo_traspaso, costo, referencia)
								VALUES
									('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
									'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id'] . "')";
                    //$result_adicionales = $this -> query($sql);
                    $result_adicionales = DB::queryArray($sql, array());
				}
			}
			
			// Receta(Crea una entrada al inventario si es receta)
			if ($value['tipo_producto'] == 5) {
				$sql = "INSERT INTO
							app_inventario_movimientos
							(id_producto, cantidad, importe, id_almacen_destino, fecha, id_empleado,
							tipo_traspaso, costo, referencia)
						VALUES
							('" . $value['idproducto'] . "', '" . $value['cantidad'] . "', '" . $value['cantidad'] . "', '" . $almacen . "', '" . $fecha . "', 
							'" . $usuario . "', 1, '" . $value['cantidad'] . "', 'Pedido " . $value['id'] . "')";
                //$result_receta = $this -> query($sql);
                $result_receta = DB::queryArray($sql, array());
			}

			$value['sucursal'] = $sucursal;
			// Kit
			if ($value['tipo_producto'] == 6) {
                //$result = $this -> actualizar_inventario_kit($value);
                
                $sql = "SELECT pe.*, p.tipo_producto
				    FROM com_pedidos_kit pe
				    LEFT JOIN app_productos p ON p.id = pe.id_producto AND pe.status = -1
				    WHERE id_pedido = " . $objeto['id'];
		        // $result['pedidos']['result_opcionales'] = $sql;
                //$pedidos = $this -> queryArray($sql);
                $pedidos = DB::queryArray($sql, array());

		        if (!empty($objeto['sucursal'])) {
			        $almacen = "SELECT a.id
						FROM administracion_usuarios au
						LEFT JOIN app_almacenes a ON a.id_sucursal = au.idSuc
						WHERE au.idSuc = " . $objeto['sucursal'] . " 
						AND a.activo = 1 LIMIT 1";
                    //$almacen = $this -> queryArray($almacen);
                    $almacen = DB::queryArray($almacen, array());
			        $almacen = $almacen['rows'][0]['id'];
		        } else {
			        session_start();
		            // Obtiene el almacen
			        $almacen = "SELECT a.id
						FROM administracion_usuarios au
						LEFT JOIN app_almacenes a ON a.id_sucursal = au.idSuc
						WHERE au.idempleado = " . $_SESSION['accelog_idempleado'] . " 
						LIMIT 1";
                    //$almacen = $this -> queryArray($almacen);
                    $almacen = DB::queryArray($almacen, array());
			        $almacen = $almacen['rows'][0]['id'];
		        }
	            // Valida que exista el almacen
		        $almacen = (empty($almacen)) ? 1 : $almacen;

		        $fecha = date('Y-m-d H:i:s');

	            /* Actualiza el inventario
	            =========================================================================== */

		        foreach ($pedidos['rows'] as $key => $value) {
		            // Filtra para que no se descuenten de inventario los opcionales(Con jitomate, lechuga, etc.)
			        $condicion = (!empty($value['opcionales'])) ? " AND p.id NOT IN(" . $value['opcionales'] . ")" : "";

		            // Obtiene los productos
			        $sql = "SELECT p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
					    FROM app_productos p
					    INNER JOIN app_producto_material m ON m.id_material = p.id
					    WHERE m.id_producto = " . $value['id_producto'] . $condicion;
			        // $result[$value['id_producto']]['result_opcionales'] = $sql;
                    //$opcionales = $this -> queryArray($sql);
                    $opcionales = DB::queryArray($sql, array());

		            // Actualiza el inventario por cada producto
			        foreach ($opcionales['rows'] as $k => $v) {
				        $sql = "INSERT INTO
							app_inventario_movimientos
							(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
							tipo_traspaso, costo, referencia)
						VALUES
							('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
							'" . $_SESSION['accelog_idempleado'] . "', 0, '" . $v['importe'] . "', 
							'Kit " . $objeto['idproducto'] . "')";
                        //$result_opcional = $this -> query($sql);
                        $result_opcional = DB::queryArray($sql, array());
			        }

			        if (!empty($value['extras'])) {
				        $sql = "SELECT p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						    FROM app_productos p
						    INNER JOIN app_producto_material m ON m.id_material = p.id
						    WHERE p.id IN(" . $value['extras'] . ") AND m.id_producto = " . $value['id_producto'];
			            // $result[$value['id_producto']]['extras'] = $sql;
                        //$adicionales = $this -> queryArray($sql);
                        $adicionales = DB::queryArray($sql, array());

			            // Actualiza el inventario por cada producto
				        foreach ($adicionales['rows'] as $k => $v) {
					        $sql = "INSERT INTO
								app_inventario_movimientos
									(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
									tipo_traspaso, costo, referencia)
							VALUES
								('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
								'" . $_SESSION['accelog_idempleado'] . "', 0, '" . $v['importe'] . "', 
								'Kit " . $objeto['idproducto'] . "')";
                            //$result_adicionales = $this -> query($sql);
                            $result_adicionales = DB::queryArray($sql, array());
				        }
			        }
		        } //FIN foreach
		
	            // Actuliza el estado del pedido para indicar que se dio de alta (status='0')
		        $sql = "UPDATE 
					com_pedidos_kit 
				SET 
					status = '0' 
				WHERE 
					status = '-1' 
				AND 
					id_pedido = " . $objeto['id'];
                //$result = $this -> query($sql);
                $result = DB::queryArray($sql, array());
		
		        return $result;
			}

			// Combo
			if ($value['tipo_producto'] == 7) {
                //$result = $this -> actualizar_inventario_combo($value);
                
                $sql = "SELECT pe.*, p.tipo_producto, p.departamento, (1 * ROUND(p.precio, 2)) AS importe, p.precio
				    FROM com_pedidos_combo pe
				    LEFT JOIN app_productos p ON p.id = pe.id_producto
				    WHERE id_pedido = " . $objeto['id']." AND pe.status = -1";
		        $result['pedidos']['result_opcionales'] = $sql;
                //$pedidos = $this -> queryArray($sql);
                $pedidos = DB::queryArray($sql, array());
		        if (!empty($objeto['sucursal'])) {
			        $almacen = "SELECT a.id
						FROM administracion_usuarios au
						LEFT JOIN app_almacenes a ON a.id_sucursal = au.idSuc
						WHERE au.idSuc = " . $objeto['sucursal'] . " AND a.activo = 1
						LIMIT 1";
                    //$almacen = $this -> queryArray($almacen);
                    $almacen = DB::queryArray($almacen, array());
			        $almacen = $almacen['rows'][0]['id'];
		        } else {
		            session_start();
	                // Valida que exista el empleado si no agrega un cero como id
				    $usuario = (!empty($_SESSION['mesero']['id'])) ? $_SESSION['mesero']['id'] : 0;
			        // Obtiene el almacen
				    $almacen = "SELECT a.id
							FROM administracion_usuarios au
							LEFT JOIN app_almacenes a ON a.id_sucursal = au.idSuc
							WHERE au.idempleado = " . $_SESSION['accelog_idempleado'] . " 
							LIMIT 1";
                    //$almacen = $this -> queryArray($almacen);
                    $almacen = DB::queryArray($almacen, array());
				    $almacen = $almacen['rows'][0]['id'];
		        }
	            // Valida que exista el almacen
		        $almacen = (empty($almacen)) ? 1 : $almacen;

		        $fecha = date('Y-m-d H:i:s');

		        foreach ($pedidos['rows'] as $key => $value) {
			        /// el costo es 0 ya que se calculo en el pedido principal -- se podria insertar el costo que viene de la tabla com_pedidos_combo pero se debe omitir el costo del combo
			        $sql = "INSERT INTO 
						com_pedidos 
							(id, idcomanda, idproducto, cantidad, npersona, tipo, status, opcionales, adicionales,
							sin, nota_opcional, nota_extra, nota_sin, origen, costo, complementos) 
					VALUES	
						(null, ".$value['id_comanda'].", ".$value['id_producto'].", '".$value['cantidad_pedidos']."', ".$value['persona'].", '".$value['departamento']."', 
						'0', '".$value['opcionales']."', '".$value['extras']."', '".$value['sin']."', '".$value['nota_opcional']."',
						'".$value['nota_extra']."', '".$value['nota_sin']."', 2, 0, '".$value['complementos']."')";
                    //$product = $this -> query($sql);
                    $product = DB::queryArray($sql, array());
			
		            /* Actualiza el inventario
		            =========================================================================== */
		
			        $sql = "INSERT INTO
						app_inventario_movimientos
						(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
						tipo_traspaso, costo, referencia)
					VALUES
						('" . $value['id_producto'] . "', '1', '" . $value['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
						'" . $usuario . "', 0, '" . $value['precio'] . "', 'Pedido " . $value['id'] . "')";
                    //$result_inventario = $this -> query($sql);
                    $result_inventario = DB::queryArray($sql, array());
			
		            // Opcionales
			        if (!empty($value['opcionales'])) {
			            // Filtra solo por los opcionales seleccionados
				        $condicion = (!empty($value['opcionales'])) ? " AND p.id IN(" . $value['opcionales'] . ")" : "";
				
			            // Obtiene los productos
				        $sql = "SELECT
							p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						FROM
							app_productos p
						INNER JOIN
								app_producto_material m
							ON
								m.id_material = p.id
						WHERE
							m.id_producto = " . $value['id_producto'] . $condicion;
				        // return $sql;
                        //$opcionales = $this -> queryArray($sql);
                        $opcionales = DB::queryArray($sql, array());
				
			            // Actualiza el inventario por cada producto
				        foreach ($opcionales['rows'] as $k => $v) {
					        $sql = "INSERT INTO
								app_inventario_movimientos
								(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
								tipo_traspaso, costo, referencia)
							VALUES
								('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
								'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id'] . "')";
                            //$result_opcional = $this -> query($sql);
                            $result_opcional = DB::queryArray($sql, array());
				        }
			        }

		            // Sin
			        if (!empty($value['sin'])) {
			            // Excluye los insumos sin del inventario
				        $condicion = (!empty($value['sin'])) ? " AND p.id NOT IN(" . $value['sin'] . ")" : "";
				
			            // Obtiene los productos
				        $sql = "SELECT
							p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						FROM
							app_productos p
						INNER JOIN
								app_producto_material m
							ON
								m.id_material = p.id
						WHERE
							m.id_producto = " . $value['id_producto'] . $condicion;
				        // return $sql;
                        //$sin = $this -> queryArray($sql);
                        $sin = DB::queryArray($sql, array());
				
			            // Actualiza el inventario por cada producto
				        foreach ($sin['rows'] as $k => $v) {
					        $sql = "INSERT INTO
								app_inventario_movimientos
								(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
								tipo_traspaso, costo, referencia)
							VALUES
								('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
								'" . $usuario . "', 0, '" . $v['importe'] . "', 'Pedido " . $value['id']  . "')";
                            //$result_opcional = $this -> query($sql);
                            $result_opcional = DB::queryArray($sql, array());
				        }
			        }
			
		            // Extras
			        if (!empty($value['extras'])) {
				        $sql = "SELECT
							p.id, m.cantidad, ROUND(p.precio, 2) AS precio, m.cantidad AS importe, p.formulaieps AS formula
						FROM
							app_productos p
						INNER JOIN
								app_producto_material m
							ON
								m.id_material = p.id
						WHERE
							p.id IN(" . $value['extras'] . ")
						AND
							m.id_producto = " . $value['id_producto'];
			            // $result[$value['id_producto']]['extras'] = $sql;
                        //$adicionales = $this -> queryArray($sql);
                        $adicionales = DB::queryArray($sql, array());

			            // Actualiza el inventario por cada producto
				        foreach ($adicionales['rows'] as $k => $v) {
					        $sql = "INSERT INTO
								app_inventario_movimientos
									(id_producto, cantidad, importe, id_almacen_origen, fecha, id_empleado,
									tipo_traspaso, costo, referencia)
							VALUES
								('" . $v['id'] . "', '" . $v['cantidad'] . "', '" . $v['importe'] . "', '" . $almacen . "', '" . $fecha . "', 
								'" . $_SESSION['accelog_idempleado'] . "', 0, '" . $v['importe'] . "', 
								'Combo " . $objeto['idproducto'] . "')";
                            //$result_adicionales = $this -> query($sql);
                            $result_adicionales = DB::queryArray($sql, array());
				        }
			        }
			
		            /* FIN Actualiza el inventario
		            =========================================================================== */
					
		        } //FIN foreach
	
	            // Actuliza el estado del pedido para indicar que se dio de alta (status='0')
		        $sql = "UPDATE 
					com_pedidos_combo 
				SET 
					status = '0' 
				WHERE 
					status = '-1' 
				AND 
					id_pedido = " . $objeto['id'];
                //$result = $this -> query($sql);
                $result = DB::queryArray($sql, array());
		
		        //return $result;
			}

		} //FIN foreach

	    /* FIN Actualiza el inventario
	    =========================================================================== */

	    //** Guarda la actividad
		$fecha = date('Y-m-d H:i:s');
	    // Valida que exista el empleado si no agrega un cero como id
        //$usuario = (!empty($_SESSION['mesero']['id'])) ? $_SESSION['mesero']['id'] : 0;
        $usuario = (!empty($objeto['id_mesero'])) ? $objeto['id_mesero'] : 0;
		$sql = "INSERT INTO com_actividades (id, empleado, accion, fecha)
				VALUES (''," . $usuario . ",'Procesa pedidos', '" . $fecha . "')";
        //$actividad = $this -> query($sql);
        $actividad = DB::queryArray($sql, array());

	    // Actuliza el estado del pedido para indicar que se dio de alta (status='0')
		$sql = "UPDATE com_pedidos 
				SET status = '0' 
				WHERE status = '-1' AND idcomanda = " . $objeto['id_comanda']; //$idcomanda
        //$result = $this -> query($sql);
        $result = DB::queryArray($sql, array());
	
	    // Consulta el tipo de operacion y lo devuelve
        //$sucursal = $this->sucursal();
        
        // Obtiene la sucursal
		session_start();

		if(isset($_SESSION['accelog_idempleado'])){
            $idempleado = $_SESSION['accelog_idempleado'];
		}else{				
			$sql = "SELECT idempleado FROM administracion_usuarios limit 1";
            //$res = $this -> queryArray($sql);
            $res = DB::queryArray($sql, array());
            $idempleado = $res['registros'][0]['idempleado'];
		}
			
		$sucursal = " SELECT DISTINCT mp.idSuc AS id FROM administracion_usuarios au 
							INNER JOIN mrp_sucursal mp ON mp.idSuc = au.idSuc 
							WHERE au.idempleado = " . $objeto['id_mesero'] . " LIMIT 1"; //idempleado
        //$sucursal = $this -> queryArray($sucursal);
        $sucursal = DB::queryArray($sucursal, array());
        $sucursal = $sucursal['registros'][0]['id'];
        

		$sql = "SELECT tipo_operacion
				FROM com_configuracion WHERE id_sucursal = ".$objeto['id_sucursal'].";";  //sucursal
        //$result = $this -> queryArray($sql);
        $result = DB::queryArray($sql, array());
        //$result = $result['rows'][0];
        //return $objeto;

		return $result;
		
		//echo json_encode($result);
    }

    public function asignar_mesa($objeto) {
        $objeto = (empty($objeto)) ? $_REQUEST : $objeto;

        $sql = "UPDATE com_mesas
				SET idempleado = " . $objeto['empleado'] . "
				WHERE id_mesa=" . $objeto['mesa'];
		// return $sql;
        //$result = $this -> query($sql);
        $result = DB::queryArray($sql, array());

		//return array("status" => true, "registros" => array(array()));
		//array('status' => true, 'registros'=> $_REQUEST)
		return $result;
		//return array('status' => true, 'registros'=> $result);
	}
	
	public static function guardar_pedido($objeto) {

	}
}

?>
