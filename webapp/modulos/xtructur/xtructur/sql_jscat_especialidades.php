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
  $mysqli->query("UPDATE constru_cat_especialidad SET borrado=1 WHERE id in ($id);");
  exit();

}
if(isset($oper) && $oper=='add'){
  $especialidad = $_POST['especialidad'];
  //echo "INSERT INTO constru_famat (id_obra,nomfam) VALUES ('$id_obra','$nomfam');";
  $mysqli->query("INSERT INTO constru_cat_especialidad (especialidad, id_obra) VALUES ('$especialidad','$id_obra');");
  exit();
}

if(isset($oper) && $oper=='edit'){
  $id = $_POST['id'];
  $especialidad = $_POST['especialidad'];
  $mysqli->query("UPDATE constru_cat_especialidad SET especialidad='$especialidad' WHERE id='$id';");
  exit();
}

$SQL = "SELECT COUNT(*) AS count FROM constru_cat_especialidad WHERE borrado=0;";
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

 $SQL = "SELECT a.especialidad, concat('ID-',a.id) as clave, a.id FROM constru_cat_especialidad a WHERE 1=1 ".$cad." AND (id_obra='$id_obra' OR id=1 OR id=2) AND a.borrado=0 ORDER BY $sidx $sord LIMIT $start , $limit";
$result = $mysqli->query($SQL);


$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;
while($row = $result->fetch_array()) {
  $responce->rows[$i]['id']=$row['id'];
  $responce->rows[$i]['cell']=array($row['clave'],$row['especialidad']);
  $i++;
}        
echo json_encode($responce);
?>