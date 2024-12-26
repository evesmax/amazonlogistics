<?php
                $ndn=0;
                $datosnormas=array("norma","etiqueta","valormaximo","valorminimo","idprueba","nombreprueba","tmcumplen","tmnocumplen","tmporcentaje");
                
                $sqlpruebas="select cn.norma,b.etiqueta, b.valormaximo, b.valorminimo, b.idprueba, cp.nombreprueba  from calidad_productoconforme a 
                                left join calidad_productoconforme_detalle b on a.idpc=b.idpc
                                left join calidad_normas cn on cn.idnorma=a.idnorma
                                left join calidad_pruebas cp on cp.idprueba=b.idprueba
                            Where a.idproducto=$idproducto";

                $result = $conexion->consultar($sqlpruebas);
                while($rs = $conexion->siguiente($result)){
                    //Agregar a la matriz
                    $datosnormas["norma"][$ndn]=$rs{"norma"};
                    $datosnormas["etiqueta"][$ndn]=$rs{"etiqueta"};
                    $datosnormas["valormaximo"][$ndn]=$rs{"valormaximo"};
                    $datosnormas["valorminimo"][$ndn]=$rs{"valorminimo"};
                    $datosnormas["idprueba"][$ndn]=$rs{"idprueba"};
                    $datosnormas["nombreprueba"][$ndn]=$rs{"nombreprueba"};
                    $datosnormas["tmcumplen"][$ndn]=0;
                    $datosnormas["tmnocumplen"][$ndn]=0;
                    $datosnormas["tmporcentaje"][$ndn]=0;
                    $ndn++;
                }
                $conexion->cerrar_consulta($result);
                
                //Actualiza Tonelajes de Normas
                for ($in = 0; $in <= $ndn-1; $in++) {
                    $datosnormas=calculanormas($datosnormas,$in);
                }
                
                
                
            //Escribe Titulo Principal    
            $htmlnormas="<center><table class='reporte' widht=100% align=center>";
            $htmlnormas.="<tr class='trencabezado'>
                            <td colspan=$p>Comparativo con Normas Asociadas al producto</td>
                        </tr>";
            
                $normaant="";
                $cuantos=0;
                //Imprime Normas
                for ($i = 0; $i <=$ndn-1; $i++) {
                    if ($normaant==$datosnormas["norma"][$i]) {
                        //Acumula valores en los correspondientes
                        $cuantos++;
                    }else{
                        //Dubuja Norma
                            $htmlnormas.="<tr class='trsubtotal'>";
                                $htmlpruebas="<td>".$datosnormas["norma"][$i]."</td>";
                                for ($j = 0; $j <=$ndn-1; $j++){
                                    if($datosnormas["norma"][$j]==$datosnormas["norma"][$i]){
                                        $htmlpruebas.="<td>".$datosnormas["nombreprueba"][$j]."</td>";
                                    }
                                }
                            $htmlnormas.="$htmlpruebas</tr>"; 

                        //Dubuja Etiquetas
                            $htmlnormas.="<tr class='trcontenido'>";
                                $htmlpruebas="<td>Parametros</td>";
                                for ($j = 0; $j <=$ndn-1; $j++){
                                    if($datosnormas["norma"][$j]==$datosnormas["norma"][$i]){
                                        $htmlpruebas.="<td>".$datosnormas["etiqueta"][$j]."</td>";
                                    }
                                }
                            $htmlnormas.="$htmlpruebas</tr>";                             
                            
                        //Dibuja Valores TM Cumplen
                            $htmlnormas.="<tr class='trcontenido'>";
                                $htmlpruebas="<td>TM Cumplen</td>";
                                for ($j = 0; $j <=$ndn-1; $j++){
                                    if($datosnormas["norma"][$j]==$datosnormas["norma"][$i]){
                                        $htmlpruebas.="<td>".number_format($datosnormas["tmcumplen"][$j],2)."</td>";
                                    }
                                }
                            $htmlnormas.="$htmlpruebas</tr>"; 
                            
                       //Dibuja Valores TM No Cumplen
                            $htmlnormas.="<tr class='trcontenido'>";
                                $htmlpruebas="<td>TM No Cumplen</td>";
                                for ($j = 0; $j <=$ndn-1; $j++){
                                    if($datosnormas["norma"][$j]==$datosnormas["norma"][$i]){
                                        $htmlpruebas.="<td>".number_format($datosnormas["tmnocumplen"][$j],2)."</td>";
                                    }
                                }
                            $htmlnormas.="$htmlpruebas</tr>";     
                            
                       //Dibuja Valores % TM Cumplen
                            $htmlnormas.="<tr class='trcontenido'>";
                                $htmlpruebas="<td>% No cumple</td>";
                                $tmtotales=0;
                                $tmnocumplen=0;
                                for ($j = 0; $j <=$ndn-1; $j++){
                                    if($datosnormas["norma"][$j]==$datosnormas["norma"][$i]){
                                        $tmtotales=($datosnormas["tmnocumplen"][$j]*1)+($datosnormas["tmcumplen"][$j]*1);
                                        $tmnocumplen=($datosnormas["tmnocumplen"][$j]*1);
                                        if($tmtotales==0) $tmtotales=1;
                                        //echo "$tmtotales / $tmnocumplen <br>";
                                        $htmlpruebas.="<td>".number_format($tmnocumplen/$tmtotales*100,2)."%</td>";
                                    }
                                }
                            $htmlnormas.="$htmlpruebas</tr>"; 
                            
                            
                        }

                    $normaant=$datosnormas["norma"][$i];
                }
                
            $htmlnormas.="</table></center>";
                
            
            echo $htmlnormas;
                
//_-_-_-_-FUNCIONES PHP_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-                
       
        function calculanormas($arreglo,$elemento){
            global $nd,$p,$ndn,$datos,$pruebas;
            //Inicializa registro
            $tmcumplen=0;
            $tmnocumplen=0;
            $r=-1;
            //Ubica Prueba en res
            for ($in = 1; $in <= $p-1; $in++) {
                //echo $pruebas["idprueba"][$in]."==".$arreglo["idprueba"][$elemento]."<br>";
                if ($pruebas["idprueba"][$in]==$arreglo["idprueba"][$elemento]){
                    $r=$in;
                    break;
                }
            }
            
            if($r>0){
                //Recorre matriz datos, determina y suma los valores que cumplen y los que no
                for ($i = 0; $i <=$nd-2; $i++) {
                    //Si cumple con la norma suma en tmcumplen
                    if((($datos["res$r"][$i]*1)<=($arreglo["valormaximo"][$elemento]*1)) && (($datos["res$r"][$i]*1)>=($arreglo["valorminimo"][$elemento]*1))){
                        $arreglo["tmcumplen"][$elemento]+=$datos["produccion"][$i];
                    }else{
                        $arreglo["tmnocumplen"][$elemento]+=$datos["produccion"][$i];
                    }
                }
            }
            
            return $arreglo;
        }                
                
                
?>