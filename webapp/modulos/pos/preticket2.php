<?php
include "../SAT/PDF/phpqrcode/qrlib.php";

session_start();
error_reporting(0);
$idventa=$_REQUEST["idventa"];
//$print = $_REQUEST["print"];
//echo '<h1>'.$idventa.'</h1>';
include("controllers/caja.php"); 



$cajaController = new Caja;
$organizacion = $cajaController->datosorganizacion();
$infoTicket = $cajaController->infoSuspendida($idventa);
//echo '/////////////////////////////////////////////////////////////////////////////////////////////////////////////';
//echo '<br>'.$infoTicket[0]['fecha'].'<br>';
//echo '/////////////////////////////////////////////////////////////////////////////////////////////////////////////';
$infoTicket = json_decode($infoTicket[0]['arreglo1']);
$infoTicket = $cajaController->object_to_array($infoTicket);
//echo '/////////////////////////////////////////////////////////////////////////////////////////////////////////////';
//print_r($infoTicket);

?>
<meta charset="UTF-8">
<link rel="stylesheet" rev="stylesheet" href="css/netpos.css" />
<link rel="stylesheet" rev="stylesheet" href="css/netpos_print.css"  media="print"/>
<link rel="stylesheet" rev="stylesheet" href="../../libraries/font-awesome-4.7.0/css/font-awesome-4.7.min.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="../../libraries/JsBarcode.all.min.js"></script>
<script src="js/ticket.js"></script>
<script>
	jQuery(document).ready(function($) {
		JsBarcode("#barcodeDiv", "<?php echo 'PRETICKET'.$idventa; ?>");
		window.print();
	});
</script>
<style>

body{
	font-family: Tahoma,'Trebuchet MS',Arial;
}
#letraschicas{
	font-size: 13px;

}
.small_button a{
	color:white;
	text-decoration:none;
	font-family:Arial, Helvetica, sans-serif;
}
.textWrap {
    text-align: justify;
    word-wrap: break-word;
    font-size: 10px;
}

@media print
{
	.item_number{display:none;}
}
</style>
<div id="receipt_wrapper">
		<div id="logo">
		<?php 
			$imagen='../../netwarelog/archivos/1/organizaciones/'.$organizacion[0]['logoempresa'];
			$imagesize=getimagesize($imagen);
			$porcentaje=0;
			if($imagesize[0]>200 && $imagesize[1]>90){
				if($imagesize[0]>$imagesize[1]){
					$porcentaje=intval(($imagesize[1]*100)/$imagesize[0]);
					$imagesize[0]=200;
					$imagesize[1]=(($porcentaje*200)/100);
				}else{
					$porcentaje=intval(($imagesize[0]*100)/$imagesize[1]);
					$imagesize[0]=200;
					$imagesize[1]=(($porcentaje*200)/100);	
				}
			}
			//"../../netwarelog/archivos/1/organizaciones/'.$cliente[0]->logoempresa.'"
			$src="";
			if($imagen!="" && file_exists($imagen))
				$src='<img src="'.$imagen.'" style="width:'.$imagesize[0].'px;height:'.$imagesize[1].'px;display:block;margin:0 auto 0 auto;"/>';
			echo $src;
		?>
	
	</div>
	<table align="center" style="width: 100%;">
	<tbody style="width: 100%;">
	<tr style="width: 100%;">
	<td style="width: 100%;">
	<div id="receipt_header" style="text-align:center;">
	<a id="btn_imprimir" style="display: none;" ><li class="fa fa-print fa-2x">Imprimir ticket</li></a>
		<div id="company_name" style="text-align: center; font-size:15px;font-family: Tahoma,'Trebuchet MS',Arial;"><?php echo $organizacion[0]['nombreorganizacion'];?></div>
	<!--	<div id="company_address"><?php echo utf8_decode($organizacion[0]['domicilio']." ".$organizacion[0]['municipio'].",".$organizacion[0]['estado']);?></div> -->

	<?php if(!empty($organizacion[0]['RFC'])) {?>
	<div id="rfc" style="text-align: center; font-size:15px;font-family: Tahoma,'Trebuchet MS',Arial;">RFC: <?php echo $organizacion[0]['RFC'];?></div>	
	<?php } ?>
	<!--<div id="company_address" style="text-align: center; font-size:15px;font-family: Tahoma,'Trebuchet MS',Arial;"><?php echo $datosSucursal[0]['direccion']." ".$datosSucursal[0]['municipio'].",".$datosSucursal[0]['estado'];?>	
	</div> -->
	<?php 
		if($organizacion[0]['paginaweb']!='-'){
			echo '<div id="paginaWeb" style="text-align: center; font-size:13px;font-family: Tahoma,'."'Trebuchet MS'".',Arial;">'.$organizacion[0]['paginaweb'].'</div>';	
		}
	?>
		<?php if(strcmp($venta[0]['estatus'],"Cancelada")==0){?>
		<!--<div id="company_phone">		
			<?php echo "Venta ".$venta[0]['estatus'];?>
		</div> -->
		<?php
	}  ?>

	<!--<div id="sale_receipt"><?php echo  $organizacion[0]['logoempresa'];?></div>	-->
	<!--<div id="sucursal" style="text-align: center; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Sucursal:<?php echo $datosSucursal[0]['nombre']; ?></div>
		<div id="sucursal" style="text-align: center; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Telefono:<?php echo $datosSucursal[0]['tel_contacto']; ?></div> -->
	<div id="sale_receipt" style="text-align: center; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Preticket</div>
	<!--<div id="customer" style="text-align: center; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Cliente:<?php echo $venta[0]['cliente']; ?></div> -->

	<div id="sale_id" style="text-align: center; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Id Preticket:<?php  echo $idventa; ?></div> 
	<?php
	
