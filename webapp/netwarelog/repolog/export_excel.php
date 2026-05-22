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

// Analizar dinámicamente qué columnas deben ser tratadas estrictamente como texto/identificadores
// Esto evita usar nombres de columnas fijos y se basa puramente en los datos
$sumFields = array();
$subtotalesSubtotal = isset($_SESSION['subtotales_subtotal']) ? $_SESSION['subtotales_subtotal'] : '';
if (!empty($subtotalesSubtotal)) {
    $sumFields = array_map('trim', explode(',', $subtotalesSubtotal));
}

// Obtener también los campos de suma ya mapeados de la sesión para evitar problemas de nombres SQL vs nombres de columnas
$mappedSumFields = array();
if (isset($_SESSION['debug_column_mapping']) && isset($_SESSION['debug_column_mapping']['valid_sum_fields'])) {
    $mappedSumFields = $_SESSION['debug_column_mapping']['valid_sum_fields'];
}

$textColumns = array();
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
        $decimals = 0;
        
        // Determinar si la columna es numérico verificada
        $isVerifiedNumericColumn = false;
        if (isset($formatInfo[$column]) && isset($formatInfo[$column]['has_format']) && $formatInfo[$column]['has_format']) {
            $isVerifiedNumericColumn = true;
            $decimals = isset($formatInfo[$column]['decimals']) ? $formatInfo[$column]['decimals'] : 0;
        } else {
            $normalizedColumn = strtolower(trim($column));
            foreach ($sumFields as $sf) {
                if (strtolower(trim($sf)) === $normalizedColumn) {
                    $isVerifiedNumericColumn = true;
                    $decimals = 2;
                    break;
                }
            }
            if (!$isVerifiedNumericColumn) {
                foreach ($mappedSumFields as $msf) {
                    if (strtolower(trim($msf)) === $normalizedColumn) {
                        $isVerifiedNumericColumn = true;
                        $decimals = 2;
                        break;
                    }
                }
            }
        }

        // El valor no debe ser vacío ni 'TOTAL GENERAL' para ser tratado como número
        if ($value !== 'TOTAL GENERAL' && $value !== '' && $value !== null && trim($value) !== '') {
            if ($isVerifiedNumericColumn) {
                $cleanValue = str_replace(array(',', ' '), '', trim($value));
                if (is_numeric($cleanValue)) {
                    $isNumber = true;
                    $numValue = floatval($cleanValue);
                }
            }
        }
        
        // Asignación de celda
        $cellStyle = $sheet->getStyle($colLetter . $currentRow);
        
        // Determinar si es un campo de ID/Folio o detectado dinámicamente como texto
        $isIdentifier = preg_match('/\bid|id\b|folio|código|codigo|remisión|remision|factura|referencia|\bdoc|documento|origen|destino|porte|carta|placa|operador|transportista|chofer|ruta|vehiculo|vehículo|contenedor|sello|guia|guía|ticket/i', $column) || isset($textColumns[$column]);
        
        if ($isNumber && !$isIdentifier) {
            $sheet->setCellValueExplicit($colLetter . $currentRow, $numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            
            $formatCode = '#,##0';
            if ($decimals > 0) {
                $formatCode .= '.' . str_repeat('0', $decimals);
            }

            $cellStyle->getNumberFormat()->setFormatCode($formatCode);
            // Números a la derecha
            $cellStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        } else {
            // Forzar a TEXTO si NO es un número verificado o si es un identificador
            // Esto evita que PHPExcel o Excel conviertan strings numéricos (como folios, códigos o cartas porte) 
            // a números con comas, notación científica o que eliminen ceros a la izquierda
            $sheet->setCellValueExplicit($colLetter . $currentRow, $value, PHPExcel_Cell_DataType::TYPE_STRING);
            // Textos a la izquierda
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