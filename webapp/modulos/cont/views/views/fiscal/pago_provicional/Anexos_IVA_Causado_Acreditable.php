<?php
header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=anexo_iva_causado_acreditable.xls");
?>
<html>
<head>
	<title>Auxiliar Impuestos</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php
	if($toexcel==0){//se muestra reporte en navegador ?>
		<script language='javascript' src='js/pdfmail.js'></script>
		<script type="text/javascript" src="js/jquery.js"></script>
	<!--LINK href="../../netwarelog/catalog/css/view.css" title="estilo" rel="stylesheet" type="text/css" />	
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<?php //include('../../netwarelog/design/css.php');?>
<!--LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->

	<?php
	}
	?>
	<style type="text/css">
		.titulo_aux{text-align: center; font: 20px arial; border: 0px solid; }
		.totales{text-align: right; font: 11px arial; font-weight: bolder; border-top: 1px solid;}
		.cabecera{ font: 11px arial; font-weight: bolder;  vertical-align: bottom; border: 0px solid; text-align: center;}
		.total_texto{font: 11px arial; font-weight: bolder; vertical-align: top;}
		.espacio{height: 20px;}
		
		table { 
			    margin-right: auto;
				margin-left:auto;
				 }
		
	</style>
</head>
<script>
	$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();
	});
</script>
<body>
	<?php if($toexcel==0){ ?>
		<div class="iconos">
		<a href="javascript:window.print();">
		<img  border="0" src="../../netwarelog/design/default/impresora.png" width="20px">
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
					<img border="0" src="../../netwarelog/repolog/img/filtros.png" title="Haga click aquí para cambiar los filtros..." onclick="regreso()">
				</td>
				<td>
				<img src="images/images.jpg" title="Exportar a Excel" onclick="excelreport()" width="20px" height="20px"> 

				</td>
		</div>
		<div class="repTitulo">Anexos IVA causado y acreditable</div>
		<?php }?>
