<?php


include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];

//Recuperando Filtros y Formando Sqlwhere
            $uw=strpos($_SESSION["sequel"],'where');
            $uo=strpos($_SESSION["sequel"],'order');
            $ct=strlen($_SESSION["sequel"]);
            $td=($ct-$uo)*-1;
        $sqlwhere=substr($_SESSION["sequel"],$uw,$td);    
    //Extrayendo Fechas
            $uw=strpos($sqlwhere,'a.fecharesultado');
        $fechainicial=substr($sqlwhere,$uw+18,10);
        $fechainicial=str_replace('"', "",$fechainicial);   
            $uw=$uw+44;
        $fechafinal=substr($sqlwhere,$uw,11);
        $fechafinal=str_replace('"', "",$fechafinal);
        
        
        $uw=strpos($sqlwhere,'(a.idfabricante');        
        $sqlfabricante=substr($sqlwhere,$uw,100);        
    //Campos Variables
        
    $coma="";
    $res="";
    $p=1;
    $sqlcampos="";
    $pruebas=array("idprueba","nombre");
    
    $sqlpruebas="select b.idprueba,p.nombreprueba from calidad_configuracionpruebas a 
                    left join calidad_configuracionpruebas_detalle b on a.idconfiguracion=b.idconfiguracion 
                    left join calidad_pruebas p on p.idprueba=b.idprueba 
                Where $sqlfabricante ";
    
    
    $result = $conexion->consultar($sqlpruebas);
    while($rs = $conexion->siguiente($result)){
            //Campos para arreglo
            $res.="$coma res$p";
            //Matriz de Pruebas Variables
            $pruebas["idprueba"][$p]=$rs{"idprueba"};
            $pruebas["nombre"][$p]=$rs{"nombreprueba"};
                //Sql para sustituir de a principal
                $sqlcampos.="$coma format(res$p,3) '".$pruebas["nombre"][$p]."' ";
        $coma=",";        
        $p++;
    }
    $conexion->cerrar_consulta($result);
    //*** - Agrega Campos a Reporte
    $sql=str_replace("@calidad",$sqlcampos,$sql);

//*** - Declaracion de Arreglo
    $nd=0; //Numero de elementos arreglo    
    $datos=array("idfabricante","idloteproducto","idproducto","idestadoproducto","diazafra","fecharesultado","idturno","produccion",$res);
//*** - $Totales=array("sub","tot");
    $sqlproduccion=", ifnull((select sum(pe.cantidad2) from produccion_entradas pe 
                            where pe.idfabricante=a.idfabricante and pe.idloteproducto=a.idloteproducto 
                              and pe.idproducto=a.idproducto and pe.idestadoproducto=a.idestadoproducto
                              and pe.idturno=(case when b.horaresultado='01:00:00' then 1 else (case when b.horaresultado='02:00:00' then 2 else 3 end) end)
                              and pe.diazafra=b.diazafra),0) produccion ";
    //*** - Adaptacion del Where
    $sqlwhere=str_replace("a.diazafra","b.diazafra",$sqlwhere);
    $sqlwhere=str_replace("And (a.idempleado=$usuario)","",$sqlwhere);       
