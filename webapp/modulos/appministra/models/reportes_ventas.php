<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class Reportes_VentasModel extends Connection
{
    

    public function listaProveedores()
    {
        $myQuery = "SELECT idPrv,codigo,razon_social FROM mrp_proveedor ORDER BY idPrv";
        return $this->query($myQuery);
    }

    public function listaClientes()
    {
        $myQuery = "SELECT id, codigo, nombre FROM comun_cliente ORDER BY id";
        return $this->query($myQuery);
    }

    public function listaSucursales()
    {
        $myQuery = "SELECT idSuc, nombre FROM mrp_sucursal WHERE activo = -1";
        return $this->query($myQuery);
    }

    public function listaUsuarios()
    {
        $myQuery = "SELECT idEmpleado, CONCAT('(',codigo,') ', nombreEmpleado,' ',apellidoPaterno) AS Usuario FROM nomi_empleados WHERE activo = -1";
        return $this->query($myQuery);
    }

    public function listaProductos()
    {
        $myQuery = "SELECT id, CONCAT('(',codigo,') ',nombre) AS Producto FROM app_productos WHERE status = 1";
        return $this->query($myQuery);
    }

    public function listaDepartamentos()
    {
        $myQuery = "SELECT id, nombre FROM app_departamento";
        return $this->query($myQuery);
    }

    public function listaUnidadesBase()
    {
        $myQuery = "SELECT id, clave, nombre FROM app_unidades_medida WHERE activo = 1";
        return $this->query($myQuery);
    }

    public function reforteGrafico($desde,$hasta,$ordenar,$radio)
    {
        if($radio==1){
            if($ordenar==1){
                $myQuery = "SELECT b.nombre,  sum(a.cantidad) as total from app_inventario_movimientos a
    inner join app_productos b on b.id=a.id_producto
    where tipo_traspaso=0 and referencia like 'Orden de venta%'
    and a.fecha between '".$desde."' and '".$hasta."'
    group by id_producto
    order by sum(a.cantidad) desc limit 5";
            }
            if($ordenar==2){
                $myQuery = "SELECT b.nombre,  sum(a.cantidad) as total from app_inventario_movimientos a
    inner join app_productos b on b.id=a.id_producto
    where tipo_traspaso=0 and referencia like 'Orden de venta%'
    and a.fecha between '".$desde."' and '".$hasta."'
    group by id_producto
    order by sum(a.cantidad) asc limit 5";
            }
        }

        if($radio==2){
            if($ordenar==1){
                $myQuery = "SELECT b.nombre,  sum(a.importe) as total from app_inventario_movimientos a
    inner join app_productos b on b.id=a.id_producto
    where tipo_traspaso=0 and referencia like 'Orden de venta%'
    and a.fecha between '".$desde."' and '".$hasta."'
    group by id_producto
    order by sum(a.importe) desc limit 5";
            }
            if($ordenar==2){
                $myQuery = "SELECT b.nombre,  sum(a.importe) as total from app_inventario_movimientos a
    inner join app_productos b on b.id=a.id_producto
    where tipo_traspaso=0 and referencia like 'Orden de venta%'
    and a.fecha between '".$desde."' and '".$hasta."'
    group by id_producto
    order by sum(a.importe) asc limit 5";
            }
        }

        return $this->query($myQuery);
    }

    public function cobranza_vendedor_reporte($vars)
    {
        $idVds = $vars['idVds'];
        $vendedores = "";
        if(intval($vars['rango']))
            $vendedores .= " AND v.id_usrcompra BETWEEN ".$idVds[0]." AND ".$idVds[1];
        else
        {
            if($idVds[0])
            {
                $vendedores .= " AND (";
                for($i=0;$i<=count($idVds)-1;$i++)
                {
                    if($i>0)
                        $vendedores .= " OR ";
                    $vendedores .= " v.id_usrcompra = ".$idVds[$i];
                }
                $vendedores .= ") ";
            }   
        }
        $cliente = '';
        if(intval($vars['cliente']))
        {
            $cliente = "AND v.id_cliente = ".$vars['cliente'];
            $cliente2 = "AND pf.id_cliente = ".$vars['cliente'];
            $cliente3 = "AND p.id_prov_cli = ".$vars['cliente'];
        }

        

        $tipoF = '';
        if($vars['tipo_doc'] == 'F')
            $tipoF = "AND folio != ''";
        if($vars['tipo_doc'] == 'C')
            $tipoF = "AND folio = ''";        

        $myQuery = "";
        if($vars['tipo_doc'] == 'F' || $vars['tipo_doc'] == '0')
        {
            $myQuery .= "(SELECT
            IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente)) AS id_usrcompra,
            e.id_encargado,
            (SELECT CONCAT(nombreEmpleado,' ',apellidoPaterno) FROM nomi_empleados WHERE idEmpleado = IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente))) AS Vendedor,
            v.id_cliente,
            (SELECT CONCAT(nombre,' (',nombretienda,')') FROM comun_cliente WHERE id = v.id_cliente) AS Cliente,
            fecha_envio AS fecha, rf.tipoComp, 
            e.id_oventa, rf.folio, e.total*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio) AS imp_factura, 
            (SELECT p.fecha_pago FROM app_pagos p INNER JOIN app_pagos_relacion pr ON pr.id_pago = p.id WHERE pr.id_documento = rf.id AND pr.id_tipo = 1 ORDER BY fecha_pago DESC LIMIT 1) AS fecha_abono,
            @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id  = rp.id_pago WHERE rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
            @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
            (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos
            FROM app_respuestaFacturacion rf
            INNER JOIN app_envios e ON e.id = rf.idSale
            INNER JOIN app_oventa v ON v.id = e.id_oventa
            INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion

            WHERE 
            e.fecha_envio BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."' 
            $cliente 
            $vendedores 
            )
            ";
        }
        if($vars['tipo_doc'] == '0')
        {
            $myQuery .= "UNION ALL";
        }

        if($vars['tipo_doc'] == 'C' || $vars['tipo_doc'] == '0')
        {
            $myQuery .= "(SELECT 
            IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente)) AS id_usrcompra,
            e.id_encargado,
            (SELECT CONCAT(nombreEmpleado,' ',apellidoPaterno) FROM nomi_empleados WHERE idEmpleado = IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente))) AS Vendedor,
            v.id_cliente,
            (SELECT CONCAT(nombre,' (',nombretienda,')') FROM comun_cliente WHERE id = v.id_cliente) AS Cliente,
            fecha_envio AS fecha, 'C' AS tipoComp, 
            e.id_oventa, e.id AS folio, e.total*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio) AS imp_factura,
            (SELECT pp.fecha_pago FROM app_pagos pp INNER JOIN app_pagos_relacion pr ON pr.id_pago = pp.id WHERE pr.id_documento = p.id AND pr.id_tipo = 0 ORDER BY fecha_pago DESC LIMIT 1) AS fecha_abono,
            @c := (SELECT SUM(rp.cargo*pp.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos pp ON pp.id  = rp.id_pago WHERE rp.id_documento = p.id AND rp.id_tipo=0 AND pp.cobrar_pagar = 0),
            @a := (SELECT SUM(rp.abono*pp.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos pp ON pp.id = rp.id_pago WHERE rp.id_documento = p.id AND rp.id_tipo=0 AND p.cobrar_pagar = 0),
            (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos
            FROM app_pagos p
            INNER JOIN app_envios e ON e.id = SUBSTRING_INDEX(p.concepto, '-', -1)
            INNER JOIN app_oventa v ON v.id = e.id_oventa
            INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion
            WHERE 
            p.fecha_pago BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."' 
            $cliente3 
            AND p.cobrar_pagar = 0
            AND p.cargo > 0
            AND p.origen = 1
            $vendedores 
            )";

            //Ventas sin credito ni factura
            $myQuery .= "UNION ALL
            (SELECT 
            IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente)) AS id_usrcompra,
            e.id_encargado,
            (SELECT CONCAT(nombreEmpleado,' ',apellidoPaterno) FROM nomi_empleados WHERE idEmpleado = IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  v.id_cliente))) AS Vendedor,
            v.id_cliente,
            (SELECT CONCAT(nombre,' (',nombretienda,')') FROM comun_cliente WHERE id = v.id_cliente) AS Cliente,
            fecha_envio AS fecha, 'C' AS tipoComp, 
            e.id_oventa, e.id AS folio, e.total*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio) AS imp_factura,
            e.fecha_envio AS fecha_abono,
            @c := 0,
            @a :=0,
            e.total*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio) AS pagos          
            FROM app_envios e
            INNER JOIN app_oventa v ON v.id = e.id_oventa
            INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion
            WHERE e.fecha_envio BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."' 
            AND e.id NOT IN (SELECT idSale FROM app_respuestaFacturacion)
            AND e.id NOT IN (SELECT id_sale FROM app_pendienteFactura WHERE id_respFact != 0)
            AND e.forma_pago != 6
            $cliente
            $vendedores  

)
";
        }

        if($vars['tipo_doc'] == 'F' || $vars['tipo_doc'] == '0')
        {
            $myQuery .= "UNION ALL
            (SELECT 
            IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  pf.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  pf.id_cliente)) AS id_usrcompra,
            e.id_encargado,
            (SELECT CONCAT(nombreEmpleado,' ',apellidoPaterno) FROM nomi_empleados WHERE idEmpleado = IF(!(SELECT idVendedor FROM comun_cliente WHERE id =  pf.id_cliente),e.id_encargado,(SELECT idVendedor FROM comun_cliente WHERE id =  pf.id_cliente))) AS Vendedor,
            pf.id_cliente,
            (SELECT CONCAT(nombre,' (',nombretienda,')') FROM comun_cliente WHERE id = pf.id_cliente) AS Cliente,
            pf.fecha AS fecha, rf.tipoComp, 
            e.id_oventa, rf.folio, SUM(pf.monto*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio)) AS imp_factura, 
            (SELECT p.fecha_pago FROM app_pagos p INNER JOIN app_pagos_relacion pr ON pr.id_pago = p.id WHERE pr.id_documento = rf.id AND pr.id_tipo = 1 ORDER BY fecha_pago DESC LIMIT 1) AS fecha_abono,
            @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id  = rp.id_pago WHERE rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
            @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
            (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos
            FROM app_pendienteFactura pf
            LEFT JOIN app_respuestaFacturacion rf ON pf.id_respFact = rf.id
            INNER JOIN  app_envios e ON e.id = pf.id_sale AND e.forma_pago = 6
            INNER JOIN app_oventa v ON v.id = e.id_oventa
            INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion

            WHERE 
            e.fecha_envio BETWEEN '".$vars['f_ini']."' AND '".$vars['f_fin']."' 
            AND pf.id_respFact != 0
            $cliente2 
            AND rf.idSale = 0
            AND rf.borrado = 0
            AND rf.xmlfile != ''
            AND rf.idFact != 0
            AND rf.tipoComp = 'F'
            AND rf.origen = 1
            $vendedores 
            GROUP BY pf.id_respFact
            )";
        }
        $myQuery .= "ORDER BY id_usrcompra, id_cliente, fecha;";
        return $this->query($myQuery);
    }

}
?>
