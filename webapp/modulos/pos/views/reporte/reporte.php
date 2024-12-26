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
    <script src="js/reporte.js"></script>

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
<!--    <script src="../../libraries/export_print/jquery-1.12.3.js"></script> -->

    <!-- morris -->
    <link rel="stylesheet" href="../../libraries/morris.js-0.5.1/morris.css">
    <script src="../../libraries/morris.js-0.5.1/raphael.min.js"></script>
    <script src="../../libraries/morris.js-0.5.1/morris.min.js"></script>
<!-- Notify  -->
	<script src="../../libraries/notify.js"></script>

    <style>
/*!
 FixedHeader 3.1.3
 ©2009-2017 SpryMedia Ltd - datatables.net/license
*/
table.fixedHeader-floating{position:fixed !important;background-color:white}table.fixedHeader-floating.no-footer{border-bottom-width:0}table.fixedHeader-locked{position:absolute !important;background-color:white}@media print{table.fixedHeader-floating{display:none}}

        .header1>th, .header1>td {
            font-weight: bold !important;
        }
        .header2>th, .header2>td {
            font-style: italic !important;
            font-weight: normal !important;
        }

        td, th {
            font-size: 70%  !important;
        }
    </style>
   <script>
/*!
 FixedHeader 3.1.3
 ©2009-2017 SpryMedia Ltd - datatables.net/license
*/
(function(d){"function"===typeof define&&define.amd?define(["jquery","datatables.net"],function(g){return d(g,window,document)}):"object"===typeof exports?module.exports=function(g,h){g||(g=window);if(!h||!h.fn.dataTable)h=require("datatables.net")(g,h).$;return d(h,g,g.document)}:d(jQuery,window,document)})(function(d,g,h,k){var j=d.fn.dataTable,l=0,i=function(b,a){if(!(this instanceof i))throw"FixedHeader must be initialised with the 'new' keyword.";!0===a&&(a={});b=new j.Api(b);this.c=d.extend(!0,
{},i.defaults,a);this.s={dt:b,position:{theadTop:0,tbodyTop:0,tfootTop:0,tfootBottom:0,width:0,left:0,tfootHeight:0,theadHeight:0,windowHeight:d(g).height(),visible:!0},headerMode:null,footerMode:null,autoWidth:b.settings()[0].oFeatures.bAutoWidth,namespace:".dtfc"+l++,scrollLeft:{header:-1,footer:-1},enable:!0};this.dom={floatingHeader:null,thead:d(b.table().header()),tbody:d(b.table().body()),tfoot:d(b.table().footer()),header:{host:null,floating:null,placeholder:null},footer:{host:null,floating:null,
placeholder:null}};this.dom.header.host=this.dom.thead.parent();this.dom.footer.host=this.dom.tfoot.parent();var e=b.settings()[0];if(e._fixedHeader)throw"FixedHeader already initialised on table "+e.nTable.id;e._fixedHeader=this;this._constructor()};d.extend(i.prototype,{enable:function(b){this.s.enable=b;this.c.header&&this._modeChange("in-place","header",!0);this.c.footer&&this.dom.tfoot.length&&this._modeChange("in-place","footer",!0);this.update()},headerOffset:function(b){b!==k&&(this.c.headerOffset=
b,this.update());return this.c.headerOffset},footerOffset:function(b){b!==k&&(this.c.footerOffset=b,this.update());return this.c.footerOffset},update:function(){this._positions();this._scroll(!0)},_constructor:function(){var b=this,a=this.s.dt;d(g).on("scroll"+this.s.namespace,function(){b._scroll()}).on("resize"+this.s.namespace,function(){b.s.position.windowHeight=d(g).height();b.update()});var e=d(".fh-fixedHeader");!this.c.headerOffset&&e.length&&(this.c.headerOffset=e.outerHeight());e=d(".fh-fixedFooter");
!this.c.footerOffset&&e.length&&(this.c.footerOffset=e.outerHeight());a.on("column-reorder.dt.dtfc column-visibility.dt.dtfc draw.dt.dtfc column-sizing.dt.dtfc",function(){b.update()});a.on("destroy.dtfc",function(){a.off(".dtfc");d(g).off(b.s.namespace)});this._positions();this._scroll()},_clone:function(b,a){var e=this.s.dt,c=this.dom[b],f="header"===b?this.dom.thead:this.dom.tfoot;!a&&c.floating?c.floating.removeClass("fixedHeader-floating fixedHeader-locked"):(c.floating&&(c.placeholder.remove(),
this._unsize(b),c.floating.children().detach(),c.floating.remove()),c.floating=d(e.table().node().cloneNode(!1)).css("table-layout","fixed").removeAttr("id").append(f).appendTo("body"),c.placeholder=f.clone(!1),c.placeholder.find("*[id]").removeAttr("id"),c.host.prepend(c.placeholder),this._matchWidths(c.placeholder,c.floating))},_matchWidths:function(b,a){var e=function(a){return d(a,b).map(function(){return d(this).width()}).toArray()},c=function(b,c){d(b,a).each(function(a){d(this).css({width:c[a],
minWidth:c[a]})})},f=e("th"),e=e("td");c("th",f);c("td",e)},_unsize:function(b){var a=this.dom[b].floating;a&&("footer"===b||"header"===b&&!this.s.autoWidth)?d("th, td",a).css({width:"",minWidth:""}):a&&"header"===b&&d("th, td",a).css("min-width","")},_horizontal:function(b,a){var e=this.dom[b],c=this.s.position,d=this.s.scrollLeft;e.floating&&d[b]!==a&&(e.floating.css("left",c.left-a),d[b]=a)},_modeChange:function(b,a,e){var c=this.dom[a],f=this.s.position,g=d.contains(this.dom["footer"===a?"tfoot":
"thead"][0],h.activeElement)?h.activeElement:null;if("in-place"===b){if(c.placeholder&&(c.placeholder.remove(),c.placeholder=null),this._unsize(a),"header"===a?c.host.prepend(this.dom.thead):c.host.append(this.dom.tfoot),c.floating)c.floating.remove(),c.floating=null}else"in"===b?(this._clone(a,e),c.floating.addClass("fixedHeader-floating").css("header"===a?"top":"bottom",this.c[a+"Offset"]).css("left",f.left+"px").css("width",f.width+"px"),"footer"===a&&c.floating.css("top","")):"below"===b?(this._clone(a,
e),c.floating.addClass("fixedHeader-locked").css("top",f.tfootTop-f.theadHeight).css("left",f.left+"px").css("width",f.width+"px")):"above"===b&&(this._clone(a,e),c.floating.addClass("fixedHeader-locked").css("top",f.tbodyTop).css("left",f.left+"px").css("width",f.width+"px"));g&&g!==h.activeElement&&g.focus();this.s.scrollLeft.header=-1;this.s.scrollLeft.footer=-1;this.s[a+"Mode"]=b},_positions:function(){var b=this.s.dt.table(),a=this.s.position,e=this.dom,b=d(b.node()),c=b.children("thead"),f=
b.children("tfoot"),e=e.tbody;a.visible=b.is(":visible");a.width=b.outerWidth();a.left=b.offset().left;a.theadTop=c.offset().top;a.tbodyTop=e.offset().top;a.theadHeight=a.tbodyTop-a.theadTop;f.length?(a.tfootTop=f.offset().top,a.tfootBottom=a.tfootTop+f.outerHeight(),a.tfootHeight=a.tfootBottom-a.tfootTop):(a.tfootTop=a.tbodyTop+e.outerHeight(),a.tfootBottom=a.tfootTop,a.tfootHeight=a.tfootTop)},_scroll:function(b){var a=d(h).scrollTop(),e=d(h).scrollLeft(),c=this.s.position,f;if(this.s.enable&&(this.c.header&&
(f=!c.visible||a<=c.theadTop-this.c.headerOffset?"in-place":a<=c.tfootTop-c.theadHeight-this.c.headerOffset?"in":"below",(b||f!==this.s.headerMode)&&this._modeChange(f,"header",b),this._horizontal("header",e)),this.c.footer&&this.dom.tfoot.length))a=!c.visible||a+c.windowHeight>=c.tfootBottom+this.c.footerOffset?"in-place":c.windowHeight+a>c.tbodyTop+c.tfootHeight+this.c.footerOffset?"in":"above",(b||a!==this.s.footerMode)&&this._modeChange(a,"footer",b),this._horizontal("footer",e)}});i.version=
"3.1.3";i.defaults={header:!0,footer:!1,headerOffset:0,footerOffset:0};d.fn.dataTable.FixedHeader=i;d.fn.DataTable.FixedHeader=i;d(h).on("init.dt.dtfh",function(b,a){if("dt"===b.namespace){var e=a.oInit.fixedHeader,c=j.defaults.fixedHeader;if((e||c)&&!a._fixedHeader)c=d.extend({},c,e),!1!==e&&new i(a,c)}});j.Api.register("fixedHeader()",function(){});j.Api.register("fixedHeader.adjust()",function(){return this.iterator("table",function(b){(b=b._fixedHeader)&&b.update()})});j.Api.register("fixedHeader.enable()",
function(b){return this.iterator("table",function(a){a=a._fixedHeader;b=b!==k?b:!0;a&&b!==a.s.enable&&a.enable(b)})});j.Api.register("fixedHeader.disable()",function(){return this.iterator("table",function(b){(b=b._fixedHeader)&&b.s.enable&&b.enable(!1)})});d.each(["header","footer"],function(b,a){j.Api.register("fixedHeader."+a+"Offset()",function(b){var c=this.context;return b===k?c.length&&c[0]._fixedHeader?c[0]._fixedHeader[a+"Offset"]():k:this.iterator("table",function(c){if(c=c._fixedHeader)c[a+
"Offset"](b)})})});return i});




   $(document).ready(function() {
        //$('#tableSales').DataTable()
        //graficar('','','');
        /*$('#tableSales').DataTable({
                            dom: 'Bfrtip',
                            buttons: [ 'excel' ],
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
        $('#cliente').select2(); */


        $('#reporte').change(function() {
            if( $('#reporte').val() == 1 ) {
                $('#filtrosExtra').show();
                $('#ordenamiento').hide();
            } else {
                $('#filtrosExtra').hide();
                $('#ordenamiento').show();
            }
        }).trigger('change');

function pad (n, length) {
    var  n = n.toString();
    while(n.length < length)
         n = "0" + n;
    return n;
}
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
        buscar();
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
<div class="container well">
    <div class="row">
        <div class="col-xs-12 col-md-12">
           <h3>Análisis de Ventas</h3>
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


                        <div class="row"></div>
                    </div>
                    <div class="col-sm-3" id="ordenamiento">
                        <label>Ordenar</label>
                        <select id="orden" class="form-control">
                            <option value="day">Dia</option>
                            <option value="week">Semana</option>
                            <option value="month">Mes</option>
                            <option value="year">Año</option>
                        </select>
                    </div>

                </div>
                <div class="row" id="filtrosExtra">
                    <div class="col-sm-3">
                        <label>Cliente</label>
                        <select  class="form-control" id="cliente">
                            <option value="0">-Seleccion un Cliente-</option>
                            <?php
                           // print_r($ventasIndex['clientes']);
                                foreach ($ventasIndex['clientes'] as $key1 => $value1) {
                                    echo '<option value="'.$value1['id'].'">'.$value1['nombre'].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Empleado</label>
                        <select id="empleado" class="form-control">
                            <option value="0">-Seleccion un Empleado-</option>
                            <?php 
                                foreach ($ventasIndex['usuarios'] as $key2 => $value2) {
                                    echo '<option value="'.$value2['idempleado'].'">'.$value2['nombre'].'</option>';
                                }
                            ?>                            

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
                </div>
                <div class="row">
                <div class="col-sm-3">
                <!-- Elegir reporte -->
                </div>
                    <div class="col-sm-5">

                    </div>
                    <div class="col-sm-3">
                        <!-- Elegir reporte -->
                        <label>Reporte</label>
                        <select id="reporte" class="form-control">
                           <option value="1">Ventas Totales</option>
                            <option value="2" selected>Productos</option>
                            <option value="10">Cortesias</option>
                            <option value="3">Formas de Pago</option>
                            <option value="4">Empleado</option>
                            <option value="5">Cliente</option>
                            <option value="6">Departamento</option>
                            <option value="7">Familia</option>
                            <option value="8">Linea</option>
                            <option value="9">Sucursales</option>                            
                          <!--  <option value="4">Cliente</option> -->
                        </select>
                    </div>
                    <div class="col-sm-1"><br>
                        <button class="btn btn-default" onclick="buscar();">Buscar</button>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row graficasYtotales">
                    <div class="col-sm-12">
                        <div class="panel-group" id="accordion_graficas" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div hrefer class="panel-heading" id="heading_graficas" role="tab" role="button" style="cursor: pointer" data-toggle="collapse" data-parent="#accordion_graficas" href="#tab_graficas" aria-controls="collapse_graficas" aria-expanded="true">
                                <h4 class="panel-title">
                                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                                    <strong>Graficas</strong>
                                </h4>
                            </div>
                            <div id="tab_graficas" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_graficas" >
                                <div class="panel-body" >
                                    <div id="contProducts" style="height:300px;overflow:auto;" class="col-sm-12">
                                        <div class="col-sm-6" id="gDonut" style="height:100%;"></div>
                                        <div class="col-sm-6" id="gLine" style="height:100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
               <!-- <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-default btn-block" onclick="graficar();">Graficas</button>
                    </div>
                </div>
                <div class="row" style="display:none;" id="graficasDiv">
                    <div class="col-sm-12">
                       <div class="col-sm-6" id="gDonut" ></div>
                        <div class="col-sm-6" id="gLine"  style="height:250px;"></div>
                    </div>
                </div> -->
                <div class="row graficasYtotales">
                    <div class="col-sm-5"></div>
                    <div class="col-sm-3">
                    <?php
                                        /*foreach ($ventasGrid['ventas'] as $key => $value) {
                                            if($value['estatus']=='Activa'){
                                                $total +=number_format($value['monto'],2,'.','');
                                            }
                                        } */

                    ?>
                       <h4>Total:<h4 id="montoTotalLabel"></h4></h4>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="row">
                    <div class="col-sm-12" style="overflow:auto;">
                            <div style="width:100% " id="tableDivCont">
                        <!--    <table class="table table-bordered table-hover" id="tableSales">

                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Empleado</th>
                                        <th>Sucursal</th>
                                        <th>Estatus</th>
                                        <th>Impuestos</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($ventasGrid['ventas'] as $key => $value) {
                                            if($value['estatus']=='Activa'){
                                                $estatus = '<span class="label label-success">Activa</span>';
                                            }else{
                                                $estatus = '<span class="label label-danger">Cancelada</span>';
                                            }
                                            echo '<tr class="rows">';
                                            echo '<td>'.$value['folio'].'</td>';
                                            echo '<td>'.$value['fecha'].'</td>';
                                            echo '<td>'.$value['cliente'].'</td>';
                                            echo '<td>'.$value['empleado'].'</td>';
                                            echo '<td>'.$value['sucursal'].'</td>';
                                            echo '<td>'.$estatus.'</td>';
                                            echo '<td>$'.number_format($value['iva'],2).'</td>';
                                            echo '<td>$'.number_format($value['monto'],2).'</td>';
                                            echo '<td><button class="btn btn-primary btn-block" onclick="ventaDetalle('.$value['folio'].');" type="button"><i class="fa fa-list-ul"></i> Detalle</button></td>';
                                            echo '</tr>';
                                            $total +=$value['monto'];
                                        }




                                    ?>
                                </tbody>

                            </table> -->
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
           <!-- Modal modalVentasDetalle -->
<!-- Modal de Ventas -->
    <div id='modalVentasDetalle' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="idFacPanel"></h4>
                </div>
                <div class="modal-body">
                    <div style="height:400px;overflow:auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                    <input id="idVentaHidden" type="hidden">
                                <table class="table table-bordered" id="tableSale">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Descripcion</th>
                                            <th>Cantidad</th>
                                            <th>Precio U.</th>
                                           <!-- <th>Descuento</th> -->
                                            <th>Impuestos</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    <div class="row">
                    <div class="col-sm-6">
                        <div id="pay">

                        </div>
                    </div>
                    <div class="col-sm-3" id="impuestosDiv"></div>
                    <div class="col-sm-3">
                        <div id="subtotalDiv" class="totalesDiv"></div>
                         <div id="ddiv" class="totalesDiv"></div>
                        <div id="totalDiv" class="totalesDiv"></div>
                        <!-- inputs donde se guarda el total y subtotal -->
                        <input type="hidden" id="inputSubTotal">
                        <input type="hidden" id="inputTotal">
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button class="btn btn-warning" onclick="cancelaVenta();"><i class="fa fa-ban" aria-hidden="true"></i> Cancelar</button>
                            <button class="btn btn-primary" onclick="imprime();"><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button>
                            <button class="btn btn-danger" onclick="javascript:$('#modalVentasDetalle').modal('hide');"><i class="fa fa-times" aria-hidden="true"></i> Salir</button>
                        </div>
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

</body>
</html>
