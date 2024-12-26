<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script language='javascript'>
$(document).ready(function()
{
	Number.prototype.format = function() {
	    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
	};
	var grupo=''
	var clasif=''
	var suma=0;
	var numero=0;
	var red;
	var cont=0;
	
			//Sumatorias por Grupo*********************************
			$(".Grupo").each(function(index)
			{
				grupo = $(this).attr('id')
				grupo = grupo.split('-')
				for(m=1;m<=12;m++)
				{
					$("td[grupo='"+grupo[1]+"'][mes='"+m+"']").each(function(index)
					{
						numero = $(this).attr('cantidad')
						suma += parseFloat(numero)
						clasif = $(this).attr('Clasif')
					});
					if(m==1)
					{
						$("#sum-"+grupo[1]).append("<td colspan='2' style='font-weight:bold;'>TOTAL "+$('td',this).html()+"</td>");	
					}
					red=''
					if(suma<0) red='color:red;'
					$("#sum-"+grupo[1]).append("<td style='text-align:right;font-weight:bold;"+red+"' class='totalGrupo' Clasif='"+clasif+"' grupo='"+grupo[1]+"' mes='"+m+"' cantidad='"+suma.toFixed(2)+"'>$ "+suma.format()+"</td>");
					suma=0;
				}
			});

			//Sumatorias por Cuentas de Mayor*********************************
			var cantgrup,total,a,g;
			$(".mayor").each(function(index)
			{
				mayor = $(this).attr('id')
				mayor = mayor.split('-')
				for(m=1;m<=12;m++)
				{
					g = $(".totalGrupo[grupo='1'][mes='"+m+"']").attr('cantidad')
					$("td[mayor='"+mayor[1]+"'][mes='"+m+"']").each(function(index)
					{
						numero = $(this).attr('cantidad')
						suma += parseFloat(numero)
						clasif = $(this).attr('Clasif')
						grupo = $(this).attr('grupo')
						
						$(this).attr('title',(parseFloat(numero)/parseFloat(g)*100).toFixed(2)+"%")
					});
					
					red=''
					if(suma<0) red='color:red;'

					total = suma / g * 100

					$("#may-"+mayor[1]).append("<td title='"+total.toFixed(2)+"%' style='text-align:right;font-weight:bold;"+red+"' class='totalGrupo' grupom='"+grupo+"' mes='"+m+"' cantidad='"+suma.toFixed(2)+"'>$ "+suma.format()+"</td>");
					suma=0;
				}
			});

			//Sumatorias por Clasificacion***************************
			$(".Clasificacion").each(function(index)
			{
				clasif = $(this).attr('id')
				//alert(clasif)
				for(m=1;m<=12;m++)
				{
					cont=0
					$("td[clasif='"+clasif+"'][mes='"+m+"'][class='totalGrupo']").each(function(index)
					{
						numero = $(this).attr('cantidad')				
					
						if(clasif == 'Resultados')
						{
							if(cont==0)
							{
								suma += parseFloat(numero)
							}
							else
							{
								suma -= parseFloat(numero)
							}
							cont++
						}
						else
						{
							suma += parseFloat(numero)
						}


					});
					if(m==1)
					{
						$("#sum-"+clasif).append("<td colspan='2' style='font-weight:bold;'>TOTAL "+clasif.toUpperCase()+"</td>");	
					}
					red=''
					if(suma<0) red='color:red;'
					$("#sum-"+clasif).append("<td style='text-align:right;font-weight:bold;"+red+"' cantidad='"+suma+"'>$ "+suma.format()+"</td>");
					suma=0;
				}
			});

			//Sumatoria de Pasivo, Capital y Resultados***************************
			var pasivo=0, capital=0, resultado=0, mes=0, res=0

			for(m=1;m<=12;m++)
			{
				mes = m+1;
				pasivo = $("#sum-Pasivo td:nth-child("+mes+")").attr('cantidad')
				if(typeof pasivo === 'undefined') pasivo=0

				capital = $("#sum-Capital td:nth-child("+mes+")").attr('cantidad')
				if(typeof capital === 'undefined') capital=0

				res = $("#sum-Resultados td:nth-child("+mes+")").attr('cantidad')
				if(typeof res === 'undefined') res=0

				resultado = parseFloat(pasivo) + parseFloat(capital) + parseFloat(res)
				
				red=''
				if(resultado<0) red='color:red;'

				$("#sum-pas-cap").append("<td style='text-align:right;"+red+"'>$ "+resultado.format()+"</td>")

			}

			//Remueve elementos que ya no son necesarios
			$("#sum-").remove();
			$("#sum-0").remove();
			if($("#tipo").val() == '1')
			{
				$("tr[clasif='Resultados']").remove();
			}
			else
			{
				$("#sum-pas-cap").remove();
			}

			$("#may-0").remove()
		
});
function generaexcel()
			{
				$().redirect('views/fiscal/generaexcel.php', {'cont': $('#imprimible').html(), 'name': $('#titulo').val()});
			}
