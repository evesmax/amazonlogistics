<?php
session_start();
$perfil = $_SESSION["accelog_idperfil"];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
    <link   rel="stylesheet" type="text/css" href="css/reporteentradas.css">
    <link rel="stylesheet" type="text/css"   href="css/registroentradas.css">  
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
    <script type="text/javascript" src="js/jquery-ui.multidatespicker.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.multidatespicker.css">
    <script type="text/javascript" src='js/reporte_entradas.js'></script>
    <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
    <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
    <script src="../../libraries/dataTable/js/datatables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
    <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
</head>
<body>
    <div id="exTab2" class="container well" style="width: 98%;border: 0;"> 
        <ul class="nav nav-tabs">
            <li class="active">
                <a  href="#1" data-toggle="tab" class="ocultos">Reporte de entradas y salidas</a>
            </li>
            <?php if($autori=='19'){?>
            <li ><a href="#segundo" data-toggle="tab" class="ocultos" id="2">Autorización de entradas</a>
            </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="1">
                <div class="container-fluid ocultos encabezado">
                    <br>
                    <b>Reporte de cambio de entradas y salidas Empleado</b>
                </div>
                <br>

                <form class="ocultos" method="post" action="index.php?c=Reportes&f=reporteEntradas" id="formentradas">
                    <!--  -->
                    <fieldset class="scheduler-border">
                        <legend align="center" class="scheduler-border">Búsqueda por periodo</legend>
                        <div class="form-inline">
<!--   <div class="col-md-7" style="text-align: right;">
<input type="checkbox"  name="checkboxG4" id="mostrarfechas" class="css-checkbox"  checked style="display: none;" />
<label for="mostrarfechas" class="css-label" onclick="activarChecked()" style="display: none;">Búsqueda por rangos de fechas.</label>
&ensp;&ensp;
<input type="checkbox" name="mostrarperiodos" id="mostrarperiodos" class="css-checkbox" style="display: none;" />
<label for="mostrarperiodos" onclick="activarCheckeddos()" class="css-label" style="margin-left: 20px;margin-right: 20px;display: none">Busqueda por periodo.</label>
</div> -->
<div class="col-md-12" style="text-align: center;">
    <button type="button" class="btn btn-primary btn-sm" id="load" style="text-align:center" data-loading-text="Consultando<i class='fa fa-refresh fa-spin '></i>">Generar Reporte</button>
    &ensp;
<!--     <a type="button" class="btn btn-sm" style="background-color:#d67166"  href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  title ="Generar reporte en PDF" border="0"> 
</a> 
&ensp; -->
<a type="button" id="impresion" class="btn btn-info btn btn-sm" href="javascript:window.print();" hidden="true" onclick="printl()">
    <img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0" ></a> 
    <?php if($registrahors =='21'){?>
    <a type="button" style='display:inline; right:.1em; position:relative;' class='fa fa-pencil-square-o fa-3x btn btn ' title="Registar horas a empleado" data-toggle='modal' data-target='#myModal' ></a>            
    <?php } ?>
</div>
</div>
<br>
<br>
<br>
<div class="form-inline" id="mostrarfecha" style="display: none;">
    <div class="col-md-4"> 
        <label>Fecha Inicio:</label>   
        <input type="text" id="fechainicio" name="fechainicio" class="selectpicker btn-sm form-control" 
        value="<?php echo @$_REQUEST['fechainicio'];?>" style="width: 70%;">
    </div>
    <div class="col-md-4">
        <label>Fecha Fin:</label>
        <input type="text" id="fechafin" name="fechafin" class="selectpicker btn-sm form-control" 
        value="<?php echo @$_REQUEST['fechafin'];?>" style="width: 70%;">
    </div>
    <div class="col-md-4">
        <label>Empleado:</label>
        <select id="nombreEmpleado"  class="selectpicker btn-sm form-control" data-live-search="true" name="empleados" data-width="60%">
            <option value="">Todos</option>
            <?php 
            while ($e = $empleados->fetch_object()){
                $b = "";
                if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
                echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.'  </option>'; }
                ?>
            </select>   
        </div>
    </div>
    <div class="form-inline" id="mostrarperiodo">
        <div class="col-md-4"> 
            <label>Tipo de periodo:</label>
            <select id="idtipop" class="form-control selectpicker btn-sm" data-live-search="true" name="idtipop" data-width="60%" title="Seleccione">
                <option value="*">Todos</option>
                <?php 
                while ($e = $tipoperiodo->fetch_object()){
                    $b = "";
                    if($e->idtipop == $idtipop){ $b='selected="selected"'; } 
                    echo '<option idtipop="'.$idtipop .'" value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
                }
                ?>
            </select>
        </div>



        <div class="col-md-4">   
            <label class="" for="nominas">Nomina:</label>
            <select id="idnomp" class="idnomp form-control selectpicker btn-sm" data-live-search="true" name="idnomp" data-width="60%">
                <input type="hidden" id="hdnIdNomp" value="<?php echo $nomina;?>" />        
            </select>

        </div> 
        <div class="col-md-4">
            <label>Empleado:</label>
            <select id="empleadosdos" class="form-control selectpicker btn-sm" data-live-search="true" name="empleadosdos" data-width="70%" title="Seleccione">
                <option value="*">Todos</option>

                <?php 
                while ($e = $empleadosdos->fetch_object()){
                    $b = "";
                    if($e->idEmpleado == $empleado){ $b='selected="selected"'; } 
                    echo '<option idEmpleado="'.$empleado .'" value="'.$e->idEmpleado .'" '. $b .'>'.$e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';
                }
                ?>

            </select>
        </div>
    </div>    
    <input type="hidden" name="txt1" id="fini"           value="<?php echo $fi; ?>" />
    <input type="hidden" name="txt2" id="ffini"          value="<?php echo $ff; ?>" />
    <input type="hidden" name=""     id="nomiActiv"      value="<?php echo $idnomp; ?>" >
    <input type="hidden" name=""     id="nombre"         value="<?php echo $nombre; ?>" >
    <input type="hidden"             id="periodoSelecc"  value="<?php echo $_POST['idtipop']; ?>">
    <input type="hidden" name=""     id="periodotipo"    value="<?php echo $periodotipo; ?>" >           
    <br/>
</fieldset>
</form>

<div id="imprimible"> 
    <?php
    $empleado =0;
    if($reporteEntradas && $reporteEntradas->num_rows>0) {
        while($in = $reporteEntradas->fetch_assoc()){
            if ($empleado != $in['idEmpleado']){
                if ($empleado != 0 ){?>
            </tbody>
        </table>
        <br>
        <br>
        <p class='muestra' hidden style='color: black;font-weight:normal;text-align: justify;font-size: 12px;'>Hago constar que la presente tarjeta ha sido marcada personalmente por mi a las horas de entradas y salidas, por lo tanto corresponde al record de mi asistencia.</p>
        <div class="col-md-12" style="text-align: center;" class='muestra'>
            <table border='0' style='color: black;'  hidden class='muestra'>
                <thead><tr><th>______________________________________</th></tr>
                    <br hidden='true' class='mostrar'>
                    <tr>
                        <td align='center' id='firma'>Firma del Empleado</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class='saltopagina' style='height:30px;'></div>
    <?php 
} ?> 
<div style='font-family: Courier;' align='center' hidden='true'>
    <b style='font-size:14px;'>Reporte de entradas y salidas de empleado</b></div>
    <div class='alert alert-info table-responsive' cellspacing='0' width='100%' style='overflow: auto;'>
        <table class='mostrartabla' style="color: black;"> 
            <tr>
                <td colspan='5'>
                    <?php   
                    $url = explode('/modulos',$_SERVER['REQUEST_URI']);
                    if($logo1 == 'logo.png') $logo1= 'x.png';
                    $logo1 = str_replace(' ', '%20', $logo1);  
                    echo "<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo1 style='width: 185px;height: 40px;padding-right:30px'>";
                    echo "<b>".$infoEmpresa['nombreorganizacion']." ".$infoEmpresa['RFC']."</b>"; ?>
                </td>
            </tr>
            <tr>
                <td colspan='3'><p style='font-size:16px;padding-top:5px;'><b>Tarjeta de checado por empleado</b></p></td>
            </tr>
            <tr>
                <td colspan='3'><b><?php echo $in['codigo'].' '.$in['nombreEmpleado'].' '.$in['apellidoPaterno'].' '.$in['apellidoMaterno'];?></b>
                </td>
            </tr>
            <tr>
                <td><b>Imss:</b><?php echo $in['nss'];?></td>
                <td style='width:210px;'><b>Curp:</b><?php echo $in['curp'];?></td>
                <td><b>RFC:</b><?php echo $in['rfc'];?></td>
            </tr>
            <tr>
                <td colspan='2'><b>Periodo</b><?php echo " ".$in['fechainicio']." "."al"." ".$in['fechafin'];?></td>
            </tr>
            <tr>
                <td style='padding-bottom:9px'><b>Total de días laborados:</b><?php echo $in['numerodias'];?>
                </td>
            </tr>
        </table>
        <?php  echo"<table id=\"tablaentradas_".$in['idEmpleado']."\" cellpadding='0' class='tablaentradas  table-striped table-bordered dt-responsive nowrap' width='100%'; style='border:solid .3px;font-size:12.5px;' border='1' bordercolor='#0000FF'>";?>
            <thead> 
                <tr style='background-color:#B4BFC1;color:#000000;height: 35px;' class="ancho">
                    <th>Empleado</th> 
                    <th>Fecha</th>
                    <th>Día</th>
                    <th>Hora Entrada</th>
                    <th>Inicio Comida</th>
                    <th>Fin Comida</th>
                    <th>Hora Salida</th>    
                    <th>Periodo</th>
                    <th>No.Nómina</th>
                    <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){?> 
                    <th class="basu" style='background-color:#B4BFC1;color:#000000;height: 35px;text-align: center;'></th>
                    <?php }else{?>
                    <th class="basu" style='background-color:#B4BFC1;color:#000000;height: 35px;text-align: center;width: 40px;'></th>
                    <?php   } ?>
                </tr>
            </thead>
            <tbody>
                <div class='container-fluid col-md-12 muestrafe' hidden>
                    <?php 
                    $url = explode('/modulos',$_SERVER['REQUEST_URI']);
                    if($logo1 == 'logo.png') $logo1= 'x.png';
                    $logo1 = str_replace(' ', '%20', $logo1); 
                    echo"<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo1 style='width: 120px;height: 25px;padding-right:30px;' >"; 
                    echo "<b style='font-size:13px;'>".$infoEmpresa['nombreorganizacion'].' '."</b>";
                    echo "<b style='font-size:13px;'>".$infoEmpresa['RFC']."</b>";
                    ?>
                    <br>
                    <br>
                    <?php  if ($_REQUEST['fechainicio'] !="" && $_REQUEST['fechafin']!="" ){
                        echo "<p style='font-size:12px;font-weight:bold'>Periodo del"." ";echo $_REQUEST['fechainicio']." al ".$_REQUEST['fechafin'];"</p>";
                    }
                    else{
                        echo"<p style='font-size:12px;font-weight:bold'>Periodo del"." ".$in['fechainicio']." "."al"." ".$in['fechafin']."</p>";
                    } ?>

                </div>

                <?php }?>
                <tr class="clase<?php echo $in['idregistro'];?>">
                    <td style="height: 35px;" class="tdtab"><?php echo $in['nombreEmpleado'].' '.$in['apellidoPaterno'].' '.$in['apellidoMaterno'];?></td>
                    <td style="height: 35px;" align="center"><?php echo $in['fecha'];?></td>
                    <td style="height: 35px;" align="center"><?php echo $in['dia'];?></td>
                    <td align="center" id="<?php echo $in['idregistro'];?>_1"  <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){ ?> onclick="editar('<?php echo $in['idregistro'];?>_1',  <?php  echo $in['idEmpleado'].','.$in['idtipop'].','.$in['idnomp'];?>)"; 
                        style="background-color:#eeeeee;height: 35px;"  title="Click para editar" <?php } ?> ><?php echo $in['horaentrada'];?>
                    </td> 
                    <td  align="center" id="<?php echo $in['idregistro'];?>_2"  <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){ ?> onclick="editar('<?php echo $in['idregistro'];?>_2',  <?php  echo $in['idEmpleado'].','.$in['idtipop'].','.$in['idnomp'];?>)"; 
                        style="background-color:#eeeeee;height: 35px;" title="Click para editar"<?php } ?> ><?php echo $in['iniciocomida'];?></td> 
                        <td align="center" id="<?php echo $in['idregistro'];?>_3" <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){ ?> onclick="editar('<?php  echo $in['idregistro'];?>_3',  <?php  echo $in['idEmpleado'].','.$in['idtipop'].','.$in['idnomp'];?>)"; 
                            style="background-color:#eeeeee;height: 35px;" title="Click para editar"<?php } ?> ><?php echo $in['fincomida'];?>
                        </td> 
                        <td align="center" id="<?php echo $in['idregistro'];?>_4" <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){ ?> onclick="editar('<?php  echo $in['idregistro'];?>_4', <?php  echo $in['idEmpleado'].','.$in['idtipop'].','.$in['idnomp'];?>)"; 
                            style="background-color:#eeeeee;height: 35px;" title="Click para editar" <?php } ?> ><?php echo $in['horasalida'];?>
                        </td> 
                        <td align="center" style="height: 35px;"><?php echo $in['nombre'];?></td>
                        <td align="center" style="height: 35px;"><?php echo $in['idnomp'];?></td>
                        <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){?> 
                        <td style="height: 35px;text-align: center;"  class="basu">
                            <?php if($in['fecha'] >= $fi && $in['fecha']<= $ff && $editar =='20'){?>
                            <a title="Eliminar dia" href="#" class="btn btn-danger btn-xs active" onclick="accionEliminaFecha('<?php echo $in['idregistro'];?>')"><span class="fa fa-trash-o fa-lg" id="101"></span>
                            </a>    
                            <?php } ?>
                        </td>
                        <?php }else{?>
                        <td style="height: 35px;" class="basu">
                        </td>
                        <?php  } ?> 
                    </tr>                  
                    <?php  
                    $empleado = $in['idEmpleado'];
                }?>
            </tbody>
        </table>
        <br>
        <p class='muestra' hidden style='color: black;font-weight:normal;text-align: justify;font-size: 12px;'>Hago constar que la presente tarjeta ha sido marcada personalmente por mi a las horas de entradas y salidas, por lo tanto corresponde al record de mi asistencia.</p>
        <div class="col-md-12" style="text-align: center;" class='muestra'>
            <table border='0' style='color: black;'  hidden class='muestra'>
                <thead><tr><th>______________________________________</th></tr>
                    <br hidden='true' class='mostrar'>
                    <tr>
                        <td align='center' id='firma'>Firma del Empleado</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<?php   }  //fin del while
