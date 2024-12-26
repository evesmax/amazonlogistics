<?php
	
	include("../../netwarelog/catalog/conexionbd.php");

//RECUPERANDO VARIABLES
        session_start();
        $idordencompra=0;
        $idorg=0;
        $fechamysql="";
        
        $idorg=$_SESSION["accelog_idorganizacion"];
        $politicas=0;
        $msg="";
        $diferencia=$_GET["diferencia"];
        $idcliente=$_GET["idcliente"];
        $idordencompra=$_GET["idordencompra"];
        $fechamysql=date("Y-m-d H:i:s");
        $e=0;
        $fecha="";
        $deposito="";
        $saldo=0;
        $totcant1=0;
        $estilototalp=" readonly style='text-align:right;color:blue;background-color: #FFFFFF;border-width:0;font-size: 12px;'";
        $estilototaln=" readonly style='text-align:right;color:red;background-color: #FFFFFF;border-width:0;font-size: 12px;'";
        $idfabricante=0;
        //Obtiene fabricante de la orden de compra
        $sqlfab="SELECT idfabricante FROM ventas_ordenesdecompra WHERE idordencompra=$idordencompra";
        $result = $conexion->consultar($sqlfab);
        while($rs = $conexion->siguiente($result)){
            $idfabricante=$rs{"idfabricante"};
        }
        $conexion->cerrar_consulta($result);
        //Consulta detallado de saldos a fabor del cliente
        $sqlestatus="SELECT sc.idcliente,sc.fecha, sc.referenciadeposito deposito, sum(sc.saldo) saldo 
                    FROM ventas_saldosclientes sc 
                    WHERE sc.idcliente=$idcliente AND sc.saldo>0
                    AND idsaldocliente IN 
                    (SELECT idsaldocliente FROM ventas_saldosclientes_detalle WHERE foliodoctoorigen IN 
                    (SELECT idordencompra FROM ventas_ordenesdecompra WHERE idfabricante=$idfabricante))
                    GROUP BY sc.idcliente,sc.fecha,sc.referenciadeposito
                    Order By sc.fecha";
                
                //echo $sqlestatus;
                
                $tblresult="<table class='reporte' align=center>
                                <tr class='trencabezado'>
                                    <td>Cantidad Requerida:</td>
                                    <td>$ ".number_format($diferencia,3)."</td>
                                    <td>Diferencia:</td>
                                    <td><input $estilototaln type=text id='diferencia' name='diferencia' value=".number_format($diferencia,3)."></td> 
                                </tr>
                                <input type=hidden id='ndiferencia' name='ndiferencia' value='".$diferencia."'>
                                <tr class='trencabezado'>
                                    <td>Fecha</td><td>Deposito</td><td>A Favor Cliente</td><td>Aplicar</td>
                                <tr>";
        
                $result = $conexion->consultar($sqlestatus);
		while($rs = $conexion->siguiente($result)){
                    $fecha=$rs{"fecha"};
                    $deposito=$rs{"deposito"};
                    $saldo=$rs{"saldo"};
                    
                    $tblresult.="<tr  class='tdcontenido'>
                                    <td>$fecha</td>
                                    <td>".$deposito."</td>
                                    <td>$ ".number_format($saldo,3)."</td>
                                    <td><input type=text id='aplicar_$e' name='aplicar_$e' value=0 onChange='recalcula(".$e.")'></td>
                                 </tr>";
                    $e++;
                    $totcant1=$totcant1+$saldo;
		}
                $conexion->cerrar_consulta($result);                        
                
                $tblresult.="<input type=hidden id='elementos' name='elementos' value='".$e."'>
                             <input type=hidden id='idcliente' name='idcliente' value='".$idcliente."'>
                             <input type=hidden id='idordencompra' name='idordencompra' value='".$idordencompra."'>
                                <tr class='trsubtotal'>
                                <td colspan=2>Total</td>
                                <td>$ ".number_format($totcant1,3)."</td>
                                <td><input $estilototalp type=text id='aplicar_total' name='aplicar_total' value=0></td>
                                <input type=hidden id='totaldepositos' name='totaldepositos' value='".$totcant1."'>
                            </tr>
                        </table>";
                
        
