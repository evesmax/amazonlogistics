table = $('#tablaprods').DataTable();
table.destroy();

if ($("#ordenmasiva").val() == 2) {

	if (!$("#thlote").length) {
		$("#tablaprods thead th:last").before("<th id='thlote'>Lote</th>");

	}
}

$('#div_ciclo').css('display', 'none');
$('#btn_savequit_usar').css('visibility', 'hidden');
resetearReq();

$('#btnlistorden').css('visibility', 'hidden');
$("#btnexplosionmasiva").css('visibility', 'hidden');
$('#btnback').css('visibility', 'visible');
//lotesss

table = $('#tablaprods').DataTable();
table.clear().draw();

$('#listareq').css('display', 'none');
$('#modal-conf1').modal('hide');
$('#nreq').css('display', 'none');
$('#nreq_load').css('display', 'block');


$(function() {

	
	$('#precionuevo').numeric();
	$('#date_entrega').datepicker({
		format : "yyyy-mm-dd",
		language : "es"
	});

	$('#date_hoy').datepicker({
		format : "yyyy-mm-dd",
		language : "es",
		startDate : new Date()

	}).on('changeDate', function(selected) {
		var minDate = new Date(selected.date.valueOf());
		$('#date_entrega').val("");
		minDate.setDate(minDate.getDate() + 1);

		$('#date_entrega').datepicker('setStartDate', minDate);
	});

	var table = $('#tablaprods').DataTable();

	var today = new Date();
	today.setDate(today.getDate() + 1);

	var dd = today.getDate();
	var mm = today.getMonth() + 1;
	//January is 0!

	var yyyy = today.getFullYear();
	if (dd < 10) {
		dd = '0' + dd;
	}
	if (mm < 10) {
		mm = '0' + mm;
	}
	var today = yyyy + '/' + mm + '/' + dd;
	$('#date_entrega').val(today);
	var today = new Date();

	var dd = today.getDate();
	var mm = today.getMonth() + 1;
	//January is 0!

	var yyyy = today.getFullYear();
	if (dd < 10) {
		dd = '0' + dd;
	}
	if (mm < 10) {
		mm = '0' + mm;
	}
	var today = yyyy + '/' + mm + '/' + dd;

	$('#date_hoy').val(today);

	//Solucion al scroll-y
	$('#tablaprods_wrapper div:nth-child(2) div:nth-child(1)').css('overflow-y', 'auto');

	$('#tablaprods_info').css('margin-to', '-10px');

	$('#c_productos').select2();

	$('#c_proveedores').select2({
		width : '300px'
	});
	$('#c_solicitante').select2();
	$('#c_prioridad').select2();
	$('#c_tipogasto').select2();
	$('#c_area').select2();
	$('#c_almacen').select2();

	$('#c_sucursal').select2();
	$('footer div').remove();

	$("#c_productos").change(function() {
		$('#btn_addProd').trigger('click');
	});

	$("#btn_addProd").click(function() {//multipleord

		idProducto = $('#c_productos').val();

		var lote = "";
		if (idProducto > 0) {// si marca un producto
			if ($("#ordenmasiva").val() == 1) {
				d = $('#filasprods tr').find('td').not(".dataTables_empty").length;
				//d=$('#filasprods tr').length;
				disabled_btn('#btn_addProd', 'Procesando...');

				if ($("#tr_" + idProducto).length) {
					valorig = $("#tr_" + idProducto + " input").val();
					$("#tr_" + idProducto + " input").val((valorig * 1) + 1);
					//refreshCants(idProducto,0,0);
					enabled_btn('#btn_addProd', 'Agregar producto');
					return false;
				}

				if (d > 0) {
					alert('Solo puedes agregar un articulo por orden de produccion');
					enabled_btn('#btn_addProd', 'Agregar producto');
					return false;
				}
			} else {
				lote = $("#lote").val();
			}
			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_addProductoProduccion",
				type : 'POST',
				dataType : 'JSON',
				data : {
					idProducto : idProducto
				},
				success : function(r) {
					console.log(r);
					if (r.success == 1) {
						
						var btndescP = '';

						if (r.datos[0].minimo === null) {
							r.datos[0].minimo = 0;
						}
						table = $('#tablaprods').DataTable();
						
						var cantprd = r.datos[0].minimo;
						
						if( $("#cant_x_lote").val() == 1){
							if(r.datos[0].cant_x_lote>0){
								cantprd = r.datos[0].cant_x_lote;
							}
							
						}
						
						if ($("#ordenmasiva").val() == 1) {

							var Rowdata = "<tr ch='0' newp='0' oldp='0' tipoDesc='0' montoD='0' id='tr_" + r.datos[0].id + "'><!--<td>0</td>--><td>" + r.datos[0].codigo + "</td><td style='cursor:pointer;' onclick='modalDescuento(" + r.datos[0].id + ",0);'>" + r.datos[0].descripcion_corta + "</td><td>" + r.datos[0].clave + "</td><!--<td id='valUnit'>" + r.adds + "</td>--><td><input style='width:60%;' class='numeros' type='text' fact='" + r.datos[0].factor + "' min='" + r.datos[0].minimo + "' value='" + cantprd + "'/></td><!--<td class='valImporte' implimpio='" + r.datos[0].costo + "' id='valImporte'>" + r.datos[0].costo + "</td>--><td><button onclick='removeProdReq(" + r.datos[0].id + ",0);' id='btn_addProd' class='btn btn-sm btn-danger' type='button' style='height:26px;'>Quitar</button>" + btndescP + "</td></tr>";

						} else {

							var Rowdata = "<tr ch='0' newp='0' oldp='0' tipoDesc='0' montoD='0' id='tr_" + r.datos[0].id + "'><!--<td>0</td>--><td>" + r.datos[0].codigo + "</td><td style='cursor:pointer;' onclick='modalDescuento(" + r.datos[0].id + ",0);'>" + r.datos[0].descripcion_corta + "</td><td>" + r.datos[0].clave + "</td><!--<td id='valUnit'>" + r.adds + "</td>--><td><input style='width:60%;' class='numeros' type='text' fact='" + r.datos[0].factor + "' min='" + r.datos[0].minimo + "' value='" + cantprd + "'/></td><!--<td class='valImporte' implimpio='" + r.datos[0].costo + "' id='valImporte'>" + r.datos[0].costo + "</td>--><td><input style='width:60%;' class='lotess' data-id='" + r.datos[0].id + "' type='text' value=" + lote + " ></td><td><button onclick='removeProdReq(" + r.datos[0].id + ",0);' id='btn_addProd' class='btn btn-sm btn-danger' type='button' style='height:26px;'>Quitar</button>" + btndescP + "</td></tr>";

						}
						//lotess

						table.row.add($(Rowdata)).draw();

						$('#panel_tabla').css('display', 'block');
						$('.numeros').numeric();
						enabled_btn('#btn_addProd', 'Agregar producto');
						//refreshCants(idProducto,0,0);
						cadcarAux = 0;

						$('.numeros').change(function() {
							if ($(this).attr("min") == 'null') {
								minimo = 0;
							} else {
								minimo = $(this).attr("min");
							}

							if ($(this).attr("fact") == 'null' || $(this).attr("fact") == '') {
								f = 0;
							} else {
								f = $(this).attr("fact");
							}

							if ($(this).val() * 1 < minimo * 1) {
								alert("Cantidad es menor al minimo permitido");
								$(this).val(minimo);
							}

							if (f > 0) {
								if ($(this).val() % f == 0) {
								} else {//por el valor q puso en el app_productos en produccion
									alert('La cantidad solo pueden ser multiplos del factor seleccionado para este producto (' + f + ')');

									$(this).val(f);
								}
							}

						});

					} else {
						alert('Error al agregar producto');
					}

				}
			});
		} else {
			alert('Selecciona un producto valido');
		}
	});

	//
	$("#btn_savequit").click(function() {

		iduserlog = $('#iduserlog').val();
		option = $('#ph span').attr('opt');
		id_op = $('#txt_nreq').text();
		idrequi = $('#idrequi').val();
		orden = $('#orden').val();

		fecha_registro = $('#date_hoy').val();
		fecha_entrega = $('#date_entrega').val();
		sol = $('#c_solicitante').val();
		prioridad = $('#c_prioridad').val();
		sucursal = $('#c_sucursal').val();
		obs = $('#comment').val();
		obs = obs.replace(/\r\n|\r|\n/g, "<br />");
		sol = $('#c_solicitante').val();

		deten = 0;
		if ($("#ordenmasiva").val() == 1) {
			if (prioridad == 0) {
				alert('Tienes que seleccionar una prioridad');
				deten = 1;
			} else if (sucursal == 0 && deten == 0) {
				alert('Tienes que seleccionar una sucursal');
				deten = 1;
			} else if (fecha_registro == '' && deten == 0) {
				alert('Tienes que seleccionar una fecha de registro');
				deten = 1;
			} else if (fecha_entrega == '' && deten == 0) {
				alert('Tienes que seleccionar una fecha de entrega');
				deten = 1;
			}
		}

		/*krmn insumos variables
		 para q no continue si no termino el movimiento de insumos*/
		if ($(".unidad").length > 0) {
			alert('Faltan unidades en sus insumos');
			deten = 1;
		}

		if (deten == 1) {
			enabled_btn('#btn_savequit', 'Generar Orden de produccion');
			return false;
		}

		detenertodo = 0;
		noceros = 0;

		if (option == 3) {
			//AQUI ES PARA EXPLOSIONNNN D MATERIAL
			totalinsumos = $('#tttr').attr('totlimpio');
			totalinsumos = (totalinsumos * 1);
			if (totalinsumos == 0) {
				alert('No puede continuar, el total debe ser mayor a 0');
				return false;
			}

			var banderaprecantidad = 0;
			if (confirm("Desea crear la pre-requision solo con el material faltante\nACEPTAR -SI  CANCELAR -NO")) {
				banderaprecantidad = 1;
			}

			idsProductos = $('#filasprods2 tr').map(function() {
				eshead = $(this).attr('eshead');
				if (eshead == 2) {
					detenertodo++;
				}
				if (eshead == 1) {

				} else {

					trid = this.id;
					id = trid.split('tr_');
					masids = id[1].split('_');
					idpadre = masids[0];
					idProd = masids[1];

					cant = $(this).find('#valCantidad').text();
					if ($("#productovariable").val() == 1) {
						cant = $("#insumo" + idProd).val();
					}
					cant = cant * 1;

					uni = $(this).find('.numeros').val();
					uni = uni * 1;

					lala = $(this).find('#cmbProv_' + idpadre + '_' + idProd).val();

					if ( typeof lala !== "undefined") {
						jj = lala.split('-');
						idProv = jj[0];

					} else {
						idProv = 0;
					}
					if ( typeof idpadre !== "undefined" && typeof idProd !== "undefined") {
						if (banderaprecantidad == 1) {
							var diferencia = parseFloat(cant - $("#exis" + idProd).text());
							cant = diferencia;
						}

						id = idProv + '>' + idpadre + '>' + idProd + '>' + cant + '>' + uni;
					}

					return id;
				}
			}).get().join('--c--');
		} else {//ESTE ES PARA NUEVA ORDENN
			//lotess
			var arraylotes = new Array();
			$(".lotess").each(function(index) {
				if ($(this).attr("data-id") > 0) {

					item = {};

					item[$(this).attr("data-id")] = $(this).val();

					arraylotes.push(item);
				}

			});
			//limpio los valores nulos
			arraylotes = arraylotes.filter(Boolean);
			arraylotes = JSON.stringify(arraylotes);
			idsProductos = $('#filasprods tr').map(function() {
				cant = $(this).find('.numeros').val();
				if (cant == '' || cant == 0 || cant <= '0') {
					noceros++;
				}
				trid = this.id;
				id = trid.split('tr_');

				if ( typeof id[1] !== "undefined") {

					id = id[1] + '>' + cant;
				}
				return id;
			}).get().join('--c--');

		}

		if (noceros > 0) {
			alert('La cantidad no puede ser 0');
			return false;
		}

		if (detenertodo > 0) {
			alert('No puede continuar ya que hay productos que no cuentan con insumos registrados');
			return false;
		}

		ttt = $('#tttr').attr('totlimpio');

		if (idsProductos == '') {
			msg_error(1);
			enabled_btn('#btn_savequit', 'Generar Orden de produccion');
			return false;
		} else {
			//("envia");
			//alert("envia");
			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_guardarOrdenP",
				type : 'POST',
				data : {
					idsProductos : idsProductos,
					fecha_registro : fecha_registro,
					fecha_entrega : fecha_entrega,
					prioridad : prioridad,
					sucursal : sucursal,
					option : option,
					obs : obs,
					iduserlog : iduserlog,
					id_op : id_op,
					ttt : ttt,
					orden : orden,
					sol : sol,
					lote : arraylotes

				},
				success : function(r) {// retonrna id de cotizacion
					//console.log(r);
					if (r > 0 || r == 'p') {
						table = $('#tablaprods').DataTable();
						table.clear().draw();
						$('#nreq').css('display', 'none');
						resetearReq();
						if (r == 'p') {
							$('#modal-confexp').modal('show');
							listareq();
						} else {
							$('#modal-conf3').modal('show');
							listareq();
						}
					} else {
						alert('Error de conexion');
						enabled_btn('#btn_savequit', 'Generar Orden de produccion');
					}
				}
			});
		}

	});
	
	//explosion material usarrrrr

	$("#btn_savequit_usar").click(function() {

		disabled_btn('#btn_savequit_usar', 'Procesando...');
		id_op = $('#txt_nreq').text();
		iduserlog = $('#iduserlog').val();
		//si
		/*insumos variables*/
		var insumos = {
			'datos' : []
		};
		$(".variables").each(function(index) {
			if ($(this).val()) {
				var idProduct = $(this).attr('data-idProduc');
				var idinsumo = $(this).attr('data-idInsumo');
				var cantidad = parseFloat($(this).attr("data-cantidad"));

				insumos.datos.push({
					"idProduct" : idProduct,
					"idinsumo" : idinsumo,
					"cantidad" : cantidad
				});
			}
		});
		var insumojson = JSON.stringify(insumos);

		if ($(".unidad").length > 0) {
			alert('Faltan unidades en sus insumos');
			enabled_btn('#btn_savequit_usar', 'Utilizar insumos existentes');
		} else {
			/*si es explosion masiva
			 mandaremos los ids de las ordenes de produccion*/
			// var idoparray = new Array;
			 if ($("#explotandoinsumosmasivos").val() == 1) {
			 	id_op = idoparray;
				// $(".multiexplosion").each(function(index) { 
					// if ($("#" + $(this).attr('id')).is(":checked")) {
						// idoparray.push($(this).attr('id'));
					// }
				// });
				// 
// 
			}
			
			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_guardarUsar",
				type : 'POST',
				data : {
					id_op : id_op,
					iduserlog : iduserlog,
					insumo : insumojson,
					insumosvariables : $("#insumosvariables").val(),
					continua : 0
				},
				success : function(r) {
					if (r == "si") {

						if (confirm("Los insumos variables no pueden ser cambiados, si existe una orden en ejecucion\nSe conservaran los insumos originales, DESEA CONTINUAR?")) {

							$.ajax({
								url : "ajax.php?c=OrdenPrd&f=a_guardarUsar",
								type : 'POST',
								data : {
									id_op : id_op,
									iduserlog : iduserlog,
									insumo : insumojson,
									insumosvariables : $("#insumosvariables").val(),
									continua : 1
								},
								success : function(r) {
									if (r > 0) {
										table = $('#tablaprods').DataTable();
										table.clear().draw();
										$('#nreq').css('display', 'none');
										resetearReq();

										$('#modal-confusar').modal('show');
										enabled_btn('#btn_savequit_usar', 'Utilizar insumos existentes');
										listareq();

									} else {
										alert('Error de conexion');
										enabled_btn('#btn_savequit_usar', 'Utilizar insumos existentes');

									}
								}
							});
						} else {
							enabled_btn('#btn_savequit_usar', 'Utilizar insumos existentes');
						}

					} else if (r > 0) {
						table = $('#tablaprods').DataTable();
						table.clear().draw();
						$('#nreq').css('display', 'none');
						resetearReq();

						$('#modal-confusar').modal('show');
						enabled_btn('#btn_savequit_usar', 'Utilizar insumos existentes');
						listareq();

					} else {
						alert('Error de conexion');
						enabled_btn('#btn_savequit_usar', 'Utilizar insumos existentes');

					}
				}
			});
		}

	}); 


	

});
function msg_error(error){
        $('#error_1').html('<div class="col-sm-12" style="padding-top:10px; display:block;">\
                    <div class="alert alert-danger">\
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>\
                        <strong>Atencion!</strong> Tienes que agregar productos para poder continuar.\
                    </div>\
                    </div>');
        $('#error_1').css('display','block');
    }
