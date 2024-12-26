<?php 

//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL
global $api_lite;
if(!isset($api_lite)){
	if(!isset($_REQUEST["netwarstore"])) require("models/connection_sqli_manual.php"); // funciones mySQLi
	else require("../webapp/modulos/pos/models/connection_sqli_manual.php"); // funciones mySQLi
}
else require $api_lite . "/modulos/pos/models/connection_sqli_manual.php";

class PdfModel extends Connection
{
 function crearpdfComplementos(){
 	
    	$sele1 = "SELECT uuid_pago from cont_facturas_relacion GROUP BY uuid_pago";
    	$res1 = $this->queryArray($sele1);
    	$sele2 = "SELECT logoempresa from organizaciones";
    	$res2 = $this->queryArray($sele2);

    	return   array('comple' => $res1['rows'], 'logo' => $res2['rows'][0]['logoempresa'] );
    }
}

?>