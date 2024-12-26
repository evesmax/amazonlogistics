<?php
	ini_set ('error_reporting', E_ALL);
?>
<html >
<head>
	<title></title>
	<script language="javascript">
		$(document).ready(function(){
			// RFC
			$("#i373").change(function(){
				change_rfc();
			});
			
			$("#i374").blur(function(){
				var result = "";
				var cnt = 0;
				val = $("#i373 option:selected").val();
				vale = $("#i374").val();

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
				val = $("#i373 option:selected").val();
				$("#i374").unmask();
				if( val == 1 ) 	$("#i374").mask("aaa-999999-***");
				else $("#i374").mask("aaaa-999999-***");
			}
		});

	</script>
</head>
<body><div id="loescrito"></div>
</body>
</html>