// Valida si la instancia tiene Foodware, para mostrar los dolares
    session_start();
	if (in_array(2156, $_SESSION['accelog_menus'])) {
	// Consulta los ajustes de Foodware
		$ajustes_foodware = $cajaController->listar_ajustes_foodware($objeto);

	// Valida si se debe de mostrar la informacion de la comanda
	if ($ajustes_foodware['mostrar_info_comanda'] == 1) {
		if (empty($_SESSION['detalles_mesa'])) {
			$objeto['id_venta'] = $_REQUEST["idventa"];
			$_SESSION['detalles_mesa'] = $cajaController->listar_detalles_comanda($objeto);
		}
		
// Imprime los datos de la comanda
		if (!empty($_SESSION['detalles_mesa'])) { ?>
			<div id="receipt_general_info" style="text-align:center; border-top:2px solid;">
				<div style="width: 5%; float: left;">&nbsp;</div>
				<?php if (!empty($_SESSION['detalles_mesa']['nombre_mesero'])) { ?>
					<div id="employee" style="width: 55%; float: left; text-align: left; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Mesero: <?php echo  $_SESSION['detalles_mesa']['nombre_mesero'] ?></div>
				<?php } ?>
				<?php if (!empty($_SESSION['detalles_mesa']['persona'])) { ?>
					<div id="persons" style="width: 35%; float: left; text-align: right;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Personas: <?php echo  $_SESSION['detalles_mesa']['persona'] ?></div>
				<?php } ?>
			</div><br>
			<div id="receipt_general_info" style="text-align:center;">
				<div style="width: 5%; float: left;">&nbsp;</div>
				<?php if (is_numeric($_SESSION['detalles_mesa']['nombre_mesa'])) { ?>
		 			<div id="mesa" style="width: 45%; float: left; text-align: left;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Mesa: #<?php echo $_SESSION['detalles_mesa']['nombre_mesa']; ?></div>
				<?php } else { ?>
					<div id="mesa" style="width: 45%; float: left; text-align: left;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">Mesa: <?php echo $_SESSION['detalles_mesa']['nombre_mesa']; ?></div>
				<?php } ?>
				<div id="comand" style="width: 45%; float: left; text-align: right; font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;"><?php echo $_SESSION['detalles_mesa']['codigo']; ?></div>

			</div>
		
			<?php
			
			unset($_SESSION['detalles_mesa']);
		}
	} } ?>
