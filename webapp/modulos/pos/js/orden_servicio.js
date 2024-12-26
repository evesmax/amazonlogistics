function newOrder(){
	 $('#modalOrden').modal();
}
function mostrar_captura_nombres(){
	var cantidad = $('#pasajeros').val(), div_container = '', contador = $('.input-nombre').length;

	if (parseInt(cantidad) > 0 && parseInt(cantidad) <= 20) {
		$('#capturar_nombres').fadeIn('slow');

		if (parseInt(contador) == 0) {
			contador = 1;
		} else if(parseInt(contador) >= parseInt(cantidad)){
			contador = 100;
		} else {
			contador = parseInt(contador)+1;
		}

		for (var i = contador; i <= parseInt(cantidad); i++) {
			div_container = '';
			div_container += "<div class='col-md-4'>";
			div_container += "<label for='input_nombre_"+i+"'>Ingrese nombre persona "+i+":</label>";
			div_container += "<div class='input-group'>";
			div_container += "<span class='input-group-addon'><span class='glyphicon glyphicon-user'></span></span>"
			div_container += "<input type='text' class='form-control input-nombre' id='input_nombre_"+i+"' data-id='"+i+"'>";
			div_container += "</div>";
			div_container += "</div>";
			$('#inputs_nombres_container').append(div_container);
		}

	} else {
		alert("Ingrese un número mayor a 0 o menor a 20.");
	}
}


	function calcularCosto(id){
		 var fechaInicio = $("#idaV").val();
		 var fechaFin    = $("#regresoV").val();

		 var mFechaInicio =  moment(fechaInicio);
		 var mFechaFin    =  moment(fechaFin);

		 var diferenciaMinutos = mFechaFin.diff(mFechaInicio,'minutes');

	//	 alert(diferenciaMinutos);

		$.ajax({
			url: 'ajax.php?c=orden_servicio&f=calcularCostoViaje',
			type: 'POST',
			dataType: 'json',
			data: {
				minutos:diferenciaMinutos,
				idAeroNave:id
			},
			beforeSend: function(e){

				$("#calculandoCosto").text("Caculando costo...");
			},
			success: function(e){
				if(e.costoDeVuelo != null){
					$("#costo_viaje").val(e.costoDeVuelo);
				}
					$("#calculandoCosto").text("");
			},
			error: function(e){
				$("#calculandoCosto").text("");
			}
		});
	}


function reiniciar_inputs_nombres(){
	if (confirm('¿Realmente desea reiniciar la captura de nombres?')) {
		$('#inputs_nombres_container').html("");
	}
}

function reiniciar_inputs_escalas(){
	if (confirm('¿Realmente desea reiniciar la captura de escalas?')) {
		$('#inputs_escalas_container').html("");
	}
}

