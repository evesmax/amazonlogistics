$(document).ready(function(){

	$('#idEmpleado').multiselect({
		nonSelectedText: 'Seleccione',
		dropRight: true,
		maxHeight: 250,
		buttonWidth: '70%'
	});


	$('#anioselec').multiselect({
		enableCaseInsensitiveFiltering: true,
		nonSelectedText: 'Seleccione',
		allSelectedText: 'Todos los años seleccionados.',
		includeSelectAllOption: true,
		selectAllText: 'Todos',
		selectText: 'd',
		filterPlaceholder: 'Buscar',
		enableFiltering:true,
		dropRight: true,
		maxHeight: 250,
		buttonWidth: '70%'
	});
});

$(function() {

	$("#idEmpleado").on("change",function(){
		var empleadosSelec=$(this).val();
		if (empleadosSelec!='*') {
			$("#emple").val(empleadosSelec);
		}
	});


	$("#anioselec").on("change",function(){
		var aniossel=$(this).val();
		if (aniossel!='*') {
			$("#anios").val(aniossel);
		}
	});

	$('#load').on('click', function() { 

		var btnguardar = $(this);
		btnguardar.button("loading");
		var status = true;


		if ($('#emple').val()=='') {

			alert("Seleccione empleados.");	 
			btnguardar.button('reset');
			status = false;

		}else{

			$.ajax({
				url:"ajax.php?c=Reportes&f=llenarReporteVacaciones",

				data:{
					idtipop   :$("#idtipop").val(),
					emple     :$("#emple").val(),
					anioselec :$("#anios").val()
				},
				success: function(r){
					$("#load").button("reset");
					$("#tablavacaciones").html(r); 
					btnguardar.button('reset');
				}

			});
			
			
		}
		
	});


	$('#idtipop').on('change', function(){
		$('#emple').val('');
		valip = $(this).val(); 

		$.ajax({
			url:"ajax.php?c=reportes&f=cargarEmpleados",
			type: 'POST',
			dataType:'json',
			data:{
				idtipop: $('#idtipop').val()
			},
			success: function(r){
//alert(r);
if(idtipop=='*'){

//option='<option value="*">Todos</option>';
}else{
	option='';
}
if(r.success==1 ){
//option='<option value="*">Todos</option>';

$.each(r.data, function( k, v ) {  

	option+='<option value="'+v.idEmpleado+'">'+v.apellidoPaterno+' '+v.apellidoMaterno+' '+v.nombreEmpleado+'</option>';

});
}else{
	option+='<option value="">No hay Empleados</option>';         
}
$('#idEmpleado').html(option);
$('#idEmpleado').multiselect('destroy');
$("#idEmpleado").prop('disabled',true);
$("#idEmpleado").attr('disabled',false);

$('#idEmpleado').multiselect({
	enableCaseInsensitiveFiltering: true,
	nonSelectedText: 'Seleccione',
	allSelectedText: 'Todos los empleados seleccionados.',
	includeSelectAllOption: true,
	selectAllText: 'Todos',
	selectText: 'd',
	filterPlaceholder: 'Buscar',
	enableFiltering:true,
	dropRight: true,
	maxHeight: 250,
	buttonWidth: '70%'
});
}
});
	});
});


// I N I C I A   G E N E R A   P D F
function pdf(){

	$('.tablavacaciones').removeAttr('font-size');
	$('.tablavacaciones').css('font-size', '10px');

	var contenido_html = $("#imprimible").html();

	$("#contenido").text(contenido_html);
	$("#divpanelpdf").modal('show');
	$('.tablavacaciones').css('font-size', '12px');
}

function generar_pdf(){
	$("#divpanelpdf").modal('hide');
}

function cancelar_pdf(){
	$("#divpanelpdf").modal('hide');
}

function pdf_generado(){
	alert("OK");
}

// T E R M I N A   G E N E R A   P D F

