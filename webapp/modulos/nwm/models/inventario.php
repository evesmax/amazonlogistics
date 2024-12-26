<?php

require("models/connection_sqli_manual.php");

class InventarioModel extends Connection {

  public function obtenerProductosEnInventario($params)
  {
    $patrones = [ '/\[/', '/\]/' ];
    $sustituciones = [ '(', ')'];
    $filtro = 'true ';
    $aux = preg_replace($patrones, $sustituciones, $params['warehouse'] );
    $filtro .= $params['warehouse'] != '[]' ? " AND a.id IN $aux" : '';
    $aux = preg_replace($patrones, $sustituciones, $params['provider'] );
    $filtro .= $params['provider'] != '[]' ? " AND p.id IN
              (SELECT	id_producto
                FROM	app_producto_proveedor sub_p
                WHERE	sub_p.id_proveedor IN $aux)" : '';
    $aux = preg_replace($patrones, $sustituciones, $params['product'] );
    $filtro .= $params['product'] != '[]' ? " AND p.id IN $aux" : '';

    $sql = "SELECT
            	IF( i.caracteristicas != '\'0\'', 1, 0 ) TIENE_CARACTERISTICA, p.series TIENE_SERIE, p.lotes TIENE_LOTE,
            	a.id ID_ALMACEN, a.codigo_manual CODIGO_ALMACEN, a.nombre NOMBRE_ALMACEN,
            	p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO,

            	uc.id ID_UNIDAD_COMPRA, uc.clave CODIGO_UNIDAD_COMPRA, uc.nombre NOMBRE_UNIDAD_COMPRA, 
              #c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) COSTO_UNITARIO,
              c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, ( IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) )  * uc.factor  COSTO_UNITARIO,
              (SUM(i.cantidad) / (uc.factor) ) EXISTENCIA_COMPRA, 
              ( SUM(i.apartados) / (uc.factor) ) APARTADOS_COMPRA, 
              ( (SUM(i.cantidad) - SUM(i.apartados)) / (uc.factor) ) DISPONIBLE_COMPRA,
              SUM(i.valor) VALOR_COMPRA,
              
            	uv.id ID_UNIDAD_VENTA, uv.clave CODIGO_UNIDAD_VENTA, uv.nombre NOMBRE_UNIDAD_VENTA, 
              p.precio PRECIO_UNITARIO, 
              SUM(i.cantidad) EXISTENCIA_VENTA, 
              SUM(i.apartados) APARTADOS_VENTA, 
              (SUM(i.cantidad) - SUM(i.apartados)) DISPONIBLE_VENTA ,
              ( (SUM(i.cantidad) - SUM(i.apartados)) * p.precio ) VALOR_VENTA,

            	{$params['measurement']} UNIT_OF_MEASUREMENT
            FROM	app_inventario i
            LEFT JOIN app_almacenes a ON i.id_almacen = a.id
            LEFT JOIN	app_productos p ON i.id_producto = p.id
            LEFT JOIN	app_unidades_medida uc ON p.id_unidad_compra = uc.id
            LEFT JOIN	app_unidades_medida uv ON p.id_unidad_venta = uv.id
            LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
            LEFT JOIN	(
            	SELECT	id_producto, MAX(id) id_movimiento
            	FROM	app_inventario_movimientos
            	WHERE	referencia LIKE '%Orden de compra / recepcion%'
            	GROUP BY id_producto
            ) m_aux ON  i.id_producto = m_aux.id_producto
            LEFT JOIN	app_inventario_movimientos m ON m_aux.id_movimiento = m.id
            WHERE  p.tipo_producto NOT IN (2,5,6,7) AND $filtro
            GROUP BY i.id_almacen, i.id_producto
            ORDER BY i.id_almacen, i.id_producto
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function obtenerCaracteristicasEnInventario($params)
  {
    $patrones = [ '/\[/', '/\]/' ];
    $sustituciones = [ '(', ')'];
    $filtro = 'true ';
    $aux = preg_replace($patrones, $sustituciones, $params['warehouse'] );
    $filtro .= $params['warehouse'] != '[]' ? " AND a.id IN $aux" : '';
    $filtro .= " AND p.id = {$params['product']}";

    $sql = "SELECT
            	i.caracteristicas, p.series TIENE_SERIE, p.lotes TIENE_LOTE,
            	a.id ID_ALMACEN, a.codigo_manual CODIGO_ALMACEN, a.nombre NOMBRE_ALMACEN,
            	p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO,

            	uc.id ID_UNIDAD_COMPRA, uc.clave CODIGO_UNIDAD_COMPRA, uc.nombre NOMBRE_UNIDAD_COMPRA, 
              #c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) COSTO_UNITARIO,
              c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, ( IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) )  * uc.factor  COSTO_UNITARIO,
              (SUM(i.cantidad) / (uc.factor) ) EXISTENCIA_COMPRA, 
              ( SUM(i.apartados) / (uc.factor) ) APARTADOS_COMPRA, 
              ( (SUM(i.cantidad) - SUM(i.apartados)) / (uc.factor) ) DISPONIBLE_COMPRA,
              SUM(i.valor) VALOR_COMPRA,
              
