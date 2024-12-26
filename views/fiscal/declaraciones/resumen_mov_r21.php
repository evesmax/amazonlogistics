<?php
if($toexcel==1){
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=resumenR21.xls");
}
?>
<html>
<head>
	<title>Resumen de movimientos general para R21 IVA </title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language='javascript' src='js/pdfmail.js'></script>
		<script type="text/javascript" src="js/jquery.js"></script><!-- 
		<script type="text/javascript" src="js/resumenGeneralR21.js"></script> -->

	<?php
	if($toexcel==0){//se muestra reporte en navegador
	?>
		<!--LINK href="../../netwarelog/catalog/css/view.css" title="estilo" rel="stylesheet" type="text/css" />	
		<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
	 	<link rel="stylesheet" href="css/style.css" type="text/css">
	 	<div class="iconos">
	 	<a href="javascript:window.print();">
		<img class="nmwaicons" border="0" src="../../netwarelog/design/default/impresora.png">
		</a>
		
		<td width="16" align="right">
		<a href="javascript:window.print();">
		</td>
		<td width="16" align="right">
		 <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a> 
		</td>
		<td width="16" align="right">
		<a href="javascript:mail();">
		<img border="0" title="Enviar reporte por correo electrónico" src="../../../webapp/netwarelog/repolog/img/email.png">
		</a>
		</td>
		<td>
			<a id="filtros" href="index.php?c=resumenGeneralR21&f=filtro" onclick="">
				<img border="0" src="../../netwarelog/repolog/img/filtros.png" title="Haga click aquí para cambiar los filtros...">
			</a>
		</td>
		</div>
	<?php
	}
	?>
	<style type="text/css">
		.titulo_r21{text-align: center; font: 20px arial; border: 0px solid;}
		
		.fondoVerde{background-color: #4c4c4c;color: white;}
		
		
	</style>
</head>
<body >
	<div class="repTitulo">Resumen de Movimientos General para R21</div>
	<div id="imprimible">
	<table style="width:100%;">
		<tr>
			<td style="width:50%;">
			<?php
			$logo=$organizacion->logoempresa;
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<img id='logo_empresa' src='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'>
			</td>
			<td style="width:50%;"></td>
		</tr>
		<tr style="text-align:center;color:#576370;font-size:12px;">
			<td colspan="2">
				<b style="color:black;font-size:18px;"><?php echo $organizacion->nombreorganizacion; ?></b><br>
				<b style="font-size:15px;">Resumen de Movimientos General para R21 IVA</b><br>
				RFC: <b><?php echo $organizacion->RFC; ?></b><br>
				Ejercicio <b><?php echo $ejercicio->NombreEjercicio;?></b> Periodo De <b><?php echo $meses[$per_ini]; ?></b> A <b><?php echo $meses[$per_fin]; ?></b>
				<br><br>
			</td>
		</tr>
	</table>


	<table  cellpadding="4" cellspacing="0" style="font-size:9px;">
			
		
			<tr style="background-color:#edeff1;font-weight:bold;"><td width="21%">Monto de los actos o actividades pagados</td>
				<td width="6%">Enero</td><td width="6%">Febrero</td><td width="6%">Marzo</td><td width="6%">Abril</td><td width="6%">Mayo</td><td width="6%">Junio</td><td width="6%">Julio</td><td width="6%">Agosto</td><td width="7%">Septiembre</td><td width="6%">Octubre</td><td width="6%">Noviembre</td><td width="6%">Diciembre</td><td width="6%">Total</td>
			</tr>

			<tr class="busqueda_fila"><td class="consepto_r21">Total de los actos o actividades pagados a la tasa del 16% de IVA</td>
				<?php $suma=0; 

				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($valorBase16[$i]['16%'],2,'.',','); $suma+=$valorBase16[$i]['16%']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
					<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila"><td class="consepto_r21">Total de los actos o actividades pagados a la tasa del 11% de IVA</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($valorBase11[$i]['11%'],2,'.',','); $suma+=$valorBase11[$i]['11%']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
					<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila"><td class="consepto_r21">Total de actos o actividades pagados en la importación de bienes y servicios a la tasa del 16%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($valorbaseimport16[$i]["16%"],2,'.',','); $suma+=$valorbaseimport16[$i]["16%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
					<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila"><td class="consepto_r21">Total de actos o actividades pagados en la importación de bienes y servicios a la tasa del 11%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($valorbaseimport11[$i]["11%"],2,'.',','); $suma+=$valorbaseimport11[$i]["11%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
					<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila"><td class="consepto_r21">Total de los demas actos o actividades pagados a la tasa del 0%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($valorbase0[$i]["0%"],2,'.',','); $suma+=$valorbase0[$i]["0%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila"><td class="consepto_r21">Total de los actos o actividades pagados por los que no se pagará el IVA (Excentos)</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($totalBaseIvaExcento[$i]["Exenta"],2,'.',','); $suma+=$totalBaseIvaExcento[$i]["Exenta"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr style="background-color:#edeff1;font-weight:bold;" >
				<td  colspan="14" align="center">Determinacion del impuesto al valor agregado</td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Total del IVA de actos o actividades pagados a la tasa del 16%</td>
				<?php $suma=0; 
				$arrIva = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($totalTasaIvaAcr16[$i]["16%"],2,'.',','); $suma+=$totalTasaIvaAcr16[$i]["16%"]; $arrIva[$i]=$totalTasaIvaAcr16[$i]["16%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Total del IVA de actos o actividades pagados a la tasa del 11%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($totalTasaIvaAcr11[$i]["11%"],2,'.',','); $suma+=$totalTasaIvaAcr11[$i]["11%"]; $arrIva[$i]+=$totalTasaIvaAcr11[$i]["11%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Total del IVA de actos o actividades pagados en la importación de bienes o servicios a la tasa del 16%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($ivaimport16[$i]["16%"],2,'.',','); $suma+=$ivaimport16[$i]["16%"]; $arrIva[$i]+=$ivaimport16[$i]["16%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Total del IVA de actos o actividades pagados en la importación de bienes o servicios a la tasa del 11%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					<td style="text-align: right;"><?php echo number_format($ivaimport11[$i]["11%"],2,'.',','); $suma+=$ivaimport11[$i]["11%"]; $arrIva[$i]+=$ivaimport11[$i]["11%"]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr style="background-color:#f6f7f8;font-weight:bold;">
				<td class="consepto_r21"><b>Total de IVA trasladado al contribuyente (Efectivamente pagado)</b></td>
				<?php $suma=0; 
				$ivaPrevio = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><b><?php echo number_format($efectivamentepagado[$i],2,'.',','); $suma+=$efectivamentepagado[$i];  ?></b></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><b><?php echo number_format($suma,2,'.',','); ?></b></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de compras y gastos nacionales para gravados</td>
				<?php $suma=0; 
				$arrIva = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($arr[$i]['GastosGravadosNacional'],2,'.',','); $suma += $arr[$i]['GastosGravadosNacional'];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de compras y gastos importación para gravados</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($arr[$i]['GastosGravadosExtrangeros'],2,'.',','); $suma += $arr[$i]['GastosGravadosExtrangeros']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de inversiones nacionales para gravados</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($arr[$i]['InvGravadosNacional'],2,'.',','); $suma += $arr[$i]['InvGravadosNacional'];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de inversiones importación para gravados</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($arr[$i]['InvGravadosExtrangeros'],2,'.',','); $suma += $arr[$i]['InvGravadosExtrangeros'];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" busqueda_fila" style="background-color:#f6f7f8;font-weight:bold;">
				<td class="consepto_r21"><b>IVA de actos gravados</b></td>
				<?php $suma=0; 
				$totalIvaPeriodo = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><b><?php echo number_format($sumagravados[$i],2,'.',','); $suma += $sumagravados[$i];?></b></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><b><?php echo number_format($suma,2,'.',','); ?></b></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de compras y gastos nacionales y de importacion para actos Excentos de IVA</td>
				<?php $suma=0; 
				$arrIva = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($arr[$i]['GastosExentos']+$arr[$i]['GastosExentosnacional'],2,'.',','); $suma+=$arr[$i]['GastosExentos']+$arr[$i]['GastosExentosnacional'];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de inversiones nacionales y de importacion para actos Excentos de IVA</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($arr[$i]['InvExentos']+$arr[$i]['InvExentosnacional'],2,'.',','); $suma+=$arr[$i]['InvExentos']+$arr[$i]['InvExentosnacional'];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>				
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" busqueda_fila">
				<td class="consepto_r21">IVA de actos Excentos</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($IVAExcentos[$i],2,'.',','); $suma+=$IVAExcentos[$i]; ; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" busqueda_fila">
				<td class="consepto_r21">IVA de bienes para generar actos Excentos y gravados (No identificados) (previo)</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($ivabienesutilizados[$i],2,'.',','); $suma+=$ivabienesutilizados[$i]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">% Art 5 IVA</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php if($use_prop==1){ echo number_format($prop,2,'.',','); } else echo "0.00";  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php if($use_prop==1){ echo number_format($prop,2,'.',','); } else echo "0.00"; ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">% Art 5-B IVA</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php if($use_prop==2){ echo number_format($prop,2,'.',','); } else echo "0.00"; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php if($use_prop==2){ echo number_format($prop,2,'.',','); } else echo "0.00"; ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">IVA de bienes para generar actos Excentos y gravados (No identificados) (definitivo)</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($multipliart5[$i],2,'.',','); $suma+=$multipliart5[$i];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" " style="font-weight:bold;background-color:#edeff1;">
				<td class="consepto_r21">Total IVA acreditable del periodo</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($totalacreditable[$i],2,'.',','); $suma+=$totalacreditable[$i]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="text-align: right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Valor de actos o actividades gravados al 16%</td>
				<?php $suma=0; 
				$sumaActosGravados = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($totalBaseIvaImp16[$i]['tasa16'],2,'.',','); $suma+=$totalBaseIvaImp16[$i]['tasa16']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Valor de actos o actividades gravados al 11%</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($totalBaseIvaImp11[$i]['tasa11'],2,'.',','); $suma+=$totalBaseIvaImp11[$i]['tasa11']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Valor de actos o actividades gravados al 0% exportación</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($totalBaseIvacausa0[$i]['tasa0'],2,'.',','); $suma += $totalBaseIvacausa0[$i]['tasa0'];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Valor de actos o actividades gravados al 0% otros</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($totalBaseIvacausaotros[$i]['otrasTasas'],2,'.',','); $suma += $totalBaseIvacausaotros[$i]['otrasTasas']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" busqueda_fila" style="background-color:#f6f7f8;font-weight:bold;">
				<td class="consepto_r21"><b>Suma de actos o actividades gravados</b></td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><b><?php echo number_format($sumaactosgravados[$i],2,'.',','); $suma += $sumaactosgravados[$i]; ?></b></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><b><?php echo number_format($suma,2,'.',','); ?></b></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Valor de actos o actividades por las que no se debe pagar el impuesto (Excentos)</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php 
				echo number_format($totalBaseIvacausaExenta[$i]['tasaExenta'],2,'.',','); $suma+=$totalBaseIvacausaExenta[$i]['tasaExenta']; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" " style="background-color:#f6f7f8;font-weight:bold;">
				<td class="consepto_r21">IVA Causado</td>
				<?php 
				$suma = 0;
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($impuestocausado[$i],2,'.',','); $suma += $impuestocausado[$i]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="text-align: right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Otros cantidades a cargo </td>
				<?php $suma=0; 
				$ivaFavorEnContra = array();
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($cargo[$i],2,'.',','); $suma += $cargo[$i];  ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class="busqueda_fila">
				<td class="consepto_r21">Otras cantidades a favor</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
				<td style="text-align: right;"><?php echo number_format($favor[$i],2,'.',','); $suma += $favor[$i]; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="background-color:#e4e7ea;font-weight:bold;text-align:right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>

			<tr class=" " style="background-color:#edeff1;font-weight:bold;">
				<td class="consepto_r21">IVA a favor o en contra</td>
				<?php $suma=0; 
				for($i=$per_ini;$i<=$per_fin;$i++){ ?>
					
				<td style="text-align: right;"><?php echo number_format($ivafavorcargo[$i],2,'.',','); $suma +=$ivafavorcargo[$i] ; ?></td>
				<?php } 
				for($i=$per_fin+1;$i<=12;$i++){?>
					<td style="text-align: right;"></td>
				<?php }?>
				<td style="text-align: right;"><?php echo number_format($suma,2,'.',','); ?></td>
			</tr>
		
	</table>
	</div>	
	<br></br>
	
</center>
<?php if($toexcel==0){?>
<div id="divpanelpdf"
				style="
					position: absolute; top:30%; left: 40%;
					opacity:0.9;
					padding: 20px;
					-webkit-border-radius: 20px;
    			border-radius: 10px;
					background-color:#000;
					color:white;
				  display:none;	
				">
				<form id="formpdf" action="libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
					<center>
					<b> Generar PDF </b>
					<br><br>

					<table style="border:none;">
						<tbody>
							<tr>
								<td style="color:white;font-size:13px;">Escala:</td>
								<td style="color:white;font-size:13px;">
									<select id="cmbescala" name="cmbescala">
									<option value=100>100</option>
<option value=99>99</option>
<option value=98>98</option>
<option value=97>97</option>
<option value=96>96</option>
<option value=95>95</option>
<option value=94>94</option>
<option value=93>93</option>
<option value=92>92</option>
<option value=91>91</option>
<option value=90>90</option>
<option value=89>89</option>
<option value=88>88</option>
<option value=87>87</option>
<option value=86>86</option>
<option value=85>85</option>
<option value=84>84</option>
<option value=83>83</option>
<option value=82>82</option>
<option value=81>81</option>
<option value=80>80</option>
<option value=79>79</option>
<option value=78>78</option>
<option value=77>77</option>
<option value=76>76</option>
<option value=75>75</option>
<option value=74>74</option>
<option value=73>73</option>
<option value=72>72</option>
<option value=71>71</option>
<option value=70>70</option>
<option value=69>69</option>
<option value=68>68</option>
<option value=67>67</option>
<option value=66>66</option>
<option value=65>65</option>
<option value=64>64</option>
<option value=63>63</option>
<option value=62>62</option>
<option value=61>61</option>
<option value=60>60</option>
<option value=59>59</option>
<option value=58>58</option>
<option value=57>57</option>
<option value=56>56</option>
<option value=55>55</option>
<option value=54>54</option>
<option value=53>53</option>
<option value=52>52</option>
<option value=51>51</option>
<option value=50>50</option>
<option value=49>49</option>
<option value=48>48</option>
<option value=47>47</option>
<option value=46>46</option>
<option value=45>45</option>
<option value=44>44</option>
<option value=43>43</option>
<option value=42>42</option>
<option value=41>41</option>
<option value=40>40</option>
<option value=39>39</option>
<option value=38>38</option>
<option value=37>37</option>
<option value=36>36</option>
<option value=35>35</option>
<option value=34>34</option>
<option value=33>33</option>
<option value=32>32</option>
<option value=31>31</option>
<option value=30>30</option>
<option value=29>29</option>
<option value=28>28</option>
<option value=27>27</option>
<option value=26>26</option>
<option value=25>25</option>
<option value=24>24</option>
<option value=23>23</option>
<option value=22>22</option>
<option value=21>21</option>
<option value=20>20</option>
<option value=19>19</option>
<option value=18>18</option>
<option value=17>17</option>
<option value=16>16</option>
<option value=15>15</option>
<option value=14>14</option>
<option value=13>13</option>
<option value=12>12</option>
<option value=11>11</option>
<option value=10>10</option>
<option value=9>9</option>
<option value=8>8</option>
<option value=7>7</option>
<option value=6>6</option>
<option value=5>5</option>
<option value=4>4</option>
<option value=3>3</option>
<option value=2>2</option>
<option value=1>1</option>
									</select> %
								</td>
							</tr>
							<tr>
								<td style="color:white;font-size:13px;">Orientación:</td>
								<td style="color:white;">
									<select id="cmborientacion" name="cmborientacion">
										<option value='P'>Vertical</option>
										<option value='L'>Horizontal</option>
									</select>
								</td>
							</tr>
					</tbody>
				</table>
				<br>
					
				<textarea id="contenido" name="contenido" style="display:none"></textarea>
				<input type='hidden' name='tipoDocu' value='hg'>
				<input type='hidden' name='nombreDocu' value='Resumen General R21'>
				<input type="submit" value="Crear PDF" autofocus >
				<input type="button" value="Cancelar" onclick="cancelar_pdf()">
				
				</center>
				</form>
			</div>
<!--GENERA PDF*************************************************-->
<!-- MAIL -->
			<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;">
			<div 
				id="divmsg"
				style="
					opacity:0.8;
					position:relative;
					background-color:#000;
					color:white;
					padding: 20px;
					-webkit-border-radius: 20px;
    				border-radius: 10px;
					left:-50%;
					top:-30%
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
<?php }?>
</body>

</html>