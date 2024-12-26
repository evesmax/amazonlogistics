<head>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="css/datatablesboot.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.min.css">
    <style>
        .p0{padding:0;}
        .glyphicon-refresh-animate {
    -animation: spin .7s infinite linear;
    -ms-animation: spin .7s infinite linear;
    -webkit-animation: spinw .7s infinite linear;
    -moz-animation: spinm .7s infinite linear;
}
@keyframes spin {
    from { transform: scale(1) rotate(0deg);}
    to { transform: scale(1) rotate(360deg);}
}
  
@-webkit-keyframes spinw {
    from { -webkit-transform: rotate(0deg);}
    to { -webkit-transform: rotate(360deg);}
}

@-moz-keyframes spinm {
    from { -moz-transform: rotate(0deg);}
    to { -moz-transform: rotate(360deg);}
}


    </style>
</head>
<body>
    <br>
    <div class="container well" style="padding:25px;margin-bottom: 150px;">
        <div class="row" style="padding-bottom:20px;">
            <div class="col-xs-12 col-md-12" style="padding:0px 0px 0px 3px;"><h3>Agrupacion de insumos a proceso </h3></div>
            <input type="hidden" id="ist" value="0">
            <input type="hidden" id="it" value="0">
            <input type="hidden" id="cadimps" value="0">
            <input type="hidden" id="auxDescG" value="0"> 

        </div>

   <input type="hidden"  id="orden" value="" />

        <div class="col-sm-12">
            <div id="panelprod" class="form-group">
                <label style="padding-top:4px;" class="col-sm-1 control-label text-left">Producto</label>
                <div class="col-sm-4" style="color:#ff0000;">
                    <select id="c_productos"  style="width:100%;">
                        <option value="0">Seleccione</option>
                        <?php foreach ($productos as $k => $v) { ?>
                            <option value="<?php echo $v['id']; ?>"><?php echo $v['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="col-sm-2 text-left" >
                    <button style=" height: 34px;" id="btn_addProd" onclick="buscaAgrupados();"  class="btn btn-default btn-sm btn-block"> Seleccionar</button>
                </div>
                
            </div>
        </div>

        
        <div id="secc_agrupados" class="col-sm-12" style="margin-top:20px;display: none;">
            <div class="col-sm-12">
                <b>Agrupaciones generadas</b>
            </div>
            <div class="col-sm-12">
            <table id="tabla_agrupados" class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th width="20%">ID</th>
                        <th width="40%">Nombre</th>
                        <th width="40%">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tcontenido">
                </tbody>
            </table>
            </div>
        </div>

        <div id="secc_proceso" class="col-sm-12" style="margin-top:20px;display: none;">
            <div class="col-sm-12 nuevaagrupacion">
                <b>Nueva agrupación</b>
            </div>
            <div class="col-sm-12 editargrupacion">
                <b>Editar agrupación</b>
            </div>

            <div class="col-sm-12 p0" style="margin-top:10px;">
                <div class="col-sm-2" style="margin-top:10px;">
                    Nombre:
                </div>

                <div class="col-sm-4" style="margin-top:10px;">
                 <input id="nombreagru" type="text" class="form-control">
                </div>

                <div class="col-sm-6" style="margin-top:10px;">
                    &nbsp;
                </div>
            </div> 
            <div class="col-sm-12 p0" style="margin-top:10px;">
                &nbsp;
            </div>
            
            <div class="col-sm-12"><!--ini-->
                <div class="alert alert-success" hidden id="noedita"> <b> No es posible editar la agrupación, el producto  seleccionado tiene un ciclo iniciado.</b></div>
                <input type="text" name="" id="valueguardar" hidden>

<div class="col-md-6 p0">
<div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="panel-group" id="accordion_insumos" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div hrefer class="panel-heading" id="heading_insumos" role="tab" role="button" style="cursor: pointer" data-toggle="collapse" data-parent="#accordion_insumos" href="#tab_insumos" aria-controls="collapse_insumos" aria-expanded="true">
              <h4 class="panel-title">
                <strong>Insumos del producto</strong>
              </h4>
            </div>
            <div id="tab_insumos" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading_insumos">
              <div class="panel-body">
                <table id="tabla_insumos" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th><strong>Código</stong></th>
                      <th align="center"><strong>Insumo</strong></th>
                      <th align="center"><strong><i class="fa fa-check fa-lg"></i></strong></th>
                    </tr>
                  </thead>
                  <tbody id="cinsumos">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    <!-- -->
    <div id="ti" class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <strong>
              Insumos
            </strong>
          </h4>
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12 col-sm-12" id="div_insumos_producto_agregados">
            <!-- En esta div se cargan los insumos de la receta -->
              <br /><br />
              <blockquote style="font-size: 16px">
                  <p>
                      Selecciona <strong>"Insumos"</strong> para agruparlos.
                  </p>
                </blockquote>
            </div>
          </div>
        </div>
      </div>
    </div>



            </div><!--fin-->
        </div>

        <div id="Botones" class="col-sm-12" style="margin-top:20px; display: none;">
            <div  class="col-sm-2">
                <button style=" height: 34px;" id="btn_guardar" onclick="guardarGrupo();"  class="btn btn-primary btn-sm btn-block"> Guardar</button>
            </div>

        </div>

    </div>

<!-- CHRIS - COMENTARIOS
============================= 
//Librerias genericas 
-->

<script src="../../libraries/jquery.min.js" type="text/javascript"></script>
<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../libraries/select2/dist/js/select2.min.js" type="text/javascript"></script>

<!-- CHRIS - COMENTARIOS
============================= 
//Librerias raiz appministra 
-->
<script src="js/numeric.js" type="text/javascript"></script>
<script src="js/moneda.js" type="text/javascript"></script>
<script src="js/datatables.min.js" type="text/javascript"></script>
<script src="js/dataTables.bootstrap.min.js" type="text/javascript"></script>
<script src="js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="js/bootstrap-datepicker.es.js" type="text/javascript"></script>
</body>

<script>
table=$('#tabla_agrupados').dataTable( {
            "sPaginationType": "full_numbers",
            "bFilter": false,
            "autoWidth": false,
            "bSearchable":false,
            "bInfo":false,
            "paging":   false,
            "ordering": false,
            "info":     false
        });

// table2=$('#tabla_insumos').dataTable( {
//             "sPaginationType": "full_numbers",
//             "bFilter": false,
//             "bSearchable":false,
//             "bInfo":false,
//             "paging":   false,
//             "ordering": false,
//             "info":     false
//         });

    $(function() {

        $('#c_productos').select2();
        

    });

    function guardarGrupo(){
        nombre=$('#nombreagru').val();
        idp=$('#c_productos').val();
        $('#valueguardar').val();
        if(nombre==''){
            alert('Escriba un nombre.');
            return false;
        }
        var table = $('#tabla_insumos').DataTable(); 
        if(table.rows( '.success' ).count()=='0' && $('#valueguardar').val()!=''){
            alert('No hay insumos a guardar.');
            return false;

            }

        $.ajax({
            url:"ajax.php?c=produccion&f=a_guardarGrupo",
            type: 'POST',
            data:{nombre:nombre,idp:idp,guardaredicion: $('#valueguardar').val()},
            success: function(r){
                if(r==0){
                    alert('No hay insumos a guardar.');
                    return false;
                }else{
                    alert('Agrupacion guardada con exito.');
                    location.reload(); 

                }

            }

        });

    }

    function eliAgrupados(id){
        $.ajax({
            url : 'ajax.php?c=produccion&f=eliAgrupados',
            type: 'POST',
            data:{id:id},
                success: function(r){
                   alert('Agrupacion eliminada correctamente');
                   location.reload(); 
                }
            });

    }

    function reloadInsumos(){
          $.ajax({
            url : 'ajax.php?c=produccion&f=reloadInsumos',
            type: 'POST',
                success: function(r){
                    $('#div_insumos_producto_agregados').html(r);
                }
            });
    }

    function buscaAgrupados(){

       var tabla = $('#tabla_insumos').DataTable();
       tabla.destroy();
        
        $('#secc_proceso').hide();
        $('#secc_agrupados').hide();
        $('#Botones').hide();
        reloadInsumos();
        $("#nombreagru").val('');
        $('.nuevaagrupacion').css('display','block');
        $('.editargrupacion').css('display','none');
        $('#valueguardar').val('');
        $('#noedita').hide(); 
        $('#nombreagru').prop("disabled", false);

        idp=$('#c_productos').val();
        if(idp==0){
            alert('Seleccione un producto.');
            return false
        }

        $.ajax({
            url:"ajax.php?c=produccion&f=buscaAgrupados",
            type: 'POST',
            dataType: 'JSON',
            data:{idp:idp},
            success: function(r){
                console.log(r);
                
                if(r.success==1){
                    if(r.agrupados!=0){
                        $('#secc_agrupados').css('display','block');
                        cadagrup='';
                        $.each(r.agrupados, function( k, v ) {
                            acciones='<button style=" width: 100px;" id="eliAgru" onclick="eliAgrupados('+v.id+');"  class="btn btn-danger btn-sm eliminar"> Eliminar</button>    <button style="width:100px;" id="btn_addProd" onclick="buscarAgrupadosVer('+v.id+');" class="btn btn-sm btn-success"> Editar </button>';
                                cadagrup+="<tr><td>"+v.id+"</td><td>"+v.nombre_agrupacion+"</td><td>"+acciones+"</td></tr>";
                        });
                        $('#tcontenido').html(cadagrup);
                       
                                        
                    }else{
                        $('#tcontenido').html('');
                        $('#secc_agrupados').css('display','none');
                    }


                    if(r.explosion!=0){
                        quedan=0;
                        $('#secc_proceso').css('display','block');
                        $('#Botones').show();
                       
                        cadagrup='';
                        obj=Object();
                        $.each(r.explosion, function( k, v ) {
                            obj['id']=v.id;
                            obj['codigo']=v.codigo;
                            obj['nombre']=v.nombre;
                            obj['unidad_nombre']=v.nombreunidad;
                            obj['idunidad']=1;
                            obj['unidad_clave']=v.claveunidad;
                            obj['div']='div_insumos_producto_agregados';
                            obj['cantidadmax']=v.cantidad;
                            obj['usada']=v.usada;
                            obj['disponible']=v.disponible;
                            quedan+=(v.disponible*1);
                            //obj['check']=

                            cadagrup+="<tr style='cursor:pointer;' id='tr_"+v.id+"' onclick='agregar_insumos_producto("+JSON.stringify(obj)+")'><td>"+v.codigo+"</td><td>"+v.nombre+"</td><td><input style='cursor: pointer' disabled='1' type='checkbox' id='check_"+v.id+"' /></td></tr>";

                        });
                        if(quedan==0){
                            $('#secc_proceso').hide();
                            $('#Botones').hide();
                            
                            alert('Se han agotado los insumos para una nueva agrupación.');
                            //return false;
                        }
                        $('#cinsumos').html(cadagrup);
                        //$('#Botones').show();
                
                       // table2.draw();

                    }else{
                        $('#cinsumos').html('');
                        $('#secc_proceso').css('display','none');
                    }
                    //table.clear().draw();
                }else{
                    $('#tcontenido').html('');
                    $('#cinsumos').html('');
                    $('#secc_agrupados').css('display','none');
                    $('#secc_proceso').css('display','none');
                }
            }
        });

    $.ajax({

        url:"ajax.php?c=produccion&f=editaragrupacion",
        type: 'POST',
        dataType: 'JSON',
        data:{
            idp:idp
        },
        success: function(r){
            if (r==1) {


                $('.eliminar').hide(); 

            
            }else{
            
              
                $('.eliminar').show(); 
               

            }
        }
    });

    }

function agregar_insumos_producto($objeto){

$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

$objeto['check']=$('#check_'+$objeto['id']).prop('checked');

  $.ajax({
    data : $objeto,
    url : 'ajax.php?c=produccion&f=agregar_insumos_producto',
    type : 'POST',
    dataType : 'html',
    async:false
  }).done(function(resp) {
    //console.log('----> Done agregar insumo '+$objeto['id']);
   // console.log(resp);

  // Carga la vista a la div
    $('#' + $objeto['div']).html(resp);


    $('#tabla_insumos_agregados input').numeric();

    //$('.selectpicker').selectpicker('refresh');

    //console.log('----> check');
    //console.log($objeto['check']);
    
    var tabla = $('#tabla_insumos').dataTable();
    var tabla = tabla.fnGetNodes();
   $('#tabla_insumos').each(function (index){
    id = $(this,tabla).attr('id');
    
      if(id == 'tr_'+$objeto['id']){

        checkbox = $(this,tabla).find('input');
        checkbox.prop("checked", true);
        $(this,tabla).addClass('success');
        if($objeto['check'] === false){        
          checkbox.prop("checked", true);
          $(this,tabla).addClass('success');
        }else{
          checkbox.prop("checked", false);
          $(this,tabla).addClass('success');
          $(this,tabla).removeClass('success');
        }
      }
      });
     
      
  }).fail(function(resp) {
    console.log('----> Fail agregar insumos por producto');
    console.log(resp);

    $mensaje = 'Error. No se pueden cargar los insumos por producto.';
    $.notify($mensaje, {
      position : "top center",
      autoHide : true,
      autoHideDelay : 5000,
      className : 'error',
      arrowSize : 15
    });
  });

}

function asignar_cant_req($objeto){
    cant =$objeto['cantidad']*1;
    disp =$objeto['disp']*1;

    if(cant>disp){
        alert('Has excedido la cantidad disponible '+disp);
        $('#cant_req_'+$objeto['id']).val(disp);
        $objeto['cantidad']=disp;
    }

    $.ajax({
        data : $objeto,
        url : 'ajax.php?c=produccion&f=asignar_cant_req',
        type : 'POST',
        dataType : 'json'
    }).done(function(resp) {
        console.log('----> Done asignar_cant_req');
        console.log(resp);
}).fail(function(resp) {
        console.log('----> Fail calcular precio');
        console.log(resp);

        $mensaje = 'Error, no se pueden hacer cambios';
        $.notify($mensaje, {
            position : "top center",
            autoHide : true,
            autoHideDelay : 5000,
            className : 'error',
            arrowSize : 15
        });
    });

}

// AM funcion para editar
function buscarAgrupadosVer($idedicion){

    $('#valueguardar').val($idedicion);
    $('#secc_proceso').hide();
    $('#secc_agrupados').hide();
    $('#Botones').hide();

    var tabla = $('#tabla_insumos').DataTable();
    tabla.destroy();
    idp = $('#c_productos').val();


    $.ajax({

        url:"ajax.php?c=produccion&f=editaragrupacion",
        type: 'POST',
        dataType: 'JSON',
        data:{
            idp:idp
        },
        success: function(r){
            if (r==1) {

                $('#nombreagru').prop("disabled", true);
                $('#Botones').hide();
                $('#noedita').show(); 

            }else{

                $('#nombreagru').prop("disabled", false);
                $('#Botones').show();
                $('#noedita').hide(); 
                $('#Botones').show();
               

            }
        }
    });

    $.ajax({
        url:"ajax.php?c=produccion&f=buscaAgrupadosEdicion",
        type: 'POST',
        dataType: 'JSON',
        data:{
            idp:idp,
            idedicion : $idedicion
        },
        success: function(r){
            console.log(r);

            if(r.success==1){
                if(r.agrupados!=0){
                    $('#secc_agrupados').css('display','block');

                }else{
                    $('#tcontenido').html('');
                    $('#secc_agrupados').css('display','none');
                }

                if(r.explosion!=0){
                    $('#secc_proceso').css('display','block');
                    $('.nuevaagrupacion').css('display','none');
                    $('.editargrupacion').css('display','block');

                    $('#nombreagru').val(r.agrupados.map(dat => dat.nombre_agrupacion));
                    var tabla = $('#tabla_insumos').dataTable({ "destroy": true } );
                    var tabla = tabla.fnGetNodes();

                    $(tabla).each(function (index){
                        var id = $(this,tabla).attr('id'); 

                        $('#'+id).removeClass('success');
                        var  idcheck = id.slice(3) 
                        $('#check_'+ idcheck).prop('checked',false);

                    });
                    $.each(r.explosion, function( k, v ) {

                        $('#tr_'+v.id).trigger('click');
                        $('#tr_'+v.id).addClass('success');
                        $('#check_'+ v.id).prop('checked',true);
                        $('#usadomax').prop("disabled", false);
                    });

                }else{
                    $('#cinsumos').html('');
                    $('#secc_proceso').css('display','none');
                }

            }else{
                $('#tcontenido').html('');
                $('#cinsumos').html('');
                $('#secc_agrupados').css('display','none');
                $('#secc_proceso').css('display','none');
            }
        }
    }); 
}


</script>