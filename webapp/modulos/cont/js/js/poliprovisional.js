/**
 * @author Carmen Gutierrez
 */
    
$(document).ready(function(){

	
	// $("#cuentaingre").select2({
         // width : "150px"
    // });
  	// $("#ivaingre").select2({
         // width : "150px"
    // });
    // $("#cuentaegre").select2({
         // width : "150px"
    // });
  	// $("#ivaegre").select2({
         // width : "150px"
    // });
//     
    
    
});
function cambio(){
	if($('#comprobante').val()==1){
		
		$('#ingresos').show();
		$('#egresos').hide();
		$('#xml').show();
		
	}if($('#comprobante').val()==2){
		
		$('#ingresos').hide();
		$('#egresos').show();
		$('#xml').show();
	}
	if($('#comprobante').val()==0){
		
		$('#ingresos').hide();
		$('#egresos').hide();
		$('#xml').hide();
	}
}
function comprueba_extension(formulario, archivo) { 
	$("#load").show();
   if (!archivo) {  $("#load").hide();
      	 alert("No ha seleccionado ningun archivo"); 
   }else{ 
      extension = (archivo.substring(archivo.lastIndexOf("."))).toLowerCase(); 
      if ('.xml' == extension) {
      	var formData = new FormData($("#formulario")[0]);
        $.ajax({
            url: 'index.php?c=CaptPolizas&f=validaxml',  
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data){
                var separa = data.split("-_-");
                if(separa[1]==0){ $("#load").hide();
                	alert("El XMl esta cancelado");
               }else if(separa[1]!=1){ $("#load").hide();
                	alert("XML no valido (Estructura contra esquema incorrecta)");
               }else if(separa[1]==1){ $("#load").hide();
              		alert("XML  validado (Estructura correcta)");
                	$('#envia').click(); 
                }
            }
            
        });
      } 
      else{ $("#load").hide();
      	 alert("Compruebe la extension del archivo. \nSolo se pueden subir archivos XML"); 
      } 
   }  
}
function cuentaingresosact(cont)
{
	$('#cargando-mensaje').css('display','inline');
	$.post("ajax.php?c=CaptPolizas&f=cuentaingresosact",
			function(datos)
			{
					$('#cuentaingre'+cont).html(datos);
					$("#cuentaingre"+cont).select2({
					 width : "150px"
					});
					$('#cargando-mensaje').css('display','none');
			});

						 //alert(datos)

}
function cuentaegresosact(cont)
{
	$('#cargando-mensaje').css('display','inline');
	$.post("ajax.php?c=CaptPolizas&f=cuentaegresosact",
			function(datos)
			{
					$('#cuentaingre'+cont).html(datos);
					$("#cuentaingre"+cont).select2({
					 width : "150px"
					});
					$('#cargando-mensaje').css('display','none');
			});

						 //alert(datos)

}
function cuentaegresosdeducible()
{
	$('#cargando-mensaje').css('display','inline');
	$.post("ajax.php?c=CaptPolizas&f=cuentaegresosact",
			function(datos)
			{
					$('#deducible').html(datos);
					$("#deducible").select2({
					 width : "150px"
					});
					$('#cargando-mensaje').css('display','none');
			});
}
function iracuenta(){
	window.parent.agregatab('../../modulos/cont/index.php?c=AccountsTree','Cuentas','',145);
	//window.location='../../modulos/cont/index.php?c=AccountsTree';
}
function borra(cont,tipo){
	$.post('ajax.php?c=CaptPolizas&f=borraprovision',{
		cont:cont,
		tipo:tipo},
		function(respues) {
			window.location.reload();
	});
}
function cancela(){
	if(confirm("Esta seguro de eliminar los datos capturados?")){
		$.post('index.php?c=CaptPolizas&f=cancela',{},function(){ window.location.reload();});
		$("#comprobante").attr("disabled",false);
	}
}
function mandaasignarcuenta(){
	window.parent.agregatab("../../modulos/cont/index.php?c=Config&f=configAccounts","Asignación de Cuentas","",1647);
} 

