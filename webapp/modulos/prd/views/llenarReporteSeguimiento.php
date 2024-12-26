<div id="imprimible"> 
  <?php
    $dato =0; $idpaso=0;
    if($reporteSeguimiento && $reporteSeguimiento->num_rows>0) {
        while($in = $reporteSeguimiento->fetch_assoc()){
            if ($dato != $in['id_orden_produccion']){
              if ($dato != 0 ){?>
                  </tbody>
                </table>
              </div> 
            <?php 
            } ?>
            <div class='table-responsive alert alert-info'>
              <div class="row">
                <div class="col-sm-12" style="color:#000000;font-weight:bold;">
                  <?php 
                    echo 'Orden de producción: '.' '.$in['id_orden_produccion'];
                  ?>   
                </div> 
                <div class="col-sm-12" style="color:#000000;font-weight:bold;" >
                  <?php echo "Producto: "." ".$in['nombre']; ?>
                </div>
                <div class="col-sm-12" style="color:#000000;font-weight:bold;padding-bottom: 30px;">
                   <?php   echo 'Cantidad: '.' '.$in['cantidad']; ?>  
                </div>
              </div>          
                <table cellpadding='0' class='table tablaseguimiento table-striped table-bordered table-responsive nowrap' width='100%';">
                  <thead> 
                     <tr style='background-color:#B4BFC1;color:#000000;height: 35px;'>
                          <td>Nombre Paso</td>
                          <td>Estatus</td>
                          <td>Pendiente</td>
                          <td>Terminado</td>  
                          <td>Fecha</td>
                      </tr>
                  </thead>
                <tbody>
            <?php 
            }
              if($idpaso != $in['idpaso']){?>
                <tr style='font-weight:bold;'> 
                  <td><?php echo $in['descripcion']; ?></td> 
                  <td></td>
                  <td></td> 
                  <td></td> 
                  <td></td> 
                </tr>
            <?php
              }?>
              <tr>
                <td style="padding-left: 30px;"><?php echo $in['alias'];?></td> 
                <td><?php echo $in['estatus'];?></td> 
                <td><?php echo number_format($in['pendiente'],3,'.',',');?></td> 
                <td><?php echo number_format($in['cantidadpproducida'],3,'.',',');?></td> 
                <td><?php echo $in['fecha_guardado'];?></td> 
              </tr> 
        <?php  
              $dato = $in['id_orden_produccion']; 
              $idpaso = $in['idpaso'];
            }
        ?>

        
      </tbody>
    </table>
    <br>
  </div>
<?php 
}  
  else{?>
     <div class='alert alert-info table-responsive'>
                <table cellpadding='0' class='tablaseguimiento table table-striped table-bordered nowrap table-responsive' width="100%">
                  <thead> 
                     <tr style='background-color:#B4BFC1;color:#000000;'>
                    <td>Nombre Paso</td>
                    <td>Estatus</td>
                    <td>Fecha</td>
                </tr>       
                  </thead>
                </table>
              </div>
<?php  } ?>  <!-- fin del while -->
</div>