<HTML>
<HEAD>
<meta http-equiv="Expires" content="Mon, 08 Oct 2014 11:59:00 GMT">
<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<link rel="stylesheet" type="text/css" href="../../../../netwarelog/design/default/netwarlog.css" />
<TITLE>Validacion XML's de facturas</TITLE>
<style>
body
{
	font-family:Arial, Helvetica, sans-serif;
}
#title
{
	background-color:#91C313;
	color:white;
	font-padding:16px;
	font-weight: bold;
}
.cont
{
	background-color:#EEEEEE;
}
td
{
	padding:3px;
}
</style>
</HEAD>
<BODY>
<div align=center>
	<form method='post' enctype='multipart/form-data'>
	<table border=0>
		<tr>
			<div class="nmwatitles">Validacion XML's de facturas</div>
		</tr>
		<tr>
			<td class='cont'>	
 				Archivo <input type='file' name='arch' size='60'>
 			</td>
		</tr>
		<tr>
			<td class='cont'>
				<INPUT TYPE="submit" class="nminputbutton" VALUE="Validar" >
				</td>
		</tr>
	</table>
	</FORM>
 <hr>
<?php
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING|E_DEPRECATED));
if (trim($_FILES['arch']['name'])=="") die("Cargue un XML");
if ($_FILES['arch']['error']==1 || $_FILES['arch']['size']==0) {
    echo "<h1><red>NO SUBIO archivo, demasiado grande</red></h1>";
    die();
} 
$arch = $_FILES['arch']['tmp_name'];
$texto = file_get_contents($arch);
unlink($arch);
echo "<b>Archivo:</b> <b style='color:blue;'>".$_FILES['arch']['name']."</b>";

///////////////////////////////////////////////////////////////////////////
// Quita Addenda solo valida fiscal
$texto = preg_replace('{<Addenda.*/Addenda>}is', '<Addenda/>', $texto);
$texto = preg_replace('{<cfdi:Addenda.*/cfdi:Addenda>}is', '<cfdi:Addenda/>', $texto);
///////////////////////////////////////////////////////////////////////////

require_once 'XML/Beautifier.php';
require_once 'Text/Highlighter.php';
$fmt = new XML_Beautifier();
$fmt->setOption("multilineTags", TRUE);
$paso = $fmt->formatString($texto);
if (substr($paso,0,10)!="XML_Parser") $texto=$paso; // XML correctamente formado
$hl =& Text_Highlighter::factory('XML',array('numbers'=>HL_NUMBERS_TABLE));
echo "<div style='height:300px; overflow:auto';";
echo $hl->highlight($texto);
echo "</div><hr />";
/////////////////////////////////////////////////////////////////////////////

libxml_use_internal_errors(true);   
$xml = new DOMDocument();
$ok = $xml->loadXML($texto);
if (!$ok) {
   display_xml_errors(); 
   die();
}

////////////////////////////////////////////////////////////////////////////
//   Con XPath obtenemos el valor de los atributos del XML
$xp = new DOMXpath($xml);
$rfc = getpath("//@rfc");
$data['rfc'] = utf8_decode($rfc[0]);
$data['rfc_receptor'] = utf8_decode($rfc[1]);
$data['total'] = getpath("//@total");
if (is_array($data['total'])) $data['total'] = $data['total'][0];
$data['version'] = getpath("//@version");
if (is_array($data['version'])) $data['version'] = $data['version'][0];
$data['version'] = trim($data['version']);
$data['seri'] = utf8_decode(trim(getpath("//@serie")));
$data['fecha'] = trim(getpath("//@fecha"));
$data['noap'] = trim(getpath("//@noAprobacion"));
$data['anoa'] = trim(getpath("//@anoAprobacion"));
$data['no_cert'] = getpath("//@noCertificado");
if (is_array($data['no_cert'])) $data['no_cert'] = $data['no_cert'][0];
$data['no_cert'] = trim($data['no_cert']);
$data['cert'] = getpath("//@certificado");
$data['sell'] = getpath("//@sello");
$data['sellocfd'] = getpath("//@selloCFD");
$data['sellosat'] = getpath("//@selloSAT");
$data['no_cert_sat'] = getpath("//@noCertificadoSAT");
$data['uuid'] = getpath("//@UUID");
//   Valores guardados en un arreglo para ser usado por las funciones
/////////////////////////////////////////////////////////////////////////////




