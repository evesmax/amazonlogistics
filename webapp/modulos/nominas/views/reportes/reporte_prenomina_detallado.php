<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8" />
   <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>  
   <link   rel="stylesheet" type="text/css" href="css/reporteacumulado.css"> 
   <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
   <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
   <script type="text/javascript" src="../../libraries/numeral.min.js"></script>
   <script type="text/javascript" src='js/reportePrenominaDetallado.js'></script>
   <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
   <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
   <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
   <link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
   <script src="../../libraries/dataTable/js/datatables.min.js"></script>
   <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
   <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
   <script type="text/javascript">
		$(document).ready(function(){	
			$('select[name*="nombre"] option[perio="11"]').hide();	
		});
	</script>
   <body>

      <div class="container-fluid" style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;">
         <b>Reporte de Prenomina Detallado</b>
      </div>
      <br>
      <div class="container well" style="width: 96%;">
         <form class="ocultos" method="post" action="index.php?c=Reportes&f=reportePrenominaDetallado" id="formDetallado"> <fieldset class="scheduler-border">
            <legend class="scheduler-border" align="center">Búsqueda</legend>
            <div class="form-inline">
                <div class="col-md-4">
                  <label>Periodo</label>
                  <select id="nombre" class="form-control selectpicker btn-sm" data-live-search="true" name="nombre" data-width="60%" title="Seleccione">
                <?php 
                while ($e = $tipoperiodo->fetch_object()){
                    $b = "";
                    if($e->idtipop == $idtipop){ $b='selected="selected"'; } 
                    echo '<option nombre="'.$e->nombre.'" perio="'.$e->idperiodicidad.'" idtipop="'.$idtipop .'" value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
                }
                ?>
           		 </select>  
            </div>

               <div class="col-md-4">
                  <label>Nomina:</label>
                  <select id="nominas" class="selectpicker nominas" data-live-search="true" name="nominas" data-width="70%" title="Seleccione"> 
                  </select>
                  	<input type="hidden" id="hdnIdNomp" value="<?php echo $nomina;?>"/>        
                  	<input type="hidden" id="nomi" name="nomi"/>
                  	<input type="hidden" id="fechainic" name="fechainic"/>
                  	<input type="hidden" id="fechafina" name="fechafina"/>
                  	<input type="hidden" id="periodnombre" name="periodnombre"/>
                  	<input type="hidden" id="period" name="period" value="<?php if (isset($_POST['nombre'])) echo $_POST['nombre']; ?>"/>
               </div>

               <div class="col-md-4"> <label>Empleado</label>
                  <select id="empleados" class="sel selectpicker" data-live-search="true" name="empleados" data-width="70%" title="Seleccione">
                  <option value="*" <?php $todos='*' ?> <?php if ($todos==$empleado) { echo 'selected="selected"';}else{echo "no iguales";} ?> >Todos</option>
                     <?php 
                while ($e = $empleados->fetch_object()){
                    $b = "";
                    if($e->idEmpleado == $empleado){ $b='selected="selected"'; } 
                    echo '<option idEmpleado="'.$empleado .'" value="'.$e->idEmpleado .'" '. $b .'>'.$e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';
                }
                ?>
                  </select>
               </div>
            </div>
            <div class="col-md-12" style="text-align: center;padding-top: 15px;">
               <button type="button" class="btn btn-primary btn-sm" id="load" style="text-align: center;" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
               <a type="button" class="btn btn-sm" style="background-color:#d67166"  href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
                  title ="Generar reporte en PDF" border="0"> 
               </a>  
            </div>
         </fieldset>  
      </form>
   <input type="hidden"  id="periodoSelecc"  value="<?php echo $_POST['nombre']; ?>">
   <div id="imprimible" class="table-responsive alert alert-info" style="color: black;"> 
      	 <?php 
         if($nomina >= $nomActiperioSelecc){?>
         <div class="container-fluid leyenda"  style="background-color:rgb(180,191,193);color:black;font-size: 14px;text-align: center;font-weight: bold;display: none;">
         	Reporte previo de nomina no autorizada
         	</div>
         	<div style="height: 3px;"></div>
     	<?php 
     	}else if($nomina < $nomActiperioSelecc) { ?>
         	<div class="container-fluid leyenda" style="background-color:rgb(180,191,193);color:black;font-size: 14px;text-align: center;font-weight: bold;display: none;">
             Reporte de nomina autorizada
         	</div>
            <div style="height: 3px;"></div>
         <?php } ?>


         <?php
         $empleado =0;
         $numero=0;
         if($reportePrenominaDetallado){?>
         <table style='font-size:12px;'>
            <tr>
               <td rowspan='4' style='width:200px;padding-right:20px;'>
                  <?php 
                  $url = explode('/modulos',$_SERVER['REQUEST_URI']);
                  if($logo1 == 'logo.png') $logo1= 'x.png';
                  $logo1 = str_replace(' ', '%20', $logo1);
                  echo "<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo1 style='width: 200px;height: 45px;'>"; 
                  ?>
               </td>
               <td><b><?php echo $infoEmpresa['nombreorganizacion'].' '.$infoEmpresa['RFC']?></b>
               </td>
            </tr>
            <tr>
               <td><b>Cálculo de la nómina</b></td></tr>
               <tr>
                  <td><?php  
                     if ($_REQUEST['nominas'] =="*") {echo"<b>Nomina periodo:</b>".' '."Todas nominas"."</p>";
                  }else {
                     echo"<b>Nómina periodo:</b>".' '.$_REQUEST['periodnombre'].' '.$_REQUEST['nomi'];
                  }?>
               </td>
            </tr>
            <tr>
               <td><?php  
                  if ($_REQUEST['nominas'] =="*") {
                  }
                  else { 
                     echo"<b>Fecha Inicial:</b>".' '.$_REQUEST['fechainic'].' '.' '."<b>Fecha Final:</b>".' '.$_REQUEST['fechafina'] ;
               }  
               ?>
            </td>
         </tr>
         <tr >
         	<td style="height: 10px;" rowspan='4'></td>
         </tr>
      </table>
     
      <?php  
      while( $datos = $empleadosTE->fetch_object() ){
			//$dias = 0;
			//$minutosmas = $minutosmenos = $totaldiaopcional = 0 ;
			$tiempoextra[ $datos->idEmpleado][$datos->fecha]['minutosdiferenciaentrada'] = $datos->minutosdiferenciaentrada;
			$tiempoextra[ $datos->idEmpleado][$datos->fecha]['minutosdiferenciacomida'] = $datos->minutosdiferenciacomida;
			$tiempoextra[ $datos->idEmpleado][$datos->fecha]['minutosdiferenciasalida'] = $datos->minutosdiferenciasalida;
			$tiempoextra[ $datos->idEmpleado][$datos->fecha]['totaldiaopcional'] = $datos->totaldiaopcional;
				
			
		}
	  	$fechainicio = $_REQUEST['fechainic'];
		$fechafin = $_REQUEST['fechafina'];
					
					
						while($datos = $empleadosTErelacion->fetch_object() ){
						$salariohora = number_format($datos->salario/$datos->horasdetalle,2,'.',',');
						$minutosmas = $minmenos = $minutosmastotal = $minutosmenos =  0;
						$dias = $banderatriple = 0;
						$cont = 1;
						$incre = 1;
						$banderadia = false;
						$fechainicio = $_REQUEST['fechainic'];
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
							
						
						$minutosmasperiodo = 0;
						// foreach($semana[ $datos->idEmpleado ]['totalsemana'] as $t){
							// $minutosmasperiodo += $t;
						// }
						$tedoble = $tetriple = $diadoble = $diatriple = 0;
						 foreach($dia[ $datos->idEmpleado ] as $t){
							$minutosmasperiodo += $t['minutos'];
							
						}
						$minutospositivos[ $datos->idEmpleado ]=$minutosmasperiodo;
						
					//	echo json_encode($dia)."<hr>";
						
						$check = "checked";
						if($tedoble == 0 && $tetriple == 0){
							$check = "";
						}
			}	?>
		
            <table class="tablepreDetallado table table-striped table-bordered table-responsive table-hover" style="width:100%;font-size: 12px;background-color: white;" border='.1px' bordercolor="#0000FF" cellpadding="2">
               <thead> 
               	<tr style='background-color:rgb(180,191,193);color:black;font-weight: bold;'>
                     <th>Jornada</th>
                     <th>Días Cheq.</th>
                     <th>Sal Hora</th>
                     <th>Sal.Base Diario</th>
                     <th>Sal.Inte Diario</th>
                     <th>Sueldo</th>
                     <th>Premio Asist.</th>
                     <th>Premio Punt.</th>
                     <th>Base</th>
                     <th>ISPT</th>
                     <th>Subs</th>
                     <th>Reten</th>
                     <th>Entreg</th>
                     <th>IMSS</th>
                     <th>P.Vac</th>
                     <th>Vacac</th>
                     <th>Días Vac</th>
                     <th>Min +</th>
                     <th>Min -</th>
                     <th><label class="kr">T.Ext$</label><label class="mn" style="display: none">Neto</label></th>     
                  </tr>
               </thead> <tbody>
<?php      if($reportePrenominaDetallado->num_rows>0) {
         while($in = $reportePrenominaDetallado->fetch_assoc()){?>
	
                  <tr>
                     <td colspan="20" style="background-color:#dae0de;">   
                        <?php echo "<b>".$in['codigo'].' '.$in['nombreEmpleado'].' '.$in['apellidoPaterno'].' '.$in['apellidoMaterno'];echo "</b>"; ?>
                     </td>
                  </tr>  
                  <tr>
                     <td><?php echo $in['horas'];?></td>
                     <td style="text-align: center;"><?php echo $in['DiasChe'];?></td>
                     <td style="text-align: right;"><?php echo (number_format($in['salarioHora'],2,'.',','));?></td>
                     <td style="text-align: right;"><?php echo (number_format($in['salario'],2,'.',','));?></td>
                     <td style="text-align: right;"><?php echo (number_format($in['sdi'],2,'.',','));?></td><td style="text-align: right;" class='sumasueldo'><?php echo (number_format($in['sueldo'],2,'.',','));?></td>
                     <td style="text-align: right;" class='tdpremioasist'>
                     <?php echo (number_format($in['primaAsistencia'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdpremioaPunt">
                     <?php echo (number_format($in['puntualidad'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdbase">
                     <?php echo (number_format($in['base'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdispt">
                     <?php echo (number_format($in['ispt'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdsubs">
                     <?php echo (number_format($in['subsid'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdretenido">
                     <?php echo (number_format($in['retenido'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdentregado">
                     <?php echo (number_format($in['entregado'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdimss">
                     <?php echo (number_format($in['imss'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdprimVac">
                     <?php echo (number_format($in['primavacacional'],2,'.',','));?></td>
                     <td style="text-align: right;" class="tdvaca">
                     <?php echo (number_format($in['vacaciones'],2,'.',','));?></td>
                     <td style="text-align: center;"><?php echo $in['diasvacaciones'];?></td>
                     <td style="text-align: center;"><?php echo $minutospositivos[ $in['idEmpleado']];?></td>
                     <td style="text-align: center;"><?php echo $in['minutosdeben'];?></td>
                     <td style="text-align: right;" >
                     <button type="button"  onclick="verTE(<?php echo $in['idEmpleado']?>,<?php echo $in['idnomp'];?>)" class="btn btn-warning btn-sm " ><label class="kr">Ver</label></button>
                     </td> 
                  </tr> 
                  <?php 
                  echo "<tr>  
                  <td colspan='20' style='text-align:right;'>"."Sueldo Neto:"." ".(number_format($in['sueldoneto'],2,'.',','))." "."-"." "."Infonavit:"." ".(number_format($in['infonavit'],2,'.',','))." "."="." <b>".(number_format($in['suelinfon'],2,'.',','))."</b></td>   
                  </tr>";

               $neto = $in['neto'];    
               $empleado = $in['idEmpleado'];
               $tabladetallepre = $this->ReportesModel->tabladetallepre($in['idEmpleado'],$in['idtipop'],$in['idnomp']);

               if($tabladetallepre->num_rows>0) { 
                  while($d = $tabladetallepre->fetch_assoc()){

                     if($d["idtipo"] == 1 || $d["idtipo"] == 4){
                        $neto+=$d['importe']; 

                     }else{
                        $neto-=$d['importe'];
                     }
                     echo 
                     "<tr> 
                     <td>".$d['concepto']."</td>  
                     <td colspan='3'>".$d['descripcion']."</td>
                     <td style='text-align:right;'>".(number_format($d['importe'],2,'.',','))."</td>
                     <td colspan='15'></td></tr>";    
                  }
               }
               echo"<tr>            
               <td colspan='20' style='text-align:right;' class='tdneto'><b>".(number_format($neto,2,'.',','))."</b></td></tr> "; 
          
                
            }
 		 echo"</tbody></table>";
         }?>

        <!--  <DIV class='alert alert-success'> -->
            <table class="tablepreDetallado table  table-striped table-bordered table-responsive table-hover" 
            style="width:100%;color: black;background-color: white;font-size: 12px;" border='.1px' bordercolor="#0000FF" cellpadding="2">
            <thead> 
               <tr style="background-color:rgb(180,191,193);font-weight: bold;">
                  <th>SUELDO TOTAL</th>
                  <th>TOTAL PREMIO ASIST.</th>
                  <th>TOTAL PREMIO PUNT.</th>
                  <th>TOTAL BASE</th>
                  <th>TOTAL ISPT</th>  
                  <th>TOTAL SUBS</th> 
                  <th>TOTAL RETENIDO</th>
                  <th>TOTAL ENTREG.</th>
                  <th>TOTAL IMSS</th>
                  <th>TOTAL PRIMA VACA.</th>
                  <th>TOTAL VACA.</th>
                  <th>TOTAL NETO</th>
               </tr>
            </thead>
            <tbody>
               <tr>    
                  <td id='tdsumasueldo' style="text-align: right;"></td>
                  <td id='tdpremioasist' style="text-align: right;"></td>
                  <td id='tdpremioaPunt' style="text-align: right;"></td>
                  <td id='tdbase' style="text-align: right;"></td>
                  <td id='tdispt' style="text-align: right;"></td> 
                  <td id='tdsubs' style="text-align: right;"></td>
                  <td id='tdretenido' style="text-align: right;"></td>
                  <td id='tdentregado' style="text-align: right;"></td>
                  <td id='tdimss' style="text-align: right;"></td>
                  <td id='tdprimVac' style="text-align: right;"></td>
                  <td id='tdvaca' style="text-align: right;"></td>
                  <td id="tdneto" style="text-align: right;"></td>
               </tr> 
            </tbody>
         </table>
         <table class="tablepreDetallado table table-striped table-bordered table-hover table-responsive" style="border: 1px;width:100%;color: black;background-color: white;" border='.1px' bordercolor='#0000FF' id="sumaconceptos" cellpadding="2">   
            <thead> 
               <tr style='background-color:rgb(180,191,193);'>
                  <th colspan='3' style="font-weight: bold;">SUMA DE CONCEPTOS:</th>
               </tr>
            </thead>
            <tbody>
               <tr style="font-weight: bold;color: black;">
                  <td>CONCEPTO</td>
                  <td>DESCRIPCIÓN</td>
                  <td>IMPORTE</td>
               </tr>
               <?php 
               if($sumasconceptos->num_rows>0){
                  while($con = $sumasconceptos->fetch_assoc()){ ?>
                  <tr>
                     <td><?php echo $con['concepto']?></td>
                     <td><?php echo $con['descripcion']?></td>
                     <td style="text-align: right;"><?php echo "$".' '.(number_format($con['importe'],2,'.',','))?></td>
                  </tr>
                  <?php  
               }   
            }?> 
         </tbody>
      </table>
   <!-- </DIV> -->

   <?php
    }
    else {?> 
      
            <table class="table table-striped table-bordered" style="width:100%;" border='.1px' bordercolor="#0000FF">
               <thead> 
                  <tr style='background-color:rgb(180,191,193);color:black;'>
                     <th>Jornada</th>
                     <th>Días Cheq.</th>
                     <th>Sal Hora</th>
                     <th>Sal.Base Diario</th>
                     <th>Sal.Inte Diario</th>
                     <th>Sueldo</th>
                     <th>Premio Asist.</th>
                     <th>Premio Punt.</th>
                     <th>Base</th>
                     <th>ISPT</th>
                     <th>Subs</th>
                     <th>Reten</th>
                     <th>Entreg</th>
                     <th>IMSS</th>
                     <th>P.Vac</th>
                     <th>Vacac</th>
                     <th>Días Vac</th>
                      <th>Min +</th>
                       <th>Min -</th>
                     <th>T.Ext$</th>
                    
                  </tr>
               </thead>
               <tbody>
                  <tr>
                  <td colspan="20" style="text-align: center;">Ningún dato disponible en esta tabla.</td>
                  </tr>
                  </tbody>
                  </table>
   
      <?php  }?>

</div>
</div>
<div id="vistate" >
	
</div>

<!--GENERA PDF*************************************************-->
<div id="divpanelpdf" class="modal fade" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Generar PDF</h4>
         </div>
         <form id="formpdf" action="../cont/libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <label>Escala (%):</label>
                     <select id="cmbescala" name="cmbescala" class="form-control">
                        <?php
                        for($i=100; $i > 0; $i--){
                           echo '<option value='. $i .'>' . $i . '</option>';
                        }
                        ?>
                     </select>
                  </div>
                  <div class="col-md-6">
                     <label>Orientación:</label>
                     <select id="cmborientacion" name="cmborientacion" class="form-control">
                        <!-- <option value='P'>Vertical</option> -->
                        <option value='L'>Horizontal</option>
                     </select>
                  </div>
               </div>
               <textarea id="contenido" name="contenido" style="display:none"></textarea>
               <input type='hidden' name='tipoDocu' value='hg'>
               <input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
               <input type='hidden' name='nombreDocu' value='Detalle Nomina'>
            </div>
            <div class="modal-footer">
               <div class="row">
                  <div class="col-md-6">
                     <input type="submit" value="Crear PDF" autofocus class="btn btn-primary btnMenu">
                  </div>
                  <div class="col-md-6">
                     <input type="button" value="Cancelar" onclick="cancelar_pdf()" class="btn btn-danger btnMenu">
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>
<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
   <div id="divmsg" style="
   opacity:0.8;
   position:relative;
   background-color:#000;
   color:white;
   padding: 20px;
   -webkit-border-radius: 20px;
   border-radius: 10px;
   left:-50%;
   top:-200px
   ">
   <center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...
   </center>
</div>
</div> 
<script>
   function cerrarloading(){
      $("#loading").fadeOut(0);
      var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
      $("#divmsg").html(divloading);
   }
</script> 
</body>
</html>
