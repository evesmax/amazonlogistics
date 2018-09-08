<?php  
  $sestmp=time();
  $id_des=$_POST['id'];
  include('conexiondb.php');
  if(!isset($_COOKIE['xtructur'])){
    echo 323; exit();
  }else{
      $cookie_xtructur = unserialize($_COOKIE['xtructur']);
      $id_obra = $cookie_xtructur['id_obra'];
  }

  $SQL = "SELECT if(amortizado_estimacion is null,0,amortizado_estimacion) amoa, estatus FROM constru_estimaciones_bit_cliente  where id_obra='$id_obra' and borrado=0 AND id_cliente='$id_obra' ORDER BY id desc LIMIT 1;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    $row = $result->fetch_array();
    $amoa=$row['amoa'];
  }else{
    $amoa=0;
  }

  $SQL = "SELECT a.*, b.nombre agru, c.nombre area, dd.especialidad esp, ee.partida, concat('RT-',g.id,' ',h.nombre,' ',h.paterno,' ',h.materno) as tenico FROM constru_estimaciones_bit_cliente a 
  left join constru_agrupador b on b.id=a.id_agru
  left join constru_especialidad c on c.id=a.id_area
  left join constru_area d on d.id=a.id_esp
  left join constru_cat_especialidad dd on dd.id=d.id_cat_especialidad
  left join constru_partida e on e.id=a.id_part 
  left join constru_cat_partidas ee on ee.id=e.id_cat_partida 
  left join constru_altas g on g.id=a.id_autorizo
  left join constru_info_tdo h on h.id_alta=g.id
  where a.id_obra='$id_obra' and a.borrado=0 AND a.id='$id_des' ORDER BY id desc LIMIT 1;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    $row2 = $result->fetch_array();
  }else{
    $row2=0;
  }

  $SQL = "SELECT anticipo as antiorg FROM constru_generales  where id='$id_obra' LIMIT 1;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    $row3 = $result->fetch_array();
  }else{
    $row3=0;
  }
?>
<script> 
function formatoNumero(numero, decimales, separadorDecimal, separadorMiles) {
    var partes, array;

    if ( !isFinite(numero) || isNaN(numero = parseFloat(numero)) ) {
        return "";
    }
    if (typeof separadorDecimal==="undefined") {
        separadorDecimal = ",";
    }
    if (typeof separadorMiles==="undefined") {
        separadorMiles = "";
    }

    // Redondeamos
    if ( !isNaN(parseInt(decimales)) ) {
        if (decimales >= 0) {
            numero = numero.toFixed(decimales);
        } else {
            numero = (
                Math.round(numero / Math.pow(10, Math.abs(decimales))) * Math.pow(10, Math.abs(decimales))
            ).toFixed();
        }
    } else {
        numero = numero.toString();
    }

    // Damos formato
    partes = numero.split(".", 2);
    array = partes[0].split("");
    for (var i=array.length-3; i>0 && array[i-1]!=="-"; i-=3) {
        array.splice(i, 0, separadorMiles);
    }
    numero = array.join("");

    if (partes.length>1) {
        numero += separadorDecimal + partes[1];
    }

    return numero;
}



