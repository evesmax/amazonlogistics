<?php

require("models/connection_sqli_manual.php");

class CajaModel extends Connection {

  public function devolucionesVenta($params)
  {
    $filtro = 'true ';
    $filtro .= "AND d.fecha_devolucion BETWEEN '{$params['startTime']}' AND '{$params['endTime']}'";
    $filtro .= $params['branchOffice'] != 0 ? "AND v.idSucursal = '{$params['branchOffice']}'" : '';
    $filtro .= $params['customer'] != 0 ? "AND v.idCliente = '{$params['customer']}'" : '';

    $sql = "SELECT	d.id ID_DEVOLUCION, d.fecha_devolucion FECHA_DEVOLUCION, SUM( ( (vp.impuestosproductoventa/vp.cantidad) + vp.preciounitario) * dd.cantidad ) TOTAL_DEVOLUCION, v.idVenta ID_VENTA, v.fecha FECHA_VENTA, v.monto TOTAL_VENTA, c.id ID_CLIENTE, IF(c.nombre IS NOT NULL, c.nombre, 'Publico General')  NOMBRE_CLIENTE, s.idSuc ID_SUCURSAL, s.nombre NOMBRE_SUCURSAL 
            FROM	app_devolucioncli d
            LEFT JOIN app_devolucioncli_datos dd ON d.id = dd.id_devolucion
            LEFT JOIN app_pos_venta_producto vp ON d.id_ov = vp.idVenta AND dd.id_producto = vp.idventa_producto
            LEFT JOIN app_pos_venta v ON d.id_ov = v.idVenta
            LEFT JOIN comun_cliente c ON v.idCliente = c.id 
            LEFT JOIN mrp_sucursal s ON v.idSucursal = s.idSuc
            WHERE	$filtro
            GROUP BY v.idVenta
            ORDER BY v.idVenta"
            ;
            
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function detalleDevolucioneVenta($params)
  {
    $filtro = 'true ';
    $filtro .= "AND m.referencia LIKE CONCAT('Devolución de venta ', '{$params['sale']}', '%')";

    $sql = "SELECT	 '{$params['sale']}' ID_VENTA, SUM(m.cantidad) CANTIDAD, p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO, m.id_producto_caracteristica caracteristicas, l.no_lote lote,   p.series tieneSerie, 
            (
                SELECT  (vp.preciounitario+(vp.impuestosproductoventa / vp.cantidad)) IMPORTE_UNITARIO
                FROM	app_pos_venta_producto vp
                WHERE	p.id = vp.idProducto
                LIMIT	1
            ) IMPORTE_UNITARIO
            FROM	app_inventario_movimientos m 
            LEFT JOIN app_producto_lotes l ON m.id_lote = l.id
            LEFT JOIN app_productos p ON m.id_producto = p.id 
            WHERE	$filtro
            GROUP BY m.id_producto, m.id_producto_caracteristica, m.id_lote	
            ORDER BY p.nombre"
            ;
            
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function detalleDevolucioneVentaSeries($params)
  {
    $filtro = 'true ';
    $filtro .= " AND m.referencia LIKE CONCAT('Devolución de venta ', '{$params['sale']}', '%') ";
    $filtro .= " AND p.id = '{$params['product']}'";

    $sql = "SELECT	 SUM(m.cantidad) CANTIDAD, p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO, m.id_producto_caracteristica caracteristicas, l.no_lote lote,   p.series tieneSerie, 
            (
                SELECT  (preciounitario+(vp.impuestosproductoventa / vp.cantidad)) IMPORTE_UNITARIO
                FROM	app_pos_venta_producto vp
                WHERE	p.id = vp.idProducto
                LIMIT	1
            ) IMPORTE_UNITARIO, s.serie NOMBRE_SERIE
            FROM	app_inventario_movimientos m 
            LEFT JOIN app_producto_lotes l ON m.id_lote = l.id
            LEFT JOIN app_productos p ON m.id_producto = p.id 
            LEFT JOIN app_producto_serie_rastro sr ON m.id = sr.id_mov
            LEFT JOIN app_producto_serie s ON sr.id_serie = s.id
            WHERE	$filtro
            GROUP BY m.id_producto, m.id_producto_caracteristica, m.id_lote, s.id
            ORDER BY p.nombre, s.serie ;
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  function getCaracteristicas()
  {
      $sql = "SELECT p.id ID_P, h.id ID_H, p.nombre CRARACTERISTICA_PADRE, h.nombre CARACTERISTICA_HIJA
              FROM    app_caracteristicas_padre p
              INNER JOIN  app_caracteristicas_hija h ON p.id = h.id_caracteristica_padre
              ORDER BY ID_P, ID_H;";
      $res = $this->queryArray($sql);
      return $res['rows'];
  }


}
