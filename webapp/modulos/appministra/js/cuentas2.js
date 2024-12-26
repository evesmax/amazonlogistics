$(function()
 {
     window.id_pago = 0;
     window.cantidad_pago = 0;
     window.ids = '';
     window.moneda = '';
     Number.prototype.format = function() {
        return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    };
    $("#tipo_cambio").val('1.00')
    $("#row_tipo_cambio").hide();
    $("#check_tipo_cambio").prop("checked",false).hide();
    $(".dbancarios1,.dbancarios2,.spei").hide()
    $("#hora_pago").keypress(function(event) {
        var c = event.keyCode || event.which,
        key = String.fromCharCode(c),
        chars = "0123456789:"
        return chars.search(key) !== -1
    })
 });


function pagosSinAsignar()
{
     datosClienteProv(1,$("#listaProveedores").val())
     $.post('ajax.php?c=cuentas&f=pagosSinAsignar',
        {
            idPrv: $("#listaProveedores").val()
        },
        function(data)
        {
            $("#trs_esp").html(data);
            if(parseInt($("#listaProveedores").val()))
            {
                $("#trs_esp").html(data);
                $("#pagar,#saldo_gral_div").show();
                //$("#pagar,#listaFacturasBtn,#listaCargosBtn,#saldo_gral_div").show();
                saldogeneral(1,$("#listaProveedores").val())
            }
            else
            {
                $("#trs_esp").html('');
                $("#pagar,#listaFacturasBtn,#listaCargosBtn,#saldo_gral_div").hide();
                $("#saldo_general").text('0.00')
            }
        });
}

function saldogeneral(tipo,provcli)
{
    /*$.post('ajax.php?c=cuentas&f=saldoGral',
        {
            idPrvCli: provcli,
            tipo: tipo
        },
        function(data)
        {
            var d = parseFloat(data);
            if(isNaN(d))
                d=0;
            var totalSaldosApl = 0;
            $(".saldoApl").each(function(index)
            {
                totalSaldosApl += parseFloat($(this).attr('saldo'))
            });
            totalSaldosApl = parseFloat(totalSaldosApl)
            if(isNaN(totalSaldosApl))
                totalSaldosApl=0;
            $("#saldo_general").text(d.format())
            $("#cargando").hide();
            $("#pantalla").show();
            $("#pagos_sin").text(totalSaldosApl.format())
            $("#total_saldos").val("$ "+d.format());
            $("#tabla-carfac").before($("#saldos_div2"));
        });*/



            var totalSaldosApl = 0;
            $(".saldoApl").each(function(index)
            {
                totalSaldosApl += parseFloat($(this).attr('saldo'))
            });
            totalSaldosApl = parseFloat(totalSaldosApl)
            if(isNaN(totalSaldosApl))
                totalSaldosApl=0;
            $("#cargando").hide();
            $("#pantalla").show();
            $("#pagos_sin").text(totalSaldosApl.format())

}

function datosClienteProv(tipo,idcli)
{
    $.post('ajax.php?c=cuentas&f=datosClienteProv',
            {
                tipo:tipo,
                idcli: idcli,
                d:1
            },
            function(data)
            {
                //alert(data)
                var datos = jQuery.parseJSON(data);
                if(!tipo)
                    $("#nombre_cliente").html("<a href='javascript:datos_cli_prov("+tipo+","+idcli+")'>"+datos.nombre+"</a>");
                else
                    $("#nombre_proveedor").html("<a href='javascript:datos_cli_prov("+tipo+","+idcli+")'>"+datos.razon_social+"</a>");
            });
}

function datos_cli_prov(tipo,idcli)
{
    $.post('ajax.php?c=cuentas&f=datosClienteProv',
            {
                tipo:tipo,
                idcli: idcli,
                d:0
            },
            function(data)
            {
                var datos = jQuery.parseJSON(data);
                var limite = parseFloat(datos.limite_credito);
                $("#rfc").html("RFC: "+datos.rfc);
                if(!tipo)
                {
                    $("#razon").html($("#nombre_cliente").text())
                    $("#rfc").html("RFC: "+datos.rfc);
                    $("#domicilio").html("Domicilio Fiscal:<br />"+datos.direccion+" "+datos.colonia+" "+datos.cp+" "+datos.municipio+" "+datos.estado);
                    $("#dias_credito").html("Días Credito:<br /><span style='color:gray;'>"+datos.dias_credito+" días</span>")
                    $("#limite_credito").html("Limite Crédito:<br /><span style='color:gray;'>$"+limite.format()+"</span>")
                    $("#datos_contacto").html("<div class='col-sm-4 col-md-4'><span class='glyphicon glyphicon-earphone'></span> "+datos.telefono1+"</div><div class='col-sm-4 col-md-4'><span class='glyphicon glyphicon-phone'></span> "+datos.celular+"</div><div class='col-sm-4 col-md-4'><span class='glyphicon glyphicon-envelope'></span>"+datos.email+"</div>")
                }
                else
                {
                    $("#razon").html($("#nombre_proveedor").text())
                    $("#domicilio").html("Domicilio Fiscal:<br />"+datos.domicilio+" "+datos.no_ext+" "+datos.cp+" "+datos.municipio+" "+datos.estado);
                    $("#dias_credito").html("Días Credito:<br /><span style='color:gray;'>"+datos.diascredito+" días</span>")
                    $("#limite_credito").html("Limite Crédito:<br /><span style='color:gray;'>$"+limite.format()+"</span>")
                    $("#datos_contacto").html("<div class='col-sm-4 col-md-4'><span class='glyphicon glyphicon-earphone'></span> "+datos.telefono+"</div><div class='col-sm-4 col-md-4'><span class='glyphicon glyphicon-globe'></span> "+datos.web+"</div><div class='col-sm-4 col-md-4'><span class='glyphicon glyphicon-envelope'></span>"+datos.email+"</div>")
                }

            });

    $('.bs-datos-modal-md').modal('show');
}

