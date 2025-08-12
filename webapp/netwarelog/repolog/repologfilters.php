<?php
/**
 * RepoLog Filters Generator
 * 
 * This script receives an id parameter, searches for a report in the database,
 * and generates dynamic filter fields based on SQL conditions.
 * 
 * Compatible with PHP 5.5.9 and MySQL 5.5.62
 */

// Include configuration and utility files
require_once 'config.php';
require_once 'sqlcleaner.php';

// Check if id parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No se proporcionó un ID de reporte.");
}

// Get the report ID from URL
$reportId = intval($_GET['id']);

// Initialize variables
$report = null;
$error = '';
$filters = array();

try {
    // Create a PDO connection
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    
    // Set error mode to throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare and execute the query to get report information including extension fields
    $stmt = $pdo->prepare("SELECT sql_select, sql_from, sql_where, sql_groupby, sql_having, sql_orderby, 
                                 subtotales_agrupaciones, subtotales_subtotal,
                                 url_include, url_include_despues, sppre, sppos, nombrereporte, estatus
                           FROM repolog_reportes 
                           WHERE idreporte = ?");
    $stmt->execute([$reportId]);
    
    // Fetch report data
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Guardar el ID del reporte actual en la sesión para uso en reporte.php
    $_SESSION['current_report_id'] = $reportId;
    
    // Close connection
    $pdo = null;
    
    // Check if report exists
    if (!$report) {
        $error = "El reporte solicitado no existe o ha sido eliminado.";
    } else {
        // Parse sql_where to generate filters
        $filters = parseWhereClause($report['sql_where']);
    }
    
} catch (PDOException $e) {
    $error = "Error de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}

/**
 * Corrige patrones combo mal formados que pueden tener paréntesis desbalanceados
 * o sintaxis incorrecta en el SQL interno
 * 
 * @param string $whereClause SQL WHERE clause con posibles patrones mal formados
 * @return string WHERE clause con patrones corregidos
 */
function fixMalformedComboPatterns($whereClause) {
    // SOLUCIÓN UNIVERSAL: Detectar y corregir patrones mal formados
    // Buscar patrones que contengan ORDER BY fuera del cierre correcto
    $malformedPattern = '/\[@([^;]+);([^;]+);([^;]+);([^)]*)\)\s*ORDER BY[^\]]*\]/i';
    
    if (preg_match($malformedPattern, $whereClause, $match)) {
        $fullPattern = $match[0];
        $label = $match[1];
        $valField = $match[2];
        $desField = $match[3];
        $incompleteSql = $match[4];
        
        // Reconstruir el patrón correctamente cerrando el SELECT apropiadamente
        $correctedSql = $incompleteSql . ' ORDER BY ' . $desField . ')';
        $correctedPattern = "[@$label;$valField;$desField;$correctedSql]";
        
        $whereClause = str_replace($fullPattern, $correctedPattern, $whereClause);
        error_log("Patrón mal formado corregido universalmente: $label");
    }
    
    // Buscar patrones con paréntesis desbalanceados
    $flexiblePattern = '/\[@([^;]+);([^;]+);([^;]+);([^\]]*)\]/';
    preg_match_all($flexiblePattern, $whereClause, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $fullPattern = $match[0];
        $sqlPart = $match[4];
        
        // Verificar balance de paréntesis
        if (stripos($sqlPart, 'select') !== false) {
            $openParens = substr_count($sqlPart, '(');
            $closeParens = substr_count($sqlPart, ')');
            
            if ($openParens > $closeParens) {
                $fixedSqlPart = $sqlPart . str_repeat(')', $openParens - $closeParens);
                $newPattern = "[@{$match[1]};{$match[2]};{$match[3]};$fixedSqlPart]";
                $whereClause = str_replace($fullPattern, $newPattern, $whereClause);
                error_log("Paréntesis balanceados en patrón: {$match[1]}");
            }
        }
    }
    
    return $whereClause;
}

/**
 * Parse WHERE clause to identify filter conditions
 * 
 * @param string $whereClause SQL WHERE clause
 * @return array Array of filter objects
 */
function parseWhereClause($whereClause) {
    $filters = array();
    
    // Regular expressions to match different filter patterns
    $regularPattern = '/\[([^\]]+)\]/'; // Matches [FilterName]
    $datePattern = '/\[#([^\]]+)\]/';  // Matches [#FilterName]
    $comboPattern = '/\[@([^;]+);([^;]+);([^;]+);([^;]+)(?:;([^;\]]+))?\]/'; // Matches [@Label;val;des;SQL;@Multiselection?]
    $sessionPattern = '/\[!([^\]]+)\]/'; // Matches [!SessionVariable]
    
    // Find all regular filters [FilterName]
    preg_match_all($regularPattern, $whereClause, $regularMatches);
    foreach ($regularMatches[1] as $match) {
        // Skip filters that start with #, @ or ! as they'll be handled by other patterns
        if (strpos($match, '#') === 0 || strpos($match, '@') === 0 || strpos($match, '!') === 0) {
            continue;
        }
        
        $filters[] = [
            'type' => 'text',
            'label' => $match,
            'id' => 'filter_' . sanitizeId($match),
            'placeholder' => 'Escriba ' . $match . '...',
            'original_name' => $match, // Original name as it appears in SQL
            'sql_pattern' => "[$match]" // SQL pattern to be replaced
        ];
    }
    
    // Find all date filters [#FilterName]
    preg_match_all($datePattern, $whereClause, $dateMatches);
    foreach ($dateMatches[1] as $match) {
        $filters[] = [
            'type' => 'date',
            'label' => $match,
            'id' => 'filter_' . sanitizeId($match),
            'placeholder' => 'Seleccione ' . $match . '...',
            'original_name' => $match, // Original name as it appears in SQL
            'sql_pattern' => "[#$match]" // SQL pattern to be replaced
        ];
    }
    
    // Find all combo filters [@Label;val;des;SQL] or [@Label;val;des;SQL;@Multiselection]
    preg_match_all($comboPattern, $whereClause, $comboMatches, PREG_SET_ORDER);
    foreach ($comboMatches as $match) {
        $label = $match[1];
        $valueField = $match[2];
        $displayField = $match[3];
        $sql = $match[4];
        $fullPattern = $match[0]; // Full match including [@...] pattern
        
        // Check if multiselection is enabled (5th parameter should be @Multiselection)
        $isMultiselection = false;
        if (isset($match[5]) && trim($match[5]) === '@Multiselection') {
            $isMultiselection = true;
        }
        
        $filters[] = [
            'type' => 'combo',
            'label' => $label,
            'id' => 'filter_' . sanitizeId($label),
            'valueField' => $valueField,
            'displayField' => $displayField,
            'sql' => $sql,
            'options' => getComboOptions($sql, $valueField, $displayField),
            'original_name' => $label, // Original name as it appears in SQL
            'sql_pattern' => $fullPattern, // SQL pattern to be replaced
            'multiselection' => $isMultiselection // Flag to indicate if multiselection is enabled
        ];
    }
    
    // Find all session variables [!SessionVariable]
    // Note: These don't create visible filters, but are processed during SQL generation
    preg_match_all($sessionPattern, $whereClause, $sessionMatches);
    foreach ($sessionMatches[1] as $match) {
        // Store in a hidden field for processing later
        // Obtener valor de la variable de sesión si existe, de lo contrario usar un valor por defecto
        $sessionValue = isset($_SESSION[$match]) ? $_SESSION[$match] : '0';
        
        $filters[] = [
            'type' => 'session',
            'name' => $match,
            'id' => 'session_' . sanitizeId($match),
            'value' => $sessionValue, // Usar el valor de la sesión
            'original_name' => $match, // Original name as it appears in SQL
            'sql_pattern' => "[!$match]" // SQL pattern to be replaced
        ];
    }
    
    return $filters;
}

/**
 * Get combo options from database using provided SQL
 * 
 * @param string $sql SQL query
 * @param string $valueField Field name for option value
 * @param string $displayField Field name for option display text
 * @return array Array of options
 */
function getComboOptions($sql, $valueField, $displayField) {
    $options = array();
    
    try {
        // Create a PDO connection
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        
        // Set error mode to throw exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Execute the query
        $stmt = $pdo->query($sql);
        
        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add "Todos" option explicitly as first option - this is important
        // Value is empty string which will trigger the "filterEmpty" condition
        $options[] = [
            'value' => '',  // Empty value triggers "Todos" handling in processFilters
            'text' => '-- Todos --'
        ];
        
        // Format results as options array
        foreach ($results as $row) {
            // Ensure values are properly formatted
            $optionValue = isset($row[$valueField]) ? $row[$valueField] : '';
            $optionText = isset($row[$displayField]) ? $row[$displayField] : '';
            
            // Add to options array
            $options[] = [
                'value' => $optionValue,
                'text' => $optionText
            ];
        }
        
        // Close connection
        $pdo = null;
        
    } catch (Exception $e) {
        // On error, at least provide the "Todos" option
        $options = array(
            [
                'value' => '',
                'text' => '-- Todos --'
            ]
        );
    }
    
    return $options;
}

/**
 * Sanitize string for use as HTML ID
 * 
 * @param string $str Input string
 * @return string Sanitized string
 */
function sanitizeId($str) {
    // Remove accents
    $str = preg_replace('/[áàâãªä]/u', 'a', $str);
    $str = preg_replace('/[ÁÀÂÃÄ]/u', 'A', $str);
    $str = preg_replace('/[éèêë]/u', 'e', $str);
    $str = preg_replace('/[ÉÈÊË]/u', 'E', $str);
    $str = preg_replace('/[íìîï]/u', 'i', $str);
    $str = preg_replace('/[ÍÌÎÏ]/u', 'I', $str);
    $str = preg_replace('/[óòôõºö]/u', 'o', $str);
    $str = preg_replace('/[ÓÒÔÕÖ]/u', 'O', $str);
    $str = preg_replace('/[úùûü]/u', 'u', $str);
    $str = preg_replace('/[ÚÙÛÜ]/u', 'U', $str);
    $str = str_replace('ç', 'c', $str);
    $str = str_replace('Ç', 'C', $str);
    $str = str_replace('ñ', 'n', $str);
    $str = str_replace('Ñ', 'N', $str);
    
    // Replace spaces and non-alphanumeric characters with underscore
    $str = preg_replace('/[^a-zA-Z0-9_]/', '_', $str);
    
    // Return lowercase string
    return strtolower($str);
}

/**
 * Build a complete SQL query from report components
 * Each component has its respective SQL keyword added explicitly
 */
/**
 * Función especial para manejar condiciones de fecha en la consulta SQL
 * Esta función:
 * 1. Detecta y elimina condiciones de fecha sin valor
 * 2. Identifica y corrige fechas con formato actual (como 're.fecha="2025/04/19"')
 * 3. Asegura que los paréntesis estén correctamente balanceados alrededor de condiciones de fecha
 */
function processDateConditions($sql) {
    // Primero, buscar y eliminar condiciones con patrones de fecha vacíos
    $datePatterns = [
        // Fechas en condiciones BETWEEN
        '/([^\s]+)\s+BETWEEN\s+\'(\[\#[^\]]+\])\'\s+AND\s+\'(\[\#[^\]]+\])\'/',
        // Fechas en condiciones de igualdad (=) con comillas simples
        '/([^\s]+)\s*=\s*\'(\[\#[^\]]+\])\'/',
        // Fechas en condiciones de igualdad (=) con comillas dobles
        '/([^\s]+)\s*=\s*"(\[\#[^\]]+\])"/',
        // Fechas en condiciones de igualdad (=) sin comillas
        '/([^\s]+)\s*=\s*(\[\#[^\]]+\])/',
        // Fechas en condiciones mayores/menores (>, <, >=, <=) con comillas
        '/([^\s]+)\s*([><]=?)\s*\'(\[\#[^\]]+\])\'/',
        // Fechas en condiciones mayores/menores (>, <, >=, <=) sin comillas
        '/([^\s]+)\s*([><]=?)\s*(\[\#[^\]]+\])/',
    ];
    
    foreach ($datePatterns as $pattern) {
        if (preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullCondition = $match[0]; // La condición completa
                
                // Buscar la condición en el SQL y verificar si está dentro de un AND/OR
                $pos = strpos($sql, $fullCondition);
                if ($pos !== false) {
                    $beforePos = max(0, $pos - 5);
                    $afterPos = $pos + strlen($fullCondition);
                    
                    $replacePart = $fullCondition;
                    
                    // Si hay un AND antes
                    if (substr($sql, $beforePos, 5) === ' AND ') {
                        $replacePart = ' AND ' . $replacePart;
                    }
                    // Si hay un AND después
                    elseif (substr($sql, $afterPos, 5) === ' AND ') {
                        $replacePart = $replacePart . ' AND ';
                    }
                    // Si hay un OR antes
                    elseif (substr($sql, $beforePos, 4) === ' OR ') {
                        $replacePart = ' OR ' . $replacePart;
                    }
                    // Si hay un OR después
                    elseif (substr($sql, $afterPos, 4) === ' OR ') {
                        $replacePart = $replacePart . ' OR ';
                    }
                    
                    // Eliminar la condición completa
                    $sql = str_replace($replacePart, '', $sql);
                }
            }
        }
    }
    
    // Segundo, buscar patrones con fechas literales en el SQL (ej: re.fecha="2025/04/19")
    // Ya no reemplazamos automáticamente la fecha actual - dejamos que los filtros de usuario funcionen
    $todayDate = date('Y/m/d');
    // Esta parte es solo para casos donde no hay filtros de fecha del usuario
    $literalDatePatterns = [];
    
    // Comprobar si hay filtros de fecha en la sesión
    $hasFechaFilter = false;
    if (isset($_SESSION['filter_values']) && is_array($_SESSION['filter_values'])) {
        foreach ($_SESSION['filter_values'] as $key => $value) {
            if (stripos($key, 'fecha') !== false && !empty($value)) {
                $hasFechaFilter = true;
                break;
            }
        }
    }
    
    // Solo si NO hay filtros de fecha del usuario, entonces buscamos fechas literales
    if (!$hasFechaFilter) {
        $literalDatePatterns = [
            // Fechas literales con comillas dobles
            '/([^\s]+)\.fecha\s*=\s*"' . preg_quote($todayDate, '/') . '"/',
            // Fechas literales con comillas simples
            '/([^\s]+)\.fecha\s*=\s*\'' . preg_quote($todayDate, '/') . '\'/',
        ];
    }
    
    error_log("Filtros de fecha del usuario detectados: " . ($hasFechaFilter ? 'SÍ' : 'NO'));
    
    foreach ($literalDatePatterns as $pattern) {
        if (preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullCondition = $match[0]; // La condición completa con fecha
                
                // Buscar la condición en el SQL y verificar si está dentro de un AND/OR
                $pos = strpos($sql, $fullCondition);
                if ($pos !== false) {
                    $beforePos = max(0, $pos - 5);
                    $afterPos = $pos + strlen($fullCondition);
                    
                    $replacePart = $fullCondition;
                    
                    // Si hay un AND antes
                    if (substr($sql, $beforePos, 5) === ' AND ') {
                        $replacePart = ' AND ' . $replacePart;
                    }
                    // Si hay un AND después
                    elseif (substr($sql, $afterPos, 5) === ' AND ') {
                        $replacePart = $replacePart . ' AND ';
                    }
                    // Si hay un OR antes
                    elseif (substr($sql, $beforePos, 4) === ' OR ') {
                        $replacePart = ' OR ' . $replacePart;
                    }
                    // Si hay un OR después
                    elseif (substr($sql, $afterPos, 4) === ' OR ') {
                        $replacePart = $replacePart . ' OR ';
                    }
                    
                    // Para este caso, mantenemos la condición pero nos aseguramos que no cause problemas
                    // No la eliminamos porque probablemente sea necesaria
                    $sql = str_replace($replacePart, ' ' . trim($fullCondition) . ' ', $sql);
                }
            }
        }
    }
    
    return $sql;
}

/**
 * Función para limpiar la consulta SQL final y arreglar problemas comunes
 * como operadores AND/OR huérfanos y paréntesis desbalanceados,
 * pero solo cuando es realmente necesario
 */