$(function() {
   
/*

  var number = numeral( $("#iaa").val() );
  var string = number.format('0,0.00');
  //alert(number.value());

  var value = number.value();

  we = numeral( $("#iaa").val() ).format('0,0.00');

  //var string = numeral(1000).format('0,0');
*/
  $( "#cargos" ).keyup(function() {
    subt2 = uf($("#retencion").val())*1 + uf($("#cargos").val())*1 + uf($("#fgarantia").val())*1;
    $("#subt2").val(subt2).currency();
    total = uf($("#subt1").val())*1 - uf($("#subt2").val())*1 + uf($("#iva").val())*1;
    $("#total").val(total).currency();
  });

  $( "#ade1, #ade2, #ade3" ).keyup(function() {
    imp_cont = uf($("#imp_con").val())*1 + uf($("#ade1").val())*1 + uf($("#ade2").val())*1 + uf($("#ade3").val())*1;
    $("#imp_cont").val(imp_cont).currency();
    //$("#imp_cont").val( $("#imp_con").val()*1 + $("#ade1").val()*1 + $("#ade2").val()*1 + $("#ade3").val()*1 );
    
  });



  $('#ade1').numeric();
  $('#ade2').numeric();
  $('#ade3').numeric();

  $('#cargos').numeric();
  $('#fecente').datepicker({ dateFormat: "yy-mm-dd" }); 
      var formEditingOptions = {
        beforeShowForm: function(form){ 
            $('.FormGrid input').css('width','278px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','274px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric(); 
          },
          afterSubmit: function(response, otro){
            console.log(response);
            console.log(otro);
            if(response.statusText=='OK'){
              return [true];
            }else if(response.statusText=='RP'){
              return [false,' Elemento repetido '];
            }else{
              return [false,' Error al editar el registro '];
            }
          },
          closeAfterEdit:true,
          width: 480
      };

//$row[clave],$row[descripcion],$row[unidtext],$row[vol_tope],$row[precio],$row[total]


  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'est_control_cliente'},
    success: function(r){
      sestmp=$('#sestmp').val();
      jQuery("#grid").jqGrid('navGrid','#pcrud',{});
      jQuery("#jq_requisiciones").jqGrid({
        url:'sql_jsest_cliente_rep.php?id_des=<?php echo $id_des; ?>&q=3&sestmp='+sestmp,
        datatype: "json",
        colNames:['Clave','Descripcion','Unidad','Vol. Tope','PU subcontrato', 'Importe', 'Vol anterior', 'Vol. Estimacion','Vol acumulado','Vol ejecutar','Imp. Estimacion'],
        colModel:[
          {name:'id_clave',index:'id_clave',stype: 'select', width:70, sortable:true,editable:true,edittype:"select",
            searchoptions:{sopt:['eq'], value:'0:Selec\"ciona;'+r.insumos },
            editoptions:{value:'0:Selecciona;'+r.insumos,
            dataEvents:[{ 
                type: 'change', fn: function(e) {
                  data='';
                  id_insumo = $(this).val();
                  $('.FormData#lainfo_adic').remove();
                  if(id_insumo>0){
                    $('.FormData#lainfo_adic_load_not').remove();
                    $('<tr rowpos="3" class="FormData" id="lainfo_adic_load">\
                        <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                        <td class="DataTD">&nbsp;\
                      Cargando...\
                        </td>\
                      </tr>').insertAfter('#tr_id_clave');


                  $.ajax({
                    url:'ajax.php',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {opcion:'desc_subcontratista_est',id_codigo:id_insumo,id_des:'<?php echo $id_des; ?>'},
                    success: function(r){
                      console.log(r.datos.totcant);
                      if(r.success==1){

                        $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Vol. Anterior</font></td>\
  <td class="DataTD">&nbsp;\
<input id="volant" disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos.vol_anterior+'">\
  </td>\
</tr>').insertAfter('#tr_id_clave');
                        $('#vol_anterior').val(r.datos.vol_anterior);
                        

                        $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">PU subcontrato</font></td>\
  <td class="DataTD">&nbsp;\
<input disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].precio+'">\
  </td>\
</tr>').insertAfter('#tr_id_clave');

                          $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Descripcion</font></td>\
  <td class="DataTD">&nbsp;\
<input disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].descripcion+'">\
  </td>\
</tr>').insertAfter('#tr_id_clave');
                          $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Unidad</font></td>\
  <td class="DataTD">&nbsp;\
<input disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].unidtext+'">\
  </td>\
</tr>').insertAfter('#tr_id_clave');

                          $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Max Cantidad</font></td>\
  <td class="DataTD">&nbsp;\
<input id="totcant" disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos.totcant+'">\
  </td>\
</tr>').insertAfter('#tr_vol_tope');

                       $('.FormData#lainfo_adic_load').remove();


                        }else{

                          $('.FormData#lainfo_adic_load').remove();

                          $('<tr rowpos="3" class="FormData" id="lainfo_adic_load_not">\
                              <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                              <td class="DataTD">&nbsp;\
                            No hay datos\
                              </td>\
                            </tr>').insertAfter('#tr_id_clave');

                          

                        }
                      }
                    });
                  }
                } 
              }]
            }
          },

          {name:'descripcion',index:'unidad', width:70, editable:false,
            editrules: {edithidden:true},
            hidden:false
          },

          {name:'unidad',index:'unidad', width:50, editable:false,
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'vol_tope',index:'vol_tope', width:70, editable:true,
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'precio',index:'precio', width:70, editable:false,
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'importe',index:'importe', width:70, editable:false, sorttype:"float", formatter:"number", summaryType:'sum',
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'vol_anterior',index:'vol_anterior', width:70, editable:true, sorttype:"float", formatter:"number",
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'vol_estimacion',index:'vol_estimacion', width:70, editable:true, sorttype:"float", formatter:"number",
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'acumulado',index:'acumulado', width:70, editable:false, sorttype:"float", formatter:"number",
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'vol_ejecutar',index:'vol_ejecutar', width:70, editable:false, sorttype:"float", formatter:"number",
            editrules: {edithidden:true},
            hidden:false
          },
          {name:'importe_est',index:'importe_est', width:70, editable:false, sorttype:"float", formatter:"number", summaryType:'sum',
            editrules: {edithidden:true},
            hidden:false
          },
        ],

        rowNum:1000000,
        rowList: [], 
        pgbuttons: false,
        pgtext: null, 
        pager: '#jqp_requisiciones',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        width: "1035",
        height: "300",
        multiselect: false,
        footerrow: true,
        gridComplete: function(){
          
          $('#preload').css('display','none');
          var sum = $("#jq_requisiciones").jqGrid('getCol','importe',false,'sum');
          var sum2 = $("#jq_requisiciones").jqGrid('getCol','importe_est',false,'sum');
          $("#jq_requisiciones").jqGrid('footerData','set',{precio:'Total: ',importe:sum});
          $("#jq_requisiciones").jqGrid('footerData','set',{vol_ejecutar:'Total: ',importe_est:sum2});

    
          
          $("input:not(.ui-pg-input)").currency();
          
          var ids = jQuery("#jq_requisiciones").jqGrid('getDataIDs');
          console.log(ids);
          for(var i=0;i < ids.length;i++){
            var cl = ids[i];
            var nn7 = $('#jq_requisiciones').jqGrid('getCell',ids[i],'nombre');
            be = ''; 
            jQuery("#jq_requisiciones").jqGrid('setRowData',ids[i],{icon:be});
          }
          $("#leg_estimacion").css('display','block');
          
        },
        editurl: "sql_jsest_cliente_rep.php?id_des=<?php echo $id_des; ?>&sestmp="+sestmp,
        caption:"Estimaciones",
        onCellSelect: function(rowid, iRow, iCol, e) {
          //$(this).jqGrid('editGridRow', rowid, formEditingOptions);
        }


      });
      
      jQuery("#jq_requisiciones").jqGrid('navGrid',"#jqp_requisiciones",
        {edit:false,add:false,del:false,search:true,
        },
        {beforeShowForm: function(form){

            $('.FormGrid input').css('width','278px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','278px');
            $('.FormGrid textarea').css('height','80px');
            $('#cantidad').numeric();  

            id_insumo = $('#id_clave').val();
                  $('.FormData#lainfo_adic').remove();
                  if(id_insumo>0){
                    $('.FormData#lainfo_adic_load_not').remove();
                    $('<tr rowpos="3" class="FormData" id="lainfo_adic_load">\
                        <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                        <td class="DataTD">&nbsp;\
                      Cargando...\
                        </td>\
                      </tr>').insertAfter('#tr_id_clave');


                  $.ajax({
                    url:'ajax.php',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {opcion:'desc_insumos',id_insumo:id_insumo},
                    success: function(r){
                      if(r.success==1){

                        $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Precio</font></td>\
  <td class="DataTD">&nbsp;\
<input disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].precio+'">\
  </td>\
</tr>').insertBefore('#tr_fecha_entrega');

                          $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Descripcion</font></td>\
  <td class="DataTD">&nbsp;\
<input disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].descripcion+'">\
  </td>\
</tr>').insertAfter('#tr_id_clave');
                          $('<tr rowpos="3" class="FormData" id="lainfo_adic">\
  <td class="CaptionTD"><font color="#cecece">Unidad</font></td>\
  <td class="DataTD">&nbsp;\
<input disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].unidtext+'">\
  </td>\
</tr>').insertAfter('#tr_id_clave');

                       $('.FormData#lainfo_adic_load').remove();


                        }else{

                          $('.FormData#lainfo_adic_load').remove();

                          $('<tr rowpos="3" class="FormData" id="lainfo_adic_load_not">\
                              <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                              <td class="DataTD">&nbsp;\
                            No hay datos\
                              </td>\
                            </tr>').insertAfter('#tr_id_clave');

                          

                        }
                      }
                    });
                  }
          },
          afterSubmit: function(response, otro){

            if(response.statusText=='OK'){
              return [true];
            }else{
              return [false,' Error al editar el registro '];
            }
          },
          closeAfterEdit:true,
          width: 480
        },
        
        {beforeShowForm: function(form){
          $('#tr_vol_anterior', form).hide();
          //$('#tr_vol_anterior').prop('display','none');
            $('.FormGrid input').css('width','278px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','278px');
            $('.FormGrid textarea').css('height','80px');
            $('#cantidad').numeric(); 
          },
          beforeSubmit: function(postdata, formid){
            totcant=$('#totcant').val();
            vvol=$('#volant').val();
            $('#vol_anterior').val(vvol);
            if((postdata.cantidad*1)>(totcant*1)){
              return [false,' &nbsp; La cantidad es mayor a la permitida'];
            }else if(postdata.cantidad==0 || postdata.cantidad==''){
              return [false,' &nbsp; La cantidad es incorrecta'];
            }else{
              return [true,''];
            }
          },
          afterSubmit: function(response, otro){
            if(response.responseText=='RP'){
              return [false,' &nbsp; Este insumo ya esta agregado  '];
            }else{
              if(response.statusText=='OK'){
                return [true];
              }else{
                return [false,' &nbsp; Error al editar el registro '];
              }
            }
          },
          closeAfterAdd:true,
          width: 480
        },
        {width: 480}, // settings for delete
        {onSearch: function(data){
          $("#jq_requisiciones").jqGrid('setColProp', 'precio_costo', { formatter:"number", });
          $("#jq_requisiciones").jqGrid('setColProp', 'precio_venta', { formatter:"number", });
         }
        } // search options
      ).jqGrid('navButtonAdd', '#jqp_requisiciones', {
        caption: "Exportar Excel",
        buttonicon: "ui-icon-export",
          onClickButton: function() {
                $("#jq_requisiciones").jqGrid('exportarExcelCliente',{nombre:"HOJATEST",formato:"excel"});
                //console.log($("#tb_ejemplo").jqGrid('exportarTextoCliente'));
            },
            position: "last"
        });

      function processAddEdit(response, postdata) {
          var success = true;
          var message = "aaa"
          var new_id = "1";
          return [success,message,new_id];
        }

      

    }
  });

      
});

  </script> 

