<script src="js/select2/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<script>
	$(document).ready(function(){
		$("#i1484").select2({
         width : "150px"
        });
        $("#i1483").select2({
         width : "150px"
        });
	});
	$("input[onclick='btn_i1483_click();']").hide();
	
	$("#i1483").on('change', function() {
		if(this.value==93){
			$('#i1485').val('NA');
		}else{
			$('#i1485').val('')
		}
	});
</script>