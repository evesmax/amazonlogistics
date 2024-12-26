<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<?php include('../../netwarelog/design/css.php');?>
<LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->

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
			$.post("ajax.php?c=CaptPolizas&f=MovimientosPolizasEliminadas",
 		 {
    		IdPoliza: this.id
  		 },
  		 function(data)
  		 {
  		 	$('#contenido').html(data);
  		 	
  		 });
		});
	//EXTENDIENDO LA FUNCION CONTAINS PARA HACERLO CASE-INSENSITIVE
	$.extend($.expr[":"], {
"containsIN": function(elem, i, match, array) {
return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}
});
//-------------------------------------------------------------
// INICIA GENERACION DE BUSQUEDA
			$("#buscar").bind("keyup", function(evt){
				//console.log($(this).val().trim());
				if(evt.type == 'keyup')
				{
					$("#lista tr:containsIN('"+$(this).val().trim()+"')").css('display','table-row');
					$("#lista tr:not(:containsIN('"+$(this).val().trim()+"'))").css('display','none');
					$("#lista tr:containsIN('*1*')").css('display','table-row');
					if($(this).val().trim() === '')
					{
						$("#lista tr").css('display','table-row');
					}
				}

			});
		// TERMINA GENERACION DE BUSQUEDA

});


function restaurar(id)
{
	$.post("ajax.php?c=CaptPolizas&f=RestaurarPoliza",
 		 {
    		IdPoliza: id,
    		PDV:0  
  		 },function(data)
  		 {
  		 	alert('Se ha restaurado la Poliza')
  		 	location.reload(); 
  		 });
}
function eliminar(id)
{
	var preg = confirm('Si elimina la poliza definitivamente ya no podra recuperarla despues.');

	if(preg)
	{
		$.post("ajax.php?c=CaptPolizas&f=EliminarPoliza",
 		 {
    		IdPoliza: id 
  		 },function()
  		 {
  		 	alert('Se ha eliminado la Poliza.')
  		 	location.reload(); 
  		 });
	}
}

</script>
<style>
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
<?php
if($PDV)
{
    $cpdv = "/ <a href='index.php?c=CaptPolizas&f=ListaPolizasPDV'>Generadas PDV</a>";
}
?>
<div class="nmwatitles">Lista de Polizas Inactivas <?php echo $cpdv; ?></div>
<div class='lateral' style='width:807px;'>
<div class='nmsubtitle' style='width:801px;'>Polizas recuperables <input type='text' class="nmcatalogbusquedainputtext" id='buscar'  name='buscar' placeholder='Buscar'></div>
<table id='lista'>
	<tr style='background-color:#BDBDBD;color:white;font-weight:bold;'><td class="nmcatalogbusquedatit"><b style='visibility:hidden;'>*1*</b># de Poliza</td><td class="nmcatalogbusquedatit">Concepto</td><td class="nmcatalogbusquedatit">Fecha</td><td class="nmcatalogbusquedatit">Ver Movimientos</td><td class="nmcatalogbusquedatit">Restaurar</td><td class="nmcatalogbusquedatit">Eliminar completamente</td></tr>
<?php
while($LPE = $ListaPolizasEliminadas->fetch_assoc())
{
	echo "<tr onMouseOver=\"this.className='over'\" onMouseOut=\"this.className='out'\"><td>".$LPE['idperiodo']."/".$LPE['numpol']."</td><td>".$LPE['concepto']."</td><td>".$LPE['fecha']."</td><td><a href='#' class='movs' id='".$LPE['id']."'>Movimientos</a></td><td><a href='javascript:restaurar(".$LPE['id'].")' class='bot'><img src='images/restaurado.png'></a></a></td><td><a href='javascript:eliminar(".$LPE['id'].")' style='font-weight:bold;color:red;' class='bot'><img src='images/eliminado.png'></a></a></td></tr>";
}
?>
</table>
</div>
<div id='verMovs' title='Ver Movimientos'>
	<div id='contenido'>
		
	</div>
</div>