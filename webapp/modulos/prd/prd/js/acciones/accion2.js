function savePasoAccion2(accion,idop,paso,idap,idp){
	clotes=$('#clotes').val();
    idsProductos = $('#insumos_block2 input').map(function() {
	    cant = $(this).val();
	    idinput = $(this).attr('id');
	    spli1 = idinput.split('b2_');
	    spli2 = spli1[1].split('_');
	    idPadre=spli2[0];
	    idHijo=spli2[1];
	    if (typeof idPadre !== "undefined") {
	        id= idPadre+'>#'+idHijo+'>#'+cant;
	    }
    	return id;
   	}).get().join('___');

    $.ajax({
        url:"ajax.php?c=Accion2&f=a_guardarPaso2",
        type: 'POST',
        data:{
            idsProductos:idsProductos,
            accion:2,
            paso:paso,
            idop:idop,
            clotes:clotes,
            idap:idap
        },
        success: function(r){
            if(r=='nolote'){
                alert('Faltan registrar los lotes');
                return false;
            }

            if(r>0){
                alert('Registro guardado con exito');
                ciclo(idop);
            }
        }
    });
}

	
function inicioaccion2(idop,paso,accion,idap,idp){
	
	$.ajax({
		url:"ajax.php?c=Accion2&f=a_clipasoAccion2",
		type: 'POST',
		dataType:'JSON',
		data:{idop:idop,paso:paso,accion:accion,idap:idap},
		success: function(r){
			if(r.success==1){
				cad2='';
				clotes=0;
				$.each(r.data, function(k,v) {
					if(v.lotes==1){
						clotes++;
						btnlote='<div class="col-sm-3" style="margin-top: 12px;"><button id="save_block2"  class="btn btn-default btn-sm btn-block" onclick="modaLote('+accion+','+idop+','+paso+','+v.idProducto+','+v.canti+',\''+v.nombre+'\')">Lote</button></div>';
					}else{
						btnlote="";
					}
					cad2+='<div class="col-sm-4" style="margin-top: 12px;">\
					'+v.nombre+'\
					</div>\
					<div class="col-sm-5" style="margin-top: 10px;">\
					<input readonly id="b2_'+idop+'_'+v.idProducto+'" type="text" name="" value="'+v.canti+'" class="form-control">\
					</div>';
				
					cad2+=btnlote;
				});
				$('#insumos_block2').html(cad2);
				$('#insumos_block2 input').numeric();
				$('#guardar_block2').html('<input id="clotes" type="hidden" value="'+clotes+'"><div class="col-sm-3"><button id="save_block2"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion2('+accion+','+idop+','+paso+','+idap+','+idp+')">Guardar</button></div>');
				$('#block_paso2').css('display','block');
			}
		}
	});

	
}