            	uv.id ID_UNIDAD_VENTA, uv.clave CODIGO_UNIDAD_VENTA, uv.nombre NOMBRE_UNIDAD_VENTA, 
              p.precio PRECIO_UNITARIO, 
              SUM(i.cantidad) EXISTENCIA_VENTA, 
              SUM(i.apartados) APARTADOS_VENTA, 
              (SUM(i.cantidad) - SUM(i.apartados)) DISPONIBLE_VENTA ,
              ( (SUM(i.cantidad) - SUM(i.apartados)) * p.precio ) VALOR_VENTA,

            	{$params['measurement']} UNIT_OF_MEASUREMENT
            FROM	app_inventario i
            LEFT JOIN app_almacenes a ON i.id_almacen = a.id
            LEFT JOIN	app_productos p ON i.id_producto = p.id
            LEFT JOIN	app_unidades_medida uc ON p.id_unidad_compra = uc.id
            LEFT JOIN	app_unidades_medida uv ON p.id_unidad_venta = uv.id
            LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
            LEFT JOIN	(
            	SELECT	id_producto, MAX(id) id_movimiento
            	FROM	app_inventario_movimientos
            	WHERE	referencia LIKE '%Orden de compra / recepcion%'
            	GROUP BY id_producto
            ) m_aux ON  i.id_producto = m_aux.id_producto
            LEFT JOIN	app_inventario_movimientos m ON m_aux.id_movimiento = m.id
            WHERE $filtro
            GROUP BY i.id_almacen, i.caracteristicas
            ORDER BY i.id_almacen, i.caracteristicas
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

  public function obtenerLotesEnInventario($params)
  {
    $patrones = [ '/\[/', '/\]/' ];
    $sustituciones = [ '(', ')'];
    $filtro = 'true ';
    $aux = preg_replace($patrones, $sustituciones, $params['warehouse'] );
    $filtro .= $params['warehouse'] != '[]' ? " AND a.id IN $aux" : '';
    $filtro .= " AND p.id = {$params['product']}";

    $sql = "SELECT
            	l.no_lote lote, l.fecha_fabricacion fabricacion, l.fecha_caducidad caducidad, IF( i.caracteristicas != '\'0\'', 1, 0 ) TIENE_CARACTERISTICA, p.series TIENE_SERIE,
            	a.id ID_ALMACEN, a.codigo_manual CODIGO_ALMACEN, a.nombre NOMBRE_ALMACEN,
            	p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO,
            	
            	uc.id ID_UNIDAD_COMPRA, uc.clave CODIGO_UNIDAD_COMPRA, uc.nombre NOMBRE_UNIDAD_COMPRA, 
              #c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) COSTO_UNITARIO,
              c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, ( IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) )  * uc.factor  COSTO_UNITARIO,
              (SUM(i.cantidad) / (uc.factor) ) EXISTENCIA_COMPRA, 
              ( SUM(i.apartados) / (uc.factor) ) APARTADOS_COMPRA, 
              ( (SUM(i.cantidad) - SUM(i.apartados)) / (uc.factor) ) DISPONIBLE_COMPRA,
              SUM(i.valor) VALOR_COMPRA,
              
