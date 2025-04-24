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
$filters = [];

try {
    // Create a PDO connection
    $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
    
    // Set error mode to throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare and execute the query to get report information including extension fields
    $stmt = $pdo->prepare("SELECT sql_select, sql_from, sql_where, sql_groupby, sql_having, sql_orderby, subtotales_agrupaciones,
                                 url_include, url_include_despues, sppre, sppos, nombrereporte, estatus
                           FROM repolog_reportes 
                           WHERE idreporte = ?");
    $stmt->execute([$reportId]);
    
    // Fetch report data
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
 * Parse WHERE clause to identify filter conditions
 * 
 * @param string $whereClause SQL WHERE clause
 * @return array Array of filter objects
 */
function parseWhereClause($whereClause) {
    $filters = [];
    
    // Regular expressions to match different filter patterns
    $regularPattern = '/\[([^\]]+)\]/'; // Matches [FilterName]
    $datePattern = '/\[#([^\]]+)\]/';  // Matches [#FilterName]
    $comboPattern = '/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/'; // Matches [@Label;val;des;SQL]
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
    
    // Find all combo filters [@Label;val;des;SQL]
    preg_match_all($comboPattern, $whereClause, $comboMatches, PREG_SET_ORDER);
    foreach ($comboMatches as $match) {
        $label = $match[1];
        $valueField = $match[2];
        $displayField = $match[3];
        $sql = $match[4];
        $fullPattern = $match[0]; // Full match including [@...] pattern
        
        $filters[] = [
            'type' => 'combo',
            'label' => $label,
            'id' => 'filter_' . sanitizeId($label),
            'valueField' => $valueField,
            'displayField' => $displayField,
            'sql' => $sql,
            'options' => getComboOptions($sql, $valueField, $displayField),
            'original_name' => $label, // Original name as it appears in SQL
            'sql_pattern' => $fullPattern // SQL pattern to be replaced
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
    $options = [];
    
    try {
        // Create a PDO connection
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        
        // Set error mode to throw exceptions
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Execute the query
        $stmt = $pdo->query($sql);
        
        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format results as options array
        foreach ($results as $row) {
            $options[] = [
                'value' => $row[$valueField],
                'text' => $row[$displayField]
            ];
        }
        
        // Close connection
        $pdo = null;
        
    } catch (Exception $e) {
        // Return empty array on error
        $options = [];
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
    // Cualquier condición de fecha que el sistema haya generado automáticamente con la fecha actual
    $todayDate = date('Y/m/d');
    $literalDatePatterns = [
        // Fechas literales con comillas dobles
        '/([^\s]+)\.fecha\s*=\s*"' . preg_quote($todayDate, '/') . '"/',
        // Fechas literales con comillas simples
        '/([^\s]+)\.fecha\s*=\s*\'' . preg_quote($todayDate, '/') . '\'/',
    ];
    
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

function buildSqlQuery($report, $filterValues) {
    global $filters; // Access the global filters array
    $sqlQuery = "SELECT " . $report['sql_select'] . " FROM " . $report['sql_from'];
    
    // Process WHERE clause with filter values
    if (!empty($report['sql_where'])) {
        $whereClause = $report['sql_where'];
        
        // First, look for direct combo patterns [@Field;val;des;sql] and replace them with selected values
        // This is a direct approach that should work more reliably
        $comboPatternRegex = '/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/'; // [@Campo;val;des;SQL]
        preg_match_all($comboPatternRegex, $whereClause, $comboMatches, PREG_SET_ORDER);
        
        foreach ($comboMatches as $match) {
            $fullPattern = $match[0];      // The entire pattern [@Field;val;des;sql]
            $label = $match[1];            // Field name/label
            $filterKey = 'filter_' . sanitizeId($label); // Expected POST field name
            
            // Check if a value was selected for this combo
            if (isset($filterValues[$filterKey]) && !empty($filterValues[$filterKey])) {
                // Use the user-selected value (corresponds to 'val' field in the combo)
                $comboValue = $filterValues[$filterKey];
                
                // For debugging - add a trace to a variable
                $trace = "Reemplazando combo: " . $fullPattern . " con valor: " . $comboValue;
                
                // Replace the entire combo pattern with the value
                $whereClause = str_replace($fullPattern, $comboValue, $whereClause);
                
                // Caso específico: Si es Zafra y está dentro de un paréntesis, vamos a ser más conservadores
                // y solo modificar si realmente hay un desbalance de paréntesis
                if ($label === 'Zafra' && strpos($whereClause, 'il.descripcionlote = ' . $comboValue) !== false) {
                    // Solo si hay un paréntesis abierto antes de il.descripcionlote pero no hay uno de cierre
                    $pattern = '/\(il\.descripcionlote\s*=\s*' . $comboValue . '/i';
                    if (preg_match($pattern, $whereClause)) {
                        // Verificamos si hay un desbalance concreto en esta parte
                        $parts = explode('il.descripcionlote', $whereClause);
                        if (count($parts) > 1) {
                            $beforePart = $parts[0];
                            $openBefore = substr_count($beforePart, '(');
                            $closeBefore = substr_count($beforePart, ')');
                            
                            // Solo si hay un claro desbalance
                            if ($openBefore > $closeBefore) {
                                // En este caso, añadimos un paréntesis de cierre después del valor pero antes del AND
                                // pero asegurándonos de no duplicar paréntesis existentes
                                if (strpos($whereClause, $comboValue . ')') === false) {
                                    $whereClause = str_replace(
                                        'il.descripcionlote = ' . $comboValue . ' and', 
                                        'il.descripcionlote = ' . $comboValue . ') and', 
                                        $whereClause
                                    );
                                }
                            }
                        }
                    }
                }
            } else {
                // If no value selected, remove the condition or replace with empty
                if (strpos($whereClause, " = " . $fullPattern) !== false) {
                    // If it's an equality comparison (=), handle differently
                    $conditionPattern = '/([^\s]+)\s*=\s*' . preg_quote($fullPattern, '/') . '/';
                    if (preg_match($conditionPattern, $whereClause, $matches)) {
                        $fieldName = $matches[1]; // The field being compared (e.g., "il.descripcionlote")
                        $conditionStr = $matches[0]; // The full condition (e.g., "il.descripcionlote = [@Zafra;...")
                        
                        // Caso especial para "il.descripcionlote = [@Zafra...]"
                        if ($fieldName === 'il.descripcionlote') {
                            // Buscar patrones como (il.descripcionlote = [@Zafra...] and
                            $fullPattern = '/\(il\.descripcionlote\s*=\s*' . preg_quote($fullPattern, '/') . '\s+and/i';
                            if (preg_match($fullPattern, $whereClause, $fullMatches)) {
                                $fullMatch = $fullMatches[0];
                                // Eliminar el paréntesis inicial y el "and" final
                                $whereClause = str_replace($fullMatch, '(', $whereClause);
                            } else {
                                // Buscar patrones como AND (il.descripcionlote = [@Zafra...])
                                $parenthesisPattern = '/\s+and\s+\(\s*il\.descripcionlote\s*=\s*' . preg_quote($fullPattern, '/') . '\s*\)/i';
                                if (preg_match($parenthesisPattern, $whereClause, $parenthesisMatches)) {
                                    $parenthesisMatch = $parenthesisMatches[0];
                                    // Eliminar completamente la condición con paréntesis
                                    $whereClause = str_replace($parenthesisMatch, '', $whereClause);
                                }
                            }
                        }
                        
                        // Para otros casos, eliminar la condición completa con AND/OR
                        $beforePos = strpos($whereClause, $conditionStr);
                        if ($beforePos !== false) {
                            $beforeChar = $beforePos > 0 ? $whereClause[$beforePos - 1] : '';
                            $afterPos = $beforePos + strlen($conditionStr);
                            $afterChar = $afterPos < strlen($whereClause) ? $whereClause[$afterPos] : '';
                            
                            $replacePart = $conditionStr;
                            
                            // Handle AND/OR before/after the condition
                            if ($beforeChar === ' ' && substr($whereClause, max(0, $beforePos - 5), 5) === ' AND ') {
                                $replacePart = ' AND ' . $replacePart;
                            } elseif ($afterChar === ' ' && substr($whereClause, $afterPos, 5) === ' AND ') {
                                $replacePart = $replacePart . ' AND ';
                            } elseif ($beforeChar === ' ' && substr($whereClause, max(0, $beforePos - 4), 4) === ' OR ') {
                                $replacePart = ' OR ' . $replacePart;
                            } elseif ($afterChar === ' ' && substr($whereClause, $afterPos, 4) === ' OR ') {
                                $replacePart = $replacePart . ' OR ';
                            }
                            
                            // Remove the condition
                            $whereClause = str_replace($replacePart, '', $whereClause);
                        }
                    }
                } else {
                    // For other cases, just replace the pattern with empty
                    $whereClause = str_replace($fullPattern, '', $whereClause);
                }
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
        
        // Process regular user inputs
        foreach ($filters as $filter) {
            if ($filter['type'] !== 'session') {
                $filterKey = $filter['id']; // e.g., filter_ingenio
                $filterPattern = $filter['sql_pattern']; // e.g., [Ingenio] or [#Fecha]
                
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
                        
                        // Determinar qué tipo de patrón LIKE es
                        if (preg_match('/\[%(.*?)%\]/', $likeParam, $innerMatches)) {
                            // Es [%valor%] - contiene % al inicio y al final
                            $replacementPattern = " LIKE '%{$filterValue}%'";
                            $whereClause = str_replace($likePatternFull, $replacementPattern, $whereClause);
                        } else if (preg_match('/\[%(.*?)\]/', $likeParam, $innerMatches)) {
                            // Es [%valor] - contiene % solo al inicio
                            $replacementPattern = " LIKE '%{$filterValue}'";
                            $whereClause = str_replace($likePatternFull, $replacementPattern, $whereClause);
                        } else if (preg_match('/\[(.*?)%\]/', $likeParam, $innerMatches)) {
                            // Es [valor%] - contiene % solo al final
                            $replacementPattern = " LIKE '{$filterValue}%'";
                            $whereClause = str_replace($likePatternFull, $replacementPattern, $whereClause);
                        } else {
                            // Reemplazar el patrón con el valor del usuario de forma estándar
                            $whereClause = str_replace($filterPattern, $filterValue, $whereClause);
                        }
                    } else {
                        // Reemplazar el patrón con el valor del usuario de forma estándar
                        $whereClause = str_replace($filterPattern, $filterValue, $whereClause);
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
        // Just show the SQL preview
        $previewSQL = $sqlQuery;
        $showPreview = true;
        
        // For debugging purposes, show exactly what filter values were received
        $debugInfo = '';
        $debugInfo .= "<h4>Valores de filtros recibidos:</h4>";
        $debugInfo .= "<pre>";
        foreach ($filterValues as $key => $value) {
            $debugInfo .= htmlspecialchars($key . ": " . $value) . "\n";
        }
        $debugInfo .= "</pre>";
        
        // Show only for test or development
        $showDebug = true;
    } else {
        // Store query and additional report parameters in session
        $_SESSION['sql_consulta'] = $sqlQuery;
        $_SESSION['repolog_report_id'] = $reportId;
        
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
        
        // Guardar los filtros aplicados para mostrarlos como subtítulo del reporte
        $appliedFilters = [];
        foreach ($filters as $filter) {
            if ($filter['type'] != 'session') {
                $filterKey = $filter['id'];
                $label = isset($filter['label']) ? $filter['label'] : (isset($filter['name']) ? $filter['name'] : '');
                
                // Solo guardar los filtros que tienen valor
                if (isset($filterValues[$filterKey]) && !empty($filterValues[$filterKey])) {
                    $appliedFilters[] = [
                        'label' => $label,
                        'value' => $filterValues[$filterKey]
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
        
        .select2-container {
            width: 100% !important;
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
                                <!-- Combo que usa 'value' para el campo 'val' (valor interno) y muestra el texto del campo 'des' -->
                                <!-- Al seleccionar una opción, el valor enviado será el del campo 'val', no el texto mostrado -->
                                <select id="<?php echo htmlspecialchars($filter['id']); ?>" 
                                        name="<?php echo htmlspecialchars($filter['id']); ?>" 
                                        class="filter-select">
                                    <option value="">-- Todos --</option>
                                    <?php foreach ($filter['options'] as $option): ?>
                                        <!-- value contiene el campo 'val' que es el valor interno para el SQL -->
                                        <option value="<?php echo htmlspecialchars($option['value']); ?>">
                                            <!-- El texto mostrado es el campo 'des' -->
                                            <?php echo htmlspecialchars($option['text']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                        <button type="submit" name="action" value="preview" class="submit-btn" style="background-color:#2196F3;">Vista Previa SQL</button>
                    <?php endif; ?>
                    <button type="submit" class="submit-btn">Generar Reporte</button>
                </div>
                
                <?php if ($showPreview): ?>
                <div class="sql-preview" style="margin-top:20px; padding:15px; background:#f0f0f0; border-radius:4px; border-left:4px solid #2196F3;">
                    <h3>Vista Previa de la Consulta SQL:</h3>
                    <div style="background:#333; color:#fff; padding:15px; border-radius:4px; overflow-x:auto;">
                        <code><?php echo htmlspecialchars($previewSQL); ?></code>
                    </div>
                    
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