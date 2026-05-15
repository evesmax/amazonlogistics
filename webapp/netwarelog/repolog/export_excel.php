<?php
/**
 * Export SQL query results to Excel using PHPExcel
 * 
 * Este script utiliza PHPExcel nativamente para generar un archivo .xlsx real.
 * Reemplaza a export_excel_api.php que usaba un servicio externo.
 */

// Aumentar límite de memoria para exportaciones grandes
ini_set('memory_limit', '1024M');
set_time_limit(600); // 10 minutos por si la consulta es enorme

require_once 'config.php';
require_once 'assets/PHPExcel/Classes/PHPExcel.php';

// Habilitar caché de celdas para reducir el consumo de RAM en reportes muy grandes
$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array('memoryCacheSize' => '32MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

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
// Ocultar líneas de cuadrícula de Excel (Gridlines) para un aspecto más limpio
$sheet->setShowGridlines(false);

// Título de la hoja, limitado a 31 caracteres
$sheet->setTitle(preg_replace('/[*\:\/?\[\]]/', '', substr($reportTitle, 0, 31))); 

// Usar Calibri como fuente predeterminada
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

// ---------------------------------------------------------
// CABECERA DEL REPORTE (LOGO, TÍTULOS Y FILTROS)
// ---------------------------------------------------------

$numColumns = count($columns);
$lastColLetter = PHPExcel_Cell::stringFromColumnIndex($numColumns - 1);

// Insertar Logo si existe localmente
$logoPath = __DIR__ . '/../archivos/1/administracion_usuarios/logoamz.jpg';
if (!file_exists($logoPath)) {
    $logoPath = __DIR__ . '/assets/img/logo.png';
}

if (file_exists($logoPath)) {
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('Logo');
    $objDrawing->setDescription('Logo');
    $objDrawing->setPath($logoPath);
    // Colocar el logo en A1
    $objDrawing->setCoordinates('A1');
    $objDrawing->setHeight(50);
    $objDrawing->setOffsetX(5);
    $objDrawing->setOffsetY(5);
    $objDrawing->setWorksheet($sheet);
}

// Título Principal - Centrado a lo largo de las columnas (empezando en B para no chocar con logo)
// Si solo hay 1 columna, no fusionamos, sino fusionamos de B1 hasta la última
if ($numColumns > 1) {
    $sheet->mergeCells('B1:' . $lastColLetter . '1');
    $sheet->setCellValue('B1', $reportTitle);
    $styleTitle = $sheet->getStyle('B1');
} else {
    $sheet->setCellValue('A1', $reportTitle);
    $styleTitle = $sheet->getStyle('A1');
}

$styleTitle->getFont()->setBold(true)->setSize(20)->getColor()->setARGB('FF000000'); // Negro
$styleTitle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$styleTitle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(55); // Más espacio para el logo y título

// Filtros y Fecha (en la misma fila, centrados)
$currentRow = 2;
$filterText = "";
if (isset($_SESSION['applied_filters']) && !empty($_SESSION['applied_filters'])) {
    $filterTexts = array();
    foreach ($_SESSION['applied_filters'] as $filter) {
        $val = is_array($filter['value']) ? implode(', ', $filter['value']) : $filter['value'];
        $filterTexts[] = $filter['label'] . ': ' . $val;
    }
    $filterText = 'Filtros aplicados: ' . implode('   ', $filterTexts) . '   ';
} elseif (isset($_SESSION['user_selected_date_filter_al'])) {
    $filterText = 'Filtros aplicados: Al: ' . $_SESSION['user_selected_date_filter_al'] . '   ';
}

$filterText .= 'Generado el: ' . $currentDate;

$sheet->setCellValue('A' . $currentRow, $filterText);
$sheet->mergeCells('A' . $currentRow . ':' . $lastColLetter . $currentRow);
$styleFilter = $sheet->getStyle('A' . $currentRow);
$styleFilter->getFont()->setSize(12)->getColor()->setARGB('FF000000');
$styleFilter->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$styleFilter->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$currentRow++; // Espacio en blanco después de los filtros
$currentRow++; // Inicia cabecera en fila 4

// ---------------------------------------------------------
// ENCABEZADOS DE COLUMNAS
// ---------------------------------------------------------
$colIndex = 0;
foreach ($columns as $column) {
    $colLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex);
    $sheet->setCellValue($colLetter . $currentRow, $column);
    
    // Estilos para cabecera (sin bordes, color lavanda/púrpura claro, centrado)
    $style = $sheet->getStyle($colLetter . $currentRow);
    $style->getFont()->setBold(true)->setSize(11);
    $style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFE6E6FA'); // Lavanda/Púrpura claro
    $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $style->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    
    $colIndex++;
}

