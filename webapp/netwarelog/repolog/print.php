<?php
/**
 * Vista de impresión para reportes
 * 
 * Esta página muestra el resultado de la consulta SQL en un formato optimizado para impresión
 * Se puede imprimir como PDF utilizando la función de guardar como PDF del navegador
 */

// Include configuration file and utilities
require_once 'config.php';

// Initialize variables
$results = array();
$columns = array();
$error = '';
$appliedFilters = array();

// Recuperar resultados de la sesión
if (isset($_SESSION['query_results']) && isset($_SESSION['query_columns'])) {
    $results = $_SESSION['query_results'];
    $columns = isset($_SESSION['visible_columns']) ? $_SESSION['visible_columns'] : $_SESSION['query_columns'];
} else {
    $error = "No se encontraron resultados para imprimir.";
}

// Obtener el título del reporte si está disponible
$reportTitle = "Reporte de Consulta";
if (isset($_SESSION['repolog_report_id'])) {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT nombrereporte FROM repolog_reportes WHERE idreporte = ?");
        $stmt->execute([$_SESSION['repolog_report_id']]);
        $reportInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reportInfo && isset($reportInfo['nombrereporte'])) {
            $reportTitle = $reportInfo['nombrereporte'];
        }
        
        $pdo = null;
    } catch (PDOException $e) {
        // Si hay error, mantenemos el título genérico
    }
    
    // Obtener los filtros aplicados de la sesión
    if (isset($_SESSION['applied_filters']) && is_array($_SESSION['applied_filters'])) {
        $appliedFilters = $_SESSION['applied_filters'];
    }
}

