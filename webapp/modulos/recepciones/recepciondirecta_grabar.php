<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	
	include("../../netwarelog/catalog/conexionbd.php");

//Recuperando Variables y Grabando Envio
            $idtransportista=$_REQUEST["cmbtransportista"]; 
            $cartaporte=$_REQUEST["txtcartaporte"];
            $nombreoperador=$_REQUEST["txtnombreoperador"]; 
            $licenciaoperador=$_REQUEST["txtlicencia"];  
            $placastractor=$_REQUEST["txtplacastractor"]; 
            $placasremolque=$_REQUEST["txtplacasremolque"]; 
            $horallegada=$_REQUEST["txthorallegada"]; 
            $cantenv1=$_REQUEST["txtcantenv1"];
            $cantenv2=$_REQUEST["txtcantenv2"];
            $obsenvio=$_REQUEST["txtobsenvio"];            

//RECUPERANDO VARIABLES PARA GRABAR RECEPCION
            $idtraslado=$_REQUEST["txtidtraslado"];
            $idenvio=$_REQUEST["txtidenvio"];
            $idrecepcion=0;
            $fecharecepcion=$_REQUEST["txtfecharec"];
            $banco=$_REQUEST["txtbanco"];
            $estiba=$_REQUEST["txtestiba"];
            $ticketbascula=$_REQUEST["txtticketbascula"];
            $referencia=$_REQUEST["txtreferencia"];
            $observaciones=$_REQUEST["txtobservaciones"];
            $almacenista=$_REQUEST["txtalmacenista"];
            $supervisor="";
            $cabocuadrilla=$_REQUEST["txtcabocuadrilla"];
            
			$cantidadenviada1=str_replace(",","",$_REQUEST["txtcantenv1"]);
            $cantidadenviada2=$_REQUEST["txtcantenv2"];
            $cantidadrecibida1=str_replace(",","",$_REQUEST["txtcantrec1"]);
			
            $cantidadrecibida2=$_REQUEST["txtcantrec2"];
            $idbodega=$_REQUEST["cmbbodega"];   //Bodega Real
            
            $difestatus1=$_REQUEST["txtestatus1"];
            $difestatus2=$_REQUEST["txtestatus2"];
 
            $diferencia1=$_REQUEST["txtcantdif1"];
            $diferencia2=$_REQUEST["txtcantdif2"];
            $folios=$_REQUEST["txtfolios"];
            $doctoorigen=4;
            
			
			
            $cantdev1=0;
            $cantdev2=0;
            $cantfalt1=0;
            $cantfalt2=0;
            $estatus1=0;
            $estatus2=0;
            

            
            //Esto es si existen diferencias
            if($diferencia1>0) {
                    $cantdev1=$_REQUEST["txtcantdev1"];
                    $cantdev2=$_REQUEST["txtcantdev2"];
                    $cantfalt1=$_REQUEST["txtcantfalt1"];
                    $cantfalt2=$_REQUEST["txtcantfalt2"];
                    $estatus1=$_REQUEST["txtestatus1"]*1;
                    $estatus2=$_REQUEST["txtestatus2"]*1; 
                    $idestadoproducto=$_REQUEST["cmbestados"];                 
            }
            

            $capturista=$_REQUEST["txtcapturista"];
 
//VALIDA INFORMACION DE VALORES

			$politica=0;
			$msg="";
            

			if(($difestatus1*1>0)){
                $politica=1;
                $msg="Falto aclarar la diferencia de envio con recepcion";
            }
			if($cantidadrecibida1==0 or $cantidadrecibida2==0){
				$politica=1;
				$msg=" Falto escribir una cantidad valida";
			}
			if(trim($folios=="")){
				$politica=1;
				$msg=" Faltaron los folios";
			}

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
			if($cantenv1==0){
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

         
?>