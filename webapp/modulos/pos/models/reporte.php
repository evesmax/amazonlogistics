<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL
require("models/connection_sqli_manual.php"); // funciones mySQLi

class ReporteModel extends Connection
{

    public function indexGridProductos(){
        $query = "SELECT * from app_productos order by id asc";
        $rest = $this->queryArray($query);

        return $rest['rows'];
    }
    public function buscaClientes($term) {
        /*obtiene los clientes*/
        $queryClientes = "SELECT  id,nombre ";
        $queryClientes .= " FROM comun_cliente ";
        $queryClientes .= " WHERE nombre like '%" . $term . "%' order by nombre desc ";

        $result = $this->queryArray($queryClientes);
        //print_r($result["rows"]);
        return $result["rows"];

    }
    public function filtros(){
        $query1 = "SELECT * from mrp_sucursal";
        $res1 = $this->queryArray($query1);

        return array('sucursales' => $res1['rows']);
    }
    public function repVentasTotales($desde,$hasta,$sucursal,$orden, $cliente,$empleado, $formaPago){
        $inicio = $desde;
        $fin = $hasta;
        $filtro="1=1";
        $filtro2="1=1";
        //echo 'inicioi='.$inicio.' hasta='.$fin;
        if($fin!="")
        {
            list($a,$m,$d)=explode("-",$fin);
            $fin=$a."-".$m."-".((int)$d+1);
        }


        if($inicio!="" && $fin=="")
        {
            $filtro.=" and v.fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro.=" and v.fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro.=" and v.fecha <= '".$fin."' and   v.fecha >= '".$inicio."' ";
        }


        if($inicio!="" && $fin=="")
        {
            $filtro2.=" and v.fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro2.=" and v.fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro2.=" and v.fecha <= '".$fin."' and   v.fecha >= '".$inicio."' ";
        }

        if($sucursal!=0){
            $filtro.=' and v.idSucursal="'.$sucursal.'"';
            $filtro2.=' and v.idSucursal="'.$sucursal.'"';
        }
        if($cliente!=0){
            $filtro.=' and v.idCliente="'.$cliente.'"';
            $filtro2.=' and v.idCliente="'.$cliente.'"';
        }
        if($empleado != 0)
            {
                $filtro.=" and e.idempleado=".$empleado;
                $filtro2.=" and e.idempleado=".$empleado;
            }
        if($formaPago!=0){
            $filtro.=' and p.idFormapago="'.$formaPago.'"';
            $filtro2.=' and p.idFormapago="'.$formaPago.'"';
        }

        $sel = "SELECT
    v.idVenta ID_VENTA, CONCAT(r.tipoComp,cf.folio) ID_FACTURA, v.fecha FECHA, s.nombre SUCURSAL, e.nombre EMPLEADO, c.nombre CLIENTE, v.monto TOTAL_VENTA, f.nombre FORMA_PAGO, v.estatus ESTATUS,
    pr.nombre PRODUCTO, vp.cantidad CANTIDAD_VENDIDA, (vp.preciounitario+vp.montodescuento) PRECIO_UNITARIO, vp.subtotal SUBTOTAL, vp.impuestosproductoventa IMPUESTOS,

    sum( if( i.nombre LIKE '%RETENIDO%', (vp.subtotal/100)*pi.porcentaje, 0 )  ) IMPUESTO_RETENIDO,
    sum( if( i.nombre NOT LIKE '%RETENIDO%', (vp.subtotal/100)*pi.porcentaje, 0 )  ) IMPUESTO_TRASLADADO,


    vp.montodescuento DESCUENTO, vp.total TOTAL, i.nombre TIPO_IMPUESTOo,
    (GROUP_CONCAT(DISTINCT i.nombre
                    ORDER BY i.nombre ASC
                    SEPARATOR '<br/>')) TIPO_IMPUESTO
FROM    app_pos_venta v
LEFT JOIN app_respuestaFacturacion r ON v.idVenta= r.idSale
LEFT JOIN cont_facturas cf ON r.folio = cf.uuid COLLATE utf8_general_ci
LEFT JOIN mrp_sucursal s ON v.idSucursal = s.idSuc
LEFT JOIN empleados e ON v.idEmpleado = e.idempleado
LEFT JOIN comun_cliente c ON v.idCliente = c.id
LEFT JOIN app_pos_venta_pagos p ON v.idVenta = p.idVenta
LEFT JOIN forma_pago f ON p.idFormapago = f.idFormapago

LEFT JOIN app_pos_venta_producto vp ON v.idVenta = vp.idVenta
LEFT JOIN app_productos pr ON vp.idProducto = pr.id
LEFT JOIN app_pos_venta_producto_impuesto pi ON vp.idventa_producto = pi.idVentaproducto
LEFT JOIN app_impuesto i ON pi.idImpuesto = i.id

WHERE  $filtro
GROUP BY v.idVenta, vp.idProducto";
        //$sel.= ' limit 10';

        $resGra = $this->queryArray($sel);

        ///monto total de ventas
        $selectVentas ="SELECT
        v.idVenta as folio,
        v.fecha as fecha,
        v.envio as envio,
        CASE WHEN c.nombre IS NOT NULL
        THEN c.nombre
        ELSE 'Publico general'
        END AS cliente,
        e.usuario as empleado,
        s.nombre as sucursal,
        CASE WHEN v.estatus =1
        THEN 'Activa'
        ELSE 'Cancelada'
        END AS estatus,
        v.montoimpuestos as iva,
        ROUND((vp.total),2) as monto
        from app_pos_venta v left join comun_cliente c on c.id=v.idCliente inner join  accelog_usuarios e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta where  v.estatus =1  and ".$filtro." order by folio desc" ;

        $resultVentas = $this->queryArray($selectVentas);


        $sel1 = 'SELECT p.nombre as label , sum(cantidad) as value, sum(vp.subtotal) as subtotal, sum(vp.impuestosproductoventa) as impuestos, ROUND(ROUND(sum(vp.total),2),2) as value2';
        $sel1.= ' from app_pos_venta_producto vp';
        $sel1.= ' INNER JOIN app_productos p ON p.id = vp.idProducto';
        $sel1.= ' INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta';
        $sel1.= ' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
        $sel1.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        $sel1.= ' where v.estatus=1 '.$filtro;
        $sel1.= ' group by idProducto';
        $sel1.= ' order by value desc';
        $sel1.= ' limit 10';
        //echo $sel1;
        $resGraD = $this->queryArray($sel1);

        //exit();
        $sel2 = 'SELECT v.fecha as y, ROUND(sum(ROUND(v.monto,2)),2) as a';
        $sel2.= ' from app_pos_venta v';
        //$sel2.= ' INNER JOIN app_pos_venta_producto vp on v.idVenta=vp.idVenta';
        $sel2.= ' where v.estatus=1 and '.$filtro2.' ';
        $sel2.= ' group by '.$orden.'(v.fecha)';
        //echo $sel2;
        //exit();
        $resGra2 = $this->queryArray($sel2);


        return array('productos' => $resGra['rows'], 'dona' => $resGraD['rows'], 'linea' => $resGra2['rows'], 'ventasTotal'=> $resultVentas['rows'] );
    }

