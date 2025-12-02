<?php
/**
 * SQL Cleaner - Herramientas especializadas para corregir problemas comunes en SQL
 */

/**
 * Extrae patrones [@...] con balance correcto de corchetes
 * IGNORA corchetes de variables [!...] porque no son patrones anidados
 * Evita capturar SQL fuera del patrón
 * 
 * @param string $sql SQL con patrones [@...]
 * @return array Array de patrones extraídos con estructura similar a preg_match_all
 */
function extractBalancedPatterns($sql) {
    $patterns = [];
    $len = strlen($sql);
    $i = 0;
    
    while ($i < $len) {
        // Buscar inicio de patrón [@
        if ($i < $len - 1 && $sql[$i] == '[' && $sql[$i+1] == '@') {
            $start = $i;
            $i += 2; // Saltar [@
            $bracketCount = 1; // Empezamos con 1 porque ya tenemos [
            $patternContent = '[@';
            
            // Recorrer hasta encontrar el ] que cierra el patrón
            while ($i < $len && $bracketCount > 0) {
                $char = $sql[$i];
                
                // CRÍTICO: Si encontramos [! es una variable de sesión, NO es patrón anidado
                // Saltar toda la variable [!...] sin contar sus corchetes
                if ($char == '[' && $i < $len - 1 && $sql[$i+1] == '!') {
                    // Encontrar el ] que cierra esta variable
                    $patternContent .= $char;
                    $i++;
                    while ($i < $len && $sql[$i] != ']') {
                        $patternContent .= $sql[$i];
                        $i++;
                    }
                    if ($i < $len) {
                        $patternContent .= $sql[$i]; // Agregar el ] de cierre
                        $i++;
                    }
                    continue;
                }
                
                $patternContent .= $char;
                
                if ($char == '[') {
                    $bracketCount++;
                } elseif ($char == ']') {
                    $bracketCount--;
                }
                
                $i++;
            }
            
            // Si los corchetes están balanceados, tenemos un patrón válido
            if ($bracketCount == 0) {
                // Parsear el patrón para extraer las partes
                // Formato: [@Label;val;des;SQL...] o [@Label;val;des;SQL...;@Multiselection]
                if (preg_match('/\[@([^;]+);([^;]+);([^;]+);(.+)\]/s', $patternContent, $match)) {
                    $patterns[] = [
                        0 => $match[0],  // Patrón completo
                        1 => $match[1],  // Label
                        2 => $match[2],  // val field
                        3 => $match[3],  // des field
                        4 => $match[4]   // SQL (puede incluir ;@Multiselection)
                    ];
                    error_log("Patrón extraído (longitud=" . strlen($match[0]) . "): " . substr($match[0], 0, 100) . "...");
                }
            }
        } else {
            $i++;
        }
    }
    
    return $patterns;
}

/**
 * Elimina una cláusula AND/OR completa que contiene un patrón sin resolver
 * Balancea paréntesis correctamente ignorando paréntesis dentro de patrones [@...]
 * 
 * @param string $sql SQL con patrón sin resolver
 * @param string $pattern Patrón a buscar y eliminar con su cláusula
 * @return string SQL sin la cláusula completa
 */
function removeCompleteClause($sql, $pattern) {
    // Buscar la posición del patrón - intentar con y sin comillas
    $patternPos = strpos($sql, $pattern);
    
    // Si no se encuentra el patrón directo, buscar con comillas alrededor
    $patternWithQuotes = null;
    if ($patternPos === false) {
        // Intentar con comillas dobles
        if (strpos($sql, '"' . $pattern . '"') !== false) {
            $patternPos = strpos($sql, '"' . $pattern . '"');
            $patternWithQuotes = '"' . $pattern . '"';
        }
        // Intentar con comillas simples
        elseif (strpos($sql, "'" . $pattern . "'") !== false) {
            $patternPos = strpos($sql, "'" . $pattern . "'");
            $patternWithQuotes = "'" . $pattern . "'";
        }
        else {
            return $sql; // No se encontró el patrón ni con comillas
        }
    }
    
    // Ajustar la posición si encontramos el patrón con comillas
    if ($patternWithQuotes !== null) {
        // Retroceder 1 carácter para incluir la comilla inicial
        $patternPos = $patternPos;
        error_log("removeCompleteClause: Patrón encontrado con comillas: $patternWithQuotes");
    }
    
    // Retroceder para encontrar el inicio de la cláusula (AND o OR)
    $clauseStart = $patternPos;
    $searchStart = max(0, $patternPos - 300); // Buscar hasta 300 caracteres atrás
    
    // Buscar el AND/OR que precede al patrón
    $substring = substr($sql, $searchStart, $patternPos - $searchStart);
    if (preg_match_all('/(and|or)\s*\(/i', $substring, $matches, PREG_OFFSET_CAPTURE)) {
        // Tomar la última coincidencia (la más cercana al patrón)
        $lastMatch = end($matches[0]);
        $clauseStart = $searchStart + $lastMatch[1];
    }
    
    // Encontrar el paréntesis de apertura después de AND/OR
    $openParenPos = $clauseStart;
    while ($openParenPos < $patternPos && $sql[$openParenPos] != '(') {
        $openParenPos++;
    }
    
    if ($openParenPos >= $patternPos) {
        // No se encontró el paréntesis de apertura, usar eliminación simple
        error_log("No se encontró paréntesis de apertura, usando eliminación simple");
        $result = str_replace($pattern, '', $sql);
        return $result;
    }
    
    // Desde el paréntesis de apertura, balancear hasta encontrar el cierre
    // IMPORTANTE: Ignorar paréntesis dentro de patrones [@...]
    $i = $openParenPos + 1;
    $len = strlen($sql);
    $parenCount = 1;
    $insidePattern = false;
    $insideVariable = false;
    
    while ($i < $len && $parenCount > 0) {
        // Detectar inicio de patrón [@
        if ($i < $len - 1 && $sql[$i] == '[' && $sql[$i+1] == '@') {
            $insidePattern = true;
        }
        // Detectar inicio de variable [!
        elseif ($i < $len - 1 && $sql[$i] == '[' && $sql[$i+1] == '!') {
            $insideVariable = true;
        }
        // Detectar fin de patrón o variable ]
        elseif ($sql[$i] == ']') {
            if ($insidePattern) {
                $insidePattern = false;
            } elseif ($insideVariable) {
                $insideVariable = false;
            }
        }
        // Solo contar paréntesis si NO estamos dentro de un patrón o variable
        elseif (!$insidePattern && !$insideVariable) {
            if ($sql[$i] == '(') {
                $parenCount++;
            } elseif ($sql[$i] == ')') {
                $parenCount--;
            }
        }
        
        $i++;
    }
    
    // Eliminar desde $clauseStart hasta $i (incluye el ) de cierre)
    $clauseEnd = $i;
    
    // CRITICAL FIX: Si hay un patrón [@...] que se extiende más allá del ), 
    // necesitamos extender clauseEnd hasta el ] final del patrón
    // El patrón puede incluir SQL como: ]) OR (...)) group by ... order by des]
    $remainingSQL = substr($sql, $clauseEnd);
    
    // Buscar el ] de cierre del patrón usando balanceo de corchetes
    // Esto asegura que capturamos solo el patrón actual, no patrones subsecuentes
    $bracketCount = 0;
    $extendTo = 0;
    $foundBracket = false;
    $len = strlen($remainingSQL);
    
    for ($k = 0; $k < $len && $k < 250; $k++) {
        if ($remainingSQL[$k] == '[') {
            $bracketCount++;
            $foundBracket = true;
        } elseif ($remainingSQL[$k] == ']') {
            if ($bracketCount > 0) {
                $bracketCount--;
                if ($bracketCount == 0) {
                    // Encontramos el cierre del patrón, extender hasta aquí + espacios
                    $extendTo = $k + 1;
                    // Consumir espacios en blanco después del ]
                    while ($extendTo < $len && ctype_space($remainingSQL[$extendTo])) {
                        $extendTo++;
                    }
                    break;
                }
            } else {
                // ] sin [ previo - es el cierre del patrón
                $extendTo = $k + 1;
                while ($extendTo < $len && ctype_space($remainingSQL[$extendTo])) {
                    $extendTo++;
                }
                break;
            }
        }
    }
    
    if ($extendTo > 0) {
        error_log("EXTENDED REMOVAL: Found pattern residue extending " . $extendTo . " chars: " . substr($remainingSQL, 0, min($extendTo, 80)));
        $clauseEnd += $extendTo;
    }
    
    $removedClause = substr($sql, $clauseStart, $clauseEnd - $clauseStart);
    error_log("Eliminando cláusula completa (longitud=" . strlen($removedClause) . "): " . substr($removedClause, 0, 150) . "...");
    
    // CRÍTICO: Preservar comilla de cierre si hay una justo ANTES del inicio de la cláusula
    // Ejemplo: ik.fecha <= "2025/10/10 23:59:59") and (campo IN [@...]) -> preservar la " antes de )
    $beforeClause = substr($sql, 0, $clauseStart);
    $afterClause = substr($sql, $clauseEnd);
    
    error_log("removeCompleteClause - BEFORE CLAUSE (last 60 chars): " . substr($beforeClause, -60));
    error_log("removeCompleteClause - AFTER CLAUSE (first 60 chars): " . substr($afterClause, 0, 60));
    
    // Si lo último antes de la cláusula es ), verificar si hay una comilla antes del )
    if ($clauseStart > 0 && preg_match('/([\"\'])(\))\s*$/i', $beforeClause, $matches)) {
        error_log("removeCompleteClause - Found quote before ) at end of beforeClause: " . $matches[0]);
        // Ya tiene la comilla, no hacer nada especial
    }
    
    $result = $beforeClause . $afterClause;
    error_log("removeCompleteClause - RESULT (around join, 120 chars): " . substr($result, max(0, $clauseStart - 60), 120));
    
    // Limpiar espacios y conectores duplicados
    $result = preg_replace('/\s+(and|or)\s+(and|or)\s+/i', ' $2 ', $result);
    $result = preg_replace('/\s+and\s+order\s+by/i', ' ORDER BY', $result);
    $result = preg_replace('/\s+or\s+order\s+by/i', ' ORDER BY', $result);
    $result = preg_replace('/WHERE\s+(and|or)\s+/i', 'WHERE ', $result);
    
    return $result;
}

/**
 * Corrige el paréntesis faltante en cláusulas IN de multiselección
 * Ejemplo: IN ("9","10" ORDER BY -> IN ("9","10") ORDER BY
 * 
 * @param string $sql Consulta SQL a corregir
 * @return string Consulta SQL con paréntesis corregidos
 */
function fixMultiselectionInClause($sql) {
    // Patrón para detectar IN con valores pero sin cierre correcto antes de ORDER BY, GROUP BY, etc.
    // IN ("valores" ORDER BY -> IN ("valores") ORDER BY
    $pattern = '/\bIN\s*\(\s*(["\'][^"\']+["\'](?:\s*,\s*["\'][^"\']+["\'])*)\s+(ORDER\s+BY|GROUP\s+BY|HAVING|LIMIT|\))/i';
    
    $fixed = preg_replace_callback($pattern, function($matches) {
        $values = trim($matches[1]);
        $nextClause = $matches[2];
        
        // Agregar el paréntesis de cierre del IN antes de la siguiente cláusula
        $result = 'IN (' . $values . ') ' . $nextClause;
        error_log("fixMultiselectionInClause: Corregido IN sin cierre - agregado ) antes de " . $nextClause);
        return $result;
    }, $sql);
    
    return $fixed;
}

/**
 * Función principal: Corrige todos los problemas conocidos en consultas SQL
 * 
 * @param string $sql Consulta SQL a limpiar
 * @return string Consulta SQL limpia
 */
function cleanSqlUniversal($sql) {
    // NUEVO: Si el SQL viene del sistema de 3 fases, solo aplicar limpiezas mínimas
    if (isset($_SESSION['usar_sistema_tres_fases']) && $_SESSION['usar_sistema_tres_fases'] === true) {
        error_log("cleanSqlUniversal: SQL viene del sistema de 3 fases - aplicando solo limpiezas mínimas");
        // Solo decodificar HTML entities y normalizar espacios
        $sql = html_entity_decode($sql, ENT_QUOTES, 'UTF-8');
        $sql = preg_replace('/\s+/', ' ', $sql); // Normalizar múltiples espacios a uno solo
        $sql = trim($sql);
        return $sql;
    }
    
    // Log inicial para depuración
    error_log("SQL antes de limpieza: " . substr($sql, -200));
    
    // Aplicar correcciones específicas primero
    $sql = fixMismatchedQuotes($sql);
    error_log("Después de fixMismatchedQuotes: " . substr($sql, -200));
    
    $sql = fixTableAliasReferences($sql);
    error_log("Después de fixTableAliasReferences: " . substr($sql, -200));
    
    // Aplicar correcciones adicionales para casos específicos
    $sql = forceFixSpecificPatterns($sql);
    error_log("Después de forceFixSpecificPatterns: " . substr($sql, -200));
    
    // Corregir JOINs tautológicos específicos (ie.idestadoproducto=ie.idestadoproducto)
    $sql = preg_replace('/\bie\.idestadoproducto\s*=\s*ie\.idestadoproducto\b/', 'ie.idestadoproducto=ik.idestadoproducto', $sql);
    if (strpos($sql, 'ie.idestadoproducto=ik.idestadoproducto') !== false) {
        error_log("Corregido JOIN tautológico: ie.idestadoproducto=ie.idestadoproducto -> ie.idestadoproducto=ik.idestadoproducto");
    }
    
    $sql = fixHtmlInCaseWhen($sql);
    $sql = normalizeQuotesInSql($sql);
    $sql = fixAllSqlIssues($sql);
    
    // NUEVA MEJORA: Limpieza específica para problemas de multiselección
    $sql = cleanMultiselectionInConditions($sql);
    
    // CRÍTICO: Corregir paréntesis faltante en IN de multiselección
    $sql = fixMultiselectionInClause($sql);
    
    // LIMPIEZA FINAL: Eliminar TODOS los patrones residuales [@...] antes de devolver el SQL
    error_log("cleanSqlUniversal: Ejecutando limpieza final de patrones residuales...");
    $sql = removeAllUnresolvedPatterns($sql);
    error_log("cleanSqlUniversal: Limpieza final completada");
    
    return $sql;
}

