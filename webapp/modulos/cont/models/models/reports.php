<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/captpolizas.php"); // funciones de captpolizas

	class ReportsModel extends Connection
	{
		private $libromayor = "(SELECT
   m.Id AS Mov_Id,
   (case when (`a`.`account_code` like '1%') then 'Activo' when (`a`.`account_code` like '2%') then 'Pasivo' when (`a`.`account_code` like '3%') then 'Capital' when (`a`.`account_code` like '4%') then 'Resultados' end) AS `Clasificacion`,
   (case when (`a`.`account_code` like '1%') then 1 when (`a`.`account_code` like '2%') then 2 when (`a`.`account_code` like '3%') then 3 when (`a`.`account_code` like '4%') then 4 end) AS `Num_Clasif`,
   (select concat(`cont_accounts`.`description`,' (',`cont_accounts`.`manual_code`,')') FROM `cont_accounts` USE INDEX (accounts_account_idx) where (`cont_accounts`.`account_id` = `a`.`main_father`)) AS `Cuenta_de_Mayor`,
   ucase(`a`.`account_code`) AS `Code`,
   ucase(`a`.`manual_code`) AS `Manual_Code`,
   ucase(`n`.`description`) AS `Naturaleza`,
   `p`.`fecha` AS `Fecha`,
   ucase(`p`.`concepto`) AS `Poliza`,
   concat(`a`.`description`,' (',`a`.`manual_code`,')') AS `Cuenta`,
   if((`m`.`TipoMovto` = 'CARGO'),`m`.`Importe`,0) AS `Cargos`,
   if((`m`.`TipoMovto` = 'ABONO'),`m`.`Importe`,0) AS `Abonos`,
   'SALDOS INICIALES' AS `Flag`,
   `p`.`idperiodo` AS `idperiodo`,
   (select `b`.`manual_code` from `cont_accounts` `b` USE INDEX (accounts_account_idx) where (`b`.`account_id` = `a`.`main_father`)) AS `Codigo_Cuenta_de_Mayor` 
   from (((`cont_movimientos` `m` USE INDEX (movimientos_Idx) join `cont_accounts` `a` USE INDEX (accounts_account_idx) on((`m`.`Cuenta` = `a`.`account_id`))) join `cont_nature` `n` on((`a`.`account_nature` = `n`.`nature_id`))) join `cont_polizas` `p` USE INDEX (polizas_idx) on((`m`.`IdPoliza` = `p`.`id`)))
   where ((`m`.`Activo` = 1) and (`p`.`activo` = 1) and ((`p`.`idperiodo` < 13) or ((`p`.`idperiodo` = 13) and `a`.`account_id` in (select `cont_accounts`.`account_id` from `cont_accounts` USE INDEX (accounts_account_idx) where (`cont_accounts`.`main_father` = (select `cont_config`.`CuentaSaldos` from `cont_config`)))))))";

		function createTempGral()
		{
			$myQuery = "CREATE TEMPORARY TABLE cont_general_temp
			(SELECT p.idperiodo,e.NombreEjercicio,a.account_code AS CodigoCuenta, m.TipoMovto,m.Importe,a.account_nature AS Naturaleza FROM cont_movimientos m INNER JOIN cont_accounts a ON a.account_id = m.Cuenta INNER JOIN cont_polizas p ON p.id = m.IdPoliza INNER JOIN cont_ejercicios e ON e.Id = p.idejercicio WHERE m.Activo=1)";
			$this->query_temp($myQuery,1,0);
		}
		function mainAccounts()
		{
			$this->createTempGral();
			$myQuery = "SELECT* FROM cont_general_temp gt INNER JOIN cont_accounts a ON a.account_code = gt.CodigoCuenta WHERE gt.CodigoCuenta LIKE '1%' GROUP BY gt.CodigoCuenta";
			$allData = $this->query_temp($myQuery,0,1);
			return $allData;
		}

		function accountName($cuenta)
		{
			$myQuery = "SELECT description FROM cont_accounts WHERE account_code = '$cuenta'";
			$name = $this->query($myQuery);
			$name = $name->fetch_array();
			return $name['description'];
		}

		function BalanceGral($tipo)
		{
			switch($tipo)
			{
				case 'Activo': $like = '1%'; break;
				case 'Pasivo': $like = '2%'; break;
				case 'Resultados': $like = '4%'; break;
			}
			$this->createTempGral();
			$myQuery = "SELECT a.description,gt.Importe,gt.TipoMovto FROM cont_accounts a INNER JOIN cont_general_temp gt ON gt.CodigoCuenta = a.account_code WHERE account_code LIKE '$like'";
			$datos = $this->query_temp($myQuery,0,1);
			return $datos;	
		}

		function getAccounts()
		{
			$myQuery = "SELECT account_id, manual_code, description FROM cont_accounts 
			WHERE status=1 AND removed=0 AND affectable=1 AND account_id NOT IN (select father_account_id FROM cont_accounts WHERE removed=0)";
			$ListaCuentas = $this->query($myQuery);
			return $ListaCuentas;	
		}

		function getAccountsMayor()
		{
			$myQuery = "SELECT account_id, manual_code, description FROM cont_accounts 
			WHERE status=1 AND removed=0 AND affectable=0 AND main_account = 1";
			$ListaCuentas = $this->query($myQuery);
			return $ListaCuentas;	
		}

		function getData_movcuentas_despues($cuentas,$fecha_antes,$fecha_despues,$rango,$tipo,$saldo)
		{
			if($rango)
			{
				$myQuery = "SELECT manual_code FROM cont_accounts WHERE account_id=".$cuentas[0];
				$Inicial = $this->query($myQuery);
				$Inicial = $Inicial->fetch_array();
				
				$myQuery = "SELECT manual_code FROM cont_accounts WHERE account_id=".$cuentas[1];
				$Final = $this->query($myQuery);
				$Final = $Final->fetch_array();
				
				if(intval($tipo))
				{
					$where = "manual_code BETWEEN '".$Inicial['manual_code']."%' AND '".$Final['manual_code']."%' AND";
				}
				else
				{
					$where = "manual_code BETWEEN '".$Inicial['manual_code']."' AND '".$Final['manual_code']."' AND";
				}
				

			}
			else
			{
				$limite = count($cuentas)-1;
				if($cuentas[0] != 'todos')
				{
					$where = "(";
					for($i=0;$i<=$limite;$i++)
					{
						if(intval($tipo))
						{
							$where .= "main_father=".$cuentas[$i];
						}
						else
						{
							$where .= "account_id=".$cuentas[$i];
						}
						if($i != $limite)
						{
							$where.=" OR ";
						}
						else
						{
							$where.=") AND ";
						}
					}
				}
			}

			if($saldo)
			{
				$ss = '';
			}
			else
			{
				$ss = "AND p.idperiodo != 13";
			}
			$myQuery = "SELECT
a.main_father,
(SELECT CONCAT(manual_code,'/',description) FROM cont_accounts WHERE account_id = a.main_father) AS NombreMayor,
a.account_id,
p.fecha AS Fecha,
t.id AS ID_Tipo_Poliza,  
t.titulo AS Tipo_Poliza,
p.idperiodo AS Periodo, 
p.numpol AS Numero_Poliza, 
p.concepto AS Concepto_Poliza, 
m.NumMovto AS Numero_Movimiento,
(select nombre from cont_segmentos where idSuc=m.IdSegmento) AS Segmento, 
a.manual_code AS Codigo_Cuenta, 
a.description AS Descripcion_Cuenta,
@saldoInicial:= (
CASE a.account_nature WHEN 1 THEN 
IFNULL((SELECT SUM(mm.Importe) FROM cont_movimientos mm INNER JOIN cont_polizas pp ON pp.id = mm.IdPoliza WHERE mm.TipoMovto = 'Abono' AND mm.Cuenta = m.Cuenta AND mm.Activo=1 AND ((pp.fecha < p.fecha) OR (pp.id=p.id AND mm.NumMovto < m.NumMovto) OR (pp.fecha = p.fecha AND pp.numpol < p.numpol))),0)-
IFNULL((SELECT SUM(mm.Importe) FROM cont_movimientos mm INNER JOIN cont_polizas pp ON pp.id = mm.IdPoliza WHERE mm.TipoMovto = 'Cargo' AND mm.Cuenta = m.Cuenta AND mm.Activo=1 AND ((pp.fecha < p.fecha) OR (pp.id=p.id AND mm.NumMovto < m.NumMovto) OR (pp.fecha = p.fecha AND pp.numpol < p.numpol))),0)
WHEN 2 THEN 
IFNULL((SELECT SUM(mm.Importe) FROM cont_movimientos mm INNER JOIN cont_polizas pp ON pp.id = mm.IdPoliza WHERE mm.TipoMovto = 'Cargo' AND mm.Cuenta = m.Cuenta AND mm.Activo=1 AND ((pp.fecha < p.fecha) OR (pp.id=p.id AND mm.NumMovto < m.NumMovto) OR (pp.fecha = p.fecha AND pp.numpol < p.numpol))),0)-
IFNULL((SELECT SUM(mm.Importe) FROM cont_movimientos mm INNER JOIN cont_polizas pp ON pp.id = mm.IdPoliza WHERE mm.TipoMovto = 'Abono' AND mm.Cuenta = m.Cuenta AND mm.Activo=1 AND ((pp.fecha < p.fecha) OR (pp.id=p.id AND mm.NumMovto < m.NumMovto) OR (pp.fecha = p.fecha AND pp.numpol < p.numpol))),0) END
) AS SaldoAntes,
CASE m.TipoMovto WHEN  'Cargo'  THEN @cargos:= m.Importe ELSE @cargos:=0 END AS Cargos, 
CASE m.TipoMovto WHEN  'Abono'  THEN @abonos:=m.Importe ELSE @abonos:=0 END AS Abonos, 
CASE a.account_nature WHEN  1  THEN @saldoInicial + (@abonos-@cargos)
WHEN 2 THEN @saldoInicial + (@cargos-@abonos) END AS SaldoDespues,
m.Concepto AS Concepto_Movimiento,
m.Referencia AS Referencia_Movimiento
FROM
cont_movimientos m 
INNER JOIN cont_accounts a ON a.account_id = m.Cuenta
INNER JOIN cont_polizas p ON p.id = m.IdPoliza
INNER JOIN cont_tipos_poliza t ON t.id = p.idtipopoliza 
WHERE
 $where p.fecha <= '$fecha_despues' AND m.Activo=1 AND p.activo=1 AND a.removed=0 $ss ORDER BY main_father,a.account_id,p.fecha,p.numpol,m.NumMovto ";
 //echo $myQuery;
			$Resultados = $this->query($myQuery);
			return $Resultados;	

		}

		function Saldos($Cuenta,$Fecha,$tipo)
		{
			if($tipo == 'Antes')
			{
				$filtro = "p.fecha<'$Fecha'";
			}
			if($tipo == 'Despues')
			{
				$filtro = "p.fecha<='$Fecha'";
			}
			$myQuery = "SELECT 
			IFNULL(SUM(m.Importe),0) AS Suma
			FROM cont_movimientos m 
			INNER JOIN cont_polizas p ON p.id = m.idPoliza 
			WHERE m.Cuenta = $Cuenta AND $filtro AND m.tipoMovto='Cargo' AND p.activo=1 AND m.activo=1";
			$Cargos = $this->query($myQuery);
			$Cargos = $Cargos->fetch_array();


			$myQuery = "SELECT 
			IFNULL(SUM(m.Importe),0) AS Suma
			FROM cont_movimientos m 
			INNER JOIN cont_polizas p ON p.id = m.idPoliza 
			WHERE m.Cuenta = $Cuenta AND $filtro AND m.tipoMovto='Abono' AND p.activo=1 AND m.activo=1";
			$Abonos = $this->query($myQuery);
			$Abonos = $Abonos->fetch_array();

			$myQuery = "SELECT a.account_nature FROM cont_accounts a WHERE a.account_id=$Cuenta";
			$Naturaleza = $this->query($myQuery);
			$Naturaleza = $Naturaleza->fetch_array();	

			if($Naturaleza['account_nature'] == 1)
			{
				$Resultado = $Abonos['Suma'] - $Cargos['Suma'];
			}

			if($Naturaleza['account_nature'] == 2)
			{
				$Resultado = $Cargos['Suma'] - $Abonos['Suma'];
			}	

			return $Resultado;
		}

		function empresa()
		{
				$myQuery = "SELECT nombreorganizacion FROM organizaciones WHERE idorganizacion=1";
				$empresa = $this->query($myQuery);	
				$empresa = $empresa->fetch_array();
				return $empresa['nombreorganizacion'];
		}

		function generarConsultaBalanza($ejercicio,$periodo)
		{
			$myQuery = "SELECT DISTINCT b.Cuenta, 
b.Clasificacion, 
b.Cuenta_de_Mayor, 
b.Code, 
(select manual_code from cont_accounts where account_code = b.Code AND removed != 1) AS Manual_Code, 
@saldoInicial:= (
CASE b.Naturaleza WHEN 'ACREEDORA' THEN 
IFNULL((SELECT SUM(b2.Abonos) FROM cont_view_init_balance2 b2 WHERE b2.Code = b.Code AND b2.Fecha BETWEEN '2009-01-01' AND '".$ejercicio."-".$periodo."-01'),0)-
IFNULL((SELECT SUM(b2.Cargos) FROM cont_view_init_balance2 b2 WHERE b2.Code = b.Code AND b2.Fecha BETWEEN '2009-01-01' AND '".$ejercicio."-".$periodo."-01'),0)
WHEN 'DEUDORA' THEN 
IFNULL((SELECT SUM(b2.Cargos) FROM cont_view_init_balance2 b2 WHERE b2.Code = b.Code AND b2.Fecha BETWEEN '2009-01-01' AND '".$ejercicio."-".$periodo."-01'),0)-
IFNULL((SELECT SUM(b2.Abonos) FROM cont_view_init_balance2 b2 WHERE b2.Code = b.Code AND b2.Fecha BETWEEN '2009-01-01' AND '".$ejercicio."-".$periodo."-01'),0) END
) AS SaldoAntes,
@cargos:= IFNULL((SELECT SUM(b2.Cargos) FROM cont_view_init_balance2 b2 WHERE b2.Fecha BETWEEN '".$ejercicio."-".$periodo."-01' AND '".$ejercicio."-".$periodo."-31' AND b2.Code=b.Code),0) AS Cargos, 
@abonos:= IFNULL((SELECT SUM(b2.Abonos) FROM cont_view_init_balance2 b2 WHERE b2.Fecha BETWEEN '".$ejercicio."-".$periodo."-01' AND '".$ejercicio."-".$periodo."-31' AND b2.Code=b.Code),0) AS Abonos, 
CASE b.Naturaleza WHEN  'ACREEDORA'  THEN IFNULL(@saldoInicial + (@abonos-@cargos),0)
WHEN 'DEUDORA' THEN IFNULL(@saldoInicial + (@cargos-@abonos),0) END AS SaldoDespues

FROM cont_view_init_balance2 b
HAVING (SaldoAntes = 0 AND Cargos <> 0 AND Abonos <> 0) OR (SaldoAntes <> 0 AND Cargos = 0 AND Abonos = 0) OR (SaldoAntes <> 0 AND Cargos <> 0 AND Abonos <> 0)
ORDER BY Manual_Code,b.Clasificacion, b.Cuenta_de_Mayor, b.Cuenta";
			$consulta = $this->query($myQuery);	
			return $consulta;
		}
		function rfc()
		{
			$myQuery = "SELECT RFC FROM organizaciones";
			$rfc = $this->query($myQuery);	
			$rfc = $rfc->fetch_array();
			return $rfc['RFC'];
		}

		function ejercicios()
		{
			$myQuery = "SELECT Id, NombreEjercicio FROM cont_ejercicios";
			$ejercicios = $this->query($myQuery);	
			return $ejercicios;
		}

		function NombreEjercicio($ej)
		{
			$myQuery = "SELECT NombreEjercicio FROM cont_ejercicios WHERE Id=$ej";
			$ejercicio = $this->query($myQuery);	
			$ejercicio = $ejercicio->fetch_assoc();
			return $ejercicio['NombreEjercicio'];
		}

		function IdEjercicio($ej)
		{
			$myQuery = "SELECT Id FROM cont_ejercicios WHERE NombreEjercicio='$ej'";
			$ejercicio = $this->query($myQuery);	
			$ejercicio = $ejercicio->fetch_assoc();
			return $ejercicio['Id'];
		}
		
		function ejercicioActual()
		{
			$myQuery = "SELECT EjercicioActual FROM cont_config WHERE id=1";
			$ejercicioActual = $this->query($myQuery);	
			$ejercicioActual = $ejercicioActual->fetch_array();
			return $ejercicioActual['EjercicioActual'];
		}

		function numpol($num)
		{
			$myQuery = "SELECT CONCAT(idperiodo,'/',numpol) AS n FROM cont_polizas WHERE id=$num AND activo=1";
			$numpol = $this->query($myQuery);	
			$numpol = $numpol->fetch_array();
			return $numpol['n'];
		}
		function generarConsultaCatalogo()
		{
			$myQuery = "SELECT 
o.codigo_agrupador AS CA, 
a.account_code, 
a.manual_code, 
a.description, 
a.account_type,
(SELECT manual_code FROM cont_accounts WHERE account_id = a.father_account_id) AS CuentaDe, 
(CASE a.account_nature WHEN 1 THEN 'A' ELSE 'D' END) AS Naturaleza
FROM cont_accounts a
LEFT JOIN cont_diarioficial o ON o.id = a.cuentaoficial
WHERE a.removed = 0 AND main_account != 2
ORDER BY a.manual_code";
			$consulta = $this->query($myQuery);	
			return $consulta;
		}
		function listaAcreditamientoProveedores()
		{
			$myQuery = "SELECT
r.id,
r.idPoliza,			
p.numpol,
(SELECT NombreEjercicio FROM cont_ejercicios WHERE Id = p.idejercicio) AS Ejercicio,
p.idperiodo,
p.concepto,
r.referencia,
(SELECT SUM(importe) FROM cont_rel_pol_prov WHERE idPoliza = r.idPoliza) AS Importes,
r.periodoAcreditamiento,
r.ejercicioAcreditamiento
FROM cont_rel_pol_prov r
LEFT JOIN cont_polizas p ON p.id = r.idPoliza
WHERE (r.periodoAcreditamiento = 0 OR r.ejercicioAcreditamiento = 0) AND r.activo=1
GROUP BY numpol";
			$polizas = $this->query($myQuery);	
			return $polizas;
		}

		function listaAcreditamientoDesglose()
		{
			$myQuery = "SELECT
r.id,
r.idPoliza,			
p.numpol,
(SELECT NombreEjercicio FROM cont_ejercicios WHERE Id = p.idejercicio) AS Ejercicio,
p.idperiodo,
p.concepto,
@tasa16 := SUBSTRING_INDEX(SUBSTRING_INDEX( r.tasa16 , '-', -2 ),'-',1),
@tasa11 := SUBSTRING_INDEX(SUBSTRING_INDEX( r.tasa11 , '-', -2 ),'-',1),
@tasa0 := SUBSTRING_INDEX(SUBSTRING_INDEX( r.tasa0 , '-', -2 ),'-',1),
@tasaExenta := SUBSTRING_INDEX(SUBSTRING_INDEX( r.tasaExenta , '-', -2 ),'-',1),
@tasa15 := SUBSTRING_INDEX(SUBSTRING_INDEX( r.tasa15 , '-', -2 ),'-',1),
@tasa10 := SUBSTRING_INDEX(SUBSTRING_INDEX( r.tasa10 , '-', -2 ),'-',1),
@otrasTasas := SUBSTRING_INDEX(SUBSTRING_INDEX( r.otrasTasas , '-', -2 ),'-',1),
(@tasa16+@tasa11+@tasa0+@tasaExenta+@tasa15+@tasa10+@otrasTasas) AS Importes,
r.periodoAcreditamiento,
r.ejercicioAcreditamiento
FROM cont_rel_desglose_iva r
LEFT JOIN cont_polizas p ON p.id = r.idPoliza
WHERE (r.periodoAcreditamiento = 0 OR r.ejercicioAcreditamiento = 0)
GROUP BY numpol";
			$polizas = $this->query($myQuery);	
			return $polizas;
		}

		function actAcreditamiento($Ids,$Periodo,$Ejercicio,$Tipo)
		{
			$Ids = explode(',', $Ids);
			$Cont=1;
			foreach ($Ids as $Id)//Por cada valor de la cadena hace una consulta
			{
				if($Cont == 1)
				{
					$where = "idPoliza = $Id";
				}
				if($Cont > 1)
				{
					$where .= " OR idPoliza = $Id";
				}
				$Cont++;
			}

			//$myQuery = "UPDATE cont_rel_pol_prov SET periodoAcreditamiento = $Periodo, ejercicioAcreditamiento = $Ejercicio WHERE $where";
			$myQuery = "UPDATE $Tipo SET periodoAcreditamiento = $Periodo, ejercicioAcreditamiento = $Ejercicio WHERE $where";
			$a = $this->query($myQuery);
			return $a;
		}	

		function proveedores()
		{
			$myQuery = "SELECT idPrv, razon_social FROM mrp_proveedor";
			$p = $this->query($myQuery);
			return $p;
		}

		function generarConsultaA29($ejercicio,$periodo_inicial,$periodo_final,$prov, $proveedor_inicial, $proveedor_final)
		{

			if($prov == 'algunos')
			{
				$rangoProv = "AND idPrv BETWEEN $proveedor_inicial AND $proveedor_final";
			}
			else
			{
				$rangoProv = '';
			}

			$myQuery = "SELECT
p.idPrv,
SUBSTRING_INDEX((SELECT tipotercero FROM cont_tipo_tercero WHERE id = p.idtipotercero),'-',1) AS TipoTercero,
SUBSTRING_INDEX((SELECT tipoOperacion FROM cont_tipo_operacion WHERE id = p.idtipoperacion),'-',1) AS TipoOperacion,
p.rfc,
pl.fecha,
pl.id,
p.numidfiscal,
p.nombrextranjero,
p.PaisdeResidencia,
p.nacionalidad,
/*16 y 15*********************/
(SELECT SUM(importeBase) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='16%' AND visible=1 AND idPrv = p.idPrv) OR tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='15%' AND visible=1 AND idPrv = p.idPrv)) AS p16,

