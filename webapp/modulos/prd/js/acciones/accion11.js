$('#insumos_block11 input').numeric();
function reabasto(opc) {
	if (opc == 1) {
		$(".reabasto").show();
		$(".reabasto").focus();
		enabled_btn('#reabasto', 'Dejar de pedir');
		$("#reabasto").attr("onclick", "reabasto(2)");
	} else {
		$(".reabasto").hide();
		enabled_btn('#reabasto', 'Solicitar insumos');
		$("#reabasto").attr("onclick", "reabasto(1)");
	}

}

function agre(op) {//beto
	if ($("#mmm_" + op).val() > 0) {
		lacant = $('#lacant').val();
		$('#agp').remove();
		$('#lose').after('<div id="agp" class="col-sm-12" style="margin-top: 10px; font-size:12px;"><b>Agrega cantidad de produccion</b> <br><input id="olo" onkeyup="vfc();" type="text" class="form-control" value=""></div>');
	} else {
		$("#agp").hide();
		$('#olo').val(0);

		$(".insumosf").val(0);
	}
}

function vfc() {
	lacant = $('#lacant').val();

	var previoppf = 0;
	$(".ppfrepor").each(function() {
		if ($(this).val() > 0) {
			previoppf += parseFloat($(this).val());
		}
	});
	//restamos al total de porductos los que ya a mandado
	previoppf = parseFloat(lacant - previoppf);
	olo = $('#olo').val();
	if (olo < 0) {
		alert("La cantidad no puede ser 0");
		$(".insumosf").val(0);
		return false;
	}
	if (olo > previoppf) {
		alert('La cantidad no puede ser mayor a la produccion');
		olo = $('#olo').val(previoppf);
		$(".insumosf").each(function() {
			$("#" + $(this).attr("id")).val($(this).attr("data-value") * previoppf);
		});
		return false;
	} else {
		$(".insumosf").each(function() {
			$("#" + $(this).attr("id")).val($(this).attr("data-value") * olo);
		});

	}

}

function savePasoAccion17(accion, idop, paso, idap, idp) {
	idemp = $('#mmm_' + idap).val();
	faltan = 0;
	deten = 0;
	ceros = 0;

	/*envio material
	 primero recorremos los listados de cantidad de los insumos
	 y comprobamos si ya fueron utlizados todos los recursos*/
	var arraycantinsumos = 0;
	var arraycantinutilizados = 0;
	$('.b11').each(function() {
		if ($(this).val() > 0) {
			arraycantinsumos += parseFloat($(this).val());
		}
	});
	$('.b11u').each(function() {
		if ($(this).val() > 0) {
			arraycantinutilizados += parseFloat($(this).val());
		}
	});

	idsProductos = $('div .lalala').map(function() {
		der = $(this).find('input').attr('id');
		derexp = der.split('_');
		idp = derexp[2];

		s1 = $('#b11_' + idop + '_' + idp).val() * 1;
		s2 = $('#b11u_' + idop + '_' + idp).val() * 1;

		rest = s1 - s2;

		idpv = $(this).find('input').val();

		if (rest - idpv < 0) {
			deten++;
		}

		ceros += idpv;

		return idemp + '###' + idp + '#' + idpv;
	}).get().join('___');
	var opc = 1;
	/*si es diferente quiere decir que debe seguir haciendo el envio*/
	if (arraycantinsumos != arraycantinutilizados) {
		opc = 0;
		if (ceros == 0) {
			alert('Todas las cantidades a usar no pueden ser 0');
			return false;
		}
		if (deten > 0) {
			alert('Las cantidades a usar sobrepasan la cantidad de insumos maxima');
			return false;
		}
		if (faltan > 0) {
			alert('No hay existencias');
			return false;
		}
		if (!idemp || idemp == 0) {
			alert("Debe seleccionar un  operador");
			return false;
		}
	} else {
		if ($('.finaliza').is(":visible")) {
			alert("Debe finalizar los envios a operador");
			return false;
		}
	}
	$.ajax({
		url : "ajax.php?c=Accion11&f=a_guardarPaso11",
		type : 'POST',
		data : {
			idsProductos : idsProductos,
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap,
			idemp : idemp,
			opc : opc,
			ppf : $('#olo').val()
		},
		success : function(r) {
			if (r > 0) {
				alert('Registro guardado con exito');
				ciclo(idop);
			}

		}
	});
}

function finalizar(id, idop) {
	$.ajax({
		url : "ajax.php?c=Accion11&f=a_finalizar",
		type : 'POST',
		data : {
			id : id
		},
		success : function(r) {
			alert('Proceso finalizado');
			$('#ff_' + id).prop('disabled', true);
			ciclo(idop);

		}
	});
}
function reabastoentrada(valor,tope,id){
	if(valor>tope){
		$("#rea_"+id).val(0);
		alert("No puede pedir mas de la cantidad de envio del insumo");
		return false;
	}
}
function reabasto(opc,id) {
	if (opc == 1) {
		$(".reabasto"+id).show();
		$(".reabasto"+id).focus();
		enabled_btn('#reabasto'+id, 'Dejar de pedir');
		$("#reabasto"+id).attr("onclick", "reabasto(2,"+id+")");
	} else {
		$(".reabasto"+id).val("");
		$("#reabastoobs"+id).val("");
		$(".reabasto"+id).hide();
		enabled_btn('#reabasto'+id, 'Solicitar insumos');
		$("#reabasto"+id).attr("onclick", "reabasto(1,"+id+")");
	}

}
function pedirreabasto(idop,idap,idmatp,operador){
	disabled_btn('#pedirreabasto'+idmatp, 'Procesando...');
	var contador =0;
	productos = $('.montoreabasto'+idmatp).map(function() {
		if($(this).val()>0){
			der = $(this).attr('id');
			derexp = der.split('_');
			idp = derexp[1];
			cant = $(this).val();
			contador += parseFloat(cant);
			return idp + '-' + cant;
		}
	}).get().join('_');
	if(contador == 0){
		alert("No esta pidiendo ningun insumo");
		$(".reabasto"+idmatp).focus();
		enabled_btn('#pedirreabasto'+idmatp, 'Pedir');
		return false;
	}
	
	
	$.ajax({
		url : "ajax.php?c=Accion18&f=reabasto",
		type : 'POST',
		data : {
			productos : productos,
			solicitante:operador,
			obs:$("#reabastoobs"+idmatp).val(),
			idop:idop,
			idap:idap,
			idmatp:idmatp
		},
		success : function(r) {
			if(r>0){
				alert('Pedido realizado!\nPuede ver su Solicitud en el menu de autorizacion de Reabasto');
				enabled_btn('#pedirreabasto'+idmatp, 'Pedir');
				$("#reabasto"+idmatp).click();
			}else{
				alert("Error en el proceso, intente de nuevo");
				enabled_btn('#pedirreabasto'+idmatp, 'Pedir');
			}

		}
	});
}
