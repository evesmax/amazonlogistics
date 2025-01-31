<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $idrecepcion=$_GET["idrecepcion"];
        
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
                    
                    
    //Variables Recepcion   
                    $fecharecepcion="";
                    $bancor="";
                    $estibar="";
                    $ticketbascular="";
                    $referenciar="";
                    $observacionesr="";
                    $almacenista="";
                    $supervisor="";
                    $cabocuadrilla="";
                    $cantidadenviada1="";
                    $cantidadenviada2="";
                    $cantidadrecibida1="";
                    $cantidadrecibida2="";
                    $idbodega="";   //Bodega Real
                    $diferencia1="";
                    $diferencia2="";
                    $foliosr="";
					$licenciaoperador="";
					$responsable="";
					$consecutivobodega=0;
            
		$sqlestatus="Select le.idenvio,lt.idtraslado,lt.referencia1 otfc, lt.fecha,  
                                of.nombrefabricante 'nombreingenio', obo.nombrebodega 'bodegaorigen',
                                obd.nombrebodega 'bodegadestino', il.descripcionlote 'zafra', 
                                ip.nombreproducto 'producto', ie.descripcionestado 'estado', 
                                format(lt.cantidad2,3) 'saldoinicial', format(IFNULL(lt.cantidadretirada2,0),3) 'retirada'
                                ,format(IFNULL(lt.cantidadrecibida2,0),3) 'recibida', 
                                format(lt.cantidad2-IFNULL(lt.cantidadretirada2,0),3) 'saldo', 
                                case when obd.idbodega in (select idbodega from relaciones_almacenadoras_bodegas t 
                                    inner join relaciones_almacenadoras_bodegas_detalle d on t.idalmacenadorabodega=d.idalmacenadorabodega 
                                    where idbodega=lt.idbodegadestino) then 'a' else 'i' end 'logo', ot.razonsocial transportista,
                                of.idfabricante, obo.idbodega, obd.idbodega idbodegadestino, ot.idtransportista, lt.idproducto,
                                le.fechaenvio,le.idtransportista,le.cartaporte,le.nombreoperador,le.placastractor,
                                    le.placasremolque,le.horallegada,le.ticketbascula,le.banco, le.estiba, 
                                    format(le.cantidad1,2) cantidad1, format(le.cantidad2,2) cantidad2,  
                                    le.folios,le.observaciones,
                                    lr.fecharecepcion, lr.banco 'bancor', lr.estiba 'estibar', lr.ticketbascula 'ticketbascular',
                                    lr.referencia 'referenciar',lr.almacenista,lr.supervisor,lr.cabocuadrilla,
                                    lr.cantidadrecibida1,lr.cantidadrecibida2,lr.diferencia1,lr.diferencia2,
                                    lr.folios 'foliosr', lr.observaciones 'observacionesr',
                                    le.licenciaoperador,obd.responsable,concat(em.nombre,' ',em.apellido1,' ',em.apellido2) 'capturista', lr.consecutivobodega, vm.nombremarca
                             From logistica_traslados lt 
                                inner join operaciones_fabricantes of on of.idfabricante=lt.idfabricante
                                inner join vista_marcas vm on lt.idmarca=vm.idmarca
                                inner join operaciones_bodegas obo on obo.idbodega=lt.idbodegaorigen
                                inner join operaciones_bodegas obd on obd.idbodega=lt.idbodegadestino
                                inner join inventarios_productos ip on ip.idproducto=lt.idproducto
                                inner join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto
                                inner join inventarios_lotes il on il.idloteproducto=lt.idloteproducto
                                inner join operaciones_transportistas ot on ot.idtransportista=lt.idtransportista 
                                inner join logistica_envios le on le.idtraslado=lt.idtraslado
                                inner join logistica_recepciones lr on lr.idtraslado=lt.idtraslado and lr.idenvio=le.idenvio
								left join empleados em on em.idempleado=lr.idempleado
                             Where lr.idrecepcion=".$idrecepcion;
              //echo $sqlestatus;

                
                $result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                        //Asignando Valores del Traslado
                                    $idtraslado=$rs{"idtraslado"};
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
                                    $idenvio=$rs{"idenvio"};
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
                                    $folios=$rs{"foliosr"};
                                    $observacionesEnv=$rs{"observaciones"};
                        //Agregando Valores de Recepcion
                                    $fecharecepcion=$rs{"fecharecepcion"};
                                    $bancor=$rs{"bancor"};
                                    $estibar=$rs{"estibar"};
                                    $ticketbascular=$rs{"ticketbascular"};
                                    $referenciar=$rs{"referenciar"};
                                    $observacionesr=$rs{"observacionesr"};
                                    $almacenista=$rs{"almacenista"};
                                    $supervisor=$rs{"supervisor"};
                                    $cabocuadrilla=$rs{"cabocuadrilla"};
                                    $cantidadenviada1=0;
                                    $cantidadenviada2=0;
                                    $cantidadrecibida1=$rs{"cantidadrecibida1"};
                                    $cantidadrecibida2=$rs{"cantidadrecibida2"};
                                    $idbodega=$rs{"idbodega"};
                                    $diferencia1=$rs{"diferencia1"};
                                    $diferencia2=$rs{"diferencia2"};
                                    $foliosr=$rs{"foliosr"};
					$licenciaoperador=$rs{"licenciaoperador"};
					$responsable=$rs{"responsable"};	
					$nombrecapturista=$rs{"capturista"};
                                    $marca=$rs{"nombremarca"};
                                    $observacionesr=$rs{"observacionesr"};

									
									
		}
		$conexion->cerrar_consulta($result);                        
                        
                
                $sqlimagen="";
                $carpeta="";
                $imgtitulo="";
				//$tipoimagen="a";
                if($tipoimagen=="i"){                        
                        $sqlimagen="select a.logotipo, concat(ifnull(a.rfc,''),' ',a.nombrefabricante) nombre, 
                            concat(a.calle,' ',ifnull(concat('No. ',a.noexterior),''),ifnull(concat('No Int. ',a.nointerior),'')) domicilio,  
                            mu.nombremunicipio municipio, es.nombreestado estado, a.codigopostal, '' telefonos  
							from operaciones_fabricantes a
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
                                        left join operaciones_almacenadoras a on a.idalmacenadora=t.idalmacenadora
                                        left join general_estados es on es.idestado=a.idestado
                                        left join general_municipios mu on mu.idmunicipio=a.idmunicipio
                                        left join operaciones_bodegas o on d.idbodega=o.idbodega Where d.idbodega=".$idbodegadestino;
                        $carpeta="../../netwarelog/archivos/1/operaciones_almacenadoras/";
                }
                //Obtiene Nombre de la Imagen
				//echo $sqlimagen;
				
				$logotipo="";
				$nombre="";
				$domicilio="";
				$cp="";
				$municipio="";
				$estado="";
				$telefonos="";
				
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
                
			//Precio Bulto
            $fechaprecio=$fecharecepcion;    
            $preciobulto=0;  
			$importetotal=0;			
            $sqlpreciobulto="select lp.preciobulto from logistica_precios_sniim lp where '$fechaprecio' between lp.fechainicial and lp.fechafinal and idproducto=$idproducto limit 1";      
                    $result = $conexion->consultar($sqlpreciobulto);
                    while($rs = $conexion->siguiente($result)){
                        $preciobulto=$rs{"preciobulto"};
                    }
            $conexion->cerrar_consulta($result);           
            //echo $sqlpreciobulto."<br>";
			$importetotal=$preciobulto*$cantidadrecibida1;
			
         //Genera Combo Transportista
                    $cmbtransportista="";
                    $sqltrans="Select idtransportista, concat(rfc,' ',razonsocial) razonsocial from operaciones_transportistas 
                                Where idtransportista=".$idtransportista."
                                order by razonsocial ";
                    $result = $conexion->consultar($sqltrans);
                    while($rs = $conexion->siguiente($result)){
                            $cmbtransportista="<strong>".$rs{"razonsocial"}."</strong>";
                    }
                    $conexion->cerrar_consulta($result);    
                
        

         //Genera Combo Bodegas
         //Verifica Politica para Seleccionar a otras bodegas
            $sel="";
            $cmbbodega="";
                    $sqlbod="Select idbodega, nombrebodega from operaciones_bodegas where idbodega=".$idbodega." order by nombrebodega";

                    $result = $conexion->consultar($sqlbod);
                    while($rs = $conexion->siguiente($result)){
                            $cmbbodega.="<b>".$rs{"nombrebodega"}."</b>";
                    }
                    $conexion->cerrar_consulta($result);  
                    

        //INICIA DIBUJANDO DATOS


	
	$html="<html>";
	$html.= "<head>";
	//Utiliza por omisión el estilo 1 del repolog
	
	$html.= "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>";
	$html.= "<meta name='author-icons' content='Rachel Fu'>";
	$html.= "<style>";
	$html.= "  body{font-size:6.5pt;color:black}";	
	$html.= "  td{font-size:7pt}";
	$html.= "</style>";
	$html.= "</head>";
        $html.=" <FORM id='envio' name='envio' method='post' action='recepcion_grabar.php'>";
	

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
				
					//Datos Organización
                                        $html.="<td width='45%' align=left style='font-family:helvetica;font-size:9pt;'>";				
						$html.="<b>Propietario: ".$nombreorganizacion."<br> Marca: ".$marca."</b><br>";
						
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
					$html.="<tr class='trencabezado'><td><b>REMISION RECEPCION</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center>";
					$html.="	<b>Folio:</b> <font color=red>".$idrecepcion."</font> 
                                                    -   <b>Folio Interno:</b> <font  color=red>".$consecutivobodega;
					$html.="	</font>";
					$html.="</td>";
					$html.="</tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                            $st="";
                                            
                                        
                                        
					
					//Fecha del movimiento
					$html.="<tr class='trencabezado'><td><b>EMISIÓN</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center style='font-size:7pt'>";									
					$html.="<b>".$fecharecepcion."</b>";
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
                                                            $html.="<b>".$bodegaorigen."</b><br>".$domiciliobodegaorigen."";
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
                                                            $html.="<b>".$bodegadestino."</b><br>".$domiciliobodegadestino."";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";								
			

                        
			//INFORMACION DEL CERTIFICADO DE LA EMPRESA
			$html.="<td width='20%'>";
				$html.="<table class='reporte' width='100%'>";
										
					//Obteniendo los datos de aprobación de la remesa de folios...
					/*
                                        $html.="<tr class='trencabezado'><td>OTFC: <b>".$otfc."</b></td></tr>";
					$html.="<tr><td align=left>
                                                   INICIAL: <b>".$saldoinicial." TM</b><br>
                                                   ENVIADO: <b>".$retirada." TM</b><br>
                                                   SALDO: <b>".$saldo." TM</b><br>    
                                                </td></tr>";																			
                                        */

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
                                        $html.="<tr class='trencabezado'><td>DATOS ENVIO</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
                                                $html.="<tr>
                                                            <td width=30%>REMISION ENVIO:</td>
                                                            <td align=left>".$idenvio."</td>
                                                        </tr>";
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
                                                            <td width=30%>LICENCIA OPERADOR:</td>
                                                            <td align=left><b>".$licenciaoperador."</b></td>
                                                        </tr>"; 
                                                $html.="<tr>
                                                            <td colspan=2 align=left width=30%>
                                                                PLACAS TRACTOR:<b>".$placastractor."</b>
                                                                PLACAS REMOLQUE:<b>".$placasremolque."</b>
                                                            </td>
                                                        </tr>";
 						$html.="<tr>
                                                            <td width=30%>OBSERVACIONES ENVIO:</td>
                                                            <td align=left><b>".$observacionesEnv."</b></td>
                                                        </tr>"; 
                                               
                                        $html.="</table>";
				$html.="</td>";
				
				
