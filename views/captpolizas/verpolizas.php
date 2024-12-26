
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(function(){
	dias_periodo()
	$.post("ajax.php?c=CaptPolizas&f=GetPolizas",
 		 {
    		Ejercicio: $('#IdExercise').val(),
    		Periodo: $('#Periodo').val()
  		 },
  		 function(data)
  		 {
  		 	$('.capturaPoliza').html(data);
  		 });
	$('body').bind("keyup", function(evt){
		if (event.ctrlKey==1)
    {
      if (evt.keyCode == 13)
      {
        $('#nuevapolizaboton').click();
      };
    };
	});

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
					$(".capturaPoliza tr:containsIN('"+$(this).val().trim()+"')").css('display','table-row');
					$(".capturaPoliza tr:not(:containsIN('"+$(this).val().trim()+"'))").css('display','none');
					$(".capturaPoliza tr:containsIN('*1*')").css('display','table-row');
					if($(this).val().trim() === '')
					{
						$(".capturaPoliza tr").css('display','table-row');
					}
				}

			});
		// TERMINA GENERACION DE BUSQUEDA

});
function nuevaPoliza(idorg,idex,per)
{
$.post("ajax.php?c=CaptPolizas&f=CreateNewPoliza",
 		 {
    		Organizacion: idorg,
    		Ejercicio: idex,
    		Periodo: per
  		 },
  		 function(data)
  		 {
  		 	if(parseInt(data))
  		 	{
  		 		window.location = 'index.php?c=CaptPolizas&f=Capturar'
  		 		//alert('si se guardo')
  		 	}
  		 	else
  		 	{
  		 		alert("Para generar la poliza del periodo 13 es necesario configurar la cuenta de saldos en la pantalla de Asignación de Cuentas.");
  		 	}
  		 });
}

function deletePoliza(id,con)
{
	var confirmar = confirm("¿Esta seguro de inactivar esta poliza "+con+"?");

	if(confirmar)
	{
		$.get( "ajax.php?c=CaptPolizas&f=ActActivo&id="+id, function() {
 		//location.reload()
 		$("#tr"+id).fadeOut(500);
		});
	}
}

function dias_periodo()
{
	$.post("ajax.php?c=CaptPolizas&f=InicioEjercicio",
 		 {
    		NombreEjercicio: $('#NameExercise').val()
  		 },
  		 function(data)
  		 {
  		 	var cad = data.split("-");
		var fin;
		if($('#Periodo').val() == 13)
		{
			$('#inicio_mes').html('31-12-'+$("#NameExercise").val());
			$('#fin_mes').html('31-12-'+$("#NameExercise").val());
		}
		else
		{
		$('#inicio_mes').html(moment($("#NameExercise").val()+'-'+cad[1]+'-'+cad[2]).add('months', $('#Periodo').val()-1).format('DD-MM-YYYY'));
		fin = moment($("#NameExercise").val()+'-'+cad[1]+'-'+cad[2]).add('months', $('#Periodo').val()).format('YYYY-MM-DD');
		fin = moment(fin).subtract('days',1).format('DD-MM-YYYY');
		$('#fin_mes').html(fin);
		}
  		 	
  		 });
	
}	
function cambioPeriodoEjercicio(tipo,operacion)
{
    if(tipo == 'Ejercicio')
    {
        ej = operacion
        per = $("#Periodo").val()
    }
    else
    {
        ej = $("#NameExercise").val()
        per = operacion
    }
	var siPuede = 1
	if($("#diferencia").html() != '$0.00')
	{
		var c = confirm("El periodo actual no esta cuadrado, aun asi desea cambiar?");
		if(!c)
		{
			siPuede = 0;
		}
	}
	if(siPuede)
	{
	$.post("ajax.php?c=CaptPolizas&f=CambioEjerciciosession",
 		 {
    		Periodo: per,
    		NameEjercicio: ej
  		 },
  		 function()
  		 {
  		 	location.reload();
  		 });
	}
}

function valoresConf()
{
   if(confirm("Seguro que quiere reestablecer el periodo y ejercicio de la configuracion?"))
   {
     $.post("ajax.php?c=CaptPolizas&f=ejercicioactual",
         function()
         {
            location.reload();
         });
   } 
}
</script>
<style>
.captura td
{
	width:265px;
	height:30px;
}

.capturaPoliza td, th
{
	width:158px;
	height:30px;
	text-align: center;
	border:1px solid #BDBDBD;
}

.over{
background-color:#91C313;
color:#FFF;
}
.out{
background-color:;
color:;
}

.flecha
{
	border:0px;
	vertical-align:middle;
	width:11px;
}

#buscar
{
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
-o-border-radius: 4px;
border-radius: 4px;
}

