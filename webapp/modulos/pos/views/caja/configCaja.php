<!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Configuración de caja</title>

        <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">

        <script src="../../libraries/jquery.min.js"></script>
        <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>

    </head>
    <body>
        <br>
        <div class="container well">
            <div class="row">

                <div class="col-sm-1">
                    <div id="btnSave">
                      <button type="submit" class="btn btn-primary" id="save"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title">Configuración de caja</h3>
                </div>


                <div class="panel-body">
                    <!-- div de los Tabs -->
                    <div id="tabsProduct">  
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#basicos">Configuración</a></li>
                        </ul>
                    </div>

                    <!-- Div contendro de los Contenidos -->
                    <div class="tab-content" style="height:400px;">
                        <div id="basicos" class="tab-pane fade in active">
                            <div class="form-horizontal col-sm-12">
                                <div class="form-group">


                                    <div class="row"> 
                                        <div class="col-sm-12">
                                            <label for="tipoDescuento">Tipo de descuento</label>
                                            <select id="tipoDescuento" class="form-control" >
                                                <option value="1" > Global </option>
                                                <option value="2" > Por producto (PP) </option>
                                                <option value="3" selected > Ambos </option>
                                            </select>
                                        </div>
                                    </div>


                                    <div id="dinamico" class="row">

                                        <div id="limit-global" >
                                            <div class="col-sm-12">
                                                <label >Límite de descuento global sin contraseña</label>
                                            </div>
                                            <div class="col-sm-3" style="display: none;">
                                                <div class="input-group">
                                                    <span class="input-group-addon">$</span>
                                                    <input type="number" class="cantidad form-control text-right" min="0" value="0.00">
                                                </div>
                                            </div>
                                            
                                             <div class="col-sm-3">
                                                <div class="input-group">
                                                    <input type="number" class="porcentaje form-control text-right" min="0" max="100"  value="0.00" >
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="limit-unit" >
                                            <div class="col-sm-12">
                                                <label >Límite de descuento por producto sin contraseña</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon">$</span>
                                                    <input type="number" class="cantidad form-control text-right" min="0" value="0.00">
                                                </div>
                                            </div>
                                            <div class="col-sm-1"></div>
                                             <div class="col-sm-3">
                                                <div class="input-group">
                                                    <input type="number" class="porcentaje form-control text-right" min="0" max="100"  value="0.00" >
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div> 


                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="password">Contraseña</label>
                                            <input type="password" id="password" class="form-control" placeholder="* * * * *">
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="confirm-password">Confirmar contraseña</label>
                                            <input type="password" id="confirm-password" class="form-control" placeholder="* * * * *">
                                        </div>
                                    </div> 



                                    <div class="row" style="display:none;">
                                        <div class="col-sm-3">
                                            <label for="caja-max" class="control_label">Caja Max.</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="number" id="caja-max" class="form-control text-right" min="0" value="0.00"> 
                                            </div>
                                        </div>
                                        <div class="col-sm-1"></div>
                                        <div class="col-sm-3">
                                            <label for="retiro-max" class="control-label">Max. retiro</label>
                                            <div class="input-group">
                                                <span class="input-group-addon">$</span>
                                                <input type="number" id="retiro-max" class="form-control text-right" min="0" value="0.00"> 
                                            </div>

                                            
                                        </div>
                                    </div> 


                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="ticket">Leyenda del ticket</label>
                                            <textarea id="ticket" class="form-control" rows="5" placeholder="Ej. Gracias por su compra =)"></textarea>
                                        </div>
                                    </div>


                                    <div class="row">

                                        <div class="col-sm-6" style="display:none;">
                                            <div class="checkbox">
                                                <label><input type="checkbox" id="cotizacion-descuento" > Cotización descuento </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="checkbox">
                                                <label><input type="checkbox" id="orden-venta-descuento" > Orden de venta descuento </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        
        <div  style="height:100px;" ></div>
        <script src="js/configCaja.js"></script> 
    </body>
</html>    