(SELECT SUM(importeBase) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='15%' AND visible=1 AND idPrv = p.idPrv)) AS p15,

(SELECT SUM(ivaPagadoNoAcreditable) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='16%' AND visible=1 AND idPrv = p.idPrv) OR tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='15%' AND visible=1 AND idPrv = p.idPrv)) AS p16N,

/*11 y 10*********************/
(SELECT SUM(importeBase) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='11%' AND visible=1 AND idPrv = p.idPrv) OR tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='10%' AND visible=1 AND idPrv = p.idPrv)) AS p11,

(SELECT SUM(importeBase) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='10%' AND visible=1 AND idPrv = p.idPrv)) AS p10,

(SELECT SUM(ivaPagadoNoAcreditable) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='11%' AND visible=1 AND idPrv = p.idPrv) OR tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='10%' AND visible=1 AND idPrv = p.idPrv)) AS p11N,

/*0*********************/
(SELECT SUM(importeBase) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='0%' AND visible=1 AND idPrv = p.idPrv)) AS p0,

/*Exenta*********************/
(SELECT SUM(importeBase) FROM cont_rel_pol_prov WHERE activo=1 AND aplica=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio AND tasa = (SELECT id FROM cont_tasaPrv WHERE tasa='Excenta' AND visible=1 AND idPrv = p.idPrv)) AS ep,

