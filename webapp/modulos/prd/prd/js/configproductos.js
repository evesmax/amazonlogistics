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


$('#marcaTodoAtt').on('change', function() {

	if ($(this).is(':checked')) {
		$(".todos2 input[type=checkbox]").prop('checked', true); 
	} else {
		$(".todos2 input[type=checkbox]").prop('checked', false);
	}
});


});


function guardarconfprod() {

	$('#save').button('loading');

	var typesArray   = [];
	var attribsArray = [];

	$('.div1').each(function(idx, elem){      
		typesArray.push( {"id" : $(elem).find('.identificar').attr("value"), "visible" : $(elem).find(".identificar").prop ("checked") ? 1 : 0,"vendible": $(elem).find(".vendible").prop ("checked") ? 1 : 0 });
	});

	$('.div2').each(function(idx, elem){ 
		attribsArray.push( {"id" : $(elem).find('.identificar2').attr("value"), "visible" :$(elem).find(".identificar2").prop ("checked") ? 1 : 0 });
	});

	$.ajax({
		url:"ajax.php?c=config&f=UpdateActtr",
		type: 'POST',
		data:{
			types: JSON.stringify(typesArray),
			attributes: JSON.stringify(attribsArray) 
		},
		success: function(resp){

			if (resp==11) {
				alert('Cambios guardados con éxito.');
				window.location.reload();

			}else{
				alert("Error al guardar.");
			}
			$('#save').button('reset'); 
		}
	});
}
