<?php
/**
 * Export SQL query results to Excel using PHPExcel with template
 * 
 * Este script usa un template Excel con logo y encabezado predefinido
 * y agrega los datos dinámicamente
 * 
 * Compatible con PHP 5.5.9
 */

// Aumentar límite de memoria para exportaciones grandes
ini_set('memory_limit', '256M');

// Include configuration files
require_once 'config.php';

// Include PHPExcel
require_once 'assets/PHPExcel/Classes/PHPExcel.php';

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

// Cargar el template Excel
$templatePath = 'assets/templates/template.xls';

try {
    // Cargar el template
    $objPHPExcel = PHPExcel_IOFactory::load($templatePath);
    
    // Obtener la hoja activa
    $sheet = $objPHPExcel->getActiveSheet();
    
    // Buscar la primera fila vacía para empezar a insertar datos
    // El template tiene el logo y encabezado, buscamos dónde termina
    $startRow = 1;
    $maxSearchRows = 20; // Buscar en las primeras 20 filas
    
    for ($i = 1; $i <= $maxSearchRows; $i++) {
        $cellValue = $sheet->getCellByColumnAndRow(0, $i)->getValue();
        // Si encontramos una celda que dice "Datos" o está vacía después de contenido
        if (empty($cellValue) && $i > 5) {
            $startRow = $i;
            break;
        }
    }
    
    // Si no encontramos fila vacía, usar fila 8 por defecto (después de logo y encabezados típicos)
    if ($startRow == 1) {
        $startRow = 8;
    }
    
    // Actualizar información dinámica si existen celdas específicas en el template
    // Intentar actualizar título (buscar celda con "Título" o similar)
    for ($i = 1; $i <= 10; $i++) {
        for ($j = 0; $j < 5; $j++) {
            $cellValue = $sheet->getCellByColumnAndRow($j, $i)->getValue();
            if (stripos($cellValue, 'Existencias') !== false || stripos($cellValue, 'Reporte') !== false) {
                $sheet->setCellValueByColumnAndRow($j, $i, $reportTitle);
                break 2;
            }
        }
    }
    
    // Agregar headers de columnas
    $col = 0;
    foreach ($columns as $columnName) {
        $sheet->setCellValueByColumnAndRow($col, $startRow, $columnName);
        // Aplicar estilo de encabezado
        $cellCoord = PHPExcel_Cell::stringFromColumnIndex($col) . $startRow;
        $sheet->getStyle($cellCoord)->getFont()->setBold(true);
        $sheet->getStyle($cellCoord)->getFill()
              ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
              ->getStartColor()->setRGB('F2F2F2');
        $col++;
    }
    
    // Agregar datos
    $row = $startRow + 1;
    foreach ($results as $dataRow) {
        $col = 0;
        foreach ($columns as $columnName) {
            $value = isset($dataRow[$columnName]) ? $dataRow[$columnName] : '';
            
            // Limpiar HTML si existe
            if (preg_match('/<[a-z][\s\S]*>/i', $value)) {
                $value = strip_tags($value);
            }
            
            // Verificar si es un número
            if (is_numeric($value)) {
                $sheet->setCellValueByColumnAndRow($col, $row, floatval($value));
                // Aplicar formato de número con 2 decimales
                $cellCoord = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
                $sheet->getStyle($cellCoord)->getNumberFormat()
                      ->setFormatCode('#,##0.00');
            } else {
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
            }
            $col++;
        }
        $row++;
    }
    
    // Auto-ajustar ancho de columnas
    foreach (range(0, count($columns) - 1) as $col) {
        $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
    }
    
    // Configurar headers para descarga
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . str_replace(' ', '_', $reportTitle) . '_' . date('Y-m-d_H-i-s') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Guardar el archivo Excel
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    
    exit;
    
} catch (Exception $e) {
    die('Error al generar el archivo Excel: ' . $e->getMessage());
}
?>
