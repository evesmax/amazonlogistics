<html>
	<head>
<!-- **	/////////////////////////- -				 CSS 				--///////////////////// **-->
		
	<!-- Iconos font-awesome -->
		<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<!-- bootstrap min CSS -->
	    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<!-- bootstrap-select -->
		<link rel="stylesheet" href="../../libraries/bootstrap-select-1.9.3/dist/css/bootstrap-select.min.css">
	<!-- DataTables  -->
	    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
		
<!-- **	//////////////////////////- -				FIN CSS 				--///////////////////// **-->
	
<!-- **	//////////////////////////- -				JS 				--///////////////////// **-->

	<!-- JQuery -->
		<script src="../../libraries/jquery.min.js"></script>
	<!-- bootstrap -->
		<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- bootstrap-select  -->
		<script src="../../libraries/bootstrap-select-1.9.3/dist/js/bootstrap-select.min.js"></script>
	<!-- Notify  -->
		<script src="../../libraries/notify.js"></script>
	<!-- DataTables  -->
		<script src="../../libraries/dataTable/js/datatables.min.js"></script>
		<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	    
	<!-- Sistema -->
		<script src="js/configuracion/configuracion.js"></script>
		<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
		<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<!-- **	//////////////////////////- -				FIN JS 				--///////////////////// **-->

		<title>Promociones</title>
	</head>
	<body>		
		<div class="row">
			<div class="col-xs-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<div class="row">
							<div class="col-xs-12" style="margin-top: 10px">
								<button 
									id="btn_nueva" 
									onclick="configuracion.vista_nueva({div:'div_promociones', btn:'btn_nueva', panel:'success'})" 
									data-loading-text="<i class='fa fa-refresh fa-spin'></i>" 
									type="button" 
									class="btn btn-success btn-lg" 
									style="width: 170px; margin-top: 0.5%;">
									<i class="fa fa-plus"></i> Agregar
								</button>
								<button 
									id="btn_editar" 
									onclick="configuracion.vista_editar_promocion({div:'div_editar',btn:'btn_editar',panel:'primary'})" 
									data-toggle="modal" 
									data-target="#modal_editar" 
									data-loading-text="<i class='fa fa-refresh fa-spin'></i>" 
									type="button" class="btn btn-primary btn-lg"  
									style="width: 170px; margin-top: 0.5%">
									<i class="fa fa-pencil"></i> Modificar
								</button>
								<button 
									id="btn_eliminar"
									onclick="configuracion.vista_eliminar_promocion({div:'div_eliminar', btn:'btn_eliminar', panel:'danger'})" 
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
						<div id="div_promociones" class="row" style="overflow: scroll;height:75%">
							<!-- En esta div se carga el contenido de las promociones -->
						</div>
				  	</div>
				</div>
			</div>
		</div>
	<!-- Modal editar-->
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
								<!-- En esta div se cargan las promociones e insumos preparados -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!-- FIN Modal editar -->
	
	<!-- Modal eliminar-->
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
								<!-- En esta div se cargan las promociones -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!-- FIN Modal editar -->
	</body>
</html>
<script>
// Carga la vista para editar las promociones
	// $('#btn_nueva').click();
	$('#btn_editar').click();
</script>