<!DOCTYPE html>
<head></head>
<body>
	
	<table>
		<tr><td>Cuenta</td>
			<td>
				<select id="listabancaria">
				<?php while($b=$listabanaria->fetch_array()){ ?>
					<option value="<?php echo $b['idbancaria']; ?>"><?php echo $b['nombre']." (".$b['description']."[".$b['manual_code']."])"; ?></option>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Folio:</td>
			<td><input type="text"/></td>
		</tr>
		
	</table>
</body>
</html>