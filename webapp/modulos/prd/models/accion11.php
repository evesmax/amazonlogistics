<?php

class Accion11Model extends OrdenPrdModel {

	function historial11($idop, $idap, $opc) {
		$myQuery = "SELECT  b.id,b.id_operador as idOperador, concat(e.nombreEmpleado,' ',e.apellidoPaterno) as nombreemp, b.f_ini, if(b.f_fin is null,0,b.f_fin) as f_fin,
			f.id as idProducto, f.nombre, a.cantidad, b.id_pa,b.cantppf
			from prd_matpro a 
			inner join prd_matp b on b.id=a.id_mp
			inner join prd_personal c on c.id_oproduccion=b.id_oproduccion
			inner join prd_personal_detalle d on d.id_personal=c.id and d.id_empleado=b.id_operador
			inner join nomi_empleados e on e.idEmpleado=d.id_empleado
			inner join app_productos f on f.id=a.id_insumo
			where b.id_oproduccion='$idop' ";
		if ($opc == 0) {
			$myQuery .= "AND b.id_pa='$idap';";
		}
		$q = $this -> queryArray($myQuery);
		return $q;

	}

	function savePaso11($idsProductos, $accion, $idop, $paso, $idap, $idempo, $opc, $ppf) {

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		if ($opc == 0) {
			$myQuery = "INSERT INTO prd_matp (id_oproduccion,id_operador,id_pa,f_ini,cantppf) VALUES ('" . $idop . "','" . $idempo . "','" . $idap . "','" . $creacion . "'," . $ppf . ") ;";

			$last_id = $this -> insert_id($myQuery);
			$d = explode('___', $idsProductos);

			$cad = '';
			foreach ($d as $k => $v) {
				$r = explode('###', $v);
				$idpersonal = $r[0];

				$ins = explode(',', $r[1]);

				foreach ($ins as $k2 => $v2) {

					$kkk = explode('#', $v2);

					$idprod = $kkk[0];
					$cant = $kkk[1];
					$cad .= "('','','" . $idprod . "','" . $cant . "','" . $idap . "','" . $last_id . "'),";
				}
			}

			$cad = trim($cad, ',');
			$myQuery = "INSERT INTO prd_matpro (id_oproduccion,id_operador,id_insumo,cantidad,id_pasoaccion,id_mp) VALUES " . $cad . " ;";
			$query = $this -> query($myQuery);
		} else {
			$myQuery = "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
			$query = $this -> query($myQuery);
		}

		echo $query;

	}

	function finalizar($id) {

		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');

		$myQuery = "UPDATE prd_matp SET f_fin='$creacion' WHERE id='$id';";
		$this -> query($myQuery);

	}
	function reabastoAutorizado11($idop,$matp,$idap){
		
		$sql = $this->query("select d.*,p.nombre as prd,e.nombreEmpleado,i.id_matp from prd_reabasto_insumos_detalle d
							inner join prd_reabasto_insumos i on i.id=d.id_reabasto
							inner join nomi_empleados e on e.idEmpleado=i.id_operador
							inner join app_productos p on p.id=d.id_insumo
							inner join prd_matp mp on mp.id=i.id_matp
							where d.id_oproduccion=$idop and d.estatus=2  and i.id_pasoaccion=$idap;");
		if($sql->num_rows>0){
			return $sql;
		}else{
			return 0;
		}
	}

}
?>