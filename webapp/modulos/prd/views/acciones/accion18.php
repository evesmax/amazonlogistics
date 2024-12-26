<script src="js/acciones/accion18.js" type="text/javascript"></script>

<div id="block_paso18" class="col-sm-8">
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Envio material a proceso variable
			<?php if( $config['reabasto_insumos'] == 1){?>
				<button id="reabasto" style="width: 115px" onclick="reabasto(1)" class="btn btn-primary btn-sm ">Solicitar insumos</button>
			<?php } ?>
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<?php $arrayreabasto = Array();
					if($autoRebasto>0){?>
					<div class="alert alert-warning" role="alert">
					  <b style="color:red;"><strong>Reabasto Autorizado!</strong> Los insumos seran agregados a la cantidad original.</b>
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
					    <span aria-hidden="true">&times;</span>
					  </button><br>
					  <div style="overflow: scroll;height: 100px;">
					  	<dl>
 						<?php 
							while($r = $autoRebasto->fetch_object()) {
								$arrayreabasto[$r->id_insumo]+=$r->cantidad ;
						?>
								<dt><?php  echo $r->nombreEmpleado;?></dt>
  								<dd><?php echo $r->cantidad . " = " .$r->prd;?></dd>
 						<?php }
					  	?>
					  	</dl>
					  </div>
					</div>
					<?php } ?>
					<div id="insumos_block18" class="col-sm-12 p0" style="">
						<input type="hidden" id="masprocesos" value="<?php echo $existepaso;?>"/>
						<div class="col-sm-3" style="margin-top: 10px;">
							<b>Producto</b>
						</div>
						<div class="col-sm-3 " style="margin-top: 10px;">
							<b style="display: none;" class="reabasto">Cantidad solicitada</b>
						</div>
						<div class="col-sm-3" style="margin-top: 10px;">
							<b>Cantidad insumos</b>
						</div>
						<div class="col-sm-3" style="margin-top: 10px;">
							<b>Cantidad utilizada</b>
						</div>
						<?php foreach ($resInsumos['rows'] as $key => $v) {?>
						<div class="col-sm-3" style="margin-top: 10px;">
                            <?php echo $v['nombre']; ?>
                        </div>
                        <div class="col-sm-3 " style="margin-top: 10px;">
                            <input style="display: none;" onkeyup="reabastoentrada(this.value,<?php echo($v['canti'] * $v['totaldeproduct']); ?>,'<?php echo $_REQUEST['idop'] . '_' . $v['idProducto']; ?>')"  existen11="<?php echo $v['existen']; ?>" id="b19_<?php echo $_REQUEST['idop'] . '_' . $v['idProducto']; ?>"   type="text" name="" value="" class="form-control montoreabasto reabasto">
                        </div>
                        <div class="col-sm-3" style="margin-top: 10px;">
                            <input existen11="<?php echo $v['existen']; ?>" id="b11_<?php echo $_REQUEST['idop'] . '_' . $v['idProducto']; ?>"  readonly type="text" name="" value="<?php echo($v['canti'] + $arrayreabasto[$v['idProducto']]); ?>" class="form-control b11">
                        </div>
                        <div class="col-sm-3" style="margin-top: 10px;">
                            <input existen11="<?php echo $v['existen']; ?>" id="b11u_<?php echo $_REQUEST['idop'] . '_' . $v['idProducto']; ?>"  readonly type="text" name="" value="<?php echo $v['usados']; ?>" class="form-control b11u">
                        </div>
						<?php }?>
						<!-- reabasto -->
						<div class="col-sm-12 reabasto" style="margin-top: 10px;display: none;">
							<hr>
							<div  class="col-sm-6"> <b>Solicitante/Operador</b>
							<?php if($solicitante->num_rows>0){?>
				         			
				          				<select  id="solicitante" class="form-control"  >
						  					<option value="0">-Seleccione-</option>
				         			<?php	while ($rowSqlpaso4 = $solicitante->fetch_assoc()) {?>
				           					<option  value="<?php echo $rowSqlpaso4['idEmpleado'];?>"><?php echo $rowSqlpaso4['nombre'];?></option>
				          			<?php	} ?>
				          				</select>
				          			
				       <?php 		} ?>
				       </div>
				       		<div  class="col-sm-6">
								<b>Observaciones</b>
								<textarea id="reabastoobs" placeholder="Describa los detalles de reabasto" class="form-control"></textarea>
	                       		
							</div>
							<div class="col-md-12">
								<button id="pedirreabasto" style="width: 90px;margin-top: 10px;" onclick="pedirreabasto(<?php echo $_REQUEST['idop'];?>,<?php echo $_REQUEST['idap'];?>)" class="btn btn-info btn-sm ">Pedir</button>
								<hr>
							</div>
							
                        </div>
						
						<?php if($wed!=0){ $x=1; $obj=array();?>
						<div class="col-sm-12" style="background-color:#f7f7f7;margin-top:30px; padding-bottom:10px; font-size:11px;">
                        	<div class="col-sm-12" style="margin-top: 10px; margin-bottom:10px; font-size:12px;"><b>Historial</b></div>
						<?php foreach ($wed as $key => $v) {
							if(array_key_exists($v['id'],$obj)){ ?>
								<div class="col-sm-6" style="margin-top: 10px;">
	                                  <?php echo $v['nombre']; ?>
	                            </div>
                             	<div class="col-sm-6" style="margin-top: 10px;">
                                       <?php echo $v['cantidad']; ?>
                              	</div>
                  	 <?php   }else{ ?>
                              	<div class="col-sm-12" style="padding:0px;margin-top: 10px;">
                               		<div class="col-sm-6" style="background-color:#ffffff;">
                                    	<b>Operador:</b>
                                	</div>
                                	<div class="col-sm-6" style="background-color:#ffffff;">
                                    <?php echo $v['nombreemp']; ?>
                                	</div>
                           		</div>
                           	 	<input type="hidden" class="ppfrepor" value="<?php echo $v['cantppf']; ?>"/>
                            
								<div class="col-sm-12" style="padding:0px;margin-top: 10px;">
                                    <div class="col-sm-6" style=" height:22px; padding-top:3px;">
                                        <b>Proceso <?php echo $x; ?>:</b>
                                    </div>
                                    <div class="col-sm-6" style=" height:22px; padding-top:3px;">
                                        <b>Inicio:</b> <?php $v['f_ini']; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                         <?php if($v['f_fin']==0){ ?>
					                                   <button style="margin-top:-3px;" id="ff_<?php echo $v['id']; ?>" onclick="finalizar18(<?php echo $v['id']; ?>,<?php echo $_REQUEST['idop']; ?>);" class="btn btn-primary btn-xs finaliza">Finalizar</button>
					                    <?php }else{ ?>
					                                   <b>Fin :</b> <?php echo $v['f_fin']; ?>
					                    <?php     } ?>
                                       
                                    </div>
                      			</div>
                      			<div class="col-sm-6" style="margin-top: 10px;">
                                    <b>Producto</b>
                                </div>
                                <div class="col-sm-6" style="margin-top: 10px;">
                                    <b>Cantidad utilizada</b>
                                </div>

                               <div class="col-sm-6" style="margin-top: 10px;">
                                    <?php echo $v['nombre']; ?>
                                </div>
                                <div class="col-sm-6" style="margin-top: 10px;">
                                    <?php echo $v['cantidad']; ?>
                                </div>
	
	
	
	
				<?php		$x++;
		$obj[$v['id']] = 1;//hacemos esto porq el query trae la info de los isnumos pero el mismo id entonces comprobamos si seguimos en el mismo para solo pintar la cantidad y no todo la info
								}//else
							} //foreach?> 					
							</div>
			<?php	}//if ?>
					</div>

                    <div class="col-sm-12" style="margin-top: 10px; margin-bottom:10px; font-size:12px;"><b>Personal utilizado</b></div>

		<?php		if($rsqlpaso4->num_rows>0){?>
         			<div id="lose" class="col-sm-12">
          				<select  id="mmm_<?php echo $_REQUEST['idap'];?>" class="form-control"  >
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
                            	<input onkeyup=topexinsumo(<?php echo ($v['canti']+$arrayreabasto[$v['idProducto']]);?>,<?php echo $v['usados'];?>,this.value,'b11i_<?php echo $_REQUEST['idop'].'_'.$v['idProducto'];?>');  existen11="<?php echo $v['existen'];?>" id="b11i_<?php echo $_REQUEST['idop'].'_'.$v['idProducto'];?>" data-value="<?php echo $v['cantproceso'];?>" type="text"  value="0" class="form-control insumosf">
                            </div>
                       </div>

                 <?php } ?>

					<div id="guardar_block18" class="col-sm-12 p0" style="margin-top: 10px;">

					</div>
				</div>
			</div>

		</div>
	</div>

