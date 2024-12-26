<?php

// Mostrar errores (solo en desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexión a la base de datos
$mysqli = new mysqli("34.66.63.218", "nmdevel", "nmdevel", "crud_example");
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

function ejecutarConsulta($mysqli, $sql, $params = [], $paramTypes = "") {
    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($paramTypes, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

// Manejo de acciones
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'list') {
    // Paginación
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10; // Número de registros por página
    $offset = ($page - 1) * $limit;

    // Ordenamiento
    $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'idloteproducto';
    $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    // Filtro por estado
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '';

    // Búsqueda
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Construir la cláusula WHERE
    $whereClause = "";
    if ($estado !== '') {
        $whereClause .= " WHERE idestadocatalogo = $estado";
    }
    if ($search !== '') {
        if ($whereClause === "") {
            $whereClause .= " WHERE ";
        } else {
            $whereClause .= " AND ";
        }
        $whereClause .= " descripcionlote LIKE '%$search%'";
    }

    // Consulta para obtener el total de registros (usando sentencia preparada)
    $countQuery = "SELECT COUNT(*) as total FROM inventarios_lotes $whereClause";
    $countResult = ejecutarConsulta($mysqli, $countQuery);
    $totalRows = $countResult->get_result()->fetch_assoc()['total'];

    // Consulta para obtener los registros de la página actual (usando sentencia preparada)
    $query = "SELECT * FROM inventarios_lotes $whereClause ORDER BY $sortColumn $sortOrder LIMIT $limit OFFSET $offset";
    $result = ejecutarConsulta($mysqli, $query);
    $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);

    // Datos para la paginación
    $totalPages = ceil($totalRows / $limit);
    $pagination = [
        'currentPage' => $page,
        'totalPages' => $totalPages,
    ];

    header('Content-Type: application/json');
    echo json_encode(['data' => $data, 'pagination' => $pagination]);

} elseif ($action === 'add') {
    // Validar datos
    if (empty($_POST['descripcionlote']) || empty($_POST['fechafabricacion']) || empty($_POST['fechacaducidad']) || empty($_POST['idestadocatalogo'])) {
        echo "0"; // Error: datos incompletos
        exit;
    }

    // Agregar un registro (usando sentencia preparada)
    $descripcionlote = $_POST['descripcionlote'];
    $fechafabricacion = $_POST['fechafabricacion'];
    $fechacaducidad = $_POST['fechacaducidad'];
    $idestadocatalogo = $_POST['idestadocatalogo'];

    $sql = "INSERT INTO inventarios_lotes (descripcionlote, fechafabricacion, fechacaducidad, idestadocatalogo) 
            VALUES (?, ?, ?, ?)";
    $stmt = ejecutarConsulta($mysqli, $sql, [$descripcionlote, $fechafabricacion, $fechacaducidad, $idestadocatalogo], "sssd");
    echo $stmt->affected_rows > 0 ? "1" : "0";

} elseif ($action === 'edit') {
    // Validar datos
    if (empty($_POST['idloteproducto']) || empty($_POST['descripcionlote']) || empty($_POST['fechafabricacion']) || empty($_POST['fechacaducidad']) || empty($_POST['idestadocatalogo'])) {
        echo "0"; // Error: datos incompletos
        exit;
    }

    // Editar un registro (usando sentencia preparada)
    $idloteproducto = $_POST['idloteproducto'];
    $descripcionlote = $_POST['descripcionlote'];
    $fechafabricacion = $_POST['fechafabricacion'];
    $fechacaducidad = $_POST['fechacaducidad'];
    $idestadocatalogo = $_POST['idestadocatalogo'];

    $sql = "UPDATE inventarios_lotes SET descripcionlote = ?, fechafabricacion = ?, fechacaducidad = ?, idestadocatalogo = ? WHERE idloteproducto = ?";
    $stmt = ejecutarConsulta($mysqli, $sql, [$descripcionlote, $fechafabricacion, $fechacaducidad, $idestadocatalogo, $idloteproducto], "sssdi");
    echo $stmt->affected_rows > 0 ? "1" : "0";

} elseif ($action === 'delete') {
    // Eliminar un registro (usando sentencia preparada)
    if (empty($_POST['idloteproducto'])) {
        echo "0"; // Error: ID de lote no proporcionado
        exit;
    }

    $idloteproducto = $_POST['idloteproducto'];

    $sql = "Update inventarios_lotes set idestadocatalogo=2 WHERE idloteproducto = ?";
    $stmt = ejecutarConsulta($mysqli, $sql, [$idloteproducto], "i");
    echo $stmt->affected_rows > 0 ? "1" : "0";

} elseif ($action === 'export') {
    // Exportar a Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=inventarios_lotes.xls");
    echo "ID\tDescripción\tFecha de Fabricación\tFecha de Caducidad\tEstado\n";

    $query = "SELECT * FROM inventarios_lotes";
    $result = ejecutarConsulta($mysqli, $query);
    $data = $result->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($data as $row) {
        $estado = $row['idestadocatalogo'] == 1 ? 'Activo' : 'Cancelado';
        echo "{$row['idloteproducto']}\t{$row['descripcionlote']}\t{$row['fechafabricacion']}\t{$row['fechacaducidad']}\t$estado\n";
    }
    exit;
}
?>