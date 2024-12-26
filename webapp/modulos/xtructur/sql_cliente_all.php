<?php
$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
if(!isset($_COOKIE['xtructur'])){
  exit();
}else{
  $cookie_xtructur = unserialize($_COOKIE['xtructur']);
  $id_obra = $cookie_xtructur['id_obra'];
}

$oper = $_POST['oper'];

$id_des = $_GET['id_des'];
$sestmp = $_GET['sestmp'];
$_search = $_GET['_search'];
$searchField = $_GET['searchField'];
$search = $_GET['searchString'];

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction



//Filtros nuevos
if(!isset($_GET['filtro_semana'])){ $filtro_semana=0; }else{ $filtro_semana=$_GET['filtro_semana']; }
if(!isset($_GET['filtro_mes'])){ $filtro_mes=0; }else{ $filtro_mes=$_GET['filtro_mes']; }
if(!isset($_GET['filtro_estatus'])){ $filtro_estatus='x'; }else{ $filtro_estatus=$_GET['filtro_estatus']; }
if(!isset($_GET['filtro_proveedor'])){ $filtro_proveedor=0; }else{ $filtro_proveedor=$_GET['filtro_proveedor']; }
//Fin filtros nuevos

if(!$sidx) $sidx =1;
// connect to the database
include('conexiondb.php');
$mysqli->query("set names 'utf8'");

$SQL = "SELECT correo_can FROM constru_config WHERE id_obra='$id_obra';";
$result = $mysqli->query($SQL);
$row = $result->fetch_array();
$correo=$row['correo_can'];

if(isset($oper) && $oper=='del'){
    $id = $_POST['id'];
    $mysqli->query("UPDATE constru_estimaciones_subcontratista SET borrado=1 WHERE id in ($id);");
    exit();
}

