var table;

function check_file()
{
  	var ext = $('#factura').val();
  	var spaces = ext;
  	ext = ext.split('.');
  	ext = ext.slice(-1)[0];
 		console.log('extension archivo: '+ext)
  	if(ext != 'zip' && ext != 'xml')
  	{
  		alert("Archivo Inválido \nEl archivo debe tener una extensión xml o zip.");
  		$("#factura").val('');
  	}
  	if(spaces.indexOf(' ') >= 0)
  	{
  		alert("Archivo Inválido \nEl nombre del archivo y/o la carpeta no deben tener espacios en blanco. \n"+spaces);
  		$("#factura").val('');	
  	}

}
function compareDates(name){
    var dateObj = new Date($(name).val());
    var month = dateObj.getUTCMonth() + 1; //months from 1-12
    var year = dateObj.getUTCFullYear();
    return month+'-'+year;
}
function getArray(arreglo){
    var CFDI = [];
    var xml;
    for(x = 0; x < arreglo.length; x ++){
        if(arreglo[x].links != undefined){
            xml = arreglo[x].links;
            xml = xml.split("temporales/");
            if(xml[1] != undefined)
            {
              xml = xml[1].split(".xml");
              CFDI.push(xml[0]+'.xml');   
            }
        }
    }
    return CFDI
}
function downloadZip(){
    if($("#inicial").val() == '' || $("#final").val() == ''){
        alert('Las fechas no son validas');
        return;
    }else{
        var DateI = compareDates("#inicial");
        var DateF = compareDates("#final");
        if(DateI != DateF){
            alert('Las fechas no son validas');
            return;
        }
    }
    $('#buscar').attr("disabled",true);
    $('#download').attr("disabled",true);
    $('#buscar').html("<i class='material-icons spin' style='font-size:1.2em;'>sync</i>");
    $.when(downloadZipC(1), downloadZipC(2), downloadZipC(4)).done(function ( v1, v2, v3 ) {
        //var uuid = v1+v2+v3;
        var temporales = JSON.parse("[" + v1[0] + "]");
        var asignados = JSON.parse("[" + v2[0] + "]");
        var comp_pago = JSON.parse("[" +v3[0] + "]");
        var cfdi =[];
        cfdi.push(getArray(temporales[0]));
        cfdi.push(getArray(asignados[0]));
        cfdi.push(getArray(comp_pago[0]));
        var cfdi_r = [];
        for(var x = 0; x < cfdi.length; x ++){
            for(var t = 0; t < cfdi[x].length; t ++){
                if(cfdi_r.length == 0){
                    cfdi_r.push(cfdi[x][t]);
                }else{
                    var valid = true;
                    for(var z = 0; z < cfdi_r.length; z ++){
                        if(cfdi_r[z] == cfdi[x][t]){
                            valid = false;
                        }
                    }
                    if(valid){
                        cfdi_r.push(cfdi[x][t]);
                    }
                }
            }
        }
        $.ajax({
            url: 'ajax.php?c=Almacen&f=dowloadZip',
            type: 'post',
            dataType: 'json',
            data: {cadena: cfdi_r},
        }).done(function(resp) {
            $('#buscar').removeAttr("disabled");
            $('#buscar').html("Buscar");
            $('#download').removeAttr("disabled");
            console.log(resp);
            if(resp.estatus==1){
                window.open("../facturas/notas/facturas.zip",'_blank');
            }
        }).fail(function() {
            console.log("error");
        }).always(function() {
            console.log("complete");
        });
    });
}
function downloadZipC(asignadas){
    if(asignadas == 1 || asignadas == 2){
        return $.post("ajax.php?c=Almacen&f=listaFacturas2",{
                inicial     :$("#inicial").val(),
                final 		:$("#final").val(),
                asignadas 	:asignadas,
                tipo_facturas :0,
                rfc 		:0,
                prov        : 0
            });
    }
    if(asignadas == 4){
        return $.post("ajax.php?c=Almacen&f=listaFacturasPagos",
            {
                inicial 	:$("#inicial").val(),
                final 		:$("#final").val(),
                asignadas   :asignadas 
            });
    }
}

