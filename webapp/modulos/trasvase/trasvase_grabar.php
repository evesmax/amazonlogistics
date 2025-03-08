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
                   
            echo "idtrasvase".$idtrasvase;
       
?>