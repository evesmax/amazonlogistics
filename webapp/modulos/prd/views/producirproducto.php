<!-- AM -->
<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <title>Producir producto</title>
   <script type="text/javascript" src="../../libraries/jquery.min.js"></script>
   <script type="text/javascript" src="../../libraries/bootstrap-3.3.7/js/bootstrap.js"></script>
   <script type="text/javascript" src='js/producirproducto.js'></script>
   <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
   <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
   <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">

   <!-- Datatable -->
   <script src="../../libraries/dataTable/js/datatables.min.js"></script>
   <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

   <!--Select 2 -->
   <link rel="stylesheet" href="../../libraries/bootstrap-select-1.9.3/dist/css/bootstrap-select.min.css"> 
   <script src="../../libraries/bootstrap-select-1.9.3/dist/js/bootstrap-select.min.js"></script>
   <script src="../../libraries/select2/dist/js/select2.min.js"></script>
   <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

   <style type="text/css">
   input[type=checkbox] {
      width: 20px;
      height: 20px;
   }
</style>
</head>

<body>
<div class="container well" style="width: 96%;"> <!--container well--> 
<form>
<div class="form-row" style="padding-bottom: 100px;padding-top: 40px;">
   <div class="col-md-3 mb-3">
      <label for="ciclo">Ciclo</label>
      <select class="form-control" id="ciclo" name="ciclo">
         <option value="">Seleccione</option>
         <option value="0">Ningun Ciclo</option>
         <?php 
         while ($e = $ciclo->fetch_object()){
            echo '<option value="'. $e->id_tipociclo .'" '. $b .'>'. $e->descripcion .'  </option>'; }?>
         </select>        
      </div>
      <div class="col-md-3 mb-3">
         <label for="departamento">Departamento</label>
         <select class="form-control" id="departamento" name="departamento">
            <option value="">Seleccione</option>
            <?php 
            while ($e = $departamento->fetch_object()){
               echo '<option value="'. $e->id .'" '. $b .'>'. $e->nombre .'  </option>'; }?>
            </select> 
         </div>
         <div class="col-md-3 mb-3">
            <label for="familia">Familia</label>
            <select class="form-control" name="familia" id="familia">
               <option value="">Seleccione</option>
               <?php 
               while ($e = $familia->fetch_object()){
                  echo '<option value="'. $e->id .'" '. $b .'>'. $e->nombre .'  </option>'; }?>
               </select>
            </div>   
            <div class="col-md-3 mb-3">
               <label for="linea">Linea</label>
               <select class="form-control" name="linea" id="linea">
                  <option value="">Seleccione</option>
                  <?php 
                  while ($e = $linea->fetch_object()){
                     echo '<option value="'. $e->id .'" '. $b .'>'. $e->nombre .'  </option>'; }?>
                  </select>
               </div>
            </div>

            <div class="panel panel-default table-responsive">
               <div class="panel-heading" style="text-align: right;">
                  <button  id="filtrar" class="btn btn-primary" data-loading-text="<i class='fa fa-refresh fa-spin '></i>" type="button">
                     <span class="glyphicon glyphicon-search"></span> Filtrar    
                  </button>
               </div>
               <div class="panel-body table-responsive">           
                  <!-- <div> -->
                     <table cellpadding='0' class='tableproducirproducto table  display table-striped table-bordered nowrap table-responsive' width="100%">
                        <thead> 
                           <tr style='background-color:#B4BFC1;color:#000000;'>
                              <th>Producto</th>
                              <th>Ciclo de producción</th>
                              <th>Acciones</th>
                              <th><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                     <!-- </div> -->
                  </div>
                  <div class="panel-footer" style="text-align: right;"> 
                     <button id="masivo" type="button" class="btn btn-success btn-md">Asiganción Masiva</button>
                  </div>
               </div>
            </form>
         </div><!--container well-->

<div class="container">
   <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
               <h4 class="modal-title ciclounico" style="text-align: center;background-color:#B4BFC1;color:#000000;padding:10px;" hidden>
               Asignación de ciclos a producto</h4>
               <h4 class="modal-title ciclomasivo" style="text-align: center;background-color:#B4BFC1;color:#000000;padding:10px;" hidden>
               Asignación de ciclos (Masivo)</h4>
            </div>
            <div class="modal-body" style="height: 200px;"> 
               <input type="hidden" name="" id='productname'>
               <input type="text" name="" id="nombreproductoeditar" readonly style="font-size: 15px;border: 0px;font-weight: bold;">
               <div class="form-inline" style="padding-top:15px">
                  <label for="selectciclo">Ciclo</label>
                  <select class="form-control" name="selectciclo" id="selectciclo">
                     <option value="">Seleccione</option>
                     <option value="0">Ningun Ciclo</option>
                     <?php 
                     while ($e = $cicloseleccion->fetch_object()){
                        echo '<option value="'. $e->id_tipociclo .'" '. $b .'>'. $e->descripcion .'  </option>'; }?>
                     </select>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-ban" aria-hidden="true"></i> Cerrar</button> 
                  <button type="button" class="btn btn-primary" id="saveIndiv" value="0"><i class="fa fa-cloud" aria-hidden="true"></i> Guardar</button>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>
</html>



