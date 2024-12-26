<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- CSS -->
<link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="../../libraries/dataTable/css/datatables.min.css">
<link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
<link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">

<!-- JS  -->
<script src='../../libraries/jquery.min.js' type='text/javascript'></script>
<script src='../../libraries/bootstrap/dist/js/bootstrap.min.js'></script>
<script src="../../libraries/dataTable/js/datatables.min.js"></script>
<script src='../../libraries/datepicker/js/bootstrap-datepicker.min.js'></script>
<script src='../../libraries/datepicker/js/bootstrap-datepicker.es.js'></script>
<script src='../../libraries/dataTable/js/dataTables.bootstrap.min.js'></script>
<script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
<script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
<script src="../../libraries/export_print/buttons.html5.min.js"></script>
<script src="../../libraries/export_print/jszip.min.js"></script>
<!--<script src="../cont/massxml/massxml.js"></script>-->

<!-- JS Embebido -->
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	$("#buscar").show();
	$("#buscando").hide();
	$('[data-toggle="tooltip"]').tooltip(); 
	$('#inicial,#final').datepicker({
				format: "yyyy-mm-dd",
				language: "es"
			});
	$('[data-toggle="tooltip"]').tooltip().click(function(event) {
		  event.preventDefault();
		});
	//Ocultar boton de eliminar cuando no esta seleccionado temporales.
	$('#asignadas').on('change',function(){
		if ($(this).val() != 1) {
			$('#contenedor-eliminar').hide();
		} else {
			$('#contenedor-eliminar').show();
		}
	});
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};
	$("#pagos").hide()
});
function loadXMLDownloaderPage(){
    window.location.href = 'massxml2/ejemplos/html/index.php?tipo=ciecc';
}
</script>
<style>
.preview{
	font-size:1.5em !important;
	padding: 0 0.2em;
}
.hide-excel{
	display: none;
}
#tabla-data>tbody>tr>td{
	hyphens:auto;
	word-break:break-word;
	text-align:center;
	vertical-align: middle;
}
#tabla-data>thead>tr>th{
	word-break:break-word;
	text-align:center;
	vertical-align:middle;
	padding:.5em 1.2em;
}
.small-col{
	max-width: 1em !important;
}
table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable 
thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable 
thead .sorting_desc_disabled:after{
	font-size: 0.8em !important;
	right:1px !important;
}
table.dataTable thead .sorting::after, table.dataTable thead .sorting_asc::after, table.dataTable 
thead .sorting_desc::after, table.dataTable thead .sorting_asc_disabled::after, table.dataTable 
thead .sorting_desc_disabled::after{
	font-size: 0.8em !important;
	right:1px !important;
}
th { font-size: 1em !important;}
td { font-size: 0.9em !important;}
.buttons-excel {
    border: none !important;
    background: none !important;
    background: transparent !important;
}
.dt-button {
    border: none !important;
    background: none !important;
    background: transparent !important;
}
.dataTables_length label{
    margin-left: 300%;
}
#tabla-data{
    width: 2500px !important;
}
#tabla-data-pagos{
    width: 2500px !important; 
}
svg {
  color: #7b7b7b;    
  height: 25px;
  fill: currentColor;
  vertical-align: bottom;
    
}

