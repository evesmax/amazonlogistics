
function traerFechas(){	
// $("#selectPeriodo").on("change",function(){
//   console.log($("#selectPeriodo option:selected").attr("fechainicio"));
// });

var idnomina = $("#selectPeriodo option:selected").attr("idnomp");
var fechainicio = $("#selectPeriodo option:selected").attr("fechainicio");
var fechafin    = $("#selectPeriodo option:selected").attr("fechafin");
var autorizado  = $("#selectPeriodo option:selected").attr("autorizado");
var editable    = $("#selectPeriodo option:selected").attr("editable");
var periodosfuturos = $("#selectPeriodo option:selected").attr("periodosfuturos");
//console.log(idnomina + '-'+ fechainicio + '-' + fechafin + '-' + autorizado + '-' + editable + '-' +periodosfuturos);

var nomin=idnomina;
$("#nominaseleccionada").val(nomin); 

var table = $('#tablaincidencias').DataTable();
table.destroy();
var cantidadDias = 0;
$.post("../../modulos/nominas/ajax.php?c=registroincidencias&f=rangofechas&fi="+fechainicio +"&ff="+fechafin,//select llena tipo operacion
	function(data) 
	{
var arreglodias =  JSON.parse(data);// parsea los datos dentro de un arreglo 
cantidadDias = arreglodias.length;
$("#trHeader").html("");//encabezados para el rango de fechas
var htmltable   =  '<th><b>CÓDIGO EMPLEADO</b></th>' + '<th><b>NOMBRE EMPLEADO</b></th>';
for (var i=0; i< arreglodias.length; i++){
htmltable = htmltable + '<th><b>' + arreglodias[i] + '</b></th>';	//Me agrega todo el rango de fechas en el periodo seleccionado.
}
$("#trHeader").append(htmltable);   
$("#tdp").attr("colspan", cantidadDias+2);/*encabezado de periodos*/
//contenidoPrenomina(idnomina,fechainicio,fechafin, editable,periodosfuturos, cantidadDias); 
$("#contenidop").html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom" ></i>');
$.ajax({
	async:false,
	type: 'POST',
	url: 'ajax.php?c=registroincidencias&f=empleadosNomina',
	data: {fechaIni:fechainicio,
		fechaFin:fechafin,
		cantDias:cantidadDias,
		idnomp: idnomina, 
		editable: editable},
		success: function(request) {
			$("#").val("");
			$("#hdnfechainicio").val(fechainicio);
			$("#hdnfechafin").val(fechafin);
			$("#contenidop").html(request);
			$("#hdneditable").val(editable); 
//$("#hdneditable").val(editable);  
$("#periodo").html("PERIODO "+fechainicio+" - "+fechafin);
$('#tablaincidencias').DataTable( {
	"language": {
		"url": "js/Spanish.json"
	},
	"scrollX": true
});
}
});
});	
}

function irConfiguracion(){
	window.parent.agregatab('../../modulos/nominas/index.php?c=Catalogos&f=configuracion','Configuracion','',2257);
	window.parent.preguntar=true;
}



function eliminarincidencia(idempleado, idnomp, fecha){ 
	var borrar=confirm("¿Desea eliminar el movimiento del dia seleccionado?");
	if (borrar){
		$.post("ajax.php?c=registroincidencias&f=eliminarincidencia",
		{
			idempleado: idempleado,
			idnomp:     $("#nominaseleccionada").val(),
			fecha:      fecha
		},
		function (data){

			if (data==1) {

				alert("Se eliminó correctamente");
				contenidoPrenomina($("#nominaseleccionada").val(),$("#hdnfechainicio").val(),$("#hdnfechafin").val(),$("#hdneditable").val(),$("#hdperiodosfuturos").val());

			}	else{
				alert("Error al eliminar."); 
			}
		});
	}
}



function tablaincidencias(idempleado, idnomp, fecha){
	$("#tablainci").html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom" ></i>');
	$.post("ajax.php?c=registroincidencias&f=tablaincidencias",
//select llena tipo operacion
{
	clave:'clave',
	nomi:'nomi',
	nombcla:'nombcla',
	idtipoincidencia:'idtipoincidencia'
},
function(request){  
	$("#seleccion").val(""); 
	$("#fecha").val(fecha);
	$("#idempleado").val(idempleado);
	$("#idnomp").val(idnomp);
	$("#idtipoincidencia").val("idIncidencia");
	$("#valor").prop("disabled","disabled");
	$('#tablainci').html(request);
});			 
}

