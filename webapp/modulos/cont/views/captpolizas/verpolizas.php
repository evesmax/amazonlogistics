
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script src='../../libraries/jquery.min.js' type='text/javascript'></script>
<script language='javascript' src='../../libraries/bootstrap/dist/js/bootstrap.min.js'></script>
<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript' src='../../libraries/dataTable/js/datatables.min.js'></script>
<script language='javascript' src='../../libraries/dataTable/js/dataTables.bootstrap.min.js'></script>
<script src="js/select2/select2.min.js"></script>
<link rel="stylesheet" href="../../libraries/dataTable/css/datatables.min.css">
<link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />

<script language='javascript'>
$(document).ready(function(){
  $('#sel_periodo').val($('#Periodo').val());
  $('#sel_ejercicio').val($('#NameExercise').val());
  
  sel_ejercicio = $('#sel_ejercicio');
  sel_ejercicio.change(function(){
    cambioPeriodoEjercicio('Ejercicio',sel_ejercicio.val());
  });
  
  sel_periodo = $('#sel_periodo');
  sel_periodo.change(function (){
    cambioPeriodoEjercicio('Periodo',sel_periodo.val());
  });
});
$(function()
{
	dias_periodo()
	$.post("ajax.php?c=CaptPolizas&f=GetPolizas",
 		 {
    		Ejercicio: $('#IdExercise').val(),
    		Periodo: $('#Periodo').val()
  		 },
  		 function(data)
  		 {
  		 	$('#resultados').html(data);
            var cookieValue = document.cookie.replace(/(?:(?:^|.*;\s*)search\s*\=\s*([^;]*).*$)|^.*$/, "$1");
            $('#listaPoliza').DataTable( {
              language: {
                  search: "Buscar:",
                  lengthMenu:"Mostrar _MENU_ elementos",
                  zeroRecords: "No hay datos.",
                  infoEmpty: "No hay datos que mostrar.",
                  info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                  paginate: {
                      first:      "Primero",
                      previous:   "Anterior",
                      next:       "Siguiente",
                      last:       "Último"
                  }
               },
                  "oSearch": {
                    "sSearch": cookieValue
                    }
          });
  		 	$('#tablaexcel').html(data);
  		});
$('#listaPoliza').on('search.dt', function() {
    var value = $('.dataTables_filter input').val();
    if(value.length > 0){
        document.cookie = "search="+value;   
    }
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
    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
    $("#saldos").select2({width:"200px"});
    window.saldos = 0;
});


function nuevaPoliza(idorg,idex,per)
{
  if(parseInt(per) == 13 && !window.saldos)
  {
    functsaldos(idorg,idex,per)
  }
  else
  {
    $.post("ajax.php?c=CaptPolizas&f=CreateNewPoliza",
         {
            Organizacion: idorg,
            Ejercicio: idex,
            Periodo: per,
            saldos: window.saldos
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
                  console.log(data)
                  alert("Para generar la poliza del periodo 13 es necesario configurar la cuenta de saldos en la pantalla de Asignacion de Cuentas.");
                }
           });
  }
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
	if($("#diferencia").html() != '$0.000')
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
function generaexcel()
{
	//$('#edicion,#thoculto').hide();
	$('#tablaexcel td:nth-child(7)').html('');
	$('#tablaexcel th:nth-child(7)').html('');
	$().redirect('views/fiscal/generaexcel.php', {'cont': $('#excel').html(), 'name': $('#title').html()});

}

function check_pol(c)
 {
    if(c.checked)
    {
        $("#xls_obj").modal({backdrop:"static"});
    }
    else
    {
        $("#xls_obj").modal("hide");
    }
 }
 function validar_xls()
    {
        var extension = $("#polizas_xls").val()
        extension = extension.split('.')
        if(!$("#polizas_xls").val() || extension[1] != 'xls')
        {
            alert('Es necesario agregar el layout (descargar el archivo xls) para generar este proceso')
            return false
        }
        else
        {
            $("#polizas_btn").attr('disabled',true)
            $("#cargando_lay").css('display','inline')
        }
    }

    function functsaldos(idorg,idex,per)
    {
      $.post("ajax.php?c=CaptPolizas&f=cuentaSaldos",
         function(data)
         {
            $("#saldos").html(data);
            $("#saldos").val(0).trigger("change")
         });
      $("#saldos13").modal("show");
      $("#saldos").attr("idorg",idorg).attr("idex",idex).attr("per",per)
    }

    function cancelar_saldos()
    {
      $("#saldos").val(0).trigger("change")
      $("#saldos13").modal("hide");
    }

    function guardar_saldos()
    {
      window.saldos = parseInt($("#saldos").val());

      if(parseInt(window.saldos))

        nuevaPoliza($("#saldos").attr('idorg'),$("#saldos").attr('idex'),$("#saldos").attr('per'));
      else
        alert("Elija una cuenta de saldos.")
    }
	function irdocumento(link){
		window.parent.preguntar=false;
		window.parent.quitartab('tb1828',1828,'Documento Edicion');
		window.parent.agregatab('../../modulos/bancos/'+link,'Documento Edicion','',1828);
		window.parent.preguntar=true;
	}
</script>
<style>
.titulo
{
    font-size:16px;
    background-color: #DDD;
    margin-top:10px;
    height:30px;
    padding-top:4px;
}

.capturaPoliza td, th
{

	height:30px;
	text-align: center;
}

.flecha
{
	border:0px;
	vertical-align:middle;
	width:11px;
}

/*#xls_obj
{
    float:left;
    position:absolute;
    background-color:#BDBDBD;
    width:400px;
    height:150px;
    border:1px solid white;
    box-shadow: 2px 2px 5px #000000;
    margin-left:2px;
    margin-top:0px;
}

#cargando_lay
{
    font-size:8px;
    color:red;
}*/

@media print
{
	#imprimir,#sumMov,#ejer,#orga,#edicion,#thoculto,.nmcatalogbusquedatit,img
	{
		display:none;
	}
}


</style>
<a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a>
<img src="images/images.jpg" title="Exportar a Excel" onclick="generaexcel()" width="25px" height="25px">
<div class="container">
<div class="row">
    <div class="col-xs-12 col-md-12 col-md-offset-1"><h3>Polizas del periodo</h3></div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-1">Nombre de la organizaci&oacute;n:</div><div class="col-xs-12 col-md-4"><input type='text' class="form-control" name='NameCompany' size='50' readonly value='<?php echo $Ex['nombreorganizacion']; ?>'></div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-2 col-md-offset-1">Titulo del Ejercicio:</div><div class="col-xs-12 col-md-4"><input type='hidden' name='IdExercise' id='IdExercise' size='50' value='<?php echo $Ex['IdEx']; ?>'><input type='text' class="form-control" name='NameExercise' id='NameExercise' size='50' readonly value='<?php echo $Ex['EjercicioActual']; ?>'></div>
</div>
    <div class="col-xs-12 col-md-10 col-md-offset-1 titulo"> Datos del Ejercicio</div>
    <br><br><br>
    <div class="row">
      <div class='col-xs-12 col-md-4 col-md-offset-1' id="ejer" style="padding-right: 0; max-width: 310px;">
          <?php
          $InicioEjercicio = explode("-",$Ex['InicioEjercicio']);
          $FinEjercicio = explode("-",$Ex['FinEjercicio']);
          ?>
        <b>Ejercicio Vigente:</b>
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
            } ?>
    </div>
    <div class="col-xs-12 col-md-1">
    <?php if ($pa['PeriodosAbiertos'] != 0): ?>
      <select id="sel_ejercicio" title="Seleccione un ejercicio">
      <?php foreach ($allExercises as $exercise) {
        $titulo = $exercise['nombreEjercicio'];
        //echo($titulo);
        echo("<option value='$titulo'>$titulo</option>");
      } ?>
      </select>
    <?php endif ?>
    </div>
    <div class='col-xs-12 col-md-4' style="padding-right: 0; max-width: 340px;">
          <b>Periodo actual:</b>

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
            } ?>

    </div>
    <div class="col-xs-12 col-md-1">
    <?php if ($pa['PeriodosAbiertos'] != 0): ?>
      <select id="sel_periodo" title="Seleccione un periodo">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
      </select>
    <?php endif ?>
    </div>
    <div class='col-xs-12 col-md-5 col-md-offset-1'>
      <?php
        echo $botonReestablecer;
      ?>
    </div>
    </div>
    
    <div class="col-xs-12 col-md-10 col-md-offset-1 titulo"> Movimientos del Periodo</div>
    <div class="col-xs-4 col-md-3 col-md-offset-1" style='text-align:center;'>Cargos: <b>$<?php echo number_format($Cargos['Cantidad'], 3); ?></b></div>
    <div class="col-xs-4 col-md-3 " style='text-align:center;'>Abonos: <b>$<?php echo number_format($Abonos['Cantidad'], 3); ?></b></div>
    <div class="col-xs-4 col-md-4 " style='text-align:center;'>Diferencia: <b style='color:red;' id='diferencia'>$<?php echo number_format($Cargos['Cantidad']-$Abonos['Cantidad'], 3); ?></b></div>
    <div class="col-xs-12 col-md-10 col-md-offset-1 titulo" style='height:auto;'>
        Lista de Polizas del Periodo &nbsp;&nbsp;<input type='button' class="btn btn-primary" id='nuevapolizaboton' value='Nueva Poliza' title='ctrl+enter' onclick='nuevaPoliza(<?php echo $Ex['IdOrganizacion']; ?>,<?php echo $Ex['IdEx']; ?>,<?php echo $Ex['PeriodoActual']; ?>)'>
        <?php
        if($tipoinstancia)
            echo "<label style='font-size:12px;'>Cargar polizas mediante layout</label> <input type='checkbox' id='check_xls' onclick='check_pol(this)'>";
        ?>
    </div>
    <div class="col-xs-12 col-md-10 col-md-offset-1 table-responsive" style='background-color:#ddd;margin-top:10px;padding-top:5px;'>
        <table class='capturaPoliza table table-striped table-bordered table-hover' id="listaPoliza">
            <thead>
							<tr style='background-color:#BDBDBD;color:white;font-weight:bold;height:30px;'>
								<th style="width: 38px !important;"># de Poliza</th>
								<th>Tipo Poliza</th>
								<th>Concepto</th>
								<th style="width: 102px !important;">Fecha</th>
								<th>Cargos</th>
								<th>Abonos</th>
								<th id='thoculto'><b style='visibility:hidden;'>*1*</b></th>
							</tr>
						</thead>
            <tbody id='resultados'>

            </tbody>
        </table>
    </div>
