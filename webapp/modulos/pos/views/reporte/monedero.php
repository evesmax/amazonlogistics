<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Reportes</title>
    <link rel="stylesheet" href="">
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- Sistema -->
    <script src="js/monedero.js"></script>

	<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.es.js" type="text/javascript"></script>


    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<!--    <script src="../../libraries/dataTable/js/datatables.min.js"></script> -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RCA -->
    <link rel="stylesheet" type="text/css" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>	

	<!-- Notify  -->
	<script src="../../libraries/notify.js"></script>

   	<script>		
	   $(document).ready(function() { 
	   		   		
		 	var desde = new Date();
			desde.setDate( desde.getDate() - 7 )
			var month = pad(desde.getUTCMonth() + 1 , 2); //months from 1-12
			var day = pad(desde.getUTCDate(), 2);
			var year = pad(desde.getUTCFullYear(), 2);
			desde = year + "-" + month + "-" + day;
			$('#desde').val(desde);

			var hasta = new Date();
			var month = pad(hasta.getUTCMonth() + 1, 2); //months from 1-12
			var day = pad(hasta.getUTCDate(), 2);
			var year = pad(hasta.getUTCFullYear(), 2);
			hasta = year + "-" + month + "-" + day;
			$('#hasta').val(hasta);

			$('#desde').datepicker({
		        format: "yyyy-mm-dd",
		        language: "es"
		    });
		    $('#hasta').datepicker({
		        format: "yyyy-mm-dd",
		        language: "es"
		    });

	 	});	  	
   	</script>
   
	<body>
	<div style="width: 95%; padding-left: 5%">
		<div class="well" >
		    <div class="row">
		        <div class="col-xs-12 col-md-12">
		           <h3>Reporte de Monedero Electrónico</h3>
		        </div>
		    </div>

		    <div class="row">
		        <div class="panel panel-default">
		            <div class="panel-heading">
		                <div class="row">
		                    <div class="col-sm-3">
		                      <label>Sucursal</label>
		                        <select id="idSucursal" class="form-control">
			                        <option value="0">-Todas-</option>
			                        <?php
			                            foreach ($filtros['sucursales'] as $key => $value) {
			                                echo '<option value="'.$value['idSuc'].'">'.$value['nombre'].'</option>';
			                            }
			                        ?>
		                        </select>
		                    </div>
		                    <div class="col-sm-3">
		                        <label>Desde</label>
		                        <div id="datetimepicker1" class="input-group date">
		                            <span class="input-group-addon">
		                                <span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                            <input id="desde" class="form-control" type="text" placeholder="Fecha de Entrega">
		                        </div>
		                    </div>
		                    <div class="col-sm-3">
		                        <label>Hasta</label>
		                        <div id="datetimepicker2" class="input-group date">
		                            <span class="input-group-addon">
		                                <span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                            <input id="hasta" class="form-control" type="text" placeholder="Fecha de Entrega">
		                        </div>                        
		                    </div>
		                </div>
		                <div class="row">
		                    <div class="col-sm-3">
		                        <label>Cliente</label>
		                        <select  class="form-control" id="cliente">
		                            <option value="0">-Seleccion un Cliente-</option>
		                            <?php                           
		                                foreach ($ventasIndex['clientes'] as $key1 => $value1) {
		                                    echo '<option value="'.$value1['id'].'">'.$value1['nombre'].'</option>';
		                                }
		                            ?>
		                        </select>
		                    </div>
		                    <div class="col-sm-3">
		                        <label>Puntos</label>
		                        <select id="puntos" class="form-control">
		                            <option value="0">-Todos-</option>                         
		                            <option value="1">Generados</option>                         
		                            <option value="2">Utilizados</option>                                                                            
		                        </select>
		                    </div>
		                    <div class="col-sm-3">
		                        <label>Forma de pago</label>
		                        <select id="cboMetodoPago" class="form-control" >
		                            <option value="0">-Seleccion una forma de pago-</option>
		                            <?php
		                                foreach ($formasDePago['formas'] as $key => $value) {
		                                    echo '<option value="'.$value['idFormapago'].'">('.$value['claveSat'].') '.$value['nombre'].'</option>';
		                                }
		                            ?>
		                        </select>
		                    </div>
		                    <div class="col-md-3"><br>
		                    	<button class="btn btn-default center-block" onclick="reloadtable();">Buscar</button>
		                    </div>
		                </div>	              
		            	<div class="panel-body">
		                	<div class="row">
		                    	<div class="col-sm-12" style="overflow:auto;">
		                            <div style="width:100% " id="tableDivCont"></div>
		                        </div>
		                    </div>
		            	</div>
		        	</div>
		    	</div>
		    </div>           

		  	<div class="modal fade" id="modalMensajes" role="dialog" style="z-index:1051;" data-backdrop="static">
		    	<div class="modal-dialog modal-sm">
		      		<div class="modal-content">
		        		<div class="modal-header">
		          			<h4 class="modal-title">Espere un momento...</h4>
		        		</div>
		        	<div class="modal-body">
		          		<div class="alert alert-default">
			            	<div align="center"><label id="lblMensajeEstado"></label></div>
			            	<div align="center"><i class="fa fa-refresh fa-spin fa-5x fa-fw margin-bottom"></i>
			                 	<span class="sr-only">Loading...</span>
			             	</div>
		        		</div>
		        	</div>
		      	</div>
		    </div>
	    </div>	  
	</div>
	</body>
</html>
<script>
	function pad (n, length) {
	    var  n = n.toString();
	    while(n.length < length)
	         n = "0" + n;
	    return n;
	}
</script>