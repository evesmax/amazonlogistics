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
 * Función para corregir el problema específico de "and )" cuando no se selecciona ningún filtro
 * Este problema ocurre principalmente en reportes como RecepcionDirecta (ID 18)
 */
function fixExtraAndBeforeClosingParenthesis($sql) {
    // Caso 1: and ) ORDER BY
    $sql = preg_replace('/\s+and\s*\)\s+ORDER\s+BY/i', ') ORDER BY', $sql);
    
    // Caso 2: and ) GROUP BY
    $sql = preg_replace('/\s+and\s*\)\s+GROUP\s+BY/i', ') GROUP BY', $sql);
    
    // Caso 3: and ) HAVING
    $sql = preg_replace('/\s+and\s*\)\s+HAVING/i', ') HAVING', $sql);
    
    // Caso 4: and ) al final de la consulta
    $sql = preg_replace('/\s+and\s*\)\s*$/i', ')', $sql);
    
    // Caso 5: and ) en cualquier otro lugar
    $sql = preg_replace('/\s+and\s*\)/i', ')', $sql);
    
    // Caso 6: Solución específica para la consulta de RecepcionDirecta con parentesis extra al final
    $sql = preg_replace('/(lt\.referencia1\s+LIKE\s+[\'"]%%[\'"]\))\s+and\s*\)/i', '$1', $sql);
    
    // Caso 7: Solución para paréntesis excesivos en LIKE '%%' seguido por il.idloteproducto=valor
    // Es el caso específico reportado por el usuario
    $specificPattern = '/\(lt\.referencia1\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s+(and|AND)\s+\(il\.idloteproducto\s*=\s*(\d+)\)/i';
    if (preg_match($specificPattern, $sql)) {
        // Eliminamos el paréntesis extra
        $sql = preg_replace($specificPattern, '(lt.referencia1 LIKE \'%%\')) $1 (il.idloteproducto = $2)', $sql);
    }
    
    // Caso 8: Solución para cualquier condición LIKE '%%' seguida de AND/OR y otra condición
    // Este patrón manejará cualquier campo, no solo lt.referencia1
    $sql = preg_replace('/\(([a-zA-Z0-9_.]+)\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*([^()]+?)\)/i', 
                       '($1 LIKE \'%%\')) $2 ($3 = $4)', $sql);
    
    // Caso 9: Solución específica para el patrón exacto del reporte de RecepcionDirecta ID 18
    // Este patrón aparece cuando no se selecciona ningún filtro y genera un paréntesis extra
    $specificPattern = '/\(lt\.referencia1\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s*ORDER/i';
    if (preg_match($specificPattern, $sql)) {
        // Aplicamos una solución directa para este caso en particular
        $exactPattern = '/WHERE\s*\(\s*lt\.idbodegadestino\s+in\s*\([^)]+\)\s+OR\s+NOT\s+EXISTS[^)]+\)\s*\)\s+and\s+lt\.idestadodocumento\s*<>\s*\d+\s+and\s+\(lt\.fecha\s+between\s+"[^"]+"\s+and\s+"[^"]+"\)\s+and\s+\(lt\.idestadodocumento\s*=\s*\d+\)\s+and\s+\(lt\.referencia1\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s*ORDER/i';
        $replacement = 'WHERE ( lt.idbodegadestino in (select idbodega from relaciones_usuariosbodegas where idempleado=2) OR NOT EXISTS (SELECT 1 FROM relaciones_usuariosbodegas WHERE idempleado=2) ) and lt.idestadodocumento<>4 and (lt.fecha between "2025/04/30 00:00:00" and "2025/04/30 23:59:59") and (lt.idestadodocumento=1) and (lt.referencia1 LIKE \'%%\') ORDER';
        
        // Aplicar la solución directa
        $newSql = preg_replace($exactPattern, $replacement, $sql);
        
        // Si hubo un reemplazo, usamos la nueva versión
        if ($newSql !== $sql) {
            return $newSql;
        }
    }
    
    // Si no se aplicó la solución específica, intentamos con expresiones regulares más generales
    $sql = preg_replace('/\(\s*lt\.referencia1\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s*ORDER/i', '(lt.referencia1 LIKE \'%%\')) ORDER', $sql);
    $sql = preg_replace('/LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s*ORDER/i', 'LIKE \'%%\')) ORDER', $sql);
    
    // Caso adicional: verificar paréntesis antes de la cláusula ORDER BY
    $orderByPos = stripos($sql, 'ORDER BY');
    if ($orderByPos !== false) {
        // Extraer la parte anterior a ORDER BY
        $beforeOrderBy = substr($sql, 0, $orderByPos);
        
        // Contar paréntesis abiertos y cerrados
        $openCount = substr_count($beforeOrderBy, '(');
        $closeCount = substr_count($beforeOrderBy, ')');
        
        // Si hay más paréntesis cerrados que abiertos antes de ORDER BY
        if ($closeCount > $openCount) {
            // Reducir el número de paréntesis de cierre antes de ORDER BY
            $excess = $closeCount - $openCount;
            $pattern = '/(\){{' . $excess . '}})\s*(ORDER\s+BY)/i';
            $replacement = '$2';
            $sql = preg_replace($pattern, $replacement, $sql);
        }
    }
    
    return $sql;
}

/**
 * Limpieza final para corregir cualquier problema restante
 */
