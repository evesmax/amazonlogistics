<?php
  include('conexiondb.php');

  $fechai=$_POST['fecha1'];
    $fechaf=$_POST['fecha2'];


$SQL = "Select * from constru_notilog where fecha>='$fechai' and fecha<='$fechaf' order by fecha desc";
      $result = $mysqli->query($SQL);
      if($result->num_rows>0) {
        while($row = $result->fetch_array() ) {
          $vernoti[]=$row;
        }
      }else{
        $vernoti=0;
      }    
?>

<table border='2' id='notifi'>
  <tr><th>Fecha</th><th>Observaciones</th><th>Modulo</th></tr>
<?php 
      if($vernoti!=0){
          foreach ($vernoti as $k => $v) { 
            $v['fecha']=substr($v['fecha'],0,10);?>
            <tr><td><?php echo $v['fecha']; ?></td><td><?php echo $v['observaciones'];?></td><td><?php echo $v['modulo'];?></td></tr>
          <?php } }?>
</table>