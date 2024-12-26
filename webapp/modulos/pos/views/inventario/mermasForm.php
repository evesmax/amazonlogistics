<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Mermas</title>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/inventario.js"></script>
<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>


    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
                $('#producto, #tipo, #proveedor, #almacen, #usuario').select2();
                $('#usuario').attr('disabled', 'disabled');
                $('#costo, #precio, #costoT, #uventa , #tipoproducto').attr('readonly', 'true');                                           
        }); 
    </script>
</head>
<?php 
//echo json_encode($productos);

 ?>
<body>
    <div class="container well">
        <div class="row">
            <h3>Mermas</h3>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <button class="btn btn-default btn-block" onclick="cambia2();"> <i class="glyphicon glyphicon-arrow-left"></i> Regresar</button>
            </div>
            <div class="col-sm-2">
                <button class="btn btn-primary btn-block" onclick="saveMerma();"> <i class="glyphicon glyphicon-floppy-disk"></i>Guardar</button>                
            </div>
        </div>
            <div class="row" id="contenido2">
                <br>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Generación de Mermas </h3>
                    </div>
                    <div class="panel-body">
                        <!--
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Selecciona el producto que deseas dar de baja</label>
                            </div>
                        </div>
                        -->
                        <div class="row">
                        <input type="hidden"  id="idnewmerma" value="<?php echo $productos['lastMerma']*1 +1; ?>">
                            <div class="col-sm-3">
                                <input type="hidden"  id="hiddenCarac_input">
                                <input type="hidden"  id="hiddenLote_input">
                                <input type="hidden"  id="hiddenExis_input">
                                <label>Proveedor</label>
                                <input type="hidden">
                                <select id="proveedor" class="form-control">
                                    <option value="0">-Selecciona un Proveedor-</option>
                                <?php 
                                    foreach ($productos['proveedores'] as $key => $value) {
                                        echo '<option value="'.$value['idPrv'].'">'.$value['razon_social'].'</option>';
                                    }

                                ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label>Producto</label>
                                <select id="producto" class="form-control" onchange="buscaCaracteristicas();">
                                <option value="0">Selecciona un Producto</option>
                                </select>
                            </div> 
                            <div class="col-sm-2">
                                <label>Tipo de Producto</label>
                                <input type="text" class="form-control" id ="tipoproducto">
                            </div>                            
                            <div class="col-sm-2">
                                <label>Almacén</label>
                                <select id="almacen" class="form-control">
                                    <option value="0">-Selecciona Almacén-</option>
                                <?php 
                                    foreach ($productos['almacenes'] as $key => $value) {
                                        echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';
                                    }

                                ?>                                    
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label>Usuario</label>
                                <select id="usuario" class="form-control">                                    
                                <?php 
                                    foreach ($productos['usuarios'] as $key => $value) {
                                        echo '<option value="'.$value['idempleado'].'" selected>'.$value['usuario'].'</option>';
                                    }

                                ?>
                                </select>                            
                            </div>                            
                        </div>
                        <div class="row">
                            <div id="divCantidad" class="col-sm-1">
                                <label>Cantidad</label>
                                <input class="form-control" id="cantidad" onblur="validaExis();" value="1">
                            </div>

                            <div class="col-sm-2">
                                <label>Unidad de Venta</label>
                                <input type="text" class="form-control" id="uventa">
                            </div>

                            <div class="col-sm-3">
                                <label>Costo unitario</label>
                                <input type="text" class="form-control" id="costo">
                                <input type="hidden" class="form-control" id="factor">
                            </div>

                            <div class="col-sm-3">
                                <label>Costo Total</label>
                                <input type="text" class="form-control" id="costoT">
                            </div>

                            <div class="col-sm-3">
                                <label>Precio</label>
                                <input type="text" class="form-control" id="precio">
                            </div> 
                                                                                                             
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <label>Tipo</label>
                                <select id="tipo" class="form-control">
                                    <option value="0">-Selecciona un Tipo-</option>
                                <?php 
                                    foreach ($productos['tipo'] as $key => $value) {
                                        echo '<option value="'.$value['id'].'" texto = "'.$value['tipo_merma'].'">'.$value['tipo_merma'].'</option>';
                                    }

                                ?>
                                </select>                            
                            </div>
                            <div class="col-sm-1">
                                <label style="color:white;">.</label>
                                <button class="btn btn-secondary btn-block" type="button" data-toggle="modal" data-target="#modaltipo"></span>...</button>                                  
                            </div> 
                            <div class="col-sm-6">
                                <label>Observaciones</label>
                                <textarea  id="obse" cols="25" rows="1" class="form-control"></textarea>
                            </div>                                                         
                            <div class="col-sm-3">
                                <br>
                               <!-- <button class="btn btn-default btn-block" onclick="agregaMerma();" style="">Agregar</button> -->
                                <div id="guardaDiv2">
                                    <button type="button" class="btn btn-info btn-block" onclick="agregaMerma();"><span class="glyphicon glyphicon-plus"></span> Agregar</button>
                                </div>
                                <div id="sded2" style="display:none;"><i class="fa fa-refresh fa-spin fa-3x"></i></div>
                            </div>                            
                        </div> 


                        <br>
                        <div class="col-sm-3 pull-right">
                                <label>Costo Total Merma</label>
                                <input type="text" class="form-control" id="costoTotal" readonly>
                            </div> 
                        <div id="tableDiv" style="display:none;">
                            <div class="row">
                                <div class="col-sm-12">
                                     <hr></hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover" id="tableMermas">
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Producto</th>
                                                <th>Almacén</th>
                                                <th>Cantidad</th>
                                                <th>Costo</th>
                                                <th>Total</th>
                                                <th>Tipo</th>                                                                                                
                                                <th>Observaciones</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10"></div>
                                <div class="col-sm-2">
                                    <!--<button class="btn btn-default" onclick="saveMerma();">Guardar Merma</button> -->

                                   <!-- <div id="guardaDiv"><button type="button" class="btn btn-success btn-block" onclick="saveMerma();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Merma</button></div>-->
                                    <div id="sded" style="display:none;"><i class="fa fa-refresh fa-spin fa-3x"></i></div>

                                </div>
                            </div>
                        </div>    
                       <!-- <div class="row">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2">
                                <button class="btn btn-default btn-block" onclick="agregaMerma();">Agregar</button>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div> 
                <!--- Modal Caracteristicass -->
    <div id="modalCarac" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content panel-default">
                <div class="modal-header panel-heading">
                    <h4 id="modal-labelCr"></h4>
                    <input type="hidden" id="carIdProddiv">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <img id="divImagenPro" height="250" width="250">
                        </div>
                        <div class="col-sm-6" id="prodCarcDiv"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="agregaCarac();">Agregar</button> 
                    <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div> 
    </div>


    <!--- Modal Lote -->
    <div id="modalLote" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content panel-default">
                <div class="modal-header panel-heading">
                    <h4 id="modal-labelLt"></h4>
                    <input type="hidden" id="lotIdProddiv">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <img id="divImagenProLot" height="250" width="250">
                        </div>
                        <div class="col-sm-6" id="prodLoteDiv"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="agregaLote();">Agregar</button> 
                    <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div> 
    </div>


    <div id="modaltipo" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content panel-default">
                <div class="modal-header panel-heading">
                    <h4 id="modal-labelCr">Agregar tipo de merma</h4>                    
                </div>
                <div class="modal-body col-md-12">
                    <label class="col-md-6"> Tipo de merma: </label>
                    <input class="col-md-6" type="text" id="tipoMerma">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="guardartipo();">Agregar</button> 
                    <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div> 
    </div>

    <div id="modalimage" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content panel-default">
                <div class="modal-header panel-heading">
                    <h4 id="modal-labelCr">Adjuntar imagen</h4>                    
                </div>
                <div class="modal-body col-md-12">

                    <div class="row"">                        
                        <form id="myForm"  method="post" enctype="multipart/form-data">
                        <input type="hidden" id="i">
                        <input type="hidden" id="productoi">
                            <div class="row">
                            <img id="imagenMerma" width="250px" height="250px" src="noimage.jpeg">
                            <div class="col-sm-6">                             
                                <div style="padding-left:10%">
                                  <input type="file" size="40" name="myfile" filename="2.jpg">
                                </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-sm-6">
                              <div style="padding-left:10%">
                                <button type="submit" class="btn btn-primary btnMenu" id="btnimagen">Agregar imagen</button> 
                              </div>
                            </div>
                            </div>
                        </form>             
                    </div>
                    <div class="row">
                      <div class="col-sm-12" style="padding-left:0%">
                          <blockquote>
                            <p>Para mejor visualización, se recomienda utilizar imágenes cuadradas.</p>
                          </blockquote>
                      </div>
                    </div>

                </div>
                <div class="modal-footer">                    
                    <button type="button" class="btn btn-danger"  data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div> 
    </div>

    </div>