$('#fecha').on('change', function(e) {
	$(this).closest('#fecha').css('backgroundColor', 'rgba(255,102,51,0.15)');
});


//Para obtener el contenido que se despliega en la tabla con el id=contenidop
function contenidoPrenomina(idnomina,fechainicio,fechafin,editable,cantidadDias){
	var table = $('#tablaincidencias').DataTable();
	table.destroy();
	$("#contenidop").html('<i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>');	
	$.post("ajax.php?c=registroincidencias&f=empleadosNomina",{
		fechaIni:fechainicio,
		fechaFin:fechafin,
		cantDias:cantidadDias,
		idnomp:  $("#nominaseleccionada").val(),
		editable: editable

	},function (request){
		$("#").val("");
		$("#hdnfechainicio").val(fechainicio);
		$("#hdnfechafin").val(fechafin);
		$("#contenidop").html(request);
		$("#hdneditable").val(editable); 
		$("#hdneditable").val(editable);  
		$("#hdnsobrerecibo").val(0); 
		$("#hdnsobrerecibo").val(0);  
		$("#periodo").html("PERIODO "+fechainicio+" - "+fechafin);

		$('#tablaincidencias').DataTable( {
			"language": {
				"url": "js/Spanish.json"
			},
			"scrollX": true
		} );
	});
}

function adicional(idempleado){    
	$("#"+idempleado).bind("contextmenu", function(e){
		$("#menu").css({'display':'block', 'left':e.pageX, 'top':e.pageY});
		$("#menu li").val(idempleado);
		return false;
	});
}

//Los campos que se muestran en el modal, al momento de seleccionar una fila de la tabla del modal,
//hace que habilite el campo del valor, si cambio de seleccion de listado me borra el valor escrito, y me suma 
//el campo de seleccion con el valor que se inserta en el campo valor. 
function incidencia(clave, idTipoIncidencia){	
	var arr = [];
	i = 0;
	$('.trseleccionado').each(function()
	{
//alert($(this).attr('id'));
arr[i++] = $(this).attr('id');
});

	$("#idtipoincidencia").val(idtipoincidencia);
	$("#clave").val(clave); 
	$("#seleccion").val(''); 
	$('.trseleccionado').each(function()
	{ 
		$("#seleccion").val( $("#seleccion").val()+','+ $(this).find('.valor').val() + $(this).attr('id') );
	});
	$("#seleccion").val( $("#seleccion").val().substring(1, $("#seleccion").val().length));

}

function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		alert('Ingresa solo números.');
		return false;
	}
	return true;
}


$(document).on('click', ".limpiar", function(){
	$(this).closest('td').prev().children().remove();
});

function togglebackground(clave,idTipoIncidencia){ 
	if ($("#"+clave).hasClass('combinable')){
		var seleccionadosnocombinables =  $(".trseleccionado:not(.combinable)").length;
		var seleccionadoscombinables   =  $(".combinable.trseleccionado").length;
		if(seleccionadosnocombinables >0 ){  

			$("#seleccion").val('');
			$('.trseleccionado').removeClass('trseleccionado');	 
			$('.valor').prop('disabled','disabled');
			$('.valor').val('');
		}
	}
	else{ 
		$('.trseleccionado').removeClass('trseleccionado');	
		$('.valor').prop('disabled','disabled');
		$('.valor').val('');
	}
	if($("#"+clave).hasClass('trseleccionado')){
		$("#"+clave).removeClass('trseleccionado');
		$("#"+clave).find('.valor').prop('disabled','disabled');
		$("#"+clave).find('.valor').val('');
	}
	else{
		$("#"+clave).addClass('trseleccionado');  
		$("#"+clave).find('.valor').prop('disabled','');
		$("#"+clave).find('.valor').focus(); 
	}	
	incidencia(clave,idTipoIncidencia);
	$(".valor").on("keyup", function(e){ 
		incidencia(clave, idTipoIncidencia );
	});		
}