//DATOS BODEGA ORIGEN
				$html.="<td width='40%' valign='top'>";
				
					
					$html.="<table class='reporte' width='100%'>";
                                        $html.="<tr class='trencabezado'><td>DATOS RECEPCION</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
 						$html.="<tr>
                                                            <td width=30%>BODEGA DESTINO REAL:</td>
                                                            <td align=left>".$cmbbodega."</td>
                                                        </tr>";                                       
                                        
						$html.="<tr align=left>
                                                            <td colspan=2>
                                                                # BANCO:<b>".$bancor."</b>
                                                                # ESTIBA:<b>".$estibar."</>
                                                            </td>
                                                        </tr>";
						$html.="<tr align=left>
                                                            <td colspan=2 align=left>
                                                                REFERENCIA:<b>".$referenciar."</b>
                                                                TICKET BASCULA:<b>".$ticketbascular."</b>
                                                            </td>       
                                                        </tr>";  

						$html.="<tr>
                                                            <td width=30%>CUADRILLA:</td>
                                                            <td align=left><b>".$cabocuadrilla."</b>
                                                        </tr>";                                                
						$html.="<tr>
                                                            <td>FOLIOS:</td>
                                                            <td align=left><b>".$foliosr."</b></td>
                                                        </tr>";	                                                

						$html.="<tr>
                                                            <td>OBSERVACIONES:</td>
                                                            <td align=left>".$observacionesr."</td>
                                                        </tr>";							
							
					$html.="</table>";
				$html.="</td>";
									
			$html.="</tr>";				
		$html.="</table>";
		$html.="</td></tr>"; //Mega tabla
		//Finaliza sección de forma de pago e impuestos
                
        //BUSCA DATOS PRODUCTO
                //Obtiene Etiqueta Descripcion Cantidad principal
                $saldosc=0;
                $saldosc=str_replace(',','',$saldo);
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
			$html.="</tr>";

			//Obteniendo los datos
                                //#Politica de Registro adicional
                                $politica="";
                                if($edita==1){
                                    $politica= " readonly onChange='recalcula(".$factor.",".$edita.")'";
                                }
                                
				$html.="<tr class=trcontenido>";
                                        $html.="<td align=center>".$marca."</td>";
					$html.="<td align=center>".$zafra."</td>";
                                        $html.="<td align=center>".$nombreproducto."</td>";
                                        $html.="<td align=center>".$nombreestado."</td>";			
				$html.="</tr>";				
			$html.="</table></center>";	
		$html.="</td></tr>"; //Mega tabla
		//Finaliza sección de conceptos
		
                $inv=" readonly style='text-align:right;color:#707070;background-color: #FFFFFF;border-width:0;font-size: 12px;'";
                
                //Inicia sección de Cantidades
		$html.="<tr><td>"; //Mega tabla
		$html.="<center><table class='reporte' width='100%'>";
			//Armando encabezado
			$html.="
                                <tr class='trencabezado'>
                                    <td colspan=2>ENVIO</td>
                                    <td colspan=2>RECEPCION</td>
                                    <td colspan=2>DIFERENCIA</td>
                                </tr>";
                                
                        $html.="
                                <tr class='trencabezado'>
                                    <td>".$unidad1."</td>
                                    <td>".$unidad2."</td>
                                    <td>".$unidad1."</td>
                                    <td>".$unidad2."</td> 
                                    <td>".$unidad1."</td>
                                    <td>".$unidad2."</td>                                       
                                </tr>";
                                
                                
				$html.="<tr class=trcontenido><font size='4'>";
                                        $html.="<td align=right>".$cantidad1."</b></td>";
					$html.="<td align=right><b>".$cantidad2."</b></td>";			
                                        $html.="<td align=right><b>".$cantidadrecibida1."</b></td>";
					$html.="<td align=right><b>".$cantidadrecibida2."</b></td>";			
                                        $html.="<td align=right><b>".$diferencia1."</b></td>";
					$html.="<td align=right><b>".$diferencia2."</b></td>";			
				$html.="</font></tr>";	
                                
			$html.="</table></center>";
                        
		$html.="</td></tr>"; //Mega tabla
		//Finaliza sección de Cantidades  
                      
    //Inicia Seccion de Devoluciones y Faltantes
                $iddevolucion=0;
                $cantdev1=0;
                $cantdev2=0;
                
                $idfaltante=0;
                $cantfalt1=0;
                $cantfalt2=0;
                //Obtiene Devoluciones
                $sqlenv="select * from logistica_devoluciones lf inner join inventarios_estados oe on lf.idestadoproducto=oe.idestadoproducto where lf.idrecepcion=$idrecepcion";
                $result = $conexion->consultar($sqlenv);
                while($rs = $conexion->siguiente($result)){
                    $iddevolucion=$rs{"id"};
                    $cantdev1=$rs{"cantidad1"};
                    $cantdev2=$rs{"cantidad2"};
                    $descripcionestado=$rs{"descripcionestado"};
                }
                $conexion->cerrar_consulta($result); 
                //Obtiene Faltantes
                $sqlenv="select * from logistica_faltantestraslados where idrecepcion=$idrecepcion";
                $result = $conexion->consultar($sqlenv);
                while($rs = $conexion->siguiente($result)){
                    $idfaltante=$rs{"idfaltante"};
                    $cantfalt1=$rs{"cantfalt1"};
                    $cantfalt2=$rs{"cantfalt2"};
                }
                $conexion->cerrar_consulta($result);                 
                
                $investatus=" readonly style='text-align:right;color:red;background-color: #FFFFFF;border-width:0;font-size: 12px;'";
		$html.="<tr><td>"; //Mega tabla
		$html.="<right><div id=devfalt><table class='reporte' width='40%' align=right>";
			//Armando encabezado

			
                                
                                
			//Obteniendo los datos
                                if($cantdev1>0){

                                $html.="
                                <tr class='trencabezado'>
                                    <td colspan=6 align=right>DIFERENCIA</td>
                                </tr>";
                                
                                    $html.="<tr class=trcontenido>";
                                            $html.="<td colspan=3><b>Folio Diferencia:$iddevolucion</b></td>";
                                            $html.="<td align=right><b>Estado Producto:$descripcionestado</b></td>";
                                            $html.="<td align=right><b>$cantdev1</b>   $unidad1</td>";
                                            $html.="<td align=right><b>".number_format($cantdev2,3)."</b>   $unidad2</td>";			
                                    $html.="</tr>";                                   
                                }

                                if($cantfalt1>0){
                                    $html.="<tr class=trcontenido>";
                                            $html.="<td colspan=4><b>Faltante Folio:$idfaltante</b></td>";
                                            $html.="<td align=right><b>$cantfalt1</b>   $unidad1</td>";
                                            $html.="<td align=right><b>".number_format($cantfalt2,3)."</b>   $unidad2</td>";			
                                    $html.="</tr>";
                                }
                                
			$html.="</table></div></right>";                
                    $html.="</td></tr>"; //Mega tabla    
