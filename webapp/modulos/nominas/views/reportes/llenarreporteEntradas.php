<div id="imprimible"> 
                    <?php
                    $empleado =0;
                    if($reporteEntradas && $reporteEntradas->num_rows>0) {
                        while($in = $reporteEntradas->fetch_assoc()){
                            if ($empleado != $in['idEmpleado']){
                                if ($empleado != 0 ){?>
                            </tbody>
                        </table>
                    </div>
                    <div class='saltopagina' style='height:30px;'></div>
                    <?php 
                } ?> 
                <div style='font-family: Courier;' align='center' hidden='true'>
                    <b style='font-size:14px;'>Reporte de entradas y salidas de empleado</b></div>
                    <div class='alert alert-info table-responsive' cellspacing='0' width='100%' style='overflow: auto;'>
                        <table class='mostrartabla' style="color: black;padding-bottom: 100px;"> 
                            <tr>
                                <td><b><?php echo $in['codigo'].' '.$in['nombreEmpleado'].' '.$in['apellidoPaterno'].' '.$in['apellidoMaterno'];?></b>
                                </td>
                            </tr>
                        </table><br>
                        <?php  echo"<table id=\"tablaentradas_".$in['idEmpleado']."\" cellpadding='0' class='table tablaentradas  table-striped table-bordered table-responsive nowrap' width='100%'; style='border:solid .3px;font-size:12.5px;' border='1' bordercolor='#0000FF'>";?>
                            <thead> 
                                <tr style='background-color:#B4BFC1;color:#000000;height: 35px;'>
                                    <th>Empleado</th> 
                                    <th>Fecha</th>
                                    <th>Día</th>
                                    <th>Hora Entrada</th>
                                    <th>Inicio Comida</th>
                                    <th>Fin Comida</th>
                                    <th>Hora Salida</th> 
                                    <th>Sucursal</th>   
                                </tr>
                            </thead>
                            <tbody>
                               <?php }?>
                                <tr>
                                    <td><?php echo $in['nombreEmpleado'].' '.$in['apellidoPaterno'].' '.$in['apellidoMaterno'];?></td>
                                    <td><?php echo $in['fecha'];?></td>
                                    <td><?php echo $in['dia'];?></td>
                                    <td><?php echo $in['horaentrada'];?></td> 
                                    <td><?php echo $in['iniciocomida'];?></td> 
                                    <td><?php echo $in['fincomida'];?></td> 
                                    <td><?php echo $in['horasalida'];?></td> 
                                    <td><?php echo $in['nombre'];?></td>           
                                </tr>                  
                                    <?php  
                                    $empleado = $in['idEmpleado'];
                                }?>
                            </tbody>
                        </table>
                    <br>
                    </div>
                                <?php   }  //fin del while
                        else { 
                            echo"<div class='alert alert-info' style='overflow-x: scroll;'>
                            <table id=\"tablaentradas_".$in['idEmpleado']."\" cellpadding='0' class='tablaentradas table table-striped table-bordered dt-responsive nowrap'  width='100%'; style='border:solid .3px;font-size:12.5px;' border='1' bordercolor='#0000FF'>";?>
                                <thead> 
                                    <tr style='background-color:#B4BFC1;color:#000000'>
                                        <th>Empleado</th>
                                        <th>Fecha</th>
                                        <th>Día</th>
                                        <th>Hora Entrada</th>
                                        <th>Inicio Comida</th>
                                        <th>Fin Comida</th>
                                        <th>Hora Salida</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <?php 
                        }       //fin if($reporteentradas->num_rows>0) 
                        ?>       
                    </div>

