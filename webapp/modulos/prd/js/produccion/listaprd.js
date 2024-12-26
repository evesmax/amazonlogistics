var idoparray = new Array;

function disabled_btn(btn,text){
        txt_orig= $(btn).val();
    $(btn).prop('disabled', true);
    $(btn).text(text);
}

function enabled_btn(btn,text){
    $(btn).prop('disabled', false);
    $(btn).html(text);
}

function listareq() {
	$("#contenido").empty();
	$('#listareq').css('display', 'none');
	resetearReq();
	$('#btnlistorden').css('visibility', 'visible');
	$("#btnexplosionmasiva").css('visibility', 'visible');
	$('#btnback').css('visibility', 'hidden');
	if ($("#explosionmat").val() == 2) {
		//para agregarlo solo si no esta
		if (!$("#check").length) {
			$("#example th:first").before("<th id='check'></th>");
		}
	} else {
		//para removerlo si esta
		if ($("#check").length) {
			$("#check").remove();
		}
	}

	$('#modal-conf2').modal('hide');
	$('#nreq').css('display', 'none');
	$('#listareq_load').css('display', 'block');
	var table = $('#example').DataTable();
	table.destroy();
	$('#example').DataTable({
		language : {
			search : "Buscar:",
			lengthMenu : "Mostrar _MENU_ elementos",
			info : "Mostrando del _START_ al _END_ de _TOTAL_ elementos",
			paginate : {
				first : "Primero",
				previous : "Anterior",
				next : "Siguiente",
				last : "Último"
			},
		},
		"columnDefs" : [{
			"width" : "8%",
			"targets" : 0
		}, {
			"width" : "15%",
			"targets" : 1
		}, {
			"width" : "15%",
			"targets" : 2
		}, {
			"width" : "15%",
			"targets" : 3
		}, {
			"width" : "11%",
			"targets" : 4
		}, {
			"width" : "11%",
			"targets" : 5
		}, {
			"width" : "15%",
			"targets" : 6,
			"orderable" : false,
			"sClass" : "center"
		}],
		"aaSorting" : [[0, 'desc']],
		ajax : {
			beforeSend : function() {
			}, //Show spinner
			complete : function() {
				$('#listareq_load').css('display', 'none');
			}, //Hide spinner
			url : "ajax.php?c=ListadoPrd&f=a_listaOrdenesP",
			type : "POST",
			data : function(d) {

			}
		}
	});
	$('#example_wrapper div:nth-child(2) div:nth-child(1)').css('overflow-y', 'auto');
	$('#listareq').css('display', 'block');

}

function resetearReq(){
        
	$('#btn_vercli').css('visibility', 'hidden');
	$('tbody').empty();
	$('#c_solicitante').find('option[value="0"]').prop('selected', true);
	$('#c_solicitante').select2();
	$('#c_tipogasto').find('option[value="0"]').prop('selected', true);
	$('#c_tipogasto').select2();
	$('#c_moneda').find('option[value="0"]').prop('selected', true);
	$('#c_moneda').select2();

	$('#c_almacen').find('option[value="0"]').prop('selected', true);
	$('#c_almacen').select2();
	$('#c_productos').select2();
	$('#moneda_tc').css('display', 'none');
	$('#moneda_tc').val('');
	$('#panel_tabla').css('display', 'none');
	$('#c_proveedores').prop('disabled', false);
	$('#c_almacen').prop('disabled', false);
	$('#c_solicitante').prop('disabled', false);
	$('#c_tipogasto').prop('disabled', false);
	$('#c_moneda').prop('disabled', false);
	$('#comment').prop('disabled', false);
	$('#date_entrega').prop('disabled', false);
	$('#date_hoy').prop('disabled', false);
	$('#comment').val('');
	$('#c_cliente').find('option[value="0"]').prop('selected', true);
	$('#c_cliente').select2();
	$('#c_cliente').prop('disabled', false);
	$('#c_prioridad').prop('disabled', false);
	$('#c_sucursal').prop('disabled', false);
	$('#c_tipogasto').val("6").trigger("change");
	$('#c_moneda').val("1").trigger("change");
	enabled_btn('#btn_savequit', 'Generar Orden de produccion');
	enabled_btn('#btn_addProd', 'Agregar producto');
	$('#panel_tabla').css('display', 'block');
	$('#panel_tabla2').css('display', 'none');
	$('#addprodoexplo').css('display', 'block');
	$('#addprodoexplo2').css('display', 'none'); 

}

