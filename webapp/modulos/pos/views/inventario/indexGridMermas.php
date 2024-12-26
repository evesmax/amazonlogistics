<?php 
//ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Ordenes de Compra</title>
    <link rel="stylesheet" href="">
</head>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="js/inventario.js"></script>
<!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <!-- Modificaciones RC -->
    <!-- <script src="../../libraries/dataTable/js/datatables.min.js"></script> -->
    <link rel="stylesheet" type="text/css" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>

    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
   <script>
   $(document).ready(function() {
    $('#tableGrid').DataTable({
                        order: [[ 0, "desc" ]],
                        dom: 'Bfrtip',
                        buttons: ['excel'],
                        language: {
                        search: "Buscar:",
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

                     }
    });
   });
   </script>
<body>  
<div class="container well">
        <div class="row">
            <!--
            <div class="col-sm-2">
                <button class="btn btn-default btn-block" onclick="cambia2();">Listado</button>
            </div>
            -->
           
        </div>
    <div class="row">
        <div class="col-sm-1">
             <h3 style="padding-left: 16px;">Mermas</h3>
        </div>
        <div  class="col-sm-2" style="padding-left: 36px; padding-top: 16px;">
            <button class="btn btn-primary btn-block" onclick="cambia1();"> <i class="glyphicon glyphicon-plus"></i> Nueva Merma</button>
        </div>


        <div class="col-sm-12" style="overflow:auto;">
                     <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGrid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Costo</th>
                        <th>Total Costo</th>  
                        <th>Precio</th>                                                                      
                      </tr>
                    </thead>
                    <tbody>
                        <?php 
                        //echo json_encode($mermasList);
                        $status="";
                        foreach ($mermasList as $key => $value) {

                            /*
                            switch ($value['tipo']) {
                                case 0:
                                $tipo = 'Caducidad';
                                break;
                                case 1:
                                $tipo = 'Robo';
                                break;
                                
                                default:
                                    $tipo = 'Otro';
                                    break;
                            }
                            */



                            if($value['activo']==1){
                                $status = '<span class="label label-success">Activo</span>';
                            }else{
                                $status = '<span class="label label-danger">Inactivo</span>';
                            }
                            echo '<tr>';
                            echo '<td>'.$value['id'].'</td>';
                            echo '<td>'.$value['fecha'].'</td>';
                            echo '<td>'.$value['tipo_merma'].'</td>';
                            echo '<td>'.$value['usuario'].'</td>';
                            echo '<td><a onclick="mermaDetalle('.$value['iddetalle'].');">'.$value['nombre'].'</a></td>';
                            echo '<td align="center">'.$value['cantidad'].'</td>';
                            echo '<td>$'.number_format($value['costo'],2).'</td>';                            
                            echo '<td>$'.number_format($value['costoTotal'],2).'</td>';
                            echo '<td>$'.number_format($value['importe'],2).'</td>';
                            //echo '<td>';
                            //echo '<a href="index.php?c=compra&f=ordenCompra&idorden='.$value['id'].'" class="btn btn-primary btn-xs active"><span class="glyphicon glyphicon-edit"></span> Editar</a> ';
                            //echo '<a href="index.php?c=compra&f=recepcionOrden&idorden='.$value['id'].'" class="btn btn-success btn-xs active"><span class="glyphicon glyphicon-edit"></span> Recibir</a> ';
                            //echo '<a href="index.php?c=compra&f=ordenCompra&idorden=100000" class="btn btn-danger btn-xs active"><span class="glyphicon glyphicon-remove"></span> Borrar</a>';
                            //echo '</td>';

                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
        </div>        
    </div>



    <!--- Modal detalle -->
    <div id="modalDetalle" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content panel-default">
                <div class="modal-header panel-heading">
                    <h4 id="modalIdMerma"></h4>
                   <!-- <input type="hidden" id="carIdProddiv"> -->
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" id="divDetalle"> 

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10"></div>
                        <!--
                        <div class="col-sm-2">
                            Total:
                            <label id="totalMerma"></label>
                        </div>
                        -->
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