<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
  <link   rel="stylesheet" type="text/css" href="css/reporteincidencias.css"> 
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
  <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
  <script type="text/javascript" src='js/reporteincidencias.js'></script>
  <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
  <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
  <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
  <link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
  <script src="../../libraries/dataTable/js/datatables.min.js"></script>
  <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
  <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
  <body>
  <div class="container-fluid ocultos" class='encabezado' style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;"><b>Reporte de Incidencias</b></div> 
  <br>
  <div class="container well" style="width: 96%;">
        <fieldset class="scheduler-border">
          <legend align="center" class="scheduler-border">Búsqueda</legend>
            <div class="form-inline" style="text-align: center;">
              <input type="checkbox"  name="checkboxG4" id="mostrarfechas" class="css-checkbox"  checked />
              <label for="mostrarfechas" class="css-label" onclick="activarChecked()">Busqueda por rangos de fechas.</label>
              <input type="checkbox" name="mostrarperiodos" id="mostrarperiodos" class="css-checkbox"/>
              <label for="mostrarperiodos" onclick="activarCheckeddos()" class="css-label" style="margin-left: 20px;margin-right: 20px;">Búsqueda por periodo.</label>          
            <button type="button" class="btn btn-primary btn-sm" id="load" style="text-align:center" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
            &ensp;
            <a type="button" class="btn btn-sm" style="background-color:#d67166" href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
              title ="Generar reporte en PDF" border="0"> 
            </a>
          </div>
          <br>
          <br>
          <div class="row">
            <div class="form-inline">
              <div class="col-md-12">
                <div class="col-md-3 mostrarfecha">
                   <label>Fecha Inicio:</label>
                   <input type="text" id="fechainicio" name="fechainicio" class="selectpicker btn-sm form-control">
                </div>
                <div class="col-md-3 mostrarfecha">
                   <label>Fecha Fin:</label>
                   <input type="text" id="fechafin" name="fechafin" class="selectpicker btn-sm form-control">
                </div>
                 <div class="col-md-3 mostrarperiodo" hidden>
                  <label>Tipo de periodo:</label>
                  <select id="nombre" name="nombre" class="form-control selectpicker btn-sm" data-live-search="true" data-width="55%">
                    <option value="*" selected="selected">Todos</option>
                    <?php 
                    while ($e = $tipoperiodo->fetch_object()){
                      $b = "";
                      if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
                      echo '<option value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
                    }
                    ?>
                  </select> 
                </div>     

              <div class="col-md-3 mostrarperiodo" hidden>
                  <label class="" for="nominas">Nomina:</label>
                  <select id="nominas" class="nominas form-control selectpicker btn-sm" data-live-search="true" name="nominas" data-width="70%">
                    <option value="*">Todos</option>
                    <?php 
                    while ($e = $nominas->fetch_object()){
                      $b = ""; 
                      if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
                      echo '<option value="'. $e->idtipop .'" '.$b .'>'.'('.$e->numnomina.')'.'  '.$e->fechainicio.' '.$e->fechafin.'</option>';
                    }
                    ?> 
                  </select> 
                  </div>
                <div class="col-md-3">
                   <label>Empleado:</label>
                   <select id="empleados"  class="selectpicker btn-sm form-control" data-live-search="true" name="empleados" class="form-control" style="width:150px;color: black" data-width="70%">
                  <option value="*">Todos</option>
                  <?php 
                  while ($e = $empleados->fetch_object()){
                    $b = "";
                    if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
                     echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.'  </option>';                              }
                    ?>
                  </select>
                </div>
                <div class="col-md-3">
                   <label>Incidencia:</label>
                    <select id="incidencias" class="form-control selectpicker btn-sm" data-live-search="true" name="incidencias" data-width="70%">
                    <option value="*">Todos</option>
                    <?php 
                    while ($e = $incidencias->fetch_object()){
                      $b = "";
                      if(isset($datos)){ if($e->idtipoincidencia == $datos->idtipoincidencia){ $b="selected"; } }
                      echo '<option value="'. $e->idtipoincidencia .'" '. $b .'>'. $e->nombre .'  </option>';
                    }
                    ?>
                  </select>  
                </div> 
              </div>
            </div>
          </div>
              <input name="txt1" type="hidden" value="<?php echo $fi; ?>" />
              <input name="txt2" type="hidden" value="<?php echo $ff; ?>" />
            </fieldset>
          <div id="imprimible" width='100%'>
            <div class="alert alert-info table-responsive" style="border: 1px solid rgb(217,237,247);">
            <div style="font-family: Courier;text-align: center;">
               <b style="font-size:14px;" hidden>Reporte de Incidencias</b>
            </div>
            <div class="container-fluid col-md-12 estinegrit" hidden>
              <?php
                  $url = explode('/modulos',$_SERVER['REQUEST_URI']);
                  if($logo1 == 'logo.png') $logo1= 'x.png';
                  $logo1 = str_replace(' ', '%20', $logo1); 
                ?>
                <img src=<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/".$logo1;?> style="width: 120px;height: 25px;padding-right:30px;" >
                <?php echo $infoEmpresa['nombreorganizacion'];?>
                <?php echo $infoEmpresa['RFC'];
                ?>
            </div>
            <div id="divRango" hidden class="estinegrit">
               Periodo del <label id="fechainiciop"></label> al <label id="fechafinalp"></label>
            </div>
            <table id="tablaincidencias" cellpadding="3" class="table table-striped table-bordered" width='100%'; border='1' bordercolor='#0000FF'>
            <thead> 
              <tr style="background-color:#B4BFC1;color:#000000">
                <th>No.(ID)</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Incidencia</th>
                <th>Fecha Fin</th>
                <th>Status</th>
                <th>Nomina</th> 
              </tr>
            </thead>
            <tbody></tbody>        
          </table>
        </div>
      </div>
    </div>
  </div>
 
  <!--GENERA PDF*************************************************-->
  <div id="divpanelpdf" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Generar PDF</h4>
        </div>
        <form id="formpdf" action="../cont/libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <label>Escala (%):</label>
                <select id="cmbescala" name="cmbescala" class="form-control">
                  <?php
                  for($i=100; $i > 0; $i--){
                    echo '<option value='. $i .'>' . $i . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label>Orientación:</label>
                <select id="cmborientacion" name="cmborientacion" class="form-control">
                  <option value='P'>Vertical</option>
                  <option value='L'>Horizontal</option>
                </select>
              </div>
            </div>
            <textarea id="contenido" name="contenido" style="display:none"></textarea>
            <input type='hidden' name='tipoDocu' value='hg'>
            <input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
            <input type='hidden' name='nombreDocu' value='Detalle Nomina'>
          </div>
          <div class="modal-footer">
            <div class="row">
              <div class="col-md-6">
                <input type="submit" value="Crear PDF" autofocus class="btn btn-primary btnMenu">
              </div>
              <div class="col-md-6">
                <input type="button" value="Cancelar" onclick="cancelar_pdf()" class="btn btn-danger btnMenu">
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
    <div id="divmsg" style="
    opacity:0.8;
    position:relative;
    background-color:#000;
    color:white;
    padding: 20px;
    -webkit-border-radius: 20px;
    border-radius: 10px;
    left:-50%;
    top:-200px
    ">
    <center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...
    </center>
  </div>
</div>
<script>
  function cerrarloading(){
    $("#loading").fadeOut(0);
    var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
    $("#divmsg").html(divloading);
  }
</script>
<script>
  function cerrarloading(){
    $("#loading").fadeOut(0);
    var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
    $("#divmsg").html(divloading);
  }
</script>
</body>
</html>