(SELECT SUM(ivaRetenido) FROM cont_rel_pol_prov WHERE activo=1 AND idProveedor = p.idPrv AND periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND ejercicioAcreditamiento = $ejercicio) AS IvaRetenido
FROM mrp_proveedor p
INNER JOIN cont_rel_pol_prov r ON r.idProveedor = p.idPrv AND activo=1
INNER JOIN cont_polizas pl ON pl.id = r.idPoliza
WHERE p.idPrv IN (SELECT idProveedor FROM cont_rel_pol_prov WHERE activo=1)
/*AND r.periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND r.ejercicioAcreditamiento = (SELECT Id FROM cont_ejercicios WHERE NombreEjercicio = $ejercicio))*/
AND r.periodoAcreditamiento BETWEEN $periodo_inicial AND $periodo_final AND r.ejercicioAcreditamiento = $ejercicio 
$rangoProv 
AND p.rfc != ''
GROUP BY p.idPrv 
ORDER BY p.idPrv
";

			$c = $this->query($myQuery);
			return $c;
		}
		function balanzaComprobacionReporte($ejercicio,$periodo_inicio,$periodo_fin,$tipoCuenta)
		{
			$p13 = " AND (p.idperiodo!=13 OR (p.idperiodo=13 AND p.idejercicio < $ejercicio AND m.Cuenta IN (SELECT account_id FROM cont_accounts WHERE main_father = (SELECT CuentaSaldos FROM cont_config WHERE id=1))))";
			if(intval($periodo_fin) == 13)
			{
				$periodo_fin = 12;
				$p13 = "";
			}
			$anio = $this->query("SELECT NombreEjercicio FROM cont_ejercicios WHERE id = $ejercicio");
			$anio = $anio->fetch_object();
			$anio = $anio->NombreEjercicio;

			if(intval($periodo_inicio) == 1)
			{
				$resultadoCargo = "0";
				$resultadoAbono = "0";
			}
			else
			{
				$resultadoCargo = "(SELECT SUM(Importe) FROM cont_movimientos m INNER JOIN cont_polizas p ON p.id = m.IdPoliza WHERE m.Activo = 1 AND p.activo = 1 AND m.Cuenta = a.account_id AND m.TipoMovto='Cargo' AND p.fecha BETWEEN '$anio-01-01' AND '$anio-".sprintf('%02d', (intval($periodo_inicio)))."-00' $p13)";
				$resultadoAbono = "(SELECT SUM(Importe) FROM cont_movimientos m INNER JOIN cont_polizas p ON p.id = m.IdPoliza WHERE m.Activo = 1 AND p.activo = 1 AND m.Cuenta = a.account_id AND m.TipoMovto='Abono' AND p.fecha BETWEEN '$anio-01-01' AND '$anio-".sprintf('%02d', (intval($periodo_inicio)))."-00' $p13)";
			}

			if($tipoCuenta == 'a')
			{
				$order = "CAST(h1 AS UNSIGNED),
CAST(h2 AS UNSIGNED),
CAST(h3 AS UNSIGNED),
CAST(h4 AS UNSIGNED),
CAST(h5 AS UNSIGNED),
CAST(h6 AS UNSIGNED)";
$hs = ", 
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 3), LENGTH(SUBSTRING_INDEX(a.account_code, '.', 3 -1)) + 1),'.', '') AS h3,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 4),LENGTH(SUBSTRING_INDEX(a.account_code, '.', 4 -1)) + 1),'.', '') AS h4,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 5), LENGTH(SUBSTRING_INDEX(a.account_code, '.', 5 -1)) + 1),'.', '') AS h5,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 6),LENGTH(SUBSTRING_INDEX(a.account_code, '.', 6 -1)) + 1),'.', '') AS h6";
			}
			else
			{
				$order = "CAST(h1 AS UNSIGNED),
CAST(h2 AS UNSIGNED),a.manual_code";
				$hs = "";
			}
			

			$myQuery = "SELECT DISTINCT a.account_code,
