$(document).ready(function(){


 if ($('#nombre').val()=='') {

     idtipop = '';
   

  }else{
    $('#nombre').val($('#periodoSelecc').val());

    idtipop = $('#periodoSelecc').val();
    cargaNominas(idtipop);

  }
});

$(function() {

  $('#nominas').on('change', function(){

    $("#fechainic").val($("#nominas option:selected").attr("fechainicial"));
    $("#fechafina").val($("#nominas option:selected").attr("fechafinal"));
    $("#nomi").val($("#nominas option:selected").attr("nomina"));

  });




  $('#nombre').on('change', function(){ 
   
    $("#period").val($("#nombre option:selected").attr("nombre"));
    var periodo= $("#nombre>option:selected").text();
    $("#periodnombre").val(periodo);
    $("#period").val($("#nombre option:selected").attr("nombre"));
    idtipop = $(this).val(); 
    cargaNominas(idtipop);
  });
  

  
  function sumasueldo() {

    var sum = 0;
    var sumAsis=0;
    var sumPun=0;
    var sumBase=0;
    var sumispt=0;
    var sumsubs=0;
    var sumrete=0;
    var sumentre=0;
    var sumimss=0;
    var sumprimV=0;
    var sumvaca=0;
    var sumDivaca=0;
    var sumneto=0;
    var sumconc=0;


    $(".sumasueldo").each(function() {
      sum+= parseFloat($(this).html().replace(",",""));
      //console.log(sum);
    });
    $(".tdpremioasist").each(function() { 
      sumAsis+= parseFloat($(this).html().replace(",",""));
      //console.log(sumAsis);
    });
    $(".tdpremioaPunt").each(function() { 
      sumPun+= parseFloat($(this).html().replace(",",""));
      //console.log(sumPun);
    });
    $(".tdbase").each(function() { 
      sumBase+= parseFloat($(this).html().replace(",",""));
      //console.log(sumBase);
    });
    $(".tdispt").each(function() { 
      sumispt+= parseFloat($(this).html().replace(",",""));
      //console.log(sumispt);
    });
    $(".tdsubs").each(function() { 
      sumsubs+= parseFloat($(this).html().replace(",",""));
      //console.log(sumsubs);
    });
    $(".tdretenido").each(function() { 
      sumrete+= parseFloat($(this).html().replace(",",""));
      //console.log(sumrete);
    });
    $(".tdimss").each(function() { 
      sumimss+= parseFloat($(this).html().replace(",",""));
      //console.log(sumimss);
    });

    $(".tdentregado").each(function() { 
      sumentre+= parseFloat($(this).html().replace(",",""));
      //console.log(sumentre);
    });

    $(".tdprimVac").each(function() { 
      sumprimV+= parseFloat($(this).html().replace(",",""));
      //console.log(sumprimV);
    });

    $(".tdvaca").each(function() { 
      sumvaca+= parseFloat($(this).html().replace(",",""));
      //console.log(sumvaca);
    });

    $(".tdneto").each(function() { 
    		var n = $(this).html().replace("<b>","");
    		n = n.replace("</b>","");
    		n = n.replace("</b>","");
 
        sumneto+= parseFloat(n.replace(",",""));
     
      //console.log(sumneto);
    });

    $(".tdconcepto").each(function() { 
      sumconc+= parseFloat($(this).html().replace(",",""));
      //console.log(sumconc);
    });

    $("#tdsumasueldo").html(numeral(sum).format('$0,0.00')); 
    $("#tdpremioasist").html(numeral(sumAsis).format('$0,0.00'));
    $("#tdpremioaPunt").html(numeral(sumPun).format('$0,0.00'));
    $("#tdbase").html(numeral(sumBase).format('$0,0.00'));
    $("#tdispt").html(numeral(sumispt).format('$0,0.00'));
    $("#tdsubs").html(numeral(sumsubs).format('$0,0.00'));
    $("#tdretenido").html(numeral(sumrete).format('$0,0.00'));
    $("#tdimss").html(numeral(sumimss).format('$0,0.00'));
    $("#tdentregado").html(numeral(sumentre).format('$0,0.00'));
    $("#tdprimVac").html(numeral(sumprimV).format('$0,0.00'));
    $("#tdvaca").html(numeral(sumvaca).format('$0,0.00'));
    $("#tdneto").html(numeral(sumneto).format('$0,0.00'));
    $("#tdconcepto").html(numeral(sumconc).format('$0,0.00'));
  };


  $('#load').on('click', function(evt) {
    
    var btnguardar = $(this);
    btnguardar.button("loading");
    var status = true;

    if ($('#empleados').val()=='' || $('#nombre').val()=='' || $('#nominas').val()=='') {

          status=false;
          alert('Llene los filtros de búsqueda.');
          btnguardar.button('reset');
    }else{

    $(this).button('loading'); 
    $("#formDetallado").submit();

    }

  });

  sumasueldo();

});
function verTE(idEmpleado,idnomp){
	$("#vistate").show();
	 $("#vistate").dialog(
	 {
			 autoOpen: false,
			 width: 900,
			 height: 310,
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
			 buttons: 
			{
				"Cerrar": function (){
					
					$('#vistate').dialog('close');		
				}
			}
		});

	$('#vistate').dialog({position:['center',200]});
	$('#vistate').dialog('open');	
	$('#vistate').load('ajax.php?c=Reportes&f=vertiempoextra&ide='+idEmpleado+'&idnomp='+idnomp);
       

 }


