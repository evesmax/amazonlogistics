<head>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">

    <!-- <link rel="stylesheet" type="text/css" href="css/datatablesboot.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.min.css"> -->
    <style>
        .p0{padding:0;}
        .glyphicon-refresh-animate {
    -animation: spin .7s infinite linear;
    -ms-animation: spin .7s infinite linear;
    -webkit-animation: spinw .7s infinite linear;
    -moz-animation: spinm .7s infinite linear;
}
@keyframes spin {
    from { transform: scale(1) rotate(0deg);}
    to { transform: scale(1) rotate(360deg);}
}
  
@-webkit-keyframes spinw {
    from { -webkit-transform: rotate(0deg);}
    to { -webkit-transform: rotate(360deg);}
}

@-moz-keyframes spinm {
    from { -moz-transform: rotate(0deg);}
    to { -moz-transform: rotate(360deg);}
}


    </style>
</head>
<?php
//require "views/partial/modal-generico.php";
?>

<div class="container well" style="margin-top: 20px;">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-12 col-md-12"><h3>Configuraci&oacute;n Produccion</h3></div>
    </div>
    <div class="row">
       <!-- Nav tabs -->
      <ul id='myTabs' class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#ejercicios" aria-controls="ejercicios" role="tab" data-toggle="tab">Ordenes de produccion</a></li>
        <li role="presentation"><a href="#periodos" aria-controls="periodos" role="tab" data-toggle="tab">Ordenes de compra</a></li>
        <li role="presentation"><a href="#metcosteo" aria-controls="metcosteo" role="tab" data-toggle="tab">Inventario produccion</a></li>
        <li role="presentation"><a href="#impuestos" aria-controls="impuestos" role="tab" data-toggle="tab">Fabricacion</a></li>
        <!-- <li role="presentation"><a href="#notificaciones" aria-controls="notificaciones" role="tab" data-toggle="tab">Notificaciones</a></li>
        <li role="presentation"><a href="#otros" aria-controls="otros" role="tab" data-toggle="tab">Otros</a></li> -->
      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade in active" id="ejercicios">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Seleccione las opciones</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Agrupacion por insumos en productos.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="agins" value="1" <?php if($agins==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="agins" value="0" <?php if($agins==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row"  style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Generacion automatica de ordenes de produccion.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="gaop" value="1" <?php if($gaop==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="gaop" value="0" <?php if($gaop==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row"  style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Generacion automatica desde pedidos.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="gap" value="1" <?php if($gap==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="gap" value="0" <?php if($gap==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Autorización para procesar ordenes de producción.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="apop" value="1" <?php if($apop==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="apop" value="0" <?php if($apop==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Notificaciones por correo electronico.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="notc" value="1" <?php if($notc==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="notc" value="0" <?php if($notc==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Se heredan datos de la orden padre (prioridad, fecha registro, fecha entrega)
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="hereda" value="1" <?php if($hereda==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="hereda" value="0" <?php if($hereda==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Se necesitan los insumos, directos o transformados en almacen para iniciar orden de producción.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="insdir" value="1" <?php if($insdir==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="insdir" value="0" <?php if($insdir==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>
                
                
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                        Generacion de ordenes de produccion por LOTES
                    </div>
                    <div class="col-md-2">
                     Si
                      <input type="radio" name="ord_x_lotes" value="1" <?php if($ord_x_lotes==1){ echo 'checked="checked"'; } ?>  style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                     No 
                      <input type="radio" name="ord_x_lotes" value="0" <?php if($ord_x_lotes==0){ echo 'checked="checked"'; } ?>  style="cursor:pointer;">
                    </div>
                  </div>
                </div>
                
                <!-- Explosión de materiales (Simple/ Multiple) -->
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                        Explosión de materiales
                    </div>
                    <div class="col-md-2">
                      Simple
                      <input type="radio" name="explomate" value="1" <?php if($explomate==1){ echo 'checked="checked"'; } ?>  style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                     Multiple 
                      <input type="radio" name="explomate" value="2" <?php if($explomate==2){ echo 'checked="checked"'; } ?>  style="cursor:pointer;">
                    </div>
                  </div>
                </div>


                <!-- Tipo de registro Ordenes de Producción -->
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                        Tipo de registro Ordenes de Producción 
                    </div>
                    <div class="col-md-2">
                      Simple
                      <input type="radio" name="ordenprod" value="1" <?php if($ordenprod==1){ echo 'checked="checked"'; } ?>  style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                     Multiple 
                      <input type="radio" name="ordenprod" value="2" <?php if($ordenprod==2){ echo 'checked="checked"'; } ?>  style="cursor:pointer;">
                    </div>
                  </div>
                </div>
                
                
                <div class="row" style="margin-top: 40px;">
                  <div class="col-md-12">
                      <center><button class='btn btn-default' onclick='guardar(1)' id='btn-1'>Guardar <span class='glyphicon glyphicon-ok'></span></button></center>
                  </div> 
                </div>

              </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="periodos">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Seleccione las opciones</h3>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Ordenes de compra sean a una sola orden de producción.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="ocop" value="1" <?php if($ocop==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="ocop" value="0" <?php if($ocop==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Generar Orden de Compra sin Requisición.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="ocsinr" value="1" <?php if($ocsinr==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="ocsinr" value="0" <?php if($ocsinr==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>
                <!-- Mostrar proveedor -->
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Mostrar proveedor
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="mostprovee" value="1" <?php if($mostprovee==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="mostprovee" value="0" <?php if($mostprovee==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 40px;">
                  <div class="col-md-12">
                      <center><button class='btn btn-default' onclick='guardar(2)' id='btn-1'>Guardar <span class='glyphicon glyphicon-ok'></span></button></center>
                  </div> 
                </div>

              </div>
            </div>

           
        </div>
        <div role="tabpanel" class="tab-pane fade" id="metcosteo">
             <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Seleccione las opciones.</h3>
              </div>
              <div class="panel-body">
                 <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      Designación de almacenes para el proceso de producción.
                    </div>
                    <div class="col-md-2">
                      Producto 
                      <input type="radio" name="deaalm" value="1" <?php if($deaalm==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      Insumos 
                      <input type="radio" name="deaalm" value="2" <?php if($deaalm==2){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      Muestras 
                      <input type="radio" name="deaalm" value="0" <?php if($deaalm==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Generación automatica salida almacen insumos.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="salins" value="1" <?php if($salins==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="salins" value="0" <?php if($salins==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Utilizar inventario de apartado.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="invprod" value="1" <?php if($invprod==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="invprod" value="0" <?php if($invprod==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 40px;">
                  <div class="col-md-12">
                      <center><button class='btn btn-default' onclick='guardar(3)' id='btn-1'>Guardar <span class='glyphicon glyphicon-ok'></span></button></center>
                  </div> 
                </div>
              </div>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="impuestos">
             <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Seleccione las opciones.</h3>
              </div>
              <div class="panel-body">
              <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Capturar pasos en procesos de produccion.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="capaso" value="1" <?php if($capaso==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="capaso" value="0" <?php if($capaso==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>


                <div class="row"  style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      Designación de almacenes para el proceso de producción.
                    </div>
                    <div class="col-md-2">
                      Producto 
                      <input type="radio" name="deaalmx" value="1" <?php if($deaalm==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      Insumos 
                      <input type="radio" name="deaalmx" value="2" <?php if($deaalm==2){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      Muestras 
                      <input type="radio" name="deaalmx" value="0" <?php if($deaalm==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Generación automatica salida almacen insumos.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="salinsx" value="1" <?php if($salins==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="salinsx" value="0" <?php if($salins==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>

                <!-- agregado uso insumos variables AM-->

                 <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                     Uso de insumos variables
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="insumvar" value="1"  style="cursor:pointer;" <?php if($insumvar==1){ echo 'checked="checked"'; } ?>  >
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="insumvar" value="0" style="cursor:pointer;" <?php if($insumvar==0){ echo 'checked="checked"'; } ?> >
                    </div>
                  </div>
                </div>
              <!-- agregado uso insumos variables -->

              <!-- agregado Generación de Ordenes de Producción desde Pedidos AM-->
                 <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                     Generación de Ordenes de Producción desde Pedidos
                    </div>
                    <div class="col-md-2">
                      <!-- produccion_pedidos -->
                      Si 
                      <input type="radio" name="productPedidos" value="1"  style="cursor:pointer;" <?php if($productPedidos==1){ echo 'checked="checked"'; } ?>  >
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="productPedidos" value="0" style="cursor:pointer;" <?php if($productPedidos==0){ echo 'checked="checked"'; } ?> >
                    </div>
                  </div>
                </div>
                <!-- agregado Generación de Ordenes de Producción desde Pedidos AM-->
                
 				<!-- agregado de reabastecimiento de insumos krmn  --> 
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-12">
                    <div class="col-md-8">
                      Solicitud de reabasto.
                    </div>
                    <div class="col-md-2">
                      Si 
                      <input type="radio" name="abasto" value="1" <?php if($reabasto_insumos==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                    <div class="col-md-2">
                      No 
                      <input type="radio" name="abasto" value="0" <?php if($reabasto_insumos==0){ echo 'checked="checked"'; } ?>   style="cursor:pointer;">
                    </div>
                  </div>
                </div>
			<!--  fin agregado de reabastecimiento de insumos krmn  --> 

                <div class="row" style="margin-top: 40px;">
                  <div class="col-md-12">
                      <center><button class='btn btn-default' onclick='guardar(4)' id='btn-1'>Guardar <span class='glyphicon glyphicon-ok'></span></button></center>
                  </div> 
                </div>
              </div>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="notificaciones">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Notificar (Mandar un email) por cada recepcion y requisicion.</h3>
              </div>
              <div class="panel-body">
                  <div class="col-xs-12 col-md-2 col-md-offset-4">Compras: </div> <div id='col-xs-12 col-md-2'><input type='text' id='not_compras' value='<?php //echo $not_compras; ?>'></div>
                  <div class="col-xs-12 col-md-2 col-md-offset-4">Ventas: </div> <div id='col-xs-12 col-md-2'><input type='text' id='not_ventas' value='<?php //echo $not_ventas; ?>'></div>
                  <div class="col-xs-12 col-md-2 col-md-offset-4">Cortes de Caja: </div> <div id='col-xs-12 col-md-2'><input type='text' id='not_cortes' value='<?php //echo $not_cortes; ?>'></div>
                  <div class="col-xs-12 col-md-5 col-md-offset-4"><label style='font-size:10px;color:gray;'>*Si dejas los campos vacios no se enviará notificaciones, para incluir varias direcciones de email agregar comas (,) ejemplo alguien@empresa.com,otro@espresa.com</label></div>
                  <div class="col-xs-12 col-md-5 col-md-offset-5" style='margin-top:20px;'><button id='guarda_not' class='btn btn-default' onclick='guardar(3)'>Guardar <span class="glyphicon glyphicon-ok"></span></button>
                  </div>
              </div> 
            </div>
        </div>
        

        <div role="tabpanel" class="tab-pane fade" id="otros">
             <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Seleccione las opciones.</h3>
              </div>
              <div class="panel-body">
                <div class="col-xs-12 col-md-3 col-md-offset-4">
                   
                   <center><button id='reiniciar' onclick='reiniciar()' class='btn btn-danger'>Reiniciar Sistema</button></center>
                   
                </div> 
              </div>
          <hr />
              <div class="row">
                  <div class="col-xs-12 col-md-2 col-md-offset-4">Dias para cancelar factura: </div> <div id='col-xs-12 col-md-6'><input type='text' id='dias_canc' value='<?php echo $dias_canc; ?>'></div> <br>
              <div class="row">
                  <div class="col-xs-12 col-md-2 col-md-offset-4">&nbsp;Dias para emitir factura: </div> <div id='col-xs-12 col-md-6'><input type='text' id='dias_emit' value='<?php echo $dias_emit; ?>'></div>
              </div>
              <div class="row">
                  <div class="col-xs-12 col-md-5 col-md-offset-4"><label style='font-size:10px;color:gray;'>*Si dejas los campos en cero se tomará soló los dias restantes del mes actual</label></div>
              </div>
              <hr />
              <!--<div class="row">
                <div class="col-xs-12 col-md-5 col-md-offset-4">
                    <input type='checkbox' id='pol_aut' onclick='pol_aut()' value='1' <?php echo $checkedPol; ?>> *Crear polizas automaticamente?
                    <br /><label style='font-size:10px;color:gray;'>*Crear un boton en el modulo de compras y ventas que genere las polizas <br />si se cuenta con el modulo Acontia.</label>
                </div> 
              </div>-->
              <div class="row">
                  <div class="col-xs-12 col-md-3 col-md-offset-4" style='margin-top:20px;'><center><button id='guarda_not' class='btn btn-default' onclick='guardar(4)'>Guardar <span class="glyphicon glyphicon-ok"></span></button></center>
                  </div>
              </div> 
            </div>
        </div>

      </div>
    </div>
</div>
<script src="../../libraries/jquery.min.js" type="text/javascript"></script>
<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../libraries/select2/dist/js/select2.min.js" type="text/javascript"></script>

<script>
$(function(){

  // $('#myTabs a').click(function (e) {
  //   e.preventDefault()
  //   $(this).tab('show')
  // })
});

function guardar(op){

    gaop=$('input[name=gaop]:checked').val();
    gap=$('input[name=gap]:checked').val();
    apop=$('input[name=apop]:checked').val();
    notc=$('input[name=notc]:checked').val();
    hereda=$('input[name=hereda]:checked').val();
    insdir=$('input[name=insdir]:checked').val();
    ocop=$('input[name=ocop]:checked').val();
    ocsinr=$('input[name=ocsinr]:checked').val();
    deaalm=$('input[name=deaalm]:checked').val();
    salins=$('input[name=salins]:checked').val();
    capaso=$('input[name=capaso]:checked').val();
    invprod=$('input[name=invprod]:checked').val();
    agins=$('input[name=agins]:checked').val();
    insumvar=$('input[name=insumvar]:checked').val();
    explomate=$('input[name=explomate]:checked').val();
    ordenprod=$('input[name=ordenprod]:checked').val();
    mostprovee=$('input[name=mostprovee]:checked').val();
    productPedidos=$('input[name=productPedidos]:checked').val();
    reabasto_insumos=$('input[name=abasto]:checked').val();
    ord_x_lotes=$('input[name=ord_x_lotes]:checked').val();


    $.ajax({
      url:"ajax.php?c=config&f=saveConfig",
      type: 'POST',
      data:{opcion:op,gap:gap,apop:apop,notc:notc,hereda:hereda,insdir:insdir,ocop:ocop,ocsinr:ocsinr,deaalm:deaalm,salins:salins,capaso:capaso,gaop:gaop,invprod:invprod,agins:agins,insumvar:insumvar,explomate:explomate,ordenprod:ordenprod,mostprovee:mostprovee,productPedidos:productPedidos,reabasto_insumos:reabasto_insumos,ord_x_lotes:ord_x_lotes},
      success: function(resp){
        //window.location.reload();
        alert('Cambios guardados con exito');

      }
    });
  

}
</script>
