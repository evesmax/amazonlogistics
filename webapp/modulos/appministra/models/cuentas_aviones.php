<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL
require("models/connection_sqli_manual.php"); // funciones mySQLi

class CuentasModel extends Connection
{
    public function listaProveedores()
    {
        $myQuery = "SELECT idPrv AS id, codigo,razon_social AS nombre FROM mrp_proveedor  WHERE diascredito >= 1  AND limite_credito >= 1 AND status = -1 ORDER BY idPrv";
        return $this->query($myQuery);
    }

    public function listaClientes()
    {
        $myQuery = "SELECT id,codigo,nombre FROM comun_cliente WHERE dias_credito >= 1 AND limite_credito >= 1 AND borrado = 0 ORDER BY id";
        return $this->query($myQuery);
    }


    public function datosSolicitud($idcli)
    {
            $res = $this->query("SELECT num_viaje, id FROM app_solicitud_viaje v WHERE v.id = $idcli");

        $res = $res->fetch_array();
        return $res;
    }


    public function listaClientesAntiguedad($vars)
    {
        $fecha_pos = $vars['f_cor'];

        if(intval($vars['id_moneda']))
        {
            $moneda = " AND vg.idMoneda = ".$vars['id_moneda'];
        }

        $where = '';
        if(intval($vars['filtros'])){
            $where .= " AND vg.fecha BETWEEN '".$vars['desde']." 00:00:00' AND '".$vars['hasta']." 23:59:59'";
            if($vars['orden'])
                $where .= " AND vg.idSolicitud = ".$vars['orden'];

            if($vars['matricula'])
                $where .= " AND idSolicitud IN (SELECT id FROM app_solicitud_viaje WHERE idaeronave = ". $vars['matricula'] .")";
        }

        $myQuery = "SELECT '' AS id_relacion, vg.id AS id_documento, v.id AS id_prov_cli,
                    CONCAT(c.nombre,'*/*',c.dias_credito,'*/*',c.rfc) AS info_cliente, vg.id AS Folio, CONCAT('Viaje: ',v.num_viaje) AS SacarConcepto, (SELECT aeronave FROM app_catalogo_aeronaves WHERE id = v.idaeronave) as avion, v.num_viaje as vuelo, v.fechaIda as fecha_vuelo,
                    vg.importe * IF(vg.tipoCambio=0,1,vg.tipoCambio) AS ImporteDoc,
                    vg.fecha AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 1 AS id_tipo

                    FROM app_solicitud_viaje_gastos vg
                    INNER JOIN app_solicitud_viaje v ON v.id = vg.idSolicitud
                    LEFT JOIN comun_cliente c ON c.id = v.idCliente
                    WHERE aprobado = 1 AND vg.activo = 1 $moneda $where
                    ORDER BY id_prov_cli, Folio, fecha_pago, id_relacion";


        return $this->query($myQuery);
    }

    function listaProveedoresAntiguedad($vars)
    {
        $where1 = "AND pa.fecha_pago <= '".$vars['f_cor']."' AND re.fecha_factura <= '".$vars['f_cor']."'";
        $where2 = "AND pa.fecha_pago <= '".$vars['f_cor']."' AND pa2.fecha_pago <= '".$vars['f_cor']."'";
        $where3 = "AND re.fecha_factura <= '".$vars['f_cor']."'";
        $where4 = "AND pa.fecha_pago <= '".$vars['f_cor']."'";
        $moneda = '';
        if(intval($vars['id_moneda']))
        {
            $moneda = " AND vg.idMoneda = ".$vars['id_moneda'];
        }

        $myQuery = "SELECT '' AS id_relacion, vg.id AS id_documento, v.idCliente AS id_prov_cli,
                    CONCAT(c.nombre,'*/*',c.dias_credito,'*/*',c.rfc) AS info_cliente, vg.id AS Folio, CONCAT('Viaje: ',v.num_viaje) AS SacarConcepto, (SELECT aeronave FROM app_catalogo_aeronaves WHERE id = v.idaeronave) as avion, v.num_viaje as vuelo, v.fechaIda as fecha_vuelo,
                    vg.importe * IF(vg.tipoCambio=0,1,vg.tipoCambio) AS ImporteDoc,
                    vg.fecha AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 1 AS id_tipo

                    FROM app_solicitud_viaje_gastos vg
                    INNER JOIN app_solicitud_viaje v ON v.id = vg.idSolicitud
                    LEFT JOIN comun_cliente c ON c.id = v.idCliente
                    WHERE v.aprobado = 1 AND  vg.activo = 1 $moneda
                    ORDER BY id_prov_cli, Folio, fecha_pago, id_relacion";


        return $this->query($myQuery);
    }