function nreq() {
	$.ajax({
		url : "ajax.php?c=OrdenPrd&f=viewOrdenPrd",
		type : 'post',
		success : function(r) {
			$("#contenido").html(r);
			//$("#contenido").load("ajax.php?c=OrdenPrd&f=viewOrdenPrd");
			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_nuevaorden",
				type : 'POST',
				dataType : 'JSON',
				data : {
					ano : 1
				},
				success : function(r) {
					if (r.success == 1) {
						resetearReq();
						if ($("#ordenmasiva").val() == 2) {
							$(".simple").hide();
							$(".multip").show();
						} else {
							$(".simple").show();
							$(".multip").hide();
						}
						if ($("#ord_x_lote").val() == 1 || $("#ordenmasiva").val() == 2) {
							$(".xlote").show();
						} else {
							$(".xlote").hide();
						}
						
						
						$("#regordenp").val(r.regordenp);
						$('#txt_nreq').text(r.op);
						$('#nreq_load').css('display', 'none');
						$('#ph').html('<span opt="1" class="label label-primary" style="cursor:pointer;">Nueva Orden de produccion</span>');
						$('#nreq').css('display', 'block');
					} else {
						alert('No se pueden cargar cotizaciones');
					}
				}
			});
		}
	});
}


function editReq(idReq, mod) {
	$.ajax({
		url : "ajax.php?c=OrdenPrd&f=viewOrdenPrd",
		type : 'post',
		success : function(r) {
			$("#contenido").html(r);

			table = $('#tablaprods').DataTable();
			table.destroy();
			$("#thlote").remove();
			//lotess
			if ($("#ordenmasiva").val() == 2) {

				if (!$("#thlote").length) {
					$("#tablaprods thead th:last").before("<th id='thlote'>Lote</th>");

				}
			}

			$('#btn_savequit_usar').css('visibility', 'hidden');
			$('#div_ciclo').css('display', 'none');
			$('#btnlistorden').css('visibility', 'hidden');
			$('#btnexplosionmasiva').css('visibility', 'hidden');
			$('#btnback').css('visibility', 'visible');

			table = $('#tablaprods').DataTable();
			table.clear().draw();
			$('#listareq').css('display', 'none');
			$('#modal-conf1').modal('hide');
			$('#nreq').css('display', 'none');
			$('#nreq_load').css('display', 'block');

			$('#panel_tabla2').css('display', 'none');
			$('#panel_tabla').css('display', 'block');
			$('#addprodoexplo').css('display', 'block');
			$('#addprodoexplo2').css('display', 'none');
			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_editarordenp",
				type : 'POST',
				dataType : 'JSON',
				data : {
					idReq : idReq,
					m : 1,
					pr : 'req'
				},
				success : function(r) {
					console.log(r);
					if ($("#ordenmasiva").val() == 2) {
						$(".simple").hide();
						$(".multip").show();
					} else {
						$(".simple").show();
						$(".multip").hide();
					}
					if (r.success == 1) {
						resetearReq();
						$('#ph').html('<span opt="2" class="label label-warning" style="cursor:pointer;">Modificar orden de produccion</span>');
						if (mod == 0) {
							$('#ph').html('<span opt="3" class="label label-default" style="cursor:pointer;">Visualizar orden de produccion</span>');
							disabledReq();
						}

						$('#userlog').text(r.requisicion.username);
						$('#iduserlog').val(r.requisicion.idempleado);
						$('#c_proveedores').prop('disabled', true);
						$('#c_almacen').prop('disabled', true);
						$('#txt_nreq').text(r.requisicion.id);
						$('#nreq_load').css('display', 'none');
						$("#c_prioridad").val(r.requisicion.prioridad).trigger("change");
						$("#c_sucursal").val(r.requisicion.idsuc).trigger("change");
						$("#date_hoy").val(r.requisicion.fi);
						$("#date_entrega").val(r.requisicion.fe);
						$("#c_solicitante").val(r.requisicion.idsol).trigger("change");

						var comment = r.requisicion.observaciones.replace(/<br\s?\/?>/g, "\n");

						$("#comment").val(comment);
						btndescPE = '';
						$.each(r.productos, function(k, v) {

							if (mod == 0) {
								eliminProd = '';
								txtdis = 'disabled';
							} else {
								txtdis = '';
								eliminProd = "<button onclick='removeProdReq(" + v.id + ",0);' id='btn_addProd' class='btn btn-sm btn-danger' type='button' style='height:26px;'>Quitar</button>";
								btndescPE = '';

							}
							if ($("#ordenmasiva").val() == 1) {
								Rowdata = "<tr ch='0' id='tr_" + v.id + "'><!--<td>0</td>--><td>" + v.codigo + "</td><td>" + v.nomprod + "</td><td>" + v.clave + "</td><!--<td id='valUnit'>" + v.adds + "</td>--><td><input style='width:60%;' class='numeros' min='" + v.minimos + "' type='text' value='" + v.cantidad + "'/></td><!--<td class='valImporte' implimpio='" + v.costo + "' id='valImporte'>" + v.costo + "</td>--><td>" + eliminProd + " " + btndescPE + "</td></tr>";

							} else {
								table = $('#tablaprods').DataTable();
								//table.destroy();
								Rowdata = "<tr ch='0' id='tr_" + v.id + "'><!--<td>0</td>--><td>" + v.codigo + "</td><td>" + v.nomprod + "</td><td>" + v.clave + "</td><!--<td id='valUnit'>" + v.adds + "</td>--><td><input style='width:60%;' class='numeros' min='" + v.minimos + "' type='text' value='" + v.cantidad + "'/></td><!--<td class='valImporte' implimpio='" + v.costo + "' id='valImporte'>" + v.costo + "</td>--><td><input style='width:60%;' class='lotess' data-id='" + v.id + "' type='text' value=" + r.requisicion.lote + " ></td><td>" + eliminProd + " " + btndescPE + "</td></tr>";

							}
							//lotess

							table.row.add($(Rowdata)).draw();

						});

						$('#btn_savequit').text('Guardar cambios');
						$('#txt_nreq').append('<input id="idrequi" type="hidden" value="' + idReq + '">');

						$('.numeros').numeric();
						$('#panel_tabla').css('display', 'block');
						$('#nreq').css('display', 'block');

						$('.numeros').change(function() {
							if ($(this).attr("min") == 'null') {
								minimo = 0;
							} else {
								minimo = $(this).attr("min");
							}

							if ($(this).val() * 1 < minimo * 1) {
								alert("Cantidad es menor al minimo permitido");
								$(this).val(minimo);
							}
						});

					} else {
						alert('No se pueden cargar cotizaciones');
					}
					/// DESCUENTO GLOBAL
					var total = r.requisicion.total;

					/// DESCUENTO GLOBAL FIN
				}
			});
		}
	});

}

