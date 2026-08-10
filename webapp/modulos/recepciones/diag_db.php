<?php
header('Content-Type: application/json; charset=utf-8');

$servidor  = "34.66.63.218";
$usuariobd = "nmdevel";
$clavebd   = "nmdevel";
$bd        = "_dbmlog0000018677";

$conn = mysqli_connect($servidor, $usuariobd, $clavebd, $bd);
if (!$conn) {
    echo json_encode(array("error" => "Connection failed: " . mysqli_connect_error()));
    exit();
}

$results = array();

// Helper to query and save results
function run_query($conn, $label, $sql) {
    global $results;
    $res = mysqli_query($conn, $sql);
    if (!$res) {
        $results[$label] = array("error" => mysqli_error($conn), "sql" => $sql);
        return;
    }
    $rows = array();
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    $results[$label] = $rows;
    mysqli_free_result($res);
}

// 1. Check warehouses (bodegas) info to find their IDs
run_query($conn, "bodegas", "SELECT idbodega, nombrebodega, calle, idmunicipio, idestado FROM operaciones_bodegas WHERE nombrebodega LIKE '%Tultepec%' OR nombrebodega LIKE '%Purific%' OR nombrebodega LIKE '%Amat%'");

// 2. Search for the specific folios in logistica_recepciones (both idrecepcion and consecutivobodega and the folios text field)
run_query($conn, "recepciones_by_id", "SELECT * FROM logistica_recepciones WHERE idrecepcion IN (5763, 5772, 5790, 5791, 5819) OR consecutivobodega IN (5763, 5772, 5790, 5791, 5819)");

// 3. Search for the specific folios in logistica_envios
run_query($conn, "envios_by_id", "SELECT * FROM logistica_envios WHERE idenvio IN (5763, 5772, 5790, 5791, 5819) OR consecutivobodega IN (5763, 5772, 5790, 5791, 5819)");

// 4. Search in inventarios_movimientos
run_query($conn, "inventarios_movimientos", "SELECT * FROM inventarios_movimientos WHERE foliodoctoorigen IN (5763, 5772, 5790, 5791, 5819) AND doctoorigen IN (3, 4)");

// 5. Search in netwarelog_transacciones_2026_s2 for any SQL queries or actions mentioning these numbers
$folios_pattern = "('5763'|'5772'|'5790'|'5791'|'5819'|5763|5772|5790|5791|5819)";
run_query($conn, "transacciones_matching", "SELECT * FROM netwarelog_transacciones_2026_s2 WHERE sqlproceso LIKE '%5763%' OR sqlproceso LIKE '%5772%' OR sqlproceso LIKE '%5790%' OR sqlproceso LIKE '%5791%' OR sqlproceso LIKE '%5819%' ORDER BY fecha DESC LIMIT 100");

// 6. Let's see all tables containing 'transacc' just in case
run_query($conn, "tables_transacciones", "SHOW TABLES LIKE '%transacc%'");

// 7. Let's see recent receptions in the date range (Aug 1 to Aug 6, 2026) to see if there are other entries we missed
run_query($conn, "recepciones_agosto", "SELECT r.idrecepcion, r.consecutivobodega, r.fecharecepcion, r.idbodega, b.nombrebodega, r.folios, r.referencia, r.idestadodocumento FROM logistica_recepciones r LEFT JOIN operaciones_bodegas b ON r.idbodega = b.idbodega WHERE r.fecharecepcion BETWEEN '2026-08-01' AND '2026-08-08' ORDER BY r.fecharecepcion DESC");

// 8. Let's see recent envios in the date range (Aug 1 to Aug 6, 2026)
run_query($conn, "envios_agosto", "SELECT e.idenvio, e.consecutivobodega, e.fechaenvio, e.idbodegaorigen, b.nombrebodega, e.folios, e.referencia, e.idestadodocumento FROM logistica_envios e LEFT JOIN operaciones_bodegas b ON e.idbodegaorigen = b.idbodega WHERE e.fechaenvio BETWEEN '2026-08-01' AND '2026-08-08' ORDER BY e.fechaenvio DESC");

// 9. Let's look for cancellations or deletes in the transacciones table specifically
run_query($conn, "cancellations_or_deletions", "SELECT * FROM netwarelog_transacciones_2026_s2 WHERE sqlproceso LIKE '%delete%' OR sqlproceso LIKE '%update%idestadodocumento%' OR sqlproceso LIKE '%cancel%' ORDER BY fecha DESC LIMIT 100");

mysqli_close($conn);

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
