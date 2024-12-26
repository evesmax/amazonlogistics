<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/produccion/rnombre.php";
		
		
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
                $('#i535').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i532').val(),cantidadp:$('#i535').val(), tipo:1},function(datos)
                            {
                                var info = datos.split("|");
                                $('#lbl535').text(info[0]);
                                $('#lbl536').text(info[1]);
                                $('#i536').val(info[2]);
                                if(info[3]==1){
                                    $('#i536').attr("disabled", true);
                                    $('#i537').focus();
                                }
                                if(info[3]==2){
                                    $('#i536').removeAttr("disabled");
                                    $('#i536').focus();
                                }

                            }
                        );                  
                });
                
                
                $('#send').bind('click', function() {  
                    $('#i536').removeAttr("disabled");              
                });
                
                
            });
            
                
                
	</script>
</head>
<body>
</body>
</html>