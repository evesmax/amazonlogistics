<html>
	<head>
<!-- ///////////////// ******** ---- 		CSS		------ ************ ////////////////// -->

	<!-- bootstrap min CSS -->
	    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<!-- Iconos font-awesome -->
    	<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<!-- Select con buscador  -->
    	<link rel="stylesheet" href="../../libraries/select2/dist/css/select2.min.css" />
	<!--  dataTables  -->
	    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<!--  Morris  -->
	    <link rel="stylesheet" href="../../libraries/morris.js-0.5.1/morris.css">
	<!-- Datepicker -->
    	<link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
		
<!-- ///////////////// ******** ---- 		FIN CSS		------ ************ ////////////////// -->

<!-- ///////////////// ******** ---- 		JS		------ ************ ////////////////// -->
	 
	<!-- JQuery -->
		<script type="text/javascript" src="../../libraries/jquery.min.js"></script>
	<!-- JQuery UI -->
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
	<!-- bootstrap JavaScript -->
		<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
	<!-- DatePaker -->
		<script src="js/ui.datepicker-es-MX.js"></script>
	<!-- Select con buscador  -->
		<script src="../../libraries/select2/dist/js/select2.min.js"></script>
	<!--  Notify  -->
		<script src="../../libraries/notify.js"></script>
	<!--  dataTables  -->
		<script src="../../libraries/dataTable/js/datatables.min.js"></script>
		<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<!--  Morris  -->
		<script src="../../libraries/morris.js-0.5.1/raphael.min.js"></script>
		<script src="../../libraries/morris.js-0.5.1/morris.min.js"></script>
	<!-- Datepicker -->
	    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>
		
	<!--  Sistema  -->
		<script src="js/comandas/reimprime.js"></script>
		<script type="text/javascript" src="js/comandas/comandas.js"></script>
		
<!-- ///////////////// ******** ---- 		FIN JS		------ ************ ////////////////// -->

		<script>
///////////////// ******** ---- 		select_buscador		------ ************ //////////////////

	//////// Cambia los select por select con buscador.
		// Como parametros puede recibir:
			// Array con los id de los select
		
			function select_buscador ($objeto) {
			// Recorre el arreglo y establece las propiedades del buscador
				$.each( $objeto, function( key, value ) {
					$("#"+value).select2({
						width : "150px"
					});
				});
			}

///////////////// ******** ---- 		FIN select_buscador		------ ************ //////////////////
		</script>
	</head>
	
	<body>
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div 
                	class="panel-heading" 
                	role="tab"
                	data-toggle="collapse" 
                	data-parent="#accordion" 
                	href="#tab_filtros" 
                	aria-controls="collapse_filtros"
            		style="cursor: pointer">
                    <h4 class="panel-title"><strong><i class="fa fa-filter"></i> Filtros</strong></h4>
                </div>
                <div id="tab_filtros" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_tab_filtros">
                    <div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="row"><?php
									date_default_timezone_set('America/Mexico_City');
							
									$f_fin = $f_ini = date('Y-m-d'); ?>
									
									<div class="col-md-5" style="padding-top: 20px">
										<div class="input-group input-group-lg">
									    	<div class="input-group-addon">
									    		<i class="fa fa-calendar"></i> inicio: 
									    	</div>
											<input type="text" id="f_ini" class="form-control" value="<?php echo $f_ini; ?>">
									    </div>
									</div>
									<div class="col-md-2" style="padding-top: 10px">
										<div class="input-group input-group-lg">
									    	Empleado:<br />
											<select id="empleado">
												<option selected value="*">-- Todos --</option><?php
												
												foreach ($empleados as $key => $value) { ?>
													<option value="<?php echo $value['id'] ?>">
														<?php echo $value['usuario'] ?>
													</option> <?php
												} ?>
												
											</select>
										</div>
									</div>
									<div class="col-md-2" style="padding-top: 10px">
										<div class="input-group input-group-lg">
									    	Sucursal:<br />
											<select id="sucursal">
												<option selected value="*">-- Todas --</option><?php
												
												foreach ($sucursales as $key => $value) { ?>
													<option value="<?php echo $value['id'] ?>">
														<?php echo $value['nombre'] ?>
													</option> <?php
												} ?>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-5" style="padding-top: 15px">
										<div class="input-group input-group-lg">
										   	<div class="input-group-addon">
										   		<i class="fa fa-calendar"></i> fin: &nbsp;&nbsp;&nbsp;&nbsp;
										   	</div>
											<input type="text" id="f_fin" class="form-control" value="<?php echo $f_fin; ?>">
									    </div>
									</div>
									<div class="col-md-2" style="padding-top: 10px">
										<div class="input-group input-group-lg">
									    	Actividad:<br />
											<select id="actividades">
												<option selected value="*">-- Todas --</option><?php
												
												foreach ($actividades as $key => $value) { ?>
													<option value="<?php echo $value['accion'] ?>">
														<?php echo $value['accion'] ?>
													</option> <?php
												} ?>
												
											</select>
										</div>
									</div>
									<div class="col-md-1" style="padding-top: 15px">
										<button class="btn btn-default btn-lg" id="btn_bsucar" data-loading-text="<i class='fa fa-refresh fa-spin'></i>" onclick="comandas.listar_actividades({sucursal : $('#sucursal').val(), btn:'btn_bsucar',empleado:$('#empleado').val(),actividad:$('#actividades').val(),f_ini:$('#f_ini').val(), f_fin:$('#f_fin').val(), div:'contenedor'})">
											<i class="fa fa-search"></i> Buscar
										</button>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row">
			<div id="contenedor" class="col-md-12">
				<!-- En esta div se carga el contenido desde el modelo -->
			</div>
		</div>
	</body>
</html>

<!-- Cambiamos los select por select con buscador -->
<script type="text/javascript">
// Creamos un arreglo con los id de los select
	$objeto=[];			
	$objeto[0] = 'empleado';
	$objeto[1] = 'actividades';
	$objeto[2] = 'sucursal';
	
// Mandamos llamar la funcion que crea el buscador
	select_buscador($objeto);

// Ejecuta la funcion que consulta las comandas
	comandas.listar_actividades({
		btn : 'btn_bsucar',
		empleado : $('#empleado').val(),
		actividad : $('#actividades').val(),
		sucursal : $('#sucursal').val(),
		f_ini : $('#f_ini').val(),
		f_fin : $('#f_fin').val(),
		div : 'contenedor'
	});
	
	comandas.convertir_calendario({id: 'f_ini'});
	comandas.convertir_calendario({id: 'f_fin'});
</script>