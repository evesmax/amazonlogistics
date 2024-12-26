<?php

//echo json_encode($datosCliente);
function randpass() {
	$alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}
 ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Formulario de Cliente</title>
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<script src="../../libraries/jquery.min.js"></script>
	<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="../../libraries/numeric.js"></script>
	<script src="js/cliente.js"></script>
	<script src="../../libraries/numeric.js"></script>
<!--Select 2 -->
	<script src="../../libraries/select2/dist/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
<!-- datetimepicker -->
<link rel="stylesheet" href="../../libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

<script src="../../libraries/bootstrap-datetimepicker/js/moment.js"></script>

<script src="../../libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#usoCfdi').select2({ width: '100%' });
		  $('#numeros').numeric();
		  $('#tipoClas').select2({'width':'100%'});
		  $('#tipoDeCredito').select2({'width':'100%'});
		  $('#moneda').select2({'width':'100%'});
		  $('#banco').select2({'width':'100%'});
		  $('#vendedor').select2({'width':'100%'});
		  $('#cuentaCont').select2({'width':'100%'});
		  $(".numeros").numeric();
		});
		function iraprepolizas(){
            window.parent.agregatab('../../modulos/cont/index.php?c=almacen&f=polizas','TPL Generacion de Polizas','',2433)
            //window.location='../../modulos/cont/index.php?c=AccountsTree';
        }
        function iracuenta(){
            window.parent.agregatab('../../modulos/cont/index.php?c=arbol&f=index','Cuentas','',145)
            //window.location='../../modulos/cont/index.php?c=AccountsTree';
        }
        function obtener_cuentas(){
            console.log('in');
            $('.btn_cuenta_contable').fadeOut();
            $.post("ajax.php?c=cliente&f=obtener_cuenta",
                function(datos) {
                    var op0 = '<option value="0">-Selecciona Cuenta-</option>';
                    $('#cuentaCont').html(op0);

                    var options = '';
                    $.each(datos, function(index, arr){
                        options += "<option value='"+arr.accound_id+"'>"+arr.description+"</option>";
                    });

                    $('#cuentaCont').append(options);
                }, "JSON");
            $('.btn_cuenta_contable').fadeIn();
        }
        function obtener_cuenta_gasto(){
            $('.btn_cuenta_gasto').fadeOut();

            $.post("ajax.php?c=cliente&f=obtener_cuenta_gasto",
                function(datos) {
                    var op0 = '<option value="0">Selecciona una cuenta de gasto</option>';
                    $('#cuentas_gastos').html(op0);

                    var options = '';
                    $.each(datos, function(index, arr){
                        options += "<option value='"+arr.id+"'>"+arr.nombre+"</option>";
                    });

                    $('#cuentas_gastos').append(options);
                    $('.btn_cuenta_gasto').fadeIn();
                }, "JSON");
        }
        function obtener_prepol_prov(){
            $('.btn_prepol_prov').fadeOut();

            $.post("ajax.php?c=cliente&f=obtener_prepolizas_provision",
                function(datos) {
                    var op0 = '<option value="0">Selecciona una prepoliza</option>';
                    $('#prepolizas_provision').html(op0);

                    var options = '';
                    $.each(datos, function(index, arr){
                        options += "<option value='"+arr.id+"'>"+arr.nombre+"</option>";
                    });

                    $('#prepolizas_provision').append(options);
                    $('.btn_prepol_prov').fadeIn();
                }, "JSON");
        }
        function obtener_prepol_pago(){
            $('.btn_prepol_pago').fadeOut();

            $.post("ajax.php?c=cliente&f=obtener_prepolizas_pago",
                function(datos) {
                    var op0 = '<option value="0">Selecciona una prepoliza</option>';
                    $('#prepolizas_pago').html(op0);

                    var options = '';
                    $.each(datos, function(index, arr){
                        options += "<option value='"+arr.id+"'>"+arr.nombre+"</option>";
                    });

                    $('#prepolizas_pago').append(options);
                    $('.btn_prepol_pago').fadeIn();
                }, "JSON");
        }
		function enviarCorreoPortal(){
			$('#btnenviarCorreo').prop('disabled',true); 
			$('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-click') );

			correoportal=$('#correoportal').val();
			userportal=$('#userportal').val();
			passportal=$('#passportal').val();
			nombre=$('#nombre').val();

			if(correoportal=='' || userportal=='' || passportal==''){
				alert('Los campos no pueden estar vacios.');
				$('#btnenviarCorreo').prop('disabled',false); 
				$('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );
				return false
			}

			$.ajax({
			url:"ajax.php?c=cliente&f=correoPortal",
			type: 'POST',
			data:{correoportal:correoportal,userportal:userportal,passportal:passportal,nombre:nombre},
			success: function(data){
				if(data==1){
					alert('Correo enviado al proveedor');
				}else{
					alert('Error en el proceso de envio');
				}
				$('#btnenviarCorreo').prop('disabled',false); 
				$('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );

			}
		  });
		}
	</script>
  <style>
	.select2-selection{
	  height: 34px !important;
	}
  </style>

</head>
<body>
<div class="container-fluid well">
	  <div class="row">
		<div class="col-sm-1">
			<button class="btn btn-default" onclick="back();"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar</button>
		</div>
		<div class="col-sm-1">
		  <button type="button" class="btn btn-primary" onclick="guardaCliente();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
		</div>
		<div class="col-sm-1">
		<?php
		  if($idCliente!=''){
			echo '<span class="label label-warning">Editando</span>';
		  }else{
			echo '<span class="label label-success">Nuevo</span>';
		  }

		?>
		</div>
	</div>
  <div class="panel panel-default">
  <div class="panel-heading"><h5>Cliente<?php
						if(isset($datosCliente)){echo ' ('.$datosCliente['basicos'][0]['nombre'].')';}?></h5></div>
  <div class="panel-body">
	<div style="heigth:400px;">
	  <div id="tabsCliente">
		<ul class="nav nav-tabs">
		  <li class="active"><a data-toggle="tab" href="#basicos">Datos Básicos</a></li>
		  <li><a data-toggle="tab" href="#datosFacturacion">Datos de Facturación</a></li>
		  <li><a data-toggle="tab" href="#direccionContactos">Directorio de contactos</a></li>
		  <li><a data-toggle="tab" href="#datosEnvios">Datos de Envío</a></li>
		  <li><a data-toggle="tab" href="#datosCredito">Crédito</a></li>
		  <li><a data-toggle="tab" href="#datosComisiones">Comisiones</a></li>
		  <li><a data-toggle="tab" href="#datosContables">Datos Contables</a></li>
		  <li><a data-toggle="tab" href="#accesoPortal">Acceso al portal</a></li>
		</ul>
	  </div>
	  <div class="tab-content" style="height:450px;">
		<div id="basicos" class="tab-pane fade in active">
		  <div class="row">
			<div class="col-sm-2">
			  <label class="control-label" for="email">ID</label>
			  <input id="idCliente" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['id'];}?>" readonly placeholder="(Autonumérico)">
			</div>
			<div class="col-sm-3">
				<label class="control-label"><span style="color:red;">* </span>Código</label>
				<input id="codigo" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['codigo'];}?>">
			</div>
			<div class="col-sm-3">
			  <label>Clasificador del Cliente</label>
			  <select id="tipoClas" class="form-control">
				<?php
				  foreach ($clasificadores as $keyClas => $valueClas) {
							   if(isset($datosCliente)){
								  if($datosCliente['basicos'][0]['id_clasificacion']==$valueClas['id']){
									echo '<option value="'.$valueClas['id'].'" selected>'.$valueClas['nombre'].'/'.$valueClas['clave'].'</option>';
								  }
								}
					echo '<option value="'.$valueClas['id'].'">'.$valueClas['nombre'].'/'.$valueClas['clave'].'</option>';
				  }
				?>
			  </select>
			</div>

		  </div>

		  <div class="row">
			<div class="col-sm-6">
			  <label class="control-label"><span style="color:red;">*</span> Nombre</label>
			  <input id="nombre" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombre'];}?>">
			</div>
			<div class="col-sm-6">
				<label class="control-label">Nombre Comercial</label>
				<input id="tienda" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombretienda'];}?>">
			</div>
		  </div>

		  <div class="row">
			<div class="col-sm-6">
			  <label class="control-label">Dirección</label>
			  <input id="direccion" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['direccion'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Exterior</label>
			  <input id="numext" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['num_ext'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Interior</label>
			  <input id="numint" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['num_int'];}?>">
			</div>
		  </div>

		  <div class="row">
			<div class="col-sm-2">
			  <label class="control-label">Colonia</label>
			  <input id="colonia" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['colonia'];}?>">
			</div>
			<div class="col-sm-2">
				<label class="control-label">Código Postal</label>
				<input id="cp" class="form-control numeros" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['cp'];}?>">
			</div>
			<div class="col-sm-2">
				<div class="row">
					<div class="col-sm-8">
						<label class="control-label"> País</label>
						<select id="selectPais" class="form-control" >
							<option value="<?php if(isset($datosCliente)){echo $datosCliente['basicos'][0]['idPais'];} ?>">
								<?php if(isset($datosCliente)){echo $datosCliente['basicos'][0]['descPais'];} ?>
							</option>
						</select>
					</div>
					<div class="col-sm-1">
						<label class="control-label"></label>
						<button type="button" data-toggle="modal" data-target="#nuevoPais" class="btn btn-success">
							<i class="fa fa-plus" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
