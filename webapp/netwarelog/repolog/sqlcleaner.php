<?php
/**
 * SQL Cleaner - Herramientas especializadas para corregir problemas comunes en SQL
 */

/**
 * Función principal: Corrige todos los problemas conocidos en consultas SQL
 * 
 * @param string $sql Consulta SQL a limpiar
 * @return string Consulta SQL limpia
 */
function fixAllSqlIssues($sql) {
    // Guardamos el SQL original para depuración
    $originalSql = $sql;
    
    // 1. Corregir FORMAT escrito como F OR MAT
    $sql = fixFormatFunction($sql);
    
    // 2. Corregir OR DER BY a ORDER BY - SOLUCIÓN DIRECTA
    $sql = fixOrderByDirectly($sql);
    
    // 3. Corregir condiciones de fecha con formato incorrecto
    $sql = fixDateConditions($sql);
    
    // 4. Eliminar paréntesis innecesarios al final
    $sql = removeTrailingParenthesis($sql);
    
    // 5. Corregir expresiones LIKE mal formadas
    $sql = fixLikeClauses($sql);
    
    // 6. Corregir problemas específicos con paréntesis y AND
    $sql = fixParenthesisWithAnd($sql);
    
    // 7. Últimas verificaciones y limpieza general
    $sql = finalSqlCleanup($sql);
    
    return $sql;
}

/**
 * Corrección especializada para problemas con paréntesis y AND
 * Esta función soluciona problemas específicos como:
 * (il.idloteproducto = 11 and (ob.nombrebodega...
 * donde falta un paréntesis de cierre
 */
function fixParenthesisWithAnd($sql) {
    // Corrección directa para el caso específico más común
    $sql = preg_replace(
        '/\(il\.idloteproducto\s*=\s*11\s+and\s+\(/', 
        '(il.idloteproducto = 11) and (', 
        $sql
    );
    
    // Corrección más general - buscar patrones similares
    $sql = preg_replace(
        '/\(([a-zA-Z0-9_.]+)\s*=\s*([0-9]+)\s+and\s+\(/', 
        '($1 = $2) and (', 
        $sql
    );
    
    // Corrección para paréntesis desbalanceados con AND/OR
    $sql = preg_replace(
        '/\(([^()]*?)\s+(AND|OR)\s+\(/', 
        '($1) $2 (', 
        $sql
    );
    
    return $sql;
}

/**
 * Corrección muy directa para el problema de OR DER BY
 */
function fixOrderByDirectly($sql) {
    // Reemplazar todas las instancias de OR DER BY por ORDER BY
    $sql = preg_replace('/\s+OR\s+DER\s+BY\s+/i', ' ORDER BY ', $sql);
    
    // Solución agresiva: buscar patrones "cerca del final de la consulta"
    if (preg_match('/OR\s+DER\s+BY[^)]*\)/i', $sql)) {
        $sql = preg_replace('/OR\s+DER\s+BY([^)]*)\)/i', 'ORDER BY$1', $sql);
    }
    
    // Reemplazar independientemente de espacios
    $sql = preg_replace('/(\s+)O\s*R\s+D\s*E\s*R\s+B\s*Y(\s+)/i', '$1ORDER BY$2', $sql);
    
    // Buscar patrones específicos al final de la consulta
    $sql = preg_replace('/\s+OR\s+DER\s+BY\s+([a-zA-Z0-9_.]+)(\s*)$/i', ' ORDER BY $1$2', $sql);
    
    return $sql;
}

/**
 * Eliminar paréntesis al final de la consulta, especialmente después de ORDER BY
 */
