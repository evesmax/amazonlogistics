<?php
// Valida que existan reservaciones
if (empty($_SESSION['produccion_acciones'])) { ?>
<br /><br />
<blockquote style="font-size: 16px">
<p>
Seleccione un <strong>"producto"</strong>
y	asígnele <strong>"procesos de producción"</strong> para agregarlos.
</p>
</blockquote><?php

return 0;
} ?>


<br /><?php


// Insumos normales
if (!empty($_SESSION['produccion_acciones'])) { ?>
<table id="tabla_insumos_agregados" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;">
<thead>
<tr>

<!--<th align="center"><strong>Cantidad</strong></th>-->
<th align="center"><strong>Accion</strong></th>
<th align="center"><strong>Estatus</strong></th>
<th align="center"><strong>Acción</strong></th>
<th align="center"><strong>Tipo</strong></th>
<th align="center"><strong>Alias</strong></th>
<th align="center"><strong>Actividad</strong></th>
<th><strong>Tiempo / Piezas</strong></th>
<!--<th align="center"><strong>Costo Proveedor</strong></th>
<th align="center"><strong>Costo Preparacion</strong></th>-->
</tr>
</thead>
<tbody><?php

$p=0;
$rdata=explode('#.#.#', $alias);


foreach ($_SESSION['produccion_acciones'] as $k => $v) {
if($alias!=''){
if($rdata[$p]!=''){
$rexp=explode('#.,#', $rdata[$p]);
$rid=$v['id'];
$rstatus=$rexp[1];
$rtipo=$rexp[2];
$ralias=$rexp[3];
$ract=$rexp[4];
$rhr=$rexp[5];
$eti=$rexp[6];
$agru=$rexp[7];
}else{
$rid=$v['id'];;
$rstatus=1;
$rtipo='';
$ralias=$v['nombre'];
$ract=1;
$rhr='';
$eti='';
$agru='';
}
}else{
$rid=$v['id'];
$rstatus=1;
$rtipo='';
$ralias=$v['nombre'];
$ract=1;
$rhr='';
$eti='';
$agru='';
}

if($rstatus==1){
$attsta=" checked ";
}else{
$attsta="";
}

if($ract==1){
$actividad='<input id="alias_hr" class="alias_hrs" style="width: 100%;" type="text" value="'.$rhr.'" />';
}
if($ract==2){
$actividad='<input id="alias_piezas" class="alias_piezas" style="width: 100%;" type="text" value="'.$rhr.'" />';
}


$exp=explode('_', $rid);
$texp=$exp[0];

$p++;

//Agrupados
if($agrupados!=0 && ($texp==11 or $rid==11)  && $pon2==1){
$td_etiqueta2='<td id="agru"><select class="form-control"><option value="0">- Seleccione agrupacion -</option>';
$cad2='';
foreach ($agrupados as $ke2 => $ve2) {
$cad2.='<option value="'.$ve2['id'].'">'.$ve2['nombre_agrupacion'].'</option>';
}
$td_etiqueta2.=$cad2.'</select></td>';
}else{
$td_etiqueta2='<td id="agru"></td>';
}

if($agru!='' && $pon2==1 && ($texp==11 or $rid==11)){
$td_etiqueta2='<td id="agru"><select class="form-control"><option value="0">- Seleccione agrupacion -</option>';
$cad2='';
foreach ($agrupados as $ke2 => $ve2) {
if($ve2['id']==$agru){
$cad2.='<option selected="selected" value="'.$ve2['id'].'">'.$ve2['nombre_agrupacion'].'</option>';
}else{
$cad2.='<option value="'.$ve2['id'].'">'.$ve2['nombre_agrupacion'].'</option>';
}
}
$td_etiqueta2.=$cad2.'</select></td>';
}

//Etiquetas
if($etiquetas!=0 && $rid==16 && $pon==1){
$td_etiqueta='<td id="eti"><select class="form-control"><option value="0">- Seleccione Etiqueta -</option>';
$cad='';
foreach ($etiquetas as $ke => $ve) {
$cad.='<option value="'.$ve['id'].'">'.$ve['nombre_etiqueta'].'</option>';
}
$td_etiqueta.=$cad.'</select></td>';
}else{
$td_etiqueta='<td id="eti"></td>';
}


if($eti!='' && $pon==1 && $rid==16){
$td_etiqueta='<td id="eti"><select class="form-control"><option value="0">- Seleccione Etiqueta -</option>';
$cad='';
foreach ($etiquetas as $ke => $ve) {
if($ve['id']==$eti){
$cad.='<option selected="selected" value="'.$ve['id'].'">'.$ve['nombre_etiqueta'].'</option>';
}else{
$cad.='<option value="'.$ve['id'].'">'.$ve['nombre_etiqueta'].'</option>';
}
}
$td_etiqueta.=$cad.'</select></td>';
}

//Etiquetas
// if($rid==16){
// $td_etiqueta='<td><select class="form-control"><option value="1">Tipo de etiqueta</option></select></td>';
// }else{
// $td_etiqueta='<td>&nbsp;</td>';
// }

?>
<tr id="<?php echo $rid; ?>"  style="cursor: pointer;">

<!-- Guarda los opcionales al cargar -->
<td align="center" class="leerdato"><?php echo $texp ?></td>
<td><input id="sta" type="checkbox" name="sta" value="1" style="cursor:pointer;" <?php echo $attsta; ?>>Activo</td>
<td><?php echo $v['nombre'] ?></td>
<td id="tdtipo">
	<?php if ($texp==1 || $texp==2  || $texp==6  || $texp==7 || $texp==8 || 
			  $texp==9 || $texp==10 || $texp==13 || $texp==17){ ?>
		<select id="tipo" class="form-control tiposecuencual">
			<option value="1" <?php if($rtipo==1){ ?> selected="selected" <?php } ?> >Secuencial</option>
		</select>
	<?php }else if ($texp==3 || $texp==4 ||  $texp==5 || $texp==11 || $texp==15 || $texp==16 || $texp==18 || $texp==14 || $texp==19){?>
		<select id="tipo" class="form-control tiposecuencual">
			<option value="1" <?php if($rtipo==1){ ?> selected="selected" <?php } ?> >Secuencial</option>
			<option value="2" <?php if($rtipo==2){ ?> selected="selected" <?php } ?> >No secuencial</option>
		</select>

	<?php  } ?>
<!-- <select id="tipo" class="form-control">
<option value="1" <?php if($rtipo==1){ ?> selected="selected" <?php } ?> >Secuencial</option>
<option value="2" <?php if($rtipo==2){ ?> selected="selected" <?php } ?> >No secuencial</option>
</select> -->
</td>
<td><input class="form-control" id="alias" style="width: 100%;" type="text" value="<?php echo $ralias ?>" /></td>
<td id="tdactividad">
<select id="actividad_<?php echo $rid; ?>" class="form-control" onchange="cambiaActividad('<?php echo $rid; ?>');">
<option value="1" <?php if($ract==1){ ?> selected="selected" <?php } ?> >Duracion</option>
<option value="2" <?php if($ract==2){ ?> selected="selected" <?php } ?> >Piezas</option>
</select>
</td>
<td id="actinput_<?php echo $rid; ?>" align="center">
<?php echo $actividad; ?>
</td>
<?php echo $td_etiqueta; ?>
<?php echo $td_etiqueta2; ?>
</tr><?php 
} ?>

</tbody>
</table><?php
}

// Insumos preparados
?>
<input type="hidden" name="prueba" id="prueba">
<script>
// Actualiza el precio de venta
$('#precio_venta').val(<?php echo $total+$total_preparado ?>);

// calcula la ganancia
//recetas.calcular_ganancia({porcentaje:$('#margen_ganancia').val()});
</script>