<!-- Modal -->
<div class="modal fade" id="nuevoPais"  role="dialog" aria-labelledby="nuevoPais" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" >Agregar nuevo País</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		<input type="text" id="inputNuevoPais" class="form-control" placeholder="Nombre de país">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		<button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoPais">Aceptar</button>
	  </div>
	</div>
  </div>
</div>
			<div class="col-sm-2">
				<div class="row">
					<div class="col-sm-8">
						<label class="control-label">Estado</label>
						<select id="selectEstado" class="form-control" >
							<option value="<?php if(isset($datosCliente)){echo $datosCliente['basicos'][0]['idEstado'];} ?>">
								<?php if(isset($datosCliente)){echo $datosCliente['basicos'][0]['descEstado'];} ?>
							</option>
						</select>
					</div>
					<div class="col-sm-1">
						<label class="control-label"></label>
						<button type="button" data-toggle="modal" data-target="#nuevoEstado" class="btn btn-success">
							<i class="fa fa-plus" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
<!-- Modal -->
<div class="modal fade" id="nuevoEstado"  role="dialog" aria-labelledby="nuevoEstado" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" >Agregar nuevo Estado</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		<select id="selectPais2" class="form-control" ></select>
		<input type="text" id="inputNuevoEstado" class="form-control" placeholder="Nombre de estado">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		<button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoEstado">Aceptar</button>
	  </div>
	</div>
  </div>
