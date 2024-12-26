function inicioaccion(idop,paso,accion,idap,idp){
 $.ajax({
        url:"ajax.php?c=Accion1&f=a_clipasoAccion1",
        type: 'POST',
        dataType:'JSON',                                
        data:{idop:idop,paso:paso,accion:accion,idap:idap},
        success: function(r){

	        if(r.success==1){
	        	 cad1='<div class="col-sm-4" style="margin-top: 10px;">\
                            <b>Producto</b>\
                        </div>\
                        <div class="col-sm-4" style="margin-top: 10px;">\
                            <b>Cantidad necesaria</b>\
                        </div>\
                        <div class="col-sm-4" style="margin-top: 10px;">\
                            <b>Existencias</b>\
                        </div>';
                        $.each(r.data, function(k,v) {
                            cad1+='<div class="col-sm-4" style="margin-top: 10px;">\
                                '+v.nombre+'\
                            </div>\
                            <div class="col-sm-4" style="margin-top: 10px;">\
                                <input existen1="'+v.existen+'" id="b1_'+v.idop+'_'+v.idProducto+'"  readonly type="text" name="" value="'+v.cantidad+'" class="form-control">\
                            </div>\
                            <div class="col-sm-4" style="margin-top: 10px; height:40px;">\
                                '+v.existen+'\
                            </div>';

                        });



                        $('#insumos_block1').html(cad1);
                        $('#insumos_block1 input').numeric();
                        $('#guardar_block1').html('<div class="col-sm-3"><button id="save_block1"  class="btn btn-primary btn-sm btn-block" onclick="savepaso('+accion+','+idop+','+paso+','+idap+','+idp+')">Utilizar insumos</button></div>');

                        $('#block_paso1').css('display','block');
	        }
        }
   });
}
 function savepaso(accion,idop,paso,idap,idp,maspaso){

    lacant=$('#lacant').val();
        
	if (accion == 1) {
		faltan = 0;
		idsProductos = $('#insumos_block1 input').map(function() {
			cant = $(this).val();
			cantexist = $(this).attr('existen1');

			if ((cant * 1) > (cantexist * 1)) {
				faltan++;
			}
			idinput = $(this).attr('id');
			spli1 = idinput.split('b1_');
			spli2 = spli1[1].split('_');
			idPadre = spli2[0];
			idHijo = spli2[1];

			if ( typeof idPadre !== "undefined") {
				id = idPadre + '>#' + idHijo + '>#' + cant;
			}

			return id;
		}).get().join('___');

		if (faltan > 0) {
			alert('No hay existencias');
			return false;
		}

		$.ajax({
			url : "ajax.php?c=Accion1&f=a_guardarPaso1",
			type : 'POST',
			data : {
				idsProductos : idsProductos,
				accion : accion,
				paso : paso,
				idop : idop,
				idap : idap
			},
			success : function(r) {
				if (r > 0) {
					alert('Registro produccion iniciado con exito');
					ciclo(idop);
				}

			}
		});
	}


}
 