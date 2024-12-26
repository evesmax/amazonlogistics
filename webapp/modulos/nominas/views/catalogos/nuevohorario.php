<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/horarios.js"></script>
	<link href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
	<script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script> 
	<title>Horarios</title>
</head>

<script>
	$(document).ready(function(){   diasclick();
		<?php 
		if(isset($datos)){
			$funcion = "2"; 
			$arrayDatos = array();
			for($i=0; $i< $datos->num_rows; $i++) {
				$objdato =  $datos->fetch_object();   
				$arrayDatos[$objdato->dia] = $objdato;
			}					
		}else{
			$funcion = "1"; 
		}
		?>
});
</script>
<body>
	<div class="container well" style="width: 98%;">	
	<input type="hidden" name="" id="diaguardar">
<input type="hidden" value="<?php echo $funcion; ?>" id="funcion"/>
<input type="hidden" value="<?php echo $encadatos->idhorario ?>" id="idhorario">
		<div   class="row" style="padding-left: 15px;">
			<div>
				<button class="btn btn-default" onclick="atraslistado()">
					<i class="fa fa-arrow-left" aria-hidden="true"></i>
					Regresar
				</button>
				<button type="button" class="btn btn-primary" id="almacenhrs" style="text-align:center" data-loading-text="<i class='fa fa-refresh fa-spin'</i>"><span class="glyphicon glyphicon-floppy-disk"></span>Guardar
				</button>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">Agregar horarios.</div>
			<div class="panel-body">
				<div class="alert alert-warning table-responsive">
					<div class="col-md-5 form-inline">
						Nombre del horario:
						<input type="text" class="form-control" style="width: 60%;" id="nombrehorario" value="<?php if(isset($datos)){ echo $encadatos->nombrehorario;}?>"> <b style="color:red">*</b>
					</div>
					<div class="col-md-7 form-inline">
						Tolerancia: 
						<input type="number" class="form-control" id="tolerancia" value="<?php if(isset($datos)){ echo $encadatos->toleranciaentrada;}?>"> <b style="color:red">*</b>
					</div>
				</div>

				<div class="alert alert-info">
					<a class="alert-info glyphicon glyphicon-info-sign"></a>
					"Si marca opcional sera un día fuera de horario donde el empleado podrá checar en el reloj y se considerará  para tiempo extra."		
				</div>
				<?php if ($funcion==2){ ?>
				<!-- <div class="alert alert-danger table-responsive" style="height:auto;">
						<a class="alert-danger glyphicon glyphicon-info-sign"></a>
						Seleccione el día para actualizar.
					</div> -->			
				<?php }?>			 
				<div class="alert alert-info table-responsive" style="overflow-x: scroll;" width="90%;"> 
					<?php $semana = array(
						"Lunes",
						"Martes",
						"Miercoles",
						"Jueves",
						"Viernes",
						"Sabado",
						"Domingo"
						);?>

						<table class="table table-hover table-bordered table-responsive table-bordered dt-responsive nowrap " style="color: black;width: 100%;" id="tablahoras">
							<thead>
								<tr style="background-color: rgb(180,191,193);text-align:center;">
									<td colspan="5"></td>
									<td colspan="2">SALIDA COMIDA</td>
									<td colspan="3"></td>
								</tr>
								<tr style="background-color: #c8cec5;"> 
									<td></td>  
									<td>Día</td>
									<td>Hora Entrada</td>
									<td>Hora Salida</td>
									<td>Come</td>
									<td>Checa Comida</td>
									<td>Desde</td>
									<td>Hasta</td>
									<td>Minutos comida</td>
									<td>Día opcional</td>
								</tr> 
							</thead>
							<tbody>
								<?php 
								foreach ($semana as $dia) {
         			 // echo  $arrayDatos[ substr($dia,0,3) ]->idhrsdetalle.'-'.$arrayDatos[ substr($dia,0,3) ]->dia;
					$datoActual =  $arrayDatos[ substr($dia,0,3) ];?>

					<tr <?php if((substr($dia,0,3) == $datoActual->dia)){?> style='background-color: #daa0a0;'  class="selected" <?php  } ?>>
					<td >
					<input type='checkbox' onclick="diasclick()" id="<?php echo $dia;?>" style='width:50px;height:20px;' class='dia'  name="dia" value="<?php echo $dia;?>">
					<script>
							<?php if((substr($dia,0,3) == $datoActual->dia) ){?>
						$("#<?php echo $dia;?>").attr("checked",true);
					<?php } ?>
			
					</script>
					</td>
					<td>&nbsp;&nbsp;<?php echo $dia;?></td>
					<td>
					<input type='time' class='tabledata form-control onlynumb entrada_<?php echo $dia;?>' style="width: 120px;" value="<?php if(isset($datoActual)) { echo $datoActual->horaentrada;}?>" id="<?php echo $dia;?>"></td>
					<td>
					<input type='time'  class='tabledata form-control onlynumb salida_<?php echo $dia;?>' style="width: 120px;"  value="<?php if(isset($datoActual)){ echo $datoActual->horasalida;}?>">
					</td>
					<td class='tabledata'>
					<input type="radio" name="radio_<?php echo $dia;?>" value="1" class="tabledata  radio_<?php echo $dia;?>"
					<?php if( $datoActual->come ==1) echo " checked='checked'";?> style="width:30px !important;height:20px !important;">Si
					<input type="radio" name="radio_<?php echo $dia;?>" value="0" class="tabledata  radio_<?php echo $dia;?>"
					<?php if( $datoActual->come =='0') echo " checked='checked'";?> style="width:30px !important;height:20px !important;">No
					<input   type="hidden" name="radiosel" id="radiosel_<?php echo $dia;?>" class="radiosel_<?php echo $dia;?> tabledata" value="<?php if(isset($datoActual)){ echo $datoActual->come;}?>">
					</td>
					<td>
					<input type="radio" name="radio2_<?php echo $dia;?>" value="1" class="tabledata  radio2_<?php echo $dia;?>"
					<?php if($datoActual->checacome ==1) echo " checked='checked'";?> style="width:30px !important;height:20px !important;">Si
					<input type="radio" name="radio2_<?php echo $dia;?>" value="0" class="tabledata  radio2_<?php echo $dia;?>"
					<?php if($datoActual->checacome =='0') echo " checked='checked'";?> style="width:30px !important;height:20px !important;">No
					<input  type="hidden" class="radioseld2_<?php echo $dia;?> tabledata onlynumb checacome_<?php echo $dia;?>" value="<?php if(isset($datoActual)){ echo $datoActual->checacome;}?>">
					</td>
					<td><input type='time'  class='tabledata form-control onlynumb desdesalidacomida_<?php echo $dia;?>' style="width: 120px;" value="<?php if(isset( $datoActual)) { echo $datoActual->desdesalidacomida;}?>">
					</td>
					<td><input type='time'   class='tabledata form-control onlynumb hastasalidacomida_<?php echo $dia;?>' style="width: 120px;" value="<?php if(isset($datoActual)){ echo $datoActual->hastasalidacomida;}?>">
					</td>	
					<td><input type='number' class='tabledata form-control mincomida_<?php echo $dia;?>' style="width: 120px;" value="<?php if(isset($datoActual)){ echo $datoActual->mincomida;}?>">
					</td>
					<td>
					<input type="radio" name="radio3_<?php echo $dia;?>" value="1" class="tabledata  radio3_<?php echo $dia;?>"
					<?php if($datoActual->opcional ==1) echo " checked='checked'";?> style="width:30px !important;height:20px !important;">Si
					<input type="radio" name="radio3_<?php echo $dia;?>" value="0" class="tabledata  radio3_<?php echo $dia;?>"
					<?php if($datoActual->opcional =='0') echo " checked='checked'";?> style="width:30px !important;height:20px !important;">No
					<input  type="hidden" class="radioseld3_<?php echo $dia;?> tabledata onlynumb opcional_<?php echo $dia;?>" value="<?php if(isset($datoActual)){ echo $datoActual->opcional;}?>">
					</td>
					</tr>	
					<?php }?>	
						
			</tbody>			
