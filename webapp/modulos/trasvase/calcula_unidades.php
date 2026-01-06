<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/trasvase/rnombre.php";
     //echo $urlapp; 
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            $(document).ready(function(){

            //Calcula bultos a toneladas
                $('#i667').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i664').val(),cantidadp:$('#i667').val(), tipo:1,productoD:$('#i669').val()},function(datos)
                            {
                                //console.log("Producto:", $('#i664').val(), "Cantidad:", $('#i667').val());
                                //alert($('#i664').val());
                                var info = datos.split("|");
                                var factorPD=info[5];
                                var cantPD1=0;
                                var cantPD2=0;

                                $('#lbl667').text(info[0]);
                                $('#lbl668').text(info[1]);
                                $('#i668').val(info[2]);

                                //Como cambiaron deben de regresar a cero por que pueden afectarse
                                cantPD2=parseFloat(info[2])/parseFloat(factorPD);
                                cantPD1=parseFloat(cantPD2)*parseFloat(factorPD); 
                                console.log("FactorPD:", factorPD, "info2:", info[2]); 
                                $('#i670').val(cantPD2.toFixed(3));
                                $('#i671').val(cantPD1);
                                //$('#i670').val(0);
                                //$('#i671').val(0);
                                //$('#i668').val(parseFloat(info[2]).toFixed(3));

                                if(info[3]==1){
                                    $('#i668').attr("disabled", true);
                                    $('#i667').focus();
                                }
                                if(info[3]==2){
                                    $('#i668').removeAttr("disabled");
                                    $('#i667').focus();
                                }
                                
                            }
                        );               
                });
                $('#i670').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i669').val(),cantidadp:$('#i670').val(), tipo:1,productoD:$('#i669').val()},function(datos)
                            {
                                //console.log("Producto:", $('#i669').val(), "Cantidad:", $('#i670').val());
                                //alert('i669'+$('#i669').val());
                                var info = datos.split("|");
                                $('#lbl670').text(info[0]);
                                $('#lbl671').text(info[1]);
                                $('#i671').val(info[2]);


                                if(info[3]==1){
                                    $('#i671').attr("disabled", true);
                                    $('#i667').focus();
                                }
                                if(info[3]==2){
                                    $('#i671').removeAttr("disabled");
                                    $('#i667').focus();
                                }
                            }
                        );
                                        
                });     
                $('#i664').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i664').val(),cantidadp:$('#i667').val(), tipo:1,productoD:$('#i669').val()},function(datos)
                            {
                                //console.log("Producto:", $('#i664').val(), "Cantidad:", $('#i667').val());
                                //alert($('#i664').val());
                                var info = datos.split("|");
                                $('#lbl667').text(info[0]);
                                $('#lbl668').text(info[1]);
                                $('#i668').val(info[2]);
                                //$('#i668').val(parseFloat(info[2]).toFixed(3));

                                //Como cambiaron deben de regresar a cero por que pueden afectarse
                                cantPD2=parseFloat(info[2])/parseFloat(factorPD);
                                cantPD1=parseFloat(cantPD2)*parseFloat(factorPD); 
                                console.log("FactorPD:", factorPD, "info2:", info[2]); 
                                $('#i670').val(cantPD2.toFixed(3));
                                $('#i671').val(cantPD1);

                                if(info[3]==1){
                                    $('#i668').attr("disabled", true);
                                    $('#i667').focus();
                                }
                                if(info[3]==2){
                                    $('#i668').removeAttr("disabled");
                                    $('#i667').focus();
                                }
                                
                            }
                        );
                                        
                });
                $('#i669').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i669').val(),cantidadp:$('#i670').val(), tipo:1,productoD:$('#i669').val()},function(datos)
                            {
                                //console.log("Producto:", $('#i669').val(), "Cantidad:", $('#i670').val());
                                //alert($('669'+'#i669').val());
                                var info = datos.split("|");
                                $('#lbl670').text(info[0]);
                                $('#lbl671').text(info[1]);
                                $('#i671').val(info[2]);

                                //Como cambiaron deben de regresar a cero por que pueden afectarse
                                cantPD2=parseFloat(info[2])/parseFloat(factorPD);
                                cantPD1=parseFloat(cantPD2)*parseFloat(factorPD); 
                                console.log("FactorPD:", factorPD, "info2:", info[2]); 
                                $('#i670').val(cantPD2.toFixed(3));
                                $('#i671').val(cantPD1);

                                if(info[3]==1){
                                    $('#i671').attr("disabled", true);
                                    $('#i667').focus();
                                }
                                if(info[3]==2){
                                    $('#i671').removeAttr("disabled");
                                    //$('#i673').focus();
                                }
                            }
                        );
                                        
                });


            //Calcula de toneladas a Bultos
                $('#i668').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i664').val(),cantidadp:$('#i668').val(), tipo:2,productoD:$('#i669').val()},function(datos)
                            {
                                //console.log("Producto:", $('#i664').val(), "Cantidad:", $('#i667').val());
                                //alert($('#i664').val());
                                var info = datos.split("|");
                                $('#lbl667').text(info[0]);
                                $('#lbl668').text(info[1]);
                                $('#i667').val(info[2]);

                                //Como cambiaron deben de regresar a cero por que pueden afectarse
                                $('#i670').val(0);
                                $('#i671').val(0);

                                //Calclare el aproximado de conversion
                                
                                //Como cambiaron deben de regresar a cero por que pueden afectarse
                                cantPD2=parseFloat(info[2])/parseFloat(factorPD);
                                cantPD1=parseFloat(cantPD2)*parseFloat(factorPD); 
                                console.log("FactorPD:", factorPD, "info2:", info[2]); 
                                $('#i670').val(cantPD2.toFixed(3));
                                $('#i671').val(cantPD1);

                                if(info[3]==1){
                                    $('#i668').attr("disabled", true);
                                    $('#i667').focus();
                                }
                                if(info[3]==2){
                                    $('#i668').removeAttr("disabled");
                                    $('#i667').focus();
                                }
                                
                            }
                        );               
                });
                $('#i671').bind('blur', function() {  
                        //Agrega nombre de unidad principal
                        $.get('<?php echo $urlapp; ?>',{producto:$('#i669').val(),cantidadp:$('#i671').val(), tipo:2,productoD:$('#i669').val()},function(datos)
                            {
                                //console.log("Producto:", $('#i669').val(), "Cantidad:", $('#i670').val());
                                //alert('i669'+$('#i669').val());
                                var info = datos.split("|");
                                $('#lbl670').text(info[0]);
                                $('#lbl671').text(info[1]);
                                $('#i670').val(info[2]);

                                if(info[3]==1){
                                    $('#i671').attr("disabled", true);
                                    $('#i667').focus();
                                }
                                if(info[3]==2){
                                    $('#i671').removeAttr("disabled");
                                    $('#i667').focus();
                                }
                            }
                        );
                                        
                });     



            //alert('Entre a funcion parte final: ' + $('#i667').val());


            });
	</script>
</head>
<body>
</body>
</html>