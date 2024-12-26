<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tarjetas de Regalo / Monedero Electrónico</title>
    <link rel="stylesheet" href="">
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/ventas.js"></script>
    <script src="../../libraries/numeric.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <!-- Modificaciones RC -->
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">

   <script>
   $(document).ready(function() {
        reloadtable(1); 
        $("#puntosRep, #clienteRep").attr('readonly', 'readonly');
        $("#divMonedero").hide();

        $('.numeros').numeric();
            // $('#tableGrid').DataTable({
            //                 dom: 'Bfrtip',
            //                 buttons: ['excel'],
            //                 language: {
            //                 search: "Buscar:",
            //                 lengthMenu:"",
            //                 zeroRecords: "No hay datos.",
            //                 infoEmpty: "No hay datos que mostrar.",
            //                 info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
            //                 paginate: {
            //                     first:      "Primero",
            //                     previous:   "Anterior",
            //                     next:       "Siguiente",
            //                     last:       "Último"
            //                 },
            //              },
            //               aaSorting : [[0,'desc' ]]
            // });
            $("#layout_row").attr("abierto","0").hide();
            $("#layout_precios").attr("abierto","0").hide();
            $("#tipo").select2();
            // $("#tipo").val(1).trigger('change');
   });
   function mostrar_layout()
   {
    if(!parseInt($("#layout_row").attr("abierto")))
        $("#layout_row").attr("abierto","1").show("slow");
    else
        $("#layout_row").attr("abierto","0").hide("slow");
   }
   /* function mostrar_layout2()
   {
    if(!parseInt($("#layout_precios").attr("abierto")))
        $("#layout_precios").attr("abierto","1").show("slow");
    else
        $("#layout_precios").attr("abierto","0").hide("slow");
   } */


   function validar(t)
   {
    if(t.layout.value == '')
    {
        alert("Agregue un archivo xls.");
        return false;
    }

   }
           function xyx(){
          
            var lil = $('#tipoCard').val();

            if(lil==1){
                $('.porcen').show();
                $('.equival').hide();
            }else{
                $('.porcen').hide();
                $('.equival').show();
            }
        }
   </script>
<body>  
    <br>
<div class="container well">
   
    <h3>Tarjetas de Regalo y Monedero Electrónico</h3>
    <div>
        <label>Tipo:</label><br>
        <select id="tipo">
            <option value="1">Tarjeta de regalo</option>
            <option value="2">Monederos electrónicos</option>
        </select>    
    </div><br>
    <div class="row">
        <div id="divTarjeta">
            <div class='col-sm-2 col-md-2'>
                <!-- <button class="btn btn-primary" onclick="newCard();"><i class="fa fa-plus" aria-hidden="true"></i> Tarjeta Nueva</button> -->
            </div>
        </div>
        <div id="divMonedero">
            <div class='col-sm-6 col-md-6'>
                <button class="btn btn-primary" onclick="newMonedero();"><i class="fa fa-plus" aria-hidden="true"></i>Nuevo Monedero</button>
                <button class="btn btn-primary" onclick="newPolitic();"><i class="fa fa-plus" aria-hidden="true"></i> Politica Nueva</button>
                <button class="btn btn-warning" onclick="reposicionM();"><i class="fa fa-sync-alt" aria-hidden="true"></i>Reposicion de Monedero</button>
            </div>
        </div>
       
        
    <!--    <div class='col-sm-12 col-md-2'>   
           <button class="btn btn-default" title='Subir productos mediante layout' onclick='mostrar_layout()'><span class='glyphicon glyphicon-upload'></span></button>
        </div> -->
    <!--     <div class='col-sm-12 col-md-2'>   
           <button class="btn btn-default" title='Subir productos mediante layout' onclick='mostrar_layout2()'><span class='glyphicon glyphicon-tags'></span></button>
        </div> -->
    </div>
    <div class='row' id='layout_row'>
        <div class='col-sm-12 col-md-offset-2 col-md-5'>
            <b>Subir productos mediante layout</b> / <a href='importacion/productos.xls'>Descargar</a><br />
            <form action='index.php?c=producto&f=subeLayout' method='post' name='archivo' enctype="multipart/form-data" id='arch' onsubmit='return validar(this)'>
                <input type='file' id='layout' name='layout'><br />
                <button type='submit' onclick='cargar_productos()'>Cargar</button>
            </form>
        </div>
    </div>
    <!-- Actualzia Precios -->
 <!--   <div class='row' id='layout_precios'>
        <div class='col-sm-12 col-md-offset-2 col-md-5'>
            <b>Actualizar precios mediante layout</b><br />
            <form action='index.php?c=producto&f=subeLayoutPrecios' method='post' name='archivo' enctype="multipart/form-data" id='arch' onsubmit='return validar(this)'>
                <input type='file' id='layout' name='layout'><br />
                <button type='submit' onclick='cargar_productos()'>Cargar</button>
            </form>
        </div>
    </div> -->
    <div class="row">
        <div class="col-sm-12">
            <!-- <label>Total: <?php echo $tarjetas['total']; ?></label> -->
        </div>
    </div>
    <div class="row">
        <div id="divMonederoT" class="col-sm-12" style="overflow:auto;display: none;">
            <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableMonedero">
                    <thead>
                        <tr>
                            <th># Monedero</th>
                            <th>Puntos</th>
                            <th>Cliente</th>
                            <th>Estatus</th> 
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            
        </div>
        <div id="divTarjetaT" class="col-sm-12" style="overflow:auto;display: none;">

            <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableTarjeta">
                    <thead>
                        <tr>
                            <th># Tarjeta</th>
                            <th>Valor</th>
                            <th>Monto Usado</th>
                            <th>Disponible</th> 
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table> 
            
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" style="overflow:auto;">

 
   
            


