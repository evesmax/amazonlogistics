<?php
// Valida que existan actividades
	if (empty($utilidades)) {?>
		<div align="center">
			<h3><span class="label label-default">* No se detecto informacion *</span></h3>
		</div><?php
		
		return 0;
	} ?>

<div class="row">
                    <div class="col-sm-12">
                        <div class="panel-group" id="accordion_graficas" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div hrefer class="panel-heading" id="heading_graficas" role="tab" role="button" style="cursor: pointer" data-toggle="collapse" data-parent="#accordion_graficas" href="#tab_graficas" aria-controls="collapse_graficas" aria-expanded="true">
                                <h4 class="panel-title">
                                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                                    <strong>Graficas</strong> 
                                </h4>
                            </div>
                            <div id="tab_graficas" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_graficas" >
                                <div class="panel-body" >
                                    <div id="contProducts" style="height:300px;overflow:auto;" class="col-sm-12">
										<div id="grafica_utilidades_dona" class="col-xs-4" style="height: 100%">
											<!-- En esta div se carga la grafica de dona -->
										</div>
										<div id="grafica_utilidades_lineal" class="col-xs-8" style="height: 100%">
											<!-- En esta div se carga la grafica de barras -->
										</div>
										
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

</div>
<div class="row">
	<div class="col-md-5"></div>
	<div class="col-md-3" id="total" align="left">
	<div class="col-md-4"></div>
		<!-- En esta div se carga el total de utilidad -->
	</div>
</div><br />
<table id="tabla_utilidades" class="table table-striped table-bordered" cellspacing="0" width="100%">
	<thead>
		<tr>
			<td align="center"><strong>Cantidad</strong></td>
			<td ><strong>Sucursal</strong></td>
			<td ><strong>Producto</strong></td>
			<td align="center"><strong>Precio</strong></td>
			<td align="center"><strong>Costo</strong></td>
			<td align="center"><strong>Total venta</strong></td>
			<td align="center"><strong>Total costo</strong></td>
			<td align="center"><strong>Ganancia</strong></td>
			<td align="center"><strong>%</strong></td>
		</tr>
	</thead>
	<tbody><?php

	// $utilidades es un array que viene desde el controlador
		foreach ($utilidades as $key => $value) {
			$total += $value['ganancia']*1;  
			if($value['costo'] > 0){ /// evita mostrar costo en 0
		?>
		
			<tr>
				<td align="center"><?php echo $value['ventas'] ?></td>
				<td><?php echo $value['sucursal'] ?></td>
				<td><?php echo $value['nombre'] ?></td>
				<td align="center"><?php echo $value['precio'] ?></td>
				<td align="center"><?php echo $value['costo'] ?></td>
				<td align="center"><?php echo $value['venta_total'] ?></td>
				<td align="center"><?php echo $value['costo_total'] ?></td>
				<td align="center"><?php echo $value['ganancia'] ?></td>
				<td align="center"><?php echo $value['porcentaje'] ?></td>
			</tr> <?php
			}
		} ?>
	</tbody>
	<input type="hidden" id="totalG" value="<?php echo number_format($total,2);  ?>">
</table>

<script>
	var total = $("#totalG").val();	
	$("#total").html('<h4>Total: $<strong>'+total+'</strong><h4>');
	
	comandas.graficar({
		div:'grafica_utilidades', 
		x:'fecha', 
		y:'ventas', 
		label:'Ventas', 
		dona:<?php echo json_encode($dona) ?>, 
		lineal:<?php echo json_encode($lineal) ?>
	});
</script>