<?php

    //echo $sql."<br><br><br>";

    $uw=strpos($_SESSION["sequel"],'where');
    $uo=strpos($_SESSION["sequel"],'order by');
    $ct=strlen($_SESSION["sequel"]);
    $td=($ct-$uo)*-1;

    $sqlwhererend=substr($_SESSION["sequel"],$uw,$td);


    $sqlresumen="select of.nombrefabricante Ingenio,
					ip.nombreproducto Producto,
					format(sum(im.cantidadsecundaria),3) Producidas,
					format(b.cantidadtm,3) 'Estimado', format(b.cantidadtm-sum(im.cantidadsecundaria),3) 'Por Producir'
				from inventarios_movimientos im left join operaciones_fabricantes of on of.idfabricante=im.idfabricante
                    left join operaciones_fabricantes vm on vm.idfabricante=im.idmarca
                    left join operaciones_bodegas ob on ob.idbodega=im.idbodega
                    left join inventarios_productos ip on ip.idproducto=im.idproducto
                    left join inventarios_lotes il on il.idloteproducto=im.idloteproducto
                    left join inventarios_estados ie on ie.idestadoproducto=im.idestadoproducto
                    left join inventarios_tiposmovimiento tm on tm.idtipomovimiento=im.idtipomovimiento
                    left join inventarios_unidadesproductos up on up.idproducto=im.idproducto
                    left join inventarios_unidadesmedida um on um.idunidadmedida=up.idunidadmedida
					left join produccion_pronosticoszafra_titulo a on a.idloteproducto=il.idloteproducto
                    left join produccion_pronosticoszafra_detalle b on a.idpronostico=b.idpronostico
                        and b.idfabricante=of.idfabricante and b.idproducto=ip.idproducto
                ".$sqlwhererend." group by of.nombrefabricante, ip.nombreproducto, b.cantidadtm ";

	//echo $sqlresumen;

    $resultado = $conexion->consultar($sqlresumen);


    $descripcion="Resumen Reporte";

?>
                <CENTER>

                <table>
                    <tr>
                    <td align="center">
                        <font size="3" color="gray"><b><?php echo $descripcion."<br>" ?></b></font>
                    </tr>
                </table>

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

		$cantidad=0;
		$estimado=0;
		$saldof=0;
                while($rs = $conexion->siguiente($resultado)){
					$cantidad+=str_replace(',', '',$rs{"Producidas"});
					$estimado+=str_replace(',', '',$rs{"Estimado"});
					$saldof+=($estimado-$cantidad);
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

				$linea = "<tr class='trsubtotal'>
								<td class='tdsubtotal' colspan=2>Totales</td>
								<td class='tdsubtotal' title='Total'>".number_format($cantidad,3)."</td>
								<td class='tdsubtotal' title='Total'>".number_format($estimado,3)."</td>
								<td class='tdsubtotal' title='Total'>".number_format($estimado-$cantidad,3)."</td>
						</tr>";
                echo $linea;

				$conexion->cerrar_consulta($resultado);


?>
                </table>
                    </CENTER>
