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
		<?php $x=1; foreach ($etiquetas['rows'] as $k => $v) { ?>
		<br>
		<div style="border: 1px dashed #d2d2d2; padding: 25px; width: 450px;">
			<table width='400' border=0 style="font-size: 13px;">
			<tr><td><?php echo $v['nombre']; ?></td></tr>
			<tr><td><img style="margin-left: -6px;" id="barcode_<?php echo $x; ?>" width="250" height="122" /> <?php echo $v['codigo']; ?></td></tr>
			<tr><td><?php echo $v['fecha']; ?>&nbsp;&nbsp;<?php echo $v['lote']; ?>&nbsp;&nbsp;<?php echo $v['peso']; ?></td></tr>
			</table>
		</div>
		<script>
		  JsBarcode("#barcode_<?php echo $x; ?>", "<?php echo $v['codigo']; ?>", {
		    format:"CODE128",
		    displayValue:false,
		    fontSize:14,
		    lineColor: "#000"
		  });
		</script>
		<?php $x++; } ?>
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