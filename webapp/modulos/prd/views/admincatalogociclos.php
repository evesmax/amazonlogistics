<!DOCTYPE html>
<html>
<head>
	<title>Catalogo ciclos</title>
	<script type="text/javascript" src="../../libraries/jquery.min.js"></script>

	<script type="text/javascript" src="../../libraries/bootstrap-3.3.7/js/bootstrap.js"></script>
	<script type="text/javascript" src='js/admincatalogociclos.js'></script>
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">


	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/sl-1.2.5/datatables.min.js"></script>

	<!-- jQuery UI -->
	<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" ></script>

	<!-- Datatables Js-->
	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>


	<!-- Datatable -->
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>


	<!--Select 2 -->
	<link rel="stylesheet" href="../../libraries/bootstrap-select-1.9.3/dist/css/bootstrap-select.min.css"> 
	<script src="../../libraries/bootstrap-select-1.9.3/dist/js/bootstrap-select.min.js"></script>
	<script src="../../libraries/select2/dist/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />



</head>
<body>
	<div class="container" style="width:98%">
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="row">
							<input type="hidden" name="valortomado" id="valortomado">
							<div class="col-xs-12" style="margin-top: 10px">
								<button
								id="btn_nueva"
								onclick="agregarnuevo();"
								data-loading-text="<i class='fa fa-refresh fa-spin'></i>"
								type="button"
								class="btn btn-success btn-lg"
								style="width: 170px; margin-top: 0.5%;">
								<i class="fa fa-plus"></i> Agregar
							</button>
							<button
							id="btn_editar"
							onclick="recetas.vista_editar2()"
							data-toggle="modal"
							data-target="#modal_editar"
							data-loading-text="<i class='fa fa-refresh fa-spin'></i>"
							type="button"
							class="btn btn-primary btn-lg"
							style="width: 170px; margin-top: 0.5%">
							<i class="fa fa-pencil"></i> Editar
						</button>
						<button
						id="btn_eliminar"
						onclick="recetas.vista_eliminar()"
						data-toggle="modal"
						data-target="#modal_eliminar"
						data-loading-text="<i class='fa fa-refresh fa-spin'></i>"
						type="button"
						class="btn btn-danger btn-lg"
						style="width: 170px; margin-top: 0.5%">
						<i class="fa fa-trash"></i> Eliminar
					</button>  
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<div class="panel-group" id="accordion_acciones" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
							<div class="panel-heading" id="heading_pasos" role="tab" role="button" style="cursor: pointer" data-toggle="collapse" data-parent="#accordion_pasos" href="#tab_pasos" aria-controls="collapse_pasos" aria-expanded="true">
								<h4 class="panel-title">
									<strong>Ciclo</strong>
								</h4>
							</div>
							<div id="tab_acciones" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_insumos">
								<div class="panel-body">
									<input type="text" id="selectciclo" class="form-control">
									<input type="hidden" id="tipociclo">
									<input type="hidden" id="filascorrectas">
								</div>
							</div>
						</div>
					</div>
					<div class="panel-group" id="accordion_acciones2" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
							<div hrefer class="panel-heading" id="heading_acciones">
								<h4 class="panel-title">
									<strong>Acciones</strong>
								</h4>
							</div>
							<div id="tab_acciones" class="table-responsive" role="tabpanel" aria-labelledby="heading_insumos">
								<div class="panel-body">
									<table id="tabla_acciones" class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th align="center"><strong>Orden</strong></th>
												<th><strong>Nombre</strong></th>
												<th>&nbsp;</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($datos['acciones_procesos_produccion'] as $k => $v) {

												if ($v['activo']=='1') {  ?>
												<tr id="tr_<?php echo $v['id'] ?>">
													<td align="center" class="idacciones"> <?php echo $v['id'] ?></td>
													<td><?php echo $v['nombre'] ?></td>
													<td align="center">
														<input style="cursor: pointer; display: none;" disabled="1" type="checkbox" id="check_a_<?php echo $v['id'] ?>" />
														<button class="btn btn-default btn-sm accionpicker" style="color:#B0B0B0;" idAccion="<?php echo $v['id']; ?>" onclick="recetas.agregar_accion({id:<?php echo $v['id'] ?>,nombre:'<?php echo $v['nombre'] ?>', tiempo_hrs: <?php echo $v['tiempo_hrs'] ?>, div:'div_insumos_agregados',check:$('#check_a_<?php echo $v['id'] ?>').prop('checked')})" style="cursor: pointer">
															<span class="glyphicon glyphicon-ok" ></span>
														</button>
													</td>
												</tr>
												<?php }
											} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 accionesagregadas">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<strong>
									Procesos de Producción.
								</strong>
							</h4>
						</div>
						<div class="panel-body table-responsive">
							<div class="row" id="phead">
								<div class="col-md-12 col-sm-12" id="div_insumos_agregados">

								</div>
							</div>
							<!-- Productos-->
							<div class="row" id="ppasos">
								<div class="col-md-9 col-sm-9">
									<h3><small>Paso:</small></h3>
									<div class="input-group input-group-lg">
										<span class="input-group-addon"><i class="fa fa-font"></i></span>
										<input id="input_paso_produccion" type="text" class="form-control"/>
									</div>
								</div>
							</div>
							<br />
							<div class="row"  id="pboton">
								<div class="col-md-8 col-sm-8">
									<button
									id="btn_agregar_paso_produccion"
									type="button"
									class="btn btn-success btn-lg"
									data-loading-text="<i class='fa fa-refresh fa-spin'></i>"
									onclick="recetas.agregar_paso({
										div:'procesos_agregados',
										btn:'btn_agregar_paso_produccion',
										prd: $('#input_paso_produccion').val()
									})">
									<i class="fa fa-check"></i> Agregar paso
								</button>
							</div>
						</div>
					</div>
				</div>

				<div style="padding: 0px;">
					<div class="panel-group" id="accordion_acciones" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
							<div hrefer class="panel-heading" id="heading_pasos" role="tab" role="button" style="cursor: pointer" data-toggle="collapse" data-parent="#accordion_pasos" href="#tab_pasos" aria-controls="collapse_pasos" aria-expanded="true">
								<h4 class="panel-title">
									<strong>Pasos</strong>
								</h4>
							</div>
							<div id="tab_acciones" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_insumos">
								<div class="panel-body">
									<table id="tabla_pasos" class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th align="center" width="15%"><strong>ID Paso</strong></th>
												<th width="70%"><strong>Paso</strong></th>
												<th  width="15%" align="center"></th>
											</tr>
										</thead>
										<tbody id="bodypasos"></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<button style="margin-bottom: 100px;" id="btn_guardar_receta_prd" type="button" class="btn btn-success btn-lg" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" onclick="recetas.guardar_procesos_produccionciclo()"><i class="fa fa-check"></i> Guardar proceso por ciclo </button>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="modal_editar" role="dialog" aria-labelledby="titulo_editar">
	<div class="modal-dialog" style="width: 90%" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button id="btn_cerrar_editar" type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="titulo_editar">Modificar</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="overflow: scroll;height:60%">
					<div class="col-xs-12" id="div_editar">
						<div class="row">
							<div class="col-xs-12">
								<table id="tabla_ciclos_editar" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><strong>Código</strong></th>
											<th><strong>Nombre</strong></th>

											<th align="center"><strong><i class="fa fa-pencil"></i></strong></th>
										</tr>
									</thead>
									<tbody>

									</tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- <div class="modal fade" id="modalpaso" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="titpaso">Paso seleccionado</h4>
			</div>
			<div class="modal-body" id="bodymodal">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div> -->
<div class="modal fade" id="modal_eliminar" role="dialog" aria-labelledby="titulo_eliminar">
	<div class="modal-dialog" style="width: 90%" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button id="btn_cerrar_eliminar" type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="titulo_eliminar">Eliminar</h4>
			</div>
			<div class="modal-body">
				<div class="row" style="overflow: scroll;height:60%">
					<div class="col-xs-12" id="div_eliminar">
						<div class="row">
							<div class="col-xs-12">
								<table id="tabla_ciclos_eliminar" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><strong>Código</strong></th>
											<th><strong>Nombre</strong></th>
											<th align="center"><strong><i class="fa fa-pencil"></i></strong></th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