</style>
</head>
<body>
<form name='newCompany' method='post' action='' onsubmit='return validaciones(this)'>
<div id='title'>Polizas del periodo</div>
<table>
	<tr><td>Nombre de la organizaci&oacute;n: </td><td><input type='text' class="nminputtext" name='NameCompany' size='50' readonly value='<?php echo $Ex['nombreorganizacion']; ?>'></td></tr>
	<tr><td>Titulo del Ejercicio: </td><td><input type='hidden' name='IdExercise' id='IdExercise' size='50' value='<?php echo $Ex['IdEx']; ?>'><input type='text' class="nminputtext" name='NameExercise' id='NameExercise' size='50' readonly value='<?php echo $Ex['EjercicioActual']; ?>'></td></td>
</table>
<div class='lateral'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Datos del Ejercicio</div>
	<table border=0>
		
		<tr>
			<?php 
			$InicioEjercicio = explode("-",$Ex['InicioEjercicio']); 
			$FinEjercicio = explode("-",$Ex['FinEjercicio']); 
			?>
		<td style='width:400px;height:30px;'><b>Ejercicio Vigente:</b> 
			<?php
			if($Ex['PeriodosAbiertos'])
				{
					if($Ex['EjercicioActual'] > $firstExercise)
					{
						?><a href="javascript:cambioPeriodoEjercicio('Ejercicio',<?php echo $Ex['EjercicioActual']-1; ?>);" title='Ejercicio Anterior'><img class='flecha' src='images/flecha_izquierda.png'></a>
				<?php }
				} ?>
	
			del (<b><?php echo $InicioEjercicio['2']."-".$InicioEjercicio['1']."-".$Ex['EjercicioActual']; ?></b>) al (<b><?php echo $FinEjercicio['2']."-".$FinEjercicio['1']."-".$Ex['EjercicioActual']; ?></b>)
			<?php if($Ex['PeriodosAbiertos'])
				{
					if($Ex['EjercicioActual'] < $lastExercise)
					{
						?><a href="javascript:cambioPeriodoEjercicio('Ejercicio',<?php echo $Ex['EjercicioActual']+1; ?>)" title='Ejercicio Siguiente'><img class='flecha' src='images/flecha_derecha.png'></a>
				<?php }
				} ?> </td>

			<td><b>Periodo actual:</b> 

		<?php 
				if($Ex['PeriodosAbiertos'])
				{
					if($Ex['PeriodoActual']>1)
					{
						?><a href="javascript:cambioPeriodoEjercicio('Periodo',<?php echo $Ex['PeriodoActual']-1; ?>);" title='Periodo Anterior'><img class='flecha' src='images/flecha_izquierda.png'></a>
				<?php }
				} ?>  
				<label id='PerAct'><?php echo $Ex['PeriodoActual']; ?></label><input type='hidden' id='Periodo' value='<?php echo $Ex['PeriodoActual']; ?>'> del (<label id='inicio_mes'></label>) al (<label id='fin_mes'></label>)  
			<?php if($Ex['PeriodosAbiertos'])
				{
					if($Ex['PeriodoActual']<13)
					{
						?><a href="javascript:cambioPeriodoEjercicio('Periodo',<?php echo $Ex['PeriodoActual']+1; ?>)" title='Periodo Siguiente'><img class='flecha' src='images/flecha_derecha.png'></a>
				<?php }
				} ?> </td>

		</tr>
		
	</table>
    <?php
        echo $botonReestablecer;
    ?>
</div>
<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Movimientos del Periodo</div>
	<table class='captura'>
		
		<tr>
			<td>Cargos: <b>$<?php echo number_format($Cargos['Cantidad'], 2); ?></b></td><td>Abonos: <b>$<?php echo number_format($Abonos['Cantidad'], 2); ?></b></td><td>Diferencia: <b style='color:red;' id='diferencia'>$<?php echo number_format($Cargos['Cantidad']-$Abonos['Cantidad'], 2); ?></b></td>
		</tr>
		
		
	</table>
</div>
<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Lista de Polizas del Periodo &nbsp;&nbsp;<input type='button' class="nminputbutton_color2" id='nuevapolizaboton' value='Nueva Poliza' title='ctrl+enter' onclick='nuevaPoliza(<?php echo $Ex['IdOrganizacion']; ?>,<?php echo $Ex['IdEx']; ?>,<?php echo $Ex['PeriodoActual']; ?>)'><input type='text'  class="nmcatalogbusquedainputtext" id='buscar' name='buscar' placeholder='Buscar'>	
	</div>
	<table class='capturaPoliza'>
	</table>
</div>

</form>
<body>
</html>