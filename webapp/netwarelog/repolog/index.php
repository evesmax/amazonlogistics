<?php
/**
 * SQL Query Results Viewer
 * 
 * This script retrieves an SQL query from the session variable,
 * executes it against a MySQL database, and displays the results
 * in an interactive HTML table with filtering and pagination.
 * 
 * Compatible with PHP 5.5.9 and MySQL 5.5.62
 */

// Include configuration file and utilities
require_once 'config.php';
require_once 'sqlcleaner.php';

// Initialize variables
$results = [];
$columns = [];
$error = '';
$query = '';
$reportTitle = "Resultados de Consulta SQL";
$appliedFilters = [];

// Obtener el título del reporte y los filtros aplicados
if (isset($_SESSION['repolog_report_id'])) {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Obtener nombre del reporte
        $stmt = $pdo->prepare("SELECT nombrereporte FROM repolog_reportes WHERE idreporte = ?");
        $stmt->execute([$_SESSION['repolog_report_id']]);
        $reportInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reportInfo && isset($reportInfo['nombrereporte'])) {
            $reportTitle = $reportInfo['nombrereporte'];
        }
        
        $pdo = null;
    } catch (PDOException $e) {
        // Si hay error, mantenemos el título genérico
    }
    
    // Obtener los filtros aplicados de la sesión
    if (isset($_SESSION['applied_filters']) && is_array($_SESSION['applied_filters'])) {
        $appliedFilters = $_SESSION['applied_filters'];
    }
}

// Function to check if a stored procedure exists in the database
function procedimientoExiste($nombreProcedimiento) {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Consulta para verificar si existe el procedimiento
        $stmt = $pdo->prepare("SELECT COUNT(*) AS existe 
                               FROM information_schema.ROUTINES 
                               WHERE ROUTINE_TYPE = 'PROCEDURE' 
                               AND ROUTINE_SCHEMA = DATABASE() 
                               AND ROUTINE_NAME = ?");
        $stmt->execute([$nombreProcedimiento]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $pdo = null;
        return ($resultado['existe'] > 0);
    } catch (PDOException $e) {
        return false;
    }
}

// Function to execute a stored procedure
function ejecutarProcedimiento($nombreProcedimiento) {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Ejecutar el procedimiento almacenado
        $stmt = $pdo->prepare("CALL " . $nombreProcedimiento);
        $stmt->execute();
        
        $pdo = null;
        return true;
    } catch (PDOException $e) {
        // Error al ejecutar el procedimiento
        error_log("Error al ejecutar procedimiento $nombreProcedimiento: " . $e->getMessage());
        return false;
    }
}

