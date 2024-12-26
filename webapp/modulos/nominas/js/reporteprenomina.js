$(document).ready(function(){
 
    $('.firma').addClass('mostrardiv');
    $(".firma").css("display", "none");
    $(".firma").hide();

    $('#impresion').css({'display':'none'});
    $('#listaRaya').css({'display':'none'});
  
    $('.mostrarrangos').hide(); 
    $('.rangoempleado').hide(); 
    $('.extracheck').hide(); 
    //cargaNominas('*');
});

function validacionesDeSelect(){

  $("#empleado").val('').selectpicker('refresh');
  $("#idnomp").val('').selectpicker('refresh');
  $("#idtipop").val('').selectpicker('refresh');
  $("#codigouno").val('').selectpicker('refresh');
  $("#codigodos").val('').selectpicker('refresh');
  $("#origen").val('').selectpicker('refresh');
}

function activarChecked(){
  if ($("#mostrarvisual").prop("checked")){
   // alert("aa");  
   $('select[name*="idtipop"] option[period="11"]').show();
   validacionesDeSelect();
   $('#mostrarimprimir').prop('checked', true);
   $('#mostrarrangos').prop('checked', false);    
   $('#mostrarimprimir').show(); 
   $('#imprimir').show(); 
   $('#impresion').css({'display':'inline'}); 
   $('#listaRaya').css({'display':'inline'}); 
   
   $('.mostrarrangos').show(); 
   $('#divVisual').hide(); 
   $('#pdf').hide(); 
 }
 else{
    // alert("bb");
    $('select[name*="idtipop"] option[period="11"]').hide();

    validacionesDeSelect();
    $('#mostrarimprimir').prop('checked', false);   
    $('#impresion').css({'display':'none'}); 
    $('#listaRaya').css({'display':'none'}); 
    $('#imprimir').css({'display':'none'}); 
    $('#divVisual').show(); 
    $('.mostrarrangos').hide(); 
    $('.rangoempleado').hide(); 
    $('.empleadocheck').show();
    $('#pdf').show();  
  }
}

function activarCheckeddos(){
  if ($("#mostrarimprimir").prop("checked")){
    // alert("cc"); 
    $('select[name*="idtipop"] option[value="3"]').hide();
    validacionesDeSelect();
    $('#mostrarvisual').prop('checked', true);     
    $('.mostrarrangos').css({'display':'none'});
    $('.rangoempleado').hide(); 
    $('.empleadocheck').show(); 
    $('#divVisual').show(); 
    $('#pdf').show(); 
    $('#impresion').css({'display':'none'});
    $('#listaRaya').css({'display':'none'});
    $('#imprimir').hide(); 
  
  }else{
  // alert("dd");
   $('select[name*="idtipop"] option[value="3"]').show();
   validacionesDeSelect();
   $('.mostrarrangos').show();  
   $('#imprimir').show(); 

   $('#mostrarrangos').prop('checked', false);     
   $('#mostrarvisual').prop('checked', false);     
   $('#mostrarimprimir').show(); 
   $('#divVisual').hide(); 
   $('#impresion').css({'display':'inline'});
   $('#listaRaya').css({'display':'inline'});
   $('#pdf').css({'display':'none'});
 }
}


function activarCheckedtres(){
  if ($("#mostrarrangos").prop("checked")){
    // alert("p");
    validacionesDeSelect();          
    $('.empleadocheck').show();
    $('.rangoempleado').hide();
    $('#pdf').hide(); 
      
  }else{
    // alert("q"); 
    validacionesDeSelect();  
    $('.empleadocheck').hide(); 
    $('.rangoempleado').show();
    $('.extracheck').hide(); 
    $('#pdf').hide(); 
  }
}

function ContPercepciones(idtipop,idnomp,idEmpleado,codigouno,codigodos,origen){

  $.post("ajax.php?c=Reportes&f=cargaPerceFiltros",{
    idtipop   : $("#idtipop").val(),
    idnomp    : $("#idnomp").val(),
    idEmpleado: $("#empleado").val(),
    codigouno : $("#codigouno").val(),
    codigodos : $("#codigodos").val(),
    origen    : $("#origen").val()
    
  },function (request){
    $("#contPerce").html(request);   
  });
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
          option+='<option value="'+v.idnomp+'" nomina="'+v.numnomina+'"  fechainicial="'+v.fechainicio+'" fechafinal="'+v.fechafin+'">('+v.numnomina+') '+v.fechainicio+' '+v.fechafin+'</option>';
        });
      }else{
        option+='<option value="">No hay nominas</option>';         
      }
      $('#idnomp').html(option);
      $('#idnomp').selectpicker('refresh'); 
      $(".idnomp li:nth-child(2)").css("background-color","#62bb5d");

    }
  });
}



