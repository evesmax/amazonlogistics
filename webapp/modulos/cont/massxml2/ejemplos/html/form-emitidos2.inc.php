<?php
$meses = array(
  '1'=>'Enero',
  '2'=>'Febrero',
  '3'=>'Marzo',
  '4'=>'Abril',
  '5'=>'Mayo',
  '6'=>'Junio',
  '7'=>'Julio',
  '8'=>'Agosto',
  '9'=>'Septiembre',
  '10'=>'Octubre',
  '11'=>'Noviembre',
  '12'=>'Diciembre'
);
$dias = range(1, 31);
$anios = [];
require '../../../../../netwarelog/webconfig.php';
$conNMDB = mysqli_connect($servidor,$usuariobd,$clavebd, $bd);
$strSql = "select NombreEjercicio from cont_ejercicios";
$data = mysqli_query($conNMDB,$strSql);
if(!$data or $data->num_rows == 0){
    $strSql = "SELECT nombre AS NombreEjercicio FROM app_ejercicios";
    $data = mysqli_query($conNMDB,$strSql);
}
while($row = $data->fetch_array()){
  $anios[]= $row['NombreEjercicio'];
}
?>
<form class="form-inline" method="POST" id="emitidos-form">
  <input type="hidden" name="accion" value="buscar-emitidos" />
  <input type="hidden" name="sesion" class="sesion-ipt" />
  <div class="form-group">
    <label for="dia">Día</label>
    <select class="form-control" id="dia_e" name="dia_e">
    <?php
    echo '<option value="0">Todos</option>';
    foreach ($dias as $value) {
      echo '<option value="'.$value.'">'.$value.'</option>';
    } ?>
    </select>
  </div>
  <div class="form-group">
    <label for="mes">Mes</label>
    <select onchange = 'getday(2)' class="form-control" id="mes_e" name="mes_e">
    <?php foreach ($meses as $key => $value) {
      echo '<option value="'.$key.'">'.$value.'</option>';
    } ?>
    </select>
  </div>
  <div class="form-group">
    <label for="anio">Año</label>
    <select onchange = 'getday(2)' class="form-control" id="anio_e" name="anio_e">
    <?php foreach ($anios as $value) {
      echo '<option value="'.$value.'">'.$value.'</option>';
    } ?>
    </select>
  </div>
  <button type="submit" class="btn btn-primary" id='buscar_emi'>Buscar</button>
  <!--<img src='../../../../cont/images/images.jpg' class="img" onclick = "fnExcelReport('tabla-emitidos')">-->
</form>