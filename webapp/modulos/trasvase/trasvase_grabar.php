<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);	
	
	include("../../netwarelog/catalog/conexionbd.php");

//Recuperando registros 
            $idtrasvase=$_REQUEST["txtidtrasvase"];
            $cantidaddestino1=$_REQUEST["txtcantidaddestino1"];
            $cantidaddestino2=$_REQUEST["txtcantidaddestino2"];
            $cantidadpnc1=$_REQUEST["txtcantidadpnc1"];
            $cantidadpnc2=$_REQUEST["txtcantidadpnc2"]; 
            $cantidadmerma1=$_REQUEST["txtcantidadmerma1"];
            $cantidadmerma2=$_REQUEST["txtcantidadmerma2"];
            $capturista=$_REQUEST["txtcapturista"];
                   

//Afecta Cantidades en Logistica_Trasvase
        $sqlafecta="UPDATE logistica_trasvase 
                        set cantidaddestinoreal1=$cantidaddestino1,
                            cantidaddestinoreal2=$cantidaddestino2,
                            cantidadpnc1=$cantidadpnc1,
                            cantidadpnc2=$cantidadpnc2,
                            cantidadmerma1=$cantidadmerma1,
                            cantidadmerma2=$cantidadmerma2,
                            idcapturista=$capturista,
                            idestadodocumento=2
                        Where idtraslado=".$idtrasvase;
        echo $sqlafecta;
        $conexion->consultar($sqlafecta);

         
?>