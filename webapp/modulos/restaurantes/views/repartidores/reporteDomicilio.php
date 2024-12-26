<style>
    tfoot, thead {
  background-color: #d3d3d3;
  color: #000000;
  font-size: 100%;
  font-weight: bold;
}
</style>
<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Domicilio</title>
    <link rel="stylesheet" href="">
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>    
<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
    <!-- Datepicker -->
    <link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/datepicker/js/bootstrap-datepicker.min.js"></script>

    <!--Data Tables -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RC -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>

    <!--Button Print css -->
    <link rel="stylesheet" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
    <!--Button Print js -->
    <script src="../../libraries/dataTable/js/dataTables.buttons.min.js"></script>
    <script src="../../libraries/dataTable/js/buttons.print.min.js"></script>
   
<body> 
<br> 
<div class="container well" >
    <div class="row">
        <div class="col-xs-12 col-md-12">
           <h3>Servicio a Domicilio</h3>
        </div>
    </div>
    <div class="row col-md-12">                     
        <div class="panel panel-default" id="divfiltro">
            <div class="panel-heading">
                
                <div class="row">
                    <div class="col-sm-4">  
                        <div class="col-sm-12">     
                            <label>&nbsp</label>                   
                            <div id="datetimepicker1" class="input-group date">                                
                                <span class="input-group-addon">                                 
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    <label>Inicio:</label>
                                </span>
                                <input id="desde" class="form-control" placeholder="Fecha de Entrega" type="text">
                            </div>
                        </div>

                        <div class="col-sm-12"> 
                            <label>&nbsp</label>                       
                            <div id="datetimepicker1" class="input-group date">
                                <span class="input-group-addon">                                 
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    <label>Fin:&nbsp&nbsp&nbsp&nbsp</label>
                                </span>
                                <input id="hasta" class="form-control" placeholder="Fecha de Entrega" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-8">
                        
                        <div class="col-sm-4">
                            <label>Empleado</label>
                            <select class="form-control" id="empleado">
                                <option value="0">Todos</option>
                                <?php   
                                    foreach ($empleados as $k => $v) {
                                        echo '<option value="'.$v['id'].'">'.$v['usuario'].'</option>';
                                    }                                    
                                 ?>
                            </select>
                        </div> 
                        <div class="col-sm-4">
                            <label>Via de contacto:</label>
                            <select class="form-control" id="viacontacto">
                                <option value="0">Todas</option>
                                <?php   
                                    foreach ($viacontacto as $k => $v) {
                                        echo '<option value="'.$v['id'].'">'.$v['nombre'].'</option>';
                                    }                                    
                                 ?>
                            </select>
                        </div> 
                        <div class="col-sm-4">
                            <label>Metodo de pago:</label>
                            <select class="form-control" id="metodopago">
                                <option value="0">Todos</option>
                                <?php   
                                    foreach ($metodopago as $k => $v) {
                                        echo '<option value="'.$v['idFormapago'].'">'.$v['nombre'].'</option>';
                                    }                                    
                                 ?>
                            </select>
                        </div> 

                        <div class="col-sm-4">&nbsp</div> 
                        <div class="col-sm-4">
                            <label>Sucursal:</label>
                            <select class="form-control" id="sucursal">
                                <option value="0">Todas</option>
                                <?php   
                                    foreach ($sucursales as $k => $v) {
                                        echo '<option value="'.$v['idSuc'].'">'.$v['nombre'].'</option>';
                                    }                                    
                                 ?>
                            </select>
                        </div> 
                        <label>&nbsp</label>
                        <div class="col-sm-4">
                            <button class="btn btn-default" onclick="table(
                                                                            $('#desde').val(),
                                                                            $('#hasta').val(),
                                                                            $('#empleado').val(),
                                                                            $('#viacontacto').val(),
                                                                            $('#metodopago').val(),
                                                                            $('#sucursal').val());">Procesar</button>
                        </div>                         
                
                    </div>                     
                </div>
                <br><br><br>
                <div id="divT">
                    
                </div>


            </div>    
        </div>
    </div>

    <script>

        function table(desde,hasta,empleado,viacontacto,metodopago,sucursal){            
            $.ajax({ /// TODOS LOS MOVIMIENTOS ENTRE EL RANGO DE FECHAS 'movs'
                    url: 'ajax.php?c=repartidores&f=tablaDomicilio',
                    type: 'post',
                    dataType: 'html',
                    data:{desde:desde,hasta:hasta,empleado:empleado,viacontacto:viacontacto,metodopago:metodopago,sucursal:sucursal}                
            })
            .done(function(data) {
                $('#divT').html('');
                $('#divT').append(data);
                $("#repartidores").DataTable({
                    "order": [[ 1, "desc" ]],
                language : {
                    search : "Buscar <i class=\"fa fa-search\"></i>",
                    lengthMenu : "Muestra _MENU_ Por pagina",
                    zeroRecords : "No hay datos.",
                    infoEmpty : "No hay datos que mostrar.",
                    info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                    infoFiltered : " -> <strong> _TOTAL_ </strong> resultados encontrados",
                    paginate : {
                        first : "Primero",
                        previous : "<<",
                        next : ">>",
                        last : "Último"
                    }
                }
            });


            });
        } 
        $(function() { 

            function hoy(){
                var hoy = new Date();
                var dd = hoy.getDate();
                var mm = hoy.getMonth()+1; //hoy es 0!
                var yyyy = hoy.getFullYear();

                if(dd<10) {
                    dd='0'+dd
                } 
                if(mm<10) {
                    mm='0'+mm
                } 
                return hoy = yyyy+'-'+mm+'-'+dd;
            }
            function mesA(){
                var fecha=new Date();
                var mesA=new Date(fecha.getTime() - (24*60*60*1000)*30);
                var dd = mesA.getDate();
                var mm = mesA.getMonth()+1;
                var yyyy = mesA.getFullYear();

                    if(dd<10) {
                        dd='0'+dd
                    } 
                    if(mm<10) {
                        mm='0'+mm
                    } 
                     return mesA = yyyy+'-'+mm+'-'+dd;
            }           

            var desde = mesA();
            var hasta = hoy();
            $('#desde').val(desde);
            $('#hasta').val(hasta);

            $("#empleado, #viacontacto, #metodopago, #sucursal").select2();
            $('#desde, #hasta').datepicker({ 
                format: "yyyy-mm-dd",
                "autoclose": true, 
                language: "es"
            }).attr('readonly','readonly'); 
            // 0 es para todo
            table(desde,hasta,0,0,0,0);

        });

        



    </script>
    