function cobrosSinAsignar()
{
    if($("#listaClientes").val() != '*|*')
    {
         datosClienteProv(0,$("#listaClientes").val())
         $.post('ajax.php?c=cuentas&f=cobrosSinAsignar',
            {
                id: $("#listaClientes").val()
            },
            function(data)
            {
                $("#trs_esp").html(data);
                $("#pagar,#saldo_gral_div").show();
                //$("#pagar,#listaFacturasBtn,#listaCargosBtn,#saldo_gral_div").show();
                saldogeneral(0,$("#listaClientes").val())

            });
     }
     else
     {
        $("#trs_esp").html('');
        $("#pagar,#listaFacturasBtn,#listaCargosBtn,#saldo_gral_div").hide();
        $("#saldo_general").text('0.00')
     }
}

function pagar(t)
{
    $('.bs-pagos-modal-md').modal('show');
    $("#importe_pago").val('0.00')
    $("#forma_pago").val(7)
    if(!parseInt(t))
    {
        $("#tipo_pago").val(0)
        $("#pc").text("Pago")
        $("#forma_pago_div").show()
    }
    else
    {
        $("#tipo_pago").val(1)
        $("#pc").text("Cargo")
        $("#forma_pago_div").hide()
    }
}

function cancelar_pagos()
{
    $('.bs-pagos-modal-md').modal('hide');
    $("#importe_pago").val('0.00')
    $("#fecha_pago").val('')
    $("#concepto_pago").val('')
}

function guardar_pagos()
{
    //alert($('#comprobante_input').val());

    //INICIA VALIDACIONES*******************************************************************************************
    //Cheque-----------------------------------------
    if($("#forma_pago").val() == 2){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 11 && $("#cuenta_bancaria_origen").val().length != 18){
            alert("Cuenta bancaria origen debe tener 11 o 18 caracteres")
            return false
        }

        if($("#cuenta_bancaria_destino").val() != '' && $("#cuenta_bancaria_destino").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 11 && $("#cuenta_bancaria_destino").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16 && $("#cuenta_bancaria_destino").val().length != 18 && $("#cuenta_bancaria_destino").val().length != 50){
            alert("Cuenta bancaria destino debe tener 10, 11, 15, 16, 18 o 50 caracteres")
            return false
        }
    }

    //Transferencia-----------------------------------------
    if($("#forma_pago").val() == 7){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 10 && $("#cuenta_bancaria_origen").val().length != 16 && $("#cuenta_bancaria_origen").val().length != 18){
            alert("Cuenta bancaria origen debe tener 10, 16 o 18 caracteres")
            return false
        }

        if($("#cuenta_bancaria_destino").val() != '' && $("#cuenta_bancaria_destino").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 18 ){
            alert("Cuenta bancaria destino debe tener 10 o 18 caracteres")
            return false
        }
    }

    //tarjeta de credito-----------------------------------------
    if($("#forma_pago").val() == 4){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 16){
            alert("Cuenta bancaria origen debe tener 16 caracteres")
            return false
        }

        if($("#cuenta_bancaria_destino").val() != '' && $("#cuenta_bancaria_destino").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 11 && $("#cuenta_bancaria_destino").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16 && $("#cuenta_bancaria_destino").val().length != 18 && $("#cuenta_bancaria_destino").val().length != 50){
            alert("Cuenta bancaria destino debe tener 10, 11, 15, 16, 18 o 50 caracteres")
            return false
        }
    }

    //monedero-----------------------------------------
    if($("#forma_pago").val() == 10){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 11 && $("#cuenta_bancaria_destino").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16 && $("#cuenta_bancaria_destino").val().length != 18 && $("#cuenta_bancaria_destino").val().length != 50){
            alert("Cuenta bancaria origen debe tener 10, 11, 15, 16, 18 o 50 caracteres")
            return false
        }

        if($("#cuenta_bancaria_destino").val() != '' && $("#cuenta_bancaria_destino").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 11 && $("#cuenta_bancaria_destino").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16 && $("#cuenta_bancaria_destino").val().length != 18 && $("#cuenta_bancaria_destino").val().length != 50){
            alert("Cuenta bancaria destino debe tener 10, 11, 15, 16, 18 o 50 caracteres")
            return false
        }
    }

    //dinero elec-----------------------------------------
    if($("#forma_pago").val() == 11){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 10){
            alert("Cuenta bancaria origen debe tener 10 caracteres")
            return false
        }
    }

    //tarjeta debito-----------------------------------------
    if($("#forma_pago").val() == 5){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 16){
            alert("Cuenta bancaria origen debe tener 16 caracteres")
            return false
        }

        if($("#cuenta_bancaria_destino").val() != '' && $("#cuenta_bancaria_destino").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 11 && $("#cuenta_bancaria_destino").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16 && $("#cuenta_bancaria_destino").val().length != 18 && $("#cuenta_bancaria_destino").val().length != 50){
            alert("Cuenta bancaria destino debe tener 10, 11, 15, 16, 18 o 50 caracteres")
            return false
        }
    }

    //tarjeta serv-----------------------------------------
    if($("#forma_pago").val() == 105){
        if($("#cuenta_bancaria_origen").val() != '' && $("#cuenta_bancaria_origen").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16){
            alert("Cuenta bancaria origen debe tener 15 o 16 caracteres")
            return false
        }

        if($("#cuenta_bancaria_destino").val() != '' && $("#cuenta_bancaria_destino").val().length != 10 && $("#cuenta_bancaria_destino").val().length != 11 && $("#cuenta_bancaria_destino").val().length != 15 && $("#cuenta_bancaria_destino").val().length != 16 && $("#cuenta_bancaria_destino").val().length != 18 && $("#cuenta_bancaria_destino").val().length != 50){
            alert("Cuenta bancaria destino debe tener 10, 11, 15, 16, 18 o 50 caracteres")
            return false
        }
    }

    //FIN VALIDACIONES*******************************************************************************************


    $("#btn_guardar_pago,#btn_cancelar_pago").attr('disabled',true);
    var idPrvCli;
    if(parseInt($("#cobrar_pagar").val())) {
      idPrvCli = $("#listaProveedores").val();
    } else {
      idPrvCli = $("#listaClientes").val();
    }

    if($('#comprobante_input').is(':visible')) {
      subir_comprobante();
    }

     $.post('ajax.php?c=cuentas&f=guardarPagos',
        {
            idPrvCli: idPrvCli,
            fecha: $("#fecha_pago").val(),
            hora: $("#hora_pago").val(),
            concepto: $("#concepto_pago").val(),
            tipo_pago: $("#tipo_pago").val(),
            importe: $("#importe_pago").val(),
            forma_pago: $("#forma_pago").val(),
            numero_cheque: $("#numero_cheque").val(),
            comprobante: get_filename_input_file(),
            cobrar_pagar: $("#cobrar_pagar").val(),
            moneda: $("#moneda").val(),
            tipo_cambio: $("#tipo_cambio").val(),
            cuenta_bancaria_origen: $("#cuenta_bancaria_origen").val(),
            banco_origen_nac: $("#banco_origen_nac").val(),
            banco_origen_ext: $("#banco_origen_ext").val(),
            cuenta_bancaria_destino: $("#cuenta_bancaria_destino").val(),
            banco_destino_nac: $("#banco_destino_nac").val(),
            banco_destino_ext: $("#banco_destino_ext").val(),
            tipo_pago_spei: $("#tipo_pago_spei").val(),
            spei_certificado: $("#spei_certificado").val(),
            spei_cadena: $("#spei_cadena").val(),
            spei_sello: $("#spei_sello").val()
        },
         function(data)
        {
            console.log("guardar_pago: "+data)
            if(parseInt(data))
            {
                if(!$("#detalle").length)//Si no viene de detalles de la cuenta
                {
                    //alert("pago normal")
                    if(parseInt($("#cobrar_pagar").val()))
                        pagosSinAsignar();
                    else
                        cobrosSinAsignar();

                    $("#layout").hide();
                    cancelar_pagos();
                    if(parseInt($("#tipo_pago").val()) == 1) {
                      //listaCargos(0);
                      //alert($("#tipo_pago").val())
                        $('#tabla-carfac').DataTable().clear().draw();
                        $('#tabla-carfac').DataTable().destroy();
                        listaCargosFacturas();
                    }
                    $('#comprobante_input').val("");
                    $('#numero_cheque').val("");
                    $("#btn_guardar_pago,#btn_cancelar_pago").removeAttr('disabled');
                }
                else
                {
                    //alert("pago detalle")
                    $('#comprobante_input').val("");
                    $('#numero_cheque').val("");
                    $("#btn_guardar_pago,#btn_cancelar_pago").removeAttr('disabled');
                    $('.bs-pagos-modal-md').modal('hide');
                    pagar_detalle(data);
                }
            }
            else
            {
                alert("No se pudo hacer el pago.")
                cancelar_pagos();
            }
        });
}

