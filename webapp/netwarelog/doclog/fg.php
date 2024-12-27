<?php
	


	include("clases/clcontroles.php");
	include("../catalog/clases/clutilerias.php");
	
	include("../catalog/conexionbd.php");


	//CSRF
	$reset_vars = false;
	include("../catalog/clases/clcsrf.php");	
	if(!$csrf->check_valid('post')){
			//$accelog_access->raise_404(); 
			exit();
	}

echo "Linea 19";

	