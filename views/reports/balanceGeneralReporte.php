<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function()
{
	$('#nmloader_div',window.parent.document).hide();
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};

	Number.prototype.toFixedDown = function(digits) {
    var re = new RegExp("(\\d+\\.\\d{" + digits + "})(\\d)"),
        m = this.toString().match(re);
    return m ? parseFloat(m[1]) : this.valueOf();
	};

	$('.clasif-Clasificacion').remove()
	$(".clasif-Activo:contains('TOTAL GRUPO')").remove()
	
	var ac_cir = $("#sumG-ACTIVOCIRCULANTE,#sumG-CIRCULANTE").attr('cantidad')
	if(typeof ac_cir === 'undefined') ac_cir=0
		

	var ac_fij = $("#sumG-ACTIVOFIJO,#sumG-FIJO").attr('cantidad')
	if(typeof ac_fij === 'undefined') ac_fij=0	
		

	var ac_dif = $("#sumG-ACTIVODIFERIDO,#sumG-DIFERIDO").attr('cantidad')
	if(typeof ac_dif === 'undefined') ac_dif=0	
		

	var total_ac = parseFloat(ac_cir) + parseFloat(ac_fij) + parseFloat(ac_dif)	
		
	$("#sumaTotal_AC").html("$ "+total_ac.toFixedDown(4).format()).css('text-align','right')
	if(total_ac < 0)
	{
		$("#sumaTotal_AC").css('color','red')
	}
	/////////////////////
	var pasivo = $("#sum-Pasivo").attr('cantidad')
	if(typeof pasivo === 'undefined') pasivo=0
		else pasivo = pasivo.replace('$ ','').replace(/,/g,'')
	
	var capital = $("#sum-Capital").attr('cantidad')
	if(typeof capital === 'undefined') capital=0
		else capital = capital.replace('$ ','').replace(/,/g,'')
	
	var resultados = $("#sum-Resultados").attr('cantidad')
	if(typeof resultados === 'undefined') resultados=0
		else resultados = resultados.replace('$ ','').replace(/,/g,'')
	
	var total_pcr = parseFloat(pasivo) + parseFloat(capital) + parseFloat(resultados)
	$("#sumaTotal_PCR").html("$ "+total_pcr.toFixedDown(4).format()).css('text-align','right')
	if(total_pcr < 0)
	{
		$("#sumaTotal_PCR").css('color','red')
	}

	$(".clasif-Resultados").remove()
	//$("#totrest").prev().remove()
	//$("#totrest").prev().remove()


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

/*
table table tr{ background: #e4ecb8;}
table table tr:nth-of-type(odd) { 
        background: #f3f3f3; 
    }
*/
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
	#imprimir,#filtros,#excel
	{
		display:none;
	}
	#logo_empresa
	{
		display:block;
	}
}
</style>

<!-- //////////////////////////////////////////////////////  -->
			<!-- BARRA DE HERRAMIENTAS DEL REPOLOG  -->
            <div class="iconos">
                <table class="bh" align="right" border="0" >
                    <tr>            
                        <td width=16 align=right>
                            <a href="javascript:window.print();"><img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0"></a>
                        </td>
                        <td width=16  align=right>
							<a href="index.php?c=reports&f=balanceGeneral&tipo=1"> <img src="../../../webapp/netwarelog/repolog/img/filtros.png" 
								title ="Haga clic aqui para cambiar filtros..." border="0"> </a>
						</td>                        
						<td width=16 align=right>
							<a href="javascript:mail();"> <img src="../../../webapp/netwarelog/repolog/img/email.png"  
							   title ="Enviar reporte por correo electrónico" border="0"> 
							</a>
						</td>
						<td width=16 align=right>
							<a href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
							   title ="Generar reporte en PDF" border="0"> 
							</a>
						</td>
						<td width=16 align=right>
							<a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>
						</td>																				
                    </tr>
                </table>
            </div>			
			<!-- //////////////////////////////////////////////////////  -->  
