// AM
$(document).ready(function(){

  $('.tableproducirproducto').DataTable({
    "language": {
      "url": "../../libraries/Spanish.json"
    },
    "paging": false,
    "info": false,
    "ordering": false,
    "searching": false
  });

  $('#ciclo').select2({ width: '100%' });
  $('#departamento').select2({ width: '100%' });
  $('#familia').select2({ width: '100%' });
  $('#linea').select2({ width: '100%' });
});


$(function() {

  $('#filtrar').on('click', function() { 

    var btnguardar = $(this);
    btnguardar.button("loading");

    if ($('#ciclo').val() =='' && $('#departamento').val()=='' && $('#familia').val()=='' && $('#linea').val()=='' ) {

      alert("Seleccione al menos un filtro.");
       btnguardar.button('reset');
   

    }else{

      $.ajax({
        url:"ajax.php?c=recetas&f=verproducirproducto",
        type: 'POST',
        dataType:'json',
        data:{
          ciclo        : $('#ciclo').val(),
          departamento : $('#departamento').val(),
          familia      : $('#familia').val(),
          linea        : $('#linea').val()
        },
        success: function(r){
          if(r.success==1 ){
            var table = $('.tableproducirproducto').DataTable();
            btnguardar.button('reset');

            $('#example-select-all').on('click', function(){
            // Check/uncheck all checkboxes in the table
            var rows = table.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            $('.tableproducirproducto tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked
              if(!this.checked){
                var el = $('#example-select-all').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
              if(el && el.checked && ('indeterminate' in el)){
                // Set visual state of "Select all" control 
                // as 'indeterminate'
                el.indeterminate = true; 
          }
        }
      });

            var table = $('.tableproducirproducto').DataTable({
              "language": {
                "url": "../../libraries/Spanish.json"
              },
              "data": r.data,
              "destroy": true,
              "autoWidth": false,
              "columns": [
              { "data": "producto","width": "35%"},
              { "data": "existemerma","width": "30%"},
              { "data": "id",
              "searchable": false,
              "orderable":false,"width": "15%",
              "render": function (data, type, row) {
                if (row.estatus === null) {

                  return '<button type="button" class="btn btn-block btn-success btn-sm" style="width:80px" onclick=cargareditar('+row.id+');><span class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></span> Editar</button>';

                }else { 

                  return '';
                }
              }
            },
            { "data": "id","width": "10%",  orderable: false,
            'targets': 3,
            'className': 'dt-body-center',
            "render": function (data, type, row) {

              if (row.estatus === null) {

                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';

              }else { 
                return '';
              }
            }
          }],
          'order': [[0, 'asc']]
        });

          }else{

            var table=$('.tableproducirproducto').DataTable( {
              "destroy": true,
              "paging": false,
              "info": false,
              "ordering": false,
              "searching": false,
              "language": {
                "url": "../../libraries/Spanish.json",
              },

              "columns": [
              null,
              null,
              null,
              null]
            });
            table.clear().draw();
            btnguardar.button('reset');
          }
        }

      });
    }
  });


  $('#masivo').on('click', function(){

    $('#myModal').modal('show');
    $('#selectciclo').select2({ width: '70%' });
    $('#saveIndiv').val(1);
    $('.ciclomasivo').show();
    $('.ciclounico').hide();
    $('#nombreproductoeditar').hide();
    $('#selectciclo').val('').trigger('change'); 
    
});  // termina $('#masivo');

    $('#saveIndiv').on('click', function(){

       if($('#saveIndiv').val()==1) {
           var arr = [];
           var table = $('.tableproducirproducto').DataTable(); 
           var selected = '';    
           table.$('input[type="checkbox"]').each(function(){
          if (this.checked) {
             selected += $(this).val()+','; 
             arr = selected;
          } 
         
          
        }); 
        console.log(arr);

        if (arr.length=='0') {

          alert("Seleccione al menos un producto para enviar masivo.");
          $('#myModal').modal('hide');
       
         }else if ($('#saveIndiv').val()==1 && $('#selectciclo').val()=='0' || ($('#saveIndiv').val()==1 && $('#selectciclo').val()=='')) {

          alert("Seleccione un ciclo para enviar masivo.");

         }else{
           
                      $.ajax({
                        url:"ajax.php?c=recetas&f=asignaciongeneral",
                        type: 'POST',
                        dataType:'json',
                        data:{
                          ciclo : $('#selectciclo').val(),
                          arr   : arr,
                          boton : $('#saveIndiv').val()

                        },
                        success: function(r){
                            if (r==1) {
                              alert("Se guardo correctamente.");

                              $( "#filtrar" ).trigger( "click" );
                              $('#myModal').modal('hide');
                              $('#example-select-all').prop('checked',false);

                        }else{
                              alert("Error al guardar.");
                    }
                        }
                      });
          }
         }
    });

     $('#saveIndiv').on('click', function() { 
   
   if ($('#saveIndiv').val()==0) {

    if($('#saveIndiv').val()==0 && $('#selectciclo').val()=='0' || ($('#saveIndiv').val()==0 && $('#selectciclo').val()=='')) {
      alert("Seleccione un ciclo.")

    }else{
     

    $.ajax({
      url:"ajax.php?c=recetas&f=asignaciongeneral",
      type: 'POST',
      dataType:'json',
      data:{
        
        ciclo  : $('#selectciclo').val(),
        boton  : $('#saveIndiv').val(),
        id     : $('#productname').val()

      },
      success: function(r){
        if (r == 1 ) {
          alert("Se guardo correctamente.");
          var table = $('.tableproducirproducto').DataTable();
          $( "#filtrar" ).trigger( "click" );
          $('#myModal').modal('hide');
          $('#example-select-all').prop('checked',false);

        }else{
          alert("Error al guardar.");
        }

      }
    }); 
  }
   }


  });
});
// Termina fuction general

function cargareditar($idseleccionado){

  $('#myModal').modal('show');
  $('#selectciclo').select2({ width: '70%' });
  $('#saveIndiv').val(0);
  $('.ciclounico').show();
  $('.ciclomasivo').hide();
  $('#selectciclo').val('').trigger('change');

  $.ajax({
    url:"ajax.php?c=recetas&f=editarproductounico",
    type: 'POST',
    dataType:'json',
    data:{
      id : $idseleccionado 
    },
    success: function(r){
      $('#nombreproductoeditar').show();
      $('#nombreproductoeditar').val(r.data.map(elem=>elem.producto));  
      $('#selectciclo').val(r.data.map(elem=>elem.valormerma)).trigger('change');
    }
  });
  
  $('#productname').val($idseleccionado);
  
  }

 

