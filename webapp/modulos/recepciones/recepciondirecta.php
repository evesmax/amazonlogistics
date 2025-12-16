<?php

  date_default_timezone_set("America/Mexico_City");

    $htmlpoliticas="	
        <script language='javascript'>
            $(document).ready(function(){
                //folios
                    $('#txtfolios').bind('focusout', function() {  
                        if($('#txtfolios').val()==0 || $('#txtfolios').val()==''){
                            alert('Debe escribir de 5 a 10 Folios separados por comas si son aleatorios o con un guion para señalar rangos');
                            $('#txtfolios').focus();
                        }            
                    });
                //VALIDANDO ACLARACIONES
                    $('#txtcantrec1').bind('focusout', function() {  
                        if($('#txtcantrec1').val()==0 || $('#txtcantrec2').val()==''){
                            alert('Debe escribir una cantidad mayor a cero');
                            $('#txtcantrec1').focus();
                        }
                        if($('#txtcantdif1').val()>0){
                            $('#devfalt').css('display', 'block');
                            $('#txtestatus1').val($('#txtcantdif1').val());
                            $('#txtestatus2').val($('#txtcantdif2').val());
                        }else{
                            $('#devfalt').css('display', 'none');
                        }
                        if($('#txtcantrec1').val()*1>$('#txtsaldotraslado').val()*1){
                            alert('No puede recibir una cantidad mayor al Saldo de la Orden de Traslado Cant.' + $('#txtcantrec1').val() + ' Saldo Traslado: ' + $('#txtsaldotraslado').val());
                            $('#txtcantrec1').val($('#txtsaldotraslado').val());
                            $('#txtcantrec2').val(0);
                            $('#txtcantrec1').focus();
                        }                                                
                    });    
                    $('#txtcantdev1').bind('focusout', function() {
                        $('#txtestatus1').val($('#txtcantdif1').val()*1-($('#txtcantdev1').val()*1+$('#txtcantfalt1').val()*1));
                        $('#txtestatus2').val($('#txtcantdif2').val()*1-($('#txtcantdev2').val()*1+$('#txtcantfalt2').val()*1));
                        if($('#txtestatus1').val()==0 && $('#txtestatus2').val()==0){
                            $('#txtestatus1').css('color', 'blue');
                            $('#txtestatus2').css('color', 'blue');
                            $('#divestatus').css('display', 'block');
                        }else{
                            $('#txtestatus1').css('color', 'red');
                            $('#txtestatus2').css('color', 'red');
                            $('#divestatus').css('display', 'none');
                        }
                    });
                    $('#txtcantfalt1').bind('focusout', function() {
                        $('#txtestatus1').val($('#txtcantdif1').val()*1-($('#txtcantdev1').val()*1+$('#txtcantfalt1').val()*1));
                        $('#txtestatus2').val($('#txtcantdif2').val()*1-($('#txtcantdev2').val()*1+$('#txtcantfalt2').val()*1));
                        if($('#txtestatus1').val()==0 && $('#txtestatus2').val()==0){
                            $('#txtestatus1').css('color', 'blue');
                            $('#txtestatus2').css('color', 'blue');
                            $('#divestatus').css('display', 'block');
                        }else{
                            $('#txtestatus1').css('color', 'red');
                            $('#txtestatus2').css('color', 'red');
                            $('#divestatus').css('display', 'none');
                        }
                    });
                    $('#btngrabar').click(function() {
                        // Validar que todos los campos de texto tengan valor
                        var camposValidos = true;
                        if($('#txtcantrec1').val()*1==0 || $('#txtcantrec1').val()*1==0 ){
                            camposValidos= false
                            alert('Debe escribir una cantidad mayor a cero');
                            $('#txtcantrec1').val()=0;
                            $('#txtcantrec1').focus(); 
                        }
                            
                        // Si todos los campos son válidos, permite el envío del formulario
                        if (camposValidos) {
                        return true;
                        } else {
                        return false; // Detiene el envío del formulario
                        }
                    });
                    
            });
        </script>";	

	include("../../netwarelog/catalog/conexionbd.php");
	$usuario=$_SESSION["accelog_idempleado"];
    //RECUPERANDO VARIABLES
         $idtraslado=$_GET["idtraslado"];
         $idenvio=0;
        
    //OBTENIENDO INFORMACION BASICA DE TRASLADOS
                    $otfc="";
                    $fecha=date("d-m-Y H:i:s");
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
					$capturista=$usuario;
					$responsable="";
					$licenciaoperador="";

		$sqlestatus="Select lt.idtraslado,lt.referencia1 otfc, lt.fecha,  
                                of.nombrefabricante 'nombreingenio', obo.nombrebodega 'bodegaorigen',
                                obd.nombrebodega 'bodegadestino', il.descripcionlote 'zafra', 
                                ip.nombreproducto 'producto', ie.descripcionestado 'estado', 
                                lt.cantidad1 'saldoinicial', format(IFNULL(lt.cantidadretirada1,0),3) 'retirada',
                                (Select sum(cantidadrecibida1) total from logistica_recepciones where idtraslado=lt.idtraslado and idestadodocumento=1) 'recibida', 
                                format(lt.cantidad1-IFNULL(lt.cantidadretirada1,0),3) 'saldo', 
                                    case when obd.idbodega in (select idbodega from relaciones_almacenadoras_bodegas t 
                                        inner join relaciones_almacenadoras_bodegas_detalle d on t.idalmacenadorabodega=d.idalmacenadorabodega 
                                        where idbodega=lt.idbodegadestino) then 'a' else 'i' end 'logo', ot.razonsocial transportista,
                                of.idfabricante, obo.idbodega, obd.idbodega idbodegadestino, ot.idtransportista, lt.idproducto,
                                    now() fechaenvio,ot.idtransportista,'' cartaporte,'' nombreoperador,'' placastractor,
                                    '' placasremolque,'' horallegada,'' ticketbascula,'' banco, '' estiba, 
                                    format(0,2) cantidad1, format(0,2) cantidad2, 
									'' consecutivobodega, '' folios, '' observaciones,
									obd.responsable, '' licenciaoperador, vm.nombremarca
                             From logistica_traslados lt 
                                left join operaciones_fabricantes of on of.idfabricante=lt.idfabricante
                                left join vista_marcas vm on lt.idmarca=vm.idmarca
                                left join operaciones_bodegas obo on obo.idbodega=lt.idbodegaorigen
                                left join operaciones_bodegas obd on obd.idbodega=lt.idbodegadestino
                                left join inventarios_productos ip on ip.idproducto=lt.idproducto
                                left join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto
                                left join inventarios_lotes il on il.idloteproducto=lt.idloteproducto
                                left join operaciones_transportistas ot on ot.idtransportista=lt.idtransportista 
                             Where lt.idtraslado=".$idtraslado;
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
                                    $saldo=$saldoinicial-$recibida;
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
                                    $folios=$rs{"folios"};
                                    $observaciones=$rs{"observaciones"};
                                    $responsable=$rs{"responsable"};
									$licenciaoperador=$rs{"licenciaoperador"};
                                    $marca=$rs{"nombremarca"};
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
                
                
	
         //Genera Combo Transportista
            $cmbtransportista="";
            $cmbtransportista = "<select id='cmbtransportista' name='cmbtransportista'>";
            $sqltrans="Select idtransportista, razonsocial from operaciones_transportistas 
                        order by razonsocial ";
            $result = $conexion->consultar($sqltrans);

            // Almacena las opciones del combo en un array para usarlas en la búsqueda
            $opcionestransportista = array();

            while ($rs = $conexion->siguiente($result)) {
                $sel = ($rs{"idtransportista"} == $idtransportista) ? " SELECTED " : "";
                $cmbtransportista .= "<option value='" . $rs{"idtransportista"} . "' " . $sel . ">" . $rs{"razonsocial"} . "</option>";
                // Guarda la opción en el array
                $opcionestransportista[] = array("id" => $rs{"idtransportista"}, "nombre" => $rs{"razonsocial"});
            }

            $conexion->cerrar_consulta($result);
            $cmbtransportista.= "</select>";                    


         //Genera  empleado
            $nombrecapturista="";
            $txtcapturista="<input type=hidden id='txtcapturista' name='txtcapturista' value='".$capturista."'>";
                    $sqltrans="Select concat(nombre,' ',apellido1,' ',apellido2) capturista from empleados where idempleado=".$capturista;

                    $result = $conexion->consultar($sqltrans);
                    while($rs = $conexion->siguiente($result)){
                            $nombrecapturista=$rs{"capturista"};
                    }
                    $conexion->cerrar_consulta($result);
         
        //Genera Combo Bodegas
        //Verifica Politica para Seleccionar a otras bodegas
            $sel="";
            $cmbbodega = "<select id='cmbbodega' name='cmbbodega'>";
            $sqlbod = "Select b.idbodega, b.nombrebodega from operaciones_bodegas b
                        Where b.idbodega=$idbodegadestino or 
                            b.idbodega in (select idbodegadestino from logistica_desviosautorizados where idbodega=$idbodegadestino and activo=-1)
                        order by b.nombrebodega";
            $result = $conexion->consultar($sqlbod);

            // Almacena las opciones del combo en un array para usarlas en la búsqueda
            $opcionesBodegas = array();

            while ($rs = $conexion->siguiente($result)) {
                $sel = ($rs{"idbodega"} == $idbodegadestino) ? " SELECTED " : "";
                $cmbbodega .= "<option value='" . $rs{"idbodega"} . "' " . $sel . ">" . $rs{"nombrebodega"} . "</option>";
                // Guarda la opción en el array
                $opcionesBodegas[] = array("id" => $rs{"idbodega"}, "nombre" => $rs{"nombrebodega"});
            }

            $conexion->cerrar_consulta($result);
            $cmbbodega .= "</select>";

                            
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
        $html.="<input type='hidden' value=".$saldo." id='txtsaldotraslado' name='txtsaldotraslado'>";    
        $html.=$htmlpoliticas;	

        $html.=" <FORM id='envio' name='envio' method='post' action='recepciondirecta_grabar.php' onsubmit='deshabilitarBoton()'>
                    <input type=hidden id='txtidtraslado' name='txtidtraslado' value='".$idtraslado."'>
                    <input type=hidden id='txtidenvio' name='txtidenvio' value='".$idenvio."'>";
		$html.=$txtcapturista;

        $html.= "<BODY style='font-family:helvetica'>
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

                    $nombreorganizacionAMZ= "ALMACENADORA MERCANTIL AMAZON";
                    $domicilioAMZ="ANAXAGORAS 1329, LETRAN VALLE, DEL. BENITO JUAREZ, CDMX."; 
                    $cpAMZ="03650";
                    $municipioAMZ="MÉXICO";
                    $estadoAMZ="CIUDAD DE MÉXICO";
                    $telefonosAMZ= "5575742291";
					//Logotipo
					$html.="<td width='15%'>".$imgtitulo."</td>";
				
					//Datos Organización
					$html.="<td width='45%' align=left style='font-family:helvetica;font-size:9pt;'>";				
						$html.="<b>".$nombreorganizacionAMZ."</b><br>";
						
						$html.=" <strong>DOMICILIO:</strong> ".$domicilioAMZ;
						$html.="<br> <strong>C.P.</strong> ".$cpAMZ;
						$html.="<br> ".$municipioiAMZ;
						$html.=" ".$estadoAMZ;
                                                if($telefonosAMZ<>''){
                                                    $html.="<br><strong>TELEFONO:</strong> ".$telefonosAMZ;
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
					$html.="	<b>Folio:</b> <font color=blue>Nuevo</font> 
                                                    -   <b>Folio Interno:</b> <font  color=blue> Nuevo";
					$html.="	</font>";
					$html.="</td>";
					$html.="</tr>";
                                        
                                        
                    //# POLITICA CONSULTA SI PUEDE EDITAR LA FECHA DE EMISION
                                            $st="";
                                            $sqlbodega="select * from logistica_politicas where idpolitica=5";
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
					$html.="<td align=center style='font-size:7pt'>";
					$fecha = new DateTime($fecha);
					$fechainfo = $fecha->format('Y-m-d')." ".$fecha->format('H:i:s');									
					$html.="<input type=text ".$st." id='txtfecharec' name='txtfecharec' value='".$fechainfo."'>";
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
                                            $html.="<tr class='trencabezado'><td>PROPIETARIO / BODEGA ORIGEN</td></tr>";
                                                            $html.="<td width='45%' align=left style='font-family:helvetica;font-size:9pt;'>";				
                                                            $html.="<b>Propietario: ".$nombreorganizacion."<br> Marca: ".$marca."</b><br>";
                                                            
                                                            //$html.=" <strong>DOMICILIO:</strong> ".$domicilio;
                                                            //$html.="<br> <strong>C.P.</strong> ".$cp;
                                                            //$html.="<br> ".$municipio;
                                                            //$html.=" ".$estado;
                                                            //                        if($telefonos<>''){
                                                            //                            $html.="<br><strong>TELEFONO:</strong> ".$telefonos;
                                                            //                        }
                                                            //$html.="<br>";					
                                                        
                                             $html.="<b> Bodega Origen: ".$bodegaorigen."</b><br>".$domicilio."<br>";
 
                                            $html.="</td>";

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
                                                            <td align=left>
                                                                <input type=text id='txtcartaporte' name='txtcartaporte' value='' Size=20>
                                                            </td>
                                                        </tr>";	
						$html.="<tr>
                                                            <td width=30%>NOMBRE OPERADOR:</td>
                                                            <td align=left>
                                                                <input type=text id='txtnombreoperador' name='txtnombreoperador' value='' Size=50>
                                                            </td>
                                                            </tr>";
 						$html.="<tr>
                                                            <td width=30%>LICENCIA OPERADOR:</td>
                                                            <td align=left>
                                                                <input type=text id='txtlicencia' name='txtlicencia' value='' Size=20>
                                                            </td>                                                        
                                                            </tr>";                                               
                                                $html.="<tr>
                                                            <td colspan=2 align=left width=30%>
                                                                PLACAS TRACTOR:<b><input type=text id='txtplacastractor' name='txtplacastractor' value='' Size=20></b>
                                                                PLACAS REMOLQUE:<b><input type=text id='txtplacasremolque' name='txtplacasremolque' value='' Size=20></b>
                                                            </td>
                                                        </tr>";
	                                                   
						$html.="<tr>
                                                            <td width=30%>LLEGADA APROXIMADA:</td>
                                                            <td align=left><b><input type=text id='txthorallegada' name='txthorallegada' value='' Size=20></b>
                                                        </tr>";
						$html.="<tr>
                                                            <td width=30%>SELLOS:</td>
                                                            <td align=left><b><input type=text id='txtobsenvio' name='txtobsenvio' value='' Size=20></b>
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
                                                                # BANCO:<input type=text id='txtbanco' name='txtbanco' value='' Size=20>
                                                                # ESTIBA:<input type=text id='txtestiba' name='txtestiba' value='' Size=20>
                                                            </td>
                                                        </tr>";
						$html.="<tr align=left>
                                                            <td colspan=2 align=left>
                                                                FOLIO EXTERNO:<input type=text id='txtreferencia' name='txtreferencia' value='' Size=20>
                                                                TICKET BASiCULA:<input type=text id='txtticketbascula' name='txtticketbascula' value='' Size=20>
                                                            </td>
                                                        </tr>";

						$html.="<tr>
                                                            <td width=30%>CUADRILLA:</td>
                                                            <td align=lefti><input type=text id='txtcabocuadrilla' name='txtcabocuadrilla' value='' Size=60></td>
                                                        </tr>";                                   
						$html.="<tr>
                                                            <td>FOLIOS:</tid>
                                                            <td align=lefti><textarea type=text id='txtfolios' name='txtfolios' rows=2 cols=45  title='Escriba los folios de los bultos Ejemplo: 89999,898928,990920 '></textarea></td>
                                                        </tr>";                                     

						$html.="<tr>
                                                            <td>OBSERVACIOiNES:</td>
                                                            <td align=lefti><textarea type=text id='txtobservaciones' name='txtobservaciones' rows=3 cols=45  title='Escriba los folios de los bultos Ejemplo: 89999,898928,990920 '></textarea></td>
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
                $saldosc=str_replace(',','',$cantidad2+0.25);
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
                                
			//Obteniendo los datos
                                //#Politica de Registro adicional
                                $politica="";
                                if($edita==1){
                                    $politica= " readonly onChange='recalcula(".$factor.",".$edita.")'";
                                }
                                
				$html.="<tr class=trcontenido>";
                                        $html.="<td align=right><input type=text value=".$cantidad1." id='txtcantenv1' name='txtcantenv1' size=20 onChange='recalcula(".$factor.",".$edita.",".$saldosc.")'></td>";
					$html.="<td align=right><input type=text value=".$cantidad2." id='txtcantenv2' name='txtcantenv2' size=20  ".$politica."></td>";			
                                        $html.="<td align=right><input type=text value=".$cantidad1." id='txtcantrec1' name='txtcantrec1' size=20 onChange='recalcula(".$factor.",".$edita.",".$saldosc.")'></td>";
					$html.="<td align=right><input type=text value=".$cantidad2." id='txtcantrec2' name='txtcantrec2' size=20 ".$politica."></td>";			
					$html.="<td align=right><input ".$inv." type=text value=0.00 id='txtcantdif1' name='txtcantdif1' size=20></td>";
					$html.="<td align=right><input ".$inv." type=text value=0.00 id='txtcantdif2' name='txtcantdif2' size=20></td>";			
				$html.="</tr>";	
                                
			$html.="</table></center>";
                        
		$html.="</td></tr>"; //Mega tabla
                //Finaliza sección de Cantidades        
                
    //Inicia Seccion de Devoluciones y Faltantes
                
                 //Verifica Politica para Seleccionar a otras bodegas
            $sel="";
            $cmbestados = "<select id='cmbestados' name='cmbestados'>";
            $sqlest = "Select idestadoproducto,descripcionestado from inventarios_estados where espnc=-1";
            $result = $conexion->consultar($sqlest);

            while ($rs = $conexion->siguiente($result)) {
                $cmbestados.= "<option value='" . $rs{"idestadoproducto"} . "'>" . $rs{"descripcionestado"} . "</option>";
            }
            $conexion->cerrar_consulta($result);
            $cmbestados.= "</select>";


                $investatus=" readonly style='text-align:right;color:red;background-color: #FFFFFF;border-width:0;font-size: 12px;'";
		$html.="<tr><td>"; //Mega tabla
		$html.="<right><div id=devfalt style='display:none;'><table class='reporte' width='40%' align=right>";
			//Armando encabezado
			$html.="
                                <tr class='trencabezado'>
                                    <td colspan=4><b>Producto que no Llego:</b></td>
                                    <td align=left><input ".$investatus." type=text value=0.00 id='txtestatus1' name='txtestatus1' size=20></td>
                                    <td align=left><input ".$investatus." type=text value=0.00 id='txtestatus2' name='txtestatus2' size=20></td>
                                </tr>";
                                
                                
			//Obteniendo los datos
                                //#Politica de Registro adicional
                                $politica="";
                                if($edita==1){
                                    $politica= " readonly onChange='recalcula(".$factor.",".$edita.")'";
                                }
                                
				$html.="<tr class=trcontenido>";
                                        $html.="<td colspan=4><b>Producto recibido como:<br>".$cmbestados."</b></td>";
                                        $html.="<td align=right><input type=text value=0 id='txtcantdev1' name='txtcantdev1' size=20 onChange='recalcula(".$factor.",".$edita.",".$saldosc.")'></td>";
					$html.="<td align=right><input type=text value=0 id='txtcantdev2' name='txtcantdev2' size=20 ".$politica."></td>";			
				$html.="</tr>";
                                
                                $html.="<tr class=trcontenido hidden>";
                                        $html.="<td colspan=4><b>Faltante</b></td>";
                                        $html.="<td align=right><input type=text value=0 id='txtcantfalt1' name='txtcantfalt1' size=20 onChange='recalcula(".$factor.",".$edita.",".$saldosc.")'></td>";
					$html.="<td align=right><input type=text value=0 id='txtcantfalt2' name='txtcantfalt2' size=20 ".$politica."></td>";			
				$html.="</tr>";
                                
                                
			$html.="</table></div></right>";
                        
		$html.="</td></tr>"; //Mega tabla                
    //Finaliza Devoluciones y Faltantes
                
                //Inicia sección de FIRMAS
		$html.="<tr><td>"; //Mega tabla
                    $html.="<br><table width='100%'>";	
                            $html.="<tr>"; 

                            //INFORMACION AMACENISTA
                            $html.="<td width='20%'>";
                                    $html.="<table class='reporte' width='100%'>";
                                            $html.="<tr class='trencabezado'><td>FIRMA CAPTURISTA</td></tr>";
                                            $html.="<tr height='60' valign='top'>";
                                                    $html.="<td>";
                                                            $html.="<center>".$nombrecapturista."</center><br>";
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
                                                            $html.="<center><b>".$responsable."</b></center><br><input id=txtalmacenista name=txtalmacenista type=hidden value='$responsable'>";
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
                                                                                    scant2=scant1*jfactor;
                                                                                    document.envio.txtcantenv2.value=format_number(scant2,3); 
                                                                                    cant1=valor(document.getElementById('txtcantrec1').value);
                                                                                    cant2=cant1*jfactor;
                                                                                    document.envio.txtcantrec2.value=format_number(cant2,3);                                                                                        
                                                                                    document.envio.txtcantdif1.value=format_number(scant1-cant1,2);
                                                                                    document.envio.txtcantdif2.value=format_number(scant2-cant2,3); 
                                                                                    cant1=0;
                                                                                    cant2=0;
                                                                                    cant1=valor(document.getElementById('txtcantdev1').value);
                                                                                    cant2=cant1*jfactor;
                                                                                    document.envio.txtcantdev2.value=format_number(cant2,3); 
                                                                                    cant1=0;
                                                                                    cant2=0;
                                                                                    cant1=valor(document.getElementById('txtcantfalt1').value);
                                                                                    cant2=cant1*jfactor;
                                                                                    document.envio.txtcantfalt2.value=format_number(cant2,3);                                                                                    
                                                                                }
                                                                                function pdf(idenvio){
                                                                                        var ref=0;
                                                                                        ref=idenvio;
                                                                                        document.location = 'pdf.php?idenvio='+ref;
                                                                                }
                                                                                function deshabilitarBoton() {
                                                                                    document.getElementById('btngrabar').disabled = true;
                                                                                    // Puedes agregar un mensaje al usuario, por ejemplo:
                                                                                    document.getElementById('btngrabar').value = 'Procesando...'; 
                                                                                }
									</script>";
		//Botones							
		$html_botones="	<INPUT name='btngrabar' id='btngrabar' class='buttons_text' type='submit' value='Procesar' title='Haz Click Para Autorizar'>
                                <INPUT name=btnregresar type='button' onclick='redireccion()' value='Regresar'>";

                                
  

     
                
                echo $html_funcionesjavascript;  
                
                $html.="<tr><td>"; //Mega tabla
                    $html.= "<center><table><tr><td>".$html_botones."</td></tr></table></center>";
                $html.="</tr></td>"; //Mega tabla

                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></form>";
                $html.="</html>";
                                           
//Depuracion
echo $html;


?>