function poliza_manual()
{
	window.parent.agregatab('../../modulos/cont/index.php?c=CaptPolizas&f=Ver','Captura','',0);
}

function buscar(prov)
{
	$("#normales").show();
	$("#pagos").hide();
	$("#contenedor-provision :input").attr("disabled", true);
    $("#contenedor-pago :input").attr("disabled", true);
    $("#contenedor-eliminar :input").attr("disabled", true);
	if ($('#asignadas').val() == 3) {
		if (confirm("¿Desea validar las canceladas?")) {
			canceladas();
		}
	}

	$('#buscar').attr("disabled",true);
	$('#buscar').html("<i class='material-icons spin' style='font-size:1.2em;'>sync</i>");
	var asignadas = 0;
	var tipo_facturas = 0;
	var rfc = 0;

	if($("#asignadas").length)
		asignadas = $("#asignadas").val()
	if($("#tipo_facturas").length)
		tipo_facturas = $("#tipo_facturas").val()
	if($("#rfc").length && $("#rfc").val() != '')
		rfc = $("#rfc").val()

	/*if(parseInt(tipo_facturas))
		asignadas = 1;
	if(parseInt(tipo_facturas) == 4)
		asignadas = 4;*/
  
    $.post("ajax.php?c=Almacen&f=listaFacturas2",
	{
		inicial 	:$("#inicial").val(),
		final 		:$("#final").val(),
		asignadas 	:asignadas,
		tipo_facturas :tipo_facturas,
		rfc 		:rfc,
		prov        : prov
	},
	function(data)
	{
		var datos = jQuery.parseJSON(data);
		console.log(datos);
		var last_index = (datos.length-1);
		$('#totales').html("$ "+(datos[last_index]['total_final']).format());
		datos.pop();
		$('#tabla-data').DataTable().destroy();
    table = $('#tabla-data').DataTable({
    	//"paging": false,
        buttons:[{
         extend: 'excel',
         footer: true,
         text:   '<img alt="text" title="Descarga los CFDI en excel" src="images/images.jpg" style="height: 25px;"/>',
         exportOptions: {
       			//Va a exportar todas las columnas menos la de los iconos y los checkbox
       			//incluye las columnas ocultas.
            columns: [0,1,2,3,5,6,7,8,9,10,11,12,13,14,15,16,17,19]
          }
        },{
            text:'<button id="download" class="buttons-excel" alt="Selecciona facturas o haz clic en el botón Todas" data-toggle="tooltip" title="Descarga los CFDI temporales,asignadas y comp. de pagos" onclick="downloadZip();"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M224 136V0h-63.6v32h-32V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zM95.9 32h32v32h-32V32zm32.3 384c-33.2 0-58-30.4-51.4-62.9L96.4 256v-32h32v-32h-32v-32h32v-32h-32V96h32V64h32v32h-32v32h32v32h-32v32h32v32h-32v32h22.1c5.7 0 10.7 4.1 11.8 9.7l17.3 87.7c6.4 32.4-18.4 62.6-51.4 62.6zm32.7-53c0 14.9-14.5 27-32.4 27S96 378 96 363c0-14.9 14.5-27 32.4-27s32.5 12.1 32.5 27zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/></svg></button>'
        }],
        language: {
          search: "Buscar:",
          lengthMenu:"Mostrar _MENU_ elementos",
          zeroRecords: "No hay datos.",
          infoEmpty: "No hay datos que mostrar.",
          info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
          paginate: {
            first:    "Primero",
            previous: "Anterior",
            next:     "Siguiente",
            last:     "Último"
          }
        },
				columnDefs: [
					{ orderable: false, targets: [4, 17, 18] },
					{ visible: false, targets: [18,19] }
				],
        "order": [[ 0, "asc" ]],
        data:datos,
        autoWidth : false,
        columns: [
          { data: 'fecha','width': '200px'},
          { data: 'rfc','width': '200px'},
          { data: 'emisor','width': '200px'},
          { data: 'receptor','width': '200px'},
          { data: 'links','width': '50px'},
          { data: 'tipo','width': '200px'},
          { data: 'pago','width': '150px'},
          { data: 'metodo','width': '200px'},
          { data: 'moneda','width': '100px'},
          { data: 'subtotal','width': '100px'},
          { data: 'ivas','width': '100px'},
          { data: 'total','width': '100px'},
          { data: 'folio','width': '200px'},
          { data: 'uuid','width': '200px'},
          { data: 'version','width': '50px'},
          { data: 'provision','width': '100px'},
          { data: 'pagos','width': '100px'},
          { data: 'check','width': '50px'},
          { data: 'domicilio_emisor','width': '200px'},
          { data: 'domicilio_receptor','width': '200px'}
        ]
    });

 
    table.buttons(0, null).container().prependTo(
        table.table().container()
    );
    //console.log(table.rows({ 'search': 'applied' }).nodes());
    $("#checkAll").on('click', function() {
    	var rows = table.rows({ 'search': 'applied' }).nodes();
  		$('input[type="checkbox"]', rows).prop('checked', this.checked);
		});
    var cantidad = 0;
    $(".importes").each(function(index)
		{
			cantidad += parseFloat($(this).attr('cantidad'))
		});
		$('#buscar').removeAttr("disabled");
		$('#buscar').html("Buscar");
		//alert(cantidad)
		$("#boton_generar").show();
        $("#tabla-data_filter").after("<div id='scrollTable' class='dataTables_scrollBody' style= 'position: relative; overflow: auto; width: 100%'></div>");
        jQuery(jQuery("#tabla-data").detach()).appendTo("#scrollTable");
	});
}

