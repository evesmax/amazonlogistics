<?php
 $url=str_replace('contenido','contenidoexcel',$_SERVER["REQUEST_URI"]);
?>
<!DOCTYPE html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
		<script language='javascript' src='js/pdfmail.js'></script>
		<script type="text/javascript" src="js/jquery.js"></script>
		<link rel="stylesheet" href="css/style.css" type="text/css">
	
	<style type="text/css" >
			 .tdlink {border: 1px solid  #424242; cursor: pointer}
			 .detalle {text-decoration:none; color:#000000;}
			 .prove {font-weight: bold; height:30px !important;}
			 .total {font-weight: bold;background:#424242; }
			 .cierre {font-weight: bold;background:#424242; color:#F2FBEF;}
			 .nodato {font-size:14px; text-align: center;	height: 34px; font-weight:bold;}
			</style>
	</head>

	<?php 
$titulo1="font-size:10px;background-color:#f6f7f8;font-weight:bold;height:30px;";
$subtitulo="font-size:10px;font-weight:bold;height:30px;background-color:#fafafa;text-align:left;margin-left:10px;"

?>
		<div class="iconos">
		<a href="javascript:window.print();">
		<img  border="0" src="../../netwarelog/design/default/impresora.png" width="20px">
		</a>
		<b style="font-size:16px; color:#6E6E6E; text-align: center;">
					<td width="16" align="right">
				 <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a> 
				</td>
				<td width="16" align="right">
				<a href="javascript:mail();">
				<img border="0" title="Enviar reporte por correo electrónico" src="../../../webapp/netwarelog/repolog/img/email.png">
				</a>
				</td>
				<td>
					<a id="filtros" href="index.php?c=ConcentradoIVAProveedor&f=verconcentrado" onclick="">
						<img border="0" src="../../netwarelog/repolog/img/filtros.png" title="Haga click aquí para cambiar los filtros...">
					</a>
				</td>
				<td>
						<img src="images/images.jpg" title="Exportar a Excel" onclick="window.open('<?php echo $url; ?>')" width="25px" height="25px"> 

				</td>
		</div>
		<div class="repTitulo">Concentrado de IVA por Proveedor</div>
		<div id="tabla">
		
<body>
<div id='imprimible'>
<table style="width:100%;">
	<tr><td style="width:50%;">
			<?php
			$url = explode('/modulos',$_SERVER['REQUEST_URI']);
			if($logo == 'logo.png') $logo = 'x.png';
			$logo = str_replace(' ', '%20', $logo);
			?>
			<!--img id='logo_empresa' src='<?php //echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' height='55'-->
		</td>
		<td style="width:50%;font-size:7px;text-align:right;color:gray;">Fecha de impresión: <label id="fech"><?php echo $fecha; ?></label></td></tr>
	<tr style="font-size:12px;color:#576370;text-align:center;"><td colspan="2" >
		<b style="font-size:18px;color:black;"><?php echo $empresa; ?></b><br>
		<b style="font-size:15px;">Concentrado de IVA por Proveedor</b><br>
		<b id="periodo"><?php echo $periodo; ?></b>
		<br><br>
	  </td></tr>
	  <tr><td colspan="2">
	  	</td></tr>
</table>
	<table align="center" class="busqueda" id="datos" width="100%" cellpadding="3" style="font-size:9px;" >
			<thead>
				<tr style='<?php echo $titulo1; ?>'>
				<th>Importe Base</th>
				<th>Otros</th>
				<th>Tasa</th>
				<th>IVA Acreditable</th>
				<th>Importe Antes Retenciones</th>
				<th>IVA Retenido</th>
				<th>ISR Retenido</th>
				<th>Total Erogacion</th>
				<th>IVA Pagado no Acreditable</th>
				</tr>
			</thead>
			<tbody><?php 
			$cont=0;
			foreach ($dato as $key => $d) {
				//var_dump($taenviar);
				// print_r($taenviar[ $d['razon_social']]);
				// $ca='index.php?c=auxiliar_controlIva&f=VerReporte&fecha_ini='.$inicio.'&fecha_fin='.$fin.'&periodoAcreditamiento=1&periodo_inicio='.$p1.'&periodo_fin='.$p2.'&pinicial='.$d['idProveedor'].'&pfinal=';
				// $ca.=$d['idProveedor'].'&ejercicio='.$e.'&prov=1&noAplica=0&tasas[]='.$taenviar[ $d['razon_social']];
							// //print_r($taenviar[$d['razon_social']]);  
				
				
				 ?>
			
				
				
				<tr  style="background-color:#edeff1;height:30px;" >
					<td colspan="3" style="text-align:center;"><?php echo $d['razon_social']; ?></td>
					<td colspan="2" style="text-align:center;">RFC: <?php echo$d['rfc']; ?></td>
					
					<td colspan="2" style="text-align:center;">CURP: <?php echo $d['curp']; ?></td>
					<td colspan="2" style="text-align:center;"><?php echo $d['tipotercero']; ?></td>
				</tr>
			
			<?php	foreach ($d['tasas'] as $key => $value) {
				
						if($value==0 ){
							if($muestra==1){  ?>
								
								<tr >
								<td align="right">0</td>
								<td align="right">0</td>
								<td align="right"><?php echo $key; ?></td>
								<td align="right">0</td>
								<td align="right">0</td>
								<td align="right">0</td>
								<td align="right">0</td>
								<td align="right">0</td>
								<td align="right">0</td>
								</tr>
							
		    <?php 	    	}
						}else{
						if($value['tasa']=='Otra Tasa 1' || $value['tasa']=='Otra Tasa 2'){
							$tasa=$value['tasa'].'('.$value['valor'].'%)';
						}else{
							$tasa=$value['tasa'];
						} ?>
               
	
				<tr class="prove" style="background-color:#f6f7f8;font-size:9px;">
					 <td align="right"><?php echo number_format($value['importeBase'],2,'.',',');?></td>
					 <td align="right"><?php echo number_format($value['otraserogaciones'],2,'.',',');?></td>
					 <td align="right"><?php echo $tasa;?></td>
					 <td align="right"><?php echo number_format($value['acredita'],2,'.',',');?></td>
					 <td align="right"><?php echo number_format($value['importeBase']+$value['otraserogaciones']+$value['acredita'],2,'.',',');?></td>
					 <td align="right"><?php echo number_format($value['ivaRetenido'],2,'.',',');?></td>
					 <td align="right"><?php echo number_format($value['isrRetenido'],2,'.',',');?></td>
					 <td align="right"><?php echo number_format($value['totalerogacion'],2,'.',',');?></td>
					 <td align="right"><?php echo number_format($value['ivaPagadoNoAcreditable'],2,'.',',');?></td>
					 </tr>
		<?php		}//else
				if($muestra==1){
					if($value['tasa']=='Otra Tasa 1'){
						$o1='';
					}else{
						$o1= '
						<tr style="background-color:#fafafa;">
							<td align="right">0</td>
							<td align="right">0</td>
							<td>Otra Tasa 1</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
						</tr>';
					}
					if($value['tasa']=='Otra Tasa 2'){
						$o2='';
					}else{
						$o2='
						<tr style="background-color:#fafafa;">
							<td align="right">0</td>
							<td align="right">0</td>
							<td>Otra Tasa 2</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
							<td align="right">0</td>
						</tr>';
					}
					
				}//else{ $o1=""; $o2="";}
			}		
					echo @$o1.@$o2;	
// 					
					
// 										
					foreach ($d['suma'] as $key => $d3) {
					if($d3!=''){ 
						?>
					<tr>
						 <td colspan="9">
						 
						 </td>
						 </tr>
						 <tr style="font-size:9px;font-weight:bold;text-align:right;background-color:#edeff1;height:30px;">
						<?php 
						// $ca='index.php?c=auxiliar_controlIva&f=VerReporte&fecha_ini='.$inicio.'&fecha_fin='.$fin.'&periodoAcreditamiento=1&periodo_inicio='.$p1.'&periodo_fin='.$p2.'&pinicial='.$d['idProveedor'].'&pfinal=';
				// $ca.=$d['idProveedor'].'&ejercicio='.$e.'&prov=1&noAplica=0&tasas[]='.$taenviar[ $d['razon_social']];
				if(!isset($inicio)){
					$inicio=0;
					$fin=0;
					
				}if(!isset($p1)){
					$p1=0;
					$p2=0;
				}
				$t=($taenviar[ $d['razon_social'] ]);
				
						 echo "<td  align='right' onclick='javascript:manda(".$inicio.",".$fin.",".$p1.",".$p2.",".$d['idProveedor'].",".$e.",".$periodoAcreditamiento.",".json_encode($t).")'>".number_format($d3["importeBase"],2,'.',',')."</td>";
						 ?>
						 <td align="right"><?php echo number_format($d3['otraserogaciones'],2,'.',',');?></td>
						 <td></td>
						 <td align="right"><?php echo number_format($d3['acredita'],2,'.',',');?></td>
						 <td align="right"><?php echo number_format($d3['importeBase']+$d3['otraserogaciones']+$d3['acredita'],2,'.',',');?></td>
						 <td align="right"><?php echo number_format($d3['ivaRetenido'],2,'.',',');?></td>
						 <td align="right"><?php echo number_format($d3['isrRetenido'],2,'.',',');?></td>
						 <td align="right"><?php echo number_format($d3['totalerogacion'],2,'.',',');?></td>
						 <td align="right"><?php echo number_format($d3['ivaPagadoNoAcreditable'],2,'.',',');?></td>
				</tr>
	<?php				}
				}
 					 
				
				$cont++;	
					
		}
?>
			</tbody>
		</table>



	</div>
		</div>
		<div id="r"></div>
	
		
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
				<input type='hidden' name='nombreDocu' value='Concentrado de iva por proveedor'>
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
	</body>
	<script>
		function manda(fecha_ini,fecha_fin,periodo_inicio,periodo_fin,pinicial,ejercicio,periodoAcreditamiento,tasas){
			$.post('ajax.php?c=auxiliar_controlIva&f=VerReporte',
			{fecha_ini:fecha_ini,
			 fecha_fin:fecha_fin,
			 periodo_inicio:periodo_inicio,
			 periodo_fin:periodo_fin,
			 pinicial:pinicial,
			 pfinal:pinicial,
			 ejercicio:ejercicio,
			 porProv:'1',
			 noAplica:'0',
			 tasas:tasas,
			 prov:'algunos',
			 periodoAcreditamiento:periodoAcreditamiento
			},function(respues){
			  $("#tabla").hide();
			  $("#r").show()
			  $("#r").html('<br><input type="button" style="margin:0 auto;color: red;border-bottom-color: blue;" onclick="javascript:regre();" value="Regresar">');

              $("#r").append(respues);
			});
		}
		function regre(){
			$("#tabla").show();
			 $("#r").hide();
		}
	</script>

	</html>