/*
 insumos variables
 */

function totalinsumosvari(cantidadxunidad, totalinsumosvari, idproducto, idproductopadre, unidad, cantproduct) {
	var totalv = 0;

	$(".insu" + unidad).each(function(index) {
		if ($(this).val() >= 0) {
			$(this).attr("data-cantidad", ($(this).val() / cantproduct));
			totalv += parseFloat($(this).val());
		}
	});

	$(".valcanti" + unidad).each(function(index) {
		if ($(this).text() > 0) {
			totalv += parseFloat($(this).text());
		}
	});
	refreshCants(idproducto, idproductopadre);
	if (cantidadxunidad < totalv || cantidadxunidad > totalv) {
		//$("#insumo"+idinsumo).val("");
		if (!$("." + unidad).length > 0) {
			$("#leyendavariable").append("<label class='" + unidad + " unidad'>La suma de " + unidad + " no coincide con el total de insumos, deben ser " + cantidadxunidad + "</label>");
		}
	} else {

		$('label').remove(":contains('La suma de " + unidad + "')");
	}
	verificaExisVariable();
}

/*funcion que ayudara a verificar la nueva existencia con el cambio variable*/
function verificaExisVariable() {
	var banderautili = 0;
	$(".variables").each(function(index) {
		if ($(this).val() >= 0) {
			var id = $(this).attr("data-idinsumo");
			if (parseFloat($(this).val()) <= parseFloat($("#exis" + id).text())) {
				banderautili -= 1;
			} else {
				banderautili += 1;
			}
		}

	});

	if (banderautili < 0) {
		$("#btn_savequit_usar").css('visibility', 'visible');
	} else {
		$("#btn_savequit_usar").hide();
	}

}