//Fin Devoluciones y Faltantes
                        
                //Inicia sección de FIRMAS
		$html.="<tr><td>"; //Mega tabla
                    $html.="<table width='100%'>";	
                            $html.="<tr>"; 

                            //INFORMACION AMACENISTA
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA CAPTURISTA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<center><b>".$nombrecapturista."</></center><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                            //INFORMACION OPERADOR
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA OPERADOR</td></tr>";
                                            $html.="<tr height='60' width=20 valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<center><b>".$nombreoperador."</b></center><br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";
                            //INFORMACION VIGILANCIA
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA ALMACENISTA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<center><b>".$responsable."</b></center><br>";
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
                                                                                    var jfactor=0,jfactord=0,jedita=0,jsaldo1=0,jsaldo2=0, 
                                                                                        cant1=0, cant2=0,scant1,scant2,jsaldo=0;
                                                                                    jfactor=factor;
                                                                                    jedita=edita;
                                                                                    jfactord=jfactor;
                                                                                    jsaldo=saldo;
                                                                                    if(jfactor==0){
                                                                                        jfactord=1;
                                                                                    }
                                                                                    jsaldo1=jsaldo/jfactor;
                                                                                    jsaldo2=jsaldo;
                                                                                    scant1=valor(document.getElementById('txtcantenv1').value);
                                                                                    scant2=valor(document.getElementById('txtcantenv2').value);
                                                                                    cant1=valor(document.getElementById('txtcantrec1').value);
                                                                                    cant2=cant1*jfactor; 
                                                                                    if((cant1<=jsaldo1) && (cant2<=jsaldo2)){
                                                                                        cant1=valor(document.getElementById('txtcantrec1').value);
                                                                                        cant2=cant1*jfactor;
                                                                                        document.envio.txtcantrec2.value=format_number(cant2,2);                                                                                        
                                                                                    }else{ 
                                                                                        alert('Las cantidades Exeden el Saldo de la Instruccion');
                                                                                        document.envio.txtcantrec1.value=format_number(0,2);
                                                                                        document.envio.txtcantrec2.value=format_number(0,2);
                                                                                    }
                                                                                    document.envio.txtcantdif1.value=format_number(scant1-cant1,2);
                                                                                    document.envio.txtcantdif2.value=format_number(scant2-cant2,2);
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



                
                echo $html_funcionesjavascript;  
                

                
                
                
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
                
                
                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body>";
                $html.="</html>";
                
                
                   
                
//Depuracion
echo $opciones.$html;


?>
