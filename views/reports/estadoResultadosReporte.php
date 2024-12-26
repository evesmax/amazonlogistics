<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function()
{
	$('#nmloader_div',window.parent.document).hide();
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};
	var ingresos = $("#sumG-INGRESOS").attr('cantidad')
	if(isNaN(ingresos)) ingresos = 0
	var egresos = $("#sumG-EGRESOS").attr('cantidad')
	if(isNaN(egresos)) egresos = 0
	var total = parseFloat(ingresos) - parseFloat(egresos)
	$("#sum-Resultados").html('$ '+total.format())
	$("#sum-Resultados").attr('cantidad',total)
	if(total < 0) $("#sum-Resultados").css('color','red');

	ingresos = $("#sumGM-INGRESOS").attr('cantidad')
	if(isNaN(ingresos)) ingresos = 0
	egresos = $("#sumGM-EGRESOS").attr('cantidad')
	if(isNaN(egresos)) egresos = 0
	total = parseFloat(ingresos) - parseFloat(egresos)
	$("#sumM-Resultados").html('$ '+total.format())
	$("#sumM-Resultados").attr('cantidad',total)
	if(total < 0) $("#sumM-Resultados").css('color','red');
	
	
	//PORCENTAJES EN COMPARACION CON EL INGRESO POR MES
	var cantidad=0;
	ingresos = $("#sumGM-INGRESOS").attr('cantidad');
	egresos = $("#sumGM-EGRESOS").attr('cantidad');
	$(".mes-INGRESOS").each(function(index)
	{
		cantidad = parseFloat($(this).attr('cantidad'))

		total = cantidad / ingresos * 100
		if(isNaN(total))
		{
			total = 0
		}
		$(this).after("<td style='text-align:right;'>"+total.format()+"%</td>")

	});

	cantidad=0;
	$(".mes-EGRESOS").each(function(index)
	{
		cantidad = parseFloat($(this).attr('cantidad'))
		total = cantidad / ingresos * 100
		if(isNaN(total))
		{
			total = 0
		}
		$(this).after("<td style='text-align:right;'>"+total.format()+"%</td>")

	});

	cantidad = 0
	cantidad = $("#sumGM-EGRESOS").attr('cantidad')
	total = cantidad / ingresos * 100
	if(isNaN(total))
		{
			total = 0
		}
	$("#sumGM-EGRESOS").after("<td style='text-align:right;'>"+total.format()+"%</td>")

	cantidad = 0
	cantidad = $("#sumM-Resultados").attr('cantidad')
	total = cantidad / ingresos * 100
	if(isNaN(total))
		{
			total = 0
		}
	$("#sumM-Resultados").after("<td style='text-align:right;'>"+total.format()+"%</td>")


	//PORCENTAJES EN COMPARACION CON EL INGRESO POR ACUMULADO
	cantidad=0;
	ingresos = $("#sumG-INGRESOS").attr('cantidad');
	egresos = $("#sumG-EGRESOS").attr('cantidad');
	$(".acum-INGRESOS").each(function(index)
	{
		cantidad = $(this).attr('cantidad')
		total = cantidad / ingresos * 100
		if(isNaN(total))
		{
			total = 0
		}
		$(this).after("<td style='text-align:right;'>"+total.format()+"%</td>")

	});

	cantidad=0;
	$(".acum-EGRESOS").each(function(index)
	{
		cantidad = $(this).attr('cantidad')
		total = cantidad / ingresos * 100
		if(isNaN(total))
		{
			total = 0
		}
		$(this).after("<td style='text-align:right;'>"+total.format()+"%</td>")

	});

	cantidad = 0
	cantidad = $("#sumG-EGRESOS").attr('cantidad')
	total = cantidad / ingresos * 100
	if(isNaN(total))
		{
			total = 0
		}
	$("#sumG-EGRESOS").after("<td style='text-align:right;'>"+total.format()+"%</td>")

	cantidad = 0
	cantidad = $("#sum-Resultados").attr('cantidad')
	total = cantidad / ingresos * 100
	if(isNaN(total))
		{
			total = 0
		}
	$("#sum-Resultados").after("<td style='text-align:right;'>"+total.format()+"%</td>")

	$('.clasif-Clasificacion').remove()
	$(".clasif-Activo:contains('TOTAL GRUPO')").remove()
	$("tr[numero='0']").remove()

	var prc;
	var prcAntes;
	for(numerillo=0;numerillo<=100;numerillo++)
	{
		
		prcAntes = '';
		prc = prc2 = 0;

		$("tr[nmtr='"+numerillo+"']").each(function(index)
		{
			prcAntes = $("td:nth-child(4)",this).html();
			prcAntes = prcAntes.replace("%","")
			prc += parseFloat(prcAntes)

			prcAntes = $("td:nth-child(6)",this).html();
			prcAntes = prcAntes.replace("%","")
			prc2 += parseFloat(prcAntes)
		});
		$("tr[numero='"+numerillo+"'] td:nth-child(4)").html(prc.format()+"%")
		$("tr[numero='"+numerillo+"'] td:nth-child(6)").html(prc2.format()+"%")
		//alert(prc)
	}

});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}	

