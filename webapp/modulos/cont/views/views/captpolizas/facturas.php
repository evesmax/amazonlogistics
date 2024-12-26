<script language='javascript'>
$(function(){
	//EXTENDIENDO LA FUNCION CONTAINS PARA HACERLO CASE-INSENSITIVE
	  $.extend($.expr[":"], {
	"containsIN": function(elem, i, match, array) {
	return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
	});
	//-------------------------------------------------------------
	// INICIA GENERACION DE BUSQUEDA
			$("#busqueda").bind("keyup", function(evt){
				//console.log($(this).val().trim());
				if(evt.type == 'keyup')
				{
					$(".listado tr:containsIN('"+$(this).val().trim()+"')").css('display','table-row');
					$(".listado tr:not(:containsIN('"+$(this).val().trim()+"'))").css('display','none');
					$(".listado tr:containsIN('*1_-{}*')").css('display','table-row');
					if($(this).val().trim() === '')
					{
						$(".listado tr").css('display','table-row');
					}
				}

			});
		// TERMINA GENERACION DE BUSQUEDA
	listaTemporales();
	$("#subidas").show();
	$("#buscadas").hide();
	$("#buscar_facturas").show()
	$("#subir_facturas").hide()

	$("#subir_facturas").click(function(event) {
		$("#subidas").show();
		$("#buscadas").hide();
		$("#buscar_facturas").show()
		$("#subir_facturas").hide()
	});

	$("#buscar_facturas").click(function(event) {
		$("#subidas").hide();
		$("#buscadas").show();
		$("#subir_facturas").show()
		$("#buscar_facturas").hide()
	});

	$("#asignar_facturas").click(function(event) {
		var copiar = [];
		var borrar = [];
		for(var i = 1 ; i<=$(".copiar").length; i++)
		{
			if($("#copiar-"+i).is(':checked'))
			{
				//alert($("#copiar-"+i).val())
				copiar.push($("#copiar-"+i).val())

			}

			if($("#borrar-"+i).is(':checked'))
			{
				//alert($("#borrar-"+i).val())
				borrar.push($("#borrar-"+i).val())
			}
		}
		$.post("ajax.php?c=Reports&f=copiaFacturaBorra",
		 		{
					IdPoliza: $('#idpoliza').val(),
					Copiar: copiar,
					Borrar: borrar
				},
				function()
				{
					$.post("ajax.php?c=CaptPolizas&f=facturas_dialog",
			 		{
						IdPoliza: $('#idpoliza').val()
					},
					function(data)
					{
					
					 	$('#listaFacturas').html(data)
					 	actualizaListaFac();
					
					});	
					actualizaListaFac();
					$("#subidas").show();
					$("#buscadas").hide();
					$("#buscar_facturas").show()
					$("#subir_facturas").hide()
					listaTemporales();

				});

	});

});

	function buttonclick(v)
	{
		$("."+v).click();
	}

	function listaTemporales()
	{
		$.post("ajax.php?c=Reports&f=listaTemporales",
		 	{
		
			},
			function(callback)
			{
				$(".listado").html(callback);
			});
	}
 	function eliminar(archivo)
 	{
 		var confirmacion = confirm("Esta seguro de eliminar este archivo: \n"+archivo);
 		if(confirmacion)
 		{
 			$.post("ajax.php?c=Reports&f=EliminarArchivo",
		 	{
				Archivo: archivo
			},
			function()
			{
			 	//location.reload();
				//alert('Eliminado')
				$.post("ajax.php?c=Reports&f=borraFacturaForm",
		 		{
					IdPoliza: $('#idpoliza').val(),
					Archivo: archivo
				},
				function()
				{
					$.post("ajax.php?c=CaptPolizas&f=facturas_dialog",
			 		{
						IdPoliza: $('#idpoliza').val()
					},
					function(data)
					{
					
					 	$('#listaFacturas').html(data)
					 	actualizaListaFac();
					
					});	 				
				});
			});
 		}
 	}
</script>
<style>
#lista td
{
	width:146px;
	text-align: center;
	border:1px solid #BDBDBD;
}

#buscar
{
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
-o-border-radius: 4px;
border-radius: 4px;
}
#loading
{
	background-color:#BDBDBD;
	color:white;
	text-align:center;
	font-weight:bold;
}
</style>
<div id='Facturas' title='Lista de Facturas.'>
	<button id='subir_facturas'>Ir a Subir Facturas</button> <button id='buscar_facturas'>Ir a Buscar Facturas No Asignadas</button><br /><br />
	<div id='subidas'>
		<div>
			<form name='fac' id='fac' action='' method='post' enctype='multipart/form-data'>
				<input type='file' name='factura[]' id='factura' multiple><input type='hidden' name='plz' id='plz' value='<?php echo $numPoliza['id']; ?>'>
				<input type='submit' id='buttonFactura' value='Asociar Facturas' class="nminputbutton_color2">  
				<span id='verif' style='color:green;display:none;'>Verificando...</span>
			</form>
		</div>
		<table id='listaFacturas'>
			
		</table>
	</div>
	<div id='buscadas'>
		<div style='font-weight:bold;text-align:right;margin-top:10px;margin-bottom:10px;'><button id='asignar_facturas'>Asignar a poliza</button></div>
<input type='text' class="nmcatalogbusquedainputtext" id='busqueda' name='busqueda' placeholder='Buscar'>
		<table class='listado'>
		
		</table>
	</div>
	
</div>
