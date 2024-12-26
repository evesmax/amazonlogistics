
<html lang="es">
<head>
    <meta http-equiv="Expires" content="0">
    <title>Pedidos</title>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/typeahead/typeahead.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric.js"></script>
    <script src="js/pedido.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
    <script src="../../libraries/typeahead/typeahead.js"></script>
    <!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <!-- <script src="../../libraries/dataTable/js/datatables.min.js"></script> -->
<link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
  <!-- Modificaciones RC -->
  <script src="../../libraries/dataTable/js/datatables.min.js"></script>
  <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
  <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
  <script src="../../libraries/export_print/buttons.html5.min.js"></script>
  <script src="../../libraries/export_print/jszip.min.js"></script>
  <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">


    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.es.js" type="text/javascript"></script>


    <script>
        $(document).ready(function() {

                $('#tabliGriP').DataTable({
                  dom: 'Bfrtip',
                  buttons: ['excel'],
                  ajax :  'ajax.php?c=pedido&f=printGridP2',
                        //data : r.data,
                      columns: [
                        {data:"id"}, 
                        {data:"idCotizacion"},
                        {data:"fecha"},
                        {data:"nombre"},
                        {data:"usuario"},
                        {data:"total"},
                        {data:"pdf"},
                        {data:"status"},
                      ],
                  language: {
                    search: "Buscar:",
                    lengthMenu:"",
                    zeroRecords: "No hay datos.",
                    infoEmpty: "No hay datos que mostrar.",
                    info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                    paginate: {
                      first:      "Primero",
                      previous:   "Anterior",
                      next:       "Siguiente",
                      last:       "Último"
                    },
                  },
                  aaSorting : [[0,'desc' ]]
                });
          //gridPedidos();
          filtrosPedidos();

                $("#cotiClienteP").select2({
                    
                });
                $("#cotiEmpleadoP").select2({
                     
                });  
          /*$.datepicker.regional['es'] = {
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
        };  */
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
<style type="text/css">
a:link {text-decoration:none;color:#000000;}
a:visited {text-decoration:none;color:#000000;}
a:active {text-decoration:none;color:#000000;}
a:hover {text-decoration:underline;color:#000000;}
</style>

<link rel="stylesheet" href="css/imprimir_bootstrap.css" type="text/css">
<style>

  .tit_tabla_buscar td
  {
    font-size:medium;
  }

  #logo_empresa /*Logo en pdf*/
  {
    display:none;
  }

  @media print
  {
    #imprimir,#filtros,#excel, #botones
    {
      display:none;
    }
    #logo_empresa
    {
      display:block;
    }
    .table-responsive{
      overflow-x: unset;
    }
    #imp_cont{
      width: 100% !important;
    }
  }
 

</style>

</head>

<body>

<div class="container well">


    <div class="row"> 
      <div class="col-md-12"><h3>Pedidos</h3></div> 
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">

          <div class="col-md-11">
            <div class="row">
              <div class="col-md-3">
                <label>Desde:</label>
                <div id="datetimepicker1" class="input-group date">
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                  <input id="desde" class="form-control" type="text">
                </div>
              </div>
              <div class="col-md-3">
                <label>Hasta:</label>
                <div id="datetimepicker1" class="input-group date">
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                  <input id="hasta" class="form-control" type="text">
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="cotiClienteP">Cliente:</label>
                  <select id="cotiClienteP" class="form-control" style="width: 100%;">
                    <option value="0">--Selecciona un Cliente--</option>
                  </select>
                </div>
                
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="cotiEmpleadoP">Empleado:</label>
                  <select id="cotiEmpleadoP" class="form-control" style="width: 100%;">
                    <option value="0">--Selecciona un Empleado--</option>
                  </select>
                </div>
                
              </div>
              
            </div>
          </div>
          <div class="col-md-1">
            <div class="row">
              <div class="col-md-12">
                <label for=""></label><br>
                <input type="button" value="Buscar" onclick="buscaP();" class="btn btn-default btnMenu">
              </div>
            </div>
          </div>
          
            <div class="row">
              
              <div class="col-md-3">
                <input type="button" onclick="createnewP();" value="Levantar Pedido" class="btn btn-primary btnMenu">
              </div> 
            </div>
          
        </div>
      </div>



      <div class="panel-body">
        <div id="reporte" class="row">
          <div class="col-md-12" id="gridPedi">
                  <div class="col-md-12 col-sm-12 col-md-12 tablaResponsiva">
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover" id='tabliGriP'>
                          <thead>
                               <tr>
                                   <th class="nmcatalogbusquedatit">ID</th>
                                   <th class="nmcatalogbusquedatit">Cotizacion</th>
                                   <th class="nmcatalogbusquedatit">Fecha</th>
                                   <th class="nmcatalogbusquedatit">Cliente</th>
                                   <th class="nmcatalogbusquedatit">Empleado</th>

                                   <th class="nmcatalogbusquedatit">Total</th>
                                   <th class="nmcatalogbusquedatit">PDF</th>
                                   <th class="nmcatalogbusquedatit">Estados</th>
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
    </div>
    <br>
    
  </div>



<!-- <div class="container" style="width:100%" id="contenido">
  <div class="row">
    <div class="col-md-12">
      <h3 class="nmwatitles text-center">
        Pedidos de Clientes
      </h3>
      <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10" id="imp_cont">
          
          <section>
            <div class="row" id="gridPedi">
              <div class="col-md-12 col-sm-12 col-md-12 tablaResponsiva">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover" id='tabliGriP'>
                      <thead>
                           <tr>
                               <th class="nmcatalogbusquedatit">ID</th>
                               <th class="nmcatalogbusquedatit">Cliente</th>
                               <th class="nmcatalogbusquedatit">Cotizacion</th>
                               <th class="nmcatalogbusquedatit">Total</th>
                               <th class="nmcatalogbusquedatit">Empleado</th>
                               <th class="nmcatalogbusquedatit">Fecha</th>
                               <th class="nmcatalogbusquedatit">PDF</th>
                               <th class="nmcatalogbusquedatit">Estados</th>
                           </tr>
                       </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div> -->

  <div class="modal fade" id="modalMensajes" role="dialog" style="z-index:1051;" data-backdrop="static">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Espere un momento...</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-default">
            <div align="center"><label id="lblMensajeEstado">Procesando...</label></div>
            <div align="center"><i class="fa fa-refresh fa-spin fa-5x fa-fw margin-bottom"></i>
                 <span class="sr-only">Loading...</span>
             </div>
        </div>
        </div>
      </div>
    </div>
  </div>



</body> 
</html>