function pagar_detalle(idpago)
{
    $('.bs-relacionar-modal-sm').modal({backdrop: 'static'});//No cierra el modal cuando se da click afuera
    $('.bs-relacionar-modal-sm').modal('show');
    var importe = $("#importe_pago").val();
    if(parseInt($("#moneda").val()) > 1)
        importe = parseFloat($("#importe_pago").val()) * parseFloat($("#tipo_cambio").val())
    $("#disponible").val(importe);
    $("#cantidad_pagar").val($("#disponible").val());
    $("#total_cargo").val($("#pendiente").attr('cantidad'));
    $("#id_pago_ret").val(idpago)
    cancelar_pagos();
}

/**
 * [mostrarNumeroCheque ]
 * @param  {[id]} id [El id seleccionado del select]
 * @return {[Input Numero de Cheque]} [Genera el HTML que se incrusta en el div num_cheque_container y comprobante_container, además los cambia a visibles]
 * Si el ID es el correspondiente a "(02) Cheque Nominativo" se despliega el input para el número
 * del cheque y comprobante, de lo contrario los esconde.
 */
function mostrar_numero_cheque(id) {
  inputNumCheque = ""+
    '<div class="col-xs-6 col-md-4">' +
      '<b>Número del cheque:</b>' +
    '</div>' +
    '<div class="col-xs-6 col-md-6">' +
      '<input type="text" id="numero_cheque" class="form-control" onkeypress="return NumCheck(event, this)">' +
    '</div>';

  inputComprobante = "" +
    '<div class="col-xs-6 col-md-4">' +
      '<b>Comprobante:</b>' +
    '</div>' +
    '<div class="col-xs-6 col-md-6">' +
      '<input type="file" id="comprobante_input" class="form-control-file"> <br>' +
    '</div>';

  if(id == 2){
    $('#num_cheque_container').removeClass('hidden');
    document.getElementById('num_cheque_container').innerHTML = inputNumCheque;
    $('#comprobante_container').removeClass('hidden');
    document.getElementById('comprobante_container').innerHTML = inputComprobante;
  } else {
    $('#num_cheque_container').addClass('hidden');
    document.getElementById('num_cheque_container').innerHTML= "";
    $('#comprobante_container').addClass('hidden');
    document.getElementById('comprobante_container').innerHTML = "";
  }

  if(id == 2 || id == 7)
    $(".hidden2").show()
  else
    $(".hidden2").hide()

    $(".dbancarios1,.dbancarios2,.spei").hide()
}

/**
 *  ### Validación ###
 * [esconder_campos]
 * Este método esconde los campos de comprobante y número cheque cuando se presiona el boton
 * cancelar y también cuando la modal que contiene el formulario no se encuentra visible.
 */
