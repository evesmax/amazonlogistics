<?php
	ini_set ('error_reporting', E_ALL);
        $urlapp=$url_dominio."modulos/produccion/rnombre.php";
		
		
		//INCLUDE ANTES
        
		
?>
<html >
<head>
	<title></title>
	<script language="javascript">
            
            $(document).ready(function(){
                $('#chktodos').bind('click', function() {  
                    //$('#chk[2]').attr('checked', true);
                    //alert('Hola '+$('#chk[1]').val());
                });
                
            });
            
            function chktodo(chk) { 
              for(i=0;ele=chk.form.elements[i];i++) 
                if(ele.type=='checkbox') 
                  ele.checked=chk.checked; 
            }  
                
	</script>
</head>
<body>
</body>
</html>