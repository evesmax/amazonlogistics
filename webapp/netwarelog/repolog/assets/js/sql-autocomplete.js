/**
 * Sistema de autocompletado inteligente para SQL con buenas prácticas
 */

// Lista de palabras clave SQL comunes
const SQL_KEYWORDS = [
    'SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 
    'ON', 'AND', 'OR', 'NOT', 'IN', 'LIKE', 'GROUP BY', 'HAVING', 'ORDER BY',
    'ASC', 'DESC', 'LIMIT', 'OFFSET', 'COUNT', 'SUM', 'AVG', 'MAX', 'MIN',
    'CASE', 'WHEN', 'THEN', 'ELSE', 'END', 'AS', 'DISTINCT', 'BETWEEN', 'IS NULL',
    'IS NOT NULL', 'EXISTS', 'UNION', 'ALL', 'WITH'
];

// Buenas prácticas SQL
const SQL_BEST_PRACTICES = {
    'SELECT': 'Seleccionar solo las columnas necesarias, evitar SELECT *',
    'WHERE': 'Usar índices en las condiciones WHERE para mejorar el rendimiento',
    'JOIN': 'Preferir INNER JOIN sobre WHERE para relacionar tablas',
    'GROUP BY': 'Evitar agrupar por columnas sin índices',
    'ORDER BY': 'Usar índices en las columnas de ordenación para mejorar el rendimiento',
    'LIKE': 'Evitar patrones que comiencen con %, ya que no pueden usar índices',
    'IN': 'Limitar el número de valores en cláusulas IN para mejor rendimiento',
    'COUNT': 'Para contar filas eficientemente, use COUNT(id) en lugar de COUNT(*)',
    'DISTINCT': 'Usar con moderación, puede impactar el rendimiento',
    'CASE': 'Usar CASE WHEN para lógica condicional en lugar de múltiples consultas',
    'AND': 'Las condiciones AND filtran más que OR, use primero las más restrictivas',
    'OR': 'Las condiciones OR pueden impactar el rendimiento, use IN cuando sea posible',
    'IS NULL': 'Las comparaciones con NULL necesitan IS NULL en lugar de =',
    'LIMIT': 'Usar LIMIT para reducir el tamaño de los resultados y mejorar el rendimiento'
};

// Tablas y columnas comunes en nuestra base de datos
const DB_TABLES = [
    { name: 'inventarios_kardex', columns: ['id', 'idfabricante', 'idmarca', 'idbodega', 'idproducto', 'idestadoproducto', 'idloteproducto', 'idtipomovimiento', 'fecha', 'cantidadinicial', 'cantidadmovimiento', 'saldo', 'foliodoctoorigen'] },
    { name: 'operaciones_fabricantes', columns: ['idfabricante', 'nombrefabricante'] },
    { name: 'vista_marcas', columns: ['idmarca', 'nombremarca'] },
    { name: 'operaciones_bodegas', columns: ['idbodega', 'nombrebodega'] },
    { name: 'inventarios_productos', columns: ['idproducto', 'nombreproducto', 'idunidadmedida'] },
    { name: 'inventarios_estados', columns: ['idestadoproducto', 'descripcionestado'] },
    { name: 'inventarios_lotes', columns: ['idloteproducto', 'descripcionlote'] },
    { name: 'inventarios_tiposmovimiento', columns: ['idtipomovimiento', 'nombremovimiento', 'efectoinventario'] },
    { name: 'inventarios_unidadesmedida', columns: ['idunidadmedida', 'descripcionunidad', 'factor'] },
    { name: 'reporte_existencias', columns: ['idreg', 'idfabricante', 'idmarca', 'idbodega', 'idproducto', 'idestadoproducto', 'idloteproducto', 'idfamilia', 'fecha', 'idempleado', 'existenciafisica', 'entradasacumuladas'] },
    { name: 'inventarios_familias', columns: ['idfamilia', 'nombrefamilia'] },
    { name: 'relaciones_usuariosbodegas', columns: ['idbodega', 'idempleado'] }
];