function disabledReq(){
        $('#c_solicitante').prop('disabled',true);
        $('#c_tipogasto').prop('disabled',true);
        $('#c_moneda').prop('disabled',true);
        $('#c_proveedores').prop('disabled',true);
        $('#c_almacen').prop('disabled',true);
        $('#c_productos').html('<option value="0">Seleccione</option>'); 
        //$('#c_productos').remove();
        $('#moneda_tc').prop('disabled',true);
        $('#comment').prop('disabled', true);
        $('#btn_savequit').prop('disabled', true);
        $('#btn_authquit').prop('disabled', true);
        $('#moneda_tc').prop('disabled', true);
        $('#date_entrega').prop('disabled', true);
        $('#checkbox').prop('disabled', true);
        $('#btn_addProd').prop('disabled', true);
        $('#opciones_2').prop('disabled', true);

    }

function eliminarOP(idop) {

	$('#modal-confdelop').modal('show').one('click', '#modal-confdelop-uno', function() {
		$.ajax({
			url : "ajax.php?c=OrdenPrd&f=a_eliminaOP",
			type : 'POST',
			dataType : 'JSON',
			data : {
				idop : idop
			},
			success : function(r) {
				if (r == 1) {
					$('#modal-confdelop').modal('hide');
					listareq();

				} else {
					$('#modal-confdelop').modal('hide');
					alert('No se puede inactivar esta cotizacion');
				}
			}
		});
	}).one('click', '#modal-confdelop-dos', function() {
		$('#modal-confdelop').modal('hide');

	});

}