function removeTrailingParenthesis($sql) {
    // Solución muy directa: Evaluar si el último carácter es un paréntesis
    if (substr(trim($sql), -1) === ')') {
        // Buscar "ORDER BY" en la consulta
        $orderByPos = stripos($sql, 'ORDER BY');
        
        if ($orderByPos !== false) {
            // La consulta contiene ORDER BY y termina con ), probablemente es un error.
            // Simplemente eliminamos el último paréntesis
            $sql = substr(trim($sql), 0, -1);
        } else {
            // No hay ORDER BY, así que revisamos el balance de paréntesis
            $openCount = substr_count($sql, '(');
            $closeCount = substr_count($sql, ')');
            
            if ($closeCount > $openCount) {
                // Hay más paréntesis de cierre, eliminar uno del final
                $sql = substr(trim($sql), 0, -1);
            }
        }
    }
    
    // Verificaciones adicionales para asegurar limpieza de situaciones complejas
    
    // Verificar específicamente el patrón re.idreg) al final
    $sql = preg_replace('/\b([a-zA-Z0-9_.]+)\)(\s*)$/i', '$1$2', $sql);
    
    // Verificar si hay una cláusula ORDER BY antes del paréntesis final
    $sql = preg_replace('/ORDER\s+BY\s+([a-zA-Z0-9_.,\s]+)\)(\s*)$/i', 'ORDER BY $1$2', $sql);
    
    return $sql;
}

/**
 * Función para corregir problemas de consultas con función FORMAT mal escrita
 */
function fixFormatFunction($sql) {
    // Corregir F OR MAT a FORMAT
    $sql = preg_replace('/F\s+OR\s+MAT\s*\(/i', 'FORMAT(', $sql);
    
    return $sql;
}

/**
 * Función para corregir problemas con fechas en WHERE
 */
function fixDateConditions($sql) {
    // Asegurar que todas las condiciones BETWEEN usen comillas dobles
    $sql = preg_replace('/between\s+\'([^\']*)\'/', 'between "$1"', $sql);
    
    // Corregir condiciones con [#Al] sin comillas - usar comillas dobles
    $sql = preg_replace('/\s+And\s+"\[#([^]]+)\]([^"]*)"/', ' AND "[#$1]$2"', $sql);
    
    // NUEVA SOLUCIÓN: Eliminar condiciones con fecha no reemplazada [#Fecha]
    // Esto sucede cuando el usuario no proporciona un valor para el filtro
    $patrones = [
        // Condiciones con re.fecha="[#Fecha]" o re.fecha='[#Fecha]'
        '/\s+(AND|OR)\s+\(re\.fecha\s*=\s*"(\[\#[^\]]+\])"\)/i',
        '/\s+(AND|OR)\s+\(re\.fecha\s*=\s*\'(\[\#[^\]]+\])\'\)/i',
        '/\s+(AND|OR)\s+re\.fecha\s*=\s*"(\[\#[^\]]+\])"/i',
        '/\s+(AND|OR)\s+re\.fecha\s*=\s*\'(\[\#[^\]]+\])\'/i',
        
        // Caso donde la condición de fecha está al inicio con AND/OR después
        '/\(re\.fecha\s*=\s*"(\[\#[^\]]+\])"\)\s+(AND|OR)/i',
        '/\(re\.fecha\s*=\s*\'(\[\#[^\]]+\])\'\)\s+(AND|OR)/i',
        '/re\.fecha\s*=\s*"(\[\#[^\]]+\])"\s+(AND|OR)/i',
        '/re\.fecha\s*=\s*\'(\[\#[^\]]+\])\'\s+(AND|OR)/i',
        
        // Condición que inicia con (
        '/\(\s*re\.fecha\s*=\s*"(\[\#[^\]]+\])"\)/i',
        '/\(\s*re\.fecha\s*=\s*\'(\[\#[^\]]+\])\'\)/i',
    ];
    
    foreach ($patrones as $patron) {
        // Para los patrones donde la condición fecha tiene AND/OR antes
        if (strpos($patron, '(AND|OR)') !== false && strpos($patron, '\s+(AND|OR)') === 0) {
            $sql = preg_replace($patron, '', $sql); // Eliminar toda la condición con AND/OR antes
        }
        // Para los patrones donde la condición fecha tiene AND/OR después
        else if (strpos($patron, '(AND|OR)') !== false && strpos($patron, '\s+(AND|OR)') > 0) {
            $sql = preg_replace($patron, '', $sql); // Eliminar toda la condición con AND/OR después
        }
        // Para patrones sin AND/OR
        else {
            $sql = preg_replace($patron, '', $sql);
        }
    }
    
    return $sql;
}

