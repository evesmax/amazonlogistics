$(function(){ 
   if( $('#tabla_abasto').length>1){
    $('#tabla_abasto').DataTable({
        "ordering": false,
    language: {
        search: "Buscar:",
        lengthMenu:"Mostrar _MENU_ elementos",
        zeroRecords: "No hay datos.",
        infoEmpty: "No hay datos que mostrar.",
        info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
        paginate: {
            first:      "Primero",
            previous:   "Anterior",
            next:       "Siguiente",
            last:       "Último"
        }
     },
    });

    $('.sorting_asc').removeClass();
	}
}); 

function verInsumos(idop,nombreprod,cantidad,opc,idread){
	$("#vistainsumos").modal('show');
	nombreprod = nombreprod.toUpperCase();

	$("h4").html("No.Orden:"+idop+"<br>Para Producir: "+nombreprod);
	$('#contenidoinsu').html('<div class="loader" align="center"></div> ');
	 // //escape() te convierte los espacios en %20 y con eso lo interpretara apropiadamente el navegador
	$('#contenidoinsu').load('ajax.php?c=Reportes&f=vertInsumos&cant='+cantidad+'&idop='+idop+'&idread='+idread+'&opc='+opc+'&prod='+escape(nombreprod));
       

 }
 function autorizarTodo(idop){
    $("#todo").prop('disabled', true);
    $("#todo").text("Autorizando...");
    
 	$.post("ajax.php?c=Reportes&f=autorizaReabasto",{
      idop: idop,
      opc:1
    },function(resp){
    	$("#todo").prop('disabled', false);
    	$("#todo").html('<span class="glyphicon glyphicon-edit"></span>Autorizar');
    	if(resp==1){
    		alert("Reabasto autorizado :)");
    		window.location.reload();
    	}else{
    		alert("Ocurrio un error, intente de nuevo");
    	}
    });
 	
 }

 function autorizarInsumo(idinsumo,idop){
    $("#btn"+idinsumo).prop('disabled', true);
    $("#btn"+idinsumo).text("Autorizando...");
    
 	$.post("ajax.php?c=Reportes&f=autorizaReabasto",{
      idop: idop,
      insumo:idinsumo,
      opc:2
    },function(resp){
    	if(resp==1){
    		alert("Insumo autorizado :)");
    		$("#btn"+idinsumo).html('Autorizado');
    	}else if(resp == 0){
    		alert("Ocurrio un error, intente de nuevo");
    		$("#btn"+idinsumo).prop('disabled', false);
    		$("#btn"+idinsumo).html('<span class="glyphicon glyphicon-edit"></span>Autorizar');
    	}else{
    		alert("Insumo autorizado :)");
    		$("#btn"+idinsumo).html('Autorizado');
    		window.location.reload();
    	}
    });
 	
 }
 function cancelar(idop){
 	if(confirm("Seguro que desea cancelar el reabasto?")){
 		$("#cancelar"+idop).prop('disabled', true);
    	$("#cancelar"+idop).text("Cancelando...");
    	$.post("ajax.php?c=Reportes&f=cancelarReabasto",{
      	idop: idop
    	},function(resp){
    		if(resp == 1){
    			alert("Reabasto Cancelado");
    			$("#cancelar"+idop).prop('disabled', false);
    			$("#cancelar"+idop).html('Cancelada');
    			window.location.reload();
    		}else if(resp == 0){
    			alert("Error en el proceso");
    			$("#cancelar"+idop).prop('disabled', false);
    			$("#cancelar"+idop).html('<span class="glyphicon glyphicon-remove"></span>Cancelar');
    			
    		}else{
    			alert("No puede cancelar reabasto con insumos autorizados.");
    			$("#cancelar"+idop).prop('disabled', false);
    			$("#cancelar"+idop).html('<span class="glyphicon glyphicon-remove"></span>Cancelar');
    		}
    	});
 	}
 }
 