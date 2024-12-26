<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script language='javascript'>
$(function()
{
	$("#verMovs").dialog({
      autoOpen: false,
      width: 600,
      height: 500,
      modal: true,
	  buttons: 
	  {
	 	  "Cerrar": function () 
		 {
			$("#verMovs").dialog('close')
		 
		 }
	  }
    });
	$('.movs').click(function()
		{
			event.preventDefault()
			$('#contenido').html('');
			$('#verMovs').dialog('open');
			$.post("ajax.php?c=CaptPolizas&f=MovimientosPolizasPDV",
 		 {
    		IdPoliza: this.id
  		 },
  		 function(data)
  		 {
  		 	$('#contenido').html(data);
  		 	
  		 });
		});
});
function restaurar(id)
{
	var preg = confirm('Esta seguro de activar la poliza?.');
	if(preg)
	{
	$.post("ajax.php?c=CaptPolizas&f=RestaurarPoliza",
 		 {
    		IdPoliza: id,
    		PDV:1 
  		 },function(data)
  		 {
  		 	alert('Se ha Autorizado la Poliza')
  		 	location.reload(); 
  		 });
	}
}
function eliminar(id)
{
	var preg = confirm('Si elimina la poliza definitivamente ya no podra recuperarla despues.');

	if(preg)
	{
		$.post("ajax.php?c=CaptPolizas&f=EliminarPoliza",
 		 {
    		IdPoliza: id 
  		 },function(data)
  		 {
  		 	alert('Se ha eliminado la Poliza.')
  		 	location.reload(); 
  		 });
	}
}

function checking(elem)
{
	$('#'+elem).click()
}
function actualiza(elem)
{
	var es_activo;
	if($('#cb_'+elem).is(':checked'))
	{
		es_activo = 1
	}
	else
	{
		es_activo = 0
	}
	$.post("ajax.php?c=CaptPolizas&f=ActivaMovPDV",
 		 {
    		Id: elem ,
    		Activo: es_activo
  		 });
}

</script>
<style>
.over{
background-color:#525154;
color:#FFF;
}
.out{
background-color:;
color:;
}
td
{
	width:158px;
	height:30px;
	text-align: center;
	border:1px solid #BDBDBD;
}

a.movs
{
	text-decoration: none;
	font-weight: bold;
	color:black;
}
a.movs:hover
{
	text-decoration: underline;
	color:white;
}
</style>
<div id='title'>Lista de Polizas sin autorizar del Punto de Venta / <a href='index.php?c=CaptPolizas&f=ListaPolizasEliminadas'>Inactivas(Acontia)</a></div>
<div class='lateral' style='width:807px;'>
<div class='nmsubtitle' style='width:801px;'>Autorizaci&oacute;n para generar movimientos contables</div>
<table>
	<tr style='background-color:#BDBDBD;color:white;font-weight:bold;'><td>Referencia</td><td>Fecha</td><td>Detalles</td><td>Autorizar</td><td>Eliminar</td></tr>
<?php
while($LPP = $ListaPolizasPDV->fetch_assoc())
{
	echo "<tr onMouseOver=\"this.className='over'\" onMouseOut=\"this.className='out'\"><td>".$LPP['referencia']."</td><td>".$LPP['fecha']."</td><td><a href='#' class='movs' id='".$LPP['id']."'>Movimientos</a></td><td><a href='javascript:restaurar(".$LPP['id'].")'><img src='images/autorizado.png'></a></td><td><a href='javascript:eliminar(".$LPP['id'].")' ><img src='images/eliminado.png'></a></td></tr>";
}
?>
</table>
</div>
<div id='verMovs' title='Ver Movimientos'>
	<div id='contenido'>
		
	</div>
</div>