<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}
$(document).ready(function(){
	$('#nmloader_div',window.parent.document).hide();
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};
	var sumatoria=0;
	var numero=0;
	$("tr").each(function(index)
	{
		if($('td:nth-child(6)',this).text() != '' && $('td:nth-child(6)',this).text() != 'Abono')
		{
			numero = $('td:nth-child(6)',this).text().replace(/\,/g,'').replace('$','');
			sumatoria += parseFloat(numero)
		}
	});
	$('#totales').text("$" + sumatoria.format());
	//Agregar este proceso para igualar los width de las columnas de la tabla
	//COMIENZA
	//Primer paso: pegar este bloque de jquery
	//Segundo paso: crear el titulo estatico
	//Tercer paso: crear el div que contendra el contenido
	//Cuarto paso: quitar anchos en tablas
	//Quinto paso: modificar el css
	//Sexto paso: height 0 en titulo real
	var ancho,texto;
	for(i=1;i<=6;i++)
	{
		ancho = $(".tit_tabla_buscari td:nth-child("+i+")").width();
		texto = $(".tit_tabla_buscari td:nth-child("+i+")").text();
		$(".estatico td:nth-child("+i+")").text(texto).width(ancho)
	}

	$("#resultados").attr('style','margin-top:-'+$(".tit_tabla_buscari").height()+'px;')
	//TERMINA
});			
</script>
<script language='javascript' src='js/pdfmail.js'></script>
<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/style.css" type="text/css">
<style>
.tit_tabla_buscar td
{
	font-size:medium;
}

#divcon
{
width:auto;
height:400px;
overflow:scroll;
}
@media print
{
	#imprimir,#filtros,#excel,#estatico,#pdf,#email
	{
		display:none;
	}

	#divcon
	{
		width:auto;
		height:auto;
	}

	#resultados
	{
		margin-top:40px !important;
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
							<a href="index.php?c=EgresosSinIva&f=Inicial"> <img src="../../../webapp/netwarelog/repolog/img/filtros.png" 
								title ="Haga clic aqui para cambiar filtros..." border="0"> </a>
						</td>                        
						<td width=16 align=right>
							<a href="javascript:mail();"> <img src="../../../webapp/netwarelog/repolog/img/email.png"  
							   title ="Enviar reporte por correo electrónico" border="0"> 
							</a>
						</td>
						<td width=16 align=right>
							<a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>
						</td>
						<td width=16 align=right>
							<a href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
							   title ="Generar reporte en PDF" border="0"> 
							</a>
						</td>																				
                    </tr>
                </table>
            </div>			
			<!-- //////////////////////////////////////////////////////  -->   

<div class="repTitulo">Egresos sin control de IVA</div>	
<br />
<!--div class='descripcion'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=EgresosSinIva&f=Inicial' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div!-->
	<input type='hidden' value='Egresos sin control de IVA.' id='titulo'>
	<div id='imprimible'>

	<table style="border:none; width:100%;background-color:#ffffff;color:black;" >
	<tr>
					<td style="width:50%">
			<?php
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'-->
					</td>
					<td style='width:50%;text-align:right;color:gray;font-size:7px;'>
						<b> <?php echo $impresion;?></b>					
					</td>
	</tr>
	<tr><td colspan="2" style='text-align:center;font-size:18px;'><b style='text-align:center;color:black;'><?php echo $empresa;?></b></td></tr>
    <tr><td style="text-align:center;color:#576370;"  colspan="2">
				<br><b style="font-size:15px;">Egresos sin control de IVA </b><br>
				Del <b><?php echo $inicio;?></b> Al <b><?php echo $fin;?></b> 
				<br><br>
		</td>
	</tr>
		
	</table>
	
	<!--Comienza titulo estatico-->
	<!--table border='0' cellspacing=0 cellpadding=0 id='estatico' align="center">
	<tr class='estatico'>
		<td class="nmcatalogbusquedatit"></td>
		<td class="nmcatalogbusquedatit"></td>
		<td class="nmcatalogbusquedatit"></td>
		<td class="nmcatalogbusquedatit"></td>
		<td class="nmcatalogbusquedatit"></td>
		<td class="nmcatalogbusquedatit"></td>
	</tr>
	</table-->
	
<!--TERMINA-->

<table border='0' align="center" cellpadding="3" style:"background-color:#ffffff;width:100%;max-width:900px;font-size:10px;">
	<thead>
	<tr style='height:22px;background-color:#edeff1;font-size:11px;font-weight:bold;'>
		<td style="min-width:100px;width:12%;">Periodo/Poliza</td>
		<td style="min-width:100px;width:12%;">Fecha</td>
		<td style="min-width:100px;width:15%;">Tipo Poliza</td>
		<td style="min-width:100px;width:25%;">Concepto</td>
		<td style="min-width:100px;width:15%;">Referencia</td>
		<td style="min-width:100px;width:21%;">Abono</td>
	</tr>
	</thead>
	<?php
	while($d = $datos->fetch_object())
	{
		if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
		{
    		$color='#ffffff';
		}
		else//Si es impar pinta esto
		{
    		$color='#f6f7f8';
		}
		echo "<tr style='height:22px;background-color:$color;text-align:center;font-size:10px;' >
				<td>$d->idperiodo/$d->numpol</td>
				<td>$d->fecha</td>
				<td>$d->tipoPoliza</td>
				<td>$d->concepto</td>
				<td>$d->referencia</td>
				<td style='text-align:right;font-weight:bold;'>$".number_format($d->Erogacion,2)."</td></tr>";
		$cont++;//Incrementa contador
	}
	?>
	<tr style='height:22px;font-size:12px;font-weight:bold;text-align:right;background-color:#edeff1;'><td></td>
	<td></td>
	<td></td>
	<td></td><td>Total: </td><td id='totales' style:"padding:10px;"></td></tr>
</table>



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
				">
				<!--<form id="formpdf" action="../../../webapp/netwarelog/repolog/pdf.php" method="post" target="_blank" onsubmit="generar_pdf()">-->
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
				<input type='hidden' name='nombreDocu' value='Egresos sin control de IVA'>
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