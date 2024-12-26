<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");

	
//RECUPERANDO VARIABLES

        $idordencompra=0;
        $idordencompra=$_GET["idordencompra"];

    //OBTENIENDO INFORMACION BASICA
                    $fechahoy=date("d-m-Y g:i:s");
                    $oc="";
                    $cliente="";
                    $representantelegal="";
                    $claveciecliente="";
                    $tipocliente="";
                    $tipomercado="";
                    $tipoventa="";
                    $fecha="";
                    $fideicomiso="";
                    $clavecontrato="";
                    $marca="";
                    $nombreproducto="";
                    $calidad="";
                    $zafra="";
                    $presentacion="";
                    $nombrebodega="";
                    $volumenorden="";
                    $precio="";
                    $importe="";
                    $fechalimitepago="";
                    $representantelegalfideicomiso="";
                    $claveciefideicomiso="";
                    
                    
                    
		$sqlestatus="select  oc.idbodega, oc.idcliente, oc.idfabricante, oc.ordendecompra, cli.razonsocial cliente,cli.representantelegal,cli.cie 'ciecliente',
                                    tic.tipocliente, tim.tipomercado, tiv.tipodeventa, oc.fecha, of.nombrefabricante fideicomiso
                                    ,of.representantelegal representantelegalfideicomiso, of.clavecie 'claveciefideicomiso', 
                                    oc.clavecontrato,vm.nombremarca marca, ip.nombreproducto, concat('En Norma: ',cn.norma) 'calidad', 
                                    il.descripcionlote 'zafra', um.descripcionunidad 'presentacion', ob.nombrebodega, format(oc.volumenorden,2) 'volumenorden',
                                    format(oc.precioventa,2) 'precio', format(oc.importe,2) importe, oc.fechalimitepago 
                            from 
                            ventas_ordenesdecompra oc 
                                left join ventas_clientes cli on cli.idcliente=oc.idcliente
                                left join operaciones_fabricantes of on of.idfabricante=oc.idfabricante
                                left join vista_marcas vm on vm.idmarca=oc.idmarca
                                left join inventarios_productos ip on ip.idproducto=oc.idproducto
                                left join inventarios_lotes il on il.idloteproducto=oc.idloteproducto
                                left join operaciones_bodegas ob on ob.idbodega =oc.idbodega
                                left join inventarios_estados ie on ie.idestadoproducto=oc.idestadoproducto
                                left join ventas_tiposmercado tim on tim.idtipomercado=oc.idtipomercado
                                left join ventas_tiposdeventa tiv on tiv.idtipodeventa=oc.idtipodeventa
                                left join ventas_tiposclientes tic on tic.idtipocliente=cli.idtipocliente
                                left join calidad_normas cn on cn.idnorma=oc.idnorma
                                left join inventarios_unidadesmedida um on um.idunidadmedida=ip.idunidadmedida
                            where oc.idordencompra=".$idordencompra;

                
                $result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                    $folio=$idordencompra;
                    $oc=$rs{"ordendecompra"};
                    $cliente=$rs{"cliente"};
                    $representantelegal=$rs{"representantelegal"};
                    $claveciecliente=$rs{"ciecliente"};
                    $tipocliente=$rs{"tipocliente"};
                    $tipomercado=$rs{"tipomercado"};
                    $tipoventa=$rs{"tipodeventa"};
                    $fecha=$rs{"fecha"};
                    $fideicomiso=$rs{"fideicomiso"};
                    $clavecontrato=$rs{"clavecontrato"};
                    $marca=$rs{"marca"};
                    $nombreproducto=$rs{"nombreproducto"};
                    $calidad=$rs{"calidad"};
                    $zafra=$rs{"zafra"};
                    $presentacion=$rs{"presentacion"};
                    $nombrebodega=$rs{"nombrebodega"};
                    $volumenorden=$rs{"volumenorden"};
                    $precio=$rs{"precio"};
                    $importe=$rs{"importe"};
                    $fechalimitepago=$rs{"fechalimitepago"};
                    $representantelegalfideicomiso=$rs{"representantelegalfideicomiso"};
                    $claveciefideicomiso=$rs{"claveciefideicomiso"};
                    $idfabricante=$rs{"idfabricante"};
                    $idbodegaorigen=$rs{"idbodega"};
                    $idcliente=$rs{"idcliente"};
                    
		}
            
		$conexion->cerrar_consulta($result);                        
                        

                
                
                $tipoimagen="i";
                $sqlimagen="";
                $carpeta="";
                $imgtitulo="";
                if($tipoimagen=="i"){                        
                        $sqlimagen="select a.logotipo, concat(ifnull(a.rfc,''),' ',a.nombrefabricante) nombre, 
                            concat(a.calle,' ',ifnull(concat('No. ',a.noexterior),''),ifnull(concat('No Int. ',a.nointerior),'')) domicilio,  
                            mu.nombremunicipio municipio, es.nombreestado estado, a.codigopostal, '' telefonos  from operaciones_fabricantes a
                            inner join general_estados es on es.idestado=a.idestado
                            inner join general_municipios mu on mu.idmunicipio=a.idmunicipio
                            where idfabricante=".$idfabricante;
                        $carpeta="../../netwarelog/archivos/1/operaciones_fabricantes/";

                }elseif($tipoimagen=="a"){
                        $sqlimagen="select a.logotipo, a.nombrealmacenadora nombre, 
                                        concat(a.calle,' ',
                                        case when a.noexterior is null then '' else concat(' No. ',a.noexterior) end,
                                        case when a.nointerior is null then '' else concat(' No. Int.',a.nointerior) end
                                        ) 'domicilio',  
                                        mu.nombremunicipio municipio, es.nombreestado estado, 0 codigopostal, a.telefonos
                                        from relaciones_almacenadoras_bodegas t inner join relaciones_almacenadoras_bodegas_detalle d on t.idalmacenadorabodega=d.idalmacenadorabodega 
                                        inner join operaciones_almacenadoras a on a.idalmacenadora=t.idalmacenadora
                                        inner join general_estados es on es.idestado=a.idestado
                                        inner join general_municipios mu on mu.idmunicipio=a.idmunicipio
                                        where idbodega=".$idbodegaorigen;
                        $carpeta="../../netwarelog/archivos/1/operaciones_almacenadoras/";
                }
                //Obtiene Nombre de la Imagen
                $result = $conexion->consultar($sqlimagen);
                while($rs = $conexion->siguiente($result)){
                        $logotipo=$rs{"logotipo"};
                        $nombre=$rs{"nombre"};
                        $domicilio=$rs{"domicilio"};
                        $cp=$rs{"codigopostal"};
                        $municipio=$rs{"municipio"};
                        $estado=$rs{"estado"};
                        $telefonos=$rs{"telefonos"};
                }
                $conexion->cerrar_consulta($result);  
                
                //Si existe la imagen la dibuja
                if(is_dir($carpeta.$logotipo)){
                    $imgtitulo.="";
                }else{
                    $imgtitulo.="<img src='".$carpeta.$logotipo."' width=150>";
                }	
                $nombreorganizacion=$nombre;
            //Genera Domicilios Bodega Origen y Destino
                $sqlbodega="select concat(a.calle,' ', 
                                        case when (a.noexterior is null or a.noexterior='') then '' else concat(' No. ',a.noexterior) end,
                                        case when (a.nointerior is null or a.nointerior='') then '' else concat(' No. Int. ',a.nointerior) end
                                    ,' ',a.colonia) 'domicilio', 
                                mu.nombremunicipio municipio, es.nombreestado estado, 
                                case when (a.codigopostal is null or a.codigopostal='') then '' else concat('CP: ',a.codigopostal) end codigopostal, a.responsable, 
                                case when (a.telefonos is null or a.telefonos='') then '' else concat(' Tels: ',a.telefonos) end telefonos
                                from operaciones_bodegas a
                                inner join general_estados es on es.idestado=a.idestado
                                inner join general_municipios mu on mu.idmunicipio=a.idmunicipio
                                Where a.idbodega=".$idbodegaorigen;
                $result = $conexion->consultar($sqlbodega);
                while($rs = $conexion->siguiente($result)){
                        $domiciliobodegaorigen=$rs{"domicilio"}." ".$rs{"codigopostal"}." ".$rs{"telefonos"}." ".$rs{"municipio"}." ".$rs{"estado"};
                }
                $conexion->cerrar_consulta($result);               
            //Genera Domicilios Bodega Origen y Destino
                $sqlcliente="select a.domiciliofiscal 'domicilio', 
                                mu.nombremunicipio municipio, es.nombreestado estado, 
                                a.contacto, 
                                case when (a.telefonoempresa is null or a.telefonoempresa='') then '' else concat(' Tels: ',a.telefonoempresa) end telefonos
                                from ventas_clientes a
                                inner join general_estados es on es.idestado=a.idestado
                                inner join general_municipios mu on mu.idmunicipio=a.idmunicipio
                                Where a.idcliente=".$idcliente;
                
                $result = $conexion->consultar($sqlbodega);
                while($rs = $conexion->siguiente($result)){
                        $domiciliocliente=$rs{"domicilio"}." ".$rs{"telefonos"}." ".$rs{"municipio"}." ".$rs{"estado"};
                }
                $conexion->cerrar_consulta($result);                   
                
                
                
               

	
	$html="<html>";
	$html.= "<head>";
	//Utiliza por omisión el estilo 1 del repolog
	$html.= "<LINK href='pdf/pdf_factura_css/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";
	$html.= "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>";
	$html.= "<meta name='author-icons' content='Rachel Fu'>";
	$html.= "<style>";
	$html.= "  body{font-size:6.5pt;color:black}";	
	$html.= "  td{font-size:7pt}";
	$html.= "</style>";
	$html.= "</head>";
        //$html.=" <FORM id='envio' name='envio' method='post' action='envio_grabar.php'>";
	
	$html.= "<body style='font-family:helvetica'>";


        $html.= "<BODY  style='font-family:helvetica'>
                                        <center style='border-style: none'>
                                            <div id='printer'>";
        
        $html.= "<LINK href='pdf/pdf_factura_css/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";                                  

        
		//INICIA MEGATABLA
		$html.="<table width='100%'>";
                
		//ENCABEZADO
		$html.="<tr><td>"; //Mega tabla
		$html.="<table width='100%'>";
						
				//Información del emisor
				$html.="<tr>"; 
				
					//Logotipo
					$html.="<td width='15%'>".$imgtitulo."</td>";
				
					//Datos Organización
					$html.="<td width='45%' align=left style='font-family:helvetica;font-size:9pt;'>";				
						$html.="<b>".$nombreorganizacion."</b><br>";
						
						$html.=" <strong>DOMICILIO:</strong> ".$domicilio;
						$html.="<br> <strong>C.P.</strong> ".$cp;
						$html.="<br> ".$municipio;
						$html.=" ".$estado;
                                                if($telefonos<>''){
                                                    $html.="<br><strong>TELEFONO:</strong> ".$telefonos;
                                                }
						$html.="<br>";					
					$html.="</td>";
				
                                $rn=$idordencompra;
				//Datos de Facturación
				$html.="<td width='30%' align=right>";
						
					$html.="<table class='reporte' width='100%'>";
					
					//Serie y Folio
					$html.="<tr class='trencabezado'><td><b>ORDEN DE COMPRA</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center>";
					$html.="	<b>Folio:</b> <font color=blue>".$idordencompra."</font>";
                                        if($oc<>""){
                                            $html.="<b>Ref:</b> <font color=blue>".$oc."</font>";
                                        }
					$html.="</td>";
					$html.="</tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                        $st="";
					//Fecha del movimiento
					$html.="<tr class='trencabezado'><td><b>EMISIÓN</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center style='font-size:10pt'>";
					$fecha = new DateTime($fecha);
					$fechainfo = $fecha->format('d-m-Y');									
					$html.="<b>".$fechainfo."</b>";
					$html.="</td>";
					$html.="</tr>";
					$html.="</table>";		
				$html.="</td>";				
			$html.="</tr>"; 
		$html.="</table>"; 
		$html.="</td></tr>"; //Mega tabla		
		//Oficio
                            $html.="<td width='60%'>";
                                    $html.="<table  width='100%'>";
                                            $html.="<tr height='55' valign='top'>";
                                                    $html.="<td>";
                                                $html.="<font size=2 face='arial'>
                                                        <br>$representantelegalfideicomiso<br>
                                                        <b>$fideicomiso</b><br><br><br>
                                                            Por medio de la presente, me permito confirmar los términos de la operación de compraventa 
                                                            de azúcar acordada entre el <b>$fideicomiso</b> (El Vendedor) y mi representada
                                                            <b>$cliente</b> (El Comprador).<br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                
                            
 		
                
 //INICIAN DATOS DE CAPTURA 
                
                //Inicia sección de forma de pago e impuestos...
		$html.="<tr><td>"; //Mega tabla		
		$html.="<table width='100%'>";
			$html.="<tr>";				
			
