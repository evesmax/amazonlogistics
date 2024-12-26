
<html lang="es">
<head>
    <meta http-equiv="Expires" content="0">
    <title>ANÁLISIS DE FACTURAS DE PROVEEDOR</title>
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
                  ajax :  'ajax.php?c=portalproveedores&f=printReport',
                        //data : r.data,
                      columns: [
                        {data:"idocompra"}, 
                        {data:"codigo"},
                        {data:"razon_social"},
                        {data:"folio"},
                        {data:"uuid"},
                        {data:"fechaFac"},
                        {data:"ocTotal"},
                        {data:"importe"},
                        {data:"estatus"},
                        {data:"complemento"},
                        {data:"pdf"},
                        {data:"acci"},

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

                $("#prove").select2({
                    
                });
        $('#desde').datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });
        $('#hasta').datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });
            
            
        });
        function FunPdf(name){
          alert(name)
          window.open("../../modulos/cont/xmls/facturas/temporales/"+name);

        }
      function aaa(a){
           // var aux = this.getAttribute('id');
          console.log(a);
              var checados = $( "input:checked" ).length;

              var oTable = $('#tabliGriP').dataTable();
              var allPages = oTable.fnGetNodes();
              var contador = 0;
              cadena='';
              var arr = [];
            //$('#clientepago').val(data);
            $('input:checked', allPages).each(function(){
                   contador++;
                });
                //console.log(arr)
                
             /* if(contador>=2){
                $('#btnaf').css('display','block');
              }else{
                $('#btnaf').css('display','none');
              } */

        }
        function modalAllfs(){

          var oTable = $('#tabliGriP').dataTable();
          var allPages = oTable.fnGetNodes();

          cadena='';
          $('input:checked', allPages).each(function(){
                  cadena+=$(this,allPages).val()+',';
              });
         
          if(cadena!=''){
            var r = confirm("Deseas Contabilizar las partidas seleccionadas?");
            if (r == true) {
                $('#modalMensajes').modal();
                $.ajax({
                  url: 'ajax.php?c=portalproveedores&f=contabilizar',
                  type: 'POST',
                  dataType: 'json',
                  data: {cadena: cadena},
                })
                .done(function(resp) {
                  console.log(resp);
                  if(resp.estatus==true){
                    $('#modalMensajes').modal('hide');
                    alert('Se contabilizaron con exito.');
                    window.location.reload();
                  }
                })
                .fail(function() {
                  console.log("error");
                })
                .always(function() {
                  console.log("complete");
                });
                
            } else {
                return false;
            } 
          }else{
            alert('Selecciona al menos una partida.');
            return false;
          }
        }
        
        function buscaP(){
        
           var desde = $('#desde').val();
           var hasta = $('#hasta').val();
           var prove = $('#prove').val(); 
           var estatus = $('#estatusSel').val();
           var conta = $('#contabilizador').val();
           $('#modalMensajes').modal()
             $.ajax({
               url: 'ajax.php?c=portalproveedores&f=printReport',
               type: 'POST',
               dataType: 'json',
               data: {desde : desde,
                      hasta : hasta,
                      prove : prove,
                      estatus : estatus,
                      conta : conta
                    },
             })
             .done(function(data) {
               console.log(data);

            var table = $('#tabliGriP').DataTable({
                              retrieve: true,
                        dom: 'Bfrtip',
                      buttons: ['excel'],
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

                            table.clear().draw();
             
                $.each(data.data, function(index, val) {

                     x ='<tr>'+
                          '<td width="5%">'+val.idocompra+'</td>'+
                          '<td>'+val.codigo+'</td>'+
                          '<td>'+val.razon_social+'</td>'+
                          '<td>'+val.folio+'</td>'+
                          '<td>'+val.uuid+'</td>'+
                          '<td>'+val.fechaFac+'</td>'+
                          '<td>'+val.ocTotal+'</td>'+
                          '<td>'+val.importe+'</td>'+
                          '<td>'+val.estatus+'</td>'+
                          '<td>'+val.pdf+'</td>'+
                          '<td>'+val.acci+'</td>'+
                        +'</tr>';
                        table.row.add($(x)).draw();
          });
                $('#modalMensajes').modal('hide')
             })
             .fail(function() {
               console.log("error");
             })
             .always(function() {
               console.log("complete");
             });
    

        }

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

<div class="container well col-sm-12">


    <div class="row"> 
      <div class="col-md-12"><h3>Análisis de Facturas de Proveedor</h3></div> 
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">

          <div class="col-md-11">
            <div class="row">
              <div class="col-md-3">
                <div class="row">
                  <div class="col-sm-6">
                    <label>Desde:</label>
                    <div id="datetimepicker1" class="input-group date">
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                      <input id="desde" class="form-control" type="text">
                    </div>
                  </div>
                  <div class="col-sm-6">
                      <label>Hasta:</label>
                      <div id="datetimepicker1" class="input-group date">
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        <input id="hasta" class="form-control" type="text">
                      </div>
                  </div>

                </div>

              </div>
            

              <div class="col-md-3">
                <div class="form-group">
                  <label for="proves">Proveedores:</label>
                  <select id="prove" class="form-control" style="width: 100%;">
                    <option value="0">--Selecciona un Proveedor--</option>
                    <?php 
                        foreach ($proveedores['proveedores'] as $key => $value) {
                          echo '<option value="'.$value['idPrv'].'">('.$value['codigo'].')'.$value['razon_social'].'</option>';
                        }
                      
                    ?>
                  </select>
                </div>
                
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="estatus">Estatus:</label>
                  <select id="estatusSel" class="form-control" style="width: 100%;">
                    <option value="0">--Selecciona estatus--</option>
                    <option value="1">En firme</option>
                    <option value="2">Recibida</option>
                    <option value="3">Programada para pago</option>
                    <option value="4">Pagada</option>
                    <option value="5">Cancelada</option>
                  </select>
                </div>
                
              </div>
              <div class="col-md-3">
                  <div class="row">
                     <div class="col-sm-6">
                      <div class="form-group">
                        <label for="contabili">Contabilizado:</label>
                        <select id="contabilizador" class="form-control" style="width: 100%;">
                          <option value="0">--Selecciona opcion--</option>
                          <option value="1">Contabilizado</option>
                          <option value="2">Sin contabilizar</option>
                        </select>
                      </div>
                     </div>
                     <div class="col-sm-6"></div>
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
                                   <th class="nmcatalogbusquedatit">OC</th>
                                   <th class="nmcatalogbusquedatit">Partida</th>
                                   <th class="nmcatalogbusquedatit">Proveedor</th>
                                   <th class="nmcatalogbusquedatit">Serie</th>
                                   <th class="nmcatalogbusquedatit">UUID</th>

                                   <th class="nmcatalogbusquedatit">Fecha Factura</th>
                                   <th class="nmcatalogbusquedatit">Monto OC</th>
                                   <th class="nmcatalogbusquedatit">Importe Fac</th>
                                   <th class="nmcatalogbusquedatit">Estatus</th>
                                   <th class="nmcatalogbusquedatit">Complemento</th>
                                   <th class="nmcatalogbusquedatit">PDF</th>
                                   <th class="nmcatalogbusquedatit"><button class="btn btn-primary" onclick="modalAllfs();">Contabilizar</button></th>

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