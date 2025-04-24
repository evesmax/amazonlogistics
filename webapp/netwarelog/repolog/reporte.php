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

// Obtener el título del reporte, configuración de subtotales y los filtros aplicados
if (isset($_SESSION['repolog_report_id'])) {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Obtener nombre del reporte y configuración de subtotales
        $stmt = $pdo->prepare("SELECT nombrereporte, subtotales_agrupaciones, subtotales_subtotal FROM repolog_reportes WHERE idreporte = ?");
        $stmt->execute([$_SESSION['repolog_report_id']]);
        $reportInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reportInfo && isset($reportInfo['nombrereporte'])) {
            $reportTitle = $reportInfo['nombrereporte'];
            
            // Guardar la información de subtotales en variables de sesión
            if (isset($reportInfo['subtotales_agrupaciones'])) {
                $_SESSION['subtotales_agrupaciones'] = $reportInfo['subtotales_agrupaciones'];
            }
            
            if (isset($reportInfo['subtotales_subtotal'])) {
                $_SESSION['subtotales_subtotal'] = $reportInfo['subtotales_subtotal'];
            }
        }
        
        $pdo = null;
    } catch (PDOException $e) {
        // Si hay error, mantenemos el título genérico
        error_log("Error al obtener información del reporte: " . $e->getMessage());
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

// Procesamos los subtotales si están configurados
$hasSubtotals = false;
$subtotalesAgrupaciones = isset($_SESSION['subtotales_agrupaciones']) ? $_SESSION['subtotales_agrupaciones'] : '';
$subtotalesSubtotal = isset($_SESSION['subtotales_subtotal']) ? $_SESSION['subtotales_subtotal'] : '';

// Calculamos los subtotales si hay resultados y las configuraciones necesarias
if (!empty($results) && !empty($subtotalesAgrupaciones) && !empty($subtotalesSubtotal)) {
    $hasSubtotals = true;
    $results = processSubtotals($results, $subtotalesAgrupaciones, $subtotalesSubtotal);
}

// Store results in session for export functionality
$_SESSION['query_results'] = $results;
$_SESSION['query_columns'] = $columns;

/**
 * Procesa los resultados para agregar subtotales según las configuraciones especificadas
 * 
 * @param array $data Los resultados de la consulta
 * @param string $groupingFields Lista de campos para agrupar, separados por comas
 * @param string $totalFields Lista de campos a totalizar, separados por comas
 * @return array Resultados procesados con filas de subtotales y totales
 */
function processSubtotals($data, $groupingFields, $totalFields) {
    if (empty($data) || empty($groupingFields) || empty($totalFields)) {
        return $data;
    }
    
    // Convertir las cadenas de campos a arrays
    $groupFields = array_map('trim', explode(',', $groupingFields));
    $sumFields = array_map('trim', explode(',', $totalFields));
    
    // Verificar que los campos existan en los datos
    $firstRow = reset($data);
    $validGroupFields = [];
    foreach ($groupFields as $field) {
        if (isset($firstRow[$field])) {
            $validGroupFields[] = $field;
        }
    }
    
    $validSumFields = [];
    foreach ($sumFields as $field) {
        if (isset($firstRow[$field])) {
            $validSumFields[] = $field;
        }
    }
    
    // Si no hay campos válidos para agrupar o totalizar, retornar los datos originales
    if (empty($validGroupFields) || empty($validSumFields)) {
        return $data;
    }
    
    // Procesamos los subtotales y totales
    $result = [];
    $subtotals = [];
    $grandTotals = [];
    
    // Inicializar totales generales
    foreach ($validSumFields as $field) {
        $grandTotals[$field] = 0;
    }
    
    // Agrupamos los datos para calcular subtotales
    $currentGroup = null;
    $currentSubtotal = null;
    
    foreach ($data as $row) {
        // Construir la clave del grupo actual
        $groupKey = '';
        foreach ($validGroupFields as $field) {
            $groupKey .= $row[$field] . '|';
        }
        
        // Si es un nuevo grupo, agregar subtotales del grupo anterior e inicializar nuevo grupo
        if ($currentGroup !== null && $currentGroup !== $groupKey) {
            // Agregar fila de subtotal del grupo anterior
            if ($currentSubtotal !== null) {
                $subtotalRow = [];
                
                // Copiar los valores de agrupación del último registro del grupo
                foreach ($validGroupFields as $field) {
                    $subtotalRow[$field] = $subtotals['lastRow'][$field];
                }
                
                // Agregar los subtotales calculados
                foreach ($validSumFields as $field) {
                    $subtotalRow[$field] = $currentSubtotal[$field];
                }
                
                // Marcar como fila de subtotal
                $subtotalRow['__is_subtotal'] = true;
                $subtotalRow['__subtotal_level'] = 1;
                
                // Agregar la fila de subtotal a los resultados
                $result[] = $subtotalRow;
            }
            
            // Reiniciar subtotales para el nuevo grupo
            $currentSubtotal = [];
            foreach ($validSumFields as $field) {
                $currentSubtotal[$field] = 0;
            }
        }
        
        // Si es el primer registro o un nuevo grupo, inicializar subtotales
        if ($currentGroup === null || $currentGroup !== $groupKey) {
            $currentGroup = $groupKey;
            
            if ($currentSubtotal === null) {
                $currentSubtotal = [];
                foreach ($validSumFields as $field) {
                    $currentSubtotal[$field] = 0;
                }
            }
        }
        
        // Agregar el registro actual a los resultados
        $result[] = $row;
        
        // Actualizar subtotales y totales generales
        foreach ($validSumFields as $field) {
            $value = is_numeric($row[$field]) ? floatval($row[$field]) : 0;
            $currentSubtotal[$field] += $value;
            $grandTotals[$field] += $value;
        }
        
        // Guardar el último registro del grupo para referencia
        $subtotals['lastRow'] = $row;
    }
    
    // Agregar subtotal del último grupo
    if ($currentSubtotal !== null) {
        $subtotalRow = [];
        
        // Copiar los valores de agrupación del último registro
        foreach ($validGroupFields as $field) {
            $subtotalRow[$field] = $subtotals['lastRow'][$field];
        }
        
        // Agregar los subtotales calculados
        foreach ($validSumFields as $field) {
            $subtotalRow[$field] = $currentSubtotal[$field];
        }
        
        // Marcar como fila de subtotal
        $subtotalRow['__is_subtotal'] = true;
        $subtotalRow['__subtotal_level'] = 1;
        
        // Agregar la fila de subtotal a los resultados
        $result[] = $subtotalRow;
    }
    
    // Agregar fila de totales generales al final
    $totalRow = [];
    
    // Dejar en blanco los campos de agrupación
    foreach ($validGroupFields as $field) {
        $totalRow[$field] = '';
    }
    
    // Agregar los totales generales
    foreach ($validSumFields as $field) {
        $totalRow[$field] = $grandTotals[$field];
    }
    
    // Marcar como fila de total general
    $totalRow['__is_subtotal'] = true;
    $totalRow['__subtotal_level'] = 2;  // Nivel 2 para total general
    
    // Agregar la fila de total general a los resultados
    $result[] = $totalRow;
    
    return $result;
}
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
        
        /* Estilos para filas de subtotales y totales */
        .subtotal-row {
            background-color: #f0f0f0;
            font-weight: bold;
            border-top: 1px solid #ccc;
        }
        .total-row {
            background-color: #e0e0e0;
            font-weight: bold;
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }
        .subtotal-row td, .total-row td {
            padding: 8px 10px;
        }
    </style>
</head>
<body>
<?php 
// Ocultar completamente el div de carga para el ambiente productivo
// No incluimos el div para evitar cargar imágenes inexistentes
// El script al final de la página también buscará este div y lo ocultará si existiera
?>
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
                            <?php 
                            // Determinar si es una fila de subtotal o total
                            $isSubtotal = isset($row['__is_subtotal']) && $row['__is_subtotal'] === true;
                            $subtotalLevel = isset($row['__subtotal_level']) ? intval($row['__subtotal_level']) : 0;
                            
                            // Definir clases CSS según el tipo de fila
                            $rowClass = '';
                            if ($isSubtotal) {
                                $rowClass = $subtotalLevel === 1 ? 'subtotal-row' : 'total-row';
                            }
                            ?>
                            <tr class="<?php echo $rowClass; ?>">
                                <?php foreach ($columns as $column): ?>
                                    <td>
                                    <?php 
                                        // Ignorar campos especiales de control
                                        if ($column === '__is_subtotal' || $column === '__subtotal_level') {
                                            echo '';
                                            continue;
                                        }
                                        
                                        $value = isset($row[$column]) ? $row[$column] : '';
                                        
                                        // Si es una fila de subtotal o total, dar formato especial
                                        if ($isSubtotal) {
                                            // Si es un campo numérico (aparece en los campos a totalizar)
                                            $subtotalesSubtotal = isset($_SESSION['subtotales_subtotal']) ? $_SESSION['subtotales_subtotal'] : '';
                                            $sumFields = array_map('trim', explode(',', $subtotalesSubtotal));
                                            
                                            if (in_array($column, $sumFields)) {
                                                // Formatear como número con 2 decimales
                                                if (is_numeric($value)) {
                                                    echo '<strong>' . number_format(floatval($value), 2, '.', ',') . '</strong>';
                                                } else {
                                                    echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                }
                                                continue;
                                            } else if ($subtotalLevel === 2) {
                                                // Para la fila de total general, mostrar "TOTAL GENERAL" en la primera columna
                                                if ($column === reset($columns)) {
                                                    echo '<strong>TOTAL GENERAL</strong>';
                                                    continue;
                                                }
                                            } else if ($subtotalLevel === 1) {
                                                // Para filas de subtotal, mostrar "Subtotal:" y el valor del campo
                                                $subtotalesAgrupaciones = isset($_SESSION['subtotales_agrupaciones']) ? $_SESSION['subtotales_agrupaciones'] : '';
                                                $groupFields = array_map('trim', explode(',', $subtotalesAgrupaciones));
                                                
                                                if (in_array($column, $groupFields)) {
                                                    if ($column === reset($groupFields)) {
                                                        echo '<strong>Subtotal: ' . htmlspecialchars($value) . '</strong>';
                                                    } else {
                                                        echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                    }
                                                    continue;
                                                }
                                            }
                                        }
                                        
                                        // Procesamiento normal para filas regulares
                                        // Detectar si parece contener HTML (case-insensitive)
                                        if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
                                            // Convertir etiquetas comunes a minúsculas para detección consistente
                                            $valueLower = strtolower($value);
                                            
                                            // Verificar tipos de HTML permitidos (minúsculas o mayúsculas)
                                            if (strpos($valueLower, '<img') !== false || 
                                                strpos($valueLower, '<a') !== false || 
                                                strpos($valueLower, '<center') !== false ||
                                                strpos($valueLower, '<div') !== false) {
                                                
                                                // Arreglar enlaces HTML sin comillas en los atributos
                                                if (preg_match('/<a\s+href=([^"\'>]+)([^>]*)>/i', $value)) {
                                                    $value = preg_replace('/(<a\s+href=)([^"\'>]+)([^>]*)>/i', '$1"$2"$3>', $value);
                                                }
                                                
                                                // Es HTML permitido, mostrarlo como tal
                                                echo $value;
                                            } else {
                                                // Es HTML pero no de los tipos permitidos, escapar
                                                echo htmlspecialchars($value);
                                            }
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
    
    <!-- Script para ocultar el div de carga en ambiente productivo -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Buscar el div de carga por su ID
            var loaderDiv = document.getElementById('nmloader_div');
            
            // Si existe, ocultarlo 
            if (loaderDiv) {
                loaderDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html>