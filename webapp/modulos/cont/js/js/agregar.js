
 $(function()
 {
	$.ui.dialog.prototype._allowInteraction = function(e) {
		return !!$(e.target).closest('.ui-dialog, .ui-datepicker, .select2-drop').length;
};
$('#numpolload').hide();
$('#facturaSelect').change(function () {
	var facsel = $('#facturaSelect').val();
	if(facsel != '-')
	{

	facsel = facsel.split('_');
	facsel = facsel[2].split('.');
    $('#referencia_mov').val(facsel[0]);
	}
	else
	{
		$('#referencia_mov').val(facsel);	
	}
  });

$( '#fac' )
  .submit( function( e ) {
  	$('#verif').css('display','inline');
    $.ajax( {
      url: 'ajax.php?c=CaptPolizas&f=subeFactura',
      type: 'POST',
      data: new FormData( this ),
      processData: false,
      contentType: false
    } ).done(function( data1 ) {
    	//$("#Facturas").dialog('refresh')
    		$.post("ajax.php?c=CaptPolizas&f=facturas_dialog",
		 	{
				IdPoliza: $('#idpoliza').val()
			},
			function(data2)
			{
				
			 	$('#listaFacturas').html(data2)
				
			});
			$('#factura').val('')
			actualizaListaFac();
			data1 = data1.split('-/-*');
			$('#verif').css('display','none');
			if(parseInt(data1[2]))
			{
				alert('Los siguientes archivos no son validos: \n'+data1[3])
			}

			if(parseInt(data1[0]))
			{
				alert('Archivos Validados: \n'+data1[1])
			}
    
  	});
    e.preventDefault();
  } );

	actualizaListaMov();
	dias_periodo();
	$('#cargando-mensaje').css('display','none');
	//---------------------------------------------------- Comienza Habilita y desabilita proveedores ----------------************
	if($('#tipoPoliza').val() == 1)
	{
	 //$('#botonProveedores').attr('disabled','disabled');
	 $('#botonProveedores').css('display','none');
	 $('#botonClientes').css('display','inline');
	}
	
	if($('#tipoPoliza').val() == 2)
	{
		//$('#botonProveedores').removeAttr('disabled');
		$('#botonProveedores').css('display','inline');
		$('#botonClientes').css('display','none');
	}

	if($('#tipoPoliza').val() == 3)
	{
		//$('#botonProveedores').removeAttr('disabled');
		$('#botonProveedores').css('display','inline');
		$('#botonClientes').css('display','none');
	}


	$('#tipoPoliza').change(function()
	{
		if($('#tipoPoliza').val() == 1)
		{
	 		//$('#botonProveedores').attr('disabled','disabled');
	 		$('#botonProveedores').css('display','none');
	 		$('#botonClientes').css('display','inline');
	 		//alert('Ingresos')
		}
		else
		{
			$('#botonProveedores').css('display','inline');
			$('#botonClientes').css('display','none');
			//alert('Otros')
		}
		$('#numpol').hide();
		$('#numpolload').show();
		$.post("ajax.php?c=CaptPolizas&f=UltimoNumPol",
 		 {
    		Periodo: $("#periodos").val(),
    		Ejercicio: $("#IdExercise").val(),
    		TipoPol: $("#tipoPoliza").val()
  		 },
  		 function(data)
  		 {
  		 	//alert("el ultimo es: "+data)
  			 var numtipo = $("#numtipo").val().split('-')
			if($('#tipoPoliza').val() == numtipo[0])
			{
				$("#numpol").val(numtipo[1])
			}
			else
			{
				$('#numpol').val(data);
			}
  		 	$('#numpolload').hide();
  		 	$('#numpol').show();
  		 });


	
	});
	//---------------------------------------------------- Termina Habilita y desabilita proveedores ----------------************
$('#numpol').change(function () 
{
$.post("ajax.php?c=CaptPolizas&f=ExisteNumPol",
        {
            Periodo:    $("#periodos").val(),
            Ejercicio:  $("#IdExercise").val(),
            TipoPol:    $("#tipoPoliza").val(),
            NumPol:     $("#numpol").val(),
            Id:         $('#idpoliza').val()
        },
        function(existe)
        {
          //alert('existe: '+existe)
            if(parseInt(existe))
            {
              	alert("El numero de poliza ya existe"); 
              	$("#guardarpolizaboton2").attr('disabled',true)
            }
            else
            {
            	$("#guardarpolizaboton2").removeAttr('disabled')
            }
        });
});
	 $("#cuenta").select2({
				 width : "150px"
				});

	 $("#ProveedoresSelect").select2({
				 width : "150px"
				});

	 $("#capturaMovimiento").dialog({
			autoOpen: false,
			width: 600,
			height: 420,
			modal: true,
			show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
		buttons: 
		{
			"Agregar Movimiento": function () 
		 {
			var todos = 0; 
			if($('#cuenta').val() == "" || $('#cuenta').val() == "0.0.0.0")
			{
				todos += 1;
			}

			if($('#concepto_mov').val() == "")
			{
				todos += 1;
			}

			if(($('#abono').val() == "" && $('#cargo').val() == "") || ($('#abono').val() == "0.00" && $('#cargo').val() == "0.00") || ($('#abono').val() == 0 && $('#cargo').val() == 0))
			{
				todos += 1;
			}


			 if(todos==0)
			 {
				 agregarMov();
				 //setInterval(function(){actualizaListaMov()},1000);
				 //actualizaListaMov();
				 $('#cuenta').val('');
				 //$('#referencia_mov').val('');
				 //$('#concepto_mov').val($('#concepto').val());
				 $('#abono').val('0.00');
				 $('#cargo').val('0.00');
				 $("#abono").removeAttr("readonly");
				 $("#cargo").removeAttr("readonly");
			}else
			{
				alert("No se puede guardar el registro, revise si la informacion es correcta. ")
			}
			 
		 }
		}
		});
	$('#agregar').click(function(){
		actuali();
$('#capturaMovimiento').dialog({position:['center',200]});
		$('#capturaMovimiento').dialog('open');
		$('button').attr('disabled','disabled');
		$('#cuenta').prop('disabled', 'disabled');
		$.post("ajax.php?c=CaptPolizas&f=UltimoMov",
		 {
				IdPoliza: $('#idpoliza').val(),
			 },
			 function(data)
			 {
				if(data)
				{
					
					$("#movto").val(parseInt(data)+1);
				}else
				{
					$("#movto").val('1');
				}
				$('button').removeAttr('disabled');
				$('#cuenta').prop('disabled', false);
buscacuentaext($('#cuenta').val());
				
			 });
			 $('#referencia_mov').val('');
			 $("#movto").removeAttr('idmov');
			 $('#concepto_mov').val($('#concepto').val());
			 $('#abono').val('0.00');
			 $('#cargo').val('0.00');
			 $('#movto').val(parseInt($('#movto').val())+1);	
			 $("#idr").val('1');
			 $("#abono").removeAttr("readonly");
			 $("#cargo").removeAttr("readonly");
			 $("#sucursal option[value='1']").attr("selected","selected");  
			 $("#segmento option[value='1']").attr("selected","selected");  
			 $("#facturaSelect option[value='-']").attr("selected","selected");  
	});
	$('body').bind("keyup", function(evt){
    if (event.ctrlKey==1)
    {
     	if(evt.keyCode == 13)
      	{
        	$('#guardarpolizaboton').click();
        	$('#actualizarboton').click();
        	$('#nuevapolizaboton').click();
      	}
      	if(evt.keyCode == 88)
    	{
      		$('#cancelarpolizaboton').click();
    	}
    	if(evt.keyCode == 73 && $('#tipoPoliza').val() != 1)
    	{
      		$('#botonProveedores').click();
    	}
    	if(evt.keyCode == 73 && $('#tipoPoliza').val() == 1)
    	{
      		$('#botonClientes').click();
    	}
    	if(evt.keyCode == 77)
      	{
      		$('#agregar').click();
      	}

      	if(evt.keyCode == 75)
      	{
      		$('#asignar_facturas').click();
      	}
    }

    if (event.altKey==1)
    {
    	if(evt.keyCode == 38)
      	{
      		$('#CuadreAgregar').click();
      	}
    }
  });
//--------------------------------Comienza Proveedores----------------------***
	$("#ProveedoresLista").dialog(
	 {
			 autoOpen: false,
			 width: 700,
			 height: 400,
			 modal: true,
			 show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
			 buttons: 
			{
				"Nuevo": function () 
				{
					abreProveedores(0,0);
				},
				"Cerrar": function () 
				{
					 $("#ProveedoresLista").dialog('close')
				}
			}
		});
	$("#Proveedores").dialog(
	 {
			 autoOpen: false,
			 width: 400,
			 height: 600,
			 modal: true,
			 show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
			 buttons: 
			{
				"Guardar": function () 
				{
				 
						if($('#importe').val() > 0 && $('#ProveedoresSelect').val() != '0' && $('.iva:checked').val() && parseFloat($('#IVANoAcreditable').val()) <= parseFloat($('#importeIVA').val()))
						{
							guardarProveedores($('#idx').val(),$('#idr').val());
							abreProveedoresLista($('#idpoliza').val());
							$("#Proveedores").dialog('close');
							//alert($('#aplica').prop('checked'))
						}
						else
						{
							alert('Hay un error en la captura, \n\nCausas:\n\n- Agregue un provedor. \n\n- Agregue un importe.\n\n- Seleccione un IVA. \n\n- La retencion del IVA no puede ser mayor al importe del IVA\n\n- El IVA no acreditable no puede ser mayor al importe del IVA.');
						}


				}
			}
		});
//--------------------------------Termina Proveedores----------------------***
//--------------------------------Comienza Causacion-----------------------***
$("#Causacion").dialog(
	 {
			 autoOpen: false,
			 width: 750,
			 height: 590,
			 modal: true,
			 show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
			 buttons: 
			{
				"Guardar": function () 
				{
				 
				 var guardar;
					var Cargos = $('#Cargos b').html();
							Cargos = Cargos.replace('$','').replace(/,/g, '');

					
						if(parseFloat($('#totalesImporteTotalHidden').val()) <= parseFloat(Cargos))
						{
								guardar = 1;
						}
						else
						{
								var acepta = confirm("El Total de 'Importe Total' es mayor a los Cargos.\nTotales: "+$('#totalesImporteTotalHidden').val()+"\nCargos: "+Cargos+"\nAun asi desea continuar?");
								if(acepta)
								{
										guardar=1;
								}
								else
								{
										guardar = 0;
								}
						}

						if(guardar)
						{
							guardaCausacion($('#idp').val());
						}

				}
			}
		});
//--------------------------------Termina Causacion-----------------------***
//--------------------------------Comienza Facturas-----------------------***
$("#Facturas").dialog(
	 {
			 autoOpen: false,
			 width: 700,
			 height: 510,
			 modal: true,
			 show:
			 {
				effect: "clip",
				duration: 500
			 },
				hide:
			 {
				effect: "clip",
				duration: 500
			 },
			 buttons: 
			{
				"Cerrar": function () 
				{
				 $("#Facturas").dialog('close')
				}
			}
		});
//--------------------------------Termina Facturas-----------------------***

//EXTENDIENDO LA FUNCION CONTAINS PARA HACERLO CASE-INSENSITIVE
	$.extend($.expr[":"], {
"containsIN": function(elem, i, match, array) {
return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
}
});
//-------------------------------------------------------------

	// INICIA GENERACION DE BUSQUEDA
			$("#buscar").bind("keyup", function(evt){
				//console.log($(this).val().trim());
				if(evt.type == 'keyup')
				{
					$("#lista tr:containsIN('"+$(this).val().trim()+"')").css('display','table-row');
					$("#lista tr:not(:containsIN('"+$(this).val().trim()+"'))").css('display','none');
					$("#lista tr:containsIN('*1*')").css('display','table-row');
					if($(this).val().trim() === '')
					{
						$("#lista tr").css('display','table-row');
					}
				}

			});
		// TERMINA GENERACION DE BUSQUEDA
 actualizaListaFac()
});
function actualizaListaFac()
{
$.post("ajax.php?c=CaptPolizas&f=listaFacturas",
		 	{
				IdPoliza: $('#idpoliza').val(),
			 },
			 function(data)
			 {
				
			 	$('#facturaSelect').html(data)
				
			 });
}

