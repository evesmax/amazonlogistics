
$(document).ready(function(){

$('#empleados').multiselect({
 enableCaseInsensitiveFiltering: true,
 nonSelectedText: 'Seleccione',
 buttonWidth: '100%'
 });


$('#horariosalta').dataTable( {

"language": {
"url": "js/Spanish.json"
},
"columns": [
{ "width": "80%" },
{ "width": "20%" }
],
"bLengthChange" : false,
"info": false
// "bPaginate": false
});


$('#tablahoras').dataTable( {
"language": {
"url": "js/Spanish.json"
},
"bLengthChange" : false,
"bPaginate": false,
"bSort": false,
"info": false
// "columns": [
// null,
// null,
// // { "width": "8%" },
// { "width": "10%" },
// { "width": "10%" },
// { "width": "25%" },
// { "width": "25%" },
// { "width": "10%" },
// null,
// null,
// { "width": "10%" }
// ]
});


$('.onlynumb').keypress(function(e) {
var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9:]/);
if (verified) {e.preventDefault();}
});
});

function newhorario(){
window.location="index.php?c=Catalogos&f=nuevohorario";
}

function atraslistado(){
window.location ="index.php?c=Catalogos&f=horarios";
}


$(function() {

	  $('#departamento').on('change', function(){
     valip = $(this).val(); 

     $.ajax({
      url:"ajax.php?c=Catalogos&f=cargaDepartament",
      type: 'POST',
      dataType:'json',
      data:{
        idDep: $(this).val() 
      },
      success: function(r){
        if(valip=='*'){
        	// alert("todos");
           option='';
        }else{
         option='';
       }
       if(r.success==1 ){

        
         $.each(r.data, function( k, v ) {  
         	option+='<option value="'+v.idEmpleado+'">'+v.apellidoPaterno+' '+v.apellidoMaterno+' '+v.nombreEmpleado+'</option>';
         });

       }else{
        option+='<option value="">No hay empleados.</option>';         
      }
 			
 			$('#empleados').html(option);
			$('#empleados').multiselect('destroy');
      $("#empleados").prop('disabled',true);
      $("#empleados").attr('disabled',false);
      
		
				$('#empleados').multiselect({
				enableCaseInsensitiveFiltering: true,
				nonSelectedText: 'Seleccione',
				allSelectedText: 'Todos los empleados seleccionados.',
				includeSelectAllOption: true,
				selectAllText: 'Todos',
				selectText: 'd',
				filterPlaceholder: 'Buscar',
				enableFiltering:true,
				dropRight: true,
				maxHeight: 250,
				buttonWidth: '100%'
	});


}
});
});

var daysel="";
var x=""; 
$('#almacenhrs').on('click', function() {

var btbhorsempl = $(this);
// btbhorsempl.button('loading'); 
 
var selectedLanguage = new Array();
$('input[name="dia"]:checked').each(function() {
selectedLanguage.push(this.value);
  console.log(selectedLanguage);
  $("#diaguardar").val(selectedLanguage);
  
  });
for(var i=0;i<selectedLanguage.length;i++){
  // alert(selectedLanguage[i]);
    x=selectedLanguage[i];
      
if($(".entrada"+"_"+x).val()=='' || $(".salida"+"_"+x).val()=='' || $(".radiosel"+"_"+x).val()=='' || $(".radioseld2"+"_"+x).val()=='' || $(".radioseld3"+"_"+x).val()==''){
    alert("Llene los datos del dia: "+x); 
    return false;
    btbhorsempl.button('reset'); 
     }
}

    if ($("#nombrehorario").val()=='' || $("#tolerancia").val()=='') {
alert("Llene campos obligatorios.");
      btbhorsempl.button('reset'); 
}
else if ($("#diaguardar").val()=='' && $("#funcion").val()=='2' ) {
alert("Seleccione un día para editar.");
btbhorsempl.button('reset'); 
}
else if ($("#diaguardar").val()=='') {
alert("Seleccione un día para agregar.");
btbhorsempl.button('reset'); 
}

else{

// var btbhorsempl = $(this);
btbhorsempl.button('loading'); 

var data = []; 
$("#tablahoras").find('.selected').each(function(){
var inputs = $(this).find(".tabledata").toArray();

var str = $(this).find(".dia").attr("value");
var dia = str.slice(0, 3);
console.log(dia);

data.push({
Dia: dia,
HoraEntrada:  $(inputs[0]).val(),
HoraSalida :  $(inputs[1]).val(),
Come:         $(inputs[5]).val(),
ChecaComida:  $(inputs[8]).val(),
Desde:  	    $(inputs[9]).val(),
Hasta:        $(inputs[10]).val(),
MinComida:    $(inputs[11]).val(),
opcional:     $(inputs[14]).val()
}); 
}); 

$.post("ajax.php?c=Catalogos&f=almacenahorariosemp",{
tolerancia:$('#tolerancia').val(),
nombrehorario:$('#nombrehorario').val(),
tableData : JSON.stringify(data),
opc:$('#funcion').val(),
idhorario:$('#idhorario').val()

},function (resp){

if(resp == 1){
  
alert("Registro satisfactorio.");
location.reload();

}
else{
alert("Ocurrió un error, intente de nuevo.");
}
btbhorsempl.button('reset'); 
}); 

 }
});



$('#empleados').change(function() {
var selectedValues = $(this).val();
$('#emplesele').val(selectedValues);
});


$('#asignarhrs').on('click', function() {
var btbhors = $(this);
btbhors.button('loading'); 

if($('#horario').val()=="" || $('#departamento').val()=="no" || $('#emplesele').val()=="" ){
$('#emplesele').val($('#empleados').val());

alert("Debe llenar todos los campos.");
btbhors.button('reset'); 
}else{

var arrayEmp = new Array();
$('#empleados :selected').each(function(i, selected) {
arrayEmp[i] = $(selected).val();
});
$.post("ajax.php?c=Catalogos&f=asignahorariosemple",{
empleados: arrayEmp,
horario:$('#horario').val(),
departamento:$('#departamento').val()

},function (resp){

if(resp >= 1){
alert("Registro satisfactorio.");
location.reload();

}else{
alert("Ocurrió un error, intente de nuevo.");
}
btbhors.button('reset'); 
}); 
}
});
});



function accionEliminarHorario(idhorario){

var confirma = confirm("¿Esta seguro que desea eliminar el horario?");
if (confirma == true) {

$.post("ajax.php?c=Catalogos&f=accionEliminarHorario",{
idhorario:idhorario

},function(request){
if(request == 1 ){
alert("horario eliminado.");
window.location.reload();
}
else if (request == 2) {
alert("No se puede eliminar, horario asignado a empleados.");

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



