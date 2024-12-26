<?php

ini_set("display_errors",0);

//HTTPOnly
ini_set('session.cookie_httponly',1);
// Estableciendo sesión de idioma a nivel instancia

$txtStylePath = "";

//Avoid session error
session_regenerate_id(); // replace the Session ID
$ok = @session_start();
if(!$ok){
	session_regenerate_id(true); // replace the Session ID
	session_start();
}
if(isset($_COOKIE["PHPSESSID"])){
	unset($_COOKIE["g"]);
	unset($_COOKIE["PHPSESSID"]);
	session_destroy();
	session_start();
}

/**
 * VERIFICA INSTANCIA
 */
$arrInstanciaG = explode("/",$_SERVER['REQUEST_URI']);
if(array_search('facturar',$arrInstanciaG)!=0){
    $strInstanciaG = $arrInstanciaG[array_search('facturar',$arrInstanciaG) - 1];
}else{
    $strInstanciaG = $arrInstanciaG[array_search('webapp',$arrInstanciaG) - 1];
}

$_SESSION["instancia"]=$strInstanciaG;
$reset_vars=true;

//VALIDA VERSION Y SITIO SEGURO
//determinando el servidor
if($_SERVER['SERVER_NAME']=="edu.netwarmonitor.com"){
        $servidor ="unmdbaurora.cyv2immv1rf9.us-west-2.rds.amazonaws.com";
        $usuariobd="unmdb";
        $clavebd="&=98+69Unmdb";
        $bd = "nmdev";
        $accelog_variable = "netappmitranetwarelog1";
}elseif($_SERVER['SERVER_NAME']=="localhost"){
        $servidor ="192.168.1.11";
        $usuariobd="nmdevel";
        $clavebd="nmdevel";
        $bd = "nmdev";
        $accelog_variable = "netappmitranetwarelog1";
}else{
        $servidor  = "34.66.63.218";
        $usuariobd = "nmdevel";
        $clavebd = "nmdevel";
        $bd = "nmdev";
        $accelog_variable = "netappmitranetwarelog1";
}

//DETERMINANDsO BASE DE DATOS
		$objConG = mysqli_connect($servidor,$usuariobd , $clavebd, "netwarstore");
		$strSqlG = "SELECT usuario_db,pwd_db,nombre_db,cobranza FROM customer WHERE instancia = '" . $strInstanciaG . "';";
		$rstWebconfigG = mysqli_query($objConG, $strSqlG);
		while ($objWebconfigG = mysqli_fetch_assoc($rstWebconfigG)) {
				$usuariobd = $objWebconfigG['usuario_db'];
				$clavebd = $objWebconfigG['pwd_db'];
				$bd = $objWebconfigG['nombre_db'];
				$accelog_variable = str_replace('_dbmlog', '', $objWebconfigG['nombre_db']) . "mlog";
				$cobranza=$objWebconfigG['cobranza'];
		}
		unset($objWebconfigG);
		mysqli_free_result($rstWebconfigG);
		unset($rstWebconfigG);

		//Recupera la version del Sistema
		$strSqlG = "SELECT version FROM $bd.netwarelog_version";
		$rstWebconfigG = mysqli_query($objConG, $strSqlG);
		while ($objWebconfigG = mysqli_fetch_assoc($rstWebconfigG)) {
				$version = $objWebconfigG['version'];
		}
		unset($objWebconfigG);
		mysqli_free_result($rstWebconfigG);
		unset($rstWebconfigG);

		mysqli_close($objConG);
		unset($strSqlG);

if($version>=2){
    //Evalua si esta en un sitio inseguro de swer asi lo redirecciona a uno seguro
    $arrInstanciaG = explode("/",$_SERVER['REQUEST_URI']);
    $strInstanciaG = $arrInstanciaG[array_search('webapp',$arrInstanciaG) - 1];
    $dom=$_SERVER['SERVER_NAME'];
    if($dom=='www.netwarmonitor.mx'){
            //Redirigir a Sitio Seguro
            header('Location: https://www.netwarmonitor.com/clientes/'.$strInstanciaG.'/webapp/netwarelog/accelog/index.php');
    }
}




/**
 * Preparing CSRF protection
 */
include "../catalog/clases/clcsrf.php";

/**
 * Getting gettext files for translation
 */

require_once("../../../lib/streams.php");
require_once("../../../lib/gettext.php");

//$locale_lang = $_GET['lang'];
$locale_lang = "en_US";
$locale_file = new FileReader("locale/$locale_lang/LC_MESSAGES/index.mo");
$locale_fetch = new gettext_reader($locale_file);

function _t($text){
	global $locale_fetch;
	return $locale_fetch->translate($text);
}

$error = 0;
if(isset($_GET['e'])){
    $error = $_GET['e'];
}

$s_cerrada = 0;
if(isset($_GET['s'])){
    $s_cerrada = $_GET['s'];
}

$org=1; //$super_idorganizacion;
$log="";
$pwd="";
$d = "";
$info="";

if(isset($_COOKIE["g"])) $org = $_COOKIE["g"];
if(isset($_COOKIE["n"])) $log = $_COOKIE["n"];

//error_log("cookie d => ".$_COOKIE["d"]);
if(isset($_COOKIE["d"])){
    $d = $_COOKIE["d"];
    $pwd = "mip130719";
}
if(isset($_COOKIE["i"])) $info = $_COOKIE["i"];

