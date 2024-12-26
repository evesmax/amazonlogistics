<head>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/datatablesboot.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.min.css">
    <style>
    th.sorting_asc, th.sorting_desc {
        background-image: none !important;
    }
    </style>
</head>

<body>
<?php 
  	$url = explode('/modulos',$_SERVER['REQUEST_URI']);
  	if($logo == 'logo.png') $logo = 'x.png';
  	$logo = str_replace(' ', '%20', $logo); 
	
	?>
    <br>
    <div class="container well" style="padding:25px;margin-bottom: 150px;">
        <div class="row" style="padding-bottom:20px;">
            <div class="col-xs-12 col-md-12" style="padding:0px 0px 0px 3px;">
                <h3>Reporte de Insumos Ordenes de producción (abasto)</h3>
            </div>
        </div>

        <div id="lista_abasto" class="row" style="display:block;margin-top:20px;font-size:12px;">
            <table id="tabla_abasto" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th width="7%">No.Orden</th>
                        <th width="25%">Producto</th>
                        <th width="7%">Cantidad</th>
                        <th width="10%">Fecha</th>
                        <th width="7%">Estatus</th>
                        <th width="7%">Insumos</th>
                        <th width="7%">Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($datos = $datos_reporte->fetch_object()){?>
                    	<tr>
                    		<td><?php echo $datos->id;?></td>
                    		<td><?php echo strtoupper($datos->nombre);?></td>
                    		<td align="center"><?php echo $datos->cantidad;?></td>
                    		<td><?php echo $datos->fecha_registro;?></td>
                    		<td align="left"><?php 
                    			if($datos->estatus==0){?>
                    			 	<span class="label label-danger" style="cursor:pointer;">Orden eliminada</span>
							<?php
							} if($datos->estatus==1){?>
							 	<span class="label label-default" style="cursor:pointer;">Registro inicial</span>
							<?php
							} if($datos->estatus==2){?>
           						<span class="label label-warning" style="cursor:pointer;">En espera de insumos</span>
          					<?php
							}if($datos->estatus==10){?>
            						<span class="label label-success" style="cursor:pointer;">Produccion finalizada</span>
         					<?php
							}if($datos->estatus==3){?>
          					 	<span class="label label-success" style="cursor:pointer;">Lista para producir</span>
          					<?php
							}if($datos->estatus==4){?>
           						<span class="label label-success" style="cursor:pointer;">Lista para producir</span>
         					<?php
							}if($datos->estatus==9){?>
            						<span class="label label-info" style="cursor:pointer;">Produccion iniciada</span>
          					<?php
							}
						?></td>
                    		<td align="center"><button type="button" onclick="verInsumos(<?php echo $datos->id;?>,'<?php echo $datos->nombre;?>',<?php echo $datos->cantidad;?>)" class="btn btn-warning btn-xs "><label class="kr">Ver</label></button></td>
                    		<td>
                    			 <a type="button" class="btn btn-sm" style="background-color:#d67166"  href="javascript:pdf(<?php echo $datos->id;?>,'<?php echo $datos->nombre;?>',<?php echo $datos->cantidad;?>);"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
				                  title ="Generar reporte en PDF" border="0"> 
				               </a>  
                    		</td>
                   <!-- <td><a target="_blank" href="javascript:pdf(<?php echo $datos->id;?>,'<?php echo $datos->nombre;?>',<?php echo $datos->cantidad;?>);" href="ajax.php?c=rep_produccion&f=vertInsumos&opc=1&cant=<?php echo $datos->cantidad;?>&idop=<?php echo $datos->id;?>&prod=<?php echo $datos->nombre;?> ">Imprimir</a></td> -->
                    	</tr>
                    	
                    	
                  <?php  
                  	}
                    ?>
                </tbody>
            </table>
        </div>
    </div>

<div id="vistainsumos" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header panel-heading">
                <h4 id="modal-label" id="nombreprd"></h4>
            </div>
            <div class="modal-body" id="contenidoinsu">
               
            </div>
             <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal">
			        <span class="glyphicon glyphicon-remove"></span>
			        <span class="hidden-xs">Cerrar</span>
			    </button>
			
			</div>
        </div>
    </div> 
</div> 
 <div id="imprimible"></div>
 <div id="divpanelpdf" class="modal fade" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title">Generar PDF</h3>
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
               <input type='hidden' name='nombreDocu' value='Detalle Orden Produccion'>
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
<!-- CHRIS - COMENTARIOS
============================= 
//Librerias genericas 
-->
<link rel="stylesheet" href="../../libraries/jquery-ui-1.11.4/jquery-ui.css">
<script src="../../libraries/jquery-1.9.1.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> 

<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../libraries/select2/dist/js/select2.min.js" type="text/javascript"></script>

<!-- CHRIS - COMENTARIOS
============================= 
//Librerias raiz appministra 
-->
<script src="js/numeric.js" type="text/javascript"></script>
<script src="js/moneda.js" type="text/javascript"></script>
<script src="js/datatables.min.js" type="text/javascript"></script>
<script src="js/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="js/bootstrap-datepicker.es.js" type="text/javascript"></script>

</body>

<script>

$(function(){

    var table = $('#tabla_abasto').DataTable();
    table.destroy();
    $('#tabla_abasto').DataTable({
        "ordering": false,
    language: {
        search: "Buscar:",
        lengthMenu:"Mostrar _MENU_ elementos",
        zeroRecords: "No hay datos.",
        infoEmpty: "No hay datos que mostrar.",
        info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
        paginate: {
            first:      "Primero",
            previous:   "Anterior",
            next:       "Siguiente",
            last:       "Último"
        }
     },
    });

    $('.sorting_asc').removeClass();

}); 
function verInsumos(idop,nombreprod,cantidad){
	$("#vistainsumos").modal('show');
	nombreprod = nombreprod.toUpperCase();

	$("h4").html("No.Orden:"+idop+"<br>Producto: "+nombreprod);
	$('#contenidoinsu').empty();
	 // //escape() te convierte los espacios en %20 y con eso lo interpretara apropiadamente el navegador
	$('#contenidoinsu').load('ajax.php?c=rep_produccion&f=vertInsumos&cant='+cantidad+'&idop='+idop+'&prod='+escape(nombreprod));
       

 }
 function pdf(idop,nombreprod,cantidad){
 	
 	$.post('index.php?c=rep_produccion&f=vertInsumos&opc=1&cant='+cantidad+'&idop='+idop+'&prod='+escape(nombreprod),{
 		
 	},function(r){
 		$("#contenidoinsu").html(r);
 		var contenido_html = $("#imprimible").html();
		$("#contenido").text(contenido_html);
 		
 		$("#divpanelpdf").modal('show');
 	});
 
}
function generar_pdf(){
  $("#divpanelpdf").modal('hide');
}
function cancelar_pdf(){
  $("#divpanelpdf").modal('hide');
}

</script>
