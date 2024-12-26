<style>
    tfoot, thead {
  background-color: #d3d3d3;
  color: #000000;
  font-size: 100%;
  font-weight: bold;
}
.rowhide {
    display: none;
}
.rowshow {
    display: none;
}
.sizeprint {
    max-width: 612px;
}
.divaling{  /*hijos*/
    float: left;
}


#divImpresion {
   width: 100%;
   max-width: 1000px;
   min-width: 800px;
   margin: 50px auto;
   font-size: 70%;
}


#contenedor {
   width: 90%;
   max-width: 1170px;
   min-width: 800px;
   margin: 50px auto;
}
#columnas {
   column-count: 6;
   column-gap: 15px;
   column-fill: auto;
}

.unidad {
   float: left;
}

</style>

<!DOCTYPE html>

<html>
<head>
    <meta http-equiv="Expires" content="0">
    <title>Pedidos</title>
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
    <!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.es.js"></script>

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

<!-- Optional theme -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>   

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

   
<body> 
<br> 
<div id ="divcont" class="container well" >
    <div class="row">
        <div class="col-xs-12 col-md-12">
           <h3>Lista de Compra</h3>
        </div>
    </div>
    <div class="row col-md-12"> <input type="hidden" value="" id="reporte"> 
        <div class="panel panel-default" id="divfiltro">
            <div class="panel-heading">
                <div class="row">
 <!--                    <div class="col-sm-6"> -->
                        
                    <div class="col-sm-3">
                        <label>Desde</label>
                        <div id="datetimepicker1" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input id="desde" class="form-control" type="text" placeholder="Fecha de Entrega">
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <label>Hasta</label>
                        <div id="datetimepicker2" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>   
                            <input id="hasta" class="form-control" type="text" placeholder="Fecha de Entrega"> 
                        </div>
                        
                        
                        <div class="row"></div>
                    </div>

<script type="text/javascript">
    $(function () {
        $('#desde').datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });
        $('#hasta').datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });



        $('#date_entrega').datepicker({
                format: "yyyy-mm-dd",
                startDate:  "<?php echo $sd2; ?>",
                endDate: "<?php echo $ed2; ?>",
                language: "es"
        });

        

        $('#date_hoy').datepicker({
                format: "yyyy-mm-dd",
                startDate:  "<?php echo $sd; ?>",
                endDate: "<?php echo $ed; ?>",
                language: "es"

        });

        $('#c_tipogasto').val(7);
        $('#c_moneda').val(1);
    });