/**
 * Función que fuerza la corrección de patrones específicos problemáticos
 */
function forceFixSpecificPatterns($sql) {
    // Patrones específicos muy directos para problemas conocidos
    $forcePatterns = array(
        // Comillas mixtas específicas
        '/"14\'/' => '"14"',
        '/\'11"/' => "'11'",
        '/"N\/A\'/' => '"N/A"',
        // Alias específico
        '/\bik\.idestadoproducto\b/' => 'ie.idestadoproducto',
    );
    
    foreach ($forcePatterns as $pattern => $replacement) {
        if (preg_match($pattern, $sql)) {
            $sql = preg_replace($pattern, $replacement, $sql);
            error_log("Aplicado patrón forzado: $pattern -> $replacement");
        }
    }
    
    return $sql;
}

/**
 * Corrige comillas desbalanceadas en SQL - GENÉRICO para todos los reportes
 * 
 * @param string $sql Consulta SQL a limpiar
 * @return string Consulta SQL con comillas corregidas
 */
function fixMismatchedQuotes($sql) {
    // Patrones de comillas desbalanceadas comunes - PHP 5.5.9 compatible
    $patterns = array(
        // Patrón AGRESIVO: "valor' -> "valor" (cualquier contexto)
        '/"([^"\']*)\'/i' => '"$1"',
        // Patrón AGRESIVO: 'valor" -> 'valor' (cualquier contexto)
        '/\'([^"\']*)\"/i' => "'$1'",
        // Patrón específico en ELSE: "N/A' -> "N/A"
        '/ELSE\s+"([^"\']*)\'\s+END/i' => 'ELSE "$1" END',
        // Patrón específico en ELSE: 'N/A" -> 'N/A'
        '/ELSE\s+\'([^"\']*)\"\s+END/i' => "ELSE '$1' END",
        // Corregir condiciones WHERE específicas con = y comillas mixtas
        '/=\s*"([^"\']*)\'/i' => '= "$1"',
        '/=\s*\'([^"\']*)\"/i' => "= '$1'",
        // Corregir dentro de paréntesis con comillas mixtas
        '/\(\s*([a-zA-Z_][a-zA-Z0-9_]*\.[a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*"([^"\']*)\'\s*\)/i' => '($1 = "$2")',
        '/\(\s*([a-zA-Z_][a-zA-Z0-9_]*\.[a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*\'([^"\']*)\"\s*\)/i' => "($1 = '$2')",
    );
    
    foreach ($patterns as $pattern => $replacement) {
        $sql = preg_replace($pattern, $replacement, $sql);
    }
    
    return $sql;
}

/**
 * Corrige referencias de alias de tabla incorrectas - GENÉRICO para todos los reportes
 * 
 * @param string $sql Consulta SQL a limpiar
 * @return string Consulta SQL con alias corregidos
 */
function fixTableAliasReferences($sql) {
    // Patrones específicos conocidos de alias incorrectos - más directo y efectivo
    $commonAliasFixes = array(
        // Problema específico del reporte 21: ik no existe, debería ser ie (inventarios_estados)
        '/\bik\.idestadoproducto\b/i' => 'ie.idestadoproducto',
        '/\bik\.descripcionestado\b/i' => 'ie.descripcionestado',
    );
    
    // Aplicar correcciones específicas primero
    foreach ($commonAliasFixes as $pattern => $replacement) {
        if (preg_match($pattern, $sql)) {
            $sql = preg_replace($pattern, $replacement, $sql);
            error_log("SQL Cleaner: Corregido alias específico con patrón '$pattern' -> '$replacement'");
        }
    }
    
    // Extraer aliases de tabla definidos en la consulta para verificaciones adicionales
    preg_match_all('/\b(FROM|JOIN)\s+([a-zA-Z_][a-zA-Z0-9_]*)\s+([a-zA-Z_][a-zA-Z0-9_]*)\b/i', $sql, $matches);
    $validAliases = array();
    
    if (!empty($matches[3])) {
        $validAliases = array_unique($matches[3]);
    }
    
    // Buscar referencias a aliases no válidos en WHERE
    if (!empty($validAliases)) {
        // Encontrar aliases incorrectos en condiciones WHERE
        preg_match_all('/\b([a-zA-Z_][a-zA-Z0-9_]*)\.[a-zA-Z_][a-zA-Z0-9_]*\s*=/', $sql, $aliasMatches);
        
        if (!empty($aliasMatches[1])) {
            $usedAliases = array_unique($aliasMatches[1]);
            
            foreach ($usedAliases as $usedAlias) {
                if (!in_array($usedAlias, $validAliases)) {
                    // Intentar encontrar el alias correcto más similar
                    $bestMatch = findBestAliasMatch($usedAlias, $validAliases);
                    if ($bestMatch) {
                        $sql = preg_replace('/\b' . preg_quote($usedAlias) . '\./', $bestMatch . '.', $sql);
                        error_log("SQL Cleaner: Corregido alias incorrecto '$usedAlias' por '$bestMatch'");
                    }
                }
            }
        }
    }
    
    return $sql;
}

/**
 * Encuentra el mejor alias coincidente basado en similitud
 */
function findBestAliasMatch($invalidAlias, $validAliases) {
    $bestMatch = null;
    $bestScore = 0;
    
    foreach ($validAliases as $validAlias) {
        // Calcular similitud simple
        $similarity = 0;
        similar_text(strtolower($invalidAlias), strtolower($validAlias), $similarity);
        
        if ($similarity > $bestScore && $similarity > 50) { // 50% de similitud mínima
            $bestScore = $similarity;
            $bestMatch = $validAlias;
        }
    }
    
    return $bestMatch;
}
/**
 * Corrige problemas específicos con HTML en cláusulas CASE WHEN
 * Particularmente útil para reportes con enlaces directos
 *
 * @param string $sql Consulta SQL a limpiar
 * @return string Consulta SQL limpia
 */
function fixHtmlInCaseWhen($sql) {
    // Patrones para el caso específico del CASE en reportes - PHP 5.5.9 compatible
    $patterns = array(
        // Patrón para enlaces de envíos
        'concat("<center><a href=\"../../modulos/envios/envio_imprimir.php?idenvio=\",ik.foliodoctoorigen," > ",ik.foliodoctoorigen," < /A > < /Center > ")' => 
            "concat('<center><a href=\"../../modulos/envios/envio_imprimir.php?idenvio=',ik.foliodoctoorigen,'\">',ik.foliodoctoorigen,'</a></center>')",
            
        // Patrón para enlaces de recepciones
        'concat("<center><a href=\"../../modulos/recepciones/recepcion_imprimir.php?idrecepcion=\",ik.foliodoctoorigen," > ",ik.foliodoctoorigen," < /A > < /Center > ")' => 
            "concat('<center><a href=\"../../modulos/recepciones/recepcion_imprimir.php?idrecepcion=',ik.foliodoctoorigen,'\">',ik.foliodoctoorigen,'</a></center>')",
            
        // Patrón para enlaces de retiros
        'concat("<center><a href=\"../../modulos/retiros/retiro_imprimir.php?folio=\",ik.foliodoctoorigen," > ",ik.foliodoctoorigen," < /A > < /Center > ")' => 
            "concat('<center><a href=\"../../modulos/retiros/retiro_imprimir.php?folio=',ik.foliodoctoorigen,'\">',ik.foliodoctoorigen,'</a></center>')",
    );
    
    foreach ($patterns as $pattern => $replacement) {
        $sql = str_replace($pattern, $replacement, $sql);
    }
    
    // Solución general para cualquier enlace con sintaxis similar - PHP 5.5.9 compatible
    $general_patterns = array(
        // Patrón general para etiquetas con espacios que pueden causar problemas
        '/concat\("(.*?)href=\\"(.*?)\\",(.*?)" > ",(.*?)," < \/A > < \/Center > "\)/i' => 
            "concat('$1href=\"$2',$3,'\">',($4),'</a></center>')",
    );
    
    foreach ($general_patterns as $pattern => $replacement) {
        $sql = preg_replace($pattern, $replacement, $sql);
    }
    
    return $sql;
}

/**
 * Normaliza comillas mixtas en SQL que pueden causar errores de sintaxis
 * Corrige problemas como '10" o "10' asegurando consistencia en las comillas
 * 
 * @param string $sql Consulta SQL con posibles comillas mixtas
 * @return string Consulta SQL con comillas normalizadas
 */
function normalizeQuotesInSql($sql) {
    // Patrón 1: Corregir el caso específico reportado ('10") -> ('10')
    $sql = preg_replace("/'([^'\"]*)\"/", "'$1'", $sql);
    
    // Patrón 2: Corregir el caso inverso ("10') -> ("10")
    $sql = preg_replace("/\"([^'\"]*)\'/", '"$1"', $sql);
    
    // Patrón 3: Corregir condiciones WHERE específicas con comillas mixtas
    // Buscar patrones como: = '10" y convertir a = '10'
    $sql = preg_replace("/(\w+\s*=\s*)'([^'\"]*)\"/", "$1'$2'", $sql);
    
    // Patrón 4: Buscar patrones como: = "10' y convertir a = "10"
    $sql = preg_replace("/(\w+\s*=\s*)\"([^'\"]*)\'/", '$1"$2"', $sql);
    
    // Patrón 5: Corregir en condiciones entre paréntesis
    // (campo = '10") -> (campo = '10')
    $sql = preg_replace("/(\([^)]*=\s*)'([^'\"]*)\"/", "$1'$2'", $sql);
    $sql = preg_replace("/(\([^)]*=\s*)\"([^'\"]*)\'/", '$1"$2"', $sql);
    
    // Patrón 6: Verificación y corrección de seguridad para cualquier patrón restante
    // Buscar cualquier ' seguido de " al final de valor
    $sql = preg_replace("/'([^']*)\"/", "'$1'", $sql);
    
    // Buscar cualquier " seguido de ' al final de valor  
    $sql = preg_replace("/\"([^\"]*)\'/", '"$1"', $sql);
    
    // Log de depuración específico para el caso reportado
    if (preg_match("/'[^']*\"/", $sql) || preg_match("/\"[^\"]*\'/", $sql)) {
        error_log("ADVERTENCIA: Aún se detectan comillas mixtas en SQL después de normalización: " . $sql);
    }
    
    return $sql;
}

