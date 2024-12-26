<?php
  $semana = strftime('%V');
  $ano = NumeroSemanasTieneUnAno(date('Y'));
  week_bounds(date('Y-m-d'), $start, $end);

  $SQL = "SELECT a.*, concat('DEST-',a.id,' -  ',b.nombre,' ',b.paterno,' ',b.materno) nombre FROM constru_altas a inner join constru_info_tdo b on b.id_alta=a.id where a.id_obra='$idses_obra' and a.borrado=0 AND a.id_tipo_alta=2;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $maestros[]=$row;
    }
  }else{
    $maestros=0;
  }

  $SQL = "SELECT a.id, a.nombre from constru_especialidad a inner join constru_agrupador b on b.id=a.id_agrupador
 where b.id_obra='$idses_obra' group by a.nombre";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $areas[]=$row;
    }
  }else{
    $areas=0;
  }


?>
<div class="row">&nbsp;</div>
<div class="panel panel-default" >
  <!-- Panel Heading -->
  <div class="panel-heading">
  <div class="panel-title">Control de indirectos</div>
  </div><!-- End panel heading -->

  <!-- Panel body -->
  <div class="panel-body" >
      <div class="row">
        <div class="col-sm-3">
          <label>Mes:</label>
          <select class="form-control" id="mes">
            <option selected="selected" value="0">Seleccione un mes</option>
            <?php 
            for($x=1; $x<=12; $x++){ ?>
              <option value="<?php echo $x; ?>">Mes <?php echo $x; ?></option>
           <?php } ?>
          </select>
        </div>
        <div class="col-sm-3" style="padding-top: 25px;">
           <button class="btn btn-primary btn-xm" onclick="controlind();"> Ver</button>

        </div>
      </div>
      
  </div><!-- ENd panel body -->
</div>



<div class="row">
  <div class="col-sm-12" id="estdestajista">
    
  </div>
</div>


