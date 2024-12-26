		<div id="contPerce" >
			<div id="imprimir" class="imprimir" style="text-align: center">
				<?php 

				$empleado =0;
				$nomina=0;
				$idtipop ="";
				$idnomp="";
				$sumaPercepciones = 0;
				$sumaDeducciones = 0 ;
				$origen = 0;
				$dtDeducciones="";
				$dtPercepciones ="";
				$origendes="";
				if($cargaEmpleadosPerceFiltros->num_rows>0) {

					while($e = $cargaEmpleadosPerceFiltros->fetch_object()){
						$dtDeducciones="";
						$dtPercepciones =""; ?>
						<div class="col-md-12">
							<div class='alert alert-info'>
								<!-- <div style="width: 90%"> -->
								 <table style='text-align:left;color:black'>
										<tbody>
											<tr>
												<td style='text-align:left;padding-bottom: 9px' colspan='5'>
													<?php 
													$url = explode('/modulos',$_SERVER['REQUEST_URI']);
													if($logo1 == 'logo.png') $logo1= 'x.png';
													$logo1 = str_replace(' ', '%20', $logo1);  
													echo "<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo1 style='width: 185px;height: 40px;padding-right:30px'>";
													echo "<b>".$infoEmpresa['nombreorganizacion']." ".$infoEmpresa['RFC']."</b></td>"; 
													?>
													&ensp;
													&ensp;
												</td>
											</tr>
											<tr>
												<td colspan='3'><b>
													<?php echo $e->codigo." ".$e->nombreEmpleado." ".$e->apellidoPaterno." ".$e->apellidoMaterno."</b>"." "?></td>
													<td><b>Departamento:</b><?php echo $e->idDep
													?></td>
												</tr>
												<tr>
													<td colspan='2'><b>Curp:</b><?php echo  $e->curp?></td>
													<td><b>Imss:</b><?php echo $e->nss?></td>
													<td><b>RFC:</b><?php  echo $e->rfc?></td>
												</tr>
												<tr>
													<td colspan='2'><b>Dias laborados:</b><?php  echo $e->diaslaborados?>
													</td>
													<td><b>Dias pagados:</b><?php echo $e->pagadosdias?>
													</td>
													<td><b>Sueldo:</b><?php echo (number_format($e->salario,2,'.',','))?>
													</td>
													
												</tr>
												<tr>
													<td colspan='2'><b>Jornada:</b><?php echo $e->horas?></td>
													<td colspan='2'><b>Periodo:</b><?php echo $e->fechainicio." al ".$e->fechafin?></td>
												</tr>
												<tr>
													<td colspan="2"><b>Nomina:</b><?php  echo $e->nombre?></td>
													<td id='nomina' name='nomina'><b>Número Nómina:</b><?php echo $e->numnomina?></td>
													<td>
														<?php 

														if ($_REQUEST['origen']!='' || $e->idtipop==3) {
															echo"<b>"."Origen:</b>"."$e->origendes";
														}
														?>
													</td>
												</tr>
												
											</tbody>
										</table> 
									
										<?php  
											// $cargaPercepcion = $this->ReportesModel->cargaPerceFiltros($e->idtipop, $e->idnomp, $e->idempleado,$_REQUEST['codigouno'],$_REQUEST['codigodos'],$_REQUEST['origen'], false );
											$cargaPercepcion = $this->ReportesModel->cargaPerceFiltros($e->idtipop, $e->idnomp, $e->idempleado,$_REQUEST['codigouno'],$_REQUEST['codigodos'],$e->origen, false );

											$sumaPercepciones=0; 

											while($per = $cargaPercepcion->fetch_assoc()){	
												$dtPercepciones .= "<tr>"."
												<td class='conc'>".$per["concepto"]."</td>
												<td class='desc'>".$per["descripcion"]."</td>
												<td class='impo'>".number_format($per["importe"],2,'.',',')."</td>
												</tr>";
												$sumaPercepciones += $per["importe"];
											}						

											$cargaDeduccion   = $this->ReportesModel->cargaDeduccionFiltros($e->idtipop,$e->idnomp,$e->idEmpleado,$e->origen);
											// echo "empleado".$$e->idEmpleado;
											$sumaDeducciones=0;

											while($ded = $cargaDeduccion->fetch_assoc()){		
												$dtDeducciones .= "<tr>"."
												<td class='conc'>".$ded["concepto"]."</td>
												<td class='desc'>".$ded["descripcion"]."</td>
												<td class='impo'>".number_format($ded["importe"],2,'.',',')."</td>
												</tr>";
												$sumaDeducciones += $ded["importe"];
											}
										?> 

										<table class='tablaper table border' width='100%' style='font-weight: normal;background-color:rgb(255,255,255)'; border='1'>
											<div class='row' align='left'; style='color: black;'>
												<div class='row'>
													<thead style='border:solid 1px';>
														<tr class='encpren'>
															<td colspan='3'><b>Percepciones</b></td>
															<td colspan='3'><b>Deducciones</b></td>
														</tr> 
													</thead>
													<tbody>
														<tr>
															<td colspan='3' style='vertical-align: top;'>
																<table style='width:100%; height:100%;font-weight: normal;'>
																	<thead>
																		<tr>
																			<th class='clave'>Clave</th>
																			<th class='concp'>Concepto</th>
																			<th class='imp'>Importe</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php echo $dtPercepciones; ?>
																	</tbody>
																</table> 
															</td>
															<td colspan='3' style='vertical-align:top;'>
																<table style='width:100%;height:100%;font-weight:normal;'>
																	<thead>
																		<tr>
																			<th class='clave'>Clave</th>
																			<th class='concp'>Concepto</th>
																			<th class='imp'>Importe</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php echo $dtDeducciones; ?>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>  
													<tr>
														<td colspan='2' align='right'><b>Suma de percepciones</b></td>
														<td align='right'><?php echo (number_format($sumaPercepciones,2,'.',','))?></td>
														<td colspan='2' align='right'><b>Suma de deducciones</b></td>
														<td align='right'><?php echo (number_format($sumaDeducciones,2,'.',','))?></td>
													</tr>
													<?php ?>
												</table>
												<div class='container-fluid row negri' style='text-align: right;'> 
													<label>NETO A PAGAR:</label>  
													<label>
														<?php  if ($sumaPercepciones>$sumaDeducciones) { 
															echo "$".number_format($sumaPercepciones - $sumaDeducciones,2,'.',','); ?>

															<?php }else
															{
																echo "$ ".number_format($sumaDeducciones-$sumaPercepciones,2,'.',',');
															}?>
														</label>
													</div>
													<br>

													<?php


													$origen=$_REQUEST['origen']; if  ($origen==1 ||  $origen==2 || $origen==3 || $e->idtipop==3 ){
													$origendes=$e->origendes;
													$origendes = ucwords($origendes); 
													
													?>

														<div class='mostrar negri firma' hidden>
															<p class='firma'><?php echo "Recibo la cantidad asentada en “Neto a Pagar” por concepto de mi ".$origendes." y demas prestaciones correspondientes al periodo que termina hoy, sin que a la fecha se me adeude ninguna cantidad."; ?> </p>
															<br>
															<div class='row firma'>
																<div class='col-md-12' style='text-align: center'>
																	<p>____________________________________</p>
																	<p>Firma</p>
																</div>

																<?php }else  { ?>

																<div class='mostrar negri firma' hidden>
																	<p class='firma'><?php echo "Recibo la cantidad asentada en “Neto a Pagar” por concepto de mi sueldo y demas prestaciones correspondientes al periodo que termina hoy, sin que a la fecha se me adeude ninguna cantidad."; ?> </p>
																	<br>
																	<div class='row firma'>
																		<div class='col-md-12' style='text-align: center'>
																			<p>____________________________________</p>
																			<p>Firma</p>
																		</div>
																		<?php  }?>
																	</div>
																</div>
															</div>
														</div>

														<div class='saltoDePagina' style='height:30px'></div> 

														<?php }
													}
													else{

														if($cargaPerceFiltros->num_rows==0 && $_REQUEST['idtipop']!=''){
															?>
															<div style='height:250px'></div> 
															<div class='w3-panel w3-padding-24 alert alert-info' style='border:solid 0px;background-color:rgb(217,237,247);width:100%;'>
																<h4>¡NO EXISTEN REGISTROS!</h4> 
															</div>

															<?php }
														} ?>
													</div>
												</div>


											</div>
										</div> 
									