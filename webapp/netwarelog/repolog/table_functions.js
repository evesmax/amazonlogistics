/**
 * Table Functions
 * 
 * JavaScript functions for handling table filtering and pagination
 * Compatible with older browsers (no ES6 features used)
 */

// Pagination variables
var currentPage = 1;
var rowsPerPage = 10;
var filteredData = [];

// Initialize the table when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize with all data
    filteredData = tableData.slice();
    
    // Setup initial pagination
    updatePagination();
    renderTable();
    
    // Set initial rows per page value
    document.getElementById('rowsPerPage').value = rowsPerPage;
});

/**
 * Filter table based on column filters and global search
 */
function filterTable() {
    // Reset to page 1 when filter changes
    currentPage = 1;
    
    // Get global search value
    var globalSearch = document.getElementById('globalSearch').value.toLowerCase();
    
    // Get column-specific filters
    var columnFilters = {};
    var filterInputs = document.querySelectorAll('.column-filter input');
    
    filterInputs.forEach(function(input) {
        var columnName = input.getAttribute('data-column');
        var filterValue = input.value.toLowerCase();
        
        if (filterValue) {
            columnFilters[columnName] = filterValue;
        }
    });
    
    // Separar las filas normales de las filas de subtotales/totales
    var regularRows = [];
    var subtotalRows = [];
    
    // Primero filtramos las filas normales
    tableData.forEach(function(row) {
        // Si es una fila de subtotal o total, la guardamos para procesarla después
        if (row.__is_subtotal) {
            subtotalRows.push(row);
            return;
        }
        
        // Es una fila normal, aplicamos los filtros
        var includeRow = true;
        
        // Check if row matches all column filters
        for (var column in columnFilters) {
            var cellValue = String(row[column] || '').toLowerCase();
            if (cellValue.indexOf(columnFilters[column]) === -1) {
                includeRow = false;
                break;
            }
        }
        
        // Check if row matches global search
        if (includeRow && globalSearch) {
            var matchesGlobal = false;
            
            for (var i = 0; i < tableColumns.length; i++) {
                // Saltamos las columnas de control
                if (tableColumns[i] === '__is_subtotal' || tableColumns[i] === '__subtotal_level') {
                    continue;
                }
                
                var colValue = String(row[tableColumns[i]] || '').toLowerCase();
                if (colValue.indexOf(globalSearch) !== -1) {
                    matchesGlobal = true;
                    break;
                }
            }
            
            if (!matchesGlobal) {
                includeRow = false;
            }
        }
        
        if (includeRow) {
            regularRows.push(row);
        }
    });
    
    // Si hay filtros activos, no incluimos subtotales/totales
    if (globalSearch || Object.keys(columnFilters).length > 0) {
        filteredData = regularRows;
    } else {
        // Si no hay filtros, incluimos todas las filas (normales y subtotales/totales)
        filteredData = tableData.slice();
    }
    
    // Update pagination and table
    updatePagination();
    renderTable();
}

/**
 * Change the number of rows displayed per page
 */
function changeRowsPerPage() {
    rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
    currentPage = 1; // Reset to first page
    updatePagination();
    renderTable();
}

/**
 * Go to previous or next page
 */
function changePage(delta) {
    currentPage += delta;
    
    // Ensure page is within bounds
    var totalPages = Math.ceil(filteredData.length / rowsPerPage);
    
    if (currentPage < 1) {
        currentPage = 1;
    } else if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    
    updatePagination();
    renderTable();
}

/**
 * Update pagination controls
 */
function updatePagination() {
    var totalPages = Math.max(1, Math.ceil(filteredData.length / rowsPerPage));
    
    document.getElementById('currentPage').textContent = currentPage;
    document.getElementById('totalPages').textContent = totalPages;
    
    // Enable/disable pagination buttons
    document.getElementById('prevPage').disabled = (currentPage <= 1);
    document.getElementById('nextPage').disabled = (currentPage >= totalPages);
}

/**
 * Render table with current filtered data and pagination
 */
