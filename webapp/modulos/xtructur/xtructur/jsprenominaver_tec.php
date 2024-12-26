

<?php
$cookie_xtructur = unserialize($_COOKIE['xtructur']);
$obra_ini = $cookie_xtructur['obra_ini'];
$obra_fin = $cookie_xtructur['obra_fin'];

$semana = strftime('%V');
week_bounds(date('Y-m-d'), $start, $end);

$SQL = "SELECT a.*, concat('DEST-',b.id,' -  ',b.nombre,' ',b.paterno,' ',b.materno) nombre FROM constru_altas a inner join constru_info_tdo b on b.id_alta=a.id where a.id_obra='$idses_obra' and a.borrado=0 AND a.id_tipo_alta=2;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $maestros[]=$row;
    }
  }else{
    $maestros=0;
  }

$SQL = "SELECT a.id, concat('NOMINA OFICINA CAMPO / ',a.fecha) as nombre from constru_bit_nominaca a
where a.id_tecnico=2 AND a.id_obra='$idses_obra' and a.borrado=0;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $estimaciones[]=$row;
    }
  }else{
    $estimaciones=0;
  }

?>

<div class="row">&nbsp;</div>
    <div class="panel panel-default" >
      <!-- Panel Heading -->
      <div class="panel-heading">
      <div class="panel-title">Autorizacion prenomina campo</div>
      </div><!-- End panel heading -->

      <!-- Panel body -->
      <div class="panel-body" >
          <div class="row" style="padding-bottom: 25px;">
  <div class="col-sm-3">
    <label>&nbsp;</label>
    <select class="form-control" id="nomi">
      <option selected="selected" value="0">Seleccione</option>
      <?php 
      if($estimaciones!=0){
        foreach ($estimaciones as $k => $v) { ?>
          <option value="<?php echo $v['id']; ?>"><?php echo $v['nombre']; ?></option>
        <?php } ?>
      <?php }else{ ?>
        <option value="0">No hay nominas genradas</option>
      <?php } ?>
    </select>
  </div>
  <div class="col-sm-3">
    <label>&nbsp;</label>
     <button style="width:100%" class="btn btn-primary btn-xm pull-right" onclick="vernomgeneradascampo();"> Ver nomina</button>


  </div>
</div>
          
      </div><!-- ENd panel body -->
    </div>



<div class="row">
  <div class="col-sm-12" id="vernomina">
    
  </div>
</div>