//*** - Genera datos al arreglo    
    $sqlresumen=" select a.idfabricante,a.idloteproducto, a.idproducto, a.idestadoproducto, date_format(a.fecharesultado,'%d-%m-%Y') fecharesultado,b.diazafra,
                    case when b.horaresultado='01:00:00' then 1 else (case when b.horaresultado='02:00:00' then 2 else 3 end) end idturno
                    ,pr.idprueba,b.resultado $sqlproduccion
                    from calidad_resultadospruebas a
                        left join calidad_resultadospruebas_detalle b on a.idresultadoprueba=b.idresultadoprueba
                        left join calidad_pruebas pr on pr.idprueba=b.idprueba
                        left join operaciones_fabricantes of on of.idfabricante=a.idfabricante
                        left join inventarios_lotes il on il.idloteproducto=a.idloteproducto
                        left join inventarios_productos ip on ip.idproducto=a.idproducto
                        left join inventarios_estados ep on ep.idestadoproducto=a.idestadoproducto
                        left join produccion_turnos pt on pt.idturno=case when b.horaresultado='01:00:00' then 1 else (case when b.horaresultado='02:00:00' then 2 else 3 end) end
    $sqlwhere and (not b.diazafra is null) order by of.nombrefabricante, b.diazafra, pt.nombreturno";
    
    //echo $sqlresumen;
    
    $idproducto=-1;
    $result = $conexion->consultar($sqlresumen);
    while($rs = $conexion->siguiente($result)){
        $idproducto=$rs{"idproducto"};
        $datos=acumuladatos($datos,$nd,$rs{"idfabricante"},$rs{"idloteproducto"},$rs{"idproducto"},$rs{"idestadoproducto"},$rs{"fecharesultado"},$rs{"diazafra"},$rs{"idturno"},$rs{"idprueba"},$rs{"resultado"},$rs{"produccion"});
    }
    $conexion->cerrar_consulta($result);
    
    
    
    //Agrega Totales
    $datos=agregatotales($datos,$nd);
    
    
    //Actualiza Datos para Grabarlos
                //Obtiene campos variables
                $rescampos="";
                for ($ibm = 1; $ibm <=$p-1; $ibm++){
                    $rescampos.=", res$ibm";
                }
                
    
    		$insert="Insert Into reporte_calidad
                    (idfabricante,idloteproducto,idproducto,fecharesultado,diazafra,
                    idturno,produccion,idestadoproducto,idempleado $rescampos) Values ";
                
		$values="";
		for ($i = 0; $i <= $nd-1; $i++) {
                    
                    //Obtiene resultados variables
                    $resvalores="";
                    for ($ibm = 1; $ibm <=$p-1; $ibm++){
                        $resvalores.=",'".$datos["res$ibm"][$i]."' ";
                    }

                   
                    $fechamysql=date("Y-m-d",strtotime($datos["fecharesultado"][$i]));
                    //echo $fechamysql."<br>";
                    
                    $values.="(
                                '".$datos["idfabricante"][$i]."',
                                '".$datos["idloteproducto"][$i]."',
                                '".$datos["idproducto"][$i]."',
                                '".$fechamysql."',
                                '".$datos["diazafra"][$i]."',
                                '".$datos["idturno"][$i]."',
                                '".$datos["produccion"][$i]."',
                                '".$datos["idestadoproducto"][$i]."',
                                '$usuario'
                                 $resvalores
                    ),";
                        
                        
		}
                             
		//Consulta para eliminar registros Anteriores del Mismo Usuario
		$sqldelete="delete from reporte_calidad where idempleado=".$usuario;
                    //echo $sqldelete."<br><br>";
                $conexion->consultar($sqldelete);
                
                //Agrega Valores Reporte
                $values=substr($values, 0, -1); //elimina la ultima coma
                    //echo $insert." ".$values."<br>";
		$conexion->consultar($insert." ".$values);
    
//    $pr="";
//    for ($i=0; $i<=$nd-1; $i++){
//        $pr=$datos["idfabricante"][$i]." | ".$datos["idloteproducto"][$i]." | ".$datos["idproducto"][$i]." | ".$datos["idestadoproducto"][$i]." | ".$datos["fecharesultado"][$i]." | ".$datos["diazafra"][$i]." | ".$datos["idturno"][$i]." | ".$datos["produccion"][$i];
//            for ($ibm = 1; $ibm <=$p-1; $ibm++){
//                $pr.=" |V$ibm| ".$datos["res$ibm"][$i];
//            }
//        echo $pr."<br>";
//    }
    
    
    
    
    
