<?php // include("bd.php");

        $datosexcel= "";
        $idresultadoprueba=$catalog_id_utilizado;
        
        $resultados=array("idresultadoprueba","diazafra","horaresultado","idprueba","resultado");
     
            //Consulta Titulo
		$sQuery = "Select * From calidad_resultadospruebas where idresultadoprueba=".$idresultadoprueba;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                    $datosexcel=$rs{"datosprueba"};
		}
		$conexion->cerrar_consulta($result);   
                //Genera Matriz
                $datosrecibidos = preg_split("/[\s,]+/", $datosexcel);
                $pruebas=array("idprueba","nombre");
                $np=0;  //Numeros de Prueba
                $emp=0; //Elementos matriz principal
                $titulosconcluidos=0;
                
                
                //Detectando Pruebas
                for ($i = 2; $i < count($datosrecibidos); $i++){                
                    if(((strpos($datosrecibidos[$i],":"))))
                        break;
                            //Agregando Clave de Prueba
                            $pruebas["idprueba"][$np]=$datosrecibidos[$i];
                            $i++;
                            $np++;
                   if(((strpos($datosrecibidos[$i],":"))))
                        break;  
                }
                
                
                $np=$np-1; //Ajusta la variable del numero de pruebas                
                $diazafra="";
                $horaresultado=0;
                $emp=0;
                
                //Repite esto hasta que se terminen los valores de la matriz
                $va=$i-1;   //Ajusta la variable del inicio de los valores
                while(count($datosrecibidos)>$va){        
                        for ($j=0; $j<$np+2; $j++){
                            if($j==0 or $j==1){
                                if($j==0)
                                    $diazafra=$datosrecibidos[$va];
                                if($j==1)
                                    
                                    if(!isset($datosrecibidos[$va])){
                                        $j=$np+3;
                                        $np=$np-1;
                                    }else{
                                        $horaresultado=$datosrecibidos[$va];
                                    }
                            }else{
                                $resultados["idresultadoprueba"][$emp]=$idresultadoprueba;
                                $resultados["diazafra"][$emp]=$diazafra;
                                $resultados["horaresultado"][$emp]=$horaresultado;
                                $resultados["idprueba"][$emp]=$pruebas["idprueba"][$j-2];
                                $resultados["resultado"][$emp]=$datosrecibidos[$va];
                                $emp++;
                            }
                            $va++;
                        }
                }

                
                $sqlinsert="Insert Into calidad_resultadospruebas_detalle (idresultadoprueba, diazafra, horaresultado, idprueba, resultado) Values ";
                $sqlvalues="";
                
                $ini="(";
                for ($j=0; $j<$emp; $j++){         
                    if(!($resultados["resultado"][$j]=="-")){
                        $sqlvalues.=$ini."'".$resultados["idresultadoprueba"][$j]."',";
                        $sqlvalues.="'".$resultados["diazafra"][$j]."',";
                        $sqlvalues.="'".$resultados["horaresultado"][$j]."',";
                        $sqlvalues.="'".$resultados["idprueba"][$j]."',";
                        $sqlvalues.="'".$resultados["resultado"][$j]."')";
                        $ini=",(";
                    }
                }
                 
                //Elimina los registros anteriores
                $sqldelete="Delete From calidad_resultadospruebas_detalle where idresultadoprueba=".$idresultadoprueba;
                //echo $sqldelete;
                $conexion->consultar($sqldelete);

                //Agrega los nuevos resultados
                echo $sqlinsert." ".$sqlvalues;
                $conexion->consultar($sqlinsert." ".$sqlvalues);
                
                echo "<br>Se Actualizaron en el sistema: <b>".$emp."</b> Resultados.<br>"
                
                
                
                

?>