function fixAllSqlIssues($sql) {
    // NUEVO: Si el SQL viene del sistema de 3 fases, NO aplicar correcciones heurísticas
    if (isset($_SESSION['usar_sistema_tres_fases']) && $_SESSION['usar_sistema_tres_fases'] === true) {
        error_log("fixAllSqlIssues: SQL viene del sistema de 3 fases - NO se aplican correcciones heurísticas");
        return $sql; // Devolver SQL sin modificar
    }
    
    // Guardamos el SQL original para depuración
    $originalSql = $sql;
    
    // 0. PRIMERO: Normalizar comillas mixtas en el SQL
    $sql = normalizeQuotesInSql($sql);
    error_log("Aplicada normalización de comillas mixtas");
    
    // 1. SEGUNDO: Corregir problemas con HTML en cláusulas CASE WHEN
    $sql = fixHtmlInCaseWhen($sql);
    error_log("Aplicada corrección de HTML en cláusulas CASE WHEN");
    
    // 2. TERCERO: Normalizar espacios en operadores SQL
    $sql = normalizarEspaciosEnOperadores($sql);
    error_log("Aplicada normalización universal de espacios en operadores");
    

    
    // 1. SEGUNDA SOLUCIÓN: Eliminar condiciones vacías (filtros con valor '%')
    $sql = eliminarCondicionesVacias($sql);
    
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
    
    // 6. NUEVA SOLUCIÓN UNIVERSAL: Corregir paréntesis desbalanceados antes de ORDER BY
    $sql = fixUnbalancedParenthesisBeforeOrderBy($sql);
    
    // 6.5 Corregir formatos incorrectos en condiciones AND múltiples
    $sql = fixMultipleAndConditions($sql);
    
    // 7. Corregir problemas específicos con paréntesis y AND
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
 * Corrige condiciones AND múltiples que no están bien separadas.
 * Por ejemplo, el caso: (obo.idbodega = '9'and (il.idloteproducto = '%'))
 * donde falta un espacio y paréntesis correctos.
 * 
 * @param string $sql Consulta SQL a corregir
 * @return string Consulta SQL corregida
 */
function fixMultipleAndConditions($sql) {
    // Soluciones generales para condiciones AND mal formadas
    $patterns = [
        // Condición: valor'and -> valor') and (paréntesis faltante antes de and)
        '/([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2and\s*\(/i' => '$1 = $2$3$2) and (',
        
        // Condición: valor"and + campo -> valor") and (campo (paréntesis faltantes)
        '/([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2and\s+([a-zA-Z0-9_.]+)/i' => '$1 = $2$3$2) and ($4',
        
        // Condición: número sin comillas: campo = 9and -> campo = 9) and
        '/([a-zA-Z0-9_.]+)\s*=\s*([0-9]+)and\s*\(/i' => '$1 = $2) and (',
        
        // Condición general: (campo = valor and (campo2 = valor2)) -> (campo = valor) and (campo2 = valor2)
        '/\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2and\s+\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\5\s*\)\)/i' => 
            '($1 = $2$3$2) and ($4 = $5$6$5)',
    ];
    
    // Aplicar cada patrón a la consulta
    foreach ($patterns as $pattern => $replacement) {
        $newSql = preg_replace($pattern, $replacement, $sql);
        if ($newSql !== $sql) {
            error_log("Corregido formato de condiciones AND múltiples usando patrón general");
            $sql = $newSql;
        }
    }
    
    // Solución general para cualquier condición sin paréntesis de cierre antes de ORDER BY
    if (stripos($sql, "ORDER BY") !== false) {
        // Patrón general para condiciones antes de ORDER BY sin paréntesis
        $orderByPatterns = [
            // Patrón 1: and (campo = valor ORDER BY -> and (campo = valor) ORDER BY
            '/and\s+\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2\s+ORDER\s+BY/i' => 
                'and ($1 = $2$3$2) ORDER BY',
            
            // Patrón 2: )and (campo = valor ORDER BY -> ) and (campo = valor) ORDER BY
            '/\)and\s+\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2\s+ORDER\s+BY/i' => 
                ') and ($1 = $2$3$2) ORDER BY',
            
            // Patrón 3: cualquier campo = valor sin cerrar antes de ORDER BY
            '/\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2\s+ORDER\s+BY/i' => 
                '($1 = $2$3$2) ORDER BY',
                
            // Patrón 4: fecha <= "valor")and -> fecha <= "valor") and
            '/([a-zA-Z0-9_.]+)\s*<=\s*[\'\"]([^\'\"]+)[\'\"][\)]+and/i' => 
                '$1 <= "$2") and',
        ];
        
        foreach ($orderByPatterns as $pattern => $replacement) {
            $newSql = preg_replace($pattern, $replacement, $sql);
            if ($newSql !== $sql) {
                error_log("Aplicada corrección universal para condición sin cerrar antes de ORDER BY");
                $sql = $newSql;
            }
        }
    }
    
    // Solución universal para condiciones AND sin paréntesis adecuados
    // Este patrón busca cualquier combinación de condiciones AND sin paréntesis adecuados
    $general_pattern = '/\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2(\s+and|\s*and\s*)\(?([a-zA-Z0-9_.]+)/i';
    $general_replacement = '($1 = $2$3$2)$4 ($5';
    
    $newSql = preg_replace($general_pattern, $general_replacement, $sql);
    if ($newSql !== $sql) {
        error_log("Aplicada corrección universal para condiciones AND sin paréntesis adecuados");
        $sql = $newSql;
    }
    
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
 * Función auxiliar para normalizar espacios en operadores SQL
 * MODIFICADO: Ahora no realiza ninguna alteración a la consulta SQL original
 * basado en la solicitud del usuario de respetar el formato original
 * 
 * @param string $sql Consulta SQL a procesar
 * @return string Consulta SQL sin modificaciones
 */
function normalizarEspaciosEnOperadores($sql) {
    // Por solicitud del usuario, dejamos la responsabilidad de escribir SQL
    // correctamente al usuario y no modificamos los espacios o caracteres
    
    // Simplemente devolvemos la consulta SQL sin ninguna modificación
    return $sql;
}

/**
 * Elimina condiciones completas cuando un filtro no tiene valor (es '%' o está vacío)
 * Esta función maneja específicamente los casos donde un filtro tipo [@...] no fue seleccionado
 * y por lo tanto debe eliminarse toda la cláusula AND/OR que lo contiene
 *
 * @param string $sql La consulta SQL a procesar
 * @return string La consulta SQL con las condiciones vacías eliminadas
 */
function eliminarCondicionesVacias($sql) {
    // Aplicar normalización de espacios primero
    $sql = normalizarEspaciosEnOperadores($sql);
    // Buscar patrones generales donde hay un filtro = '%'
    $patrones = [
        // Patrón general 1: and (campo = '%')
        '/\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)/i',
        
        // Patrón general 2: and campo = '%'
        '/\s+(and|AND)\s+([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]/i',
        
        // Patrón general 3: or (campo = '%')
        '/\s+(or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)/i',
        
        // Patrón general 4: or campo = '%'
        '/\s+(or|OR)\s+([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]/i',
        
        // Patrón general 5: and (campo = '') - comillas vacías
        '/\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"][\'\"]\s*\)/i',
        
        // Patrón general 6: and campo = '' - comillas vacías
        '/\s+(and|AND)\s+([a-zA-Z0-9_.]+)\s*=\s*[\'\"][\'\"]/i',
        
        // Patrón general 7: and (campo = '%%') - condición con %% doble
        '/\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%%[\'\"]\s*\)/i'
    ];
    
    // Aplicar cada patrón y eliminar las condiciones vacías
    foreach ($patrones as $patron) {
        if (preg_match($patron, $sql, $matches)) {
            $operador = $matches[1]; // AND u OR
            $campo = isset($matches[2]) ? $matches[2] : ''; // El campo de la condición
            
            // Eliminar la condición completa
            $sql = preg_replace($patron, '', $sql);
            error_log("Eliminada condición vacía: $operador $campo = '%'");
        }
    }
    
    // Caso especial: paréntesis dobles por eliminación de condición (and ()) -> eliminar completamente
    $sql = preg_replace('/\s+(and|AND)\s+\(\s*\)/i', '', $sql);
    
    // Solución GENERAL para cualquier campo con valor % antes de ORDER BY
    if (stripos($sql, 'ORDER BY') !== false) {
        // Patrón general para cualquier campo con % antes de ORDER BY
        $sql = preg_replace('/\)\s+(AND|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)\s+ORDER\s+BY/i', 
                           ') ORDER BY', $sql);
        error_log("Aplicada solución general para condiciones con % antes de ORDER BY");
    }
    
    // Solución GENERAL para condiciones de filtro = valor AND filtro2 = % antes de ORDER BY
    $generalPattern = '/\(([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2\s*and\s*\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)\s+ORDER\s+BY/i';
    if (preg_match($generalPattern, $sql, $matches)) {
        $campo = $matches[1]; // El campo del primer filtro (ej: obd.idbodega)
        $tipoComilla = $matches[2]; // El tipo de comilla original (' o ")
        $valor = $matches[3]; // El valor no-vacío (ej: '9')
        
        $replacement = '('.$campo.' = '.$tipoComilla.$valor.$tipoComilla.') ORDER BY';
        $sql = preg_replace($generalPattern, $replacement, $sql);
        error_log("Aplicada solución general para eliminar filtros vacíos antes de ORDER BY - manteniendo comillas originales");
    }
    
    // Patrón general para condiciones vacías antes de ORDER BY - aún más general para capturar cualquier variante similar
    if (stripos($sql, 'ORDER BY') !== false) {
        // Patrón 1: cierre de paréntesis + AND + condición con '%' + ORDER BY
        $sql = preg_replace('/\)\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)\s+ORDER\s+BY/i', ') ORDER BY', $sql);
        
        // Patrón 2: condición normal + AND + condición con '%' + ORDER BY (sin paréntesis iniciales)
        $sql = preg_replace('/([a-zA-Z0-9_.]+)\s*=\s*([\'\"])([^\'\"]+)\2\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)\s+ORDER\s+BY/i', '$1 = $2$3$2 ORDER BY', $sql);
        
        // Patrón 3: orden arbitrario - si hay AND (campo = '%') justo antes de ORDER BY
        $sql = preg_replace('/\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)\s+ORDER\s+BY/i', ' ORDER BY', $sql);
        
        error_log("Eliminada condición vacía antes de ORDER BY");
    }
    
    // Buscar condiciones específicas con fechaenvio between
    if (preg_match('/\(([a-zA-Z0-9_.]+)\.fechaenvio\s+between\s+[\'\"]([^\'\"]+)[\'\"]\s+and\s+[\'\"]([^\'\"]+)[\'\"]\s*\)\s*(AND|and)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)/i', $sql, $matches)) {
        // Tenemos una condición de fecha seguida por un filtro vacío con '%'
        $condicionCompleta = $matches[0];
        $campo = $matches[1] . '.fechaenvio';
        $fechaInicio = $matches[2];
        $fechaFin = $matches[3];
        
        // Eliminar solo la parte AND (campo = '%')
        $reemplazo = '(' . $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '")';
        $sql = str_replace($condicionCompleta, $reemplazo, $sql);
        error_log("Corregida condición de fecha con filtro vacío: $condicionCompleta -> $reemplazo");
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
    
    // Caso 6: Solución general para cualquier campo LIKE con paréntesis extra
    $sql = preg_replace('/\(([a-zA-Z0-9_.]+)\s+LIKE\s+[\'"]%%[\'"]\s*\)\s+and\s*\)/i', '($1 LIKE \'%%\')', $sql);
    
    // Caso 7: Solución general para paréntesis excesivos en condiciones LIKE seguido por AND + otra condición
    $generalPattern = '/\(([a-zA-Z0-9_.]+)\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*([^()]+?)\)/i';
    $sql = preg_replace($generalPattern, '($1 LIKE \'%%\')) $2 ($3 = $4)', $sql);
    
    // Caso 8: Solución general para cualquier condición LIKE con paréntesis extra antes de ORDER BY
    $sql = preg_replace('/\(([a-zA-Z0-9_.]+)\s+LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s*ORDER/i', '($1 LIKE \'%%\')) ORDER', $sql);
    
    // Solución general para cualquier campo LIKE con paréntesis extra
    $sql = preg_replace('/LIKE\s+[\'"]%%[\'"]\s*\)\s*\)\s*ORDER/i', 'LIKE \'%%\')) ORDER', $sql);
    
    // NUEVAS SOLUCIONES PARA REPORTE 2 Y PROBLEMAS SIMILARES
    
    // Caso 9: Fechas con paréntesis extras antes de AND (condiciones con paréntesis desbalanceados)
    // Patrón: "2025/05/08 23:59:59")) and (ip.idproducto = '25') AND ("
    $sql = preg_replace('/(\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2}:\d{2})[\'"]\)\)+(\s+(and|AND|or|OR))/i', '$1")$2', $sql);
    
    // Caso 10: Paréntesis dobles o triples que causan desbalance
    $sql = preg_replace('/23:59:59[\'"]\)\)+\s*/i', '23:59:59")', $sql);
    
    // Caso 11: Condición AND ( incompleta al final
    $sql = preg_replace('/\s+(and|AND|or|OR)\s+\(\s*(ORDER BY|GROUP BY|HAVING|LIMIT)/i', ' $2', $sql);
    
    // Caso 12: Condición AND ( incompleta y desbalanceada
    $sql = preg_replace('/\s+(and|AND|or|OR)\s+\(\s*$/i', '', $sql);
    
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
/**
 * Elimina paréntesis excesivos o mal formados en consultas SQL
 * Soluciona problemas específicos con filtros anidados
 *
 * @param string $query La consulta SQL a procesar
 * @return string La consulta SQL corregida
 */
function eliminaParentesisExcesivos($query) {
    // Caso específico: secuencias de cierre de paréntesis separados por AND/OR
    // Ejemplo: )) AND ()) ORDER BY -> )) ORDER BY
    $query = preg_replace('/\)\s*\)\s+(AND|OR)\s+\(\s*\)\s*\)/i', ')) ', $query);
    
    // Problema común: Paréntesis de apertura seguidos de cierre sin contenido
    // Ejemplo: WHERE (()) -> WHERE ()
    $query = preg_replace('/\(\s*\(\s*\)\s*\)/i', '()', $query);
    
    // Problema común: Condición vacía
    // Ejemplo: WHERE () AND campo=valor -> WHERE campo=valor
    $query = preg_replace('/WHERE\s+\(\s*\)\s+(AND|OR)/i', 'WHERE ', $query);
    
    // Corrección general para cualquier campo LIKE con paréntesis extra
    if (stripos($query, 'LIKE') !== false) {
        // Arreglar el problema de AND extra antes de ORDER BY
        $query = preg_replace('/\)\s*\)\s+(AND|OR)?\s*ORDER BY/i', ')) ORDER BY', $query);
        
        // Solución general para cualquier campo con LIKE '%%'
        $query = preg_replace('/\(([a-zA-Z0-9_.]+)\s+LIKE\s+\'%%\'\)\s*\)\s+(AND|OR)/i', 
                             '($1 LIKE \'%%\')) ', $query);
    }
    
    // Eliminar AND/OR redundantes al final de condiciones
    // Ejemplo: WHERE (campo1=1 AND campo2=2 AND ) -> WHERE (campo1=1 AND campo2=2)
    $query = preg_replace('/(\s+(AND|OR)\s+)+\)/i', ')', $query);
    
    // NUEVO: Detectar y corregir paréntesis extra antes de ORDER BY
    // Caso específico: ...))) ORDER BY... -> ...)) ORDER BY...
    $query = preg_replace('/\)\s*\)\s*\)\s+ORDER\s+BY/i', ')) ORDER BY', $query);
    
    // NUEVO: Detectar paréntesis desbalanceados al final de la consulta
    // Contar el número de paréntesis de apertura y cierre
    $openParens = substr_count($query, '(');
    $closeParens = substr_count($query, ')');
    
    // Si hay más paréntesis de cierre que de apertura
    if ($closeParens > $openParens) {
        // Eliminar el exceso de paréntesis
        $excess = $closeParens - $openParens;
        
        // Si hay paréntesis excesivos, eliminarlos justo antes de ORDER BY
        if (preg_match('/\)\s*\){' . $excess . '}\s+ORDER\s+BY/i', $query)) {
            $query = preg_replace('/\)\s*\){' . $excess . '}\s+ORDER\s+BY/i', ')) ORDER BY', $query);
        }
        // O al final de la consulta si no hay ORDER BY
        else {
            $pattern = '/\){' . $excess . '}\s*$/';
            if (preg_match($pattern, $query)) {
                $query = preg_replace($pattern, ')', $query);
            }
        }
    }
    
    // SOLUCIÓN UNIVERSAL MEJORADA: Corrección para cualquier problema de paréntesis antes de ORDER BY
    
    // PASO 1: Corregir patrón específico del reporte 5 y similares
    // Este patrón maneja casos como: ...)) and (campo = valor ORDER BY...
    $matches = [];
    if (preg_match('/\)\s+(and|AND|or|OR)\s+\(([^)]+?)\s+(ORDER\s+BY)/i', $query, $matches)) {
        $fullPattern = $matches[0];
        $operator = $matches[1];  // 'and' o 'or'
        $condition = $matches[2]; // contenido entre paréntesis
        $orderBy = $matches[3];   // 'ORDER BY'
        
        // Reemplazar con paréntesis cerrado correctamente
        $replacement = ') ' . $operator . ' (' . $condition . ') ' . $orderBy;
        $query = str_replace($fullPattern, $replacement, $query);
        error_log("Corregido patrón universal: ') $operator (campo = valor ORDER BY' -> ') $operator (campo = valor) ORDER BY'");
    }
    
    // PASO 2: Correcciones generales para cualquier patrón de paréntesis antes de ORDER BY
    $query = preg_replace('/\)\)\)\s+(ORDER\s+BY)/i', ')) $1', $query); // ))) ORDER BY -> )) ORDER BY
    $query = preg_replace('/\)\)\s+(ORDER\s+BY)/i', ')) $1', $query);   // )) ORDER BY -> )) ORDER BY (normalización)
    
    // PASO 3: Verificar con expresión regular más amplia para otros casos posibles
    $query = preg_replace('/\)\s+(and|AND|or|OR)\s+\(([^)]+)\s+(ORDER\s+BY|GROUP\s+BY|HAVING)/i', 
                       ') $1 ($2) $3', $query);
    
    error_log("Aplicada corrección universal mejorada para paréntesis antes de ORDER BY");
    
    // NUEVA SOLUCIÓN: Arreglar específicamente el patrón de paréntesis desbalanceados antes de ORDER BY
    if (stripos($query, 'ORDER BY') !== false) {
        // Dividir la consulta en dos partes: antes y después de ORDER BY
        $parts = preg_split('/ORDER\s+BY/i', $query, 2);
        if (count($parts) == 2) {
            $beforeOrderBy = $parts[0];
            $afterOrderBy = $parts[1];
            
            // Contar paréntesis antes de ORDER BY
            $openBefore = substr_count($beforeOrderBy, '(');
            $closeBefore = substr_count($beforeOrderBy, ')');
            
            // Si hay más paréntesis de apertura que de cierre antes de ORDER BY
            if ($openBefore > $closeBefore) {
                // Añadir los paréntesis faltantes
                $diff = $openBefore - $closeBefore;
                $beforeOrderBy .= str_repeat(')', $diff);
                error_log("Añadidos $diff paréntesis de cierre antes de ORDER BY");
            } 
            // Si hay más paréntesis de cierre que de apertura
            else if ($closeBefore > $openBefore) {
                // Eliminar los paréntesis de cierre sobrantes
                $diff = $closeBefore - $openBefore;
                // Eliminar los paréntesis desde el final
                for ($i = 0; $i < $diff; $i++) {
                    $pos = strrpos($beforeOrderBy, ')');
                    if ($pos !== false) {
                        $beforeOrderBy = substr($beforeOrderBy, 0, $pos) . substr($beforeOrderBy, $pos + 1);
                    }
                }
                error_log("Eliminados $diff paréntesis de cierre sobrantes antes de ORDER BY");
            }
            
            // Reconstruir la consulta con paréntesis balanceados
            $query = $beforeOrderBy . 'ORDER BY' . $afterOrderBy;
        }
    }
    
    return $query;
}

/**
 * Función especial para eliminar cláusulas AND completas cuando no se selecciona ningún valor en combos
 * 
 * Esta función elimina cláusulas completas del tipo:
 * and (obo.idbodega = "[@BodegaOrigen;val;des;select idbodega val, nombrebodega des from operaciones_bodegas order by des]")
 * 
 * @param string $sql La consulta SQL original
 * @param array $emptyFilters Arreglo con los nombres de los filtros que están vacíos
 * @return string La consulta SQL sin las cláusulas AND correspondientes a filtros vacíos
 */
function eliminarClausulasAndCompletas($sql, $emptyFilters = array()) {
    // Si no hay filtros vacíos, no hacemos nada
    if (empty($emptyFilters)) {
        return $sql;
    }
    
    // Log para depuración
    error_log("Aplicando eliminación de cláusulas AND para filtros vacíos: " . implode(", ", $emptyFilters));
    error_log("SQL original: " . $sql);
    
    // Para cada filtro vacío, eliminar la cláusula AND completa
    foreach ($emptyFilters as $filterName) {
        // CRÍTICO: Los patrones ahora capturan la comilla final en un grupo para preservarla
        // Estructura: capturar todo el patrón [@...] pero preservar la comilla final
        $patterns = array(
            // Patrón 1: and (campo = "[@Filtro;...]")
            '/\s+and\s+\([a-zA-Z0-9_.]+\s*=\s*"?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)\s*\)/i',
            
            // Patrón 2: and (campo in ("[@Filtro;...]"))
            '/\s+and\s+\([a-zA-Z0-9_.]+\s+in\s+\("?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)\s*\)\)/i',
            
            // Patrón 3: and campo = "[@Filtro;...]"
            '/\s+and\s+[a-zA-Z0-9_.]+\s*=\s*"?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)/i',
            
            // Patrón 4: and campo in ("[@Filtro;...]")
            '/\s+and\s+[a-zA-Z0-9_.]+\s+in\s+\("?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)\s*\)/i',
            
            // Patrón 5: OR (campo = "[@Filtro;...]")
            '/\s+or\s+\([a-zA-Z0-9_.]+\s*=\s*"?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)\s*\)/i',
            
            // Patrón 6: OR campo = "[@Filtro;...]"
            '/\s+or\s+[a-zA-Z0-9_.]+\s*=\s*"?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)/i',
            
            // Patrón 7: and (campo = 'valor') - para cualquier filtro vacío con valor específico
            '/\s+and\s+\([a-zA-Z0-9_.]+\s*=\s*[\'\"][^\'\"]*' . preg_quote($filterName, '/') . '[^\'\"]*[\'\"]\s*\)/i',
            
            // Patrón 8: Detectar cláusulas dentro de múltiples niveles de paréntesis
            '/\s+and\s+\(\s*\(\s*[a-zA-Z0-9_.]+\s*=\s*"?\[@' . preg_quote($filterName, '/') . '[^]]*\](["\']?)\s*\)\s*\)/i',
            
            // Patrón 9: Patrones similares pero sin el signo @ (para reporte 4)
            '/\s+and\s+\([a-zA-Z0-9_.]+\s*=\s*"?\[' . preg_quote($filterName, '/') . '[^]]*\](["\']?)\s*\)/i',
            '/\s+and\s+[a-zA-Z0-9_.]+\s*=\s*"?\[' . preg_quote($filterName, '/') . '[^]]*\](["\']?)/i'
        );
        
        // Aplicar cada patrón PRESERVANDO la comilla final
        foreach ($patterns as $pattern) {
            $oldSQL = $sql;
            // IMPORTANTE: $1 contiene la comilla final capturada, la preservamos en el reemplazo
            $sql = preg_replace($pattern, '$1', $sql);
            // Si hubo un cambio, registrarlo
            if ($oldSQL !== $sql) {
                error_log("Patrón aplicado (preservando comilla final): " . $pattern);
                error_log("SQL después del patrón: " . $sql);
            }
        }
    }
    
    return $sql;
}

/**
 * Limpia y corrige consultas SQL con problemas específicos en la cláusula WHERE
 * 
 * @param string $sql Consulta SQL a procesar
 * @return string Consulta SQL corregida
 */
function fixWhereClauseIssues($sql) {
    // 1. Corregir WHERE AND -> WHERE
    $sql = preg_replace('/WHERE\s+(AND|OR)\s+/i', 'WHERE ', $sql);
    
    // 2. Corregir (columna = valor) AND) -> (columna = valor)
    $sql = preg_replace('/\(([^()]+)\)\s+(AND|OR)\s*\)/i', '($1)', $sql);
    
    // 3. Corregir WHERE) -> WHERE
    $sql = preg_replace('/WHERE\s*\)/i', 'WHERE', $sql);
    
    // 4. Corregir WHERE AND( -> WHERE (
    $sql = preg_replace('/WHERE\s+(AND|OR)\s*\(/i', 'WHERE (', $sql);
    
    // 5. Corregir múltiples AND/OR consecutivos
    $sql = preg_replace('/\s+(AND|OR)\s+(AND|OR)\s+/i', ' $1 ', $sql);
    
    // NUEVAS CORRECCIONES PARA EL REPORTE 2 Y SIMILARES
    
    // 6. Corregir WHERE ... AND ( -> WHERE ... (cuando hay paréntesis incompletos)
    $sql = preg_replace('/WHERE\s+([^()]*?)\s+(AND|OR)\s+\(\s*(ORDER BY|GROUP BY|HAVING|LIMIT)/i', 'WHERE $1 $3', $sql);
    
    // 7. Corregir paréntesis desbalanceados en condiciones de fecha
    $sql = preg_replace('/(\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2}:\d{2})[\'"]\)\)+/i', '$1")', $sql);
    
    // 8. Corregir AND ( incompleto sin cierre al final de la cláusula WHERE
    $sql = preg_replace('/\s+(AND|OR)\s+\(\s*$/', '', $sql);
    
    // 9. Corregir AND ( seguido directamente por ORDER BY o final de cadena
    $sql = preg_replace('/\s+(AND|OR)\s+\(\s*(ORDER BY|GROUP BY|HAVING|$)/i', ' $2', $sql);
    
    // 10. Corregir formato de paréntesis para condiciones between (específico para reporte 2)
    $sql = preg_replace('/BETWEEN\s+[\'"](.*?)[\'"]\s+AND\s+[\'"](.*?)[\'"]\)\)+(\s+(and|AND))/i', 
                      'BETWEEN "$1" AND "$2")$3', $sql);
    
    // 11. Corregir espacio faltante entre fecha y and
    $sql = preg_replace('/"\)(\s*)(and|AND)/i', '") $2', $sql);
    
    return $sql;
}

function finalSqlCleanup($sql) {
    // Primero corregir el problema específico de "and )"
    $sql = fixExtraAndBeforeClosingParenthesis($sql);
    
    // SOLUCIÓN ESPECÍFICA: Solo eliminar patrones [@...] que interfieren con GROUP BY/ORDER BY
    // Solo actuar si hay patrones mal formados que causan errores de sintaxis SQL
    if (preg_match('/[@][^]]*\]\"\s*(GROUP\s+BY|ORDER\s+BY)/i', $sql)) {
        error_log("Detectado patrón mal formado que interfiere con GROUP BY/ORDER BY en finalSqlCleanup");
        $sql = preg_replace('/and\s*\([^)]*[@][^]]*\]\"\s*(GROUP\s+BY|ORDER\s+BY)/i', ' $1', $sql);
    }
    
    // CRÍTICO: Eliminar AND/OR extra antes de GROUP BY y ORDER BY
    $sql = preg_replace('/\s+(AND|OR)\s+(GROUP\s+BY|ORDER\s+BY)/i', ' $2', $sql);
    
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
    
    // Patrón general para cualquier campo en condiciones con paréntesis extra
    $sql = preg_replace('/\)\s*\)\s+and\s+\(([a-zA-Z0-9_.]+)\s*=\s*(\d+|[\'\"][^\'\"]*[\'\"])\)/i', ')) and ($1 = $2)', $sql);
    
    // NUEVA SOLUCIÓN: Limpieza general para filtros "Todos"
    // Esto soluciona el problema de condiciones como campo = ''
    // que se generan cuando el usuario selecciona "Todos" en un combo
    
    // Buscar y eliminar condiciones de igualdad con cadena vacía en cualquier campo
    // Ejemplo: campo = '' AND ... -> (eliminado) AND ...
    
    // Solución general para cualquier campo con valor vacío
    // Caso 1: AND campo = '' -> eliminar esta condición
    $sql = preg_replace('/\s+(AND|OR)\s+([a-zA-Z0-9_.]+)\s*=\s*\'\'(\s+(AND|OR)|$|\))/i', '$3', $sql);
    
    // Caso 2: campo = '' AND -> eliminar campo = ''
    $sql = preg_replace('/(\s+(AND|OR)|\(|^)\s*([a-zA-Z0-9_.]+)\s*=\s*\'\'(\s+(AND|OR)|$|\))/i', '$1$4', $sql);
    
    // Caso 3: WHERE campo = '' -> eliminar esta condición
    $sql = preg_replace('/WHERE\s+([a-zA-Z0-9_.]+)\s*=\s*\'\'(\s+(AND|OR)|$|\))/i', 'WHERE$2', $sql);
    
    // Caso 4: (campo = '') -> reemplazar por (1=1)
    $sql = preg_replace('/\(\s*([a-zA-Z0-9_.]+)\s*=\s*\'\'\s*\)/i', '(1=1)', $sql);
    
    // SOLUCIÓN ESPECIAL: Detectar y eliminar el patrón problemático "and (and )" o variantes
    // Este patrón aparece cuando se seleccionan filtros en orden no secuencial
    $sql = preg_replace('/\s+and\s+\(\s*and\s*\)/i', '', $sql);
    $sql = preg_replace('/\s+AND\s+\(\s*AND\s*\)/i', '', $sql);
    $sql = preg_replace('/\s+and\s+\(\s*and\s+/i', ' and (', $sql);
    $sql = preg_replace('/\s+AND\s+\(\s*AND\s+/i', ' AND (', $sql);
    
    // También eliminar el patrón "and ()" que puede aparecer por las mismas razones
    $sql = preg_replace('/\s+and\s+\(\s*\)/i', '', $sql);
    $sql = preg_replace('/\s+AND\s+\(\s*\)/i', '', $sql);
    
    // NUEVA SOLUCIÓN PARA REPORTE 2 Y SIMILARES:
    // Detectar y eliminar patrones "AND (" o "OR (" sin condición que llevan a ORDER BY
    $sql = preg_replace('/\s+(AND|and|OR|or)\s+\(\s*(ORDER BY|GROUP BY|HAVING|LIMIT)/i', ' $2', $sql);
    
    // Detectar y eliminar cualquier "AND (" o "OR (" incompleto al final
    $sql = preg_replace('/\s+(AND|and|OR|or)\s+\(\s*$/i', '', $sql);
    
    // Aplicar corrección específica para el reporte 2: formato incorrecto con fecha y and
    $sql = preg_replace('/(\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2}:\d{2})[\'"]\)\)+(\s+(and|AND))/i', '$1")$2', $sql);
    
    // Aplicar la nueva función para corregir problemas en la cláusula WHERE
    $sql = fixWhereClauseIssues($sql);
    
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

/**
 * Corrige condiciones con paréntesis desbalanceados antes de ORDER BY
 * Esta función proporciona una solución universal para cualquier tipo de reporte
 * 
 * @param string $sql Consulta SQL a corregir
 * @return string Consulta SQL con paréntesis balanceados correctamente
 */
function fixUnbalancedParenthesisBeforeOrderBy($sql) {
    // Si no hay ORDER BY, no es necesario procesar
    if (stripos($sql, 'ORDER BY') === false) {
        return $sql;
    }
    
    // Dividir la consulta en partes: antes y después de ORDER BY
    // Usar un tercer parámetro en explode para limitar a 2 resultados y prevenir errores
    $parts = explode('ORDER BY', $sql, 2);
    $beforeOrder = $parts[0];
    $afterOrder = isset($parts[1]) ? $parts[1] : "";
    
    // Primera solución: corregir problema con paréntesis extra después de fechas
    // Este patrón corrige el caso "ik.fecha <= "2025/05/06")and" pero de forma general para cualquier campo
    
    // Primero corregir el caso < = con espacio entre medio
    $patronConEspacio = '/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s*<\s+=\s*[\'\"]([^\'\"]+)[\'\"][\)]+/i';
    if (preg_match($patronConEspacio, $beforeOrder, $matches)) {
        $campoTabla = $matches[1]; // Por ejemplo "ik"
        $nombreCampo = $matches[2]; // Por ejemplo "fecha"
        $valorCampo = $matches[3]; // Por ejemplo "2025/05/06"
        
        // Extraer la sección completa
        $patronCompleto = $campoTabla . '.' . $nombreCampo . ' < = "' . $valorCampo . '")';
        $correccion = $campoTabla . '.' . $nombreCampo . ' <= "' . $valorCampo . '"';
        
        // Reemplazar quitando el paréntesis excesivo y corrigiendo el operador
        $beforeOrder = str_replace($patronCompleto, $correccion, $beforeOrder);
        error_log("Corregido paréntesis extra después de campo con operador < = con espacio: $patronCompleto -> $correccion");
    }
    
    // Ahora corregir para cualquier campo con operador <= (sin espacio)
    $patronFechaProblematica = '/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s*<=\s*[\'\"]([^\'\"]+)[\'\"][\)]+/i';
    if (preg_match($patronFechaProblematica, $beforeOrder, $matches)) {
        $campoTabla = $matches[1]; // Por ejemplo "ik"
        $nombreCampo = $matches[2]; // Por ejemplo "fecha"
        $valorCampo = $matches[3]; // Por ejemplo "2025/05/06"
        
        // Extraer la sección completa
        $patronCompleto = $campoTabla . '.' . $nombreCampo . ' <= "' . $valorCampo . '")';
        $correccion = $campoTabla . '.' . $nombreCampo . ' <= "' . $valorCampo . '"';
        
        // Reemplazar quitando el paréntesis excesivo
        $beforeOrder = str_replace($patronCompleto, $correccion, $beforeOrder);
        error_log("Corregido paréntesis extra después de campo con operador <=: $patronCompleto -> $correccion");
    }
    
    // Segunda solución: patrón general para cualquier campo con filtro vacío antes de ORDER BY
    // Este patrón maneja casos donde hay un campo con valor no vacío seguido de un campo con '%'
    $patronGeneral = '/\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*and\s*\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)$/i';
    if (preg_match($patronGeneral, $beforeOrder, $matches)) {
        // Encontrado un campo seguido por filtro vacío
        $campoConValor = $matches[1]; // El primer campo (con valor no vacío)
        $valor = $matches[2]; // El valor del primer campo
        $campoVacio = $matches[3]; // El campo con filtro vacío
        
        // Eliminar la condición con el filtro vacío
        $beforeOrder = preg_replace($patronGeneral, '('.$campoConValor.' = \''.$valor.'\')', $beforeOrder);
        error_log("Aplicada corrección general: eliminada condición $campoVacio = '%' manteniendo $campoConValor = '$valor'");
    }
    
    // SOLUCIÓN UNIVERSAL: Detectar condiciones tipo "campo = valor" sin cerrar antes de ORDER BY
    $patronesBusqueda = [
        // Patrón 1: ')) and (campo = valor ORDER BY' - paréntesis sin cerrar con doble paréntesis previo
        '/\)\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*$/i' => true,
        
        // Patrón 2: ') and (campo = valor ORDER BY' - paréntesis sin cerrar con un paréntesis previo
        '/\)\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*$/i' => true,
        
        // Patrón 3: 'and (campo = valor ORDER BY' - paréntesis sin cerrar sin paréntesis previo
        '/\s+(and|AND|or|OR)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*$/i' => false,
        
        // Patrón general para condición después de paréntesis dobles
        '/\)\)\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*$/i' => true,
        
        // Patrón general para condición anidada con filtro vacío
        '/\s+(and|AND)\s+\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*and\s*\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]%[\'\"]\s*\)$/i' => true
    ];
    
    // Verificar cada patrón y corregir según corresponda
    foreach ($patronesBusqueda as $patron => $tieneParentesisPrevio) {
        if (preg_match($patron, $beforeOrder, $matches)) {
            $operador = $matches[1];
            $campo = isset($matches[2]) ? $matches[2] : 'obd.idbodega';
            $valor = isset($matches[3]) ? $matches[3] : $matches[2];
            
            // CONDICIONES ESPECIALES PARA FILTROS VACÍOS
            $eliminarCondicion = false;
            
            // Si el valor es '%' (filtro no seleccionado), evaluamos eliminar la condición
            if ($valor === '%' || $valor === "''") {
                // Esta lógica evita que se agreguen filtros innecesarios cuando no se selecciona un valor
                // Enfoque general: eliminar cualquier filtro que tenga valor '%' o cadena vacía
                error_log("Se eliminará la condición para filtro vacío: $campo = $valor");
                $eliminarCondicion = true;
            }
            
            // Procesar basado en el tipo de condición
            if ($eliminarCondicion) {
                // Eliminar la condición completa del patrón
                $beforeOrder = substr($beforeOrder, 0, strlen($beforeOrder) - strlen($matches[0]));
                error_log("Eliminada condición para filtro no seleccionado: $campo = $valor");
            } else {
                // Construir la condición correcta con paréntesis balanceados
                $condicionCompleta = $matches[0];
                $condicionCorregida = ($tieneParentesisPrevio ? '' : ' ') . 
                                     "$operador ($campo = '$valor')";
                
                // Reemplazar la condición en la parte antes de ORDER BY
                $beforeOrder = substr($beforeOrder, 0, strlen($beforeOrder) - strlen($condicionCompleta)) . 
                              $condicionCorregida;
                
                error_log("Aplicada corrección universal para condición sin cerrar: $campo = $valor");
            }
        }
    }
    
    // PASO ADICIONAL: Asegurar balance de paréntesis en toda la parte anterior a ORDER BY
    $openCount = substr_count($beforeOrder, '(');
    $closeCount = substr_count($beforeOrder, ')');
    
    // Balancear paréntesis si es necesario
    if ($openCount != $closeCount) {
        if ($openCount > $closeCount) {
            // Faltan paréntesis de cierre
            $beforeOrder .= str_repeat(')', $openCount - $closeCount);
            error_log("Añadidos " . ($openCount - $closeCount) . " paréntesis de cierre faltantes");
        } else {
            // Sobran paréntesis de cierre
            $beforeOrder = rtrim($beforeOrder);
            $exceso = $closeCount - $openCount;
            
            // Eliminar paréntesis excesivos desde el final
            for ($i = 0; $i < $exceso && substr($beforeOrder, -1) === ')'; $i++) {
                $beforeOrder = substr($beforeOrder, 0, -1);
                error_log("Eliminado un paréntesis excesivo");
            }
        }
    }
    
    // Realizar corrección específica para el patrón (le.fechaenvio between "fecha" and "fecha"AND
    // Este patrón es muy común y causa problemas cuando falta un paréntesis de cierre después de la fecha
    
    // Patrones universales para corregir problemas con operadores de fecha (BETWEEN, <=, >=)
    $patronesFechas = [
        // Patrón 1: (campo between "fecha" and "fecha"AND (otra condición) - paréntesis inicial presente
        '/\(([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s+between\s+[\'\"]([^\'\"]+)[\'\"]\s+and\s+[\'\"]([^\'\"]+)[\'\"]\s*(AND|and)\s+\(/i',
        
        // Patrón 2: campo between "fecha" and "fecha"AND (otra condición) - sin paréntesis inicial
        '/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s+between\s+[\'\"]([^\'\"]+)[\'\"]\s+and\s+[\'\"]([^\'\"]+)[\'\"]\s*(AND|and)\s+\(/i',
        
        // Patrón 3: campo <= "fecha")and (otra condición) - paréntesis extra después de fecha
        '/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s*<=\s*[\'\"]([^\'\"]+)[\'\"][\)]+and\s*\(/i',

        // Patrón 4: campo between "fecha" and "fecha"and (caso sin espacio entre fecha y and)
        '/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s+between\s+[\'\"]([^\'\"]+)[\'\"]\s+[Aa][Nn][Dd]\s+[\'\"]([^\'\"]+)[\'\"]([Aa][Nn][Dd])\s*\(/i',
        
        // Patrón 5: And campo between "fecha" And "fecha"and (caso general con 'And' y sin espacio después)
        '/\s+[Aa][Nn][Dd]\s+([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s+between\s+[\'\"]([^\'\"]+)[\'\"]\s+[Aa][Nn][Dd]\s+[\'\"]([^\'\"]+)[\'\"]([Aa][Nn][Dd])\s*\(/i',
        
        // Patrón 6: between "fecha" And "fecha"and - sin paréntesis y sin espacio
        '/between\s+[\'\"]([^\'\"]+)[\'\"]\s+[Aa][Nn][Dd]\s+[\'\"]([^\'\"]+)[\'\"]([Aa][Nn][Dd])/i',
        
        // Patrón 7: fecha BETWEEN "fecha1" AND "fecha2")and - paréntesis extra después del between
        '/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s+[Bb][Ee][Tt][Ww][Ee][Ee][Nn]\s+[\'\"]([^\'\"]+)[\'\"]\s+[Aa][Nn][Dd]\s+[\'\"]([^\'\"]+)[\'\"][\)]+([Aa][Nn][Dd])/i'
    ];
    
    foreach ($patronesFechas as $index => $patronFecha) {
        if (preg_match($patronFecha, $beforeOrder, $matches)) {
            // Manejo específico según el tipo de patrón
            switch ($index) {
                case 2: // Patrón 3: fecha <= "valor")and - paréntesis extra después de fecha
                    $campo = $matches[1] . '.' . $matches[2];
                    $fecha = $matches[3];
                    
                    // Detectar el patrón exacto con paréntesis sobrante
                    $patronProblematico = $campo . ' <= "' . $fecha . '")and (';
                    $patronCorregido = $campo . ' <= "' . $fecha . '") and (';
                    
                    // Aplicar la corrección específica para este caso
                    $beforeOrder = str_replace($patronProblematico, $patronCorregido, $beforeOrder);
                    error_log("Corregido paréntesis extra después de fecha con operador <=");
                    break;
                    
                case 3: // Patrón 4: campo between "fecha" and "fecha"and - sin espacio después de fecha
                case 4: // Patrón 5: And campo between "fecha" And "fecha"and - sin espacio después de fecha
                    $campo = $matches[1] . '.' . $matches[2];
                    $fechaInicio = $matches[3];
                    $fechaFin = $matches[4];
                    $conectorDespues = $matches[5]; // "and" sin espacio después de la fecha
                    
                    // Crear patrones para reemplazo
                    $patronProblematico = $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '"' . $conectorDespues;
                    $patronCorregido = $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '") ' . $conectorDespues;
                    
                    // Aplicar corrección añadiendo paréntesis y espacio
                    $beforeOrder = str_replace($patronProblematico, $patronCorregido, $beforeOrder);
                    error_log("Corregido problema de fecha sin paréntesis ni espacio: $patronProblematico -> $patronCorregido");
                    break;
                    
                case 5: // Patrón 6: between "fecha" And "fecha"and - caso general
                    $fechaInicio = $matches[1];
                    $fechaFin = $matches[2];
                    $conectorDespues = $matches[3]; // "and" sin espacio después
                    
                    // Corregir simplemente añadiendo un espacio después de la fecha
                    $patronProblematico = 'between "' . $fechaInicio . '" and "' . $fechaFin . '"' . $conectorDespues;
                    $patronCorregido = 'between "' . $fechaInicio . '" and "' . $fechaFin . '") ' . $conectorDespues;
                    
                    // Aplicar corrección
                    $beforeOrder = str_replace($patronProblematico, $patronCorregido, $beforeOrder);
                    error_log("Corregido formato general de between sin paréntesis ni espacio");
                    break;
                    
                case 6: // Patrón 7: fecha BETWEEN "fecha1" AND "fecha2")and - paréntesis extra después del between
                    $campo = $matches[1] . '.' . $matches[2];
                    $fechaInicio = $matches[3];
                    $fechaFin = $matches[4];
                    $conectorDespues = $matches[5]; // "and" después del paréntesis
                    
                    // Crear patrones para reemplazo - quitar paréntesis extra
                    $patronProblematico = $campo . ' BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '")' . $conectorDespues;
                    $patronCorregido = $campo . ' BETWEEN "' . $fechaInicio . '" AND "' . $fechaFin . '") ' . $conectorDespues;
                    
                    // Aplicar corrección
                    $beforeOrder = str_replace($patronProblematico, $patronCorregido, $beforeOrder);
                    error_log("Corregido problema de paréntesis extra después de BETWEEN: $patronProblematico -> $patronCorregido");
                    break;
                    
                default: // Patrones 0 y 1 (normales de BETWEEN)
                    // Procesar patrones BETWEEN estándar
                    $campo = $matches[1] . '.' . $matches[2];
                    $fechaInicio = $matches[3];
                    $fechaFin = $matches[4];
                    $conector = $matches[5];
                    
                    // Determinar si la condición ya tiene paréntesis al inicio
                    $tieneParentesisInicio = strpos($matches[0], '(' . $campo) === 0;
                    
                    // Construir los patrones según si ya tiene paréntesis de apertura o no
                    if ($tieneParentesisInicio) {
                        // Patrón completo a buscar - ya tiene paréntesis de apertura
                        $patronFechaIncompleto = '(' . $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '"' . $conector;
                        
                        // Reemplazar por el patrón correcto (con paréntesis de cierre)
                        $patronFechaCorrecto = '(' . $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '") ' . $conector;
                    } else {
                        // Patrón completo a buscar - sin paréntesis de apertura
                        $patronFechaIncompleto = $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '"' . $conector;
                        
                        // Reemplazar por el patrón correcto (con paréntesis completos)
                        $patronFechaCorrecto = '(' . $campo . ' between "' . $fechaInicio . '" and "' . $fechaFin . '") ' . $conector;
                    }
                    
                    // Aplicar corrección
                    $beforeOrder = str_replace($patronFechaIncompleto, $patronFechaCorrecto, $beforeOrder);
                    error_log("Corregida condición de fecha sin paréntesis de cierre: $patronFechaIncompleto");
                    break;
            }
        }
    }
    
    // Solución general para cualquier campo con BETWEEN y problemas de paréntesis
    if (stripos($beforeOrder, ' between') !== false && 
        preg_match('/\.([a-zA-Z0-9_]+)\s+between\s+[\'\"]([^\'\"]+)[\'\"]\s+and\s+[\'\"]([^\'\"]+)[\'\"](AND|and|\s+AND|\s+and)/i', $beforeOrder, $matches)) {
        
        $textoFecha = $matches[0];
        $fechaInicio = $matches[1];
        $fechaFin = $matches[2];
        $conector = isset($matches[3]) ? $matches[3] : '';
        
        // Verificar si hay una condición AND después sin paréntesis cerrado
        $posFecha = strpos($beforeOrder, $textoFecha);
        if ($posFecha !== false) {
            $parteAnterior = substr($beforeOrder, 0, $posFecha);
            $ultimoParentesis = strrpos($parteAnterior, '(');
            if ($ultimoParentesis !== false) {
                // Buscar si este paréntesis está cerrado
                $segmentoAVerificar = substr($beforeOrder, $ultimoParentesis, $posFecha - $ultimoParentesis + strlen($textoFecha));
                $openCount = substr_count($segmentoAVerificar, '(');
                $closeCount = substr_count($segmentoAVerificar, ')');
                
                if ($openCount > $closeCount) {
                    // Falta cerrar paréntesis, reconstruir esta sección específica
                    $nuevoFechaBetween = '(' . substr($segmentoAVerificar, 1) . ') ' . $conector;
                    $beforeOrder = str_replace($segmentoAVerificar . $conector, $nuevoFechaBetween, $beforeOrder);
                    error_log("Reconstruida condición BETWEEN para balancear paréntesis");
                }
            }
        }
    }
    
    // Reconstruir la consulta completa
    $fixedSql = trim($beforeOrder) . ' ORDER BY ' . $afterOrder;
    
    // Corrección final: si hay un patrón ORDER BY al final sin nada después, asegurarse de que haya un espacio
    if (substr($fixedSql, -9) === 'ORDER BY') {
        $fixedSql .= ' ';
    }
    
    // SOLUCIÓN ESPECÍFICA PARA fecha < = "..." Y PARÉNTESIS EXTRA ANTES DE ORDER BY
    if (preg_match('/([a-zA-Z0-9_.]+)\.([a-zA-Z0-9_]+)\s*<\s*=\s*[\'\"]([^\'\"]+)[\'\"][\)]+([Aa][Nn][Dd])\s*\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]/i', $beforeOrder, $matches)) {
        $campoFecha = $matches[1] . '.' . $matches[2];
        $valorFecha = $matches[3];
        $conector = $matches[4]; // "and"
        $campoFiltro = $matches[5]; // "ob.idbodega"
        $valorFiltro = $matches[6]; // "9"
        
        $patronProblematico = $campoFecha . ' < = "' . $valorFecha . '")' . $conector . ' (' . $campoFiltro . ' = \'' . $valorFiltro . '\'';
        $patronCorregido = $campoFecha . ' <= "' . $valorFecha . '") ' . $conector . ' (' . $campoFiltro . ' = \'' . $valorFiltro . '\'';
        
        // Aplicar corrección específica para este caso
        $hayReemplazo = str_replace($patronProblematico, $patronCorregido, $beforeOrder, $count);
        if ($count > 0) {
            $beforeOrder = $hayReemplazo;
            error_log("Corregido problema específico de fecha con < = y paréntesis extra: $patronProblematico -> $patronCorregido");
        } else {
            // Intentar otra variante del patrón
            $patronProblematico2 = $campoFecha . ' <= "' . $valorFecha . '")' . $conector . ' (' . $campoFiltro . ' = \'' . $valorFiltro . '\'';
            $patronCorregido2 = $campoFecha . ' <= "' . $valorFecha . '") ' . $conector . ' (' . $campoFiltro . ' = \'' . $valorFiltro . '\'';
            
            $hayReemplazo2 = str_replace($patronProblematico2, $patronCorregido2, $beforeOrder, $count);
            if ($count > 0) {
                $beforeOrder = $hayReemplazo2;
                error_log("Corregido problema específico de fecha con <= y paréntesis extra (variante 2)");
            }
        }
    }
    
    // Realizar una última verificación de paréntesis antes del ORDER BY
    $openCount = substr_count($beforeOrder, '(');
    $closeCount = substr_count($beforeOrder, ')');
    
    // Si hay más paréntesis cerrados que abiertos, intentamos arreglarlo
    if ($closeCount > $openCount) {
        error_log("Encontrada condición sin cerrar: $beforeOrder");
        error_log("Balance de paréntesis: $openCount abiertos, $closeCount cerrados");
        
        // Eliminar el exceso de paréntesis de cierre
        // CRÍTICO: Preservar comillas que vienen antes del paréntesis
        // Estrategia: Encontrar TODAS las posiciones de ) de derecha a izquierda,
        // marcar cuáles tienen comilla antes, eliminar solo las que NO tienen comilla
        $diff = $closeCount - $openCount;
        $removed = 0;
        $tempSql = $beforeOrder;
        
        error_log("PARENTHESIS BALANCING: Necesito eliminar $diff paréntesis");
        error_log("BEFORE SQL (last 100 chars): " . substr($tempSql, -100));
        
        // Encontrar posiciones de ) sin comilla antes, de derecha a izquierda
        while ($removed < $diff && $tempSql !== '') {
            $len = strlen($tempSql);
            $found = false;
            
            // Buscar de derecha a izquierda el primer ) que NO tenga comilla ni valor antes
            for ($i = $len - 1; $i >= 0; $i--) {
                if ($tempSql[$i] === ')') {
                    // Encontramos un )
                    // Verificar si tiene comilla justo antes
                    if ($i > 0 && ($tempSql[$i-1] === '"' || $tempSql[$i-1] === "'")) {
                        // Tiene comilla, SALTAR este paréntesis, continuar buscando
                        error_log("SKIP ) at position $i - has quote before: " . substr($tempSql, max(0, $i-5), 7));
                        continue;
                    }
                    // NUEVO: Verificar si viene después de un valor (número o letra) - indicaría un filtro con valor
                    // Patrón: (campo = valor) donde valor puede ser número o texto sin comillas
                    elseif ($i > 0 && preg_match('/[a-zA-Z0-9]$/', $tempSql[$i-1])) {
                        // Viene después de número o letra, podría ser un filtro con valor
                        // Verificar si hay un patrón = antes para confirmar
                        $context = substr($tempSql, max(0, $i-20), 20);
                        if (preg_match('/=\s*[a-zA-Z0-9]+$/', $context)) {
                            // Es un filtro con valor, SALTAR este paréntesis
                            error_log("SKIP ) at position $i - closes filter value: " . substr($tempSql, max(0, $i-10), 12));
                            continue;
                        }
                    }
                    
                    // NO tiene comilla ni es cierre de filtro, ELIMINAR este paréntesis
                    error_log("REMOVE ) at position $i - no protection: " . substr($tempSql, max(0, $i-5), 7));
                    $tempSql = substr($tempSql, 0, $i) . substr($tempSql, $i + 1);
                    $removed++;
                    $found = true;
                    break;
                }
            }
            
            // Si no encontramos ningún ) sin comilla, terminar
            if (!$found) {
                error_log("NO MORE ) without quote found. Removed $removed of $diff needed");
                break;
            }
        }
        
        error_log("AFTER SQL (last 100 chars): " . substr($tempSql, -100));
        error_log("Successfully removed $removed parentheses");
        
        $beforeOrder = $tempSql;
        error_log("Corregida condición sin cerrar");
        error_log("Eliminado un paréntesis excesivo preservando comillas");
    }
    
    // SOLUCIÓN PARA "AND" COLGANTE AL FINAL DE LA CLÁUSULA WHERE
    // Este caso ocurre cuando hay un "and" sin condición después, justo antes de ORDER BY
    // Ejemplo: "... and (of.idfabricante = '10') and ORDER BY ..."
    
    // Patrón 1: Búsqueda de "and" o "AND" al final antes de reconstruir la consulta
    if (preg_match('/\s+(and|AND)\s*$/i', $beforeOrder)) {
        $beforeOrder = preg_replace('/\s+(and|AND)\s*$/i', '', $beforeOrder);
        error_log("Eliminado AND colgante al final de la cláusula WHERE");
    }
    
    // Patrón 2: Caso específico "filtro = valor) and ORDER BY"
    $patronAndColgante = '/\(([a-zA-Z0-9_.]+)\s*=\s*[\'\"]([^\'\"]+)[\'\"]\s*\)\s+(and|AND)\s*$/i';
    if (preg_match($patronAndColgante, $beforeOrder, $matches)) {
        $campo = $matches[1];
        $valor = $matches[2];
        $operador = $matches[3]; // "and" o "AND"
        
        $patronProblematico = '(' . $campo . ' = \'' . $valor . '\') ' . $operador;
        $patronCorregido = '(' . $campo . ' = \'' . $valor . '\')';
        
        $beforeOrder = str_replace($patronProblematico, $patronCorregido, $beforeOrder);
        error_log("Eliminado AND colgante después de filtro: $patronProblematico -> $patronCorregido");
    }
    
    // Reconstruir la consulta nuevamente con la parte corregida
    $fixedSql = trim($beforeOrder) . ' ORDER BY ' . $afterOrder;
    
    // CORRECCIÓN FINAL CRÍTICA: Restaurar comillas de cierre faltantes en fechas
    // Patrón: "YYYY/MM/DD HH:MM:SS) -> "YYYY/MM/DD HH:MM:SS")
    // Buscar fechas con comilla de apertura pero sin comilla de cierre antes del paréntesis
    $fixedSql = preg_replace('/(\d{4}\/\d{2}\/\d{2}\s+\d{2}:\d{2}:\d{2})(\))/i', '$1"$2', $fixedSql);
    
    error_log("Balance de paréntesis: $openCount abiertos, $closeCount cerrados");
    error_log("SQL reconstruido completamente: $fixedSql");
    
    return $fixedSql;
}

/**
 * Limpieza específica para condiciones IN malformadas en multiselección
 * Corrige patrones como: IN (9","10","2"" ) -> IN ("9","10","2")
 * 
 * @param string $sql Consulta SQL con posibles condiciones IN malformadas
 * @return string Consulta SQL con condiciones IN corregidas
 */
function cleanMultiselectionInConditions($sql) {
    $originalSql = $sql;
    
    // SOLUCIÓN UNIVERSAL: Detectar y corregir CUALQUIER patrón IN malformado
    // Patrón 1: IN (valor1","valor2","valorN"" ) -> IN ("valor1","valor2","valorN")
    // Funciona con cualquier tipo de valor: números, texto, fechas, etc.
    $sql = preg_replace_callback('/IN\s*\(\s*([^"\'()]+(?:","[^"\'()]+)*)""\s*\)/i', function($matches) {
        $values = explode('","', $matches[1]);
        $cleanValues = array_map('trim', $values);
        return 'IN ("' . implode('","', $cleanValues) . '")';
    }, $sql);
    
    // Patrón 2: IN (valor1","valor2" ) -> IN ("valor1","valor2") - Sin doble comilla al final
    $sql = preg_replace_callback('/IN\s*\(\s*([^"\'()]+(?:","[^"\'()]+)*)"\s*\)/i', function($matches) {
        $values = explode('","', $matches[1]);
        $cleanValues = array_map('trim', $values);
        // Si ya tiene comilla al inicio, mantenerla
        if (substr($matches[1], 0, 1) === '"') {
            return 'IN (' . $matches[1] . ')';
        } else {
            return 'IN ("' . implode('","', $cleanValues) . '")';
        }
    }, $sql);
    
    // Patrón 3: IN (valor", valor2", valorN" ) -> IN ("valor","valor2","valorN")
    $sql = preg_replace_callback('/IN\s*\(\s*([^"\'()]+(?:",[^"\'()]+)*"\s*)\)/i', function($matches) {
        $content = trim($matches[1]);
        $values = explode('",', $content);
        $cleanValues = array_map(function($v) {
            return '"' . trim(str_replace('"', '', $v)) . '"';
        }, $values);
        return 'IN (' . implode(',', $cleanValues) . ')';
    }, $sql);
    
    // CORRECCIÓN ESPECIAL: Si el patrón está entre comillas externas
    $sql = preg_replace('/IN\s+"\s*\(([^"]+)\)\s*"/i', 'IN ($1)', $sql);
    
    // LIMPIEZA FINAL: Asegurar formato consistente de espacios
    $sql = preg_replace('/IN\s+\(/i', 'IN (', $sql);
    $sql = preg_replace('/,\s*"/i', ',"', $sql);
    $sql = preg_replace('/"\s*,/i', '",', $sql);
    
    // Log solo si hubo cambios
    if ($sql !== $originalSql) {
        error_log("Aplicada limpieza universal de multiselección IN: corregidos patrones malformados para cualquier tipo de valor");
    }
    
    return $sql;
}

/**
 * Elimina TODOS los patrones [@...] no resueltos del SQL final
 * Esta función se debe llamar justo antes de mostrar o ejecutar el SQL
 * para asegurar que no queden patrones sin procesar
 * 
 * @param string $sql SQL query con posibles patrones no resueltos
 * @return string SQL limpio sin patrones [@...]
 */
function removeAllUnresolvedPatterns($sql) {
    // PROTECCIÓN: Si el SQL viene del sistema de 3 fases, NO aplicar eliminaciones destructivas
    if (isset($_SESSION['usar_sistema_tres_fases']) && $_SESSION['usar_sistema_tres_fases'] === true) {
        error_log("removeAllUnresolvedPatterns: SQL viene del sistema de 3 fases - aplicando solo eliminaciones mínimas de patrones no resueltos");
        // Solo eliminar patrones [@...] reales no resueltos, sin tocar paréntesis
        $sql = preg_replace('/\[@[^\]]*\]/', '', $sql);
        return $sql;
    }
    
    $originalSql = $sql;
    
    // Patrón que detecta cualquier [@...] sin importar su contenido o ubicación
    // CRÍTICO: Incluir las comillas que rodean el patrón para eliminarlas también
    // Esto evita dejar comillas vacías que afecten a valores posteriores
    
    // Primero eliminar patrones CON comillas balanceadas: "[@...]" o '[@...]'
    // CRÍTICO: Preservar comilla de cierre cuando está después del patrón (caso: "valor") con patrón entre valor y ))
    $patternDoubleQuotes = '/"[@[^\]]*]"(["\']?)/';  // Captura comilla opcional después del cierre
    $patternSingleQuotes = "/'[@[^\]]*]'([\"']?)/";  // Captura comilla opcional después del cierre
    
    if (preg_match_all($patternDoubleQuotes, $sql, $matches)) {
        foreach ($matches[0] as $unresolvedPattern) {
            error_log("Eliminando patrón con comillas dobles: " . substr($unresolvedPattern, 0, 50) . "...");
        }
        // Preservar la comilla capturada en $1
        $sql = preg_replace($patternDoubleQuotes, '$1', $sql);
    }
    
    if (preg_match_all($patternSingleQuotes, $sql, $matches)) {
        foreach ($matches[0] as $unresolvedPattern) {
            error_log("Eliminando patrón con comillas simples: " . substr($unresolvedPattern, 0, 50) . "...");
        }
        // Preservar la comilla capturada en $1
        $sql = preg_replace($patternSingleQuotes, '$1', $sql);
    }
    
    // Finalmente eliminar patrones SIN comillas (si quedó alguno)
    // CRÍTICO: También preservar comilla si viene después del patrón
    $patternNoQuotes = '/\[@[^\]]*\](["\']?)/';
    if (preg_match_all($patternNoQuotes, $sql, $matches)) {
        foreach ($matches[0] as $unresolvedPattern) {
            error_log("Eliminando patrón sin comillas: " . substr($unresolvedPattern, 0, 50) . "...");
        }
        // Preservar la comilla capturada en $1
        $sql = preg_replace($patternNoQuotes, '$1', $sql);
    }
    
    // NUEVO: Eliminar residuos de patrones parcialmente removidos
    // Estos residuos aparecen cuando un patrón @Multiselection queda sin resolver
    // Ejemplo: " ORDER BY des;@Multiselection])" después de procesar otros filtros
    
    // Patrón 1: Eliminar fragmentos "group by [campos] order by [campo]]" residuales
    // Estos quedan cuando se eliminan cláusulas con patrones complejos
    $groupByResidue = '/\s+group\s+by\s+[a-zA-Z0-9_,\s]+\s+order\s+by\s+[a-zA-Z0-9_]+\s*\]*/i';
    if (preg_match_all($groupByResidue, $sql, $matches)) {
        foreach ($matches[0] as $residue) {
            error_log("Eliminando residuo GROUP BY...ORDER BY: " . substr($residue, 0, 60) . "...");
        }
        $sql = preg_replace($groupByResidue, ' ', $sql);
    }
    
    // Patrón 2: Eliminar " ORDER BY campo;@Multiselection])" o variantes
    $residuePattern = '/\s+order\s+by\s+[a-zA-Z0-9_]+\s*;@Multiselection\]\)?/i';
    if (preg_match_all($residuePattern, $sql, $matches)) {
        foreach ($matches[0] as $residue) {
            error_log("Eliminando residuo ORDER BY;@Multiselection: " . substr($residue, 0, 50) . "...");
        }
        $sql = preg_replace($residuePattern, ' ', $sql);
    }
    
    // Patrón 3: Eliminar solo ";@Multiselection])" o "@Multiselection]" residuales
    $simpleResiduePattern = '/\s*;?@Multiselection\]\)?/';
    if (preg_match($simpleResiduePattern, $sql)) {
        error_log("Eliminando residuo @Multiselection");
        $sql = preg_replace($simpleResiduePattern, '', $sql);
    }
    
    // Patrón 4: Eliminar corchetes ] sueltos que pueden quedar
    $sql = preg_replace('/\s*\]\s*(?=(ORDER|GROUP|WHERE|AND|OR|;|\s*$))/i', ' ', $sql);
    
    // CRÍTICO: Eliminar paréntesis extras con comillas que quedan después de eliminar múltiples cláusulas
    // Ejemplo 1: ik.fecha <= "2025/10/12 23:59:59" ) ") and -> ik.fecha <= "2025/10/12 23:59:59" and
    // Ejemplo 2: ik.fecha <= "2025/10/12 23:59:59" ") ORDER BY -> ik.fecha <= "2025/10/12 23:59:59" ORDER BY
    $sql = preg_replace('/\s*\)\s*["\']?\s*\)\s+(and|or)\s+/i', ' $1 ', $sql);
    $sql = preg_replace('/\s*["\']?\s*\)\s+(ORDER\s+BY|GROUP\s+BY)/i', ' $1', $sql);
    
    // Limpiar condiciones vacías que pueden quedar después de eliminar los patrones
    // Ejemplo: "and (campo IN "")" -> se elimina
    $sql = preg_replace('/\s+(and|AND|or|OR)\s+\([^)]*\s+(IN|=|LIKE)\s*["\']["\']?\s*\)/i', '', $sql);
    
    // CRÍTICO: Eliminar cláusulas IN vacías cuando no se seleccionó ningún valor en filtros multiselección
    // Ejemplos: "and (ob.idbodega IN )" -> se elimina completamente
    //          "OR (campo IN )" -> se elimina
    // MEJORA: Preservar comilla de cierre si hay una antes del patrón eliminado
    // Ejemplo: ik.fecha <= "2025/10/10 23:59:59") and (campo IN ) -> preservar la " antes de )
    $sql = preg_replace_callback('/(["\']?)(\))\s+(and|or)\s+\(\s*[a-zA-Z0-9_.]+\s+IN\s+\)/i', function($matches) {
        // Preservar la comilla si existe
        return $matches[1] . $matches[2];
    }, $sql);
    
    // También eliminar variaciones sin paréntesis externos: "and campo IN ()"
    $sql = preg_replace('/\s+(and|or)\s+[a-zA-Z0-9_.]+\s+IN\s+\(\s*\)/i', '', $sql);
    
    // Limpiar paréntesis vacíos
    $sql = preg_replace('/\(\s*\)/', '', $sql);
    
    // Limpiar AND/OR colgantes
    $sql = preg_replace('/\s+(and|AND|or|OR)\s+(ORDER\s+BY|GROUP\s+BY|HAVING|\))/i', ' $2', $sql);
    $sql = preg_replace('/\s+(and|AND|or|OR)\s*$/i', '', $sql);
    
    // Limpiar espacios múltiples
    $sql = preg_replace('/\s+/', ' ', $sql);
    $sql = trim($sql);
    
    // CRÍTICO: Verificar y corregir comillas desbalanceadas después de todas las limpiezas
    // Patrón: detectar valores con comilla de apertura pero sin cierre antes de paréntesis y palabras clave SQL
    // Ejemplo: (campo = "10) ORDER BY -> (campo = "10") ORDER BY
    
    // Caso 1: Comilla sin cerrar seguida de ) y luego palabra clave SQL
    $sql = preg_replace('/([a-zA-Z0-9_.]+\s*=\s*")([^"]+)(\))\s+(ORDER\s+BY|GROUP\s+BY|HAVING|AND|OR|;)/i', '$1$2"$3 $4', $sql);
    
    // Caso 2: Comilla sin cerrar seguida directamente de palabra clave SQL (sin paréntesis)
    $sql = preg_replace('/([a-zA-Z0-9_.]+\s*=\s*")([^"]+)\s+(ORDER\s+BY|GROUP\s+BY|HAVING|AND|OR|;)/i', '$1$2" $3', $sql);
    
    // También para comillas simples - Caso 1
    $sql = preg_replace('/([a-zA-Z0-9_.]+\s*=\s*\')([^\']+)(\))\s+(ORDER\s+BY|GROUP\s+BY|HAVING|AND|OR|;)/i', '$1$2\'$3 $4', $sql);
    
    // También para comillas simples - Caso 2
    $sql = preg_replace('/([a-zA-Z0-9_.]+\s*=\s*\')([^\']+)\s+(ORDER\s+BY|GROUP\s+BY|HAVING|AND|OR|;)/i', '$1$2\' $3', $sql);
    
    // Log solo si hubo cambios
    if ($sql !== $originalSql) {
        error_log("Se eliminaron patrones no resueltos del SQL para mostrar/ejecutar correctamente");
    }
    
    return $sql;
}

/**
 * ============================================================================
 * NUEVO SISTEMA DE 3 FASES PARA PROCESAMIENTO DE FILTROS
 * Compatible con PHP 5.5.9 y MySQL 5.5.62
 * ============================================================================
 */

/**
 * FASE 1: PARSEO - Extrae definiciones estructuradas de filtros del WHERE
 * 
 * Analiza el WHERE clause y extrae cada condición de filtro con sus límites exactos,
 * creando una estructura de datos que permite manipulación precisa sin regex heurísticos
 * 
 * @param string $whereClause Cláusula WHERE original con patrones [@...]
 * @return array Array de FilterDefinition con estructura:
 *   [
 *     'type' => 'combo'|'fixed',  // tipo de cláusula
 *     'pattern' => '[@...]',       // patrón a buscar/reemplazar
 *     'clause' => 'and (campo = pattern)', // cláusula SQL completa
 *     'connector' => 'and'|'or',   // conector lógico
 *     'field' => 'campo',          // nombre del campo SQL
 *     'operator' => '='|'IN',      // operador SQL
 *     'is_multiselection' => bool, // si es IN clause
 *     'label' => 'Nombre',         // label del filtro
 *     'start_pos' => int,          // posición inicio en WHERE original
 *     'end_pos' => int             // posición fin en WHERE original
 *   ]
 */
function extractFilterDefinitions($whereClause) {
    $definitions = array();
    
    // Log para debugging
    error_log("======= FASE 1: EXTRACCIÓN DE FILTER DEFINITIONS =======");
    error_log("WHERE CLAUSE ORIGINAL: " . substr($whereClause, 0, 200) . "...");
    
    // 1. Extraer patrones combo [@...] usando el parser de brackets balanceados
    $comboPatterns = extractBalancedPatterns($whereClause);
    
    foreach ($comboPatterns as $pattern) {
        $fullPattern = $pattern[0];  // [@...]
        $label = $pattern[1];
        $sqlPart = $pattern[4];
        
        // Determinar si es multiselección
        $isMultiselection = (stripos($sqlPart, ';@Multiselection') !== false);
        
        // Buscar la cláusula completa que contiene este patrón
        // Formato típico: and (campo = 'patrón') o and (campo IN patrón)
        $clause = extractCompleteClause($whereClause, $fullPattern);
        
        if ($clause) {
            // Extraer el conector (and/or) y el campo
            if (preg_match('/(and|or)\s*\(\s*([a-zA-Z0-9_.]+)\s*(=|IN)\s*/i', $clause['text'], $match)) {
                $connector = strtolower($match[1]);
                $field = $match[2];
                $operator = strtoupper($match[3]);
                
                $definitions[] = array(
                    'type' => 'combo',
                    'pattern' => $fullPattern,
                    'clause' => $clause['text'],
                    'connector' => $connector,
                    'field' => $field,
                    'operator' => $operator,
                    'is_multiselection' => $isMultiselection,
                    'label' => $label,
                    'start_pos' => $clause['start'],
                    'end_pos' => $clause['end']
                );
                
                error_log("Combo Filter extraído: Label='$label', Operator=$operator, Multi=" . ($isMultiselection ? 'YES' : 'NO'));
            }
        }
    }
    
    // NOTA: Los patrones de fecha [#...] ya NO se extraen aquí
    // Se reemplazan ANTES del sistema de 3 fases en buildSqlQueryThreePhase
    // para evitar tratarlos como filtros opcionales
    
    // 3. Extraer patrones de texto [FilterName] (que no sean #, @, !)
    if (preg_match_all('/\[([^\]#@!]+)\]/i', $whereClause, $textMatches, PREG_OFFSET_CAPTURE)) {
        foreach ($textMatches[0] as $idx => $match) {
            $fullPattern = $match[0];
            $label = $textMatches[1][$idx][0];
            
            $clause = extractCompleteClause($whereClause, $fullPattern);
            if ($clause) {
                if (preg_match('/(and|or)\s*\(\s*([a-zA-Z0-9_.]+)\s+(LIKE|=)\s*/i', $clause['text'], $m)) {
                    $definitions[] = array(
                        'type' => 'text',
                        'pattern' => $fullPattern,
                        'clause' => $clause['text'],
                        'connector' => strtolower($m[1]),
                        'field' => $m[2],
                        'operator' => strtoupper($m[3]),
                        'is_multiselection' => false,
                        'label' => $label,
                        'start_pos' => $clause['start'],
                        'end_pos' => $clause['end']
                    );
                    error_log("Text Filter extraído: Label='$label'");
                }
            }
        }
    }
    
    // 4. Identificar cláusulas fijas (sin patrones) que siempre deben estar presentes
    $fixedClauses = extractFixedClauses($whereClause, $definitions);
    foreach ($fixedClauses as $fixed) {
        $definitions[] = $fixed;
    }
    
    // 5. También extraer variables de sesión [!...] para reemplazo directo (no son filtros)
    // Las variables de sesión se reemplazan antes del procesamiento de 3 fases
    
    // Ordenar por posición para mantener el orden original
    usort($definitions, function($a, $b) {
        return $a['start_pos'] - $b['start_pos'];
    });
    
    error_log("Total Filter Definitions extraídos: " . count($definitions));
    
    return $definitions;
}

/**
 * Extrae la cláusula SQL completa que contiene un patrón
 * Busca hacia atrás para encontrar el 'and' o 'or', y hacia adelante hasta el siguiente 'and'/'or' o final
 * 
 * @param string $whereClause WHERE clause completo
 * @param string $pattern Patrón a buscar
 * @return array|null Array con 'text', 'start', 'end' o null si no se encuentra
 */
function extractCompleteClause($whereClause, $pattern) {
    $patternPos = strpos($whereClause, $pattern);
    if ($patternPos === false) {
        return null;
    }
    
    // Buscar hacia atrás para encontrar el inicio de la cláusula (and/or o inicio del WHERE)
    $start = 0;
    $beforePattern = substr($whereClause, 0, $patternPos);
    
    // Buscar el último 'and' o 'or' antes del patrón
    if (preg_match_all('/(and|or)\s+\(/i', $beforePattern, $matches, PREG_OFFSET_CAPTURE)) {
        $lastMatch = end($matches[0]);
        $start = $lastMatch[1]; // posición del último and/or
    }
    
    // Buscar hacia adelante para encontrar el final de la cláusula
    // El final es: el siguiente 'and'/'or' al mismo nivel, o el final del WHERE
    $afterPatternStart = $patternPos + strlen($pattern);
    $afterPattern = substr($whereClause, $afterPatternStart);
    
    // Buscar el cierre de paréntesis que balancea la cláusula
    $end = strlen($whereClause);
    $level = 0;
    $inClause = false;
    
    for ($i = $start; $i < strlen($whereClause); $i++) {
        $char = $whereClause[$i];
        
        if ($char == '(') {
            $level++;
            $inClause = true;
        } elseif ($char == ')') {
            $level--;
            if ($inClause && $level == 0) {
                // Encontramos el cierre de la cláusula
                $end = $i + 1;
                
                // Verificar si hay más contenido después que pertenece a esta cláusula
                // Ejemplo: patrón con GROUP BY, ORDER BY fuera del paréntesis
                $remaining = substr($whereClause, $end);
                if (preg_match('/^\s*(group\s+by|order\s+by)[^\]]*\]/i', $remaining, $match)) {
                    $end += strlen($match[0]);
                }
                
                break;
            }
        }
    }
    
    $clauseText = substr($whereClause, $start, $end - $start);
    
    return array(
        'text' => $clauseText,
        'start' => $start,
        'end' => $end
    );
}

/**
 * Extrae cláusulas fijas (sin patrones de filtro) del WHERE
 * Estas son condiciones que siempre deben estar presentes
 * Usa balance de paréntesis para manejar subconsultas complejas
 * 
 * @param string $whereClause WHERE clause original
 * @param array $filterDefs Definiciones de filtro ya extraídas
 * @return array Array de cláusulas fijas
 */
function extractFixedClauses($whereClause, $filterDefs) {
    $fixedClauses = array();
    
    // Crear un mapa de posiciones ocupadas por filtros
    $occupiedRanges = array();
    foreach ($filterDefs as $def) {
        $occupiedRanges[] = array($def['start_pos'], $def['end_pos']);
    }
    
    // Buscar conectores AND/OR opcionalmente seguidos de NOT EXISTS y luego paréntesis
    // Maneja casos como: AND (...), OR (...), AND NOT EXISTS (...), OR NOT EXISTS (...)
    $offset = 0;
    while (preg_match('/(and|or)\s+(?:not\s+)?(?:exists\s+)?\(/i', $whereClause, $match, PREG_OFFSET_CAPTURE, $offset)) {
        // Extraer el conector (AND/OR) del match
        preg_match('/(and|or)/i', $match[0][0], $connectorMatch);
        $connector = $connectorMatch[1];
        $clauseStart = $match[0][1];
        $parenStart = $match[0][1] + strlen($match[0][0]) - 1; // posición del '('
        
        // Usar stack para encontrar el paréntesis de cierre balanceado
        $parenStack = 1;
        $pos = $parenStart + 1;
        $clauseEnd = $parenStart + 1;
        
        while ($pos < strlen($whereClause) && $parenStack > 0) {
            if ($whereClause[$pos] == '(') {
                $parenStack++;
            } elseif ($whereClause[$pos] == ')') {
                $parenStack--;
                if ($parenStack == 0) {
                    $clauseEnd = $pos + 1; // incluir el ')'
                    break;
                }
            }
            $pos++;
        }
        
        // Extraer el texto completo de la cláusula
        $clauseText = substr($whereClause, $clauseStart, $clauseEnd - $clauseStart);
        
        // Verificar si esta posición está ocupada por un filtro
        $isOccupied = false;
        foreach ($occupiedRanges as $range) {
            // Verificar si hay overlap con ranges ocupados
            if (!(($clauseEnd <= $range[0]) || ($clauseStart >= $range[1]))) {
                $isOccupied = true;
                break;
            }
        }
        
        // Si no está ocupada y no contiene patrones de COMBO [@...], es una cláusula fija
        // IMPORTANTE: Las cláusulas con [#Del]/[#Al] SÍ son fijas (fechas siempre se aplican)
        // Solo excluimos [!...] (variables de sesión que ya fueron reemplazadas antes) y [@...] (combos)
        $containsComboPattern = (strpos($clauseText, '[@') !== false);
        $containsDatePattern = (strpos($clauseText, '[#') !== false);
        
        if (!$isOccupied && !$containsComboPattern) {
            // Es cláusula fija si:
            // 1. No tiene patrones de combo [@...]
            // 2. Puede tener patrones de fecha [#...] que se reemplazan después
            
            $fixedClauses[] = array(
                'type' => 'fixed',
                'pattern' => '',
                'clause' => $clauseText,
                'connector' => strtolower($connector),
                'field' => '',
                'operator' => '',
                'is_multiselection' => false,
                'label' => $containsDatePattern ? 'fixed_date' : 'fixed',
                'start_pos' => $clauseStart,
                'end_pos' => $clauseEnd,
                'is_active' => true  // Las fijas siempre están activas
            );
            
            error_log("Fixed clause encontrado" . ($containsDatePattern ? " (con fecha)" : "") . ": " . substr($clauseText, 0, 80) . "...");
        }
        
        // Continuar buscando desde el final de esta cláusula
        $offset = $clauseEnd;
    }
    
    // BÚSQUEDA ADICIONAL: Capturar cláusulas SIN paréntesis
    // Ejemplo: "And ik.fecha <= "[#Al]  23:59:59""
    // Estas cláusulas no tienen paréntesis pero deben preservarse
    $offset = 0;
    while (preg_match('/(and|or)\s+([a-zA-Z0-9_.]+)\s*([<>=!]+)\s*["\']?\[#[^\]]+\][^and|or]*/i', $whereClause, $match, PREG_OFFSET_CAPTURE, $offset)) {
        $clauseStart = $match[0][1];
        $clauseText = $match[0][0];
        $clauseEnd = $clauseStart + strlen($clauseText);
        $connector = strtolower($match[1][0]);
        
        // Verificar que no esté ocupada por un filtro
        $isOccupied = false;
        foreach ($occupiedRanges as $range) {
            if (!(($clauseEnd <= $range[0]) || ($clauseStart >= $range[1]))) {
                $isOccupied = true;
                break;
            }
        }
        
        if (!$isOccupied) {
            $fixedClauses[] = array(
                'type' => 'fixed',
                'pattern' => '',
                'clause' => trim($clauseText),
                'connector' => $connector,
                'field' => '',
                'operator' => '',
                'is_multiselection' => false,
                'label' => 'fixed_date',
                'start_pos' => $clauseStart,
                'end_pos' => $clauseEnd,
                'is_active' => true
            );
            
            error_log("Fixed clause sin paréntesis encontrado: " . substr($clauseText, 0, 80) . "...");
        }
        
        $offset = $clauseEnd;
    }
    
    return $fixedClauses;
}

/**
 * FASE 2: EVALUACIÓN - Determina qué filtros están activos y prepara valores de reemplazo
 * 
 * @param array $filterDefinitions Array de FilterDefinition de la Fase 1
 * @param array $filterValues Valores de filtros desde $_POST
 * @param array $filters Array global de filtros con metadata
 * @return array Array de filtros evaluados con 'is_active' y 'replacement_value'
 */
function evaluateFilters($filterDefinitions, $filterValues, $filters) {
    $evaluated = array();
    
    error_log("======= FASE 2: EVALUACIÓN DE FILTROS =======");
    
    foreach ($filterDefinitions as $def) {
        // Las cláusulas fijas siempre están activas
        if ($def['type'] == 'fixed') {
            $evaluated[] = array_merge($def, array(
                'is_active' => true,
                'replacement_value' => $def['clause']
            ));
            continue;
        }
        
        // Determinar la clave del filtro según el tipo
        $filterKey = 'filter_' . sanitizeId($def['label']);
        $hasValue = false;
        $value = null;
        
        // Verificar si el filtro tiene valor según su tipo
        // NOTA: 'date' ya no se maneja aquí - se procesa antes del sistema de 3 fases
        if ($def['type'] == 'text') {
            // Para texto, verificar que no esté vacío
            $hasValue = isset($filterValues[$filterKey]) && trim($filterValues[$filterKey]) !== '';
            $value = isset($filterValues[$filterKey]) ? $filterValues[$filterKey] : '';
        } elseif ($def['type'] == 'combo') {
            // Para combos, verificar valor o array
            $hasValue = isset($filterValues[$filterKey]) && $filterValues[$filterKey] !== '';
            
            // Para multiselección, también verificar si es array no vacío
            if ($def['is_multiselection'] && isset($filterValues[$filterKey])) {
                if (is_array($filterValues[$filterKey])) {
                    $hasValue = !empty($filterValues[$filterKey]);
                }
            }
            $value = isset($filterValues[$filterKey]) ? $filterValues[$filterKey] : '';
        }
        
        if ($hasValue) {
            // Filtro activo - preparar valor de reemplazo según tipo
            $replacementValue = '';
            
            if ($def['is_multiselection'] && is_array($value)) {
                // Construir IN clause: IN ('val1','val2','val3') - MySQL 5.5 compatible
                $quotedValues = array_map(function($v) {
                    return "'" . addslashes($v) . "'";
                }, $value);
                $replacementValue = 'IN (' . implode(',', $quotedValues) . ')';
                error_log("Filter '$filterKey' activo (multiselección): " . $replacementValue);
            } elseif ($def['type'] == 'text' && $def['operator'] == 'LIKE') {
                // Texto con LIKE - agregar wildcards si no los tiene
                if (strpos($value, '%') === false) {
                    $replacementValue = "'%" . addslashes($value) . "%'";
                } else {
                    $replacementValue = "'" . addslashes($value) . "'";
                }
                error_log("Filter '$filterKey' activo (text/LIKE): " . $replacementValue);
            } else {
                // Valor simple con comillas simples MySQL 5.5
                $replacementValue = "'" . addslashes($value) . "'";
                error_log("Filter '$filterKey' activo (simple): " . $replacementValue);
            }
            
            $evaluated[] = array_merge($def, array(
                'is_active' => true,
                'replacement_value' => $replacementValue
            ));
        } else {
            // Filtro inactivo - no se incluirá en el WHERE final
            error_log("Filter '$filterKey' INACTIVO - será eliminado");
            $evaluated[] = array_merge($def, array(
                'is_active' => false,
                'replacement_value' => ''
            ));
        }
    }
    
    return $evaluated;
}

/**
 * FASE 3: REGENERACIÓN - Reconstruye el WHERE clause desde cero con solo filtros activos
 * 
 * @param array $evaluatedFilters Filtros evaluados de la Fase 2
 * @param string $originalWhere WHERE original (para preservar cláusulas fijas)
 * @return string WHERE clause limpio y regenerado
 */
function regenerateWhereClause($evaluatedFilters, $originalWhere) {
    error_log("======= FASE 3: REGENERACIÓN DE WHERE CLAUSE =======");
    
    $activeClauses = array();
    
    foreach ($evaluatedFilters as $filter) {
        if (!$filter['is_active']) {
            continue; // Saltar filtros inactivos
        }
        
        if ($filter['type'] == 'fixed') {
            // Cláusula fija - agregar tal cual
            $activeClauses[] = $filter['clause'];
        } else {
            // Cláusula de filtro - construir desde cero
            $connector = $filter['connector'];
            $field = $filter['field'];
            $operator = $filter['operator'];
            $value = $filter['replacement_value'];
            
            // Construir cláusula usando el operador correcto (=, <=, >=, LIKE, IN, etc.)
            if ($operator == 'IN') {
                // Para IN, el valor ya incluye "IN (...)"
                $clause = "$connector ($field $value)";
            } elseif ($operator == 'LIKE') {
                // Para LIKE, usar el operador LIKE
                $clause = "$connector ($field LIKE $value)";
            } else {
                // Para otros operadores (=, <=, >=, <>, etc.) usar el operador tal cual
                $clause = "$connector ($field $operator $value)";
            }
            
            $activeClauses[] = $clause;
            error_log("Cláusula regenerada: $clause");
        }
    }
    
    // Unir todas las cláusulas activas
    if (empty($activeClauses)) {
        error_log("No hay cláusulas activas - WHERE vacío");
        return '';
    }
    
    $whereClause = implode(' ', $activeClauses);
    
    // Limpiar el primer conector si empieza con 'and' o 'or'
    $whereClause = preg_replace('/^\s*(and|or)\s+/i', '', $whereClause);
    
    error_log("WHERE regenerado: " . substr($whereClause, 0, 200) . "...");
    
    return $whereClause;
}

/**
 * FUNCIÓN PRINCIPAL: Procesa filtros usando el sistema de 3 fases
 * 
 * @param string $whereClause WHERE clause original con patrones
 * @param array $filterValues Valores de filtros desde $_POST
 * @param array $filters Array global de filtros
 * @return string WHERE clause procesado y limpio
 */
function processFiltersThreePhase($whereClause, $filterValues, $filters) {
    error_log("========================================");
    error_log("INICIANDO PROCESAMIENTO DE 3 FASES");
    error_log("========================================");
    
    // FASE 1: Extracción de definiciones
    $filterDefinitions = extractFilterDefinitions($whereClause);
    
    // FASE 2: Evaluación de filtros activos
    $evaluatedFilters = evaluateFilters($filterDefinitions, $filterValues, $filters);
    
    // FASE 3: Regeneración del WHERE
    $newWhere = regenerateWhereClause($evaluatedFilters, $whereClause);
    
    error_log("========================================");
    error_log("PROCESAMIENTO DE 3 FASES COMPLETADO");
    error_log("========================================");
    
    return $newWhere;
}
?>