if(isset($oper) && $oper=='add'){
    $id_clave = $_POST['id_clave'];
    $vol_anterior = $_POST['vol_anterior'];
    $SQL="SELECT id FROM constru_estimaciones_subcontratista WHERE id_obra='$id_obra' AND sestmp='$sestmp' AND id_bit_subcontratista=0 and borrado=0 AND id_insumo='$id_clave';";
    $result = $mysqli->query($SQL);
    if($result->num_rows>0) {
        echo 'RP';
        exit();
    }

    $vol_tope = $_POST['vol_tope'];
    $vol_estimacion = $_POST['vol_estimacion'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $mysqli->query("INSERT INTO constru_estimaciones_subcontratista (id_obra,id_insumo,id_bit_subcontratista,vol_tope,sestmp,vol_estimacion,vol_anterior) VALUES ('$id_obra', '$id_clave',0, '$vol_tope','$sestmp','$vol_estimacion','$vol_anterior');");
    exit();
}

if(isset($oper) && $oper=='edit'){
    $id = $_POST['id'];
    $id_clave = $_POST['id_clave'];

    $cantidad = $_POST['cantidad'];

    $fecha_entrega = $_POST['fecha_entrega'];

    $mysqli->query("UPDATE constru_requisiciones SET id_clave='$id_clave', cantidad='$cantidad', fecha_entrega='$fecha_entrega' WHERE id='$id';");
    exit();
}


$SQL="SELECT COUNT(*) AS count FROM constru_requisiciones WHERE id_obra='$id_obra' AND sestmp>0 AND id_requi=0 and borrado=0;";
$result = $mysqli->query($SQL);
$row = $result->fetch_array();
$count = $row['count'];

if( $count >0 ) {
    $total_pages = ceil($count/$limit);
} else {
    $total_pages = 0;
}
if ($page > $total_pages) $page=$total_pages;
$start = $limit*$page - $limit; // do not put $limit*($page - 1)
if($start<0) $start=0;
if($_search=='true'){
    $soper=$_GET['searchOper'];
    $searchField='a.'.$searchField;
    if($soper=='eq'){
        $cad=" AND ".$searchField."='".$search."' ";
    }elseif($soper=='ne'){
        $cad=" AND ".$searchField."!='".$search."' ";
    }elseif($soper=='cn'){
        $cad=" AND ".$searchField." LIKE  '%".$search."%' ";
    }elseif($soper=='nc'){
        $cad=" AND ".$searchField." NOT LIKE  '%".$search."%' ";
    }elseif($soper=='lt'){
        $cad=" AND ".$searchField." <  ".$search." ";
    }elseif($soper=='gt'){
        $cad=" AND ".$searchField." >  ".$search." ";
    }else{
        echo 'Operador de busqueda incorrecto';
        exit();
    }
}else{
    $cad='';
}

/*
WHEN 0 THEN concat('Estimacion-',a.id,' Semana: ',a.xxano,' ','<input type=\"button\" value=\"Cancelar\" style=\"cursor:pointer\" onclick=\"autorizarestAll(\'sub\',',a.id,',',2,');\" > ',' <input type=\"button\" value=\"Autorizar\" style=\"cursor:pointer\" onclick=\"autorizarestAll(\'sub\',',a.id,',',1,');\" >')
WHEN 2 THEN concat('Estimacion-',a.id,' Semana: ',a.xxano,' ','<font color=\"#ff0000\">Estimacion Cancelada</font>')
WHEN 1 THEN concat('Estimacion-',a.id,' Semana: ',a.xxano,' ','<font color=\"#070\">Estimacion Autorizada</font>')
END as Estimacion,

*/

//Filtors nuevos
$cadenafiltro = '';
if($filtro_mes!=0){
   $filtro_semana=0; 
}

if($filtro_semana!=0){

    $cadenafiltro.=" AND yearweek(fecha,1)='".$filtro_semana."' ";
}

if($filtro_mes!=0){
    $ym = explode('-', $filtro_mes);
    $cadenafiltro.=" AND YEAR(fecha,1) = '".$ym[0]."' AND MONTH(fecha) = '".$ym[1]."'";
}

if($filtro_estatus!='x'){
    $cadenafiltro.=" AND b.estatus = '".$filtro_estatus."' ";
}

if($filtro_proveedor!=0){
    $cadenafiltro.=" AND b.id_cliente = '".$filtro_proveedor."' ";
}




if($correo==1){

$SQL = "SELECT
concat(bx.cliente) as Cliente, b.estatus,
CASE b.estatus 
WHEN 0 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<input type=\"button\" value=\"Cancelar\" style=\"cursor:pointer\" data-toggle=\"modal\" data-target=\"#mailmodal\"  data-eid=',b.id, ' > ',' <input type=\"button\" value=\"Autorizar\" style=\"cursor:pointer\" onclick=\"autorizarestAll(\'cli\',',b.id,',',1,',',0,');\" >')
WHEN 2 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<font color=\"#ff0000\">Estimacion Cancelada</font>')
WHEN 1 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<font color=\"#070\">Estimacion Autorizada</font> ')
WHEN 4 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<font color=\"#070\">Estimacion Autorizada</font> ')
END as Estimacion, a.id_insumo,
c.precio_costo, c.codigo, c.descripcion, c.unidtext, a.vol_anterior, a.vol_estimacion, fac.folio, b.id
    from constru_estimaciones_bit_cliente b
    inner join constru_estimaciones_cliente a on a.id_bit_cliente=b.id
    inner join constru_generales bx on bx.id=b.id_cliente
    left join constru_recurso c on c.id=a.id_insumo
    left join app_respuestaFacturacion fac on fac.idSale=b.id and fac.proviene=4
    WHERE  b.id_obra='$id_obra'".$cadenafiltro." AND a.sestmp>0  AND b.borrado=0  ORDER BY $sidx $sord LIMIT $start,$limit";
$result = $mysqli->query($SQL);

$count=$result->num_rows;
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;}

else{

$SQL = "SELECT
concat(bx.cliente) as Cliente, b.estatus,
CASE b.estatus 
WHEN 0 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<input type=\"button\" value=\"Cancelar\" style=\"cursor:pointer\" onclick=\"autorizarestAll(\'cli\',',b.id,',',2,',','0',');\" > ',' <input type=\"button\" value=\"Autorizar\" style=\"cursor:pointer\" onclick=\"autorizarestAll(\'cli\',',b.id,',',1,',',0,');\" >')
WHEN 2 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<font color=\"#ff0000\">Estimacion Cancelada</font>')
WHEN 1 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<font color=\"#070\">Estimacion Autorizada</font>')
WHEN 4 THEN concat('Estimacion-',b.id,' Fecha: ',substr(b.fecha,1,10),' ','<font color=\"#070\">Estimacion Autorizada</font>')
END as Estimacion, a.id_insumo,
c.precio_costo, c.codigo, c.descripcion, c.unidtext, a.vol_anterior, a.vol_estimacion, fac.folio, b.id
    from constru_estimaciones_bit_cliente b
    inner join constru_estimaciones_cliente a on a.id_bit_cliente=b.id
    inner join constru_generales bx on bx.id=b.id_cliente
    left join constru_recurso c on c.id=a.id_insumo
    left join app_respuestaFacturacion fac on fac.idSale=b.id and fac.proviene=4
    WHERE b.id_obra='$id_obra'".$cadenafiltro." AND a.sestmp>0  AND b.borrado=0  ORDER BY $sidx $sord LIMIT $start,$limit";
$result = $mysqli->query($SQL);

$count=$result->num_rows;
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;}


$i=0;

session_start();
while($row = $result->fetch_array()) {


if (in_array($_SESSION['accelog_nombre_instancia'], $cookie_xtructur['instancias_acontia'])){
    if($row['estatus']==1){
        $facturar="<input type=\"button\" value=\"Facturar\" style=\"cursor:pointer\" onclick=\"facturaCliente('".$row['id']."');\" >";
        $row['Estimacion']=$row['Estimacion'].=' '.$facturar;
    }
    if($row['estatus']==4){
        $ver=" <input type=\"button\" value=\"XML\" style=\"cursor:pointer\" onclick=\"verXML('".$row['folio']."');\" >  <input type=\"button\" value=\"PDF\" style=\"cursor:pointer\" onclick=\"verPDF('".$row['folio']."');\" >";
        $row['Estimacion']=$row['Estimacion'].=' '.$ver;
    }
    

    
    
    //$ver="<input type=\"button\" value=\"PDF\" style=\"cursor:pointer\" onclick=\"verPDF(',fac.folio,');\" > <input type=\"button\" value=\"XML\" style=\"cursor:pointer\" onclick=\"verXML(',fac.folio,');\" >"
    
}else{
 //echo 333;
}


    $SQL ="SELECT sum(unidad) as totcant FROM constru_recurso WHERE id_obra='$id_obra' AND codigo='".$row['codigo']."' AND borrado=0;";

    $result3 = $mysqli->query($SQL);
    $row3 = $result3->fetch_array();
    $vol_tope=$row3['totcant'];
    $total=$vol_tope*$row['precio_costo'];

    $SQL ="SELECT sum(a.vol_estimacion) as vol_anterior from constru_estimaciones_cliente a 
    inner join constru_estimaciones_bit_cliente b on b.id=a.id_bit_cliente AND b.estatus!=2
    where a.id_bit_cliente>0 AND a.sestmp>0 AND a.id_obra='$id_obra' AND a.id_insumo='".$row['id_insumo']."';";

    $result2 = $mysqli->query($SQL);
    $row2 = $result2->fetch_array();
    $vol_anterior = $row2['vol_anterior']-$row['vol_estimacion'];
    $vol_acumulado = $row['vol_anterior']+$row['vol_estimacion'];
    $vol_ejecutar = $vol_tope-$vol_acumulado;
    $imp_est=$row['precio_costo']*$row['vol_estimacion'];


    $responce->rows[$i]['id']=$i;
    $responce->rows[$i]['cell']=array($row['Cliente'],$row['Estimacion'],$row['codigo'],$row['descripcion'],$row['unidtext'],$vol_tope,$row['precio_costo'],$total,$row['vol_anterior'],$row['vol_estimacion'],$vol_acumulado,$vol_ejecutar,$imp_est);
    $i++;
}        

echo json_encode($responce);
?>