function buscar_pagos()
{
	$("#normales").hide()
	$("#pagos").show()
	$('#buscar').attr("disabled",true);
	$('#buscar').html("<i class='material-icons spin' style='font-size:1.2em;'>sync</i>");
	$.post("ajax.php?c=Almacen&f=listaFacturasPagos",
	{
		inicial 	:$("#inicial").val(),
		final 		:$("#final").val(),
		asignadas   :4 
	},
	function(data)
	{
		var datos = jQuery.parseJSON(data);
		console.log(datos);
		var last_index = (datos.length-1);
		$('#totales').html("$ "+(datos[last_index]['total_final']).format());
		datos.pop();
		$('#tabla-data-pagos').DataTable().destroy();
    table = $('#tabla-data-pagos').DataTable({
    	//"paging": false,
        buttons:[{
            extend: 'excel',
            footer: true,
            text:   '<img alt="text" title="Descarga los CFDI en excel" src="images/images.jpg" style="height: 25px;"/>',
            exportOptions: {
                //Va a exportar todas las columnas menos la de los iconos y los checkbox
                //incluye las columnas ocultas.
            columns: [0,1,2,3,4,6,7,8,9,10,11,12,13,14,15]
            }
        },{
            text:'<button id="download" class="buttons-excel" alt="Selecciona facturas o haz clic en el botón Todas" data-toggle="tooltip" title="Descarga los CFDI temporales,asignadas y comp. de pagos" onclick="downloadZip();"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M224 136V0h-63.6v32h-32V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zM95.9 32h32v32h-32V32zm32.3 384c-33.2 0-58-30.4-51.4-62.9L96.4 256v-32h32v-32h-32v-32h32v-32h-32V96h32V64h32v32h-32v32h32v32h-32v32h32v32h-32v32h22.1c5.7 0 10.7 4.1 11.8 9.7l17.3 87.7c6.4 32.4-18.4 62.6-51.4 62.6zm32.7-53c0 14.9-14.5 27-32.4 27S96 378 96 363c0-14.9 14.5-27 32.4-27s32.5 12.1 32.5 27zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"/></svg></button>'
        }],
        language: {
          search: "Buscar:",
          lengthMenu:"Mostrar _MENU_ elementos",
          zeroRecords: "No hay datos.",
          infoEmpty: "No hay datos que mostrar.",
          info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
          paginate: {
            first:    "Primero",
            previous: "Anterior",
            next:     "Siguiente",
            last:     "Último"
          }
        },
        "order": [[ 0, "asc" ]],
        data:datos,
        autoWidth : false,
        columns: [
          { data: 'uuid','width': '200px' },
          { data: 'fecha','width': '200px' },
          { data: 'rfc','width': '200px' },
          { data: 'emisor','width': '200px' },
          { data: 'receptor','width': '200px' },
          { data: 'links','width': '50px' },
          { data: 'tipo','width': '200px' },
          { data: 'folio','width': '200px' },
          { data: 'uuid_doc','width': '200px' },
          { data: 'pago','width': '50px' },
          { data: 'moneda','width': '50px' },
          { data: 'saldo_ant','width': '50px' },
          { data: 'saldo_inso','width': '50px' },
          { data: 'importe','width': '50px'},
          { data: 'parcialidad','width': '50px' },
          { data: 'fecha_sub','width': '200px' }
        ]
    });
 
    table.buttons(0, null).container().prependTo(
        table.table().container()
    );
    //console.log(table.rows({ 'search': 'applied' }).nodes());
    $("#checkAll").on('click', function() {
    	var rows = table.rows({ 'search': 'applied' }).nodes();
  		$('input[type="checkbox"]', rows).prop('checked', this.checked);
		});
    var cantidad = 0;
    $(".importes").each(function(index)
		{
			cantidad += parseFloat($(this).attr('cantidad'))
		});
		//alert(cantidad)
		$('#buscar').removeAttr("disabled");
		$('#buscar').html("Buscar");
        $("#tabla-data-pagos_filter").after("<div id='scrollTablePagos' class='dataTables_scrollBody' style= 'position: relative; overflow: auto; width: 100%'></div>");
        jQuery(jQuery("#tabla-data-pagos").detach()).appendTo("#scrollTablePagos");
	});
}

