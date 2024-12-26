<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Recalculo de inventario</title>

	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome-4.7.0/css/font-awesome-4.7.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/typeahead/typeahead.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric.js"></script>
	<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
    
<!--Data Tables -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.es.js" type="text/javascript"></script>
   <script>

    <!-- Modificaciones RC -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>

    <!--Button Print css -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
    <!--Button Print js -->
    <script src="../../libraries/dataTable/js/dataTables.buttons.min.js"></script>
    <script src="../../libraries/dataTable/js/buttons.print.min.js"></script>
    <script src="../../libraries/export_print/pdfmake.min.js"></script>
    <script src="../../libraries/export_print/vfs_fonts.js"></script>

</head>
<body>


	<div class="container well">

<!-- 	<div class="col-sm-1">
        <a class="btn btn-default" href="index.php?c=ajustesinventario&f=indexGrid"><i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar</a>
    </div> -->

		<div class="row"> 
			<div class="col-md-12"><h3> Kárdex Antibióticos</h3></div> 
		</div>
		<div class="panel-default">
			<div class="panel-heading">
				<div class="row">

					<div class="col-sm-3">
                        <label>Desde</label>
                        <div id="datetimepicker1" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input id="desde" class="form-control" type="text" placeholder="Fecha de inicio">
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <label>Hasta</label>
                        <div id="datetimepicker2" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input id="hasta" class="form-control" type="text" placeholder="Fecha final">
                        </div>
                    </div>

					<!-- <div class="col-sm-2">
						<div class="form-group">
							<label for="usuario">Usuarios</label>
							<select id="usuario" class="form-control" >
								<option value=""> - Todos - </option>
								<?php 
								foreach ($usuarios as $key => $value) { 
								?>
									<option value="<?php echo $value['idempleado']; ?>"><?php echo $value['nombre']; ?></option>
								<?php } 
								?>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label for="medico">Médicos</label>
							<select id="medico" class="form-control" >
								<option value=""> - Todos - </option>
								<?php 
								foreach ($medicos as $key => $value) { 
								?>
									<option value="<?php echo $value['idmedico']; ?>"><?php echo $value['nombre']; ?></option>
								<?php } 
								?>
							</select>
						</div>
					</div>
					
					<div class="col-sm-2">
						<div class="form-group">
							<label for="proveedore">Proveedores</label>
							<select id="proveedor" class="form-control" >
								<option value=""> - Todos - </option>
								<?php 
								foreach ($proveedores as $key => $value) { 
								?>
									<option value="<?php echo $value['idPrv']; ?>"><?php echo $value['razon_social']; ?></option>
								<?php } 
								?>
							</select>
						</div>
					</div> -->

					<div class="col-sm-3">
						<div class="form-group">
							<label for="producto">Producto</label>
							<select id="producto" class="form-control" >
								<!-- <option value=""> - Todos - </option> -->
								<?php 
								foreach ($productos as $key => $value) { 
								?>
									<option value="<?php echo $value['id']; ?>"><?php echo $value['nombre']; ?></option>
								<?php } 
								?>
							</select>
						</div>
					</div>					
				<!-- </div>


				<div class="row"> 
					
                    <div class="col-sm-9"></div> -->
                    <div class="col-sm-3 ">
						<div class="form-group">
							<label > </label>
							<button id="procesar" class="btn btn-default" style="width: 100%; height: 100%;">Procesar</button>
						</div>
					</div>

				</div>
			</div>
		</div>
		<br>
		<div  class="row">
			<table class="table">
				<thead>
					<th>Fecha</th>
					<th>Usuario</th>
					<th>Movimiento</th>
					<th>Lote</th>
					<th>Cantidad</th>
					<th>Inventario Inicial</th>
					<th>Inventario Actual</th>
					<th>Proveedor</th>
					<th>Médico</th>
					<th>Cédula</th>
					<th>Receta</th>
					<th>Estatus</th>
				</thead>
				<tbody id="reporte"></tbody>
			</table>
			
		</div>
	</div>
	
</body>
<script>
		$('#desde')
		.datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });
        $('#hasta')
        .datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });
		$('#usuario')
		.select2({ width: '100%' });
		$('#medico')
		.select2({ width: '100%' });
		$('#producto')
		.select2({ width: '100%' });
		$('#proveedor')
		.select2({ width: '100%' });
		$('#procesar').on('click', function(event) {
			event.preventDefault();
			
			if($('#desde').val() == '' || $('#hasta').val() == ''){
				alert("Introduce un rango de fecha válido.");
				return;
			}
			$.ajax({
			    url: 'ajax.php?c=inventario&f=generaKardexAntibiotico',
			    type: 'GET',
			    dataType: '',
			    data: {
			    	desde: $('#desde').val() ,
			    	hasta: $('#hasta').val() ,
			    	usuario: $('#usuario').val() ,
			    	medico: $('#medico').val() ,
			    	producto: $('#producto').val() ,
			    	proveedor: $('#proveedor').val() ,
				},
			})
			.done(function(data) {
			    console.log("success");
			    $('#reporte').empty().append(data);
			})
			.fail(function() {
			    console.log("error");
			})
			.always(function() {
			    console.log("complete");
			});
		});
</script>
<script src="js/inventario.js"></script>
</html>