//Funciones PHP
        function acumuladatos($arreglo,$elementos,$idfabricante,$idloteproducto,$idproducto,$idestadoproducto,$fecharesutado,$diazafra,$idturno,$idprueba,$resultado,$produccion){
            global $nd,$p,$fechainicial;
            //Verifica si existe Registro en Arreglo
            $eb=buscar($arreglo,$nd,$idfabricante,$idloteproducto,$idproducto,$idestadoproducto,$fecharesutado,$diazafra,$idturno);
            if($eb==-1){
                //Si no existe lo agrega
                    $arreglo["idfabricante"][$elementos]=$idfabricante;
                    $arreglo["idloteproducto"][$elementos]=$idloteproducto;
                    $arreglo["idproducto"][$elementos]=$idproducto;
                    $arreglo["idestadoproducto"][$elementos]=$idestadoproducto;
                    $arreglo["fecharesultado"][$elementos]=$fecharesutado;
                    $arreglo["diazafra"][$elementos]=$diazafra;
                    $arreglo["idturno"][$elementos]=$idturno;
                    $arreglo["produccion"][$elementos]=$produccion;
                    
                    //echo "Fecha Agregada: ".$arreglo["fecharesultado"][$elementos]."<br>";
                    
                    //Inicializa valores
                    for ($j=1; $j<=$p-1; $j++){
                        $arreglo["res$j"][$elementos]=0; 
                    }
                   $pa=regresaprueba($idprueba);
                   $arreglo["res$pa"][$elementos]=$resultado;
                   
                   $nd++;
            }else{
                //Actualiza Valores   
                   $pa=regresaprueba($idprueba);
                   $arreglo["res$pa"][$eb]=$resultado;
                   
            }
            //echo "<br><b>$pa - $elementos ".$arreglo["res$pa"][$elementos]."</b></br>";
            //Actualiza Valor en campo variable y en acumulado
            
            return $arreglo;
        }
        
        function buscar($arreglo,$elementos,$idfabricante,$idloteproducto,$idproducto,$idestadoproducto,$fecharesutado,$diazafra,$idturno){
            global $nd;
            //Busca elementos en una matriz y si lo encuentra regresa el elemento en el que esta en caso de no encontrarlo regresa -1
            if ($elementos>=0){

                for ($ibm = 0; $ibm <=$nd-1; $ibm++) {
                    //echo "Entre: $ibm ".$arreglo["cliente"][$ibm]."<br>";
                    //echo "'".$arreglo["fecharesultado"][$ibm]."' == '".$fecharesutado."'<br>";
			if(
                                $arreglo["idfabricante"][$ibm]==$idfabricante
                                && $arreglo["idloteproducto"][$ibm]==$idloteproducto
                                && $arreglo["idproducto"][$ibm]==$idproducto
                                && $arreglo["idestadoproducto"][$ibm]==$idestadoproducto
                                && $arreglo["fecharesultado"][$ibm]==$fecharesutado
                                && $arreglo["diazafra"][$ibm]==$diazafra
                                && $arreglo["idturno"][$ibm]==$idturno){
                            return $ibm;
                            break;
                        }
		}	
            }   
            return -1;  
        }
    
        function agregatotales($arreglo,$elementos){
            global $nd,$p;
            //Inicializa registro
            if($nd>0){
                    $totproduccion=0;
                        $arreglo["idfabricante"][$nd]=$arreglo["idfabricante"][$nd-1];
                        $arreglo["idloteproducto"][$nd]=$arreglo["idloteproducto"][$nd-1];
                        $arreglo["idproducto"][$nd]=$arreglo["idproducto"][$nd-1];
                        $arreglo["idestadoproducto"][$nd]=$arreglo["idestadoproducto"][$nd-1];
                        $arreglo["fecharesultado"][$nd]=$arreglo["fecharesultado"][$nd-1];
                        $arreglo["diazafra"][$nd]=-1;
                        $arreglo["idturno"][$nd]=$arreglo["idturno"][$nd-1];
                        $arreglo["produccion"][$nd]=$arreglo["produccion"][$nd-1];
                        for ($j=1; $j<=$p-1; $j++){
                            $arreglo["res$j"][$nd]=0; 
                        }                    
                //Agrega Totales
                    for ($ibm = 0; $ibm<=$nd-1; $ibm++) {
                        for ($j=1; $j<=$p-1; $j++){
                            $arreglo["res$j"][$nd]+=($arreglo["res$j"][$ibm]*$arreglo["produccion"][$ibm]); 
                        }
                        $totproduccion+=$arreglo["produccion"][$ibm];
                    }    
                //Agrega Ponderados
                    if ($totproduccion>0){
                        for ($j=1; $j<=$p-1; $j++){
                            $arreglo["res$j"][$nd]=$arreglo["res$j"][$nd]/$totproduccion; 
                        } 
                    }
                    $arreglo["produccion"][$nd]=$totproduccion;
                $nd++;
            }
            return $arreglo;
        }
        
        function regresaprueba($idprueba){
            global $p,$pruebas;
            for ($ibm = 1; $ibm <=$p-1; $ibm++){
                if($pruebas["idprueba"][$ibm]==$idprueba){
                    //echo "<br>ENCONTRADO: ".$pruebas["nombre"][$ibm].": ".$pruebas["idprueba"][$ibm]." == ".$idprueba." <b>$ibm</b><br>";
                    return $ibm;
                    break;
                }
                
            }
            return -1;
        }
        
        
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
        
?>