function canceladas()
{
	$(".btn").attr('disabled',true)
	$("a,input:file,img").hide();
	$('#canc_load').css('display','block');
	var inicial = $("#inicial").val() + " 00:00:00";
	var final   = $("#final").val() + " 23:59:59";
	$.post("ajax.php?c=CaptPolizas&f=canceladas",
	{
		inicial 	:inicial,
		final 		:final
	},
	function(data)
	{
		console.log('Canceladas: ',data);
		$('#canc_load').css('display','none');
		if(parseInt(data))
		{
			alert('Hubieron '+data+' cancelados');
			//location.reload();
			$(".btn").attr('disabled',false);
		}
		else
		{
			alert('No hubo cancelados')
			$(".btn").attr('disabled',false)
			$("a,input:file,img").show();
		}
	});
}

$( '#fac' ).submit( function( e ) {
	e.preventDefault();
  	$('#verif').css('display','inline');
    $.ajax( {
      url: 'ajax.php?c=CaptPolizas&f=subeFacturaZip',
      type: 'POST',
      data: new FormData( this ),
      processData: false,
      contentType: false
    } ).done(function( data1 ) {
    	//alert(data1)
    	//$("#Facturas").dialog('refresh')
    	console.log(data1)

			$('#factura').val('')
			data1 = data1.split('-/-*');
			$('#verif').css('display','none');

			if(parseInt(data1[0]))
			{
				if(parseInt(data1[3]))
				{
					alert('Los siguientes '+data1[3]+' archivos no son validos: \n'+data1[4])
					console.log('Los siguientes '+data1[3]+' archivos no son validos: \n'+data1[4]);
				}

				if(parseInt(data1[1]))
				{
					alert(data1[1]+' Archivos Validados: \n'+data1[2])
					console.log(data1[1]+' Archivos Validados: \n'+data1[2]);
				}
				//alert(parseInt(data1[5]))
				if(parseInt(data1[5])){
					abrefacturasrepetidas();

				}else{
					location.reload();
				}
			}
			else
			{
				alert("El archivo zip no cumple con el formato correcto\nDebe llamarse igual que la carpeta que contiene los xmls.\nSólo debe contener una carpeta.\nEl nombre de la carpeta no debe contener espacios en blanco.");
			}
  	});
  });

