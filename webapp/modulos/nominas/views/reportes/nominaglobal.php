<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
    <link   rel="stylesheet" type="text/css" href="css/reporteincidencias.css"> 

  <link   rel="stylesheet" href="css/reportenomina.css" type="text/css">
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
  <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
  <script type="text/javascript" src='js/resumenglobal.js'></script>
  
  <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
  <script src="../../libraries/dataTable/js/datatables.min.js"></script>
  <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
  <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css"> 
  <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
  <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
  <script language='javascript' src='../cont/js/pdfmail.js'></script>
</head>
<body>
  <div class="container-fluid" class='encabezado' 
  style="text-align:center;font-family: Courier;font-size: 25px;">
    <b>Resumen de Nomina Global</b>
  </div>
  <br><br>
  <div class="container well" style="width: 96%;">
  	  <fieldset class="scheduler-border">
        <legend align="center" class="scheduler-border">Búsqueda</legend>
  	 		<div class="col-md-4 ">
		          <label >Tipo de periodo:</label>
		          <select id="tipoperiodo" name="tipoperiodo" class="form-control selectpicker btn-sm" data-live-search="true" data-width="55%" multiple="" title="Seleccione">
		            <option value="0" >Todos</option>
		            <?php 
		            while ($e = $tipoperiodo->fetch_object()){
		           
		              echo '<option value="'. $e->idtipop .'" >'. $e->nombre .'  </option>';
		            }
		            ?>
		          </select> 
		     </div>
	          <div class="col-md-4">
	          	<label >Ejercicio:</label>
	              <select id="ano" class="form-control selectpicker btn-sm" data-live-search="true" name="ano" data-width="70%" title="Seleccione">
	               <?php 
	               if($anosperiodo){
		            while ($e = $anosperiodo->fetch_object()){
		           
		              echo '<option value="'. $e->ano .'" >'. $e->ano .'  </option>';
		            }
		           }else{
		           	echo "<option>Sin nomina</option>";
		           }?>
	              </select> 
	         </div>
	   
	         <div class="col-md-4">
	          	<label >Mes:</label>
	              <select id="mes" class="form-control selectpicker btn-sm" data-live-search="true" name="mes" data-width="70%" multiple="" title="Seleccione">
		               <option value="01">Enero</option>
		               <option value="02">Febrero</option>
		               <option value="03">Marzo</option>
		               <option value="04">Abril</option>
		               <option value="05">Mayo</option>
		               <option value="06">Junio</option>
		               <option value="07">Julio</option>
		               <option value="08">Agosto</option>
		               <option value="09">Septiembre</option>
		               <option value="10">Octubre</option>
		               <option value="11">Noviembre</option>
	                	   <option value="12">Diciembre</option>
	              </select> 
	         </div>
            
	        
	        <div class="col-md-12" style="text-align: right;padding-top: 15px;"> 
              <button type="button" class="btn btn-primary btn-sm" id="load" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
            <a type="button" class="btn btn-sm" style="background-color:#d67166" href="javascript:pdfi();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title="Generar reporte en PDF" border="0"> 
               </a>
            </div>
   		</fieldset>
  	<br>
  	<div id="imprimible" class="table-responsive alert alert-info" style="color: black;"> 
  		<div class='mostrartabla' style="display: none;" >
	   	 <table   style='font-size:12px;' >
            <tr>
               <td rowspan='4' style='width:200px;padding-right:20px;'>
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
               <td><b>Resumen Global de Nomina</b></td></tr>
               <tr>
                  <td>Meses: <label id="mesesp"></label>
               </td>
            </tr>
            <tr>
              <td>Periodo: <label id="perip"></label>
              	Ejercicios: <label id="anop"></label>
            </td>
            
         <tr >
         	<td style="height: 10px;" rowspan='4'></td>
         </tr>
      </table>
      </div>
  	 <div class="alert alert-info table-responsive">
      <table id="tablates" cellpadding="0" class="table table-striped nowrap table-bordered"  width='100%';>
        <thead>
          <tr style="background-color:rgb(180,191,193);font-weight: bold;">
                  <th>SUELDO TOTAL</th>
                  <th>TOTAL PREMIO ASIST.</th>
                  <th>TOTAL PREMIO PUNT.</th>
                  <th>TOTAL BASE</th>
                  <th>TOTAL ISPT</th>  
                  <th>TOTAL SUBS</th> 
                  <th>TOTAL IMSS</th>
                  <th>TOTAL PRIMA VACA.</th>
                  <th>TOTAL VACA.</th>
                  <th>TOTAL NETO</th>
               </tr>
        </thead>
        <tbody id="contenidop">
        </tbody>
      
      </table><br>
      <table id="conceptossum" cellpadding="0" class="table table-striped nowrap table-bordered"  width='100%';>
            <thead> 
               <tr style='background-color:rgb(180,191,193);'>
                  <th colspan='3' style="font-weight: bold;">SUMA DE CONCEPTOS:</th>
               </tr>
               <tr style="font-weight: bold;color: black;">
                  <th>CONCEPTO</th>
                  <th>DESCRIPCIÓN</th>
                  <th>IMPORTE</th>
               </tr>
            </thead>
            <tbody id="sumacon">
               
              
         	</tbody>
      </table>
      
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
               <input type='hidden' name='nombreDocu' value='Nomina Global'>
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
</body>
</html>
