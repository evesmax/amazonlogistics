
<div class="table-responsive">
<div id="imprimible" style="padding: 10px;" >
   <table style='font-size:12px;'>
      <tr>
         <td rowspan='4' style='width:200px;padding-right: 10px;'>
            <?php 
            $url = explode('/modulos',$_SERVER['REQUEST_URI']);
            if($logo1 == 'logo.png') $logo1= 'x.png';
            $logo1 = str_replace(' ', '%20', $logo1);
            echo "<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo1 style='width: 200px;height: 45px;'>"; 
            ?>
         </td>
         <td><b><?php echo $infoEmpresa['nombreorganizacion'].' '.$infoEmpresa['RFC']?></b>
         </td>
      </tr>
      <tr>
         <td><b>Reporte de Vacaciones</b></td>
      </tr>
      <tr>
         <td><b>Periodo Analizado: <?php if ($_REQUEST['anioselec']!='') {
            echo $_REQUEST['anioselec']; 
         }else{
            echo "Todos";
         }?></b></td>      
      </tr>
      <tr>
         <td><b>Fecha de emisión: 
            <?php 

            date_default_timezone_set("Mexico/General");

            $hora  = date ("H:i:s");
            $fecha = date ("j/n/Y");

            echo $fecha.' '.$hora;
            ?>
         </b>
      </td>
   </tr>
   <tr>
      <td style="height: 30px;"></td>
   </tr>
</table>

   <?php

   $empleado = 0;
   $anio     = 0;
       
   if($cargarvacaciones->num_rows>0) {

      while($in = $cargarvacaciones->fetch_assoc()){?>
    
         <table class="tablavacaciones table table-striped table-bordered table-responsive table-hover" style="width:100%;font-size: 12px;background-color: white;" border='.1px' bordercolor="#0000FF" cellpadding="2">
            <thead> 
               <tr style="background-color:#B4BFC1;color:#000000;">
                  <td>Empleado</td>
                  <td>Fecha Inicial</td>
                  <td>Fecha Final</td>
                  <td>Días Tomados</td>
                  <td>Antigüedad Años</td>
                  <td>Dias</td>
                  <td>Días que corresponden</td>
                  <td>Días pendientes</td>
                  <td>Días pendientes próximo periodo</td>
                  <td>Año</td>
                  <td>Fecha Alta</td>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td colspan="4">
                  <?php echo $in['codigo']." ".$in['nombreEmpleado']." ".$in['apellidoPaterno']." ".$in['apellidoMaterno'];?>
                  </td>
                  <td><?php  echo $in['AniosAntiguedad'];?></td>
                  <td><?php  echo $in['dias'];?></td>      
                  <td><?php  echo $in['diasporley'];?></td>
                  <td><?php  echo $in['diasSumadosAcumu'];?></td> 
                  <td><?php echo number_format($in['diasquetocan'],1,'.',''); ?></td>  
                  <td><?php echo $in['AnioFecha'];?></td>
                  <td><?php echo date('d-m-Y', strtotime($in['fecha_altabajareingreso']))?></td>
                  </tr> 
                  <?php 

                  $empleado = $in['idEmpleado'];

                  $anio = $in['AnioFecha'];

                  ?>

                  <?php 

                  $detalleVacaciones = $this->ReportesModel->detalleVacaciones($in['idEmpleado'],$in['idtipop'],$in['AnioFecha']);

                  if($detalleVacaciones->num_rows>0) { 

                        $fechaEmpleado = $in['AnioFecha'];
                        while($d = $detalleVacaciones->fetch_assoc()){
                     
                     echo 
                        "<tr> 
                        <td></td>
                        <td>".date('d-m-Y', strtotime($d['fechainicial']))."</td>
                        <td>".date('d-m-Y', strtotime($d['fechafinal']))."</td>
                        <td colspan='7'>".$d['diasvacaciones']." "."Dias"."</td>
                        </tr>";    
                     
                  }
                        
                  }?>
                  </tbody>
                    </table>
                 <!-- </div> -->

                  <?php  }
               
                  }?>


</div>
</div>
   <!-- </div> -->
   <link rel="stylesheet" type="text/css"  href="css/reportevacaciones.css">

