<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

class MedicoModel extends Connection {
    public function indexGridProductos($limit){
       // $query = "SELECT * from app_productos order by id asc";
/*  COMENTE ESTA CONSULTA PARA QUITAR EL CAMPO Y LA TABLA DE USUARIOS Y EMPLEADOS PARA HACER LA CONSULTA MAS RAPIDA ADEMAS DE LOS LIMITES
        $query = "SELECT us.idempleado ,c.id_proveedor as idProve,c.costo,p.*";
        $query .= "from app_productos p ";
        $query .= "left join app_costos_proveedor c on c.id_producto=p.id ";
        $query .= "left join accelog_usuarios us on us.idempleado=p.idempleado ";
        $query .= "group by p.id ";
        $query .= "order by p.id asc ".$limit;
*/        //echo $query;
        $query = "SELECT c.id_proveedor as idProve,c.costo,p.*";
        $query .= "from app_productos p ";
        $query .= "left join app_costos_proveedor c on c.id_producto=p.id ";
        $query .= "where p.status = 1 or p.status = 0 ";
        $query .= "group by p.id ";
        $query .= "order by p.id asc ";
        $rest = $this->queryArray($query);
        
        return array('productos' => $rest['rows'], 'total' => $rest['total'] );
    }

    public function guardarLay($dato)
    {
        $sql = "INSERT INTO app_destinos (activo, clave , nombre ,  id_clasificacion)
                VALUES  ('{$dato[1]}' , '{$dato[2]}' , '{$dato[3]}' , '99')";
                $this->queryArray( $sql );
    }

    public function validarProductos($val)
    {
        $sql = "SELECT  clave, nombre
                FROM    app_destinos
                GROUP BY clave 
                HAVING COUNT(clave) > 1; ";

        $res = $this->query($sql);
        return $res;
    }

    public function traeCargados($clas)
    {
        $sql = "SELECT  id, activo estatus, clave, nombre
                FROM    app_destinos WHERE id_clasificacion = $clas";
        $res = $this->query($sql);
        return $res;
    }

    public function borrar($val)
    {
        $sql = "DELETE FROM app_destinos WHERE id_clasificacion = $val;";
        $this->query($sql);
    }

    public function confirmar($val)
    {
        $sql = "UPDATE app_destinos SET id_clasificacion = NULL WHERE id_clasificacion = $val";
        $this->query($sql);
    }

    public function inactivarLay($idProd,$num)
    {
        $myQuery = "UPDATE app_destinos SET id_clasificacion = $num WHERE id = $idProd";
        $this->query($myQuery);
        return $myQuery;
    }

    public function reactivarLay($idProd,$num)
    {
        $myQuery = "UPDATE app_destinos SET id_clasificacion = $num WHERE id = $idProd";
        $this->query($myQuery);
        return $myQuery;
    }

    function guardar($clave, $nombre, $activo)
    {
        $sql = "INSERT INTO app_destinos (clave , nombre , activo)
                VALUES  ('$clave' , '$nombre' , $activo)";
        $res = $this->queryArray( $sql );
        return $res['insertId'];
    }
    function mostrar($id="")
    {
        $sql = "SELECT  *
                FROM    medicos
                #WHERE   id LIKE '%$id%'; ";
        $res = $this->queryArray( $sql );
        return $res['rows'];
    }
    function editar($id, $clave, $nombre, $activo)
    {
        $sql = "UPDATE  app_destinos
                SET     clave='$clave' ,
                        nombre='$nombre' ,
                        activo='$activo'
                WHERE   id = '$id'; ";
        $res = $this->queryArray( $sql );
        return $res;
    }

    public function paises(){
        $query = 'SELECT * from paises;';
        $result = $this->queryArray($query);
        return $result['rows'];
    }
    public function estados2($idPais){
        $query = 'Select * from estados where idpais = '.$idPais;
        $result = $this->queryArray($query);
        return $result['rows'];
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

    public function datosMedico($idMedico){
        $query = "SELECT * from medicos where id='$idMedico'";
        $result = $this->queryArray($query);

        $idTmp = $result['rows'][0]['idPais'];
        $sql = "SELECT  pais
                FROM    paises
                WHERE   idpais = $idTmp";
        $res = $this->queryArray($sql);
        $result['rows'][0]['descPais'] = $res['rows'][0]['pais'];

        $idTmp = $result['rows'][0]['idEstado'];
        $sql = "SELECT  estado
                FROM    estados
                WHERE   idestado = $idTmp";
        $res = $this->queryArray($sql);
        $result['rows'][0]['descEstado'] = $res['rows'][0]['estado'];

        $idTmp = $result['rows'][0]['idMunicipio'];
        $sql = "SELECT  municipio
                FROM    municipios
                WHERE   idmunicipio = $idTmp";
        $res = $this->queryArray($sql);
        $result['rows'][0]['descMunicipio'] = $res['rows'][0]['municipio'];

        $idTmp = $result['rows'][0]['idVendedor'];
        $sql = "SELECT idadmin, CONCAT( CONCAT ( CONCAT( CONCAT(nombre, ' ') , apellidos), ' | ') , nombreusuario) nombre
                FROM administracion_usuarios 
                WHERE    idadmin = $idTmp";
        $res = $this->queryArray($sql);
        $result['rows'][0]['descVendedor'] = $res['rows'][0]['nombre'];
        
        return $result['rows'][0];
    }

    public function buscaVendedores($term) {
        /*obtiene los vendedores*/
        $sql = "SELECT idadmin id, CONCAT( CONCAT ( CONCAT( CONCAT(nombre, ' ') , apellidos), ' | ') , nombreusuario) text
                FROM administracion_usuarios 
                WHERE nombre LIKE '%$term%' OR  apellidos LIKE '%$term%' OR nombreusuario LIKE '%$term%' 
                ORDER BY nombre, apellidos, nombreusuario ";

        $result = $this->queryArray($sql);
        //print_r($result["rows"]);
        return $result;

    }

    public function updateMedico($id,$codigo,$nombre,$cedula,$direccion,$numext,$numint,$colonia,$cp,$pais,$estado,$municipio,$ciudad,$tel1,$comisionventa,$comisioncobranza,$vendedor){

        $update = " UPDATE medicos
                    SET 
                    codigo='$codigo',nombre='$nombre',cedula='$cedula',dircalle='$direccion',direxterior='$numext',dirinterior='$numint',colonia='$colonia',codigopostal='$cp',idPais='$pais',idEstado='$estado',idMunicipio='$municipio',ciudad='$ciudad',telefono='$tel1',comisionventa='$comisionventa',comisioncobranza='$comisioncobranza',idVendedor='$vendedor'
                    WHERE id='$id';";
        $resUpdate = $this->queryArray($update);

        return array('status' => true );

    }

    public function guardaMedico($codigo,$nombre,$cedula,$direccion,$numext,$numint,$colonia,$cp,$pais,$estado,$municipio,$ciudad,$tel1,$comisionventa,$comisioncobranza,$vendedor){

        $update = " INSERT INTO medicos (codigo, nombre, cedula, dircalle, direxterior, dirinterior, colonia, codigopostal, idPais, idEstado, idMunicipio, ciudad, telefono, comisionventa, comisioncobranza, idVendedor)
                    VALUES
                        ('$codigo', '$nombre', '$cedula', '$direccion', '$numext', '$numint', '$colonia', '$cp', '$pais', '$estado', '$municipio', '$ciudad', '$tel1', '$comisionventa', '$comisioncobranza', '$vendedor');";
        $resUpdate = $this->queryArray($update);

        return array('status' => true );

    }
}
?>
