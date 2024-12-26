<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Relación usuario - productos</title>
    <link rel="stylesheet" href="">
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- <script src="js/prodsuc.js"></script> -->
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

   <!-- <script>
   $(document).ready(function() {
        //$('#tableSales').DataTable()
        //graficar('','','','','');
        $('#table1').DataTable({
                            dom: 'Bfrtip',
                            buttons: [ 'excel' ],
                            language: {
                                search: "Buscar",
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


        $('#cliente').select2();

        $('#desde').datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });
        $('#hasta').datepicker({
            format: "yyyy-mm-dd",
            language: "es"
        });

      $('#generica').change(function() {
        if($(this).is(":checked")) {
            $('#satCl67').val('01010101');
        $('#divisionSat').empty();
        $('#divisionSat').append('<option value="0">-Selecciona-</option>');

        $('#grupoSat').empty();
        $('#grupoSat').append('<option value="0">-Selecciona-</option>');

        $('#claseSat').empty();
        $('#claseSat').append('<option value="0">-Selecciona-</option>');

        $('#claveSat').empty();
        $('#claveSat').append('<option value="0">-Selecciona-</option>');
        $('#divisionSat').select2({width:'100%'});
        $('#grupoSat').select2({width:'100%'});
        $('#claseSat').select2({width:'100%'});
        $('#claveSat').select2({width:'100%'});
        }else{
            $('#satCl67').val('');
        }
    });
         
   });
   </script> -->
<body>  
<div class="container well">
    <div class="row">
        <div class="col-xs-12 col-md-12">
           <h3>Relación usuario - productos</h3>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="form-group">  
                    
                    <div class="row">
                        <div class="col-sm-2">
                            <select class="erre form-control " id="selectDepartamento" >

                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select class="erre form-control" id="selectFamilia" >

                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select class="erre form-control" id="selectLinea" >

                            </select>
                        </div>
                        <div class="col-sm-1">
                            <i class="fa fa-undo fa-2x" aria-hidden="true" onclick="resetFilters();" style="color: white; background-color: rgba(0, 0, 0, 0.1); border-radius: 100%; margin: 4px;"></i>
                        </div>

                        <div class="col-sm-3" style="color:#ff0000;">
                            <select id="c_solicitante" class="form-control" placeholder="Empleado">
                                <?php foreach ($empleados as $k => $v) { ?>
                                    <option area="<?php echo $v['nomarea']; ?>" value="<?php echo $v['idempleado']; ?>"><?php echo $v['nombre']; ?> (<?php echo $v['nomarea']; ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary" onclick="vinculaClave();">Vincular</button>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th><button class="btn btn-prymary" onclick="sellAll();">Todos</button></th>
                                    <th>ID</th>
                                    <th>Codigo</th>
                                    <th>Producto</th>
                                    <th>Departamento</th>
                                    <th>Familia</th>
                                    <th>Linea</th>
                                    <th>Empleado</th>
                                </tr>
                            </thead>
                            <tbody id="table1body">
                                
                            </tbody>
                        </table>
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
<script>
    $('#table1').DataTable({
                            dom: 'Bfrtip',
                            buttons: [ 'excel' ],
                            language: {
                                search: "Buscar",
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
    $('#c_solicitante').select2({placeholder: "Empleado", width: '100%'});
    $("#selectDepartamento").select2({
        placeholder: "Departamento",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=caja&f=buscarClasificadores',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                return { clasificador : 1,
                    patron: params.term };
            },

            processResults: function (data) {
                $("#selectDepartamento").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    })
    .on("change", function(e) {
        $("#selectFamilia").empty().trigger('change');
    });
    $("#selectFamilia").select2({
        placeholder: "Familia",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=caja&f=buscarClasificadores',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                return { clasificador : 2,
                    departamento : $('#selectDepartamento').val(),
                    patron: params.term };
            },

            processResults: function (data) {
                $("#selectFamilia").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    })
    .on("change", function(e) {
        $("#selectLinea").empty().trigger('change');
    });
    $("#selectLinea").select2({
        placeholder: "Linea",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=caja&f=buscarClasificadores',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                return { clasificador : 3,
                    familia : $('#selectFamilia').val(),
                    patron: params.term };
            },

            processResults: function (data) {
                $("#selectLinea").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    })
    .on("change", function(e) {
        cargarMas();
    });
    function resetFilters(){
        $("#selectDepartamento").empty().trigger('change');
    }

    function cargarMas(){
        var departamento = $('#selectDepartamento').val(),
            familia = $('#selectFamilia').val(),
            linea = $('#selectLinea').val();

        $.ajax({
            url: 'ajax.php?c=producto&f=cargarProductos',
            type: 'post',
            dataType: 'json',
            data: { departamento: departamento,
                    familia : familia,
                    linea : linea
                }
        })
        .done(function(resp) {
            console.log(resp);
            var table = $('#table1').DataTable();
            table.clear().draw();

            var x = '';
            $.each(resp, function(index, val) {
                if (val.tipo_producto!=3) {
                    x = `
                        <tr>
                            <td><input type="checkbox" class="checkPro" value="${val.ID}" /></td>
                            <td>${val.ID}</td>
                            <td>${val.CODIGO}</td>
                            <td>${val.NOMBRE}</td>
                            <td>${val.DEPARTAMENTO ? val.DEPARTAMENTO : ''}</td>
                            <td>${val.FAMILIA ? val.FAMILIA : ''}</td>
                            <td>${val.LINEA ? val.LINEA : ''}</td>
                            <td>${val.EMPLEADO ? val.EMPLEADO : ''}</td>
                        </tr>
                    `;
                    table.row.add($(x)).draw(); 
                } 
            });
            $('#table1').dataTable();

        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    }

    function sellAll(){
        var oTable = $('#table1').dataTable();
        var allPages = oTable.fnGetNodes();

        if ($('.checkPro',allPages).is(":checked")) {
            $('.checkPro',allPages).prop('checked', false);
            //aaa();
        }else{
            $('.checkPro',allPages).prop('checked', true);
            //aaa();
        }
    }

    function vinculaClave(){
        var oTable = $('#table1').dataTable();
        var empleado = $('#c_solicitante').val();
        var allPages = oTable.fnGetNodes();
    
        productos=[];
        $('input:checked', allPages).each(function(){
            productos.push( $(this,allPages).val() );
        });
        if(productos == [] ){
            alert('No has seleccionado ningun producto.');
            return false;
        }

        $.ajax({
            url: 'ajax.php?c=producto&f=vinculacionMasivaEmpleadoProducto',
            type: 'POST',
            dataType: 'json',
            data: {productos: productos, empleado : empleado},
        })
        .done(function(res) {
            console.log(res);
            if(res.estatus==true){
                alert('Se vincularon correctamente.');
                window.location.reload();
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }

</script>
</html>