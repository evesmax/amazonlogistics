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
                $('#i667').bind('change', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i664').val(),cantidadp:$('#i667').val(), tipo:1},function(datos)
                            {
                                //console.log("Producto:", $('#i664').val(), "Cantidad:", $('#i667').val());
                                //alert($('#i664').val());
                                var info = datos.split("|");
                                $('#lbl667').text(info[0]);
                                $('#lbl668').text(info[1]);
                                //$('#i668').val(info[2]);
                                $('#i668').val(parseFloat(info[2]).toFixed(3));

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
                $('#i670').bind('change', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i669').val(),cantidadp:$('#i670').val(), tipo:1},function(datos)
                            {
                                //console.log("Producto:", $('#i669').val(), "Cantidad:", $('#i670').val());
                                //alert('i669'+$('#i669').val());
                                var info = datos.split("|");
                                $('#lbl670').text(info[0]);
                                $('#lbl671').text(info[1]);
                                $('#i671').val(info[2]);

                                if(info[3]==1){
                                    $('#i671').attr("disabled", true);
                                    $('#i673').focus();
                                }
                                if(info[3]==2){
                                    $('#i671').removeAttr("disabled");
                                    $('#i673').focus();
                                }
                            }
                        );
                                        
                });     
                $('#i664').bind('change', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i664').val(),cantidadp:$('#i667').val(), tipo:1},function(datos)
                            {
                                //console.log("Producto:", $('#i664').val(), "Cantidad:", $('#i667').val());
                                //alert($('#i664').val());
                                var info = datos.split("|");
                                $('#lbl667').text(info[0]);
                                $('#lbl668').text(info[1]);
                                //$('#i668').val(info[2]);
                                $('#i668').val(parseFloat(info[2]).toFixed(3));


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
                $('#i669').bind('change', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i669').val(),cantidadp:$('#i670').val(), tipo:1},function(datos)
                            {
                                //console.log("Producto:", $('#i669').val(), "Cantidad:", $('#i670').val());
                                //alert($('669'+'#i669').val());
                                var info = datos.split("|");
                                $('#lbl670').text(info[0]);
                                $('#lbl671').text(info[1]);
                                $('#i671').val(info[2]);

                                if(info[3]==1){
                                    $('#i671').attr("disabled", true);
                                    $('#i673').focus();
                                }
                                if(info[3]==2){
                                    $('#i671').removeAttr("disabled");
                                    $('#i673').focus();
                                }
                            }
                        );
                                        
                });

                // Espera a que el contenido HTML de la página esté completamente cargado
                document.addEventListener('DOMContentLoaded', function() {
                    
                    // Busca el elemento por su ID
                    const selector = document.getElementById('i664');
                    // Le asigna el evento 'change'
                    selector.addEventListener('change', function() {
                        console.log("El selector ha cambiado (versión sin jQuery).");
                        document.getElementById('i667').value = 0;
                        document.getElementById('i668').value = 0;
                    });

                });

                $('#send').bind('click', function() {  
                    $('#i668').removeAttr("disabled");  
                    $('#i671').removeAttr("disabled");              
                });
            });
	</script>
</head>
<body>
</body>
</html>