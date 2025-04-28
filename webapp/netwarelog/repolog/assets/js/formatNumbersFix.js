/**
 * Formatter para valores numéricos - soporta formatos americano y europeo
 * Este script asegura que todos los números se muestren con el formato correcto:
 * - Punto decimal
 * - Coma para separador de miles
 * - Siempre 2 decimales para valores numéricos
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ejecutar después de 100ms para dar tiempo a que todos los datos se carguen
    setTimeout(formatNumbersInTable, 100);
});

// Función principal para formatear números
function formatNumbersInTable() {
    console.log("Ejecutando formatNumbersInTable");
    
    // Procesar todas las celdas de la tabla
    var allCells = document.querySelectorAll('#resultsTable td');
    allCells.forEach(function(cell) {
        formatCellContent(cell);
    });
    
    // Verificar todas las filas para formatos especiales
    processSpecialRows();
}

// Función para formatear el contenido de una celda
function formatCellContent(cell) {
    // No procesar celdas que ya han sido formateadas
    if (cell.getAttribute('data-formatted') === 'true') {
        return;
    }
    
    var text = cell.textContent.trim();
    if (!text) return;
    
    // 1. Verificar si es un número puro (12345)
    if (/^\d+$/.test(text)) {
        applyNumberFormat(cell, parseFloat(text));
    }
    // 2. Verificar si es un número con decimales sin separador de miles (1234.56)
    else if (/^\d+\.\d+$/.test(text)) {
        applyNumberFormat(cell, parseFloat(text));
    }
    // 3. Verificar si es un número con formato europeo simple (1234,56)
    else if (/^\d+,\d+$/.test(text)) {
        var number = parseFloat(text.replace(',', '.'));
        applyNumberFormat(cell, number);
    }
    // 4. Verificar si es un número con formato americano (1,234.56)
    else if (/^\d{1,3}(,\d{3})*(\.\d+)?$/.test(text)) {
        var cleanText = text.replace(/,/g, '');
        applyNumberFormat(cell, parseFloat(cleanText));
    }
    // 5. Verificar si es un número con formato europeo completo (1.234,56)
    else if (/^\d{1,3}(\.\d{3})*(,\d+)$/.test(text)) {
        var cleanText = text.replace(/\./g, '').replace(',', '.');
        applyNumberFormat(cell, parseFloat(cleanText));
    }
    
    // Marcar la celda como formateada
    cell.setAttribute('data-formatted', 'true');
}

// Función para aplicar formato de número con 2 decimales
function applyNumberFormat(cell, number) {
    if (isNaN(number)) return;
    
    // Conservamos el valor original
    cell.setAttribute('data-original-value', cell.textContent.trim());
    cell.setAttribute('data-raw-value', number);
    
    // Formateamos con 2 decimales, coma para miles y punto decimal
    var formatted = number.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
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