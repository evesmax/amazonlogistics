<?php
	//include("../../netwarelog/catalog/conexionbd.php");

	
    $uw=strpos($_SESSION["sequel"],'where');
    $uo=strpos($_SESSION["sequel"],'order by');
    $ct=strlen($_SESSION["sequel"]);
    $td=($ct-$uo)*-1;
	
$sqllimpia="Delete From reporte_estadisticaventas where idref=-1";
$resultado = $conexion->consultar($sqllimpia);

    $sqlwhererend=substr($_SESSION["sequel"],$uw,$td);

	$sqlwhererend=str_replace("fecha", "oc.fecha",$sqlwhererend);     
	$sqlwhererend=str_replace("ingenio", "of.nombrefabricante",$sqlwhererend);  
	$sqlwhererend=str_replace("bodega", "ob.nombrebodega",$sqlwhererend); 
	$sqlwhererend=str_replace("producto", "ip.nombreproducto",$sqlwhererend);
	$sqlwhererend=str_replace("zafra", "il.descripcionlote",$sqlwhererend);
	$sqlwhererend=str_replace("r.Cliente", "cli.razonsocial",$sqlwhererend);
	$sqlwhererend=str_replace("oe", "coe.OE",$sqlwhererend);
        $sqlwhererend=str_replace("IE", "loe.referencia2",$sqlwhererend);
		$sqlwhererend=str_replace("r.TipoVenta", "tiv.tipodeventa",$sqlwhererend);
	
	$sqlwhererend=str_replace("oc.fecha", "loe.fecha",$sqlwhererend); 

	
	$gastoglogistica="ifnull((select sum(costototal) from logistica_costos lc where lc.idestado=ob.idestado and lc.idmunicipio=ob.idmunicipio and lc.idfabricante=oc.idfabricante),0) ";
    
	$sqlresumen="Select loe.fecha, coe.oe,loe.referencia2 ie,of.nombrefabricante ingenio, 
                case when vm.nombremarca=of.nombrefabricante then 'Propia' else vm.nombremarca end marca, 
                ob.NombreBodega bodega, ge.nombreestado estado, gm.nombremunicipio poblacion, cli.razonsocial cliente, 
                tic.tipocliente, il.descripcionlote zafra, ip.NombreProducto producto,  oc.volumenorden cantidadtm, 
                oc.importe importeventa, 
				ifnull(oc.precioventabutos,oc.precioventa/20)-(case when ob.tipobodega=-1 then 0 else $gastoglogistica end) preciobulto, 
				(case when ob.tipobodega=-1 then 0 else $gastoglogistica end) importeglogistica,
                ifnull(oc.precioventabutos,oc.precioventa/20) preciototalbulto, 
                (select sum(ocd.importe) from ventas_ordenescompra_depositos ocd where ocd.idordencompra=oc.idordencompra) deposito,
                oc.observaciones, tiv.tipodeventa tipoventa, -1 idref, oc.dias, oc.ordendecompra
				,(select ocd.fechadeposito from ventas_ordenescompra_depositos ocd where ocd.idordencompra=oc.idordencompra limit 1) fechapago
				,ie.descripcionestado calidad, tim.tipomercado, vsm.segmentomercado, cn.norma, oc.clavecontrato, oc.costo  
                from ventas_ordenesdecompra oc
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
                        left join consecutivos_oe coe on coe.idordencompra=oc.idordencompra
                        left join logistica_ordenesentrega loe on loe.referencia1=coe.oe
                        left join general_estados ge on ge.idestado=ob.idestado
                        left join general_municipios gm on gm.idmunicipio=ob.idmunicipio
                        left join ventas_segmentosdemercado vsm on vsm.idsegmento=oc.idsegmento 					
                        $sqlwhererend And oc.idestadodocumento=2
                order by loe.fecha";
				
		echo "<!--$sqlresumen-->";
        //AGREGA ESTADISTICA NORMAL
            $sqlagrega="Insert Into reporte_estadisticaventas $sqlresumen";
		
        
        $resultado = $conexion->consultar($sqlagrega);  
        
        $sqlwhererend=str_replace("loe.fecha", "c.fechacancelacion",$sqlwhererend);
        
        $sqlresumen="Select c.fechacancelacion, c.oecancelacion,0 ie,of.nombrefabricante ingenio, 
                    case when vm.nombremarca=of.nombrefabricante then 'Propia' else vm.nombremarca end marca, 
                    ob.NombreBodega bodega, ge.nombreestado estado, gm.nombremunicipio poblacion, cli.razonsocial cliente, 
                    tic.tipocliente, il.descripcionlote zafra, ip.NombreProducto producto,  (c.cantidad2*-1) cantidadtm, 
                    (c.cantidad2)*(oc.precioventa) importeventa, 
					ifnull(oc.precioventabutos,oc.precioventa/20)-(case when ob.tipobodega=-1 then 0 else $gastoglogistica end) preciobulto, 
					(case when ob.tipobodega=-1 then 0 else $gastoglogistica end) importeglogistica,
                                    ifnull(oc.precioventabutos,oc.precioventa/20) preciototalbulto, 
                                    0 deposito,
                                    c.observaciones, tiv.tipodeventa tipoventa, -1 idref, oc.dias, oc.ordendecompra 
									,'' fechapago
									,ie.descripcionestado calidad, tim.tipomercado, vsm.segmentomercado, cn.norma, oc.clavecontrato, oc.costo
                                    from ventas_ordenesdecompra oc
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
                                            left join consecutivos_oe coe on coe.idordencompra=oc.idordencompra
                                            left join logistica_ordenesentrega loe on loe.referencia1=coe.oe
											left join logistica_cancelacionordenesentrega c on c.idordenentrega=loe.idordenentrega
                                            left join general_estados ge on ge.idestado=ob.idestado
                                            left join general_municipios gm on gm.idmunicipio=ob.idmunicipio
                        left join ventas_segmentosdemercado vsm on vsm.idsegmento=oc.idsegmento 
						$sqlwhererend And c.idestadodocumento=1 
                order by loe.fecha";

        //AGREGA ESTADISTICA NORMAL
		$sqlagrega="Insert Into reporte_estadisticaventas $sqlresumen";
        $resultado = $conexion->consultar($sqlagrega);    
        


?>

