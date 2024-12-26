<!DOCTYPE html>
	<head>
			<meta charset="utf-8">
			<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
			<script type="text/javascript" src="js/concentradoivaprove.js"></script>
			<link rel="stylesheet" type="text/css" href="css/moviprove.css"/>
			<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet">
            <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
			<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
		<style type="text/css">
         #mov{display: none;}
        </style>
	</head>
	<body>

		<div id='confi' style="">
		
        <div class="repTitulo">Concentrado de IVA por Proveedor.</div><br>
        <div class="per" id="info">
            <ul>
                <li><label>Considerar periodo de acreditamiento</label><input type="checkbox" class="nminputcheck" id='acreditamiento'   onclick="funcion();" checked=""/><input type="hidden" id='cred' value=1></li>
                <div id="eje">
                <li><label>Ejercicio</label><select id='ejercicio' class="nminputselect"></select></li>
                <li><label>Periodo Del:</label><select style="margin-right: 35%;" id="delperiodo"  class="nminputselect">
                    <option selected value='1'>Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                    
                </select></li>
                <li><label>Al:</label><select id="alperiodo"  class="nminputselect" >
                    <option selected value='1'>Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                    
                </select></li>
                </div>

                <div id="mov">
                <li><label>Movimientos Del:</label><input type="date" class="nminputtext" id="inicio" style="margin-right: 12%;width:31%;"/></li>
                <li><label>Al:</label><input type="date" class="nminputtext" id="fin" style='width:31%;'/></li>
                </div>
                <li><label>Provedores a</label><input type="radio" class="nminputradio" name='prove' value='1' onclick="muestra();" checked=""> Todos
    <input type="radio" class="nminputradio" name='prove' value='2' onclick="muestra();"> Algunos <br>
    <label id='label' style="display: none;">Ejem. 1-10,21,51</label><input type="text" class="nminputtext" id='algunos' style='display: none;'></li>
                <li><label>Ver todas las tazas</label><input type="checkbox" class="nminputcheck" id='tasas'    checked=""/></li>
                <li><label>Mostrar todos lo que aplican</label><input type="checkbox" class="nminputcheck" id='aplica' value="1" checked=""/></li>
                <li><label>Usar Fecha de Impresión</label><input type="checkbox" class="nminputcheck" id='fecha'   onclick="fecImp();" checked=""/></li>
                <div id="fimpr">
                    <li><label>Fecha de Impresión</label><input type="text" class="nminputtext" id="inicio2" style="margin-right: 12%;width:31%;" value="<?php echo date('Y-m-d');?>"/></li>
                </div>
                <li><label></label><input type="button" class="nminputbutton" id='ejecutar' title="EJECUTAR REPORTE" value='Ejecutar Reporte <F10>' onclick="concentra();"></li>
            </ul>
        </div>



		<!--div class="nmwatitles">Concentrado de IVA por Proveedor.</div><br>
        <div id="pestanas">
            <ul id="lista">
                <li id="pestana1"><a href='javascript:cambiarPestana(pestanas,pestana1);'>Principal</a></li>
                <li id="pestana2"><a href='javascript:cambiarPestana(pestanas,pestana2);'>Otros Parametros</a></li>
            </ul>
        </div>
 
        <body onload="javascript:cambiarPestana(pestanas,pestana1);">
 
        <div id="contenidopestanas">
            <div id="cpestana1">
                <input type="checkbox" class="nminputcheck" id='acreditamiento'   onclick="funcion();" checked=""/><label>Considerar periodo de acreditamiento.</label>
                <input type="hidden" id='cred' value=1>
                <br/><br/>
                <label>Ejercicio.</label><br>
                <select id='ejercicio' class="nminputselect">
                	
                </select>
                <br/><br/>
                <label style="margin-right: 38%;">Del periodo</label><label >Al periodo</label><br>
                <select style="margin-right: 35%;" id="delperiodo"  class="nminputselect">
                	<option selected value='1'>Enero</option>
                	<option value="2">Febrero</option>
                	<option value="3">Marzo</option>
                	<option value="4">Abril</option>
                	<option value="5">Mayo</option>
                	<option value="6">Junio</option>
                	<option value="7">Julio</option>
                	<option value="8">Agosto</option>
                	<option value="9">Septiembre</option>
                	<option value="10">Octubre</option>
                	<option value="11">Noviembre</option>
                	<option value="12">Diciembre</option>
                	
                </select>
                
                <select id="alperiodo"  class="nminputselect" >
                	<option selected value='1'>Enero</option>
                	<option value="2">Febrero</option>
                	<option value="3">Marzo</option>
                	<option value="4">Abril</option>
                	<option value="5">Mayo</option>
                	<option value="6">Junio</option>
                	<option value="7">Julio</option>
                	<option value="8">Agosto</option>
                	<option value="9">Septiembre</option>
                	<option value="10">Octubre</option>
                	<option value="11">Noviembre</option>
                	<option value="12">Diciembre</option>
                	
                </select><br/><br/>
  				<label style="margin-right: 35%;">Movimientos del</label><label >Al</label><br>
	
	<input type="date" class="nminputtext" id="inicio" style="margin-right: 12%;width:31%;"/>
	<input type="date" class="nminputtext" id="fin" style='width:31%;'/> 
	<br/><br/>
	Proveedores a imprimir <br/>
	<input type="radio" class="nminputradio" name='prove' value='1' onclick="muestra();" checked="">Todos
	<input type="radio" class="nminputradio" name='prove' value='2' onclick="muestra();">Algunos
	<input type="text" class="nminputtext" id='algunos' style='display: none;'><label id='label' style="display: none;">Ejem. 1-10,21,51</label>
	<br>
	<input type="checkbox" class="nminputcheck" id='tasas'    checked=""/><label>Ver todas las tasas.</label>
	<br>
	 <input type="checkbox" class="nminputcheck" id='aplica' value="1" checked=""/>Mostrar solo los que Aplican.

	 <input type="checkbox" id='excel'   onclick="" />Exportar a excel >
	
	<br/><br/>
	<input type="button" class="nminputbutton" id='ejecutar' title="EJECUTAR REPORTE" value='Ejecutar Reporte <F10>' onclick="concentra();">
	&nbsp;&nbsp;&nbsp;&nbsp;
	
            </div>
            <div id="cpestana2">
            	
 <input type="checkbox" class="nminputcheck" id='fecha'   onclick="" checked=""/>
 <label>Usar fecha de impresi&oacute;n .</label>
 <input type="text" class="nminputtext" id="inicio2" style="margin-right: 12%;width:31%;" value="<?php // echo date('Y-m-d');?>"/>
            
            
            </div>
	</div-->
	</div>
	<div id="acredita" style="display: none;"></div>	
	<div id='detallado' style="display: none;"></div>	
	</body>
</html>