// Alias comunes utilizados en las consultas
const COMMON_ALIASES = {
    'ik': 'inventarios_kardex',
    'of': 'operaciones_fabricantes',
    'vm': 'vista_marcas',
    'ob': 'operaciones_bodegas',
    'ip': 'inventarios_productos',
    'ie': 'inventarios_estados',
    'il': 'inventarios_lotes',
    'tm': 'inventarios_tiposmovimiento',
    'um': 'inventarios_unidadesmedida',
    're': 'reporte_existencias',
    'ifa': 'inventarios_familias'
};

/**
 * Inicializar el sistema de autocompletado
 */
function initSqlAutocomplete() {
    const sqlTextareas = document.querySelectorAll('.sql-editor');
    
    sqlTextareas.forEach(textarea => {
        // Crear contenedor del tooltip
        const tooltipContainer = document.createElement('div');
        tooltipContainer.className = 'sql-tooltip';
        tooltipContainer.style.display = 'none';
        document.body.appendChild(tooltipContainer);
        
        // Eventos del textarea
        textarea.addEventListener('input', event => {
            handleSqlInput(event, tooltipContainer);
        });
        
        textarea.addEventListener('keydown', event => {
            handleSqlKeydown(event, tooltipContainer);
        });
        
        textarea.addEventListener('blur', () => {
            tooltipContainer.style.display = 'none';
        });
    });
}

/**
 * Manejar entrada de texto en el editor SQL
 */
function handleSqlInput(event, tooltipContainer) {
    const textarea = event.target;
    const text = textarea.value;
    const cursorPos = textarea.selectionStart;
    
    // Encuentra la palabra actual bajo el cursor
    const currentWord = getCurrentWord(text, cursorPos);
    
    if (currentWord) {
        // Buscar sugerencias para la palabra actual
        const suggestions = getSuggestions(currentWord, text);
        
        if (suggestions.length > 0) {
            // Mostrar tooltip con sugerencias
            showTooltip(textarea, tooltipContainer, suggestions, currentWord);
        } else {
            tooltipContainer.style.display = 'none';
        }
    } else {
        tooltipContainer.style.display = 'none';
    }
}

/**
 * Manejar pulsaciones de teclas en el editor SQL
 */
function handleSqlKeydown(event, tooltipContainer) {
    // Si el tooltip está visible y el usuario presiona Enter o Tab
    if (tooltipContainer.style.display !== 'none' && 
        (event.key === 'Enter' || event.key === 'Tab')) {
        const selectedSuggestion = tooltipContainer.querySelector('.suggestion.selected');
        
        if (selectedSuggestion) {
            event.preventDefault();
            insertSuggestion(event.target, selectedSuggestion.textContent);
            tooltipContainer.style.display = 'none';
        }
    }
    
    // Navegación por las sugerencias con teclas de flecha
    if (tooltipContainer.style.display !== 'none' && 
        (event.key === 'ArrowUp' || event.key === 'ArrowDown')) {
        event.preventDefault();
        
        const suggestions = tooltipContainer.querySelectorAll('.suggestion');
        let selectedIndex = -1;
        
        suggestions.forEach((suggestion, index) => {
            if (suggestion.classList.contains('selected')) {
                selectedIndex = index;
                suggestion.classList.remove('selected');
            }
        });
        
        if (event.key === 'ArrowDown') {
            selectedIndex = (selectedIndex + 1) % suggestions.length;
        } else {
            selectedIndex = selectedIndex <= 0 ? suggestions.length - 1 : selectedIndex - 1;
        }
        
        suggestions[selectedIndex].classList.add('selected');
        suggestions[selectedIndex].scrollIntoView({ block: 'nearest' });
    }
    
    // Cerrar el tooltip con Escape
    if (event.key === 'Escape') {
        tooltipContainer.style.display = 'none';
    }
}

