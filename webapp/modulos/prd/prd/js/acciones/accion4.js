function savePasoAccion4(accion,idop,paso,idap,idp){
	idsProductos = $('#bodyempleado4 tr').map(function() {
	    idinput = $(this).attr('id');
	    spli1 = idinput.split('_');
	    idEmpleado=spli1[2];
		maq='';

        if (typeof idEmpleado !== "undefined") {
            id= idEmpleado+'>#'+maq;
        }
        return id;
  }).get().join('___');
   
    if(idsProductos==''){
        alert('Tienes que agregar personal');
        return false;
    }

    $.ajax({
        url:"ajax.php?c=Accion4&f=a_guardarPaso4",
        type: 'POST',
        data:{
            idsProductos:idsProductos,
            accion:accion,
            paso:paso,
            idop:idop,
            idap:idap
        },
        success: function(r){
            if(r>0){
                alert('Registro personal guardado con exito');
                ciclo(idop);
                
            }

        }
    });
}
function inicioaccion4(idop,paso,accion,idap,idp){
	$.ajax({
		url:"ajax.php?c=Accion2&f=a_clipasoAccion2",
		type: 'POST',
		dataType:'JSON',
		data:{idop:idop,paso:paso,accion:accion,idap:idap},
		success: function(r){
			if(r.success==1){
				$('#block_paso4').css('display','block');																									
			    $('#guardar_block4').html('<div class="col-sm-3"><button id="save_block4"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion4('+accion+','+idop+','+paso+','+idap+','+idp+')">Guardar</button></div>');
			    $('#bodyempleado4').html(r.data);
			}
		}
	});
}
function emp4(){
        idEmpleado= $('#select_empleados4').val();
        nombreEmpleado = $("#select_empleados4 option:selected").text();
        areaEmpleado = $("#select_empleados4 option:selected").attr('area');

        
        repetido=0;
        $("#bodyempleado4 tr").each(function( index ) {
            aaa = $(this).attr('id');
            if(aaa=='tr_empp_'+idEmpleado){
                repetido++;
            }
        });

        if(repetido>0){
            alert('Personal repetido');
            return false;
        }


        if(idEmpleado==0){
            alert('Seleccione un empleado');
            return false;
        }




        agrega='<tr id="tr_empp_'+idEmpleado+'">\
        <td>'+nombreEmpleado+'</td>\
        <td><button id="eliemp4" style=" padding: 0px;  height:33px;" onclick="eliemp4('+idEmpleado+');" class="btn btn-danger btn-sm btn-block">Elimina</button></td>\
        </tr>';



        $('#bodyempleado4').append(agrega);

    }
 function eliemp4(idEmpleado){
        $('#tr_empp_'+idEmpleado).remove();

    }