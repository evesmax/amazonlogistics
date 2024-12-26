var datosAumentoSalario;
$(document).ready(function(){

  $('.aumentosalario').DataTable( {
    "scrollX": true,
    "language": {
      "url": "js/Spanish.json",
      "info": "No existen registros."
    },
    "lengthMenu": [ 5,10, 25, 50, 75, 100 ]
  }); 


  var clicker=0;
  $("#empleado option[value='*']").click(function(e){
    clicker++;
    $(this).prop('selected', false);
    if(clicker%2!=0){
      $("#mpleado option[value!='all']").prop('selected', true);
    }
    else
      $("#mpleado option[value!='all']").prop('selected', false);
  });

  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
    ignoreReadonly: true,
    useCurrent: false ,
    locale: 'es'
  });


  $('#checkbox1').click(function() {
// Si esta seleccionado (si la propiedad checked es igual a true)
  if ($(this).prop('checked')) {
// Selecciona cada input que tenga la clase 
  $('#radio1').prop('disabled',false);
  $("#radio2").prop("disabled", false); 
  $("#radio3").prop("disabled", false);   
  $("#montosalario").prop("disabled", false); 
  $("#montosalario").val(); 

} else {
// Deselecciona cada input que tenga la clase 
$('#radio1').prop('disabled',true);
$("#radio2").prop("disabled", true); 
$("#radio3").prop("disabled", true);   
$("#montosalario").prop("disabled", true); 
$("#montosalario").val(0); 

}
});

  $('#radio1').on('click', function(){

    $("#montosalario").attr("placeholder", "Porcentaje").blur();

//$("#etiqueta").text("Porcentaje");
    $("#etiqtit").text("%");
    $("#etiqtit").val("");
});

    $('#radio2').on('click', function(){
    $("#montosalario").attr("placeholder", "Incremento en cantidad").blur();

//$("#etiqueta").text("Incremento en cantidad");
    $("#etiqtit").text("$");
    $("#etiqtit").val("");
});

    $('#radio3').on('click', function(){
    $("#montosalario").attr("placeholder", "Nuevo salario").blur();

//$("#etiqueta").text("Nuevo salario");
    $("#etiqtit").text("$");
    $("#etiqtit").val("");
});



//CARGAR LOS DEPARTAMENTOS
$('#idtipop').on('change', function(){

  $("#dep").val('*').selectpicker('refresh');
  llenarEmpleados();

  valip = $(this).val(); 

  $('#dep').on('change', function(){
    llenarEmpleados();  
  });
});

datosAumentoSalario='';

});

function llenarEmpleados(){
  $.ajax({
      url:"ajax.php?c=Sobrerecibo&f=cargaEmple",
      type: 'POST',
      dataType:'json',
      data:{
        idtipop: $('#idtipop').val(),
        dep:$('#dep').val()

      },
      success: function(r){
        if(valip=='*'){
//option='<option value="*">Todos</option>';
}else{
  option='';
}
if(r.success==1 ){

// option='<option value="*">Todos</option>';
$.each(r.data, function( k, v ) {

  option+='<option value="'+v.idEmpleado+'">'+v.apellidoPaterno+' '+v.apellidoMaterno+' '+v.nombreEmpleado+'</option>';
});

}else{
  option+='<option value="">No hay Empleados</option>';         
}

$('#empleado').html(option);
$('#empleado').selectpicker('refresh');

}
}); 
}


$(function() {

  $('#load').on('click', function(evt) {

    var btndiafest = $(this);
    btndiafest.button('loading'); 

    if($("#montosalario").val().trim() =='' || isNaN($("#montosalario").val()) || $("#registro").val() =='' ||  $("#idtipop").val() =='' ||  $("#dep").val() =='' ||  $("#emple").val() ==''   ){             
      alert("Llene los filtros.");
      evt.preventDefault();
      btndiafest.button('reset'); 

    }else{

      $('.aumentosalario').DataTable().destroy();

      $.post("ajax.php?c=Sobrerecibo&f=aumentarsala",{  
        registro: $("#registro").val(),
        checkbox1: $('input[name="checkbox"]:checked').val(),
        dep:$("#dep").val(),
        emple:$("#emple").val(),
        empleado:$("#empleado").val(),
        radio:$('input:radio[name=radio]:checked').val(),
        idtipop:$("#idtipop").val(),
        montosalario:$("#montosalario").val()

      },function(resp){


        $("#nomb").html(resp);
        $('.aumentosalario').DataTable( {
          "scrollX": true,
          "language": {
            "url": "js/Spanish.json",
            "info": "No existen registros."
          },
          "lengthMenu": [ 5,10, 25, 50, 75, 100 ]
        }); 
        btndiafest.button('reset'); 

      });
    }
  });

  $('#empleado').selectpicker('refresh');
  $("#empleado").on("change",function(){
    var valor=$(this).val();
    if (valor!='*') {
      $("#emple").val(valor);
    }
  });

  $("#idtipop").on("change",function(){
    var valor=$(this).val();

    if (valor!='*') {   
      $("#pagot").val(valor);
    }
  });

  $('#guardarAumento').on('click', function(evt) { 

    if($("#txtfecha").val().trim() ==''){             
      alert("Selecciona una fecha de aplicación..");
      evt.preventDefault();
      $(this).button('reset');

    }else{

      $.ajax({
        url:"ajax.php?c=Sobrerecibo&f=existeAumento",
        type: 'POST',
        dataType:'json',
        data:
        {  
          txtfecha: $("#txtfecha").val(),
          pru:$("#pru").val()

        },
        success: function(r){

          if(r==1){
            guardaAumentoSueldo();

          }else if (r=2) {

            if(confirm("El periodo activo ya tiene registros,debera recalcular nomina.")){

              guardaAumentoSueldo();
              window.parent.agregatab('../../modulos/nominas/index.php?c=Prenomina&f=vistaPrenomina','Calculo de prenomina','',2282);

            }
          }

          else{
            alert("Error.");
          } 
        },
        error: function(e){
          alert("Error.");
        }
      });
    }
  });

  guardaAumentoSueldo = function(){
    $.ajax({
      url:"ajax.php?c=Sobrerecibo&f=guardarAumentoSalaHisto",
      type: 'POST',
      dataType:'json',
      data:
      {  
        checkbox1: $('input[name="checkbox"]:checked').val(),
        emple:$("#emple").val(),
        radio:$('input:radio[name=radio]:checked').val(),
        montosalario: $("#montosalario").val(),
        txtfecha: $("#txtfecha").val(),
        idnomp: $("#idtipop option:selected").attr("idnomp"),
        idtipop: $("#idtipop").val()
      },
      success: function(r){
        if(r==1){
          alert("Guardado.");
        }else if (r=2) {
          alert("La fecha de aplicación,debe ser de inicio de periodo.");

        }else if (r=3) {
          alert("tres");
        }

        else{
          alert("Error.");
        } 
      },
      error: function(e){
        alert("Error.");
      }
    });
  }

});