//DATOS TRANSPORTE
				$html.="<td width='40%' valign='top'>";
				
					
;
					$html.="<table class='reporte' width='100%'>";
						$html.="<tr>
                                                            <td width=30%>Clave Contrato:</td>
                                                            <td align=left><b>".$clavecontrato."</b></td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>Marca:</td>
                                                            <td align=left><b>".$marca."</b></td>
                                                        </tr>";	
						$html.="<tr>
                                                            <td width=30%>Tipo Producto:</td>
                                                            <td align=left><b>".$nombreproducto."</b></td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>Calidad:</td>
                                                            <td align=left><b>".$calidad."</b></td>
                                                        </tr>";							
	                                                   
						$html.="<tr>
                                                            <td width=30%>Zafra:</td>
                                                            <td align=left><b>$zafra</b></td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>Presentacion:</td>
                                                            <td align=left><b>$presentacion</b></td>
                                                        </tr>";   
						$html.="<tr>
                                                            <td width=30%>Ubicacion:</td>
                                                            <td align=left><b>$domiciliobodegaorigen</b></td>
                                                        </tr>";
                                                $html.="<tr>
                                                            <td width=30%>Bodega:</td>
                                                            <td align=left><b>$nombrebodega</b></td>
                                                        </tr>"; 
						$html.="<tr>
                                                            <td width=30%>Volumen Orden:</td>
                                                            <td align=left><b>".$volumenorden." TM</b></td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>Precio:</td>
                                                            <td align=left><b>$".$precio."</b></td>
                                                        </tr>";	
                                               
                                        $html.="</table>";
				$html.="</td></font>";
				
                               
