<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script language='javascript'>
function guardaAcreditamiento()
{
	var str = '';
	$(".chbx:checked").each(function(index)
		{
				
				if(index == 0)//Si es el primer o solo es un registro se agrega a la cadena sin coma
					{
						str = $(this).val()	
					}
					else
					{
						str += "," + $(this).val();
					}
		});
		if(str)
		{
			$.post("ajax.php?c=Reports&f=actAcreditamiento",
      		{
        		Ids: str,
        		Periodo: $('#periodo').val(),
        		Ejercicio: $('#ejercicio').val(),
        		Tipo: 'cont_rel_desglose_iva'
      		},
      		function(data)
      		{
        		if(data)
        		{
        			location.reload();//Actualiza
        			//alert(data)
        		}
        		else
        		{
        			alert('Error, la operacion no se pudo guardar.')
        		}
      		});
			
		}
		else
		{
			alert('Debes seleccionar una poliza para agregar el acreditamiento.')
		}
}
function todos()
{
	if($("#all").is(':checked'))
	{
		$(".chbx").click();
		
	}
	else
	{
		$(".chbx").removeAttr('checked');
	
	}
}
</script>
<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
<style>
.tit_tabla_buscar td
{
	font-size:12px;
	height:30px;
}
</style>
<div class='nmwatitles'>Lista de Acreditacion Desglose de IVA / <a href='index.php?c=reports&f=listaAcreditamientoProveedores'>Ir a Proveedores</a></div>
<table style='margin-left:10px;margin-bottom:10px;margin-top: 14px;' cellspacing=0 cellpadding=0 id='resultados'>
	<tr>
		<td colspan=5></td>
		<td>
			<select name='periodo' id='periodo' class="nminputselect">
				<option value='--'>Periodo</option>
				<option value='0'>Ninguno</option>
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
			</select>
		</td>
		<td>
			<select name='ejercicio' id='ejercicio' class="nminputselect">
				<option value='--'>Ejercicio</option>
				<option value='0'>Ninguno</option>
				<?php
					while($e = $ejercicios->fetch_assoc())
					{
						echo "<option value='".$e['Id']."'>".$e['NombreEjercicio']."</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr class='tit_tabla_buscar'>
		
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=50># Poliza</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=50>Periodo</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=50>Ejercicio</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=250>Concepto</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=150>Importes</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=150>Periodo Acreditamiento</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=150>Ejercicio Acreditamiento</td>
		<td class="nmcatalogbusquedatit" style=" border-left: 2px solid;text-align: center;" width=10><input type='checkbox' id='all' onclick='todos()' class='nminputcheck'> Todos</td>
	</tr>
<?php
while($p = $polizas->fetch_assoc())
{
	if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
		{
    		$color='nmcatalogbusquedacont_1';
		}
		else//Si es impar pinta esto
		{
    		$color='nmcatalogbusquedacont_2';
		}
	echo "<tr class='$color'>";
	echo "<td>".$p['numpol']."</td><td>".$p['idperiodo']."</td><td>".$p['Ejercicio']."</td><td>".$p['concepto']."</td><td>".$p['Importes']."</td><td>".$p['periodoAcreditamiento']."</td><td>".$p['ejercicioAcreditamiento']."</td><td><input class='nminputcheck chbx' type='checkbox' value='".$p['idPoliza']."'></td>";
	echo "</tr>";
	$cont++;//Incrementa contador
}
?>	
<tr>
		<td colspan=6></td>
		<td><input type='button' name='guardar' id='guardar' class="nminputbutton" value='Actualizar' onclick='guardaAcreditamiento()'></td>
</tr>
</table>