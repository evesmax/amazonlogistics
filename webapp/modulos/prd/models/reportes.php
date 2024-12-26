<?php

class ReportesModel extends OrdenPrdModel {

	function listareabastoGnr() {
		$sql = $this -> query("select r.*,e.nombreEmpleado,p.nombre nombreprd, d.cantidad from prd_reabasto_insumos r
							inner join nomi_empleados e on e.idEmpleado=r.id_operador
							inner join prd_orden_produccion_detalle d on d.id_orden_produccion=r.id_oproduccion
							inner join app_productos p on p.id=d.id_producto
							order by r.estatus asc");
		if ($sql -> num_rows > 0) {
			return $sql;

		} else {
			return 0;
		}
	}

	function insumosReabasto($idread) {
		$sql = $this -> query("SELECT r.*,p.nombre, u.clave
		from prd_reabasto_insumos_detalle r
		left join app_productos p on p.id=r.id_insumo
		left join app_unidades_medida u on u.id=p.id_unidad_compra
		where
		r.id_reabasto=$idread;");
		if ($sql -> num_rows > 0) {
			return $sql;
		} else {
			return 0;
		}
	}

	public function organizacion() {
		$datos = $this -> query("SELECT o.*, (SELECT municipio FROM municipios WHERE idmunicipio = o.idmunicipio) AS municipio, (SELECT estado FROM estados WHERE idestado = o.idestado) AS estado FROM organizaciones o WHERE o.idorganizacion = 1");
		$datos = $datos -> fetch_assoc();
		return $datos;
	}

	function logo() {
		$myQuery = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
		$logo = $this -> query($myQuery);
		$logo = $logo -> fetch_assoc();
		return $logo['logoempresa'];
	}
	//insumos de un producto de fabricacion
	function insumoMaterial($idinsu){
		$sql = $this->query("select m.cantidad,u.clave,p.nombre
				from
				app_producto_material m 
				left join app_unidades_medida u on u.id=m.id_unidad
				left join app_productos p on p.id=m.id_material
				where m.id_producto=$idinsu;");
		if($sql->num_rows>0){
			return $sql;
		}else{
			return 0;
		}
	}
	//reabasto
	function insumoPendiente($idop){
		$sql= $this->query("select count(id) from prd_reabasto_insumos_detalle where id_oproduccion =$idop  and estatus=1");
		if($sql->num_rows>0){
			return 1;
		}else{
			return 0;
		}
	}
	//cantidad en inventario acuerdo a almacen de orden de produccion, de donde estan apartados los demas insumos
	function cantidadAlmacenOrden($idop){
		$sql= $this->query("select i.id_producto, IFNULL(i.cantidad - i.apartados,0) cantidad from app_inventario i
			inner join app_inventario_movimientos mi on i.id_almacen=mi.id_almacen_origen and i.id_producto=mi.id_producto
 			where mi.referencia='Orden de produccion / Apartado usarInsumo -$idop'  and mi.tipo_traspaso=3;");
 		return $sql;
	}
	function autorizaTodo($idop){
		// sacar los ids de los insumos q aun no esten autorizados
		$sql = "insert into app_inventario_movimientos (id_producto,id_producto_caracteristica,id_pedimento,id_lote,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,origen,id_poliza_mov,consigna)
 			(select mi.id_producto,mi.id_producto_caracteristica,mi.id_pedimento,mi.id_lote,ri.cantidad,0,mi.id_almacen_origen,mi.id_almacen_destino,mi.fecha,mi.id_empleado,mi.tipo_traspaso,mi.costo,'Orden de produccion / Apartado ReabastoInsumo -$idop',mi.estatus,mi.origen,mi.id_poliza_mov,mi.consigna from
 			 app_inventario_movimientos mi
 			 inner join prd_reabasto_insumos_detalle ri on mi.id_producto = ri.id_insumo
 			where mi.referencia='Orden de produccion / Apartado usarInsumo -$idop'  and mi.tipo_traspaso=3 and  ri.estatus=1);";
		
		$sql.="update prd_reabasto_insumos_detalle rd,prd_reabasto_insumos ri set ri.estatus=2,rd.estatus=2 where ri.id_oproduccion=$idop and rd.id_oproduccion=$idop;";
		while ($this->connection->next_result()) {;}
		if($this->dataTransact($sql) ===true){
			return 1;
		}else{
			return 0;
		}
	
	}
	function autorizaInsumo($idop,$idinsumo){ 
		date_default_timezone_set("Mexico/General");
		$creacion = date('Y-m-d H:i:s');
		$sql = "insert into app_inventario_movimientos (id_producto,id_producto_caracteristica,id_pedimento,id_lote,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,origen,id_poliza_mov,consigna)
 			(select mi.id_producto,mi.id_producto_caracteristica,mi.id_pedimento,mi.id_lote,ri.cantidad,0,mi.id_almacen_origen,mi.id_almacen_destino,'$creacion',mi.id_empleado,mi.tipo_traspaso,mi.costo,'Orden de produccion / Apartado ReabastoInsumo -$idop',mi.estatus,mi.origen,mi.id_poliza_mov,mi.consigna 
 			from
 			 app_inventario_movimientos mi
 			 inner join prd_reabasto_insumos_detalle ri on mi.id_producto = ri.id_insumo
 			where mi.referencia='Orden de produccion / Apartado usarInsumo -$idop' and mi.tipo_traspaso=3 and ri.estatus=1 and ri.id_insumo=$idinsumo);";
		
		$sql.="update prd_reabasto_insumos_detalle rd set rd.estatus=2 where rd.id_oproduccion=$idop and id_insumo=$idinsumo;";
		
		if($this->multi_query($sql)){
			while ($this->connection->next_result()) {;}
			$sql= $this->query("select count(id) cont from prd_reabasto_insumos_detalle where id_oproduccion =$idop  and estatus=1");
			$cont = $sql->fetch_object();
			if($cont->cont == 0){
				//si ya autorizo todos los insumos autoriza el pedido
				$this->query("update prd_reabasto_insumos ri set ri.estatus=2 where ri.id_oproduccion=$idop;");
				return 2;
			}else{
				return 1;
			}
		}else{
			return 0;
		}
	}
	function cancelarReabasto($idop){
		$au = $this->validaEstatus($idop);
		if($au==1){
			return 2;
		}else{
			$sql = "update prd_reabasto_insumos_detalle rd,prd_reabasto_insumos ri set ri.estatus=3,rd.estatus=3 where ri.id_oproduccion=$idop and rd.id_oproduccion=$idop;";
			if($this->query($sql)){
				return 1;
			}else{
				return 0;
			}
		}
	}
	function validaEstatus($idop){
		$sql = $this->query("select * from prd_reabasto_insumos_detalle where estatus=2 and id_oproduccion=$idop;");
		if($sql->num_rows>0){
			return 1;
		}else{
			return 0;
		}
	}

}
?>