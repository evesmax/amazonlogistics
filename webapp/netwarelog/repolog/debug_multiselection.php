<?php
// Archivo de debug específico para multiselección

// Simular datos de entrada para debug
$whereClause = 'and (obd.idbodega IN [@Bodega;9;Bodega Central;9;@Multiselection] )';
$fullPattern = '[@Bodega;9;Bodega Central;9;@Multiselection]';
$comboValue = ['9', '10'];
$debug_info = [];

echo "<h2>DEBUG: Procesamiento de Multiselección</h2>\n";

// Simular el proceso exacto que está en repologfilters.php
echo "<p><strong>WHERE Original:</strong> " . htmlspecialchars($whereClause) . "</p>\n";

// Crear la cadena de valores para IN
$valuesString = "'" . implode("','", array_map('addslashes', array_map('strval', $comboValue))) . "'";
echo "<p><strong>Values String generado:</strong> " . htmlspecialchars($valuesString) . "</p>\n";

// Verificar si el patrón ya contiene IN en el SQL
$hasIN = preg_match('/\s+IN\s+["\']?' . preg_quote($fullPattern, '/') . '["\']?/i', $whereClause);
echo "<p><strong>¿Tiene IN en el patrón?</strong> " . ($hasIN ? 'SÍ' : 'NO') . "</p>\n";

if ($hasIN) {
    echo "<p><strong>ANTES del reemplazo:</strong> " . htmlspecialchars($whereClause) . "</p>\n";
    echo "<p><strong>Patrón a reemplazar:</strong> " . htmlspecialchars($fullPattern) . "</p>\n";
    echo "<p><strong>Valores para reemplazo:</strong> (" . htmlspecialchars($valuesString) . ")</p>\n";
    
    $whereClause = str_replace($fullPattern, "($valuesString)", $whereClause);
    
    // CORRECCIÓN ESPECIAL: Si el patrón está entre comillas, corregir IN "(...)" -> IN (...)
    $beforeQuotesFix = $whereClause;
    $whereClause = preg_replace('/IN\s+"\s*\(([^"]+)\)\s*"/i', 'IN ($1)', $whereClause);
    if ($whereClause !== $beforeQuotesFix) {
        echo "<p>✓ Aplicada corrección de comillas: " . htmlspecialchars($whereClause) . "</p>\n";
    }
    
    echo "<p><strong>DESPUÉS del reemplazo:</strong> " . htmlspecialchars($whereClause) . "</p>\n";
    
    // Aplicar limpieza universal
    echo "<p><strong>Aplicando limpieza universal...</strong></p>\n";
    
    $original = $whereClause;
    
    // Patrón: IN (valor1","valor2"" ) -> IN ("valor1","valor2")
    $whereClause = preg_replace('/IN\s*\(\s*([^",\)]+)","([^",\)]+)""\s*\)/i', 'IN ("$1","$2")', $whereClause);
    if ($whereClause !== $original) {
        echo "<p>✓ Aplicado patrón de 2 valores: " . htmlspecialchars($whereClause) . "</p>\n";
    }
    
    // Para múltiples valores
    $whereClause = preg_replace_callback('/IN\s*\(\s*([^"\']+(?:","[^"\']+)*)""\s*\)/i', function($matches) {
        $values = explode('","', $matches[1]);
        $cleanValues = array_map('trim', $values);
        return 'IN ("' . implode('","', $cleanValues) . '")';
    }, $whereClause);
    
    // Asegurar comillas al inicio
    $whereClause = preg_replace('/IN\s*\(\s*([^"\']+),/i', 'IN ("$1",', $whereClause);
    
    echo "<p><strong>RESULTADO FINAL:</strong> " . htmlspecialchars($whereClause) . "</p>\n";
}

// Probar también diferentes variantes del patrón
$testPatterns = [
    'and (obd.idbodega IN [@Bodega;9;Bodega Central;9;@Multiselection] )',
    'and (obd.idbodega = [@Bodega;9;Bodega Central;9;@Multiselection] )',
    'AND (obd.idbodega IN "[@Bodega;9;Bodega Central;9;@Multiselection]" )',
    'AND (obd.idbodega IN [@Bodega;9;Bodega Central;9;@Multiselection] )',
    'and (obd.idbodega IN (9","10","2"" ))'  // Patrón problemático específico
];

echo "<h3>Pruebas con diferentes patrones:</h3>\n";
foreach ($testPatterns as $i => $pattern) {
    echo "<p><strong>Patrón " . ($i+1) . ":</strong> " . htmlspecialchars($pattern) . "</p>\n";
    
    $hasINTest = preg_match('/\s+IN\s+["\']?' . preg_quote($fullPattern, '/') . '["\']?/i', $pattern);
    echo "<p>¿Detecta IN? " . ($hasINTest ? 'SÍ' : 'NO') . "</p>\n";
    
    if ($hasINTest) {
        $result = str_replace($fullPattern, "($valuesString)", $pattern);
        
        // Aplicar corrección de comillas igual que en el código real
        $beforeQuotesFix = $result;
        $result = preg_replace('/IN\s+"\s*\(([^"]+)\)\s*"/i', 'IN ($1)', $result);
        
        echo "<p>Resultado inicial: " . htmlspecialchars($beforeQuotesFix) . "</p>\n";
        if ($result !== $beforeQuotesFix) {
            echo "<p>✓ Corregido a: " . htmlspecialchars($result) . "</p>\n";
        } else {
            echo "<p>Final: " . htmlspecialchars($result) . "</p>\n";
        }
    } else {
        // Para el patrón problemático específico, aplicar limpieza directa
        if (strpos($pattern, '","') !== false && strpos($pattern, '"" )') !== false) {
            echo "<p><strong>⚠️  Patrón problemático detectado - aplicando limpieza específica</strong></p>\n";
            
            $cleanedPattern = $pattern;
            
            // Aplicar la nueva limpieza mejorada
            $cleanedPattern = preg_replace_callback('/IN\s*\(\s*(\d+(?:","[^,\)]+)*)""\s*\)/i', function($matches) {
                $values = explode('","', $matches[1]);
                $cleanValues = array_map('trim', $values);
                return 'IN ("' . implode('","', $cleanValues) . '")';
            }, $cleanedPattern);
            
            echo "<p>✓ Limpieza aplicada: " . htmlspecialchars($cleanedPattern) . "</p>\n";
        }
    }
    echo "<hr>\n";
}
?>