</div>

<div id='xls_obj' class="modal fade" tabindex="-1" role="dialog" >
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Cargar Polizas mediante Layout</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <a href='Formato_polizas2.xls'>Descargar Layout</a>
            <hr/>
          </div>
        </div>
        <form action='index.php?c=CaptPolizas&f=GuardaPolXls' method='post' name='archivo_xls' enctype="multipart/form-data" id='xls_arch' onsubmit='return validar_xls()'>
          <div class="row">
            <div class="col-md-12">
              <input type='file' id='polizas_xls' name='polizas_xls' >
              <input type='hidden' name='xls_ejercicio' value='<?php echo $Ex['EjercicioActual']; ?>' >
            </div>
          </div>
          <div class="row" id='cargando_lay' style='display:none;'>
            <div class="col-md-12">
              <label style="color:green;">Cargando...</label>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <div class="col-md-6 col-md-offset-6">
          <input class="btn btn-primary btnMenu" type='submit' id='polizas_btn' value='Cargar'>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="excel" style="display:none">
  	<div ><b>Lista de Polizas del Periodo</b> </div><br></br>
  	<table id="tablaexcel"></table>
</div><a href="" onclick=""></a>

<div class="modal fade bs-cuentaSaldosP13-modal-sm" id='saldos13' tabindex="-1" role="dialog" aria-labelledby="cuentaSaldosP13">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header panel-heading" style='background-color:#337ab7;color:#FFFFFF;'>
                <center><h4 id="modal-label">Elija la cuenta de saldos</h4></center>
            </div>
      <div class="modal-body well">
        <div class="row">
         <center>
           <select name='saldos' id='saldos'>
           </select>
         </center>
        </div>
      </div>
            <div class="modal-footer">
                <button class='btn btn-default btn-sm' onclick="guardar_saldos()">Guardar</button><button class='btn btn-default btn-sm' onclick="cancelar_saldos()">Cancelar</button>
            </div>
    </div>
  </div>
</div>
