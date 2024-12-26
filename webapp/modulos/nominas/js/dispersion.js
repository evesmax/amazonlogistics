$(document).ready(function(){
  $('.tablainicio').DataTable({
    "language": {
      "url": "js/Spanish.json"
    },"bLengthChange" : false,
    "scrollX": true
  });

  $('#tablanueva').DataTable({
    "language": {
      "url": "js/Spanish.json"
    },"bLengthChange" : false,
    "scrollX": true
  });
  $('#fecha').datetimepicker({
    format: 'YYYY-MM-DD',
    ignoreReadonly: true,
    useCurrent: false ,
    locale: 'es'
  });



  $('#fechafin').datetimepicker({
    format: 'YYYY-MM-DD',
    ignoreReadonly: true,
    useCurrent: false ,
    locale: 'es'
  });
});

function atraslistado(){
  window.location ="index.php?c=Dispersion&f=dispersion";
}
function newDispersion(){
  window.location="index.php?c=Dispersion&f=cargaDeDatos";
}


$(function() {
  $('#tipoperiodo').on('change', function(){
  $('#tablanueva').DataTable().destroy();
var v=$("#tipoperiodo>option:selected").text();
var x=$("#tipoperiodo>option:selected").val();
$("#descperiodo").val(v);

//alert("no esta vacio");
if ($("#descperiodo").val()!='') {


  $.post("ajax.php?c=Dispersion&f=cargardatosperiodo",{ 
    tipoperiodo:$("#tipoperiodo").val()
  },function(resp){
    $("#datosperiodo").html(resp);

    $('#tablanueva').DataTable({ 
      "language": {
        "url": "js/Spanish.json",
        "info": "No existen registros."},
        "lengthMenu": [ 5,10, 25, 50, 75, 100 ],
        "scrollX": true
      });   



    $(".check").attr('checked', false);

    $(".check").on('click',function(i, o) { 
      $(this).parents("tr").toggleClass("selected");
    });

    var tippago='';
    $('#tipopago').on('change', function(){ 
      tippago=$("#tipopago>option:selected").val();
      if (tippago==1) {
        $("#txtfechafin").prop('disabled', false);
      }else{
        $("#txtfechafin").prop('disabled', true);
      }
    });



    function replaceAll( text, busca, reemplaza ){
      while (text.toString().indexOf(busca) != -1)
        text = text.toString().replace(busca,reemplaza);
      return text;
    }
// replaceAll("123.345.567", ".", "" );
var suma=0;
$(".numbersOnly").keypress(function (e) {
//if the letter is not digit then display error and don't type anything
if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
//display error message
return false;
}
});

$('.check').on('change', function(){

  var table =  $("#tablanueva").DataTable();
  var data1 = table.rows('.selected').data().toArray();
  var cant = data1.length;
  var suma=0;
  var xx=0;
  for (var i = 0 ; i< data1.length; i++){
    suma = (data1[i][4]);  
    xx+=parseFloat(suma.replace(',',''));

  }
  $("#total").val(numeral(xx).format('$0,0.00'));

  y=parseFloat(suma.replace(',','').replace('.',''));
//console.log(y);

var sumatotal=$("#total").val().replace(',','').replace('.','').replace('$','');

$("#numchecks").val(cant);
$("#totalformt").val(sumatotal);

$("#cantEmpleSeleccion").val(cant);
})

returnFormat  = function(text, maxlength){ 
  var a = text;
  return  "0".repeat( maxlength - a.length) + a; 
}


/*TXT*/
function descargarArchivo(contenidoEnBlob, nombreArchivo) {
  emisora= returnFormat($('#emisora').val(), $('#emisora').attr('maxlength'));
  consec= returnFormat($('#consecutivo').val(), $('#consecutivo').attr('maxlength'));
  nombre ='NI'+emisora+consec;

  if ($("#emisora").val()!='' && $("#consecutivo").val()!='' && $("#tipoRegistro").val()!='' 
    && $("#claveServicio").val()!='' && $("#txtfecha").val()!='' && $("#cuentacargo").val()!='' && $("#tipopago").val()!=''  && $("#numchecks").val()!=0 ) {

    var reader = new FileReader();
  reader.onload = function (event) {
    var save = document.createElement('a');
    save.href = event.target.result;
    save.target = '_blank';
    save.download = nombre+'.pag';
    var clicEvent = new MouseEvent('click', {
      'view': window,
      'bubbles': true,
      'cancelable': true
    });
    save.dispatchEvent(clicEvent);
    (window.URL || window.webkitURL).revokeObjectURL(save.href);
  };
  reader.readAsDataURL(contenidoEnBlob);

}
}

//Función de ayuda: reúne los datos a exportar en un solo objeto
function obtenerDatos() {


  var tippago=$("#tipopago").val();
  var fechainicio=$("#fechainicio").val();

  var refeServi = " ".repeat(40);
  var refeLeyeOr = " ".repeat(40);
  var accion=" ".repeat(1);
  var fillerdos=" ".repeat(18);
  check=($("#numchecks").val());

// var str = $("#txtfecha").val();
// fecha=str.replace(/[^a-zA-Z 0-9.]+/g,'');

if (tippago==1) {
  var fechainicio=$("#fechainicio").val();
  $("#txtfecha").val(fechainicio);
  var fecha=fechainicio.replace(/[^a-zA-Z 0-9.]+/g,'');

}else{
  var str = $("#txtfecha").val();
  var fecha=str.replace(/[^a-zA-Z 0-9.]+/g,'');

}

if (tippago==1) {

  var fechafin=$("#txtfechafin").val();
   $("#txtfecha").val(fechafin);
  fechafin=fechafin.replace(/[^a-zA-Z 0-9.]+/g,'');
}else{
  var fechafin='00000000';
}

return {
  tipoRegistro: returnFormat($('#tipoRegistro').val(), $('#tipoRegistro').attr('maxlength')),
  claveServicio:returnFormat($('#claveServicio').val(), $('#claveServicio').attr('maxlength')),
  emisora:      returnFormat($('#emisora').val(), $('#emisora').attr('maxlength')),
  txtfecha:fecha,
  consecutivo:  returnFormat($('#consecutivo').val(), $('#consecutivo').attr('maxlength')),
  check:        returnFormat($('#numchecks').val(), $('#numchecks').attr('maxlength')),
  totalformt:   returnFormat($('#totalformt').val(), $('#totalformt').attr('maxlength')),
  naenviadas:   returnFormat($('#naenviadas').val(), $('#naenviadas').attr('maxlength')),
  iaenviadas:   returnFormat($('#iaenviadas').val(), $('#iaenviadas').attr('maxlength')),
  nbenviadas:   returnFormat($('#naenviadas').val(), $('#naenviadas').attr('maxlength')),
  ibenviadas:   returnFormat($('#iaenviadas').val(), $('#iaenviadas').attr('maxlength')),
  cuentaverif:  returnFormat($('#cuentaverif').val(), $('#cuentaverif').attr('maxlength')),
  tipopago:     returnFormat($('#tipopago').val(), $('#tipopago').attr('maxlength')),
  espacios:     returnFormat($('#espacios').val(), $('#espacios').attr('maxlength')),
  fechafin:fechafin,
  cuentacargo:     returnFormat($('#cuentacargo').val(), $('#cuentacargo').attr('maxlength')),
  filler:          returnFormat($('#filler').val(), $('#filler').attr('maxlength')),
  tiporegistro:    returnFormat($('#tiporegistro').val(), $('#tiporegistro').attr('maxlength')),
  txtfecha:fecha,
  refeServi:refeServi,
  refeLeyeOr:refeLeyeOr,
  tipMovim:returnFormat($('#tipMovim').val(), $('#tipMovim').attr('maxlength')),
  accion:accion,
  importIva:returnFormat($('#importIva').val(), $('#importIva').attr('maxlength')),
  fillerdos:fillerdos
};
};


//Genera un objeto Blob con los datos en un archivo TXT
function generarTexto(datos) {
  

  var texto=[];
texto.push(datos.tipoRegistro);     //Tipo de registro
texto.push(datos.claveServicio);    //clave de servicio
texto.push(datos.emisora);          //Emisora 
texto.push(datos.txtfecha);         //fecha de proceso
texto.push(datos.consecutivo);      //consecutivo
texto.push(datos.check);            //numero total de registros enviados
texto.push(datos.totalformt);       //importe total de enviados
texto.push(datos.naenviadas);       //numero total de altas enviadas
texto.push(datos.iaenviadas);       //importe total de altas enviadas
texto.push(datos.nbenviadas);       //numero total de bajas enviadas
texto.push(datos.ibenviadas);       //importe total de bajas enviadas
texto.push(datos.cuentaverif);      //numero total de cuentas a verificar
texto.push(datos.tipopago);         //accion
texto.push(datos.espacios);         //espacios
texto.push(datos.fechafin);         //fecha de adelanto de nomina
texto.push(datos.cuentacargo);      //cuenta cargo
texto.push(datos.filler);           //filler

var table =  $("#tablanueva").DataTable();
var data1 = table.rows('.selected').data().toArray();  
for (var i = 0 ; i< data1.length; i++){

texto.push('\n');
texto.push(datos.tiporegistro);               //tipo de registro
texto.push(datos.txtfecha);                   //fecha de aplicacion
texto.push(returnFormat(data1[i][2],10));     //numero de empleado 
texto.push(datos.refeServi);                  //referencia del servicio
texto.push(datos.refeLeyeOr);                 //referencia leyenda del ordenante
importenormal=replaceAll(data1[i][4],",","");
importeformat=replaceAll(importenormal,".","");
texto.push(returnFormat(importeformat,15));   //importe
texto.push(data1[i][5]);                      //numero del banco receptor
texto.push(returnFormat(data1[i][6],2));      //tipo de cuenta
texto.push(returnFormat(data1[i][7],18));     //numero de cuenta
texto.push(datos.tipMovim);                   //tipo de movimiento
texto.push(datos.accion);                     //accion
texto.push(datos.importIva);                  //importe iva de la operacion
texto.push(datos.fillerdos);                  //filler

}
// Agregamos nuestro valor al arreglo
return new Blob(texto, {
  type: 'text/plain'
});
}

document.getElementById('guardar').addEventListener('click', function () {
  var datos = obtenerDatos();
  descargarArchivo(generarTexto(datos), 'archivo.pag');
}, false);


}); 
}
});

$('#guardar').on('click',function(event) {
  if ($("#emisora").val()=='' || $("#consecutivo").val()==''  || $("#tipoRegistro").val()=='' 
    || $("#claveServicio").val()==''  || $("#txtfecha").val()==''  || $("#cuentacargo").val()==''  || $("#tipopago").val()==''  || $("#numchecks").val()==0 ) {
    alert("Llene todos los campos.");
}
if ($("#emisora").val()!='' && $("#consecutivo").val()!='' && $("#tipoRegistro").val()!='' 
  && $("#claveServicio").val()!='' && $("#txtfecha").val()!='' && $("#cuentacargo").val()!='' && $("#tipopago").val()!='' && $("#numchecks").val()!=0 ) {

  var table =  $("#tablanueva").DataTable();
var data1 = table.rows('.selected').data().toArray();

$.post("ajax.php?c=Dispersion&f=actualizaStatus",{ 
  empleId:$("#empleId").val(),
  nominadesc:$("#tipoperiodo>option:selected").val(),
  consecutivo:$("#consecutivo").val(),
  fechainicio:$("#fechainicio").val(),
  txtfecha:$("#txtfecha").val(),
  tipopago:$("#tipopago").val(),
  tableData : JSON.stringify(data1)

},function(resp){

  if(parseInt(resp) == 1){
    window.location.reload();
  
    $('#tipoperiodo').val('*');
    $('#total').val("0,0.00");
    $('#descperiodo').val('');
    //document.location.reload(true);
  }else{
    alert("Error en el procceso intente de nuevo");
  }
}); 
}
});
});


function accionEliminarDispersion(idEmpleado,idnomp){

  var confirma = confirm("¿Esta seguro que desea eliminar el concepto?");
  if (confirma == true) {

    $.post("ajax.php?c=Dispersion&f=accionEliminarDispersion",{
      idEmpleado:idEmpleado,       
      idnomp:idnomp

    },function(request){
      if(request == 1 ){
        alert("Datos eliminados.");
        window.location.reload();
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
