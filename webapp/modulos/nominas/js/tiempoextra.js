/**
 * @author KRMN
 */
function irConfiguracion(){
	window.parent.agregatab('../../modulos/nominas/index.php?c=Catalogos&f=configuracion','Configuracion','',2257);
	window.parent.preguntar=true;
 }
 
function tiempoxtiempo(idtxt){
	//alert($("#txt").val());
	if($("#"+idtxt).is(":checked")){
	// alert("si"+idtxt);
	 $("#divd"+idtxt).hide("slow");
	 $("#inputd"+idtxt).show();
	 
	 $("#divt"+idtxt).hide("slow");
	 $("#inputt"+idtxt).show();
	 
	 $("#importete"+idtxt).show();
	 $("#importe"+idtxt).hide("slow");
	 
	  $("#importetet"+idtxt).show();
	 $("#importet"+idtxt).hide("slow");
	 
	 $("#inputdiado"+idtxt).show();
	 $("#diadoble"+idtxt).hide("slow");
	 
	 $("#inputdiatri"+idtxt).show();
	 $("#diatriple"+idtxt).hide("slow");
	 
	 
	}else{
		$("#inputd"+idtxt).hide("slow");
		$("#divd"+idtxt).show();
		
		$("#inputt"+idtxt).hide("slow");
		$("#divt"+idtxt).show();
		
		$("#importete"+idtxt).hide("slow");
		$("#importe"+idtxt).show();
		
		$("#importetet"+idtxt).hide("slow");
		$("#importet"+idtxt).show();
		
		$("#diadoble"+idtxt).show();
	 	$("#inputdiado"+idtxt).hide("slow");
	 	
	 	 $("#diatriple"+idtxt).show();
		 $("#inputdiatri"+idtxt).hide("slow");
	}
}
var formatter = new Intl.NumberFormat('en-US', {
          style: 'currency',
         currency: 'usd',
         minimumFractionDigits: 2,
          });
/*tipote = tipo de horas
 num = numero en el input
 id = id de empleado usado para identificar el input
 */
function calculoimporte(num, id,tipote,salariohora){
	var triple = doble = 0;
	if(tipote == "triple"){
		 triple = ( (  num  ) * salariohora) * 3; 
		 $("#importetet"+id).val(formatter.format( triple) );
	}
	if(tipote == "doble"){
		//si la periodicidad es semanal solo podra colocar 9 en dobles no mas 
		//ya que marca la ley q las primeras nueve son dobles en una semana
		if(num > 9 && $("#periodicidad").val() == 2){
			alert("Las primeras 9 hrs deben ser dobles");
			$("#inputd"+id).val(9);
		}else{
		 doble = ( (  num  ) * salariohora) * 2;
		  $("#importete"+id).val( formatter.format(doble) );
		}
	}
	
}

