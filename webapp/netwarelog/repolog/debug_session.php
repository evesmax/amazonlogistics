<?php
session_start();
header('Content-Type: text/plain');

echo "=== ACCELOG ID EMPLEADO ===\n";
echo isset($_SESSION['accelog_idempleado']) ? $_SESSION['accelog_idempleado'] : 'NOT SET';
echo "\n\n";

echo "=== SQL CONSULTA ===\n";
echo isset($_SESSION['sql_consulta']) ? $_SESSION['sql_consulta'] : 'NOT SET';
echo "\n\n";

echo "=== FILTER VALUES ===\n";
if (isset($_SESSION['filter_values'])) {
    print_r($_SESSION['filter_values']);
} else {
    echo "NOT SET";
}
echo "\n";

echo "=== APPLIED FILTERS ===\n";
if (isset($_SESSION['applied_filters'])) {
    print_r($_SESSION['applied_filters']);
} else {
    echo "NOT SET";
}
echo "\n";
?>
