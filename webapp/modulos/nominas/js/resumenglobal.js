
 $(function() {
 	
$('#load').on('click', function() {
	$("#tablate tbody").empty();
	$("#conceptossum tbody").empty();
	var btnguardar = $(this);
	btnguardar.button("loading");
 	$.post("ajax.php?c=reportes&f=contenidoResumeGlobal",{
 		ano:$("#ano").val(),
 		periodo :$("#tipoperiodo").val(),
 		mes:$("#mes").val()
 	},function(resp){
 		
 		var sumacont = resp.split("-_-");
 		
 		$("#sumacon").html(sumacont[3]);
 		$("#contenidop").html(sumacont[4]);
 		if ( $.fn.dataTable.isDataTable( '#conceptossum' ) ) {//comprobamos si ya fue definida
		   $('#conceptossum').DataTable();//si ya solo refrescamos
		}
		else {
		    $('#conceptossum').DataTable( {//sino esta definida, definimos
				"language": {
					"url": "js/Spanish.json"
				}
			});
		}
		$("#mesesp").text(sumacont[0]);
		$("#anop").text(sumacont[2]);
		$("#perip").text(sumacont[1]);
		btnguardar.button('reset');
 	});
 });
 
 
  
});

 function pdfi(){
 
  var table = $('.conceptossum').DataTable();
  table.destroy();
  
  
    var table = $('#conceptossum').DataTable();
    table.destroy();
     $('.mostrartabla').show();

    var contenido_html = $("#imprimible").html();


    $("#contenido").text(contenido_html);

 

   

     $("#divpanelpdf").modal('show');
  $('.mostrartabla').hide();
  $('.conceptossum').DataTable( {
      "language": {
        "url": "js/Spanish.json"
      }
    } );



  }
  function generar_pdf(){ 
    $("#divpanelpdf").modal('hide');
    $('.mostrartabla').css({'display':'inline'});
  }
  function cancelar_pdf(){
   // $("#divpanelpdf").modal('hide');
  }

  function pdf_generado(){
    alert("OK");
  }