            	uv.id ID_UNIDAD_VENTA, uv.clave CODIGO_UNIDAD_VENTA, uv.nombre NOMBRE_UNIDAD_VENTA, 
              p.precio PRECIO_UNITARIO, 
              SUM(i.cantidad) EXISTENCIA_VENTA, 
              SUM(i.apartados) APARTADOS_VENTA, 
              (SUM(i.cantidad) - SUM(i.apartados)) DISPONIBLE_VENTA ,
              ( (SUM(i.cantidad) - SUM(i.apartados)) * p.precio ) VALOR_VENTA,

            	{$params['measurement']} UNIT_OF_MEASUREMENT
            FROM	app_inventario i
            LEFT JOIN app_producto_lotes l ON i.lote = l.id
            LEFT JOIN app_almacenes a ON i.id_almacen = a.id
            LEFT JOIN	app_productos p ON i.id_producto = p.id
            LEFT JOIN	app_unidades_medida uc ON p.id_unidad_compra = uc.id
            LEFT JOIN	app_unidades_medida uv ON p.id_unidad_venta = uv.id
            LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
            LEFT JOIN	(
            	SELECT	id_producto, MAX(id) id_movimiento
            	FROM	app_inventario_movimientos
            	WHERE	referencia LIKE '%Orden de compra / recepcion%'
            	GROUP BY id_producto
            ) m_aux ON  i.id_producto = m_aux.id_producto
            LEFT JOIN	app_inventario_movimientos m ON m_aux.id_movimiento = m.id
            WHERE $filtro
            GROUP BY i.id_almacen, i.lote
            ORDER BY i.id_almacen, i.lote
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function obtenerSeriesEnInventario($params)
  {
    $patrones = [ '/\[/', '/\]/' ];
    $sustituciones = [ '(', ')'];
    $filtro = 'true ';
    $aux = preg_replace($patrones, $sustituciones, $params['warehouse'] );
    $filtro .= $params['warehouse'] != '[]' ? " AND a.id IN $aux" : '';
    $filtro .= " AND p.id = {$params['product']}";

    $sql = "SELECT
            	s.serie serie, IF( i.caracteristicas != '\'0\'', 1, 0 ) TIENE_CARACTERISTICA, p.lotes TIENE_LOTE,
            	a.id ID_ALMACEN, a.codigo_manual CODIGO_ALMACEN, a.nombre NOMBRE_ALMACEN,
            	p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO,

            	uc.id ID_UNIDAD_COMPRA, uc.clave CODIGO_UNIDAD_COMPRA, uc.nombre NOMBRE_UNIDAD_COMPRA, 
              #c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) COSTO_UNITARIO,
              c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, ( IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) )  * uc.factor  COSTO_UNITARIO,
              (SUM(i.cantidad) / (uc.factor) ) EXISTENCIA_COMPRA, 
              ( SUM(i.apartados) / (uc.factor) ) APARTADOS_COMPRA, 
              ( (SUM(i.cantidad) - SUM(i.apartados)) / (uc.factor) ) DISPONIBLE_COMPRA,
              SUM(i.valor) VALOR_COMPRA,
              
            	uv.id ID_UNIDAD_VENTA, uv.clave CODIGO_UNIDAD_VENTA, uv.nombre NOMBRE_UNIDAD_VENTA, 
              p.precio PRECIO_UNITARIO, 
              SUM(i.cantidad) EXISTENCIA_VENTA, 
              SUM(i.apartados) APARTADOS_VENTA, 
              (SUM(i.cantidad) - SUM(i.apartados)) DISPONIBLE_VENTA ,
              ( (SUM(i.cantidad) - SUM(i.apartados)) * p.precio ) VALOR_VENTA,

