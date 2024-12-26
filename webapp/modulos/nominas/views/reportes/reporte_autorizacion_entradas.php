

 <div class="panel panel-default">
<div class="panel-heading">  
    <?php  if ($diacompletoentradas->num_rows!=0 || $entradasoriginales->num_rows!=0) { 
        ?>
        <div style="text-align: right;">
                <a title="autoriza todos los cambios" href="#" class="btn btn-primary" onclick="AutorizarEmpleado('<?php echo $empleado; ?>','<?php echo  $_REQUEST['idnomp'];?>')">
                <span class="glyphicon glyphicon-floppy-disk"></span> Autorizar todo</a>

                 <a title="anular todos los cambios" href="#" class="btn btn-danger" onclick="Eliminartodo('<?php echo $empleado; ?>','<?php echo  $_REQUEST['idnomp'];?>')">
                <span class="glyphicon glyphicon-trash"></span> Anular todo</a>    
            </div>
            <?php  
        } ?>
    </div>
      <div class="panel-body">
                    <?php if($diacompletoentradas->num_rows>0){?>
                    <br>
                    <br>
                    <?php  }?> 
                    <?php
                    $empleado =0;
                    $htmlHorariosModificados ="";
                    $cantidadRegistrosProcesados = 0;
                    $idnomp ="";
                    $htmlTablaHorariosModificados = 
                        "<table id=\"tablemodif\" cellspacing=\"0\" width=\"100%\" class=\"tablemodif table-responsive table table-striped table-bordered\">
                                            <thead> 
                                                <tr style=\"background-color:#B4BFC1;color:#000000;\">
                                                    <th>Empleado</th>
                                                    <th>Fecha</th>
                                                    <th>Día</th>
                                                    <th>Hora Entrada</th>
                                                    <th>Inicio Comida</th>
                                                    <th>Fin Comida</th>
                                                    <th>Hora Salida</th>
                                                    <th>Periodo</th>
                                                    <th>No.Nómina</th>
                                                </tr>
                                            </thead>
                                            <tbody> ";                        

                    if($diacompletoentradas->num_rows>0) {
                        while($in = $diacompletoentradas->fetch_assoc()){
                            $cantidadRegistrosProcesados ++;

                            if ($empleado != $in['idEmpleado'] && $empleado != 0){ ?>

                                <div class="col-md-12 table-responsive alert alert-info uno"> 
                                    <br>
                                    <div style="text-align: right;" class="col-md-12">
                                         <a href="#" class="btn btn-primary btn-sm" onclick="AutorizarEmpleado('<?php echo $empleado; ?>','<?php echo  $in['idnomp'];?>','<?php echo  $diacompleto;?>')">
                                         <span class="glyphicon glyphicon-floppy-disk" ></span> Autorizar cambios</a>
                                           <a href="#" class="btn btn-danger btn-sm" onclick="Eliminartodo('<?php echo $empleado; ?>','<?php echo  $idnomp;?>','<?php echo  $diacompleto;?>')">
                                 <span class="glyphicon glyphicon-trash" ></span> Anular cambios
                                </a> 
  
                                    </div> 
                                    <br>
                                    <br>
                                
                                    <div class="col-md-12 table-responsive alert alert-danger">
                                        <b>HORARIO COMPLETO</b><br><br>
                                                <?php  echo 
                                                 $htmlTablaHorariosModificados.   
                                                 $htmlHorariosModificados.
                                            "</tbody>
                                        </table>
                                    </div>"
                                    ; ?>
                                </div>
                            <?php 
                                $htmlHorariosModificados ="";
                            }  //fin if

                            $htmlHorariosModificados.=
                                "       <tr>
                                            <td>".$in['apellidoPaterno']." ".$in['apellidoMaterno']." ".$in['nombreEmpleado']."</td>
                                            <td>".$in['fecha']."</td>
                                            <td>".$in['dia']."</td>
                                            <td>".$in['horaentrada']."</td>
                                            <td>".$in['iniciocomida']."</td>
                                            <td>".$in['fincomida']."</td>
                                            <td>".$in['horasalida']."</td>
                                            <td>".$in['nombre']."</td>
                                            <td>".$in['idnomp']."</td>
                                        </tr>
                                ";
                       
                            $empleado = $in['idEmpleado'];         
                            $idnomp = $in['idnomp'];  
                            $diacompleto = $in['diacompleto'];     
                        } //fin while
                        ?>
                        <div class="col-md-12 table-responsive alert alert-info"> 
                            <br>
                            <div style="text-align: right;" class="col-md-12">
                                 <a href="#" class="btn btn-primary btn-sm" onclick="AutorizarEmpleado('<?php echo $empleado; ?>','<?php echo  $idnomp;?>','<?php echo  $diacompleto;?>')">
                                 <span class="glyphicon glyphicon-floppy-disk" ></span> Autorizar empleado</a>  
                                   <a href="#" class="btn btn-danger btn-sm" onclick="Eliminartodo('<?php echo $empleado; ?>','<?php echo  $idnomp;?>','<?php echo  $diacompleto;?>')">
                                 <span class="glyphicon glyphicon-trash" ></span> Anular cambios
                                </a> 

                            </div> 
                            <br>
                            <br>
                           
                            <div class="col-md-12 table-responsive alert alert-danger">
                                <b>HORARIO COMPLETO</b><br><br>
                                        <?php  echo 
                                         $htmlTablaHorariosModificados.   
                                         $htmlHorariosModificados.
                                    "</tbody>
                                </table>
                            </div>"
                            ; ?>
                        </div>
                    <?php
                    }
                    else if($entradasoriginales->num_rows>0){

                     ?>
                    <br>
                    <br>
                    <br>
                    <?php  }?>
                    
                    <?php
                    $empleado =0;
                    $htmlHorariosActuales = "";
                    $htmlHorariosModificados ="";
                    $cantidadRegistrosProcesados = 0;
                    $idnomp ="";
                    $htmlTablaHorariosActuales = 
                        "<table id=\"tableoriginal\" cellspacing=\"0\" width=\"100%\" class=\"tableoriginal table-responsive table table-striped table-bordered\">
                                            <thead> 
                                                <tr style=\"background-color:#B4BFC1;color:#000000;\">
                                                    <th>Empleado</th>
                                                    <th>Fecha</th>
                                                    <th>Día</th>
                                                    <th>Hora Entrada</th>
                                                    <th>Inicio Comida</th>
                                                    <th>Fin Comida</th>
                                                    <th>Hora Salida</th>
                                                    <th>Periodo</th>
                                                    <th>No.Nómina</th>
                                                </tr>
                                            </thead>
                                            <tbody>";

                    $htmlTablaHorariosModificados = 
                        "<table id=\"tablemodif\" cellspacing=\"0\" width=\"100%\" class=\"tablemodif table-responsive table table-striped table-bordered\">
                                            <thead> 
                                                <tr style=\"background-color:#B4BFC1;color:#000000;\">
                                                    <th>Empleado</th>
                                                    <th>Fecha</th>
                                                    <th>Día</th>
                                                    <th>Hora Entrada</th>
                                                    <th>Inicio Comida</th>
                                                    <th>Fin Comida</th>
                                                    <th>Hora Salida</th>
                                                    <th>Periodo</th>
                                                    <th>No.Nómina</th>
                                                </tr>
                                            </thead>
                                            <tbody> ";                        

                    if($entradasoriginales->num_rows>0) {
                        while($in = $entradasoriginales->fetch_assoc()){
                            $cantidadRegistrosProcesados ++;

                            if ($empleado != $in['idEmpleado'] && $empleado != 0){ ?>

                                <div class="col-md-12 table-responsive alert alert-info uno"> 
                                    <br>
                                    <div style="text-align: right;" class="col-md-12">
                                         <a href="#" class="btn btn-primary btn-sm" onclick="AutorizarEmpleado('<?php echo $empleado; ?>','<?php echo  
                                $idnomp;?>','<?php echo  $diacompleto2;?>')">
                                         <span class="glyphicon glyphicon-floppy-disk" ></span> Autorizar empleado</a>
                                           <a href="#" class="btn btn-danger btn-sm" onclick="Eliminartodo('<?php echo $empleado; ?>','<?php echo  
                                $idnomp;?>','<?php echo  $diacompleto2;?>')">
                                         <span class="glyphicon glyphicon-trash"></span> Anular cambios
                                            </a> 
 
                                    </div> 
                                    <br>
                                    <br>
                                    <div class="col-md-6 table-responsive alert alert-success">
                                         <b>HORARIO ACTUAL</b><br><br>
                                                <?php  echo 
                                                 $htmlTablaHorariosActuales.
                                                 $htmlHorariosActuales.
                                            "</tbody>
                                        </table>
                                    </div>"
                                    ; ?>
                                    <div class="col-md-6 table-responsive alert alert-danger">
                                        <b>HORARIO MODIFICADO</b><br><br>
                                                <?php  echo 
                                                 $htmlTablaHorariosModificados.   
                                                 $htmlHorariosModificados.
                                            "</tbody>
                                        </table>
                                    </div>"
                                    ; ?>
                                </div>
                            <?php
                                $htmlHorariosActuales = "";
                                $htmlHorariosModificados ="";
                            }  //fin if

                            $htmlHorariosActuales.=
                                "       <tr>
                                           <td>".$in['apellidoPaterno']." ".$in['apellidoMaterno']." ".$in['nombreEmpleado']."</td>
                                            <td>".$in['fechaoriginal']."</td>
                                            <td>".$in['diaoriginal']."</td>
                                            <td>".$in['horaEntradaOriginal']."</td>
                                            <td>".$in['horaInicioComidaOriginal']."</td>
                                            <td>".$in['horaFinComidaOriginal']."</td>
                                            <td>".$in['horaSalidaOriginal']."</td>
                                            <td>".$in['nombre']."</td>
                                            <td>".$in['idnomp']."</td>
                                        </tr>"; 

                            $htmlHorariosModificados.=
                                "       <tr>
                                            <td>".$in['apellidoPaterno']." ".$in['apellidoMaterno']." ".$in['nombreEmpleado']."</td>
                                            <td>".$in['fechaoriginal']."</td>
                                            <td>".$in['diaoriginal']."</td>
                                            <td>".$in['horaEntradaModificado']."</td>
                                            <td>".$in['horaInicioComidaModificado']."</td>
                                            <td>".$in['horaFinComidaModificado']."</td>
                                            <td>".$in['horaSalidaModificado']."</td>
                                            <td>".$in['nombre']."</td>
                                            <td>".$in['idnomp']."</td>
                                        </tr>
                                ";
                       
                            $empleado = $in['idEmpleado'];         
                            $idnomp = $in['idnomp']; 
                            $diacompleto2 = $in['diacompleto'];  
                            
                        } //fin while
                        ?>
                        <div class="col-md-12 table-responsive alert alert-info"> 
                            <br>
                            <div style="text-align: right;" class="col-md-12">
                                <a href="#" class="btn btn-primary btn-sm" onclick="AutorizarEmpleado('<?php echo $empleado; ?>','<?php echo  $idnomp;?>','<?php echo  $diacompleto2;?>')">
                                 <span class="glyphicon glyphicon-floppy-disk" ></span> Autorizar empleado
                                </a> 
                                <a href="#" class="btn btn-danger btn-sm" onclick="Eliminartodo('<?php echo $empleado; ?>','<?php echo  
                                $idnomp;?>','<?php echo  $diacompleto2;?>')">
                                 <span class="glyphicon glyphicon-trash" ></span> Anular cambios
                                </a> 



                                 
                            </div> 
                            <br>
                            <br>
                            <div class="col-md-6 table-responsive alert alert-success">
                                 <b>HORARIO ACTUAL</b><br><br>
                                        <?php  echo 
                                         $htmlTablaHorariosActuales.
                                         $htmlHorariosActuales.
                                    "</tbody>
                                </table>
                            </div>"
                            ; ?>
                            <div class="col-md-6 table-responsive alert alert-danger">
                                <b>HORARIO MODIFICADO</b><br><br>
                                        <?php  echo 
                                         $htmlTablaHorariosModificados.   
                                         $htmlHorariosModificados.
                                    "</tbody>
                                </table>
                            </div>"
                            ; ?>
                        </div>
                    </div>
                    <?php
                } ?>

                <?php  if ($diacompletoentradas->num_rows==0 && $entradasoriginales->num_rows==0) { 
                    ?>
                    <br>
                    <div class='alert alert-info' style="text-align: center;"><h4>NO TIENE HORARIOS MODIFICADOS</h4>
                    </div>      
                    <?php  
                } ?>



      </div>
    </div>

                    