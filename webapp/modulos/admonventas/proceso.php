<?php    

    //jQuery

    $htmlpoliticas="	
        <script language='javascript'>
            $(document).ready(function(){
                //Limite Cancelacion
                    $('#txtcantidad').bind('focusout', function() {  
                        if($('#txtcantidad').val()*1>$('#txtsaldo').val()*1){
                            alert('No puede cancelar una cantidad mayor al Saldo');
                            $('#txtcantidad').val(0);
                            $('#txtcantidad').focus();
                        }
                        if($('#txtcantidad').val()*1<$('#txtsaldo').val()*1){
                            $('#txtestatus').val('Parcial');
                        }else{
                            $('#txtestatus').val('Completo');
                        }
                    });
            });
        </script>";


    //Recibe Variables de Link 
    session_start();
    
    include("bd.php"); 
    
    $tipo=$_GET["tipo"];
    $idref=$_GET["idref"];
    $fechacorte=date("Y-m-d");
    
    
    $html="<input type=hidden id='idref' name='idref' value='".$idref."'>";
    //Determinando SubReporte
    switch ($tipo) {
        case 1:
            $subm="agregar.php";
            $subreportetitulo="Relacionar Orden de Entrega (OE)";
            $sqlrepd="select re.referencia1 'OE', re.referencia2 'IE', re.Fecha,ob.nombrebodega 'Bodega',vc.razonsocial 'Cliente',of.nombrefabricante 'Ingenio',vm.nombremarca 'Marca',ip.NombreProducto,il.descripcionlote 'Zafra',
                            format(re.cantidad2,3) 'Inicial ™',
                            format((select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento<>4 and lor.fechasalida<='$fechacorte 23:59:59' limit 1),3) 'Retirada ™',
                            format(ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0),3) 'devuelta ™',
                            format(ifnull((Select sum(c.cantidad2) from logistica_cancelacionordenesentrega c Where c.idordenentrega=re.idordenentrega and c.fechacancelacion<='$fechacorte 23:59:59' and c.idestadodocumento=1),0),3) 'Cancelada ™',
                            format(
                                re.cantidad2
                                -(select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento<>4 and lor.fechasalida<='$fechacorte 23:59:59' limit 1)
                                -ifnull((Select sum(c.cantidad2) from logistica_cancelacionordenesentrega c Where c.idordenentrega=re.idordenentrega and c.fechacancelacion<='$fechacorte 23:59:59' and c.idestadodocumento=1),0)
								+ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0)
                            ,3) 'Saldo ™'
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
                            re.idordenentrega=$idref";
                            
                    //Inicia Seccion de Relacion de OES
                            $oe="";
                            
                            
                        //Genera Combo OE
                            $combooe="";
                            $sel="";
                            $sqlt="Select idordenentrega, referencia1 oe,referencia2 ie from logistica_ordenesentrega 
                                       where idestadodocumento=1 
                                       order by referencia1";
                            $combooe="<select id=combooe name=combooe>";
                                    $result = $conexion->consultar($sqlt);
                                    while($rs = $conexion->siguiente($result)){
                                            if($rs{"idordenentrega"}==$idref){
                                                $oe=$rs{"oe"};
                                            }
                                            $combooe.="<Option value=".$rs{"oe"}." ".$sel.">".$rs{"oe"}." - IE: ".$rs{"ie"}."</option>";
                                    }
                                    $conexion->cerrar_consulta($result);  
                            $combooe.="</select>"; 
                        //Genera Historial

                            $htmlh="";
                            $sqlt="select h.idhistorico, h.fecha, h.oe, h.oerelacion, h.observaciones,h.fecha from logistica_historialoe h
                                    Where h.oe ='$oe' order by h.fecha";
                                    $result = $conexion->consultar($sqlt);
                                    while($rs = $conexion->siguiente($result)){
                                        $idh=$rs{"idhistorico"};
                                        $htmlh.="<tr class=tdsubtotal>
                                                    <td class='tdsubtotal' align='center'><a href='eliminar.php?idref=$idref&idh=$idh'><img src=menos.png></a></td><td>".$rs{"fecha"}."</td>
                                                    <td class='tdsubtotal'>".$rs{"oerelacion"}."</td>
                                                    <td class='tdsubtotal'>".$rs{"observaciones"}."</td>
                                                </td>";    
                                    }
                                    $conexion->cerrar_consulta($result); 
                            

                            $inputreadonly=" readonly style='text-align:center;color:red;background-color: #FFFFFF;border-width:0;font-size: 14px;'";
                            $html.="<tr><td>"; //Mega tabla
                            $html.="<center><table class='reporte'  align=center>";
                                    //Armando encabezado
                                    $html.="
                                            <tr class='trencabezado'>
                                                <td colspan=4 align=right>DATOS RELACION OE</td>
                                            </tr>";


                                    //Obteniendo los datos

                                            $html.="<tr class=trcontenido>";
                                                    $html.="<td>Aplicaciones</td>";
                                                    $html.="<td>OE</td>";
                                                    $html.="<td>OE Relación</td>";
                                                    $html.="<td>Observaciones</td>";
                                            $html.="</tr>";

                                            $html.="<tr class=trcontenido>";
                                                    $html.="<td align='center'><input type='image' src='mas.png' alt='Agregar' title='' /></td>
                                                            <td>
                                                                <input ".$inputreadonly." type=text value=$oe id='txtoe' name='txtoe'>
                                                            </td>";
                                                    $html.="<td align=right>$combooe</td>";
                                                    $html.="<td align=left>
                                                                <textarea type=text id='txtobs' name='txtobs' rows=3 cols=45  title='Escriba las observaciones de la relacion'></textarea>
                                                            </td>";
                                            $html.="</tr>";
                                            $html.=$htmlh;

                                    $html.="</table></center>";

                            $html.="</td></tr>"; //Mega tabla                
                        //Finaliza Devoluciones y Faltantes            
            break;
        case 2:
            $subm="cancelar.php";
            $subreportetitulo="<b>Cancelar Orden de Entrega (OE)</b>";
            $sqlrepd="select re.referencia1 'OE', re.referencia2 'IE', re.Fecha,ob.nombrebodega 'Bodega',vc.razonsocial 'Cliente',of.nombrefabricante 'Ingenio',vm.nombremarca 'Marca',ip.NombreProducto,il.descripcionlote 'Zafra',
                            format(re.cantidad2,3) 'Inicial ™',
                            format((select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento<>4 and lor.fechasalida<='$fechacorte 23:59:59' limit 1),3) 'Retirada ™',
                            format(ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0),3) 'devuelta ™',
                            format(ifnull((Select sum(c.cantidad2) from logistica_cancelacionordenesentrega c Where c.idordenentrega=re.idordenentrega and c.fechacancelacion<='$fechacorte 23:59:59' and c.idestadodocumento=1),0),3) 'Cancelada ™',
                            format(
                                re.cantidad2
                                -(select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento<>4 and lor.fechasalida<='$fechacorte 23:59:59' limit 1)
                                -ifnull((Select sum(c.cantidad2) from logistica_cancelacionordenesentrega c Where c.idordenentrega=re.idordenentrega and c.fechacancelacion<='$fechacorte 23:59:59' and c.idestadodocumento=1),0)
								+ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0)
                            ,3) 'Saldo ™'
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
                            re.idordenentrega=$idref";
                    //Inicia Seccion de Relacion de OES
                            $oe="";
                            $cantidadtm=0;
                            $html="<input type=hidden id='idref' name='idref' value='".$idref."'>";
                            
                            //Obteniendo cantidades y OE
                                    $result = $conexion->consultar($sqlrepd);
                                    while($rs = $conexion->siguiente($result)){
                                        $oe=$rs{"OE"};
                                        $cantidadtm=$rs{"Saldo ™"};
                                        $saldo=str_replace(',','',$cantidadtm);
                                        $html.="<input type=hidden id='txtsaldo' name='txtsaldo' value='".$saldo."'>";
                                    }
                                    $conexion->cerrar_consulta($result); 
                                    
                                    if($saldo>0){        
                                        $inputreadonly=" readonly style='text-align:center;color:red;background-color: #FFFFFF;border-width:0;font-size: 14px;'";
                                        $html.="<tr><td>"; //Mega tabla
                                        $html.="<center><table class='reporte'  align=center>";
                                                //Armando encabezado
                                                $html.="
                                                        <tr class='trencabezado'>
                                                            <td colspan=4 align=right>DATOS PARA CANCELAR</td>
                                                        </tr>";


                                                //Obteniendo los datos

                                                        $html.="<tr class=trcontenido>";
                                                                $html.="<td>Aplicaciones</td>";
                                                                $html.="<td>Cancelar OE</td>";
                                                                $html.="<td>Cantidad(TM)</td>";
                                                                $html.="<td>Observaciones</td>";
                                                        $html.="</tr>";

                                                        $html.="<tr class=trcontenido>";
                                                                $html.="<td align='center'><input type='image' src='cancelar.png' alt='Agregar' title='Cancelar' /></td>
                                                                        <td>
                                                                            <input ".$inputreadonly." type=text value=$oe id='txtoe' name='txtoe'>
                                                                        </td>";
                                                                $html.="<td>
                                                                            <input type=text value=$saldo id='txtcantidad' name='txtcantidad'><br>
                                                                            <input ".$inputreadonly." type=text value='Completo' id='txtestatus' name='txtestatus'>
                                                                        </td>";
                                                                $html.="<td align=left>
                                                                            <textarea type=text id='txtobs' name='txtobs' rows=3 cols=45  title='Escriba las observaciones de la relacion'></textarea>
                                                                        </td>";
                                                        $html.="</tr>";

                                                $html.="</table></center>";

                                        $html.="</td></tr>"; //Mega tabla 
                                    }else{
                                        echo "<center><font color=red><b>No es posible cancelar, por que no tiene saldo disponible</b></font></center>";
                                    }
                        //Finaliza Devoluciones y Faltantes              
            break; 
    }
    


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
<FORM id='proceso' name='proceso' method='post' action='<?php echo $subm; ?>'>
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
                        <font size="3" color="gray"><b><?php echo $descripcion."<br> $subreportetitulo"."<br>" ?></b></font>
                        <font size="1" color="gray">
                        <?php
                            echo $filtros_seleccionados_tit;
                        ?>
                        </font></td>
                    </tr>
                </table>


            
