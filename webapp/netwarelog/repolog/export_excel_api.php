<?php
/**
 * Export SQL query results to Excel using external API
 * 
 * Este script consume un API externo para generar reportes Excel profesionales
 * con formato avanzado, logo y estilos predefinidos.
 * 
 * API: https://qssools.replit.app/api/generate-excel/
 * Compatible con PHP 5.5.9
 */

ini_set('memory_limit', '256M');

require_once 'config.php';

if (!isset($_SESSION['query_results']) || !isset($_SESSION['query_columns'])) {
    die("No hay resultados para exportar.");
}

$results = $_SESSION['query_results'];
$columns = isset($_SESSION['visible_columns']) ? $_SESSION['visible_columns'] : $_SESSION['query_columns'];

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
    }
}

// Fecha actual con timezone de Ciudad de México
date_default_timezone_set('America/Mexico_City');
$currentDate = date('d/m/Y H:i:s');

$filterInfo = "";
if (isset($_SESSION['applied_filters']) && !empty($_SESSION['applied_filters'])) {
    $filterParts = [];
    foreach ($_SESSION['applied_filters'] as $filter) {
        if (isset($filter['label']) && isset($filter['value'])) {
            $value = is_array($filter['value']) ? implode(', ', $filter['value']) : $filter['value'];
            $filterParts[] = $filter['label'] . ": " . $value;
        }
    }
    if (!empty($filterParts)) {
        $filterInfo = implode("    ", $filterParts);
    }
} elseif (isset($_SESSION['user_selected_date_filter_al'])) {
    $filterInfo = "Al: " . $_SESSION['user_selected_date_filter_al'];
}

$titleHtml = "";
if (!empty($filterInfo)) {
    $titleHtml .= "<strong>Filtros aplicados:</strong> " . htmlspecialchars($filterInfo) . "    ";
}
$titleHtml .= "Generado el: " . $currentDate;

$logoUrl = "https://qsoftwaresolutions.net/clientes/amazon/webapp/netwarelog/archivos/1/administracion_usuarios/logoamz.jpg";

$customerInfo = "<strong>" . htmlspecialchars($reportTitle) . "</strong>";

$formatInfo = array();
if (isset($_SESSION['column_format_info'])) {
    $formatInfo = $_SESSION['column_format_info'];
}
error_log("=== FORMATO DE COLUMNAS PARA EXCEL ===");
error_log("Format Info disponible: " . (empty($formatInfo) ? "NO" : "SI"));
if (!empty($formatInfo)) {
    foreach ($formatInfo as $col => $info) {
        error_log("Columna: " . $col . " -> decimales: " . (isset($info['decimals']) ? $info['decimals'] : 'no definido'));
    }
}

$dataFormatted = array();
foreach ($results as $dataRow) {
    $row = array();
    foreach ($columns as $columnName) {
        $value = isset($dataRow[$columnName]) ? $dataRow[$columnName] : '';
        
        if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
            $value = strip_tags($value);
        }
        
        if (is_numeric(str_replace(',', '', $value))) {
            $cleanValue = str_replace(',', '', $value);
            
            $decimals = 2;
            if (isset($formatInfo[$columnName]) && isset($formatInfo[$columnName]['decimals'])) {
                $decimals = $formatInfo[$columnName]['decimals'];
            }
            
            $value = number_format(floatval($cleanValue), $decimals, '.', ',');
        }
        
        $row[] = $value;
    }
    $dataFormatted[] = $row;
}

$apiUrl = 'https://qssools.replit.app/api/generate-excel/68e2452b-ae58-47f7-a857-ae16a96b19e5';

$payload = array(
    'title' => $titleHtml,
    'logoUrl' => $logoUrl,
    'customerInfo' => $customerInfo,
    'headers' => $columns,
    'data' => $dataFormatted
);

error_log("=== EXCEL API REQUEST ===");
error_log("API URL: " . $apiUrl);
error_log("Report Title: " . $reportTitle);
error_log("Columns count: " . count($columns));
error_log("Data rows count: " . count($dataFormatted));
error_log("Headers: " . implode(", ", $columns));

$jsonPayload = json_encode($payload);
error_log("Payload size: " . strlen($jsonPayload) . " bytes");
error_log("Payload preview (first 500 chars): " . substr($jsonPayload, 0, 500));

$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-Type: application/json' . "\r\n" .
                    'Content-Length: ' . strlen($jsonPayload) . "\r\n",
        'content' => $jsonPayload,
        'timeout' => 60,
        'ignore_errors' => true
    )
);

$context = stream_context_create($options);
$response = @file_get_contents($apiUrl, false, $context);

error_log("=== EXCEL API RESPONSE ===");

if ($response === false) {
    $error = error_get_last();
    error_log("ERROR: Request failed - " . ($error ? $error['message'] : 'Unknown error'));
    die('Error al conectar con el API. Verifique su conexión a internet.');
}

if (isset($http_response_header)) {
    $httpCode = 0;
    foreach ($http_response_header as $header) {
        if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
            $httpCode = intval($matches[1]);
            break;
        }
    }
    error_log("HTTP Code: " . $httpCode);
    
    if ($httpCode !== 200) {
        error_log("ERROR: API returned HTTP " . $httpCode);
        error_log("Response: " . substr($response, 0, 1000));
        die('Error del API (HTTP ' . $httpCode . '): ' . $response);
    }
} else {
    error_log("WARNING: Could not determine HTTP status code");
}

error_log("Response size: " . strlen($response) . " bytes");
error_log("Response starts with: " . substr($response, 0, 10));

if (substr($response, 0, 2) === 'PK') {
    error_log("Response is direct binary Excel file (XLSX)");
    $excelContent = $response;
} else {
    error_log("Response is JSON, trying to decode");
    $responseData = json_decode($response, true);
    
    if (!isset($responseData['file'])) {
        error_log("ERROR: Response does not contain 'file' field");
        if (is_array($responseData)) {
            error_log("Response keys: " . implode(", ", array_keys($responseData)));
        }
        die('Error: La respuesta del API no contiene el archivo en base64');
    }
    
    $base64File = $responseData['file'];
    error_log("Base64 file length: " . strlen($base64File) . " chars");
    
    $excelContent = base64_decode($base64File);
    
    if ($excelContent === false) {
        error_log("ERROR: Failed to decode base64");
        die('Error al decodificar el archivo base64');
    }
}

error_log("Excel content size: " . strlen($excelContent) . " bytes");

$filename = str_replace(' ', '_', $reportTitle) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
error_log("Generated filename: " . $filename);
error_log("=== EXCEL DOWNLOAD SUCCESS ===");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Content-Length: ' . strlen($excelContent));

echo $excelContent;
exit;
?>