    public function repCortesias($desde,$hasta,$sucursal,$orden){
        $inicio = $desde;
        $fin = $hasta;
        if($fin!="")
        {
            list($a,$m,$d)=explode("-",$fin);
            $fin=$a."-".$m."-".((int)$d+1);
        }

        if($inicio!="" && $fin=="")
        {
            $filtro.=" and fecha >= '".$inicio."' ";
            $filtro2.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro.=" and fecha <= '".$fin."' ";
            $filtro2.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
            $filtro2.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }
        // PARA MOSTRAR SOLO CORTESIAS
        $filtro .= " and vp.total = 0 ";
        $filtro2 .= " and vp.total = 0 ";

        if($sucursal!=0){
            $filtro.=' and v.idSucursal="'.$sucursal.'"';
            $filtro2.=' and v.idSucursal="'.$sucursal.'"';
        }  

        //NEW SQL PARA MOSTRAR LAS PROMOCIONES
        $sel = 'SELECT if(p.nombre is null ,vp.comentario,p.nombre) label,
            sum(cantidad) as value, 
            sum(vp.subtotal) as subtotal, 
            sum(vp.impuestosproductoventa) as impuestos, 
            sum(vp.total) as total, 
            s.nombre as sucursal 
            FROM app_pos_venta_producto vp 
            LEFT JOIN app_productos p ON p.id = vp.idProducto 
            INNER JOIN app_pos_venta v ON v.idVenta=vp.idVenta 
            INNER JOIN accelog_usuarios u ON u.idempleado = v.idEmpleado 
            INNER JOIN mrp_sucursal s ON s.idSuc=v.idSucursal 
            where v.estatus=1 '.$filtro.' AND vp.comentario <> "omitir"
            group by idProducto, vp.comentario
            order by total desc;';

        $resGra = $this->queryArray($sel);

        $sel1 = "SELECT p.nombre as label , sum(cantidad) as value, sum(vp.subtotal) as subtotal, sum(vp.impuestosproductoventa) as impuestos, ROUND(ROUND(sum(vp.total),2),2) as value2
                FROM app_pos_venta_producto vp
                INNER JOIN app_productos p ON p.id = vp.idProducto
                INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta
                INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado
                INNER JOIN mrp_sucursal s on s.idSuc=v.idSucursal
                WHERE v.estatus=1 ".$filtro."
                group by idProducto
                order by value desc
                limit 10";      
        $resGraD = $this->queryArray($sel1);


        $sel2 = 'SELECT v.fecha as y, ROUND(sum(ROUND(v.monto,2)),2) as a';
        $sel2.= ' from app_pos_venta v';
        $sel2.= ' where v.estatus=1 '.$filtro2.' ';
        $sel2.= ' group by '.$orden.'(v.fecha)';

        $resGra2 = $this->queryArray($sel2);


        return array('productos' => $resGra['rows'], 'dona' => $resGraD['rows'], 'linea' => $resGra2['rows'], 'ventasTotal'=> '' );
    }
    public function repProductos($desde,$hasta,$sucursal,$orden){
        $inicio = $desde;
        $fin = $hasta;
        //$filtro=1;
        //echo 'inicioi='.$inicio.' hasta='.$fin;
        if($fin!="")
        {
            list($a,$m,$d)=explode("-",$fin);
            $fin=$a."-".$m."-".((int)$d+1);
        }


        if($inicio!="" && $fin=="")
        {
            $filtro.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }


        if($inicio!="" && $fin=="")
        {
            $filtro2.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro2.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro2.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }

        if($sucursal!=0){
            $filtro.=' and v.idSucursal="'.$sucursal.'"';
            $filtro2.=' and v.idSucursal="'.$sucursal.'"';
        }

        // $sel = 'SELECT p.nombre as label , sum(cantidad) as value, sum(vp.subtotal) as subtotal, sum(vp.impuestosproductoventa) as impuestos, sum(vp.total) as total, s.nombre as sucursal';
        // $sel.= ' from app_pos_venta_producto vp';
        // $sel.= ' INNER JOIN app_productos p ON p.id = vp.idProducto';
        // $sel.= ' INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta';
        // $sel.= ' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
        // $sel.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        // $sel.= ' where v.estatus=1 '.$filtro;
        // $sel.= ' group by idProducto';
        // $sel.= ' order by total desc';
        //$sel.= ' limit 10';
        //echo $sel.'<br><br>';

        //NEW SQL PARA MOSTRAR LAS PROMOCIONES
        $sel = 'SELECT        
            if(p.nombre is null ,vp.comentario,p.nombre) label,
            sum(cantidad) as value, 
            sum(vp.subtotal) as subtotal, 
            sum(vp.impuestosproductoventa) as impuestos, 
            sum(vp.total) as total, 
            s.nombre as sucursal 
            FROM app_pos_venta_producto vp 
            LEFT JOIN app_productos p ON p.id = vp.idProducto 
            INNER JOIN app_pos_venta v ON v.idVenta=vp.idVenta 
            INNER JOIN accelog_usuarios u ON u.idempleado = v.idEmpleado 
            INNER JOIN mrp_sucursal s ON s.idSuc=v.idSucursal 
            where v.estatus=1 '.$filtro.' AND vp.comentario <> "omitir" 
            group by idProducto, vp.comentario
            order by total desc;';


        $resGra = $this->queryArray($sel);

        ///monto total de ventas
        $selectVentas ="SELECT
        v.idVenta as folio,
        v.fecha as fecha,
        v.envio as envio,
        CASE WHEN c.nombre IS NOT NULL
        THEN c.nombre
        ELSE 'Publico general'
        END AS cliente,
        e.usuario as empleado,
        s.nombre as sucursal,
        CASE WHEN v.estatus =1
        THEN 'Activa'
        ELSE 'Cancelada'
        END AS estatus,
        v.montoimpuestos as iva,
        ROUND((vp.total),2) as monto
        from app_pos_venta v left join comun_cliente c on c.id=v.idCliente inner join  accelog_usuarios e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta where  v.estatus =1  ".$filtro." order by folio desc" ;
        //echo $selectVentas.'<br><br>';
        //$resultVentas = $this->queryArray($selectVentas);
        $resultVentas='';

        $sel1 = 'SELECT p.nombre as label , sum(cantidad) as value, sum(vp.subtotal) as subtotal, sum(vp.impuestosproductoventa) as impuestos, ROUND(ROUND(sum(vp.total),2),2) as value2';
        $sel1.= ' from app_pos_venta_producto vp';
        $sel1.= ' INNER JOIN app_productos p ON p.id = vp.idProducto';
        $sel1.= ' INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta';
        $sel1.= ' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
        $sel1.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        $sel1.= ' where v.estatus=1 '.$filtro;
        $sel1.= ' group by idProducto';
        $sel1.= ' order by value desc';
        $sel1.= ' limit 10';
        //echo $sel1.'<br><br>';
        $resGraD = $this->queryArray($sel1);

        //exit();
        $sel2 = 'SELECT v.fecha as y, ROUND(sum(ROUND(v.monto,2)),2) as a';
        $sel2.= ' from app_pos_venta v';
        //$sel2.= ' INNER JOIN app_pos_venta_producto vp on v.idVenta=vp.idVenta';
        $sel2.= ' where v.estatus=1 '.$filtro2.' ';
        $sel2.= ' group by '.$orden.'(v.fecha)';
        //echo $sel2;
        //exit();
        $resGra2 = $this->queryArray($sel2);

        return array('productos' => $resGra['rows'], 'dona' => $resGraD['rows'], 'linea' => $resGra2['rows'], 'ventasTotal'=> '' );
    }