<!--                      <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGrid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Numero</th>
                        <th>Valor</th>
                        <th>Estatus</th>
                        <th>Monto Usado</th>
                        <th>Disponible</th>
                        <th>Puntos</th>
                        <th>Cliente</th>
                        <th>Modificar</th>

                      </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $status = '';
                        $total = 0;
                        foreach ($tarjetas['tarjetas'] as $key => $value) {
                            $disponible = $value['valor'] - $value['montousado'];
                            if($value['usada']!=1){

                                $status = '<span class="label label-success">Activo</span>';
                                //$botones = '<a href="#" class="btn btn-primary btn-xs active" onclick="modificaGiftCard('.$value['id'].');"><span class="glyphicon glyphicon-edit"></span> Editar</a><a href="#" class="btn btn-danger btn-xs active" onclick="desactivaGift('.$value['id'].');"><span class="glyphicon glyphicon-remove"></span> Desactivar</a>';
                                $botones = '<a href="#" class="btn btn-danger btn-xs active" onclick="desactivaGift('.$value['id'].');"><span class="glyphicon glyphicon-remove"></span> Desactivar</a>';
                            }else{
                                $status = '<span class="label label-danger">Inactivo</span>';
                                if($disponible > 0 || ($value['puntos'] > $value['montousado'])){
                                    $botones = '<a href="#" class="btn btn-info btn-xs active" onclick="activaGift('.$value['id'].');"><span class="glyphicon glyphicon-check"></span> Activar</a>';
                                }else{
                                    $botones = '<a href="#" class="btn btn-warning btn-xs active" ></span> Sin saldo</a>';
                                }
                                
                                
                            }
                            echo '<tr>';
                            echo '<td>'.$value['id'].'</td>';
                            echo '<td>'.$value['numero'].'</td>';
                            echo '<td>$'.number_format($value['valor'],2).'</td>';
                            echo '<td>'.$status.'</td>';
                            echo '<td>$'.number_format($value['montousado'],2).'</td>';
                            
                            echo '<td>$'.$disponible.'</td>';

                            echo '<td>'.$value['puntos'].'</td>';
                            echo '<td>'.$value['cliente'].'</td>';
                            echo '<td>';
                           // echo '<a href="index.php?c=producto&f=index&idProducto='.$value['id'].'" class="btn btn-primary btn-xs active"><span class="glyphicon glyphicon-edit"></span> Editar</a> ';
                            //echo '<a href="#" class="btn btn-danger btn-xs active" onclick="borraProducto('.$value['id'].');"><span class="glyphicon glyphicon-remove"></span> Borrar</a>';
                            echo $botones;
                            echo '</td>';

                            echo '</tr>';
                            
                        }
                        ?>
                    </tbody>
                </table> -->


        </div>
    </div>