</div>
</td>
</tr>
</tbody>
</table>
<table border='0' style="width: 100%; border-top:2px solid;" align="center">
	<tr style="font-weight: bold; font-size:15px; font-family: Tahoma,'Trebuchet MS',Arial;">
		<!--<th style="width:25%;" class='item_number'>#</th>-->
		<th style="width:20%; text-align: center;">Cant</th>
		<th style="width:40%; text-align: left;">Producto</th>
		<!--<th style="width:17%;">Precio</th>-->

		<!--<th style="width:16%;text-align:center;">Descuento</th>-->
		<?php echo ($precio_unit_ticket == "1") ? '<th style="width:20%;text-align:center;">P. U.</th>' : ''; ?>
		<th style="width:20%;text-align:center;">Total</th>
	</tr>
	<?php 
		$sub = 0;
		$descDesc = '';
		foreach ($infoTicket as $key => $value) {
			if($key!='cargos' && $key!='descGeneral'){
				if($value['comentario'] != 'omitir'){// evita que se muestren productos-complementos
				 echo '<tr style="font-size:13px; font-family: Tahoma,'."'".'Trebuchet MS'."'".',Arial; text-align: center">';
				 echo "<td style='width:25%; text-align:center;'>".$value['cantidad']."</td>";
				  	if($value['tipodescuento']=='C'){
				 		$descDesc  = '[Cortesia]';
				 	}
				 if($value['montodescuento'] > 0){
				 	//$descDesc  = '[Precio:$'.number_format($value['precio'],2).',Descuento:$'.number_format($value['montodescuento'],2).'/'.$value['tipodescuento'].$value['descuento'].']';
				 	$descDesc  = '[Descuento:$'.number_format($value['montodescuento'],2).'/'.$value['tipodescuento'].$value['descuento'].']';
				 }
				 $nm = $value['nombre'];
				 echo "<td style='width:34%; text-align: left;' class='textWrap'><span class='short_name'>".$nm.' '.$descDesc."</td>";
				 echo ($precio_unit_ticket == "1") ? ("<td style='width:23%; text-align: center; text-align:center;'>$".number_format($value['preciounitario'],2)."</td>") : '';
				 echo "<td style='width:23%; text-align: center; text-align:center;'>$".number_format(($value['cantidad'] * $value['precio']),2)."</td>";
				 echo "</tr>";
				 $sub +=($value['cantidad'] * $value['precio']);
				 $descDesc = '';
				} // evita que se muestren productos-complementos fin
			}
			
		}
	?>
		<?php
		/*if($producto->montodescuento>0){
			?>
			<tr>
				<td style='text-align:center;'>Desc:</td><td style='text-align:center;'>$<?php echo number_format( $producto->montodescuento,2,".",","); ?></td>
			</tr>
			<?php
		} */
	?>	<tr style="width: 100%; ">
		<td colspan="4" style="width:100%;border-top:2px solid; ">
			<div style="text-align: right;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">
				<strong>Subtotal: </strong>$<?php echo number_format($infoTicket['cargos']['subtotal'],2,".",","); ?>
			</div>
		</td>
	</tr>
		<?php 
			if($infoTicket['descGeneral'] > 0){?>
				<tr style="width: 100%">
					<td colspan="4" style="width:100%;">
						<div style="text-align: right;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">
							<strong>Descuento: </strong>$<?php echo number_format($infoTicket['descGeneral'],2); ?>
						</div>
					</td>
				</tr>impuestosPorcentajes
			<?php }
		?>
	<?php 
		$totalimpuestos = 0;
		//print_r($impuestos_venta);
		foreach ($infoTicket['cargos']['impuestosPorcentajes'] as $key2 => $value2) {
			//echo 'CCCC'.$key;
			echo '<tr style="width: 100%"><td colspan="4" style="width:100%;">';?>
			<div style="text-align: right;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">
				<strong><?php echo $key2; ?>: </strong>$<?php echo number_format($value2,2); ?>
			</div>
			<?php echo '</td></tr>';
			$totalimpuestos+=$value2;
		}
	?>
		<tr style="width:100%;">
		<td colspan="4" style="width:100%;">
			<div style="text-align: right;font-size:13px;font-family: Tahoma,'Trebuchet MS',Arial;">
				<!--<strong>Total: </strong>$<?php echo number_format((($sub+$totalimpuestos) - $venta[0]['descuento']),2,".",",").' '.$venta[0]['codigo']; ?> -->
				<strong>Total: </strong>$<?php 
				if(($venta[0]['tipo_cambio'] * 1) > 1){
					echo number_format((($infoTicket['cargos']['subtotal']+$totalimpuestos) - $infoTicket['descGeneral']),2,".",","); 
				}else{
					echo number_format((($infoTicket['cargos']['subtotal']+$totalimpuestos) - $infoTicket['descGeneral']),2,".",",");
				}
				
				?>
			</div>
		</td>
	</tr>
</table>
<div style="text-align: center; margin-top:10px; font-style: bold;">
		<img id="barcodeDiv" style="width:190px;margin-left:-3px; font-style: bold;"/>
	</div>



</div><!-- Div receipt_wrapper -->