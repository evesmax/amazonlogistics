<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script language='javascript'>

$(function(){
//EXTENDIENDO LA FUNCION CONTAINS PARA HACERLO CASE-INSENSITIVE
  $.extend($.expr[":"], {
"containsIN": function(elem, i, match, array) {
return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}
});
//-------------------------------------------------------------

	// INICIA GENERACION DE BUSQUEDA
			$("#busqueda").bind("keyup", function(evt){
				//console.log($(this).val().trim());
				if(evt.type == 'keyup')
				{
					$(".listado tr:containsIN('"+$(this).val().trim()+"')").css('display','table-row');
					$(".listado tr:not(:containsIN('"+$(this).val().trim()+"'))").css('display','none');
					$(".listado tr:containsIN('*1_-{}*')").css('display','table-row');
					if($(this).val().trim() === '')
					{
						$(".listado tr").css('display','table-row');
					}
				}

			});
		// TERMINA GENERACION DE BUSQUEDA
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
				alert('Eliminado')
			 });
 		}
 	}

 	function actualiza()
 	{
 		location.reload()
 	}

</script>
<style>
.itemList
{
	height:50px;
}
</style>
<div class="nmwatitles">Almacen de XML's Facturas</div>
<input type="button" class="nminputbutton_color2" onclick="ordenar($('.itemList'), true)" value="Ordenar RFC por A-Z">
<input type='button' value='Actualizar' onclick='actualiza()' class='nminputbutton'><input type='text' class="nmcatalogbusquedainputtext" id='busqueda' name='busqueda' placeholder='Buscar'>

	<div style='background-color:#EEEEEE;width:405px;padding:5px;margin-top:5px;margin-bottom:5px;'>
		<form name='fac' id='fac' action='' method='post' enctype='multipart/form-data'>
			<b>Subir factura(s) xml o zip.</b><br />
			<input type='file' name='factura[]' id='factura'><br /><input type='hidden' name='plz' id='plz' value='<?php echo $numPoliza['id']; ?>'><input type='submit' id='buttonFactura' value='Asociar Facturas' class="nminputbutton_color2">  <span id='verif' style='color:green;display:none;'>Verificando...</span>
		</form>
	</div>


<table border='0' class='listado' style='width:1780px;'>
	
			<tr class='aa' style='color:white;background-color:gray;font-weight:bold;height:30px;'>
			<th style='width:150px !important;'>RFC</th>
			<th style='width:250px !important;'>Nombre</th>
			<th style='width:150px !important;text-align:center;'>Fecha Timbre</th>
			<th style='width:100px !important;'></th>
			<th style='width:100px !important;text-align:center;'>Poliza</th>
			<th style='width:100px !important;text-align:center;'>Tipo de Factura</th>
			<th style='width:200px !important;text-align:right;'>Importes</th>
			<th style='width:180px !important;text-align:center;'>Impuestos IVA</th>
			<th style='width:250px !important;'>Folio _ UUID</th>
			<th style='width:150px !important;'>Fecha de Subida</th>
			<th style='width:120px !important;'></th>
			<th style='width:80px !important;'></th>
		</tr>
	</table>
	<div style='width:1850px;height:400px;overflow:scroll;'>
	<table border='0' class='listado' style='width:1780px;'>
<?php
//require('xmls/funciones/generarXML.php');

$dir = "xmls/facturas/*/*";

