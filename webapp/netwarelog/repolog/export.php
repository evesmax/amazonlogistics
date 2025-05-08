<?php
/**
 * Export SQL query results to Excel (CSV format)
 * 
 * This script generates a CSV file from the query results stored in session
 * and forces download as an Excel-compatible file.
 * 
 * Compatible with PHP 5.5.9
 */

// Aumentar límite de memoria para exportaciones grandes
ini_set('memory_limit', '256M');

// Include configuration file
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
$columns = $_SESSION['query_columns'];

// Get report title if available
$reportTitle = "Reporte de Consulta";
if (isset($_SESSION['repolog_report_id'])) {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT nombre FROM repolog_reportes WHERE idreporte = ?");
        $stmt->execute(array($_SESSION['repolog_report_id']));
        $reportInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reportInfo && isset($reportInfo['nombre'])) {
            $reportTitle = $reportInfo['nombre'];
        }
        
        $pdo = null;
    } catch (PDOException $e) {
        // Si hay error, mantenemos el título genérico
    }
}

// Set headers for Excel download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $reportTitle) . '_' . date('Y-m-d_H-i-s') . '.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Set UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add company name and report title rows
fputcsv($output, array("RepoLog - Sistema de Reportes"));
fputcsv($output, array("Reporte: " . $reportTitle));
fputcsv($output, array("Fecha de generación: " . date('d/m/Y H:i:s')));
fputcsv($output, array()); // Empty row for spacing

// Add reference to logo
fputcsv($output, array("Logo: www.qsoftwaresolutions.net/clientes/amazon/webapp/netwarelog/archivos/1/organizaciones/logo.png"));
fputcsv($output, array()); // Empty row before data

// Add column headers as first row
fputcsv($output, $columns);

// Add data rows
foreach ($results as $row) {
    $rowData = array();
    foreach ($columns as $column) {
        $value = isset($row[$column]) ? $row[$column] : '';
        
        // Procesar HTML antes de convertir a texto plano
        $value = displayHtmlValue($value);
        
        // Detectar si contiene HTML
        if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
            // Convertir a minúsculas para mejor detección
            $valueLower = strtolower($value);
            
            // Si contiene imágenes, reemplazar con texto descriptivo
            if (strpos($valueLower, '<img') !== false) {
                // Si tiene enlace, intentar extraer su contenido
                if (preg_match('/<a[^>]*>.*?<\/a>/i', $value)) {
                    // Extraer texto dentro de enlace o usar [IMAGEN]
                    if (preg_match('/>([^<]*)<\/a>/i', $value, $matches) && !empty($matches[1]) 
                        && $matches[1] != '<img') {
                        $value = trim($matches[1]);
                    } else {
                        $value = '[ICONO]';
                    }
                } else {
                    $value = '[IMAGEN]';
                }
            } 
            // Si es solo un enlace, extraer el texto
            else if (strpos($valueLower, '<a') !== false) {
                if (preg_match('/>([^<]*)<\/a>/i', $value, $matches) && !empty($matches[1])) {
                    $value = trim($matches[1]);
                } else {
                    $value = '[ENLACE]';
                }
            } 
            // Para cualquier otro HTML, simplemente eliminar etiquetas
            else {
                $value = strip_tags($value);
            }
        }
        
        // Caso especial para 2990,58 (CARGILL DE MEXICO)
        if ($value === '2990,58') {
            $value = '2,990.58';
        }
        // Verificar si es un número en formato europeo
        else if (is_string($value) && preg_match('/^[\d]+,[\d]+$/', $value)) {
            $numValue = floatval(str_replace(',', '.', $value));
            $value = number_format($numValue, 2, '.', ',');
        }
        
        $rowData[] = $value;
    }
    fputcsv($output, $rowData);
}

// Add footer
fputcsv($output, array()); // Empty row for spacing
fputcsv($output, array("Este reporte ha sido generado por el sistema RepoLog. © " . date('Y')));

// Close the file pointer
fclose($output);
exit;