//DATOS BODEGA ORIGEN
				$html.="<td width='40%' valign='top'>";
				
					

					$html.="<table class='reporte' width='100%'>";							
						$html.="<tr>
                                                            <td width=30%>Fecha Limite Pago:</td>
                                                            <td align=left><b>".$fechalimitepago."</b></td>
                                                        </tr>";	                                                   
						$html.="<tr>
                                                            <td width=30%>Plazo de emisión de OE:</td>
                                                            <td align=left><b>2 días a partir de la recepción del pago</b></td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>Plazo máximo de retiro:</td>
                                                            <td align=left><b>45 días naturales</b></td>
                                                        </tr>";   
						$html.="<tr>
                                                            <td width=30%>Costo mensual de almacenaje [$/ton/mes]:</td>
                                                            <td align=left><b>$60.00</b></td>
                                                        </tr>";
                                                $html.="<tr>
                                                            <td width=30%>Referencia bancaria (Vendedor):</td>
                                                            <td align=left><b>$claveciefideicomiso</b></td>
                                                        </tr>"; 
                                                $html.="<tr>
                                                            <td width=30%>Referencia bancaria (Vendedor):</td>
                                                            <td align=left><b>$claveciecliente</b></td>
                                                        </tr>"; 
						$html.="<tr>
                                                            <td width=30%>Monto:</td>
                                                            <td align=left><b>$".$importe."</b></td>
                                                        </tr>";                                                
					$html.="</table>";
				$html.="</td>";
									
			$html.="</tr>";				
		$html.="</table>";
		$html.="</td></tr>"; //Mega tabla
		//Finaliza sección de forma de pago e impuestos



                            $html.="<td width='60%'>";
                                    $html.="<table  width='100%'>";
                                            $html.="<tr height='55' valign='top'>";
                                                    $html.="<td>";
                                                $html.="<font size=2 face='arial'>
                                                        <br>Sin Otro Particular, me es grato enviarle un cordial Saludos";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                
                //Inicia sección de FIRMAS
		$html.="<tr><td>"; //Mega tabla
                    $html.="<br><br><table width='100%'>";	
                            $html.="<tr>"; 

                            //INFORMACION DEL EMISOR
                                            //Inicia sección de FIRMAS
                            $html.="<td width='60%'>";
                                    $html.="<table  width='50%'>";
                                            $html.="<tr height='55' valign='top'>";
                                                    $html.="<td>";
                                                $html.="<font size=2 face='arial'>
                                                        <br><b>$representantelegal</b><br>Representante Legal<br>$cliente<br><br>
                                                        <b>c.c.p. Juan José Soto Galeano</b> - Estrategia PROASA";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                            
                            
                            $html.="</td></table>";
                
                $html.="</tr>";				
		$html.="</table>";
                $html.="</tr></td>"; //Mega tabla
                


                //funciones javascript		
		$html_funcionesjavascript="";


                $opciones="";
                
                
                //$html.="<tr><td>"; //Mega tabla
                //    $html.= "<center><table><tr><td>".$html_botones."</td></tr></table></center>";
                //$html.="</tr></td>"; //Mega tabla
                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></div></form>";
                $html.="</html>";
                
                   
	//CREANDO ARCHIVO PDF
		
	require_once("../../netwarelog/repolog/dompdf-0.5.1/dompdf_config.inc.php");
	ini_set("memory_limit",$tamano_buffer);  // Configurable webconfig
	//ini_set("memory_limit","120M");  // Configurable webconfig
	//echo $html;
	//exit();
	
	$dompdf = new DOMPDF();
	$dompdf->set_paper('letter', 'portrait');
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("orden_de_compra.pdf");
	
	//Depurar liberías del pdf
	$output = $dompdf->output();
	echo $output;
	$arr = array('Attachment'=>0);
	//$dompdf->stream(utf8_decode($rfc."-".$serie."-".$foliofactura.".pdf"),$arr);	
	

?>