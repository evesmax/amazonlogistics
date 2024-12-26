var catalogo = "empresario";
var formulario = "frm";
var columnas_centradas = [ 1 ];

$(document).ready(function() {

    $("#btn_guardar_folio").click(function(){
      seleccionarFolio($("#id").val(), $("#folio").val());
    });

});

function mostrarFolio(identificador, folio)
{
	$('#modal').off().on('shown.bs.modal', function () {
    $("#folio").val(folio);
    $("#id").val(identificador);
	}).modal({backdrop: 'static', keyboard: false, show: true});
}

function seleccionarFolio(id, folio){
  $.ajax({
    type: "POST",
      url: "../inovekia_dashboard/ajax.php?c=empresario&f=folio",
      dataType: "json",
      data: { id: id, folio: folio },
      success: function(respuesta){
        if(respuesta.status !== undefined && respuesta.status == true){
          mensajeIcono("success", "", "Informacion guardada correctamente", function(){
            $("#modal").modal("hide");
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