<?php
	
	
	include("../../netwarelog/catalog/conexionbd.php");
	
    //RECUPERANDO VARIABLES
         $identradasproduccion=$_GET["folio"];
        
    //OBTENIENDO INFORMACION BASICA DE TRASLADOS
         
                $fecha="";
                $ingenio="";
                $bodega="";
                $producto="";
                $zafra="";
                $estado="";
                $cantidad1="";
                $cantidad2="";
                $folios="";
                $observaciones="";
                $turno="";
                $diazafra="";
                $capturista="";
                $responsable="";
                $idproducto="";
                $estadoproducto="";
				$consecutivobodega=0;
		$sqlestatus="Select pe.identradasproduccion,
                                im.fecha, 
                                of.nombrefabricante ingenio,
                                ob.nombrebodega bodega,
                                ip.nombreproducto producto, 
                                il.descripcionlote zafra,
                                ie.descripcionestado estado, 
                                im.cantidad 'cantidad1',
                                format((im.cantidad*tm.efectoinventario)*um.factor,2) cantidad2,
                                pe.folios, pe.observaciones,pt.nombreturno 'turno',pe.diazafra,
                                concat(em.nombre,' ',em.apellido1,' ',em.apellido2) 'capturista', 
                                ob.responsable, im.idfabricante, im.idbodega, im.idproducto, pe.consecutivobodega
                            From inventarios_movimientos im 
                                left join produccion_entradas pe on pe.identradasproduccion=im.foliodoctoorigen
                                left join operaciones_fabricantes of on of.idfabricante=im.idfabricante
                                left join operaciones_bodegas ob on ob.idbodega=im.idbodega
                                left join inventarios_productos ip on ip.idproducto=im.idproducto
                                left join inventarios_lotes il on il.idloteproducto=im.idloteproducto
                                left join inventarios_estados ie on ie.idestadoproducto=im.idestadoproducto
                                left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento
                                left join inventarios_unidadesproductos up on up.idproducto=im.idproducto
                                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                left join empleados em on em.idempleado=pe.idempleado
                                left join produccion_turnos pt on pt.idturno=pe.idturno
                            Where im.doctoorigen=2 and pe.identradasproduccion=".$identradasproduccion;
                
                //echo $sqlestatus;
		$result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                        //Asignando Valores del Traslado
                        $fecha=$rs{"fecha"};
                        $ingenio=$rs{"ingenio"};
                        $bodega=$rs{"bodega"};
                    $producto=$rs{"producto"};
                    $zafra=$rs{"zafra"};
                    $estadoproducto=$rs{"estado"};
                    $cantidad1=$rs{"cantidad1"};
                    $cantidad2=$rs{"cantidad2"};
                        $folios=$rs{"folios"};
                        $turno=$rs{"turno"};
                        $diazafra=$rs{"diazafra"};
                    $capturista=$rs{"capturista"};
                    $responsable=$rs{"responsable"};
                        $observaciones=$rs{"observaciones"};
                        $idfabricante=$rs{"idfabricante"};
                        $idbodegaorigen=$rs{"idbodega"};
						$idproducto=$rs{"idproducto"};
						$consecutivobodega=$rs{"consecutivobodega"};
		}
		$conexion->cerrar_consulta($result);                        
                        
                
                $sqlimagen="";
                $carpeta="";
                $imgtitulo="";
				$tipoimagen="i";
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
				
                                $rn=$identradasproduccion;
				//Datos de Facturación
				$html.="<td width='30%' align=right>";
						
					$html.="<table class='reporte' width='100%'>";
					
					//Serie y Folio
					$html.="<tr class='trencabezado'><td><b>FOLIO PRODUCCION</b></td></tr>";
					$html.="<tr>";
					$html.="<td align=center>";
					$html.="	<b>Folio:</b> <font color=red>".$identradasproduccion."</font>";   
					$html.="	</font>";
					$html.="</td>";
					$html.="</tr><tr align='center'><td align='center'>Folio Origen:<b> $consecutivobodega</b></td></tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                        $st=$fecha;
                                            
                                        
                                        
                                        
                                        
					
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
                                                            $html.="<b>".$bodega."</b><br>".$domiciliobodegaorigen."<br>";
                                                    $html.="</td>";																															
                                            $html.="</tr>";
                                    $html.="</table>";																
                            $html.="</td>";								
			

                        
			//INFORMACION DEL CERTIFICADO DE LA EMPRESA
			$html.="<td width='20%'>";
				$html.="<table class='reporte' width='100%'>";
										
					//Obteniendo los datos de aprobación de la remesa de folios...
					$html.="<tr class='trencabezado'><td>GENERALES: <b></b></td></tr>";
					$html.="<tr><td align=left>
                                                   DIA ZAFRA: <b>".$diazafra." </b><br>
                                                   TURNO: <b>".$turno." </b><br>
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
                                        $html.="<tr class='trencabezado'><td>DATOS PRODUCCION</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
						$html.="<tr>
                                                            <td width=30%>INGENIO:</td>
                                                            <td align=left>".$ingenio."</td>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>FOLIO:</td>
                                                            <td align=left><b>".$folios."</b></td>
                                                        </tr>";	
						$html.="<tr>
                                                            <td width=30%>OBSERVACIONES:</td>
                                                            <td align=left><b>".$observaciones."</b></td>
                                                        </tr>";
                                                
                                               
                                        $html.="</table>";
				$html.="</td>";
				
				
