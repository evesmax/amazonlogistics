<script>     
$(function() {
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
            if(response.statusText=='OK'){
              return [true];
            }else{
              return [false,' Error al editar el registro '];
            }
          },
          closeAfterEdit:true,
          width: 480
      };

      $.ajax({
        url:'ajax.php',
        type: 'POST',
        dataType: 'JSON',
        data: {opcion:'familiast'},
        success: function(r){


      
      jQuery("#jq_alta_categoria").jqGrid({
        url:'sql_jstabtecnicos.php?q=3',
        datatype: "json",
        colNames:['Familia','Clave categoria','Categoria','Salario mensual'],
        colModel:[
          {name:'id_familia',index:'id_familia',stype: 'select', width:70, sortable:true,editable:true,edittype:"select",editoptions:{value:r.familias},searchoptions:{sopt:['eq'], value:r.familias }
          },
          {name:'clave_cat',index:'clave_cat', width:45, editable:false,
            editrules: {edithidden:false},
            hidden:false
          },
          {name:'categoria',index:'categoria', width:70, editable:true,
            editrules: {edithidden:false},
            hidden:false
          },
          {name:'sal_mensual',index:'sal_mensual', width:35, editable:true, formatter:'currency',
            formatoptions:{decimalSeparator:".", thousandsSeparator: "", decimalPlaces: 2, prefix: "$"},
            editrules: {edithidden:false},
            hidden:false
          }
        ],

        rowNum:1000000,
        rowList: [], 
        pgbuttons: false,
        pgtext: null, 
        pager: '#jqp_alta_categoria',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        autowidth: true,
        height: "300",
        multiselect: true,
        gridComplete: function(){
          var ids = jQuery("#jq_alta_categoria").jqGrid('getDataIDs');
          console.log(ids);
          for(var i=0;i < ids.length;i++){
            var cl = ids[i];
            var nn7 = $('#jq_alta_categoria').jqGrid('getCell',ids[i],'nombre');
            be = ''; 
            jQuery("#jq_alta_categoria").jqGrid('setRowData',ids[i],{icon:be});
          }
        },
        editurl: "sql_jstabtecnicos.php",
        onCellSelect: function(rowid, iRow, iCol, e) {
         // $(this).jqGrid('editGridRow', rowid, formEditingOptions);
        }


      });

      jQuery("#jq_alta_categoria").jqGrid('navGrid',"#jqp_alta_categoria",
        {edit:true,add:true,del:true,search:true,
        },
        {beforeShowForm: function(form){ 
            $('.FormGrid input').css('width','278px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','274px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric(); 
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
            $('.FormGrid input').css('width','278px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','274px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric(); 
          },
          afterSubmit: function(response, otro){
            if(response.statusText=='OK'){
              return [true];
            }else{
              return [false,' Error al guardar el registro '];
            }
          },
          closeAfterAdd:true,
          width: 480
        },
        {}, // settings for delete
        {onSearch: function(data){
          $("#jq_alta_categoria").jqGrid('setColProp', 'precio_costo', { formatter:"number", });
          $("#jq_alta_categoria").jqGrid('setColProp', 'precio_venta', { formatter:"number", });
         }
        } // search options
      ).jqGrid('navButtonAdd', '#jqp_alta_categoria', {
        caption: "Exportar Excel",
        buttonicon: "ui-icon-export",
          onClickButton: function() {
                $("#jq_alta_categoria").jqGrid('exportarExcelCliente',{nombre:"HOJATEST",formato:"excel"});
                //console.log($("#tb_ejemplo").jqGrid('exportarTextoCliente'));
            },
            position: "last"
        });

      
    }
  });
});

  </script> 

  <body>

    <div id="divcuandohayobra" class="row">
      <div class="col-sm-12">
        <div class="navbar navbar-default"  style="margin-top:10px;">
          <div class="navbar-header">
              <div class="navbar-brand" style="color:#333;">Tabulador tecnico administrativo</div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 tablaResponsiva">
            <div class="table-responsive" id="dtabla">
                <table id="jq_alta_categoria"></table>
                <div id="jqp_alta_categoria"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

</body>
  