$(document).ready(function(){


	$('select').selectpicker();

	$('#fechadescanso').datetimepicker({

		format: 'YYYY-MM-DD',
		ignoreReadonly: true,
		useCurrent: false ,
		locale: 'es',
		minDate:$('#fini').val(),
		maxDate:$('#ffinal').val()

	});
// 
// 
//funciona para hacer movible el modal.
$("#myModal").draggable({
	handle: ".modal-header"
});

//mensajes al dar clic en las fechas de periodo agrega estilos rojo,verde,amarillo
$(".p_anterior").click(function(){ 
	alert('Has seleccionado un periodo que es anterior al periodo actual de trabajo y no puede ser editado.'); 

});

// $(".p_actual").click(function(){ 
// //alert('Ha seleccionado periodo actual.');
// });

/*Al click agrega lo que se indica en la clase de css y manda el alert.*/
$(".p_futuro").click(function(){ 

	alert('A seleccionado un periodo que es futuro al periodo actual.');
});

//cuando hagamos click, el menú desaparecerá
$(document).click(function(e){
	if(e.button == 0){
		$("#menu").css("display", "none");
	}
});

//si pulsamos escape, el menú desaparecerá
$(document).keydown(function(e){
	if(e.keyCode == 27){
		$("#menu").css("display", "none");
	}
});

//controlamos los botones del menú
$("#menu").click(function(e){
	switch(e.target.id){
		case "sobre":
		window.parent.preguntar=false;
		window.parent.quitartab("tb2297",2297,"Sobre-recibo");
		window.parent.agregatab('../../modulos/nominas/index.php?c=Sobrerecibo&f=sobrereciboview&inf='+e.target.value,'Sobre-recibo','',2297);
		window.parent.preguntar=true;
		break; 

		case "emple":
		window.parent.preguntar=false;
		window.parent.quitartab("tb2209",2209,"Empleados");
		window.parent.agregatab('../../modulos/nominas/index.php?c=Catalogos&f=empleadoview&editar='+e.target.value,'Empleados','',2209);
		Window.parent.preguntar=true;         
		break;

		case "eliminar":
		alert("eliminado!");
		break;
	}        
});


$(function() {

	$('#almacdiafest').on('click', function() {

		var btndiafest = $(this);
		btndiafest.button('loading'); 

		if(!$('#txtfecha').val()){

			alert("Debe seleccionar una fecha.");	
			btndiafest.button('reset'); 
		}
		else{
			$.post("ajax.php?c=registroincidencias&f=almacenaDiaFest",{
				txtfecha: $('#txtfecha').val(),
				idnomp  : $('#idnominaact').val()
			},function (resp){

				if(resp == 1){
					alert("Registro satisfactorio.");
					if ($("#nominaseleccionada").val()!=0) {
						contenidoPrenomina($("#nominaseleccionada").val(),$("#hdnfechainicio").val(),$("#hdnfechafin").val(),$("#hdneditable").val(),$("#hdperiodosfuturos").val()); 
					}
					$(".close").click();

				}else{
					alert("Ocurrió un error, intente de nuevo :(");
				}
				btndiafest.button('reset'); 
			}); 
		}
	});

//Hace la funcion del boton guardar/Aceptar
$('#load').on('click', function() {
	var table = $('#tablaincidencias').DataTable();
	table.destroy();
	var status = true;
	$('.trseleccionado').each(function() 
	{  
		if($(this).find('.valor').val() ==""){
			status= false;
		}
	});

	if(!status){
		alert("Falta campos obligatorios.");
		btnguardar.button('reset');
	} 	    
else{ //mandamos los valores para insertar en la tabla 
	var btnguardar = $(this);
	$(this).button('loading'); 
	var arr = [];
	$('.trseleccionado').each(function()
	{      
		arr.push({
//autorizado: $(this).attr('autorizado'),
clave: $(this).attr('id'),
valor:$(this).find('.valor').val(),
fecha: $("#fecha").val(),
tipoincidencia: $(this).find('.tipoIncidencia').val()
});
	});


	$.post("ajax.php?c=registroincidencias&f=almacenaincidencia",{  
		arreglo: arr,	           			  
		idempleado:$("#idempleado").val(),
		idnomp:$("#idnomp").val()

	},function (request){ 
		alert(request); 
//el llamado a contenidoPrenomina hace que se refresque la tabla del catálogo de incidencias
contenidoPrenomina($("#idnomp").val(), $("#hdnfechainicio").val(),$("#hdnfechafin").val(),$("#hdneditable").val()) ;
//cerramos la ventana
$("#btnCerrar").click(); 
btnguardar.button('reset');

}); 

	return true;
}

});
});	
});



 function cambiaperiodo(periodo){
 	$.post("ajax.php?c=Prenomina&f=cambiaPeriodo",{
		idtipop: periodo
		},function(resp){
			if(resp == 1){
				window.location.reload();
			}
		});
 }


