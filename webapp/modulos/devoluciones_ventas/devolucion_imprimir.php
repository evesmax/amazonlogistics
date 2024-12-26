<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");

	
//RECUPERANDO VARIABLES
        
	session_start();
	$usuario= $_SESSION["accelog_idempleado"];
        
    //OBTENIENDO INFORMACION BASICA
                    $idtraslado=0;
                    $idenvio=0;
                    $idrecepcion=0;                    
                    $iddevolucion=$_GET["folio"];
                    
                    $fecha=date("d-m-Y g:i:s");;
                    $ot="";
                    $fechaot="";
                    $fechaenvio="";
                    $fecharecepcion="";
                    $nombreingenio="";
                    $idfabricante="";
                    $bodegaorigen="";
                    $idbodegaorigen="";
                    $bodegadestino="";
                    $idbodegadestino="";                    
                    
                    $zafra="";
                    $nombreproducto="";
                    $nombreestado="";
                    
                    $idproducto="";
                    $tipoimagen="";
                    $transportista="";
                    $capturista=$usuario;
                    $responsable="";
                    $saldo=0;
                    
                    $cartaporte="";
                    $operador="";
                    $licenciaoperador="";
                    $placastractor="";
                    $placasremolque="";
                    
                    
                    $enviada1=0;
                    $recibida1=0;
                    $devuelta1=0;
                    $diferencia1=0;
                    
		
		$sqlestatus="
                            Select lr.idrecepcion, lt.referencia1,lt.fecha fechaoe, le.fechaenvio, lr.fecharecepcion,
                                    of.nombrefabricante, obo.nombrebodega bodegaorigen,
                                    obd.nombrebodega bodegadestino, ot.razonsocial transportista,
                                    ld.cartaporte, ld.operador nombreoperador,ld.placastractor, lr.licenciaoperador,
                                    ld.placasremolque, il.descripcionlote zafra, 
                                    ip.nombreproducto,ip.idproducto, ie.descripcionestado estadoproducto, 
                                    format(le.cantidad1,2) enviadabts, format(le.cantidad2,2) enviadatm,
                                    format(lr.cantidadrecibida1,2) recibidabts, format(lr.cantidadrecibida2,2) recibidatm, 
                                    ifnull(ld.cantidad1,0) devueltabts, format(ifnull(ld.cantidad2,0),2) devueltatm, 
                                    format(le.cantidad1-ifnull(lr.cantidadrecibida1,0)-ifnull(ld.cantidad1,0),2) diferenciabts, 
                                    format(le.cantidad2-ifnull(lr.cantidadrecibida2,0)-ifnull(ld.cantidad2,0),2) diferenciatm,
                                    ld.observaciones, lt.idfabricante, lt.idbodegaorigen, lt.idbodegadestino, ot.idtransportista,
                                    obd.responsable, le.idenvio, lt.idtraslado, ld.folios, lr.idrecepcion
                            From logistica_traslados lt 
                                    left join operaciones_fabricantes of on of.idfabricante=lt.idfabricante
                                    left join operaciones_bodegas obo on obo.idbodega=lt.idbodegaorigen
                                    left join operaciones_bodegas obd on obd.idbodega=lt.idbodegadestino
                                    left join inventarios_productos ip on ip.idproducto=lt.idproducto
                                    left join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto
                                    left join inventarios_lotes il on il.idloteproducto=lt.idloteproducto 
                                    left join logistica_envios le on le.idtraslado=lt.idtraslado 
                                    left join logistica_recepciones lr on lr.idtraslado=lt.idtraslado and lr.idenvio=le.idenvio
                                    left join logistica_devoluciones ld on ld.idrecepcion=lr.idrecepcion 
                                    left join operaciones_transportistas ot on ot.idtransportista=ld.idtransportista                    
                            Where ld.iddevolucion=$iddevolucion order by lr.idrecepcion";
