function savePasoAccion17(accion, idop, paso, idap, idp) {
	lacant=$('#lacant').val();
	$.ajax({
		url : "ajax.php?c=Accion17&f=a_guardarPaso17",
		type : 'POST',
		data : {
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap,
			idp : idp,
			lacant : lacant
		},
		success : function(r) {
			if (r > 0) {
				alert('Registro en inventario con exito');
				ciclo(idop);
			} else {
				alert('Error intente de nuevo');
				ciclo(idop);
			}

		}
	});
}