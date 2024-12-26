<?php

require("models/connection_sqli_manual.php");

class FiltersModel extends Connection {
  function obtenerAmacenes() {
      $sql = "SELECT id value, CONCAT('(',codigo_manual, ') ' , nombre) label
              FROM app_almacenes
              WHERE activo = 1;";
      $res = $this->queryArray($sql);
      return $res['rows'];
  }
  function obtenerSucursales() {
    $sql = "SELECT idSuc value, nombre label
    FROM mrp_sucursal
    WHERE activo != 0;";
    $res = $this->queryArray($sql);
    return $res['rows'];
}
function obtenerClientes() {
  $sql = "SELECT id value, nombre label
          FROM comun_cliente;";
  $res = $this->queryArray($sql);
  return $res['rows'];
}
  function obtenerProveedores() {
      $sql = "SELECT idPrv value, CONCAT('(',codigo, ') ' , razon_social) label
              FROM mrp_proveedor;";
      $res = $this->queryArray($sql);
      return $res['rows'];
  }
  function obtenerProductos() {
      $sql = "SELECT id value, CONCAT('(',codigo, ') ' , nombre) label
              FROM app_productos
              WHERE tipo_producto NOT IN (2,5,6,7);";
      $res = $this->queryArray($sql);
      return $res['rows'];
  }
  function obtenerUnidadesDeMedida() {
      $sql = "SELECT id value, CONCAT('(',clave, ') ' , nombre) label
              FROM app_unidades_medida;";
      $res = $this->queryArray($sql);
      return $res['rows'];
  }
  public function obtenerPermisoAccion($accion)
  {
    $sql = "SELECT	*
            FROM	accelog_usuarios u
            INNER JOIN administracion_usuarios au ON u.idempleado = au.idempleado
            INNER JOIN accelog_perfiles p ON au.idperfil = p.idperfil
            INNER JOIN accelog_perfiles_ac ap ON p.idperfil = ap.idperfil
            WHERE	u.idempleado = '{$_SESSION['accelog_idempleado']}' AND ap.idaccion = '$accion';";
    $res = $this->queryArray($sql);
    return $res['rows'];
        
  }

  public function buscarClasificadores( $clasificador, $patron, $parentClasific) {

    switch ($clasificador) {
      case '1':
        $tabla = 'app_departamento';
        $filtro = "";
        break;
      case '2':
        $tabla = 'app_familia';
        $filtro = "AND id_departamento='$parentClasific'";
        break;
      case '3':
        $tabla = 'app_linea';
        $filtro = "AND id_familia='$parentClasific'";
        break;
      case '4':
        $tabla = 'app_productos';
        $filtro = "AND linea='$parentClasific'";
        break;
      case '5':
        $tabla = 'app_productos';
        $filtro = "AND departamento='$parentClasific'";
        break;
      default:
      # code...
      break;
    }
    if($parentClasific == 0)
      $filtro = "";

    $sql = "SELECT	id value, nombre as label
            FROM	$tabla
            WHERE	nombre LIKE '%$patron%' $filtro";

    $res = $this->queryArray($sql);

    return json_encode( $res );
  }
}
