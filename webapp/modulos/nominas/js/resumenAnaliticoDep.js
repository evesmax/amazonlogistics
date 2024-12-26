
$(document).ready(function(){

   $('.resumenAnalDep').DataTable( {
      "language": {
      "url": "js/Spanish.json"
        }
  });

 //$('#empleado').multiselect('destroy');
  $('#dep').multiselect({
  nonSelectedText: 'Seleccione',
  selectAllName: 'select-all-name',
  includeSelectAllOption: true,
  selectAllText: 'Todos!',
  filterPlaceholder: 'Buscar',
  enableFiltering:true,
  dropRight: true,
  buttonWidth: '100%'
});

 $('#idnomp').multiselect({
  nonSelectedText: 'Seleccione',
  selectAllName: 'select-all-name',
  includeSelectAllOption: true,
  selectAllText: 'Todos!',
  filterPlaceholder: 'Buscar',
  enableFiltering:true,
  dropRight: true,
  buttonWidth: '70%'
});
});



function activarChecked(){
//alert("primer alert");
   if ($("#mostrarnomiDep").prop("checked")){
    //alert(1);
       $('#mostrarrangonomi').prop('checked', true);  
       $('#mostrarfiltrodos').show(); 
       $('#mostrarprimerfiltro').hide();   
   }else{
    //alert(2);
       $('#mostrarrangonomi').prop('checked', false);
       $('#mostrarprimerfiltro').show();  
       $('#mostrarfiltrodos').hide();   
}
}

function activarCheckedrango(){
//alert("segundo alert");
  if ($("#mostrarrangonomi").prop("checked")){
    //alert(1);
      $('#mostrarnomiDep').prop('checked', true);  
      $('#mostrarprimerfiltro').show(); 
      $('#mostrarfiltrodos').hide();        
   }else{
      $('#mostrarnomiDep').prop('checked', false); 
      //alert(2); 
      $('#mostrarfiltrodos').show(); 
      $('#mostrarprimerfiltro').hide();       
       
   }
 }

// function validacionesDeSelect(){

//   $("#empleado").val('*').selectpicker('refresh');
//   $("#idnomp").val('*').selectpicker('refresh');
//   $("#idtipop").val('*').selectpicker('refresh');
//   $("#codigouno").val('').selectpicker('refresh');
//   $("#codigodos").val('').selectpicker('refresh');
//   $("#origen").val('').selectpicker('refresh');
// }



// function activarChecked(){
//   if ($("#mostrarvisual").prop("checked")){
//    // alert("aa");  
//    $('select[name*="idtipop"] option[value="3"]').show();
//    validacionesDeSelect();
//    $('#mostrarimprimir').prop('checked', true);
//    $('#mostrarimprimir').show(); 
//    $('#imprimir').show(); 
//    $('#impresion').css({'display':'inline'}); 
//    $('#listaRaya').css({'display':'inline'}); 
   
//    $('.mostrarrangos').show(); 
//    $('#divVisual').hide(); 
//    $('#pdf').hide(); 


//  }
//  else{
//     //alert("bb");
//     $('select[name*="idtipop"] option[value="3"]').hide();
//     validacionesDeSelect();
//     $('#mostrarimprimir').prop('checked', false);   
//     $('#impresion').css({'display':'none'}); 
//     $('#listaRaya').css({'display':'none'}); 
//     $('#imprimir').css({'display':'none'}); 
//     $('#divVisual').show(); 
//     $('.mostrarrangos').hide(); 
//     $('.rangoempleado').hide(); 
//     $('.empleadocheck').show();
//     $('#pdf').show();  
//   }
// }

// function activarCheckeddos(){
//   if ($("#mostrarimprimir").prop("checked")){
//     //alert("cc"); 
//     $('select[name*="idtipop"] option[value="3"]').hide();
//     validacionesDeSelect();
//     $('#mostrarvisual').prop('checked', true);     
//     $('.mostrarrangos').css({'display':'none'});
//     $('.rangoempleado').hide(); 
//     $('.empleadocheck').show(); 
//     $('#divVisual').show(); 
//     $('#pdf').show(); 
//     $('#impresion').css({'display':'none'});
//     $('#listaRaya').css({'display':'none'});
//     $('#imprimir').hide(); 
    
    
    