function cleanSqlQuery($sql) {
    // Primero guardamos el SQL original para comparar al final
    $originalSql = $sql;
    
    // Verificar y corregir el error específico "OR DER BY" (ORDER BY mal formado)
    $sql = preg_replace('/\s+OR\s+DER\s+BY\s+/i', ' ORDER BY ', $sql);
    
    // Caso muy específico: cuando OR DER BY aparece justo antes de un paréntesis final
    $sql = preg_replace('/\s+OR\s+DER\s+BY\s+([a-zA-Z0-9_.]+)\)/i', ' ORDER BY $1', $sql);
    
    // Eliminar AND/OR huérfanos (que no tienen condiciones a ambos lados)
    $sql = preg_replace('/\(\s*AND\s+/i', '(', $sql);
    $sql = preg_replace('/\(\s*OR\s+/i', '(', $sql);
    $sql = preg_replace('/\s+AND\s*\)/i', ')', $sql);
    $sql = preg_replace('/\s+OR\s*\)/i', ')', $sql);
    
    // Eliminar AND/OR al inicio o final de la cláusula WHERE
    $sql = preg_replace('/WHERE\s+AND\s+/i', 'WHERE ', $sql);
    $sql = preg_replace('/WHERE\s+OR\s+/i', 'WHERE ', $sql);
    $sql = preg_replace('/\s+AND\s+ORDER\s+BY/i', ' ORDER BY', $sql);
    $sql = preg_replace('/\s+OR\s+ORDER\s+BY/i', ' ORDER BY', $sql);
    $sql = preg_replace('/\s+AND\s+GROUP\s+BY/i', ' GROUP BY', $sql);
    $sql = preg_replace('/\s+OR\s+GROUP\s+BY/i', ' GROUP BY', $sql);
    
    // Caso especial para il.descripcionlote
    $sql = preg_replace('/\(\s*AND\s+il\.descripcionlote/i', '(il.descripcionlote', $sql);
    
    // AND/OR consecutivos sin condiciones entre ellos
    $sql = preg_replace('/\s+AND\s+AND\s+/i', ' AND ', $sql);
    $sql = preg_replace('/\s+OR\s+OR\s+/i', ' OR ', $sql);
    $sql = preg_replace('/\s+AND\s+OR\s+/i', ' OR ', $sql);
    $sql = preg_replace('/\s+OR\s+AND\s+/i', ' AND ', $sql);
    
    // Múltiples espacios en blanco
    $sql = preg_replace('/\s+/', ' ', $sql);
    
    // Contar paréntesis abiertos y cerrados
    $openCount = substr_count($sql, '(');
    $closeCount = substr_count($sql, ')');
    
    // Caso específico para el patrón de Zafra que causa problemas
    // (il.idloteproducto = 15 and
    $sql = preg_replace('/\(il\.idloteproducto\s*=\s*(\d+)\s+and/i', '(il.idloteproducto = $1) AND', $sql);
    
    // Balancear paréntesis en toda la consulta si están desbalanceados
    if ($openCount != $closeCount) {
        // Si hay más abiertos que cerrados, agregar cerrados al final
        if ($openCount > $closeCount) {
            $diff = $openCount - $closeCount;
            // Para desbalances más grandes, agregar todos los paréntesis de cierre necesarios al final
            $sql .= str_repeat(')', $diff);
        }
        // Si hay más cerrados que abiertos, agregar abiertos al principio de la cláusula WHERE
        else if ($closeCount > $openCount) {
            $diff = $closeCount - $openCount;
            // Buscar WHERE y agregar paréntesis allí
            $wherePos = stripos($sql, 'WHERE');
            if ($wherePos !== false) {
                $sql = substr($sql, 0, $wherePos + 6) . str_repeat('(', $diff) . substr($sql, $wherePos + 6);
            } else {
                // Si no hay WHERE, solo equilibrar eliminando paréntesis de cierre extra
                $sql = preg_replace('/\)+$/', substr(')', 0, $openCount), $sql);
            }
        }
    }
    
    // Caso específico para paréntesis antes de ORDER BY, GROUP BY, etc.
    // Verificar si hay paréntesis sin cerrar justo antes de otras cláusulas
    $orderByPos = stripos($sql, 'ORDER BY');
    $groupByPos = stripos($sql, 'GROUP BY');
    $havingPos = stripos($sql, 'HAVING');
    
    // Obtener la posición más cercana (no -1) para saber dónde insertar los paréntesis
    $positions = array_filter([$orderByPos, $groupByPos, $havingPos], function($pos) {
        return $pos !== false;
    });
    
    if (!empty($positions)) {
        $minPos = min($positions);
        
        // Contar paréntesis antes de esta posición
        $beforeClause = substr($sql, 0, $minPos);
        $openBefore = substr_count($beforeClause, '(');
        $closeBefore = substr_count($beforeClause, ')');
        
        // Si hay más abiertos que cerrados antes de la cláusula, añadir cerrados
        if ($openBefore > $closeBefore) {
            $diff = $openBefore - $closeBefore;
            $sqlStart = substr($sql, 0, $minPos);
            $sqlEnd = substr($sql, $minPos);
            $sql = $sqlStart . str_repeat(')', $diff) . ' ' . $sqlEnd;
        }
    }
    
    // Eliminar paréntesis vacíos ()
    $sql = preg_replace('/\(\s*\)/', '', $sql);
    
    // Eliminar espacios entre paréntesis
    $sql = preg_replace('/\(\s+/', '(', $sql);
    $sql = preg_replace('/\s+\)/', ')', $sql);
    
    // Corregir espacios alrededor de los operadores AND/OR
    $sql = preg_replace('/\s*AND\s*/', ' AND ', $sql);
    $sql = preg_replace('/\s*OR\s*/', ' OR ', $sql);
    
    // Asegurarnos de que no haya múltiples paréntesis de cierre al final
    $sql = rtrim($sql);
    if (substr($sql, -2) === '))') {
        // Verificar si realmente necesitamos ambos
        $tempSql = substr($sql, 0, -1); // Quitar el último paréntesis
        $openInTemp = substr_count($tempSql, '(');
        $closeInTemp = substr_count($tempSql, ')');
        
        if ($openInTemp == $closeInTemp) {
            $sql = $tempSql;
        }
    }
    
    // Caso muy específico: cuando hay un paréntesis al final después de ORDER BY
    if (preg_match('/ORDER\s+BY\s+[a-zA-Z0-9_.]+\)(\s*)$/i', $sql)) {
        $sql = preg_replace('/ORDER\s+BY\s+([a-zA-Z0-9_.]+)\)(\s*)$/i', 'ORDER BY $1$2', $sql);
    }
    
    return $sql;
}

/**
 * Función para reemplazar patrones de combo no sustituidos en la consulta SQL
 * Esta solución es efectiva cuando hay múltiples filtros combinados y algunos 
 * patrones [@Filtro;val;des;...] permanecen en la consulta sin ser sustituidos
 * 
 * @param string $sql Consulta SQL con patrones a reemplazar
 * @param array $filters Array de filtros disponibles
 * @param array $filterValues Valores seleccionados por el usuario
 * @return string Consulta SQL con patrones reemplazados
 */
