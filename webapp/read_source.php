<?php
header('Content-Type: text/plain; charset=utf-8');

$file = isset($_GET['file']) ? $_GET['file'] : '';

// Restrict reading only within the project directories for security
$allowed_files = [
    'clinventarios' => 'modulos/inventarios/clases/clinventarios.php',
    'recepcion_grabar' => 'modulos/recepciones/recepcion_grabar.php',
    'recepciondirecta_grabar' => 'modulos/recepciones/recepciondirecta_grabar.php',
    'consecutivos' => 'modulos/recepciones/recepcion_grabar.php'
];

if (array_key_exists($file, $allowed_files)) {
    $real_path = $allowed_files[$file];
    if (file_exists($real_path)) {
        echo file_get_contents($real_path);
    } else {
        echo "File $real_path does not exist on server!";
    }
} else {
    echo "Usage: read_source.php?file=[clinventarios|recepcion_grabar|recepciondirecta_grabar]";
}
?>
