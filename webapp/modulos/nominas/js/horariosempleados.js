$(document).ready(function(){
	
	
	$('#empleados').multiselect({
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
		buttonWidth: '100%',
		onDeselectAll: function() {
		alert('Deseleccionados todos.');
		}
	});

	$('#almacenhrs').on('click', function() {

		var btbhorsempl = $(this);
		btbhorsempl.button('loading'); 

		if(!$('#horaentra').val() || !$('#horacomida').val() || !$('#horasalida').val() || !$('#horasalida').val() || !$('#tolerancia').val() ){
			alert("Debe llenar todos los campos.")	;
			btbhorsempl.button('reset'); 
		}else{

			var arrayEmp = new Array();
			$('#empleados :selected').each(function(i, selected) {
				arrayEmp[i] = $(selected).val();
			});
			$.post("ajax.php?c=Catalogos&f=asignahorariosemple",{
				empleados: arrayEmp,
				horaentra:$('#horaentra').val(),
				horacomida:$('#horacomida').val(),
				horasalida:$('#horasalida').val(),
				tolerancia:$('#tolerancia').val()

			},function (resp){

				if(resp >= 1){
					alert("Registro satisfactorio.");
					   $('.input').val('');
					   //$("#empleados option:selected").prop("selected", false);
  					   //$("#empleados").multiselect('refresh');
					   location.reload();

				}else{
					alert("Ocurrió un error, intente de nuevo.");
				}
				btbhorsempl.button('reset'); 
			}); 
		}

	});
});
