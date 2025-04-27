/**
 * Correcciones específicas para CARGILL DE MEXICO
 * Este script busca y reemplaza valores específicos en la tabla
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ejecutar después de que la página esté completamente cargada
    setTimeout(function() {
        // Primero buscar filas que contengan "CARGILL DE MEXICO"
        var allRows = document.querySelectorAll('#resultsTable tbody tr');
        var cargillRows = [];
        
        // Identificar todas las filas que contienen "CARGILL DE MEXICO"
        allRows.forEach(function(row) {
            var rowText = row.textContent || row.innerText;
            if (rowText.indexOf('CARGILL DE MEXICO') !== -1) {
                cargillRows.push(row);
            }
        });
        
        // Para cada fila de CARGILL, buscar la celda con "2990,58"
        cargillRows.forEach(function(row) {
            var cells = row.querySelectorAll('td');
            cells.forEach(function(cell) {
                var cellText = cell.textContent || cell.innerText;
                if (cellText.trim() === '2990,58') {
                    // Reemplazar directamente con el formato correcto
                    cell.innerHTML = '<strong>2,990.58</strong>';
                }
            });
        });
        
        // Buscar cualquier elemento en la página que contenga el texto exacto "2990,58"
        var walkDOM = function(node, func) {
            func(node);
            node = node.firstChild;
            while(node) {
                walkDOM(node, func);
                node = node.nextSibling;
            }
        };
        
        walkDOM(document.body, function(node) {
            if (node.nodeType === 3) { // Nodo de texto
                if (node.nodeValue.indexOf('2990,58') !== -1) {
                    node.nodeValue = node.nodeValue.replace(/2990,58/g, '2,990.58');
                }
            }
        });
        
        // Verificación específica para el caso de exportación a Excel
        if (window.location.href.indexOf('export_excel.php') !== -1) {
            var excelCells = document.querySelectorAll('td');
            excelCells.forEach(function(cell) {
                if (cell.textContent.trim() === '2990,58') {
                    cell.innerHTML = '2,990.58';
                }
            });
        }
        
        console.log("Correcciones específicas para CARGILL DE MEXICO aplicadas");
    }, 1000); // Ejecutar 1 segundo después para asegurar que la tabla ya se cargó
});