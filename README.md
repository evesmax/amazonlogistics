# Amazon Logistics - Consideraciones de Desarrollo

Este proyecto tiene requerimientos y limitantes específicos de infraestructura que deben ser estrictamente respetados por cualquier desarrollador o IA (Asistente de Código) durante su desarrollo y mantenimiento.

## ⚠️ Entorno de Ejecución (CRÍTICO)
- **Sistema Operativo:** Ubuntu 14.04 (Ubuntu 14)
- **Versión de PHP:** 5.5.9
- **Base de Datos:** MySQL Ver 14.14 Distrib 5.5.62

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

## 🔒 Estrategia de Consistencia Transaccional (Bugfix 2026-07)
Para prevenir que ocurran inconsistencias en la base de datos (por ejemplo, registrar recepciones o envíos sin su correspondiente afectación de movimientos de inventario en `inventarios_movimientos`):

1. **Uso de Transacciones en PHP:**
   - Todos los flujos de grabación complejos (como [recepcion_grabar.php](file:///Users/evesmax/projects/personal/amazonlogistics/webapp/modulos/recepciones/recepcion_grabar.php) y [recepciondirecta_grabar.php](file:///Users/evesmax/projects/personal/amazonlogistics/webapp/modulos/recepciones/recepciondirecta_grabar.php)) deben encapsular sus consultas dentro de un bloque `try-catch` y ejecutar una transacción de base de datos (`START TRANSACTION` / `COMMIT` / `ROLLBACK`).
   - El resultado de cada llamada a `$conexion->consultar()` debe ser estrictamente verificado. Si retorna `false`, se debe lanzar una excepción para abortar y revertir toda la transacción.

2. **Validación de Parámetros de Inventario:**
   - La función `agregarmovimiento` de la clase [clinventarios](file:///Users/evesmax/projects/personal/amazonlogistics/webapp/modulos/inventarios/clases/clinventarios.php) debe validar de manera estricta que todos los identificadores y valores numéricos requeridos no sean nulos o vacíos antes de ejecutar sentencias SQL sin comillas, previniendo fallos sintácticos silenciosos.

3. **Bitácora de Transacciones:**
   - Cada flujo de registro de recepciones debe invocar la función de auditoría `$conexion->transaccion($nombreproceso, $sql)` para registrar la operación en las tablas dinámicas por año y semestre (`netwarelog_transacciones_{año}_{semestre}`).