</div>
			<div class="col-sm-2">
				<div class="row">
					<div class="col-sm-8">
						<label class="control-label">Municipio</label>
						<select id="selectMunicipio" class="form-control" >
							<option value="<?php if(isset($datosCliente)){echo $datosCliente['basicos'][0]['idMunicipio'];} ?>">
								<?php if(isset($datosCliente)){echo $datosCliente['basicos'][0]['descMunicipio'];} ?>
							</option>
						</select>
					</div>
					<div class="col-sm-1">
						<label class="control-label"></label>
						<button type="button" data-toggle="modal" data-target="#nuevoMunicipio" class="btn btn-success">
							<i class="fa fa-plus" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			</div>
<!-- Modal -->
<div class="modal fade" id="nuevoMunicipio"  role="dialog" aria-labelledby="nuevoMunicipio" aria-hidden="true">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" >Agregar nuevo Municipio</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
	  <select id="selectPais3" class="form-control" ></select>
		<select id="selectEstado3" class="form-control" ></select>
		<input type="text" id="inputNuevoMunicipio" class="form-control" placeholder="Nombre de municipio">
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		<button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoMunicipio">Aceptar</button>
	  </div>
	</div>
  </div>
</div>
			<div class="col-sm-2">
			  <label class="control-label">Ciudad</label>
			  <input id="ciudad" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['ciudad'];}?>">
			</div>
		  </div>

		  <div class="row">
			<div class="col-sm-3">
			  <label class="control-label">Email</label>
			  <input id="email" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['email'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Celular</label>
			  <input id="celular" class="form-control numeros" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['celular'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Teléfono 1</label>
			  <input id="tel1" class="form-control numeros" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['telefono1'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Teléfono 2</label>
			  <input id="tel2" class="form-control numeros" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['telefono2'];}?>">
			</div>
		  </div>

		  <div class="row">
				<div class="col-sm-3" style="height:130px;">
					<div class="form-group">
						<label class="control-label">Fecha de cumpleaños
						</label>
						<div class='input-group date' id='datetimepicker10'>
							<input id='cumpleanos' type='text' class="form-control" placeholder="dd / mm" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['cumpleanos'];}
					?>"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar">
								</span>
							</span>
						</div>
						<script type="text/javascript">
							$(function () {
								$('#datetimepicker10').datetimepicker({
									viewMode: 'months',
									format: 'DD/MM'
								});
							});
						</script>
					</div>
				</div>
				
		  </div>

		<!--  <div class="row">
			<div class="col-sm-3">
			  <label class="control-label">RFC</label>
			  <input id="rfc" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">CURP</label>
			  <input id="curp" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['curp'];}?>">
			</div>
		  </div> -->

		<!--  <div class="row">
			<div class="col-sm-3">
			  <label class="control-label">Dias de Credito</label>
			  <input id="diasCredito" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['dias_credito'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Limite de Credito</label>
			  <input id="limiteCredito" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['limite_credito'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Moneda</label>
			  <!--<input id="moneda" class="form-control" type="text" value=""> -->
			 <!-- <select id="moneda" class="form-control">
				<?php

				  foreach ($moneda as $keyMon => $valueMon) {
					echo '<option value="'.$valueMon['coin_id'].'">'.$valueMon['description'].'/'.$valueMon['codigo'].'</option>';
				  }

				?>
			  </select>
			</div>
			<div class="col-sm-3"></div>
		  </div> -->



		  <div class="row"><br>
			<div class="col-sm-10"></div>
			<!--<div class="col-sm-1"><button type="button" class="btn btn-primary" onclick="guardaCliente();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button></div> -->
		  </div>
		</div><!-- Fin del div basicos -->
		<div id="datosFacturacion" class="tab-pane fade">
		  <div class="row"><br>
			<div class="col-sm-8">
			  <blockquote>
				<p>Si los datos de Facturación son los mismos que los básicos, transfiérelos de los básicos a facturación con el botón de Transferir.</p>
			  </blockquote>
			</div>
			<div class="col-sm-3">
			  <button class="btn btn-info btn-block" onclick="trans();"><i class="fa fa-exchange" aria-hidden="true"></i> Transferir</button>
			</div>

		  </div>
				  <div class="row">
					<div class="col-sm-6">
					  <input type="hidden" id="idComunFact" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['id'];}?>">

					  <label class="control-label"><span style="color:red;">*</span> Razón Social</label>
					  <input type="text" id="razonSocial" class="form-control datFc" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['razon_social'];}?>">
					</div>

				  </div>
				  <div class="row">
					  <div class="col-sm-3">
						<label class="control-label"><span style="color:red;">*</span> RFC</label>
						<input id="hideprod" type="hidden" value="<?php echo $configF; ?>">
						<input id="rfc" class="form-control datFc" type="text" value="<?php
								  if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['rfc'];}?>">
					  </div>
					  <div class="col-sm-3">
						<label class="control-label">CURP</label>
						<input id="curp" class="form-control datFc" type="text" value="<?php
								  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['curp'];}?>">
					  </div>
					  <div class="col-sm-3">
						<label class="control-label"> Email</label>
						<input type="text" id="emailFacturacion" class="form-control" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['correo'];}?>">
					  </div>
				  </div>
				  <div class="row">
					<div class="col-sm-6">
					  <label class="control-label">Dirección de Facturación</label>
					  <input id="direccionFact" class="form-control datFc" type="text" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['domicilio'];}?>">
					</div>
					<div class="col-sm-3">
					  <label class="control-label">Exterior e Interior F.</label>
					  <input id="numextFact" class="form-control datFc" type="text" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['num_ext'];}?>">
					</div>
				   <!-- <div class="col-sm-3">
					  <label class="control-label">Interior F.</label>
					  <input id="numintFact" class="form-control" type="text" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['num_int'];}?>">
					</div> -->
				  </div>
				  <div class="row">
					<div class="col-sm-2">
					  <label class="control-label">Colonia</label>
					  <input id="coloniaFact" class="form-control datFc" type="text" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['colonia'];}?>">
					</div>
					<div class="col-sm-2">
						<label class="control-label"><span style="color:red;">*</span>Código Postal</label>
						<input id="cpFact" class="form-control numeros datFc" type="text" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['cp'];}?>">
					</div>
					<div class="col-sm-2">
						<label class="control-label"> País</label>
						<select id="paisFact2" class="form-control" onchange="estadosFc();">
						  <option value="0">-Selecciona un pais</option>
						  <?php
							foreach ($paises as $key => $value) {
							  if(isset($datosCliente)){
								if($datosClienteFact['fact'][0]['idPais']==$value['idpais']){
								  echo '<option value="'.$value['idpais'].'" selected>'.$value['pais'].'</option>';
								}
							  }
							  echo '<option value="'.$value['idpais'].'">'.$value['pais'].'</option>';
							}
						  ?>
						</select>
					</div>
					<div class="col-sm-3">
						<label class="control-label"> Estado</label>
						<select id="estadoFact" class="form-control datFc" onchange="municipiosFc();">
						  <option value="0">-Selecciona un estado</option>
							<?php
								foreach ($estados as $key => $value) {
									if(isset($datosClienteFact)){
										if($datosClienteFact['fact'][0]['estado']==$value['idestado']){
											echo '<option value="'.$value['idestado'].'" selected>'.$value['estado'].'</option>';
										}
									}
									echo '<option value="'.$value['idestado'].'">'.$value['estado'].'</option>';
								}
							?>
						</select>
					</div>
					<div class="col-sm-3">
						<label class="control-label"> Municipio</label>
						<select  id="municipiosFact" class="form-control datFc">
						  <option value='0'>-Selecciona un municipio--</option>
						  <?php
							foreach ($municipiosFc as $keyMu => $valueMu) {
								if(isset($datosClienteFact)){
								  if($datosClienteFact['fact'][0]['idMunicipio']==$valueMu['idmunicipio']){
									echo '<option value="'.$valueMu['idmunicipio'].'" selected>'.$valueMu['municipio'].'</option>';
								  }
								}
								echo '<option value="'.$valueMu['idmunicipio'].'">'.$valueMu['municipio'].'</option>';
							}
						  ?>
						</select>
					</div>
				  </div>
				  <div class="row">
					<div class="col-sm-3">
					  <label> Ciudad</label>
					  <input id="ciudadFact" type="text" class="form-control datFc"value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['ciudad'];}?>">
					</div>
					<div class="col-sm-3" style="display: none;">
					   <label><span style="color:red;">*</span> País</label>
					  <input id="paisFact" type="text" class="form-control datFc" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['pais'];}?>">
					</div>
					<div class="col-sm-3">
					   <label><span style="color:red;">*</span>Régimen Fiscal</label>
					   <select id="regimenFact" class="form-control datFc">
					   	<?php
					   	foreach ($regimenes as $keyMu => $valueMu) {
					   		if(isset($datosClienteFact)){
					   			if($datosClienteFact['fact'][0]['regimen_fiscal']==$valueMu['c_regimenfiscal']){
					   				echo '<option value="'.$valueMu['c_regimenfiscal'].'" selected="true">'.$valueMu['c_regimenfiscal'].' '.$valueMu['descripcion'].'</option>';
					   			}else{
					   				echo '<option value="'.$valueMu['c_regimenfiscal'].'">'.$valueMu['c_regimenfiscal'].' '.$valueMu['descripcion'].'</option>';
					   			}
					   		}else{
					   			echo '<option value="'.$valueMu['c_regimenfiscal'].'">'.$valueMu['c_regimenfiscal'].' '.$valueMu['descripcion'].'</option>';
					   		}
					   		
					   	} 
					   ?>
					   </select>
					  <!--<input id="regimenFact" type="text" class="form-control datFc" value="<?php
								if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['regimen_fiscal'];}?>">-->
					</div>
					<div class="col-sm-3">
	                    <label>Uso CFDI:</label>
	                    <select id="usoCfdi" class="form-control">
	                        <?php 
	                            foreach ($usoCFDI['usos'] as $key => $value) {
	                                echo '<option value="'.$value['id'].'"'.( ($value['id'] == $datosCliente['basicos'][0]['usoCFDI'] ) ? "selected" : "" ).'>('.$value['c_usocfdi'].') '.$value['descripcion'].'</option>';
	                            } 

	                        ?>
	                    </select>
	                </div>
				  </div>
		</div><!-- fin del Tab de facturacion -->

		<!-- D I R E C T O R I O   D E   C O N T A C T O S  -->
							<div id="direccionContactos" class="tab-pane fade">
								<div class="row"> <br>
									<div class="col-sm-6"> <blockquote> Si los datos del contacto son los mismos de los Datos básicos; transfiérelos con el botón Transferir </blockquote> </div>
									<div class="col-sm-3" "> <br>
										<button class="btn btn-info btn-block" onclick="transferirDat();"><i class="fa fa-exchange" aria-hidden="true"></i> Transferir de datos básicos</button>
									</div>
								</div> <br>

								<div class="row">
									<div class="col-sm-4">
										<label class="control-label">Nombre</label>
										<input type="text" id="nombreC" class="form-control">
									</div>
									<div class="col-sm-4">
										<label class="control-label">Cargo</label>
										<input type="text" id="cargoC" class="form-control">
									</div>

									<div class="col-sm-4">
										<label class="control-label">Correo Electrónico</label>
										<input type="text" id="emailC" class="form-control">
									</div>
								</div>

								<div class="row">
									<div class="col-sm-4">
											<label class="control-label">Teléfono y extensión</label>
											<input id="telefonoC" class="form-control datFc" type="text"> 
										</div>
										<div class="col-sm-4">
											<label class="control-label">Celular</label>
											<input type="text" id="celularC" class="form-control">
										</div>
										<br><br><br><br>
										<div class="col-sm-4">
											<button type="button" class="btn btn-success" onclick="agregarContacto();">
												<i  class="fa fa-plus cursor" aria-hidden="true"></i>
											</button>
											<!--<button onclick="savelist();">Guardar Lista</button> -->
										</div>
								</div> <br>
								<div class="row">
									<div class="col-sm-6">
										<table id="contacList" class="table">
											<thead>
												<tr>
													<th></th>
													<th>Nombre</th>
													<th>Cargo</th>
													<th>Correo</th>
													<th>Teléfono</th>
													<th>Celular</th>
												</tr>
											</thead>
											<tbody>
												<?php
												
													foreach ($datosCliente['basicos'][0]['contactos']['rows'] as $keyx => $valuex) {
														echo '<tr id="cont_'.$valuex['nombre'].'" idRel="'.$valuex['idCont'].'" nombre="'.$valuex['nombre'].'" cargo="'.$valuex['cargo'].'" email="'.$valuex['email'].'" telefono="'.$valuex['telefono'].'" celular="'.$valuex['celular'].'" >';
														echo '<td><span class="glyphicon glyphicon-remove" onclick="removeClien(\''.$valuex['idCont'].'\');"></span></td>';
														echo '<td>'.$valuex['nombre'].'</td>';
														echo '<td>'.$valuex['cargo'].'</td>';
														echo '<td>'.$valuex['email'].'</td>';
														echo '<td>'.$valuex['telefono'].'</td>';
														echo '<td>'.$valuex['celular'].'</td>';
														echo '<td></td>';
														echo '</tr>';
													}
												?>
											</tbody>
										</table>
									</div>  
								</div>
							</div><!-- fin del Tab Directorio de contactos -->


		<div id="datosCredito" class="tab-pane fade">
		  <div class="row">
		  <div class="col-sm-3">
			<label>Tipo de Crédito</label>
			<select name="tipoDeCredito" id="tipoDeCredito" class="form-control">
			  <option value="0">-Selecciona un Crédito-</option>
			  <?php
				foreach ($tipoCredito as $keyCre => $valueCre) {
					if(isset($datosCliente)){
					  if($datosCliente['basicos'][0]['id_tipo_credito']==$valueCre['id']){
						  echo '<option value="'.$valueCre['id'].'" selected>'.$valueCre['nombre'].'/'.$valueCre['clave'].'</option>';
					  }
					}
					echo '<option value="'.$valueCre['id'].'">'.$valueCre['nombre'].'/'.$valueCre['clave'].'</option>';
				}

			  ?>
			</select>
		  </div>
		  <div class="col-sm-3">
		  </div>
		  <div class="col-sm-2">
				<label>RFC local</label>
			  	<input type="text" id="rfcBanc" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc_banco'];}?>">
			</div>
		  <div class="col-sm-2">
			  <label>Banco local</label>
			  <select id="banco" class="form-control">
				<?php
				foreach ($bancos as $keyBan => $valueBan) {
				  if(isset($datosCliente)){
					  if($datosCliente['basicos'][0]['idBanco']==$valueBan['idbanco']){
						  echo '<option value="'.$valueBan['idbanco'].'" selected>'.$valueBan['nombre'].'/'.$valueBan['Clave'].'</option>';
					  }
				  }

				  echo '<option value="'.$valueBan['idbanco'].'">'.$valueBan['nombre'].'/'.$valueBan['Clave'].'</option>';
				}
				?>
			  </select>
			</div>
			<div class="col-sm-2">
			  <label>#Cuenta local</label>
			  <input type="text" id="cuentaBanc" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['numero_cuenta_banco'];}?>">
			</div>
			
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <label class="control-label">Días de Crédito</label>
			  <input id="diasCredito" class="form-control numeros" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['dias_credito'];}?>">
			</div>

			<div class="col-sm-3">
			  <label>Saldo</label>
			  <input id="saldo" type="text" class="form-control numeros" readonly>
			</div>
			<div class="col-sm-2">
				<label>RFC internacional</label>
			  <input type="text" id="rfcBancInter" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc_banco_internacional'];}?>">
			</div>
			<div class="col-sm-2">
			  <label>Banco internacional</label>
			  <input type="text" id="bancoInter" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['idBancoInternacional'];}?>">
			</div>
			<div class="col-sm-2">
			  <label>#Cuenta internacional</label>
			  <input type="text" id="cuentaBancInter" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['numero_cuenta_banco_internacional'];}?>">
			</div>
			
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <label class="control-label">Límite de Crédito</label>
			  <input id="limiteCredito" class="form-control numeros" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['limite_credito'];}?>">
			</div>
		  </div>
		  <?php
				$x = '';
				$y = '';
				  if(isset($datosCliente)){
					  if($datosCliente['basicos'][0]['permitir_vtas_credito']==1){
						$x = 'checked';
					  }else{
						$x = '';
					  }
					  if($datosCliente['basicos'][0]['permitir_exceder_limite']==1){
						$y = 'checked';
					  }else{
						$y= '';
					  }
				  }


		  ?>
		  <div class="row">
			<div class="col-sm-3">
			  <div class="checkbox">
				<label>
				  <input id="checkVc" type="checkbox" value="" <?php echo $x; ?>>
				  Permitir ventas a crédito
				</label>
			  </div>
			  <div class="checkbox disabled">
				<label>
				  <input id="checkLc" type="checkbox" value="" <?php echo $y; ?>>
				  Permitir exceder límite de crédito
				</label>
			  </div>
			</div>
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <label>Descuento por pronto pago (%)</label>
			  <input id="descuentoPP" type="text" class="form-control numeros" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['dcto_pronto_pago'];}?>">
			</div>
			<div class="col-sm-3">
			  <label>Intereses Moratorios (%)</label>
			  <input id="interesesMoratorios" type="text" class="form-control numeros" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['intereses_moratorios'];}?>">
			</div>
			<div class="col-sm-3"></div>
			<div class="col-sm-3"></div>
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <label class="control-label">Moneda</label>
			  <!--<input id="moneda" class="form-control" type="text" value=""> -->
			  <select id="moneda" class="form-control">
				<?php

				  foreach ($moneda as $keyMon => $valueMon) {
					if(isset($datosCliente)){
					  if($datosCliente['basicos'][0]['id_moneda']==$valueMon['coin_id']){
						  echo '<option value="'.$valueMon['coin_id'].'" selected>'.$valueMon['description'].'/'.$valueMon['codigo'].'</option>';
					  }
					}
					echo '<option value="'.$valueMon['coin_id'].'">'.$valueMon['description'].'/'.$valueMon['codigo'].'</option>';
				  }

				?>
			  </select>
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Lista de Precio</label>
			  <select id="listaPrecio" class="form-control">
				<option value="0" selected>-Ninguna-</option>
				<?php
				foreach ($listaPre as $key1 => $value1) {
				  if(isset($datosCliente)){
					  if($datosCliente['basicos'][0]['id_lista_precios']==$value1['id']){
						  echo '<option value="'.$value1['id'].'" selected>'.$value1['nombre'].'</option>';
					  }else{
						 echo '<option value="'.$value1['id'].'">'.$value1['nombre'].'</option>';
					  }
					  
				  }				  
				}
				?>
			  </select>
			</div>
		  </div>
		  <div class="row">
			<!-- <div class="col-sm-3">
			  <label>Banco local</label>
			  <select id="banco" class="form-control">
				<?php
				foreach ($bancos as $keyBan => $valueBan) {
				  if(isset($datosCliente)){
					  if($datosCliente['basicos'][0]['idBanco']==$valueBan['idbanco']){
						  echo '<option value="'.$valueBan['idbanco'].'" selected>'.$valueBan['nombre'].'/'.$valueBan['Clave'].'</option>';
					  }
				  }

				  echo '<option value="'.$valueBan['idbanco'].'">'.$valueBan['nombre'].'/'.$valueBan['Clave'].'</option>';
				}
				?>
			  </select>
			</div>
			<div class="col-sm-3">
			  <label>Cuenta local</label>
			  <input type="text" id="cuentaBanc" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['numero_cuenta_banco'];}?>">
			</div>
			<div class="col-sm-3">
				<label>RFC local</label>
			  	<input type="text" id="rfcBanc" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc_banco'];}?>">
			</div>
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <label>Banco internacional</label>
			  <input type="text" id="bancoInter" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['idBancoInternacional'];}?>">
			</div>
			<div class="col-sm-3">
			  <label>Cuenta internacional</label>
			  <input type="text" id="cuentaBancInter" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['numero_cuenta_banco_internacional'];}?>">
			</div>
			<div class="col-sm-3">
				<label>RFC internacional</label>
			  <input type="text" id="rfcBancInter" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc_banco_internacional'];}?>">
			</div>
		  </div> -->
		</div>
	</div><!-- Fin de tab credito -->
		<div id="datosComisiones" class="tab-pane fade">
		  <div class="row">
			<div class="col-sm-3">
			  <label>Comisión de Venta (%)</label>
			  <input id="comisionVenta" type="text" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['comision_vta'];}?>">
			</div>
			<div class="col-sm-3">
			  <label>Comisión de Cobranza (%)</label>
			  <input id="comisionCobranza" type="text" class="form-control" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['comision_cobranza'];}?>">
			</div>
		  </div>
		  <div class="row">
			<div class="col-sm-3">
			  <label>Vendedor</label>
			  <select id="vendedor" class="form-control">
				<option value="0">-Selecciona Vendedor-</option>
				<?php
				$empleados['empleados'];
				  foreach ($empleados['empleados'] as $key8 => $value8) {
					if(isset($datosCliente)){
						if($datosCliente['basicos'][0]['idVendedor']==$value8['idEmpleado']){
							echo '<option value="'.$value8['idEmpleado'].'" selected>'.$value8['nombreEmpleado'].' '.$value8['apellidoMaterno'].'</option>';
						}
					}

					echo '<option value="'.$value8['idEmpleado'].'">'.$value8['nombreEmpleado'].' '.$value8['apellidoMaterno'].'</option>';
				  }
				?>


				?>
			  </select>
			</div>
		  </div>
		</div><!-- Fin del tab de comisiones  -->
		<div id="datosEnvios" class="tab-pane fade">
		  <div class="row">
		   <div class="col-sm-4">
			  <label>Domicilio de Envíos</label>
			  <textarea id="enviosDom" cols="30" rows="5" class="form-control"><?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['envios'];}?></textarea>
		   </div>
		  </div>
		</div><!-- Fin del tab de envios-->

		<div id="datosContables" class="tab-pane fade">
			<br>
		  <div class="row">
		  	<div class="col-sm-3">
			  <label>Cuenta Contable</label>
                <img src='images/cuentas.png' onclick='iracuenta()' title='Abrir Ventana de Cuentas' style='vertical-align:middle;'>
                <img src='images/reload.png' class="btn_cuenta_contable" onclick='obtener_cuentas();' title='Actualizar Cuentas' style='vertical-align:middle;'>
				  <select id="cuentaCont" class="form-control">
						<option value="0">-Selecciona Cuenta-</option>
					  <?php
						  foreach ($cuentas as $keyCont => $valueCont) {
							if(isset($datosCliente)){
							  if($datosCliente['basicos'][0]['cuenta']==$valueCont['account_id']){
								  echo '<option value="'.$valueCont['account_id'].'" selected>'.$valueCont['description'].' ('.$valueCont['manual_code'].')'.'</option>';
							  }
							}
							echo '<option value="'.$valueCont['account_id'].'">'.$valueCont['description'].' ('.$valueCont['manual_code'].')'.'</option>';
						  }
						?>
					</select>
		  	</div>
				<div class="col-sm-3" id="preopolizas_provision_container">
					<label for="prepolizas_provision">Prepoliza de provision:</label>
                    <i class="material-icons" onclick="iraprepolizas();" title="Agregar Prepoliza" style="font-size:1.3em;vertical-align:middle; color:#96BE33;">add_circle</i>
                    <img src='images/reload.png' class="btn_prepol_prov" onclick='obtener_prepol_prov();' title='Actualizar Cuentas' style='vertical-align:middle;'>
					<select id="prepolizas_provision" class="form-control">
						<option value="0">Seleccione una prepoliza.</option>
						<?php
							while($prepoliza_pr = $prepolizas_prov->fetch_assoc()){
								$id     = $prepoliza_pr['id'];
								$nombre = $prepoliza_pr['nombre'];
								$selected = '';
								if($datosCliente['basicos'][0]['id_prepoliza'] == $id)
									$selected = 'selected';
								echo("<option value='$id' $selected>$nombre</option>");
							}
						?>
					</select>
				</div>
				<div class="col-sm-3" id="preopolizas_pago_container">
					<label for="prepolizas_pago">Prepoliza de pago:</label>
                    <i class="material-icons" onclick="iraprepolizas();" title="Agregar Prepoliza" style="font-size:1.3em;vertical-align:middle; color:#96BE33;">add_circle</i>
                    <img src='images/reload.png' class="btn_prepol_pago" onclick='obtener_prepol_pago();' title='Actualizar Cuentas' style='vertical-align:middle;'>
					<select id="prepolizas_pago" class="form-control">
						<option value="0">Seleccione una prepoliza.</option>
						<?php
							while($prepoliza_pa = $prepolizas_pago->fetch_assoc()){
								$id     = $prepoliza_pa['id'];
								$nombre = $prepoliza_pa['nombre'];

								$selected = '';
								if($datosCliente['basicos'][0]['id_prepoliza_pagos'] == $id)
									$selected = 'selected';
								echo("<option value='$id' $selected>$nombre</option>");
							}
						?>
					</select>
				</div>
				<div class="col-sm-3" id="cuentas_gastos_container">
					<label for="cuentas_gastos">Seleccione la cuenta de gasto</label>
                    <img src='images/cuentas.png' onclick='iracuenta()' title='Abrir Ventana de Cuentas' style='vertical-align:middle;'>
                    <img src='images/reload.png' class="btn_cuenta_gasto" onclick='obtener_cuenta_gasto();' title='Actualizar Cuentas' style='vertical-align:middle;'>                           
					<select id="cuentas_gastos" class="form-control">
						<option value="0">Seleccione una cuenta de gasto.</option>
						<?php 
						if(intval($cuentasGastos->num_rows))
						{
							while ($cuenta_gasto = $cuentasGastos->fetch_assoc()) 
							{
								$id     = $cuenta_gasto['id'];
								$nombre = $cuenta_gasto['nombre'];

								$selected = '';
								if($datosCliente['basicos'][0]['id_cuenta_gasto'] == $id)
									$selected = 'selected';

								echo("<option value='$id' $selected>$nombre</option>");
							}
						}
							
						?>
					</select>
				</div>
		  </div>
		</div><!-- Fin del tab Datos Contable-->
		<div id="accesoPortal" class="tab-pane fade">
		  <div class="row">
		  <div class="col-sm-12" style="margin-top: 20px;">
			   <div class="col-sm-2">
					<b>Correo:</b>
			   </div>  
			   <div class="col-sm-10">
					<input style="width:300px;" id="correoportal" class="form-control" type="text" value="<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['email'];}?>" readonly>
			   </div>

		   </div>
		   <div class="col-sm-12" style="margin-top: 10px;">
			   <div class="col-sm-2">
					<b>Usuario:</b>
			   </div>  
			   <div class="col-sm-10">
					<input style="width:300px;" id="userportal" class="form-control" type="text" value="usuarioCliente_<?php
						if(isset($datosCliente)){echo $datosCliente['basicos'][0]['id'];}?>" readonly>
			   </div>

		   </div>
		   <div class="col-sm-12" style="margin-top: 10px;">
			   <div class="col-sm-2">
					<b>Contraseña:</b>
			   </div>  
			   <div class="col-sm-10">
					<input style="width:300px;" id="passportal" class="form-control" type="password" value="<?php echo randpass(); ?>" readonly>
			   </div>
			   
		   </div>
		   <div class="col-sm-12" style="margin-top: 10px;">
				<div class="col-sm-2">
				&nbsp;
			   </div> 
			   <div class="col-sm-10">
					<button id="btnenviarCorreo" txt-original='Enviar correo' txt-click='Enviando correo' type="button" class="btn btn-default" onclick="enviarCorreoPortal();">Enviar correo</button>
			   </div>  
			   
		   </div>
		  </div>
		</div><!-- Fin del tab accesoPortal-->
	  </div>  <!-- Fin del div de los tabs -->
  </div><!-- fin de contenedor overflow -->
  </div> <!-- Fin del Panel Body -->
  </div>
</div>
  <!--          Molda Success           -->
  <div id="modalSuccess" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content panel-success">
			<div class="modal-header panel-heading">
				<h4 id="modal-label">Exito!</h4>
			</div>
			<div class="modal-body">
				<p>Tu Cliente se guardo existosamente</p>
			</div>
			<div class="modal-footer">
				<button id="modal-btnconf2-uno" type="button" class="btn btn-default" onclick="back();">Continuar</button>
			</div>
		</div>
	</div>
  </div>
</body>
</html>
