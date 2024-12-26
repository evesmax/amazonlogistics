<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

	class CaptPolizasModel extends Connection
	{
		function getExerciseInfo()
		{
			$myQuery = "SELECT c.IdOrganizacion, o.nombreorganizacion,e.NombreEjercicio,e.Id AS IdEx,c.PeriodoActual,c.EjercicioActual,c.InicioEjercicio,c.FinEjercicio,c.PeriodosAbiertos,c.Estructura FROM cont_config c INNER JOIN organizaciones o ON o.idorganizacion = c.IdOrganizacion INNER JOIN cont_ejercicios e ON e.NombreEjercicio = c.EjercicioActual";
			$companies = $this->query($myQuery);
			return $companies;
		}

		function getSegmentoInfo()
		{
			$myQuery = "SELECT* FROM cont_segmentos;";
			$s = $this->query($myQuery);
			return $s->num_rows;
		}

		function getActivePolizas($ejercicio,$periodo)
		{
			//$myQuery = "SELECT p.*,(SELECT titulo FROM cont_tipos_poliza WHERE id=p.idtipopoliza) AS tipopoliza FROM cont_polizas p WHERE p.activo=1 AND p.idejercicio=".$ejercicio." AND p.idperiodo=".$periodo;
			$myQuery = "SELECT 
id,
p.numpol,
(SELECT titulo FROM cont_tipos_poliza WHERE id=p.idtipopoliza) AS tipopoliza,
p.concepto,
p.fecha,
IFNULL((SELECT SUM(Importe) FROM cont_movimientos WHERE TipoMovto ='Cargo' AND IdPoliza = p.id AND Activo=1),0) AS Cargos,
IFNULL((SELECT SUM(Importe) FROM cont_movimientos WHERE TipoMovto = 'Abono' AND IdPoliza = p.id AND Activo=1),0) AS Abonos,
fecha_creacion,
usuario_creacion,
fecha_modificacion,
usuario_modificacion
FROM cont_polizas p
WHERE 
p.idejercicio = $ejercicio
AND p.idperiodo = $periodo
AND p.activo=1
ORDER BY p.idtipopoliza,p.numpol";
			$polizas = $this->query($myQuery);
			return $polizas;
		}
}
?>