    public function repFormaDePago($desde,$hasta,$sucursal,$orden){
        $inicio = $desde;
        $fin = $hasta;
        //$filtro=1;
        //echo 'inicioi='.$inicio.' hasta='.$fin;
        if($fin!="")
        {
            list($a,$m,$d)=explode("-",$fin);
            $fin=$a."-".$m."-".((int)$d+1);
        }


        if($inicio!="" && $fin=="")
        {
            $filtro.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }


        if($inicio!="" && $fin=="")
        {
            $filtro2.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro2.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro2.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }

        if($sucursal!=0){
            $filtro.=' and v.idSucursal="'.$sucursal.'"';
            $filtro2.=' and v.idSucursal="'.$sucursal.'"';
        }

        $sel ='SELECT v.fecha,fp.nombre as label , IF(fp.claveSat = "01", ROUND(sum((p.monto - v.cambio)),2) , ROUND(sum(p.monto ),2)) as value, s.nombre as sucursal';
        $sel.=' from app_pos_venta_pagos as p';
        $sel.=' INNER JOIN app_pos_venta v on v.idVenta=p.idVenta';
        $sel.=' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
        $sel.=' inner join forma_pago fp on fp.idFormapago=p.idFormapago';
        $sel.=' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        $sel.=' where v.estatus=1'.$filtro;
        $sel.=' group by fp.nombre';
        $sel.=' order by value desc';
        //$sel.= ' limit 10';
        //echo $sel.'///';
        //exit();
        $resGra = $this->queryArray($sel);

       /* $sel1 = 'SELECT p.nombre as label , sum(cantidad) as value2, sum(vp.subtotal) as subtotal, sum(vp.impuestosproductoventa) as impuestos, ROUND(ROUND(sum(vp.total),2),2) as value';
        $sel1.= ' from app_pos_venta_producto vp';
        $sel1.= ' INNER JOIN app_productos p ON p.id = vp.idProducto';
        $sel1.= ' INNER JOIN app_pos_venta v on v.idVenta=vp.idVenta';
        $sel1.= ' INNER JOIN accelog_usuarios u on u.idempleado = v.idEmpleado';
        $sel1.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        $sel1.= ' where v.estatus=1 '.$filtro;
        $sel1.= ' group by idProducto';
        $sel1.= ' order by total desc';
        $sel1.= ' limit 10';
        //echo $sel1;
        $resGraD = $this->queryArray($sel1); */
                ///monto total de ventas
        $selectVentas ="SELECT
        v.idVenta as folio,
        v.fecha as fecha,
        v.envio as envio,
        CASE WHEN c.nombre IS NOT NULL
        THEN c.nombre
        ELSE 'Publico general'
        END AS cliente,
        e.usuario as empleado,
        s.nombre as sucursal,
        CASE WHEN v.estatus =1
        THEN 'Activa'
        ELSE 'Cancelada'
        END AS estatus,
        v.montoimpuestos as iva,
        ROUND((vp.total),2) as monto
        from app_pos_venta v left join comun_cliente c on c.id=v.idCliente inner join  accelog_usuarios e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta where  v.estatus =1  ".$filtro." order by folio desc" ;

        $resultVentas = $this->queryArray($selectVentas);

        //exit();
        $sel2 = 'SELECT v.fecha as y,fp.nombre  , sum(p.monto) as a, s.nombre as sucursal ';
        $sel2.= ' from app_pos_venta_pagos as p';
        $sel2.= ' INNER JOIN app_pos_venta v on v.idVenta=p.idVenta';
        $sel2.= ' INNER JOIN accelog_usuarios u on u.idempleado=v.idEmpleado';
        $sel2.= ' inner join forma_pago fp on fp.idFormapago=p.idFormapago';
        $sel2.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        $sel2.= ' where v.estatus=1 '.$filtro2;
       // $sel2.= ' group by fp.nombre';
        $sel2.= ' group by '.$orden.'(v.fecha)';

        //echo $sel2;
        //exit();
        /*$sel2 = 'SELECT v.fecha as y, ROUND(sum(ROUND(v.monto,2)),2) as a';
        $sel2.= ' from app_pos_venta v';
        //$sel2.= ' INNER JOIN app_pos_venta_producto vp on v.idVenta=vp.idVenta';
        $sel2.= ' where v.estatus=1 '.$filtro2.' ';
        $sel2.= ' group by '.$orden.'(v.fecha)'; */
        //echo $sel2;
        //exit();
        $resGra2 = $this->queryArray($sel2);


        return array('formasPago' => $resGra['rows'], 'dona' => $resGra['rows'], 'linea' => $resGra2['rows'], 'ventasTotal'=> $resultVentas['rows'] );
    }

