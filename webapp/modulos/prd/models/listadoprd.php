<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class ListadoPrdModel extends Connection{
	function listaOrdenesP(){
	    $myQuery = "SELECT a.id, pr.nombre,pd.cantidad,SUBSTRING(a.fecha_registro,1,10) as fr, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe,d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as usuario, a.estatus, a.autorizado,pr.insumovariable,pr.vendible
	    FROM prd_orden_produccion a
	    INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
	    left JOIN mrp_sucursal d on d.idSuc=a.id_sucursal 
	    inner join prd_orden_produccion_detalle pd on pd.id_orden_produccion=a.id
	    inner join app_productos pr on pr.id=pd.id_producto
	    ORDER BY a.id desc;";
		$listaReq = $this->query($myQuery);
		return $listaReq;

    }
	function bandera(){
		$myQuery = "SELECT aut_ord_prod,genoc_sinreq,insumosvariables,explosionmat,regordenp,mostrar_prov_op,ord_x_lotes FROM prd_configuracion ;";
		$resultb = $this->query($myQuery);
        $row = $resultb->fetch_array();
		return $row;
    }
	
	
}
?>