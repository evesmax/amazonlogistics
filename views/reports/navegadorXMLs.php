<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script language='javascript'>
$(document).ready(function()
{
	ordenar('.itemList',true)
});

 	function eliminar(archivo)
 	{
 		var confirmacion = confirm("Esta seguro de eliminar este archivo: \n"+archivo);
 		if(confirmacion)
 		{
 			$.post("ajax.php?c=Reports&f=EliminarArchivo",
		 	{
				Archivo: archivo
			 },
			 function(data)
			 {
			 	location.reload();
				//alert('Eliminado')
			 });
 		}
 	}
 	function ordenar(elementos, orden){
	var lista = $(elementos).parent();
	var elemLista = $(elementos).get();
	elemLista.sort(function(a, b) {
	   var compA = omitirAcentos($(a).text().toUpperCase());
	   var compB = omitirAcentos($(b).text().toUpperCase());
	   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
	})
	if(orden){
		$(elemLista).each( function(ind, elem) { $(lista).append(elem); });
	}else{
		$(elemLista).each( function(ind, elem) { $(lista).prepend(elem); });
	}
}
function omitirAcentos(text) {
    var acentos = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç";
    var original = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc";
    for (var i=0; i<acentos.length; i++) {
        text = text.replace(acentos.charAt(i), original.charAt(i));
    }
    return text;
}
</script>
<table width=650 border=0>
<tr><td class="nmwatitles" colspan=7>
	<?php 
if($directorio == "balanzas")
{
	require('xmls/funciones/generarXML.php');
	echo "Lista de XML de Comprobaci&oacute;n";
	$function = "balanzaComprobacionXML";
	$logo = "xml.jpg";
}
if($directorio == "cuentas")
{
	require('xmls/funciones/generarXML.php');
	echo "Lista de XML de Cat&aacute;logo";
	$function = "catalogoXML";
	$logo = "xml.jpg";
}
if($directorio == "auxcuentas")
{
	require('xmls/funciones/generarXML.php');
	echo "Lista de XML de Auxiliar de Cuentas y/o Subcuentas";
	$function = "auxCuentasXML";
	$logo = "xml.jpg";
}
if($directorio == "a29")
{
	require('xmls/funciones/generarTXT.php');
	echo "Lista de TXT del A29";
	$function = "a29Txt";
	$logo = "txt.png";
}

if($directorio == "polizas")
{   
    require('xmls/funciones/generarXML.php');
    echo "Lista de Polizas del mes XML";
    $function = "polizasXML";
    $logo = "xml.jpg";
}

if($directorio == "folios")
{   
    require('xmls/funciones/generarXML.php');
    echo "Lista de Folios Fiscales del mes XML";
    $function = "foliosXML";
    $logo = "xml.jpg";
}

?></td></tr>

<?php
if($_GET['sub'])
{
	echo "<tr><td style='text-align:center;'><a href='index.php?c=Reports&f=$function'>Regresar</a></td></tr>";
}
else
{
	$boton = str_replace('.jpg', '', $logo);
	$boton = str_replace('.png', '', $boton);
	echo "<tr><td colspan=2><input type='button' class='nminputbutton' value='Generar $boton'  id='generar' title='ctrl + g' tipo='$directorio'></td></tr>";	
}
$carpeta = "$directorio/".$_GET['sub'];
$ruta = "xmls/".$carpeta;
$directorio = opendir($ruta); //ruta actual
sort($directorio);
while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
{
	if($archivo != '.' AND $archivo != '..' AND $archivo != '.DS_Store' AND $archivo != '.file' AND $archivo != '.file.rtf')
	{
		$extension = substr($archivo,-4,4);
		if ( $extension != '.xml' && $extension != '.txt')//verificamos si es o no un directorio
   		{
        	echo "<tr><td width=70><img src='xmls/imgs/carpeta.jpg'></td><td><b><a href='index.php?c=Reports&f=$function&sub=".$archivo."'>[".$archivo."]</a></b></td><td></td><td></td><td></td><td></td><td></td></tr>"; //de ser un directorio lo envolvemos entre corchetes
    	}
    	else
    	{
    		if($extension == '.xml')
    		{
    			$exten = 'XML';
    		}
    		if($extension == '.txt')
    		{
    			$exten = 'TXT';
    		}
        	echo "<tr style='text-align:center;height:50px;' class='itemList'><td><img src='xmls/imgs/$logo' width=30></td><td><b>".$archivo . "</b></td><td width=100> <a href='$ruta/$archivo' target='_blank'>Ver</a> </td><td width=150>Descarga: <a href='xmls/funciones/descargaXML.php?ruta=".$carpeta."&nombre=".$archivo."' target='_blank'>$exten</a> / <a href='xmls/funciones/descargaXML.php?ruta=".$carpeta."&nombre=".$archivo."&tipo=1' target='_blank'>ZIP</a></td><td><a href='xmls/funciones/descargaExcel.php?ruta=".$carpeta."&nombre=".$archivo."&tipo=".$function."' target='_blank'>Excel</a></td><td>Creado  o modificado el: ".date ("d/m/Y H:i:s",filectime($ruta."/".$archivo))."</td><td><a href='javascript:eliminar(\"".$ruta."/".$archivo."\")'><img src='images/eliminado.png' title='Eliminar'></a></td></tr>";
    	}
	}
}
?>
</table>