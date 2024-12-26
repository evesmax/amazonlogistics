var catalogo = "consultor";
var formulario = "frm";
var columnas_centradas = [ 2 ];
var mapa;
var marcador;
var fecha;
var _empresario;
var _consultor;

var tabla_modulo_empresarios;
var tabla_modulo_seguimientos;

$(document).ready(function() {

  	tabla_modulo_empresarios = $('#data_table_empresarios').DataTable({
      	language: {
        	url: 'http://cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json'
      	},
      	"columnDefs": [
		    { className: "dt-body-center", "targets": [ 1, 2, 3, 4 ] }
		]
  	});

    tabla_modulo_seguimientos = $('#data_table_seguimientos').DataTable({
        language: {
          url: 'http://cdn.datatables.net/plug-ins/1.10.12/i18n/Spanish.json'
        },
        "columnDefs": [
        { className: "dt-body-center", "targets": [ ] }
    ]
    });

    $('#fecha').datetimepicker({
      inline: true,
      sideBySide: true,
      locale: 'es',
      useCurrent: false
    }).on('dp.change', function(event){
      fecha = event.date.format('YYYY-MM-DD HH:mm:ss');
    });

    $("#btn_guardar_visita").click(function(){
      seleccionarVisita();
    });

});

function iniciaMapa()
{
  mapa = new google.maps.Map(document.getElementById('mapa'), {
        center: {lat: 23.7628153, lng: -101.8123433},
        zoom: 4
      });
  mapa.addListener('click', function(e) {
      if(marcador == null){
        marcador = new google.maps.Marker({
          position: e.latLng,
          map: mapa
        });  
      } else {
        marcador.setPosition(e.latLng);
      }
      mapa.panTo(e.latLng);
  });
}

function mostrarEmpresarios(identificador)
{
	$('#modal').off().on('shown.bs.modal', function () {
    popularTablaConParametros("empresario", { 'consultor': identificador }, tabla_modulo_empresarios);
	}).modal({backdrop: 'static', keyboard: false, show: true});
}

function seleccionarEmpresario(consultor, empresario){
  $.ajax({
    type: "POST",
      url: "../inovekia_dashboard/ajax.php?c=empresario&f=seleccionar",
      dataType: "json",
      data: { id_consultor: consultor, id_empresario: empresario },
      success: function(respuesta){
        if(respuesta.status !== undefined && respuesta.status == true){
          mensajeIcono("success", "", "Informacion guardada correctamente", function(){
            popularTablaConParametros("empresario", { 'consultor': consultor }, tabla_modulo_empresarios);
          });
        }else{
          mensajeIcono("error", "Un momento...", respuesta.mensaje, function(){});
        }
      },
      error: function(error){
        mensajeIcono("error", "Un momento...", "No se ha podido completar esta accion, por favor intentalo nuevamente", function(){});
      }
  });
}

function eliminarEmpresario(consultor, empresario){
  $.ajax({
    type: "POST",
      url: "../inovekia_dashboard/ajax.php?c=empresario&f=eliminar",
      dataType: "json",
      data: { id_consultor: consultor, id_empresario: empresario },
      success: function(respuesta){
        if(respuesta.status !== undefined && respuesta.status == true){
          mensajeIcono("success", "", "Informacion guardada correctamente", function(){
            popularTablaConParametros("empresario", { 'consultor': consultor }, tabla_modulo_empresarios);
          });
        }else{
          mensajeIcono("error", "Un momento...", respuesta.mensaje, function(){});
        }
      },
      error: function(error){
        mensajeIcono("error", "Un momento...", "No se ha podido completar esta accion, por favor intentalo nuevamente", function(){});
      }
  });
}

function visita(consultor, empresario)
{
  _empresario = empresario;
  _consultor = consultor;
  $('#modal-visita').off().on('shown.bs.modal', function () {
    iniciaMapa();
  }).modal({backdrop: 'static', keyboard: false, show: true});
}

function seleccionarVisita(consultor, empresario){
  if(fecha !== undefined && marcador !== undefined){
    var tiempo = fecha.split(" ");
    $.ajax({
      type: "POST",
        url: "../inovekia_dashboard/ajax.php?c=empresario&f=visita",
        dataType: "json",
        data: { empresario: _empresario, consultor: _consultor, fecha: tiempo[0], hora: tiempo[1], latitud: marcador.getPosition().lat(), longitud: marcador.getPosition().lng() },
        success: function(respuesta){
          if(respuesta.status !== undefined && respuesta.status == true){
            mensajeIcono("success", "", "Informacion guardada correctamente", function(){});
          }else{
            mensajeIcono("error", "Un momento...", respuesta.mensaje, function(){});
          }
        },
        error: function(error){
          mensajeIcono("error", "Un momento...", "No se ha podido completar esta accion, por favor intentalo nuevamente", function(){});
        }
    });
  } else {
    mensajeIcono("error", "Un momento...", "Debes seleccionar la fecha y hora de la visita, asi como la ubicación del lugar", function(){});
  }
}

function seguimiento(consultor, empresario)
{
  $('#modal-seguimiento').off().on('shown.bs.modal', function () {
    popularTablaConParametros("empresario", { 'consultor': consultor, 'empresario': empresario }, tabla_modulo_seguimientos, "seguimiento");
  }).modal({backdrop: 'static', keyboard: false, show: true});
}