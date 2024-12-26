var arbolUnidades = {

	init : function()
	{
		$('#btnNuevaUnidad').bind('click', function(event) {
			arbolUnidades.nueva();
		});
		$('#btnModificarUnidad').bind('click', function(event) {
			arbolUnidades.modificar();
		});
		$('#btnEliminarUnidad').bind('click', function(event) {
			arbolUnidades.eliminar();
		});
	},

	desglosadas : function()
	{
		var seleccionado = $('#cboUnidadesTree option:selected').val();

		$('#resultUnidades').empty();

		if(seleccionado != '')
		{
			$.ajax({
				url: '../index.php/unidadesTree/desglozadas',
				type: 'POST',
				dataType: 'json',
				data: {
					seleccionado : seleccionado
				},
			})
			.done(function(data) {

				$.each(data[0], function(index, val) {

					var contenedorUnidad = $(document.createElement('div')).addClass('col-sm-12 result').css({'padding':'0'}).attr({'id':'divUnidades'+val.idUni}).appendTo($('#resultUnidades'));
					$(document.createElement('div')).addClass('col-sm-7').html(val.compuesto).css({'padding':'0','height':'34px'}).appendTo(contenedorUnidad);

					//alert(data[1])
					if(data[1]!=false)
					{
						var cboNuevaUnidad = $(document.createElement('select')).addClass('col-sm-5').attr('id', 'cboNuevaUnidad').css({'margin-top':'6px'}).appendTo(contenedorUnidad);
						$(document.createElement('option')).attr('value','').html('Nueva Unidad Basica').appendTo(cboNuevaUnidad);
						$.each(data[1], function(index, val) {
							$(document.createElement('option')).attr('value',val.id).html(val.tipo).appendTo(cboNuevaUnidad);
						});
						cboNuevaUnidad.bind('change', function(event) {

							arbolUnidades.agregar(val.idUni,$(this).val());
						});
					}else
					{
						var contenedorBtn = $(document.createElement('div')).addClass('col-sm-4').css({'padding':'0','border-left':'1px solid','text-align':'center'}).appendTo(contenedorUnidad);
						var btn1 = $(document.createElement('div')).addClass('radio col-sm-6').html('<label class="radio-inline"><input type="radio" name="chbUnid" id="unidad'+val.idUni+'" value="'+val.idUni+'" >Unidad Base</label>').css({'margin-left':'11%'}).appendTo(contenedorBtn);
						if(val.permiso != 0)
						{

							var btn2 = $(document.createElement('input')).attr({'type':'button','value':'Quitar'}).addClass('btn btn-danger pull-right').appendTo(contenedorBtn);
							btn2.bind('click', function(event) {
								arbolUnidades.quitar(val.idUni);
							});
						}
						
						$('#unidad'+val.idUni+'').bind('click', function(event) {
							var seleccionado = $(this).val();
							arbolUnidades.unidadBase(seleccionado);
						});

						if(val.orden == 1)
						{
							$('#unidad'+val.idUni).attr({'checked':'checked'});
						}
					}

				});

});
}
},
unidadBase : function(id)
{
	$.ajax({
		url: '../index.php/unidadesTree/uBase',
		type: 'POST',
		dataType: 'json',
		data: 
		{
			tree: $('#cboUnidadesTree option:selected').attr('id'),
			id : id,
		},
	})
	.done(function() {
		console.log("success");
	});
	
},
quitar : function(id)
{
	var unidad = $('#cboUnidadesTree option:selected').attr('id');
	$.ajax({
		url: '../index.php/unidadesTree/quitar',
		type: 'POST',
		dataType: 'json',
		data: {
			id: id,
			unidad : unidad,
		},
	})
	.done(function(data) {

		if(data[0])
		{
			$('#divUnidades'+id).hide('slow', function() {
				$('#divUnidades'+id).remove();

				var cboContenedor = $('#cboUnidadesTree').empty();
				$(document.createElement('option')).attr({'value':''}).html('Selecciona').appendTo(cboContenedor);
				$.each(data[1], function(index, val) {
					$(document.createElement('option')).attr({'value':val["identificadores"],'id':val["id"]}).html(val["tipo"]).appendTo(cboContenedor);
				});
				$(document.createElement('option')).attr({'value':'000'}).html('Sin Unidad Basica').appendTo(cboContenedor);

				$('[id='+unidad+']','#cboUnidadesTree').attr('selected','selected');
			});
		}
	});

},
agregar : function(id,unidad)
{

	$.ajax({
		url: '../index.php/unidadesTree/agregar',
		type: 'POST',
		dataType: 'json',
		data: {
			id: id,
			unidad:unidad
		},
	})
	.done(function(data) {
		if(data[0])
		{
			$('#divUnidades'+id).hide('slow', function() {
				$('#divUnidades'+id).remove();

				var cboContenedor = $('#cboUnidadesTree').empty();
				$(document.createElement('option')).attr({'value':''}).html('Selecciona').appendTo(cboContenedor);
				$.each(data[1], function(index, val) {
					$(document.createElement('option')).attr({'value':val["identificadores"],'id':val["id"]}).html(val["tipo"]).appendTo(cboContenedor);
				});
				$(document.createElement('option')).attr({'value':'000'}).html('Sin Unidad Basica').appendTo(cboContenedor);

				$('#cboUnidadesTree').val('000');
			});
		}
	});

},

nueva : function()
{

	$('#diagloNuevaUnidad').dialog({
		position: 'top',
		modal: true,
		width: '500px',
		buttons:
		[
		{
			text:'Agregar',
			class: 'btn btn-success col-sm-5 pull-left',
			click: function()
			{ 
				if($('#txtNuevaUnidad').val()!= '')
				{

					$.ajax({
						url: '../index.php/unidadesTree/nueva',
						type: 'POST',
						dataType: 'json',
						data: {
							nombre: $('#txtNuevaUnidad').val()
						},
					})
					.done(function(data) {
						if(data[0])
						{
							var cboContenedor = $('#cboUnidadesTree').empty();
							$(document.createElement('option')).attr({'value':''}).html('Selecciona').appendTo(cboContenedor);
							$.each(data[1], function(index, val) {
								$(document.createElement('option')).attr({'value':val["identificadores"],'id':val["id"]}).html(val["tipo"]).appendTo(cboContenedor);
							});
							$(document.createElement('option')).attr({'value':'000'}).html('Sin Unidad Basica').appendTo(cboContenedor);
						}
					});
					$('#txtNuevaUnidad').val('');
					$(this).dialog("close");
				}else
				{
					alert("Debes introducir el nombre de la unidad.");
				}
			},
		},
		{
			text: "Cancelar",
			class: 'btn btn-danger col-sm-5 pull-right',
			click: function()
			{
				$(this).dialog("close");
			}
		}
		],
	});
},
modificar : function(){

	var texto = $('#cboUnidadesTree option:selected').text();
	var seleccion = $('#cboUnidadesTree option:selected').attr('id');
	$('#lblAnteriorNombre2').text(texto);

	if(seleccion != null)
	{
		$('#dialogModificarUnidad').dialog({
			position: 'top',
			modal: true,
			width: '500px',
			title: "Modificacion del nombre de la Unidad Basica",
			buttons:
			[
			{
				text:'Cambiar',
				class: 'btn btn-success col-sm-5 pull-left',
				click: function()
				{ 
					$.ajax({
						url: '../index.php/unidadesTree/modifica',
						type: 'POST',
						dataType: 'json',
						data: {
							nombre: $('#txtModificaNombre').val(),
							id : seleccion,
						},
					})
					.done(function(data) {
						if(data)
						{
							window.location.reload();
						}else
						{
							alert('No puedes modificar esta unidad');
						}
					});
					$('#txtNuevaUnidad').val('');
					$(this).dialog("close");
				},
			},
			{
				text: "Cancelar",
				class: 'btn btn-danger col-sm-5 pull-right',
				click: function()
				{
					$(this).dialog("close");
				}
			}
			],
		});	
	}else
	{
		alert('Primero debes seleccionar la Unidad Base para modificarla.');
	}

},

eliminar : function()
{
	var texto = $('#cboUnidadesTree option:selected').text();
	var seleccion = $('#cboUnidadesTree option:selected').attr('id');
	$('#lblUnidadEliminar').text(texto);

	if(seleccion != null)
	{
		$('#dialogEliminarUnidad').dialog({
			position: 'top',
			modal: true,
			width: '500px',
			title: "Eliminar la Unidad Basica",
			buttons:
			[
			{
				text:'Eliminar',
				class: 'btn btn-success col-sm-5 pull-left',
				click: function()
				{ 
					$.ajax({
						url: '../index.php/unidadesTree/eliminar',
						type: 'POST',
						dataType: 'json',
						data: {
							nombre: $('#txtModificaNombre').val(),
							id : seleccion,
						},
					})
					.done(function(data) {
						if(data)
						{
							window.location.reload();
						}else
						{
							alert('No puedes eliminar esta unidad');
						}
					});
					$('#txtNuevaUnidad').val('');
					$(this).dialog("close");
				},
			},
			{
				text: "Cancelar",
				class: 'btn btn-danger col-sm-5 pull-right',
				click: function()
				{
					$(this).dialog("close");
				}
			}
			],
		});	
	}else
	{
		alert('Primero debes seleccionar la Unidad Base para eliminarla.');
	}
}

}