function explosionMat(idop, orden) {
	$.ajax({
		url : "ajax.php?c=OrdenPrd&f=viewOrdenPrd",
		type : 'post',
		success : function(r) {
			$("#contenido").html(r);
			$("#explotandoinsumosmasivos").val(0);
			//es un explosion individual

			$('#div_ciclo').css('display', 'none');

			if (orden == '0') {
				//requisisi

				$('#btn_savequit').text('Generar Requisicion');
				$('#orden').val('0');
			} else {
				$('#btn_savequit').text('Generar Orden');

				$('#tit').text('Orden de Compra');

				$('#orden').val('1');
			}

			$('#btnlistorden').css('visibility', 'hidden');
			$("#btnexplosionmasiva").css('visibility', 'hidden');
			$('#btnback').css('visibility', 'visible');

			table = $('#tablaprods2').DataTable();
			table.destroy();

			$('#listareq').css('display', 'none');
			$('#modal-conf1').modal('hide');
			$('#nreq').css('display', 'none');
			$('#nreq_load').css('display', 'block');
			$('#panel_tabla').css('display', 'none');
			$('#panel_tabla2').css('display', 'block');
			$('#nreq').css('display', 'block');

			$('#addprodoexplo').css('display', 'none');
			$('#addprodoexplo2').css('display', 'block');
			if (orden == '0') {
				$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales - Requisiciones</span>');
			} else {
				$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales - Ordenes</span>');
			}

			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_explosionMat",
				type : 'POST',
				dataType : 'JSON',
				data : {
					idop : idop
				},
				success : function(r) {

					if (r.success == 1) {

						$('#userlog').text(r.requisicion.username);
						$('#iduserlog').val(r.requisicion.idempleado);
						$('#txt_nreq').text(r.requisicion.id);
						$('#nreq_load').css('display', 'none');
						$("#c_prioridad").val(r.requisicion.prioridad).trigger("change");
						$("#c_sucursal").val(r.requisicion.idsuc).trigger("change");
						$("#date_hoy").val(r.requisicion.fi);
						$("#date_entrega").val(r.requisicion.fe);
						$("#c_solicitante").val(r.requisicion.idsol).trigger("change");
						$("#c_prioridad").prop('disabled', true);
						$("#c_sucursal").prop('disabled', true);
						$("#date_hoy").prop('disabled', true);
						$("#date_entrega").prop('disabled', true);
						$("#comment").prop('disabled', true);
						$("#c_solicitante").prop('disabled', true);
						$('#panelexplosion').css('display', 'block');

						var comment = r.requisicion.observaciones.replace(/<br\s?\/?>/g, "\n");

						$("#comment").val(comment);

						$.each(r.productos, function(k, v) {
							//eliminProd="<button onclick='removeProdReq("+v.id+",0);' id='btn_addProd' class='btn btn-sm btn-danger' type='button' style='height:26px;'>Quitar</button>";

							eliminProd = '';

							Rowdata1 = "<tr ch='0' id='tr_" + v.id + "' eshead='1' style='background-color:#eee;'><td colspan='4'><b>Orden de produccion:</b> " + v.nomprod + "</td><td colspan='5' style='color:red;size:14px' id='leyendavariable'></td></tr>";

							$('#filasprods2').append(Rowdata1);

							if (v.insumos != 0) {
								usar = 0;
								$.each(v.insumos, function(k2, v2) {
									cant_total = v2.cantidad * v.cantidad;

									cant_total = parseFloat(cant_total).toFixed(2);

									if (v2.existencias < cant_total) {
										usar++;
										ext = '<font color="#ff0000" id="exis' + v2.idProducto + '">' + v2.existencias + '</font>';
									} else {
										ext = '<font color="#096" id="exis' + v2.idProducto + '">' + v2.existencias + '</font>';
									}

									/*toco krmn aqui
									 * insumos variables solo si son de la misma unidad
									 */
									var insumosvariable = $("#insumosvariables").val();
									var cantidadtotal = cant_total;
									$("#productovariable").val(0);
									if (insumosvariable == 1) {// verifica si en la configuracion permite insumos variables
										if (v.insumovariable == 1) {//verifica si el producto es variable
											$("#productovariable").val(1);
											cantidadtotal = "<input onkeyup = totalinsumosvari(" + v2.cantidadunidad + "," + r.cantidadinsumos + "," + v2.idProducto + "," + v.id + ",'" + v2.unidad_clave + "'," + v2.cantproduct + ") data-unidad='" + v2.unidad_clave + "' data-cantidad='" + cantidadtotal / v2.cantproduct + "' data-idInsumo='" + v2.idProducto + "' data-idProduc='" + v.id + "' style='width:60%;' class='insu" + v2.unidad_clave + " variables' type='text' id='insumo" + v2.idProducto + "'  value = '" + cant_total + "' />";
										}
									}
									//fin */
									var listaprv = "<td></td><td></td>";
									if ($("#mostrarprv").val() == 1) {
										listaprv = "<td id='valUnit'>" + v2.listprovs + "</td><td><input style='width:60%;' class='numeros' type='text' value='0' disabled /></td>";
									}
									Rowdata = "<tr ch='0' id='tr_" + v.id + "_" + v2.idProducto + "' eshead='0'><td>" + v2.codigo + "</td><td>" + v2.nombre + "</td><td>" + v2.unidad_clave + "</td>" + listaprv + "<td class='valCantidad valcanti" + v2.unidad_clave + "' id='valCantidad'>" + cantidadtotal + "</td><td class='exxxis'>" + ext + "</td><td class='text-right' id='ttt' implimpio='0'>0.00</td><td>" + eliminProd + "</td></tr>";
									$('#filasprods2').append(Rowdata);

								});
							} else {
								Rowdata = "<tr ch='0' id='tr_" + v.id + "' eshead='2'><td colspan='8'>Este producto no tiene insumos registrados</td></tr>";
								$('#filasprods2').append(Rowdata);
							}

							if (usar == 0) {
								$('#btn_savequit_usar').css('visibility', 'visible');
							} else {
								$('#btn_savequit_usar').css('visibility', 'hidden');
							}

						});
					}

				}
			});
		}
	});
}

