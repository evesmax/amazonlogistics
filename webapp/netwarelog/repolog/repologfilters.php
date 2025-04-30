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
    $filters = array();
    
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
        
        // First, look for direct combo patterns [@Field;val;des;sql] and replace them with selected values
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
                $debug_info[] = "Valor seleccionado: " . $filterValues[$filterKey];
            } else {
                $debug_info[] = "No hay valor seleccionado para este filtro";
            }
            
            // Detectar si este es un filter nulo (Todos) o tiene un valor seleccionado
            $filterEmpty = !isset($filterValues[$filterKey]) || trim($filterValues[$filterKey]) === '';
            
            // Si el usuario seleccionó un valor específico (no Todos)
            if (isset($filterValues[$filterKey]) && !empty($filterValues[$filterKey])) {
                // Usuario seleccionó un valor específico (por ejemplo, un ID numérico)
                $comboValue = $filterValues[$filterKey];
                
                // SOLUCIÓN PARA TIPOS COMBO: Asegurar que solo usemos el valor (val) y no el patrón completo
                
                // Aquí aplicamos directamente el valor seleccionado de la opción al SQL
                // El valor ya viene correcto desde el select HTML, que tiene value="opcion['value']"
                
                // For debugging - add a trace to a variable
                $debug_info[] = "Reemplazando combo: " . $fullPattern . " con valor: " . $comboValue;
                
                // Almacenar el patrón completo para búsquedas precisas (incluye los corchetes [@...])
                $patternWithBrackets = preg_quote($fullPattern, '/');
                
                // Buscar específicamente condiciones con il.idloteproducto y variantes de Zafra
                if ($label === 'Zafra') {
                    $debug_info[] = "Procesando caso especial para Zafra";
                    
                    // Buscar patrones comunes para Zafra
                    // Estas expresiones buscan la condición completa, incluidos paréntesis, AND y OR
                    
                    // Caso 1: (il.idloteproducto = [@Zafra;...])
                    if (preg_match('/\(\s*il\.idloteproducto\s*=\s*' . $patternWithBrackets . '\s*\)/i', $whereClause, $matches)) {
                        $fullCondition = $matches[0];
                        $replacement = '(il.idloteproducto = ' . $comboValue . ')';
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
                // Para otros filtros combo (no Zafra), aplicar reemplazo directo
                else {
                    $whereClause = str_replace($fullPattern, $comboValue, $whereClause);
                    $debug_info[] = "Reemplazo directo: " . $fullPattern . " -> " . $comboValue;
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
    
    // Verificación FINAL para patrones no procesados de tipo [@Campo;val;des;SQL]
    global $debug_info_html;
    
    // Buscar cualquier patrón de tipo [@*;*;*;*] que haya quedado sin procesar
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
                    $debug_info[] = "Aplicando corrección para $fieldName (valor: $comboValue) - Reemplazando patrón directo";
                    
                    // Reemplazo más específico primero
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
                    $sqlQuery = str_replace("'" . $fullPattern . "'", $comboValue, $sqlQuery);
                }
            }
        }
        
        // Limpieza general de SQL para prevenir errores de sintaxis
        $debug_info[] = "Aplicando limpieza general del SQL para prevenir errores de sintaxis";
        
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
        
        // Si aún quedan patrones no procesados, reemplazarlos con un valor comodín
        if (preg_match('/\[@([^;]+);([^;]+);([^;]+);([^\]]+)\]/i', $sqlQuery)) {
            $debug_info[] = "¡ADVERTENCIA! Todavía quedan patrones sin procesar. Aplicando limpieza final.";
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
        
        // Añadir información sobre el SQL final
        $debug_info_html .= '<h4>SQL final generado:</h4>';
        $debug_info_html .= '<pre>' . htmlspecialchars($sqlQuery) . '</pre>';
        
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
        
        // Añadir información adicional sobre el reporte ID 18 (RecepcionDirecta)
        if ($reportId == 18) {
            $debugInfo .= "<h4>Información de Depuración Especial para RecepcionDirecta (ID 18):</h4>";
            $debugInfo .= "<p>Este reporte requiere atención especial cuando no se seleccionan filtros debido a un patrón especial.</p>";
            $debugInfo .= "<pre>Patrón detectado: " . (strpos($sqlQuery, '(lt.referencia1 LIKE \'%%\')) ORDER') !== false ? 'SÍ' : 'NO') . "</pre>";
            $debugInfo .= "<pre>SQL original:\n" . htmlspecialchars($sqlQuery) . "</pre>";
            
            // Fix directo para RecepcionDirecta
            if (strpos($sqlQuery, '(lt.referencia1 LIKE \'%%\')) ORDER') !== false ||
                strpos($sqlQuery, ')) ORDER BY lt.fecha') !== false) {
                
                // Aplicar solución específica para este caso
                require_once 'sqlcleaner.php';
                $sqlQuery = fixExtraAndBeforeClosingParenthesis($sqlQuery);
                $debugInfo .= "<p>Solución aplicada a RecepcionDirecta (ID 18)</p>";
                $debugInfo .= "<pre>SQL corregido:\n" . htmlspecialchars($sqlQuery) . "</pre>";
            }
        }
        
        // Show only for test or development
        $showDebug = true;
    } else {
        // Store query and additional report parameters in session
        $_SESSION['sql_consulta'] = $sqlQuery;
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
                                <!-- Selector con búsqueda simple -->
                                <div class="select-search-container">
                                    <select id="<?php echo htmlspecialchars($filter['id']); ?>" 
                                            name="<?php echo htmlspecialchars($filter['id']); ?>" 
                                            class="filter-select-search">
                                        <option value="">-- Todos --</option>
                                        <?php foreach ($filter['options'] as $option): ?>
                                            <option value="<?php echo htmlspecialchars($option['value']); ?>">
                                                <?php echo htmlspecialchars($option['text']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <script>
                                $(document).ready(function() {
                                    // Inicializa Select2 para este campo específico
                                    $("#<?php echo htmlspecialchars($filter['id']); ?>").select2({
                                        placeholder: "-- Seleccione o escriba --",
                                        allowClear: true,
                                        width: '100%'
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