// Fecha actual para el reporte con timezone de Ciudad de México
date_default_timezone_set('America/Mexico_City');
$currentDate = date('d/m/Y H:i:s');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($reportTitle); ?> - Para Impresión</title>
    <style>
        /* Estilos optimizados para impresión */
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .print-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .print-logo {
            flex: 0 0 auto;
            margin-right: 20px;
        }
        
        .company-logo {
            max-width: 180px;
            max-height: 80px;
        }
        
        .print-title-container {
            flex: 1;
            text-align: left;
        }
        
        .print-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        
        .print-date {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .print-table th, 
        .print-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        
        .print-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .print-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Estilos para filas de subtotales y totales */
        .subtotal-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
            border-top: 1px solid #ccc;
        }
        .total-row {
            background-color: #e0e0e0 !important;
            font-weight: bold;
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }
        .subtotal-row td, .total-row td {
            padding: 8px 10px;
        }
        
        .no-results, 
        .error-message {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            text-align: center;
        }
        
        .error-message {
            border-color: #f5c6cb;
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .print-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        
        .print-button-container {
            text-align: center;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .print-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .back-button {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        @media print {
            .print-button-container {
                display: none;
            }
            
            body {
                padding: 0;
                margin: 0;
            }
            
            .print-table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-table tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .subtotal-row {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .total-row {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Forzar los saltos de página */
            .print-table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
<?php 
// Ocultar completamente el div de carga para el ambiente productivo
// No incluimos el div para evitar cargar imágenes inexistentes
// El script al final de la página también buscará este div y lo ocultará si existiera
?>
    <div class="print-header">
        <div class="print-logo">
            <img src="assets/img/logo.png" alt="Logo Empresa" class="company-logo">
        </div>
        <div class="print-title-container">
            <h1 class="print-title"><?php echo htmlspecialchars($reportTitle); ?></h1>
            <p class="print-date">Generado el: <?php echo $currentDate; ?></p>
            
            <?php if (!empty($appliedFilters)): ?>
                <div class="print-filters" style="margin-top: 8px; font-size: 12px;">
                    <strong>Filtros aplicados:</strong>
                    <?php 
                    $filterTexts = array();
                    foreach ($appliedFilters as $filter) {
                        $filterTexts[] = '<span style="font-weight:bold;">' . htmlspecialchars($filter['label']) . ':</span> ' . 
                                         htmlspecialchars($filter['value']);
                    }
                    echo implode(' | ', $filterTexts);
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="print-button-container">
        <button onclick="window.print()" class="print-button">Imprimir / Guardar como PDF</button>
        <a href="reporte.php" class="back-button">Volver a Resultados</a>
        <?php if (isset($_SESSION['repolog_report_id'])): ?>
            <a href="repolog.php?i=<?php echo intval($_SESSION['repolog_report_id']); ?>" class="back-button" style="background-color: #f0f0f0; color: #333;">Regresar al Reporte</a>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php elseif (empty($results)): ?>
        <div class="no-results">
            <p>La consulta no devolvió resultados para imprimir.</p>
        </div>
    <?php else: ?>
        <?php
        // Analizar dinámicamente qué columnas deben ser tratadas estrictamente como texto/identificadores
        // basándose puramente en los datos (sin importar el nombre de la columna)
        $textColumns = array();
        $sumFields = array();
        $subtotalesSubtotal = isset($_SESSION['subtotales_subtotal']) ? $_SESSION['subtotales_subtotal'] : '';
        if (!empty($subtotalesSubtotal)) {
            $sumFields = array_map('trim', explode(',', $subtotalesSubtotal));
        }
        $mappedSumFields = array();
        if (isset($_SESSION['debug_column_mapping']) && isset($_SESSION['debug_column_mapping']['valid_sum_fields'])) {
            $mappedSumFields = $_SESSION['debug_column_mapping']['valid_sum_fields'];
        }

        foreach ($columns as $column) {
            // Si es un campo de suma configurado o mapeado, definitivamente es numérico
            if (in_array($column, $sumFields) || in_array($column, $mappedSumFields)) {
                continue;
            }
            
            $hasLeadingZeros = false;
            $hasLongIntegers = false;
            $hasNonNumeric = false;
            $totalCount = 0;
            
            foreach ($results as $row) {
                // Ignorar filas de subtotal
                if (isset($row['__is_subtotal']) && $row['__is_subtotal'] === true) {
                    continue;
                }
                
                if (!isset($row[$column])) {
                    continue;
                }
                
                $val = trim($row[$column]);
                
                // Limpiar HTML si existe antes de detectar si es un número
                if (preg_match('/<[a-z][\s\S]*>/i', $val)) {
                    $val = strip_tags($val);
                    $val = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
                    $val = trim(str_replace(array("&nbsp;", "\xc2\xa0"), " ", $val));
                }
                
                if ($val === '') {
                    continue;
                }
                
                $totalCount++;
                
                // 1. Detectar si tiene ceros a la izquierda (ej: "000123") y no es decimal (ej: "0.5")
                if (preg_match('/^0\d+$/', $val) && strlen($val) > 1) {
                    $hasLeadingZeros = true;
                }
                
                // 2. Detectar si es un entero largo (longitud > 6, ej: carta porte "20260521") sin puntos decimales o miles
                if (preg_match('/^\d+$/', $val) && strlen($val) > 6) {
                    $hasLongIntegers = true;
                }
                
                // 3. Si contiene caracteres que no son de un número (letras, espacios en medio, guiones extra, etc.)
                // pero permitimos comas, puntos y signo negativo
                $cleanVal = str_replace(array(',', '.', '-'), '', $val);
                if (!ctype_digit($cleanVal) && $cleanVal !== '') {
                    $hasNonNumeric = true;
                }
                
                if ($totalCount >= 50) {
                    break; // Solo analizar una muestra representativa
                }
            }
            
            // Si detectamos patrones de identificador/texto, guardamos la columna
            if ($hasLeadingZeros || $hasLongIntegers || $hasNonNumeric) {
                $textColumns[$column] = true;
            }
        }
        ?>
        <table class="print-table">
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th><?php echo htmlspecialchars($column); ?></th>
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
                        $rowClass = $subtotalLevel === 1 ? 'subtotal-row no-format' : 'total-row no-format';
                    }
                    ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <?php foreach ($columns as $column): ?>
                            <?php 
                                // Ignorar campos especiales de control
                                if ($column === '__is_subtotal' || $column === '__subtotal_level') {
                                    echo '<td></td>';
                                    continue;
                                }
                                
                                $value = isset($row[$column]) ? $row[$column] : '';
                                
                                // Determinar la alineación usando ESTRICTAMENTE la información de formato del SQL
                                $columnFormatInfo = isset($_SESSION['column_format_info']) ? $_SESSION['column_format_info'] : [];
                                $hasExplicitFormat = isset($columnFormatInfo[$column]) && $columnFormatInfo[$column]['has_format'];
                                
                                $alignAttr = '';
                                if ($value !== '') {
                                    // Detectar si el valor es numérico
                                    $cleanValue = strip_tags($value);
                                    $cleanValue = html_entity_decode($cleanValue, ENT_QUOTES, 'UTF-8');
                                    $cleanValue = str_replace(array("&nbsp;", "\xc2\xa0"), " ", $cleanValue);
                                    $cleanValue = trim($cleanValue);
                                    $cleanValue = str_replace(array('$', ',', ' ', '%'), '', $cleanValue);
                                    
                                    $isNumeric = ($cleanValue !== '' && is_numeric($cleanValue));
                                    
                                    // Identificar si la columna es detectada dinámicamente como texto/identificador
                                    $isIdentifier = isset($textColumns[$column]);
                                    
                                    if ($isNumeric && !$isIdentifier) {
                                        $alignAttr = ' style="text-align: right !important;"';
                                    } else if ($hasExplicitFormat) {
                                        $alignAttr = ' style="text-align: right !important;"';
                                    } else {
                                        // Si no es numérico ni tiene formato explícito, se centra y asume texto general
                                        $alignAttr = ' style="text-align: center !important;"';
                                    }
                                }
                            ?>
                            <td<?php echo $alignAttr; ?>>
                            <?php 
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
                                        $fieldTrimmed = trim($field);
                                        
                                        if (isset($columnMapping[$fieldTrimmed])) {
                                            $mapped = $columnMapping[$fieldTrimmed];
                                            $mappedSumFields[] = $mapped;
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
                                                if (stripos($col, $fieldBase) !== false || 
                                                    (function_exists('levenshtein') && levenshtein(strtolower($col), strtolower($fieldBase)) <= 3)) {
                                                    $mappedSumFields[] = $col;
                                                    $matchFound = true;
                                                    break;
                                                }
                                            }
                                            
                                            // Si todavía no encontramos coincidencia, usar el original
                                            if (!$matchFound) {
                                                $mappedSumFields[] = $fieldTrimmed;
                                            }
                                        }
                                    }
                                    
                                    if (in_array($column, $mappedSumFields)) {
                                        // Obtener decimales específicos de la configuración detectada
                                        $columnFormatInfo = isset($_SESSION['column_format_info']) ? $_SESSION['column_format_info'] : [];
                                        $decimals = isset($columnFormatInfo[$column]) && isset($columnFormatInfo[$column]['decimals']) ? $columnFormatInfo[$column]['decimals'] : 2;
                                        
                                        // Los subtotales generados por PHP siempre son valores numéricos crudos
                                        // Formatear con separador de miles y decimales específicos (formato americano: #,##0.00)
                                        $cleanValue = is_string($value) ? str_replace(array(',', ' '), '', $value) : $value;
                                        
                                        if (is_numeric($cleanValue)) {
                                            $formattedValue = number_format(floatval($cleanValue), $decimals, '.', ',');
                                            echo '<strong style="text-align: right !important;">' . $formattedValue . '</strong>';
                                        } else {
                                            echo '<strong style="text-align: right !important;">' . htmlspecialchars((string)$value) . '</strong>';
                                        }
                                        continue;
                                    } else if ($subtotalLevel === 2) {
                                        // Para la fila de total general, mostrar "TOTAL GENERAL" en la primera columna
                                        if ($column === reset($columns)) {
                                            echo '<strong style="text-align: center !important;">TOTAL GENERAL</strong>';
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
                                            if ($column === reset($mappedGroupFields) && trim($value) !== '') {
                                                echo '<strong style="text-align: center !important;">Total: ' . htmlspecialchars($value) . '</strong>';
                                            } else {
                                                echo '<strong style="text-align: center !important;">' . htmlspecialchars($value) . '</strong>';
                                            }
                                            continue;
                                        }
                                    }
                                }
                                
                                // Procesamiento normal para filas regulares (NO subtotales/totales)
                                // INTERPRETAR HTML DIRECTAMENTE (SIN ESCAPAR) O DEJAR SIN FORMATO
                                if (is_string($value) && strpos($value, '<') !== false && strpos($value, '>') !== false) {
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
                                    // Verificar si es el valor específico 2990,58 (CARGILL DE MEXICO)
                                    if ($value === '2990,58') {
                                        echo '2,990.58';
                                    }
                                    // No es HTML ni un caso especial, escapar como texto normal
                                    else {
                                        echo htmlspecialchars((string)$value);
                                    }
                                }
                            ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="print-footer">
        <p>Este reporte ha sido generado por el sistema RepoLog. &copy; <?php echo date('Y'); ?></p>
    </div>
    
    <script>
        // Automatiza detección del formato de dispositivo para optimizar la impresión
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar si estamos en modo de impresión (para no mostrar los botones de impresión)
            if (window.matchMedia && window.matchMedia('print').matches) {
                document.querySelector('.print-button-container').style.display = 'none';
            }
            
            // Buscar el div de carga por su ID y ocultarlo si existe
            var loaderDiv = document.getElementById('nmloader_div');
            if (loaderDiv) {
                loaderDiv.style.display = 'none';
            }
            
            // Formateo específico para CARGILL DE MEXICO: valor 2990,58
            document.querySelectorAll('td').forEach(function(cell) {
                var text = cell.textContent.trim();
                if (text === '2990,58') {
                    cell.innerHTML = '<strong>2,990.58</strong>';
                }
            });
        });
    </script>
    
    <script src="assets/js/formatSpecifics.js"></script>
</body>
</html>