else {  // else de if($reporteentradas && $reporteentradas->num_rows>0) 
    echo"<div class='alert alert-info' style='overflow-x: scroll;'>
    <table id=\"tablaentradas_".$in['idEmpleado']."\" cellpadding='0' class='tablaentradas table table-striped table-bordered dt-responsive nowrap'  width='100%'; style='border:solid .3px;font-size:12.5px;' border='1' bordercolor='#0000FF'>";?>
        <thead> 
            <tr style='background-color:#B4BFC1;color:#000000'>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Día</th>
                <th>Hora Entrada</th>
                <th>Inicio Comida</th>
                <th>Fin Comida</th>
                <th>Hora Salida</th>
                <th>Periodo</th>
                <th>No.Nómina</th>
            </tr>
        </thead>
    </table>
</div>
<?php 
}       //fin if($reporteentradas->num_rows>0) 
?>       
</div>
</div>

<div class="tab-pane" id="segundo">
</div>

</div>
</div>
</div>
</div>
</div>


<!--GENERA PDF*************************************************-->
<div id="divpanelpdf" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Generar PDF</h4>
            </div>
            <form id="formpdf" action="../cont/libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Escala (%):</label>
                            <select id="cmbescala" name="cmbescala" class="form-control">
                                <?php
                                for($i=100; $i > 0; $i--){
                                    echo '<option value='. $i .'>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Orientación:</label>
                            <select id="cmborientacion" name="cmborientacion" class="form-control">
                                <option value='P'>Vertical</option>
                                <option value='L'>Horizontal</option>
                            </select>
                        </div>
                    </div>
                    <textarea id="contenido" name="contenido" style="display:none"></textarea>
                    <input type='hidden' name='tipoDocu' value='hg'>
                    <input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
                    <input type='hidden' name='nombreDocu' value='Detalle Entradas'>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="submit" value="Crear PDF" autofocus class="btn btn-primary btnMenu">
                        </div>
                        <div class="col-md-6">
                            <input type="button" value="Cancelar" onclick="cancelar_pdf()" class="btn btn-danger btnMenu">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
    <div id="divmsg" style="
    opacity:0.8;
    position:relative;
    background-color:#000;
    color:white;
    padding: 20px;
    -webkit-border-radius: 20px;
    border-radius: 10px;
    left:-50%;
    top:-200px
    ">
    <center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...
    </center>
</div>
</div> 
<script>
    function cerrarloading(){
        $("#loading").fadeOut(0);
        var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
        $("#divmsg").html(divloading);
    }
</script>
<div class="modal fade" id="myModal" role="dialog" data-backdrop="static" >
    <div class="modal-dialog">
        <div class="modal-content" style="padding:10px;">
            <div class="modal-header " >
                <h4 class="modal-title" style="text-align: center;height:30px;margin-left:5px;margin-top: 3px;font-size: 16px;">
                    Registro Manual de horas.
                    <button type="button" class="close panelTitleTxt glyphicon glyphicon-remove" data-dismiss="modal" id="btnCerrar" aria-hidden="true" style="margin-top: 2.5px;margin-right:5px;";></button>
                </h4>
            </div> <br>
            <div class="modal-body">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert">

                    </button>
                    <i class="fa fa-info-circle fa-lg"></i>
                    INFORMACION.<br>
                    Recuerde que solo podra alimentar horarios para la nomina activa.
                </div>
                <div class="alert alert-info" ><br>
                    <h4 style="text-align: center">Periodo de <?php echo $fi; ?> al <?php echo $ff; ?></h4>
                    <div class="row ">
                        <div class="col-md-6">
                            Empleado
                            <select multiple="" id="empleadocal" name="empleadocal[]" class="selectpicker" data-width="100%" data-live-search="true">
                                <?php 
                                while($e = $empleadosactivosperiodo->fetch_object()){?>
                                <option value="<?php echo $e->idEmpleado;?>"><?php echo $e->apellidoPaterno. " ".$e->apellidoMaterno." ".$e->nombreEmpleado; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6"><br>
                            <div class='input-group date'>
                                <input type="text"   name="diascheck" id="diascheck" value="" class="form-control input-md" />
                                <span class="input-group-addon" >
                                    <span> <input type="hidden" name="calendario[]" id="calendario"  class="input-append date cale"  /></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive "><br>
                        <table class="table table-sm table table-fixed table-hover table-bordered" >
                            <thead>
                                <tr style="background-color: rgb(110,110,110);color: #FFFFFF;">
                                    <th >Entrada</th>
                                    <th>Inicio Comida</th>
                                    <th>Fin Comida</th>
                                    <th>Salida</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="time" id="entrada"  max="23:30:00"  step="1"/></td>
                                    <td><input type="time" id="comidaini" max="23:30:00" step="1"/></td>
                                    <td><input type="time" id="comidafin" max="23:30:00" step="1"/></td>
                                    <td><input type="time" id="salida" max="23:30:00"  step="1"/></td>
                                </tr>
                            </tbody>
                        </table><br>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="almahrs" style="text-align:center" data-loading-text="<i class='fa fa-refresh fa-spin '</i>">Registrar horario</button>

                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){



            $("#calendario").multiDatesPicker({ 
                dateFormat: 'yy-mm-dd',
                showOn: "button",
                buttonImage: "images/cal.png",
                buttonImageOnly: true,
                defaultDate: null,
                onClose: function(selected,evnt) {  
                    fechas = $(this).val().split(',');
                    $("#diascheck").val($(this).val());  
                }
            });
            $("#calendario").datepicker("option","minDate", '<?php echo $fi; ?>');
            $("#calendario").datepicker("option","maxDate", '<?php echo $ff; ?>');

            $(function() {

                $("#btnCerrar").on('click', function(){ 
                    $("#diascheck").val("");
                    $("#entrada,#comidaini,#comidafin,#salida").val("");
                    $("#empleadocal option").attr("selected",false); 
                    $("#empleadocal").selectpicker("refresh");
                    $("#calendario").multiDatesPicker('resetDates');
                });
                $('#almahrs').on('click', function() {
                    var btnhrs = $(this);
                    btnhrs.button('loading'); 
//var fechas = $('#calendario').val().split(",")
if(!$('#empleadocal').val() || !$('#calendario').val() ){
    alert("Debe seleccionar empleado y fecha")  ;
    btnhrs.button('reset'); 
}else{
    $.post("ajax.php?c=reportes&f=almacenaHorario",{
        idempleado:$('#empleadocal').val(),
        fechas: $('#calendario').val(),
        entrada:$('#entrada').val(),
        comidaini:$('#comidaini').val(),
        comidafin:$('#comidafin').val(),
        salida:$('#salida').val(),
        idnomp :<?php echo $nominaActual->idnomp;?>

    },function (resp){
        if(resp == 1){
            alert("Registro satisfactorio :)");
            $(".close").click();
        }else{
            alert("Ocurrió un error, intente de nuevo :(");
        }
        btnhrs.button('reset'); 

        $.ajax({
            url:"ajax.php?c=reportes&f=CargarAutorizacionEntradas",
            type: 'POST',
            data:{},
            success: function(resp){
                $("#segundo").html(resp);
            } 
        });
    }); 
}
});
            });
            $.ajax({
                url:"ajax.php?c=reportes&f=CargarAutorizacionEntradas",
                type: 'POST',
                data:{  
                    idnomp     : $('#nomiActiv').val(),
                    nombre     : $('#nombre').val(),
                    periodotipo: $('#periodotipo').val(),
                    fini       :$('#fini').val(),
                    ffini      :$('#ffini').val()
                },
                success: function(resp){
                    $("#segundo").html(resp);
                }
            });


        });

    </script>
</style>
</body>
</html>

