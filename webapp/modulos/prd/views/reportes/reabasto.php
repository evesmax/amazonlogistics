<!DOCTYPE >
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script src="js/reportes/reabasto.js" type="text/javascript"></script>

</head>
<style>
	.loader {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
}

</style>
<body>
	<br>
	<div class="container well" style="padding:25px;margin-bottom: 150px;">
		<div class="row" style="padding-bottom:20px;">
			<div class="col-xs-12 col-md-12" style="padding:0px 0px 0px 3px;">
				<h3>Reabasto de ordenes de produccion</h3>
			</div>
		</div>
		<div id="lista_abasto" class="row" style="display:block;margin-top:20px;font-size:12px;">
			<table id="tabla_abasto" class="table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="6%">No.Reabasto</th>
						<th width="7%">No.Orden</th>
						<th width="25%">Operador</th>
						<th width="25%">Obervaciones</th>
						<th width="25%">Para fabricar</th>
						<th width="7%">Cantidad</th>
						<th width="7%">Estatus</th>
						<th width="10%">Insumos(Autorizacion)</th>
						<th width="20%"></th>
					</tr>
				</thead>
				<tbody id="contenidoabasto">
					 <?php
					 if($lista->num_rows>0){
                    while($datos = $lista->fetch_object()){?>
                    	<tr>
                    		<td><?php echo $datos -> id; ?></td>
                    		<td><?php echo $datos -> id_oproduccion; ?></td>
                    		<td><?php echo strtoupper($datos -> nombreEmpleado); ?></td>
                    		<td><?php echo($datos -> observaciones); ?></td>
                    		<td><?php echo $datos -> nombreprd; ?></td>
                    		<td><?php echo $datos -> cantidad; ?></td>
                    		<?php 
                    		if($datos->estatus==1){?>
                    		<td align="left">
                    			 	<span class="label label-danger" style="cursor:pointer;">Sin autorizar</span>
                    		</td>
                    		 <td align="center"><button type="button" onclick="verInsumos(<?php echo $datos -> id_oproduccion; ?>,'<?php echo $datos -> nombreprd; ?>',<?php echo $datos -> cantidad; ?>,1,<?php echo $datos -> id; ?>)" class="btn btn-warning btn-xs "><label class="kr">Ver</label></button></td>
                    		<td>
                    			<button class="btn btn-danger btn-xs" onclick="cancelar(<?php echo $datos -> id_oproduccion; ?>)" id="cancelar<?php echo $datos -> id_oproduccion; ?>"><span class="glyphicon glyphicon-remove"></span>Cancelar</button>
                    		</td>
							<?php } if($datos->estatus==2){ ?>
								<td align="left">
							 	<span class="label label-success" style="cursor:pointer;">Autorizada</span>
							 </td>
							 <td align="center"><button type="button" onclick="verInsumos(<?php echo $datos -> id_oproduccion; ?>,'<?php echo $datos -> nombreprd; ?>',<?php echo $datos -> cantidad; ?>,0,<?php echo $datos -> id; ?>)" class="btn btn-warning btn-xs "><label class="kr">Ver</label></button></td>
							 <td></td>
							<?php } if($datos->estatus==3){ ?>
								<td align="left">
           						<span class="label label-default" style="cursor:pointer;">Cancelada</span>
           					</td>
           					<td align="center"><button type="button" onclick="verInsumos(<?php echo $datos -> id_oproduccion; ?>,'<?php echo $datos -> nombreprd; ?>',<?php echo $datos -> cantidad; ?>,0,<?php echo $datos -> id; ?>)" class="btn btn-warning btn-xs "><label class="kr">Ver</label></button></td>
							<td></td>
          					<?php
							}
							}
						}else{?>
							<tr><td colspan="9" align="center">No tiene ninguna solicitud de reabasto</td></tr>
						<?php }?>
							
				</tbody>
			</table>
		</div>

	</div>
	<div id="vistainsumos" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content panel-success">
				<div class="modal-header panel-heading">
					<h4 id="modal-label" id="nombreprd"></h4>
				</div>
				<div class="modal-body" id="contenidoinsu" align="center">
					 
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						<span class="glyphicon glyphicon-remove"></span>
						<span class="hidden-xs">Cerrar</span>
					</button>

				</div>
			</div>
		</div>
	</div>
</body>
</html>