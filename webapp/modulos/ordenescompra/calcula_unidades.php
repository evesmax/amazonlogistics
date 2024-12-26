<?php
        
        $idempleado=0;
        $idempleado=$_SESSION["accelog_idempleado"];
        $e=0;
        $e=$a;
        
        if($idempleado=1){
            $e=1;
        }   
        
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/ordenescompra/rnombre.php";
        $urlapp2=$url_dominio."modulos/ordenescompra/rprecio.php";
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            
            $(document).ready(function(){ 
//                $('#i584').bind('blur', function() {  
//                       $('#i603').val('<?php //echo $oc; ?>');  
//                });
//                $('#i603').bind('click', function() {  
//                       $('#i603').val('<?php //echo $oc; ?>');              
//                });
                if('<?php echo $e; ?>'==0){
                    if($('#i610').val()==2){
                        alert('El Documento no se puede modificar, ya esta procesado');
                        $('#send').attr("disabled", true);
                    }
                }
                $('#i589').bind('blur', function() {  
                    $.get('<?php echo $urlapp2; ?>',{preciopb:$('#i786').val(), producto:$('#i588').val(),tipo:2},function(datos)
                        {   
                            var info = datos.split("|");
                            var idnorma=0;
                            idnorma=info[0]*1;
                            $('#i592').val(idnorma);
                        }
                    );
                });	
                $('#i586').bind('blur', function() {  
                    $('#i587').val($('#i586').val()); 
                    $.get('<?php echo $urlapp2; ?>',{preciopb:$('#i786').val(), producto:$('#i588').val(),tipo:2},function(datos)
                        {   
                            var info = datos.split("|");
                            var idnorma=0;
                            idnorma=info[0]*1;
                            $('#i592').val(idnorma);
                        }
                    );
                });		
                $('#i786').bind('blur', function() {  
                        //RecalCula Precio por Bulto
                        $.get('<?php echo $urlapp2; ?>',{preciopb:$('#i786').val(), producto:$('#i588').val(),tipo:1},function(datos)
                            {  
                                
                                var info = datos.split("|");
                                var preciotm=0;
                                preciotm=info[0]*1;
                                $('#i599').val(preciotm);
                                $('#i599').attr("readonly", true);
                            }
                        );
                                        
                });                 
                
                $('#i585').bind('blur', function() {  
                        //Agrega Existencia por cambio Zafra
                        $.get('<?php echo $urlapp; ?>',{idfabricante:$('#i586').val(),idmarca:$('#i587').val(),idproducto:$('#i588').val(),idlote:$('#i585').val(),idestadoproducto:$('#i589').val(),idbodega:$('#i596').val(),volumenorden:$('#i598').val()},function(datos)
                            {  
                                var info = datos.split("|");
                                $('#i597').removeAttr("disabled");
                                $('#i597').val(info[0]);
                                $('#i597').attr("disabled", true);
                                

                            }
                        );
                                        
                });                
                $('#i586').bind('blur', function() {  
                        //Agrega Existencia por cambio Ingenio
                        $.get('<?php echo $urlapp; ?>',{idfabricante:$('#i586').val(),idmarca:$('#i587').val(),idproducto:$('#i588').val(),idlote:$('#i585').val(),idestadoproducto:$('#i589').val(),idbodega:$('#i596').val(),volumenorden:$('#i598').val()},function(datos)
                            {  
                                var info = datos.split("|");
                                $('#i597').removeAttr("disabled");
                                $('#i597').val(info[0]);
                                $('#i597').attr("disabled", true);
                            }
                        );
                                        
                });
                $('#i587').bind('blur', function() {  
                        //Agrega Existencia por cambio Marca
                        $.get('<?php echo $urlapp; ?>',{idfabricante:$('#i586').val(),idmarca:$('#i587').val(),idproducto:$('#i588').val(),idlote:$('#i585').val(),idestadoproducto:$('#i589').val(),idbodega:$('#i596').val(),volumenorden:$('#i598').val()},function(datos)
                            {  
                                var info = datos.split("|");
                                $('#i597').removeAttr("disabled");
                                $('#i597').val(info[0]);
                                $('#i597').attr("disabled", true);
                            }
                        );
                                        
                }); 
                $('#i597').bind('click', function() {  
                        //Agrega Existencia por cambio Marca
                        $.get('<?php echo $urlapp; ?>',{idfabricante:$('#i586').val(),idmarca:$('#i587').val(),idproducto:$('#i588').val(),idlote:$('#i585').val(),idestadoproducto:$('#i589').val(),idbodega:$('#i596').val(),volumenorden:$('#i598').val()},function(datos)
                            {  
                                var info = datos.split("|");
                                $('#i597').removeAttr("disabled");
                                $('#i597').val(info[0]);
                                $('#i597').attr("disabled", true);
                            }
                        );
                                        
                }); 
                $('#i598').bind('blur', function() {  
                        //Agrega Existencia por focus a volumen orden
                        $.get('<?php echo $urlapp; ?>',{idfabricante:$('#i586').val(),idmarca:$('#i587').val(),idproducto:$('#i588').val(),idlote:$('#i585').val(),idestadoproducto:$('#i589').val(),idbodega:$('#i596').val(),volumenorden:$('#i598').val()},function(datos)
                            {  
                                var info = datos.split("|");
                                var cant1=0, cant2=0;
                                cant1=info[0]*1;
                                cant2=info[1]*1;
                                $('#i597').removeAttr("disabled");
                                $('#i597').val(info[0]);
                                $('#i597').attr("disabled", true);
                                //alert(" "+cant1+" "+cant2);
                                if(cant1<cant2){
                                    alert("No puede vender mas de la existencia, disponible "+cant1+" "+cant2);
                                    $('#i598').val(cant1);
                                    $('#i585').focus();
                                }
                                
                            }
                        );
                                        
                });
                
                $('#send').bind('click', function() {  
                    $('#i597').removeAttr("disabled");              
                });
                
                
            });
            
                
                
	</script>
</head>
<body>
</body>
</html>