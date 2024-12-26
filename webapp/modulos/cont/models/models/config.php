<?php
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

	class ConfigModel extends Connection
	{
		function importinsert($cadena)
		{
			$sql=$this->query("INSERT INTO `cont_accounts` (`account_id`, `account_code`, `manual_code`, `description`, `sec_desc`, `account_type`, `status`, `main_account`, `cash_flow`, `reg_date`, `currency_id`, `group_dig`, `id_sucursal`, `seg_neg_mov`, `affectable`, `mod_date`, `father_account_id`, `removable`, `account_nature`, `removed`,`main_father`,`cuentaoficial`)
					VALUES ( ".$cadena.";");
					
		}
		function saveConfig($act,$IdCompany,$default_catalog,$structure,$values,$level,$numpol,$rfc,$begin,$period,$periods,$current_period,$open_periods,$primera_vez,$cl_num)
		{	
			$date_begin=explode('-',$begin);//Acomoda la fecha al formato de la bd
			if(!$act)
			{
				$myQuery = "INSERT INTO cont_config (IdOrganizacion,TipoCatalogo,Estructura,TipoValores,TipoNiveles,NumPol,RFC,InicioEjercicio,FinEjercicio,TipoPeriodo,NumPeriodos,PeriodoActual,PeriodosAbiertos,EjercicioActual,PrimeraVez) 
						VALUES ($IdCompany,'$default_catalog','$structure','$values','$level',$numpol,'$rfc','$date_begin[2]-$date_begin[1]-$date_begin[0]',DATE_ADD('$date_begin[2]-$date_begin[1]-$date_begin[0]',INTERVAL 364 DAY),'$period',$periods,$current_period,$open_periods,0,0)";
			}
			else
			{
				if(intval($primera_vez))
				{
					// Inicia seccion que llena el arbol contable
					if($default_catalog<2)
					{
						$handle = ( $default_catalog == 1 ) ? fopen( 'models/fullTree.sql', "r" ) : fopen( 'models/basicTree.sql', "r" );
					    
					    $query = fread($handle,10000);
						$this->query( $query );
						fclose($handle);
	
						//INICIA//////////////////////////////////////////////////
						//Si el usuario eligio - en vez de . en la mascara de la configuracion, substituye todos los . con -.

						//Compara si la estructura es con guio entonces hace las modificaciones
						/*if(strpos($structure, '-') == true)
						{
							//Extrae todas las cuentas
							$c = $this->query("SELECT account_id,manual_code FROM cont_accounts");
							while($cn = $c->fetch_assoc())
							{
								//Substituye el caracter
								$nuevo = str_replace('.', '-', $cn['manual_code']);
								//Actualiza el registro con el nuevo caracter
								$this->query("UPDATE cont_accounts SET manual_code = '$nuevo' WHERE account_id = ".$cn['account_id']);
							}
						}*/
						//TERMINA//////////////////////////////////////////////////
						

					}
					// Inicia seccion que llena el arbol contable
					$myQuery = "UPDATE  cont_config SET 
					TipoCatalogo = '$default_catalog',
					Estructura = '$structure',
					TipoValores = '$values',
					TipoNiveles = '$level',
					NumPol = $numpol,
					RFC = '$rfc',
					ClaveOrg = '$cl_num',
					InicioEjercicio = '$date_begin[2]-$date_begin[1]-$date_begin[0]',
					FinEjercicio = DATE_ADD('$date_begin[2]-$date_begin[1]-$date_begin[0]',INTERVAL 364 DAY),
					TipoPeriodo = '$period',
					NumPeriodos = $periods,
					PeriodoActual = $current_period,
					PeriodosAbiertos = $open_periods,
					PrimeraVez = 0
					";
				}
				else
				{
					$myQuery = "UPDATE  cont_config SET 
					TipoValores = '$values',
					RFC = '$rfc',
					InicioEjercicio = '$date_begin[2]-$date_begin[1]-$date_begin[0]',
					FinEjercicio = DATE_ADD('$date_begin[2]-$date_begin[1]-$date_begin[0]',INTERVAL 364 DAY),
					TipoPeriodo = '$period',
					NumPeriodos = $periods,
					PeriodoActual = $current_period,
					PeriodosAbiertos = $open_periods,
					PrimeraVez = 0
					";
				}
			}
			$this->query( $myQuery );			
		}

		function saveConfigAccounts($compras,$ventas,$dev,$clientes,$IVA,$Caja,$TR,$Bancos,$Saldos,$Flujo,$Proveedores,$utilidades,$perdida,$CuentaIVAPendientePago,$CuentaIVApagado,$CuentaIVAPendienteCobro,$CuentaIVAcobrado,$CuentaIEPSPendientePago,$CuentaIEPSpagado,$CuentaIEPSPendienteCobro,$CuentaIEPScobrado,$CuentaDeudores,$ISH,$IVAretenido,$ISRretenido,$statusIVAIEPS,$statusRetencionISH,$iepsgasto,$statusIEPS)
		{	
			//$date_begin=explode('-',$begin);//Acomoda la fecha al formato de la bd
			
					// Inicia seccion que llena el arbol contable
					$myQuery = "UPDATE  cont_config SET 
					CuentaCompras = -1,
					CuentaVentas = $ventas,
					CuentaDev = -1,
					CuentaClientes = $clientes,
					CuentaIVA = $IVA,
					CuentaCaja = $Caja,
					CuentaTR = -1,
					CuentaBancos = $Bancos,
					CuentaSaldos = $Saldos,
					CuentaFlujoEfectivo = -1,
					CuentaProveedores = $Proveedores,
					CuentaUtilidad = $utilidades,
					CuentaPerdida = $perdida,
					CuentaIVAPendientePago = $CuentaIVAPendientePago,
					CuentaIVApagado = $CuentaIVApagado,
					CuentaIVAPendienteCobro = $CuentaIVAPendienteCobro,
					CuentaIVAcobrado = $CuentaIVAcobrado,
					CuentaIEPSPendientePago = $CuentaIEPSPendientePago,
					CuentaIEPSpagado = $CuentaIEPSpagado,
					CuentaIEPSPendienteCobro = $CuentaIEPSPendienteCobro,
					CuentaIEPScobrado = $CuentaIEPScobrado,
					CuentaDeudores = $CuentaDeudores,
					ISH = $ISH,
					ISRretenido = $ISRretenido,
					IVAretenido = $IVAretenido,
					CuentaIEPSgasto = $iepsgasto,
					statusIVAIEPS = $statusIVAIEPS,
					statusRetencionISH = $statusRetencionISH,
					statusIEPS = $statusIEPS
					WHERE id=1
					";
				
			$this->query( $myQuery );			
		}

		function getAllExercises()
		{
			$myQuery = "SELECT e.Id, e.NombreEjercicio, e.Cerrado, c.EjercicioActual FROM cont_ejercicios e LEFT JOIN cont_config c ON c.EjercicioActual = e.NombreEjercicio ORDER BY NombreEjercicio";
			$companies = $this->query($myQuery);
			return $companies;
		}


		function getCompanyName($idorg)
		{
			$myQuery = "SELECT nombreorganizacion FROM organizaciones WHERE idorganizacion=".$idorg;
			$companies = $this->query($myQuery);
			$com = $companies->fetch_assoc();
			return $com;
		}

		function getExerciseName($idex)
		{
			$myQuery = "SELECT NombreEjercicio FROM cont_ejercicios WHERE Id=".$idex;
			$exercises = $this->query($myQuery);
			$ex = $exercises->fetch_assoc();
			return $ex;
		}

		function getExerciseInfo()
		{
			$myQuery = "SELECT * FROM cont_config";
			$info = $this->query($myQuery);
			$inform = $info->fetch_assoc();
			return $inform;
		}

		function Establecer($EjActivo)
		{
			$myQuery = " UPDATE cont_config SET InicioEjercicio = '$EjActivo-01-01',FinEjercicio = '$EjActivo-12-31',EjercicioActual = $EjActivo";
			$companies = $this->query($myQuery);
		}

		//Proceso que cierra el ejercicio
		function CloseExercise($Id,$Ejercicio) 
		{
			//Busca si la poliza del periodo 13 ha sido generada
			$myQuery = "SELECT id FROM cont_polizas WHERE idejercicio = $Id AND idperiodo = 13 AND activo=1";
			$existePoliza = $this->query($myQuery);
			$poliza13 = mysqli_num_rows($existePoliza);

			if($poliza13)
			{

				//Crear nuevo periodo
				$anteriorCerrado = $this->anteriorCerrado($Ejercicio);//bandera, El ejercicio anterior ha sido cerrado
				if(intval($anteriorCerrado))
				{
					//Cerrar ejercicio
					$myQuery = "UPDATE cont_ejercicios SET Cerrado=1 WHERE Id=".$Id;
					$this->query($myQuery);
					
					$NuevoEjercicio = $Ejercicio+2;
					$myQuery = "INSERT INTO cont_ejercicios (NombreEjercicio,Cerrado) VALUES ($NuevoEjercicio,0)";
					$this->query($myQuery);
					$AgregaPolizaPDV = "UPDATE cont_polizas SET idejercicio = (SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = $NuevoEjercicio) WHERE fecha BETWEEN '$NuevoEjercicio-01-01' AND '$NuevoEjercicio-12-31' AND pdv_aut = 1";
					$this->query($AgregaPolizaPDV);
				
					//Liberar Memoria
					unset($Id,$Ejercicio,$myQuery,$NuevoEjercicio);
				
					return 'Si';
				}
				else
				{
					return 'NoCerradoAnterior';
				}
			}
			else
			{
				return 'No';
			}
		}

		function anteriorCerrado($Ejercicio)
		{
			$myQuery = "SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = $Ejercicio";
			$Actual = $this->query($myQuery);
			$Actual = $Actual->fetch_assoc();
			if(intval($Actual['Id']) > 1)
			{
				$Ejercicio = intval($Ejercicio)-1;
				$myQuery = "SELECT Cerrado FROM cont_ejercicios WHERE NombreEjercicio = $Ejercicio";
				$Existe = $this->query($myQuery);
				$Existe = $Existe->fetch_assoc();
				return $Existe['Cerrado'];
			}

			return '1';
		}
		function IsEmpty()
		{
			$myQuery = "SELECT Id FROM cont_ejercicios";
			$IsEmpty = $this->query($myQuery);
			$IE = mysqli_num_rows($IsEmpty);
			return $IE;
		}
			function FirstExercise($Ejercicio)
		{
			$myQuery = "INSERT INTO cont_ejercicios (NombreEjercicio,Cerrado) VALUES ($Ejercicio,0)";
			$this->query($myQuery);
			$myQuery = "INSERT INTO cont_config (IdOrganizacion,TipoCatalogo,Estructura,TipoValores,TipoNiveles,NumPol,RFC,InicioEjercicio,FinEjercicio,TipoPeriodo,NumPeriodos,PeriodoActual,PeriodosAbiertos,EjercicioActual,PrimeraVez) 
						VALUES ( 1, 1, '999.9999', 'n', 'a',0, '', '$Ejercicio-01-01', '$Ejercicio-12-31', 'm', 12, 1, 0, $Ejercicio,1)";
			$this->query($myQuery);
			$myQuery = "INSERT INTO cont_config_pdv (historial,conectar,polizas_por) 
						VALUES (0,0,0)";
			$this->query($myQuery);

			//Agrega las polizas del punto de venta para este ejercicio
			$AgregaPolizaPDV = "UPDATE cont_polizas SET idejercicio = (SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = $Ejercicio) WHERE fecha BETWEEN '$Ejercicio-01-01' AND '$Ejercicio-12-31' AND pdv_aut = 1";
			$this->query($AgregaPolizaPDV);

			//Crea un ejercicio extra posterior, al crearlo por primera vez
			$Ejercicio = intval($Ejercicio)+1;
			$myQuery = "INSERT INTO cont_ejercicios (NombreEjercicio,Cerrado) VALUES ($Ejercicio,0)";
			$this->query($myQuery);
		}
		function getRFC()
		{
			$myQuery = "SELECT RFC FROM organizaciones";
			$rfc = $this->query($myQuery);
			$rfc = $rfc->fetch_assoc();
			return $rfc['RFC'];
		}
		
		// Inician Metodos de obtencion de cuentas afectables y tipo de cuenta
			function getAccounts($code)
			{
				$myQuery = "SELECT account_id, description ".$code." FROM cont_accounts 
				WHERE status=1 AND removed=0 AND affectable=1 AND account_id NOT IN (select father_account_id FROM cont_accounts WHERE removed=0)";
				$ListaCuentas = $this->query($myQuery);
				return $ListaCuentas;	
			}
			// para sacar solo las cuentas de mayor
			function cuentasmayor(){
				$sql=$this->query("SELECT account_id,manual_code,description FROM cont_accounts 
				WHERE status=1 AND removed=0 AND affectable=0 AND main_account=1");
				return $sql;
			}

			function CuentaTipoCaptura()
			{
				$myQuery = "SELECT TipoNiveles FROM cont_config";
				$valor = $this->query($myQuery);
				$valor = $valor->fetch_assoc();
				if($valor['TipoNiveles'] == 'a') $resultado = 'account_code';
				if($valor['TipoNiveles'] == 'm') $resultado = 'manual_code';

				return $resultado;
			}
		// Terminan Metodos de obtencion de cuentas afectables y tipo de cuenta

		public function getNumAccounts()
		{
			$sql  = "SELECT ";
			$sql .= "	COUNT(*) ";
			$sql .= "FROM ";
			$sql .= "cont_accounts;";
			$result = $this->query( $sql );

			$result = $result->fetch_array( MYSQLI_NUM );
			return  $result[0];
		}

		function getConfigAccount($Account)
		{

			$myQuery = "SELECT $Account FROM cont_config WHERE id=1";
			$GetAccount = $this->query($myQuery);
			$GetAccount = $GetAccount->fetch_assoc();
			if($GetAccount[$Account] == '')
			{
				$GetAccount[$Account] = -1;
			}
			return $GetAccount[$Account];	
		}

		function updateAccount($Account,$NewAccount)
		{
			$myQuery = "UPDATE cont_movimientos SET Cuenta = $NewAccount WHERE Cuenta = $Account";
			$this->query($myQuery);
		}

		//Comienza funciones punto de venta
		function getExerciseInfoPDV()
		{
			$myQuery = "SELECT p.*, c.EjercicioActual FROM cont_config_pdv p INNER JOIN cont_config c ON c.id=p.id where p.id=1";
			$info = $this->query($myQuery);
			$inform = $info->fetch_assoc();
			return $inform;
		}

		function claves()
		{
			$myQuery = "SELECT CONCAT(Sector_Publico,'.',Sector_Financiero,'.',Sector_Economia1,'.',Sector_Economia2,'.',Entes_Publicos) AS Clave, Descripcion FROM pre_clasif_adm ORDER BY Clave";
			$i = $this->query($myQuery);
			return $i;
		}

		function saveConfigPDV($historial,$conectar,$corte,$anterior_corte,$ventas,$clientes,$IVA,$caja,$bancos)
		{
			$historial_c = (intval($anterior_corte)) ? '' : 'historial = '.$historial.',';
			$myQuery = "UPDATE cont_config_pdv SET $historial_c 
			conectar = $conectar, 
			polizas_por = $corte,
			ventas = $ventas,
			clientes = $clientes, 
			iva = $IVA,
			caja = -1,
			bancos = -1
			WHERE id = 1";
			$this->query($myQuery);
		}

		function saveHistoryPDV($ejercicio)
		{
			for($periodo=1;$periodo<=12;$periodo++)
			{
				$numpol = $this->ultimaNumpol($ejercicio,$periodo);
				if(is_null($numpol))
				{
					$numpol=0;
				}
				for($dia=1;$dia<=31;$dia++)
				{
					$ventas = $this->ventas($ejercicio,$periodo,$dia);
					if(intval($ventas->num_rows)>0)
					{
						$numpol++;
						$Poliza = $this->poliza($ejercicio,$periodo,$dia,$numpol);
						$consulta = '';
						$NumMovto = 1;
						while($v = $ventas->fetch_assoc())
						{
							$factura = "-";
							$archivo = "../facturas/".$v['Factura'].".xml";
							if(file_exists($archivo))
							{
								global $xp;
								$newdir = "xmls/facturas/$Poliza";
								mkdir($newdir, 0777);

								$file 	= $archivo;
								$texto 	= file_get_contents($file);
								$texto 	= preg_replace('{<Addenda.*/Addenda>}is', '<Addenda/>', $texto);
								$texto 	= preg_replace('{<cfdi:Addenda.*/cfdi:Addenda>}is', '<cfdi:Addenda/>', $texto);
								$xml 	= new DOMDocument();
								$xml->loadXML($texto);
								
								$xp = new DOMXpath($xml);
								$data['uuid'] 	= $this->getpath("//@UUID");
								$data['folio'] 	= $this->getpath("//@folio");
								$data['emisor'] = $this->getpath("//@nombre");
								$factura = $data['folio']."_".$data['emisor'][0]."_".$data['uuid'].".xml";
								copy($archivo,$newdir."/".$factura);
							}
							if(floatval($v['montoMenosImpuestos'])>0)
								{
									$prodventas = $this->ventaProductos($v['idVenta']);
									while($pv = $prodventas->fetch_assoc())
									{
										if(intval($pv['idCuenta']) != 0)
										{
											$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura) 
													VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",".$pv['idCuenta'].",'Abono',".number_format($pv['SubTotal'],2,'.','').",'Ventas Linea ".$pv['nombre']."','Venta PDV(".$v['idVenta'].")',0,NOW(),'$factura'); ";//Guarda Ventas
										}
										else
										{
											$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura) 
													VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT ventas FROM cont_config_pdv WHERE id=1),'Abono',".number_format($pv['SubTotal'],2,'.','').",'Ventas','Venta PDV(".$v['idVenta'].")',0,NOW(),'$factura'); ";//Guarda Ventas
										}
									}
									$NumMovto++;
								}
							/*if(floatval($v['montoMenosImpuestos'])>0)
							{
								$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura) 
												VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT ventas FROM cont_config_pdv WHERE id=1),'Abono',".number_format($v['montoMenosImpuestos'],2,'.','').",'Ventas','Venta PDV(".$v['idVenta'].")',0,NOW(),'-'); ";//Guarda Ventas
								$NumMovto++;
							}*/
							if(floatval($v['montoimpuestos'])>0)
							{
								$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura) 
												VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT iva FROM cont_config_pdv WHERE id=1),'Abono',".number_format($v['montoimpuestos'],2,'.','').",'IVA','Venta PDV(".$v['idVenta'].")',0,NOW(),'$factura'); ";//Guarda IVA
								$NumMovto++;
							}

							//INICIA Medios de pago----------------------------------------

							/*if(floatval($v['Efectivo'])>0)
							{
								$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto,IdSegmento,IdSucursal,Cuenta,TipoMovto,Importe,Referencia,Concepto,Activo,FechaCreacion,Factura) 
												VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT bancos FROM cont_config_pdv WHERE id=1),'Cargo',".number_format($v['Efectivo'],2,'.','').",'Bancos','Provision Venta PDV(".$v['idVenta'].")',0,NOW(),'-'); ";//Guarda Bancos
								$NumMovto++;
							}
							if(floatval($v['Credito'])>0)
							{
								$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto,IdSegmento,IdSucursal,Cuenta,TipoMovto,Importe,Referencia,Concepto,Activo,FechaCreacion,Factura,Persona) 
												VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT clientes FROM cont_config_pdv WHERE id=1),'Cargo',".number_format($v['Credito'],2,'.','').",'Cliente','Provision Venta PDV(".$v['idVenta'].")',0,NOW(),'-','1-".$v['idCliente']."'); ";//Guarda Cliente
								$NumMovto++;
							}*/
							if(intval($v['Cuenta'])>0)
								{
									$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto,IdSegmento,IdSucursal,Cuenta,TipoMovto,Importe,Referencia,Concepto,Activo,FechaCreacion,Factura,Persona) 
													VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",".$v['Cuenta'].",'Cargo',".number_format($v['monto'],2,'.','').",'Cliente','Venta PDV(".$v['idVenta'].")',0,NOW(),'$factura','1-".$v['idCliente']."'); ";//Guarda Cliente
									$NumMovto++;
								}
								else
								{
									$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto,IdSegmento,IdSucursal,Cuenta,TipoMovto,Importe,Referencia,Concepto,Activo,FechaCreacion,Factura,Persona) 
													VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT clientes FROM cont_config_pdv WHERE id=1),'Cargo',".number_format($v['monto'],2,'.','').",'Cliente','Venta PDV(".$v['idVenta'].")',0,NOW(),'$factura','1-".$v['idCliente']."'); ";//Guarda Cliente
									$NumMovto++;	
								}

							//TERMINA Medios de pago----------------------------------------
							
							/*if(floatval($v['Caja'])>0)
							{
								$consulta .= "INSERT INTO cont_movimientos(IdPoliza, NumMovto,IdSegmento,IdSucursal,Cuenta,TipoMovto,Importe,Referencia,Concepto,Activo,FechaCreacion,Factura,Persona) 
												VALUES($Poliza,$NumMovto,1,".$v['idSucursal'].",(SELECT caja FROM cont_config_pdv WHERE id=1),'Abono',".number_format($v['Credito'],2,'.','').",'Cliente','Provision Venta PDV(".$v['idVenta'].")',0,NOW(),'-','1-".$v['idCliente']."'); ";//Guarda Cliente
								$NumMovto++;
							}*/
						}
						$this->dataTransact($consulta);
					}
				}
			}
		
		}

		function getpath($qry) 
		{
			global $xp;
			$prm = array();
			$nodelist = $xp->query($qry);
			foreach ($nodelist as $tmpnode)  
			{
	    		$prm[] = trim($tmpnode->nodeValue);
	    	}
			$ret = (sizeof($prm)<=1) ? $prm[0] : $prm;
			return($ret);
		}

		function ventas($ejercicio,$periodo,$dia)
		{
			$myQuery = "SELECT 
v.idVenta,
v.idCliente, 
(SELECT cuenta FROM comun_cliente WHERE id=v.idCliente) AS Cuenta,
@Efectivo := (SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 1) AS EfectivoAntes,
@Cambio := v.cambio AS Cambio,
@Resultado:=@Efectivo - @Cambio AS EfectivoMenosCambio,
(v.monto-v.montoimpuestos) AS montoMenosImpuestos, 
v.monto,
v.montoimpuestos,
IF (@Resultado<=0,0,@Resultado)  AS Efectivo,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 2) AS Cheques,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 3) AS TarjetaRegalo,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 4) AS TarjetaCredito,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 5) AS TarjetaDebito,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 6) AS Credito,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 7) AS Transferencia,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 8) AS Spei,
(SELECT SUM(monto) FROM venta_pagos WHERE idVenta = v.idVenta AND idFormaPago = 9) AS Otro,
IF (@Resultado<0,@Resultado*-1,0)  AS Caja,
(SELECT folio FROM pvt_respuestaFacturacion WHERE idSale = v.idVenta LIMIT 1) AS Factura,
v.idSucursal,
v.fecha
FROM 	venta v 
WHERE 	fecha BETWEEN '$ejercicio-".sprintf('%02d', (intval($periodo)))."-".sprintf('%02d', (intval($dia)))." 00:00:00' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-".sprintf('%02d', (intval($dia)))." 23:59:59' 
AND v.estatus=1;";
			$ventas = $this->query($myQuery);
			return $ventas;
		}

		function ventaProductos($idVenta)
		{
			$qry = "SELECT 
SUM(vp.subtotal) AS SubTotal,
l.idCuenta, 
l.nombre
FROM venta_producto vp
INNER JOIN mrp_producto p ON p.idProducto = vp.idProducto
INNER JOIN mrp_linea l ON l.idLin = p.idLinea
WHERE idVenta = $idVenta
GROUP BY idLin";
			$data = $this->query( $qry );
			return $data;
		}		

		function poliza($ejercicio,$periodo,$dia,$numpol)
		{
			$myQuery = "INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, referencia, concepto, cargos, abonos, ajuste, fecha, fecha_creacion, activo, eliminado, pdv_aut)
							 			VALUES(1,(SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = $ejercicio),$periodo,".$numpol.",3,'','Provision Ventas PDV($dia/$periodo/$ejercicio)',0,0,0,'$ejercicio-".sprintf('%02d', (intval($periodo)))."-".sprintf('%02d', (intval($dia)))."',NOW(),0,0,1)";
			$idPoliza = $this->insert_id($myQuery);
			return $idPoliza;
		}

		function cuentasAfectables(){
				$sql=$this->query("SELECT account_id,manual_code,description FROM cont_accounts 
				WHERE status=1 AND removed=0 AND affectable=1 AND main_account=3");
				return $sql;
			}
			function ultimaNumpol($ejercicio,$periodo)
			{
				$myQuery = "SELECT numpol FROM cont_polizas WHERE idperiodo=$periodo AND idejercicio = (SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = '$ejercicio') ORDER BY numpol DESC LIMIT 1";
				$numpol = $this->query($myQuery);
				$numpol = $numpol->fetch_assoc();
				return $numpol['numpol'];
			}

			function passAdmin($Pass)
			{
				include('../../netwarelog/webconfig.php');
				$strPwd = $Pass;
				$strPwd = crypt($strPwd,$accelog_salt);

				$strResult = "NOF";

				$strSql = "SELECT * FROM accelog_usuarios WHERE idempleado = 2 AND clave = '" . $strPwd . "';";
				//$strSql = "SELECT * FROM accelog_usuarios;";
				$rstPwd = $this->query($strSql);
				if($rstPwd->num_rows>0)
				{
    				$strResult = "OK";
				}
				return $strResult;
			}

			function ReiniciarContabilidad()
			{
				$myQuery = "
				TRUNCATE TABLE cont_accounts; 
				TRUNCATE TABLE cont_config; 
				TRUNCATE TABLE cont_config_pdv; 
				TRUNCATE TABLE cont_ejercicios; 
				TRUNCATE TABLE cont_movimientos; 
				TRUNCATE TABLE cont_polizas; 
				TRUNCATE TABLE cont_rel_desglose_iva; 
				TRUNCATE TABLE cont_rel_pol_prov;
				TRUNCATE TABLE cont_segmentos;
				TRUNCATE TABLE bco_cuentas_bancarias;
				DELETE FROM cont_tasaPrv WHERE idPrv IN (SELECT idPrv FROM mrp_proveedor WHERE ImOtSis = 1);
				DELETE FROM mrp_proveedor WHERE ImOtSis = 1;
				";
				$this->multi_query($myQuery);
			}

		//Termina funciones punto de venta
		function circulante(){
			$sql=$this->query("select * from cont_accounts where  (account_code like '1.1%' || account_code like '2.1%') and affectable=1 ");
			return $sql;

		}
		function CuentaGasto(){
			$sql=$this->query("select * from cont_accounts where  account_code like '4.2%'  and affectable=1 ");
			return $sql;

		}
	}
?>