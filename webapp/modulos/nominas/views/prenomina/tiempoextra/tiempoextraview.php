<!DOCTYPE html>
<head>
	 	 <script src="js/jquery-1.10.2.min.js"></script>
  	<script type="text/javascript" src="js/jquery.number.js"></script>  


	<link rel="stylesheet" href="../../libraries/bootstrapcheck/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../libraries/bootstrapcheck/css/bootstrap-theme.min.css">
  <script src="../../libraries/bootstrapcheck/js/bootstrap-checkbox.min.js" ></script>

   <script src="js/tiempoextra.js"></script>

</head>
<script>
	$(function() { $('.checkboxsino').checkboxpicker(); });
</script>
<body>
	<div class="container well" style="width: 100%">
		<h3 align="center"> 	Tiempo extra de empleado</h3><hr>
		<h4 align="center"> Periodo :
			<div align="center" style="width: 250px">
				<select id="periodonom" class="selectpicker" data-width="100%" data-live-search="true" onchange="cambiaperiodo(this.value)">
					<?php
					while($p = $periodos->fetch_object()){
						if($nominaActiva['idtipop'] == $p->idtipop){ $se = "selected";}else{ $se="";}?>
						<option value="<?php echo $p->idtipop;?>" <?php echo $se;?>><?php echo $p->nombre; ?></option>
			<?php	}
					?>
				</select>
		</div>
			</h4>
		<h5 align="center"> <?php echo $nominaActiva['fechainicio']." al ".$nominaActiva['fechafin'];?></h5><hr>
		<input type="hidden" id="periodicidad" value="<?php echo $nominaActiva['idperiodicidad'];?>" />
		<input type="hidden" id="idnomp" value="<?php echo $nominaActiva['idnomp'];?>" />
		<div class="alert alert-warning">
	        <button type="button" class="close" data-dismiss="alert">
	            <span aria-hidden="true">×</span>
	            <span class="sr-only">Cerrar</span>
	        </button>
	        <i class="fa fa-info-circle fa-lg"></i> INFORMACION<br>
	      * Los minutos represantados en positivos son acuerdo a su <a href="" title="Ir a Configuracion" onclick="irConfiguracion()">Configuracion</a> de T.E. <br>
	      * Los minutos negativos son todos aquellos que el empleado tomo de mas en su horario habitual: retardos, comida , salidas antes etc.<br>
	      * Si decide pagar tiempo por tiempo debera colocar manualmente el tiempo extra a pagar. <br>
	    	  *	<b>Si no autoriza tiempo por tiempo las horas que marque se pagaran redondeadas de acuerdo a la siguiente nota</b>
	     <br> <b>NOTA:</b> Se deben considerar el número de horas extra completas y en caso de tener fracciones se deben redondear.
		<br>Ejemplo:<br>
			HorasExtra= 3<br>
			Fundamento Legal: Artículo 65, 66, 67 y 68 de la Ley Federal del Trabajo. 

	    </div>
		<div class="alert alert-info">
			<table cellpadding="1" cellspacing="1" class="table table-over table-bordered ">
				<thead>
					<tr style="background-color:rgb(180,191,193);color:black;font-weight: bold;">
						<td>Empleado</td>
						<td>Jornada</td>
						<td>Salario por diario</td>
						<td>Salario por hora</td>
						<td>Minutos +</td>
						<td>Minutos -</td>
						<td>Dias X2</td>
						<td>Dias X3</td>
						<td>Hrs Doble </td>
						<td>Hrs Triple</td>
						<td>Importe doble</td>
						<td>Importe triple</td>
						<td>Autorizar tiempo por tiempo</td>
						<td>Pagar tiempo extra</td>
						
					</tr>
				</thead>
				<tbody>
					<?php
					$fechainicio = $nominaActiva['fechainicio'];
					$fechafin = $nominaActiva['fechafin'];
					
					
					while( $datos = $empleadosTE->fetch_object() ){
						//$dias = 0;
						//$minutosmas = $minutosmenos = $totaldiaopcional = 0 ;
						$tiempoextra[ $datos->idEmpleado][$datos->fecha]['minutosdiferenciaentrada'] = $datos->minutosdiferenciaentrada;
						$tiempoextra[ $datos->idEmpleado][$datos->fecha]['minutosdiferenciacomida'] = $datos->minutosdiferenciacomida;
						$tiempoextra[ $datos->idEmpleado][$datos->fecha]['minutosdiferenciasalida'] = $datos->minutosdiferenciasalida;
						$tiempoextra[ $datos->idEmpleado][$datos->fecha]['totaldiaopcional'] = $datos->totaldiaopcional;
							
						
						
					}
					
					while($datos = $empleadosTErelacion->fetch_object() ){
						$salariohora = number_format($datos->salario/$datos->horasdetalle,2,'.',',');
						$minutosmas = $minmenos = $minutosmastotal = $minutosmenos =  0;
						$dias = $banderatriple = 0;
						$cont = 1;
						$incre = 1;
						$banderadia = false;
						$fechainicio = $nominaActiva['fechainicio'];
						while(strtotime($fechafin) >= strtotime($fechainicio))
						{
									/*si la fecha es igual entonces es dia q tiene en el checador y se suman los tiempos*/
									$banderadia = false;
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciaentrada']  > 0 && $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciaentrada']  >= $configuracionTE->minacumulaTE){
										$minutosmas += ( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciaentrada']  - $configuracionTE->mincuentaTE );
										$minutosmastotal += ( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciaentrada']  - $configuracionTE->mincuentaTE );
										$banderadia = true;
									}
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciacomida'] > 0 && $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciacomida'] >= $configuracionTE->minacumulaTE){
										$minutosmas += ( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciacomida'] - $configuracionTE->mincuentaTE );
										$banderadia = true;
										$minutosmastotal += ( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciacomida'] - $configuracionTE->mincuentaTE );
										
									}
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciasalida'] > 0 && $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciasalida'] >= $configuracionTE->minacumulaTE){
										$minutosmas += ( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciasalida'] - $configuracionTE->mincuentaTE );
										$banderadia = true;
										$minutosmastotal += ( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciasalida'] - $configuracionTE->mincuentaTE );
										
									}
								
								
								
									
									/*TIEMPO NEGATIVO*/
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciaentrada'] < 0){
										$minutosmenos += $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciaentrada'];

										//$minutosmenos = $minutosmenos * -1;//por -1 para q me haga positivo el tiempo solo para visualizacion
									}
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciacomida'] < 0 ){
										$minutosmenos += $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciacomida'];
										//$minutosmenos = $minutosmenos * -1;
									}
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciasalida'] < 0 ){
										$minutosmenos += $tiempoextra[ $datos->idEmpleado ][$fechainicio]['minutosdiferenciasalida'];
										//$minutosmenos = $minutosmenos * -1;
									}
									/*TIEMPO EXTRA DE DIAS OPCIONALES*/
									if( $tiempoextra[ $datos->idEmpleado ][$fechainicio]['totaldiaopcional'] > 0 ){
										$minutosmas += $tiempoextra[ $datos->idEmpleado ][$fechainicio]['totaldiaopcional'];
										$minutosmastotal += $tiempoextra[ $datos->idEmpleado ][$fechainicio]['totaldiaopcional'];
										$banderadia = true;
									} 
									
									
									if( $banderadia ){ //con q un dia aplique para tiempo extra se incrementa el dia
										$dias += 1;
									}
											//incrementamos el dia
							$te = $minutosmas / 60;
							if($banderatriple == 1 && $cont != 7){// echo "entre1";
								//$dia[$datos->idEmpleado ][$incre]['diastriples'] = $dias;
								//$dia[$datos->idEmpleado ][$incre]['triple'] = $te;
								//$dia[$datos->idEmpleado ][$incre]['minutos'] = $minutosmas;
								//$banderatriple = 0;//esto para q no entre hasta q aplique
							}
							// else if($cont != 7 && $te < 9){//hago esto para ir acumulando los dias q aplican para doble
								// //echo "entre2";
								// //$tedoble = $tetriple = $tepor = 0;
								// //$dia[$datos->idEmpleado ][$incre]['diasdobles'] = $dias;
								// //$dia[$datos->idEmpleado ][$incre]['dobles'] = $te;
