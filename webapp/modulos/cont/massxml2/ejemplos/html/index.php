<?php
$tipo = null;
switch (empty($_GET['tipo']) ? null : $_GET['tipo']) {
	case 'ciecc':
	default:
		$tipo = 'ciecc';
		require dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'DescargaMasivaCfdi.php';
		$descargaCfdi = new DescargaMasivaCfdi;
		break;
	
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Descarga Masiva de CFDIs</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script>
            function getday(tipo){
                if(tipo == 1){
                    var date = ''+$('#anio').val() + '-' + $('#mes').val() + '-' + '01';
                }else{
                    var date = ''+$('#anio_e').val() + '-' + $('#mes_e').val() + '-' + '01';
                }
                var end = moment(date,'YYY-MM-DD').endOf('month').format('DD');
                var op0 = '<option value="0">Todos</option>';
                var options = '';
                for(var x = 1; x<= end; x ++){
                    options += "<option value='"+x+"'>"+x+"</option>";
                }
                if(tipo == 1){
                    $('#dia').html(op0);
                    $('#dia').append(options);
                }else{
                    $('#dia_e').html(op0);
                    $('#dia_e').append(options);
                }
            }
            function check_all(position,name){
                var form = $('.descarga-form').get(position).elements;
                var value = true;
                if($('#' + name).is(':checked')){
                    value = true;
                }else{
                    value = false;
                }   
                for(var x = 0; x < form.length; x ++){
                    var name = form[x].name;
                    var name_element = name.substring(0,3);
                    if(name_element == 'xml' ){
                        form[x].checked = value;
                    }
                }
            }
        </script>
	</head>
	<body>
		<?php
		if(isset($_COOKIE['inst_lig']))
		{
			?>
				<div style="font-size:20px;text-align:center;background-color:#f5f5f5;border:1px solid #e3e3e3;margin-bottom:10px;" 	class="col-xs-12 col-md-12">Conectado a: <?php echo $_COOKIE['inst_lig'] ?></div>
			<?php
		}
		else
			echo '';
		?>

	    <div id="main">
			<div class="container-fluid">
			<div class="row">
				<div class="col-sm-4"><a class="btn btn-default" href="javascript:backToDigitalWHouse();" role="button"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a></div>
				<div class="col-sm-8"><h3 style='color:#005A8F;'>Descarga Masiva de XML's desde el SAT</h3></div>
			</div>
<?php
	require 'form-login-ciec-captcha.inc.php';	
?>
				<div class="tablas-resultados">
					<div class="overlay"></div>
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#recibidos" aria-controls="recibidos" role="tab" data-toggle="tab">Recibidos</a></li>
						<li role="presentation"><a href="#emitidos" aria-controls="emitidos" role="tab" data-toggle="tab">Emitidos</a></li>
					</ul>
					<div class="tab-content">
					    <div role="tabpanel" class="tab-pane active" id="recibidos">
					    	<?php require 'form-recibidos.inc.php'; ?>
							<form method="POST" class="descarga-form">
								<input type="hidden" name="accion" value="descargar-recibidos" />
								<input type="hidden" name="sesion" class="sesion-ipt" />
								<div style="overflow:auto">
									<table class="table table-hover table-condensed" id="tabla-recibidos">
										<thead>
											<tr>
												<th class="text-center">XML</th>
												<th class="text-center">Acuse</th>
												<th>Efecto</th>
												<th>Razón Social del Receptor</th>
												<th>RFC Receptor</th>
												<th>Estado</th>
												<th>Folio Fiscal</th>
												<th>Emisión</th>
												<th>Total</th>
												<th>Certificación</th>
												<th>Cancelación</th>
												<th>PAC</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
								<div class="text-right">
									<!--<a href="#" class="btn btn-primary excel-export" download="cfdi_recibidos.xls">Exportar a Excel</a>-->
									<button type="submit" class="btn btn-success">Descargar seleccionados</button>
								</div>
							</form>
					    </div>
					    <div role="tabpanel" class="tab-pane" id="emitidos">
							<?php require 'form-emitidos2.inc.php'; ?>
							<form method="POST" class="descarga-form">
								<input type="hidden" name="accion" value="descargar-emitidos" />
								<input type="hidden" name="sesion" class="sesion-ipt" />
								<div style="overflow:auto">
									<table class="table table-hover table-condensed" id="tabla-emitidos">
										<thead>
											<tr>
												<th class="text-center">XML</th>
												<th class="text-center">Acuse</th>
												<th>Efecto</th>
												<th>Razón Social del Receptor</th>
												<th>RFC Receptor</th>
												<th>Estado</th>
												<th>Folio Fiscal</th>
												<th>Emisión</th>
												<th>Total</th>
												<th>Certificación</th>
												<th>PAC</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
								<div class="text-right">
									<!--<a href="#" class="btn btn-primary excel-export" download="cfdi_emitidos.xls">Exportar a Excel</a>-->
									<button type="submit" class="btn btn-success">Descargar seleccionados</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/code.js"></script>
	</body>
</html>
<script language="javascript">
function reload()
{
	location.reload();
}
function backToDigitalWHouse()
{
    window.history.back();
}
</script>