function facturas()
{

	$.post("ajax.php?c=CaptPolizas&f=facturas_dialog",
		 	{
				IdPoliza: $('#idpoliza').val()
			 },
			 function(data)
			 {
				
			 	$('#listaFacturas').html(data)
				
			 });
	$('#Facturas').dialog({position:['center',200]});
		$('#Facturas').dialog('open');	
}
function actualizaCuentas()
{
	$('#cargando-mensaje').css('display','inline');
	$.post("ajax.php?c=CaptPolizas&f=actualizaCuentas",
			function(datos)
			{
					$('#cuenta').html(datos)
					$("#cuenta").select2({
					 width : "150px"
					});
					$('#cargando-mensaje').css('display','none');
			});
			//buscacuentaext($('#cuenta').val());

						 //alert(datos)

}

function iracuenta(){
	window.parent.agregatab('../../modulos/cont/index.php?c=AccountsTree','Cuentas','',145)
	//window.location='../../modulos/cont/index.php?c=AccountsTree';
}

function actuali(){
		actualizaCuentas();
		$('#c').show();
			$('#a').show();
			$("#abonoext").val(0);
			$("#cargoext").val(0);
			$('#muestraextca').hide();
			$('#muestraextab').hide();
			$('#carext').hide;
			$('#abext').hide;
			$('#relacion').hide();
	}

