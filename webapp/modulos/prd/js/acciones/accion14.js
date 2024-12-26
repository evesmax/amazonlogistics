function cantidadmerma(tope, cantidad, idinput) {
	if (cantidad > tope) {
		alert("No puedes mermar mas de la cantidad a producir");
		$(".mer").val(tope);

	}
}

$('#tipomerma').select2();
$('#insumos_block14 input').numeric();

//tipo = 1 total prd 2 = total merma
//topeprd cantidad total de producto de orden prd

function savePasoAccion14(accion, idop, paso, idap, idp,topeprd) {
	var tipomerma = $("#tipomerma").val();
	var observacion = $("#observacion14").val();
	//return false;
	disabled_btn('#save_block14', 'Procesando...');
	idsProductos = $('#insumos_block14 input').map(function() {
		merma = $(this).val();
		if($("#tiporegmerma").val()==1){
			merma = topeprd - parseFloat($(this).val());
		}
		idinput = $(this).attr('id');
		spli1 = idinput.split('b14_');
		spli2 = spli1[1].split('_');
		idPadre = spli2[0];
		idHijo = spli2[1];

		if ( typeof idPadre !== "undefined") {
			id = idPadre + '>#' + idHijo + '>#' + merma + '>#' + tipomerma + '>#' + observacion;
		}

		return id;
	}).get().join('___');
	
	$.ajax({
		url : "ajax.php?c=Accion14&f=a_guardarPaso14",
		type : 'POST',
		data : {
			idsProductos : idsProductos,
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap
		},
		success : function(r) {
			if (r > 0) {
				enabled_btn('#save_block14', 'Guardar');
				alert('Registro merma guardado con exito');
				ciclo(idop);
			}

		}
	});
}
//tipo = 1 total prd 2 = total merma
function cambiareg(tipo){ 
	$("#tiporegmerma").val(tipo);
	if(tipo==2){
		$("#totalprdtotal").attr("checked",false);
		$(".tmerma").show("slow");
		$(".tprd").hide();
	}else{
		$("#totalmermatotal").attr("checked",false);
		$(".tprd").show("slow");
		$(".tmerma").hide();
	}
	
}