<div class="row">
  <div class="col-xs-12 tablaResponsiva">
    <div class="table-responsive" id="dtabla">
      <input id="sestmp" type="hidden" value="<?php echo $sestmp; ?>">
      <table id="jq_requisiciones"></table>
      <div id="jqp_requisiciones"></div>
    </div>
  </div>
</div> 

<h5>Estimacion</h5>
<div class="row">
  <div class="col-sm-3">
    <label>Importe de contrato:</label>
    <input class="form-control" id="imp_con" type="text" value="<?php echo $row2['imp_contrato']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Adendum 1:</label>
    <input class="form-control" id="ade1" type="text" value="<?php echo $row2['ade1']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Adendum 2:</label>
    <input class="form-control" id="ade2" type="text" value="<?php echo $row['ade2']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Adendum 3:</label>
    <input class="form-control" id="ade3" type="text" value="<?php echo $row2['ade3']; ?>" disabled="disabled">
  </div>
</div>
<div class="row">
  <div class="col-sm-3">
    <label>Importe total de contrato:</label>
    <input class="form-control" id="imp_cont" type="text" value="<?php echo $row2['imp_tot_contrato']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Anticipo <?php echo $row3['antiorg']; ?>%:</label>
    <input class="form-control" id="anti" type="text" value="<?php echo $row2['anticipo']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Importe amortizado anterior:</label>
    <input class="form-control" id="iaa" type="text" value="<?php echo $row2['amortizado_anterior']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Importe amortizado estimacion:</label>
    <input class="form-control" id="iae" type="text" value="<?php echo $row2['amortizado_estimacion']; ?>" disabled="disabled">
  </div>
