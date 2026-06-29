<?php
session_start();
header('Content-Type: text/plain');

if (function_exists('opcache_reset')) {
    opcache_reset();
}

echo "=== DIAGNOSTIC REPORT ===\n";
echo "Session DB: " . (isset($_SESSION['bd']) ? $_SESSION['bd'] : 'Not Set') . "\n";
echo "Accelog variable: " . (isset($_SESSION['accelog_variable']) ? $_SESSION['accelog_variable'] : 'Not Set') . "\n";
echo "Employee ID (accelog_idempleado): " . (isset($_SESSION['accelog_idempleado']) ? $_SESSION['accelog_idempleado'] : 'Not Set') . "\n";
echo "Last Report ID: " . (isset($_SESSION['repolog_report_id']) ? $_SESSION['repolog_report_id'] : 'Not Set') . "\n";

echo "\n--- Last Query Stored ---\n";
echo (isset($_SESSION['sql_consulta']) ? $_SESSION['sql_consulta'] : 'Not Set') . "\n";

echo "\n--- Filter Values ---\n";
if (isset($_SESSION['filter_values'])) {
    print_r($_SESSION['filter_values']);
} else {
    echo "Not Set\n";
}

echo "\n--- Filters Metadata ---\n";
if (isset($_SESSION['filters'])) {
    print_r($_SESSION['filters']);
} else {
    echo "Not Set\n";
}

echo "\n--- System Tre Phase Status ---\n";
echo "usar_sistema_tres_fases: " . (isset($_SESSION['usar_sistema_tres_fases']) ? ($_SESSION['usar_sistema_tres_fases'] ? 'TRUE' : 'FALSE') : 'Not Set') . "\n";

echo "\n--- sql_problematic ---\n";
echo (isset($_SESSION['sql_problematic']) ? $_SESSION['sql_problematic'] : 'Not Set') . "\n";

echo "\n--- sql_cleaned ---\n";
echo (isset($_SESSION['sql_cleaned']) ? $_SESSION['sql_cleaned'] : 'Not Set') . "\n";
?>
