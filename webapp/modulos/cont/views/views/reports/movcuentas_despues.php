
<html lang="sp">
	<head>
        <!--LINK href="../../../webapp/netwarelog/utilerias/css_repolog/estilo-1.css" title="estilo" rel="stylesheet" type="text/css" / -->
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--TITULO VENTANA-->
		<title>Movimientos por Cuentas</title>
		<meta name="generator" content="Netbeans">
		<meta name="author" content="Omar Mendoza"><!-- Date: 2010-08-07 -->
		<meta name="author-icons" content="Rachel Fu"><!-- Date: 2010-08-07 -->

        <!--PLUG IN CATALOG-->
        <script type="text/javascript" src="../../../webapp/netwarelog/catalog/js/jquery.js"></script>		

		<script type="text/javascript">
			
			function pdf(){
				/*	
				var anchopadre = document.getElementById("tabla_reporte").parent.style.width;
				alert(anchopadre);
				var anchotabla = document.getElementById("tabla_reporte").style.width;
			  var pleft = (anchopadre / 2)+(anchotabla/2);
				alert(pleft);
				*/
				var contenido_html = $("#idcontenido_reporte").html();
				//contenido_html = contenido_html.replace(/\"/g,"\\\"");
				$("#contenido").attr("value",contenido_html);	
				
				$("#divpanelpdf").fadeIn(500);
			}
			function generar_pdf(){
				$("#divpanelpdf").fadeOut();
				//$("#loading").fadeIn(500);
			}
			function cancelar_pdf(){
				$("#divpanelpdf").fadeOut();
			}

			function pdf_generado(){
				alert("OK");
			}
			//$('#frpdf').load(function() {
  		//	alert("the iframe has been loaded");
			//});	
			//document.getElementById("frpdf").onload = function() {
  		//	alert("myframe is loaded");
			//};
	
			//$('#frpdf').ready(function () {
			//  alert("perfecto");
			//});

			function mail(){
				var msg = "Registre el correo electrónico a quién desea enviarle el reporte:";
				var a = prompt(msg,"@netwaremonitor.com");
				if(a!=null){
					var html_contenido_reporte;
					html_contenido_reporte = $("#idcontenido_reporte").html();
					$("#loading").fadeIn(500);
					$("#divmsg").load("../../../webapp/netwarelog/repolog/mail.php?a="+a, {reporte:html_contenido_reporte});
				}
			}	
		</script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function(){
	
	$('table.bh tbody tr').append("<td><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a></td>")

	//$("th:contains('Subtotal [')").text('Subtotal').next().next().after("<th style='border:solid 1px;background-color:#efefef;text-align:left;font-size:12px;'></th>");
	//$("th:contains('TOTAL')").text('Total de la Cuenta').next().next().after("<th style='border:solid 1px;background-color:#efefef;text-align:left;font-size:12px;'></th>");
});	
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $("#idcontenido_reporte").html(), 'name': $("#titulo").val()});
			}
</script>
	</head>

	<body>
<?php //Nuevo Commit
						$url = explode('/modulos',$_SERVER['REQUEST_URI']);
						if($logo == 'logo.png') $logo = 'x.png';
						$logo = str_replace(' ', '%20', $logo);
						?>
			<!-- PDF -->
			<!--<iframe name="frpdf" style="width:600px;height:400px;"
onload="pdf_generado()"></iframe>-->
			
			<div 
				id="divpanelpdf"
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
				<input type='hidden' name='nombreDocu' value='movimientos_cuentas'>
				<input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
				<input type="submit" value="Crear PDF" autofocus >
				<input type="button" value="Cancelar" onclick="cancelar_pdf()">
				
				</center>
				</form>
			</div>

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

		
			<!-- //////////////////////////////////////////////////////  -->
			<!-- BARRA DE HERRAMIENTAS DEL REPOLOG  -->
            <div class="iconos">
                <table align="right" border="0" >
                    <tr>            
                        <td width=16 align=right>
                            <a href="javascript:window.print();"><img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0"></a>
                        </td>
                        <td width=16  align=right>
							<a href="index.php?c=Reports&f=movcuentas"> <img src="../../../webapp/netwarelog/repolog/img/filtros.png" 
								title ="Haga clic aqui para cambiar filtros..." border="0"> </a>
						</td>  
						<td width=16  align=right>
						<a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>
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
                    </tr>
                </table>
            </div>			
			<!-- //////////////////////////////////////////////////////  -->            

