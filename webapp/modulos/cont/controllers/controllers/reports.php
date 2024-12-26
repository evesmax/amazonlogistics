<?php
require('common.php');//Carga la funciones comunes top y footer
require("models/reports.php");

class Reports extends Common
{
	public $ReportsModel;

	function __construct(){
	$this->ReportsModel = new ReportsModel();
	$this->ReportsModel->connect();
	}

	function __destruct()
	{
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this->ReportsModel->close();
	}

	function movcuentas()
	{
		if(isset($_GET['t']))
		{
			$listaCuentas = $this->ReportsModel->getAccountsMayor();
		}
		else
		{
			$listaCuentas = $this->ReportsModel->getAccounts();
		}
		require('views/reports/movcuentas.php');
	}

	function movcuentas_despues()
	{
		
		$fecha_antes = $_REQUEST['f3_3']."-".$_REQUEST['f3_1']."-".$_REQUEST['f3_2'];
		$fecha_despues = $_REQUEST['f4_3']."-".$_REQUEST['f4_1']."-".$_REQUEST['f4_2'];
		//echo $_REQUEST['cuentas'];
		$saldo = 0;
		if(isset($_REQUEST['saldos'])) $saldo = 1;
		$datos = $this->ReportsModel->getData_movcuentas_despues($_REQUEST['cuentas'],$fecha_antes,$fecha_despues,$_REQUEST['rango'],$_REQUEST['tipo'],$saldo);
		$empresa = $this->ReportsModel->empresa();
		$logo = $this->ReportsModel->logo();
		require('views/reports/movcuentas_despues.php');
	}