// Check if SQL query exists in session
if (isset($_SESSION['sql_consulta']) && !empty($_SESSION['sql_consulta'])) {
    $query = $_SESSION['sql_consulta'];
    
    // Aplicamos todas las correcciones al SQL una vez más antes de ejecutar
    $query = fixAllSqlIssues($query);
    
    // Para debugging
    $_SESSION['sql_consulta_original'] = $_SESSION['sql_consulta'];
    $_SESSION['sql_consulta_fixed'] = $query;
    
    // 1. Check and execute url_include (before executing the query)
    if (isset($_SESSION['repolog_url_include']) && !empty($_SESSION['repolog_url_include'])) {
        $includeFile = trim($_SESSION['repolog_url_include']);
        if (file_exists($includeFile)) {
            include $includeFile;
        }
    }
    
    // 2. Check and execute stored procedure before query (sppre)
    if (isset($_SESSION['repolog_sppre']) && !empty($_SESSION['repolog_sppre'])) {
        $sppreName = trim($_SESSION['repolog_sppre']);
        if (procedimientoExiste($sppreName)) {
            ejecutarProcedimiento($sppreName);
        }
    }
    
    try {
        // Create a PDO connection
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        
        // Set error mode to throw exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare and execute the query
        $stmt = $pdo->query($query);
        
        // Get column count
        $columnCount = $stmt->columnCount();
        
        // Get column names
        for ($i = 0; $i < $columnCount; $i++) {
            $columnMeta = $stmt->getColumnMeta($i);
            $columns[] = $columnMeta['name'];
        }
        
        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Close connection
        $pdo = null;
        
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} else {
    $error = "No SQL query found in session.";
}

// Store results in session for export functionality
$_SESSION['query_results'] = $results;
$_SESSION['query_columns'] = $columns;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($reportTitle); ?> - Resultados</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Estilos adicionales para el título y los filtros */
        .report-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0 0 5px 0;
        }
        .report-filters {
            font-size: 14px;
            color: #666;
            margin: 10px 0 0 0;
        }
        .filter-item {
            display: inline-block;
            background-color: #f5f5f5;
            border-radius: 4px;
            padding: 3px 8px;
            margin: 2px 5px 2px 0;
            border: 1px solid #e0e0e0;
        }
        .filter-label {
            font-weight: bold;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="sql-results-container">
        <div class="header-actions">
            <div class="back-container">
                <?php if (isset($_SESSION['repolog_report_id'])): ?>
                    <a href="repolog.php?i=<?php echo intval($_SESSION['repolog_report_id']); ?>" class="back-btn">Regresar</a>
                <?php else: ?>
                    <button onclick="window.history.back()" class="back-btn">Regresar</button>
                <?php endif; ?>
            </div>
            <?php if (!empty($results)): ?>
                <div class="action-buttons">
                    <a href="export.php" class="export-btn">Descargar CSV</a>
                    <a href="export_excel.php" class="export-excel-btn">Descargar Excel</a>
                    <a href="print.php" class="print-btn">Imprimir PDF</a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Título del reporte y filtros aplicados -->
        <div class="report-header">
            <h1 class="report-title"><?php echo htmlspecialchars($reportTitle); ?></h1>
            
            <?php if (!empty($appliedFilters)): ?>
                <div class="report-filters">
                    <strong>Filtros aplicados:</strong>
                    <?php foreach ($appliedFilters as $filter): ?>
                        <span class="filter-item">
                            <span class="filter-label"><?php echo htmlspecialchars($filter['label']); ?>:</span>
                            <?php echo htmlspecialchars($filter['value']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php elseif (empty($results)): ?>
            <div class="no-results">
                <p>La consulta no devolvió resultados.</p>
            </div>
        <?php else: ?>
            <div class="table-controls">
                <div class="search-container">
                    <input type="text" id="globalSearch" placeholder="Buscar en todos los campos..." onkeyup="filterTable()">
                </div>
                <div class="pagination-controls">
                    <span>Filas por página:</span>
                    <select id="rowsPerPage" onchange="changeRowsPerPage()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <div class="pagination-buttons">
                        <button id="prevPage" onclick="changePage(-1)">Anterior</button>
                        <span id="currentPage">1</span> de <span id="totalPages">1</span>
                        <button id="nextPage" onclick="changePage(1)">Siguiente</button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table id="resultsTable">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <th>
                                    <?php echo htmlspecialchars($column); ?>
                                    <div class="column-filter">
                                        <input type="text" placeholder="Filtrar <?php echo htmlspecialchars($column); ?>..." 
                                               onkeyup="filterTable()" data-column="<?php echo htmlspecialchars($column); ?>">
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                    <td>
                                    <?php 
                                        $value = $row[$column] ?? '';
                                        
                                        // Detectar si parece contener HTML
                                        if (preg_match('/<[a-z][\s\S]*>/i', $value) && 
                                            (strpos($value, '<img') !== false || 
                                             strpos($value, '<a') !== false || 
                                             strpos($value, '<center') !== false ||
                                             strpos($value, '<div') !== false)) {
                                            // Es HTML, mostrarlo como tal
                                            echo $value;
                                        } else {
                                            // No es HTML, escapar como texto normal
                                            echo htmlspecialchars($value);
                                        }
                                    ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($results)): ?>
        <script>
            // Store data for JavaScript processing
            const tableData = <?php echo json_encode($results); ?>;
            const tableColumns = <?php echo json_encode($columns); ?>;
        </script>
        <script src="assets/js/table_functions.js"></script>
    <?php endif; ?>
    
    <?php
    // Execute post-processing functionality
    
    // 3. Check and execute stored procedure after query (sppos)
    if (isset($_SESSION['repolog_sppos']) && !empty($_SESSION['repolog_sppos'])) {
        $spposName = trim($_SESSION['repolog_sppos']);
        if (procedimientoExiste($spposName)) {
            ejecutarProcedimiento($spposName);
        }
    }
    
    // 4. Check and execute url_include_despues (after displaying results)
    if (isset($_SESSION['repolog_url_include_despues']) && !empty($_SESSION['repolog_url_include_despues'])) {
        $includeFileDespues = trim($_SESSION['repolog_url_include_despues']);
        if (file_exists($includeFileDespues)) {
            include $includeFileDespues;
        }
    }
    ?>
</body>
</html>