// Abrir un directorio, y proceder a leer su contenido
$archivos = glob($dir,GLOB_NOSORT);
array_multisort(array_map('filectime', $archivos),SORT_DESC,$archivos);
$color = '';
$contador=0;
foreach($archivos as $file) 
{
	if ($contador%2==0)
	{
		$color="style='background-color:#EEEEEE;'";
	}
	else
	{
		$color="";
	}
	$aa = simplexml_load_file($file);
	if($namespaces = $aa->getNamespaces(true))
	{
		$child = $aa->children($namespaces['cfdi']);
		foreach($child->Emisor[0]->attributes() AS $a => $b)
		{
			if($a == 'rfc')
			{
				$rfcEmisor = $b;
			}
			if($a == 'nombre')
			{
				$nombreEmisor = $b;
			}

		}

		foreach($child->Receptor[0]->attributes() AS $a => $b)
		{
			if($a == 'rfc')
			{
				$rfcReceptor = $b;
			}
			if($a == 'nombre')
			{
				$nombreReceptor = $b;
			}

		}

		$encontro = 0;
		if($rfcEmisor == $RFCInstancia)
		{
			$tipoDeComprobante = 'Ingreso';
			$rfcTercero = $rfcReceptor;
			$nombreTercero = $nombreReceptor;
			$encontro = 1;
		}

		if($rfcReceptor == $RFCInstancia)
		{
			$tipoDeComprobante = 'Egreso';
			$rfcTercero = $rfcEmisor;
			$nombreTercero = $nombreEmisor;
			$encontro = 1;
		}

		if(!$encontro)
		{
			$tipoDeComprobante = 'Otro';
			$rfcTercero = $rfcEmisor;
			$nombreTercero = $nombreEmisor;
		}

		foreach($child->Impuestos[0]->attributes() AS $a => $b)
		{
			if($a == 'totalImpuestosTrasladados')
			{
				$totalImpuestosTrasladados = $b;
			}
		}

		foreach($child->Complemento[0]->children($namespaces['tfd'])->attributes() AS $a => $b)
		{
			if($a == 'FechaTimbrado')
			{
				$FechaTimbrado = $b;
			}
		}

		/*foreach($aa->attributes() AS $a => $b)
		{
			if($a == 'tipoDeComprobante')
			{
				$tipoDeComprobante = $b;
			}
		}*/


		$importes = 0;
		

		//echo "++".count($child->Conceptos->Concepto);
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
		
				$soloruta = str_replace("/".basename($file), '', $file);
				$carpeta = explode('/',$file);
				if($carpeta[2] == 'temporales')
				{	
					$totalImpuestosTrasladados = floatval($totalImpuestosTrasladados);
					echo "<tr class='itemList' $color>
					<td style='width:150px !important;'>". $rfcTercero ."</td>
					<td style='width:250px !important;'>". $nombreTercero ."</td>
					<td style='width:150px !important;text-align:center;'>". $FechaTimbrado ."</td>
					<td style='width:100px !important;'><a href='$file' target='_blank'>Ver</a></td>
					<td style='width:100px !important;'><center>Temporal</center></td>
					<td style='width:100px !important;'><center>$tipoDeComprobante</center></td>
					<td style='text-align:right;width:200px !important;'>$ ".number_format($importes,2)."</td>
					<td style='text-align:center;width:180px !important;'>$ ".number_format($totalImpuestosTrasladados,2)."</td>
					<td style='width:250px !important;'>" . basename($file) . "</td>
					<td style='width:150px !important;'><center>".date ("d/m/Y H:i:s",filectime($file))."</center></td>";
					echo "<td style='width:120px !important;'><i style='color:red;'>No Asignada</i></td>";
					echo "<td style='width:80px !important;'><img style='cursor:pointer;cursor:hand;' src='images/eliminado.png' title='Eliminar XML'  onclick=eliminaxml('".urlencode($file)."') /></td></tr>";
				}
				else
				{
					$numpol = $this->ReportsModel->numpol($carpeta[2]);
					if(intval($numpol) > 0)
					{
						$totalImpuestosTrasladados = floatval($totalImpuestosTrasladados);
						$name = explode('_',basename($file));
						$name = str_replace('.xml', '', $name);
						echo "<tr class='itemList' $color>
						<td style='width:150px !important;'>". $rfcTercero ."</td>
						<td style='width:250px !important;'>". $nombreTercero ."</td>
						<td style='width:150px !important;text-align:center;'>". $FechaTimbrado ."</td>
						<td style='width:100px !important;'><a href='$file' target='_blank'>Ver</a></td>
						<td style='width:100px !important;'><center>$numpol</center></td>
						<td style='width:100px !important;'><center>$tipoDeComprobante</center></td>
						<td style='text-align:right;width:200px !important;'>$ ".number_format($importes,2)."</td>
						<td style='text-align:center;width:180px !important;'>$ ".number_format($totalImpuestosTrasladados,2)."</td>";

						echo "<td style='width:250px !important;'>" . $name[0] . "_" . $name[2] . "</td><td style='width:150px !important;'><center>".date ("d/m/Y H:i:s",filectime($file))."</center></td>";
						echo "<td style='width:120px !important;'>Asignada</td>
						<td style='width:80px !important;'></td></tr>";
					}
				
				}
	}
	$contador++;
}
?>
	
</table>
</div>
<script>
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

function eliminaxml(xml){
	if(confirm('¿Realmente desea eliminar el XML?'))
  	{
  		$.post("ajax.php?c=Reports&f=Eliminarxml",
		 	{
				xml: xml
			 },
			 function(data)
			 {
			 	alert('XML Eliminado');
			 	actualiza();
				
			 });
  	}
} 
 
$( '#fac' )
  .submit( function( e ) {
  	$('#verif').css('display','inline');
    $.ajax( {
      url: 'ajax.php?c=CaptPolizas&f=subeFacturaZip',
      type: 'POST',
      data: new FormData( this ),
      processData: false,
      contentType: false
    } ).done(function( data1 ) {
    	//$("#Facturas").dialog('refresh')
    		$.post("ajax.php?c=CaptPolizas&f=facturas_dialog",
		 	{
				IdPoliza: 'temporales'
			},
			function()
			{
				location.reload();
			 	//$('#listaFacturas').html(data2)
				
			});
			$('#factura').val('')
			data1 = data1.split('-/-*');
			$('#verif').css('display','none');
			if(parseInt(data1[2]))
			{
				alert('Los siguientes '+data1[2]+' archivos no son validos: \n'+data1[3])
			}

			if(parseInt(data1[0]))
			{
				alert(data1[0]+' Archivos Validados: \n'+data1[1])
			}
    
  	});
    e.preventDefault();
  }); 

</script>