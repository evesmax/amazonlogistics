$(document).ready(function(){
	$("#btn_activar_prontipagos").click(function(){
		$.ajax({
			type: "POST",                                            
			url: "ajax.php?c=config_caja&f=activarProntipagos",
			dataType: 'json',
			data: { usuario: $("#prontipagos_usuario").val(), contrasena: $("#prontipagos_contrasena").val() },
			success: function(respuesta) {
				if(respuesta.status == true) {
					var html = "";
					$.each(respuesta.productos, function(index, producto){
						//if(producto.nombre.includes("TIEMPO AIRE")){
							html += "<option value='"+ producto.sku +"'>"+ producto.nombre + " | $" + producto.precio +"</option>";
						//}
					});
					$("#prontipagos_productos").html(html);
					$('.selectpicker').selectpicker('refresh');
				} else {
					alert(respuesta.mensaje);
				}
			},
			error: function() {                                     
				alert('Error al conectar con el servidor, porfavor intenta mas tarde.');
			}
		});
	});

	$("#btn_activar_productos_prontipagos").click(function(){
		$.ajax({
			type: "POST",                                            
			url: "ajax.php?c=config_caja&f=activarProductosProntipagos",
			dataType: 'json',
			data: { productos: $("#prontipagos_productos").val() },
			success: function(respuesta) {
				if(respuesta.status == true) {
					
				} else {
					alert("");
				}
			},
			error: function() {                                     
				alert('Error al conectar con el servidor, porfavor intenta mas tarde.');
			}
		});
	});

});


$('#tipoDescuento').on('change', function(e) {

	switch( $(this).val() ) {
		case "1":
			$('#limit-global').css('visibility', 'visible');
			$('#limit-unit').css('visibility', 'hidden');
			break;
		case "2":
			$('#limit-global').css('visibility', 'hidden');
			$('#limit-unit').css('visibility', 'visible');
			break;
		case "3":
			$('#limit-global').css('visibility', 'visible');
			$('#limit-unit').css('visibility', 'visible');
			break;
		default:
			alert("Se ha producido un error inesperado al procesar el tipo de descuento.");
	}
});

$('#save').on('click', function(e) {

	var datos = obtenerDatos(); 	
	console.log(datos);
	// alert(JSON.stringify(datos) );
	

	var comillas = false;
	var continuar = true;

	console.log(datos);
	$.each( datos, function( key, value ) {
		



		if(value === "" || value === null || value === undefined){

			if(key == "ticket" && continuar == true){
				continuar = true;
			}else {
				continuar = false;
			}
		}
		else {
			if(key == "password" || key == "ticket"){
				if(value.indexOf('\"') != -1 || value.indexOf('\'') != -1){
					alert(`No puedes ulilizar comillas en el campo de ${key}`);
					comillas = true;
				}
			}
		}
	});

	if(continuar) {
		if(!comillas){
			$.ajax({
				type: "POST",                                            
				url: "ajax.php?c=config_caja&f=actualiza",
				data: datos,                                        
				timeout: 2000,                                          
				dataType: 'json',
				success: function(data) {

					console.log(data);
					if(data != null) {
						alert("Actualizado exitosamente!");
						validaciondeObligatorios();
					}
					else{
						alert("Error en el servidor!");
					}
				},
				error: function() {                                     
					alert('Error al conectar con el servidor, porfavor intenta mas tarde.');
				}
			});
		}
	}
	else {
		validaciondeObligatorios();
		alert("Verifica no haber dejado campos incorrectos o vacios");
	}

});