<div id="imprimible">
	
	<table border="0" cellpadding="3" style="width:100%;max-width:800px;font-size:11px;">
		<tr>
			<td>
				<?php
				$logo=$organizacion->logoempresa;
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'-->
			</td><td></td>
			<td valign="top" style="font-size:7px;text-align:right;color:gray;">
			<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
			</td>
		</tr>
		<tr style="text-align:center;color:#576370;">
			<td colspan=3><b style='font-size:18px;color:black;'><?php echo $organizacion->nombreorganizacion; ?></b><br>
				<b style='font-size:15px;'>Anexo IVA causado acreditable</b><br>
				<b style='font-size:12px;'>Periodo de Acreditamiento <?php echo $meses[$ini].' - '.$meses[$fin].'  '.$ejercicio->NombreEjercicio; ?></b><br><br>
			</td>
		</tr>

		<!--thead>
			<tr><td colspan="3" class="titulo_aux"></td></tr>
			<tr><td colspan="3" class="titulo_aux"></td></tr> 	
			<tr><td colspan="3" class="titulo_aux"></td></tr>
			
		</thead-->
		<tbody>
			
			<tr style="background-color:#edeff1;font-weight:bold;text-align:left;">
				<td width="50%">-- IVA CAUSADO --</td><td width="25%">&nbsp;</td><td width="25%">&nbsp;</td>
			</tr>
			<tr class="">
				<td class="" >Actos o Actividades Gravados al 16%</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format($arr["tasa16"]->baset,2,'.',','); ?></td>
			</tr>
			<tr class="">
				<td class="" >Actos o Actividades Gravados al 11%</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["tasa11"]->baset,2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Actos o Actividades Gravados al 0%</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["tasa0"]->baset,2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Actos o Actividades Exentos</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["tasaExenta"]->baset,2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Actos o Actividades en Otras Tasas</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["otrasTasas"]->baset,2,'.',','); ?></td>
				
			</tr>
			<tr style="background-color:#edeff1;">
				<td class="total_texto" >Suma Actos o Actividades Gravados</td><td class="" >&nbsp;</td><td class="totales" align="right"><?php echo number_format(@$arr["tasa16"]->baset+@$arr["tasa11"]->baset+@$arr["tasa0"]->baset+@$arr["tasaExenta"]->baset+@$arr["otrasTasas"]->baset,2,'.',','); ?></td>
			</tr>


			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>


			<tr>
				<td class="" >IVA Causado al 16%</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["tasa16"]->ivat,2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >IVA Causado al 11%</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["tasa11"]->ivat,2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >IVA Retenido</td><td class="" >&nbsp;</td><td class="" align="right"><?php  echo number_format(@$arr["ivaRetenido"],2,'.',','); ?></td>
				
			</tr>
			<tr style="background-color:#edeff1;">
				<td class="total_texto" >Total IVA Causado</td><td class="" >&nbsp;</td><td class="totales" align="right"><?php  $a=$arr["tasa16"]->ivat+$arr["tasa11"]->ivat+$arr["ivaRetenido"]; echo number_format($a,2,'.',',');?></td>
			</tr>

			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>

			<tr style="background-color:#edeff1;font-weight:bold;">
				<td >-- COMPRAS Y GASTOS --</td><td class="" >&nbsp;</td><td >&nbsp;</td>
			</tr>
			<tr>
				<td class="" >Compras y Gastos al 16%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$baseComprado["16%"],2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Compras y Gastos al 11%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$baseComprado["11%"],2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Compras y Gastos al 0%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$baseComprado["0%"],2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Compras y Gastos Exentos</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$baseComprado["Exenta"],2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >Compras y Gastos en Otras Tasas</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$baseComprado["Otra Tasa 1"]+@$baseComprado["Otra Tasa 2"],2,'.',','); ?></td>
				
			</tr>
			
			<tr style="background-color:#edeff1;">
				<td class="total_texto" >Total Compras y Gastos Gravados</td><td class="" >&nbsp;</td><td class="totales" align="right"><?php echo number_format(@$totalBaseComprado,2,'.',','); ?></td>
			</tr>

			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>
				
			<tr>
				<td class="" >IVA de Compras y Gastos al 16%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$ivaComprado["16%"],2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >IVA de Compras y Gastos al 11%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$ivaComprado["11%"],2,'.',','); ?></td>
			</tr>
			<tr>
				<td class="" >IVA de Compras y Gastos en Otras Tasas</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$ivaComprado["Otra Tasa 1"]+@$ivaComprado["Otra Tasa 2"],2,'.',','); ?></td>
			</tr>
			<?php if($acredita==0){ ?>
				
			
			<tr>
				<td class="" >IVA Retenido</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$baseComprado["ivaretenido"],2,'.',','); ?></td>
				
			</tr>
			<?php }else{ $baseComprado["ivaretenido"]=0;} ?>
			<tr style="background-color:#edeff1;">
				<td class="total_texto" >Total IVA de Compras y Gastos </td><td class="" >&nbsp;</td><td class="totales" align="right"><?php echo number_format(@$ivaComprado["16%"]+@$ivaComprado["11%"]+@$ivaComprado["Otra Tasa 1"]+@$ivaComprado["Otra Tasa 2"]+$baseComprado["ivaretenido"],2,'.',','); ?></td>
			</tr>

			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>


			<tr style="background-color:#edeff1;font-weight:bold;">
				<td >-- IVA PAGADO ACREDITABLE --</td><td class="" >&nbsp;</td><td >&nbsp;</td>
			</tr>
			<tr>
				<td class="" >Actos y Actividades Pagados al 16%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['16%']->baseacr,2,".",","); ?></td>
			</tr>
			<tr>
				<td class="" >Actos y Actividades Pagados al 11%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['11%']->baseacr,2,".",","); ?></td>
			</tr>
			<tr>
				<td class="" >Actos y Actividades Pagados al 0%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['0%']->baseacr,2,".",","); ?></td>
			</tr>
			<tr>
				<td class="" >Actos y Actividades Exentos</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['Exenta']->baseacr,2,".",","); ?></td>
			</tr>
			<tr style="background-color:#edeff1;">
				<td class="total_texto" >Total Actos y Actividades Pagados</td><td class="" >&nbsp;</td><td class="totales" align="right"><?php echo @number_format(@$arr['16%']->baseacr+@$arr['11%']->baseacr+@$arr['0%']->baseacr+@$arr['Exenta']->baseacr,2,'.',',');?></td>
			</tr>

			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>
				
			<tr>
				<td class="" >IVA de Actos y Actividades Pagados al 16%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['16%']->ivacredit,2,".",","); ?></td>
			</tr>
			<tr>
				<td class="" >IVA de Actos y Actividades Pagados al 11%</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['11%']->ivacredit,2,".",","); ?></td>
			</tr>
			<?php if($acredita==0){ ?>
			<tr>
				<td class="" >IVA Retenido</td><td class="" >&nbsp;</td><td class=""align="right" ><?php echo number_format(@$arr['retenido'],2,'.',',');?></td>
			</tr>
			<tr>
				<td class="" >IVA Acreditable Retenido de Meses Anteriores</td><td class="" >&nbsp;</td><td class="" align="right"><?php echo number_format(@$arr['mes'],2,'.',','); ?></td>
			</tr>
			<?php } else {$arr['retenido']=0; $arr['mes']=0;} ?>
			<tr style="background-color:#edeff1;">
				<td class="total_texto" >Total IVA Acreditable</td><td class="" >&nbsp;</td><td class="totales" align="right"><?php  $b=@$arr['16%']->ivacredit+@$arr['11%']->ivacredit-@$arr['retenido']+@$arr['mes']; echo  number_format($b,2,'.',','); ?></td>
			</tr>

			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>
			<tr>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
				<td class="espacio" ></td>
			</tr>

			<tr style="background-color:#edeff1;font-weight:bold;">
				<td >-- DETERMINACION DEL IVA --</td><td class="" >&nbsp;</td><td >&nbsp;</td>
			</tr>
			<tr>
				<?php $r=$a-$b;  ?>
				<td class="total_texto" >IVA a Cargo</td><td class="" >&nbsp;</td><td class="total_texto numero" align="right"><?php if($r>=0){ echo number_format($r,2,'.',',');}else{ echo "0.0000"; }?></td>
			</tr>
			<tr>
				<td class="total_texto" >IVA a Favor</td><td class="" >&nbsp;</td><td class="total_texto numero" align="right"><?php if($r<0){ echo number_format($r,2,'.',',');}else{ echo "0.0000"; }?></td>
			</tr>

		</tbody>
		<tfoot>
		</tfoot>
	</table>
</div>	
<?php if($toexcel==0){	?>
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