<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "netwarelog/webconfig.php";
include "netwarelog/catalog/conexionbd.php";

$pwd = "Temporal123";
$hash = $conexion->fencripta($pwd, $accelog_salt);

echo "Accelog Salt: " . $accelog_salt . "\n";
echo "Generated Hash for 'Temporal123': " . $hash . "\n";

$sql = "SELECT idempleado, usuario, clave FROM accelog_usuarios LIMIT 10";
$result = $conexion->consultar($sql);
echo "Users:\n";
while($rs = $conexion->siguiente($result)){
    echo $rs['idempleado'] . " | " . $rs['usuario'] . " | " . $rs['clave'] . "\n";
}

$conexion->cerrar_consulta($result);
?>
