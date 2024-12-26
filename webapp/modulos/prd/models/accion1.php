<?php

class Accion1Model extends OrdenPrdModel{
	
	function savePaso1($idsProductos,$accion,$idop,$paso,$idap){

        date_default_timezone_set("Mexico/General");
        $creacion=date('Y-m-d H:i:s');
        $myQuery = "UPDATE prd_orden_produccion SET estatus='9', fecha_p='$creacion' WHERE id='$idop';";
        $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";

        if($this->dataTransact($myQuery) === true){
        		return 1;
        }else{
        		return 0;
        }

    }
}

?>
	