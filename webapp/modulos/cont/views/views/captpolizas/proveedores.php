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
#loading
{
	background-color:#BDBDBD;
	color:white;
	text-align:center;
	font-weight:bold;
}
</style>
<div id='Proveedores' title='Relacionar Proveedor'>
	<input type='button' style='border:0px;color:white;width:100%;' value='Cargando datos...' id='loading'>
	<TABLE id='conte'>
		<tr><td><input type='hidden' id='idr'></td></td><td><input type='hidden' id='idx'></td></tr>
	<TR><TD><b>Proveedor:</b></TD><TD>
		<select name='ProveedoresSelect' id='ProveedoresSelect' onclick='modificaInfoProv(1)'>
			<option value='0'>---</option>
			<?php
		while($Proveedores = $Providers->fetch_assoc())
		{
			echo "<option value='".$Proveedores['idPrv']."'>".$Proveedores['razon_social']."</option>";
		}
		?>	
		</select>
	</TD></TR>
	<TR><TD>Referencia</TD><TD><input type='text' id='referencia' name='referencia'></TD></TR>
	<TR><TD>Importe</TD><TD><input type='text' id='importe' name='importe' onchange='modificaImpuestos()'></TD></TR>
	<TR><TD>IVA</TD><TD>
	<label id='ivas'></label>
	</TD></TR>
	<TR><TD>Importe Base</TD><TD><input type='text' id='importeBase' name='importeBase' onchange='importeIVA(1)'></TD></TR>
	<TR id="acreditaietu" style="display:none"><TD><b>Importe Base/Acreditamiento para IETU</b></TD><TD><input type='text' id='acreietu' name='acreietu'  ></TD></TR>
	<TR><TD>Importe IVA</TD><TD><input type='text' id='importeIVA' name='importeIVA' readonly ></TD></TR>
	<TR><TD>Otras erogaciones</TD><TD><input type='text' id='otrasErogaciones' name='otrasErogaciones' onchange='importeAntesRetenciones()'></TD></TR>
	<TR><TD>Importe antes de retenciones</TD><TD><input type='text' id='importeAntesRetenciones' name='importeAntesRetenciones' readonly></TD></TR>
	<TR><TD>Retenci&oacute;n IVA</TD><TD>(<label id='retivaNum'></label>%)<br /> <input type='text' id='retiva' name='retiva' onchange='totalErogacion()'></TD></TR>
	<TR><TD>Retenci&oacute;n ISR</TD><TD>(<label id='retisrNum'></label>%)<br /> <input type='text' id='retisr' name='retisr' onchange='totalErogacion()'></TD></TR>
	<TR><TD>Total erogación</TD><TD><input type='text' id='totalErogacion' name='totalErogacion' readonly ></TD></TR>
	<TR><TD>IVA Pagado no acreditable</TD><TD><input type='text' id='IVANoAcreditable' name='IVANoAcreditable' value='0'></TD></TR>
	<tr style="display: none" id="ietumuestra"><td><b>Tipo de IETU</b> </td>
		<td>
			<select name='ietu' id='ietu' style="this.style.width = 'auto'; width:230px;" onchange="compruebaid()">
			<option value='0' >---</option>
			
			
			</select>
		</td></tr>
	
	<TR><TD>El movimiento aplica para control de IVA</TD><TD><input type='checkbox' id='aplica' value='1' checked ></TD></TR>
	
	
	</TABLE>
	
</div>
<div id='ProveedoresLista' title='Lista de proveedores relacionados'>
</div>
<script>
	function compruebaid(){
		if($('#ietu').val() == 24){
			alert("Solo debe considerar las cuotas tanto de IMSS, Infonavit y SAR que son a cargo del empleador");
		}
	}
	
</script>