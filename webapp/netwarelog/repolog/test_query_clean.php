<?php
// Emulate session
session_start();

$_SESSION['accelog_idempleado'] = 2;
$_SESSION['usar_sistema_tres_fases'] = true;

// Mock report array (ID 1)
$report = [
    'idreporte' => 1,
    'nombrereporte' => 'Existencias',
    'sql_select' => 'ob.nombrebodega AS Bodega, of.nombrefabricante AS Cliente, vm.nombremarca AS Marca, il.descripcionlote AS Zafra, ifa.nombrefamilia AS Producto, ip.nombreproducto AS Presentación, ie.descripcionestado AS EstadoProducto, FORMAT(SUM(ik.inventarioinicial), 0) AS Existencia, FORMAT(SUM(ik.inventarioinicial * um.factor), 3) AS ExistenciaTM',
    'sql_from' => 'inventarios_existencias ik inner join operaciones_fabricantes of on of.idfabricante=ik.idfabricante left join vista_marcas vm on vm.idmarca=ik.idmarca inner join operaciones_bodegas ob on ob.idbodega=ik.idbodega inner join inventarios_productos ip on ip.idproducto=ik.idproducto inner join inventarios_estados ie on ie.idestadoproducto=ik.idestadoproducto inner join inventarios_lotes il on il.idloteproducto=ik.idloteproducto left join inventarios_unidadesmedida um on um.idunidadmedida= ip.idunidadmedida left join inventarios_familias ifa on ifa.idfamilia=ip.idfamilia',
    'sql_where' => 'and (ik.inventarioinicial<>0) and (ob.idestadocatalogo<>4) and (ik.idempleado=[!accelog_idempleado]) and ( ik.idbodega in [@Nombre Bodega;val;des;Select idbodega val, nombrebodega des from vista_bodegasusuarios where (idempleado=[!accelog_idempleado]) OR (NOT EXISTS (SELECT 1 FROM relaciones_usuariosbodegas WHERE idempleado =[!accelog_idempleado])) group by idbodega, nombrebodega order by des;@Multiselection;@KeepColumn] ) and ( ik.idfabricante in [@Cliente;val;des;select idfabricante val, nombrefabricante des from vista_fabricantesusuarios where (idempleado=[!accelog_idempleado]) OR (NOT EXISTS (SELECT 1 FROM relaciones_usuariosfabricantes WHERE idempleado =[!accelog_idempleado])) group by idfabricante, nombrefabricante order by des;@Multiselection;@KeepColumn] ) and (ik.fecha <= "[#Al]  23:59:59") GROUP BY ik.idbodega, ik.idfabricante, ik.idmarca, ik.idloteproducto, ik.idproducto, ik.idestadoproducto ORDER BY Bodega, Cliente, Marca, Zafra, Producto, Presentación, EstadoProducto',
    'subtotales_agrupaciones' => 'Bodega,Cliente',
    'subtotales_subtotal' => 'Existencia,ExistenciaTM',
    'url_include' => '',
    'url_include_despues' => '',
    'sppre' => '',
    'sppos' => ''
];

// Mock database config variables
$servidor = "34.66.63.218";
$usuariobd = "nmdevel";
$clavebd = "nmdevel";
$bd = "netwarstore";

// Connect to get database instance
$objConG = mysqli_connect($servidor, $usuariobd, $clavebd, $bd);
$strSqlG = "SELECT usuario_db, pwd_db, nombre_db FROM customer WHERE instancia = 'amazon'";
$res = mysqli_query($objConG, $strSqlG);
$row = mysqli_fetch_assoc($res);
$usuariobd = $row['usuario_db'];
$clavebd = $row['pwd_db'];
$bd = $row['nombre_db'];
mysqli_close($objConG);

define('DB_DSN', "mysql:host=$servidor;dbname=$bd;charset=utf8");
define('DB_USER', $usuariobd);
define('DB_PASS', $clavebd);

// Include required functions
require_once __DIR__ . '/../../webapp/netwarelog/repolog/sqlcleaner.php';
require_once __DIR__ . '/../../webapp/netwarelog/repolog/repologfilters.php';

// Check with single bodega selection
$filterValues = [
    'filter_nombre_bodega' => ['11'],
    'filter_al' => '2026/06/29'
];

echo "1. Initial filter values:\n";
print_r($filterValues);

// Simulating filters parsing as in repologfilters.php
// We need to populate the global $filters variable.
$filters = [
    [
        'id' => 'filter_nombre_bodega',
        'type' => 'combo',
        'original_name' => 'Nombre Bodega',
        'multiselection' => true,
        'keep_column' => true
    ],
    [
        'id' => 'filter_al',
        'type' => 'date',
        'original_name' => 'Al'
    ]
];

// Step 1: buildSqlQueryThreePhase
$sqlQuery = buildSqlQueryThreePhase($report, $filterValues);
echo "\n2. SQL Query built by 3-phase:\n$sqlQuery\n";

// Step 2: reemplazarPatronesComboNoSustituidos (this modifies $filterValues by reference)
$finalSql = reemplazarPatronesComboNoSustituidos($sqlQuery, $filters, $filterValues);
echo "\n3. SQL Query after reemplazarPatronesComboNoSustituidos:\n$finalSql\n";
echo "Filter values after function (reference modification check):\n";
print_r($filterValues);

// Save to session
$_SESSION['sql_consulta'] = $finalSql;
$_SESSION['filter_values'] = $filterValues;

// Simulating reporte.php load
$query = $_SESSION['sql_consulta'];
$sessionFilterValues = $_SESSION['filter_values'];

// In reporte.php, they include and call:
$query = reemplazarPatronesComboNoSustituidos($query, $filters, $sessionFilterValues);
echo "\n4. SQL Query in reporte.php after first replace:\n$query\n";

$query = fixExtraAndBeforeClosingParenthesis($query);
$query = fixAllSqlIssues($query);
$query = fixUnbalancedParenthesisBeforeOrderBy($query);
if (function_exists('eliminaParentesisExcesivos')) {
    $query = eliminaParentesisExcesivos($query);
    $query = eliminaParentesisExcesivos($query);
}

echo "\n5. SQL Query after heuristic cleaners:\n$query\n";

$query = cleanSqlUniversal($query);
echo "\n6. SQL Query after cleanSqlUniversal:\n$query\n";

// Connect to database using PDO and execute query
try {
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n7. Execution successful! Returned " . count($results) . " rows.\n";
    if (count($results) > 0) {
        print_r($results);
    }
} catch (Exception $e) {
    echo "\n7. Execution failed: " . $e->getMessage() . "\n";
}
?>
