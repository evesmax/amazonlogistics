<?php


//Recibe filtros 

        $uw=strpos($_SESSION["sequel"],'idcierre');
        $uo=strpos($_SESSION["sequel"],'group');
        $ct=strlen($_SESSION["sequel"]);
        $td=($ct-$uo)*-1;
        $sqlwhere="And ".substr($_SESSION["sequel"],$uw,$td);

		//echo $sqlwhere."<br>";
//Elimina el filtro de cierre del sql principal        
		$sql=str_replace($sqlwhere,'',$sql);

//Genera el sql para las subconsultas
		$sql=str_replace('@cierre',$sqlwhere,$sql);
		
        $tablacomplemento="<table class='reporte'>
                            <tr class='trencabezado'>
                                <td>Concepto</td>
								<td>Total Zafras Anteriores</td>
								<td>Total Z-09/10</td><td>Total Z-10/11</td>
								<td>Total Z-11/12</td>
								<td>Total</td>
                            </tr>
                            <tr class='trcontenido'>
                                <td>Azucar Vendida Pendiente de Entregar</td>
                                <td>".regresa(1,1,$sqlwhere,$conexion)."</td>
                                <td>".regresa(1,2,$sqlwhere,$conexion)."</td>
                                <td>".regresa(1,3,$sqlwhere,$conexion)."</td>
                                <td>".regresa(1,4,$sqlwhere,$conexion)."</td>
                                <td>".regresa(1,5,$sqlwhere,$conexion)."</td>
                             </tr>  
                             <tr class='trcontenido'>
                                <td>Azucar Disponible Para Venta</td>
                                <td>".regresa(2,1,$sqlwhere,$conexion)."</td>
                                <td>".regresa(2,2,$sqlwhere,$conexion)."</td>
                                <td>".regresa(2,3,$sqlwhere,$conexion)."</td>
                                <td>".regresa(2,4,$sqlwhere,$conexion)."</td>
                                <td>".regresa(2,5,$sqlwhere,$conexion)."</td>
                             </tr>           
                             <tr class='trcontenido'>
                                <td>Azucar Certificada</td>
                                <td>".regresa(3,1,$sqlwhere,$conexion)."</td>
                                <td>".regresa(3,2,$sqlwhere,$conexion)."</td>
                                <td>".regresa(3,3,$sqlwhere,$conexion)."</td>
                                <td>".regresa(3,4,$sqlwhere,$conexion)."</td>
                                <td>".regresa(3,5,$sqlwhere,$conexion)."</td>
                             </tr>";

             //echo $tablacomplemento;   
                
                
            //Funciones de Arreglos
            function regresa($c,$f,$cierre,$conexionh){
                    //c=consulta, f=filtro
                    
                    switch ($f) {
                        case 1:
                            $filtro=" And idloteproducto IN (SELECT idloteproducto FROM inventarios_lotes WHERE agrupador='Zafras Anteriores') ";
                            break;
                        case 2:
                            $filtro=" And idloteproducto=6 "; //09/10
                            break;
                        case 3:
                            $filtro=" And idloteproducto=7 "; //10/11
                            break;
                        case 4: 
                            $filtro=" And idloteproducto=8 "; //11/12
                            break;
                        case 5: 
                            $filtro=" "; //Todas
                            break;                        
                    }
                
                
                    switch ($c) {
                        case 1:
                            $sqlc="SELECT ifnull(sum(comprometida),0) valor FROM cierre_saldos WHERE (idproducto<>3) $filtro $cierre";   //Comprometida
                            break;
                        case 2:
                            $sqlc="SELECT ifnull(sum(disponible),0) valor FROM cierre_saldos WHERE (idproducto<>3) $filtro $cierre";   //Disponible
                            break;
                        case 3:
                            $sqlc="SELECT ifnull(sum(certificados),0) valor FROM cierre_saldos WHERE (idproducto<>3) $filtro $cierre";   //Certificada
                            break;
                    }
                
                    $valor=0;

                    //Sustituye Variables
		            $resultado = $conexionh->consultar($sqlc);
                    while($rs = $conexionh->siguiente($resultado)){
                        $valor=number_format($rs{"valor"},3);
                    }
                    $conexionh->cerrar_consulta($resultado);

                return $valor;
            }  
                

?>
