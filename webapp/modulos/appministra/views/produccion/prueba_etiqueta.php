<head>
	<script src="js/barcode.js" type="text/javascript"></script>
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
	$array = array(
		['8919101','ENERO 2020','G300129','OXY THERM SOBRE','7.24'],
		['8989101','FEBRERO 2020','H270129','NAPP C BOTE','8.24'],
		['9090101','MAYO 2020','K170129','NOCHE SUAVE, 500 Ml','18.98'],
		['8912101','ENERO 2020','G250129','COFFE CROM BOTE','15.04'],
		['8955101','ENERO 2020','G310129','CLOROFIL CAPS','3.76']
		//9106005	Mayo del 2020	L200129	Oxy Cocktail Set Bote	6.4500
);
$x = 0;
foreach ($array as $value){
  //print json_encode($value);
  //echo $array[$x][0] . " " . $array[$x][1] . " " ;

  $codigo			= $array[$x][0];
  $caducidad 	= $array[$x][1];
  $lote				= $array[$x][2];
  $producto   = $array[$x][3];
  $peso				= $array[$x][4];
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
			<!--<a href='javascript:window.print()'>Imprimir</a>-->
		</div>
	</center>
</div>
<!--
<script language='javascript'>
$(function() {
  $('footer').remove();
  window.print();
});
</script>-->
