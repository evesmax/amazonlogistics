<script>     
    $(function() {

      
      jQuery("#jq_presupuesto").jqGrid({
        url:'sql_jsinventarios.php?q=3',
        datatype: "json",
        colNames:['Naturaleza','Clave','Descripcion', 'U.M.', 'Cantidad', 'Entradas', 'Salidas', 'Entradas traspaso', 'Salidas traspaso', 'Restante','En almacen'],
        colModel:[

          {name:'naturaleza',index:'naturaleza',stype: 'select', width:75, sortable:false,editable:true,edittype:"select",
            editoptions:{ value:'0:Seleccione;Catalogo:Catalogo;Extra:Extra;Adicional:Adicional;No cobrable:No cobrable',
              dataEvents:[{ 
                type: 'change', fn: function(e) {
                  $('#codigo_clave').val('');
                  $('#descripcion').val('');
                  $('#unidtext').val('');
                  $('#unidad').val('');
                  $('#precio_costo').val('');
                  $('#unidad').val('');
                  $('#descripcion').prop('disabled', false);
                  $('#unidtext').prop('disabled', false);
                  $('#precio_costo').prop('disabled', false);
                  idnat = $(this).val();
                  if(idnat=='Extra'){
                    $('#lainfo_adic').css('visibility','hidden');
                    $('#tr_codigo_clave').css('visibility','visible');
                    $('#natext').html('EXT- ');
                    $('#codigo_clave').css('width','247');
                  }else if(idnat=='No cobrable'){
                    $('#lainfo_adic').css('visibility','hidden');
                    $('#tr_codigo_clave').css('visibility','visible');
                    $('#natext').html('OTO- ');
                    $('#codigo_clave').css('width','245');
                    $('#precio_costo').val('0.00');
                    $('#precio_costo').prop('disabled', true);
                  }else if(idnat=='Adicional'){
                    $('#claveadi').prop('selectedIndex',0);
                    $('#tr_codigo_clave').css('visibility','hidden');
                    $('#lainfo_adic').css('visibility','visible');
                  }else{
                    $('#lainfo_adic').css('visibility','hidden');
                    $('#tr_codigo_clave').css('visibility','visible');
                    $('#natext').html('');
                    $('#codigo_clave').css('width','278');
                  }


                } 
              }]
            },
            searchoptions:{sopt:['eq'], value:'0:Seleccione;Catalogo:Catalogo;Extra:Extra;Adicional:Adicional;No cobrable:No cobrable' }
          },
          {name:'codigo_clave',index:'clave', width:100, editable:true},
          {name:'descripcion',index:'descripcion', width:400, sortable:false,editable:true,edittype:"textarea", editoptions:{rows:"2",cols:"20"}
          }, 
          {name:'U.M',index:'unidtext', width:50, sortable:false,editable:true /*,formatter:verificaPres*/ }, 

          {name:'Cantidad',index:'unidad', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
          {name:'Entradas',index:'Entradas', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
          {name:'Entradas_traspaso',index:'Entradas_traspaso', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
          {name:'Salidas',index:'Salidas', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
          {name:'Salidas_traspaso',index:'Salidas_traspaso', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
          {name:'Restante',index:'Restante', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
          {name:'Almacen',index:'Almacen', width:100, sortable:false,editable:true,sorttype:"float", formatter:"number"},
        ],
        rowNum:1000000,
        rowList: [], 
        pgbuttons: false,
        pgtext: null,
        pager: '#jqp_presupuesto',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        autowidth: true,
        height: "300",
       // multiselect:true,
        footerrow: true,
        gridComplete: function(){
          var ids = jQuery("#jq_presupuesto").jqGrid('getDataIDs');
          $("#jq_presupuesto").jqGrid('setColProp', 'precio_costo', { formatter:"text", });
          $("#jq_presupuesto").jqGrid('setColProp', 'precio_venta', { formatter:"text", });
          var sum_costo = $("#jq_presupuesto").jqGrid('getCol','total_costo',false,'sum');
          var sum_venta = $("#jq_presupuesto").jqGrid('getCol','total_venta',false,'sum');
          $("#jq_presupuesto").jqGrid('footerData','set',{precio_costo:'Total costo:',total_costo:sum_costo,precio_venta:'Total venta:',total_venta:sum_venta});
          for (var i = 0; i < ids.length; i++) 
          {
              var rowId = ids[i];
              

              var rowData = jQuery('#jq_presupuesto').jqGrid ('getRowData', rowId);
              if(rowData.unidtext==''){
                $('#jq_presupuesto').jqGrid('setRowData', rowId, {unidtext:' ',unidad:' ',precio_costo:' ',total_costo:' ' });
                $('tr#'+rowId).find('input').replaceWith(' ');
              }
              if(rowData.naturaleza=='Catalogo'){
                $('tr#'+rowId).find('input').replaceWith(' ');
              }
          }
        },
        beforeSelectRow: function(rowid, e)
        {
            var rowData = $(this).jqGrid('getRowData', rowid);
           if(rowData.naturaleza=='Catalogo'){
              return false;
           }else{
              jQuery("#jq_presupuesto").jqGrid('resetSelection');
              return(true);
           }
        },
        editurl: "sql_jsinventarios.php",
        
      });

      jQuery("#jq_presupuesto").jqGrid('navGrid',"#jqp_presupuesto",
        {edit:false,add:false,del:false,search:true},
        {beforeShowForm: function(form){
            $('#codigo_clave').before('<span id="natext"></span>');

            $('<tr rowpos="3" class="FormData" id="lainfo_adic" style="visibility:hidden;" >\
  <td class="CaptionTD">Clave</td>\
  <td class="DataTD">&nbsp;\
<select id="claveadi" onchange="seda();" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;"><option value="0">Cargando...</option></select>\
  </td>\
</tr>').insertAfter('#tr_naturaleza');

            laclave = $('#codigo_clave').val();
            cad='<option value="0">Seleccione</option>';
              $.ajax({
                url:"ajax.php",
                type: 'POST',
                dataType: 'JSON',
                data:{opcion:'claves_recurso'},
                success: function(r){
                  if(r.success==1){
                    $.each( r.datos, function( k, v ) {
                      cad+='<option value="'+v.id+'">'+v.codigo+'</option>';
                    });
                  }else{
                      cad+='<option value="0">No hay recursos dados de alta</option>';
                  }
                  $('#claveadi').html('');
                  $('#claveadi').html(cad);
                  $("#claveadi option:contains("+laclave+")").attr('selected', 'selected');
                }
              });

            $('.FormGrid input').css('width','270px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','274px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric();
            idnat = $('#naturaleza').val();
            if(idnat=='Extra'){
              $('#codigo_clave').val($('#codigo_clave').val().substring(4));
              $('#natext').html('EXT- ');
              $('#codigo_clave').css('width','247');
            }else if(idnat=='No cobrable'){
              $('#codigo_clave').val($('#codigo_clave').val().substring(4));
              $('#natext').html('OTO- ');
              $('#codigo_clave').css('width','247');
              $('#precio_costo').prop('disabled', true);
            }else if(idnat=='Adicional'){
              
              $('#tr_codigo_clave').css('visibility','hidden');
              $('#lainfo_adic').css('visibility','visible');
              $('#descripcion').prop('disabled', true);
              $('#unidtext').prop('disabled', true);
              $('#precio_costo').prop('disabled', true);

            }

          },
          closeAfterEdit:true,
          width: 400
        },
        {beforeShowForm: function(form){ 
            $('#codigo_clave').before('<span id="natext"></span>'); 

            $('<tr rowpos="3" class="FormData" id="lainfo_adic" style="visibility:hidden;" >\
  <td class="CaptionTD">Clave</td>\
  <td class="DataTD">&nbsp;\
<select id="claveadi" onchange="sedai();" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;"><option value="0">Cargando...</option></select>\
  </td>\
</tr>').insertAfter('#tr_naturaleza');


            $('.FormGrid input').css('width','278px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','278px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric();

            cad='<option value="0">Seleccione</option>';
             $.ajax({
                url:"ajax.php",
                type: 'POST',
                dataType: 'JSON',
                data:{opcion:'claves_insumos'},
                success: function(r){
                  if(r.success==1){
                    $.each( r.datos, function( k, v ) {
                      cad+='<option value="'+v.id+'">'+v.clave+'</option>';
                    });
                  }else{
                      cad+='<option value="0">No hay recursos dados de alta</option>';
                  }

                  $('#claveadi').html(cad);
                    
                }
            });

            

          },
          closeAfterAdd:true,
          width: 480
        },
        {}, // settings for delete
        {onSearch: function(data){
          $("#jq_presupuesto").jqGrid('setColProp', 'precio_costo', { formatter:"number", });
          $("#jq_presupuesto").jqGrid('setColProp', 'precio_venta', { formatter:"number", });
         }
        } // search options
      ).jqGrid('navButtonAdd', '#jqp_presupuesto', {
        caption: "Exportar Excel",
        buttonicon: "ui-icon-export",
          onClickButton: function() {
                $("#jq_presupuesto").jqGrid('exportarExcelCliente',{nombre:"HOJATEST",formato:"excel"});
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
              <div class="navbar-brand" style="color:#333;">Inventarios</div>
          </div>
        </div>
        <div class="row">
        <div class="col-xs-12 tablaResponsiva">
          <div class="table-responsive" id="dtabla">
            <table id="jq_presupuesto"></table>
            <div id="jqp_presupuesto"></div>
          </div>
        </div>
      </div>
      </div>
    </div>

</body>
  
  
  