</script>
                    <div class="col-sm-3">
                        <label>Clientes</label><br>
                        <select id="cliente" class="form-control" style="width: 100%;" multiple="multiple">
                            <option value="0" selected="selected">-Todos-</option>                        
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Productos</label><br>
                        <select id="producto" class="form-control" style="width: 100%;" multiple="multiple">
                            <option value="0" selected="selected">-Todos-</option>                        
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <label>Tipo de reporte</label><br>
                        <select id="tiporeporte" class="form-control" style="width: 100%;" >
                            <option value="1" selected="selected">Compras por empleado</option>  
                            <option value="2" >Lista de compras generales</option>                     
                        </select>
                    </div>

                    <div class="col-sm-3" id="opcion1" >
                        <div>
                            <label>Empleado</label><br>
                            <select id="empleado" class="form-control" style="width: 100%;" multiple="multiple">
                                <option value="0" selected="selected">-Todos-</option>                        
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3" id="opcion2" >
                        <label>Reporte</label><br>
                        <input type="radio" name="rep" id="R1det" value="det" checked="checked">Detallado<br>
                        <input type="radio" name="rep" id="R1imp" value="imp">Impresion<br>                     
                    </div>

                     

                    <div class="col-sm-3">
                        <label></label><br>
                        <button class="btn btn-default"  style="width: 100%;" onclick="listaCompra();">Procesar</button><br> 
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-sm-3">
                        <div style="display: none;" id="btnOrden">
                            <button class="btn btn-primary" onclick="(function() { $('#infoOrdenCompra, #infoOrdenCompra2').show() })(); ">Realiza Ordenes de Compra</button> 
                        </div>                
                    </div>
                </div>





                <div id="infoOrdenCompra" class="row" style="display: none;">
                    <div class="panel-body" style="padding:15px 0 10px 0; font-size:12px;">
                    <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-left">Usuario:</label>
                        <div class="col-sm-6" style="color:#096;">
                            <label id="userlog"><?php echo $username; ?></label>
                            <input type='hidden' id="iduserlog" value='<?php echo $iduser; ?>'>
                        </div>
                        
                    </div>
                    </div>
                    <div class="col-sm-12" style="padding-top:10px;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-left">No. Requisicion</label>
                        <div id="txt_nreq" class="col-sm-2" style="color:#ff0000;">
                        </div>
                        <label class="col-sm-2 control-label text-left">Fecha</label>
                        <div id="fechahoy" class="col-sm-2">
                            <input style="height:30px;width:100%" id="date_hoy" type="text" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label text-left">Fecha entrega</label>
                        <div class="col-sm-2 text-left">
                            <input style="height:30px;width:100%" id="date_entrega" type="text" class="form-control">
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-12" style="padding-top:10px;">
                    <div class="form-group">
                        <!-- <label class="col-sm-2 control-label text-left">Normal</label>
                        <div class="col-sm-2" style="color:#ff0000;">
                            <input  type="radio" name="radiourgente" id="opciones_1" value="0" checked>
                        </div> -->
                        <label class="col-sm-2 control-label text-left urgent">Urgente</label>
                        <div class="col-sm-2 urgent" style="color:#ff0000;">
                            <input  type="radio" name="radiourgente" id="opciones_2" value="1" checked>
                        </div>
                        <label class="col-sm-2 control-label text-left no_fact" style="">No. de Factura</label>
                        <div class="col-sm-2 no_fact" style="color:#ff0000;">
                            <input style="height:30px;width:100%" id="num_fact" type="text" class="form-control">
                        </div>
                        <label class="col-sm-2 control-label text-left">Inventariable</label>
                        <div class="col-sm-2 text-left" style="color:#ff0000;">
                            <input  type="checkbox" name="normal" id="checkbox" value="opcion_1" checked>
                        </div>
                        
                    </div>
                    </div>   
                    <div class="col-sm-12" style="padding-top:10px;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-left">Solicitante</label>
                        <div class="col-sm-2" style="color:#ff0000;">
                            <select id="c_solicitante" style="width:100%;">
                                <option value="0">Seleccione</option>
                                <?php foreach ($empleados as $k => $v) { ?>
                                    <option area="<?php echo $v['nomarea']; ?>" value="<?php echo $v['idempleado']; ?>"><?php echo $v['nombre']; ?> (<?php echo $v['nomarea']; ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label text-left">&nbsp;</label>
                        <div class="col-sm-2" style="color:#000;">
                            &nbsp;
                        </div>
                        <label class="col-sm-2 control-label text-left">Tipo gasto</label>
                        <div class="col-sm-2 text-left" style="color:#ff0000;">
                            <select id="c_tipogasto"  style="width:100%;">
                                <option value="0">Seleccione</option>
                                <?php foreach ($tipoGasto as $k => $v) { ?>
                                    <option value="<?php echo $v['id']; ?>"><?php echo $v['nombreclasificador']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-12" style="padding-top:10px;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-left">Almacen</label>
                        <div class="col-sm-2" style="color:#ff0000;">
                            <select id="c_almacen"  style="width:100%;">
                                <option value="0">Seleccione</option>
                              <?php foreach ($almacenes as $k => $v) { ?>
                                    <option value="<?php echo $v['id']; ?>"><?php echo $v['nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <label class="col-sm-2 control-label text-left">Moneda</label>
                        <div class="col-sm-2" style="color:#ff0000;">
                            <select id="c_moneda"  style="width:100%;">
                                <option value="0">Seleccione</option>
                              <?php foreach ($monedas as $k => $v) { ?>
                                    <option tc="<?php echo $v['tc']; ?>" value="<?php echo $v['coin_id']; ?>"><?php echo $v['codigo']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                        <input type="text" id="moneda_tc"  placeholder="Tipo de cambio" style="display:none;height:28px;">
                        </div>

                    </div>
                    </div>

                    
                    

                    <div class="col-sm-12" style="padding-top:10px;">
                    <div class="form-group">
                        <div class="col-sm-12 text-right">
                            
                            <button id="btn_authquit_2" onclick="realizaOrdenes();" class="btn btn-sm btn-success pull-center" type="button" style="height:28px; ">Guardar y Recibir Orden</button>

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

                <div id="infoOrdenCompra2" class="row" style="display: none;">
                    <div class="col-sm-12" style="padding-top:10px;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label text-left">Observaciones</label>
                        <div class="col-sm-10" style="color:#ff0000;">
                            <textarea class="form-control" rows="3" id="comment"></textarea>
                        </div>
                    </div>
                    </div>




            <input type="hidden" id="ist" value="0">
            <input type="hidden" id="it" value="0">
            <input type="hidden" id="cadimps" value="0">
                </div>





            </div>    
        </div>
    </div>
</div>



    
<div class="container">
    <div class="container" id="divambos" style="overflow:auto">
        <div class="col-sm-12">

            <div id="table_ambos_div">
                <table id="table_ambos" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th td width="60%">Producto</th>
                            <th td width="10%">Uni. Comp.</th>
                            <th td width="20%">Cantidad</th>
                            <th td width="20%">Costo de Compra</th>
                            <th td width="20%">Proveedor</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td align = "right">Sub Total</td>
                            <td></td>
                            <td></td>
                            <td align = "right" id="subtotal"></td>
                             <td align = "right" id="eeeeee"></td>
                        </tr>
                        <tr>
                            <td align = "right">Impuestos</td>
                            <td></td>
                            <td></td>
                            <td align = "right" id="impuestos"></td>
                            <td align = "right" id="aaaaa"></td>
                        </tr>
                        <tr>
                            <td align = "right">Total</td>
                            <td align = "right"></td>
                            <td align = "right"><label id="total"></label></td>
                            <td align = "right"><label id="totalI"></label></td>
                            <td align = "right"><label id="rrrrr"></label></td>
                        </tr>
                        </tfoot>
                </table>
            </div>
            



            <div id="tablaComprasEmpleado_div">
                <table id="tablaComprasEmpleado" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Código producto</th>
                            <th>Producto</th>
                            <th>Unidad de medida</th>
                            <th>Cantidad total de compra</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
            
        </div>
    </div>
    <div class="container" id="divImpresion" style="overflow:auto">

    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" style="z-index:1051;" data-backdrop="static">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Espere un momento...</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-default">
            <div align="center"><label id="lblMensajeEstado"></label></div>
            <div align="center"><i class="fa fa-refresh fa-spin fa-5x fa-fw margin-bottom"></i>
                 <span class="sr-only">Loading...</span>
             </div>
        </div>
        </div>
      </div>
    </div>
</div>

<script>
    

   $(document).ready(function() {
        var idclienteLog = 0; 
        function hoy(){
            var hoy = new Date();
            var dd = hoy.getDate();
            var mm = hoy.getMonth()+1; //hoy es 0!
            var yyyy = hoy.getFullYear();

            if(dd<10) {
                dd='0'+dd
            } 
            if(mm<10) {
                mm='0'+mm
            } 
            return hoy = yyyy+'-'+mm+'-'+dd;
        }

        var horainicial = $("#horaIn").val() + ":" + $("#minutoIn").val() + ":00";
        var horafinal = $("#horafin").val() + ":" + $("#minutofin").val() + ":00";
	    var hoy = hoy();
	    
        $('#desde').val(hoy);
        $('#hasta').val(hoy);

        $("#divambos").hide();       
        $("#divImpresion").hide();
        $('#desde, #hasta').datepicker({ 
            format: "yyyy-mm-dd",
            "autoclose": true, 
            language: "es"
        }).attr('readonly','readonly');
        $('#producto, #cliente , #empleado').select2(); 
        $('#tiporeporte').on('change', function(event) {
            event.preventDefault();
            if($('#tiporeporte').val() == 1 ){
                $('#opcion1').show();
                //$('#opcion2').hide();
                $('#btnOrden').hide();
                $('#infoOrdenCompra, #infoOrdenCompra2').hide() ;
            } else {
                $('#opcion1').hide();
                //$('#opcion2').show();
                $('#btnOrden').show();
                //$('#infoOrdenCompra, #infoOrdenCompra2').show() ;
            }

        });
        $.ajax({
                url: 'ajax.php?c=caja&f=selectListaCompra',
                type: 'post',
                dataType: 'json',
                data : {idclienteLog:idclienteLog},
        })
        .done(function(data) {

            $.each(data.clientes, function(index, val) {
                  $('#cliente').append('<option value="'+val.id+'">'+val.nombreCliente+'</option>');  
            });
            $.each(data.productos, function(index, val) {
                  $('#producto').append('<option value="'+val.id+'">'+val.nombre+'</option>');  
            }); 
            $.each(data.empleados, function(index, val) {
                  $('#empleado').append('<option value="'+val.idempleado+'">'+val.usuario+'</option>');  
            }); 
        }) 
    });


    function rowU(codigo){

        if($(".rowU"+codigo+"").hasClass('rowhide') == true){
            $(".rowU"+codigo+"").removeClass('rowhide');
            $("#iU"+codigo+"").removeClass('glyphicon-chevron-down');
            $("#iU"+codigo+"").addClass('glyphicon-chevron-up'); 
        }else{
            $(".rowU"+codigo+"").addClass('rowhide');
            $("#iU"+codigo+"").removeClass('glyphicon-chevron-up');
            $("#iU"+codigo+"").addClass('glyphicon-chevron-down'); 
        }  
    }
    function listaCompra(){

        var producto = $('#producto').val();
        var cliente = $('#cliente').val();
        var desde = $('#desde').val();
        var hasta = $('#hasta').val();
        var tiporeporte = $('#tiporeporte').val();
        var empleado = $('#empleado').val()

        

        if(desde == '' && hasta != ''){
            alert('Debe Selecionar Desde que Fecha');
            return false;
        }
        if(hasta == '' && desde != ''){
            alert('Debe Selecionar Hasta que Fecha');
            return false;
        }
        if(hasta != '' && desde != ''){
            if(desde > hasta){
                alert('No debe ser mayor la fecha de inicio a la fecha final');
                return false;
            }
        }

        var R1 = "";

          if ($('#R1det').prop('checked')) {
            R1 = $('#R1det').val();
          }
          if ($('#R1imp').prop('checked')) {
            R1 = $('#R1imp').val();
          }

        $('#tablaComprasEmpleado_div').hide();
        $('#table_ambos_div').hide();
        $("#modalMensajes").modal('show');
        if(tiporeporte == 1) {
            if(R1 == 'det'){
                listaCompraPorEmpleado(desde,hasta,producto,cliente,empleado);
                $("#divambos").hide();       
                $("#divImpresion").hide();
            }
            if(R1 == 'imp'){
                listaCompraPorEmpleado2(desde,hasta,producto,cliente,empleado);
                $("#divambos").hide();       
                $("#divImpresion").hide();
            }
        } else {
            if(R1 == 'det'){
                listarCompra(desde,hasta,producto,cliente);
                $("#divambos").hide();       
                $("#divImpresion").hide();
            }
            if(R1 == 'imp'){
                listarCompra2(desde,hasta,producto,cliente);
                $("#divambos").hide();       
                $("#divImpresion").hide();
            }
        }
        
        
    }
    function listaCompraPorEmpleado(desde,hasta,producto,cliente,empleado) {
        $.ajax({
            url: 'ajax.php?c=caja&f=listaCompraPorEmpleado',
            type: 'post',
            dataType: 'json',
            data:{desde:desde,hasta:hasta,producto:producto,cliente:cliente,empleado,user:0}, 
        })
        .done(function(data) {
            console.log(data);
            var table = $('#tablaComprasEmpleado')
                .DataTable( {
                    dom: 'Bfrtip',
                    buttons: [
                                {
                                    extend: 'print',
                                    title: $('h2').text(),
                                    customize: function ( win ) {
                                        $(win.document.body)
                                            .css( 'font-size', '10pt' )
                                            .prepend(
                                                '<h3>Lista de Compra por empleado</h3><br>'
                                            );                                                     
                                    }
                                },
                                
                            ],
                    language: { 
                        buttons: {
                            print: 'Imprimir'
                        }
                    },
                    destroy: true,
                    searching: false,
                    paginate: false,
                    filter: false,
                    sort: false,
                    info: false,
                    language: { 
                        buttons: {
                            print: 'Imprimir'
                        },                                                                   
                        search: "Buscar:",
                        lengthMenu:"Mostrar _MENU_ elementos",
                        zeroRecords: "No hay datos.",
                        infoEmpty: "No hay datos que mostrar.",
                        info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                        paginate: {
                            first:      "Primero",
                            previous:   "Anterior",
                            next:       "Siguiente",
                            last:       "Último"
                        }
                    },
                });
            table.clear().draw();

            $.each(data.rows, function(index, val) {
                table.row.add($(`
                    <tr>
                        <th>${val.empleado}</th>
                        <th>${val.codigo_producto}</th>
                        <th>${val.nombre_producto}</th>
                        <th>${val.unidad}</th>
                        <th>${val.cantidad}</th>
                    </tr>
                    `)).draw();
                table.row.add($(`
                    <tr>
                        <td hidden></td>
                        <td hidden></td>
                        <td hidden></td>
                        <td hidden></td>
                        <td colspan="5">${val.clientes}</td>
                    </tr>
                    `)).draw();
            });
            $("#modalMensajes").modal('hide');
            $("#divambos").show();
            $('#tablaComprasEmpleado_div').show();
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    }
    function listaCompraPorEmpleado2(desde,hasta,producto,cliente,empleado){
        $("#divImpresion").html('');
        $("#divImpresion").append('<button id="btnprint" type="button" class="btn btn-secondary" onclick="printIA();">Imprimir</button><br><br>');
        var cont = 0;
        var btnU = '';

        $.ajax({ 
            url: 'ajax.php?c=caja&f=listaCompraPorEmpleado2',
            type: 'post',
            dataType: 'json',
            data:{desde:desde,hasta:hasta,producto:producto,cliente:cliente,empleado:empleado,user:0}, 
        })
        .done(function(data) {  
            idemp = '';
            $.each(data, function(index, val) {
            if(idemp != val.idempleado){
                $("#divImpresion").append('<div class="unidad" id="div'+val.idempleado+'"></div>');
                $("#div"+val.idempleado+"").append('<table border = "1" id='+val.idempleado+'></table>');
                $("#"+val.idempleado+"").append('<tr><td width=150px bgcolor="#E6E6FA">'+val.empleado+'</td></tr>');
            }
                $("#"+val.idempleado+"").append('<tr><td width=150px>'+val.compra+'</td></tr>'); 
                idemp = val.idempleado;               
            });
            $("#modalMensajes").modal('hide');
            $("#divImpresion").show(); 
                           
        })
    }
    function listarCompra(desde,hasta,producto,cliente){
        
        var cont = 0;
        var btnU = '';

        $.ajax({ 
            url: 'ajax.php?c=caja&f=listarCompra',
            type: 'post',
            dataType: 'json',
            data:{desde:desde,hasta:hasta,producto:producto,cliente:cliente,user:0}, 
        })
        .done(function(data) {  
            console.log(data);
            var table = $('#table_ambos').DataTable( {dom: 'Bfrtip',
                                                            buttons: [
                                                                        {
                                                                            extend: 'print',
                                                                            title: $('h2').text(),
                                                                            customize: function ( win ) {
                                                                                $(win.document.body)
                                                                                    .css( 'font-size', '10pt' )
                                                                                    .prepend(
                                                                                        '<h3>Lista de Compra</h3><br>'
                                                                                    );                                                     
                                                                            }
                                                                        },
                                                                        'excel',
                                                                    ],
                                                            language: { 
                                                                buttons: {
                                                                    print: 'Imprimir'
                                                                }
                                                            },
                                                            destroy: true,
                                                            searching: false,
                                                            paginate: false,
                                                            filter: false,
                                                            sort: false,
                                                            info: false,
                                                            language: { 
                                                                buttons: {
                                                                    print: 'Imprimir'
                                                                },                                                                   
                                                            search: "Buscar:",
                                                            lengthMenu:"Mostrar _MENU_ elementos",
                                                            zeroRecords: "No hay datos.",
                                                            infoEmpty: "No hay datos que mostrar.",
                                                            info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                                                            paginate: {
                                                                        first:      "Primero",
                                                                        previous:   "Anterior",
                                                                        next:       "Siguiente",
                                                                        last:       "Último"
                                                                    }
                                                            },
                                    });
                table.clear().draw();
            var x               ='';
            var y               ='';
            var totalI          = 0;
            var total           = 0;
            var totalImp        = 0;
            var subtotal        = 0;
            var provex = '';
            var optiHtm = '';
            $.each(data.prod, function(index, val) {
                console.log(val.proveedor);
                cont++;                   
                var idprod          = val.idprod;
                var caracteristicas = val.caracteristicas;
                var nombre          = val.nombre;
                var cantidad        = val.cantidad*1;
                var costoCompra     = val.costoCompra*1;
                var impuestos       = val.impuestos*1;
                var caract          = val.caract;
                var unidad          = val.unidad;
                provex  =   val.proveedor;
                    total       += cantidad;
                    totalI      += costoCompra;
                    totalImp    += impuestos;
                
                cantidadF       = cantidad.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                costoCompraF    = costoCompra.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');  
                
                btnU = '<button class="btn btn-default btn-xs" onclick="rowU('+idprod+cont+')"><i id="iU'+idprod+cont+'" class="glyphicon glyphicon-chevron-down"></i></button>';

                $.each(provex, function(indice, valor) {
                     optiHtm += '<option value="'+valor.idPrv+'">'+valor.razon_social+'</option>';
                });

                x ='<tr idproducto="'+idprod+'" cantidad="'+cantidadF+'" costo="'+costoCompra+'">'+
                        '<td><b>'+btnU+' '+nombre+' '+caract+'</b></td>'+
                        '<td><b>'+unidad+'</b></td>'+
                        '<td align="center"><b>'+cantidadF+'</b></td>'+
                        '<td align="center"><b>$'+costoCompraF+'</b></td>'+
                        '<td><select class="form-control" id="prove_'+idprod+'">'+optiHtm+'<option value="0">--Seleccione proveedor--</option></select></td>'+
                    '</tr>';  
                table.row.add($(x)).draw();
                optiHtm = '';

                $.each(data.prod2, function(index, v) {
                    var idprod2          = v.idprod;
                    var caracteristicas2 = v.caracteristicas;
                    var nombreCliente    = v.nombreCliente;
                    var cantidad2        = v.cantidad*1;
                    var costoCompra2     = v.costoCompra*1;
                    var impuestos2       = v.impuestos*1;
                    var caract2          = v.caract;

                    cantidadF2      = cantidad2.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                    costoCompraF2   = costoCompra2.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');  

                     if(idprod == idprod2 && caracteristicas == caracteristicas2){
                        y ='<tr class="rowU'+idprod+cont+' rowhide">'+
                            '<td>-- <em>'+nombreCliente+'</em></td>'+
                            '<td></td>'+
                            '<td align="center">'+cantidadF2+'</td>'+
                            '<td align="center">$'+costoCompraF2+'</td>'+
                            '<td></td>'+
                        '</tr>';
                        table.row.add($(y)).draw();
                     }   
                })            
            })

            $("#modalMensajes").modal('hide');
            $("#divambos").show();
            $('#table_ambos_div').show();
             
             subtotal   = totalI - totalImp;
             subtotalF  = subtotal.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
             $("#subtotal").text('$'+subtotalF);

             totalIF    = totalI.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
             $("#totalI").text('$'+totalIF);
             
             totalF     = total.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
             $("#total").text(totalF);
             
             totalImpF  = totalImp.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');                         
             $("#impuestos").text('$'+totalImpF);
             $('#btnOrden').show();
                          
        })
    }
    function listarCompra2(desde,hasta,producto,cliente){
        $("#divImpresion").html('');
        $("#divImpresion").append('<button id="btnprint" type="button" class="btn btn-secondary" onclick="printIA();">Imprimir</button><br><br>');
        var cont = 0;
        var btnU = '';

        $.ajax({ 
            url: 'ajax.php?c=caja&f=listarCompra2',
            type: 'post',
            dataType: 'json',
            data:{desde:desde,hasta:hasta,producto:producto,cliente:cliente,user:0}, 
        })
        .done(function(data) {  
 
            $.each(data, function(index, val) {

            if(val.aux == 1){
                $("#divImpresion").append('<div class="unidad" id="div'+val.idclinete+'"></div>');
                $("#div"+val.idclinete+"").append('<table border = "1" id='+val.idclinete+'></table>');
                $("#"+val.idclinete+"").append('<tr><td width=150px bgcolor="#E6E6FA">'+val.cliente+'</td></tr>');
            }
                $("#"+val.idclinete+"").append('<tr><td width=150px>'+val.compra+'</td></tr>');                
            });
            $("#modalMensajes").modal('hide');
            $("#divImpresion").show(); 
                           
        })
    }
    function realizaOrdenes(){

    var proveedores = [];
    $('[id^="prove_"]').each( function( index, el ) {
        if( ! proveedores.some( p => p.proveedor === $(el).val() ) ) 
            proveedores.push( { 'proveedor' : $(this).val() , 'productos' : [] } );
    });
    $('#table_ambos tr[idproducto]').each(function(index, el) {
        ind = proveedores.findIndex( p => p.proveedor === $(el).find('[id^="prove_"]').val() );
        if( ind !== -1 ) 
            proveedores[ind].productos.push( { 
                'idProducto' : $(el).attr('idproducto') , 
                'cantidad' : $(el).attr('cantidad'), 
                'costo' : $(el).attr('costo') } 
                ); 
    });
    ind = proveedores.findIndex( p => p.proveedor == 0 );
    if( ind !== -1 ) proveedores.splice( ind , 1);



    iduserlog = $('#iduserlog').val();

    //Verifica si es edicion o update
    //siempre es nueva orden
    option = '1';

    //No hay requisisión previa        
    id_req = '0'; //$('#txt_nreq').text();
    idrequi = '0'; //$('#idrequi').val();




    solicitante=$('#c_solicitante').val();
    tipogasto=$('#c_tipogasto').val();
    moneda=$('#c_moneda').val();
    //proveedor=$('#c_proveedores').val();

    almacen=$('#c_almacen').val();

    tipo_compra=2;

    num_fact=$('#num_fact').val();

    obs=$('#comment').val();
    obs = obs.replace(/\r\n|\r|\n/g,"<br />");


    fechahoy=$('#fechahoy').text();
    fechahoy=$('#date_hoy').val();
    fechaentrega=$('#date_entrega').val();


    if(moneda==1){
        moneda_tc=0;
    }else{
        moneda_tc=$('#moneda_tc').val();  
    }

    var cxp = 0;
    if(confirm("¿Quuieres que se envia a saldo por pagar?")){
        cxp = 1;
    } else {
        cxp = 0;
    }


    urgente = $('input[name=radiourgente]:checked').val();
    if ($('#checkbox').is(':checked')) {
        inventariable=1;
    }else{
        inventariable=0;
    }


    //ist=$('#ist').val();
    //it=$('#it').val();
    //cadimps=$('#cadimps').val();


    deten=0;
    if(solicitante==0){ 
        alert('Tienes que seleccionar un solicitante'); 
        deten=1;
    }else if(fechahoy=='' && deten==0){ 
        alert('Tienes que seleccionar una fecha de entrega'); 
        deten=1;
    }else if(fechaentrega=='' && deten==0){ 
        alert('Tienes que seleccionar una fecha de entrega'); 
        deten=1;
    }else if(tipogasto==0 && deten==0){ 
        alert('Tienes que seleccionar un tipo de gasto'); 
        deten=1; 
    }else if(moneda==0 && deten==0){ 
        alert('Tienes que seleccionar una moneda'); 
        deten=1; 
    }else if(moneda_tc=='' && deten==0 && moneda>1){ 
        alert('Tienes que ingresar un tipo de cambio'); 
        deten=1; 
    }else if(almacen==0 && deten==0){ 
        alert('Tienes que seleccionar un almacen'); 
        deten=1; 
    }/*else if(ist==0 && deten==0){ 
    alert('El subtotal debe ser mayor a 0'); 
    deten=1; 
    }else if(it==0 && deten==0){ 
    alert('El total debe ser mayor a 0'); 
    deten=1; 
}*/

if(deten==1){
    return false;
}

preciosvaliods=0;
modCosto=$('#modCosto').val();


console.log('proveedores->',proveedores)
var proveedor = 0;
var cadProducuctsImp = '';
var idsProductos = '';
var its = 0;
var it = 0;
var cadimps = '';
$.each( proveedores ,function(index, el) {
    proveedor = el.proveedor;

    cadProducuctsImp = '';
    idsProductos = '';
    $.each( el.productos , function(index, prod) {
        //subtotal = parseFloat(prod.costo) * parseFloat(prod.cantidad);

        cadProducuctsImp +=prod.idProducto+'-'+prod.cantidad+'-'+prod.costo+'-'+0+'/';

        if( index != 0 ) idsProductos += ',# ';
        idsProductos +=  prod.idProducto+'>#'+prod.cantidad+'>#'+prod.costo+'>#'+0;
    });
    //ist = subtotal;

    $.ajax({
        url: '../appministra/ajax.php?c=compras&f=calculaPrecios',
        type: 'POST',
        dataType: 'json',
        async: false,
        data: {productos: cadProducuctsImp},
    })
    .done(function(data) {
        cadimps='';
        $.each(data.cargos.ppii, function(index, val) {
            cadimps+=index+"#"+val+'|';
        });

        ist = data.cargos.subtotal;
        it = data.cargos.total;
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });



    if(idsProductos==''){
        //alert('No hay productos')
        return false;
    }else{
        console.log(idsProductos);
        $.ajax({
            url: '../appministra/ajax.php?c=compras&f=a_guardarOrden',
            type: 'POST',
            async: false,
            data: {
                idsProductos:idsProductos,
                solicitante:solicitante,
                tipogasto:tipogasto,
                moneda:moneda,
                proveedor:proveedor,
                urgente:urgente,
                inventariable:inventariable,
                moneda_tc:moneda_tc,
                fechaentrega:fechaentrega,
                fechahoy:fechahoy,
                option:option,
                idrequi:idrequi,
                almacen:almacen,
                num_fact: num_fact,
                tipo_compra: tipo_compra,
                ist:ist,
                cxp:cxp,
                it:it,
                idactivo:1,
                obs:obs,
                cadimps:cadimps,
                iduserlog:iduserlog
            },
        })
        .done(function() {
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }
});

location.reload();


}


    function printIA(){
        $("#divcont, #btnprint").hide();
        window.print();
        //alert("printing");
        $("#divcont, #btnprint").show();
    }




    
</script>
</div>
</body>
</html>
