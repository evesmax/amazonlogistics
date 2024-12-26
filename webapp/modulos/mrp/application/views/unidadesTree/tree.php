<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/unidadesTree.css">
	<!--<LINK href="../../../netwarelog/design/default/netwarlog.css"   title="estilo" rel="stylesheet" type="text/css" / -->
		<?php include('../../netwarelog/design/css.php');?>
	    <LINK href="../../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->

	<script src="../js/unidadesTree.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	
	<script>
	$(function() {
		arbolUnidades.init();
	});
	</script>

	<title>Tree</title>
</head>
<body>
	<div>
		<div class="nmwatitles">Arbol de Unidades</div>
	</div>
	<div id="divUnidades" class="well">
		<select id="cboUnidadesTree" onchange="arbolUnidades.desglosadas();" class="nminputselect">
			<option value="">Selecciona</option>
			<?php 

				foreach ($uniBasicas as $key => $value) {
					echo "<option id='".$value['id']."' value='".$value['identificadores']."'>".$value['tipo']."</option>";
				}
				echo "<option value='000'>Sin unidad Basica</option>";
			 ?>
		</select>
		<input type="button" id="btnNuevaUnidad" class="btn btn-success col-md-offset-1" value="Nuevo Tipo de Unidad">
		<input type="button" id="btnModificarUnidad" class="btn btn-warning col-md-offset-1" value="Modificar Tipo de Unidad">
		<input type="button" id="btnEliminarUnidad" class="btn btn-danger col-md-offset-1" value="Eliminar Tipo de Unidad">
		<div id="unidadesDesglosadas" class="container" style="padding:0;border:1px solid;">
			<div class="col-sm-7" style="padding:0;border:1px solid;text-align: center;">
				Unidad
			</div>
			<div class="col-sm-5" style="padding:0;border:1px solid;text-align: center;">
				Acciones
			</div>
			<div class="col-sm-12" id="resultUnidades" style="padding:0">
				
			</div>
		</div>
	</div>
	<div id="diagloNuevaUnidad" style="display:none;">
		<div class="col-sm-12">
			<label for="">Nombre de la nueva Unidad Basica</label>
		</div>
		<div class="col-sm-12">
			<input type="text" id="txtNuevaUnidad" value="">
		</div>
	</div>
	<div id="dialogModificarUnidad" style="display:none;">
		<div class="col-sm-12">
			<label id="lblAnteriorNombre" class="col-sm-6"> Nombre anterior :</label>
			<label id="lblAnteriorNombre2" class="col-sm-6"> </label>
			<label id="lblNuevoNombre" class="col-sm-6"> Nuevo Nombre :</label>
			<input type="text" id="txtModificaNombre" value="">
		</div>
	</div>
	<div id="dialogEliminarUnidad" style="display:none;">
		<div class="col-sm-12">
			Estas Seguro de eliminar la Unidad Basica 
			<label id="lblUnidadEliminar"></label>
		</div>
	</div>
</body>
</html>