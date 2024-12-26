$(document).ready(function(){



	if ($('input[name=checkbox]:checked').val()==1 ) {
		
		 	$('.mostrarperiodo').hide();
	     	$('.mostrarfecha').show();
	     	$('.empleado').show(); 

		}else if ($('input[name=checkbox]:checked').val()==2 ) {
			
			$('.mostrarperiodo').show();
	     	$('.mostrarfecha').hide(); 
	     	$('.empleado').show();  
	     	
		}else{

			$('.mostrarperiodo').hide();
	    	$('.mostrarfecha').hide();

	}
	
	$('#tablanominas').DataTable( { 	
		"language": {
			"url": "js/Spanish.json"
		}
	});

	$('.selectpicker').selectpicker({
		size: 4
	});

	$.datepicker.setDefaults($.datepicker.regional['es-MX']);
	$("#fechainicial").datepicker({
		maxDate: 365,
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
		onSelect: function(selected) {
			$("#fechafinal").val(selected);
			$("#fechafinal").datepicker("option","minDate", selected);	
		}
	});
	
	$("#fechafinal").datepicker({ 
		dateFormat: 'yy-mm-dd',
		maxDate:365,
		numberOfMonths: 1,
		onSelect: function(selected) {
			$("#inicial").datepicker("option","maxDate", selected);
			$("#empleado").prop('disabled', true);
		}
	});  


  if ($('#tipoperiodo').val()=='*') {

     	idtipop = '';
     	cargaNominas(idtipop);

  }if ($("#periodoselec").val()==11) {

  		$('.extracheck').show(); 
		$('.nomina').hide();

  }else{

    	idtipop = $('#periodoSelecc').val();
    	cargaNominas(idtipop);
  }

});

function cancelarFactura(uuid,idNominatimbre){

	if(confirm("¿Esta seguro de cancelar esta Factura?")){
		$("#cargando"+idNominatimbre).show();
		$("#cancela"+idNominatimbre).hide();
		$.post("ajax.php?c=Nominalibre&f=cancelaReciboNomina",
		{
			uuid:uuid,
			idNominatimbre:idNominatimbre
		},
		function(resp){
			alert(resp);

			$("#cargando"+idNominatimbre).hide();
			$("#cancela"+idNominatimbre).show();
			$("#load").click();
		});
	}
}


function envioCorreos(){

	x=0;
	cadena='';
	$( "#tablanominas tr" ).each(function(index) {

		if($(this).attr('idemp')){
			xml=$(this).attr('xml');
			cadena+=$(this).attr('idemp')+'#.#'+$(this).attr('xml')+'#.#'+$(this).attr('nomemp')+'#.#'+$(this).attr('fechaini')+'#.#'+$(this).attr('fechafin')+'##.##';
			$.post("../cont/controllers/visorpdf.php",{
				name:xml,
				id:"temporales",
				nominas:1
			},function callback () {
			});
		}
	});
	$("#loading").fadeIn(500);
	$("#divmsg").load("mail.php", {cadena:cadena,m:1});
}

$(function() {

	$("#habilitar").click(function(event) {
		$("#fechainicial").prop('disabled', false);
		$("#fechafinal").prop('disabled', false);
	});

	$('#load').on('click', function() { 

		if ($('input[name=checkbox]:checked').val()==undefined){

			alert("Seleccione una opción.");
			$(this).button('reset'); 

		}else if ($("#mostrarfechas").prop("checked") && (!$("#fechainicial").val() || !$("#fechafinal").val())){

			alert("Seleccione una fecha.");
			$(this).button('reset'); 

		}else{
			$(this).button('loading');
			$("#formfecha").submit();
		}
	});
	
	
	
    $('#tipoperiodo').on('change', function(){
    	
    		idtipop = $(this).val(); 
  			cargaNominas(idtipop);

  			if($("#tipoperiodo>option:selected").val() == 3){ 
    			$('.extracheck').show();
    			$('.nomina').hide();
    			

    
    		}else{
      			$("#origen").val('').selectpicker('refresh');
      			$('.extracheck').hide(); 
      			$('.nomina').show(); 
    		}
    		    $("#periodoselec").val($("#tipoperiodo option:selected").attr("period"));
 	 
	});
});


