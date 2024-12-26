<?php	
include("../../netwarelog/catalog/conexionbd.php");

//RECUPERANDO VARIABLES

        $texto="";
        $idcancelacion=$_GET["idcancelacion"];
        $fechamysql=date("Y-m-d H:i:s");

                $sqltextocarta="Select * From logistica_textocartas tc 
                    where '$fechamysql' between tc.fechainicial and tc.fechafinal and tc.idtextocarta=2";
                
                
                $result = $conexion->consultar($sqltextocarta);
		while($rs = $conexion->siguiente($result)){
                    $texto=$rs{"textocarta"};
                }
                $conexion->cerrar_consulta($result);
                
        //Variables    

                
    //OBTENIENDO INFORMACION BASICA
                    $fecha="";
                    $fechacancelacion="";
                    $oec="";
                    $oe="";
                    $ie="";
                    $responsableingenio="";
                    $nombreingenio="";
                    $toneladas=0;
                    $nombrecliente="";
                    $toneladascanceladas=0;
                    $gerenteadministrativo="";

					
		$sqlestatus="Select 
                                loe.fecha, c.fechacancelacion,c.oecancelacion oec, loe.referencia1 oe, loe.referencia2 ie, 
                                of.representantelegal responsableingenio, of.nombrefabricante nombreingenio,cli.razonsocial nombrecliente, 
                                loe.cantidad2 toneladas, c.cantidad2 toneladascanceladas, of.contacto gerenteadministrativo, 
                                cli.representantelegal 
                            from logistica_cancelacionordenesentrega c 
                                    left join logistica_ordenesentrega loe on loe.idordenentrega=c.idordenentrega
                                    left join ventas_ordenesdecompra oc on oc.ordendecompra=loe.idpedido
                                    left join ventas_clientes cli on cli.idcliente=loe.idcliente
                                    left join operaciones_fabricantes of on of.idfabricante=loe.idfabricante
                                    left join vista_marcas vm on vm.idmarca=loe.idmarca
                                    left join inventarios_productos ip on ip.idproducto=loe.idproducto
                                    left join inventarios_lotes il on il.idloteproducto=loe.idloteproducto
                                    left join operaciones_bodegas ob on ob.idbodega =loe.idbodega
                                    left join inventarios_estados ie on ie.idestadoproducto=loe.idestadoproducto
                                    left join inventarios_unidadesmedida um on um.idunidadmedida=ip.idunidadmedida 
                            where 
                                c.idcancelacion=".$idcancelacion;
                //echo $sqlestatus;

                $result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){

                    $fecha=$rs{"fecha"};
                    $fechacancelacion=$rs{"fechacancelacion"};
                    $oec=$rs{"oec"};
                    $oe=$rs{"oe"};
                    $ie=$rs{"ie"};
                    $responsableingenio=$rs{"responsableingenio"};
                    $nombreingenio=$rs{"nombreingenio"};
                    $toneladas=$rs{"toneladas"};
                    $nombrecliente=$rs{"nombrecliente"};
                    $toneladascanceladas=$rs{"toneladascanceladas"};
                    $gerenteadministrativo=$rs{"gerenteadministrativo"};
		}
                $conexion->cerrar_consulta($result);                        
                
                
                if($gerenteadministrativo<>""){
                    $gerenteadministrativo.=" - Gerente Administrativo";
                }else{
                    $gerenteadministrativo="";
                }
		

        $texto=str_replace("@fecha", "México, D.F., a $fechacancelacion",$texto);           
        $texto=str_replace("@oe", $oe,$texto);   
        $texto=str_replace("@ie", $ie,$texto);   
        $texto=str_replace("@oec", $oec,$texto);
        $texto=str_replace("@responsableingenio", $responsableingenio,$texto);        
        $texto=str_replace("@nombreingenio", $nombreingenio,$texto);        
        $texto=str_replace("@toneladas", $toneladas,$texto);  
        $texto=str_replace("@toneladascanceladas", $toneladascanceladas,$texto); 
        $texto=str_replace("@nombrecliente", $nombrecliente,$texto);
 	$texto=str_replace("@gerenteadministrativo", $gerenteadministrativo,$texto);
        
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
	$dompdf->stream("OEC_".$oec.".pdf");
	
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