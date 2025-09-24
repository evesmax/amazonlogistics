<?php
	//phpinfo();

	

new DateTimeZone('UTC-6');

// A partir de aquí, todo el código que use funciones de fecha/hora
// utilizará la zona horaria de la Ciudad de México.

echo date('Y-m-d H:i:s');
	 
if (PHP_SAPI != "cli") {
    die("Please run this test from CLI!
");
}
 
ini_set("display_errors", 0);
ini_set("output_buffering", 0);
error_reporting(0);
if (!ini_get("safe_mode")) {
    set_time_limit(1);
}
 
echo "Testing float behaviour. If this script hangs or terminates with an error ".
     "message due to maximum execution time limit being reached, you should ".
     "update your PHP installation asap!
";
echo "For more information refer to .
";
$d = (double)"2.2250738585072011e-308";
echo "Your system seems to be safe.
";

	
?>
