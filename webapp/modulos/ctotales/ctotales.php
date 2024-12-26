<?php

$rep=$_SESSION['repolog_idreporte'];
if($rep==3){
                //Proceso Include Antes
                $i=10; //Campos
                $mtotales=1; //Para que los agrege
                $ctotales=array("mostrar","decimales","valordefecto");
                    $ctotales["mostrar"][0]=0;
                    $ctotales["decimales"][0]=0;
                    $ctotales["valordefecto"][0]="Totales";
                   
                    for ($z=1; $z<=$i; $z++){
                        $ctotales["mostrar"][$z]=0;
                        $ctotales["decimales"][$z]=0;
                        $ctotales["valordefecto"][$z]="";
                    }
                    
                    //Totales
                        $ctotales["mostrar"][9]=1;
                        $ctotales["decimales"][9]=2;
                        $ctotales["valordefecto"][9]="";

                        $ctotales["mostrar"][10]=1;
                        $ctotales["decimales"][10]=3;
                        $ctotales["valordefecto"][10]="";   
}
//DetalladoMovimientos(Entradas por Traslado)
if($rep==33){
                //Proceso Include Antes
                $i=24; //Campos
                $mtotales=1; //Para que los agrege
                $ctotales=array("mostrar","decimales","valordefecto");
                    $ctotales["mostrar"][0]=0;
                    $ctotales["decimales"][0]=0;
                    $ctotales["valordefecto"][0]="Totales";
                   
                    for ($z=1; $z<=$i; $z++){
                        $ctotales["mostrar"][$z]=0;
                        $ctotales["decimales"][$z]=0;
                        $ctotales["valordefecto"][$z]="";
                    }
                    
                    //Totales
                        $ctotales["mostrar"][20]=1;
                        $ctotales["decimales"][20]=2;
                        $ctotales["valordefecto"][20]="";

                        $ctotales["mostrar"][21]=1;
                        $ctotales["decimales"][21]=3;
                        $ctotales["valordefecto"][21]="";   

                        $ctotales["mostrar"][22]=1;
                        $ctotales["decimales"][22]=2;
                        $ctotales["valordefecto"][22]="";

                        $ctotales["mostrar"][23]=1;
                        $ctotales["decimales"][23]=3;
                        $ctotales["valordefecto"][23]="";                     

}
//DetalladoMovimientos(Entradas por Traslado)
if($rep==41){
                //Proceso Include Antes
                $i=22; //Campos
                $mtotales=1; //Para que los agrege
                $ctotales=array("mostrar","decimales","valordefecto");
                    $ctotales["mostrar"][0]=0;
                    $ctotales["decimales"][0]=0;
                    $ctotales["valordefecto"][0]="Totales";
                   
                    for ($z=1; $z<=$i; $z++){
                        $ctotales["mostrar"][$z]=0;
                        $ctotales["decimales"][$z]=0;
                        $ctotales["valordefecto"][$z]="";
                    }
                    
                    //Totales
                        $ctotales["mostrar"][19]=1;
                        $ctotales["decimales"][19]=2;
                        $ctotales["valordefecto"][19]="";

                        $ctotales["mostrar"][20]=1;
                        $ctotales["decimales"][20]=3;
                        $ctotales["valordefecto"][20]="";   


}
//Detallado Movimientos (Salidas por Venta)
if($rep==34){
                //Proceso Include Antes
                $i=16; //Campos
                $mtotales=1; //Para que los agrege
                $ctotales=array("mostrar","decimales","valordefecto");
                    $ctotales["mostrar"][0]=0;
                    $ctotales["decimales"][0]=0;
                    $ctotales["valordefecto"][0]="Totales";
                   
                    for ($z=1; $z<=$i; $z++){
                        $ctotales["mostrar"][$z]=0;
                        $ctotales["decimales"][$z]=0;
                        $ctotales["valordefecto"][$z]="";
                    }
                    
                    //Totales
                        $ctotales["mostrar"][10]=1;
                        $ctotales["decimales"][10]=2;
                        $ctotales["valordefecto"][10]="";

                        $ctotales["mostrar"][11]=1;
                        $ctotales["decimales"][11]=3;
                        $ctotales["valordefecto"][11]="";   

}
?>