(SELECT CONCAT(manual_code,'/',description) FROM cont_accounts WHERE account_id = a.main_father) AS mayor,
a.manual_code,
a.description,
a.account_nature,

CASE account_type
WHEN 4 THEN $resultadoCargo
ELSE (SELECT SUM(Importe) FROM cont_movimientos m INNER JOIN cont_polizas p ON p.id = m.IdPoliza WHERE m.Activo = 1 AND p.activo = 1 AND m.Cuenta = a.account_id AND m.TipoMovto='Cargo' AND p.fecha < '$anio-$periodo_inicio-01' AND (p.idperiodo!=13 OR (p.idperiodo=13 AND m.Cuenta IN (SELECT account_id FROM cont_accounts WHERE main_father = (SELECT CuentaSaldos FROM cont_config WHERE id=1)))))
END AS CargosAntes,

CASE account_type
WHEN 4 THEN $resultadoAbono
ELSE (SELECT SUM(Importe) FROM cont_movimientos m INNER JOIN cont_polizas p ON p.id = m.IdPoliza WHERE m.Activo = 1 AND p.activo = 1 AND m.Cuenta = a.account_id AND m.TipoMovto='Abono' AND p.fecha < '$anio-$periodo_inicio-01' AND (p.idperiodo!=13 OR (p.idperiodo=13 AND m.Cuenta IN (SELECT account_id FROM cont_accounts WHERE main_father = (SELECT CuentaSaldos FROM cont_config WHERE id=1)))))
END AS AbonosAntes,


																																																															   
(SELECT SUM(Importe) FROM cont_movimientos m INNER JOIN cont_polizas p ON p.id = m.IdPoliza WHERE m.Activo = 1 AND p.activo = 1 AND m.Cuenta = a.account_id AND m.TipoMovto='Cargo' AND p.fecha BETWEEN '$anio-$periodo_inicio-01' AND '$anio-$periodo_fin-31' $p13) AS CargosMes,
(SELECT SUM(Importe) FROM cont_movimientos m INNER JOIN cont_polizas p ON p.id = m.IdPoliza WHERE m.Activo = 1 AND p.activo = 1 AND m.Cuenta = a.account_id AND m.TipoMovto='Abono' AND p.fecha BETWEEN '$anio-$periodo_inicio-01' AND '$anio-$periodo_fin-31' $p13) AS AbonosMes,

REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1), LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),'.', '') AS h1,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2),LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),'.', '') AS h2
$hs

 
FROM cont_accounts a

WHERE a.account_type < 5 AND a.account_id IN (SELECT Cuenta FROM cont_movimientos WHERE Activo = 1)

ORDER BY 
$order



