# Amazon Logistics - Consideraciones de Desarrollo

Este proyecto tiene requerimientos y limitantes específicos de infraestructura que deben ser estrictamente respetados por cualquier desarrollador o IA (Asistente de Código) durante su desarrollo y mantenimiento.

## ⚠️ Entorno de Ejecución (CRÍTICO)
- **Sistema Operativo:** Ubuntu 14.04
- **Versión de PHP:** 5.5.9

## Reglas de Codificación (Restricciones PHP 5.5.9)
Debido a la antigüedad del servidor, **ESTÁ ESTRICTAMENTE PROHIBIDO** utilizar características, sintaxis o funciones introducidas en PHP 7.x, 8.x o superior. 

Para garantizar que el código funcione en producción, recuerda siempre estas restricciones:

1. **No usar tipado escalar ni de retorno:**
   - ❌ `function suma(int $a, int $b): int` 
   - ✅ `function suma($a, $b)`

2. **No usar el operador Null Coalescing (`??`):**
   - ❌ `$valor = $_POST['dato'] ?? 'por defecto';`
   - ✅ `$valor = isset($_POST['dato']) ? $_POST['dato'] : 'por defecto';`

3. **No usar Arrow Functions (Funciones Flecha `fn() =>`):**
   - ❌ `array_map(fn($item) => $item * 2, $lista);`
   - ✅ `array_map(function($item) { return $item * 2; }, $lista);`

4. **No usar el operador Spaceship (`<=>`).**

5. **No usar el Spread Operator (`...`) ni rest parameters.**

6. **No usar asignaciones de arrays desestructuradas nativas de versiones recientes** (ej. `[$a, $b] = $array;` no está soportado en iteraciones complejas, usa la función `list()`).

7. **Manejo de Errores:**
   - En PHP 5.5 no existe la clase `Error` ni la interfaz `Throwable`. Debes usar siempre la clase tradicional `Exception`.

8. **Clases Anónimas:**
   - No están soportadas en PHP 5.5 (se introdujeron en PHP 7.0).

Antes de plantear o integrar cualquier cambio, verifica y comprueba en la documentación de PHP si la función a usar existe y es compatible con PHP ^5.5.9.
