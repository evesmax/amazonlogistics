
$(document).ready(function(){

  $('.tablaentradas').DataTable( {
    "language": {
      "url": "js/Spanish.json"
    }
  });

  $.datepicker.setDefaults($.datepicker.regional['es-MX']);
  $("#fechainicio").datepicker({
    maxDate: 365,
    dateFormat: 'yy-mm-dd',
    numberOfMonths: 1
  });

  $("#fechafin").datepicker({ 
    dateFormat: 'yy-mm-dd',
    maxDate:365,
    numberOfMonths: 1
  }); 
});


$(function() {

  $('#load').on('click', function(evt) { 

    $(this).button('loading');
    if($("#fechainicio").val() =='' || isNaN($("#fechafin").val()=='' || $("#empleado").val()=='' || $("#sucursal").val()=='' )){  
      alert("Seleccione un filtro de búsqueda.");

      $(this).button('reset');  

    }else{

      $.ajax({
        url:"ajax.php?c=reporteEntradas&f=llenarReporteEntradas",

        data:{
          fechainicio: $('#fechainicio').val(),
          fechafin: $('#fechafin').val(),
          empleado: $('#empleado').val(),
          sucursal: $('#sucursal').val()

        },
        success: function(r){
          $("#load").button("reset");
          $("#llenarentradas").html(r); 
          $('.tablaentradas').DataTable( {
            "destroy": true,
            "language": {
              "url": "js/Spanish.json",
              "info": "No existen registros."     
            }
          }); 
        }
      });
    }
  });
});



// I M P R I M I R   P D F

function printl(){
  
  $('.tablaentradas').css({'fontSize':'11px'}); 

  var table = $('.tablaentradas').DataTable();
  table.destroy();

  $('.border').css({'border':'0px'});

  setTimeout(function () { 
    window.close();

    $('.tablaentradas').css({'fontSize':'12.5px'}); 
    $(".border").css('border','');
    $('.tablaentradas').DataTable( {
      "language": {
        "url": "js/Spanish.json"
      }
    });
  }, 3000);
}

