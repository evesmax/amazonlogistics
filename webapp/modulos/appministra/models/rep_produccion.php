<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi
class Rep_ProduccionModel extends Connection{
    
// 	
	// SELECT a.id as idOP, b.id_producto, p.codigo, p.nombre, u.clave, (b.cantidad*m.cantidad) as cant_insumo, a.dependencia from prd_orden_produccion a
// inner join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
// left join app_producto_material m on m.id_producto=b.id_producto
// left join app_productos p on p.id=m.id_material
// left join app_unidades_medida u on u.id=p.id_unidad_compra
// where p.tipo_producto!=8
// order by a.id desc;
	function reporteAbasto(){
		$sql = $this->query("SELECT a.id, pr.nombre,pd.cantidad, a.estatus, a.autorizado,a.fecha_registro
            FROM prd_orden_produccion a
            inner join prd_orden_produccion_detalle pd on pd.id_orden_produccion=a.id
            inner join app_productos pr on pr.id=pd.id_producto
            where a.dependencia=0
            ORDER BY a.id desc;");
		if($sql->num_rows>0){
			return $sql;
		}else{
			return 0;
		}
	}
	function insumosOrden($idop){
		$sql = $this->query("SELECT a.id as idOP, b.id_producto, p.codigo, p.nombre, u.clave, (b.cantidad*m.cantidad) as cant_insumo, a.dependencia ,b.cantidad cantprod,p.tipo_producto,m.id_material
		from prd_orden_produccion a
		inner join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
		left join app_producto_material m on m.id_producto=b.id_producto 
		left join app_productos p on p.id=m.id_material
		left join app_unidades_medida u on u.id=p.id_unidad_compra
		where  a.id=$idop  ;");
		if($sql->num_rows>0){
			return $sql;
		}else{
			return 0;
		}
	}
	function bandera(){
		$myQuery = "SELECT aut_ord_prod,genoc_sinreq,insumosvariables FROM prd_configuracion ;";
		$resultb = $this->query($myQuery);
        $row = $resultb->fetch_array();
        return $row;
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
	 public function organizacion()
    {
        $datos = $this->query("SELECT o.*, (SELECT municipio FROM municipios WHERE idmunicipio = o.idmunicipio) AS municipio, (SELECT estado FROM estados WHERE idestado = o.idestado) AS estado FROM organizaciones o WHERE o.idorganizacion = 1");
        $datos = $datos->fetch_assoc();
        return $datos;
    }
	
	function logo()
	{
		$myQuery = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
		$logo = $this->query($myQuery);
		$logo = $logo->fetch_assoc();
		return $logo['logoempresa'];
	}
	function editarordenp($idop) {

		$myQuery = "SELECT a.id, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe, d.idSuc as idsuc,d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as username, a.estatus, a.prioridad, a.observaciones, b.idempleado,a.solicitante as idsol, e.cantidad,a.lote ,concat(em.nombreEmpleado,' ',em.apellidoPaterno) as solicitante,a.observaciones
        FROM prd_orden_produccion a 
        INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
        left JOIN mrp_sucursal d on d.idSuc=a.id_sucursal
        left JOIN prd_orden_produccion_detalle e on e.id_orden_produccion=a.id
         left JOIN nomi_empleados em on em.idEmpleado=a.solicitante
        WHERE a.id=".$idop;
		$datosReq = $this -> query($myQuery);
		return $datosReq;

	}
	
	
	/*esto es viejo*/
    function reporte_abasto(){
            $sql = "SELECT a.id as idOP, b.id_producto, p.codigo, p.nombre, u.clave, (b.cantidad*m.cantidad) as cant_insumo, a.dependencia from prd_orden_produccion a
inner join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
left join app_producto_material m on m.id_producto=b.id_producto
left join app_productos p on p.id=m.id_material
left join app_unidades_medida u on u.id=p.id_unidad_compra
where p.tipo_producto!=8
order by a.id desc;";
            $result = $this->queryArray($sql);
            
            if($result['total']>0){
                $elkey=0;
                $cuantos=0;
                $array_orden=array();
                $array_2n=array();
                $array_1n=array();
                $array_0n=array();

                foreach ($result['rows'] as $k => $v) {
                    if($v['dependencia']!=0){
                        $exp=explode('-', $v['dependencia']);
                        $c_exp= count($exp);
                        if($c_exp==2){
                            $array_2n[]=$v;
                        }
                        if($c_exp==1){
                            $array_1n[]=$v;
                        }



                    }else{
                       


                        $array_1n = array_reverse($array_1n);
                        $array_2n = array_reverse($array_2n);
                        
                        foreach ($array_2n as $k2 => $v2) {
                            $array_orden[]=$v2;
                        }

                        foreach ($array_1n as $k1 => $v1) {
                            $array_orden[]=$v1;
                        }

                        
                        $array_orden[]=$v;
                        $array_2n=array();
                        $array_1n=array();


                    }
                }


            }

            return $array_orden; 
    }
 

}