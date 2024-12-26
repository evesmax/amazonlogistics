<?php

                                
                                

    //Define fecha del dia y fecha del corte
        //Fecha de Corte
        $uw=strpos($_SESSION["sequel"],'where loe.fecha');

            $sfechacorte=substr($_SESSION["sequel"],$uw+18,10);   


            //Fecha de Corte
            $fecha = new DateTime($sfechacorte);
            $fechacorte = $fecha->format('Y-m-d');                   
            
            
            //Sustituye now() por sfechacorte;
            $sql=str_replace("now()","'$fechacorte 23:59:59'",$sql);

?>
