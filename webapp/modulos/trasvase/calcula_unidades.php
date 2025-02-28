<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."/modulos/trasvase/rnombre.php";
     //echo $urlapp; 
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            
            $(document).ready(function(){
                $('#i667').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i265').val(),cantidadp:$('#i667').val(), tipo:1},function(datos)
                            {
                                var info = datos.split("|");
                                $('#lbl667').text(info[0]);
                                $('#lbl667').text(info[1]);
                                $('#i668').val(info[2]);

                                if(info[3]==1){
                                    $('#i668').attr("disabled", true);
                                    $('#i669').focus();
                                }
                                if(info[3]==2){
                                    $('#i668').removeAttr("disabled");
                                    $('#i669').focus();
                                }
                                
                            }
                        );
                                        
                });
                
                $('#send').bind('click', function() {  
                    $('#i669').removeAttr("disabled");              
                });
                
                
            });    
	</script>
</head>
<body>
</body>
</html>