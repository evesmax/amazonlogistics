<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idenvio=$_GET["idenvio"];
        
    //OBTENIENDO INFORMACION BASICA DE TRASLADOS
                    $otfc="";
                    $fecha=date("d-m-Y g:i:s");
                    $nombreingenio="";
                    $idfabricante="";
                    $bodegaorigen="";
                    $idbodegaorigen="";
                    $bodegadestino="";
                    $zafra="";
                    $nombreproducto="";
                    $nombreestado="";
                    $saldoinicial=0;
                    $retirada=0;
                    $recibida=0;
                    $saldo=0;
                    $tipoimagen="";
                    $transportista="";
                    $fechaotfc="";
                    
   //VARIABLES DEL ENVIO
                    
                    $fechaenvio="";
                    $idtransportista=0;
                    $cartaporte=0;
                    $nombreoperador=0;
                    $placastractor=0;
                    $placasremolque=0;
                    $horallegada=0;
                    $ticketbascula=0;
                    $banco=0;
                    $estiba=0;
                    $cantidad1=0;
                    $cantidad2=0;
                    $consecutivobodega=0;
                    $folios="";
                    $observaciones="";
                    
		$sqlestatus="Select lt.referencia1 otfc, lt.fecha,  
                                of.nombrefabricante 'nombreingenio', obo.nombrebodega 'bodegaorigen',
                                obd.nombrebodega 'bodegadestino', il.descripcionlote 'zafra', 
                                ip.nombreproducto 'producto', ie.descripcionestado 'estado', 
                                format(lt.cantidad2,3) 'saldoinicial', format(IFNULL(lt.cantidadretirada2,0),3) 'retirada'
                                ,format(IFNULL(lt.cantidadrecibida2,0),3) 'recibida', 
                                format(lt.cantidad2-IFNULL(lt.cantidadretirada2,0),3) 'saldo', 
                                    case when obo.idbodega in (select idbodega from relaciones_almacenadoras_bodegas t 
                                        inner join relaciones_almacenadoras_bodegas_detalle d on t.idalmacenadorabodega=d.idalmacenadorabodega 
                                        where idbodega=lt.idbodegaorigen) then 'a' else 'i' end 'logo', ot.razonsocial transportista,
                                of.idfabricante, obo.idbodega, obd.idbodega idbodegadestino, ot.idtransportista, lt.idproducto,
                                    le.fechaenvio,le.idtransportista,le.cartaporte,le.nombreoperador,le.placastractor,
                                    le.placasremolque,le.horallegada,le.ticketbascula,le.banco, le.estiba, 
                                    format(le.cantidad1,2) cantidad1, format(le.cantidad2,2) cantidad2, le.consecutivobodega, le.folios,le.observaciones
                             From logistica_traslados lt 
                                inner join operaciones_fabricantes of on of.idfabricante=lt.idfabricante
                                inner join operaciones_bodegas obo on obo.idbodega=lt.idbodegaorigen
                                inner join operaciones_bodegas obd on obd.idbodega=lt.idbodegadestino
                                inner join inventarios_productos ip on ip.idproducto=lt.idproducto
                                inner join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto
                                inner join inventarios_lotes il on il.idloteproducto=lt.idloteproducto
                                inner join operaciones_transportistas ot on ot.idtransportista=lt.idtransportista 
                                inner join logistica_envios le on le.idtraslado=lt.idtraslado
                             Where le.idenvio=".$idenvio;
                //echo $sqlestatus;
		$result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                        //Asignando Valores del Traslado
                                    $otfc=$rs{"otfc"};
                                    $fechaotfc=$rs{"fecha"}; 
                                    $nombreingenio=$rs{"nombreingenio"};
                                    $idfabricante=$rs{"idfabricante"};
                                    $bodegaorigen=$rs{"bodegaorigen"};
                                    $idbodegaorigen=$rs{"idbodega"};
                                    $bodegadestino= $rs{"bodegadestino"};
                                    $idbodegadestino= $rs{"idbodegadestino"};
                                    $zafra= $rs{"zafra"};
                                    $nombreproducto= $rs{"producto"};
                                    $idproducto=$rs{"idproducto"};
                                    $nombreestado= $rs{"estado"};
                                    $saldoinicial= $rs{"saldoinicial"};
                                    $retirada= $rs{"retirada"};
                                    $recibida= $rs{"recibida"};
                                    $saldo=$rs{"saldo"};
                                    $tipoimagen=$rs{"logo"};
                                    $transportista=$rs{"transportista"};
                                    $idtransportista=$rs{"idtransportista"};
                        //Asignando Valores del Envio Procesado
                                    $fechaenvio=$rs{"fechaenvio"};
                                    $idtransportista=$rs{"idtransportista"};
                                    $cartaporte=$rs{"cartaporte"};
                                    $nombreoperador=$rs{"nombreoperador"};
                                    $placastractor=$rs{"placastractor"};
                                    $placasremolque=$rs{"placasremolque"};
                                    $horallegada=$rs{"horallegada"};
                                    $ticketbascula=$rs{"ticketbascula"};
                                    $banco=$rs{"banco"};
                                    $estiba=$rs{"estiba"};
                                    $cantidad1=$rs{"cantidad1"};
                                    $cantidad2=$rs{"cantidad2"};
                                    $consecutivobodega=$rs{"consecutivobodega"};
                                    $folios=$rs{"folios"};;
                                    $observaciones=$rs{"observaciones"};;
		}
		$conexion->cerrar_consulta($result);                        
                        
                
                $sqlimagen="";
                $carpeta="";
                $imgtitulo="";
                if($tipoimagen=="i"){                        
                        $sqlimagen="select a.logotipo, a.nombrefabricante nombre, 
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
                                Where a.idbodega=".$idbodegadestino;
                $result = $conexion->consultar($sqlbodega);
                while($rs = $conexion->siguiente($result)){
                        $domiciliobodegadestino=$rs{"domicilio"}." ".$rs{"codigopostal"}." ".$rs{"telefonos"}." ".$rs{"municipio"}." ".$rs{"estado"};
                }
                $conexion->cerrar_consulta($result);                   
                
                
	
         //Genera Combo Transportista
                    $cmbtransportista="";
                    $sqltrans="Select idtransportista, razonsocial from operaciones_transportistas 
                                Where idtransportista=".$idtransportista."
                                order by razonsocial ";
                    $result = $conexion->consultar($sqltrans);
                    while($rs = $conexion->siguiente($result)){
                            $cmbtransportista="<strong>".$rs{"razonsocial"}."</strong>";
                    }
                    $conexion->cerrar_consulta($result);    
                
        //INICIA DIBUJANDO DATOS


	
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
        $html.=" <FORM id='envio' name='envio' method='post' action='envio_grabar.php'>";
	

        $html.= "<BODY onload='printSelec()' style='font-family:helvetica'>
                                        <center style='border-style: none'>
                                            <div id='printer'>";
                                            


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
				
                                $rn=$idenvio;
				//Datos de Facturación
				$html.="<td width='30%' align=right>";
						
					$html.="<table class='reporte' width='100%'>";
					
					//Serie y Folio
					$html.="<tr class='trencabezado'><td><b>REMISION ENVIO</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center>";
					$html.="	<b>Folio:</b> <font color=red>".$idenvio."</font> 
                                                    -   <b>Folio Interno:</b> <font  color=red> ".$consecutivobodega;
					$html.="	</font>";
					$html.="</td>";
					$html.="</tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                        $st=$fechaenvio;
                                            
                                        
                                        
                                        
                                        
					
					//Fecha del movimiento
					$html.="<tr class='trencabezado'><td><b>EMISIÓN</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center style='font-size:7pt'>";
					$fecha = new DateTime($fecha);
					$fechainfo = $fecha->format('Y-m-d')." ".$fecha->format('H:i:s');									
					$html.=$st;
					$html.="</td>";
					$html.="</tr>";
					
					$html.="</table>";
							
							
				$html.="</td>";				
			$html.="</tr>"; 
		$html.="</table>"; 
		$html.="</td></tr>"; //Mega tabla		
		//Concluye información del emisor, folio y emisión
							
											
		//Inicia sección del receptor y info de folios y certificado digital
		$html.="<tr><td>"; //Mega tabla
                    $html.="<table width='100%'>";	
                            $html.="<tr>"; 

                            //INFORMACION DEL EMISOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>BODEGA ORIGEN</td></tr>";
                                            $html.="<tr height='55' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b>".$bodegaorigen."</b><br>".$domiciliobodegaorigen."<br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                            //INFORMACION DEL RECEPTOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>BODEGA DESTINO</td></tr>";
                                            $html.="<tr height='55' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b>".$bodegadestino."</b><br>".$domiciliobodegadestino."<br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";								
			

                        
			//INFORMACION DEL CERTIFICADO DE LA EMPRESA
			$html.="<td width='20%'>";
				$html.="<table class='reporte' width='100%'>";
										
					//Obteniendo los datos de aprobación de la remesa de folios...
					$html.="<tr class='trencabezado'><td>OTFC: <b>".$otfc."</b></td></tr>";
					$html.="<tr><td align=left>
                                                   INICIAL: <b>".$saldoinicial." TM</b><br>
                                                   ENVIADO: <b>".$retirada." TM</b><br>
                                                   SALDO: <b>".$saldo." TM</b><br>    
                                                </td></tr>";																			
																			

				$html.="</table>";								
			$html.="</td>";
																						
		$html.="</tr>";
		$html.="</table>";
		$html.="</td></tr>"; //Mega tabla
		//Fin sección del receptor y info de folios y certificado digital
		
                
 //INICIAN DATOS DE CAPTURA 
                
                //Inicia sección de forma de pago e impuestos...
		$html.="<tr><td>"; //Mega tabla		
		$html.="<table width='100%'>";
			$html.="<tr>";				
			
//DATOS TRANSPORTE
				$html.="<td width='40%' valign='top'>";
				
					
					$html.="<table class='reporte' width='100%'>";
                                        $html.="<tr class='trencabezado'><td>DATOS TRANSPORTE</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
						$html.="<tr>
                                                            <td width=30%>TRANSPORTISTA:</td>
                                                            <td align=left>".$cmbtransportista."</td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>CARTA PORTE:</td>
                                                            <td align=left><b>".$cartaporte."</b></td>
                                                        </tr>";	
						$html.="<tr>
                                                            <td width=30%>NOMBRE OPERADOR:</td>
                                                            <td align=left><b>".$nombreoperador."</b></td>
                                                        </tr>";
                                                
                                                $html.="<tr>
                                                            <td colspan=2 align=left width=30%>
                                                                PLACAS TRACTOR:".$placastractor."
                                                                PLACAS REMOLQUE:<b>".$placasremolque."</b>
                                                            </td>
                                                        </tr>";
	                                                   
						$html.="<tr>
                                                            <td width=30%>LLEGADA APROXIMADA:</td>
                                                            <td align=left><b>".$horallegada."</b>
                                                        </tr>";
                                               
                                        $html.="</table>";
				$html.="</td>";
				
				
//DATOS BODEGA ORIGEN
				$html.="<td width='40%' valign='top'>";
				
					
					$html.="<table class='reporte' width='100%'>";
                                        $html.="<tr class='trencabezado'><td>DATOS BODEGA</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
						$html.="<tr align=left>
                                                            <td colspan=2>
                                                                # BANCO:<b>".$banco."</b>
                                                                # ESTIBA:<b>".$estiba."</b>
                                                            </td>
                                                        </tr>";
						$html.="<tr>
                                                            <td>TICKET BASCULA:</td>
                                                            <td align=left><b>".$ticketbascula."</b></td>
                                                        </tr>";                                                
						$html.="<tr>
                                                            <td>FOLIOS:</td>
                                                            <td align=left><b>".$folios."</b></td>
                                                        </tr>";	                                                
						                                                 

						$html.="<tr>
                                                            <td>OBSERVACIONES:</td>
                                                            <td align=left>".$observaciones."</td>
                                                        </tr>";				
					$html.="</table>";
				$html.="</td>";
									
			$html.="</tr>";				
		$html.="</table>";
		$html.="</td></tr>"; //Mega tabla
		//Finaliza sección de forma de pago e impuestos
                
        //BUSCA DATOS PRODUCTO
                //Obtiene Etiqueta Descripcion Cantidad principal
                $edita=2;
                $factor=0;
                $desc1="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,u.factor FROM inventarios_productos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$idproducto;
                    $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $desc1 = $rs["descripcionunidad"];
                }
                $conexion->cerrar_consulta($result);
                
                //Asignacion de Etiqueta Cantidad Secundaria
                
                $desc2="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,ifnull(i.factor,0) factor,i.idtipounidadmedida edita  
                    FROM inventarios_unidadesproductos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$idproducto." Limit 1";
                    $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $desc2 = $rs["descripcionunidad"];
                        $factor= $rs["factor"];
                        $edita=$rs{"edita"}; //POLOTICA SI EDITA=1 NO PERMITE MODIFICAR, EDITA=2 PERMITE MODIFICAR 
                }

                $conexion->cerrar_consulta($result);
                $unidad1=$desc1;
                $unidad2=$desc2;
                
		//Inicia sección de conceptos
		$html.="<tr><td>"; //Mega tabla
		$html.="<center><table class='reporte' width='100%'>";

			//Armando encabezado
			$html.="
                                <tr class='trencabezado'><td colspan=6>DATOS DEL PRODUCTO</td></tr>
                                <tr class='trencabezado'>";
                                $html.="<td>MARCA</td>";
				$html.="<td>ZAFRA</td>";
				$html.="<td>PRODUCTO</td>";
				$html.="<td>ESTADO PRODUCTO</td>";
				$html.="<td>".$unidad1."</td>";
				$html.="<td>".$unidad2."</td>";				
			$html.="</tr>";

			//Obteniendo los datos
                                //#Politica de Registro adicional
                                $politica="";
                                if($edita==1){
                                    $politica= " readonly onChange='recalcula(".$factor.",".$edita.")'";
                                }
                                
				$html.="<tr class=trcontenido>";
                                        $html.="<td align=center>".$nombreingenio."</td>";
					$html.="<td align=center>".$zafra."</td>";
                                        $html.="<td align=center>".$nombreproducto."</td>";
                                        $html.="<td align=center>".$nombreestado."</td>";
                                        $html.="<td align=right>".$cantidad1."</td>";
					$html.="<td align=right>".$cantidad2."</td>";			
				$html.="</tr>";				
			$html.="</table></center>";	
		$html.="</td></tr>"; //Mega tabla
		//Finaliza sección de conceptos
			
                
                
                
                //Inicia sección de FIRMAS
		$html.="<tr><td>"; //Mega tabla
                    $html.="<br><br><table width='100%'>";	
                            $html.="<tr>"; 

                            //INFORMACION DEL EMISOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA SELLO BODEGA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b></b><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                            //INFORMACION DEL RECEPTOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA OPERADOR</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b></b><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td></table>";
                
                $html.="</tr>";				
		$html.="</table>";
                $html.="</tr></td>"; //Mega tabla
                



                //funciones javascript		
		$html_funcionesjavascript=" <script type='text/javascript'>
										function redireccion() {
											var pagina = '../../netwarelog/repolog/reporte.php';
											document.location.href=pagina;
										}
                                                                                function valor(num) {
                                                                                    var numerostring='',numero=0;
                                                                                    numero=num.replace(/,/,'');
                                                                                    return numero;
                                                                                }
                                                                                function format_number(pnumber,decimals){
                                                                                    if (isNaN(pnumber)) { return 0};
                                                                                    if (pnumber=='') { return 0};
                                                                                    var snum = new String(pnumber);
                                                                                    var sec = snum.split('.');
                                                                                    var whole = parseFloat(sec[0]);
                                                                                    var result = '';
                                                                                    if(sec.length > 1){
                                                                                        var dec = new String(sec[1]);
                                                                                        dec = String(parseFloat(sec[1])/Math.pow(10,(dec.length - decimals)));
                                                                                        dec = String(whole + Math.round(parseFloat(dec))/Math.pow(10,decimals));
                                                                                        var dot = dec.indexOf('.');
                                                                                        if(dot == -1){
                                                                                            dec += '.';
                                                                                            dot = dec.indexOf('.');
                                                                                        }
                                                                                        while(dec.length <= dot + decimals) { dec += '0'; }
                                                                                        result = dec;
                                                                                    } else{
                                                                                        var dot;
                                                                                        var dec = new String(whole);
                                                                                        dec += '.';
                                                                                        dot = dec.indexOf('.');
                                                                                        while(dec.length <= dot + decimals) { dec += '0'; }
                                                                                        result = dec;
                                                                                    }
                                                                                    return result;
                                                                                }
                                                                                function recalcula(factor,edita) {
                                                                                    var jfactor=0,jedita=0, cant1=0, cant2=0;
                                                                                    jfactor=factor;
                                                                                    jedita=edita;
                                                                                    cant1=valor(document.getElementById('txtcantidad1').value);
                                                                                    cant2=cant1*jfactor;
                                                                                    document.envio.txtcantidad2.value=format_number(cant2,2);
                                                                                }
                                                                                function printSelec() {
                                                                                        var c, tmp;
                                                                                        c = document.getElementById('printer');
                                                                                        tmp = window.open(' ','Impresión.');
                                                                                        tmp.document.open();
                                                                                        tmp.document.write(c.innerHTML);
                                                                                        tmp.document.close();
                                                                                        tmp.print();
                                                                                        tmp.close();
                                                                                }
                                                                                function pdf(idenvio){
                                                                                        var ref=0;
                                                                                        ref=idenvio;
                                                                                        document.location = 'pdf.php?p='+ref;
                                                                                }
									</script>";


           
                
                
                echo $html_funcionesjavascript;  

		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></div></form>";
                $html.="</html>";
                
                   	
	$conexion->cerrar();
	
	//echo $html;
	

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
	$dompdf->stream("remision_envio_folio_".$idenvio.".pdf");
	
	//Depurar liberías del pdf
	//$output = $dompdf->output();
	//echo $output;
	//$arr = array('Attachment'=>0);
	//$dompdf->stream(utf8_decode($rfc."-".$serie."-".$foliofactura.".pdf"),$arr);	
	
	function cambiautf8 ($dato){
		if(mb_detect_encoding($dato, "UTF-8") == "UTF-8"){
			$dato = utf8_encode($dato);
		}
		return $dato;
	}
?>