function cargarinformacion(){
  $("#loade").show();
    if ($("#mostrarvisual").prop("checked")){
      $.post("ajax.php?c=Reportes&f=tablaReporteSobrerecibo",{
        idtipop:$("#idtipop").val(),
        idnomp:$("#idnomp").val(),
        idEmpleado: $("#empleado").val(),
        nomi: $('#nomi').val(),
        nomidos: $("#nomidos").val()
        
        
      },function(resp){   
        //  alert(resp); 
        $("#divVisual").html(resp); 
        
        var extensions = {
          "sLength": "custom_length_class text-left", 
          "sInfo": 'text-left'
        }
        
        $.extend($.fn.dataTableExt.oStdClasses, extensions);
        $.extend($.fn.dataTableExt.oJUIClasses, extensions);
        
        $('#divVisualx').DataTable( {
          // "scrollX": true,
          "language": {
            "url": "js/Spanish.json",
            "info": "No existen registros."
            
          },
          "lengthMenu": [ 5,10, 25, 50, 75, 100 ]
        } );            
      });
      $("#divVisual").html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>');           
      $('#divVisual').show();
      $('#divVisualv').show();  
      $('#imprimir').hide(); 
      $('#impresion').css({'display':'none'}); 
      $('.mostrarrangos').hide(); 
      $('.rangoempleado').hide(); 
      $('.empleadocheck').show(); 
    }
    
    if ($("#mostrarimprimir").prop("checked")){

      $('.mostrarrangos').show();     
      $('#mostrarimprimir').show();   
      $('#divVisual').hide(); 
      $('#impresion').css({'display':'inline'});
      
      if(($("#codigouno").val()!="" && $("#codigodos").val()=="") || ($("#codigodos").val()!="" && $("#codigouno").val()=="")){
        alert("seleccione un rango valido.");
        
      }else{ 

        $("#contPerce").html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>');     
        $('#imprimir').show(); 
        $('#pdf').css({'display':'none'}); 
        ContPercepciones();
      }
    }

}



$(function() {
  
 $('#idnomp').on('change', function(){ 
  var v=$("#idnomp>option:selected").text();
  if(v != "Todos"){
      $("#nomi").val($("#idnomp option:selected").attr("fechainicial"));
      $("#nomidos").val($("#idnomp option:selected").attr("fechafinal"));
    }
  });


 $('#idtipop').on('change', function(){ 
  var v=$("#idtipop>option:selected").text(); 
  $("#peri").val(v);  
  valip = $(this).val(); 
  cargaNominas(valip);
  if($("#idtipop>option:selected").val() == 3){ 
    $('.extracheck').show(); 
    
    }else{
      $("#origen").val('').selectpicker('refresh');
      $('.extracheck').hide(); 
    }
  });

 $('#load').on('click', function() {


if ($('#mostrarvisual').prop('checked')==true || ($('#mostrarimprimir').prop('checked')==true && $('#mostrarrangos').prop('checked')==false)) {

    if ($("#idtipop").val()=='' || $("#idnomp").val()=='' || $("#empleado").val()=='') {
      alert("Llene los filtros.");
  }else{
     cargarinformacion();
  }
}else
  if ($('#mostrarimprimir').prop('checked')==true && $('#mostrarrangos').prop('checked')==true && $("#idtipop>option:selected").val() != '3' ) {
    
       if ($("#idtipop").val()=='' || $("#idnomp").val()=='' || $("#codigouno").val()=='' || $("#codigodos").val()=='') {
     alert("Llene los filtros.");
     
  }else{
    cargarinformacion();
  }
  
}else{
   if ($('#mostrarimprimir').prop('checked')==true && $('#mostrarrangos').prop('checked')==true && $("#idtipop>option:selected").val() == '3') {
   
    
  if ($("#idtipop").val()=='' || $("#idnomp").val()=='' || $("#origen").val()=='' || $("#codigouno").val()=='' || $("#codigodos").val()=='') {
   alert("Llene los filtros.");

}else  {
  cargarinformacion();
 
     }
  }
}
});


 $('#codigouno').on('change', function(){

 var option='';
  $.ajax({
    url:"ajax.php?c=reportes&f=cargarcodigo",
    type: 'POST',
    dataType:'json',
    data:{
      codigouno: $('#codigouno').val()
    },
    success: function(x){
   
      if(x.success==1){
        $.each(x.data, function( k, v) {  
          option+='<option value="'+v.idEmpleado+'">'+v.codigo+'</option>';
        });
      }

      $('#codigodos').html(option);
      $('#codigodos').selectpicker('refresh'); 
     
    }
  });
});
});

//INICIA GENERA PDF
function pdf(){

//   /*E M P I E Z A  T A B L A  V I S U A L *REPORTE PRENOMINA**/
 var table = $('#divVisualx').DataTable();
 table.destroy();
  
$(".coluno").removeAttr("width"); 
$(".colemple").removeAttr("width"); 
$('.taman').removeAttr("fontSize");   
  
//   /*T E R M I N A  R E P O R T E  P R E N O M I N A*/
 $(".mostrar").show();

//   /*E M P I E Z A  T A B L A  V I S U A L *REPORTE PRENOMINA**/
  
 $('.coluno').css({'width': '55px '});
 $('.colemple').css({'width': '85px '});
 $('.taman').css({'fontSize':'7.5px'});
  
  
  var contenido_html = $("#imprimible").html();

  $('#divVisualx').DataTable( {
    "language": {
      "url": "js/Spanish.json"
    }
  }); 
  $('.taman').css({'fontSize':'9.6px'});
//   /*TERMINA PRENOMNA*/
  
   $("#contenido").text(contenido_html);
   $("#divpanelpdf").modal('show');
   $(".mostrar").hide();
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


// EMPIEZA GENERAR IMPRIMIR
function printl(){

  setTimeout(function () { 
   
    window.close(); 
    $('.estilopdf').css({'color':'white','fontSize':'16px'});
    
  }, 500);
  
}


function listaRaya(){

    $('.firma').removeClass('mostrar');

    setTimeout(function () { 
    window.close();
    $('.firma').addClass('mostrar');
    
  }, 1000);
  






}