<table class="reporte">
    <tbody>

<?php
        

 
//INICIO SUPER TABLA
$htmlr="<table width='100%'>";
    


            //SQL PRINCIPAL
                $sql=$sqlrepd;
                $result = $conexion->consultar($sql);         
   $htmlr.="<tr>";
   
   
                $htmlr.="
                        <table class='reporte'><tbody>                   
                            <tr class='trencabezado' >";
                                    $i=0;
                                    while($i < mysql_num_fields($result)){
                                        $meta = mysql_fetch_field($result, $i);
                                        if(!$meta){
                                            $htmlr.="información no disponible";
                                        } else {
                                            $htmlr.="<td>".$meta->name."</td>";
                                        }
                                        $i++;
                                    }
                $htmlr.="</tr>"; //Fin Tabla Titulo
                        
                while($rs = $conexion->siguiente($result)){
                        
                       //Dibuja Titulo de INSTRUCCION 
                       
                                    //Datos Titulo
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
                                    
                        
                        
                        //Agrega Tabla Detalle
                        //echo regresadetalle($rs{"idinstruccion"},$finicial,$ffinal,$conexion);
                        //$htmlr.=regresadetalle($rs{"idinstruccion"},$finicial,$ffinal,$conexion,$reporte);
                                    if($cambiaestilo){
                                            $linea = "<tr class='trsubtotal'>".$linea."</tr>";
                                    } else {
                                            $linea = "<tr class='trcontenido'>".$linea."</tr>";
                                    }
				    $htmlr.=$linea;
                        
                }
                
                //Genera Datos
                $conexion->cerrar_consulta($result);    
        
        
//FIN SUPER TABLA          
$htmlr.="</table>";  


//ESTILOS
  

//IMPRIME REPORTE FINAL
echo $htmlpoliticas;    //Politicas
echo $htmlr;    //Detalle
echo $html; //Aplicaciones



?>
        
        
    </tbody>
</table>




              