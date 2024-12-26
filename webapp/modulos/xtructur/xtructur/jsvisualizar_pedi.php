<?php
///chais

    $SQL = "SELECT DISTINCT a.id, d.nombre FROM constru_pedis a 
    left JOIN constru_info_tdo d on d.id_alta=a.solicito
      where a.id_obra='$idses_obra' ORDER BY a.id DESC";
      $result = $mysqli->query($SQL);
      if($result->num_rows>0) {
        while($row = $result->fetch_array() ) {
          $vercompras[]=$row;
        }
      }else{
        $vercompras=0;
      }    
///chais

      
?>
<script> 
        $(document).ready(function () {
            $("#jq_arbol").jqGrid({
                url:'sql_visualizar_pedi.php',
                datatype: "json",
                colModel: [
                    { label: 'Orden', name: 'Orden', width: 255, sortable:false },
                    { label: 'Solicito', name: 'Solicito',  width: 70, sortable:false },
                    { label: 'Requisicion', name: 'Requisicion',  width: 70, sortable:false },
                    { label: 'Proveedor', name: 'prov',  width: 70, sortable:false },
                    { label: 'Area', name: 'area',  width: 60, sortable:false },
                    { label: 'Especialidad', name: 'especialidad',  width: 60, sortable:false },
                    { label: 'Partida', name: 'partida',  width: 60, sortable:false },
                    { label: 'Clave', name: 'clave',  width: 70, sortable:false },
                    { label: 'Descripcion', name: 'descripcion',  width: 70, sortable:false },
                    { label: 'U.M', name: 'unidad',  width: 30, sortable:false },
                    { label: 'Cantidad', name: 'cantidad',  width: 40, sortable:false },
                    { label: 'PU concurso', name: 'precio',  width: 35, sortable:false,sorttype:"float", formatter:"number" },
                    { 
                        label: 'Importe concurso', 
                        name: 'importec',
                        width: 70,
                        sorttype:"float", 
                        formatter:"number",
                        sortable:false,
                        summaryTpl: "<b>{0}</b>",
                        summaryType:'sum'
                    },
                    { label: 'PU compra', name: 'precioc',  width: 35, sortable:false,sorttype:"float", formatter:"number" },
                    { 
                        label: 'Importe compra', 
                        name: 'importe',
                        width: 70,
                        sorttype:"float", 
                        formatter:"number",
                        sortable:false,
                        summaryTpl: "<b>{0}</b>",
                        summaryType:'sum'
                    },
                ],
                loadComplete: function() {
                    $(".jq_arbolghead_0").css("background", "#c0c0c0");
                    $(".jq_arbolghead_1").css("background", "#d0d0d0");
                    $(".jq_arbolghead_2").css("background", "#e0e0e0");
                    $(".jq_arbolghead_3").css("background", "#f0f0f0");
                    $("tr #null").remove();

                },
        loadonce:true,
        viewrecords: true,
        rowNum:1000000,
        rowList: [], 
        pgbuttons: false,
        pgtext: null,  
        autowidth: true,
        height: "300",
        sortname: 'pedis',
        pager: "#jqp_arbol",
        grouping: true,
                groupingView: {
                    groupField: ["Orden", "Solicito", "Requisicion"],
                    groupColumnShow: [false, false, false],
                    groupText: [
          //  "Agrupador: <b>{0}</b> - ${importe}",
                    "Orden: <b>{0}</b>",
                    "Solicito: <b>{0}</b>",
                    "Requisicion: <b>{0}</b>"
          ],
                    groupOrder: ["asc", "asc", "asc"],
                    groupSummary : [false, false, true],
          groupSummaryPos: [],
                    groupCollapse: false
                }
            });

            
            jQuery("#jq_arbol").jqGrid('navGrid',"#jqp_arbol",
                {
                  edit:false,
                  add:false,
                  del:false,
                  search:false
                },
                {},
                {beforeShowForm: function(form){ 
                    $('.FormGrid input').css('width','270px');
                    $('.FormGrid select').css('width','278px');
                    $('.FormGrid textarea').css('width','274px');
                    $('.FormGrid textarea').css('height','80px');
                    $('#unidad').numeric(); 
                    $('#precio_costo').numeric(); 
                    $('#precio_venta').numeric();
                  },
                  closeAfterAdd:true,
                  width: 600
                },
                {width: 480}, // settings for delete
                {} // search options
              ).jqGrid('navButtonAdd', '#jqp_arbol', {
                caption: "Exportar Excel",
                buttonicon: "ui-icon-export",
                  onClickButton: function() {
                        $("#jq_arbol").jqGrid('exportarExcelCliente',{nombre:"hojarequisComp",formato:"excel"});
                        //console.log($("#tb_ejemplo").jqGrid('exportarTextoCliente'));
                    },
                    position: "last"
                });
        });

    </script>

 <body>

    <div id="divcuandohayobra" class="row">
      <div class="col-sm-12">
        <div class="navbar navbar-default"  style="margin-top:10px;">
          <div class="navbar-header">
              <div class="navbar-brand" style="color:#333;">Autorizacion de ordenes de compra</div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 tablaResponsiva">
            <div class="table-responsive" id="dtabla">
                <table id="jq_arbol"></table>
                <div id="jqp_arbol"></div>
            </div>
          </div>
        </div>
      </div>
    </div>




  <div class="row">&nbsp;</div>
  <div class="panel panel-default" >
      <!-- Panel Heading -->
      <div class="panel-heading">
      <div class="panel-title">Generar PDF</div>
      </div><!-- End panel heading -->

      <!-- Panel body -->
      <div class="panel-body" >
        <div class="row">
            <div class="col-sm-3 col-xs-8">
              <select class="form-control" id="compraver">
                <option selected="selected" value="0">Selecciona una Compra</option>
                <?php 
                if($vercompras!=0){
                  foreach ($vercompras as $k => $v) { ?>
                    <option value="<?php echo $v['id']; ?>">OC-<?php echo $v['id'];?></option>
                  <?php } ?>
                <?php }else{ ?>
                  <option value="0">No hay Ordenes de Compra</option>
                <?php } ?>
              </select>
            </div>
            

            <div class="col-sm-1">
              

              <button id="btnpdfcomp" class="btn btn-primary btn-xm pull-right" onclick="pdfcompras('comp');"><span class="glyphicon glyphicon-download"></span> PDF</button>

            </div>

        </div>

      </div>
  </div>

  </body>