function reemplazarPatronesComboNoSustituidos($sql, $filters, $filterValues) {
    // Log para depuración
    error_log("Buscando patrones de combo no sustituidos en SQL");
    
    // Si no hay filtros o valores, no hay nada que hacer
    if (empty($filters) || empty($filterValues)) {
        return $sql;
    }
    
    // Imprimir todas las claves disponibles en filterValues para depuración
    error_log("Valores de filtros disponibles: " . implode(", ", array_keys($filterValues)));
    
    // MEJORA: Buscar específicamente el patrón problemático de BodegaOrigen
    if (strpos($sql, '[@BodegaOrigen') !== false) {
        error_log("ENCONTRADO patrón específico de BodegaOrigen - buscando valor en filtros");
        
        // Buscar el filtro específico para BodegaOrigen en varias formas posibles
        $bodegaOrigenKeys = [
            'filter_bodegaorigen',
            'filter_BodegaOrigen',
            'filter_Bodega_Origen',
            'filter_bodega_origen'
        ];
        
        $bodegaOrigenValue = null;
        foreach ($bodegaOrigenKeys as $key) {
            if (isset($filterValues[$key]) && !empty($filterValues[$key])) {
                $bodegaOrigenValue = $filterValues[$key];
                error_log("Encontrado valor específico para BodegaOrigen: $bodegaOrigenValue con clave $key");
                break;
            }
        }
        
        // Si encontramos un valor, reemplazar el patrón directamente
        if ($bodegaOrigenValue !== null) {
            // Patrones específicos para BodegaOrigen con diferentes comillas
            $patterns = [
                '(obo.idbodega = "[@BodegaOrigen;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des]")',
                "(obo.idbodega = '[@BodegaOrigen;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des]')",
                '(obo.idbodega = [@BodegaOrigen;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des])'
            ];
            
            foreach ($patterns as $pattern) {
                $replacement = "(obo.idbodega = '$bodegaOrigenValue')";
                $newSql = str_replace($pattern, $replacement, $sql);
                if ($newSql !== $sql) {
                    error_log("Reemplazado patrón específico de BodegaOrigen con valor $bodegaOrigenValue");
                    $sql = $newSql;
                    break;
                }
            }
        }
    }
    
    // MEJORA: Lo mismo para otros patrones específicos problemáticos
    if (strpos($sql, '[@BodegaDestino') !== false) {
        error_log("ENCONTRADO patrón específico de BodegaDestino - buscando valor en filtros");
        
        // Buscar valores posibles para BodegaDestino
        $bodegaDestinoKeys = [
            'filter_bodegadestino',
            'filter_BodegaDestino',
            'filter_Bodega_Destino',
            'filter_bodega_destino'
        ];
        
        $bodegaDestinoValue = null;
        foreach ($bodegaDestinoKeys as $key) {
            if (isset($filterValues[$key]) && !empty($filterValues[$key])) {
                $bodegaDestinoValue = $filterValues[$key];
                error_log("Encontrado valor específico para BodegaDestino: $bodegaDestinoValue con clave $key");
                break;
            }
        }
        
        // Si encontramos un valor, reemplazar el patrón directamente
        if ($bodegaDestinoValue !== null) {
            // Patrones para BodegaDestino con diferentes comillas
            $patterns = [
                '(obd.idbodega = "[@BodegaDestino;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des]")',
                "(obd.idbodega = '[@BodegaDestino;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des]')",
                '(obd.idbodega = [@BodegaDestino;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des])'
            ];
            
            foreach ($patterns as $pattern) {
                $replacement = "(obd.idbodega = '$bodegaDestinoValue')";
                $newSql = str_replace($pattern, $replacement, $sql);
                if ($newSql !== $sql) {
                    error_log("Reemplazado patrón específico de BodegaDestino con valor $bodegaDestinoValue");
                    $sql = $newSql;
                    break;
                }
            }
        }
    }
    
    // Buscar todos los patrones de tipo [@nombre;val;des;...] que aún estén en la consulta
    // Esta parte es el reemplazo genérico original, mejorado
    $patternRegex = '/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/';
    if (preg_match_all($patternRegex, $sql, $matches, PREG_SET_ORDER)) {
        error_log("Encontrados " . count($matches) . " patrones genéricos no sustituidos");
        
        foreach ($matches as $match) {
            $fullPattern = $match[0]; // [@nombre;val;des;...]
            $filterName = $match[1];  // nombre
            $valField = $match[2];    // val
            $desField = $match[3];    // des
            $sqlQuery = $match[4];    // SQL de consulta
            
            // MEJORA: Imprimir el patrón completo para depuración
            error_log("Procesando patrón: $fullPattern");
            
            // MEJORA: Más variantes de nombres de filtros para mayor robustez
            $possibleFilterKeys = [
                'filter_' . strtolower($filterName),
                'filter_' . sanitizeId($filterName),
                'filter_' . str_replace(' ', '_', strtolower($filterName)),
                strtolower($filterName),
                'filter_' . strtolower(str_replace(' ', '', $filterName))
            ];
            
            // MEJORA: Imprimir las claves que estamos buscando
            error_log("Buscando valores para las claves: " . implode(", ", $possibleFilterKeys));
            
            $filterValue = null;
            $filterFound = false;
            
            // Buscar el filtro en los valores proporcionados por el usuario
            foreach ($possibleFilterKeys as $filterKey) {
                if (isset($filterValues[$filterKey]) && $filterValues[$filterKey] !== '') {
                    $filterValue = $filterValues[$filterKey];
                    $filterFound = true;
                    error_log("Encontrado valor $filterValue para el filtro $filterName con clave $filterKey");
                    break;
                }
            }
            
            // Si no encontramos un valor directo, buscar en los filtros originales con más flexibilidad
            if (!$filterFound) {
                foreach ($filters as $filter) {
                    // MEJORA: Verificar también si el id del filtro o label coincide aproximadamente
                    $filterLabel = isset($filter['label']) ? $filter['label'] : '';
                    $filterId = isset($filter['id']) ? $filter['id'] : '';
                    
                    if (isset($filter['type']) && $filter['type'] === 'combo' && 
                        (strcasecmp($filterLabel, $filterName) === 0 || 
                         stripos($filterId, strtolower($filterName)) !== false ||
                         stripos($filterId, strtolower(str_replace(' ', '_', $filterName))) !== false ||
                         stripos($filterId, strtolower(str_replace(' ', '', $filterName))) !== false ||
                         strtolower($filterId) === 'filter_' . strtolower(str_replace(' ', '_', $filterName)) ||
                         strtolower($filterId) === 'filter_' . sanitizeId($filterName))) {
                        
                        $filterKey = $filter['id'];
                        if (isset($filterValues[$filterKey]) && $filterValues[$filterKey] !== '') {
                            $filterValue = $filterValues[$filterKey];
                            $filterFound = true;
                            error_log("Encontrado valor $filterValue para filtro $filterName por búsqueda flexible en filtros (id: $filterId)");
                            break;
                        }
                    }
                }
            }
            
            // MEJORA: Si todavía no se encuentra, hacer una verificación adicional para filtros específicos
            if (!$filterFound) {
                // Verificación específica para BodegaOrigen
                if (strcasecmp($filterName, 'BodegaOrigen') === 0 || 
                    strcasecmp($filterName, 'Bodega Origen') === 0 || 
                    strcasecmp($filterName, 'Bodega_Origen') === 0) {
                    
                    $bodegaOrigenKeys = [
                        'filter_bodegaorigen', 'filter_bodega_origen', 'bodegaorigen', 'bodega_origen',
                        'filter_origen', 'origen', 'filter_bodega', 'bodega'
                    ];
                    
                    foreach ($bodegaOrigenKeys as $key) {
                        if (isset($filterValues[$key]) && !empty($filterValues[$key])) {
                            $filterValue = $filterValues[$key];
                            $filterFound = true;
                            error_log("Encontrado valor $filterValue para BodegaOrigen con clave alternativa $key");
                            break;
                        }
                    }
                }
                
                // Verificación específica para BodegaDestino
                if (strcasecmp($filterName, 'BodegaDestino') === 0 || 
                    strcasecmp($filterName, 'Bodega Destino') === 0 || 
                    strcasecmp($filterName, 'Bodega_Destino') === 0) {
                    
                    $bodegaDestinoKeys = [
                        'filter_bodegadestino', 'filter_bodega_destino', 'bodegadestino', 'bodega_destino',
                        'filter_destino', 'destino'
                    ];
                    
                    foreach ($bodegaDestinoKeys as $key) {
                        if (isset($filterValues[$key]) && !empty($filterValues[$key])) {
                            $filterValue = $filterValues[$key];
                            $filterFound = true;
                            error_log("Encontrado valor $filterValue para BodegaDestino con clave alternativa $key");
                            break;
                        }
                    }
                }
            }
            
            // Si hemos encontrado un valor, reemplazar el patrón en la consulta con más variantes
            if ($filterFound && $filterValue !== null) {
                // MEJORA: Intentar más variantes de reemplazo para el patrón
                $patterns = [
                    '"' . $fullPattern . '"', // Con comillas dobles
                    "'" . $fullPattern . "'", // Con comillas simples
                    $fullPattern,            // Sin comillas
                    '(' . $fullPattern . ')', // Con paréntesis
                    '(obo.idbodega = "' . $fullPattern . '")', // Patrón específico BodegaOrigen
                    "(obo.idbodega = '" . $fullPattern . "')",
                    '(obd.idbodega = "' . $fullPattern . '")', // Patrón específico BodegaDestino
                    "(obd.idbodega = '" . $fullPattern . "')"
                ];
                
                // Buscar patrones específicos según el nombre del filtro
                if (strcasecmp($filterName, 'BodegaOrigen') === 0) {
                    $patternFound = false;
                    $bodegaPatterns = [
                        '(obo.idbodega = "' . $fullPattern . '")', 
                        "(obo.idbodega = '" . $fullPattern . "')",
                        '(obo.idbodega = ' . $fullPattern . ')',
                        'obo.idbodega = "' . $fullPattern . '"',
                        "obo.idbodega = '" . $fullPattern . "'",
                        'obo.idbodega = ' . $fullPattern
                    ];
                    
                    foreach ($bodegaPatterns as $pattern) {
                        $replacement = "(obo.idbodega = '$filterValue')";
                        if (strpos($sql, $pattern) !== false) {
                            $newSql = str_replace($pattern, $replacement, $sql);
                            if ($newSql !== $sql) {
                                error_log("Reemplazado patrón específico BodegaOrigen: $pattern con $replacement");
                                $sql = $newSql;
                                $patternFound = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$patternFound) {
                        // Si no se encontró un patrón específico, intentar reemplazos genéricos
                        foreach ($patterns as $pattern) {
                            $newSql = str_replace($pattern, $filterValue, $sql);
                            if ($newSql !== $sql) {
                                error_log("Reemplazado patrón genérico para BodegaOrigen: $pattern con $filterValue");
                                $sql = $newSql;
                                break;
                            }
                        }
                    }
                } 
                else if (strcasecmp($filterName, 'BodegaDestino') === 0) {
                    $patternFound = false;
                    $bodegaPatterns = [
                        '(obd.idbodega = "' . $fullPattern . '")', 
                        "(obd.idbodega = '" . $fullPattern . "')",
                        '(obd.idbodega = ' . $fullPattern . ')',
                        'obd.idbodega = "' . $fullPattern . '"',
                        "obd.idbodega = '" . $fullPattern . "'",
                        'obd.idbodega = ' . $fullPattern
                    ];
                    
                    foreach ($bodegaPatterns as $pattern) {
                        $replacement = "(obd.idbodega = '$filterValue')";
                        if (strpos($sql, $pattern) !== false) {
                            $newSql = str_replace($pattern, $replacement, $sql);
                            if ($newSql !== $sql) {
                                error_log("Reemplazado patrón específico BodegaDestino: $pattern con $replacement");
                                $sql = $newSql;
                                $patternFound = true;
                                break;
                            }
                        }
                    }
                    
                    if (!$patternFound) {
                        // Si no se encontró un patrón específico, intentar reemplazos genéricos
                        foreach ($patterns as $pattern) {
                            $newSql = str_replace($pattern, $filterValue, $sql);
                            if ($newSql !== $sql) {
                                error_log("Reemplazado patrón genérico para BodegaDestino: $pattern con $filterValue");
                                $sql = $newSql;
                                break;
                            }
                        }
                    }
                }
                else {
                    // Para otros filtros, intentar reemplazos genéricos
                    foreach ($patterns as $pattern) {
                        $newSql = str_replace($pattern, $filterValue, $sql);
                        if ($newSql !== $sql) {
                            error_log("Reemplazado patrón genérico: $pattern con $filterValue");
                            $sql = $newSql;
                            break;
                        }
                    }
                }
            } else {
                error_log("No se encontró valor para el filtro $filterName - el patrón $fullPattern se mantendrá en la consulta");
                
                // MEJORA: Para casos donde no se encontró un valor, verificar si debemos eliminar la cláusula completa
                if (strcasecmp($filterName, 'BodegaOrigen') === 0) {
                    // Intentar eliminar la cláusula de BodegaOrigen si no hay valor
                    $bodegaPatterns = [
                        'and (obo.idbodega = "' . $fullPattern . '")', 
                        "and (obo.idbodega = '" . $fullPattern . "')",
                        'and (obo.idbodega = ' . $fullPattern . ')',
                        'AND (obo.idbodega = "' . $fullPattern . '")',
                        "AND (obo.idbodega = '" . $fullPattern . "')",
                        'AND (obo.idbodega = ' . $fullPattern . ')'
                    ];
                    
                    foreach ($bodegaPatterns as $pattern) {
                        if (strpos($sql, $pattern) !== false) {
                            $newSql = str_replace($pattern, '', $sql);
                            if ($newSql !== $sql) {
                                error_log("Eliminada cláusula de BodegaOrigen sin valor: $pattern");
                                $sql = $newSql;
                                break;
                            }
                        }
                    }
                }
                else if (strcasecmp($filterName, 'BodegaDestino') === 0) {
                    // Intentar eliminar la cláusula de BodegaDestino si no hay valor
                    $bodegaPatterns = [
                        'and (obd.idbodega = "' . $fullPattern . '")', 
                        "and (obd.idbodega = '" . $fullPattern . "')",
                        'and (obd.idbodega = ' . $fullPattern . ')',
                        'AND (obd.idbodega = "' . $fullPattern . '")',
                        "AND (obd.idbodega = '" . $fullPattern . "')",
                        'AND (obd.idbodega = ' . $fullPattern . ')'
                    ];
                    
                    foreach ($bodegaPatterns as $pattern) {
                        if (strpos($sql, $pattern) !== false) {
                            $newSql = str_replace($pattern, '', $sql);
                            if ($newSql !== $sql) {
                                error_log("Eliminada cláusula de BodegaDestino sin valor: $pattern");
                                $sql = $newSql;
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    
    return $sql;
}

function buildSqlQuery($report, $filterValues) {
    global $filters; // Access the global filters array
    global $debug_info; // Para compartir información de depuración
    $sqlQuery = "SELECT " . $report['sql_select'] . " FROM " . $report['sql_from'];
    
    // Process WHERE clause with filter values
    if (!empty($report['sql_where'])) {
        $whereClause = $report['sql_where'];
        
        // Depuración - Inicializamos aquí para tener acceso a toda la información
        $debug_info = array();
        
        // PARCHE ESPECÍFICO PARA ZAFRA EN REPORTE 18
        // Este es un arreglo directo para el problema reportado con el filtro Zafra
        if (isset($_GET['id']) && intval($_GET['id']) == 18) {
            $debug_info[] = "Aplicando parche especial para el reporte 18 (Zafra)";
            
            // Caso específico: Si no hay valor para el filtro Zafra (opción "Todos"), eliminar toda la condición
            if (!isset($_POST['filter_zafra']) || $_POST['filter_zafra'] === '') {
                // Eliminar patrón exacto tal como aparece en el SQL
                $exactPattern = "/and\\s*\\(il\\.idloteproducto\\s*=\\s*'\\[@Zafra;val;des;SELECT idloteproducto val, descripcionlote des from inventarios_lotes order by des\\]'\\)/i";
                $whereClause = preg_replace($exactPattern, '', $whereClause);
                
                // Patrón alternativo
                $zafraPattern = "/and\\s*\\(il\\.idloteproducto\\s*=\\s*'\\[@Zafra[^\\]]*\\]'\\)/i";
                $whereClause = preg_replace($zafraPattern, '', $whereClause);
                $debug_info[] = "Eliminada condición Zafra (opción Todos)";
            }
            // Caso específico: Si hay un valor seleccionado para Zafra, reemplazar el patrón por el valor
            else if (isset($_POST['filter_zafra']) && !empty($_POST['filter_zafra'])) {
                $comboValue = $_POST['filter_zafra'];
                
                // Reemplazar el patrón exacto 
                $exactPattern = "\\(il\\.idloteproducto\\s*=\\s*'\\[@Zafra;val;des;SELECT idloteproducto val, descripcionlote des from inventarios_lotes order by des\\]'\\)";
                $whereClause = preg_replace("/$exactPattern/i", "(il.idloteproducto = $comboValue)", $whereClause);
                
                // Patrón alternativo
                $zafraPattern = "\\(il\\.idloteproducto\\s*=\\s*'\\[@Zafra[^\\]]*\\]'\\)";
                $whereClause = preg_replace("/$zafraPattern/i", "(il.idloteproducto = $comboValue)", $whereClause);
                $debug_info[] = "Reemplazado patrón Zafra con valor: $comboValue";
            }
        }
        
        // First, preprocess any malformed combo patterns before processing
        $whereClause = fixMalformedComboPatterns($whereClause);
        
        // Then look for direct combo patterns [@Field;val;des;sql] and replace them with selected values
        // This is a direct approach that should work more reliably
        $comboPatternRegex = '/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/'; // [@Campo;val;des;SQL]
        preg_match_all($comboPatternRegex, $whereClause, $comboMatches, PREG_SET_ORDER);
        
        $debug_info[] = "Total patrones combo encontrados: " . count($comboMatches);
        
        foreach ($comboMatches as $match) {
            $fullPattern = $match[0];      // The entire pattern [@Field;val;des;sql]
            $label = $match[1];            // Field name/label
            $val_field = $match[2];        // Field for value (val) in SQL
            $des_field = $match[3];        // Field for text (des) in SQL
            $filterKey = 'filter_' . sanitizeId($label); // Expected POST field name
            
            // Agregar info para depuración
            $debug_info[] = "Procesando patrón: " . $fullPattern;
            $debug_info[] = "Label: " . $label . ", Campo valor: " . $val_field . ", Campo descriptor: " . $des_field;
            $debug_info[] = "Filtro POST esperado: " . $filterKey;
            
            if (isset($filterValues[$filterKey])) {
                // Manejar arrays para multiselección en debug
                if (is_array($filterValues[$filterKey])) {
                    $debug_info[] = "Valores seleccionados (multiselección): " . implode(', ', array_map('strval', $filterValues[$filterKey]));
                } else {
                    $debug_info[] = "Valor seleccionado: " . strval($filterValues[$filterKey]);
                }
            } else {
                $debug_info[] = "No hay valor seleccionado para este filtro";
            }
            
            // Detectar si este es un filter nulo (Todos) o tiene un valor seleccionado
            // Para arrays (multiselección), verificar si está vacío diferente
            $filterEmpty = false;
            if (isset($filterValues[$filterKey])) {
                if (is_array($filterValues[$filterKey])) {
                    $filterEmpty = empty($filterValues[$filterKey]);
                } else {
                    $filterEmpty = trim(strval($filterValues[$filterKey])) === '';
                }
            } else {
                $filterEmpty = true;
            }
            
            // Si el usuario seleccionó un valor específico (no Todos)
            if (isset($filterValues[$filterKey]) && !empty($filterValues[$filterKey])) {
                // Usuario seleccionó un valor específico (por ejemplo, un ID numérico)
                $comboValue = $filterValues[$filterKey];
                
                // SOLUCIÓN PARA TIPOS COMBO: Asegurar que solo usemos el valor (val) y no el patrón completo
                
                // Aquí aplicamos directamente el valor seleccionado de la opción al SQL
                // El valor ya viene correcto desde el select HTML, que tiene value="opcion['value']"
                
                // For debugging - add a trace to a variable
                if (is_array($comboValue)) {
                    $debug_info[] = "Reemplazando combo: " . $fullPattern . " con valores múltiples: " . implode(', ', array_map('strval', $comboValue));
                } else {
                    $debug_info[] = "Reemplazando combo: " . $fullPattern . " con valor: " . strval($comboValue);
                }
                
                // Manejar multiselección desde el inicio
                if (is_array($comboValue)) {
                    // Procesar multiselección - crear condición IN()
                    $debug_info[] = "Detectada multiselección para $label";
                    
                    // Crear la cadena de valores para IN sin agregar IN() si ya existe
                    $valuesString = "'" . implode("','", array_map('addslashes', array_map('strval', $comboValue))) . "'";
                    
                    // Verificar si el patrón ya contiene IN en el SQL
                    if (preg_match('/\s+IN\s+["\']?' . preg_quote($fullPattern, '/') . '["\']?/i', $whereClause)) {
                        // Ya hay IN en la consulta, solo reemplazar el patrón con los valores
                        $debug_info[] = "IN ya presente en consulta, solo reemplazando valores";
                        $debug_info[] = "ANTES del reemplazo: " . $whereClause;
                        $debug_info[] = "Patrón a reemplazar: $fullPattern";
                        $debug_info[] = "Valores para reemplazo: ($valuesString)";
                        $whereClause = str_replace($fullPattern, "($valuesString)", $whereClause);
                        
                        // CORRECCIÓN ESPECIAL: Si el patrón está entre comillas, corregir IN "(...)" -> IN (...)
                        $whereClause = preg_replace('/IN\s+"\s*\(([^"]+)\)\s*"/i', 'IN ($1)', $whereClause);
                        
                        $debug_info[] = "DESPUÉS del reemplazo: " . $whereClause;
                        $debug_info[] = "Multiselección: reemplazado $fullPattern con ($valuesString)";
                        
                        // Limpiar inmediatamente patrones malformados después del reemplazo
                        $whereClause = preg_replace('/IN\s*"IN\s*\(/i', 'IN (', $whereClause);
                        $whereClause = preg_replace('/IN\s*"\s*\(/i', 'IN (', $whereClause);  
                        $whereClause = preg_replace('/"\s*"\s*\)\s*\)/i', '")', $whereClause);
                        $whereClause = preg_replace('/""\s*\)\s*\)/i', '")', $whereClause);
                        $whereClause = preg_replace('/\(\s*IN\s*\(/i', '(', $whereClause);
                        
                        // Casos específicos para el patrón problemático IN "("value1","value2"" ))
                        $whereClause = preg_replace('/IN\s*"?\s*\(\s*"/i', 'IN (', $whereClause);
                        $whereClause = preg_replace('/"\s*"\s*\)\s*\)/i', '")', $whereClause);
                        
                        // LIMPIEZA UNIVERSAL COMPLETA: Funciona con cualquier valor y cualquier cantidad
                        // Usar la misma función universal que en sqlcleaner.php
                        $whereClause = cleanMultiselectionInConditions($whereClause);
                        
                        $debug_info[] = "Aplicada limpieza de comillas dobles malformadas";
                    } else {
                        // No hay IN en la consulta, usar la lógica original
                        $inCondition = "IN ($valuesString)";
                        
                        // Buscar el campo en la condición SQL para construir la consulta completa
                        if (preg_match('/([a-zA-Z0-9_]+\.?[a-zA-Z0-9_]+)\s*=\s*["\']?' . preg_quote($fullPattern, '/') . '["\']?/', $whereClause, $fieldMatch)) {
                            $fieldName = $fieldMatch[1];
                            $fullInCondition = "$fieldName $inCondition";
                            
                            // Intentar varios patrones de reemplazo para multiselección
                            $replacementPatterns = [
                                "/\(" . preg_quote($fieldName, '/') . "\s*=\s*\"" . preg_quote($fullPattern, '/') . "\"\)/i" => "($fullInCondition)",
                                "/\(" . preg_quote($fieldName, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'\)/i" => "($fullInCondition)",
                                "/" . preg_quote($fieldName, '/') . "\s*=\s*\"" . preg_quote($fullPattern, '/') . "\"/i" => $fullInCondition,
                                "/" . preg_quote($fieldName, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'/i" => $fullInCondition,
                                "/" . preg_quote($fieldName, '/') . "\s*=\s*" . preg_quote($fullPattern, '/') . "/i" => $fullInCondition
                            ];
                            
                            $replaced = false;
                            foreach ($replacementPatterns as $pattern => $replacement) {
                                $newWhereClause = preg_replace($pattern, $replacement, $whereClause);
                                if ($newWhereClause !== $whereClause) {
                                    $whereClause = $newWhereClause;
                                    $debug_info[] = "Multiselección aplicada: $fieldName con patrón $pattern";
                                    $replaced = true;
                                    break;
                                }
                            }
                            
                            if (!$replaced) {
                                // Fallback: reemplazar directamente el patrón
                                $whereClause = str_replace($fullPattern, $inCondition, $whereClause);
                                $debug_info[] = "Multiselección fallback: reemplazado patrón directamente";
                            }
                        } else {
                            // Fallback directo
                            $whereClause = str_replace($fullPattern, $inCondition, $whereClause);
                            $debug_info[] = "Multiselección fallback directo: sin detectar campo";
                        }
                    }
                    
                    // Saltarse el procesamiento de selección única
                    continue;
                }
                
                // Almacenar el patrón completo para búsquedas precisas (incluye los corchetes [@...])
                $patternWithBrackets = preg_quote($fullPattern, '/');
                
                // Buscar específicamente condiciones con il.idloteproducto y variantes de Zafra
                if ($label === 'Zafra') {
                    $debug_info[] = "Procesando caso especial para Zafra";
                    
                    // Para selección única (no arrays)
                    $singleValue = is_array($comboValue) ? $comboValue[0] : $comboValue;
                    
                    // Buscar patrones comunes para Zafra
                    // Estas expresiones buscan la condición completa, incluidos paréntesis, AND y OR
                    
                    // Caso 1: (il.idloteproducto = [@Zafra;...])
                    if (preg_match('/\(\s*il\.idloteproducto\s*=\s*' . $patternWithBrackets . '\s*\)/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $replacement = '(il.idloteproducto = ' . $singleValue . ')';
                        $whereClause = str_replace($fullCondition, $replacement, $whereClause);
                        $debug_info[] = "Reemplazo en caso 1: " . $fullCondition . " -> " . $replacement;
                    }
                    // Caso 2: and (il.idloteproducto = [@Zafra;...]) 
                    else if (preg_match('/and\s*\(\s*il\.idloteproducto\s*=\s*' . $patternWithBrackets . '\s*\)/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $replacement = 'and (il.idloteproducto = ' . $comboValue . ')';
                        $whereClause = str_replace($fullCondition, $replacement, $whereClause);
                        $debug_info[] = "Reemplazo en caso 2: " . $fullCondition . " -> " . $replacement;
                    }
                    // Caso 3: and il.idloteproducto = [@Zafra;...]
                    else if (preg_match('/and\s+il\.idloteproducto\s*=\s*' . $patternWithBrackets . '/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $replacement = 'and il.idloteproducto = ' . $comboValue;
                        $whereClause = str_replace($fullCondition, $replacement, $whereClause);
                        $debug_info[] = "Reemplazo en caso 3: " . $fullCondition . " -> " . $replacement;
                    }
                    // Caso 4: il.idloteproducto = [@Zafra;...]
                    else if (preg_match('/il\.idloteproducto\s*=\s*' . $patternWithBrackets . '/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $replacement = 'il.idloteproducto = ' . $comboValue;
                        $whereClause = str_replace($fullCondition, $replacement, $whereClause);
                        $debug_info[] = "Reemplazo en caso 4: " . $fullCondition . " -> " . $replacement;
                    }
                    // Si no se encontró ningún patrón específico, hacer el reemplazo básico
                    else {
                        $whereClause = str_replace($fullPattern, $comboValue, $whereClause);
                        $debug_info[] = "Reemplazo general para Zafra: " . $fullPattern . " -> " . $comboValue;
                    }
                } 
                // Para otros filtros combo (no Zafra), aplicar reemplazo directo con comillas consistentes
                else {
                    // SOLUCIÓN GENERAL: Asegurar comillas consistentes
                    if (strpos($whereClause, "'" . $fullPattern . "'") !== false) {
                        $whereClause = str_replace("'" . $fullPattern . "'", "'" . $comboValue . "'", $whereClause);
                        $debug_info[] = "Reemplazo con comillas simples: '" . $fullPattern . "' -> '" . $comboValue . "'";
                    } else if (strpos($whereClause, '"' . $fullPattern . '"') !== false) {
                        $whereClause = str_replace('"' . $fullPattern . '"', '"' . $comboValue . '"', $whereClause);
                        $debug_info[] = "Reemplazo con comillas dobles: \"" . $fullPattern . "\" -> \"" . $comboValue . "\"";
                    } else {
                        $whereClause = str_replace($fullPattern, $comboValue, $whereClause);
                        $debug_info[] = "Reemplazo directo: " . $fullPattern . " -> " . $comboValue;
                    }
                }
            }
            // Si es un filtro vacío (opción "Todos"), necesitamos eliminar la condición del SQL
            else if ($filterEmpty) {
                $debug_info[] = "Procesando filtro vacío (Todos) para: " . $fullPattern;
                
                // Manejo especial para Zafra - eliminar la condición completa
                if ($label === 'Zafra') {
                    $debug_info[] = "Procesando caso especial Zafra con 'Todos'";
                    
                    // Caso especial 1: Buscar "(il.idloteproducto = [@Zafra...])""
                    if (preg_match('/\(\s*il\.idloteproducto\s*=\s*' . preg_quote($fullPattern, '/') . '\s*\)/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $debug_info[] = "Encontrada condición con paréntesis para eliminar: " . $fullCondition;
                        
                        // Buscar si hay AND/OR antes o después de esta condición
                        $pos = strpos($whereClause, $fullCondition);
                        if ($pos !== false) {
                            $beforePos = max(0, $pos - 5);
                            $afterPos = $pos + strlen($fullCondition);
                            
                            // Verificar AND/OR antes
                            $beforePart = substr($whereClause, $beforePos, 5);
                            if (strtoupper(trim($beforePart)) === 'AND') {
                                $fullCondition = 'AND ' . $fullCondition;
                                $debug_info[] = "Incluyendo AND antes: " . $fullCondition;
                            } else if (strtoupper(trim(substr($beforePart, 0, 2))) === 'OR') {
                                $fullCondition = 'OR ' . $fullCondition;
                                $debug_info[] = "Incluyendo OR antes: " . $fullCondition;
                            }
                            
                            // Verificar AND/OR después
                            $afterPart = substr($whereClause, $afterPos, 5);
                            if (strtoupper(trim($afterPart)) === 'AND') {
                                $fullCondition = $fullCondition . ' AND';
                                $debug_info[] = "Incluyendo AND después: " . $fullCondition;
                            } else if (strtoupper(trim(substr($afterPart, 0, 2))) === 'OR') {
                                $fullCondition = $fullCondition . ' OR';
                                $debug_info[] = "Incluyendo OR después: " . $fullCondition;
                            }
                            
                            // Eliminar la condición completa
                            $whereClause = str_replace($fullCondition, '', $whereClause);
                            $debug_info[] = "Condición eliminada, SQL resultante: " . $whereClause;
                        }
                    }
                    // Caso 2: and il.idloteproducto = [@Zafra...] sin paréntesis
                    else if (preg_match('/and\s+il\.idloteproducto\s*=\s*' . preg_quote($fullPattern, '/') . '/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $debug_info[] = "Encontrada condición con AND para eliminar: " . $fullCondition;
                        
                        // Eliminar hasta el siguiente AND/OR o fin de la cláusula
                        $pos = strpos($whereClause, $fullCondition) + strlen($fullCondition);
                        $restOfClause = substr($whereClause, $pos);
                        
                        // Buscar el siguiente AND/OR
                        if (preg_match('/\s+(AND|OR)\s+/i', $restOfClause, $opMatches, PREG_OFFSET_CAPTURE)) {
                            $opPos = $opMatches[0][1];
                            $fullCondition .= substr($restOfClause, 0, $opPos);
                        }
                        
                        $whereClause = str_replace($fullCondition, '', $whereClause);
                        $debug_info[] = "Condición eliminada, SQL resultante: " . $whereClause;
                    }
                    // Caso general para Zafra: cualquier condición que incluya el patrón
                    else {
                        $debug_info[] = "Usando eliminación general para Zafra";
                    }
                }
                
                // ESTRATEGIA GENERAL: 1. Buscar la condición completa que contiene el patrón
                //                     2. Determinar si es parte de AND/OR
                //                     3. Eliminar la condición completa preservando la estructura SQL
                
                // Buscar condiciones de igualdad como "campo = [@Filtro]"
                $equalityPatterns = array(
                    'equals' => '/([^\s]+)\s*=\s*' . preg_quote($fullPattern, '/') . '/i',
                    'like' => '/([^\s]+)\s*LIKE\s*[\'"]?[%]?' . preg_quote($fullPattern, '/') . '[%]?[\'"]?/i'
                );
                
                foreach ($equalityPatterns as $type => $pattern) {
                    if (preg_match($pattern, $whereClause, $matches)) {
                        $fieldName = $matches[1]; // El campo al que se aplica el filtro
                        $conditionStr = $matches[0]; // La condición completa
                        
                        // Ahora determinar si esta condición está dentro de AND/OR
                        // y eliminarla apropiadamente
                        
                        // Caso 1: "(campo = [@Filtro])" - Condición dentro de paréntesis
                        $parenPattern = '/\(\s*' . preg_quote($conditionStr, '/') . '\s*\)/i';
                        if (preg_match($parenPattern, $whereClause, $parenMatches)) {
                            // Necesitamos verificar si los paréntesis están dentro de AND/OR
                            $fullCondition = $parenMatches[0];
                            $wherePos = stripos($whereClause, 'WHERE');
                            $condPos = strpos($whereClause, $fullCondition);
                            
                            // Si está justo después de WHERE, eliminar la condición entera
                            if ($wherePos !== false && ($condPos - $wherePos) <= 7) {
                                // Es la primera condición después de WHERE
                                // Buscar si hay AND/OR después
                                $afterPos = $condPos + strlen($fullCondition);
                                $afterStr = substr($whereClause, $afterPos, 6);
                                
                                if (stripos($afterStr, ' AND ') === 0) {
                                    // Hay un AND después, eliminarlo también
                                    $whereClause = str_replace($fullCondition . ' AND', '', $whereClause);
                                } else if (stripos($afterStr, ' OR ') === 0) {
                                    // Hay un OR después, eliminarlo también
                                    $whereClause = str_replace($fullCondition . ' OR', '', $whereClause);
                                } else {
                                    // No hay operador después, solo eliminar la condición
                                    $whereClause = str_replace($fullCondition, '', $whereClause);
                                }
                            } else {
                                // Está en medio de la cláusula WHERE
                                // Buscar si hay AND/OR antes
                                $beforeStr = substr($whereClause, 0, $condPos);
                                
                                if (preg_match('/\s+AND\s+$/i', $beforeStr)) {
                                    // Hay un AND antes
                                    $whereClause = preg_replace('/\s+AND\s+' . preg_quote($fullCondition, '/') . '/', '', $whereClause);
                                } else if (preg_match('/\s+OR\s+$/i', $beforeStr)) {
                                    // Hay un OR antes
                                    $whereClause = preg_replace('/\s+OR\s+' . preg_quote($fullCondition, '/') . '/', '', $whereClause);
                                } else {
                                    // Caso extraño, solo reemplazar
                                    $whereClause = str_replace($fullCondition, '', $whereClause);
                                }
                            }
                        }
                        // Caso 2: "campo = [@Filtro] AND/OR" - Condición seguida de operador
                        else if (preg_match('/' . preg_quote($conditionStr, '/') . '\s+(AND|OR)/i', $whereClause, $opAfterMatches)) {
                            $operator = $opAfterMatches[1];
                            $fullCondition = $conditionStr . ' ' . $operator;
                            
                            // Si está después de WHERE, eliminar la condición y el operador
                            $wherePos = stripos($whereClause, 'WHERE');
                            $condPos = strpos($whereClause, $conditionStr);
                            
                            if ($wherePos !== false && ($condPos - $wherePos) <= 7) {
                                // Es la primera condición después de WHERE
                                $whereClause = str_replace('WHERE ' . $fullCondition, 'WHERE ', $whereClause);
                            } else {
                                // Está en medio de la cláusula, buscar qué hay antes
                                $beforeStr = substr($whereClause, 0, $condPos);
                                
                                if (preg_match('/\s+AND\s+$/i', $beforeStr)) {
                                    // AND antes: "... AND campo = [@Filtro] AND ..."
                                    $whereClause = preg_replace('/\s+AND\s+' . preg_quote($conditionStr, '/') . '\s+(AND|OR)/i', ' $1', $whereClause);
                                } else if (preg_match('/\s+OR\s+$/i', $beforeStr)) {
                                    // OR antes: "... OR campo = [@Filtro] AND ..."
                                    $whereClause = preg_replace('/\s+OR\s+' . preg_quote($conditionStr, '/') . '\s+(AND|OR)/i', ' $1', $whereClause);
                                } else {
                                    // Caso extraño
                                    $whereClause = str_replace($fullCondition, '', $whereClause);
                                }
                            }
                        }
                        // Caso 3: "AND/OR campo = [@Filtro]" - Operador antes de condición
                        else if (preg_match('/(AND|OR)\s+' . preg_quote($conditionStr, '/') . '/i', $whereClause, $opBeforeMatches)) {
                            $operator = $opBeforeMatches[1];
                            $fullCondition = $operator . ' ' . $conditionStr;
                            $whereClause = str_replace($fullCondition, '', $whereClause);
                        }
                        // Caso 4: campo = [@Filtro] sin AND/OR - posiblemente única condición
                        else {
                            $whereClause = str_replace($conditionStr, '1=1', $whereClause);
                        }
                    }
                }
                
                // Limpieza final: Eliminar WHERE vacío
                $whereClause = preg_replace('/WHERE\s+$/i', '', $whereClause);
                
                // Reemplazar "WHERE 1=1 AND" por "WHERE"
                $whereClause = preg_replace('/WHERE\s+1\s*=\s*1\s+AND/i', 'WHERE', $whereClause);
                
                // Reemplazar "WHERE 1=1" al final por cadena vacía
                $whereClause = preg_replace('/WHERE\s+1\s*=\s*1\s*$/i', '', $whereClause);
            }
            // Note: We'll still continue with regular processing for any patterns that weren't handled
        }
        
        // Second, handle session variables with the pattern [!VarName]
        foreach ($filters as $filter) {
            if ($filter['type'] === 'session') {
                // Para variables de sesión, usar el valor de la sesión
                $filterValue = $filter['value']; // Valor de la sesión o por defecto
                $pattern = $filter['sql_pattern']; // Should be [!VarName]
                
                // Replace session variables - always present
                $whereClause = str_replace($pattern, $filterValue, $whereClause);
            }
        }
        
        // Identificar todos los patrones de sustitución en el WHERE
        $patterns = [];
        
        // Regular expressions para identificar los diferentes tipos de patrones
        $regularPatternRegex = '/\[([^\]#@!]+)\]/'; // [Campo]
        $datePatternRegex = '/\[#([^\]]+)\]/';  // [#Campo]
        $comboPatternRegex = '/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/'; // [@Campo;val;des;SQL]
        
        // Encontrar todos los patrones normales [Campo]
        preg_match_all($regularPatternRegex, $whereClause, $regularMatches, PREG_SET_ORDER);
        foreach ($regularMatches as $match) {
            $patterns[] = [
                'full_pattern' => $match[0], // [Campo]
                'field_name' => $match[1],   // Campo
                'condition_pattern' => null  // Se llenará luego
            ];
        }
        
        // Encontrar todos los patrones fecha [#Campo]
        preg_match_all($datePatternRegex, $whereClause, $dateMatches, PREG_SET_ORDER);
        foreach ($dateMatches as $match) {
            $patterns[] = [
                'full_pattern' => $match[0], // [#Campo]
                'field_name' => $match[1],   // Campo
                'condition_pattern' => null  // Se llenará luego
            ];
        }
        
        // Encontrar todos los patrones combo [@Campo;val;des;SQL]
        preg_match_all($comboPatternRegex, $whereClause, $comboMatches, PREG_SET_ORDER);
        foreach ($comboMatches as $match) {
            $patterns[] = [
                'full_pattern' => $match[0], // [@Campo;val;des;SQL]
                'field_name' => $match[1],   // Campo
                'condition_pattern' => null  // Se llenará luego
            ];
        }
        
        // Para cada patrón encontrado, identificar la condición completa (ej: "field LIKE '%[Campo]%'")
        foreach ($patterns as &$pattern) {
            // Patrones comunes para condiciones WHERE
            $conditionRegexes = [
                // campo LIKE '%[pattern]%'
                '/[^\s]+\s+LIKE\s+[\'"]%' . preg_quote($pattern['full_pattern'], '/') . '%[\'"]/',
                // campo LIKE '[pattern]%'
                '/[^\s]+\s+LIKE\s+[\'"]\s*' . preg_quote($pattern['full_pattern'], '/') . '%[\'"]/',
                // campo LIKE '%[pattern]'
                '/[^\s]+\s+LIKE\s+[\'"]%\s*' . preg_quote($pattern['full_pattern'], '/') . '[\'"]\s*/',
                // campo = '[pattern]'
                '/[^\s]+\s*=\s*[\'"]\s*' . preg_quote($pattern['full_pattern'], '/') . '[\'"]\s*/',
                // campo = [pattern] (para valores numéricos)
                '/[^\s]+\s*=\s*' . preg_quote($pattern['full_pattern'], '/') . '\s*/',
                // cualquier otra condición con el patrón
                '/[^\s\(\)]+[^=\<\>\s]+\s*' . preg_quote($pattern['full_pattern'], '/') . '/'
            ];
            
            foreach ($conditionRegexes as $regex) {
                if (preg_match($regex, $whereClause, $matches)) {
                    $pattern['condition_pattern'] = $matches[0];
                    break;
                }
            }
        }
        
        // Recopilar los nombres de los filtros vacíos para combo boxes
        $emptyComboFilters = array();
        
        // Process regular user inputs
        foreach ($filters as $filter) {
            if ($filter['type'] !== 'session') {
                $filterKey = $filter['id']; // e.g., filter_ingenio
                $filterPattern = $filter['sql_pattern']; // e.g., [Ingenio] or [#Fecha]
                
                // Para filtros tipo combo, recopilar los nombres cuando están vacíos
                if ($filter['type'] === 'combo' && (!isset($filterValues[$filterKey]) || $filterValues[$filterKey] === '')) {
                    // Extraer el nombre del filtro sin el prefijo filter_
                    $filterName = str_replace('filter_', '', $filterKey);
                    $emptyComboFilters[] = $filterName;
                }
                
                // Si el usuario no proporcionó valor para este filtro
                if (!isset($filterValues[$filterKey]) || $filterValues[$filterKey] === '') {
                    // Buscar si este patrón está en nuestra lista de patrones
                    foreach ($patterns as $pattern) {
                        if ($pattern['full_pattern'] === $filterPattern) {
                            // Si tenemos la condición completa, analizamos el tipo
                            if ($pattern['condition_pattern']) {
                                $conditionStr = $pattern['condition_pattern'];
                                $pos = strpos($whereClause, $conditionStr);
                                
                                if ($pos !== false) {
                                    // Verificar si es un operador LIKE
                                    if (stripos($conditionStr, ' LIKE ') !== false) {
                                        // Verificamos si contiene comillas en la estructura LIKE
                                        if (preg_match('/LIKE\s+[\'"](.*?)[\'"]/i', $conditionStr, $likeMatches)) {
                                            // La expresión LIKE completa con comillas
                                            $originalPattern = $likeMatches[0]; 
                                            $likeValue = $likeMatches[1]; // El valor entre comillas
                                            
                                            // Verificar si es un patrón con corchetes (puede tener % dentro)
                                            if (preg_match('/\[%(.*?)%\]/', $likeValue) || 
                                                preg_match('/\[%(.*?)\]/', $likeValue) ||
                                                preg_match('/\[(.*?)%\]/', $likeValue)) {
                                                // Es un patrón [%valor%], [%valor] o [valor%]
                                                // En caso de campo vacío, convertir a '%%'
                                                $replacementPattern = " LIKE '%%'";
                                            }
                                            // Verificar diferentes patrones de LIKE
                                            else if (strpos($likeValue, '%') === false) {
                                                // No tiene %, reemplazar por LIKE '%%'
                                                $replacementPattern = " LIKE '%%'";
                                            } 
                                            else if (strpos($likeValue, '%') === 0 && strrpos($likeValue, '%') === strlen($likeValue) - 1) {
                                                // Tiene % al inicio y al final (LIKE '%xyz%')
                                                $replacementPattern = " LIKE '%%'";
                                            }
                                            else if (strpos($likeValue, '%') === 0) {
                                                // Tiene % solo al inicio (LIKE '%xyz')
                                                $replacementPattern = " LIKE '%'";
                                            }
                                            else if (strrpos($likeValue, '%') === strlen($likeValue) - 1) {
                                                // Tiene % solo al final (LIKE 'xyz%')
                                                $replacementPattern = " LIKE '%'";
                                            }
                                            else {
                                                // Para otros casos, usar '%%'
                                                $replacementPattern = " LIKE '%%'";
                                            }
                                            
                                            // Reemplazar la expresión LIKE manteniendo todo lo demás
                                            $replacementCondition = str_replace($originalPattern, $replacementPattern, $conditionStr);
                                            $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                        }
                                        else {
                                            // No se encontraron comillas, puede ser un LIKE %[Producto]% o LIKE %%
                                            
                                            // Verificamos si es una expresión LIKE con %, típico de una sustitución previa
                                            if (stripos($conditionStr, ' LIKE ') !== false) {
                                                // Verificar si es un patrón LIKE con corchetes sin comillas
                                                if (preg_match('/\s+LIKE\s+(\[%.*%\]|\[%.*\]|\[.*%\])/', $conditionStr, $bracketMatches)) {
                                                    $bracketPattern = $bracketMatches[1]; // El patrón entre corchetes, ej: [%Ingenio%]
                                                    
                                                    // Reemplazar por LIKE '%%' con comillas simples
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)\[.*?\]/', '$1\'%%\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // Detectar casos como LIKE %" y similares
                                                else if (preg_match('/\s+LIKE\s+%[\'"]?$/', $conditionStr)) {
                                                    // Casos como "LIKE %" o "LIKE %"" (con comilla al final)
                                                    // Reemplazar por LIKE '%%'
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)%[\'"]?$/', '$1\'%%\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // LIKE %% (entre dos % al inicio y final sin comillas)
                                                else if (preg_match('/\s+LIKE\s+%+%+/', $conditionStr)) {
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)%+%+/', '$1\'%%\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // LIKE % (solo un % sin comillas)
                                                else if (preg_match('/\s+LIKE\s+%$/', $conditionStr)) {
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)%$/', '$1\'%\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // LIKE %algo% (algún texto entre % sin comillas)
                                                else if (preg_match('/\s+LIKE\s+%[^%\s\[]+%/', $conditionStr)) {
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)%([^%\s\[]*)%/', '$1\'%$2%\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // LIKE algo% (algún texto y luego % sin comillas)
                                                else if (preg_match('/\s+LIKE\s+[^%\s\[]+%/', $conditionStr)) {
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)([^%\s\[]*)%/', '$1\'$2%\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // LIKE %algo (% y luego algún texto sin comillas)
                                                else if (preg_match('/\s+LIKE\s+%[^%\s\[]+/', $conditionStr)) {
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)%([^%\s\[]*)/', '$1\'%$2\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                                // Cualquier otro caso de LIKE sin comillas ni %
                                                else {
                                                    $replacementCondition = preg_replace('/(\s+LIKE\s+)([^\s\'"\[]+)/', '$1\'$2\'', $conditionStr);
                                                    $whereClause = str_replace($conditionStr, $replacementCondition, $whereClause);
                                                }
                                            }
                                            else {
                                                // Si no es un LIKE, eliminamos con el método habitual
                                                $beforeChar = $pos > 0 ? $whereClause[$pos - 1] : '';
                                                $afterPos = $pos + strlen($conditionStr);
                                                $afterChar = $afterPos < strlen($whereClause) ? $whereClause[$afterPos] : '';
                                                
                                                $replacePart = $conditionStr;
                                                
                                                // Si hay un AND antes
                                                if ($beforeChar === ' ' && substr($whereClause, max(0, $pos - 5), 5) === ' AND ') {
                                                    $replacePart = ' AND ' . $replacePart;
                                                }
                                                // Si hay un AND después
                                                elseif ($afterChar === ' ' && substr($whereClause, $afterPos, 5) === ' AND ') {
                                                    $replacePart = $replacePart . ' AND ';
                                                }
                                                // Si hay un OR antes
                                                elseif ($beforeChar === ' ' && substr($whereClause, max(0, $pos - 4), 4) === ' OR ') {
                                                    $replacePart = ' OR ' . $replacePart;
                                                }
                                                // Si hay un OR después
                                                elseif ($afterChar === ' ' && substr($whereClause, $afterPos, 4) === ' OR ') {
                                                    $replacePart = $replacePart . ' OR ';
                                                }
                                                
                                                $whereClause = str_replace($replacePart, '', $whereClause);
                                            }
                                        }
                                    }
                                    else {
                                        // Para otros operadores, eliminamos con cuidado
                                        $beforeChar = $pos > 0 ? $whereClause[$pos - 1] : '';
                                        $afterPos = $pos + strlen($conditionStr);
                                        $afterChar = $afterPos < strlen($whereClause) ? $whereClause[$afterPos] : '';
                                        
                                        $replacePart = $conditionStr;
                                        
                                        // Si hay un AND antes
                                        if ($beforeChar === ' ' && substr($whereClause, max(0, $pos - 5), 5) === ' AND ') {
                                            $replacePart = ' AND ' . $replacePart;
                                        }
                                        // Si hay un AND después
                                        elseif ($afterChar === ' ' && substr($whereClause, $afterPos, 5) === ' AND ') {
                                            $replacePart = $replacePart . ' AND ';
                                        }
                                        // Si hay un OR antes
                                        elseif ($beforeChar === ' ' && substr($whereClause, max(0, $pos - 4), 4) === ' OR ') {
                                            $replacePart = ' OR ' . $replacePart;
                                        }
                                        // Si hay un OR después
                                        elseif ($afterChar === ' ' && substr($whereClause, $afterPos, 4) === ' OR ') {
                                            $replacePart = $replacePart . ' OR ';
                                        }
                                        
                                        $whereClause = str_replace($replacePart, '', $whereClause);
                                    }
                                }
                            } else {
                                // Si no pudimos encontrar la condición completa, al menos reemplazamos el patrón
                                $whereClause = str_replace($pattern['full_pattern'], '', $whereClause);
                            }
                            break;
                        }
                    }
                } else {
                    // Si el usuario SÍ proporcionó un valor, lo aplicamos
                    $userValue = $filterValues[$filterKey];
                    
                    // Para fechas, convertimos el formato
                    if ($filter['type'] === 'date') {
                        // Si la fecha está vacía o no es válida, simplemente eliminamos esta condición
                        if (empty($userValue) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $userValue)) {
                            // Buscar el patrón completo en conditions_pattern para eliminarlo
                            foreach ($patterns as $pattern) {
                                if ($pattern['full_pattern'] === $filterPattern && $pattern['condition_pattern']) {
                                    $condStr = $pattern['condition_pattern'];
                                    
                                    // Verificar si la condición está dentro de un AND/OR y eliminarla completa
                                    $pos = strpos($whereClause, $condStr);
                                    if ($pos !== false) {
                                        $beforePos = max(0, $pos - 5);
                                        $afterPos = $pos + strlen($condStr);
                                        
                                        // Buscar AND/OR cercanos
                                        $replacePart = $condStr;
                                        
                                        // Si hay un AND antes
                                        if (substr($whereClause, $beforePos, 5) === ' AND ') {
                                            $replacePart = ' AND ' . $replacePart;
                                        }
                                        // Si hay un AND después
                                        elseif (substr($whereClause, $afterPos, 5) === ' AND ') {
                                            $replacePart = $replacePart . ' AND ';
                                        }
                                        // Si hay un OR antes
                                        elseif (substr($whereClause, $beforePos, 4) === ' OR ') {
                                            $replacePart = ' OR ' . $replacePart;
                                        }
                                        // Si hay un OR después
                                        elseif (substr($whereClause, $afterPos, 4) === ' OR ') {
                                            $replacePart = $replacePart . ' OR ';
                                        }
                                        
                                        // Eliminar la condición completa
                                        $whereClause = str_replace($replacePart, '', $whereClause);
                                    } else {
                                        // Si no podemos ubicar la condición completa, al menos reemplazamos el patrón
                                        $whereClause = str_replace($filterPattern, '', $whereClause);
                                    }
                                    break;
                                }
                            }
                            
                            // Si no encontramos el patrón en patterns, simplemente reemplazamos
                            if (strpos($whereClause, $filterPattern) !== false) {
                                $whereClause = str_replace($filterPattern, '', $whereClause);
                            }
                            
                            // Continuamos con el siguiente filtro
                            continue;
                        }
                            
                        // Convertir formato YYYY-MM-DD a YYYY/MM/DD si es necesario
                        $dateParts = explode('-', $userValue);
                        if (count($dateParts) === 3) {
                            $filterValue = $dateParts[0] . '/' . $dateParts[1] . '/' . $dateParts[2];
                        } else {
                            $filterValue = $userValue;
                        }
                        
                        // Registrar la fecha de usuario para su uso en reporte.php
                        $_SESSION['user_selected_date_' . $filterKey] = $filterValue;
                        error_log("Fecha seleccionada para $filterKey: $filterValue");
                    } else if ($filter['type'] === 'combo') {
                        // Para combos, asegurarnos de usar el valor seleccionado (val) y no el texto
                        // El valor ya viene correcto desde el select HTML que tiene value="opcion['value']"
                        $filterValue = $userValue;
                        
                        // En este punto, el valor de $filterValue ya corresponde al campo 'val' del combo
                        // No necesitamos hacer conversiones adicionales, ya que el select HTML ya retorna
                        // el valor del atributo "value" para la opción seleccionada
                    } else {
                        // Para otros tipos de campos, usamos el valor tal cual
                        $filterValue = $userValue;
                    }
                    
                    // Verificar si el patrón está dentro de una cláusula LIKE con %
                    // Por ejemplo: LIKE "[%Ingenio%]" o LIKE "[%Ingenio]" o LIKE "[Ingenio%]"
                    if (preg_match('/LIKE\s+[\'"](\[%.*%\]|\[%.*\]|\[.*%\])[\'"]/', $whereClause, $likeMatches)) {
                        $likePatternFull = $likeMatches[0]; // El patrón LIKE completo
                        $likeParam = $likeMatches[1]; // El patrón [%valor%]
                        
                        // Asegurar que $filterValue sea string para interpolación
                        $singleFilterValue = is_array($filterValue) ? strval($filterValue[0]) : strval($filterValue);
                        
                        // Determinar qué tipo de patrón LIKE es
                        if (preg_match('/\[%(.*?)%\]/', $likeParam, $innerMatches)) {
                            // Es [%valor%] - contiene % al inicio y al final
                            $replacementPattern = " LIKE '%{$singleFilterValue}%'";
                            $whereClause = str_replace($likePatternFull, $replacementPattern, $whereClause);
                        } else if (preg_match('/\[%(.*?)\]/', $likeParam, $innerMatches)) {
                            // Es [%valor] - contiene % solo al inicio
                            $replacementPattern = " LIKE '%{$singleFilterValue}'";
                            $whereClause = str_replace($likePatternFull, $replacementPattern, $whereClause);
                        } else if (preg_match('/\[(.*?)%\]/', $likeParam, $innerMatches)) {
                            // Es [valor%] - contiene % solo al final
                            $replacementPattern = " LIKE '{$singleFilterValue}%'";
                            $whereClause = str_replace($likePatternFull, $replacementPattern, $whereClause);
                        } else {
                            // Reemplazar el patrón con el valor del usuario de forma estándar
                            // Asegurar que $filterValue sea string para str_replace
                            $singleFilterValue = is_array($filterValue) ? strval($filterValue[0]) : strval($filterValue);
                            $whereClause = str_replace($filterPattern, $singleFilterValue, $whereClause);
                        }
                    } else {
                        // Reemplazar el patrón con el valor del usuario de forma estándar
                        // Asegurar que $filterValue sea string para str_replace
                        $singleFilterValue = is_array($filterValue) ? strval($filterValue[0]) : strval($filterValue);
                        $whereClause = str_replace($filterPattern, $singleFilterValue, $whereClause);
                    }
                }
            }
        }
        
        // Buscar expresiones LIKE que no tengan comillas simples alrededor
        // Esto capturará patrones como LIKE %" y les dará formato correcto
        $whereClause = preg_replace('/(\s+LIKE\s+)%[\'"]?(\s+|\)|$)/', "$1'%%'$2", $whereClause);
        
        // Buscar otros casos de expresiones LIKE mal formadas
        $whereClause = preg_replace('/(\s+LIKE\s+)[\'"]?%[\'"]?(\s+|\)|$)/', "$1'%%'$2", $whereClause);
        $whereClause = preg_replace('/(\s+LIKE\s+)[\'"]?%+[\'"]?(\s+|\)|$)/', "$1'%%'$2", $whereClause);
        
        // Casos específicos como like %" o LIKE %'
        $whereClause = preg_replace('/\s+LIKE\s+%"/', " LIKE '%%'", $whereClause);
        $whereClause = preg_replace('/\s+LIKE\s+%\'/', " LIKE '%%'", $whereClause);
        
        // Caso con paréntesis y AND/OR - formato específico para el caso de vm.nombremarca like %"
        $whereClause = preg_replace('/(\()([^()]*?LIKE\s+)%"([^()]*?\))/', "$1$2'%%'$3", $whereClause);
        
        // Caso específico para cada nombre de campo que sabemos que está causando problemas
        $whereClause = preg_replace('/(vm\.nombremarca\s+LIKE\s+)%"/', "$1'%%'", $whereClause);
        $whereClause = preg_replace('/(ifa\.nombrefamilia\s+LIKE\s+)%"/', "$1'%%'", $whereClause);
        $whereClause = preg_replace('/(ip\.nombreproducto\s+LIKE\s+)%"/', "$1'%%'", $whereClause);
        $whereClause = preg_replace('/(ie\.descripcionestado\s+LIKE\s+)%"/', "$1'%%'", $whereClause);
        $whereClause = preg_replace('/(il\.descripcionlote\s+LIKE\s+)%"/', "$1'%%'", $whereClause);
        $whereClause = preg_replace('/(ob\.nombrebodega\s+LIKE\s+)%"/', "$1'%%'", $whereClause);
        
        // Buscar LIKE %" al final de la expresión o antes de un AND/OR
        $whereClause = preg_replace('/LIKE\s+%"(\s+(AND|OR|$))/', "LIKE '%%'$1", $whereClause);
        
        // Limpieza final de la cláusula WHERE
        // Eliminar ANDs y ORs repetidos o al inicio/final
        $whereClause = preg_replace('/\s+AND\s+AND\s+/', ' AND ', $whereClause);
        $whereClause = preg_replace('/\s+OR\s+OR\s+/', ' OR ', $whereClause);
        $whereClause = preg_replace('/^\s*AND\s+/', '', $whereClause);
        $whereClause = preg_replace('/^\s*OR\s+/', '', $whereClause);
        $whereClause = preg_replace('/\s+AND\s*$/', '', $whereClause);
        $whereClause = preg_replace('/\s+OR\s*$/', '', $whereClause);
        
        // Manejar paréntesis vacíos o incorrectos
        $whereClause = preg_replace('/\(\s*\)/', '', $whereClause);
        
        // Paso final: verificar si WHERE contiene algo válido o solo caracteres especiales
        $whereNoSpecialChars = preg_replace('/[\s\(\)]+/', '', $whereClause);
        $whereNoSpecialChars = preg_replace('/(AND|OR)/i', '', $whereNoSpecialChars);
        
        // Si la cláusula WHERE no está vacía y contiene algo más que solo caracteres especiales,
        // añadirla a la consulta. De lo contrario, no incluir WHERE en absoluto.
        if (trim($whereClause) !== '' && !empty($whereNoSpecialChars)) {
            $sqlQuery .= " WHERE " . $whereClause;
        }
    }
    
    // Procesamos las cláusulas adicionales
    // Add GROUP BY clause if exists (siempre es seguro añadir GROUP BY)
    if (!empty($report['sql_groupby'])) {
        $sqlQuery .= " GROUP BY " . $report['sql_groupby'];
    }
    
    // Add HAVING clause if exists (requiere un GROUP BY antes)
    if (!empty($report['sql_having']) && !empty($report['sql_groupby'])) {
        $sqlQuery .= " HAVING " . $report['sql_having'];
    }
    
    // Add ORDER BY clause if exists (siempre es seguro añadir ORDER BY)
    if (!empty($report['sql_orderby'])) {
        $sqlQuery .= " ORDER BY " . $report['sql_orderby'];
    }
    
    // Aplicar todas las correcciones al SQL usando el nuevo limpiador mejorado
    $sqlQuery = fixAllSqlIssues($sqlQuery);
    
    // Guardar la consulta original y la corregida para debugging
    $_SESSION['sql_consulta_original_repologfilters'] = $sqlQuery;
    $_SESSION['sql_consulta_fixed_repologfilters'] = $sqlQuery;
    
    // Verificación FINAL para patrones no procesados de tipo [@Campo;val;des;SQL]
    global $debug_info_html;
    
    // Buscar cualquier patrón de tipo [@*;*;*;*] que haya quedado sin procesar
    // LIMPIEZA ESPECÍFICA: Solo eliminar patrones que interfieren con GROUP BY/ORDER BY
    if (preg_match('/[@][^]]*\]\"\s*(GROUP\s+BY|ORDER\s+BY)/i', $sqlQuery)) {
        error_log("Detectado patrón mal formado en verificación final - aplicando corrección específica");
        $sqlQuery = preg_replace('/and\s*\([^)]*[@][^]]*\]\"\s*(GROUP\s+BY|ORDER\s+BY)/i', ' $1', $sqlQuery);
    }
    
    // Aplicar corrección adicional para patrones mal formados
    $sqlQuery = fixMalformedComboPatterns($sqlQuery);
    
    if (preg_match_all('/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/i', $sqlQuery, $unprocessedMatches)) {
        $debug_info[] = "¡ATENCIÓN! Se encontraron " . count($unprocessedMatches[0]) . " patrones de filtro sin procesar en el SQL final. Aplicando corrección general.";
        
        foreach ($unprocessedMatches[0] as $index => $fullPattern) {
            $fieldName = $unprocessedMatches[1][$index]; // El nombre del campo (ej: Zafra)
            $valField = $unprocessedMatches[2][$index];  // El campo para valor (val)
            $desField = $unprocessedMatches[3][$index];  // El campo para texto (des)
            $filterKey = 'filter_' . sanitizeId($fieldName);
            
            $debug_info[] = "Procesando patrón sin resolver: " . $fullPattern;
            $debug_info[] = "Campo: " . $fieldName . ", Filtro POST: " . $filterKey;
            
            // Buscar campos relacionados con este patrón en el SQL
            // Primero buscamos si hay una condición completa para este campo
            if (preg_match('/([a-z0-9_.]+)\s*=\s*\'' . preg_quote($fullPattern, '/') . '\'/i', $sqlQuery, $fieldMatches)) {
                $actualField = $fieldMatches[1]; // El campo real en el SQL (ej: il.idloteproducto)
                $debug_info[] = "Campo SQL encontrado: " . $actualField;
                
                // Caso 1: Cuando no hay valor para el filtro (opción Todos)
                if (!isset($_POST[$filterKey]) || $_POST[$filterKey] === '') {
                    $debug_info[] = "Aplicando corrección para $fieldName (Todos) - Eliminando condición completa";
                    
                    // Patrones para eliminar la condición completa
                    $patterns = array(
                        "/and\s*\(" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'\)/i",
                        "/\s*and\s*" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'/i",
                        "/\(" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'\)/i"
                    );
                    
                    foreach ($patterns as $pattern) {
                        $sqlQueryBefore = $sqlQuery;
                        $sqlQuery = preg_replace($pattern, '', $sqlQuery);
                        
                        if ($sqlQueryBefore !== $sqlQuery) {
                            $debug_info[] = "Patrón aplicado correctamente: " . $pattern;
                            break;
                        }
                    }
                }
                // Caso 2: Hay un valor específico para el filtro
                else if (isset($_POST[$filterKey]) && !empty($_POST[$filterKey])) {
                    $comboValue = $_POST[$filterKey];
                    
                    // Check if it's a multiselection (array of values)
                    $isMultiselection = is_array($comboValue);
                    
                    if ($isMultiselection) {
                        $debug_info[] = "Aplicando corrección para $fieldName (multiselección: " . implode(',', $comboValue) . ") - Reemplazando con IN";
                        
                        // Para multiselección, crear una condición IN con los valores seleccionados
                        $valuesString = "'" . implode("','", array_map('addslashes', $comboValue)) . "'";
                        $inCondition = "$actualField IN ($valuesString)";
                        
                        // Reemplazo específico para multiselección
                        $patterns = array(
                            "/\(" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'\)/i" => "($inCondition)",
                            "/" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'/i" => $inCondition
                        );
                        
                        foreach ($patterns as $pattern => $replacement) {
                            $sqlQueryBefore = $sqlQuery;
                            $sqlQuery = preg_replace($pattern, $replacement, $sqlQuery);
                            
                            if ($sqlQueryBefore !== $sqlQuery) {
                                $debug_info[] = "Multiselección aplicada correctamente: " . $pattern . " -> " . $replacement;
                                break;
                            }
                        }
                    } else {
                        $debug_info[] = "Aplicando corrección para $fieldName (valor: $comboValue) - Reemplazando patrón directo";
                        
                        // Reemplazo más específico primero para selección única
                        $patterns = array(
                            "/\(" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'\)/i" => "(" . $actualField . " = $comboValue)",
                            "/" . preg_quote($actualField, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'/i" => $actualField . " = $comboValue"
                        );
                        
                        foreach ($patterns as $pattern => $replacement) {
                            $sqlQueryBefore = $sqlQuery;
                            $sqlQuery = preg_replace($pattern, $replacement, $sqlQuery);
                            
                            if ($sqlQueryBefore !== $sqlQuery) {
                                $debug_info[] = "Patrón aplicado correctamente: " . $pattern . " -> " . $replacement;
                                break;
                            }
                        }
                    }
                }
            } 
            // Si no encontramos el patrón exacto, intentamos una búsqueda más genérica
            else {
                $debug_info[] = "No se encontró un campo SQL específico para el patrón, aplicando reemplazo directo";
                
                // Caso 1: Cuando no hay valor para el filtro (opción Todos)
                if (!isset($_POST[$filterKey]) || $_POST[$filterKey] === '') {
                    // Intento de reemplazo directo más agresivo para patrones genéricos
                    $sqlQuery = preg_replace("/and\s*\([^=]+=\s*'" . preg_quote($fullPattern, '/') . "'\)/i", "", $sqlQuery);
                    $sqlQuery = preg_replace("/\s+and\s+[^=]+=\s*'" . preg_quote($fullPattern, '/') . "'/i", "", $sqlQuery);
                }
                // Caso 2: Hay un valor específico para el filtro
                else if (isset($_POST[$filterKey]) && !empty($_POST[$filterKey])) {
                    $comboValue = $_POST[$filterKey];
                    
                    // Check if it's a multiselection (array of values)
                    $isMultiselection = is_array($comboValue);
                    
                    if ($isMultiselection) {
                        // Para multiselección, crear una condición IN con los valores seleccionados
                        $valuesString = "'" . implode("','", array_map('addslashes', $comboValue)) . "'";
                        
                        // Detectar el campo de la condición para construir un IN()
                        if (preg_match('/([a-zA-Z0-9_]+\.?[a-zA-Z0-9_]+)\s*=\s*["\']?' . preg_quote($fullPattern, '/') . '["\']?/', $sqlQuery, $fieldMatch)) {
                            $fieldName = $fieldMatch[1];
                            $inCondition = "$fieldName IN ($valuesString)";
                            
                            // Reemplazar la condición de igualdad con IN
                            $patterns = [
                                "/\(" . preg_quote($fieldName, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'\)/i" => "($inCondition)",
                                "/" . preg_quote($fieldName, '/') . "\s*=\s*'" . preg_quote($fullPattern, '/') . "'/i" => $inCondition,
                                "/\(" . preg_quote($fieldName, '/') . "\s*=\s*\"" . preg_quote($fullPattern, '/') . "\"\)/i" => "($inCondition)",
                                "/" . preg_quote($fieldName, '/') . "\s*=\s*\"" . preg_quote($fullPattern, '/') . "\"/i" => $inCondition
                            ];
                            
                            foreach ($patterns as $pattern => $replacement) {
                                $sqlQueryBefore = $sqlQuery;
                                $sqlQuery = preg_replace($pattern, $replacement, $sqlQuery);
                                if ($sqlQueryBefore !== $sqlQuery) {
                                    $debug_info[] = "Multiselección aplicada: $pattern -> $replacement";
                                    break;
                                }
                            }
                        } else {
                            // Fallback: reemplazar directamente el patrón con IN
                            $sqlQuery = str_replace($fullPattern, "IN ($valuesString)", $sqlQuery);
                        }
                    } else {
                        // Single selection - mantener lógica original
                        // SOLUCIÓN GENERAL: Asegurar comillas consistentes alrededor del valor
                        // Detectar si el patrón está entre comillas simples o dobles y mantener consistencia
                        if (strpos($sqlQuery, "'" . $fullPattern . "'") !== false) {
                            // Patrón está entre comillas simples - reemplazar con comillas simples
                            $sqlQuery = str_replace("'" . $fullPattern . "'", "'" . $comboValue . "'", $sqlQuery);
                        } else if (strpos($sqlQuery, '"' . $fullPattern . '"') !== false) {
                            // Patrón está entre comillas dobles - reemplazar con comillas dobles
                            $sqlQuery = str_replace('"' . $fullPattern . '"', '"' . $comboValue . '"', $sqlQuery);
                        } else {
                            // Patrón sin comillas específicas - usar el valor tal como viene
                            $sqlQuery = str_replace($fullPattern, $comboValue, $sqlQuery);
                        }
                    }
                }
            }
        }
        
        // Limpieza general de SQL para prevenir errores de sintaxis
        $debug_info[] = "Aplicando limpieza general del SQL para prevenir errores de sintaxis";
        
        // Limpieza específica para multiselección - eliminar doble IN y comillas malformadas
        $sqlQuery = preg_replace('/\s+IN\s+"IN\s*\(/i', ' IN (', $sqlQuery);
        $sqlQuery = preg_replace('/\s+IN\s+\'IN\s*\(/i', ' IN (', $sqlQuery);
        $sqlQuery = preg_replace('/IN\s*"IN\s*\(/i', 'IN (', $sqlQuery);
        $sqlQuery = preg_replace('/IN\s*\'IN\s*\(/i', 'IN (', $sqlQuery);
        
        // Casos más específicos de comillas malformadas como IN "("9","10"" ))
        $sqlQuery = preg_replace('/IN\s+"?\(\s*"/i', 'IN (', $sqlQuery);
        $sqlQuery = preg_replace('/IN\s+\'\(\s*\'/i', 'IN (', $sqlQuery);
        
        // Patrón muy específico para IN "("valores"" ))
        $sqlQuery = preg_replace('/IN\s*"\s*\(\s*"/i', 'IN ("', $sqlQuery);
        $sqlQuery = preg_replace('/"\s*"\s*\)\s*\)/i', '")', $sqlQuery);
        
        // CORRECCIÓN ESPECIAL: Si el patrón está entre comillas, corregir IN "(...)" -> IN (...)
        $sqlQuery = preg_replace('/IN\s+"\s*\(([^"]+)\)\s*"/i', 'IN ($1)', $sqlQuery);
        
        // LIMPIEZA UNIVERSAL COMPLETA: Usar la función universal para cualquier caso
        $sqlQuery = cleanMultiselectionInConditions($sqlQuery);
        
        // Eliminar comillas dobles malformadas al final de condiciones IN
        $sqlQuery = preg_replace('/""\s*\)\s*\)/i', '")', $sqlQuery);
        $sqlQuery = preg_replace('/"\s*"\s*\)\s*\)/i', '")', $sqlQuery);
        $sqlQuery = preg_replace('/,"\s*"\s*\)/i', ')', $sqlQuery);
        $sqlQuery = preg_replace('/,""\s*\)/i', ')', $sqlQuery);
        $sqlQuery = preg_replace('/"\s*\)\s*\)/i', ')', $sqlQuery);
        
        // Casos específicos de comillas dobles extra al final
        $sqlQuery = preg_replace('/,\s*"\s*"\s*\)/i', ')', $sqlQuery);
        $sqlQuery = preg_replace('/"\s*,\s*\)/i', ')', $sqlQuery);
        
        // Corregir problemas específicos con IN y paréntesis
        $sqlQuery = preg_replace('/\(\s*IN\s*\(/i', '(', $sqlQuery);
        $sqlQuery = preg_replace('/IN\s*\(\s*IN\s*\(/i', 'IN (', $sqlQuery);
        
        // Limpieza de espacios extra alrededor de IN
        $sqlQuery = preg_replace('/\s+IN\s+\s+/i', ' IN ', $sqlQuery);
        
        $debug_info[] = "Aplicada limpieza específica para multiselección";
        
        // Corregir problemas de sintaxis SQL causados por los reemplazos
        $sqlQuery = preg_replace("/and\s+and/i", "and", $sqlQuery);
        $sqlQuery = preg_replace("/where\s+and/i", "where", $sqlQuery);
        $sqlQuery = preg_replace("/and\s*\(\s*\)/i", "", $sqlQuery);
        $sqlQuery = preg_replace("/\(\s*\)/i", "", $sqlQuery);
        $sqlQuery = preg_replace("/\s+and\s*$/i", "", $sqlQuery);
        
        // Eliminar condiciones vacías
        $sqlQuery = preg_replace("/where\s*$/i", "", $sqlQuery);
        
        // Aplicar limpieza avanzada usando la función especializada
        require_once 'sqlcleaner.php';
        $debug_info[] = "Aplicando limpieza especializada con fixExtraAndBeforeClosingParenthesis()";
        $sqlQuery = fixExtraAndBeforeClosingParenthesis($sqlQuery);
        $sqlQuery = fixAllSqlIssues($sqlQuery);
        

        
        // SOLUCIÓN UNIVERSAL: Si aún quedan patrones no procesados, aplicar limpieza general
        if (preg_match('/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/i', $sqlQuery)) {
            $debug_info[] = "¡ADVERTENCIA! Todavía quedan patrones sin procesar. Aplicando limpieza universal.";
            
            // Eliminar condiciones completas que contengan patrones no resueltos
            $sqlQuery = preg_replace('/and\s*\([^)]*=\s*["\']?\[@[^]]*\]["\']?\s*\)/i', '', $sqlQuery);
            
            // Si aún quedan patrones, reemplazar con comodín
            $sqlQuery = preg_replace("/'\[@[^]]*\]'/i", "'%'", $sqlQuery);
        }
    }
    
    // Generar HTML de depuración si hay información disponible
    if (isset($debug_info) && !empty($debug_info)) {
        global $debug_info_html;
        $debug_info_html = '<div class="debug-info" style="background:#f8f9fa;border:1px solid #ddd;padding:10px;margin-top:20px;font-size:12px;font-family:monospace;">';
        $debug_info_html .= '<h4>Información de Depuración de Patrones [@Campo;val;des;SQL]:</h4>';
        $debug_info_html .= '<ul>';
        foreach ($debug_info as $info) {
            $debug_info_html .= '<li>' . htmlspecialchars($info) . '</li>';
        }
        $debug_info_html .= '</ul>';
        
        // Aplicamos la función de reemplazo de patrones no sustituidos
        // Este paso asegura que la vista previa y el SQL ejecutado sean idénticos
        $finalSql = reemplazarPatronesComboNoSustituidos($sqlQuery, $filters, $filterValues);
        
        // Añadir información sobre el SQL final
        $debug_info_html .= '<h4>SQL final generado (EXACTAMENTE el mismo que se ejecutará):</h4>';
        $debug_info_html .= '<pre>' . htmlspecialchars($finalSql) . '</pre>';
        
        // Usar este SQL final como retorno en lugar del original
        $sqlQuery = $finalSql;
        
        $debug_info_html .= '</div>';
    }
    
    return $sqlQuery;
}

// Variables para la vista previa del SQL
$previewSQL = '';
$showPreview = false;

// When form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($report)) {
    $filterValues = $_POST;
    
    // Build the complete SQL query
    $sqlQuery = buildSqlQuery($report, $filterValues);
    
    // Check if it's a preview request or submit
    if (isset($_POST['action']) && $_POST['action'] === 'preview') {
        // ******** IMPORTANTE: ESTA SECCIÓN DEBE SER IDÉNTICA A LA SOLUCIÓN EN reporte.php ********
        // Para garantizar que la vista previa muestre EXACTAMENTE el SQL final, implementamos
        // todas las transformaciones de reporte.php aquí - código copiado al 100%
        
        require_once 'sqlcleaner.php';
        
        // PASO PRELIMINAR: Fechas
        // Reemplazar fechas específicas como las que se hacen en reporte.php
        $startDate = isset($filterValues['filter_del']) ? $filterValues['filter_del'] : date('Y/m/d');
        $endDate = isset($filterValues['filter_al']) ? $filterValues['filter_al'] : date('Y/m/d');
        
        // Extender fechas para incluir todo el día
        $startTimeStr = " 00:00:00";
        $endTimeStr = " 23:59:59";
        
        // Formatear fechas completas
        $startDateFormatted = $startDate . $startTimeStr;
        $endDateFormatted = $endDate . $endTimeStr;
        
        // Reemplazar patrones de fecha justo como en reporte.php
        $sqlQuery = str_replace("[#Del]", "\"$startDateFormatted\"", $sqlQuery);
        $sqlQuery = str_replace("[#Al]", "\"$endDateFormatted\"", $sqlQuery);
        
        // PASO 1: Aplicar el método para eliminar cláusulas AND completas para combos vacíos
        if (!empty($emptyComboFilters)) {
            $debug_info[] = "Filtros combo vacíos encontrados: " . implode(", ", $emptyComboFilters);
            $sqlQuery = eliminarClausulasAndCompletas($sqlQuery, $emptyComboFilters);
        }
        
        // DEBUGGING: Registrar todos los valores de filtro recibidos para analizar el problema
        error_log("=== VALORES DE FILTROS RECIBIDOS (DEBUG) ===");
        foreach ($filterValues as $key => $value) {
            error_log("Filtro: $key, Valor: " . ($value === '' ? '(vacío)' : $value));
        }
        
        // PASO 2: Aplicar todas las limpiezas generales
        // Igual que en reporte.php
        $sqlQuery = fixAllSqlIssues($sqlQuery);
        $sqlQuery = eliminaParentesisExcesivos($sqlQuery);
        // Aplicar una segunda vez para casos difíciles con múltiples paréntesis
        $sqlQuery = eliminaParentesisExcesivos($sqlQuery);
        
        // PASO 3: SOLUCIÓN DEFINITIVA SIMPLIFICADA Y MEJORADA
        // Normalizar la consulta SQL para facilitar su procesamiento
        
        // PASO PRELIMINAR 0: CORRECCIÓN EXACTA PARA EL REPORTE 5
        // Detección absoluta del patrón en reporte 5, que es extremadamente específico
        $patronReporte5 = '/\)\)\s+and\s+\(obd\.idbodega\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s+ORDER\s+BY/i';
        if (preg_match($patronReporte5, $sqlQuery, $matches)) {
            $fullMatch = $matches[0];
            $valor = $matches[1];
            $correcto = ')) and (obd.idbodega = \'' . $valor . '\') ORDER BY';
            $sqlQuery = str_replace($fullMatch, $correcto, $sqlQuery);
            error_log("Vista Previa: ¡SOLUCIÓN ESPECÍFICA APLICADA PARA REPORTE 5!");
        }

        // PASO PRELIMINAR 1: SOLUCIÓN GENERAL PARA PARÉNTESIS FALTANTES
        // Buscar cualquier condición del tipo "and (campo = 'valor' ORDER BY" y cerrar el paréntesis
        $patronGeneral = '/\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]*)[\'\"]\s+(ORDER\s+BY)/i';
        if (preg_match($patronGeneral, $sqlQuery)) {
            $sqlQuery = preg_replace($patronGeneral, ') $1 ($2 = \'$3\') $4', $sqlQuery);
            error_log("Vista Previa: Aplicada corrección general para condiciones sin cerrar antes de ORDER BY");
        }
        
        // PASO PRELIMINAR 2: SOLUCIÓN PARA DOBLE PARÉNTESIS
        // Este patrón busca: ")) and (campo = 'valor' ORDER BY" (doble paréntesis al inicio)
        $patronDobleParentesis = '/\)\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s+(ORDER\s+BY)/i';
        if (preg_match($patronDobleParentesis, $sqlQuery, $matches)) {
            $fullMatch = $matches[0];
            $operator = $matches[1];
            $campo = $matches[2];
            $valor = $matches[3];
            $orderBy = $matches[4];
            
            $replacement = ')) ' . $operator . ' (' . $campo . ' = \'' . $valor . '\') ' . $orderBy;
            $sqlQuery = str_replace($fullMatch, $replacement, $sqlQuery);
            error_log("Vista Previa: Corregido doble paréntesis con patrón general");
        }
        
        // PASO 0: Normalizar la consulta 
        // Reemplazar múltiples espacios con uno solo
        $sqlQuery = preg_replace('/\s+/', ' ', $sqlQuery);
        
        // Verificar si hay el patrón "ORDER BY"
        if (stripos($sqlQuery, 'ORDER BY') !== false) {
            // PASO 1: Separar la consulta en dos partes (antes y después de ORDER BY)
            list($beforeOrder, $afterOrder) = explode('ORDER BY', $sqlQuery, 2);
            
            // PASO 1.5: DETECCIÓN DE ERRORES COMUNES
            // Buscar condiciones sin cerrar antes de ORDER BY
            $matches = [];
            if (preg_match_all('/and\s+\(([a-zA-Z0-9_.]+)\s*=\s*\'([^\']*)\'\s*$/', $beforeOrder, $matches)) {
                $condition = $matches[0][0];
                $field = $matches[1][0];
                $value = $matches[2][0];
                
                // Reemplazar la condición sin cerrar por una correctamente formada
                $replacement = "and ($field = '$value')";
                $beforeOrder = str_replace($condition, $replacement, $beforeOrder);
            }
            
            // PASO 2: Buscar cláusula fecha y asegurar que termina correctamente
            if (preg_match('/\(lt\.fecha BETWEEN "[^"]+"\s+AND\s+"[^"]+"\)/', $beforeOrder, $matches)) {
                $fechaClause = $matches[0];
                
                // Obtener todo lo que está después de la cláusula fecha
                $posAfterFecha = strpos($beforeOrder, $fechaClause) + strlen($fechaClause);
                $afterFechaContent = substr($beforeOrder, $posAfterFecha);
                
                // Verificación especial para detectar condiciones adicionales después de la fecha
                if (preg_match('/\)\s+and\s+\(([a-zA-Z0-9_.]+)\s*=/', $afterFechaContent)) {
                    // Si hay un paréntesis extra después de la cláusula fecha, eliminarlo
                    if (substr(trim($afterFechaContent), 0, 1) === ')' && substr_count($beforeOrder, '(') < substr_count($beforeOrder, ')')) {
                        $afterFechaContent = substr(trim($afterFechaContent), 1);
                    }
                    
                    // Asegurarse de que la condición adicional termina con un paréntesis
                    if (trim($afterFechaContent) !== '' && substr(trim($afterFechaContent), -1) !== ')') {
                        $afterFechaContent .= ')';
                    }
                }
                
                // Reconstruir la parte antes de ORDER BY
                $beforeOrder = substr($beforeOrder, 0, $posAfterFecha) . $afterFechaContent;
            }
            
            // PASO 3: Asegurar que hay un balance correcto de paréntesis antes de ORDER BY
            $openCount = substr_count($beforeOrder, '(');
            $closeCount = substr_count($beforeOrder, ')');
            
            // Solución ultra simple pero efectiva: forzar un balance correcto
            if ($openCount != $closeCount) {
                if ($openCount > $closeCount) {
                    // Faltan paréntesis de cierre
                    $beforeOrder .= str_repeat(')', $openCount - $closeCount);
                } else {
                    // Sobran paréntesis de cierre
                    // Simplemente quitar los paréntesis excesivos al final
                    $beforeOrder = rtrim($beforeOrder);
                    while (substr($beforeOrder, -1) === ')' && $closeCount > $openCount) {
                        $beforeOrder = substr($beforeOrder, 0, -1);
                        $closeCount--;
                    }
                }
            }
            
            // PASO 4: Reconstruir la consulta con las partes corregidas
            $sqlQuery = trim($beforeOrder) . ' ORDER BY ' . $afterOrder;
            
            // PASO 5: Verificación final para casos extremos
            // Eliminar dobles paréntesis vacíos (que pueden quedar como resultado de filtros eliminados)
            $sqlQuery = preg_replace('/\(\s*\)/', '', $sqlQuery);
            
            // Reemplazar AND ( ) con nada
            $sqlQuery = preg_replace('/AND\s*\(\s*\)/', '', $sqlQuery);
            
            // Eliminar dobles WHERE
            $sqlQuery = preg_replace('/WHERE\s+WHERE/', 'WHERE', $sqlQuery);
            
            // Eliminar WHERE vacío antes de ORDER BY
            $sqlQuery = preg_replace('/WHERE\s+ORDER\s+BY/', 'ORDER BY', $sqlQuery);
        }
        
        // Aplicar todas las correcciones generales - incluyendo la nueva solución universal
        $sqlQuery = fixAllSqlIssues($sqlQuery);
        
        // Aplicar específicamente la solución universal de paréntesis desbalanceados
        // Esto es crucial porque asegura que la vista previa muestre exactamente lo mismo que se ejecutará
        if (function_exists('fixUnbalancedParenthesisBeforeOrderBy')) {
            $sqlQuery = fixUnbalancedParenthesisBeforeOrderBy($sqlQuery);
            error_log("Vista Previa: Aplicada solución universal para paréntesis desbalanceados");
        }
        
        // Aplicar función específica para eliminación de paréntesis excesivos
        if (function_exists('eliminaParentesisExcesivos')) {
            $sqlQuery = eliminaParentesisExcesivos($sqlQuery);
            // Aplicar una segunda vez para casos difíciles con múltiples paréntesis
            $sqlQuery = eliminaParentesisExcesivos($sqlQuery);
        }
        
        // Aplicamos todas las transformaciones que se aplicarían en reporte.php
        // para asegurar que la vista previa sea exactamente igual al SQL ejecutado
        
        // 1. Verificar si hay variables de fecha disponibles (similar a reporte.php)
        $startDate = date("Y/m/d");
        $endDate = date("Y/m/d");
        
        // Buscar fechas en SESSION (más común)
        if (isset($_SESSION['user_selected_date_filter_del'])) {
            $startDate = $_SESSION['user_selected_date_filter_del'];
        }
        
        if (isset($_SESSION['user_selected_date_filter_al'])) {
            $endDate = $_SESSION['user_selected_date_filter_al'];
        }
        
        // O buscar en los valores de filtros actuales
        foreach ($filterValues as $key => $value) {
            if (stripos($key, 'del') !== false || stripos($key, 'inicio') !== false || stripos($key, 'desde') !== false) {
                if (!empty($value)) {
                    $startDate = $value;
                }
            }
            else if (stripos($key, 'al') !== false || stripos($key, 'fin') !== false || stripos($key, 'hasta') !== false) {
                if (!empty($value)) {
                    $endDate = $value;
                }
            }
        }
        
        // 2. Aplicar fechas a la consulta SQL
        $sqlQuery = preg_replace('/BETWEEN\s+["\']([^"\']+)["\']\s+AND\s+["\']([^"\']+)["\']/i', 
                          "BETWEEN \"$startDate 00:00:00\" AND \"$endDate 23:59:59\"", 
                          $sqlQuery);
                          
        // 3. Aplicar corrección final de paréntesis (igual que en reporte.php)
        $sqlQuery = eliminaParentesisExcesivos($sqlQuery);
        
        // Guardamos el SQL completo en la sesión para mostrarlo como SQL ejecutado
        // IMPORTANTE: Aplicar la misma limpieza universal que en reporte.php para sincronizar
        $sqlQueryLimpio = cleanSqlUniversal($sqlQuery);
        
        $_SESSION['sql_final_ejecutado'] = $sqlQueryLimpio;
        $previewSQL = $sqlQueryLimpio;
        $showPreview = true;
        
        // For debugging purposes, show exactly what filter values were received
        $debugInfo = '';
        $debugInfo .= "<h4>Valores de filtros recibidos:</h4>";
        $debugInfo .= "<pre>";
        foreach ($filterValues as $key => $value) {
            $debugInfo .= htmlspecialchars($key . ": " . $value) . "\n";
        }
        $debugInfo .= "</pre>";
        
        // Añadir información adicional sobre todos los reportes
        $debugInfo .= "<h4>Información de Procesamiento SQL:</h4>";
        $debugInfo .= "<p>Esta consulta muestra exactamente el mismo SQL que se ejecutará al generar el reporte.</p>";
        $debugInfo .= "<pre>SQL completamente procesado y listo para ejecución:</pre>";
        
        // Mostrar información específica para el reporte 18 (RecepcionDirecta)
        if ($reportId == 18) {
            $debugInfo .= "<h4>Información de Depuración Especial para RecepcionDirecta (ID 18):</h4>";
            $debugInfo .= "<p>Este reporte requiere atención especial cuando hay filtros específicos.</p>";
        }
        
        // Show only for test or development
        $showDebug = true;
    } else {
        // Aplicamos las mismas transformaciones que en vista previa y en reporte.php
        // para garantizar consistencia completa
        
        // Primero aplicamos todas las transformaciones de sqlcleaner
        require_once 'sqlcleaner.php';
        
        // Aplicar el nuevo método para eliminar cláusulas AND completas para combos vacíos
        if (!empty($emptyComboFilters)) {
            $debug_info[] = "Filtros combo vacíos encontrados: " . implode(", ", $emptyComboFilters);
            $sqlQuery = eliminarClausulasAndCompletas($sqlQuery, $emptyComboFilters);
        }
        
        // Aplicar limpieza general de SQL - incluyendo la nueva solución universal
        $sqlQuery = fixAllSqlIssues($sqlQuery);
        
        // Aplicar específicamente la solución universal de paréntesis desbalanceados
        if (function_exists('fixUnbalancedParenthesisBeforeOrderBy')) {
            $sqlQuery = fixUnbalancedParenthesisBeforeOrderBy($sqlQuery);
            error_log("Submit: Aplicada solución universal para paréntesis desbalanceados");
        }
        
        // APLICAR LA SOLUCIÓN DEFINITIVA Y MEJORADA (exactamente igual que en reporte.php)
        
        // PASO PRELIMINAR 0: CORRECCIÓN EXACTA PARA EL REPORTE 5
        // Detección absoluta del patrón en reporte 5, que es extremadamente específico
        $patronReporte5 = '/\)\)\s+and\s+\(obd\.idbodega\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s+ORDER\s+BY/i';
        if (preg_match($patronReporte5, $sqlQuery, $matches)) {
            $fullMatch = $matches[0];
            $valor = $matches[1];
            $correcto = ')) and (obd.idbodega = \'' . $valor . '\') ORDER BY';
            $sqlQuery = str_replace($fullMatch, $correcto, $sqlQuery);
            error_log("Submit: ¡SOLUCIÓN ESPECÍFICA APLICADA PARA REPORTE 5!");
        }

        // PASO PRELIMINAR 1: SOLUCIÓN GENERAL PARA PARÉNTESIS FALTANTES
        // Buscar cualquier condición del tipo "and (campo = 'valor' ORDER BY" y cerrar el paréntesis
        $patronGeneral = '/\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]*)[\'\"]\s+(ORDER\s+BY)/i';
        if (preg_match($patronGeneral, $sqlQuery)) {
            $sqlQuery = preg_replace($patronGeneral, ') $1 ($2 = \'$3\') $4', $sqlQuery);
            error_log("Submit: Aplicada corrección general para condiciones sin cerrar antes de ORDER BY");
        }
        
        // PASO PRELIMINAR 2: SOLUCIÓN PARA DOBLE PARÉNTESIS
        // Este patrón busca: ")) and (campo = 'valor' ORDER BY" (doble paréntesis al inicio)
        $patronDobleParentesis = '/\)\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s+(ORDER\s+BY)/i';
        if (preg_match($patronDobleParentesis, $sqlQuery, $matches)) {
            $fullMatch = $matches[0];
            $operator = $matches[1];
            $campo = $matches[2];
            $valor = $matches[3];
            $orderBy = $matches[4];
            
            $replacement = ')) ' . $operator . ' (' . $campo . ' = \'' . $valor . '\') ' . $orderBy;
            $sqlQuery = str_replace($fullMatch, $replacement, $sqlQuery);
            error_log("Submit: Corregido doble paréntesis con patrón general");
        }
        
        // PASO 0: Normalizar la consulta 
        // Reemplazar múltiples espacios con uno solo
        $sqlQuery = preg_replace('/\s+/', ' ', $sqlQuery);
        
        // Verificar si hay el patrón "ORDER BY"
        if (stripos($sqlQuery, 'ORDER BY') !== false) {
            // PASO 1: Separar la consulta en dos partes (antes y después de ORDER BY)
            list($beforeOrder, $afterOrder) = explode('ORDER BY', $sqlQuery, 2);
            
            // PASO 1.5: DETECCIÓN DE ERRORES COMUNES
            // Buscar condiciones sin cerrar antes de ORDER BY
            $matches = [];
            if (preg_match_all('/and\s+\(([a-zA-Z0-9_.]+)\s*=\s*\'([^\']*)\'\s*$/', $beforeOrder, $matches)) {
                error_log("Encontrada condición sin cerrar: " . $matches[0][0]);
                $condition = $matches[0][0];
                $field = $matches[1][0];
                $value = $matches[2][0];
                
                // Reemplazar la condición sin cerrar por una correctamente formada
                $replacement = "and ($field = '$value')";
                $beforeOrder = str_replace($condition, $replacement, $beforeOrder);
                
                error_log("Corregida condición sin cerrar");
            }
            
            // PASO 2: Buscar cláusula fecha y asegurar que termina correctamente
            if (preg_match('/\(lt\.fecha BETWEEN "[^"]+"\s+AND\s+"[^"]+"\)/', $beforeOrder, $matches)) {
                $fechaClause = $matches[0];
                
                // Obtener todo lo que está después de la cláusula fecha
                $posAfterFecha = strpos($beforeOrder, $fechaClause) + strlen($fechaClause);
                $afterFechaContent = substr($beforeOrder, $posAfterFecha);
                
                // Verificación especial para detectar condiciones adicionales después de la fecha
                if (preg_match('/\)\s+and\s+\(([a-zA-Z0-9_.]+)\s*=/', $afterFechaContent)) {
                    error_log("Detectadas condiciones adicionales después de fecha");
                    
                    // Si hay un paréntesis extra después de la cláusula fecha, eliminarlo
                    if (substr(trim($afterFechaContent), 0, 1) === ')' && substr_count($beforeOrder, '(') < substr_count($beforeOrder, ')')) {
                        error_log("Eliminando paréntesis extra después de la fecha");
                        $afterFechaContent = substr(trim($afterFechaContent), 1);
                    }
                    
                    // Asegurarse de que la condición adicional termina con un paréntesis
                    if (trim($afterFechaContent) !== '' && substr(trim($afterFechaContent), -1) !== ')') {
                        error_log("Contenido después de fecha necesita cierre: " . $afterFechaContent);
                        $afterFechaContent .= ')';
                    }
                }
                
                // Reconstruir la parte antes de ORDER BY
                $beforeOrder = substr($beforeOrder, 0, $posAfterFecha) . $afterFechaContent;
            }
            
            // PASO 3: Asegurar que hay un balance correcto de paréntesis antes de ORDER BY
            $openCount = substr_count($beforeOrder, '(');
            $closeCount = substr_count($beforeOrder, ')');
            
            // Solución ultra simple pero efectiva: forzar un balance correcto
            if ($openCount != $closeCount) {
                if ($openCount > $closeCount) {
                    // Faltan paréntesis de cierre
                    $beforeOrder .= str_repeat(')', $openCount - $closeCount);
                } else {
                    // Sobran paréntesis de cierre
                    // Simplemente quitar los paréntesis excesivos al final
                    $beforeOrder = rtrim($beforeOrder);
                    while (substr($beforeOrder, -1) === ')' && $closeCount > $openCount) {
                        $beforeOrder = substr($beforeOrder, 0, -1);
                        $closeCount--;
                    }
                }
            }
            
            // PASO 4: Reconstruir la consulta con las partes corregidas
            $sqlQuery = trim($beforeOrder) . ' ORDER BY ' . $afterOrder;
            
            // PASO 5: Verificación final para casos extremos
            // Eliminar dobles paréntesis vacíos (que pueden quedar como resultado de filtros eliminados)
            $sqlQuery = preg_replace('/\(\s*\)/', '', $sqlQuery);
            
            // Reemplazar AND ( ) con nada
            $sqlQuery = preg_replace('/AND\s*\(\s*\)/', '', $sqlQuery);
            
            // Eliminar dobles WHERE
            $sqlQuery = preg_replace('/WHERE\s+WHERE/', 'WHERE', $sqlQuery);
            
            // Eliminar WHERE vacío antes de ORDER BY
            $sqlQuery = preg_replace('/WHERE\s+ORDER\s+BY/', 'ORDER BY', $sqlQuery);
        }
        
        // NUEVA SOLUCIÓN: Aplicar reemplazo de patrones de combo no sustituidos
        // Esto asegura que la SQL guardada sea idéntica a la que se mostrará y ejecutará
        $finalSql = reemplazarPatronesComboNoSustituidos($sqlQuery, $filters, $filterValues);
        
        // Log para verificar que el SQL sea idéntico al mostrado en la vista previa
        error_log("SQL FINAL (después de reemplazarPatronesComboNoSustituidos): " . $finalSql);
        
        // Guardar la SQL generada en la sesión
        $_SESSION['sql_consulta'] = $finalSql;
        
        // Guardar también en sql_final_ejecutado para consistencia con el botón "Mostrar SQL"
        // Aplicar la misma limpieza universal que en reporte.php
        $finalSqlLimpio = cleanSqlUniversal($finalSql);
        $_SESSION['sql_final_ejecutado'] = $finalSqlLimpio;
        
        // Guardar una copia original para referencia
        $_SESSION['sql_consulta_original'] = $finalSql;
        
        // Guardar el ID del reporte
        $_SESSION['repolog_report_id'] = $reportId;
        
        // Guardar campos de subtotales
        if (!empty($report['subtotales_agrupaciones'])) {
            $_SESSION['subtotales_agrupaciones'] = $report['subtotales_agrupaciones'];
        } else {
            unset($_SESSION['subtotales_agrupaciones']);
        }
        
        // Procesar el campo de subtotales
        if (!empty($report['subtotales_subtotal'])) {
            // Mantener los campos originales sin FORMAT para permitir sumatoria de múltiples campos
            $_SESSION['subtotales_subtotal'] = $report['subtotales_subtotal'];
            
            // Para debug, guardar también el valor original
            $_SESSION['subtotales_subtotal_original'] = $report['subtotales_subtotal'];
        } else if ($reportId == 7) {
            // Para el reporte 7, siempre usar múltiples campos separados por coma
            $_SESSION['subtotales_subtotal'] = 'lr.cantidadrecibida1,lr.cantidadrecibida2';
            
            // Para debug, guardar también el valor original
            $_SESSION['subtotales_subtotal_original'] = 'lr.cantidadrecibida1,lr.cantidadrecibida2';
        } else {
            unset($_SESSION['subtotales_subtotal']);
        }
        
        // Guardar los filtros y valores de filtros en la sesión para uso posterior (en reporte.php)
        $_SESSION['filters'] = $filters;
        $_SESSION['filter_values'] = $filterValues;
        
        // Save extension functionality params if available
        if (!empty($report['url_include'])) {
            $_SESSION['repolog_url_include'] = $report['url_include'];
        } else {
            unset($_SESSION['repolog_url_include']);
        }
        
        if (!empty($report['url_include_despues'])) {
            $_SESSION['repolog_url_include_despues'] = $report['url_include_despues'];
        } else {
            unset($_SESSION['repolog_url_include_despues']);
        }
        
        if (!empty($report['sppre'])) {
            $_SESSION['repolog_sppre'] = $report['sppre'];
        } else {
            unset($_SESSION['repolog_sppre']);
        }
        
        if (!empty($report['sppos'])) {
            $_SESSION['repolog_sppos'] = $report['sppos'];
        } else {
            unset($_SESSION['repolog_sppos']);
        }
        
        // Guardar los filtros aplicados para mostrarlos como subtítulo del reporte, 
        // mostrando valores descriptivos en lugar de IDs para combos
        $appliedFilters = [];
        foreach ($filters as $filter) {
            if ($filter['type'] != 'session') {
                $filterKey = $filter['id'];
                $label = isset($filter['label']) ? $filter['label'] : (isset($filter['name']) ? $filter['name'] : '');
                
                // Solo guardar los filtros que tienen valor
                if (isset($filterValues[$filterKey]) && !empty($filterValues[$filterKey])) {
                    $filterValue = $filterValues[$filterKey];
                    $displayValue = $filterValue; // Por defecto, usamos el valor tal cual
                    
                    // NUEVA LÓGICA: Manejar filtros de multiselección (arrays)
                    if (is_array($filterValue)) {
                        $displayValues = [];
                        
                        // Para cada valor seleccionado, buscar su descripción
                        foreach ($filterValue as $singleValue) {
                            $singleDisplayValue = $singleValue; // Valor por defecto
                            
                            // Si es un filtro tipo combo, buscar la descripción
                            if ($filter['type'] === 'combo' && isset($filter['options']) && is_array($filter['options'])) {
                                foreach ($filter['options'] as $option) {
                                    if (isset($option['value']) && $option['value'] == $singleValue) {
                                        $singleDisplayValue = $option['text'];
                                        break;
                                    }
                                }
                            }
                            
                            $displayValues[] = $singleDisplayValue;
                        }
                        
                        $displayValue = implode(', ', $displayValues);
                    }
                    // LÓGICA ORIGINAL: Para filtros de selección individual
                    else if ($filter['type'] === 'combo' && isset($filter['options']) && is_array($filter['options'])) {
                        // Primero intentamos buscar en las opciones del combo
                        foreach ($filter['options'] as $option) {
                            if (isset($option['value']) && $option['value'] == $filterValue) {
                                $displayValue = $option['text'];
                                break;
                            }
                        }
                    }
                    // Si es un filtro con query (select dinámico), buscar el valor en la base de datos
                    else if ($filter['type'] === 'combo' && isset($filter['query']) && !empty($filter['query'])) {
                        try {
                            // Crear conexión a BD
                            $lookupPdo = new PDO(DB_DSN, DB_USER, DB_PASS);
                            $lookupPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            // Extraer tablas y columnas de la query original
                            $query = $filter['query'];
                            
                            // NUEVA LÓGICA: Manejar multiselección con queries dinámicas
                            if (is_array($filterValue)) {
                                $displayValues = [];
                                
                                foreach ($filterValue as $singleValue) {
                                    $singleDisplayValue = $singleValue; // Valor por defecto
                                    
                                    // Analizar si el query tiene estructura value,text o es una consulta completa
                                    if (preg_match('/SELECT\s+([^,]+),\s*([^,\s]+)\s+FROM\s+([^\s;]+)/i', $query, $matches)) {
                                        $valueColumn = trim($matches[1]);
                                        $textColumn = trim($matches[2]);
                                        $tableName = trim($matches[3]);
                                        
                                        // Construir consulta para buscar el valor descriptivo
                                        $lookupQuery = "SELECT $textColumn FROM $tableName WHERE $valueColumn = ?";
                                        $stmt = $lookupPdo->prepare($lookupQuery);
                                        $stmt->execute([$singleValue]);
                                        
                                        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $singleDisplayValue = reset($row); // Primer valor del array asociativo
                                        }
                                    }
                                    
                                    $displayValues[] = $singleDisplayValue;
                                }
                                
                                $displayValue = implode(', ', $displayValues);
                            }
                            // LÓGICA ORIGINAL: Para filtros individuales
                            else {
                                // Analizar si el query tiene estructura value,text o es una consulta completa
                                if (preg_match('/SELECT\s+([^,]+),\s*([^,\s]+)\s+FROM\s+([^\s;]+)/i', $query, $matches)) {
                                    $valueColumn = trim($matches[1]);
                                    $textColumn = trim($matches[2]);
                                    $tableName = trim($matches[3]);
                                    
                                    // Construir consulta para buscar el valor descriptivo
                                    $lookupQuery = "SELECT $textColumn FROM $tableName WHERE $valueColumn = ?";
                                    $stmt = $lookupPdo->prepare($lookupQuery);
                                    $stmt->execute([$filterValue]);
                                    
                                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $displayValue = reset($row); // Primer valor del array asociativo
                                    }
                                }
                            }
                            
                            $lookupPdo = null;


                        } catch (Exception $e) {
                            // Si hay error, mantener el valor original
                            error_log("Error al buscar valor descriptivo: " . $e->getMessage());
                        }
                    }
                    
                    // Añadir filtro con valor descriptivo
                    $appliedFilters[] = [
                        'label' => $label,
                        'value' => $displayValue
                    ];
                }
            }
        }
        $_SESSION['applied_filters'] = $appliedFilters;
        
        // Redirect directly to reporte.php to mostrar resultados
        header('Location: reporte.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtros de Reporte: <?php echo htmlspecialchars(isset($report['nombrereporte']) ? $report['nombrereporte'] : 'Sin nombre'); ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Añadir jQuery y jQuery UI para el selector de fechas -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
    <!-- Select2 para los filtros con búsqueda -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .filters-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f8f8f8;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .filter-item {
            margin-bottom: 15px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .filter-input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        .filter-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            height: 40px;
        }
        
        /* Estilos para el nuevo autocompletado */
        .autocomplete-container {
            position: relative;
            width: 100%;
        }
        
        .filter-autocomplete {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            height: 40px;
        }
        
        /* Estilos para el menú de autocompletado */
        .ui-autocomplete {
            max-height: 220px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 0 0 4px 4px;
            z-index: 1000 !important;
            width: auto !important;
            min-width: 200px;
        }
        
        .ui-menu-item {
            cursor: pointer;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        
        .ui-menu-item div {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            text-align: left;
        }
        
        .ui-menu-item:first-child div {
            background-color: #5c7cfa;
            color: white;
        }
        
        .ui-menu-item div:hover,
        .ui-menu-item div.ui-state-active {
            background-color: #5c7cfa !important;
            color: white !important;
        }
        
        .ui-menu-item strong {
            font-weight: bold;
            color: inherit;
        }
        
        /* Quitar el ícono de dropdown y agregar un campo de nivel */
        .filter-autocomplete {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="%23888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px !important;
        }
        
        .filter-date {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .submit-btn:hover {
            background-color: #45a049;
        }
        
        .error-message {
            color: #f44336;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        /* Estilos mejorados para select2 */
        .select2-container {
            width: 100% !important;
        }
        
        .select-search-container {
            width: 100%;
        }
        
        /* Personalización del select2 para que se parezca a la imagen de referencia */
        .select2-container--default .select2-selection--single {
            height: 40px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            padding-left: 0;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #5c7cfa;
        }
        
        .select2-results__option {
            padding: 10px;
            font-size: 14px;
            line-height: 1.2;
        }
        
        .select2-search--dropdown .select2-search__field {
            padding: 8px;
            font-size: 14px;
        }
        
        .select2-container--open .select2-dropdown--below {
            border-color: #ccc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<?php 
// Ocultar completamente el div de carga para el ambiente productivo
// No incluimos el div para evitar cargar imágenes inexistentes
// El script al final de la página también buscará este div y lo ocultará si existiera
?>
    <div class="filters-container">
        <h1>Filtros de Reporte: <?php echo htmlspecialchars(isset($report['nombrereporte']) ? $report['nombrereporte'] : 'Sin nombre'); ?></h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php elseif (empty($filters)): ?>
            <div class="error-message">
                <p>No se encontraron filtros para este reporte.</p>
            </div>
        <?php else: ?>
            <form id="filters-form" method="post" action="">
                <?php foreach ($filters as $filter): ?>
                    <?php if ($filter['type'] !== 'session'): // No mostrar filtros de tipo 'session' ?>
                        <div class="filter-item">
                            <label class="filter-label" for="<?php echo htmlspecialchars($filter['id']); ?>">
                                <?php echo htmlspecialchars($filter['label']); ?>
                            </label>
                            
                            <?php if ($filter['type'] === 'text'): ?>
                                <input type="text" 
                                       id="<?php echo htmlspecialchars($filter['id']); ?>" 
                                       name="<?php echo htmlspecialchars($filter['id']); ?>" 
                                       class="filter-input" 
                                       placeholder="<?php echo htmlspecialchars($filter['placeholder']); ?>">
                                       
                            <?php elseif ($filter['type'] === 'date'): ?>
                                <?php $today = date('Y-m-d'); ?>
                                <input type="text" 
                                       id="<?php echo htmlspecialchars($filter['id']); ?>" 
                                       name="<?php echo htmlspecialchars($filter['id']); ?>" 
                                       class="filter-date" 
                                       value="<?php echo $today; ?>"
                                       placeholder="<?php echo htmlspecialchars($filter['placeholder']); ?>">
                                       
                            <?php elseif ($filter['type'] === 'combo'): ?>
                                <!-- Selector con búsqueda simple o multiselección -->
                                <div class="select-search-container">
                                    <select id="<?php echo htmlspecialchars($filter['id']); ?>" 
                                            name="<?php echo htmlspecialchars($filter['id']); ?><?php echo isset($filter['multiselection']) && $filter['multiselection'] ? '[]' : ''; ?>" 
                                            class="filter-select-search"<?php echo isset($filter['multiselection']) && $filter['multiselection'] ? ' multiple' : ''; ?>>
                                        <?php if (!isset($filter['multiselection']) || !$filter['multiselection']): ?>
                                            <option value="">-- Todos --</option>
                                        <?php endif; ?>
                                        <?php foreach ($filter['options'] as $option): ?>
                                            <?php if ($option['value'] !== '' || (!isset($filter['multiselection']) || !$filter['multiselection'])): ?>
                                                <option value="<?php echo htmlspecialchars($option['value']); ?>">
                                                    <?php echo htmlspecialchars($option['text']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <script>
                                $(document).ready(function() {
                                    // Inicializa Select2 para este campo específico
                                    $("#<?php echo htmlspecialchars($filter['id']); ?>").select2({
                                        placeholder: "<?php echo isset($filter['multiselection']) && $filter['multiselection'] ? '-- Seleccione múltiples opciones --' : '-- Seleccione o escriba --'; ?>",
                                        allowClear: true,
                                        width: '100%'<?php echo isset($filter['multiselection']) && $filter['multiselection'] ? ',
                                        multiple: true' : ''; ?>
                                    });
                                });
                                </script>
                            <?php endif; ?>
                        </div>
                    <?php else: // Para filtros de tipo 'session', agregamos campo oculto con valor fijo ?>
                        <input type="hidden" 
                               id="<?php echo htmlspecialchars($filter['id']); ?>" 
                               name="<?php echo htmlspecialchars($filter['id']); ?>" 
                               value="<?php echo htmlspecialchars($filter['value']); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="buttons-container" style="display:flex; gap:10px;">
                    <?php if ($report['estatus'] == 'D'): ?>
                        <button type="submit" name="action" value="preview" class="submit-btn" style="background-color:#2196F3;">Mostrar SQL</button>
                    <?php endif; ?>
                    <button type="submit" class="submit-btn">Generar Reporte</button>
                </div>
                
                <?php if ($showPreview): ?>
                <div class="sql-preview" style="margin-top:20px; padding:15px; background:#f0f0f0; border-radius:4px; border-left:4px solid #4CAF50;">
                    <?php if (isset($_SESSION['sql_final_ejecutado']) && !empty($_SESSION['sql_final_ejecutado'])): ?>
                    <h3>SQL Ejecutado en la Última Consulta:</h3>
                    <div style="background:#333; color:#fff; padding:15px; border-radius:4px; overflow-x:auto;">
                        <code><?php echo htmlspecialchars($_SESSION['sql_final_ejecutado']); ?></code>
                    </div>
                    <?php else: ?>
                    <h3>SQL en Construcción</h3>
                    <p style="color:#666;">Haga clic en "Generar Reporte" para ver el SQL exacto utilizado.</p>
                    <?php endif; ?>
                    
                    <?php if (isset($debug_info) && !empty($debug_info)): ?>
                    <div style="margin-top:20px; padding:15px; background:#f1f8e9; border-radius:4px; border-left:4px solid #8bc34a;">
                        <h3>Información de Depuración de Patrones [@Campo;val;des;SQL]:</h3>
                        <ul style="margin-left: 20px; font-family: monospace; line-height: 1.5;">
                            <?php foreach ($debug_info as $line): ?>
                                <li><?php echo htmlspecialchars($line); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($debug_info_html)): ?>
                        <?php echo $debug_info_html; ?>
                    <?php endif; ?>
                    
                    <?php if (isset($showDebug) && $showDebug): ?>
                    <div style="margin-top:20px; padding:15px; background:#f1f8e9; border-radius:4px; border-left:4px solid #8bc34a;">
                        <h3>Información de Depuración:</h3>
                        <?php echo $debugInfo; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
            
            <script>
                $(document).ready(function() {
                    // Initialize datepicker for date fields
                    $('.filter-date').datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "1900:2100"
                    });
                    
                    // Add select2 for better combo experience if select2 is available
                    if ($.fn.select2) {
                        $('.filter-select').select2({
                            placeholder: "Seleccione una opción",
                            allowClear: true
                        });
                    }
                    
                    // Validate date format
                    $('.filter-date').blur(function() {
                        const dateValue = $(this).val();
                        if (dateValue && !isValidDate(dateValue)) {
                            alert('Por favor, ingrese una fecha válida en formato YYYY-MM-DD');
                            $(this).val('');
                        }
                    });
                    
                    // Date validation function
                    function isValidDate(dateString) {
                        // First check for the pattern
                        if(!/^\d{4}-\d{1,2}-\d{1,2}$/.test(dateString)) {
                            return false;
                        }
                        
                        // Parse the date parts to integers
                        const parts = dateString.split("-");
                        const year = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10);
                        const day = parseInt(parts[2], 10);
                        
                        // Check the ranges of month and day
                        if(year < 1000 || year > 3000 || month == 0 || month > 12) {
                            return false;
                        }
                        
                        const monthLength = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                        
                        // Adjust for leap years
                        if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0)) {
                            monthLength[1] = 29;
                        }
                        
                        // Check the range of the day
                        return day > 0 && day <= monthLength[month - 1];
                    }
                });
            </script>
        <?php endif; ?>
    </div>
    
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