</div>
        <!--          Molda Warning           -->
  <div id="modalElimina" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Eliminar!</h4>
            </div>
            <div class="modal-body">
                <p>Deseas desactivar este producto?</p>
                <input type="hidden" id="eliminaProd">
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf2-uno" type="button" class="btn btn-danger" onclick="borraProducto2();">Eliminar</button> 
            </div>
        </div>
    </div> 
  </div>


<div id="modalActiva" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-warning">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Activar!</h4>
            </div>
            <div class="modal-body">
                <p>Deseas activar este producto?</p>
                <input type="hidden" id="activaProd">
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf2-uno" type="button" class="btn btn-danger" onclick="activar2();">Activar</button> 
            </div>
        </div>
    </div> 
  </div>

<div id="modal-giftCard" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-default">
            <div class="modal-header panel-heading">
            <button class="close" type="button" data-dismiss="modal">×</button>
                <h4 id="modal-label">Nueva Tarjeta de Regalo</h4>
                <input type="hidden" id="idGiftCard">
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <label for="">ID Venta</label>
                    <input type="text" class="form-control" id="idVenta" >
                </div>
                <div class="col-sm-6" style="padding-top: 25px;">
                    <button id="btnbuscaVenta" class="btn btn-primary" onclick="verificaVenta();">Buscar</button>
                </div>
            </div>
            <div id="divtarjetas">
                
            </div>
