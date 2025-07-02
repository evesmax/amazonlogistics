/**
 * Formatter para valores numéricos - soporta formatos americano y europeo
 * Este script asegura que todos los números se muestren con el formato correcto:
 * - Punto decimal
 * - Coma para separador de miles
 * - Detecta automáticamente los decimales desde el SQL FORMAT(campo, N)
 * - Solo formatea números que realmente tienen decimales, respetando IDs y folios
 */

// Variable global para almacenar la información de formato de columnas
var columnFormatInfo = {};
// Variable para evitar ejecuciones múltiples
var formattingInProgress = false;
var formattingCompleted = false;

// ELIMINADO: Este DOMContentLoaded se maneja desde table_functions.js
// para evitar ejecuciones múltiples que causan duplicación de datos

// Función principal para formatear números
function formatNumbersInTable() {
    // Evitar ejecuciones múltiples
    if (formattingInProgress || formattingCompleted) {
        console.log("Formateo ya en progreso o completado, saltando ejecución");
        return;
    }
    
    formattingInProgress = true;
    console.log("Ejecutando formatNumbersInTable");
    
    try {
        // NUEVA FUNCIONALIDAD: Obtener información de formato desde PHP
        // Verificar si hay un elemento con la información de formato
        var formatInfoElement = document.getElementById('column-format-info');
        if (formatInfoElement) {
            try {
                // Parsear la información JSON del elemento
                columnFormatInfo = JSON.parse(formatInfoElement.textContent);
                console.log("Información de formato cargada:", columnFormatInfo);
            } catch (e) {
                console.error("Error al parsear información de formato:", e);
            }
        }
        
        // Procesar todas las celdas de la tabla
        var allCells = document.querySelectorAll('#resultsTable td');
        allCells.forEach(function(cell) {
            formatCellContent(cell);
        });
        
        // Verificar todas las filas para formatos especiales
        processSpecialRows();
        
        // Marcar como completado
        formattingCompleted = true;
        console.log("Formateo completado exitosamente");
    } catch (error) {
        console.error("Error durante el formateo:", error);
    } finally {
        formattingInProgress = false;
    }
}

// Función para reinicializar el formateo (llamada cuando se genera un nuevo reporte)
function resetAndFormatNumbers() {
    // Resetear las variables de control
    formattingInProgress = false;
    formattingCompleted = false;
    
    // Limpiar cualquier atributo de formateo previo
    var allCells = document.querySelectorAll('#resultsTable td[data-formatted="true"]');
    allCells.forEach(function(cell) {
        cell.removeAttribute('data-formatted');
    });
    
    // Ejecutar el formateo de nuevo
    setTimeout(formatNumbersInTable, 50);
}