";
//echo $myQuery;

		$c = $this->query($myQuery);
		return $c;

		}
		/*function balanzaComprobacionReporte($ejercicio,$periodo_inicio,$periodo_fin,$tipoCuenta)
		{

			if($tipoCuenta == 'm')
			{
				$orden = 'Codigo';
				$masSplits = '';
			}
			//Si el tipo de codigo de la cuenta es automatico
			if($tipoCuenta == 'a')
			{
				$orden = '';
				$masSplits = '';
				for($i=3;$i<=8;$i++)
				{
					if($i!=8)
					{
						$orden .= "CAST(h$i AS UNSIGNED), ";
					}
					else
					{
						$orden .= "CAST(h$i AS UNSIGNED) ";
					}
					$masSplits .= "REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', $i), LENGTH(SUBSTRING_INDEX(a.account_code, '.', $i -1)) + 1),'.', '') AS h$i, ";
				}
			}

			if($periodo_inicio == '01')
			{
				$rangoCargosAntes = '0';
				$rangoAbonosAntes = '0';
				$nuevoEjercicio = (intval($ejercicio)-1);
				$mes_anterior = $nuevoEjercicio.'-12-31';
			}
			else
			{
				$mes_anterior= $ejercicio.'-'.sprintf('%02d', (intval($periodo_inicio)-1)).'-31';
				$rangoCargosAntes = "(SELECT SUM(vv.Cargos) FROM $this->libromayor vv WHERE vv.Code = v.Code AND vv.Fecha BETWEEN '$ejercicio-01-01' AND '$mes_anterior')";
				$rangoAbonosAntes = "(SELECT SUM(vv.Abonos) FROM $this->libromayor vv WHERE vv.Code = v.Code AND vv.Fecha BETWEEN '$ejercicio-01-01' AND '$mes_anterior')";
			}
			$myQuery = "SELECT
DISTINCT a.account_id,
v.Cuenta,
SUBSTRING(account_code,1,1) AS Familia,
(SELECT manual_code FROM cont_accounts USE INDEX (accounts_account_idx) WHERE account_id = a.main_father) AS Clave_de_Mayor,
v.Cuenta_de_Mayor,
v.Manual_Code,
v.Manual_Code AS Codigo,
v.Naturaleza,
CASE v.Clasificacion WHEN 'Resultados' THEN $rangoCargosAntes
ELSE (SELECT SUM(Cargos) FROM $this->libromayor vv WHERE vv.Code = v.Code AND vv.Fecha BETWEEN '2009-01-01' AND '$mes_anterior') END AS CargosAntes,

CASE v.Clasificacion WHEN 'Resultados' THEN $rangoAbonosAntes
ELSE (SELECT SUM(Abonos) FROM $this->libromayor vv WHERE vv.Code = v.Code AND vv.Fecha BETWEEN '2009-01-01' AND '$mes_anterior') END AS AbonosAntes,

REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),
       '.', '') AS h1,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),
       '.', '') AS h2,
       $masSplits

(SELECT SUM(Cargos) FROM $this->libromayor vv WHERE vv.Code = v.Code AND vv.Fecha BETWEEN '$ejercicio-$periodo_inicio-01' AND '$ejercicio-$periodo_fin-31') AS Cargos,
(SELECT SUM(Abonos) FROM $this->libromayor vv WHERE vv.Code = v.Code AND vv.Fecha BETWEEN '$ejercicio-$periodo_inicio-01' AND '$ejercicio-$periodo_fin-31') AS Abonos

FROM $this->libromayor v
INNER JOIN cont_accounts a USE INDEX (accounts_account_idx) ON a.account_code = v.Code AND removed = 0
WHERE v.Fecha <= '$ejercicio-$periodo_fin-31' AND NOT (v.Fecha = '$ejercicio-$periodo_fin-31' AND idperiodo = 13)
ORDER BY
CAST(h1 AS UNSIGNED),CAST(h2 AS UNSIGNED) , $orden";
			$c = $this->query($myQuery);
			echo $myQuery;
			//echo "<br />Numero: ".$c->num_rows;
			return $c;
		}*/

		function listaSegmentoSucursal($tipo)
		{
			if($tipo)//Si es 0 es segmento , si es 1 es sucursal
			{
				return CaptPolizasModel::ListaSucursales();//Funcion que obtiene la lista de sucursales en la clase captpolizas
			}
			else
			{
				return CaptPolizasModel::ListaSegmentos();//Funcion que obtiene la lista de segmentos en la clase captpolizas
			}
		}

		function listaMonedas(){
			$myQuery="SELECT coin_id,description,codigo FROM cont_coin";
			$monedas= $this->query($myQuery);
			return $monedas;
		}

		function nomSucursal($id)
		{
			$myQuery = "SELECT nombre FROM mrp_sucursal WHERE idSuc=$id";
			$Sucursalr = $this->query($myQuery);
			$Sucursal = $Sucursalr->fetch_object();
			return $Sucursal->nombre;
		}

		function nomSegmento($id)
		{
			$myQuery = "SELECT nombre FROM cont_segmentos WHERE idSuc=$id";
			$segmentor = $this->query($myQuery);
			$Segmento = $segmentor->fetch_object();
			return $Segmento->nombre;	
		}

		function balanceGeneralReporteActivo($ejercicio,$periodo,$sucursal,$segmento,$tipoCuenta)
		{

			if($tipoCuenta == 'm')
			{
				$orden = 'Codigo';
				$masSplits = '';
			}
			//Si el tipo de codigo de la cuenta es automatico
			if($tipoCuenta == 'a')
			{
				$orden = '';
				$masSplits = '';
				for($i=3;$i<=8;$i++)
				{
					if($i!=8)
					{
						$orden .= "CAST(h$i AS UNSIGNED), ";
					}
					else
					{
						$orden .= "CAST(h$i AS UNSIGNED) ";
					}
					$masSplits .= "REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', $i), LENGTH(SUBSTRING_INDEX(a.account_code, '.', $i -1)) + 1),'.', '') AS h$i, ";
				}
			}
			$where = '';
			if(intval($segmento))
			{
				$where .= 'AND b.idsegmento = '.$segmento;
			}
			if(intval($sucursal))
			{
				$where .= ' AND b.idsucursal = '.$sucursal;
			}
			$myQuery = "SELECT 
b.Clasificacion,
(SELECT description FROM cont_accounts WHERE account_code LIKE LEFT(b.Code, 3) AND removed=0) AS Grupo,
b.Cuenta_de_Mayor,
a.manual_code AS Codigo,
a.account_code AS CodigoSistema,
b.Naturaleza,
b.idsucursal,
b.idsegmento,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),
       '.', '') AS h1,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),
       '.', '') AS h2,
       $masSplits