cargaNominas  = function(idTipop){

	$.ajax({
		url:"ajax.php?c=reportes&f=periodo",
		type: 'POST',
		dataType:'json',
		data:{
			idtipop: idTipop
		},
		success: function(r){

			if(idTipop=='*'){

				option='<option value="*">Todos</option>';
			}else{
				option='';
			}

			if(r.success==1 ){
				option='<option value="0">Todos</option>';

				$.each(r.data, function( k, v ) {  

					option+='<option value="'+v.idnomp+'">('+v.numnomina+') '+v.fechainicio+' '+v.fechafin+'</option>'; 
				});

			}else{

				option+='<option value="">No hay nominas</option>';         
			}

			$('#nominas').html(option);
            $("#nominas option[value='']").remove();
            $('#nominas').selectpicker('refresh'); 
            $("#nominas").val($('#hdnIdNomp').val());
            $('#nominas').selectpicker('refresh'); 
            $(".nominas li:nth-child(2)").css("background-color","#62bb5d");
		}

	});
}


function mailNominas(xml,correo,fechaini,fechafin){

	if (correo=="") {
		correo="@netwarmonitor.com";
	}else{
		correo=correo;
	}
	var msg = "Registre el correo electrónico a quién desea enviarle el XML:";
	var a = prompt(msg,correo);
	if(a!=null){
		$.post("../cont/controllers/visorpdf.php",{
			name:xml,
			id:"temporales",
			nominas:1
		},function (resp){
			$("#loading").fadeIn(500);
			$("#divmsg").load("mail.php?a="+a, {xml:xml,fechaini:fechaini,fechafin:fechafin});
		});
	}
}


function reutilizaFactura(idNominatimbre){
	window.parent.preguntar=false;
	window.parent.quitartab("tb2356",2356,"  Nomina Manual ");
	window.parent.agregatab("../../modulos/nominas/index.php?c=Nominalibre&f=viewNomina&idnomina="+idNominatimbre,"  Nomina Manual ","",2356);
	window.parent.preguntar=true;
}

  function activarChecked(){
  	
  	$("#nominas").html("");
         $('#nominas').selectpicker('refresh');
      $("#tipoperiodo").val(0);
      	$("#tipoperiodo").selectpicker("refresh"); 
    if ($("#mostrarfechas").prop("checked") && $('#nominacompleta').val()==1){
        //alert("aa");  
        $('#mostrarfechas').prop('checked', true);
        $('#mostrarperiodos').prop('checked', true);
        $('.mostrarfecha').hide(); 
        $('.mostrarperiodo').show(); 
        $('.empleado').show(); 
        
        
      

      }
      else{
       $('.empleado').show(); 
        //alert("bb");
        $('#mostrarperiodos').prop('checked', false);
        $('.mostrarperiodo').hide();
        $('.mostrarfecha').show();  
      }
    }



    function activarCheckeddos(){
    	

    	$("#nominas").html("");
     	$('#nominas').selectpicker('refresh');
  		$("#tipoperiodo").val(0);
  		$("#tipoperiodo").selectpicker("refresh"); 
      	
      if ($("#mostrarperiodos").prop("checked") && $('#nominacompleta').val()==1){
       
        $('#mostrarfechas').prop('checked', true);
        $('#mostrarperiodos').prop('checked', true);
        $('.mostrarfecha').show(); 
        $('.mostrarperiodo').hide(); 
        $('.empleado').show(); 
      
      }
      else{

       	 $('.empleado').show(); 
         $('#mostrarfechas').prop('checked', false);     
         $('.mostrarperiodo').show(); 
         $('.mostrarfecha').hide();  
         $('#fechainicial').val('').datepicker("refresh");
         $('#fechafinal').val('').datepicker("refresh");
       }
     }

