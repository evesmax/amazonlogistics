<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);
 
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
 
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];




//Declarando Matriz de manejo e datos
        $ep=1;   //Inicia session de elementos matriz 

        
//Recupera Filtros
        //Obtiene Where
        $uw=strpos($_SESSION["sequel"],'where');
        $uo=strpos($_SESSION["sequel"],'and (re.fecha');
        $ct=strlen($_SESSION["sequel"]);
        $td=($ct-$uo)*-1;
        $sqlwhere=substr($_SESSION["sequel"],$uw,$td);
        
        $sqlorderby="  order by of.nombrefabricante, ip.nombreproducto, il.descripcionlote, ob.nombrebodega  ";
        
    //Define fecha del dia y fecha del corte
        //Fecha de Corte
        $uw=strpos($_SESSION["sequel"],'re.fecha');
            //echo $uw."<br><br>";
        $uo=strpos($_SESSION["sequel"],'re.idempleado');
            //echo $uo."<br><br>";
            
        $ct=strlen($_SESSION["sequel"]); //Ancho Cadena Total
        $td=($ct-($uo-8))*-1;
            $sfechacorte=substr($_SESSION["sequel"],$uw+11,$td);      
            
  
        //Fecha de Corte
            $fecha = new DateTime($sfechacorte);
            $fechacorte = $fecha->format('Y-m-d');
 
        //Fecha del Dia
            $sfechadia =$fecha=date("Y-m-d");

            $fecha = new DateTime($sfechadia);
            $fechadia = $fecha->format('Y-m-d');
            
            $desaldos=0;

            
            //Dias
              
        //Fecha de Corte
        $uw=strpos($_SESSION["sequel"],'and re.dias');
            //echo $uw." Dias<br><br>";
        $uo=strpos($_SESSION["sequel"],'order');
            //echo $uo."<br><br>";
            
        $ct=strlen($_SESSION["sequel"]); //Ancho Cadena Total
        $td=($ct-($uo))*-1;
            $dias=substr($_SESSION["sequel"],$uw,$td);        
        //Validadando Dias
//            $ct=strlen($dias);
//            $ana=substr($dias,$ct-2);
//            if ($ana=='= '){
//                $dias=$dias."0";
//            }
        $dias=str_replace("re.dias","datediff('$fechacorte',re.fecha)",$dias);
