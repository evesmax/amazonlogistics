
var ciudad = "";
$(document).ready(function(){

  // $.post("ajax.php?c=CatalogosRfc&f=rfcEmpleados",
  // // url:"/PTSC/ValidaRFC/consultaMasiva.jsf"
  // // data:uploadFile,
  // {
  // },function(resp){
  //
  // var contenidoDeArchivo = resp;
  // var elem = document.getElementById('descargar');
  // elem.download = "archivo.txt";
  // elem.href = "data:application/octet-stream,"  + encodeURIComponent(contenidoDeArchivo);
  //
  // });
  //
  //
  //
  // //function empleadosGuardar(){
  // // var datosfrm = new FormData(document.getElementById('formempleados'));
  // $.ajax({
  // type:"POST",
  // url:"https://agsc.siat.sat.gob.mx/PTSC/ValidaRFC/ServletProcesaArchivo",
  // data: uploadFile,
  // //  processData: false,
  // contentType: false,
  // success: function(respuesta){
  //
  // alert(respuesta);
  // // var contenidoDeArchivo = resp;
  // //     var elem = document.getElementById('descargar');
  // //     elem.download = "archivo.txt";
  // //     elem.href = "data:application/octet-stream,"  + encodeURIComponent(contenidoDeArchivo);
  //
  // // window.location = 'index.php?c=Catalogos&f=listaEmpleados';
  // },
  // // error: function(error){
  // // }
  // });

  //  }
  DerPatro();
  HabilitaRegisPatro();

  //PERMITE INGRESAR SOLO CARACTERES DE TIPO NUMERICO
  $('.solo-numero').keypress(function(e) {
    var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9]/);
    if (verified) {e.preventDefault();}
  });

  $.datepicker.setDefaults($.datepicker.regional['es-MX']);
  $("#fechaalta,#fechavariable,#fechadiario,#fechapromedio,#fechaintegrado").datepicker({
    dateFormat: 'yy-mm-dd',
    useMinutes:  false,
    useSeconds:  false,
    changeMonth: true,
    changeYear:  true
  });
  $("#nacimiento").datepicker({dateFormat: 'yy-mm-dd',
  useMinutes:  false,
  useSeconds:  false,
  changeMonth: true,
  changeYear:  true,
  yearRange: '1920:2020'});



});


function irHorariosEmple(){
window.parent.agregatab('../../modulos/nominas/index.php?c=Catalogos&f=horariosempleados','Horario Empleado','',2442);
window.parent.preguntar=true;
 }


function DerPatro(){

  if  ($("#registropatronal").val()==0) {
    //alert($("#registropatronal").val());
    $("#nss").prop('disabled', true);
    //  $("#puesto").attr('disabled', true);
    $("#sbcfija").prop('disabled', true);

  }else  {

    $("#nss").prop('disabled', false);
    //$("#puesto").prop('disabled', false);
    $("#sbcfija").prop('disabled', false);
  }
}

function HabilitaRegisPatro(){
  if ($("#contrato").val()>=0 && $("#contrato").val()<=8) {
    //alert($("#contrato").val());
    $("#registropatronal").prop('disabled', false);

  }else{
    $("#registropatronal").prop('disabled', true);

  }

}

