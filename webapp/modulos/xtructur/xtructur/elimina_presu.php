<?php
	include('conexiondb.php');
	$id_pres=$_POST['id_pres'];
	$mysqli->query("DELETE FROM constru_recurso WHERE id_presupuesto='$id_pres';");
	$mysqli->query("DELETE FROM constru_presupuesto WHERE id='$id_pres';");
?>