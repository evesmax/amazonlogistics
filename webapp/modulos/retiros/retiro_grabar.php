<?php
include("../../netwarelog/webconfig.php");
set_time_limit($tiempo_timeout);

include("../../netwarelog/catalog/conexionbd.php");

//RECUPERANDO VARIABLES
$idordenentrega = $_REQUEST["txtidordenentrega"];
$idretiro = "";
$fechasalida = $_REQUEST["txtfechasalida"];
$idtransportista = $_REQUEST["cmbtransportista"];
$cartaporte = $_REQUEST["txtcartaporte"];
$nombreoperador = $_REQUEST["txtoperador"];
$placastractor = $_REQUEST["txtplacastractor"];
$placasremolque = $_REQUEST["txtplacasremolque"];
$referencia1 = $_REQUEST["txtrefcliente"];
$ticketbascula = $_REQUEST["txtticketbascula"];
$banco = $_REQUEST["txtbanco"];
$estiba = $_REQUEST["txtestiba"];
$cantidad1 = $_REQUEST["txtcantidad1"];
$cantidad2 = $_REQUEST["txtcantidad2"];
$doctoorigen = 5;
$folios = $_REQUEST["txtfolios"];
$observaciones = $_REQUEST["txtobservaciones"];
$capturista=$_REQUEST["txtcapturista"];
$licenciaoperador=$_REQUEST["txtlicencia"];
$sellos=$_REQUEST["txtsellos"];
$cuadrilla=$_REQUEST["txtcuadrilla"];
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

