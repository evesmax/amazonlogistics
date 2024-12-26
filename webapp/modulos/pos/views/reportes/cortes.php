<!DOCTYPE html>
<html>
<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Caja 3.0</title>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome-4.7.0/css/font-awesome-4.7.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/typeahead/typeahead.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric.js"></script>
    <script src="js/ticket.js"></script>
    <script src="js/caja2.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
    <script src="../../libraries/typeahead/typeahead.js"></script>
    <!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <!-- <script src="../../libraries/dataTable/js/datatables.min.js"></script> -->
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RCA -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>
</head>
<body>
	<div class="row">
        <div class="col-sm-12">
            <br>
            <div class="col-sm-1" style="text-align: right;">
                <label>Sucursal:</label>
            </div>
            <div class="col-sm-5" style="text-align: center;">                
                <select id="slsuc" class="form-control">
                        <option value="0">Selecciona una Sucursal</option>
                        <?php foreach ($sucursales as $k => $v) {
                            echo '<option value="'.$v["idSuc"].'">"'.$v["nombre"].'"</option>';
                        }?>                                                                                                             
                    </select>
            </div>  
            <div class="col-sm-1" style="text-align: right;">
                <label>Cortes:</label>
            </div>
            <div class="col-sm-5" style="text-align: center;">                                
                    <select id="slcorte" class="form-control">
                        <option value="0">Selecciona un Corte</option>
                        <?php foreach ($cortes as $k => $v) {
                            echo '<option value="'.$v["id"].'" inicio ="'.$v["inicio"].'" fin="'.$v["fin"].'">Del '.$v["inicio"].' al '.$v["fin"].'</option>';
                        }?>                                                                                                             
                    </select>
            </div>
            <br>                  	
            <div class="row" style="padding-top: 30px;">
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">

                            <div class="row">
                                <div class="col-sm-4">
                                    <h3 class="panel-title">Saldos</h3>
                               <!-- <label>Desde 2030-9494-90</label>
                                <label>Hasta 4849-900-009</label> -->
                                </div>
                               <div class="col-sm-4">
                                    <label>Desde: </label><label id="desdeCutText"></label>
                                    <input type="hidden" id="desdeCut" class="form-control" readonly>
                                </div>
                                <div class="col-sm-4">
                                    <label>Hasta: </label><label id="hastaCutText"></label>
                                    <input type="hidden" id="hastaCut" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <label>S.Inicial</label>
                                        </span>
                                        <input type="text" class="form-control" id="saldo_inicial" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <label>Ventas</label>
                                        </span>
                                        <input type="text" class="form-control" id="monto_ventas" readonly>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <label>Disponible</label>
                                        </span>
                                        <input type="text" class="form-control" id="saldo_disponible" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <label>Retiro</label>
                                        </span>
                                        <input type="text" class="form-control numeros" id="retiro_caja" style="background-color: #FFCCDD;">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <label>Deposito</label>
                                        </span>
                                        <input type="text" class="form-control numeros" id="deposito_caja" style="background-color: #A9F5A9;">
                                    </div>
                                </div> -->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">

        </div>
        <div class="col-sm-2" hidden>
            <select name="tipoCorte" id="tipoCorte" class="form-control" placeholder="Tipo de corte" required>
            <?php
            if( !isset( $_SESSION['corteParcial']['inicial'] ) ) { ?>
                <option value="1">Corte Normal</option>
            <?php } ?>
                <option value="2">Corte Parcial</option>
                <option value="3">Corte Z</option>
            </select>
        </div>
    </div>

    <div class="row">
    	<div class="col-sm-12">
    		<div class="col-sm-7">
    			<label>Cortes Parciales</label>
    			<table class="table table-bordered table-hover" id="tableCP">
    				<thead>
    					<tr>
        					<th>ID Corte</th>
        					<th>Fecha y hora</th>
        					<th>Usuario</th>
        					<th>Turno</th>
        					<th>Total de ventas</th>
        				</tr>                    					
    				</thead>
    				<tbody>                    					
    				</tbody>                    			
    			</table>
    		</div>
    		<div class="col-sm-1">                    			
    		</div>
    		<div class="col-sm-4">
    			<label>.</label>
    			<table class="table table-bordered table-hover" id="tableCP2">
    				<thead>
    					<tr>
        					<th>Saldo disponible</th>
        					<th>Saldo Reportado</th>
							<th>Diferencia</th>
        				</tr>                    					
    				</thead>
    				<tbody>                    					
    				</tbody>                    			
    			</table>                    			
    		</div>
    	</div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div style="height:350px;overflow:auto;">
                <div class="row">
                    <div class="col-sm-12">
                        <h4>Pagos</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover" id="gridPagosCut">
                            <thead>
                                <tr>
                                    <th>ID Venta</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>EF</th>
                                    <th>TC</th>
                                    <th>TD</th>
                                    <th>CR</th>
                                    <th>CH</th>
                                    <th>TRA</th>
                                    <th>SPEI</th>
                                    <th>TR</th>
                                    <th>NI</th>
                                    <th>TVales</th>
                                    <th>Cortesía</th>
                                    <th>Otros</th>
                                    <th>Cambio</th>
                                    <th>Impuestos</th>
                                    <th>Monto</th>
                                    <th>Des.</th>
                                    <th>Importe</th>
                                    <th>Ingreso(EF-Cambio)</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>



            </div>
        </div>
    </div>
                

