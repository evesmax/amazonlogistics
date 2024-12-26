<?php    

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);

    session_start();
    $idorg = $_SESSION["accelog_idorganizacion"];
    $sql = $_SESSION["sequel"];
    $descripcion = $_SESSION["desc"];
    $idestiloomision = $_SESSION["iestilo"];

    
        //OBTENCION DE FILTROS SELECCIONADOS EN MODO HUM
    $filtros_seleccionados_tit = "";
    $filtros_seleccionados_tr = "";
    
    if(isset($_SESSION["repolog_filtros"])){
        $filtros_etiquetas =$_SESSION["repolog_filtros"];
        $filtros_valores_hum = $_SESSION["repolog_valores_hum"];
        $filtros_cuantos = $_SESSION["repolog_cuantos"];
    } else {
        $filtros_cuantos = 0;
    }

	$incluir = 1;
    for($i = 1; $i<=$filtros_cuantos; $i++){
		
		$incluir=1;
        if($filtros_valores_hum[$i]!=""){
            $pos_barra=strrpos($filtros_etiquetas[$i],"#");
            if(is_numeric($pos_barra)){
                $filtros_seleccionados_tit.=strtoupper(substr($filtros_etiquetas[$i],1));
                $filtros_seleccionados_tr.=strtoupper(substr($filtros_etiquetas[$i],1));
            } else {

                    $pos_barra=strrpos($filtros_etiquetas[$i],"@");
                    if(is_numeric($pos_barra)){

                        $caracter_pregunta = ";";
                        $pos_etiqueta=strpos($filtros_etiquetas[$i],$caracter_pregunta);                                               
                        $pos_barra+=1;
                        $etiqueta = substr($filtros_etiquetas[$i], $pos_barra, $pos_etiqueta-$pos_barra);                                                                                                
                        $filtros_seleccionados_tit.= strtoupper($etiqueta);
                        $filtros_seleccionados_tr.= strtoupper($etiqueta);

                    } else {

			            $pos_barra=strrpos($filtros_etiquetas[$i],"!");
			            if(is_numeric($pos_barra)){
							$incluir = 0;
							//ESTOS NO SE IMPRIMEN POR SER SESION 
							//QUEDA EL CODIGO POR SI ALGUNA VEZ SE DECIDE DEJARLOS
			                //$filtros_seleccionados_tit.=strtoupper(substr($filtros_etiquetas[$i],1));
			                //$filtros_seleccionados_tr.=strtoupper(substr($filtros_etiquetas[$i],1));
			            } else {
	                        $filtros_seleccionados_tit.= strtoupper($filtros_etiquetas[$i]);
	                        $filtros_seleccionados_tr.= strtoupper($filtros_etiquetas[$i]);
						}

                    }

            }
			if($incluir==1){
	            $filtros_seleccionados_tit.= "=<b>".strtoupper($filtros_valores_hum[$i])."</b> &nbsp; ";
	            $filtros_seleccionados_tr.= "= ".strtoupper($filtros_valores_hum[$i])."   ";				
			}

        }

    }

    
