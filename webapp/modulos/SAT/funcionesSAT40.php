<?php 

	/* UTF8 AZURIAN
	======================================================= */
	$azurian = array_encode($azurian);

	if($bloqueo==1){
		$JSON = array('success' =>5, 'azurian' =>json_encode($azurian), 
			'error'=>5, 
			'mensaje'=>'Facturar ticket pendiente', 
			'dump'=>$valida,
			'idVenta'=>$idVenta);
		echo json_encode($JSON);
		exit();
	}

	function array_encode(&$arr){
	  array_walk_recursive($arr, function(&$val, $key){
	  	$val=trim($val);
	  	if(!mb_check_encoding($val, 'UTF-8')) $val = utf8_encode($val); 
	  });

	  return $arr;
	}

	function numericos($var,$dot){
		if(!preg_match('/[^\d'.$dot.']/', $var)) {
			return 1;
		}else{
			return 0;
		}
	}

	function validaRFC($rfc){
		if (preg_match('/[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?/', $rfc)) {
			return 1;
		}else{
			return 0;
		}
	}

	function validacion($clave,$var){
		if($clave=='pem'){
			$open = fopen($var, "r");
			$contenido = fread($open, filesize($var));
			fclose($open);
			if($contenido!=''){
				return 1;
			}else{
				return 0;
			}
			
		}
	}
	function generaIniVigencia($rfc_cliente,$cer_cliente,$pathdc){
		$comando='openssl x509 -inform DER -in '.$cer_cliente.' -noout -startdate > "'.$pathdc.'/iniVigencia.txt"';
    	exec($comando);
    	return 1;

	}
	function generaFinVigencia($rfc_cliente,$cer_cliente,$pathdc){
		$comando='openssl x509 -inform DER -in '.$cer_cliente.' -noout -enddate > "'.$pathdc.'/finVigencia.txt"';
    	exec($comando);
    	return 1;
 
	}

	function generaPem($rfc_cliente,$key_cliente,$pwd_cliente,$pathdc){
		$pem = $pathdc.'/'.$rfc_cliente.'.pem';
		$comando="openssl pkcs8 -inform DER -in ".$key_cliente." -passin pass:'".$pwd_cliente."' -out ".$pem."";
    	exec($comando);

    	$validacion = validacion('pem',$pem);
    	return $validacion; 
	}

	function generaNoCertificado($rfc_cliente,$cer_cliente,$pathdc){
		$comando='openssl x509 -inform DER -in '.$cer_cliente.' -noout -serial > "'.$pathdc.'/noCertificado.txt"';
	    exec($comando);

	    $noCertificado_open = fopen("".$pathdc."/noCertificado.txt", "r");
	    $noCertificado = fread($noCertificado_open, filesize($pathdc.'/noCertificado.txt'));
	    fclose($noCertificado_open);

	    $noCertificado=  preg_replace("/serial=/", "", trim($noCertificado));
	    $temporal=  str_split($noCertificado);
	    $noCertificado="";
	    $i=0;
	    foreach ($temporal as $value) {
	        if(($i%2))
	        $noCertificado .= $value;
	        $i++;
	    }

    	return $noCertificado;

	}

	function generaCertificado($rfc_cliente,$cer_cliente,$pathdc){
		$comando='openssl x509 -inform DER -in '.$cer_cliente.' > "'.$pathdc.'/certificado.txt"';  
	    exec($comando); 

	    $certificado_open = fopen($pathdc.'/certificado.txt', "r");
	    $certificado = fread($certificado_open, filesize($pathdc.'/certificado.txt'));
	    fclose($certificado_open);

	    $certificado=  str_replace("-----BEGIN CERTIFICATE-----", "", $certificado);
	    $certificado=  str_replace("-----END CERTIFICATE-----", "", $certificado);
	    $certificado=  str_replace("\n", "", $certificado);
	    $certificado= trim($certificado);
	    return $certificado;
	}

	function generaCadenaOriginal($cad,$dteTrailer,$rfc_cliente,$pathdc){
		$archivo = fopen($pathdc.'/CO' . $dteTrailer . '.txt','w');
		fwrite($archivo,$cad);
		fclose($archivo);
		return 1;
	
	}
	
	function generaSello($rfc_cliente,$dteTrailer,$pathdc){
		$pem = $pathdc.'/'.$rfc_cliente.'.pem';
		$comando="openssl dgst -sha256 -sign ".$pem." '".$pathdc."/CO" .$dteTrailer. ".txt' | openssl enc -base64 -A -out ".$pathdc."/sello" .$dteTrailer. ".txt"; 
	  	exec($comando);

		$sello_open = fopen($pathdc.'/sello' . $dteTrailer . '.txt', "r");
		$sello = fread($sello_open, filesize($pathdc.'/sello' . $dteTrailer . '.txt'));
		fclose($sello_open);

	  	$sello=trim($sello);

	  	unlink($pathdc.'/sello' . $dteTrailer . '.txt');
	  	unlink($pathdc.'/CO' . $dteTrailer .'.txt');

	  	return $sello;
	}

			/* VALIDACION RFC'S
			======================================================= */
			$r1=validaRFC($azurian['Emisor']['Rfc']);
			$r2=validaRFC($azurian['Receptor']['Rfc']);
			if($r1==0){
				$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
					'error'=>113, 
					'mensaje'=>'El RFC Emisor esta mal formado'.$azurian['Emisor']['Rfc'], 
					'dump'=>'',
					'idVenta'=>$idVenta);
				echo json_encode($JSON);
				exit();
			}
			if($r2==0){
				$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
					'error'=>114, 
					'mensaje'=>'El RFC Receptor esta mal formado'.$azurian['Receptor']['Rfc'], 
					'dump'=>'',
					'idVenta'=>$idVenta);
				echo json_encode($JSON);
				exit();
			}


				/* VALIDACION DE IVA


			/* VALIDACION ARREGLO AZURIAN================================================
			 */
			$cadenaOriginal=array();

			$vacios=array();
			$obligatorios=array('Moneda','metodoDePago','LugarExpedicion','version','fecha','formaDePago','tipoDeComprobante','subTotal','total');
			
			foreach ($azurian['Basicos'] as $key => $value) {
				if($key=='subTotal' || $key=='subTotal'){
					if(numericos($value,'.')==0){
						$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
							'error'=>120, 
							'mensaje'=>'Los datos numericos (Sub total o Total) estan incorrectos', 
							'dump'=>$valida,
							'idVenta'=>$idVenta);
						echo json_encode($JSON);
						exit();
					}
				}
				if($value==''){
					$vacios[]=$key;
				}
			}
			$valida = array_intersect($obligatorios, $vacios);

			if(count($valida)>=1){
				$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
						'error'=>101, 
						'mensaje'=>'Los datos basicos estan vacios o mal capturados', 
						'dump'=>$valida,
						'idVenta'=>$idVenta);
				echo json_encode($JSON);
				exit();
			}

			$vacios=array();
			$obligatorios=array('rfc','nombre');
			foreach ($azurian['Emisor'] as $key => $value) {
				if($value==''){
					$vacios[]=$key;
				}
			}
			$valida = array_intersect($obligatorios, $vacios);
			if(count($valida)>=1){
				$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
						'error'=>102, 
						'mensaje'=>'RFC o Razon Social del emisor estan vacios o mal capturados', 
						'dump'=>$valida,
						'idVenta'=>$idVenta);
				echo json_encode($JSON);
				exit();
			}

			$vacios=array();
			$obligatorios=array('calle','noExterior','colonia','localidad','municipio','estado','pais','codigoPostal');
			foreach ($azurian['FiscalesEmisor'] as $key => $value) {
				if($key=='codigoPostal'){
					if(numericos($value,'')==0){
						$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
							'error'=>121, 
							'mensaje'=>'El Codigo Postal del emisor es incorrecto', 
							'dump'=>$valida,
							'idVenta'=>$idVenta);
						echo json_encode($JSON);
						exit();
					}
				}
				if($value==''){
					$vacios[]=$key;
				}
			}
			$valida = array_intersect($obligatorios, $vacios);
			if(count($valida)>=1){
				$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
						'error'=>103, 
						'mensaje'=>'Los datos de direccion del emisor estan vacios o mal capturados', 
						'dump'=>$valida,
						'idVenta'=>$idVenta);
				echo json_encode($JSON);
				exit();
			}
	$vacios=array();
	$obligatorios=array('Rfc','Nombre');
	foreach ($azurian['Receptor'] as $key => $value) {
		if($value==''){
			$vacios[]=$key;
		}
	}	

	/*if($azurian['Receptor']['Rfc']=='XAXX010101000'){
		$azurian['Receptor']['Nombre'] = 'Factura Generica';
	} */

	$valida = array_intersect($obligatorios, $vacios);
	if(count($valida)>=1 && $azurian['Receptor']['Rfc']!='XAXX010101000'){
		$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
				'error'=>105, 
				'mensaje'=>'RFC o Razon Social del receptor estan vacios o mal capturados', 
				'dump'=>$valida,
				'idVenta'=>$idVenta);
		echo json_encode($JSON);
		exit();
	}


		if($azurian['Conceptos']['conceptos']==''){
			$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
					'error'=>107, 
					'mensaje'=>'La cadena de conceptos esta mal formada o vacia', 
					'dump'=>$conceptos,
					'idVenta'=>$idVenta);
			echo json_encode($JSON);
			exit();
		}	

		if (!isset($comPago)){
		//	if($soloExento != 0){
				if($azurian['Impuestos']['ivas']==''){
					$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
							'error'=>108, 
							'mensaje'=>'La cadena de impuestos esta mal formada o vacia', 
							'dump'=>$conceptos,
							'idVenta'=>$idVenta);
					echo json_encode($JSON);
					exit();
				}
			//}

		}
			
		//$azurian['Receptor']['Nombre'] =  str_replace('"','&quot;',$azurian['Receptor']['Nombre']);
        //$azurian['Receptor']['Nombre'] =  str_replace("'","&apos;",$azurian['Receptor']['Nombre']);
		/*$pathdc = APPPATH.'../assets/SAT/cliente';
		//echo $pathdc;
		//exit();
		$resDF = $this->m_facturatienda->emisorDatosFacturacion();
		$cer_cliente = $pathdc . '/'.$resDF['cer'];
		$key_cliente = $pathdc . '/'.$resDF['key'];
		$pwd_cliente = $resDF['password'];
		$rfc_cliente = $azurian['Emisor']['Rfc'];*/
		$noc = generaNoCertificado($rfc_cliente,$cer_cliente,$pathdc);
		//echo $noc;
		
			/* FORMAR CADENA ORGINAL
		======================================================= */
		$cadOri=$azurian['Basicos']['Version'];
		$cadOri.='|'.$azurian['Basicos']['Serie'];
		$cadOri.='|'.$azurian['Basicos']['Folio'];
		$cadOri.='|'.$azurian['Basicos']['Fecha'];
		$cadOri.='|'.$azurian['Basicos']['FormaPago'];
		$cadOri.='|'.$noc;
		$cadOri.='|'.$azurian['Basicos']['SubTotal'];
		//$cadOri.='|'.$azurian['Basicos']['formaDePago'];
		//$cadOri.='|'.$azurian['Basicos']['SubTotal'];
		/*if($azurian['Basicos']['Descuento'] > 0){
			$cadOri.='|'.$azurian['Basicos']['Descuento'];
		} */
		
		$cadOri.='|'.$azurian['Basicos']['Moneda'];
		if($azurian['Basicos']['Moneda']!='MXN'){
			//$cadOri.='|'.$azurian['Basicos']['TipoCambio'];
		}
		
		$cadOri.='|'.$azurian['Basicos']['Total'];
		$cadOri.='|'.$azurian['Basicos']['TipoDeComprobante'];
		$cadOri.='|'.$azurian['Basicos']['Exportacion'];
		$cadOri.='|'.$azurian['Basicos']['MetodoPago'];
		$cadOri.='|'.$azurian['Basicos']['LugarExpedicion'];
		/*if($azurian['Basicos']['NumCtaPago']!=''){
			$cadOri.='|'.$azurian['Basicos']['NumCtaPago'];
		} */
		/*if($azurian['Realacionados']['cadena']!=''){
			$cadOri.='|'.trim($azurian['Realacionados']['cadena'],'|');
		}*/
		$cadOri.='|'.$azurian['Emisor']['Rfc'];
		$cadOri.='|'.$azurian['Emisor']['Nombre'];
		$cadOri.='|'.$azurian['Emisor']['RegimenFiscal'];
		/*$cadOri.='|'.$azurian['FiscalesEmisor']['calle'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['noExterior'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['colonia'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['localidad'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['municipio'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['estado'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['pais'];
		$cadOri.='|'.$azurian['FiscalesEmisor']['codigoPostal']; 
		$cadOri.='|'.$azurian['Regimen']['Regimen'];*/
		$cadOri.='|'.$azurian['Receptor']['Rfc'];
		$cadOri.='|'.$azurian['Receptor']['Nombre'];
		$cadOri.='|'.$azurian['Receptor']['DomicilioFiscalReceptor'];
		$cadOri.='|'.$azurian['Receptor']['RegimenFiscalReceptor'];
		$cadOri.='|'.$azurian['Receptor']['UsoCFDI'];
		/*$cadOri.='|'.$azurian['DomicilioReceptor']['calle'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['noExterior'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['colonia'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['localidad'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['municipio'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['estado'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['pais'];
		$cadOri.='|'.$azurian['DomicilioReceptor']['codigoPostal']; */
		/*----- Comillas ------*/
		$azurian['Conceptos']['conceptosOri']= preg_replace('/&quot;/', '"', $azurian['Conceptos']['conceptosOri']);
		$azurian['Conceptos']['conceptosOri']= preg_replace('/&apos;/', "'", $azurian['Conceptos']['conceptosOri']);

		$cadOri.=$azurian['Conceptos']['conceptosOri'];
		$cadOri.=$azurian['Impuestos']['isr'];
		$cadOri.=$azurian['Impuestos']['iva'];
		//$cadOri.=$azurian['Impuestos']['rtp_cad'];
		if ($comPago==1){
			$cadOri.=$azurian['ComprobantePago']['cadena'];
		}
		///Adenda INE
		$cadOri.=$azurian['adenda']['cadena'];
		//print_r($azurian['Conceptos']['conceptosOri']);
		$cadOri=preg_replace('/\|{2,}/', '|',$cadOri);
		$cadOri=preg_replace('/ {2,}/', ' ',$cadOri);
		$cadOri='||'.$cadOri.'||';
		/* FUCIONES GENERAR SELLOS Y CERTIFICADOS
			============================================================ */

			
			$pem = generaPem($rfc_cliente,$key_cliente,$pwd_cliente,$pathdc);
			 $azurian['Receptor']['Nombre'] =  str_replace('"','&quot;',$azurian['Receptor']['Nombre']);
             $azurian['Receptor']['Nombre'] =  str_replace("'",'&apos;',$azurian['Receptor']['Nombre']);
			if($pem!=1){
				$JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
						'error'=>109, 
						'mensaje'=>'Error al leer el certificado pem, no es posible facturar con esta instancia', 
						'dump'=>'',
						'idVenta'=>$idVenta);
				echo json_encode($JSON);
				exit();
			}


			$ini = generaIniVigencia($rfc_cliente,$cer_cliente,$pathdc);
			$fin = generaFinVigencia($rfc_cliente,$cer_cliente,$pathdc);
			$noc = generaNoCertificado($rfc_cliente,$cer_cliente,$pathdc);
			$cer = generaCertificado($rfc_cliente,$cer_cliente,$pathdc);
			$fff = date('YmdHis').rand(100,999);
			$ori = generaCadenaOriginal($cadOri,$fff,$rfc_cliente,$pathdc);
			$sel = generaSello($rfc_cliente,$fff,$pathdc);	

			/* GENERAR XML
			======================================================== */
			$idComprobante=time();
			//$azurian['Receptor']['Nombre'] =  str_replace('"','&quot;',$azurian['Receptor']['Nombre']);
        	//$azurian['Receptor']['Nombre'] =  str_replace("'","&apos;",$azurian['Receptor']['Nombre']);
			//$azurian['Receptor']['Nombre'] =  str_replace('&quot;','"',$azurian['Receptor']['Nombre']);
        	//$azurian['Receptor']['Nombre'] =  str_replace("&apos;","'",$azurian['Receptor']['Nombre']);

			$azurian['Emisor']['Nombre']= preg_replace('/&/', '&amp;', $azurian['Emisor']['Nombre']);
			$azurian['Receptor']['Nombre']= preg_replace('/&/', '&amp;', $azurian['Receptor']['Nombre']);
			$azurian['Conceptos']['conceptos']= preg_replace('/&/', '&amp;', $azurian['Conceptos']['conceptos']);
			$azurian['Emisor']['Rfc']= preg_replace('/&/', '&amp;', $azurian['Emisor']['Rfc']);
			$azurian['Receptor']['Rfc']= preg_replace('/&/', '&amp;', $azurian['Receptor']['Rfc']);
			$azurian['Receptor']['Nombre'] =  str_replace('"','&quot;',$azurian['Receptor']['Nombre']);
            $azurian['Receptor']['Nombre'] =  str_replace("'","&apos;",$azurian['Receptor']['Nombre']);
		/*----Comillas -----*/
		    $azurian['Conceptos']['conceptos']= preg_replace('/&amp;quot;/', '&quot;', $azurian['Conceptos']['conceptos']);
		    $azurian['Conceptos']['conceptos']= preg_replace('/&amp;apos;/', '&apos;', $azurian['Conceptos']['conceptos']);

		    $azurian['Receptor']['Nombre'] =  str_replace('&quot;','&amp;quot;',$azurian['Receptor']['Nombre']);
        	$azurian['Receptor']['Nombre'] =  str_replace("&apos;","&amp;apos;",$azurian['Receptor']['Nombre']);
			$XML='';
			/*$XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/3' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd'"; */
			//echo $comPago.'>>';
			if ($comPago==0){
				if($azurian['adenda']==''){
					$XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/4' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd'";
				}else{
					/*$XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/4' xmlns:ine='http://www.sat.gob.mx/ine' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/ine http://www.sat.gob.mx/sitio_internet/cfd/ine/ine11.xsd'"; 
					$XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/4' xmlns:ine='http://www.sat.gob.mx/ine' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/ine http://www.sat.gob.mx/sitio_internet/cfd/ine/ine11.xsd'";*/
					$XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/4' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:ine='http://www.sat.gob.mx/ine' xsi:schemaLocation='http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/ine http://www.sat.gob.mx/sitio_internet/cfd/ine/ine11.xsd'";
				}
				 
			}else{
				//echo '333';
				//exit();
				///Comprobante de pago
				/*$XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/3' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd'"; */
				 $XML.="<?xml version='1.0' encoding='UTF-8'?><cfdi:Comprobante xmlns:cfdi='http://www.sat.gob.mx/cfd/4' xmlns:pago20='http://www.sat.gob.mx/Pagos20' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/Pagos20 http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd'";
			}	
			
			
			/*$XML.='<?xml version="1.0" encoding="UTF-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"'; */


			foreach ($azurian['Basicos'] as $key => $value) {
				if($key=='Sello'){
					$value=$sel;
				}
				if($key=='NoCertificado'){
					$value=$noc;
				}
				if($key=='Certificado'){
					$value=$cer;
				}
				if($value!=''){
					if($key!='NumCtaPago'){
						$XML.=" ".$key."='".$value."'";
						//$XML.=' '.$key.'="'.$value.'"';
					}
				}
			}
			$XML.='>';

			/*if($azurian['Realacionados']['xml']!=''){
				$XML.=$azurian['Realacionados']['xml'];
			} */


			$XML.='<cfdi:Emisor';
			foreach ($azurian['Emisor'] as $key => $value) {
				if($value!=''){
					$XML.=" ".$key."='".$value."'";
					//$XML.=' '.$key.'="'.$value.'"';
				}
			}
			$XML.='/>';

			/*$XML.='<cfdi:DomicilioFiscal';
			foreach ($azurian['FiscalesEmisor'] as $key => $value) {
				if($value!=''){
					$XML.=" ".$key."='".$value."'";
				}
			}
			$XML.='/>'; */
			/*$XML.='<cfdi:RegimenFiscal';
			foreach ($azurian['Regimen'] as $key => $value) {
				if($value!=''){
					$XML.=" ".$key."='".$value."'";
				}
			}
			$XML.='/>'; */
			$XML.='<cfdi:Receptor';
			foreach ($azurian['Receptor'] as $key => $value) {
				if($value!=''){
					$XML.=" ".$key."='".$value."'";
					//$XML.=' '.$key.'="'.$value.'"';
				}
			}
			$XML.='/>';

			/*if($azurian['Receptor']['rfc']=='XAXX010101000'){

			}else{
				/*$XML.='<cfdi:Domicilio';
				foreach ($azurian['DomicilioReceptor'] as $key => $value) {
					if($value!=''){
						$XML.=" ".$key."='".$value."'";
					}
				}
				$XML.='/>'; */
			//}*/


			$XML.='';
			$XML.='<cfdi:Conceptos>'.$azurian['Conceptos']['conceptos'].'</cfdi:Conceptos>';

			if ($comPago==0){
				//if($soloExento != 0){
					$XML.='<cfdi:Impuestos ';
					if ($azurian['Impuestos']['totalImpuestosRetenidos']>0) {
						$XML.="TotalImpuestosRetenidos='".$azurian['Impuestos']['totalImpuestosRetenidos']."' ";
						//$XML.='TotalImpuestosRetenidos="'.$azurian['Impuestos']['totalImpuestosRetenidos'].'" ';
					}
					$XML.=" TotalImpuestosTrasladados='".$azurian['Impuestos']['totalImpuestosTrasladados']."'>";
					//$XML.=' TotalImpuestosTrasladados="'.$azurian['Impuestos']['totalImpuestosTrasladados'].'">';
					$XML.=$azurian['Impuestos']['ivas'];
					//$XML.='</cfdi:Impuestos><cfdi:Complemento>'.'</cfdi:Complemento>'.$azurian['adenda']['xml'].'</cfdi:Comprobante>';
					$XML.='</cfdi:Impuestos><cfdi:Complemento>'.$azurian['adenda']['xml'].'</cfdi:Complemento></cfdi:Comprobante>';				
				//}else{
					//$XML.='<cfdi:Complemento></cfdi:Complemento>'.$azurian['adenda'].'</cfdi:Comprobante>';
				//}

			}else{
				$XML.='<cfdi:Complemento>'.$azurian['ComprobantePago']['xml'].'</cfdi:Complemento>'.$azurian['adenda']['xml'].'</cfdi:Comprobante>';
			}

			

			$azurian['Emisor']['Nombre']= preg_replace('/&amp;/', '&', $azurian['Emisor']['Nombre']);
			$azurian['Receptor']['Nombre']= preg_replace('/&amp;/', '&', $azurian['Receptor']['Nombre']);
			$azurian['Conceptos']['conceptos']= preg_replace('/&amp;/', '&', $azurian['Conceptos']['conceptos']);
			$azurian['Emisor']['Rfc']= preg_replace('/&amp;/', '&', $azurian['Emisor']['Rfc']);
			$azurian['Receptor']['Rfc']= preg_replace('/&amp;/', '&', $azurian['Receptor']['Rfc']);
			/*----Comillas ------*/
			$azurian['Conceptos']['conceptos']= preg_replace('/&apos;/', "'", $azurian['Conceptos']['conceptos']);
			$azurian['Conceptos']['conceptos']= preg_replace('/&quot;/', '"', $azurian['Conceptos']['conceptos']);
			//echo '<br><br>';
			//echo $cadOri;
			//echo '<br><br>';
			//$XML = str_replace("'",'"',str_replace(">","&gt;",str_replace("<","&lt;",trim($XML))));
			//echo $XML;
			//exit();
			$archivo = fopen('../../modulos/SAT/xml33.xml','w');
			
			fwrite($archivo,$XML);
			fclose($archivo);

				//INVOCAR WSINVOICE
	//if(!isset($positionPath))
	//	$positionPath="../../modulos";


	if(!isset($positionPath))
		$positionPath="../../modulos";
	

	if(isset($kiosko)){
		$positionPath="../webapp/modulos";
	}

	if(isset($transport)){
			$positionPath = "../../modulos";
	}
