
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
 
<b>CLASIFICACION NIF DE CUENTAS</b><br /><br />
<table>
<tr><th>NUMERO DE CUENTA</th><th>DESCRIPCION</th><th>ASIGNAR NIF</th></tr>
<?php
$select = "<option value='0'>Ninguno</option>";
while($n = $nif->fetch_object())
{
	$select .= "<option value='$n->id'>$n->clasificacion / $n->nivel</option>";
}
$cont=1;
while($cm = $cuentas_mayor->fetch_object())
{
	echo "<tr class='niftr' cont='$cont'><td>$cm->manual_code</td><td>$cm->description</td><td><input type='hidden' id='lbl_$cont' value='$cm->nif'><select class='selects' id='sel_$cont' onchange='cambia($cont,$cm->account_id)'>$select</select></td></tr>";
	$cont++;
}
?>
</table>
<script language='javascript'>
$(document).ready(function()
{
	$(".niftr").each(function()
	{
		var cont = $(this).attr('cont');
		$("#sel_"+cont).val($("#lbl_"+cont).val())
	});
	$(".selects").select2({
        	 width : "550px"
        });
});
function cambia(s,c)
{
	$.post("ajax.php?c=AccountsTree&f=UpdateNif",
                  {
                	IdCuenta: c,
                	Valor: $("#sel_"+s).val()
                  });
}
</script>