function confirmar_inputs_nombres(){
	var val = true;
	$.each($('.input-nombre'), function(index, input){
		if ($(input).val() == '' && val !== false) {
			alert("El campo "+$(input).attr('data-id')+" esta vacío.");
			val = false;
			return val;
		} else {
			if (($(input).val()).length < 5 && val !== false) {
				alert("El campo "+$(input).attr('data-id')+" no cumple con el minimo de 5 caracteres.");
				val = false;
				return val;
			} else {
				val = true;
			}
		}
	});
	if (val !== false) {
		$('#tabs_avion a[href="#captura"]').tab('show');
	}
}
function confirmar_inputs_escalas(){
	var val = true;
	$.each($('.input-nombre'), function(index, input){
		if ($(input).val() == '' && val !== false) {
			alert("El campo "+$(input).attr('data-id')+" esta vacío.");
			val = false;
			return val;
		} else {
			if (($(input).val()).length < 5 && val !== false) {
				alert("El campo "+$(input).attr('data-id')+" no cumple con el minimo de 5 caracteres.");
				val = false;
				return val;
			} else {
				val = true;
			}
		}
	});
	if (val !== false) {
		$('#tabs_avion a[href="#captura"]').tab('show');
	}
}
function mostrar_captura_escalas(){
	var cantidad = $('#escalas').val(), div_container = '', contador = $('.input-escalas').length;

	if (parseInt(cantidad) > 0 && parseInt(cantidad) <= 20) {
		$('#capturar_escalas').fadeIn('slow');

		if (parseInt(contador) == 0) {
			contador = 1;
		} else if(parseInt(contador) >= parseInt(cantidad)){
			contador = 100;
		} else {
			contador = parseInt(contador)+1;
		}
		var optionsSelec = $('#desOrigen').html()
		for (var i = contador; i <= parseInt(cantidad); i++) {
			div_container = '';
			div_container += "<div class='row'>"
			div_container += "<div class='col-xs-3'>";
			div_container += "<label for='input_escala_"+i+"'>Origen "+i+":</label>";


			//div_container += "<input type='text' class='form-control input-escalas' id='input_escala_"+i+"' data-id='"+i+"'>";
			div_container +='<select id="sel_origen_'+i+'" class="form-control sl2">'+optionsSelec+'</select>';

			div_container += "</div>";
			div_container += "<div class='col-xs-3'>";
			div_container += "<label for='input_escala_"+i+"'>Destino "+i+":</label>";


			//div_container += "<input type='text' class='form-control input-escalas' id='input_escala_"+i+"' data-id='"+i+"'>";
			div_container +='<select id="sel_destino_'+i+'" onchange="cambia_destino('+i+')" class="form-control sl2">'+optionsSelec+'</select>';

			div_container += "</div>";
			div_container += "<div class='col-xs-3'>";
			div_container += "<label for='input_escala_"+i+"'>Tiempo Aprox "+i+" (mins):</label>";
			//div_container += "<div class='input-group'>";
			//div_container += "<span class='input-group-addon'><span class='glyphicon glyphicon-user'></span></span>"
			div_container += "<input type='number' class='form-control input-escalas' id='input_escala_"+i+"' data-id='"+i+"' onkeyup='calculaTotalTime();'>";
			//div_container += "</div>";
			div_container += "</div>";

			div_container += "<div class='col-xs-3'>";
			div_container += "<label for='input_fecha_"+i+"'>Fecha "+i+":</label>";
			div_container += "<input type='text' class='form-control input-fecha' id='input_fecha_"+i+"' data-id='"+i+"'>";
			div_container += "</div>";

			div_container += "</div>";

			$('#inputs_escalas_container').append(div_container);

		}
		$('.sl2').select2({ width: '100%' });
		$('.input-fecha').datepicker({
		format: "yyyy-mm-dd",
		language: "es"
		});
		//$('.desOrige').clone().appendTo('#'+c);
		//$('.desOrige').clone().appendTo('#'+d);

	} else {
		alert("Ingrese un número mayor a 0 o menor a 20.");
	}
}

function cambiarTipoDeCambio(idTipoMoneda){
	$.ajax({
		url: 'ajax.php?c=orden_servicio&f=obtenerTipoDeCambioPorMoneda',
		type: 'POST',
		dataType: 'json',
		data: {
			tipoMoneda:idTipoMoneda
		},
		beforeSend: function(e){
			$("#esperaTipoCambio").text("Espera...");
		},
		success: function(e){
			if(e[0] != null){
				$("#tipo_cambio").val(e[0].tipo_cambio);
			}else{
				$("#tipo_cambio").val(0);
			}
			$("#esperaTipoCambio").text("");
		},
		error: function(e){
			$("#esperaTipoCambio").text("");
		}
	});
}

