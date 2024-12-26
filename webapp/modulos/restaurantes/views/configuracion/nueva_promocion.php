<?php
// Valida que existan productos
	if (empty($productos)) {?>
		<div align="center">
			<h3><span class="label label-default">* No hay productos *</span></h3>
		</div><?php
		
		return 0;
	} ?>
<style>
	 /* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
} 
</style>
<div class="col-md-5"><?php
// Valida que existan productos
	if (!empty($productos)) { ?>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div class="panel-group" id="accordion_productos" role="tablist" aria-multiselectable="true">
					<div class="panel panel-default">
						<div hrefer class="panel-heading" id="heading_productos" role="tab" role="button" style="cursor: pointer" data-toggle="collapse" data-parent="#accordion_productos" href="#tab_productos" aria-controls="collapse_productos" aria-expanded="true">
							<h4 class="panel-title">
								<strong>Productos</strong>
							</h4>
						</div>
						<div id="tab_productos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_productos">
							<div class="panel-body">
								<table id="tabla_productos" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>											
										<tr>
											<th align="center"><strong>Producto</strong></th>
											<th><strong>Unidad</strong></th>
											<th align="center"><strong>Precio</strong></th>
											<th align="center"><strong>d</strong></th>
											<th align="center"><strong>f</strong></th>
											<th align="center"><strong>l</strong></th>
											<th align="center"><strong><i class="fa fa-check fa-lg" onclick="configuracion.clicksFakes();"></i></strong></th> 
											<!--<th align="center"><strong><i class="fa fa-check fa-lg"></i></strong></th> -->
										</tr>
									</thead>
									<tbody><?php
										foreach ($productos as $k => $v) { ?>
											<tr id="tr_<?php echo $v['idProducto'] ?>" onclick="configuracion.agregar_producto({comprar_recibir: configuracion.comprar_recibir, tipo: $('#tipo').val(), id:<?php echo $v['idProducto'] ?>, nombre:'<?php echo $v['nombre'] ?>', precio:'<?php echo $v['precio'] ?>', div:'div_productos_agregados', check:$('#check_<?php echo $v['idProducto'] ?>').prop('checked')})" style="cursor: pointer">
												<td align="center">
													<?php echo $v['nombre'] ?>
												</td>
												<td>
													<?php echo $v['unidad'] ?>
												</td>
												<td align="center">
													$ <?php echo number_format($v['precio'], 2, '.', ''); ?>
												</td> 
												<td>
													<?php echo $v['departamento'] ?>
												</td>
												<td>
													<?php echo $v['familia'] ?>
												</td>
												<td>
													<?php echo $v['linea'] ?>
												</td>
												<td align="center">
													<input style="cursor: pointer" disabled="1" type="checkbox" id="check_<?php echo $v['idProducto'] ?>" />
												</td>
											</tr><?php
										} ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			configuracion.convertir_dataTable2({id: 'tabla_productos'});
		</script><?php
	} ?>
</div> <!-- Fin lado izquierdo -->
<div class="col-md-7">
	<div class="panel panel-<?php echo $panel ?>">
		<div class="panel-heading">
			<h4 class="panel-title">
				<strong>
					Promocion
				</strong>
			</h4>
		</div>
		<div class="panel-body">
			<div class="row">
			<!-- En esta div se cargan los productos de la receta -->

				<div class="col-md-12 col-sm-12" id="div_productos_agregados">
					<br /><br />
					<blockquote style="font-size: 16px">
				    	<p>
				      		Selecciona <strong>"Productos"</strong> para agregarlos a la promocion.
				    	</p>
				    </blockquote>
				</div>
			</div>
			<form id="form_promocion">
				<div class="row">
					<div class="col-md-8 col-sm-8">
						<h3><small>Nombre:</small></h3>
		        		<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-font"></i></span>
							<input required="1" id="nombre" type="text" class="form-control"/>
						</div>
					</div>
					<div class="col-md-4 col-sm-4">
						<h3><small>Tipo:</small></h3>
			       		<div class="input-group input-group-lg" id="notificaciones">
							<span class="input-group-addon"><i class="fa fa-edit"></i></span>
							<select id="tipo" onchange="configuracion.cambiar_tipo({tipo: $(this).val()})" class="selectpicker" data-width="80%">
								<option selected value="1">Por descuento</option>
								<option value="2">Por cantidad</option>
								<?php 
									if($isFood==1){
								?>
								<option value="3">Mayor precio</option>
								<option value="4">Precio fijo</option>
								<option value="5">Comprar y obtener</option>
								<?php 
									}
								?>
								<option value="10">Lista de precio</option>
								<option value="11">Cumpleaños</option>
							</select>
						</div>
						<script>configuracion.cambiar_tipo({tipo: $('#tipo').val()})</script>
					</div>
				</div>
				<br>
				<div class="row" id="div_promo_cumple">
					<div class="col-sm-12">
						<!--<label class="switch">
						  <input type="checkbox">
						  <span class="slider round"></span>
						</label>     -->
						<input id="toggle-two" type="checkbox" checked data-toggle="toggle" data-on="Cortesía" data-off="Descuento" data-onstyle="default" data-offstyle="default" data-width="150">
					</div>
				</div>
				<div class="row" id="div_lista_precios">
					<div class="col-md-4 col-sm-4">
						<h3><small>Lista de precio:</small></h3>
						<select id="listaPrecio" class="form-control" onchange="configuracion.cambiaLista()">
							<option value="0">-Selecciona Lista-</option>
							<?php 
								foreach ($listas as $key => $value) {
									echo '<option value="'.$value['id'].'" descuento="'.$value['porcentaje'].'">'.$value['nombre'].'</option>';
								}

							?>
						</select>
					</div>	
					<div class="col-md-4 col-sm-4">
						<h3><small>Por cada:</small></h3>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-cubes"></i></span>
							<input id="cantidadL" type="number" class="form-control" value="0"/>
						</div>
					</div>
					<div class="col-md-4 col-sm-4" id="desLista">
						<h3><small>Descuento:</small></h3>
		        		<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-percent"></i></span>
							<input id="descuentoL" type="number" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="row" id="div_por_descuento">
					<div class="col-md-3 col-sm-3">
						<h3><small>Descuento:</small></h3>
		        		<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-percent"></i></span>
							<input id="descuento" type="number" class="form-control"/>
						</div>
					</div>
					<!--
						<h3><small style="color: white;">.</small></h3>
						<div class="col-md-4 col-sm-4">
			        		<div class="input-group input-group-lg">
								<span class="input-group-addon">Sucursal</span>
								<select id="sucursal" class="selectpicker" data-width="80%" multiple>
									<?php 
										foreach ($sucursales as $key => $value) {
											echo '<option value="'.$value['idSuc'].'">'.$value['nombre'].'</option>';
										}
									 ?>														
								</select>
							</div>
						</div>
					-->
				</div>

				<div class="row" id="div_precio_fijo">
					<div class="col-md-3 col-sm-3">
						<h3><small>Precio:</small></h3>
		        		<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
							<input id="precio_fijo" type="number" class="form-control"/>
						</div>
					</div>
				</div>
				<div class="row" id="div_por_cantidad">
					<div class="col-md-6 col-sm-6">
						<h3><small>Por cada:</small></h3>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-cubes"></i></span>
							<input id="cantidad" type="number" class="form-control"/>
						</div>
					</div>
					<div class="col-md-6 col-sm-6">
						<h3><small>Descontar:</small></h3>
						<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fa fa-cube"></i></span>
							<input id="cantidad_descuento" type="number" class="form-control"/>
						</div>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-4 col-sm-4">
		        		<div class="input-group input-group-lg">
							<span class="input-group-addon">Sucursal</span>
							<select id="sucursal" class="selectpicker" data-width="80%" multiple>
								<?php 
									foreach ($sucursales as $key => $value) {
										echo '<option value="'.$value['idSuc'].'">'.$value['nombre'].'</option>';
									}
								 ?>														
							</select>
						</div>
					</div>						

				</div>
				<div class="row">	
					
					<div class="col-md-4 col-sm-4">							
						<h3><small>Unidad Medida:</small></h3>
		        		<div class="input-group input-group-lg">
							<span class="input-group-addon"><i class="fas fa-ruler"></i></span>
							<select id="unidadventa" class="selectpicker">
								<?php 
									foreach ($unidades as $key => $value) {										
										echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';										
									}
								 ?>	
							</select>
						</div>					
					</div>				
					<div class="col-md-8 col-sm-8 pull-right">
						<div style="padding-top: 60px;">
							<div class="col-md-6 col-sm-6">
								<input type="text" id="clavesat" class="form-control">
							</div>
							<div class="col-md-6 col-sm-6">
								<label class="checkbox-inline"> <input id="chkgen" type="checkbox"> <b>Usar Clave Generica</b> </label>
							</div>																
						</div>																			
					</div>
					
				</div>
				<br />

				<div class="row">
					<div class="col-md-12 col-sm-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h5><i class="fa fa-clock-o"></i> Horario</h5>
							</div>
							<div class="panel-body">
								<div class="row">
									
								</div><br>								
								<div class="col-md-5">
									<label class="checkbox-inline">
										<input id="do" type="checkbox" class="chpro" value="">
										Do
									</label>
									<label class="checkbox-inline">
										<input id="lu" type="checkbox" class="chpro" value="">
										Lu 
									</label>
									<label class="checkbox-inline">
										<input id="ma" type="checkbox" class="chpro" value="">
										Ma 
									</label>
									<label class="checkbox-inline">
										<input id="mi" type="checkbox" class="chpro" value="">
										Mi 
									</label>
									<label class="checkbox-inline">
										<input id="ju" type="checkbox" class="chpro" value="">
										Ju 
									</label>
									<label class="checkbox-inline">
										<input id="vi" type="checkbox" class="chpro" value="">
										Vi 
									</label>
									<label class="checkbox-inline">
										<input id="sa" type="checkbox" class="chpro" value="">
										Sa 
									</label>
								</div>
								<div class="col-md-5">
									<div class="row" align="center">
										<div class="col-xs-5">
											<input 
												type="time" 
												value="<?php echo $value['inicio'] ?>" 
												id="inicio" />
										</div>
										<div class="col-xs-1">a</div>
										<div class="col-xs-5">
											<input 
												type="time" 
												value="<?php echo $value['fin'] ?>" 
												id="fin" />
										</div>
									</div>
								</div>
								<div class="col-md-1">
									<button onclick="allcheck();" class="btn btn-info btn-sm pull-right" type="button"><i class="fa fa-check"></i> Siempre</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="row">
				<div class="col-md-8 col-sm-8">
					<button 
						id="btn_guardar_promocion" type="button" 
						class="btn btn-success btn-lg" 
						data-loading-text="<i class='fa fa-refresh fa-spin'></i>" 
						onclick="configuracion.guardar_promocion({form:'form_promocion', btn:'btn_guardar_promocion'})">
						<i class="fa fa-check"></i> Ok
					</button>
					<button 
						id="btn_actualizar_promocion" 
						style="display: none" 
						type="button" 
						class="btn btn-primary btn-lg" 
						data-loading-text="<i class='fa fa-refresh fa-spin'></i>" 
						onclick="configuracion.actualizar_promocion({id_promocion:$(this).attr('id_promocion'), form:'form_promocion', btn:'btn_actualizar_promocion'})">
						<i class="fa fa-check"></i> Ok
					</button>
				</div>
			</div>
		</div>
	</div>
</div><!-- Fin lado derecho -->
<script>
	$(document).ready(function() {
		  $('#toggle-two').bootstrapToggle({
	      on: 'Cortesía',
	      off: 'Descuento'
	    });
	$('#toggle-two').change(function() {
      var x = $('#toggle-two').is(':checked');
      if(x==true){
      	$('#div_por_descuento').hide();
      }else{
      	$('#div_por_descuento').show();
      }
    })    	
        $('#unidadventa').val(6);
        $('.selectpicker').selectpicker('refresh');
	});
	function allcheck(){
		$("#inicio").val('00:01');
		$("#fin").val('23:59');
		$(".chpro").attr('checked', 'checked');		
	}
	function verificaclave(){
		alert(111);
	}
	$("#chkgen").change(function(event) {

		if( $('#chkgen').prop('checked') ) {
		    $('#clavesat').val('01010101');
		}else{
			$('#clavesat').val('');
		}
		
	});
</script>