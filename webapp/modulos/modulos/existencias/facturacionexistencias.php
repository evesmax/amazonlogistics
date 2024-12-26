<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);

//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];

//Declarando Matriz de manejo e datos
        $ep=1;   //Inicia session de elementos matriz 
        $existencias=array("idfabricante","idmarca","idproducto","idloteproducto","idbodega",
                            "entradasacumuladas","entradasdeldia","salidasdeldia","salidasacumuladas","devoluciones","existenciafisica",
                            "cedes","comprometida","entransito","reservado","disponible",
                            "idestadoproducto","fecha","idfamilia","idempleado");
        
//Recupera Filtros
        //Obtiene Where
        $sfechacorte="";
        $sfechafactura="";
        
        $uw=strpos($_SESSION["sequel"],'where');
        $uo=strpos($_SESSION["sequel"],'and (re.fecha=');
        $ct=strlen($_SESSION["sequel"]);
        $td=($ct-$uo)*-1;
        $sqlwhere=substr($_SESSION["sequel"],$uw,$td);
        
        $sqlorderby="  order by of.nombrefabricante, ip.nombreproducto, ie.idestadoproducto, il.descripcionlote, ob.nombrebodega  ";
        
//Define fecha del dia y fecha del corte
            //Fecha de Corte
            $uw=strpos($_SESSION["sequel"],'re.fecha=');
                //echo $uw."<br><br>";
            $uo=strpos($_SESSION["sequel"],'re.fecha<=');
                //echo $uo."<br><br>";

            $ct=strlen($_SESSION["sequel"]); //Ancho Cadena Total
            $td=($ct-($uo-8))*-1;
            
        $sfechacorte=substr($_SESSION["sequel"],$uw+10,$td-2);       
            
//Define fecha de limite de entradas
            $uw=strpos($_SESSION["sequel"],'re.fecha<=');
                //echo $uw."<br><br>";
            $uo=strpos($_SESSION["sequel"],'re.idempleado=');
                //echo $uo."<br><br>";

            $ct=strlen($_SESSION["sequel"]); //Ancho Cadena Total
            $td=($ct-($uo-8))*-1;
        $sfechafactura=substr($_SESSION["sequel"],$uw+11,$td-1);  


        
        
        //Fecha de Corte
            $fecha = new DateTime($sfechacorte);
            $fechacorte=$fecha->format('Y-m-d');
            
        //Fecha Factura
            $fecha = new DateTime($sfechafactura);
            $fechafactura = $fecha->format('Y-m-d');
            
        //Fecha del Dia
            $sfechadia =$fecha=date("Y-m-d");

            $fecha = new DateTime($sfechadia);
            $fechadia = $fecha->format('Y-m-d');
            
            $desaldos=0;

            
//Actualiza Valores de Sql
            $fecha = new DateTime($sfechacorte);
            $fechareporte = $fecha->format('d-m-Y');

            $fecha = new DateTime($sfechafactura);
            $fechareporte2 = $fecha->format('d-m-Y');
            
            $etif="Inv Fisico TM: <br> [$fechareporte]";
            $sql=str_replace("@fisica",$etif,$sql);
            $_SESSION["subtotales_agrupaciones"]=str_replace("@fisica",$etif,$_SESSION["subtotales_agrupaciones"]);
            $_SESSION["subtotales_funciones"]=str_replace("@fisica",$etif,$_SESSION["subtotales_funciones"]);
            
            $etif="Entradas TM: <br> [$fechareporte2]";
            $sql=str_replace("@entradas",$etif,$sql);
            $_SESSION["subtotales_agrupaciones"]=str_replace("@entradas",$etif,$_SESSION["subtotales_agrupaciones"]);
            $_SESSION["subtotales_funciones"]=str_replace("@entradas",$etif,$_SESSION["subtotales_funciones"]);

            
            $etif="Devoluciones TM: <br> [$fechareporte2]";
            $sql=str_replace("@devoluciones",$etif,$sql);   
            $_SESSION["subtotales_agrupaciones"]=str_replace("@devoluciones",$etif,$_SESSION["subtotales_agrupaciones"]);
            $_SESSION["subtotales_funciones"]=str_replace("@devoluciones",$etif,$_SESSION["subtotales_funciones"]);

            
            $etif="Entradas Acumuladas TM: <br> [$fechareporte2]";
            $sql=str_replace("@eacumuladas",$etif,$sql);  
            $_SESSION["subtotales_agrupaciones"]=str_replace("@eacumuladas",$etif,$_SESSION["subtotales_agrupaciones"]);
            $_SESSION["subtotales_funciones"]=str_replace("@eacumuladas",$etif,$_SESSION["subtotales_funciones"]);
            
            
            $etif="Salidas TM: <br> [$fechareporte2]";
            $sql=str_replace("@salidas",$etif,$sql);
            $_SESSION["subtotales_agrupaciones"]=str_replace("@salidas",$etif,$_SESSION["subtotales_agrupaciones"]);
            $_SESSION["subtotales_funciones"]=str_replace("@salidas",$etif,$_SESSION["subtotales_funciones"]);

            
            $etif="Salidas Acumuladas TM: <br> [$fechareporte2]";
            $sql=str_replace("@sacumuladas",$etif,$sql); 
            $sqlwhere=str_replace("@sacumuladas",$etif,$sqlwhere);
            $_SESSION["subtotales_agrupaciones"]=str_replace("@sacumuladas",$etif,$_SESSION["subtotales_agrupaciones"]);
            $_SESSION["subtotales_funciones"]=str_replace("@sacumuladas",$etif,$_SESSION["subtotales_funciones"]);

            
            $fechacortereal="";
            $fechacortereal=suma_fechas($fechacorte, 1);
            $sqlfechames=" And (re.fecha between '".$fechacortereal." 00:00:00' and '$sfechafactura 23:59:59') "; //El movimiento del ultimo segundo
			
            $sqlfechacortemes=" And (re.fecha<='".$fechafactura." 23:59:59') "; //El movimiento del ultimo segundo

