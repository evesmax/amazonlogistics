<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
<link rel="stylesheet" href="css/style.css" type="text/css">
<script language='javascript'>
	$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();

	//Agregar este proceso para igualar los width de las columnas de la tabla
	//INICIA
	for(var i=1;i<=13;i++)
	{
		if(parseInt($("#t"+i).width()) > parseInt($(".c"+i).width()))
		{
			$(".c"+i).width($("#t"+i).width()).attr('tamano',$("#t"+i).width())	
		}

		if(parseInt($(".c"+i).width()) > parseInt($("#t"+i).width()))
		{
			$("#t"+i).width($(".c"+i).width()).attr('tamano',$(".c"+i).width())	
		}
	}
	//TERMINA


	});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}
</script>
<script language='javascript' src='js/pdfmail.js'></script>
<style>
.tit_tabla_buscar td
{
	font-size:12px;
}

.razon_social
{
	background-color:gray;
	color:white;
}

.derecha
{
	text-align: center;
}

.right
{
	text-align: right;
}

/*INICIA Mejora al titulo estatico*************/
/*
Paso 1: Agregar este CSS
Paso 2: quitar clases del netwar a las tablas
Paso 3: Agregar el jquery
Paso 4: agregar los ids y las clases al tbody y tds
Paso 5: Quitar width a la tabla
Paso 6: agregar los styles
.tb {
    height: 400px;
    overflow: auto;
    
}

td
{
	padding: 4px;
}

*/

thead, tbody{
    display:block;}
/*TERMINA Mejora al titulo estatico*************/
@media print
{
	#imprimir,#filtros,#excel
	{
		display:none;
	}
}
</style>

<div class='iconos'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a><a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a> <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=auxiliar_impuestos&f=filtro' id='filtros' onclick='$("#nmloader_div",window.parent.document).show();'><img src="../../netwarelog/repolog/img/filtros.png" border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>

<div class="repTitulo">Auxiliar de Impuestos (Ingresos)</div>	

<input type='hidden' value='Auxiliar de Impuestos (Ingresos)' id='titulo'>
<div id='imprimible'>

	<table style="width:100%;max-width:1000px;" align="center">
		<tr>
			<td width='50%'>
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
		<tr style="color:#576370;text-align:center;">
			<?php
			echo $fecha_reporte;
			?>
		</tr>
	</table>

	<table border='0' style='width:100%;max-width:1000px;font-size:9px;' align="center" cellpadding=3 id='ingresos'>
		
	<tr style='background-color:#edeff1;font-weight:bold;font-size:9px;'>
		<td width='5%' id='t1'>Fecha Poliza</td>
		<td width='5%' id='t2'>Tipo Poliza</td>
		<td width='5%' id='t3'>Poliza</td>
		<td width='10%' id='t4'>Concepto</td>
		<td width='5%' id='t5'>Ejercicio</td>
		<td width='5%' id='t6'> Periodo</td>
		<td width='5%' id='t7'>Tasa IVA</td>
		<td width='10%' id='t8' style='text-align:right;'>Actos o Actividades Gravados (Base del IVA)</td>
		<td width='10%' id='t9' style='text-align:right;'>Importe de IVA</td>
		<td width='10%' id='t10' style='text-align:right;'>IVA Retenido</td>
		<td width='10%' id='t11' style='text-align:right;'>IVA Causado</td>
		<td width='10%' id='t12' style='text-align:right;'>ISR Retenido</td>
		<td width='10%' id='t13' style='text-align:right;'>Otros</td>
	</tr>
	
	
	<?php
		while($i = $ingresos->fetch_object())
		{
			$ivaRetenido=0;
			$isrRetenido=0;
			if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
			{
    			$color='#ffffff';
			}
			else//Si es impar pinta esto
			{
    			$color='#fafafa';
			}

			if(floatval($i->ivaRetenido) != 0)
			{
				$ivaRetenido = floatval($i->ivaRetenido)*(-1);
			}
			if(floatval($i->isrRetenido) != 0)
			{
				$isrRetenido = floatval($i->isrRetenido)*(-1);
			}
			echo "<tr id='e' style='background-color:$color'>
					<td class='c1'>$i->fecha</td>
					<td class='c2'>$i->TipoPoliza</td>
					<td class='c3'><center>$i->idperiodo/$i->numpol</center></td>
					<td class='c4'>$i->concepto</td>
					<td class='c5'>$i->Ejercicio</td>
					<td class='c6'>$i->Periodo</td>
					<td class='c7'>$i->Tasa</td>
					<td class='c8' style='text-align:right;'>".number_format($i->ImporteBase,2)."</td>
					<td class='c9' style='text-align:right;'>".number_format($i->ImporteIVA,2)."</td>
					<td class='c10' style='text-align:right;'>".number_format($ivaRetenido,2)."</td>
					<td class='c11' style='text-align:right;'>".number_format($i->causado,2)."</td>
					<td class='c12' style='text-align:right;'>".number_format($isrRetenido,2)."</td>
					<td class='c13' style='text-align:right;'>".number_format($e->otros,2)."</td>";


					
					$ImporteBase += $i->ImporteBase;
					$ImporteIVA += $i->ImporteIVA;
					$ivaRetenidoT += $ivaRetenido;
					$causado += $i->causado;
					$isrRetenidoT += $isrRetenido;
					$otros += $i->otros;
					
			$cont++;//Incrementa contador


		}
		echo "<tr style='text-align:right;font-weight:bold;background-color:#edeff1;' tipo='subtotalDT'><td colspan='7' style='text-align:left;'>Totales: </td><td>$".number_format($ImporteBase,2)."</td><td>$".number_format($ImporteIVA,2)."</td><td>$".number_format($ivaRetenidoT,2)."</td><td>$".number_format($causado,2)."</td><td>$".number_format($isrRetenidoT,2)."</td><td>$".number_format($otros,2)."</td></tr>";
	?>

</table>

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
				<input type='hidden' name='nombreDocu' value='Estado de Resultados'>
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
			<script language='javascript'>
				function cerrarloading(){
					$("#loading").fadeOut(0);
					var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
					$("#divmsg").html(divloading);
				}
			</script>