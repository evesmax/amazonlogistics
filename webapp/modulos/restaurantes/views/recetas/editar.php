<?php
// Valida que existan recetas o insumos preparados
	if (empty($datos)) { ?>
		<br /><br />
		<blockquote style="font-size: 16px">
			<p>
				No se encontraron <strong> Recetas</strong>
				o <strong>Insumos preparados</strong>
			</p>
		</blockquote><?php
		
		return 0;
	} ?>

<div class="row">
	<div class="col-xs-12">
		<table id="tabla_recetas_editar" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><strong>Nombre</strong></th>
					<th><strong>Insumos</strong></th>
					<th><strong>Insu. Preparados</strong></th>
					<th><strong>Costo</strong></th>
					<th><strong>Preparacion</strong></th>
					<th align="center"><strong><i class="fa fa-pencil"></i></strong></th>
				</tr>
			</thead>
			<tbody><?php
				foreach ($datos as $k => $v) {
				// Genera una cadena con los nombres de lo insumos
					$insumos='';
					foreach ($v['insumos'] as $key => $value) {
						$insumos.=$value['nombre'].', ';
					}
					$insumos = substr($insumos, 0,-2); 
					
				// Genera una cadena con los nombres de lo insumos preparados
					$insumos_preparados='';
					foreach ($v['insumos_preparados'] as $key => $value) {
						$insumos_preparados.=$value['nombre'].', ';
					}
					$insumos_preparados=substr($insumos_preparados, 0,-2);
				
				// Si es insumo preparado lo pinta de azul
					$clase = ($v['tipo_producto'] == 4) ? 'info' : '' ; ?>
					
					<tr class="<?php echo $clase ?>">
						<td><?php echo $v['nombre'] ?></td>
						<td><?php echo $insumos ?></td>
						<td><?php echo $insumos_preparados ?></td>
						<td><?php echo $v['costo'] ?></td>
						<td><?php echo $v['preparacion'] ?></td>
						<td align="center"><?php
							$proveedores_insumos = explode( ',', $v['proveedores_insumos']);
							foreach ($proveedores_insumos as $key => $value) {
								$v['insumos'][$key]['proveedor_select'] = $value;
							}
							$v['div']='div_insumos_agregados';
							$insumo=json_encode($v);
							$insumo=str_replace('"', "'", $insumo); ?>
							
							<button 
								id="btn_editar_<?php echo $v['idProducto'] ?>" 
								data-loading-text="<i class='fa fa-refresh fa-spin'></i>" 
								class="btn btn-primary btn-lg" 
								title="Editar" 
								onclick="$(this).attr('disabled', true); recetas.editar(<?php echo $insumo  ?>)">
								<i class="fa fa-pencil"></i>
							</button>
						</td>
					</tr><?php
				} ?>
			</tbody>
		</table>
	</div>
	<script>
		$(document).ready(function() {
			// recetas.convertir_dataTable({id: 'tabla_recetas_editar'});
		});
		// recetas.convertir_dataTable({id: 'tabla_recetas_editar'});
		
		// 2 -> El usuario no puede ordenar la tabla
		var ordenar = true;
		// Ordena la tabla(default desc 1ra columna)
		var $orden = 'desc';
		
		var botones = [
			{
				extend : 'excel',
				className : 'btn btn-success'
			}, {
				extend : 'print',
				className : 'btn btn-info'
			}
		];

		$('#tabla_recetas_editar').DataTable({				
				"deferRender":true,
				ordering: ordenar,
			    buttons: botones,
				dom : 'Bfrtip',
				language : {
					buttons : {
						print : "<i class='fa fa-print'></i> Imprimir",
						excel : "<i class='fa fa-file-excel-o'></i> Exportar",
					},			
					search : "<i class=\"fa fa-search\"></i>",
					lengthMenu : "_MENU_ por pagina",
					zeroRecords : "No hay datos.",
					infoEmpty : "No hay datos que mostrar.",
					info : " ",
					infoFiltered : " -> <strong> _TOTAL_ </strong> resultados encontrados",
					paginate : {
						first : "Primero",
						previous : "<<",
						next : ">>",
						last : "Último"
					}
				},
				order: [[0, $orden]]
			});
	</script>
</div> 