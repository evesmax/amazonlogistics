$(document).ready(function(){
$("#myModal").draggable({
			handle: ".modal-header"
		});
		
    $('select[name*="idtipop"] option[value="3"]').hide();

    $('.tablaentradas').DataTable( {
        "language": {
        "url": "js/Spanish.json",
      }
    });

  $.datepicker.setDefaults($.datepicker.regional['es-MX']);
  $("#fechainicio").datepicker({
    maxDate: 365,
    dateFormat: 'yy-mm-dd',
    numberOfMonths: 1,
    onSelect: function(selected) {
      $("#final").datepicker("option","minDate", selected);
      $("#nombreEmpleado").prop('disabled', false);
      $("#nominas").prop('disabled', true);
      $("#nombre").prop('disabled', true);
    }
  });

  $("#fechafin").datepicker({ 
   dateFormat: 'yy-mm-dd',
   maxDate:365,
   numberOfMonths: 1,
   onSelect: function(selected) {
    $("#inicial").datepicker("option","maxDate", selected);
    $("#nombreEmpleado").prop('disabled', false);
    $("#nominas").prop('disabled', true);
    $("#nombre").prop('disabled', true);     
  }
}); 
 

  if ($('#idtipop').val()=='*') {

     idtipop = '';
     cargaNominas(idtipop);

  }else{

    idtipop = $('#periodoSelecc').val();
    cargaNominas(idtipop);

  }
  });


   function envioCorreos(){
    x=0;
    cadena='';
    $("#table tr").each(function(index) {
      if(x>0){
        cadena+=$(this).attr('idemp')+'#.#'+$(this).attr('xml')+'##.##';
      }

      x++;
    });
   $("#divmsg").load("mail.php", {cadena:cadena,m:1});
}


function guardarinput(e,input, idempleado, idtipop, idnomp){
   
  vali= $('#i_'+input).val();
  idnomp= $('#nomiActiv').val();

  if(e.keyCode === 13){
      e.preventDefault(); 
  
     $.ajax({
      url:"ajax.php?c=reportes&f=actHoras",
      type: 'POST',
      data:{  vali:vali,
              input:input,
              idempleado: idempleado,
              idtipop: idtipop,
              idnomp: idnomp
             },
      success: function(r){
        $('#'+input).html('<td id="'+input+'" onclick="editar(\''+input+'\');">'+vali+'</td>');
            $.ajax({
                url:"ajax.php?c=reportes&f=CargarAutorizacionEntradas",
                type: 'POST',
                data:{ 
                 idnomp: idnomp,
                 nombre      : $('#nombre').val(),
                 periodotipo : $('#periodotipo').val(),
                 fini        : $('#fini').val(),
                 ffini       : $('#ffini').val()
                  },
                success: function(resp){
                  $("#segundo").html(resp);
                }
            });
      }
    });
   }else if(e.keyCode === 27 || e.code === 'Escape' ){
		$('#'+input).html('<td id="'+input+'" onclick="editar(\''+input+'\');">'+vali+'</td>');
	}

 }


 function editar(iddiv, idempleado, idtipop, idnomp){
  valortd= $.trim($('#'+iddiv).text());
  $('#'+iddiv).html('<input id="i_'+iddiv+'" onkeydown="guardarinput(event,\''+iddiv+'\','+idempleado+','+idtipop+','+idnomp+');" title="Presione ENTER para guardar y ESC para salir" style="width:100%;" type="text" value="'+valortd+'">');
  $('#i_'+iddiv).focus().val("").val(valortd); 
  $('#'+iddiv).prop('onclick',null).off('click');
}


function AutorizarEmpleado(idEmpleado,idnomp,diacompleto){
  
  var confirma = confirm("¿Esta seguro que desea autorizar las entradas.?");
  if (confirma == true) {
    $.post("ajax.php?c=Reportes&f=AutorizarEntradasEmple",{
      idEmpleado:idEmpleado,       
      idnomp:idnomp,
      diacompleto:diacompleto

    },function(request){
      if(request ==1){
        alert("Entradas actualizadas.");
            $.ajax({
                url:"ajax.php?c=reportes&f=CargarAutorizacionEntradas",
                type: 'POST',
                data:{ 
          
                      },
                     success: function(resp){                
                      $("#segundo").html(resp);
                }
            });   
      }
      else{
        alert("Error en el proceso.");
      } 
    });
    return true;

  }else{
    window.close();
  }
  $("#"+load).hide();
 
}