//echo $sqlestatus;
//exit();                
    $result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                        $ot=$rs{"referencia1"};
                        $fechaot=$rs{"fechaoe"};
                        $fechaenvio=$rs{"fechaenvio"};
                        $fecharecepcion=$rs{"fecharecepcion"};
                        $nombreingenio=$rs{"nombrefabricante"};
                        $idfabricante=$rs{"idfabricante"};
                        $bodegaorigen=$rs{"bodegaorigen"};
                        $idbodegaorigen=$rs{"idbodegaorigen"};
                        $bodegadestino=$rs{"bodegadestino"};
                        $idbodegadestino=$rs{"idbodegadestino"};                    
                        $zafra=$rs{"zafra"};
                        $nombreproducto=$rs{"nombreproducto"};
                        $nombreestado=$rs{"estadoproducto"};
                        $idproducto=$rs{"idproducto"};
                        $tipoimagen="a";
                        $nombretransportista=$rs{"transportista"};
                        $idtransportista=$rs{"idtransportista"};
                        $responsable=$rs{"responsable"};
                        $saldo=$rs{"diferenciatm"};
                        
                        $cartaporte=$rs{"cartaporte"};
                        $operador=$rs{"nombreoperador"};
                        $licenciaoperador=$rs{"licenciaoperador"};
                        $placastractor=$rs{"placastractor"};
                        $placasremolque=$rs{"placasremolque"};
                        
                        $enviada1=$rs{"enviadabts"};
                        $recibida1=$rs{"recibidabts"};
                        $devuelta1=$rs{"devueltabts"};
                        $devuelta2=$rs{"devueltatm"};
                        $diferencia1=$rs{"diferenciabts"};
                        $idenvio=$rs{"idenvio"};
                        $idtraslado=$rs{"idtraslado"};
                        $idrecepcion=$rs{"idrecepcion"};
                        
                        $observaciones=$rs{"observaciones"};
                        $folios=$rs{"folios"};
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
                                        where idbodega=".$idbodegadestino;
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
                
//echo $domiciliobodegaorigen." <br> ".$domiciliobodegadestino;                
         //Genera Combo Transportista
         //Verifica Politica para Seleccionar a otros transportistas
            $sel="";
            $cmbtransportista="";
                    $sqltrans="Select idtransportista, razonsocial 
					from operaciones_transportistas where idtransportista=$idtransportista order by razonsocial";

                    $result = $conexion->consultar($sqltrans);
                    while($rs = $conexion->siguiente($result)){
                            $cmbtransportista=$rs{"razonsocial"};
                    }
                    $conexion->cerrar_consulta($result);
         
             //Genera  empleado
            $nombrecapturista="";
            $txtcapturista="<input type=hidden id='txtcapturista' name='txtcapturista' value='".$capturista."'>";
                    $sqltrans="Select concat(nombre,' ',apellido1,' ',apellido2) capturista from empleados where idempleado=".$capturista;

                    $result = $conexion->consultar($sqltrans);
                    while($rs = $conexion->siguiente($result)){
                            $nombrecapturista=$rs{"capturista"};
                    }
                    $conexion->cerrar_consulta($result); 
					
        //INICIA DIBUJANDO DATOS
               

           
	
	$html="<html>";
	$html.= "<head>";
	//Utiliza por omisión el estilo 1 del repolog
	$html.= "<LINK href='pdf/pdf_factura_css/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";
	$html.="<script language='javascript' type='text/javascript' src='../../netwarelog/catalog/js/jquery.js'></script>";	
	$html.= "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>";
	$html.= "<meta name='author-icons' content='Rachel Fu'>";
	$html.= "<style>";
	$html.= "  body{font-size:6.5pt;color:black}";	
	$html.= "  td{font-size:7pt}";
	$html.= "</style>";
	$html.= "</head>";
	//Escribe "Politicas""
	$html.= "<body style='font-family:helvetica'>";
 
        $html.= "<BODY onload='printSelec()' style='font-family:helvetica'>
                                        <center style='border-style: none'>
                                            <div id='printer'>";
        
        $html.= "<LINK href='pdf/pdf_factura_css/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";                                  

                                        

	$html.=$txtcapturista;

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
				
                                $rn=$iddevolucion;
				//Datos de Facturación
				$html.="<td width='30%' align=right>";
						
					$html.="<table class='reporte' width='100%'>";
					
					//Serie y Folio
					$html.="<tr class='trencabezado'><td><b>REMISION DEVOLUCION</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center>";
					$html.="	<b>Folio:</b> <font color=blue size=5>".$rn."</font>";  
					$html.="	</font>";
					$html.="</td>";
					$html.="</tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                        $st="";
 
                                        $st=" readonly  
                                            style='text-align:right;color:#707070;background-color: #FFFFFF;border-width:0;font-size: 12px;'";

                                        
                                        
					//Fecha del movimiento
					$html.="<tr class='trencabezado'><td><b>EMISIÓN</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center style='font-size:7pt'>";
					$fecha = new DateTime($fecha);
					$fechainfo = $fecha->format('Y-m-d')." ".$fecha->format('H:i:s');									
					$html.="<input type=text ".$st." id='txtfechadevolucion' name='txtfechadevolucion' value='".$fechainfo."'>";
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
                                                            $html.="<b>".$bodegadestino."</b><br>".$domiciliobodegadestino."<br>";
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
                                                            $html.="<b>".$bodegaorigen."</b><br>".$domiciliobodegaorigen."<br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";								

                            



			$html.="<td width='20%'>";
				$html.="<table class='reporte' width='100%'>";
										
					//Obteniendo los datos de aprobación de la remesa de folios...
					$html.="<tr class='trencabezado'><td>OT: <b>".$ot."</b>--Envio:<b>$idenvio</b>--Recepcion:<b>$idrecepcion</b></td></tr>";
					$html.="<tr><td align=left>
                                                   Envio:<b>$enviada1</b><br>
                                                   Recepcion:<b>$recibida1</b><br>
                                                   Devoluciones:<b>$devuelta1</b><br>
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
                                                            <td align=left><b>$cmbtransportista</b></td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>CARTA PORTE:</td>
                                                            <td align=left><b>$cartaporte</b></td>
                                                        </tr>";
                                                
						$html.="<tr>
                                                            <td width=30%>NOMBRE OPERADOR:</td>
                                                            <td align=left><b>$operador</b></td>
                                                        </tr>";
														
                                                $html.="<tr>
                                                            <td colspan=2 align=left width=30%>
                                                                PLACAS TRACTOR:<b>$placastractor</b>
                                                                PLACAS REMOLQUE:<b>$placasremolque</b>
                                                            </td>
                                                        </tr>";
	                                                   
                                               
                                        $html.="</table>";
				$html.="</td>";
				
				
