<?php

include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);
//Obtiene usuario
$usuario=$_SESSION["accelog_idempleado"];


//Recupera Filtros
        //Obtiene Where
        $uw=strpos($_SESSION["sequel"],'where');
        $uo=strpos($_SESSION["sequel"],'and (ik.fecha');
        $ct=strlen($_SESSION["sequel"]);
        $td=($ct-$uo)*-1;
        $sqlwhere=substr($_SESSION["sequel"],$uw,$td);

    //Define fecha del dia y fecha del corte
        //Fecha de Corte
        $uw=strpos($_SESSION["sequel"],'re.fecha');
            //echo $uw."<br><br>";
        $uo=strpos($_SESSION["sequel"],'re.idempleado');
            //echo $uo."<br><br>";

        $ct=strlen($_SESSION["sequel"]); //Ancho Cadena Total
        $td=($ct-($uo-8))*-1;
            $sfechainicio=substr($_SESSION["sequel"],$uw+10,$td+8);
            $sfechafin=substr($_SESSION["sequel"],$uw+15,$td);

            //echo $sfechacorte;

        //Fecha de Corte
            //$fecha = new DateTime($sfechacorte);
            //$fechacorte = $fecha->format('Y-m-d');

        //Fecha del Dia
            //$sfechadia =$fecha=date("Y-m-d");

            //$fecha = new DateTime($sfechadia);
            //$fechadia = $fecha->format('Y-m-d');

            echo "<br> Desde Linea 39 de kardex.php <br>";
            echo "Fecha Inicio: ".$sfechainicio."<br>";
            echo "Fecha".$sfechafin."<br>";
            exit();
//SQL'S ___

        /*
        $sqlfechacorte=" And (re.fecha<='".$fechacorte." 23:59:59') "; //El movimiento del ultimo segundo
        $sqlclaves="";
        $resultado = $conexion->consultar($sqlclaves);
        while($rs = $conexion->siguiente($resultado)){
            $ingenio=$rs{"idfabricante"};
        
        }
        $conexion->cerrar_consulta($resultado);
        */

        //LLamar SP
        $sqlsp="call generaKardex('2025-01-01 23:59:59','2025-02-16 23:59:59',3,16,NULL,NULL,NULL,NULL,1);";
        $resultado = $conexion->consultar($sqlsp);

        

?>
