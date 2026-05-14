<?php
/**
 * Export SQL query results to Excel using PHPExcel
 * 
 * Este script utiliza PHPExcel nativamente para generar un archivo .xlsx real.
 * Reemplaza a export_excel_api.php que usaba un servicio externo.
 */

// Aumentar límite de memoria para exportaciones grandes
ini_set('memory_limit', '512M');
set_time_limit(300); // 5 minutos por si la consulta es muy grande

require_once 'config.php';
require_once 'assets/PHPExcel/Classes/PHPExcel.php';

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
        // Ignorar error y dejar título por defecto
    }
}

date_default_timezone_set('America/Mexico_City');
$currentDate = date('d/m/Y H:i:s');

// Formato de columnas si existe (opcional)
$formatInfo = array();
if (isset($_SESSION['column_format_info'])) {
    $formatInfo = $_SESSION['column_format_info'];
}

// Inicializar PHPExcel
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Amazon Logistics RepoLog")
                             ->setLastModifiedBy("Amazon Logistics")
                             ->setTitle($reportTitle)
                             ->setSubject($reportTitle)
                             ->setDescription("Reporte generado por RepoLog")
                             ->setKeywords("reporte excel")
                             ->setCategory("Reportes");

$sheet = $objPHPExcel->getActiveSheet();
// Título de la hoja, limitado a 31 caracteres
$sheet->setTitle(preg_replace('/[*\:\/?\[\]]/', '', substr($reportTitle, 0, 31))); 

// ---------------------------------------------------------
// CABECERA DEL REPORTE (TÍTULOS Y FILTROS)
// ---------------------------------------------------------

// Título Principal
$sheet->setCellValue('A1', 'AMAZON LOGISTICS');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF0066CC');
$sheet->setCellValue('B1', $reportTitle);
$sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);

// Fecha
$sheet->setCellValue('B2', 'Generado el: ' . $currentDate);
$sheet->getStyle('B2')->getFont()->getColor()->setARGB('FF666666');

// Filtros
$currentRow = 3;
if (isset($_SESSION['applied_filters']) && !empty($_SESSION['applied_filters'])) {
    $filterTexts = array();
    foreach ($_SESSION['applied_filters'] as $filter) {
        $val = is_array($filter['value']) ? implode(', ', $filter['value']) : $filter['value'];
        $filterTexts[] = $filter['label'] . ': ' . $val;
    }
    $sheet->setCellValue('A' . $currentRow, 'Filtros aplicados: ' . implode(' | ', $filterTexts));
    $sheet->mergeCells('A' . $currentRow . ':' . PHPExcel_Cell::stringFromColumnIndex(count($columns) - 1) . $currentRow);
    $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true)->getColor()->setARGB('FF666666');
    $currentRow++;
} elseif (isset($_SESSION['user_selected_date_filter_al'])) {
    $sheet->setCellValue('A' . $currentRow, 'Filtros aplicados: Al ' . $_SESSION['user_selected_date_filter_al']);
    $sheet->mergeCells('A' . $currentRow . ':' . PHPExcel_Cell::stringFromColumnIndex(count($columns) - 1) . $currentRow);
    $sheet->getStyle('A' . $currentRow)->getFont()->setItalic(true)->getColor()->setARGB('FF666666');
    $currentRow++;
}

$currentRow++; // Espacio antes de la tabla

// ---------------------------------------------------------
// ENCABEZADOS DE COLUMNAS
// ---------------------------------------------------------
$colIndex = 0;
foreach ($columns as $column) {
    $colLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex);
    $sheet->setCellValue($colLetter . $currentRow, $column);
    
    // Estilos para cabecera
    $style = $sheet->getStyle($colLetter . $currentRow);
    $style->getFont()->setBold(true);
    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
    $style->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $colIndex++;
}

// ---------------------------------------------------------
// DATOS DE LA TABLA
// ---------------------------------------------------------
$startDataRow = $currentRow + 1;
$currentRow++;