</script>
<script language='javascript' src='js/pdfmail.js'></script>
<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
<link href="css/style.css" rel="stylesheet" type="text/css" />
<style>
.tit_tabla_buscar td
{
	font-size:medium;
}

#logo_empresa /*Logo en pdf*/
	{
		display:none;
	}

@media print
{
	#imprimir,#filtros,#excel,#email_icon
	{
		display:none;
	}

	#logo_empresa
	{
		display:block;
	}
}
</style>

<?php 
$moneda=$_POST['moneda'];
if($_POST['valMon']){$valMon=$_POST['valMon'];}else{$valMon=1;}
//$valMon=13.5;
$titulo1="font-size:10px;background-color:#f6f7f8;font-weight:bold;height:30px;";
$subtitulo="font-size:9px;font-weight:bold;height:30px;background-color:#fafafa;text-align:left;margin-left:10px;"

?>

<div class='iconos'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=reports&f=balanceGeneral&tipo=0' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>

<div class="repTitulo">Estado de Resultados</div>	



<input type='hidden' value='Estado de Resultados.' id='titulo'>
	

<div id='imprimible'>

	
	<!--Titulo congelado-->
<!--INICIA-->

<table style='width:100%' align="center">
	<tr><td width='50%'>

			<?php
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'-->
			</td>
			<td valign="top" width='50%' style="font-size:7px;text-align:right;color:gray;">
		<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
	</td>
	</tr>
	<tr style="text-align:center;color:#576370;">
		<td colspan="2">
			<b style='font-size:18px;color:#000000;'><?php echo $empresa; ?></b> <br>
			<b style='font-size:15px;'>Estado de Resultados </b> <br> 
			Ejercicio <b><?php echo $ej; ?></b>  Periodo  <b><?php echo $periodo; ?></b><br>
			Sucursal <b><?php echo $nomSucursal; ?></b> Segmento <b><?php echo $nomSegmento; ?></b> 
			<?php if($valMon>1){echo "<br>Moneda <b>$moneda</b> Tipo de Cambio $ <b>$valMon</b>";}?>
			<br><br>
		</td>
	</tr>
</table>
	