function cambia_destino(n)
{
	var n2 = n+1;
	$("#sel_origen_"+n2).val($("#sel_destino_"+n).val()).trigger('change');
}
function saveRequest(){
	var hayErrores = 0;
	$('.form-control').each(function(index) {
		if($(this).attr('type') != 'search'){
			if($(this).val() == ''){
				hayErrores++;
			}
		}
	});

	if(!hayErrores){
		var num_viaje = $('#num_viaje').val();
		var pasajeros = $('#pasajeros').val();
		var pasajeros_nom = '';
		$(".input-nombre").each(function(index){
			pasajeros_nom += $(this).val() + ","
		})
		var aeronave = $('#aeronave').val();
		var escalas = $('#escalas').val();
		var escalas_array = new Array()
		for(i=1;i<=escalas;i++){
			escalas_array.push(new Array(
				i,
				$("#sel_origen_"+i).val(),
				$("#sel_destino_"+i).val(),
				$("#input_escala_"+i).val(),
				$("#input_fecha_"+i).val()
				));
		}
		escalas_array = JSON.stringify(escalas_array);
		//alert(escalas_array)

		var origen = $('#sel_origen_1').val();
		var destino = $('#sel_destino_'+escalas).val();
		var ida = $('#idaV').val();
		var regreso = $('#regresoV').val();
		var redondo = $('input:radio[name=redondo]:checked').val();
		var tipoViaje = $('input:radio[name=nacional]:checked').val();
		var totalTiempo = $('#totalTiempo').val();
		var nombreCliente = $('#nombreCliente').val();
		var costo_viaje = $('#costo_viaje').val();
		var idmoneda = $('#idmoneda').val();
		var tipo_cambio = $('#tipo_cambio').val();
		var tarifaDeViaje = $("#tarifaDeViaje").val();
		if(tipo_cambio == '' || tipo_cambio == '0.00' || tipo_cambio == '0' || tipo_cambio == ' ')
			$tipo_cambio = '1';

		    $('#lblMensajeEstado').text('Procesando...');
	        $('#modalMensajes').modal({
	                        show:true,
	                        keyboard: false,
	                    });
		$.ajax({
			url: 'ajax.php?c=orden_servicio&f=guarda_solicitud',
			type: 'POST',
			dataType: 'json',
			data: {origen : origen,
				   num_viaje : num_viaje,
				   destino : destino,
				   pasajeros : pasajeros,
				   pasajeros_nom:pasajeros_nom,
				   aeronave : aeronave,
				   escalas : escalas,
				   escalas_array:escalas_array,
				   ida : ida,
				   regreso : regreso,
				   redondo : redondo,
				   tipoViaje : tipoViaje,
				   totalTiempo : totalTiempo,
				   nombreCliente : nombreCliente,
				   costo_viaje : costo_viaje,
				   idmoneda:idmoneda,
				   tipo_cambio:tipo_cambio,
					 tarifaDeViaje:tarifaDeViaje
			},
		})
		.done(function(resp) {
			console.log(resp);
			 $('#modalMensajes').modal('hide');
			 window.location.reload();
		})
		.fail(function(e) {
			console.log(e);
		})
		.always(function() {
			console.log("complete");
		});
	}
	else{
		alert("Falta llenar un campo.")
	}
}
function newGasto(id){
		$('#idSolici').val(id);
		listaGastos(0,id);
}
function newGasto2(){
	$('#nuevoGasto').modal();
}
function saveGasto(){
	var fecha = $('#fechaGasto').val();
	var importe = $('#importeGasto').val();
	var formaPago = $('#fpGasto').val();
	var cuentaGasto = $('#cuentaGasto').val();
	var segmentoGasto = $('#segmentoGasto').val();
	var sucursalGasto = $('#sucursalGasto').val();
	var categoriaGasto = $('#categoriaGasto').val();
	var referenciaGasto = $('#referenciaGasto').val();
		$('#lblMensajeEstado').text('Procesando...');
        $('#modalMensajes').modal({
                        show:true,
                        keyboard: false,
                    });
	$.ajax({
		url: 'ajax.php?c=orden_servicio&f=saveGasto',
		type: 'POST',
		dataType: 'json',
		data: {fecha: fecha,
				importe : importe,
				formaPago : formaPago,
				cuentaGasto : cuentaGasto,
				segmentoGasto : segmentoGasto,
				sucursalGasto : sucursalGasto,
				categoriaGasto : categoriaGasto,
				referenciaGasto : referenciaGasto,
				idSolicitud : $('#idSolici').val(),
		},
	})
	.done(function(resp) {
		console.log(resp);
		if(resp.estatus==true){
			var table = $('#tableGridGastos').DataTable();
			//table.clear().draw();
			    $.each(resp.newRow, function(index, val) {

                 x ='<tr class="filas" id="r_'+val.id+'">'+
                                '<td>'+val.id+'</td>'+
                                '<td>'+val.concepto+'</td>'+
                                '<td>'+val.fecha+'</td>'+
                                '<td>'+val.fp+'</td>'+
                                '<td>'+val.banco+' ('+val.cuenta+')</td>'+
                                '<td>$'+parseFloat(val.importe).toFixed(2)+'</td>'+
                                //'<td>$'+parseFloat(val.monto).toFixed(2)+'</td>'+
                                //'<td><button class="btn btn-primary" onclick="editaGasto('+val.id+');" type="button"><i class="fa fa-pen"></i></button><button class="btn btn-danger" onclick="eliminaGasto('+val.id+');" type="button"><i class="fa fa-trash-alt"></i></button>X</td>';
                                '<td><button class="btn btn-primary" onclick="editaGasto('+val.id+');" type="button"><i class="fa fa-pen"></i></button><button class="btn btn-danger" onclick="eliminaGasto('+val.id+');" type="button"><i class="fa fa-trash-alt"></i></button>X</td>';
                                '</tr>';

                    table.row.add($(x)).draw();
            });
			$('#modalMensajes').modal('hide');
			$('#nuevoGasto').modal('hide');
		}
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});

}
function eliminaGasto(id){
		var r = confirm("Estas seguro de  eliminar el gasto?");
        if (r == true) {
        	$.ajax({
        		url: 'ajax.php?c=orden_servicio&f=eliminaGasto',
        		type: 'post',
        		dataType: 'json',
        		data: {id: id},
        	})
        	.done(function(resp) {
        		console.log(resp);
        		if(resp.estatus==true){
        			var table = $('#tableGridGastos').DataTable();
        			table.row("#r_"+id).remove().draw();
        		}

        	})
        	.fail(function() {
        		console.log("error");
        	})
        	.always(function() {
        		console.log("complete");
        	});

        }else{

        }
}
function cotizaModal(id){
	$('#idSoliCoti').val(id);
	$('#modalCotiza').modal();
}
function agregaProd(){
	var idProd = $('#prod').val();
	$.ajax({
		url: 'ajax.php?c=orden_servicio&f=agregaPCoti',
		type: 'POST',
		dataType: 'json',
		data: {idProd: idProd},
	})
	.done(function(data) {
		console.log(data);
		cantidad  = 1;
		precio = 0;
		        $('#proTable tr:last').after('<tr idProducto="'+data[0].id+'" id="x_'+data[0].id+'">'+
                                '<td><span class="glyphicon glyphicon-remove" onclick="elimina('+data[0].id+');"></span></td>'+
                                '<td>'+data[0].codigo+'</td>'+
                                '<td>'+data[0].nombre+'</td>'+
                                //'<td>'+cantidad+'</td>'+
                                //'<td><input id="ordenado_'+data[0].id+'" type="text" value="'+cantidad+'"></td>'+
                                '<td><input id="cant_'+data[0].id+'" type="text" value="'+cantidad+'" onkeyup="calculaPrecios(2)"></td>'+
                                '<td>$<input id="cost_'+data[0].id+'" type="text" value="'+precio+'" onkeyup="calculaPrecios(2)"></td>'+
                                //'<td>$'+precio+'</td>'+
                                '<td><label id="subto_'+data[0].id+'">$'+(precio * cantidad)+'</label></td>'+
                                '</tr>');
		         calculaPrecios(2);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});

}
function elimina(id){
    //alert('i');
    $('#x_'+id).remove();
}
function calculaPrecios(tipo){
var subtotal = 0;
var total = 0;
var productos = '';
var x = 0;


    if(tipo==1){
        var oTable = $('#proTable').dataTable();
        var allPages = oTable.fnGetNodes();
        console.log(allPages);
        $(allPages).each(function (index)
        {   //console.log($("#tablita input:hidden"));
            //alert('alertoeoeoeoeoeoe');
            //contador++;
            idProducto = $(this, allPages).attr('idProducto');
            //alert(idProducto);
            cantidad = $('#cant_'+idProducto, allPages).val();
            precio = $('#cost_'+idProducto, allPages).val();
            x = precio * cantidad;
            $('#subto_'+idProducto, allPages).text('$'+parseFloat(x).toFixed(2));
            if(cantidad > 0){

                subtotal = parseFloat(precio) * parseFloat(cantidad);
                productos +=idProducto+'-'+cantidad+'-'+precio+'/';
            }

            total +=parseFloat(subtotal);
            subtotal = 0;
        });
    }else{
        //var oTable = $('#proTable').dataTable();
        //var allPages = oTable.fnGetNodes();
        //console.log(allPages);
        $('#proTable tr').each(function (index)
        {   //console.log($("#tablita input:hidden"));
            //alert('alertoeoeoeoeoeoe');
            //contador++;
            idProducto = $(this).attr('idProducto');
            //alert(idProducto);
            cantidad = $('#cant_'+idProducto).val();
            precio = $('#cost_'+idProducto).val();
            x = precio * cantidad;
            $('#subto_'+idProducto).text('$'+parseFloat(x).toFixed(2));
            if(cantidad > 0){

                subtotal = parseFloat(precio) * parseFloat(cantidad);
                productos +=idProducto+'-'+cantidad+'-'+precio+'/';
            }

            total +=parseFloat(subtotal);
            subtotal = 0;
        });
    }
    //alert(productos);
    $.ajax({
        url: 'ajax.php?c=orden_servicio&f=calculaPrecios',
        type: 'POST',
        dataType: 'json',
        data: {productos: productos},
    })
    .done(function(data) {
        console.log(data);
        $('#impuestosDiv').empty();
        $('.totalesDiv').empty();
        $.each(data.cargos.impuestosPorcentajes, function(index, val) {
            $('#impuestosDiv').append('<div class="row">'+
                        '<div class="col-sm-6"><label>'+index+':</label></div>'+
                        '<div class="col-sm-6"><label>$'+parseFloat(val).toFixed(2)+'</label></div>'+
                        '</div>');
        });
        $('#subtotalDiv').append('<div class="row">'+
                        '<div class="col-sm-6"><h4>Subtotal:$'+parseFloat(data.cargos.subtotal).toFixed(2)+'</h4></div>'+
                        '</div>');
        $('#totalDiv').append('<div class="row">'+
                        '<div class="col-sm-6"><h4>Total:$'+parseFloat(data.cargos.total).toFixed(2)+'</h4></div>'+
                        '</div>');

        $('#inputSubTotal').val(parseFloat(data.cargos.subtotal).toFixed(2));
        $('#inputTotal').val(parseFloat(data.cargos.total).toFixed(2));
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

    $('#totalOrden').val(parseFloat(total).toFixed(2));
    $('#totalOrdenLable').text(parseFloat(total).toFixed(2));
}
function gurdarCoti(tipo){
    $('#guardaDiv').hide();
    $('#sded').show();
loadingModal();
   var idSoliCoti = $('#idSoliCoti').val();
    //var idAlmacen = $('#almacen').val();
    var obs = $('#obs').val();

    var subTotal = $('#inputSubTotal').val();
    var total = $('#inputTotal').val();
    var user = $('#autorizo').val();

    var productos = '';
    var contador = 0;
    var error = 0;


    if(tipo==1){
        var oTable = $('#proTable').dataTable();
        var allPages = oTable.fnGetNodes();

        $(allPages).each(function (index)
        {   //console.log($("#tablita input:hidden"));

            contador++;
            idProducto = $(this,allPages).attr('idProducto');
            cantidad = $('#cant_'+idProducto, allPages).val();
            if(cantidad < 1 && cantidad!=''){

                error = 1;

            }
            precio = $('#cost_'+idProducto, allPages).val();
            if(cantidad!=''){
                productos +=idProducto+'-'+cantidad+'-'+precio+'/';
            }

        });
    }else{

        $('#proTable tr').each(function (index)
        {   //console.log($("#tablita input:hidden"));

            contador++;
            idProducto = $(this).attr('idProducto');
            cantidad = $('#cant_'+idProducto).val();
            if(cantidad < 1 && cantidad!=''){

                error = 1;

            }
            precio = $('#cost_'+idProducto).val();
            if(cantidad!=''){
                productos +=idProducto+'-'+cantidad+'-'+precio+'/';
            }

        });
    }

    if(error==1){
        alert('La cantidad a pedir debe ser mayor a cero.');
        $('#guardaDiv').show();
        $('#sded').hide();
        return false;
    }
    if(productos==''){
        alert('No existen productos en la compra, agregalos.');
        $('#guardaDiv').show();
        $('#sded').hide();
        return false;
    }

    $.ajax({
        url: 'ajax.php?c=orden_servicio&f=gurdarCoti',
        type: 'POST',
        dataType: 'json',
        data: {

               productos: productos,
               idSoliCoti : idSoliCoti,
               subTotal : subTotal,
               total : total,
               obs : obs,
           },
    })
    .done(function(data) {
        console.log(data);
        cierraModal();
        if(data.status==true){
        	$.ajax({
        		url: 'ajax.php?c=orden_servicio&f=pdfCotizacion',
        		type: 'POST',
        		dataType: 'json',
        		data: {idSoliCoti: idSoliCoti},
        	})
        	.done(function(data) {
        		console.log(data);
        		alert('Se genero tu cotizacion');
  				window.location.reload();
        	})
        	.fail(function() {
        		console.log("error");
        	})
        	.always(function() {
        		console.log("complete");
        	});


        }


    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });


}
 function cierraModal(){
    $('#modalMensajes').modal('toggle');
 }
 function loadingModal(){
    $('#modalMensajes').modal({
        show:true,
    });
 }
 function verPdf(id){
 	 window.open("../../modulos/cotizaciones/cotizacionesPdf/cotizacion_"+id+".pdf");
 }
 function verPdfF(id){
 	 window.open("../../modulos/cont/xmls/facturas/temporales/"+id+".xml");
 }
 function calculaTotalTime(){
	var val = true;
	var total = 0;
	$.each($('.input-escalas'), function(index, input){
		total += Number($(input).val())
	});
	$('#totalTiempo').val(total);


}
function ventaFac(id){

	$.ajax({
		url: 'ajax.php?c=orden_servicio&f=sendCajaOrden',
		type: 'POST',
		dataType: 'json',
		data: {id: id},
	})
	.done(function(data) {
		idPedido=id;
		console.log("success");
		 /* if(data.venta==true){
          alert('Este pedido ya se hizo venta');
          return false;
        } */
        //window.parent.agregatab("../../modulos/appministra/index.php?c=configuracion&f=listas_precio","Listas de Precios","",1988);
          //$.each(data.codigo, function(index, val) {
                var outElement=$("#tb3457-u",window.parent.document).parent();
                var caja=outElement.find("#tb2051-u");
                var pestana=$("body",window.parent.document).find("#tb2051-1");
                var openCaja=$("body",window.parent.document).find("#mnu_2051");
                var pathname = window.location.pathname;
                var url=document.location.host+pathname;

                openCaja.trigger('click');

                pestana.trigger('click');
                //if(caja.length>0){
                  var campoBuscar=$(".frurl",caja).contents().find("#search-producto");
                  var campoCantidad=$(".frurl",caja).contents().find("#cantidad-producto");
                  var campoPedido=$(".frurl",caja).contents().find("#idPedido");

                  ///PAra el pedido

                  campoPedido.trigger('focus');
                  campoPedido.val(idPedido);

                  //campoCantidad.trigger('focus');
                  //campoCantidad.val(val.cantidad);

                  campoBuscar.trigger("focus");
                  //campoBuscar.trigger("click");
                  campoBuscar.val('OSPP'+idPedido);
                  campoBuscar.trigger({type: "keypress", which: 13});


                var outElement=$("#tb3457-u",window.parent.document).parent();
                var caja=outElement.find("#tb2357-u");
                var pestana=$("body",window.parent.document).find("#tb2357-1");
                var openCaja=$("body",window.parent.document).find("#mnu_2357");
                var pathname = window.location.pathname;
                var url=document.location.host+pathname;

                openCaja.trigger('click');

                pestana.trigger('click');
                //if(caja.length>0){
                  var campoBuscar=$(".frurl",caja).contents().find("#search-producto");
                  var campoCantidad=$(".frurl",caja).contents().find("#cantidad-producto");
                  var campoPedido=$(".frurl",caja).contents().find("#idPedido");

                  ///PAra el pedido

                  campoPedido.trigger('focus');
                  campoPedido.val(idPedido);

                  //campoCantidad.trigger('focus');
                  //campoCantidad.val(val.cantidad);

                  campoBuscar.trigger("focus");
                  //campoBuscar.trigger("click");
                  campoBuscar.val('OSPP'+idPedido);
                  campoBuscar.trigger({type: "keypress", which: 13});


                  var clienteCajaStr=$(".frurl",caja).contents().find('#cliente-caja');
                  var clienteCajaId=$(".frurl",caja).contents().find('#hidencliente-caja');
                  conosole.log(clienteCajaStr)
                  //clienteCajaStr.typeahead('val', 'ZOILA');
                  alert(clienteCajaStr.val())
                  //$('#hidencliente-caja').val(666);
                  //caja.checatimbres(666);




	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});

}
function verDocumentos(id){
	window.open("../../modulos/facturas/"+id+".pdf");
	window.open("../../modulos/cont/xmls/facturas/temporales/"+id+".xml");
}

function listaCategorias(){
	 $.post('ajax.php?c=orden_servicio&f=listaCategorias',
    function(data){
		$('#tableGridCategorias_body').html(data);

	});
}

function generar()
{
	var arreglo = new Array()
	$(".checks").each(function(index){
		if($(this).prop('checked'))
			arreglo.push($(this).attr('check'));
	});
	console.log(arreglo)
	var idSolicitud = $('#idSolici').val();
	listaGastos(arreglo,idSolicitud)
}

function listaGastos(arreglo,idSolicitud)
{
	console.log("***************************************");
	$.ajax({
		url: 'ajax.php?c=orden_servicio&f=gastosInfoSum',
		type: 'POST',
		dataType: 'json',
		data: {
			idSolicitud:idSolicitud
		},
		beforeSend: function(e){
			$("#sumandoGastos").text("Estamos realizando la suma...");
		},
		success: function(e){
			console.log(e.importe);
			/*if(e[0] != null){
				$("#tipo_cambio").val(e[0].tipo_cambio);
			}else{
				$("#tipo_cambio").val(0);
			}*/
			$("#totalGastos").val(e.importe);
			$("#sumandoGastos").text("");
		},
		error: function(e){
			$("#sumandoGastos").text("");
		}
	});

	$.post('ajax.php?c=orden_servicio&f=gastosInfo', {
		arreglo:arreglo,
		id:idSolicitud
	},function(data)
            {
            	console.log(data);
				$('#modalGastos').modal();
                var datos = jQuery.parseJSON(data);
                console.log(datos);
                $('#tableGridGastos').DataTable().rows().remove();
                $('#tableGridGastos').DataTable().destroy();
                $('#tableGridGastos').DataTable( {
                    dom: 'Bfrtip',
                    buttons: [ 'pageLength', 'excel' ],
                    language: {
                        buttons: {
                            pageLength: "Mostrar %d filas"
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
                     data:datos,
                     columns: [
                        { data: 'num_viaje' },
                        { data: 'fecha' },
                        { data: 'categoria' },
                        { data: 'importe' },
                        { data: 'Moneda' },
                        { data: 'tipoCambio' },
                        { data: 'cuenta' },
                        { data: 'formaPago' },
                        { data: 'referencia' },
                    ]
                });
            });
}

function guardar()
{
	var arreglo = new Array()
	var arreglo_temps = new Array()
	var idgasto
	$(".num_viaje").each(function(index){
		idgasto = $(this).attr('gasto')
			arreglo.push(new Array(
				idgasto,
				$(this).val(),
				$("#fecha_"+idgasto).val(),
				$("#idcategoria_"+idgasto).val(),
				$("#importe_"+idgasto).val(),
				$("#idmoneda_"+idgasto).val(),
				$("#tipoCambio_"+idgasto).val(),
				$("#cuentas_"+idgasto).val(),
				$("#formaPago_"+idgasto).val(),
				$("#referencia_"+idgasto).val())
			);
	});
	arreglo = JSON.stringify(arreglo)
	//console.log(arreglo)

	$(".num_viaje_temp").each(function(index){
			idgasto = $(this).attr('num')
			arreglo_temps.push(new Array(
				idgasto,
				$(this).val(),
				$("#fecha_0_temp_"+idgasto).val(),
				$("#idcategoria_0_temp_"+idgasto).val(),
				$("#importe_0_temp_"+idgasto).val(),
				$("#idmoneda_0_temp_"+idgasto).val(),
				$("#tipoCambio_0_temp_"+idgasto).val(),
				$("#cuentas_0_temp_"+idgasto).val(),
				$("#formaPago_0_temp_"+idgasto).val(),
				$("#referencia_0_temp_"+idgasto).val())
			);
	});
	arreglo_temps = JSON.stringify(arreglo_temps)
	//console.log(arreglo_temps)
	$.post('ajax.php?c=orden_servicio&f=guardar_gastos', {
		arreglo:arreglo,
		arreglo_temps:arreglo_temps,
		idSolicitud:$('#idSolici').val()
	},function(data)
      {
      	console.log(data)
      	$('#modalGastos').modal('hide');
      });
}

function autorizar(n){
	$.post('ajax.php?c=orden_servicio&f=autorizar', {
		idsolicitud:$('#idsol-aut').val(),
		aprobado:n
	},function(data)
      {
      	console.log(data)
      	$('#modalAutorizar').modal('hide');
      	if(data){
      		if(n)
      			$("#apr_"+$('#idsol-aut').val()).html("<span class='label label-success'>Aprobado</span>")
      		else
      			$("#apr_"+$('#idsol-aut').val()).html("<span class='label label-danger'>No Aprobado</span>")
      	}
      	else
      		alert("Ocurrio un error;")
      });
}
function abre_aprobacion(id){
	$("#idsol-aut").val('');
	$('#modalAutorizar').modal()
	$("#idsol-aut").val(id);
}