// 									
							// }
							else if($te >= 9 && $cont != 7){// esto quiere decir q aun no finalizada la semana se acumularon las 9 primeras dobles
								//echo "entre3";
								
								//$banderatriple = 1;	
								//$tedoble = number_format($te,2);
								if( $banderatriple == 0){
								
									$dia[$datos->idEmpleado ][$incre]['diasdobles'] = $dias;
									$dia[$datos->idEmpleado ][$incre]['dobles']= 9;
									$dia[$datos->idEmpleado ][$incre]['minutos']= $minutosmas;
									$tetriple = number_format($te - 9,2);
									if($tetriple>0){
										$banderatriple = 1;
										$dia[$datos->idEmpleado ][$incre]['diastriples'] = 1;
										$dia[$datos->idEmpleado ][$incre]['triple'] = $tetriple;
										$dia[$datos->idEmpleado ][$incre]['minutos']= $minutosmas;
									}
								}else{//si la bandera triple esta activa solo acumulara triple
									$dia[$datos->idEmpleado ][$incre]['diastriples'] = $dias;
									$dia[$datos->idEmpleado ][$incre]['triple'] = $te;
									$dia[$datos->idEmpleado ][$incre]['minutos']= $minutosmas;
									//$banderatriple = 1;
								}
								$minutosmas = 0; //hago los minutos mas 0 para q ahora lo q se junte sea triple	
								$dias = 0;
								
								
							}
							
								
							
							else if($cont == 7 ){// si llego al tope de 7 veremos cuanto acumulo de ambos tiempos
								//$banderatriple = 0;
								
								//echo "entre4".$te;
								if( $banderatriple == 0){
									if($te <= 9){
										
										//$tepor = 2;
										$tedoble = number_format($te,2);
										$dia[$datos->idEmpleado ][$incre]['diasdobles'] = $dias;
										$dia[$datos->idEmpleado ][$incre]['dobles']= $tedoble;
										$dia[$datos->idEmpleado ][$incre]['minutos']= $minutosmas;
									}else{
										
										//$banderatriple = 1;
										$dia[$datos->idEmpleado ][$incre]['diasdobles'] = $dias;
										$dia[$datos->idEmpleado ][$incre]['dobles'] = 9;
										$tetriple = number_format($te - 9,2);
										$dia[$datos->idEmpleado ][$incre]['diastriples'] = 1;
										$dia[$datos->idEmpleado ][$incre]['triple'] = $tetriple;
										$dia[$datos->idEmpleado ][$incre]['minutos']= $minutosmas;
										
									}
								}else{//sihay bandera ya solo acumula el tiempo extra q se junto porq quiere decir q ubo el tope de doble
									$banderatriple = 0;// se pone en 0 porq ya es el tope de 7
									$dia[$datos->idEmpleado ][$incre]['diastriples'] = $dias;
									$dia[$datos->idEmpleado ][$incre]['triple'] = $te;
									$dia[$datos->idEmpleado ][$incre]['minutos']= $minutosmas;
								}
								/*cuando el contador este en 7 q es una semana debera comprobar si junto el tiempo
								 * acumulado semanal para hacer el pago*/
									//if($cont == 7){
										 /*si termino la semana pero no llego al acumulado semanal entonces se reinicia el tiempo extra*/
								 $semana[ $datos->idEmpleado ]['totalsemana'][] = $minutosmas;
								 $semana[ $datos->idEmpleado ]['diassemana'][] = $dias;
// 								
								
								if( $minutosmas < $configuracionTE->acumuladosemanal ){
									//si no lo acumulo se borrar lo almacenado previo
									unset($dia[$datos->idEmpleado ][$incre]);
								}
							$minutosmas  = 0;
							 // /*se reinicia los contadores*/ 
								 $cont = 0;
								 $dias = 0;
							
									
									
						}
						$cont ++;		
						$incre ++;	
									
								
								
									
									
								//}//if(strtotime(
								$fechainicio = date("Y-m-d", strtotime($fechainicio . " + 1 day"));
					}//while de fechas
							
						if($cont<7){
							//echo "entre aqui";
							if( $minutosmastotal < $configuracionTE->acumuladosemanal ){
								$minutosmastotal = 0;
							}else{
								$semana[ $datos->idEmpleado ]['totalsemana'][] = $minutosmastotal;
								$semana[ $datos->idEmpleado ]['diassemana'][] = $dias;
							}
						}
						$minutosmasperiodo = 0;
						// foreach($semana[ $datos->idEmpleado ]['totalsemana'] as $t){
							// $minutosmasperiodo += $t;
						// }
						$tedoble = $tetriple = $diadoble = $diatriple = 0;
						 foreach($dia[ $datos->idEmpleado ] as $t){
							$minutosmasperiodo += $t['minutos'];
							$tedoble += $t['dobles'];
							$tetriple += $t['triple'];
							$diadoble += $t['diasdobles'];
							$diatriple+= $t['diastriples'];
						}
						 
						
					//	echo json_encode($dia)."<hr>";
						
						$impdoble = ($salariohora * 2) * number_format($tedoble,2) ;
						$imptriple = ($salariohora * 3) * number_format($tetriple,2);
						$check = "checked";
						if($tedoble == 0 && $tetriple == 0){
							$check = "";
						} 
						?>
						<tr onmouseout="this.style.background='#CFE8F5'" onmouseover="this.style.background='#F9F9F9'">
							<td><?php echo $datos->nombreempleado; ?></td>
							<td align="center"><?php echo $datos->horasdetalle; ?></td>
							<td align="right"><?php echo $datos->salario; ?></td>
							<td align="right"><?php echo $salariohora; ?></td>
							<td align="right"><?php echo $minutosmasperiodo; ?></td>
							<td align="right"><?php echo ($minutosmenos*-1); ?></td>
							<td align="right">
								<div id="diadoble<?php echo $datos->idEmpleado;?>">
									<b  id="diado<?php echo $datos->idEmpleado;?>"><?php echo $diadoble; ?> </b>
								</div>
								<input  type="txt" size="1" id="inputdiado<?php echo $datos->idEmpleado;?>" style="display:none" />

							</td>
							<td align="right">
								<div id="diatriple<?php echo $datos->idEmpleado;?>">
									<b  id="diatri<?php echo $datos->idEmpleado;?>"><?php echo $diatriple; ?> </b>
								</div>
								<input  type="txt" size="1" id="inputdiatri<?php echo $datos->idEmpleado;?>" style="display:none" />

							</td>
							<td align="right">
								<div id="divd<?php echo $datos->idEmpleado;?>">
									<input type="checkbox"  id="doble<?php echo $datos->idEmpleado;?>" />
									<b id="originald<?php echo $datos->idEmpleado;?>"><?php echo number_format($tedoble,2); ?></b>
								</div>
								<input  type="txt" size="3" id="inputd<?php echo $datos->idEmpleado;?>" style="display:none" onkeyup="calculoimporte(this.value,<?php echo $datos->idEmpleado;?>,'doble',<?php echo $salariohora;?>);" onkeypress="return solonumeriviris(event,this)"/>
								
							</td>
							<td>
								<div id="divt<?php echo $datos->idEmpleado;?>">
									<input type="checkbox" id="triple<?php echo $datos->idEmpleado;?>" />
									<b id="originalt<?php echo $datos->idEmpleado;?>"><?php echo number_format($tetriple,2); ?></b>
								</div>
								<input type="txt" size="3" id="inputt<?php echo $datos->idEmpleado;?>" style="display:none" onkeyup="calculoimporte(this.value,<?php echo $datos->idEmpleado;?>,'triple',<?php echo $salariohora;?>);" onkeypress="return solonumeriviris(event,this)"/>

							</td>
							<td align="right">
								<div id="importe<?php echo $datos->idEmpleado;?>">
									<b style="color:red" id="originalpagod<?php echo $datos->idEmpleado;?>">$<?php echo number_format($impdoble,2,'.',','); ?> </b>
								</div>
								<input readonly="" type="txt" size="6" id="importete<?php echo $datos->idEmpleado;?>" style="display:none" />

							</td>
							<td align="right">
								<div id="importet<?php echo $datos->idEmpleado;?>">
									<b style="color:red" id="originalpagot<?php echo $datos->idEmpleado;?>">$<?php echo number_format($imptriple,2,'.',','); ?> </b>
								</div>
								<input readonly="" type="txt" size="6" id="importetet<?php echo $datos->idEmpleado;?>" style="display:none" />

							</td>
							
							<td  align="center">
								 <input  type="checkbox" id="<?php echo $datos->idEmpleado;?>" class="checkboxsino" onchange="tiempoxtiempo(<?php echo $datos->idEmpleado;?>)">
							</td>
							<td  align="center">
								<input  type="checkbox" class="checkboxsino autoriza" id="okte<?php echo $datos->idEmpleado;?>" data-value="<?php echo $datos->idEmpleado;?>"  data-name="<?php echo $datos->nombreempleado;?>" <?php echo $check;?> >							
							</td>
						</tr>
				<?php	}
					?>
				</tbody>
			</table>
			<button type="button" class="btn btn-primary" id="finaliza" data-loading-text="<i class='fa fa-refresh fa-spin '></i>">Finalizar</button>
		</div>
	</div>
</body>

</html> 
