<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("netwarelog/catalog/conexionbd.php");

echo "<h1>Diagnostic Folio 5735</h1>";

// 1. Check reception
echo "<h2>1. Reception</h2>";
$sql_rec = "SELECT * FROM logistica_recepciones WHERE idrecepcion = 5735";
$res_rec = $conexion->consultar($sql_rec);
if ($res_rec && $conexion->count_rows($res_rec) > 0) {
    while ($row = $conexion->siguiente($res_rec)) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
        $idenvio = $row['idenvio'];
        $idtraslado = $row['idtraslado'];
    }
} else {
    echo "<p style='color:red;'>Reception 5735 NOT found in logistica_recepciones!</p>";
}

if (isset($idenvio)) {
    // 2. Check shipment
    echo "<h2>2. Shipment</h2>";
    $sql_env = "SELECT * FROM logistica_envios WHERE idenvio = " . intval($idenvio);
    $res_env = $conexion->consultar($sql_env);
    if ($res_env && $conexion->count_rows($res_env) > 0) {
        while ($row = $conexion->siguiente($res_env)) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        }
    } else {
        echo "<p style='color:red;'>Shipment $idenvio NOT found in logistica_envios!</p>";
    }
}

// 3. Check inventory movement
echo "<h2>3. Inventory Movement</h2>";
$sql_mov = "SELECT * FROM inventarios_movimientos WHERE foliodoctoorigen = 5735 AND doctoorigen = 4";
$res_mov = $conexion->consultar($sql_mov);
if ($res_mov && $conexion->count_rows($res_mov) > 0) {
    while ($row = $conexion->siguiente($res_mov)) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
} else {
    echo "<p style='color:red;'>Inventory movement for Folio 5735 (doctoorigen=4) NOT found in inventarios_movimientos!</p>";
}

// 4. Check logistica_traslados for matching traslado and check quantities
if (isset($idtraslado)) {
    echo "<h2>4. Traslado Detail</h2>";
    $sql_tras = "SELECT * FROM logistica_traslados WHERE idtraslado = " . intval($idtraslado);
    $res_tras = $conexion->consultar($sql_tras);
    if ($res_tras && $conexion->count_rows($res_tras) > 0) {
        while ($row = $conexion->siguiente($res_tras)) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        }
    } else {
        echo "<p style='color:red;'>Traslado $idtraslado NOT found in logistica_traslados!</p>";
    }
}

?>