function recal() {
	var subtotal = 0;
	var total = 0;

	$("#filasprods2 tr").each(function(index) {
		eshead = $(this).attr('eshead');
		if (eshead == 1) {

		} else {
			totalfila = $(this).find('#ttt').attr('implimpio');
			totalfila = (totalfila * 1);
			total += totalfila;
		}

	});

	$('#tttr').attr('totlimpio', total);
	tc = $('#tttr').text(total).currency().text();
	$('#tttr').text('$' + tc + ' MXN');
}

function refreshCants(idProducto, idProdPadre) {
	p = $('#cmbProv_' + idProdPadre + '_' + idProducto).val();
	provcosto = p.split('-');
	idProv = provcosto[0];
	costo = provcosto[1];
	$('#tr_' + idProdPadre + '_' + idProducto).find('.numeros').val(costo);

	valCantidad = $('#tr_' + idProdPadre + '_' + idProducto).find('#valCantidad').text();
	if (!valCantidad) {
		valCantidad = $('#insumo' + idProducto).val();
	}

	ttt = valCantidad * costo;

	$('#tr_' + idProdPadre + '_' + idProducto).find('#ttt').attr('implimpio', ttt);

	$('#tr_' + idProdPadre + '_' + idProducto).find('#ttt').text(ttt).currency();
	//krmn
	recal();

}
function removeProdReq(idProducto,cadcar){
        table = $('#tablaprods').DataTable();
        rowquit= $('#tr_'+idProducto+"[ch='"+cadcar+"']");
        table.row(rowquit).remove().draw();

        if ( table.data().length !== 0 ) {
            
        }else{
            $('#c_proveedores').prop('disabled',false);
            $('#c_almacen').prop('disabled',false);
            $('#c_cliente').prop('disabled',false);
            $('#c_moneda').prop('disabled',false);
        }

        //recalcula();

    }
    
    

