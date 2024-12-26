<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class ConsultorModel extends Connection
{
   
    public function grid($organismo = 0){
        $registros = array();
        if($organismo == 0){
            $consultores = 'SELECT e.idempleado, e.nombre, u.usuario FROM empleados AS e 
                            INNER JOIN accelog_usuarios AS u ON u.idempleado = e.idempleado 
                            INNER JOIN administracion_usuarios AS au ON au.idempleado = u.idempleado 
                            INNER JOIN accelog_usuarios_per AS up ON up.idempleado = au.idempleado 
                            WHERE e.visible = -1;';
            $consultores = $this->queryArray($consultores);
            $consultores = $consultores['rows'];

            foreach ($consultores as $consultor) {
                $item = array();
                $item[0] = $consultor['nombre'];
                $item[1] = $consultor['usuario'];
                $item[2] = "<a href='javascript:mostrarEmpresarios(\"". $consultor['idempleado'] ."\");'><i class='fa fa-address-book'></i></a>";
                $registros[] = $item;
            }
            return array("status" => true, "registros" => $registros);

        }else{
            $consultores = 'SELECT e.idempleado, e.nombre, iso.id_organismo 
                            FROM empleados AS e 
                            INNER JOIN accelog_usuarios AS u ON u.idempleado = e.idempleado 
                            INNER JOIN administracion_usuarios AS au ON au.idempleado = u.idempleado 
                            INNER JOIN accelog_usuarios_per AS up ON up.idempleado = au.idempleado 
                            LEFT JOIN netwarstore.inovekia_organismo_consultor AS iso ON iso.id_consultor = e.idempleado
                            WHERE e.visible = -1 AND (iso.id_consultor IS NULL OR iso.id_organismo = '. $organismo .')
                            LIMIT 100;';

            $consultores = $this->queryArray($consultores);
            $consultores = $consultores['rows'];

            foreach ($consultores as $consultor) {
                $item = array();
                $item[0] = $consultor['nombre'];

                if(is_null($consultor['id_organismo'])){
                    $item[1] = "<a href='javascript:seleccionarConsultor(\"". $organismo ."\", \"". $consultor['idempleado'] ."\");'><i class='fa fa-check'></i></a>";            
                }else{
                    $item[1] = "<a href='javascript:eliminarConsultor(\"". $organismo ."\", \"". $consultor['idempleado'] ."\");'><i class='fa fa-close'></i></a>";
                }

                $registros[] = $item;
            }
        }
        return array("status" => true, "registros" => $registros);
    }

    public function seleccionar($organismo, $consultor){
        $seleccionar = "INSERT INTO netwarstore.inovekia_organismo_consultor VALUES(null, ". $consultor .", ". $organismo .", 1, '". date("Y-m-d H:i:s") ."', '". date("Y-m-d H:i:s") ."');";
        $seleccionar = $this->queryArray($seleccionar);
        if($seleccionar['insertId'] > 0){
            $respuesta = array("status" => true);
        } else {
            $respuesta = array("status" => false, "mensaje" => "No fue posible guardar la información");
        }
        return $respuesta;
    }

    public function eliminar($organismo, $consultor){
        $eliminar = "DELETE FROM netwarstore.inovekia_organismo_consultor WHERE id_organismo = ". $organismo ." AND id_consultor = ". $consultor .";";
        $eliminar = $this->queryArray($eliminar);
        if($eliminar['status']){
            $respuesta = array("status" => true);
        } else {
            $respuesta = array("status" => false, "mensaje" => "No fue posible guardar la información");
        }
        
        return $respuesta;
    }

}

?>