/**
 * Obtener la palabra actual bajo el cursor
 */
function getCurrentWord(text, cursorPos) {
    const leftText = text.substring(0, cursorPos);
    const match = leftText.match(/[a-zA-Z_][a-zA-Z0-9_\.]*$/);
    return match ? match[0] : '';
}

/**
 * Obtener sugerencias basadas en la palabra actual y el contexto
 */
function getSuggestions(word, fullText) {
    let suggestions = [];
    const wordLower = word.toLowerCase();
    
    // Primero verificar si parece ser un alias o tabla
    const isTableOrAlias = word.indexOf('.') > -1;
    
    if (isTableOrAlias) {
        // Es una referencia a una columna con alias o tabla (ej: tabla.columna)
        const parts = word.split('.');
        const tableOrAlias = parts[0];
        const columnPrefix = parts.length > 1 ? parts[1] : '';
        
        // Buscar en tablas
        suggestions = getSuggestionsForTableColumn(tableOrAlias, columnPrefix);
    } else {
        // Sugerir palabras clave SQL
        SQL_KEYWORDS.forEach(keyword => {
            if (keyword.toLowerCase().startsWith(wordLower)) {
                suggestions.push({
                    text: keyword,
                    type: 'keyword',
                    description: SQL_BEST_PRACTICES[keyword] || ''
                });
            }
        });
        
        // Sugerir tablas y alias
        DB_TABLES.forEach(table => {
            if (table.name.toLowerCase().startsWith(wordLower)) {
                suggestions.push({
                    text: table.name,
                    type: 'table',
                    description: `Tabla con ${table.columns.length} columnas`
                });
            }
        });
        
        // Sugerir alias comunes
        Object.entries(COMMON_ALIASES).forEach(([alias, table]) => {
            if (alias.toLowerCase().startsWith(wordLower)) {
                suggestions.push({
                    text: alias,
                    type: 'alias',
                    description: `Alias para ${table}`
                });
            }
        });
    }
    
    return suggestions;
}

/**
 * Obtener sugerencias para columnas de una tabla o alias
 */
function getSuggestionsForTableColumn(tableOrAlias, columnPrefix) {
    let suggestions = [];
    const columnPrefixLower = columnPrefix.toLowerCase();
    
    // Primero buscar si es un alias conocido
    let tableName = '';
    if (Object.keys(COMMON_ALIASES).includes(tableOrAlias)) {
        tableName = COMMON_ALIASES[tableOrAlias];
    } else {
        // Si no es un alias, puede ser el nombre directo de la tabla
        tableName = tableOrAlias;
    }
    
    // Buscar la tabla
    const tableInfo = DB_TABLES.find(t => t.name === tableName);
    
    if (tableInfo) {
        // Sugerir columnas de la tabla
        tableInfo.columns.forEach(column => {
            if (columnPrefix === '' || column.toLowerCase().startsWith(columnPrefixLower)) {
                suggestions.push({
                    text: `${tableOrAlias}.${column}`,
                    type: 'column',
                    description: `Columna de ${tableName}`
                });
            }
        });
    }
    
    return suggestions;
}

/**
 * Mostrar tooltip con sugerencias
 */
