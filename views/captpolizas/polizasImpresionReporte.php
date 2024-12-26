<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script-->
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};


	//Proceso que recorre los valores de los elementos y los suma. 
	//Cuando termina agrega los elementos con los resultados
	
	//var total = 0;
	var subtotal = 0;
	for(var f = 7; f <= 8; f++)//Recorre cada columna
	{
		$('#xx').append("<td id='"+f+"'></td>");//Anexa un elemento a la tabla
	//	if(f!=9)
	//	{
			$("#polizas tr").each(function(index)//Recorre cada fila
			{
				if($(this).attr('tipo') == 'numero')//Si es un elemento de tipo numero hace la suma de los resulados
				{
					//total += parseFloat($("td:nth-child("+f+")",this).text())//Sumatoria total
					subtotal += parseFloat($("td:nth-child("+f+")",this).text().replace(/\,/g,'').replace('$',''))//Sumatoria del proveedor
				}
				if($(this).next().attr('tipo') == 'subtotal' && $(this).next().next().attr('tipo') != 'numero')//Si es un campo del tipo subtotal y el siguiente elemento no es de tipo numero entonces agrega elementos
				{
					if(f==7)//si se trata del primer barrido agraga td`s de relleno a la tabla
					{
						$(this).next().append("<td></td><td></td><td></td><td></td><td></td><td>Sumas Iguales:</td>");
					}
					
					if(isNaN(subtotal)){subtotal=0;}

					
						$(this).next().append("<td style='text-align:right;'>$ "+subtotal.format()+"</td>");//Agrega la suma del subtotal
					
					subtotal=0;	//se reinicia la suma
				}
			});	
	//	if(isNaN(total)){total=0;}
		
	//	$("#"+f).text(total.toFixed(2));//Agrega el total al elemento
	//	total = 0;
	//	}
	}

});	

function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}
$(document).ready(function(){
	$('#nmloader_div',window.parent.document).hide();	
});			
</script>
<script language='javascript' src='js/pdfmail.js'></script>
<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
<link rel="stylesheet" href="css/style.css" type="text/css">
<style>
@media print
{
	#imprimir,#filtros,#excel,#estatico,#pdf,#email
	{
		display:none;
	}

}
</style>

<!-- //////////////////////////////////////////////////////  -->
			<!-- BARRA DE HERRAMIENTAS DEL REPOLOG  -->
            <div class="iconos">
                <table align="right" border="0" >
                    <tr>            
                        <td width=16 align=right>
                            <a href="javascript:window.print();"><img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0"></a>
                        </td>
                        <td width=16  align=right>
							<a href="index.php?c=polizasImpresion&f=Inicial&tipo=<?php echo $_GET['tipo']; ?>"> <img src="../../../webapp/netwarelog/repolog/img/filtros.png" 
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
<?php 
if($_GET['tipo']==1){
				$tit="Impresión de Polizas";
				}else{
				$tit="Libro de Diario";
				}
?>
<div class="repTitulo"><?php echo "$tit"; ?></div>	
<br />
<!--div class='descripcion'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0"></a>
	<a href='index.php?c=EgresosSinIva&f=Inicial' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div!-->
	<input type='hidden' value='<?php echo "$tit"; ?>' id='titulo'>
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
						<!--b> Fecha de Impresión <br><?php // echo date("d/m/Y H:i:s"); ?></b><br><br-->					
					</td>
	</tr>
	<tr><td colspan="2" style='text-align:center;font-size:18px;'><b style='text-align:center;color:black;'><?php echo $empresa;?></b></td></tr>
    <tr><td style="text-align:center;color:#576370;"  colspan="2">
				<b style="font-size:15px;"><?php echo "$tit";?></b><br>
				Del <b><?php echo $inicio;?></b> Al <b><?php echo $fin;?></b> 
				<br><br>
		</td>
	</tr>
		
	</table>
	

<!--TERMINA-->

<table border='0' align="center" id='polizas' cellpadding="3" style="width:100%;max-width:1000px;font-size:10px;">
	
