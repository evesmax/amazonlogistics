<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Modulo de Repartidores</title>
    <link rel="stylesheet" href="">
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
    <script src="js/repartidores/repartidores.js"></script>
<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <!--Data Tables -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RC -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>

    <!--Button Print css -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
    <!--Button Print js -->
    <script src="../../libraries/dataTable/js/dataTables.buttons.min.js"></script>
    <script src="../../libraries/dataTable/js/buttons.print.min.js"></script>
    <!--  Notify  -->
        <script src="js/notify.js"></script>
    <body> 
        <?php 
            //print_r($configuracion);
         ?> 
    <!-- Empleados -->
        <h3>Asignar Pedidos</h3>
        <div class="row">

            <div class="col-md-12"><?php
                $clases[0] = 'default';
                $clases[1] = 'success';
                $clases[2] = 'warning';
                $clases[3] = 'primary';
                $clases[4] = 'danger';
                $clases[5] = 'info';
                        
                $posi = 0;
                
                foreach ($_SESSION['permisos']['empleados'] as $key => $value) { ?>
                    <?php  /// pide contraseña basandose en la  configuracion 
                        if($configuracion['pedir_pass'] == 1){                                    
                    ?>
                    <div class="pull-left" style="padding:5px">
                        <button 
                            type="button" 
                            class="btn btn-<?php echo $clases[$posi] ?> btn-lg" 
                            onclick="comandas.modal_login({empleado:'<?php echo $value['usuario'] ?>', id:'<?php echo $value['id'] ?>'})" 
                            style="width: 110px;"                             
                            data-toggle="modal" 
                            data-target="#modal_pass">
                            <i class="fa fa-user"></i> <br>
                            <i style="font-size: 15px" ><?php echo substr($value['usuario'], 0, 8); ?></i>
                        </button>

                     <?php          
                        }
                    ?>

                    <?php  /// no pide contraseña basandose en la configuracion 
                        if($configuracion['pedir_pass'] == 2){                                    
                    ?>
                    <div class="pull-left" style="padding:5px">
                        <button 
                            type="button" 
                            class="btn btn-<?php echo $clases[$posi] ?> btn-lg" 
                            onclick="comandas.listar_asignacion({empleado:'<?php echo $value['usuario'] ?>', id:'<?php echo $value['id'] ?>'})" 
                            style="width: 110px;"                             
                            data-toggle="modal" 
                            data-target="#modal_mesas">
                            <i class="fa fa-user"></i> <br>
                            <i style="font-size: 15px" ><?php echo substr($value['usuario'], 0, 8); ?></i>
                        </button>

                     <?php          
                        }
                    ?>

                    </div><?php
                
                    $posi++;
                    $posi = ($posi > 5) ? 0 : $posi;
                    
                } ?>
            </div>
        </div>
    <!-- FIN Empleados -->

        <div class="container panel col-xs-12 col-md-12" >
            <div class="row">
                <div class="col-xs-12 col-md-12">
                   <h3>Pedidos Repartidores</h3>
                </div>
            </div>
            <div class="container panel">
                <div class="container" style="overflow:auto">
                    <div class="col-sm-12">
                        <table id="table_pedidos" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th td width="30">Pedido</th>
                            <th td width="30">Repartidor</th>
                            <th td width="30">Tiempo de Entrega</th>
                            <th td width="30">Confirmacion de Entrega</th>
                          </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        <tfoot>
                            
                        </tfoot>
                      </table>
                    </div>
                </div>
            </div>
        </div>


    <!-- Modal pass-->
        <div class="modal fade" id="modal_pass" tabindex="-1" role="dialog" aria-labelledby="titulo_pass" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn_cerrar_pass" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="titulo_pass">Ingresar</h4>
                    </div>
                    <div class="modal-body">
                        <input readonly="1" id="id_empleado" type="text" class="form-control" style="visibility:hidden" />
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-user"></i> </span>
                                    <input readonly="1" id="empleado" type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-lock"></i> </span>
                                    <input autocomplete="off" name="empleado" onkeypress="if(((document.all) ? event.keyCode : event.which)==13) comandas.iniciar_sesion({empleado: $('#id_empleado').val(),pass:$('#pass_empleado').val()})" id="pass_empleado" type="password" class="form-control" autofocus="autofocus">
                                </div>
                            </div>
                        </div>
                    </div>
                
                <!-- Botones -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"  onclick="comandas.iniciar_sesion({empleado: $('#id_empleado').val(),pass:$('#pass_empleado').val()})">
                            <i class="fa fa-sign-in"></i> Entrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <!-- FIN Modal pass-->
    
    <!-- Modal Mesas -->
        <div class="modal fade" style="height:95%" id="modal_mesas" tabindex="-1" role="dialog" aria-labelledby="titulo_mesas" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn_cerrar_mesas" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="titulo_mesas">Seleccionar</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row" style="overflow: scroll;height:55%">
                            <div class="col-xs-12" id="contenedor">
                                <!-- En esta div se cargar las mesas -->
                                    
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-xs-12">
                                <button id="btn_guardar" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"  onclick="comandas.guardar_asignacion({empleado: $('#id_empleado').val(),vista:1})" class="btn btn-success btn-lg" type="button">
                                    <i class="fa fa-check"></i> Guardar
                                </button>
                                <button id="cerrar_modal" type="button" class="btn btn-danger btn-lg" data-dismiss="modal">
                                    <i class="fa fa-ban"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- FIN Modal Mesas -->
    </body>
    <button onclick="listarpedidosRep()"></button>
</html>
<script>
    //comandas.listar_mesas({div:'contenedor', asignar:1});

    $(document).ready(function() {
        autoload();
    });
</script>
</script>