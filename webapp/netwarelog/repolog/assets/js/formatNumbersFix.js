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
        
        // Procesar todas las celdas de la tabla, EXCEPTO las que están en filas de subtotales
        var allCells = document.querySelectorAll('#resultsTable td');
        allCells.forEach(function(cell) {
            // Verificar si la celda está en una fila de subtotal (que tiene la clase no-format)
            var parentRow = cell.closest('tr');
            if (parentRow && (parentRow.classList.contains('no-format') || 
                             parentRow.classList.contains('subtotal-row') || 
                             parentRow.classList.contains('total-row'))) {
                console.log("Saltando formateo de celda en fila de subtotal/total, clases:", parentRow.className);
                return; // Saltar el formateo de esta celda
            }
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
    
    // Regla Estricta: Ya no se altera el formato de las celdas en el cliente.
    // El formato viene estrictamente definido desde el SQL.
    // Solo marcamos la celda como formateada para evitar ejecuciones futuras.
    cell.setAttribute('data-formatted', 'true');
}

// Función para aplicar formato de número con los decimales especificados
function applyNumberFormat(cell, number, decimals) {
    if (isNaN(number)) return;
    
    // Si no se especifica, usar 0 decimales por defecto
    if (typeof decimals === 'undefined') {
        decimals = 0;
    }
    
    // Conservamos el valor original
    cell.setAttribute('data-original-value', cell.textContent.trim());
    cell.setAttribute('data-raw-value', number);
    cell.setAttribute('data-decimals', decimals);
    
    // Formateamos con los decimales específicos, formato americano: coma para miles y punto para decimal
    var formatted = number.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
    
    // Aplicamos el formato (se fuerza internamente a alineación derecha)
    cell.innerHTML = '<strong class="text-right" style="text-align: right !important;">' + formatted + '</strong>';
    cell.style.cssText = 'text-align: right !important;';
    cell.className = 'text-right';
}

// Función para procesar filas con formatos o reglas especiales
function processSpecialRows() {
    var allRows = document.querySelectorAll('#resultsTable tbody tr');
    
    allRows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        
        // Verificar si es una fila de subtotal - NO REFORMATEAR porque PHP ya las formatea correctamente
        if (row.classList.contains('subtotal-row') || row.classList.contains('total-row')) {
            // Marcar las celdas como ya formateadas para evitar que JS las modifique
            cells.forEach(function(cell) {
                cell.setAttribute('data-formatted', 'true');
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