?>
<FORM id="reporte" name="reporte">
<html lang="sp">
	<head>
        <LINK href="../../netwarelog/utilerias/css_repolog/estilo-<?php echo $idestiloomision; ?>.css" title="estilo" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--TITULO VENTANA-->
		<title><?php echo $descripcion; ?></title>
		<meta name="generator" content="Netbeans">
		<meta name="author" content="Omar Mendoza"><!-- Date: 2010-08-07 -->
		<meta name="author-icons" content="Rachel Fu"><!-- Date: 2010-08-07 -->

        <!--PLUG IN CATALOG-->
        <script type="text/javascript" src="../../netwarelog/catalog/js/jquery.js"></script>		

		<script type="text/javascript">
			function pdf(){
				var p = prompt("Porcentaje de vista (100% por omisión):",100);
				p = parseFloat(p);
				document.location = "pdf.php?p="+p;
			}
			function mail(){
				var a = prompt("Registre el correo electrónico a quién desea enviarle el reporte:","@netwaremonitor.com");
				$("#divmsg").load("mail.php?a="+a)
			}			
		</script>
	</head>
	<body>
            <!-- BARRA DE HERRAMIENTAS DEL REPOLOG  -->
            <div class="fechahora">
                <table class="impresionhora" align="right">
                    <tr>            
                        <td align=right>
                            <?php
                            //echo date('l jS \of F Y h:i:s A');
                            echo date('d/m/Y h:i:s A');
                            ?> &nbsp;
                        </td>
                        <td width=16 align=right>
                            <a href="javascript:window.print();"><img src="../../netwarelog/repolog/img/impresora.png" border="0"></a>
                        </td>
                        <td width=16  align=right>
							<a href="../../netwarelog/repolog/reporte.php"> <img src="../../netwarelog/repolog/img/filtros.png" 
								title ="Haga clic aqui para cambiar filtros..." border="0"> </a>
						</td>                        
						<td width=16 align=right>
							<a href="javascript:mail();"> <img src="../../netwarelog/repolog/img/email.png"  
							   title ="Enviar reporte por correo electrónico" border="0"> 
							</a>
						</td>
						<td width=16 align=right>
							<a href="javascript:pdf();"> <img src="../../netwarelog/repolog/img/pdf.gif"  
							   title ="Generar reporte en PDF" border="0"> 
							</a>
						</td>																				
                    </tr>
					<tr>
						<th colspan=8>
							<div id="divmsg"></div>			
						</th>
					</tr>
                </table>
            </div>			
			<!-- //////////////////////////////////////////////////////  -->            


            
            <div class="imagen">
                <img src="../../netwarelog/utilerias/img_org/<?php echo $idorg; ?>.png" width="200" height="50">
            </div>

            <br><br>

            <center>

                <table>
                    <tr>
                    <td align="center">
                        <font size="3" color="gray"><b><?php echo $descripcion."<br>" ?></b></font>
                        <font size="1" color="gray">
                        <?php
                            echo $filtros_seleccionados_tit." <br> DETALLADO DEL:".$_GET['finicial']." AL: ".$_GET['ffinal'];
                        ?>
                        </font></td>
                    </tr>
                </table>


            
<table class="reporte">
    <tbody>

<?php
//PERSONALIZADA    
        include("bd.php"); 

        $sqlwhere=$_GET['sqlwhere'];
        $reporte=$_GET['reporte'];
        $finicial=$_GET['finicial'];
        $ffinal=$_GET['ffinal'];
        $fechacorte=$ffinal;
        
