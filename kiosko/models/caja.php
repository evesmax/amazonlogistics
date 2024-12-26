<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class CajaModel extends Connection
{

    public function datosFacturacion($id) {
        if ($id != '') {
            $datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
            e.estado estado,ciudad,municipio,regimen_fiscal
            from comun_facturacion cf left join estados e on  e.idestado=cf.estado
            where  id=" . $id;

            $result = $this->queryArray($datosFacturacion);

            if ($result["total"] > 0) {
                return $result["rows"][0];
            }
        } else {
            return false;
        }
    }
    
    public function object_to_array($data) {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        }
        return $data;
    }
    
    public function pendienteFacturacion($idFacturacion, $monto, $cliente, $idventa, $trackId, $azurian, $documento) {
    
        $azurian = base64_encode($azurian); 
        $fechaactual = date('Y-m-d H:i:s');
        $tipo = ($documento = 2 ? 'F' : 'R');

        if (is_numeric($cliente)) {
            $query = "INSERT into app_pendienteFactura values(''," . $idventa . ",'" . $fechaactual . "'," . $cliente . ",'" . $monto . "',0,'" . $trackId . "','" . $azurian . "','" . $tipo . "',2);";
            $resultquery = $this->queryArray($query);

                //echo $query;
            return array("status" => true, "type" => 1);
        } else {
            $query = "INSERT into app_pendienteFactura values(''," . $idventa . ",'" . $fechaactual . "',NULL,'" . $monto . "',0,'" . $trackId . "','" . $azurian . "','" . $tipo . "','2');";
                //echo $query;
            $resultquery = $this->queryArray($query);
            return array("status" => true, "type" => 2);
        }
    }

    public function envioFactura($uid, $Email, $azurian, $doc) {
//echo $Email.'===';
            //$azurian=json_decode($azurian);
        $azurian = cajaModel::object_to_array($azurian);
        $datosTimbrado = $azurian['datosTimbrado'];

        if ($azurian['FiscalesEmisor']['noExterior'] == '') {
            $nemi = '';
        } else {
            $nemi = ' #' . $azurian['FiscalesEmisor']['noExterior'];
        }

        if ($azurian['DomicilioReceptor']['noExterior'] == '') {
            $nrec = '';
        } else {
            $nrec = ' #' . $azurian['DomicilioReceptor']['noExterior'];
        }

        //Obteniendo la descripcion de la forma de pago
        $idVenta = $datosTimbrado['idVenta'];
        $formapago = "";

        $queryFormaPago = " SELECT nombre,referencia,claveSat from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;

        $resultqueryFormaPago = $this->queryArray($queryFormaPago);

        foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
            if (strlen($pagosValue["referencia"]) > 0) {
                $formapago .= $pagosValue['nombre'] .'('.$pagosValue['claveSat'].')'.",";
                $refFormaPago = $pagosValue['referencia'];
                //$formapago .= $pagosValue['claveSat'] . ",";
            } else {
                //$formapago .= $pagosValue['nombre'] . ",";
                $formapago .= $pagosValue['nombre'] .'('.$pagosValue['claveSat'].')'.",";
                $refFormaPago = '';
            }
        }

        $formapago = substr($formapago, 0, strlen($formapago) - 1);

        if ($formapago == "") {
            $formapago = ".";
        }
        $azurian['Basicos']['metodoDePago'] = $formapago;
        if (isset($azurian['Basicos']['version'])){

                include "../webapp/modulos/SAT/PDF/CFDIPDF.php";

                $obj = new CFDIPDF( );

                if ($doc == 3) {
                    $doc = "recibo";
                } else {
                    $doc = "";
                }
                $azurian['Conceptos']['conceptosOri'] = preg_replace('/&apos;/', "'", $azurian['Conceptos']['conceptosOri']);
                $azurian['Conceptos']['conceptosOri'] = preg_replace('/&quot;/', '"', $azurian['Conceptos']['conceptosOri']);
                    //$obj->ponerColor('#333333');
                $obj->datosCFD($datosTimbrado['UUID'], $azurian['Basicos']['serie'] . ' ' . $azurian['Basicos']['folio'], $datosTimbrado['noCertificado'], $datosTimbrado['FechaTimbrado'], $datosTimbrado['FechaTimbrado'], $datosTimbrado['noCertificadoSAT'], $azurian['Basicos']['formaDePago'], $azurian['Basicos']['tipoDeComprobante'], $doc);
                $obj->lugarE($azurian['Basicos']['LugarExpedicion']);
                $obj->datosEmisor($azurian['Emisor']['nombre'], $azurian['Emisor']['rfc'], $azurian['FiscalesEmisor']['calle'] . $nemi, $azurian['FiscalesEmisor']['localidad'], $azurian['FiscalesEmisor']['colonia'], $azurian['FiscalesEmisor']['municipio'], $azurian['FiscalesEmisor']['estado'], $azurian['FiscalesEmisor']['codigoPostal'], $azurian['FiscalesEmisor']['pais'], $azurian['Regimen']['Regimen']);
                $obj->datosReceptor($azurian['Receptor']['nombre'], $azurian['Receptor']['rfc'], $azurian['DomicilioReceptor']['calle'] . $nrec, $azurian['DomicilioReceptor']['localidad'], $azurian['DomicilioReceptor']['colonia'], $azurian['DomicilioReceptor']['municipio'], $azurian['DomicilioReceptor']['estado'], $azurian['DomicilioReceptor']['codigoPostal'], $azurian['DomicilioReceptor']['pais']);
                $obj->agregarConceptos($azurian['Conceptos']['conceptosOri']);
                $obj->agregarTotal($azurian['Basicos']['subTotal'], $azurian['Basicos']['total'], $azurian['nnf']['nnf']);
                $obj->agregarMetodo($azurian['Basicos']['metodoDePago'], $refFormaPago, $azurian['Basicos']['total']);
                $obj->agregarSellos($datosTimbrado['csdComplemento'], $datosTimbrado['selloCFD'], $datosTimbrado['selloSAT']);
                $obj->agregarObservaciones($azurian['Observacion']['Observacion']);
                $obj->generar("../webapp/netwarelog/archivos/1/organizaciones/" . $azurian['org']['logo'] . "", 0);
                $obj->borrarConcepto();

                $queryIdReceptor = "SELECT nombre from comun_facturacion where rfc='".$azurian['Receptor']['rfc']."' order by nombre desc";
                $resultOne = $this->queryArray($queryIdReceptor);
        }else{
            //echo '3.3';
            $versionFac = "3.3";
        }
            



        /*$queryCupon = "SELECT cupon from comun_cliente_inadem where idCliente=".$resultOne['rows'][0]['nombre'];
        if($this->queryArray($queryCupon)){
            $resultTwo = $this->queryArray($queryCupon);
            $cuponInadem = $resultTwo['rows'][0]['cupon'];
        }else{
           $resultTwo = '';
           $cuponInadem = '';
        }  */
       
        $selRes = "SELECT serieCsdEmisor from app_respuestaFacturacion where folio='".$uid."'";
        $res = $this->queryArray($selRes);
        //echo '$'.$uuid;
        $cuponInadem = '';
        if ($Email != '') {
            $_REQUEST["factura_mailing"] = true;
            require_once('../webapp/modulos/phpmailer/sendMail.php');

            $mail->From = "netwarmonitorsoporte@gmail.com";
            $mail->FromName = "NetwareMonitor";
            $mail->Subject = "Factura Generada";
            $mail->AltBody = "NetwarMonitor";
            $mail->MsgHTML('Factura Generada');
            if($res['rows'][0]['serieCsdEmisor']=='3'){
                $mail->AddAttachment('../webapp/modulos/cont/xmls/facturas/temporales/'. $uid .'.xml');
                $mail->AddAttachment('../webapp/modulos/facturas/'. $uid .'.pdf');
            }else{
                $mail->AddAttachment('../webapp/modulos/cont/xmls/facturas/temporales/'. $uid .'.xml');
                $mail->AddAttachment('../webapp/modulos/facturas/'. $uid .'.pdf');
            } 

            $mail->AddAddress($Email, $Email);


            @$mail->Send();
        }
        //$cuponInadem='';
       if($cuponInadem ==null || $cuponInadem==''){
        return array("status" => true, "receptor" => str_replace(' ','_',$azurian['Receptor']['nombre']), "cupon" => false);
       }else{
        return array("status" => true, "receptor" => str_replace(' ','_',$azurian['Receptor']['nombre']), "cupon" => $cuponInadem);
       } 
    }

    public function guardarFacturacion($UUID, $noCertificadoSAT, $selloCFD, $selloSAT, $FechaTimbrado, $idComprobante, $idFact, $idVenta, $noCertificado, $tipoComp, $monto, $cliente, $trackId, $idRefact, $azurian, $estatus) {

        $id = $idRefact;
        $idVenta = base64_decode($id);
        $clave = array();

        $clave["instancia"] = substr($id,0,2);

        $clave["anho"] = substr($idVenta,2,4);
        $clave["mes"] = substr($idVenta,6,2);
        $clave["dia"] = substr($idVenta,8,2);
        $clave["hora"] = substr($idVenta,10,2);
        $clave["minuto"] = substr($idVenta,12,2);
        $clave["segundo"] = substr($idVenta,14,2);
        $clave["venta"] = substr($idVenta,16);

        $yadecimal =substr($id,2);
        $desc = hexdec($yadecimal);

        $idRefact = substr($desc,14);

        $yadecimal =substr($id,2);
        $desc = hexdec($yadecimal);

        $idVenta = substr($desc,14);


        $azu = $azurian;
        $azu = str_replace("\\", "", $azu);
        if($azu!=''){ 
            $azu=json_decode($azu); 
        }
        $azu = $this->object_to_array($azu);






        $azurian = base64_encode($azurian);
        $fechaactual = preg_replace('/T/', ' ', $FechaTimbrado);
        if ($idRefact == 'c') {
            $tipoComp = 'C';
            $queryRespuesta = "UPDATE app_respuestaFacturacion SET borrado=2 WHERE idSale='$idVenta'";
            $this->queryArray($queryRespuesta);
        }

        $insertRespuestaFacturacion = "INSERT INTO app_respuestaFacturacion "
        . "(idSale,idFact,folio,factNum,serieCsdSat,serieCsdEmisor,selloDigitalSat,selloDigitalEmisor,fecha,borrado,tipoComp,idComprobante,cadenaOriginal,origen,proviene) VALUES "
        . "('" . $idVenta . "','" . $idFact . "','" . $UUID . "','" . $trackId . "','" . $noCertificadoSAT . "','" . $noCertificado . "','" . $selloSAT . "','" . $selloCFD . "','" . $fechaactual . "',0,'" . $tipoComp . "','" . $idComprobante . "','" . $azurian . "','2','1');";

        $resultInsert = $this->queryArray($insertRespuestaFacturacion);
        $insertedId = $resultInsert["insertId"];


        if (is_numeric($insertedId)) {
            $queryUpdateContador = "UPDATE app_contadorFacturas set total=total+1 where id=1";
            $this->queryArray($queryUpdateContador);

            $ContadorLicencias = "UPDATE comun_parametros_licencias set valor=valor-1 where parametro='Facturas'";
            $this->queryArray($ContadorLicencias);

            if ($tipoComp == "R") {
                $queryUpdateFolo = "UPDATE pvt_serie_folio SET folio_r=folio_r+1 where id=1";
            } else {
                $queryUpdateFolo = "UPDATE pvt_serie_folio SET folio=folio+1 where id=1";
            }
            $this->queryArray($queryUpdateFolo); 
        }

        if (preg_match('/all/', $idRefact)) {
            $idRefact = preg_replace('/all/', '', $idRefact);
            $updatePendienteFactura = "UPDATE app_pendienteFactura SET facturado=1 WHERE id_sale in (" . $idRefact . ")";
            $this->queryArray($updatePendienteFactura);
        }

        if ($idRefact > 0 && $idRefact != 'c') {
            $updatePendienteFactura = "UPDATE app_pendienteFactura SET facturado=1 WHERE id_sale='$idRefact'";
            $this->queryArray($updatePendienteFactura);
        }
        $queryEnvio = "UPDATE app_pos_venta set envio=2 where idVenta=".$idVenta;
        $this->queryArray($queryEnvio);
                //////Insert en cont_facturas
        //$UUID = '1A16D7FD-883E-460C-B32B-53902A1096F5';
        $xmlx = $UUID .'.xml';

        include("../webapp/libraries/xml2json/xml2json.php");

        $cont_xml = simplexml_load_file('../webapp/modulos/cont/xmls/facturas/temporales/'. $UUID .'.xml');
        $cont_array = xmlToArray($cont_xml);
        $json = utf8_encode(json_encode($cont_array,JSON_UNESCAPED_UNICODE));
        $json = str_replace("\\", "", $json);
       
        date_default_timezone_set("Mexico/General");
        $fechaactual = date("Y-m-d H:i:s");
        $fechaFac = str_replace('T', ' ', $azu['Basicos']['fecha']);
        $version = '3.3';
        if (isset($azu['Basicos']['version'])){
            $inseCon = "INSERT into cont_facturas(folio,uuid,er,tipo,serie,emisor,receptor,importe,moneda,rfc,fecha,fecha_subida,xml,version,cancelada,json,temporal,origen) values('".$azu['Basicos']['folio']."','".$UUID."','E','Ingreso','".$azu['Basicos']['serie']."','".$azu['Emisor']['nombre']."','".$azu['Receptor']['nombre']."','".$azu['Basicos']['total']."','".$azu['Basicos']['Moneda']."','".$azu['Receptor']['rfc']."','".$fechaFac."','".$fechaactual."','".$xmlx."','".$version."','0','".$json."','1',1)"; 
        }else{
            
            $inseCon = "INSERT into cont_facturas(folio,uuid,er,tipo,serie,emisor,receptor,importe,moneda,rfc,fecha,fecha_subida,xml,version,cancelada,json,temporal,origen) values('".$azu['Basicos']['Folio']."','".$UUID."','E','Ingreso','".$azu['Basicos']['Serie']."','".$azu['Emisor']['Nombre']."','".$azu['Receptor']['Nombre']."','".$azu['Basicos']['Total']."','".$azu['Basicos']['Moneda']."','".$azu['Receptor']['Rfc']."','".str_replace('T', ' ', $azu['Basicos']['Fecha'])."','".$fechaactual."','".$xmlx."','".$version."','0','".$json."','1',1)"; 
        }

        $this->queryArray($inseCon);
        
        return $insertedId;
    }

    public function datosorganizacion(){
        $selectOrg = "SELECT * from organizaciones c left join estados e on e.idestado=c.idestado left join municipios m on m.idmunicipio=c.idmunicipio where idorganizacion=1";
        $resultSelect = $this->queryArray($selectOrg);
        return $resultSelect['rows'];
    }

    public function datosventa($idventa){
        $selectVenta = "SELECT 
        v.idVenta as folio,
        v.fecha as fecha, 
        v.cambio as cambio,
        v.impuestos as jsonImpuestos,
        v.descuentoGeneral as descuento,
        CASE WHEN c.nombre IS NOT NULL 
               THEN c.nombre
               ELSE 'Publico general'
        END AS cliente,
        v.idCliente as idCliente,
        c.email emailCliente,
        e.nombre as empleado,
        s.nombre as sucursal,
        CASE WHEN v.estatus =1 
               THEN 'Activa'
               ELSE 'Cancelada'
        END AS estatus,
        v.montoimpuestos as impuestos,
        (v.monto) as monto,
        m.description,
        m.codigo,
        v.folio_recibo as recibo,
        v.tipo_cambio 
         from app_pos_venta v left join comun_cliente c on c.id=v.idCliente left join cont_coin m on m.coin_id=v.moneda inner join  empleados e on e.idempleado=v.idEmpleado inner join mrp_sucursal s on s.idSuc=v.idSucursal 
         where v.idVenta=".$idventa;
        $resutl = $this->queryArray($selectVenta);
        return $resutl['rows'];
    }

    public function obtenerIdVenta($codigoTicket){
        $id = $codigoTicket;
        $yadecimal =substr($id,2);
        $desc = hexdec($yadecimal);
        $idventa = substr($desc,14);
        return $idventa;
    }

    public function verificaFacturacionValida($idVenta){
        //ECHO $idVenta.'X';
        $id = $idVenta;
        $idVenta = base64_decode($id);
        $clave = array();

        $clave["instancia"] = substr($id,0,2);

        $clave["anho"] = substr($idVenta,2,4);
        $clave["mes"] = substr($idVenta,6,2);
        $clave["dia"] = substr($idVenta,8,2);
        $clave["hora"] = substr($idVenta,10,2);
        $clave["minuto"] = substr($idVenta,12,2);
        $clave["segundo"] = substr($idVenta,14,2);
        $clave["venta"] = substr($idVenta,16);

        $yadecimal =substr($id,2);
        $desc = hexdec($yadecimal);

        $idVenta = substr($desc,14);
        // echo '['.$idVenta.']';
        //$idVenta = $this->obtenerIdVenta($idVenta);
        $selectVenta = "SELECT id FROM app_respuestaFacturacion WHERE idSale = " . $idVenta;
        //ECHO $selectVenta;
        $result = $this->queryArray($selectVenta);

        $validaDias = $this->revisaDias($idVenta);
        if($validaDias > 0){
            return "pasada";
        }

        if((int)$result["total"] > 0){
            return "facturada";
        }else{
            return "ok";
        } 
        
    }

    public function productosventa($idVenta){
        //$idVenta = 158;
        $selProd = "    SELECT 
                            IF(vp.comentario!='', CONCAT(p.nombre, vp.comentario), 
                                IF(f.descripcion!='', CONCAT(p.nombre, f.descripcion), p.nombre)) 
                            AS nombre, p.descripcion_corta,p.precio,
                            vp.idProducto as id, p.codigo, vp.preciounitario, vp.cantidad, vp.montodescuento, vp.total, 
                            vp.impuestosproductoventa, vp.comentario , vp.caracteristicas, vp.tipodescuento,vp.descuento, vp.idventa_producto, vp.series, COUNT(d.id) devoluciones
                        FROM 
                            app_pos_venta_producto vp 
                        LEFT JOIN 
                                app_productos p 
                            ON 
                                vp.idProducto=p.id
                        LEFT JOIN
                                app_campos_foodware f
                            ON
                                p.id=f.id_producto
                        LEFT JOIN 
                                app_devolucioncli_datos d
                            ON  vp.idventa_producto = d.id_producto
                        WHERE 
                            vp.idVenta=".$idVenta."
                        GROUP BY vp.idventa_producto";
        //print_r($selProd);                  
        $resSel = $this->queryArray($selProd);
        $caras = '';
        $seriesNombre = '';
        //print_r($resSel['rows']);
        //exit();
        foreach ($resSel['rows'] as $k => $v) {
           // echo '['.$k.']<br>';
            if($v['caracteristicas']!="'0'"){
                $caracteristicas2 =  explode("*", $v['caracteristicas']);
                foreach ($caracteristicas2 as $key => $value) {
                    $expv=explode('=>', $value);
                    $ip=$expv[0];
                    $ih=$expv[1];
                    $my = "SELECT concat('( ',a.nombre,': ',b.nombre,' )') as dcar FROM app_caracteristicas_padre a
                    LEFT JOIN app_caracteristicas_hija b on b.id=".$ih."
                    WHERE a.id=".$ip.";";
                    $producto = $this->queryArray($my);
                    //echo $producto['rows'][0]['dcar'].'<br>';
                    $caras.= $producto['rows'][0]['dcar'];
                }
                $resSel['rows'][$k]['nombre'] = $resSel['rows'][$k]['nombre'].$caras;
                if($resSel['rows'][$k]['descripcion_corta']!=''){
                    $resSel['rows'][$k]['descripcion_corta'] = $resSel['rows'][$k]['descripcion_corta'].$caras;
                }else{
                    $resSel['rows'][$k]['descripcion_corta'] = '';
                }
                
                $caras = '';
            } 
            //echo $v['series'];
            if($v['series']!=''){

                $v['series'] = explode(',',$v['series']);
                foreach ($v['series'] as $keySeries => $valueSeries) {
                    $selSerie = "SELECT serie from app_producto_serie where id=".$valueSeries;
                    $resSelSerie = $this->queryArray($selSerie);
                    //echo $resSelSerie['rows'][0]['serie'].'<br>';
                    if($resSelSerie['rows'][0]['serie']!=''){
                        $seriesNombre.=$resSelSerie['rows'][0]['serie'].',';
                    }
                } 
                $seriesNombre = '['.$seriesNombre.']';
                $resSel['rows'][$k]['nombre'] = $resSel['rows'][$k]['nombre'].$seriesNombre;
                if($resSel['rows'][$k]['descripcion_corta']!=''){
                    $resSel['rows'][$k]['descripcion_corta'] = $resSel['rows'][$k]['descripcion_corta'].$seriesNombre;
                }else{
                    $resSel['rows'][$k]['descripcion_corta'] = '';
                }
                
                
            } 
        }
        //echo $seriesNombre;
        //exit();


         //print_r($resSel['rows']);
        //echo $caras;
        return $resSel['rows'];
    }

    public function pagos($idVenta){
        $selectPagos = "SELECT vp.monto, fp.nombre from app_pos_venta_pagos vp inner join venta v on v.idVenta=vp.idVenta inner join forma_pago fp on vp.idFormapago=fp.idFormapago where v.idVenta=".$idVenta;
        $resPagos = $this->queryArray($selectPagos);
        return $resPagos['rows'];
    }

    public function estados(){
        $query = 'Select * from estados';
        $result = $this->queryArray($query);
        return $result['rows'];
    }

    public function municipios($idEstado){
        $queryM = "SELECT * from municipios where idestado=".$idEstado;
        $result = $this->queryArray($queryM);
        return $result['rows'];
    }
    
    public function munici(){
        $queryM = "SELECT * from municipios";
        $result = $this->queryArray($queryM);
        return $result['rows'];
    }

    public function descuentoGeneral($descuento){

    }

    public function verificaRfcmodal($rfc){
        if($this->validaRFC($rfc) == 1){
            $select = "SELECT f.id,f.nombre,f.rfc,f.razon_social,f.correo,f.pais,f.regimen_fiscal,f.domicilio,f.num_ext,f.cp,f.colonia,e.estado,f.ciudad,f.municipio from comun_facturacion f,estados e where f.rfc='".$rfc."' and f.estado=e.idestado";
            $resSelct = $this->queryArray($select);
            if($resSelct['total']>0){
                return array('estatus' => true ,'datosFac' => $resSelct['rows']);
            }else{
                return array('estatus' => false );
            }
        }else{
            return array('rfc_no_valido' => true);
        }
    }

    public function datosFacturacionCliente($idFact){
      /*$datosFacturacion = "SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
            e.estado estado,ciudad,municipio,regimen_fiscal,cf.estado as idEstado
            from comun_facturacion cf left join estados e on  e.idestado=cf.estado
            where  id=" . $idFact; */
        $datosFacturacion ="SELECT nombre, domicilio,cp,colonia,num_ext,pais,correo,razon_social,rfc,cf.id as idFac,
            e.estado estado,ciudad,cf.municipio,regimen_fiscal,cf.estado as idEstado, m.idmunicipio as idMunicipio
            from comun_facturacion cf left join estados e on  e.idestado=cf.estado
            left join municipios m on cf.municipio=m.municipio
            where  id=".$idFact;   

            $result = $this->queryArray($datosFacturacion);
         
        return array("Datafact" => $result['rows']);
    }

    public function updateDatosFac($idFac,$rfc,$razSoc,$email,$pais,$regimen,$domicilio,$numero,$cp,$col,$estado,$municipio,$ciudad){
        if($this->validaRFC($rfc) == 1){
            $selcMuni = "SELECT * from municipios where idmunicipio=".$municipio;
            $resmunici = $this->queryArray($selcMuni);
            $municipioNombre = $resmunici['rows'][0]['municipio'];

            $update = "UPDATE comun_facturacion set rfc='".$rfc."', razon_social='".$razSoc."', correo='".$email."', pais='".$pais."', regimen_fiscal='".$regimen."', domicilio='".$domicilio."', num_ext='".$numero."', cp='".$cp."', colonia='".$col."', estado='".$estado."', ciudad='".$ciudad."', municipio='".$municipioNombre."' where id=".$idFac;

            $res = $this->queryArray($update);

            return  array('estatus' => true , 'Datos' => $res['rows'] );
        }else{
            return array('rfc_no_valido' => true);
        }

    }

    public function oneFact($idComunFactu,$idVenta,$usoCfdi){
        $id = $idVenta;
        $idVenta = base64_decode($id);
        $clave = array();

        $clave["instancia"] = substr($id,0,2);

        $clave["anho"] = substr($idVenta,2,4);
        $clave["mes"] = substr($idVenta,6,2);
        $clave["dia"] = substr($idVenta,8,2);
        $clave["hora"] = substr($idVenta,10,2);
        $clave["minuto"] = substr($idVenta,12,2);
        $clave["segundo"] = substr($idVenta,14,2);
        $clave["venta"] = substr($idVenta,16);

        $yadecimal =substr($id,2);
        $desc = hexdec($yadecimal);

        $idVenta = substr($desc,14);

        /////Si es a credito y existe
        $sql = "SELECT    p.idFormapago, p.monto, v.monto montoV
                FROM    app_pos_venta v
                LEFT JOIN app_pos_venta_pagos p ON v.idVenta = p.idVenta
                WHERE v.idVenta = '$idVenta'; ";
                //echo $sql;
        $formasPago = $this->queryArray($sql);

        $pagoTotal = 0;
        $pagoConCredito = 0;
        $otrasFormasPago = 0;
        foreach ($formasPago['rows'] as $key => $value) {
            $pagoTotal += (float) $value['monto'];
            if($value['idFormapago'] == 6)
                $pagoConCredito += (float) $value['monto'];
            else
                $otrasFormasPago += (float) $value['monto'];
        }
        if( $pagoConCredito != 0 ){

            $sql = "SELECT  CASE WHEN (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) IS NULL THEN cargo 
        ELSE (cargo - (SELECT SUM(abono) FROM app_pagos_relacion WHERE id_documento = c.id ) ) END restante
                                FROM    app_pagos  p
                                RIGHT JOIN (
                                    select id
                                    FROM    app_pagos
                                    WHERE substring_index( substring_index(concepto,':',1) , ' ' , -1) = '$idVenta'
                                ) c ON p.id = c.id";

            $creditoPendiente = $this->queryArray($sql);

            $this->saldarCuenta( $idVenta, ($creditoPendiente['rows'][0]['restante'] ? $creditoPendiente['rows'][0]['restante'] : 0) );

        }




        require_once('../webapp/modulos/SAT/config.php');
        date_default_timezone_set("Mexico/General");
        $fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-10 minute"));

        $SeleCad = "SELECT cadenaOriginal,factNum FROM app_pendienteFactura WHERE id_sale=".$idVenta;
        $cadenaOri = $this->queryArray($SeleCad);
          ///recuperar si tiene trackid
        $rastreo = $cadenaOri['rows'][0]['factNum'];
        if($rastreo!=0){
           $consultaCFDI = 1;
        }
        //echo $cadenaOri['rows'][0]['cadenaOriginal'];
        $azurian=base64_decode($cadenaOri['rows'][0]['cadenaOriginal']);

        $azurian = str_replace("\\", "", $azurian);
        if($azurian!=''){ 
            $azurian=json_decode($azurian); 
        }
        $azurian = $this->object_to_array($azurian);


        if (isset($azurian['Basicos']['version'])){
            //echo '3.2';
            $version = '3.2';
        }else{
            $azurian['Receptor']['UsoCFDI'] = $usoCfdi;
            $this->pendienteFactura33($azurian,$idComunFactu,$idVenta);
            exit();
        }
        //////SELECCIONA PAC
        $qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
        $respac = $this->queryArray($qrpac);
        $pac = $respac["rows"][0]["pac"];

            $queryConfiguracion = "SELECT a.*, b.regimen as regimenf FROM pvt_configura_facturacion a INNER JOIN pvt_catalogo_regimen b WHERE a.id=1 AND b.id=a.regimen;";
            $returnConfiguracion = $this->queryArray($queryConfiguracion);
            if ($returnConfiguracion["total"] > 0) {
                $r = (object) $returnConfiguracion["rows"][0];

                /* DATOS OBLIGATORIOS DEL EMISOR
                ================================================================== */
                $rfc_cliente = $r->rfc;

                $parametros['EmisorTimbre'] = array();
                $parametros['EmisorTimbre']['RFC'] = $r->rfc;
                $parametros['EmisorTimbre']['RegimenFiscal'] = $r->regimenf;
                $parametros['EmisorTimbre']['Pais'] = $r->pais;
                $parametros['EmisorTimbre']['RazonSocial'] = $r->razon_social;
                $parametros['EmisorTimbre']['Calle'] = $r->calle;
                $parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
                $parametros['EmisorTimbre']['Colonia'] = $r->colonia;
                $parametros['EmisorTimbre']['Ciudad'] = $r->ciudad; //Ciudad o Localidad
                $parametros['EmisorTimbre']['Municipio'] = $r->municipio;
                $parametros['EmisorTimbre']['Estado'] = $r->estado;
                $parametros['EmisorTimbre']['CP'] = $r->cp;
                $cer_cliente = $pathdc . '/' . $r->cer;
                $key_cliente = $pathdc . '/' . $r->llave;
                $pwd_cliente = $r->clave;
            } else {

                $JSON = array('success' => 0,
                    'error' => 1001,
                    'mensaje' => 'No existen datos de emisor.');
                echo json_encode($JSON);
                exit();
            }

        /* Datos Receptor
        ============================================================== */
        if($idComunFactu!=''){

            $selComFac = "SELECT * FROM comun_facturacion WHERE id=".$idComunFactu;
            $result = $this->queryArray($selComFac);
            //Estado
            $selEstado ="SELECT estado from estados where idestado=".$result['rows'][0]['estado'];
            $resultEstado = $this->queryArray($selEstado);
            

            $idCliente=$rs{'nombre'};
            $azurian['Receptor']['rfc']=strtoupper($result['rows'][0]['rfc']);
            $azurian['Receptor']['nombre']=strtoupper($result['rows'][0]['razon_social']);
            $azurian['DomicilioReceptor']['calle']=$result['rows'][0]['domicilio'];
            $azurian['DomicilioReceptor']['noExterior']=$result['rows'][0]['num_ext'];
            $azurian['DomicilioReceptor']['colonia']=$result['rows'][0]['colonia'];
            $azurian['DomicilioReceptor']['localidad']=$result['rows'][0]['ciudad'];
            $azurian['DomicilioReceptor']['municipio']=$result['rows'][0]['municipio'];
            $azurian['DomicilioReceptor']['estado']=$resultEstado['rows'][0]['estado'];
            $azurian['DomicilioReceptor']['pais']=$result['rows'][0]['pais'];
            $azurian['DomicilioReceptor']['codigoPostal']=$result['rows'][0]['cp'];
            $azurian['Correo']['Correo'] = $result['rows'][0]['correo'];
            
        }else{
            $idCliente='';
            $azurian['Receptor']['rfc']='XAXX010101000';
            $azurian['Receptor']['nombre']='Factura generica';
            $azurian['DomicilioReceptor']['calle']='';
            $azurian['DomicilioReceptor']['noExterior']='';
            $azurian['DomicilioReceptor']['colonia']='';
            $azurian['DomicilioReceptor']['localidad']='';
            $azurian['DomicilioReceptor']['municipio']='';
            $azurian['DomicilioReceptor']['estado']='';
            $azurian['DomicilioReceptor']['pais']='';
            $azurian['DomicilioReceptor']['codigoPostal']='';
            $azurian['Correo']['Correo'] = '';
        }       


        $serFol = "SELECT * FROM pvt_serie_folio WHERE id=1";
        $rs3 = $this->queryArray($serFol);

        $selecLogo = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
        $rs4 = $this->queryArray($selecLogo);

        $azurian['org']['logo'] = $rs4['rows'][0]['logoempresa'];

        /* Moneda y Tipo de cambio */
        $azurian['Basicos']['TipoCambio'] = 1.00;
        $azurian['Basicos']['Moneda'] = 'MXN';
        $azurian['Observacion'] = '';

        /* Datos serie y folio
        ============================================================== */
        $azurian['Basicos']['serie']=$rs3['rows'][0]['serie']; //No obligatorio
        $azurian['Basicos']['folio']=$rs3['rows'][0]['folio'];

        /* Datos Emisor
        ============================================================== */
        $azurian['Emisor']['rfc']=strtoupper($parametros['EmisorTimbre']['RFC']);
        $azurian['Emisor']['nombre']=strtoupper($parametros['EmisorTimbre']['RazonSocial']);
        /* Datos Fiscales Emisor
        ============================================================== */
        $azurian['FiscalesEmisor']['calle']=$parametros['EmisorTimbre']['Calle'];
        $azurian['FiscalesEmisor']['noExterior']=$parametros['EmisorTimbre']['NumExt'];
        $azurian['FiscalesEmisor']['colonia']=$parametros['EmisorTimbre']['Colonia'];
        $azurian['FiscalesEmisor']['localidad']=$parametros['EmisorTimbre']['Ciudad'];
        $azurian['FiscalesEmisor']['municipio']=$parametros['EmisorTimbre']['Municipio'];
        $azurian['FiscalesEmisor']['estado']=$parametros['EmisorTimbre']['Estado'];
        $azurian['FiscalesEmisor']['pais']=$parametros['EmisorTimbre']['Pais'];
        $azurian['FiscalesEmisor']['codigoPostal']=$parametros['EmisorTimbre']['CP'];

        /* Datos Regimen
        ============================================================== */
        $azurian['Regimen']['Regimen']=$parametros['EmisorTimbre']['RegimenFiscal'];

        /* Fecha Factura
        ============================================================== */
        $azurian['Basicos']['fecha']=$fecha;
       

        /* Impuestos
        ============================================================== */
        $tisr=$azurian['Impuestos']['totalImpuestosRetenidos'];
        $tiva=$azurian['Impuestos']['totalImpuestosTrasladados'];
        $tieps=$azurian['Impuestos']['totalImpuestosIeps']; 

        if($pagoConCredito > 0){
            $azurian['Basicos']['MetodoPago'] = 'PPD';
        }
        //Obteniendo la descripcion de la forma de pago
        $formapago = "";

        $queryFormaPago = " SELECT nombre,referencia,claveSat from app_pos_venta_pagos vp inner join forma_pago fp on vp.idFormapago = fp.idFormapago where vp.idVenta=" . $idVenta;

        $resultqueryFormaPago = $this->queryArray($queryFormaPago);

        foreach ($resultqueryFormaPago["rows"] as $key => $pagosValue) {
            if (strlen($pagosValue["referencia"]) > 0) {
                //$formapago .= $pagosValue['claveSat'] . " Ref:" . $pagosValue['referencia'] . ",";
               // $formapago .= $pagosValue['claveSat'] . ",";
                $formapago .= $pagosValue['claveSat'].",";
                $refFormaPago = $pagosValue['referencia'];
            } else {
                $formapago .= $pagosValue['claveSat'] . ",";
                $refFormaPago = '';
            }
        }

        $formapago = substr($formapago, 0, strlen($formapago) - 1);

        if ($formapago == "") {
            $formapago = ".";
        }

        $azurian['Basicos']['metodoDePago'] = $formapago;
        $azurian['Basicos']['NumCtaPago'] = $refFormaPago;
    //    $positionPath='../../webapp/modulos';

        
        if($pac==2){
            require_once('../webapp/modulos/SAT/funcionesSAT2.php');
        }else if($pac==1){
            require_once('../webapp/modulos/lib/nusoap.php');
            require_once('../webapp/modulos/SAT/funcionesSAT.php');  
        }         
        //require_once('../webapp/modulos/lib/nusoap.php');
        //require_once('../webapp/modulos/SAT/funcionesSAT.php');      

    }

    public function newClientDatfact($idFac,$rfc,$razSoc,$email,$pais,$regimen,$domicilio,$numero,$cp,$col,$estado,$municipio,$ciudad){
        if($this->validaRFC($rfc) == 1){
            $queryCliente = "INSERT INTO comun_cliente (nombre,direccion,colonia,email,cp,idEstado,idMunicipio,rfc) values ";
            $queryCliente .="('".$razSoc."','".$domicilio.' '.$numero."','".$col."','".$email."','".$cp."','".$estado."','".$municipio."','".$rfc."')";
            $insertClienteRes = $this->queryArray($queryCliente);
            $idClienteInsert = $insertClienteRes['insertId'];



            $selcMuni = "SELECT * from municipios where idmunicipio=".$municipio;
            $resmunici = $this->queryArray($selcMuni);
            $municipioNombre = $resmunici['rows'][0]['municipio'];

            $insertCo = "INSERT into comun_facturacion(nombre,rfc,razon_social,correo,pais,regimen_fiscal,domicilio,num_ext,cp,colonia,estado,ciudad,municipio) values('".$idClienteInsert."','".$rfc."','".$razSoc."','".$email."','".$pais."','".$regimen."','".$domicilio."','".$numero."','".$cp."','".$col."','".$estado."','".$ciudad."','".$municipioNombre."')";
            $resInsert = $this->queryArray($insertCo);
            
            if(is_numeric($resInsert['insertId'])){
                return  array('estatus' => true );
            }else{
                return  array('estatus' => false );
            }
        }else{
            return array('rfc_no_valido' => true);
        }
    }
    public function guardaTIDPe($trackId,$id){
        //$id = $idVenta;
        $idVenta = base64_decode($id);
        $clave = array();

        $clave["instancia"] = substr($id,0,2);

        $clave["anho"] = substr($idVenta,2,4);
        $clave["mes"] = substr($idVenta,6,2);
        $clave["dia"] = substr($idVenta,8,2);
        $clave["hora"] = substr($idVenta,10,2);
        $clave["minuto"] = substr($idVenta,12,2);
        $clave["segundo"] = substr($idVenta,14,2);
        $clave["venta"] = substr($idVenta,16);

        $yadecimal =substr($id,2);
        $desc = hexdec($yadecimal);

        $id = substr($desc,14);
        $upd = "UPDATE app_pendienteFactura set factNum='".$trackId."' where id_sale=".$id;
        $res = $this->queryArray($upd);

        return 1;
    }

    public function revisaDias($idVenta){
        date_default_timezone_set("Mexico/General");
        $myQuery = " SELECT v.fecha, c.factura_emision from app_pos_venta v
                    inner join app_configuracion c on c.id=1
                     where v.idVenta=".$idVenta;
                    
        $resultque = $this->queryArray($myQuery);
        $fechafactura =  $resultque['rows'][0]['fecha'];
        $diasCancelacion =  $resultque['rows'][0]['factura_emision'];
        $fechaActual = date('Y-m-d');

        if($diasCancelacion==0){
            $date1 = new DateTime($fechafactura);
            $date2 = new DateTime($fechaActual);
            if ($date1->format('Y-m') === $date2->format('Y-m')) {
                return -1;
            }else{
                return 1;
            }
        }else{
            $fechaMaximaCancelar = strtotime ( '+'.$diasCancelacion.' day' , strtotime ( $fechafactura ) ) ;
            $fechaMaximaCancelar = date ( 'Y-m-d' , $fechaMaximaCancelar );

            $datetime1 = new DateTime($fechafactura);
            $datetime2 = new DateTime($fechaMaximaCancelar);
            $datetime3 = new DateTime($fechaActual);

            $interval = $datetime2->diff($datetime3);
            $tienedias = $interval->format('%R%a');
            return $tienedias*1;
        }
        
        
    }
    public function validaRFC($rfc){
        if (preg_match('/[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?/', $rfc)) {
            return 1;
        }else{
            return 0;
        }
    }
    public function pendienteFactura33($azurian, $idComunFactu, $obser,$seriex){
        //echo 'dejoekdoked';
        date_default_timezone_set("Mexico/General");
        $fecha=date('Y-m-d').'T'.date('H:i:s',strtotime("-10 minute"));

            ////Busca el pack para facturar
            $qrpac = "SELECT pac FROM pvt_configura_facturacion WHERE id=1;";
            $respac = $this->queryArray($qrpac);
            $pac = $respac["rows"][0]["pac"];


            $queryConfiguracion = "SELECT a.*, b.c_regimenfiscal as regimenf FROM pvt_configura_facturacion a INNER JOIN c_regimenfiscal b WHERE a.id=1 AND b.id=a.regimen;";
            $returnConfiguracion = $this->queryArray($queryConfiguracion);
            if ($returnConfiguracion["total"] > 0) {
                $r = (object) $returnConfiguracion["rows"][0];

                /* DATOS OBLIGATORIOS DEL EMISOR
                ================================================================== */
                $rfc_cliente = $r->rfc;

                $parametros['EmisorTimbre'] = array();
                $parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
                $parametros['EmisorTimbre']['RFC'] = utf8_decode($r->rfc);
                $parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
                /*$parametros['EmisorTimbre']['RegimenFiscal'] = utf8_decode($r->regimenf);
                $parametros['EmisorTimbre']['Pais'] = utf8_decode($r->pais);
                $parametros['EmisorTimbre']['RazonSocial'] = utf8_decode($r->razon_social);
                $parametros['EmisorTimbre']['Calle'] = utf8_decode($r->calle);
                $parametros['EmisorTimbre']['NumExt'] = $r->num_ext;
                $parametros['EmisorTimbre']['Colonia'] = utf8_decode($r->colonia);
                $parametros['EmisorTimbre']['Ciudad'] = utf8_decode($r->ciudad); //Ciudad o Localidad
                $parametros['EmisorTimbre']['Municipio'] = utf8_decode($r->municipio);
                $parametros['EmisorTimbre']['Estado'] = utf8_decode($r->estado);
                $parametros['EmisorTimbre']['CP'] = $r->cp; */
                //$pathdc = '../../..';
                //../webapp/modulos/SAT/funcionesSAT33
                $pathdc = '../webapp/modulos/SAT/cliente';
                $cer_cliente = $pathdc . '/' . $r->cer;
                $key_cliente = $pathdc . '/' . $r->llave;
                $pwd_cliente = $r->clave;
                $positionPath="../webapp/modulos";
                $kiosko = '1';
            } else {

                $JSON = array('success' => 0,
                    'error' => 1001,
                    'mensaje' => 'No existen datos de emisor.');
                echo json_encode($JSON);
                exit();
            }
            
                    /* Datos Receptor
        ============================================================== */
        /*if($cliente>0){
          //$result = $this->conexion->consultar("SELECT * FROM comun_facturacion WHERE id='$rrfc';");
          $result = "SELECT c.nombre,c.id, c.rfc, c.razon_social, c.correo, c.pais, c.regimen_fiscal, c.domicilio, c.num_ext, c.cp, c.colonia, e.estado, c.ciudad, c.municipio from comun_facturacion c , estados e WHERE e.idestado=c.estado and id='".$cliente."'";
          $rs = $this->queryArray($result);
          //print_r($rs);
          $idCliente=$rs['rows'][0]['nombre'];
          $azurian['Receptor']['Rfc']=strtoupper($rs['rows'][0]['rfc']);
          $azurian['Receptor']['Nombre']=strtoupper($rs['rows'][0]['razon_social']);
          $azurian['Receptor']['UsoCFDI'] = 'G01';

        }else{
          $idCliente='';
          $azurian['Receptor']['Rfc']='XAXX010101000';
          $azurian['Receptor']['Nombre']='Factura generica';
          $azurian['Receptor']['UsoCFDI'] = 'G01';
        } */
        if($idComunFactu!=''){

            $selComFac = "SELECT * FROM comun_facturacion WHERE id=".$idComunFactu;
            $result = $this->queryArray($selComFac);
            //Estado
            $selEstado ="SELECT estado from estados where idestado=".$result['rows'][0]['estado'];
            $resultEstado = $this->queryArray($selEstado);
            

            $idCliente=$rs{'nombre'};
            $azurian['Receptor']['Rfc']=strtoupper($result['rows'][0]['rfc']);
            $azurian['Receptor']['Nombre']=strtoupper($result['rows'][0]['razon_social']);
            //$azurian['Receptor']['UsoCFDI'] = 'G01';

     
            $azurian['Correo']['Correo'] = $result['rows'][0]['correo'];
            
        }else{
            $idCliente='';
            $azurian['Receptor']['Rfc']='XAXX010101000';
            $azurian['Receptor']['Nombre']='Factura generica';
            $azurian['Receptor']['UsoCFDI'] = 'G01';
            $azurian['Correo']['Correo'] = 'ovazquez@netwarmonitor.com';
        } 

        /* --- Configuracion de las series  ---*/
        $serFol = "SELECT * FROM pvt_serie_folio WHERE id=1";
        $rs3 = $this->queryArray($serFol);

        $result4 = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
        $rs4 = $this->queryArray($result4);

        $azurian['org']['logo']        = $rs4['rows'][0]['logoempresa'];
        /* Datos serie y folio
        ============================================================== */
        $azurian['Basicos']['Serie']=$rs3['rows'][0]['serie']; //No obligatorio
        $azurian['Basicos']['Folio']=$rs3['rows'][0]['folio'];

        /* Datos Emisor
        ============================================================== */
        $azurian['Emisor']['RegimenFiscal'] = $parametros['EmisorTimbre']['RegimenFiscal'];
        $azurian['Emisor']['Nombre']=strtoupper($parametros['EmisorTimbre']['RazonSocial']);
        $azurian['Emisor']['Rfc']=strtoupper($parametros['EmisorTimbre']['RFC']);

        /* Fecha Factura
        ============================================================== */
        $azurian['Basicos']['Fecha']=$fecha;

        /* Impuestos
        ============================================================== */
        $tisr=$azurian['Impuestos']['totalImpuestosRetenidos'];
        $tiva=$azurian['Impuestos']['totalImpuestosTrasladados'];
        $tieps=$azurian['Impuestos']['totalImpuestosIeps'];


        //echo '('.$azurian['Basicos']['MetodoPago'] .')/'.$azurian['Basicos']['FormaPago'];
        if($azurian['Basicos']['FormaPago']=='99'){
            $azurian['Basicos']['MetodoPago'] = 'PPD';
            //echo '('.$azurian['Basicos']['MetodoPago'] .')';
        }
        //echo '('.$azurian['Basicos']['MetodoPago'] .')';

        //print_r($azurian);
        //exit();
        $soloExento = 1;

        if($pac==2){
            require_once('../webapp/modulos/SAT/funcionesSAT33.php');
        }else if($pac==1){
            require_once('../webapp/modulos/lib/nusoap.php');
            require_once('../webapp/modulos/SAT/funcionesSAT33.php');  
        }  

        
    }

    public function datosSucursal($idVenta){

        $sel1 = "SELECT idSucursal from app_pos_venta where idVenta=".$idVenta;
        $res1 = $this->queryArray($sel1);
        $idSuc = $res1['rows'][0]['idSucursal'];

        $select = "SELECT * from mrp_sucursal s left join estados e on e.idestado=s.idEstado left join municipios m on m.idmunicipio=s.idMunicipio  where idSuc=".$idSuc;
        $res2 = $this->queryArray($select);
       

        return $res2['rows'];
    }
    public function configTikcet(){
            $configTikcet = "SELECT ticket_config FROM pvt_configura_facturacion WHERE id=1";
            $res = $this->queryArray($configTikcet);    
            return $res['rows'][0]['ticket_config'];
    }
    function obtenerLeyenda() {
        
        $sql = "SELECT  leyenda_ticket
                FROM    app_config_ventas
                WHERE   id=1";
        $res = $this->queryArray($sql);
        return $res['rows'][0]['leyenda_ticket'];
    }
    function obtenerConfigVenta() {
        
        $sql = "SELECT  *
                FROM    app_config_ventas
                WHERE   id=1";
        $res = $this->queryArray($sql);
        return $res['rows'][0];
    }
    function listar_ajustes_foodware($objet) {
        $sql = "SELECT 
                    * 
                FROM 
                    com_configuracion";
        $result = $this -> queryArray($sql);

        return $result;
    }
    public function usoCfdi(){
        $selectOrg = "SELECT * from c_usocfdi";
        $resultSelect = $this->queryArray($selectOrg);
        return array('usos' => $resultSelect['rows']);
    }
    function listar_detalles_comanda($objeto){
        $sql = "SELECT
                    m.nombre AS nombre_mesa, m.domicilio, cli.celular AS tel, u.usuario AS nombre_mesero
                FROM
                    com_comandas c
                LEFT JOIN
                        com_mesas m
                    ON
                        m.id_mesa = c.idmesa
                LEFT JOIN
                        comun_cliente cli
                    ON
                        cli.nombre = m.nombre
                LEFT JOIN
                         accelog_usuarios u
                    ON
                        c.idempleado = u.idempleado
                WHERE
                    c.id_venta = ".$objeto['id_venta'];
        // return $sql;
        $result = $this->queryArray($sql);
        
        return $result;
    }
        public function saldarCuenta( $idVenta, $creditoPendiente ){

        $sql = "SELECT  p.*
                FROM    app_pagos  p
                RIGHT JOIN (
                    select id
                    FROM    app_pagos
                    WHERE substring_index( substring_index(concepto,':',1) , ' ' , -1) = '$idVenta'
                ) c ON p.id = c.id";
        $datosCxC = $this->queryArray($sql);
        
        $tmpConcepto = $datosCxC['rows'][0]['concepto'];
        $tmpConcepto = explode( ":" , $tmpConcepto );
        $datosCxC['rows'][0]['concepto'] = $tmpConcepto[0] .":". $tmpConcepto[1];

        $sql = "UPDATE app_pagos
                    SET concepto = '{$datosCxC['rows'][0]['concepto']}'
                    WHERE id = '{$datosCxC['rows'][0]['id']}' ;";
        $this->queryArray($sql);
        $sql = "INSERT INTO app_pagos (cobrar_pagar, id_prov_cli, cargo, abono, fecha_pago, concepto, id_forma_pago, id_moneda, tipo_cambio, origen)
                VALUES ({$datosCxC['rows'][0]['cobrar_pagar']} , {$datosCxC['rows'][0]['id_prov_cli']} , '0' , {$creditoPendiente} , NOW() ,'{$datosCxC['rows'][0]['concepto']}' ,{$datosCxC['rows'][0]['id_forma_pago']} , {$datosCxC['rows'][0]['id_moneda']} , {$datosCxC['rows'][0]['tipo_cambio']} , {$datosCxC['rows'][0]['origen']} );";
        $pagoCxC = $this->queryArray($sql);

        //require("../../appministra/models/cuentas.php");
        //$cuentasModel = new CuentasModel();
        $this->guardar_relacion( $pagoCxC['insertId'] , ($datosCxC['rows'][0]['id'].'@|@') ,'0',($creditoPendiente.'@|@'),'MXN@|@' , 'MXN');

    }

    function guardar_relacion($idpago,$idrelaciones,$tipo,$valores,$monedas,$monedaPago)
    {
        $fecha = $this->query("SELECT fecha_pago FROM app_pagos WHERE id = $idpago");
        $fecha = $fecha->fetch_object();

        $myQuery = '';
        $idrelaciones = explode("@|@",$idrelaciones);
        $valores = explode("@|@",$valores);
        $monedas = explode("@|@",$monedas);

        ///////////////////////ACONTIA///////////////////////////////
        ////////////////////////////////////////////////////////////
        $genera_poliza = 0; //Por default no genera poliza
        //Si tiene acontia y esta conectado
        $conexion_acontia = $this->query("SELECT conectar_acontia, pol_autorizacion FROM app_configuracion WHERE id = 1");
        $conexion_acontia = $conexion_acontia->fetch_assoc();

        if(intval($conexion_acontia['conectar_acontia']))
        {
            //Buscar si es una cuenta por pagar o por cobrar
            $cobrar_pagar = $this->query("SELECT * FROM app_pagos WHERE id = $idpago");
            $cobrar_pagar = $cobrar_pagar->fetch_assoc();

            if(intval($cobrar_pagar['cobrar_pagar']))
            {
                $idpol = 3;//Busca la poliza de cxp
                $concepto = "Pago CXP";
            }
            else
            {
                $idpol = 4;//Busca la poliza de cxc
                $concepto = "Cobro CXC";
            }


            //Busca si es poliza automatica
            $automatica = $this->query("SELECT* FROM app_tpl_polizas WHERE id = $idpol");
            $automatica = $automatica->fetch_assoc();

            //Si es automatica y se genera por documento
            if(intval($automatica['automatica']) && intval($automatica['poliza_por_mov']) == 1)
            {
                $fecha = explode('-',$cobrar_pagar['fecha_pago']);

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
                $numpol = $this->query("SELECT pp.numpol+1 FROM cont_polizas pp WHERE pp.idtipopoliza = ".$automatica['id_tipo_poliza']." AND pp.activo = 1 AND pp.idejercicio = $ejercicio AND pp.idperiodo = ".intval($fecha[1])." ORDER BY pp.numpol DESC LIMIT 1");
                $numpol = $numpol->fetch_assoc();
                $numpol = $numpol['numpol'];
                if(!intval($numpol))
                    $numpol = 1;
                $activo = 1;
                if(intval($conexion_acontia['pol_autorizacion']))
                    $activo = 0;


                $id_poliza_acontia = $this->insert_id("INSERT INTO cont_polizas(idorganizacion, idejercicio, idperiodo, numpol, idtipopoliza, concepto, fecha, fecha_creacion, activo, eliminado, pdv_aut, usuario_creacion, usuario_modificacion)
                 VALUES(1,$ejercicio,".intval($fecha[1]).",$numpol,".$automatica['id_tipo_poliza'].",'".$automatica['nombre_poliza']." ".$cobrar_pagar['concepto']."','".$cobrar_pagar['fecha_pago']."',DATE_SUB(NOW(), INTERVAL 6 HOUR), $activo, 0, 0, 0, 0)");
                $cont = 0;//Contador de movimientos
                $genera_poliza = 1;
            }
        }
        //Termina conexion con acontia
        ////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////

        $limite = count($idrelaciones)-2;
        for($i=0;$i<=$limite;$i++)
        {
            if($monedaPago != 'MXN')
                $valores[$i] = floatval($valores[$i]) / floatval($this->tipo_cambio_pago($idpago));

            $myQuery = "INSERT INTO app_pagos_relacion(id,id_pago,id_tipo,id_documento,cargo,abono) VALUES(0,$idpago,$tipo,".$idrelaciones[$i].",0,".$valores[$i]."); ";
            $id_pago_rel = $this->insert_id($myQuery);

            ///////////////////////ACONTIA///////////////////////////////
            ////////////////////////////////////////////////////////////
            if(intval($id_pago_rel) && $genera_poliza)
            {
                $cuentas_poliza = $this->query("SELECT id_cuenta, tipo_movto, id_dato FROM app_tpl_polizas_mov WHERE activo = 1 AND id_tpl_poliza = $idpol");
                while($cp = $cuentas_poliza->fetch_assoc())
                {
                    $cont++;
                    //Cargo o abono
                    if(intval($cp['tipo_movto']) == 1)
                        $tipo_movto = "Abono";
                    if(intval($cp['tipo_movto']) == 2)
                        $tipo_movto = "Cargo";

                    //dependiendo el tipo de dato sera el valor que tomara, en este caso solo existe el total del pago.
                    $importe = 0;
                    switch(intval($cp['id_dato']))
                    {
                        case 1 : $importe = $valores[$i]; break;
                        case 2 : $importe = $valores[$i]; break;
                        case 3 : $importe = 0; break;
                        case 4 : $importe = $valores[$i]; break;
                        case 5 : $importe = $valores[$i]; break;
                    }

                    //Si tiene cuenta de clientes busca si el id del cliente esta vinculado a una cuenta, si no es asi lo asignara a la cuenta configurada.
                    if(intval($cp['id_dato']) == 4 && intval($cobrar_pagar['cobrar_pagar']) == 0 && intval($cobrar_pagar['id_prov_cli']))
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



                    $id_mov = $this->insert_id("INSERT INTO cont_movimientos(IdPoliza, NumMovto, IdSegmento, IdSucursal, Cuenta, TipoMovto, Importe, Referencia, Concepto, Activo, FechaCreacion, FormaPago, tipocambio)
                                VALUES($id_poliza_acontia, $cont, 1, 1, ".$cp['id_cuenta'].", '$tipo_movto', $importe, '','$concepto Doc: $idrelaciones[$i]', $activo, DATE_SUB(NOW(), INTERVAL 6 HOUR), ".$cobrar_pagar['id_forma_pago'].", ".$cobrar_pagar['tipo_cambio'].")");
                    $ids_movs .= $id_mov.",";

                }
                $this->query("UPDATE app_pagos_relacion SET id_poliza_mov = '$ids_movs' WHERE id = $id_pago_rel");
                $ids_movs = '';
            }
            //Termina conexion con acontia
            ////////////////////////////////////////////////////////////
            ////////////////////////////////////////////////////////////
        }
        if(($limite+1) == $cont)
            return 1;
        else
            return 0;
        //return $this->dataTransact($myQuery);
    }

} ///fin de la clase
?>