function esconder_campos() {
  //Si la modal no esta visible esconde los campos comprobante y numero_cheque
  if (!$('#pagos_modal').hasClass('in')) {
    $('#num_cheque_container').addClass('hidden');
    $('#comprobante_container').addClass('hidden');
  }
}

/**
 * [subir_comprobante]
 *
 */
function subir_comprobante () {
  var file_data = $('#comprobante_input').prop('files')[0];
  //alert(file_data.type);
  var form_data = new FormData();
  form_data.append("file", file_data);
  //alert(form_data);
  $.ajax({
    url       : 'controllers/comprobantes.php', // point to server-side PHP script
    type      : 'post',
    dataType  : 'text',  // what to expect back from the PHP script, if anything

    cache       :  false,
    contentType :  false,
    processData :  false,

    data    : form_data,
    success : function(php_script_response){
        alert(php_script_response); // display response from the PHP script, if any
    }
  });
}

function get_filename_input_file() {
  var fullPath = $('#comprobante_input').val();
  if (fullPath) {
      var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
      var filename = fullPath.substring(startIndex);
      if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
          filename = filename.substring(1);
      }
      return filename;
  }
}

function aplicar(id,moneda)
{
    window.id_pago = id;
    window.cantidad_pago = $("#saldo_pago-"+id).attr('saldo')
    window.moneda = moneda
    $('.bs-aplicar-modal-sm').modal('show');
}

function aplicar_factura()
{
    //alert(window.id_pago)
    $(".pagos_lista").val(0)
    $('.bs-aplicar-modal-sm').modal('hide');
    listaFacturas(1)
}

function aplicar_cargo()
{
    $(".pagos_lista").val(0)
    $('.bs-aplicar-modal-sm').modal('hide');
    listaCargos(1)
}

function listaFacturas(t)
{
    var idPrvCli;
    if(parseInt($("#cobrar_pagar").val()))
        idPrvCli = $("#listaProveedores").val();
    else
        idPrvCli = $("#listaClientes").val();

    $.post('ajax.php?c=cuentas&f=listaFacturas',
        {
            idPrvCli: idPrvCli,
            cobrar_pagar: $("#cobrar_pagar").val()
        },
        function(data)
        {
            $("#trs_fac").html(data);
            if(parseInt(t))
            {
                $(".chk").show();
                $("#relacionar_factura").show();
            }
            else
            {
                $(".chk").hide();
                $("#relacionar_factura").hide();
            }
            sumariza(0)
            $('.bs-listaFacturas-modal-lg').modal('show');
        });


}

function listaCargos(t)
{
    var idPrvCli;
    if(parseInt($("#cobrar_pagar").val()))
        idPrvCli = $("#listaProveedores").val();
    else
        idPrvCli = $("#listaClientes").val();

    $.post('ajax.php?c=cuentas&f=listaCargos',
        {
            idPrvCli: idPrvCli,
            cobrar_pagar: $("#cobrar_pagar").val()
        },
        function(data)
        {
            $("#trs_car").html(data);
            if(parseInt(t))
            {
                $(".chkCar").show();
                $("#relacionar_cargo").show();
            }
            else
            {
                $(".chkCar").hide();
                $("#relacionar_cargo").hide();
            }
            sumariza(1)
            $('.bs-listaCargos-modal-lg').modal('show');
        });
}

function mostrar()
{
    window.nomostrar = 0;
    if($("#nomostrar").is(':checked'))
        window.nomostrar = 1;

    var a = $("#saldos_div2");
    $('#tabla-carfac').DataTable().clear().draw();
    $('#tabla-carfac').DataTable().destroy();
    listaCargosFacturas()
    $("#tabla-carfac").before(a);
}

function listaCargosFacturas()
{
    //alert(window.nomostrar)
    var idPrvCli;
    if(parseInt($("#cobrar_pagar").val()))
        idPrvCli = $("#listaProveedores").val();
    else
        idPrvCli = $("#listaClientes").val();

    $.post('ajax.php?c=cuentas&f=listaCargosFacturas',
        {
            idPrvCli: idPrvCli,
            cobrar_pagar: $("#cobrar_pagar").val(),
            nomostrar: window.nomostrar
        },
        function(data)
        {
            //console.log(data)
           var datos = jQuery.parseJSON(data);
                $('#tabla-carfac').DataTable( {
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    language: {
                        search: "Buscar:",
                        lengthMenu:"Mostrar _MENU_ elementos",
                        zeroRecords: "No hay coincidencias.",
                        infoEmpty: "No hay coincidencias que mostrar.",
                        infoFiltered: "",
                        info:"Mostrando del _START_ al _END_ de _TOTAL_ cuentas",
                        paginate: {
                            first:      "Primero",
                            previous:   "Anterior",
                            next:       "Siguiente",
                            last:       "Último"
                        }
                     },
                     "order": [[ 0, "asc" ]],
                     data:datos,
                     columns: [
                        { data: 'fech_cargo' },
                        { data: 'fecha_venc' },
                        { data: 'concepto' },
                        { data: 'folio' },
                        { data: 'moneda' },
                        { data: 'monto' },
                        { data: 'abonado' },
                        { data: 'actual' },
                        { data: 'estatus' },
                        { data: 'ov' }
                    ]
                });
                var saldo = 0;

                for(i=0;i<=datos.length-1;i++)
                    saldo += parseFloat(datos[i].actual_im);

                $("#total_saldos").val("$ "+saldo.format())
                $("#tabla-carfac").before($("#saldos_div2"));
        });
}