//INICIO SUPER TABLA
$htmlr="<table width='100%'>";

        if($reporte==13 || $reporte==12){
            //SQL PRINCIPAL
            if($reporte==13){
				$sqldev="ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where lds.idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0)";
			
                $sqlcan="(select ifnull(sum(c.cantidad2),0) from logistica_cancelacionordenesentrega c 
                        where c.fechacancelacion<='$fechacorte 23:59:59' and c.idordenentrega=re.idordenentrega and c.idestadodocumento=1)";
                                            
                $sql="select re.referencia1 'OE', re.referencia2 'IE', re.Fecha,ob.nombrebodega 'Bodega',vc.razonsocial 'Cliente',of.nombrefabricante 'Ingenio',vm.nombremarca 'Marca',ip.NombreProducto,il.descripcionlote 'Zafra',
                            format(sum(re.cantidad2),3) 'Inicial ™',
                            format((select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<='$fechacorte 23:59:59' limit 1),3) 'Retirada ™',
                            format(ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where lds.idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0),3) 'devuelta ™',
                            format($sqlcan,3) 'Cancelado ™',
                                format(
                                    sum(re.cantidad2)-
                                    (select ifnull(sum(lor.cantidad2),0) 
                                        from logistica_retiros lor 
                                            where re.idordenentrega=lor.idordenentrega 
                                                and lor.idestadodocumento=1 and lor.fechasalida<='$fechacorte 23:59:59' limit 1)-$sqlcan+$sqldev
                                ,3) 'Saldo ™',
                            re.idordenentrega idinstruccion
                        from logistica_ordenesentrega re 
                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                            left join vista_marcas vm on vm.idmarca=re.idmarca
                            left join ventas_clientes vc on vc.idcliente=re.idcliente
                            left join operaciones_bodegas ob on ob.idbodega=re.idbodega 
                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                        where 
                            (re.fecha<='$fechacorte 23:59:59') 
                            $sqlwhere
                        group by re.referencia1, re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega";
            }else{
                $sql="select re.referencia1 'OT',re.Fecha, ob.nombrebodega 'BodegaOrigen',obd.nombrebodega 'Bodega Destino',of.nombrefabricante 'Ingenio',vm.nombremarca 'Marca',ip.NombreProducto,il.descripcionlote 'Zafra',
                            format(sum(re.cantidad2),3) 'Inicial ™',
                            format((select ifnull(sum(loe.cantidad2),0) from logistica_envios loe where re.idtraslado=loe.idtraslado and loe.fechaenvio<='$fechacorte 23:59:59' and loe.idestadodocumento<>4 limit 1),3) 'Enviada ™',
                            format((select ifnull(sum(lor.cantidadrecibida2),0) from logistica_recepciones lor where lor.idtraslado=re.idtraslado and lor.fecharecepcion<='$fechacorte 23:59:59' and lor.idestadodocumento<>4 limit 1),3) 'Recibida ™',
                            format(ifnull((select sum(ld.cantidad2) from logistica_devoluciones ld where ld.idtraslado=re.idtraslado and ld.idestadodocumento=1 and ld.fechadevolucion <='$fechacorte 23:59:59'),0),3) 'Devuelta ™',
							format(sum(re.cantidad2)-(select ifnull(sum(loe.cantidad2),0) from logistica_envios loe where re.idtraslado=loe.idtraslado and loe.fechaenvio<='$fechacorte 23:59:59' limit 1),3) 'Saldo ™',
                            re.idtraslado idinstruccion
                        from logistica_traslados re 
                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                            left join vista_marcas vm on vm.idmarca=re.idmarca
                            left join operaciones_bodegas ob on ob.idbodega=re.idbodegaorigen 
                            left join operaciones_bodegas obd on obd.idbodega=re.idbodegadestino 
                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                        where 
                            (re.fecha<='$fechacorte 23:59:59') 
                            $sqlwhere
                        group by re.referencia1,re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodegaorigen";
			}
//echo $sql;
            
                $result = $conexion->consultar($sql);         
   $htmlr.="<tr>";
                while($rs = $conexion->siguiente($result)){
                        
                       //Dibuja Titulo de INSTRUCCION 
                       $htmlr.="
                        <table class='reporte'><tbody>                   
                            <tr class='trencabezado' >";
                                    $i=0;
                                    while($i < mysql_num_fields($result)-1){
                                        $meta = mysql_fetch_field($result, $i);
                                        if(!$meta){
                                            $htmlr.="información no disponible";
                                        } else {
                                            $htmlr.="<td>".$meta->name."</td>";
                                        }
                                        $i++;
                                    }
                        $htmlr.="</tr>"; //Fin Tabla Titulo
                                    //Datos Titulo
                                    $linea="";
                                    $cambiaestilo=false;
                                    $e=0;
                                    while($e < mysql_num_fields($result)-1){
                                        $meta = mysql_fetch_field($result, $e);
                                        if(!$meta){		
                                            echo "información no disponible";
                                        } else {
                                            $d = $rs{$meta->name};
					    if(strtolower($d)=="sub total"||strtolower($d)=="subtotal"||strtolower($d)=="total"){
						$cambiaestilo=true;                                                
                                            }
                                            if(strpos($d,"TOTAL")!=false){
                                                $cambiaestilo=true;
                                            }
                                            $estilotd = "tdcontenido";
                                            if($cambiaestilo){
                                                $estilotd = "tdsubtotal";
                                            } else {
                                                $signopesos = strpos($rs{$meta->name},"$"); //echo "      --".$rs{$meta->name}."  ".$signopesos."--    ";
                                                if($signopesos !== false){
                                                    $estilotd = "tdmoneda";
                                                }
                                                $signoporcentaje = strpos($rs{$meta->name},"%");
                                                if($signoporcentaje !== false){
                                                    $estilotd = "tdmoneda";
                                                }
                                            }
                                            $linea.="<td class='".$estilotd."' title='".$meta->name."'>".$rs{$meta->name}."</td>";
                                        }
                                        $e++;
                                    }	
                                    if($cambiaestilo){
                                            $linea = "<tr class='trsubtotal'>".$linea."</tr>";
                                    } else {
                                            $linea = "<tr class='trcontenido'>".$linea."</tr>";
                                    }
                                    $htmlr.=$linea;
                        $htmlr.="</table>";
                        
                        
                        //Agrega Tabla Detalle
                        //echo regresadetalle($rs{"idinstruccion"},$finicial,$ffinal,$conexion);
                        $htmlr.=regresadetalle($rs{"idinstruccion"},$finicial,$ffinal,$conexion,$reporte);
                        
                        
                }
    $htmlr.="</tr>";
                
                //Genera Datos
                $conexion->cerrar_consulta($result);    
        }
        
