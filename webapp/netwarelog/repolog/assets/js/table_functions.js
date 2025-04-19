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
    
    // Apply filters to the data
    filteredData = tableData.filter(function(row) {
        // Check if row matches all column filters
        for (var column in columnFilters) {
            var cellValue = String(row[column] || '').toLowerCase();
            if (cellValue.indexOf(columnFilters[column]) === -1) {
                return false;
            }
        }
        
        // Check if row matches global search
        if (globalSearch) {
            var matchesGlobal = false;
            
            for (var i = 0; i < tableColumns.length; i++) {
                var colValue = String(row[tableColumns[i]] || '').toLowerCase();
                if (colValue.indexOf(globalSearch) !== -1) {
                    matchesGlobal = true;
                    break;
                }
            }
            
            if (!matchesGlobal) {
                return false;
            }
        }
        
        return true;
    });
    
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
        
        tableColumns.forEach(function(column) {
            var cell = document.createElement('td');
            var value = filteredData[i][column] || '';
            
            // Convertir a string si no lo es
            value = String(value);
            
            // Detectar si parece ser contenido HTML
            if (
                value.indexOf('<') !== -1 && 
                value.indexOf('>') !== -1 && 
                (
                    value.indexOf('<img') !== -1 || 
                    value.indexOf('<a') !== -1 || 
                    value.indexOf('<center') !== -1 ||
                    value.indexOf('<div') !== -1
                )
            ) {
                // Es HTML, configurar innerHTML en lugar de textContent
                cell.innerHTML = value;
            } else {
                // No es HTML, usar textContent para escapar caracteres
                cell.textContent = value;
            }
            
            row.appendChild(cell);
        });
        
        tableBody.appendChild(row);
    }
}
