<?php

//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class ProveedoresModel extends Connection {
	public function indexGrid() {
		$myQuery = "SELECT * from mrp_proveedor;";
		$resultados = $this->queryArray($myQuery);
		return array('proveedores' =>$resultados['rows'] ,'total' => $resultados['total'] );
	}

	public function paises(){
		$query = 'SELECT * from paises where idpais IN (1,43, 47,54,85);';
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function estados(){
		$query = 'Select * from estados';
		$result = $this->queryArray($query);
		return $result['rows'];
	}
	public function estados2($idPais){
		$query = 'Select * from estados where idpais = '.$idPais;
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function munici(){
		$queryM = "SELECT * from municipios";
		$result = $this->queryArray($queryM);
		return $result['rows'];
	}

	public function municipios($idEstado){
		$queryM = "SELECT * from municipios where idestado=".$idEstado;
		$result = $this->queryArray($queryM);
		return $result['rows'];
	}

	public function listaPrecios(){
		$query = 'SELECT * from app_lista_precio where activo=1';
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function moneda(){
		$query = "SELECT * from cont_coin";
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function creditos(){
		$queryCre = "SELECT * from app_tipo_credito where activo=1";
		$rescredi = $this->queryArray($queryCre);
		return $rescredi['rows'];
	}

	public function clasificadoresTipos(){
		//$queryClas = "SELECT * FROM app_clasificadores where padre = 4 and activo=1";
		$queryClas = "SELECT * FROM app_clasificadores where tipo = 2 and  padre > 0 and activo=1;";
		$resClas= $this->queryArray($queryClas);
		return $resClas['rows'];
	}

	public function obtenEmple(){
		$query = "SELECT  * from nomi_empleados";
		$result = $this->queryArray($query);
		return array("empleados" => $result['rows']);
	}

	public function bancos(){
		$query = "SELECT * from cont_bancos";
		$resBan = $this->queryArray($query);
		return $resBan['rows'];
	}

	public function cuentas(){
		$query = ' SELECT account_id, manual_code, description FROM cont_accounts where main_account = 3 AND removed=0 AND main_father = (SELECT CuentaClientes FROM cont_config) ORDER BY manual_code ASC;';
		$resCu = $this->queryArray($query);
		return $resCu['rows'];
	}
	
	public function datosProveedor($idProveedor){
		$query = 'SELECT * FROM mrp_proveedor WHERE idPrv='.$idProveedor;
		$result = $this->queryArray($query);

		$idTmp = $result['rows'][0]['idpais'];
		$sql = "SELECT  pais
				FROM    paises
				WHERE   idpais = $idTmp";
		$res = $this->queryArray($sql);
		$result['rows'][0]['descPais'] = $res['rows'][0]['pais'];

		$idTmp = $result['rows'][0]['idestado'];
		$sql = "SELECT  estado
				FROM    estados
				WHERE   idestado = $idTmp";
		$res = $this->queryArray($sql);
		$result['rows'][0]['descEstado'] = $res['rows'][0]['estado'];

		$idTmp = $result['rows'][0]['idmunicipio'];
		$sql = "SELECT  municipio
				FROM    municipios
				WHERE   idmunicipio = $idTmp";
		$res = $this->queryArray($sql);
		$result['rows'][0]['descMunicipio'] = $res['rows'][0]['municipio'];

		$query2 = 'SELECT bp.id, bp.idbanco, bp.numCT, ba.nombre FROM cont_bancosPrv bp
					LEFT JOIN cont_bancos ba on ba.idbanco = bp.idbanco 
					WHERE bp.idPrv='.$idProveedor;
		$result2 = $this->queryArray($query2);

		$query3 = 'SELECT * FROM pos_contactos 
					WHERE idPrv='.$idProveedor;
		$result3 = $this->queryArray($query3);

		$query8 = 'SELECT * from comun_facturacion where nombre="'.$idProveedor.'" and cliPro=2';
		$res8 = $this->queryArray($query8);

		return array("basicos" => $result['rows'], "bancos" => $result2['rows'], "contactos" => $result3['rows'], 'fact' => $res8['rows']);
	}

	// datos fiscales
	public function tipoProveedor() {
		$query = 'SELECT * FROM tipo_proveedor;';
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function cuentap(){
		$query = "SELECT account_id, CONCAT(manual_code, ' ',description) nombre_cuenta ,account_type,account_nature FROM cont_accounts ca WHERE affectable =1 AND main_father = (SELECT CuentaProveedores FROM cont_config) AND removed = 0 ORDER BY manual_code ASC;";
		return $this->query($query);
	}

	public function cuentaCliente(){
		$query = "SELECT co.account_id, CONCAT(manual_code, ' ',description) nombre_cuenta
			 FROM cont_accounts co
			 WHERE co.status=1 
			 AND co.removed=0 
			 AND co.affectable=1 AND main_father = (SELECT CuentaClientes FROM cont_config) ORDER BY manual_code ASC;";
		$result = $this->queryArray($query);
		return $result['rows'];
	}
	
	public function tipoTercero(){
		$query = "SELECT * from cont_tipo_tercero;";
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function tipoIva(){
		$query = "SELECT * FROM cont_tipo_iva;";
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function tasas($idProveedor,$idtasaAsumir){
		$query = "SELECT * FROM cont_tasaPrv WHERE idPrv = ".$idProveedor.";";
		$result = $this->queryArray($query);

		$query1 = "SELECT * FROM cont_tasaPrv WHERE id = ".$idtasaAsumir.";";
		$result1 = $this->queryArray($query1);

		return array("tasas" => $result['rows'], "tasasAsumir" => $result1['rows']);
	}
	
	public function tipoOpercaion(){
		$query = 'SELECT o.id, o.tipoOperacion FROM cont_tipo_operacion as o;';
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function borraProve($id){

		$query = 'UPDATE mrp_proveedor SET status = 0 WHERE idPrv='.$id.';';
		$result = $this->queryArray($query);
		return $result['status'];

	}

		public function activaProve($id){

		$query = 'UPDATE mrp_proveedor SET status = -1 WHERE idPrv='.$id.';';
		$result = $this->queryArray($query);
		return $result['status'];


	}

	public function borraContactoProve($id){
		$query = 'DELETE FROM pos_contactos WHERE idCont='.$id.';';
		$result = $this->queryArray($query);
		return $result['status'];
	}

	public function tipoOpercaion2($tipoTercero){
		$query = 'SELECT o.id, o.tipoOperacion 
			FROM cont_tipo_operacion as o
			INNER JOIN cont_relacion_ter_oper as rel on o.id=rel.idtipoperacion
			INNER JOIN cont_tipo_tercero as ter on  rel.idtipotercero= ter.id
			AND ter.id= '.$tipoTercero.';';
		$result = $this->queryArray($query);
		return $result['rows'];
	}

	public function saveProvedor($idProveedor,$codigo,$tipoClas,$razon_social,$rfc,$nombre_comercial,$calle,$no_ext,$no_int,$colonia,$cp,$pais,$estado,$municipios,$nombre_contacto,$email,$telefono,$web,$stringCont,$diasCredito,$saldo,$limiteCredito,$tipo,$cuenta,$beneficiario,$cuentaCliente,$tipoTercero,$tipoTerceroOperacion,$numidfiscal,$nombrextranjero,$nacionalidad,$ivaretenido,$isretenido,$idtipoiva,$tasa,$tasas,$stringBanco,$aux,$ciudad,$tasaAsumir,$minimoPieza,$minimoImportePedido,$lugarEntrega,$prepolizas_provision,$prepolizas_pago,$cuentas_gastos,$rfcFac,$razonSocialF,$emailFacturacion) {
		//echo $lugarEntrega;
		if($aux == 1){//save
			$queryProvedores = "INSERT INTO mrp_proveedor (codigo,razon_social,rfc,telefono,email,web,diascredito,idpais,idestado,idmunicipio,idtipotercero,idtipoperacion,cuenta,numidfiscal,nombrextranjero,nacionalidad,ivaretenido,isretenido,idTasaPrvasumir,idtipoiva,idtipo,beneficiario_pagador,cuentacliente,nombre,nombre_comercial,clasificacion,limite_credito,status,calle,no_ext,no_int,cp,saldo,colonia,ciudad,minimo_piezas,minimo_importe_pedido,lugar_entrega,id_prepoliza,id_prepoliza_pagos,id_cuenta_gasto) values ('".$codigo."','".$razon_social."','".$rfc."','".$telefono."','".$email."','".$web."','".$diasCredito."','".$pais."','".$estado."','".$municipios."','".$tipoTercero."','".$tipoTerceroOperacion."','".$cuenta."','".$numidfiscal."','".$nombrextranjero."','".$nacionalidad."','".$ivaretenido."','".$isretenido."','0','".$idtipoiva."','".$tipo."','".$beneficiario."','".$cuentaCliente."','".$nombre_contacto."','".$nombre_comercial."','".$tipoClas."','".$limiteCredito."','-1','".$calle."','".$no_ext."','".$no_int."','".$cp."','".$saldo."','".$colonia."','".$ciudad."','".$minimoPieza."','".$minimoImportePedido."','".$lugarEntrega."',".$prepolizas_provision.",".$prepolizas_pago.",".$cuentas_gastos.")";
			$insertProveedores = $this->queryArray($queryProvedores);
			$idProveedoresInsert = $insertProveedores['insertId'];
			if($rfcFac!=''){
				$qi = 'INSERT into comun_facturacion(nombre,rfc,razon_social,correo,pais,estado,municipio,cliPro) values("'.$idProveedoresInsert.'","'.$rfcFac.'","'.$razonSocialF.'","'.$emailFacturacion.'","'.$pais.'","'.$estado.'","'.$municipios.'","2")';
				$this->queryArray($qi);
			}
		}

		if($aux == 2){//edit
			$queryE = "UPDATE mrp_proveedor SET idPrv = '".$idProveedor."', codigo = '".$codigo."', razon_social = '".$razon_social."', rfc = '".$rfc."', telefono = '".$telefono."', email = '".$email."', web = '".$web."', diascredito = '".$diasCredito."', idpais = '".$pais."', idestado = '".$estado."', idmunicipio = '".$municipios."', idtipotercero = '".$tipoTercero."', idtipoperacion = '".$tipoTerceroOperacion."', cuenta = '".$cuenta."', numidfiscal = '".$numidfiscal."', nombrextranjero = '".$nombrextranjero."', nacionalidad = '".$nacionalidad."', ivaretenido = '".$ivaretenido."', isretenido = '".$isretenido."', idtipoiva = '".$idtipoiva."', idtipo = '".$tipo."', beneficiario_pagador = '".$beneficiario."', cuentacliente = '".$cuentaCliente."', nombre = '".$nombre_contacto."', nombre_comercial = '".$nombre_comercial."', clasificacion = '".$tipoClas."', limite_credito = '".$limiteCredito."', calle = '".$calle."', no_ext = '".$no_ext."', no_int = '".$no_int."', cp = '".$cp."', saldo = '".$saldo."', colonia = '".$colonia."', ciudad = '".$ciudad."', minimo_piezas='".$minimoPieza."', minimo_importe_pedido='".$minimoImportePedido."', lugar_entrega='".$lugarEntrega."', id_prepoliza=".$prepolizas_provision.", id_prepoliza_pagos=".$prepolizas_pago.", id_cuenta_gasto=".$cuentas_gastos." WHERE idPrv = '".$idProveedor."';";
			$resultE = $this->queryArray($queryE);
			$idProveedoresInsert = $idProveedor;

			$sel = "SELECT * from comun_facturacion where nombre='".$idProveedor."'";
			$resSel1 = $this->queryArray($sel);
			 if($resSel1['total'] > 0){
				if($rfcFac!=''){
					$qi = 'UPDATE comun_facturacion set rfc="'.$rfcFac.'", razon_social="'.$razonSocialF.'", correo="'.$emailFacturacion.'", pais="'.$pais.'", estado="'.$estado.'", municipio="'.$municipios.'" where nombre="'.$idProveedoresInsert.'" and cliPro=2';
					$this->queryArray($qi);
				}
			 }else{
				if($rfcFac!=''){
					$qi = 'INSERT into comun_facturacion(nombre,rfc,razon_social,correo,pais,estado,municipio,cliPro) values("'.$idProveedoresInsert.'","'.$rfcFac.'","'.$razonSocialF.'","'.$emailFacturacion.'","'.$pais.'","'.$estado.'","'.$municipios.'","2")';
					$this->queryArray($qi);
				}
			 }
		}

		//tasas
			$limpiatasa = $this->queryArray("DELETE FROM cont_tasaPrv WHERE idPrv = ".$idProveedoresInsert." AND id NOT IN (SELECT tasa FROM cont_rel_pol_prov WHERE activo = 1);");
			$tasas = str_replace(' ', '', $tasas);
			$arrayTasas = explode(",", $tasas);
			$arra = array_reverse($arrayTasas);
			unset($arra[0]); 
			//print_r($arra);

			// PARA GUARDAR LA TASA A ASUMIR
			$queryA = "INSERT INTO cont_tasaPrv (id, idPrv, tasa, valor, visible)
						SELECT * FROM (SELECT 0 AS id, '".$idProveedoresInsert."', '".$tasa."','".$tasaAsumir."',1 as vis) AS tmp
						WHERE NOT EXISTS (SELECT idPrv FROM cont_tasaPrv WHERE idPrv = $idProveedoresInsert AND tasa = '$tasa') LIMIT 1;";
			$resultA = $this->queryArray($queryA);
			// PARA GUARDAR LA TASA A ASUMIR FIN

			foreach ($arra as $key => $value) {
				$arrayTasas2 = explode("-", $value);
				$valor = $arrayTasas2[0];
				$tasa2 = $arrayTasas2[1];
				$queryT = "INSERT INTO cont_tasaPrv (id, idPrv, tasa, valor, visible)
						   SELECT * FROM (SELECT 0 AS id, '".$idProveedoresInsert."', '".$tasa2."','".$valor."',1 as vis) AS tmp
						   WHERE NOT EXISTS (SELECT idPrv FROM cont_tasaPrv WHERE idPrv = $idProveedoresInsert AND tasa = '$tasa2') LIMIT 1;";

				$result = $this->queryArray($queryT);
			}
		//tasas fin

		//update tasa a asumir       
			// consultar tabla para sacar el id con idprv y $tasa
			$query = 'SELECT id FROM cont_tasaPrv where idPrv = '.$idProveedoresInsert.' and tasa = "'.$tasa.'";';
			$result = $this->queryArray($query);
			$idtasaAsumirR = $result['rows'][0]['id'];

			//update tasa a asumir
			$queryU = "UPDATE mrp_proveedor SET idTasaPrvasumir = ".$idtasaAsumirR."  WHERE idPrv=".$idProveedoresInsert.";";
			$resultU = $this->queryArray($queryU);			
		//update tasa a asumir fin 
		

		//tabla contactos proveedor
			echo $stringCont;

			$arraystringCont = explode("#", $stringCont);
			$arraystringContR = array_reverse($arraystringCont);
			unset($arraystringContR[0]); 
			$arraystringCont = array_reverse($arraystringContR);
			foreach ($arraystringCont as $key => $value1) {
				$arrayCont = explode("-", $value1);
				$nombre = $arrayCont[1];
				$cargo = $arrayCont[2];
				$email = $arrayCont[3];
				$telefonoC = $arrayCont[4];
				$celularC = $arrayCont[5];

				$queryC = "INSERT INTO pos_contactos (nombre, cargo, email, telefono, celular, idPrv) VALUES ('".$nombre."','".$cargo."','".$email."','".$telefonoC."','".$celularC."','".$idProveedoresInsert."');";
				$resultC = $this->queryArray($queryC);
			}
		//tabla contactos proveedor fin

		//tabla bancos proveedor      
			$sql = "DELETE FROM cont_bancosPrv
					WHERE idPrv='$idProveedoresInsert';";
			$this->queryArray($sql);
			
			$arraystringBanc = explode("#", $stringBanco);
			$arraystringBancR = array_reverse($arraystringBanc);
			unset($arraystringBancR[0]);
			$arraystringBanc = array_reverse($arraystringBancR); 
			foreach ($arraystringBanc as $key => $value2) {
				$arrayBanc = explode("-", $value2);
				$idbanco = $arrayBanc[1];
				$numCT = $arrayBanc[2];

				$queryB = "INSERT INTO cont_bancosPrv (idbanco, idPrv, numCT) VALUES ('".$idbanco."','".$idProveedoresInsert."','".$numCT."');";
				$resultB = $this->queryArray($queryB);
			}
		//tabla bancos proveedor fin
		return 0;
	}

	public function saldoProv($id) {
		$queryS = "SELECT (IFNULL(
					(SELECT SUM(IFNULL(r.imp_factura * IF(rq.tipo_cambio = 0, 1, rq.tipo_cambio), 0)) as saldo 
					FROM app_recepcion_xml r INNER JOIN
						app_ocompra c ON c.id = r.id_oc INNER JOIN
						app_requisiciones rq ON rq.id = c.id_requisicion
					WHERE c.id_proveedor = ".$id." AND xmlfile != ''), 0) - (
				IFNULL(
					(SELECT SUM(IFNULL(
						(SELECT SUM(rp.abono * p.tipo_cambio) 
						FROM app_pagos_relacion rp INNER JOIN
							app_pagos p ON p.id = rp.id_pago
						WHERE rp.id_documento = c.id AND rp.id_tipo = 1 AND p.cobrar_pagar = 1), 0)) AS saldo
					FROM app_ocompra c
					WHERE c.id_proveedor = ".$id."),0 ))) + 
			(SELECT (IFNULL(SUM(p.cargo * p.tipo_cambio), 0) - 
				IFNULL(SUM(IFNULL(
					(SELECT SUM(pr.abono * pa.tipo_cambio)
					FROM app_pagos_relacion pr INNER JOIN
						app_pagos pa ON pa.id = pr.id_pago
					WHERE pr.id_tipo = 0 AND pr.id_documento = p.id AND pa.cobrar_pagar = 1), 0)), 0)) AS saldo
				FROM app_pagos p
				WHERE p.id_prov_cli = ".$id." AND p.cobrar_pagar = 1 and p.cargo > 0) AS saldoGral ";
		$resultS = $this->queryArray($queryS);
		return $resultS['rows'];
	}

	function existeProveedorPortal($correoportal,$userportal,$passportal){
		$sql = "SELECT nombreusuario, clave from administracion_usuarios WHERE nombreusuario='$userportal';";
		$res = $this->queryArray($sql);
		return $res;
	}

	function fencripta($pwd, $salt) {
		$resultado = crypt($pwd, $salt);
		return $resultado;
	}

	function guardarUsuarioPortal2($correoportal,$userportal,$passportal,$nombre){
		
		//2407 menu portal proveedores en configuracion

		session_start();
		$acceperfil= $_SESSION['accelog_idperfil'];// 5        
		$accelogV = $_SESSION['accelog_variable'];
		$accelogV = $_SESSION['accelog_variable'];
		$idorg= $_SESSION["accelog_idorganizacion"];


		$accelog_salt = "$2a$07$".$accelogV."aaaaaaa$";
		$calve = $this->fencripta($passportal, $accelog_salt);

		$sql = "SELECT idperfil from accelog_perfiles WHERE nombre='PORTALPROVEEDOR';";
		$res = $this->queryArray($sql);

		if($res['total']>0){
			$idperfil=$res['rows'][0]['idperfil'];
			$sqlx = "SELECT * from accelog_perfiles_me WHERE idperfil='$idperfil' AND idmenu='2407';";
			$resx = $this->queryArray($sqlx);
			if($resx['total']==0){
				$sql = "INSERT INTO accelog_perfiles_me (idperfil, idmenu) values ('$idperfil','2407')";
				$this->query($sql);
			}            
		}else{
			$sql = "INSERT INTO accelog_perfiles (nombre, visible) values ('PORTALPROVEEDOR','-1')";
			$idperfil = $this->insert_id($sql);
			 $sql = "INSERT INTO accelog_perfiles_me (idperfil, idmenu) values ('$idperfil','2407')";
			$this->query($sql);
		}


		$sql = "INSERT INTO empleados (nombre, apellido1, apellido2, idorganizacion, visible, administrador) values ('$nombre', '----', '----', '$idorg', '-1', 0)";
		$id_empleado = $this->insert_id($sql);

		$sql = "INSERT INTO accelog_usuarios (idempleado, usuario, clave, css) values ('$id_empleado', '$userportal', '$calve', 'default')";
		$this->query($sql);

		$sql = "INSERT INTO administracion_usuarios2 (nombre, apellidos, nombreusuario, clave, confirmaclave, correoelectronico, foto, idperfil, idempleado,  idSuc) values ('$nombre', '', '$userportal', '$passportal', '$passportal', '$correoportal', '', '$idperfil', '$id_empleado',  NULL)";
		$this->query($sql);

		$sql = "INSERT INTO accelog_usuarios_per (idperfil, idempleado) values ('$idperfil', '$id_empleado')";
		$this->query($sql);

	}

	function enviaCorreoPortal($correoportal,$userportal,$passportal,$nombre){

		$sql = "SELECT idperfil from accelog_perfiles WHERE nombre='PORTALPROVEEDOR';";
		$res = $this->queryArray($sql);

		if($res['total']>0){
			$idperfil=$res['rows'][0]['idperfil'];
			$sqlx = "SELECT * from accelog_perfiles_me WHERE idperfil='$idperfil' AND idmenu='2407';";
			$resx = $this->queryArray($sqlx);
			if($resx['total']==0){
				$sql = "INSERT INTO accelog_perfiles_me (idperfil, idmenu) values ('$idperfil','2407')";
				$this->query($sql);
			}            
		}else{
			$sql = "INSERT INTO accelog_perfiles (nombre, visible) values ('PORTALPROVEEDOR','-1')";
			$idperfil = $this->insert_id($sql);
			 $sql = "INSERT INTO accelog_perfiles_me (idperfil, idmenu) values ('$idperfil','2407')";
			$this->query($sql);
		}


		session_start();
		$nombre_inst=$_SESSION["accelog_nombre_instancia"];


		$h='<br>';
		$h.='<b>Url acceso:</b> <a href="http://'.$nombre_inst.'.netwarmonitor.mx">http://'.$nombre_inst.'.netwarmonitor.mx</a><br>';
		$h.='<b>Usuario:</b> '.$userportal.'<br>';
		$h.='<b>Contraseña:</b> '.$passportal.'<br>';

		// echo $correoportal;

		// $mail->Subject = "Portal de Proveedores";
		// $mail->AltBody = "Portal de Proveedores";
		// $mail->MsgHTML($h);
		// $mail->AddAddress($correoportal, $correoportal);
		// $headers = "MIME-Version: 1.0" . "\r\n";
		// $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// $headers .= "From: mailer@netwarmonitor.com" . "\r\n";

		// if(mail($correoportal, 'Portal de Proveedores', $h, $headers)){
		// 	echo 1;
		// }else{
		// 	echo 0;
		// }

		require_once('../../modulos/phpmailer/sendMail.php');

			$mail->Subject = "Portal de Proveedores";
			$mail->AltBody = "NetwarMonitor";
			$mail->MsgHTML($h);
			$mail->AddAddress($correoportal, $correoportal);
			
			//$mail->AddAddress($email, $email);


			if(@$mail->Send()){
				echo 1;
			}else{
				echo 0;
			}

		// if($mail->Send()){
		// 	echo 1;
		// }else{
		// 	echo 0;
		// }

	}

	function listaOrdenesCompra($idProveedor)
        {
            

            $myQuery = "SELECT d.idoc, SUBSTRING(a.fecha,1,10), /* pr.razon_social, */ b.nombreEmpleado as nombre, SUBSTRING(a.fecha_entrega,1,10) as fechaf, /* alm.nombre as almacen, */ if(d.total is null,TRUNCATE(a.total,2), TRUNCATE(d.total,2) ) as importe, /* a.urgente, */ a.activo, a.id as idreq, min(op.estatus) estatusPartida
            FROM app_requisiciones a
            left JOIN nomi_empleados b on b.idEmpleado=a.id_solicito
            left join mrp_proveedor pr on pr.idPrv=a.id_proveedor
            left join app_almacenes alm on alm.id=a.id_almacen
            LEFT JOIN app_area_empleado c on c.id=b.id_area_empleado
            left join app_opartidas op on op.idocompra=a.id
            left JOIN (SELECT b2.costo, a2.cantidad, b2.id_producto, a2.id as fff, a2.id_requisicion
, b2.id_proveedor
                       FROM app_requisiciones_datos a2
                       inner JOIN app_costos_proveedor b2 on b2.id_producto=a2.id_producto) as s2 on
 s2.id_requisicion=a.id and s2.id_proveedor=a.id_proveedor
            LEFT join (Select r.total, r.id_requisicion, r.id as idoc from app_ocompra r) d on d.id_requisicion=a.id
            where (a.activo!=3 AND a.activo!=2) and a.activo!=0 and a.pr!=2 and a.id_proveedor='$idProveedor'
            GROUP BY a.id
            ORDER BY a.id desc;";



            $listaReq = $this->query($myQuery);
            return $listaReq;

        }

        function listaXmlsCompra2($idoc){
            $myQuery = "SELECT * from app_recepcion_xml where id_oc='$idoc' order by id;";
            $listaReq = $this->query($myQuery);
            return $listaReq;
        }

        function listaRecepcionesAdju($idoc)
        {
            if($idoc>0){
                $add=' WHERE a.id_oc='.$idoc.' ';
            }else{
                $add='';
            }
            $myQuery = "SELECT b.id, a.id as idr, SUBSTRING(a.fecha_recepcion,1,10) as fechar, a.no_factura, SUBSTRING(a.fecha_factura,1,10) as fechaf, a.imp_factura, a.estatus, a.activo, a.id_consignacion, a.total FROM app_recepcion a 
            inner join app_ocompra b on b.id=a.id_oc 
            ".$add.";";



            $listaReq = $this->query($myQuery);
            return $listaReq;

        }

        function verificarPagos($idoc){
            $myQuery = "SELECT a.id_recepcion from app_recepcion_datos a
                        inner join app_recepcion b on b.id=a.id_recepcion
                        where b.id_oc=".$idoc.";";
            $resultque = $this->queryArray($myQuery);

            if($resultque['total']>0){
                $tpagos=0;
                foreach ($resultque['rows'] as $k => $v) {
                    $myQuery2 = "SELECT count(*) as pagos from app_pagos a
                    inner join app_pagos_relacion b on b.id_documento=a.id
                    where concepto='Recepcion-".$v['id_recepcion']."';";
                    $r2 = $this->queryArray($myQuery2);
                    $tp =  $r2['rows'][0]['pagos']*1;
                    $tpagos+=$tp;
                }
            }

            if($tpagos>0){
                echo 1;
            }else{
                echo 0;
            }
        }


		function listaRecepcionesAdju2($idoc)
		{
			if($idoc>0){
				$add=' WHERE a.id_oc='.$idoc.' ';
			}else{
				$add='';
			}
			$myQuery = "SELECT b.id, a.id as idr, SUBSTRING(a.fecha_recepcion,1,10) as fechar, a.no_factura, SUBSTRING(a.fecha_factura,1,10) as fechaf, a.imp_factura, a.estatus, a.activo, a.id_consignacion, a.total FROM app_recepcion a 
			inner join app_ocompra b on b.id=a.id_oc 
			".$add.";";



			$listaReq = $this->query($myQuery);
			return $listaReq;

		}

		function updateProveedorPortal($idProveedor,$calle,$no_ext, $no_int, $cp, $colonia, $selectPais, $selectEstado, $selectMunicipio, $ciudad, $nombre_contacto, $email,$telefono,$web){

			$update = "UPDATE mrp_proveedor set  calle='".$calle."', no_ext='".$no_ext."', no_int='".$no_int."', cp='".$cp."', colonia='".$colonia."', idpais='".$selectPais."', idestado='".$selectEstado."', idmunicipio='".$selectMunicipio."', ciudad='".$ciudad."',  nombre='".$nombre_contacto."', email='".$email."', telefono='".$telefono."',  web='".$web."' WHERE idPrv=".$idProveedor;
			$resUpdate = $this->queryArray($update);

			return array('status' => true , 'idClienteInser' => $idProveedor, 'comun_fac' => 0);

		}

		function listaXmlsCompra($idoc){
			$myQuery = "SELECT arp.*,m.codigo as moneda from app_recepcion_xml arp left join cont_coin m on m.coin_id=arp.moneda where arp.id_oc='$idoc' order by arp.id;";
			$listaReq = $this->query($myQuery);
			return $listaReq;
		}

		function rfcOrganizacion(){
			$sql=$this->query("select RFC from organizaciones ");
			return $sql->fetch_assoc();
		}

		function guardaXmlAdju2($fac_folio,$fac_fecha,$fac_total,$fac_uuid,$fac_concepto,$xmlfile,$idoc,$subtotal)
		{

			date_default_timezone_set("Mexico/General");
			$fecha_subida=date('Y-m-d H:i:s'); 

			$myQuery = "INSERT INTO app_recepcion_xml (id_oc,fecha_factura,imp_factura,xmlfile,concepto,fecha_subida) VALUES ('$idoc','$fac_fecha','$fac_total','$xmlfile','$fac_concepto','$fecha_subida');";
			$last_id = $this->insert_id($myQuery);

			if($last_id>0){

				$myQuery = "SELECT a.id_recepcion from app_recepcion_datos a
						inner join app_recepcion b on b.id=a.id_recepcion
						where b.id_oc=".$idoc.";";
				$resultque = $this->queryArray($myQuery);

				if($resultque['total']>0){
					foreach ($resultque['rows'] as $k => $v) {
						$myQuery2 = "DELETE FROM app_pagos where concepto='Recepcion-".$v['id_recepcion']."' ";
						$this->query($myQuery2);
					}
				}

				///////////////////////ACONTIA///////////////////////////////
				///////////////////////////////////	/////////////////////////
				/*
				//Si tiene acontia y esta conectado
				$conexion_acontia = $this->query("SELECT conectar_acontia, pol_autorizacion FROM app_configuracion WHERE id = 1");
				$conexion_acontia = $conexion_acontia->fetch_assoc();
				
				if(intval($conexion_acontia['conectar_acontia']))
				{
					//Buscar el tipo de gasto
					$tipo_gasto = $this->query("SELECT rq.id_tipogasto, c.id_proveedor, rq.tipo_cambio FROM app_requisiciones rq 
												INNER JOIN app_ocompra c ON c.id_requisicion = rq.id WHERE c.id = $idoc");
					$tipo_gasto = $tipo_gasto->fetch_assoc();
					$id_proveedor = $tipo_gasto['id_proveedor'];
					$tipo_cambio = $tipo_gasto['tipo_cambio'];
					if(!intval($tipo_cambio))
						$tipo_cambio = 1;
					$tipo_gasto = $tipo_gasto['id_tipogasto'];

					//Si la compra esta relacionada a un tipo de gasto continua
					if(intval($tipo_gasto))
					{
						//Busca si es poliza automatica HACE UN LIMIT POR SI EXISTE MAS DE UNA TOMARA LA ULTIMA CONFIGURACION
						$automatica = $this->query("SELECT* FROM app_tpl_polizas WHERE id > 9 AND id_gasto = $tipo_gasto ORDER BY id DESC LIMIT 1");
						$automatica = $automatica->fetch_assoc();
						$idpol = $automatica['id'];

						//Si es automatica y se genera por documento CONTINUA
						if(intval($automatica['automatica']) && intval($automatica['poliza_por_mov']) == 1)
						{
							$fecha = explode('-',$fac_fecha);

							//Busca el id del ejercicio, si no existe, busca el ultimo y le suma al id para sacar el ejercicio
							$ejercicio = $this->query("SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = ".$fecha[0]);
							$ejercicio = $ejercicio->fetch_assoc();
							$ejercicio = $ejercicio['Id'];

							//Si no existe calcula el Id
							if(!intval($ejercicio))
							{
								$ejercicioAntes = $this->query("SELECT * FROM cont_ejercicios ORDER BY Id DESC LIMIT 1");
								$ejercicioAntes = $ejercicioAntes->fetch_assoc();
								$nuevoEj = intval($fecha[0]) - intval($ejercicioAntes['NombreEjercicio']);
								$ejercicio = intval($ejercicioAntes['Id']) + $nuevoEj;
							}
							$numpol = $this->query("SELECT pp.numpol+1 FROM cont_polizas pp WHERE pp.idtipopoliza = ".$automatica['id_tipo_poliza']." AND pp.activo = 1 AND pp.idejercicio = $ejercicio AND pp.idperiodo = ".intval($fecha[1])." ORDER BY pp.numpol DESC LIMIT 1");
							$numpol = $numpol->fetch_assoc();
							$numpol = $numpol['numpol'];
							if(!intval($numpol))
								$numpol = 1;
							$activo = 1;
							if(intval($conexion_acontia['pol_autorizacion']))
								$activo = 0;

							//Genera la poliza
							$id_poliza_acontia = $this->insert_id("INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, referencia, concepto, fecha, fecha_creacion, activo, eliminado, pdv_aut, usuario_creacion, usuario_modificacion)
								 VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'Poliza Fac. $fac_uuid','".$automatica['nombre_poliza']." $fac_concepto','$fac_fecha',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, ".$_SESSION["accelog_idempleado"].", 0)");
							$cont = 0;//Contador de movimientos
							
							$cuentas_poliza = $this->query("SELECT id_cuenta, tipo_movto, id_dato, nombre_impuesto FROM app_tpl_polizas_mov WHERE activo = 1 AND id_tpl_poliza = $idpol");
							
							$ruta   = "../cont/xmls/facturas/";//Ruta donde se copiara
							//Genera Movimientos de la poliza
							while($cp = $cuentas_poliza->fetch_assoc())
							{
								$cont++;
								//Cargo o abono
								if(intval($cp['tipo_movto']) == 1)
									$tipo_movto = "Abono";
								if(intval($cp['tipo_movto']) == 2)
									$tipo_movto = "Cargo";

								//dependiendo el tipo de dato sera el valor que tomara.
								if(intval($cp['id_dato']) == 2)
								{
									//Si es el subtotal
									$importe = $subtotal;
								}
								elseif(intval($cp['id_dato']) == 3)
								{
									$importe = 0;
									if($cp['nombre_impuesto'])
									{
										$impu = str_replace('%', '', $cp['nombre_impuesto']);
										$impu = explode(' ', $impu);
										//Si es el impuesto
										$aa = simplexml_load_file($ruta.'temporales/'.$xmlfile);
										if($namespaces = $aa->getNamespaces(true))
										{
											$child = $aa->children($namespaces['cfdi']);
											for($j=0;$j<=(count($child->Impuestos->Traslados->Traslado)-1);$j++)
											{
												$bandera1 = $bandera2 = $cantidad = 0;
												foreach($child->Impuestos->Traslados->Traslado[$j]->attributes() AS $a => $b)
												{
													if($a == 'impuesto' && strtoupper($b) == $impu[0])
														$bandera1 = 1;
													
													if($impu[1] != 'EXENTO')
													{
														if($a == 'tasa' && floatval($b) == floatval($impu[1]))
															$bandera2 = 1;
													}
													else
													{
														if($a == 'tasa' && $b == $impu[1])
															$bandera2 = 1;
													}
													
													if($a == 'importe')
														$cantidad = $b;

													if($bandera1 && $bandera2 && $cantidad)
														$importe = $cantidad;
												}
											}
										}
										//unset($aa);
									}
								}
								else
								{
									//Si es total, cliente o proveedor agrega el total en el importe
									$importe = $fac_total;
								}

								

								$id_mov = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura, FormaPago, tipocambio) 
								VALUES($id_poliza_acontia, $cont, 1, 1, ".$cp['id_cuenta'].", '$tipo_movto', $importe, '','".$automatica['nombre_poliza']." $fac_concepto $impuesto', $activo, DATE_SUB(NOW(), INTERVAL 6 HOUR), '$xmlfile', 1, $tipo_cambio)");
								$ids_movs .= $id_mov.",";

								//Crear carpeta y copiar xml de la factura, ya se que esta no es el controlador pero no quedaba de otra, asi que hare una excepcion.
								if(!file_exists($ruta.$id_poliza_acontia))//Si no existe la carpeta de ese poliza la crea
								{
									mkdir ($ruta.$id_poliza_acontia, 0777);
								}
								copy($ruta.'temporales/'.$xmlfile, $ruta.$id_poliza_acontia."/".$xmlfile);    
								

							}
							$this->query("UPDATE app_recepcion_xml SET id_poliza_mov = '$ids_movs' WHERE id = $last_id");
							$ids_movs = '';
						}
					}
				}
*/

				//Termina conexion con acontia
				////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////

			}

			return $last_id;
		}

	function obtener_prepolizas_pago(){
		$myQuery = "SELECT id, nombre_poliza AS nombre FROM cont_tpl_polizas WHERE provision = 0;";
		$Result  = $this->query($myQuery);
		return $Result;
	}

	function obtener_prepolizas_provision(){
		$myQuery = "SELECT id, nombre_poliza AS nombre FROM cont_tpl_polizas WHERE provision = 1;";
		$Result  = $this->query($myQuery);
		return $Result;
	}

	function obtener_cuenta_gasto_padre(){
		$myQuery = "SELECT account_code FROM cont_accounts AS a INNER JOIN cont_config AS c ON a.account_id = IF(c.CuentasGastosPolizas = -1, 0, c.CuentasGastosPolizas);";
		$Result  = $this->query($myQuery);
		return $Result;
	}

	function obtener_cuentas_gasto($cuenta_gasto){
		$myQuery = "SELECT account_id AS id, CONCAT(manual_code,' ',description) AS nombre FROM cont_accounts WHERE main_account = 3 AND removed = 0 AND account_code LIKE '$cuenta_gasto%' ORDER BY manual_code ASC;";
		$Result  = $this->query($myQuery);
		return $Result;
	}
	public function verificaCodigo($idProve,$codigo,$rfc){
		
		if($rfc != 'XAXX010101000' && $rfc != 'XEXX010101000')
			$where = "(codigo = '".$codigo."' OR rfc = '".$rfc."')";
		else
			$where = "codigo = '".$codigo."'";

		$sel = "SELECT idPrv FROM mrp_proveedor WHERE $where AND idPrv!=".$idProve;
		$res = $this->queryArray($sel);
		return $res['total'];
	}

	public function listaCargos($idPrvCli,$cobrar_pagar)
    {
        if(!intval($cobrar_pagar))
            $cliProv = "(SELECT dias_credito FROM comun_cliente WHERE id = p.id_prov_cli) AS diascredito, ";
        else
            $cliProv = "(SELECT diascredito FROM mrp_proveedor WHERE idPrv = p.id_prov_cli) AS diascredito, ";

        $myQuery = "SELECT p.id, p.tipo_cambio, (SELECT codigo FROM cont_coin WHERE coin_id = p.id_moneda) AS moneda, p.tipo_cambio, p.fecha_pago, @c := p.cargo*p.tipo_cambio, p.cargo, 
        $cliProv 
        p.concepto, @p := IFNULL((SELECT SUM(pr.abono) FROM app_pagos_relacion pr WHERE pr.activo = 1 AND pr.id_tipo = 0 AND pr.id_documento = p.id  AND (SELECT id_moneda FROM app_pagos WHERE id = pr.id_pago) = 1),0) AS pagos,
                    @p2 := IFNULL((SELECT SUM(pr.abono*(SELECT tipo_cambio FROM app_pagos WHERE id = pr.id_pago)) FROM app_pagos_relacion pr WHERE pr.activo = 1 AND pr.id_tipo = 0 AND pr.id_documento = p.id AND (SELECT id_moneda FROM app_pagos WHERE id = pr.id_pago) != 1),0) AS pagos2,
                    (@c-(@p+@p2)) AS saldo
                    FROM app_pagos p
                    WHERE p.id_prov_cli = $idPrvCli
                    AND p.cobrar_pagar = $cobrar_pagar
                    AND p.cargo > 0
                    ";
        return $this->query($myQuery);

    }

        public function listaFacturas($idPrvCli,$cobrar_pagar)
    {
        //$conexion_bancos = $this->conectado_bancos();

        if(intval($cobrar_pagar))
        {
            

            $myQuery = "SELECT 1 AS rq_tipo_cambio, p.id AS id_oc, 4 AS origen, 'MXN' AS Moneda, CONCAT('(',x.folio,') Obra: ',g.obra,' Est: ',p.id) AS desc_concepto, x.id AS id, x.fecha_fac,0 AS no_factura,SUM(p.total) AS imp_factura,SUM(p.total) AS importe_pesos, x.xml_file, (SELECT diascredito FROM mrp_proveedor WHERE id_xtructur = p.id_prov) AS diascredito,
                    @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = x.id AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                    @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = x.id AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                    (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, (SELECT folio FROM cont_facturas WHERE xml LIKE CONCAT('%',x.xml_file,'%')) AS folio_fac 
                                                                                                
                    FROM constru_estimaciones_bit_prov p
                    INNER JOIN constru_xml_pedis x ON x.id_estimacion = p.id
                    INNER JOIN constru_generales g ON g.id = p.id_obra
                    INNER JOIN constru_pedis pe ON pe.id = p.id_oc AND pe.id_prov = p.id_prov
                    WHERE p.id_prov = (SELECT id_xtructur FROM mrp_proveedor WHERE idPrv = $idPrvCli) AND p.estatus = 1 AND x.borrado != 1 AND pe.fPago = 6
                    GROUP BY x.xml_file

                    UNION ALL
                    
                     /* FACTURAS ALMACEN*/
                   (SELECT 1 AS rq_tipo_cambio, fa.id AS id_oc, 5 AS origen, fa.moneda COLLATE utf8_general_ci AS Moneda , fa.uuid AS desc_concepto, fa.id AS id, fa.fecha AS fecha_fac,0 AS no_factura,fa.importe AS imp_factura,fa.importe AS importe_pesos, fa.xml AS xmlfile, (SELECT diascredito FROM mrp_proveedor WHERE rfc = fa.rfc COLLATE utf8_general_ci AND status = -1) AS diascredito,
                    @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = fa.id AND rp.id_tipo=5 AND p.cobrar_pagar = 1),
                    @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND  rp.id_documento = fa.id AND rp.id_tipo=5 AND p.cobrar_pagar = 1),
                    (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, fa.folio AS folio_fac  
                    FROM cont_facturas fa
                    WHERE fa.cancelada = 0 AND fa.rfc != 'XAXX010101000' AND fa.rfc != 'XEXX010101000' AND fa.fecha >= '2018-01-01 00:00:00' AND fa.rfc COLLATE utf8_general_ci = (SELECT rfc FROM mrp_proveedor WHERE idPrv = $idPrvCli) AND (json NOT LIKE '%TipoDeComprobante\":\"E\"%' AND json NOT LIKE '%TipoRelacion\":\"01\"%' AND json NOT LIKE '%TipoRelacion\":\"03\"%'))


                    ORDER BY id_oc;";
        }
        else
        {
            if(intval($idPrvCli))
            {
                $myQuery = "SELECT rf.id AS id, 1 AS origen,v.tipo_cambio AS rq_tipo_cambio, v.idVenta AS id_oventa, 
                            (SELECT codigo FROM cont_coin WHERE coin_id = IF(v.moneda = 0,1,v.moneda)) AS Moneda, CONCAT('Venta POS: ',v.idVenta) AS desc_concepto, rf.folio, rf.id AS idres, rf.fecha AS fecha_factura,@total := ((vp.monto-v.cambio)/IF(v.tipo_cambio = 0,1,v.tipo_cambio)) AS imp_factura, (@total*IF(v.tipo_cambio = 0,1,v.tipo_cambio)) AS importe_pesos, rf.xmlfile, (SELECT dias_credito FROM comun_cliente WHERE id = v.idCliente) AS diascredito,
                            @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND  rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
                            @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
                            (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, (SELECT folio FROM cont_facturas WHERE uuid COLLATE utf8_general_ci LIKE CONCAT('%',rf.folio,'%') AND cancelada = 0) AS folio_fac 
                            FROM app_pos_venta_pagos vp
                            INNER JOIN app_pos_venta v ON vp.idVenta = v.idVenta
                            INNER JOIN app_respuestaFacturacion rf ON rf.idSale = v.idVenta
                            LEFT JOIN comun_cliente c ON c.id = v.idCliente
                            WHERE v.idCliente = $idPrvCli AND estatus = 1 AND (v.documento = 1 || v.documento = 2) 
                            AND vp.idFormapago = 6 AND rf.origen = 2 AND rf.borrado != 1
                            /*TERMINA POS*/

                            UNION ALL 

                            /*COMIENZA XTRUCTUR*/
                            SELECT rf.id AS id, 2 AS origen,1 AS rq_tipo_cambio, c.id AS id_oventa, 
                            'MXN' AS Moneda, CONCAT('Est. Obra: ',c.id) AS desc_concepto, rf.folio, rf.id AS idres, rf.fecha AS fecha_factura,@total := c.total AS imp_factura, (@total) AS importe_pesos, rf.xmlfile, (SELECT dias_credito FROM comun_cliente WHERE id_xtructur = c.id_cliente) AS diascredito,
                            @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
                            @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
                            (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, (SELECT folio FROM cont_facturas WHERE uuid COLLATE utf8_general_ci LIKE CONCAT('%',rf.folio,'%')) AS folio_fac 
                                                        
                            FROM constru_estimaciones_bit_cliente c
                            INNER JOIN app_respuestaFacturacion rf ON rf.idSale = c.id
                            WHERE c.id_cliente = (SELECT id_xtructur FROM comun_cliente WHERE id = $idPrvCli) AND c.estatus = 4 AND rf.proviene = 4 AND c.fp = 6 AND rf.borrado != 1
                            ";
            }
           

        }

        $listaFacturas = $this->query($myQuery);
        return $listaFacturas;
    }

    function listaFormasPago()
    {
        return $this->query("SELECT* FROM view_forma_pago WHERE claveSat < '99' ORDER BY claveSat");
    }

    function listaMonedas()
    {
        return $this->query("SELECT* FROM cont_coin ORDER BY coin_id");
    }

     function info_car_fac($id,$t,$cp,$pos)
    {
        if($t == 'c')
        {
            if(!intval($cp))
                $provcli = "(SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = id_prov_cli) AS provcli";
            else
                $provcli = "(SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = id_prov_cli) AS provcli";
            $myQuery = "SELECT id_prov_cli, $provcli, (cargo*tipo_cambio) AS cargo, concepto, tipo_cambio,fecha_pago FROM app_pagos WHERE id = $id";
        }
        if($t == 'f')
        {
            if(!intval($cp))
            {
                if(intval($pos) == 1)
                {
                    /*VENTAS POS*/
                    $myQuery = "SELECT v.idCliente AS id_prov_cli, 
                            (SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = v.idCliente) AS provcli, 
                            SUM(vp.monto-v.cambio) AS cargo, 
                            CONCAT(rf.folio,' Venta POS: ',v.idVenta) AS concepto, IF(v.tipo_cambio = 0,1,v.tipo_cambio) AS tipo_cambio, rf.fecha AS fecha_pago
                            FROM app_respuestaFacturacion rf
                            INNER JOIN app_pos_venta_pagos vp ON vp.idVenta = rf.idSale
                            INNER JOIN app_pos_venta v ON v.idVenta = vp.idVenta
                            WHERE rf.id = $id AND vp.idFormapago = 6";
                }
                if(intval($pos) == 2)
                {
                    /*XTRUCTUR*/
                    $myQuery = "SELECT cl.id AS id_prov_cli, 
                            CONCAT(cl.nombre,'**/**',cl.dias_credito) AS provcli, 
                            SUM(c.total) AS cargo, 
                            CONCAT(rf.folio,' Est. Xtructur: ',c.id) AS concepto, 1 AS tipo_cambio, rf.fecha AS fecha_pago
                            FROM app_respuestaFacturacion rf
                            INNER JOIN constru_estimaciones_bit_cliente c ON c.id = rf.idSale
                            LEFT JOIN comun_cliente cl ON cl.id_xtructur = c.id_cliente
                            WHERE rf.id = $id AND c.fp = 6";
                }
            }
            else
            {
                if(intval($pos) < 4)
                {
                    /*COMPRAS*/
                    $myQuery = "SELECT oc.id_proveedor AS id_prov_cli, 
                            (SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = oc.id_proveedor) AS provcli, 
                            SUM(CASE rq.id_moneda WHEN 1 THEN r.imp_factura WHEN 2 THEN r.imp_factura*rq.tipo_cambio END) AS cargo, 
                            r.concepto, rq.tipo_cambio, r.fecha_factura AS fecha_pago
                            FROM app_recepcion_xml r
                            INNER JOIN app_ocompra oc ON oc.id = r.id_oc
                            INNER JOIN app_requisiciones rq ON rq.id = oc.id_requisicion
                            WHERE r.id_oc = $id";
                }
                if(intval($pos) == 4)
                {
                    /*XTRUCTUR*/
                    $myQuery = "SELECT p.idPrv AS id_prov_cli, 
                            CONCAT(p.razon_social,'**/**',p.diascredito) AS provcli, 
                            SUM(e.total) AS cargo, 
                            CONCAT(x.folio,'<br />Obra: ',g.obra,'<br />Est: ',e.id) AS concepto, 1 AS tipo_cambio, x.fecha_fac AS fecha_pago
                            FROM constru_xml_pedis x
                            INNER JOIN constru_estimaciones_bit_prov e ON e.id = x.id_estimacion
                            INNER JOIN constru_generales g ON g.id = e.id_obra
                            LEFT JOIN mrp_proveedor p ON p.id_xtructur = e.id_prov
                            WHERE x.id = $id";
                }

                 if(intval($pos) == 5)
                {
                    /*FACTURAS ALMACEN DIGITAL*/
                    $myQuery = "SELECT p.idPrv AS id_prov_cli, 
                            CONCAT(p.razon_social,'**/**',p.diascredito) AS provcli, 
                            SUM(fa.importe) AS cargo, 
                            fa.folio AS concepto, 1 AS tipo_cambio, fa.fecha AS fecha_pago
                            FROM cont_facturas fa
                            LEFT JOIN mrp_proveedor p ON p.rfc = fa.rfc COLLATE utf8_general_ci
                            WHERE fa.id = $id";
                }
                
            }

                
        }

        $datos = $this->query($myQuery);
        $datos = $datos->fetch_assoc();
        return $datos;
    }

    public function buscaPoliza($idmov)
    {
        $res = $this->query("SELECT IdPoliza FROM cont_movimientos WHERE Id = $idmov");
        $res = $res->fetch_assoc();
        return $res['IdPoliza'];
    }

    public function carac_padre()
    {
        $arr = Array();
        $myQuery = "SELECT id,nombre FROM app_caracteristicas_padre";
        $res = $this->query($myQuery);
        while($r = $res->fetch_assoc())
            $arr[$r['id']] = $r['nombre'];

        return json_encode($arr);
    }

    public function carac_hija()
    {
        $arr = Array();
        $myQuery = "SELECT id,nombre FROM app_caracteristicas_hija";
        $res = $this->query($myQuery);
        while($r = $res->fetch_assoc())
            $arr[$r['id']] = $r['nombre'];

        return json_encode($arr);
    }

     public function prov_prod_reporte($vars)
    {

        if($vars['f_ini']==''){
            $vars['f_ini']='2000-01-01';
        }
        if($vars['f_fin']==''){
            $vars['f_fin']='3000-01-01';
        }
        $idPrvs = $vars['idPrvs'];
        $proveedores = "";
        if(intval($vars['rango']))
            $proveedores .= " AND co.id_proveedor BETWEEN ".$idPrvs[0]." AND ".$idPrvs[1];
        else
        {
            if($idPrvs[0])
            {
                $proveedores .= " AND (";
                for($i=0;$i<=count($idPrvs)-1;$i++)
                {
                    if($i>0)
                        $proveedores .= " OR ";
                    $proveedores .= " co.id_proveedor = ".$idPrvs[$i];
                }
                $proveedores .= ") ";
            }   
        }

        $proveedores = " AND co.id_proveedor = ".$idPrvs;

        $almacenes='';
        if(intval($vars['sucursal']))
        {
            $almacenes = "AND a.id_sucursal = ".$vars['sucursal'];
            if(intval($vars['almacen']))   
                $almacenes = "AND co.id_almacen = ".$vars['almacen'];
        }

        $usuario = '';
        if(intval($vars['usuario']))
            $usuario = "AND co.id_usrcompra = ".$vars['usuario'];

        $caracteristicas = '';
        if(intval($vars['tipo_producto']) == 1)
        {
            if(intval($vars['departamento']))
                $caracteristicas .= " AND pr.departamento = ".$vars['departamento'];

            if(intval($vars['familia']))
                $caracteristicas .= " AND pr.familia = ".$vars['familia'];

            if(intval($vars['linea']))
                $caracteristicas .= " AND pr.linea = ".$vars['linea'];
        }

        if(intval($vars['tipo_producto']) == 2)
        {
            if(intval($vars['caracteristica_padre']))
                $caracteristicas .= " AND cd.caracteristica LIKE '%".$vars['caracteristica_padre']."=>%'";

            if(intval($vars['caracteristica_hija1']))
            {
                $caracteristicas .= " AND (";
                for($k = $vars['caracteristica_hija1']; $k <= $vars['caracteristica_hija2']; $k++)
                {
                    if($k > intval($vars['caracteristica_hija1']))
                        $caracteristicas .= " OR ";
                    $caracteristicas .= " cd.caracteristica LIKE '%=>$k%' ";
                }
                $caracteristicas .= " ) ";
            }
        }

        $unidad_base = '';
        if(intval($vars['unidad_base']))
            $unidad_base = "AND id_unidad_compra = ".$vars['unidad_base']; 

        $factor = "pr.id_unidad_compra";
        if(intval($vars['unidad_base_conversion']))
        {
            $factor = $vars['unidad_base_conversion'];
        }

        $producto = '';
        if(intval($vars['producto']))
            $producto = " AND cd.id_producto = ".$vars['producto'];
        
        $recepcion = "";
        $fecha = "co.fecha";
        $activo = "co.activo";
        $idc = "co.id AS id_compra,";
        if(intval($vars['tipo_doc']) == 2)
        {
            $recepcion = "RIGHT JOIN app_recepcion re ON re.id_oc = co.id";
            $fecha = "re.fecha_recepcion";
            $activo = "re.activo";
            $idc = "re.id AS id_compra,";
        }


if($vars['status_doc']!=''){
        if(intval($vars['status_doc']) == 1)
            $vars['status_doc'] = "1 OR ".$activo." = 4 OR ".$activo." = 5";
        if(intval($vars['status_doc']) == 0)
            $vars['status_doc'] = 3;

        $act='AND ('.$activo.' ='.$vars['status_doc'].')';
    }else{
        $act='';
    }

        $myQuery = "SELECT 
cd.caracteristica,
co.id_proveedor,
(SELECT razon_social FROM mrp_proveedor WHERE idPrv = co.id_proveedor) Proveedor,
pr.id_tipo_costeo,
cd.id_producto,
CONCAT('(',pr.codigo,') ',pr.nombre) AS Producto,
$fecha AS fecha, 
$idc 
cd.cantidad AS cantidad,
(SELECT CONCAT(nombre,'*|*',factor) FROM app_unidades_medida WHERE id = $factor) AS UnidadBase,
cd.costo, 
(cd.costo*cd.cantidad) AS Importe, 
cd.impuestos 
FROM app_ocompra_datos cd 
INNER JOIN app_ocompra co ON co.id = cd.id_ocompra
LEFT JOIN app_productos pr ON  pr.id = cd.id_producto
LEFT JOIN app_almacenes a ON a.id = co.id_almacen
$recepcion 
WHERE $fecha BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."' 
$producto 
$proveedores 
$almacenes 
$usuario 
$caracteristicas 
$unidad_base 


ORDER BY co.id_proveedor, cd.id_producto, $fecha";
return $this->query($myQuery);
    }

    public function prov_prod_reporte_req($vars)
    {
        $idPrvs = $vars['idPrvs'];
        $proveedores = "";
        if(intval($vars['rango']))
            $proveedores .= " AND re.id_proveedor BETWEEN ".$idPrvs[0]." AND ".$idPrvs[1];
        else
        {
            if($idPrvs[0])
            {
                $proveedores .= " AND (";
                for($i=0;$i<=count($idPrvs)-1;$i++)
                {
                    if($i>0)
                        $proveedores .= " OR ";
                    $proveedores .= " re.id_proveedor = ".$idPrvs[$i];
                }
                $proveedores .= ") ";
            }   
        }

        $almacenes='';
        if(intval($vars['sucursal']))
        {
            $almacenes = "AND a.id_sucursal = ".$vars['sucursal'];
            if(intval($vars['almacen']))   
                $almacenes = "AND re.id_almacen = ".$vars['almacen'];
        }

        $usuario = '';
        if(intval($vars['usuario']))
            $usuario = "AND re.id_solicito = ".$vars['usuario'];

        $caracteristicas = '';
        if(intval($vars['tipo_producto']) == 1)
        {
            if(intval($vars['departamento']))
                $caracteristicas .= " AND pr.id_departamento = ".$vars['departemento'];

            if(intval($vars['familia']))
                $caracteristicas .= " AND pr.id_familia = ".$vars['familia'];

            if(intval($vars['linea']))
                $caracteristicas .= " AND pr.id_linea = ".$vars['linea'];
        }

        if(intval($vars['tipo_producto']) == 2)
        {
            if(intval($vars['caracteristica_padre']))
                $caracteristicas .= " AND rd.caracteristica LIKE '%".$vars['caracteristica_padre']."=>%'";

            if(intval($vars['caracteristica_hija1']))
            {
                $caracteristicas .= " AND (";
                for($k = $vars['caracteristica_hija1']; $k <= $vars['caracteristica_hija2']; $k++)
                {
                    if($k > intval($vars['caracteristica_hija1']))
                        $caracteristicas .= " OR ";
                    $caracteristicas .= " rd.caracteristica LIKE '%=>$k%' ";
                }
                $caracteristicas .= " ) ";
            }
        }

        $unidad_base = '';
        if(intval($vars['unidad_base']))
            $unidad_base = "AND id_unidad_compra = ".$vars['unidad_base']; 

        $factor = "pr.id_unidad_compra";
        if(intval($vars['unidad_base_conversion']))
        {
            $factor = $vars['unidad_base_conversion'];
        }

        $producto = '';
        if(intval($vars['producto']))
            $producto = " AND rd.id_producto = ".$vars['producto'];

        if(intval($vars['status_doc']) == 1)
            $vars['status_doc'] = "1 OR re.activo = 4 OR re.activo = 5";
        if(intval($vars['status_doc']) == 0)
            $vars['status_doc'] = "0 OR re.activo = 3";

        $myQuery = "SELECT 
rd.caracteristica,
re.id_proveedor,
(SELECT razon_social FROM mrp_proveedor WHERE idPrv = re.id_proveedor) Proveedor,
rd.id_producto,
CONCAT('(',pr.codigo,') ',pr.nombre) AS Producto,
re.fecha, 
re.id AS id_compra,
rd.cantidad AS cantidad,
(SELECT CONCAT(nombre,'*|*',factor) FROM app_unidades_medida WHERE id = $factor) AS UnidadBase,
(SELECT costo FROM app_costos_proveedor WHERE id_producto = rd.id_producto AND id_proveedor = re.id_proveedor) AS costo,
((SELECT costo FROM app_costos_proveedor WHERE id_producto = rd.id_producto AND id_proveedor = re.id_proveedor)*rd.cantidad) AS Importe, 
0 AS impuestos 
FROM app_requisiciones_datos rd 
INNER JOIN app_requisiciones re ON re.id = rd.id_requisicion
LEFT JOIN app_productos pr ON  pr.id = rd.id_producto
LEFT JOIN app_almacenes a ON a.id = re.id_almacen
WHERE re.fecha BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."' 
$producto 
$proveedores 
$almacenes 
$usuario 
$caracteristicas 
$unidad_base 
AND pr.id_moneda = ".$vars['moneda']." 
AND (re.activo = ".$vars['status_doc'].") 

ORDER BY re.id_proveedor, rd.id_producto, re.fecha";
return $this->query($myQuery);
    }
    function partidaOrden($idoc){
    	//$selct = "SELECT * from app_opartidas where idocompra=".$idoc;
    	$selct = "SELECT p.*, x.xmlfile 
				from app_opartidas p 
				left join app_recepcion_xml x on x.id=p.idRecepcion 
				where p.idocompra=".$idoc;
				//echo $selct;

    	$res = $this->queryArray($selct);
    	return array('partidas' => $res['rows']);
    }

    function xmlToArray($xml, $options = array()) {
        $defaults = array(
            'namespaceSeparator' => ':',//you may want this to be something other than a colon
            'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
            'alwaysArray' => array(),   //array of xml tag names which should always become arrays
            'autoArray' => true,        //only create arrays for tags which appear more than once
            'textContent' => '$',       //key used for the text content of elements
            'autoText' => true,         //skip textContent key if node has no attributes or child nodes
            'keySearch' => false,       //optional search and replace on tag and attribute names
            'keyReplace' => false       //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace
     
        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                        str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                        . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                        . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }
     
        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = $this->xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);
     
                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                        str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
     
                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                            in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }
     
        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
     
        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
                ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
     
        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }

    function guardaXmlAdju($fac_folio,$fac_fecha,$fac_total,$fac_uuid,$fac_concepto,$xmlfile,$idoc,$subtotal,$cadena)
        {



            date_default_timezone_set("Mexico/General");
            $fecha_subida=date('Y-m-d H:i:s'); 

            $myQuery = "INSERT INTO app_recepcion_xml (id_oc,fecha_factura,imp_factura,xmlfile,concepto,fecha_subida) VALUES ('$idoc','$fac_fecha','$fac_total','$xmlfile','$fac_concepto','$fecha_subida');";
            $last_id = $this->insert_id($myQuery);



            if($last_id>0){

	            $cadTrim = trim($cadena,',');
	            $cad = explode(',', $cadTrim);

	            foreach ($cad as $key => $value) {
	            	$updatePar = "UPDATE app_opartidas set idRecepcion='".$last_id."' where id=".$value;
	            	$insertPArt = "INSERT into app_partidas_xml(idPartida,idRecepcion) values('".$value."','".$last_id."')";
	            	$this->queryArray($updatePar);
	            	$this->queryArray($insertPArt);
	            }


                $myQuery = "SELECT a.id_recepcion from app_recepcion_datos a
                        inner join app_recepcion b on b.id=a.id_recepcion
                        where b.id_oc=".$idoc.";";
                $resultque = $this->queryArray($myQuery);

                if($resultque['total']>0){
                    foreach ($resultque['rows'] as $k => $v) {
                        $myQuery2 = "DELETE FROM app_pagos where concepto='Recepcion-".$v['id_recepcion']."' ";
                        $this->query($myQuery2);
                    }
                }

                ///////////////////////ACONTIA///////////////////////////////
                ////////////////////////////////////////////////////////////

                //Si tiene acontia y esta conectado
                $conexion_acontia = $this->query("SELECT conectar_acontia, pol_autorizacion FROM app_configuracion WHERE id = 1");
                $conexion_acontia = $conexion_acontia->fetch_assoc();
                
                if(intval($conexion_acontia['conectar_acontia']))
                {
                    //Buscar el tipo de gasto
                    $tipo_gasto = $this->query("SELECT rq.id_tipogasto, c.id_proveedor, rq.tipo_cambio FROM app_requisiciones rq 
                                                INNER JOIN app_ocompra c ON c.id_requisicion = rq.id WHERE c.id = $idoc");
                    $tipo_gasto = $tipo_gasto->fetch_assoc();
                    $id_proveedor = $tipo_gasto['id_proveedor'];
                    $tipo_cambio = $tipo_gasto['tipo_cambio'];
                    if(!intval($tipo_cambio))
                        $tipo_cambio = 1;
                    $tipo_gasto = $tipo_gasto['id_tipogasto'];

                    //Si la compra esta relacionada a un tipo de gasto continua
                    if(intval($tipo_gasto))
                    {
                        //Busca si es poliza automatica HACE UN LIMIT POR SI EXISTE MAS DE UNA TOMARA LA ULTIMA CONFIGURACION
                        $automatica = $this->query("SELECT* FROM app_tpl_polizas WHERE id > 9 AND id_gasto = $tipo_gasto ORDER BY id DESC LIMIT 1");
                        $automatica = $automatica->fetch_assoc();
                        $idpol = $automatica['id'];

                        //Si es automatica y se genera por documento CONTINUA
                        if(intval($automatica['automatica']) && intval($automatica['poliza_por_mov']) == 1)
                        {
                            $fecha = explode('-',$fac_fecha);

                            //Busca el id del ejercicio, si no existe, busca el ultimo y le suma al id para sacar el ejercicio
                            $ejercicio = $this->query("SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = ".$fecha[0]);
                            $ejercicio = $ejercicio->fetch_assoc();
                            $ejercicio = $ejercicio['Id'];

                            //Si no existe calcula el Id
                            if(!intval($ejercicio))
                            {
                                $ejercicioAntes = $this->query("SELECT * FROM cont_ejercicios ORDER BY Id DESC LIMIT 1");
                                $ejercicioAntes = $ejercicioAntes->fetch_assoc();
                                $nuevoEj = intval($fecha[0]) - intval($ejercicioAntes['NombreEjercicio']);
                                $ejercicio = intval($ejercicioAntes['Id']) + $nuevoEj;
                            }
                            $numpol = $this->query("SELECT pp.numpol+1 FROM cont_polizas pp WHERE pp.idtipopoliza = ".$automatica['id_tipo_poliza']." AND pp.activo = 1 AND pp.idejercicio = $ejercicio AND pp.idperiodo = ".intval($fecha[1])." ORDER BY pp.numpol DESC LIMIT 1");
                            $numpol = $numpol->fetch_assoc();
                            $numpol = $numpol['numpol'];
                            if(!intval($numpol))
                                $numpol = 1;
                            $activo = 1;
                            if(intval($conexion_acontia['pol_autorizacion']))
                                $activo = 0;

                            //Genera la poliza
                            $id_poliza_acontia = $this->insert_id("INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, referencia, concepto, fecha, fecha_creacion, activo, eliminado, pdv_aut, usuario_creacion, usuario_modificacion)
                                 VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'Poliza Fac. $fac_uuid','".$automatica['nombre_poliza']." $fac_concepto','$fac_fecha',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, ".$_SESSION["accelog_idempleado"].", 0)");
                            $cont = 0;//Contador de movimientos
                            
                            $cuentas_poliza = $this->query("SELECT id_cuenta, tipo_movto, id_dato, nombre_impuesto FROM app_tpl_polizas_mov WHERE activo = 1 AND id_tpl_poliza = $idpol");
                            
                            $ruta   = "../cont/xmls/facturas/";//Ruta donde se copiara
                            //Genera Movimientos de la poliza
                            while($cp = $cuentas_poliza->fetch_assoc())
                            {
                                $cont++;
                                //Cargo o abono
                                if(intval($cp['tipo_movto']) == 1)
                                    $tipo_movto = "Abono";
                                if(intval($cp['tipo_movto']) == 2)
                                    $tipo_movto = "Cargo";

                                //dependiendo el tipo de dato sera el valor que tomara.
                                if(intval($cp['id_dato']) == 2)
                                {
                                    //Si es el subtotal
                                    $importe = $subtotal;
                                }
                                elseif(intval($cp['id_dato']) == 3)
                                {
                                    $importe = 0;
                                    if($cp['nombre_impuesto'])
                                    {
                                        $impu = str_replace('%', '', $cp['nombre_impuesto']);
                                        $impu = explode(' ', $impu);
                                        //Si es el impuesto
                                        $aa = simplexml_load_file($ruta.'temporales/'.$xmlfile);
                                        if($namespaces = $aa->getNamespaces(true))
                                        {
                                            $child = $aa->children($namespaces['cfdi']);
                                            for($j=0;$j<=(count($child->Impuestos->Traslados->Traslado)-1);$j++)
                                            {
                                                $bandera1 = $bandera2 = $cantidad = 0;
                                                foreach($child->Impuestos->Traslados->Traslado[$j]->attributes() AS $a => $b)
                                                {
                                                    if($a == 'impuesto' && strtoupper($b) == $impu[0])
                                                        $bandera1 = 1;
                                                    
                                                    if($impu[1] != 'EXENTO')
                                                    {
                                                        if($a == 'tasa' && floatval($b) == floatval($impu[1]))
                                                            $bandera2 = 1;
                                                    }
                                                    else
                                                    {
                                                        if($a == 'tasa' && $b == $impu[1])
                                                            $bandera2 = 1;
                                                    }
                                                    
                                                    if($a == 'importe')
                                                        $cantidad = $b;

                                                    if($bandera1 && $bandera2 && $cantidad)
                                                        $importe = $cantidad;
                                                }
                                            }
                                        }
                                        //unset($aa);
                                    }
                                }
                                else
                                {
                                    //Si es total, cliente o proveedor agrega el total en el importe
                                    $importe = $fac_total;
                                }

                                

                                $id_mov = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura, FormaPago, tipocambio) 
                                VALUES($id_poliza_acontia, $cont, 1, 1, ".$cp['id_cuenta'].", '$tipo_movto', $importe, '','".$automatica['nombre_poliza']." $fac_concepto $impuesto', $activo, DATE_SUB(NOW(), INTERVAL 6 HOUR), '$xmlfile', 1, $tipo_cambio)");
                                $ids_movs .= $id_mov.",";

                                //Crear carpeta y copiar xml de la factura, ya se que esta no es el controlador pero no quedaba de otra, asi que hare una excepcion.
                                if(!file_exists($ruta.$id_poliza_acontia))//Si no existe la carpeta de ese poliza la crea
                                {
                                    mkdir ($ruta.$id_poliza_acontia, 0777);
                                }
                                copy($ruta.'temporales/'.$xmlfile, $ruta.$id_poliza_acontia."/".$xmlfile);    
                                

                            }
                            $this->query("UPDATE app_recepcion_xml SET id_poliza_mov = '$ids_movs' WHERE id = $last_id");
                            $ids_movs = '';
                        }
                    }
                }


                //Termina conexion con acontia
                ////////////////////////////////////////////////////////////
                ////////////////////////////////////////////////////////////

            }


$cont_xml = simplexml_load_file('../../modulos/cont/xmls/facturas/temporales/'.$xmlfile);

$json = $this->xmlToArray($cont_xml);
if( isset($json['Comprobante']['@Version']) ){
    //3.3
    $folio = $json['Comprobante']['@Folio'];
    $uuid=$fac_uuid;
    $er='R';
    $tipo='Egreso';
    $serie= $json['Comprobante']['@Serie'];
    $emisor= $json['Comprobante']['cfdi:Emisor']['@Nombre'];
    $receptor= $json['Comprobante']['cfdi:Receptor']['@Nombre'];
    $importe= $json['Comprobante']['@Total'];
    $moneda= $json['Comprobante']['@Moneda'];
    $rfc= $json['Comprobante']['cfdi:Emisor']['@Rfc'];
    $fecha=$json['Comprobante']['@Fecha'];
    $fecha_subida=$fechahoy;
    $xml='../../modulos/cont/xmls/facturas/temporales/'.$uuid.'.xml';
    $version=$json['Comprobante']['@Version'];
    $cancelada=0;
    $json=json_encode($json,JSON_HEX_APOS);
    $temporal=1;
}else{
    //3.2
    $folio = $json['Comprobante']['@folio'];
    $uuid=$fac_uuid;
    $er='R';
    $tipo='Egreso';
    $serie= $json['Comprobante']['@serie'];
    $emisor= $json['Comprobante']['cfdi:Emisor']['@nombre'];
    $receptor= $json['Comprobante']['cfdi:Receptor']['@nombre'];
    $importe= $json['Comprobante']['@total'];
    $moneda= $json['Comprobante']['@Moneda'];
    $rfc= $json['Comprobante']['cfdi:Emisor']['@rfc'];
    $fecha=$json['Comprobante']['@fecha'];
    $fecha_subida=$fechahoy;
    $xml='../../modulos/cont/xmls/facturas/temporales/'.$uuid.'.xml';
    $version=$json['Comprobante']['@version'];
    $cancelada=0;
    $json=json_encode($json,JSON_HEX_APOS);
    $temporal=1;


}


$idFacInsert = $this->insert_id(" INSERT INTO cont_facturas (folio,uuid,er,tipo,serie,emisor,receptor,importe,moneda,rfc,fecha,fecha_subida,xml,version,cancelada,json,temporal,origen) VALUES ('$folio','$uuid','$er','$tipo','$serie','$emisor','$receptor','$importe','$moneda','$rfc','$fecha','$fecha_subida','$xmlfile','$version','$cancelada','$json','$temporal',5) ");
//$this->insert_id($myQuery);
	
		$cadTrim = trim($cadena,',');
        $cad = explode(',', $cadTrim);
        foreach ($cad as $key => $value) {
        	$updatePar = "UPDATE app_opartidas set idContFactura='".$idFacInsert."' where id=".$value;
        	$this->queryArray($updatePar);
        	
        }


            return $last_id;

        }

    function pagos_detalle($id,$t,$ori)
    {

        //Si es un cargo
        if($t == 'c')
            $tipo = '0';
        if($t == 'f')
            $tipo = $ori;
        
        $myQuery = "SELECT pr.id_pago, pr.id AS id_rel, p.fecha_pago, (pr.abono*p.tipo_cambio) AS abono, (SELECT CONCAT('(',claveSat,') ',nombre) FROM forma_pago WHERE idFormapago = p.id_forma_pago) AS forma_pago, origen, activo, id_poliza_mov 
                        FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_pago 
                        WHERE pr.id_documento = $id AND pr.id_tipo = $tipo ORDER BY pr.id";    

        return $this->query($myQuery);
    }

    // function info_car_fac($id,$t,$cp,$pos)
    // {
    //     if($t == 'c')
    //     {
    //         if(!intval($cp))
    //             $provcli = "(SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = id_prov_cli) AS provcli";
    //         else
    //             $provcli = "(SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = id_prov_cli) AS provcli";
    //         $myQuery = "SELECT id_prov_cli, $provcli, (cargo*tipo_cambio) AS cargo, concepto, tipo_cambio,fecha_pago FROM app_pagos WHERE id = $id";
    //     }
    //     if($t == 'f')
    //     {
    //         if(!intval($cp))
    //         {
    //             if(intval($pos) == 1)
    //             {
    //                 /*VENTAS POS*/
    //                 $myQuery = "SELECT v.idCliente AS id_prov_cli, 
    //                         (SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = v.idCliente) AS provcli, 
    //                         SUM(vp.monto-v.cambio) AS cargo, 
    //                         CONCAT(rf.folio,' Venta POS: ',v.idVenta) AS concepto, IF(v.tipo_cambio = 0,1,v.tipo_cambio) AS tipo_cambio, rf.fecha AS fecha_pago
    //                         FROM app_respuestaFacturacion rf
    //                         INNER JOIN app_pos_venta_pagos vp ON vp.idVenta = rf.idSale
    //                         INNER JOIN app_pos_venta v ON v.idVenta = vp.idVenta
    //                         WHERE rf.id = $id AND vp.idFormapago = 6";
    //             }
    //             if(intval($pos) == 2)
    //             {
    //                 /*XTRUCTUR*/
    //                 $myQuery = "SELECT cl.id AS id_prov_cli, 
    //                         CONCAT(cl.nombre,'**/**',cl.dias_credito) AS provcli, 
    //                         SUM(c.total) AS cargo, 
    //                         CONCAT(rf.folio,' Est. Xtructur: ',c.id) AS concepto, 1 AS tipo_cambio, rf.fecha AS fecha_pago
    //                         FROM app_respuestaFacturacion rf
    //                         INNER JOIN constru_estimaciones_bit_cliente c ON c.id = rf.idSale
    //                         LEFT JOIN comun_cliente cl ON cl.id_xtructur = c.id_cliente
    //                         WHERE rf.id = $id AND c.fp = 6";
    //             }
    //         }
    //         else
    //         {
    //             if(intval($pos) < 4)
    //             {
    //                 COMPRAS
    //                 $myQuery = "SELECT oc.id_proveedor AS id_prov_cli, 
    //                         (SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = oc.id_proveedor) AS provcli, 
    //                         SUM(CASE rq.id_moneda WHEN 1 THEN r.imp_factura WHEN 2 THEN r.imp_factura*rq.tipo_cambio END) AS cargo, 
    //                         r.concepto, rq.tipo_cambio, r.fecha_factura AS fecha_pago
    //                         FROM app_recepcion_xml r
    //                         INNER JOIN app_ocompra oc ON oc.id = r.id_oc
    //                         INNER JOIN app_requisiciones rq ON rq.id = oc.id_requisicion
    //                         WHERE r.id_oc = $id";
    //             }
    //             if(intval($pos) == 4)
    //             {
    //                 /*XTRUCTUR*/
    //                 $myQuery = "SELECT p.idPrv AS id_prov_cli, 
    //                         CONCAT(p.razon_social,'**/**',p.diascredito) AS provcli, 
    //                         SUM(e.total) AS cargo, 
    //                         CONCAT(x.folio,'<br />Obra: ',g.obra,'<br />Est: ',e.id) AS concepto, 1 AS tipo_cambio, x.fecha_fac AS fecha_pago
    //                         FROM constru_xml_pedis x
    //                         INNER JOIN constru_estimaciones_bit_prov e ON e.id = x.id_estimacion
    //                         INNER JOIN constru_generales g ON g.id = e.id_obra
    //                         LEFT JOIN mrp_proveedor p ON p.id_xtructur = e.id_prov
    //                         WHERE x.id = $id";
    //             }

    //              if(intval($pos) == 5)
    //             {
    //                 /*FACTURAS ALMACEN DIGITAL*/
    //                 $myQuery = "SELECT p.idPrv AS id_prov_cli, 
    //                         CONCAT(p.razon_social,'**/**',p.diascredito) AS provcli, 
    //                         SUM(fa.importe) AS cargo, 
    //                         fa.folio AS concepto, 1 AS tipo_cambio, fa.fecha AS fecha_pago
    //                         FROM cont_facturas fa
    //                         LEFT JOIN mrp_proveedor p ON p.rfc = fa.rfc COLLATE utf8_general_ci
    //                         WHERE fa.id = $id";
    //             }
                
    //         }

                
    //     }

    //     $datos = $this->query($myQuery);
    //     $datos = $datos->fetch_assoc();
    //     return $datos;
    // }
    

    //ver Movimientos de proveedor AM
	function verMovimientosProveedores($id){

		$sql = $this->queryarray("SELECT app_ocompra.fecha,DATE_FORMAT(app_ocompra.fecha, '%d/%m/%Y')as fecha2, app_ocompra.total, ROUND(app_ocompra.total , 2 ) as monto2,mrp_proveedor.razon_social, 'Compras' as tipo_documento
			from app_ocompra
			inner join mrp_proveedor on mrp_proveedor.idPrv = app_ocompra.id_proveedor
			where mrp_proveedor.idPrv = $id and activo = 4 order by fecha desc limit 10;");

		if($sql['total']>0){
			$JSON=array('success'=>1, 'data'=>$sql['rows'],'nombre'=>$sql['rows'][0]['razon_social']);
		}else{
			$JSON=array('success'=>0);
		}
		echo json_encode($JSON);  

	}
	function printReport($filtro){
		$query = "SELECT p.id as id_partida,p.idocompra, p.codigo, p.descripcion, oc.total as ocTotal,p.monto, p.estatus,p.noreceptor,p.fechaPago, p.idRecepcion,pr.razon_social, re.imp_factura, re.xmlfile, re.fecha_subida,
		f.uuid, CONCAT(f.serie,'',f.folio) AS folio, f.importe ,f.fecha as fechaFac, p.fechaConta, (select count(uuid_pago) from cont_facturas_relacion where uuid_relacionado = f.uuid) complemento
		from app_opartidas p
		left join app_ocompra oc on oc.id=p.idocompra
		left join mrp_proveedor pr on pr.idPrv=oc.id_proveedor
		left join app_recepcion_xml re on re.id=p.idRecepcion
		left join cont_facturas f on f.id=p.idContFactura ".$filtro;
		//echo $query;
		//exit();
        $result = $this->queryArray($query);
      
        return array('data' => $result["rows"] , 'perfil' => $_SESSION['accelog_idperfil']);
	}
	function ordenesPartidas($idProveedor,$filtro=''){
		$query = "SELECT p.id as id_partida,p.idocompra, p.codigo, p.descripcion, oc.total as ocTotal,p.monto, p.estatus,p.noreceptor,p.fechaPago, p.idRecepcion,pr.razon_social, re.imp_factura, re.xmlfile, re.fecha_subida,
		f.uuid, CONCAT(f.serie,'',f.folio) AS folio, f.importe ,f.fecha as fechaFac, p.fechaConta
		from app_opartidas p
		left join app_ocompra oc on oc.id=p.idocompra
		left join mrp_proveedor pr on pr.idPrv=oc.id_proveedor
		left join app_recepcion_xml re on re.id=p.idRecepcion
		left join cont_facturas f on f.id=p.idContFactura where oc.id_proveedor=".$idProveedor." ".$filtro.'';
		//echo $query;
		//exit();
        $result = $this->queryArray($query);
      
        return array('data' => $result["rows"] , 'perfil' => $_SESSION['accelog_idperfil']);
	}
	function contabilizar($cad){
		date_default_timezone_set("Mexico/General");
		$fechaactual = date("Y-m-d H:i:s");

		$cad2 = explode(',',trim($cad,','));
		foreach ($cad2 as $key => $value) {
			$queUp = "UPDATE app_opartidas set fechaConta='".$fechaactual."' where id=".$value;
			$this->queryArray($queUp);
		}
		return array('estatus' => true );
		
	}
	function guardaPdf($cad,$folio,$monto,$fecha,$moneda,$pdfname,$idOc){
 			date_default_timezone_set("Mexico/General");
            $fecha_subida=date('Y-m-d H:i:s'); 
            $fac_concepto = 'Factura Extranjero';
            $myQuery = "INSERT INTO app_recepcion_xml (id_oc,fecha_factura,imp_factura,xmlfile,concepto,fecha_subida,folio,moneda) VALUES ('$idOc','$fecha','$monto','$pdfname','$fac_concepto','$fecha_subida','$folio','$moneda');";
            $last_id = $this->insert_id($myQuery);

	            $cadTrim = trim($cad,',');
	            $cad = explode(',', $cadTrim);

	            foreach ($cad as $key => $value) {
	            	$updatePar = "UPDATE app_opartidas set idRecepcion='".$last_id."' where id=".$value;
	            	$insertPArt = "INSERT into app_partidas_xml(idPartida,idRecepcion) values('".$value."','".$last_id."')";
	            	$this->queryArray($updatePar);
	            	$this->queryArray($insertPArt);
	            }

	            return array('estatus' => true );
}

}
?>