// INICIA GENERA PDF
function pdf(){
	

  $(".jornada").removeAttr("width");
  $(".diascheck").removeAttr("width");
  $(".salhora").removeAttr("width");
     
  $('.tablepreDetallado').removeAttr('font-size');
  $('.tablepreDetallado').css('font-size', '8px');

	$('.kr').css({'display':'none'});
	$('.mn').css({'display':'block'});

  $('.leyenda').css({'display':'block'});

  $('.jornada').css({'width':'50%'});
  $('.diascheck').css({'width':'25%'});
  $('.salhora').css({'width':'25%'});

  
  var contenido_html = $("#imprimible").html();


  $("#contenido").text(contenido_html);
  $('.leyenda').css({'display':'none'});
  $("#divpanelpdf").modal('show');
	$('.kr').css({'display':'block'});
	$('.mn').css({'display':'none'});

  $('.tablepreDetallado').removeAttr('font-size');
  $('.tablepreDetallado').css('font-size', '12px');


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
// TERMINA GENERA PDF




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

       // option='<option value="*">Todos</option>';
      }else{
        option='';
      }
      if(r.success==1){
       //option='<option value="*"  title="Seleccione">Seleccione</option>';

        $.each(r.data, function( k, v ) {     
          option+='<option value="'+v.idnomp+'" nomina="'+v.numnomina+'" fechainicial="'+v.fechainicio+'" fechafinal="'+v.fechafin+'">('+v.numnomina+') '+v.fechainicio+' '+v.fechafin+'</option>';    
        });
      }else{
        option+='<option value="">No hay nominas</option>';         
      }
          $('#nominas').html(option);
          $("#nominas option[value='']").remove();
          $('#nominas').selectpicker('refresh'); 
          $("#nominas").val($('#hdnIdNomp').val());
          $('#nominas').selectpicker('refresh'); 
      
          $("#fechainic").val($("#nominas option:selected").attr("fechainicial"));
          $("#fechafina").val($("#nominas option:selected").attr("fechafinal"));
          $("#nomi").val($("#nominas option:selected").attr("nomina"));

          $("#periodnombre").val($("#nombre option:selected").attr("nombre"));
         
          $(".nominas li:nth-child(1)").css("background-color","#62bb5d");
    }
  });
}

