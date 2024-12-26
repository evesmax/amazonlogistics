function savePasoAccion9(accion, idop, paso, idap, idp) {
	$.ajax({
		url : "ajax.php?c=Accion9&f=a_guardarPaso9",
		type : 'POST',
		data : {
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap
		},
		success : function(r) {
			if (r > 0) {
				alert('Registro Fin de produccion guardado con exito');
				ciclo(idop);
			} else {
				alert('Error intente de nuevo');
				ciclo(idop);
			}

		}
	});
}