<!--             <div class="row">
           <div class="col-sm-6">
                    <label for="">Numero de Tarjeta</label>
                    <input type="text" class="form-control" id="numeroTarjeta" >
                </div>
                <div class="col-sm-6">
                    <label for="">Monto</label>
                    <input type="text" class="form-control numeros" id="montoTarjeta">
                </div> 
            </div>-->
            <!-- <div class="row">
                <div class="col-sm-6">
                    <label for="">Puntos</label><input type="text" class="form-control numeros" id="puntos">
                </div>
                <div class="col-sm-1">
                    <label for=""> </label>
                    <button class="btn btn-default" onclick="clienteAddButton();"><i class="fa fa-user-plus" aria-hidden="true"></i></button>
                </div>
                <div class="col-sm-5">
                    <label for="">Cliente</label>
                    <select  class="form-control" id="cliente">
                        <?php foreach ($clientes as $key => $value) { ?>
                            <option value="<?php echo $value['id'] ?>"><?php echo $value['nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div> -->


            <div class="row" style="display:none;" id="modDiv">
                <div class="col-sm-6">
                    <label for="">Monto Usado</label>
                    <input type="text" class="form-control numeros" id="usado" >
                </div>
                <div class="col-sm-6">
                    <label for="">Monto Disponible</label>
                    <input type="text" class="form-control numeros" id="disponible" >
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <!-- <button class="btn btn-primary" onclick="saveGiftCard();">Guardar</button> -->
                <button class="btn btn-primary" onclick="saveTarjeta();">Guardar</button>
            </div>
        </div>
    </div> 
</div>

<div id="giftCardPolitics" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-default">
            <div class="modal-header panel-heading">
            <button class="close" type="button" data-dismiss="modal">×</button>
                <h4 id="modal-label">Nueva Politica</h4>
                <input type="hidden" id="idGiftCard">
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <label>Nombre</label>
                    <input type="text" id="namePolitic" class="form-control">
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-4">
                    <label>Tipo</label>
                    <select id="tipoCard" class="form-control" onchange="xyx();">
                        <option value="1">Procentaje</option>
                        <option value="2">Equivalencia</option>
                    </select>
                </div>
                <div class="col-xs-6 porcen">
                    <label>Porcentaje</label>
                    <input type="text" id="percent" class="form-control">
                </div> 
                <div class="col-xs-4 equival" style="display: none;">
                    <label>Dinero $</label>
                    <input type="text" id="money" class="form-control">
                </div>
                <div class="col-xs-4 equival" style="display: none;">
                    <label>Puntos</label>
                    <input type="text" id="points" class="form-control">
                </div>
            </div>
            <div class="row">
                
            </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="savePolitic();">Guardar</button>
            </div>
        </div>
    </div> 
</div>

    <div id='modalCliente' class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-user-plus fa-lg" aria-hidden="true"></i> Agregar Cliente </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div class="col-sm-2">
                        <label class="control-label" for="email">ID</label>
                        <input id="idCliente" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['id'];}?>" readonly placeholder="(Autonumerico)">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Codigo</label>
                          <input id="codigo" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['codigo'];}?>">
                      </div>
                      <div class="col-sm-7">
                          <div class="alert alert-warning"><strong>NOTA:</strong>Si el cliente se registra con RFC y Razon Social, automaticamente se ingresaran a sus datos de facturacion.</div>
                      </div>

                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <label class="control-label">Nombre</label>
                        <input id="nombre" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombre'];}?>">
                      </div>
                      <div class="col-sm-6">
                          <label class="control-label">Razon Social</label>
                          <input id="razonSocial" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombre'];}?>">
                      </div>

                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">RFC</label>
                        <input id="rfc2" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">CURP</label>
                        <input id="curp" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['curp'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Régimen Fiscal</label>
                          <input id="regimen" type="text" class="form-control">
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                          <label class="control-label">Tienda</label>
                          <input id="tienda" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombretienda'];}?>">
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <label class="control-label">Direccion</label>
                        <input id="direccion" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['direccion'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Exterior</label>
                        <input id="numext" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['num_ext'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Interior</label>
                        <input id="numint" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['num_int'];}?>">        
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Colonia</label>
                        <input id="colonia" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['colonia'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Codio Postal</label>
                          <input id="cp" class="form-control numeros" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['cp'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Estado</label>
                          <select id="estado" class="form-control" onchange="municipiosF();">
                            <option value="0">-Selecciona un estado</option>
                            <?php 
                              foreach ($estados as $key => $value) {
                                echo '<option value="'.$value['idestado'].'">'.$value['estado'].'</option>';
                              }
                            ?>
                          </select>
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Municipio</label>
                          <select  id="municipios" class="form-control">
                            <option value='0'>-Selecciona un municipio--</option>

                          </select>
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="control-label">Ciudad</label>
                            <input id="cdF" type="text" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Email</label>
                        <input id="email" class="form-control" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['email'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Celular</label>
                        <input id="celular" class="form-control numeros" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['celular'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Telefono 1</label>
                        <input id="tel1" class="form-control numeros" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['telefono1'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Telefono 2</label>
                        <input id="tel2" class="form-control numeros" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['telefono2'];}?>"> 
                      </div>      
                    </div>
                    


                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Dias de Credito</label>
                        <input id="diasCredito" class="form-control numeros" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['dias_credito'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Limite de Credito</label>
                        <input id="limiteCredito" class="form-control numeros" type="text" value="<?php 
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['limite_credito'];}?>"> 
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Moneda</label>
                        <!--<input id="moneda" class="form-control" type="text" value=""> -->
                        <select id="moneda" class="form-control">
                          <?php 
                    
                            foreach ($moneda as $keyMon => $valueMon) {
                              echo '<option value="'.$valueMon['coin_id'].'">'.$valueMon['description'].'/'.$valueMon['codigo'].'</option>';
                            }

                          ?>
                        </select>
                      </div>
                      <div class="col-sm-3"></div>
                    </div>

                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Lista de Precio</label>
                        <select id="listaPrecio" class="form-control">
                          <?php 
                          foreach ($listaPre as $key1 => $value1) {
                            echo '<option value="'.$value1['id'].'">'.$value1['nombre'].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-sm-3"></div>
                      <div class="col-sm-3"></div>
                      <div class="col-sm-3"></div>
                    </div>

                    <div class="row"><br>
                      <div class="col-sm-10"></div>
                      <div class="col-sm-1"></div>
                    </div>    
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button type="button" class="btn btn-primary" onclick="guardaCliente();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalSuccess" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Exito!</h4>
            </div>
            <div class="modal-body">
                <p>Tu Cliente se guardo existosamente</p>
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf2-uno" type="button" class="btn btn-default" onclick="cierramodales();">Continuar</button> 
            </div>
        </div>
    </div> 
  </div>

    <div id="modalReposicion" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content panel-warning">
                <div class="modal-header panel-heading">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 id="modal-label">Reposición de monedero</h4>
                    <input type="hidden" id="idGiftCard">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for=""># Monedero</label>
                            <input type="text" class="form-control" id="numMonedero" >
                        </div>
                        <div class="col-sm-4">
                            <label for="">Puntos</label>
                            <input type="text" class="form-control numeros" id="puntosRep">
                        </div>
                        <div class="col-sm-4">
                            <label for="">Cliente</label>
                            <input type="text" class="form-control numeros" id="clienteRep">
                            <input type="hidden" class="form-control numeros" id="idclienteRep">
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="">Nuevo # Monedero</label>
                            <input type="text" class="form-control numeros" id="numMonederoNew">
                        </div>    
                        <div class="col-sm-6">
                            <label for="">¿Conservar puntos?</label>
                            <div>
                                <input type="radio" id="si" name="rep" value="si" checked><label for="si">Si</label>
                                <input type="radio" id="no" name="rep" value="no"><label for="no">No</label>
                            </div>                        
                        </div>
                    </div>
                </div>            
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="saveNewRep();">Guardar</button>
                </div>
            </div>
        </div> 
    </div>

    <div id="modalMonederoNew" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content panel-warning">
                <div class="modal-header panel-heading">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 id="modal-label">Nuevo Monedero</h4>
                    <input type="hidden" id="idGiftCard">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for=""># Monedero</label>
                            <input type="text" class="form-control" id="numMonNew" >
                        </div>
                        <div class="col-sm-4">
                            <label for="">Puntos</label>
                            <input type="text" class="form-control numeros" id="puntosMonNew">
                        </div>
                        <div class="col-sm-4">
                            <label for="">Cliente</label>
                            <select id="selectCli">
                                <?php foreach ($clientes as $key => $value) { ?>
                                    <option value="<?php echo $value['id'] ?>"><?php echo $value['nombre'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div><br>
                </div>            
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="saveNewMon();">Guardar</button>
                </div>
            </div>
        </div> 
    </div>


</body>
</html>
<script>
    $("#tipo").change(function(event) {
        var tipo = $("#tipo").val();        
        if(tipo == 1){
            $("#divMonedero").hide();
            $("#divTarjeta").show();
            reloadtable(1);              
            
        }else{
            $("#divTarjeta").hide();
            $("#divMonedero").show();;
            reloadtable(2);
        
        }
    });
    function reloadtable(tipo){
        $.ajax({
            url: 'ajax.php?c=caja&f=reloadtable',
            type: 'POST',
            dataType: 'json',
            data: {tipo:tipo},
        })

        .done(function(data) {
            if(tipo == 1){                
                $('#divMonederoT').hide();
                
                var table   = $('#tableTarjeta').DataTable({
                                    autowidth: 'false',
                                    dom: 'Bfrtip',
                                    buttons: [ 'excel' ],
                                                    language: {
                                                    search: "Buscar:",
                                                    lengthMenu:"",
                                                    zeroRecords: "No hay datos.",
                                                    infoEmpty: "No hay datos que mostrar.",
                                                    info:"Mostrando del _START_ al _END_ de _TOTAL_ facturas",
                                                    paginate: {
                                                        first:      "Primero",
                                                        previous:   "Anterior",
                                                        next:       "Siguiente",
                                                        last:       "Último"
                                                    },
                                                 },
                                                  aaSorting : [[0,'desc' ]]
                                });
                table.clear().draw();
                var x ='';
                $.each(data, function(index, val) {
                    console.log(data);

                    if(val.usada == 0){
                        var status = '<span class="label label-success">Activo</span>';
                        var botones = '<a href="#" class="btn btn-danger btn-xs active" onclick="desactivaGift('+val.id+');"><span class="glyphicon glyphicon-remove"></span> Desactivar</a>';
                    }else{
                        var status = '<span class="label label-danger">Inactivo</span>';
                        if(val.disponible > 0 || val.puntos > val.montousado){
                            var botones = '<a href="#" class="btn btn-info btn-xs active" onclick="activaGift('+val.id+');"><span class="glyphicon glyphicon-check"></span> Activar</a>'; 
                        }else{
                            var botones = '<a href="#" class="btn btn-warning btn-xs active" ></span> Sin saldo</a>';
                        }
                    }

                    x ='<tr>'+
                            '<td>'+val.numero+'</td>'+
                            '<td>'+val.valor+'</td>'+
                            '<td>'+val.montousado+'</td>'+
                            '<td>'+val.disponible+'</td>'+
                            '<td>'+status+'</td>'+
                            '<td>'+botones+'</td>'+
                        '</tr>';
                    table.row.add($(x)).draw();                         

                });
                $('#divTarjetaT').show();
            }else{                
                $('#divTarjetaT').hide();
                
                var table   = $('#tableMonedero').DataTable({
                                    autowidth: 'false',
                                    dom: 'Bfrtip',
                                    buttons: [ 'excel' ],
                                                    language: {
                                                    search: "Buscar:",
                                                    lengthMenu:"",
                                                    zeroRecords: "No hay datos.",
                                                    infoEmpty: "No hay datos que mostrar.",
                                                    info:"Mostrando del _START_ al _END_ de _TOTAL_ facturas",
                                                    paginate: {
                                                        first:      "Primero",
                                                        previous:   "Anterior",
                                                        next:       "Siguiente",
                                                        last:       "Último"
                                                    },
                                                 },
                                                  aaSorting : [[0,'desc' ]]
                                });
                table.clear().draw();
                var x ='';
                $.each(data, function(index, val) {

                    if(val.usada == 0){
                        var status = '<span class="label label-success">Activo</span>';
                        var botones = '<a href="#" class="btn btn-danger btn-xs active" onclick="desactivaGift('+val.id+');"><span class="glyphicon glyphicon-remove"></span> Desactivar</a>';
                    }else{
                        var status = '<span class="label label-danger">Inactivo</span>';
                        if(val.disponible > 0 || val.puntos > val.montousado){
                            var botones = '<a href="#" class="btn btn-info btn-xs active" onclick="activaGift('+val.id+');"><span class="glyphicon glyphicon-check"></span> Activar</a>'; 
                        }else{
                            var botones = '<a href="#" class="btn btn-warning btn-xs active" ></span> Sin saldo</a>';
                        }
                    }

                    x ='<tr>'+
                            '<td>'+val.numero+'</td>'+
                            '<td>'+val.puntos+'</td>'+
                            '<td>'+val.cliente+'</td>'+
                            '<td>'+status+'</td>'+
                            '<td>'+botones+'</td>'+
                        '</tr>';
                    table.row.add($(x)).draw();                         

                });
                $('#divMonederoT').show();
            }            
        });
        
    }
    function newMonedero(){
        $("#numMonNew, #puntosMonNew").val('');
        $("#modalMonederoNew").modal('show');
    }
    function reposicionM(){
        $("#modalReposicion").modal('show');
    }
    $("#numMonedero").blur(function(event) {
        var id = $("#numMonedero").val();
        $.ajax({
            url: 'ajax.php?c=caja&f=dataMonedero',
            type: 'POST',
            dataType: 'json',
            data: {id:id},
        })
        .done(function(data) {
            console.log(data);
            if(data.total > 0){
                $("#puntosRep").val(data.rows[0].puntos);
                $("#clienteRep").val(data.rows[0].cliente);
                $("#idclienteRep").val(data.rows[0].idcliente);
            }else{
                alert('El monedero no existe');
                $("#puntosRep").val('');
                $("#clienteRep").val('');
                $("#idclienteRep").val('');
            }
            
        });        
    }); 
    function saveNewMon(){
        var monedero = $("#numMonNew").val();
        $.ajax({
            url: 'ajax.php?c=caja&f=verificaMonedero',
            type: 'POST',
            dataType: 'json',
            data: {monedero:monedero},
            async:false,
        })
        .done(function(data) {
            if(data.total == 0){
                var puntos = $("#puntosMonNew").val();
                var idcliente = $("#selectCli").val();                

                $.ajax({
                    url: 'ajax.php?c=caja&f=saveNewMon',
                    type: 'POST',
                    dataType: 'json',
                    data: {monedero:monedero,puntos:puntos,idcliente:idcliente},
                })
                .done(function(data) {
                    console.log(data);
                    $("#tipo").val(2).trigger('change');
                    $("#modalMonederoNew").modal('hide');
                })

            }else{
                alert('El numero de monedero ya existe!!');
                return 0;
            }
        });
        
    }   
    function saveNewRep(){

        var monedero = $("#numMonederoNew").val();
        // verifica si existe

        $.ajax({
            url: 'ajax.php?c=caja&f=verificaMonedero',
            type: 'POST',
            dataType: 'json',
            data: {monedero:monedero},
            async:false,
        })
        .done(function(data) {
            console.log(data);
            if(data.total == 0){
                var numMonedero = $("#numMonedero").val();
                // var puntosRep = $("#puntosRep").val();
                var idclienteRep = $("#idclienteRep").val();
                var numMonederoNew = $("#numMonederoNew").val();
                var puntos = 0;
                if($('#si').is(':checked')){
                    puntos = $("#puntosRep").val(); 
                }
                $.ajax({
                    url: 'ajax.php?c=caja&f=saveNewRep',
                    type: 'POST',
                    dataType: 'json',
                    data: {numMonedero:numMonedero,numMonederoNew:numMonederoNew,puntos:puntos,idcliente:idclienteRep},
                })
                .done(function(data) {
                    console.log(data);
                    $("#tipo").val(2).trigger('change');
                    $("#modalReposicion").modal('hide');
                })
                        
            }else{
                alert('El numero de monedero ya existe!!');
                return 0;
            }
        });
        
    }
    function verificaVenta(){
        var idVenta = $("#idVenta").val();
        $("#divtarjetas").html('');
        $.ajax({
            url: 'ajax.php?c=caja&f=verificaVenta',
            type: 'POST',
            dataType: 'json',
            data: {idVenta:idVenta},
        })
        .done(function(data) {
            console.log(data);

            $.each(data, function(key, val) {
                var div =
                        '<div class="row">'+ 
                            '<div class="col-sm-6">'+
                                '<label for="">Numero de Tarjeta</label>'+
                                '<input type="text" class="form-control numeroTarjeta" id="numeroTarjeta_'+val.id+'" >'+
                            '</div>'+
                            '<div class="col-sm-6">'+
                                '<label for="">Monto</label>'+
                                '<input type="text" class="form-control numeros montoTarjeta" id="montoTarjeta_'+val.id+'" value="'+val.monto+'" readonly>'+
                            '</div>'+
                        '</div>';
                $("#divtarjetas").append(div);               
            });
        });        
    }
    function saveTarjeta(){
        var numTarjetas = [];
        var montoTarjetas = [];
        var newarray = [];

        var idVenta = $("#idVenta").val();

        $(".numeroTarjeta").each(function(){
            numTarjetas.push($(this).val());
        });
        $(".montoTarjeta").each(function(){
            montoTarjetas.push($(this).val());
        });

        $.ajax({
            url: 'ajax.php?c=caja&f=saveTarjeta',
            type: 'POST',
            dataType: 'json',
            data: {numTarjetas:numTarjetas,montoTarjetas:montoTarjetas,idVenta:idVenta},
        })
        .done(function(data) {
            console.log(data);
            $("#tipo").val(1).trigger('change');
            $("#modal-giftCard").modal('hide');
        });
        

        // $.each(numTarjetas,function(key, val) {
        //     $.each(montoTarjetas,function(key2, val2) {
        //         if(key == key2){
        //             arne.push({"fila":$("#input_nombre_"+i).val(),"monto":100})
        //             //newarray.push({"numT":1),"monT":2})
        //             // newarray.push([]['numT'] = val);
        //             // newarray.push([]['monT'] = val2);
        //         }
        //     });
        // });
        // console.log(newarray)

    }
</script>