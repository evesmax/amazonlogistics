<?php
         // Si session_start() no está dentro de support.php o webconfig.php, 
		// descomenta la siguiente línea:
		// session_start();

		ini_set('session.cookie_httponly',1);
		include 'support.php';
		include dirname(__FILE__).'/../webconfig.php';
		include 'clases/clconexion.php';

		// --- INICIO DE VALIDACIÓN DE SESIÓN ---
		if (!isset($_SESSION["accelog_idempleado"]) || empty($_SESSION["accelog_idempleado"])) {
			echo "<script type='text/javascript'>
					alert('Se caduco la sesion');
					window.location.href = dirname(__FILE__).'/webapp/netwarelog/accelog/index.php';
				  </script>";
			exit(); // Detiene la ejecución para que no se procese la base de datos ni el resto del HTML
		}
		// --- FIN DE VALIDACIÓN ---
	
		//$conexion = new conexion($servidor,$usuariobd,$clavebd,$bd,$instalarbase,$tipobd);
		$conexion = new conexion($servidor,$usuariobd,$clavebd,$bd,$instalarbase,$tipobd);

		date_default_timezone_set('Etc/GMT+6');	

		//echo $_SESSION["accelog_idempleado"];
	
	/*
        ini_set('session.cookie_httponly',1);
		include 'support.php';
		include dirname(__FILE__).'/../webconfig.php';
		include 'clases/clconexion.php';
	
		//$conexion = new conexion($servidor,$usuariobd,$clavebd,$bd,$instalarbase,$tipobd);
		$conexion = new conexion($servidor,$usuariobd,$clavebd,$bd,$instalarbase,$tipobd);

		date_default_timezone_set('Etc/GMT+6');	

		echo $_SESSION["accelog_idempleado"];
	*/
       
?>