//SQL'S ___

        $sqlfechacorte=" And (re.fecha<='".$fechacorte." 23:59:59') "; //El movimiento del ultimo segundo
        
        
        //Sql Obtiene Saldos
                
        $sqlsaldosoe="select re.referencia1, re.referencia2, re.fecha,re.idbodega,re.idcliente,re.idfabricante,re.idmarca,
                            ip.idproducto,il.idloteproducto,
                            sum(re.cantidad2) 'inicial',
                            (select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<='$fechacorte 23:59:59' limit 1) 'retirada',
                            format(ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0),3)  'devuelta',
                            sum(re.cantidad2)-(select ifnull(sum(lor.cantidad2),0) from logistica_retiros lor where re.idordenentrega=lor.idordenentrega and lor.idestadodocumento=1 and lor.fechasalida<='$fechacorte 23:59:59' limit 1)
							+ifnull((Select sum(lds.cantidad2) from logistica_devoluciones_salidas lds where lds.idordenentrega=re.idordenentrega and lds.idestadodocumento=2),0) saldo,
                            ie.idestadoproducto, datediff('$fechacorte',re.fecha) dias
                        from logistica_ordenesentrega re 
                            left join operaciones_fabricantes of on of.idfabricante=re.idfabricante 
                            left join vista_marcas vm on vm.idmarca=re.idmarca
                            left join ventas_clientes vc on vc.idcliente=re.idcliente
                            left join operaciones_bodegas ob on ob.idbodega=re.idbodega 
                            left join inventarios_productos ip on ip.idproducto=re.idproducto 
                            left join inventarios_lotes il on il.idloteproducto=re.idloteproducto
                            left join inventarios_estados ie on ie.idestadoproducto=re.idestadoproducto
                            left join inventarios_unidadesproductos up on up.idproducto=re.idproducto
                            left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida 
                            left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia 
                            $sqlwhere 
                            And re.fecha<='$fechacorte 23:59:59'
                            And (re.fechacancelacion>='$fechacorte 23:59:59' or re.fechacancelacion is null)       
                        group by re.referencia1,re.referencia2, re.idfabricante,re.idmarca,re.idproducto,re.idestadoproducto,re.idloteproducto, re.idbodega $sqlorderby";        
        
        //echo $sqlsaldosoe;
        $totales=array("sub","tot"); //[1]=Inicial, [2]=Retirada, [3]=Devuelta, [4]=Saldo,
   
                //Inicializa Arreglo Totales
                $totales=intotales($totales,4,"sub",0);
                $totales=intotales($totales,4,"tot",0);
                

                $saldosoe=array("referencia1","referencia2","fecha","idbodega","idcliente","idfabricante","idmarca",
                            "idproducto","idloteproducto","idestadoproducto","inicial","retirada","devuelta","saldo","idempleado","dias");
                //LLena Matriz con Valores Principales

                $ingant="";
                $resultado = $conexion->consultar($sqlsaldosoe);
                while($rs = $conexion->siguiente($resultado)){
   
                                //Si no es el primer registro y el Fabricante cambio entonces 
                                //echo "Ant: ".$ingant." <> Actual: ".$rs{"idfabricante"}."<br>";
                                if($ep>1 && $ingant<>$rs{"idfabricante"}){
                                    //Suma al Arreglo Tot el valor de Sub
                                        $totales=sumaarreglos($totales,4,"tot","sub");       
                                    //Agrega un elemento a la matriz de Sub Total
                                            $saldosoe["referencia1"][$ep]="Sub Total";
                                            $saldosoe["referencia2"][$ep]="";
                                            $saldosoe["fecha"][$ep]=$saldosoe["fecha"][$ep-1];
                                            $saldosoe["idbodega"][$ep]=$saldosoe["idbodega"][$ep-1];                          
                                            $saldosoe["idcliente"][$ep]=$saldosoe["idcliente"][$ep-1]; 
                                            $saldosoe["idfabricante"][$ep]=$saldosoe["idfabricante"][$ep-1];
                                            $saldosoe["idmarca"][$ep]=$saldosoe["idmarca"][$ep-1];
                                            $saldosoe["idproducto"][$ep]=$saldosoe["idproducto"][$ep-1];
                                            $saldosoe["idloteproducto"][$ep]=$saldosoe["idloteproducto"][$ep-1];
                                            $saldosoe["idestadoproducto"][$ep]=$saldosoe["idestadoproducto"][$ep-1];
                                                $saldosoe["inicial"][$ep]=$totales["sub"][0];
                                                $saldosoe["retirada"][$ep]=$totales["sub"][1];
                                                $saldosoe["devuelta"][$ep]=$totales["sub"][2];
                                                $saldosoe["saldo"][$ep]=$totales["sub"][3];
                                            $saldosoe["idempleado"][$ep]=$saldosoe["idempleado"][$ep-1];
                                            $saldosoe["dias"][$ep]=$saldosoe["dias"][$ep-1];
                                            
                                    //Resetea el Arreglo sub
                                    $totales=intotales($totales,4,"sub",0);
									$ep=$ep+1;
									//Agrega Reistro Actual
											$saldosoe["referencia1"][$ep]=$rs{"referencia1"};
											$saldosoe["referencia2"][$ep]=$rs{"referencia2"};
											$saldosoe["fecha"][$ep]=$rs{"fecha"};
											$saldosoe["idbodega"][$ep]=$rs{"idbodega"};
											$saldosoe["idcliente"][$ep]=$rs{"idcliente"}; 
                                                                                        $saldosoe["idfabricante"][$ep]=$rs{"idfabricante"};
                                                                                        $saldosoe["idmarca"][$ep]=$rs{"idfabricante"};                                                                                        
                                                                                        $saldosoe["idproducto"][$ep]=$rs{"idproducto"};
                                                                                        $saldosoe["idloteproducto"][$ep]=$rs{"idloteproducto"};
                                                                                        $saldosoe["idestadoproducto"][$ep]=$rs{"idestadoproducto"};
												$saldosoe["inicial"][$ep]=$rs{"inicial"};
													$totales["sub"][0]+=$rs{"inicial"};    
												$saldosoe["retirada"][$ep]=$rs{"retirada"};
													$totales["sub"][1]+=$rs{"retirada"};                       
												$saldosoe["devuelta"][$ep]=$rs{"devuelta"};
													$totales["sub"][2]+=$rs{"devuelta"};                       
												$saldosoe["saldo"][$ep]=$rs{"saldo"};
													$totales["sub"][3]+=$rs{"saldo"};;                        
											$saldosoe["idempleado"][$ep]=$usuario;	
                                                                                        $saldosoe["dias"][$ep]=$rs{"dias"};
						}else{
                                                    					$saldosoe["referencia1"][$ep]=$rs{"referencia1"};
											$saldosoe["referencia2"][$ep]=$rs{"referencia2"};
											$saldosoe["fecha"][$ep]=$rs{"fecha"};
											$saldosoe["idbodega"][$ep]=$rs{"idbodega"};
											$saldosoe["idcliente"][$ep]=$rs{"idcliente"}; 
                                                                                        $saldosoe["idfabricante"][$ep]=$rs{"idfabricante"};
                                                                                        $saldosoe["idmarca"][$ep]=$rs{"idfabricante"};                                                                                        
                                                                                        $saldosoe["idproducto"][$ep]=$rs{"idproducto"};
                                                                                        $saldosoe["idloteproducto"][$ep]=$rs{"idloteproducto"};
                                                                                        $saldosoe["idestadoproducto"][$ep]=$rs{"idestadoproducto"};
												$saldosoe["inicial"][$ep]=$rs{"inicial"};
													$totales["sub"][0]+=$rs{"inicial"};    
												$saldosoe["retirada"][$ep]=$rs{"retirada"};
													$totales["sub"][1]+=$rs{"retirada"};                       
												$saldosoe["devuelta"][$ep]=$rs{"devuelta"};
													$totales["sub"][2]+=$rs{"devuelta"};
												$saldosoe["saldo"][$ep]=$rs{"saldo"};
													$totales["sub"][3]+=$rs{"saldo"};                        
											$saldosoe["idempleado"][$ep]=$usuario;
                                                                                        $saldosoe["dias"][$ep]=$rs{"dias"};
                                                    }
                    $ingant=$rs{"idfabricante"};    
                    $ep=$ep+1;

                }
                $conexion->cerrar_consulta($resultado);
        
                            //AGREGA ULTIMO SUBTOTAL
                                if($ep>1){
                                    //Suma al Arreglo Tot el valor de Sub
                                        $totales=sumaarreglos($totales,4,"tot","sub");       
                                    //Agrega un elemento a la matriz de Sub Total
                                            $saldosoe["referencia1"][$ep]="Sub Total";
                                            $saldosoe["referencia2"][$ep]="";
                                            $saldosoe["fecha"][$ep]=$saldosoe["fecha"][$ep-1];
                                            $saldosoe["idbodega"][$ep]=$saldosoe["idbodega"][$ep-1];                          
                                            $saldosoe["idcliente"][$ep]=$saldosoe["idcliente"][$ep-1]; 
                                            $saldosoe["idfabricante"][$ep]=$saldosoe["idfabricante"][$ep-1];
                                            $saldosoe["idmarca"][$ep]=$saldosoe["idmarca"][$ep-1];
                                            $saldosoe["idproducto"][$ep]=$saldosoe["idproducto"][$ep-1];
                                            $saldosoe["idloteproducto"][$ep]=$saldosoe["idloteproducto"][$ep-1];
                                            $saldosoe["idestadoproducto"][$ep]=$saldosoe["idestadoproducto"][$ep-1];
                                                $saldosoe["inicial"][$ep]=$totales["sub"][0];
                                                $saldosoe["retirada"][$ep]=$totales["sub"][1];
                                                $saldosoe["devuelta"][$ep]=$totales["sub"][2];
                                                $saldosoe["saldo"][$ep]=$totales["sub"][3];
                                            $saldosoe["idempleado"][$ep]=$saldosoe["idempleado"][$ep-1];
                                            $saldosoe["dias"][$ep]=$saldosoe["dias"][$ep-1];
                                    //Resetea el Arreglo sub
                                    $totales=intotales($totales,4,"sub",0);
                                
                            //AGREGA TOTAL FINAL
                                        //Agrega un elemento a la matriz de Total
                                        $ep=$ep+1;
                                            $saldosoe["referencia1"][$ep]="Total";
                                            $saldosoe["referencia2"][$ep]="";
                                            $saldosoe["fecha"][$ep]=$saldosoe["fecha"][$ep-1];
                                            $saldosoe["idbodega"][$ep]=$saldosoe["idbodega"][$ep-1];                          
                                            $saldosoe["idcliente"][$ep]=$saldosoe["idcliente"][$ep-1]; 
                                            $saldosoe["idfabricante"][$ep]=$saldosoe["idfabricante"][$ep-1];
                                            $saldosoe["idmarca"][$ep]=$saldosoe["idmarca"][$ep-1];
                                            $saldosoe["idproducto"][$ep]=$saldosoe["idproducto"][$ep-1];
                                            $saldosoe["idloteproducto"][$ep]=$saldosoe["idloteproducto"][$ep-1];
                                            $saldosoe["idestadoproducto"][$ep]=$saldosoe["idestadoproducto"][$ep-1];
                                                $saldosoe["inicial"][$ep]=$totales["tot"][0];
                                                $saldosoe["retirada"][$ep]=$totales["tot"][1];
                                                $saldosoe["devuelta"][$ep]=$totales["tot"][2];
                                                $saldosoe["saldo"][$ep]=$totales["tot"][3];
                                            $saldosoe["idempleado"][$ep]=$saldosoe["idempleado"][$ep-1];
                                            $saldosoe["dias"][$ep]=$saldosoe["dias"][$ep-1];
                                    //Resetea el Arreglo sub
                                    $totales=intotales($totales,4,"sub",0);
                                    $ep=$ep+1;
                                }
                            

       //Consulta para eliminar registros Anteriores del Mismo Usuario
		$sqldelete="delete from reporte_saldosoe where idempleado=".$usuario;
                    //echo $sqldelete."<br><br>";
                $conexion->consultar($sqldelete); 