//SQL'S ___

        $sqlfechacorte=" And (re.fecha<='".$fechacorte." 23:59:59') "; //El movimiento del ultimo segundo
        
        if($fechacorte>=$fechadia){
            //Sql Si la fecha de corte es del mayor o igual al dia de hoy
            $sqlexistencias="select of.idfabricante, vm.idmarca, ip.idproducto,il.idloteproducto,ob.idbodega, 
                                re.saldosecundario 'existenciafisica', 
                                re.entradassecundario 'entradasacumuladas', re.salidassecundario 'salidasacumuladas',
                                0 entradasdeldia,
                                0 salidasdeldia,
                                ie.idestadoproducto,ip.idfamilia
                        from inventarios_saldos re
                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                            left join vista_marcas vm on vm.idmarca=re.idmarca
                            left join inventarios_productos ip on ip.idproducto=re.idproducto
                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                            left join operaciones_bodegas ob on ob.idbodega =re.idbodega
                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                            ".$sqlwhere.$sqlorderby;
							
            //Si son OE del Dia
            $sqlwheretemp="";
            $sqloe="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega, 
                        case when re.saldo1 is null then sum(re.cantidad1) else sum(re.saldo1) end 'saldo1',
                        case when re.saldo2 is null then sum(re.cantidad2) else sum(re.saldo2) end 'saldo2', 
                    from logistica_ordenesentrega re 
                                left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                                left join vista_marcas vm on vm.idmarca=re.idmarca
                                left join operaciones_bodegas ob on ob.idbodega=re.idbodega
                                left join inventarios_productos ip on ip.idproducto=re.idproducto
                                left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                                left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                                left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia
                    $sqlwheretemp And re.fecha<='$fechacorte 23:59:59'
                    And (re.fechacancelacion>='$fechacorte 23:59:59' or re.fechacancelacion is null)
                    group by re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto,
                    re.idbodega";
            $desaldos=1;
            
            //Obtener Entradas y Salidas del Dia  
        }else{
            
            
            $sqlexistencias="select 
                                of.idfabricante, vm.idmarca, ip.idproducto,il.idloteproducto,ob.idbodega,
                                sum(re.cantidadsecundaria*tm.efectoinventario) 'existenciafisica',
                                0 'entradasacumuladas',
                                0 'salidasacumuladas',
                                0 'entradasdeldia',
                                0 'salidasdeldia',
                                ie.idestadoproducto,ip.idfamilia
                            from 
                                inventarios_movimientos re 
                                left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                                left join vista_marcas vm on vm.idmarca=re.idmarca
                                left join operaciones_bodegas ob on ob.idbodega=re.idbodega
                                left join inventarios_productos ip on ip.idproducto=re.idproducto
                                left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                                left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                                left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=re.idtipomovimiento
                                left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                                ".$sqlwhere.$sqlfechacorte." 
                            group by of.idfabricante, vm.idmarca, ip.idproducto,il.idloteproducto,ob.idbodega, 
                                re.idestadoproducto,ip.idfamilia ".$sqlorderby;

            //exit();
			$sqlwheretemp="";
            $sqloe="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega,
                        sum(re.cantidad1)-
                        (select ifnull(sum(lor.cantidad1),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=3 and lor.fechasalida<='$fechacorte 23:59:59'  limit 1) 'saldo1',
                        sum(re.cantidad2)-
                        (select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=3  and lor.fechasalida<='$fechacorte 23:59:59' limit 1) 'saldo2'
                    from logistica_ordenesentrega re 
                                left join operaciones_fabricantes of on of.idfabricante=re.idfabricante
                                left join vista_marcas vm on vm.idmarca=re.idmarca
                                left join operaciones_bodegas ob on ob.idbodega=re.idbodega
                                left join inventarios_productos ip on ip.idproducto=re.idproducto
                                left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                                left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                                left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                                left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia
                    $sqlwheretemp And re.fecha<='$fechacorte 23:59:59'
                        And (re.fechacancelacion>='$fechacorte 23:59:59' or re.fechacancelacion is null)
                        group by re.idordenentrega, re.fechacancelacion, re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto,re.idbodega";
            //Obtener Acumulado ENtradas, Acumulado Salidas, y Entradas y Salidas del Dia
            
			//echo "entre a consulta de dias anteriores a hoy";
            $desaldos=0;
            
        }

		//echo $sqloe."<br><br>";
        //echo "EXISTENCIAS: <br><br> $sqlexistencias<br><br>";
                
                $totales=array("sub","tot"); //[1]=SalidasDia, [2]=Salidas Acumuladas, [3]=Devoluciones, [4]=Existencia Fisica,
                                                         //[5]=Ccedes, [6]=Comprometida, [7]=En Transito, [8]=Reservado
                                                         //[9]=Disponible
                //Inicializa Arreglo Totales
                $totales=intotales($totales,9,"sub",0);
                $totales=intotales($totales,9,"tot",0);
                
                $existencias=array("idfabricante","idmarca","idproducto","idloteproducto","idbodega",
                            "entradasacumuladas","salidasdeldia","salidasacumuladas","devoluciones","existenciafisica",
                            "cedes","comprometida","entransito","reservado","disponible",
                            "idestadoproducto","fecha","idfamilia","idempleado");

                //LLena Matriz con Valores Principales
                //echo $sqlexistencias."<br><br>";
                
                $ingant="";
                $resultado = $conexion->consultar($sqlexistencias);
                while($rs = $conexion->siguiente($resultado)){
   
                                //Si no es el primer registro y el Fabricante cambio entonces 
                                //echo "Ant: ".$ingant." <> Actual: ".$rs{"idfabricante"}."<br>";
						if($ep>1 && $ingant<>$rs{"idfabricante"}){
                                    //Suma al Arreglo Tot el valor de Sub
                                        $totales=sumaarreglos($totales,9,"tot","sub");       
                                    //Agrega un elemento a la matriz de Sub Total
                                            $existencias["idfabricante"][$ep]=$existencias["idfabricante"][$ep-1];
                                            $existencias["idmarca"][$ep]=$existencias["idmarca"][$ep-1];
                                            $existencias["idproducto"][$ep]=$existencias["idproducto"][$ep-1];
                                            $existencias["idloteproducto"][$ep]=$existencias["idloteproducto"][$ep-1];
                                            $existencias["idbodega"][$ep]=$existencias["idbodega"][$ep-1];                          
                                            $existencias["entradasacumuladas"][$ep]="Sub Total"; 
                                                $existencias["entradasdeldia"][$ep]=$totales["sub"][0];
                                                $existencias["salidasdeldia"][$ep]=$totales["sub"][1];
                                                $existencias["salidasacumuladas"][$ep]=$totales["sub"][2];
                                                $existencias["devoluciones"][$ep]=$totales["sub"][3];
                                                $existencias["existenciafisica"][$ep]=$totales["sub"][4];
                                                $existencias["cedes"][$ep]=$totales["sub"][5];
                                                $existencias["comprometida"][$ep]=$totales["sub"][6];
                                                $existencias["entransito"][$ep]=$totales["sub"][7];
                                                $existencias["reservado"][$ep]=$totales["sub"][8];
                                                $existencias["disponible"][$ep]=$totales["sub"][9];
                                            $existencias["idestadoproducto"][$ep]=$existencias["idestadoproducto"][$ep-1];
                                            $existencias["fecha"][$ep]=$existencias["fecha"][$ep-1];
                                            $existencias["idfamilia"][$ep]=$existencias["idfamilia"][$ep-1];
                                            $existencias["idempleado"][$ep]=$existencias["idempleado"][$ep-1];
                                    //Resetea el Arreglo sub
                                    $totales=intotales($totales,9,"sub",0);
                                    $ep=$ep+1;
									//Agrega Reistro Actual
											$existencias["idfabricante"][$ep]=$rs{"idfabricante"};
											$existencias["idmarca"][$ep]=$rs{"idmarca"};
											$existencias["idproducto"][$ep]=$rs{"idproducto"};
											$existencias["idloteproducto"][$ep]=$rs{"idloteproducto"};
											$existencias["idbodega"][$ep]=$rs{"idbodega"};                        
											$existencias["entradasacumuladas"][$ep]=$rs{"entradasacumuladas"};
												$existencias["entradasdeldia"][$ep]=$rs{"entradasdeldia"};
													$totales["sub"][0]+=$rs{"entradasdeldia"};    
												$existencias["salidasdeldia"][$ep]=$rs{"salidasdeldia"};
													$totales["sub"][1]+=$rs{"salidasdeldia"};                       
												$existencias["salidasacumuladas"][$ep]=$rs{"salidasacumuladas"};
													$totales["sub"][2]+=$rs{"salidasacumuladas"};                       
												$existencias["devoluciones"][$ep]=0;
													$totales["sub"][3]+=0;                        
												$existencias["existenciafisica"][$ep]=$rs{"existenciafisica"};
													$totales["sub"][4]+=$rs{"existenciafisica"};
												$existencias["cedes"][$ep]=0;
													$totales["sub"][5]+=0;
												$existencias["comprometida"][$ep]=0;
													$totales["sub"][6]+=0;
												$existencias["entransito"][$ep]=0;
													$totales["sub"][7]+=0;                        
												$existencias["reservado"][$ep]=0;
													$totales["sub"][8]+=0;
												$existencias["disponible"][$ep]=$rs{"existenciafisica"}; //TEMPORAL 
													$totales["sub"][9]+=$rs{"existenciafisica"};
											$existencias["idestadoproducto"][$ep]=$rs{"idestadoproducto"};
											$existencias["fecha"][$ep]=$fechacorte;
											$existencias["idfamilia"][$ep]=$rs{"idfamilia"};
											$existencias["idempleado"][$ep]=$usuario;
						}else{
									$existencias["idfabricante"][$ep]=$rs{"idfabricante"};
									$existencias["idmarca"][$ep]=$rs{"idmarca"};
									$existencias["idproducto"][$ep]=$rs{"idproducto"};
									$existencias["idloteproducto"][$ep]=$rs{"idloteproducto"};
									$existencias["idbodega"][$ep]=$rs{"idbodega"};                        
									$existencias["entradasacumuladas"][$ep]=$rs{"entradasacumuladas"};
										$existencias["entradasdeldia"][$ep]=$rs{"entradasdeldia"};
											$totales["sub"][0]+=$rs{"entradasdeldia"};    
										$existencias["salidasdeldia"][$ep]=$rs{"salidasdeldia"};
											$totales["sub"][1]+=$rs{"salidasdeldia"};                       
										$existencias["salidasacumuladas"][$ep]=$rs{"salidasacumuladas"};
											$totales["sub"][2]+=$rs{"salidasacumuladas"};                       
										$existencias["devoluciones"][$ep]=0;
											$totales["sub"][3]+=0;                        
										$existencias["existenciafisica"][$ep]=$rs{"existenciafisica"};
											$totales["sub"][4]+=$rs{"existenciafisica"};
										$existencias["cedes"][$ep]=0;
											$totales["sub"][5]+=0;
										$existencias["comprometida"][$ep]=0;
											$totales["sub"][6]+=0;
										$existencias["entransito"][$ep]=0;
											$totales["sub"][7]+=0;                        
										$existencias["reservado"][$ep]=0;
											$totales["sub"][8]+=0;
										$existencias["disponible"][$ep]=$rs{"existenciafisica"}; //TEMPORAL 
											$totales["sub"][9]+=$rs{"existenciafisica"};
									$existencias["idestadoproducto"][$ep]=$rs{"idestadoproducto"};
									$existencias["fecha"][$ep]=$fechacorte;
									$existencias["idfamilia"][$ep]=$rs{"idfamilia"};
									$existencias["idempleado"][$ep]=$usuario;
                        }
                    $ingant=$rs{"idfabricante"};    
                    $ep=$ep+1;

                }
                $conexion->cerrar_consulta($resultado);

                            //AGREGA ULTIMO SUBTOTAL
                                if($ep>1){
                                    //Suma al Arreglo Tot el valor de Sub
                                        $totales=sumaarreglos($totales,9,"tot","sub");       
                                    //Agrega un elemento a la matriz de Sub Total
                                            $existencias["idfabricante"][$ep]=$existencias["idfabricante"][$ep-1];
                                            $existencias["idmarca"][$ep]=$existencias["idmarca"][$ep-1];
                                            $existencias["idproducto"][$ep]=$existencias["idproducto"][$ep-1];
                                            $existencias["idloteproducto"][$ep]=$existencias["idloteproducto"][$ep-1];
                                            $existencias["idbodega"][$ep]=$existencias["idbodega"][$ep-1];                          
                                            $existencias["entradasacumuladas"][$ep]="Sub Total"; 
                                                $existencias["entradasdeldia"][$ep]=$totales["sub"][0];
                                                $existencias["salidasdeldia"][$ep]=$totales["sub"][1];
                                                $existencias["salidasacumuladas"][$ep]=$totales["sub"][2];
                                                $existencias["devoluciones"][$ep]=$totales["sub"][3];
                                                $existencias["existenciafisica"][$ep]=$totales["sub"][4];
                                                $existencias["cedes"][$ep]=$totales["sub"][5];
                                                $existencias["comprometida"][$ep]=$totales["sub"][6];
                                                $existencias["entransito"][$ep]=$totales["sub"][7];
                                                $existencias["reservado"][$ep]=$totales["sub"][8];
                                                $existencias["disponible"][$ep]=$totales["sub"][9];
                                            $existencias["idestadoproducto"][$ep]=$existencias["idestadoproducto"][$ep-1];
                                            $existencias["fecha"][$ep]=$existencias["fecha"][$ep-1];
                                            $existencias["idfamilia"][$ep]=$existencias["idfamilia"][$ep-1];
                                            $existencias["idempleado"][$ep]=$existencias["idempleado"][$ep-1];
                                    //Resetea el Arreglo sub
                                    $totales=intotales($totales,9,"sub",0);
                                
                            //AGREGA TOTAL FINAL
                                        //Agrega un elemento a la matriz de Total
                                        $ep=$ep+1;
                                            $existencias["idfabricante"][$ep]=$existencias["idfabricante"][$ep-1];
                                            $existencias["idmarca"][$ep]=$existencias["idmarca"][$ep-1];
                                            $existencias["idproducto"][$ep]=$existencias["idproducto"][$ep-1];
                                            $existencias["idloteproducto"][$ep]=$existencias["idloteproducto"][$ep-1];
                                            $existencias["idbodega"][$ep]=$existencias["idbodega"][$ep-1];                          
                                            $existencias["entradasacumuladas"][$ep]="Total";
                                                $existencias["entradasdeldia"][$ep]=$totales["tot"][0];
                                                $existencias["salidasdeldia"][$ep]=$totales["tot"][1];
                                                $existencias["salidasacumuladas"][$ep]=$totales["tot"][2];
                                                $existencias["devoluciones"][$ep]=$totales["sub"][3];
                                                $existencias["existenciafisica"][$ep]=$totales["tot"][4];
                                                $existencias["cedes"][$ep]=$totales["tot"][5];
                                                $existencias["comprometida"][$ep]=$totales["tot"][6];
                                                $existencias["entransito"][$ep]=$totales["tot"][7];
                                                $existencias["reservado"][$ep]=$totales["tot"][8];
                                                $existencias["disponible"][$ep]=$totales["tot"][9];
                                            $existencias["idestadoproducto"][$ep]=$existencias["idestadoproducto"][$ep-1];
                                            $existencias["fecha"][$ep]=$existencias["fecha"][$ep-1];
                                            $existencias["idfamilia"][$ep]=$existencias["idfamilia"][$ep-1];
                                            $existencias["idempleado"][$ep]=$existencias["idempleado"][$ep-1];
                                    //Resetea el Arreglo sub
                                    $totales=intotales($totales,9,"sub",0);
                                    $ep=$ep+1;
                                }

                                
           
                            
//ACUMULADAS
                if($desaldos==0){   
                        //Obtiene ENTRADAS DEL MES FACTURACION
                                    $sqlentradassalidas="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega,
                                                             sum(re.cantidadsecundaria) 'entradasacumuladas'
                                                        from inventarios_movimientos re 
                                                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                                                            left join vista_marcas vm on vm.idmarca=re.idmarca left join operaciones_bodegas ob on ob.idbodega=re.idbodega 
                                                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                                                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto 
                                                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto 
                                                            left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=re.idtipomovimiento 
                                                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto 
                                                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                                                        $sqlwhere And (tm.efectoinventario=1 and tm.afectafacturacion=-1) $sqlfechames
                                                        group by of.idfabricante, vm.idmarca,  ip.idproducto,il.idloteproducto,ob.idbodega, ie.idestadoproducto,ip.idfamilia 
                                                        order by of.nombrefabricante, ip.nombreproducto, il.descripcionlote, ob.nombrebodega";
                                                $resultado = $conexion->consultar($sqlentradassalidas);
                                                while($rs = $conexion->siguiente($resultado)){
                                                      $existencias=agregavalor($existencias,$ep,"entradasdeldia",$rs{"idfabricante"},$rs{"idmarca"},$rs{"idproducto"},$rs{"idestadoproducto"},$rs{"idloteproducto"},$rs{"idbodega"},$rs{"entradasacumuladas"});  
                                                }        
                                    $conexion->cerrar_consulta($resultado); 

                                  
                                    
                        //Obtiene ENTRADAS ACUMULADAS A LA FECHA
                                        $sqlentradassalidas="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega,
                                                             sum(re.cantidadsecundaria) 'entradasacumuladas'
                                                        from inventarios_movimientos re 
                                                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                                                            left join vista_marcas vm on vm.idmarca=re.idmarca left join operaciones_bodegas ob on ob.idbodega=re.idbodega 
                                                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                                                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto 
                                                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto 
                                                            left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=re.idtipomovimiento 
                                                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto 
                                                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                                                        $sqlwhere And tm.efectoinventario=1 $sqlfechacortemes
                                                        group by of.idfabricante, vm.idmarca,  ip.idproducto,il.idloteproducto,ob.idbodega, ie.idestadoproducto,ip.idfamilia 
                                                        order by of.nombrefabricante, ip.nombreproducto, il.descripcionlote, ob.nombrebodega";
                                                $resultado = $conexion->consultar($sqlentradassalidas);
                                                while($rs = $conexion->siguiente($resultado)){
                                                      $existencias=agregavalor($existencias,$ep,"entradasacumuladas",$rs{"idfabricante"},$rs{"idmarca"},$rs{"idproducto"},$rs{"idestadoproducto"},$rs{"idloteproducto"},$rs{"idbodega"},$rs{"entradasacumuladas"});  
                                                }        
                                    $conexion->cerrar_consulta($resultado); 
                                    
                                    
                                    
                        //Obtiene SALIDAS DEL MES
                                    $sqlentradassalidas="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega,
                                                             sum(re.cantidadsecundaria) 'salidasacumuladas'
                                                        from inventarios_movimientos re 
                                                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                                                            left join vista_marcas vm on vm.idmarca=re.idmarca left join operaciones_bodegas ob on ob.idbodega=re.idbodega 
                                                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                                                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto 
                                                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto 
                                                            left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=re.idtipomovimiento 
                                                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto 
                                                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                                                        $sqlwhere And tm.efectoinventario=-1 $sqlfechames
                                                        group by of.idfabricante, vm.idmarca, ip.idproducto,il.idloteproducto,ob.idbodega, ie.idestadoproducto,ip.idfamilia 
                                                        order by of.nombrefabricante, ip.nombreproducto, il.descripcionlote, ob.nombrebodega";
                                                $resultado = $conexion->consultar($sqlentradassalidas);
                                                while($rs = $conexion->siguiente($resultado)){
                                                      $existencias=agregavalor($existencias,$ep,"salidasdeldia",$rs{"idfabricante"},$rs{"idmarca"},$rs{"idproducto"},$rs{"idestadoproducto"},$rs{"idloteproducto"},$rs{"idbodega"},$rs{"salidasacumuladas"});  
                                                }        
                                    $conexion->cerrar_consulta($resultado);
                                    
                        //Obtiene SALIDAS ACUMULADAS
                                    $sqlentradassalidas="select re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega,
                                                             sum(re.cantidadsecundaria) 'salidasacumuladas'
                                                        from inventarios_movimientos re 
                                                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                                                            left join vista_marcas vm on vm.idmarca=re.idmarca left join operaciones_bodegas ob on ob.idbodega=re.idbodega 
                                                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                                                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto 
                                                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto 
                                                            left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=re.idtipomovimiento 
                                                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto 
                                                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                                                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                                                        $sqlwhere And tm.efectoinventario=-1 $sqlfechacortemes
                                                        group by of.idfabricante, vm.idmarca, ip.idproducto,il.idloteproducto,ob.idbodega, ie.idestadoproducto,ip.idfamilia 
                                                        order by of.nombrefabricante, ip.nombreproducto, il.descripcionlote, ob.nombrebodega";
                                                $resultado = $conexion->consultar($sqlentradassalidas);
                                                while($rs = $conexion->siguiente($resultado)){
                                                      $existencias=agregavalor($existencias,$ep,"salidasacumuladas",$rs{"idfabricante"},$rs{"idmarca"},$rs{"idproducto"},$rs{"idestadoproducto"},$rs{"idloteproducto"},$rs{"idbodega"},$rs{"salidasacumuladas"});  
                                                }        
                                    $conexion->cerrar_consulta($resultado);                                    
                                    
                                    
                }
        
            //Obtener Devoluciones Ventas
            
        

        
        
//RECALCULA ELEMETOS MATRIZ
                    $totales=intotales($totales,9,"sub",0);
                    $totales=intotales($totales,9,"tot",0);             
                
                    for ($i = 1; $i <= $ep-1; $i++) {
                            
                           //AGREGA SUBTOTALES 
                            if ($existencias["entradasacumuladas"][$i]=="Sub Total") {
                                
                                $existencias["entradasdeldia"][$i]=$totales["sub"][0];
                                $existencias["salidasdeldia"][$i]=$totales["sub"][1];
                                $existencias["salidasacumuladas"][$i]=$totales["sub"][2];
                                $existencias["devoluciones"][$i]=$totales["sub"][3];
                                $existencias["existenciafisica"][$i]=$totales["sub"][4];
                                $existencias["cedes"][$i]=$totales["sub"][5];
                                $existencias["comprometida"][$i]=$totales["sub"][6];
                                $existencias["entransito"][$i]=$totales["sub"][7];
                                $existencias["reservado"][$i]=$totales["sub"][8];
                                $existencias["disponible"][$i]=$totales["sub"][4]+$totales["sub"][0]+$totales["sub"][3];

                                //Suma Arreglos
                                $totales=sumaarreglos($totales,9,"tot","sub");       
                                //Resetea Arreglo Sub
                                $totales=intotales($totales,9,"sub",0);

                            }else{
                                $existencias["disponible"][$i]=$existencias["existenciafisica"][$i]+$existencias["entradasdeldia"][$i]-($existencias["devoluciones"][$i]);
                                
                                $totales["sub"][0]+=$existencias["entradasdeldia"][$i];
                                $totales["sub"][1]+=$existencias["salidasdeldia"][$i];
                                $totales["sub"][2]+=$existencias["salidasacumuladas"][$i];
                                $totales["sub"][3]+=$existencias["devoluciones"][$i];
                                $totales["sub"][4]+=$existencias["existenciafisica"][$i];
                                $totales["sub"][5]+=$existencias["cedes"][$i];
                                $totales["sub"][6]+=$existencias["comprometida"][$i];
                                $totales["sub"][7]+=$existencias["entransito"][$i];
                                $totales["sub"][8]+=$existencias["reservado"][$i];
                                $totales["sub"][9]+=$existencias["disponible"][$i];
                            }
                           
                           //AGREGA TOTAL FINAL
                           if ($existencias["entradasacumuladas"][$i]=="Total") { 
                                $existencias["entradasdeldia"][$i]=$totales["tot"][0];
                                $existencias["salidasdeldia"][$i]=$totales["tot"][1];
                                $existencias["salidasacumuladas"][$i]=$totales["tot"][2];
                                $existencias["devoluciones"][$i]=$totales["tot"][3];
                                $existencias["existenciafisica"][$i]=$totales["tot"][4];
                                $existencias["cedes"][$i]=$totales["tot"][5];
                                $existencias["comprometida"][$i]=$totales["tot"][6];
                                $existencias["entransito"][$i]=$totales["tot"][7];
                                $existencias["reservado"][$i]=$totales["tot"][8];
                                $existencias["disponible"][$i]=$totales["tot"][4]+$totales["tot"][0]+$totales["tot"][3];
                               
                           }
                    }

//Define matriz de elementos con totales y cantidades formateadas
                                
        

//Llenando Matriz de elementos en funcion de la consulta
		//Grabando Datos en Consulta para insertar
		$insert="Insert Into reporte_existencias
                    (idfabricante,idmarca,idproducto,idloteproducto,idbodega,
                    entradasacumuladas,entradasdeldia,salidasdeldia,salidasacumuladas,devoluciones,existenciafisica,
                    cedes,comprometida,entransito,reservado,disponible,
                    idestadoproducto,fecha,idfamilia,idempleado) Values ";
                
		$values="";
		for ($i = 1; $i <= $ep-1; $i++) {
                    if ($existencias["entradasacumuladas"][$i]<>"Sub Total" && $existencias["entradasacumuladas"][$i]<>"Total"){
			$values.="(
                            '".$existencias["idfabricante"][$i]."',
                            '".$existencias["idmarca"][$i]."',
                            '".$existencias["idproducto"][$i]."',
                            '".$existencias["idloteproducto"][$i]."',
                            '".$existencias["idbodega"][$i]."',
                            '".$existencias["entradasacumuladas"][$i]."',
                            '".$existencias["entradasdeldia"][$i]."',
                            '".$existencias["salidasdeldia"][$i]."',
                            '".$existencias["salidasacumuladas"][$i]."',
                            '".$existencias["devoluciones"][$i]."',
                            '".$existencias["existenciafisica"][$i]."',
                            '".$existencias["cedes"][$i]."',
                            '".$existencias["comprometida"][$i]."',
                            '".$existencias["entransito"][$i]."',
                            '".$existencias["reservado"][$i]."',
                            '".$existencias["disponible"][$i]."',                         
                            '".$existencias["idestadoproducto"][$i]."',
                            '".$existencias["fecha"][$i]."',
                            '".$existencias["idfamilia"][$i]."',
                            '".$existencias["idempleado"][$i]."'
			),";
                        
                    }
                        
                }
                
               
		//Consulta para eliminar registros Anteriores del Mismo Usuario
		$sqldelete="delete from reporte_existencias where idempleado=".$usuario;
                    //echo $sqldelete."<br><br>";
                $conexion->consultar($sqldelete);
                
                //Agregando registros a Reporte
                $values=substr($values, 0, -1); //elimina la ultima coma
                    //echo $insert." ".$values."<br>";
		$conexion->consultar($insert." ".$values);
                
