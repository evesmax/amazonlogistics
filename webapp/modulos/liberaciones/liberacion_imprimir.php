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
					$descripcionestado="";
                    $dias=30;
                    $idsegmento="";
					
		$sqlestatus="select  oc.idbodega, oc.idcliente, oc.idfabricante, oc.ordendecompra, cli.razonsocial cliente,cli.representantelegal,cli.cie 'ciecliente',
                                    tic.tipocliente, tim.tipomercado, tiv.tipodeventa, oc.fecha, of.nombrefabricante fideicomiso
                                    ,of.representantelegal representantelegalfideicomiso, of.clavecie 'claveciefideicomiso', 
                                    oc.clavecontrato,vm.nombremarca marca, ip.nombreproducto, concat('En Norma: ',cn.norma) 'calidad', 
                                    il.descripcionlote 'zafra', um.descripcionunidad 'presentacion', ob.nombrebodega, format(oc.volumenorden,2) 'volumenorden',
                                    format(oc.precioventa,2) 'precio', format(oc.importe,2) importe, oc.fechalimitepago,
                                    (select oe from consecutivos_oe where idordencompra=oc.idordencompra order by idoe desc limit 1) 'oe',
                                    (select ie from consecutivos_ie where idordencompra=oc.idordencompra order by idie desc  limit 1) 'ie', 
									of.contacto 'gerenteadministrativo', ie.descripcionestado, oc.dias, oc.idsegmento, tim.idtipomercado 
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
                    $descripcionestado=$rs{"descripcionestado"};
					$dias=$rs{"dias"};
					$idsegmento=$rs{"idsegmento"};
					$idtipomercado=$rs{"idtipomercado"};
		}
                $conexion->cerrar_consulta($result);                        
                
                $zafra=str_replace("*", "",$zafra);
                $descripcionestado=str_replace("*", "",$descripcionestado);        
//SUSTITUYENDO VALORES DEL TEXTO
                if ($dias==0){
					$dias=30;
				}
				$leyendamercado="";
				if ($idsegmento==2){
					$leyendamercado="Este producto será entregado bajo régimen de exportación mercado americano";
					$texto=str_replace("@leyendamercado", $leyendamercado,$texto);
				}elseif ($idsegmento==3){
					$leyendamercado="Este producto será entregado bajo régimen de exportación IMMEX";
					$texto=str_replace("@leyendamercado", $leyendamercado,$texto);					
				}
				
				
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

		//Solo Mostrara Norma cuando es mercado Domestico
		if ($idtipomercado==1){
			$texto=str_replace("@norma", $calidad,$texto);
		}else{
			$texto=str_replace("@norma", "",$texto);
		}
		$texto=str_replace("@leyendamercado", $leyendamercado,$texto);
        $texto=str_replace("@responsableingenio", $representantelegalfideicomiso,$texto);        
        $texto=str_replace("@nombreingenio", $fideicomiso,$texto);        

        $texto=str_replace("@toneladas", $volumenorden,$texto);        
        $texto=str_replace("@producto", $nombreproducto." ".$descripcionestado,$texto);
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
                                <a href='javascript:pdf(".$idordencompra.");'> <img src='../../netwarelog/repolog/img/pdf.gif'  
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