<?php
/*
 * Clase para que puedan utilizar los otros módulos del scm.
 * Automáticamente crea ya un registro de entrada.
 */

class clinventarios{
    /*Regresa Existencias Ventas*/
    function regresaexistenciaventas($idfabricante,$idmarca,$idproducto,$idloteproducto,$idestadoproducto,$idbodega,$conexion){
        $existencia=0;
        $sqlexistencias="select im.saldosecundario 'fisica',
                            (
                            select sum(oe.cantidad2)-sum(oe.cantidadretirada2) 'saldo2'
                                             from logistica_ordenesentrega oe
                            where 	oe.fechacancelacion is null and oe.idestadodocumento<>4 and
                                                    oe.idfabricante=im.idfabricante and oe.idmarca=im.idmarca and 			oe.idproducto=im.idproducto and oe.idestadoproducto=im.idestadoproducto and 			oe.idloteproducto=im.idloteproducto and oe.idbodega=im.idbodega
                            ) oe,
							(
                            select sum(loc.cantidad2) 'saldo2'
                                             from logistica_ordenesentrega oe
                                                  left join logistica_cancelacionordenesentrega loc on loc.idordenentrega=oe.idordenentrega
                            where   oe.idfabricante=im.idfabricante and oe.idmarca=im.idmarca and 			oe.idproducto=im.idproducto and oe.idestadoproducto=im.idestadoproducto and 			oe.idloteproducto=im.idloteproducto and oe.idbodega=im.idbodega
                            ) cpoe,
                            (
                            select ifnull(sum(ce.cantidad2),0) from logistica_certificados ce
                            where 	ce.fechacancelacion is null and
                                                    ce.idfabricante=im.idfabricante and ce.idmarca=im.idmarca and 			ce.idproducto=im.idproducto and ce.idestadoproducto=im.idestadoproducto and 			ce.idloteproducto=im.idloteproducto and ce.idbodega=im.idbodega
                            ) ce,
                            (
                            select ifnull(sum(oc.volumenorden),0) from ventas_ordenesdecompra oc
                            where 	(oc.idestadodocumento=1 or oc.idestadodocumento=2) and
                                                    oc.idfabricante=im.idfabricante and oc.idmarca=im.idmarca and 			oc.idproducto=im.idproducto and oc.idestadoproducto=im.idestadoproducto and 			oc.idloteproducto=im.idloteproducto and oc.idbodega=im.idbodega
                            ) oc,
							(
                            select sum(re.cantidad2)-sum(re.cantidadretirada2) 'saldo2'
                                             from logistica_reservadeproductos re
                            where 	re.fechacancelacion is null and
                                                    re.idfabricante=im.idfabricante and re.idmarca=im.idmarca and 			re.idproducto=im.idproducto and re.idestadoproducto=im.idestadoproducto and 			re.idloteproducto=im.idloteproducto and re.idbodega=im.idbodega
                            ) re,
							(
                            select ifnull(pv.cantidad2,0) 'saldo2'
                                             from logistica_provisionvirtualproducto pv
                            where 	pv.idfabricante=im.idfabricante and pv.idmarca=im.idmarca and 			pv.idproducto=im.idproducto and pv.idestadoproducto=im.idestadoproducto and 			pv.idloteproducto=im.idloteproducto and pv.idbodegaorigen=im.idbodega
                            ) pv
                        from inventarios_saldos im
                        where im.idfabricante=$idfabricante and im.idmarca=$idmarca and im.idproducto=$idproducto
                        and im.idloteproducto=$idloteproducto and im.idestadoproducto=$idestadoproducto and im.idbodega=$idbodega";

        $result = $conexion->consultar($sqlexistencias);
        while($rs = $conexion->siguiente($result)){
            $existencia = ($rs{"fisica"}+$rs{"pv"})-($rs{"oe"}+$rs{"ce"}+$rs{"re"}-$rs{"cpoe"});
        }
        $conexion->cerrar_consulta($result);
        return $existencia;
    }
    /*Regresa Existencias Logistica*/


