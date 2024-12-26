<?php    

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);

    //DEFINIENDO FECHAS
    session_start();
            //Fecha de Corte
        $uw=strpos($_SESSION["sequel"],'re.fecha');
        $uo=strpos($_SESSION["sequel"],'re.idempleado');
        $ct=strlen($_SESSION["sequel"]); //Ancho Cadena Total
        $td=($ct-($uo-8))*-1;
            $sfechacorte=substr($_SESSION["sequel"],$uw+10,$td);        
    //Fecha de Corte
            $fecha = new DateTime($sfechacorte);
            $fechacorte = $fecha->format('Y-m-d');
    //Fecha del Dia
            $sfechadia =$fecha=date("Y-m-d");
            $fecha = new DateTime($sfechadia);
            $fechadia = $fecha->format('Y-m-d');

    //Recibe Variables de Link 
    $idsubreporte=$_GET["idreporte"];
    $subreportetitulo="";
    $sqlrepd="";
    $idfabricante=$_GET["idfabricante"];
    $idmarca=$_GET["idmarca"];
    $idproducto=$_GET["idproducto"];
    $idestadoproducto=$_GET["idestadoproducto"];
    $idloteproducto=$_GET["idloteproducto"];
    $idbodega=$_GET["idbodega"];
    $mtotales=0;

    
    
    //Determinando SubReporte
    switch ($idsubreporte) {
        case 1:
            $subreportetitulo="Detallado - Entradas Acumuladas";
            $sqlrepd="select case when tm.nombremovimiento is null then 'Total' else tm.nombremovimiento end Movimiento, 
                                format(sum(im.cantidad),0) Bultos,
                             format((sum(im.cantidadsecundaria)),3) Toneladas 
             from inventarios_movimientos im 
                left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento 
                left join inventarios_unidadesproductos up on up.idproducto=im.idproducto
                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida
            Where  im.fecha<='$fechacorte 23:59:59' and tm.efectoinventario=1
            And im.idfabricante=$idfabricante And im.idmarca=$idmarca And im.idproducto=$idproducto 
            And im.idestadoproducto=$idestadoproducto And im.idloteproducto=$idloteproducto
            And im.idbodega=$idbodega
            Group By tm.nombremovimiento  with rollup";         
            break;
        case 2:
            $subreportetitulo="Detallado - Entradas del Dia";
            $sqlrepd="select case when tm.nombremovimiento is null then 'Total' else tm.nombremovimiento end Movimiento, 
                                format(sum(im.cantidad),0) Bultos,
                             format((sum(im.cantidadsecundaria)),3) Toneladas 
             from inventarios_movimientos im 
                left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento 
                left join inventarios_unidadesproductos up on up.idproducto=im.idproducto
                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida
            Where  (im.fecha between '$fechacorte 00:00:00' And '$fechacorte 23:59:59') and tm.efectoinventario=1
            And im.idfabricante=$idfabricante And im.idmarca=$idmarca And im.idproducto=$idproducto 
            And im.idestadoproducto=$idestadoproducto And im.idloteproducto=$idloteproducto
            And im.idbodega=$idbodega
            Group By tm.nombremovimiento  with rollup";
            break;
        case 3:
            $subreportetitulo="Detallado - Salidas del Dia";
            $sqlrepd="select case when tm.nombremovimiento is null then 'Total' else tm.nombremovimiento end Movimiento, 
                                format(sum(im.cantidad),0) Bultos,
                             format((sum(im.cantidadsecundaria)),3) Toneladas 
             from inventarios_movimientos im 
                left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento 
                left join inventarios_unidadesproductos up on up.idproducto=im.idproducto
                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida
            Where  (im.fecha between '$fechacorte 00:00:00' And '$fechacorte 23:59:59') and tm.efectoinventario=-1
            And im.idfabricante=$idfabricante And im.idmarca=$idmarca And im.idproducto=$idproducto 
            And im.idestadoproducto=$idestadoproducto And im.idloteproducto=$idloteproducto
            And im.idbodega=$idbodega
            Group By tm.nombremovimiento  with rollup";
            break;
        case 4:
            $subreportetitulo="Detallado - Salidas Acumuladas";
            $sqlrepd="select case when tm.nombremovimiento is null then 'Total' else tm.nombremovimiento end Movimiento, 
                                format(sum(im.cantidad),0) Bultos,
                             format((sum(im.cantidadsecundaria)),3) Toneladas 
             from inventarios_movimientos im 
                left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento 
                left join inventarios_unidadesproductos up on up.idproducto=im.idproducto
                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida
            Where  im.fecha<='$fechacorte 23:59:59' and tm.efectoinventario=-1
            And im.idfabricante=$idfabricante And im.idmarca=$idmarca And im.idproducto=$idproducto 
            And im.idestadoproducto=$idestadoproducto And im.idloteproducto=$idloteproducto
            And im.idbodega=$idbodega
            Group By tm.nombremovimiento  with rollup";            
            break;
        case 5:
            $subreportetitulo="Detallado - Devoluciones";
            break;        
        case 6:
            $subreportetitulo="Detallado - Certificados de Deposito";
            $sqlrepd=" select lc.NoCede, lc.BonoPrenda, lc.CedeRelacion,lc.FechaOperacion, lc.FechaVencimiento, oa.nombrealmacenadora 'Almacenadora',vt.nombretenedor 'Tenedor', of.nombrefabricante 'Ingenio', ip.nombreproducto 'Producto', il.descripcionlote 'Zafra', ie.descripcionestado 'Estado', format(lc.cantidad1,0) '(Bts)', format(lc.cantidad2,3) '(™)', format(lc.cantidad2*1000,3) 'Kilos', format(lc.preciotonelada,2) 'Precio (™)', lc.importetotal 'Importe', 
                                case when idestadodocumento<>4 then 'ACTIVO' else concat('CANCELADO EL: ',lc.fechacancelacion) End 'Estatus' 
                        from  logistica_certificados lc 
                                left join operaciones_fabricantes of on of.idfabricante=lc.idfabricante
                                left join vista_marcas vm on vm.idmarca=lc.idmarca  
                                left join inventarios_productos ip on ip.idproducto=lc.idproducto 
                                left join  inventarios_estados ie on ie.idestadoproducto=lc.idestadoproducto 
                                left join inventarios_lotes il on il.idloteproducto=lc.idloteproducto 
                                left join operaciones_bodegas obo on obo.idbodega=lc.idbodega 
                                left join operaciones_almacenadoras oa on oa.idalmacenadora=lc.idalmacenadora
                                left join vista_tenedores vt on vt.idtenedor=lc.idtenedor 
                         where (lc.fechaoperacion<='$fechacorte  23:59:59') And lc.idfabricante=$idfabricante And lc.idmarca=$idmarca 
                                And lc.idproducto=$idproducto And lc.idestadoproducto=$idestadoproducto And lc.idloteproducto=$idloteproducto
                                And lc.idbodega=$idbodega";
            break;
        case 7:
            $subreportetitulo="Detallado - Comprometida";
            $sqlrepd="select re.referencia1 'OE', re.referencia2 'IE', re.Fecha,ob.nombrebodega 'Bodega',vc.razonsocial 'Cliente',of.nombrefabricante 'Ingenio',vm.nombremarca 'Marca',ip.NombreProducto,il.descripcionlote 'Zafra',
                            format(sum(re.cantidad2),3) 'Inicial ™',
                            format((select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<='$fechacorte 23:59:59' limit 1),3) 'Retirada ™',
                            0 'devuelta ™',
							format((select ifnull(sum(c.cantidad2),0) from logistica_cancelacionordenesentrega c 
								where c.fechacancelacion<='$fechacorte 23:59:59' and c.idordenentrega=re.idordenentrega and c.idestadodocumento=1),3) 
							'Cancelado ™',
                            format(sum(re.cantidad2)-
								(select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<='$fechacorte 23:59:59' limit 1)-
								(select ifnull(sum(c.cantidad2),0) from logistica_cancelacionordenesentrega c 
								where c.fechacancelacion<='$fechacorte 23:59:59' and c.idordenentrega=re.idordenentrega and c.idestadodocumento=1)
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
                            (re.fecha<='$fechacorte 23:59:59') 
                            And (re.fechacancelacion<='$fechacorte 23:59:59' or re.fechacancelacion is null) 
                            And re.idfabricante=$idfabricante 
                            And re.idmarca=$idmarca And re.idproducto=$idproducto And re.idestadoproducto=$idestadoproducto 
                            And re.idloteproducto=$idloteproducto And re.idbodega=$idbodega
                        group by re.referencia1, re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega";
            break;
        case 8:
            $subreportetitulo="Detallado - En Transito";
            $sqlrepd="select lr.idrecepcion 'Remision', lt.referencia1 otfc, le.FechaEnvio, lr.FechaRecepcion, ld.FechaDevolucion,ld.FechaRecepcion,
						of.nombrefabricante 'Ingenio', obo.nombrebodega 'Bodega Origen',
						obd.nombrebodega 'Bodega Destino', ot.razonsocial Transportista,
						 le.CartaPorte, le.NombreOperador,le.PlacasTractor,
						le.PlacasRemolque, il.descripcionlote 'Zafra', 
						ip.nombreproducto 'Producto', ie.descripcionestado 'Estado Producto', 
						format(le.cantidad2,3) 'Enviada TM',
						format(lr.cantidadrecibida2,3) 'Recibida ™', 
						format(ifnull(le.cantidad2,0)-ifnull(lr.cantidadrecibida2,0),3) 'Diferencia Traslados ™',
						format(ifnull(ld.cantidad2,0),2) 'Devuelta ™', 
						format(ifnull(ld.cantidadrecibida2,0),2) 'Devuelta Recibida ™', 
						format(ifnull(ld.cantidad2,0)-ifnull(ld.cantidadrecibida2,0),3) 'Diferencia Devolucion ™',
						format(ifnull(lf.cantfalt2,0),3) 'Faltante Aclarado ™',
						format(ifnull(le.cantidad2,0)-ifnull(lr.cantidadrecibida2,0)-ifnull(ld.cantidadrecibida2,0), ifnull(lf.cantfalt2,0),3) 'Diferencia ™',
						format(
							(ifnull(le.cantidad2,0)-ifnull(lr.cantidadrecibida2,0))-(ifnull(ld.cantidadrecibida2,0))-(ifnull(lf.cantfalt2,0))
						,3) 'Total Transito'
					from logistica_traslados lt 
						left join operaciones_fabricantes of on of.idfabricante=lt.idfabricante
						left join operaciones_bodegas obo on obo.idbodega=lt.idbodegaorigen
						left join operaciones_bodegas obd on obd.idbodega=lt.idbodegadestino
						left join inventarios_productos ip on ip.idproducto=lt.idproducto
						left join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto
						left join inventarios_lotes il on il.idloteproducto=lt.idloteproducto
						left join operaciones_transportistas ot on ot.idtransportista=lt.idtransportista 
						left join logistica_envios le on le.idtraslado=lt.idtraslado 
						left join logistica_recepciones lr on lr.idtraslado=lt.idtraslado and lr.idenvio=le.idenvio
						left join logistica_devoluciones ld on ld.idrecepcion=lr.idrecepcion 
						left join logistica_faltantestraslados lf on lf.idrecepcion=lr.idrecepcion
					where (lr.fecharecepcion <='$fechacorte 23:59:59') And le.idestadodocumento<>4
						AND (ifnull(le.cantidad2,0)-ifnull(lr.cantidadrecibida2,0))<>0
						    And lt.idfabricante=$idfabricante 
                            And lt.idmarca=$idmarca And lt.idproducto=$idproducto And lt.idestadoproducto=$idestadoproducto 
                            And lt.idloteproducto=$idloteproducto And lt.idbodegadestino=$idbodega";
			
			//Variables Sumatoria
				$i=26; //Campos
                $mtotales=1; //Para que los agrege
                $ctotales=array("mostrar","decimales","valordefecto");
                    $ctotales["mostrar"][0]=0;
                    $ctotales["decimales"][0]=0;
                    $ctotales["valordefecto"][0]="Totales";
                   
                    for ($z=1; $z<=$i; $z++){
                        $ctotales["mostrar"][$z]=0;
                        $ctotales["decimales"][$z]=0;
                        $ctotales["valordefecto"][$z]="";
                    }
                    
                    //Totales
						$ctotales["mostrar"][17]=1;
                        $ctotales["decimales"][17]=3;
                        $ctotales["valordefecto"][17]="";
						
                        $ctotales["mostrar"][18]=1;
                        $ctotales["decimales"][18]=3;
                        $ctotales["valordefecto"][18]="";

                        $ctotales["mostrar"][19]=1;
                        $ctotales["decimales"][19]=2;
                        $ctotales["valordefecto"][19]="";   

                        $ctotales["mostrar"][20]=1;
                        $ctotales["decimales"][20]=3;
                        $ctotales["valordefecto"][20]="";
						
						$ctotales["mostrar"][21]=1;
                        $ctotales["decimales"][21]=3;
                        $ctotales["valordefecto"][21]="";

                        $ctotales["mostrar"][22]=1;
                        $ctotales["decimales"][22]=2;
                        $ctotales["valordefecto"][22]="";   

                        $ctotales["mostrar"][23]=1;
                        $ctotales["decimales"][23]=3;
                        $ctotales["valordefecto"][23]="";
						
						$ctotales["mostrar"][24]=1;
                        $ctotales["decimales"][24]=3;
                        $ctotales["valordefecto"][24]="";

                        $ctotales["mostrar"][25]=1;
                        $ctotales["decimales"][25]=2;
                        $ctotales["valordefecto"][25]="";   

                        $ctotales["mostrar"][26]=1;
                        $ctotales["decimales"][26]=3;
                        $ctotales["valordefecto"][26]="";
			
			//echo $sqlrepd;					
            break; 
        case 9:
            $subreportetitulo="Detallado - Reservado";
            $sqlrepd="Select  re.idreserva Folio, re.referencia1 'OR', re.Fecha,ob.nombrebodega 'Bodega',cli.razonsocial 'Cliente',of.nombrefabricante 'Ingenio', vm.nombremarca 'Marca', 
                        concat(ip.nombreproducto,' ',ie.descripcionestado) Producto, il.descripcionlote 'Zafra',
                        format(re.cantidad1,0) 'Inicial bts',format(re.cantidad2,3) 'Inicial ™'
                        From logistica_reservadeproductos re 
                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                            left join vista_marcas vm on vm.idmarca=re.idmarca
                            left join operaciones_bodegas ob on ob.idbodega=re.idbodega
                            left join inventarios_productos ip on ip.idproducto=re.idproducto
                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia
                         left join ventas_clientes cli on cli.idcliente=re.idcliente             
                         where 
                            (re.fecha<='$fechacorte 23:59:59') 
                            And (re.fechacancelacion<='$fechacorte 23:59:59' or re.fechacancelacion is null) 
                            And re.idfabricante=$idfabricante 
                            And re.idmarca=$idmarca And re.idproducto=$idproducto And re.idestadoproducto=$idestadoproducto 
                            And re.idloteproducto=$idloteproducto And re.idbodega=$idbodega";
            break;     
        case 10: 
            $subreportetitulo="Informacion de Bodega";
            $sqlrepd="select a.NombreBodega, concat(a.calle,' ', 
                            case when (a.noexterior is null or a.noexterior='') then '' else concat(' No. ',a.noexterior) end,
                            case when (a.nointerior is null or a.nointerior='') then '' else concat(' No. Int. ',a.nointerior) end
                            ,' ',a.colonia,'  C.P.', a.codigopostal) 'Domicilio', concat(a.telefonos,' ',a.telefonosmoviles) Telefonos, 
                            mu.nombremunicipio Municipio, es.nombreestado Estado, a.Responsable, a.CorreoElectronico,
                            case when a.tipobodega=-1 then 'BODEGA INGENIO' else 'EXTERNA' end 'Tipo Bodega'
                        from operaciones_bodegas a
                                inner join general_estados es on es.idestado=a.idestado
                                inner join general_municipios mu on mu.idmunicipio=a.idmunicipio
                                Where a.idbodega=$idbodega";
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
    

    include("bd.php"); 
            //SQL PRINCIPAL

                $sql=$sqlrepd;
				//echo $sql;
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
                                        
												//Acumula Totales e todos los campos numericos
                                                $valor=str_replace(",","",$rs{$meta->name});
                                                if(is_numeric($valor)){
                                                    $tot[$e]+=$valor*1;
                                                }else{
                                                    $tot[$e]+=0;
                                                }										
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
				
				//Solo funciona si en un proceso previo se actualiza el arreglo de ctotales
				$linea="";
				if($mtotales==1){
                    $linea="";
                    for ($z=0; $z<=$e-1; $z++){
                        //Busca elemento $ctotales(mostrar,decimales,valordefecto)-Configuracion Totales
                        if($ctotales["mostrar"][$z]==1){
                            $d=$ctotales["decimales"][$z];
                            $linea.="<td class='tdsubtotal'>".number_format($tot[$z],$d)."</td>";
                        }else{
                            $linea.="<td class='tdsubtotal'>".$ctotales["valordefecto"][$z]."</td>";
                        }
                            
                    }
                    $linea="<tr class='trsubtotal'>".$linea."</tr>";
                    //echo $linea;
                }
				
				$htmlr.=$linea;
				
                //Genera Datos
                $conexion->cerrar_consulta($result);    
        
        
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
                        le.idenvio 'Rem.', le.fechaenvio 'Fecha Envio', of.nombrefabricante 'Ingenio', ot.razonsocial 'Transportista',le.nombreoperador 'Operador',le.licenciaoperador 'Licencia',
                        le.cartaporte 'C. Porte',le.placastractor 'Placas Tractor',le.placasremolque 'Remolque',
                                format(le.cantidad1,2) 'Envio', 
                                format(le.cantidad2,2) 'Envio ™',
                                lr.fecharecepcion 'Recepción', lr.referencia 'Ref. Recepción',
                                lr.cantidadrecibida1 'Recibida',
                                lr.cantidadrecibida2 'Recibida ™',
                                lr.diferencia1 'Diferencia',
                                lr.diferencia2 'Diferencia ™',obo.nombrebodega 'Bodega Real',
                                lr.observaciones 'Obs. Recepcion'
                        From logistica_traslados lt 
                        inner join operaciones_fabricantes of on of.idfabricante=lt.idfabricante 
                        inner join inventarios_productos ip on ip.idproducto=lt.idproducto 
                        inner join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto 
                        inner join inventarios_lotes il on il.idloteproducto=lt.idloteproducto 
                        left join logistica_envios le on le.idtraslado=lt.idtraslado 
                        left join operaciones_transportistas ot on ot.idtransportista=le.idtransportista 
                        left join logistica_recepciones lr on lr.idtraslado=lt.idtraslado and lr.idenvio=le.idenvio 
                        left join operaciones_bodegas obo on obo.idbodega=lr.idbodega 
                        Where le.idestadodocumento=1 and lt.idtraslado=$idinstruccion and (le.fechaenvio between '$finicial 00:00:00' and '$ffinal 23:59:59')";

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
                                        $total1+=$rs{"Cantidad"};
                                        $total2+=$rs{"Retiradas ™"};
                                    }else{
                                        //Totales Recepciones
                                        $total1+=$rs{"Envio"};
                                        $total2+=$rs{"Envio ™"};
                                        $total3+=$rs{"Recibida"};
                                        $total4+=$rs{"Recibida ™"};
                                        $total5+=$rs{"Diferencia"};
                                        $total6+=$rs{"Diferencia ™"};
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
					$linea="<tr class='trsubtotal'><td colspan=9>Totales</td><td>".number_format($total1,2)."</td><td>".number_format($total2, 3)."</td><td colspan=2><td></td>";
                                    }else{
                                        $linea="<tr class='trsubtotal'><td colspan=9>Totales</td><td>".number_format($total1,2)."</td><td>".number_format($total2, 3)."</td><td colspan=2></td><td>".number_format($total3,2)."</td><td>".number_format($total4,2)."</td><td>".number_format($total5,2)."</td><td>".number_format($total6,2)."</td><td></td><td></td>";
                                    }
                                        $htmldetalle.=$linea;
					$htmldetalle.="</table><br><br>";   
                return $htmldetalle;
}





?>
        
        
    </tbody>
</table>



              