$('body').bind("keyup", function(evt)
{
  if (evt.ctrlKey==1)//ctrl
  {
   	if(evt.keyCode == 85) //u  --- relaciona facturas fisicas y crea registro en bd
  	{
  		alert("Ejecutar funcion que recorre las facturas y si no se encuentran almacenadas en base de datos las registrará.");
  		if(confirm("Esta seguro que quiere correr esta funcion? puede tardar varios minutos en completarse."))
  		{
  			$.post("ajax.php?c=Almacen&f=buscaFacturas",
			{},
			function(data)
			{
				console.log(data)
				if(parseInt(data))
					alert("Proceso Finalizado exitosamente.")
			});
  		}
  	}

  	if(evt.keyCode == 87) //w --- recorre los xmls y busca en bd si esta asignada, entonces crea la carpeta fisica
  	{
  		alert("Ejecutar funcion que recorre las facturas y busca si esta asignada");
  		if(confirm("Esta seguro que quiere correr esta funcion? puede tardar varios minutos en completarse."))
  		{
  			$.post("ajax.php?c=Almacen&f=relacionaFacturasMovs",
		{},
		function(data)
		{
			console.log(data)
			if(parseInt(data))
				alert("Proceso Finalizado exitosamente.")
		});
  		}
  	}
  }
 });

function abrefacturasrepetidas(){
	$.post("ajax.php?c=CaptPolizas&f=listaRepetidos",
		 	{

			},
			function(callback)
			{
				$("#repe").html(callback);
			});

	$('#almacen_repe').modal('show');
}

function afrAgregar(){
	var copiar = [];
		for(var i = 1 ; i<=$(".copia").length; i++)
		{
			if($("#copia-"+i).is(':checked'))
			{
				copiar.push($("#copia-"+i).val());
			}
		}
		$("#load").show();
	$.post("ajax.php?c=CaptPolizas&f=copiaRepetidos",{
		opc:1,
		xml: copiar

		},function(r){
			console.log(r);
			$("#load").hide();
			location.reload();
		});
}

function afrCancelar(){
	$.post("ajax.php?c=CaptPolizas&f=copiaRepetidos",{
	opc: 0
	},function(r){
		console.log(r);
		$("#load").hide();
		location.reload();
	});
}

function descargarXMLs(){
	var rows = table.rows({ 'search': 'applied' }).nodes();
	var arr_inputs = $('input:checked', rows);
	//Validamos que el usuario tenga seleccionado algun checkbox
	if (arr_inputs.length > 0) {
		//Si el usuario confirma que desea descargar los xmls...
		if (confirm('¿Desea descargar los XMLs?')) {
			var xmls = new Array();
			//Obtenemos los nombres de los xmls en registros de la tabla
			$.each(arr_inputs, function(index, input){
				xmls.push($(input).attr('xml'));
			});
			//Luego los descargamos por ajax
			$.post('ajax.php?c=backup&f=descargarXMLS',
			{
				xmls: xmls
			},
			function (data){
				console.log(data);
				$('#hiddenContainer').html(data.download);
				var linkZip = $('#zipPathXML').val();
				if(linkZip != undefined) {
					window.location = linkZip;
					$.post('ajax.php?c=backup&f=borrarZip', {link: linkZip});
				} else {
					alert("Hubo un error y no se genero la descarga.")
				}
			}, "JSON");
		}
	//Si no, le informamos que tiene que seleccionar un checkbox para poder descargar	 
	} else {
		alert("Seleccione al menos un registro para poder descargar.");
	}
}