    /* Documentación clase */
    function agregarmovimiento($tipomovimiento,$fabricante,$marca,$bodega,$producto,$lote,$estadoproducto,$cantidad,$cantidadsecundaria,$fecha,$doctoorigen,$foliodoctoorigen,$conexion){

        //Agrega Movimiento Detallado a Kardex
        $sql = "
				insert into inventarios_movimientos
				(idtipomovimiento, idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto, cantidad, cantidadsecundaria, fecha, doctoorigen, foliodoctoorigen)
				 values
				(".$tipomovimiento.",".$fabricante.",".$marca.",".$bodega.",".$producto.",".$lote.",".$estadoproducto.",".$cantidad.",".$cantidadsecundaria.",'".$fecha."','".$doctoorigen."',".$foliodoctoorigen.")";
        $conexion->consultar($sql);

        echo "<br><br>Linea 70 - clinventario:  $sql<br><br>";

        //Agrega Acumulado
                $efectoinventario=0;
                $entradas=0;
                $salidas=0;
                $saldo=0;
                $entradassecundario=0;
                $salidassecundario=0;
                $saldosecundario=0;

                $sQuery = "SELECT idtipomovimiento, efectoinventario FROM inventarios_tiposmovimiento i where idtipomovimiento=".$tipomovimiento;
					//c.nombrecliente, t.nombremovimiento, l.descripcionlote, e.descripcionestado, b.nombrebodega
		$result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
			$efectoinventario = $rs['efectoinventario'];
		}
		$conexion->cerrar_consulta($result);

                if($efectoinventario==-1){
                    $entradas=0;
                    $salidas=$cantidad;

                    $entradassecundario=0;
                    $salidassecundario=$cantidadsecundaria;
                }
                if($efectoinventario==1){
                    $entradas=$cantidad;
                    $salidas=0;

                    $entradassecundario=$cantidadsecundaria;
                    $salidassecundario=0;
                }

                $agregasaldo=1;
        //Verifica si existen registros en la tabla Inventarios_saldos
                $sQuery = "SELECT idfabricante FROM inventarios_saldos i where idfabricante=".$fabricante." And idbodega=".$bodega." And idproducto=".$producto.
                            " And idloteproducto=".$lote." And idestadoproducto=".$estadoproducto;

                $result = $conexion->consultar($sQuery);
		while($rs = $conexion->siguiente($result)){
			$agregasaldo = 0;
		}
		$conexion->cerrar_consulta($result);
                if($agregasaldo==1){
                    //Si no existe movimiento provio en saldos crea un registro
                    $saldo=$entradas-$salidas;
                    $saldosecundario=$entradassecundario-$salidassecundario;
                    $sql="Insert Into inventarios_saldos
                        (idfabricante, idmarca, idbodega, idproducto, idloteproducto, idestadoproducto, entradas, salidas, saldo, entradassecundario, salidassecundario, saldosecundario, fechaactualizacion, doctoorigen, foliodoctoorigen) Values
                        (".$fabricante.",".$marca.",".$bodega.",".$producto.",".$lote.",".$estadoproducto.",".$entradas.",".$salidas.",".$saldo.",".$entradassecundario.",".$salidassecundario.",".$saldosecundario.",'".$fecha."',".$doctoorigen.",".$foliodoctoorigen.")";
                }
                elseif ($agregasaldo==0){
                    //Si ya existe previo edita el actual
                    $sql="Update inventarios_saldos set
                                                        entradas=(entradas+".$entradas."), salidas=(salidas+".$salidas."), saldo=(saldo+(".$entradas."-".$salidas.")),
                                                        entradassecundario=(entradassecundario+".$entradassecundario."), salidassecundario=(salidassecundario+".$salidassecundario."), saldosecundario=(saldosecundario+(".$entradassecundario."-".$salidassecundario."))
                                        where idfabricante=".$fabricante." And idmarca=".$marca." And idbodega=".$bodega." And idproducto=".$producto.
                                        " And idloteproducto=".$lote." And idestadoproducto=".$estadoproducto;
                }
                //echo $sql;

                echo "<br><br>Linea 130 - clinventario:  $sql<br><br>";
                $conexion->consultar($sql);


    }

}

?>
