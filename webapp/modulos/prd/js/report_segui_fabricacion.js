$(document).ready(function(){

 $('input[name="fecha"]').daterangepicker({
            autoUpdateInput: false,
            "locale": {
            "format": "YYYY-MM-DD",
            "separator": " - ",

            "applyLabel": "Guardar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "Hasta",
            "customRangeLabel": "Personalizar",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Setiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ]
        }, "opens": "center"
});
 $('input[name="fecha"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
  });

  $('input[name="fecha"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
});

	$('.tablaseguimiento').DataTable( {
      "language": {
         "url": "../../libraries/Spanish.json"
       }
    });

  $('#ordenprod1').multiselect({
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Seleccione',
        allSelectedText: 'Todas las ordenes seleccionadas.',
        includeSelectAllOption: true,
        selectAllText: 'Todos',
        selectText: 'd',
        filterPlaceholder: 'Buscar',
        enableFiltering:true,
        dropRight: true,
        maxHeight: 250,
        buttonWidth: '100%'
  });

$('#producto1').multiselect({
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: 'Seleccione',
        allSelectedText: 'Todos los productos seleccionados.',
        includeSelectAllOption: true,
        selectAllText: 'Todos',
        selectText: 'd',
        filterPlaceholder: 'Buscar',
        enableFiltering:true,
        dropRight: true,
        maxHeight: 250,
        buttonWidth: '100%'
  });


});


$(function() {

		$('#producto1').on('change', function()  { $('#producto').val($('#producto1').val());   })
		$('#ordenprod1').on('change', function() { $('#ordenprod').val($('#ordenprod1').val()); })

		$('#load').on('click', function() { 
			
			$('#load').button('loading');

			if ($("#ordenprod").val()=='' && $("#producto").val()=='' && $('input[name="fecha"]').val()=='') {
				
				alert("Seleccione al menos un filtro");
				
				$('#load').button('reset'); 

			}else{
				$.ajax({
					url:"ajax.php?c=config&f=llenarReporteSeguimiento",
					data:{
						ordenprod  :$("#ordenprod").val(),
						producto   :$("#producto").val(),
						fecha      :$('input[name="fecha"]').val()
						
					},
					success: function(r){
						$("#llenatablaseguimiento").html(r); 
						$('.tablaseguimiento').DataTable( {
							"language": {
								"url": "../../libraries/Spanish.json"
							},
							"destroy": true,
							"ordering": false

						}); 
						$('#load').button('reset'); 
					}
				});
			}	
		});
	});