<!--Separador****|****-->
	<?php 

	//print_r($_POST);
	//echo "$datos";
	$ahora="";
	$cont=1;
	while($d = $datos->fetch_object()){
		
		if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
		{
    		$color='#fafafa';
		}
		else//Si es impar pinta esto
		{
    		$color='#ffffff';
		}
		
		
		

		if($d->CARGO==0){
			$cargo=0;
		}else{
			$cargo=str_replace(',','',$d->CARGO);
			$SumTcargo += $cargo;
			$cargo='$ '.number_format($cargo,2);
		}

		if($d->ABONO==0){
			$abono=0;
		}else{
			$abono=str_replace(',','',$d->ABONO);
			$SumTabono += $abono;
			$abono='$ '.number_format($abono,2);
		}


		

		$ahora="$d->NUM_POL/$d->TIPO_POLIZA/$FECHA";
		if($antes==$ahora){
		  echo "<tr style='height:30px;background-color:$color;text-align:center;font-size:9px;' tipo='numero'>
				<td>$d->NUM_MOVIMIENTO</td>
				<td>$d->CODIGO</td>
				<td>$d->CUENTA</td>
				<td>$d->SEGMENTO</td>
				<td>$d->REFERENCIA_MOV</td>
				<td>$d->CONCEPTO_MOV</td>
				<td style='text-align:right;font-weight:bold;'> $cargo</td>
				<td style='text-align:right;font-weight:bold;'> $abono</td>
				</tr>";

		}else{

		echo "
		<tr style='text-align:right;font-weight:bold;background-color:#f6f7f8;font-size:8px;' tipo='subtotal'></tr>

		<tr style='height:20px;'><td></td><td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td></tr>

		<!--Separador**|**-->

		<tr style='background-color:#edeff1;text-align:left;height:30px;'>
		<td>No. Poliza</td>
		<td><b>$d->NUM_POL</b></td>
		<td>Concepto</td>
		<td><b>$d->CONCEPTO</b></td>
		<td>Tipo</td>
		<td><b>$d->TIPO_POLIZA</b></td>
		<td>Fecha</td>
		<td><b>$d->FECHA</b></td>
		</tr>

		<tr style='background-color:#f6f7f8;font-weight:bold;text-align:center;height:30px;'>
			<td style='width:8%;'>No. Movimiento</td>
			<td style='width:10%;'>Código Manual</td>
			<td style='width:15%;'>Nombre de la Cuenta</td>
			<td style='width:16%;'>Segmento de Negocio</td>
			<td style='width:12%;'>Referencia del Movimiento</td>
			<td style='width:15%;'>Concepto del Movimiento</td>
			<td style='width:12%;'>Cargo</td>
			<td style='width:12%;'>Abono</td>
		</tr>

		<tr style='height:22px;background-color:$color;text-align:center;font-size:9px;' tipo='numero' >
				<td tittle='num'>$d->NUM_MOVIMIENTO</td>
				<td tittle='cod'>$d->CODIGO</td>
				<td tittle='cue'>$d->CUENTA</td>
				<td tittle='seg'>$d->SEGMENTO</td>
				<td tittle='ref'>$d->REFERENCIA_MOV</td>
				<td tittle='con'>$d->CONCEPTO_MOV</td>
				<td tittle='car' style='text-align:right;font-weight:bold;'> $cargo</td>
				<td tittle='abo' style='text-align:right;font-weight:bold;'> $abono</td>
				</tr>";

		}
		$cont++; //Incrementa contador 
		$antes="$d->NUM_POL/$d->TIPO_POLIZA/$FECHA";
	} 
	?>
	<tr style='text-align:right;font-weight:bold;background-color:#f6f7f8;font-size:8px;' tipo='subtotal'></tr>
	<!--tr style='height:22px;font-size:12px;font-weight:bold;text-align:right;background-color:#edeff1;'><td></td>
	<td></td>
	<td></td>
	<td></td><td>Total: </td><td id='totales' style:"padding:10px;"></td></tr-->
	<tr><td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td></tr>
	<?php if($_GET['tipo']!=1){ 
								//$SumTcargo=str_replace(',','',$SumTcargo);
								//$SumTabono=str_replace(',','',$SumTabono);
								


								echo "<tr style='height:22px;'><td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td></tr>
	<tr style='height:22px;font-weight:bold;background-color:#edeff1;'><td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td style='text-align:center'>Total</td>
											<td style='text-align:right'>$ ".number_format($SumTcargo,2)."</td>
											<td style='text-align:right'>$ ".number_format($SumTabono,2)."</td></tr>";
							} 
								?>
	<!--tr  id='xx' style='text-align:center;font-weight:bold;background-color:#edeff1;font-size:9px;' tipo='total'><td>Totales:</td><td></td><td></td><td></td><td></td><td></td></tr-->
</table>

<?php //Nuevo Commit ?>

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
					<?php if($_GET['tipo']==1){ 
						echo "<form id='formpdf' action='libraries/pdf/examples/polizasImpresion.php' method='post' target='_blank' onsubmit='generar_pdf()'>";
						}else{
						echo "<form id='formpdf' action='libraries/pdf/examples/generaPDF.php' method='post' target='_blank' onsubmit='generar_pdf()'>";	
						}

						?>
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
				<input type='hidden' name='nombreDocu' value='Polizas Impresión'>
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

