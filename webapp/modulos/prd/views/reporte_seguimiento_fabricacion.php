<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>Reporte Seguimiento Fabricacion</title>

  <script type="text/javascript" src="../../libraries/jquery.min.js"></script>
  <link   rel="stylesheet" href="css/stylesheet-pure-css.css">

  <!-- Select multiple -->
  <link   href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
  <script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>
  <!--  -->

  <!-- Daterangepicker -->
  <script type="text/javascript" src="../../libraries/daterangepicker/moment.js"></script>
  <script type="text/javascript" src="../../libraries/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="../../libraries/daterangepicker/daterangepicker.css" />

  <script type="text/javascript" src="../../libraries/bootstrap-3.3.7/js/bootstrap.js"></script>
  <script type="text/javascript" src='js/report_segui_fabricacion.js'></script>
  <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
  <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
  <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">

  <!-- Datatable -->
  <script src="../../libraries/dataTable/js/datatables.min.js"></script>
  <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

</head>
<body>
  <form>
    <div class="panel-group container well" style="width: 96%;"> <!--container well-->
      <div style="text-align:center;font-family: Courier;font-size: 25px;" 
      class="container-fluid"><b>Reporte Seguimiento Fabricación</b>
    </div>
    <hr style="border: 1px solid;">
    <div class="form-row" style="padding-bottom: 100px;padding-top: 30px;">
      <div class="col-md-3 mb-3">
        <label for="ordenprod1">Orden</label>
        <select id="ordenprod1" name="ordenprod1" class="btn-sm form-control" title="Seleccione" multiple>
          <?php 
          while ($e = $ordenproduccion->fetch_object()){
            echo '<option value="'. $e->id .'" '. $b .'>'. $e->id .'  </option>'; }?>
          </select> 
          <input type="hidden" name="ordenprod" id="ordenprod">
        </div>
        <div class="col-md-4 mb-4">
          <label for="producto1">Producto</label>
          <select id="producto1" class="btn-sm form-control" title="Seleccione" multiple>
            <?php 
            while ($e = $producto->fetch_object()){
              echo '<option value="'. $e->id .'" '. $b .'>'. $e->nombre .'  </option>'; }?>
            </select>
            <input type="hidden" name="producto" id="producto">
          </div>
          <div class="col-md-3 mb-3">
            <label for="fecha">Fechas</label>
            <input type="text" name="fecha" value="" class="form-control" placeholder="Rango de fechas"/>
          </div>   
          <div class="col-md-2 mb-2">
            <label for="load"><br></label>
            <button type="button" class="btn btn-primary btn-sm form-control" id="load" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>" style='background-color:#337AB7;'>
            Generar Reporte</button>
          </div> 
        </div>
        <div class="panel panel-default">
          <div class="panel-body border" id="llenatablaseguimiento">           
            <?php  if($reporteSeguimiento->num_rows==0) { ?>
            <div class='alert alert-info table-responsive'>
              <table cellpadding='0' class='tablaseguimiento table table-striped table-bordered nowrap table-responsive' width="100%">
                <thead> 
                  <tr style='background-color:#B4BFC1;color:#000000;'>
                    <td>Nombre Paso</td>
                    <td>Estatus</td>
                    <td>Pendiente</td>
                    <td>Terminado</td>  
                    <td>Fecha</td>
                  </tr>
                </thead>
              </table>
            </div>
            <?php 
          } ?>   
        </div> 
      </div>
    </div><!--container well-->
  </form>
</body>
</html>