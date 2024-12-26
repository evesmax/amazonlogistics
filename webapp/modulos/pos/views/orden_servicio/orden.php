<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Solicitud de Servicio</title>
	<link rel="stylesheet" href="">
	</head>
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<script src="../../libraries/jquery.min.js"></script>
	<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="http://momentjs.com/downloads/moment.min.js"></script>
	<script src="js/orden_servicio.js"></script>
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
		<!-- Datepicker -->
	<link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
	<script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>
	<script src="../../libraries/datepicker/js/bootstrap-datepicker.es.js" type="text/javascript"></script>

	<link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<!--<script src="../../libraries/dataTable/js/datatables.min.js"></script>-->
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
   <script>
   $(document).ready(function() {
	$('#origenV').select2({ width: '100%' });
	$('#destinoV').select2({ width: '100%' });
	$('#nombreCliente').select2({ width: '100%' });

	$('#idaV,#regresoV').datetimepicker(
		{
			format: 'YYYY-MM-DD HH:mm'
		}
	);


		$('#tableGrid').DataTable({
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
<body>
<div class="container well">
	<div class="row">
		<div class="col-sm-8">
			<h3>
				Solicitud de Servicio
				<button class="btn btn-primary" onclick="newOrder();"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Solicitud</button>
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
						<th>#Solicitud</th>
					<!--	<th>Tipo de Vuelo</th>
						<th>Tipo de Viaje</th> -->
						<th>Numero de viaje</th>
						<th>Origen</th>
						<th>Destino</th>
						<th>Fecha Ida</th>
						<th>Fecha Regreso</th>
						<th>Fecha</th>
						<th>Estatus</th>
						<!--<th>Estatus</th> -->
					  </tr>
					</thead>
					<tbody>

						<?php
							$redo = '';
							$tipo = '';
							foreach ($solicitudes['solici'] as $key => $value) {
								if($value['estatus']==2){
									$pdf = '<button class="btn btn-default" onclick="verPdf('.$value['id'].')"><i class="fa fa-file-pdf-o"></i></button>';
								}elseif($value['estatus']==5){
									$pdf='<span class="label label-success" style="display:block;" onclick="verDocumentos(\''.$value['uuid'].'\')">Facturado</span>';
								}else{
									$pdf='<span class="label label-warning" style="display:block;">Pendiente</span>';
								}
								echo '<tr>';
								echo '<td>'.$value['id'].'</td>';
							/*	if($value['tipoVuelo']==1){
									$tipo = 'Nacional';
								}else{
									$tipo = 'Extranjero';
								}
								echo '<td>'.$tipo.'</td>';
								if($value['tipoViaje']==1){
									$redo = 'Redondo';
								}else{
									$redo = 'Sencillo';
								}
								echo '<td>'.$redo.'</td>'; */
								echo '<td>'.$value['num_viaje'].'</td>';
								echo '<td>'.$value['origenN'].'</td>';
								echo '<td>'.$value['destinoN'].'</td>';
								echo '<td>'.$value['fechaIda'].'</td>';
								echo '<td>'.$value['fechaRegreso'].'</td>';
								echo '<td>'.$value['fecha'].'</td>';
								echo '<td>'.$pdf.'</td>';
								//echo '<td>PDF</td>';
								//echo '<td>estatus</td>';
								echo '</tr>';
							}

						?>
					</tbody>
				</table>
		</div>
	</div>
</div>

<div class="modal fade" id="modalOrden">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><i class="fa fa-plane" aria-hidden="true"></i> Nueva Orden de Servicio</h3>
			</div>
			<div class="modal-body">
				<div id="primVisA">
					<!--<div role="tabpanel">-->
						<!-- Nav tabs -->
						<!--<ul class="nav nav-tabs" role="tablist" id="tabs_avion">
							<li role="presentation" class="active">
								<a href="#home" aria-controls="home" role="tab" data-toggle="tab">General</a>
							</li>
							<li id="tab_captura_nombres" style="display:none;">
								<a data-toggle="tab" href="#capturar_nombres">Capturar Pasajeros</a>
							</li>
							<li id="tab_captura_escalas" style="display:none;">
								<a data-toggle="tab" href="#capturar_escalas">Capturar Escalas</a>
							</li>
						</ul>-->
						<!-- Tab panes -->
						<div class="contenido">
							<div id="home">
								<div class="row">
									<div class="col-sm-12">
										<h3>Datos Generales</h3>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-4">
										<label>No. de Viaje</label>
										<input type="number" class="form-control" id="num_viaje">
									</div>
								</div>
								<div class="row">
									<div class="col-xs-4">
										<label>Ida:</label>
										<div id="datetimepicker1" class="input-group date">
											<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
											</span>
											<input id="idaV" class="form-control ghp" placeholder="Ida" type="text">
										</div>
									</div>
									<div class="col-xs-4">
										<label>Regreso:</label>
										<div id="datetimepicker2" class="input-group date">
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
											<input id="regresoV" class="form-control ghp" placeholder="Regreso" type="text">
										</div>
									</div>
									<div class="col-xs-4">
										<label>Nombre Clientes</label>

										<select class="form-control" id="nombreCliente">
										<?php
											foreach ($clientes['clientes'] as $key => $value) {
												echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';
											}

										?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-4">
										<label>No. Pasajeros</label>
										<input type="number" class="form-control" id="pasajeros" onchange='mostrar_captura_nombres()'>
									</div>
									<div class="col-xs-4">
										<label>Aeronave</label>
										<select class="form-control" id="aeronave" onchange="calcularCosto(this.value)">
										<?php
											while($a = $aeronaves->fetch_object()) {
												echo "<option value='$a->id'>$a->aeronave / $a->tipo</option>";
											}

										?>
										</select>
									</div>
									<div class="col-xs-4">
										<label>No. de Escalas</label>
										<input type="number" class="form-control" id="escalas" onchange='mostrar_captura_escalas()'>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-2" align="left" style="display:none;">
										<label>Tipo de Viaje</label>
										<div class="radio">
									  		<label><input name="nacional" value="2" type="radio">Nacional</label>
										</div>
										<div class="radio disabled">
									  		<label><input name="nacional" value="1" checked="true" type="radio">Extranjero</label>
										</div>
									</div>
									<div class="col-xs-2" align="left" style="display:none;">
										<label>Tipo de Vuelo</label>
										<div class="radio">
									  		<label><input name="redondo" value="2" type="radio">Redondo</label>
										</div>
										<div class="radio disabled">
									  		<label><input name="redondo" value="1" checked="true" type="radio">Sencillo</label>
										</div>
									</div>
									<div class="col-xs-2">
										<label>Tarifa de Viaje</label>
										<input type="number" class="form-control" id="tarifaDeViaje">
									</div>
									<div class="col-xs-2">
										<label>Costo de viaje</label>
										<input type="number" class="form-control" id="costo_viaje">
										<span class="label label-info" id="calculandoCosto"></span>
									</div>
									<div class="col-xs-2">
										<label>Moneda</label>
										<select id='idmoneda' class='form-control' onchange="cambiarTipoDeCambio(this.value)">
										<option value='1'>MXN</option>
										<option value='2'>USD</option>
										<option value='3'>EUR</option>
										</select>
									</div>
									<div class="col-xs-2">
										<label>Tipo de Cambio</label>
										<input type="number" class="form-control" id="tipo_cambio" value='1'>
										<span class="label label-info" id="esperaTipoCambio"></span>
									</div>
									<div class="col-xs-4">
										<label>Total de tiempo (minutos)</label>
										<input type="text" id="totalTiempo" class="form-control" readonly="readonly">
									</div>
								</div>
								<div class="row" style="display:none;">
										<select class="form-control" id="desOrigen">
											<option value="0" origenADis="0">-Selecciona-</option>
											<?php foreach ($destino['destinos'] as $key => $value) {
													echo '<option value="'.$value['id'].'" origenADis="'.$value['clave'].'">('.$value['clave'].') '.$value['nombre'].'</option>';
												} ?>
										</select>
								</div>
							</div>

							<!-- Capturar nombres -->
							<div id="capturar_nombres" style='display:none;background-color: #F5F5F5;margin-top:10px;padding:5px;'>
										<div class="row" style="margin-top:.5em;">
											<div class="col-xs-10">
												<h4 id="titulo_captura_nombre">Capturar nombres</h4>
											</div>
											<div class="col-xs-1">
												<!--<button type="button" class="btn btn-success btn-block" onclick='confirmar_inputs_nombres();'>
													<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
												</button>-->
											</div>
											<div class="col-xs-1">
												<button type="button" class="btn btn-danger btn-block" onclick='reiniciar_inputs_nombres();'>
													<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</button>
											</div>
										</div>

										<div class="row" id="inputs_nombres_container"></div>
							</div>
							<!-- Capturar Escalas -->
							<div id="capturar_escalas" style='display:none;background-color: #F5F5F5;margin-top:10px;padding:5px;'>
										<div class="row" style="margin-top:.5em;">
											<div class="col-xs-10">
												<h4 id="titulo_captura_nombre">Capturar Escalas</h4>
											</div>
											<div class="col-xs-1">
												<!--<button type="button" class="btn btn-success btn-block" onclick='confirmar_inputs_escalas();'>
													<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
												</button>-->
											</div>
											<div class="col-xs-1">
												<button type="button" class="btn btn-danger btn-block" onclick='reiniciar_inputs_escalas();'>
													<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
												</button>
											</div>
										</div>

										<div class="row" >
											<div class="col-xs-12" id="inputs_escalas_container"></div>
										</div>
							</div>
						</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" onclick="saveRequest();">Guardar</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalMensajes" role="dialog" style="z-index:1051;" data-backdrop="static">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Espere un momento...</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-default">
            <div align="center"><label id="lblMensajeEstado"></label></div>
            <div align="center"><i class="fa fa-refresh fa-spin fa-5x fa-fw margin-bottom"></i>
                 <span class="sr-only">Loading...</span>
             </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