b.Cargos,
b.Abonos,
SUM(IF(b.Naturaleza=1,b.Abonos-b.Cargos,b.Cargos-b.Abonos)) AS CargosAbonos
FROM cont_view_init_balance2 b
INNER JOIN cont_accounts a ON a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)
WHERE b.Cuenta_de_Mayor != '' $where 
AND b.Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' AND b.Clasificacion = 'Activo' 
GROUP BY Cuenta_de_Mayor
ORDER BY CAST(h1 AS UNSIGNED),CAST(h2 AS UNSIGNED) , $orden";
			$datos = $this->query($myQuery);
			//echo $myQuery;
			return $datos;
		}

		function balanceGeneralReporteDemas($ejercicio,$periodo,$sucursal,$segmento,$tipo,$tipoCuenta,$detalle,$p13)
		{
			$where = $whereSub = $myQuery = $activo = $cuentas = '';

			if($tipoCuenta == 'm')
			{
				$orden = 'Codigo';
				$masSplits = '';
			}
			//Si el tipo de codigo de la cuenta es automatico
			if($tipoCuenta == 'a')
			{
				$orden = '';
				$masSplits = '';
				for($i=3;$i<=8;$i++)
				{
					if($i!=8)
					{
						$orden .= "CAST(h$i AS UNSIGNED), ";
					}
					else
					{
						$orden .= "CAST(h$i AS UNSIGNED) ";
					}
					$masSplits .= "REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', $i), LENGTH(SUBSTRING_INDEX(a.account_code, '.', $i -1)) + 1),'.', '') AS h$i, ";
				}
			}

			if(intval($segmento))
			{
				$where .= "AND b.idsegmento = ".$segmento;
				$whereSub .= "AND idsegmento = ".$segmento;
			}
			if(intval($sucursal))
			{
				$where .= " AND b.idsucursal = ".$sucursal;
				$whereSub .= " AND idsucursal = ".$sucursal;
			}

			if(intval($tipo)==1)
			{
				$activo = "AND b.Clasificacion != 'Activo'";
			}

			if(intval($detalle)==1)
			{
				$cuentas = ", Code";
				$mayorOdetalle = "Code = b.Code";
			}
			else
			{
				$mayorOdetalle = "Cuenta_de_Mayor = b.Cuenta_de_Mayor";
			}

			$periodo13 = ' AND b.idperiodo != 13 ';
			$periodo13_sub = ' AND idperiodo != 13 ';
			$saldos = "AND IF (b.Fecha = '$ejercicio-12-31',b.idperiodo!=13,b.idperiodo<=13)";
			if(intval($p13))
			{
				$periodo13 = '';
				$periodo13_sub = '';
				$saldos = '';
			}

			if(intval($tipo))
			{
				$myQuery .= "(SELECT 
b.Clasificacion,
(SELECT description FROM cont_accounts WHERE account_code LIKE LEFT(b.Code, 3) AND removed=0) AS Grupo,
b.Cuenta_de_Mayor,
b.Cuenta, 
(SELECT manual_code FROM cont_accounts WHERE account_code =  b.Code AND removed=0) AS CuentaAfectable, 
a.manual_code AS Codigo,
a.account_code AS CodigoSistema,
b.Naturaleza,
b.idsucursal,
b.idsegmento,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),
       '.', '') AS h1,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),
       '.', '') AS h2,
       $masSplits
@Cargos := (SELECT SUM(Cargos) FROM cont_view_init_balance2 WHERE Cuenta_de_Mayor = b.Cuenta_de_Mayor AND Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)-1))."-31' $whereSub ),
@Abonos := (SELECT SUM(Abonos) FROM cont_view_init_balance2 WHERE Cuenta_de_Mayor = b.Cuenta_de_Mayor AND Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)-1))."-31' $whereSub),
(IF(b.Naturaleza=1,@Abonos-@Cargos,@Cargos-@Abonos)) AS CargosAbonosAnterior,
SUM(IF(b.Naturaleza=1,b.Abonos-b.Cargos,b.Cargos-b.Abonos)) AS CargosAbonos
FROM cont_view_init_balance2 b
INNER JOIN cont_accounts a ON a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)
WHERE b.Cuenta_de_Mayor != '' $where 
 $saldos  
AND b.Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' AND b.Clasificacion !='Resultados' AND b.Clasificacion !='' $activo GROUP BY Cuenta_de_Mayor $cuentas)

UNION ALL";
			}
			
$myQuery .= "
(SELECT 
b.Clasificacion,
(SELECT description FROM cont_accounts WHERE account_code LIKE LEFT(b.Code, 3) AND removed=0) AS Grupo,
b.Cuenta_de_Mayor,
b.Cuenta,
(SELECT manual_code FROM cont_accounts WHERE account_code =  b.Code AND removed=0) AS CuentaAfectable, 
a.manual_code AS Codigo,
a.account_code AS CodigoSistema,
b.Naturaleza,
b.idsucursal,
b.idsegmento,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),
       '.', '') AS h1,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),
       '.', '') AS h2,
       $masSplits
";

if(!intval($tipo))
{
$myQuery .= "@CargosMes := (SELECT SUM(Cargos) FROM cont_view_init_balance2 WHERE $mayorOdetalle AND Fecha BETWEEN '$ejercicio-".sprintf('%02d', (intval($periodo)))."-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' $whereSub $periodo13_sub),
@AbonosMes := (SELECT SUM(Abonos) FROM cont_view_init_balance2 WHERE $mayorOdetalle AND Fecha BETWEEN '$ejercicio-".sprintf('%02d', (intval($periodo)))."-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' $whereSub $periodo13_sub),
(IF(b.Naturaleza=1,@AbonosMes-@CargosMes,@CargosMes-@AbonosMes)) AS CargosAbonosMes,";
}

$myQuery .= "@Cargos := (SELECT SUM(Cargos) FROM cont_view_init_balance2 WHERE Cuenta_de_Mayor = b.Cuenta_de_Mayor AND Fecha BETWEEN '$ejercicio-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)-1))."-31' $whereSub $periodo13_sub),
@Abonos := (SELECT SUM(Abonos) FROM cont_view_init_balance2 WHERE Cuenta_de_Mayor = b.Cuenta_de_Mayor AND Fecha BETWEEN '$ejercicio-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)-1))."-31' $whereSub $periodo13_sub),
(IF(b.Naturaleza=1,@Abonos-@Cargos,@Cargos-@Abonos)) AS CargosAbonosAnterior,
SUM(IF(b.Naturaleza=1,b.Abonos-b.Cargos,b.Cargos-b.Abonos)) AS CargosAbonos
FROM cont_view_init_balance2 b
INNER JOIN cont_accounts a ON a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)
WHERE b.Cuenta_de_Mayor != '' $where
 $periodo13 
AND b.Fecha BETWEEN '$ejercicio-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' AND b.Clasificacion ='Resultados' GROUP BY Cuenta_de_Mayor $cuentas)
ORDER BY CAST(h1 AS UNSIGNED),CAST(h2 AS UNSIGNED) , $orden";
			$datos = $this->query($myQuery);
			//echo $myQuery;
			return $datos;
		}

		function tipoCuenta()
		{
			$myQuery = "SELECT TipoNiveles FROM cont_config WHERE id=1";
			$tc = $this->query($myQuery);
			$tc = $tc->fetch_assoc();
			return $tc['TipoNiveles'];
		}

		function EstadoResultadoxSegmento($ejercicio,$periodo,$sucursal,$detalle){
			if(intval($sucursal))
			{
				$suc .= " AND idsucursal = ".$sucursal;
			}

			if(intval($detalle))
			{
				$gr="code";
			}
			else
			{
				$gr="Cuenta_de_mayor";
			}

			$myQuery ="	SELECT 	Clasificacion,(SELECT description FROM cont_accounts WHERE account_code LIKE LEFT(Code, 3)) AS Grupo,
								Cuenta_de_mayor,naturaleza,
								(SELECT manual_code FROM cont_accounts WHERE account_code =  Code) AS CuentaAfectable, 
								a.manual_code AS Codigo,
								a.account_code AS CodigoSistema,code,Cuenta,
								sum(cargos) as cargos,sum(abonos) as abonos,
								sum(abonos)-sum(cargos) as resultado,idperiodo,idsegmento,idsucursal 
						FROM cont_view_init_balance2, cont_accounts a
						WHERE clasificacion='Resultados' AND Fecha BETWEEN '$ejercicio-".sprintf('%02d', (intval($periodo)))."-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' 
								$suc AND a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = Code AND removed=0)
						GROUP BY $gr,idsegmento,idsucursal
						ORDER BY a.manual_code,idsegmento";
			$datos = $this->query($myQuery);
			//echo $myQuery;
			return $datos;
		}

		function balanceGeneralReportePeriodos($ejercicio,$sucursal,$segmento,$tipo,$tipoCuenta,$detalle)
		{
			///////////////////////////////////////////////////
			//Si el tipo de codigo de la cuenta es manual
			$orden='';

			if(intval($detalle))
			{
				$account = "account_id";
				$demayor = 'Codigo';
				$mayor   = "(SELECT manual_code FROM cont_accounts WHERE account_id =  a.main_father) AS Mayor, ";
				$orden .= "Mayor, ";
			}
			else
			{
				$account = "main_father";
				$demayor = 'Codigo';
				$mayor 	 = '';
			}

			if($tipoCuenta == 'm')
			{
				$orden .= 'Codigo,';
				$masSplits = '';
			}
			//Si el tipo de codigo de la cuenta es automatico
			if($tipoCuenta == 'a')
			{
				$masSplits = '';
				for($i=3;$i<=8;$i++)
				{
					$orden .= "CAST(h$i AS UNSIGNED), ";
					$masSplits .= "REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', $i), LENGTH(SUBSTRING_INDEX(a.account_code, '.', $i -1)) + 1),'.', '') AS h$i, ";
				}
			}
			///////////////////////////////////////////////////

			$where = '';
			if(intval($segmento))
			{
				$where .= 'AND b.idsegmento = '.$segmento;
			}
			if(intval($sucursal))
			{
				$where .= ' AND b.idsucursal = '.$sucursal;
			}

			if(!intval($tipo))
			{
				$where .= " AND b.Clasificacion = 'Resultados'";
			}

			$myQuery='';
			
			$mes = '01';

			for($m=1;$m<=12;$m++)
			{
				if(!intval($tipo))
				{
					$mes = sprintf('%02d', (intval($m)));
				}

				$myQuery .= "(SELECT 
'".sprintf('%02d', (intval($m)))."' AS Mes,
b.Clasificacion,
(SELECT description FROM cont_accounts WHERE account_code LIKE LEFT(b.Code, 3) AND removed=0) AS Grupo,
b.Cuenta_de_Mayor,
a.manual_code AS Codigo,
a.account_code AS CodigoSistema,
$mayor
b.Cuenta,
b.Naturaleza,
b.idsucursal,
b.idsegmento,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),
       '.', '') AS h1,
REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2),
       LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),
       '.', '') AS h2,
       $masSplits 
b.Cargos,
b.Abonos,
SUM(IF(b.Naturaleza=1,b.Abonos-b.Cargos,b.Cargos-b.Abonos)) AS CargosAbonos

FROM cont_view_init_balance2 b
INNER JOIN cont_accounts a ON a.account_id = (SELECT $account FROM cont_accounts WHERE account_code = b.Code AND removed=0)
WHERE b.Cuenta_de_Mayor != '' AND b.Clasificacion !='' $where 
AND IF(b.Clasificacion = 'Resultados',b.Fecha BETWEEN '$ejercicio-$mes-01' AND '$ejercicio-".sprintf('%02d', (intval($m)))."-31',b.Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($m)))."-31') ";
if($m==12)
{
	$myQuery .= "AND IF (b.Fecha = '$ejercicio-12-31',b.idperiodo!=13,b.idperiodo<=13) ";
}
$myQuery .= "GROUP BY $demayor)";
if($m!=12)
{
	$myQuery .= 'UNION
	';
}
else
{
	$myQuery .= " ORDER BY CAST(h1 AS UNSIGNED),CAST(h2 AS UNSIGNED) , $orden Mes";
}
			}
			//echo $myQuery;
			$datos = $this->query($myQuery);
			return $datos;
		}

		function nifReporte($ejercicio,$periodo,$sucursal,$segmento,$tipo,$p13)
		{
			$where = '';

			if(intval($segmento))
			{
				$where .= "AND b.idsegmento = ".$segmento;
			}
			if(intval($sucursal))
			{
				$where .= " AND b.idsucursal = ".$sucursal;
			}
			
			$periodo13 = ' AND b.idperiodo != 13 ';
			$periodo13bb = ' AND bb.idperiodo != 13 ';
			$periodo13_sub = ' AND idperiodo != 13 ';
			$saldos = "AND IF (b.Fecha = '$ejercicio-12-31',b.idperiodo!=13,b.idperiodo<=13)";
			$saldosbb = "AND IF (bb.Fecha = '$ejercicio-12-31',bb.idperiodo!=13,bb.idperiodo<=13)";
			if(intval($p13))
			{
				$periodo13 = '';
				$periodo13_sub = '';
				$saldos = '';
			}

			$myQuery = '';
			if($tipo == 3)
			{
			$myQuery = "(SELECT 
Clasificacion,
Nivel,
NIF,
CodigoSistema,
Naturaleza,
SUM(IF(Naturaleza=1,Abonos-Cargos,Cargos-Abonos)) AS CargosAbonos,
SUM(IF(Naturaleza=1,AbonosAntes-CargosAntes,CargosAntes-AbonosAntes)) AS CargosAbonosAnterior

FROM ((SELECT 
b.Clasificacion,
(SELECT n.nivel FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)) AS Nivel,
(SELECT CONCAT(n.id,'/',n.clasificacion) FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)) AS NIF,
(SELECT account_code FROM cont_accounts WHERE account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)) AS CodigoSistema,
b.Naturaleza,
b.idsucursal,
b.idsegmento,
b.Cargos AS Cargos,
b.Abonos AS Abonos,
0 AS CargosAntes,
0 AS AbonosAntes

FROM cont_view_init_balance2 b
WHERE b.Cuenta_de_Mayor != '' $where $saldos AND b.Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' AND b.Clasificacion !='Resultados')

UNION ALL

(SELECT 
bb.Clasificacion,
(SELECT n.nivel FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = bb.Code AND removed=0)) AS Nivel,
(SELECT CONCAT(n.id,'/',n.clasificacion) FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = bb.Code AND removed=0)) AS NIF,
(SELECT account_code FROM cont_accounts WHERE account_id = (SELECT main_father FROM cont_accounts WHERE account_code = bb.Code AND removed=0)) AS CodigoSistema,
bb.Naturaleza,
bb.idsucursal,
bb.idsegmento,
0 AS Cargos,
0 AS Abonos,
bb.Cargos AS CargosAntes,
bb.Abonos AS AbonosAntes

FROM cont_view_init_balance2 bb
WHERE bb.Cuenta_de_Mayor != '' $where $saldosbb AND bb.Fecha BETWEEN '2000-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)-1))."-31' AND bb.Clasificacion !='Resultados')) AS n
WHERE NIF != ''
GROUP BY NIF
)

UNION ALL";
}

			$myQuery .= "(SELECT 
Clasificacion,
Nivel,
NIF,
CodigoSistema,
Naturaleza,
SUM(IF(Naturaleza=1,Abonos-Cargos,Cargos-Abonos)) AS CargosAbonos,
SUM(IF(Naturaleza=1,AbonosAntes-CargosAntes,CargosAntes-AbonosAntes)) AS CargosAbonosAntes

FROM ((SELECT 
b.Clasificacion,
(SELECT n.nivel FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)) AS Nivel,
(SELECT CONCAT(n.id,'/',n.clasificacion) FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)) AS NIF,
(SELECT account_code FROM cont_accounts WHERE account_id = (SELECT main_father FROM cont_accounts WHERE account_code = b.Code AND removed=0)) AS CodigoSistema,
b.Naturaleza,
b.idsucursal,
b.idsegmento,
b.Cargos AS Cargos,
b.Abonos AS Abonos,
0 AS CargosAntes,
0 AS AbonosAntes

FROM cont_view_init_balance2 b
WHERE b.Cuenta_de_Mayor != '' $where $periodo13 AND b.Fecha BETWEEN '$ejercicio-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)))."-31' AND b.Clasificacion ='Resultados')

UNION ALL

