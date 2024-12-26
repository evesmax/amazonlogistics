
<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Ordenes de Compra</title>
	<link rel="stylesheet" href="">
	</head>
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<script src="../../libraries/jquery.min.js"></script>
	<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="js/cliente.js"></script>
	<!--Select 2 -->
	<script src="../../libraries/select2/dist/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

	<!--Modificaciones RC -->
	<script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
	<script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
	<script src="../../libraries/export_print/buttons.html5.min.js"></script>
	<script src="../../libraries/export_print/jszip.min.js"></script>
	<!--Button Print css -->
	<!-- <link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css"> -->
	<link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">


	<link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<!--<script src="../../libraries/dataTable/js/datatables.min.js"></script>-->
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
   <script>
   $(document).ready(function() {

		$('#tableGrid').DataTable({
				"columns": [
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					null,
					{ "orderable": false },
				],
				dom: 'Bfrtip',
				buttons: [ 'excel' ],
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
   });
   </script>
   <!-- Alinear a la derecha contenido del td de la tabla del modal AM -->
	<style>
    	.alignright { text-align: right; }
    	.modal-title{ text-align:center;background-color: #5cb85c;color: white;font-weight:normal;padding: 5px;}
   
	</style>
<body>
<div class="container well">
	<div class="row">
		<div class="col-sm-8">
			<h3>
				Clientes
				<button class="btn btn-primary" onclick="newClient();"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Cliente</button>
			</h3>
		</div>

	</div>
<!-- 	<div class="row">
		<div class="col-sm-12">
			<label>Total: <?php echo $clientes['total']; ?></label>
		</div>
	</div> -->
	<div class="row">
		<div class="col-sm-12" style="overflow:auto;">
					 <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGrid">
					<thead>
					  <tr>
						<th>ID</th>
						<th>Código</th>
						<th>RFC</th>
						<th>Razón Social</th>
						<th>Nombre</th>
						<th>Teléfono</th>
						<th>Correo electrónico</th>
						<th>Estatus</th>
						<th>Acciones</th>
					  </tr>
					</thead>
					<tbody>
						<?php
						$status="";

						foreach ($clientes['clientes'] as $key => $value) {

							if($value['borrado']==0){
								$status = '<span class="label label-success">Activo</span>';
							}else{
								$status = '<span class="label label-danger">Inactivo</span>';
							}
							echo '<tr>';

							echo '<td><a href="index.php?c=cliente&f=index&idCliente='.$value['id'].'" title="Editar">'.$value['id'].'</a></td>';
							echo '<td>'.$value['codigo'].'</td>';							
							echo '<td>'.$value['rfc'].'</td>';
							$razonSocial = $this->ClienteModel->razonSocialCliente($value['id']);
							$razonSocialDisplay = is_null($razonSocial['razon_social']) ? $value['nombretienda'] : $razonSocial['razon_social'];
							echo '<td>'.$razonSocialDisplay.'</td>';
							echo '<td>'.$value['nombre'].'</td>';
							echo '<td>'.$value['celular'].'</td>';
							echo '<td>'.$value['email'].'</td>';
							echo '<td>'.$status.'</td>';
							echo '<td>';
							$cxcStr = "../../modulos/appministra/index.php?c=cuentas&f=cuentasxcobrar&id=" . $value['id'];
							if($value['borrado']==0){
									echo '<a onclick="borraCliente('.$value['id'].');" class="active"><i alt="Desactivar" title="Desactivar" class="fa fa-toggle-on fa-lg" aria-hidden="true" style="color:#000; "></i></a>';
									echo '<a onclick="window.parent.agregatab(&apos;' . $cxcStr . '&apos;,&apos;Detalle del cliente&apos;,&apos;&apos;,1675);" href="#" class="active"><i alt="Cuentas por cobrar" title="Cuentas por cobrar" class="fa fa-file-o fa-lg" aria-hidden="true" style="color:#000; padding-left: 20px;""></i></a>';
									echo '<a data-toggle="modal" data-target="#exampleModal" onclick="modalMovimientos('.$value['id'].');" id="modal"><i alt="Ver movimientos" title="Ver Movimientos" class="fa fa-history fa-lg" aria-hidden="true" style="color:#000;padding-left: 20px; "></i></a>';
							}else{
								echo '<a onclick="activaCliente('.$value['id'].');" class="active"><i alt="Activar" title="Activar" class="fa fa-toggle-off fa-lg" aria-hidden="true" style="color:#000"></i></a>';
							}

							echo '</td>';

							echo '</tr>';

							$razonSocialDisplay = "";
						}
						?>
					</tbody>
				</table>
		</div>
	</div>
</div>

<!-- Modal AM -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content table-responsive">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">MOVIMIENTOS POR CLIENTES</h5>
				<div class="form-group nombreCliente" style="padding-top: 15px" hidden>
					<label>Cliente:</label>
					<input type="text" name="nombreCliente" id="nombreCliente" style="border:0px">
				</div>
			</div>
			<div class="modal-body">
				<table id="tablemovimientos" class="table table-striped table-bordered" style="width:100%">
					<thead>
						<tr style="background-color: black;color: white;text-align: center;">
							<td colspan="3">HISTORIAL DE MOVIMIENTOS</td>
						</tr>
						<tr>
							<th>Fecha</th>
							<th style="text-align: center;">Tipo de movimiento</th>
							<th>Monto</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- Termina Modal -->


</body>
</html>