// Función para formatear el contenido de una celda
function formatCellContent(cell) {
    // No procesar celdas que ya han sido formateadas
    if (cell.getAttribute('data-formatted') === 'true') {
        return;
    }
    
    var text = cell.textContent.trim();
    if (!text) return;
    
    // MEJORA: Determinar si es un ID o folio (no aplicar formato)
    // 1. Si la celda está dentro de un enlace HTML, probablemente es un folio o ID - no formatear
    if (cell.innerHTML.indexOf('<a href') !== -1 || 
        cell.innerHTML.indexOf('<A HREF') !== -1 || 
        cell.innerHTML.indexOf('<center>') !== -1 || 
        cell.innerHTML.indexOf('<CENTER>') !== -1) {
        // Es un enlace o un elemento con formato especial, no aplicar formato numérico
        cell.setAttribute('data-formatted', 'true');
        return;
    }
    
    // Obtener información de la columna
    var headerIndex = Array.from(cell.parentNode.children).indexOf(cell);
    var columnName = '';
    var headerCell = null;
    
    if (headerIndex !== -1) {
        headerCell = document.querySelector('#resultsTable thead th:nth-child(' + (headerIndex + 1) + ')');
        if (headerCell) {
            columnName = headerCell.textContent.trim();
            var headerText = columnName.toLowerCase();
            
            // 2. Si el encabezado de columna contiene palabras clave que indican IDs, no formatear
            if (headerText.includes('id') || 
                headerText.includes('folio') || 
                headerText.includes('código') || 
                headerText.includes('codigo') || 
                headerText.includes('referencia') ||
                headerText.includes('num')) {
                // Es una columna de ID o referencia, no aplicar formato
                cell.setAttribute('data-formatted', 'true');
                return;
            }
            
            // 3. NUEVA FUNCIONALIDAD: Verificar si tenemos información de formato desde el SQL para esta columna
            var formatConfig = null;
            
            // Intentar coincidencia exacta por nombre de columna
            if (columnFormatInfo && columnFormatInfo[columnName] && columnFormatInfo[columnName].has_format) {
                formatConfig = columnFormatInfo[columnName];
            }
            // Si no hay coincidencia exacta, intentar con nombres parciales para manejar alias con prefijos o sufijos
            else if (columnFormatInfo) {
                // Recorrer todas las configuraciones de formato
                for (var cfgColName in columnFormatInfo) {
                    if (columnFormatInfo.hasOwnProperty(cfgColName)) {
                        // Verificar si el nombre de la columna actual contiene o está contenido en la configuración
                        if ((columnName.indexOf(cfgColName) !== -1 || cfgColName.indexOf(columnName) !== -1) && 
                            columnFormatInfo[cfgColName].has_format) {
                            formatConfig = columnFormatInfo[cfgColName];
                            console.log("Coincidencia parcial para columna: " + columnName + " con configuración para: " + cfgColName);
                            break;
                        }
                    }
                }
            }
            
            // Si encontramos una configuración, aplicarla
            if (formatConfig) {
                var decimals = formatConfig.decimals;
                
                // Convertir el texto a número según su formato actual
                var number = null;
                
                // Detectar el formato del número y convertirlo apropiadamente
                if (/^\d+$/.test(text)) {
                    // Número entero
                    number = parseInt(text);
                } else if (/^\d+\.\d+$/.test(text)) {
                    // Número decimal americano (1234.56)
                    number = parseFloat(text);
                } else if (/^\d+,\d+$/.test(text)) {
                    // Número decimal europeo simple (1234,56)
                    number = parseFloat(text.replace(',', '.'));
                } else if (/^\d{1,3}(,\d{3})*\.\d+$/.test(text)) {
                    // Número americano con separador de miles (1,234.56)
                    number = parseFloat(text.replace(/,/g, ''));
                } else if (/^\d{1,3}(\.\d{3})*,\d+$/.test(text)) {
                    // Número europeo completo (1.234,56)
                    number = parseFloat(text.replace(/\./g, '').replace(',', '.'));
                }
                
                if (number !== null) {
                    // Agregar información de detección para depuración
                    cell.setAttribute('data-format-detection', formatConfig.detection_type || 'unknown');
                    cell.setAttribute('data-format-field', formatConfig.field || 'unknown');
                    
                    // Aplicar formato con los decimales específicos de la columna
                    applyNumberFormat(cell, number, decimals);
                    cell.setAttribute('data-formatted', 'true');
                    return;
                }
            }
        }
    }
    
    // SOLO aplicar formato a números que realmente tienen decimales o formato numérico
    
    // 1. Verificar si es un número con decimales sin separador de miles (1234.56)
    if (/^\d+\.\d+$/.test(text)) {
        // Contar decimales existentes para preservarlos
        var parts = text.split('.');
        var decimals = parts[1] ? parts[1].length : 2;
        applyNumberFormat(cell, parseFloat(text), decimals);
    }
    // 2. Verificar si es un número con formato europeo simple con decimales (1234,56)
    else if (/^\d+,\d+$/.test(text)) {
        var parts = text.split(',');
        var decimals = parts[1] ? parts[1].length : 2;
        var number = parseFloat(text.replace(',', '.'));
        applyNumberFormat(cell, number, decimals);
    }
    // 3. Verificar si es un número con formato americano CON DECIMALES (1,234.56)
    else if (/^\d{1,3}(,\d{3})*\.\d+$/.test(text)) {
        var parts = text.split('.');
        var decimals = parts[1] ? parts[1].length : 2;
        var cleanText = text.replace(/,/g, '');
        applyNumberFormat(cell, parseFloat(cleanText), decimals);
    }
    // 4. Verificar si es un número con formato europeo completo CON DECIMALES (1.234,56)
    else if (/^\d{1,3}(\.\d{3})*,\d+$/.test(text)) {
        var parts = text.split(',');
        var decimals = parts[1] ? parts[1].length : 2;
        var cleanText = text.replace(/\./g, '').replace(',', '.');
        applyNumberFormat(cell, parseFloat(cleanText), decimals);
    }
    // 5. Verificar si la columna tiene un nombre que indica valores monetarios o cantidades
    else if (headerCell && (
        headerText.includes('cantidad') || 
        headerText.includes('monto') || 
        headerText.includes('total') || 
        headerText.includes('precio') || 
        headerText.includes('costo') || 
        headerText.includes('valor') ||
        headerText.includes('saldo') ||
        headerText.includes('tm') ||
        headerText.includes('tonelada'))) {
        
        // Para columnas numéricas que deberían tener formato, verificar si es un número entero
        if (/^\d+$/.test(text)) {
            // Verificar SQL para detectar cuántos decimales usar
            var defaultDecimals = (headerText.includes('tm') || headerText.includes('tonelada')) ? 3 : 2;
            applyNumberFormat(cell, parseFloat(text), defaultDecimals);
        }
    }
    
    // Marcar la celda como formateada
    cell.setAttribute('data-formatted', 'true');
}

