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
   <script>
   $(document).ready(function() {
   	$('#fpGasto').select2({ width: '100%' });
	$('#cuentaGasto').select2({ width: '100%' });
	$('#segmentoGasto').select2({ width: '100%' });
	$('#sucursalGasto').select2({ width: '100%' });
	$('#categoriaGasto').select2({ width: '100%' });
	$('#fechaGasto').datepicker({
	format: "yyyy-mm-dd",
	language: "es"
	});

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
		$('#tableGridGastos').DataTable({
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
		listaCategorias();
   });

   </script>
   <style>
   	.modal-body {
    max-height: 800px;
	}
	#tableGridCategorias{
	    height: 200px;
	    display: inline-block;
	    width: 100%;
	    overflow: auto;
	}
   </style>
<body>
<div class="container well">
	<div class="row">
		<div class="col-sm-8">
			<h3>
				Presupuestos
				<!--<button class="btn btn-primary" onclick="newOrder();"><i class="fa fa-plus" aria-hidden="true"></i> Nueva Orden</button> -->
			</h3>
		</div>

	</div>
<!-- 	<div class="row">
		<div class="col-sm-12">
			<label>Total: <?php echo $clientes['total']; ?></label>
		</div>
	</div> -->
	<!--<pre>
		<?php //print_r($solicitudes) ?>
	</pre>-->
	<div class="row">
		<div class="col-sm-12" style="overflow:auto;">
					 <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGrid">
					<thead>
					  <tr>
						<th>#Solicitud</th>
						<th>Numero de viaje</th>
						<th>Cliente</th>
					<!--	<th>Tipo de Vuelo</th>
						<th>Tipo de Viaje</th> -->
						<th>Origen</th>
						<th>Destino</th>
						<th>Fecha Ida</th>
						<th>Fecha Regreso</th>
						<th>Fecha</th>
						<!--<th>PDF</th>-->
						<th>Estatus</th>
						<th>Acciones</th>
					  </tr>
					</thead>
					<tbody>
						<?php

							$redo = '';
							$tipo = '';
							$xml = '';
							foreach ($solicitudes['solici'] as $key => $value) {
								if($value['estatus']==2){
									$pdf = '<button class="btn btn-default" onclick="verPdf('.$value['id'].')"><i class="fa fa-file-pdf-o"></i></button>';
									$pdf.='<a onclick="ventaFac('.$value['id'].');" class="btn btn-default"><i class="fa fa-shopping-basket"></i></a>';
								}else{
									$pdf='';
								}
								if($value['uuid']!=''){

									$xml ='<a class="btn btn-default" alt="Visualizar PDF" title="Visualizar PDF" onclick="verPdfF(\''.$value['uuid'].'\');"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
								}else{
									$xml ='';
								}
								echo '<tr>';
								echo '<td>'.$value['id'].'</td>';
								echo '<td>'.$value['num_viaje'].'</td>';
								echo '<td>'.$value['nombre'].'</td>';
								/*if($value['tipoVuelo']==1){
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
								echo '<td>'.$value['origenN'].'</td>';
								echo '<td>'.$value['destinoN'].'</td>';
								echo '<td>'.$value['fechaIda'].'</td>';
								echo '<td>'.$value['fechaRegreso'].'</td>';
								echo '<td>'.$value['fecha'].'</td>';
							//	echo '<td>PDF</td>';
								if(!intval($value['tiene_gastos']))
									$aprobado = '<span class="label label-default">Nueva</span>';
								else{
									switch(intval($value['aprobado'])){
										case 0: if($_SESSION["accelog_idperfil"] == '(2)')
													$aprobado = '<div id="apr_'.$value['id'].'"><a href="javascript:abre_aprobacion('.$value['id'].')"><span class="label label-warning">Aprobación Pendiente</span></a></div>';
												else
													$aprobado = '<div id="apr_'.$value['id'].'"><span class="label label-warning">Aprobación Pendiente</span></div>';
												break;
										case 1: $aprobado = '<span class="label label-success">Aprobado</span>';
												break;
										case 2: $aprobado = '<span class="label label-danger">No Aprobado</span>';
												break;
									}
								}

								echo "<td>$aprobado</td>";
								echo '<td><button class="btn btn-success" onclick="newGasto('.$value['id'].');">$</button>';
								echo '<button class="btn btn-info" onclick="cotizaModal('.$value['id'].');"><i class="fa fa-clipboard"></i></button>'.$pdf.$xml;
								//echo '<button class="btn btn-warning" onclick="verPdf('.$value['id'].')"><i class="fa fa-file-pdf-o"></i></button>';
								echo '</td>';
								echo '</tr>';
							}						?>
					</tbody>
				</table>
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



  <div class="modal fade" id="modalGastos">
  	<div class="modal-dialog"  style="width:100%;">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  				<h4 class="modal-title">Gastos</h4>
  				<input type="hidden" id="idSolici">
  			</div>
  			<div class="modal-body">
  				<div class="row">
  					<div class="col-xs-2">
  						<button class="btn btn-success" onclick="$('#modal-categorias').modal();">+ Seleccion de Categorias</button>
  					</div>
						<div class="col-xs-3 col-xs-offset-8">
								<div class="form-group">
									<label>Total de gastos (MXN)</label>
									<div class="input-group">
										<div class="input-group-addon">$</div>
										<input type="text" class="form-control" id="totalGastos" placeholder="Suma">
										<div class="input-group-addon">.00</div>
									</div>
								</div>
								<span class="label label-info" id="sumandoGastos"></span>
							</div>

  				</div><br>
  				<div class="row">
  					<div class="col-xs-12">
  						 <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGridGastos">
					<thead>
					  <tr>
						<th># de Viaje</th>
						<th>Fecha Gasto</th>
						<th>Categoria</th>
						<th>Importe</th>
						<th>Moneda</th>
						<th>T.C.</th>
						<th>Cuenta</th>
						<th>Forma de pago</th>
						<th>Referencia</th>
					  </tr>
					</thead>
					<tbody id=''>
					</tbody>
				</table>
  					</div>
  				</div>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  				<button type="button" class="btn btn-primary" onclick='guardar()'>Guardar</button>
  			</div>
  		</div>
  	</div>
  </div>


  <div class="modal fade" id="modal-categorias">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  				<h4 class="modal-title">Categoria</h4>
  			</div>
  			<div class="modal-body">
				<div class="col-sx-12">
	  				<table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGridCategorias">
	  					<thead>
					  		<tr>
								<th></th>
								<th>Categoria</th>
								<th>Moneda</th>
								<th>Importe</th>
							</tr>
						</thead>
						<tbody id='tableGridCategorias_body'>
						</tbody>
	  				</table>
				</div>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  				<button type="button" class="btn btn-primary" onclick="generar();">Generar</button>
  			</div>
  		</div>
  	</div>
  </div>

  <div class="modal fade" id="modalCotiza">
  	<div class="modal-dialog modal-lg">
  		<div class="modal-content">
  			<div class="modal-header">
  				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  				<h4 class="modal-title">Cotizacion</h4>
  				<input type="hidden" id="idSoliCoti">
  			</div>
  			<div class="modal-body">
  				<div class="row">
					<div class="col-sm-12">
						<div class="panel panel-default">
						  <div class="panel-heading">
							<div class="row">
								<div class="col-sm-10">
									<select id="prod" class="form-control">
										<option value="0">-Selecciona-</option>
										<?php

											foreach ($productos['productos'] as $key => $value) {
												echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';
											}

										?>
									</select>
								</div>
								<div class="col-sm-2">
									<button class="btn btn-success" onclick="agregaProd();">Agregar</button>
								</div>
							</div>
						  </div>
						  <div class="panel-body">
						  	<div class="row">
						  		<div class="col-sm-12">
									<div class="container" style="width: 100%; height: 400px; overflow: auto; background-color:#fbfbf2;">
						               <!-- <i class="fa fa-refresh fa-spin fa-5x"></i> -->
						             <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="proTable">
						                    <thead>
						                      <tr>
						                      	<th> </th>
						                        <th>Codigo</th>
						                        <th>Producto</th>
						                        <th>Cantidad</th>
						                        <th>$Unitario</th>
						                        <th>Importe</th>
						                      </tr>
						                    </thead>
						                    <div id="xxxxx">
						                    <tbody id="tableBody">
						                    </tbody>
						                    </div>
						                </table>
						            </div> <!--fin del contenedor overflow-->
						  		</div>
						  	</div>
							<div class="row">
						        <div class="col-sm-6"></div>
						        <div class="col-sm-3" id="impuestosDiv"></div>
						        <div class="col-sm-3">
						            <div id="subtotalDiv" class="totalesDiv"></div>
						            <div id="totalDiv" class="totalesDiv"></div>
						            <!-- inputs donde se guarda el total y subtotal -->
						            <input type="hidden" id="inputSubTotal">
						            <input type="hidden" id="inputTotal">
						        </div>
					        </div>
					        <div class="row">
					        	<div class="col-sm-12">
					        		<label>Observaciones</label>
					        		<textarea id="obs" rows="3" class="form-control"></textarea>
					        	</div>
					        </div>
						  </div>
						</div>
					</div>
  				</div>
  			</div>
  			<div class="modal-footer">
  				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
  				<button type="button" class="btn btn-primary" onclick="gurdarCoti(2);">Guardar</button>
  			</div>
  		</div>
  	</div>
  </div>

   <div class="modal fade" id="modalMensajes" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
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

  <div class="modal fade" id="modalAutorizar" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Autorización:<input type='hidden' id='idsol-aut'></h4>
        </div>
        <div class="modal-body">
        	<div class="row">
	          <div class='col-sm-6'><button class='btn btn-success' onclick='autorizar(1)'>Autorizar</button></div>
	          <div class='col-sm-6'><button class='btn btn-danger' onclick='autorizar(0)'>No Autorizar</button></div>
	         </div>
        </div>
        <div class="modal-footer">
  			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
  		</div>
      </div>
    </div>
  </div>


</body>
</html>
