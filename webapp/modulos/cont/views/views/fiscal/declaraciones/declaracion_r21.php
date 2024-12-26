<?php
if($toexcel==1){
 header("Content-Type: application/vnd.ms-excel");
 header("Expires: 0");
 header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
 header("content-disposition: attachment;filename=DeclaracionR21.xls");
 }
?>
<html>
<head>
	<script language='javascript' src='js/pdfmail.js'></script>
		<script type="text/javascript" src="js/jquery.js"></script>
	<title>Declaración R21 IVA </title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<?php 
	$concepto_r21="text-align:left;width:35%;";
	$valor_r21="text-align:right;width:12%;";
	$esp_medio="width:6%;";
	$titulo_v="background-color:#edeff1;font-weight:bold;height:30px;"
	?>
	<style type="text/css">
		.titulo_r21{text-align: center; font: 23px arial; border: 0px}
		.sub{text-align: center; font: 16px arial; font-weight: bold; border-top: 2px solid; border-bottom: 2px solid;}
		
		.valor_r21{width: 75px; text-align: right; font: 13px arial; vertical-align: top;}
		.esp_medio{width: 30px;}
		.titulo_v{background-color: #4c4c4c;color: white;}
	</style>
	
<?php
	if($toexcel==0){//se muestra reporte en navegador
?>	
	<!--LINK href="../../netwarelog/catalog/css/view.css" title="estilo" rel="stylesheet" type="text/css" />	
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
	<link rel="stylesheet" href="css/style.css" type="text/css"> 		
	
<?php }?>
</head>	
<body >
	<div class="iconos">
		<a href="javascript:window.print();">
		<img class="nmwaicons" border="0" src="../../netwarelog/design/default/impresora.png">
		</a>
		<td width="16" align="right">
		 <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a> 
		</td>
		<td width="16" align="right">
		<a href="javascript:mail();">
		<img border="0" title="Enviar reporte por correo electrónico" src="../../../webapp/netwarelog/repolog/img/email.png">
		</a>
		</td>
		<td>
			<a id="filtros" onclick="" href="index.php?c=declaracionR21&f=filtro">
				<img border="0" title="Haga click aquí para cambiar los filtros..." src="../../netwarelog/repolog/img/filtros.png">
			</a>
		</td>
	</div>

	<div class="repTitulo">Declaración R21 de Impuesto al Valor Agregado</div>
	<div id='imprimible'>
	<table width="100%">
		<tr>
			<td width="50%">
				<?php
			$logo=$organizacion->logoempresa;
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'></td-->
			<td valign="top" width='50%' style="font-size:7px;text-align:right;color:gray;">
			<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
			</td>
		</tr>
		<tr style="color:#576370;text-align:center;">
			<td colspan=2>
				<b style="font-size:18px;color:black;"><?php echo $organizacion->nombreorganizacion; ?></b><br>
				<b style="font-size:15px;">Declaración R21</b><br>
				Ejercicio <b><?php echo $ejercicio->NombreEjercicio ?></b> Periodo <b> <?php echo $meses[$per_ini]; ?></b><br><br>
			</td>
		</tr>
	</table>

	<table border="0" cellpadding="3" align="center" class="table_r21 busqueda" style="width:100%;max-width:900px;font-size:10px;">
			<tr style="<?php echo $titulo_v; ?>"><td colspan="5" >Montos pagados</td></tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>" >Total de actos o actividades pagados a la tasa del 16% de IVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIva16->base,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Total de actos o actividades pagados a la tasa del 11% de IVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIva11->base,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de actos o actividades pagados en la importacion de bienes o servicios a la tasa del 16% de IVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalbaseimport16->base,2,'.',','); ?></td>  
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Total de los demas actos o actividades pagados a la tasa del 0% de IVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIva0->base,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de actos o actividades pagados en la importacion de bienes o servicios a la tasa del 11% de IVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalbaseimport11->base,2,'.',','); ?></td>  
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Total de los demas actos o actividades pagados por lo que no se pagara el IVA (Excentos) </td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIvaExcento->base,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr style="<?php echo $titulo_v; ?>"><td colspan="5" >Determinación del impuesto al valor agregado acreditable</td></tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de IVA de actos o actividades pagados a la tasa del 16%</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalTasaIvaAcr16->IVA,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Total de IVA correspondiente a actos o actividades gravados</td>
				<td style="<?php echo $valor_r21; ?>"><?php  echo number_format($sumagravados,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de IVA de actos o actividades pagados a la tasa del 11%</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalTasaIvaAcr11->IVA,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">IVA trasladado o pagado en la importación por adquisición de bienes distintos de las inversiones, adquisición de servicios o por el uso o goce temporal de bienes destinados exclusivamente para realizar actos o actividades por los que no se se está obligado al pago del impuesto</td>
 				<td style="<?php echo $valor_r21; ?>" id="gocetemporal"><?php echo number_format($arr['GastosExentos'],2,'.',','); ?></td> 
				</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de IVA de actos o actividades pagados en la importación de bienes y servicios a la tasa del 16%</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($ivaimport16->IVA,2,'.',','); ?></td>  
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">IVA trasladado o pagado en la importación de inversiones destinadas exclusivamente para realizar actos o actividades por los que que no se está obligado al pago del impuesto</td>
				<td style="<?php echo $valor_r21; ?>" id="importacioninversiones" ><?php echo number_format($arr['InvExentos'],2,'.',','); ?></td>  
				</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de IVA de actos o actividades pagados en la importación de bienes y servicios a la tasa del 11%</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($ivaimport11->IVA,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">IVA de bienes utilizados indistintamente para realizar actos o actividades gravados y actos o actividades por los que no se está obligado al pago del impuesto</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($ivabienesutilizados,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Proporción utilizada conforme al artículo 5-B de la LIVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php if($prop_select==2){echo number_format($prop,4,'.',',');} else{echo number_format(0,4,'.',',');} ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Proporción utilizada conforme al artículo 5 de la LIVA</td>
				<td style="<?php echo $valor_r21; ?>"><?php if($prop_select==1){echo number_format($prop,4,'.',',');} else{echo number_format(0,4,'.',',');} ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de IVA trasladado al contribuyente (Efectivamente pagado)</td>
				
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($efectivamentepagado,2,'.',',');  ?></td>				
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">IVA acreditable de bienes utilizados indistintamente para realizar actos o actividades gravados y actos o actividades por los que no se está obligado al pago del impuesto</td>
				<td style="<?php echo $valor_r21; ?>" id="multipliart5"><?php echo number_format($multipliart5,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">IVA trasladado por adquisición de bienes distintos de las inversiones, adquisición de servicios o por el uso o goce temporal de bienes que se utilizan exclusivamente para realizar actos o actividades gravados</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($arr['GastosGravadosNacional'],2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">IVA acreditable</td>
				<td style="<?php echo $valor_r21; ?>" id="ivaacreditable"><?php echo number_format($ivaacreditable,2,'.',','); ?></td>

			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">IVA pagado en la importación por adquisición de bienes distintos de las inversiones, adquisición de servicios o por el uso o goce temporal de bienes que se utilizan exclusivamente para realizar actos o actividades gravados</td>
				<td style="<?php echo $valor_r21; ?>" id="ivaimporgosetemp"><?php echo number_format($arr['GastosGravadosExtrangeros'],2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Monto acreditable actualizado a incrementar derivado del ajuste</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($montoAjuste,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">IVA trasladado por la adquisición de inversiones destinadas exclusivamente para realizar actos o actividades gravados</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($arr['InvGravadosNacional'],2,'.',','); ?></td> <!-- en este se debe verificar q si tiene iva pagado no acreditable se reste con importe iva y no el importe base  y ese sera el new import bas -->
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Total IVA acreditable de periodo</td>
				<td style="<?php echo $valor_r21; ?>" id="totalacreditable" ><?php echo number_format($totalacreditable,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila"><td style="<?php echo $concepto_r21; ?>">IVA pagado por la importación de inversiones destinadas exclusivamente para realizar actos o actividades gravados</td>
				<td style="<?php echo $valor_r21; ?>" id="tipoiva"><?php echo number_format($arr['InvGravadosExtrangeros'],2,'.',','); ?></td> 
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>"></td>
				<td style="<?php echo $valor_r21; ?>"></td></tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>

			<tr style="<?php echo $titulo_v; ?>"><td colspan="5" >Determinación del Impuesto al Valor Agregado</td></tr>
			
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Valor de los actos o actividades gravados a la tasa del 16%</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIvaImp16,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Otras cantidades a cargo del contribuyente</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($otrasCargo,2,'.',',') ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Valor de los actos o actividades gravados a la tasa del 11%</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIvaImp11,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Otras cantidades a favor del contribuyente</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($otrasFavor,2,'.',',') ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Valor de los actos o actividades gravados a la tasa del 0% exportación</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIvacausa0,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Cantidad a cargo</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($cargo,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Valor de los actos o actividades gravados a la tasa del 0% otros</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIvacausaotros,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Saldo a favor</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($favor,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Suma de los actos o actividades gravados</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($sumaactosgravados,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Devolución inmediata obtenida</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($devolucionObtenida,2,'.',',') ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Acreditamientos del saldo a favor de periodos anteriores (Sin exceder de la cantidad a cargo)</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($acredAnteriores,2,'.',',') ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Saldo a favor del periodo</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($saldofavorperiodo,2,'.',',') ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Valor de los actos o actividades por los que no se deba pagar del impuesto (Exentos)</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($totalBaseIvacausaExenta,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Diferencia a cargo</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($diferenciacargo,2,'.',',') ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Impuesto causado</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($impuestocausado,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">IEPS acreditable de alcohol, alcohol desnaturalizado y mieles incristalizable de productos distintos de bebidas alcoholicas</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($iepsAcred,2,'.',',') ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Cantidad actualizada a reintegrarse derivada del ajuste</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($cantidadReintegrarse,2,'.',',') ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Impuesto a cargo</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($impuestocargoresult,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">IVA retenido al contribuyente</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($retenidocontri,2,'.',','); ?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>">Remanente de saldo a favor IEPS acreditable de alcohol, alcohol desnaturalizado y mieles incristalizable de productos distintos de bebidas alcohólicas</td>
				<td style="<?php echo $valor_r21; ?>"><?php echo number_format($remateieps,2,'.',','); ?></td>
			</tr>
			<tr class="busqueda_fila">
				<td colspan="5">&nbsp;</td>
			</tr>
			<tr class="busqueda_fila">
				<td style="<?php echo $concepto_r21; ?>">Total de IVA acreditable</td>
				<td style="<?php echo $valor_r21; ?>" ><?php echo number_format($totalacreditable,2,'.',',');?></td>
				<td style="<?php echo $esp_medio; ?>"></td>
				<td style="<?php echo $concepto_r21; ?>"></td>
				<td style="<?php echo $valor_r21; ?>"></td>
			</tr>

			<tfoot><tr><th colspan=5> Reporte R21</th></tr></tfoot>
	</table>
</div>
	<br></br>

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
				<input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
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