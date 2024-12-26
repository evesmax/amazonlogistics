<?php
session_start();
$idperfil = preg_replace('/\(|\)/','',$_SESSION['accelog_idperfil']);
$oper = $_POST['oper'];

$_search = $_GET['_search'];
$searchField = $_GET['searchField'];
$search = $_GET['searchString'];

$page = $_GET['page']; // get the requested page
$limit = $_GET['rows']; // get how many rows we want to have into the grid
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
$sord = $_GET['sord']; // get the direction
if(!$sidx) $sidx =1;
// connect to the database

include('conexiondb.php');
$mysqli->query("SET NAMES utf8");

if(isset($oper) && $oper=='del'){
	$id = $_POST['id'];
	$mysqli->query("UPDATE constru_generales SET borrado=1 WHERE id in ($id);");
	exit();
}

if(isset($oper) && $oper=='add'){
	$licitacion = $_POST['licitacion'];
	$contrato = $_POST['contrato'];
	$cliente = $_POST['cliente'];
	$construye = $_POST['construye'];
	$obra = $_POST['obra'];
	$localizacion = $_POST['localizacion'];
	$clave = $_POST['clave'];
	$inicio = $_POST['inicio'];
	$termino = $_POST['termino'];
	//$hon = $_POST['hon'];
	$iva = $_POST['iva'];
	//$fsr = $_POST['fsr'];
	$presupuesto = $_POST['presupuesto'];
	$anticipo = $_POST['anticipo'];
	$ade1 = $_POST['ade1'];
	$ade2 = $_POST['ade2'];
	$ade3 = $_POST['ade3'];
	$ade4 = $_POST['ade4'];
	$director = $_POST['director'];
	$superintendencia = $_POST['superintendencia'];
	$control = $_POST['control'];
	$sup_imss = $_POST['supervision'];
	/*$elaboro = $_POST['elaboro'];
	$revision = $_POST['revision'];
	$autorizacion = $_POST['autorizacion'];
	*/
	$id_presupuesto = $_POST['id_presupuesto'];

	$fecha_contrato = $_POST['fecha_contrato'];
	$no_compromiso = $_POST['no_compromiso'];
	$fecha_compromiso = $_POST['fecha_compromiso'];
	$no_obra = $_POST['no_obra'];
	$telefono = $_POST['telefono'];

	$i=$mysqli->query("INSERT INTO constru_generales (licitacion,contrato,cliente,construye,obra,localizacion,clave,inicio,termino,iva,presupuesto,anticipo,director,superintendencia,control,sup_imss, id_presupuesto,fecha_contrato,no_compromiso,fecha_compromiso,no_obra,ade1,ade2,ade3,ade4,telefono) VALUES ('$licitacion','$contrato','$cliente','$construye','$obra','$localizacion','$clave','$inicio','$termino','$iva','$presupuesto','$anticipo','$director','$superintendencia','$control','$sup_imss','$id_presupuesto','$fecha_contrato','$no_compromiso','$fecha_compromiso','$no_obra','$ade1','$ade2','$ade3','$ade4','$telefono');");
	if($i==1){
		$id_obra=$mysqli->insert_id;
		$mysqli->query("INSERT INTO constru_proforma (id_obra) VALUES ('$id_obra');");
		$mysqli->query("INSERT INTO constru_proforma2 (id_obra) VALUES ('$id_obra');");
		$mysqli->query("INSERT INTO constru_desgloce (id_obra,id_cc) SELECT $id_obra,id FROM constru_cuentas_cargo WHERE id_costo=25;");

			$mysqli->query("INSERT INTO constru_familias (id_obra,id_categoria_familia,familia) VALUES
				('$id_obra',2,'MAESTRO'),
				('$id_obra',2,'SOBRESTANTE'),
				('$id_obra',2,'CABO'),
				('$id_obra',2,'OFICIAL'),
				('$id_obra',2,'OPERADOR'),
				('$id_obra',2,'MANIOBRISTA'),
				('$id_obra',2,'AYUDANTE ESPECIALIZADO'),
				('$id_obra',2,'PEON');");


			$mysqli->query("INSERT INTO constru_familias (id_obra,id_categoria_familia,familia) VALUES
				('$id_obra',1,'DIRECTOR'),
				('$id_obra',1,'GERENTE'),
				('$id_obra',1,'SUPERINTENDENTE'),
				('$id_obra',1,'JEFE INGENIEROS'),
				('$id_obra',1,'COORDINADOR OBRA'),
				('$id_obra',1,'COORDINADOR CONTROL OBRA'),
				('$id_obra',1,'RESIDENTE'),
				('$id_obra',1,'AUXILIAR RESIDENTE'),
				('$id_obra',1,'AUXILIAR CONTROL'),
				('$id_obra',1,'TOPOGRAFO'),
				('$id_obra',1,'PROYECTISTA'),
				('$id_obra',1,'JEFE TALLER'),
				('$id_obra',1,'DIBUJANTE'),
				('$id_obra',1,'COORDINADOR COMPRAS'),
				('$id_obra',1,'JEFE COMPRAS'),
				('$id_obra',1,'ADMINISTRADOR OBRA'),
				('$id_obra',1,'CONTADOR'),
				('$id_obra',1,'AUXILIAR ADMINISTRATIVO'),
				('$id_obra',1,'JEFE ALMACEN'),
				('$id_obra',1,'SECRETARIA'),
				('$id_obra',1,'JEFE SEGURIDAD E HIGIENE'),
				('$id_obra',1,'CHOFER'),
				('$id_obra',1,'AUXILIAR DE SEGURIDAD E HIGIENE'),
				('$id_obra',1,'AUXILIAR ALMACEN'),
				('$id_obra',1,'AYUDANTE ALMACEN'),
				('$id_obra',1,'CHECADOR TIEMPO'),
				('$id_obra',1,'AFANADORA'),
				('$id_obra',1,'VELADOR Y/O VIGILANTE'),
				('$id_obra',1,'MECANICO'),
				('$id_obra',1,'OTROS');");

			$mysqli->query("INSERT INTO constru_config (id_obra,autorizaciones) VALUES ('$id_obra',0);");

			$idusr = $_SESSION['accelog_idempleado'];



			$mysqli->query("INSERT INTO constru_obrasusuario (iduser, idobra) VALUES ($idperfil, $id_obra);");

	}
	exit();
}

if(isset($oper) && $oper=='edit'){

	$id = $_POST['id'];
	$licitacion = $_POST['licitacion'];
	$contrato = $_POST['contrato'];
	$cliente = $_POST['cliente'];
	$construye = $_POST['construye'];
	$obra = $_POST['obra'];
	$localizacion = $_POST['localizacion'];
	$clave = $_POST['clave'];
	$inicio = $_POST['inicio'];
	$termino = $_POST['termino'];
	//$hon = $_POST['hon'];
	$iva = $_POST['iva'];
	//$fsr = $_POST['fsr'];
	$presupuesto = $_POST['presupuesto'];
	$anticipo = $_POST['anticipo'];
	$ade1 = $_POST['ade1'];
	$ade2 = $_POST['ade2'];
	$ade3 = $_POST['ade3'];
	$ade4 = $_POST['ade4'];
	$director = $_POST['director'];
	$superintendencia = $_POST['superintendencia'];
	$control = $_POST['control'];
	$sup_imss = $_POST['supervision'];
	/*$elaboro = $_POST['elaboro'];
	$revision = $_POST['revision'];
	$autorizacion = $_POST['autorizacion'];
	*/
	$id_presupuesto = $_POST['id_presupuesto'];

	$fecha_contrato = $_POST['fecha_contrato'];
	$no_compromiso = $_POST['no_compromiso'];
	$fecha_compromiso = $_POST['fecha_compromiso'];
	$no_obra = $_POST['no_obra'];
	$telefono = $_POST['telefono'];


	$mysqli->query("UPDATE constru_generales SET licitacion='$licitacion', contrato='$contrato', cliente='$cliente', construye='$construye', obra='$obra', localizacion='$localizacion', clave='$clave', inicio='$inicio', termino='$termino', iva='$iva', presupuesto='$presupuesto', anticipo='$anticipo', director='$director', superintendencia='$superintendencia', control='$control', sup_imss='$sup_imss', id_presupuesto='$id_presupuesto', fecha_contrato='$fecha_contrato', no_compromiso='$no_compromiso', fecha_compromiso='$fecha_compromiso', no_obra='$no_obra', ade1='$ade1', ade2='$ade2', ade3='$ade3', ade4='$ade4', telefono='$telefono' WHERE id='$id';");
	exit();
}

$SQL = "SELECT COUNT(*) AS count FROM constru_generales WHERE borrado=0;";
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

if($_search=='true'){
	$soper=$_GET['searchOper'];
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



	$nfiltro='  ';	


if($idperfil==2){
$SQL="SELECT a.*, b.nombre nomconst FROM constru_generales a LEFT JOIN constru_contratista b on b.id=a.construye  WHERE 1=1 ".$cad." AND a.borrado=0 ".$nfiltro."  ORDER BY $sidx $sord LIMIT $start , $limit";
}else{
$SQL="SELECT a.*, b.nombre nomconst FROM constru_generales a LEFT JOIN constru_contratista b on b.id=a.construye
	left join constru_obrasusuario c on  a.id=c.idobra  
  WHERE 1=1 ".$cad." AND a.borrado=0 ".$nfiltro." AND c.iduser='$idperfil' group by a.id ORDER BY $sidx $sord LIMIT $start , $limit";
}
$result = $mysqli->query($SQL);

$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$i=0;

while($row = $result->fetch_array()) {
    $responce->rows[$i]['id']=$row['id'];
    $responce->rows[$i]['cell']=array($row['obra'],$row['cliente'],$row['licitacion'],$row['nomconst'],$row['clave'],$row['contrato'],$row['localizacion'],$row['inicio'],$row['termino'],$row['iva'],$row['presupuesto'],$row['anticipo'],$row['ade1'],$row['ade2'],$row['ade3'],$row['ade4'],$row['director'],$row['superintendencia'],$row['control'],$row['sup_imss'],$row['fecha_contrato'],$row['no_compromiso'],$row['fecha_compromiso'],$row['no_obra'],$row['telefono']);
    $i++;
}        
echo json_encode($responce);
?>