function autorizar(id){
        $.ajax({
            url:"ajax.php?c=OrdenPrd&f=a_autorizar",
            type: 'POST',
            data:{id:id},
            success: function(r){
               window.location.reload();
                console.log(r);
            }
        });

    }
function abrirNueva(option){
    window.parent.agregatab("../../modulos/appministra/index.php?c=produccion&f=prerequisito","Pre-Requisiciones","",2392);
}



	/*explosion masiva de materiales*/

$(function() {

	$("#btnexplosionmasiva").click(function() {
		var btn = $(this);
		btn.button("loading");

//function explosionmasiva() {
	
			//le digo a la view q estos explosionando una masiva
			$("#explotandoinsumosmasivos").val(1);
			idoparray = new Array();
			$(".multiexplosion").each(function(index) {
				if ($("#" + $(this).attr('id')).is(":checked")) {
					//alert($(this).attr('id'));
					idoparray.push($(this).attr('id'));

				}
			});
			//console.log(idoparray);

			$('#div_ciclo').css('display', 'none');
$.ajax({
		url : "ajax.php?c=OrdenPrd&f=viewOrdenPrd",
		type : 'post',
		success : function(r) {
			$("#contenido").html(r);
			if (orden == '0') {
				$('#btn_savequit').text('Generar Requisicion');
				$('#orden').val('0');
			} else {
				$('#btn_savequit').text('Generar Orden');
				$('#tit').text('Orden de Compra');
				$('#orden').val('1');
			}

			$('#btnlistorden').css('visibility', 'hidden');
			$("#btnexplosionmasiva").css('visibility', 'hidden');
			$('#btnback').css('visibility', 'visible');

			table = $('#tablaprods2').DataTable();
			table.destroy();

			$('#listareq').css('display', 'none');
			$('#modal-conf1').modal('hide');
			$('#nreq').css('display', 'none');
			$('#nreq_load').css('display', 'block');
			$('#panel_tabla').css('display', 'none');
			$('#panel_tabla2').css('display', 'block');
			$('#nreq').css('display', 'block');

			$('#addprodoexplo').css('display', 'none');
			$('#addprodoexplo2').css('display', 'block');

			if (orden == '0') {
				$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales - Requisiciones</span>');
			} else {
				$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales Masiva - Ordenes</span>');
			}

			$.ajax({
				url : "ajax.php?c=OrdenPrd&f=a_explosionMatMasiva",
				type : 'POST',
				dataType : 'JSON',
				data : {
					idop : idoparray
				},
				success : function(r) {
						btn.button("reset");
					if (r.success == 1) {

						$('#nreq_load').css('display', 'none');
						$(".simple").hide();
						$("#c_prioridad").prop('disabled', true);
						$("#c_sucursal").prop('disabled', true);
						$("#date_hoy").prop('disabled', true);
						$("#date_entrega").prop('disabled', true);
						$("#comment").prop('disabled', true);
						$("#c_solicitante").prop('disabled', true);

						$('#panelexplosion').css('display', 'block');
						//

						$.each(r.productos, function(k, v) {

							eliminProd = '';

							if (v.insumos != 0) {
								usar = 0;
								$.each(v.insumos, function(k2, v2) {
									cant_total = v2.canti;

									cant_total = parseFloat(cant_total).toFixed(2);

									if (v2.existencias < cant_total) {
										usar++;
										ext = '<font color="#ff0000">' + v2.existencias + '</font>';
									} else {
										ext = '<font color="#096">' + v2.existencias + '</font>';
									}

									var cantidadtotal = cant_total;

									var listaprv = "<td></td><td></td>";
									if ($("#mostrarprv").val() == 1) {
										listaprv = "<td id='valUnit'>" + v2.listprovs + "</td><td><input style='width:60%;' class='numeros' type='text' value='0' disabled /></td>";
									}

									Rowdata = "<tr ch='0' id='tr_" + v.id + "_" + v2.idProducto + "' eshead='0'><td>" + v2.codigo + "</td><td>" + v2.nombre + "</td><td>" + v2.unidad_clave + "</td>" + listaprv + "<td class='valCantidad valcanti" + v2.unidad_clave + "' id='valCantidad'>" + cantidadtotal + "</td><td class='exxxis'>" + ext + "</td><td class='text-right' id='ttt' implimpio='0'>0.00</td><td>" + eliminProd + "</td></tr>";
									$('#filasprods2').append(Rowdata);

								});
							} else {
								Rowdata = "<tr ch='0' id='tr_" + v.id + "' eshead='2'><td colspan='8'>Este producto no tiene insumos registrados</td></tr>";
								$('#filasprods2').append(Rowdata);
							}

							if (usar == 0) {
								$('#btn_savequit_usar').css('visibility', 'visible');
							} else {
								$('#btn_savequit_usar').css('visibility', 'hidden');
							}

						});
					}

				}
			});
		}
	});

});
});