<div class="repTitulo">Balance General</div>	
<!--div class="per3">
	<ul>
	<li><label>Balance De:</label> <?php // echo $empresa; ?></li>
	<li><label>Ejercicio:</label> <?php //echo $ej; ?></li>
	<li><label>Periodo:</label> <?php ///echo $periodo; ?></li>
	</ul>
	<div class="iconos">
	<a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=reports&f=balanceGeneral&tipo=1' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a>
    </di>
	<div align="center">
		<?php
		/*	$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);*/
			?>
			<img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'>
	</div>
</div-->

<?php
$moneda=$_POST['moneda'];
if($_POST['valMon']){$valMon=$_POST['valMon'];}else{$valMon=1;}
// Manejo de Colores -----------------------------------------------
$titulo="font-weight:bold;font-size:9px;color:black;background-color:#edeff1;height:30px;";
$subtitulo="font-weight:bold;height:30px;background-color:#fafafa;font-size:9px;";

			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>

<div class='descripcion'>
	<input type='hidden' value='Balance General.' id='titulo'>
	<div id='imprimible'>


<table border=0 style='width:100%;max-width:900px;' style='background:white;' align="center">
<tr style='background:white;'>
	<td ></td>
	<td></td>
	<td valign="top" style="font-size:7px;text-align:right;color:gray;">
		<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
	</td>
</tr>
<tr style='background:white;'>
	<td colspan='3' style='text-align:center;font-size:18px;'>
		<b style='text-align:center;'><?php echo $empresa;?></b>
	</td>
</tr>
<tr style='background:white;color:#576370;text-align:center;font-size:12px'> 	
	<td colspan='3'>
		<b style="font-size:15px;">Balance General</b><br>
		Ejercicio <b><?php echo $ej;?></b>  Periodo <b><?php echo $periodo; ?> </b><br>
		Sucursal <b><?php echo $nomSucursal; ?></b> Segmento <b><?php echo $nomSegmento; ?></b> 
		<?php if($valMon>1){echo "<br>Moneda <b>$moneda</b> Tipo de Cambio $ <b>$valMon</b>";}?>
		<br><br>
	</td>