function solonumeriviris(e, field){
	 key = e.keyCode ? e.keyCode : e.which; 
        if (key == 8 || key ==9 || key ==37 || key ==39 || key ==127)  return true; 

        if ((key > 47 && key < 58)  || key == 46) {
          if (field.value == "" && key != 46) return true; 
          var  regexp = /^[0-9]+((\.)|(\.[0-9]{1,2}))?$/; 
          return (regexp.test(field.value + String.fromCharCode(key)));

        }

        return false;
}
$(function(){
	$('#finaliza').on('click', function() { 
    		var button = $(this);
    		button.button('loading');
    		$.post("ajax.php?c=Prenomina&f=detalleTiempoExtra",{
			idnomp:$("#idnomp").val(),
			opc:1
		},function callback(){
			
		button.button('reset');
		var numreg = $(".autoriza").length;
	    		$(".autoriza").each(function (index) {
	    			var id = $(this).attr('data-value');
	    			var nombreempleado = $(this).attr('data-name');
	    			var doble = triple = importedoble = importetriple  = 0;
	    			var auto = 1;
	    			/*verifica que tiempo extra de empleado  esta autorizado*/
	    			numreg = numreg -1;
	    			if($("#okte"+id).is(":checked")){
	    				button.button('loading');
					/*si esta autorizado viene para ver si tiene tiempo x tiempo
					 
					 * Si tiene autorizado debera tomar los valores de los inputs*/
					if( $("#"+id).is(":checked") ){
						/*si los dias y las horas dobles o estras estan solo extran debe estar por lo menos uno u otro*/
						if( (!$("#inputd"+id).val() && !$("#inputt"+id).val()) || (!$("#inputdiado"+id).val() && !$("#inputdiatri"+id).val() ) ){
							alert("No introdujo todos los campos en el empleado: "+nombreempleado);
							button.button('reset');
							return false;
						}
						else if( ($("#inputd"+id).val()>0) && (!$("#inputdiado"+id).val() || $("#inputdiado"+id).val()==0) ){
							alert("No introdujo los dias de pago doble  en el empleado: "+nombreempleado);
							button.button('reset');
							return false;
						}
						else if( ($("#inputt"+id).val()>0) && (!$("#inputdiatri"+id).val() || $("#inputdiatri"+id).val()==0) ){
							alert("No introdujo los dias de pago triple  en el empleado: "+nombreempleado);
							button.button('reset');
							return false;
						}
						
						else if( ($("#inputdiado"+id).val()>0) && ( !$("#inputd"+id).val() || $("#inputd"+id).val()==0 ) ){
							alert("No introdujo las horas de pago doble  en el empleado: "+nombreempleado);
							button.button('reset');
							return false;
						}
						else if( ($("#inputdiatri"+id).val()>0) && ( !$("#inputt"+id).val() || $("#inputt"+id).val()==0) ){
							alert("No introdujo las horas de pago triple  en el empleado: "+nombreempleado);
							button.button('reset');
							return false;
						}
						
						 doble = $("#inputd"+id).val();
						 triple = $("#inputt"+id).val();
						 
						 diadoble = $("#inputdiado"+id).val();
						 diatriple = $("#inputdiatri"+id).val();
						
						 importedoble = $("#importete"+id).val();
						 importetriple = $("#importetet"+id).val();
						 auto = 0;
					/*De no tener tiempo por tiempo debera verificar si el usuario
					 marco el pago de TE doble y TE triple*/
					}else{
						var diatriple = diadoble = importedoble = importetriple = 0;
						if($("#doble"+id).is(":checked") ){
							doble = $("#originald"+id).text();
							importedoble = $("#originalpagod"+id).text();
							diadoble = $("#diado"+id).text();
						
						}
						if($("#triple"+id).is(":checked") ){
							triple = $("#originalt"+id).text();
							importetriple = $("#originalpagot"+id).text();
							diatriple = $("#diatri"+id).text();
						}
						
						
						if( (triple==0 && doble==0)  ){
							alert("No tiene las horas a pagar marcadas en el empleado: "+nombreempleado);
						}
						
					}
					
					$.post("ajax.php?c=Prenomina&f=detalleTiempoExtra",{
						doble:doble,
						triple:triple,
						diadoble:diadoble,
						diatriple:diatriple,
						importedoble:importedoble,
						importetriple:importetriple,
						idnomp:$("#idnomp").val(),
						idEmpleado:id,
						opc:0,
						auto:auto
					},function(){
						button.button('reset');
					});
					// console.log("doble",doble);
					// console.log("triple",triple);
					// console.log("importe doble",importedoble);
					// console.log("importe triple",importetriple);
					//$(this).button('reset');
				}
			});
			//alert(numreg);
		if(numreg==0){
			alert("Proceso terminado!");
		}
		
		});
    	}); 
});

function cambiaperiodo(periodo){
 	$.post("ajax.php?c=Prenomina&f=cambiaPeriodo",{
		idtipop: periodo
		},function(resp){
			if( resp == 1){
				window.location.reload();
			}
		});
 }

