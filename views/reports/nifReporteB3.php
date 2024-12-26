<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function()
{
	$('#nmloader_div',window.parent.document).hide();
	

});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#repTitulo').val()});
			}		
</script>
<script language='javascript' src='js/pdfmail.js'></script>
<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
<link rel="stylesheet" href="css/style.css" type="text/css">
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
$titulo1="font-size:11px;background-color:#edeff1;font-weight:bold;height:30px;";
$subtitulo="font-size:10px;font-weight:bold;height:30px;background-color:#fafafa;text-align:left;"

?>

<div class='iconos' style='margin-left:10px;margin-bottom:10px;'>
	<a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a>
	<a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   
	<a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=reports&f=balanceGeneral&tipo=4' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>
<div class="repTitulo">Estado de Resultado Integral</div>	

<input type='hidden' value='Estado de resultado Integral.' id='titulo'>
<div id='imprimible'>

	<table style='width:100%;max-width:800px;text-align:left;' align="center">
		<tr>
			<td width="50%"><?php
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php // echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'></td-->
			<td valign="top" width='50%' style="font-size:7px;text-align:right;color:gray;">
			<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
			</td>
		</tr>
		<tr style="font-color:#576370;text-align:center;">
			<td colspan='2'>
				<b style="font-size:18px;"><?php echo $empresa; ?></b><br>
				<b style="font-size:15px;">Estado de Resultado Integral</b><br>
				Ejercicio <b><?php echo $ej; ?></b> | Periodo <b> <?php echo $periodo; ?> </b><br>
				Sucursal <b><?php echo $nomSucursal; ?></b> Segmento <b><?php echo $nomSegmento; ?></b> <br><br>
			</td>
		</tr>
	<table>
	<!--Titulo congelado-->
	<!--INICIA-->
<table border=0 align="center" style='max-width:800px;width:100%;font-size:9px;text-align:left;'>
	<thead>
	<tr style='<?php echo "$titulo1"; ?>;text-transform: uppercase;text-align:center;'>
		<td style='min-width:270px;width:50%;'>Clasificacion</td>
		<td style='min-width:170px;width:25%;'><?php echo $periodo;?></td>
		<td style='min-width:170px;width:25%;'><?php echo $periodoAnterior; ?></td>
	</tr>
	</thead>
	<?php
	$bloque=0;
	$totalGrupo=0;
	while($d = $datos->fetch_object())
	{

		$NIF = explode('/', $d->NIF);
		if(intval($NIF[0]) > intval($bloque) && $bloque != 0) 
		{
			echo "<tr style='height:30px;background-color:#f6f7f8;font-weight:bold;'><td>$titulo</td><td style='text-align:right;'>$ ".number_format($totalGrupo,2)."</td><td style='text-align:right;'>$ ".number_format($totalGrupoAnterior,2)."</td></tr>";
		}

		$d->CargosAbonos = $d->CargosAbonos * -1;
		$d->CargosAbonosAntes = $d->CargosAbonosAntes * -1;
		
		echo "<tr class='$d->Clasificacion' numero='".$NIF[0]."'><td>".$NIF[1]."</td><td class='resCA' cantidad='$d->CargosAbonos' style='text-align:right;'>$ ".number_format($d->CargosAbonos,2)."</td><td class='resCAA' cantidad='$d->CargosAbonosAntes' style='text-align:right;'>$ ".number_format($d->CargosAbonosAntes,2)."</td></tr>";	
		$totalGrupo += $d->CargosAbonos;
		$totalGrupoAnterior += $d->CargosAbonosAntes;

		if(intval($NIF[0]) <= 76)
		{
			$titulo = 'Utilidad Bruta';
			$bloque = 76;
		}

		if(intval($NIF[0]) > 76 && intval($NIF[0]) <= 86)
		{
			$titulo = 'Utilidad de Operacion';
			$bloque = 86;
		}

		if(intval($NIF[0]) > 86 && intval($NIF[0]) <= 88)
		{
			$titulo = 'Utilidad Antes de Impuestos';
			$bloque = 88;
		}

		if(intval($NIF[0]) == 89)
		{
			$titulo = 'Utilidad de Operaciones Continuas';
			$bloque = 89;
		}

		if(intval($NIF[0]) == 90)
		{
			$titulo = 'Utilidad Neta';
			$bloque = 90;
		}

		if(intval($NIF[0]) > 90 && intval($NIF[0]) <= 94)
		{
			$titulo = 'Otros Resultados Integrales';
			$bloque = 94;
		}
		
		
	}
	if(intval($NIF[0]) <= 94)
	{
		echo "<tr style='$subtitulo'><td>$titulo</td><td style='text-align:right;'>$ ".number_format($totalGrupo,2)."</td><td style='text-align:right;'>$ ".number_format($totalGrupoAnterior,2)."</td></tr>";
	}
	echo "<tr style='$titulo1;height:30px;'><td style='text-align:left;'>RESULTADO INTEGRAL</td><td style='text-align:right;' id='tpcc'>$ ".number_format($totalGrupo,2)."</td><td style='text-align:right;' id='tpccAnterior'>$ ".number_format($totalGrupoAnterior,2)."</td></tr>";
	?>

</table>

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
				<!--form id="formpdf" action="../../../webapp/netwarelog/repolog/pdf.php" method="post" target="_blank" onsubmit="generar_pdf()"-->
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
