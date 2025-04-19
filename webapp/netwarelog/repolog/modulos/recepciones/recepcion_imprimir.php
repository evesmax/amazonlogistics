<?php
// Este es un archivo de demostración para mostrar la impresión
// de una recepción específica

// Recuperar el ID de recepción de la URL
$idrecepcion = isset($_GET['idrecepcion']) ? intval($_GET['idrecepcion']) : 0;

// Mostrar un mensaje simple
echo "<html><head><title>Impresión de Recepción #$idrecepcion</title>";
echo "<style>body { font-family: Arial; padding: 20px; }";
echo ".demo-container { border: 1px solid #ccc; padding: 20px; margin: 20px auto; max-width: 600px; }";
echo ".demo-header { text-align: center; margin-bottom: 20px; }";
echo "</style></head><body>";
echo "<div class='demo-container'>";
echo "<div class='demo-header'><h1>Recepción #$idrecepcion</h1></div>";
echo "<p>Este es un ejemplo de una página de impresión para la recepción #$idrecepcion.</p>";
echo "<p>En una implementación real, aquí se mostraría el detalle completo de la recepción.</p>";
echo "<p>Puedes usar esta funcionalidad para mostrar y/o imprimir información detallada a partir de los vínculos en HTML.</p>";
echo "</div>";
echo "</body></html>";
?>