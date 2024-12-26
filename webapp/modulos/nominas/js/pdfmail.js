	
	//I N I C I A   G E N E R A   P D F 
 	function pdf(){
 		
 		$('.pdfremove').removeAttr('style');
 		$('.trsize').removeAttr('style');
        $('.pdfremove').css({'fontSize':'8px'});
        $('.clave').css({'text-align':'center'});
     



	    $('.accOculta').hide();
	    $('.tablasobrerecibo').css({'fontSize':'9px'});
	    $('.iEncab').css({'background-color':'rgb(48,73,95)','height':'20px','color':'while'});  
		$('.brmail').css({'display':'none'});
		$('.agregPD').css({'display':'none'});

		 var contenido_html = $("#imprimible").html();

		 $("#contenido").text(contenido_html);
		 $('.agregPD').css({'display':'inline'});
		 $("#divpanelpdf").modal('show');
		 $('.tablasobrerecibo').css({'fontSize':'12.5px'});
		 $(".color").css({'background-color':'while'});
		 $('.editbls').removeClass('editbls') 
		 $('.accOculta').show();
		 $('.editbls').addClass('editbls')	;

		 $('.trsize').css({'fontSize':'11px'});
		 $('.pdfremove').css({'fontSize':'11px'});
		 $('.col70').attr({'width':'70px;'});
		 $('.col180').attr({'width':'180px;'});
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
	

	// C O M I E N Z A   G E N E R A R   M A I L 
	function mail(){
		
		var msg = "Registre el correo electrónico a quién desea enviarle el reporte:";
		var a = prompt(msg,"@netwarmonitor.com");
		if(a!=null){
			$('.pdfremove').removeAttr('style');
			$('.accOculta').hide();
			$('.col180').css({'text-align':'left'});
		 	$('.iEncab').css({'background-color':'rgb(48,73,95)','height':'20px','color':'while'});  
		    $('.agregPD').css({'display':'none'});
		    $('.estinegrit').css({'fontWeight':'bold'});
			var html_contenido_reporte;
			html_contenido_reporte = $("#imprimible").html();
			
			$("#loading").fadeIn(500);
			$("#divmsg").load("../../../webapp/netwarelog/repolog/mail.php?a="+a, {reporte:html_contenido_reporte});
			$('.accOculta').show();
			$('.agregPD').css({'display':'inline'});
			$('.clave').css({'text-align':'center'});

			$('.pdfremove').css({'fontSize':'11px'});
		    $('.col70').attr({'width':'70px;'});
		    $('.col180').attr({'width':'180px;'});
		}
	}	
	// TERMINA GENERAR MAIL
	


