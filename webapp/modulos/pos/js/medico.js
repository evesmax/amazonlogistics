
function nuevoMedico(){

    var pathname = window.location.pathname;
    window.location = window.location.protocol + '//'+document.location.host+pathname+'?c=medico&f=index';
    
}
function back(){
    var pathname = window.location.pathname;
    window.location = window.location.protocol + '//'+document.location.host+pathname+'?c=medico&f=indexGridMedicos';
}
function guardar(){

    var id = $('#idmedico').val();
    var codigo = $('#codigo').val();
    var nombre = $('#nombre').val();
    var cedula = $('#cedula').val();
    var direccion = $('#direccion').val();
    var numext = $('#numext').val();
    var numint = $('#numint').val();
    var colonia = $('#colonia').val();
    var cp = $('#cp').val();
    var pais = $('#selectPais').val();
    var estado = $('#selectEstado').val();
    var municipio = $('#selectMunicipio').val();
    var ciudad = $('#ciudad').val();
    var tel1 = $('#tel1').val();
    var comisionventa = $('#comisionventa').val();
    var comisioncobranza = $('#comisioncobranza').val();
    var vendedor = $('#vendedor').val();

    if(codigo==''){
        alert('Debes de agregar un codigo');
        return false;
    }
    if(nombre == ''){
        alert('Debes agregar un nombre');
        return false;
    }
    if(cedula  == ''){
        alert('Debes agregar una cédula');
        return false;
    }


    $.ajax({
        url: 'ajax.php?c=medico&f=guardaMedico',
        type: 'POST',
        dataType: 'json',
        data: {
            id: id,
            codigo: codigo,
            nombre: nombre,
            cedula: cedula,
            direccion: direccion,
            numext: numext,
            numint: numint,
            colonia: colonia,
            cp: cp,
            pais: pais,
            estado: estado,
            municipio: municipio,
            ciudad: ciudad,
            tel1: tel1,
            comisionventa: comisionventa,
            comisioncobranza: comisioncobranza,
            vendedor: vendedor,
        },
    })
    .done(function(data) {
        console.log(data);
        if(data.status == true && data.idProducto !=''){
            $('#modalSuccess').modal({
                show:true,
            });
        }else{
            alert(data.mensaje);
            $('#btnSave').show();
            $('#loadingPro').hide();
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
} 


function cambiarEstatus(id, clave, nombre, estatus) {
    modalEditarHotel(id, clave, nombre, estatus);
    editarHotel();
}



















window.onload = function() {
    $("#selectPais, #selectPais2, #selectPais3").select2({
        placeholder: "Selecciona País",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=cliente&f=buscarLocalizacion',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                return { idLoc : 1,
                    patron: params.term };
            },

            processResults: function (data) {
                //$("#selectPais").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    })
    .on("change", function(e) {
        $("#selectEstado").empty().trigger('change');
        $("#selectMunicipio").empty().trigger('change');
    });
    $("#selectEstado, #selectEstado3").select2({
        placeholder: "Selecciona Estado",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=cliente&f=buscarLocalizacion',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                if($(this).attr('id') == "selectEstado")
                    pais = $('#selectPais').val();
                else
                    pais = $('#selectPais3').val();
                return { idLoc : 2,
                    pais : pais,
                    patron: params.term };
            },

            processResults: function (data) {
                //$("#selectEstado").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    })
    .on("change", function(e) {
        $("#selectMunicipio").empty().trigger('change');
    });;
    $("#selectMunicipio").select2({
        placeholder: "Selecciona Municipio",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=cliente&f=buscarLocalizacion',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                return { idLoc : 3,
                    estado : $('#selectEstado').val(),
                    patron: params.term };
            },

            processResults: function (data) {
                //$("#selectMunicipio").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    });

    $('#btnNuevoPais').on('click', () => {
        if( $('#inputNuevoPais').val() != "" ){
            datos = {};
            datos.nombre = $('#inputNuevoPais').val();
            $.ajax({
                type: "POST",
                url: 'ajax.php?c=cliente&f=nuevoPais',
                data: datos,
                timeout: 2000,
                dataType: 'json',
                complete: function() {

                },
                success: function(data) {
                    alert("Se ha agregado nuevo país");
                },
                error: function() {
                    alert("Ha ocurrido un error al procesar");
                }
            });
        }
        else {
            alert("No puedes dejar el campos vacios");
        }
    });
    $('#btnNuevoEstado').on('click', () => {
        if( $('#inputNuevoEstado').val() != "" && $('#selectPais2').val() != ""  ) {
            datos = {};
            datos.nombre = $('#inputNuevoEstado').val();
            datos.idPais = $('#selectPais2').val();
            $.ajax({
                type: "POST",
                url: 'ajax.php?c=cliente&f=nuevoEstado',
                data: datos,
                timeout: 2000,
                dataType: 'json',
                complete: function() {

                },
                success: function(data) {
                    alert("Se ha agregado nuevo estado sin problema alguno");
                                    $('#inputNuevoEstado').val('');
                            },
                error: function() {
                    alert("Ha ocurrido un error al procesar");
                }
            });
        }
        else {
            alert("No puedes dejar el campos vacios");
            }
    });
    $('#btnNuevoMunicipio').on('click', () => {
        if( $('#inputNuevoMunicipio').val() != "" && $('#selectPais3').val() != "" && $('#selectEstado2').val() != "" ){
            datos = {};
            datos.nombre = $('#inputNuevoMunicipio').val();
            datos.idEstado = $('#selectEstado3').val();
            $.ajax({
                type: "POST",
                url: 'ajax.php?c=cliente&f=nuevoMunicipio',
                data: datos,
                timeout: 2000,
                dataType: 'json',
                complete: function() {

                },
                success: function(data) {
                    alert("Se ha agregado nuevo municipio");
                                    $('#inputNuevoMunicipio').val('');
                },
                error: function() {
                    alert("Ha ocurrido un error al procesar");
                }
            });
        }
        else {
            alert("No puedes dejar el campos vacios");
        }
    });


    $("#vendedor").select2({
        placeholder: "Vendedor",
        delay: 250,
        width:'100%',
        ajax: {
            url: 'ajax.php?c=medico&f=buscaVendedores',
            type: 'GET',
            dataType: 'json',

            data: function(params) {
                return { 
                    patron: params.term };
            },

            processResults: function (data) {
                //$("#selectPais").empty();
                return { results: data.rows };
            },
            cache: true
        },
        templateResult: function format(state) {
            return  state.text;
        },
        templateSelection: function format(state) {
            return  state.text;
        }
    })
    
}