</div>
<div class="row">
  <div class="col-sm-3">
    <label>Total amortizado:</label>
    <input class="form-control" id="tota" type="text" value="<?php echo $row2['tot_amortizado']; ?>" disabled="disabled">
  </div>
  <div class="col-sm-3">
    <label>Por amortizar:</label>
    <input class="form-control" id="poramo" type="text" value="<?php echo $row2['por_amortizar']; ?>" disabled="disabled"> 
  </div>
  <div class="col-sm-3"></div>
  <div class="col-sm-3"></div>
</div>
<h5>Generar estimacion</h5>
<div class="row">
  <div class="col-sm-8">
    <h5>&nbsp;</h5>
    <div class="row">
      <div class="col-sm-4">
        <label>Importe esta estimacion:</label>
        <input class="form-control" id="imp_est" type="text" value="<?php echo $row2['imp_estimacion']; ?>" disabled="disabled">
      </div>
      <div class="col-sm-4">
        <label>Subtotal 1:</label>
        <input class="form-control" id="subt1" type="text" value="<?php echo $row2['subtotal1']; ?>" disabled="disabled">
      </div>
      <div class="col-sm-4">
        <label>Fondo de garantia 5%:</label>
        <input class="form-control" id="fgarantia" type="text" value="<?php echo $row2['fondo_garantia']; ?>" disabled="disabled">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Retencion 4%:</label>
        <input class="form-control" id="retencion" type="text" value="<?php echo $row2['retencion']; ?>" disabled="disabled"> 
      </div>
      <div class="col-sm-4">
        <label>Cargos:</label>
        <input class="form-control" id="cargos" type="text" value="<?php echo $row2['cargos']; ?>" disabled="disabled">
      </div>
      <div class="col-sm-4">
        <label>Subtotal 2 (Retenciones):</label>
        <input class="form-control" id="subt2" type="text" value="<?php echo $row2['subtotal2']; ?>" disabled="disabled">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Iva 16%:</label>
        <input class="form-control" id="iva" type="text" value="<?php echo $row2['iva']; ?>" disabled="disabled"> 
      </div>
      <div class="col-sm-4">
        <label>Total:</label>
        <input class="form-control" id="total" type="text" value="<?php echo $row2['total']; ?>" disabled="disabled">
      </div>
      <div class="col-sm-4">
        <label>Solicito:</label>
        <?php echo $row2['tenico']; ?>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <label>Fecha:</label>
        <?php echo $row2['fecha']; ?>
      </div>
      <div class="col-sm-4"></div>
      <div class="col-sm-4"></div>
    </div>
  </div>
  <div class="col-sm-4">
    <h5>Planeacion</h5>
    <div class="row">
      <div class="col-sm-12">
        <?php echo $row2['agru']; ?> > <?php echo $row2['area']; ?> > <?php echo $row2['esp']; ?> > <?php echo $row2['partida']; ?>
      </div>
    </div>
    <?php if ($row2['estatus']==0){ ?>
      <div class="row">
        <div class="col-sm-6">
          <input class="btn btn-primary btnMenu" class="best" style="cursor:pointer;" type="button" value="Autorizar" onclick="autorizarest('cli',1);"> 
        </div>
        <div class="col-sm-6">
          <input class="btn btn-danger btnMenu" class="best" style="cursor:pointer;" type="button" value="Cancelar" onclick="autorizarest('cli',2);">
        </div>
      </div>
    <?php } ?>
    <?php if ($row2['estatus']==1){ ?>
      <div class="row">
        <div class="col-sm-12">
          <b><font color="green">Estimacion aceptada</font></b>
        </div>
      </div>
    <?php } ?>
    <?php if ($row2['estatus']==2){ ?>
      <div class="row">
        <div class="col-sm-12">
          <b><font color="#ff0000">Estimacion rechazada</font></b>
        </div>
      </div>
    <?php } ?>
  </div>
</div>


    