//DATOS BODEGA ORIGEN
				$html.="<td width='40%' valign='top'>";
				
					
					$html.="<table class='reporte' width='100%'>";
                                        $html.="<tr class='trencabezado'><td>RESPONSABLES</td></tr>";
					$html.="</table>";
					$html.="<table class='reporte' width='100%'>";
						$html.="<tr>
                                                            <td>CAPTURISTA:</td>
                                                            <td align=left><b>".$capturista."</b></td>
                                                        </tr>";                                                
						$html.="<tr>
                                                            <td>ALMACENISTA:</td>
                                                            <td align=left><b>".$responsable."</b></td>
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
                                        $html.="<td align=center>".$ingenio."</td>";
					$html.="<td align=center>".$zafra."</td>";
                                        $html.="<td align=center>".$producto."</td>";
                                        $html.="<td align=center>".$estadoproducto."</td>";
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
                                                            $html.="<b><center>".$capturista."</center></b><br>";
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
                                                                                        document.location = 'pdf.php?idenvio='+ref;
                                                                                }
									</script>";
		//Botones

                $opciones="
                <left>    
                <div id=opciones>
                    <table  width=100>
                        <td width=16 align=right>
                            <a href='javascript:printSelec();'><img src='../../netwarelog/repolog/img/impresora.png' border='0'></a>
                        </td>
                        <td width=16 align=right>
                                <a href='javascript:pdf(".$identradasproduccion.");'> <img src='../../netwarelog/repolog/img/pdf.gif'  
                                   title ='Generar reporte en PDF' border='0'> 
                                </a>
                        </td>
                        <td width=16  align=right>
                                <a href='../../netwarelog/repolog/reporte.php'> <img src='../../netwarelog/repolog/img/filtros.png' 
                                        title ='Haga clic aqui para cambiar filtros...' border='0'> </a>
                        </td>
                    </table>
                </div><left>";

                
		$html_botones="<INPUT name=btnregresar type='button' onclick='redireccion()' value='Regresar'>";


                
                
                echo $html_funcionesjavascript;  
                
                //$html.="<tr><td>"; //Mega tabla
                //$html.= "<center><table><tr><td>".$html_print_fin."</td></tr></table></center>";
                //$html.="</tr></td>"; //Mega tabla
                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></div></form>";
                $html.="</html>";
                
                   
                

echo $opciones.$html;


?>