function ciudadbusca(idestado){
  $.post("ajax.php?c=Catalogos&f=municipios",{
    idestado:$("#entidad").val()
  },function(resp){
    $("#ciudad").html(resp).selectpicker("refresh");
    $("#ciudad").val(ciudad).selectpicker("refresh");
  });
}
$(function() {


  jQuery('#seleccionarImagen').on('change', function(e) {
    var Lector,
    oFileInput = this;

    if (oFileInput.files.length === 0) {
      return;
    };

    Lector = new FileReader();
    Lector.onloadend = function(e) {
      jQuery('#vistaPrevia').attr('src', e.target.result);
    };
    Lector.readAsDataURL(oFileInput.files[0]);

  });

  $('#load').on('click', function() {

    var btnguardar = $(this);
    btnguardar.button("loading");
    var status = true;
    var nss=$("#nss").val();

    if($("#valida").is(':checked')) {

      if(!$("#codigo").val() || !$("#fechaalta").val() || !$("#nombre").val() || !$("#salario").val() || !$("#curp").val() || !$("#nacimiento").val() || $("#turnotrabajo").val()==0  )
      {

        status = false;
        alert("Faltan campos obligatorios.");
        //$(this).button('reset');
        btnguardar.button('reset');
      }

       else if($("#vistaPrevia").attr("src") == "images/default.jpeg" || $("#valordefoto").val()==0) {

        status=false;
        alert("Seleccione una fotografía valida.");
        btnguardar.button('reset');
      }
      if($("#interbancaria").val()) {
      	if (($("#interbancaria").val().length<10 || $("#interbancaria").val().length>11) && ($("#interbancaria").val().length<16 || $("#interbancaria").val().length==17 || $("#interbancaria").val().length>18)){
	        status=false;
	        alert('Clabe incorrecta, debe contener entre 10, 11, 16 ó 18 digitos.');
	        btnguardar.button('reset');
      	}
      }
       else if($("#banco").val()=='18' && $("#numcuenta").val().length<10){
         status=false;
         alert("Número de cuenta para pago debe contener 10 digitos.");
         btnguardar.button('reset');
      }

      else if (($("#nominas").val()==1)  && ($("#periodo").val()==0 )) {
        status = false;
        alert("Selecciona un tipo de periodo ");
        btnguardar.button('reset');
       }
      // }else if (($("#nominas").val()==1) && ($("#NominasManual").val()==0) && ($("#periodo").val()==0 )) {
      	// status = false;
        // alert("Selecciona un tipo de periodo");
        // btnguardar.button('reset');
      // }
      // }else if (($("#nominas").val()==0) && ($("#NominasManual").val()==1) && ($("#periodo").val()==0 )){
      	// status = true;
      // }

      else if ( ($("#nominas").val()==1 || ($("#NominasManual").val()==1)) && ($("#registropatronal").val()!=0) ) {

       // if(!$("#nss").val() || !$("#sbcfija").val() || $("#puesto").val()==0)
        if(!$("#nss").val() || !$("#sbcfija").val() )

        {
          status = false;
          alert("Faltan campos Obligatorios.");
          btnguardar.button('reset');
        }

        if($("#sbcfija").val() <= 0){
          status = false;
          alert('SBC Parte Fija incorrecta.');
          btnguardar.button('reset');

        }

        else if (!nss.match(/^(\d{2})(\d{2})(\d{2})\d{5}$/)) {
          status=false;
          alert("El número NSS no es correcto.");
          btnguardar.button('reset');
        }
      }

      else if ($("#contrato").val()>=0 && $("#contrato").val()<=8){
        if($("#registropatronal").val()==0){
          status = false;
          alert("Seleccione un Registro Patronal.");
          btnguardar.button('reset');

        }

      }
    } else {
      var clasif = $("#clasificacionapp").val();
      if(!$("#codigo").val() || !$("#nombre").val())
      {
        status = false;
        alert("¡Debe escribir codigo y nombre!");
        if(clasif == 0){
          alert("Debe seleccionar una clasificacion [Appministra]");
          btnguardar.button('reset');
          return false;
        }
        btnguardar.button('reset');
      }
    }

    var strCorrecta = $("#rfc").val();
    if ($("#rfc").val().length == 12){
      var valid = '^[A-Z,a-z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,a-z,0-9]?[A-Z,a-z,0-9]?[0-9,A-Z,a-z]?';
    }
    else{
      var valid = '^(([A-Z]|[a-z]|\s){1})(([A-Z]|[a-z]){3})([0-9]{6})((([A-Z]|[a-z]|[0-9]){3}))';
    }

    var validRfc=new RegExp(valid);
    var curp=$("#curp").val();//toma el valor que se ingreso en el campo de CURP
    var numDec=$("#salario").val();
    var matchArray=strCorrecta.match(validRfc);

    if($("#valida").is(':checked')) {
      if (matchArray==null) {
        status=false;
        alert('El RFC insertado no es valido.');
        btnguardar.button('reset');
        //$(this).button('reset');
        return false;
      }

      else if(!curp.match(/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/
)){//expresion regular para validar CURP
        status=false;
        alert('curp incorrecta!');
        //$(this).button('reset');
        btnguardar.button('reset');
      }

      else{

        if(status){
          empleadosGuardar();
          //$("#formempleados").submit();
        }
        return true;
      }

    }else{

      if(status){
        empleadosGuardar();
        //$("#formempleados").submit();
      }
      return true;
    }

  });

$.ajax({

  url:"ajax.php?c=Catalogos&f=llenarhistorico",
  type: 'POST',
  dataType:'json',
  data:{

    empleado:$("#idempleado").val()
  },
  success: function(r){

    if(r.success==1){

      $('.tablahistorico').DataTable( {
        "destroy": true,
        "bAutoWidth": false,
        //"ordering": false,
        "lengthChange": false,
        "language": {
          "url": "js/Spanish.json"
        },

        "data": r.data,
        "columns": [
        {"data":"fecha"},
        {"data":"descripcionEstatus"}
        ]
      });

    }else{


     $('.tablahistorico').DataTable( {
        "destroy": true,
        "bAutoWidth": false,
        "lengthChange": false,
        "language": {
          "url": "js/Spanish.json"
        },
        "columns": [
        null,
        null
        ]
      });
      //table.clear().draw();
    }
  }
});
});