function obtenerDatos() {
	

	/*var printAuto = 0;

        if($("#corte_1").is(':checked')) {  
             printAuto = 1; 
        } else {  
            printAuto = 0; 
        } */

	var datos = { };
	datos.tipoDescuento = validarMonto( $('#tipoDescuento').val() , 3) ? $('#tipoDescuento').val() : (alert("Tipo de descuento inválido"));
	switch( datos.tipoDescuento ) {
		case "1":
			datos.limitGlobalCantidad =  $('#limit-global .cantidad').val();
			datos.limitGlobalPorcentaje = validarMonto( $('#limit-global .porcentaje').val() , 100 ) ? $('#limit-global .porcentaje').val() : (alert("Porcentaje de descuento global inválido"));
			datos.limitUnitCantidad = "0";
			datos.limitUnitPorcentaje = "0";
			break;
		case "2":
			datos.limitUnitCantidad = $('#limit-unit .cantidad').val();
			datos.limitUnitPorcentaje = validarMonto( $('#limit-unit .porcentaje').val() , 100) ? $('#limit-unit .porcentaje').val() : (alert("Porcentaje de descuento por producto inválido"));
			datos.limitGlobalCantidad =  "0";
			datos.limitGlobalPorcentaje = "0";
			break;
		case "3":
			datos.limitUnitCantidad = $('#limit-unit .cantidad').val();
			datos.limitUnitPorcentaje = validarMonto( $('#limit-unit .porcentaje').val() , 100 ) ? $('#limit-unit .porcentaje').val() : (alert("Porcentaje de descuento por producto inválido"));
			datos.limitGlobalCantidad = $('#limit-global .cantidad').val();
			datos.limitGlobalPorcentaje = validarMonto( $('#limit-global .porcentaje').val() , 100) ? $('#limit-global .porcentaje').val() : (alert("Porcentaje de descuento global inválido"));
			break;
		default:
			alert("Se ha producido un error inesperado, tu registro podría ser incorrecto.");
	}
	datos.password = validarPassword ( $('#password').val(), $('#confirm-password').val() ) 
		? $('#password').val() 
		: (function() {alert("La confirmación de contraseña es incorrecta"); return "";})();
	datos.cajaMax = $('#caja-max').val();
	datos.retitoMax = $('#retiro-max').val();
	datos.ticket = $('#ticket').val();
	datos.ticket = datos.ticket.replace('\n', ' ');
	datos.precio_unit_ticket = $('#verPrecioUnitario').val();
	datos.cotizacionDescuento = $('#cotizacion-descuento').is(':checked');
	datos.ordenVentaDescuento = $('#orden-venta-descuento').is(':checked');
	datos.printAuto = $('#impresionAutomatica').val();
	datos.puntos = $('#verTarjetas').val();
	datos.activarDevCan = $('#activarDevCan').val();
	datos.activarRetiroDevCan = $('#activarRetiroDevCan').val();
	datos.modifica_precios = $('#activaPrecio').val();
	datos.moduloPrint = $('#moduloPrint').val();
	datos.moduloPin=$('#moduloPin').val();
	datos.activaAntibioticos = $('#activaAntibioticos').val();
	datos.cortesP = $('#cortesP').val();
	datos.moduloTipoPrint = $('#moduloTipoPrint').val();
	datos.limiteMontoCaja = $('#limit-caja .cantidad').val();
	
	datos.sitrack =  $('#sitrack2').val()!='' ? $('#sitrack2').val(): " ";
	datos.sitpass =  $('#sitrack2').val()== 1 ? $('#sitpass').val(): " "; 
	datos.situser =  $('#sitrack2').val()== 1 ? $('#situser').val(): " "; 

	// AM cotizaciones
	datos.formato_cotiza  = $('input:radio[name=formato_cotiza]:checked').val();
	datos.termCondic      = $('#termCondic').val()!='' ? $('#termCondic').val(): " ";

				
	// AM MODULOS COMPLEMENTOS para la ruta del peso de la bascula
	datos.direcBascula    = $('#direcBascula').val()!='' ? $('#direcBascula').val(): " ";
	
	return datos;
}

function validarMonto(cantidad, maximo) {
	return (cantidad <= maximo);
}

function validarPassword(pass, confirm) {
	return (pass != "") && (pass === confirm);
}


// Valido los campos obligatorios, no permite guardar sin estos AM
function validaciondeObligatorios(){

	if ($('#password').val()=='' ||  $('#confirm-password').val()==''){
		$( "#password" ).addClass( "camposobligatorios" );
		$( "#confirm-password" ).addClass( "camposobligatorios" );


	}else{
		$( "#password" ).removeClass( "camposobligatorios" );
		$( "#confirm-password" ).removeClass( "camposobligatorios" );

	}
	
	ActivarGPS();
	
}

$('#sitrack2').on('change', function(e) {
	ActivarGPS();
});

function ActivarGPS(){

	if ($('#sitrack2').val()==1 && ($('#sitpass').val()=='' ||  $('#situser').val()=='')) {
	
		$('#sitpass').addClass( "camposobligatorios" );
		$('#situser').addClass( "camposobligatorios" );

	}else{
		$('#sitpass').removeClass( "camposobligatorios" );
		$('#situser').removeClass( "camposobligatorios" );

		
	}
}
