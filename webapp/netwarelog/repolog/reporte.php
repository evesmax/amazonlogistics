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
        
        // Para depuración
        error_log("Reporte ID: " . $_SESSION['repolog_report_id']);
        error_log("Información del reporte: " . print_r($reportInfo, true));
        
        // Registro para depuración
        error_log("Configuración de subtotales desde BD: Agrupaciones = " . 
                  (isset($_SESSION['subtotales_agrupaciones']) ? $_SESSION['subtotales_agrupaciones'] : 'No definido') . 
                  ", Campos a sumar = " . 
                  (isset($_SESSION['subtotales_subtotal']) ? $_SESSION['subtotales_subtotal'] : 'No definido'));
        
        $pdo = null;
    } catch (PDOException $e) {
        // Si hay error, mantenemos el título genérico
        error_log("Error al obtener información del reporte: " . $e->getMessage());
        
        // Log del error
        error_log("No se pudo obtener la configuración de subtotales de la base de datos.");
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

// Obtener datos directamente de la base de datos para depuración
$debug_db_values = [];
if (isset($_SESSION['repolog_report_id'])) {
    try {
        $debug_pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $debug_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $debug_stmt = $debug_pdo->prepare("SELECT idreporte, nombrereporte, subtotales_agrupaciones, subtotales_subtotal FROM repolog_reportes WHERE idreporte = ?");
        $debug_stmt->execute([$_SESSION['repolog_report_id']]);
        $debug_db_values = $debug_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si no hay valores en la sesión pero sí en la base de datos, actualizar
        if (empty($subtotalesAgrupaciones) && !empty($debug_db_values['subtotales_agrupaciones'])) {
            $subtotalesAgrupaciones = $debug_db_values['subtotales_agrupaciones'];
            $_SESSION['subtotales_agrupaciones'] = $subtotalesAgrupaciones;
        }
        
        if (empty($subtotalesSubtotal) && !empty($debug_db_values['subtotales_subtotal'])) {
            $subtotalesSubtotal = $debug_db_values['subtotales_subtotal'];
            $_SESSION['subtotales_subtotal'] = $subtotalesSubtotal;
        }
        
        $debug_pdo = null;
    } catch (Exception $e) {
        $debug_db_values = ['error' => $e->getMessage()];
    }
}

// Aplicar subtotales si hay resultados y si están configurados explícitamente
if (!empty($results)) {
    // Verificar si ambos campos de configuración de subtotales están presentes en la base de datos
    $subtotalesConfiguradosExplicitamente = false;
    
    // Revisar si tenemos valores en la base de datos para este reporte
    if (isset($_SESSION['repolog_report_id'])) {
        try {
            $check_pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
            $check_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $check_stmt = $check_pdo->prepare("SELECT subtotales_agrupaciones, subtotales_subtotal FROM repolog_reportes WHERE idreporte = ?");
            $check_stmt->execute([$_SESSION['repolog_report_id']]);
            $check_result = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si al menos uno de los campos está configurado
            if ($check_result) {
                // Siempre actualizar con los valores de la base de datos, tengan contenido o no
                $subtotalesAgrupaciones = $check_result['subtotales_agrupaciones'];
                $subtotalesSubtotal = $check_result['subtotales_subtotal'];
                $_SESSION['subtotales_agrupaciones'] = $subtotalesAgrupaciones;
                $_SESSION['subtotales_subtotal'] = $subtotalesSubtotal;
                
                // Solo activar subtotales si al menos uno de los campos tiene contenido
                if (!empty($check_result['subtotales_agrupaciones']) || !empty($check_result['subtotales_subtotal'])) {
                    $subtotalesConfiguradosExplicitamente = true;
                }
            }
            
            $check_pdo = null;
        } catch (Exception $e) {
            error_log("Error verificando configuración de subtotales: " . $e->getMessage());
        }
    }
    
    // Solo calcular subtotales si están configurados explícitamente
    if ($subtotalesConfiguradosExplicitamente) {
        // Si al menos hay campos para totalizar, aplicar subtotales
        if (!empty($subtotalesSubtotal)) {
            $hasSubtotals = true;
            
            // Si no hay campos de agrupación, usaremos solo totales generales
            $groupingFields = !empty($subtotalesAgrupaciones) ? $subtotalesAgrupaciones : '';
            
            // Guardar para depuración
            $_SESSION['debug_process_subtotals'] = array(
                'groupingFields' => $groupingFields,
                'subtotalesSubtotal' => $subtotalesSubtotal
            );
            
            $results = processSubtotals($results, $groupingFields, $subtotalesSubtotal);
        }
        // Si solo tenemos campos de agrupación pero no campos de suma
        else if (!empty($subtotalesAgrupaciones)) {
            // Intentar detectar campos numéricos para totalizar
            $firstRow = reset($results);
            $numericFields = [];
            
            // Buscar campos numéricos en la primera fila de resultados
            foreach ($firstRow as $key => $value) {
                if (is_numeric($value) || 
                    (is_string($value) && is_numeric(preg_replace('/[^\d.-]/', '', $value)))) {
                    $numericFields[] = $key;
                }
            }
            
            // Si encontramos campos numéricos, calcular subtotales
            if (!empty($numericFields)) {
                $hasSubtotals = true;
                // Usar todos los campos numéricos encontrados, no solo el primero
                $results = processSubtotals($results, $subtotalesAgrupaciones, implode(',', $numericFields));
            }
        }
    } else {
        // No calcular subtotales si no están configurados
        error_log("Subtotales no configurados explícitamente para el reporte " . (isset($_SESSION['repolog_report_id']) ? $_SESSION['repolog_report_id'] : 'desconocido'));
    }
}
    
    // Comentario eliminado para producción

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
/**
 * Función optimizada para convertir todos los valores numéricos a formato float para cálculos
 * Esta función maneja correctamente los formatos:
 * - Americano/Mexicano: 1,234.56 (coma como separador de miles, punto como decimal)
 * - Europeo simple: 1234,56 (coma como decimal)
 * - Europeo completo: 1.234,56 (punto como separador de miles, coma como decimal)
 * - Numérico simple: 1234.56 o 1234
 */
function fixAmericanNumberFormat(&$data) {
    // Si no hay datos, retornar
    if (empty($data)) {
        return;
    }
    
    // Obtener las claves de la primera fila para identificar columnas numéricas
    $firstRow = reset($data);
    $numericColumns = [];
    
    // Identificar TODAS las columnas que podrían contener valores numéricos
    foreach ($firstRow as $key => $value) {
        // Verificar si parece ser un campo numérico o con formato numérico
        if (is_numeric($value) || 
            (is_string($value) && 
             (preg_match('/^[\d,.]+$/', $value) || 
              preg_match('/^-[\d,.]+$/', $value)))) { // Permitir negativos también
            $numericColumns[] = $key;
        }
    }
    
    // Procesar todas las filas y convertir explícitamente los valores
    foreach ($data as &$row) {
        foreach ($numericColumns as $column) {
            if (isset($row[$column])) {
                $originalValue = $row[$column];
                
                // Si ya es un número, no necesitamos convertirlo
                if (is_numeric($originalValue) && !is_string($originalValue)) {
                    continue;
                }
                
                // Convertir a string para poder procesar
                $stringValue = strval($originalValue);
                
                // 1. Detectar formato americano (1,234.56) - ESTE ES EL FORMATO OBJETIVO
                if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $stringValue)) {
                    // Quitar las comas y convertir a float
                    $cleanValue = str_replace(',', '', $stringValue);
                    $row[$column] = floatval($cleanValue);
                }
                // 2. Detectar formato simple con punto decimal (1234.56)
                else if (preg_match('/^\d+\.\d+$/', $stringValue)) {
                    // Ya tiene el formato correcto, simplemente convertir a float
                    $row[$column] = floatval($stringValue);
                }
                // 3. Detectar formato europeo simple con coma decimal (1234,56)
                else if (preg_match('/^\d+,\d+$/', $stringValue)) {
                    // Reemplazar coma por punto para el formato correcto
                    $cleanValue = str_replace(',', '.', $stringValue);
                    $row[$column] = floatval($cleanValue);
                }
                // 4. Detectar formato europeo completo (1.234,56)
                else if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)$/', $stringValue)) {
                    // Quitar puntos y reemplazar coma por punto
                    $cleanValue = str_replace('.', '', $stringValue);
                    $cleanValue = str_replace(',', '.', $cleanValue);
                    $row[$column] = floatval($cleanValue);
                }
                // 5. Caso especial para números enteros con comas (1,234)
                else if (preg_match('/^\d{1,3}(,\d{3})+$/', $stringValue)) {
                    // Quitar las comas y convertir a float
                    $cleanValue = str_replace(',', '', $stringValue);
                    $row[$column] = floatval($cleanValue);
                }
                // 6. Números enteros simples
                else if (preg_match('/^\d+$/', $stringValue)) {
                    $row[$column] = floatval($stringValue);
                }
                
                // Guardar para depuración
                if (isset($cleanValue) && $stringValue !== $cleanValue) {
                    $_SESSION['american_format_fixes'][] = [
                        'column' => $column,
                        'original' => $originalValue,
                        'cleaned' => isset($cleanValue) ? $cleanValue : $stringValue,
                        'fixed' => $row[$column]
                    ];
                }
            }
        }
    }
}