span.bigger
{
	font-size:50px;
	color:#8e8e8e;
}
</style>
<div class="container" style='width:100%;'>
	<div class='row' style='border-bottom:4px double #eee;'>
		<div class="col-xs-12 col-md-4" style='text-align:left;'>
			<img src='images/logo_acontia.jpg' style='width:60px;'>
		</div>
		<div class="col-xs-12 col-md-4" style='text-align:center;'>
			<b class='empresa' style='font-size:20px;'>Almacen Digital</b><br>
		</div>
		<div class="col-xs-12" style='text-align:center; width:13%; margin-left:6%;'>
			<a href='javascript:loadXMLDownloaderPage()'><span class="glyphicon glyphicon-cloud-download bigger" title="Descarga Masiva SAT" aria-hidden="true"></span></a>
		</div>
	</div>
	<div class='row' style='margin-top:20px;'>
		<div class='col-xs-12 col-md-7'>
			<div class='row'>
				<div class='col-xs-12 col-md-4'>
					<div class="input-group">
		                <span class="input-group-addon glyphicon glyphicon-calendar" id="basic-addon1"></span>
		                <input type="text" class="form-control" style="top:1px;" id='inicial' placeholder="Fecha Inicial">
	            	</div>
				</div>
				<div class='col-xs-12 col-md-4'>
					<div class="input-group">
		                <span class="input-group-addon glyphicon glyphicon-calendar" id="basic-addon2"></span>
		                <input type="text" class="form-control" style="top:1px;" id='final' placeholder="Fecha Final">
	            	</div>
				</div>
				<div class='col-xs-12 col-md-4'>
					<select id='asignadas' class='form-control' onchange='agregar_funcion()'>
						<option value='1'>Temporales</option>
						<option value='2'>Asignadas</option>
						<option value='3'>Canceladas</option>
						<option value='4'>Comp. de Pagos</option>
					</select>
				</div>
			</div>
			<div class='row' style='margin-top:5px;'>
				<div class='col-xs-12 col-md-4'>
					<select id='tipo_facturas' class='form-control'>
						<option value='1'>Ingresos</option>
						<option value='2'>Egresos</option>
						<option value='3'>Nomina</option>
						<!--<option value='4'>Pagos</option>-->
					</select>
				</div>
				<div class='col-xs-12 col-md-4'>
					<input type='text' id='rfc' class='form-control' placeholder="RFC">
				</div>
				<div class='col-xs-12 col-md-4'>
					<center><button id='buscar' class='btn btn-primary' onclick='buscar(0)'>Buscar</button></center>
				</div>
			</div>
		</div>
		<div class='col-xs-12 col-md-5'>
			<div class='col-xs-12 col-md-8'>
				<div class='col-xs-12'>
					<label>Subir factura(s) xml o zip:</label>
				</div>
				<div class='col-xs-12'>
					<div class="form-group">
						<form name='fac' id='fac' action='' method='post' enctype='multipart/form-data'>
							<input type='file' name='factura[]' id='factura' onchange='check_file()'>
							<p class="help-block">Se recomienda subir archivos con un maximo de 1000 XMLs.</p>
					 </div>	
				</div>
			</div>
			<div class='col-xs-12 col-md-4'>
				<div class='col-xs-12'>
						<button type='submit' id='buttonFactura' class="btn btn-default btn-block" style='color:gray;'><span class='glyphicon glyphicon-upload'></span> Subir CFDIs</button>
					</form>
					<input type='hidden' name='plz' id='plz' value='<?php echo $numPoliza['id']; ?>'>
					<br />
					<span id='verif' style='color:green;display:none;'>Verificando...</span>
				</div>
			</div>

		</div>
	</div>

    <div class='row' style='margin-top:-1%;'>
    </div>
	<div class='row' style='border-top:2px double #eee;padding-top:10px;'>
	</div>
	<div class='row table-responsive' style='margin-top:1em;position:relative;' id='normales'>
		<table id="tabla-data" class="table table-striped table-hover" cellspacing="0" >
				<thead style='background-color:#337ab7;color:white;'>
				<tr>
					<th>Fecha</th>
					<th>RFC</th>
					<th>Emisor</th>
					<th>Receptor</th>
					<th style="width:1em !important;"></th>
					<th>TipoFactura</th>
					<th>FormaPago</th>
					<th>MetodoPago</th>
					<th>Moneda</th>
					<th>Subtotal</th>
					<th>Impuestos IVA</th>
					<th>Total</th>
					<th>Serie y Folio</th>
					<th>UUID Factura</th>
					<th>Version</th>
					<th>Poliza Provision</th>
					<th>Poliza Cobro/Pago</th>
					<th><input type="checkbox" id="checkAll" onchange ="actions()"></th>
					<th class="hide-excel">Domicilio Emisor</th>
					<th class="hide-excel">Domicilio Receptor</th>
				</tr>
			</thead>
			<tbody id='trs'></tbody>
		</table>
	</div>
	<div class='row table-responsive' style='margin-top:1em;position:relative;' id='pagos'>
		<table id="tabla-data-pagos" class="table table-striped table-hover" cellspacing="0" >
				<thead style='background-color:#7b7b7b;color:white;'>
				<tr>
					<th>UUID Comp.</th>
					<th>Fecha</th>
					<th>RFC</th>
					<th>Emisor</th>
					<th>Receptor</th>
					<th class="small-col" style="width:1em !important;"></th>
					<th>TipoFactura</th>
					<th>Serie y Folio</th>
					<th>UUID Factura</th>
					<th>FormaPagoDoc</th>
					<th>MonedaDoc</th>
					<th>ImpSaldoAnt.</th>
					<th>ImpSaldoInsoluto</th>
					<th>ImpPagado</th>
					<th>NumParcialidad</th>
					<th>Fecha Subida</th>
				</tr>
			</thead>
			<tbody id='trs_pagos'></tbody>
		</table>
	</div>
	<div class='row' style='margin-top:20px;border-top:4px double #eee;'>
		<div class="col-md-2 col-md-offset-8" style="text-align: center;">
			<h4>Totales:</h4>
		</div>
		<div class="col-md-2" style="text-align: center;">
			<h5 id="totales">$ 0.00</h5>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-md-offset-6">
			<div class="row">
			<div class="col-md-4" id="pivote">
			</div>
				<div class="col-md-4" id="contenedor-provision">
					<!-- <button class="btn btn-info btn-block" onclick="descargarXMLs()">
						Descargar Seleccionados
					</button> -->
					<button class="btn btn-primary btn-block bloquear" onclick="generar_polizas(1)">
						Generar poliza de Provision
					</button>
				</div>
				<!--<div class="col-md-4" id="contenedor-pago">
					<button class="btn btn-info btn-block bloquear" onclick="generar_polizas(2)">
						Generar poliza de pagos
					</button>
				</div>-->
				<div class="col-md-4" id="contenedor-eliminar">
					<button class="btn btn-danger btn-block bloquear" onclick="eliminarSeleccionados()">
						Eliminar Seleccionados
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="hiddenContainer"></div>
<div id="almacen_repe" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal- style="width: 7.69% !important;"">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Archivos repetidos</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                	<div class="col-md-12">
                		<label>Seleccione los archivos que desea copiar o cancele para no hacer nada</label>
									</div>
                </div>
                <div class="row" style="display: none;" id="load">
                	<div class="col-md-12">
                		<label style="color: green;">Espere un momento...</label>
									</div>
                </div>
                <div class="row">
                	<div class="col-md-12 col-sm-12 col-xs-12 tablaResponsiva">
                		<div class="table-responsive">
                			<table id='repe' class="table">
                			</table>
                		</div>
                	</div>
                </div>
            	</div>
            <div class="modal-footer">
            	<div class="row">
                    <div class="col-md-3 col-md-offset-6">
                      <button type="button" class="btn btn-primary btn-block" onclick="javascript:afrAgregar();">Almacenar</button>
                    </div>
                    <div class="col-md-3">
                      <button type="button" class="btn btn-danger btn-block" onclick="javascript:afrCancelar();">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/almacen.js"></script>