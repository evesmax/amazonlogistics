function savePasoAccion21(accion,idop,paso,idap,idp){
	disabled_btn('#finalizar', 'Guardando...');
	$.ajax({
		url : "ajax.php?c=Accion21&f=a_guardarPaso21",
		type : 'POST',
		data : {
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap,
			idp : idp
		},
		success : function(r) {
			if (r > 0) {
				alert('Finalizado con exito');
				ciclo(idop);
			} else {
				alert('Error intente de nuevo');
				ciclo(idop);
			}

		}
	});
}
function generarEtiqueta(idop){
	
	window.open("index.php?c=Accion21&f=etiqueta&idop="+idop);
	
}
