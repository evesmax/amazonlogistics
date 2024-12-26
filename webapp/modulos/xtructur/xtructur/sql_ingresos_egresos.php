<?php
if(!isset($_COOKIE['xtructur'])){
    echo 323; exit();
}else{
    $cookie_xtructur = unserialize($_COOKIE['xtructur']);
    $id_obra = $cookie_xtructur['id_obra'];
}
 
include('conexiondb.php');

$SQL = "SELECT id FROM constru_presupuesto WHERE id_obra='$id_obra';";
$result = $mysqli->query($SQL);
$row = $result->fetch_array();
$id_presupuesto=$row['id'];

$oper = $_POST['oper'];
$id_partida = 0;
$sema = $_GET['sema'];
$_search = $_GET['_search'];
$searchField = $_GET['searchField'];
$search = $_GET['searchString'];


$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;
// connect to the database

$mysqli->query("SET NAMES utf8");

$SQL = "SELECT COUNT(*) AS count FROM constru_pedis a
LEFT JOIN constru_pedidos b1 on b1.id_pedid=a.id
LEFT JOIN constru_requis c1  on c1.id=b1.id_requis
LEFT JOIN constru_requisiciones b on b.id_requi=c1.id
LEFT JOIN constru_insumos c on c.id=b.id_clave
left JOIN constru_info_tdo d on d.id_alta=a.solicito
left join constru_entrada_almacen f on f.id_oc=a.id and f.id_insumo=b.id_clave  AND f.id_req=c1.id
WHERE a.estatus=3 AND a.id_obra='$id_obra' AND a.borrado=0 AND b1.borrado=0;";
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



$SQL = "SELECT (b.presupuesto*(b.anticipo/100)) as antiva from constru_generales b WHERE b.id='$id_obra' LIMIT 1;";
$result = $mysqli->query($SQL);
if($result->num_rows>0) {
    $row = $result->fetch_array();
    $antiva=$row['antiva'];
    $masiva=($antiva*1)*0.16;
    $antiva=$antiva+$masiva;
}else{
    $antiva=0.00;
}

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
$ingacumu=0;
$acumulado=0;
$drimp=0;
$ingacumuant=0.00;
for($r=1; $r<=52; $r++){

    $SQL = "SELECT a.semana, a.rem_aut, c.imp_estimacion from constru_bit_remesas a
    LEFT JOIN constru_estimaciones_bit_cliente c on c.id_obra=a.id_obra AND c.semana=a.semana AND c.estatus=1
    WHERE a.id_obra='$id_obra' AND a.semana='$r' GROUP BY a.id ORDER BY a.semana LIMIT 1;";
    $result = $mysqli->query($SQL);


    if($r>1){
        $antiva=0.00;
    }

    if($result->num_rows>0) {
        $row = $result->fetch_array();
        $rema=$row['rem_aut'];
        $rimp=$row['imp_estimacion'];
        $tting=($antiva*1)+($rimp*1);
        $drimp+=$rimp;
        $acumulado+=$rema;
        $ingacumu=($ingacumuant*1)+($tting*1);
        $diferencia=$ingacumu-$acumulado;
        $responce->rows[$i]['semana']=$r;
        $responce->rows[$i]['cell']=array($r,$rema,$acumulado,$antiva,$rimp,$tting,$ingacumu,$diferencia);
        $i++;
    }else{
        $row = $result->fetch_array();
        $rema=0.00;
        $rimp=0.00;
        $tting=($antiva*1)+($rimp*1);
        $drimp+=$rimp;
        $acumulado+=$rema;
        $ingacumu=($ingacumuant*1)+($tting*1);
        $diferencia=$ingacumu-$acumulado;
        $responce->rows[$i]['semana']=$r;
        $responce->rows[$i]['cell']=array($r,$rema,$acumulado,$antiva,$rimp,$tting,$ingacumu,$diferencia);
        $i++;
    }

$ingacumuant=$ingacumu;

        
} 
echo json_encode($responce);
?>