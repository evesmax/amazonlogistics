<script language='javascript'>
$(function()
 {
 	var t = '<?php echo $directorio; ?>'
 	switch(t)
 	{
 		case 'balanzas':
 					$("#complementario,#bnc").show()
 					$("#tipoPol,#numOrden").hide()
 					break;
		case 'cuentas':
					$("#complementario,#bnc").hide()
 					$("#tipoPol,#numOrden").hide()
 					break; 					
 		case 'a29':
			 		$("#complementario,#bnc").hide()
 					$("#tipoPol,#numOrden").hide()
 					break;
 		case 'polizas':
 					$("#complementario,#bnc").hide()
 					$("#tipoPol,#numOrden").show()
 					break;
 		case 'auxcuentas':
 					$("#complementario,#bnc").hide()
 					$("#tipoPol,#numOrden").show()
 					break;
 		case 'folios':
					$("#complementario,#bnc").hide()
 					$("#tipoPol,#numOrden").hide()
 					break; 								


 	}
 
 	$( "#datepicker" ).datepicker(
			{ 
				dateFormat: "dd-mm-yy",
				monthNames: [ "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
				dayNamesMin: [ "Do","Lu","Ma","Mi","Ju","Vi","Sa"],
				minDate: new Date(2014, 12, 1)
			});
 $('#datepicker').hide()
//Si el ejercicio es mayor o igual a 2015, pueden haber balanzas complementarias
$('#ejercicio').change(function(event) 
{
	comp(t)
});
 $('#bnc').change(function(event) 
 {
	 	if($(this).is(':checked'))
	 	{
	 		$('#datepicker').show()
	 	}
	 	else
	 	{
	 		$('#datepicker').hide()
	 		$('#datepicker').val('')	
	 	}
 });	
 //$("#ejercicio").val($("#actual").val())
 $('#ejercicio option[value="'+$("#actual").val()+'"]').attr("selected", "selected");
$("#generaXML").dialog({
			autoOpen: false,
			width: 350,
			height: 230,
			modal: true,
			show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
		buttons: 
		{
			"Generar XML": function () 
		 {
		
			$('#cargando').show()
			$('.ui-button').attr('disabled','disabled')
			var postFunc;
			var locat;
			var compl=1;
			//alert($('#generar').attr('tipo'))
			if($('#periodo').val() != '0')
			{
				if($('#generar').attr('tipo') == 'balanzas')
				{
					postFunc = 'generarXMLBalanza';
					locat =  'balanzaComprobacionXML'
					
					//Si el checkbox de complementario esta chequeado y el input text de fecha esta vacio
					//no guardara y arrojará un mensaje
					if($('#bnc').is(':checked') && $('#datepicker').val() == '')
					{
						compl = 0
					}
				}
				if($('#generar').attr('tipo') == 'cuentas')
				{
					postFunc = 'generarXMLCatalogo';
					locat =  'catalogoXML'	
				}

				if($('#generar').attr('tipo') == 'polizas')
				{
					postFunc = 'generarXMLPolizas';
					locat =  'polizasXML'		
				}

				if($('#generar').attr('tipo') == 'auxcuentas')
				{
					postFunc = 'generarXMLauxCuentas';
					locat =  'auxCuentasXML'		
				}

				if($('#generar').attr('tipo') == 'folios')
				{
					postFunc = 'generarXMLFolios';
					locat =  'foliosXML'	
				}
				var flag = 1 //Bandera de si se reemplaza o no
				//Compara si el archivo ya existe----------------
				if(compl)
				{
					$.post("ajax.php?c=Reports&f=existeArchivo",
					 {
						Funcion: locat,
						Tipo: Number($('#bnc').is(':checked')),
						Ejercicio: $('#ejercicio').val(),
						Periodo: $('#periodo').val()

					 },
					function(data)
				 	{
				 		//alert("Bandera Inicial: "+flag)
				 		//alert("Existe Archivo: "+data)
						if(parseInt(data))
						{
							var c = confirm("Esta seguro que desea reemplazar?")
							if(!c)
							{  
								flag = 0//No se reemplaza
								$('#cargando').hide();
								$('.ui-button').removeAttr('disabled');
							}
						}
						//alert("Cambio de Bandera: "+flag)
						if(flag)
						{
							//alert(flag)
							var guarda = 1
							var patron = /[A-Z]{3}[0-6][0-9][0-9]{5}(\/)[0-9]{2}/;
							if(locat == 'polizasXML' && ($("#tipoPol").val() == "AF" || $("#tipoPol").val() == "FC") && (!patron.test($("#numOrden").val()) || $("#numOrden").val() == ''))
							{
								guarda = 0
							}
							if(guarda)
							{
								$.post("ajax.php?c=Reports&f="+postFunc,
								{
									Tipo: Number($('#bnc').is(':checked')),
									Fecha: $("#datepicker").val(),
									Ejercicio: $('#ejercicio').val(),
									Periodo: $('#periodo').val(),
									tipoPol:$("#tipoPol").val(),
									numOrden:$("#numOrden").val()
						 		},
					 			function(n)
					 			{
					 				//alert(n)
					 				if(parseInt(n))
					 				{
					 					window.location.replace("index.php?c=Reports&f="+locat+"&sub="+$('#ejercicio').val());
					 				}	
					 				else
					 				{
					 					noGuarda("No hay datos en este periodo para generar el xml.");
					 				}
					 			});
							}
							else
							{
								noGuarda("Capture el numero de orden correctamente.");
							}
						}
				 	});
				}
				else
				{
					noGuarda("Seleccione una fecha de modificacion para complementario.");
				}

			}
			else
			{
				noGuarda("Seleccione un Periodo.");
			}
			 
		 }
		}
		});
$('#generar').click(function(){
		$('#generaXML').dialog({position:['left',100]});
		$('#generaXML').dialog('open');
	});
comp(t)
$("#tipoPol").change(function(event) {
	if($(this).val() == 'AF' || $(this).val() == 'FC')
	{
		$("#numOrden").show()
	}
	else
	{
		$("#numOrden").hide()	
	}
	});
 	
 	antes2015()
 	$("#ejercicio").change(function(event) {
 		antes2015()
 	});
});

function antes2015()
{
	if(parseInt($('#ejercicio').val()) >= 2015)
 	{
 		$("#periodo").removeAttr('disabled')
 	}
 	else
 	{
 		$("#periodo").val('0')
 		$("#periodo").attr('disabled','disabled')
 	}
}

function noGuarda(mensaje)
{
	alert(mensaje);
	$('#cargando').hide();
	$('.ui-button').removeAttr('disabled');
}

function comp(t)
{
	if(parseInt($("#ejercicio").val()) >= 2015 && t == 'balanzas')
	{
		$('label,#bnc').show()	
	}
	else
	{
		$('label,#bnc').hide()
	}
}
</script>
<style>
#lista td
{
	width:146px;
	text-align: center;
	border:1px solid #BDBDBD;
}

#buscar
{
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
-o-border-radius: 4px;
border-radius: 4px;
}

#cargando
{
	display:none;
	position:absolute;
	z-index:1;
}

#tbl_gen td
{
	height:30px;
}

.ui-datepicker-month
{
 color:black;
}

</style>

<div id='generaXML' title='Generar XMLs'>
	<table id='tbl_gen'>
		<tr><td>Ejercicio:<input type='hidden' id='actual' value='<?php echo $ejercicio_actual ?>' class='nminputselect'></td><td><select name='ejercicio' id='ejercicio' class='nminputselect'>
			<?php  
			while($e = $ejercicios->fetch_array())
			{
				echo "<option value='".$e['NombreEjercicio']."'>".$e['NombreEjercicio']."</option>";
			}
			?>
		</select></td></tr>
		<tr><td>Periodo:</td><td><select id='periodo' class='nminputselect'>
			<option value='0'>Selecciona un periodo</option>
			<option value='01'>Enero</option>
			<option value='02'>Febrero</option>
			<option value='03'>Marzo</option>
			<option value='04'>Abril</option>
			<option value='05'>Mayo</option>
			<option value='06'>Junio</option>
			<option value='07'>Julio</option>
			<option value='08'>Agosto</option>
			<option value='09'>Septiembre</option>
			<option value='10'>Octubre</option>
			<option value='11'>Noviembre</option>
			<option value='12'>Diciembre</option>
		</select></td></tr>
		<tr><td>
			<label id='complementario'>Complementario</label>
			<select id='tipoPol' name='tipoPol'>
				<option value='AF'>Acto de fiscalizaci&oacute;n</option>
				<option value='FC'>Fiscalizaci&oacute;n compulsa</option>
				<option value='DE'>Devoluci&oacute;n</option>
				<option value='CO'>Compensaci&oacute;n</option>
			</select><input type='checkbox' name='bnc' id='bnc' value='1'></td>
			<td><input type='text' id='datepicker' name='fecha_comp' value=''><input type='text' id='numOrden' name='numOrden' value='' placeholder='Numero de Orden'></td></tr>
		<tr><td><div id='cargando'>
	<b style='color:#91C313;'>Generando XML...</b>

</div></td></tr>
	</table>
</div>