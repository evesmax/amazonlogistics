<?php

        $idempleado=0;
        $idempleado=$_SESSION["accelog_idempleado"];
        //$url_dominio=$_SESSION["url_dominio"];
        $e=0;
        $e=$a;

        if($idempleado=1){
            $e=1;
        }
	ini_set ('error_reporting', E_ALL);

        $urlapp=$url_dominio."modulos/produccion/rnombre.php";
        //echo $urlapp;

		//if ($_SESSION['catalog_nuevo']==0){
		//		echo $valor;
		//		$linkcot="modulos/produccion/produccion_imprimir.php?folio=".$VALORCAMPOFOLIO;
		//		echo "<A href='".$url_dominio.$linkcot."'><img src='../../netwarelog/repolog/img/impresora.png' border='0'>Produccion</A>";
		//}

?>
<html >
<head>
	<title></title>
	<script language="javascript">

            $(document).ready(function(){

				if('<?php echo $e; ?>'==0){
                    if($('#i227').val()==2){
                        alert('El Documento no se puede modificar, ya esta procesado');
                        $('#send').attr("disabled", true);
                    }
                }

                $('#i220').bind('blur', function() {
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i219').val(),cantidadp:$('#i220').val(), tipo:1},function(datos)
                            {
                                var info = datos.split("|");
                                $('#lbl220').text(info[0]);
                                $('#lbl221').text(info[1]);
                                $('#i221').val(info[2]);
                                if(info[3]==1){
                                    $('#i221').attr("disabled", true);
                                    $('#i222').focus();
                                }
                                if(info[3]==2){
                                    $('#i221').removeAttr("disabled");
                                    $('#i221').focus();
                                }

                            }
                        );
                });


                $('#send').bind('click', function() {
                    $('#i221').removeAttr("disabled");
                });


            });



	</script>
</head>
<body>
</body>
</html>
