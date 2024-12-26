  $(document).ready(function(){

    $('#tablaincidencias').DataTable( {
      "bAutoWidth": false,   
      "aaSorting": [[ 5, "asc" ]],
      "language": {
        "url": "js/Spanish.json"
      }
    });

    $('select[name*="nombre"] option[value="3"]').hide();

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
  });


  function activarChecked(){

  if ($("#mostrarfechas").prop("checked")){
  //alert("aa");  
  $('#mostrarfechas').prop('checked', true);
  $('#mostrarperiodos').prop('checked', true);
  $('.mostrarfecha').hide(); 
  $('.mostrarperiodo').show(); 

  }
  else{
  //alert("bb");
  $('#mostrarperiodos').prop('checked', false);
  $('.mostrarperiodo').hide();
  $('.mostrarfecha').show();  
  }
  }

  function activarCheckeddos(){
  
  if ($("#mostrarperiodos").prop("checked")){
  //alert("cc");  
  $('#mostrarfechas').prop('checked', true);
  $('#mostrarperiodos').prop('checked', true);
  $('.mostrarfecha').show(); 
  $('.mostrarperiodo').hide(); 
  }
  else{

  //alert("dd");
  $('#mostrarfechas').prop('checked', false);     
  $('.mostrarperiodo').show(); 
  $('.mostrarfecha').hide();  
  $('#fechainicio').val('').datepicker("refresh");
  $('#fechafin').val('').datepicker("refresh");
  }
  }

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

  function guardarinput(e,input){
    vali= $('#i_'+input).val();

    if(e.keyCode === 13){
  e.preventDefault(); // Ensure it is only this code that rusn
  $.ajax({
    url:"ajax.php?c=reportes&f=actHoras",
    type: 'POST',
    data:{vali:vali,input:input},
    success: function(r){
      $('#'+input).html('<td id="'+input+'" onclick="editar(\''+input+'\');">'+vali+'</td>');
    }
  });
  }
  }

  function editar(iddiv){
    valortd= $('#'+iddiv).text();

    $('#'+iddiv).html('<input id="i_'+iddiv+'" onkeypress="guardarinput(event,\''+iddiv+'\');"  style="width:100%;" type="text" value="'+valortd+'">');
    $('#'+iddiv).prop('onclick',null).off('click');
  }

  $(function() {
    $('#idnomp').on('change', function(){
      var disabled = $(this).val() == 'true' ? false : true;
      $("#fechafin").prop('disabled', true);
      $("#fechainicio").prop('disabled', true);
      $("#nombreEmpleado").prop('disabled', true);
    });


    $('#nombre').on('change', function(){

      valip = $(this).val(); 

      $.ajax({
        url:"ajax.php?c=reportes&f=periodo",
        type: 'POST',
        dataType:'json',
        data:{idtipop: $(this).val() },
        success: function(r){
          if(valip=='*'){
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

          $('#nominas').html(option);
          $('#nominas').selectpicker('refresh');
          $(".nominas li:nth-child(2)").css("background-color","#62bb5d");
        }
      });
    });


    $('#load').on('click', function() { 

      $(this).button('loading');
      if ($("#mostrarfechas").prop("checked")){
        if(!$("#fechafin").val() || !$("#fechainicio").val()){
          alert("Seleccione una fecha.");
          $('#load').button('reset');
        }else{
          $(this).button('loading');
          llenarreporteo();
        }
      }else{
        llenarreporteo();
      }  
    });
  });


  function llenarreporteo(){

    $.ajax({
      url:"ajax.php?c=Reportes&f=llenartablaIncidencias",
      type: 'POST',
      dataType:'json',
      data:{ 
        fechainicio: $('#fechainicio').val(),
        fechafin:    $('#fechafin').val(),
        empleados:   $('#empleados').val(),
        incidencias: $('#incidencias').val(),
        nombre:      $('#nombre').val(),
        nominas:     $('#nominas').val()
      },
      success: function(r){
  //var datax=JSON.stringify(r);
  var myObj, x;
  myObj = r;
  fechain = myObj["fechainicial"];
  fechafi = myObj["fechafinal"];
  
  document.getElementById("fechainiciop").innerHTML = fechain;
  document.getElementById("fechafinalp").innerHTML  = fechafi;
  


  if(r.success==1){
    $('#load').button('reset');

    $('#tablaincidencias').DataTable( {
      "destroy": true,
      "bAutoWidth": false,
      "language": {
        "url": "js/Spanish.json"
      },

      "data": r.data,
      "columns": [

      {"data":"idtipoincidencia"}, 
      {"data":"nombreCompleto"},
      {"data":"fechaseleccion"},
      {"data":"nombre"},
      {"data":"fechafinal"},
      {"data":"autorizadoletras"},
      {"data":"nom"}
      ] 
    });

  }else{

    $('#load').button('reset');

    var table=$('#tablaincidencias').DataTable( {
      "destroy": true,
      "bAutoWidth": false,
      "language": {
        "url": "js/Spanish.json"
      },
      "columns": [
      null,
      null,
      null,
      null,
      null,
      null,
      null
      ]
    });
    table.clear().draw();
  }
  }
  });
  }

  //INICIA GENERA PDF
  function pdf(){

 if ($('#fechainicio').val()!='' && $('#fechafin').val()!='') {
   
    $("#divRango").show();

  }else{

    $("#divRango").hide();
  }

    var table = $('#tablaincidencias').DataTable({
      "paging":   false,
      "ordering": false,
      "info":     false,
      "destroy": true,
      "bAutoWidth": false,
      "searching": false,
      "ordering": false
    });

    $('.estinegrit').css({'fontWeight':'bold','fontSize':'11px'});  

    var contenido_html = $("#imprimible").html();

    $("#contenido").text(contenido_html);

    $('#tablaincidencias').DataTable({
      "paging":   true,
      "ordering": true,
      "info":     true,
      "destroy": true,
      "bAutoWidth": false,
      "searching": true,
      "ordering": true, 
      "language": {
        "url": "js/Spanish.json"
      }
    });
    $("#divRango").hide();

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
  // TERMINA GENERA PDF
  // 
  // 