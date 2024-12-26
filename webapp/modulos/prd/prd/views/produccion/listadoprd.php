<!DOCTYPE >
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script src="js/produccion/listaprd.js" type="text/javascript"></script>
	<script src="js/produccion/cicloprd.js" type="text/javascript"></script>


</head>
<script>
$( document ).ready(function() {
	<?php 
	if($tipoexplosion == 1){?>
		$("#btnexplosionmasiva").hide();
	<?php }
	?>
	listareq();
});
</script>
<body>
	<input type="hidden" id="insumosvariables" value="<?php echo $insumosvariables;?>">
  	<input type="hidden" id="explosionmat" value="<?php echo $tipoexplosion;?>">
  	<input type="hidden" id="ordenmasiva" value="<?php echo $ordenmasiva;?>">
  	<input type="hidden" id="mostrarprv" value="<?php echo $mostrarprv;?>">
  	<input type="hidden" id="ord_x_lote" value="<?php echo $ord_x_lote;?>">
  	<input type="hidden" id="productovariable" value="0">
   	<input type="hidden" id="explotandoinsumosmasivos" value="0"><!-- este es para saber si esta dentro de una explosion masiva -->

	<br>
	<!-- modales -->

<div id="modal-conf4" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Atencion!</h4>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas inactivar esta cotizacion?</p>
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf4-uno" type="button" class="btn btn-default">Continuar</button> 
                <button id="modal-btnconf4-dos" type="button" class="btn btn-default">Cancelar</button>
            </div>
        </div>
    </div> 
</div> 

<div id="modal-confdelop" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Atencion!</h4>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar esta orden de produccion?</p>
            </div>
            <div class="modal-footer">
                <button id="modal-confdelop-uno" type="button" class="btn btn-default">Continuar</button> 
                <button id="modal-confdelop-dos" type="button" class="btn btn-default">Cancelar</button>
            </div>
        </div>
    </div> 
</div>

<div id="modal-confusar" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Orden de produccion actualizada!</h4>
            </div>
            <div class="modal-body">
                <p>El producto esta listo para producirse.</p>
            </div>
        </div>
    </div> 
</div>  


<div id="modal-conf3" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Orden de produccion guardada!</h4>
            </div>
            <div class="modal-body">
                <p>La orden fue guardada exitosamente.</p>
            </div>
        </div>
    </div> 
</div> 

<div id="modal-confexp" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Pre-requisicione guardada!</h4>
            </div>
            <div class="modal-body">
                <p>La pre-requisicion fue guardada exitosamente.</p>
            </div>
        </div>
    </div> 
</div> 

<div id="modal-conf1" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Atencion!</h4>
            </div>
            <div class="modal-body">
                <p>Tienes una orden de produccion sin guardar, ¿Deseas continuar sin guardar cambios?</p>
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf1-uno" type="button" class="btn btn-default">Continuar</button> 
                <button id="modal-btnconf1-dos" type="button" class="btn btn-default">Cancelar</button>
            </div>
        </div>
    </div> 
</div>


<div id="modal-conf2" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Atencion!</h4>
            </div>
            <div class="modal-body">
                <p>Tienes una orden de produccion sin guardar, ¿Deseas continuar sin guardar cambios?</p>
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf2-uno" type="button" class="btn btn-default">Continuar</button> 
                <button id="modal-btnconf2-dos" type="button" class="btn btn-default">Cancelar</button>
            </div>
        </div>
    </div> 
</div>
<!-- modals -->

	<div class="container well" style="padding:25px;margin-bottom: 150px;">
		<div class="row" style="padding-bottom:20px;">
            <div class="col-xs-12 col-md-12" style="padding:0px 0px 0px 3px;"><h3>Orden de producción  </h3></div>
            <input type="hidden" id="ist" value="0">
            <input type="hidden" id="it" value="0">
            <input type="hidden" id="cadimps" value="0">
            <input type="hidden" id="auxDescG" value="0"> 
        </div>
        <input type="hidden"  id="orden" value="" />
        
        <div class="row" style="margin-bottom:10px;">
			<button class="btn btn-default" type="button" id="nueva" onclick="nreq();">
				Nueva orden
			</button>
			<button id="btnlistorden" class="btn btn-default" type="submit" onclick="listareq();">
				Listado ordenes
			</button>
			<button type="button" class="btn btn-primary btn-primary" id="btnexplosionmasiva" style="text-align: center;" data-loading-text="<i class='fa fa-refresh fa-spin'></i>">
				Explosion de materiales masivo
			</button>

			<button id="btnback" class="btn btn-default pull-right" style="visibility:hidden;" onclick="listareq();">
				<span class="glyphicon glyphicon-arrow-left"></span> Regresar
			</button>
		</div>
		<div id="nreq_load" class="row" style="display:none;font-size:12px;padding:2px;">
			<span class="fa fa-refresh fa-spin"></span>
		</div>

		<div id="listareq_load" class="row" style="display:none;font-size:12px;padding:2px;">
			<span class="fa fa-refresh fa-spin"></span>
		</div>
		
			<div id="listareq" class="row" style="display:block;margin-top:20px;font-size:12px;display:none">
				<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>No. Orden.</th>
							<th>Producto</th>
							<th>Cantidad</th>
							<th>Fecha Registro</th>
							<th>Fecha Inicio.</th>
							<th>Fecha Entrega</th>
							<th>Sucursal</th>
							<th>Usuario</th>
							<th>Estatus</th>
							<th class="no-sort" style="text-align: center;">Acciones</th>
						</tr>
					</thead>
				</table>
			</div>
		<div id="contenido"></div>	
		

	</div>

</body>
</html>