	function a29Txt()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$proveedor_inicial = $this->ReportsModel->proveedores();
		$proveedor_final = $this->ReportsModel->proveedores();
		$directorio = "a29";
		require("views/reports/navegadorXMLs.php");
	}

	function generarA29()
	{
		$ej = explode('-',$_POST['Ejercicio']);
		$ejercicio = $ej[1];
		$periodo_inicial = $_POST['Periodo_inicial'];
		$periodo_final = $_POST['Periodo_final'];
		$prov = $_POST['Prov'];
		$proveedor_inicial = $_POST['Proveedor_inicial'];
		$proveedor_final = $_POST['Proveedor_final'];
		$datos = $this->ReportsModel->generarConsultaA29($ej[0],$periodo_inicial,$periodo_final,$prov, $proveedor_inicial, $proveedor_final);
		$nombreTXT = "a29_".$prov."_".$periodo_inicial."_".$periodo_inicial."_".$ejercicio;//Nombre del archivo txt
		$ruta = "xmls/a29/" . $ejercicio . "/";//Ruta donde se guardara
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
		{
			mkdir ($ruta, 0777);
		}

		$texto = "";
		while($d = $datos->fetch_object())
		{
			$p16 = $p15 = $p16N = $p11 = $p10 = $p11N = $p0 = $ep = $IvaRetenido = '';
			
			if($d->p16 != '') $p16 = round($d->p16);
			if($d->p15 != '') $p15 = round($d->p15);
			if($d->p16N == '0.00')
				{
					$p16N = '';
				} 
				else
				{
					$p16N = round($d->p16N);
					if(intval($p16N) == 0) $p16N = '';
				}
			if($d->p11 != '') $p11 = round($d->p11);
			if($d->p10 != '') $p10 = round($d->p10);
			if($d->p11N == '0.00')
				{
					$p11N = '';
				} 
				else
				{
					$p11N = round($d->p11N);
					if(intval($p11N) == 0) $p11N = '';
				}
			if($d->p0 != '') $p0 = round($d->p0);
			if($d->ep != '') $ep = round($d->ep);
			if($d->IvaRetenido == '0.00')
				{
					$IvaRetenido = '';
				} 
				else
				{
					$IvaRetenido = round($d->IvaRetenido);
					if(intval($IvaRetenido) == 0) $IvaRetenido = '';
				}

			$texto .= "$d->TipoTercero|$d->TipoOperacion|$d->rfc|$d->numidfiscal|$d->nombrextranjero|||$p16|$p15|$p16N|$p11|$p10|$p11N||||||$p0|$ep|$IvaRetenido||\n";
		}



		//Genera el archivo y lo guarda o sobreescribe.
		$num=0;
		while(file_exists($ruta . "/" . $nombreTXT . "_" . $num . ".txt"))
		{
			$num++;
		}
		$nombre = $ruta . "/" . $nombreTXT . "_" . $num . ".txt";
		$archivo = fopen($nombre, "w+");					
		fwrite($archivo,$texto); 
		fclose($archivo);	
		echo 1;
	}

	function balanzaComprobacionXML()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$directorio = "balanzas";
		require("views/reports/navegadorXMLs.php");
	}

	function generarXMLBalanza()
	{
		$ejercicio 	=	 $_POST['Ejercicio'];
		$periodo 	=	 $_POST['Periodo'];
		$fecha 		=	 explode('-',$_POST['Fecha']);

		$rfc 		=	 $this->ReportsModel->rfc();
		//$datos 	=	 $this->ReportsModel->generarConsultaBalanza($ejercicio,$periodo);//Trae los datos de la consulta Balanza de comprobacion
		$tipoCuenta = 	 $this->ReportsModel->tipoCuenta();
		$datos 		=	 $this->ReportsModel->balanzaComprobacionReporte($this->ReportsModel->IdEjercicio($ejercicio),(int) $periodo,(int) $periodo,$tipoCuenta);//Trae los datos de la consulta Balanza de comprobacion
		if($_POST['Tipo'])
		{
			$tipo = 'BC';
		}
		else
		{
			$tipo = 'BN';
		}
		$nombreXML 	=	 "$rfc$ejercicio$periodo$tipo";//Nombre del archivo xml
		$ruta 		= 	 "xmls/balanzas/" . $ejercicio . "/";//Ruta donde se guardara
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
			{
				mkdir ($ruta, 0777);
			}

		//Llena el arreglo con los datos de la consulta----------------------------------	
		$xml['Balanza']['version'] 		= '1.1';
		$xml['Balanza']['rfc'] 			= $rfc;
		$xml['Balanza']['TotalCtas'] 	= $datos->num_rows;
		$xml['Balanza']['Mes'] 			= $periodo;
		$xml['Balanza']['Ano'] 			= $ejercicio;

		$n = 0;//Numeracion del arreglo
		while($d = $datos->fetch_array())
		{
			//Calculos-----------------------------------------------------------------
			if($d['account_nature'] == '2')
			{
				$SaldoAntes 	= floatval($d['CargosAntes']) - floatval($d['AbonosAntes']);
				$SaldoDespues	= $SaldoAntes + floatval($d['CargosMes']) - floatval($d['AbonosMes']);
			}
			if($d['account_nature'] == '1')
			{
				$SaldoAntes 	= floatval($d['AbonosAntes']) - floatval($d['CargosAntes']);
				$SaldoDespues 	= $SaldoAntes + floatval($d['AbonosMes']) - floatval($d['CargosMes']);
			}

			//Almacena resultado--------------------------------------------------------
			$xml['Balanza']['Cuentas'];
			$xml['Balanza']['Cuentas'][$n]['NumCta'] 	= $d['manual_code'];
			$xml['Balanza']['Cuentas'][$n]['SaldoIni'] 	= number_format($SaldoAntes, 2, '.', '');
			$xml['Balanza']['Cuentas'][$n]['Debe'] 		= number_format($d['CargosMes'], 2, '.', '');
			$xml['Balanza']['Cuentas'][$n]['Haber'] 	= number_format($d['AbonosMes'], 2, '.', '');
			$xml['Balanza']['Cuentas'][$n]['SaldoFin'] 	= number_format($SaldoDespues, 2, '.', '');
			$n++;
		}
		//Escribe y genera el codigo XML
		$xml_final='';
		$tipo2 = str_replace('B', '', $tipo);
		if($tipo2 == "C") $tipo2 = "C' FechaModBal='".$fecha[2]."-".$fecha[1]."-".$fecha[0];
		$xml_final.="<BCE:Balanza xsi:schemaLocation='www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion/BalanzaComprobacion_1_1.xsd' Version='".$xml['Balanza']['version']."' RFC='".$xml['Balanza']['rfc']."' Mes='".$xml['Balanza']['Mes']."' Anio='".$xml['Balanza']['Ano']."' TipoEnvio='".$tipo2."' xmlns:BCE='www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>";
		foreach ($xml['Balanza']['Cuentas'] as $key => $row) 
		{
			$xml_final.="
			<BCE:Ctas NumCta='".$row['NumCta']."' SaldoIni='".$row['SaldoIni']."' Debe='".$row['Debe']."' Haber='".$row['Haber']."' SaldoFin='".$row['SaldoFin']."' />";
		}
		$xml_final.="</BCE:Balanza>";

			//Genera el archivo y lo guarda o sobreescribe.
		/*$num=0;
		while(file_exists($ruta . "/" . $nombreXML . "_" . $num . ".xml"))
		{
			$num++;
		}
		$nombre = $ruta . "/" . $nombreXML . "_" . $num . ".xml";
		*/
		$nombre = $ruta . "/" . $nombreXML . ".xml";
		$archivo = fopen($nombre, "w+");					
		fwrite($archivo,$xml_final); 
		fclose($archivo);
		echo 1;
	}

	function catalogoXML()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$directorio = "cuentas";
		require("views/reports/navegadorXMLs.php");
	}


	function generarXMLCatalogo()
	{
		$ejercicio 	= 	$_POST['Ejercicio'];
		$periodo 	= 	$_POST['Periodo'];
		$rfc 		=	$this->ReportsModel->rfc();
		$datos 		= 	$this->ReportsModel->generarConsultaCatalogo();//Trae los datos de la consulta Balanza de comprobacion
		$nombreXML 	= 	$rfc.$ejercicio.$periodo."CT";//Nombre del archivo xml
		$ruta 		= 	"xmls/cuentas/" . $ejercicio;//Ruta donde se guardara
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
			{
				mkdir ($ruta, 0777);
			}

		//Llena el arreglo con los datos de la consulta----------------------------------	
		$xml['Catalogo']['version']		= 		'1.1';
		$xml['Catalogo']['rfc'] 		= 		$rfc;
		$xml['Catalogo']['TotalCtas'] 	= 		$datos->num_rows;
		$xml['Catalogo']['Mes'] 		= 		$periodo;
		$xml['Catalogo']['Ano'] 		= 		$ejercicio;

		$n = 0;//Numeracion del arreglo
		while($d = $datos->fetch_array())
		{
			
			$Nivel 	= 	substr_count($d['account_code'], '.');
			if(intval($d['account_type']) != 3)
			{
				$Nivel 	= 	intval($Nivel)-1;
			}
			else
			{
				$Nivel 	= 	intval($Nivel);	
			}
			$desc 	= 	str_replace('&','&amp;',$d['description']);
			$desc 	= 	str_replace('"','&quot;',$desc);
			$desc 	= 	str_replace("'",'&apos;',$desc);
			$desc 	= 	str_replace("<",'&lt;',$desc);
			$desc 	= 	str_replace(">",'&gt;',$desc);

			$CA 	=	number_format($d['CA'],2);
			$CA     =   str_replace('.00', '', $CA);

			$xml['Catalogo']['Cuentas'];
			$xml['Catalogo']['Cuentas'][$n]['CodAgrup'] = 	$CA;
			$xml['Catalogo']['Cuentas'][$n]['NumCta'] 	= 	$d['manual_code'];
			$xml['Catalogo']['Cuentas'][$n]['Desc'] 	= 	$desc;
			$xml['Catalogo']['Cuentas'][$n]['SubCtaDe'] = 	$d['CuentaDe'];
			$xml['Catalogo']['Cuentas'][$n]['Nivel'] 	= 	$Nivel;
			$xml['Catalogo']['Cuentas'][$n]['Natur'] 	= 	$d['Naturaleza'];
			$n++;
		}
		//Escribe y genera el codigo XML
		$xml_final	=	'';
		$xml_final	.=	"<catalogocuentas:Catalogo Anio='".$xml['Catalogo']['Ano']."' 
						Mes='".$xml['Catalogo']['Mes']."'
						RFC='".$xml['Catalogo']['rfc']."'
						Version='".$xml['Catalogo']['version']."' 
						xmlns:catalogocuentas='www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas'
						xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
						xsi:schemaLocation='www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas/CatalogoCuentas_1_1.xsd'>";
		foreach ($xml['Catalogo']['Cuentas'] as $key => $row) 
		{
			if(intval($row['Nivel']) <= 1)
			{
				$xml_final	.=	"<catalogocuentas:Ctas CodAgrup='".$row['CodAgrup']."' NumCta='".$row['NumCta']."' Desc='".$row['Desc']."' Nivel='".$row['Nivel']."' Natur='".$row['Natur']."' />";
			}
			else
			{
				$xml_final	.=	"<catalogocuentas:Ctas CodAgrup='".$row['CodAgrup']."' NumCta='".$row['NumCta']."' Desc='".$row['Desc']."' SubCtaDe='".$row['SubCtaDe']."' Nivel='".$row['Nivel']."' Natur='".$row['Natur']."' />";
			}
		}
		$xml_final	.=	"</catalogocuentas:Catalogo>";


			//Genera el archivo y lo guarda o sobreescribe.
		/*$num=0;
		while(file_exists($ruta . "/" . $nombreXML . "_" . $num . ".xml"))
		{
			$num++;
		}*/
		//$nombre 	=	$ruta . "/" . $nombreXML . "_" . $num . ".xml";
		$nombre 	=	$ruta . "/" . $nombreXML . ".xml";
		$archivo 	=	fopen($nombre, "w+");					
		fwrite($archivo,$xml_final); 
		fclose($archivo);
		echo 1;
	}

	function auxCuentasXML()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$directorio = "auxcuentas";
		require("views/reports/navegadorXMLs.php");
	}


	function generarXMLauxCuentas()
	{
		$ejercicio 	= 	$_POST['Ejercicio'];
		$periodo 	= 	$_POST['Periodo'];
		$rfc 		=	$this->ReportsModel->rfc();
		$fecha_antes =	"$ejercicio-$periodo-01";
		$fecha_despues 	=   "$ejercicio-$periodo-31";
		$datos 		= 	$this->ReportsModel->getData_movcuentas_despues('todos',$fecha_antes,$fecha_despues);//Trae los datos del Auxiliar de Movimiento por Cuenta
		$nombreXML 	= 	$rfc.$ejercicio.$periodo."XC";//Nombre del archivo xml
		$ruta 		= 	"xmls/auxcuentas/" . $ejercicio;//Ruta donde se guardara
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
			{
				mkdir ($ruta, 0777);
			}
		// Tipo de Solicitud	
		if($_POST['tipoPol'] == "AF" || $_POST['tipoPol'] == "FC")
		{
			$TipoSolicitud = "TipoSolicitud='".$_POST['tipoPol']."' NumOrden='".$_POST['numOrden']."'";
		}
		else
		{
			$TipoSolicitud = "TipoSolicitud='".$_POST['tipoPol']."'";	
		}
		//Llena el arreglo con los datos de la consulta----------------------------------	
		$xml['Catalogo']['version']		= 		'1.0';
		$xml['Catalogo']['rfc'] 		= 		$rfc;
		$xml['Catalogo']['TotalCtas'] 	= 		$datos->num_rows;
		$xml['Catalogo']['Mes'] 		= 		$periodo;
		$xml['Catalogo']['Ano'] 		= 		$ejercicio;

		//Escribe y genera el codigo XML
		$xml_final  ="";
		$xml_final	.=	"<?xml version='1.0' encoding='UTF-8'?>";
		$xml_final	.=	"<AuxiliarCtas:AuxiliarCtas xmlns:AuxiliarCtas='www.sat.gob.mx/esquemas/ContabilidadE/1_1/AuxiliarCtas'
						 xmlns:xs='http.w3.org/2001/XMLSchema'
						 targetNamespace='www.sat.gob.mx/esquema/ContabilidadE/1_1/AuxiliarCtas' elementFormDefault='qualified'
						 Version='".$xml['Catalogo']['version']."' RFC='".$xml['Catalogo']['rfc']."' Mes='".$xml['Catalogo']['Mes']."' Anio='".$xml['Catalogo']['Ano']."'
						 $TipoSolicitud atributeFormDefault='unqualified'>";
		while($d = $datos->fetch_array())
		{
		if(strtotime($d['Fecha']) >= strtotime($fecha_antes)){
			if($anterior!=$d['Codigo_Cuenta']){
			if($anterior){$xml_final .= "</AuxiliarCtas:Cuenta>";}
			$desc 	= 	str_replace('&','&amp;',$d['Descripcion_Cuenta']);
			$desc 	= 	str_replace('"','&quot;',$desc);
			$desc 	= 	str_replace("'",'&apos;',$desc);
			$desc 	= 	str_replace("<",'&lt;',$desc);
			$desc 	= 	str_replace(">",'&gt;',$desc);
			$xml_final.="<AuxiliarCtas:Cuenta NumCta='".$d['Codigo_Cuenta']."' DesCta='$desc' SaldoIni='".number_format($this->ReportsModel->Saldos($d['Codigo_Cuenta'],$fecha_despues,'Antes'),2)."' SaldoFin='".number_format($this->ReportsModel->Saldos($d['Codigo_Cuenta'],$fecha_despues,'Despues'),2)."'>";
			}else{
			$conc 	= 	str_replace('&','&amp;',$d['Concepto_Poliza']);
			$conc 	= 	str_replace('"','&quot;',$conc);
			$conc 	= 	str_replace("'",'&apos;',$conc);
			$conc 	= 	str_replace("<",'&lt;',$conc);
			$conc 	= 	str_replace(">",'&gt;',$conc);
			$xml_final .="<AuxiliarCtas:DetalleAux Fecha='".$d['Fecha']."' NumUnIdenPol='".$d['ID_Tipo_Poliza']."-$ejercicio-$periodo-".$d['Numero_Poliza']."' Concepto='$conc' Debe='".number_format($d['Cargos'],2,'.','')."' Haber='".number_format($d['Abonos'],2,'.','')."' />";
			$anterior= $d['Codigo_Cuenta'];
			}
		 }
		}
		$xml_final	.=	"	</AuxiliarCtas:Cuenta>
						 </AuxiliarCtas:AuxiliarCtas>";


			//Genera el archivo y lo guarda o sobreescribe.
		/*$num=0;
		while(file_exists($ruta . "/" . $nombreXML . "_" . $num . ".xml"))
		{
			$num++;
		}*/
		//$nombre 	=	$ruta . "/" . $nombreXML . "_" . $num . ".xml";
		$nombre 	=	$ruta . "/" . $nombreXML . ".xml";
		$archivo 	=	fopen($nombre, "w+");					
		fwrite($archivo,$xml_final); 
		fclose($archivo);
		echo 1;
	}

	function existeArchivo()
	{
		if($_POST['Tipo'])
		{
			$tipo = 'BC';
		}
		else
		{
			$tipo = 'BN';
		}
		$rfc 	=	$this->ReportsModel->rfc();
			switch($_POST['Funcion'])
			{
				case 'catalogoXML':
									$ruta = 'xmls/cuentas/'.$_POST['Ejercicio']."/$rfc".$_POST['Ejercicio'].$_POST['Periodo']."-CT.xml";
									break;
				case 'balanzaComprobacionXML':
									$ruta = 'xmls/balanzas/'.$_POST['Ejercicio']."/$rfc".$_POST['Ejercicio'].$_POST['Periodo']."$tipo.xml";
									break;											
			}
			if(file_exists($ruta))
			{
				echo 1;
			}
			else
			{
				echo 0;
			}
	}

	function Eliminarxml()
	{
		unlink(urldecode($_POST['xml']));
	}

	function EliminarArchivo()
	{
		unlink($_POST['Archivo']);
	}
	
	function almacenXML()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$RFCInstancia = $this->ReportsModel->rfc();
		$directorio = "facturas";
		require("views/reports/navegadorXMLs_facturas.php");
	}

	function listaTemporales()
	{	global $xp;
		$listaTemporales = "<tr><td width='50' style='color:white;'>*1_-{}*</td><td width='150'></td><td width='50'><td width='50'></td><td width='50'></td><td width='50'></td></td><td width='50'></td><td width='50'></td><td width='50'></td><td width='50' style='font-weight:bold;font-size:9px;text-align:center;'>Sólo Copiar<button id='copiar_todos' onclick='buttonclick(\"copiar\")'>Todos</button></td><td width='50' style='font-weight:bold;font-size:9px;text-align:center;'>Copiar y Eliminar<button id='borrar_todos' onclick='buttonclick(\"borrar\")'>Todos</button></td></tr>";
		//require('xmls/funciones/generarXML.php');

		$dir = "xmls/facturas/temporales/*";

		// Abrir un directorio, y proceder a leer su contenido
		$archivos = glob($dir,GLOB_NOSORT);
		array_multisort(array_map('filectime', $archivos),SORT_DESC,$archivos);

		$cont=1;
		foreach($archivos as $file) 
		{
			$texto 	= file_get_contents($file);
			$xml 	= new DOMDocument();
			$xml->loadXML($texto);
			$xp = new DOMXpath($xml);
			$data['total'] = $this->getpath("//@total");
			$data['descripcion'] = $this->getpath("//@descripcion");
			
			$data['rfc'] = $this->getpath("//@rfc");
			$data['FechaTimbrado'] = $this->getpath("//@FechaTimbrado");
			

			if(is_array($data['descripcion']))
			{
				$data['descripcion'] = $data['descripcion'][0];
			}

			if($data['rfc'][0] == $this->ReportsModel->rfc())
			{
				$tipoDeComprobante = "Ingreso";
			}
			elseif($data['rfc'][1] == $this->ReportsModel->rfc())
			{
				$tipoDeComprobante = "Egreso";	
			}
			else
			{
				$tipoDeComprobante = "Otro";	
			}


			$name = explode('_',$file);
			$listaTemporales .= "<tr>
			<td width='50'><img src='xmls/imgs/xml.jpg' width=30></td>
			<td width='400'>".$name[1]."</td>
			<td width='100'><a href='$file' target='_blank'>Ver</a></td>
			<td width=300'><b>".$data['descripcion']."</b></td>
			<td width='60'><center>".$tipoDeComprobante."</center></td>
			<td align='center' width='200'><b style='color:orange'>$".number_format($data['total'],2,'.',',')."</b></td><td></td>
			<td width='200'>".$data['FechaTimbrado']."</td><td></td>
			<td width='50' style='text-align:center;'><input title='Sólo copiar' type='radio' name='radio-$cont' id='copiar-$cont' value='".$file."' class='copiar'></td>
			<td width='50' style='text-align:center;'><input title='Copiar y pegar' type='radio' name='radio-$cont' id='borrar-$cont' value='".$file."' class='borrar'></td>
			</tr>";
			$cont++;
		}
		echo $listaTemporales;
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
	function copiaFacturaBorra()
	{
		$ruta 	= "xmls/facturas/" . $_POST['IdPoliza'];//Ruta donde se copiara
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
		{
			mkdir ($ruta, 0777);
		}

		for($i=0;$i<=count($_POST['Copiar'])-1;$i++)
		{
			$nueva = explode('temporales/',$_POST['Copiar'][$i]);
			copy($_POST['Copiar'][$i], $ruta."/".$nueva[1]);	
		}

		for($i=0;$i<=count($_POST['Borrar'])-1;$i++)
		{
			$nueva = explode('temporales/',$_POST['Borrar'][$i]);
			copy($_POST['Borrar'][$i], $ruta."/".$nueva[1]);	
			unlink($_POST['Borrar'][$i]);
		}
	}

	function listaAcreditamientoProveedores()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$polizas = $this->ReportsModel->listaAcreditamientoProveedores();
		require("views/reports/listaAcreditamientoProveedores.php");
	}

	function listaAcreditamientoDesglose()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$polizas = $this->ReportsModel->listaAcreditamientoDesglose();
		require("views/reports/listaAcreditamientoDesglose.php");
	}

	function actAcreditamiento()
	{
		$a = $this->ReportsModel->actAcreditamiento($_POST['Ids'],$_POST['Periodo'],$_POST['Ejercicio'],$_POST['Tipo']);
		echo $a;
	}

	function balanzaComprobacion()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$monedas = $this ->ReportsModel->listaMonedas();
		require("views/reports/balanzaComprobacion.php");

	}

	function balanzaComprobacionReporte()
	{
		$logo = $this->ReportsModel->logo();
		$inicio = sprintf('%02d', $_POST['periodo_inicio']);
		$fin = sprintf('%02d', $_POST['periodo_fin']);
		$fecIni=$this->NombrePeriodo($inicio);
		$fecFin=$this->NombrePeriodo($fin);
		$tipoCuenta = $this->ReportsModel->tipoCuenta();
		$datos = $this->ReportsModel->balanzaComprobacionReporte($_POST['ejercicio'],$inicio,$fin,$tipoCuenta);
		$n_cuentas = $datos->num_rows;
		$ej = $_POST['ejercicio'];
		$ej2 = $this->ReportsModel->NombreEjercicio($ej);
		$tipoVista = $_POST['tipo'];
		$empresa = $this->ReportsModel->empresa();
		require("views/reports/balanzaComprobacionReporte2.php");
	}

	function balanceGeneral()
	{
		ini_set('display_errors','Off');
		$ejercicios = $this->ReportsModel->ejercicios();
		$sucursales = $this->ReportsModel->listaSegmentoSucursal(1);
		$segmentos = $this->ReportsModel->listaSegmentoSucursal(0);
		$monedas = $this ->ReportsModel->listaMonedas();
		require("views/reports/balanceGeneral.php");
	}

	function NombrePeriodo($periodo)
	{
		$saldos = "";
		if(intval($periodo) == 13)
			{
				$periodo = 12;
				$saldos = " con Saldos";
			} 
		$p;
		switch(intval($periodo))
			{
				case 1:$p  = 'Enero';break;
				case 2:$p  = 'Febrero';break;
				case 3:$p  = 'Marzo';break;
				case 4:$p  = 'Abril';break;
				case 5:$p  = 'Mayo';break;
				case 6:$p  = 'Junio';break;
				case 7:$p  = 'Julio';break;
				case 8:$p  = 'Agosto';break;
				case 9:$p  = 'Septiembre';break;
				case 10:$p = 'Octubre';break;
				case 11:$p = 'Noviembre';break;
				case 12:$p = 'Diciembre'.$saldos;break;
			}
			return $p;
	}

	function balanceGeneralReporte()
	{
		$periSaldo = '';
		if(intval($_POST['periodo']) == 13)
		{
			$_POST['periodo'] = 12;
			$p13 = 1;
			$periSaldo = ' con Saldos';
		}
		$ej = $_POST['ejercicio'];
		$empresa = $this->ReportsModel->empresa();
		$tipoCuenta = $this->ReportsModel->tipoCuenta();
		$logo = $this->ReportsModel->logo();
		if($_POST['segmento']>=1){
			$nomSegmento=$this->ReportsModel->nomSegmento($_POST['segmento']);
		}else { $nomSegmento="Todos"; }
		if($_POST['sucursal']>=1){
			$nomSucursal=$this->ReportsModel->nomSucursal($_POST['sucursal']);
		}else { $nomSucursal="Todas"; }
		
		if(intval($_POST['periodo']))
		{	
			$periodo = $this->NombrePeriodo($_POST['periodo']);
			$periodo = $periodo . $periSaldo;
			$anterior = intval($_POST['periodo']) -1;
			if($anterior == 0)
			{
				$anterior = 12;
			}
			$periodoAnterior = $this->NombrePeriodo($anterior);
			
			switch(intval($_GET['tipo']))
			{
				case 0: 
				//Estado de resultados
					if($_POST['comSeg']==1){
						$segmentos = $this->ReportsModel->listaSegmentoSucursal(0);
						$datos = $this->ReportsModel->EstadoResultadoxSegmento($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['detalle']);
						require("views/reports/estadoResultadosxSegmento.php");
						}else{
							$datos = $this->ReportsModel->balanceGeneralReporteDemas($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['segmento'],$_GET['tipo'],$tipoCuenta,$_POST['detalle'],$p13);
							require("views/reports/estadoResultadosReporte.php");
							}
					
					break;
				case 1:
				//Balance General
					$datos = $this->ReportsModel->balanceGeneralReporteDemas($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['segmento'],$_GET['tipo'],$tipoCuenta,$_POST['detalle'],$p13);
					$activos = $this->ReportsModel->balanceGeneralReporteActivo($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['segmento'],$tipoCuenta);
					require("views/reports/balanceGeneralReporte.php");
					break;
				case 2:
				//Estado de Origen y aplicacion de recursos
					$datos = $this->ReportsModel->balanceGeneralReporteDemas($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['segmento'],$_GET['tipo'],$tipoCuenta,$_POST['detalle'],$p13);
					require("views/reports/estadoOrigenReporte.php");
					break;
				case 3:
				//NIF B6 Estado de situacion financiera
					$datos = $this->ReportsModel->nifReporte($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['segmento'],$_GET['tipo'],$p13);
					require("views/reports/nifReporte.php");
					break;	
				case 4:
				//NIF B3 Estado de resultado Integral
					$datos = $this->ReportsModel->nifReporte($_POST['ejercicio'],$_POST['periodo'],$_POST['sucursal'],$_POST['segmento'],$_GET['tipo'],$p13);
					require("views/reports/nifReporteB3.php");
					break;					
			}
		}
		else
		{
			//Balance General y Estado de resultados a 12 periodos
			$datos = $this->ReportsModel->balanceGeneralReportePeriodos($_POST['ejercicio'],$_POST['sucursal'],$_POST['segmento'],$_GET['tipo'],$tipoCuenta,$_POST['detalle']);
			require("views/reports/balanceGeneralReportePeriodos.php");
		}
		
	}

	function catalogoCuentas()
	{
		$naturalezas = $this->ReportsModel->naturalezas();	
		$tipos = $this->ReportsModel->tipos();
		require("views/reports/catalogoCuentas.php");
	}

	function catalogoCuentasReporte()
	{
		$logo = $this->ReportsModel->logo();
		$empresa = $this->ReportsModel->empresa();
		$tipoCuenta = $this->ReportsModel->tipoCuenta();	
		$cuentas = $this->ReportsModel->catalogoCuentasReporte($_POST['naturaleza'],$_POST['tipo'],$tipoCuenta);
		require("views/reports/catalogoCuentasReporte.php");
	}

	function polizasXML()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$directorio = "polizas";
		require("views/reports/navegadorXMLs.php");
	}

	function generarXMLPolizas()
	{
		//INICIAN VARIABLES INICIALES/////////////////////////////////////////////////
		$ejercicio 	= 	$_POST['Ejercicio'];
		$periodo 	= 	$_POST['Periodo'];
		$rfc 		= 	$this->ReportsModel->rfc();
		$datos 		= 	$this->ReportsModel->generarConsultaPolizas($ejercicio,$periodo);//Trae los datos de la consulta Balanza de comprobacion
		$nombreXML 	= 	$rfc.$ejercicio.$periodo."PL";//Nombre del archivo xml
		$ruta 		= 	"xmls/polizas/" . $ejercicio;//Ruta donde se guardara
		//TERMINAN VARIABLES INICIALES***********************************************

		//INICIA TIPO DE SOLICITUD/////////////////////////////////////////////////
		if($_POST['tipoPol'] == "AF" || $_POST['tipoPol'] == "FC")
		{
			$TipoSolicitud = "TipoSolicitud='".$_POST['tipoPol']."' NumOrden='".$_POST['numOrden']."'";
		}
		else
		{
			$TipoSolicitud = "TipoSolicitud='".$_POST['tipoPol']."'";	
		}
		//TERMINA TIPO DE SOLICITUD***********************************************

		//INICIA SI LA CARPETA NO EXISTE LA CREA CON SUS PERMISOS///////////////////
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
			{
				mkdir ($ruta, 0777);
			}
		//TERMINA SI LA CARPETA NO EXISTE LA CREA CON SUS PERMISOS*****************
		
		
		$xml_final	=	'';

		//INICIA LA GENERACION DEL XML//////////////////////////////////////////////

		//Cabeceras
		$xml_final	.=	"<PLZ:Polizas xsi:schemaLocation='www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo/PolizasPeriodo_1_1.xsd' 
		Version='1.1' RFC='$rfc' Mes='$periodo' Anio='$ejercicio' 
		$TipoSolicitud xmlns:PLZ='www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>";

		$IdPolizaAnterior = 0;

		//Si la consulta devuelve datos genera el archivo y manda true
		if($datos->num_rows)
		{
			while($d = $datos->fetch_array())
			{			
				$d['conceptoPoliza'] 	= 	str_replace('&','&amp;',$d['conceptoPoliza']);
				$d['conceptoMovimiento'] = str_replace('&','&amp;',$d['conceptoMovimiento']);
				$d['conceptoPoliza'] 	= 	str_replace('"','&quot;',$d['conceptoPoliza']);
				$d['conceptoMovimiento'] = str_replace('"','&quot;',$d['conceptoMovimiento']);
				$d['conceptoPoliza'] 	= 	str_replace("'",'&apos;',$d['conceptoPoliza']);
				$d['conceptoMovimiento'] = str_replace("'",'&amp;',$d['conceptoMovimiento']);
				$d['conceptoPoliza'] 	= 	str_replace("<",'&lt;',$d['conceptoPoliza']);
				$d['conceptoMovimiento'] = str_replace("<",'&lt;',$d['conceptoMovimiento']);
				$d['conceptoPoliza'] 	= 	str_replace(">",'&gt;',$d['conceptoPoliza']);
				$d['conceptoMovimiento'] = str_replace(">",'&gt;',$d['conceptoMovimiento']);
				if($d['id'] != $IdPolizaAnterior)
				{
					if($IdPolizaAnterior != 0) $xml_final .= "</PLZ:Poliza>";
					$xml_final .= "<PLZ:Poliza NumUnIdenPol='".$d['idtipopoliza']."-$ejercicio-$periodo-".$d['numpol']."' Fecha='".$d['fecha']."' Concepto='".$d['conceptoPoliza']."'>";
				}

				//Si es cargo o abono
				if($d['TipoMovto'] == 'Cargo')
					{
						$cargo = $d['Importe'];
						$abono = 0.0;
					}
					else
					{
						$cargo = 0.0;
						$abono = $d['Importe'];	
					}
				
				$xml_final .= "<PLZ:Transaccion NumCta='".$d['manual_code']."' DesCta='".$d['description']."' Concepto='".$d['conceptoMovimiento']."' Debe='".number_format($cargo,2,'.','')."' Haber='".number_format($abono,2,'.','')."' >";

				if(($d['Factura'] != '' || $d['Factura'] != NULL) AND $d['idtipopoliza'] != 1 AND $d['TipoMovto'] == 'Abono')
				{

					//Busca la factura relacionada, si encuentra la factura agrega estas lineas del CompNal al xml
					if($datosFactura = $this->MontoFactura($d['id'],$d['Factura']))
					{
						//$datosFactura = "10xxx_*-*_rfcloco_*-*_1200";
						$datosFactura = explode('_*-*_',$datosFactura);

						$xml_final .= "<PLZ:CompNal UUID_CFDI='".$datosFactura[0]."' RFC='".$datosFactura[1]."' MontoTotal='".number_format($datosFactura[2],2,'.','')."' />";
					}	
				}

				//Si la forma de pago es con cheque.
						if(intval($d['FormaPago']) == 2)
						{
							//Si se trata de una cuenta de bancos
							if($this->ReportsModel->CuentaBancos($d['main_father']) && $d['TipoMovto'] == 'Abono' && $d['numero'])
							{
								$cbo = explode('*-/-*',$d['CuentaBancariaBancoOrigen']);
								$xml_final .= "<PLZ:Cheque Num='".$d['numero']."' BanEmisNal='".$cbo[1]."' CtaOri='".$cbo[0]."' Fecha='".$d['fecha']."' Benef='".$d['Benef']."' RFC='".$d['rfc']."' Monto='".$d['Importe']."' />";
							}
						}elseif(intval($d['FormaPago']) == 3 || intval($d['idFormaPago']) == 8)
						{
							//Si la forma de pago es con una transferencia o spei.
							if($this->ReportsModel->CuentaBancos($d['main_father']) && $d['TipoMovto'] == 'Abono')
							{
								$cbo = explode('*-/-*',$d['CuentaBancariaBancoOrigen']);
								$xml_final .= "<PLZ:Transferencia CtaOri='".$cbo[0]."' BancoOriNal='".$cbo[1]."' CtaDest='".$d['CuentaDestino']."' BancoDestNal='".$d['BancoDestino']."' Fecha='".$d['fecha']."' Benef='".$d['Benef']."' RFC='".$d['rfc']."' Monto='".$d['Importe']."' />";
							}
						}else
						{
							//Si es metodo de pago credito o debito lo cambia a otros
							if(intval($d['idFormaPago']) == 5 || intval($d['idFormaPago']) == 6) $d['FormaPago'] = '99';
							
							//Otros metodos de pago
							if($this->ReportsModel->CuentaBancos($d['main_father']) && $d['TipoMovto'] == 'Abono' && $d['FormaPago'])
							{
								$cbo = explode('*-/-*',$d['CuentaBancariaBancoOrigen']);
								$xml_final .= "<PLZ:OtrMetodoPago MetPagoPol='".$d['FormaPago']."' Fecha='".$d['fecha']."' Benef='".$d['Benef']."' RFC='".$d['rfc']."' Monto='".$d['Importe']."' />";
							}
						}
				
				$xml_final .= "</PLZ:Transaccion>";

				//Guarda el ultimo id de la poliza
				$IdPolizaAnterior = $d['id'];

			}

			$xml_final .= "</PLZ:Poliza>";
			$xml_final	.=	"</PLZ:Polizas>";
			//TERMINA LA GENERACION DEL XML//////////////////////////////////////////////


				//Genera el archivo y lo guarda o sobreescribe.
			/*$num=0;
			while(file_exists($ruta . "/" . $nombreXML . "_" . $num . ".xml"))
			{
				$num++;
			}*/
			//$nombre 	=	$ruta . "/" . $nombreXML . "_" . $num . ".xml";

			//ruta y nombre del archivo xml
			$nombre 	=	$ruta . "/" . $nombreXML . ".xml";

			//abre o crea el documento
			$archivo 	=	fopen($nombre, "w+");					

			//escribe el documento con el contenido del xml (la variable $xml_final almacena el contenido)
			fwrite($archivo,$xml_final); 

			//cierra el documento y devuelve true
			fclose($archivo);
			echo 1;
		}
		else
		{
			//Si la consulta no genera datos manda false
			echo 0;
		}
	}

	function foliosXML()
	{
		$ejercicios = $this->ReportsModel->ejercicios();
		$ejercicio_actual = $this->ReportsModel->ejercicioActual();
		$directorio = "folios";
		require("views/reports/navegadorXMLs.php");
	}

	function generarXMLFolios()
	{

		//INICIAN VARIABLES INICIALES/////////////////////////////////////////////////
		$ejercicio 	= 	$_POST['Ejercicio'];
		$periodo 	= 	$_POST['Periodo'];
		$rfc 		= 	$this->ReportsModel->rfc();
		$datos 		= 	$this->ReportsModel->generarConsultaFolios($ejercicio,$periodo);//Trae los datos de la consulta Balanza de comprobacion
		$nombreXML 	= 	$rfc.$ejercicio.$periodo."XF";//Nombre del archivo xml
		$ruta 		= 	"xmls/folios/" . $ejercicio;//Ruta donde se guardara
		//TERMINAN VARIABLES INICIALES***********************************************


		//INICIA SI LA CARPETA NO EXISTE LA CREA CON SUS PERMISOS///////////////////
		if(!file_exists($ruta))//Si no existe la carpeta de ese periodo la crea
			{
				mkdir ($ruta, 0777);
			}
		//TERMINA SI LA CARPETA NO EXISTE LA CREA CON SUS PERMISOS*****************
		
		
		$xml_final	=	'';

		//INICIA LA GENERACION DEL XML//////////////////////////////////////////////

		//Cabeceras
		$xml_final	.=	"<RepAux:RepAuxFol xmlns:RepAux='www.sat.gob.mx/esquemas/ContabilidadE/1_1/AuxiliarFolios' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='www.sat.gob.mx/esquemas/ContabilidadE/1_1/AuxiliarFolios http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/AuxiliarFolios/AuxiliarFolios_1_2.xsd' Version='1.2' RFC='$rfc' Anio='$ejercicio' Mes='$periodo' TipoSolicitud='AF'>";

		$IdPolizaAnterior = 0;

		//Si la consulta devuelve datos genera el archivo y manda true
		if($datos->num_rows)
		{
			while($d = $datos->fetch_array())
			{			
				
				if($d['id'] != $IdPolizaAnterior)
				{
					if($IdPolizaAnterior != 0) $xml_final .= "</RepAux:DetAuxFol>";
					$xml_final .= "<RepAux:DetAuxFol NumUnIdenPol='".$d['idtipopoliza']."-$ejercicio-$periodo-".$d['numpol']."' Fecha='".$d['fecha']."'>";
				}
				
				//Busca la factura relacionada, si encuentra la factura agrega estas lineas del CompNal al xml
					if($datosFactura = $this->MontoFactura($d['id'],$d['Factura']))
					{
						//$datosFactura = "10xxx_*-*_rfcloco_*-*_1200";
						$datosFactura = explode('_*-*_',$datosFactura);
						
						if($datosFactura[4] == 'MXP') $datosFactura[4] = "MXN";
						$xml_final .= "<RepAux:ComprNal UUID_CFDI='".$datosFactura[0]."' MontoTotal='".number_format($datosFactura[2],2,'.','')."' RFC='".$datosFactura[1]."' MetPagoAux='".$datosFactura[3]."' Moneda='".$datosFactura[4]."' TipCamb='1.0' />";
					}


				//Guarda el ultimo id de la poliza
				$IdPolizaAnterior = $d['id'];

			}

			$xml_final .= "</RepAux:DetAuxFol>";
			$xml_final	.=	"</RepAux:RepAuxFol>";
			//TERMINA LA GENERACION DEL XML//////////////////////////////////////////////


				//Genera el archivo y lo guarda o sobreescribe.
			/*$num=0;
			while(file_exists($ruta . "/" . $nombreXML . "_" . $num . ".xml"))
			{
				$num++;
			}*/
			//$nombre 	=	$ruta . "/" . $nombreXML . "_" . $num . ".xml";

			//ruta y nombre del archivo xml
			$nombre 	=	$ruta . "/" . $nombreXML . ".xml";

			//abre o crea el documento
			$archivo 	=	fopen($nombre, "w+");					

			//escribe el documento con el contenido del xml (la variable $xml_final almacena el contenido)
			fwrite($archivo,$xml_final); 

			//cierra el documento y devuelve true
			fclose($archivo);
			echo 1;
		}
		else
		{
			//Si la consulta no genera datos manda false
			echo 0;
		}
	}

	function MontoFactura($poliza,$factura)
	{
		$return = false;
		//Carga el archivo
		
			if($f = simplexml_load_file("xmls/facturas/$poliza/$factura"))
			{
				if($namespaces = $f->getNamespaces(true))
				{
					//Buscara el los namespaces del cfdi
					$child = $f->children($namespaces['cfdi']);

					//Busca el RFC del xml
					foreach($child->Emisor[0]->attributes() AS $a => $b)
					{
						if($a == 'rfc')
						{
							$rfc = $b;
						}

					}

					foreach($f->attributes() AS $a => $b)
					{
						if($a == 'metodoDePago')
						{
							$pago = $b;
							$pago = $this->ReportsModel->metodoPago($pago);
						}

						if($a == 'Moneda')
						{
							$moneda = $b;
						}

						

					}


					foreach($child->Impuestos[0]->attributes() AS $a => $b)
					{
						if($a == 'totalImpuestosTrasladados')
						{
							$totalImpuestosTrasladados = $b;
						}

						if($a == 'totalImpuestosRetenidos')
						{
							$totalImpuestosRetenidos = $b;
						}

					}

					//Extrae el UUID del nombre de la factura
					$uuid = str_replace('.xml', '', $factura);
					$uuid = explode('_',$uuid);
					$uuid = $uuid[2];

					//Busca los importes y los suma
					for($i=0;$i<=(count($child->Conceptos->Concepto)-1);$i++)
					{

						foreach($child->Conceptos->Concepto[$i]->attributes() AS $a => $b)
						{
								if($a == 'importe')
								{
									$importes += floatval($b);
								}
						}
					}
					$importes = $importes + $totalImpuestosTrasladados - $totalImpuestosRetenidos;
					$return  = $uuid."_*-*_".$rfc."_*-*_".$importes."_*-*_".$pago."_*-*_".$moneda;
			
				}
			}

		
		return $return;
	}

	function borraFacturaForm()
	{
		$Archivo = explode('/', $_POST['Archivo']);

		$this->ReportsModel->borraFacturaForm($_POST['IdPoliza'],$Archivo[3]);
	}

}
?>