//echo '?'.$positionPath.'/wsinvoice/sealInvoice.php';


	$strPassword = $passPAC;
	$strUser = $userPAC;
	if($strUser=='pruebasWS'){
		$urlPac = 'http://dev33.facturacfdi.mx/WSTimbradoCFDIService?wsdl';
	}else{
		$urlPac = 'https://v33.facturacfdi.mx/WSTimbradoCFDIService?wsdl';
	}

	$strXMLString .= str_replace("'",'"',str_replace(">","&gt;",str_replace("<","&lt;",str_replace("&amp;","&amp;amp;",trim($XML)))));

	$strXMLBody = '<S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/"><S:Body><ns3:TimbrarCFDI xmlns:ns3="http://wservicios/" xmlns:xmime="http://www.w3.org/2005/05/xmlmime"><accesos><password>' . $strPassword . '</password><usuario>' . $strUser . '</usuario></accesos><comprobante>' . $strXMLString . '</comprobante></ns3:TimbrarCFDI></S:Body></S:Envelope>';

    $arrHeaders = array('Content-Type: text/xml; charset="utf-8"', 'Content-Length: ' . strlen($strXMLBody), 'Accept: text/xml', 'Cache-Control: no-cache', 'Pragma: no-cache', 'SOAPAction: "TimbrarCFDI"');

     $strXMLString2 = $strXMLString;
        unset($strXMLString);

        //$arrHeaders = array('Content-Type: text/xml; charset="utf-8"', 'Content-Length: ' . strlen($strXMLBody), 'Accept: text/xml', 'Cache-Control: no-cache', 'Pragma: no-cache', 'SOAPAction: "TimbrarCFDI"');

        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curlObj, CURLOPT_URL, $urlPac);
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlObj, CURLOPT_TIMEOUT, 180);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, $arrHeaders);
        curl_setopt($curlObj, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curlObj, CURLOPT_POST, true);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $strXMLBody);
        curl_setopt($curlObj, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $xmlResult = curl_exec($curlObj);
        //var_dump($xmlResult);
        //exit();
	    $arrResult = array('strStatus'=>'','strCode'=>'','strResult'=>'');
	    $objDOM = new DOMDocument();
	    $objDOM->loadXML($xmlResult);
	    $xmlNode = $objDOM->getElementsByTagName('xmlTimbrado');
	    foreach($xmlNode as $objNode){
	        $strSealedXML = $objDOM->getElementsByTagName('xmlTimbrado')->item(0)->nodeValue;

		$xmlDTResp = explode('<cfdi:Complemento>',$strSealedXML);
	        $pcad=explode('UUID="',$xmlDTResp[1]);
	        $cad=explode('"',$pcad[1]);

	        $arrResult['strStatus'] = '1';
	       	//$arrResult['strCode'] = substr($strSealedXML,strpos($strSealedXML,"UUID=") + 6,36);
		$arrResult['strCode'] = $cad[0];
	        
		$arrResult['strResult'] = base64_encode($strSealedXML);
	        unset($strSealedXML);
	    }
	    unset($objNode);
	    unset($xmlNode);
	    $xmlNode = $objDOM->getElementsByTagName('error');
	    foreach($xmlNode as $objNode){
	        $arrResult['strStatus'] = '0';
	        $arrResult['strCode'] = '00';
	        $arrResult['strResult'] = $objDOM->getElementsByTagName('error')->item(0)->nodeValue;
	    }
	    unset($objNode);
	    unset($xmlNode);
	    $xmlNode = $objDOM->getElementsByTagName('codigoError');
	    foreach($xmlNode as $objNode){
	    	
	        $arrResult['strStatus'] = '0';
	        $arrResult['strCode'] = (string) $objDOM->getElementsByTagName('codigoError')->item(0)->nodeValue;
	        $arrResult['strResult'] = (string) $objDOM->getElementsByTagName('error')->item(0)->nodeValue;
	    } 
	 
	    unset($objNode);
	    unset($xmlNode);
	    unset($objDOM);

	    if($arrResult['strStatus']=='1'){
	        $xmlorigi=base64_decode($arrResult['strResult']);
	        //$xmlBasicTimbrados = 
	        $xmlDTResp = explode('<cfdi:Complemento>',$xmlorigi);
	        $pcad=explode('UUID="',$xmlDTResp[1]);
	        $cad=explode('"',$pcad[1]);
	        $datosTimbrado['UUID']=$cad[0];
	        $objResult[0]['strCode'] = $cad[0];
	        $pcad=explode('noCertificadoSAT="',$xmlorigi);
	        $cad=explode('"',$pcad[1]);
	        $datosTimbrado['noCertificadoSAT']=$cad[0];

	        $pcad=explode('selloCFD="',$xmlorigi);
	        $cad=explode('"',$pcad[1]);
	        $datosTimbrado['selloCFD']=$cad[0];

	        $pcad=explode('selloSAT="',$xmlorigi);
	        $cad=explode('"',$pcad[1]);
	        $datosTimbrado['selloSAT']=$cad[0];

	        $pcad=explode('FechaTimbrado="',$xmlorigi);
	        $cad=explode('"',$pcad[1]);
	        $datosTimbrado['FechaTimbrado']=$cad[0];
	        $datosTimbrado['idFact']=$idFact;
	        $datosTimbrado['idVenta']=$idVenta;
	        $datosTimbrado['noCertificado']=3;
	        $datosTimbrado['tipoComp']='F';
	        $datosTimbrado['csdComplemento']='|1.0|'.$datosTimbrado['UUID'].'|'.$datosTimbrado['FechaTimbrado'].'|'.$datosTimbrado['selloCFD'].'|'.$datosTimbrado['noCertificadoSAT'];

	        $azurian['datosTimbrado']=$datosTimbrado;
	        //var_dump($datosTimbrado);

	        $arrResult['strStatus'] = '1';
	        $arrResult['strMessage'] = $arrResult['strCode'];
	        $strPDFFile = '../cont/xmls/facturas/temporales/' . $arrResult['strCode']. '.xml';
	        $strXMLFile = '../cont/xmls/facturas/temporales/' . $arrResult['strCode']. '.xml';

	        /////Saber donde guardar el XML , diferencia entre lo nuevo y lo viejo.
	       /* $conDB = mysqli_connect(DB_SERVER,$strInstanceDBUser,$strInstanceDBPassword,$strInstanceDB);
	        mysqli_query($conDB,"SET NAMES '" . DB_CHARSET . "'");
	        $strSql = "SHOW TABLES LIKE 'app_respuestaFacturacion';";
	        $rstFCAuthData = mysqli_query($conDB,$strSql);
	        $tableExists = mysqli_num_rows($rstFCAuthData) > 0;
	        if($tableExists > 0){
	            $strXMLFile = INVOICE_PATHDIGITAL . $objResult[0]['strCode'] . '.xml';
	        }else{
	            $strXMLFile = INVOICE_PATHDIGITAL . $objResult[0]['strCode'] . '.xml';
	        }
	        
	        unset($objFCAuthData);
	        mysqli_free_result($rstFCAuthData);
	        unset($rstFCAuthData); 
	        */
	       
	        file_put_contents($strXMLFile,base64_decode($arrResult['strResult']));

	        //$objXmlToPDf = new invoiceXmlToPdf($strXMLFile,$strLogoEmpresa,$intRed,$intGreen,$intBlue,$strPDFFile);        
	        //$objXmlToPDf->genPDF();

	        $JSON = array('success' =>1, 
	                'estatus'=>'La factura se ha creado exitosamente.', 'azurian' =>json_encode($azurian),
	                'datos'=>$datosTimbrado,
	                'idVenta'=>$idVenta,
	                'idCliente'=>$idFact,
	                'monto'=>$azurian['Basicos']['total'],
	                'xmlfile'=>$arrResult['strCode'] . '.xml',
	                'correo'=>$azurian['Correo']['Correo']);      
	        //return $JSON;
	        echo json_encode($JSON);
	        exit();
    		unset($JSON);  	    	
	    }else{

	    	$arrResult['strStatus'] = '0';
	        $arrResult['strMessage'] = $arrResult['strCode'] . ' - ' . $arrResult['strResult'] ;

	        if($arrResult['strMessage']==''){
	            $objResult['strCode']='110';
	            $arrResult['strMessage']='Error 110, en estos momentos no es posible conectar con los servidores del SAT, intentar mas tarde.';
	        }
	        $JSON = array('success' =>0, 'azurian' =>json_encode($azurian), 
	                'error'=>$arrResult['strCode'], 
	                'mensaje'=>$arrResult['strMessage'], 
	                'dump'=>'',
                'idVenta'=>$idVenta);
	        //return $JSON;
	        echo json_encode($JSON);
	        exit();
    		unset($JSON);
	    }
	//include ($positionPath.'/wsinvoice/sealInvoice.php');

?>