    public function repEmpleadoVenta($desde,$hasta,$sucursal,$orden){
        $inicio = $desde;
        $fin = $hasta;
        //$filtro=1;
        //echo 'inicioi='.$inicio.' hasta='.$fin;
        if($fin!="")
        {
            list($a,$m,$d)=explode("-",$fin);
            $fin=$a."-".$m."-".((int)$d+1);
        }


        if($inicio!="" && $fin=="")
        {
            $filtro.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }


        if($inicio!="" && $fin=="")
        {
            $filtro2.=" and fecha >= '".$inicio."' ";
        }
        if($fin!="" && $inicio=="")
        {
            $filtro2.=" and fecha <= '".$fin."' ";
        }
        if($inicio!="" && $fin!="")
        {
            $filtro2.=" and fecha <= '".$fin."' and   fecha >= '".$inicio."' ";
        }

        if($sucursal!=0){
            $filtro.=' and v.idSucursal="'.$sucursal.'"';
            $filtro2.=' and v.idSucursal="'.$sucursal.'"';
        }

        $sel = 'SELECT u.usuario as label, round(sum(v.monto),2) as value, v.fecha as y, round(sum(v.monto),2) as a
                from app_pos_venta v
                left join accelog_usuarios u on u.idempleado=v.idEmpleado
                where v.estatus=1 '.$filtro.'
                 group by u.usuario;';
        $resGra = $this->queryArray($sel);

        $selectVentas ="SELECT
        v.idVenta as folio,
        v.fecha as fecha,
        v.envio as envio,
        CASE WHEN c.nombre IS NOT NULL
        THEN c.nombre
        ELSE 'Publico general'
        END AS cliente,
        e.usuario as empleado,
        s.nombre as sucursal,
        CASE WHEN v.estatus =1
        THEN 'Activa'
        ELSE 'Cancelada'
        END AS estatus,
        v.montoimpuestos as iva,
        ROUND((vp.total),2) as monto
        from app_pos_venta v left join comun_cliente c on c.id=v.idCliente inner join  accelog_usuarios e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta where  v.estatus =1  ".$filtro." order by folio desc" ;

        $resultVentas = $this->queryArray($selectVentas);

        //exit();
        /*$sel2 = 'SELECT v.fecha as y,fp.nombre  , sum(p.monto) as a, s.nombre as sucursal ';
        $sel2.= ' from app_pos_venta_pagos as p';
        $sel2.= ' INNER JOIN app_pos_venta v on v.idVenta=p.idVenta';
        $sel2.= ' INNER JOIN accelog_usuarios u on u.idempleado=v.idEmpleado';
        $sel2.= ' inner join forma_pago fp on fp.idFormapago=p.idFormapago';
        $sel2.= ' inner join mrp_sucursal s on s.idSuc=v.idSucursal';
        $sel2.= ' where v.estatus=1 '.$filtro2;
       // $sel2.= ' group by fp.nombre';
        $sel2.= ' group by '.$orden.'(v.fecha)';

        $resGra2 = $this->queryArray($sel2); */


        return array('empleadoVenta' => $resGra['rows'], 'dona' => $resGra['rows'], 'linea' => $resGra['rows'], 'ventasTotal'=> $resultVentas['rows'] );
    }