//FIN SUPER TABLA          
$htmlr.="</table>";  


//ESTILOS
  

//IMPRIME REPORTE FINAL
echo $htmlr;    




//FUNCIONES
function regresadetalle($idinstruccion,$finicial,$ffinal,$conexion,$reporte){
                $htmldetalle="Sin Informacion en esas Fechas";
                if($reporte==13){
                $sql="select 
                        lr.idretiro 'Remision',lr.fechasalida 'Fecha', ot.razonsocial 'Linea Transportista',
                        lr.nombreoperador 'Operador',lr.licenciaoperador 'Licencia', lr.cartaporte 'C.Porte', 
                        lr.placastractor 'Placas Tractor', lr.placasremolque 'Placas Remolque', lr.referencia1 'Ref. Cliente', 
                        lr.cantidad1 Cantidad, format(lr.cantidad2,3) 'Retiradas ™', lr.observaciones
                        from logistica_retiros lr 
                                left join logistica_ordenesentrega lo on lo.idordenentrega=lr.idordenentrega
                                left join operaciones_bodegas ob on ob.idbodega=lo.idbodega
                                left join operaciones_transportistas ot on ot.idtransportista=lr.idtransportista
                        Where lr.idestadodocumento=1 and lo.idordenentrega=$idinstruccion and (lr.fechasalida between '$finicial 00:00:00' and '$ffinal 23:59:59')";
                }else if($reporte==12){
                $sql="Select 
                        le.idenvio 'Rem.', date_format(le.fechaenvio,'%d-%m-%Y %H:%i:%s') 'Fecha Envio', of.nombrefabricante 'Ingenio', ot.razonsocial 'Transportista',le.nombreoperador 'Operador',le.licenciaoperador 'Licencia',
                        le.cartaporte 'C. Porte',le.placastractor 'Placas Tractor',le.placasremolque 'Remolque',
                                format(le.cantidad1,2) 'Envio', 
                                format(le.cantidad2,2) 'Envio ™',
                                lr.fecharecepcion 'Recepción', lr.idrecepcion 'Rem. Recepcion', lr.referencia 'Ref. Recepción',
                                lr.cantidadrecibida1 'Recibida',
                                lr.cantidadrecibida2 'Recibida ™',
                                lr.diferencia1 'Diferencia',
                                lr.diferencia2 'Diferencia ™',oborigen.nombrebodega 'Bodega Origen',obo.nombrebodega 'Bodega Real',
                                lr.observaciones 'Obs. Recepcion'
                        From logistica_traslados lt 
                        left join operaciones_fabricantes of on of.idfabricante=lt.idfabricante 
                        left join inventarios_productos ip on ip.idproducto=lt.idproducto 
						left join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto 
                        left join inventarios_lotes il on il.idloteproducto=lt.idloteproducto 
                        left join logistica_envios le on le.idtraslado=lt.idtraslado 
                        left join operaciones_transportistas ot on ot.idtransportista=le.idtransportista 
                        left join logistica_recepciones lr on lr.idtraslado=lt.idtraslado and lr.idenvio=le.idenvio 
                        left join operaciones_bodegas obo on obo.idbodega=lr.idbodega
						left join operaciones_bodegas oborigen on oborigen.idbodega=lt.idbodegaorigen
                        Where le.idestadodocumento<>4 and lt.idtraslado=$idinstruccion and ((lr.fecharecepcion between '$finicial 00:00:00' and '$ffinal 23:59:59') or (lr.fecharecepcion is null)) ";
						//echo $sql."<br>";
                }
                $result = $conexion->consultar($sql);  
                $htmldetalle="
                        <table class='reporte'><tbody>                   
                            <tr class='trencabezadosub' >";
                                    $i=0;
                                    while($i < mysql_num_fields($result)){
                                        $meta = mysql_fetch_field($result, $i);
                                        if(!$meta){
                                            $htmldetalle.="información no disponible";
                                        } else {
                                            $htmldetalle.="<td>".$meta->name."</td>";
                                        }
                                        $i++;
                                    }
                $htmldetalle.="</tr>"; //Fin Tabla Titulo
                
                $total1=0;
                $total2=0;
                $total3=0;
                $total4=0;
                $total5=0;
                $total6=0;
                
                while($rs = $conexion->siguiente($result)){
                                    if($reporte==13){
                                        //Totales Envio
                                        $total1+=str_replace(',','',$rs{"Cantidad"});
                                        $total2+=str_replace(',','',$rs{"Retiradas ™"});
                                    }else{
                                        //Totales Recepciones
                                        $total1+=str_replace(',','',$rs{"Envio"});
                                        $total2+=str_replace(',','',$rs{"Envio ™"});
                                        $total3+=str_replace(',','',$rs{"Recibida"});
                                        $total4+=str_replace(',','',$rs{"Recibida ™"});
                                        $total5+=str_replace(',','',$rs{"Diferencia"});
                                        $total6+=str_replace(',','',$rs{"Diferencia ™"});
                                    }
                                    $linea="";
                                    $cambiaestilo=false;
                                    $e=0;
                                    while($e < mysql_num_fields($result)){
                                        $meta = mysql_fetch_field($result, $e);
                                        if(!$meta){		
                                            echo "información no disponible";
                                        } else {
                                            $d = $rs{$meta->name};
					    if(strtolower($d)=="sub total"||strtolower($d)=="subtotal"||strtolower($d)=="total"){
						$cambiaestilo=true;                                                
                                            }
                                            if(strpos($d,"TOTAL")!=false){
                                                $cambiaestilo=true;
                                            }
                                            $estilotd = "tdcontenido";
                                            if($cambiaestilo){
                                                $estilotd = "tdsubtotal";
                                            } else {
                                                $signopesos = strpos($rs{$meta->name},"$"); //echo "      --".$rs{$meta->name}."  ".$signopesos."--    ";
                                                if($signopesos !== false){
                                                    $estilotd = "tdmoneda";
                                                }
                                                $signoporcentaje = strpos($rs{$meta->name},"%");
                                                if($signoporcentaje !== false){
                                                    $estilotd = "tdmoneda";
                                                }
                                            }
                                            $linea.="<td class='".$estilotd."' title='".$meta->name."'>".$rs{$meta->name}."</td>";
                                        }
                                        $e++;
                                    }	
                                    //$cambiaestilo=true;
                                    if($cambiaestilo){
                                            $linea = "<tr class='trsubtotal'>".$linea."</tr>";
                                    } else {
                                            $linea = "<tr class='trcontenido'>".$linea."</tr>";
                                    }                    
									$htmldetalle.=$linea;
                }
                $conexion->cerrar_consulta($result);
					//TOTAL
                                    if($reporte==13){
					$linea="<tr class='trsubtotal'><td colspan=9>Totales</td><td>".number_format($total1,2)."</td><td>".number_format($total2, 3)."</td><td colspan=2><td></td><td></td>";
                                    }else{
                                        $linea="<tr class='trsubtotal'><td colspan=9>Totales</td><td>".number_format($total1,2)."</td><td>".number_format($total2, 3)."</td><td colspan=3></td><td>".number_format($total3,2)."</td><td>".number_format($total4,2)."</td><td>".number_format($total5,2)."</td><td>".number_format($total6,2)."</td><td></td><td></td><td></td>";
                                    }
                                        $htmldetalle.=$linea;
					$htmldetalle.="</table><br><br>";   
                return $htmldetalle;
}





?>
        
        
    </tbody>
</table>



              