$(document).ready(function(){
	$('#nmloader_div',window.parent.document).hide();	
});			
</script>
<!--COMIENZA-->
<script language='javascript' src='js/pdfmail.js'></script>
<!--TERMINA-->
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
if(intval($_GET['tipo']))
{
	$titulo = "Balance General";
}
else
{
	$titulo = "Estado de Resultados";	
}
?>

<!-- //////////////////////////////////////////////////////  -->
			<!-- BARRA DE HERRAMIENTAS DEL REPOLOG  -->
            <div class="bh">
                <table class="bh" align="right" border="0" style="margin-right:30px;">
                    <tr>            
                        <td width=16 align=right>
                            <a href="javascript:window.print();"><img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0"></a>
                        </td>
                        <td width=16  align=right>
							<a href="index.php?c=reports&f=balanceGeneral&tipo=<?php echo $_GET['tipo']; ?>"> <img src="../../../webapp/netwarelog/repolog/img/filtros.png" 
								title ="Haga clic aqui para cambiar filtros..." border="0"> </a>
						</td>                        
						<td width=16 align=right>
							<a href="javascript:mail();"> <img src="../../../webapp/netwarelog/repolog/img/email.png"  
							   title ="Enviar reporte por correo electrónico" border="0"> 
							</a>
						</td>
						<!--td width=16 align=right>
							<a href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
							   title ="Generar reporte en PDF" border="0"> 
							</a>
						</td-->
						<td width=16 align=right>
							<a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>
						</td>																				
                    </tr>
                </table>
            </div>			
			<!-- //////////////////////////////////////////////////////  --> 
<?php
$moneda=$_POST['moneda'];
if($_POST['valMon']){$valMon=$_POST['valMon'];}else{$valMon=1;}
//$valMon=13.5;
// Manejo de Colores -----------------------------------------------
$titulo1="font-weight:bold;font-size:10px;color:black;background-color:#edeff1;height:30px;";
$subtitulo="font-weight:bold;height:30px;background-color:#fafafa;font-size:9px;";
$total="height:30px;font-weight:bold;background-color:#dbdfe3;font-size:11px;";
?>

<div class="repTitulo"><?php echo $titulo; ?> Periodos</div>	
<br />
<!--div class='descripcion'  style='margin-left:10px;margin-bottom:10px;'><a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a><a href='javascript:generaexcel()' id='excel'><img src='images/images.jpg' width='35px'></a>   <a href="javascript:mail();"><img src="../../../webapp/netwarelog/repolog/img/email.png" title ="Enviar reporte por correo electrónico" border="0" id='email_icon'></a>
	<a href='index.php?c=reports&f=balanceGeneral&tipo=<?php //echo $_GET['tipo']; ?>' onclick="javascript:$('#nmloader_div',window.parent.document).show();" id='filtros' onclick="javascript:$('#nmloader_div',window.parent.document).hide();"><img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'></a></div-->
	<input type='hidden' value='<?php echo $titulo; ?> Periodos.' id='titulo'>
	<div id='imprimible'>
	
	

	<!--table width='95%'>
		<tr>
			<td colspan='3'><b> a 12 meses de: </b> <?php //echo $empresa; ?>&nbsp;&nbsp;&nbsp;<b>Del Ejercicio</b> <?php echo $ej; ?></td>
		</tr>
	</table-->
<!--Titulo congelado-->
<!--INICIA-->
<table border=0 width='2120' style='background:white;font-size:10px;'>
<tr style='background:white;'><td width:50%;>

			<?php
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<img id='logo_empresa' src='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'>
		</td><td width:50%;></td></tr>
		<tr style='background:white;'><td colspan='2' style='text-align:center;font-size:18px;'><b style='text-align:center;'><?php echo $empresa;?></b></td></tr>
               <tr style='background:white;'> 	<td style="text-align:center;color:#576370;font-size:12px;" colspan='2'>
									<b style='font-size:15px;'><?php echo $titulo; ?> a 12 Periodos</b><br>
									Ejercicio: <b><?php echo $ej;?> </b>Sucursal <b><?php echo $nomSucursal; ?></b> Segmento <b><?php echo $nomSegmento; ?></b> 
									<?php if($valMon>1){echo "<br>Moneda <b>$moneda</b> Tipo de Cambio <b>$ $valMon </b> ";}?>
									<br><br>
				
				</td>
	</tr>
