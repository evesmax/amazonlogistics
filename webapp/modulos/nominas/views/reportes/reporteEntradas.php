<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
    <link   rel="stylesheet" type="text/css" href="css/reporteentradas.css">
    <link rel="stylesheet" type="text/css"   href="css/registroentradas.css">  
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
    <script type="text/javascript" src='js/reporteEntradas.js'></script>
    <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
    <!-- <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script> -->
</head>
<body>
    <br class="ocultos">
    <div style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;" class="container-fluid ocultos"><b>
        Reporte de entradas y salidas Empleado
    </b>
    </div>
    <br class="ocultos">
    <!-- <form class="ocultos" method="post" action="index.php?c=reporteentradas&f=reporteEntradas" id="formentradas"> -->
        <div class="container well ocultos border" style="width: 96%;">
            <fieldset class="scheduler-border">
                <legend align="center" class="scheduler-border">Búsqueda</legend>
                <div class="form-inline" id="mostrarfecha">
                    <div class="col-md-3"> 
                        <label>Fecha Inicio:</label>   
                        <input type="text" id="fechainicio" name="fechainicio" class="form-control" 
                        value="<?php echo @$_REQUEST['fechainicio'];?>" style="width: 60%;">
                    </div>
                    <div class="col-md-3">
                        <label>Fecha Fin:</label>
                        <input type="text" id="fechafin" name="fechafin" class="form-control" 
                        value="<?php echo @$_REQUEST['fechafin'];?>" style="width: 60%;">
                    </div>
                    <div class="col-md-3">
                        <label>Empleado:</label>
                        <select id="empleado"  class="selectpicker btn-sm form-control" data-live-search="true" name="empleado" data-width="60%">
                            <option value="*">Todos</option>
                            <?php 
                            while ($e = $empleados->fetch_object()){
                                echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.'  </option>'; }?>      
                            </select>  
                        </div>
                        <div class="col-md-3">
                            <label>Sucursal:</label>
                            <select id="sucursal"  class="selectpicker btn-sm form-control" data-live-search="true" name="sucursal" data-width="60%">
                                <option value="*">Todos</option>
                                <?php 
                                while ($e = $sucursal->fetch_object()){
                                    echo '<option value="'. $e->idSuc .'" '. $b .'>'. $e->nombre .'  </option>'; }?>
                                </select> 
                            </div>
                        </div>
                    </fieldset>   
                </div>
                <!-- </form> -->

                <div class="panel-group container well border" style="width: 96%;">
                    <div class="panel panel-default border">
                        <div class="panel-heading border"   style="text-align: right;">  
                            <button type="button" class="btn btn-primary btn-sm ocultos" id="load" style="text-align:center" data-loading-text="Consultando<i class='fa fa-refresh fa-spin '></i>">Generar Reporte</button>
                            <a type="button" id="impresion" class="btn btn-info btn btn-sm ocultos" href="javascript:window.print();" hidden="true" onclick="printl()">
                                <img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0" >
                            </a> 
                        </div>
                        <div class="panel-body border" id="llenarentradas">           
                            <?php  if($reporteEntradas->num_rows==0) { ?>

                            <div class='alert alert-info' style='overflow-x: scroll;'>
                                <table cellpadding='0' class='tablaentradas table table-striped table-bordered dt-responsive nowrap' width='100%'; style='border:solid .3px;font-size:12.5px;' border='1' bordercolor='#0000FF'>
                                    <thead> 
                                        <tr style='background-color:#B4BFC1;color:#000000'>
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
                                </table>
                            </div>
                            <?php 
                        } ?>   
                    </div> 
                    <!--div de panel body-->
                </div>
            </div>
        </body>
        </html>
