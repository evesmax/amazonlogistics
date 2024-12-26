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
                                format(loe.cantidad2,3) toneladas, format(c.cantidad2,3) toneladascanceladas, of.contacto gerenteadministrativo, 
                                cli.representantelegal 
                            from logistica_cancelacionordenesentrega c 
                                    left join logistica_ordenesentrega loe on loe.idordenentrega=c.idordenentrega
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
        $texto=str_replace("@cancelado", $oec,$texto);
        $texto=str_replace("@responsableingenio", $responsableingenio,$texto);        
        $texto=str_replace("@nombreingenio", $nombreingenio,$texto);        
        $texto=str_replace("@toneladas", $toneladas,$texto);  
        $texto=str_replace("@tmcanceladas", $toneladascanceladas,$texto); 
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
	$html.= "  td{font-size:8pt}";
	$html.= "</style>";
	$html.= "</head>";
//$html.=" <FORM id='envio' name='envio' method='post' action='envio_grabar.php'>";
	
	$html.= "<body style='font-family:helvetica'>";


        $html.= "<BODY onload='printSelec()' style='font-family:helvetica'>
                                        <center style='border-style: none'>
                                            <div id='printer'>";
        
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
               
                
                
                
                                //funciones javascript		
		$html_funcionesjavascript=" <script type='text/javascript'>
										function redireccion() {
											var pagina = '../../netwarelog/repolog/reporte.php';
											document.location.href=pagina;
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
                                                                                function pdf(folio,pdf){
                                                                                        var ref=0;
                                                                                        ref=folio;
                                                                                        document.location = 'pdf.php?idordencompra='+ref;
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
                                <a href='javascript:pdf(".$idcancelacion.");'> <img src='../../netwarelog/repolog/img/pdf.gif'  
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

                
                   
echo $opciones.$html;                



?>