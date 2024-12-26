function periodo(periodo){

  	$("#button"+periodo).hide("slow");
  	$("#load"+periodo).show();
	
	$.post("ajax.php?c=Cierreanual&f=nuevoPeriodoAnual",{
		periodo: periodo
		
		},function(resp){
			if(resp==1){
				alert("Se han creado las nominas del nuevo ejercicio!");
			}else if(resp == 3){
				alert("El nomina del periodo no se encuentra o aun no autoriza la ultima nomina del ejercicio anterior.");
				
			}else if(resp==4){
				alert("El ejercicio ya fue generado");
			}
			else{
				alert("Error en el proceso intente de nuevo.");
				
			}
			$("#button"+periodo).show("slow");
  				$("#load"+periodo).hide();
			
		}
	);
		

}	
	
	
