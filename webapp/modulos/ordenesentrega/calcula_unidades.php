<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/traslados/rnombre.php";
        //echo $urlapp; 
     
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            
            $(document).ready(function(){
                        
                        // Elementos de Solo Lectura
                        $('#i368_1, #i368_2, #i368_3,#i368t').prop('readonly', true);
                        // Ocultar el elemento de la imagen
                        $('#i368_img').hide();
                        
                $('#i389').bind('blur', function() {  
                        //Agrega nombre de unidad principal , cartaporte:$('#i654').val(), idtransportista:$('#select2-i653-container').val()
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i387').val(),cantidadp:$('#i389').val(), tipo:1, cartaporte:$('#i654').val(), idtransportista:$('#i653').val()},function(datos)
                            {
                                //console.log(datos);
                                var info = datos.split("|");
                                $('#lbl389').text(info[0]);
                                $('#lbl390').text(info[1]);
                                //$('#i390').val(info[2]);
                                $('#i390').val(parseFloat(info[2]).toFixed(3));
                                $('send').attr("disabled", false);

                                if(info[3]==1){
                                    $('#i390').attr("disabled", true);
                                    $('#i391').focus();
                                }
                                if(info[3]==2){
                                    $('#i390').removeAttr("disabled");
                                    $('#i390').focus();
                                }
                               if(info[4]>0){
                                    $('#i654').val(0);
                                    alert("Carta Porte Duplicada");
                                    $('#i654').focus();
                                    $('send').attr("disabled", false);
                               }
                               //console.log("Duplicada:"+info[4]+" CartaPorte:"+$('#i654').val()+" Transportista"+$('#i653').val());
                            }
                        );           
                });

                $('#i654').bind('blur', function() {  
                        //Agrega nombre de unidad principal , cartaporte:$('#i654').val(), idtransportista:$('#select2-i653-container').val()
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i387').val(),cantidadp:$('#i389').val(), tipo:1, cartaporte:$('#i654').val(), idtransportista:$('#i653').val()},function(datos)
                            {
                                //console.log(datos);
                                $('send').attr("disabled", false);
                                var info = datos.split("|");
                                $('#lbl389').text(info[0]);
                                $('#lbl390').text(info[1]);
                                $('#i390').val(parseFloat(info[2]).toFixed(3));
                                //$('#i390').val(info[2]);

                                if(info[3]==1){
                                    $('#i390').attr("disabled", true);
                                    $('#i391').focus();
                                }
                                if(info[3]==2){
                                    $('#i390').removeAttr("disabled");
                                    $('#i390').focus();
                                }
                               if(info[4]>0){
                                    $('send').attr("disabled", true);
                                    $('#i654').val(0);
                                    alert("Carta Porte Duplicada");
                                    $('#i654').focus();
                               }
                               //console.log("Duplicada:"+info[4]+" CartaPorte:"+$('#i654').val()+" Transportista"+$('#i653').val());
                            }
                        );           
                });

                $('#select2-i653-container').bind('blur', function() {  
                        //Agrega nombre de unidad principal , cartaporte:$('#i654').val(), idtransportista:$('#select2-i653-container').val()
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i387').val(),cantidadp:$('#i389').val(), tipo:1, cartaporte:$('#i654').val(), idtransportista:$('#i653').val()},function(datos)
                            {
                                //console.log(datos);
                                $('send').attr("disabled", false);
                                var info = datos.split("|");
                                $('#lbl389').text(info[0]);
                                $('#lbl390').text(info[1]);
                                $('#i390').text(parseFloat(info[2]).toFixed(3)); 
                                //$('#i390').val(info[2]);

                                if(info[3]==1){
                                    $('#i390').attr("disabled", true);
                                    $('#i391').focus();
                                }
                                if(info[3]==2){
                                    $('#i390').removeAttr("disabled");
                                    $('#i390').focus();
                                }
                               if(info[4]>0){
                                    $('send').attr("disabled", true);
                                    $('#i654').val(0);
                                    alert("Carta Porte Duplicada");
                                    $('#i654').focus();
                               }
                               //console.log("Duplicada:"+info[4]+" CartaPorte:"+$('#i654').val()+" Transportista"+$('#i653').val());
                            }
                        );           
                })

                // Espera a que el contenido HTML de la página esté completamente cargado
                document.addEventListener('DOMContentLoaded', function() {
                    
                    // Busca el elemento por su ID
                    const selector = document.getElementById('i387');

                    // Le asigna el evento 'change'
                    selector.addEventListener('change', function() {
                        console.log("El selector ha cambiado (versión sin jQuery).");
                        document.getElementById('i389').value = 0;
                        document.getElementById('i390').value = 0;
                    });

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