/**
 * Función avanzada para corregir expresiones LIKE sin comillas en consultas SQL
 */
function fixLikeClauses($sql) {
    // Primera etapa: corregir expresiones como "LIKE %"" sin comillas simples alrededor
    $sql = preg_replace_callback('/\b([A-Za-z0-9_\.]+)\s+LIKE\s+%"/', function($matches) {
        return $matches[1] . " LIKE '%%'";
    }, $sql);
    
    // Segunda etapa: corregir todos los casos específicos que sabemos que están causando problemas
    $specificFields = [
        'vm.nombremarca', 'ifa.nombrefamilia', 'ip.nombreproducto', 
        'ie.descripcionestado', 'il.descripcionlote', 'ob.nombrebodega',
        're.entradasacumuladas'
    ];
    
    foreach ($specificFields as $field) {
        // Reemplazar directamente cada variante problemática
        $sql = str_replace($field . " LIKE %\"", $field . " LIKE '%%'", $sql);
        $sql = str_replace($field . " like %\"", $field . " LIKE '%%'", $sql);
    }
    
    // Tercera etapa: usar una expresión regular más fuerte que capture situaciones con paréntesis y AND/OR
    $sql = preg_replace('/\b(LIKE|like)\s+%"/', " LIKE '%%'", $sql);
    
    // Cuarta etapa: reemplazar cualquier variante literal de LIKE %" que quede
    $sql = str_replace(" LIKE %\"", " LIKE '%%'", $sql);
    $sql = str_replace(" like %\"", " LIKE '%%'", $sql);
    
    // Quinta etapa: búsqueda profunda dentro de paréntesis
    $sql = preg_replace_callback('/\([^()]*\)/', function($matches) {
        $inner = $matches[0];
        $inner = str_replace(" LIKE %\"", " LIKE '%%'", $inner);
        $inner = str_replace(" like %\"", " LIKE '%%'", $inner);
        return $inner;
    }, $sql);
    
    return $sql;
}

/**
 * Limpieza final para corregir cualquier problema restante
 */
function finalSqlCleanup($sql) {
    // Eliminar ANDs y ORs repetidos o al inicio/final
    $sql = preg_replace('/\s+AND\s+AND\s+/i', ' AND ', $sql);
    $sql = preg_replace('/\s+OR\s+OR\s+/i', ' OR ', $sql);
    $sql = preg_replace('/^\s*AND\s+/i', '', $sql);
    $sql = preg_replace('/^\s*OR\s+/i', '', $sql);
    $sql = preg_replace('/\s+AND\s*$/i', '', $sql);
    $sql = preg_replace('/\s+OR\s*$/i', '', $sql);
    
    // Verificación final para paréntesis equilibrados
    $openCount = substr_count($sql, '(');
    $closeCount = substr_count($sql, ')');
    
    if ($openCount > $closeCount) {
        // Faltan paréntesis de cierre, agregar al final
        $diff = $openCount - $closeCount;
        $sql .= str_repeat(')', $diff);
    } elseif ($closeCount > $openCount) {
        // Hay demasiados paréntesis de cierre
        // Verificar si el problema está al final
        $endingParens = '';
        $tempSql = $sql;
        while (substr(trim($tempSql), -1) === ')') {
            $endingParens .= ')';
            $tempSql = substr(trim($tempSql), 0, -1);
        }
        
        if (strlen($endingParens) >= ($closeCount - $openCount)) {
            // Eliminar solo los paréntesis excedentes del final
            $sql = $tempSql . substr($endingParens, 0, strlen($endingParens) - ($closeCount - $openCount));
        }
    }
    
    return $sql;
}
?>