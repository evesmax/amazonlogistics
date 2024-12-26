<?php

if($configuraciones['hideprod'] == 1){
	?>
	<script>
		$("#backdep").show();
	</script>
	<?php

}else{
?>

<button
	class="btn btn-lg btn-warning"
	style="height: 50px; width: 100px"
	onclick="comandera.productos = ''; comandera.area_inicio()">
	<div class="row">
		<div>
			<i class="fa fa-undo fa-lg"></i><br />
		</div>
	</div>
</button>


<?php
}
 ?>



<?php if($configuraciones['hideprod'] == 1){
	$class = "col-md-7 col-xs-7 right";
	$style = "width:60%; border: solid; text-aling:right;";
	$style2= "width:16%;";
}else{
	$class = "col-md-6 col-xs-6";
	$style = "";
}
 ?>

<?php
foreach ($familias as $key => $value) { ?>
	<button
		style="<?php echo $style2; ?>"
		class="btn btn-lg btn-departamento"
		onclick="comandera.listar_lineas({
			familia: <?php echo $value['idFam'] ?>,
			div: 'div_departamentos',
			div_productos: 'div_productos'
		})">
		<div class="row">
			<div style="width:100px; height: 15px; font-size: 13px;">
				<?php echo substr($value['nombre'], 0, 20)  ?>
			</div>
		</div>
	</button><?php
}
?>
