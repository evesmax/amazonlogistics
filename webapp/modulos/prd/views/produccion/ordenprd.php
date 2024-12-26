	<script src="js/produccion/ordenproduccion.js" type="text/javascript"></script>
<div id="nreq" class="row" >
    	<div class="panel panel-default">
        	<div id="ph"  class="panel-heading"><span opt="1" class="label label-primary" style="cursor:pointer;">Nueva Orden de produccion</span></div>
                <div class="panel-body" style="padding:15px 0 10px 0; font-size:12px;">
                    <div class="col-sm-12">
                    	<div class="form-group">
                        	<label class="col-sm-2 control-label text-left">Usuario:</label>
	                        <div class="col-sm-10" style="color:#096;">
	                            <label id="userlog"><?php echo $username; ?></label>
	                            <input type='hidden' id="iduserlog" value='<?php echo $iduser; ?>'>
	                        </div>
                    	</div>
                    </div>
                    
                  	<div class="col-sm-12" style="padding-top:10px;">
						<div class="form-group">
							<input type="hidden" id="regordenp"/>
							<label class="col-sm-2 control-label text-left simple">No. Orden</label>
							<div id="txt_nreq" class="col-sm-2 simple" style="color:#ff0000;"></div>
							<label class="col-sm-2 control-label text-left simple">Fecha registro</label>
							<div id="fechahoy" class="col-sm-2 simple" >
								<input style="height:30px;width:100%" id="date_hoy" type="text" class="form-control">
							</div>
							<label class="col-sm-2 control-label text-left simple">Fecha entrega</label>
							<div class="col-sm-2 text-left simple">
								<input style="height:30px;width:100%" id="date_entrega" type="text" class="form-control">
							</div>
						</div>
					</div>

                    <div class="col-sm-12 simple" style="padding-top:10px;">
                    	<div class="form-group">
                        	<label class="col-sm-2 control-label text-left">Prioridad</label>
                        	<div class="col-sm-2" style="color:#ff0000;">
	                            <select id="c_prioridad"  style="width:100%;">
	                                <option value="0">Seleccione</option>
	                                    <option value="1">Alta</option>
	                                    <option value="2">Baja</option>
	                            </select>
                        	</div>
                        	<label class="col-sm-2 control-label text-left">Sucursal</label>
                        	<div class="col-sm-2" style="color:#ff0000;">
                            	<select id="c_sucursal"  style="width:100%;">
                            		<?php if($sucursales==0){ ?>
                            		<option value="0">Seleccione</option>
                                	<option value="0">No hay sucursales</option>
                            		<?php }else{ ?>
                                	<option value="0">Seleccione</option>
                              		<?php foreach ($sucursales as $k => $v) { ?>
                                    <option value="<?php echo $v['idSuc']; ?>"><?php echo $v['nombre']; ?></option>
                                	<?php }} ?>
                            	</select>
                        	</div>
                       		<label class="col-sm-2 control-label text-left">Solicitante</label>
                          	<div class="col-sm-2" style="color:#ff0000;">
	                            <select id="c_solicitante" style="width:100%;">
	                                <option value="0">Seleccione</option>
	                                <?php foreach ($empleados as $k => $v) { ?>
	                                    <option area="<?php echo $v['nomarea']; ?>" value="<?php echo $v['idempleado']; ?>"><?php echo $v['nombre']; ?> (<?php echo $v['nomarea']; ?>)</option>
	                                <?php } ?>
	                            </select>
                        	</div>

	                        <div class="col-sm-2">
	                        	<input type="text" id="moneda_tc"  placeholder="Tipo de cambio" style="display:none;height:28px;">
	                        </div>

                   	 </div>
                 </div>

                 <div id="addprodoexplo" class="col-sm-12" style="padding-top:15px;">
                 	<div class="panel panel-default" style="border-radius:0px;">
                        <div class="panel-body" style="padding:15px 0 10px 0; font-size:12px;background-color:#f4f4f4;border:1px solid #fff;">
                            <div class="col-sm-12 p0">
                            	<div id="panelprod" class="form-group">
                            	 	<label class="col-sm-1 control-label text-left multip xlote">Lote:</label>
	                        		<div class="col-sm-2 multip xlote" style="color:#096;">
	                           			<input id="lote" class="form-control" type="text">
	                       		 	</div>
                                	<label class="col-sm-1 control-label text-left">Producto</label>
                               		<div class="col-sm-6" style="color:#ff0000;">
	                                    <select id="c_productos"  style="width:100%;">
	                                        <option value="0">Seleccione</option>
	                                        <?php foreach ($productos as $k => $v) { ?>
	                                            <option value="<?php echo $v['id']; ?>"><?php echo $v['nombre']; ?></option>
	                                        <?php } ?>
	                                    </select>
                                	</div>
			                    
	                                <div class="col-sm-2 text-left" >
	                                    <button id="btn_addProd"  class="btn btn-default btn-sm btn-block"><span class="glyphicon glyphicon-plus"></span> Agregar producto</button>
	                                </div>
                            	</div>
                            </div>
                        </div>
                  	</div>
               	</div>

               	<div id="addprodoexplo2" class="col-sm-12" style="padding-top:30px;">
					<div class="panel panel-default" style="border-radius:0px;">
						<div class="panel-body" style="padding:15px 0 10px 0; font-size:12px;background-color:#f4f4f4;border:1px solid #fff;">
							<div class="col-sm-12 p0">
								<div id="panelprod" class="form-group">
									<label id='tit' class="col-sm-2 control-label text-left">Requisicion</label>
								</div>
							</div>
						</div>
					</div>
				</div>
                <div id="panel_tabla" class="col-sm-12" style="padding: 15px 37px 15px 31px; display:none;">
					<table width="100%" id="tablaprods" class="table table-hover">
						<thead>
							<tr>
								<th width="10%" align="left">Codigo</th>
								<th width="30%" align="left">Descripcion</th>
								<th width="10%" align="left">Unidad</th>
								<th width="10%" align="left">Cantidad</th>
								<th class="no-sort" width="15%" align="right">&nbsp;</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="3" style="text-align:right">&nbsp;</th>
								<th colspan="2"></th>
							</tr>
						</tfoot>
						<tbody id="filasprods"></tbody>
					</table>
				</div>

                <div id="panel_tabla2" class="col-sm-12" style="padding: 15px 37px 15px 31px; display:none;">
					<table width="100%" id="tablaprods2" class="table table-hover">
						<thead>
							<tr>
								<th width="10%" align="left">Codigo</th>
								<th width="30%" align="left">Descripcion</th>
								<th width="10%" align="left">Unidad</th>
								<th width="10%" align="left">Proveedor</th>
								<th width="10%" align="left">$Unitario</th>
								<th width="10%" align="left">Cantidad</th>
								<th width="10%" align="left">Existencias</th>
								<th width="10" align="left" class="text-right">Subtotal</th>
								<th class="no-sort" width="15%" align="right">&nbsp;</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="6" style="text-align:right">&nbsp;</th>
								<th colspan="2"></th>
							</tr>
						</tfoot>
						<tbody id="filasprods2"></tbody>
					</table>
					<div class="col-sm-12" style="margin: -30px 0 20px 0;">
						<div class="col-sm-10 text-right">
							<b>Total</b>
						</div>
						<div id="tttr" totlimpio="0.00" class="col-sm-2 text-right">
							0.00
						</div>
					</div>
				</div>

                <div class="col-sm-12" style="padding-top:10px;">
					<div class="form-group">
						<label class="col-sm-2 control-label text-left">Observaciones</label>
						<div class="col-sm-10" style="color:#ff0000;">
							<textarea class="form-control" rows="3" id="comment"></textarea>
						</div>
					</div>
				</div>

               <div class="col-sm-12" style="padding-top:10px;">
					<div class="form-group">
						<div class="col-sm-12 text-right">
							<input id="cadenaCoti" type="hidden" value="">
							<button id="btn_savequit_usar" class="btn btn-sm btn-primary pull-center" type="button" style="height:28px;">
								Utilizar insumos existentes
							</button>

							<button id="btn_savequit" class="btn btn-sm btn-success pull-center" type="button" style="height:28px;">
								Generar Orden de produccion
							</button>

						</div>
					</div>
				</div>
				
                <div id="error_1"></div>

                <div id="data_almacen" style="display:none;">
                    <select id="c_almacen" style="width:100px;">
                        <?php foreach ($empleados as $k => $v) { ?>
                            <option value="<?php echo $v['idempleado']; ?>"><?php echo $v['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>

            </div>
         </div>             
   </div>
