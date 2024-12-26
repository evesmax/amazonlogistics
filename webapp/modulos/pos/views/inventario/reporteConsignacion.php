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
    <!-- DataTable -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<!-- Modificaciones RC -->
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
	<script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
	<script src="../../libraries/export_print/buttons.html5.min.js"></script>
	<script src="../../libraries/export_print/jszip.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">

    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>


</head>
<body>


	<div class="container well">

	

		<div class="row"> 
			<div class="col-md-12"><h3> Reporte de ventas a consigna</h3></div> 
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
                            <input id="desde" class="form-control" type="text" placeholder="">
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <label>Hasta</label>
                        <div id="datetimepicker2" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input id="hasta" class="form-control" type="text" placeholder="">
                        </div>

                    </div>
					<div class="col-sm-3">
						<div class="form-group">
							<label for="proveedor">Proveedor</label>
							<select id="proveedor" class="form-control">
								<!-- <option value=""> - Todos - </option> -->
								<?php 
								foreach ($proveedores as $key => $value) { 
								?>
									<option value="<?php echo $value['idPrv']; ?>"><?php echo $value['razon_social']; ?></option>
								<?php } 
								?>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label for="almacen">Almacen</label>
							<select id="almacen" class="form-control">
								<?php 
								foreach ($almacenes as $key => $value) { 
								?>
									<option value="<?php echo $value['id']; ?>"><?php echo $value['nombre']; ?></option>
								<?php } 
								?>
							</select>
						</div>
					</div>
					
					

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
		<div id="reporte" class="row" style="overflow-x:  scroll;">
			<table id="tablaConsigna" class="table table-striped table-bordered sizeprint" style="width: 100%">
				<thead>
					<tr>
						<th> ID producto </th>
						<th> Código  </th>
						<th> Producto  </th>
						<th> Costo unitario </th>
						<th> Importe venta </th>
						<th> # Inventario Actual </th>
						<th> Entradas </th>
						<th> Devolución cliente </th>
						<th> Devolución proveedor </th>
						<th> Costo dev. proveedor </th>
						<th> # Merma </th>
						<th> Importe mermas </th>
						<th> # Ventas </th>
						<th> Importe de ventas </th>
				  	</tr>
				</thead>
				<tbody>
					
				</tbody>
				<tfoot>

				</tfoot>
			</table>
			
		</div>
	</div>
	
</body>
<script>
	var tablaConsigna = $('#tablaConsigna').DataTable({
		dom: 'Bfrtip',
		buttons: ['excel'],
		destroy: true,
		language: {
			search: "Buscar:",
			lengthMenu:"",
			zeroRecords: "No hay datos.",
			infoEmpty: "",
			info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
			paginate: {
				first:      "Primero",
				previous:   "Anterior",
				next:       "Siguiente",
				last:       "Último"
			},
		},
		aaSorting : [[0,'desc' ]]
	});
	$('#desde').datepicker({
        format: "yyyy-mm-dd",
        language: "es"
    });
    $('#hasta').datepicker({
        format: "yyyy-mm-dd",
        language: "es"
    });
    $('#procesar').click(function(event) {
    	$('#desde').val()
    	$('#hasta').val()
    	$('#proveedor').val()
    	$('#almacen').val()
    	$.ajax({
            url: 'ajax.php?c=inventario&f=reporteConsignacion',
            type: 'GET',
            dataType: 'json',
            data: 	{ 
            			desde : $('#desde').val(),
                    	hasta : $('#hasta').val(),
                    	proveedor : $('#proveedor').val(),
                    	almacen :  $('#almacen').val()
                	},
        })
        .done(function(resp) {
        	var tablaConsigna = $('#tablaConsigna').DataTable({
					dom: 'Bfrtip',
					buttons: ['excel'],
					destroy: true,
					language: {
						search: "Buscar:",
						lengthMenu:"",
						zeroRecords: "No hay datos.",
						infoEmpty: "",
						info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
						paginate: {
							first:      "Primero",
							previous:   "Anterior",
							next:       "Siguiente",
							last:       "Último"
						},
					},
					aaSorting : [[0,'desc' ]]
				});
        	tablaConsigna.clear().draw();
        	//$('#tablaConsigna>tbody').empty()

        	$.each(resp, function(index, val) {
/*           		$('#tablaConsigna>tbody').append(`
           			<tr>
						<td> ${val.id} </td>
						<td> ${val.codigo} </td>
						<td> ${val.nombre} </td>
						<td style="text-align: right;"> ${parseFloat(val.costo).toFixed(2)} </td>
						<td style="text-align: right;"> ${parseFloat(val.precio).toFixed(2)} </td>
						<td style="text-align: right;"> ${val.inventarioActual} </td>
						<td style="text-align: right;"> ${val.entradasOC} </td>
						<td style="text-align: right;"> ${val.devolucionesV} </td>
						<td style="text-align: right;"> ${val.devolucionesOC} </td>
						<td style="text-align: right;"> ${parseFloat(val.costoDevOC).toFixed(2)} </td>
						<td style="text-align: right;"> ${val.merma} </td>
						<td style="text-align: right;"> ${parseFloat(val.importeMerma).toFixed(2)} </td>
						<td style="text-align: right;"> ${val.salidasV} </td>
						<td style="text-align: right;"> ${parseFloat(val.importeV).toFixed(2)} </td>
				  	</tr>
           		`);*/
           		
           		tablaConsigna.row.add($(`
           			<tr>
						<td> ${val.id} </td>
						<td> ${val.codigo} </td>
						<td> ${val.nombre} </td>
						<td style="text-align: right;"> ${val.costo} </td>
						<td style="text-align: right;"> ${val.precio} </td>
						<td style="text-align: right;"> ${val.inventarioActual} </td>
						<td style="text-align: right;"> ${val.entradasOC} </td>
						<td style="text-align: right;"> ${val.devolucionesV} </td>
						<td style="text-align: right;"> ${val.devolucionesOC} </td>
						<td style="text-align: right;"> ${val.costoDevOC} </td>
						<td style="text-align: right;"> ${val.merma} </td>
						<td style="text-align: right;"> ${val.importeMerma} </td>
						<td style="text-align: right;"> ${val.salidasV} </td>
						<td style="text-align: right;"> ${val.importeV} </td>
				  	</tr>
           		`)).draw();
           	});

        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    });
</script>
</html>