function showTooltip(textarea, tooltipContainer, suggestions, currentWord) {
    // Limpiar contenido anterior
    tooltipContainer.innerHTML = '';
    
    // Crear lista de sugerencias
    const list = document.createElement('ul');
    list.className = 'suggestions-list';
    
    suggestions.forEach((suggestion, index) => {
        const item = document.createElement('li');
        item.className = `suggestion ${suggestion.type}`;
        if (index === 0) item.classList.add('selected');
        
        // Texto principal
        const textSpan = document.createElement('span');
        textSpan.className = 'suggestion-text';
        textSpan.textContent = suggestion.text;
        item.appendChild(textSpan);
        
        // Descripción (si existe)
        if (suggestion.description) {
            const descSpan = document.createElement('span');
            descSpan.className = 'suggestion-description';
            descSpan.textContent = suggestion.description;
            item.appendChild(descSpan);
        }
        
        // Evento de clic para insertar sugerencia
        item.addEventListener('click', () => {
            insertSuggestion(textarea, suggestion.text);
            tooltipContainer.style.display = 'none';
        });
        
        // Evento de mouseover para cambiar selección
        item.addEventListener('mouseover', () => {
            list.querySelectorAll('.suggestion').forEach(s => s.classList.remove('selected'));
            item.classList.add('selected');
        });
        
        list.appendChild(item);
    });
    
    tooltipContainer.appendChild(list);
    
    // Posicionar tooltip cerca del cursor
    positionTooltip(textarea, tooltipContainer, currentWord);
    
    // Mostrar tooltip
    tooltipContainer.style.display = 'block';
}

/**
 * Posicionar el tooltip cerca del cursor
 */
function positionTooltip(textarea, tooltipContainer, currentWord) {
    const textareaRect = textarea.getBoundingClientRect();
    const cursorPosition = getCursorPosition(textarea, currentWord);
    
    const left = textareaRect.left + cursorPosition.left;
    const top = textareaRect.top + cursorPosition.top + 20; // Un poco debajo del cursor
    
    tooltipContainer.style.left = `${left}px`;
    tooltipContainer.style.top = `${top}px`;
    tooltipContainer.style.maxWidth = `${textareaRect.width}px`;
}

/**
 * Obtener posición del cursor en coordenadas
 */
function getCursorPosition(textarea, currentWord) {
    // Crear un elemento espejo para calcular la posición
    const mirror = document.createElement('div');
    mirror.style.position = 'absolute';
    mirror.style.top = '-9999px';
    mirror.style.left = '-9999px';
    mirror.style.width = `${textarea.clientWidth}px`;
    mirror.style.height = 'auto';
    mirror.style.whiteSpace = 'pre-wrap';
    mirror.style.wordWrap = 'break-word';
    mirror.style.fontSize = window.getComputedStyle(textarea).fontSize;
    mirror.style.fontFamily = window.getComputedStyle(textarea).fontFamily;
    mirror.style.lineHeight = window.getComputedStyle(textarea).lineHeight;
    mirror.style.padding = window.getComputedStyle(textarea).padding;
    
    document.body.appendChild(mirror);
    
    // Texto hasta el cursor
    const textUntilCursor = textarea.value.substring(0, textarea.selectionStart);
    // Reemplazar la última aparición de la palabra actual con un marcador
    const lastIndex = textUntilCursor.lastIndexOf(currentWord);
    const textWithMarker = textUntilCursor.substring(0, lastIndex) + 
                          '<span id="cursor-marker"></span>' + 
                          textUntilCursor.substring(lastIndex);
    
    mirror.innerHTML = textWithMarker;
    
    // Obtener posición del marcador
    const marker = mirror.querySelector('#cursor-marker');
    const position = {
        left: marker.offsetLeft,
        top: marker.offsetTop
    };
    
    // Limpiar
    document.body.removeChild(mirror);
    
    return position;
}

/**
 * Insertar sugerencia en el textarea
 */
function insertSuggestion(textarea, suggestion) {
    const text = textarea.value;
    const cursorPos = textarea.selectionStart;
    
    // Encuentra la palabra actual bajo el cursor
    const currentWord = getCurrentWord(text, cursorPos);
    
    if (currentWord) {
        // Reemplazar la palabra actual con la sugerencia
        const startPos = cursorPos - currentWord.length;
        const newText = text.substring(0, startPos) + suggestion + text.substring(cursorPos);
        textarea.value = newText;
        
        // Mover cursor al final de la sugerencia insertada
        const newCursorPos = startPos + suggestion.length;
        textarea.setSelectionRange(newCursorPos, newCursorPos);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initSqlAutocomplete);