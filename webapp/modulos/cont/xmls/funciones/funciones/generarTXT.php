<script language='javascript'>
$(function()
 {
 //$("#ejercicio").val($("#actual").val())
 $('#ejercicio option[value="'+$("#actual").val()+'"]').attr("selected", "selected");
$("#generaTXT").dialog({
			autoOpen: false,
			width: 400,
			height: 320,
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
			"Generar TXT": function () 
		 {
		
		 	$('#cargando').show()
		 	$('.ui-button').attr('disabled','disabled')
			var postFunc;
			var locat;
			//alert($('#generar').attr('tipo'))
			if($('#periodo').val() != '0')
			{
				if($('#generar').attr('tipo') == 'balanzas')
				{
					postFunc = 'generarXMLBalanza';
					locat =  'balanzaComprobacionXML'
				}
				if($('#generar').attr('tipo') == 'cuentas')
				{
					postFunc = 'generarXMLCatalogo';
					locat =  'catalogoXML'	
				}

				if($('#generar').attr('tipo') == 'a29')
				{
					$.post("ajax.php?c=Reports&f=generarA29",
					 {
				 		Ejercicio: $('#ejercicio').val(),
						Periodo_inicial: $('#periodo_inicial').val(),
						Periodo_final: $('#periodo_final').val(),
						Prov: $('input:radio[name=prov]:checked').val(),
						Proveedor_inicial: $('#proveedor_inicial').val(),
						Proveedor_final: $('#proveedor_final').val()
				 	},
			 		function(data)
			 		{
			 			var car = $('#ejercicio').val().split('-')
						window.location.replace("index.php?c=Reports&f=a29Txt&sub="+car[1]);
			 		});
				}
				else
				{
					$.post("ajax.php?c=Reports&f="+postFunc,
					 {
				 		Ejercicio: $('#ejercicio').val(),
						Periodo: $('#periodo').val()
				 	},
			 		function(data)
			 		{
			 			var car = $('#ejercicio').val().split('-')
						window.location.replace("index.php?c=Reports&f="+locat+"&sub="+car[1]);
			 		});
				}
			}
			else
			{
				alert("Seleccione un Periodo");
			}
			 
		 }
		}
		});
$('#generar').click(function(){
$('#generaTXT').dialog({position:['left',100]});
		$('#generaTXT').dialog('open');
	});
});
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

</style>

<div id='generaTXT' title='Generar TXTs'>
	<table>
		<tr><td>Ejercicio:<input type='hidden' id='actual' value='<?php echo $ejercicio_actual ?>' class='nminputselect'></td><td><select name='ejercicio' id='ejercicio' class='nminputselect'>
			<?php  
			while($e = $ejercicios->fetch_array())
			{
				echo "<option value='".$e['Id']."-".$e['NombreEjercicio']."'>".$e['NombreEjercicio']."</option>";
			}
			?>
		</select></td></tr>
		<tr><td>Del Periodo:</td><td><select id='periodo_inicial' class='nminputselect'>
			<option value='1'>Enero</option>
			<option value='2'>Febrero</option>
			<option value='3'>Marzo</option>
			<option value='4'>Abril</option>
			<option value='5'>Mayo</option>
			<option value='6'>Junio</option>
			<option value='7'>Julio</option>
			<option value='8'>Agosto</option>
			<option value='9'>Septiembre</option>
			<option value='10'>Octubre</option>
			<option value='11'>Noviembre</option>
			<option value='12'>Diciembre</option>
		</select></td></tr>
		</tr>
		<tr><td>Al Periodo:</td><td><select id='periodo_final' class='nminputselect'>
			<option value='1'>Enero</option>
			<option value='2'>Febrero</option>
			<option value='3'>Marzo</option>
			<option value='4'>Abril</option>
			<option value='5'>Mayo</option>
			<option value='6'>Junio</option>
			<option value='7'>Julio</option>
			<option value='8'>Agosto</option>
			<option value='9'>Septiembre</option>
			<option value='10'>Octubre</option>
			<option value='11'>Noviembre</option>
			<option value='12'>Diciembre</option>
		</select></td></tr>
		<tr>
			<td><input type='radio' id='prov' name='prov' value='todos' checked> Todos</td><td><input type='radio' id='prov' name='prov' value='algunos'> Algunos</td>
		</tr>
		<tr><td>Del Proveedor:</td><td><select name='proveedor_inicial' id='proveedor_inicial' class='nminputselect'>
			<?php  
			while($p_ini = $proveedor_inicial->fetch_array())
			{
				echo "<option value='".$p_ini['idPrv']."'>".$p_ini['razon_social']."</option>";
			}
			?>
		</select></td></tr>
		<tr><td>Al Proveedor:</td><td><select name='proveedor_final' id='proveedor_final' class='nminputselect'>
			<?php  
			while($p_fin = $proveedor_final->fetch_array())
			{
				echo "<option value='".$p_fin['idPrv']."'>".$p_fin['razon_social']."</option>";
			}
			?>
		</select></td></tr>
		<tr><td><div id='cargando'>
	<b style='color:#91C313;'>Generando TXT...</b>

</div></td></tr>
	</table>
</div>