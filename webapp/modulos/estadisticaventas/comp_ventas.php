<?php
    
    //echo $sql."<br><br><br>";

    $uw=strpos($_SESSION["sequel"],'where');
    $uo=strpos($_SESSION["sequel"],'order by');
    $ct=strlen($_SESSION["sequel"]);
    $td=($ct-$uo)*-1;
	
    $sqlwhererend=substr($_SESSION["sequel"],$uw,$td);
	
    
    $sqlresumen="select 'Totales' Totales, format(sum(Cantidadtm),3) 'Cantidad (TM)',format(sum(importeventa),2) 'ImporteVenta', format(sum(deposito),2) 'DepositoClientes'
					from reporte_estadisticaventas r
                ".$sqlwhererend;
				
	//echo $sqlresumen;
    
    $resultado = $conexion->consultar($sqlresumen);
    
    $descripcion="Resumen Reporte";

?>
                <CENTER>


                <table class="reporte">
                <tr class="trencabezado" >
                            <?php
                            $i=0;
                            while($i < mysql_num_fields($resultado)){
                                $meta = mysql_fetch_field($resultado, $i);
                                if(!$meta){
                                    echo "información no disponible";
                                } else {
                                    ?>
                                    <td><?php echo $meta->name; ?></td>
                                    <?php
                                }
                                $i++;
                            }
                            ?>
                </tr>
            <?php
		$valortotal="Sub Total";				
		
                while($rs = $conexion->siguiente($resultado)){					
		
									$linea="";
                                    $cambiaestilo=false;
						
                                    $i=0;
                                    while($i < mysql_num_fields($resultado)){
                                        $meta = mysql_fetch_field($resultado, $i);
                                        if(!$meta){		
                                            echo "información no disponible";
                                        } else {


                                            $d = $rs{$meta->name};
					    if(strtolower($d)=="sub total"||strtolower($d)=="subtotal"||strtolower($d)=="total"){
						$cambiaestilo=true;                                                
                                            }

                                            if(strpos($d,"TOTAL")!=false){
                                                $cambiaestilo=true;
                                            }

                                            $estilotd = "tdcontenido";
                                            if($cambiaestilo){
                                                $estilotd = "tdsubtotal";
                                            } else {

                                                $signopesos = strpos($rs{$meta->name},"$"); //echo "      --".$rs{$meta->name}."  ".$signopesos."--    ";
                                                if($signopesos !== false){
                                                    $estilotd = "tdmoneda";
                                                }

                                                $signoporcentaje = strpos($rs{$meta->name},"%");
                                                if($signoporcentaje !== false){
                                                    $estilotd = "tdmoneda";
                                                }

                                            }
                                            $linea.="<td class='".$estilotd."' title='".$meta->name."'>".$rs{$meta->name}."</td>";

                                        }
                                        $i++;
                                    }
						
                                    if($cambiaestilo){
                                            $linea = "<tr class='trsubtotal'>".$linea."</tr>";
                                    } else {
                                            $linea = "<tr class='trcontenido'>".$linea."</tr>";
                                    }
				    echo $linea;
                                    
                }
				
				
				$conexion->cerrar_consulta($resultado);


?>
                </table>
                    </CENTER>