function relacionar(t)
{
    $("#relacionar_factura").attr('disabled',true)
    var tipochk;
    if(parseInt(t))
        tipochk = 'chk';
    else
        tipochk = 'chkCar';

    var chks = 0;
    var inputs = '';
    var split = '';
    window.ids = '';
    $("input[class='"+tipochk+"']:checked").each(function()
        {
            split = $(this).attr('id');
            split = split.split('-');
            if(parseInt(t))
            {
                inputs += "<div class='row'><div class='col-xs-6 col-md-6 col-md-offset-1'>"+$("#folio-"+split[1]).text()+"</div><div class='col-xs-5 col-md-5'><input type='text' class='pagos_lista form-control' id='rel-"+split[1]+"' value='"+parseFloat($("#orel-"+split[1]).val()).toFixed(2)+"' orig='"+parseFloat($("#saldo-"+split[1]).attr('saldo')).toFixed(2)+"' onkeypress='return NumCheck(event, this)'></div></div>";
                $("#inputRelaciones").html(inputs);
            }
            else
                inputs += "<div class='row'><div class='col-xs-6 col-md-6 col-md-offset-1'>"+split[1]+"</div><div class='col-xs-5 col-md-5'><input type='text' class='pagos_lista form-control' id='rel-"+split[1]+"' value='"+parseFloat($("#orel-"+split[1]).val()).toFixed(2)+"' orig='"+parseFloat($("#saldoC-"+split[1]).attr('saldo')).toFixed(2)+"' onkeypress='return NumCheck(event, this)'></div></div>";
                $("#inputRelaciones").html(inputs);
            chks++;
            window.ids += split[1]+"@|@"
        });
        console.log("Chacando: ",window.ids)

    if(chks)
    {
        if(parseInt(t))
        {
            $("#folio_o_cargo").text("Folio")
            $("#guardar_relacion").attr('onclick','guardar_relacion(1)')
        }
        else
        {
            $("#folio_o_cargo").text("Documento")
            $("#guardar_relacion").attr('onclick','guardar_relacion(0)')
        }

        $('.bs-relaciones-modal-md').modal('show');
        //$("#guardar_relacion").click();
    }
}

function habilita_input(id,tipo)
{
    if($("#chkbx"+tipo+"-"+id).prop('checked') ? 1:0)
        $("#orel-"+id).attr('disabled',false)
    else
    {
        $("#orel-"+id).val('0.00')
        $("#orel-"+id).trigger('change')
        $("#orel-"+id).attr('disabled',true)
    }
}

function act_suma(t)
{
    var tipo;
    if(t)
        tipo = 'c'
    else
        tipo = 'f'

    var total = 0;
    $(".pagos_lista_"+tipo).each(function(index)
    {
        if($(this).val() == '')
            $(this).val('0.00')
        total += parseFloat($(this).val())
    });
    $("#saldo_pag_"+t).html("$ "+total.format()).attr('saldo',total)
    sumariza(t)

}

function sumariza(n)
{
    var pago = parseFloat(window.cantidad_pago).toFixed(2)
    var saldo = parseFloat($("#saldo_pag_"+n).attr('saldo')).toFixed(2)

    var dif =  pago - saldo;
    $("#dif_rel_"+n).html("$ "+dif.format())
}

function cancelar_relacion() {
    $('.bs-relaciones-modal-md').modal('hide');
    window.ids = '';
}

function tipo_cambio()
{
    if(parseInt($("#moneda").val()) == 1)
    {
        $("#tipo_cambio").val('1.00')
        $("#row_tipo_cambio").hide();
    }

    if(parseInt($("#moneda").val()) == 2)
    {
         $.post('ajax.php?c=cuentas&f=tipo_cambio',
        {
            fecha:$("#fecha_pago").val()
        },
        function(data)
        {
            $("#tipo_cambio").val(data)
            $("#row_tipo_cambio").show();
        });

    }

    if(parseInt($("#moneda").val()) > 2)
    {
        $("#tipo_cambio").val('')
        $("#row_tipo_cambio").show();
    }
}

function guardar_relacion(t){

    var ids_aux = window.ids;
    var valores = '';
    var tipo_cambio = 1;
    var sumaValores=0;
    var origs = 0;
    var monedas = '';
    ids_aux = ids_aux.split("@|@");
    var chkbx = '#chkbxCar';
    var cants_mal = 0;
    if(parseInt(t))
        chkbx = '#chkbx';

    for(i=0;i<=parseInt(ids_aux.length)-2;i++)
    {
        valores += $("#rel-"+ids_aux[i]).val()+"@|@";
        monedas += $(chkbx+"-"+ids_aux[i]).attr('moneda')+"@|@";
        origs += $(chkbx+"-"+ids_aux[i]).attr('orig')+"@|@";
        //alert("cobro"+$(chkbx+"-"+ids_aux[i]).attr('moneda')+" pago"+window.moneda+" / "+sumaValores)
        sumaValores += parseFloat($("#rel-"+ids_aux[i]).val());
        if(parseFloat($("#rel-"+ids_aux[i]).val()) > parseFloat($("#rel-"+ids_aux[i]).attr('orig')))
        {
            cants_mal++;
        }
    }

    var cantidad_pago = parseFloat(window.cantidad_pago).toFixed(2);
    var sumaValores1 = (sumaValores).toFixed(2);
    var ids = window.ids;

    console.log("Valores: ",valores);
    console.log("Monedas: ",monedas);
    console.log("MonedaPago: ",window.moneda);
    console.log("Origs: ",origs);
    console.log("Ides: ",window.ids);
    console.log("Tipo: ",t);
    console.log("CobrarPagar: ",$("#cobrar_pagar").val());
    console.log("FechaPliza: ",$('#fecha_poliza').val());
    console.log("idPago: ",window.id_pago);
    
    if(Number(cantidad_pago) >= Number(sumaValores1) && !cants_mal){
        
      $.post('ajax.php?c=cuentas&f=guardar_relacion',
      {
          idpago : window.id_pago,
          idrelaciones: ids,
          fecha_poliza: $('#fecha_poliza').val(),
          tipo: t,
          valores : valores,
          monedas: monedas,
          monedaPago : window.moneda,
          origs: origs
      },
      function(response) {
        if(response>0){

          $("#relacionar_factura").removeAttr('disabled');
          printer(t,valores,monedas,$("#cobrar_pagar").val());
          if(parseInt(t)) listaFacturas(1);
          else listaCargos(1);

          if(parseInt($("#cobrar_pagar").val())){
            pagosSinAsignar();
          }else{
            //Generar complemento solo en cuentas por cobrar.
            cobrosSinAsignar();
            if(parseInt(t)){
              if(confirm("Quieres generar complemento de pago?")){
                generar_complemento(valores,monedas,ids);
              }
            }
          }

          cancelar_relacion();
          $('#relacionar_factura').removeAttr("disabled");
          $('#relacionar_factura').html("Relacionar");
          $('#tabla-carfac').DataTable().clear().draw();
          $('#tabla-carfac').DataTable().destroy();
          window.cantidad_pago = parseFloat(window.cantidad_pago).toFixed(2) - sumaValores
          listaCargosFacturas();
        }else{
          console.log("Error.- Error al crear la relación.");
          cancelar_relacion();
          cancelar_relacion();
          $('#relacionar_factura').removeAttr("disabled");
          $('#relacionar_factura').html("Relacionar");
          $('#tabla-carfac').DataTable().clear().draw();
          $('#tabla-carfac').DataTable().destroy();
          window.cantidad_pago = parseFloat(window.cantidad_pago).toFixed(2) - sumaValores
          listaCargosFacturas();
        }
      });
    }
    else{
      if(cants_mal)
        alert("Una cantidad rebasa al saldo de la factura o cargo, revise porfavor.")
      else
        alert("La suma del saldo de los documentos es mayor al saldo del pago.\nSaldo Documentos: "+sumaValores.format()+"\nSaldo Pago:"+parseFloat(window.cantidad_pago).format())
    }

}

