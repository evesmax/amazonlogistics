<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function()
{
	$('#nmloader_div',window.parent.document).hide();
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};

	$('.clasif-Clasificacion').remove()
	$(".clasif-Activo:contains('TOTAL GRUPO')").remove()

	var activo, pasivo, capital, resultados, total;
	activo = $("#sum1-Activo").attr('cantidad')
	if(isNaN(activo))
	{
		activo = 0;
	}
	pasivo = $("#sum1-Pasivo").attr('cantidad')
	if(isNaN(pasivo))
	{
		pasivo = 0;
	}
	capital = $("#sum1-Capital").attr('cantidad')
	if(isNaN(capital))
	{
		capital = 0;
	}
	resultados = $(".sum1-Resultados").attr('cantidad')
	if(isNaN(resultados))
	{
		resultados = 0;
	}
	total = parseFloat(activo) + parseFloat(pasivo) + parseFloat(capital) + parseFloat(resultados)

	$("#sumaTotal1").html("$ "+total.format())

	activo = $("#sum2-Activo").attr('cantidad')
	if(isNaN(activo))
	{
		activo = 0;
	}
	pasivo = $("#sum2-Pasivo").attr('cantidad')
	if(isNaN(pasivo))
	{
		pasivo = 0;
	}
	capital = $("#sum2-Capital").attr('cantidad')
	if(isNaN(capital))
	{
		capital = 0;
	}
	resultados = $(".sum2-Resultados").attr('cantidad')
	if(isNaN(resultados))
	{
		resultados = 0;
	}
	total = parseFloat(activo) + parseFloat(pasivo) + parseFloat(capital) + parseFloat(resultados)

	$("#sumaTotal2").html("$ "+total.format())

	$('#total-resultados2').html($('#total-resultados').html())
	$('#total-resultados2').css({'font-weight':'bold'})

	$('#total-resultados').remove()

	$(".clasif-Resultados").remove()
	$(".quitar").remove()

});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
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
$moneda=$_POST['moneda'];
if($_POST['valMon']){$valMon=$_POST['valMon'];}else{$valMon=1;}

$titulo1="font-size:10px;background-color:#f6f7f8;font-weight:bold;height:30px;";
$subtitulo="font-size:10px;font-weight:bold;height:30px;background-color:#fafafa;text-align:left;margin-left:10px;"

?>


<div class='iconos'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=reports&f=balanceGeneral&tipo=2' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>

<div class="repTitulo">Estado de Origen y Aplicacion de Recursos</div>	
<br />

<input type='hidden' value='Estado de Origen y aplicacion de producto.' id='titulo'>
<div id='imprimible'>
	
	<table style='width:100%;max-width:800px;' align="center" cellpadding="3">
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
				<b style="font-size:18px;color:black;"><?php echo $empresa; ?></b><br>
				<b style="font-size:15px;">Estado de Origen y Aplicación</b><br>
				Ejercicio <b><?php echo $ej; ?></b> Periodo <b><?php echo $periodo; ?></b><br> 
			    Sucursal <b><?php echo $nomSucursal; ?></b> Segmento <b><?php echo $nomSegmento; ?></b> 
			    <?php if($valMon>1){echo "<br>Moneda <b>$moneda</b> Tipo de Cambio $ <b>$valMon</b>";}?>
			    <br><br>
			</td>
		</tr>
	
	<tr><td colspan="2">
