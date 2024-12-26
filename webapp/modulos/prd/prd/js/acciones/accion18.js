function topexinsumo(insumototal, insumoutilizado, valor, idinput) {
	var valorutilizado = parseFloat(valor) + parseFloat(insumoutilizado);
	//alert(valorutilizado)
	var totalpendiente = parseFloat(insumototal) - parseFloat(insumoutilizado);
	if (valor > insumototal) {
		alert("No puede enviar mas insumos de la cantidad total de insumo");
		$("#" + idinput).val(totalpendiente);
		return false;
	} else if (valor < insumototal && (valorutilizado > insumototal)) {
		alert("No puede enviar mas insumos de la cantidad total de insumo");
		$("#" + idinput).val(totalpendiente);
		return false;
	} else if (valor == insumototal && (valorutilizado > insumototal)) {
		alert("No puede enviar mas insumos de la cantidad total de insumo");
		$("#" + idinput).val(totalpendiente);
		return false;

	}
}

function savePasoAccion18(accion, idop, paso, idap, idp,maspaso) {

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
	/*si no dio click en contnuar  quiere decir que debe seguir haciendo el envio*/
	if (!maspaso) {
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

	} else {
		if (!confirm("Terminara el envio unicamente con lo que aparece en historial, desea continuar?")) {
			return false;
		}
		if ($('.finaliza').is(":visible")) {
			alert("Debe finalizar los envios a operador");
			return false;
		}
	}

	$.ajax({
		url : "ajax.php?c=Accion18&f=a_guardarPaso18",
		type : 'POST',
		data : {
			idsProductos : idsProductos,
			accion : 18,
			paso : paso,
			idop : idop,
			idap : idap,
			idemp : idemp,
			opc : opc,
			ppf : 0,
		},
		success : function(r) {
			if (r > 0) {
				alert('Registro guardado con exito');
				ciclo(idop);
			}

		}
	});
}
function finalizar18(id, idop) {
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
function reabasto(opc) {
	if (opc == 1) {
		$(".reabasto").show();
		$(".reabasto").focus();
		enabled_btn('#reabasto', 'Dejar de pedir');
		$("#reabasto").attr("onclick", "reabasto(2)");
	} else {
		$(".reabasto,#reabastoobs").val("");
		$("#solicitante").val(0);
		$(".reabasto").hide();
		enabled_btn('#reabasto', 'Solicitar insumos');
		$("#reabasto").attr("onclick", "reabasto(1)");
	}

}
function reabastoentrada(valor,tope,id){
	if(valor>tope){
		$("#b19_"+id).val(0);
		alert("No puede pedir mas de la cantidad del insumo");
		return false;
	}
}
function pedirreabasto(idop,idap){
	disabled_btn('#pedirreabasto', 'Procesando...');
	var contador =0;
	productos = $('.montoreabasto').map(function() {
		if($(this).val()>0){
			der = $(this).attr('id');
			derexp = der.split('_');
			idp = derexp[2];
			idop = derexp[1];
			cant = $(this).val();
			contador += parseFloat(cant);
			return idp + '-' + cant;
		}
	}).get().join('_');
	if(contador == 0){
		alert("No esta pidiendo ningun insumo");
		$(".reabasto").focus();
		enabled_btn('#pedirreabasto', 'Pedir');
		return false;
	}
	if($("#solicitante").val()==0){
		alert("Debe seleccionar un Solicitante/Operador");
		enabled_btn('#pedirreabasto', 'Pedir');
		return false;
	}
	
	$.ajax({
		url : "ajax.php?c=Accion18&f=reabasto",
		type : 'POST',
		data : {
			productos : productos,
			solicitante:$("#solicitante").val(),
			obs:$("#reabastoobs").val(),
			idop:idop,
			idap:idap
		},
		success : function(r) {
			if(r>0){
				alert('Pedido realizado!\nPuede ver su Solicitud en el menu de autorizacion de Reabasto');
				enabled_btn('#pedirreabasto', 'Pedir');
				$("#reabasto").click();
			}else{
				alert("Error en el proceso, intente de nuevo");
				enabled_btn('#pedirreabasto', 'Pedir');
			}

		}
	});
}