function generar_complemento(valores,monedas,ids) {

  $('#relacionar_factura').attr("disabled",true);
  $('#relacionar_factura').html("<i class='material-icons spin'>Generando...</i>");

  var cliprov;
  var idcliprov;
  var doctorel;
  var cadena = "";
  var ids_aux = ids.substring(0, ids.length-3);
  var contadorFacturasPPD = 0;

  ids_aux = ids_aux.split("@|@");

  if(parseInt($("#cobrar_pagar").val())) {
    cliprov = 2;
    idcliprov = $("#listaProveedores").val();
  } else {
    cliprov = 1;
    idcliprov = $("#listaClientes").val();
  }

  var idpago = window.id_pago;

  $.post('ajax.php?c=cuentas&f=info_pago',
  {
    idpago: idpago,
    cp: $("#cobrar_pagar").val()
  },
  function(info_pago) { 
    
    info_pago = jQuery.parseJSON(info_pago); //console.log(info_pago);

    var datos_bancos_xml = "";
    var datos_bancos_cad = "";
    var datos_spei = "";
    var datos_spei_cad = "";

    if(info_pago.RfcEmisorCtaOrd != '' && info_pago.RfcEmisorCtaOrd != null){
      //datos_bancos_xml += "RfcEmisorCtaOrd='"+info_pago.RfcEmisorCtaOrd+"' "
      datos_bancos_xml += 'RfcEmisorCtaOrd="'+info_pago.RfcEmisorCtaOrd+'" ';
      datos_bancos_cad += "|"+info_pago.RfcEmisorCtaOrd;
    }
    if(info_pago.NomBancoOrdExt != '' && info_pago.NomBancoOrdExt != null){
      //datos_bancos_xml += "NomBancoOrdExt='"+info_pago.NomBancoOrdExt+"' "
      datos_bancos_xml += 'NomBancoOrdExt="'+info_pago.NomBancoOrdExt+'" ';
      datos_bancos_cad += "|"+info_pago.NomBancoOrdExt;
    }
    if(info_pago.CtaOrdenante != '' && info_pago.CtaOrdenante != null){
      //datos_bancos_xml += "CtaOrdenante='"+info_pago.CtaOrdenante+"' "
      datos_bancos_xml += 'CtaOrdenante="'+info_pago.CtaOrdenante+'" ';
      datos_bancos_cad += "|"+info_pago.CtaOrdenante;
    }
    if(info_pago.RfcEmisorCtaBen != '' && info_pago.RfcEmisorCtaBen != null){
      //datos_bancos_xml += "RfcEmisorCtaBen='"+info_pago.RfcEmisorCtaBen+"' "
      datos_bancos_xml += 'RfcEmisorCtaBen="'+info_pago.RfcEmisorCtaBen+'" ';
      datos_bancos_cad += "|"+info_pago.RfcEmisorCtaBen;
    }
    if(info_pago.CtaBeneficiario != '' && info_pago.CtaBeneficiario != null){
      //datos_bancos_xml += "CtaBeneficiario='"+info_pago.CtaBeneficiario+"' "
      datos_bancos_xml += 'CtaBeneficiario="'+info_pago.CtaBeneficiario+'" ';
      datos_bancos_cad += "|"+info_pago.CtaBeneficiario;
    }

    if(parseInt(info_pago.spei)){

      //datos_spei += " TipoCadPago='01' ";
      datos_spei += ' TipoCadPago="01" ';
      datos_spei_cad += "|01"

      if(info_pago.spei_certificado != ''){
        //datos_spei += "CertPago='"+info_pago.spei_certificado+"' ";
        datos_spei += 'CertPago="'+info_pago.spei_certificado+'" ';
        datos_spei_cad += "|"+info_pago.spei_certificado;
      }

      if(info_pago.spei_cadena != ''){
        //datos_spei += "CadPago='"+info_pago.spei_cadena+"' ";
        datos_spei += 'CadPago="'+info_pago.spei_cadena+'" ';
        datos_spei_cad += "|"+info_pago.spei_cadena;
      }

      if(info_pago.spei_sello != ''){
        //datos_spei += "SelloPago='"+info_pago.spei_sello+"' ";
        datos_spei += 'SelloPago="'+info_pago.spei_sello+'" ';
        datos_spei_cad += "|"+info_pago.spei_sello;
      }
    }

    valores = valores.substring(0, valores.length-3);

    //doctorel = "<pago10:Pago FechaPago='"+info_pago.fecha_pago+"' FormaDePagoP='"+info_pago.forma_pago+"' MonedaP='"+info_pago.moneda+"' Monto='"+parseFloat(info_pago.monto).toFixed(2)+"' "+datos_bancos_xml+datos_spei+">";
    doctorel = '<pago10:Pago FechaPago="'+info_pago.fecha_pago+'" FormaDePagoP="'+info_pago.forma_pago+'" MonedaP="'+info_pago.moneda+'" Monto="'+parseFloat(info_pago.monto).toFixed(2)+'" '+datos_bancos_xml+datos_spei+'>';
    cadena = info_pago.fecha_pago+"|"+info_pago.forma_pago+"|"+info_pago.moneda+"|"+parseFloat(info_pago.monto).toFixed(2)+datos_bancos_cad+datos_spei_cad;

    var SaldoInsoluto;
    var folio;
    valores = valores.split('@|@');
    var ii = 0; //Variable pivote

    // console.log("datos_bancos_xml: ",datos_bancos_xml);
    // console.log("datos_bancos_cad: ",datos_bancos_cad);
    // console.log("datos_spei: ",datos_spei);
    // console.log("datos_spei_cad: ",datos_spei_cad);
    // console.log("doctorel: ",doctorel);
    // console.log("cadena: ",cadena);
    // console.log("Valores: ",valores);
    // console.log("ids_aux: ",ids_aux);

    usoCfdi = "";
    contadorFacturasPPD=0;

    for(i=0;i<ids_aux.length;i++){
      
      SaldoInsoluto = 0;

      $.ajax({
        async:false,
        url: 'ajax.php?c=cuentas&f=info_rel2',
        type: 'POST',
        dataType: 'json',
        data: {
          idrel: ids_aux[i]
        },
      }).done(function(datos_rel) { //console.log("datos_rel: ",datos_rel);
        
        if(datos_rel.metodo_pago == 'PPD') {

          usoCfdi = datos_rel.usoCfdi;
          
          folio = "";
          if(datos_rel.folio != '' && datos_rel.folio != null)
              //folio = "Folio='"+datos_rel.folio+"'";
              folio = 'Folio="'+datos_rel.folio+'"';

          serie = "";
          if(datos_rel.serie != '' && datos_rel.serie != null)
              //serie = "Serie='"+datos_rel.serie+"'";
              serie = 'Serie="'+datos_rel.serie+'"';

          if(datos_rel.ImpSaldoAnt == 0)
              datos_rel.ImpSaldoAnt = info_pago.monto;

          SaldoInsoluto = parseFloat(datos_rel.ImpSaldoAnt) - parseFloat(valores[i]);

          //doctorel += "<pago10:DoctoRelacionado IdDocumento='"+datos_rel.uuid+"' "+serie+" "+folio+" MonedaDR='"+datos_rel.moneda+"' MetodoDePagoDR='"+datos_rel.metodo_pago+"' NumParcialidad='"+datos_rel.parcialidad+"' ImpSaldoAnt='"+parseFloat(datos_rel.ImpSaldoAnt).toFixed(2)+"' ImpPagado='"+parseFloat(valores[i]).toFixed(2)+"' ImpSaldoInsoluto='"+SaldoInsoluto.toFixed(2)+"' />";
          doctorel += '<pago10:DoctoRelacionado IdDocumento="'+datos_rel.uuid+'" '+serie+' '+folio+' MonedaDR="'+datos_rel.moneda+'" MetodoDePagoDR="'+datos_rel.metodo_pago+'" NumParcialidad="'+datos_rel.parcialidad+'" ImpSaldoAnt="'+parseFloat(datos_rel.ImpSaldoAnt).toFixed(2)+'" ImpPagado="'+parseFloat(valores[i]).toFixed(2)+'" ImpSaldoInsoluto="'+SaldoInsoluto.toFixed(2)+'"/>';
          cadena += "|"+datos_rel.uuid+"|"+datos_rel.serie+"|"+datos_rel.folio+"|"+datos_rel.moneda+"|"+datos_rel.metodo_pago+"|"+datos_rel.parcialidad+"|"+parseFloat(datos_rel.ImpSaldoAnt).toFixed(2)+"|"+parseFloat(valores[i]).toFixed(2)+"|"+SaldoInsoluto.toFixed(2);

          contadorFacturasPPD++;
        }
      }).fail(function(e) {
        console.log("Error.- ",e);
      });
    }

    doctorel += "</pago10:Pago>";
    
    // console.log('idcliprov: '+idcliprov+"\n");
    // console.log('cliprov: '+cliprov+"\n");
    // console.log("xml: "+doctorel);
    // console.log("cadena: "+cadena);
    // console.log("contadorFacturasPPD: ",contadorFacturasPPD)

    if(contadorFacturasPPD>0) {
      $.ajax({
      async:false,
      url: '../pos/ajax.php?c=caja&f=comprobantesPago',
      type: 'POST',
      dataType: 'json',
      data: {
        idcliprov: idcliprov,
        cliprov: cliprov,
        doctorel: doctorel,
        cadena: cadena,
        usoCfdi: usoCfdi
      },
      }).done(function(datos) { console.log(datos)
        if(datos.success) {
          alert(datos.estatus)
          guardarxmlBD(datos.xmlfile,datos.azurian,idcliprov,cliprov);
        } else {
          alert("Hubo un problema, "+datos.mensaje)
        }
      }).fail(function(e){
        console.log("Error.- ",e);
      });
    } else{
      console.log("No se genero factura porque no habia facturas PPD")
    }
  });
}

