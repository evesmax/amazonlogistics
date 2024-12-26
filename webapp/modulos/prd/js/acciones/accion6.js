
 $("#lote6_fechafab").datepicker({ format: "yyyy-mm-dd",language: "es"});
 $("#lote6_fechacad").datepicker({ format: "yyyy-mm-dd",language: "es"});

function savePasoAccion6(accion,idop,paso,idap,idp){
	disabled_btn('#save_block6', 'Procesando...');
	$.ajax({
		url : "ajax.php?c=Accion6&f=a_guardarPaso6",
		type : 'POST',
		data : {
			accion : accion,
			paso : paso,
			idop : idop,
			idap : idap,
			lote : $('#lote').val(),
			fechacad:$("#lote6_fechacad").val(),
			fechafab:$("#lote6_fechafab").val()
		},
		success : function(r) {
			enabled_btn('#save_block6', 'Guardar Lote');
			if (r > 0) {
				alert('Registro de lote almacenado');
				ciclo(idop);
			}else{
				alert("Error en el proceso intente de nuevo");
			}

		}
	});
}
/*formula para kerlab lote
 [Lote + Letra de Mes + Fía de Fabricación + 0 + Calculo del número]
Número de Identificador = [100 + (año de fabricación - año de operación) +1]
*/
function generarLote(){
	var operacion = 1990;
	if (!$("#lote6_fechafab").val()){
		alert("Debe marcar la fecha de fabricacion");
		$("#lote6_fechafab").focus();
		return false;
	}
	var fabricacion = $("#lote6_fechafab").val();
	var separa = fabricacion.split("-");
	var diaFab = separa[2];
	var mesNum = separa[1];
	var mes = new Array();
	mes[1]="G", mes[2]="H",mes[3]="I",mes[4]="J",mes[5]="K",mes[6]="L",mes[7]="A",mes[8]="B",mes[9]="C",mes[10]="D",mes[11]="E",mes[12]="F";
	var ano = separa[0]-operacion;
	var numero = parseInt(100 + ano + 1);
	var lote = mes[parseInt(mesNum)] + diaFab + 0 + numero;
	//alert(lote);
	$("#lote").val(lote);
}