foreach ($results as $row) {
    $colIndex = 0;
    foreach ($columns as $column) {
        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex);
        $value = isset($row[$column]) ? $row[$column] : '';
        
        // 1. Limpieza de HTML
        if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
            $valueLower = strtolower($value);
            // Si es imagen
            if (strpos($valueLower, '<img') !== false) {
                // Verificar si está dentro de un enlace
                if (preg_match('/<a[^>]*href="([^"]*)"[^>]*>.*?<\/a>/i', $value, $matches)) {
                    // Extraer texto del enlace
                    if (preg_match('/>([^<]*)<\/a>/i', $value, $contentMatches) && !empty($contentMatches[1]) && $contentMatches[1] != '<img') {
                        $value = trim($contentMatches[1]);
                    } else {
                        // Intentar extraer el atributo title de la imagen
                        if (preg_match('/title="([^"]*)"/i', $value, $titleMatch)) {
                            $value = trim($titleMatch[1]);
                        } else {
                            $value = $matches[1]; // url si todo lo demas falla
                        }
                    }
                } else {
                    // Tratar de sacar el title de la imagen o alt
                    if (preg_match('/title="([^"]*)"/i', $value, $titleMatch)) {
                        $value = trim($titleMatch[1]);
                    } else {
                        $value = '[IMAGEN]';
                    }
                }
            } 
            // Si es un enlace puro
            else if (strpos($valueLower, '<a') !== false) {
                if (preg_match('/>([^<]*)<\/a>/i', $value, $matches) && !empty(trim($matches[1]))) {
                    $value = trim($matches[1]);
                } else {
                    $value = strip_tags($value);
                }
            } 
            // Limpiar todo lo demás
            else {
                $value = strip_tags($value);
            }
            
            // Decodificar entidades HTML como &nbsp;
            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            $value = trim(str_replace(array("&nbsp;", "\xc2\xa0"), " ", $value));
        }
        
        // 2. Detección y formato de números
        $isNumber = false;
        $numValue = 0;
        
        // Caso específico mencionado en print.php
        if ($value === '2990,58') {
            $isNumber = true;
            $numValue = 2990.58;
        } 
        // Formatos con comas y puntos
        else if (is_string($value) && preg_match('/^[\d.,\-]+$/', trim($value))) {
            $cleanValue = trim($value);
            
            // Formato europeo (2.990,58) o coma para decimal
            if (strpos($cleanValue, ',') !== false && strpos($cleanValue, '.') !== false) {
                if (strpos($cleanValue, '.') < strpos($cleanValue, ',')) {
                    $cleanValue = str_replace('.', '', $cleanValue); // quitar puntos de miles
                    $cleanValue = str_replace(',', '.', $cleanValue); // coma a punto decimal
                } else {
                    // 2,990.58
                    $cleanValue = str_replace(',', '', $cleanValue);
                }
            }
            // Formato solo con coma decimal (2990,58)
            else if (strpos($cleanValue, ',') !== false) {
                $cleanValue = str_replace(',', '.', $cleanValue);
            }
            
            if (is_numeric($cleanValue) && $cleanValue != '') {
                $isNumber = true;
                $numValue = floatval($cleanValue);
            }
        } 
        else if (is_numeric(str_replace(',', '', trim($value))) && trim($value) != '') {
            // Manejar string como "1,234.56" o "1234.56"
            $cleanValue = str_replace(',', '', trim($value));
            if (is_numeric($cleanValue)) {
                $isNumber = true;
                $numValue = floatval($cleanValue);
            }
        }
        
        // 3. Asignación de celda y estilo
        if ($isNumber) {
            $sheet->setCellValueExplicit($colLetter . $currentRow, $numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            
            // Determinar decimales según config o por defecto 2
            $decimals = 2;
            if (isset($formatInfo[$column]) && isset($formatInfo[$column]['decimals'])) {
                $decimals = $formatInfo[$column]['decimals'];
            }
            
            $formatCode = '#,##0.00';
            if ($decimals == 0) $formatCode = '#,##0';
            
            $sheet->getStyle($colLetter . $currentRow)->getNumberFormat()->setFormatCode($formatCode);
        } else {
            // Tratar como string
            // Si el texto parece un número muy largo (ej. códigos de barras o RFC), forzar a texto
            if (is_numeric($value) && strlen($value) > 11) {
                $sheet->setCellValueExplicit($colLetter . $currentRow, $value, PHPExcel_Cell_DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue($colLetter . $currentRow, $value);
            }
        }
        
        // Bordes de la celda de datos
        $sheet->getStyle($colLetter . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
        $colIndex++;
    }
    $currentRow++;
}

// ---------------------------------------------------------
// AUTOAJUSTE DE COLUMNAS
// ---------------------------------------------------------
// Iteramos sobre las columnas para ajustar su ancho
for ($i = 0; $i < count($columns); $i++) {
    $colLetter = PHPExcel_Cell::stringFromColumnIndex($i);
    // AutoSize
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}

// ---------------------------------------------------------
// GUARDAR Y EXPORTAR
// ---------------------------------------------------------
// Limpiar buffer de salida por si algo se imprimió
if (ob_get_length() > 0) {
    ob_end_clean();
}

$filename = str_replace(' ', '_', $reportTitle) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1'); // IF IE 9
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;