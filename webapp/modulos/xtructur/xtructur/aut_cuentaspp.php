<?php
$SQL = "SELECT id, semana, fecha FROM constru_bit_remesa a where a.id_obra='$idses_obra' AND estatus>0 order by semana desc;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $remesas[]=$row;
    }
  }else{
    $remesas=0;
  }
?>

<div class="row">&nbsp;</div>
    <div class="panel panel-default" >
      <!-- Panel Heading -->
      <div class="panel-heading">
      <div class="panel-title">Autorizar cuentas por pagar</div>
      </div><!-- End panel heading -->

      <!-- Panel body -->
      <div class="panel-body" >
          <div class="col-sm-6">

    <div class="row">
      <div class="col-sm-6">
        <select class="form-control" id="desta2">
          <option selected="selected" value="0">Seleccione una remesa (pago)</option>
          <?php 
          if($remesas!=0){
            foreach ($remesas as $k => $v) { ?>
              <option value="<?php echo $v['id']; ?>">Remesa semana: <?php echo $v['semana']; ?></option>
            <?php } ?>
          <?php }else{ ?>
            <option value="0">No hay remesas</option>
          <?php } ?>
        </select>
      </div>
      <div class="col-sm-6">
         <button style="width: 100%"  class="btn btn-primary btn-xm pull-right" onclick="verremesa();"> Ver remesa</button>

      </div>
    </div>
  </div>
          
      </div><!-- ENd panel body -->
    </div>




  <div class="row">
  <div class="col-sm-12" id="estdestajista">
  </div>
</div>