    function saldoInicialFactura($id_documento,$id_tipo,$f_ini,$cp)//Se hizo copia de esta funcion en cuentas.php
    {
        $myQuery = "SELECT IFNULL(SUM(pr.abono*pa.tipo_cambio),0) AS resultado FROM app_pagos_relacion pr INNER JOIN app_pagos pa ON pa.id = pr.id_pago WHERE pr.activo = 1 AND pa.cobrar_pagar = $cp AND pr.id_documento = $id_documento AND pr.id_tipo = $id_tipo";

        $saldo = $this->query($myQuery);
        $abonos = $saldo->fetch_assoc();
        return $abonos['resultado'];
    }

    public function conectado_bancos()
    {
        $res = $this->query("SELECT conectar_bancos FROM app_configuracion WHERE id = 1");
        $res = $res->fetch_assoc();
        return $res['conectar_bancos'];
    }

    public function listaGastos($idPrvCli,$cobrar_pagar)
    {

        $myQuery = "
                      SELECT
                              1 AS rq_tipo_cambio,
                              vg.id AS id_oc,
                              1 AS origen,
                              vg.idMoneda AS Moneda,
                              CONCAT(vg.id,' / ',vg.referencia) AS desc_concepto,
                              vg.id AS id,
                              vg.fecha AS fecha_factura,
                              0 AS no_factura,
                              vg.importe AS imp_factura,
                              vg.importe*IF(vg.tipoCambio=0,1,tipoCambio) AS importe_pesos,
                              vg.id AS xmlfile,
                              (SELECT dias_credito FROM comun_cliente WHERE id = v.idCliente AND borrado = 0) AS diascredito,
                              @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = vg.id AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                              @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND  rp.id_documento = vg.id AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                              (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, (SELECT CONCAT('(',clave,') ',nombre) FROM app_categoria_aeronaves WHERE id = vg.idcategoria) AS categoria, v.num_viaje AS vuelo, (SELECT aeronave FROM app_catalogo_aeronaves WHERE id = v.idaeronave) as aeronave,
                              cc.codigo AS codigoMoneda
                        FROM app_solicitud_viaje_gastos vg
                        INNER JOIN app_solicitud_viaje v ON v.id = vg.idSolicitud
                        LEFT JOIN cont_coin cc ON(cc.coin_id = vg.idMoneda)
                        WHERE vg.idSolicitud = $idPrvCli
                        ORDER BY id_oc;";



        $listaFacturas = $this->query($myQuery);
        return $listaFacturas;
    }

    //------------------------------
    public function pagosCobrosSinAsignar($id,$cp)
    {

        $myQuery = "SELECT p.*, (SELECT CONCAT('(',claveSat,') ',nombre) FROM forma_pago WHERE idFormapago = p.id_forma_pago) AS fp,
                    (SELECT codigo FROM cont_coin WHERE coin_id = p.id_moneda) AS Moneda,
                    @c := (SELECT SUM(cargo) FROM app_pagos_relacion WHERE activo = 1 AND id_pago = p.id),
                    @a := (SELECT SUM(abono) FROM app_pagos_relacion WHERE activo = 1 AND id_pago = p.id),
                    @r := (IFNULL(@c,0) - IFNULL(@a,0)),
                    (p.abono - p.cargo + IFNULL(@r,0)) AS saldo
                    FROM app_pagos p
                    WHERE p.id_prov_cli = $id AND cargo = 0 AND cobrar_pagar = $cp";
        return $this->query($myQuery);
    }

    public function listaFormasPago()
    {
        return $this->query("SELECT* FROM view_forma_pago WHERE claveSat < '99' ORDER BY claveSat");
    }

    public function guardarPagos($variables)
    {
        // tipo de cambio
        if(intval($variables['moneda']) == 1)
            $tipo_cambio = 1;
        else
            $tipo_cambio = $variables['tipo_cambio'];

        if(!intval($variables['tipo_pago']))
        {
            $cargo = "0";
            $abono = $variables['importe'];
        }
        else
        {
            $cargo = $variables['importe'];
            $abono = "0";
        }

        $myQuery = "INSERT INTO app_pagos(id, cobrar_pagar, id_prov_cli, cargo, abono, fecha_pago, concepto, id_forma_pago, numero_cheque, comprobante, id_moneda, tipo_cambio, cuenta_bancaria_origen, idbanco_origen_nac, idbanco_origen_ext, cuenta_bancaria_destino, idbanco_destino_nac, idbanco_destino_ext)
                    VALUES(0, ".$variables['cobrar_pagar'].", ".$variables['idPrvCli'].", $cargo, $abono, '".$variables['fecha']."', '".$variables['concepto']."', ".$variables['forma_pago'].",'".$variables['numero_cheque']."','".$variables['comprobante']."',".$variables['moneda'].",$tipo_cambio,'".$variables['cuenta_bancaria_origen']."','".$variables['banco_origen_nac']."','".$variables['banco_origen_ext']."','".$variables['cuenta_bancaria_destino']."','".$variables['banco_destino_nac']."','".$variables['banco_destino_ext']."');";
        return $this->insert_id($myQuery);
    }

    function listaFolios($idPrv)
    {
        $myQuery = "SELECT r.no_factura FROM app_recepcion r WHERE ";
        return $this->query($myQuery);
    }

    public function listaCargos($idPrvCli,$cobrar_pagar)
    {
        if(!intval($cobrar_pagar))
            $cliProv = "(SELECT dias_credito FROM comun_cliente WHERE id = p.id_prov_cli) AS diascredito, ";
        else
            $cliProv = "(SELECT diascredito FROM mrp_proveedor WHERE idPrv = p.id_prov_cli AND status = -1) AS diascredito, ";

        $myQuery = "SELECT p.id, p.tipo_cambio, (SELECT codigo FROM cont_coin WHERE coin_id = p.id_moneda) AS moneda, p.tipo_cambio, p.fecha_pago, @c := p.cargo*p.tipo_cambio, p.cargo,
        $cliProv
        p.concepto, @p := IFNULL((SELECT SUM(pr.abono) FROM app_pagos_relacion pr WHERE pr.activo = 1 AND pr.id_tipo = 0 AND pr.id_documento = p.id  AND (SELECT id_moneda FROM app_pagos WHERE id = pr.id_pago) = 1),0) AS pagos,
                    @p2 := IFNULL((SELECT SUM(pr.abono*(SELECT tipo_cambio FROM app_pagos WHERE id = pr.id_pago)) FROM app_pagos_relacion pr WHERE pr.activo = 1 AND pr.id_tipo = 0 AND pr.id_documento = p.id AND (SELECT id_moneda FROM app_pagos WHERE id = pr.id_pago) != 1),0) AS pagos2,
                    (@c-(@p+@p2)) AS saldo
                    FROM app_pagos p
                    WHERE p.id_prov_cli = $idPrvCli
                    AND p.cobrar_pagar = $cobrar_pagar
                    AND p.cargo > 0
                    ";
        return $this->query($myQuery);

    }

    function pagos_detalle($id,$t)
    {

        //Si es un cargo
        if($t == 'c')
            $tipo = '0';
        if($t == 'g')
            $tipo = '1';

        $myQuery = "SELECT pr.id_pago, pr.id AS id_rel, p.fecha_pago, (pr.abono*p.tipo_cambio) AS abono, (SELECT CONCAT('(',claveSat,') ',nombre) FROM forma_pago WHERE idFormapago = p.id_forma_pago) AS forma_pago, origen, activo, id_poliza_mov, p.concepto, IF(pr.usuario = 0,'<i style=\'font-size:12px;\'>No hay dato</i>',(SELECT usuario FROM accelog_usuarios WHERE idempleado = pr.usuario)) AS usuario
                        FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_pago
                        WHERE pr.id_documento = $id AND pr.id_tipo = $tipo ORDER BY pr.id";

        return $this->query($myQuery);
    }

    function info_car_fac($id,$t,$cp)
    {
        if($t == 'c')
        {
            if(!intval($cp))
                $provcli = "(SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = id_prov_cli) AS provcli";
            else
                $provcli = "(SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = id_prov_cli AND status = -1) AS provcli";
            $myQuery = "SELECT id_prov_cli, $provcli, (cargo*tipo_cambio) AS cargo, concepto, tipo_cambio,fecha_pago FROM app_pagos WHERE id = $id";
        }
        if($t == 'g')
        {
            if(!intval($cp))
            {
                if(intval($pos) == 1)
                {
                    /*VENTAS POS*/
                    $myQuery = "SELECT v.idCliente AS id_prov_cli,
                            (SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = v.idCliente) AS provcli,
                            SUM((vp.monto-v.cambio) - IFNULL((SELECT SUM(monto) FROM app_notas_credito WHERE idfactura = rf.id),0)) AS cargo,
                            CONCAT(f.folio,' / ',f.serie,' / ',f.uuid,' Venta POS: ',v.idVenta) AS concepto, IF(v.tipo_cambio = 0,1,v.tipo_cambio) AS tipo_cambio, rf.fecha AS fecha_pago
                            FROM app_respuestaFacturacion rf
                            INNER JOIN cont_facturas f ON uuid LIKE CONCAT('%',rf.folio,'%') COLLATE utf8_general_ci
                            INNER JOIN app_pos_venta_pagos vp ON vp.idVenta = rf.idSale
                            INNER JOIN app_pos_venta v ON v.idVenta = vp.idVenta
                            WHERE rf.id = $id AND vp.idFormapago = 6";
                }
            }
            else
            {

                $myQuery = "SELECT v.id AS id_prov_cli,
                            v.num_viaje AS provcli,
                            SUM(vg.importe*IF(vg.tipoCambio=0,1,vg.tipoCambio)) AS cargo,
                            CONCAT(vg.id,' / ',vg.referencia) AS concepto, 1 AS tipo_cambio, vg.fecha AS fecha_pago, (SELECT CONCAT('(',clave,') ',nombre) FROM app_categoria_aeronaves WHERE id = vg.idcategoria) AS categoria
                            FROM app_solicitud_viaje_gastos vg
                            LEFT JOIN app_solicitud_viaje v ON v.id = vg.idSolicitud AND aprobado = 1
                            WHERE vg.id = $id";
            }
        }

        $datos = $this->query($myQuery);
        $datos = $datos->fetch_assoc();
        return $datos;
    }

    function guardar_relacion($idpago,$idrelaciones,$tipo,$valores,$monedas,$monedaPago,$origs)
    {
        $fecha = $this->query("SELECT fecha_pago FROM app_pagos WHERE id = $idpago");
        $fecha = $fecha->fetch_object();

        $myQuery = '';
        $idrelaciones = explode("@|@",$idrelaciones);
        $valores = explode("@|@",$valores);
        $monedas = explode("@|@",$monedas);
        $origs = explode("@|@",$origs);
        $contBancos = 0;



        $limite = count($idrelaciones)-2;
        for($i=0;$i<=$limite;$i++)
        {
            if($monedaPago != 'MXN')
                $valores[$i] = floatval($valores[$i]) / floatval($this->tipo_cambio_pago($idpago));

            $myQuery = "INSERT INTO app_pagos_relacion(id,id_pago,id_tipo,id_documento,cargo,abono,usuario) VALUES(0,$idpago,".$origs[$i].",".$idrelaciones[$i].",0,".$valores[$i].",2); ";
            $id_pago_rel = $this->insert_id($myQuery);



        }

        return $id_pago_rel;
    }

    function getUUID($xml)
        {
            if(strpos($xml, '_') === false){
                $xml = str_replace('.xml', '', $xml);
            }
            else{
                $xml = explode('_', $xml);
                if(isset($xml[2]))
                    $xml = str_replace('.xml', '', $xml[2]);
                elseif(isset($xml[1]))
                    $xml = str_replace('.xml', '', $xml[1]);
                else
                    $xml = '';
            }
            return $xml;
        }

    function esBancos($idcuenta)
    {
        $myQuery = "SELECT account_id
                    FROM cont_accounts
                    WHERE account_id = $idcuenta
                    AND account_code LIKE CONCAT((SELECT account_code
                                                    FROM cont_accounts
                                                    WHERE account_id = (SELECT CuentaBancos
                                                                        FROM cont_config
                                                                        WHERE id = 1)),'%')
                    ";
        if($es = $this->query($myQuery))
        {
            $es = $es->fetch_assoc();
            $existe = intval($es['account_id']);
        }
        else
            $existe = 0;

        return $existe;
    }

    function saldoGral($vars)
    {
        $conexion_bancos = $this->conectado_bancos();
        if(intval($vars['tipo']))
        {
            $myQuery = "SELECT (IFNULL((SELECT
            SUM(IFNULL(r.imp_factura*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio),0) ) AS saldo
            FROM app_recepcion_xml r INNER JOIN app_ocompra c ON c.id = r.id_oc
            INNER JOIN app_requisiciones rq ON rq.id = c.id_requisicion
            WHERE c.id_proveedor = ".$vars['idPrvCli']." AND xmlfile != ''),0) -
            (IFNULL((SELECT
            SUM(IFNULL((SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = c.id AND rp.id_tipo=1 AND p.cobrar_pagar = ".$vars['tipo']."),0)) AS saldo
            FROM app_ocompra c
            WHERE c.id_proveedor = ".$vars['idPrvCli']." ),0))
            )+
            ";
        }
        else
        {
            $where = "AND vp.idFormapago = 6";
            if(intval($conexion_bancos))
            {
                $where = "AND rf.id_poliza_mov != '0'";
            }
            if(intval($vars['idPrvCli']))
            {
                $myQuery = "SELECT IFNULL((SELECT
                            SUM(IFNULL(e.total*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio),0) - IFNULL((SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = ".$vars['tipo']."),0)) AS saldo
                            FROM app_respuestaFacturacion rf
                            INNER JOIN  app_envios e ON e.id = rf.idSale AND e.forma_pago = 6
                            INNER JOIN app_oventa v ON v.id = e.id_oventa
                            INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion
                            WHERE v.id_cliente = ".$vars['idPrvCli']." AND rf.tipoComp = 'F' AND rf.xmlfile != '' AND rf.origen=1 AND rf.borrado != 1),0) + ";
                $myQuery .= "IFNULL((SELECT
                                SUM(IFNULL(pf.monto*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio),0)) AS saldo
                                FROM app_pendienteFactura pf
                                INNER JOIN app_respuestaFacturacion rf ON pf.id_respFact = rf.id
                                INNER JOIN  app_envios e ON e.id = pf.id_sale AND e.forma_pago = 6
                                INNER JOIN app_oventa v ON v.id = e.id_oventa
                                INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion
                                WHERE v.id_cliente = ".$vars['idPrvCli']."
                                AND rf.idFact != 0
                                AND pf.id_respFact != 0
                                AND rf.idSale = 0
                                AND rf.borrado != 1
                                AND rf.origen = 1),0) -  ";

                $myQuery .= "IFNULL((SELECT SUM(pr.abono*pa.tipo_cambio)
                            FROM app_pagos_relacion pr
                            INNER JOIN app_pagos pa ON pa.id = pr.id_pago
                            LEFT JOIN app_respuestaFacturacion rf ON rf.id = pr.id_documento
                            WHERE pr.activo = 1 AND pa.id_prov_cli = ".$vars['idPrvCli']." AND pa.cobrar_pagar = 0
                            AND rf.idFact != 0
                            AND rf.idSale = 0
                            AND rf.borrado != 1
                            AND rf.origen = 1),0) +";

                $myQuery .= "IFNULL((SELECT
                            SUM(IFNULL((vp.monto-v.cambio),0) - IFNULL((SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = ".$vars['tipo']."),0)) AS saldo
                            FROM app_respuestaFacturacion rf
                            INNER JOIN app_pos_venta v ON v.idVenta = rf.idSale
                            INNER JOIN app_pos_venta_pagos vp ON vp.idVenta = v.idVenta $where
                            WHERE rf.origen = 2 AND rf.borrado != 1
                            AND v.idCliente = ".$vars['idPrvCli']." AND rf.tipoComp = 'F'),0) +  ";
            }
            else
            {
                 $myQuery = "SELECT IFNULL((SELECT
                                SUM(IFNULL(e.total*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio),0)) AS saldo
                                FROM app_pendienteFactura pf
                                INNER JOIN app_respuestaFacturacion rf ON pf.id_respFact = rf.id
                                INNER JOIN  app_envios e ON e.id = pf.id_sale AND e.forma_pago = 6
                                INNER JOIN app_oventa v ON v.id = e.id_oventa
                                INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion
                                WHERE rf.idFact = 0
                                AND pf.id_respFact != 0
                                AND rf.borrado != 1
                                AND rf.idSale = 0),0) -

                                IFNULL((SELECT
                                    SUM(IFNULL((SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = rf.id AND rp.id_tipo=1 AND p.cobrar_pagar = 0),0)) AS saldo
                                FROM app_respuestaFacturacion rf
                                WHERE rf.idFact = 0 AND rf.borrado != 1
                                AND rf.idSale = 0),0) + ";
            }

        }

        $myQuery .= "
        (SELECT
        (IFNULL(SUM(p.cargo*p.tipo_cambio),0)-IFNULL(SUM(IFNULL((SELECT SUM(pr.abono*pa.tipo_cambio) FROM app_pagos_relacion pr INNER JOIN app_pagos pa ON pa.id = pr.id_pago WHERE pr.activo = 1 AND pr.id_tipo = 0 AND pr.id_documento = p.id AND pa.cobrar_pagar = ".$vars['tipo']."),0)),0)) AS saldo
        FROM app_pagos p
        WHERE p.id_prov_cli = ".$vars['idPrvCli']."
        AND p.cobrar_pagar = ".$vars['tipo']."
        AND p.cargo > 0) AS saldoGral
                         ";
        $info = $this->query($myQuery);
        $info = $info->fetch_assoc();
        return $info['saldoGral'];
    }
    function tipo_cambio($fecha)
    {
        $myQuery = "SELECT tipo_cambio FROM cont_tipo_cambio WHERE fecha = '$fecha'";
        $info = $this->query($myQuery);
        $info = $info->fetch_assoc();
        return $info['tipo_cambio'];
    }

    function listaMonedas()
    {
        return $this->query("SELECT* FROM cont_coin ORDER BY coin_id");
    }

    function tipo_cambio_pago($id)
    {
        $myQuery = "SELECT tipo_cambio FROM app_pagos WHERE id = $id";
        $info = $this->query($myQuery);
        $info = $info->fetch_assoc();
        return $info['tipo_cambio'];
    }

    function hayMovs($t)
    {
        $res = $this->query("SELECT id FROM app_pagos WHERE cobrar_pagar = $t");
        return $res->num_rows;
    }

    public function guardarLay($dato,$tipo)
    {
        $tipo_cambio = 1;
        if(intval($dato[6]) == 2)
        {
            $tipo_cambio = $this->query("SELECT tipo_cambio FROM cont_tipo_cambio WHERE fecha = '".$dato[3]."'");
            $tipo_cambio = $tipo_cambio->fetch_assoc();
            $tipo_cambio = $tipo_cambio['tipo_cambio'];
        }

        $this->query("INSERT INTO app_pagos VALUES(0,$tipo,".$dato[1].",".$dato[2].",0,'".$dato[3]."','Saldo Inicial(".$dato[4].") ".$dato[5]."',1,".$dato[6].",$tipo_cambio,4,'','','');");
    }

    public function borrar($tipo)
    {
        $this->query("DELETE FROM app_pagos WHERE origen = 4 AND cobrar_pagar = $tipo");
    }

    public function cuentas_sis_anterior($vars)
    {
        include('../../netwarelog/webconfig.php');
        $bd2 = '';
        $login = 0;
        $coneccion2 = mysqli_connect("34.66.63.218","nmdevel","nmdevel","netwarstore");
        if($bd2 = $coneccion2->query("SELECT nombre_db FROM customer WHERE instancia = '".$vars['inst']."'"))
        {
            $bd2 = $bd2->fetch_assoc();
            $bd2 = $bd2['nombre_db'];
            $bd_sin = explode('mlog',$bd2);
            $salt = "$2a$07$".$bd_sin[1]."mlogaaaaaaa.";
            $contra = crypt($vars['contra'],$salt);

            if($login = $coneccion2->query("SELECT idempleado FROM $bd2.accelog_usuarios WHERE usuario = '".$vars['usu']."'"))
            {
                $login = $login->fetch_assoc();
                $login = $login['idempleado'];
            }
        }


        if($bd2 != '' && intval($login))
        {
            $myQuery = "INSERT INTO $bd.app_pagos(cobrar_pagar, id_prov_cli, cargo, abono, fecha_pago, concepto, id_forma_pago, id_moneda, tipo_cambio, origen) ";
            if(!intval($vars['t']))
            {
                $myQuery .= "SELECT 0, idCliente, saldoactual, 0, fechacargo, CONCAT(concepto,' (VER ANT) ID VENTA: ',idVenta) AS concepto, 1, 1, 1, 9
                             FROM $bd2.cxc
                             WHERE
                             estatus = 0 AND saldoactual > 0 AND idCliente > 0;";
                //Clientes
                $myQuery2 = "CREATE TABLE $bd.comun_cliente_res LIKE $bd.comun_cliente;
                             INSERT INTO $bd.comun_cliente_res SELECT* FROM $bd.comun_cliente;
                             DELETE FROM $bd.comun_cliente;
                             ALTER TABLE $bd.comun_cliente AUTO_INCREMENT=1;
                             INSERT INTO $bd.comun_cliente SELECT* FROM $bd2.comun_cliente;";
            }
            else
            {
                $myQuery .= "SELECT 1, idProveedor, saldoactual, 0, fechacargo, CONCAT(concepto,' (VER ANT)') AS concepto, 1, 1, 1, 9
                            FROM $bd2.cxp
                            WHERE
                            estatus = 0 AND saldoactual > 0 AND idProveedor > 0;";
                //Proveedores
                $myQuery2 = "CREATE TABLE $bd.mrp_proveedor_res LIKE $bd.mrp_proveedor;
                             INSERT INTO $bd.mrp_proveedor_res SELECT* FROM $bd.mrp_proveedor;
                             DELETE FROM $bd.mrp_proveedor;
                             ALTER TABLE $bd.mrp_proveedor AUTO_INCREMENT=1;
                             INSERT INTO $bd.mrp_proveedor SELECT* FROM $bd2.mrp_proveedor;";
            }

            if($coneccion2->query($myQuery))
            {
                $coneccion2->multi_query($myQuery2);
                $return = 0;//0 No hubo problemas
            }
            else
                $return = 2;// 2 Hubo un problema en la consulta
        }
        else
            $return = 1;// 1 El usuario y/o contraseña son invalidos o no existe la instancia

        $coneccion2->close();
        return $return;
    }

    public function organizacion()
    {
        $datos = $this->query("SELECT o.*, (SELECT municipio FROM municipios WHERE idmunicipio = o.idmunicipio) AS municipio, (SELECT estado FROM estados WHERE idestado = o.idestado) AS estado FROM organizaciones o WHERE o.idorganizacion = 1");
        $datos = $datos->fetch_assoc();
        return $datos;
    }

    public function info_pago($id,$cp)
    {
        if(intval($cp))
            $cli_prov = "SELECT razon_social FROM mrp_proveedor WHERE idPrv = id_prov_cli";
        else
            $cli_prov = "SELECT nombre FROM comun_cliente WHERE id = id_prov_cli";

        $datos = $this->query("SELECT id, ($cli_prov) AS cli_prov, fecha_pago, concepto, id_moneda, tipo_cambio, cobrar_pagar FROM app_pagos WHERE id = $id");
        $datos = $datos->fetch_assoc();
        return $datos;
    }

    public function info_pago2($idpago)
    {

        $datos = $this->query("SELECT p.fecha_pago,
                            (SELECT claveSat FROM forma_pago WHERE idFormapago = p.id_forma_pago) AS forma_pago,
                            (SELECT codigo FROM cont_coin WHERE coin_id = p.id_moneda) AS moneda, p.abono,
                            /*INFO DE PAGOS*/
                            IFNULL((SELECT rfc FROM cont_bancos WHERE idbanco = p.idbanco_origen_nac),'') AS RfcEmisorCtaOrd,
                            IFNULL((SELECT nombre FROM cont_bancos WHERE idbanco = p.idbanco_origen_ext),'') AS NomBancoOrdExt,
                            p.cuenta_bancaria_origen AS CtaOrdenante,
                            IFNULL((SELECT rfc FROM cont_bancos WHERE idbanco = p.idbanco_destino_nac),'') AS RfcEmisorCtaBen,
                            p.cuenta_bancaria_destino AS CtaBeneficiario

                            FROM app_pagos p WHERE p.id = $idpago");
        $datos = $datos->fetch_assoc();
        return $datos;
    }

    public function usuario($user)
    {
        $sql = $this->query("select usuario from accelog_usuarios where idempleado=".$user);
        $nombre =  $sql->fetch_assoc();
        return $nombre['usuario'];
    }

    public function concepto_docu($idrelacion,$tipo,$cp,$ori)
    {
        if(intval($tipo))
        {
            if(intval($cp))
                $myQuery = "SELECT concepto FROM app_recepcion_xml WHERE id_oc = $idrelacion LIMIT 1";
            else
            {
                if(intval($ori) == 1)
                {
                    $myQuery = "SELECT
                            CASE
                            WHEN rf.idSale = 0 THEN 'Factura Multiple'
                            ELSE e.desc_concepto END AS concepto
                            FROM app_respuestaFacturacion rf
                            LEFT JOIN app_envios e ON e.id = rf.idSale
                            WHERE rf.id = $idrelacion";
                }

                if(intval($ori) == 2 || intval($ori) == 999)
                {
                    if(intval($ori) == 999)
                        $ori = $this->ori($idrelacion);
                    $myQuery = "SELECT
                            CASE
                            WHEN rf.idSale = 0 THEN 'Factura Multiple'
                            ELSE CONCAT('VENTA POS: ',v.idVenta) END AS concepto
                            FROM app_respuestaFacturacion rf
                            LEFT JOIN app_pos_venta v ON v.idVenta = rf.idSale
                            WHERE rf.id = $idrelacion";
                }

                if(intval($ori) == 4)
                {
                    $myQuery = "SELECT
                                CONCAT('Est. Xtructur: ',c.id) AS concepto
                                FROM app_respuestaFacturacion rf
                                LEFT JOIN constru_estimaciones_bit_cliente c ON c.id = rf.idSale
                                WHERE rf.id = $idrelacion";
                }

            }

        }
        else
            $myQuery = "SELECT concepto FROM app_pagos WHERE id = $idrelacion";

            $dato = $this->query($myQuery);
            $dato = $dato->fetch_assoc();
            return $dato['concepto'];
    }

    public function ori($idrelacion)
    {
        $res = $this->query("SELECT origen FROM app_respuestaFacturacion WHERE id = $idrelacion;");
        $res = $res->fetch_assoc();
        return $res['origen'];
    }

    public function info_rel($idrel,$cp)
    {
        if(intval($cp))
            $case = "SELECT rq.id_moneda FROM app_recepcion_xml x INNER JOIN app_ocompra c ON c.id = x.id_oc INNER JOIN app_requisiciones rq ON rq.id = c.id_requisicion WHERE x.id = r.id_documento";
        else
            $case = "SELECT rq.id_moneda FROM app_respuestaFacturacion rf INNER JOIN app_envios e ON e.id = rf.idSale INNER JOIN app_oventa v ON v.id = e.id_oventa INNER JOIN app_requisiciones_venta rq ON rq.id = v.id_requisicion WHERE rf.id = r.id_documento";

        $myQuery = "SELECT r.abono, p.tipo_cambio, p.id_moneda,
                    (CASE r.id_tipo
                    WHEN 0 THEN (SELECT id_moneda FROM app_pagos WHERE id = r.id_documento)
                    WHEN 1 THEN ($case)
                    END) AS monedaPago, r.id_documento
                    FROM app_pagos_relacion r
                    INNER JOIN app_pagos p ON p.id = r.id_pago
                    WHERE r.id = $idrel";
        $res = $this->query($myQuery);
        $res = $res->fetch_assoc();
        return $res;
    }

    public function info_rel2($idrel)
    {
        $myQuery = "SELECT f.uuid, f.xml, f.serie, f.folio, f.moneda, f.json, f.version, f.importe,
                    IFNULL((SELECT CONCAT(parcialidades+1,'*|*',imp_saldo_insoluto) FROM cont_facturas_relacion WHERE uuid_relacionado LIKE CONCAT('%',f.uuid,'%') ORDER BY parcialidades DESC LIMIT 1),0) AS info_ult_rel
                    FROM cont_facturas f
                    WHERE f.uuid = (SELECT folio FROM app_respuestaFacturacion WHERE id = $idrel) COLLATE utf8_general_ci;";
        $res = $this->query($myQuery);
        $res = $res->fetch_assoc();
        return $res;
    }

    public function info_rel3($idrel)
    {
        $myQuery = "SELECT f.uuid, f.xml FROM cont_facturas f WHERE f.id = $idrel;";
        $res = $this->query($myQuery);
        $res = $res->fetch_assoc();
        return $res;
    }

    public function cancelar_pagos_det($valores)
    {
        $ids = '';
        for($i=0;$i<=count($valores)-1;$i++)
        {
            $myQuery = "UPDATE app_pagos_relacion SET activo = 0 WHERE id = $valores[$i];";
            $this->query($myQuery);
            $movs = $this->query("SELECT id_poliza_mov FROM app_pagos_relacion WHERE id = $valores[$i];");
            while($m = $movs->fetch_object())
            {
                if($m->id_poliza_mov != '')
                {
                    $idmov = $m->id_poliza_mov;
                    $idmov = explode(',', $idmov);
                    $IdPoliza = $this->query("SELECT IdPoliza FROM cont_movimientos WHERE Id = $idmov[0]");
                    $IdPoliza = $IdPoliza->fetch_assoc();
                    $IdPoliza = $IdPoliza['IdPoliza'];
                    $this->query("UPDATE cont_polizas SET activo = 0 WHERE id = $IdPoliza");
                }
            }
        }
    }

    public function buscaPoliza($idmov)
    {
        $res = $this->query("SELECT IdPoliza FROM cont_movimientos WHERE Id = $idmov");
        $res = $res->fetch_assoc();
        return $res['IdPoliza'];
    }

    public function respaldocxc()
    {
        $fecha = date('Y_m_d_H_i_s',strtotime('-7 hour',strtotime(date('Y-m-d H:i:s'))));

        $this->query("CREATE TABLE app_pagos_$fecha LIKE app_pagos;");
        $this->query("INSERT INTO app_pagos_$fecha SELECT* FROM app_pagos;");
        $this->query("CREATE TABLE app_pagos_relacion_$fecha LIKE app_pagos_relacion;");
        $this->query("INSERT INTO app_pagos_relacion_$fecha SELECT* FROM app_pagos_relacion;");
        $this->query("DELETE FROM app_pagos WHERE cobrar_pagar = 1 AND concepto LIKE '%cargo por factura%' AND id NOT IN (SELECT id_documento FROM app_pagos_relacion WHERE id_tipo = 0);");
        return 1;
    }

    public function buscacargosfacturas()
    {
        $myQuery = "SELECT pr.id AS id_rel, p.concepto FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_documento WHERE p.cobrar_pagar = 1 AND pr.id_tipo = 0 AND pr.activo = 1 AND concepto LIKE 'cargo por factura%'";

        return $this->query($myQuery);
    }
    public function id_fac($uuid)
    {
        $myQuery = "SELECT id FROM cont_facturas WHERE uuid LIKE CONCAT('%','$uuid','%')";
        $res = $this->query($myQuery);
        $res = $res->fetch_assoc();
        return $res['id'];
    }

    public function actualiza_relacion($id_rel,$id_fac)
    {
        $this->query("UPDATE app_pagos_relacion SET id_tipo = 5, id_documento = $id_fac WHERE id = $id_rel;");
    }

    public function elimina_cargos_factura($uuid)
    {
        $this->query("DELETE FROM app_pagos WHERE cobrar_pagar = 1 AND concepto LIKE '%cargo por factura $uuid%';");
    }

    public function tieneRelacion($id)
    {
        $res = $this->query("SELECT IFNULL(SUM(activo),0) as Activos FROM app_pagos_relacion WHERE id_pago = $id AND activo = 1                   ");
        $res = $res->fetch_assoc();
        return intval($res['Activos']);

    }

    public function eliminar_pago($idpago,$cp)
    {
        return $this->query("DELETE FROM app_pagos WHERE id = $idpago AND cobrar_pagar = $cp");
    }

    function object_to_array($data) {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        }
        return $data;
    }

    function listaBancos()
    {
        return $this->query("SELECT* FROM cont_bancos");
    }

    function banco_proveedor_cliente($vars)
    {
        $idcliprov = $vars['idcliprov'];
        if(intval($vars['cp'])){//CXP
            return $this->query("SELECT '' AS banco_origen_nac, '' AS cuenta_bancaria_origen, '' AS banco_origen_ext,idbanco AS banco_destino_nac, numCT AS cuenta_bancaria_destino, '' AS banco_destino_ext FROM cont_bancosPrv WHERE idPrv = $idcliprov LIMIT 1");
        }
        else{
            return $this->query("SELECT idBanco AS banco_origen_nac, numero_cuenta_banco AS cuenta_bancaria_origen, idBancoInternacional AS banco_origen_ext, '' AS banco_destino_nac, '' AS cuenta_bancaria_destino, '' AS banco_destino_ext FROM comun_cliente WHERE id = $idcliprov ;");
        }

    }

    function matriculas()
    {
        return $this->query("SELECT id,aeronave FROM app_catalogo_aeronaves");
    }

    function ordenes()
    {
        return $this->query("SELECT id,num_viaje FROM app_solicitud_viaje WHERE aprobado = 1");
    }
}
?>