</tr>

	<tr>
		<td valign='top' style='width:49%;'>
			<table style="font-size:9px;width:100%;text-align:left;" cellpadding=3 >
				<tr style='<?php echo "$titulo"; ?>'><td colspan='3'>ACTIVO</td></tr>
				<?php
				//Carga los Activos***************************************************************************************************
				$grupoAnterior='Grupo';
				$sumaCantidad = 0;
				$n=1;
				while($a = $activos->fetch_object())
				{
					$CM = explode(' / ',$a->Cuenta_de_Mayor,2);
					$grupoAnterior_1 = str_replace(' ', '', $grupoAnterior);
					if($grupoAnterior != $a->Grupo)
						{
							$red='';
							if(floatval($sumaCantidadGrupo) < 0) $red = "color:red;";
							echo "<tr style='$subtitulo' class='clasif-$a->Clasificacion'><td colspan='2'>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior_1' style='text-align:right;$red' cantidad='$sumaCantidadGrupo'>$ ".number_format($sumaCantidadGrupo,2)."</td></tr>";
							$sumaCantidadGrupo = 0;
							// echo "<tr style='text-align:left;'><td colspan='3' class='clasif-$a->Clasificacion'></td></tr>";	
							echo "<tr style='$subtitulo' class='clasif-$a->Clasificacion'><td colspan='3'>".strtoupper($a->Grupo)."</td></tr>";	
						}
						$red='';
						if(floatval($a->CargosAbonos) < 0) $red = 'color:red;';
						if($a->CargosAbonos>=0.01 || $a->CargosAbonos<= -0.01)
						{

							echo "<tr><td style='mso-number-format:\"@\";text-align:left;width:27%;min-width:80px;'>".$a->Codigo."</td><td style='width:43%;min-width:200px;'>".$CM[1]."</td><td style='text-align:right;$red;width:30%;min-width:120px;'>$ ".number_format($a->CargosAbonos/$valMon,2)."</td></tr>";
							$sumaCantidad += $a->CargosAbonos/$valMon;
							$sumaCantidadGrupo += $a->CargosAbonos/$valMon;
							$grupoAnterior = $a->Grupo;
						}
						$n++;
				}
				$red='';
				$grupoAnterior_1 = str_replace(' ', '', $grupoAnterior);
				if(floatval($sumaCantidadGrupo) < 0) $red = "color:red;";
				echo "<tr style='$subtitulo' class='clasif-$a->Clasificacion'><td colspan='2'>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior_1' style='text-align:right;$red' cantidad='$sumaCantidadGrupo'>$ ".number_format($sumaCantidadGrupo,2)."</td></tr>";
							$sumaCantidadGrupo = 0;
				$red='';
				if(floatval($sumaCantidad) < 0) $redT = "color:red;";
				?>
				
				<!--tr style='font-weight:bold;color:white;background-color:#69771e;height:30px;'><td colspan='2'>TOTAL ACTIVO</td><td id='sumaTotal_AC' style='text-align:right;<?php //echo $red;?>'></td></tr!-->
			</table>
			
		</td>
		<td style='width:2%;'></td>
		<td valign='top' style='width:49%;'>
			<table style="font-size:9px;width:100%;text-align:left;" cellpadding=3  >
				<?php
				//Carga los Pasivo, Capital y Resutados******************************************************************************
				$clasifAnterior='Clasificacion';//Almacena la clasificacion anterior
				$grupoAnterior='Grupo';
				$sumaCantidad = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				$sumaCantidadGrupo = 0;//Almacena la sumatoria de las cantidades de la cuenta de mayor
				$t=1;
				while($d = $datos->fetch_object())
				{
					$CM = explode(' / ',$d->Cuenta_de_Mayor,2);
					if($clasifAnterior != $d->Clasificacion)
					{
						if($grupoAnterior != $d->Grupo)
						{
							if(floatval($sumaCantidadGrupo) < 0) $red = "color:red;";
							echo "<tr style='$subtitulo' class='clasif-$clasifAnterior'><td colspan='2'>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior' style='text-align:right;$red' cantidad='$sumaCantidadGrupo'>$ ".number_format($sumaCantidadGrupo,2)."</td></tr>";
							$sumaCantidadGrupo = 0;
						}

						//comienza cuenta de clasificacion
						$red='';
						if(floatval($sumaCantidad) < 0) $red = "color:red;";
						echo "<tr style='$titulo' class='clasif-$clasifAnterior'><td colspan='2'>TOTAL ".strtoupper($clasifAnterior)."</td><td id='sum-$clasifAnterior' style='text-align:right;$red' cantidad='$sumaCantidad'>$ ".number_format($sumaCantidad,2)."</td></tr>";
						$sumaCantidad = 0;
						//echo "<tr class='clasif-$clasifAnterior'><td colspan='3'></td></tr>";	
						if('PASIVO'!=strtoupper($d->Clasificacion)){ echo "<tr><td colspan='3' style='height:15px;background-color:#ffffff'></td></tr>";}
						echo "<tr style='$titulo' class='clasif-$d->Clasificacion'><td colspan='3'>".strtoupper($d->Clasificacion)."</td></tr>";	
							
						//termina cuenta de clasificacion
						
						if($grupoAnterior != $d->Grupo)
						{
							//echo "<tr><td colspan='3' class='clasif-$d->Clasificacion'></td></tr>";	
							echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td colspan='3'>".strtoupper($d->Grupo)."</td></tr>";	
						}
					}
					else
					{
						if($grupoAnterior != $d->Grupo)
						{
							$red='';
							if(floatval($sumaCantidadGrupo) < 0) $red = "color:red;";
							echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td colspan='2'>TOTAL ".strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior' style='text-align:right;$red' cantidad='$sumaCantidadGrupo'>$ ".number_format($sumaCantidadGrupo,2)."</td></tr>";
							$sumaCantidadGrupo = 0;
							// echo "<tr><td colspan='3' class='clasif-$d->Clasificacion'></td></tr>";	
							echo "<tr style='$subtitulo' class='clasif-$d->Clasificacion'><td colspan='3'>".strtoupper($d->Grupo)."</td></tr>";	
						}
					}
					$red='';

					if($d->CargosAbonos>=0.01 || $d->CargosAbonos<= -0.01)
					{
						$Resultados = $d->CargosAbonos*-1;
						$Resultados = $Resultados/$valMon;
						if(floatval($Resultados) < 0) $red = 'color:red;';
						echo "<tr class='clasif-$d->Clasificacion'><td style='mso-number-format:\"@\";width:27%;min-width:80px;'>".$d->Codigo."</td><td style='width:43%;min-width:200px;'>".$CM[1]."</td><td style='text-align:right;$red:;width:30%;min-width:120px;'>$ ".number_format($Resultados,2)."</td></tr>";
						$sumaCantidadGrupo += $Resultados;
						$sumaCantidad += $Resultados;
						
						$grupoAnterior = $d->Grupo;
						$red='';
						if(floatval($sumaCantidad) < 0) $red = "color:red;";
					}
					$t++;
					$clasifAnterior = $d->Clasificacion;
				}
				if($clasifAnterior != 'Resultados')
				{
					echo "<tr style='$subtitulo' id='totrest'><td colspan='2'>TOTAL ". strtoupper($grupoAnterior)."</td><td id='sumG-$grupoAnterior' style='text-align:right;$red' cantidad='$sumaCantidadGrupo'>$ ". number_format($sumaCantidadGrupo,2)."</td></tr>";
					?> <tr style='<?php echo "$subtitulo"; ?>' id='totrest'><td colspan='2'>TOTAL <?php echo strtoupper($clasifAnterior);?></td><td id='sum-<?php echo $clasifAnterior."' style='text-align:right;".$red;?>' cantidad='<?php echo $sumaCantidad ?>'>$ <?php echo number_format($sumaCantidad,2); ?></td></tr><?php
				}else{
				?><tr style='<?php echo "$subtitulo"; ?>' id='totrest'><td colspan='2'>TOTAL <?php echo strtoupper($clasifAnterior);?></td><td id='sum-<?php echo $clasifAnterior."' style='text-align:right;".$red;?>' cantidad='<?php echo $sumaCantidad ?>'>$ <?php echo number_format($sumaCantidad,2); ?></td></tr><?php		
			}
				?>
				
				
			
			</table>
		</td>
	</tr>
	<tr>
		
		<td><table style="font-size:10px;padding:3px;width:100%;text-align:left;" ><tr style='<?php echo "$titulo"; ?>'><td style='width:70%;min-width:280px;'>TOTAL ACTIVO</td><td id='sumaTotal_AC' style='text-align:right;width:30%;min-width:120px;<?php echo $redT;?>'></td></tr></table></td>
		<td></td>
		<td><table style="font-size:10px;padding:3px;width:100%;text-align:left;" ><tr style='<?php echo "$titulo"; ?>'><td style='width:70%;min-widht:280px;'>TOTAL PASIVO, CAPITAL Y RESULTADOS</td><td id='sumaTotal_PCR' style='width:30%;min-width:120px;'></td></tr></table></td>
    </tr>

</table>	


<input type='hidden' id='totalMayores' value='<?php echo $sumaCont; ?>'>
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
				<!--form id="formpdf" action="../../../webapp/netwarelog/repolog/pdf.php" method="post" target="_blank" onsubmit="generar_pdf()"-->
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
				<input type='hidden' name='nombreDocu' value='Balance General'>
				<input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
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