function guardarxmlBD(xmlfile,azurian,idcliprov,cliprov)
{
    $.post('../cont/ajax.php?c=almacen&f=guardarxmlBD',
        {
            xmlfile:xmlfile,
            caja:1,
            azurian:azurian,
            idcliprov:idcliprov,
            cliprov:cliprov
        },
        function(data)
        {
            if(data)
                console.log("La factura se guardo en la BD" +data)
                pdf33(azurian,idcliprov,cliprov);
        });
}

function printer(t,valores,monedas,cp)
{
    $.post('ajax.php?c=cuentas&f=printer',
        {
            idpago : window.id_pago,
            idrelaciones: window.ids,
            tipo: t,
            valores : valores,
            monedas: monedas,
            monedaPago : window.moneda,
            cp: cp,
            proc_final:1,
            ori:999
        },
        function(data)
        {
            var win=window.open('about:blank');
            with(win.document)
            {
              open();
              write(data);
              close();
            }
        });

}

function cuentas_sis_anterior(t)
{
    var tipo,funcion;
    if(parseInt(t))
    {
        tipo='pagar';
        funcion='cuentasxpagar';
    }
    else
    {
        tipo='cobrar';
        funcion='cuentasxcobrar';
    }

    if(confirm("Esta seguro que quiere cargar las cuentas por " + tipo + " de la version anterior?"))
    {

        $.post('ajax.php?c=cuentas&f=cuentas_sis_anterior',
        {
            t       :t,
            inst    :$("#instancia").val(),
            usu     :$("#usuario_p").val(),
            contra  :$("#contrasenia_p").val()
        },
        function(data)
        {
            console.log(data)
            //Si no hubo problemas
            if(!parseInt(data))
            {
                alert("Se creo un cargo por cada cuenta por " + tipo + " de la version anterior.")
                window.location = 'index.php?c=cuentas&f='+funcion;
            }
            //Si no existe la instancia o esta mal el login
            if(parseInt(data) == 1)
                alert("El usuario y/o la contraseña son invalidos, o no existe la instancia.")

            //Hubo un error en la consulta
            if(parseInt(data) == 2)
                alert("Ocurrio un error y no se generaron las cuentas.")

        });
    }
}