    private static function filtrarFecha($campo, $inicio, $fin) {
        $filtro = "";

        if(preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$inicio) &&
            !preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$fin))
        {
            $filtro.="$campo >= $inicio";
        }
        elseif(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$inicio) &&
            preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$fin))
        {
            $filtro.="$campo <= $fin";
        }
        elseif(preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$inicio) &&
            preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$fin))
        {
            $filtro.="$campo BETWEEN '$inicio' AND '$fin'";
        }
        elseif(!preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$inicio) &&
            !preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/",$fin))
        {
            $filtro.="0 = 0";
        }
        return $filtro;
    }

    private static function filtrarSucursal($campo, $nombre) {
        $filtro ="";

        if($nombre != 0) {
            $filtro = "$campo = '$nombre'";
        }
        else{
            $filtro = "0 = 0";
        }
        return $filtro;
    }

    public function repDepartamento($desde, $hasta, $sucursal, $orden){
        $fecha = ReporteModel::filtrarFecha("v.fecha", $desde, $hasta);
        $suc = ReporteModel::filtrarSucursal("v.idSucursal", $sucursal);

        $sql = "SELECT  IF(d.nombre  IS NOT NULL, d.nombre, 'Sin departamento') AS label,
                        round(SUM(vp.total),2) AS value
                FROM    app_pos_venta AS v
                LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta
                LEFT JOIN   app_productos AS p ON vp.idProducto = p.id
                LEFT JOIN   app_departamento AS d ON p.departamento = d.id
                WHERE   v.estatus = 1 AND
                        ( $suc ) AND
                        ( $fecha )
                GROUP BY    label
                ORDER BY    v.fecha";

        return $this->queryArray($sql);
    }

    public function repFamilia($desde, $hasta, $sucursal, $orden){
        $fecha = ReporteModel::filtrarFecha("v.fecha", $desde, $hasta);
        $suc = ReporteModel::filtrarSucursal("v.idSucursal", $sucursal);

        $sql = "SELECT  IF(f.nombre  IS NOT NULL, f.nombre, 'Sin familia') AS label,
                        round(SUM(vp.total),2) AS value
                FROM    app_pos_venta AS v
                LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta
                LEFT JOIN   app_productos AS p ON vp.idProducto = p.id
                LEFT JOIN   app_familia AS f ON p.departamento = f.id
                WHERE   v.estatus = 1 AND
                        ( $suc ) AND
                        ( $fecha )
                GROUP BY    label
                ORDER BY    v.fecha";

        return $this->queryArray($sql);
    }


    public function repSucursal($desde, $hasta, $sucursal, $orden){
        $fecha = ReporteModel::filtrarFecha("v.fecha", $desde, $hasta);
        $suc = ReporteModel::filtrarSucursal("v.idSucursal", $sucursal);

        $sql = "SELECT  IF(s.nombre  IS NOT NULL, s.nombre, 'Sin Sucursal') AS label,
                        round(SUM(vp.total),2) AS value
                FROM app_pos_venta AS v
                LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta
                LEFT JOIN   mrp_sucursal AS s ON v.idSucursal = s.idSuc
                WHERE   v.estatus = 1 AND
                        ( $suc ) AND
                        ( $fecha )
                GROUP BY  label
                ORDER BY    s.nombre;";
        return $this->queryArray($sql);
    }

    public function repLinea($desde, $hasta, $sucursal, $orden){
        $fecha = ReporteModel::filtrarFecha("v.fecha", $desde, $hasta);
        $suc = ReporteModel::filtrarSucursal("v.idSucursal", $sucursal);

        $sql = "SELECT  IF(l.nombre  IS NOT NULL, l.nombre, 'Sin linea') AS label,
                        round(SUM(vp.total),2) AS value
                FROM    app_pos_venta AS v
                LEFT JOIN   app_pos_venta_producto AS vp ON vp.idVenta = v.idVenta
                LEFT JOIN   app_productos AS p ON vp.idProducto = p.id
                LEFT JOIN   app_linea AS l ON p.departamento = l.id
                WHERE   v.estatus = 1 AND
                        ( $suc ) AND
                        ( $fecha )
                GROUP BY    label
                ORDER BY    v.fecha";

        return $this->queryArray($sql);
    }

