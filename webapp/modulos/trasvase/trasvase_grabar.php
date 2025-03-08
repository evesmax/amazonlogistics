<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);	
	
	include("../../netwarelog/catalog/conexionbd.php");
    include("../inventarios/clases/clinventarios.php");
    $movimientos = new clinventarios();

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
        $sQuery = "Select *,CURDATE() fechadia from inventarios_trasvase where idtrasvase=".$idtrasvase;
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
                        $fabricante=$rs{"idfabricante"};
                        $marca=$rs{"idmarca"};
                        $idbodega=$rs{"idbodega"};
                        $producto=$rs{"idproducto"};
                        $lote=$rs{"idloteproducto"};
                        $estadoproducto=$rs{"idestadoproducto"};
                        $cantidad1=$rs{"cantidad1"};
                        $cantidad2=$rs{"cantidad2"};
                        $productodestino=$rs{"idproductodestino"};
                        $fechadia=$rs{"fechadia"};
                }
		$conexion->cerrar_consulta($result);
        echo $sQuery."<br>";

//trasvase
$doctoorigen=6;       
//Afectando Inventario con Documento.
        if ($cantidad1>0) {
            //Agrega Movimiento Almacen Salida de Producto Origen
            $tipomovimiento=22;
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$producto,$lote,$estadoproducto,$cantidad1,$cantidad2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
            echo "Actualiza inventario Salida TipoMovimiento:$tipomovimiento,Fabricante:$fabricante,Marca:$marca,IdBodega:$idbodega,Producto:$producto,Lote:$lote,EstadoProducto:$estadoproducto,Cantidad1:$cantidad1,Cantidad2:$cantidad2,Fecha:$fechadia,DoctOrigen:$doctoorigen,FolioOrigen:$idtrasvase<br>";
        }

        if ($cantidaddestino1>0) { 
            //Agrega Movimiento Almacen Entrada de Nuevo Producto
            $tipomovimiento=23;
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadoproducto,$cantidaddestino1,$cantidaddestino2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
            echo "Actualiza inventario Entrad PResultado TipoMovimiento:$tipomovimiento,Fabricante:$fabricante,Marca:$marca,Bodega:$idbodega,Producto:$productodestino,LOte:$lote,EstadoProducto:$estadoproducto,Cantidad1:$cantidaddestino1,Cantidad2:$cantidaddestino2,Fecha:$fechadia,DoctoOrigen:$doctoorigen,Folio:$idtrasvase <br>";
        }
        
        if ($cantidadpnc1>0) { 
            $tipomovimiento=23;
            $estadopnc=4;
            //Agrega Movimiento Almacen Producto No Conforme
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadopnc,$cantidadpnc1,$cantidadpnc2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
            echo "Actualiza inventario Entrada NConforme TipoMovimiento:$tipomovimiento,Fabricante:$fabricante,Marca:$marca,Bodega:$idbodega,Producto:$productodestino,Lote:$lote,EstadoProducto:$estadopnc,Cantidad1:$cantidadpnc1,Cantidad2:$cantidadpnc2,Fecha:$fechadia,Doctoorigen:$doctoorigen,Folio:$idtrasvase, <br>";

        }

        if ($cantidadmerma1>0) { 
            //Agrega Movimiento Almacen Producto No Conforme
            $doctoorigen=24;
            //$movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadoproducto,$cantidadmerma1,$cantidadmerma2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
            echo "Actualiza inventario Salida por Merma TipoMovimiento:$tipomovimiento,Fabricante:$fabricante,Marca:$marca,Bodega:$idbodega,Producto:$productodestino,Lote:$lote,Estado:$estadoproducto,Cantidad1:$cantidadmerma1,Cantidad2:$cantidadmerma2,Fecha:$fechadia,DoctoOrigen:$doctoorigen,Folio:$idtrasvase <br>";
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
                        Where idtrasvase=".$idtrasvase;
        echo $sqlafecta;
        $conexion->consultar($sqlafecta);

        //header("Location: trasvase_imprimir.php?idtrasvase=".$idtrasvase) 
         
?>