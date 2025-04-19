<?php
/**
 * Export SQL query results to Excel (CSV format)
 * 
 * This script generates a CSV file from the query results stored in session
 * and forces download as an Excel-compatible file.
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
        
        $stmt = $pdo->prepare("SELECT nombre FROM repolog_reportes WHERE idreporte = ?");
        $stmt->execute([$_SESSION['repolog_report_id']]);
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
fputcsv($output, ["RepoLog - Sistema de Reportes"]);
fputcsv($output, ["Reporte: " . $reportTitle]);
fputcsv($output, ["Fecha de generación: " . date('d/m/Y H:i:s')]);
fputcsv($output, []); // Empty row for spacing

// Add reference to logo
fputcsv($output, ["Logo: www.qsoftwaresolutions.net/clientes/amazon/webapp/netwarelog/archivos/1/organizaciones/logo.png"]);
fputcsv($output, []); // Empty row before data

// Add column headers as first row
fputcsv($output, $columns);

// Add data rows
foreach ($results as $row) {
    $rowData = [];
    foreach ($columns as $column) {
        $rowData[] = isset($row[$column]) ? $row[$column] : '';
    }
    fputcsv($output, $rowData);
}

// Add footer
fputcsv($output, []); // Empty row for spacing
fputcsv($output, ["Este reporte ha sido generado por el sistema RepoLog. © " . date('Y')]);

// Close the file pointer
fclose($output);
exit;
