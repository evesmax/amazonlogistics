<script> 
    $(function() {
      $.ajax({
        url:'ajax.php',
        type: 'POST',
        data: {opcion:'constructoras'},
        success: function(r){
          console.log(r);
      var formEditingOptions = {
        beforeShowForm: function(form){ 
            $('.FormGrid input').css('width','320px');
            $('.FormGrid select').css('width','328px');
            $('.FormGrid textarea').css('width','324px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric(); 
            $('#honorarios').numeric(); 
            $('#iva').numeric(); 
            $('#presupuesto').numeric(); 
            $('#anticipo').numeric(); 
            $('#ade1').numeric(); 
            $('#ade2').numeric(); 
            $('#ade3').numeric(); 
            $('#ade4').numeric(); 

          },
          beforeSubmit: function(postdata, formid){
            inicio=$('#inicio').val();
            termino=$('#termino').val();
            presupuesto=$('#presupuesto').val();
            anticipo=$('#anticipo').val();

            if(inicio == 0 || termino == 0){
              return [false, '&nbsp; Introduzca la fecha'];
              }else if(Date.parse(inicio) >= Date.parse(termino))
              {
                return [false, '&nbsp; La fecha de Inicio debe ser anterior a la fecha de Termino'];
              }else if(presupuesto <= 0){
                return [false, '&nbsp; Introduzca el Presupuesto'];
              }else if(anticipo < 0){
                return [false, '&nbsp; Introduzca el Anticipo'];
              }else{ 
                cerrar_session(modulo);
                return[true,''];
              }
          },
          closeAfterEdit:true,
        width: 500,
        recreateForm: true
      };

      
      jQuery("#rowed22").jqGrid({
        url:'sql_jsaltaobra.php?q=3',
        datatype: "json",
        colNames:['Obra', 'Cliente', 'Licitacion No.', 'Construye', 'Clave', 'Contrato', 'Direccion de obra', 'Inicio', 'Termino', 'IVA', 'Presupuesto', 'Anticipo %', 'Adendum 1', 'Adendum 2', 'Adendum 3', 'Adendum 4', 'Director de obra', 'Super intendencia', 'Control de obra', 'Supervision', 'Fecha de contrato','No. de compromiso','Fecha de compromiso','Numero de obra','Telefono'],
        colModel:[
          {name:'obra',index:'obra', width:200, sortable:false,editable:true}, 
          {name:'cliente',index:'cliente', width:200, sortable:false,editable:true},
          {name:'licitacion',index:'licitacion', width:140, sortable:false,editable:true},
          {name:'construye',index:'construye',stype: 'select', width:140, sortable:false,editable:true,edittype:"select",editoptions:{value:r},searchoptions:{sopt:['eq'], value:r },
            editrules: {edithidden:true},
            hidden:true
          }, 
          {name:'clave',index:'clave', width:70, sortable:false,editable:true}, 
          {name:'contrato',index:'contrato', width:0, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          }, 
          {name:'localizacion',index:'localizacion', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:"inicio",index:"inicio",width:50,align:"left",formatter:"date",editable:true,sorttype: "date",formatoptions: {srcformat: "Y-m-d H:i:s", newformat: "Y-m-d"},
            editoptions:{ 
              dataInit: function(el){ 
                setTimeout(function(){ 
                  $(el).datepicker({ dateFormat: "yy-mm-dd" }); 
                }, 200); 
              }
            },
            editrules: {edithidden:true},
            hidden:true
          }, 
          {name:"termino",index:"termino",width:50,align:"left",formatter:"date",editable:true,sorttype: "date",formatoptions: {srcformat: "Y-m-d H:i:s", newformat: "Y-m-d"},
            editoptions:{ 
              dataInit: function(el){ 
                setTimeout(function(){ 
                  $(el).datepicker({ dateFormat: "yy-mm-dd" }); 
                }, 200); 
              }
            },
            editrules: {edithidden:true},
            hidden:true
          },
         /* {name:'hon',index:'hon', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          }, */
          {name:'iva',index:'iva', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'presupuesto',index:'presupuesto', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'anticipo',index:'anticipo', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'ade1',index:'ade1', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'ade2',index:'ade2', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'ade3',index:'ade3', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'ade4',index:'ade4', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'director',index:'director', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'superintendencia',index:'superintendencia', width:150, sortable:false,editable:true,hidden:true, editrules: 
            {edithidden:true} 
          },
          {name:'control',index:'control', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          }, 
          {name:'supervision',index:'supervision', width:150, sortable:false,editable:true, hidden:true, editrules: 
            {edithidden:true} 
          }, 
          {name:"fecha_contrato",index:"fecha_contrato",width:50,align:"left",formatter:"date",editable:true,sorttype: "date",formatoptions: {srcformat: "Y-m-d H:i:s", newformat: "Y-m-d"},
            editoptions:{ 
              dataInit: function(el){ 
                setTimeout(function(){ 
                  $(el).datepicker({ dateFormat: "yy-mm-dd" }); 
                }, 200); 
              }
            },
            editrules: {edithidden:true},
            hidden:true
          },
          {name:'no_compromiso',index:'no_compromiso', width:150, sortable:false,editable:true,hidden:true,editrules: 
          {edithidden:true} 
          },
          {name:"fecha_compromiso",index:"fecha_compromiso",width:50,align:"left",formatter:"date",editable:true,sorttype: "date",formatoptions: {srcformat: "Y-m-d H:i:s", newformat: "Y-m-d"},
            editoptions:{ 
              dataInit: function(el){ 
                setTimeout(function(){ 
                  $(el).datepicker({ dateFormat: "yy-mm-dd" }); 
                }, 200); 
              }
            },
            editrules: {edithidden:true},
            hidden:true
          },
          {name:'no_obra',index:'no_obra', width:150, sortable:false,editable:true,hidden:true,editrules: 
          {edithidden:true} 
          },
          {name:'telefono',index:'telefono', width:150, sortable:false,editable:true,hidden:true,editrules: 
          {edithidden:true} 
          }
        ],
        loadComplete: function() {
                    $(".jq_arbolghead_0").css("background", "#c0c0c0");
                    $(".jq_arbolghead_1").css("background", "#d0d0d0");
                    $(".jq_arbolghead_2").css("background", "#e0e0e0");
                    $(".jq_arbolghead_3").css("background", "#f0f0f0");
                    $("tr #null").remove();

        },
        rowNum:1000000,
        rowList: [], 
        pgbuttons: false,
        pgtext: null,  
        pager: '#prowed22',
        sortname: 'id',
        viewrecords: true,
        autowidth: true,  
        sortorder: "desc",
        editurl: "sql_jsaltaobra.php",

        multiselect:true,
        height:260

      });

      jQuery("#rowed22").jqGrid('navGrid',"#prowed22",
        {edit:true,add:true,del:true,search:true},
        formEditingOptions,
        {beforeShowForm: function(form){ 
            $('.FormGrid input').css('width','270px');
            $('.FormGrid select').css('width','278px');
            $('.FormGrid textarea').css('width','274px');
            $('.FormGrid textarea').css('height','80px');
            $('#unidad').numeric(); 
            $('#precio_costo').numeric(); 
            $('#precio_venta').numeric();

            $('#iva').numeric(); 
            $('#presupuesto').numeric(); 
            $('#anticipo').numeric(); 
            $('#ade1').numeric(); 
            $('#ade2').numeric(); 
            $('#ade3').numeric(); 
            $('#ade4').numeric(); 
          },
          beforeSubmit: function(postdata, formid){
            inicio=$('#inicio').val();
            termino=$('#termino').val();
            presupuesto=$('#presupuesto').val();
            anticipo=$('#anticipo').val();

            if(inicio == 0 || termino == 0){
              return [false, '&nbsp; Introduzca la fecha'];
              }else if(Date.parse(inicio) >= Date.parse(termino))
              {
                return [false, '&nbsp; La fecha de Inicio debe ser anterior a la fecha de Termino'];
              }else if(presupuesto <= 0){
                return [false, '&nbsp; Introduzca el Presupuesto'];
              }else if(anticipo < 0){
                return [false, '&nbsp; Introduzca el Anticipo'];
              }else{ 
                cerrar_session(modulo);
                return[true,''];
              }
          },
          closeAfterAdd:true,
          width: 600
        },
        {width: 480}, // settings for delete
        {} // search options
      ).jqGrid('navButtonAdd', '#prowed22', {
        caption: "Exportar Excel",
        buttonicon: "ui-icon-export",
          onClickButton: function() {
                $("#rowed22").jqGrid('exportarExcelCliente',{nombre:"HOJATEST",formato:"excel"});
            },
            position: "last"
        });
      }
    });
});
</script> 
<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="css/dialogo.css" type="text/css">
<style>
  @media print{
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
  .btnMenu{
    border-radius: 0; 
    width: 100%;
    margin-bottom: 0.3em;
    margin-top: 0.3em;
  }
  .row
  {
      margin-top: 0.5em !important;
  }
  .titulo, h4, h3{
      background-color: #eee;
      padding: 0.4em;
  }
  .modal-title{
    background-color: unset !important;
    padding: unset !important;
  }
  .nmwatitles, [id="title"] {
    padding: 8px 0 3px !important;
    background-color: unset !important;
  }
  .select2-container{
    width: 100% !important;
  }
  .select2-container .select2-choice{
    background-image: unset !important;
    height: 31px !important;
  }
  .twitter-typeahead{
    width: 100% !important;
  }
  .tablaResponsiva{
      max-width: 100vw !important; 
      display: inline-block;
  }
  /*
  .table tr, .table td{
    border: none !important;
  }
  */
  .ms-container{
    width: 100% !important;
  }
  .ms-selectable, .ms-selection{
    margin-top: 1em;
  }
</style>
<body>
  <div class="container" style="width:100%">
    <div class="row">
      <div class="col-sm-10 col-sm-offset-1">
        <div class="navbar navbar-default"  style="margin-top:10px;">
          <div class="navbar-header">
              <div class="navbar-brand" style="color:#333;">Alta de obra</div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 tablaResponsiva">
            <div class="table-responsive" id="dtabla">
              <table id="rowed22">
              </table>
              <div id="prowed22">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
