<?php
/**
 * Prueba de formato de números
 * Este archivo permite verificar diferentes soluciones para el formato de números
 */

// Include configuration file
require_once 'config.php';

// Valores de ejemplo
$valores = [
    "2990,58", 
    "2.990,58", 
    "2,990.58", 
    2990.58,
    "CARGILL DE MEXICO: 2990,58"
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Formato de Números</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #f8f8f8;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        section {
            margin-bottom: 30px;
            padding: 15px;
            background: #fff;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }
        
        h1, h2 {
            color: #333;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
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
        
        .formatted {
            font-weight: bold;
            color: #2196F3;
        }
        
        code {
            background: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Prueba de Formato de Números</h1>
        
        <div class="back-container" style="margin-bottom: 20px;">
            <a href="repolog.php?i=1" class="back-btn">Regresar a Reportes</a>
        </div>
        
        <section>
            <h2>1. Formato con PHP</h2>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Valor Original</th>
                            <th>Tipo</th>
                            <th>Usando number_format()</th>
                            <th>Usando str_replace()</th>
                            <th>Caso Especial 2990,58</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($valores as $valor): ?>
                            <tr>
                                <td><?php echo $valor; ?></td>
                                <td><code><?php echo gettype($valor); ?></code></td>
                                <td>
                                    <?php 
                                    // Intentar con number_format
                                    if (is_numeric($valor)) {
                                        echo '<span class="formatted">' . number_format($valor, 2, '.', ',') . '</span>';
                                    } else {
                                        // Si es string, intentar convertir
                                        $numerico = str_replace(',', '.', $valor);
                                        if (is_numeric($numerico)) {
                                            echo '<span class="formatted">' . number_format(floatval($numerico), 2, '.', ',') . '</span>';
                                        } else {
                                            echo "No aplicable";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (is_string($valor)): ?>
                                        <?php 
                                        // Detectar formato europeo (2.990,58) y convertir a mexicano (2,990.58)
                                        if (strpos($valor, ',') !== false) {
                                            $limpio = str_replace('.', '', $valor); // Quitar puntos (separadores de miles)
                                            $limpio = str_replace(',', '.', $limpio); // Convertir comas a puntos (decimales)
                                            
                                            if (is_numeric($limpio)) {
                                                $formateado = number_format(floatval($limpio), 2, '.', ',');
                                                echo '<span class="formatted">' . $formateado . '</span>';
                                            } else {
                                                echo "No aplicable";
                                            }
                                        } else {
                                            echo "No aplicable";
                                        }
                                        ?>
                                    <?php else: ?>
                                        No aplicable
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    // Caso especial: Detectar '2990,58' exactamente
                                    if (is_string($valor) && strpos($valor, '2990,58') !== false) {
                                        echo '<span class="formatted">2,990.58</span>';
                                    } else {
                                        echo "No aplica";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section>
            <h2>2. Formato con JavaScript</h2>
            
            <p>Tabla con valores originales que serán procesados por JavaScript:</p>
            
            <div class="table-container">
                <table id="js-test-table">
                    <thead>
                        <tr>
                            <th>Valor Original</th>
                            <th>Usando toLocaleString()</th>
                            <th>Expresión Regular</th>
                            <th>Caso Especial</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($valores as $valor): ?>
                            <tr>
                                <td class="original-value"><?php echo $valor; ?></td>
                                <td class="locale-string"></td>
                                <td class="regex-format"></td>
                                <td class="special-case"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        
        <section>
            <h2>3. Formato con SQL (MySQL)</h2>
            
            <p>Valores formateados directamente desde MySQL:</p>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Valor Original</th>
                            <th>Usando FORMAT()</th>
                            <th>Usando REPLACE()</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            foreach ($valores as $valor) {
                                if (is_string($valor) && strpos($valor, 'CARGILL') !== false) {
                                    // Omitir valores de texto complejos
                                    continue;
                                }
                                
                                // Calcular valor para MySQL
                                $valorSQL = is_string($valor) ? str_replace(',', '.', $valor) : $valor;
                                
                                echo "<tr>";
                                echo "<td>{$valor}</td>";
                                
                                // Usar FORMAT() de MySQL
                                $stmt = $pdo->prepare("SELECT FORMAT(?, 2) AS formato");
                                $stmt->execute([$valorSQL]);
                                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                echo "<td><span class='formatted'>" . $resultado['formato'] . "</span></td>";
                                
                                // Usar REPLACE() de MySQL
                                $stmt = $pdo->prepare("SELECT REPLACE(REPLACE(CAST(? AS CHAR), '.', '@'), ',', '.') AS formato");
                                $stmt->execute([$valorSQL]);
                                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                                $formato = str_replace('@', ',', $resultado['formato']);
                                
                                echo "<td><span class='formatted'>" . $formato . "</span></td>";
                                
                                echo "</tr>";
                            }
                            
                            $pdo = null;
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='3'>Error de conexión: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Procesar todas las filas de la tabla
        var rows = document.querySelectorAll('#js-test-table tbody tr');
        
        rows.forEach(function(row) {
            var originalCell = row.querySelector('.original-value');
            var originalText = originalCell.textContent.trim();
            
            // Celda de toLocaleString()
            var localeCell = row.querySelector('.locale-string');
            
            // Verificar si es un número o puede convertirse a número
            var numValue = parseFloat(originalText.replace(',', '.'));
            if (!isNaN(numValue)) {
                localeCell.innerHTML = '<span class="formatted">' + 
                    numValue.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + '</span>';
            } else {
                localeCell.textContent = "No aplicable";
            }
            
            // Celda de expresión regular
            var regexCell = row.querySelector('.regex-format');
            
            // Verificar si coincide con el patrón de números con coma decimal
            if (/^[\d]+,[\d]+$/.test(originalText)) {
                var value = parseFloat(originalText.replace(',', '.'));
                regexCell.innerHTML = '<span class="formatted">' + 
                    value.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + '</span>';
            } else {
                regexCell.textContent = "No aplicable";
            }
            
            // Celda de caso especial
            var specialCell = row.querySelector('.special-case');
            
            // Verificar si contiene '2990,58'
            if (originalText.indexOf('2990,58') !== -1) {
                specialCell.innerHTML = '<span class="formatted">2,990.58</span>';
            } else {
                specialCell.textContent = "No aplicable";
            }
        });
    });
    </script>
</body>
</html>