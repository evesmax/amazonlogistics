<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function()
{
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};

	$('#nmloader_div',window.parent.document).hide();	
	
	var suma1=0;suma2=0;suma3=0;suma4=0;suma5=0;suma6=0;
	
	/*$(".mayores").each(function(index, el) {
		
		mayor = $(this).attr('id');
		$(".afectables[mayor='"+mayor+"']").each(function() {
			if(mayor == mayorAntes)
			{
				suma1 += parseFloat($('td:nth-child(3)',this).attr('cantidad'))
				suma2 += parseFloat($('td:nth-child(4)',this).attr('cantidad'))
				suma3 += parseFloat($('td:nth-child(5)',this).attr('cantidad'))
				suma4 += parseFloat($('td:nth-child(6)',this).attr('cantidad'))
				suma5 += parseFloat($('td:nth-child(7)',this).attr('cantidad'))
				suma6 += parseFloat($('td:nth-child(8)',this).attr('cantidad'))
			}
			else
			{
				$(".mayores[id='"+mayorAntes+"'] td:nth-child(3)").text("$ "+suma1.format())
				$(".mayores[id='"+mayorAntes+"'] td:nth-child(4)").text("$ "+suma2.format())
				$(".mayores[id='"+mayorAntes+"'] td:nth-child(5)").text("$ "+suma3.format())
				$(".mayores[id='"+mayorAntes+"'] td:nth-child(6)").text("$ "+suma4.format())
				$(".mayores[id='"+mayorAntes+"'] td:nth-child(7)").text("$ "+suma5.format())
				$(".mayores[id='"+mayorAntes+"'] td:nth-child(8)").text("$ "+suma6.format())
				suma1 = parseFloat($('td:nth-child(3)',this).attr('cantidad'))
				suma2 = parseFloat($('td:nth-child(4)',this).attr('cantidad'))
				suma3 = parseFloat($('td:nth-child(5)',this).attr('cantidad'))
				suma4 = parseFloat($('td:nth-child(6)',this).attr('cantidad'))
				suma5 = parseFloat($('td:nth-child(7)',this).attr('cantidad'))
				suma6 = parseFloat($('td:nth-child(8)',this).attr('cantidad'))
			}
			alert(mayorAntes)
			mayorAntes = mayor
			
		});
	});*/

	$(".mayores").each(function(){
		suma1=0;suma2=0;suma3=0;suma4=0;suma5=0;suma6=0;
		$(".afectables[mayor='"+$(this).attr('id')+"']").each(function(){
		 	suma1+= parseFloat($('td:nth-child(3)',this).attr('cantidad'))
		 	suma2+= parseFloat($('td:nth-child(4)',this).attr('cantidad'))
		 	suma3+= parseFloat($('td:nth-child(5)',this).attr('cantidad'))
		 	suma4+= parseFloat($('td:nth-child(6)',this).attr('cantidad'))
		 	suma5+= parseFloat($('td:nth-child(7)',this).attr('cantidad'))
		 	suma6+= parseFloat($('td:nth-child(8)',this).attr('cantidad'))
		});
		$(".mayores[id='"+$(this).attr('id')+"'] td:nth-child(3)").text("$ "+suma1.format())
				$(".mayores[id='"+$(this).attr('id')+"'] td:nth-child(4)").text("$ "+suma2.format())
				$(".mayores[id='"+$(this).attr('id')+"'] td:nth-child(5)").text("$ "+suma3.format())
				$(".mayores[id='"+$(this).attr('id')+"'] td:nth-child(6)").text("$ "+suma4.format())
				$(".mayores[id='"+$(this).attr('id')+"'] td:nth-child(7)").text("$ "+suma5.format())
				$(".mayores[id='"+$(this).attr('id')+"'] td:nth-child(8)").text("$ "+suma6.format())
	});
	var total1=0;total2=0;total3=0;total4=0;total5=0;total6=0;
	$(".titulo").each(function(){
		suma1=0;suma2=0;suma3=0;suma4=0;suma5=0;suma6=0;
		$(".afectables[titulo='"+$(this).attr('id')+"']").each(function(){
		 	suma1+= parseFloat($('td:nth-child(3)',this).attr('cantidad'))
		 	suma2+= parseFloat($('td:nth-child(4)',this).attr('cantidad'))
		 	suma3+= parseFloat($('td:nth-child(5)',this).attr('cantidad'))
		 	suma4+= parseFloat($('td:nth-child(6)',this).attr('cantidad'))
		 	suma5+= parseFloat($('td:nth-child(7)',this).attr('cantidad'))
		 	suma6+= parseFloat($('td:nth-child(8)',this).attr('cantidad'))
		 	total1+= parseFloat($('td:nth-child(3)',this).attr('cantidad'))
		 	total2+= parseFloat($('td:nth-child(4)',this).attr('cantidad'))
		 	total3+= parseFloat($('td:nth-child(5)',this).attr('cantidad'))
		 	total4+= parseFloat($('td:nth-child(6)',this).attr('cantidad'))
		 	total5+= parseFloat($('td:nth-child(7)',this).attr('cantidad'))
		 	total6+= parseFloat($('td:nth-child(8)',this).attr('cantidad'))
		});
		$("#"+$(this).attr('id')+" td:nth-child(3)").text("$ "+suma1.format())
		$("#"+$(this).attr('id')+" td:nth-child(4)").text("$ "+suma2.format())
		$("#"+$(this).attr('id')+" td:nth-child(5)").text("$ "+suma3.format())
		$("#"+$(this).attr('id')+" td:nth-child(6)").text("$ "+suma4.format())
		$("#"+$(this).attr('id')+" td:nth-child(7)").text("$ "+suma5.format())
		$("#"+$(this).attr('id')+" td:nth-child(8)").text("$ "+suma6.format())
	});
	$("#totales td:nth-child(3)").text("$ "+total1.format())
	$("#totales td:nth-child(4)").text("$ "+total2.format())
	$("#totales td:nth-child(5)").text("$ "+total3.format())
	$("#totales td:nth-child(6)").text("$ "+total4.format())
	$("#totales td:nth-child(7)").text("$ "+total5.format())
	$("#totales td:nth-child(8)").text("$ "+total6.format())
	
