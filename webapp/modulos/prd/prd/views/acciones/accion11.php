<script src="js/acciones/accion11.js" type="text/javascript"></script>

<div id="block_paso11" class="col-sm-8">
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Envio material a proceso
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<?php $arrayreabasto = Array();
					if($autotodas>0){?>
					<div class="alert alert-warning" role="alert">
					  <b style="color:red;"><strong>Reabasto Autorizado!</strong> Los insumos seran agregados a la cantidad original.</b>
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					    <span aria-hidden="true">&times;</span>
					  </button><br>
					  <div style="overflow: scroll;height: 100px;">
					  	<dl>
 						<?php 
							while($r = $autotodas->fetch_object()) {
								$arrayreabasto[$r->id_matp][$r->id_insumo]+=$r->cantidad ;
						?>
								<dt><?php  echo $r->nombreEmpleado;?> (<b style="color: #337AB7"> Envio #ID: <?php echo $r->id_matp; ?></b> )</dt>
  								<dd><?php echo $r->cantidad . " = " .$r->prd;?></dd>
 						<?php }
					  	?>
					  	</dl>
					  </div>
					</div>
					<?php } ?>
					<div id="insumos_block11" class="col-sm-12 p0" style="margin-top: 10px;">
						
						<div class="col-sm-6" style="margin-top: 10px;">
							<b>Producto</b>
						</div>
						<div class="col-sm-3" style="margin-top: 10px;">
							<b>Cantidad insumos</b>
						</div>
						<div class="col-sm-3" style="margin-top: 10px;">
							<b>Cantidad utilizada</b>
						</div>
						<?php foreach ($resInsumos['rows'] as $key => $v) {?>
						<div class="col-sm-6" style="margin-top: 10px;">
                            <?php echo $v['nombre']; ?>
                        </div>
                        <div class="col-sm-3" style="margin-top: 10px;">
                            <input existen11="<?php echo $v['existen']; ?>" id="b11_<?php echo $_REQUEST['idop'] . '_' . $v['idProducto']; ?>"  readonly type="text" name="" value="<?php echo($v['cantproceso'] * $v['totaldeproduct']); ?>" class="form-control b11">
                        </div>
                        <div class="col-sm-3" style="margin-top: 10px;">
                            <input existen11="<?php echo $v['existen']; ?>" id="b11u_<?php echo $_REQUEST['idop'] . '_' . $v['idProducto']; ?>"  readonly type="text" name="" value="<?php echo $v['usados']; ?>" class="form-control b11u">
                        </div>
						<?php }if($wed!=0){ $x=1; $obj=array();?>
						<div class="col-sm-12" style="background-color:#f7f7f7;margin-top:30px; padding-bottom:10px; font-size:11px;">
                        	<div class="col-sm-12" style="margin-top: 10px; margin-bottom:10px; font-size:12px;"><b>Historial</b></div>
						<?php foreach ($wed as $key => $v) {
							if(array_key_exists($v['id'],$obj)){ ?>
								<div class="col-sm-6" style="margin-top: 10px;">
	                                  <?php echo $v['nombre']; ?>
	                            </div>
                             	<div class="col-sm-3" style="margin-top: 10px;">
                                       <?php echo "<b>".$v['cantidad']."</b>";
									    if($arrayreabasto[$v['id']][$v['idProducto']]){
                                    		echo " Reabasto :" .  $arrayreabasto[$v['id']][$v['idProducto']];
                                    	}?>
                              	</div>
                              	  <!-- REABASTOOOO -->
                              	<div class="col-sm-3 " style="margin-top: 10px;">
                                    <input style="display: none" onkeyup="reabastoentrada(this.value, <?php echo $v['cantidad']; ?>,<?php echo $v['idProducto']; ?>)"   id="rea_<?php echo $v['idProducto']; ?>"   type="text" name="" value="" class="form-control reabasto<?php echo $v['id']; ?> montoreabasto<?php echo $v['id']; ?>">
                                </div>
                              
                                 <div class="col-sm-9 reabasto<?php echo $v['id']; ?>" style="margin-top: 10px;display: none">
                                    <b>Observaciones</b>
									<textarea id="reabastoobs<?php echo $v['id']; ?>" placeholder="Describa los detalles de reabasto" class="form-control"></textarea>
                               	 </div>
                               	 <div class="col-sm-3 reabasto<?php echo $v['id']; ?>" style="margin-top: 10px;display: none">
									<button id="pedirreabasto<?php echo $v['id']; ?>" style="margin-top: 20px;" onclick="pedirreabasto(<?php echo $_REQUEST['idop'];?>,<?php echo $_REQUEST['idap'];?>,<?php echo $v['id']; ?>,<?php echo $v['idOperador'];?>)" class="btn btn-info btn-sm ">Pedir</button>
                                </div>
                                 <!-- REABASTOOOO -->
                  	 <?php   }else{ ?>
                              	<div class="col-sm-12" style="padding:0px;margin-top: 10px;">
                               		<div class="col-sm-6" style="background-color:#ffffff;">
                                    	<b>Operador:</b>
                                	</div>
                                	<div class="col-sm-3" style="background-color:#ffffff;">
                                    <?php echo $v['nombreemp']; ?>
                                	</div>
                                	<!-- BOTON DE REABASTO -->
                                	<div class="col-sm-3" style="background-color:#ffffff;">
                                		<?php if( $config['reabasto_insumos'] == 1){?>
										<button id="reabasto<?php echo $v['id']; ?>" style="margin-top:-3px;" onclick="reabasto(1,<?php echo $v['id']; ?>)" class="btn btn-info btn-xs ">Solicitar insumos</button>
										<?php } ?>
                           				</div>
                           			</div>
                           			  <!-- REABASTOOOO -->
                           	 	<input type="hidden" class="ppfrepor" value="<?php echo $v['cantppf']; ?>"/>
                            
								<div class="col-sm-12" style="padding:0px;margin-top: 10px;">
                                    <div class="col-sm-6" style=" height:22px; padding-top:3px;">
                                        <b>Proceso:<?php echo $x; ?> <b style="color: #337AB7">#ID:<?php echo $v['id'];?></b> Cantidad PPF <?php echo $v['cantppf']; ?></b>
                                    </div>
                                    <div class="col-sm-6" style=" height:22px; padding-top:3px;">
                                        <b>Inicio:</b> <?php $v['f_ini']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <?php if($v['f_fin']==0){ ?>
					                                   <button style="margin-top:-3px;" id="ff_<?php echo $v['id']; ?>" onclick="finalizar(<?php echo $v['id']; ?>,<?php echo $_REQUEST['idop']; ?>);" class="btn btn-primary btn-xs finaliza">Finalizar</button>
					                    <?php }else{ ?>
					                                   <b>Fin :</b> <?php echo $v['f_fin']; ?>
					                    <?php     } ?>
                                       
                                    </div>
                      			</div>
                      			<div class="col-sm-6" style="margin-top: 10px;">
                                    <b>Producto</b>
                                </div>
                                <div class="col-sm-3" style="margin-top: 10px;">
                                    <b>Cantidad utilizada</b>
                                </div>
                                  <!-- REABASTOOOO -->
                                <div class="col-sm-3 " style="margin-top: 10px;">
                                    <b class="reabasto<?php echo $v['id']; ?>" style="display: none;">Cantidad solicitada</b>
                                </div>
  									<!--fin  REABASTOOOO -->
                               <div class="col-sm-6" style="margin-top: 10px;">
                                    <?php echo $v['nombre']; ?>
                                </div>
                                <div class="col-sm-3" style="margin-top: 10px;">
                                    <?php echo "<b>".$v['cantidad']."</b>";
                                    if($arrayreabasto[$v['id']][$v['idProducto']]){
                                    	echo " Reabasto :" .  $arrayreabasto[$v['id']][$v['idProducto']];
                                    }?>
                                </div>
                                  <!-- REABASTOOOO -->
                                <div class="col-sm-3 reabasto<?php echo $v['id']; ?>" style="margin-top: 10px;">
                                    <input style="display: none" onkeyup="reabastoentrada(this.value, <?php echo $v['cantidad']; ?>,<?php echo $v['idProducto']; ?>)"   id="rea_<?php echo $v['idProducto']; ?>"   type="text" name="" value="" class="form-control reabasto<?php echo $v['id']; ?> montoreabasto<?php echo $v['id']; ?>">
                                </div>
                                  <!--fin  REABASTOOOO -->
								
	
				<?php		$x++;
		$obj[$v['id']] = 1;//hacemos esto porq el query trae la info de los isnumos pero el mismo id entonces comprobamos si seguimos en el mismo para solo pintar la cantidad y no todo la info
								}//else
								?>
						<?php	} //foreach?> 					
							</div>
			<?php	}//if ?>
					</div>

                    <div class="col-sm-12" style="margin-top: 30px; font-size:12px;"><b>Personal utilizado</b></div>

		<?php		if($rsqlpaso4->num_rows>0){?>
         			<div id="lose" class="col-sm-12">
          				<select onchange="agre(<?php echo $_REQUEST['idap'];?>)" id="mmm_<?php echo $_REQUEST['idap'];?>" class="form-control"  >
		  					<option value="0">-Seleccione-</option>
         			<?php	while ($rowSqlpaso4 = $rsqlpaso4->fetch_assoc()) {?>
           					<option  value="<?php echo$rowSqlpaso4['idEmpleado'];?>"><?php echo $rowSqlpaso4['nombre'];?></option>
          			<?php	} ?>
          				</select>
          			</div>
       <?php 		} 

				foreach ($resInsumos['rows'] as $key => $v) {?>
                     <div class="lalala">
                          <div class="col-sm-6" style="margin-top: 10px;">
                                <?php echo $v['nombre'];?>
                           </div>
                            <div class="col-sm-6" style="margin-top: 10px;">
                                <input readonly="" existen11="<?php echo $v['existen'];?>" id="b11i_<?php echo $_REQUEST['idop'].'_'.$v['idProducto'];?>" data-value="<?php echo $v['cantproceso'];?>" type="text" name="" value="0" class="form-control insumosf">
                            </div>
                       </div>

                 <?php } ?>

					<div id="guardar_block11" class="col-sm-12 p0" style="margin-top: 10px;">

					</div>
				</div>
			</div>

		</div>
	</div>

</div>
<script>
	txtbtn = 'Iniciar proceso';

//envio mat
var arraycantinsumos = 0;
var arraycantinutilizados = 0;
$('.b11').each(function() {
	if ($(this).val() > 0) {
		arraycantinsumos += parseFloat($(this).val());
	}
});
$('.b11u').each(function() {
	if ($(this).val() > 0) {
		arraycantinutilizados += parseFloat($(this).val());
	}
});

if (arraycantinsumos != arraycantinutilizados) {
	txtbtn = 'Iniciar otro proceso';
} else {
	txtbtn = "Guardar";
}
$('#guardar_block11').html('<div class="col-sm-3"><button id="save_block11"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion17(<?php echo $_REQUEST['accion'];?>, <?php echo $_REQUEST['idop'];?>,<?php echo $_REQUEST['paso'];?>,<?php echo $_REQUEST['idap'];?>,<?php echo $_REQUEST['idp'];?>)">' + txtbtn + '</button></div>');

</script>