function Eliminartodo(idEmpleado,idnomp,diacompleto){
  
  var confirma = confirm("¿Esta seguro que desea anular los cambios.?");
  if (confirma == true) {
    $.post("ajax.php?c=Reportes&f=eliminarTodoAutorizacionEntradas",{
      idEmpleado:idEmpleado,       
      idnomp:idnomp,
      diacompleto:diacompleto

    },function(request){
      
      if(request == 1){
        alert("Elimino correctamente.");
            $.ajax({
                url:"ajax.php?c=reportes&f=CargarAutorizacionEntradas",
                type: 'POST',
                data:{},
                success: function(resp){
                 $("#segundo").html(resp);
                }
            });   
      }
      else{
        alert("Error en el proceso.");
      } 
    });
    return true;
  }else{
    window.close();
  }
  $("#"+load).hide();
 }



cargaNominas  = function(idTipop){
  
  $.ajax({
    url:"ajax.php?c=reportes&f=periodo",
    type: 'POST',
    dataType:'json',
    data:{
      idtipop: idTipop
    },
    success: function(r){
      //alert(r);
      if(idTipop=='*'){

        option='<option value="*">Todos</option>';
      }else{
        option='';
      }
      if(r.success==1 ){
        option='<option value="*">Todos</option>';

        $.each(r.data, function( k, v ) {  

          option+='<option value="'+v.idnomp+'">('+v.numnomina+') '+v.fechainicio+' '+v.fechafin+'</option>';
          
        });
      }else{
        option+='<option value="">No hay nominas</option>';         
      }
      $('#idnomp').html(option);
      $("#idnomp").val($('#hdnIdNomp').val());
      $('#idnomp').selectpicker('refresh'); 
      $(".idnomp li:nth-child(2)").css("background-color","#62bb5d");
    }
  });
}


$(function() {

    $('#idnomp').on('change', function(){
    var disabled = $(this).val() == 'true' ? false : true;
    $("#fechafin").prop('disabled', true);
    $("#fechainicio").prop('disabled', true);
    $("#nombreEmpleado").prop('disabled', true);
  });




 $('#idtipop').on('change', function(){ 
  
  //var v=$("#idtipop>option:selected").text(); 
  // alert("v"+v);
  // $("#peri").val(v);  
  idtipop = $(this).val(); 
  cargaNominas(idtipop);
 
  });
  
  $('#load').on('click', function() {
  
   $(this).button('loading');
      if ($("#mostrarfechas").prop("checked")){
      if(!$("#fechafin").val() || !$("#fechainicio").val()){
        alert("Seleccione una fecha.");
        $(this).button('reset'); 

   }else{
    $("#formentradas").submit();
  }    
}
else{
  $("#formentradas").submit();  
 }

});
});


  //INICIA GENERA PDF
  function pdf(){

    $(".collwidt").removeAttr("border");
    $('.mostrartabla').css({'display':'none'});
    $('.saltopagina').css({'display':'block'});
    $('.saltopagina').css({'page-break-before':'always'});
    $(".tablaentradas").removeAttr("fontSize");
    $('.tablaentradas').css({'fontSize':'9px'}); 

    var table = $('.tablaentradas').DataTable();
    table.destroy();
    $(".unoents").removeAttr("width");
    $(".dosents").removeAttr("width");
    $(".collwidt").removeAttr("width");
    $(".collwidts").removeAttr("width");
     
    $('.unoents').css({'width':'40px'});
    $('.dosents').css({'width':'100px'});
    $('.collwidt').css({'width':'60px'});
    $('.collwidts').css({'width':'20px'});
  


    var contenido_html = $("#imprimible").html();


    $("#contenido").text(contenido_html);

    $('.tablaentradas').DataTable( {
        "language": {
        "url": "js/Spanish.json"
      }
    } );

    $("#divpanelpdf").modal('show');
    $('.mostrartabla').css({'display':'inline'});

    $('.mostrar').css({'display':'inline'});
    $('.tablaentradas').css({'fontSize':'12.5px'}); 

  }
  function generar_pdf(){
    $("#divpanelpdf").modal('hide');
    $('.mostrartabla').css({'display':'inline'});
  }
  function cancelar_pdf(){
    $("#divpanelpdf").modal('hide');
  }

  function pdf_generado(){
    alert("OK");
  }
// TERMINA GENERA PDF


 //I M P R I M I R   P D F
 function printl(){
  $('.tablaentradas').css({'fontSize':'11px'}); 
 
  var table = $('.tablaentradas').DataTable();
  table.destroy();
  
  setTimeout(function () { 
    window.close();

  $('.tablaentradas').css({'fontSize':'12.5px'}); 

  $('.tablaentradas').DataTable( {
      "language": {
        "url": "js/Spanish.json"
      }
    } );
   
  }, 3000);

}

function accionEliminaFecha(idregistro){
	if(confirm("Esta seguro de eliminar el registro del dia?\nRecuerde que NO podra recuperarlo")){
		$.post("ajax.php?c=reportes&f=eliminarHorario",{
			idregistro:idregistro
		},function(resp){
			if(resp == 1){
				alert("Registro eliminado");
				$(".clase"+idregistro).hide("slow");
			}else{
				alert("Ocurrio un error, intente de nuevo");
			}
		});
	}
}