</body>
</html>

<script>

function corteButtonAccion(desde,hasta,idcorte){

    caja.mensaje('Procesando...');
    $('#desdeCut').val(desde);
    $('#hastaCut').val(hasta);
    $('#desdeCutText').text('');
    $('#hastaCutText').text('');
    $('#saldo_inicial').val('');
    $('#monto_ventas').val('');
    $('#saldo_disponible').val('');
    $('#deposito_caja').val('');
    $('#retiro_caja').val('');
    var desde = $('#desdeCut').val();
    var hasta = $('#hastaCut').val();
    $.ajax({
        url: 'ajax.php?c=reportes&f=obtenCortes',
        type: 'post',
        dataType: 'json',
        data: {show: 0,desde:desde,hasta:hasta,idcorte:idcorte},
    })
    .done(function(resCor) {
        console.log(resCor);
        $('#desdeCut').val(resCor.desde);
        $('#hastaCut').val(resCor.hasta);
        $('#desdeCutText').text(resCor.desde);
        $('#hastaCutText').text(resCor.hasta);
        ///Llena la tabla de los pagos
        var cliente = '';
        var Efectivo = 0, TCredito = 0, TDebito = 0, CxC = 0, Cheque  = 0, Trans = 0, SPEI  = 0, TRegalo  = 0, Ni = 0, cambio = 0;
        var Impuestos = 0, Monto =0, Importe = 0, efectivoCambio2 = 0, dess = 0, TVales = 0, Cortesia = 0, Otros = 0;        
        $('.cutRows').empty();

        $.each(resCor.cortesP, function(index, val) {

        	$('#tableCP tr:last').after('<tr class="cutRows">'+
                        '<td>'+val.id+'</td>'+                        
                        '<td>'+val.fecha+'</td>'+
                        '<td>'+val.nombre+'</td>'+
                        '<td>'+val.turno+'</td>'+
                        '<td>$'+val.total_ventas+'</td>'+                      
                        '</tr>');

        	$('#tableCP2 tr:last').after('<tr class="cutRows">'+
                        '<td>$'+val.disponible+'</td>'+
                        '<td>$'+val.reportado+'</td>'+ 
                        '<td>$'+val.diferencia+'</td>'+                       
                        '</tr>');

        });


        $.each(resCor.ventas, function(index, val) {
            if(val.nombre==null){
                cliente = 'Publico General';
            }else{
                cliente = val.nombre;
            }
            efectivoCambio = (val.Efectivo - val.cambio);
                    $('#gridPagosCut tr:last').after('<tr class="cutRows '+(val.estatus =="1" ? ( val.condevolucion ? "bg-warning" : "" ) : "bg-danger")+'">'+
                        '<td>'+val.idVenta+'</td>'+
                        '<td>'+cliente+'</td>'+
                        '<td>'+val.fecha+'</td>'+
                        '<td>$'+val.Efectivo+'</td>'+
                        '<td>$'+val.TCredito+'</td>'+
                        '<td>$'+val.TDebito+'</td>'+
                        '<td>$'+val.CxC+'</td>'+
                        '<td>$'+val.Cheque+'</td>'+
                        '<td>$'+val.Trans+'</td>'+
                        '<td>$'+val.SPEI+'</td>'+
                        '<td>$'+val.TRegalo+'</td>'+
                        '<td>$'+val.Ni+'</td>'+
                        '<td>$'+val.TVales+'</td>'+
                        '<td>$'+val.Cortesia+'</td>'+
                        '<td>$'+val.Otros+'</td>'+
                        '<td>$'+val.cambio+'</td>'+
                        '<td>$'+val.Impuestos+'</td>'+
                        '<td>$'+val.Monto+'</td>'+
                        '<td>$'+parseFloat(val.descuentoGeneral).toFixed(2)+'</td>'+
                        '<td>$'+val.Importe+'</td>'+
                        '<td>$'+parseFloat(efectivoCambio).toFixed(2)+'</td>'+
                        '</tr>');
                    Efectivo +=  parseFloat(val.Efectivo);
                    TCredito += parseFloat(val.TCredito);
                    TDebito += parseFloat(val.TDebito);
                    CxC += parseFloat(val.CxC);
                    Cheque += parseFloat(val.Cheque);
                    Trans += parseFloat(val.Trans);
                    SPEI += parseFloat(val.SPEI);
                    TRegalo +=parseFloat(val.TRegalo);
                    Ni += parseFloat(val.Ni);
                    cambio += parseFloat(val.cambio);
                    Impuestos += parseFloat(val.Impuestos);
                    Monto += parseFloat(val.Monto);
                    Importe += parseFloat(val.Importe);
                    efectivoCambio2 += parseFloat(efectivoCambio);
                    dess += parseFloat(val.descuentoGeneral);
                    TVales += parseFloat(val.TVales);
                     Cortesia += parseFloat(val.Cortesia);
                     Otros += parseFloat(val.Otros);
        });
                // $('#gridPagosCut tr:last').after('<tr class="cutRows">'+
                //         '<td colspan="3">Totales</td>'+
                //         '<td>$'+Efectivo.toFixed(2)+'</td>'+
                //         '<td>$'+TCredito.toFixed(2)+'</td>'+
                //         '<td>$'+TDebito.toFixed(2)+'</td>'+
                //         '<td>$'+CxC.toFixed(2)+'</td>'+
                //         '<td>$'+Cheque.toFixed(2)+'</td>'+
                //         '<td>$'+Trans.toFixed(2)+'</td>'+
                //         '<td>$'+SPEI.toFixed(2)+'</td>'+
                //         '<td>$'+TRegalo.toFixed(2)+'</td>'+
                //         '<td>$'+Ni.toFixed(2)+'</td>'+
                //         '<td>$'+TVales.toFixed(2)+'</td>'+
                //         '<td>$'+Cortesia.toFixed(2)+'</td>'+
                //         '<td>$'+Otros.toFixed(2)+'</td>'+
                //         '<td>$'+cambio.toFixed(2)+'</td>'+
                //         '<td>$'+Impuestos.toFixed(2)+'</td>'+
                //         '<td>$'+Monto.toFixed(2)+'</td>'+
                //         '<td>$'+dess.toFixed(2)+'</td>'+
                //         '<td style="background-color: #FFCCDD;">$'+Importe.toFixed(2)+'</td>'+
                //         '<td style="background-color: #a9f5a9;">$'+efectivoCambio2.toFixed(2)+'</td>'+
                // 	'</tr>');






        $('#saldo_inicial').val(resCor.montoInical);
        //$('#monto_ventas').val(resCor.monto_ventas);
        $('#monto_ventas').val(resCor.ventas_total);        
        //$('#saldo_disponible').val(resCor.saldoDisponible);

        caja.eliminaMensaje();
        $('#modalCorteDeCaja').modal({
            show:true,
        });
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });


}

$("#slsuc").change(function(event) {
    var idSuc = $("#slsuc").val();
    $("#slcorte").html('');
    $.ajax({
        url: 'ajax.php?c=reportes&f=cortesTerminados',
        type: 'POST',
        dataType: 'json',
        data: {idSuc:idSuc},
    })
    .done(function(data) {
        console.log(data);
        $("#slcorte").append('<option value="0">Seleccione un Corte</option>');
        $.each(data, function(index, val) {
            $("#slcorte").append('<option value="'+val.id+'" inicio ="'+val.inicio+'" fin="'+val.fin+'">Del '+val.inicio+' al '+val.fin+'</option>');             
        });
    });
    
});

$("#slcorte").change(function(event) {
	var idcorte = $("#slcorte").val();
	var desde 	= $('#slcorte option:selected').attr('inicio');
	var hasta 	= $('#slcorte option:selected').attr('fin');

	corteButtonAccion(desde,hasta,idcorte);

});

$(function() {
	console.log('ready');

});
	

</script>