<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Pruebas de click derecho</title>
	<link rel="stylesheet" href="js/select2/select2.css">
	<link rel="stylesheet" href="css/accountsTree.css">
	<script src='js/jquery.js' type='text/javascript'></script>
	<script src='js/jquery.tinysort.min.js' type='text/javascript'></script>
	<script src='js/select2/select2.js' type='text/javascript'></script>
	<script src='js/jquery.maskedinput.js' type='text/javascript'></script>
	
	<script>
	<?php 
	require 'js/accountsTree.js.php';
	?>
	</script>
</head>
<body>
	<div id="spinner"></div>
	<div class="layer"></div>
	<div class="nmwatitles">Mi arbol Contable</div>
	
	<div id="context">
		<span class='nw'>Nuevo</span>
		<div class="line nw"></div>
		<span class='sn'>Agregar Hijo</span>
		<div class="line sn"></div>
		<span class='dl'>Eliminar</span>
			<!-- <div class="line dl"></div>
			<span class="nt">Ninguno</span> -->
			<div class="line"></div>		
			<span class="mv">Mover</span>
		</div>
		<div id="context2">
			<span>A nivel de...</span>
			<div class="line"></div>
			<span>Como Hijo de...</span>
		</div>
		<div id="content">
			<div id='srch' style="margin-top:5px;">
				<label for="search" >Buscar: </label> 
				<input type="text" class='rounded nmcatalogbusquedainputtext' style='width:auto' id='search'>
				<input type="button" class="nminputbutton_color2" id="exportar" value="Exportar cuentas" onclick="exportar()">
				<span class='sort nminputbutton_color2' >Ordenar Alfabeticamente</span>

				<div class="tooltip">
					Utilice doble click para modificar una cuenta y click derecho para mostrar mas opciones
					<span>x</span>
				</div>
				<span class="help">?</span>
				
			</div>	
			<br>		
			<hr class='separator' />
			<div class="left" id='cont' style='width:100%;'>
				<ul></ul>
			</div>
			<div class="table">
				<div id="form" >
					<div class="ui-widget-header" style="text-align: center; font-size: 18px; padding-top: 7px; height: 28px ! important;">Registro de Cuenta</div>
					<div class="formColumn">
						<div class="container">
							<label for="accountNumber">Numero de la cuenta:</label>
						</div>

						<div class="container">
							<label for="accountName">Nombre de la Cuenta:</label>
						</div>
						<div class="container">
							<label for="fatherAccount">Sub-cuenta de:</label>
						</div>
						<div class="container">
							<label for="nature">Naturaleza:</label>
						</div>
						<div class="container">
							<label for="coin">Moneda:</label>
						</div>
						<div class="container">
							<label for="sucursal">Utiliza Multiples Sucursales:</label>
						</div>
					</div>
					<div class="formColumn">
						<div class="container">
							<input type="text" class='rounded validate nminputtext' placeholder='<?php echo $inputMask; ?>' name="accountNumber" id="accountNumber">

						</div>
						
						<div class="container">
							<input type="text" maxlength='100' class='rounded validate nminputtext' name='cuenta' id='accountName' >
						</div>
						
						<div class="container">
							<input type="hidden" class='rounded' name='fatherAccount' id="fatherAccount">
							<label id='fatherAccountId'></label>							
						</select>
					</div>
					
					<div class="container">
						
						<select name="nature" id="nature" class="nminputselect">
							<?php echo $nature; ?>
						</select>
					</div>
					
					<div class="container">
						<select name="coin" id="coin" class="nminputselect">
							<?php echo $coins; ?>
						</select>
					</div>
					
					<div class="container">
						<select name="sucursal" id="sucursal" class="nminputselect">
							<option value='1'>SI</option>
							<option value="0" selected>NO</option>
						</select>
					</div>
				</div>
				<div class="formColumn">
					
					<div class="container">
						<label for="auto">Numero de la cuenta automatico:</label>
					</div>
					
					<div class="container">
						<label for="secondName">Nombre En un segundo idioma:</label>
					</div>
					
					<div class="container">
						<label for="type">Tipo de Cuenta:</label>
					</div>
					
					<div class="container">
						<label for="main">Clasificacion de la Cuenta:</label>
					</div>
					
					<div class="container">
						<label for="status">Estatus:</label>
					</div>
					
					<!-- <div class="container">
						<label for="group_dig" style="display: none">Digito Agrupador</label>
					</div> -->
					<div class="container">
						<label for="group_dig">Cuenta asociada(Digito Agrupador)</label>
						
					</div>
				</div>
				
				<div class="formColumn">
					<div class="container">
						<input type="hidden" class='rounded validate' placeholder='<?php echo $inputMask; ?>' name="auto" id="auto">
						<label id='accountNumberId'></label>
					</div>
					<div class="container">
						<input type="text" maxlength='100' class='rounded nminputtext' name='segundo' id='secondName' >
					</div>
					<div class="container">
						<select name="tipo" id="type">
							<?php echo $classification; ?>
						</select>
					</div>
					<div class="container">
						<select name="main" id="main">
							<?php echo $type; ?>
						</select>
					</div>
					<div class="container">
						<select name="status" id="status">
							<?php echo $status; ?>
						</select>
					</div>
					
					<!-- <div class="container">
						<input type="text" class='rounded numeric' value='0' name="group_dig" id="group_dig" style="display: none">
					</div> -->
				<input type="text" class='rounded numeric' value='0' name="group_dig" id="group_dig" style="display: none">

					<div class="container">
						<select id="oficial" name="oficial" style="this.style.width = 'auto'; width:230px;" class="nminputselect">
							<?php echo $oficial; ?>
						</select>
					</div>
					<div style='text-align:right; padding-top:15px'>	
					<!-- <input type="button" id='newBro' value='Nuevo Hermano' style='float:left;'>
					<input type="button" id='newSon' value='Nuevo Hijo' style='float:left;'>	 -->			
					
					<input type="button" class='' id='cancel' value="Cancelar">
					<input type="button" class='' id='sve' value="Guardar">
				</div>
			</div>
			<br></br>
		</div>
	</div>
</div>
<div class="end">
</div>