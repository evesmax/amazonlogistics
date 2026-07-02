<?php
	
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
            $foliosorigenreal=$_REQUEST["txtfoliosorigenreal"];
            $foliosdestinoreal=$_REQUEST["txtfoliosdestinoreal"];
            $txtobservaciones=$_REQUEST["txtobservaciones"];      
            
            // Transformar la fecha que viene en formato d-m-Y H:i:s a Y-m-d H:i:s para MySQL
            $fecharec_raw = $_REQUEST["txtfecharec"];
            $fechadia = "";
            $datetime = DateTime::createFromFormat('d-m-Y H:i:s', $fecharec_raw);
            if ($datetime) {
                $fechadia = $datetime->format('Y-m-d H:i:s');
            } else {
                $fechadia = date("Y-m-d H:i:s", strtotime($fecharec_raw));
            }

//Recupera valores de consulta

try {
    $conexion->consultar("START TRANSACTION");

    $sQuery = "Select * from inventarios_trasvase where idtrasvase=".$idtrasvase." FOR UPDATE";
    $result = $conexion->consultar($sQuery);
    if ($result === false) {
        throw new Exception("Error al consultar el trasvase original: " . mysql_error());
    }
    
    $idestadodocumento = null;
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
        $idestadodocumento=$rs{"idestadodocumento"};
    }
    $conexion->cerrar_consulta($result);
    
    if ($idestadodocumento === null) {
        throw new Exception("No se encontró el registro de trasvase especificado.");
    }
    
    // Bloqueo de seguridad: Evitar procesar nuevamente si ya está procesado
    if ($idestadodocumento == 2) {
        $conexion->consultar("ROLLBACK");
        header("Location: trasvase_imprimir.php?idtrasvase=" .$idtrasvase);
        exit();
    }

    //trasvase
    $doctoorigen=6;       
    //Afectando Inventario con Documento.
    if ($cantidad1>0) {
        //Agrega Movimiento Almacen Salida de Producto Origen
        $tipomovimiento=22;
        $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$producto,$lote,$estadoproducto,$cantidad1,$cantidad2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
    }

    if ($cantidaddestino1>0) { 
        //Agrega Movimiento Almacen Entrada de Nuevo Producto
        $tipomovimiento=23;
        $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadoproducto,$cantidaddestino1,$cantidaddestino2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
    }
    
    if ($cantidadpnc1>0) { 
        $tipomovimiento=23;
        $estadopnc=4;
        //Agrega Movimiento Almacen Producto No Conforme
        $movimientos->agregarmovimiento($tipomovimiento,$fabricante,$marca,$idbodega,$productodestino,$lote,$estadopnc,$cantidadpnc1,$cantidadpnc2,$fechadia,$doctoorigen,$idtrasvase,$conexion);
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
                        idestadodocumento=2,
                        foliosorigenreal='$foliosorigenreal',
                        foliosdestinoreal='$foliosdestinoreal',
                        fechaop='$fechadia',
                        obsproceso='$txtobservaciones'
                    Where idtrasvase=".$idtrasvase;
    
    $res = $conexion->consultar($sqlafecta);
    if ($res === false) {
        throw new Exception("Error al actualizar inventarios_trasvase: " . mysql_error());
    }

    // Registrar transaccion
    $conexion->transaccion("GRABAR TRASVASE: " . $idtrasvase, $sqlafecta);

    $conexion->consultar("COMMIT");

    header("Location: trasvase_imprimir.php?idtrasvase=" .$idtrasvase);

} catch (Exception $e) {
    $conexion->consultar("ROLLBACK");
    echo "<div style='color:red; font-family:helvetica; font-size:14px; font-weight:bold; margin:20px; text-align:center;'>";
    echo "Error al registrar el trasvase: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    exit();
} 
         
?>