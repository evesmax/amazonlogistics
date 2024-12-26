<?php
  $sestmp=time();
  $sema=$_POST['sema'];
  include('conexiondb.php');
  if(!isset($_COOKIE['xtructur'])){
    echo 323; exit();
  }else{
      $cookie_xtructur = unserialize($_COOKIE['xtructur']);
      $id_obra = $cookie_xtructur['id_obra'];
  }
  $SQL = "SELECT a.*, concat('PROV-',b.id,' -  ',b.razon_social_sp) nombre FROM constru_altas a inner join constru_info_sp b on b.id_alta=a.id where a.id_obra='$id_obra' and a.borrado=0 AND a.id_tipo_alta=5;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $proveedores[]=$row;
    }
  }else{
    $proveedores=0;
  }
  $SQL = "SELECT a.*, concat('RT-',b.id,' -  ',b.nombre,' ',b.paterno,' ',b.materno) nombre FROM constru_altas a inner join constru_info_tdo b on b.id_alta=a.id where a.id_obra='$id_obra' and a.borrado=0 AND a.id_tipo_alta=1;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $tecnicos[]=$row;
    }
  }else{
    $tecnicos=0;
  }

   $SQL = "SELECT * FROM constru_bit_remesas where id_bit_remesa='$sema';";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    $row = $result->fetch_array();
    $pas=$row['tot_pasiv'];
    $ra=$row['rem_aut'];
  }else{
    $pas=0;
    $ra=0;
  }

  $SQL = "SELECT estatus FROM constru_bit_remesa where id='$sema';";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    $row = $result->fetch_array();
    $estatus=$row['estatus'];
  }else{
    $estatus=1;
  }

?>

<div class="row">
  <div class="col-sm-6">
    <div class="row">
      <div class="col-sm-4">
        <label>Total de pasivos:</label>
        <input class="form-control" id="ttp" value="<?php echo $pas; ?>" disabled="disabled">
      </div>
      <div class="col-sm-4">
        <label>Remesa autorizada:</label>
        <input class="form-control" id="rea" value="<?php echo $ra; ?>" disabled="disabled">
      </div>
    </div>
  </div>
</div>

<script> 
        $(document).ready(function () {

          

          $('#ttp').currency();
$('#preload').css('display','none');
$('#fecente').datepicker({ dateFormat: "yy-mm-dd" }); 

            $("#jq_arbol").jqGrid({
                url:'sql_visualizar_remesa_rep.php?sema=<?php echo $sema; ?>',
                datatype: "json",
                colModel: [
                    { label: 'Proveedor', name: 'Proveedor',  width: 70, sortable:false },
                    { label: 'Fecha', name: 'fecha',  width: 70, sortable:false },
                    { label: 'Estimacion', name: 'estimacion',  width: 70, sortable:false },
                    { label: 'No. Factura', name: 'no_factura',  width: 40, sortable:false },
                   // { label: 'Importe', name: 'importe',  width: 60, sortable:false },
                    //{ label: 'Precio de concurso', name: 'precio',  width: 60, sortable:false,sorttype:"float", formatter:"number" },
                    { 
                        label: 'Importe', 
                        name: 'importe',
                        width: 70,
                        sorttype:"float", 
                        formatter:"number",
                        sortable:false,
                        summaryType:'sum'
                    },
                    { 
                        label: 'Pagado remesas', 
                        name: 'pagado',
                        width: 70,
                        sorttype:"float", 
                        formatter:"number",
                        sortable:false,
                        summaryType:'sum'
                    },
                    { 
                        label: 'Saldo por pagar', 
                        name: 'saldop',
                        width: 70,
                        sorttype:"float", 
                        formatter:"number",
                        sortable:false,
                        summaryType:'sum'
                    },
                    { label: 'Importe pagado', name: 'impsem',  width: 60, sortable:false}
                ],
                loadComplete: function() {
                    $(".jq_arbolghead_0").css("background", "#c0c0c0");
                    $(".jq_arbolghead_1").css("background", "#d0d0d0");
                    $(".jq_arbolghead_2").css("background", "#e0e0e0");
                    $(".jq_arbolghead_3").css("background", "#f0f0f0");
                    $("tr #null").remove();
                    $('input[class^=quis__]').numeric();
                    $('input[class^=quis__]').prop('disabled',true);
                    $('input[class^=quis__]').keyup(function() {

                      var sema = 0;
                      $('input[class^=quis__]').each(function() {
                          sema += Number($(this).val());
                      });

                      ra=$('#rea').val();
                      if( (ra*1)<sema ){
                        alert('La remesa autorizada no puede ser menor al importe capturado');
                        $(this).val(0);
                      }
                    });
                    //$('input[class^=quis_]').prop('disabled',true);
                    $('input.ccbox').on('change', function() {
                        id_oc = $(this).val();
                        //$('input[class^=quis_]').prop('disabled',true);

                        if(!$(this).is(':checked') ){
                          $('.quis_'+id_oc+'_').prop('disabled',true);
                        }else{
                          $('.quis_'+id_oc+'_').prop('disabled',false);
                        }
                       // $('input.ccbox').not(this).prop('checked', false);  
                        
                      
                    });

                },

        cellEdit: true,
        loadonce:true,
        viewrecords: true,
        rowNum:1000000,
        rowList: [], 
        pgbuttons: false,
        pgtext: null,
        autowidth: true,
        height: "450",
        sortname: 'pedis',
        pager: "#jqp_arbol",
        grouping: true,
                groupingView: {
                    groupField: ["Proveedor"],
                    groupColumnShow: [false],
                    groupText: [
          //  "Agrupador: <b>{0}</b> - ${importe}",
                    "<b>{0}</b>"
          ],
                    groupOrder: ["desc"],
                    groupSummary : [true],
          groupSummaryPos: [],
                    groupCollapse: false
                }
            });

            
            jQuery("#jq_arbol").jqGrid('navGrid',"#jqp_arbol",
                {
                  edit:false,
                  add:true,
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
                        $("#jq_arbol").jqGrid('exportarExcelCliente',{nombre:"hojarequis",formato:"excel"});
                        //console.log($("#tb_ejemplo").jqGrid('exportarTextoCliente'));
                    },
                    position: "last"
                });


        });

    </script>

<div class="row">
  <div class="col-xs-12 tablaResponsiva">
    <div class="table-responsive" id="dtabla">
        <input id="sestmp" type="hidden" value="<?php echo $sestmp; ?>">
        <table id="jq_arbol"></table>
        <div id="jqp_arbol"></div>
    </div>
  </div>
</div> 

<div class="row" style="padding-top: 10px;">
  <?php if ($row['estatus']==1){ ?>
    <div class="col-sm-3">
    <button style="width:100%" class="btn btn-primary btn-xm" onclick="autorizarem(2,<?php echo $sema; ?>);"> Autorizar</button>
    </div>
    <div class="col-sm-3">
    <button style="width:100%" class="btn btn-danger btn-xm" onclick="autorizarem(3,<?php echo $sema; ?>);"> Cancelar</button>

    </div>
  <?php } ?>
  <?php if ($row['estatus']==2){ ?>
    <div class="col-sm-6">
      <b><font color="green">Remesa aceptada</font></b>
    </div>
  <?php } ?>
  <?php if ($row['estatus']==3){ ?>
    <div class="col-sm-6">
      <b><font color="#ff0000">Remesa rechazada</font></b>
    </div>
  <?php } ?>
</div>