function renderTable() {
    var tableBody = document.querySelector('#resultsTable tbody');
    tableBody.innerHTML = '';
    
    // Calculate start and end indices for current page
    var startIndex = (currentPage - 1) * rowsPerPage;
    var endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);
    
    // No data to display
    if (filteredData.length === 0) {
        var emptyRow = document.createElement('tr');
        var emptyCell = document.createElement('td');
        emptyCell.colSpan = tableColumns.length;
        emptyCell.textContent = 'No se encontraron resultados';
        emptyCell.className = 'no-results';
        emptyRow.appendChild(emptyCell);
        tableBody.appendChild(emptyRow);
        return;
    }
    
    // Create rows for current page
    for (var i = startIndex; i < endIndex; i++) {
        var row = document.createElement('tr');
        var rowData = filteredData[i];
        
        // Determinar si es una fila de subtotal o total
        var isSubtotal = rowData.__is_subtotal === true;
        var subtotalLevel = rowData.__subtotal_level || 0;
        
        // Asignar clase CSS para las filas de subtotales y totales
        if (isSubtotal) {
            if (subtotalLevel === 1) {
                row.className = 'subtotal-row';
            } else if (subtotalLevel === 2) {
                row.className = 'total-row';
            }
        }
        
        tableColumns.forEach(function(column) {
            // Ignorar columnas de control especiales
            if (column === '__is_subtotal' || column === '__subtotal_level') {
                return;
            }
            
            var cell = document.createElement('td');
            var value = rowData[column] || '';
            
            // Convertir a string si no lo es
            value = String(value);
            
            // Si es una fila de subtotal o total, dar formato especial
            if (isSubtotal) {
                // En filas de subtotales, los valores numéricos deben mostrarse en negrita
                var isSumField = false;
                
                // Comprobar si la sesión tiene información de campos de suma
                if (typeof subtotalesSubtotal !== 'undefined') {
                    var sumFields = subtotalesSubtotal.split(',').map(function(item) { 
                        return item.trim(); 
                    });
                    isSumField = sumFields.indexOf(column) !== -1;
                } else {
                    // Como fallback, verificamos si el valor parece numérico
                    isSumField = !isNaN(parseFloat(value));
                }
                
                if (isSumField) {
                    // Formatear números con 2 decimales
                    if (!isNaN(parseFloat(value))) {
                        var num = parseFloat(value);
                        value = '<strong>' + num.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong>';
                        cell.innerHTML = value;
                        row.appendChild(cell);
                        return;
                    } else {
                        value = '<strong>' + value + '</strong>';
                        cell.innerHTML = value;
                        row.appendChild(cell);
                        return;
                    }
                } else if (subtotalLevel === 2 && column === tableColumns[0]) {
                    // Para fila de total general, mostrar "TOTAL GENERAL" en la primera columna
                    cell.innerHTML = '<strong>TOTAL GENERAL</strong>';
                    row.appendChild(cell);
                    return;
                } else if (subtotalLevel === 1) {
                    // Para filas de subtotal, verificar si es un campo de agrupación
                    var isGroupField = false;
                    
                    if (typeof subtotalesAgrupaciones !== 'undefined') {
                        var groupFields = subtotalesAgrupaciones.split(',').map(function(item) { 
                            return item.trim(); 
                        });
                        isGroupField = groupFields.indexOf(column) !== -1;
                    }
                    
                    if (isGroupField) {
                        if (column === groupFields[0]) {
                            cell.innerHTML = '<strong>Subtotal: ' + escapeHtml(value) + '</strong>';
                        } else {
                            cell.innerHTML = '<strong>' + escapeHtml(value) + '</strong>';
                        }
                        row.appendChild(cell);
                        return;
                    }
                }
            }
            
            // Procesamiento normal para filas regulares
            // Detectar si parece ser contenido HTML (case-insensitive)
            if (
                value.indexOf('<') !== -1 && 
                value.indexOf('>') !== -1
            ) {
                // Convertir a minúsculas para mejor detección (sin modificar el valor original)
                var valueLower = value.toLowerCase();
                
                if (
                    valueLower.indexOf('<img') !== -1 || 
                    valueLower.indexOf('<a') !== -1 || 
                    valueLower.indexOf('<center') !== -1 ||
                    valueLower.indexOf('<div') !== -1
                ) {
                    // Arreglar enlaces HTML sin comillas en los atributos href
                    if (/<a\s+href=([^"'>]+)([^>]*)>/i.test(value)) {
                        value = value.replace(/(<a\s+href=)([^"'>]+)([^>]*)>/gi, '$1"$2"$3>');
                    }
                    
                    // Es HTML permitido, configurar innerHTML
                    cell.innerHTML = value;
                } else {
                    // Es HTML pero no de los tipos permitidos, escapar
                    cell.textContent = value;
                }
            } else {
                // No es HTML, usar textContent para escapar caracteres
                cell.textContent = value;
            }
            
            row.appendChild(cell);
        });
        
        tableBody.appendChild(row);
    }
}

// Función auxiliar para escapar HTML
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