function fechaactu(){
	if($('#idperio').val()<10){ fec=0+$('#idperio').val(); }else{ fec=$('#idperio').val(); }
	$("#fecha").val($("#ejercicio").val()+'-'+fec+'-01').attr('readonly','readonly');
	
	$.datepicker.setDefaults($.datepicker.regional['es-MX']);
    $("#fecha").datepicker({
	 	maxDate: 365,
	 	dateFormat: 'yy-mm-dd',
        numberOfMonths: 1
    });
}
function fechadefault(){
	if($('#idperio').val()<10){ fec=0+$('#idperio').val(); }else{ fec=$('#idperio').val(); }
	$("#fecha").val($("#ejercicio").val()+'-'+fec+'-01').attr('readonly','readonly');
	     
   $.datepicker.setDefaults($.datepicker.regional['es-MX']);
    $("#fecha").datepicker({
	 	maxDate: 365,
	 	dateFormat: 'yy-mm-dd',
        numberOfMonths: 1
        
    });
}
function actualizaCuentas(cont)
{
	$('#cargando-mensaje').css('display','inline');
	$.post("ajax.php?c=CaptPolizas&f=actualizaprovprovecuentas",
			function(datos)
			{
					$('#CuentaProveedores'+cont).html(datos);
					$("#CuentaProveedores"+cont).select2({
					 width : "150px"
					});
					$('#cargando-mensaje').css('display','none');
			});

						 //alert(datos)

}  
function actualizaCuentascli(cont)
{
	$('#cargando-mensaje').css('display','inline');
	$.post("ajax.php?c=CaptPolizas&f=actualizaprovclicuentas",
			function(datos)
			{
					$('#CuentaClientes'+cont).html(datos);
					$("#CuentaClientes"+cont).select2({
					 width : "150px"
					});
					$('#cargando-mensaje').css('display','none');
			});

						 //alert(datos)

}
function mes(periodo){
 	var p1;
    if(periodo==1 ){ p1='Enero'; }
    if(periodo==2){ p1='Febrero'; }	
	if(periodo==3){ p1='Marzo';  }
    if(periodo==4){ p1='Abril';}
    if(periodo==5){ p1='Mayo';}
    if(periodo==6){ p1='Junio';}
    if(periodo==7){ p1='Julio';}
    if(periodo==8){ p1='Agosto'; }
    if(periodo==9){ p1='Septiembre'; }
    if(periodo==10){ p1='Octubre';  }
    if(periodo==11){ p1='Noviembre';  }
    if(periodo==12){ p1='Diciembre';}
     return p1;
}
  function guardaprovimultiple(){// ver q pasa con los totales y con cuenta proeveedorrs
		$("#comprobante").attr("disabled",false);
		$("#agregaprevio").hide();
		$("#cancela").hide();
		$("#load2").show();
		if($("#dife").html()>0 || $("#dife").html()<0){
			if(!confirm("La poliza no esta cuadrada desea guardar de todos modos?")){
				$("#load,#load2").hide();
				$("#agregaprevio").show();
				$("#cancela").show();
				return false;
			}
		}
		var fecha=$('#fecha').val();
		if(fecha!=""){
			var fec;
			var sep=fecha.split('-');
			if($('#idperio').val()<10){ fec=0+$('#idperio').val(); }else{ fec=$('#idperio').val(); }
		
			if(fec==sep[1] && sep[0]==$("#ejercicio").val()){
			$("#load").show(); $("#guardar").hide();
				$.post('ajax.php?c=CaptPolizas&f=guardaprovimultiple',{
					fecha:fecha
				},function (resp){
					if(resp==0){  
						 var id=0;
						 if (confirm("Poliza generada Correctamente!. \n Desea ver la poliza?")){
						 	$.post('ajax.php?c=CaptPolizas&f=consultaultima',{},
						 		function (idpoli){ 
						 			$("#load2").hide();
						 			window.location="index.php?c=CaptPolizas&f=provisionmultiple";
						 			window.parent.preguntar=false;
						 			window.parent.quitartab("tb0",0,"Polizas");
						 			window.parent.agregatab('../../modulos/cont/index.php?c=CaptPolizas&f=ModificarPoliza&id='+idpoli+'&im=3','Polizas','',0);
									window.parent.preguntar=true;
						 		});
						 }else{
						 	$("#load2").hide();
						 	window.location="index.php?c=CaptPolizas&f=provisionmultiple";
						 }
						 
					}if (resp==1){
						 alert("Fallo al general poliza!."); window.location="index.php?c=CaptPolizas&f=verprovision";
						$("#load2").hide();
					}
				});
		}else{
			alert("Elija una fecha acorde al periodo y ejercicio actual (mes:"+mes($('#idperio').val())+",año:"+$("#ejercicio").val()+")");
			 $("#load2").hide();$("#agregaprevio").show();$("#cancela").show();
			 $('#fecha').css("border-color","red");
			
		}
	 }else{
		 alert("Elija una fecha para la poliza");
		 $("#load2").hide();$("#agregaprevio").show();$("#cancela").show();
		 $('#fecha').css("border-color","red");
	 }
			
}

function abrefacturas(){
	
	$.post("ajax.php?c=CaptPolizas&f=listaTemporalesProvision",
		 	{
		
			},
			function(callback)
			{
				$(".listado").html(callback);
			});
	 $("#almacen").dialog(
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
				"Previsualizar": function (){
					 var copiar = [];
						for(var i = 1 ; i<=$(".copiar").length; i++)
						{
							if($("#copiar-"+i).is(':checked'))
							{
								copiar.push($("#copiar-"+i).val());
							}
						}
					$.post("index.php?c=CaptPolizas&f=guardaProvisionMultipleAlmacen",{
						comprobante: $('#comprobante').val(),
						xml: copiar,
						fecha:$("#fecha").val()
						},function(r){
							window.location="index.php?c=CaptPolizas&f=provisionmultiple";
						});
				}
			}
		});

	$('#almacen').dialog({position:['center',200]});
	$('#almacen').dialog('open');	
}
$(function(){
$.extend($.expr[":"], {
	"containsIN": function(elem, i, match, array) {
	return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
	}
	});
$("#busqueda").bind("keyup", function(evt){
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
});
function buttonclick(v)
	{
		$("."+v).click();
	}
function buttondesclick(v)
	{
		$("."+v).attr('checked',false);
	}
		
function abrefacturascomprobacion(){
	
	$.post("ajax.php?c=CaptPolizas&f=listaTemporalesProvision",
		 	{
		
			},
			function(callback)
			{
				$(".listado").html(callback);
			});
	 $("#almacen").dialog(
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
				"Previsualizar": function (){
					 var copiar = [];
						for(var i = 1 ; i<=$(".copiar").length; i++)
						{
							if($("#copiar-"+i).is(':checked'))
							{
								copiar.push($("#copiar-"+i).val());
							}
						}
					$.post("index.php?c=CaptPolizas&f=comprobacionGastosAlmacen",{
						xml: copiar,
						fecha:$("#fecha").val()
						},function(r){
							window.location='index.php?c=CaptPolizas&f=comprobacion';
						});
				}
			}
		});

	$('#almacen').dialog({position:['center',200]});
	$('#almacen').dialog('open');	
}