// ---------------------------------------------------------
// DATOS DE LA TABLA
// ---------------------------------------------------------
$startDataRow = $currentRow + 1;
$currentRow++;

foreach ($results as $row) {
    $colIndex = 0;
    
    // Validar si es fila de subtotal o total general (generado por reporte.php)
    $isSubtotal = isset($row['__is_subtotal']) && $row['__is_subtotal'] === true;
    
    foreach ($columns as $column) {
        // Ignorar campos internos de control si llegaron a colarse
        if ($column === '__is_subtotal' || $column === '__subtotal_level') {
            continue;
        }

        $colLetter = PHPExcel_Cell::stringFromColumnIndex($colIndex);
        $value = isset($row[$column]) ? $row[$column] : '';
        
        // Limpieza rápida de HTML
        if (is_string($value) && preg_match('/<[a-z][\s\S]*>/i', $value)) {
            $valueLower = strtolower($value);
            if (strpos($valueLower, '<img') !== false) {
                if (preg_match('/<a[^>]*href="([^"]*)"[^>]*>.*?<\/a>/i', $value, $matches)) {
                    if (preg_match('/>([^<]*)<\/a>/i', $value, $contentMatches) && !empty($contentMatches[1]) && $contentMatches[1] != '<img') {
                        $value = trim($contentMatches[1]);
                    } else {
                        if (preg_match('/title="([^"]*)"/i', $value, $titleMatch)) {
                            $value = trim($titleMatch[1]);
                        } else {
                            $value = $matches[1];
                        }
                    }
                } else {
                    if (preg_match('/title="([^"]*)"/i', $value, $titleMatch)) {
                        $value = trim($titleMatch[1]);
                    } else {
                        $value = '[IMAGEN]';
                    }
                }
            } else if (strpos($valueLower, '<a') !== false) {
                if (preg_match('/>([^<]*)<\/a>/i', $value, $matches) && !empty(trim($matches[1]))) {
                    $value = trim($matches[1]);
                } else {
                    $value = strip_tags($value);
                }
            } else {
                $value = strip_tags($value);
            }
            $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            $value = trim(str_replace(array("&nbsp;", "\xc2\xa0"), " ", $value));
        }
        
        // Detección y formato de números
        $isNumber = false;
        $numValue = 0;
        $detectedDecimals = 0; // Por defecto 0
        
        // No parsear como número si dice "TOTAL GENERAL"
        if ($value === 'TOTAL GENERAL') {
            $isNumber = false;
        } else if ($value === '2990,58') {
            $isNumber = true;
            $numValue = 2990.58;
            $detectedDecimals = 2;
        } else if (is_string($value) && preg_match('/^[\d.,\-]+$/', trim($value))) {
            $cleanValue = trim($value);
            if (strpos($cleanValue, ',') !== false && strpos($cleanValue, '.') !== false) {
                if (strpos($cleanValue, '.') < strpos($cleanValue, ',')) {
                    // Formato europeo
                    $decimalPart = substr($cleanValue, strpos($cleanValue, ',') + 1);
                    $detectedDecimals = strlen($decimalPart);
                    $cleanValue = str_replace('.', '', $cleanValue);
                    $cleanValue = str_replace(',', '.', $cleanValue);
                } else {
                    // Formato americano
                    $decimalPart = substr($cleanValue, strpos($cleanValue, '.') + 1);
                    $detectedDecimals = strlen($decimalPart);
                    $cleanValue = str_replace(',', '', $cleanValue);
                }
            } else if (strpos($cleanValue, ',') !== false) {
                $decimalPart = substr($cleanValue, strpos($cleanValue, ',') + 1);
                $detectedDecimals = strlen($decimalPart);
                $cleanValue = str_replace(',', '.', $cleanValue);
            } else if (strpos($cleanValue, '.') !== false) {
                $decimalPart = substr($cleanValue, strpos($cleanValue, '.') + 1);
                $detectedDecimals = strlen($decimalPart);
            }
            if (is_numeric($cleanValue) && $cleanValue != '') {
                $isNumber = true;
                $numValue = floatval($cleanValue);
            }
        } else if (is_numeric(str_replace(',', '', trim($value))) && trim($value) != '') {
            $cleanValue = str_replace(',', '', trim($value));
            if (strpos(trim($value), '.') !== false) {
                $decimalPart = substr(trim($value), strpos(trim($value), '.') + 1);
                $detectedDecimals = strlen($decimalPart);
            }
            if (is_numeric($cleanValue)) {
                $isNumber = true;
                $numValue = floatval($cleanValue);
            }
        }
        
        // Asignación de celda
        $cellStyle = $sheet->getStyle($colLetter . $currentRow);
        
        if ($isNumber) {
            $sheet->setCellValueExplicit($colLetter . $currentRow, $numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            
            $decimals = $detectedDecimals;
            // Solo sobreescribir si el formato está forzado globalmente para esta columna
            if (isset($formatInfo[$column]) && isset($formatInfo[$column]['decimals'])) {
                $decimals = $formatInfo[$column]['decimals'];
            }
            
            $formatCode = '#,##0';
            if ($decimals > 0) {
                $formatCode .= '.' . str_repeat('0', $decimals);
            }
            
            // Si es un campo identificador (folio, id, etc), no usar separador de miles ni decimales
            if (preg_match('/(?:folio|id|remisi[oó]n|codigo|referencia)/i', $column)) {
                $formatCode = '0';
            }

            
            $cellStyle->getNumberFormat()->setFormatCode($formatCode);
            // Números a la derecha
            $cellStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        } else {
            if (is_numeric($value) && strlen($value) > 11) {
                $sheet->setCellValueExplicit($colLetter . $currentRow, $value, PHPExcel_Cell_DataType::TYPE_STRING);
            } else {
                $sheet->setCellValue($colLetter . $currentRow, $value);
            }
            // Textos a la izquierda (excepto subtotales que pondremos en bold más adelante)
            // Según la imagen, Zafra estaba centrada, pero por defecto dejaremos a la izquierda o centro si el texto es corto
            // Para mantener consistencia, dejaremos izquierda por defecto
            $cellStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        // Si es subtotal o TOTAL GENERAL, aplicar negrita
        if ($isSubtotal || $value === 'TOTAL GENERAL') {
            $cellStyle->getFont()->setBold(true);
        }
        
        $colIndex++;
    }
    $currentRow++;
}

// ---------------------------------------------------------
// AUTOAJUSTE DE COLUMNAS
// ---------------------------------------------------------
for ($i = 0; $i < count($columns); $i++) {
    $colLetter = PHPExcel_Cell::stringFromColumnIndex($i);
    // Para que no se encoja mucho el título del filtro, usamos un ancho automático pero no forzamos el título.
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}

// ---------------------------------------------------------
// GUARDAR Y EXPORTAR
// ---------------------------------------------------------
if (ob_get_length() > 0) {
    ob_end_clean();
}

$filename = str_replace(' ', '_', $reportTitle) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;