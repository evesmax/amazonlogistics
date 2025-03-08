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
                   
//Recupera valores de consulta
        $sQuery = "Select *,CURDATE() fechadia from inventarios_trasvase where idtrasvase".$idtrasvase;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                        $fabricante=$rs{"idfabricante"};
                        $marca=$rs{"fabricante"};
                        $idbodega=$rs{"idbodega"};
                        $producto=$rs{"producto"};
                        $lote=$rs{"lote"};
                        $estadoproducto=$rs{"estadoproducto"};
                        $cantidad1=$rs{"cantidad1"};
                        $cantidad2=$rs{"cantidad2"};
                        $productodestino=$rs{"idproductodestino"};
                        $fechadia=$rs{"fechadia"};
                }
		$conexion->cerrar_consulta($result);

       
//Afectando Inventario con Documento.
        if ($cantidad1>0 {
            //Agrega Movimiento Almacen Salida de Producto Origen
            $doctoorigen=22;
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$producto,$lote,$estadoproducto,$cantidad1,$cantidad2,$fechadia,$doctoorigen,$idtrsvase,$conexion);
        }

        if ($cantidaddestino1>0 { 
            //Agrega Movimiento Almacen Entrada de Nuevo Producto
            $doctoorigen=23;
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadoproducto,$cantidaddestino1,$cantidaddestino2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
        }
        
        if ($cantidadpnc1>0 { 
            $doctoorigen=23;
            $estadopnc=4;
            //Agrega Movimiento Almacen Producto No Conforme
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadopnc,$cantidadpnc1,$cantidadpnc2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
        }

        if ($cantidadmerma1>0 { 
            //Agrega Movimiento Almacen Producto No Conforme
            $doctoorigen=24;
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$idestadoproducto,$cantidadmerma1,$cantidadmerma2,$fechadia,$doctoorigen,$idrecepcion,$conexion);
        }


    //Afecta Cantidades en Logistica_Trasvase
        $sqlafecta="UPDATE inventarios_trasvase 
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

        header("Location: trasvase_imprimir.php?idtrasvase=".$idtrasvase) 
         
?>