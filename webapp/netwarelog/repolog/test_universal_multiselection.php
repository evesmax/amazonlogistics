<?php
// Test universal multiselection cleaning
require_once 'sqlcleaner.php';

echo "<h2>TEST: Limpieza Universal de Multiselección</h2>\n";

// Casos de prueba universales
$testCases = [
    // Números
    'and (obd.idbodega IN (9","10","2"" ))',
    'and (cliente.id IN (1","2","3","4","5"" ))',
    
    // Texto
    'and (usuario.nombre IN (Juan","María","Pedro"" ))',
    'and (producto.categoria IN (Alimentos","Bebidas","Limpieza"" ))',
    
    // Fechas
    'and (fecha.mes IN (2023-01","2023-02","2023-03"" ))',
    
    // Valores mixtos
    'and (status.codigo IN (A1","B2","C3","D4"" ))',
    
    // Muchos valores
    'and (region.id IN (Norte","Sur","Este","Oeste","Centro","Noroeste"" ))',
    
    // Con espacios en valores
    'and (empresa.nombre IN (Empresa A","Empresa B","Empresa C"" ))',
    
    // Solo dos valores
    'and (activo.estado IN (Si","No"" ))',
    
    // Un solo valor malformado
    'and (tipo.nombre IN (Principal"" ))'
];

echo "<h3>Casos de Prueba:</h3>\n";

foreach ($testCases as $i => $testCase) {
    echo "<div style='margin: 15px 0; padding: 10px; border: 1px solid #ccc;'>\n";
    echo "<p><strong>Caso " . ($i + 1) . ":</strong></p>\n";
    echo "<p><strong>Original:</strong> <code>" . htmlspecialchars($testCase) . "</code></p>\n";
    
    $cleaned = cleanMultiselectionInConditions($testCase);
    
    echo "<p><strong>Limpiado:</strong> <code>" . htmlspecialchars($cleaned) . "</code></p>\n";
    
    $success = (strpos($cleaned, '""') === false && 
                strpos($cleaned, '"",') === false && 
                !preg_match('/IN\s*\(\s*[^"]/i', $cleaned));
                
    echo "<p><strong>Estado:</strong> " . ($success ? '✅ CORRECTO' : '❌ NECESITA REVISIÓN') . "</p>\n";
    echo "</div>\n";
}

echo "<h3>Test de Robustez - Casos Extremos:</h3>\n";

$extremeCases = [
    // Comillas mixtas
    'and (campo IN (valor1\',\"valor2\",\"valor3\"\" ))',
    
    // Números y texto mezclados
    'and (mixto IN (123","ABC","456","DEF"" ))',
    
    // Valores muy largos
    'and (descripcion IN (Este es un valor muy largo con espacios","Otro valor largo","Tercer valor"" ))',
    
    // Casos ya correctos (no deben cambiar)
    'and (correcto IN ("valor1","valor2","valor3"))',
    
    // Sin multiselección
    'and (simple = "valor_unico")'
];

foreach ($extremeCases as $i => $testCase) {
    echo "<div style='margin: 15px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9;'>\n";
    echo "<p><strong>Caso Extremo " . ($i + 1) . ":</strong></p>\n";
    echo "<p><strong>Original:</strong> <code>" . htmlspecialchars($testCase) . "</code></p>\n";
    
    $cleaned = cleanMultiselectionInConditions($testCase);
    
    echo "<p><strong>Limpiado:</strong> <code>" . htmlspecialchars($cleaned) . "</code></p>\n";
    
    $changed = ($cleaned !== $testCase);
    echo "<p><strong>¿Cambió?:</strong> " . ($changed ? '✅ SÍ (necesario)' : '✅ NO (correcto)') . "</p>\n";
    echo "</div>\n";
}
?>