//Elimina Valores Anteriores del usuario



//Insertando valores en la tabla de reporte existencias

                
                
//Funciones

                
	function intotales($matriz,$elementos,$campo,$valor){
		
		for ($ibm = 0; $ibm <= $elementos; $ibm++) {
			$matriz[$campo][$ibm]=$valor;
		}		
		return $matriz;
	}

	function sumaarreglos($arreglo,$elementos,$camporesultado,$campovalor){
		for ($ibm = 0; $ibm <= $elementos; $ibm++) {
			$arreglo[$camporesultado][$ibm]+=$arreglo[$campovalor][$ibm];
		}		
		return $arreglo;         
	}
        //Matriz, Elementos,Campo Resultado, Filtros
        function agregavalor($arreglo,$elementos,$camporesultado,$idfabricante,$idmarca,$idproducto,$idestadoproducto,$idloteproducto,$idbodega,$campovalor){
                global $ep,$usuario,$fechacorte;
                //echo "Elementos: $ep usuario: $usuario <br>";
                //Recorre la matriz hasta que encuentra el valor
                for ($ibm = 1; $ibm <= $elementos-1; $ibm++) {
                        if($arreglo["idfabricante"][$ibm]==$idfabricante 
                                && $arreglo["idmarca"][$ibm]==$idmarca 
                                && $arreglo["idproducto"][$ibm]==$idproducto 
                                && $arreglo["idestadoproducto"][$ibm]==$idestadoproducto 
                                && $arreglo["idloteproducto"][$ibm]==$idloteproducto 
                                && $arreglo["idbodega"][$ibm]==$idbodega){
                            $arreglo[$camporesultado][$ibm]=$arreglo[$camporesultado][$ibm]+$campovalor;
                            return $arreglo;
                            break;
                        }
                        
		}
                
                //Inicializa Variables
                        $arreglo["idfabricante"][$ep]=$idfabricante;
                        $arreglo["idmarca"][$ep]=$idmarca;
                        $arreglo["idproducto"][$ep]=$idproducto;
                        $arreglo["idloteproducto"][$ep]=$idloteproducto;
                        $arreglo["idbodega"][$ep]=$idbodega;
                        $arreglo["entradasacumuladas"][$ep]=0;
                        $arreglo["salidasdeldia"][$ep]=0;
                        $arreglo["salidasacumuladas"][$ep]=0;
                        $arreglo["devoluciones"][$ep]=0;
                        $arreglo["existenciafisica"][$ep]=0;
                        $arreglo["cedes"][$ep]=0;
                        $arreglo["comprometida"][$ep]=0;
                        $arreglo["entransito"][$ep]=0;
                        $arreglo["reservado"][$ep]=0;
                        $arreglo["disponible"][$ep]=0;
                        $arreglo["idestadoproducto"][$ep]=$idestadoproducto;
                        $arreglo["fecha"][$ep]=$fechacorte;
                        $arreglo["idfamilia"][$ep]=0;
                        $arreglo["disponible"][$ep]=0;
                        $arreglo["idempleado"][$ep]=$usuario;
                        //Graba variable que tiene valor
                        $arreglo["$camporesultado"][$ep]=$campovalor;
                        $ep++;                        
		return $arreglo;
        };
        
       function suma_fechas($fecha,$ndias)     
		{		
			  if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
				list($año,$mes,$dia)=split("/", $fecha);
				
			  if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))	
				list($año,$mes,$dia)=split("-",$fecha);
				
				$nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
				$nuevafecha=date("Y-m-d",$nueva);
					
			  return ($nuevafecha);  
		};
        
?>
