<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/style.css">
<script language='javascript'>
	$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();
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
	background-color:#f6f7f8;
}

.derecha
{
	text-align: center;
}

@media print
{
	#imprimir,#filtros,#excel
	{
		display:none;
	}
}
</style>
<div class='iconos'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=conciliacion_IVA_contable_fiscal&f=filtro' id='filtros' onclick='$("#nmloader_div",window.parent.document).show();'><img src="../../netwarelog/repolog/img/filtros.png" border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>

<div class="repTitulo">Conciliación de de IVA fiscal y contable</div>	

	<input type='hidden' value='Conciliacion Iva Contable y Fiscal' id='titulo'>
	<div id='imprimible'>
	<table width='100%'>
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
		<tr style="text-align:center;font-size:12px;">
			<?php
			echo $fecha;
			?>
		</tr>
	</table>
	<table border='0' style='width:100%;max-width:900px;font-size:10px;' align="center" cellpadding=3 id='trasladado'>
	<tr class='' style="background-color:#edeff1;text-align:center;font-size:10px;font-weight:bold;height:30px">
		<td width='9%'>Fecha Poliza</td>
		<td width='7%'>Tipo Poliza</td>
		<td width='8%'>Periodo / Poliza</td>
		<td width='9%'>Ejercicio</td>
		<td width='9%'> Periodo</td>
		<td width='18%'>Concepto</td>
		<td width='10%'>IVA Contable</td>
		<td width='10%'>IVA Fiscal</td>
		<td width='10%'>IVA Pagado No Acreditable</td>
		<td width='10%'>Diferencia</td>
	</tr>
	<?php
	if($trasladado)
	{
		$totalImporteContable = 0;
		$totalTotalFiscal = 0;
		echo "<tr><td colspan='10' style='text-align:left;background-color:#f6f7f8;height:30px;'><b>IVA Trasladado</b></td></tr>";
		while($dt = $datosTrasladado->fetch_object())
		{
			if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
			{
    			$color='#ffffff';
			}
			else//Si es impar pinta esto
			{
    			$color='#fafafaf';
			}


			echo "<tr id='dt' style='background-color:$color;'>
					<td>$dt->fecha</td>
					<td>$dt->TipoPoliza</td>
					<td>$dt->idperiodo/$dt->numpol</td>
					<td>$dt->Ejercicio</td>
					<td>$dt->idperiodo</td>
					<td>$dt->concepto</td>
					<td style='text-align:right;'>".number_format($dt->ImporteContable,2)."</td>
					<td style='text-align:right;'>".number_format($dt->TotalFiscal,2)."</td>
					<td style='text-align:right;'>".number_format($dt->IvaNoAcreditable,2)."</td>";

					$dif = $dt->ImporteContable-$dt->TotalFiscal;
					if($dif<-0.01)
					{
						$color="color:red";
					}
				echo "<td style='$color;text-align:right;'>".number_format($dif,2)."</td>
					</tr>";

					$totalImporteContable += $dt->ImporteContable;
					$totalTotalFiscal += $dt->TotalFiscal;
			$cont++;//Incrementa contador


		}
		echo "<tr style='text-align:left;font-weight:bold;background-color:#f6f7f8;height:30px;' tipo='subtotalDT'><td colspan='6'></td><td style='text-align:right;'>$".number_format($totalImporteContable,2)."</td><td style='text-align:right;'>$".number_format($totalTotalFiscal,2)."</td><td colspan='2'></td></tr>";
	}

	if($acreditable)
	{
		$totalImporteContable = 0;
		$totalImporteFiscal = 0;
		echo "<tr style='background-color:#f6f7f8;height:30px;text-align:left;'><td colspan='10'><b>IVA Acreditable</b></td></tr>";
		while($da = $datosAcreditado->fetch_object())
		{
			if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
			{
    			$color='#ffffff';
			}
			else//Si es impar pinta esto
			{
    			$color='#fafafa';
			}


			echo "<tr id='da' style='background-color:$color'>
					<td>$da->fecha</td>
					<td>$da->TipoPoliza</td>
					<td>$da->idperiodo/$da->numpol</td>
					<td>$da->Ejercicio</td>
					<td>$da->idperiodo</td>
					<td>$da->concepto</td>
					<td style='text-align:right;'>".number_format($da->ImporteContable,2)."</td>
					<td style='text-align:right;'>".number_format($da->ImporteFiscal,2)."</td>
					<td style='text-align:right;'>".number_format($da->IvaNoAcreditable,2)."</td>";

					$dif = $da->ImporteFiscal-$da->ImporteContable;
					if($dif<-0.01)
					{
						$color="color:red";
					}
				echo "<td style='$color;text-align:right;'>".number_format($dif,2)."</td>
					</tr>";
			$totalImporteContable += $da->ImporteContable;
			$totalImporteFiscal += $da->ImporteFiscal;
			$cont++;//Incrementa contador


		}
		echo "<tr style='text-align:left;font-weight:bold;background-color:#f6f7f8;' tipo='subtotalDA'><td colspan='6'></td><td style='text-align:right;'>$".number_format($totalImporteContable,2)."</td><td style='text-align:right;'>$".number_format($totalImporteFiscal,2)."</td><td colspan='2'></td></tr>";
	}
	if($trasladado)
	{
		$totalImporteContable = 0;
		$totalTotalFiscal = 0;
		echo "<tr style='color:white;background-color#f6f7f8;height:30px;text-align:left;'><td colspan='10'><b>IVA Trasladado Sin IVA</b></td></tr>";
		while($dtn = $datosTrasladadoNo->fetch_object())
		{
			if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
			{
    			$color='#ffffff';
			}
			else//Si es impar pinta esto
			{
    			$color='#fafafa';
			}


			echo "<tr id='dtn' style='background-color:$color'>
					<td>$dtn->fecha</td>
					<td>$dtn->TipoPoliza</td>
					<td>$dtn->idperiodo/$dtn->numpol</td>
					<td>$dtn->Ejercicio</td>
					<td>$dtn->idperiodo</td>
					<td>$dtn->concepto</td>
					<td style='text-align:right;'>".number_format($dtn->ImporteContable,2)."</td>
					<td style='text-align:right;'>".number_format($dtn->TotalFiscal,2)."</td>
					<td style='text-align:right;'>".number_format($dtn->IvaNoAcreditable,2)."</td>";

					$dif = $dtn->ImporteContable-$dtn->TotalFiscal;
					if($dif<-0.01)
					{
						$color="color:red";
					}
				echo "<td style='$color;text-align:right;'>".number_format($dif,2)."</td>
					</tr>";

			$totalImporteContable += $dtn->ImporteContable;
			$totalTotalFiscal +=  $dtn->TotalFiscal;
			$cont++;//Incrementa contador


		}
		echo "<tr style='text-align:left;font-weight:bold;background-color:#edeff1;' tipo='subtotalDTN'><td colspan='6'></td><td style='text-align:right;'>$".number_format($totalImporteContable,2)."</td><td style='text-align:right;'>$".number_format($totalTotalFiscal,2)."</td><td colspan='2'></td></tr>";
	}
	if($acreditable)
	{
		$totalImporteContable = 0;
		$totalImporteFiscal = 0;
		echo "<tr style='background-color:#f6f7f8;height:30px;text-align:left;'><td colspan='10'><b>IVA Acreditable Sin IVA</b></td></tr>";
		while($dan = $datosAcreditadoNo->fetch_object())
		{
			if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
			{
    			$color='#ffffff';
			}
			else//Si es impar pinta esto
			{
    			$color='#fafafa';
			}


			echo "<tr id='dan' style='background-color:$color'>
					<td>$dan->fecha</td>
					<td>$dan->TipoPoliza</td>
					<td>$dan->idperiodo/$dan->numpol</td>
					<td>$dan->Ejercicio</td>
					<td>$dan->idperiodo</td>
					<td>$dan->concepto</td>
					<td style='text-align:right;'>".number_format($dan->ImporteContable,2)."</td>
					<td style='text-align:right;'>".number_format($dan->ImporteFiscal,2)."</td>
					<td style='text-align:right;'>".number_format($dan->IvaNoAcreditable,2)."</td>";

					$dif = $dan->ImporteFiscal-$dan->ImporteContable;
					if($dif<-0.01)
					{
						$color="color:red";
					}
				echo "<td style='background-color:$color;text-align:right;'>".number_format($dif,2)."</td>
					</tr>";

			$totalImporteContable += $dan->ImporteContable;
			$totalImporteFiscal += $dan->ImporteFiscal;
			$cont++;//Incrementa contador


		}
		echo "<tr style='text-align:left;font-weight:bold;background-color:#f6f7f8;' tipo='subtotalDAN'><td colspan='6'></td><td style='text-align:right;'>$".number_format($totalImporteContable,2)."</td><td style='text-align:right;'>$".number_format($totalImporteFiscal,2)."</td><td colspan='2'></td></tr>";
	}
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
			<script>
				function cerrarloading(){
					$("#loading").fadeOut(0);
					var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
					$("#divmsg").html(divloading);
				}
			</script>