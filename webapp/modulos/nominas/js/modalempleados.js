$(document).ready(function(){


 $('#tableGrid').DataTable( {
         
          "language": {
            "url": "js/Spanish.json"
          }
        } ); 


});

modalbaja = function(idempleado, accion,idtipop){

	var fechaminima=$("#fechainic").val();
	$("#txtfecha").val("");

	if (fechaminima) {
		$('#fecha').datetimepicker({
			format: 'YYYY-MM-DD',
			minDate:fechaminima,
			ignoreReadonly: true,
			useCurrent: false,
			locale: 'es'
		});
	}else{

		$('#fecha').datetimepicker({
			format: 'YYYY-MM-DD',
			ignoreReadonly: true,
			useCurrent: false,
			locale: 'es'
		});
	}


	$("#fecha").on("dp.change", function (e) {

		if ($("#nominas").val()==1) {
			var date = e.date.format("YYYY-MM-DD");  
			$.post("ajax.php?c=Catalogos&f=periodoactual",{
				fecha:date,
				idtipoperi: idtipop
			},
			function(resp){

				if(resp=="false"){ 
					$("#txtfecha").val(""); 
					alert("La fecha no está dentro del periodo actual.");
					$("#txtfecha").val("");
				} 
			});
		}
	}); 


	$('#btnbaja').on('click', function() { 

		if ($("#txtfecha").val() ==""){
			alert("Seleccione una fecha."); 

		}
		else {

			var btnguardar = $(this);
			btnguardar.button("loading");  
			var fecha = $("#txtfecha").val();  
			var status = true;  
			$.post("ajax.php?c=Catalogos&f=accionEmpleado",{
				fecha:fecha ,
				accion:accion,
				idempleado:idempleado
			},
			function(resp){
				alert(resp);
				btnguardar.button('reset');	
				window.location.reload();
			});
			
		}
	}); 

};


function pdf(){
  
 var table = $('#tableGrid').DataTable({"destroy": true});
 table.destroy();


 $(".ocultarcoll").hide();
 $(".listadoemple").show();

 $(".codigo").removeAttr("width");
 $(".nombreemple").removeAttr("width");
 $(".nss").removeAttr("width");
 $(".rfc").removeAttr("width");
 $(".curp").removeAttr("width");
 $(".status").removeAttr("width");


 $('.codigo').css({'height':'25px'});
 $('.codigo').css({'width':'5%'});
 $('.nombreemple').css({'width':'30%'});
 $('.nss').css({'width':'15%'});
 $('.rfc').css({'width':'15%'});
 $('.curp').css({'width':'20%'});
 $('.status').css({'width':'15%'});


var contenido_html = $("#imprimible").html();
var table = $('#tableGrid').DataTable();
 table.destroy();

$("#contenido").text(contenido_html);
$('.tableGrid').DataTable( {
 
    "language": {
      "url": "js/Spanish.json"
    }
  } );

$('.codigo').css({'height':'10px'});

$(".ocultarcoll").show();
$(".listadoemple").hide();

$(".codigo").removeAttr("width");
$(".nombreemple").removeAttr("width");
$(".nss").removeAttr("width");
$(".rfc").removeAttr("width");
$(".curp").removeAttr("width");
$(".status").removeAttr("width");
$(".ocultarcoll").removeAttr("width");

$('.codigo').css({'width':'58px'});
$('.nombreemple').css({'width':'298px'});
$('.nss').css({'width':'76px'});
$('.rfc').css({'width':'102px'});
$('.curp').css({'width':'162px'});
$('.status').css({'width':'61px'});
$('.ocultarcoll').css({'width':'107px'});

 
$("#divpanelpdf").modal('show');

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





