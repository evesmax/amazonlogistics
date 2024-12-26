<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/traslados/rnombre.php";
     
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            
            $(document).ready(function(){
                $('#i268').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i265').val(),cantidadp:$('#i268').val(), tipo:1},function(datos)
                            {
                                var info = datos.split("|");
                                $('#lbl268').text(info[0]);
                                $('#lbl269').text(info[1]);
                                $('#i269').val(info[2]);

                                if(info[3]==1){
                                    $('#i269').attr("disabled", true);
                                    $('#i272').focus();
                                }
                                if(info[3]==2){
                                    $('#i269').removeAttr("disabled");
                                    $('#i269').focus();
                                }
                                
                            }
                        );
                                        
                });
                
                $('#send').bind('click', function() {  
                    $('#i269').removeAttr("disabled");              
                });
                
                
            });
            
                
                
	</script>
</head>
<body>
</body>
</html>