<div class="repTitulo">Reporte de Movimientos por Cuentas</div>

<FORM id="reporte" name="reporte"  method="post" action="redirecciona.php">

	<input type='hidden' value='Movimiento por Cuentas.' id='titulo'>
<center>
	
<div id="idcontenido_reporte" style='widht:100%'>
	
			<table style="border:none; width:100%;background-color:#ffffff;">
				<tr>
					<td style="width:50%">
						
						<!--img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'-->
					</td>
					<td valign="top" width='50%' style="font-size:7px;text-align:right;color:gray;">
					<b>Fecha de Impresión<br><?php echo date("d/m/Y H:i:s"); ?></b><br>
					</td>
				</tr>
				<tr style='text-align:center;color:#576370;font-size:12px;'>
					<td colspan='2'>
						<b style="font-size:18px;color:black;"><?php echo $empresa;?></b><br>
						<b style='font-size:16px;'>Movimientos por Cuentas</b><br>
						Del <b><?php echo $fecha_antes;?> </b>Al <b> <?php echo $fecha_despues;?></b>
						<br><br>
					</td>
				
          		</tr>
					<!--</table>-->

					<tr><td colspan='2' >

					<!-- TABLA REPORTE --> 
					<table  style="width:100%;max-width:1000px;text-align:center;font-size:9px;" id="tabla_reporte" class="reporte" border="0" align="center" >
                
                    
            	
                <tr style="background-color:#d2d7dc;height:30px;color:black;font-size:10px;font-weight:bold;">
                	<td style="width:8%;">Fecha</td>
                	<td style="width:7%;">No. de Cuenta</td>
                	<td style="width:7%;">Tipo de Poliza</td>
                	<td style="width:10%;">Poliza</td>
                	<td style="width:9%;">Concepto de Poliza</td>
                	<td style="width:11%;">Concepto de Movimiento</td>
                	<td style="width:11%;">Referencia de Movimiento</td>
                	<td style="width:8%;">Segmento de Negocio</td>
                	<td style="width:10%;">Cargos</td>
                	<td style="width:9%;">Abonos</td>
                	<td style="width:10%;">Saldo</td>
                </tr>
	
                <!--Separador****|****-->
				<!--Separador**|**-->
<?php
$cont=0;
$cantidadMovs=$datos->num_rows;
while($info = $datos->fetch_object())
{

if($anterior != $info->account_id)
		{
			
			if($cont!=0)
				{
					echo "<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='background-color:#dbdfe3'></td>
					<td style='background-color:#dbdfe3';><strong>Saldo Despues:</strong></td>
					<td style='background-color:#dbdfe3'> ".number_format($this->ReportsModel->Saldos($anterior,$fecha_despues,'Despues'),2)."</strong></td>
					</tr><!--Separador**|**-->";
				}
			if(intval($_REQUEST['tipo']))
			{
				$NM = explode("/",$info->NombreMayor);	
				echo "<tr class='otros' style='background-color:#edeff1;height:30px;' tipo='Mayores'>
				<td colspan=2>Cuenta Mayor: <b>$NM[0]</b></td>
				<td colspan=5><b>$NM[1]</b></td>
				<td></td>
				<td colspan=3></td></tr>";	
			}

			echo "<tr class='otros' style='background-color:#edeff1;height:30px;' tipo='Cuentas'>
			<td colspan=2>Cuenta: <b>$info->Codigo_Cuenta</b></td>
			<td colspan=5><b>$info->Descripcion_Cuenta</b></td>
			<td></td>
			<td></td><td><strong>Saldo Antes: </strong></td><td>".number_format($this->ReportsModel->Saldos($info->account_id,$fecha_antes,'Antes'),2)."</strong></td>
			</tr>";
		}
if(strtotime($info->Fecha) >= strtotime($fecha_antes))
{


echo "
<tr class='' style:'font-size:8px;' tipo='movimientos'>
<td class='tdcontenido' title='Fecha'>".$info->Fecha."</td>
<td class='tdcontenido' title='No_Cuenta'>".$info->Codigo_Cuenta."</td>
<td class='tdcontenido' title='Tipo_Poliza'>".$info->Tipo_Poliza."</td>
<td class='tdcontenido' title='Numero_Poliza'>#".$info->Numero_Poliza." / Periodo ".$info->Periodo."</td>
<td class='tdcontenido' title='Concepto_Poliza'>".$info->Concepto_Poliza."</td>
<td class='tdcontenido' title='Concepto_Movimiento'>".$info->Concepto_Movimiento."</td>
<td class='tdcontenido' title='Referencia_Movimiento'>".$info->Referencia_Movimiento."</td>
<td class='tdcontenido' title='Segmento'>".$info->Segmento."</td>
<td class='tdcontenido' title='Cargos'>".$info->Cargos."</td>
<td class='tdcontenido' title='Abonos'>".$info->Abonos."</td>
<td class='tdcontenido' title='CargosTitulo' style='text-align:right;'>".number_format($info->Cargos,2)."</td>
<td class='tdcontenido' title='AbonosTitulo' style='text-align:right;'>".number_format($info->Abonos,2)."</td>
<td class='tdcontenido' title='Saldo' style='text-align:right;'>".number_format($info->SaldoDespues,2)."</td></tr>";

}


//$tercer = $anterior;
$anterior = $info->account_id;
$anteriorMayor = $info->main_father;;
//$saldo_antes = $info->SaldoAntes;
//$saldo_despues = $info->SaldoDespues;
$cont++;
if($cont == $cantidadMovs)
				{
					echo "<tr id='sub-$info->account_id' style='font-weight:bold;background-color:#f6f7f8;height:30px;' tipo='subtotal' align='right'></tr>";
					echo "<tr><td colspan='8'></td><td colspan=2 style='background-color:#ccc';color:white;><strong>Saldo Despues:</strong></td><td> ".number_format($this->ReportsModel->Saldos($anterior,$fecha_despues,'Despues'),2)."</strong></td></tr>";
				}
}
?>                
              
              <tr  id='xx' style='font-weight:bold;background-color:#d2d7dc;' tipo='total' align='right'><td colspan='8'>Totales:</td></tr>  
            
            </table>
						</td></tr></table><!--TABLA DESDE LOS FILTROS  -->

