<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		.cuerpo{width: 520px; height: auto  padding: 7px; font-family: arial;}
		.tamanoSel{width: 200px;	text-overflow: ellipsis;}

	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function()
	{
		$('#nmloader_div',window.parent.document).hide();
	});
		
	</script>
</head>
<body>
	
	<div class='repTitulo' >Catalogo de Cuentas</div>
	<div class="per">
		<form name='reporte' id='info' method='post' action='index.php?c=reports&f=catalogoCuentasReporte'>
					<ul><li><label>Naturaleza:</label>
							<select name='naturaleza'>
								<option value='0'>Todos</option>
								<?php
								while($n = $naturalezas->fetch_object())
								{
									echo "<option value='$n->nature_id'>$n->description</option>";
								}
								?>
								
							</select></li>
						<li><label>Tipo:</label>
							<select name='tipo'>
								<option value='0'>Todos</option>
								<?php
								while($t = $tipos->fetch_object())
								{
									echo "<option value='$t->type_id'>$t->description</option>";
								}
								?>
								
							</select></li>
						<li><label></label><input type="submit" onclick="$('#nmloader_div',window.parent.document).show();" class="nminputbutton" value="Ejecutar Reporte"></li>
						</ul>

					</form>
				
	</div>

	
</body>
</html>