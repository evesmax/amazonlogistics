function inicioaccion3(idop,paso,accion,idap,idp){

    $.ajax({
        async:false, 
        url:"ajax.php?c=Accion3&f=a_clipasoAccion3",
        type: 'POST',
        dataType:'JSON',                                
        data:{idop:idop,paso:paso,accion:accion,idap:idap},
        success: function(r){

            if(r.success==1){

                cad3='';
                $.each(r.data, function(k,v) {
                    cad3+='<div class="col-sm-4" style="margin-top: 10px;">\
                    '+v.nombre+'\
                    </div>\
                    <div class="col-sm-8" style="margin-top: 10px;">\
                    <input class="valor form-control" id="b3_'+v.idop+'_'+v.idProducto+'" type="text" name="" value="'+v.peso+'">\
                    </div>';
                });

                $('#insumos_block3').html(cad3);
                $('#insumos_block3 input').numeric();
                $('#guardar_block3').html('<div class="col-sm-3"><button id="save_block3"  class="btn btn-primary btn-sm btn-block" onclick="savepaso('+accion+','+idop+','+paso+','+idap+','+idp+')">Guardar</button></div>');
                $('#block_paso3').css('display','block');
            }
        }
    });
    var urlbascula='';
    $.ajax({
            url:"ajax.php?c=Accion3&f=url_bascula",
            type: 'POST',
            success: function(r){
                urlbascula=r;   
         
    $.ajax({
        async:false, 
        url: urlbascula+"/solicitar_peso.php",
        type: 'post',
        dataType: 'json',

    }).done(function(resPeso) {
        console.log("success");

        $('.valor').val(resPeso.peso);

    })
    .fail(function(jqXHR, textStatus, errorThrown) {
        alert("1.- Verifica que la aplicación de la bascula este activa\n2.- Revisa que tienes permisos para acceder en tu navegador");
        window.open(urlbascula+"/solicitar_peso.php");

    })
    .always(function() {
        console.log("complete");
    });
       }
        });
}

function savepaso(accion,idop,paso,idap,idp,maspaso){

    idsProductos = $('#insumos_block3 input').map(function() {
        peso = $(this).val();
        idinput = $(this).attr('id');
        spli1 = idinput.split('b3_');
        spli2 = spli1[1].split('_');
        idPadre=spli2[0];
        idHijo=spli2[1];

        if (typeof idPadre !== "undefined") {
            id= idPadre+'>#'+idHijo+'>#'+peso;
        }

        return id;
    }).get().join('___');

    $.ajax({
        url:"ajax.php?c=Accion3&f=a_guardarPaso3",
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
                alert('Registro peso guardado con exito');
                ciclo(idop);
            }
        }
    });
}