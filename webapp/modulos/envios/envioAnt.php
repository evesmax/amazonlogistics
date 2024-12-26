<?php 

		include("bd.php");		

		
		//Recupera la referencia del traslado
                $idtraslado=$_GET["folio"];
					
                //Reinicia Variables
                    $otfc="";
                    $fecha=date("d-m-Y g:i:s");
                    $nombreingenio="";
                    $idfabricante="";
                    $bodegaorigen="";
                    $idbodegaorigen="";
                    $bodegadestino="";
                    $zafra="";
                    $nombreproducto="";
                    $nombreestado="";
                    $saldoinicial=0;
                    $retirada=0;
                    $recibida=0;
                    $saldo=0;
                    $tipoimagen="";
                    $transportista="";
                    $fechaotfc="";

                //$html_titulo Lenando Variables de Titulo de la Liquidacion
		$sqlestatus="Select lt.referencia1 otfc, lt.fecha,  
                                of.nombrefabricante 'nombreingenio', obo.nombrebodega 'bodegaorigen',
                                obd.nombrebodega 'bodegadestino', il.descripcionlote 'zafra', 
                                ip.nombreproducto 'producto', ie.descripcionestado 'estado', 
                                format(lt.cantidad2,3) 'saldoinicial', format(IFNULL(lt.cantidadretirada2,0),3) 'retirada'
                                ,format(IFNULL(lt.cantidadrecibida2,0),3) 'recibida', 
                                format(lt.cantidad2-IFNULL(lt.cantidadretirada2,0),3) 'saldo', 
                                    case when obo.idbodega in (select idbodega from relaciones_almacenadoras_bodegas t 
                                        inner join relaciones_almacenadoras_bodegas_detalle d on t.idalmacenadorabodega=d.idalmacenadorabodega 
                                        where idbodega=lt.idbodegaorigen) then 'a' else 'i' end 'logo', ot.razonsocial transportista,
                                of.idfabricante, obo.idbodega
                             From logistica_traslados lt 
                                inner join operaciones_fabricantes of on of.idfabricante=lt.idfabricante
                                inner join operaciones_bodegas obo on obo.idbodega=lt.idbodegaorigen
                                inner join operaciones_bodegas obd on obd.idbodega=lt.idbodegadestino
                                inner join inventarios_productos ip on ip.idproducto=lt.idproducto
                                inner join  inventarios_estados ie on ie.idestadoproducto=lt.idestadoproducto
                                inner join inventarios_lotes il on il.idloteproducto=lt.idloteproducto
                                inner join operaciones_transportistas ot on ot.idtransportista=lt.idtransportista 
                             Where lt.idtraslado=".$idtraslado;
		//echo $sqlestatus."<br><br><br>";
		$result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                                    $otfc=$rs{"otfc"};
                                    $fechaotfc=$rs{"fecha"}; 
                                    $nombreingenio=$rs{"nombreingenio"};
                                    $idfabricante=$rs{"idfabricante"};
                                    $bodegaorigen=$rs{"bodegaorigen"};
                                    $idbodegaorigen=$rs{"idbodega"};
                                    $bodegadestino= $rs{"bodegadestino"};
                                    $zafra= $rs{"zafra"};
                                    $nombreproducto= $rs{"producto"};
                                    $nombreestado= $rs{"estado"};
                                    $saldoinicial= $rs{"saldoinicial"};
                                    $retirada= $rs{"retirada"};
                                    $recibida= $rs{"recibida"};
                                    $saldo=$rs{"saldo"};
                                    $tipoimagen=$rs{"logo"};
                                    $transportista=$rs{"transportista"};
		}
		$conexion->cerrar_consulta($result);                        
                        
                
                $sqlimagen="";
                $carpeta="";
                $imgtitulo="<center>";
                if($tipoimagen=="i"){                        
                        $sqlimagen="select logotipo, nombrefabricante nombre from operaciones_fabricantes where idfabricante=".$idfabricante;
                        $carpeta="../../netwarelog/archivos/1/operaciones_fabricantes/";

                }elseif($tipoimagen=="a"){
                        $sqlimagen="select a.logotipo, a.nombrealmacenadora nombre from relaciones_almacenadoras_bodegas t 
                                        inner join relaciones_almacenadoras_bodegas_detalle d on 
                                            t.idalmacenadorabodega=d.idalmacenadorabodega 
                                        inner join operaciones_almacenadoras a on a.idalmacenadora=t.idalmacenadora
                                        where idbodega=".$idbodegaorigen;
                        $carpeta="../../netwarelog/archivos/1/operaciones_almacenadoras/";
                }
                //Obtiene Nombre de la Imagen
                $result = $conexion->consultar($sqlimagen);
                while($rs = $conexion->siguiente($result)){
                        $logotipo=$rs{"logotipo"};
                        $nombre=$rs{"nombre"};
                }
                $conexion->cerrar_consulta($result);  
                
                //Si existe la imagen la dibuja
                if(is_dir($carpeta.$logotipo)){
                    $imgtitulo.="";
                }else{
                    $imgtitulo.="<img src='".$carpeta.$logotipo."' width=150><br>";
                }
                $imgtitulo.="<strong>".$nombre."</strong>"."</center>";
                
		//Inicia Tabla
                $html_titulo="";
                $html_titulo.=$imgtitulo;
                
                $html_titulo.="<center><font color=silver>Remision Envio Producto</font>
                                <table width=800  class='reporte'> 
                                    <tr class='trencabezado'> 
                                        <td>Folio Documento</td>
                                        <td>Folio Interno Bodega</td>
                                        <td>OTFC</td>
                                        <td>Inicial (TM)</td>
                                        <td>Retirado (TM)</td>
                                        <td>Recibido (TM)</td>
                                        <td>Saldo (TM)</td>
                                        <td>Fecha</td>
                                    </tr> 
                                    <tr class='trcontenido'>
                                        <td><font color=silver>Nuevo</font></td>
                                        <td><font color=silver>Nuevo</font></td>
                                        <td>".$otfc."</td>
                                        <td>".$saldoinicial."</td>
                                        <td>".$retirada."</td>
                                        <td>".$recibida."</td>
                                        <td>".$saldo."</td>
                                        <td><input type=text id=fecha value='".$fecha."'></td>
                                    </tr>
                                </table></center>";
		$html_titulo.="</tbody> 
                                </table>";
                
                $html_titulo.="<center>
                                <table width=800  class='reporte'> 
                                    <tr class='trencabezado'> 
                                        <td>Ingenio</td>
                                        <td>Bodega Origen</td>
                                        <td>Bodega Destino</td>
                                        <td>Transportista Asignado</td>
                                    </tr> 
                                    <tr class='trcontenido'>
                                        <td>".$nombreingenio."</td>
                                        <td>".$bodegaorigen."</td>
                                        <td>".$bodegadestino."</td>
                                        <td>".$transportista."</td>
                                    </tr>
                                </table></center>";
		$html_titulo.="</tbody> 
                                </table>";

                $html_titulo.="<center>
                        <table width=800  class='reporte'> 
                            <tr class='trencabezado'> 
                                <td colspan=2>Datos Transporte</td><td colspan=2>Datos Bodega</td></tr> 
                                    <tr><table width=400  class='reporte'> 
                                        <tr class='trcontenido'> 
                                            <td><strong>Operador:</strong><input type=text id=operador value=''></td>
                                        </tr>
                                        <tr class='trcontenido'> 
                                            <td><strong>C.Porte:</strong><input type=text id=operador value=''></td>
                                        </tr> 
                                        <tr class='trcontenido'> 
                                            <td><strong>Placas Vehiculo:</strong><input type=text id=operador value=''></td>
                                        </tr>
                                        <tr class='trcontenido'> 
                                            <td><strong>Licencia Chofer:</strong><input type=text id=operador value=''></td>
                                        </tr>                                    
                                    </table>
                                    </tr>
                        </table></center>";
                
                $html_titulo.="";
                
                $html_titulo.="<table width=400  class='reporte'> 
                                    <tr class='trcontenido'> 
                                        <td><strong>Operador:</strong><input type=text id=operador value=''></td>
                                    </tr>
                                    <tr class='trcontenido'> 
                                        <td><strong>C.Porte:</strong><input type=text id=operador value=''></td>
                                    </tr> 
                                    <tr class='trcontenido'> 
                                        <td><strong>Placas Vehiculo:</strong><input type=text id=operador value=''></td>
                                    </tr>
                                    <tr class='trcontenido'> 
                                        <td><strong>Licencia Chofer:</strong><input type=text id=operador value=''></td>
                                    </tr>                                    
                                </table>";
                
		$html_titulo.="</tbody> 
                                </table>";
                
                $html_seccion1="";
                      
		//funciones javascript		
		$html_funcionesjavascript=" <script type='text/javascript'>
										function redireccion() {
											var pagina = '../../netwarelog/repolog/reporte.php';
											document.location.href=pagina;
										}
                                                                                function valor(num) {
                                                                                    var numerostring='',numero=0;
                                                                                    numero=num.replace(/,/,'');
                                                                                    return numero;
                                                                                }
                                                                                function format_number(pnumber,decimals){
                                                                                    if (isNaN(pnumber)) { return 0};
                                                                                    if (pnumber=='') { return 0};
                                                                                    var snum = new String(pnumber);
                                                                                    var sec = snum.split('.');
                                                                                    var whole = parseFloat(sec[0]);
                                                                                    var result = '';
                                                                                    if(sec.length > 1){
                                                                                        var dec = new String(sec[1]);
                                                                                        dec = String(parseFloat(sec[1])/Math.pow(10,(dec.length - decimals)));
                                                                                        dec = String(whole + Math.round(parseFloat(dec))/Math.pow(10,decimals));
                                                                                        var dot = dec.indexOf('.');
                                                                                        if(dot == -1){
                                                                                            dec += '.';
                                                                                            dot = dec.indexOf('.');
                                                                                        }
                                                                                        while(dec.length <= dot + decimals) { dec += '0'; }
                                                                                        result = dec;
                                                                                    } else{
                                                                                        var dot;
                                                                                        var dec = new String(whole);
                                                                                        dec += '.';
                                                                                        dot = dec.indexOf('.');
                                                                                        while(dec.length <= dot + decimals) { dec += '0'; }
                                                                                        result = dec;
                                                                                    }
                                                                                    return result;
                                                                                }
                                                                                function recalcula() {
                                                                                    alert('Hola');
                                                                                }
									</script>";
		//Botones							
		$html_botones="	<INPUT name='btngrabar' class='buttons_text' type='submit' value='Procesar' title='Haz Click Para Autorizar'>
                                <INPUT name=btnregresar type='button' onclick='redireccion()' value='Regresar'>";
						
//print_r($aDel);
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="imagetoolbar" content="no" />

        <link href="../../netwarelog/utilerias/css_repolog/estilo-1.css" title="estilo" rel="stylesheet" type="text/css" />

</head>
<body>                
        <FORM id="envio" name="envio"  method="post" action="envio_grabar.php">
        <center><div id="contenidos"></div>
				<div id="content">
                                <?php
                                    //Imprime Codigo Variable y envio parametros
                                    //echo "<INPUT id=txtidoperador type=hidden name=txtidoperador value='".$idoperador."'>";
                                                                        
                                                                        echo $html_titulo;
                                                                        echo $html_seccion1;
                                                                        
									echo $html_funcionesjavascript."<br>";
									echo $html_botones;
                                ?>
		</div></center>
		</form>
</body>
</html>