try {
    $conexion->consultar("START TRANSACTION");

    $sql = "Insert Into logistica_retiros (idordenentrega,fechasalida,idtransportista,cartaporte,nombreoperador,
                                                    placastractor,placasremolque,referencia1,ticketbascula,banco,estiba,
                                                    cantidad1,cantidad2,folios,observaciones,
                                                    idestadodocumento,licenciaoperador,idempleado,sellos,cuadrilla
                                                ) VALUES 
                                                ('" . $idordenentrega . "','" . $fechasalida . "','" . $idtransportista . "','" . $cartaporte . "','" . $nombreoperador . "','" .
            $placastractor . "','" . $placasremolque . "','" . $referencia1 . "','" . $ticketbascula . "','" . $banco . "','" .
            $estiba . "','" . $cantidad1 . "','" . $cantidad2 . "','" . $folios . "','" . $observaciones . "',1,'" . $licenciaoperador . "','" . $capturista . "','" . $sellos . "','" . $cuadrilla . "')";

    $res = $conexion->consultar($sql);
    if ($res === false) {
        throw new Exception("Error al registrar el retiro en logistica_retiros: " . mysql_error());
    }
    $idretiro = $conexion->insert_id();

    // Registrar transaccion
    $conexion->transaccion("RETIRO: " . $idretiro, $sql);

    //Afectando Inventario con Documento.
    //Consulta Politicas el Tipo de movimiento que afecta
    //# POLITICA Consulta Politicas el Tipo de movimiento que afecta
    $tipomovimiento = "18";
    $sqlbodega = "select * from logistica_politicas where idpolitica=9";
    $result = $conexion->consultar($sqlbodega);
    if ($result === false) {
        throw new Exception("Error al consultar logistica_politicas: " . mysql_error());
    }
    while ($rs = $conexion->siguiente($result)) {
        $tipomovimiento = $rs{"valor1"};
    }
    $conexion->cerrar_consulta($result);

    include("../inventarios/clases/clinventarios.php");
    $movimientos = new clinventarios();

    //Consulta Detalle
    $sQuery = "select b.idfabricante 'fabricante',b.idmarca ,b.idbodega 'bodega',b.idproducto 'producto',
                                    b.idloteproducto 'lote', b.idestadoproducto 'estadoproducto',
                                    b.cantidad1, b.cantidad2, 
                                    ifnull(b.cantidadretirada1,0) 'cantidadretirada1',
                                    ifnull(b.cantidadretirada2,0) 'cantidadretirada2',
                                    ifnull(b.saldo1,0) 'saldo1',
                                    ifnull(b.saldo2,0) 'saldo2'
                                from logistica_ordenesentrega b 
                                Where b.idordenentrega=$idordenentrega";
    echo $sQuery . "<br>";

    $result = $conexion->consultar($sQuery);
    if ($result === false) {
        throw new Exception("Error al obtener detalle de la orden de entrega: " . mysql_error());
    }
    while ($rs = $conexion->siguiente($result)) {
        $fabricante = $rs{"fabricante"};
        $marca = $rs{"idmarca"};
        $bodega = $rs{"bodega"};
        $producto = $rs{"producto"};
        $lote = $rs{"lote"};
        $estadoproducto = $rs{"estadoproducto"};
        //Datos para afectar
        $cantidadretirada1 = $rs{"cantidadretirada1"} + $cantidad1;
        $cantidadretirada2 = $rs{"cantidadretirada2"} + $cantidad2;
        $saldo1 = $rs{"cantidad1"} - $cantidadretirada1;
        $saldo2 = $rs{"cantidad2"} - $cantidadretirada2;
        
        //Agrega Movimiento Almacen
        $movimientos->agregarmovimiento($tipomovimiento, $fabricante, $marca, $bodega, $producto, $lote, $estadoproducto, $cantidad1, $cantidad2, $fechasalida, $doctoorigen, $idretiro, $conexion);
        
        //Afecta Cantidades en Ordenes de Entrega
        $sqlafecta = "UPDATE logistica_ordenesentrega 
                        SET cantidadretirada1=$cantidadretirada1,cantidadretirada2=$cantidadretirada2,saldo1=$saldo1,saldo2=$saldo2 
                        WHERE idordenentrega=" . $idordenentrega;
        $res = $conexion->consultar($sqlafecta);
        if ($res === false) {
            throw new Exception("Error al actualizar la orden de entrega logistica_ordenesentrega: " . mysql_error());
        }
    }
    $conexion->cerrar_consulta($result);

    //Consecutivo Interno Bodega 
    $consecutivobodega=-1;
    //Afecta Folio Interno
    $sqlcon="select ifnull(consecutivobodega,0) consecutivobodega from logistica_consecutivosbodega where idbodega=$bodega";
    $result = $conexion->consultar($sqlcon);
    if ($result === false) {
        throw new Exception("Error al consultar consecutivobodega: " . mysql_error());
    }
    while ($rs = $conexion->siguiente($result)) {
        $consecutivobodega = $rs{"consecutivobodega"};
    }
    $conexion->cerrar_consulta($result);

    if($consecutivobodega>0){
        $sqlafecta="Update logistica_consecutivosbodega set consecutivobodega=($consecutivobodega+1) where idbodega=$bodega";
        $consecutivobodega++;
    }else{
        $consecutivobodega=1;
        $sqlafecta="Insert Into logistica_consecutivosbodega (idbodega,doctoorigen,consecutivobodega) Values ('$bodega','0',1)";
    }
    $res = $conexion->consultar($sqlafecta);
    if ($res === false) {
        throw new Exception("Error al actualizar consecutivobodega: " . mysql_error());
    }
    echo $sqlafecta."<br>";

    //Actualiza en Documento
    $sqlafecta="Update logistica_retiros set consecutivobodega=$consecutivobodega where idretiro=$idretiro";
    $res = $conexion->consultar($sqlafecta);
    if ($res === false) {
        throw new Exception("Error al actualizar consecutivobodega en logistica_retiros: " . mysql_error());
    }
    echo $sqlafecta."<br>";

    $conexion->consultar("COMMIT");

    header("Location: retiro_imprimir.php?folio=" . $idretiro);

} catch (Exception $e) {
    $conexion->consultar("ROLLBACK");
    echo "<div style='color:red; font-family:helvetica; font-size:14px; font-weight:bold; margin:20px; text-align:center;'>";
    echo "Error al registrar el retiro: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    exit();
}
?>