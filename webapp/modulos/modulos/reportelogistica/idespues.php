    <?PHP 
		//Sql Inicializa
		$sql="";
		$linea="";
		//Inicializa Variables de Totales
		$totales=array("sub","tot");
		//Inicializa Arreglo Totalesx
		$totales=intotales($totales,5,"sub",0);
		$totales=intotales($totales,5,"tot",0);
		
		$sql=$_SESSION["sequel"];
		$idreporte=$_SESSION["repolog_idreporte"];
		//Si es 13 el numero de reportes calcula totales Ventas
		$resultado = $conexion->consultar($sql);
		while($rs = $conexion->siguiente($resultado)){
			if($idreporte==13){
				//0=Saldo Inicial, 1=Retirada, 2=Devuelta, 3=Saldo
				  $totales["tot"][0]+=str_replace(',','',$rs{"Saldo Inicial (TM)"});
				  $totales["tot"][1]+=str_replace(',','',$rs{"Retirada (TM)"});
				  $totales["tot"][2]+=str_replace(',','',$rs{"Devuelta (TM)"});
				  $totales["tot"][3]+=str_replace(',','',$rs{"Cancelado (TM)"});
				  $totales["tot"][4]+=str_replace(',','',$rs{"Saldo (TM)"});
			}elseif ($idreporte==12){
				//0=Inicial, 1=Retirada, 2=Saldo
				  $totales["tot"][0]+=str_replace(',','',$rs{"Saldo Inicial (TM)"});
				  $totales["tot"][1]+=str_replace(',','',$rs{"Retirada (TM)"});
				  $totales["tot"][2]+=str_replace(',','',$rs{"Saldo (TM)"});				
			}  
		}        
		$conexion->cerrar_consulta($resultado);
		//Dibujando Totales
		if($idreporte==13){
			//0=Saldo Inicial, 1=Retirada, 2=Devuelta, 3=Saldo
			  $linea="<table class='reporte' align=center>                  
				<tr class='trsubtotal'  align=right>
					<td class=tdsubtotal>Saldo Inicial (TM):<br>".number_format($totales["tot"][0],3)."</td>
					<td class=tdsubtotal>Retirada (TM):<br>".number_format($totales["tot"][1],3)."</td>
					<td class=tdsubtotal>Devuelta (TM):<br>".number_format($totales["tot"][2],3)."</td>
					<td class=tdsubtotal>Cancelado (TM):<br>".number_format($totales["tot"][3],3)."</td>
					<td class=tdsubtotal>Saldo (TM):<br>".number_format($totales["tot"][4],3)."</td>
				</tr>
					</table>";
		}elseif ($idreporte==12){
			//0=Inicial, 1=Retirada, 2=Saldo
			  $linea="<table class='reporte' align=center>                  
				<tr class='trsubtotal' align=right>
					<td class=tdsubtotal>Saldo Inicial (TM):<br>".number_format($totales["tot"][0],3)."</td>
					<td class=tdsubtotal>Retirada (TM):<br>".number_format($totales["tot"][1],3)."</td>
					<td class=tdsubtotal>Saldo (TM):<br>".number_format($totales["tot"][2],3)."</td>
				</tr></Table>";			
		}  
		echo $linea;

        //Establece fecha del Dia
        $sfechadia=date("Y-m-d");
            $dia=date("d");
            $mes=date("m");
            $año=date("Y");
       
        
    ?>


                <!--CSS-->
                <LINK href="../../netwarelog/utilerias/css_repolog/estilo-1.css" title="estilo" rel="stylesheet" type="text/css" />
                <LINK href="../../netwarelog/catalog/css/view.css" title="estilo" rel="stylesheet" type="text/css" />
                <!--JS-->
		<script type="text/javascript" src="../../netwarelog/catalog/js/view.js"></script>
		<script type="text/javascript" src="../../netwarelog/catalog/js/calendar.js"></script>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--TITULO VENTANA-->
                <br><br><br>
				<center>Filtros Detallado</center>
                <center>
                    <table class="reporte">
                            <tr class="trcontenido">
                                <input name="txt1" type="hidden" value="1">
                            </tr>
                            <tr class="trcontenido">
                                <input name="txt2" type="hidden" value="1">
                            </tr>

                            <tr class="trcontenido">
                                <td class="tdcontenido">
                                    Del:</td>
                                <td class="tdcontenido">
                                    <input id='f1_3' name='f1_3' title='Día' size='2' maxlength='2' value='<?php echo $dia?>' type='text'> /
                                    <input id='f1_2' name='f1_2' title='Mes' size='2' maxlength='2' value='<?php echo $mes?>' type='text'> /
                                    <input id='f1_1' name='f1_1' title='Año' size='4' maxlength='4' value='<?php echo $año?>' type='text'>
                                    &nbsp;<img id='f1_img' class='datepicker' src='img/calendar.gif' 
                                    alt='Seleccione una fecha.' title='Haga clic para seleccionar una fecha.'>&nbsp;
                                        <script type='text/javascript'>
                                                Calendar.setup({
                                                        inputField	 : 'f1_1',
                                                        baseField    : 'f1',
                                                        displayArea  : 'f1_area',
                                                        button		 : 'f1_img',
                                                        ifFormat	 : '%B %e, %Y',
                                                        onSelect	 : selectDate
                                                });
                                        </script>
                                </td>

                                <td class="tdcontenido">
                                    Al:
                                </td>
                                <td class="tdcontenido">
                                    <input id='f2_3' name='f2_3' title='Día' size='2' maxlength='2' value='<?php echo $dia?>' type='text'> /
                                    <input id='f2_2' name='f2_2' title='Mes' size='2' maxlength='2' value='<?php echo $mes?>' type='text'> /
                                    <input id='f2_1' name='f2_1' title='Año' size='4' maxlength='4' value='<?php echo $año?>' type='text'>	
                                    &nbsp;<img id='f4_img' class='datepicker' src='img/calendar.gif' alt='Seleccione una fecha.' title='Haga clic para seleccionar una fecha.'>&nbsp;

                                        <script type='text/javascript'>
                                                Calendar.setup({
                                                        inputField	 : 'f2_1',
                                                        baseField    : 'f2',
                                                        displayArea  : 'f2_area',
                                                        button		 : 'f2_img',
                                                        ifFormat	 : '%B %e, %Y',
                                                        onSelect	 : selectDate
                                                });
                                        </script>
                                </td>
                         </tr>

                    </table>


                    <INPUT name="btngenerar" class="buttons_text" type="submit" value="Reporte Detallado" title="Haz Click Ver ek detallado">
                </center>

     <?PHP 
		//Funciones

						
			function intotales($matriz,$elementos,$campo,$valor){
				
				for ($ibm = 0; $ibm <= $elementos; $ibm++) {
					$matriz[$campo][$ibm]=$valor;
				}		
				return $matriz;
			}

			function sumaarreglos($arreglo,$elementos,$camporesultado,$campovalor){
				for ($ibm = 0; $ibm <= $elementos; $ibm++) {
					$arreglo[$camporesultado][$ibm]+=$arreglo[$campovalor][$ibm];
				}		
				return $arreglo;         
			}	 
	 
	 
	 
	 ?>