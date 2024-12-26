<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Alerta Configuracion</title>
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome-4.7.0/css/font-awesome-4.7.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/typeahead/typeahead.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric.js"></script>
    <script src="js/ticket.js"></script>
    <script src="js/caja2.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
    <script src="../../libraries/typeahead/typeahead.js"></script>
    <!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <!-- <script src="../../libraries/dataTable/js/datatables.min.js"></script> -->
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RCA -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>
</head>

<body>
	<div class="container">
		<br><br><br>
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<div class="panel panel-warning padre">
					<div class="panel-heading">
						<h1 class="panel-title">Alerta  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h1>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-3"><i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></i></div>
							<div class="col-sm-5"><strong>Alerta! </strong>Para continuar deberás realizar la Configuracion Avanzada</div>
							<div class="col-sm-4">
								<!--<div class="row">
									<div class="col-sm-12"><button class="btn btn-default btn-block" onclick="irConfig();">Ir a Configuracion</button></div>
								</div> -->
								
								<div class="row">
									<div class="col-sm-12"><button class="btn btn-default btn-block" onclick="javascript:window.location.reload();">Recargar</button></div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3"></div>
		</div>
	</div>
	
</body>
</html>