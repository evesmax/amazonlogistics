<head>
	<script src="../appministra/js/barcode.js" type="text/javascript"></script>
</head>
<style>
@media print
{
	#imprime { display:none; }
}
</style>
<div style='margin-left:10px;'>
	<center>

<?php
	
$x = 0;
foreach ($array as $value){
  
  $codigo			= $array[$x][0];
  $caducidad 	= $array[$x][1];
  $lote				= $array[$x][2];
  $producto   = $array[$x][3];
  $peso				= $array[$x][4]."Kg";
  $espacios 	= "&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp";
  $x = $x + 1;
 ?>

	 <div style="border: 1px dashed #d2d2d2; padding: 25px; width: 450px; margin-top:15px;">
	 	<table width='400' border=0 style="font-size: 13px;">
	 	<tr><td><? echo $producto; ?></td></tr>
	 	<tr><td><img style="margin-left: -6px;" id="barcode2" width="250" height="122" /> <? echo $espacios . $codigo ?></td></tr>
	 	<tr><td>Fecha de caducidad: <?php echo($caducidad);?>&nbsp;&nbsp;No. Lote <?php echo $lote;?>&nbsp;&nbsp;<?php echo $peso; ?></td></tr>
	 	</table>
	 </div>
	 <script>
	 	JsBarcode("#barcode2", "<?php echo $codigo; ?> ", {
	 		format:"CODE128",
	 		displayValue:false,
	 		fontSize:14,
	 		lineColor: "#000"
	 	});
	 </script>

<?php
}
 ?>
		<div id='imprime'>
			<a href='javascript:window.print()'>Imprimir</a>
		</div>
	</center>
</div>

<script language='javascript'>
$(function() {
  $('footer').remove();
  window.print();
});
</script>