<?php
	
        
	include("../../netwarelog/catalog/conexionbd.php");

	
//RECUPERANDO VARIABLES
        $idretiro=$_GET["folio"];

        
    //OBTENIENDO INFORMACION BASICA
                    $oe="";
                    $fecha=date("d-m-Y g:i:s");
                    $nombreingenio="";
                    $idfabricante="";
                    $bodegaorigen="";
                    $idbodegaorigen="";
                    $nombrecliente="";
                    $zafra="";
                    $nombreproducto="";
                    $nombreestado="";
                    $saldoinicial=0;
                    $retirada=0;
                    $saldo=0;
                    $tipoimagen="";
                    $transportista="";
                    $fechaotfc="";
     		    $nombremarca="";
                    $cuadrilla="";
   //VARIABLES DEL ENVIO
                    
                    $fecharetiro="";
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
                    $nombrefamilia="";
		
				$sqlcan="";
				$sqldev="";
				$sqlcan="(select ifnull(sum(c.cantidad2),0) from logistica_cancelacionordenesentrega c 
                        where c.fechacancelacion<=now() and c.idordenentrega=lo.idordenentrega and c.idestadodocumento=1)";                        
                $sqldev="ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds
						where idordenentrega=lo.idordenentrega and lds.idestadodocumento=2),0)"; 
                    
		$sqlestatus="Select lo.referencia1 oefc, lo.fecha,  
                                of.nombrefabricante 'nombreingenio', ob.nombrebodega 'bodegaorigen',
                                vc.razonsocial 'cliente', il.descripcionlote 'zafra', 
                                ip.nombreproducto 'producto', ie.descripcionestado 'estado', 
                                format(lo.cantidad2,3) 'saldoinicial', 
								format(IFNULL(lo.cantidadretirada2,0),3) 'retirada',
								format($sqlcan,3) 'cancelado',
								format($sqldev,3) 'devuelto',
                                format(lo.cantidad2-IFNULL(lo.cantidadretirada2,0)-$sqlcan+$sqldev,3) 'saldo', 
                                    case when ob.idbodega in (select idbodega from relaciones_almacenadoras_bodegas t 
                                        inner join relaciones_almacenadoras_bodegas_detalle d on t.idalmacenadorabodega=d.idalmacenadorabodega 
                                        where idbodega=lo.idbodega) then 'a' else 'i' end 'logo',
                                of.idfabricante, ob.idbodega, vc.idcliente idcliente, lo.idproducto,
                                lr.fechasalida,lr.idtransportista,lr.cartaporte,lr.nombreoperador,lr.placastractor,
                                lr.placasremolque,lr.referencia1 refcliente,lr.ticketbascula,lr.banco, lr.estiba, 
                                format(lr.cantidad1,2) cantidad1, 
                                format(lr.cantidad2,2) cantidad2, lr.folios,
                                lr.observaciones,lr.licenciaoperador,ob.responsable,
                                concat(ifnull(em.nombre,''),' ',ifnull(em.apellido1,''),' ',ifnull(em.apellido2,'')) 'capturista', lr.consecutivobodega, vm.nombremarca,lr.sellos,lr.cuadrilla, ifa.nombrefamilia 
                             From logistica_ordenesentrega lo 
                                inner join operaciones_fabricantes of on of.idfabricante=lo.idfabricante
				inner join vista_marcas vm on vm.idmarca=lo.idmarca
                                inner join operaciones_bodegas ob on ob.idbodega=lo.idbodega
                                inner join ventas_clientes vc on vc.idcliente=lo.idcliente
                                inner join inventarios_productos ip on ip.idproducto=lo.idproducto
                                inner join  inventarios_estados ie on ie.idestadoproducto=lo.idestadoproducto
                                inner join inventarios_lotes il on il.idloteproducto=lo.idloteproducto
                                inner join logistica_retiros lr on lr.idordenentrega=lo.idordenentrega
				left join empleados em on em.idempleado=lr.idempleado
                                left join inventarios_familias ifa on ip.idfamilia=ifa.idfamilia
                             Where lr.idretiro=".$idretiro;
                
                //echo $sqlestatus;
                //exit();
                
                $result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                                    $nombrefamilia=$rs{"nombrefamilia"};
                                    $cancelado=$rs{"cancelado"};
                                    $devuelto=$rs{"devuelto"};
                                    $otfc=$rs{"oefc"};
                                    $fechaoefc=$rs{"fecha"}; 
                                    $nombreingenio=$rs{"nombreingenio"};
                                    $idfabricante=$rs{"idfabricante"};
                                    $bodegaorigen=$rs{"bodegaorigen"};
                                    $idbodegaorigen=$rs{"idbodega"};
                                    $nombrecliente= $rs{"cliente"};
                                    $idcliente= $rs{"idcliente"};
                                    $zafra= $rs{"zafra"};
                                    $nombreproducto= $rs{"producto"};
                                    $idproducto=$rs{"idproducto"};
                                    $nombreestado= $rs{"estado"};
                                    $saldoinicial= $rs{"saldoinicial"};
                                    $retirada= $rs{"retirada"};
									
                                    $saldo=$rs{"saldo"};
                                    $tipoimagen=$rs{"logo"};
                                    
                                       //VARIABLES DEL ENVIO
                                    $fecharetiro=$rs{"fechasalida"};
                                    $idtransportista=$rs{"idtransportista"};
                                    $cartaporte=$rs{"cartaporte"};
                                    $nombreoperador=$rs{"nombreoperador"};
                                    $placastractor=$rs{"placastractor"};
                                    $placasremolque=$rs{"placasremolque"};
                                    $referencia1=$rs{"refcliente"};
                                    $ticketbascula=$rs{"ticketbascula"};
                                    $banco=$rs{"banco"};
                                    $estiba=$rs{"estiba"};
                                    $cantidad1=$rs{"cantidad1"};
                                    $cantidad2=$rs{"cantidad2"};
                                    $consecutivobodega=$rs{"consecutivobodega"};
                                    $folios=$rs{"folios"};
                                    $observaciones=$rs{"observaciones"};
                                    $nombrecapturista=$rs{"capturista"};
									$licenciaoperador=$rs{"licenciaoperador"};
									$responsable=$rs{"responsable"};
									$nombremarca=$rs{"nombremarca"};
                                    $sellos=$rs["sellos"];
                                    $cuadrilla=$rs["cuadrilla"];
									
		}
            
		$conexion->cerrar_consulta($result);                        
                        
                
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
                
                $result = $conexion->consultar($sqlcliente);
                while($rs = $conexion->siguiente($result)){
                        $domiciliocliente=$rs{"domicilio"}." ".$rs{"telefonos"}." ".$rs{"municipio"}." ".$rs{"estado"};
                }
                $conexion->cerrar_consulta($result);                   
                
                
                
                
         //Genera Combo Transportista
         //Verifica Politica para Seleccionar a otros transportistas
            $sel="";
            //$cmbtransportista="<select id=cmbtransportista name=cmbtransportista>";
                    $sqltrans="Select idtransportista, concat(rfc,' ',razonsocial) razonsocial from operaciones_transportistas where idtransportista=".$idtransportista." order by razonsocial";

                    $result = $conexion->consultar($sqltrans);
                    while($rs = $conexion->siguiente($result)){
                            $cmbtransportista="<b>".$rs{"razonsocial"}."</b>";
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
        //$html.=" <FORM id='envio' name='envio' method='post' action='envio_grabar.php'>";
	
	$html.= "<body style='font-family:helvetica'>
                    <FORM id=envio name=envio method=post action=retiro_grabar.php>";                


        $html.= "<BODY onload='printSelec()' style='font-family:helvetica'>
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
			
                                        $nombreorganizacionAMZ= "ALMACENADORA MERCANTIL AMAZON";
                                        $domicilioAMZ="ANAXAGORAS 1329, LETRAN VALLE, DEL. BENITO JUAREZ, CDMX."; 
                                        $cpAMZ="03650";
                                        $municipioAMZ="MÉXICO";
                                        $estadoAMZ="CIUDAD DE MÉXICO";
                                        $telefonosAMZ= "5575742291";

					//Datos Organización
					$html.="<td width='45%' align=left style='font-family:helvetica;font-size:9pt;'>";				
						$html.="<b>".$nombreorganizacionAMZ."</b><br>";
						
						$html.=" <strong>DOMICILIO:</strong> ".$domicilioAMZ;
						$html.="<br> <strong>C.P.</strong> ".$cpAMZ;
						$html.="<br> ".$municipioAMZ;
						$html.=" ".$estadoAMZ;
                                                if($telefonosAMZ<>''){
                                                    $html.="<br><strong>TELEFONO:</strong> ".$telefonosAMZ;
                                                }
						$html.="<br>";					
					$html.="</td>";
				
                                $rn=$idretiro;
				//Datos de Facturación
				$html.="<td width='30%' align=right>";
						
					$html.="<table class='reporte' width='100%'>";
					
					//Serie y Folio
					$html.="<tr class='trencabezado'><td><b>REMISION SALIDA</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center style='font-size:13pt'>";
					$html.="	<b>Folio:</b> <font color=blue>".$rn."</font>";
					$html.="	</font>";
					$html.="</td>";
					$html.="</tr><tr align='center'><td align='center'>Folio Origen:<b> $consecutivobodega</b></td></tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                        $st="";
                                            $sqlbodega="select * from logistica_politicas where idpolitica=8";
                                            $result = $conexion->consultar($sqlbodega);
                                            while($rs = $conexion->siguiente($result)){
                                                if($rs{"aplica"}==0){ //No permitira que se edite la fecha
                                                    $st=" readonly  
                                                        style='text-align:right;color:#707070;background-color: #FFFFFF;border-width:0;font-size: 12px;'";
                                                }
                                            }
                                            $conexion->cerrar_consulta($result);
                                        
                                        
                                        
                                        
					
					//Fecha del movimiento
					$html.="<tr class='trencabezado'><td><b>EMISIÓN</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center style='font-size:13pt'>";
					$fecha = new DateTime($fecha);
					$fechainfo = $fecha->format('Y-m-d')." ".$fecha->format('H:i:s');	
                                        
                                        $fecha_formateada = date("d/m/Y H:i:s", strtotime($fecharetiro));
                                        $html.="<b>".$fecha_formateada."</b>";
					//$html.="<b>".$fecharetiro."</b>";
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
                                            $html.="<tr class='trencabezado'><td>CLIENTE</td></tr>";
                                            $html.="<tr height='55' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b>".$nombrecliente."</b><br>".$domiciliocliente."<br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";								
			
			//INFORMACION DEL CERTIFICADO DE LA EMPRESA
			$html.="<td width='20%'>";
				$html.="<table class='reporte' width='100%'>";
										
					//Obteniendo los datos de aprobación de la remesa de folios...
					$html.="<tr class='trencabezado' style='font-size:13pt'><td>REFERENCIA: <b>".$otfc."</b></td></tr>";
					$html.="<tr><td align=left>
                                                   INICIAL: <b>".$saldoinicial." TM</b><br>
                                                   ENVIADO: <b>".$retirada." TM</b><br>
												   CANELADO: <b>".$cancelado." TM</b><br>
												   DEVUELTO: <b>".$devuelto." TM</b><br>
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
                                                            <td align=left>".$cartaporte."</td>
                                                        </tr>";	
						$html.="<tr>
                                                            <td width=30%>NOMBRE OPERADOR:</td>
                                                            <td align=left>".$nombreoperador."</td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>LICENCIA OPERADOR:</td>
                                                            <td align=left>".$licenciaoperador."</td>
                                                        </tr>";
														
                                                $html.="<tr>
                                                            <td colspan=2 align=left width=30%>
                                                                PLACAS TRACTOR:".$placastractor."
                                                                PLACAS REMOLQUE:".$placasremolque."
                                                            </td>
                                                        </tr>";
	                                                   
						$html.="<tr>
                                                            <td width=30%>FOLIO CLIENTE:</td>
                                                            <td align=left>".$referencia1."</td>
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
                                                                # BANCO:".$banco."
                                                                # ESTIBA:".$estiba."
                                                                # CUADRILLA: ".$cuadrilla." 
                                                            </td>
                                                        </tr>";
						$html.="<tr>
                                                            <td>REMISIÓN INGENIO:</td>
                                                            <td align=left>".$ticketbascula."
                                                        </tr>";                                                
						$html.="<tr>
                                                            <td>FOLIOS:</td>
                                                            <td align=left>".$folios."</td>
                                                        </tr>";	                                                
						                                                 
						$html.="<tr>
                                                            <td>SELLOS:</td>
                                                            <td align=left>".$sellos."</td>
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
                                $html.="<td>INGENIO-MARCA</td>";
				$html.="<td>ZAFRA</td>";
				$html.="<td>PRODUCTO</td>";
				//$html.="<td>ESTADO PRODUCTO</td>";
				$html.="<td>".$unidad1."</td>";
				$html.="<td>".$unidad2."</td>";				
			$html.="</tr>";
                        $saldosc=0;
                        $saldosc=str_replace(',','',$saldo);
                        
			//Obteniendo los datos
                                //#Politica de Registro adicional
                                $politica="";
                                if($edita==1){
                                    $politica= " readonly onChange='recalcula(".$factor.",".$edita.",".$saldosc.")'";
                                }
				$temp="$nombreingenio <br> $nombremarca";
				if($nombreingenio==$nombremarca){
					$temp="$nombreingenio - Propia";
				}                
				$html.="<tr class=trcontenido>";
                                        $html.="<td align=center>".$temp."</td>";
					$html.="<td align=center>".$zafra."</td>";
                                        $html.="<td align=center>".$nombrefamilia." ".$nombreproducto."</td>";
                                        //$html.="<td align=center>".$nombreestado."</td>";
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
                                            $html.="<tr class='trencabezado'><td>FIRMA CAPTURISTA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b><center>".$nombrecapturista."</center></b><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                            //INFORMACION DEL EMISOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA OPERADOR</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b><center>".$nombreoperador."</center></b><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";							
                            //INFORMACION DEL RECEPTOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA ALMACENISTA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b><center>".$responsable."</center></b><br>";
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
                                                                                function recalcula(factor,edita,saldo) {
                                                                                    var jfactor=0,jfactord=0,jedita=0,jsaldo1=0,jsaldo2=0, cant1=0, cant2=0,jsaldo=0;
                                                                                    jfactor=factor;
                                                                                    jedita=edita;
                                                                                    jfactord=jfactor;
                                                                                    jsaldo=saldo;
                                                                                    if(jfactor==0){
                                                                                        jfactord=1;
                                                                                    }
                                                                                    jsaldo1=jsaldo/jfactor;
                                                                                    jsaldo2=jsaldo;
                                                                                    cant1=valor(document.getElementById('txtcantidad1').value);
                                                                                    cant2=cant1*jfactor; 
                                                                                    if((cant1<=jsaldo1) && (cant2<=jsaldo2)){
                                                                                        cant1=valor(document.getElementById('txtcantidad1').value);
                                                                                        cant2=cant1*jfactor;
                                                                                        document.envio.txtcantidad2.value=format_number(cant2,2);                                                                                        
                                                                                    }else{ 
                                                                                        alert('Las cantidades Exeden el Saldo de la Instruccion');
                                                                                        document.envio.txtcantidad1.value=format_number(0,2);
                                                                                        document.envio.txtcantidad2.value=format_number(0,2);
                                                                                    }
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
                                                                                        document.location = 'pdf.php?idenvio='+ref;
                                                                                }
									</script>";
		//Botones							
		$html_botones="	<INPUT name='btngrabar' class='buttons_text' type='submit' value='Procesar' title='Haz Click Para Autorizar'>
                                <INPUT name=btnregresar type='button' onclick='redireccion()' value='Regresar'>";


                $opciones="
                <left>    
                <div id=opciones>
                    <table  width=100>
                        <td width=16 align=right>
                            <a href='javascript:printSelec();'><img src='../../netwarelog/repolog/img/impresora.png' border='0'></a>
                        </td>
                        <td width=16  align=right>
                                <a href='../../netwarelog/repolog/reporte.php'> <img src='../../netwarelog/repolog/img/filtros.png' 
                                        title ='Haga clic aqui para cambiar filtros...' border='0'> </a>
                        </td>
                    </table>
                </div><left>";
                
                echo $html_funcionesjavascript;  
                
                //$html.="<tr><td>"; //Mega tabla
                //    $html.= "<center><table><tr><td>".$html_botones."</td></tr></table></center>";
                //$html.="</tr></td>"; //Mega tabla
                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></div></form>";
                $html.="</html>";
                
                   
                

echo $opciones.$html;

?>

