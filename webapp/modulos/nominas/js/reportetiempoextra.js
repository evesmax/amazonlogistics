$(document).ready(function(){
	

	// $('#tablate').DataTable( { 	
		// "language": {
			// "url": "js/Spanish.json"
		// }
	// }); 
}); 
 $(function() {
 	
$('#load').on('click', function() {
	var sepa = $("#nominas").val().split("/"); 
	var btnguardar = $(this);
		 btnguardar.button("loading");
 	$.post("ajax.php?c=reportes&f=contenidote",{
 		idnomp:sepa[0],
 		empleado :$("#nombreEmpleado").val(),
 		fi:sepa[1],
 		ff:sepa[2]
 	},function(resp){
 		btnguardar.button('reset');
 		$("#contenidote").html(resp);
 		if ( $.fn.dataTable.isDataTable( '#tablate' ) ) {//comprobamos si ya fue definida
		    table = $('#tablate').DataTable();//si ya solo refrescamos
		}
		else {
		    table =$('#tablate').DataTable( {//sino esta definida, definimos
					"language": {
						"url": "js/Spanish.json"
					}
				});
		}
 	});
 });
 
 
  $('#tipoperiodo').on('change', function(){

   
		if($(this).val() != 0){
		    $.ajax({
		      url:"ajax.php?c=reportes&f=periodo",
		      type: 'POST',
		      dataType:'json',
		      data:{idtipop: $(this).val() },
		      success: function(r){
		       
		       if(r.success==1 ){
		
		               option='';
		               $.each(r.data, function( k, v ) {  
		               option+='<option value="'+v.idnomp+'/'+v.fechainicio+'/'+v.fechafin+'">('+v.numnomina+') '+v.fechainicio+' '+v.fechafin+'</option>';
		            });
		
		           }else{
		            option+='<option value="">No hay nominas</option>';         
		          }
		
		          $('#nominas').html(option);
		          $('#nominas').selectpicker('refresh');
		          $(".nominas li:nth-child(1)").css("background-color","#62bb5d");
		        }
		      });
	     }else{
	     	 $('#nominas').html("");
		     $('#nominas').selectpicker('refresh');
	     }
	});
});


function recibo(idempleado,idnomp,idtipop){
	 window.parent.preguntar=false;
     window.parent.quitartab("tb2297",2297,"Recibo");
     window.parent.agregatab('../../modulos/nominas/index.php?c=reportes&f=cargaPerceFiltros&idEmpleado='+idempleado+'&idtipop='+idtipop+'&idnomp='+idnomp,'Recibo','',2297);
     window.parent.preguntar=true;
}
