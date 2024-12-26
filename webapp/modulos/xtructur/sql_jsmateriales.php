<?php
if(!isset($_COOKIE['xtructur'])){
  exit();
}else{
  $cookie_xtructur = unserialize($_COOKIE['xtructur']);
  $id_obra = $cookie_xtructur['id_obra'];
}

$oper = $_POST['oper'];
$_search = $_GET['_search'];
$searchField = $_GET['searchField'];
$search = $_GET['searchString'];

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;

include('conexiondb.php');
$mysqli->query("SET NAMES utf8");

if(isset($oper) && $oper=='del'){
  $id = $_POST['id'];
  $mysqli->query("UPDATE constru_famat SET borrado=1 WHERE id in ($id);");
  exit();

}
if(isset($oper) && $oper=='add'){
  $nomfam = $_POST['nomfam'];
  //echo "INSERT INTO constru_famat (id_obra,nomfam) VALUES ('$id_obra','$nomfam');";
  $mysqli->query("INSERT INTO constru_famat (nomfam) VALUES ('$nomfam');");
  exit();
}

if(isset($oper) && $oper=='edit'){
  $id = $_POST['id'];
  $nomfam = $_POST['nomfam'];
  $mysqli->query("UPDATE constru_famat SET nomfam='$nomfam' WHERE id='$id';");
  exit();
}

$SQL = "SELECT COUNT(*) AS count FROM constru_famat WHERE borrado=0;";
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

$SQL = "SELECT a.*, concat('FAMMAT-',a.id) as clave FROM constru_famat a WHERE 1=1 ".$cad." AND a.borrado=0 ORDER BY $sidx $sord LIMIT $start , $limit";
$result = $mysqli->query($SQL);


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
while($row = $result->fetch_array()) {
  $responce->rows[$i]['id']=$row['id'];
  $responce->rows[$i]['cell']=array($row['clave'],$row['nomfam']);
  $i++;
}        
echo json_encode($responce);
?>