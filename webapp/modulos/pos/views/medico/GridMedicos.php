<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Medicos</title>
		<link rel="stylesheet" href="">
	</head>

	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<script src="../../libraries/jquery.min.js"></script>
	<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="js/medico.js"></script>
<!--Select 2 -->
	<script src="../../libraries/select2/dist/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

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

	<script>
		$(document).ready(function() {
			$('#tableGrid').DataTable({
				dom: 'Bfrtip',
				buttons: ['excel'],
				language: {
					search: "Buscar:",
					lengthMenu:"",
					zeroRecords: "No hay datos.",
					infoEmpty: "No hay datos que mostrar.",
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
			$("#layout_row").attr("abierto","0").hide();
			$("#layout_precios").attr("abierto","0").hide();
		});

		function mostrar_layout() {
			if(!parseInt($("#layout_row").attr("abierto")))
				$("#layout_row").attr("abierto","1").show("slow");
			else
				$("#layout_row").attr("abierto","0").hide("slow");
		}

		function mostrar_layout2() {
			if(!parseInt($("#layout_precios").attr("abierto")))
				$("#layout_precios").attr("abierto","1").show("slow");
			else
				$("#layout_precios").attr("abierto","0").hide("slow");
		}

		function validar(t) {
			if(t.layout.value == '') {
				alert("Agregue un archivo xls.");
				return false;
			}
		}
	</script>

	<body>
		<div class="container well">
			<div class="row">
				<div class='col-sm-8'>
					<h3>
						Medicos
						<button class="btn btn-primary" onclick="nuevoMedico()" >
							<i class="fa fa-plus" aria-hidden="true"></i> Nuevo 
						</button>
					</h3>
				</div>
<!-- 				<div class='col-sm-12 col-md-2'>
					<button class="btn btn-default" title='Subir destinos mediante layout' onclick='mostrar_layout()'><span class='glyphicon glyphicon-upload'></span></button>
				</div> -->
	
			</div>

			<!-- <div class='row' id='layout_row' >
				<div class='col-sm-12 col-md-offset-2 col-md-5'>
					<b>Subir destinos mediante layout</b> / <a href='importacion/destinos.xls'>Descargar</a><br />
					<form action='index.php?c=destino&f=subeLayout' method='post' name='archivo' enctype="multipart/form-data" id='arch' onsubmit='return validar(this)'>
						<input type='file' id='layout' name='layout'><br />
						<button type='submit' onclick='cargar_productos()'>Cargar</button>
					</form>
				</div>
			</div> --> 


			<div class="row">
				<div class="col-sm-12" style="overflow:auto;">
					<table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGrid">
						<thead>
							<tr>
								<th>ID</th>
								<th>Nombre</th>
								<th>Cédula</th>
								<th>Estatus</th>
								<th>Acciones</th>
							</tr>
						</thead>

						<tbody>
							<?php 
								foreach ($medicos as $key => $value) {

									if($value['activo']==1){
										$status = '<span class="label label-success">Activo</span>';
										$botones = '<!-- <button class="btn btn-primary btn-xs active" title="Editar" data-toggle="modal" data-target=".editarHotel" onclick="modalEditarHotel('. "{$value['id']} , '{$value['clave']}'  , '{$value['nombre']}'  , '{$value['activo']}'" .')">
												<span class="glyphicon glyphicon-edit"></span>
											</button> -->
											<a href="#" class="btn btn-danger btn-xs active" onclick="cambiarEstatus('. "{$value['id']} , '0'" .')" title="Desactivar">
												<span class="glyphicon glyphicon-remove"></span>
											</a>';
									} else {
										$status = '<span class="label label-danger">Inactivo</span>';
										$botones = '<a href="#" class="btn btn-info btn-xs active" onclick="cambiarEstatus('. "{$value['id']} , '1'" .')" title="Activar">
												<span class="glyphicon glyphicon-check"></span>
											</a>';
									}

									echo 	"<tr>
												<td><a href='./index.php?c=medico&f=index&idMedico={$value['id']}'>{$value['id']}</a> </td>
												<td>{$value['nombre']}</td>
												<td>{$value['cedula']}</td>
												<td>$status</td>
												<td>$botones</td>
											</tr>";
									
								}
							?>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="modalActiva" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content panel-warning">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<div class="modal-header panel-heading">
						<h4 id="modal-label">Activar!</h4>
					</div>
					<div class="modal-body">
						<p>Deseas activar este destino?</p>
						<input type="hidden" id="activaProd">
					</div>
					<div class="modal-footer">
						<button id="modal-btnconf2-uno" type="button" class="btn btn-danger" onclick="activar2();">Activar</button>
					</div>
				</div>
			</div>
		</div>

	</body>
</html>