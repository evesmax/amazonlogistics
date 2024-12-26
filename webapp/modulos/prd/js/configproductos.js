// @autor --- Anali M ---



$(function() {


	if ($(".todos1 input:checked").length=='10') {
		$('[name="marcaTodoTipo"]').prop('checked',true);
	}else{
		$('[name="marcaTodoTipo"]').prop('checked',false);
	}


	if ($(".todos2 input:checked").length=='6') {
		$('[name="marcaTodoAtt"]').prop('checked',true);
	}else{
		$('[name="marcaTodoAtt"]').prop('checked',false);

	}

	if ($(".todos3 input:checked").length=='10') {
		$('[name="marcaTodoVendible"]').prop('checked',true);
	}else{
		$('[name="marcaTodoVendible"]').prop('checked',false);

	}

	
// Funciones para marcar todos los check
$('#marcaTodoTipo').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos1 input[type=checkbox]").prop('checked', true); 
	} else {
		$(".todos1 input[type=checkbox]").prop('checked', false);
	}
});

$('#marcaTodoVendible').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos3 input[type=checkbox]").prop('checked', true); 
	} else {
		$(".todos3 input[type=checkbox]").prop('checked', false);
	}
});
$('#marcaTodoPeso').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos4 input[type=checkbox]").prop('checked', true); 
	} else {
		$(".todos4 input[type=checkbox]").prop('checked', false);
	}
});


$('#marcaTodoAtt').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos2 input[type=checkbox]").prop('checked', true); 
	} else {
		$(".todos2 input[type=checkbox]").prop('checked', false);
	}
});

$('#validarpeso').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos4 input[type=checkbox]").prop('disabled', false); 
		$("#marcaTodoPeso").prop('disabled', false); 
		$("#peso").val(1);
	} else {
		$(".todos4 input[type=checkbox]").prop('disabled', true);
		$("#marcaTodoPeso").prop('disabled', true); 
		$("#peso").val(0);
	}
});
$('#clasifi').on('change', function() {

	if ($(this).is(':checked')) {
		
		$("#cla").val(1);
	} else {
		 
		$("#cla").val(0);
	}
});
$('#marcaTodocaja').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos5 input[type=checkbox]").prop('checked', true); 
		
	} else {
		$(".todos5 input[type=checkbox]").prop('checked', false);
		
	}
});

});


function guardarconfprod() {

	$('#save').button('loading');

	var typesArray   = [];
	var attribsArray = [];

	$('.div1').each(function(idx, elem){      
		typesArray.push( {"id" : $(elem).find('.identificar').attr("value"), "visible" : $(elem).find(".identificar").prop ("checked") ? 1 : 0,"vendible": $(elem).find(".vendible").prop ("checked") ? 1 : 0,"validaxpeso": $(elem).find(".validaxpeso").prop ("checked") ? 1 : 0,"caja_master": $(elem).find(".cajamaster").prop ("checked") ? 1 : 0  });

	});

	$('.div2').each(function(idx, elem){ 
		attribsArray.push( {"id" : $(elem).find('.identificar2').attr("value"), "visible" :$(elem).find(".identificar2").prop ("checked") ? 1 : 0 });
	});

	$.ajax({
		url:"ajax.php?c=config&f=UpdateActtr",
		type: 'POST',
		data:{
			types: JSON.stringify(typesArray),
			attributes: JSON.stringify(attribsArray),
			confpeso:$("#peso").val(),
			clasificador:$("#cla").val()
		},
		success: function(resp){

			if (resp==111) {
				alert('Cambios guardados con éxito.');
				window.location.reload();

			}else{
				alert("Error al guardar.");
			}
			$('#save').button('reset'); 
		}
	});
}

