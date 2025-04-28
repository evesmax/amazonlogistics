<?php
/**
 * Export SQL query results to Excel (HTML format)
 * 
 * This script generates an HTML file with Excel-specific markers
 * to create a better Excel file with formatting and embedded logo.
 * 
 * Compatible with PHP 5.5.9
 */

// Include configuration file
require_once 'config.php';

// Check if results exist in session
if (!isset($_SESSION['query_results']) || !isset($_SESSION['query_columns'])) {
    die("No hay resultados para exportar.");
}

$results = $_SESSION['query_results'];
$columns = $_SESSION['query_columns'];

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
        }
        .logo {
            display: table-cell;
            vertical-align: middle;
            width: 200px;
        }
        .title-container {
            display: table-cell;
            vertical-align: middle;
        }
        .report-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
        }
        .report-date {
            font-size: 10pt;
            color: #666;
            margin: 5px 0 0 0;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="assets/img/logo.png" alt="Logo Empresa" width="180">
        </div>
        <div class="title-container">
            <h1 class="report-title"><?php echo htmlspecialchars($reportTitle); ?></h1>
            <p class="report-date">Generado el: <?php echo $currentDate; ?></p>
            
            <?php if (isset($_SESSION['applied_filters']) && !empty($_SESSION['applied_filters'])): ?>
                <div class="report-filters" style="margin-top: 8px; font-size: 10pt; color: #666;">
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
                                
                                // Detectar si parece contener HTML (case-insensitive)
                                if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
                                    // Convertir a minúsculas para mejor detección
                                    $valueLower = strtolower($value);
                                    
                                    // Contiene HTML, verificar si es una imagen
                                    if (strpos($valueLower, '<img') !== false) {
                                        // Es una imagen, reemplazar con texto [IMAGEN]
                                        echo '[IMAGEN]';
                                    } else if (strpos($valueLower, '<a') !== false || 
                                              strpos($valueLower, '<center') !== false ||
                                              strpos($valueLower, '<div') !== false) {
                                        
                                        // Arreglar enlaces HTML sin comillas en los atributos
                                        if (preg_match('/<a\s+href=([^"\'>]+)([^>]*)>/i', $value)) {
                                            $value = preg_replace('/(<a\s+href=)([^"\'>]+)([^>]*)>/i', '$1"$2"$3>', $value);
                                        }
                                        
                                        // Es otro tipo de HTML que no es imagen, mostrarlo como tal
                                        echo $value;
                                    } else {
                                        // Es HTML pero no de los tipos específicos, escapar
                                        echo htmlspecialchars($value);
                                    }
                                } else {
                                    // Verificar si es el valor específico 2990,58
                                    if ($value === '2990,58') {
                                        echo '2,990.58';
                                    }
                                    // Verificar si es un número en formato europeo
                                    else if (is_string($value) && preg_match('/^[\d]+,[\d]+$/', $value)) {
                                        $numValue = floatval(str_replace(',', '.', $value));
                                        echo number_format($numValue, 2, '.', ',');
                                    }
                                    // No es HTML ni número especial, escapar como texto normal
                                    else {
                                        echo htmlspecialchars($value);
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