</body>
</html>

<script>    
    $("#myForm").on("submit", function(e){
            e.preventDefault();
            var f = $(this);
            var formData = new FormData(document.getElementById("myForm"));
            formData.append("dato", "valor");
            var i = $("#i").val();
            var productoi = $("#productoi").val();
            var idnewmerma = $("#idnewmerma").val();

            //alert(i+'_para subir image_'+productoi+' merma '+idnewmerma);
            //return 0;
            //formData.append(f.attr("name"), $(this)[0].files[0]);
            //formData.attr("filename",idnewmerma'_'+i+'_'+productoi);
            $.ajax({
                url: "ajax.php?c=inventario&f=uploadfile",
                type: "post",
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
       processData: false
            })
                .done(function(res){
                  console.log(res);
                  $('#imagenMerma').attr("src",res.direccion);                  
                  $('#imagenDir_'+idnewmerma+'_'+i+'_'+productoi).val(res.direccion);
                    
                });
        }); 
    // validacion
    $(function() {
        $('#divCantidad').on('keydown', '#cantidad', function(e){-1!==$.inArray(e.keyCode,[46,8,9,27,13,110,190])||(/65|67|86|88/.test(e.keyCode)&&(e.ctrlKey===true||e.metaKey===true))&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()});
        
    })

    function validaExis(){
        /// para lotes
        var lote = $("#producto option:selected").attr('lote');
        if(lote == 1){
            var exis = $('#hiddenExis_input').val();
            var cant = $('#cantidad').val();
            if (cant > exis){
                alert('No cuneta con suficiente inventario');
                $('#cantidad').val(exis);
            } 
        }        
    }

    $('#cantidad').focusout(function() {
        var cantidad = $("#cantidad").val();
        var costo = $("#costo").val();
        var factor = $("#factor").val();
        costo = costo * factor;
        $("#costoT").val(cantidad * costo);
  });

    $("#proveedor").change(function() {        
            $("#producto").html('<option value="0">Seleccione</option>');
            $("#producto").select2();
            idProveedor=$(this).val()            
            if(idProveedor>0){
                $.ajax({
                    url:"ajax.php?c=inventario&f=a_getProvProducto",
                    type: 'POST',
                    dataType: 'JSON',
                    data:{idProveedor:idProveedor},
                    success: function(r){
                        console.log(r);
                        if(r.success==1){
                            llenado='';
                            llenado='<option value="0">Seleccione un producto</option>';
                            $.each( r.datos, function(k,v) {
                                llenado+='<option value="'+v.id+'" lote="'+v.lote+'">'+v.descripcion_corta+'</option>';
                            });
                            $("#producto").html('');
                            $("#producto").append(llenado);
                            $("#producto").select2();
                            $("#producto").prop('disabled',false);
                        }else{
                            $("#producto").prop('disabled',true);
                            alert('El proveedor seleccionado no tiene productos');
                        }
                                                                        
                    }
                });
            }else{
                $("#producto").html('<option value="0">Seleccione</option>');
                $("#producto").select2();
            }
        });


</script>