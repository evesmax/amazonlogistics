<?php
/**
 * Export SQL query results to Excel (HTML format)
 * 
 * This script generates an HTML file with Excel-specific markers
 * to create a better Excel file with formatting and embedded logo.
 * 
 * Compatible with PHP 5.5.9
 */

// Aumentar límite de memoria para exportaciones grandes
ini_set('memory_limit', '256M');

// Include configuration files
require_once 'config.php';
// Se ha eliminado la referencia a all_level_html_fix.php para mostrar el HTML tal cual

// Función simple para mostrar HTML
function displayHtmlValue($value) {
    // Simplemente retornar el valor tal cual
    return $value;
}

// Check if results exist in session
if (!isset($_SESSION['query_results']) || !isset($_SESSION['query_columns'])) {
    die("No hay resultados para exportar.");
}

$results = $_SESSION['query_results'];
$columns = isset($_SESSION['visible_columns']) ? $_SESSION['visible_columns'] : $_SESSION['query_columns'];

// Get report title if available
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
}

// Fecha actual para el reporte
$currentDate = date('d/m/Y H:i:s');

// Set headers for Excel download (HTML format)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $reportTitle) . '_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Generate HTML content for Excel
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:x="urn:schemas-microsoft-com:office:excel" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name><?php echo htmlspecialchars($reportTitle); ?></x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .logo-container {
            display: table-cell;
            width: 200px;
            vertical-align: middle;
            padding-right: 20px;
        }
        .company-logo-text {
            font-size: 18pt;
            font-weight: bold;
            color: #0066CC;
            line-height: 1.2;
            font-family: 'Arial Black', Arial, sans-serif;
        }
        .title-container {
            display: table-cell;
            vertical-align: middle;
        }
        .report-title {
            font-size: 20pt;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .report-date {
            font-size: 12pt;
            color: #666;
            margin: 0;
        }
        .report-filters {
            margin-top: 8px;
            font-size: 12pt;
            color: #666;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <div class="company-logo-text">
                AMAZON<br>LOGISTICS
            </div>
        </div>
        
        <div class="title-container">
            <h1 class="report-title"><?php echo htmlspecialchars($reportTitle); ?></h1>
            <p class="report-date">Generado el: <?php echo $currentDate; ?></p>
            
            <?php if (isset($_SESSION['applied_filters']) && !empty($_SESSION['applied_filters'])): ?>
                <div class="report-filters">
                    <strong>Filtros aplicados:</strong>
                    <?php 
                    $filterTexts = array();
                    foreach ($_SESSION['applied_filters'] as $filter) {
                        $filterTexts[] = '<span style="font-weight:bold;">' . htmlspecialchars($filter['label']) . ':</span> ' . 
                                       htmlspecialchars($filter['value']);
                    }
                    echo implode(' | ', $filterTexts);
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (empty($results)): ?>
        <p>La consulta no devolvió resultados para exportar.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th><?php echo htmlspecialchars($column); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <td>
                            <?php 
                                $value = isset($row[$column]) ? $row[$column] : '';
                                
                                // Aplicar la función displayHtmlValue para corregir el HTML
                                $value = displayHtmlValue($value);
                                
                                // Detectar si parece contener HTML (case-insensitive)
                                if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
                                    // Convertir a minúsculas para mejor detección
                                    $valueLower = strtolower($value);
                                    
                                    // Contiene HTML, verificar si es una imagen
                                    if (strpos($valueLower, '<img') !== false) {
                                        // Es una imagen, extraer el contenido del enlace si existe
                                        if (preg_match('/<a href="[^"]*"[^>]*>.*?<\/a>/i', $value, $matches)) {
                                            // Si tiene enlace, extraer texto de enlace o usar [IMAGEN ENLACE]
                                            if (preg_match('/>([^<]*)<\/a>/i', $value, $contentMatches) && !empty($contentMatches[1]) 
                                                && $contentMatches[1] != '<img') {
                                                echo trim($contentMatches[1]);
                                            } else {
                                                echo '[IMAGEN ENLACE]';
                                            }
                                        } else {
                                            // Sin enlace, solo una imagen
                                            echo '[IMAGEN]';
                                        }
                                    } else if (strpos($valueLower, '<a') !== false) {
                                        // Es un enlace, extraer el texto del enlace
                                        if (preg_match('/>([^<]*)<\/a>/i', $value, $matches) && !empty($matches[1])) {
                                            echo trim($matches[1]);
                                        } else {
                                            // No se pudo extraer el texto, mostrar [ENLACE]
                                            echo '[ENLACE]';
                                        }
                                    } else if (strpos($valueLower, '<center') !== false ||
                                              strpos($valueLower, '<div') !== false) {
                                        
                                        // Eliminar etiquetas HTML para mostrar solo el texto
                                        echo strip_tags($value);
                                    } else {
                                        // Es HTML - Mostrar sin etiquetas para Excel
                                        echo strip_tags($value);
                                    }
                                } else {
                                    // Verificar si parece ser un número formateado (con comas o puntos)
                                    if (is_string($value) && preg_match('/^[\d.,]+$/', $value)) {
                                        // Extraer solo dígitos y punto decimal, ignorando separadores de miles
                                        $cleanedValue = $value;
                                        
                                        // Formato europeo (2.990,58) -> (2990.58)
                                        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
                                            // Si tiene puntos y luego coma, es formato europeo
                                            if (strpos($value, '.') < strpos($value, ',')) {
                                                $cleanedValue = str_replace('.', '', $value); // Quitar puntos
                                                $cleanedValue = str_replace(',', '.', $cleanedValue); // Convertir coma a punto
                                            }
                                        } 
                                        // Formato con coma decimal (2990,58) -> (2990.58)
                                        else if (strpos($value, ',') !== false) {
                                            $cleanedValue = str_replace(',', '.', $value);
                                        }
                                        
                                        // Verificar si ahora es un número válido
                                        if (is_numeric($cleanedValue)) {
                                            // Agregar mso:number-format para indicar a Excel que es un número
                                            // y el formato específico a utilizar
                                            echo '<span style="mso:number-format:\'#,##0.00_\)\;\[Red\](#,##0.00\)\'">' . 
                                                  $cleanedValue . 
                                                 '</span>';
                                        } else {
                                            echo $value; // No se pudo convertir, mostrar original
                                        }
                                    }
                                    // Verificar si es un número sin formato
                                    else if (is_numeric($value)) {
                                        // Es un número sin formato, usar estilo para Excel
                                        echo '<span style="mso:number-format:\'#,##0.00_\)\;\[Red\](#,##0.00\)\'">' . 
                                              $value . 
                                             '</span>';
                                    }
                                    // No es HTML ni número - MOSTRAR TAMBIÉN SIN ESCAPAR
                                    else {
                                        echo $value;
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
    
    <div class="footer">
        <p>Este reporte ha sido generado por el sistema RepoLog. &copy; <?php echo date('Y'); ?></p>
    </div>
</body>
</html>
<?php
exit;
?>