//DATOS BODEGA ORIGEN
				$html.="<td width='40%' valign='top'>";
				
					
					$html.="<table class='reporte' width='100%'>";
                                        $html.="<tr class='trencabezado'><td>DATOS BODEGA</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
                                               
						$html.="<tr>
                                                            <td>FOLIOS:</td>
                                                            <td align=left><b>$folios</b></td>
                                                        </tr>";	                                                
						                                                 

						$html.="<tr>
                                                            <td>OBSERVACIONES:</td>
                                                            <td align=left><b>$observaciones</b></td>
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
                        $saldosc=0;
                        $saldosc=str_replace(',','',$saldo);
                        
			//Obteniendo los datos
                                //#Politica de Registro adicional
                                $politica="";
                                if($edita==1){
                                    $politica= " readonly onChange='recalcula(".$factor.",".$edita.",".$saldosc.")'";
                                }
                                
				$html.="<tr class=trcontenido>";
                                        $html.="<td align=center>".$nombreingenio."</td>";
					$html.="<td align=center>".$zafra."</td>";
                                        $html.="<td align=center>".$nombreproducto."</td>";
                                        $html.="<td align=center>".$nombreestado."</td>";
                                        $html.="<td align=right><b>$devuelta1</b></td>";
					$html.="<td align=right><b>$devuelta2</b></td>";			
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
                                            $html.="<tr class='trencabezado'><td>FIRMA ALMACENISTA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b><center>".$responsable."</center></b><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
							$nombreoperador="";
                            //INFORMACION DEL RECEPTOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA OPERADOR</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<b><center><b>$operador</b></center></b><br>";
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
                        <td width=16 align=right>
                                <a href='javascript:pdf(".$iddevolucion.");'> <img src='../../netwarelog/repolog/img/pdf.gif'  
                                   title ='Generar reporte en PDF' border='0'> 
                                </a>
                        </td>
                        <td width=16  align=right>
                                <a href='../../netwarelog/repolog/reporte.php'> <img src='../../netwarelog/repolog/img/filtros.png' 
                                        title ='Haga clic aqui para cambiar filtros...' border='0'> </a>
                        </td>
                    </table>
                </div><left>";
                
                echo $html_funcionesjavascript;  
                
                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></div></form>";
                $html.="</html>";
                
                   
                
//Depuracion
echo $opciones.$html;

?>