<!--Titulo congelado-->
<!--INICIA-->
<table border=0 style='width:100%;font-size:10px;' cellpadding="3">
	<thead>
	<tr style='<?php echo "$titulo1"; ?>'>
		<td style='min-width:120px;width:15%;'>No. DE CUENTA</td>
		<td style='min-width:220px;width:45%;'>DESCRIPCIÓN</td>
		<td style='min-width:150px;width:20%;'>ORIGEN</td>
		<td style='min-width:150px;width:20%;'>APLICACIÓN</td>
	</tr>
	<tr id='total-resultados2' style='<?php echo"$titulo1"; ?>;text-align:left;'></tr>
	
	</thead>			
				
				
				<?php
				//Carga los Pasivo, Capital y Resutados******************************************************************************
				$clasifAnterior='Clasificacion';//Almacena la clasificacion anterior
				//$grupoAnterior='Grupo';
				$sumaOrigen = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				//$sumaOrigenGrupo = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				$sumaApli = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				//$sumaApliGrupo = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				while($d = $datos->fetch_object())
				{
					$CM = explode(' / ',$d->Cuenta_de_Mayor,2);
					if($clasifAnterior != $d->Clasificacion)
					{
						/*if($grupoAnterior != $d->Grupo)
						{
							echo "<tr style='font-weight:bold;height:30px;' class='clasif-$clasifAnterior'><td colspan='2'>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior' style='text-align:right;'>$ ".number_format($sumaOrigenGrupo,2)."</td><td id='sumG-$grupoAnterior' style='text-align:right;$red'>$ ".number_format($sumaApliGrupo,2)."</td></tr>";
							$sumaOrigenGrupo = 0;
							$sumaApliGrupo = 0;
						}*/

						//comienza cuenta de clasificacion
						$red='';
						echo "<tr style='$subtitulo' class='clasif-$clasifAnterior'><td></td><td>TOTAL ".strtoupper($clasifAnterior)."</td><td id='sum1-$clasifAnterior' style='text-align:right;' cantidad='".number_format($sumaOrigen,2,'.','')."'>$ ".number_format($sumaOrigen,2)."</td><td id='sum2-$clasifAnterior' style='text-align:right;' cantidad='".number_format($sumaApli,2,'.','')."'>$ ".number_format($sumaApli,2)."</td></tr>";
						$sumaOrigen = 0;
						$sumaApli = 0;
						echo "<tr class='clasif-$clasifAnterior'><td></td><td></td><td></td><td></td></tr>";	
						echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td></td><td>".strtoupper($d->Clasificacion)."</td><td></td>
						<td></td></tr>";	
						//termina cuenta de clasificacion
						
						/*if($grupoAnterior != $d->Grupo)
						{
							echo "<tr><td colspan='3' class='clasif-$d->Clasificacion'></td></tr>";	
							echo "<tr style='font-weight:bold;height:30px;' class='clasif-$d->Clasificacion'><td colspan='3'>".strtoupper($d->Grupo)."</td></tr>";	
						}*/
					}
					/*else
					{
						if($grupoAnterior != $d->Grupo)
						{
							$red='';
							echo "<tr style='font-weight:bold;height:30px;' class='clasif-$d->Clasificacion'><td colspan='2'>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior' style='text-align:right;'>$ ".number_format($sumaOrigenGrupo,2)."</td><td id='sumG-$grupoAnterior' style='text-align:right;'>$ ".number_format($sumaApliGrupo,2)."</td></tr>";
							$sumaOrigenGrupo = 0;
							$sumaApliGrupo = 0;
							echo "<tr><td colspan='3' class='clasif-$d->Clasificacion'></td></tr>";	
							echo "<tr style='font-weight:bold;height:30px;' class='clasif-$d->Clasificacion'><td colspan='3'>".strtoupper($d->Grupo)."</td></tr>";	
						}
					}*/
					$red='';
					$ResultadosAntes = $d->CargosAbonosAnterior/$valMon;
					$Resultados = $d->CargosAbonos/$valMon;
					
						if($d->Clasificacion != 'Activo')
						{
							$Resultados *=-1;
							$ResultadosAntes *=-1;
							$resultadoParcial = $ResultadosAntes - $Resultados;
						}else
						{
							$resultadoParcial = $Resultados - $ResultadosAntes;
						}
					
					if($resultadoParcial<0)
					{
						$origen = $resultadoParcial*-1;
						$aplicacion = 0;
					}

					if($resultadoParcial>0)
					{
						$origen = 0;
						$aplicacion = $resultadoParcial;
					}
					
						if($resultadoParcial!=0)
						{
							echo "<tr class='clasif-$d->Clasificacion'><td style='mso-number-format:\"@\";'>".$d->Codigo."</td><td style='text-align:left;'>".$CM[1]."</td><td class='quitar'>$ResultadosAntes</td><td class='quitar'>$Resultados</td><td style='text-align:right;'>$ ".number_format($origen,2)."</td><td style='text-align:right;'>$ ".number_format($aplicacion,2)."</td></tr>";
						}
						
						//$sumaOrigenGrupo += $origen;
						$sumaOrigen += $origen;
						//$sumaApliGrupo += $aplicacion;
						$sumaApli += $aplicacion;
						$origen=0; $aplicacion=0;
					
					
					$clasifAnterior = $d->Clasificacion;
					//$grupoAnterior = $d->Grupo;
					$red='';
					if(floatval($sumaCantidad) < 0) $red = "color:red;";
				}
				$sumaRes=$sumaOrigen - $sumaApli;

				if(floatval($sumaRes)>0)
				{
					$origenRes = $sumaRes;
					$apliRes = 0;
				}

				if(floatval($sumaRes)<0)
				{
					$origenRes = 0;
					$apliRes = $sumaRes*-1;
				}
				?>
				
				<tr style='<?php echo "$subtitulo"; ?>'><td></td><td>TOTAL</td><td id='sumaTotal1' style='text-align:right;'></td><td id='sumaTotal2' style='text-align:right;'></td></tr>
				<tr style='<?php echo "$titulo1";?>' id='total-resultados'><td></td><td>TOTAL RESULTADOS</td><td class='sum1-<?php echo $clasifAnterior."' style='text-align:right;";?>' cantidad='<?php echo number_format($origenRes,2,'.',''); ?>'>$ <?php echo number_format($origenRes,2); ?></td><td class='sum2-<?php echo $clasifAnterior."' style='text-align:right;";?>' cantidad='<?php echo number_format($apliRes,2,'.',''); ?>'>$ <?php echo number_format($apliRes,2); ?></td></tr>
			</table>

<input type='hidden' id='totalMayores' value='<?php echo $sumaCont; ?>'>
<!--INICIA TITULO CONGELADO-->

</td></tr>
</table>


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
				<!--<form id="formpdf" action="../../../webapp/netwarelog/repolog/pdf.php" method="post" target="_blank" onsubmit="generar_pdf()">-->
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