</table>

<table border=0 width='2120' style="background:white;">
	<!--Logo en las tablas de aqui-->
			
		<!--Logo en las tablas hasta aqui-->
	<tr style='<?php echo "$total"; ?>'><td style='text-align:left;width:100px;'>CODIGO</td><td style='text-align:left;width:220px;'>CUENTA DE MAYOR</td><td style='width:150px;'>ENERO</td><td style='width:150px;'>FEBRERO</td><td style='width:150px;'>MARZO</td><td style='width:150px;'>ABRIL</td><td style='width:150px;'>MAYO</td><td style='width:150px;'>JUNIO</td><td style='width:150px;'>JULIO</td><td style='width:150px;'>AGOSTO</td><td style='width:150px;'>SEPTIEMBRE</td><td style='width:150px;'>OCTUBRE</td><td style='width:150px;'>NOVIEMBRE</td><td style='width:150px;'>DICIEMBRE</td></tr>
</table>
<div style='width:2120px;height:400px;overflow:scroll;background:white;'>
<!--TERMINA-->
	<table border=0 width='2120'>
		<tr style='text-align:right;font-weight:bold;height:5px;'><td style='text-align:left;width:100px;'></td><td style='text-align:left;width:220px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td><td style='width:150px;'></td></tr>
		<?php
		function startsWith($haystack, $needle) 
				{
				    // search backwards starting from haystack length characters from the end
				    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
				}
		$codigoAnterior='';
		$grupoAnterior='';
		$grupoCont = $number = 0;
		$clasifAnterior='';
			while($d = $datos->fetch_object())
			{
		$CM = explode(' / ',$d->Cuenta_de_Mayor,2);
				//Si es a modo detalle pinta la cuenta de mayor sino el nombre de la afectable
					if(intval($_POST['detalle']))
					{
						$account = $d->Cuenta;
					}
					else
					{
						$account = $CM[1];
					}

				$title = $d->Grupo;
				if(startsWith($d->Grupo,'RESULTADO ACREEDOR') || startsWith($d->Grupo,'RESULTADOS ACREEDOR'))
					{
						$d->Grupo = "INGRESOS";	
					}

					if(startsWith($d->Grupo,'RESULTADO DEUDOR') || startsWith($d->Grupo,'RESULTADOS DEUDOR'))
					{
						$d->Grupo = "EGRESOS";	
					}
					//Si los siguientes meses no tienen valores rellena con ceros
					//INICIA
					if($codigoAnterior != $d->Codigo)
					{
						if(intval($mesAnterior) < 12 && intval($mesAnterior > 0))
						{
							for($m=intval($mesAnterior)+1;$m<=12;$m++)
							{
								echo "<td style='text-align:right;'>$ 0.00</td>";
							}
							echo "</tr>";
						}
					}
					//TERMINA
					if($d->Cuenta_de_Mayor != $mayorAnterior AND intval($_POST['detalle']))
						{	
							echo "<tr style='border-top:1px solid black;color:gray;font-weight:bold;text-align:right;' id='may-$number'><td colspan='2' style='text-align:left;'>TOTAL $mayorAnterior</td></tr>";
						}
				if($clasifAnterior != $d->Clasificacion)
				{
					if($grupoAnterior != $d->Grupo)
					{
						$grupoCont++;
						echo "<tr id='sum-".($grupoCont-1)."' style='$subtitulo' clasif='$clasifAnterior'></tr>";	
					}
					
					echo "<tr id='sum-$clasifAnterior' style='$titulo1'></tr>";
					echo "<tr style='height:20px;'><td colspan='14'></td></tr>";	
					echo "<tr id='$d->Clasificacion' class='Clasificacion' style='$titulo1'><td colspan='14' >".strtoupper($d->Clasificacion)."</td></tr>";
					
					if($grupoAnterior != $d->Grupo)
					{
						echo "<tr id='grupo-$grupoCont' class='Grupo' clasif='$d->Clasificacion' style='$subtitulo'><td colspan='14' >$title</td></tr>";	
					}
				}
				else
				{
					if($grupoAnterior != $d->Grupo)
					{
						$grupoCont++;
						echo "<tr id='sum-".($grupoCont-1)."' style='$subtitulo' clasif='$d->Clasificacion'></tr>";
						echo "<tr style='height:5px;'><td colspan='14'></td></tr>";		
						echo "<tr id='grupo-$grupoCont' style='$subtitulo' class='Grupo' clasif='$d->Clasificacion'><td colspan='14' >$title</td></tr>";	
					}
				}
				
				if($d->Clasificacion != 'Activo' && $d->Grupo != 'EGRESOS')
					{
						$d->CargosAbonos = $d->CargosAbonos*-1;
					}
				
				$red='';
				if(floatval($d->CargosAbonos)<0) $red='color:red;';
				
				if($codigoAnterior != $d->Codigo)
				{

					if($d->Cuenta_de_Mayor != $mayorAnterior AND intval($_POST['detalle']))
						{	
							//echo "<tr style='border-top:1px solid black;color:gray;font-weight:bold;text-align:right;' id='may-$number'><td colspan='2' style='text-align:left;'>Total $mayorAnterior</td></tr>";
							$number++;
							echo "<tr style='color:gray;font-weight:bold;height:50px;' class='mayor' id='mayor-$number'><td colspan='2'>$d->Mayor / $d->Cuenta_de_Mayor</td></tr>";
						}

					echo "<tr clasif='$d->Clasificacion'><td style='mso-number-format:\"@\";'>".$d->Codigo."</td><td>".$account."</td>";
					for($m=1;$m<intval($d->Mes);$m++)
					{
						echo "<td style='text-align:right;'>$ 0.00</td>";
					}

					echo "<td clasif='$d->Clasificacion' mayor='$number' grupo='$grupoCont' mes='".intval($d->Mes)."' style='text-align:right;$red' cantidad='".number_format(($d->CargosAbonos/$valMon),2,'.','')."'>$ ".number_format(($d->CargosAbonos/$valMon),2)."</td>";
				}
				else
				{
					//SI entre un registro y otro pasa mas de un mes sin nada entonces rellena con ceros
					//INICIAL
					if((intval($d->Mes) - intval($mesAnterior)) > 1 )
					{
						for($m=$mesAnterior;$m<intval($d->Mes-1);$m++)
						{
							echo "<td style='text-align:right;'>$ 0.00</td>";
						}
					}
					//TERMINA
					echo "<td title='' clasif='$d->Clasificacion' mayor='$number' grupo='$grupoCont' mes='".intval($d->Mes)."' style='text-align:right;$red' cantidad='".number_format(($d->CargosAbonos/$valMon),2,'.','')."'>$ ".number_format(($d->CargosAbonos/$valMon),2)."</td>";
					if(intval($d->Mes)==12)
					{
						echo "</tr>";	
					}
				}
				$mesAnterior 	= $d->Mes;
				$codigoAnterior = $d->Codigo;
				$grupoAnterior 	= $d->Grupo;
				$clasifAnterior = $d->Clasificacion;
				$mayorAnterior 	= $d->Cuenta_de_Mayor;
			}
			echo "<tr style='border-top:1px solid black;color:gray;font-weight:bold;text-align:right;' id='may-$number'><td colspan='2' style='text-align:left;'>Total $mayorAnterior</td></tr>";
			
			if(intval($_GET['tipo'])){
				echo "<tr id='sum-$grupoCont' style='height:30px;' clasif='$clasifAnterior'></tr>";	
				echo "<tr id='sum-$clasifAnterior' style='$subtitulo'></tr>";
				echo "<tr style='height:7px;'><td colspan='14'></td></tr>";
				}	else{
				echo "<tr id='sum-$grupoCont' style='$subtitulo' clasif='$clasifAnterior'></tr>";	
				echo "<tr style='height:7px;'><td colspan='14'></td></tr>";
				echo "<tr id='sum-$clasifAnterior' style='$total'></tr>";
					}
			echo "<tr id='sum-pas-cap' style='$total'><td colspan='2'>TOTAL PASIVO, CAPITAL Y RESULTADOS</td></tr>";	
		?>
</table>
<!--INICIA TITULO CONGELADO-->
</div>
<!--TERMINA-->
</div>
<input type='hidden' value='<?php echo $_GET['tipo']; ?>' id='tipo'>
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