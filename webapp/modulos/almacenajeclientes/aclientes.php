<?php
    

    include("../../netwarelog/webconfig.php");
    set_time_limit($tiempo_timeout);

    //Obtiene usuario
    $usuario=$_SESSION["accelog_idempleado"];

                                
    $pi=0;
    $pf=0;
    $sqlwhere="";
    $fechainicial="";
    $fechafinal="";
    $sqloe="";
    $salidas=array("fecha","cantidad");
    
    //Define rango de fecha
        $pi=strpos($_SESSION["sequel"],'where');
        $pf=strpos($_SESSION["sequel"],'group')-6;
            $sqlwhere=substr($_SESSION["sequel"],$pi,$pi-$pf);   

        $pi=strpos($sqlwhere,'BETWEEN')+9;
        
            $fechainicial=substr($sqlwhere,$pi,$pi-5);   
            $fechafinal=substr($sqlwhere,$pi+26,19);  


           
            
            
            //echo $fechainicial."<br>";
            //echo $fechafinal."<br>";
            
            $sqlsaldo="(loe.cantidad2-
                        (SELECT ifnull(sum(lor.cantidad2),0) FROM logistica_retiros lor WHERE loe.idordenentrega=lor.idordenentrega AND lor.idestadodocumento=1 AND lor.fechasalida<'$fechainicial' LIMIT 1)-
                        (SELECT ifnull(sum(c.cantidad2),0) FROM logistica_cancelacionordenesentrega c WHERE c.fechacancelacion<'$fechainicial' AND c.idordenentrega=loe.idordenentrega AND c.idestadodocumento=1))";
            
        
        //Inicial de OE'S    
        $sqloe="SELECT  loe.idordenentrega,
                    $sqlsaldo saldo,    
                    loe.cantidadretirada2, ifnull((SELECT costo FROM ventas_ordenesdecompra WHERE idordencompra=loe.idpedido),2) costo
                    FROM logistica_ordenesentrega loe 
                    WHERE loe.fecha<='$fechainicial' AND (loe.fechacancelacion<='$fechainicial' OR loe.fechacancelacion IS null)
                        AND $sqlsaldo>0";
		//echo $sqloe;
		 
        $coma="";
        $sqlvalores="";
        $resultado = $conexion->consultar($sqloe);
        while($rsOE = $conexion->siguiente($resultado)){
            $idordenentrega=$rsOE{"idordenentrega"};
            $saldoi=$rsOE{"saldo"};
            $costodia=$rsOE{"costo"};
            $ns=0;
            //Obtiene salidas del dia y los mete en arreglo
                $sqlsalidas="SELECT sum(lor.cantidad2) cantidad2, date_format(lor.fechasalida,'%Y-%m-%d') fecha FROM logistica_retiros lor 
                                WHERE lor.idordenentrega=$idordenentrega
                                    AND lor.fechasalida BETWEEN '$fechainicial' AND '$fechafinal'
									And lor.idestadodocumento<>4
                                GROUP BY date_format(lor.fechasalida,'%Y-%m-%d')
                                ORDER BY date_format(lor.fechasalida,'%Y-%m-%d')";
                //echo $sqlsalidas;
                $resultado2 = $conexion->consultar($sqlsalidas);
                while($rsSalidas = $conexion->siguiente($resultado2)){
                    $ns++;
                    $salidas["fecha"][$ns]=$rsSalidas{"fecha"};
                    $salidas["cantidad"][$ns]=$rsSalidas{"cantidad2"};
                    //echo $salidas["fecha"][$ns]." - ".$salidas["cantidad"][$ns]."<br>";
                }
                $conexion->cerrar_consulta($resultado2);                
            //Recorre Dias del Rango Por Cada OE
            $fechai=date("Y-m-d", strtotime( "$fechainicial"));
            while(strtotime($fechai)<=strtotime($fechafinal)) { 
			
                $agregaregistro=aplicacobro($idordenentrega,$fechai,$conexion);
                //Verifica si el dia cuenta o nell (Si esta e dias vencidos, si no esta cancelado, si no es dia de paro)
                if ($agregaregistro==1){ 
                    $totaldia=$saldoi*$costodia;
                    if($totaldia>0){
                        //Agrega Registro
                        $sqlvalores.="$coma ('$idordenentrega','$fechai','$saldoi','$costodia','$totaldia','$usuario')";
                        $coma=",";
                    }    
                }
			$salidasdia=buscaenarreglo($salidas,$ns,"fecha",$fechai,"cantidad");
			$saldoi=$saldoi-$salidasdia;
            //Incrementa Fecha
            $fechai = date("Y-m-d", strtotime( "$fechai + 1 DAY"));
            }  
            
        }
        $conexion->cerrar_consulta($resultado);
        
        //Elimina Valores Previos
        $sqleliminar="Delete From ventas_almacenajeclientes where idempleado='$usuario'";
        $conexion->consultar($sqleliminar);
        
        //Agrega Valores
        $sqlagregar="Insert Into ventas_almacenajeclientes (idordenentrega,fecha,saldo,costodia,totaldia,idempleado) 
                                                            Values $sqlvalores ";
        $conexion->consultar($sqlagregar);

        function aplicacobro($idordenentrega,$fecha,$conexionf){
		$valor=0;
                $idfabricante=0;
                $idbodega=0;
                //Verificando si esta vencio y el OE no esta Cancelado
                $sqlvalida="SELECT loe.idfabricante, loe.idbodega
                FROM logistica_ordenesentrega loe 
                WHERE loe.idordenentrega=$idordenentrega and 
                    date_format(ADDDATE(loe.fecha,ifnull((SELECT dias FROM ventas_ordenesdecompra WHERE idordencompra=loe.idpedido),30)),'%Y-%m-%d')<'$fecha'
                    and (loe.fechacancelacion>'$fecha 00:00:00' or loe.fechacancelacion is null)";
                
                //echo $sqlvalida."<br><br>";

                
                $resultado = $conexionf->consultar($sqlvalida);
                while($rs = $conexionf->siguiente($resultado)){
                    $valor=1;
                    $idfabricante=$rs{"idfabricante"};
                    $idbodega=$rs{"idbodega"};
                    
                }
                $conexionf->cerrar_consulta($resultado);   
                
                //echo "<br>RESULTADO: $valor <br>";
                //exit();
                
                
                //Verificando si no es dia de paro
                $sqlparo="SELECT idparo FROM logistica_paroingenios WHERE idfabricante=$idfabricante and idbodega=$idbodega and '$fecha' BETWEEN date_format(fechainicial,'%Y-%m-%d') AND date_format(fechafinal,'%Y-%m-%d')";
                //echo $sqlparo."<br><br>";
                
                $resultado = $conexionf->consultar($sqlparo);
                while($rs = $conexionf->siguiente($resultado)){
                    $valor=0;
                }
                $conexionf->cerrar_consulta($resultado);
                
                //echo "<br>RESULTADO: $valor <br>";
                
            return $valor;
	}        
        
        
        function buscaenarreglo($arreglo,$elementos,$campoarreglo,$valorbuscado,$campovalor){
		$valor=0;
                if ($elementos>0){
                    for ($ibm = 1; $ibm <= $elementos; $ibm++) {
                        if($arreglo[$campoarreglo][$ibm]==$valorbuscado){
                            $valor=$arreglo[$campovalor][$ibm];
                        }
                    }		
                }
		return $valor;
	}
        
        
        

?>