if($('#tipoVista').val() == '1')
{
	$(".mayores").remove();
}

if($('#tipoVista').val() == '2')
{
	$(".afectables").remove();	
}
});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}
</script>
<script language='javascript' src='js/pdfmail.js'></script>
<link rel="stylesheet" href="css/style.css" type="text/css">
<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
<style>
.titulo
{
	color:black;background-color:#edeff1;height:30px;font-weight:bold;text-align:left;
}

.afectables
{
	height: 30px !important;text-align:left;;
}
.mayores
{
	color:black;height:30px;text-align:left;
}
.tit_tabla_buscar td
{
	font-size:medium;
}

#logo_empresa /*Logo en pdf*/
	{
		display:none;
	}

.clasemayor
{
	color:white;
	background-color:gray;
}
#titulo_impresion
{
	visibility: hidden;
}
#sc
{
	overflow:scroll;
}
@media print
{
	#imprimir,#filtros,#excel,#titulo,#email_icon
	{
		display:none;
	}
	#titulo_impresion
	{
		visibility:visible;
	}
	#sc
	{
		overflow: visible;
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

ini_set('memory_limit', '-1');
?>	

<div class='iconos'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=reports&f=balanzaComprobacion' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>
<div class="repTitulo">Balanza de Comprobación</div>

<input type='hidden' value='Balanza de Comprobacion.' id='titulo'>
<div id='imprimible'>
	<table width='100%'>
		<tr>
			<td width="50%">
				<?php
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php // echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'-->
			</td>
			<td valign="top" width='50%' style="font-size:7px;text-align:right;color:gray;">
			<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
			</td>
		</tr>
		<tr style="color:#576370;text-align:center;">
			<td colspan=2>
				<b style="font-size:18px;color:black;"><?php echo $empresa; ?></b><br>
				<b style="font-size:15px;">Balanza de Comprobación </b><br>   
				Ejercicio <b><?php echo $ej2; ?></b> Periodo De <b><?php echo $fecIni; ?></b> A <b><?php echo $fecFin; ?></b><br>
				<?php
				if($tipoVista == 1)
				{
					$nivel = 'Afectables';
				}
				if($tipoVista == 2)
				{
					$nivel = 'Mayor';
				}
				if($tipoVista == 3)
				{
					$nivel = 'Todos';
				}
				?>
				A nivel de <span id='nivel' style='font-weight:bold;'><?php echo $nivel; ?></span> No. de Cuentas: <b><span id='cuentaCuentas'><?php echo $n_cuentas;?></span></b>
				<?php if($valMon>1){echo "<br>Moneda <b>$moneda</b> Tipo de Cambio <b>$ $valMon </b> ";}?>
				<br><br>
			</td>
		</tr>
	</table>
<!--table border='0' style='width:100%;'>
	
	<tr class='tit_tabla_buscar' id='titulo' style='background-color:#edeff1;font-size:10px;'>
		<td style='width:9%'>Codigo</td>
		<td style='width:13%'>Nombre</td>
		<td style='width:13%'>Saldo Inicial Deudor</td>
		<td style='width:13%'>Saldo Inicial Acreedor</td>
		<td style='width:13%'>Cargos</td>
		<td style='width:13%'>Abonos</td>
		<td style='width:13%'>Saldo Final Deudor</td>
		<td style='width:13%'>Saldo Final Acreedor</td>
	</tr>
</table-->
<table border='0' align='center' cellpadding=3 style='width:100%;max-width:950px;font-size:9px;'>
	<thead>
	<tr style='background-color:#e4e7ea;font-size:10px;font-weight:bold;height:30px;text-align:center;'>
		<td style='width:8%'>Código</td>
		<td style='width:20%'>Nombre</td>
		<td style='width:12%'>Saldo Inicial Deudor</td>
		<td style='width:12%'>Saldo Inicial Acreedor</td>
		<td style='width:12%'>Cargos</td>
		<td style='width:12%'>Abonos</td>
		<td style='width:12%'>Saldo Final Deudor</td>
		<td style='width:12%'>Saldo Final Acreedor</td>
	</tr>
	</thead>
	<tbody>
		<?php 
		$mayor 		= '';
		$codigo 	= '';
		$grupo		= '';
		$cargosMayor= 0;
		$abonosMayor= 0;
		$cargos 	= 0;
		$abonos 	= 0;
		$cont 		= 0;
		$contMayor	= 0;
			while($d = $datos->fetch_object())
			{

				//Pinta los grupos, activo, pasivo, capital y resultados
				if($d->h1 != $grupo)
				{
					switch($d->h1)
					{
						case 1:$tituloGrupo = 'ACTIVO';break;
						case 2:$tituloGrupo = 'PASIVO';break;
						case 3:$tituloGrupo = 'CAPITAL';break;
						case 4:$tituloGrupo = 'RESULTADOS';break;
					}
					echo "<tr style='font-weight:bold;' id='$tituloGrupo' class='titulo'><td>$tituloGrupo</td><td></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td></tr>";
				}

				//pinta las sumas por cuenta de mayor
				$IdDescMayor = explode('/',$d->mayor);
				if($tipoVista==2)
				{
					
	    				$colorMayor='#fafafa';
	    				
					
				}
				else
				{
					$colorMayor="#f6f7f8;font-weight: bold";
				}
				if($d->mayor != $mayor)
				{
					echo "<tr class='mayores' style='background-color:$colorMayor;' id='".$IdDescMayor[0]."'><td>".$IdDescMayor[0]."</td><td>".$IdDescMayor[1]."</td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td></tr>";
				}

				//pinta las sumas por cuentas afectables
				if($d->CargosMes=='') $d->CargosMes=0;
				if($d->AbonosMes=='') $d->AbonosMes=0;
				if($d->CargosAntes=='') $d->CargosAntes=0;
				if($d->AbonosAntes=='') $d->AbonosAntes=0;
				
				if($d->account_nature == 1)
				{
					$InicialDeudor = 0;
					$InicialAcreedor = floatval($d->AbonosAntes) - floatval($d->CargosAntes);
					$InicialAcreedor = $InicialAcreedor/$valMon;
					$Suma = floatval($d->AbonosMes) - floatval($d->CargosMes);
					$Suma = $Suma/$valMon;
					$FinalDeudor = 0;
					$FinalAcreedor = floatval($InicialAcreedor) + floatval($Suma);
				}

				if($d->account_nature == 2)
				{
					$InicialDeudor = floatval($d->CargosAntes) - floatval($d->AbonosAntes);
					$InicialDeudor = $InicialDeudor/$valMon;
					$InicialAcreedor = 0;
					$Suma = floatval($d->CargosMes) - floatval($d->AbonosMes);
					$Suma = $Suma/$valMon;
					$FinalDeudor = floatval($InicialDeudor) + floatval($Suma);
					$FinalAcreedor = 0;
				}

				
					if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
					{
	    				$color2='#ffffff';
					}
					else//Si es impar pinta esto
					{
	    				$color2='#fafafa';
					}
					$cont++;
				
				
					$cm=$d->CargosMes/$valMon;
					$am=$d->AbonosMes/$valMon;
				
					echo "<tr class='afectables' style='background-color:$color2;' mayor='".$IdDescMayor[0]."' titulo='$tituloGrupo'><td>$d->manual_code</td><td>$d->description</td><td style='text-align:right;' cantidad='$InicialDeudor'>$ ".number_format($InicialDeudor,2)."</td><td style='text-align:right;' cantidad='$InicialAcreedor'>$ ".number_format($InicialAcreedor,2)."</td><td style='text-align:right;' cantidad='$cm'>$ ".number_format(($d->CargosMes/$valMon),2)."</td><td style='text-align:right;' cantidad='$am'>$ ".number_format(($d->AbonosMes/$valMon),2)."</td><td style='text-align:right;' cantidad='$FinalDeudor'>$ ".number_format($FinalDeudor,2)."</td><td style='text-align:right;' cantidad='$FinalAcreedor'>$ ".number_format($FinalAcreedor,2)."</td></tr>";	
				

				//guarda anterior
				$mayor 		= $d->mayor;
				$codigo 	= $d->account_code;
				$grupo 		= $d->h1;

			}
		?>
		<tr id='totales' style='background-color:e4e7ea;height:30px;font-weight:bold;'><td>TOTALES</td><td></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td><td style='text-align:right'></td></tr>
	</tbody>
</table>

	<input type='hidden' id='tipoVista' value='<?php echo $tipoVista; ?>'>
</div>
<!--GENERA PDF*************************************************-->
<div id="divpanelpdf"
				style="
					position: absolute; top:200px; left: 40%;
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
				<input type='hidden' name='nombreDocu' value='Balanza de Comprobacion'>
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
					top:-200px
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