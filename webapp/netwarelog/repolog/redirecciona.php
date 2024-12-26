<?php
    
    include("parametros.php");  
    

    //Redirecciona de manera manual cuando necesita algun reporte pasar algun proceso
    $reporte=0;
    $reporte=$_REQUEST["txtidreporte"];
        
//FECHA INICIAL        
        $diai=$_REQUEST["f1_3"];    //Dia
        $mesi=$_REQUEST["f1_2"];    //Mes
        $añoi=$_REQUEST["f1_1"];    //Año
        $finicial=$añoi."-".$mesi."-".$diai;
//FECHA FINAL        
        $diaf=$_REQUEST["f2_3"];    //Dia
        $mesf=$_REQUEST["f2_2"];    //Mes
        $añof=$_REQUEST["f2_1"];    //Año
        $ffinal=$añof."-".$mesf."-".$diaf;
    
     //FACTURACION 

 
	    if($reporte==12 or $reporte==13){
                $countos=0;
                if($reporte==13){
                    $aliascampo="re.idordenentrega";
                }elseif($reporte==12){
                    $aliascampo="re.idtraslado";
                }

                //Recupera Variables de seleccion
                $vacio=empty($_REQUEST['chk']);
                 if($vacio==""){ 
                    //$countos=0;
                    $whereordenes=" And (";
                    $first="";
                    foreach ($_REQUEST['chk'] as $checkbox){ 
                        $whereordenes.=$first.$aliascampo."=".$checkbox." ";
                        $first=" Or ";
                        $countos=$countos+1;
                    }
                    
                    $url="../../modulos/reportelogistica/detallado.php?sqlwhere=$whereordenes)&reporte=$reporte&finicial=$finicial&ffinal=$ffinal";
                    echo "Generando Datos...";
                        //REDIRECCIONA A PROCESO
                         ?>
                            <script>
                                var pagina='<?php echo $url?>';
                                document.location.href=pagina;
                            </script>
                        <?PHP   
                         
                }elseif($vacio==1){
                         ?>
                            <script>
                                alert("Es necesario seleccionar instrucciones :2");
                                javascript:history.back(1);
                            </script>
                        <?PHP   
                }
            }
	
   
  
    
?>
