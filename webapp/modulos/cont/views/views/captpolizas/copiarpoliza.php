<style>
#lista td
{
	width:146px;
	text-align: center;
	border:1px solid #BDBDBD;
}

#buscar
{
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
-o-border-radius: 4px;
border-radius: 4px;
}

#cargando
{
	display:none;
	position:absolute;
	z-index:1;
}

</style>
<script src="js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<div id='copiarPoliza' title='Copiar Poliza' >
<div id='title'>Copiar</div><br>
<form action="index.php?c=CaptPolizas&f=copiaPoliza" method="post">
	<input type='hidden' value='<?php echo $numPoliza['id']; ?>' name='idpoliza' id='idpoliza'>

	<select onchange="copia()" id="ele" name="ele" class="nminputselect">
		<option value="1">Completa</option>
		<option value="2">Movimientos</option>
	</select>
<br>
	
<br>
<input type="text" id="conceptocopy" name="conceptocopy" class="nminputtext" placeholder="Concepto..."/>
<label id="fechaco">Fecha</label><input type="date" id="fechacopy" name="fechacopy"  class="nminputtext" style="width: 157px;height: 35px"/><br>

<br>
<select id="movi"  multiple="" name="movimientoscopi[]" style="display: none" class="nminputselect">
</select>

<br><label id="txtc" name="txtc" style="display: none">Copiar a:</label>
<div id="selectpoliza" >
<select id="idpolicopy" name="idpolicopy" class="nminputselect" > 
	
</select>
<input type="submit" value="" id="submit" style="display: none" > 
</form>
</div>			
</div>