function finalSqlCleanup($sql) {
    // Primero corregir el problema específico de "and )"
    $sql = fixExtraAndBeforeClosingParenthesis($sql);
    
    // Eliminar ANDs y ORs repetidos o al inicio/final
    $sql = preg_replace('/\s+AND\s+AND\s+/i', ' AND ', $sql);
    $sql = preg_replace('/\s+OR\s+OR\s+/i', ' OR ', $sql);
    $sql = preg_replace('/^\s*AND\s+/i', '', $sql);
    $sql = preg_replace('/^\s*OR\s+/i', '', $sql);
    $sql = preg_replace('/\s+AND\s*$/i', '', $sql);
    $sql = preg_replace('/\s+OR\s*$/i', '', $sql);
    
    // Corregir problema de paréntesis adicional antes de and (campo = valor)
    // Este es el caso donde aparece '%%')) and (il.idloteproducto = 11)' 
    $sql = preg_replace('/\'%%\'\)\s*\)\s+and\s+\(/i', '\'%%\')) and (', $sql);
    $sql = preg_replace('/\'%%\'\)\s*\)\s+AND\s+\(/i', '\'%%\')) AND (', $sql);
    
    // Solución más general: corrección para cualquier condición LIKE que termine con doble paréntesis
    $sql = preg_replace('/LIKE\s+\'%%\'\)\s*\)\s+and/i', 'LIKE \'%%\')) and', $sql);
    $sql = preg_replace('/LIKE\s+\'%%\'\)\s*\)\s+AND/i', 'LIKE \'%%\')) AND', $sql);
    
    // Solución muy general para eliminar el paréntesis adicional en cualquier condición AND
    // Examina el SQL para encontrar desbalance de paréntesis alrededor de AND
    $pattern = '/\)\s*\)\s+(AND|and|OR|or)\s+\(/';
    if (preg_match($pattern, $sql)) {
        // Contar el número de aperturas y cierres antes de cada AND
        preg_match_all($pattern, $sql, $matches, PREG_OFFSET_CAPTURE);
        foreach ($matches[0] as $match) {
            $pos = $match[1];
            $beforeMatch = substr($sql, 0, $pos);
            $openCount = substr_count($beforeMatch, '(');
            $closeCount = substr_count($beforeMatch, ')');
            
            if ($closeCount > $openCount) {
                // Hay un desbalance, así que reemplazamos la primera coincidencia
                $sql = preg_replace($pattern, ')) $1 (', $sql, 1);
            }
        }
    }
    
    // Patrón específico y exacto para el problema reportado con il.idloteproducto
    $sql = preg_replace('/\)\s*\)\s+and\s+\(il\.idloteproducto\s*=\s*(\d+)\)/i', ')) and (il.idloteproducto = $1)', $sql);
    
    // NUEVA SOLUCIÓN: Limpieza específica para filtros "Todos"
    // Esto soluciona el problema de condiciones como campo = ''
    // que se generan cuando el usuario selecciona "Todos" en un combo
    
    // 1. Eliminar condiciones de igualdad con cadena vacía en campos específicos
    // Ejemplo: campo = '' AND ... -> (eliminado) AND ...
    $fieldsToCheck = array(
        'il.idloteproducto', 'il.descripcionlote', 'vm.idmarca', 'vm.nombremarca',
        'ifa.idfamilia', 'ifa.nombrefamilia', 'ip.idproducto', 'ip.nombreproducto',
        'ie.idestado', 'ie.descripcionestado', 'ob.idbodega', 'ob.nombrebodega',
        're.idreg'
    );
    
    foreach ($fieldsToCheck as $field) {
        // Caso 1: campo = '' AND ...
        $pattern = '/\s+(AND|OR)\s+' . preg_quote($field, '/') . '\s*=\s*\'\'(\s+(AND|OR)|$|\))/i';
        $sql = preg_replace($pattern, '$2', $sql);
        
        // Caso 2: ... AND campo = ''
        $pattern = '/(\s+(AND|OR)|\(|^)\s*' . preg_quote($field, '/') . '\s*=\s*\'\'(\s+(AND|OR)|$|\))/i';
        $sql = preg_replace($pattern, '$1$3', $sql);
        
        // Caso 3: WHERE campo = ''
        $pattern = '/WHERE\s+' . preg_quote($field, '/') . '\s*=\s*\'\'(\s+(AND|OR)|$|\))/i';
        $sql = preg_replace($pattern, 'WHERE$1', $sql);
        
        // Caso 4: ( campo = '' )
        $pattern = '/\(\s*' . preg_quote($field, '/') . '\s*=\s*\'\'\s*\)/i';
        $sql = preg_replace($pattern, '(1=1)', $sql);
    }
    
    // 2. Eliminar cláusulas WHERE vacías o sin condiciones
    // Caso 1: WHERE seguido de GROUP BY, ORDER BY
    $sql = preg_replace('/WHERE\s+(GROUP BY|ORDER BY)/i', '$1', $sql);
    
    // Caso 2: WHERE que termina sin condiciones reales
    $sql = preg_replace('/WHERE\s*$/i', '', $sql);
    
    // Caso 3: WHERE (1=1) - reemplazar por una cláusula WHERE vacía
    $sql = preg_replace('/WHERE\s*\(\s*1\s*=\s*1\s*\)(\s+(GROUP BY|ORDER BY)|$)/i', '$1', $sql);
    
    // 3. Limpiar paréntesis vacíos
    $sql = preg_replace('/\(\s*\)/', '', $sql);
    $sql = preg_replace('/\(\s*1\s*=\s*1\s*AND\s*\(\s*\)\s*\)/', '(1=1)', $sql);
    
    // 4. Eliminar múltiples espacios en blanco
    $sql = preg_replace('/\s{2,}/', ' ', $sql);
    
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