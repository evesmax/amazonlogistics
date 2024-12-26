

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontawesome5.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/datatables.min.css">
    <link rel="stylesheet" href="css/datatables.bootstrap.css">
	  <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="css/alertify.min.css">
  	<link rel="stylesheet" type="text/css" href="css/default.min.css">




  </head>
  <body>
    <?php include_once("html/modales/modalAgregarPiloto.php"); ?>
    <div class="col-md-10 col-md-offset-1" style="margin-top:10px;">

                <div class="panel panel-warning panel-table">
                  <div class="panel-heading">
                    <div class="row">
                      <div class="col col-xs-6">
                        <h3 class="panel-title"><i class="fas fa-users"></i> Lista de pilotos</h3>
                      </div>
                      <div class="col col-xs-6 text-right">
                        <button type="button" class="btn btn-sm btn-primary btn-create" onclick="modalAgregarPiloto()">
                          <i class="fas fa-user-plus"></i> Agregar Piloto</button>
                      </div>
                    </div>
                  </div>
                  <div class="panel-body">
                    <table class="table table-striped table-bordered dt-responsive nowrap" id="dtPilotos">
                      <thead>
                        <tr>
                            <th class="centerTd"><em class="fa fa-cog"></em></th>
                            <th class="hidden-xs centerTd">ID</th>
                            <th class="centerTd">Nombre</th>
                            <th class="centerTd">Apellidos</th>
                            <th class="centerTd">Fecha Nacimiento</th>
                            <th class="centerTd">Numero de Certificado</th>
                            <th class="centerTd">Activo</th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($pilotos as $r): ?>
                            <tr id="rowtr<?php echo $r["idPiloto"]; ?>">
                                <th class="centerRow">
                                  <?php if($r["bActivo"] == 1){ ?>
                                    <a id="btn<?php echo $r["idPiloto"]; ?>" data-id="<?php echo $r["idPiloto"]; ?>" data-bactivo="<?php echo $r["bActivo"]; ?>" onclick="desactivar(this)" class="btn btn-danger"><em class="fas fa-trash-alt"></em></a>
                                  <?php }else{ ?>
                                    <a id="btn<?php echo $r["idPiloto"]; ?>" data-id="<?php echo $r["idPiloto"]; ?>" data-bactivo="<?php echo $r["bActivo"]; ?>" onclick="desactivar(this)" class="btn btn-success"><em class="fas fa-check-circle"></em></a>
                                  <?php }?>
                                    <a id="btnedit<?php echo $r["idPiloto"]; ?>" class="btn btn-warning" data-id="<?php echo $r["idPiloto"];?>" onclick="editar(this)"><em class="fas fa-user-edit"></em></a>
                                </th>
                                <th class="hidden-xs centerRow"><?php echo $r["idPiloto"]; ?></th>
                                <th class="centerRow"><?php echo $r["vNombre"]; ?></th>
                                <th class="centerRow"><?php echo $r["vApellidos"]; ?></th>
                                <th class="centerRow"><?php echo $r["dFechaNacimiento"]; ?></th>
                                <th class="centerRow"><?php echo $r["vNumeroCertificado"]; ?></th>
                                <th class="centerRow">
                                                    <?php
                                                      if($r["bActivo"] == 1){
                                                        echo "<input id=\"check".$r["idPiloto"]."\" type=\"checkbox\" checked disabled=\"true\"/>";
                                                      }else{
                                                        echo "<input id=\"check".$r["idPiloto"]."\" type=\"checkbox\" disabled=\"true\"/>";
                                                      }
                                                    ?>
                                </th>
                            </tr>
                          <?php endforeach; ?>
                      </tbody>
                    </table>

                  </div>

                </div>
    </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/datatables.min.js"></script>
  <script src="js/datatables.bootstrap.min.js" charset="utf-8"></script>
  <script src="js/funcionesModuloPiloto.js"></script>
  <script src="js/jquery-ui.min.js"></script>
  <script src="js/alertify.min.js" type="text/javascript"></script>
  <script>
  $(document).ready(function() {
      $('#dtPilotos').DataTable();
  } );

  </script>
</html>