//VALIDA SOLO CARACTERES EN NÚMEROS CON DOS DECIMALES
function NumDec(e, field) {

  key = e.keyCode ? e.keyCode : e.which;
  //alert(key);
  // backspace , tab, left, right, delete
  if (key == 8 || key ==9 || key ==37 || key ==39 || key ==127)  return true;

  if ((key > 47 && key < 58)  || key == 46) {
    //if no text
    if (field.value == "" && key != 46) return true;
    var  regexp = /^[0-9]+((\.)|(\.[0-9]{1,2}))?$/;
    return (regexp.test(field.value + String.fromCharCode(key)));

  }

  return false;

}


function atraslistado(){
  window.location = "index.php?c=Catalogos&f=listaEmpleados";
}

function accionEmpleado(idempleado,accion,idtipop){

  var d = new Date();
  var fecha = d.getDate();

  var confi = alerta = '';
  if( accion == 2){
    confi = "¿Esta seguro de dar de baja al empleado?";
    alerta = "dado de baja";
  }
  else if( accion == 3){
    confi = "Antes de reingresar al empleado asegurese de imprimir\n toda la informacion referente a la liquidacion del empleado\nDesea continuar con el reingreso del empleado?";
    alerta = "reingresado";
  }
  if(confirm (confi)){
    modalbaja(idempleado, accion,idtipop);
    $('#myModal').find('.modal-title').html("El empleado será " + alerta +  " con esta fecha, ¿Desea continuar?")
    $('#myModal').modal('show');
  }
}

function empleadosGuardar(){
  var datosfrm = new FormData(document.getElementById('formempleados'));
  $.ajax({
    type: "POST",
    url: url_empleados,
    data: datosfrm,
    processData: false,
    contentType: false,
    success: function(respuesta){
      alert(respuesta);
      window.location = 'index.php?c=Catalogos&f=listaEmpleados';

    },
    error: function(error){
    }
  });

}
function newEmpleado(){
  window.location="index.php?c=Catalogos&f=empleadoview";
}
function calculosdi(){
  $.post("ajax.php?c=Catalogos&f=SDI",{
    idempleado: $("#idempleado").val(),
    fechaalta:$("#fechaalta").val(),
    tipoempleado: $("#tipoempleado").val(),
    salariodiario: $("#salario").val()
  },function(resp){
    $("#sbcfija").val(resp);
  });
}




function borraHuella(){
  if ($("#noHuella").val()=='') {
    alert("Seleccione una huella.");
  }else{

    $.post("ajax.php?c=Catalogos&f=eliminaHuella",{
      idempleado: $("#idempleado").val(),
      noHuella: $("#noHuella>option:selected").val()
    },function(resp){

      if(resp == 1){
        alert("Huella eliminada!");
        $('#noHuella').selectpicker('refresh');
        $('#noHuella').val('');
        window.location = 'index.php?c=Catalogos&f=listaEmpleados';

      }else{
        alert("No se pudo eliminar la huella. Intente de nuevo");
    }
});
  }
}




function abrirEmpleSobre(idempleado){

  window.parent.preguntar=false;
  window.parent.quitartab("tb2297",2297,"Sobre-recibo");
  window.parent.agregatab('../../modulos/nominas/index.php?c=Sobrerecibo&f=sobrereciboview&inf='+idempleado,'Sobre-recibo','',2297);
  window.parent.preguntar=true;
}