///////////////// ******** ---- 			listar_ventas_cliente_producto			------ ************ //////////////////
//////// Lista las ventas del cliente por los productos
	// Como parametros puede recibir:
		// f_ini -> Fecha de inicio
		// f_fin -> Fecha final
		// sucursal -> ID de las sucursal
		// graficar -> 1 -> dia, 2 -> semana, 3 -> mes, 4 -> año

	function listar_ventas_cliente_producto($objeto) {
	// Filtra por la sucursal si existe
		$condicion .= (!empty($objeto['sucursal'])) ?
			' AND v.idSucursal = ' . $objeto['sucursal']: '';
	// Se filtra por fecha de inicio y fin si estas existen
		$condicion .= (!empty($objeto['f_ini']) && !empty($objeto['f_fin'])) ?
			' AND v.fecha BETWEEN \'' . $objeto['f_ini'] . ' 00:01:00\' AND \'' . $objeto['f_fin'] . ' 23:59:00\'' : '';
	// Agrupa la consulta por los parametros indicados si existe, si no la agrupa por id
		$condicion .= (!empty($objeto['agrupar'])) ? ' GROUP BY ' . $objeto['agrupar'] : ' GROUP BY c.id, pv.idProducto';

		$sql = "SELECT
					IF(c.nombre IS NOT NULL, c.nombre, 'Publico General') nombre, COUNT(v.idVenta) AS ventas, SUM(pv.cantidad) AS cantidad, p.nombre AS producto,
					SUM(pv.total) AS monto, v.fecha, s.nombre AS sucursal
				FROM
                    app_pos_venta v

                LEFT JOIN
                    comun_cliente c
                    ON
                        v.idCliente = c.id
				LEFT JOIN
						app_pos_venta_producto pv
					ON
						pv.idVenta = v.idVenta
				LEFT JOIN
						mrp_sucursal s
					ON
						s.idSuc = v.idSucursal
				LEFT JOIN
						app_productos p
					ON
						p.id = pv.idProducto
				WHERE
					1 = 1 and v.estatus = 1 ".
				$condicion."
				ORDER BY
					c.nombre, cantidad DESC, producto";
		// return $sql;
		$result = $this -> queryArray($sql);

		return $result;
	}

    public function formasDePago(){
        $query = "select * from view_forma_pago WHERE activo = 1 ORDER BY claveSat ASC ";
        $res = $this->queryArray($query);

        return array('formas' => $res['rows'] );
    }
    public function ventasIndex()
    {
        //$result2 = $this->touchProducts();

        
        $result2  = '';
        $query3 = "SELECT * from empleados";
        $result3 = $this->queryArray($query3);

        $query45 = "SELECT * from comun_cliente";
        $result5 = $this->queryArray($query45);

        //return $result['rows'];
        return array('productos' => $result2 ,  'usuarios' => $result3['rows'], 'clientes' => $result5['rows']);


    }

///////////////// ******** ---- 			FIN listar_ventas_cliente_producto		------ ************ //////////////////

} ///fin de la clase
?>
