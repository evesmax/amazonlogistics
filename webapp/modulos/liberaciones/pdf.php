<?php	
	include("../../netwarelog/catalog/conexionbd.php");

	
//RECUPERANDO VARIABLES

        $texto="";
        $idordencompra=0;
        $idordencompra=$_GET["idordencompra"];

                $fechamysql=date("Y-m-d H:i:s");

                $sqltextocarta="Select * From logistica_textocartas tc 
                    where '$fechamysql' between tc.fechainicial and tc.fechafinal and tc.idtextocarta=1";
                
                
                $result = $conexion->consultar($sqltextocarta);
		while($rs = $conexion->siguiente($result)){
                    $texto=$rs{"textocarta"};
                }
                $conexion->cerrar_consulta($result);
                
            //Variables    

                
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
                    $gerenteadministrativo="";

                    
                    
		$sqlestatus="select  oc.idbodega, oc.idcliente, oc.idfabricante, oc.ordendecompra, cli.razonsocial cliente,cli.representantelegal,cli.cie 'ciecliente',
                                    tic.tipocliente, tim.tipomercado, tiv.tipodeventa, oc.fecha, of.nombrefabricante fideicomiso
                                    ,of.representantelegal representantelegalfideicomiso, of.clavecie 'claveciefideicomiso', 
                                    oc.clavecontrato,vm.nombremarca marca, ip.nombreproducto, concat('En Norma: ',cn.norma) 'calidad', 
                                    il.descripcionlote 'zafra', um.descripcionunidad 'presentacion', ob.nombrebodega, format(oc.volumenorden,2) 'volumenorden',
                                    format(oc.precioventa,2) 'precio', format(oc.importe,2) importe, oc.fechalimitepago,
                                    (select oe from consecutivos_oe where idordencompra=oc.idordencompra order by idoe desc limit 1) 'oe',
                                    (select ie from consecutivos_ie where idordencompra=oc.idordencompra order by idie desc  limit 1) 'ie', of.contacto 'gerenteadministrativo'
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
                //echo $sqlestatus;
                
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
                    $oe=$rs{"oe"};
                    $ie=$rs{"ie"};
                    $gerenteadministrativo=$rs{"gerenteadministrativo"};
                    
		}
                $conexion->cerrar_consulta($result);                        
                
                $zafra=str_replace("*", "",$zafra);
                        
//SUSTITUYENDO VALORES DEL TEXTO
                $dias=30;
                $costoalmacenaje="$ 2.00";
                $tipocliente=$tipoventa;
                $responsablecliente1=$representantelegal;
                
        if($gerenteadministrativo<>""){
            $gerenteadministrativo.=" - Gerente Administrativo";
        }else{
            $gerenteadministrativo="";
        }
        
        $texto=str_replace("@fecha", "México, D.F., a $fechahoy",$texto);           
        $texto=str_replace("@oe", $oe,$texto);   
        $texto=str_replace("@ie", $ie,$texto);   
                
        $texto=str_replace("@responsableingenio", $representantelegalfideicomiso,$texto);        
        $texto=str_replace("@nombreingenio", $fideicomiso,$texto);        

        $texto=str_replace("@toneladas", $volumenorden,$texto);        
        $texto=str_replace("@producto", $nombreproducto." ".$calidad.", ".$presentacion,$texto);
        $texto=str_replace("@zafra", $zafra,$texto);
        $texto=str_replace("@nombrecliente", $cliente,$texto);
        $texto=str_replace("@contrato", $clavecontrato,$texto);
        $texto=str_replace("@bodega", $nombrebodega,$texto);
        $texto=str_replace("@dias", $dias,$texto);
        $texto=str_replace("@costoalmacenaje", $costoalmacenaje,$texto);
        $texto=str_replace("@tipocliente", $tipocliente,$texto);
        //$texto=str_replace("@responsableventas1", $responsableventas1, $texto);
 	$texto=str_replace("@gerenteadministrativo", $gerenteadministrativo,$texto);
        $texto=str_replace("@responsablecliente1", $responsablecliente1,$texto);
        
	$html="<html>";
	$html.= "<head>";
	//Utiliza por omisión el estilo 1 del repolog
	$html.= "<LINK href='pdf/pdf_factura_css/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";
	$html.= "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>";
	$html.= "<meta name='author-icons' content='Rachel Fu'>";
	$html.= "<style>";
	$html.= "  body{font-size:0pt;color:black}";	
	$html.= "  td{font-size:10pt}";
	$html.= "</style>";
	$html.= "</head>";
        //$html.=" <FORM id='envio' name='envio' method='post' action='envio_grabar.php'>";
	
	$html.= "<body style='font-family:helvetica'>";


        $html.= "<BODY style='font-family:helvetica'>
      ";
        
        $html.= "<LINK href='pdf/pdf_factura_css/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";                                  

        
		//INICIA MEGATABLA
		$html.="<table width='100%'>";
                
		//ENCABEZADO
		$html.="<tr><td>"; //Mega tabla
                    $html.=$texto;
                $html.="</td></tr>"; //Mega tabla
                
		//FIN MEGATABLA
		$html.="</table>";
                $html.="</body></div></form>";
                $html.="</html>";
               

               
	
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
	$dompdf->stream("OE_".$oe.".pdf");
	
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