function processSubtotals($data, $groupingFields, $totalFields) {
    // PRE-PROCESAMIENTO: Corregir valores numéricos en formato americano
    fixAmericanNumberFormat($data);
    
    // Para depuración, almacenar los parámetros originales
    $_SESSION['debug_subtotals_params'] = array(
        'groupingFields' => $groupingFields,
        'totalFields' => $totalFields,
        'data_sample' => !empty($data) ? array_slice($data, 0, 3) : 'empty'
    );
    
    // Inicializar arrays para depuración
    $_SESSION['debug_number_conversion'] = array();
    $_SESSION['debug_field_mapping'] = array();
    $_SESSION['debug_american_format'] = array();
    $_SESSION['american_format_fixes'] = array();
    
    // Log para depuración
    error_log("processSubtotals: Campos a agrupar = $groupingFields, Campos a sumar = $totalFields");
    
    // Guarda información más detallada para depuración
    $_SESSION['subtotales_subtotal_original'] = $totalFields;
    $_SESSION['subtotales_agrupaciones_original'] = $groupingFields;
    
    // Si no hay datos, retornar vacío
    if (empty($data)) {
        return $data;
    }
    
    // Si no hay campos para totalizar, retornar los datos originales
    if (empty($totalFields)) {
        return $data;
    }
    
    // Convertir las cadenas de campos a arrays
    $groupFields = array_map('trim', explode(',', $groupingFields));
    $sumFields = array_map('trim', explode(',', $totalFields));
    
    // Crear mapeo de nombres de campo SQL a nombres de columna visibles
    $columnMapping = [];
    
    // Lista de campos que pueden requerir mapeo por reporte específico
    $reportSpecificMappings = [];
    
    // Mapeos generales conocidos 
    $generalKnownMappings = [
        // Mapeos para campos de agrupación comunes
        'of.nombrefabricante' => ['Propietario', 'Ingenio'],
        'ob.nombrebodega' => ['Bodega', 'Bodega Destino'],
        'obo.nombrebodega' => ['Bodega Destino'],
        'vm.nombremarca' => ['Marca'],
        
        // Mapeos para campos de suma comunes
        'lr.cantidadrecibida1' => ['Cantidad Recibida (bts)'],
        'lr.cantidadrecibida2' => ['Cantidad Recibida (tm)'],
        'cantidadrecibida1' => ['Cantidad Recibida (bts)'],
        'cantidadrecibida2' => ['Cantidad Recibida (tm)'],
        'ik.inventarioinicial' => ['InventarioInicial', 'Inventario Inicial', 'Inventario'],
        'ik.entradastotales' => ['EntradasTotales', 'Entradas Totales', 'Entradas'],
        'ik.porfacturar' => ['PorFacturar', 'TotalFacturacion', 'Total Facturacion']
    ];
    
    // Crear lista plana de todos los posibles mapeos
    $flatMappings = [];
    foreach ($generalKnownMappings as $sqlField => $possibleColumns) {
        foreach ($possibleColumns as $column) {
            $flatMappings[$sqlField] = $column;
        }
    }
    
    // Tratar casos específicos para reportes
    if (isset($_SESSION['repolog_report_id'])) {
        $reportId = $_SESSION['repolog_report_id'];
        
        if ($reportId == 23) {
            // Mapeos específicos para el reporte 23
            $reportSpecificMappings = [
                'ob.nombrebodega' => 'Bodega',
                'of.nombrefabricante' => 'Ingenio',
                'vm.nombremarca' => 'Marca',
                'ik.inventarioinicial' => 'InventarioInicial',
                'ik.inventarioinicialtm' => 'InventarioInicialTM', 
                'ik.entradastotales' => 'EntradasTotales',
                'ik.porfacturar' => 'TotalFacturacion'
            ];
        }
        else if ($reportId == 7) {
            // Mapeos específicos para el reporte 7
            $reportSpecificMappings = [
                'of.nombrefabricante' => 'Ingenio',
                'obo.nombrebodega' => 'Bodega Destino',
                'obd.nombrebodega' => 'Bodega Origen',
                'lr.cantidadrecibida1' => 'Cantidad Recibida (bts)',
                'lr.cantidadrecibida2' => 'Cantidad Recibida (TM)',
                'cantidadrecibida1' => 'Cantidad Recibida (bts)',
                'cantidadrecibida2' => 'Cantidad Recibida (TM)'
            ];
        }
    }
    
    // Verificar que los campos existan en los datos y crear mapeo
    $firstRow = reset($data);
    $validGroupFields = [];
    
    error_log("Campos de agrupación originales: " . print_r($groupFields, true));
    error_log("Campos a sumar originales: " . print_r($sumFields, true));
    error_log("Columnas disponibles: " . print_r(array_keys($firstRow), true));
    
    foreach ($groupFields as $field) {
        // 1. Verificar si existe directamente
        if (isset($firstRow[$field])) {
            $validGroupFields[] = $field;
            $columnMapping[$field] = $field;
            continue;
        }
        
        // 2. Verificar si tenemos un mapeo específico para este reporte
        if (isset($reportSpecificMappings[$field])) {
            $mappedColumn = $reportSpecificMappings[$field];
            if (isset($firstRow[$mappedColumn])) {
                $validGroupFields[] = $mappedColumn;
                $columnMapping[$field] = $mappedColumn;
                continue;
            }
        }
        
        // 3. Intentar todos los mapeos generales posibles
        if (isset($generalKnownMappings[$field])) {
            $found = false;
            foreach ($generalKnownMappings[$field] as $possibleColumn) {
                if (isset($firstRow[$possibleColumn])) {
                    $validGroupFields[] = $possibleColumn;
                    $columnMapping[$field] = $possibleColumn;
                    $found = true;
                    break;
                }
            }
            if ($found) continue;
        }
        
        // 4. Intentar búsqueda inteligente por el nombre base del campo
        $fieldParts = explode('.', $field);
        $baseName = end($fieldParts);
        
        foreach (array_keys($firstRow) as $column) {
            if (stripos($column, $baseName) !== false || 
                (function_exists('levenshtein') && levenshtein(strtolower($column), strtolower($baseName)) <= 3)) {
                $validGroupFields[] = $column;
                $columnMapping[$field] = $column;
                
                error_log("Mapeo inteligente: Campo SQL '$field' mapeado a columna '$column'");
                break;
            }
        }
    }
    
    $validSumFields = [];
    foreach ($sumFields as $field) {
        $fieldTrim = trim($field);
        
        // 1. Verificar si existe directamente
        if (isset($firstRow[$fieldTrim])) {
            $validSumFields[] = $fieldTrim;
            $columnMapping[$fieldTrim] = $fieldTrim;
            error_log("Campo de suma '$fieldTrim' encontrado directamente");
            continue;
        }
        
        // 2. Verificar si tenemos un mapeo específico para este reporte
        if (isset($reportSpecificMappings[$fieldTrim])) {
            $mappedColumn = $reportSpecificMappings[$fieldTrim];
            if (isset($firstRow[$mappedColumn])) {
                $validSumFields[] = $mappedColumn;
                $columnMapping[$fieldTrim] = $mappedColumn;
                error_log("Campo de suma '$fieldTrim' mapeado específicamente a '$mappedColumn'");
                continue;
            }
        }
        
        // 3. Intentar todos los mapeos generales posibles
        if (isset($generalKnownMappings[$fieldTrim])) {
            $found = false;
            foreach ($generalKnownMappings[$fieldTrim] as $possibleColumn) {
                if (isset($firstRow[$possibleColumn])) {
                    $validSumFields[] = $possibleColumn;
                    $columnMapping[$fieldTrim] = $possibleColumn;
                    $found = true;
                    error_log("Campo de suma '$fieldTrim' mapeado de general a '$possibleColumn'");
                    break;
                }
            }
            if ($found) continue;
        }
        
        // 4. Intentar búsqueda inteligente por el nombre base del campo
        $fieldParts = explode('.', $fieldTrim);
        $baseName = end($fieldParts);
        
        foreach (array_keys($firstRow) as $column) {
            if (stripos($column, $baseName) !== false || 
                (function_exists('levenshtein') && levenshtein(strtolower($column), strtolower($baseName)) <= 3)) {
                $validSumFields[] = $column;
                $columnMapping[$fieldTrim] = $column;
                
                error_log("Mapeo inteligente: Campo SQL '$fieldTrim' mapeado a columna '$column'");
                break;
            }
        }
        
        // 5. Si llegamos aquí, significa que no pudimos encontrar un mapeo. Registrarlo para depuración
        if (!in_array($fieldTrim, $validSumFields) && !array_key_exists($fieldTrim, $columnMapping)) {
            error_log("ADVERTENCIA: No se encontró mapeo para el campo de suma '$fieldTrim'");
            
            // Como último recurso, buscar campos similares o con parte del nombre
            foreach (array_keys($firstRow) as $column) {
                if (stripos($column, 'cantidad') !== false || 
                    stripos($column, 'total') !== false ||
                    stripos($column, 'sum') !== false ||
                    stripos($column, 'bts') !== false ||
                    stripos($column, 'tm') !== false) {
                    
                    $validSumFields[] = $column;
                    $columnMapping[$fieldTrim] = $column;
                    error_log("Mapeo de último recurso: Campo SQL '$fieldTrim' mapeado a columna numérica '$column'");
                    break;
                }
            }
        }
    }
    
    // Agregar información de mapeo a la sesión para depuración
    $_SESSION['debug_column_mapping'] = array(
        'original_group_fields' => $groupFields,
        'original_sum_fields' => $sumFields,
        'valid_group_fields' => $validGroupFields,
        'valid_sum_fields' => $validSumFields,
        'column_mapping' => $columnMapping
    );
    
    // Si no hay campos válidos para totalizar, retornar los datos originales
    if (empty($validSumFields)) {
        return $data;
    }
    
    // Si no hay campos de agrupación válidos, permitir calcular solo totales generales
    $calculateOnlyGrandTotals = empty($validGroupFields);
    
    // Procesamos los subtotales y totales
    $result = array();
    $subtotals = array();
    $grandTotals = array();
    
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
                $subtotalRow = array();
                
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
            $currentSubtotal = array();
            foreach ($validSumFields as $field) {
                $currentSubtotal[$field] = 0;
            }
        }
        
        // Si es el primer registro o un nuevo grupo, inicializar subtotales
        if ($currentGroup === null || $currentGroup !== $groupKey) {
            $currentGroup = $groupKey;
            
            if ($currentSubtotal === null) {
                $currentSubtotal = array();
                foreach ($validSumFields as $field) {
                    $currentSubtotal[$field] = 0;
                }
            }
        }
        
        // Agregar el registro actual a los resultados
        $result[] = $row;
        
        // Actualizar subtotales y totales generales
        foreach ($validSumFields as $field) {
            // Los valores ya fueron procesados por fixAmericanNumberFormat
            // Solo necesitamos convertir a número si todavía es una cadena
            $rawValue = $row[$field];
            $value = is_numeric($rawValue) ? floatval($rawValue) : 0;
            
            // Actualizar subtotales y totales
            $currentSubtotal[$field] += $value;
            $grandTotals[$field] += $value;
        }
        
        // Guardar el último registro del grupo para referencia
        $subtotals['lastRow'] = $row;
        
        // Guardar referencia para el último registro del grupo
        $subtotals['current_group_rows'] = $row;
    }
    
    // Agregar subtotal del último grupo
    if ($currentSubtotal !== null) {
        $subtotalRow = array();
        
        // Copiar los valores de agrupación del último registro
        foreach ($validGroupFields as $field) {
            $subtotalRow[$field] = $subtotals['lastRow'][$field];
        }
        
        // Usar el valor calculado normal para todos los campos
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
    $totalRow = array();
    
    // Dejar en blanco los campos de agrupación, excepto el primero que mostrará "TOTAL GENERAL"
    $firstField = true;
    foreach ($validGroupFields as $field) {
        if ($firstField) {
            $totalRow[$field] = 'TOTAL GENERAL';
            $firstField = false;
        } else {
            $totalRow[$field] = '';
        }
    }
    
    // Si no hay campos de agrupación, asignaremos "TOTAL GENERAL" a la primera columna
    if (empty($validGroupFields) && !empty($firstRow)) {
        // array_key_first no está disponible en PHP < 7.3, usar alternativa compatible
        reset($firstRow);
        $firstColumn = key($firstRow);
        $totalRow[$firstColumn] = 'TOTAL GENERAL';
    }
    
    // Agregar los totales generales - usar valores normales
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
                
                <!-- Botón de depuración de subtotales removido -->
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
                                            
                                            $columnMapping = isset($_SESSION['debug_column_mapping']) && isset($_SESSION['debug_column_mapping']['column_mapping']) ? 
                                                              $_SESSION['debug_column_mapping']['column_mapping'] : [];
                                            $mappedSumFields = [];
                                            
                                            // Convertir los campos SQL a campos de visualización
                                            foreach ($sumFields as $field) {
                                                // Verificar primero el mapeo específico del reporte
                                                $fieldTrimmed = trim($field);
                                                
                                                // Depuración
                                                if (!isset($_SESSION['debug_field_mapping_sql_to_display'])) {
                                                    $_SESSION['debug_field_mapping_sql_to_display'] = [];
                                                }
                                                // Usar array asociativo en lugar de corchetes para compatibilidad con PHP 5.5.9
                                                $_SESSION['debug_field_mapping_sql_to_display'][] = array(
                                                    'sql_field' => $fieldTrimmed,
                                                    'lookup_in' => 'columnMapping',
                                                    'column_mapping' => $columnMapping
                                                );
                                                
                                                if (isset($columnMapping[$fieldTrimmed])) {
                                                    $mapped = $columnMapping[$fieldTrimmed];
                                                    $mappedSumFields[] = $mapped;
                                                    $_SESSION['debug_field_mapping_sql_to_display'][] = array(
                                                        'sql_field' => $fieldTrimmed,
                                                        'mapped_to' => $mapped,
                                                        'via' => 'column_mapping'
                                                    );
                                                } else {
                                                    // Intentar buscar por coincidencia parcial
                                                    $matchFound = false;
                                                    $fieldBase = $fieldTrimmed;
                                                    if (strpos($fieldTrimmed, '.') !== false) {
                                                        $parts = explode('.', $fieldTrimmed);
                                                        $fieldBase = end($parts);
                                                    }
                                                    
                                                    // Intentar mapeo directo si coincide nombre con una columna disponible
                                                    foreach ($columns as $col) {
                                                        // Verificar si la función levenshtein existe en esta versión de PHP
                                                        if (stripos($col, $fieldBase) !== false || 
                                                            (function_exists('levenshtein') && levenshtein(strtolower($col), strtolower($fieldBase)) <= 3)) {
                                                            $mappedSumFields[] = $col;
                                                            $matchFound = true;
                                                            $_SESSION['debug_field_mapping_sql_to_display'][] = array(
                                                                'sql_field' => $fieldTrimmed,
                                                                'mapped_to' => $col,
                                                                'via' => 'similarity'
                                                            );
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Si todavía no encontramos coincidencia, usar el original
                                                    if (!$matchFound) {
                                                        $mappedSumFields[] = $fieldTrimmed;
                                                        $_SESSION['debug_field_mapping_sql_to_display'][] = array(
                                                            'sql_field' => $fieldTrimmed,
                                                            'mapped_to' => $fieldTrimmed,
                                                            'via' => 'no_match_found'
                                                        );
                                                    }
                                                }
                                            }
                                            
                                            if (in_array($column, $mappedSumFields)) {
                                                // Formatear como número con 2 decimales (formato mexicano: 2,990.58)
                                                // Formatear con separador de miles y 2 decimales
                                                if (is_numeric($value)) {
                                                    // Es un número, formatear con comas para miles y punto para decimales
                                                    $formattedValue = number_format(floatval($value), 2, '.', ',');
                                                    echo '<strong>' . $formattedValue . '</strong>';
                                                } else if (is_string($value)) {
                                                    // Si ya tiene formato americano (1,234.56), asegurarse que tiene 2 decimales
                                                    if (preg_match('/^\d{1,3}(,\d{3})*(\.\d+)?$/', $value)) {
                                                        // Extraer el número sin formateo para reformatearlo
                                                        $cleanValue = str_replace(',', '', $value);
                                                        if (is_numeric($cleanValue)) {
                                                            $formattedValue = number_format(floatval($cleanValue), 2, '.', ',');
                                                            echo '<strong>' . $formattedValue . '</strong>';
                                                        } else {
                                                            echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                        }
                                                    } 
                                                    // Si tiene formato europeo simple (1234,56), convertirlo a americano
                                                    else if (preg_match('/^\d+,\d+$/', $value)) {
                                                        $cleanValue = str_replace(',', '.', $value);
                                                        if (is_numeric($cleanValue)) {
                                                            $formattedValue = number_format(floatval($cleanValue), 2, '.', ',');
                                                            echo '<strong>' . $formattedValue . '</strong>';
                                                        } else {
                                                            echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                        }
                                                    }
                                                    // Si tiene formato europeo completo (1.234,56), convertirlo a americano
                                                    else if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)$/', $value)) {
                                                        $cleanValue = str_replace('.', '', $value);
                                                        $cleanValue = str_replace(',', '.', $cleanValue);
                                                        if (is_numeric($cleanValue)) {
                                                            $formattedValue = number_format(floatval($cleanValue), 2, '.', ',');
                                                            echo '<strong>' . $formattedValue . '</strong>';
                                                        } else {
                                                            echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                        }
                                                    }
                                                    // Para cualquier otro caso, intentar convertir y formatear
                                                    else {
                                                        $cleanValue = preg_replace('/[^\d.-]/', '', $value);
                                                        if (is_numeric($cleanValue)) {
                                                            $formattedValue = number_format(floatval($cleanValue), 2, '.', ',');
                                                            echo '<strong>' . $formattedValue . '</strong>';
                                                        } else {
                                                            echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                        }
                                                    }
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
                                                
                                                $columnMapping = isset($_SESSION['debug_column_mapping']) && isset($_SESSION['debug_column_mapping']['column_mapping']) ? 
                                                                  $_SESSION['debug_column_mapping']['column_mapping'] : [];
                                                $mappedGroupFields = [];
                                                
                                                // Convertir los campos SQL a campos de visualización usando el mismo enfoque mejorado
                                                foreach ($groupFields as $field) {
                                                    $fieldTrimmed = trim($field);
                                                    
                                                    // Verificar mapeo directo
                                                    if (isset($columnMapping[$fieldTrimmed])) {
                                                        $mappedGroupFields[] = $columnMapping[$fieldTrimmed];
                                                    } 
                                                    // Buscar por coincidencia parcial
                                                    else {
                                                        $fieldBase = $fieldTrimmed;
                                                        if (strpos($fieldTrimmed, '.') !== false) {
                                                            $parts = explode('.', $fieldTrimmed);
                                                            $fieldBase = end($parts);
                                                        }
                                                        
                                                        $matchFound = false;
                                                        foreach ($columns as $col) {
                                                            if (stripos($col, $fieldBase) !== false || 
                                                                (function_exists('levenshtein') && levenshtein(strtolower($col), strtolower($fieldBase)) <= 3)) {
                                                                $mappedGroupFields[] = $col;
                                                                $matchFound = true;
                                                                break;
                                                            }
                                                        }
                                                        
                                                        if (!$matchFound) {
                                                            $mappedGroupFields[] = $fieldTrimmed;
                                                        }
                                                    }
                                                }
                                                
                                                if (in_array($column, $mappedGroupFields)) {
                                                    if ($column === reset($mappedGroupFields)) {
                                                        echo '<strong>Subtotal: ' . htmlspecialchars($value) . '</strong>';
                                                    } else {
                                                        echo '<strong>' . htmlspecialchars($value) . '</strong>';
                                                    }
                                                    continue;
                                                }
                                            }
                                        }
                                        
                                        // Procesamiento normal para filas regulares
                                        // Verificar si es un número o parece un número formateado
                                        if (is_numeric($value)) {
                                            // Es un número, formatear con comas para miles y punto para decimales
                                            $formattedValue = number_format(floatval($value), 2, '.', ',');
                                            echo '<strong>' . $formattedValue . '</strong>';
                                        }
                                        // Verificar si parece un número en formato europeo (con coma decimal, como 2990,58)
                                        else if (is_string($value) && preg_match('/^[0-9]+,[0-9]+$/', $value)) {
                                            // Es un número con formato europeo (2990,58)
                                            $cleanValue = str_replace(',', '.', $value); // Convertir a formato con punto decimal
                                            $formattedValue = number_format(floatval($cleanValue), 2, '.', ','); // Formato americano/mexicano
                                            echo '<strong>' . $formattedValue . '</strong>';
                                        }
                                        // Verificar si parece un número en formato americano (1,234.56)
                                        else if (is_string($value) && preg_match('/^\d{1,3}(,\d{3})*(\.\d+)?$/', $value)) {
                                            // Extraer el número sin formateo para reformatearlo
                                            $cleanValue = str_replace(',', '', $value);
                                            if (is_numeric($cleanValue)) {
                                                $formattedValue = number_format(floatval($cleanValue), 2, '.', ',');
                                                echo '<strong>' . $formattedValue . '</strong>';
                                            } else {
                                                echo htmlspecialchars($value);
                                            }
                                        }
                                        // Detectar si parece contener HTML (case-insensitive)
                                        else if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
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
            // Store data for JavaScript processing (usando var en lugar de const para compatibilidad)
            var tableData = <?php echo json_encode($results); ?>;
            var tableColumns = <?php echo json_encode($columns); ?>;
            
            // Código para asegurarse que los valores numéricos tienen el formato correcto
            // Pero respetando los formatos originales en lo posible
            document.addEventListener('DOMContentLoaded', function() {
                // Ejecutar formatoNumbersInTable() desde formatNumbersFix.js
                if (typeof formatNumbersInTable === 'function') {
                    formatNumbersInTable();
                }
            });
        </script>
        <script src="assets/js/table_functions.js"></script>
        <script src="assets/js/formatNumbersFix.js"></script>
        <script src="assets/js/formatSpecifics.js"></script>
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
    
    <?php 
    // Sección de depuración eliminada para entorno de producción
    ?>
    
</body>
</html>