<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
<link rel="stylesheet" href="css/style.css" type="text/csss">
<script language='javascript'>
$(document).ready(function()
{

	$('#nmloader_div',window.parent.document).hide();
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};


	//var total = 0;
	var subtotal = 0;
	for(var f = 5; f <= 10; f++)//Recorre cada columna
	{
		$('#xx').append("<td id='"+f+"'></td>");//Anexa un elemento a la tabla
		//if(f!=9)
		//{
			$("#resultados tr").each(function(index)//Recorre cada fila
			{
				if($(this).attr('tipo') == 'numero')//Si es un elemento de tipo numero hace la suma de los resulados
				{
					//total += parseFloat($("td:nth-child("+f+")",this).text())//Sumatoria total
					subtotal += parseFloat($("td:nth-child("+f+")",this).text())//Sumatoria del proveedor
				}
				if($(this).next().attr('tipo') == 'subtotal' && $(this).next().next().attr('tipo') != 'numero')//Si es un campo del tipo subtotal y el siguiente elemento no es de tipo numero entonces agrega elementos
				{
					if(f==5)//si se trata del primer barrido agraga td`s de relleno a la tabla
					{
						$(this).next().append("<td colspan=2>Total Cuenta:</td><td></td><td></td>");
					}
					
					if(isNaN(subtotal)){subtotal=0;}

					
						$(this).next().append("<td style='text-align:right;'>$"+subtotal.format()+"</td>");//Agrega la suma del subtotal
					
					subtotal=0;	//se reinicia la suma
				}
			});	
		//if(isNaN(total)){total=0;}
		
		//$("#"+f).text(total.toFixed(2));//Agrega el total al elemento
		//total = 0;
		//}
	}

});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}
</script>
<script language='javascript' src='js/pdfmail.js'></script>
<style>

.proveedor
{
background-color:#FFFFFF;
}
.proveedor2
{
background-color:#f6f7f8;
}

.tit_tabla_buscar td
{
	font-size:12px;
}

.razon_social
{
	
	text-align:left;
}

.derecha,#resultados
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
	<a href='index.php?c=flujoEfectivoIva&f=Inicial' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros'><img src="../../netwarelog/repolog/img/filtros.png" border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div>
<div class="repTitulo">&nbsp;Conciliaci&oacute;n de Flujo de efectivo e IVA</div>

<input type='hidden' value='Conciliaci&oacute;n de Flujo de efectivo e IVA.' id='titulo'>
<div id='imprimible'>

	<table width='100%'>
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
		<tr style="text-align:center;color:#576370;">
			<?php
			echo $fecha;
			?>
		</tr>
	</table>
<table style='width:100%;font-size:8px;max-width:900px;' cellpadding=3 id='resultados' align="center">
	<thead>
		<tr style="background-color:#edeff1;font-size:9px;font-weight:bold;height:30px;" >
		<td style='width:7%;'>Periodo / Poliza</td>
		<td style='width:10%;'>Fecha</td>
		<td style='width:10%;'>Tipo Poliza</td>
		<td style='width:15%;'>Concepto</td>
		<td style='width:12%;'>Abonos Flujo de Efectivo</td>
		<td style='width:8%;'>Total</td>
		<td style='width:8%;'>Base</td>
		<td style='width:8%;'>IVA</td>
		<td style='width:11%;'>Iva Pagado No Acreditable</td>
		<td style='width:11%;'>Otras Erogaciones</td>
	</tr>
	</thead>
	<?php
	$anterior ='';
	$pagoIva = 0;
	while($d = $datos->fetch_object())
	{

		if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
		{
    		$color='#ffffff';
		}
		else//Si es impar pinta esto
		{
    		$color='#fafafa';
		}

		if($anterior != $d->account_code)
		{
			echo "<tr style='height:5px;'><td colspan='10' style='height:5px;'></td></tr>
			<tr class='razon_social' style='background-color:#edeff1;font-weight:bold;height:30px;'>
			<td colspan=2 title='Cuenta'>$d->manual_code</td>
			<td colspan=3>$d->description</td>
			<td colspan=3></td>
			<td colspan=4></td>
			</tr>";
		}

		echo "<tr tipo='numero' style='height:30px !important;background-color:$color;'>
				<td title='Periodo/Poliza'>$d->idperiodo/$d->numpol</td>
				<td title='Fecha'>$d->fecha</td>
				<td class='derecha' title='Tipo de poliza'>$d->TipoPoliza</td>
				<td title='Concepto'>$d->concepto</td>
				<td title='Abonos a Flujo de Efectivo' style='text-align:right;'>".number_format($d->TotalAbonos,2)."</td>
				<td title='Total' style='text-align:right;'>".number_format($d->Total,2)."</td>
				<td title='Importe Base' style='text-align:right;'>".number_format($d->ImporteBase,2)."</td>
				<td title='Importe IVA' style='text-align:right;'>".number_format($d->ImporteIva,2)."</td>
				<td title='IVA Pagado No Acreditable' style='text-align:right;'>".number_format($d->IvaPagadoNoAcreditable,2)."</td>
				<td title='Otras Erogaciones' style='text-align:right;'>".number_format($d->Erogaciones,2)."</td>";

				echo "</tr>";
				if($_POST['impDetalleProv'])
				{
					if($_POST['soloAplican'])
					{
						$ProvsCuenta = $this->ProvsCuenta($d->idPoliza,'Aplican');	
					}
					else
					{
						$ProvsCuenta = $this->ProvsCuenta($d->idPoliza,'Todas');	
					}
					while($p = $ProvsCuenta->fetch_object())
					{
						if($cont2%2==0)//Si el contador es par pinta esto en la fila del grid
						{
    						$color2='proveedor';
						}
						else//Si es impar pinta esto
						{
    						$color2='proveedor2';
						}
						$aplica='';
						if($p->aplica == 0)
						{
							$aplica=" (<b>No Aplica</b>)";
						}
						echo "<tr class='$color2' tipo='proveedor'><td></td><td></td><td colspan='3' style='text-align:left;'>$p->Proveedor.$aplica</td><td>".number_format($p->Total,2,'.','')."</td><td>".number_format($p->importeBase,2,'.','')."</td><td>".number_format($p->ImporteIva,2,'.','')."</td><td>".number_format($p->ivaPagadoNoAcreditable,2,'.','')."</td><td>".number_format($p->otrasErogaciones,2,'.','')."</td></tr>";
						$cont2++;
					}
				}
		echo "<tr id='$d->Cuenta' style='text-align:center;font-weight:bold;background-color:#edeff1;' tipo='subtotal'></tr>
				";

		$cont++;//Incrementa contador
		$anterior = $d->account_code;

	}
	?>
	<!--<tr  id='xx' style='text-align:center;font-weight:bold;background-color:#91C313;color:white;' tipo='total'><td>Totales:</td><td></td><td></td><td></td></tr>-->
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
				<input type='hidden' name='nombreDocu' value='Flujo de efectivo e iva'>
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