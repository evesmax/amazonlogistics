<table  cellspacing="2" cellpadding="2" width="100%" class="table-striped table-bordered" id="table">
	<thead >
			<tr style="background-color: #808080">
			<th><b>Producto</b></th>
			<th><b>Cantidad pedido</b></th>
			<th><b>Almacen</b></th>
			<th><b>Existencia</b></th>
			<th><b>Generar Orden Prd</b></th>
			<th><b>Accion</b></th>
			<th><b>Cant a producir</b></th>
			</tr>
		
	</thead>
	<tbody>
		<?php
		foreach ($_SESSION["caja"] as $key => $producto) {
				if($key!='cargos' && $key!='descGeneral' && $key!='descGeneralCant'){
					$producto = (object) $producto;
					$tipoprd = $this->pedidoModel->tipoPrd($producto->idProducto);
					if($tipoprd == 8 || $tipoprd == 9){?>
					<tr id="tr<?php echo $producto->idProducto;?>">
						<td>
							<input type="hidden" class="idsprd" value="<?php echo $producto->idProducto;?>" />
							<?php echo $producto->nombre;?>
						</td>
						<td align="center">
							<?php echo $producto->cantidad;?>
						</td>
						<td>
							<select class="form-control" onchange="almacenprd(this.value,<?php echo $producto->idProducto;?>,<?php echo $producto->cantidad;?>)" id="almacen<?php echo $producto->idProducto;?>">
								<option value="0">Seleccione</option>
								<?php
								foreach($arrayAlmacen as $key=>$a){?>
									<option value="<?php  echo $key; ?>"><?php echo $a;?></option>
								<?php
								}
								?>
							</select>
						</td>
						<td align="center" style="font-size: 14px;color: #" id="cant<?php echo $producto->idProducto;?>"></td>
						<td>
							<select class="form-control" onchange="generarord(<?php echo $producto->idProducto;?>,<?php echo $producto->cantidad;?>)" id="orden<?php echo $producto->idProducto;?>">
								<option value="1">SI</option>
								<option value="0">NO</option>
							</select>
						</td>
						<td>
							<select class="form-control" onchange="producircant(<?php echo $producto->cantidad;?>,<?php echo $producto->idProducto;?>)" id="como<?php echo $producto->idProducto;?>">
								<option value="2">Producir todo</option>
								<option value="1">Solo Faltante</option>
							</select>
						</td>
						<td id="canttotal<?php echo $producto->idProducto;?>" align="center" style="font-weight: bold;">
							<?php echo $producto->cantidad;?>
						</td>
						
					</tr>
				<?php
					}
				}
		}
		?>
	</tbody>
</table>
<script>
function guardarorden(){
	var obs = $('#txtareaObservaciones').val();
	var dataString = $('#fromExtra').serialize();
	idsProductos = $('.idsprd').map(function() {
			idprd = $(this).val();
			cant = $("#canttotal"+idprd).html();
			generaorden = $("#orden"+idprd).val();

				id = idprd + '>#' + cant + '>#' + generaorden;
			
			return id;
		}).get().join('___');
	
	$.ajax({
			url: 'ajax.php?c=caja&f=guardarCambiospedido',
			type: 'POST',
			data: {
				idsProductos:idsProductos
				},
				beforeSend: function() {
					caja.mensaje("Guardando Cambios");
				},
				success: function(resp) {
					caja.eliminaMensaje();
				
						alert('Cambios almacenados con exito.');
						$.ajax({
									url: 'ajax.php?c=caja&f=guardarPedido',
									type: 'POST',
									dataType: 'json',
									async: true,
									data: {
										idFact: $("#rfc").val(),
										documento: $("#documento").val(),
										cliente: $("#hidencliente-caja").val(),
										suspendida: $("#s_cliente").val(),
										comentario: $('#txtareacomentariosProducto').val(),
										moneda: $('#monedaVenta').val(),
										obs : obs,
										dataString : dataString
										},
										beforeSend: function() {
											caja.mensaje("Guardando Pedido");
										},
										success: function(resp) {
											console.log('----> success pedido');
											console.log(resp);
					
											caja.eliminaMensaje();
											if(resp.idPedido > 0){
												$("#vistaordenprd").hide();
												alert('Pedido realizado con exito.');
												window.location.reload();
											}
										
									},
									error: function(data) {
										caja.eliminaMensaje();
										alert(data.msg);
									}
								});
				    		
				    }
						
				});
					
			
	


}
	function almacenprd(val,idprd,cant){
		$.post("ajax.php?c=Pedido&f=existenciaPrd",{
			almacen:val,
			idprd:idprd
		},function (resp){
			$("#cant"+idprd).html(resp);
			
			generarord(idprd);
			producircant(cant,idprd);
		});
	}
	function producircant(cant,idprd){
		if($("#como"+idprd).val() == 1){
			if(!$("#cant"+idprd).html()){
				alert("Seleccione Almacen");
				return false;
			}
			var total = parseFloat(cant - $("#cant"+idprd).html());
			$("#canttotal"+idprd).html(total);
		}else{
			$("#canttotal"+idprd).html(cant);
		}
		
	}
	function generarord(idprd,cant){
		if($("#orden"+idprd).val()==0){
			$("#generar"+idprd).hide("slow");
		}else{
			$("#generar"+idprd).show("slow");
		}
		
	}
</script>