</div>
<script>
txtbtn = 'Iniciar proceso';

//envio mat
var  arraycantinsumos = 0;
var arraycantinutilizados  = 0;
$('.b11').each(function() { 
	if($(this).val()>0){
		arraycantinsumos += parseFloat($(this).val());
	}
});
$('.b11u').each(function() {
	if($(this).val()>0){
		arraycantinutilizados +=parseFloat($(this).val());
	}
});

if(arraycantinsumos!=arraycantinutilizados){
    txtbtn='Iniciar otro proceso';
}else{
	txtbtn = "Guardar";
}

$('#guardar_block18').html('<div class=""><button id="save_block18"  class="btn btn-primary btn-sm" onclick="savePasoAccion18(<?php echo $_REQUEST['accion'];?>, <?php echo $_REQUEST['idop'];?>,<?php echo $_REQUEST['paso'];?>,<?php echo $_REQUEST['idap'];?>,<?php echo $_REQUEST['idp'];?>)">' + txtbtn + '</button></div>');
                        //quiere decir que hay otra accion 18 y ahi podra continuar si quiere
if($("#masprocesos").val()>0 && txtbtn!="Guardar"){
	 $('#guardar_block18').append('<div class=""><br><hr><button id="save_block18"  class="btn btn-success btn-sm" onclick="savePasoAccion18(<?php echo $_REQUEST['accion'];?>, <?php echo $_REQUEST['idop'];?>,<?php echo $_REQUEST['paso'];?>,<?php echo $_REQUEST['idap'];?>,<?php echo $_REQUEST['idp'];?>,1)">Continuar en otro envio</button></div>');

}

</script>