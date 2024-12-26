function solicitarpeso(){
	disabled_btn('#peso', 'Pesando...');
	
	var urlbascula='';
    $.ajax({
            url:"ajax.php?c=Accion3&f=url_bascula",
            type: 'POST',
            success: function(r){
                urlbascula=r;   
         
    $.ajax({
        async:false, 
        url: urlbascula+"/solicitar_peso.php",
        type: 'post',
        dataType: 'json',

    }).done(function(resPeso) {
        console.log("success");

        $('#numpeso').val(resPeso.peso);
		enabled_btn('#peso', 'Pesar');
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert("1.- Verifica que la aplicación de la bascula este activa\n2.- Revisa que tienes permisos para acceder en tu navegador");
        window.open(urlbascula+"/solicitar_peso.php");
		enabled_btn('#peso', 'Pesar');
    })
    .always(function() {
        console.log("complete");
    });
       }
        });
}

function guardarpeso(paquete,idop,cantxemp){
	if(!$("#numpeso").val()){
		alert("Debe pesar el empaque.");
	}else{
		disabled_btn('#guardarpeso', 'Guardando...');
		$.post("ajax.php?c=Accion20&f=guardarPesoEmp",{
			idop:idop,
			peso:$("#numpeso").val(),
			cantxempa:cantxemp,
			paquete:paquete
		},function(resp){
			if(resp == 1){
				alert("Peso registrado");
				ciclo(idop);
			}else{
				alert("Error al guardar peso, intente de nuevo");
				enabled_btn('#guardarpeso', 'Guardar Peso');
			}
		});
	}
}
function savePasoAccion20(accion,idop,paso,idap,idp,sobrante){
	var almacen  = 0;
	if(sobrante>0){
		if( !$("#sobrante").val() ){
			alert("Debe seleccionar un almacen");
			var almacen = $("#sobrante").val();
			return false;
		}
		var almacen = $("#sobrante").val();
	}
	disabled_btn('#finalizar', 'Guardando...');
	$.ajax({
		url : "ajax.php?c=Accion20&f=a_guardarPaso20",
		type : 'POST',
		data : {
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap,
			idp : idp,
			sobrante:sobrante,
			almacen:almacen
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
