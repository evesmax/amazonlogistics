<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Formulario de Cliente</title>
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<script src="../../libraries/jquery.min.js"></script>
	<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="../../libraries/numeric.js"></script>
	<script src="js/medico.js"></script>
	<script src="../../libraries/numeric.js"></script>
<!--Select 2 -->
	<script src="../../libraries/select2/dist/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
<!-- datetimepicker -->
<link rel="stylesheet" href="../../libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

<script src="../../libraries/bootstrap-datetimepicker/js/moment.js"></script>

<script src="../../libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<!-- 	<script>
		$(document).ready(function() {
		  $('#numeros').numeric();
		  $('#tipoClas').select2({'width':'100%'});
		  $('#tipoDeCredito').select2({'width':'100%'});
		  $('#moneda').select2({'width':'100%'});
		  $('#banco').select2({'width':'100%'});
		  $('#vendedor').select2({'width':'100%'});
		  $('#cuentaCont').select2({'width':'100%'});
		  $(".numeros").numeric();
		});
		
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
	</script> -->
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
		  <button type="button" class="btn btn-primary" onclick="guardar();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
		</div>
		<div class="col-sm-1">
		<?php
		  if($idMedico!=''){
			echo '<span class="label label-warning">Editando</span>';
			echo "<input id='idmedico' class='form-control' type='hidden' value='$idMedico'>";
		  }else{
			echo '<span class="label label-success">Nuevo</span>';
		  }

		?>
		</div>
	</div>
  <div class="panel panel-default">
  <div class="panel-heading"><h5>Médico<?php
						if(isset($datosMedico)){echo ' ('.$datosMedico['nombre'].')';}?></h5></div>
  <div class="panel-body">
	<div style="heigth:300px;">
	  <div id="tabsCliente">
		<ul class="nav nav-tabs">
		  <li class="active"><a data-toggle="tab" href="#basicos">Datos Básicos</a></li>
		  <li><a data-toggle="tab" href="#datosComision">Datos de Comisión</a></li>
		</ul>
	  </div>
	  <div class="tab-content" style="height:350px;">
		<div id="basicos" class="tab-pane fade in active">
		  <div class="row">
			<div class="col-sm-4">
				<label class="control-label"><span style="color:red;">*</span>Código</label>
				<input id="codigo" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['codigo'];}?>">
			</div>
			<div class="col-sm-4">
			  <label class="control-label"><span style="color:red;">*</span> Nombre</label>
			  <input id="nombre" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['nombre'];}?>">
			</div>
			<div class="col-sm-4">
			  <label class="control-label"><span style="color:red;">*</span> Cédula</label>
			  <input id="cedula" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['cedula'];}?>">
			</div>
		  </div>

		  <div class="row">
			<div class="col-sm-6">
			  <label class="control-label">Dirección</label>
			  <input id="direccion" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['dircalle'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Exterior</label>
			  <input id="numext" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['direxterior'];}?>">
			</div>
			<div class="col-sm-3">
			  <label class="control-label">Interior</label>
			  <input id="numint" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['dirinterior'];}?>">
			</div>
		  </div>

		  <div class="row">
			<div class="col-sm-2">
			  <label class="control-label">Colonia</label>
			  <input id="colonia" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['colonia'];}?>">
			</div>
			<div class="col-sm-2">
				<label class="control-label">Código Postal</label>
				<input id="cp" class="form-control numeros" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['codigopostal'];}?>">
			</div>
			<div class="col-sm-2">
				<div class="row">
					<div class="col-sm-8">
						<label class="control-label"><span style="color:red;"></span> País</label>
						<select id="selectPais" class="form-control" >
							<option value="<?php if(isset($datosMedico)){echo $datosMedico['idPais'];} ?>">
								<?php if(isset($datosMedico)){echo $datosMedico['descPais'];} ?>
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
							<option value="<?php if(isset($datosMedico)){echo $datosMedico['idEstado'];} ?>">
								<?php if(isset($datosMedico)){echo $datosMedico['descEstado'];} ?>
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
							<option value="<?php if(isset($datosMedico)){echo $datosMedico['idMunicipio'];} ?>">
								<?php if(isset($datosMedico)){echo $datosMedico['descMunicipio'];} ?>
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
						if(isset($datosMedico)){echo $datosMedico['ciudad'];}?>">
			</div>
		  </div>

		  <div class="row">

			<div class="col-sm-3">
			  <label class="control-label">Teléfono</label>
			  <input id="tel1" class="form-control numeros" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['telefono'];}?>">
			</div>

		  </div>

		</div><!-- Fin del div basicos -->
		<div id="datosComision" class="tab-pane fade">
		  <div class="row">
			<div class="col-sm-4">
				<label class="control-label"><span style="color:red;"></span>Comisión de Venta (%)</label>
				<input id="comisionventa" class="form-control" type="number" value="<?php
						if(isset($datosMedico)){echo $datosMedico['comisionventa'];}?>">
			</div>
		</div>
		<div class="row" style="display:none;">
			<div class="col-sm-4">
			  <label class="control-label"><span style="color:red;"></span>Comisión de Cobranza (%)</label>
			  <input id="comisioncobranza" class="form-control" type="number" value="<?php
						if(isset($datosMedico)){echo $datosMedico['comisioncobranza'];}?>">
			</div>
		</div>
		<div class="row" style="display:none;">
			<div class="col-sm-4">
				<label class="control-label">Vendedor</label>
						<select id="vendedor" class="form-control" >
							<option value="<?php if(isset($datosMedico)){echo $datosMedico['idVendedor'];} ?>">
								<?php if(isset($datosMedico)){echo $datosMedico['descVendedor'];} ?>
							</option>
						</select>


			  <!-- <label class="control-label"><span style="color:red;"></span>Vendedor [Lista de usuarios registrados]</label>
			  <input id="nombre" class="form-control" type="text" value="<?php
						if(isset($datosMedico)){echo $datosMedico['nombre'];}?>"> -->
			</div>
		  </div>
		</div><!-- fin del Tab de comisión -->


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
