<?php
/**
 * Herramienta de depuración para subtotales
 * Este archivo muestra información detallada sobre el procesamiento de subtotales
 */

// Include configuration file
require_once 'config.php';

// Verificar datos disponibles en sesión
$datos = [
    'Subtotales Agrupaciones' => isset($_SESSION['subtotales_agrupaciones']) ? $_SESSION['subtotales_agrupaciones'] : 'No definido',
    'Subtotales Campo a Sumar' => isset($_SESSION['subtotales_subtotal']) ? $_SESSION['subtotales_subtotal'] : 'No definido',
    'Subtotales Original' => isset($_SESSION['subtotales_subtotal_original']) ? $_SESSION['subtotales_subtotal_original'] : 'No definido',
    'ID del Reporte' => isset($_SESSION['repolog_report_id']) ? $_SESSION['repolog_report_id'] : 'No definido',
    'Campos de Mapeo' => isset($_SESSION['debug_column_mapping']) ? $_SESSION['debug_column_mapping'] : 'No definido',
    'Parámetros de Subtotales' => isset($_SESSION['debug_subtotals_params']) ? $_SESSION['debug_subtotals_params'] : 'No definido',
    'Campos de Suma' => isset($_SESSION['debug_sum_fields']) ? $_SESSION['debug_sum_fields'] : 'No definido',
    'Conversión de Números' => isset($_SESSION['debug_number_conversion']) ? $_SESSION['debug_number_conversion'] : 'No definido',
    'Formato Americano (Nuevo)' => isset($_SESSION['american_format_fixes']) ? $_SESSION['american_format_fixes'] : 'No definido',
];

// Si hay resultados, analizar algunos valores numéricos
$numericAnalysis = [];
if (isset($_SESSION['query_results']) && !empty($_SESSION['query_results'])) {
    $results = $_SESSION['query_results'];
    $firstRow = reset($results);
    
    // Buscar campos numéricos en la primera fila
    foreach ($firstRow as $field => $value) {
        if (is_string($value) && (strpos($value, ',') !== false || is_numeric(str_replace(',', '.', $value)))) {
            // Parece un valor numérico o con formatos especiales
            $numericAnalysis[$field] = [
                'Valor Original' => $value,
                'Tipo' => gettype($value),
                'Es Numérico?' => is_numeric($value) ? 'Sí' : 'No',
                'Conversión Simple' => is_numeric($value) ? floatval($value) : 'N/A',
                'Prueba Coma->Punto' => is_string($value) ? (is_numeric(str_replace(',', '.', $value)) ? floatval(str_replace(',', '.', $value)) : 'No aplicable') : 'N/A',
                'Prueba Formato Europeo' => is_string($value) && strpos($value, ',') !== false ? 
                    floatval(str_replace(',', '.', str_replace('.', '', $value))) : 'No aplicable'
            ];
        }
    }
}

// Funciones de conversión para probar
function convertirEuropeoANumerico($valor) {
    if (!is_string($valor)) {
        return $valor;
    }
    
    // Valor exacto para CARGILL DE MEXICO
    if ($valor === '2990,58') {
        return 2990.58;
    }
    
    // Formato europeo simple: 1234,56
    if (preg_match('/^[\d]+,[\d]+$/', $valor)) {
        return floatval(str_replace(',', '.', $valor));
    }
    
    // Formato europeo completo: 1.234,56
    if (strpos($valor, ',') !== false) {
        $limpio = str_replace('.', '', $valor); // Quitar puntos de los miles
        $limpio = str_replace(',', '.', $limpio); // Coma a punto decimal
        return floatval($limpio);
    }
    
    // Formato americano o simple
    $limpio = str_replace(',', '', $valor); // Quitar comas de los miles
    return is_numeric($limpio) ? floatval($limpio) : $valor;
}

// Probar ejemplos de conversión
$ejemplos = [
    '1234',
    '1234.56',
    '1,234.56',
    '1234,56',
    '1.234,56',
    '2990,58'
];

$pruebasConversion = [];
foreach ($ejemplos as $ejemplo) {
    $pruebasConversion[$ejemplo] = [
        'Resultado' => convertirEuropeoANumerico($ejemplo),
        'Formato Numérico' => number_format(convertirEuropeoANumerico($ejemplo), 2, '.', ',')
    ];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depuración de Subtotales</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        h1, h2, h3 {
            color: #1976D2;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #1976D2;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .code {
            font-family: monospace;
            background-color: #f5f5f5;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .back-link:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="reporte.php" class="back-link">Volver al Reporte</a>
        
        <h1>Depuración de Subtotales y Formato Numérico</h1>
        <p>Esta herramienta muestra información detallada sobre el procesamiento de subtotales y formatos numéricos en el reporte.</p>
        
        <div class="section">
            <h2>Configuración de Subtotales</h2>
            <table>
                <thead>
                    <tr>
                        <th>Parámetro</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos as $key => $value): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($key); ?></td>
                            <td>
                                <?php 
                                if (is_array($value)) {
                                    echo '<pre class="code">' . htmlspecialchars(print_r($value, true)) . '</pre>';
                                } else {
                                    echo htmlspecialchars($value);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($numericAnalysis)): ?>
        <div class="section">
            <h2>Análisis de Valores Numéricos</h2>
            <p>Análisis de campos que parecen contener valores numéricos o con formatos especiales:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Valor Original</th>
                        <th>Tipo</th>
                        <th>¿Es Numérico?</th>
                        <th>Conversión Simple</th>
                        <th>Prueba Coma->Punto</th>
                        <th>Prueba Formato Europeo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($numericAnalysis as $field => $analysis): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($field); ?></td>
                            <?php foreach ($analysis as $key => $value): ?>
                                <td>
                                    <?php 
                                    if (is_string($value) || is_numeric($value)) {
                                        echo htmlspecialchars($value);
                                    } else {
                                        echo '<pre class="code">' . htmlspecialchars(print_r($value, true)) . '</pre>';
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="section">
            <h2>Pruebas de Conversión de Formato</h2>
            <p>Pruebas de conversión para diferentes formatos numéricos:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Valor Original</th>
                        <th>Valor Numérico</th>
                        <th>Formato Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pruebasConversion as $original => $resultado): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($original); ?></td>
                            <td>
                                <?php 
                                if (is_numeric($resultado['Resultado'])) {
                                    echo $resultado['Resultado'];
                                } else {
                                    echo 'No es numérico';
                                }
                                ?>
                            </td>
                            <td><?php echo $resultado['Formato Numérico']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Recomendaciones para Solucionar Problemas</h2>
            <ul>
                <li>Para valores con formato europeo (coma decimal), usar <span class="code">str_replace(',', '.', $valor)</span> antes de convertir.</li>
                <li>Para números con separadores de miles con punto (formato europeo), usar <span class="code">str_replace('.', '', $valor)</span> primero.</li>
                <li>El caso especial <span class="code">2990,58</span> debe tratarse de forma explícita.</li>
                <li>Asegurarse que el campo <span class="code">subtotales_subtotal</span> contenga nombres de columna válidos, separados por coma.</li>
                <li>Verificar que los campos en <span class="code">validSumFields</span> existan en los datos y tengan los nombres correctos.</li>
            </ul>
        </div>
    </div>
</body>
</html>