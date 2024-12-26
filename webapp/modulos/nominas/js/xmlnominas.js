
$(document).ready(function(){
	$.datepicker.setDefaults($.datepicker.regional['es-MX']);
	
	$("#fechapago").datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			onSelect: function(selected) {
				var	fecha = new Date($("#fechainicio").val());
				fecha.setDate(fecha.getDate() + 1);
				$("#fechapago").datepicker("option", "minDate",fecha);
			}
		});
		
 $("#timbra").on('click', function() {
 	
 	if( !$("#idnomina").val() ){
 		alert("Debe seleccionar una nomina!");
 	}
 	else{
 		var sepa2 = $("#idnomina").val().split("/");
		var btnguardar = $(this);
		btnguardar.button("loading");
		if($("#origen").val()!=0){
			var idemple = $("#fini").val().split("/");
			$.post("ajax.php?c=Prenomina&f=xmlNominaExtraordinaria",{
				idnomp:sepa2[0],
				fechapago: $("#fechapago").val(),
				origen:$("#origen").val(),
				idEmpleado:idemple[0]
			},function(resp){
				$("#loading").fadeIn(500);
				$("#divmsg").html(resp);
				btnguardar.button("reset");
				if($("#origen").val() == 2){
					cambiofi(2);//recarla lista de finiquitos
				}
				
			});
		}else{
			
			$.post("ajax.php?c=Prenomina&f=xmlNomina",{
				idnomp:sepa2[0],
				fechapago: $("#fechapago").val()
			},function(resp){
				$("#loading").fadeIn(500);
				$("#divmsg").html(resp);
				btnguardar.button("reset");
				recargaLista();
			});
		}
	}
	});	
		
});
function recargaLista(){
	$.post("ajax.php?c=Prenomina&f=listaNominasTimbrar",{
	},function(resp){
		$("#idnomina").html(resp).selectpicker('refresh');
	});
	
}
function iniciocalendario(valor){
	var sepa = valor.split("/");
	$("#fechainicio").val(sepa[1]);
	$("#fechapago").val( sepa[1]);
		
	var	fecha2 = new Date($("#fechainicio").val());
	fecha2.setDate(fecha2.getDate() + 1);
	$("#fechapago").datepicker("option", "minDate",fecha2);
	
		
}
function cambiaperiodo(periodo){
	$("#divori").hide("slow");
	$("#origen").val(0);
	$("#origen").selectpicker('refresh');
 	$.post("ajax.php?c=Prenomina&f=cambiaPeriodo",{
		idtipop: periodo
		},function(resp){
			if( resp == 1){
				window.location.reload();
			}else{
				$("#origen").html('<option value="1">Aguinaldo</option><option value="2">Finiquito</option><option value="3">PTU</option>');
				$("#origen").selectpicker('refresh');
				$("#divori").show("slow");
				$.post("ajax.php?c=Prenomina&f=nominaextraordinaria",{
					idtipop:periodo
				},function(request){
					$("#idnomina").html(request).selectpicker('refresh');
				});
			}
		});
 }
 function cambiofi(origen){
 	if(origen == 2){
 		$("#divfini").show("slow");
 		var sepa2 = $("#idnomina").val().split("/");
 		$.post("ajax.php?c=Prenomina&f=traerfiniquitos",{
		idnomp: sepa2[0]
		},function(resp){
			if(resp == 0){
				$("#fini").html("<option>No tiene finiquitos</option>");
			}else{
				$("#fini").html(resp);
			}
			$("#fini").selectpicker('refresh');
		});
 	}else{
 		$("#divfini").hide("slow");
 	}
 }
 
 function finibaja(id){
 	var sepa2 = id.split("/");
 	$("#fechapago").val(sepa2[1]);
 	$("#fechapago").selectpicker('refresh');
 }