<table align="center" valing="center" cellpadding="3" style='font-size:9px;width:100%;max-width:900px;' >
	<thead>
	<tr style='font-weight:bold;background-color:#edeff1;height:30px;' valign='center'>
		<td style='width:8%;min-width:90px;'>CLAVE</td>
		<td style='width:24%;min-width:230px;'>CUENTA <?php if(!intval($_POST['detalle'])) echo "DE MAYOR"; ?></td>
		<td style='width:18%;min-width:170px;'>CANTIDAD DEL MES</td>
		<td style='width:16%;min-width:90px;'>% DEL MES</td>
		<td style='width:18%;min-width:170px;'>CANTIDAD ACUMULADA</td>
		<td style='width:16%;min-width:90px;'>% ACUMULADO</td>
	</tr>
	</thead>

				
				<?php
				function startsWith($haystack, $needle) 
				{
				    // search backwards starting from haystack length characters from the end
				    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
				}
				$nn=0;
				$clasifAnterior='Clasificacion';//Almacena la clasificacion anterior
				$grupoAnterior='Grupo';
				$sumaCantidad = $sumaCantidadMes = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				$sumaCantidadGrupo = $sumaCantidadGrupoMes = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				while($d = $datos->fetch_object())
				{
					$CM = explode(' / ',$d->Cuenta_de_Mayor,2);
					$title = $d->Grupo;

					if(startsWith($d->Grupo,'RESULTADO ACRE') || startsWith($d->Grupo,'RESULTADOS ACRE'))
					{
						$d->Grupo = "INGRESOS";	
					}

					if(startsWith($d->Grupo,'RESULTADO DEUDOR') || startsWith($d->Grupo,'RESULTADOS DEUDOR'))
					{
						$d->Grupo = "EGRESOS";	
					}
					if($clasifAnterior != $d->Clasificacion)
					{
						if($grupoAnterior != $d->Grupo)
						{

							if(floatval($sumaCantidadGrupo) < 0) $red = "style='color:red;'";
							if(floatval($sumaCantidadGrupoMes) < 0) $redMes = "style='color:red;'";
							echo "<tr style='$subtitulo' class='clasif-$clasifAnterior'><td></td><td>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumGM-$grupoAnterior' $redMes cantidad='".number_format($sumaCantidadGrupoMes,2,'.','')."'>$ ".number_format($sumaCantidadGrupoMes,2)."</td><td>100%</td><td id='sumG-$grupoAnterior' $red cantidad='".number_format($sumaCantidadGrupo,2,'.','')."'>$ ".number_format($sumaCantidadGrupo,2)."</td><td>100%</td></tr>";
							$sumaCantidadGrupo = $sumaCantidadGrupoMes = 0;
						}


						//comienza cuenta de clasificacion
						$red = $redMes = '';
						if(floatval($sumaCantidad) < 0) $red = "style='color:red;'";
						if(floatval($sumaCantidadMes) < 0) $redMes = "style='color:red;'";
						echo "<tr style='font-weight:bold;height:30px;' class='clasif-$clasifAnterior'><td></td><td>TOTAL ".strtoupper($clasifAnterior)."</td><td id='sumM-$clasifAnterior' $redMes>$ ".number_format($sumaCantidadMes,2)."</td><td>100%</td><td id='sum-$clasifAnterior' $red>$ ".number_format($sumaCantidad,2)."</td><td>100%</td></tr>";
						$sumaCantidad = $sumaCantidadMes = 0;
						echo "<!--tr class='clasif-$clasifAnterior' style='font-weight:bold;height:3px;'><td colspan='6'></td></tr-->";	
						echo "<tr style='$titulo1' class='clasif-$d->Clasificacion'><td></td><td style='text-align:left;'>".strtoupper($d->Clasificacion)."</td><td style='text-align:right;'></td><td></td><td></td><td></td></tr>";	
						//termina cuenta de clasificacion
						
						if($grupoAnterior != $d->Grupo)
						{
							echo "<!--tr style='height:3px;'><td colspan='6' class='clasif-$d->Clasificacion'></td></tr-->";	
							echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td></td><td>".strtoupper($title)."</td><td></td><td></td><td></td><td></td></tr>";	
						}
						if($d->Cuenta_de_Mayor != $mayorAnterior AND intval($_POST['detalle']))
						{	
							echo "<tr numero='$nn' style='border-top:1px solid black;color:gray;font-weight:bold;text-align:right;' class='anterior'><td></td><td style='text-align:left;'>Total $mayorAnterior</td><td>$".number_format($sumaCantidadMayorMes,2)."</td><td></td><td>$".number_format($sumaCantidadMayor,2)."</td><td></td></tr>";
							echo "<tr style='color:gray;font-weight:bold;height:50px;text-align:left;'><td></td><td>$d->Codigo / ".$CM[1]."</td></tr>";
							$sumaCantidadMayor=0;
							$sumaCantidadMayorMes=0;
							$nn++;
						}

					}
					else
					{
						
						if($d->Cuenta_de_Mayor != $mayorAnterior AND intval($_POST['detalle']))
						{
							echo "<tr numero='$nn' style='border-top:1px solid black;color:gray;font-weight:bold;text-align:right;' class='anterior'><td></td><td style='text-align:left;'>Total $mayorAnterior</td><td>$".number_format($sumaCantidadMayorMes,2)."</td><td></td><td>$".number_format($sumaCantidadMayor,2)."</td><td></td></tr>";
						}
						if($grupoAnterior != $d->Grupo)
						{
							$red = $redMes ='';
							if(floatval($sumaCantidadGrupo) < 0) $red = "color:red";
							if(floatval($sumaCantidadGrupoMes) < 0) $redMes = "color:red";
							echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td></td><td>TOTAL ".strtoupper($titleAnterior)."</td><td id='sumGM-$grupoAnterior' style='text-align:right;$redMes' cantidad='".number_format($sumaCantidadGrupoMes,2,'.','')."'>$ ".number_format($sumaCantidadGrupoMes,2)."</td><td style='text-align:right;'>100%</td><td id='sumG-$grupoAnterior' style='text-align:right;$red' cantidad='".number_format($sumaCantidadGrupo,2,'.','')."'>$ ".number_format($sumaCantidadGrupo,2)."</td><td style='text-align:right;'>100%</td></tr>";
							$sumaCantidadGrupo = $sumaCantidadGrupoMes = 0;
							echo "<!--tr><td colspan='6' class='clasif-$d->Clasificacion' style='height:15px;'></td></tr-->";	
							echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td></td><td>".strtoupper($title)."</td><td></td>
							<td></td>
							<td></td>
							<td></td></tr>";	

						}
						if($d->Cuenta_de_Mayor != $mayorAnterior AND intval($_POST['detalle']))
						{
							echo "<tr style='color:gray;font-weight:bold;height:50px;text-align:left;'><td></td><td>$d->Codigo / ".$CM[1]."</td></tr>";
							$sumaCantidadMayor=0;
							$sumaCantidadMayorMes=0;
							$nn++;
						}
					}
					$red = $redMes = '';
					$ResultadosMes = $d->CargosAbonosMes;
					$ResultadosMes = $ResultadosMes/$valMon;
					$Resultados = $d->CargosAbonos;
					$Resultados = $Resultados/$valMon;
					if($d->Grupo == "INGRESOS")
					{
						$Resultados = $Resultados *-1;
						$ResultadosMes = $ResultadosMes *-1;
					}
					if(floatval($Resultados) < 0) $red = 'color:red;';
					if(floatval($ResultadosMes) < 0) $redMes = 'color:red;';
					if(!intval($_POST['detalle']))
					{
						$nc = $d->Codigo;
						$tc = $CM[1];
					} 
					else
					{
						 $nc = $d->CuentaAfectable;
						 $tc = $d->Cuenta;
					}
				
					echo "<tr class='clasif-$d->Clasificacion' nmtr='$nn'><td style='mso-number-format:\"@\";'>".$nc."</td><td style='text-align:left;'>".$tc."</td><td style='text-align:right;$redMes' class='mes-$d->Grupo' cantidad='".number_format($ResultadosMes,2,'.','')."'>$ ".number_format($ResultadosMes,2)."</td><td style='text-align:right;$red' class='acum-$d->Grupo' cantidad='".number_format($Resultados,2,'.','')."'>$ ".number_format($Resultados,2)."</td></tr>";
					$sumaCantidadGrupo 		+= $Resultados;
					$sumaCantidad 			+= $Resultados;
					$sumaCantidadMayor 		+= $Resultados;
					$sumaCantidadGrupoMes 	+= $ResultadosMes;
					$sumaCantidadMes 		+= $ResultadosMes;
					$sumaCantidadMayorMes 	+= $ResultadosMes;
					$clasifAnterior 		= $d->Clasificacion;
					$grupoAnterior 			= $d->Grupo;
					$mayorAnterior 			= $d->Cuenta_de_Mayor;
					$red = $redMes = '';
					$titleAnterior 			= $title;
				}
				
				if(floatval($sumaCantidadGrupo) < 0) $red = "color:red";
				if(floatval($sumaCantidadGrupoMes) < 0) $redMes = "color:red";
				echo "<tr numero='$nn' style='border-top:1px solid black;color:gray;font-weight:bold;text-align:right;' class='anterior'>
						<td></td><td style='text-align:left;'>Total $mayorAnterior</td>
						<td>$".number_format($sumaCantidadMayorMes,2)."</td>
						<td>$".number_format($sumaCantidadMayor,2)."</td>
					  </tr>";
				echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'>
						<td></td>
						<td>TOTAL ".strtoupper($titleAnterior)."</td>
						<td id='sumGM-$grupoAnterior' style='text-align:right;$redMes' cantidad='".number_format($sumaCantidadGrupoMes,2,'.','')."'>$ ".number_format($sumaCantidadGrupoMes,2)."</td>
						<td id='sumG-$grupoAnterior' style='text-align:right;$red' cantidad='".number_format($sumaCantidadGrupo,2,'.','')."'>$ ".number_format($sumaCantidadGrupo,2)."</td>
					  </tr>";

				if(floatval($sumaCantidad) < 0) $red = "color:red;";
				if(floatval($sumaCantidadMes) < 0) $redMes = "color:red;";
				echo "<tr style='$titulo1'><td>
						</td><td style='text-align:left;'>TOTAL RESULTADOS</td>
						<td id='sumM-$clasifAnterior' style='text-align:right;$redMes'></td>
						<td id='sum-$clasifAnterior' style='text-align:right;$red'></td>
					  </tr>";
				?>
			</table>
<input type='hidden' id='totalMayores' value='<?php echo $sumaCont; ?>'>
<!--INICIA TITULO CONGELADO-->


<!--TERMINA-->
</div>

<!--GENERA PDF*************************************************-->
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
				  z-index:1;
				">
				<form id="formpdf" action="libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
				<!--form id="formpdf" action="../../../webapp/netwarelog/repolog/pdf.php" method="post" target="_blank" onsubmit="generar_pdf()">-->
					<center>
					<b> Generar PDF </b>
					<br><br>

					<table style="border:none;z-index:1;">
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
				<input type='hidden' name='nombreDocu' value='Estado de Resultados'>
				<input type="submit" value="Crear PDF" autofocus >
				<input type="button" value="Cancelar" onclick="cancelar_pdf()">
				
				</center>
				</form>
			</div>
<!--GENERA PDF*************************************************-->
<!-- MAIL -->
			<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
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