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
$results = [];
$columns = [];
$error = '';
$appliedFilters = [];

// Recuperar resultados de la sesión
if (isset($_SESSION['query_results']) && isset($_SESSION['query_columns'])) {
    $results = $_SESSION['query_results'];
    $columns = $_SESSION['query_columns'];
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

// Fecha actual para el reporte
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
            
            /* Forzar los saltos de página */
            .print-table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
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
                    $filterTexts = [];
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
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <td>
                            <?php 
                                $value = isset($row[$column]) ? $row[$column] : '';
                                
                                // Detectar si parece contener HTML
                                if (preg_match('/<[a-z][\s\S]*>/i', $value) && 
                                    (strpos($value, '<img') !== false || 
                                     strpos($value, '<a') !== false || 
                                     strpos($value, '<center') !== false ||
                                     strpos($value, '<div') !== false)) {
                                    // Es HTML, mostrarlo como tal
                                    echo $value;
                                } else {
                                    // No es HTML, escapar como texto normal
                                    echo htmlspecialchars($value);
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
        });
    </script>
</body>
</html>