function eliminar_xmls(xmls){
	console.log(xmls);
	$.post("ajax.php?c=backup&f=Eliminarxmls2",
		{xmls: xmls},
		function(data){
            alert(data);
            $("#buscar").click();
		});
}

function eliminarSeleccionados() {
	var rows = table.rows({ 'search': 'applied' }).nodes();
	var arr_inputs = $('input:checked', rows);
	//Validamos que este seleccionada al menos un registro
	if (arr_inputs.length > 0) {
		//Validamos que el usuario sabe que esta borrando facturas
		if(confirm('¿Realmente desea eliminar los XMLs?')) {
			var xmls = new Array();
			//Obtenemos los nombres de los xmls en registros de la tabla
			$.each(arr_inputs, function(index, input){
				xmls.push($(input).attr('xml'));
			});
			eliminar_xmls(xmls);
		}
		//location.reload();
    //$("#buscar").click();
	} else {
		alert("Debe seleccionar al menos un registro.");
	}
}

function agregar_funcion()
{
	var tipo = 0;
	
	if($("#asignadas").length)
		tipo = $("#asignadas").val()
	if($("#tipo_facturas").length)
		tipo = $("#tipo_facturas").val()

	if(parseInt(tipo) && parseInt(tipo) != 4)
		$("#buscar").attr('onclick','buscar()')
	else
		$("#buscar").attr('onclick','buscar_pagos()')
}

function generar_polizas(tipo)
{
  $("input[type='search']").val('')
	var rows = table.rows({ 'search': 'applied' }).nodes();
	var arr_inputs = $('input:checked', rows);
  var tipo_op = 'Pago';
  if(tipo == 1)
    tipo_op = 'Provision';
	//Validamos que este seleccionada al menos un registro
	if (arr_inputs.length > 0) {
		//Validamos que el usuario sabe que esta borrando facturas
		if(confirm('¿Quiere hacer las polizas de '+tipo_op+' de estas facturas?')) {
			$('.bloquear').attr("disabled",true);
			$('#buscar').attr("disabled",true);
			
			var xmls = new Array();
			//Obtenemos los nombres de los xmls en registros de la tabla
			$.each(arr_inputs, function(index, input){
				xmls.push($(input).attr('idfac'));
			});
      console.log(xmls)
			hacer_polizas(xmls,tipo);
		}
		//location.reload();
	} else {
		alert("Debe seleccionar al menos un registro.");
	}
}

function hacer_polizas(xmls,tipo)
{
	$.post("ajax.php?c=Almacen&f=hacer_polizas",
		{
			xmls: xmls,
			tipofac:$("#tipo_facturas").val(),
      tipopol:tipo
		},
		function(data)
		{
			console.log('callback: '+data)
			if(data) 
			{
				alert("Las siguientes "+data+" facturas se han creado exitosamente.");
				$("#buscar").click();
			}
			else
				alert("No se generó ninguna poliza.")
			$('.bloquear').removeAttr("disabled");
			$('#buscar').removeAttr("disabled");
		});
}
function actions(){
    if($('#tabla-data tr').filter(':has(:checkbox:checked)').length > 0 ){
        $("#contenedor-provision :input").attr("disabled", false);
        $("#contenedor-pago :input").attr("disabled", false);
        $("#contenedor-eliminar :input").attr("disabled", false);
    }else{
        $("#contenedor-provision :input").attr("disabled", true);
        $("#contenedor-pago :input").attr("disabled", true);
        $("#contenedor-eliminar :input").attr("disabled", true);
    }
    
    $('#tabla-data tr').filter(':has(:checkbox:checked)').each(function() {
        if($(this).find("td").eq(15).html() != undefined && $(this).find("td").eq(15).html() != ''){
            $("#contenedor-provision :input").attr("disabled", true);
        }
        if($(this).find("td").eq(16).html() != undefined && $(this).find("td").eq(16).html() != ''){
            $("#contenedor-pago :input").attr("disabled", true);
        }
    });
}