<div class="grid-stack-item" id="<?php echo $id ?>" data-gs-no-resize="1" data-gs-width="2"  data-gs-height="2">
	<div style="cursor: move; width:100%; height:100%;text-align: center;" class="grid-stack-item-content ui-draggable-handle">
		<div>
			<img id="img_<?php echo $id ?>" style="width: 67px; height: 99px; max-width: 100%" src="images/mapademesas/ocupada_cuadrada_2p.png">
			<div id="div_total_<?php echo $id ?>" class="price" style="display:none; font-size: 12px; color: white; position: absolute; left 0; top: 0; background-color: rgba(0,0,0,0.6); padding: 3px; border-radius: 15px" ></div>
			<div id="div_tiempo_<?php echo $id ?>" class="time" style="display: none; font-size: 12px; color: white; position: absolute; right: 0; bottom: 0; background-color: rgba(0,0,0,0.6); padding: 3px; border-radius: 15px"></div>	
			<div 
				onclick="comandera.mandar_mesa_comandera({
				id_mesa: <?php echo $id ?>,
				tipo: 3,
				nombre_mesa_2: '<?php echo $objeto['nombreMesa'] ?>',
				id_comanda: $(this).attr('id_comanda'),													
				tipo_operacion: 1
				})"
				id="mesa_<?php echo $id ?>"
				mesa_status="1"
				id_comanda=""
				style="color: white; width: 55%; cursor: pointer; position: absolute; font-size: 11px; transform: translate(-50%, -50%); left: 50%; top: 30%;">																		  			
					<div id="mesero_<?php echo $id ?>" style="font-size:12px">
						<?php echo $objeto['empleado']; ?>	
					</div>																	
		       		<div id="div_nombre_mesa_<?php echo $id ?>" style=" font-size: 18px;">
		       			<?php echo $objeto['nombreMesa'] ?>					       				
		       		</div>
			</div>
		</div>
	</div>
</div>


<script>
// Carga la comandera
	comandera.mandar_mesa_comandera({
		id_mesa: <?php echo $id ?>,
		tipo: 1,
		id_comanda: '',
		nombre: '<?php echo  $objeto['nombre'] ?>',
		nombre_mesa_2: '<?php echo  $objeto['nombre'] ?>',
		tipo_operacion: 1
	});
	
// Crea la mesa y la agrega a la cuadricula	
	var div = <?php echo $objeto['area_select'] ?>;// departamento
	gridLlD = $('#contenedor-'+div+'').data('gridstack');

	gridLlD.addWidget = function(el, x, y, width, height, auto_position, min_width, max_width, min_height, max_height, id) {

		el = $(el);
		if ( typeof id != 'undefined')
			el.attr('id', id);
		if ( typeof x != 'undefined')
			el.attr('data-gs-x', x);
		if ( typeof y != 'undefined')
			el.attr('data-gs-y', y);
		if ( typeof width != 'undefined')
			el.attr('data-gs-width', width);
		if ( typeof height != 'undefined')
			el.attr('data-gs-height', height);
		if ( typeof min_width != 'undefined')
			el.attr('data-gs-min-width', min_width);
		if ( typeof max_width != 'undefined')
			el.attr('data-gs-max-width', max_width);
		if ( typeof min_height != 'undefined')
			el.attr('data-gs-min-height', min_height);
		if ( typeof max_height != 'undefined')
			el.attr('data-gs-max-height', max_height);
		if ( typeof auto_position != 'undefined')
			el.attr('data-gs-auto-position', auto_position ? 'yes' : null);
		this.container.append(el);
		this._prepare_element(el);
		this._update_container_height();
		return el;
	};
	gridLlD.addWidget($('#<?php echo $id ?>'), 0, 0, 2, 3, false, null, null, null, null, '<?php echo $id ?>');
</script>

	
