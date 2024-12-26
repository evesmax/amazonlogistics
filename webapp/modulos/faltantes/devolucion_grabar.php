<?php

include("../../netwarelog/catalog/conexionbd.php");

//Solo Genera Documento de Devolucion


$idtraslado=$_REQUEST["txtidtraslado"];
$idenvio=$_REQUEST["txtidenvio"];
$idrecepcion=$_REQUEST["txtidrecepcion"];
$iddevolucion=0;
$fechadevolucion = $_REQUEST["txtfechadevolucion"];

$idtransportista = $_REQUEST["cmbtransportista"];
$cartaporte = $_REQUEST["txtcartaporte"];
$nombreoperador = $_REQUEST["txtoperador"];
$licenciaoperador=$_REQUEST["txtlicencia"];
$placastractor = $_REQUEST["txtplacastractor"];
$placasremolque = $_REQUEST["txtplacasremolque"];
$folios = $_REQUEST["txtfolios"];
$observaciones = $_REQUEST["txtobservaciones"];
$cantidad1 = $_REQUEST["txtcantidad1"];
$cantidad2 = $_REQUEST["txtcantidad2"];
$doctoorigen = 6;
$capturista=$_REQUEST["txtcapturista"];
//Grabando Documento

//VALIDA INFORMACION DE VALORES
			$politica=0;
			$msg="";

			if((trim($cartaporte)=="")){
				$politica=1;
				$msg=" Falta la Carta Porte";
			}
			if(trim($placastractor)==""){
				$politica=1;
				$msg=" Faltaron las Placas del Tractor";
			}
			if(trim($nombreoperador)==""){
				$politica=1;
				$msg=" Falto el Nombre del Operador";
			}
			if(trim($licenciaoperador=="")){
				$politica=1;
				$msg=" Falto el numero de Licencia del Operador";
			}
			if($cantidad1==0){
				$politica=1;
				$msg=" Falto escribir una cantidad valida";
			}
			if(trim($folios=="")){
				$politica=1;
				$msg=" Faltaron los folios";
			}		
       if($politica==1){
            echo "
            <script  language='javascript'>
                alert('Verifique que la informacion este correcta, ".$msg."');
                history.back();
            </script>";    
            exit();
        }
//FIN POLITICAS

        
$cantidadrecibida1=0;
$cantidadrecibida2=0;
$diferencia1=$cantidad1;
$diferencia2=$cantidad2;

        
$sql = "Insert Into logistica_devoluciones (idtraslado,idenvio,idrecepcion,fechadevolucion,idtransportista,
                                                cartaporte,operador,placastractor,placasremolque,cantidad1,
                                                cantidad2,folios,observaciones,cantidadrecibida1,cantidadrecibida2,
                                                diferencia1,diferencia2,idestadodocumento
                                            ) VALUES 
        ('" . $idtraslado . "','" . $idenvio . "','" . $idrecepcion . "','" . $fechadevolucion . "','" . $idtransportista . "','" .
        $cartaporte . "','" . $nombreoperador . "','" . $placastractor . "','" . $placasremolque . "','" . $cantidad1 . "','" .
        $cantidad2 . "','" . $folios . "','" . $observaciones . "','" . $cantidadrecibida1 . "','" . $cantidadrecibida2 . "','".
        $diferencia1."','".$diferencia2."','1')";

$conexion->consultar($sql);
$iddevolucion = $conexion->insert_id();


header("Location: devolucion_imprimir.php?folio=" . $iddevolucion)

        
        
?>