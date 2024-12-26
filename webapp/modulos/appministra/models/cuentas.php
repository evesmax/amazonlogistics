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


    public function datosClienteProv($tipo,$idcli,$d)
    {
        if(intval($d))
        {
            $datos1 =  'nombre';
            $datos2 = 'razon_social';
        }
        else
        {
            $datos1 =  'c.*, (SELECT municipio FROM municipios WHERE idmunicipio = c.idMunicipio) AS municipio, (SELECT estado FROM estados WHERE idestado = c.idEstado) AS estado';
            $datos2 =  'p.*, (SELECT municipio FROM municipios WHERE idmunicipio = p.idmunicipio) AS municipio, (SELECT estado FROM estados WHERE idestado = p.idestado) AS estado';

        }
        if(!$tipo)
            $res = $this->query("SELECT $datos1 FROM comun_cliente c WHERE c.id = $idcli");
        else
            $res = $this->query("SELECT $datos2 FROM mrp_proveedor p WHERE p.idPrv = $idcli");
        $res = $res->fetch_array();
        return $res;
    }


    public function listaClientesAntiguedad($vars)
    {
        $fecha_pos = $vars['f_cor'];

        if(intval($vars['id_moneda']))
        {
            $m = $vars['id_moneda'];
            if(intval($m) == 1)
                $m = 0;
            $moneda_pos = "AND v.moneda = $m";
            $moneda = "AND p.id_moneda = ".$vars['id_moneda'];
        }

        $myQuery = "SELECT '' AS id_relacion, rf.id AS id_documento, v.idCliente AS id_prov_cli,
                    CONCAT(c.nombre,'*/*',c.dias_credito,'*/*',c.rfc) AS info_cliente, rf.folio AS Folio, CONCAT('Factura Venta POS: ',v.idVenta) AS SacarConcepto,
                    ((vp.monto-v.cambio) - IFNULL((SELECT SUM(monto) FROM app_notas_credito WHERE idfactura = rf.id),0)) AS ImporteDoc,
                    /*((vp.monto-v.cambio) / IF(v.tipo_cambio=0,1,v.tipo_cambio) - IFNULL((SELECT SUM(monto) FROM app_notas_credito WHERE idfactura = rf.id),0)) AS ImporteDoc, */
                    /*(SELECT importe FROM cont_facturas WHERE uuid COLLATE utf8_general_ci = rf.folio AND cancelada = 0 LIMIT 1) * IF(v.tipo_cambio=0,1,v.tipo_cambio) - IFNULL((SELECT SUM(monto) FROM app_notas_credito WHERE idfactura = rf.id),0) AS ImporteDoc, */
                    rf.fecha AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 1 AS id_tipo
                    FROM app_pos_venta_pagos vp
                    INNER JOIN app_pos_venta v ON vp.idVenta = v.idVenta
                    INNER JOIN app_respuestaFacturacion rf ON rf.idSale = v.idVenta
                    LEFT JOIN comun_cliente c ON c.id = v.idCliente
                    WHERE v.estatus = 1 AND (v.documento = 1 || v.documento = 2) $moneda_pos
                    AND v.idCliente AND rf.origen = 2 AND vp.idFormapago = 6 AND rf.borrado != 1
                    /*TERMINA SECCION POS*/

                    UNION ALL

                    /*SECCION XTRUCTUR*/
                    SELECT '' AS id_relacion, rf.id AS id_documento, cl.id AS id_prov_cli,
                    CONCAT(cl.nombre,'*/*',cl.dias_credito,'*/*',cl.rfc) AS info_cliente, rf.folio AS Folio, CONCAT('Factura Est. Xtructur: ',c.id) AS SacarConcepto,
                    c.total AS ImporteDoc,
                    rf.fecha AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 1 AS id_tipo
                    FROM constru_estimaciones_bit_cliente c
                    INNER JOIN app_respuestaFacturacion rf ON rf.idSale = c.id
                    LEFT JOIN comun_cliente cl ON cl.id_xtructur = c.id_cliente
                    WHERE c.estatus = 4 AND rf.proviene = 4 AND c.fp = 6 AND rf.borrado != 1 AND c.id_cliente

                    UNION ALL

                    /*SECCION CARGOS*/
                    SELECT '' AS id_relacion, p.id AS id_documento, p.id_prov_cli AS id_prov_cli, CONCAT(c.nombre,'*/*',c.dias_credito,'*/*',c.rfc) AS info_cliente, p.id AS Folio, p.concepto AS SacarConcepto,
                    CASE p.id_moneda
                    WHEN 1 THEN p.cargo
                    WHEN 2 THEN p.cargo*p.tipo_cambio END AS ImporteDoc,
                    p.fecha_pago AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 0 AS id_tipo
                    FROM app_pagos p
                    INNER JOIN comun_cliente c ON c.id = p.id_prov_cli
                    WHERE p.cobrar_pagar = 0 AND p.cargo != 0 $moneda

                    ORDER BY id_prov_cli, Folio, fecha_pago, id_relacion
                    ";


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
            $moneda = " AND p.moneda = ".$vars['id_moneda'];
        }

        $myQuery = "SELECT '' AS id_relacion, fa.id AS id_documento , p.idPrv AS id_prov_cli, CONCAT(p.razon_social COLLATE utf8_general_ci,'*/*',p.diascredito,'*/*',p.rfc) AS info_proveedor, fa.id AS Folio, CONCAT('Folio ',fa.folio) AS SacarConcepto, fa.importe AS ImporteDoc, fa.fecha AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 1 AS id_tipo, 1 AS Fac
                    FROM cont_facturas fa FORCE INDEX (idx_rfc, idx_uuid, idx_fecha)
                    RIGHT JOIN mrp_proveedor p FORCE INDEX (idx_status, idx_rfc) ON p.rfc = fa.rfc COLLATE utf8_general_ci AND p.status = -1
                    WHERE tipo LIKE '%EGRESO%' AND fa.rfc != 'XAXX010101000' AND fa.rfc != 'XEXX010101000' AND fa.fecha >= '2018-01-01 00:00:00' AND cancelada = 0  AND xml IN (SELECT xmlfile FROM app_recepcion_xml) AND (json NOT LIKE '%TipoDeComprobante\":\"E\"%' AND json NOT LIKE '%TipoRelacion\":\"01\"%' AND json NOT LIKE '%TipoRelacion\":\"03\"%')

                    UNION ALL

                    /* CARGOS*/
                   SELECT '' AS id_relacion, p.id AS id_documento, p.id_prov_cli AS id_prov_cli, CONCAT(pro.razon_social,'*/*',pro.diascredito,'*/*',pro.rfc) AS info_proveedor, p.id AS Folio, p.concepto AS SacarConcepto,
                    CASE p.id_moneda
                    WHEN 1 THEN p.cargo
                    WHEN 2 THEN p.cargo*p.tipo_cambio END AS ImporteDoc,
                    p.fecha_pago AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 0 AS id_tipo, 0 AS Fac
                    FROM app_pagos p
                    INNER JOIN mrp_proveedor pro ON pro.idPrv = p.id_prov_cli
                    WHERE p.cobrar_pagar = 1 AND p.cargo != 0 $moneda

                    UNION ALL

                    /* FACTURAS ALMACEN*/
                    SELECT '' AS id_relacion, fa.id AS id_documento , p.idPrv AS id_prov_cli, CONCAT(p.razon_social,'*/*',p.diascredito,'*/*',p.rfc) AS info_proveedor, fa.id AS Folio, CONCAT('Folio ',fa.folio) AS SacarConcepto, fa.importe AS ImporteDoc, fa.fecha AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 5 AS id_tipo, 1 AS Fac
                    FROM cont_facturas fa FORCE INDEX (idx_rfc, idx_uuid, idx_fecha)
                    RIGHT JOIN mrp_proveedor p FORCE INDEX (idx_status, idx_rfc) ON p.rfc = fa.rfc COLLATE utf8_general_ci AND p.status = -1
                    WHERE tipo LIKE '%EGRESO%' AND fa.rfc != 'XAXX010101000' AND fa.rfc != 'XEXX010101000' AND fa.fecha >= '2018-01-01 00:00:00' AND cancelada = 0 AND xml NOT IN (SELECT xmlfile FROM app_recepcion_xml) AND (json NOT LIKE '%TipoDeComprobante\":\"E\"%' AND json NOT LIKE '%TipoRelacion\":\"01\"%' AND json NOT LIKE '%TipoRelacion\":\"03\"%')

                    UNION ALL

                    /* FACTURAS XTRUCTUR*/
                    SELECT '' AS id_relacion, x.id AS id_documento , p.idPrv AS id_prov_cli, CONCAT(p.razon_social,'*/*',p.diascredito,'*/*',p.rfc) AS info_proveedor, x.id AS Folio, 'Est. Prov. Xtructur' AS SacarConcepto, e.total AS ImporteDoc, x.fecha_fac AS fecha_documento,
                    '' AS fecha_pago, '' AS concepto, '' AS abono, 1 AS id_tipo, 1 AS Fac
                    FROM constru_estimaciones_bit_prov e
                    INNER JOIN constru_xml_pedis x ON x.id_estimacion = e.id
                    INNER JOIN constru_pedis pe ON pe.id = e.id_oc AND pe.id_prov = e.id_prov
                    LEFT JOIN mrp_proveedor p ON p.id_xtructur = e.id_prov
                    WHERE e.estatus = 1 AND pe.fpago = 6 AND x.borrado != 1
                    ORDER BY id_prov_cli, Folio, fecha_pago, id_relacion
                    ";


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

    public function listaFacturas($idPrvCli,$cobrar_pagar)
    {
        //$conexion_bancos = $this->conectado_bancos();

        if(intval($cobrar_pagar))
        {
            /*SELECT rq.tipo_cambio AS rq_tipo_cambio, r.id_oc, 1 AS origen, (SELECT codigo FROM cont_coin WHERE coin_id = rq.id_moneda) AS Moneda, r.concepto AS desc_concepto, r.id_oc AS id, r.fecha_factura,0 AS no_factura,SUM(r.imp_factura) AS imp_factura,SUM(r.imp_factura*IF(rq.tipo_cambio = 0,1,rq.tipo_cambio)) AS importe_pesos, r.xmlfile, (SELECT diascredito FROM mrp_proveedor WHERE idPrv = c.id_proveedor) AS diascredito,
                    @c := (SELECT SUM(rp.cargo) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = r.id_oc AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                    @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = r.id_oc AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                    (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, (SELECT folio FROM cont_facturas WHERE xml LIKE CONCAT('%',r.xmlfile,'%')) AS folio_fac
                    FROM app_recepcion_xml r INNER JOIN app_ocompra c ON c.id = r.id_oc
                    INNER JOIN app_requisiciones rq ON rq.id = id_requisicion
                    WHERE c.id_proveedor = $idPrvCli AND xmlfile != ''
                    GROUP BY id_oc

                    UNION ALL*/

                    /* FACTURAS XTRUCTUR*/
            $myQuery = "SELECT 1 AS rq_tipo_cambio, p.id AS id_oc, 4 AS origen, 'MXN' AS Moneda, CONCAT('(',x.folio,') Obra: ',g.obra,' Est: ',p.id) AS desc_concepto, x.id AS id, x.fecha_fac AS fecha_factura, 0 AS no_factura,SUM(p.total) AS imp_factura,SUM(p.total) AS importe_pesos, x.xml_file, (SELECT diascredito FROM mrp_proveedor WHERE id_xtructur = p.id_prov) AS diascredito,
                    @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = x.id AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                    @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = x.id AND rp.id_tipo=1 AND p.cobrar_pagar = 1),
                    (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, (SELECT CONCAT(folio,'/',serie) AS Folio FROM cont_facturas WHERE xml LIKE CONCAT('%',x.xml_file,'%')) AS folio_fac

                    FROM constru_estimaciones_bit_prov p
                    INNER JOIN constru_xml_pedis x ON x.id_estimacion = p.id
                    INNER JOIN constru_generales g ON g.id = p.id_obra
                    INNER JOIN constru_pedis pe ON pe.id = p.id_oc AND pe.id_prov = p.id_prov
                    WHERE p.id_prov = (SELECT id_xtructur FROM mrp_proveedor WHERE idPrv = $idPrvCli) AND p.estatus = 1 AND x.borrado != 1 AND pe.fPago = 6
                    GROUP BY x.xml_file

                    UNION ALL

                     /* FACTURAS ALMACEN*/
                   (SELECT 1 AS rq_tipo_cambio, fa.id AS id_oc, 5 AS origen, fa.moneda COLLATE utf8_general_ci AS Moneda , fa.uuid AS desc_concepto, fa.id AS id, fa.fecha AS fecha_factura,0 AS no_factura,fa.importe AS imp_factura,fa.importe AS importe_pesos, fa.xml AS xmlfile, (SELECT diascredito FROM mrp_proveedor WHERE idPrv = $idPrvCli AND status = -1) AS diascredito,
                    @c := (SELECT SUM(rp.cargo*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND rp.id_documento = fa.id AND rp.id_tipo=5 AND p.cobrar_pagar = 1),
                    @a := (SELECT SUM(rp.abono*p.tipo_cambio) FROM app_pagos_relacion rp INNER JOIN app_pagos p ON p.id = rp.id_pago WHERE rp.activo = 1 AND  rp.id_documento = fa.id AND rp.id_tipo=5 AND p.cobrar_pagar = 1),
                    (IFNULL(@a,0) - IFNULL(@c,0)) AS pagos, CONCAT(fa.folio,'/',fa.serie) AS folio_fac
                    FROM cont_facturas fa
                    WHERE fa.cancelada = 0 AND fa.rfc != 'XAXX010101000' AND fa.rfc != 'XEXX010101000' AND fa.fecha >= '2018-01-01 00:00:00' AND fa.rfc COLLATE utf8_general_ci = (SELECT rfc FROM mrp_proveedor WHERE idPrv = $idPrvCli) AND (json NOT LIKE '%TipoDeComprobante\":\"E\"%' AND json NOT LIKE '%TipoRelacion\":\"01\"%' AND json NOT LIKE '%TipoRelacion\":\"03\"%' AND fa.tipo LIKE '%Egreso%'))


                    ORDER BY id_oc;";
        }
        else
        {
            if(intval($idPrvCli))
            {
                $myQuery = "SELECT 
                            	rf.id AS id,
                                1 AS origen,
                                v.tipo_cambio AS rq_tipo_cambio,
                                v.idVenta AS id_oventa,
                            	(SELECT
                            		codigo
                            	FROM cont_coin
                                WHERE coin_id = IF(v.moneda = 0,1,v.moneda)) AS Moneda,
                                CONCAT('Venta POS: ',v.idVenta) AS desc_concepto,
                                rf.folio,
                                rf.id AS idres,
                                rf.fecha AS fecha_factura,
                            	@total := ABS(((vp.monto-v.cambio)/IF(v.tipo_cambio = 0,1,v.tipo_cambio) - IFNULL(
                            		(SELECT
                            			SUM(monto)
                            		 FROM app_notas_credito
                            		 WHERE idfactura = rf.id),0))) AS imp_factura,
                                ABS((@total*IF(v.tipo_cambio = 0,1,v.tipo_cambio))) AS importe_pesos,
                                rf.xmlfile,
                                (SELECT
                            		dias_credito
                                 FROM comun_cliente
                                 WHERE id = v.idCliente) AS diascredito,
                            	@c := (SELECT
                            			SUM(rp.cargo*p.tipo_cambio)
                            		   FROM app_pagos_relacion rp
                            		   INNER JOIN app_pagos p ON p.id = rp.id_pago
                            		   WHERE rp.activo = 1
                            			AND  rp.id_documento = rf.id
                            			AND rp.id_tipo=1 AND p.cobrar_pagar = 0),
                            	@a := (SELECT
                            			SUM(rp.abono*p.tipo_cambio)
                            		   FROM app_pagos_relacion rp
                            		   INNER JOIN app_pagos p ON p.id = rp.id_pago
                            		   WHERE rp.activo = 1
                            			AND rp.id_documento = rf.id
                            			AND rp.id_tipo=1
                            			AND p.cobrar_pagar = 0),
                            	(IFNULL(@a,0) - IFNULL(@c,0)) AS pagos,
                                (SELECT
                            		CONCAT(folio,'/',serie) AS Folio
                            	 FROM cont_facturas
                                 WHERE uuid
                            		COLLATE utf8_general_ci LIKE CONCAT('%',rf.folio,'%')
                            		AND cancelada = 0 LIMIT 1) AS folio_fac
                            FROM app_pos_venta_pagos vp
                            INNER JOIN app_pos_venta v ON vp.idVenta = v.idVenta
                            INNER JOIN app_respuestaFacturacion rf ON rf.idSale = v.idVenta
                            LEFT JOIN comun_cliente c ON c.id = v.idCliente
                            WHERE v.idCliente = {$idPrvCli} AND estatus = 1 AND (v.documento = 1 || v.documento = 2)
                            	AND vp.idFormapago = 6 AND rf.origen = 2 AND rf.borrado != 1
                            /*TERMINA POS*/
                            UNION ALL
                            /*COMIENZA XTRUCTUR*/
                            SELECT
                            	rf.id AS id,
                                2 AS origen,
                                1 AS rq_tipo_cambio,
                                c.id AS id_oventa,
                            	'MXN' AS Moneda,
                                CONCAT('Est. Obra: ',c.id) AS desc_concepto,
                                rf.folio,
                                rf.id AS idres,
                                rf.fecha AS fecha_factura,
                                @total := c.total AS imp_factura,
                                (@total) AS importe_pesos,
                                rf.xmlfile,
                                (SELECT
                            		dias_credito
                            	 FROM comun_cliente
                                 WHERE id_xtructur = c.id_cliente) AS diascredito,
                            	@c := (SELECT
                            				SUM(rp.cargo*p.tipo_cambio)
                            			FROM app_pagos_relacion rp
                                        INNER JOIN app_pagos p ON p.id = rp.id_pago
                                        WHERE rp.activo = 1
                            				AND rp.id_documento = rf.id
                                            AND rp.id_tipo=1
                                            AND p.cobrar_pagar = 0),
                            	@a := (SELECT
                            				SUM(rp.abono*p.tipo_cambio)
                            			FROM app_pagos_relacion rp
                                        INNER JOIN app_pagos p ON p.id = rp.id_pago
                                        WHERE rp.activo = 1
                            				AND rp.id_documento = rf.id
                                            AND rp.id_tipo=1
                                            AND p.cobrar_pagar = 0),
                            	(IFNULL(@a,0) - IFNULL(@c,0)) AS pagos,
                                (SELECT
                            		CONCAT(folio,'/',serie) AS Folio
                            	FROM cont_facturas
                                WHERE uuid
                            	COLLATE utf8_general_ci LIKE CONCAT('%',rf.folio,'%')) AS folio_fac
                            		FROM constru_estimaciones_bit_cliente c
                            		INNER JOIN app_respuestaFacturacion rf ON rf.idSale = c.id
                            		WHERE c.id_cliente = (SELECT
                            								id_xtructur
                            							  FROM comun_cliente
                            							  WHERE id = {$idPrvCli})
                            			AND c.estatus = 4
                            			AND rf.proviene = 4
                            			AND c.fp = 6
                            			AND rf.borrado != 1;";
            }


        }

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

        $myQuery = "INSERT INTO app_pagos(id,
                    cobrar_pagar,
                    id_prov_cli,
                    cargo,
                    abono,
                    fecha_pago,
                    concepto,
                    id_forma_pago,
                    numero_cheque,
                    comprobante,
                    id_moneda,
                    tipo_cambio,
                    cuenta_bancaria_origen,
                    idbanco_origen_nac,
                    idbanco_origen_ext,
                    cuenta_bancaria_destino,
                    idbanco_destino_nac,
                    idbanco_destino_ext,
                    spei,
                    spei_certificado,
                    spei_cadena,
                    spei_sello)
                    VALUES(0,
                    ".$variables['cobrar_pagar'].",
                    ".$variables['idPrvCli'].",
                    $cargo,
                    $abono,
                    '".$variables['fecha']." ".$variables['hora']."',
                    '".$variables['concepto']."',
                    ".$variables['forma_pago'].",
                    '".$variables['numero_cheque']."',
                    '".$variables['comprobante']."',
                    ".$variables['moneda'].",
                    $tipo_cambio,
                    '".$variables['cuenta_bancaria_origen']."',
                    '".$variables['banco_origen_nac']."',
                    '".$variables['banco_origen_ext']."',
                    '".$variables['cuenta_bancaria_destino']."',
                    '".$variables['banco_destino_nac']."',
                    '".$variables['banco_destino_ext']."',
                    '".$variables['tipo_pago_spei']."',
                    '".$variables['spei_certificado']."',
                    '".$variables['spei_cadena']."',
                    '".$variables['spei_sello']."');";
        return $this->insert_id($myQuery);
    }

    function listaFolios($idPrv)
    {
        $myQuery = "SELECT r.no_factura FROM app_recepcion r WHERE ";
        return $this->query($myQuery);
    }

    public function foliosFac($id_oc)
    {
        $lista = '';
        $res = $this->query("SELECT xmlfile FROM app_recepcion_xml WHERE id_oc = $id_oc");
        while($r = $res->fetch_object())
        {
            $a = explode('_',$r->xmlfile);
            if(!$a[0])
                $a[0] = $a[2];
            $lista .= $a[0].", ";
        }
        return $lista;
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

    function pagos_detalle($id,$t,$ori)
    {

        //Si es un cargo
        if($t == 'c')
            $tipo = '0';
        if($t == 'f')
            $tipo = $ori;

        $myQuery = "SELECT pr.id_pago, pr.id AS id_rel, p.fecha_pago, (pr.abono*p.tipo_cambio) AS abono, (SELECT CONCAT('(',claveSat,') ',nombre) FROM forma_pago WHERE idFormapago = p.id_forma_pago) AS forma_pago, origen, activo, id_poliza_mov, p.concepto, IF(pr.usuario = 0,'<i style=\'font-size:12px;\'>No hay dato</i>',(SELECT usuario FROM accelog_usuarios WHERE idempleado = pr.usuario)) AS usuario
                        FROM app_pagos_relacion pr INNER JOIN app_pagos p ON p.id = pr.id_pago
                        WHERE pr.id_documento = $id AND pr.id_tipo = $tipo ORDER BY pr.id";

        return $this->query($myQuery);
    }

    function info_car_fac($id,$t,$cp,$pos)
    {
        if($t == 'c')
        {
            if(!intval($cp))
                $provcli = "(SELECT CONCAT(nombre,'**/**',dias_credito) FROM comun_cliente WHERE id = id_prov_cli) AS provcli";
            else
                $provcli = "(SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = id_prov_cli AND status = -1) AS provcli";
            $myQuery = "SELECT id_prov_cli, $provcli, (cargo*tipo_cambio) AS cargo, concepto, tipo_cambio,fecha_pago FROM app_pagos WHERE id = $id";
        }
        if($t == 'f')
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
                if(intval($pos) == 2)
                {
                    /*XTRUCTUR*/
                    $myQuery = "SELECT cl.id AS id_prov_cli,
                            CONCAT(cl.nombre,'**/**',cl.dias_credito) AS provcli,
                            SUM(c.total) AS cargo,
                            CONCAT(f.folio,' / ',f.serie,' / ',f.uuid,' Est. Xtructur: ',c.id) AS concepto, 1 AS tipo_cambio, rf.fecha AS fecha_pago
                            FROM app_respuestaFacturacion rf
                            INNER JOIN cont_facturas f ON uuid LIKE CONCAT('%',rf.folio,'%') COLLATE utf8_general_ci
                            INNER JOIN constru_estimaciones_bit_cliente c ON c.id = rf.idSale
                            LEFT JOIN comun_cliente cl ON cl.id_xtructur = c.id_cliente
                            WHERE rf.id = $id AND c.fp = 6";
                }
            }
            else
            {
                if(intval($pos) < 4)
                {
                    /*COMPRAS*/
                    $myQuery = "SELECT oc.id_proveedor AS id_prov_cli,
                            (SELECT CONCAT(razon_social,'**/**',diascredito) FROM mrp_proveedor WHERE idPrv = oc.id_proveedor) AS provcli,
                            SUM(CASE rq.id_moneda WHEN 1 THEN r.imp_factura WHEN 2 THEN r.imp_factura*rq.tipo_cambio END) AS cargo,
                            r.concepto, rq.tipo_cambio, r.fecha_factura AS fecha_pago
                            FROM app_recepcion_xml r
                            INNER JOIN app_ocompra oc ON oc.id = r.id_oc
                            INNER JOIN app_requisiciones rq ON rq.id = oc.id_requisicion
                            WHERE r.id_oc = $id";
                }
                if(intval($pos) == 4)
                {
                    /*XTRUCTUR*/
                    $myQuery = "SELECT p.idPrv AS id_prov_cli,
                            CONCAT(p.razon_social,'**/**',p.diascredito) AS provcli,
                            SUM(e.total) AS cargo,
                            CONCAT(x.folio,'<br />Obra: ',g.obra,'<br />Est: ',e.id) AS concepto, 1 AS tipo_cambio, x.fecha_fac AS fecha_pago
                            FROM constru_xml_pedis x
                            INNER JOIN constru_estimaciones_bit_prov e ON e.id = x.id_estimacion
                            INNER JOIN constru_generales g ON g.id = e.id_obra
                            LEFT JOIN mrp_proveedor p ON p.id_xtructur = e.id_prov
                            WHERE x.id = $id";
                }

                 if(intval($pos) == 5)
                {
                    /*FACTURAS ALMACEN DIGITAL*/
                    $myQuery = "SELECT p.idPrv AS id_prov_cli,
                            CONCAT(p.razon_social,'**/**',p.diascredito) AS provcli,
                            SUM(fa.importe) AS cargo,
                            CONCAT(fa.folio,' / ',fa.serie,' / ',fa.uuid) AS concepto, 1 AS tipo_cambio, fa.fecha AS fecha_pago
                            FROM cont_facturas fa
                            LEFT JOIN mrp_proveedor p ON p.rfc = fa.rfc COLLATE utf8_general_ci AND status = -1
                            WHERE fa.id = $id";
                }

            }


        }

        $datos = $this->query($myQuery);
        $datos = $datos->fetch_assoc();
        return $datos;
    }

    function guardar_relacion(
      $idpago,
      $idrelaciones,
      $fecha_poliza,
      $tipo,
      $valores,
      $monedas,
      $monedaPago,
      $origs
    ){

      session_start();

      if($fecha_poliza == ''){
          $fecha = $this->query(
            "SELECT
              fecha_pago
            FROM app_pagos
            WHERE id = $idpago"
          );
          $fecha = $fecha->fetch_object();
      }
      else{
          $fecha = $fecha_poliza;
      }

      $idrelaciones = substr(trim($idrelaciones), 0, -3);
      $valores = substr(trim($valores), 0 , -3);
      $monedas = substr(trim($monedas), 0, -3);
      $origs = substr(trim($origs), 0, -3);

      $myQuery = '';
      $idrelaciones = explode("@|@",$idrelaciones);
      $valores = explode("@|@",$valores);
      $monedas = explode("@|@",$monedas);
      $origs = explode("@|@",$origs);
      $contBancos = 0;
      $id_pago_rel = 0;

      ///////////////////////ACONTIA///////////////////////////////
      ////////////////////////////////////////////////////////////
      $genera_poliza = 0; //Por default no genera poliza
      //Si tiene acontia y esta conectado
      $conexion_acontia = $this->query(
        "SELECT
          conectar_acontia,
          pol_autorizacion
        FROM app_configuracion
        WHERE id = 1"
      );
      $conexion_acontia = $conexion_acontia->fetch_assoc();

      if($conexion_acontia['conectar_acontia'] == 1){
          //Buscar si es una cuenta por pagar o por cobrar
          $cobrar_pagar = $this->query("SELECT * FROM app_pagos WHERE id = $idpago");
          $cobrar_pagar = $cobrar_pagar->fetch_assoc();
          if($fecha_poliza == '')
              $fecha_poliza = $cobrar_pagar['fecha_pago'];

          if(intval($cobrar_pagar['cobrar_pagar']))
          {
              if(intval($tipo))
                  $idpol = 3;//Busca la poliza de cxp
              else
                  $idpol = 12;//Busca la poliza de cxp
              $concepto = "Pago CXP";
          }
          else
          {
              if(intval($tipo))
                  $idpol = 4;//Busca la poliza de cxc
              else
                  $idpol = 11;//Busca la poliza de cxc

              $concepto = "Cobro CXC";
          }


          //Busca si es poliza automatica
          $automatica = $this->query("SELECT* FROM app_tpl_polizas WHERE id = $idpol");
          $automatica = $automatica->fetch_assoc();

          //Si es automatica y se genera por documento
          if(intval($automatica['automatica']) && intval($automatica['poliza_por_mov']) == 1)
          {
              $fecha = explode('-',$fecha_poliza);

              //Busca el id del ejercicio, si no existe, busca el ultimo y le suma al id para sacar el ejercicio
              $ejercicio = $this->query("SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = ".$fecha[0]);
              $ejercicio = $ejercicio->fetch_assoc();
              $ejercicio = $ejercicio['Id'];

              //Si no existe calcula el Id
              if(!intval($ejercicio))
              {
                  $ejercicioAntes = $this->query("SELECT * FROM cont_ejercicios ORDER BY Id DESC LIMIT 1");
                  $ejercicioAntes = $ejercicioAntes->fetch_assoc();
                  $nuevoEj = intval($fecha[0]) - intval($ejercicioAntes['NombreEjercicio']);
                  $ejercicio = intval($ejercicioAntes['Id']) + $nuevoEj;
              }

              $numpol = $this->query("SELECT IFNULL((SELECT pp.numpol+1  FROM cont_polizas pp WHERE pp.idtipopoliza = ".$automatica['id_tipo_poliza']." AND pp.activo = 1 AND pp.idejercicio = $ejercicio AND pp.idperiodo = ".intval($fecha[1])." ORDER BY pp.numpol DESC LIMIT 1),0) AS n");

              $numpol = $numpol->fetch_assoc();
              $numpol = $numpol['n'];
              if(!intval($numpol))
                  $numpol = 1;

              $activo = 1;
              if(intval($conexion_acontia['pol_autorizacion']))
                  $activo = 0;


              $id_poliza_acontia = $this->insert_id("INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, concepto, origen, idorigen, fecha, fecha_creacion, activo, eliminado, pdv_aut, usuario_creacion, usuario_modificacion)
                VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'".$automatica['nombre_poliza']." ".$cobrar_pagar['concepto']."','CXC',$idpago,'".$fecha_poliza."',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, ".$_SESSION["accelog_idempleado"].", 0)");
              $cont = 0;//Contador de movimientos
              $genera_poliza = 1;
              $sumImportes = 0;
          }
      }
      //Termina conexion con acontia
      ////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////
      //print_r($idrelaciones);

      for($i=0; $i<sizeof($idrelaciones); $i++){
          
        if($monedaPago != 'MXN')
          $valores[$i] = floatval($valores[$i]) / floatval($this->tipo_cambio_pago($idpago));

        if (empty($origs[$i])) {
            $origs[$i] = 0;
        }

          $myQuery = 
            "INSERT INTO app_pagos_relacion(
              id_pago,
              id_tipo,
              id_documento,
              cargo,
              abono,
              usuario
            ) VALUES(
              {$idpago},
              {$origs[$i]},
              {$idrelaciones[$i]},
              0,
              {$valores[$i]},
              {$_SESSION["accelog_idempleado"]}
            );";

          $id_pago_rel = $this->insert_id($myQuery);

          ///////////////////////ACONTIA///////////////////////////////
          ////////////////////////////////////////////////////////////
          if($id_pago_rel>0 && $genera_poliza) {
              
            $cuentas_poliza = $this->query(
              "SELECT
                id_cuenta,
                tipo_movto,
                id_dato
              FROM app_tpl_polizas_mov
              WHERE activo = 1
              AND id_tpl_poliza = $idpol"
            );
  
            while($cp = $cuentas_poliza->fetch_assoc()){
                  
              $cont++;
              //Cargo o abono
              if(intval($cp['tipo_movto']) == 1)
                  $tipo_movto = "Abono";
              if(intval($cp['tipo_movto']) == 2)
                  $tipo_movto = "Cargo";

              //dependiendo el tipo de dato sera el valor que tomara, en este caso solo existe el total del pago.
              $importe = 0;
              $subtot = floatval($valores[$i]) / 1.16;
              switch(intval($cp['id_dato']))
              {
                  case 1 : $importe = $valores[$i]; break;
                  case 2 : $importe = $subtot; break;
                  case 3 : $importe = floatval($valores[$i]) - $subtot; break;
                  case 4 : $importe = $valores[$i]; break;
                  case 5 : $importe = $valores[$i]; break;
              }

              //Si tiene cuenta de clientes busca si el id del cliente esta vinculado a una cuenta, si no es asi lo asignara a la cuenta configurada.
              if(intval($cp['id_dato']) == 4 && intval($cobrar_pagar['cobrar_pagar']) == 0)
              {
                  $cuentaCliProv = $this->query("SELECT cuenta FROM comun_cliente WHERE id = ".$cobrar_pagar['id_prov_cli']);
                  $cuentaCliProv = $cuentaCliProv->fetch_assoc();
                  if(intval($cuentaCliProv['cuenta']))
                      $cp['id_cuenta'] = $cuentaCliProv['cuenta'];
              }

              //Si tiene cuenta de proveedor busca si el id del proveedor esta vinculado a una cuenta, si no es asi lo asignara a la cuenta configurada.
              if(intval($cp['id_dato']) == 5 && intval($cobrar_pagar['cobrar_pagar']) == 1)
              {
                  $cuentaCliProv = $this->query("SELECT cuenta FROM mrp_proveedor WHERE idPrv = ".$cobrar_pagar['id_prov_cli']);
                  $cuentaCliProv = $cuentaCliProv->fetch_assoc();
                  if(intval($cuentaCliProv['cuenta']))
                      $cp['id_cuenta'] = $cuentaCliProv['cuenta'];
              }

              $iddoc = $idrelaciones[$i];
              $ref = '';
              if(intval($origs[$i])){
                  if(intval($cobrar_pagar['cobrar_pagar']))
                      $info_fac = $this->info_rel3($idrelaciones[$i]);
                  else
                      $info_fac = $this->info_rel2($idrelaciones[$i]);


                  $ruta = "../cont/xmls/facturas/";

                  if(file_exists($ruta . "temporales/" . $info_fac['xml'])){
                      if(!file_exists($ruta . $id_poliza_acontia ."/")){
                          mkdir ($ruta . $id_poliza_acontia ."/",0777);
                      }
                      if(!file_exists($ruta . $id_poliza_acontia . "/" . $info_fac['xml']))
                          copy($ruta . "temporales/" . $info_fac['xml'], $ruta . $id_poliza_acontia . "/" . $info_fac['xml']);
                      //leer el json para obtener
                      $info_fac['json'] = str_replace("\\", "", $info_fac['json']);
                      $json = json_decode($info_fac['json']);
                      $json = $this->object_to_array($json);
                      $iddoc = $json['Comprobante']['cfdi:Conceptos']['cfdi:Concepto']['@Descripcion'];

                      if(is_array($iddoc))
                          $iddoc = $iddoc[0];

                      $ref = $info_fac['xml'];
                  }

              }

              if(!$this->esBancos($cp['id_cuenta']))
              {
                  $xml = $this->getUUID($ref);
                  $id_mov = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, Factura, FormaPago, tipocambio)
                          VALUES($id_poliza_acontia, $cont, 1, 1, ".$cp['id_cuenta'].", '$tipo_movto', $importe, '$xml','$concepto Doc: $iddoc', $activo, DATE_SUB(NOW(), INTERVAL 6 HOUR), '$ref', ".$cobrar_pagar['id_forma_pago'].", ".$cobrar_pagar['tipo_cambio'].")");
                  $ids_movs .= $id_mov.",";

                  //Hacer relacion de facturas y poliza

                  if($xml != '')
                  {
                      $myQuery = "INSERT INTO cont_facturas_poliza(uuid,tipo_poliza,id_poliza,activo)
                                  SELECT * FROM (SELECT '$xml' as xml, 1 as tipo, $id_poliza_acontia as idpoliza,1 as activo) AS tmp
                                  WHERE NOT EXISTS (SELECT uuid FROM cont_facturas_poliza WHERE uuid = '$xml' AND id_poliza = $id_poliza_acontia) LIMIT 1;";

                      $this->query($myQuery);
                  }
                  //FIN

              }
              else
              {
                  $contBancos++;
                  $id_cuenta = $cp['id_cuenta'];
                  if($cobrar_pagar['tipo_cambio'])
                      $importe = $importe * floatval($cobrar_pagar['tipo_cambio']);
                  $sumImportes += $importe;
                  $tipo_movto_bn = $tipo_movto;
                  $forma_pago = $cobrar_pagar['id_forma_pago'];
              }
              }
              $this->query("UPDATE app_pagos_relacion SET id_poliza_mov = '$ids_movs' WHERE id = $id_pago_rel");
              $ids_movs = '';
          }
      }

      if($contBancos>0) {
          $this->query(
            "INSERT INTO cont_movimientos(
              IdPoliza,
              NumMovto,
              IdSegmento,
              IdSucursal,
              Cuenta,
              TipoMovto,
              Importe,
              Referencia,
              Concepto,
              Activo,
              FechaCreacion,
              Factura,
              FormaPago,
              tipocambio
            ) VALUES (
              $id_poliza_acontia,
              $cont,
              1,
              1,
              ".$id_cuenta.",
              '$tipo_movto_bn',
              $sumImportes,
              'Suma de Importes de Bancos $ref',
              '$concepto Suma Bancos',
              $activo,
              DATE_SUB(NOW(), INTERVAL 6 HOUR),
              '$ref',
              ".$forma_pago.",
              0
            )"
          );
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

  public function info_pago2($idpago){

    $datos = $this->query(
      "SELECT
        p.fecha_pago,
        fp.claveSat AS forma_pago,
        cc.codigo AS moneda,
        p.abono,
        IFNULL(cb.rfc,'') AS RfcEmisorCtaOrd,
        IFNULL(cb.nombre,'') AS NomBancoOrdExt,
        p.cuenta_bancaria_origen AS CtaOrdenante,
        IFNULL(cbn.rfc,'') AS RfcEmisorCtaBen,
        p.cuenta_bancaria_destino AS CtaBeneficiario,
        p.spei,
        p.spei_certificado,
        p.spei_cadena,
        p.spei_sello
      FROM app_pagos AS p
      LEFT JOIN forma_pago AS fp ON(p.id_forma_pago = fp.idFormapago)
      LEFT JOIN cont_coin AS cc ON(p.id_moneda = cc.coin_id)
      LEFT JOIN cont_bancos AS cb ON(p.idbanco_origen_ext = cb.idbanco OR  p.idbanco_origen_nac = cb.idbanco)
      LEFT JOIN cont_bancos AS cbn ON(p.idbanco_destino_nac = cbn.idbanco)
      WHERE p.id = {$idpago}"
    );
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

  public function info_rel2($idrel) {
    $myQuery = 
      "SELECT
        f.uuid,
        f.xml,
        f.serie,
        f.folio,
        f.moneda,
        f.json,
        f.version,
        ROUND(f.importe,2) AS importe,
        IFNULL(
          (SELECT
            CONCAT(parcialidades+1,'*|*',imp_saldo_insoluto)
          FROM cont_facturas_relacion
          WHERE uuid_relacionado LIKE CONCAT('%',f.uuid,'%')
          ORDER BY parcialidades
          DESC LIMIT 1),
        0) AS info_ult_rel
      FROM cont_facturas f
      WHERE f.uuid = (
        SELECT
          folio
        FROM app_respuestaFacturacion
        WHERE id = {$idrel}
      );";
    $res = $this->query($myQuery);
    $res = $res->fetch_assoc();
    ////Actualiza importe menos nortas de credito
    $myqueryFre = 
      "SELECT
        ROUND(IFNULL(sum(n.monto),0),2) AS saldo
      FROM app_notas_credito n, app_respuestaFacturacion r
      WHERE n.idFactura = r.id
      AND r.id = {$idrel}";
    $resMon = $this->queryArray($myqueryFre);
    $res['importe'] = $res['importe'] - $resMon['rows'][0]['saldo'];
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
    public function datosFacturacionCom($id,$cliprov){
            if ($id != '') {
            $datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
            e.estado estado,ciudad,municipio,regimen_fiscal
            from comun_facturacion cf left join estados e on  e.idestado=cf.estado
            where  cliPro='".$cliprov."' and  nombre=" . $id;
            //echo $datosFacturacion;
            $result = $this->queryArray($datosFacturacion);

            if ($result["total"] > 0) {
                $logo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1;";
                $logo = $this->queryArray($logo);
                $r3 = $logo["rows"][0]['logoempresa'];

                return array('correo' => $result["rows"][0]['correo'], 'logo' => $r3);
            }
        } else {
            return false;
        }
    }
        public function datosorganizacion(){
        $selectOrg = "SELECT * from organizaciones c left join estados e on e.idestado=c.idestado left join municipios m on m.idmunicipio=c.idmunicipio where idorganizacion=1";
        $resultSelect = $this->queryArray($selectOrg);
        return $resultSelect['rows'];
    }
    public function datosClienteRecibo($id){
        $query = "SELECT c.nombre,c.direccion,c.colonia,c.cp,e.estado,m.municipio,c.rfc,c.num_ext
                from comun_cliente c
                left join estados e on e.idestado=c.idestado
                left join municipios m on m.idmunicipio=c.idmunicipio
                where c.id=".$id;
        $resultSelect = $this->queryArray($query);
        return $resultSelect['rows'];        

    }

    // public function configAcontia(){
    //     $res = $this->query("SELECT usabancos FROM cont_config WHERE id = 1");
    //     if($res = $res->fetch_assoc())
    //         $return = intval($res['usabancos']);
    //     else
    //         $return = 0;
    //     return $return;
    // }
}
?>