</div>
 <!-- idcontenido_reporte -->
</center>

        </body>
        <script language='javascript'>
$(document).ready(function()
{

	//Proceso que recorre los valores de los elementos y los suma. 
	//Cuando termina agrega los elementos con los resultados
	
	var total = 0;
	var subtotal = 0;
	for(var f = 9; f <= 10; f++)//Recorre cada columna
	{
		$('#xx').append("<td id='"+f+"'></td>");//Anexa un elemento a la tabla
		$("table #tabla_reporte tr").each(function(index)//Recorre cada fila
		{
			if($(this).attr('tipo') == 'movimientos')//Si es un elemento de tipo numero hace la suma de los resulados
			{
				total += parseFloat($("td:nth-child("+f+")",this).text())//Sumatoria total
				subtotal += parseFloat($("td:nth-child("+f+")",this).text())//Sumatoria del proveedor
			}

			if($(this).next().attr('tipo') == 'subtotal' && $(this).next().next().attr('tipo') != 'movimientos')//Si es un campo del tipo subtotal y el siguiente elemento no es de tipo numero entonces agrega elementos
				{
					if(f==9)//si se trata del primer barrido agraga td`s de relleno a la tabla
					{
						$(this).next().append("<td colspan='8'>Subtotal:</td>");
					}
					
					if(isNaN(subtotal)){subtotal=0;}

					$(this).next().append("<td>"+subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})+"</td>");//Agrega la suma del subtotal
					subtotal=0;	//se reinicia la suma
				}
				
				if($(this).next().attr('tipo') == 'subtotal' && $(this).next().next().attr('tipo') == 'movimientos')//Si es un campo del tipo subtotal y el siguiente elemento no es de tipo numero entonces agrega elementos
				{
					$(this).next().remove()
				}
		});	
		
		if(isNaN(total)){total=0;}
		
		$("#"+f).text(total.toLocaleString('en-US', {minimumFractionDigits: 2}));//Agrega el total al elemento
		total = 0;
	}
	//$('[tipo=subtotal]').append('<td></td>')
	$('[tipo=total]').append('<td></td>')
	$('.tdcontenido:nth-child(9)').remove()
	$('.tdcontenido:nth-child(9)').remove()
	$('.tdencabezado:nth-child(8)').remove()
	$('.tdencabezado:nth-child(8)').remove()

});
</script>
        		
</html>