            	{$params['measurement']} UNIT_OF_MEASUREMENT
            FROM	app_inventario i
            LEFT JOIN app_producto_serie s ON i.id_producto = s.id_producto AND i.id_almacen = s.id_almacen
            LEFT JOIN app_almacenes a ON i.id_almacen = a.id
            LEFT JOIN	app_productos p ON i.id_producto = p.id
            LEFT JOIN	app_unidades_medida uc ON p.id_unidad_compra = uc.id
            LEFT JOIN	app_unidades_medida uv ON p.id_unidad_venta = uv.id
            LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
            LEFT JOIN	(
            	SELECT	id_producto, MAX(id) id_movimiento
            	FROM	app_inventario_movimientos
            	WHERE	referencia LIKE '%Orden de compra / recepcion%'
            	GROUP BY id_producto
            ) m_aux ON  i.id_producto = m_aux.id_producto
            LEFT JOIN	app_inventario_movimientos m ON m_aux.id_movimiento = m.id
            WHERE s.estatus = '0' AND $filtro
            GROUP BY i.id_almacen, s.id
            ORDER BY i.id_almacen, s.id
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function obtenerMovimientos($params)
  {
    $filtro = 'true ';
    $filtro .= $params['warehouse'] != 0 ? " AND (ao.id = {$params['warehouse']} OR ad.id = {$params['warehouse']})" : '';
    $filtro .= $params['product'] != 0 ? " AND p.id = {$params['product']}" : '';

    $sql = "SELECT

            	p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO, m.id_producto_caracteristica caracteristicas, l.no_lote lote,   p.series tieneSerie,
            	u.idempleado ID_RESPONSABLE, u.usuario RESPONSABLE, m.id ID_MOVIMIENTO, m.fecha FECHA, m.referencia DETALLE, m.tipo_traspaso TIPO_MOVIMIENTO,

            	ad.id ID_ALMACEN_DESTINO, ad.codigo_manual CODIGO_ALMACEN_DESTINO, ad.nombre NOMBRE_ALMACEN_DESTINO,
              ao.id ID_ALMACEN_ORIGEN, ao.codigo_manual CODIGO_ALMACEN_ORIGEN, ao.nombre NOMBRE_ALMACEN_ORIGEN,


            	IF( m.id_almacen_destino, m.cantidad, '0' ) CANTIDAD_ENTRADA, IF( m.id_almacen_destino, m.costo, '0' ) COSTO_UNITARIO_ENTRADA, IF( m.id_almacen_destino, (m.cantidad * m.costo), '0' ) COSTO_ENTRADA,
              IF( m.id_almacen_origen != 0, m.cantidad, '0' ) CANTIDAD_SALIDA, IF( m.id_almacen_origen != 0, m.costo, '0' ) COSTO_UNITARIO_SALIDA, IF( m.id_almacen_origen != 0, (m.cantidad * m.costo), '0' ) COSTO_SALIDA,
              m.cantidad CANTIDAD_SALDO, m.costo COSTO_UNITARIO_SALDO, (m.cantidad * m.costo) COSTO_SALDO

            FROM    app_inventario_movimientos m
            LEFT JOIN 	accelog_usuarios u ON m.id_empleado = u.idempleado
            LEFT JOIN app_producto_lotes l ON m.id_lote = l.id
            LEFT JOIN	app_almacenes ao ON m.id_almacen_origen = ao.id
            LEFT JOIN	app_almacenes ad ON m.id_almacen_destino = ad.id
            LEFT JOIN	app_productos p ON m.id_producto = p.id
            #LEFT JOIN	app_producto_proveedor pp ON p.id = pp.id_producto
            LEFT JOIN	app_producto_caracteristicas pc ON p.id = pc.id_producto
            LEFT JOIN	app_unidades_medida uc ON p.id_unidad_compra = uc.id
            LEFT JOIN	app_unidades_medida uv ON p.id_unidad_venta = uv.id
            LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
            WHERE m.estatus = '1' AND m.tipo_traspaso != '3' AND p.tipo_producto NOT IN (2,5,6,7) AND $filtro
            GROUP BY 	p.id, m.id
            ORDER BY 	p.id, m.id
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function obtenerSeriesMovimiento($params)
  {
    $filtro = 'true ';
    $filtro .= " AND r.id_mov = '{$params['movement']}'";
    $filtro .= " AND s.id_producto = '{$params['product']}'";

    $sql = "SELECT	s.id ID_SERIE, s.serie NOMBRE_SERIE
            FROM	app_producto_serie_rastro r
            LEFT JOIN app_producto_serie s ON r.id_serie = s.id
            WHERE	$filtro
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

  public function antiguedadInventario( $clasificador, $parentClasific) {

    switch ($clasificador) {
      case '1':
      $filtro = "AND p.departamento='$parentClasific'";
        break;
      case '2':
      $filtro = "AND p.familia='$parentClasific'";
        break;
      case '3':
        $filtro = "AND p.linea='$parentClasific'";
        break;
      case '4':
        $filtro = "AND p.id='$parentClasific'";
        break;
      default:
      # code...
      break;
    }
    if($parentClasific == 0)
      $filtro = "";

    $sql = "SELECT	d.nombre DERTAMENTO, p.id ID, p.codigo CODIGO, p.nombre NOMBRE, uc.clave UNIDAD_COMPRA,
                  SUM(i.cantidad) CANTIDAD, SUM(i.cantidad) * ( IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) ) FECHA, DATE_ADD( NOW(), INTERVAL -60 DAY) , NOW(),
                  IF( m.fecha BETWEEN DATE_ADD( NOW(), INTERVAL -60 DAY) AND NOW() , 
                    '60', 
                    IF( m.fecha BETWEEN DATE_ADD( NOW(), INTERVAL -90 DAY) AND NOW() , 
                      '90', 
                      IF( m.fecha BETWEEN DATE_ADD( NOW(), INTERVAL -120 DAY) AND NOW() , 
                        '120', 
                        '+'
                      )
                    )
                  ) RANGO
              FROM	app_productos p
              LEFT JOIN app_departamento d ON p.departamento = d.id
              LEFT JOIN app_unidades_medida uc ON p.id_unidad_compra = uc.id
              #LEFT JOIN app_inventario_movimientos m ON p.id = m.id_producto
              LEFT JOIN	(
                SELECT	id_producto, MAX(id) id_movimiento
                FROM	app_inventario_movimientos
                WHERE	referencia LIKE '%Orden de compra / recepcion%'
                GROUP BY id_producto
              ) m_aux ON  p.id = m_aux.id_producto
              LEFT JOIN	app_inventario_movimientos m ON m_aux.id_movimiento = m.id
              LEFT JOIN	app_inventario i ON p.id = i.id_producto
              LEFT JOIN	app_productos pr ON i.id_producto = pr.id
              LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
              WHERE	p.tipo_producto NOT IN (2,5,6,7) $filtro
              GROUP BY p.id;";

    $res = $this->queryArray($sql);

    return $res['rows'];
  }

  public function detalleAntiguedadInventario($params) {
    $producto = $params['product'];
    $sql = "SELECT	r.id NUMERO_RECEPCION, r.fecha_recepcion FECHA_ENTREGA, a.nombre ALMACEN_DESTINO, p.razon_social PROVEEDOR, m.cantidad CANTIDAD, c.codigo MONEDA, m.costo COSTO_UNITARIO, (m.costo*m.cantidad) IMPORTE_TOTAL
            FROM    app_inventario_movimientos m
            LEFT JOIN app_recepcion r ON SUBSTR(m.referencia, 30) = r.id
            LEFT JOIN app_almacenes a ON m.id_almacen_destino = a.id
            LEFT JOIN app_ocompra oc ON r.id_oc = oc.id
            LEFT JOIN mrp_proveedor p ON oc.id_proveedor = p.idPrv
            LEFT JOIN app_recepcion_xml rx ON SUBSTR(m.referencia, 30) = rx.id
            LEFT JOIN cont_coin c ON rx.moneda = c.coin_id
            WHERE	m.referencia LIKE '%Orden de compra / recepcion -%' and m.id_producto = '$producto';
          ;";
        $res = $this->queryArray($sql);
        return $res['rows'];
  }

  public function rotacionInventario($params)
  {
    $filtro = 'true ';
    switch ($params['clasificado']) {
      case '1':
      $filtro .= $params['department'] != 0 ? "AND p.departamento='{$params['department']}'" : '';
        break;
      case '4':
        $filtro .= $params['product'] != 0 ? "AND p.id='{$params['product']}'" : '';
        break;
      default:
      # code...
      break;
    }

    $sql = "SELECT
              d.id ID_DEPARTAMENTO, d.nombre NOMBRE_DEPARTAMENTO, p.id ID_PRODUCTO, p.codigo CODIGO_PRODUCTO, p.nombre NOMBRE_PRODUCTO,

              uc.id ID_UNIDAD_COMPRA, uc.clave CODIGO_UNIDAD_COMPRA, uc.nombre NOMBRE_UNIDAD_COMPRA, 
              c.id ID_COSTEO, c.nombre NOMBRE_COSTEO, ( IF( c.id = '1' OR/*TODO Pendiente costo especifico*/ c.id = '6', (SUM(i.valor)/SUM(i.cantidad)) , IF( c.id = '3', m.costo , '0' ) ) )  * uc.factor  COSTO_UNITARIO,
              (SUM(i.cantidad) / (uc.factor) ) AS EXISTENCIA_COMPRA, 
              ( SUM(i.apartados) / (uc.factor) ) APARTADOS_COMPRA, 
              ( (SUM(i.cantidad) - SUM(i.apartados)) / (uc.factor) ) DISPONIBLE_COMPRA,
              SUM(i.valor) VALOR_COMPRA,
              
              ventas_periodo.ventas_periodo VENTAS_PERIODO, 
              ( ( ventas_periodo.ventas_periodo ) / ( ( SUM(i.cantidad) / (uc.factor) ) ) ) INDICE_ROTACION ,
              ( DATEDIFF( NOW() , DATE_SUB(NOW(), INTERVAL {$params['range']} MONTH) )   /   ( ventas_periodo.ventas_periodo / ( ( SUM(i.cantidad) / (uc.factor) ) ) )  ) DIAS_PERMANENCIA_INVENTARIO

            FROM	app_inventario i
            LEFT JOIN	app_productos p ON i.id_producto = p.id
            LEFT JOIN	app_departamento d ON d.id = p.departamento
            LEFT JOIN	app_unidades_medida uc ON p.id_unidad_compra = uc.id
            LEFT JOIN	app_costeo c ON p.id_tipo_costeo  = c.id
            LEFT JOIN	(
              SELECT	id_producto, MAX(id) id_movimiento
              FROM	app_inventario_movimientos
              WHERE	referencia LIKE '%Orden de compra / recepcion%'
              GROUP BY id_producto
            ) m_aux ON  i.id_producto = m_aux.id_producto
            LEFT JOIN	app_inventario_movimientos m ON m_aux.id_movimiento = m.id
            
            LEFT JOIN	(
              SELECT	id_producto, SUM(cantidad) ventas_periodo
              FROM	app_inventario_movimientos
              WHERE	referencia LIKE 'Venta %' AND fecha BETWEEN DATE_SUB(NOW(), INTERVAL {$params['range']} MONTH) AND NOW() 
              GROUP BY id_producto
            ) ventas_periodo ON  i.id_producto = ventas_periodo.id_producto
            
            WHERE  p.tipo_producto NOT IN (2,5,6,7) AND $filtro
            GROUP BY i.id_producto
            ORDER BY i.id_producto
            ;";
    $res = $this->queryArray($sql);
    return $res['rows'];
  }

}