// Función para aplicar formato de número con los decimales especificados
function applyNumberFormat(cell, number, decimals) {
    if (isNaN(number)) return;
    
    // Si no se especifica, usar 2 decimales por defecto
    if (typeof decimals === 'undefined') {
        decimals = 2;
    }
    
    // Conservamos el valor original
    cell.setAttribute('data-original-value', cell.textContent.trim());
    cell.setAttribute('data-raw-value', number);
    cell.setAttribute('data-decimals', decimals);
    
    // Formateamos con los decimales específicos, coma para miles y punto decimal
    var formatted = number.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
    
    // Aplicamos el formato
    cell.innerHTML = '<strong>' + formatted + '</strong>';
}

// Función para procesar filas con formatos o reglas especiales
function processSpecialRows() {
    var allRows = document.querySelectorAll('#resultsTable tbody tr');
    
    allRows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        
        // Verificar si es una fila de subtotal
        if (row.classList.contains('subtotal-row') || row.classList.contains('total-row')) {
            // Asegurarse que los valores numéricos tienen formato adecuado
            cells.forEach(function(cell) {
                if (cell.getAttribute('data-formatted') !== 'true') {
                    formatCellContent(cell);
                }
            });
        }
        
        // Verificar si es una fila para un cliente específico (ejemplo CARGILL DE MEXICO)
        var hasSpecialClient = false;
        cells.forEach(function(cell) {
            if (cell.textContent.indexOf('CARGILL DE MEXICO') !== -1) {
                hasSpecialClient = true;
            }
        });
        
        if (hasSpecialClient) {
            cells.forEach(function(cell) {
                // Formatear de nuevo para asegurar precisión
                if (cell.getAttribute('data-formatted') !== 'true') {
                    formatCellContent(cell);
                }
            });
        }
    });
}

// Función auxiliar para obtener todos los nodos de texto en un elemento
function getTextNodes(node) {
    var textNodes = [];
    
    if (node.nodeType === 3) { // 3 = nodo de texto
        textNodes.push(node);
    } else {
        var children = node.childNodes;
        for (var i = 0; i < children.length; i++) {
            textNodes = textNodes.concat(getTextNodes(children[i]));
        }
    }
    
    return textNodes;
}