// {{{ Valida_XSD
valida_xsd();
if ($data['version']=="3.2") 
{
    valida_en_sat();
}

function valida_xsd() {
   
global $data, $xml,$texto;
libxml_use_internal_errors(true);   
switch ($data['version']) {
  case "2.0":
    echo "Version 2.0 (CFD)<br>";
    $ok = $xml->schemaValidate("xsds/cfdv2complemento.xsd");
    break;
  case "2.2":
    echo "Version 2.2 (CFD)<br>";
    $ok = $xml->schemaValidate("xsds/cfdv22complemento.xsd");
    break;
  case "3.0":
    echo "Version 3.0 (CFDI)<br>";
    $ok = $xml->schemaValidate("xsds/cfdv3complemento.xsd");
    break;
  case "3.2":
    echo "Version 3.2 (CFDI)<br>";
    $ok = $xml->schemaValidate("xsds/cfdv32.xsd");
    break;
  default:
    $ok = false;
    echo "<b style='color:red;'>Versi&oacute;n inv&aacute;lida ".$data['version']."</b><br>";
}
if ($ok) {
    echo "<h3>Esquema v&aacute;lido</h3>";
} else {
    echo "<h3 style='color:red;'>Estructura contra esquema incorrecta</h3>";
    display_xml_errors(); 
}
echo "<hr>";
}

// {{{ Valida este XML en el servidor del SAT 
function valida_en_sat() {
    global $data;
    error_reporting(E_ALL);
    require_once('nusoap/nusoap.php');
    error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING|E_DEPRECATED));
    $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";

    $soapclient = new nusoap_client($url,$esWSDL=true);
    $soapclient->soap_defencoding = 'UTF-8'; 
    $soapclient->decode_utf8 = false;

    $rfc_emisor = utf8_encode($data['rfc']);
    $rfc_receptor = utf8_encode($data['rfc_receptor']);
    $impo = (double)$data['total'];
    $impo=sprintf("%.6f", $impo);
    $impo = str_pad($impo,17,"0",STR_PAD_LEFT);

    $uuid = strtoupper($data['uuid']);

    $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";

    $prm = array('expresionImpresa'=>$factura);

    $buscar=$soapclient->call('Consulta',$prm);

    echo "<h3>El SAT reporta:</h3>";
    echo "Status del C&oacute;digo: ".$buscar['ConsultaResult']['CodigoEstatus']."<br>";
    echo "Status: ".$buscar['ConsultaResult']['Estado']."<br>";

}
// }}}

// }}} Valida XSD




// {{{ get path,  ejecuta el Xpath
function getpath($qry) {
global $xp;
$prm = array();
$nodelist = $xp->query($qry);
foreach ($nodelist as $tmpnode)  {
    $prm[] = trim($tmpnode->nodeValue);
    }
$ret = (sizeof($prm)<=1) ? $prm[0] : $prm;
return($ret);
}
/// }}}}
// {{{ display_xml_errors
function display_xml_errors() {
    global $texto;
    $lineas = explode("\n", $texto);
    $errors = libxml_get_errors();

    echo "<pre>";
    foreach ($errors as $error) {
        echo display_xml_error($error, $lineas);
    }
    echo "</pre>";

    libxml_clear_errors();
}
/// }}}}
// {{{ display_xml_error
function display_xml_error($error, $lineas) {
    $return  = htmlspecialchars($lineas[$error->line - 1]) . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Linea: $error->line" .
               "\n  Columna: $error->column";
    echo "$return\n\n--------------------------------------------\n\n";
}
/// }}}}
?>
</div>
</BODY>
</HTML>