function NumCheck(e, field) {
  key = e.keyCode ? e.keyCode : e.which
  // backspace
  if (key == 8) return true
  // 0-9
  if (key > 47 && key < 58) {
    if (field.value == "") return true
    regexp = /.[0-9]{10}$/
    return !(regexp.test(field.value))
  }
  // .
  if (key == 46) {
    if (field.value == "") return false
    regexp = /^[0-9]+$/
    return regexp.test(field.value)
  }
  // other key
  return false

}

function eliminar_pago(id,cp)
{
 if(confirm('Esta seguro que desea eliminar este pago?'))
 {
    $.post('ajax.php?c=cuentas&f=eliminar_pago',
        {
           idpago:id,
           cp:cp
        },
        function(data)
        {
            if(data)
            {
                if(cp)
                    pagosSinAsignar();
                else
                    cobrosSinAsignar();
            }
            else
                alert("Ocurrió un error y no se elimino el pago "+data)
        });
 }
}

function verCamposCheqTrans(cp,idcliprov){

    $("#tipo_pago_spei").val(0)
    $("#spei_certificado").val('')
    $("#spei_cadena").val('')
    $("#spei_sello").val('')

    if(parseInt($("#forma_pago").val()) == 2){
        $(".dbancarios1").show()
        $(".dbancarios2,.spei").hide()
    }
    else if(parseInt($("#forma_pago").val()) == 7){
        $(".dbancarios1,.dbancarios2").show();
        $(".spei").hide();
    }

    $.post('ajax.php?c=cuentas&f=banco_proveedor_cliente',
    {
        formapago:$("#forma_pago").val(),
        cp:cp,
        idcliprov: idcliprov
    },
    function(data)
    {
        console.log(data)
        var datos = jQuery.parseJSON(data);

        $("#cuenta_bancaria_origen").val(datos.cuenta_bancaria_origen)
        $("#banco_origen_nac").val(datos.banco_origen_nac)
        $("#banco_origen_ext").val(datos.banco_origen_ext)
        $("#cuenta_bancaria_destino").val(datos.cuenta_bancaria_destino)
        $("#banco_destino_nac").val(datos.banco_destino_nac)
        $("#banco_destino_ext").val(datos.banco_destino_ext)
    });
}
function pdf33(azurian,idcliprov,cliprov){
//console.log(azurian);
var datos = jQuery.parseJSON(azurian);

    $.ajax({
        url: 'ajax.php?c=cuentas&f=datosFacturacionCom',
        type: 'POST',
        dataType: 'json',
        data: {idcliprov: idcliprov,
                cliprov : cliprov
            },
    })
    .done(function(res) {
        console.log(res);

            $.ajax({
                url: '../pos/ajax.php?c=caja&f=pdf33',
                type: 'POST',
                dataType: 'json',
                data: {uid: datos.datosTimbrado.UUID,
                        logo: res.logo,
                        obser : '',
                    },
            })
            .done(function(respPdf) {
                console.log(respPdf);

                $.ajax({
                    async: false,
                    type: 'POST',
                    url: '../pos/ajax.php?c=caja&f=envioFactura',
                    dataType: 'json',
                    data: {
                        uid: datos.datosTimbrado.UUID,
                        correo: res.correo,
                        azurian: datos,
                        doc: 2
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




            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
}

function es_spei()
{
    if($("#forma_pago").val() == '7' && $("#tipo_pago_spei").val() == '1'){
        $(".spei").show();
    }
    else{
        $(".spei").hide();
    }
}
