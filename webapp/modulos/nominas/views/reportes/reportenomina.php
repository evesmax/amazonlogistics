<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
  <link   rel="stylesheet" type="text/css" href="css/reporteincidencias.css"> 

  <link   rel="stylesheet" href="css/reportenomina.css" type="text/css">
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
  <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
  <script type="text/javascript" src='js/reportenomina.js'></script>
  
  <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
  <script src="../../libraries/dataTable/js/datatables.min.js"></script>
  <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
  <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css"> 
  <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
  <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
  <script language='javascript' src='../cont/js/pdfmail.js'></script>
</head>
<body>
  <input type="hidden" id="nominacompleta" value="<?php echo $nominascompleto;?>">
  
  <div class="container-fluid encabezado">
    <b>Reporte de nóminas timbradas y envío masivo</b>
  </div>
  <br><br>
  <div class="container well" style="width: 96%;">
    <form method="post" action="index.php?c=Reportes&f=reporteNominas" id="formfecha">

      <fieldset class="scheduler-border">
        <legend align="center" class="scheduler-border">Búsqueda</legend>
        <div class="row">
        	
         <div class="form-inline" align="center" >
           <input type="checkbox"  name="checkbox" id="mostrarfechas" class="css-checkbox"  value="1" <?php if($checkbox==1 and $nominascompleto == 1 ){ echo 'checked="checked"'; } ?>  <?php if($checkbox==1 and $nominascompleto == 0 ){ echo 'checked="checked"'; }?> />
        	  <!-- <input type="checkbox"  name="checkbox" id="mostrarfechas" class="css-checkbox"  value="1" <?php if($checkbox==1){ echo 'checked="checked"'; } ?> /> -->
            <label for="mostrarfechas" class="css-label"  onclick="activarChecked()" >Busqueda por rangos de fechas.
            </label>
            <?php if($nominascompleto == 1){ ?>
            <input type="checkbox"  id="mostrarperiodos" class="css-checkbox" name="checkbox" value="2"  <?php if($checkbox==2){ echo 'checked="checked"'; } ?> />
            <label for="mostrarperiodos" onclick="activarCheckeddos()" class="css-label" style="margin-left: 20px;margin-right: 20px;">Búsqueda por periodo.</label>          
         
          </div><br>
         <?php } ?>

          <div class="form-inline" style="padding-top: 20px;">
            <div class="col-md-4 mostrarfecha">
              <label>Fecha Inicio:</label>
              <input type="text" id="fechainicial" autocomplete="false" name="fechainicial" class="form-control btn-sm"  value="<?php echo $_REQUEST['fechainicial'];?>" 
              style="width: 70%;"> 
            </div>
            <div class="col-md-4 mostrarfecha">
              <label>Fecha Fin:</label>
              <input type="text" id="fechafinal"  autocomplete="false" name="fechafinal" class="form-control btn-sm"  
              value="<?php echo $_REQUEST['fechafinal'];?>" style="width: 70%;">  
            </div>
            
             <?php if($nominascompleto == 1){ ?>
            <div class="col-md-3 mostrarperiodo" hidden>
                  <label>Tipo de periodo:</label>
                  <select id="tipoperiodo" name="tipoperiodo" class="form-control selectpicker btn-sm" data-live-search="true" data-width="55%" title="Seleccione">
                    <?php 
                      while ($e = $tipoperiodo->fetch_object()){
                      $b = "";
                      if($e->idtipop == $periodoseleccionado){ $b='selected="selected"'; } 
                      echo '<option period="'.$e->idperiodicidad.'" value="'. $e->idtipop .'" '. $b .'>'.$e->nombre .'</option>';
                  }?> 
                  </select> 
                  <input type="hidden" id="periodoSelecc"  value="<?php echo $_POST['tipoperiodo']; ?>">
                  <input type="hidden" id="periodoselec"   name="periodoselec" value="<?php echo $_POST['periodoselec']; ?>">      
            </div>
            <?php } ?>
            
            <div class="col-md-3 extracheck" hidden>
                  <label class="extracheck">Origen</label>  
                  <select id="origen" class="extracheck form-control selectpicker btn-xs" data-live-search="true" 
                  name="origen" data-width="70%" title="Seleccione">
                      <option  value=""  <?php echo (($_POST['origen']=="")?"selected":"");?> >Todos</option>
                      <option  value="1" <?php echo (($_POST['origen']=="1")?"selected":"");?> >Aguinaldo</option>
                      <option  value="2" <?php echo (($_POST['origen']=="2")?"selected":"");?> >Finiquito</option>
                      <option  value="3" <?php echo (($_POST['origen']=="3")?"selected":"");?> >Ptu</option>
                    </select> 
                </div>

            <div class="col-md-3 mostrarperiodo nomina" hidden>
                  <label for="nominas">Nomina:</label>
                  <select id="nominas" class="nominas form-control selectpicker btn-sm" data-live-search="true" name="nominas" data-width="70%" title="Seleccione">
                     <input type="hidden" id="hdnIdNomp" value="<?php echo $nominaseleccionada;?>" />        
                  </select> 
            </div>
            
            <div class="col-md-4 empleado" hidden>
              <label>Empleado:</label>
              <select class="selectpicker" id="nombreEmpleado" data-live-search="true" name="empleados" title="Seleccione">
                <option value="0" <?php echo (($_POST['empleados']=="0")?"selected":"");?> >Todos</option>
                <?php 
                while ($e = $empleados->fetch_object()){
                  $b = "";
                  if($e->idEmpleado == $nombreEmpleado){ $b='selected="selected"'; } 
                  echo '<option value="'. $e->idEmpleado .'" '. $b .'>'.$e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';
                }?>
              </select> 
            </div>
          </div>
            <div class="col-md-12" style="text-align: center;padding-top: 15px;"> 
              <button type="button" class="btn btn-primary btn-sm" id="load" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
              <button type="button" class="btn btn-success btn-sm" onclick="envioCorreos();">Envio</button>
            </div>     
        </div>
      </fieldset>
    </form>
    <br>
    <br>
    <div class="alert alert-info table-responsive">
      <table id="tablanominas" cellpadding="0" class="table table-striped nowrap table-bordered"  width='100%';>
        <thead>
          <tr style="background-color:#B4BFC1;color:#000000">
            <th>No.(ID)</th>
            <th>UUID</th>
            <th>Empleado</th>
            <th>Fecha inicial pago</th>
            <th>Fecha final pago</th>
            <th>Días de pago</th>
            <th>Subtotal</th>
            <th>Descuento</th>
            <th>Total</th>
            <th>Accion</th> 
            <th hidden="true"></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if($reporteNomi){
            while($in = $reporteNomi->fetch_assoc()){
              $meses = array('1' => 'Enero','2' => 'Febrero','3' => 'Marzo','4' => 'Abril','5' => 'Mayo','6' => 'Junio','7' => 'Julio','8' => 'Agosto','9' => 'Septiembre','10' => 'Octubre','11' => 'Noviembre','12' => 'Diciembre');
              if($in['cancelado']==1){ $cancel = "style='text-decoration: line-through' "; }else{ $cancel="";}  
              ?>
              <tr <?php echo $cancel;?> class="out" onmouseout="this.className='out'" onmouseover="this.className='over'" xml="<?php echo $in['nombreXML'];?>" idemp="<?php echo $in['email'];?>" nomemp="<?php echo $in['nombreEmpleado'];?>" fechaini="<?php echo $in['fechainicial']; ?>" fechafin="<?php echo $in['fechafinal'];?>" >
                <td><?php echo $in['idNominatimbre'];?></td>
                <td><?php echo $in['UUID'];?></td>
                <td><?php echo $in['nombreEmpleado'].' '.$in['apellidoPaterno'].' '.$in['apellidoMaterno'];?></td>
                <td><?php echo $in['fechainicial'];?></td>
                <td><?php echo $in['fechafinal'];?></td>
                <td><?php echo number_format($in['diaspago'],2,'.',',');?></td>
                <td><?php echo number_format($in['subtotal'],2,'.',',');?></td>
                <td><?php echo number_format($in['descuento'],2,'.',',');?></td>
                <td><?php echo number_format($in['total'],2,'.',',');?></td>
                <td id="cancela<?php echo $in['idNominatimbre'];?>">
                <a href="../cont/xmls/facturas/temporales/<?php echo $in['nombreXML'];?>" target='_blank'>
                  <img src="images/lupa2.png" style="width: 19px;" title="Visor de XML.">
                </a>
                <a href="javascript:mailNominas('<?php echo $in['nombreXML'];?>','<?php echo $in['email'];?>','<?php echo $in['fechainicial'];?>','<?php echo $in['fechafinal'];?>');">
                  <img src="../../../webapp/netwarelog/repolog/img/email.png"  
                  title ="Enviar Facturas por correo electrónico" style="width: 19px;">
                </a>
                <a href="../cont/xmls/facturas/temporales/<?php echo $in['nombreXML'];?>" id="descargar" download>
                  <img src="images/xml.png" style="width: 19px;" id="descargarp" title="Descarga de XML.">
                </a>
                <a href="../cont/controllers/visorpdf.php?name=<?php echo $in['nombreXML'];?>&id=temporales" target="_blank"> 
                  <img src="images/pdf.png" style="width: 19px;" title="Descargar PDF.">
                </a>
                <a href="javascript:reutilizaFactura('<?php echo $in['idNominatimbre'];?>');">
                  <img src="images/reload.png" style="width: 19px;" title="Reutilizar Nomina.">
                </a>
                  <?php if($in['cancelado']==0 ){ ?>
                <a id="cancl" href="javascript:cancelarFactura('<?php echo $in['UUID'];?>',<?php echo $in['idNominatimbre'];?>);">
                  <img style="width: 26px;height: 26px;"  title="Cancelar Factura." src="images/cancelar.png">
                </a>
                  <?php 
                }else{ echo "<b style='color:red'>CANCELADA</b>";}?>
                </td>
              <td id="cargando<?php echo $in['idNominatimbre'];?>" style="display: none;width:30px !important;">
                  <b>Cancelando</b><i class='fa fa-refresh fa-spin '></i>
              </td>
            </tr>
        <?php }
      }else{ ?> 
      <?php } ?>
    </tbody>
  </table>
</div>
</div>
<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
<div 
id="divmsg"
style="
opacity:0.8;
position:relative;
background-color:#000;
color:white;
padding: 20px;
-webkit-border-radius: 20px;
border-radius: 10px;
left:-50%;
top:-30%
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
</body>
</html>
