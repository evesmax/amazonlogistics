<?php
$_REQUEST['cont'] = str_replace("<img id=\"logo_empresa\" src=\"", "<b style='color:#FFFFFF;'>", $_REQUEST['cont']);
$_REQUEST['cont'] = str_replace("\" height=\"55\">", "</b>", $_REQUEST['cont']);

if(!$_REQUEST['name']){
						$_REQUEST['name']="reporte";
						}else{
							$_REQUEST['name'] = str_replace(" ","",$_REQUEST['name']);
							$_REQUEST['name'] = str_replace(".","",$_REQUEST['name']);
							}

header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=".$_REQUEST['name'].".xls");

echo utf8_decode($_REQUEST['cont']);

//Nuevo Commit
?>