</table>
</div>
</div>
</div>
</div>
</body>
</html>
<script> 
	function diasclick(){
	
	$(".dia").each(function(){
// 		
// console.log($(this));
// alert($(this).attr('value'));


if($(this).prop('checked')){
$(this).parents("tr").addClass("selected");
$(".selected").css("background-color", "#daa0a0");
var	day = $(this).attr('value');

if ($(".radiosel"+"_"+day).val()=="0") {
	
}
$(".radio"+"_"+day).click(function(){
var radioval=$(this).val();
$(".radiosel"+"_"+day).val(radioval);

if ($(".radiosel"+"_"+day).val()=='1' || $(".radioseld2"+"_"+day).val()=='1') {
	$(".desdesalidacomida"+"_"+day).prop('disabled',false);
$(".hastasalidacomida"+"_"+day).prop('disabled',false);
$(".mincomida"+"_"+day).prop('disabled',false);
	
}

else{
$(".desdesalidacomida"+"_"+day).prop('disabled',true);
$(".hastasalidacomida"+"_"+day).prop('disabled',true);
$(".mincomida"+"_"+day).prop('disabled',true);
}
});

$(".radio2"+"_"+day).click(function(){
var radioval2=$(this).val();
$(".radioseld2"+"_"+day).val(radioval2);
if ($(".radiosel"+"_"+day).val()=='1' || $(".radioseld2"+"_"+day).val()=='1') {
	$(".desdesalidacomida"+"_"+day).prop('disabled',false);
$(".hastasalidacomida"+"_"+day).prop('disabled',false);
$(".mincomida"+"_"+day).prop('disabled',false);
	
}

else{

$(".desdesalidacomida"+"_"+day).prop('disabled',true);
$(".hastasalidacomida"+"_"+day).prop('disabled',true);
$(".mincomida"+"_"+day).prop('disabled',true);
}

});

$(".radio3"+"_"+day).click(function(){
var radioval3=$(this).val();
$(".radioseld3"+"_"+day).val(radioval3);
if ($(".radiosel"+"_"+day).val()=='1' || $(".radioseld2"+"_"+day).val()=='1') {
	$(".desdesalidacomida"+"_"+day).prop('disabled',false);
$(".hastasalidacomida"+"_"+day).prop('disabled',false);
$(".mincomida"+"_"+day).prop('disabled',false);
}

});


}else{

 $("#diaguardar").val('');
$(this).parents("tr").removeClass("selected");
$(this).parents("tr").removeAttr("style");
}
});

}


</script>