//Llenando Matriz de elementos en funcion de la consulta
		//Grabando Datos en Consulta para insertar
		$insert="Insert Into reporte_saldosoe
                    (referencia1,referencia2,fecha,idbodega,idcliente,idfabricante,idmarca,
                            idproducto,idloteproducto,idestadoproducto,inicial,retirada,devuelta,saldo,idempleado,dias) Values ";
        
		$limit=0; 
		$coma="";	
		$values="";
		for ($i = 1; $i <= $ep-1; $i++) {
			$values.="$coma(
                            '".$saldosoe["referencia1"][$i]."',
                            '".$saldosoe["referencia2"][$i]."',
                            '".$saldosoe["fecha"][$i]."',
                            '".$saldosoe["idbodega"][$i]."',
                            '".$saldosoe["idcliente"][$i]."',
                            '".$saldosoe["idfabricante"][$i]."',
                            '".$saldosoe["idmarca"][$i]."',
                            '".$saldosoe["idproducto"][$i]."',
                            '".$saldosoe["idloteproducto"][$i]."',
                            '".$saldosoe["idestadoproducto"][$i]."',
                            '".$saldosoe["inicial"][$i]."',
                            '".$saldosoe["retirada"][$i]."',
                            '".$saldosoe["devuelta"][$i]."',
                            '".$saldosoe["saldo"][$i]."',
                            '".$saldosoe["idempleado"][$i]."',
                            '".$saldosoe["dias"][$i]."'    
			)";
			$coma=",";
			//Afecta de 1000 en 1000 para que no falle la putada
			if($limit>=1000){
				echo 
				$conexion->consultar($insert." ".$values);
				$limit=0;
				
				$values="";
			}
			$limit++;
                        
		}

		
             
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
                //Recorre la matriz hasta que encuentra el valor
                for ($ibm = 1; $ibm <= $elementos-1; $ibm++) {
			if(
								$arreglo["idfabricante"][$ibm]==$idfabricante 
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
		return $arreglo;
        };
        
        
        
?>
