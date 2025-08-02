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
                $('#i389').bind('blur', function() {  
                        //Agrega nombre de unidad principal , cartaporte:$('#i654').val(), idtransportista:$('#select2-i653-container').val()
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i387').val(),cantidadp:$('#i389').val(), tipo:1, cartaporte:$('#i654').val(), idtransportista:$('#i653').val()},function(datos)
                            {
                                //console.log(datos);
                                var info = datos.split("|");
                                $('#lbl389').text(info[0]);
                                $('#lbl390').text(info[1]);
                                $('#i390').val(info[2]);
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
                                $('#i390').val(info[2]);

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
                                $('#i390').val(info[2]);

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

                $('#i387').bind('click', function() {
                    // Aquí puedes poner el resto de tu código
                    // Por ejemplo, reiniciar las cantidades como en tu pregunta anterior.
                    $('#i389').val(0);
                    $('#i390').val(0);
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