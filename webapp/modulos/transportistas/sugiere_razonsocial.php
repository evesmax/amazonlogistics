<?php
	ini_set ('error_reporting', E_ALL);
?>
<html >
<head>
	<title></title>
	<script language="javascript">
		$(document).ready(function(){
			// RFC
			$("#i249").change(function(){
				change_rfc();
			});
			
			$("#i250").blur(function(){
				var result = "";
				var cnt = 0;
				val = $("#i249 option:selected").val();
				vale = $("#i250").val();

				for( i=0;i<vale.length;i++ )
					if( vale.charAt(i)== "_" ){
						result = "abc"; cnt++;
					}
				
				if( val == 1 && result != "" ) 		result = "Moral";
				else if( val == 2 && result != "" ) result = "Fisica";

				if( val==1 ) val++;
				if( cnt > 0 )// if( result != "" )
					alert('El RFC Persona '+result+' debe tener '+(vale.length-val)+' Caraceteres!\n\nFaltan: '+(cnt)+'.');
			});
			
			jQuery(function(){ change_rfc(); });
			
			function change_rfc(){
				val = $("#i249 option:selected").val();
				$("#i250").unmask();
				if( val == 1 ) 	$("#i250").mask("aaa-999999-***");
				else 			$("#i250").mask("aaaa-999999-***");
			}
		});
		// if(document.getElementById("i250").value==''){
			// document.getElementById("i250").value = document.getElementById("i249").options[document.getElementById("i249").selectedIndex].text;
		// }
	</script>
</head>
<body><div id="loescrito"></div>
</body>
</html>