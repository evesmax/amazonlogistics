<script src="js/acciones/accion20.js" type="text/javascript"></script>

<div id="block_paso20" class="col-sm-8" >
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Registro de producto a empaque
		</div>
		<div class="panel-body"  style="font-size:12px;">
			
				
					<div id="guardar_block20" class="row col-sm-8" style="margin-top: 10px;">
						<div class="col-sm-12 alert alert-success">
							Cantidad por empaque: <b><?php echo $campos['cantidadxempaque']; ?></b><br>
							Usted podra fabricar <b><?php echo $paquetes; ?></b> empaques!.
							
						</div>
					</div>
					<br><br><br><br><br>
					<?php if($empacadosOrden+1 <= $paquetes){?>
					<div class="panel panel-primary" style="width: 62.5%" >
						<div class="panel-heading">
							Pesado de empaques
						</div>
						<div class="panel-body">
							<h4>Empaque No.<?php echo $empacadosOrden+1;?></h4>
		
							<div class="col-sm-6">
								Peso:
								<input type="text" id="numpeso" class="form-control" readonly="" />
							</div>
		
						</div>
					</div>
					<div class="row col-sm-3">
						<br>
						<button id="peso"  class="btn btn-primary btn-sm btn-block" onclick="solicitarpeso()">
							Pesar
						</button>
					</div>
					<div class="col-sm-3">
						<br>
						<button id="guardarpeso"  class="btn btn-success btn-sm btn-block" onclick="guardarpeso(<?php echo $empacadosOrden+1;?>,<?php echo $_REQUEST['idop'];?>,<?php echo $campos['cantidadxempaque']; ?>)">
							Guardar Peso
						</button>
		
					</div>
				    <?php }else {?>
				    
				    <div class="col-sm-7 alert alert-warning">
				    		Ya termino de Empacar!<br>
				    	</div>
				    	<?php if ($sobrante>0){?>
				    		<div class="col-sm-7 alert alert-danger">
				    			Usted tiene <b style="font-size: 14px;"><?php echo $sobrante;?> Piezas</b> sobrantes en esta orden! Marque el almacén a donde irán los productos sobrantes<br>
				    			<select id="sobrante" class="form-control"   >
				    				<?php 
				    				while($a = $almacenes->fetch_object()){?>
				    					<option value="<?php echo $a->id;?>"><?php echo $a->nombre;?></option>
				    				<?php  } ?>
				    			</select>
				    			
				    		</div>
				    		<?php } ?>
				    		
				    <div class="col-sm-12"> 
				    	
				    	<div class="col-sm-3">
							<button id="finalizar"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion20(<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>,<?php echo $sobrante;?>)">
								Finalizar
							</button>
						</div>
					</div>
				    <?php }?>
					


					
				
			

		</div>
	</div>

</div>