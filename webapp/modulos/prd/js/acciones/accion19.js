var tiemp;
function iniciar(accion, idop, paso, idap, idp) {
	disabled_btn('#ini_block19', 'Iniciando...');

	if ($("#operador").val() == 0) {
		alert("Debe seleccionar un operador para iniciar la actividad.");
		enabled_btn('#ini_block19', 'Iniciar actividad');
		return false;
	}
	$.ajax({
		url : "ajax.php?c=Accion19&f=actividad",
		type : 'POST',
		data : {
			paso : paso,
			idop : idop,
			idap : idap,
			idp : idp,
			opc : 1,
			operador : $("#operador").val()

		},
		success : function(r) {
			if (r != 0) {
				enabled_btn('#ini_block19', 'Iniciar actividad');
				$("#iniciar").hide();
				$("#terminar").show();
				tiempo(r);
			} else {
				alert("Error en el proceso, intente de nuevo");
				ciclo(idop);
			}

		}
	});

}

function finalizar(accion, idop, paso, idap, idp) {
	clearInterval(tiemp);
	disabled_btn('#fin_block19', 'Finalizando...');
	$.ajax({
		url : "ajax.php?c=Accion19&f=actividad",
		type : 'POST',
		data : {
			paso : paso,
			idop : idop,
			idap : idap,
			idp : idp,
			opc : 2,
			accion : accion

		},
		success : function(r) {
			if (r != 0) {
				enabled_btn('#fin_block19', 'Finalizar');
				ciclo(idop);
				$("#iniciar").hide();
			} else {
				alert("Error en el proceso, intente de nuevo");
				ciclo(idop);
			}
		}
	});
}

function tiempo(fecha) {
	tiemp = setInterval(function() {
		$.ajax({
			url : "ajax.php?c=Accion19&f=tiempo",
			type : 'POST',
			data : {
				fecha : fecha
			},
			success : function(r) {
				$("#tiempo").html(r);
			}
		});

	}, 1000);
}