$fecha="";
$dia="";
$mes="";
$año="";

$fecha=date("Y-m-d");

$dia=date("d");
$mes=date("m");
$año=date("Y");

?>

<!DOCTYPE html>
<html>

<head>
<!--
	<link rel="icon" type="image/icon" href="http://www.netwarmonitor.mx/assets/img/ico16px.png">
	<link rel="apple-touch-icon" href="http://www.netwarmonitor.mx/assets/img/ico60px.png">
	<link rel="apple-touch-icon" sizes="76x76" href="http://www.netwarmonitor.mx/assets/img/ico76px.png">
	<link rel="apple-touch-icon" sizes="120x120" href="http://www.netwarmonitor.mx/assets/img/ico120px.png">
	<link rel="apple-touch-icon" sizes="152x152" href="http://www.netwarmonitor.mx/assets/img/ico152px.png">
-->

    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Q Software Solutions</title>

    <link href="../../libraries/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../design/index/index.css" rel="stylesheet" type="text/css" />

    <script>
        function mensaje(m){
            switch (m)
            {
                case 1:
                    alert("<?php echo _t("Usuario o contraseña no válidos."); ?>");
                    break;
                case 3:
                    alert("<?php echo _t("El correo electronico no es válido."); ?>");
                    break;
                case 2:
                    alert("<?php echo _t("El correo que proporciono esta asociado a otro usuario, escriba otro."); ?>");
                    break;
								case 4:
										alert("<?php echo _t("Servicio Suspendido por falta de pago, favor de comunicarse  al área de cuentas por cobrar  al tel. 30029300 ext 807 para realizar el pago y restablecer el sistema."); ?>");
										break;
            }
        }

    </script>

</head>
<body<?php
if($error==1) echo " onload='mensaje(1)'";
if($error==3) echo " onload='mensaje(2)'";
if($error==2) echo " onload='mensaje(3)'";
if($error==4) echo " onload='mensaje(4)'";

$ro="";
if($cobranza==1){
		$ro="hidden='true'";
		echo "<br><br><br><br><br><br>
					<div id='divlogin_container'>
						<center><strong><font color=red size=19>Servicio Suspendido, favor de comunicarse con su asesor, para restablecer el sistema.</font></strong></center>
					</div>";
}

?>>


	<br /><br /><br /><br /><br /><br /><br /><br />
	<div id="divlogin_container" <?php echo $ro;?>>
    <div id="divlogin">
        <form id="frmaccess" name="frmaccess" action="validapwd.php" method="post">

            <?php
            //CSRF
            $form_names = $csrf->form_names(array('txtusuario','txtclave'),true);
            echo $csrf->input_token($token_id,$token_value);
            //error_log("\nANTES tokenid:".$token_id."  tokenval:".$token_value."  txtusuario:".$form_names['txtusuario']);


						?>


            <input
            	class="input"
            	placeholder="<?php echo _t("Escriba su usuario"); ?>"
            	type="text"
            	id="txtusuario"
            	name="<?php echo $form_names['txtusuario']; ?>"
            	value="<?php echo $log; $ro?>">

            <br /><br />
						<input
            	class="input"
            	placeholder="<?php echo _t("Contraseña"); ?>"
         		type="password"
         		AUTOCOMPLETE="off"
         		id="txtclave"
         		name="<?php echo $form_names['txtclave']; ?>"
         		value="<?php echo $pwd; $ro?>">

			<br /><br />
            <?php
            	$checked='';
            	if($info=="g"){
                	$checked='checked="checked"';
            	}
            ?>
            <span>
            	<input value="g" type="checkbox" id="chkinfo" name="chkinfo" <?php echo $checked; ?>> <?php echo _t("Recordar Contraseña"); ?>
            </span>

            <br /><br />
            <input
            	type="submit"
            	id="btnsubmit"
            	name="btnsubmit"
            	value="<?php echo _t("Iniciar sesión"); ?>">

            <br /><br />

			<a href="javascript:opd();" class="footerlink"><?php echo _t("Recuperar la contraseña.") ?></a>

            <script type="text/javascript">

                function opd(){

                    $("#msgrespuesta").html("[ Espere un momento por favor ... ] &nbsp;&nbsp;");
                    var pcorreo=prompt("Capture el correo electrónico del usuario:");

                    $.ajax({
                        type: 	"post",
                        url: 		"ecl.php",
                        async: 	false,
                        data: 	{ c: pcorreo, <?php echo $token_id; ?>:"<?php echo $token_value; ?>" }
                }).done(function(msg){
                    alert(msg);
                    $("#msgrespuesta").html("");
                });

                }
            </script>

						<?php
							if($cobranza==1){
									echo "<strong>Servicio Suspendido por falta de pago, favor de comunicarse  al área de cuentas por cobrar  al tel. 30029300 ext 807 para realizar el pago y restablecer el sistema.</strong>";
							}
						?>

            <input type="hidden" id="d" name="d" value="<?php echo $d; ?>">
            <input type="hidden" id="stylepath" name="stylepath" value="<?php echo $txtStylePath; ?>">
        </form>





</div>



<!--
	Loading landing
 -->
<iframe
	id="frBackground"
	src="../../../landing/index.php"
	frameborder="0"
></iframe>

<script src="../../libraries/jquery.min.js"></script>
<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>


</body>
</html>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-75293719-1', 'auto');
  ga('send', 'pageview');

</script>
