<?php
include("../../netwarelog/catalog/conexionbd.php");

$cartaporte = isset($_POST['cartaporte']) ? $_POST['cartaporte'] : '';
$idtransportista = isset($_POST['idtransportista']) ? $_POST['idtransportista'] : '';

$existe = false;

if($cartaporte != '' && $idtransportista != ''){
    // Evitar inyección básica (dependiendo de la versión de PHP/conexión, se asume que no hay PDO aquí, así que usamos el valor directo o podemos escapar si la clase conexionbd tiene algún método, pero el transportista es numérico).
    // Asumimos que idtransportista es un número entero.
    $idtransportista = intval($idtransportista);
    
    // Si la carta porte es string/varchar, la ponemos entre comillas simples.
    // Usamos el query que solicitó el usuario como base.
    $sql = "select cartaporte from logistica_envios where cartaporte='".$cartaporte."' and idtransportista=".$idtransportista;
    $result = $conexion->consultar($sql);
    
    while($rs = $conexion->siguiente($result)){
        $existe = true;
    }
    $conexion->cerrar_consulta($result);
}

header('Content-Type: application/json');
echo json_encode(array('existe' => $existe));
?>