//   }else{
//    // alert("dd");
//    $('select[name*="idtipop"] option[value="3"]').show();
//    validacionesDeSelect();
//    $('.mostrarrangos').show();  
//    $('#imprimir').show(); 
//    $('#mostrarvisual').prop('checked', false);     
//    $('#mostrarimprimir').show(); 
//    $('#divVisual').hide(); 
//    $('#impresion').css({'display':'inline'});
//    $('#listaRaya').css({'display':'inline'});
//    $('#pdf').css({'display':'none'});
//  }
// }


// function activarCheckedtres(){
//   if ($("#mostrarrangos").prop("checked")){
//     //alert("p");
//     validacionesDeSelect();          
//     $('.empleadocheck').show();
//     $('.rangoempleado').hide();
//     $('#pdf').hide(); 
    
    
    
//   }else{
//     //alert("q"); 
//     validacionesDeSelect();  
//     $('.empleadocheck').hide(); 
//     $('.rangoempleado').show();
//     $('.extracheck').hide(); 
//     $('#pdf').hide(); 
//   }
// }

function ContPercepciones(idtipop,idnomp,idEmpleado,codigouno,codigodos,origen){
  var idtipop    = $("#idtipop").val();
  var idnomp     = $("#idnomp").val();
  var idEmpleado = $("#empleado").val();
  var codigouno  = $("#codigouno").val();
  var codigodos  = $("#codigodos").val();
  var origen     = $("#origen").val();
  
  $.post("ajax.php?c=Reportes&f=cargaPerceFiltros",{
    idtipop:idtipop,
    idnomp:idnomp,
    idEmpleado:idEmpleado,
    codigouno:codigouno,
    codigodos:codigodos,
    origen:origen
    
  },function (request){
    //alert(request);
    $("#contPerce").html(request);   
  });
}



cargaNominas  = function(idTipop){
  // alert(idTipop);
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
      if(r.success==1 ){
        // option='<option value="*">Todos</option>';
        $.each(r.data, function( k, v ) {  
          option+='<option value="'+v.idnomp+'">('+v.numnomina+') '+v.fechainicio+' '+v.fechafin+'</option>';
        });
      }else{
        option+='<option value="">No hay nominas</option>';         
      }
      $('#idnomp').html(option);

$('#idnomp').html(option);
$('#idnomp').multiselect('destroy');
$('#idnomp').multiselect({
  nonSelectedText: 'Seleccione',
  selectAllName: 'select-all-name',
  includeSelectAllOption: true,
  selectAllText: 'Todos!',
  filterPlaceholder: 'Buscar',
  enableFiltering:true,
  dropRight: true,
  buttonWidth: '100%'
});
      //$('#idnomp').selectpicker('refresh'); 
    }
  });
}

$(function() {
  
  

$("#dep").on("change",function(){
    var valor=$(this).val();
    if (valor!='*') {
      $("#depa").val(valor);
    }
  });


 $('#idnomp').on('change', function(){ 
var valornomi=$(this).val();
    if (valornomi!='*') {
      $("#idnomi").val(valornomi);
    }



  var v=$("#idnomp>option:selected").text();
  if(v != "Todos"){
    var i = v.split(")"), j = i[3], k = i[1]; 
    var cadena =k;  
    uno=cadena.substring(11,0);
      // alert(uno);
      dos = cadena.substring(22,12);
      // alert(dos);  
      $("#nomi").val(uno);
      $("#nomidos").val(dos);
    }
  });


//CARGA EL SELECT DE PERIODO CON EL DE LA NOMINA
 $('#idtipop').on('change', function(){ 
  var v=$("#idtipop>option:selected").text(); 

  $("#peri").val(v);  
  valip = $(this).val(); 
 
  cargaNominas(valip);
  if($("#idtipop>option:selected").val() == 3){
    $('.extracheck').show(); 
      //alert("aa");
    }else{
      $("#origen").val('').selectpicker('refresh');
      $('.extracheck').hide(); 
    }
  });



 $('#load').on('click', function() {
  $(this).button('loading'); 
  $("#formdep").submit();

  });
});