//Envio a impresion de contenido
        
                $texto="<center>
                    <font color=red><b>$msg</b></font><br> 
                    <font color=black><b>
                    Capture las cantidades aplicar:
                        <br><br>
                        $tblresult
                        <br><br>
                    </b></font> 
                    </center>";
        
        

                
        
	$html="<html>";
	$html.= "<head>";
//Utiliza por omisión el estilo 1 del repolog
        $html=" <FORM id='proceso' name='proceso' method='post' action='completadepositos_grabar.php'>";
        $html.= "<LINK href='../../netwarelog/utilerias/css_repolog/estilo-1.css' title='estilo' rel='stylesheet' type='text/css' />";
	$html.= "<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>";
	$html.= "<meta name='author-icons' content='Rachel Fu'>";
	$html.= "<style>";
	$html.= "  body{font-size:0pt;color:white}";	
	$html.= "  td{font-size:8pt}";
	$html.= "</style>";
	$html.= "</head>";
	
	$html.= "<body style='font-family:helvetica'>";


        $html.= "<BODY style='font-family:helvetica'>
                                        <center style='border-style: none'>
                                            <div id='printer'>";
        

            $html.="<div class='imagen'>
                        <img src='../../netwarelog/utilerias/img_org/$idorg.png' width='200' height='50'>
                    </div><br>";
		//INICIA MEGATABLA
		$html.="<table width='100%'>";
                
		//ENCABEZADO
		$html.="<tr><td>"; //Mega tabla
                    $html.=$texto;
                $html.="</td></tr>"; //Mega tabla
                

               
                
                
                
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
                                                function recalcula(elementos){
                                                    var e=0,total=0, limite=0, diferencia=0;
                                                    e=elementos;
                                                    //alert(e);
                                                    for(x=0; x <= e; x = x+1){
                                                        total=total+document.getElementById('aplicar_'+x).value*1;
                                                    };
                                                    document.proceso.aplicar_total.value=format_number(total,3);
                                                    limite=format_number(document.getElementById('totaldepositos').value*1,3);
                                                    diferencia=format_number(document.getElementById('ndiferencia').value*1,3);
                                                    
                                                    document.proceso.diferencia.value=format_number(diferencia-total,3);


                                                    if(limite<total){
                                                        alert('no puede aplicar mas que el saldo disponible');
                                                        document.proceso.aplicar_total.value=0;
                                                        document.proceso.diferencia.value=format_number(diferencia,3);
                                                        for(x=0; x <= e; x = x+1){
                                                            document.proceso.elements['aplicar_'+x].value=0;
                                                        };
                                                    } 
                                                    
                                                }
                                            </script>";
		//Botones							
		$html_autorizar="<INPUT name='btngrabar' id='btngrabar' class='buttons_text' type='submit' value='Procesar' title='Haz Click Para Autorizar'>";
                $html_regresar= "<INPUT name=btnregresar type='button' onclick='redireccion()' value='Regresar'>";
                
                //Validacion de Politicas
                if($politicas==1){
                    $html_autorizar=""; //Elimina la funcionalidad
                }
                
                
                $opciones="
                <left>    
                <div id=opciones>
                    <table  width=100>
                        <td width=16 align=right>
                            <a href='javascript:printSelec();'><img src='../../netwarelog/repolog/img/impresora.png' border='0'></a>
                        </td>
                        <td width=16  align=right>
                                <a href='../../netwarelog/repolog/reporte.php'> <img src='../../netwarelog/repolog/img/filtros.png' 
                                        title ='Haga clic aqui para cambiar filtros...' border='0'> </a>
                        </td>
                    </table>
                </div><left>";
                
                
                
                //FIN MEGATABLA
		$html.=" </table>";
                $html.="$html_autorizar $html_regresar
                        </body></div>
                        </form>";
                $html.="</html>";
               
echo $opciones.$html;                
echo $html_funcionesjavascript;


?>