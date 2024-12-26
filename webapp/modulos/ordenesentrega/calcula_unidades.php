<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/traslados/rnombre.php";
     
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            
            $(document).ready(function(){
                $('#i389').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i387').val(),cantidadp:$('#i389').val(), tipo:1},function(datos)
                            {
                                var info = datos.split("|");
                                $('#lbl389').text(info[0]);
                                $('#lbl390').text(info[1]);
                                $('#i390').val(info[2]);

                                if(info[3]==1){
                                    $('#i390').attr("disabled", true);
                                    $('#i391').focus();
                                }
                                if(info[3]==2){
                                    $('#i390').removeAttr("disabled");
                                    $('#i390').focus();
                                }
                                
                            }
                        );
                                        
                });
                
                $('#send').bind('click', function() {  
                    $('#i390').removeAttr("disabled");              
                });
                
                
            });
            
                
                
	</script>
</head>
<body>
</body>
</html>