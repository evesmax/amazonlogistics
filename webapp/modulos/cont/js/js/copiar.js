 $(document).ready(function(){

 $.datepicker.setDefaults($.datepicker.regional['es-MX']);
    $("#fechacopy").datepicker({
	 	maxDate: 365,
	 	dateFormat: 'yy-mm-dd',
        numberOfMonths: 1
        
    });
  });
function copia(){
	if($("#ele").val()==2){
		$.post('ajax.php?c=CaptPolizas&f=movListado',{ 
			idPoliza:$("#idpoliza").val()
			},function (resp){
				$("#movi").html(resp);
			});
			$.post('ajax.php?c=CaptPolizas&f=polizasCopy',{ 
				Ejercicio:$("#IdExercise").val()
				},function (resp2){
					$("#idpolicopy").html(resp2);
			}); 
		$("#movi,#txtc,#selectpoliza").show();
		$("#conceptocopy,#fechacopy,#fechaco").hide();
		
	}else{
		$("#conceptocopy,#fechacopy,#fechaco").show();
		$("#movi,#selectpoliza,#txtc").hide();
	}
}
function copiarPoliza(){ 
	$("#copiarPoliza").dialog({
	autoOpen: false,
			width: 430,
			height: 450,
			modal: true,
			show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
	buttons: {
		"Copiar": function () {
			$("#submit").click();
		}
	}
});

$('#copiarPoliza').dialog({position:['center',100]});
$('#copiarPoliza').dialog('open');
$("#ele").val(1);
copia();
$("#idpolicopy").select2({
     width : "150px"
    });


}