(SELECT 
bb.Clasificacion,
(SELECT n.nivel FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = bb.Code AND removed=0)) AS Nivel,
(SELECT CONCAT(n.id,'/',n.clasificacion) FROM cont_accounts a INNER JOIN cont_clasificacion_nif n  ON n.id = a.nif WHERE a.account_id = (SELECT main_father FROM cont_accounts WHERE account_code = bb.Code AND removed=0)) AS NIF,
(SELECT account_code FROM cont_accounts WHERE account_id = (SELECT main_father FROM cont_accounts WHERE account_code = bb.Code AND removed=0)) AS CodigoSistema,
bb.Naturaleza,
bb.idsucursal,
bb.idsegmento,
0 AS Cargos,
0 AS Abonos,
bb.Cargos AS CargosAntes,
bb.Abonos AS AbonosAntes

FROM cont_view_init_balance2 bb
WHERE bb.Cuenta_de_Mayor != '' $where $periodo13bb AND bb.Fecha BETWEEN '$ejercicio-01-01' AND '$ejercicio-".sprintf('%02d', (intval($periodo)-1))."-31' AND bb.Clasificacion ='Resultados')) AS n
WHERE NIF != ''
GROUP BY NIF
)
ORDER BY SUBSTRING(NIF,1,2) * 1 ASC";
			$datos = $this->query($myQuery);
			return $datos;
		}

		function catalogoCuentasReporte($naturaleza,$tipo,$tipoCuenta)
		{

			if($tipoCuenta == 'm')
			{
				$orden = 'CodigoManual';
				$masSplits = '';
			}
			//Si el tipo de codigo de la cuenta es automatico
			if($tipoCuenta == 'a')
			{
				$orden = '';
				$masSplits = '';
				for($i=3;$i<=8;$i++)
				{
					if($i!=8)
					{
						$orden .= "CAST(h$i AS UNSIGNED), ";
					}
					else
					{
						$orden .= "CAST(h$i AS UNSIGNED) ";
					}
					$masSplits .= ", REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', $i), LENGTH(SUBSTRING_INDEX(a.account_code, '.', $i -1)) + 1),'.', '') AS h$i ";
				}
			}
			$where = '';
			if(intval($naturaleza))
			{
				$where = "a.account_nature=$naturaleza AND ";
			}

			if(intval($tipo))
			{
				$where .= " t.type_id=$tipo AND ";
			}

			$myQuery = "SELECT 
			(SELECT codigo_agrupador FROM cont_diarioficial WHERE id = cuentaoficial) AS CodigoOficial,
			a.manual_code AS CodigoManual,
			a.description AS Nombre,
			t.description AS Clasificacion,
			(CASE WHEN a.account_code LIKE '1%' THEN 'Activo' WHEN a.account_code LIKE '2%' THEN 'Pasivo' WHEN a.account_code LIKE '3%' THEN 'Capital' WHEN a.account_code LIKE '4%' THEN 'Resultados' WHEN a.account_code LIKE '5%' THEN 'De Orden' END) AS TipoCuenta, 
			n.description AS Naturaleza, 
			(CASE WHEN affectable = 1 THEN 'Si' WHEN affectable = 0 THEN 'No' END) AS Afectable,
			REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 1), LENGTH(SUBSTRING_INDEX(a.account_code, '.', 1 -1)) + 1),'.', '') AS h1,
			REPLACE(SUBSTRING(SUBSTRING_INDEX(a.account_code, '.', 2), LENGTH(SUBSTRING_INDEX(a.account_code, '.', 2 -1)) + 1),'.', '') AS h2
			$masSplits
			FROM	
			cont_accounts a 
			INNER JOIN cont_nature n ON n.nature_id = a.account_nature 
			INNER JOIN cont_main_type t ON t.type_id = a.main_account
			WHERE 
			$where 
			a.removed = 0 
			ORDER BY 
			CAST(h1 AS UNSIGNED), CAST(h2 AS UNSIGNED),
			$orden 
			";

			$cuentas = $this->query($myQuery);
			//echo $myQuery;
			return $cuentas;
		}

		function naturalezas()
		{
			$myQuery = "SELECT* FROM cont_nature";
			$naturalezas = $this->query($myQuery);
			return $naturalezas;
		}

		function tipos()
		{
			$myQuery = "SELECT* FROM cont_main_type";
			$tipos = $this->query($myQuery);
			return $tipos;
		}

		function logo()
		{
			$myQuery = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
			$logo = $this->query($myQuery);
			$logo = $logo->fetch_assoc();
			return $logo['logoempresa'];
		}

		function borraFacturaForm($IdPoliza,$Archivo)
		{
			$myQuery = "UPDATE cont_movimientos SET Factura = '-', Referencia = '' WHERE IdPoliza = $IdPoliza AND Factura LIKE '%$Archivo%';";
			$this->query($myQuery);
		}

		function generarConsultaPolizas($ejercicio,$periodo)
		{
			$myQuery = "SELECT 
p.id, 
p.idtipopoliza, 
p.numpol, 
p.idperiodo, 
p.idejercicio,
p.fecha,
p.concepto AS conceptoPoliza,
p.numero,
p.rfc,
(SELECT CONCAT(cb.cuenta,'*-/-*',(SELECT Clave FROM cont_bancos WHERE idbanco = cb.idbanco)) FROM bco_cuentas_bancarias cb WHERE cb.idbancaria = p.idCuentaBancariaOrigen) AS CuentaBancariaBancoOrigen,
p.numtarjcuent AS CuentaDestino,
(SELECT Clave FROM cont_bancos WHERE idbanco = p.idbanco) AS BancoDestino,
(SELECT razon_social FROM mrp_proveedor WHERE idPrv = p.beneficiario) AS Benef,
a.manual_code,
a.main_father,
a.description,
m.NumMovto,
m.concepto AS conceptoMovimiento,
m.referencia,
m.TipoMovto,
m.Importe,
m.Factura,
m.FormaPago AS idFormaPago,
(SELECT claveSat FROM forma_pago WHERE idFormaPago = m.FormaPago) AS FormaPago
FROM cont_movimientos m 
INNER JOIN cont_polizas p ON p.id = m.IdPoliza 
INNER JOIN cont_accounts a USE INDEX (accounts_account_idx) ON a.account_id = m.Cuenta
WHERE 
p.activo=1
AND m.Activo=1
AND p.fecha BETWEEN '$ejercicio-$periodo-01' AND '$ejercicio-$periodo-31'
ORDER BY p.idtipopoliza, p.numpol, p.fecha, m.NumMovto";
			$consulta = $this->query($myQuery);	
			return $consulta;
		}

		function generarConsultaFolios($ejercicio,$periodo)
		{
			$myQuery = "SELECT DISTINCT m.Factura,p.idtipopoliza,p.numpol,p.id,p.fecha,m.Referencia
FROM cont_polizas p
INNER JOIN cont_movimientos m ON m.IdPoliza = p.id
WHERE m.Factura != '' AND m.Factura != '-' AND m.Factura != '0'
AND p.fecha BETWEEN '$ejercicio-$periodo-01' AND '$ejercicio-$periodo-31'
AND p.activo AND m.Activo
ORDER BY p.fecha, p.id";
			$consulta = $this->query($myQuery);	
			return $consulta;
		}


		function CuentaBancos($idCuenta)
		{
			$tf = false;
			$myQuery = "SELECT CuentaBancos FROM cont_config WHERE id=1";
			$resultado = $this->query($myQuery);
			$resultado = $resultado->fetch_assoc();
			if(intval($resultado['CuentaBancos']) == intval($idCuenta))
			{
				$tf = true;
			}
			return $tf;
		}
		function metodoPago($tipo)
		{
			$myQuery = "SELECT claveSat FROM forma_pago WHERE nombre = '$tipo'";
			$consulta = $this->query($myQuery);	
			$consulta = $consulta->fetch_assoc();
			return $consulta['claveSat'];

		}

	}
?>


