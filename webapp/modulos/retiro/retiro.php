<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retiro de Caja</title>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/typeahead.css" />
    <link rel="stylesheet" href="css/caja/caja.css" />
    <?php include('../../netwarelog/design/css.php');?>
    <LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->
 <!--   <link rel="stylesheet" href="http://cdn.datatables.net/1.10.8/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/caja/caja.js" ></script>
    <script type="text/javascript" src="js/retiro/retiro.js" ></script>
    <script type="text/javascript" src="js/typeahead.js" ></script>
    <script type="text/javascript" src="js/caja/punto_venta.js" ></script>
    <script type="text/javascript" src="../punto_venta/js/jquery.alphanumeric.js" ></script>
<!--  <script type="text/javascript" src="http://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
   <script type="text/javascript" src="js/jquery.dataTables.min.js" ></script>
    <script type="text/javascript" src="js/table.js" ></script>
    <script type="text/javascript" src="http://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script> -->
    <script>
    $(document).ready(function() {
        pintatabla();
        usuarios();
       $.datepicker.regional['es'] = {
             closeText: 'Cerrar',
             prevText: '<Ant',
             nextText: 'Sig>',
             currentText: 'Hoy',
             monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
             monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
             dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
             dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
             dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
             weekHeader: 'Sm',
             dateFormat: 'dd/mm/yy',
             firstDay: 1,
             isRTL: false,
             showMonthAfterYear: false,
             yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#desde").datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd' 
        });
        $("#hasta").datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd' 
        });

    }); 

    </script>
<body>
   <div class=" nmwatitles ">Retiro de Efectivo</div>
<br>
    <div class="col-xs-12">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Cantidad y Concepto</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="text">Cantidad:</label>
                        <input type="number" class="form-control nminputtext" id="cantidad">
                    </div>
                    <div class="form-group">
                        <label for="text">Concepto:</label>
                        <input type="text" class="form-control nminputtext" id="concepto">
                     </div>
                     <button type="button" class="btn btn-success" onclick="retira()">Guardar</button>
                </div>
            </div>
       </div>
    </div> 
    <br><br><br><br><br>
    <div class="col-xs-10" style="padding-top:20px;">
        <label>Desde:</label>
        <input type="text" id="desde" class="nminputtext">
        <label>Hasta:</label>
        <input type="text" id="hasta" class="nminputtext">
        <label>Usuario</label>
        <select id="usuario">
        </select>
        <input type="button" value="Buscar" onclick="filtra();" class="nminputbutton_color2">
    </div>
    <div class="col-xs-10" style="padding-top:25px;">
        <table class="table display" id="tablita"  cellspacing="0" width="100%">
            <thead>
              <tr>
                <th class="nmcatalogbusquedatit">ID</th>
                <th class="nmcatalogbusquedatit">Cantidad</th>
                <th class="nmcatalogbusquedatit">Concepto</th>
                <th class="nmcatalogbusquedatit">Usuario</th>
                <th class="nmcatalogbusquedatit">Fecha</th>
                <th class="nmcatalogbusquedatit">Imprimir</th>
              </tr>
            </thead>
         <!--   <tbody id="cuerpotablita"></tbody> -->

            
        </table>
    </div>
</body>
</html>